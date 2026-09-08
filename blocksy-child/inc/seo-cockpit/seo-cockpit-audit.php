<?php
/**
 * SEO Cockpit deterministic site-audit engine.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NEXUS_SEO_AUDIT_STATE_OPTION   = 'nexus_seo_cockpit_site_audit_state';
const NEXUS_SEO_AUDIT_HISTORY_OPTION = 'nexus_seo_cockpit_site_audit_history';
const NEXUS_SEO_AUDIT_LOCK           = 'nexus_seo_cockpit_site_audit_lock';
const NEXUS_SEO_AUDIT_EVENT          = 'nexus_seo_cockpit_site_audit_batch';

/**
 * Score categories. Performance stays unmeasured until CrUX is fused in.
 *
 * @return array<string,array<string,mixed>>
 */
function nexus_seo_audit_categories() {
	return [
		'indexability' => [ 'label' => 'Crawling & Indexierung', 'weight' => 25, 'measured' => true ],
		'snippets'     => [ 'label' => 'Title & Snippets', 'weight' => 15, 'measured' => true ],
		'structure'    => [ 'label' => 'Headings & Struktur', 'weight' => 15, 'measured' => true ],
		'links'        => [ 'label' => 'Interne Verlinkung', 'weight' => 15, 'measured' => true ],
		'performance'  => [ 'label' => 'Core Web Vitals', 'weight' => 10, 'measured' => false ],
		'schema'       => [ 'label' => 'Structured Data', 'weight' => 10, 'measured' => true ],
		'media'        => [ 'label' => 'Bilder & Medien', 'weight' => 5, 'measured' => true ],
		'content'      => [ 'label' => 'Content-Signale', 'weight' => 5, 'measured' => true ],
	];
}

/** Normalize a URL for graph comparisons. */
function nexus_seo_audit_normalize_url( $url ) {
	$parts = wp_parse_url( trim( (string) $url ) );
	if ( ! is_array( $parts ) || empty( $parts['host'] ) ) {
		return '';
	}
	$scheme = strtolower( (string) ( $parts['scheme'] ?? wp_parse_url( home_url( '/' ), PHP_URL_SCHEME ) ) );
	$host   = strtolower( (string) $parts['host'] );
	$port   = isset( $parts['port'] ) ? ':' . absint( $parts['port'] ) : '';
	$path   = '/' . ltrim( (string) ( $parts['path'] ?? '/' ), '/' );
	$path   = preg_replace( '#/+#', '/', $path );
	if ( '/' !== $path ) {
		$path = untrailingslashit( $path );
	}
	return $scheme . '://' . $host . $port . $path;
}

/** Same-host check. */
function nexus_seo_audit_is_internal( $url ) {
	return strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) ) === strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );
}

/** Resolve relative hrefs. */
function nexus_seo_audit_resolve_url( $href, $base ) {
	$href = html_entity_decode( trim( (string) $href ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	if ( '' === $href || '#' === $href[0] || preg_match( '#^(?:mailto|tel|javascript|data):#i', $href ) ) {
		return '';
	}
	if ( preg_match( '#^https?://#i', $href ) ) {
		return nexus_seo_audit_normalize_url( $href );
	}
	$base_parts = wp_parse_url( $base );
	if ( ! is_array( $base_parts ) || empty( $base_parts['host'] ) ) {
		return '';
	}
	$scheme = (string) ( $base_parts['scheme'] ?? 'https' );
	$host   = (string) $base_parts['host'];
	$port   = isset( $base_parts['port'] ) ? ':' . absint( $base_parts['port'] ) : '';
	if ( 0 === strpos( $href, '//' ) ) {
		return nexus_seo_audit_normalize_url( $scheme . ':' . $href );
	}
	$href_path = (string) wp_parse_url( $href, PHP_URL_PATH );
	if ( '' === $href_path ) {
		return '';
	}
	$path = '/' === $href_path[0] ? $href_path : trailingslashit( dirname( (string) ( $base_parts['path'] ?? '/' ) ) ) . $href_path;
	$segments = [];
	foreach ( explode( '/', $path ) as $segment ) {
		if ( '' === $segment || '.' === $segment ) {
			continue;
		}
		if ( '..' === $segment ) {
			array_pop( $segments );
			continue;
		}
		$segments[] = $segment;
	}
	return nexus_seo_audit_normalize_url( $scheme . '://' . $host . $port . '/' . implode( '/', $segments ) );
}

/** Build a stable issue payload. */
function nexus_seo_audit_issue( $code, $category, $severity, $title, $recommendation, $url, $penalty = 0, $evidence = [] ) {
	return [
		'id'             => md5( $code . '|' . $url . '|' . wp_json_encode( $evidence ) ),
		'code'           => sanitize_key( $code ),
		'category'       => sanitize_key( $category ),
		'severity'       => sanitize_key( $severity ),
		'title'          => (string) $title,
		'recommendation' => (string) $recommendation,
		'url'            => (string) $url,
		'penalty'        => max( 0, (float) $penalty ),
		'evidence'       => is_array( $evidence ) ? $evidence : [],
	];
}

/** Latest state. */
function nexus_seo_audit_get_state() {
	$state = get_option( NEXUS_SEO_AUDIT_STATE_OPTION, [] );
	return is_array( $state ) ? $state : [];
}

/** Save latest state without autoload. */
function nexus_seo_audit_save_state( $state ) {
	if ( false === get_option( NEXUS_SEO_AUDIT_STATE_OPTION, false ) ) {
		add_option( NEXUS_SEO_AUDIT_STATE_OPTION, $state, '', false );
		return;
	}
	update_option( NEXUS_SEO_AUDIT_STATE_OPTION, $state, false );
}

/** Discover public WordPress URLs. */
function nexus_seo_audit_discover_urls() {
	$post_types = get_post_types( [ 'public' => true ], 'names' );
	unset( $post_types['attachment'] );
	$ids = get_posts( [
		'post_type' => array_values( $post_types ), 'post_status' => 'publish', 'posts_per_page' => -1,
		'fields' => 'ids', 'orderby' => 'ID', 'order' => 'ASC', 'no_found_rows' => true,
	] );
	$urls = [ nexus_seo_audit_normalize_url( home_url( '/' ) ) ];
	foreach ( $ids as $post_id ) {
		$url = nexus_seo_audit_normalize_url( get_permalink( $post_id ) );
		if ( '' !== $url && nexus_seo_audit_is_internal( $url ) ) {
			$urls[] = $url;
		}
	}
	$urls = array_values( array_unique( array_filter( $urls ) ) );
	return array_slice( $urls, 0, max( 1, (int) apply_filters( 'nexus_seo_site_audit_max_urls', 200 ) ) );
}

/** Read one named meta tag. */
function nexus_seo_audit_dom_meta( $dom, $name ) {
	foreach ( $dom->getElementsByTagName( 'meta' ) as $node ) {
		if ( strtolower( trim( (string) $node->getAttribute( 'name' ) ) ) === strtolower( $name ) ) {
			return trim( (string) $node->getAttribute( 'content' ) );
		}
	}
	return '';
}

/** Read canonical URL. */
function nexus_seo_audit_dom_canonical( $dom, $base ) {
	foreach ( $dom->getElementsByTagName( 'link' ) as $node ) {
		$rels = preg_split( '/\s+/', strtolower( trim( (string) $node->getAttribute( 'rel' ) ) ) );
		if ( is_array( $rels ) && in_array( 'canonical', $rels, true ) ) {
			return nexus_seo_audit_resolve_url( (string) $node->getAttribute( 'href' ), $base );
		}
	}
	return '';
}

/** Parse HTML into deterministic fields. */
function nexus_seo_audit_parse_html( $body, $url ) {
	$data = [
		'parser_available' => class_exists( 'DOMDocument' ), 'title' => '', 'meta_description' => '', 'robots' => '',
		'canonical' => '', 'h1' => [], 'word_count' => 0, 'image_count' => 0, 'images_missing_alt' => 0,
		'jsonld_count' => 0, 'jsonld_invalid' => 0, 'internal_links' => [],
	];
	if ( ! class_exists( 'DOMDocument' ) ) {
		return $data;
	}
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$loaded = $dom->loadHTML( '<?xml encoding="utf-8" ?>' . (string) $body, LIBXML_NOWARNING | LIBXML_NOERROR );
	libxml_clear_errors();
	if ( ! $loaded ) {
		$data['parser_available'] = false;
		return $data;
	}
	$titles = $dom->getElementsByTagName( 'title' );
	if ( $titles->length ) {
		$data['title'] = trim( wp_strip_all_tags( (string) $titles->item( 0 )->textContent ) );
	}
	$data['meta_description'] = nexus_seo_audit_dom_meta( $dom, 'description' );
	$data['robots'] = strtolower( nexus_seo_audit_dom_meta( $dom, 'robots' ) );
	$data['canonical'] = nexus_seo_audit_dom_canonical( $dom, $url );
	foreach ( $dom->getElementsByTagName( 'h1' ) as $node ) {
		$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $node->textContent ) ) );
		if ( '' !== $text ) {
			$data['h1'][] = $text;
		}
	}
	foreach ( $dom->getElementsByTagName( 'img' ) as $image ) {
		$data['image_count']++;
		if ( ! $image->hasAttribute( 'alt' ) ) {
			$data['images_missing_alt']++;
		}
	}
	foreach ( $dom->getElementsByTagName( 'script' ) as $script ) {
		if ( 'application/ld+json' !== strtolower( trim( (string) $script->getAttribute( 'type' ) ) ) ) {
			continue;
		}
		$data['jsonld_count']++;
		$json = trim( (string) $script->textContent );
		if ( '' === $json || null === json_decode( $json, true ) ) {
			$data['jsonld_invalid']++;
		}
	}
	$links = [];
	foreach ( $dom->getElementsByTagName( 'a' ) as $link ) {
		$target = nexus_seo_audit_resolve_url( (string) $link->getAttribute( 'href' ), $url );
		if ( '' !== $target && nexus_seo_audit_is_internal( $target ) ) {
			$links[] = $target;
		}
	}
	$data['internal_links'] = array_values( array_unique( $links ) );
	$text = preg_replace( '#<(script|style|noscript)\b[^>]*>.*?</\1>#is', ' ', (string) $body );
	$text = preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $text ) );
	if ( preg_match_all( '/[\p{L}\p{N}][\p{L}\p{N}\p{M}\-]*/u', (string) $text, $matches ) ) {
		$data['word_count'] = count( $matches[0] );
	}
	return $data;
}

/** Immediate page rules. */
function nexus_seo_audit_page_issues( $page ) {
	$issues = [];
	$url = (string) ( $page['url'] ?? '' );
	$code = absint( $page['status_code'] ?? 0 );
	if ( ! empty( $page['fetch_error'] ) ) {
		return [ nexus_seo_audit_issue( 'fetch_error', 'indexability', 'critical', 'URL konnte nicht abgerufen werden', 'Server-, DNS-, TLS- oder Timeout-Fehler prüfen.', $url, 15, [ 'message' => $page['fetch_error'] ] ) ];
	}
	if ( $code >= 500 ) {
		$issues[] = nexus_seo_audit_issue( 'http_5xx', 'indexability', 'critical', 'Serverfehler', 'URL muss stabil erfolgreich antworten.', $url, 15, [ 'status' => $code ] );
	} elseif ( $code >= 400 ) {
		$issues[] = nexus_seo_audit_issue( 'http_4xx', 'indexability', 'high', 'Fehlerhafter HTTP-Status', 'Ziel reparieren, wiederherstellen oder sauber weiterleiten.', $url, 10, [ 'status' => $code ] );
	}
	if ( 200 !== $code || empty( $page['is_html'] ) ) {
		return $issues;
	}
	if ( empty( $page['parser_available'] ) ) {
		$issues[] = nexus_seo_audit_issue( 'parser_unavailable', 'structure', 'info', 'HTML-Parser nicht verfügbar', 'PHP-DOM-Erweiterung aktivieren.', $url );
		return $issues;
	}
	if ( false !== strpos( strtolower( (string) ( $page['robots'] ?? '' ) ), 'noindex' ) ) {
		$issues[] = nexus_seo_audit_issue( 'noindex', 'indexability', 'critical', 'Veröffentlichte URL steht auf noindex', 'Prüfen, ob der Ausschluss beabsichtigt ist; sonst noindex entfernen.', $url, 12 );
	}
	$canonical = (string) ( $page['canonical'] ?? '' );
	if ( '' === $canonical ) {
		$issues[] = nexus_seo_audit_issue( 'canonical_missing', 'indexability', 'low', 'Canonical fehlt', 'Eine eindeutige kanonische URL ausgeben.', $url, 1 );
	} elseif ( nexus_seo_audit_normalize_url( $canonical ) !== nexus_seo_audit_normalize_url( $url ) ) {
		$issues[] = nexus_seo_audit_issue( 'canonical_conflict', 'indexability', 'high', 'Canonical zeigt auf eine andere URL', 'Canonical-Ziel fachlich prüfen.', $url, 6, [ 'canonical' => $canonical ] );
	}
	$title = trim( (string) ( $page['title'] ?? '' ) );
	if ( '' === $title ) {
		$issues[] = nexus_seo_audit_issue( 'title_missing', 'snippets', 'high', 'SEO-Title fehlt', 'Einen eindeutigen, suchintention-nahen Title ausgeben.', $url, 7 );
	} else {
		$length = function_exists( 'mb_strlen' ) ? mb_strlen( $title ) : strlen( $title );
		if ( $length < 25 || $length > 65 ) {
			$issues[] = nexus_seo_audit_issue( 'title_length_hint', 'snippets', 'low', 'Title-Länge auffällig', 'Nicht auf starre Zeichenlimits optimieren; SERP-Tauglichkeit prüfen.', $url, 1, [ 'characters' => $length ] );
		}
	}
	$description = trim( (string) ( $page['meta_description'] ?? '' ) );
	if ( '' === $description ) {
		$issues[] = nexus_seo_audit_issue( 'meta_description_missing', 'snippets', 'medium', 'Meta Description fehlt', 'Eine präzise Description als Snippet-Kandidat formulieren.', $url, 2 );
	}
	$h1 = is_array( $page['h1'] ?? null ) ? $page['h1'] : [];
	if ( empty( $h1 ) ) {
		$issues[] = nexus_seo_audit_issue( 'h1_missing', 'structure', 'high', 'H1 fehlt', 'Eine klare Hauptüberschrift für das Seitenthema ausgeben.', $url, 5 );
	} elseif ( count( $h1 ) > 1 ) {
		$issues[] = nexus_seo_audit_issue( 'multiple_h1', 'structure', 'info', 'Mehrere H1 gefunden', 'Nicht automatisch ein SEO-Fehler; semantische Eindeutigkeit prüfen.', $url, 0, [ 'count' => count( $h1 ) ] );
	}
	$missing_alt = absint( $page['images_missing_alt'] ?? 0 );
	if ( $missing_alt ) {
		$issues[] = nexus_seo_audit_issue( 'images_missing_alt', 'media', 'medium', 'Bilder ohne alt-Attribut', 'Inhaltliche Bilder beschreiben; dekorative Bilder bewusst mit leerem alt kennzeichnen.', $url, min( 3, 1 + $missing_alt / 5 ), [ 'count' => $missing_alt ] );
	}
	$invalid_jsonld = absint( $page['jsonld_invalid'] ?? 0 );
	if ( $invalid_jsonld ) {
		$issues[] = nexus_seo_audit_issue( 'jsonld_invalid', 'schema', 'high', 'Ungültiges JSON-LD gefunden', 'JSON-LD syntaktisch reparieren und validieren.', $url, min( 6, 3 * $invalid_jsonld ), [ 'count' => $invalid_jsonld ] );
	}
	if ( 0 === absint( $page['jsonld_count'] ?? 0 ) ) {
		$issues[] = nexus_seo_audit_issue( 'structured_data_absent', 'schema', 'info', 'Kein JSON-LD gefunden', 'Nur passenden Schema-Typ ergänzen, wenn die Seite die Voraussetzungen erfüllt.', $url );
	}
	if ( absint( $page['word_count'] ?? 0 ) < 200 ) {
		$issues[] = nexus_seo_audit_issue( 'low_text_volume', 'content', 'info', 'Wenig sichtbarer Text', 'Nicht nach Wortzahl optimieren; Vollständigkeit zur Suchintention prüfen.', $url, 0, [ 'words' => absint( $page['word_count'] ?? 0 ) ] );
	}
	return $issues;
}

/** Fetch and parse one URL. */
function nexus_seo_audit_crawl_url( $url ) {
	$page = [
		'url' => $url, 'status_code' => 0, 'is_html' => false, 'fetch_error' => '', 'parser_available' => true,
		'title' => '', 'meta_description' => '', 'robots' => '', 'canonical' => '', 'h1' => [], 'word_count' => 0,
		'image_count' => 0, 'images_missing_alt' => 0, 'jsonld_count' => 0, 'jsonld_invalid' => 0, 'internal_links' => [],
	];
	$response = wp_remote_get( $url, [
		'timeout' => 15, 'redirection' => 5,
		'user-agent' => 'Nexus SEO Cockpit Site Audit/1.0; ' . home_url( '/' ),
		'headers' => [ 'Accept' => 'text/html,application/xhtml+xml' ],
	] );
	if ( is_wp_error( $response ) ) {
		$page['fetch_error'] = $response->get_error_message();
		$page['issues'] = nexus_seo_audit_page_issues( $page );
		return $page;
	}
	$page['status_code'] = absint( wp_remote_retrieve_response_code( $response ) );
	$content_type = strtolower( (string) wp_remote_retrieve_header( $response, 'content-type' ) );
	$page['is_html'] = false !== strpos( $content_type, 'text/html' ) || false !== strpos( $content_type, 'application/xhtml+xml' );
	if ( 200 === $page['status_code'] && $page['is_html'] ) {
		$page = array_merge( $page, nexus_seo_audit_parse_html( wp_remote_retrieve_body( $response ), $url ) );
	}
	$page['issues'] = nexus_seo_audit_page_issues( $page );
	return $page;
}

/** Cross-page duplicate/linkgraph rules. */
function nexus_seo_audit_apply_sitewide_rules( $state ) {
	$pages = is_array( $state['pages'] ?? null ) ? $state['pages'] : [];
	$title_map = []; $description_map = []; $status_map = []; $incoming = []; $indexes = [];
	foreach ( $pages as $index => $page ) {
		$url = nexus_seo_audit_normalize_url( (string) ( $page['url'] ?? '' ) );
		if ( '' === $url ) { continue; }
		$indexes[ $url ] = $index; $status_map[ $url ] = absint( $page['status_code'] ?? 0 ); $incoming[ $url ] = 0;
		$title = strtolower( trim( (string) ( $page['title'] ?? '' ) ) );
		if ( '' !== $title ) { $title_map[ $title ][] = $url; }
		$description = strtolower( trim( (string) ( $page['meta_description'] ?? '' ) ) );
		if ( '' !== $description ) { $description_map[ $description ][] = $url; }
	}
	foreach ( $pages as $page ) {
		$source = nexus_seo_audit_normalize_url( (string) ( $page['url'] ?? '' ) );
		$broken = [];
		foreach ( (array) ( $page['internal_links'] ?? [] ) as $target ) {
			$target = nexus_seo_audit_normalize_url( $target );
			if ( isset( $incoming[ $target ] ) && $target !== $source ) { $incoming[ $target ]++; }
			if ( isset( $status_map[ $target ] ) && $status_map[ $target ] >= 400 ) { $broken[] = $target; }
		}
		if ( $broken && isset( $indexes[ $source ] ) ) {
			$pages[ $indexes[ $source ] ]['issues'][] = nexus_seo_audit_issue( 'broken_internal_links', 'links', 'high', 'Kaputte interne Links', 'Interne Links auf funktionierende kanonische Ziele umstellen.', $source, min( 10, 4 + count( $broken ) ), [ 'targets' => array_values( array_unique( $broken ) ) ] );
		}
	}
	foreach ( $title_map as $urls ) {
		if ( count( $urls ) < 2 ) { continue; }
		foreach ( $urls as $url ) { $pages[ $indexes[ $url ] ]['issues'][] = nexus_seo_audit_issue( 'duplicate_title', 'snippets', 'high', 'Title ist auf mehreren URLs identisch', 'Eigenständigen Title je indexierbarer Seite formulieren.', $url, 4, [ 'duplicates' => $urls ] ); }
	}
	foreach ( $description_map as $urls ) {
		if ( count( $urls ) < 2 ) { continue; }
		foreach ( $urls as $url ) { $pages[ $indexes[ $url ] ]['issues'][] = nexus_seo_audit_issue( 'duplicate_meta_description', 'snippets', 'medium', 'Meta Description ist mehrfach identisch', 'Description an Seite und Suchintention anpassen.', $url, 2, [ 'duplicates' => $urls ] ); }
	}
	$home = nexus_seo_audit_normalize_url( home_url( '/' ) );
	foreach ( $incoming as $url => $count ) {
		if ( $url === $home || $count > 0 || ! isset( $indexes[ $url ] ) ) { continue; }
		$index = $indexes[ $url ];
		if ( 200 === absint( $pages[ $index ]['status_code'] ?? 0 ) ) {
			$pages[ $index ]['issues'][] = nexus_seo_audit_issue( 'orphan_candidate', 'links', 'medium', 'Keine eingehenden internen Links im Audit gefunden', 'Prüfen, ob die URL bewusst isoliert ist; wichtige Seiten intern verlinken.', $url, 3 );
		}
	}
	$state['pages'] = $pages;
	return $state;
}

/** Score one page. */
function nexus_seo_audit_score_page( $page ) {
	$categories = nexus_seo_audit_categories();
	$deductions = array_fill_keys( array_keys( $categories ), 0.0 );
	foreach ( (array) ( $page['issues'] ?? [] ) as $issue ) {
		$category = sanitize_key( (string) ( $issue['category'] ?? '' ) );
		if ( isset( $deductions[ $category ] ) ) { $deductions[ $category ] += max( 0, (float) ( $issue['penalty'] ?? 0 ) ); }
	}
	$weight_total = 0; $deduction_total = 0; $category_scores = [];
	foreach ( $categories as $key => $config ) {
		if ( empty( $config['measured'] ) ) { $category_scores[ $key ] = null; continue; }
		$weight = (float) $config['weight']; $weight_total += $weight;
		$deduction = min( $weight, $deductions[ $key ] ); $deduction_total += $deduction;
		$category_scores[ $key ] = (int) round( 100 - ( $deduction / $weight * 100 ) );
	}
	$page['score'] = $weight_total > 0 ? max( 0, min( 100, (int) round( 100 - ( $deduction_total / $weight_total * 100 ) ) ) ) : 0;
	$page['category_scores'] = $category_scores;
	return $page;
}

/** Finalize sitewide score/history. */
function nexus_seo_audit_finalize( $state ) {
	$state = nexus_seo_audit_apply_sitewide_rules( $state );
	$pages = (array) ( $state['pages'] ?? [] ); $issues = []; $score_total = 0; $scored = 0;
	$severity = [ 'critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0, 'info' => 0 ];
	$cat_totals = []; $cat_counts = [];
	foreach ( $pages as $index => $page ) {
		$page = nexus_seo_audit_score_page( $page ); $pages[ $index ] = $page; $score_total += absint( $page['score'] ?? 0 ); $scored++;
		foreach ( (array) ( $page['category_scores'] ?? [] ) as $key => $value ) { if ( null !== $value ) { $cat_totals[ $key ] = ( $cat_totals[ $key ] ?? 0 ) + $value; $cat_counts[ $key ] = ( $cat_counts[ $key ] ?? 0 ) + 1; } }
		foreach ( (array) ( $page['issues'] ?? [] ) as $issue ) { $issues[] = $issue; $sev = sanitize_key( (string) ( $issue['severity'] ?? 'info' ) ); if ( isset( $severity[ $sev ] ) ) { $severity[ $sev ]++; } }
	}
	$cat_scores = [];
	foreach ( nexus_seo_audit_categories() as $key => $config ) { $cat_scores[ $key ] = empty( $config['measured'] ) ? null : ( ! empty( $cat_counts[ $key ] ) ? (int) round( $cat_totals[ $key ] / $cat_counts[ $key ] ) : null ); }
	$rank = [ 'critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3, 'info' => 4 ];
	usort( $issues, static function( $a, $b ) use ( $rank ) { $ar = $rank[ $a['severity'] ?? 'info' ] ?? 5; $br = $rank[ $b['severity'] ?? 'info' ] ?? 5; return $ar === $br ? ( (float) ( $b['penalty'] ?? 0 ) <=> (float) ( $a['penalty'] ?? 0 ) ) : ( $ar <=> $br ); } );
	$state['pages'] = $pages; $state['issues'] = $issues; $state['health_score'] = $scored ? (int) round( $score_total / $scored ) : 0;
	$state['category_scores'] = $cat_scores; $state['severity_counts'] = $severity; $state['processed_urls'] = count( $pages ); $state['status'] = 'completed'; $state['finished_at'] = current_time( 'mysql' );
	$history = get_option( NEXUS_SEO_AUDIT_HISTORY_OPTION, [] ); $history = is_array( $history ) ? $history : [];
	array_unshift( $history, [ 'run_id' => (string) ( $state['run_id'] ?? '' ), 'finished_at' => $state['finished_at'], 'health_score' => $state['health_score'], 'total_urls' => absint( $state['total_urls'] ?? 0 ), 'severity_counts' => $severity ] );
	$history = array_slice( $history, 0, 12 );
	if ( false === get_option( NEXUS_SEO_AUDIT_HISTORY_OPTION, false ) ) { add_option( NEXUS_SEO_AUDIT_HISTORY_OPTION, $history, '', false ); } else { update_option( NEXUS_SEO_AUDIT_HISTORY_OPTION, $history, false ); }
	return $state;
}

/** Schedule next background batch. */
function nexus_seo_audit_schedule_batch( $run_id ) {
	$args = [ (string) $run_id ];
	if ( '' !== $run_id && ! wp_next_scheduled( NEXUS_SEO_AUDIT_EVENT, $args ) ) { wp_schedule_single_event( time() + 2, NEXUS_SEO_AUDIT_EVENT, $args ); }
	if ( function_exists( 'spawn_cron' ) ) { spawn_cron(); }
}

/** Process one small background batch. */
function nexus_seo_audit_process_batch( $run_id ) {
	$state = nexus_seo_audit_get_state();
	if ( empty( $state ) || (string) ( $state['run_id'] ?? '' ) !== (string) $run_id || 'completed' === ( $state['status'] ?? '' ) ) { return; }
	if ( get_transient( NEXUS_SEO_AUDIT_LOCK ) ) { nexus_seo_audit_schedule_batch( $run_id ); return; }
	set_transient( NEXUS_SEO_AUDIT_LOCK, '1', 2 * MINUTE_IN_SECONDS );
	try {
		$urls = is_array( $state['urls'] ?? null ) ? $state['urls'] : []; $cursor = absint( $state['cursor'] ?? 0 );
		$end = min( count( $urls ), $cursor + max( 1, (int) apply_filters( 'nexus_seo_site_audit_batch_size', 4 ) ) );
		$state['status'] = 'running'; if ( empty( $state['started_at'] ) ) { $state['started_at'] = current_time( 'mysql' ); }
		for ( $i = $cursor; $i < $end; $i++ ) { $state['pages'][] = nexus_seo_audit_crawl_url( (string) $urls[ $i ] ); $state['cursor'] = $i + 1; $state['processed_urls'] = $i + 1; }
		if ( $state['cursor'] >= count( $urls ) ) { $state = nexus_seo_audit_finalize( $state ); } else { nexus_seo_audit_save_state( $state ); nexus_seo_audit_schedule_batch( $run_id ); }
	} catch ( Throwable $throwable ) {
		$state['status'] = 'failed'; $state['finished_at'] = current_time( 'mysql' ); $state['error_message'] = $throwable->getMessage();
	} finally {
		delete_transient( NEXUS_SEO_AUDIT_LOCK ); nexus_seo_audit_save_state( $state );
	}
}
add_action( NEXUS_SEO_AUDIT_EVENT, 'nexus_seo_audit_process_batch', 10, 1 );

/** Start a run from wp-admin. */
function nexus_handle_seo_audit_start() {
	if ( ! function_exists( 'nexus_current_user_can_manage_seo_cockpit' ) || ! nexus_current_user_can_manage_seo_cockpit() ) { wp_die( esc_html__( 'Nicht erlaubt.', 'blocksy-child' ) ); }
	check_admin_referer( 'nexus_seo_audit_start' );
	$current = nexus_seo_audit_get_state();
	if ( in_array( (string) ( $current['status'] ?? '' ), [ 'queued', 'running' ], true ) ) { wp_safe_redirect( add_query_arg( 'audit_notice', 'already_running', admin_url( 'admin.php?page=nexus-seo-cockpit-site-audit' ) ) ); exit; }
	$urls = nexus_seo_audit_discover_urls(); $run_id = wp_generate_uuid4();
	$state = [ 'version' => 1, 'run_id' => $run_id, 'status' => 'queued', 'queued_at' => current_time( 'mysql' ), 'started_at' => '', 'finished_at' => '', 'urls' => $urls, 'cursor' => 0, 'total_urls' => count( $urls ), 'processed_urls' => 0, 'pages' => [], 'issues' => [], 'health_score' => null, 'category_scores' => [], 'severity_counts' => [] ];
	nexus_seo_audit_save_state( $state ); nexus_seo_audit_schedule_batch( $run_id );
	wp_safe_redirect( add_query_arg( 'audit_notice', 'started', admin_url( 'admin.php?page=nexus-seo-cockpit-site-audit' ) ) ); exit;
}
add_action( 'admin_post_nexus_seo_audit_start', 'nexus_handle_seo_audit_start' );
