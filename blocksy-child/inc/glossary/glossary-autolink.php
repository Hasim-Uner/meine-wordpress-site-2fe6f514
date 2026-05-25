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
 * Return explicit non-registry internal link mappings for the Solar/SHK pillar.
 *
 * Default Solar mappings live in the glossary registry as alias terms so keyword
 * ownership stays data-driven. This hook remains for runtime extensions.
 *
 * @return array<string, string> Map of exact keyword phrase => target URL.
 */
function nexus_get_solar_pillar_autolink_mappings() {
	/**
	 * Filter explicit Solar/SHK autolink mappings outside the registry.
	 *
	 * @param array<string, string> $mappings Exact phrase => URL.
	 */
	return apply_filters( 'nexus_solar_pillar_autolink_mappings', [] );
}

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

	$registry = [];

	if ( function_exists( 'nexus_get_glossary_registry' ) && function_exists( 'nexus_get_glossary_term_detail_url' ) ) {
		$registry = nexus_get_glossary_registry();
	}

	// Sammle linkbare Begriffe: phrase → link config.
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
		$slug  = sanitize_title( (string) ( $term['slug'] ?? $title ) );

		if ( '' === $title || mb_strlen( $title ) < 3 ) {
			continue;
		}

		$phrases = array_values(
			array_unique(
				array_filter(
					array_merge( [ $title ], (array) ( $term['keywords_match'] ?? [] ) ),
					static function ( $phrase ) {
						return is_string( $phrase ) && mb_strlen( trim( $phrase ) ) >= 3;
					}
				)
			)
		);

		foreach ( $phrases as $phrase ) {
			$phrase = trim( (string) $phrase );

			if ( isset( $terms[ $phrase ] ) ) {
				continue;
			}

			$terms[ $phrase ] = [
				'url'        => $url,
				'title'      => sprintf( 'Glossar: %s', $title ),
				'class'      => 'glossary-autolink',
				'linked_key' => 'glossary:' . ( '' !== $slug ? $slug : mb_strtolower( $title ) ),
			];
		}
	}

	foreach ( nexus_get_solar_pillar_autolink_mappings() as $phrase => $url ) {
		$phrase = trim( (string) $phrase );
		$url    = trim( (string) $url );

		if ( '' === $phrase || '' === $url || mb_strlen( $phrase ) < 3 ) {
			continue;
		}

		if ( isset( $terms[ $phrase ] ) ) {
			continue;
		}

		$terms[ $phrase ] = [
			'url'        => $url,
			'title'      => sprintf( 'Solar-Pillar: %s', $phrase ),
			'class'      => 'glossary-autolink glossary-autolink--solar',
			'linked_key' => 'solar_pillar:' . sanitize_title( $phrase ),
		];
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

	foreach ( $terms as $title => $config ) {
		if ( $link_count >= $max_links ) {
			break;
		}

		$url = (string) ( $config['url'] ?? '' );

		if ( '' === $url ) {
			continue;
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
			static function ( $matches ) use ( $url, $title, $config, &$linked, &$link_count ) {
				$matched_text = $matches[1];
				$key          = (string) ( $config['linked_key'] ?? mb_strtolower( $title ) );

				if ( isset( $linked[ $key ] ) ) {
					return $matched_text;
				}

				$linked[ $key ] = true;
				$link_count++;

				return sprintf(
					'<a href="%s" class="%s" title="%s">%s</a>',
					esc_url( $url ),
					esc_attr( (string) ( $config['class'] ?? 'glossary-autolink' ) ),
					esc_attr( (string) ( $config['title'] ?? sprintf( 'Glossar: %s', $title ) ) ),
					esc_html( $matched_text )
				);
			},
			$content,
			1
		);
	}

	return $content;
}
