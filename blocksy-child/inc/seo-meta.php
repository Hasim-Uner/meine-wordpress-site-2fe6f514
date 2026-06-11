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
		'Anfrage-Systeme für Solar & Wärmepumpe | Haşim Üner'
	);
}

/**
 * Return the enforced homepage SEO description.
 *
 * @return string
 */
function hu_get_homepage_description() {
	$cpl_before = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before', 'display', '150 €' ) : '150 €';
	$cpl_after  = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after', 'display', '22 €' ) : '22 €';

	return (string) apply_filters(
		'hu_homepage_seo_description',
		sprintf( 'Eigene Anfragen statt Portal-Leads: Marktcheck, Vorqualifizierung und Tracking für Solar- und Wärmepumpen-Anbieter. E3-Case: CPL von %s auf %s.', $cpl_before, $cpl_after )
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
		'Blog: Solar Leadgenerierung, SEO & CRO | Haşim Üner'
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
		'Analysen zu eigenen Anfrage-Systemen für Solar-/Wärmepumpen-Betriebe: Portal-Leads, SEO, Tracking, CRO und WordPress-Performance.'
	);
}

/**
 * Return SEO defaults for public category archives.
 *
 * @return array<string, array<string, string>>
 */
function hu_get_category_archive_seo_map() {
	return (array) apply_filters(
		'hu_category_archive_seo_map',
		[
			'solar-waermepumpen-anfrage-systeme' => [
				'title'       => 'Solar & SHK Anfrage-Systeme | Haşim Üner',
				'description' => 'Analysen zu eigenen Anfrage-Systemen für Solar-/SHK-Betriebe: Portal-Leads, Marktcheck, Lead-Qualität, Tracking und Conversion statt gemieteter Nachfrage.',
			],
			'sichtbarkeit-daten-conversion' => [
				'title'       => 'Sichtbarkeit, Daten & Conversion | Haşim Üner',
				'description' => 'SEO, Tracking, Core Web Vitals und CRO für Anfrage-Systeme: wie Sichtbarkeit belastbare Daten liefert und aus Traffic qualifizierte Anfragen entstehen.',
			],
			'wordpress-growth-agentur' => [
				'title'       => 'WordPress-Systeme & Anfrage-Architektur | Haşim Üner',
				'description' => 'WordPress als technische Basis für Anfrage-Systeme: Struktur, SEO, Performance, Tracking und CRO in der richtigen Reihenfolge.',
			],
			'markteinordnung' => [
				'title'       => 'Lead-Portal Markteinordnung | Haşim Üner',
				'description' => 'Schonungslose Einordnung von Lead-Portalen, Kostenlogik und Nachfrage-Miete für Solar- und SHK-Betriebe mit Fokus auf CPO statt CPL.',
			],
			'owned-leads' => [
				'title'       => 'Owned Leads statt Portal-Leads | Haşim Üner',
				'description' => 'Beiträge zu eigener Leadgenerierung, First-Party-Daten und Nachfrage-Infrastruktur: weniger Portal-Abhängigkeit, mehr Kontrolle über Anfragequalität.',
			],
			'seo' => [
				'title'       => 'Technisches SEO für Anfrage-Systeme | Haşim Üner',
				'description' => 'Technisches SEO für Anfrage-Systeme: Struktur, Indexierung, Content-Architektur und interne Links als Fundament für qualifizierte Anfragen.',
			],
			'tracking' => [
				'title'       => 'Tracking & Analytics für Anfrage-Systeme | Haşim Üner',
				'description' => 'GA4, Server-Side Tracking, Consent und CRM-Rückführung für Anfrage-Systeme: Daten, die Budgetsteuerung und Conversion-Lernen ermöglichen.',
			],
			'cro' => [
				'title'       => 'CRO & UX für qualifizierte Anfragen | Haşim Üner',
				'description' => 'Conversion-Optimierung für Anfrage-Systeme: Angebotslogik, Reibungsverluste, Proof, Formulare und klare nächste Schritte für qualifizierte Anfragen.',
			],
			'wordpress-performance' => [
				'title'       => 'WordPress Performance & CWV | Haşim Üner',
				'description' => 'WordPress Performance, Core Web Vitals, Hosting und Frontend-Bloat: Ladezeit als SEO-, Ads- und Conversion-Hebel für Anfrage-Systeme.',
			],
			'strategie' => [
				'title'       => 'Strategie für eigene Anfrage-Systeme | Haşim Üner',
				'description' => 'Strategische Beiträge zu eigenen Anfrage-Systemen: Zielmarkt, Angebot, Funnel-Logik, Budgetsteuerung und Priorisierung vor der Umsetzung.',
			],
		]
	);
}

/**
 * Resolve the effective SEO title and description for a category archive.
 *
 * @param WP_Term|null $term Category term.
 * @return array{title: string, description: string}
 */
function hu_get_category_archive_seo( $term = null ) {
	if ( null === $term ) {
		$term = get_queried_object();
	}

	if ( ! ( $term instanceof WP_Term ) || 'category' !== $term->taxonomy ) {
		return [
			'title'       => '',
			'description' => '',
		];
	}

	$map = hu_get_category_archive_seo_map();

	if ( ! empty( $map[ $term->slug ] ) && is_array( $map[ $term->slug ] ) ) {
		return [
			'title'       => isset( $map[ $term->slug ]['title'] ) ? trim( wp_strip_all_tags( (string) $map[ $term->slug ]['title'] ) ) : '',
			'description' => isset( $map[ $term->slug ]['description'] ) ? trim( wp_strip_all_tags( (string) $map[ $term->slug ]['description'] ) ) : '',
		];
	}

	$term_name        = trim( wp_strip_all_tags( (string) $term->name ) );
	$term_description = trim( wp_strip_all_tags( (string) $term->description ) );

	return [
		'title'       => hu_build_compact_branded_title( $term_name . ' Blog' ),
		'description' => '' !== $term_description
			? wp_trim_words( $term_description, 24, '…' )
			: sprintf( 'Analysen und Einordnungen zu %s: Sichtbarkeit, Daten, Conversion und eigene Anfrage-Systeme.', $term_name ),
	];
}

/**
 * Return forced SEO overrides for singular pages that must ignore legacy DB meta.
 *
 * @return array<string, array<string, string>>
 */
function hu_get_forced_singular_seo_map() {
	$e3_cpl_reduction = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_reduction', 'display', 'über 85 %' ) : 'über 85 %';
	$e3_cpl_before    = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before', 'display', '150 €' ) : '150 €';
	$e3_cpl_after     = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after', 'display', '22 €' ) : '22 €';

	return (array) apply_filters(
		'hu_forced_singular_seo_map',
		[
			'kontakt' => [
				'title'       => 'Kontakt & Projektanfrage | Haşim Üner',
				'description' => 'Projekt oder Frage kurz einordnen: ein Formular, händisch geprüfte Rückmeldung innerhalb von 48 Stunden. Kein Pflicht-Call, kein Vertriebsteam.',
			],
			'kontaktiere-mich' => [
				'title'       => 'Kontakt & Projektanfrage | Haşim Üner',
				'description' => 'Projekt oder Frage kurz einordnen: ein Formular, händisch geprüfte Rückmeldung innerhalb von 48 Stunden. Kein Pflicht-Call, kein Vertriebsteam.',
			],
			'uber-mich' => [
				'title'       => 'Über Haşim Üner | Solar-Anfrage-Systeme',
				'description' => 'Haşim Üner baut eigene Anfrage-Systeme für Solar- und Wärmepumpen-Anbieter: weg von gemieteten Portal-Leads, hin zu eigener Infrastruktur.',
			],
			// 'wgos' / 'wordpress-growth-operating-system' sowie Tool-/Audit-Legacy-Routen:
			// Seiten sind noindex, sitemap-excluded oder geschuetzte 301-Einstiege,
			// daher keine oeffentlichen Meta-Signale mehr.
			'wordpress-agentur-hannover' => [
				'title'       => 'WordPress Agentur Hannover für B2B-Anfragen',
				'description' => sprintf( 'WordPress Agentur Hannover für B2B-Anfragen: technisches SEO, Tracking, CRO. Erst Projektprüfung, dann Umsetzung — belegt am E3-Case (CPL %s → %s).', $e3_cpl_before, $e3_cpl_after ),
			],
			'wordpress-agentur' => [
				'title'       => 'WordPress Agentur Hannover für B2B-Anfragen',
				'description' => sprintf( 'WordPress Agentur Hannover für B2B-Anfragen: technisches SEO, Tracking, CRO. Erst Projektprüfung, dann Umsetzung — belegt am E3-Case (CPL %s → %s).', $e3_cpl_before, $e3_cpl_after ),
			],
			'ergebnisse' => [
				'title'       => 'Ergebnisse & Case Studies | WordPress, SEO, CRO',
				'description' => 'Ergebnisse aus öffentlich sichtbaren WordPress-, SEO-, Tracking- und CRO-Projekten: E3 New Energy, Systemlogik und konkrete nächste Schritte.',
			],
			'case-studies-e-commerce' => [
				'title'       => 'Ergebnisse & Case Studies | WordPress, SEO, CRO',
				'description' => 'Ergebnisse aus öffentlich sichtbaren WordPress-, SEO-, Tracking- und CRO-Projekten: E3 New Energy, Systemlogik und konkrete nächste Schritte.',
			],
			'case-studies' => [
				'title'       => 'Ergebnisse & Case Studies | WordPress, SEO, CRO',
				'description' => 'Ergebnisse aus öffentlich sichtbaren WordPress-, SEO-, Tracking- und CRO-Projekten: E3 New Energy, Systemlogik und konkrete nächste Schritte.',
			],
			'e3-new-energy' => [
				'title'       => hu_get_e3_methodology_case_title(),
				'description' => hu_get_e3_methodology_case_description(),
			],
			'case-e3' => [
				'title'       => hu_get_e3_methodology_case_title(),
				'description' => hu_get_e3_methodology_case_description(),
			],
			// 'wordpress-wartung-hannover' + 'wordpress-seo-hannover' + 'ki-integration-wordpress' entfernt:
			// alte Service-Slugs bleiben noindex/sitemap-excluded, aber ohne erzwungene 301-Pflicht;
			// /ki-integration-wordpress/ ist noindex. Keine eigenstaendigen SEO-Signale mehr noetig.
			'solar-waermepumpen-leadgenerierung' => [
				'title'       => 'Photovoltaik & Wärmepumpe: Leadgenerierung ohne Portale',
				'description' => sprintf( 'B2B-Leadgenerierung für Solar & Wärmepumpe: eigene qualifizierte Anfragen statt geteilter Portal-Leads. E3: %s niedrigerer CPL in 6 Monaten.', $e3_cpl_reduction ),
			],
			'website-fuer-solar-und-waermepumpen-anbieter' => [
				'title'       => 'Photovoltaik & Wärmepumpe: Leadgenerierung ohne Portale',
				'description' => sprintf( 'B2B-Leadgenerierung für Solar & Wärmepumpe: eigene qualifizierte Anfragen statt geteilter Portal-Leads. E3: %s niedrigerer CPL in 6 Monaten.', $e3_cpl_reduction ),
			],
			'solar-leads-kaufen-alternative' => [
				'title'       => 'Solar Leads kaufen? Alternative ohne Portal-Abhängigkeit',
				'description' => sprintf( 'Solar Leads kaufen oder eigene Anfragen aufbauen? Portal-Leads werden mehrfach verkauft. Der Vergleich pro Anfrage — E3-Case: %s niedrigerer CPL.', $e3_cpl_reduction ),
			],
			'server-side-tracking-b2b' => [
				'title'       => 'Server-Side Tracking für B2B-Leadgenerierung – DSGVO & CAPI',
				'description' => 'Server-Side Tracking für B2B-Anfrage-Systeme: GA4, Meta CAPI & Consent Mode v2 auf eigenem Server. Saubere Attribution trotz Cookieless und Ad-Blockern.',
			],
			'b2b-solar-leads' => [
				'title'       => 'B2B Solar Leads: gewerbliche PV-Projekte statt Masse',
				'description' => 'B2B-Leadgenerierung für gewerbliche Photovoltaik: Hallendächer, Quartierskonzepte, PPA. Eigenes Anfrage-System für Buying-Center, nicht Mengen-Leads.',
			],
			'eigene-leadgenerierung-vs-portale' => [
				'title'       => 'Portal-Leads vs. eigenes System: TCO-Vergleich Solar/SHK',
				'description' => sprintf( '24-Monats-TCO: Portal-Leads (DAA, Aroundhome, Check24) vs. eigenes Anfrage-System. E3: %s niedrigerer CPL in 6 Monaten.', $e3_cpl_reduction ),
			],
			'lead-funnel-solar' => [
				'title'       => 'Lead-Funnel Solar & Wärmepumpe – Aufbau für B2B-Marketing',
				'description' => 'Lead-Funnel-Architektur für Photovoltaik- und Wärmepumpen-Anbieter: vom Erstkontakt zur qualifizierten Anfrage. Vorqualifizierung, Lead-Scoring, CRM-Übergabe.',
			],
			'kunden-gewinnen-solarteure' => [
				'title'       => 'Kunden gewinnen für Solarteure – ohne Portal-Leads',
				'description' => sprintf( 'Wie Solarteure & Wärmepumpen-Anbieter systematisch Kunden gewinnen – ohne DAA, Aroundhome oder Check24. E3: %s niedrigerer CPL.', $e3_cpl_reduction ),
			],
			'cost-per-lead-photovoltaik' => [
				'title'       => 'Cost per Lead Photovoltaik: Was Solar-Anfragen wirklich kosten',
				'description' => sprintf( 'CPL-Rechnung für Photovoltaik- und Wärmepumpen-Anbieter: Portal-Leads vs. eigenes System. E3-Referenz: %s niedrigere Kosten pro Anfrage in 6 Monaten.', $e3_cpl_reduction ),
			],
			'qualifizierte-pv-anfragen' => [
				'title'       => 'Qualifizierte PV-Anfragen: 4 Merkmale guter Solar-Leads',
				'description' => 'Vier Merkmale einer qualifizierten Photovoltaik-Anfrage: Intent, Exklusivität, Vorqualifizierung, Echtzeit. Mit Praxisbezug E3 New Energy.',
			],
			'solar-leads-kosten-studie' => [
				'title'       => 'Was kosten Solar-Leads? Marktstudie DACH 2026 (CPL & CPO)',
				'description' => 'Marktstudie: Photovoltaik- & Wärmepumpen-Leads kosten 40–150 € — doch der Cost-per-Order liegt um ein Vielfaches höher. Preise je Modell, Methodik, Benchmark.',
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
 * Resolve image metadata for social previews.
 *
 * @param string $url           Image URL.
 * @param int    $attachment_id Optional attachment ID.
 * @param string $size          WordPress image size.
 * @return array{url: string, width: int, height: int, type: string}
 */
function hu_get_social_image_meta( $url = '', $attachment_id = 0, $size = 'full' ) {
	$url           = is_string( $url ) ? trim( $url ) : '';
	$attachment_id = absint( $attachment_id );
	$image         = [
		'url'    => $url,
		'width'  => 0,
		'height' => 0,
		'type'   => '',
	];

	if ( $attachment_id > 0 ) {
		$src = wp_get_attachment_image_src( $attachment_id, $size );

		if ( is_array( $src ) && ! empty( $src[0] ) ) {
			$image['url']    = (string) $src[0];
			$image['width']  = ! empty( $src[1] ) ? absint( $src[1] ) : 0;
			$image['height'] = ! empty( $src[2] ) ? absint( $src[2] ) : 0;
		}

		$mime_type = get_post_mime_type( $attachment_id );
		if ( is_string( $mime_type ) && '' !== $mime_type ) {
			$image['type'] = $mime_type;
		}
	}

	if ( '' !== $image['url'] && ( 0 === $image['width'] || 0 === $image['height'] || '' === $image['type'] ) ) {
		$resolved_id = function_exists( 'attachment_url_to_postid' ) ? absint( attachment_url_to_postid( $image['url'] ) ) : 0;

		if ( $resolved_id > 0 && $resolved_id !== $attachment_id ) {
			$resolved = hu_get_social_image_meta( $image['url'], $resolved_id, $size );
			$image    = array_merge(
				$image,
				array_filter(
					$resolved,
					static function ( $value ) {
						return '' !== $value && 0 !== $value;
					}
				)
			);
		}
	}

	if ( '' !== $image['url'] && '' === $image['type'] ) {
		$path      = (string) wp_parse_url( $image['url'], PHP_URL_PATH );
		$file_type = wp_check_filetype( $path );

		if ( ! empty( $file_type['type'] ) ) {
			$image['type'] = (string) $file_type['type'];
		}
	}

	return $image;
}

/**
 * Merge social image metadata into an SEO meta array.
 *
 * @param array<string, mixed> $meta  Current meta array.
 * @param array<string, mixed> $image Image metadata.
 * @return array<string, mixed>
 */
function hu_apply_social_image_meta( $meta, $image ) {
	if ( empty( $image['url'] ) ) {
		return $meta;
	}

	$meta['og_image'] = (string) $image['url'];

	if ( ! empty( $image['width'] ) ) {
		$meta['og_image_width'] = absint( $image['width'] );
	}

	if ( ! empty( $image['height'] ) ) {
		$meta['og_image_height'] = absint( $image['height'] );
	}

	if ( ! empty( $image['type'] ) ) {
		$meta['og_image_type'] = (string) $image['type'];
	}

	return $meta;
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
 * Return a pure @id reference to the canonical Person entity used as author.
 *
 * The full Person node is emitted site-wide by hu_output_schema(), so callers
 * only reference it by @id rather than repeating an inline Person object.
 *
 * @return array<string, string>
 */
function hu_get_canonical_author_person() {
	return [ '@id' => home_url( '/uber-mich/#person' ) ];
}

/**
 * Build a BreadcrumbList schema array for a Solar/SHK sub-page.
 *
 * @param string $current_url   Absolute current URL.
 * @param string $current_label Human-readable current page label.
 * @return array<string, mixed>
 */
function hu_get_solar_subpage_breadcrumb_schema( $current_url, $current_label ) {
	return [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'@id'             => trailingslashit( (string) $current_url ) . '#breadcrumb',
		'itemListElement' => [
			[
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'Startseite',
				'item'     => home_url( '/' ),
			],
			[
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => 'Solar & Wärmepumpen Leadgenerierung',
				'item'     => home_url( '/solar-waermepumpen-leadgenerierung/' ),
			],
			[
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => (string) $current_label,
				'item'     => (string) $current_url,
			],
		],
	];
}

/**
 * Return the canonical list of SEO sub-page paths in the Solar/SHK cluster.
 *
 * These pages are deeper content assets that route into the marktcheck on
 * /solar-waermepumpen-leadgenerierung/ and form the topical authority
 * cluster around the Solar money page. Used by the SEO cockpit to classify
 * them with a dedicated page role and priority score.
 *
 * @return array<int, string>
 */
function hu_get_solar_seo_subpage_paths() {
	static $cache = null;

	if ( null === $cache ) {
		$cache = [
			trailingslashit( '/solar-leads-kaufen-alternative' ),
			trailingslashit( '/server-side-tracking-b2b' ),
			trailingslashit( '/b2b-solar-leads' ),
			trailingslashit( '/eigene-leadgenerierung-vs-portale' ),
			trailingslashit( '/lead-funnel-solar' ),
			trailingslashit( '/kunden-gewinnen-solarteure' ),
			trailingslashit( '/cost-per-lead-photovoltaik' ),
			trailingslashit( '/qualifizierte-pv-anfragen' ),
			trailingslashit( '/solar-leads-kosten-studie' ),
		];
	}

	return $cache;
}

/**
 * Check whether a URL path belongs to the Solar SEO sub-page cluster.
 *
 * @param string $path Normalized URL path (with leading and trailing slash).
 * @return bool
 */
function hu_is_solar_seo_subpage_path( $path ) {
	$path = '/' === substr( (string) $path, -1 ) ? (string) $path : trailingslashit( (string) $path );

	return in_array( $path, hu_get_solar_seo_subpage_paths(), true );
}

/**
 * Return page templates that should stay noindex/nofollow.
 *
 * @return array<int, string>
 */
function hu_get_noindex_nofollow_templates() {
	return [
		'template-portal.php',
	];
}

/**
 * Return slugs that should stay noindex/nofollow because they are protected or private.
 *
 * @return array<int, string>
 */
function hu_get_noindex_nofollow_slugs() {
	return [
		'portal',
		'login',
		'kunden-login',
		'wgos',
		'wordpress-growth-operating-system',
	];
}

/**
 * Return public/legacy slugs that should be noindex but still pass link equity.
 *
 * @return array<int, string>
 */
function hu_get_noindex_follow_slugs() {
	return [
		'alle-loesungen',
		'alle-loesungen-im-detail',
		'anfrage',
		'audit-linkedin',
		'case-studies',
		'case-studies-e-commerce',
		'conversion-rate-optimization',
		'core-web-vitals',
		'danke',
		'danke-anfage-audit',
		'danke-anfrage-audit',
		'energie-fahrplan-demo',
		'thank-you',
		'kontaktiere-mich',
		'loesungen',
		'meta-ads',
		'360-deep-dive',
		'readiness-diagnose',
		'system-diagnose',
		'growth-audit',
		'audit',
		'customer-journey-audit',
		'360-audit',
		'wordpress-tech-audit',
		'kostenlose-tools',
		'tools',
		'website-performance-analyse',
		'website-fuer-solar-und-waermepumpen-anbieter',
		'wgos-systemlandkarte',
		'ki-integration-wordpress',
		'ki-integration',
		'wordpress-agentur',
		'wordpress-seo-hannover',
		'wordpress-wartung-hannover',
		'roi-rechner',
		'seo',
		'whitelabel',
		'whitelabel-retainer',
		'whitelabel-retainer-proof',
	];
}

/**
 * Resolve the effective robots directive for a singular post/page.
 *
 * @param int $post_id Post ID.
 * @return array{robots: string, noindex: bool}
 */
function hu_get_singular_robots_context( $post_id ) {
	$post_id = absint( $post_id );
	if ( $post_id <= 0 ) {
		return [
			'robots'  => 'index, follow',
			'noindex' => false,
		];
	}

	$template            = (string) get_page_template_slug( $post_id );
	$slug                = (string) get_post_field( 'post_name', $post_id );
	$acf_noindex         = function_exists( 'get_field' ) ? (bool) get_field( 'seo_noindex', $post_id ) : false;
	$legacy_robots_meta  = get_post_meta( $post_id, 'rank_math_robots', true );
	$legacy_noindex      = is_array( $legacy_robots_meta ) ? in_array( 'noindex', $legacy_robots_meta, true ) : 'noindex' === $legacy_robots_meta;
	$is_noindex_nofollow = in_array( $template, hu_get_noindex_nofollow_templates(), true )
		|| in_array( $slug, hu_get_noindex_nofollow_slugs(), true );
	$is_noindex_follow   = in_array( $slug, hu_get_noindex_follow_slugs(), true )
		|| $acf_noindex
		|| $legacy_noindex;

	if ( $is_noindex_nofollow ) {
		return [
			'robots'  => 'noindex, nofollow',
			'noindex' => true,
		];
	}

	if ( $is_noindex_follow ) {
		return [
			'robots'  => 'noindex, follow',
			'noindex' => true,
		];
	}

	return [
		'robots'  => 'index, follow',
		'noindex' => false,
	];
}

/**
 * Return a localized last-updated date for a template-driven sub-page.
 *
 * @param string $template_path Absolute filesystem path to the template file.
 * @return string ISO 8601 date string (YYYY-MM-DD) or empty string on failure.
 */
function hu_get_subpage_last_updated_iso( $template_path ) {
	$mtime = @filemtime( (string) $template_path );

	if ( ! $mtime ) {
		return '';
	}

	return gmdate( 'Y-m-d', (int) $mtime );
}

/**
 * Return a human-readable German last-updated label for a template-driven sub-page.
 *
 * @param string $template_path Absolute filesystem path to the template file.
 * @return string e.g. "17. Mai 2026" or empty string on failure.
 */
function hu_get_subpage_last_updated_label( $template_path ) {
	$mtime = @filemtime( (string) $template_path );

	if ( ! $mtime ) {
		return '';
	}

	$months = [
		1 => 'Januar', 2 => 'Februar', 3 => 'März', 4 => 'April', 5 => 'Mai', 6 => 'Juni',
		7 => 'Juli', 8 => 'August', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Dezember',
	];

	$day   = (int) gmdate( 'j', (int) $mtime );
	$month = (int) gmdate( 'n', (int) $mtime );
	$year  = (int) gmdate( 'Y', (int) $mtime );

	if ( ! isset( $months[ $month ] ) ) {
		return '';
	}

	return sprintf( '%d. %s %d', $day, $months[ $month ], $year );
}

/**
 * Get the SEO title for the E3 methodology case.
 *
 * @return string
 */
function hu_get_e3_methodology_case_title() {
	return 'E3 New Energy: Cost per Lead von 150 € auf 22 € – Solar-Case';
}

/**
 * Get the SEO description for the E3 methodology case.
 *
 * @return string
 */
function hu_get_e3_methodology_case_description() {
	return 'E3 New Energy senkte den CPL mit eigenem Anfrage-System statt Portal-Leads um über 85 %: 1.750+ qualifizierte PV- & Wärmepumpen-Anfragen, 12 % Abschluss.';
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
	return 'Kontakt & Projektanfrage | Haşim Üner';
}

/**
 * Get the SEO description for the contact request page.
 *
 * @return string
 */
function hu_get_contact_offer_description() {
	return 'Projekt oder Frage kurz einordnen: ein Formular, händisch geprüfte Rückmeldung innerhalb von 48 Stunden. Kein Pflicht-Call, kein Vertriebsteam.';
}



/**
 * Resolve the effective document title for all title filters.
 *
 * @return string
 */
function hu_get_resolved_document_title() {
	if ( is_front_page() ) {
		return hu_get_homepage_title();
	}

	if ( is_home() ) {
		return hu_get_blog_archive_title();
	}

	if ( is_category() ) {
		$category_seo = hu_get_category_archive_seo();
		if ( ! empty( $category_seo['title'] ) ) {
			return (string) $category_seo['title'];
		}
	}

	$forced_seo = hu_get_forced_singular_seo();
	if ( ! empty( $forced_seo['title'] ) ) {
		return (string) $forced_seo['title'];
	}

	if ( function_exists( 'nexus_get_current_wgos_cluster_route_slug' ) && function_exists( 'nexus_get_wgos_cluster_page_seo_defaults' ) ) {
		$cluster_slug     = nexus_get_current_wgos_cluster_route_slug();
		$cluster_defaults = '' !== $cluster_slug ? nexus_get_wgos_cluster_page_seo_defaults( $cluster_slug ) : null;

		if ( ! empty( $cluster_defaults['title'] ) ) {
			return (string) $cluster_defaults['title'];
		}
	}

	if ( hu_is_seo_cornerstone_article() ) {
		return 'Technisches SEO + Performance Marketing: Fundament fehlt';
	}

	if ( is_singular( 'post' ) ) {
		$post_id   = get_queried_object_id();
		$seo_title = hu_get_stored_seo_value( $post_id, 'seo_title', 'rank_math_title' );

		return '' !== $seo_title ? $seo_title : hu_get_post_title_pattern( $post_id );
	}

	if ( hu_is_audit_offer_page() ) {
		return 'Marktcheck mit Fit-Entscheid | Haşim Üner';
	}

	if ( hu_is_contact_offer_page() ) {
		return hu_get_contact_offer_title();
	}

	if ( hu_is_domdar_case_study_page() ) {
		$post_id   = get_queried_object_id();
		$seo_title = hu_get_stored_seo_value( $post_id, 'seo_title', 'rank_math_title' );

		return '' !== $seo_title ? $seo_title : hu_get_domdar_case_study_title();
	}

	if ( is_singular() ) {
		$post_id    = get_queried_object_id();
		$slug       = $post_id ? get_post_field( 'post_name', $post_id ) : '';
		$seo_title  = hu_get_stored_seo_value( $post_id, 'seo_title', 'rank_math_title' );
		$defaults   = function_exists( 'nexus_get_wgos_cluster_page_seo_defaults' ) ? nexus_get_wgos_cluster_page_seo_defaults( get_post( $post_id ) ) : null;

		if ( '' !== $seo_title ) {
			return $seo_title;
		} elseif ( ! empty( $defaults['title'] ) ) {
			return (string) $defaults['title'];
		} elseif ( in_array( $slug, [ 'wgos', 'wordpress-growth-operating-system' ], true ) ) {
			return 'WGOS Client Dashboard | Haşim Üner';
		}
	}

	return '';
}

/**
 * Override the document title where an exact title string is required.
 *
 * @param string $title Existing title.
 * @return string
 */
function hu_pre_get_document_title_override( $title ) {
	$resolved_title = hu_get_resolved_document_title();

	return '' !== $resolved_title ? $resolved_title : $title;
}

/**
 * Override document titles when no SEO plugin takes over.
 *
 * @param array $parts Current title parts.
 * @return array
 */
function hu_document_title_overrides( $parts ) {
	$resolved_title = hu_get_resolved_document_title();

	if ( '' !== $resolved_title ) {
		$parts['title'] = $resolved_title;
		unset( $parts['page'] );
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

	$robots_context       = hu_get_singular_robots_context( $post_id );
	$canonical            = hu_is_e3_methodology_case_post( $post_id ) ? home_url( '/e3-new-energy/' ) : (string) get_permalink( $post_id );

	return [
		'title'              => trim( wp_strip_all_tags( (string) $title ) ),
		'description'        => trim( wp_strip_all_tags( (string) $description ) ),
		'canonical'          => $canonical,
		'robots'             => $robots_context['robots'],
		'noindex'            => $robots_context['noindex'],
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
		if ( ! empty( $meta['og_image_width'] ) ) {
			printf( '<meta property="og:image:width" content="%d">' . "\n", absint( $meta['og_image_width'] ) );
		}
		if ( ! empty( $meta['og_image_height'] ) ) {
			printf( '<meta property="og:image:height" content="%d">' . "\n", absint( $meta['og_image_height'] ) );
		}
		if ( ! empty( $meta['og_image_type'] ) ) {
			printf( '<meta property="og:image:type" content="%s">' . "\n", esc_attr( $meta['og_image_type'] ) );
		}
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
 * @return array{description: string, canonical: string, robots: string, og_title: string, og_image: string, og_image_width: int, og_image_height: int, og_image_type: string, og_type: string}
 */
function hu_get_seo_meta() {

	$meta = [
		'description'     => '',
		'canonical'       => '',
		'robots'          => 'index, follow',
		'og_title'        => '',
		'og_image'        => '',
		'og_image_width'  => 0,
		'og_image_height' => 0,
		'og_image_type'   => '',
		'og_type'         => 'website',
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

	} elseif ( function_exists( 'hu_is_request_analysis_request_path' ) && hu_is_request_analysis_request_path() ) {
		$meta['og_title']    = 'Marktcheck | Haşim Üner';
		$meta['description'] = 'Manueller Marktcheck für Solar- und Wärmepumpen-Betriebe: händische Einordnung von Region, Vertrieb und Anfragequalität mit klarem Fit-Entscheid.';
		$meta['canonical']   = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
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

		$robots_context = hu_get_singular_robots_context( $post_id );
		$meta['robots'] = $robots_context['robots'];

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
			$og_image_arr = get_field( 'og_image', $post_id );
			if ( is_array( $og_image_arr ) && ! empty( $og_image_arr['url'] ) ) {
				$meta = hu_apply_social_image_meta(
					$meta,
					[
						'url'    => (string) $og_image_arr['url'],
						'width'  => isset( $og_image_arr['width'] ) ? absint( $og_image_arr['width'] ) : 0,
						'height' => isset( $og_image_arr['height'] ) ? absint( $og_image_arr['height'] ) : 0,
						'type'   => isset( $og_image_arr['mime_type'] ) ? (string) $og_image_arr['mime_type'] : '',
					]
				);

				if ( empty( $meta['og_image_width'] ) || empty( $meta['og_image_height'] ) ) {
					$meta = hu_apply_social_image_meta(
						$meta,
						hu_get_social_image_meta( (string) $og_image_arr['url'], absint( $og_image_arr['ID'] ?? 0 ), 'full' )
					);
				}
			} elseif ( is_string( $og_image_arr ) && $og_image_arr ) {
				$meta = hu_apply_social_image_meta( $meta, hu_get_social_image_meta( $og_image_arr ) );
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
			$meta['og_title']    = 'Technisches SEO + Performance Marketing: Fundament fehlt';
			$meta['description'] = 'Performance Marketing ohne technisches SEO-Fundament verbrennt Budget. So wirken Technik, CRO und Tracking zusammen - inklusive Entscheider-Checkliste.';
		}

		if ( hu_is_audit_offer_page() ) {
			$meta['og_title']    = 'Marktcheck für Solar- und Wärmepumpen-Anbieter | Haşim Üner';
			$meta['description'] = 'Manueller Marktcheck statt Software-Einheitsbrei: Region, Vertrieb und Anfragequalität händisch einordnen und den nächsten sinnvollen Schritt klären.';
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
				$meta['og_title'] = 'WGOS Client Dashboard | Haşim Üner';
			}

			if ( empty( $meta['description'] ) ) {
				$meta['description'] = 'Internes Client- und Delivery-Dashboard für berechtigte WGOS-Projekte.';
			}
		}

		if ( empty( $meta['description'] ) ) {
			if ( ! empty( $cluster_defaults['description'] ) ) {
				$meta['description'] = (string) $cluster_defaults['description'];
			}
		}

		// noindex pages need no public snippet; the auto-excerpt can surface
		// raw editor markup (e.g. inline CSS on thank-you pages).
		if ( empty( $meta['description'] ) && empty( $robots_context['noindex'] ) ) {
			$excerpt = get_the_excerpt( $post_id );
			if ( $excerpt ) {
				$meta['description'] = wp_trim_words( wp_strip_all_tags( $excerpt ), 25, '…' );
			}
		}

		if ( empty( $meta['og_image'] ) ) {
			$thumb_id = get_post_thumbnail_id( $post_id );
			if ( $thumb_id ) {
				$meta = hu_apply_social_image_meta( $meta, hu_get_social_image_meta( '', $thumb_id, 'large' ) );
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
		if ( $term instanceof WP_Term ) {
			if ( 'category' === $term->taxonomy ) {
				$category_seo        = hu_get_category_archive_seo( $term );
				$meta['og_title']    = $category_seo['title'];
				$meta['description'] = $category_seo['description'];
			} else {
				$meta['robots']  = 'noindex, follow';
				$meta['og_title'] = single_term_title( '', false ) . ' · ' . get_bloginfo( 'name' );
				if ( $term->description ) {
					$meta['description'] = wp_trim_words( wp_strip_all_tags( $term->description ), 25, '…' );
				}
			}

			$term_link = get_term_link( $term );
			if ( ! is_wp_error( $term_link ) ) {
				$meta['canonical'] = $term_link;
			}
		}

	} elseif ( is_author() || is_date() ) {

		$meta['robots']  = 'noindex, follow';
		$meta['og_title'] = wp_get_document_title();

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
		$meta = hu_apply_social_image_meta( $meta, hu_get_social_image_meta( hu_get_profile_image_url() ) );
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
 * - loesungen / alle-loesungen: noindex (interne Angebotsübersicht, nicht mehr beworben)
 * - energie-fahrplan-demo: Showroom/Legacy-Demo, kein aktiver Leadpfad
 * - case-studies* / Agentur- und Solar-Aliasse: 301 auf kanonische Ziele
 * - alte Service-, Tool- und ROI-Slugs: 410 Gone und aus Sitemap entfernen
 * - alte WGOS-Slugs: noindex/access-protected und aus Sitemap entfernen
 * - growth-audit und generische Audit-Aliasse: 301 auf Marktcheck
 * - whitelabel*: diskreter Akquisepfad, aber kein aktives SEO-Ziel
 *
 * @return array<int, string>
 */
function nexus_get_sitemap_excluded_slugs() {
	return array_values(
		array_unique(
			array_merge(
				hu_get_noindex_follow_slugs(),
				hu_get_noindex_nofollow_slugs()
			)
		)
	);
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
 * Keep native taxonomy sitemaps focused on curated category archives.
 *
 * Tags and ad-hoc taxonomies are intentionally noindex/follow unless they are
 * built as maintained landing pages with explicit SEO defaults.
 *
 * @param array<string, WP_Taxonomy> $taxonomies Public taxonomy objects.
 * @return array<string, WP_Taxonomy>
 */
function hu_filter_indexable_sitemap_taxonomies( $taxonomies ) {
	if ( ! is_array( $taxonomies ) ) {
		return $taxonomies;
	}

	foreach ( array_keys( $taxonomies ) as $taxonomy ) {
		if ( 'category' !== $taxonomy ) {
			unset( $taxonomies[ $taxonomy ] );
		}
	}

	return $taxonomies;
}
add_filter( 'wp_sitemaps_taxonomies', 'hu_filter_indexable_sitemap_taxonomies' );
