# Direct Mail: Print-Brief an eine Geschäftsführung

Herkunft: Das Copywriting-Prüfraster und die Bewusstseinsstufen stammen aus coreyhaines31/marketingskills. Dieses Repository enthält dort kein Direct-Mail-Material; die folgende Mechanik ist eine eigene Herleitung aus Grundprinzipien, kein Import. Kalte E-Mail und Social-Spam wurden bewusst verworfen (§ 7 UWG).

## Mechanik

Jede Regel mit einem Satz Begründung.

1. Ein Leser: eine namentlich genannte Geschäftsführung, ein Unternehmen. Ein Brief an alle ist ein Brief an niemanden.
2. Musterbruch durch Genauigkeit, nicht durch Gimmick. Der erste Absatz nennt eine beobachtbare Tatsache über genau dieses Unternehmen, etwa einen gemessenen Befund aus einer öffentlichen Prüfung der eigenen Website oder des Formulars, mit `[Q: …]`-Marker. Warum: nur Wissen über den Leser unterscheidet den Brief von Massenpost.
3. Stärkster Aufhänger ist das eigene Objekt des Lesers, zum Beispiel der gedruckte, kommentierte Screenshot seines Formulars. Keine allgemeine Marktstatistik als Einstieg; sie gehört niemandem und der Leser überfliegt sie.
4. Der Brief beginnt nicht mit „Ich“ oder „Wir“. Warum: ein Einstieg über den Absender sagt dem Leser nichts über sich.
5. Keine Sammelanrede („Sehr geehrte Damen und Herren“), keine nachgeahmte Handschrift, keine künstliche Dringlichkeit, keine Fristen oder Platzzähler (BRAND Anti-Patterns und Preferred Terms). Warum: jedes Signal von Massenversand entwertet die Genauigkeit des ersten Absatzes.
6. Länge: eine Seite, höchstens 350 Wörter. Warum: ein Brief, der nicht auf einen Blick erfassbar ist, wird zurückgestellt.

## Aufbau

1. Befund: der beobachtete Fakt über das Unternehmen.
2. Folge: was es kostet, nur wenn belegt. Fehlt der Beleg, entfällt der Schritt.
3. Mechanismus: genau einer, in Betriebssprache statt Technikjargon.
4. Beleg: genau einer; Zahlen ausschließlich aus dem Kanon (BRAND, Abschnitt „Fakten stehen nie als Text“), mit `[Q: canon:<Schlüssel>]`.
5. Ein Antwortweg: genau ein `[CTA]`. Die Wahl der Route folgt `docs/architecture/CONVERSION_ROUTING.md`; hier wird sie nicht wiederholt. Kontaktdaten und Antwortzeit kommen aus dem Kanon.
6. P.S.: der meistgelesene Teil. Es wiederholt entweder den beobachteten Fakt oder den einen Antwortweg.

Danach der Hinweis zu Datenquelle und Widerspruch (siehe Rechtsrahmen) als kleingedruckte Schlusszeile.

Hinweis zum Entwurf: Adressblock, Absender und Datum gehören ins Druck-Template, nicht in den Entwurf; Notizen stehen in HTML-Kommentaren und fallen mit `--strip` heraus.

## Prüfung

- Prüfraster aus `SKILL.md` auf jeden Satz, besonders auf Beleg und Swap-Test.
- Bewusstseinsstufe: Die Geschäftsführung kennt das Problem meist nicht (problem-unaware). Der nächste Schritt ist deshalb Erkennen und Antworten, nicht Kaufen.
- Positionierungskette: Was würde die Geschäftsführung sonst tun (nichts, intern lösen, Agentur)? Welcher eine Unterschied trägt? Nur dieser steht im Brief.
- `channel-check.py brief` muss ohne Befund laufen; Zahlen ohne Marker, Agentursprache, gesperrte Begriffe, Fristen und „kostenlos“ sind Befunde, über die ein Mensch entscheidet.

## Rechtsrahmen

Keine Rechtsberatung, vor dem ersten Versand prüfen lassen.

- Briefwerbung an Unternehmen ist nach § 7 UWG grundsätzlich zulässig, sofern der Empfänger nicht erkennbar widerspricht (Sperrvermerk, frühere Ablehnung). Deshalb: Widerspruchsliste führen, vor jedem Versand abgleichen, Einträge dauerhaft einhalten.
- Nachfassen nur per Post oder nachdem der Empfänger geantwortet hat. Keine kalte E-Mail als Nachfassen (§ 7 Abs. 2 Nr. 2 UWG verlangt vorherige ausdrückliche Einwilligung), kein kalter Anruf ohne mindestens mutmaßliche Einwilligung (§ 7 Abs. 2 Nr. 1 UWG).
- Wird eine namentlich genannte natürliche Person angeschrieben: kurzer Datenschutzhinweis mit Datenquelle (Art. 14 DSGVO) und Hinweis auf das Widerspruchsrecht gegen Direktwerbung (Art. 21 Abs. 2 und 3 DSGVO).
- Befunde über ein Unternehmen nur aus öffentlich zugänglichen Quellen; die Prüfung (was, wann, womit) so festhalten, dass sie wiederholbar ist.

## Versand-Checkliste

- Linter ohne Befund, Endfassung mit `--strip` erzeugt
- Widerspruchsliste abgeglichen, Herkunft der Adresse dokumentiert
- Jede Zahl im Kanon oder mit Quellenbeschreibung belegt
- Inhaber hat Text und Rechtsfragen freigegeben
