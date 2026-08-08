#!/usr/bin/env bash
#
# Kundensprache aus den eigenen Anfragen holen.
#
# docs/standards/VOICE_OF_CUSTOMER.md steht auf UNGEFUELLT, obwohl das
# Rohmaterial laengst existiert: blocksy-child/inc/crm.php legt zu jeder
# Anfrage die Freitextfelder als Post-Meta ab — _nexus_contact_message und
# _nexus_contact_tracking_setup enthalten die Worte des Interessenten selbst.
#
# Das Skript liest sie und schreibt sie im VoC-Format vor. Es entscheidet
# nichts: Auswahl, Kuerzung und Anonymisierung bleiben Handarbeit, weil beides
# Urteil braucht und ein geglaettetes Zitat laut VOICE_OF_CUSTOMER.md nichts
# wert ist.
#
# Laeuft auf dem Server, wo WordPress und WP-CLI liegen — nicht hier im Repo.
#
#   bash scripts/harvest-voice-of-customer.sh            letzte 50 Anfragen
#   bash scripts/harvest-voice-of-customer.sh 200        mehr
#
# Die Ausgabe geht nach .ai/memory/ und ist per .gitignore ausgeschlossen.
# Kundendaten gehoeren nicht ins Repo — in VOICE_OF_CUSTOMER.md kommen nur
# die von Hand anonymisierten Zitate.

set -euo pipefail

ROOT="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"
cd "$ROOT"

LIMIT="${1:-50}"
if ! [[ "$LIMIT" =~ ^[0-9]+$ ]]; then
  echo "FEHLER: Anzahl erwartet, z. B. 'harvest-voice-of-customer.sh 200'." >&2
  exit 2
fi

if ! command -v wp >/dev/null 2>&1; then
  cat >&2 <<'EOF'
FEHLER: WP-CLI nicht gefunden.

Dieses Skript liest die WordPress-Datenbank und laeuft deshalb auf dem
Server, nicht im Repo-Checkout. Dort:

  cd /pfad/zur/wordpress-installation
  bash /pfad/zum/repo/scripts/harvest-voice-of-customer.sh

Ohne WP-CLI geht es auch von Hand: In der Datenbank stehen die Freitexte
unter den Meta-Keys _nexus_contact_message und _nexus_contact_tracking_setup
am Post-Type der CRM-Kontakte (siehe blocksy-child/inc/crm.php).
EOF
  exit 2
fi

OUT_DIR=".ai/memory"
mkdir -p "${OUT_DIR}"
OUT="${OUT_DIR}/voc-rohmaterial-$(date +%Y-%m-%d).md"

{
  printf '# Rohmaterial Kundensprache — %s\n\n' "$(date +%Y-%m-%d)"
  printf 'Ungefiltert und nicht anonymisiert. Diese Datei bleibt gitignored.\n'
  printf 'Was in docs/standards/VOICE_OF_CUSTOMER.md soll, wird von Hand\n'
  printf 'ausgewaehlt und anonymisiert — Gewerk und Groessenordnung statt Name.\n\n'
  printf -- '---\n\n'
} > "${OUT}"

# --format=json, damit Zeilenumbrueche in den Freitexten nicht die Tabelle
# zerreissen. jq formatiert daraus die VoC-Bloecke.
wp post list \
  --post_type=nexus_contact \
  --posts_per_page="${LIMIT}" \
  --orderby=date --order=DESC \
  --fields=ID,post_date \
  --format=json 2>/dev/null \
| jq -r '.[] | "\(.ID)\t\(.post_date)"' \
| while IFS=$'\t' read -r id created; do
    msg="$(wp post meta get "${id}" _nexus_contact_message 2>/dev/null || true)"
    trk="$(wp post meta get "${id}" _nexus_contact_tracking_setup 2>/dev/null || true)"
    company="$(wp post meta get "${id}" _nexus_contact_company 2>/dev/null || true)"
    [[ -z "${msg}" && -z "${trk}" ]] && continue
    {
      printf '## Anfrage %s — %s\n\n' "${id}" "${created%% *}"
      [[ -n "${company}" ]] && printf 'Absender (vor dem Uebertragen entfernen): %s\n\n' "${company}"
      if [[ -n "${msg}" ]]; then
        printf '> %s\n\n' "${msg//$'\n'/$'\n'> }"
        printf -- '— [Gewerk], [Groessenordnung], Kontaktformular, %s\n\n' "${created%% *}"
      fi
      [[ -n "${trk}" ]] && printf 'Zur Datenlage: %s\n\n' "${trk}"
      printf -- '---\n\n'
    } >> "${OUT}"
  done

COUNT="$({ rg -c '^## Anfrage ' "${OUT}" 2>/dev/null || true; } | rg '^[0-9]+$' || echo 0)"

printf '\n%s Anfragen mit Freitext in %s\n' "${COUNT}" "${OUT}"
cat <<'EOF'

Naechster Schritt: durchgehen und die Saetze auswaehlen, die ein Problem in
Kundenworten benennen — auch die unbequemen. Dann anonymisiert nach
docs/standards/VOICE_OF_CUSTOMER.md uebertragen, mit Gewerk, Groessenordnung,
Kanal und Datum.

Drei echte Saetze schlagen jede weitere Umformulierung durch einen Agenten.
EOF
