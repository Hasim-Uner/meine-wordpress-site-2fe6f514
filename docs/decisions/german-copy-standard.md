# German Copy Standard

## Rule

Visible German copy uses real UTF-8 characters.

Examples:

- `öffentlich`, not `oeffentlich`
- `für`, not `fuer`
- `Über mich`, not `Ueber mich`
- `prüfen`, not `pruefen`

## Exceptions

ASCII-only forms are acceptable for:

- slugs
- URLs
- file names
- CSS classes
- PHP/JS identifiers

Examples:

- `/hasim-uener/`
- `page-datenschutz.php`
- `nexus_get_page_url()`

## Guardrail

The workflow `.github/workflows/copy-style.yml` checks newly added visible copy for common ASCII transliterations.
It is intentionally scoped to changed lines so legacy content does not block deployment all at once.
