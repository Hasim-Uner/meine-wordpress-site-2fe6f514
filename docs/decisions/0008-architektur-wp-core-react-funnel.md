# 0008 WordPress-Core mit React-Funnel-Layer

- Datum: 2026-05-02
- Status: überwiegend abgelöst (Nachtrag 2026-09-22); gültig bleibt nur „WordPress ist das Hauptsystem, kein Headless-Stack, kein SaaS-Produkt“

## Entscheidung

WordPress bleibt das Hauptsystem. Funnel-Erlebnisse werden als React-Mikro-Apps im Theme eingebettet. Es wird kein vollständig Headless-Stack und kein SaaS-Produkt eingeführt.

Die Anfragesystem-Analyse bleibt der aktive React-Funnel-Layer im Theme. Der frühere Showroom-Pfad ist nicht mehr Teil der Funnel-Architektur.

## Begründung

Der Solo-Betrieb braucht eine Architektur, die schnell auslieferbar, versionierbar und wartbar bleibt. WordPress trägt Marketing-Pages, Money-Pages und Editor-Inhalte; React trägt fokussierte Funnel-Erlebnisse mit eigener Build-Struktur.

## Konsequenzen

- Marketing-Pages, Money-Pages und Blog bleiben im WordPress-Editor.
- Funnel-Erlebnisse bauen nach `/wp-content/themes/<theme>/<funnel-name>/dist/`.
- Submit-, Tracking-, n8n- und CRM-Schichten kommen erst nach versioniertem Contract, explizitem Consent und Feature-Flag.
- Der Default-Pfad der Anfragesystem-Analyse verarbeitet keine personenbezogenen Daten und erzeugt keinen CRM-Datensatz.
- Der retired EnergieFahrplan-Showroom bleibt kein SaaS und kein Lead-Pflichtpfad.
- Der Growth Audit bleibt Legacy-/Sekundärpfad und darf nicht als Hauptfunnel zurückkehren.
- `scripts/build-theme-dist.sh` wird multi-funnel-fähig.
- Pro Kunde kann ein Funnel aus der kanonischen Schablone abgeleitet und branchenspezifisch angepasst werden.

## Nachtrag 2026-05-07

Der Default-Fragepfad der Anfragesystem-Analyse bleibt lokal und ohne personenbezogene Daten. Der separate Kontakt-Schritt ist inzwischen hinter sichtbarer Einwilligung aktiv: WordPress REST speichert Analyse-Leads in `nexus_contact`, Brevo versendet Transaktionsmails. n8n bleibt für diese Route nicht angebunden.

## Nachtrag 2026-05-13

Der EnergieFahrplan-Showroom wurde aus dem Repo-Funnel entfernt. Die Beweisführung läuft über E3, Methodik und die direkte Anfragesystem-Analyse.

## Nachtrag 2026-09-22

Es gibt keinen aktiven React-Funnel-Layer mehr. Die Anfragesystem-Analyse ist
durch den Marktcheck auf `/solar-waermepumpen-leadgenerierung/#marktcheck`
ersetzt (`docs/specs/anfrage-system-analyse-form-v1.md`, Status
`superseded-by-marketcheck`); der Marktcheck ist Vanilla-JavaScript und sendet
an `nexus/v1/audit-request`. `blocksy-child/readiness/` existiert nicht mehr,
und der Endpoint `analysis-submit` ist seit diesem Datum standardmäßig
abgeschaltet (`HU_FEATURE_READINESS_SUBMIT`). Neue Funnel-Erlebnisse entstehen
als PHP-Templates mit Vanilla-JavaScript, wie es `AGENTS.md` vorgibt.
