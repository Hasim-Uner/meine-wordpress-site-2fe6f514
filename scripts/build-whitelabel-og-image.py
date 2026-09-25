#!/usr/bin/env python3
"""Rendert das og:image der White-Label-Seite.

    python3 scripts/build-whitelabel-og-image.py

Warum eine eigene Kachel: Der globale Fallback ist das Portraet im
Hochformat (1066 x 1600). LinkedIn beschneidet es auf 1,91 : 1, und die
Seite wird dort aktiv geteilt. Die Kachel steht im Querformat und sagt,
worum es geht; das Portraet ist dasselbe wie im Hero der Seite.

JPG statt PNG: Die rechte Haelfte ist ein Foto. Als PNG waere die Datei
ein Vielfaches groesser, ohne dass der Text links schaerfer wird.

Die Schriften kommen aus blocksy-child/fonts/. Es sind dieselben Dateien,
die die Seite ausliefert; das Bild kann deshalb nicht in einer anderen
Schrift stehen als die Seite, auf die es zeigt. Sie liegen nur als WOFF
vor und werden fuer FreeType in ein temporaeres Verzeichnis entpackt.

Abhaengigkeiten: Pillow, fonttools, brotli.
"""

from __future__ import annotations

import pathlib
import sys
import tempfile

try:
    from PIL import Image, ImageDraw, ImageFont
except ImportError:  # pragma: no cover
    sys.exit("Pillow fehlt: pip install Pillow fonttools brotli")

try:
    from fontTools.ttLib import TTFont
except ImportError:  # pragma: no cover
    sys.exit("fonttools fehlt: pip install Pillow fonttools brotli")

WURZEL = pathlib.Path(__file__).resolve().parent.parent
THEME = WURZEL / "blocksy-child"
FONTS = THEME / "fonts"
PORTRAET = THEME / "assets" / "img" / "hasim-freelancer-portrait-480x600.webp"
ZIEL = THEME / "assets" / "img" / "whitelabel-retainer-og.jpg"

BREITE, HOEHE = 1200, 630
HOECHSTENS_KB = 300

# Tokens aus assets/css/system.css (:root), auf denen die Strecke der
# White-Label-Seite steht.
PAPIER = (255, 255, 255)
TINTE = (23, 20, 18)
GRAU = (91, 85, 77)
MATT = (115, 107, 98)
STEMPEL = (184, 66, 15)
HAAR = (222, 218, 211)


def mische(a: tuple[int, int, int], b: tuple[int, int, int], anteil_a: float):
    """color-mix(in srgb, a anteil_a, b) wie in startseite-strecke.css."""
    return tuple(round(x * anteil_a + y * (1 - anteil_a)) for x, y in zip(a, b))


# --st-linie: color-mix(in srgb, var(--matt) 55%, var(--papier)).
LINIE = mische(MATT, PAPIER, 0.55)

TITEL = ["White-Label", "für Agenturen"]
UNTERZEILE = "WordPress · Tracking"
NAME = "Haşim Üner"


def entpacke(name: str, ordner: pathlib.Path) -> pathlib.Path:
    """WOFF nach TTF, damit FreeType die Datei lesen kann."""
    quelle = FONTS / name
    if not quelle.exists():
        sys.exit(f"Schrift fehlt: {quelle}")
    ziel = ordner / (quelle.stem + ".ttf")
    schrift = TTFont(quelle)
    schrift.flavor = None
    schrift.save(ziel)
    return ziel


def pruefe_deckung(pfad: pathlib.Path, texte: list[str]) -> None:
    """Deckung ueber die cmap, nicht ueber die gerenderte Maske.

    Ein .notdef-Kaestchen hat ebenfalls eine Bounding Box und wuerde jede
    maskenbasierte Pruefung bestehen. "Haşim Üner" braucht s-Cedille und
    U-Umlaut aus demselben Schnitt.
    """
    cmap = set(TTFont(pfad).getBestCmap())
    fehlend = sorted({z for t in texte for z in t if ord(z) not in cmap})
    if fehlend:
        sys.exit(f"{pfad.name} deckt nicht ab: {' '.join(fehlend)}")


def setze(zeichne, xy, text, schrift, fill, sperrung_em: float = 0.0) -> float:
    """Setzt Text auf der Grundlinie, mit Laufweite wie letter-spacing.

    Pillow kennt keine Laufweite. Jedes Zeichen steht deshalb an der Laenge
    seines Praefixes plus Sperrung; die Unterschneidung der Schrift bleibt
    so erhalten, weil textlength() sie im Praefix mitrechnet.
    """
    x, y = xy
    sperrung = sperrung_em * schrift.size
    for i, zeichen in enumerate(text):
        vorlauf = zeichne.textlength(text[:i], font=schrift) + i * sperrung
        zeichne.text((x + vorlauf, y), zeichen, font=schrift, fill=fill, anchor="ls")
    return zeichne.textlength(text, font=schrift) + (len(text) - 1) * sperrung


def portraet(breite: int, hoehe: int) -> Image.Image:
    """Fuellt die rechte Spalte, Zuschnitt auf die Bildmitte."""
    bild = Image.open(PORTRAET).convert("RGB")
    faktor = max(breite / bild.width, hoehe / bild.height)
    bild = bild.resize(
        (round(bild.width * faktor), round(bild.height * faktor)),
        Image.Resampling.LANCZOS,
    )
    links = (bild.width - breite) // 2
    oben = (bild.height - hoehe) // 2
    return bild.crop((links, oben, links + breite, oben + hoehe))


def main() -> None:
    with tempfile.TemporaryDirectory() as tmp:
        ordner = pathlib.Path(tmp)
        # Satoshi ist --serif-display, die Schrift der H1 im Hero. Dort steht
        # sie mit Gewicht 500; der Name bekommt den fetten Schnitt.
        medium_pfad = entpacke("Satoshi-Medium.woff", ordner)
        bold_pfad = entpacke("Satoshi-Bold.woff", ordner)
        pruefe_deckung(medium_pfad, TITEL + [UNTERZEILE])
        pruefe_deckung(bold_pfad, [NAME])

        titel_font = ImageFont.truetype(str(medium_pfad), 88)
        unter_font = ImageFont.truetype(str(medium_pfad), 44)
        name_font = ImageFont.truetype(str(bold_pfad), 30)

        bild = Image.new("RGB", (BREITE, HOEHE), PAPIER)
        zeichne = ImageDraw.Draw(bild)

        # Rechte Spalte: das Portraet aus dem Hero, randlos.
        spalte_x = 752
        bild.paste(portraet(BREITE - spalte_x, HOEHE), (spalte_x, 0))

        # Messlinie in der Randspalte wie auf der Seite: Haarlinie ueber die
        # ganze Hoehe, bis zur ersten Marke mit Stempel gefuellt.
        linie_x = 60
        marke_y = 124
        zeichne.line([(linie_x, 0), (linie_x, HOEHE)], fill=LINIE, width=2)
        zeichne.line([(linie_x, 0), (linie_x, marke_y)], fill=STEMPEL, width=2)
        punkt = 7
        zeichne.ellipse(
            [linie_x - punkt, marke_y - punkt, linie_x + punkt, marke_y + punkt],
            fill=STEMPEL,
        )

        rand = 108

        # Titel wie die H1: Gewicht 500, letter-spacing -0.045em,
        # line-height 0.95.
        zeilenabstand = round(titel_font.size * 0.95)
        grundlinie = 158
        for zeile in TITEL:
            breite = setze(zeichne, (rand, grundlinie), zeile, titel_font, TINTE, -0.045)
            if rand + breite > spalte_x - 40:
                sys.exit(f"Titelzeile laeuft ins Portraet: {zeile!r}")
            grundlinie += zeilenabstand

        # Unterzeile im Ton des leisen H1-Satzes (--grau).
        setze(zeichne, (rand, grundlinie + 44), UNTERZEILE, unter_font, GRAU, -0.02)

        # Absender unten, ueber einer Haarlinie.
        zeichne.line([(rand, 486), (spalte_x - 56, 486)], fill=HAAR, width=2)
        setze(zeichne, (rand, 540), NAME, name_font, TINTE, -0.01)

        ZIEL.parent.mkdir(parents=True, exist_ok=True)
        # 4:4:4 statt Chroma-Unterabtastung: sonst franst der Text an den
        # Kanten farbig aus, und der Stempelpunkt wird braun.
        bild.save(
            ZIEL,
            "JPEG",
            quality=88,
            subsampling=0,
            optimize=True,
            progressive=True,
        )

    groesse_kb = ZIEL.stat().st_size / 1024
    if groesse_kb >= HOECHSTENS_KB:
        sys.exit(f"{ZIEL.name} ist {groesse_kb:.0f} KB, Grenze {HOECHSTENS_KB} KB")
    print(f"{ZIEL.relative_to(WURZEL)} · {BREITE}x{HOEHE} · {groesse_kb:.0f} KB")


if __name__ == "__main__":
    main()
