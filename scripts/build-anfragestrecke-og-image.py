#!/usr/bin/env python3
"""Rendert das og:image der Anfragestrecke.

    python3 scripts/build-anfragestrecke-og-image.py

Warum ein Skript und kein einmalig hochgeladenes Bild: Die Kachel zeigt
die beiden CPL-Werte aus dem E3-Canon. Aendern die sich, muss sich das
Bild mitaendern — mit einem Generator ist das eine Zeile statt einer
Designrunde.

Die Schriften kommen aus blocksy-child/fonts/. Es sind dieselben Dateien,
die die Seite ausliefert; das Bild kann deshalb nicht in einer anderen
Schrift stehen als die Seite, auf die es zeigt.

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
FONTS = WURZEL / "blocksy-child" / "fonts"
ZIEL = WURZEL / "blocksy-child" / "assets" / "img" / "anfragestrecke-og.png"

BREITE, HOEHE = 1200, 630

# Tokens aus assets/css/anfragestrecke.css.
PAPIER = (255, 255, 255)
TINTE = (23, 20, 18)
GRAU = (91, 85, 77)
MATT = (115, 107, 98)
STEMPEL = (184, 66, 15)
HAAR = (222, 218, 211)

# Werte aus canon/e3-proof-canon.php.
CPL_VORHER = "150 €"
CPL_NACHHER = "22 €"
ZEITRAUM = "6 Monate"


def entpacke(name: str, ordner: pathlib.Path) -> pathlib.Path:
    """woff2 nach ttf, damit FreeType die Datei lesen kann."""
    quelle = FONTS / name
    if not quelle.exists():
        sys.exit(f"Schrift fehlt: {quelle}")
    ziel = ordner / (quelle.stem + ".ttf")
    schrift = TTFont(quelle)
    schrift.flavor = None
    schrift.save(ziel)
    return ziel


def main() -> None:
    with tempfile.TemporaryDirectory() as tmp:
        ordner = pathlib.Path(tmp)
        serif_pfad = entpacke("Newsreader-Variable-latin.woff2", ordner)
        mono500_pfad = entpacke("IBMPlexMono-500-latin.woff2", ordner)
        # Das s-Cedille in "Haşim" liegt im latin-ext-Schnitt. Ohne ihn
        # steht im Absender ein leeres Kaestchen.
        mono_ext_pfad = entpacke("IBMPlexMono-500-latin-ext.woff2", ordner)

        # Newsreader hat die Achsen in der Reihenfolge (Weight, Optical Size).
        # Sie andersherum zu setzen ist kein Fehler, den Pillow meldet: die
        # Werte werden stillschweigend in die jeweilige Achse geklemmt, und
        # heraus kommt eine Haarlinie in Displaygroesse.
        def serif(groesse: int, gewicht: int = 400, optisch: int | None = None):
            schrift = ImageFont.truetype(str(serif_pfad), groesse)
            if optisch is None:
                optisch = min(48, max(8, round(groesse * 0.62)))
            try:
                schrift.set_variation_by_axes([gewicht, optisch])
            except Exception:  # noqa: BLE001 - statische Fallback-Instanz
                pass
            return schrift

        def mono(groesse: int):
            return ImageFont.truetype(str(mono500_pfad), groesse)

        def mono_ext(groesse: int):
            return ImageFont.truetype(str(mono_ext_pfad), groesse)

        # Deckung ueber die cmap, nicht ueber die gerenderte Maske: ein
        # .notdef-Kaestchen hat ebenfalls eine Bounding Box und wuerde jede
        # maskenbasierte Pruefung bestehen.
        deckung = {
            str(mono500_pfad): set(TTFont(mono500_pfad).getBestCmap()),
            str(mono_ext_pfad): set(TTFont(mono_ext_pfad).getBestCmap()),
        }

        def hat_glyph(schrift, zeichen: str) -> bool:
            zeichen_satz = deckung.get(schrift.path)
            if zeichen_satz is None:
                return True
            return ord(zeichen) in zeichen_satz

        def schreibe_gemischt(zeichne, xy, text, haupt, ersatz, fill):
            """Setzt Text zeichenweise und weicht auf den Ersatzschnitt aus.

            "HAŞIM ÜNER" mischt latin (Ü) und latin-ext (Ş). Eine
            Schriftdatei allein deckt den Namen nicht ab.
            """
            x, y = xy
            for zeichen in text:
                schrift = haupt if hat_glyph(haupt, zeichen) else ersatz
                zeichne.text((x, y), zeichen, font=schrift, fill=fill)
                x += zeichne.textlength(zeichen, font=schrift)
            return x

        def pfeil_nach_rechts(zeichne, x, y, laenge, farbe, staerke=3):
            """Gezeichnet, nicht gesetzt.

            U+2192 fehlt im latin-Schnitt von IBM Plex Mono — als Glyphe
            waere hier ein leeres Kaestchen gelandet.
            """
            zeichne.line([(x, y), (x + laenge, y)], fill=farbe, width=staerke)
            spitze = 9
            zeichne.polygon(
                [
                    (x + laenge, y),
                    (x + laenge - spitze, y - spitze * 0.62),
                    (x + laenge - spitze, y + spitze * 0.62),
                ],
                fill=farbe,
            )

        bild = Image.new("RGB", (BREITE, HOEHE), PAPIER)
        zeichne = ImageDraw.Draw(bild)

        rand = 78

        # Kopfzeile
        zeichne.text((rand, 66), "ANFRAGESTRECKE", font=mono(19), fill=STEMPEL)
        zeichne.text(
            (rand, 98),
            "PHOTOVOLTAIK · WÄRMEPUMPE · SPEICHER",
            font=mono(19),
            fill=MATT,
        )
        zeichne.line([(rand, 142), (BREITE - rand, 142)], fill=TINTE, width=1)

        # Schlagzeile
        zeichne.text((rand, 186), "Anfragen, die auf", font=serif(74, 400), fill=TINTE)
        zeichne.text((rand, 272), "Ihrer Domain entstehen.", font=serif(74, 400), fill=TINTE)

        # Unterzeile
        zeichne.text(
            (rand, 382),
            "Eigenes Anfragesystem statt gekaufter Portal-Leads.",
            font=serif(30, 400),
            fill=GRAU,
        )

        # Beleg unten links
        zeichne.line([(rand, 460), (BREITE - rand, 460)], fill=HAAR, width=1)
        zeichne.text(
            (rand, 486),
            "DOKUMENTIERTER FALL · " + ZEITRAUM.upper(),
            font=mono(17),
            fill=MATT,
        )

        gross = mono(52)
        klein = serif(26, 400)

        y_wert = 516
        x = rand
        zeichne.text((x, y_wert), CPL_VORHER, font=gross, fill=GRAU)
        x += zeichne.textlength(CPL_VORHER, font=gross) + 30
        pfeil_nach_rechts(zeichne, x, y_wert + 36, 44, STEMPEL)
        x += 44 + 30
        zeichne.text((x, y_wert), CPL_NACHHER, font=gross, fill=STEMPEL)
        x += zeichne.textlength(CPL_NACHHER, font=gross) + 26
        zeichne.text((x, y_wert + 24), "je qualifizierter Anfrage", font=klein, fill=GRAU)

        # Absender rechts oben. Der Stempelpunkt sitzt hinter dem Namen auf
        # der Grundlinie, nicht darueber — sonst laeuft er ins letzte R.
        marke = "HAŞIM ÜNER"
        marke_font = mono(21)
        marke_ersatz = mono_ext(21)
        breite_marke = sum(
            zeichne.textlength(
                zeichen,
                font=marke_font if hat_glyph(marke_font, zeichen) else marke_ersatz,
            )
            for zeichen in marke
        )
        punkt = 6
        start_x = BREITE - rand - breite_marke - punkt - 5
        ende_x = schreibe_gemischt(
            zeichne, (start_x, 66), marke, marke_font, marke_ersatz, TINTE
        )
        zeichne.rectangle(
            [ende_x + 5, 66 + 21 - punkt, ende_x + 5 + punkt, 66 + 21], fill=STEMPEL
        )

        ZIEL.parent.mkdir(parents=True, exist_ok=True)
        bild.save(ZIEL, "PNG", optimize=True)
        print(f"{ZIEL.relative_to(WURZEL)} · {ZIEL.stat().st_size // 1024} KB")


if __name__ == "__main__":
    main()
