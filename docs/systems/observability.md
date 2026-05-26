# Observability (Light)

## Architektur-Überblick
Um fehlgeschlagene API-Aufrufe (besonders Lead-Eingänge im `nexus/v1`-Layer) zuverlässig zu monitoren, ohne die Performance oder die Datenbank durch zu viele Writes zu beeinträchtigen, verwendet die Architektur ein "Observability light"-Muster.

Kernkomponenten:
1. **API Telemetry Module (`inc/api-telemetry.php`)**: Ein schlankes Modul, das sich über den WordPress-Filter `rest_post_dispatch` in den Request-Lifecycle einklinkt.
2. **In-Memory / Transient Ring-Buffer**: Fehlgeschlagene Requests (HTTP Status >= 400) werden in einem Array gesammelt. Dieses Array wird als WordPress Transient (`nexus_api_telemetry_logs`) gespeichert. Die Liste ist auf maximal 100 Einträge limitiert, um den Speicherverbrauch niedrig zu halten.
3. **Admin-Dashboard**: Unter "Nexus CRM -> API Fehler" existiert eine geschützte (Capability `edit_pages`) Ansicht, die die Einträge tabellarisch aufbereitet. 

## Datenpunkte (DSGVO-konform)
Bei einem Fehler werden ausschließlich strukturierte, unkritische Metadaten erfasst:
- `timestamp`: Zeitpunkt des Fehlers.
- `route`: Der aufgerufene Endpunkt (z. B. `/nexus/v1/review-request`).
- `traceId`: Die eindeutige Trace-ID (aus dem Header `X-Nexus-Trace-Id` oder der Response).
- `error_code`: Der maschinenlesbare Fehlercode.
- `http_status`: Der HTTP Statuscode (z. B. 400, 429, 500).
- `ip_hash`: Ein gesalzener MD5-Hash der Nutzer-IP (`md5($ip . HU_SALT)`), um eine grobe Zuteilung zu ermöglichen, ohne die echte IP-Adresse zu speichern.
- `payload_keys`: Die Keys des gesendeten Payloads. Die eigentlichen Werte (z. B. Namen, E-Mail-Adressen) werden verworfen.

## Wartung
Das Admin-Menü bietet eine einfache "Logs leeren"-Funktion, mit der der Transient manuell zurückgesetzt werden kann. Der Transient verfällt zudem automatisch nach 30 Tagen.
