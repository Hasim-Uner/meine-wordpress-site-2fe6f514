<?php
/**
 * Versioned service routes for tracking and performance marketing.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the versioned cluster-page definitions that replace editor drift.
 *
 * @return array<string, array<string, mixed>>
 */
function nexus_get_service_cluster_page_data() {
	static $pages = null;

	if ( null !== $pages ) {
		return $pages;
	}

	// Die Cluster wordpress-seo-hannover, core-web-vitals und conversion-rate-optimization
	// sind in die Agentur-Page integriert; 301-Redirects sitzen in inc/helpers.php
	// (nexus_redirect_legacy_offer_paths). Daten-Arrays wurden hier entfernt, damit
	// kein verwaister Konfigurations-Ballast in jedem Request initialisiert wird.
	$pages = [
		// page-ga4.php rendert die Seite selbst; das Register liefert Titel,
		// Auszug, FAQ (sichtbar und als FAQPage-Schema) und Meta-Vorgaben.
		'ga4-tracking-setup' => [
			'title'            => 'GA4 Tracking Setup für B2B-WordPress-Websites',
			'lead'             => 'GA4 Tracking Setup heißt hier: Event-Logik, Consent, GTM und serverseitige Signale so bauen, dass Sie Anfragen, Einstiegsseiten und Leadqualität belastbar sehen.',
			'faq_items'        => [
				[
					'question' => 'Wie richtet man ein sauberes GA4 Tracking Setup für eine B2B-Website ein?',
					'answer'   => 'Mit klaren Conversion-Zielen, einem Event-Blueprint, sauberem Consent-Verhalten, GTM-Struktur und einer Management-Sicht auf die relevanten Schritte. Ohne diese Reihenfolge bleibt GA4 schnell ein Datenarchiv statt einer Entscheidungsgrundlage.',
				],
				[
					'question' => 'Wann lohnt sich Server Side Tracking mit Google Tag Manager?',
					'answer'   => 'Wenn Browser-Signale wegbrechen, Consent das Bild verzieht oder Kampagnen und Leadquellen sauberer gemessen werden müssen. Server Side Tracking ist vor allem dann sinnvoll, wenn Datenqualität für operative Entscheidungen relevant wird.',
				],
				[
					'question' => 'Brauche ich nur GA4 oder zuerst ein Tracking Audit?',
					'answer'   => 'Wenn bereits Tags, Formulare oder mehrere Kanäle im Spiel sind, ist ein Tracking Audit meist der bessere Start. Es klärt, wo Daten fehlen, doppelt feuern oder falsch interpretiert werden, bevor neue Logik aufgebaut wird.',
				],
				[
					'question' => 'Ist das auch für Google Ads und Leadgenerierung relevant?',
					'answer'   => 'Ja. Ohne belastbare Messung bleiben Einstiegsseiten, Kampagnenqualität und Leadpfade unscharf. Gerade für B2B-Leadgenerierung ist ein sauberes GA4- und Tracking-Setup die Grundlage für sinnvolle Optimierung.',
				],
				[
					'question' => 'Brauche ich eine Agentur für das Google Analytics 4 Setup oder kann ich das selbst einrichten?',
					'answer'   => 'Einfache GA4-Installationen sind selbst machbar. Sobald Consent Mode, serverseitige Signalverarbeitung, Event-Blueprint und die Verknüpfung mit Formularen, Leadpfaden und Kampagnen ins Spiel kommen, lohnt sich eine erfahrene Begleitung. Fehler im Setup zeigen sich oft erst dann, wenn Entscheidungen auf falschen Daten aufbauen.',
				],
			],
			'meta_title'       => 'GA4 Tracking Setup für B2B | Haşim Üner',
			'meta_description' => 'GA4 Tracking Setup für B2B-WordPress: Google Analytics 4 einrichten, Consent Mode, GTM-Struktur und Server Side Tracking für belastbare Leadsignale.',
		],
		// Seit 2026-09-22 rendert page-performance.php die Seite selbst im
		// Gutachten-Layout. Das Register liefert nur noch Titel, Meta und FAQ,
		// damit sichtbare Fragen und FAQPage-Schema aus derselben Quelle kommen.
		'performance-marketing' => [
			'title'            => 'Performance Marketing',
			'faq_items'        => [
				[
					'question' => 'Übernehmen Sie auch nur die Kampagnen, ohne alles andere anzufassen?',
					'answer'   => 'Ja, wenn Messung und Zielseite bereits tragen. Wenn nicht, sage ich das vorher. Kampagnenbetreuung auf einem Setup, das falsche Conversions meldet, kostet Budget und liefert Daten, aus denen sich nichts ableiten lässt.',
				],
				[
					'question' => 'Warum zuerst das Tracking und nicht sofort mehr Budget?',
					'answer'   => 'Weil Google Ads und Meta auf das optimieren, was gemeldet wird. Zählt jedes Formular gleich, lernt das System, billige Kontakte einzukaufen statt passender Projektanfragen. Mehr Budget verstärkt diesen Fehler, es korrigiert ihn nicht.',
				],
				[
					'question' => 'Welche Kanäle betreuen Sie – und welche nicht?',
					'answer'   => 'Google Ads und Meta Ads im B2B-Kontext, dort wo sie an WordPress, Tracking und Conversion hängen. Nicht dabei: Mediaplanung für große Multi-Markt-Budgets, Kreativproduktion für Bewegtbild und Marktplatz-Werbung.',
				],
				[
					'question' => 'Was kostet das?',
					'answer'   => sprintf(
						'Die Messung beginnt mit %1$s für %2$s netto; Server-Side Tracking und Meta CAPI sind eigene Stufen darüber. Zielseite und Kampagnenbetreuung richten sich nach dem Umfang; Scope und Preis stehen vor dem Start schriftlich fest. Beschreiben Sie kurz die Ausgangslage, dann kommt eine konkrete Einschätzung zurück.',
						hu_tracking_product_ladder()['measurement']['name'],
						hu_tracking_price( 'measurement', 'setup' )
					),
				],
				[
					'question' => 'Arbeiten Sie auch für Performance-Agenturen?',
					'answer'   => 'Ja. Für Agenturen setze ich Tracking, Server-Side und Landingpages unter deren Namen um, mit Code und Zugängen in den Accounts der Agentur. Anfrage und Einstieg laufen über die White-Label-Seite.',
				],
			],
			'meta_title'       => 'Performance Marketing B2B: erst Messung, dann Budget',
			'meta_description' => 'Performance Marketing für B2B: erst Messung und Landingpage, dann Budget – damit Google Ads und Meta auf echte Anfragen optimieren, nicht auf Formular-Klicks.',
		],
	];

	return $pages;
}

/**
 * Resolve one cluster page by slug or current post.
 *
 * @param string|WP_Post|null $value Page slug or post object.
 * @return array<string, mixed>|null
 */
function nexus_get_service_cluster_page( $value = null ) {
	if ( null === $value ) {
		$value = get_post();

		if ( ! ( $value instanceof WP_Post ) && function_exists( 'nexus_get_current_service_cluster_route_slug' ) ) {
			$value = nexus_get_current_service_cluster_route_slug();
		}
	}

	if ( $value instanceof WP_Post ) {
		$value = $value->post_name;
	}

	$slug  = sanitize_title( (string) $value );
	$pages = nexus_get_service_cluster_page_data();

	return $pages[ $slug ] ?? null;
}

/**
 * Check whether the current request belongs to a versioned cluster page.
 *
 * @param string|WP_Post|null $value Page slug or post object.
 * @return bool
 */
function nexus_is_service_cluster_page( $value = null ) {
	return is_array( nexus_get_service_cluster_page( $value ) );
}

/**
 * Get SEO defaults for a cluster page.
 *
 * @param string|WP_Post|null $value Page slug or post object.
 * @return array<string, string>|null
 */
function nexus_get_service_cluster_page_seo_defaults( $value = null ) {
	$page = nexus_get_service_cluster_page( $value );

	if ( ! is_array( $page ) ) {
		return null;
	}

	return [
		'title'       => (string) ( $page['meta_title'] ?? '' ),
		'description' => (string) ( $page['meta_description'] ?? '' ),
	];
}

/**
 * Return FAQ entities for one cluster page.
 *
 * @param string|WP_Post|null $value Page slug or post object.
 * @return array<int, array<string, mixed>>
 */
function nexus_get_service_cluster_page_faq_entities( $value = null ) {
	$page = nexus_get_service_cluster_page( $value );

	if ( ! is_array( $page ) ) {
		return [];
	}

	$entities = [];

	foreach ( (array) ( $page['faq_items'] ?? [] ) as $item ) {
		$question = isset( $item['question'] ) ? trim( wp_strip_all_tags( (string) $item['question'] ) ) : '';
		$answer   = isset( $item['answer'] ) ? trim( wp_strip_all_tags( (string) $item['answer'] ) ) : '';

		if ( '' === $question || '' === $answer ) {
			continue;
		}

		$entities[] = [
			'@type'          => 'Question',
			'name'           => $question,
			'acceptedAnswer' => [
				'@type' => 'Answer',
				'text'  => $answer,
			],
		];
	}

	return $entities;
}

/**
 * Return the slug-to-template mapping for versioned cluster routes.
 *
 * @return array<string, string>
 */
function nexus_get_service_cluster_route_templates() {
	return [
		'ga4-tracking-setup'           => get_stylesheet_directory() . '/page-ga4.php',
		'performance-marketing'        => get_stylesheet_directory() . '/page-performance.php',
	];
}

/**
 * Return the active cluster slug for the current request path if available.
 *
 * @return string
 */
function nexus_get_current_service_cluster_route_slug() {
	if ( ! function_exists( 'nexus_get_current_request_path' ) ) {
		return '';
	}

	$request_path = nexus_get_current_request_path();

	foreach ( array_keys( nexus_get_service_cluster_route_templates() ) as $slug ) {
		if ( trailingslashit( '/' . $slug ) === $request_path ) {
			return $slug;
		}
	}

	return '';
}

/**
 * Prevent canonical redirects from fighting virtual cluster routes.
 *
 * @param string|false $redirect_url Redirect target.
 * @return string|false
 */
function nexus_disable_canonical_redirect_for_cluster_routes( $redirect_url ) {
	if ( '' !== nexus_get_current_service_cluster_route_slug() ) {
		return false;
	}

	return $redirect_url;
}
add_filter( 'redirect_canonical', 'nexus_disable_canonical_redirect_for_cluster_routes' );

/**
 * Turn cluster routes into virtual pages when no published page owns the slug.
 *
 * @param bool     $preempt  Existing preempt flag.
 * @param WP_Query $wp_query Current query.
 * @return bool
 */
function nexus_preempt_cluster_404( $preempt, $wp_query ) {
	if ( is_admin() || wp_doing_ajax() || ! ( $wp_query instanceof WP_Query ) ) {
		return $preempt;
	}

	$slug = nexus_get_current_service_cluster_route_slug();

	// `pre_handle_404` fires before WordPress marks the request as 404.
	// Virtual cluster routes must therefore key off the route slug alone.
	if ( '' === $slug ) {
		return $preempt;
	}

	$wp_query->is_404                = false;
	$wp_query->is_page               = true;
	$wp_query->is_singular           = true;
	$wp_query->is_home               = false;
	$wp_query->is_archive            = false;
	$wp_query->is_posts_page         = false;
	$wp_query->queried_object        = null;
	$wp_query->queried_object_id     = 0;
	$wp_query->query_vars['pagename'] = $slug;
	unset( $wp_query->query['error'], $wp_query->query_vars['error'] );

	status_header( 200 );

	return true;
}
add_filter( 'pre_handle_404', 'nexus_preempt_cluster_404', 10, 2 );

/**
 * Force key legacy routes onto versioned cluster templates.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function nexus_force_cluster_route_templates( $template ) {
	if ( is_admin() ) {
		return $template;
	}

	$current_slug     = nexus_get_current_service_cluster_route_slug();
	$route_templates  = nexus_get_service_cluster_route_templates();

	foreach ( $route_templates as $slug => $forced_template ) {
		if ( ( is_page( $slug ) || $current_slug === $slug ) && file_exists( $forced_template ) ) {
			return $forced_template;
		}
	}

	return $template;
}
add_filter( 'template_include', 'nexus_force_cluster_route_templates', 97 );

/**
 * Remove 404 body classes for virtual cluster routes.
 *
 * @param array<int, string> $classes Existing body classes.
 * @return array<int, string>
 */
function nexus_add_virtual_cluster_body_class( $classes ) {
	$slug = nexus_get_current_service_cluster_route_slug();

	if ( '' === $slug ) {
		return $classes;
	}

	$classes   = array_diff( $classes, [ 'error404' ] );
	$classes[] = 'page';
	$classes[] = 'page-' . sanitize_html_class( $slug );
	$classes[] = 'page-template-default';

	return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'nexus_add_virtual_cluster_body_class', 20 );

/**
 * Ensure versioned cluster routes exist as published WordPress pages.
 *
 * Virtual rendering stays active, but real published pages make native sitemap
 * output, URL resolution and admin-level SEO tooling align with the public URL.
 *
 * @return void
 */
function nexus_maybe_ensure_cluster_route_pages() {
	if ( ! nexus_route_pages_ensure_due() ) {
		return;
	}

	$route_templates = nexus_get_service_cluster_route_templates();
	$page_data       = nexus_get_service_cluster_page_data();

	foreach ( $route_templates as $slug => $template_path ) {
		$page_id  = 0;
		$existing = get_page_by_path( $slug );

		if ( $existing instanceof WP_Post ) {
			$page_id = (int) $existing->ID;

			if ( 'publish' !== (string) $existing->post_status ) {
				$updated_id = wp_update_post(
					wp_slash(
						[
							'ID'          => $page_id,
							'post_status' => 'publish',
						]
					),
					true
				);

				if ( is_wp_error( $updated_id ) ) {
					continue;
				}
			}
		} else {
			$definition = isset( $page_data[ $slug ] ) && is_array( $page_data[ $slug ] ) ? $page_data[ $slug ] : [];
			$title      = ! empty( $definition['title'] ) ? (string) $definition['title'] : ucwords( str_replace( '-', ' ', $slug ) );
			$excerpt    = ! empty( $definition['lead'] ) ? (string) $definition['lead'] : '';

			$page_id = wp_insert_post(
				wp_slash(
					[
						'post_type'    => 'page',
						'post_status'  => 'publish',
						'post_title'   => $title,
						'post_name'    => $slug,
						'post_content' => '',
						'post_excerpt' => $excerpt,
					]
				),
				true
			);

			if ( is_wp_error( $page_id ) ) {
				continue;
			}
		}

		$page_id = (int) $page_id;

		if ( $page_id <= 0 ) {
			continue;
		}

		update_post_meta( $page_id, '_wp_page_template', basename( $template_path ) );
		delete_post_meta( $page_id, 'seo_noindex' );
		delete_post_meta( $page_id, 'rank_math_robots' );

		$definition = isset( $page_data[ $slug ] ) && is_array( $page_data[ $slug ] ) ? $page_data[ $slug ] : [];
		$lead       = ! empty( $definition['lead'] ) ? (string) $definition['lead'] : '';

		if ( '' !== $lead && '' === trim( (string) get_post_field( 'post_excerpt', $page_id ) ) ) {
			wp_update_post(
				[
					'ID'           => $page_id,
					'post_excerpt' => $lead,
				]
			);
		}

		if ( '' === trim( (string) get_post_meta( $page_id, 'seo_title', true ) ) && ! empty( $definition['meta_title'] ) ) {
			update_post_meta( $page_id, 'seo_title', (string) $definition['meta_title'] );
		}

		if ( '' === trim( (string) get_post_meta( $page_id, 'seo_description', true ) ) && ! empty( $definition['meta_description'] ) ) {
			update_post_meta( $page_id, 'seo_description', (string) $definition['meta_description'] );
		}
	}
}
add_action( 'init', 'nexus_maybe_ensure_cluster_route_pages', 28 );
