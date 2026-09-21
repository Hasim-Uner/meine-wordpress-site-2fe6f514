# Auftrag: Funnel-Reparatur hasimuener.de

Stand 21.09.2026 · Grundlage: Live-Prüfung aller Money Pages und Zielseiten
Ablage im Repo: `docs/briefings/funnel/auftrag.md`

Zwei Durchläufe. Der erste repariert, was heute Anfragen kostet. Der zweite räumt Widersprüche auf.
Nichts anderes anfassen — keine Designänderungen, keine neue Copy außerhalb der genannten Stellen.

---

## Durchlauf 1 — kostet heute Anfragen

```
Drei Reparaturen, sonst nichts. Arbeite auf einem Branch, zeig mir den Diff, kein Push auf main.

1. MARKTCHECK-FORMULAR
   Auf /solar-waermepumpen-leadgenerierung/ ist "Marktcheck starten" ein mailto-Link.
   Vor dem Umbau gab es dort ein mehrstufiges Formular (in der Git-History suchen nach
   "Aktivieren Sie JavaScript für den Marktcheck"). Diese Komponente zurück an den Anker
   #marktcheck setzen. Alle Buttons "Marktcheck starten" und "Kostenlos prüfen lassen" auf
   dieser Seite, auf /ergebnisse/ und auf /case-study-solar-leadgenerierung/ zeigen danach
   auf /solar-waermepumpen-leadgenerierung/#marktcheck — kein mailto mehr.
   Test: Formular einmal vollständig absenden und prüfen, dass die Anfrage ankommt.

2. SOLAR-SEITE AUF DIE KORRIGIERTE FASSUNG
   docs/briefings/solar-money-page/referenz.html durch die beigefügte
   anfragestrecke-produktion.html ersetzen und die Seiten-Copy daran angleichen.
   Danach muss im Theme ein grep nach "Verfahren läuft", "auf Nachfrage" und
   "Anonymisiert" leer sein. In der Referenz steht ein HTML-Kommentar am Marktcheck:
   dort gehört die Formular-Komponente aus Punkt 1 hin.

3. KEIN FIRMENNAME
   Auf /whitelabel-retainer/ den Link auf e3-newenergy.de entfernen. Der Fall heißt
   überall "ein mittelständischer PV-Installationsbetrieb" — ohne Link, ohne Namen,
   ohne Begründung für die Anonymisierung.
```

---

## Durchlauf 2 — Widersprüche

```
Vier Punkte. Branch, Diff, kein Push auf main.

1. KANON STATT SUCHEN/ERSETZEN
   Antwortzeit ist sitewide "innerhalb von 24 Stunden werktags". Die Angabe kommt
   ausschließlich aus inc/canon/messaging-canon.php; in Templates steht nur der Verweis.
   Dazu eine Sperrliste im Build: "2 Werktage", "zwei Werktagen", "48 Stunden",
   "hallo@hasimuener.de", "hasim@hasimuener.de", "Verfahren läuft" brechen den Build ab.
   Betrifft mindestens: Startseite, /whitelabel-retainer/, /solar-waermepumpen-leadgenerierung/,
   /kontakt/, /wordpress-agentur-hannover/, /case-study-solar-leadgenerierung/,
   /hasim-uener/ (dort steht noch hallo@) und alle Dateien in docs/briefings/.

2. /ergebnisse/
   301 auf /case-study-solar-leadgenerierung/. Menüpunkt "Ergebnisse" zeigt auf /#arbeiten
   (den Abschnitt "Ausgewählte Arbeiten" der Startseite, dort eine id vergeben).
   Den Link "WordPress & Technik" auf /wordpress-agentur-hannover/ ebenfalls auf /#arbeiten.

3. TRACKING-PAKETE EINHEITLICH
   /ga4-tracking-setup/ und /server-side-tracking-b2b/ nutzen dieselben Paketnamen und
   Preise: Basis 1.290 €, Performance 1.900 €, Individuell ab 3.500 €. "Core" und "Revenue"
   entfallen. Auf /server-side-tracking-b2b/ wird "Gesamten Anfrageweg im Marktcheck
   einordnen" zu "Tracking-Projekt anfragen" → /kontakt/?type=project&focus=tracking.

4. KONTAKTFORMULAR
   Den Parameter focus auswerten: relaunch → "Relaunch/Neue Seite", conversion →
   "Conversion & CRO", tracking → "Tracking & Analytics" vorauswählen. Unter dem
   Absende-Button ein Satz: "Sie bekommen innerhalb von 24 Stunden werktags eine Antwort
   von mir persönlich — keine automatische Mailserie."
```

---

## Danach, einzeln

- `/wordpress-freelancer-hannover/` per 301 auf `/` statt Duplikat mit Canonical
- Fallstudie: „1–5 % Abschlussquote vorher" → „im einstelligen Bereich" (Marktannahme, keine Messung)
- Solar-Seite: die fünf JSON-LD-Blöcke aus `docs/briefings/solar-money-page/seo-briefing.md`, Abschnitt 7.3
- Startseite: die drei Punkte aus dem letzten Auftrag (H1 ohne Ort, Prüfstand unter den Hero, Satz zum Ausfallrisiko)

---

## Prüfprotokoll für den nächsten Durchgang

Jede Seite dieselben fünf Fragen, damit Seiten vergleichbar werden:

1. **Identität** — Title, Canonical, H1, erste zwei Sätze: wer macht hier was für wen?
2. **Wege** — jeder Button, jeder Link im Fließtext mit Ziel-URL
3. **Kanon** — Preise, Kennzahlen, Antwortzeit, E-Mail wörtlich
4. **Abschluss** — wo entsteht hier eine Anfrage, und funktioniert das?
5. **Querprüfung** — Karte zeichnen, Sackgassen und Widersprüche markieren, nach Wirkung sortieren
