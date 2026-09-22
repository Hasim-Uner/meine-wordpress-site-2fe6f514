# Nexus CRM Sales Operations

Stand: 2026-09-22

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

Neue vertriebsrelevante `nexus_contact`-Datensätze werden automatisch in eine offene Opportunity überführt. Sales-relevant sind Projektanfragen, allgemeine Kontaktanfragen, White-Label-Anfragen (Quelle und Segment `whitelabel_request`, seit 2026-09-22) und Marktcheck-Leads (`nexus_crm_contact_is_sales_relevant()` in `inc/crm-sales/sync.php`). Reine Blog-Abos und Bestandskundenanliegen erzeugen keine Opportunity.

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

## Antwortfrist-Wächter

`inc/crm-sales/watchdog.php` hängt am selben stündlichen Cron-Event. Er meldet
Opportunities in den Stufen Neu, Qualifiziert oder Später, die seit Anlage
weder eine ausgehende Mail über das CRM (`outbound`-Aktivität oder
`_nexus_opportunity_last_contact_at`) noch einen Stufenwechsel haben. Der
Erinnerungspunkt liegt sechs Werktagsstunden vor Ablauf der zugesagten
Antwortzeit (`nexus_compute_intake_response_deadline()`, gleiche Konstante wie
die sichtbare Zusage). Pro Lauf geht eine interne Sammelmail an die
Benachrichtigungsadresse; jede Opportunity wird einmal gemeldet
(`_nexus_opportunity_response_reminder_at`) und bekommt eine Aktivität
`response_reminder`. Ausgenommen sind manuell angelegte Leads und
Opportunities, die älter als 14 Tage sind. Fehlversand wird dreimal versucht.

Wer außerhalb des CRM antwortet, setzt danach die Stufe; das beendet die
Überwachung für diese Opportunity.

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

Aktuell schreiben in diese Timeline:

- eigene CRM-Ereignisse (Anlage, Stufenwechsel)
- manuell oder automatisch versendete E-Mails
- jede Kontakt- und White-Label-Anfrage als `inbound_inquiry` mit Anfragetext und Herkunft, auch wenn der Kontakt schon existiert; so gehen wiederholte Anfragen nicht im Upsert verloren
- gescheiterte interne Benachrichtigungen (`nexus_record_lead_notification_failure()`), zusätzlich als Hinweis im Dashboard und in den CRM-Ansichten
- Antwortfrist-Erinnerungen (`response_reminder`)

Eingehende E-Mail, WhatsApp und SMS sind bewusst noch keine behauptete Live-Funktion. Dafür ist jeweils ein verifizierter Provider-/Webhook-Adapter erforderlich. Neue Adapter sollen ausschließlich in die gemeinsame Activity-Schicht schreiben und nicht eigene Kontakt- oder Pipeline-Silos anlegen.

## Admin-Oberflächen

Unter `Nexus CRM` kommen zwei operative Bereiche hinzu:

- `Vertrieb`: KPIs, Follow-up-Queue, Pipeline-Board, manuelle Lead-Anlage und Opportunity-Akte.
- `Kommunikation`: zentrale kanalneutrale Aktivitätsansicht.

Die bestehende CRM-Kontaktliste erhält zusätzlich Vertriebsstufe, Pipeline-Wert, nächste Aktion und einen Vertriebsfilter. In der Kontaktakte verlinkt eine kompakte Vertriebsbox die aktive Opportunity.

## Herkunft

Kontakt- und White-Label-Anfragen tragen die Herkunft aus der Browser-Session:
Landing-, Einstiegs-, vorherige und Referrer-URL, Kampagnenquelle und
Suchbegriff, `utm_medium`, `utm_campaign` sowie die freiwillige Angabe, wie
jemand aufmerksam wurde. `nexus_sanitize_inquiry_attribution()` bereinigt die
Werte, `nexus_get_inquiry_attribution_meta()` legt die Meta-Keys
`_nexus_contact_*` fest (jeweils `inc/crm.php`). Die Kontaktakte zeigt die
Herkunft der letzten Anfrage, die interne Mail einen Block „Herkunft“.

## Daten- und Systemgrenzen

- WordPress bleibt Source of Truth für Pre-Sales.
- Brevo bleibt Versand-/Transportebene und wird nicht zum führenden CRM.
- n8n bleibt inaktiv und ist für diese Pipeline nicht erforderlich.
- Das Kundenportal bleibt Source of Truth für Post-Sales-Projektabwicklung.
- Keine API-Schlüssel oder Provider-Secrets werden im Repo gespeichert.

## Relevante Dateien

- `blocksy-child/inc/crm.php`
- `blocksy-child/inc/review-crm.php`
- `blocksy-child/inc/contact-page.php`
- `blocksy-child/inc/whitelabel-request.php`
- `blocksy-child/inc/crm-sales.php`
- `blocksy-child/inc/crm-sales/`
- `blocksy-child/assets/css/nexus-crm-sales-admin.css`
- `blocksy-child/inc/mail.php`
