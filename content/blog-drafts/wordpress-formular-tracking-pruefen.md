## WordPress Publish-Pack

- Status: Entwurf
- Slug: `wordpress-formular-tracking-pruefen`
- Kategorie: Tracking
- Zielgruppe: Geschäftsführung, Marketing und Vertrieb mit einem WordPress-Anfrageformular
- Suchfrage: Wie prüfe ich, ob eine Formularanfrage ankommt und genau einmal gezählt wird?
- SEO-Titel: WordPress-Formular prüfen: Zustellung und Zählung testen
- Meta-Beschreibung: Das Formular meldet Erfolg, doch Postfach und Auswertung zeigen andere Zahlen. Drei Testfälle helfen, die Zustellung und Zählung Ihrer Anfragen zu prüfen.
- Auszug: Im Bericht stehen zwei Anfragen, im Postfach liegt eine. Drei lokale Testfälle zeigen, wie Sie Formularannahme, Zustellung und Zählung getrennt prüfen.
- Primärer CTA: Conversion-Tracking für Anfragen einrichten lassen → `/ga4-tracking-setup/`
- Rückverweis nach Veröffentlichung: Auf der Tracking-Angebotsseite im Abschnitt zum Abnahmeprotokoll auf diesen Beitrag verlinken.

# WordPress meldet „Anfrage gesendet“. Ist sie angekommen und genau einmal gezählt worden?

Im Kampagnenbericht stehen zwei Anfragen. Im Vertrieb liegt eine Mail. Bevor Sie Budget verschieben, verfolgen Sie eine einzelne Testanfrage vom Formular bis ins Postfach und in die Auswertung. Stimmen die drei Stellen für denselben Versuch überein, wissen Sie mehr als nach einem Vergleich zweier Monatssummen.

**Für den Test brauchen Sie eine eindeutige Kennung und einen Satz, der festlegt, was bei Ihnen zählt.** Zum Beispiel: „Eine Anfrage zählt, sobald WordPress ein gültiges Formular angenommen hat.“ Dann darf eine fehlende Vertriebs-Mail trotz korrekter Zählung vorkommen. Lautet Ihre Definition dagegen „im CRM angelegter Vorgang“, endet der Test erst beim CRM-Eintrag. Eine qualifizierte Anfrage entsteht noch einen Schritt später, wenn der Vertrieb sie geprüft hat.

## Ein Klick, drei mögliche Ergebnisse

Diese drei Durchläufe stammen aus einem lokalen Browsertest vom 22. November 2026. Ein Demoformular nahm jede Eingabe an. Ein lokales Testpostfach und ein separater Ereigniszähler machten die beiden nächsten Schritte einzeln sichtbar. In zwei Durchläufen war jeweils ein Fehler absichtlich eingebaut.

| Testkennung | Meldung im Browser | Vom Formular angenommen | Im Zielpostfach | In der Auswertung |
|---|---|---:|---:|---:|
| FORM-01 | Anfrage angenommen | 1 | 1 | 1 |
| FORM-02 | Anfrage angenommen | 1 | 1 | 2 |
| FORM-03 | Anfrage angenommen | 1 | 0 | 1 |

Bei **FORM-02** erhielt der Vertrieb genau eine Nachricht. Der Demo-Code meldete denselben Vorgang zweimal an die Auswertung. Wer nur den Bericht liest, könnte der Kampagne für denselben Kontakt zweimal Erfolg zuschreiben.

Bei **FORM-03** stimmten Formular und Zähler überein. Der Fehler lag auf dem Weg ins Postfach. Eine Änderung am Tracking hätte dem Vertrieb die fehlende Nachricht nicht zurückgebracht.

Der Versuch verwendete künstliche Anfragen, einen lokalen Zähler und ein lokal beschriebenes Testpostfach. Er prüfte weder einen öffentlichen Mailversand noch ein bestimmtes WordPress-Plugin. Für die eigene Website ist deshalb der Eintrag im tatsächlich vereinbarten Ziel entscheidend.

## So prüfen Sie Ihr eigenes Formular

1. **Legen Sie einen Testfall fest.** Wählen Sie das Formular, über das ein Interessent wirklich anfragt. Vereinbaren Sie mit dem Vertrieb, welches Postfach oder welche CRM-Ansicht zählt. Verwenden Sie Testdaten und eine einmalige Kennung wie „FORM-A“. Für den Abgleich genügen Testkennung und Uhrzeit. Namen, E-Mail-Adresse und Nachrichtentext gehören nicht in die Ereignisauswertung.
2. **Senden Sie genau einmal ab.** Notieren Sie Uhrzeit und Rückmeldung. Prüfen Sie, falls zugänglich, ob das Formularsystem den Vorgang als angenommen führt. Eine Schaltfläche kann auf einen Klick reagieren, obwohl ein Pflichtfeld fehlt. Ein Klick allein taugt deshalb schlecht als Maß für eingegangene Anfragen.
3. **Lassen Sie den Empfänger nachsehen.** Taucht die Kennung beim Vertrieb auf? Eine Kopie an die Testperson beantwortet diese Frage nicht. Geht eine Mail zunächst an ein Sammelpostfach und von dort ins CRM, prüfen Sie beide Stationen. So erkennen Sie, an welcher Stelle die Spur endet.
4. **Vergleichen Sie den einzelnen Vorgang.** Sehen Sie im vorhandenen Ereignisprotokoll nach, wie oft die vereinbarte Aktion auftrat. Halten Sie Testumgebung, Uhrzeit und Messbedingungen fest. Bietet Ihr Bericht nur zeitversetzte Summen, bleibt die Einzelzuordnung offen. Eine kurzfristige Differenz reicht dann für die Diagnose nicht aus.
5. **Versuchen Sie es ohne zweite Anfrage.** Laden Sie nach einem erfolgreichen Test die Bestätigungsseite neu. Der Zähler für angenommene Anfragen muss dabei gleich bleiben. Prüfen Sie separat einen ungültigen Versuch mit leerem Pflichtfeld. Weder Postfach noch Anfragezähler sollten dafür einen Zugang zeigen.

## Was Sie aus einer Abweichung ableiten können

**Eine Mail, zwei Ereignisse:** Prüfen Sie zuerst, wodurch beide Ereignisse ausgelöst wurden. Ein Klick auf „Senden“ und die anschließende Erfolgsbestätigung können getrennt erfasst werden. Wenn beide dieselbe Anfrage repräsentieren sollen, legen Sie eine maßgebliche Bestätigung fest. Wiederholen Sie danach denselben Versuch mit neuer Testkennung.

**Ein Ereignis, keine Mail:** Suchen Sie den Eintrag zuerst im Formularsystem. Ist er dort vorhanden, kontrollieren Sie Empfängeradresse, Versandprotokoll und den tatsächlichen Eingang einschließlich Filter und Weiterleitung. Steht im Versandprotokoll „versendet“, fehlt noch die Bestätigung des Vertriebs: Ist genau diese Testnachricht dort auffindbar?

**Eine Mail, kein Ereignis:** Der Vertrieb kann diese Anfrage bereits bearbeiten. Prüfen Sie für die Auswertung, welche Aktion als Erfolg definiert wurde, ob sie bei diesem Formular eintritt und unter welchen Messbedingungen der Test lief. Wegen einer fehlenden Zählung muss der Interessent sein Formular nicht noch einmal senden.

Für die Abnahme genügt eine Zeile pro Versuch: Testkennung, erwartetes Ergebnis, Formularannahme, Eingang, Zählung und offene Abweichung. So können Marketing und Vertrieb denselben Fehler besprechen, ohne über unterschiedliche Zahlen zu streiten.

Wenn Ihre Anfragen ankommen und die Zahlen trotzdem auseinanderlaufen, können Sie die Messstrecke samt Testfällen und Abnahmeprotokoll prüfen lassen. [Conversion-Tracking für Anfragen einrichten lassen](https://hasimuener.de/ga4-tracking-setup/).
