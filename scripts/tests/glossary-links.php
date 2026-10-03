<?php
/** Regression contract for HTML integrity, restrained linking and context gates. */
require __DIR__ . '/glossary-links-harness.php';
error_reporting( E_ALL );
set_error_handler( static function ( $severity, $message, $file, $line ) { throw new ErrorException( $message, 0, $severity, $file, $line ); } );
nav_test_use_context( 'article_cro' );
$GLOBALS['nav_test_permalink'] = home_url( '/example-article/' );
$checks = 0;
function glossary_link_check( $condition, $message ) {
	global $checks;
	$checks++;
	if ( ! $condition ) { throw new RuntimeException( $message ); }
}
function glossary_link_count( $html ) { return substr_count( $html, 'data-glossary-term=' ); }

$input = '<!-- wp:paragraph {"note":"CRM"} --><p title="CPL > CRM">Cost per Lead und <strong>CRM</strong>.</p><!-- /wp:paragraph --><p>CPL, CRM und Conversion.</p><p>Conversion ist ein Ziel.</p>';
$html = nexus_glossary_autolink( $input );
glossary_link_check( glossary_link_count( $html ) === 3, 'One per block, one per term, in reading order.' );
glossary_link_check( strpos( $html, '<strong>CRM</strong>' ) !== false, 'Keep inline emphasis and density.' );
glossary_link_check( strpos( $html, 'title="CPL > CRM"' ) !== false && strpos( $html, '<!-- wp:paragraph {"note":"CRM"} -->' ) !== false, 'Attributes and Gutenberg comments must be byte-identical.' );
glossary_link_check( nexus_glossary_autolink( $html ) === $html, 'Repeated filters must not add links or change IDs.' );
glossary_link_check( strpos( $html, '<p>CPL, <span' ) !== false, 'Aliases share a term budget; do not link CPL again.' );

foreach ( [ 'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'button', 'summary', 'form', 'label', 'code', 'pre', 'nav', 'header', 'footer', 'figure', 'blockquote', 'svg', 'math', 'template', 'dialog' ] as $tag ) {
	$protected = '<' . $tag . '><strong>CRM</strong><span>Conversion</span></' . $tag . '>';
	glossary_link_check( nexus_glossary_link_content( '<p>' . $protected . '</p>' ) === '<p>' . $protected . '</p>', 'Protected ancestor: ' . $tag );
}
foreach ( [ 'data-glossary-skip', 'hidden', 'aria-hidden="true"', 'contenteditable="true"', 'role="button"' ] as $attribute ) {
	$input = '<section ' . $attribute . '><p><strong>CRM</strong></p></section><p>Conversion</p>';
	$html = nexus_glossary_link_content( $input );
	glossary_link_check( strpos( $html, '<strong>CRM</strong>' ) !== false && glossary_link_count( $html ) === 1, 'Inherited exclusion: ' . $attribute );
}
foreach ( [ 'script', 'style', 'textarea', 'noscript' ] as $tag ) {
	$raw = '<' . $tag . '>const a = "<p>CRM</p>"; const b = "</section>";</' . $tag . '>';
	$html = nexus_glossary_link_content( $raw . '<p>Conversion</p>' );
	glossary_link_check( strpos( $html, $raw ) === 0 && glossary_link_count( $html ) === 1, 'Raw-text isolation: ' . $tag );
}
$input = '<p data-copy=\'CRM > CPL\' title="Conversion"><span>CRM</span></p>';
$html = nexus_glossary_link_content( $input );
glossary_link_check( strpos( $html, "data-copy='CRM > CPL'" ) !== false && glossary_link_count( $html ) === 1, 'Quoted > cannot split an attribute into text.' );
glossary_link_check( nexus_glossary_link_content( '<p>éCRM CRMs CRM-Konten microconversionen</p>' ) === '<p>éCRM CRMs CRM-Konten microconversionen</p>', 'Unicode word boundaries and compounds.' );
foreach ( [ '&nbsp;', '&#160;', '&#xA0;' ] as $space ) {
	$html = nexus_glossary_link_content( '<p>Consent' . $space . 'Mode</p>' );
	glossary_link_check( glossary_link_count( $html ) === 1 && strpos( $html, '>Consent' . $space . 'Mode</a>' ) !== false, 'Preserve encoded space: ' . $space );
}
foreach ( [ '<p>CRM', '<p><a>CRM</p>', '<p title="CRM>Conversion</p>', '<p>CRM</p><!-- unfinished', "<p>CRM \xFF</p>" ] as $malformed ) {
	glossary_link_check( nexus_glossary_link_content( $malformed ) === $malformed, 'Malformed fragments must remain unchanged.' );
}
$input = implode( '', array_map( static function ( $term ) { return '<p>' . $term . '</p>'; }, [ 'CRM', 'Conversion', 'INP', 'LCP', 'CLS', 'TTFB', 'WebP', 'Attribution', 'Staging', 'Lead-Scoring' ] ) );
$html = nexus_glossary_link_content( $input );
glossary_link_check( glossary_link_count( $html ) === 8, 'Hard article budget.' );
glossary_link_check( nexus_glossary_link_content( $html ) === $html, 'Budget is stable across repeated renders.' );
glossary_link_check( nexus_glossary_link_content( $input, 0 ) === $input, 'Disabled automatic budget.' );

$html = nexus_glossary_link( 'attribution', 'Attribution' );
$term = nexus_get_glossary_definition( 'attribution' );
glossary_link_check( strpos( $html, home_url( '/glossar/attribution/' ) ) !== false, 'Published definition destination.' );
glossary_link_check( strpos( $html, esc_html( $term['short_definition'] ) ) !== false, 'Definition comes from the registry.' );
glossary_link_check( strpos( $html, 'role="tooltip" hidden' ) !== false, 'Hidden AT description and normal link without JS.' );
preg_match( '~aria-describedby="([^"]+)"~', $html, $id );
glossary_link_check( strpos( $html, 'id="' . $id[1] . '"' ) !== false, 'Description reference resolves.' );
glossary_link_check( nexus_glossary_link( 'attribution' ) !== $html, 'Unique IDs across repeated explicit components.' );
glossary_link_check( strpos( nexus_glossary_link( 'attribution', '<img onerror="alert(1)">' ), '<img' ) === false, 'Explicit labels are escaped.' );
glossary_link_check( nexus_glossary_link( 'does-not-exist', '<b>Text</b>' ) === '&lt;b&gt;Text&lt;/b&gt;', 'Missing term keeps escaped wording.' );
glossary_link_check( nexus_glossary_explain_text( '<script>CRM</script>', 'crm' ) !== '<script>CRM</script>', 'Plain-text fields never become untrusted HTML.' );
glossary_link_check( strpos( nexus_glossary_term_shortcode( [ 'slug' => 'crm' ], 'CRM' ), '/glossar/crm/' ) !== false, 'Editor opt-in uses the same component.' );

$GLOBALS['nav_glossary_posts']['crm']->post_status = 'draft';
unset( $GLOBALS['nexus_glossary_post_lookup'] );
glossary_link_check( nexus_glossary_link( 'crm' ) === 'CRM' && nexus_glossary_link_content( '<p>CRM</p>' ) === '<p>CRM</p>', 'Never guess an unpublished destination.' );
$GLOBALS['nav_glossary_posts']['crm']->post_status = 'publish';
unset( $GLOBALS['nexus_glossary_post_lookup'] );
$GLOBALS['nav_test_permalink'] = home_url( '/glossar/crm/' );
glossary_link_check( nexus_glossary_link( 'crm' ) === 'CRM', 'No self-link.' );
$GLOBALS['nav_test_permalink'] = nexus_get_glossary_primary_url( 'core-web-vitals' );
glossary_link_check( nexus_glossary_link( 'core-web-vitals' ) === 'Core Web Vitals', 'No self-link through a query-owner anchor.' );
$GLOBALS['nav_test_permalink'] = home_url( '/example-article/' );

foreach ( [ 'glossary_fixture_loop', 'glossary_fixture_main' ] as $flag ) {
	$GLOBALS[ $flag ] = false;
	glossary_link_check( nexus_glossary_autolink( '<p>CRM</p>' ) === '<p>CRM</p>', 'No secondary loop: ' . $flag );
	$GLOBALS[ $flag ] = true;
}
foreach ( [ 'glossary_fixture_admin', 'glossary_fixture_feed' ] as $flag ) {
	$GLOBALS[ $flag ] = true;
	glossary_link_check( nexus_glossary_autolink( '<p>CRM</p>' ) === '<p>CRM</p>', 'No non-page output: ' . $flag );
	$GLOBALS[ $flag ] = false;
}
foreach ( [ 'contact', 'imprint', 'not_found' ] as $context ) {
	nav_test_use_context( $context );
	glossary_link_check( ! nexus_glossary_has_link_context(), 'Do not enqueue on unrelated route: ' . $context );
	glossary_link_check( nexus_glossary_autolink( '<p>CRM</p>' ) === '<p>CRM</p>', 'Templates do not get blanket automatic linking.' );
}
foreach ( [ 'home', 'tracking', 'server_side', 'case_study', 'solar_cluster', 'whitelabel', 'conversion', 'article_cro' ] as $context ) {
	nav_test_use_context( $context );
	if ( 'tracking' === $context ) { $GLOBALS['nav_test']['page'] = 'ga4-tracking-setup'; }
	glossary_link_check( nexus_glossary_has_link_context(), 'Enqueue selected route: ' . $context );
}
nav_test_use_context( 'contact' );
$GLOBALS['glossary_fixture_post'] = new WP_Post();
$GLOBALS['glossary_fixture_post']->post_content = '[hu_begriff slug="crm"]CRM[/hu_begriff]';
glossary_link_check( nexus_glossary_has_link_context(), 'Explicit editor opt-in can load assets on another page.' );
unset( $GLOBALS['glossary_fixture_post'] );

echo "PASS: {$checks} glossary link checks (HTML integrity, aliases, budgets, escaping, missing targets, context and AT wiring).\n";
