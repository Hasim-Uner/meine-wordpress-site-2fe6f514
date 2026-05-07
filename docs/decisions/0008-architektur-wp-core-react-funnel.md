# 0008 WordPress-Core mit React-Funnel-Layer

- Datum: 2026-05-02
- Status: accepted

## Entscheidung

WordPress bleibt das Hauptsystem. Funnel-Erlebnisse werden als React-Mikro-Apps im Theme eingebettet. Es wird kein vollständig Headless-Stack und kein SaaS-Produkt eingeführt.

`blocksy-child/energie-fahrplan/` bleibt die kanonische Schablone für React-Funnel. Die Demo-App bleibt im Mono-Repo und wird nicht in ein externes Schwesterrepo extrahiert.

## Begründung

Der Solo-Betrieb braucht eine Architektur, die schnell auslieferbar, versionierbar und wartbar bleibt. WordPress trägt Marketing-Pages, Money-Pages und Editor-Inhalte; React trägt fokussierte Funnel-Erlebnisse mit eigener Build-Struktur.

## Konsequenzen

- Marketing-Pages, Money-Pages und Blog bleiben im WordPress-Editor.
- Funnel-Erlebnisse bauen nach `/wp-content/themes/<theme>/<funnel-name>/dist/`.
- Submit-, Tracking-, n8n- und CRM-Schichten kommen erst nach versioniertem Contract, explizitem Consent und Feature-Flag.
- Der Default-Pfad der Anfrage-System-Analyse verarbeitet keine personenbezogenen Daten und erzeugt keinen CRM-Datensatz.
- Die EnergieFahrplan-Demo bleibt Showroom, nicht SaaS und nicht Lead-Pflichtpfad.
- Der Growth Audit bleibt Legacy-/Sekundärpfad und darf nicht als Hauptfunnel zurückkehren.
- `scripts/build-theme-dist.sh` wird multi-funnel-fähig.
- Pro Kunde kann ein Funnel aus der kanonischen Schablone abgeleitet und branchenspezifisch angepasst werden.

## Nachtrag 2026-05-07

Der Default-Fragepfad der Anfrage-System-Analyse bleibt lokal und ohne personenbezogene Daten. Der separate Kontakt-Schritt ist inzwischen hinter sichtbarer Einwilligung aktiv: WordPress REST speichert Analyse-Leads in `nexus_contact`, Brevo versendet Transaktionsmails. n8n bleibt für diese Route nicht angebunden.
