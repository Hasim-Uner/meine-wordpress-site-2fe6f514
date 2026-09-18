#!/usr/bin/env bash
#
# Canon guard — blockiert verbotene Fakten-Literale im gesamten Repo.
#
# Anders als scripts/lint-canon-drift.sh prueft dieser Guard nicht den Diff,
# sondern den Stand des Arbeitsbaums. Ein Wert, der sich vor der Sperre
# eingeschlichen hat, faellt damit genauso auf wie ein neu hinzugefuegter.
#
# Die Sperrliste steht in scripts/canon-forbidden-values.txt und ist dort
# dokumentiert. Dieses Skript kennt kein einziges verbotenes Literal selbst.
#
# Exit 0 = sauber, Exit 1 = Fundstelle(n), Exit 2 = Konfigurationsfehler.

set -euo pipefail

root_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
rules_file="${CANON_RULES_FILE:-$root_dir/scripts/canon-forbidden-values.txt}"

if [ ! -f "$rules_file" ]; then
  echo "Canon guard: Sperrliste nicht gefunden: $rules_file" >&2
  exit 2
fi

# Der Guard und seine Sperrliste tragen die Muster naturgemaess im Klartext.
# Sie schliessen sich selbst aus — gleiche Begruendung wie beim
# Selbstausschluss in scripts/lint-canon-drift.sh.
pathspec=(
  "$root_dir"
  ":(exclude)scripts/canon-guard.sh"
  ":(exclude)scripts/canon-forbidden-values.txt"
)

rule_ids=()
rule_patterns=()
rule_contexts=()
rule_hints=()

while IFS=$'\t' read -r kind field_a field_b field_c field_d; do
  case "${kind:-}" in
    rule)
      if [ -z "${field_a:-}" ] || [ -z "${field_b:-}" ]; then
        echo "Canon guard: unvollstaendige rule-Zeile in $rules_file" >&2
        exit 2
      fi
      rule_ids+=("$field_a")
      rule_patterns+=("$field_b")
      rule_contexts+=("${field_c:--}")
      rule_hints+=("${field_d:-}")
      ;;
    ignore)
      if [ -z "${field_a:-}" ]; then
        echo "Canon guard: ignore-Zeile ohne Pfad in $rules_file" >&2
        exit 2
      fi
      pathspec+=(":(exclude)${field_a}")
      ;;
    ''|\#*)
      ;;
    *)
      echo "Canon guard: unbekannter Zeilentyp '${kind}' in $rules_file" >&2
      exit 2
      ;;
  esac
done < <(sed -e 's/[[:space:]]*$//' "$rules_file")

if [ "${#rule_ids[@]}" -eq 0 ]; then
  echo "Canon guard: keine Regel in $rules_file" >&2
  exit 2
fi

cd "$root_dir"

in_git_work_tree=0
if git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
  in_git_work_tree=1
fi

# Sucht ein Muster ueber alle versionierten und neu angelegten Dateien.
# -I laesst Binaerdateien aus, --untracked erfasst noch nicht committete
# Dateien, respektiert dabei aber .gitignore.
search() {
  local pattern="$1"

  if [ "$in_git_work_tree" -eq 1 ]; then
    git grep --no-color -n -I -E --untracked -e "$pattern" -- "${pathspec[@]}" || true
    return
  fi

  # Fallback ohne Git (etwa in einem entpackten Tarball): dieselbe Suche mit
  # grep, die Ignore-Pfade werden dabei nachtraeglich herausgefiltert.
  local excludes=()
  local spec
  for spec in "${pathspec[@]}"; do
    case "$spec" in
      ':(exclude)'*) excludes+=("${spec#:(exclude)}") ;;
    esac
  done

  local raw
  raw="$(grep -rnI -E --exclude-dir=.git -e "$pattern" . || true)"
  [ -n "$raw" ] || return

  local line path prefix keep
  while IFS= read -r line; do
    path="${line#./}"
    path="${path%%:*}"
    keep=1
    for spec in "${excludes[@]}"; do
      prefix="${spec%/\*\*}"
      if [ "$path" = "$spec" ] || { [ "$prefix" != "$spec" ] && case "$path" in "$prefix"/*) true ;; *) false ;; esac; }; then
        keep=0
        break
      fi
    done
    [ "$keep" -eq 1 ] && printf '%s\n' "${line#./}"
  done <<< "$raw"
}

violations=0
report=""

for index in "${!rule_ids[@]}"; do
  id="${rule_ids[$index]}"
  pattern="${rule_patterns[$index]}"
  context="${rule_contexts[$index]}"
  hint="${rule_hints[$index]}"

  hits="$(search "$pattern")"

  if [ -n "$hits" ] && [ "$context" != "-" ]; then
    hits="$(printf '%s\n' "$hits" | grep -E -- "$context" || true)"
  fi

  [ -n "$hits" ] || continue

  violations=$(( violations + 1 ))
  report+=$'\n'"── ${id} — ${hint}"$'\n'
  report+="$hits"$'\n'
done

if [ "$violations" -gt 0 ]; then
  echo "Canon guard failed: verbotene Werte im Repo."
  echo "Fakten (Kontakt, Antwortzeit, Preise, Kennzahlen) werden nie als Text"
  echo "geschrieben, sondern ueber den Kanon in blocksy-child/inc/canon/ gelesen."
  echo "Sperrliste: ${rules_file#"$root_dir"/}"
  printf '%s\n' "$report"
  exit 1
fi

echo "Canon guard passed: keine verbotenen Werte gefunden."
