<?php
/**
 * Static checks for the two signal layers AI search systems read first:
 * the crawler policy in robots.txt and the identity claims in the schema graph.
 *
 * Both are built in PHP inside the theme, so a broken edit is only visible after
 * a deploy. This harness stubs the handful of WordPress functions the two
 * modules touch, renders them, and asserts the invariants that actually matter.
 * It is not a WordPress test suite and does not try to be one.
 *
 * Usage: php scripts/lint-entity-crawler-signals.php
 * Exit 0 = all invariants hold, 1 = at least one finding.
 */

define( 'ABSPATH', __DIR__ );

$GLOBALS['hu_lint_failures'] = 0;

/**
 * @param bool   $condition Assertion result.
 * @param string $message   What was asserted.
 * @return void
 */
function hu_lint_assert( $condition, $message ) {
	if ( $condition ) {
		echo "OK:   {$message}\n";
		return;
	}

	echo "FAIL: {$message}\n";
	$GLOBALS['hu_lint_failures']++;
}

// --- Minimal WordPress surface -------------------------------------------

function home_url( $path = '/' ) {
	$path = (string) $path;

	if ( '' === $path ) {
		return 'https://hasimuener.de';
	}

	return 'https://hasimuener.de' . ( '/' === $path[0] ? $path : '/' . $path );
}

function content_url( $path = '' ) {
	return 'https://hasimuener.de/wp-content' . (string) $path;
}

function trailingslashit( $string ) {
	return rtrim( (string) $string, '/\\' ) . '/';
}

function add_action() {}
function add_filter() {}
function remove_action() {}
function get_option( $option ) {
	return 'blog_charset' === $option ? 'UTF-8' : '';
}
// hu_get_profile_image_url() comes from helpers.php below; do not stub it.
function hu_get_brand_logo_url() {
	return 'https://hasimuener.de/wp-content/uploads/logo.webp';
}
function hu_get_contact_email() {
	return 'kontakt@hasimuener.de';
}
function untrailingslashit( $string ) {
	return rtrim( (string) $string, '/' );
}
function wp_parse_url( $url, $component = -1 ) {
	return -1 === $component ? parse_url( $url ) : parse_url( $url, $component );
}
// Returning nothing from the page lookups makes every URL resolver fall back to
// its hardcoded default. Those defaults are what the live site serves today —
// verified by diffing this render against https://hasimuener.de/llms.txt — so
// the offline render stays faithful without a database.
function get_page_by_path() {
	return null;
}
function get_posts() {
	return [];
}
function get_permalink() {
	return false;
}
function get_post_status() {
	return false;
}
function get_term_by() {
	return false;
}
function get_term_link() {
	return false;
}
function is_wp_error( $thing ) {
	return false;
}
function sanitize_title( $title ) {
	$title = strtolower( (string) $title );
	$title = preg_replace( '/[^a-z0-9]+/', '-', $title );

	return trim( (string) $title, '-' );
}
function add_query_arg( $args, $url ) {
	$query = urldecode( http_build_query( (array) $args ) );

	return $url . ( false === strpos( $url, '?' ) ? '?' : '&' ) . $query;
}

require_once __DIR__ . '/../blocksy-child/inc/robots-txt.php';
require_once __DIR__ . '/../blocksy-child/inc/org-schema.php';
require_once __DIR__ . '/../blocksy-child/inc/helpers.php';
require_once __DIR__ . '/../blocksy-child/inc/llms-txt.php';
require_once __DIR__ . '/../blocksy-child/inc/schema-positioning.php';

// --- robots.txt -----------------------------------------------------------

echo "\n########## robots.txt ##########\n\n";

$robots = nexus_get_robots_txt_content();
$groups = nexus_get_robots_txt_agent_groups();

// The vendors document one agent per purpose. Losing a search agent is the
// expensive mistake: it removes the site from that assistant's citable index.
$required_agents = [
	'OAI-SearchBot',
	'Claude-SearchBot',
	'PerplexityBot',
	'ChatGPT-User',
	'Claude-User',
	'Perplexity-User',
	'GPTBot',
	'ClaudeBot',
	'*',
];

foreach ( $required_agents as $agent ) {
	hu_lint_assert(
		in_array( $agent, nexus_get_robots_txt_user_agents(), true ),
		"robots.txt addresses User-agent: {$agent}"
	);
}

hu_lint_assert(
	1 === preg_match( '/^Sitemap: https:\/\/hasimuener\.de\/wp-sitemap\.xml$/m', $robots ),
	'robots.txt ends with an absolute Sitemap directive'
);

hu_lint_assert(
	false === strpos( $robots, "\nDisallow: /\n" ),
	'robots.txt contains no blanket Disallow: /'
);

// A named group makes that agent ignore the `*` group, so an agent that is
// named but left without rules ends up with no directives at all.
$blocks = preg_split( '/\n\s*\n/', trim( $robots ) );
$agents_in_blocks = 0;

foreach ( (array) $blocks as $block ) {
	if ( false === strpos( $block, 'User-agent:' ) ) {
		continue;
	}

	$agents_in_blocks += preg_match_all( '/^User-agent: /m', $block );

	hu_lint_assert(
		false !== strpos( $block, 'Allow: /' )
			&& false !== strpos( $block, 'Disallow: /wp-admin/' )
			&& false !== strpos( $block, 'Allow: /wp-admin/admin-ajax.php' ),
		'robots.txt group carries the full rule set: ' . trim( explode( "\n", ltrim( $block, "# \n" ) )[0] )
	);
}

hu_lint_assert(
	$agents_in_blocks === count( nexus_get_robots_txt_user_agents() ),
	'every declared user agent appears in a rendered group'
);

// --- llms.txt -------------------------------------------------------------

echo "\n########## llms.txt ##########\n\n";

$llms_rendered = nexus_get_llms_txt_content();
$llms_snapshot = (string) file_get_contents( __DIR__ . '/../llms.txt' );

// AGENTS.md points every repo agent at the root llms.txt as the canonical route
// index, but the file the site actually serves is generated by llms-txt.php.
// A hand-edited snapshot silently drifts — it published a stale intake focus
// (followup_scope) while the live route used implementation_scope.
// Regenerate with: php scripts/lint-entity-crawler-signals.php --write-llms
if ( in_array( '--write-llms', (array) ( $argv ?? [] ), true ) ) {
	file_put_contents( __DIR__ . '/../llms.txt', $llms_rendered );
	echo "llms.txt neu geschrieben.\n";
	$llms_snapshot = $llms_rendered;
}

hu_lint_assert(
	$llms_snapshot === $llms_rendered,
	'root llms.txt matches the rendered route index (regenerate with --write-llms)'
);

preg_match_all( '/^- \[[^\]]+\]\(([^)]+)\)/m', $llms_rendered, $link_matches );
$llms_paths = $link_matches[1];

hu_lint_assert(
	count( $llms_paths ) > 0,
	'llms.txt lists routes at all'
);

$retired = nexus_get_retired_gone_paths();

foreach ( $llms_paths as $path ) {
	hu_lint_assert(
		'/' === substr( $path, 0, 1 ),
		"llms.txt route is root-relative: {$path}"
	);

	// Strip query and fragment before comparing: /kontakt/?type=... is the
	// /kontakt/ route, and #marktcheck is a section of its page.
	$bare = strtok( strtok( $path, '?' ), '#' );
	$bare = '/' === $bare ? '/' : rtrim( $bare, '/' ) . '/';

	hu_lint_assert(
		! in_array( $bare, $retired, true ),
		"llms.txt route is not retired (410): {$bare}"
	);
}

hu_lint_assert(
	count( $llms_paths ) === count( array_unique( $llms_paths ) )
		|| count( $llms_paths ) - count( array_unique( $llms_paths ) ) <= 2,
	'llms.txt repeats at most the two deliberate cross-section entries'
);

// --- Redirects ------------------------------------------------------------

echo "\n########## Redirect-Matrix ##########\n\n";

$redirect_map = nexus_get_legacy_offer_redirect_map();
$retired      = nexus_get_retired_gone_paths();

/**
 * Reduce an absolute or relative URL to its comparable path.
 *
 * @param string $url URL or path.
 * @return string
 */
$to_path = static function ( $url ) {
	$path = (string) wp_parse_url( (string) $url, PHP_URL_PATH );

	return '' === $path || '/' === $path ? '/' : rtrim( $path, '/' ) . '/';
};

$source_paths = array_map( $to_path, array_keys( $redirect_map ) );

foreach ( $redirect_map as $source => $target ) {
	$target_path = $to_path( $target );

	// A target that is itself a source produces a 301 chain. Every hop costs
	// crawl budget and dilutes the signal the consolidation was meant to bundle.
	hu_lint_assert(
		! in_array( $target_path, $source_paths, true ),
		"redirect target is not itself redirected: {$source} -> {$target_path}"
	);

	// A target that is retired answers 410, so the redirect would land on a
	// dead end instead of a successor.
	hu_lint_assert(
		! in_array( $target_path, $retired, true ),
		"redirect target is not a retired (410) path: {$source} -> {$target_path}"
	);

	hu_lint_assert(
		$to_path( $source ) !== $target_path,
		"redirect does not loop onto itself: {$source}"
	);
}

// A path cannot both redirect and answer 410; whichever hook runs first wins
// and the other rule becomes a lie in the source.
$conflicting = array_intersect( $source_paths, $retired );

hu_lint_assert(
	[] === $conflicting,
	'no path is both redirected and retired (conflict: ' . implode( ', ', $conflicting ) . ')'
);

// The route index must not advertise a path that redirects.
foreach ( $llms_paths as $path ) {
	$bare = strtok( strtok( $path, '?' ), '#' );
	$bare = '/' === $bare ? '/' : rtrim( $bare, '/' ) . '/';

	hu_lint_assert(
		! in_array( $bare, $source_paths, true ),
		"llms.txt route is not a redirect source: {$bare}"
	);
}

// --- Entity graph ---------------------------------------------------------

echo "\n########## Person / Organization ##########\n\n";

$person_same_as   = hu_person_same_as_urls();
$business_same_as = hu_business_same_as_urls();
$person           = hu_get_person_node();

hu_lint_assert(
	'https://hasimuener.de/hasim-uener/#person' === hu_person_schema_id(),
	'Person node has one canonical @id'
);

hu_lint_assert(
	'Haşim Üner' === $person['name'],
	'Person.name uses the canonical Turkish spelling'
);

hu_lint_assert(
	! in_array( $person['name'], (array) $person['alternateName'], true ),
	'Person.alternateName does not repeat the canonical name'
);

// The core entity-resolution invariant. Person and Organization are two nodes
// with two @ids; an identical sameAs set claims they are one entity and makes
// worksFor/founder between them meaningless. Compare normalized: a trailing
// slash does not make two profile URLs different entities.
$normalize = static function ( array $urls ) {
	return array_map(
		static function ( $url ) {
			return rtrim( strtolower( (string) $url ), '/' );
		},
		$urls
	);
};

$overlap = array_intersect( $normalize( $person_same_as ), $normalize( $business_same_as ) );

hu_lint_assert(
	[] === $overlap,
	'Person.sameAs and Organization.sameAs share no URL (overlap: ' . implode( ', ', $overlap ) . ')'
);

// Verified 2026-09: github.com/Hasim-hannover is the personal user account
// (the ORCID record lists it as a researcher URL), github.com/Hasim-Uner is the
// organization account that owns the production repositories.
hu_lint_assert(
	in_array( 'https://github.com/Hasim-hannover', $person_same_as, true ),
	'Person.sameAs carries the personal GitHub account'
);

hu_lint_assert(
	! in_array( 'https://github.com/Hasim-Uner', $person_same_as, true )
		&& ! in_array( 'https://github.com/Hasim-Uner/', $person_same_as, true ),
	'Person.sameAs does not claim the GitHub organization'
);

hu_lint_assert(
	in_array( 'https://github.com/Hasim-Uner', $business_same_as, true ),
	'Organization.sameAs carries the GitHub organization'
);

// ORCID identifies a researcher, never an organization.
hu_lint_assert(
	[] === array_filter( $business_same_as, static function ( $url ) {
		return false !== strpos( $url, 'orcid.org' );
	} ),
	'Organization.sameAs carries no ORCID identifier'
);

foreach ( array_merge( $person_same_as, $business_same_as ) as $url ) {
	hu_lint_assert(
		1 === preg_match( '#^https://#', $url ) && false !== filter_var( $url, FILTER_VALIDATE_URL ),
		"sameAs entry is an absolute https URL: {$url}"
	);
}

// The strongest guard against the bug class this file exists for: the
// positioning layer rewrites the identity nodes at render time, so a wrong
// value in the graph builder stays invisible in the HTML while every direct
// caller of hu_get_person_node() still gets it. If builder and normalizer
// agree, normalizing is a no-op. Any future divergence — jobTitle,
// description, knowsAbout, anything — fails here instead of hiding.
$normalized_person = hu_normalize_positioned_schema_node( $person );
$person_drift      = [];

foreach ( $normalized_person as $field => $value ) {
	if ( ! array_key_exists( $field, $person ) || $person[ $field ] !== $value ) {
		$person_drift[] = $field;
	}
}

hu_lint_assert(
	[] === $person_drift,
	'positioning layer is a no-op on the Person node (drifting: ' . implode( ', ', $person_drift ) . ')'
);

hu_lint_assert(
	! in_array( 'Medienwissenschaft', (array) $person['knowsAbout'], true ),
	'Person.knowsAbout carries no publicist topic — sameAs links the spheres, knowsAbout states the expertise'
);

// Schema and visible content must tell the same story. The person hub surfaces
// a subset of the profiles as rel="me" links; every URL it links must be a
// profile the Person node actually claims, or the page corroborates something
// the graph does not say (and vice versa: a sameAs nobody can see is asserted,
// not verifiable).
$about_template = (string) file_get_contents( __DIR__ . '/../blocksy-child/page-hasim-uener.php' );

foreach ( [ 'https://www.linkedin.com/in/hasim-uener/', 'https://github.com/Hasim-hannover', 'https://hasimuener.org/' ] as $visible_profile ) {
	hu_lint_assert(
		false !== strpos( $about_template, $visible_profile ),
		"person hub links the profile visibly: {$visible_profile}"
	);

	hu_lint_assert(
		in_array( $visible_profile, $person_same_as, true ),
		"Person.sameAs claims the profile the person hub links: {$visible_profile}"
	);
}

// Hard bans from docs/standards/BRAND_AND_COPY.md must not survive in the graph
// builder, not even where the positioning layer would overwrite them at render
// time. A masked claim is still a claim any direct caller can emit.
$banned = [ 'Architekt für eigene Anfragesysteme', 'Growth Architect', 'WordPress Growth Operating System' ];
$graph  = wp_json_encode_stub( [ $person, hu_brand_map_url(), $business_same_as ] );

foreach ( $banned as $term ) {
	hu_lint_assert(
		false === mb_strpos( $graph, $term ),
		"retired claim absent from the identity nodes: {$term}"
	);
}

/**
 * @param mixed $value Value to serialize.
 * @return string
 */
function wp_json_encode_stub( $value ) {
	return (string) json_encode( $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
}

echo "\n";

if ( $GLOBALS['hu_lint_failures'] > 0 ) {
	echo "{$GLOBALS['hu_lint_failures']} Befund(e).\n";
	exit( 1 );
}

echo "Alle Signal-Invarianten erfuellt.\n";
exit( 0 );
