<?php
/**
 * NEXUS SEO Meta & Indexierungssteuerung
 *
 * Pluginlose Eigenimplementierung fuer Title, Description, OG Tags,
 * Twitter Card, Canonical und Robots.
 *
 * Legacy: Liest noch vorhandene rank_math_* Post-Meta als Fallback,
 * falls ACF-Felder leer sind. Neue Inhalte nutzen ausschliesslich ACF.
 *
 * [SEO] inc/seo-meta: OG-Bild Override, Indexierungs-Logik
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', 'hu_seo_meta_tags', 1 );

add_filter( 'pre_get_document_title', 'hu_pre_get_document_title_override' );
add_filter( 'document_title_parts', 'hu_document_title_overrides' );

/**
 * Return the enforced homepage SEO title.
 *
 * @return string
 */
function hu_get_homepage_title() {
	return (string) apply_filters(
		'hu_homepage_seo_title',
		'WordPress als Nachfrage-System für B2B | Haşim Üner'
	);
}

/**
 * Return the enforced homepage SEO description.
 *
 * @return string
 */
function hu_get_homepage_description() {
	return (string) apply_filters(
		'hu_homepage_seo_description',
		'Ich mache aus Ihrer WordPress-Website ein planbares Anfragesystem: Positionierung, technische SEO, Tracking und Conversion-Logik für B2B.'
	);
}

/**
 * Return the enforced blog index SEO title.
 *
 * @return string
 */
function hu_get_blog_archive_title() {
	return (string) apply_filters(
		'hu_blog_archive_seo_title',
		'Insights zu WordPress und SEO | Haşim Üner'
	);
}

/**
 * Return the enforced blog index SEO description.
 *
 * @return string
 */
function hu_get_blog_archive_description() {
	return (string) apply_filters(
		'hu_blog_archive_seo_description',
		'Analysen zu WordPress, technischer SEO, Tracking und Conversion-Logik für B2B-Websites.'
	);
}

/**
 * Return forced SEO overrides for singular pages that must ignore legacy DB meta.
 *
 * @return array<string, array<string, string>>
 */
function hu_get_forced_singular_seo_map() {
	$e3_cpl_reduction = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_reduction', 'display', '-85,3 %' ) : '-85,3 %';

	return (array) apply_filters(
		'hu_forced_singular_seo_map',
		[
			'kontakt' => [
				'title'       => 'Kontakt & Projektanfrage | Haşim Üner',
				'description' => 'Projekt starten oder Frage stellen: Formular ausfuellen, Rueckmeldung in 48 h. Kein Pflicht-Call, kein Sales-Druck – nur eine fundierte Ersteinschaetzung.',
			],
			'kontaktiere-mich' => [
				'title'       => 'Kontakt & Projektanfrage | Haşim Üner',
				'description' => 'Projekt starten oder Frage stellen: Formular ausfuellen, Rueckmeldung in 48 h. Kein Pflicht-Call, kein Sales-Druck – nur eine fundierte Ersteinschaetzung.',
			],
			'uber-mich' => [
				'title'       => 'Über Hasim Üner – Anfrage-Infrastruktur für Solar & Wärmepumpe',
				'description' => 'Ich baue Solar- und Wärmepumpen-Anbietern eigene Anfrage-Systeme. Weg von gemieteten Portal-Leads, hin zu Infrastruktur im eigenen Eigentum.',
			],
			// 'wgos' / 'wordpress-growth-operating-system' Meta-Einträge entfernt:
			// Seiten sind noindex, daher keine öffentlichen Meta-Signale mehr.
			'tools' => [
				'title'       => 'Kostenlose Website- und ROI-Tools | Haşim Üner',
				'description' => 'Kostenlose Tools für ROI, Website-Analyse und Performance: schnelle Checks für Marketing-, Website- und WordPress-Entscheidungen.',
			],
			'kostenlose-tools' => [
				'title'       => 'Kostenlose Website- und ROI-Tools | Haşim Üner',
				'description' => 'Kostenlose Tools für ROI, Website-Analyse und Performance: schnelle Checks für Marketing-, Website- und WordPress-Entscheidungen.',
			],
			'wordpress-agentur-hannover' => [
				'title'       => 'WordPress Agentur Hannover – SEO, Wartung & Conversion',
				'description' => sprintf( 'WordPress Agentur in Hannover: technisches SEO, Wartungsvertrag, Tracking und Conversion für B2B-Websites als verbundenes System. Referenz E3 — %s Leadkosten.', $e3_cpl_reduction ),
			],
			'wordpress-agentur' => [
				'title'       => 'WordPress Agentur Hannover – SEO, Wartung & Conversion',
				'description' => sprintf( 'WordPress Agentur in Hannover: technisches SEO, Wartungsvertrag, Tracking und Conversion für B2B-Websites als verbundenes System. Referenz E3 — %s Leadkosten.', $e3_cpl_reduction ),
			],
			'ergebnisse' => [
				'title'       => 'Ergebnisse & Case Studies | WordPress, SEO, CRO',
				'description' => 'Ergebnisse aus WordPress-, SEO-, Tracking- und CRO-Projekten: E3, DOMDAR und Whitelabel-Proof mit klarem naechsten Schritt.',
			],
			'growth-audit' => [
				'title'       => 'System-Diagnose für Solar- und Wärmepumpen-Anbieter | Haşim Üner',
				'description' => 'In 60 Sekunden sehen Sie, wo Ihre Website Anfragen verliert. Diagnose für Performance, Tracking, SEO und Content mit priorisierten Hebeln statt Tool-Score.',
			],
			'case-studies-e-commerce' => [
				'title'       => 'Ergebnisse & Case Studies | WordPress, SEO, CRO',
				'description' => 'Ergebnisse aus WordPress-, SEO-, Tracking- und CRO-Projekten: E3, DOMDAR und Whitelabel-Proof mit klarem naechsten Schritt.',
			],
			'case-studies' => [
				'title'       => 'Ergebnisse & Case Studies | WordPress, SEO, CRO',
				'description' => 'Ergebnisse aus WordPress-, SEO-, Tracking- und CRO-Projekten: E3, DOMDAR und Whitelabel-Proof mit klarem naechsten Schritt.',
			],
			'e3-new-energy' => [
				'title'       => hu_get_e3_methodology_case_title(),
				'description' => hu_get_e3_methodology_case_description(),
			],
			'case-e3' => [
				'title'       => hu_get_e3_methodology_case_title(),
				'description' => hu_get_e3_methodology_case_description(),
			],
			'audit-linkedin' => [
				'title'       => 'Kostenloses Website Audit für mehr Anfragen | Haşim Üner',
				'description' => 'Ich analysiere, wo Klarheit, Vertrauen, Struktur und Conversion-Logik auf deiner Website bremsen – mit fundierter Ersteinschätzung ohne Pflicht-Call.',
			],
			// 'wordpress-wartung-hannover' + 'wordpress-seo-hannover' + 'ki-integration-wordpress' entfernt:
			// /wordpress-seo-hannover/ und /wordpress-wartung-hannover/ sind 301 auf die Agentur-Page (Anker-Sektionen);
			// /ki-integration-wordpress/ ist noindex. Keine eigenständigen SEO-Signale mehr nötig.
			'solar-waermepumpen-leadgenerierung' => [
				'title'       => 'Solar & Wärmepumpen Leadgenerierung: Eigenes Anfrage-System statt Leads kaufen | Haşim Üner',
				'description' => sprintf( 'Leadgenerierung für Photovoltaik und Wärmepumpen: Bauen Sie ein eigenes Anfrage-System für exklusive Solar-Leads. Referenz E3 New Energy: %s Kosten pro Anfrage in 9 Monaten. Diagnose vor Umsetzung.', $e3_cpl_reduction ),
			],
			'website-fuer-solar-und-waermepumpen-anbieter' => [
				'title'       => 'Solar & Wärmepumpen Leadgenerierung: Eigenes Anfrage-System statt Leads kaufen | Haşim Üner',
				'description' => sprintf( 'Leadgenerierung für Photovoltaik und Wärmepumpen: Bauen Sie ein eigenes Anfrage-System für exklusive Solar-Leads. Referenz E3 New Energy: %s Kosten pro Anfrage in 9 Monaten. Diagnose vor Umsetzung.', $e3_cpl_reduction ),
			],
		]
	);
}

/**
 * Resolve forced SEO overrides for the current singular object.
 *
 * @param int $post_id Post ID.
 * @return array<string, string>
 */
function hu_get_forced_singular_seo( $post_id = 0 ) {
	$post_id = (int) $post_id;

	if ( $post_id <= 0 ) {
		$post_id = (int) get_queried_object_id();
	}

	if ( $post_id <= 0 ) {
		return [];
	}

	$slug = (string) get_post_field( 'post_name', $post_id );
	$map  = hu_get_forced_singular_seo_map();

	if ( empty( $map[ $slug ] ) || ! is_array( $map[ $slug ] ) ) {
		return [];
	}

	return [
		'title'       => isset( $map[ $slug ]['title'] ) ? trim( wp_strip_all_tags( (string) $map[ $slug ]['title'] ) ) : '',
		'description' => isset( $map[ $slug ]['description'] ) ? trim( wp_strip_all_tags( (string) $map[ $slug ]['description'] ) ) : '',
	];
}

/**
 * Build a compact branded title that stays within SERP-safe bounds.
 *
 * @param string $title      Raw title value.
 * @param string $brand      Brand suffix.
 * @param int    $max_length Target maximum length.
 * @return string
 */
function hu_build_compact_branded_title( $title, $brand = 'Haşim Üner', $max_length = 60 ) {
	$title = trim( wp_strip_all_tags( (string) $title ) );
	$brand = trim( wp_strip_all_tags( hu_normalize_brand_text( (string) $brand ) ) );

	if ( '' === $title ) {
		return $brand;
	}

	$separator       = ' | ';
	$available_title = max( 15, (int) $max_length - mb_strlen( $separator . $brand ) );

	if ( mb_strlen( $title ) > $available_title ) {
		$title = mb_substr( $title, 0, $available_title );
		$space = mb_strrpos( $title, ' ' );

		if ( false !== $space ) {
			$title = mb_substr( $title, 0, $space );
		}

		$title = rtrim( $title, " \t\n\r\0\x0B|:-" );
	}

	return trim( $title ) . $separator . $brand;
}

/**
 * Return the enforced SEO title for single blog posts.
 *
 * @param int $post_id Current post ID.
 * @return string
 */
function hu_get_post_title_pattern( $post_id ) {
	$post_id = (int) $post_id;

	if ( $post_id <= 0 ) {
		return 'Haşim Üner';
	}

	return hu_build_compact_branded_title( get_the_title( $post_id ) );
}

/**
 * Determine whether a stored SEO string still contains unresolved token syntax.
 *
 * @param mixed $value Raw SEO value.
 * @return bool
 */
function hu_seo_value_has_tokens( $value ) {
	if ( ! is_string( $value ) || '' === trim( $value ) ) {
		return false;
	}

	return (bool) preg_match( '/%[a-z0-9_-]+%/i', $value );
}

/**
 * Read an SEO value from ACF first, then from legacy post meta.
 *
 * Legacy post meta (e.g. rank_math_title, rank_math_description) may still
 * exist in the database from a previous plugin installation. New content
 * should use ACF fields exclusively.
 *
 * @param int    $post_id          Post ID.
 * @param string $acf_field        ACF field name.
 * @param string $legacy_meta_key  Legacy post meta key (e.g. former plugin data).
 * @return string
 */
function hu_get_stored_seo_value( $post_id, $acf_field, $legacy_meta_key = '' ) {
	$post_id = (int) $post_id;

	if ( $post_id <= 0 ) {
		return '';
	}

	if ( function_exists( 'get_field' ) && $acf_field ) {
		$acf_value = get_field( $acf_field, $post_id );
		if ( is_string( $acf_value ) && '' !== trim( $acf_value ) ) {
			return trim( wp_strip_all_tags( $acf_value ) );
		}
	}

	if ( ! $legacy_meta_key ) {
		return '';
	}

	$legacy_value = get_post_meta( $post_id, $legacy_meta_key, true );
	if ( ! is_string( $legacy_value ) || '' === trim( $legacy_value ) ) {
		return '';
	}

	if ( hu_seo_value_has_tokens( $legacy_value ) ) {
		return '';
	}

	return trim( wp_strip_all_tags( $legacy_value ) );
}



/**
 * Check whether current query is the SEO cornerstone article.
 *
 * @return bool
 */
function hu_is_seo_cornerstone_article() {
	if ( ! is_singular() ) {
		return false;
	}

	$post_id = get_queried_object_id();
	if ( ! $post_id ) {
		return false;
	}

	$slug = get_post_field( 'post_name', $post_id );
	return 'technisches-seo-performance-fundament' === $slug;
}



/**
 * Check whether current query is the audit offer page.
 *
 * @return bool
 */
function hu_is_audit_offer_page() {
	return function_exists( 'nexus_is_audit_page' ) && nexus_is_audit_page();
}

/**
 * Check whether current query is the contact request page.
 *
 * @return bool
 */
function hu_is_contact_offer_page() {
	return function_exists( 'nexus_is_contact_page' ) && nexus_is_contact_page();
}

/**
 * Check whether current query is the DOMDAR case-study page.
 *
 * @return bool
 */
function hu_is_domdar_case_study_page() {
	if ( ! is_singular() ) {
		return false;
	}

	$post_id = get_queried_object_id();
	if ( ! $post_id ) {
		return false;
	}

	$slug = get_post_field( 'post_name', $post_id );

	return in_array( $slug, [ 'case-study-domdar', 'domdar' ], true );
}

/**
 * Check whether a post object is the E3 methodology case.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function hu_is_e3_methodology_case_post( $post_id = 0 ) {
	$post_id = absint( $post_id );

	if ( $post_id <= 0 ) {
		$post_id = absint( get_queried_object_id() );
	}

	if ( $post_id <= 0 ) {
		return false;
	}

	$slug     = (string) get_post_field( 'post_name', $post_id );
	$template = (string) get_page_template_slug( $post_id );

	return in_array( $slug, [ 'e3-new-energy', 'case-e3' ], true )
		|| in_array( $template, [ 'page-e3-new-energy.php', 'page-case-e3.php' ], true );
}

/**
 * Check whether current query is the E3 methodology case.
 *
 * @return bool
 */
function hu_is_e3_methodology_case_page() {
	return is_singular() && hu_is_e3_methodology_case_post();
}

/**
 * Get the SEO title for the E3 methodology case.
 *
 * @return string
 */
function hu_get_e3_methodology_case_title() {
	return 'Methodik-Case E3 New Energy: Wie ein eigenes Anfrage-System Portal-Abhängigkeit ablöst';
}

/**
 * Get the SEO description for the E3 methodology case.
 *
 * @return string
 */
function hu_get_e3_methodology_case_description() {
	return 'Solar- und Wärmepumpen-Anbieter E3 New Energy hatte zwei parallele Lead-Quellen — gekaufte Portal-Leads und kostenlose Viessmann-Partner-Anfragen. Eine Methodik-Story über strukturelle Anfrage-Architektur.';
}

/**
 * Get the SEO title for the DOMDAR case study.
 *
 * @return string
 */
function hu_get_domdar_case_study_title() {
	return 'Case Study: DOMDAR | Sustainable Commerce | Haşim Üner';
}

/**
 * Get the SEO description for the DOMDAR case study.
 *
 * @return string
 */
function hu_get_domdar_case_study_description() {
	return 'Vom 54€ Warenkorb zur 120€ Profit-Maschine in 6 Wochen. Wie wir ohne Budget-Erhöhung die Conversion auf 4,6% steigerten.';
}

/**
 * Get the SEO title for the contact request page.
 *
 * @return string
 */
function hu_get_contact_offer_title() {
	return 'Kontakt für WordPress, SEO und CRO | Haşim Üner';
}

/**
 * Get the SEO description for the contact request page.
 *
 * @return string
 */
function hu_get_contact_offer_description() {
	return 'Projektanfrage für WordPress, SEO, Tracking und CRO: kurzer Einstieg, klare nächste Schritte und Rückmeldung für neue Projekte und bestehende Kunden.';
}



/**
 * Override the document title where an exact title string is required.
 *
 * @param string $title Existing title.
 * @return string
 */
function hu_pre_get_document_title_override( $title ) {
	if ( is_front_page() ) {
		return hu_get_homepage_title();
	}

	if ( is_home() ) {
		return hu_get_blog_archive_title();
	}

	$forced_seo = hu_get_forced_singular_seo();
	if ( ! empty( $forced_seo['title'] ) ) {
		return (string) $forced_seo['title'];
	}

	if ( function_exists( 'nexus_get_current_wgos_cluster_route_slug' ) ) {
		$cluster_defaults = nexus_get_wgos_cluster_page_seo_defaults( nexus_get_current_wgos_cluster_route_slug() );

		if ( ! empty( $cluster_defaults['title'] ) ) {
			return (string) $cluster_defaults['title'];
		}
	}

	if ( hu_is_contact_offer_page() ) {
		return hu_get_contact_offer_title();
	}

	if ( function_exists( 'nexus_is_audit_linkedin_page' ) && nexus_is_audit_linkedin_page() ) {
		return 'Kostenloses Website Audit für mehr Anfragen | Haşim Üner';
	}

	if ( hu_is_domdar_case_study_page() ) {
		$post_id   = get_queried_object_id();
		$seo_title = hu_get_stored_seo_value( $post_id, 'seo_title', 'rank_math_title' );

		return '' !== $seo_title ? $seo_title : hu_get_domdar_case_study_title();
	}

	if ( hu_is_seo_cornerstone_article() ) {
		return 'Technisches SEO + Performance Marketing: Fundament fehlt';
	}

	if ( is_singular( 'post' ) ) {
		return hu_get_post_title_pattern( get_queried_object_id() );
	}

	return $title;
}

/**
 * Override document titles when no SEO plugin takes over.
 *
 * @param array $parts Current title parts.
 * @return array
 */
function hu_document_title_overrides( $parts ) {
	if ( is_front_page() ) {
		$parts['title'] = hu_get_homepage_title();
		return $parts;
	}

	if ( is_home() ) {
		$parts['title'] = hu_get_blog_archive_title();
		return $parts;
	}

	$forced_seo = hu_get_forced_singular_seo();
	if ( ! empty( $forced_seo['title'] ) ) {
		$parts['title'] = (string) $forced_seo['title'];
		unset( $parts['page'] );
		return $parts;
	}

	if ( function_exists( 'nexus_get_current_wgos_cluster_route_slug' ) ) {
		$cluster_defaults = nexus_get_wgos_cluster_page_seo_defaults( nexus_get_current_wgos_cluster_route_slug() );

		if ( ! empty( $cluster_defaults['title'] ) ) {
			$parts['title'] = (string) $cluster_defaults['title'];
			unset( $parts['page'] );
			return $parts;
		}
	}

	if ( hu_is_seo_cornerstone_article() ) {
		$parts['title'] = 'Technisches SEO + Performance Marketing: Fundament fehlt';
		return $parts;
	}

	if ( is_singular( 'post' ) ) {
		$parts['title'] = hu_get_post_title_pattern( get_queried_object_id() );
		return $parts;
	}

	if ( hu_is_audit_offer_page() ) {
		$parts['title'] = 'Kostenlose System-Diagnose | Haşim Üner';
		return $parts;
	}

	if ( hu_is_contact_offer_page() ) {
		$parts['title'] = hu_get_contact_offer_title();
		return $parts;
	}

	if ( hu_is_domdar_case_study_page() ) {
		$post_id   = get_queried_object_id();
		$seo_title = hu_get_stored_seo_value( $post_id, 'seo_title', 'rank_math_title' );

		$parts['title'] = '' !== $seo_title ? $seo_title : hu_get_domdar_case_study_title();

		return $parts;
	}

	if ( is_singular() ) {
		$post_id    = get_queried_object_id();
		$slug       = $post_id ? get_post_field( 'post_name', $post_id ) : '';
		$seo_title  = hu_get_stored_seo_value( $post_id, 'seo_title', 'rank_math_title' );
		$defaults   = function_exists( 'nexus_get_wgos_cluster_page_seo_defaults' ) ? nexus_get_wgos_cluster_page_seo_defaults( get_post( $post_id ) ) : null;

		if ( '' !== $seo_title ) {
			$parts['title'] = $seo_title;
		} elseif ( ! empty( $defaults['title'] ) ) {
			$parts['title'] = (string) $defaults['title'];
		} elseif ( in_array( $slug, [ 'wgos', 'wordpress-growth-operating-system' ], true ) ) {
			$parts['title'] = 'WGOS - WordPress Wachstumssystem für messbare Nachfrage';
		}
	}

	return $parts;
}

/**
 * Return the effective singular SEO context for one post ID.
 *
 * This helper is reused by admin tooling like the SEO cockpit so the
 * backend can evaluate SEO state on the same basis as frontend output.
 *
 * @param int $post_id Post ID.
 * @return array<string, mixed>
 */
function hu_get_singular_post_seo_context( $post_id ) {
	$post_id = absint( $post_id );
	$post    = get_post( $post_id );

	if ( ! ( $post instanceof WP_Post ) ) {
		return [];
	}

	$forced           = hu_get_forced_singular_seo( $post_id );
	$cluster_defaults = function_exists( 'nexus_get_wgos_cluster_page_seo_defaults' ) ? nexus_get_wgos_cluster_page_seo_defaults( $post ) : null;
	$stored_title     = hu_get_stored_seo_value( $post_id, 'seo_title', 'rank_math_title' );
	$stored_desc      = hu_get_stored_seo_value( $post_id, 'seo_description', 'rank_math_description' );
	$title            = $stored_title;
	$description      = $stored_desc;
	$title_source     = '' !== $stored_title ? 'stored' : 'fallback';
	$desc_source      = '' !== $stored_desc ? 'stored' : 'fallback';

	if ( ! empty( $forced['title'] ) ) {
		$title        = (string) $forced['title'];
		$title_source = 'forced';
	} elseif ( '' === $title && 'post' === $post->post_type ) {
		$title        = hu_get_post_title_pattern( $post_id );
		$title_source = 'post_pattern';
	} elseif ( '' === $title && is_array( $cluster_defaults ) && ! empty( $cluster_defaults['title'] ) ) {
		$title        = (string) $cluster_defaults['title'];
		$title_source = 'cluster_default';
	} elseif ( '' === $title ) {
		$title = get_the_title( $post_id );
	}

	if ( ! empty( $forced['description'] ) ) {
		$description = (string) $forced['description'];
		$desc_source = 'forced';
	} elseif ( '' === $description && is_array( $cluster_defaults ) && ! empty( $cluster_defaults['description'] ) ) {
		$description = (string) $cluster_defaults['description'];
		$desc_source = 'cluster_default';
	} elseif ( '' === $description ) {
		$excerpt     = get_post_field( 'post_excerpt', $post_id );
		$description = '' !== trim( $excerpt ) ? wp_trim_words( wp_strip_all_tags( $excerpt ), 25, '…' ) : '';
	}

	$acf_noindex          = function_exists( 'get_field' ) ? get_field( 'seo_noindex', $post_id ) : false;
	$legacy_robots_meta   = get_post_meta( $post_id, 'rank_math_robots', true );
	$legacy_noindex       = is_array( $legacy_robots_meta ) ? in_array( 'noindex', $legacy_robots_meta, true ) : 'noindex' === $legacy_robots_meta;
	$noindex              = (bool) ( $acf_noindex || $legacy_noindex );
	$canonical            = hu_is_e3_methodology_case_post( $post_id ) ? home_url( '/e3-new-energy/' ) : (string) get_permalink( $post_id );

	return [
		'title'              => trim( wp_strip_all_tags( (string) $title ) ),
		'description'        => trim( wp_strip_all_tags( (string) $description ) ),
		'canonical'          => $canonical,
		'robots'             => $noindex ? 'noindex, nofollow' : 'index, follow',
		'noindex'            => $noindex,
		'title_source'       => $title_source,
		'description_source' => $desc_source,
	];
}

/**
 * Output SEO meta tags.
 *
 * Pluginlose Eigenimplementierung: Title, Description, OG Tags,
 * Twitter Card, Canonical und Robots.
 *
 * @return void
 */
function hu_seo_meta_tags() {

	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}

	$meta = hu_get_seo_meta();

	if ( empty( $meta ) ) {
		return;
	}

	// Meta Description
	if ( ! empty( $meta['description'] ) ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $meta['description'] ) );
	}

	// Canonical URL
	if ( ! empty( $meta['canonical'] ) ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $meta['canonical'] ) );
	}

	// Robots
	if ( ! empty( $meta['robots'] ) ) {
		printf( '<meta name="robots" content="%s">' . "\n", esc_attr( $meta['robots'] ) );
	}

	// Open Graph
	if ( ! empty( $meta['og_title'] ) ) {
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $meta['og_title'] ) );
	}
	if ( ! empty( $meta['description'] ) ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $meta['description'] ) );
	}
	if ( ! empty( $meta['canonical'] ) ) {
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $meta['canonical'] ) );
	}
	if ( ! empty( $meta['og_image'] ) ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $meta['og_image'] ) );
		echo '<meta property="og:image:width" content="1200">' . "\n";
		echo '<meta property="og:image:height" content="630">' . "\n";
	}
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $meta['og_type'] ) );
	echo '<meta property="og:locale" content="de_DE">' . "\n";
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );

	// Twitter Card
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	if ( ! empty( $meta['og_title'] ) ) {
		printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $meta['og_title'] ) );
	}
	if ( ! empty( $meta['description'] ) ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $meta['description'] ) );
	}
	if ( ! empty( $meta['og_image'] ) ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $meta['og_image'] ) );
	}
}

/**
 * Build SEO meta data array for the current page.
 *
 * Priority: ACF fields → Post/Page data → Auto-generated fallbacks.
 *
 * @return array{description: string, canonical: string, robots: string, og_title: string, og_image: string, og_type: string}
 */
function hu_get_seo_meta() {

	$meta = [
		'description' => '',
		'canonical'   => '',
		'robots'      => 'index, follow',
		'og_title'    => '',
		'og_image'    => '',
		'og_type'     => 'website',
	];

	// ── Utility-Seiten → noindex ──────────────────────────────────
	$noindex_templates = [
		'template-portal.php',
	];

	$noindex_slugs = [
		'danke',
		'danke-anfage-audit',
		'danke-anfrage-audit',
		'thank-you',
		'portal',
		'login',
		'kunden-login',
		'360-deep-dive',
		'readiness-diagnose',
		'anfrage-system-analyse',
	];

	if ( is_front_page() ) {
		$meta['og_title']    = hu_get_homepage_title();
		$meta['description'] = hu_get_homepage_description();
		$meta['canonical']   = home_url( '/' );

	} elseif ( is_home() ) {
		$meta['og_title']    = hu_get_blog_archive_title();
		$meta['description'] = hu_get_blog_archive_description();
		$meta['canonical']   = get_permalink( get_option( 'page_for_posts' ) );

	} elseif ( hu_is_contact_offer_page() ) {
		$meta['og_title']    = hu_get_contact_offer_title();
		$meta['description'] = hu_get_contact_offer_description();
		$meta['canonical']   = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' );

	} elseif ( function_exists( 'nexus_is_audit_linkedin_page' ) && nexus_is_audit_linkedin_page() ) {
		$meta['og_title']    = 'Kostenloses Website Audit für mehr Anfragen | Haşim Üner';
		$meta['description'] = 'Ich analysiere, wo Klarheit, Vertrauen, Struktur und Conversion-Logik auf deiner Website bremsen – mit fundierter Ersteinschätzung ohne Pflicht-Call.';
		$meta['canonical']   = function_exists( 'nexus_get_audit_linkedin_url' ) ? nexus_get_audit_linkedin_url() : home_url( '/audit-linkedin/' );
		$meta['og_image']    = function_exists( 'nexus_get_audit_linkedin_featured_image_url' ) ? nexus_get_audit_linkedin_featured_image_url() : content_url( '/uploads/2026/03/audit-linkedin-featured-1200x675-1.png' );
		$meta['robots']      = 'noindex, follow';

	} elseif ( function_exists( 'hu_is_request_analysis_request_path' ) && hu_is_request_analysis_request_path() ) {
		$meta['og_title']    = 'Anfrage-System-Analyse | Haşim Üner';
		$meta['description'] = 'Evidenzbasierte Analyse für Solar-, SHK- und Wärmepumpenbetriebe: Marktbild, Anfragepfad, Leadkosten-Korridor und Empfehlung für oder gegen ein eigenes Anfrage-System.';
		$meta['canonical']   = home_url( '/anfrage-system-analyse/' );
		$meta['robots']      = 'noindex, follow';

	} elseif ( function_exists( 'nexus_get_current_wgos_cluster_route_slug' ) && '' !== nexus_get_current_wgos_cluster_route_slug() ) {
		$cluster_slug        = nexus_get_current_wgos_cluster_route_slug();
		$cluster_defaults    = function_exists( 'nexus_get_wgos_cluster_page_seo_defaults' ) ? nexus_get_wgos_cluster_page_seo_defaults( $cluster_slug ) : null;
		$cluster_page        = function_exists( 'nexus_get_wgos_cluster_page' ) ? nexus_get_wgos_cluster_page( $cluster_slug ) : null;
		$meta['og_title']    = ! empty( $cluster_defaults['title'] ) ? (string) $cluster_defaults['title'] : '';
		$meta['description'] = ! empty( $cluster_defaults['description'] ) ? (string) $cluster_defaults['description'] : '';
		$meta['canonical']   = home_url( '/' . $cluster_slug . '/' );

		if ( empty( $meta['og_title'] ) && is_array( $cluster_page ) && ! empty( $cluster_page['title'] ) ) {
			$meta['og_title'] = (string) $cluster_page['title'];
		}

	} elseif ( is_singular() ) {
		$post_id  = get_queried_object_id();
		$template = get_page_template_slug( $post_id );
		$slug     = get_post_field( 'post_name', $post_id );
		$forced_seo = hu_get_forced_singular_seo( $post_id );

		// noindex check: Template/Slug-basiert, ACF-Feld oder Legacy-Meta
		$acf_noindex          = function_exists( 'get_field' ) ? get_field( 'seo_noindex', $post_id ) : false;
		$legacy_robots_meta   = get_post_meta( $post_id, 'rank_math_robots', true );
		$legacy_noindex       = is_array( $legacy_robots_meta ) ? in_array( 'noindex', $legacy_robots_meta, true ) : 'noindex' === $legacy_robots_meta;

		if ( in_array( $template, $noindex_templates, true ) || in_array( $slug, $noindex_slugs, true ) || $acf_noindex || $legacy_noindex ) {
			$meta['robots'] = 'noindex, nofollow';
		}

		// ACF fields first (if ACF Pro is active)
		$meta['description'] = hu_get_stored_seo_value( $post_id, 'seo_description', 'rank_math_description' );
		$meta['og_title']    = hu_get_stored_seo_value( $post_id, 'seo_title', 'rank_math_title' );
		$cluster_defaults    = function_exists( 'nexus_get_wgos_cluster_page_seo_defaults' ) ? nexus_get_wgos_cluster_page_seo_defaults( get_post( $post_id ) ) : null;

		if ( ! empty( $forced_seo['description'] ) ) {
			$meta['description'] = (string) $forced_seo['description'];
		}

		if ( ! empty( $forced_seo['title'] ) ) {
			$meta['og_title'] = (string) $forced_seo['title'];
		}

		if ( function_exists( 'get_field' ) ) {
			$og_image_arr        = get_field( 'og_image', $post_id );
			if ( is_array( $og_image_arr ) && ! empty( $og_image_arr['url'] ) ) {
				$meta['og_image'] = $og_image_arr['url'];
			} elseif ( is_string( $og_image_arr ) && $og_image_arr ) {
				$meta['og_image'] = $og_image_arr;
			}
		}

		// Fallbacks: auto-generate from post data
		if ( empty( $meta['og_title'] ) ) {
			if ( ! empty( $cluster_defaults['title'] ) ) {
				$meta['og_title'] = (string) $cluster_defaults['title'];
			} else {
				$meta['og_title'] = get_the_title( $post_id ) . ' · ' . get_bloginfo( 'name' );
			}
		}

		if ( 'technisches-seo-performance-fundament' === $slug ) {
			$meta['og_title'] = 'Technisches SEO + Performance Marketing: Fundament fehlt';
			$meta['description'] = 'Performance Marketing ohne technisches SEO-Fundament verbrennt Budget. So wirken Technik, CRO und Tracking zusammen - inklusive Entscheider-Checkliste.';
		}

		if ( hu_is_audit_offer_page() ) {
			$meta['og_title']    = 'System-Diagnose für Solar- und Wärmepumpen-Anbieter | Haşim Üner';
			$meta['description'] = 'In 60 Sekunden sehen Sie, wo Ihre Website Anfragen verliert. Diagnose für Performance, Tracking, SEO und Content mit priorisierten Hebeln statt Tool-Score.';
		}

		if ( hu_is_domdar_case_study_page() ) {
			$seo_title       = hu_get_stored_seo_value( $post_id, 'seo_title', 'rank_math_title' );
			$seo_description = hu_get_stored_seo_value( $post_id, 'seo_description', 'rank_math_description' );

			if ( '' === $seo_title ) {
				$meta['og_title'] = hu_get_domdar_case_study_title();
			}

			if ( '' === $seo_description ) {
				$meta['description'] = hu_get_domdar_case_study_description();
			}
		}

		if ( in_array( $slug, [ 'wgos', 'wordpress-growth-operating-system' ], true ) ) {
			if ( empty( $meta['og_title'] ) ) {
				$meta['og_title'] = 'WGOS - WordPress Wachstumssystem für messbare Nachfrage';
			}

			if ( empty( $meta['description'] ) ) {
				$meta['description'] = 'Das WordPress Growth Operating System verbindet Strategie, SEO, Tracking, Performance und Conversion zu einem strukturierten Nachfrage-System für Unternehmen.';
			}
		}

		if ( empty( $meta['description'] ) ) {
			if ( ! empty( $cluster_defaults['description'] ) ) {
				$meta['description'] = (string) $cluster_defaults['description'];
			}
		}

		if ( empty( $meta['description'] ) ) {
			$excerpt = get_the_excerpt( $post_id );
			if ( $excerpt ) {
				$meta['description'] = wp_trim_words( wp_strip_all_tags( $excerpt ), 25, '…' );
			}
		}

		if ( empty( $meta['og_image'] ) ) {
			$thumb = get_the_post_thumbnail_url( $post_id, 'large' );
			if ( $thumb ) {
				$meta['og_image'] = $thumb;
			}
		}

		// Canonical
		$meta['canonical'] = get_permalink( $post_id );

		// OG type
		if ( is_singular( 'post' ) ) {
			$meta['og_type'] = 'article';
		}

		if ( hu_is_e3_methodology_case_post( $post_id ) ) {
			$meta['og_title']    = hu_get_e3_methodology_case_title();
			$meta['description'] = hu_get_e3_methodology_case_description();
			$meta['canonical']   = home_url( '/e3-new-energy/' );
			$meta['og_type']     = 'article';
		}

	} elseif ( is_category() || is_tag() || is_tax() ) {

		$term = get_queried_object();
		if ( $term ) {
			$meta['og_title'] = single_term_title( '', false ) . ' · ' . get_bloginfo( 'name' );
			if ( $term->description ) {
				$meta['description'] = wp_trim_words( wp_strip_all_tags( $term->description ), 25, '…' );
			}
			$meta['canonical'] = get_term_link( $term );
		}

	} elseif ( is_search() ) {

		$meta['robots']   = 'noindex, follow';
		$meta['og_title'] = sprintf(
			/* translators: %s: search query */
			__( 'Suche: %s', 'blocksy-child' ),
			get_search_query()
		) . ' · ' . get_bloginfo( 'name' );

	} elseif ( is_404() ) {

		$meta['robots']   = 'noindex, follow';
		$meta['og_title'] = __( 'Seite nicht gefunden (404)', 'blocksy-child' ) . ' · ' . get_bloginfo( 'name' );
	}

	// Global OG-Image Fallback: Profilbild als Default wenn kein seitenspezifisches Bild gesetzt ist.
	if ( empty( $meta['og_image'] ) ) {
		$meta['og_image'] = hu_get_profile_image_url();
	}

	return $meta;
}

/**
 * Remove Blocksy's default canonical since we manage it ourselves.
 *
 * If a third-party SEO plugin is ever re-introduced, this guard prevents
 * duplicate canonical tags by only removing rel_canonical when no known
 * plugin is active.
 */
add_action( 'template_redirect', function () {
	if ( ! defined( 'WPSEO_VERSION' ) && ! defined( 'SEOPRESS_VERSION' ) ) {
		remove_action( 'wp_head', 'rel_canonical' );
	}
} );

/**
 * Slugs deprecated in der neuen Positionierung:
 * - wgos / wordpress-growth-operating-system: noindex (Legacy-Hub)
 * - ki-integration-wordpress / ki-integration: noindex (Legacy-Thema)
 * - loesungen: noindex (interne Angebotsübersicht, nicht mehr beworben)
 * - wordpress-seo-hannover / wordpress-wartung-hannover: 301 auf Agentur-Page-Anker
 *
 * @return array<int, string>
 */
function nexus_get_sitemap_excluded_slugs() {
	return [
		'wordpress-growth-operating-system',
		'wgos',
		'ki-integration-wordpress',
		'ki-integration',
		'loesungen',
		'readiness-diagnose',
		'anfrage-system-analyse',
		'wordpress-seo-hannover',
		'wordpress-wartung-hannover',
	];
}

/**
 * Resolve excluded slugs to page IDs once per request.
 *
 * @return array<int, int>
 */
function nexus_get_sitemap_excluded_ids() {
	static $ids = null;

	if ( null !== $ids ) {
		return $ids;
	}

	$ids = [];
	foreach ( nexus_get_sitemap_excluded_slugs() as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page instanceof WP_Post ) {
			$ids[] = (int) $page->ID;
		}
	}

	$ids = array_values( array_unique( $ids ) );
	return $ids;
}

/**
 * Exclude deprecated/noindex pages from WordPress core sitemap (wp-sitemap.xml).
 *
 * Verhindert Mischsignale: Sitemap-Eintrag ("crawl mich") vs. noindex-Header
 * oder 301-Redirect. Google würde sonst Crawl-Budget auf Dead-End-URLs verschwenden.
 */
add_filter( 'wp_sitemaps_posts_query_args', function ( $args, $post_type ) {
	if ( 'page' !== $post_type ) {
		return $args;
	}

	$excluded_ids = nexus_get_sitemap_excluded_ids();
	if ( empty( $excluded_ids ) ) {
		return $args;
	}

	$existing               = $args['post__not_in'] ?? [];
	$args['post__not_in']   = array_values( array_unique( array_merge( (array) $existing, $excluded_ids ) ) );

	return $args;
}, 10, 2 );

/**
 * Exclude deprecated/noindex pages from Rank Math sitemap (sitemap_index.xml).
 *
 * Rank Math erkennt noindex-Meta in der eigenen Settings-Oberfläche automatisch,
 * unser noindex wird aber per HTTP-Header + wp_head gesetzt — daher explizite
 * ID-basierte Exclusion als Safety-Net.
 */
add_filter( 'rank_math/sitemap/exclude_posts', function ( $excluded ) {
	$excluded = is_array( $excluded ) ? $excluded : [];
	$excluded = array_values( array_unique( array_merge( $excluded, nexus_get_sitemap_excluded_ids() ) ) );
	return $excluded;
} );
