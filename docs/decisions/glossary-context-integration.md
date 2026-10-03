# Glossar als kontextuelle Hilfe

Stand: 2026-10-03. Definitionen und Ziele bleiben im bestehenden Glossarregister.

## Entscheidung

Startseite und 20 informative Seitentemplates erklären gezielt wenige Begriffe
im Fließtext. Die Auswahl folgt dem Verständnisbedarf an der jeweiligen Stelle:
WebP, Canonical, XML-Sitemap und strukturierte Daten im Website-Angebot;
Testumgebung auf der Landingpage; Consent Mode bei Tracking und White-Label;
Attribution, A/B-Test und Conversion in ihren fachlichen Zusammenhängen;
CRM, Intent, Vorqualifizierung, Lead-Score und CPL im Energie-Cluster.
Überschriften, Navigation, Akkordeon-Auslöser, Formulare und CTAs bleiben frei.

Blogartikel behalten ihre automatische Verlinkung, mit einem Link je Begriff,
einem je Absatz/Listeneintrag/Tabellenzelle und höchstens acht insgesamt.
Ausgewertet werden Titel und `keywords_match`, keine breiten Suchsynonyme.
Eine zweite Verarbeitung ergänzt keine weiteren Links. Fremde oder unvollständige
HTML-Fragmente werden unverändert zurückgegeben.

## Zuständigkeiten

| Quelle | Verantwortung |
| --- | --- |
| `inc/glossary/glossary-registry.php` | Definition, Status und bestehendes Ziel; Alias-Begriffe behalten ihren Query Owner |
| `inc/glossary/glossary-autolink.php` | Ein gemeinsames HTML-Format, explizite Helfer, Shortcode, vorsichtige Artikelverlinkung und Asset-Scope |
| `assets/css/glossary-links.css` | Dezenter Link und Definition mit den Tokens aus `system.css` |
| `assets/js/glossary-links.js` | Hover, Tastatur, Escape und Positionierung; kein Begriffskatalog im Browser |

Nur veröffentlichte, auflösbare Begriffe erhalten einen Link. Fehlende Ziele,
Entwürfe und Links auf dieselbe Seite, einschließlich Fragment-Aliasen, bleiben
Text. Die Auswahl verändert weder gespeicherte Inhalte noch Indexierung,
Canonical-Ziele oder die bestehenden Conversion-Hooks.

Templates verwenden `nexus_glossary_link( 'crm', 'CRM' )`; für einen bestehenden
Textwert gibt es `nexus_glossary_explain_text( $text, 'staging', 'Testumgebung' )`.
Redaktionelle Seiten können mit
`[hu_begriff slug="attribution"]Attribution[/hu_begriff]` gezielt optieren.
Die Helfer gehören ausschließlich in erklärenden Text, außerhalb vorhandener
Links, Überschriften und interaktiver Elemente. `data-glossary-skip` schließt einen
ganzen Bereich von der automatischen Verarbeitung aus.

## Browser-Verhalten

Hover öffnet nach kurzer Verzögerung; Tastaturfokus sofort. Die Definition bleibt
beim Wechsel des Zeigers in die Erklärung offen. Escape schließt sie, ohne den
Fokus zu verschieben. Der Link führt immer zum bestehenden Begriffsziel.
Auf Touch-Geräten genügt ein Tap für die Navigation. Ohne JavaScript bleibt der
Link nutzbar; `aria-describedby` verknüpft die serverseitige Kurzdefinition.

Native Popover bringen die Erklärung über abschneidende Layoutcontainer. Browser
ohne diese API erhalten einen am Dokumentkörper positionierten Fallback. Breite,
Höhe und Position werden an den sichtbaren Viewport angepasst, auch bei Scrollen,
mehrzeiligen Begriffen und Fensteränderungen. Es gibt keine Animation oder
zusätzlichen Requests für Definitionen, Cookies, Telemetrie oder Bibliotheken.
Die kleinen Assets laden nur für betroffene Templates, Artikel oder den Shortcode.

Die Browserentscheidung berücksichtigt die lokale Modern-Web-Guidance
`interest-triggered-tooltips`: `interestfor` bleibt wegen unvollständiger
browserübergreifender Unterstützung aus dem gemeinsamen Runtime-Pfad.
Die Interaktion folgt dem WAI-ARIA-Tooltip-Muster und WCAG 1.4.13
(schließbar, mit dem Zeiger erreichbar, solange benötigt sichtbar).

## Absicherung

`npm run test:glossary` prüft Register plus gezielte HTML-, Budget-, Ziel- und
Scope-Fälle. `glossary-links.spec.cjs` prüft native Anzeige und Fallback, Tastatur,
Escape, Touch, Betrieb ohne JavaScript, Viewport-Grenzen sowie die echten
Startseiten-, White-Label- und Website-Templates. Die Tests laufen in den
bestehenden Provisioning- und Navigation-Gates; `npm run check` bleibt der
gemeinsame lokale und CI-Einstieg.
