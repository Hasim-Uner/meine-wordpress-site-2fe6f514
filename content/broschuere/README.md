# Broschüre · Eigene Anfrage-Systeme (Wickelfalz, A4 quer)

Print-Broschüre auf Basis der aktuellen Positionierung
(`docs/standards/BRAND_AND_COPY.md`) und der kanonischen Werte aus
`blocksy-child/inc/canon/` (E3-Proof, Diagnose-Leiter, Founding-Frame).

## Dateien

- `broschuere-anfrage-systeme.html` — Quelle (HTML/CSS, druckfertiges Layout)
- `Broschuere_hasimuener_Anfrage-Systeme.pdf` — gebautes PDF (2 Bögen à 3 Panels)
- `assets/` — Satoshi/JetBrains-Mono-Webfonts (Kopien) + QR-Code

## Build

```bash
pip install weasyprint qrcode[pil]
cd content/broschuere
python3 -m weasyprint broschuere-anfrage-systeme.html Broschuere_hasimuener_Anfrage-Systeme.pdf
```

QR-Code neu erzeugen (Ziel: Marktcheck):

```bash
python3 -c "
import qrcode
qr = qrcode.QRCode(error_correction=qrcode.constants.ERROR_CORRECT_M, box_size=12, border=0)
qr.add_data('https://hasimuener.de/solar-waermepumpen-leadgenerierung/#marktcheck')
qr.make(fit=True)
qr.make_image(fill_color='#0c0d0e', back_color='#f3efe5').save('assets/qr-marktcheck.png')
"
```

## Struktur (Wickelfalz)

- Bogen 1 (außen): Rückseite/Kontakt · Einschlagklappe mit E3-Proof · Titelseite
- Bogen 2 (innen): Das Problem · Der Weg (Marktcheck → System-Diagnose → Umsetzung) · Erster Schritt mit QR

## Hinweise für die Druckerei

- Panels sind gleichmäßig 3 × 99 mm; für echten Wickelfalz die Einschlagklappe
  je nach Papierstärke ca. 2 mm schmaler anlegen (Falzzugabe).
- Farben sind RGB (Creme `#f3efe5`, Ink `#0c0d0e`, Kupfer `#b46a3c`);
  CMYK-Konvertierung der Druckerei überlassen, Creme-Fläche randabfallend drucken.

## Inhaltliche Leitplanken

- Zahlen nur aus `blocksy-child/inc/canon/e3-proof-canon.php` (kein ROAS, kein „9 Monate").
- Primär-CTA ist der Marktcheck; `/growth-audit` und WGOS sind retired/intern
  und dürfen nicht mehr auf Print-Material erscheinen.
