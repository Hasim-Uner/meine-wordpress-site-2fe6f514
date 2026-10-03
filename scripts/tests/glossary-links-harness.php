<?php
/** Real glossary modules and data; only the WordPress/database boundary is fake. */
require_once __DIR__ . '/navigation-harness.php';

class WP_Post {
	public $ID;
	public $post_name;
	public $post_title;
	public $post_type = 'glossary_term';
	public $post_status = 'publish';
	public $post_content = '';
}
function add_shortcode( ...$args ) {}
function hu_normalize_brand_text( $text ) { return $text; }
function shortcode_atts( $defaults, $atts, $name = '' ) { return array_merge( $defaults, (array) $atts ); }
function wp_unique_id( $prefix = '' ) { static $id = 0; return $prefix . ++$id; }
function get_post( $post = null ) { return $post ?? ( $GLOBALS['glossary_fixture_post'] ?? null ); }
function get_post_meta( ...$args ) { return ''; }
function in_the_loop() { return $GLOBALS['glossary_fixture_loop'] ?? true; }
function is_main_query() { return $GLOBALS['glossary_fixture_main'] ?? true; }
function has_shortcode( $content, $tag ) { return strpos( $content, '[' . $tag ) !== false; }

// DOM parses attribute values independently from the production tokenizer.
// A local run may prepend WordPress's actual HTML API instead of this boundary.
if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
	class WP_HTML_Tag_Processor {
		private $element;
		public function __construct( $html ) {
			$previous = libxml_use_internal_errors( true );
			$dom = new DOMDocument();
			$dom->loadHTML( '<?xml encoding="UTF-8">' . $html, LIBXML_NONET );
			preg_match( '~^<([a-z][a-z0-9:-]*)~i', $html, $match );
			$this->element = isset( $match[1] ) ? $dom->getElementsByTagName( strtolower( $match[1] ) )->item( 0 ) : null;
			libxml_clear_errors();
			libxml_use_internal_errors( $previous );
		}
		public function next_tag() { return $this->element instanceof DOMElement; }
		public function get_attribute( $name ) {
			return $this->element && $this->element->hasAttribute( $name ) ? $this->element->getAttribute( $name ) : null;
		}
	}
}

require_once get_stylesheet_directory() . '/inc/glossary/glossary.php';
require_once get_stylesheet_directory() . '/inc/glossary/glossary-registry.php';
require_once get_stylesheet_directory() . '/inc/glossary/glossary-autolink.php';

$GLOBALS['nav_glossary_posts'] = [];
foreach ( nexus_get_glossary_registry() as $term ) {
	if ( ! nexus_glossary_term_requires_post( $term ) ) { continue; }
	$post = new WP_Post();
	$post->ID = count( $GLOBALS['nav_glossary_posts'] ) + 1;
	$post->post_name = $term['slug'];
	$post->post_title = $term['title'];
	$GLOBALS['nav_glossary_posts'][ $term['slug'] ] = $post;
}
