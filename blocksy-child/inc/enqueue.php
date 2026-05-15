<?php
/**
 * NEXUS Asset Management (CSS/JS Enqueue)
 *
 * Zentrale Registrierung aller Styles und Scripts.
 * Static asset versions on live-like environments, filemtime() only in local development.
 *
 * [Speed] Enqueue-Logik: Conditional Loading per Template/Seitentyp.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'hu_enqueue_assets', 20 );

/**
 * Enqueue all theme styles and scripts conditionally.
 *
 * @return void
 */
function hu_enqueue_assets() {
	$css_dir = get_stylesheet_directory() . '/assets/css/';
	$css_uri = get_stylesheet_directory_uri() . '/assets/css/';
	$js_dir  = get_stylesheet_directory() . '/assets/js/';
	$js_uri  = get_stylesheet_directory_uri() . '/assets/js/';
	$queried_id = get_queried_object_id();
	$is_seo_cornerstone_template = $queried_id && 'page-seo-cornerstone.php' === get_page_template_slug( $queried_id );
	$is_cluster_page = function_exists( 'nexus_is_wgos_cluster_page' ) && nexus_is_wgos_cluster_page();

	// ── Parent Theme ──────────────────────────────────────────────
	wp_enqueue_style(
		'blocksy-child-style',
		get_stylesheet_uri(),
		[],
		hu_get_asset_version( get_stylesheet_directory() . '/style.css' )
	);

	// ── GLOBAL: Design System (Single Source of Truth) ─────────────
	hu_enqueue_css( 'nexus-design-system', 'design-system.css', [ 'blocksy-child-style' ] );

	// ── GLOBAL: Custom Header ──────────────────────────────────────
	hu_enqueue_css( 'nexus-site-header-css', 'site-header.css', [ 'nexus-design-system' ] );

	if ( is_front_page() || is_page_template( 'template-about.php' ) ) {
		hu_enqueue_css( 'nexus-founding-cohort-css', 'founding-cohort.css', [ 'nexus-design-system' ] );
	}

	// ── GLOBAL: Core JS (Scroll-Spy, FAQ, Counter, Progress Bar) ──
	hu_enqueue_js( 'nexus-core-js', 'nexus-core.js' );
	hu_enqueue_js( 'nexus-site-header-js', 'site-header.js', [ 'nexus-core-js' ] );

	// ── GLOBAL: Legal Page Modal (Datenschutz / Impressum Overlay) ──
	if ( ! is_page( 'datenschutz' ) && ! is_page( 'impressum' ) ) {
		hu_enqueue_js( 'nexus-legal-modal-js', 'legal-modal.js' );
	}

	if ( is_front_page() && wp_script_is( 'ct-scripts', 'registered' ) ) {
		hu_mark_script_for_defer( 'ct-scripts' );
	}

	$calendar_embed = function_exists( 'nexus_get_audit_calendar_embed_config' ) ? nexus_get_audit_calendar_embed_config() : [];

	if ( ! empty( $calendar_embed['origin'] ) && ! empty( $calendar_embed['cal_link'] ) && ! empty( $calendar_embed['url'] ) ) {
		hu_enqueue_js( 'nexus-cal-embed-js', 'cal-embed.js', [ 'nexus-core-js' ] );
		wp_localize_script(
			'nexus-cal-embed-js',
			'NexusCalEmbedConfig',
			[
				'bookingUrl'     => esc_url_raw( (string) $calendar_embed['url'] ),
				'calOrigin'      => esc_url_raw( (string) $calendar_embed['origin'] ),
				'calLink'        => sanitize_text_field( (string) $calendar_embed['cal_link'] ),
				'bookingLinks'   => array_values(
					array_filter(
						array_map(
							static function ( $entry ) {
								if ( ! is_array( $entry ) ) {
									return null;
								}

								$url      = isset( $entry['url'] ) ? esc_url_raw( (string) $entry['url'] ) : '';
								$cal_link = isset( $entry['cal_link'] ) ? sanitize_text_field( (string) $entry['cal_link'] ) : '';

								if ( '' === $url || '' === $cal_link ) {
									return null;
								}

								return [
									'url'      => $url,
									'calLink'  => $cal_link,
								];
							},
							(array) ( $calendar_embed['booking_links'] ?? [] )
						)
					)
				),
				'namespace'      => 'nexus-audit-call',
				'embedScriptUrl' => esc_url_raw( 'https://app.cal.com/embed/embed.js' ),
			]
		);
	}


	// ── GLOBAL: Blog-Header Fallback (nicht auf Blog-Index — nutzt Site-Header) ──
	if ( ( is_archive() || is_singular( 'post' ) ) && ! is_home() ) {
		hu_enqueue_css( 'nexus-blog-header-css', 'blog-header.css', [ 'nexus-design-system' ] );
	}

	// ── GLOBAL: Blog Notify ────────────────────────────────────────
	if ( is_home() || is_archive() || is_singular( 'post' ) || ( function_exists( 'nexus_is_blog_notify_page' ) && nexus_is_blog_notify_page() ) ) {
		hu_enqueue_css( 'nexus-blog-notify-css', 'blog-notify.css', [ 'nexus-design-system' ] );
		hu_enqueue_js( 'nexus-blog-notify-js', 'blog-notify.js', [ 'nexus-core-js' ] );
		wp_localize_script(
			'nexus-blog-notify-js',
			'NexusBlogNotifyConfig',
			[
				'restEndpoint'   => esc_url_raw( rest_url( 'nexus/v1/blog-subscribe' ) ),
				'nonce'          => wp_create_nonce( 'nexus_blog_notify_subscribe' ),
				'successMessage' => 'Fast geschafft. Bitte bestätigen Sie Ihre Anmeldung über die E-Mail in Ihrem Postfach.',
				'errorMessage'   => 'Das hat gerade nicht funktioniert. Bitte prüfen Sie Ihre E-Mail-Adresse oder versuchen Sie es gleich noch einmal.',
			]
		);
	}

	// ── A) Startseite (homepage.css nur auf Front, nicht Blog-Index) ──
	if ( is_front_page() ) {
		hu_enqueue_css( 'nexus-home-css', 'homepage.css', [ 'nexus-design-system' ] );
		hu_enqueue_css( 'nexus-home-cro-css', 'homepage-cro.css', [ 'nexus-home-css' ] );
		hu_enqueue_css( 'nexus-home-redesign-css', 'homepage-redesign.css', [ 'nexus-home-css' ] );
		hu_enqueue_js( 'nexus-home-js', 'homepage.js', [ 'nexus-core-js' ] );
		hu_enqueue_js( 'nexus-home-cro-js', 'homepage-cro.js', [ 'nexus-home-js' ] );
		hu_enqueue_js( 'nexus-home-redesign-js', 'homepage-redesign.js', [ 'nexus-core-js' ] );
		hu_enqueue_js( 'nexus-home-mindmap-teaser-js', 'homepage-mindmap-teaser.js', [ 'nexus-home-js' ] );
		wp_localize_script(
			'nexus-home-mindmap-teaser-js',
			'NexusHomeMindmapConfig',
			[
				// WGOS ist deprecated in der neuen Positionierung — CTA-Exit auf den Marktcheck umgeleitet
				'wgosUrl' => function_exists( 'nexus_get_audit_url' )
					? nexus_get_audit_url()
					: home_url( '/kontakt/' ),
			]
		);
	}

	// ── B) Blog-Archive Scripts ───────────────────────────────────
	if ( is_home() ) {
		hu_enqueue_css( 'nexus-blog-archive-css', 'blog-archive.css', [ 'nexus-design-system' ] );
		hu_enqueue_js( 'nexus-archive-js', 'blog-archive.js', [ 'nexus-core-js' ] );
	}

	// ── C) Kategorie-Seiten (Pillar Hub) ──────────────────────────
	if ( is_category() ) {
		hu_enqueue_css( 'nexus-category-hub-css', 'category-hub.css', [ 'nexus-design-system' ] );
	}

	// ── C2) Sonstige Archiv-Seiten (Tag, Datum etc.) ──────────────
	if ( is_archive() && ! is_home() && ! is_category() ) {
		hu_enqueue_css( 'nexus-home-css', 'homepage.css', [ 'nexus-design-system' ] );
	}

	// ── D) Einzelbeitrag (Blog Post) ──────────────────────────────
	if ( is_singular( 'post' ) || $is_seo_cornerstone_template ) {
		hu_enqueue_css( 'nexus-single-css', 'single.css', [ 'nexus-design-system' ] );
		hu_enqueue_css( 'nexus-wgos-bridge-css', 'wgos-bridge.css', [ 'nexus-single-css' ] );
	}

	if ( is_singular( 'post' ) ) {
		hu_enqueue_css( 'nexus-related-content-css', 'related-content.css', [ 'nexus-design-system' ] );
		hu_enqueue_css( 'nexus-footer-cta-css', 'footer-cta.css', [ 'nexus-design-system' ] );
		hu_enqueue_js( 'nexus-blog-inline-cta-js', 'blog-inline-cta.js', [ 'nexus-core-js' ] );
	}

	if ( is_singular( 'post' ) || $is_seo_cornerstone_template ) {

		// Hide duplicate title from Blocksy (single posts + cornerstone template)
		wp_add_inline_style( 'blocksy-child-style', '
			.single-post .entry-header .entry-title,
			.single-post .ct-page-title,
			.page-template-page-seo-cornerstone .entry-header .entry-title,
			.page-template-page-seo-cornerstone .ct-page-title,
			.single-template-page-seo-cornerstone .entry-header .entry-title,
			.single-template-page-seo-cornerstone .ct-page-title {
				display: none !important;
			}
		' );
	}

	// ── E) Template: Über Mich ────────────────────────────────────
	if ( is_page_template( 'template-about.php' ) ) {
		hu_enqueue_css( 'nexus-about-css', 'about-page.css', [ 'nexus-design-system' ] );
	}

	// ── E2) Kontakt ───────────────────────────────────────────────
	if ( function_exists( 'nexus_is_contact_page' ) && nexus_is_contact_page() ) {
		hu_enqueue_css( 'nexus-contact-css', 'contact.css', [ 'nexus-design-system' ] );
		hu_enqueue_js( 'nexus-contact-js', 'contact.js', [ 'nexus-core-js' ] );
		$contact_requested_type = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : '';
		$contact_type_options   = function_exists( 'nexus_get_contact_request_type_options' ) ? nexus_get_contact_request_type_options() : [];
		$contact_is_scoped_landing = in_array( $contact_requested_type, [ 'audit', 'implementation', 'ongoing' ], true )
			&& isset( $contact_type_options[ $contact_requested_type ] );

		wp_localize_script(
			'nexus-contact-js',
			'NexusContactConfig',
			[
				'restEndpoint'     => esc_url_raw( rest_url( 'nexus/v1/contact-request' ) ),
				'successMessage'   => 'Danke. Ihre Anfrage ist eingegangen. Sie erhalten innerhalb von 24 Stunden eine Rückmeldung.',
				'errorMessage'     => 'Die Anfrage konnte gerade nicht gesendet werden. Bitte versuchen Sie es erneut.',
				'callUrl'          => esc_url_raw( function_exists( 'nexus_get_audit_calendar_url' ) ? nexus_get_audit_calendar_url() : home_url( '/kontakt/' ) ),
				'isScopedLanding'  => $contact_is_scoped_landing,
			]
		);
	}

	// ── F) Template: Agentur Service ──────────────────────────────
	if ( is_page_template( 'page-wordpress-agentur.php' ) || is_page_template( 'page-wordpress-agentur-hannover.php' ) || is_page( 'wordpress-agentur' ) || is_page( 'wordpress-agentur-hannover' ) ) {
		hu_enqueue_css( 'nexus-home-css', 'homepage.css', [ 'nexus-design-system' ] );
		hu_enqueue_css( 'nexus-contact-css', 'contact.css', [ 'nexus-design-system' ] );
		hu_enqueue_css( 'nexus-agentur-css', 'agentur.css', [ 'nexus-home-css', 'nexus-contact-css' ] );
		hu_enqueue_js( 'nexus-contact-js', 'contact.js', [ 'nexus-core-js' ] );

		wp_localize_script(
			'nexus-contact-js',
			'NexusContactConfig',
			[
				'restEndpoint'    => esc_url_raw( rest_url( 'nexus/v1/contact-request' ) ),
				'successMessage'  => 'Danke. Ihre Projektprüfung ist eingegangen. Sie erhalten innerhalb von 24 Stunden eine Rückmeldung.',
				'errorMessage'    => 'Die Anfrage konnte gerade nicht gesendet werden. Bitte versuchen Sie es erneut.',
				'callUrl'         => esc_url_raw( function_exists( 'nexus_get_audit_calendar_url' ) ? nexus_get_audit_calendar_url() : home_url( '/kontakt/' ) ),
				'isScopedLanding' => true,
			]
		);
	}

	// ── F1a) Solar-/Wärmepumpen-Leadgenerierung (SOLARA Redesign) ──
	// Eigene CSS/JS-Stack; legacy energy-systems / review-funnel werden hier
	// bewusst NICHT geladen, weil das Template ein eigenes Visual-System hat
	// und einen 60-Sek-Marktcheck im Hero rendert (REST → CRM).
	if ( is_page( 'solar-waermepumpen-leadgenerierung' ) || is_page_template( 'page-solar-waermepumpen-leadgenerierung.php' ) ) {
		hu_enqueue_css( 'nexus-solar-leadgen-solara-css', 'solar-leadgenerierung-solara.css', [ 'nexus-design-system' ] );
		hu_enqueue_js( 'nexus-solar-leadgen-solara-js', 'solar-leadgenerierung-solara.js', [ 'nexus-core-js' ] );

		$marktcheck_cfg = [
			'restEndpoint' => esc_url_raw( rest_url( 'nexus/v1/audit-request' ) ),
			'calcomUrl'    => function_exists( 'hu_get_analysis_calcom_base_url' )
				? hu_get_analysis_calcom_base_url()
				: 'https://cal.com/hasim-uener/30min?overlayCalendar=true',
			'diagnoseUrl'  => function_exists( 'hu_get_request_analysis_url' )
				? hu_get_request_analysis_url()
				: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ),
			'caseUrl'      => home_url( '/e3-new-energy/' ),
			'privacyUrl'   => home_url( '/datenschutz/' ),
			'pageUrl'      => function_exists( 'nexus_get_energy_systems_url' )
				? nexus_get_energy_systems_url()
				: home_url( '/solar-waermepumpen-leadgenerierung/' ),
		];
		wp_localize_script( 'nexus-solar-leadgen-solara-js', 'NexusMarktcheckConfig', $marktcheck_cfg );
	}

	// ── F1b) Schwester-Templates (E3-Case, Service-Landing) ────────
	if ( is_page( 'website-fuer-solar-und-waermepumpen-anbieter' ) || is_page( 'e3-new-energy' ) || is_page_template( 'page-website-fuer-solar-und-waermepumpen-anbieter.php' ) || is_page_template( 'page-e3-new-energy.php' ) || is_page_template( 'page-case-e3.php' ) ) {
		hu_enqueue_css( 'nexus-review-funnel-css', 'review-funnel.css', [ 'nexus-design-system' ] );
		hu_enqueue_css( 'nexus-energy-systems-css', 'energy-systems.css', [ 'nexus-review-funnel-css' ] );
		hu_enqueue_js( 'nexus-solar-hero-js', 'solar-hero.js', [ 'nexus-core-js' ] );
		hu_enqueue_js( 'nexus-review-funnel-js', 'review-funnel.js', [ 'nexus-core-js' ] );
		wp_localize_script(
			'nexus-review-funnel-js',
			'NexusReviewConfig',
			[
				'auditLabel'   => 'Marktcheck',
				'submitLabel'  => 'Marktcheck starten',
				'restEndpoint' => esc_url_raw( rest_url( 'nexus/v1/audit-request' ) ),
			]
		);
	}

	// ── G) Template: WGOS System ──────────────────────────────────
	if ( is_page_template( 'page-wgos.php' ) || is_page( 'wgos' ) || is_page( 'wordpress-growth-operating-system' ) ) {
		hu_enqueue_css( 'nexus-home-css', 'homepage.css', [ 'nexus-design-system' ] );
		hu_enqueue_css( 'nexus-wgos-css', 'wgos.css', [ 'nexus-home-css' ] );
		hu_enqueue_js( 'nexus-wgos-js', 'wgos.js', [ 'nexus-core-js' ] );
	}

	// ── G2) Template: WGOS Asset Hub ──────────────────────────────
	if ( is_page_template( 'page-wgos-assets.php' ) || is_page( 'wgos-systemlandkarte' ) || is_page( 'wgos-asset-hub' ) || is_page( 'systemlandkarte' ) ) {
		hu_enqueue_css( 'nexus-home-css', 'homepage.css', [ 'nexus-design-system' ] );
		hu_enqueue_css( 'nexus-wgos-css', 'wgos.css', [ 'nexus-home-css' ] );
		hu_enqueue_css( 'nexus-wgos-assets-css', 'wgos-assets.css', [ 'nexus-wgos-css' ] );
		hu_enqueue_js( 'nexus-wgos-js', 'wgos.js', [ 'nexus-core-js' ] );

		$explorer_path = get_stylesheet_directory() . '/assets/js/wgos-asset-explorer.js';

		if ( file_exists( $explorer_path ) ) {
			hu_enqueue_js( 'nexus-wgos-asset-explorer-js', 'wgos-asset-explorer.js', [ 'wp-element' ] );

			wp_add_inline_script(
				'nexus-wgos-asset-explorer-js',
				'window.WGOSAssetData = ' . wp_json_encode( nexus_get_wgos_asset_explorer_payload(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';' .
				'window.NexusWgosExplorerConfig = ' . wp_json_encode( [ 'links' => nexus_get_wgos_asset_explorer_links() ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';',
				'before'
			);
		}
	}

	// ── G3) Template: WGOS Asset Detail ───────────────────────────
	if ( is_singular( 'wgos_asset' ) ) {
		hu_enqueue_css( 'nexus-home-css', 'homepage.css', [ 'nexus-design-system' ] );
		hu_enqueue_css( 'nexus-wgos-css', 'wgos.css', [ 'nexus-home-css' ] );

		wp_add_inline_style(
			'blocksy-child-style',
			'
			.single-wgos_asset .entry-header .entry-title,
			.single-wgos_asset .ct-page-title {
				display: none !important;
			}
		'
		);
	}

	// ── G4) Template: Glossar Hub ─────────────────────────────────
	if ( function_exists( 'nexus_is_glossary_hub_page' ) && nexus_is_glossary_hub_page() ) {
		hu_enqueue_css( 'nexus-home-css', 'homepage.css', [ 'nexus-design-system' ] );
		hu_enqueue_css( 'nexus-wgos-css', 'wgos.css', [ 'nexus-home-css' ] );
		hu_enqueue_css( 'nexus-glossary-css', 'glossary.css', [ 'nexus-wgos-css' ] );
	}

	// ── G5) Template: Glossar Detail ──────────────────────────────
	if ( is_singular( 'glossary_term' ) ) {
		hu_enqueue_css( 'nexus-home-css', 'homepage.css', [ 'nexus-design-system' ] );
		hu_enqueue_css( 'nexus-wgos-css', 'wgos.css', [ 'nexus-home-css' ] );
		hu_enqueue_css( 'nexus-glossary-css', 'glossary.css', [ 'nexus-wgos-css' ] );

		wp_add_inline_style(
			'blocksy-child-style',
			'
			.single-glossary_term .entry-header .entry-title,
			.single-glossary_term .ct-page-title {
				display: none !important;
			}
		'
		);
	}

	// ── H) Template: System-Diagnose Funnel ──────────────────────────
	if ( nexus_is_audit_page() ) {
		wp_add_inline_style(
			'blocksy-child-style',
			'
			body.page-growth-audit .entry-header {
				display: none !important;
			}
		'
		);
	}

	// ── H-LI) Template: LinkedIn Audit Landing Page ──────────────
	if ( function_exists( 'nexus_is_audit_linkedin_page' ) && nexus_is_audit_linkedin_page() ) {
		hu_enqueue_css( 'nexus-audit-linkedin-css', 'audit-linkedin.css', [ 'nexus-design-system' ] );
		hu_enqueue_js( 'nexus-audit-linkedin-js', 'audit-linkedin.js', [ 'nexus-core-js' ] );
		wp_localize_script(
			'nexus-audit-linkedin-js',
			'NexusAuditLinkedInConfig',
			[
				'restEndpoint'  => esc_url_raw( rest_url( 'nexus/v1/audit-request' ) ),
				'errorMessage'  => 'Die Anfrage konnte gerade nicht gesendet werden. Bitte versuche es erneut.',
			]
		);
	}

	// ── H2) Template: 360° Deep-Dive ─────────────────────────────
	if ( is_page_template( 'page-360-deep-dive.php' ) || is_page( '360-deep-dive' ) ) {
		hu_enqueue_css( 'nexus-audit-results-css', 'audit-results.css', [ 'nexus-design-system' ] );
		hu_enqueue_css( 'nexus-deepdive-css', 'deep-dive.css', [ 'nexus-audit-results-css' ] );
	}

	// ── I) Template: SEO Landing (Hannover) ───────────────────────
	if ( ! $is_cluster_page && ( is_page_template( 'page-seo.php' ) || is_page( 'seo' ) || is_page( 'wordpress-seo-hannover' ) ) ) {
		hu_enqueue_css( 'nexus-seo-css', 'seo.css', [ 'nexus-design-system' ] );
		hu_enqueue_js( 'nexus-seo-js', 'seo.js', [ 'nexus-core-js' ] );
	}

	if ( $is_cluster_page ) {
		hu_enqueue_css( 'nexus-cluster-pillar-css', 'cluster-pillar.css', [ 'nexus-design-system' ] );
	}

	// ── I2) Template: SEO Cornerstone Artikel (Post + Page) ────────
	if ( $is_seo_cornerstone_template ) {
		hu_enqueue_css( 'nexus-seo-cornerstone-css', 'seo-cornerstone.css', [ 'nexus-single-css' ] );
	}

	// ── J) Template: Core Web Vitals ──────────────────────────────
	if ( ! $is_cluster_page && ( is_page_template( 'page-cwv.php' ) || is_page( 'core-web-vitals' ) || is_page( 'core-web-vitals-optimierung' ) ) ) {
		hu_enqueue_css( 'nexus-cwv-css', 'cwv.css', [ 'nexus-design-system' ] );
	}

	// ── K) Template: Performance Marketing ────────────────────────
	if ( ! $is_cluster_page && ( is_page_template( 'page-performance.php' ) || is_page( 'performance-marketing' ) ) ) {
		hu_enqueue_css( 'nexus-performance-css', 'performance.css', [ 'nexus-design-system' ] );
	}

	// ── L) Template: Conversion Rate Optimization (CRO) ──────────
	if ( ! $is_cluster_page && ( is_page_template( 'page-cro.php' ) || is_page( 'conversion-rate-optimization' ) ) ) {
		hu_enqueue_css( 'nexus-cro-css', 'cro.css', [ 'nexus-design-system' ] );
	}

	// ── M) Template: GA4 & Tracking Setup ─────────────────────────
	if ( ! $is_cluster_page && ( is_page_template( 'page-ga4.php' ) || is_page( 'ga4-tracking-setup' ) ) ) {
		hu_enqueue_css( 'nexus-ga4-css', 'ga4.css', [ 'nexus-design-system' ] );
	}

	// ── N) Template: Meta Ads (Facebook & Instagram) ──────────────
	if ( is_page_template( 'page-meta-ads.php' ) || is_page( 'meta-ads' ) ) {
		hu_enqueue_css( 'nexus-meta-ads-css', 'meta-ads.css', [ 'nexus-design-system' ] );
	}

	// ── P) Template: Ergebnisse Hub + Whitelabel Proof ─────────────
	if (
		is_page_template( 'page-case-studies-e-commerce.php' )
		|| is_page_template( 'page-whitelabel-retainer.php' )
		|| is_page( 'case-studies-e-commerce' )
		|| is_page( 'ergebnisse' )
		|| is_page( 'whitelabel-retainer' )
		|| is_page( 'whitelabel-retainer-proof' )
		|| is_page( 'whitelabel' )
	) {
		hu_enqueue_css( 'nexus-results-css', 'results.css', [ 'nexus-design-system' ] );
	}

	// ── Q) Template: Öffentliche Case Studies ──────────────────────
	if ( is_page_template( 'page-case-study-domdar.php' ) || is_page( 'case-study-domdar' ) ) {
		hu_enqueue_css( 'nexus-home-css', 'homepage.css', [ 'nexus-design-system' ] );
		hu_enqueue_css( 'nexus-case-study-css', 'case-study.css', [ 'nexus-home-css' ] );
	}
}

/**
 * Disable core block styles on the fully versioned homepage.
 *
 * The homepage is rendered from PHP templates and does not need the global
 * block library in its critical rendering path.
 *
 * @return void
 */
function hu_disable_core_block_styles_on_custom_templates() {
	if ( is_admin() ) {
		return;
	}

	if ( is_front_page() || is_home() ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
	}
}
add_action( 'wp_enqueue_scripts', 'hu_disable_core_block_styles_on_custom_templates', 100 );

/**
 * Return the active WordPress environment name for asset versioning.
 *
 * @return string
 */
function hu_get_asset_environment_type() {
	if ( function_exists( 'wp_get_environment_type' ) ) {
		return sanitize_key( (string) wp_get_environment_type() );
	}

	return 'production';
}

/**
 * Determine whether asset versions should stay dynamic for local work.
 *
 * @return bool
 */
function hu_should_use_dynamic_asset_versions() {
	return in_array( hu_get_asset_environment_type(), [ 'local', 'development' ], true );
}

/**
 * Return the static asset version used on live-like environments.
 *
 * `HU_THEME_VERSION` may be injected from `wp-config.php` or deployment
 * tooling. If it is absent, fall back to the child theme stylesheet version.
 *
 * @return string
 */
function hu_get_static_asset_version() {
	static $version = null;

	if ( null !== $version ) {
		return $version;
	}

	if ( defined( 'HU_THEME_VERSION' ) ) {
		$constant_version = trim( (string) HU_THEME_VERSION );

		if ( '' !== $constant_version ) {
			$version = $constant_version;

			return $version;
		}
	}

	$theme          = wp_get_theme( get_stylesheet() );
	$theme_version  = $theme instanceof WP_Theme ? trim( (string) $theme->get( 'Version' ) ) : '';
	$version        = '' !== $theme_version ? $theme_version : '1.0.0';

	return $version;
}

/**
 * Resolve the correct asset version for the current environment.
 *
 * Local and development keep `filemtime()` for zero-config cache busting.
 * Live-like environments use the static theme version and avoid per-request
 * filesystem I/O.
 *
 * @param string $path Absolute asset path.
 * @return string
 */
function hu_get_asset_version( $path ) {
	static $dynamic_versions = [];

	$path = (string) $path;

	if ( '' === $path || ! file_exists( $path ) ) {
		return hu_get_static_asset_version();
	}

	if ( ! hu_should_use_dynamic_asset_versions() ) {
		return hu_get_static_asset_version();
	}

	if ( isset( $dynamic_versions[ $path ] ) ) {
		return $dynamic_versions[ $path ];
	}

	$dynamic_versions[ $path ] = (string) filemtime( $path );

	return $dynamic_versions[ $path ];
}

/**
 * Helper: Enqueue a CSS file with environment-aware cache busting.
 *
 * @param string $handle  Stylesheet handle.
 * @param string $file    Filename inside assets/css/.
 * @param array  $deps    Dependencies.
 * @return void
 */
function hu_enqueue_css( $handle, $file, $deps = [] ) {
	$path = get_stylesheet_directory() . '/assets/css/' . $file;
	if ( ! file_exists( $path ) ) {
		return;
	}
	wp_enqueue_style(
		$handle,
		get_stylesheet_directory_uri() . '/assets/css/' . $file,
		$deps,
		hu_get_asset_version( $path )
	);
}

/**
 * Helper: Enqueue a JS file with environment-aware cache busting (footer, defer).
 *
 * @param string $handle  Script handle.
 * @param string $file    Filename inside assets/js/.
 * @param array  $deps    Dependencies.
 * @return void
 */
function hu_enqueue_js( $handle, $file, $deps = [] ) {
	$path = get_stylesheet_directory() . '/assets/js/' . $file;
	if ( ! file_exists( $path ) ) {
		return;
	}
	wp_enqueue_script(
		$handle,
		get_stylesheet_directory_uri() . '/assets/js/' . $file,
		$deps,
		hu_get_asset_version( $path ),
		true
	);

	hu_mark_script_for_defer( $handle );
}

/**
 * Return theme script handles that should stay blocking/immediate.
 *
 * Keep core boot logic and the global header runtime outside the defer path.
 *
 * @return array<int, string>
 */
function hu_get_non_deferred_script_handles() {
	$handles = apply_filters(
		'hu_non_deferred_script_handles',
		[
			'nexus-core-js',
			'nexus-site-header-js',
		]
	);

	$handles = array_filter(
		array_map(
			static function ( $handle ) {
				return sanitize_key( (string) $handle );
			},
			(array) $handles
		)
	);

	return array_values( array_unique( $handles ) );
}

/**
 * Return script handles that should be force-deferred for the current request.
 *
 * This is used for route-specific performance tuning where otherwise-blocking
 * scripts are safe to delay until after parsing.
 *
 * @return array<int, string>
 */
function hu_get_force_deferred_script_handles() {
	$handles = [];

	if ( is_front_page() ) {
		$handles = [
			'ct-scripts',
			'nexus-core-js',
			'nexus-site-header-js',
		];
	}

	$handles = apply_filters( 'hu_force_deferred_script_handles', $handles );
	$handles = array_filter(
		array_map(
			static function ( $handle ) {
				return sanitize_key( (string) $handle );
			},
			(array) $handles
		)
	);

	return array_values( array_unique( $handles ) );
}

/**
 * Check whether a theme script should receive defer.
 *
 * @param string $handle Script handle.
 * @return bool
 */
function hu_should_defer_script( $handle ) {
	$handle = sanitize_key( (string) $handle );

	if ( '' === $handle ) {
		return false;
	}

	if ( in_array( $handle, hu_get_force_deferred_script_handles(), true ) ) {
		return true;
	}

	if ( 0 !== strpos( $handle, 'nexus-' ) ) {
		return false;
	}

	return ! in_array( $handle, hu_get_non_deferred_script_handles(), true );
}

/**
 * Mark a script handle for deferred loading with a WordPress strategy plus fallback.
 *
 * @param string $handle Script handle.
 * @return void
 */
function hu_mark_script_for_defer( $handle ) {
	global $hu_deferred_script_handles;

	if ( ! hu_should_defer_script( $handle ) ) {
		return;
	}

	if ( ! is_array( $hu_deferred_script_handles ) ) {
		$hu_deferred_script_handles = [];
	}

	$hu_deferred_script_handles[] = sanitize_key( (string) $handle );
	$hu_deferred_script_handles   = array_values( array_unique( $hu_deferred_script_handles ) );

	wp_script_add_data( $handle, 'strategy', 'defer' );
}

/**
 * Helper: Enqueue a JS module file with environment-aware cache busting.
 *
 * @param string $handle Script handle.
 * @param string $file   Filename inside assets/js/.
 * @param array  $deps   Dependencies.
 * @return void
 */
function hu_enqueue_module_js( $handle, $file, $deps = [] ) {
	global $hu_module_script_handles;

	hu_enqueue_js( $handle, $file, $deps );

	if ( ! is_array( $hu_module_script_handles ) ) {
		$hu_module_script_handles = [];
	}

	$hu_module_script_handles[] = $handle;
}

/**
 * Print selected script handles as ES modules.
 *
 * @param string $tag    Original script tag.
 * @param string $handle Script handle.
 * @param string $src    Script source URL.
 * @return string
 */
function hu_filter_module_script_tag( $tag, $handle, $src ) {
	global $hu_deferred_script_handles, $hu_module_script_handles;

	if ( empty( $hu_module_script_handles ) || ! in_array( $handle, $hu_module_script_handles, true ) ) {
		if ( empty( $hu_deferred_script_handles ) || ! in_array( $handle, $hu_deferred_script_handles, true ) ) {
			return $tag;
		}

		if ( false !== strpos( $tag, ' defer' ) || false !== strpos( $tag, ' async' ) ) {
			return $tag;
		}

		return preg_replace( '/<script\b/', '<script defer', $tag, 1 ) ?: $tag;
	}

	return sprintf(
		'<script type="module" src="%1$s" id="%2$s-js"></script>' . "\n",
		esc_url( $src ),
		esc_attr( $handle )
	);
}
add_filter( 'script_loader_tag', 'hu_filter_module_script_tag', 10, 3 );
