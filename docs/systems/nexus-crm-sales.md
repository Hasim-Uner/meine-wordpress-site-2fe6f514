# Nexus CRM Sales Operations

Stand: 2026-09-10

## Zweck

Nexus CRM trennt Pre-Sales von der späteren Projektabwicklung:

1. `nexus_contact` = gemeinsame Kontaktidentität.
2. `nexus_opportunity` = konkrete Vertriebschance mit Pipeline-Stufe, Potenzialwert und nächster Aktion.
3. `nexus_crm_activity` = kanalneutrale Aktivitätshistorie für CRM-Ereignisse und Kommunikation.
4. Das externe Kundenportal beginnt erst nach gewonnenem Auftrag und bleibt für Onboarding, Projektsteuerung, Dateien und Freigaben zuständig.

## Pipeline

Kanonische Stufen:

- Neu
- Qualifiziert
- Kontakt aufgenommen
- Gespräch
- Angebot
- Follow-up
- Später / Nurture
- Gewonnen
- Verloren

Der Kontakt bleibt die Person/Firma; eine Opportunity ist der konkrete Verkaufsfall. Dadurch kann das Modell später mehrere Verkaufschancen pro Kontakt tragen, ohne die Kontaktakte zu überladen.

## Lead-Sync

Neue vertriebsrelevante `nexus_contact`-Datensätze werden automatisch in eine offene Opportunity überführt. Sales-relevant sind aktuell Projektanfragen, allgemeine Kontaktanfragen und Analyse-/Marktcheck-Leads. Reine Blog-Abos und Bestandskundenanliegen erzeugen keine Opportunity.

`nexus_review_request` bleibt der spezialisierte Intake-Datensatz. Nach gespeicherter Qualifikation wird der Kontakt in `nexus_contact` gespiegelt und mit einer Opportunity verknüpft. Der spezialisierte Audit-Datensatz wird nicht ersetzt.

Bestehende Kontakte und Review-Requests werden nicht still im Hintergrund migriert. Die Vertriebsseite bietet dafür die explizite Aktion `Bestehende Leads übernehmen`.

## Follow-ups

Jede Opportunity besitzt:

- nächste Aktion
- Fälligkeit
- Pipeline-Wert
- interne Notizen
- optional ein einmalig geplantes automatisches E-Mail-Follow-up

Automatische E-Mail-Follow-ups sind pro Opportunity standardmäßig deaktiviert. Wird ein Follow-up explizit aktiviert, prüft ein stündlicher WordPress-Cron-Lauf die Fälligkeit und sendet über die vorhandene `wp_mail`-/Brevo-Schicht. Überschneidende Cron-Läufe werden per Lock abgefangen. Fehlversand wird maximal dreimal versucht und anschließend für manuelle Prüfung deaktiviert.

Blog-DOI und Sales-Follow-up bleiben getrennte Prozesse.

## Kommunikation

`nexus_record_crm_activity()` ist der gemeinsame Adapterpunkt für Timeline und Kommunikationsansicht. Gespeichert werden unter anderem:

- Kontakt und Opportunity
- Ereignistyp
- Kanal
- Richtung (`inbound`, `outbound`, `internal`)
- Betreff und Inhalt
- Provider und Provider-ID
- Zustellstatus
- Zeitpunkt

Aktuell schreibt Nexus CRM eigene CRM-Ereignisse sowie manuell oder automatisch versendete E-Mails in diese Timeline.

Eingehende E-Mail, WhatsApp und SMS sind bewusst noch keine behauptete Live-Funktion. Dafür ist jeweils ein verifizierter Provider-/Webhook-Adapter erforderlich. Neue Adapter sollen ausschließlich in die gemeinsame Activity-Schicht schreiben und nicht eigene Kontakt- oder Pipeline-Silos anlegen.

## Admin-Oberflächen

Unter `Nexus CRM` kommen zwei operative Bereiche hinzu:

- `Vertrieb`: KPIs, Follow-up-Queue, Pipeline-Board, manuelle Lead-Anlage und Opportunity-Akte.
- `Kommunikation`: zentrale kanalneutrale Aktivitätsansicht.

Die bestehende CRM-Kontaktliste erhält zusätzlich Vertriebsstufe, Pipeline-Wert, nächste Aktion und einen Vertriebsfilter. In der Kontaktakte verlinkt eine kompakte Vertriebsbox die aktive Opportunity.

## Daten- und Systemgrenzen

- WordPress bleibt Source of Truth für Pre-Sales.
- Brevo bleibt Versand-/Transportebene und wird nicht zum führenden CRM.
- n8n bleibt inaktiv und ist für diese Pipeline nicht erforderlich.
- Das Kundenportal bleibt Source of Truth für Post-Sales-Projektabwicklung.
- Keine API-Schlüssel oder Provider-Secrets werden im Repo gespeichert.

## Relevante Dateien

- `blocksy-child/inc/crm.php`
- `blocksy-child/inc/review-crm.php`
- `blocksy-child/inc/crm-sales.php`
- `blocksy-child/inc/crm-sales/`
- `blocksy-child/assets/css/nexus-crm-sales-admin.css`
- `blocksy-child/inc/mail.php`
