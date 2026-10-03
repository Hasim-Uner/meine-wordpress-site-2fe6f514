<?php
/**
 * Contextual glossary links: explicit in templates, restrained in article copy.
 * The registry owns definitions and destinations; content is never saved back.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'the_content', 'nexus_glossary_autolink', 80 );
add_shortcode( 'hu_begriff', 'nexus_glossary_term_shortcode' );

/** @return array<string, string> Exact phrase => existing query-owner URL. */
function nexus_get_solar_pillar_autolink_mappings() {
	return apply_filters( 'nexus_solar_pillar_autolink_mappings', [] );
}

/** @return array<string, array<string, string>> Exact phrase => alias config. */
function nexus_get_contextual_glossary_aliases() {
	return apply_filters( 'nexus_contextual_glossary_aliases', [] );
}

/** @param string $url Candidate. @return bool Same page, including hash aliases. */
function nexus_glossary_is_current_url( $url ) {
	$current = (string) get_permalink();
	return '' !== $current
		&& wp_parse_url( $url, PHP_URL_HOST ) === wp_parse_url( $current, PHP_URL_HOST )
		&& trailingslashit( (string) wp_parse_url( $url, PHP_URL_PATH ) ) === trailingslashit( (string) wp_parse_url( $current, PHP_URL_PATH ) );
}

/**
 * Render a deliberately selected term. Missing/draft/self targets stay text.
 * Use in explanatory copy, never inside another link, heading or CTA.
 *
 * @param string $slug Registry identifier.
 * @param string $label Optional visible wording, escaped here.
 * @return string Safe inline HTML.
 */
function nexus_glossary_link( $slug, $label = '' ) {
	$term  = nexus_get_glossary_definition( $slug );
	$label = '' !== $label ? $label : (string) ( $term['title'] ?? $slug );
	$text  = esc_html( $label );
	if ( ! is_array( $term ) || 'publish' !== $term['status'] ) {
		return $text;
	}
	$url = nexus_get_glossary_term_detail_url( $term );
	$tip = trim( wp_strip_all_tags( (string) $term['short_definition'] ) );
	if ( '' === $url || '' === $tip || nexus_glossary_is_current_url( $url ) ) {
		return $text;
	}
	return nexus_glossary_link_markup( $text, [
		'url' => $url, 'tooltip' => $tip, 'class' => 'glossary-autolink', 'linked_key' => 'glossary:' . $term['slug'],
	] );
}

/**
 * Explain one chosen phrase in a plain-text data field without changing copy.
 *
 * @param string $text Plain text, escaped here.
 * @param string $slug Registry identifier.
 * @param string $phrase Optional exact visible phrase, e.g. Testumgebung.
 * @return string Safe inline HTML.
 */
function nexus_glossary_explain_text( $text, $slug, $phrase = '' ) {
	$term   = nexus_get_glossary_definition( $slug );
	$phrase = '' !== $phrase ? $phrase : (string) ( $term['title'] ?? $slug );
	$pattern = '~(?<![\p{L}\p{M}\p{N}_\-])' . preg_quote( $phrase, '~' ) . '(?![\p{L}\p{M}\p{N}_\-])~iu';
	if ( '' === $phrase || ! preg_match( $pattern, $text, $match, PREG_OFFSET_CAPTURE ) ) {
		return esc_html( $text );
	}
	$label  = $match[0][0];
	$offset = $match[0][1];
	return esc_html( substr( $text, 0, $offset ) ) . nexus_glossary_link( $slug, $label ) . esc_html( substr( $text, $offset + strlen( $label ) ) );
}

/**
 * Only informative routes with deliberate template links, articles, or an
 * editor opt-in load the small preview assets. No global dictionary payload.
 *
 * @return bool
 */
function nexus_glossary_has_link_context() {
	$pages = [
		'wordpress-website-erstellen-lassen', 'landingpage-erstellen-lassen', 'whitelabel-retainer',
		'conversion-optimierung', 'ga4-tracking-setup', 'server-side-tracking-b2b', 'wordpress-agentur-hannover',
		'performance-marketing', 'technisches-seo-performance-fundament', 'case-study-solar-leadgenerierung', 'e3-new-energy',
		'solar-waermepumpen-leadgenerierung', 'b2b-solar-leads', 'waermepumpen-leads', 'qualifizierte-pv-anfragen',
		'lead-funnel-solar', 'cost-per-lead-photovoltaik', 'kunden-gewinnen-solarteure', 'solar-leads-kosten-studie',
		'eigene-leadgenerierung-vs-portale', 'solar-leads-kaufen-alternative',
	];
	$templates = array_merge( [ 'page-ga4.php', 'page-performance.php', 'page-wordpress-agentur.php', 'page-case-study-solar.php', 'page-seo-cornerstone.php' ], array_map( static function ( $slug ) { return 'page-' . $slug . '.php'; }, $pages ) );
	if ( is_front_page() || is_singular( 'post' ) || is_page( $pages ) || is_page_template( $templates ) ) {
		return true;
	}
	$post = get_post();
	return is_singular( 'page' ) && $post instanceof WP_Post && has_shortcode( $post->post_content, 'hu_begriff' );
}

/**
 * One markup owner for template, shortcode and automatic links.
 *
 * @param string $label_html Escaped label, or unchanged HTML text token.
 * @param array<string, string> $config Destination, definition and stable key.
 * @return string Safe inline HTML; AT can read the definition without JS.
 */
function nexus_glossary_link_markup( $label_html, $config ) {
	$tip = trim( (string) ( $config['tooltip'] ?? '' ) );
	$id  = wp_unique_id( 'glossary-tip-' );
	return sprintf(
		'<span class="glossary-autolink-wrap" data-glossary-term="%1$s"><a href="%2$s" class="%3$s"%4$s>%5$s</a>%6$s</span>',
		esc_attr( $config['linked_key'] ), esc_url( $config['url'] ), esc_attr( $config['class'] ),
		'' !== $tip ? ' aria-describedby="' . esc_attr( $id ) . '"' : '', $label_html,
		'' !== $tip ? '<span id="' . esc_attr( $id ) . '" class="glossary-autolink__popover" role="tooltip" hidden>' . esc_html( $tip ) . '</span>' : ''
	);
}

/**
 * Editor opt-in: [hu_begriff slug="attribution"]Attribution[/hu_begriff].
 *
 * @param array<string, string>|string $atts Shortcode attributes.
 * @param string|null $content Plain-text label; no nested shortcodes.
 * @return string
 */
function nexus_glossary_term_shortcode( $atts, $content = null ) {
	$atts = shortcode_atts( [ 'slug' => '' ], $atts, 'hu_begriff' );
	return nexus_glossary_link( (string) $atts['slug'], trim( wp_strip_all_tags( (string) $content ) ) );
}

/**
 * Published registry phrases only. Search synonyms do not become autolinks.
 * Existing alias filters retain their query-owner routing.
 *
 * @return array<string, array<string, string>> Case-folded phrase => config.
 */
function nexus_get_glossary_link_candidates() {
	$terms = [];
	foreach ( nexus_get_glossary_registry() as $term ) {
		if ( 'publish' !== $term['status'] || ! $term['show_in_hub'] ) {
			continue;
		}
		$url = nexus_get_glossary_term_detail_url( $term );
		$tip = trim( wp_strip_all_tags( (string) $term['short_definition'] ) );
		if ( '' === $url || '' === $tip || nexus_glossary_is_current_url( $url ) ) {
			continue;
		}
		foreach ( array_merge( [ $term['title'] ], $term['keywords_match'] ) as $phrase ) {
			$key = mb_strtolower( trim( (string) $phrase ) );
			if ( mb_strlen( $key ) >= 3 && ! isset( $terms[ $key ] ) ) {
				$terms[ $key ] = [ 'url' => $url, 'tooltip' => $tip, 'class' => 'glossary-autolink', 'linked_key' => 'glossary:' . $term['slug'] ];
			}
		}
	}
	foreach ( nexus_get_contextual_glossary_aliases() as $phrase => $config ) {
		$key = mb_strtolower( trim( (string) $phrase ) );
		if ( mb_strlen( $key ) < 3 || isset( $terms[ $key ] ) || empty( $config['url'] ) || empty( $config['tooltip'] ) || nexus_glossary_is_current_url( $config['url'] ) ) {
			continue;
		}
		$terms[ $key ] = [
			'url' => $config['url'], 'tooltip' => wp_strip_all_tags( $config['tooltip'] ),
			'class' => $config['class'] ?? 'glossary-autolink glossary-autolink--alias',
			'linked_key' => $config['linked_key'] ?? 'glossary_alias:' . sanitize_title( $phrase ),
		];
	}
	foreach ( nexus_get_solar_pillar_autolink_mappings() as $phrase => $url ) {
		$key = mb_strtolower( trim( (string) $phrase ) );
		if ( mb_strlen( $key ) >= 3 && ! isset( $terms[ $key ] ) && '' !== $url && ! nexus_glossary_is_current_url( $url ) ) {
			$terms[ $key ] = [ 'url' => $url, 'class' => 'glossary-autolink glossary-autolink--solar', 'linked_key' => 'solar_pillar:' . sanitize_title( $phrase ) ];
		}
	}
	uksort( $terms, static function ( $a, $b ) { return mb_strlen( $b ) - mb_strlen( $a ); } );
	return $terms;
}

/**
 * Article enhancement only. Templates and other editor surfaces opt in.
 *
 * @param string $content Rendered article HTML.
 * @return string
 */
function nexus_glossary_autolink( $content ) {
	if ( is_admin() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	return nexus_glossary_link_content( $content );
}

/**
 * Transform text tokens only, preserving original HTML and an exclusion stack.
 * Quote-aware tokens preserve attributes, comments, entities and block markup.
 * Raw-text elements ignore apparent tags until their own close. Unbalanced or
 * ambiguous fragments fail closed. No DOM serialization or database mutation.
 *
 * @param string $content Rendered fragment.
 * @param int $max_links Budget: one link per term and prose block, eight total.
 * @return string
 */
function nexus_glossary_link_content( $content, $max_links = 8 ) {
	$terms = nexus_get_glossary_link_candidates();
	if ( empty( $terms ) || $max_links < 1 ) {
		return $content;
	}
	$phrases = array_map( static function ( $phrase ) {
		return str_replace( ' ', '(?:\s|&nbsp;|&#0*160;|&#x0*a0;)+', preg_quote( $phrase, '~' ) );
	}, array_keys( $terms ) );
	$pattern = '~(?<![\p{L}\p{M}\p{N}_&;\-])(?:' . implode( '|', $phrases ) . ')(?![\p{L}\p{M}\p{N}_;\-])~iu';
	$tokens  = preg_split( '~(<!--[\s\S]*?(?:-->|$)|<!\[CDATA\[[\s\S]*?(?:\]\]>|$)|<(?:(?:"[^"]*"|\x27[^\x27]*\x27)|[^\x27">])*>)~', $content, -1, PREG_SPLIT_DELIM_CAPTURE );
	if ( false === $tokens ) {
		return $content;
	}
	$protected = [ 'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'button', 'summary', 'form', 'label', 'select', 'code', 'pre', 'kbd', 'samp', 'nav', 'header', 'footer', 'figure', 'blockquote', 'svg', 'math', 'template', 'dialog' ];
	$raw_tags  = [ 'script', 'style', 'textarea', 'title', 'xmp', 'iframe', 'noembed', 'noframes', 'noscript', 'plaintext' ];
	$void_tags = [ 'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr' ];
	$stack     = [];
	$linked    = [];
	$blocks    = [];
	$block_id  = 0;
	// Reserve authored/generated links before matching: repeated filter runs are
	// idempotent and cannot silently add another eight explanations.
	foreach ( $tokens as $existing ) {
		if ( preg_match( '~^<span\b~i', $existing ) ) {
			$processor = new WP_HTML_Tag_Processor( $existing );
			if ( $processor->next_tag() ) {
				$key = $processor->get_attribute( 'data-glossary-term' );
				if ( is_string( $key ) ) {
					$linked[ $key ] = true;
				}
			}
		}
	}
	$count = count( $linked );
	foreach ( $tokens as &$token ) {
		$parent = ! empty( $stack ) ? $stack[ count( $stack ) - 1 ] : [ 'blocked' => false, 'block' => 0, 'raw' => '' ];
		if ( '' !== $parent['raw'] && ! preg_match( '~^</' . $parent['raw'] . '\s*>$~i', $token ) ) {
			continue;
		}
		if ( '' !== $token && '<' === $token[0] ) {
			if ( ( 0 === strpos( $token, '<!--' ) && '-->' !== substr( $token, -3 ) ) || ( 0 === strpos( $token, '<![CDATA[' ) && ']]>' !== substr( $token, -3 ) ) ) {
				return $content;
			}
			if ( preg_match( '~^</([a-z][a-z0-9:-]*)\s*>$~i', $token, $match ) ) {
				$frame = array_pop( $stack );
				if ( ! is_array( $frame ) || strtolower( $match[1] ) !== $frame['tag'] ) {
					return $content;
				}
				continue;
			}
			if ( ! preg_match( '~^<([a-z][a-z0-9:-]*)\b~i', $token, $match ) ) {
				continue; // Comments and declarations remain unchanged.
			}
			$tag       = strtolower( $match[1] );
			$processor = new WP_HTML_Tag_Processor( $token . ( in_array( $tag, $raw_tags, true ) ? '</' . $tag . '>' : '' ) );
			if ( ! $processor->next_tag() ) {
				return $content;
			}
			$key = $processor->get_attribute( 'data-glossary-term' );
			if ( is_string( $key ) ) {
				$linked[ $key ] = true;
				if ( $parent['block'] > 0 ) {
					$blocks[ $parent['block'] ] = 1;
				}
			}
			$blocked = $parent['blocked'] || in_array( $tag, $protected, true ) || in_array( $tag, $raw_tags, true )
				|| null !== $processor->get_attribute( 'data-glossary-skip' ) || null !== $processor->get_attribute( 'hidden' )
				|| 'true' === $processor->get_attribute( 'aria-hidden' ) || null !== $processor->get_attribute( 'contenteditable' )
				|| in_array( $processor->get_attribute( 'role' ), [ 'button', 'link', 'tab', 'menuitem', 'rowheader' ], true ) || is_string( $key );
			$block = $parent['block'];
			if ( in_array( $tag, [ 'p', 'li', 'dd', 'td' ], true ) ) {
				$block = ++$block_id;
				$blocks[ $block ] = 0;
			}
			if ( ! in_array( $tag, $void_tags, true ) && ! preg_match( '~/\s*>$~', $token ) ) {
				$stack[] = [ 'tag' => $tag, 'blocked' => $blocked, 'block' => $block, 'raw' => in_array( $tag, $raw_tags, true ) ? $tag : '' ];
			}
			continue;
		}
		if ( false !== strpos( $token, '<' ) ) {
			return $content;
		}
		if ( $parent['blocked'] || 0 === $parent['block'] || $count >= $max_links || $blocks[ $parent['block'] ] > 0 ) {
			continue;
		}
		$replacement = preg_replace_callback( $pattern, static function ( $match ) use ( $terms, &$linked, &$count, &$blocks, $parent, $max_links ) {
			$key    = mb_strtolower( (string) preg_replace( '~(?:\s|&nbsp;|&#0*160;|&#x0*a0;)+~iu', ' ', $match[0] ) );
			$config = $terms[ $key ];
			if ( isset( $linked[ $config['linked_key'] ] ) || $count >= $max_links || $blocks[ $parent['block'] ] > 0 ) {
				return $match[0];
			}
			$linked[ $config['linked_key'] ] = true;
			$count++;
			$blocks[ $parent['block'] ]++;
			return nexus_glossary_link_markup( $match[0], $config );
		}, $token );
		if ( null === $replacement ) {
			return $content;
		}
		$token = $replacement;
	}
	unset( $token );
	return empty( $stack ) ? implode( '', $tokens ) : $content;
}
