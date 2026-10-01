<?php
/** Regression coverage for CRM filtering, service roles and missing GSC days. */
define( 'ABSPATH', __DIR__ );
define( 'DAY_IN_SECONDS', 86400 );

class WP_Post {
	public $ID;
	public $post_title;
	public $post_date = '2026-09-20 12:00:00';
	public function __construct( $id, $title ) { $this->ID = $id; $this->post_title = $title; }
}

function add_action() {}
function add_filter() {}
function remove_action() {}
function absint( $value ) { return abs( (int) $value ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $value ) ); }
function sanitize_title( $value ) { return sanitize_key( $value ); }
function wp_unslash( $value ) { return $value; }
function wp_strip_all_tags( $value ) { return strip_tags( $value ); }
function wp_json_encode( $value ) { return json_encode( $value ); }
function wp_parse_args( $value, $defaults = [] ) { return array_merge( $defaults, (array) $value ); }
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function admin_url( $path = '' ) { return home_url( '/wp-admin/' . $path ); }
function trailingslashit( $value ) { return rtrim( $value, '/' ) . '/'; }
function wp_parse_url( $value, $component = -1 ) { return parse_url( $value, $component ); }
function wp_timezone() { return new DateTimeZone( 'Europe/Berlin' ); }
function wp_date( $format, $timestamp = null ) { return gmdate( $format, $timestamp ?? 1790294400 ); }
function current_time() { return 1790294400; }
function get_option( $key, $default = false ) { return $default; }
function get_page_by_path() { return null; }
function get_term_by() { return false; }
function get_term_link() { return false; }
function get_post_status() { return false; }
function url_to_postid() { return 0; }
function post_type_exists() { return true; }
function get_edit_post_link( $id ) { return admin_url( 'post.php?post=' . $id ); }
function get_the_title( $post ) { return $post->post_title; }
function get_post_meta( $id, $key, $single = true ) { return $GLOBALS['crm_meta'][ $id ][ $key ] ?? ''; }
function get_posts( $args ) {
	if ( 'nexus_review_request' !== ( $args['post_type'] ?? '' ) ) { return []; }
	$posts = $GLOBALS['crm_posts'];
	return ( $args['posts_per_page'] ?? -1 ) < 0 ? $posts : array_slice( $posts, 0, $args['posts_per_page'] );
}
function add_query_arg( $args, $url ) { return $url . '&' . http_build_query( $args ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return esc_html( $value ); }
function esc_url( $value ) { return esc_html( $value ); }
function number_format_i18n( $value, $decimals = 0 ) { return number_format( (float) $value, $decimals, ',', '.' ); }

$theme = dirname( __DIR__, 2 ) . '/blocksy-child/inc/';
require $theme . 'helpers.php';
foreach ( [ 'core', 'leads', 'insights', 'command-center', 'sync', 'ui', 'dashboard-v3' ] as $module ) {
	require $theme . 'seo-cockpit/seo-cockpit-' . $module . '.php';
}

$passed = 0;
function expect_same( $label, $expected, $actual ) {
	global $passed;
	if ( $expected !== $actual ) {
		fwrite( STDERR, 'FAIL ' . $label . ': expected ' . var_export( $expected, true ) . ', got ' . var_export( $actual, true ) . "\n" );
		exit( 1 );
	}
	$passed++;
	echo 'PASS ' . $label . "\n";
}

foreach ( [ 'TEST', 'test lead', 'TeSt-CRM', 'TESTING' ] as $title ) {
	expect_same( 'exclude raw prefix ' . $title, false, nexus_is_seo_cockpit_lead_signal( new WP_Post( 1, $title ) ) );
}
expect_same( 'keep TEST within a real title', true, nexus_is_seo_cockpit_lead_signal( new WP_Post( 1, 'Firma TEST GmbH' ) ) );
expect_same( 'reject non-post input', false, nexus_is_seo_cockpit_lead_signal( null ) );

$real_url = home_url( '/whitelabel-retainer/' );
$test_url = home_url( '/test-only/' );
$GLOBALS['crm_posts'] = [];
$GLOBALS['crm_meta'] = [];
// More recent TEST records than the queue limit must not hide real requests.
for ( $id = 1; $id <= 20; $id++ ) {
	$GLOBALS['crm_posts'][] = new WP_Post( $id, ( $id % 2 ? 'TEST' : 'test' ) . ' fixture' );
	$GLOBALS['crm_meta'][ $id ] = [ '_nexus_review_status' => 'won', '_nexus_review_entry_page_url' => $test_url ];
}
$GLOBALS['crm_posts'][] = new WP_Post( 21, 'Real request' );
$GLOBALS['crm_posts'][] = new WP_Post( 22, 'Firma TEST GmbH' );
foreach ( [ 21, 22 ] as $id ) {
	$GLOBALS['crm_meta'][ $id ] = [ '_nexus_review_status' => 'new', '_nexus_review_entry_page_url' => $real_url ];
}
$GLOBALS['crm_posts'][] = new WP_Post( 23, 'Real previous-period request' );
$GLOBALS['crm_posts'][22]->post_date = '2026-08-20 12:00:00';
$GLOBALS['crm_meta'][23] = [ '_nexus_review_status' => 'sent', '_nexus_review_entry_page_url' => $real_url ];
$ranges = [ 'current_start' => '2026-09-01', 'current_end' => '2026-09-28', 'previous_start' => '2026-08-01', 'previous_end' => '2026-08-31' ];
$leads = nexus_get_seo_cockpit_lead_snapshot_data( $ranges );
expect_same( 'current lead count excludes TEST', 2, $leads['overview']['current']['requests'] );
expect_same( 'previous lead count stays correct', 1, $leads['overview']['previous']['requests'] );
expect_same( 'lifetime lead count excludes TEST', 3, $leads['overview']['lifetime']['requests'] );
expect_same( 'TEST won records cannot create won signals', 0, $leads['overview']['lifetime']['won'] );
expect_same( 'TEST attribution is absent', false, isset( $leads['page_map'][ $test_url ] ) );
expect_same( 'real attribution stays correct', 2, $leads['page_map'][ $real_url ]['current']['requests'] );
expect_same( 'CRM database fixture remains intact', 23, count( $GLOBALS['crm_posts'] ) );

$rows = nexus_get_revenue_command_center_lead_rows( [], 2 );
expect_same( 'filter precedes queue limit', [ 'Real request', 'Firma TEST GmbH' ], array_column( $rows, 'target_label' ) );
$lanes = nexus_seo_cockpit_v3_action_lanes( [ 'rows' => $rows ] );
expect_same( 'Sofort contains only the two real leads', [ 'Real request', 'Firma TEST GmbH' ], array_column( $lanes['now'], 'target_label' ) );
ob_start();
nexus_seo_cockpit_v3_render_system_status( [ 'leads' => $leads ], [], [], [] );
$html = ob_get_clean();
expect_same( 'rendered Audit-CRM counter', true, false !== strpos( $html, '<strong>2 Leads</strong>' ) );

$insight = [ 'url' => $test_url, 'page_role' => 'page', 'type' => 'DECAY', 'metrics' => [ 'impressions' => 50, 'clicks' => 1 ], 'priority_parts' => [ 'confidence' => 10 ] ];
$score = nexus_score_revenue_command_center_insight( $insight, [ 'leads' => $leads ] );
expect_same( 'TEST-only page gives zero revenue lead component', 0, $score['components']['lead_signal'] );
$insight['url'] = $real_url;
$score = nexus_score_revenue_command_center_insight( $insight, [ 'leads' => $leads ] );
expect_same( 'real leads still contribute to revenue', true, $score['components']['lead_signal'] > 0 );

expect_same( 'Whitelabel role is Service', 'service', nexus_get_seo_cockpit_page_role( [], $real_url ) );
// Use the actual priority enrichment and URL Radar renderer, not a supplied label.
$radar_insight = nexus_enrich_seo_cockpit_insight_priority( [ 'url' => $real_url, 'type' => 'MONEY_PAGE_UNDERPERFORMING' ], [ 'leads' => $leads ] );
ob_start();
nexus_seo_cockpit_v3_render_problem_cards( [ 'problem_pages' => [ [ 'url' => $real_url, 'primary' => $radar_insight ] ] ] );
$radar_html = ob_get_clean();
expect_same( 'URL Radar renders Service', true, false !== strpos( $radar_html, '<span>Service</span>' ) );
expect_same( 'case study keeps Proof role', 'results', nexus_get_seo_cockpit_page_role( [], home_url( '/case-study-solar-leadgenerierung/' ) ) );
$action = nexus_resolve_revenue_command_center_insight_action( [ 'url' => $real_url, 'type' => 'MONEY_PAGE_UNDERPERFORMING', 'page_role' => 'service' ] );
expect_same( 'Whitelabel action matches the agency offer', true, false !== strpos( $action['next_action'], 'Agentur-Angebot' ) && false === strpos( $action['next_action'], 'Marktcheck' ) );

$series = nexus_normalize_seo_cockpit_date_series( '2026-09-26', '2026-09-28', [
	[ 'keys' => [ '2026-09-26' ], 'clicks' => 7, 'impressions' => 100, 'ctr' => 0.07, 'position' => 8 ],
	[ 'keys' => [ '2026-09-27' ], 'clicks' => 5, 'impressions' => 50, 'ctr' => 0.1, 'position' => 6 ],
] );
expect_same( 'normalizer identifies trailing missing day', false, $series[2]['has_data'] );
function trend_value( $series, $metric ) {
	ob_start();
	nexus_render_seo_cockpit_trend_card( $series, $metric, $metric );
	$html = ob_get_clean();
	$dom = new DOMDocument();
	@$dom->loadHTML( '<?xml encoding="UTF-8">' . $html );
	$x = new DOMXPath( $dom );
	return [ $x->evaluate( 'string(//strong)' ), $x->evaluate( 'string(//strong/@title)' ) ];
}
foreach ( [ 'clicks' => '5', 'impressions' => '50', 'ctr' => '10,0%', 'position' => '6,0' ] as $metric => $value ) {
	expect_same( 'Pulse ' . $metric . ' skips missing tail', [ $value, 'Letzter Datentag: 2026-09-27' ], trend_value( $series, $metric ) );
}
$series[2]['has_data'] = true;
expect_same( 'a genuine latest zero stays zero', [ '0', 'Letzter Datentag: 2026-09-28' ], trend_value( $series, 'clicks' ) );
expect_same( 'no data is not a measured zero', [ '—', '' ], trend_value( [], 'clicks' ) );
expect_same( 'all padded days show no data', [ '—', '' ], trend_value( nexus_normalize_seo_cockpit_date_series( '2026-09-26', '2026-09-28', [] ), 'position' ) );
$old_key = nexus_get_seo_cockpit_cache_key( 'snapshot', [ nexus_get_seo_cockpit_property(), 28 ] );
expect_same( 'old persisted evaluation cache is invalidated', true, $old_key !== nexus_get_seo_cockpit_snapshot_cache_key( 28 ) );
echo "OK action hub: $passed checks\n";
