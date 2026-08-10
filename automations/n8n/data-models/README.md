# Data Models

Hier liegen implizite oder explizite Payload-Contracts zwischen n8n und anderen Systemen.

Aktuell wichtig:

- Frontend-Payload fuer den Customer Journey Audit
- V3-Contract fuer den Instant-Results-Refactor
- Anfragesystem-Analyse Submit-Payload v1 für den neuen Analyse-Einstieg. Der bestehende Contract-Dateiname `readiness-diagnosis-payload.v1.contract.json` bleibt bis zur nächsten Contract-Version intern stabil.

Ziel:

- kein versteckter Contract nur im Code
- klare Versionsbasis für Theme, n8n und künftige Agenten
- Default-Pfade ohne unnötige personenbezogene Daten
