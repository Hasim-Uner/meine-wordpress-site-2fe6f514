#!/usr/bin/env python3
"""Validate route/page/template/indexing contracts without booting WordPress."""

from pathlib import Path
import re
import sys

ROOT = Path(__file__).resolve().parents[1]
THEME = ROOT / "blocksy-child"
HELPERS = THEME / "inc" / "helpers.php"
SEO_META = THEME / "inc" / "seo-meta.php"

failures = []


def fail(message):
    failures.append(message)
    print(f"FAIL: {message}")


def ok(message):
    print(f"OK: {message}")


def function_body(text, name):
    match = re.search(rf"^function\s+{re.escape(name)}\s*\([^)]*\)\s*\{{", text, re.M)
    if not match:
        fail(f"missing function {name}()")
        return ""
    start = match.end()
    depth = 1
    i = start
    while i < len(text) and depth:
        if text[i] == "{":
            depth += 1
        elif text[i] == "}":
            depth -= 1
        i += 1
    if depth:
        fail(f"unterminated function {name}()")
        return ""
    return text[start:i - 1]


def quoted_slugs(body):
    values = re.findall(r"['\"](/?[a-z0-9][a-z0-9\-/]*/?)['\"]", body)
    return [value.strip("/") for value in values if value.strip("/")]


helpers = HELPERS.read_text(encoding="utf-8")
seo = SEO_META.read_text(encoding="utf-8")

provisioned_body = function_body(helpers, "nexus_get_provisioned_pages")
entries = re.findall(
    r"'slug'\s*=>\s*'([^']+)'.*?'template'\s*=>\s*'([^']+)'",
    provisioned_body,
    re.S,
)

if not entries:
    fail("no provisioned route pages found")
else:
    ok(f"found {len(entries)} provisioned route pages")

slugs = [slug for slug, _ in entries]
templates = [template for _, template in entries]

for label, values in (("provisioned slug", slugs), ("provisioned template", templates)):
    duplicates = sorted({value for value in values if values.count(value) > 1})
    if duplicates:
        fail(f"duplicate {label}s: {', '.join(duplicates)}")
    else:
        ok(f"{label}s are unique")

for slug, template in entries:
    path = THEME / template
    if not path.is_file():
        fail(f"{slug}: missing template {template}")
        continue

    content = path.read_text(encoding="utf-8")
    canonical_slug_template = template == f"page-{slug}.php"

    # WordPress resolves page-{slug}.php automatically and does not require a
    # Template Name header. Custom filenames should keep the header so they
    # remain valid selectable page templates outside this provisioner too.
    if not canonical_slug_template and "Template Name:" not in content:
        fail(f"{slug}: custom template {template} has no Template Name header")
    else:
        mode = "slug template" if canonical_slug_template else "named template"
        ok(f"{slug}: {mode} exists")

noindex = set(quoted_slugs(function_body(seo, "hu_get_noindex_follow_slugs")))
noindex |= set(quoted_slugs(function_body(seo, "hu_get_noindex_nofollow_slugs")))
retired = set(quoted_slugs(function_body(helpers, "nexus_get_retired_gone_paths")))
legacy_redirect_body = function_body(helpers, "nexus_get_legacy_offer_redirect_map")
legacy_redirects = {
    value.strip("/")
    for value in re.findall(r"['\"](/[^'\"]+/)['\"]\s*=>", legacy_redirect_body)
    if value.strip("/")
}

for slug in slugs:
    if slug in noindex:
        fail(f"provisioned public route is also noindex: {slug}")
    if slug in retired:
        fail(f"provisioned public route is also retired/410: {slug}")
    if slug in legacy_redirects:
        fail(f"provisioned public route is also a legacy redirect source: {slug}")

if not set(slugs) & noindex:
    ok("provisioned routes are not in noindex lists")
if not set(slugs) & retired:
    ok("provisioned routes are not in retired/410 paths")
if not set(slugs) & legacy_redirects:
    ok("provisioned routes are not legacy redirect sources")

# The proof page is indexable since the owner's release decision of
# 2026-10-03 (it was held out of the index before). Guard the decision so an
# unrelated SEO cleanup cannot flip it back; the same slug list drives the
# robots meta and the sitemap exclusion.
proof_slug = "case-study-solar-leadgenerierung"
proof_comment = "Release decision 2026-10-03: the solar proof page"
if proof_slug in noindex:
    fail("solar proof is noindex again; a new release decision is required and this guard must change with it")
elif proof_comment not in seo:
    fail("solar proof release decision comment is missing in hu_get_noindex_follow_slugs()")
else:
    ok("solar proof is indexable and the release decision is documented")

required_lock_symbols = [
    "nexus_acquire_route_pages_lock",
    "nexus_release_route_pages_lock",
    "nexus_route_pages_ensure_due",
]
for symbol in required_lock_symbols:
    if f"function {symbol}" not in helpers:
        fail(f"missing provisioning lock symbol: {symbol}")
    else:
        ok(f"provisioning lock symbol present: {symbol}")

if failures:
    print(f"Route architecture guard failed with {len(failures)} issue(s).", file=sys.stderr)
    sys.exit(1)

print("Route architecture guard passed.")
