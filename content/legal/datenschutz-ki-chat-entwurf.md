# ENTWURF: Datenschutz-Abschnitt „KI-Assistent“ (`#ki-chat`)

Status: **Entwurf, nicht live, vor Veröffentlichung juristisch prüfen.**
Ziel: `blocksy-child/page-datenschutz.php`, neuer Abschnitt nach „4. Kontaktaufnahme
und Formulare“ mit `id="ki-chat"`, dazu die Anpassung von Abschnitt 2 (unten).
Grundlage: technischer Stand laut `docs/architecture/CHAT_ASSISTANT.md`. Wo dieser
Text etwas über AWS behauptet, muss es vor Veröffentlichung in der AWS-Konsole und
den Vertragsunterlagen tatsächlich bestätigt sein (siehe Prüfpunkte).

---

## Textvorschlag für die Seite

### 5. KI-Assistent

Auf dieser Website können Sie Fragen an einen KI-Assistenten stellen. Er ist kein
Mensch und antwortet automatisch aus den Inhalten dieser Website und unseren
festgelegten Angebots- und Preisangaben. Antworten können unvollständig oder
fehlerhaft sein. Verbindlich sind die Angaben auf den jeweiligen Seiten und
unsere persönliche Rückmeldung.

Der Assistent wird erst geladen, wenn Sie ihn öffnen. Vorher wird dafür nichts
übertragen und nichts in Ihrem Browser gespeichert.

**Was verarbeitet wird.** Wenn Sie eine Nachricht senden, übermittelt Ihr Browser
den bisherigen Gesprächsverlauf dieser Sitzung und den Pfad der Seite, auf der
Sie sich befinden, an unseren Server. Unser Server leitet diese Angaben zusammen
mit unseren Anweisungen an das Sprachmodell weiter und gibt die Antwort an Sie
zurück. Wir speichern den Gesprächsinhalt dabei nicht auf unserem Server. Für den
Missbrauchsschutz und die Kostenbegrenzung verarbeiten wir kurzfristig einen aus
Ihrer IP-Adresse gebildeten Einwegwert (Hash) und zählen Anfragen; außerdem
speichern wir zusammengefasste Kosten- und Mengenzahlen ohne Bezug zu Ihrer Person.

**Sprachmodell über Amazon Bedrock.** Die Antworten erzeugt ein Sprachmodell
(Claude von Anthropic), das wir über den Dienst Amazon Bedrock der Amazon Web
Services EMEA SARL, 38 Avenue John F. Kennedy, L-1855 Luxemburg, nutzen. Die
Verarbeitung erfolgt in Rechenzentren in der Europäischen Union; als Region ist
Frankfurt am Main festgelegt, einzelne Anfragen können innerhalb der EU auf
andere Regionen verteilt werden. AWS ist für uns als Auftragsverarbeiter gemäß
Art. 28 DSGVO tätig. Nach Angaben von AWS werden Eingaben und Antworten nicht zum
Training der Modelle verwendet und nicht an den Modellanbieter Anthropic
weitergegeben. Die Protokollierung der Modellaufrufe bei AWS haben wir abgeschaltet.
[PRÜFEN: Speicherdauer bei AWS, Missbrauchserkennung, EU-Regionen des
Inferenzprofils, Drittlandbezug der Amazon-Unternehmensgruppe und Hinweis auf das
EU-U.S. Data Privacy Framework bzw. Standardvertragsklauseln.]

**Bitte keine sensiblen Daten.** Geben Sie im Chat keine Gesundheitsdaten,
Zugangsdaten, Zahlungsdaten oder vertraulichen Geschäftsinformationen ein.

**Übergabe an Haşim Üner.** Wenn Sie es wünschen, bereitet der Assistent eine
Anfrage vor. Sie sehen eine Zusammenfassung, können Name, E-Mail-Adresse und
Firma prüfen und entscheiden selbst, ob Sie die Anfrage absenden. Erst dann
speichern wir Name, E-Mail-Adresse, gegebenenfalls Firma, die Zusammenfassung und
die Seite im WordPress-Backend und erhalten eine interne Benachrichtigung per
E-Mail über Brevo (siehe „E-Mail-Zustellung über Brevo“). Den Gesprächsverlauf
übernehmen wir nur, wenn Sie das beim Absenden ausdrücklich auswählen. Der
Assistent verschickt keine E-Mails an Sie oder an andere Adressen.

**Browser-Speicher.** Damit Ihr Gespräch beim Wechsel zwischen Seiten erhalten
bleibt, speichert der Assistent den Verlauf im Sitzungsspeicher Ihres Browsers
(sessionStorage), aber erst, nachdem Sie ihn geöffnet haben. Der Speicher wird
gelöscht, wenn Sie den Tab schließen oder im Chat „Neu starten“ wählen. Es werden
keine Cookies gesetzt und kein dauerhafter Speicher (localStorage) genutzt.

**Rechtsgrundlagen.** Die Beantwortung Ihrer Fragen erfolgt auf Grundlage von
Art. 6 Abs. 1 lit. b DSGVO, soweit sie der Anbahnung eines Auftrags dient, und im
Übrigen auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO; unser berechtigtes Interesse
liegt darin, Fragen zu unserem Angebot schnell und aus den eigenen Inhalten zu
beantworten. Die Übergabe Ihrer Anfrage erfolgt auf Ihre Veranlassung nach
Art. 6 Abs. 1 lit. b DSGVO; die Übernahme des Gesprächsverlaufs auf Grundlage
Ihrer Einwilligung nach Art. 6 Abs. 1 lit. a DSGVO, die Sie jederzeit mit Wirkung
für die Zukunft widerrufen können. Der Sitzungsspeicher ist für den von Ihnen
gewünschten Chat unbedingt erforderlich (§ 25 Abs. 2 Nr. 2 TDDDG).
[PRÜFEN: Rechtsgrundlagen-Zuordnung, ob für das Senden von Nachrichten selbst eine
Einwilligung vorzuziehen ist.]

**Speicherdauer.** Gesprächsinhalte speichern wir nur im Fall einer Übergabe, und
dann wie andere Anfragen (siehe „Speicherdauer“ oben). Die Einwegwerte für den
Missbrauchsschutz verfallen nach spätestens einer Stunde.

---

## Notwendige Anpassung an bestehender Stelle

Abschnitt „2. Cookies, Tracking und Browser-Speicher“ sagt heute, dass nichts im
Browser gespeichert wird, auch nicht im sessionStorage. Mit dem öffentlichen
Assistenten stimmt das nicht mehr uneingeschränkt. Vorschlag für den ersten Absatz:

> Bei der rein informatorischen Nutzung dieser öffentlich zugänglichen Website setzen
> wir keine Cookies und speichern nichts in Ihrem Browser. Einzige Ausnahme: Wenn Sie
> den KI-Assistenten öffnen, speichert er den Gesprächsverlauf im Sitzungsspeicher
> Ihres Browsers, bis Sie den Tab schließen (siehe [KI-Assistent](#ki-chat)).

Ebenso den Satz „keine Speicherung im Browser für Statistik, Tracking oder
Komfortfunktionen“ prüfen (der Chat-Verlauf ist eine Komfortfunktion des
angeforderten Dienstes) und im Kurzüberblick sowie im Hero die Aussage
„speichert nichts in Ihrem Browser“ entsprechend einschränken.

---

## Prüfpunkte vor Veröffentlichung (juristisch und technisch)

1. **KI-Transparenz** (Art. 50 Abs. 1 KI-Verordnung): Der Hinweis im Chat-Fenster
   und hier ausreichend? Formulierung „KI-Assistent“ im Fenster ist umgesetzt.
2. **AWS-Vertrag**: AVV/DPA in den AWS Service Terms bestätigt, EU-Regionen des
   Inferenzprofils `eu.anthropic.…` in der Konsole geprüft, Model Invocation
   Logging nachweislich aus, Speicherdauer und Missbrauchserkennung bei Bedrock
   nachgelesen und im Text korrekt.
3. **Drittlandbezug**: Amazon-Konzern (USA); Formulierung zu DPF/SCC und zu
   möglichen Zugriffen prüfen.
4. **Rechtsgrundlagen** wie oben markiert; Einwilligungstext der Übergabekarte
   („Ich bin einverstanden, dass meine Angaben zur Bearbeitung meiner Anfrage
   gespeichert werden“) und Opt-in für den Verlauf abstimmen.
5. **Browser-Speicher**: Einordnung des sessionStorage nach § 25 TDDDG; Abschnitt 2,
   Kurzüberblick und Hero konsistent anpassen.
6. **Verzeichnis von Verarbeitungstätigkeiten** um „KI-Assistent“ ergänzen.
7. **Gate**: Der öffentliche Orb wird erst nach dieser Prüfung, der bestandenen
   Bedrock-Eval (≥ 18/20, Prompt-Injection und Mail-Relay bestanden) und Hasims
   Freigabe aktiviert. Bis dahin sehen ihn nur eingeloggte Administratoren
   (`HU_CHAT_MODE=preview`).
