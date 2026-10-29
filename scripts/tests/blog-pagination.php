<?php
/** Regression: paginated blog content must retain its own clean URL identity. */
require __DIR__ . '/navigation-harness.php';
require_once get_stylesheet_directory() . '/inc/seo-meta.php';
require_once get_stylesheet_directory() . '/inc/org-schema.php';

function wp_check_filetype( $file ) { return [ 'type' => 'image/jpeg' ]; }

function blog_pagination_check( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
	echo 'PASS ' . $message . "\n";
}

$wp_rewrite = new class {
	public $pagination_base = 'page';
	public function using_permalinks() { return '' !== get_option( 'permalink_structure', '' ); }
};
$wp_query = (object) [ 'posts' => [] ];

// Only WordPress request/options/permalink boundaries are fixtures. Both the
// emitted head and CollectionPage come from the real production functions.
$cases = [
	[ '/%postname%/', '/blog/', 0, 'page', '/blog/', '/blog/?utm_source=mail' ],
	[ '/%postname%/', '/blog/', 1, 'page', '/blog/', '/blog/page/1/?gclid=example' ],
	[ '/%postname%/', '/blog/', 2, 'page', '/blog/page/2/', '/blog/page/2/?utm_source=mail&gclid=example' ],
	[ '/%postname%/', '/blog/', 7, 'page', '/blog/page/7/', '/blog/page/7/?filter=example' ],
	[ '/%postname%/', '/journal/', 3, 'seite', '/journal/seite/3/', '/journal/seite/3/?utm_medium=mail' ],
	[ '/%postname%', '/blog', 2, 'page', '/blog/page/2', '/blog/page/2?utm_source=mail' ],
	[ '/index.php/%postname%/', '/sub/index.php/blog/', 2, 'page', '/sub/index.php/blog/page/2/', '/sub/index.php/blog/page/2/' ],
	[ '', '/?page_id=17', 1, 'page', '/?page_id=17', '/?page_id=17&utm_source=mail' ],
	[ '', '/?page_id=17', 2, 'page', '/?page_id=17&paged=2', '/?page_id=17&paged=2&utm_source=mail' ],
];

foreach ( $cases as [ $structure, $permalink, $paged, $pagination_base, $expected_path, $request ] ) {
	nav_test_use_context( 'imprint' );
	$GLOBALS['nav_test']['page']        = '';
	$GLOBALS['nav_test']['posts_index'] = true;
	$GLOBALS['nav_test_options']        = [ 'page_for_posts' => 17, 'permalink_structure' => $structure ];
	$GLOBALS['nav_test_permalink']      = home_url( $permalink );
	$GLOBALS['nav_test_query_vars']     = [ 'paged' => $paged ];
	$wp_rewrite->pagination_base        = $pagination_base;
	$_SERVER['REQUEST_URI']             = $request;
	$expected                          = home_url( $expected_path );

	ob_start();
	hu_seo_meta_tags();
	$html = ob_get_clean();
	$dom = new DOMDocument();
	$dom->loadHTML( '<!doctype html><html><head>' . $html . '</head><body></body></html>' );
	$xpath      = new DOMXPath( $dom );
	$canonicals = $xpath->query( '//link[@rel="canonical"]' );
	$og_urls    = $xpath->query( '//meta[@property="og:url"]' );
	$robots     = $xpath->query( '//meta[@name="robots"]' );
	$schema     = hu_get_blog_archive_collection_schema();

	blog_pagination_check( 1 === $canonicals->length && $expected === $canonicals->item( 0 )->getAttribute( 'href' ), 'One clean self canonical: ' . $request );
	blog_pagination_check( 1 === $og_urls->length && $expected === $og_urls->item( 0 )->getAttribute( 'content' ), 'Open Graph matches canonical: ' . $expected_path );
	blog_pagination_check( 1 === $robots->length && 'index, follow, max-image-preview:large' === $robots->item( 0 )->getAttribute( 'content' ), 'Blog page remains indexable: ' . $expected_path );
	blog_pagination_check( $expected === $schema['url'] && $expected . '#collection' === $schema['@id'] && $expected . '#itemlist' === $schema['mainEntity']['@id'], 'Collection and list IDs identify the same page: ' . $expected_path );
}

echo "OK blog-pagination\n";
