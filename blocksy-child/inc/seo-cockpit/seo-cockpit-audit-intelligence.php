<?php
/**
 * SEO Cockpit audit intelligence layer.
 *
 * Adds Google index status, Search Console performance mapping and deeper
 * crawl/information-architecture diagnostics without changing the deterministic
 * technical health score.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return generic anchor labels that carry little topical meaning. */
function nexus_seo_audit_intelligence_generic_anchors() {
	return [ 'hier', 'mehr', 'weiter', 'mehr erfahren', 'mehr lesen', 'weiterlesen', 'ansehen', 'details', 'klicken', 'jetzt klicken', 'learn more', 'read more' ];
}

/** Guess the semantic DOM area for a link. */
function nexus_seo_audit_intelligence_link_context( $node ) {
	$cursor = $node;
	$limit  = 0;
	while ( $cursor instanceof DOMNode && $limit < 10 ) {
		$name = strtolower( (string) $cursor->nodeName );
		if ( in_array( $name, [ 'main', 'article', 'nav', 'header', 'footer', 'aside' ], true ) ) {
			return $name;
		}
		$cursor = $cursor->parentNode;
		$limit++;
	}
	return 'body';
}

/** Return useful text for an anchor, including accessible/image fallbacks. */
function nexus_seo_audit_intelligence_anchor_text( $link ) {
	$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $link->textContent ) ) );
	if ( '' !== $text ) {
		return $text;
	}
	foreach ( [ 'aria-label', 'title' ] as $attribute ) {
		$value = trim( (string) $link->getAttribute( $attribute ) );
		if ( '' !== $value ) {
			return $value;
		}
	}
	foreach ( $link->getElementsByTagName( 'img' ) as $image ) {
		$value = trim( (string) $image->getAttribute( 'alt' ) );
		if ( '' !== $value ) {
			return $value;
		}
	}
	return '';
}

/** Recursively flatten JSON-LD nodes and @graph objects. */
function nexus_seo_audit_intelligence_collect_schema_nodes( $value, &$nodes ) {
	if ( ! is_array( $value ) ) {
		return;
	}
	$is_assoc = [] !== $value && array_keys( $value ) !== range( 0, count( $value ) - 1 );
	if ( $is_assoc ) {
		if ( isset( $value['@type'] ) ) {
			$nodes[] = $value;
		}
		if ( isset( $value['@graph'] ) && is_array( $value['@graph'] ) ) {
			foreach ( $value['@graph'] as $child ) {
				nexus_seo_audit_intelligence_collect_schema_nodes( $child, $nodes );
			}
		}
		return;
	}
	foreach ( $value as $child ) {
		nexus_seo_audit_intelligence_collect_schema_nodes( $child, $nodes );
	}
}

/** Return semantic schema diagnostics from decoded nodes. */
function nexus_seo_audit_intelligence_schema_hints( $nodes ) {
	$hints = [];
	foreach ( $nodes as $node ) {
		$types = isset( $node['@type'] ) ? (array) $node['@type'] : [];
		foreach ( $types as $type ) {
			$type = (string) $type;
			$required = [];
			switch ( $type ) {
				case 'BreadcrumbList': $required = [ 'itemListElement' ]; break;
				case 'Article':
				case 'BlogPosting':
				case 'NewsArticle': $required = [ 'headline', 'author', 'datePublished' ]; break;
				case 'Organization': $required = [ 'name', 'url' ]; break;
				case 'LocalBusiness': $required = [ 'name', 'address' ]; break;
				case 'WebSite': $required = [ 'name', 'url' ]; break;
				case 'Person': $required = [ 'name' ]; break;
			}
			if ( empty( $required ) ) {
				continue;
			}
			$missing = [];
			foreach ( $required as $field ) {
				if ( ! isset( $node[ $field ] ) || '' === trim( wp_strip_all_tags( is_scalar( $node[ $field ] ) ? (string) $node[ $field ] : '' ) ) ) {
					if ( is_array( $node[ $field ] ?? null ) && ! empty( $node[ $field ] ) ) {
						continue;
					}
					$missing[] = $field;
				}
			}
			if ( ! empty( $missing ) ) {
				$hints[] = [ 'type' => $type, 'missing' => $missing ];
			}
		}
	}
	return $hints;
}

/** Capture richer link/heading/schema details while the existing crawler fetches HTML. */
function nexus_seo_audit_intelligence_capture_http_response( $response, $args, $url ) {
	if ( is_wp_error( $response ) || ! function_exists( 'nexus_seo_audit_normalize_url' ) ) {
		return $response;
	}
	$user_agent = (string) ( $args['user-agent'] ?? '' );
	if ( false === strpos( $user_agent, 'Nexus SEO Cockpit Site Audit/' ) ) {
		return $response;
	}
	$status = absint( wp_remote_retrieve_response_code( $response ) );
	$type   = strtolower( (string) wp_remote_retrieve_header( $response, 'content-type' ) );
	if ( 200 !== $status || ( false === strpos( $type, 'text/html' ) && false === strpos( $type, 'application/xhtml+xml' ) ) || ! class_exists( 'DOMDocument' ) ) {
		return $response;
	}
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$loaded = $dom->loadHTML( '<?xml encoding="utf-8" ?>' . (string) wp_remote_retrieve_body( $response ), LIBXML_NOWARNING | LIBXML_NOERROR );
	libxml_clear_errors();
	if ( ! $loaded ) {
		return $response;
	}
	$link_details = [];
	foreach ( $dom->getElementsByTagName( 'a' ) as $link ) {
		$target = nexus_seo_audit_resolve_url( trim( (string) $link->getAttribute( 'href' ) ), $url );
		if ( '' === $target || ! nexus_seo_audit_is_internal( $target ) ) {
			continue;
		}
		$rel = strtolower( trim( (string) $link->getAttribute( 'rel' ) ) );
		$link_details[] = [
			'target'   => $target,
			'anchor'   => nexus_seo_audit_intelligence_anchor_text( $link ),
			'rel'      => $rel,
			'nofollow' => false !== strpos( ' ' . $rel . ' ', ' nofollow ' ),
			'context'  => nexus_seo_audit_intelligence_link_context( $link ),
		];
	}
	$heading_outline = [];
	for ( $level = 1; $level <= 6; $level++ ) {
		foreach ( $dom->getElementsByTagName( 'h' . $level ) as $heading ) {
			$heading_outline[] = [
				'level' => $level,
				'text'  => trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $heading->textContent ) ) ),
				'order' => method_exists( $heading, 'getLineNo' ) ? absint( $heading->getLineNo() ) : 0,
			];
		}
	}
	usort( $heading_outline, static function ( $a, $b ) { return absint( $a['order'] ) <=> absint( $b['order'] ); } );
	$schema_nodes = [];
	$schema_types = [];
	foreach ( $dom->getElementsByTagName( 'script' ) as $script ) {
		if ( 'application/ld+json' !== strtolower( trim( (string) $script->getAttribute( 'type' ) ) ) ) {
			continue;
		}
		$decoded = json_decode( trim( (string) $script->textContent ), true );
		if ( is_array( $decoded ) ) {
			nexus_seo_audit_intelligence_collect_schema_nodes( $decoded, $schema_nodes );
		}
	}
	foreach ( $schema_nodes as $node ) {
		foreach ( (array) ( $node['@type'] ?? [] ) as $type_name ) {
			$type_name = trim( (string) $type_name );
			if ( '' !== $type_name ) {
				$schema_types[] = $type_name;
			}
		}
	}
	$key = nexus_seo_audit_normalize_url( $url );
	if ( '' !== $key ) {
		if ( ! isset( $GLOBALS['nexus_seo_audit_intelligence_capture'] ) || ! is_array( $GLOBALS['nexus_seo_audit_intelligence_capture'] ) ) {
			$GLOBALS['nexus_seo_audit_intelligence_capture'] = [];
		}
		$GLOBALS['nexus_seo_audit_intelligence_capture'][ $key ] = [
			'internal_link_details' => $link_details,
			'heading_outline'       => $heading_outline,
			'schema_types'          => array_values( array_unique( $schema_types ) ),
			'schema_hints'          => nexus_seo_audit_intelligence_schema_hints( $schema_nodes ),
			'has_main'              => $dom->getElementsByTagName( 'main' )->length > 0,
			'has_article'           => $dom->getElementsByTagName( 'article' )->length > 0,
			'time_elements'         => absint( $dom->getElementsByTagName( 'time' )->length ),
		];
	}
	return $response;
}
add_filter( 'http_response', 'nexus_seo_audit_intelligence_capture_http_response', 20, 3 );

/** Slim URL Inspection payload for audit-state persistence. */
function nexus_seo_audit_intelligence_slim_inspection( $inspection ) {
	return [
		'checked_at'       => absint( $inspection['checked_at'] ?? 0 ),
		'verdict'          => sanitize_text_field( (string) ( $inspection['verdict'] ?? '' ) ),
		'coverage_state'   => sanitize_text_field( (string) ( $inspection['coverage_state'] ?? '' ) ),
		'indexing_state'   => sanitize_text_field( (string) ( $inspection['indexing_state'] ?? '' ) ),
		'page_fetch_state' => sanitize_text_field( (string) ( $inspection['page_fetch_state'] ?? '' ) ),
		'robots_txt_state' => sanitize_text_field( (string) ( $inspection['robots_txt_state'] ?? '' ) ),
		'last_crawl_time'  => sanitize_text_field( (string) ( $inspection['last_crawl_time'] ?? '' ) ),
		'google_canonical' => esc_url_raw( (string) ( $inspection['google_canonical'] ?? '' ) ),
		'user_canonical'   => esc_url_raw( (string) ( $inspection['user_canonical'] ?? '' ) ),
		'sitemaps'         => array_values( array_map( 'esc_url_raw', (array) ( $inspection['sitemaps'] ?? [] ) ) ),
		'referring_count'  => count( (array) ( $inspection['referring_urls'] ?? [] ) ),
	];
}

/** Merge same-request HTTP capture data into newly crawled pages. */
function nexus_seo_audit_intelligence_merge_capture( $state ) {
	$captured = isset( $GLOBALS['nexus_seo_audit_intelligence_capture'] ) && is_array( $GLOBALS['nexus_seo_audit_intelligence_capture'] ) ? $GLOBALS['nexus_seo_audit_intelligence_capture'] : [];
	if ( empty( $captured ) || empty( $state['pages'] ) || ! is_array( $state['pages'] ) ) {
		return $state;
	}
	foreach ( $state['pages'] as $index => $page ) {
		if ( ! is_array( $page ) ) {
			continue;
		}
		$key = nexus_seo_audit_normalize_url( (string) ( $page['url'] ?? '' ) );
		if ( '' !== $key && isset( $captured[ $key ] ) ) {
			$state['pages'][ $index ] = array_merge( $page, $captured[ $key ] );
		}
	}
	return $state;
}

/** Add cached/fresh Google index data to pages newly processed in this batch. */
function nexus_seo_audit_intelligence_add_google_index( $state, $old ) {
	if ( ! function_exists( 'nexus_get_seo_cockpit_url_inspection' ) || ! function_exists( 'nexus_get_seo_cockpit_setup_state' ) ) {
		return $state;
	}
	$setup = nexus_get_seo_cockpit_setup_state();
	if ( empty( $setup['is_ready'] ) ) {
		$state['google_index'] = [ 'status' => 'unavailable', 'message' => 'Search Console ist nicht vollständig verbunden.' ];
		return $state;
	}
	$old_count = is_array( $old['pages'] ?? null ) ? count( $old['pages'] ) : 0;
	$new_count = is_array( $state['pages'] ?? null ) ? count( $state['pages'] ) : 0;
	if ( $new_count <= $old_count ) {
		return $state;
	}
	$inspected = absint( $state['google_index']['inspected'] ?? 0 );
	$errors    = absint( $state['google_index']['errors'] ?? 0 );
	for ( $index = $old_count; $index < $new_count; $index++ ) {
		$page = $state['pages'][ $index ] ?? null;
		if ( ! is_array( $page ) || ! nexus_seo_audit_page_is_indexable_scope( $page ) ) {
			continue;
		}
		$result = nexus_get_seo_cockpit_url_inspection( (string) ( $page['url'] ?? '' ), false );
		if ( is_wp_error( $result ) ) {
			$errors++;
			$state['pages'][ $index ]['google_index_error'] = $result->get_error_message();
			continue;
		}
		$state['pages'][ $index ]['google_index'] = nexus_seo_audit_intelligence_slim_inspection( $result );
		$inspected++;
	}
	$state['google_index'] = [
		'status'    => $errors > 0 ? 'partial' : 'ok',
		'inspected' => $inspected,
		'errors'    => $errors,
		'message'   => $errors > 0 ? 'Einzelne URL-Inspektionen konnten nicht geladen werden.' : '',
	];
	return $state;
}

/** Compute a PageRank-style relative internal authority score. */
function nexus_seo_audit_intelligence_internal_authority( $adjacency ) {
	$urls = array_keys( $adjacency );
	$n    = count( $urls );
	if ( 0 === $n ) {
		return [];
	}
	$damping = 0.85;
	$rank    = array_fill_keys( $urls, 1 / $n );
	for ( $iteration = 0; $iteration < 20; $iteration++ ) {
		$next     = array_fill_keys( $urls, ( 1 - $damping ) / $n );
		$dangling = 0.0;
		foreach ( $urls as $source ) {
			$targets = array_values( array_unique( array_filter( (array) ( $adjacency[ $source ] ?? [] ) ) ) );
			if ( empty( $targets ) ) {
				$dangling += $rank[ $source ];
				continue;
			}
			$share = $rank[ $source ] / count( $targets );
			foreach ( $targets as $target ) {
				if ( isset( $next[ $target ] ) ) {
					$next[ $target ] += $damping * $share;
				}
			}
		}
		if ( $dangling > 0 ) {
			$share = $damping * $dangling / $n;
			foreach ( $urls as $url ) {
				$next[ $url ] += $share;
			}
		}
		$rank = $next;
	}
	$max = max( $rank );
	if ( $max <= 0 ) {
		return array_fill_keys( $urls, 0.0 );
	}
	foreach ( $rank as $url => $value ) {
		$rank[ $url ] = round( $value / $max * 100, 1 );
	}
	return $rank;
}

/** Detect heading-level jumps. */
function nexus_seo_audit_intelligence_heading_jumps( $outline ) {
	$jumps = [];
	$previous = null;
	foreach ( $outline as $heading ) {
		$level = absint( $heading['level'] ?? 0 );
		if ( $level < 1 || $level > 6 ) {
			continue;
		}
		if ( null !== $previous && $level > $previous + 1 ) {
			$jumps[] = [ 'from' => $previous, 'to' => $level, 'text' => (string) ( $heading['text'] ?? '' ) ];
		}
		$previous = $level;
	}
	return $jumps;
}

/** Analyze crawl depth, link support, redirects, anchors and schema semantics. */
function nexus_seo_audit_intelligence_architecture( $state ) {
	$pages      = is_array( $state['pages'] ?? null ) ? $state['pages'] : [];
	$active     = [];
	$indexes    = [];
	$status_map = [];
	foreach ( $pages as $index => $page ) {
		if ( ! is_array( $page ) ) {
			continue;
		}
		$url = nexus_seo_audit_normalize_url( (string) ( $page['url'] ?? '' ) );
		if ( '' === $url ) {
			continue;
		}
		$indexes[ $url ]    = $index;
		$status_map[ $url ] = absint( $page['status_code'] ?? 0 );
		if ( nexus_seo_audit_page_is_indexable_scope( $page ) ) {
			$active[ $url ] = true;
		}
	}
	$adjacency = array_fill_keys( array_keys( $active ), [] );
	$incoming  = array_fill_keys( array_keys( $active ), [] );
	$anchor_in = array_fill_keys( array_keys( $active ), [] );
	$redirect_sources = [];
	$nofollow_internal = 0;
	foreach ( $active as $source => $_ ) {
		$page    = $pages[ $indexes[ $source ] ];
		$details = is_array( $page['internal_link_details'] ?? null ) ? $page['internal_link_details'] : [];
		if ( empty( $details ) ) {
			foreach ( (array) ( $page['internal_links'] ?? [] ) as $target ) {
				$details[] = [ 'target' => $target, 'anchor' => '', 'rel' => '', 'nofollow' => false, 'context' => 'unknown' ];
			}
		}
		foreach ( $details as $detail ) {
			$target = nexus_seo_audit_normalize_url( (string) ( $detail['target'] ?? '' ) );
			if ( '' === $target || $target === $source ) {
				continue;
			}
			if ( ! empty( $detail['nofollow'] ) ) {
				$nofollow_internal++;
			}
			if ( isset( $status_map[ $target ] ) && $status_map[ $target ] >= 300 && $status_map[ $target ] < 400 ) {
				$redirect_sources[ $source ][ $target ] = true;
			}
			if ( ! isset( $active[ $target ] ) ) {
				continue;
			}
			$adjacency[ $source ][ $target ] = $target;
			$incoming[ $target ][ $source ]  = $source;
			$anchor = strtolower( trim( preg_replace( '/\s+/u', ' ', (string) ( $detail['anchor'] ?? '' ) ) ) );
			if ( '' !== $anchor ) {
				$anchor_in[ $target ][] = $anchor;
			}
		}
		$adjacency[ $source ] = array_values( $adjacency[ $source ] );
	}
	$home   = nexus_seo_audit_normalize_url( home_url( '/' ) );
	$depths = array_fill_keys( array_keys( $active ), null );
	$paths  = array_fill_keys( array_keys( $active ), [] );
	if ( isset( $active[ $home ] ) ) {
		$depths[ $home ] = 0;
		$paths[ $home ]  = [ $home ];
		$queue = [ $home ];
		while ( ! empty( $queue ) ) {
			$source = array_shift( $queue );
			foreach ( (array) ( $adjacency[ $source ] ?? [] ) as $target ) {
				if ( null !== $depths[ $target ] ) {
					continue;
				}
				$depths[ $target ] = absint( $depths[ $source ] ) + 1;
				$paths[ $target ]  = array_merge( (array) $paths[ $source ], [ $target ] );
				$queue[]           = $target;
			}
		}
	}
	$authority       = nexus_seo_audit_intelligence_internal_authority( $adjacency );
	$findings        = [];
	$generic_anchors = nexus_seo_audit_intelligence_generic_anchors();
	$depth_values    = [];
	foreach ( $active as $url => $_ ) {
		$index     = $indexes[ $url ];
		$in_count  = count( $incoming[ $url ] ?? [] );
		$out_count = count( $adjacency[ $url ] ?? [] );
		$depth     = $depths[ $url ];
		if ( null !== $depth ) {
			$depth_values[] = $depth;
		}
		$anchors = (array) ( $anchor_in[ $url ] ?? [] );
		$generic_count = 0;
		foreach ( $anchors as $anchor ) {
			if ( in_array( $anchor, $generic_anchors, true ) ) {
				$generic_count++;
			}
		}
		$generic_ratio = count( $anchors ) > 0 ? $generic_count / count( $anchors ) : 0;
		$pages[ $index ]['architecture'] = [
			'crawl_depth'           => $depth,
			'crawl_path'            => $paths[ $url ] ?? [],
			'incoming_sources'      => $in_count,
			'outgoing_targets'      => $out_count,
			'internal_authority'    => (float) ( $authority[ $url ] ?? 0 ),
			'incoming_anchor_count' => count( $anchors ),
			'generic_anchor_ratio'  => round( $generic_ratio, 3 ),
		];
		if ( null !== $depth && $depth >= 4 ) {
			$findings[] = nexus_seo_audit_issue( 'deep_crawl_path', 'links', 'medium', 'Wichtige URL liegt tief in der Klickstruktur', 'Prüfen, ob die Seite über einen thematisch passenden Hub oder eine stärkere interne Verlinkung näher an die Startseite rücken sollte.', $url, 0, [ 'depth' => $depth, 'path' => $paths[ $url ] ?? [] ] );
		} elseif ( null !== $depth && $depth >= 3 && $in_count <= 1 ) {
			$findings[] = nexus_seo_audit_issue( 'weak_internal_support', 'links', 'low', 'Wenig interne Unterstützung', 'Bei strategisch wichtigen Seiten einen zusätzlichen kontextuellen Link aus einem passenden Hub oder Leistungsbereich prüfen.', $url, 0, [ 'depth' => $depth, 'incoming_sources' => $in_count ] );
		}
		if ( count( $anchors ) >= 3 && $generic_ratio >= 0.75 ) {
			$findings[] = nexus_seo_audit_issue( 'generic_anchor_pattern', 'links', 'info', 'Viele generische interne Ankertexte', 'Kontextuelle Links nach Möglichkeit mit beschreibenden Ankertexten versehen; Navigation und Buttons nicht künstlich überoptimieren.', $url, 0, [ 'ratio' => round( $generic_ratio, 2 ), 'anchors' => array_slice( array_values( array_unique( $anchors ) ), 0, 8 ) ] );
		}
		$jumps = nexus_seo_audit_intelligence_heading_jumps( is_array( $pages[ $index ]['heading_outline'] ?? null ) ? $pages[ $index ]['heading_outline'] : [] );
		if ( ! empty( $jumps ) ) {
			$findings[] = nexus_seo_audit_issue( 'heading_level_jump', 'structure', 'info', 'Überschriftenebenen werden übersprungen', 'Semantische Gliederung prüfen. Ein Ebenensprung ist nicht automatisch ein SEO-Fehler, kann aber Struktur und Barrierefreiheit verschlechtern.', $url, 0, [ 'jumps' => array_slice( $jumps, 0, 5 ) ] );
		}
		$schema_hints = is_array( $pages[ $index ]['schema_hints'] ?? null ) ? $pages[ $index ]['schema_hints'] : [];
		if ( ! empty( $schema_hints ) ) {
			$findings[] = nexus_seo_audit_issue( 'schema_semantic_hint', 'schema', 'info', 'Schema semantisch unvollständig', 'Schema-Typ und erwartete Eigenschaften fachlich prüfen; nur Daten auszeichnen, die auf der Seite tatsächlich sichtbar bzw. zutreffend sind.', $url, 0, [ 'hints' => array_slice( $schema_hints, 0, 5 ) ] );
		}
	}
	foreach ( $redirect_sources as $source => $targets ) {
		$findings[] = nexus_seo_audit_issue( 'internal_links_to_redirect', 'links', 'medium', 'Interne Links laufen über Redirects', 'Interne Links direkt auf das endgültige kanonische Ziel umstellen.', $source, 0, [ 'targets' => array_keys( $targets ) ] );
	}
	$max_depth = ! empty( $depth_values ) ? max( $depth_values ) : 0;
	$avg_depth = ! empty( $depth_values ) ? round( array_sum( $depth_values ) / count( $depth_values ), 1 ) : 0;
	$orphans = 0;
	foreach ( $depths as $url => $depth ) {
		if ( $url !== $home && null === $depth ) {
			$orphans++;
		}
	}
	$state['pages'] = $pages;
	$state['intelligence']['architecture'] = [
		'active_urls'           => count( $active ),
		'max_depth'             => $max_depth,
		'average_depth'         => $avg_depth,
		'unreachable_urls'      => $orphans,
		'redirect_link_sources' => count( $redirect_sources ),
		'nofollow_internal'     => $nofollow_internal,
	];
	$state['intelligence']['architecture_findings'] = $findings;
	return $state;
}

/** Parse sitemap XML recursively with a conservative request cap. */
function nexus_seo_audit_intelligence_read_sitemap( $url, &$visited, &$budget ) {
	$result = [ 'urls' => [], 'sitemaps' => [], 'errors' => [] ];
	$url = esc_url_raw( (string) $url );
	if ( '' === $url || $budget <= 0 || isset( $visited[ $url ] ) ) {
		return $result;
	}
	$visited[ $url ] = true;
	$budget--;
	$response = wp_remote_get( $url, [
		'timeout' => 12,
		'redirection' => 3,
		'user-agent' => 'Nexus SEO Cockpit Intelligence/1.0; ' . home_url( '/' ),
		'headers' => [ 'Accept' => 'application/xml,text/xml,*/*;q=0.1' ],
	] );
	if ( is_wp_error( $response ) ) {
		$result['errors'][] = $response->get_error_message();
		return $result;
	}
	$status = absint( wp_remote_retrieve_response_code( $response ) );
	if ( $status < 200 || $status >= 300 ) {
		$result['errors'][] = 'HTTP ' . $status . ': ' . $url;
		return $result;
	}
	$body = (string) wp_remote_retrieve_body( $response );
	if ( '' === trim( $body ) || ! function_exists( 'simplexml_load_string' ) ) {
		return $result;
	}
	libxml_use_internal_errors( true );
	$xml = simplexml_load_string( $body );
	libxml_clear_errors();
	if ( false === $xml ) {
		$result['errors'][] = 'Ungültiges Sitemap-XML: ' . $url;
		return $result;
	}
	$url_nodes = $xml->xpath( '//*[local-name()="url"]/*[local-name()="loc"]' );
	if ( is_array( $url_nodes ) ) {
		foreach ( $url_nodes as $node ) {
			$loc = nexus_seo_audit_normalize_url( (string) $node );
			if ( '' !== $loc ) {
				$result['urls'][ $loc ] = true;
			}
		}
	}
	$sitemap_nodes = $xml->xpath( '//*[local-name()="sitemap"]/*[local-name()="loc"]' );
	if ( is_array( $sitemap_nodes ) ) {
		foreach ( array_slice( $sitemap_nodes, 0, 20 ) as $node ) {
			$child = esc_url_raw( trim( (string) $node ) );
			if ( '' === $child ) {
				continue;
			}
			$result['sitemaps'][ $child ] = true;
			if ( $budget > 0 ) {
				$child_result = nexus_seo_audit_intelligence_read_sitemap( $child, $visited, $budget );
				$result['urls']     = $result['urls'] + (array) $child_result['urls'];
				$result['sitemaps'] = $result['sitemaps'] + (array) $child_result['sitemaps'];
				$result['errors']   = array_merge( $result['errors'], (array) $child_result['errors'] );
			}
		}
	}
	return $result;
}

/** Run site-level robots/sitemap checks. */
function nexus_seo_audit_intelligence_site_checks( $state ) {
	$checks = [];
	$findings = [];
	$robots_url = home_url( '/robots.txt' );
	$response = wp_remote_get( $robots_url, [
		'timeout' => 10,
		'redirection' => 3,
		'user-agent' => 'Nexus SEO Cockpit Intelligence/1.0; ' . home_url( '/' ),
		'headers' => [ 'Accept' => 'text/plain,*/*;q=0.1' ],
	] );
	$robots_body = '';
	$robots_ok = false;
	if ( is_wp_error( $response ) ) {
		$checks['robots'] = [ 'status' => 'error', 'message' => $response->get_error_message() ];
		$findings[] = nexus_seo_audit_issue( 'robots_unreachable', 'indexability', 'medium', 'robots.txt konnte nicht geprüft werden', 'robots.txt erreichbar machen und Server-/Cache-Regeln prüfen.', $robots_url, 0 );
	} else {
		$status = absint( wp_remote_retrieve_response_code( $response ) );
		$robots_body = (string) wp_remote_retrieve_body( $response );
		$robots_ok = 200 === $status;
		$checks['robots'] = [ 'status' => $status, 'bytes' => strlen( $robots_body ) ];
		if ( ! $robots_ok ) {
			$findings[] = nexus_seo_audit_issue( 'robots_http_status', 'indexability', 'medium', 'robots.txt antwortet nicht mit 200', 'robots.txt und Webserver-Regeln prüfen.', $robots_url, 0, [ 'status' => $status ] );
		}
	}
	if ( $robots_ok && preg_match( '/User-agent:\s*\*[\s\S]{0,1000}?Disallow:\s*\/\s*(?:\r?\n|$)/i', $robots_body ) ) {
		$findings[] = nexus_seo_audit_issue( 'robots_blocks_all', 'indexability', 'critical', 'robots.txt blockiert die gesamte Website', 'Disallow: / für User-agent: * sofort entfernen, sofern die Website öffentlich indexierbar sein soll.', $robots_url, 0 );
	}
	$sitemap_candidates = [];
	if ( $robots_ok && preg_match_all( '/^\s*Sitemap:\s*(\S+)\s*$/im', $robots_body, $matches ) ) {
		foreach ( $matches[1] as $candidate ) {
			$candidate = esc_url_raw( trim( (string) $candidate ) );
			if ( '' !== $candidate ) {
				$sitemap_candidates[ $candidate ] = true;
			}
		}
	}
	if ( empty( $sitemap_candidates ) ) {
		$sitemap_candidates[ home_url( '/sitemap_index.xml' ) ] = true;
		$sitemap_candidates[ home_url( '/wp-sitemap.xml' ) ]   = true;
	}
	$visited = [];
	$budget = 12;
	$all_sitemap_urls = [];
	$all_sitemaps = [];
	$sitemap_errors = [];
	foreach ( array_keys( $sitemap_candidates ) as $candidate ) {
		if ( $budget <= 0 ) {
			break;
		}
		$parsed = nexus_seo_audit_intelligence_read_sitemap( $candidate, $visited, $budget );
		$all_sitemap_urls = $all_sitemap_urls + (array) $parsed['urls'];
		$all_sitemaps     = $all_sitemaps + (array) $parsed['sitemaps'];
		$sitemap_errors   = array_merge( $sitemap_errors, (array) $parsed['errors'] );
		if ( ! empty( $parsed['urls'] ) || ! empty( $parsed['sitemaps'] ) ) {
			$all_sitemaps[ $candidate ] = true;
		}
	}
	$active = [];
	$nonindexable = [];
	foreach ( (array) ( $state['pages'] ?? [] ) as $page ) {
		if ( ! is_array( $page ) ) {
			continue;
		}
		$url = nexus_seo_audit_normalize_url( (string) ( $page['url'] ?? '' ) );
		if ( '' === $url ) {
			continue;
		}
		if ( nexus_seo_audit_page_is_indexable_scope( $page ) ) {
			$active[ $url ] = true;
		} elseif ( ! empty( $page['expected_noindex'] ) || nexus_seo_audit_page_has_noindex( $page ) || absint( $page['status_code'] ?? 0 ) >= 300 ) {
			$nonindexable[ $url ] = true;
		}
	}
	$missing_from_sitemap = [];
	foreach ( $active as $url => $_ ) {
		if ( ! isset( $all_sitemap_urls[ $url ] ) ) {
			$missing_from_sitemap[] = $url;
		}
	}
	$bad_in_sitemap = [];
	foreach ( $all_sitemap_urls as $url => $_ ) {
		if ( isset( $nonindexable[ $url ] ) ) {
			$bad_in_sitemap[] = $url;
		}
	}
	if ( ! empty( $all_sitemap_urls ) && ! empty( $missing_from_sitemap ) ) {
		$findings[] = nexus_seo_audit_issue( 'indexable_urls_missing_sitemap', 'indexability', 'low', 'Indexierbare URLs fehlen in der Sitemap', 'Sitemap-Generator und bewusste Ausnahmen prüfen. Eine Sitemap ist kein Rankingfaktor, erleichtert Google aber Discovery und Crawl-Steuerung.', home_url( '/' ), 0, [ 'count' => count( $missing_from_sitemap ), 'examples' => array_slice( $missing_from_sitemap, 0, 10 ) ] );
	}
	if ( ! empty( $bad_in_sitemap ) ) {
		$findings[] = nexus_seo_audit_issue( 'nonindexable_urls_in_sitemap', 'indexability', 'medium', 'Nicht indexierbare URLs stehen in der Sitemap', 'Redirect-, noindex- und Fehler-URLs aus XML-Sitemaps entfernen.', home_url( '/' ), 0, [ 'count' => count( $bad_in_sitemap ), 'examples' => array_slice( $bad_in_sitemap, 0, 10 ) ] );
	}
	$checks['sitemap'] = [
		'discovered_sitemaps' => array_keys( $all_sitemaps ),
		'url_count' => count( $all_sitemap_urls ),
		'missing_indexable' => count( $missing_from_sitemap ),
		'nonindexable_present' => count( $bad_in_sitemap ),
		'errors' => array_slice( array_values( array_unique( $sitemap_errors ) ), 0, 10 ),
	];
	if ( empty( $all_sitemap_urls ) ) {
		$findings[] = nexus_seo_audit_issue( 'sitemap_not_discovered', 'indexability', 'info', 'Keine auswertbare XML-Sitemap gefunden', 'Prüfen, ob eine XML-Sitemap bewusst deaktiviert ist oder unter einer anderen URL liegt.', home_url( '/' ), 0 );
	}
	$state['intelligence']['site_checks']   = $checks;
	$state['intelligence']['site_findings'] = $findings;
	return $state;
}

/** Build Google-index diagnostics after all crawl pages were processed. */
function nexus_seo_audit_intelligence_google_findings( $state ) {
	$findings = [];
	$indexable = 0;
	$inspected = 0;
	$passed = 0;
	$canonical_diff = 0;
	$fetch_problems = 0;
	foreach ( (array) ( $state['pages'] ?? [] ) as $page ) {
		if ( ! is_array( $page ) || ! nexus_seo_audit_page_is_indexable_scope( $page ) ) {
			continue;
		}
		$indexable++;
		$url = (string) ( $page['url'] ?? '' );
		$inspection = is_array( $page['google_index'] ?? null ) ? $page['google_index'] : [];
		if ( empty( $inspection ) ) {
			continue;
		}
		$inspected++;
		$verdict = strtoupper( (string) ( $inspection['verdict'] ?? '' ) );
		if ( 'PASS' === $verdict ) {
			$passed++;
		} elseif ( '' !== $verdict && 'VERDICT_UNSPECIFIED' !== $verdict ) {
			$findings[] = nexus_seo_audit_issue( 'google_index_verdict', 'indexability', 'medium', 'Google meldet keinen sauberen Index-Status', 'Coverage State, Page Fetch State, Robots und Canonical in der URL Inspection prüfen.', $url, 0, [ 'verdict' => $verdict, 'coverage_state' => (string) ( $inspection['coverage_state'] ?? '' ) ] );
		}
		$fetch_state = strtoupper( (string) ( $inspection['page_fetch_state'] ?? '' ) );
		if ( '' !== $fetch_state && 'SUCCESSFUL' !== $fetch_state && 'PAGE_FETCH_STATE_UNSPECIFIED' !== $fetch_state ) {
			$fetch_problems++;
			$findings[] = nexus_seo_audit_issue( 'google_fetch_problem', 'indexability', 'high', 'Google meldet ein Crawl-/Fetch-Problem', 'URL Inspection öffnen und Server-, Robots-, Redirect- oder Zugriffsproblem beheben.', $url, 0, [ 'page_fetch_state' => $fetch_state ] );
		}
		$google_canonical = nexus_seo_audit_normalize_url( (string) ( $inspection['google_canonical'] ?? '' ) );
		$user_canonical   = nexus_seo_audit_normalize_url( (string) ( $inspection['user_canonical'] ?? '' ) );
		if ( '' !== $google_canonical && '' !== $user_canonical && $google_canonical !== $user_canonical ) {
			$canonical_diff++;
			$findings[] = nexus_seo_audit_issue( 'google_canonical_differs', 'indexability', 'medium', 'Google wählt ein anderes Canonical', 'Interne Links, Sitemap, Redirects und Duplicate-Content-Signale für diese URL prüfen.', $url, 0, [ 'google' => $google_canonical, 'user' => $user_canonical ] );
		}
	}
	$state['intelligence']['google_index_summary'] = [
		'indexable' => $indexable,
		'inspected' => $inspected,
		'passed' => $passed,
		'canonical_mismatch' => $canonical_diff,
		'fetch_problems' => $fetch_problems,
	];
	$state['intelligence']['google_findings'] = $findings;
	return $state;
}

/** Enrich audit state during batch saves. */
function nexus_seo_audit_intelligence_filter_state( $value, $old_value, $option ) {
	if ( ! is_array( $value ) || empty( $value['run_id'] ) ) {
		return $value;
	}
	$old_value = is_array( $old_value ) ? $old_value : [];
	$value = nexus_seo_audit_intelligence_merge_capture( $value );
	$value = nexus_seo_audit_intelligence_add_google_index( $value, $old_value );
	if ( 'completed' === (string) ( $value['status'] ?? '' ) ) {
		$value = nexus_seo_audit_intelligence_architecture( $value );
		$value = nexus_seo_audit_intelligence_google_findings( $value );
		$value = nexus_seo_audit_intelligence_site_checks( $value );
		$value['intelligence']['generated_at'] = current_time( 'mysql' );
	}
	return $value;
}
add_filter( 'pre_update_option_' . NEXUS_SEO_AUDIT_STATE_OPTION, 'nexus_seo_audit_intelligence_filter_state', 20, 3 );

/** Normalize Search Console page rows into an audit URL map. */
function nexus_seo_audit_intelligence_page_metric_map( $rows ) {
	$map = [];
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) || empty( $row['keys'][0] ) ) {
			continue;
		}
		$url = nexus_seo_audit_normalize_url( (string) $row['keys'][0] );
		if ( '' === $url ) {
			continue;
		}
		$map[ $url ] = [
			'clicks' => (float) ( $row['clicks'] ?? 0 ),
			'impressions' => (float) ( $row['impressions'] ?? 0 ),
			'ctr' => (float) ( $row['ctr'] ?? 0 ),
			'position' => (float) ( $row['position'] ?? 0 ),
		];
	}
	return $map;
}

/** Return percent change with a null baseline when the previous value is zero. */
function nexus_seo_audit_intelligence_percent_change( $current, $previous ) {
	if ( 0.0 === (float) $previous ) {
		return null;
	}
	return round( ( ( (float) $current - (float) $previous ) / abs( (float) $previous ) ) * 100, 1 );
}

/** Build Search Console performance data for the Site Audit UI. */
function nexus_seo_audit_get_search_performance( $state ) {
	$empty = [
		'status' => 'unavailable',
		'message' => 'Search-Performance ist nicht verfügbar.',
		'range_days' => 28,
		'overview' => [],
		'pages' => [],
		'opportunities' => [],
		'cannibalization' => [],
		'generated_at' => 0,
	];
	if ( ! function_exists( 'nexus_get_seo_cockpit_snapshot' ) ) {
		return $empty;
	}
	$snapshot = nexus_get_seo_cockpit_snapshot( false, 28 );
	if ( is_wp_error( $snapshot ) ) {
		$empty['message'] = $snapshot->get_error_message();
		return $empty;
	}
	$current_map  = nexus_seo_audit_intelligence_page_metric_map( (array) ( $snapshot['current_page_rows'] ?? [] ) );
	$previous_map = nexus_seo_audit_intelligence_page_metric_map( (array) ( $snapshot['previous_page_rows'] ?? [] ) );
	$active = [];
	$page_data = [];
	$post_ids = [];
	foreach ( (array) ( $state['pages'] ?? [] ) as $page ) {
		if ( ! is_array( $page ) || ! nexus_seo_audit_page_is_indexable_scope( $page ) ) {
			continue;
		}
		$url = nexus_seo_audit_normalize_url( (string) ( $page['url'] ?? '' ) );
		if ( '' === $url ) {
			continue;
		}
		$active[ $url ] = true;
		$post_ids[ $url ] = absint( $page['post_id'] ?? 0 );
		$current  = $current_map[ $url ] ?? [ 'clicks' => 0.0, 'impressions' => 0.0, 'ctr' => 0.0, 'position' => 0.0 ];
		$previous = $previous_map[ $url ] ?? [ 'clicks' => 0.0, 'impressions' => 0.0, 'ctr' => 0.0, 'position' => 0.0 ];
		$page_data[ $url ] = [
			'url' => $url,
			'current' => $current,
			'previous' => $previous,
			'click_change_pct' => nexus_seo_audit_intelligence_percent_change( $current['clicks'], $previous['clicks'] ),
			'impression_change_pct' => nexus_seo_audit_intelligence_percent_change( $current['impressions'], $previous['impressions'] ),
			'ctr_change_pp' => round( ( $current['ctr'] - $previous['ctr'] ) * 100, 2 ),
			'position_change' => ( $current['position'] > 0 && $previous['position'] > 0 ) ? round( $previous['position'] - $current['position'], 1 ) : null,
			'queries' => [],
		];
	}
	$query_groups = [];
	foreach ( (array) ( $snapshot['query_page_rows'] ?? [] ) as $row ) {
		if ( ! is_array( $row ) || empty( $row['keys'][0] ) || empty( $row['keys'][1] ) ) {
			continue;
		}
		$url = nexus_seo_audit_normalize_url( (string) $row['keys'][0] );
		$query = trim( (string) $row['keys'][1] );
		if ( '' === $url || '' === $query || ! isset( $active[ $url ] ) ) {
			continue;
		}
		$query_row = [
			'query' => $query,
			'clicks' => (float) ( $row['clicks'] ?? 0 ),
			'impressions' => (float) ( $row['impressions'] ?? 0 ),
			'ctr' => (float) ( $row['ctr'] ?? 0 ),
			'position' => (float) ( $row['position'] ?? 0 ),
		];
		$page_data[ $url ]['queries'][] = $query_row;
		$query_key = function_exists( 'mb_strtolower' ) ? mb_strtolower( $query ) : strtolower( $query );
		$query_groups[ $query_key ] = $query_groups[ $query_key ] ?? [ 'query' => $query, 'pages' => [] ];
		$query_groups[ $query_key ]['pages'][ $url ] = $query_row;
	}
	foreach ( $page_data as &$page ) {
		usort( $page['queries'], static function ( $a, $b ) { return (float) $b['impressions'] <=> (float) $a['impressions']; } );
		$page['queries'] = array_slice( $page['queries'], 0, 5 );
	}
	unset( $page );
	$opportunities = [];
	foreach ( $page_data as $url => $data ) {
		$current = $data['current'];
		$previous = $data['previous'];
		$position = (float) $current['position'];
		$impressions = (float) $current['impressions'];
		$clicks = (float) $current['clicks'];
		$ctr = (float) $current['ctr'];
		if ( $impressions >= 50 && $clicks <= 0 ) {
			$opportunities[] = [ 'type' => 'zero_click_demand', 'severity' => 'medium', 'title' => 'Impressionen ohne Klicks', 'url' => $url, 'reason' => sprintf( '%.0f Impressionen, 0 Klicks, Ø Position %.1f', $impressions, $position ), 'score' => 90 + min( 50, $impressions / 10 ) ];
		}
		if ( $impressions >= 30 && $position >= 8 && $position <= 20 ) {
			$opportunities[] = [ 'type' => 'striking_distance', 'severity' => 'medium', 'title' => 'Ranking in Schlagdistanz', 'url' => $url, 'reason' => sprintf( 'Ø Position %.1f bei %.0f Impressionen', $position, $impressions ), 'score' => 80 + min( 50, $impressions / 20 ) + ( 20 - $position ) ];
		}
		$ctr_floor = 0.0;
		if ( $position > 0 && $position <= 3 ) {
			$ctr_floor = 0.08;
		} elseif ( $position <= 5 ) {
			$ctr_floor = 0.04;
		} elseif ( $position <= 10 ) {
			$ctr_floor = 0.02;
		}
		if ( $impressions >= 100 && $ctr_floor > 0 && $ctr < $ctr_floor ) {
			$opportunities[] = [ 'type' => 'ctr_candidate', 'severity' => 'low', 'title' => 'CTR-Potenzial', 'url' => $url, 'reason' => sprintf( 'CTR %.1f%% bei Ø Position %.1f und %.0f Impressionen', $ctr * 100, $position, $impressions ), 'score' => 65 + min( 50, $impressions / 25 ) ];
		}
		if ( (float) $previous['impressions'] >= 20 && $impressions >= 10 && (float) $previous['position'] > 0 && $position - (float) $previous['position'] >= 3 ) {
			$opportunities[] = [ 'type' => 'ranking_loss', 'severity' => 'medium', 'title' => 'Rankingverlust', 'url' => $url, 'reason' => sprintf( 'Ø Position %.1f → %.1f', (float) $previous['position'], $position ), 'score' => 95 + min( 50, $impressions / 20 ) ];
		}
		if ( (float) $previous['clicks'] >= 5 && $clicks <= (float) $previous['clicks'] * 0.7 ) {
			$opportunities[] = [ 'type' => 'click_loss', 'severity' => 'medium', 'title' => 'Deutlicher Klickverlust', 'url' => $url, 'reason' => sprintf( 'Klicks %.0f → %.0f', (float) $previous['clicks'], $clicks ), 'score' => 100 + (float) $previous['clicks'] - $clicks ];
		}
		$post_id = absint( $post_ids[ $url ] ?? 0 );
		$modified_ts = $post_id > 0 ? (int) get_post_modified_time( 'U', true, $post_id ) : 0;
		if ( $modified_ts > 0 && $impressions >= 50 && $modified_ts < time() - ( 18 * 30 * 24 * 60 * 60 ) ) {
			$opportunities[] = [ 'type' => 'stale_with_demand', 'severity' => 'info', 'title' => 'Älterer Inhalt mit Suchnachfrage', 'url' => $url, 'reason' => 'Seit mehr als 18 Monaten nicht geändert und weiterhin mit Impressionen.', 'score' => 40 + min( 40, $impressions / 50 ) ];
		}
	}
	$cannibalization = [];
	foreach ( $query_groups as $group ) {
		$qualified = [];
		$total_impressions = 0.0;
		foreach ( (array) $group['pages'] as $url => $row ) {
			if ( (float) $row['impressions'] >= 10 ) {
				$qualified[ $url ] = $row;
				$total_impressions += (float) $row['impressions'];
			}
		}
		if ( count( $qualified ) >= 2 && $total_impressions >= 30 ) {
			uasort( $qualified, static function ( $a, $b ) { return (float) $b['impressions'] <=> (float) $a['impressions']; } );
			$cannibalization[] = [ 'query' => (string) $group['query'], 'pages' => $qualified, 'impressions' => $total_impressions ];
		}
	}
	usort( $opportunities, static function ( $a, $b ) { return (float) ( $b['score'] ?? 0 ) <=> (float) ( $a['score'] ?? 0 ); } );
	usort( $cannibalization, static function ( $a, $b ) { return (float) $b['impressions'] <=> (float) $a['impressions']; } );
	$current_overview  = (array) ( $snapshot['overview']['current'] ?? [] );
	$previous_overview = (array) ( $snapshot['overview']['previous'] ?? [] );
	return [
		'status' => 'ok',
		'message' => '',
		'range_days' => absint( $snapshot['range_days'] ?? 28 ),
		'generated_at' => absint( $snapshot['generated_at'] ?? 0 ),
		'overview' => [
			'current' => $current_overview,
			'previous' => $previous_overview,
			'click_change_pct' => nexus_seo_audit_intelligence_percent_change( (float) ( $current_overview['clicks'] ?? 0 ), (float) ( $previous_overview['clicks'] ?? 0 ) ),
			'impression_change_pct' => nexus_seo_audit_intelligence_percent_change( (float) ( $current_overview['impressions'] ?? 0 ), (float) ( $previous_overview['impressions'] ?? 0 ) ),
			'ctr_change_pp' => round( ( (float) ( $current_overview['ctr'] ?? 0 ) - (float) ( $previous_overview['ctr'] ?? 0 ) ) * 100, 2 ),
			'position_change' => ( (float) ( $current_overview['position'] ?? 0 ) > 0 && (float) ( $previous_overview['position'] ?? 0 ) > 0 ) ? round( (float) $previous_overview['position'] - (float) $current_overview['position'], 1 ) : null,
		],
		'pages' => $page_data,
		'opportunities' => array_slice( $opportunities, 0, 30 ),
		'cannibalization' => array_slice( $cannibalization, 0, 20 ),
	];
}

/** Return all non-score intelligence findings in priority order. */
function nexus_seo_audit_get_intelligence_findings( $state ) {
	$intelligence = is_array( $state['intelligence'] ?? null ) ? $state['intelligence'] : [];
	$findings = array_merge( (array) ( $intelligence['google_findings'] ?? [] ), (array) ( $intelligence['site_findings'] ?? [] ), (array) ( $intelligence['architecture_findings'] ?? [] ) );
	$rank = [ 'critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3, 'info' => 4 ];
	usort( $findings, static function ( $a, $b ) use ( $rank ) {
		$left  = $rank[ $a['severity'] ?? 'info' ] ?? 5;
		$right = $rank[ $b['severity'] ?? 'info' ] ?? 5;
		return $left <=> $right;
	} );
	return $findings;
}
