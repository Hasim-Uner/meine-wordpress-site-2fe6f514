# Die Anfrage-Website — Messung

Hypothese: Produktname, Lieferumfang und berechenbarer Endpreis vor dem ersten
Klick erhöhen passende Anfragen je Seitenaufruf. Prüfpunkt: acht Wochen nach
Livegang oder 300 Aufrufe (was zuerst eintritt). Freigabe 02.10.2026;
Livegangdatum und Ausgangswerte beim Merge ergänzen, nicht aus der Freigabe ableiten.

Primär: bestätigte Website-Formularanfragen / Aufrufe der Produktseite.
Sekundär: CTA-Klicks nach Position, Rechnernutzung nach Seitenzahl/Art/Tracking,
Schalterwechsel, Anfragequalität (passend/unklar/unpassend) im CRM.
Matomo-Kategorie `anfrage_website`: `cta_click` mit position und Umfang,
`rechner_change` mit seiten/art/tracking, `toggle_durchleuchtung` mit modus,
`form_submit` nach Erfolg mit seiten/art/tracking. Eventname ist JSON mit
freigegebenen Dimensionen, keine Namen, E-Mail-Adressen, Nachrichten oder IDs.
Der bestehende Matomo-Tracker muss die Queue abholen; auf Produkt und Kontakt
wird `disableCookies` gesetzt. Den realen Empfang beim Livegang prüfen.

Acht FAQ folgen der finalen Version 6; die frühere Begrenzung auf vier gilt nicht
mehr. Der lokale Kontextlink ist Teil der Änderung und kann die Besucherqualität
verändern. Vorher/Nachher daher mit Herkunft und absoluten Anfragezahlen auswerten;
300 Aufrufe erlauben bei wenigen Anfragen keinen sicheren Wirkungsnachweis.
Bewerten: beibehalten bei besserem Anfrageanteil und gleicher/besserer Qualität;
bei Preis-/Umfangsfragen Formularübergabe und Copy prüfen, bei geringem Zufluss
Sichtbarkeit zuerst bearbeiten. Keine unbelegten Conversion-Ausgangswerte.
