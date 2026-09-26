#!/usr/bin/env bash
# Print the current navigation from the repo contract, then the manual checks.
# Nothing here is hardcoded: header, footer and 404 are rendered from
# blocksy-child/inc/commercial-routing.php through the test harness.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../.." && pwd -P)"
cd "$ROOT"

php -r '
require "scripts/tests/navigation-harness.php";
nav_test_use_context( "imprint" );
$path = static function ( $url ) {
	$parts = parse_url( $url );
	return ( $parts["path"] ?? "/" ) . ( isset( $parts["query"] ) ? "?" . $parts["query"] : "" ) . ( isset( $parts["fragment"] ) ? "#" . $parts["fragment"] : "" );
};
echo "Header (hu_get_site_header_navigation_contract):\n";
foreach ( hu_get_primary_navigation_contract() as $item ) {
	printf( "- %s -> %s [%s]\n", $item["label"], $path( $item["url"] ), $item["track"] );
}
$footer = hu_get_site_footer_navigation_contract();
echo "\nFooter ways (hu_get_site_footer_navigation_contract):\n";
foreach ( $footer["picks"] as $pick ) {
	printf( "- %s -> %s [%s]\n", trim( $pick["strong"] ), $path( $pick["url"] ), $pick["track"] );
}
echo "\nFooter directory:\n";
foreach ( $footer["directory"] as $group ) {
	printf( "- %s: %s\n", $group["title"], implode( ", ", array_map( static function ( $item ) use ( $path ) {
		return $item["label"] . " " . $path( $item["url"] );
	}, $group["items"] ) ) );
}
'

cat <<'EOF'

Repo gates:
- php scripts/tests/navigation-contract.php
- npm run test:navigation-ui

Manual WordPress admin (not verifiable from the repo):
- Optional: rebuild the stored menu via /wp-admin/?nexus_rebuild_menu=1.
  It is backend state only; no template renders it.
- Purge the page cache (nginx/Varnish) so no page keeps the old header.

Post-deploy QA, uncached:
- Header on desktop and mobile: "Tracking" opens /ga4-tracking-setup/.
- Footer: "Server-Side Tracking" opens /server-side-tracking-b2b/.
- Mobile: open the menu, Escape closes it, a link closes it.
- A 404 URL lists the header routes and no Marktcheck.
EOF
