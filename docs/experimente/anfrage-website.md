# Die Anfrage-Website — Messung

Hypothese des Flow-Durchgangs vom 05.10.2026: Eine klare Entscheidungsfolge,
früh sichtbarer Preis und ausdrücklich gewählte Extras erhöhen den Anteil
passender Website-Anfragen. Die leichtere Gestaltung allein belegt keinen
Conversion-Effekt. Kein Livegangdatum oder Ausgangswert aus der Freigabe ableiten;
beides erst beim Merge/Deployment dokumentieren.

Primär: bestätigte, als passend eingeordnete Website-Projektanfragen im CRM
(`_nexus_contact_focus=website`) relativ zu relevanten Aufrufen der Produktseite
in Koko. Rohzahlen und Anteil berichten. Das serverseitige `anfrage_gesendet`
(`inc/inquiry-events.php`) bestätigt die Annahme, zählt aber das Kontaktformular
insgesamt; diesen Gesamtwert nicht als Website-Konversion ausgeben. Zuordnung
zur Produktseite und Besucherquelle vor einer Quotenauswertung prüfen.

`data-track-*`-Hooks bleiben erhalten. Es ist kein Matomo-/GTM-Tracker im Theme
angebunden; Klicks und Rechneränderungen dürfen ohne nachgewiesenen Empfänger
nicht als verfügbare Messreihe dargestellt werden. Dieser Durchgang ergänzt
keine Messung, Cookies, Browser-Speicherung oder Drittanbieter.

Prüfpunkt: acht Wochen nach Livegang oder 300 Aufrufe (was zuerst eintritt).
Die Schwelle ist ein Anlass zur Auswertung, kein Signifikanznachweis.
Anfragequalität (passend/unklar/unpassend), Preis-/Umfangsfragen und Quellenmix
mitbetrachten. Bei geringem Volumen bleiben Veränderungen Richtungsindikatoren;
ein Vorher/Nachher-Vergleich belegt keine Kausalität.

Zusätzlich qualitative Prüfung: Erstbesucher sollen Angebot, nächsten Schritt
und Gesamtpreis ihrer Auswahl erklären können. Vor der Designwahl den Aufpreis
vorhersagen lassen, danach einen passenden Umfang ohne Hilfe zusammenstellen.
Beibehalten, wenn Verständnis und Anfragequalität stimmen. Wiederkehrende
Rückfragen zuerst an der betreffenden Entscheidung und Übergabe prüfen.
