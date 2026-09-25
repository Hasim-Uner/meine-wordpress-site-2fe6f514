#!/usr/bin/env python3
"""Rendert die og:images, die das Theme mitbringt.

    python3 scripts/build-og-images.py

Drei Kacheln, alle 1200 x 630 als JPG:

- whitelabel-retainer-og.jpg   /whitelabel-retainer/
- og-fallstudie-anfragesystem.jpg   /case-study-solar-leadgenerierung/
- og-standard.jpg   Standard fuer jede Seite ohne eigenes Bild

Welche Seite welche Kachel bekommt, entscheidet inc/seo-meta.php
(hu_get_route_social_image() und hu_get_default_social_image()).

Warum eigene Kacheln: Der fruehere Standard war das Portraet im Hochformat
(1066 x 1600). LinkedIn und Messenger beschneiden es auf 1,91 : 1. Die
Kacheln stehen im Querformat und sagen, worum es geht.

JPG statt PNG: Zwei der drei Kacheln sind zur Haelfte ein Foto. Als PNG
waeren sie ein Vielfaches groesser, ohne dass der Text schaerfer wird.

Die Schriften kommen aus blocksy-child/fonts/. Es sind dieselben Dateien,
die die Seite ausliefert; das Bild kann deshalb nicht in einer anderen
Schrift stehen als die Seite, auf die es zeigt. Sie liegen nur als WOFF
vor und werden fuer FreeType in ein temporaeres Verzeichnis entpackt.

Die Zahlen der Fallstudie liest das Skript aus
blocksy-child/inc/canon/e3-proof-canon.php, nicht aus einer Kopie hier.
Der Fall ist nach aussen anonymisiert: kein Firmenname, kein Logo, keine
Region, und auch der Dateiname sagt nichts ueber den Betrieb.

Abhaengigkeiten: Pillow, fonttools, brotli.
"""

from __future__ import annotations

import pathlib
import re
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
BILDER = THEME / "assets" / "img"
# Dasselbe Motiv wie im Hero der Startseite und der White-Label-Seite.
PORTRAET = BILDER / "hasim-freelancer-portrait-480x600.webp"
FALL_KANON = THEME / "inc" / "canon" / "e3-proof-canon.php"

BREITE, HOEHE = 1200, 630
HOECHSTENS_KB = 300

# Tokens aus assets/css/system.css (:root), auf denen die Strecke von
# Startseite und White-Label-Seite steht.
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

# Linker Rand: Messlinie bei 60, Text ab 108. Rechte Spalte fuer das Portraet.
LINIE_X = 60
RAND = 108
SPALTE_X = 752


def kanon_wert(name: str) -> str:
    """Liest define( 'NAME', wert ) aus dem Fall-Kanon."""
    quelle = FALL_KANON.read_text(encoding="utf-8")
    treffer = re.search(
        r"define\(\s*'" + re.escape(name) + r"',\s*(?:'([^']*)'|(\d+))\s*\)", quelle
    )
    if not treffer:
        sys.exit(f"{name} fehlt in {FALL_KANON.relative_to(WURZEL)}")
    return treffer.group(1) if treffer.group(1) is not None else treffer.group(2)


def fall_label() -> str:
    """Das Fall-Label des Kanons als Satzanfang."""
    label = kanon_wert("HU_E3_CASE_LABEL")
    return label[:1].upper() + label[1:]


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


def pfeil(zeichne, x: float, y: float, laenge: float, farbe, staerke: int = 6) -> None:
    """Gezeichnet, nicht gesetzt: U+2192 fehlt in IBM Plex Mono (latin)."""
    spitze = staerke * 3.2
    zeichne.line([(x, y), (x + laenge - spitze * 0.6, y)], fill=farbe, width=staerke)
    zeichne.polygon(
        [
            (x + laenge, y),
            (x + laenge - spitze, y - spitze * 0.62),
            (x + laenge - spitze, y + spitze * 0.62),
        ],
        fill=farbe,
    )


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


def leinwand(marke_y: int):
    """Papier und Messlinie wie auf der Seite.

    Haarlinie ueber die ganze Hoehe, bis zur ersten Marke mit Stempel
    gefuellt.
    """
    bild = Image.new("RGB", (BREITE, HOEHE), PAPIER)
    zeichne = ImageDraw.Draw(bild)
    zeichne.line([(LINIE_X, 0), (LINIE_X, HOEHE)], fill=LINIE, width=2)
    zeichne.line([(LINIE_X, 0), (LINIE_X, marke_y)], fill=STEMPEL, width=2)
    punkt = 7
    zeichne.ellipse(
        [LINIE_X - punkt, marke_y - punkt, LINIE_X + punkt, marke_y + punkt],
        fill=STEMPEL,
    )
    return bild, zeichne


def speichere(bild: Image.Image, name: str) -> None:
    ziel = BILDER / name
    ziel.parent.mkdir(parents=True, exist_ok=True)
    # 4:4:4 statt Chroma-Unterabtastung: sonst franst der Text an den
    # Kanten farbig aus, und der Stempelpunkt wird braun.
    bild.save(
        ziel,
        "JPEG",
        quality=88,
        subsampling=0,
        optimize=True,
        progressive=True,
    )
    groesse_kb = ziel.stat().st_size / 1024
    if groesse_kb >= HOECHSTENS_KB:
        sys.exit(f"{ziel.name} ist {groesse_kb:.0f} KB, Grenze {HOECHSTENS_KB} KB")
    print(f"{ziel.relative_to(WURZEL)} · {BREITE}x{HOEHE} · {groesse_kb:.0f} KB")


def pruefe_breite(links: float, breite: float, grenze: float, text: str) -> None:
    if links + breite > grenze:
        sys.exit(f"Zeile laeuft ueber den Rand: {text!r}")


def kachel_mit_portraet(schriften, name, titel, unterzeile, absender=None, grundlinie=158):
    """Links Titel und Unterzeile, rechts das Portraet aus dem Hero.

    Titel wie die H1: Gewicht 500, letter-spacing -0.045em, line-height
    0.95. Die Unterzeile im Ton des leisen H1-Satzes (--grau). Der Absender
    steht, wenn es einen gibt, unten ueber einer Haarlinie.
    """
    bild, zeichne = leinwand(marke_y=124 + (grundlinie - 158))
    bild.paste(portraet(BREITE - SPALTE_X, HOEHE), (SPALTE_X, 0))

    titel_font = schriften["medium"](88)
    zeilenabstand = round(titel_font.size * 0.95)
    for zeile in titel:
        breite = setze(zeichne, (RAND, grundlinie), zeile, titel_font, TINTE, -0.045)
        pruefe_breite(RAND, breite, SPALTE_X - 40, zeile)
        grundlinie += zeilenabstand

    # Die Unterzeile nimmt die groesste Stufe, die neben das Portraet passt.
    for groesse in (44, 40, 36, 32):
        unter_font = schriften["medium"](groesse)
        if zeichne.textlength(unterzeile, font=unter_font) <= SPALTE_X - 40 - RAND:
            break
    breite = setze(zeichne, (RAND, grundlinie + 44), unterzeile, unter_font, GRAU, -0.02)
    pruefe_breite(RAND, breite, SPALTE_X - 40, unterzeile)

    if absender:
        zeichne.line([(RAND, 486), (SPALTE_X - 56, 486)], fill=HAAR, width=2)
        setze(zeichne, (RAND, 540), absender, schriften["bold"](30), TINTE, -0.01)

    speichere(bild, name)


def setze_betrag(zeichne, xy, zahl: str, einheit: str, schriften, groesse: int, fill) -> float:
    """Kennzahl wie .st-kennzahl mit .st-einheit auf der Startseite.

    Mono, letter-spacing -0.045em; die Einheit steht in 0.6em mit 0.1em
    Abstand daneben, ohne die Luecke eines Mono-Leerzeichens.
    """
    x, y = xy
    breite = setze(zeichne, (x, y), zahl, schriften["mono"](groesse), fill, -0.045)
    breite += round(groesse * 0.1)
    breite += setze(zeichne, (x + breite, y), einheit, schriften["mono"](round(groesse * 0.6)), fill)
    return breite


def kachel_fallstudie(schriften, name):
    """Die Zahl zuerst, darunter wofuer sie steht und fuer wen.

    Mono fuer die Betraege wie die Kennzahlen der Startseite, der neue Wert
    in Stempel: Orange steht auf der Seite ausschliesslich fuer Messwerte.
    """
    vorher = kanon_wert("HU_E3_CPL_BEFORE")
    nachher = kanon_wert("HU_E3_CPL_AFTER")
    label = fall_label()
    einheit = "Kosten pro Anfrage"

    bild, zeichne = leinwand(marke_y=190)

    groesse = 150
    grundlinie = 262
    x = RAND
    x += setze_betrag(zeichne, (x, grundlinie), vorher, "€", schriften, groesse, GRAU)
    x += 48
    pfeil(zeichne, x, grundlinie - 50, 112, STEMPEL)
    x += 112 + 48
    x += setze_betrag(zeichne, (x, grundlinie), nachher, "€", schriften, groesse, STEMPEL)
    pruefe_breite(0, x, BREITE - 72, f"{vorher} € → {nachher} €")

    einheit_font = schriften["medium"](64)
    breite = setze(zeichne, (RAND, grundlinie + 104), einheit, einheit_font, TINTE, -0.03)
    pruefe_breite(RAND, breite, BREITE - 72, einheit)

    zeichne.line([(RAND, 486), (BREITE - 72, 486)], fill=HAAR, width=2)
    breite = setze(zeichne, (RAND, 540), label, schriften["medium"](34), GRAU, -0.01)
    pruefe_breite(RAND, breite, BREITE - 72, label)

    speichere(bild, name)


def main() -> None:
    with tempfile.TemporaryDirectory() as tmp:
        ordner = pathlib.Path(tmp)
        # Satoshi ist --serif-display, die Schrift der H1 im Hero. Dort steht
        # sie mit Gewicht 500; der Absender bekommt den fetten Schnitt.
        # IBM Plex Mono ist --mono, die Schrift der Kennzahlen.
        medium_pfad = entpacke("Satoshi-Medium.woff", ordner)
        bold_pfad = entpacke("Satoshi-Bold.woff", ordner)
        mono_pfad = entpacke("IBMPlexMono-500-latin.woff2", ordner)

        texte_medium = [
            "White-Label", "für Agenturen", "WordPress · Tracking",
            "Haşim Üner", "WordPress · Tracking · Conversion",
            "Kosten pro Anfrage", fall_label(),
        ]
        pruefe_deckung(medium_pfad, texte_medium)
        pruefe_deckung(bold_pfad, ["Haşim Üner"])
        pruefe_deckung(mono_pfad, ["0123456789 €"])

        schriften = {
            "medium": lambda g: ImageFont.truetype(str(medium_pfad), g),
            "bold": lambda g: ImageFont.truetype(str(bold_pfad), g),
            "mono": lambda g: ImageFont.truetype(str(mono_pfad), g),
        }

        kachel_mit_portraet(
            schriften,
            "whitelabel-retainer-og.jpg",
            titel=["White-Label", "für Agenturen"],
            unterzeile="WordPress · Tracking",
            absender="Haşim Üner",
        )
        # Eine Titelzeile weniger und kein Absender: der Block steht dafuer
        # tiefer, damit er die Hoehe wie bei der White-Label-Kachel fuellt.
        kachel_mit_portraet(
            schriften,
            "og-standard.jpg",
            titel=["Haşim Üner"],
            unterzeile="WordPress · Tracking · Conversion",
            grundlinie=292,
        )
        kachel_fallstudie(schriften, "og-fallstudie-anfragesystem.jpg")


if __name__ == "__main__":
    main()
