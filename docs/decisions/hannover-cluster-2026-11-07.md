# Hannover-Cluster: Bestands-WooCommerce und Anfrageherkunft

Auftrag und Freigabe: Hasim, 07.11.2026. Quelle ist der
[Opus-Plan, Teil A](https://app.notion.com/p/411aa8436293810fa1b1d1f57dcc3dda).
Umsetzung als ein PR; Veröffentlichung und produktive Abnahme separat.

## Bestehende Rollen erhalten

`/wordpress-agentur-hannover/` bleibt der lokale Agentur-Owner.
Der Freelancer-Owner ist bereits seit der Konsolidierung vom 13.09. `/`.
`/wordpress-freelancer-hannover/` hat bereits eine 301 dorthin; sein Template
ist ein Kompatibilitätsrenderer. Die Prüfung des Ist ergibt daher keinen
Auftrag für eine weitere Landingpage, neue Weiterleitung oder Löschung.
Die beschreibenden Links im gemeinsamen Footer bleiben erhalten.

## Enger WooCommerce-Umfang

Die lokale Agenturseite ergänzt einen kurzen Abschnitt für bestehende Shops:
Tracking, Checkout-Conversion und Wartung. Shop-Neubauten sind ausgeschlossen.
Leistungskarte, H2-Abschnitt und Service-Katalog beschreiben denselben Umfang.
Nur der neue CTA verwendet `type=project&focus=implementation_scope`.
Die vorhandenen Kontaktparameter und Anfrageziele bleiben unverändert.

Die überholte WooCommerce-Keyword-Sperre aus der Solar-/Shopify-Positionierung
wird durch die Zuordnung zur Hannover-Seite mit diesem engen Umfang ersetzt.
Aus dem Plan werden keine ungeprüften Ranking-, Suchvolumen- oder
Autoritätszahlen in öffentliche Texte übernommen.

## Herkunft beim Absenden

Vorhandene CRM- und Mailfelder werden wiederverwendet: Formularseite,
Einstiegsseite soweit bekannt, interne Vorseite, Referrer sowie Quelle, Medium
und Kampagne. `utm_source` bleibt als `ads_source` kompatibel zum bestehenden
Vertrag. Versteckte Felder sind bis zur Absendung leer und werden bei jedem
Versuch aktuell befüllt. Keine Cookies, kein localStorage, keine neue
Sitzungsspeicherung. Der bereits vorhandene opt-in-Sitzungsweg in NexusCore
wird durch diesen Auftrag weder aktiviert noch erweitert.

Ohne Speicherung über mehrere Seiten ist die echte erste Einstiegsseite nach
einem internen Wechsel unbekannt. Dieses Feld bleibt leer; die bekannte Vorseite
wird getrennt gespeichert. Referrer und Seitenadressen verlieren Query und
Fragment. Kampagnenwerte stehen in den dafür vorgesehenen separaten Feldern.

## Abnahme

Den neuen Abschnitt auf schmalem und breitem Bildschirm prüfen, bestehenden
Freelancer-Redirect beibehalten, Service-Daten mit sichtbarem Umfang abgleichen.
Formular-Test: Herkunft erst bei Absendung, korrekte URL-Bereinigung,
aktuelle Werte beim erneuten Versuch, bestehende Anfragequelle und Speicherung
im CRM sowie interne Mail. Testgrenzen und Vorher/Nachher gehören in den PR.
Teil B (Verzeichnisse, manuelle Maßnahmen, alte Nummer, Rank-Tracker) bleibt
gemäß Plan bei Hasim.
