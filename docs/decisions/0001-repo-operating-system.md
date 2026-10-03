# 0001 Repo als Operating System aufbauen, Theme-Pfad stabil halten

Status: angenommen

Datum: 2026-03-07

## Kontext

Das Repo war bisher vor allem ein WordPress-Theme-Repo mit vereinzelten Notizen, Content-Drafts und Skill-Dateien. Fachlich existiert bereits ein zusammenhaengendes System aus Website, Audit-Funnel, Tracking-Logik und Angebotsarchitektur. Strukturell war dieses Wissen aber noch nicht als Operating System organisiert.

Gleichzeitig deployt GitHub Actions aktuell nur `blocksy-child/` live nach WordPress. Ein harter Umbau des Theme-Pfads wuerde deshalb unnoetiges Betriebsrisiko erzeugen.

## Entscheidung

- Das Repo bekommt einen Operating-System-Layer um den bestehenden Theme-Code herum.
- `blocksy-child/` bleibt vorerst an Ort und Stelle.
- Root-Dokumente definieren Status, Systemkarte und Agentenkontext.
- Betriebswissen, Referenzen und Content-Artefakte werden in klarere Bereiche ueberfuehrt.
- Prompts und Agentenwissen erhalten eigene Zielordner, auch wenn die Bestaende noch unvollstaendig sind.

## Konsequenzen

Positive Folgen:

- keine Unterbrechung des bestehenden Deploy-Pfads
- klares Einstiegssystem fuer Menschen und Agenten
- bessere Trennung zwischen Technik, Betrieb und Wissen
- vorbereitetes Zielsystem für Prompt-Bibliothek und Agenten-Kontext

Bewusste Einschraenkung:

- Die Zielstruktur ist sauberer als der bisherige Ist-Zustand, aber noch nicht vollstaendig.
- WordPress-Editor-Content bleibt vorerst ein externer Content-Layer und muss dokumentiert, nicht verdrängt werden.

## Naechste Architekturentscheidungen

- Datenmodell fuer Audit-Payload und Deep-Dive-Uebergabe
- Trennlinie zwischen Repo-Content, Editor-Content und CRM-Daten
- Refactor-Pfad fuer `page-wgos.php`
