<?php
/**
 * Glossar Auto-Linking: Verlinkt Glossar-Begriffe automatisch in Blog-Posts.
 *
 * Nur erster Treffer pro Begriff pro Post wird verlinkt.
 * Keine Verlinkung innerhalb von Headings, Links, Buttons oder Code-Blöcken.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'the_content', 'nexus_glossary_autolink', 80 );

/**
 * Auto-link glossary terms in blog post content.
 *
 * @param string $content Post content.
 * @return string Modified content with glossary links.
 */
function nexus_glossary_autolink( $content ) {
	if ( ! is_singular( 'post' ) || is_admin() ) {
		return $content;
	}

	if ( ! function_exists( 'nexus_get_glossary_registry' ) || ! function_exists( 'nexus_get_glossary_term_detail_url' ) ) {
		return $content;
	}

	$registry = nexus_get_glossary_registry();

	if ( empty( $registry ) ) {
		return $content;
	}

	// Sammle linkbare Begriffe: title → URL.
	$terms = [];

	foreach ( $registry as $term ) {
		if ( 'publish' !== ( $term['status'] ?? '' ) ) {
			continue;
		}

		$url = nexus_get_glossary_term_detail_url( $term );

		if ( '' === $url ) {
			continue;
		}

		$title = trim( (string) ( $term['title'] ?? '' ) );

		if ( '' === $title || mb_strlen( $title ) < 3 ) {
			continue;
		}

		$terms[ $title ] = $url;
	}

	if ( empty( $terms ) ) {
		return $content;
	}

	// Sortiere nach Länge (längste zuerst) um Teilwort-Matches zu vermeiden.
	uksort( $terms, static function ( $a, $b ) {
		return mb_strlen( $b ) - mb_strlen( $a );
	} );

	// Verlinke max. 1x pro Begriff, max. 8 Glossar-Links pro Post.
	$linked       = [];
	$link_count   = 0;
	$max_links    = 8;
	$current_url  = get_permalink();

	foreach ( $terms as $title => $url ) {
		if ( $link_count >= $max_links ) {
			break;
		}

		// Nicht auf sich selbst verlinken.
		if ( trailingslashit( $url ) === trailingslashit( $current_url ) ) {
			continue;
		}

		// Case-insensitive Wortgrenzen-Match, aber nicht innerhalb von HTML-Tags,
		// Links, Headings, Code oder Buttons.
		$escaped_title = preg_quote( $title, '/' );

		// Callback für sicheres Ersetzen: nur außerhalb von geschützten Tags.
		$content = preg_replace_callback(
			'/(?<![<\/\w])(\b' . $escaped_title . '\b)(?![^<]*<\/(a|h[1-6]|code|pre|button|summary)>)/iu',
			static function ( $matches ) use ( $url, $title, &$linked, &$link_count ) {
				$matched_text = $matches[1];
				$key          = mb_strtolower( $title );

				if ( isset( $linked[ $key ] ) ) {
					return $matched_text;
				}

				$linked[ $key ] = true;
				$link_count++;

				return sprintf(
					'<a href="%s" class="glossary-autolink" title="%s">%s</a>',
					esc_url( $url ),
					esc_attr( sprintf( 'Glossar: %s', $title ) ),
					esc_html( $matched_text )
				);
			},
			$content,
			1
		);
	}

	return $content;
}
