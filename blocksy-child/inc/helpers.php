<?php
/**
 * NEXUS Helper Functions
 *
 * Wiederverwendbare Utility-Funktionen für das gesamte Theme.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calculate estimated reading time for a post.
 *
 * @param  int|null $post_id  Post ID (defaults to current post).
 * @param  int      $wpm      Words per minute.
 * @return int      Reading time in minutes (minimum 1).
 */
function nexus_get_reading_time( $post_id = null, $wpm = 200 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$content    = get_post_field( 'post_content', $post_id );
	$word_count = str_word_count( wp_strip_all_tags( $content ) );
	$minutes    = max( 1, (int) ceil( $word_count / $wpm ) );

	return $minutes;
}

/**
 * Get an ACF field with a safe fallback.
 *
 * Wraps get_field() so templates never break if ACF is inactive.
 *
 * @param  string     $field_name  ACF field name.
 * @param  mixed      $default     Default value if field is empty.
 * @param  int|false  $post_id     Post ID or false for current post.
 * @return mixed
 */
function nexus_get_field( $field_name, $default = '', $post_id = false ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $field_name, $post_id );
	return ( $value !== null && $value !== '' && $value !== false ) ? $value : $default;
}

/**
 * Return the default profile image URL (Profilbild).
 *
 * @return string Absolute URL.
 */
function hu_get_profile_image_url() {
	return home_url( '/wp-content/uploads/2024/10/Profilbild_Hasim-Uener.webp' );
}

/**
 * Return the portrait image URL (Startseite/Whitelabel variant).
 *
 * @return string Absolute URL.
 */
function hu_get_portrait_image_url() {
	return home_url( '/wp-content/uploads/2026/01/Hasim-Uener-Prtraeit_Startseite.webp' );
}

/**
 * Return the canonical public proof and vocabulary set for public pages.
 *
 * @return array<string, mixed>
 */
function nexus_get_public_proof_data() {
	static $proof = null;

	if ( null !== $proof ) {
		return $proof;
	}

	$e3_metrics = function_exists( 'hu_e3_canon' ) ? hu_e3_canon()['metrics'] : [];

	$proof = [
		'metrics'             => [
			'lead_count'       => [
				'value'   => $e3_metrics['lead_count']['display'] ?? '1.750',
				'label'   => 'Anfragen insgesamt',
				'context' => 'im aufgebauten System',
			],
			'sales_conversion' => [
				'value'   => $e3_metrics['sales_conversion']['display'] ?? '15 %',
				'label'   => 'Abschlussquote',
				'context' => 'unter vorqualifizierten CRM-Leads',
			],
			'cpl_reduction'    => [
				'value'   => $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %',
				'label'   => 'Kosten pro Anfrage',
				'context' => 'gegenüber Lead-Einkauf',
			],
		],
		'ownership_sentence'  => 'Code, Inhalte, Zugänge und Setups bleiben bei Ihnen. Laufende Zusammenarbeit bedeutet Weiterentwicklung, nicht Abhängigkeit.',
		'primary_term'        => 'Eigenes Anfragesystem für Solar- und Wärmepumpen-Anbieter',
		'framework_label'     => 'Anfragesystem-Methode',
		'framework_long'      => 'Methode für eigene Anfragesysteme',
		'allowed_subterms'    => [
			'Marktcheck',
			'priorisierte Folgeanalyse',
			'kontrollierte Weiterentwicklung',
			'planbare Growth-Kapazität',
		],
	];

	return $proof;
}

/**
 * Return selected canonical public proof metrics in display order.
 *
 * @param array<int, string> $keys Metric keys.
 * @return array<int, array<string, string>>
 */
function nexus_get_public_proof_metric_list( $keys ) {
	$proof   = nexus_get_public_proof_data();
	$metrics = isset( $proof['metrics'] ) && is_array( $proof['metrics'] ) ? $proof['metrics'] : [];
	$list    = [];

	foreach ( (array) $keys as $key ) {
		if ( isset( $metrics[ $key ] ) && is_array( $metrics[ $key ] ) ) {
			$list[] = $metrics[ $key ];
		}
	}

	return $list;
}

/**
 * Return the public ownership sentence used on kaufnahen Seiten.
 *
 * @return string
 */
function nexus_get_public_ownership_sentence() {
	$proof = nexus_get_public_proof_data();

	return isset( $proof['ownership_sentence'] ) ? (string) $proof['ownership_sentence'] : '';
}

/**
 * Return the primary public market category.
 *
 * @return string
 */
function nexus_get_public_primary_term() {
	$proof = nexus_get_public_proof_data();

	return isset( $proof['primary_term'] ) ? (string) $proof['primary_term'] : '';
}

/**
 * Return the expanded public framework label.
 *
 * @return string
 */
function nexus_get_public_framework_label() {
	$proof = nexus_get_public_proof_data();

	return isset( $proof['framework_label'] ) ? (string) $proof['framework_label'] : '';
}

/**
 * Return the canonical entry CTA copy for the solar marketcheck.
 *
 * Keep purchase-intent copy in one place so the primary CTA label and its
 * supporting promise stay aligned across key surfaces.
 *
 * @return array<string, mixed>
 */
function nexus_get_audit_cta_copy() {
	static $copy = null;

	if ( null !== $copy ) {
		return $copy;
	}

	$copy = [
		'label'             => 'Marktcheck mit Fit-Entscheid starten',
		'compact_microcopy' => 'Händische Analyse · Befund per E-Mail, ' . hu_marketcheck_reply_label( true ),
		'footer_note'       => 'Marktcheck: Manueller, tiefer Marktcheck statt Software-Einheitsbrei. Händische Analyse deiner Region — ' . hu_marketcheck_reply_label() . ', per E-Mail.',
	];

	return $copy;
}

/**
 * Return the canonical primary CTA label for the marketcheck.
 *
 * @return string
 */
function nexus_get_audit_cta_label() {
	$copy = nexus_get_audit_cta_copy();

	return isset( $copy['label'] ) ? (string) $copy['label'] : 'Marktcheck mit Fit-Entscheid starten';
}

/**
 * Return the compact marketcheck CTA microcopy.
 *
 * @return string
 */
function nexus_get_audit_compact_microcopy() {
	$copy = nexus_get_audit_cta_copy();

	return isset( $copy['compact_microcopy'] ) ? (string) $copy['compact_microcopy'] : '';
}

/**
 * Return the full marketcheck footer note.
 *
 * @return string
 */
function nexus_get_audit_footer_note() {
	$copy = nexus_get_audit_cta_copy();

	return isset( $copy['footer_note'] ) ? (string) $copy['footer_note'] : '';
}

/**
 * Inline up-right arrow for "this leads somewhere" affordances.
 *
 * ↗ (U+2197) has Emoji_Presentation fallbacks in the system font stacks of
 * macOS and iOS: statt eines Pfeils erscheint ein blau-weisser Kasten, im
 * orangenen Header-CTA besonders auffaellig. Ein Variation Selector wuerde das
 * fuer die meisten, aber nicht fuer alle Kombinationen aus Schrift und
 * Systemversion loesen. Inline-SVG nimmt die Schrift ganz aus der Gleichung,
 * erbt currentColor und traegt damit Hover-, Fokus- und Theme-Zustaende
 * unveraendert weiter.
 *
 * Das Markup ist dekorativ: aria-hidden, focusable=false. Umgebende Container
 * behalten ihre Groesse und Zentrierung.
 *
 * @param int $size Edge length in pixels.
 * @return string
 */
function hu_arrow_up_right_svg( $size = 14 ) {
	return sprintf(
		'<svg class="hu-arrow-svg" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 17 17 7"/><path d="M8 7h9v9"/></svg>',
		(int) $size
	);
}

/**
 * Shared opening for the closing Marktcheck section on the intercept routes.
 *
 * Six Seiten öffneten ihren Final-CTA mit demselben Satz und hängten nur eine
 * seitenspezifische Zusage an. Die geteilte Hälfte steht hier, damit die
 * Antwortzeit nicht wieder auf sechs Seiten einzeln gepflegt wird.
 *
 * @return string
 */
function nexus_get_marketcheck_final_lead_in() {
	$reply = hu_marketcheck_reply_label();

	// Kein Gedankenstrich am Ende: die aufrufenden Seiten haengen ihre eigene
	// Zusage mit einem an. Zwei Striche in einem Satz lesen sich holprig.
	return sprintf(
		'Manueller, tiefer Marktcheck statt Software-Einheitsbrei. Händische Analyse Ihrer Region per E-Mail, %s',
		$reply
	);
}

/**
 * Render a tracking-ready CTA button.
 *
 * Outputs an <a> element with proper data-track-* attributes
 * for GTM Server-Side event capture.
 *
 * @param array $args {
 *     @type string $url       Link URL.
 *     @type string $text      Button text.
 *     @type string $action    Tracking action name (e.g. 'cta_audit_hero').
 *     @type string $category  Tracking category (default: 'lead_gen').
 *     @type string $class     CSS classes (default: 'btn btn-primary').
 *     @type bool   $new_tab   Open in new tab (default: false).
 * }
 * @return string HTML.
 */
function nexus_cta_button( $args = [] ) {
	$defaults = [
		'url'      => '#',
		'text'     => __( 'Jetzt starten', 'blocksy-child' ),
		'action'   => 'cta_generic',
		'category' => 'lead_gen',
		'class'    => 'btn btn-primary',
		'new_tab'  => false,
	];

	$args   = wp_parse_args( $args, $defaults );
	$target = $args['new_tab'] ? ' target="_blank" rel="noopener noreferrer"' : '';

	return sprintf(
		'<a href="%s" class="%s" data-track-action="%s" data-track-category="%s"%s>%s</a>',
		esc_url( $args['url'] ),
		esc_attr( $args['class'] ),
		esc_attr( $args['action'] ),
		esc_attr( $args['category'] ),
		$target,
		esc_html( $args['text'] )
	);
}

/**
 * Truncate a string to a maximum length at word boundaries.
 *
 * @param  string $text    Input text.
 * @param  int    $length  Maximum character length.
 * @param  string $suffix  Suffix to append when truncated.
 * @return string
 */
function nexus_truncate( $text, $length = 155, $suffix = '…' ) {
	$text = wp_strip_all_tags( $text );

	if ( mb_strlen( $text ) <= $length ) {
		return $text;
	}

	$truncated = mb_substr( $text, 0, $length );
	$last_space = mb_strrpos( $truncated, ' ' );

	if ( $last_space !== false ) {
		$truncated = mb_substr( $truncated, 0, $last_space );
	}

	return $truncated . $suffix;
}

/**
 * Get theme asset URL (shortcut for frequent use).
 *
 * @param  string $path Relative path within /assets/.
 * @return string Full URL.
 */
function nexus_asset_url( $path ) {
	return get_stylesheet_directory_uri() . '/assets/' . ltrim( $path, '/' );
}

/**
 * Get theme asset absolute path.
 *
 * @param  string $path Relative path within /assets/.
 * @return string Absolute file path.
 */
function nexus_asset_path( $path ) {
	return get_stylesheet_directory() . '/assets/' . ltrim( $path, '/' );
}

/**
 * Resolve a page ID from one or more possible slugs.
 *
 * @param string|array $paths Candidate page paths, ordered by preference.
 * @return int
 */
function nexus_get_page_id( $paths ) {
	$paths = (array) $paths;

	foreach ( $paths as $path ) {
		$page = get_page_by_path( trim( (string) $path, '/' ) );
		if ( $page instanceof WP_Post ) {
			return (int) $page->ID;
		}
	}

	return 0;
}

/**
 * Resolve a page ID by assigned page template.
 *
 * Cached per request so repeated CTA helpers stay cheap.
 *
 * @param string $template Page template filename.
 * @return int
 */
function nexus_get_page_id_by_template( $template ) {
	static $cache = [];

	$template = (string) $template;

	if ( isset( $cache[ $template ] ) ) {
		return $cache[ $template ];
	}

	$page_ids = get_posts(
		[
			'post_type'              => 'page',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'orderby'                => 'menu_order title',
			'order'                  => 'ASC',
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_key'               => '_wp_page_template',
			'meta_value'             => $template,
		]
	);

	$cache[ $template ] = ! empty( $page_ids ) ? (int) $page_ids[0] : 0;

	return $cache[ $template ];
}

/**
 * Resolve a permalink from one or more possible slugs with a sane fallback.
 *
 * @param string|array $paths    Candidate page paths, ordered by preference.
 * @param string       $fallback Optional fallback URL.
 * @return string
 */
function nexus_get_page_url( $paths, $fallback = '' ) {
	$paths   = (array) $paths;
	$page_id = nexus_get_page_id( $paths );

	if ( $page_id ) {
		return get_permalink( $page_id );
	}

	if ( $fallback ) {
		return $fallback;
	}

	if ( ! empty( $paths ) ) {
		return home_url( '/' . trim( (string) $paths[0], '/' ) . '/' );
	}

	return home_url( '/' );
}

/**
 * Resolve a category archive URL by slug with a deterministic fallback.
 *
 * @param string $slug     Category slug.
 * @param string $fallback Optional fallback URL.
 * @return string
 */
function nexus_get_category_url( $slug, $fallback = '' ) {
	$slug = sanitize_title( (string) $slug );

	if ( '' === $slug ) {
		return $fallback ? $fallback : home_url( '/category/' );
	}

	$term = get_term_by( 'slug', $slug, 'category' );

	if ( $term instanceof WP_Term ) {
		$url = get_term_link( $term );

		if ( ! is_wp_error( $url ) ) {
			return $url;
		}
	}

	if ( $fallback ) {
		return $fallback;
	}

	return home_url( '/category/' . $slug . '/' );
}

/**
 * Return the public category label for legacy category slugs.
 *
 * Keep database slugs stable while avoiding retired Growth wording in visible
 * blog navigation, cards and archive headings.
 *
 * @param WP_Term|string $category Category term or fallback label.
 * @return string
 */
function hu_get_public_category_label( $category ) {
	$slug = '';
	$name = '';

	if ( $category instanceof WP_Term ) {
		$slug = (string) $category->slug;
		$name = (string) $category->name;
	} else {
		$name = (string) $category;
		$slug = sanitize_title( $name );
	}

	$labels = [
		'wordpress-growth-agentur' => __( 'WordPress-Systeme', 'blocksy-child' ),
	];

	return $labels[ $slug ] ?? $name;
}

/**
 * Return the canonical public URL for one versioned service cluster route.
 *
 * Cluster routes stay on stable public slugs even if legacy editor pages with
 * shorter aliases still exist in the database.
 *
 * @param string $slug     Canonical cluster slug.
 * @param string $fallback Optional fallback URL.
 * @return string
 */
function nexus_get_service_cluster_route_url( $slug, $fallback = '' ) {
	$slug = sanitize_title( (string) $slug );

	if ( '' === $slug ) {
		return $fallback ? $fallback : home_url( '/' );
	}

	return home_url( '/' . $slug . '/' );
}

/**
 * Resolve the versioned set of primary public URLs used for SEO and internal linking.
 *
 * Keep all canonical internal targets in one place so template, footer, cockpit and
 * editorial bridges point to the same primary URLs.
 *
 * @return array<string, string>
 */
function nexus_get_primary_public_url_map() {
	static $urls = null;

	if ( is_array( $urls ) ) {
		return $urls;
	}

	$request_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
	$agentur_url = nexus_get_page_url(
		[ 'wordpress-agentur-hannover', 'wordpress-agentur' ],
		home_url( '/wordpress-agentur-hannover/' )
	);
	// Die Agentur-Seite hat seit dem Umbau zur Entscheidungsseite keine Anker
	// #methode, #technisches-seo oder #wordpress-wartung mehr. SEO, Betreuung
	// und Methode behandelt dort #zusammenarbeit; SEO- und Wartungs-Queries
	// gehören laut docs/seo/query-ownership.csv weiter dieser Seite.
	// Conversion-Links führen zur Leistung "Anfragestrecken & Landingpages"
	// auf der Startseite.
	$seo_url     = trailingslashit( $agentur_url ) . '#zusammenarbeit';
	$cro_url     = home_url( '/#angebot-funnel' );
	// Wartung hat seit 2026-10-24 eine eigene nationale Route.
	$wartung_url = nexus_get_page_url( [ 'wordpress-wartung' ], home_url( '/wordpress-wartung/' ) );
	$cwv_url     = trailingslashit( $agentur_url ) . '#zusammenarbeit';

	$urls = [
		'home'                 => home_url( '/' ),
		'blog'                 => function_exists( 'nexus_get_blog_posts_url' ) ? nexus_get_blog_posts_url() : home_url( '/blog/' ),
		'audit'                => $request_url,
		'audit_linkedin'       => $request_url,
		'results'              => function_exists( 'nexus_get_results_url' ) ? nexus_get_results_url() : home_url( '/case-study-solar-leadgenerierung/' ),
		'glossary'             => nexus_get_page_url(
			[ 'glossar' ],
			home_url( '/glossar/' )
		),
		'agentur'              => $agentur_url,
		'seo'                  => $seo_url,
		'wartung'              => $wartung_url,
		'tracking'             => nexus_get_service_cluster_route_url(
			'ga4-tracking-setup',
			home_url( '/ga4-tracking-setup/' )
		),
		'cwv'                  => $cwv_url,
		'cro'                  => $cro_url,
		'performance_marketing'=> nexus_get_service_cluster_route_url(
			'performance-marketing',
			home_url( '/performance-marketing/' )
		),
		'landingpage'          => nexus_get_page_url(
			[ 'landingpage-erstellen-lassen' ],
			home_url( '/landingpage-erstellen-lassen/' )
		),
		'conversion'           => nexus_get_page_url(
			[ 'conversion-optimierung' ],
			home_url( '/conversion-optimierung/' )
		),
		'website'              => nexus_get_page_url(
			[ 'wordpress-website-erstellen-lassen' ],
			home_url( '/wordpress-website-erstellen-lassen/' )
		),
		'tools'                => $request_url,
		'performance_analysis' => $request_url,
		'about'                => nexus_get_page_url(
			[ 'hasim-uener', 'uber-mich' ],
			home_url( '/hasim-uener/' )
		),
		'contact'              => function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' ),
		'impressum'            => nexus_get_page_url(
			[ 'impressum' ],
			home_url( '/impressum/' )
		),
		'datenschutz'          => nexus_get_page_url(
			[ 'datenschutz' ],
			home_url( '/datenschutz/' )
		),
		'e3'                   => nexus_get_page_url(
			[ 'case-study-solar-leadgenerierung', 'e3-new-energy', 'case-studies/e3-new-energy', 'case-e3' ],
			home_url( '/case-study-solar-leadgenerierung/' )
		),
		'energy'               => function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' ),
		'solar_leads_alternative' => nexus_get_page_url(
			[ 'solar-leads-kaufen-alternative' ],
			home_url( '/solar-leads-kaufen-alternative/' )
		),
		'solar_leads_cost_study' => nexus_get_page_url(
			[ 'solar-leads-kosten-studie' ],
			home_url( '/solar-leads-kosten-studie/' )
		),
		'solar_leads_tco'      => nexus_get_page_url(
			[ 'solar-leads-kaufen-alternative' ],
			home_url( '/solar-leads-kaufen-alternative/' )
		) . '#vergleich',
		'solar_cpl'            => nexus_get_page_url(
			[ 'solar-leads-kosten-studie' ],
			home_url( '/solar-leads-kosten-studie/' )
		) . '#cpo',
		'solar_funnel'         => nexus_get_page_url(
			[ 'solar-waermepumpen-leadgenerierung' ],
			home_url( '/solar-waermepumpen-leadgenerierung/' )
		) . '#strecke',
		'solar_tracking'       => nexus_get_page_url(
			[ 'server-side-tracking-b2b' ],
			home_url( '/server-side-tracking-b2b/' )
		),
		'request'              => $request_url,
		'whitelabel'           => function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' ),
		'seo_category'         => nexus_get_category_url( 'seo', home_url( '/category/seo/' ) ),
		'tracking_category'    => nexus_get_category_url( 'tracking', home_url( '/category/tracking/' ) ),
		'cro_category'         => nexus_get_category_url( 'cro', home_url( '/category/cro/' ) ),
		'performance_category' => nexus_get_category_url( 'wordpress-performance', home_url( '/category/wordpress-performance/' ) ),
	];

	return $urls;
}

/**
 * Resolve a single primary public URL by semantic key.
 *
 * @param string $key      URL key from nexus_get_primary_public_url_map().
 * @param string $fallback Optional fallback URL.
 * @return string
 */
function nexus_get_primary_public_url( $key, $fallback = '' ) {
	$key = (string) $key;
	$map = nexus_get_primary_public_url_map();

	if ( isset( $map[ $key ] ) && '' !== (string) $map[ $key ] ) {
		return (string) $map[ $key ];
	}

	return $fallback ? $fallback : home_url( '/' );
}

/**
 * Return the FAQ set for the consolidated local WordPress agency page.
 *
 * Shared between the page template and centralized JSON-LD output.
 *
 * @return array<int, array{question: string, answer: string}>
 */
function nexus_get_agentur_faq_items() {
	return [
		[
			'question' => 'Welche WordPress Agentur in Hannover passt für B2B-Projekte?',
			'answer'   => 'Das hängt weniger von der Größe des Anbieters als vom Arbeitsmodell ab. Brauchen Sie mehrere Teams parallel, feste Vertretung oder SLA-Strukturen, ist eine größere Agentur im Vorteil. Wenn WordPress, technisches SEO, Tracking und Conversion eng zusammengehören sollen, kann ein direkter Verantwortungsweg sinnvoller sein.',
		],
		[
			'question' => 'Was ist der Unterschied zwischen einer klassischen WordPress-Agentur und direkter Zusammenarbeit?',
			'answer'   => 'In einer klassischen Agentur laufen Beratung, Projektleitung und Umsetzung häufig über mehrere Rollen. Bei der direkten Zusammenarbeit bleiben Entscheidung, technische Umsetzung und spätere Auswertung bei derselben Person. Das reduziert Übergaben, ersetzt aber kein großes Team, wenn mehrere Disziplinen gleichzeitig volle Kapazität brauchen.',
		],
		[
			'question' => 'Bieten Sie WordPress SEO in Hannover an?',
			'answer'   => 'Ja, mit technischem Schwerpunkt: Suchintention und Seitenrollen, interne Verlinkung, Title und Canonical, strukturierte Daten, Performance sowie Weiterleitungen bei Relaunches. Content-Produktion oder Linkkauf sind kein pauschales Zusatzpaket; zuerst wird geprüft, welcher technische oder strukturelle Hebel tatsächlich fehlt.',
		],
		[
			'question' => 'Erstellen Sie komplette WordPress-Websites oder optimieren Sie nur bestehende Seiten?',
			'answer'   => 'Beides. Möglich sind neue WordPress-Websites, Relaunches, Landingpages und die technische Weiterentwicklung bestehender Systeme. Bei einem Neubau oder Relaunch werden Umfang, Preis und Zeitrahmen vor dem Start festgelegt; bei bestehenden Seiten beginnt die Arbeit mit dem konkreten Engpass.',
		],
		[
			'question' => 'Arbeiten Sie nur mit Unternehmen aus Hannover?',
			'answer'   => 'Nein. Der Sitz ist Pattensen bei Hannover, persönliche Termine sind in der Region nach Vereinbarung möglich. Entwicklung, Reviews, Tracking und Abstimmung funktionieren remote im gesamten DACH-Raum.',
		],
		[
			'question' => 'Wann ist eine klassische Agentur die bessere Wahl?',
			'answer'   => 'Wenn Sie 24/7-Bereitschaft, feste Vertretung, große E-Commerce-Teams, viele parallel laufende Fachdisziplinen oder formale Beschaffungsprozesse mit mehreren Rollen brauchen. Diese Anforderungen löst ein größeres Team strukturell besser.',
		],
		[
			'question' => 'Brauche ich für bessere Conversion automatisch einen Relaunch?',
			'answer'   => 'Nein. Ein Relaunch ist nur sinnvoll, wenn die bestehende Struktur den nächsten Schritt technisch oder inhaltlich blockiert. Häufig reichen klarere Angebotsführung, bessere Messung, ein anderes Formular oder gezielte technische Korrekturen.',
		],
		[
			'question' => 'Bieten Sie WordPress-Wartung in Hannover an?',
			'answer'   => sprintf( 'Ja, als feste Wartung für %1$s netto im Monat, für WooCommerce-Shops %2$s. Neben Updates und Backups prüft jeden Monat eine echte Testanfrage, ob Anfragen ankommen. Bei einem Ausfall reagiere ich %3$s; eine 24/7-Bereitschaft und ein Vertretungsteam gibt es nicht. Vor dem Start steht der Übernahme-Check.', hu_maintenance_price(), hu_maintenance_price( true ), hu_maintenance_outage_promise() ),
		],
	];
}

/**
 * Return the FAQ set for the landing page offer /landingpage-erstellen-lassen/.
 *
 * Shared between the page template and the FAQPage node in inc/org-schema.php.
 * Preise kommen aus dem Kanon; Lieferzeit und Voraussetzungen werden bewusst
 * nicht pauschal zugesagt, sondern vor dem Start schriftlich festgelegt.
 *
 * @return array<int, array{key: string, question: string, answer: string}>
 */
function nexus_get_landingpage_faq_items() {
	$price         = hu_landingpage_price( true );
	$website_price = hu_freelancer_website_price( true );

	return [
		[
			'key'      => 'kosten',
			'question' => 'Was kostet es, eine Landingpage erstellen zu lassen?',
			'answer'   => sprintf( '%s als Festpreis für eine WordPress-Seite und ein Angebot: Konzept, Text, Anfrageformular mit Bestätigung, seitenbezogene Herkunfts- und Conversion-Erfassung sowie Prüfung und Übergabe mit zwei Korrekturschleifen. Anzeigenbetreuung, Foto- und Videoproduktion und mehrere Seitenvarianten werden gesondert vereinbart.', $price ),
		],
		[
			'key'      => 'text',
			'question' => 'Wer schreibt die Texte?',
			'answer'   => 'Ich. Sie liefern im Auftaktgespräch und im Fragebogen die Fakten: Angebot, Zielgruppe, Preise und die Einwände, die Sie aus Gesprächen kennen. Daraus schreibe ich den Text, und Sie geben ihn frei, bevor die Seite gebaut wird.',
		],
		[
			'key'      => 'dauer',
			'question' => 'Wie lange dauert es?',
			'answer'   => 'Den Termin lege ich vor dem Start schriftlich fest. Er hängt vor allem daran, wie schnell Fakten, Bilder und Freigaben bei mir sind. Live geht die Seite erst nach Ihrer Abnahme.',
		],
		[
			'key'      => 'wordpress',
			'question' => 'Muss meine Website mit WordPress laufen?',
			'answer'   => 'Die Landingpage baue ich in WordPress. Läuft Ihre Website mit einem anderen System, klären wir vor dem Festpreis, wo die Seite liegen kann. Domain, Hosting und Zugänge bleiben bei Ihnen.',
		],
		[
			'key'      => 'cookies',
			'question' => 'Braucht die Landingpage einen Cookie-Banner?',
			'answer'   => 'Seite und Formular setzen keine eigenen Tracking-Cookies. Für GA4 oder Google Ads berücksichtige ich Ihr bestehendes Consent-Setup und die jeweiligen Einwilligungszustände. Ein neues vollständiges GA4-, GTM- oder Consent-Setup wird als separates Tracking-Projekt vereinbart.',
		],
		[
			'key'      => 'messung',
			'question' => 'Welche Herkunft und Conversion werden erfasst?',
			'answer'   => 'Einstiegsseite und verfügbare Kampagnenparameter werden mit der Anfrage übergeben. Die vereinbarte Conversion dieser Seite richte ich in Ihren vorhandenen GA4- oder Google-Ads-Konten ein. Welche Herkunft erfassbar ist und wie eine Conversion zählt, klären wir anhand von Linkparametern, Einwilligungszuständen und dem bestehenden Setup.',
		],
		[
			'key'      => 'website',
			'question' => 'Wann ist eine Website die bessere Wahl?',
			'answer'   => sprintf( 'Wenn Sie mehrere Leistungen zeigen wollen oder noch keine Website haben. Die Anfrage-Website startet bei %s für Grundsystem und erste Hauptseite; weitere Seiten werden nach Aufgabe kalkuliert. Die Landingpage ist für ein einzelnes Angebot gedacht, auf das Sie Besucher gezielt schicken.', $website_price ),
		],
		[
			'key'      => 'pflege',
			'question' => 'Kann ich die Seite danach selbst ändern?',
			'answer'   => 'Ja. Texte und Bilder pflegen Sie im WordPress-Editor, und bei der Übergabe zeige ich Ihnen, wie. Größere Änderungen übernehme ich auf Wunsch im Monatskontingent.',
		],
	];
}

/** Visible website FAQs and FAQPage use this single source. */
function nexus_get_website_faq_items() {
	$rules = hu_website_calculator_rules();
	return [
		[ 'key' => 'kosten', 'question' => 'Was kostet es, eine WordPress-Website erstellen zu lassen?', 'answer' => sprintf( 'Ab %1$s für Grundsystem und erste Hauptseite. Weitere Seiten werden nach Aufgabe kalkuliert: kurze Seite %2$s, Standardseite %3$s, Leistungs-/Verkaufsseite %4$s. Technisches SEO, die geprüfte Anfrage-Strecke und das Basis-Daten-Cockpit sind enthalten. Texterstellung ist optional. Conversion-Tracking, CRM, individuelles Screendesign und ein individuelles Dashboard mit zusätzlichen Datenquellen können Sie ergänzen; den verbindlichen Gesamtpreis erhalten Sie vor Beauftragung.', hu_freelancer_website_price( true ), hu_format_eur( HU_WEBSITE_PAGE_UTILITY ), hu_format_eur( HU_WEBSITE_PAGE_STANDARD ), hu_format_eur( HU_WEBSITE_PAGE_SALES ) ) ],
		[ 'key' => 'dauer', 'question' => 'Wie lange dauert die Umsetzung?', 'answer' => sprintf( 'Die Produktionszeit hängt von Seitentypen und Zusatzmodulen ab. Tracking ergänzt einmal %1$s, Standard-CRM %2$s; Texterstellung und individuelles Screendesign haben eigene Zeitbeiträge. Der Rechner zeigt die geplante Produktionszeit bis zum geprüften Abnahmestand. Starttermin und Ihre Freigabezeiten kommen separat dazu.', number_format( (float) $rules['days']['tracking'], 0, ',', '.' ) . ' Werktag', number_format( (float) $rules['days']['crm'], 0, ',', '.' ) . ' Werktage' ) ],
		[ 'key' => 'design', 'question' => 'Was ist der Unterschied zwischen Basisdesign und Screendesign?', 'answer' => 'Beim enthaltenen Basisdesign passe ich bewährte responsive Layouts, Typografie und Farben an Ihr Unternehmen an. Beim individuellen Screendesign entwickle ich eine eigene Gestaltungsrichtung in Figma für Desktop und Mobil. Wiederverwendete Seitenlayouts zählen nur einmal.' ],
		[ 'key' => 'texte', 'question' => 'Muss ich die Texte selbst liefern?', 'answer' => 'Nein. Fertige, freigegebene Texte pflege ich ohne Texterstellungs-Aufpreis ein. Wenn ich die Texte auf Basis Ihrer Angaben entwickle, kalkuliert der Rechner den Zusatzaufwand nach Seitentyp. Rechtstexte liefern Sie selbst.' ],
		[ 'key' => 'lizenzen', 'question' => 'Bin ich danach an Wartung, Pagebuilder oder Hosting gebunden?', 'answer' => 'Nein. Die Website läuft auf Ihrem Hosting, Domain und Zugänge bleiben bei Ihnen und Sie erhalten den Projektcode. Im Grundprodukt gibt es keine Pflichtwartung und keine Pflichtlizenzen für Theme oder Pagebuilder. Externe Dienste mit laufenden Kosten werden vor Beauftragung benannt.' ],
		[ 'key' => 'uebernahme', 'question' => 'Übernehmen Sie auch eine bestehende WordPress-Website?', 'answer' => sprintf( 'Ja. Bei einem Relaunch prüfe ich Bestand, URLs und technische Voraussetzungen. Für unklare oder fremde Setups gibt es vorab den Übernahme-Check für %s; bei Beauftragung wird er verrechnet.', hu_freelancer_takeover_check_price( true ) ) ],
	];
}

/**
 * Return the FAQ set for the conversion offer /conversion-optimierung/.
 *
 * Shared between the page template and the FAQPage node in inc/org-schema.php.
 * Drei Fragen, wortgleich mit dem Prototyp der Verdichtung: Traffic, WordPress,
 * KI. Die Abgrenzung zur Landingpage und die Anrechnung der Analyse stehen
 * an anderer Stelle der Seite (Abschnitt 4), "Warum kein A/B-Test" als
 * Marginalie in Abschnitt 3. Kein Betrag steht in den Antworten.
 *
 * @return array<int, array{key: string, question: string, answer: string}>
 */
function nexus_get_conversion_faq_items() {
	return [
		[
			'key'      => 'traffic',
			'question' => 'Wie viel Traffic braucht meine Seite?',
			'answer'   => 'Keine feste Grenze. Es sollten Besucher kommen und zumindest vereinzelt Anfragen. Ohne Besucher ist zuerst Sichtbarkeit das Thema.',
		],
		[
			'key'      => 'wordpress',
			'question' => 'Muss meine Website mit WordPress gebaut sein?',
			'answer'   => 'Für die Analyse nicht. Umsetzen kann ich in WordPress. Bei anderen Systemen steht im Befund, was Ihr Team selbst erledigen kann.',
		],
		[
			'key'      => 'ki',
			'question' => 'Arbeitet eine KI mit meinen Anfragen?',
			'answer'   => 'Nur wenn Sie die Einordnung beauftragen, und nur für Freitext. Entscheidungen treffen feste Regeln. Verarbeitet wird auf Wunsch in Deutschland oder der EU.',
		],
	];
}

/**
 * Return the FAQ set for the Aroundhome cost decision page.
 *
 * The visible disclosures and the FAQPage node both consume this array. Facts
 * about the provider stay deliberately narrow and point back to the current
 * written offer; the page must not turn public marketing copy into a promise
 * about an individual partner contract.
 *
 * @return array<int, array{key: string, question: string, answer: string}>
 */
function nexus_get_aroundhome_faq_items() {
	return [
		[
			'key'      => 'kosten',
			'question' => 'Was kostet Aroundhome für Handwerker?',
			'answer'   => 'Aroundhome nennt keinen öffentlichen Preis. Laut Partnerseite zahlen Fachbetriebe einen Festpreis pro übermittelter Anfrage, abhängig vom gewählten Produkt; der Betrag wird erst im digitalen Vertragsprozess angezeigt. Eine Provision auf den Auftrag fällt nicht an, die Gebühr aber auch dann, wenn kein Auftrag entsteht.',
		],
		[
			'key'      => 'serioes',
			'question' => 'Ist Aroundhome seriös?',
			'answer'   => 'Aroundhome veröffentlicht Partner-AGB und beschreibt sein Abrechnungsmodell offen auf der Partnerseite. Seriosität sagt aber nichts darüber, ob der Kanal für Ihren Betrieb wirtschaftlich ist. Das entscheiden Ihr Vertrag und Ihre Kosten pro gewonnenem Auftrag.',
		],
		[
			'key'      => 'funktioniert',
			'question' => 'Funktioniert Aroundhome für Solar- und SHK-Betriebe?',
			'answer'   => 'Es funktioniert, wenn Sie schnell reagieren und genug Anfragen in Aufträge verwandeln. Jede Anfrage geht laut Aroundhome an bis zu drei Fachbetriebe, wer zuerst anruft, hat die besten Chancen. Ob es sich rechnet, zeigt der Rechner oben mit Ihrer Abschlussquote.',
		],
		[
			'key'      => 'exklusivitaet',
			'question' => 'Sind Aroundhome-Leads exklusiv?',
			'answer'   => 'In der Regel nicht. Aroundhome beschreibt eine Vermittlung an höchstens drei Fachpartner, also bis zu zwei Mitbewerber pro Anfrage. Was für Ihr Produkt gilt, steht im Vertrag.',
		],
		[
			'key'      => 'laufzeit',
			'question' => 'Gibt es eine Mindestlaufzeit?',
			'answer'   => 'Laut Partnerseite ist die Zusammenarbeit in den meisten Fällen monatlich kündbar. Pausen, etwa im Urlaub, können beantragt werden, werden aber geprüft. Lassen Sie sich Laufzeit, Mengen und Pausenregeln schriftlich bestätigen.',
		],
		[
			'key'      => 'erfahrungen',
			'question' => 'Welche Aroundhome-Erfahrungen sind aussagekräftig?',
			'answer'   => 'Nur solche, die Gewerk, Region, Zeitraum und Abschlussquote nennen. Einzelne Online-Bewertungen liefern gute Fragen für das Vertragsgespräch, aber keinen Beleg für Ihre Kosten pro Auftrag. Belastbarer sind Ihre eigenen CRM-Zahlen nach zwei bis drei Monaten.',
		],
		[
			'key'      => 'alternativen',
			'question' => 'Welche Alternativen gibt es zu Aroundhome?',
			'answer'   => 'Andere Portale wie Checkfox, Wattfox oder DAA verkaufen Anfragen nach ähnlichem Prinzip. Die eigentliche Alternative ist ein eigener Anfrageweg über Ihre Website: Die Anfragen gehören nur Ihnen und die Daten bleiben im Betrieb. Übergangsweise funktioniert beides parallel, wenn Sie die Quellen getrennt messen.',
		],
		[
			'key'      => 'beratung',
			'question' => 'Ersetzt diese Seite eine Vertragsberatung?',
			'answer'   => 'Nein. Sie ist eine wirtschaftliche Entscheidungshilfe, keine Rechtsberatung. Rechtliche Fragen zu Ihrem Vertrag gehören zu einer qualifizierten Beratung.',
		],
	];
}

/**
 * Return the FAQ set for the Checkfox decision page.
 *
 * Gleiche Bauart wie nexus_get_aroundhome_faq_items(): sichtbare Antworten und
 * FAQPage-Node lesen dasselbe Array und koennen nicht auseinanderlaufen.
 *
 * Die Fragen stammen aus echten Suchanfragen der Route
 * (seo-research/2026-09/, 90d: "checkfox serioes" 785 Impressionen,
 * "checkfox erfahrungen" 311, "checkfox bewertung" 69, "was ist checkfox" 27) —
 * nicht aus erfundenen Prompt-Beispielen.
 *
 * Die Antworten halten die Linie der Seite: keine Preise, Exklusivitaetsregeln
 * oder Qualitaetsquoten eines Anbieters, keine Rechtsauskunft und kein Urteil
 * ueber konkrete Vertraege. Was hier steht, muss der Betrieb selbst pruefen
 * koennen.
 *
 * @return array<int, array{key: string, question: string, answer: string}>
 */
function nexus_get_checkfox_faq_items() {
	return [
		[
			'key'      => 'serioesitaet',
			'question' => 'Ist Checkfox seriös?',
			'answer'   => 'Seriosität lässt sich an prüfbaren Merkmalen festmachen statt am Bauchgefühl: vollständiges Impressum, nachvollziehbare Datenschutzhinweise und ein einsehbares Vertragswerk, in dem Exklusivität, Anzahl paralleler Empfänger, Reklamationsgründe und Kündigungsfrist geregelt sind. Diese Prüfung können Sie vor der Unterschrift selbst durchführen. Ob sich der Kanal für Ihren Betrieb rechnet, ist eine davon getrennte Frage — sie entscheidet sich an Ihrer Abschlussquote und Ihrer Vertriebszeit, nicht an der Seriosität des Anbieters.',
		],
		[
			'key'      => 'was-ist',
			'question' => 'Was ist Checkfox?',
			'answer'   => 'Checkfox tritt als Portal auf: Endkundenanfragen aus dem Solar- und Wärmepumpen-Bereich werden gesammelt und an ausführende Betriebe weitergegeben. Der Weg lautet Endkunde → Portal → Verteilung laut Vertrag → Betrieb. Die Nachfrage entsteht damit über die Reichweite des Portals, nicht über Ihre eigene. Wie viele Betriebe eine Anfrage parallel erhalten und welche Exklusivität gilt, steht im jeweiligen Vertrag und nicht in einer allgemeinen Marktaussage.',
		],
		[
			'key'      => 'kosten-pro-auftrag',
			'question' => 'Was kostet eine Anfrage bei Checkfox?',
			'answer'   => 'Diese Seite nennt bewusst keinen Preis: Konditionen sind vertragsabhängig und ohne Einsicht in Ihr konkretes Angebot nicht belegbar. Entscheidend ist ohnehin nicht der Stückpreis, sondern was ein gewonnener Auftrag kostet. Die Rechnung lautet: Anfragepreis geteilt durch Abschlussquote. Rechenbeispiel mit frei gewählten Zahlen: 100 € pro Anfrage bei 10 % Abschlussquote ergeben 1.000 € pro Auftrag, nicht 100 €. Setzen Sie Ihre eigenen Werte ein — erst damit wird die Rechnung belastbar.',
		],
		[
			'key'      => 'erfahrungen',
			'question' => 'Welche Erfahrungen gibt es mit Checkfox?',
			'answer'   => 'Öffentliche Bewertungen schwanken stark und bilden meist Einzelfälle ab; sie ersetzen die Prüfung Ihres konkreten Vertrags nicht. Diese Seite sammelt deshalb keine Erfahrungsberichte, sondern gibt zwei überprüfbare Schritte an die Hand: den Vertragscheck vor der Unterschrift und die Rechnung Kosten pro Auftrag nach den ersten Anfragen. Beides können Sie nachrechnen, einen fremden Erfahrungsbericht nicht.',
		],
		[
			'key'      => 'beratung',
			'question' => 'Ersetzt diese Einordnung eine Rechts- oder Vertragsberatung?',
			'answer'   => 'Nein. Die Seite ist eine wirtschaftliche Entscheidungshilfe und keine Rechtsberatung. Rechtliche Fragen und die Auslegung Ihres konkreten Vertrags gehören in eine qualifizierte rechtliche Prüfung.',
		],
	];
}

/**
 * Return the objection set for the anonymized solar methodology case.
 *
 * Shared between the page template and centralized JSON-LD output, so the
 * visible answers and the FAQPage node can never drift apart.
 *
 * Every figure comes from blocksy-child/inc/canon/: case metrics from the E3
 * canon, the Anfragestrecke price from hu_anfragestrecke_price() (since
 * 2026-10-23 the case is proof for that product, not only for the Marktcheck).
 *
 * @return array<int, array{question: string, answer: string}>
 */
function nexus_get_e3_case_faq_items() {
	$cpl_after = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after' ) : '22 €';
	$timeline  = hu_e3_canon()['timeline'];

	return [
		[
			'question' => sprintf( 'Sind %s pro Anfrage auf meinen Betrieb übertragbar?', $cpl_after ),
			'answer'   => sprintf( 'Nicht als Zusage. %s war ein erreichter Wert der optimierten Kampagnen, kein Durchschnitt des gesamten Projekts und kein Tarif. Was die Kosten pro Anfrage in Ihrem Fall bestimmt, sind Region, Produktmix und der Werbedruck Ihrer Mitbewerber. Übertragbar ist die Mechanik: Kaufabsicht, Exklusivität, Vorqualifizierung und Reaktionszeit lassen sich in jedem Betrieb mit eigenem Vertrieb nachbauen. Die Zahl, die dabei herauskommt, ist von Fall zu Fall verschieden.', $cpl_after ),
		],
		[
			'question' => 'Worauf beziehen sich Leadzahl und Conversion-Raten?',
			'answer'   => hu_e3_summary( 'definitions' ),
		],
		[
			'question' => 'Wie lange dauert es, bis eigene Anfragen ankommen?',
			'answer'   => $timeline['preparation'] . ' ' . $timeline['campaign'] . ' ' . $timeline['optimization'] . ' ' . $timeline['followup'],
		],
		[
			'question' => 'Was kostet der Aufbau eines eigenen Anfragesystems?',
			'answer'   => function_exists( 'hu_anfragestrecke_price' )
				? sprintf( 'Die Strecke aus diesem Fall gibt es heute als Festpreis: Landingpage, mehrstufiges Formular, Conversion-Tracking und Benachrichtigung für %1$s netto, mit CRM-Anbindung und Rückmeldung an Google Ads für %2$s netto. Werbebudget kommt dazu und läuft über Ihre eigenen Konten. Für Solar- und Wärmepumpenbetriebe gibt es zusätzlich den kostenlosen Marktcheck.', hu_anfragestrecke_price(), hu_anfragestrecke_price( true ) )
				: 'Das hängt davon ab, was bereits steht. Ein Betrieb mit sauberem CRM und funktionierender Website braucht etwas anderes als einer, der bei Tracking und Anfragestrecke bei null anfängt.',
		],
		[
			'question' => 'Was muss der Betrieb selbst leisten?',
			'answer'   => 'Drei Dinge. Erstens einen Vertrieb, der neue Anfragen zügig anruft — die Reaktionszeit ist ein Teil des Systems, kein Randthema. Zweitens einen Ansprechpartner mit Entscheidungsbefugnis für Rückfragen zu Angebot und Zielgebiet. Drittens Zugriff auf Werbekonten, Website und CRM. Der Aufbau selbst liegt nicht beim Betrieb.',
		],
		[
			'question' => 'Funktioniert das auch ohne eigenen Außendienst?',
			'answer'   => 'Nein. Ein Anfragesystem erzeugt Gespräche, keine Aufträge. Die Abschlussquote in diesem Mandat entstand, weil hinter den Anfragen eine Vertriebsmannschaft stand, die Termine wahrnehmen und Angebote schreiben konnte. Ohne diesen Anschluss verlagert ein besseres System das Problem nur nach hinten.',
		],
		[
			'question' => 'Ersetzt das den Portal-Einkauf sofort?',
			'answer'   => 'Nein, nicht sofort. Portal-Einkauf und eigene Strecke liefen parallel. Heruntergefahren wurde der Einkauf erst, als die eigene Strecke messbar lieferte. So hatte der Vertrieb in der Anlaufphase keine Lücke.',
		],
		[
			'question' => 'Wem gehören die Anfragen und die Daten?',
			'answer'   => 'Dem Betrieb. Eigene Domain, eigenes Formular, eigene Datenbank, eigene Werbekonten. Das ist kein Nebeneffekt, sondern der Kern des Unterschieds zum Portal-Einkauf: Eine gekaufte Anfrage ist ein Vorgang, eine eigene Anfrage ist ein Bestand, der auch nächstes Jahr noch zählt.',
		],
	];
}

/**
 * Resolve the former audit page URL.
 *
 * The standalone Growth Audit and System-Diagnose page are retired; keep this
 * helper as a compatibility alias so older CTA surfaces route into the
 * marketcheck on the solar landing page.
 *
 * @return string
 */
function nexus_get_audit_url() {
	return function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
}

/**
 * Resolve the canonical energy systems landing page URL.
 *
 * @return string
 */
function nexus_get_energy_systems_url() {
	$page_id = nexus_get_page_id( [ 'solar-waermepumpen-leadgenerierung' ] );

	if ( $page_id ) {
		return get_permalink( $page_id );
	}

	$template_page_id = nexus_get_page_id_by_template( 'page-solar-waermepumpen-leadgenerierung.php' );

	if ( $template_page_id ) {
		$permalink = get_permalink( $template_page_id );
		$path      = trailingslashit( '/' . ltrim( (string) wp_parse_url( $permalink, PHP_URL_PATH ), '/' ) );

		if ( '/website-fuer-solar-und-waermepumpen-anbieter/' !== $path ) {
			return $permalink;
		}
	}

	return home_url( '/solar-waermepumpen-leadgenerierung/' );
}

/**
 * Resolve the canonical URL for the retired dedicated request page.
 *
 * The public /anfrage/ intake was retired in favor of the marketcheck.
 * Keep this helper as a compatibility alias so old
 * templates and editor links cannot route users back into the old form.
 *
 * @return string
 */
function nexus_get_anfrage_url() {
	return function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
}

/**
 * Resolve the canonical hard-CTA URL for qualified request analysis.
 *
 * @return string
 */
function nexus_get_primary_request_url() {
	return function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
}

/**
 * Return the canonical hard-CTA label for the solar inquiry flow.
 *
 * @return string
 */
function nexus_get_primary_request_cta_label() {
	return 'Marktcheck mit Fit-Entscheid starten';
}

/**
 * Resolve the primary results hub page ID.
 *
 * @return int
 */
function nexus_get_results_page_id() {
	$canonical_page_id = nexus_get_page_id( [ 'ergebnisse' ] );

	if ( $canonical_page_id ) {
		return $canonical_page_id;
	}

	// Der Hub hiess bis 2026-09 page-case-studies-e-commerce.php. In WordPress
	// gespeicherte Template-Zuweisungen zeigen weiter auf den alten Namen, also
	// bleibt er als zweiter Versuch stehen.
	foreach ( [ 'page-ergebnisse.php', 'page-case-studies-e-commerce.php' ] as $template ) {
		$template_page_id = nexus_get_page_id_by_template( $template );

		if ( $template_page_id ) {
			return $template_page_id;
		}
	}

	return nexus_get_page_id( [ 'case-studies-e-commerce', 'case-studies' ] );
}

/**
 * Determine whether the current request renders the results hub itself.
 *
 * Enger als nexus_is_results_context(): nur die Hub-Route, nicht der ganze
 * Proof-Bereich. wp_head laeuft vor template_include, deshalb entscheidet hier
 * die Seite, nicht das aufgeloeste Template.
 *
 * @return bool
 */
function hu_is_results_hub_request() {
	if ( is_admin() || ! is_page() ) {
		return false;
	}

	$results_page_id = nexus_get_results_page_id();

	if ( $results_page_id && is_page( $results_page_id ) ) {
		return true;
	}

	return is_page( [ 'ergebnisse', 'case-studies-e-commerce', 'case-studies' ] );
}

/**
 * Resolve the primary results URL.
 *
 * Der Hub /ergebnisse/ ist seit 2026-09-25 stillgelegt: veraltetes Menue,
 * alte Antwortzeit und Details, die den anonymisierten Fall wiedererkennbar
 * machten. Der Nachweis ist die Fallstudie. Jeder Verweis auf "Ergebnisse"
 * zeigt deshalb direkt dorthin, ohne Umweg ueber die Weiterleitung in
 * nexus_redirect_legacy_results_path().
 *
 * Nicht ueber nexus_get_primary_public_url(): Deren Map ruft diese Funktion
 * selbst auf.
 *
 * @return string
 */
function nexus_get_results_url() {
	$case = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];

	return (string) ( $case['url'] ?? home_url( '/case-study-solar-leadgenerierung/' ) );
}

/**
 * Resolve the whitelabel proof page ID.
 *
 * @return int
 */
function nexus_get_whitelabel_page_id() {
	$template_page_id = nexus_get_page_id_by_template( 'page-whitelabel-retainer.php' );

	if ( $template_page_id ) {
		return $template_page_id;
	}

	return nexus_get_page_id( [ 'whitelabel-retainer', 'whitelabel-retainer-proof', 'whitelabel' ] );
}

/**
 * Resolve the whitelabel proof page URL.
 *
 * @return string
 */
function nexus_get_whitelabel_page_url() {
	$page_id = nexus_get_whitelabel_page_id();

	if ( $page_id ) {
		return get_permalink( $page_id );
	}

	return home_url( '/whitelabel-retainer/' );
}

/**
 * Return the FAQ set for the White-Label partner funnel.
 *
 * Shared between the page template and centralized JSON-LD output so the
 * visible answers, tracking labels, and FAQPage node cannot drift apart.
 *
 * Seit dem Relaunch auf dem Strecke-System (2026-09) stehen hier nur noch
 * Fragen, die die Seite nicht schon selbst beantwortet. Preise und
 * Test-Sprint-Umfang stehen in Abschnitt 05, Fehler oder Aenderung
 * im Ablauf (04), Zugaenge, Eigentum und Ausstieg in der Absicherung (06). Die Schluessel bleiben
 * stabil, weil sie als data-track-label der FAQ-Klicks laufen.
 *
 * @return array<int, array{key: string, question: string, answer: string}>
 */
function nexus_get_whitelabel_faq_items() {
	return [
		[
			'key'      => 'kapazitaet',
			'question' => 'Wie viel Kapazität hast du?',
			'answer'   => 'Wie viel in welchem Zeitraum frei ist, klären wir vor dem Angebot, Abwesenheiten plane ich offen ein. Braucht ihr mehrere Entwickler parallel oder Bereitschaft rund um die Uhr, sprechen wir das ebenfalls vorher an.',
		],
		[
			'key'      => 'bestand',
			'question' => 'Arbeitest du auch in bestehenden WordPress-Installationen?',
			'answer'   => 'Ja. Die meisten Aufgaben betreffen laufende Installationen: Weiterentwicklung, Fehler, Ladezeit und Erweiterungen.',
		],
		[
			'key'      => 'stack',
			'question' => 'Müssen wir unseren Stack ändern?',
			'answer'   => 'Nein. Hosting, Theme, Page Builder, Git und Deployment bleiben, wenn sie technisch funktionieren. Ich arbeite in eurem Workflow. Gibt es noch keinen, reicht meist eine Trennung zwischen Staging und Live.',
		],
		[
			'key'      => 'vor-ort',
			'question' => 'Kommst du auch vor Ort?',
			'answer'   => 'Zu Kickoff, Workshop oder Abnahme in der Region Hannover, Hildesheim, Braunschweig, Wolfsburg und Celle ja. Die Umsetzung läuft remote. Termine weiter weg stimmen wir einzeln ab.',
		],
		[
			'key'      => 'recht',
			'question' => 'Arbeitest du als Subunternehmer?',
			'answer'   => 'Ja. Ich trete als eigenes Unternehmen auf, arbeite mit eigenen Arbeitsmitteln zu selbst gewählten Zeiten und rechne vereinbarte Leistungen ab, nicht Anwesenheit. Auch das Monatskontingent gilt für definierte Aufgaben, nicht für eine Einbindung in eure Dienstpläne. Wo personenbezogene Daten im Spiel sind, schließen wir zusätzlich einen Vertrag zur Auftragsverarbeitung nach DSGVO.',
		],
	];
}

/**
 * Determine whether the current request is inside the public results area.
 *
 * @return bool
 */
function nexus_is_results_context() {
	$results_page_id = nexus_get_results_page_id();

	if ( $results_page_id && is_page( $results_page_id ) ) {
		return true;
	}

	return is_page( 'case-studies-e-commerce' )
		|| is_page( 'case-studies' )
		|| is_page( 'ergebnisse' )
		|| is_page( 'case-study-solar-leadgenerierung' )
		|| is_page( 'e3-new-energy' )
		|| is_page( 'case-study-domdar' )
		|| is_page( 'domdar' )
		|| is_page( 'whitelabel-retainer' )
		|| is_page( 'whitelabel-retainer-proof' )
		|| is_page( 'whitelabel' )
		|| is_page_template( 'page-ergebnisse.php' )
		|| is_page_template( 'page-case-studies-e-commerce.php' )
		|| is_page_template( 'page-whitelabel-retainer.php' );
}

/**
 * Return the current request path with leading and trailing slash.
 *
 * @return string
 */
function nexus_get_current_request_path() {
	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	$request_path = wp_parse_url( $request_uri, PHP_URL_PATH );

	return trailingslashit( '/' . ltrim( (string) $request_path, '/' ) );
}

/**
 * Read the deployment marker shipped with the built child theme.
 *
 * @return string Commit SHA, or '' outside a deployed build.
 */
function nexus_get_deploy_marker_sha() {
	$path = trailingslashit( get_stylesheet_directory() ) . '.nexus-deploy-sha';

	if ( ! is_readable( $path ) ) {
		return '';
	}

	$sha = strtolower( trim( (string) file_get_contents( $path ) ) );

	return preg_match( '/^[a-f0-9]{7,40}$/', $sha ) ? $sha : '';
}

/**
 * Read what the production deploy recorded about the live revision.
 *
 * deploy.yml legt neben .nexus-deploy-sha die Datei .nexus-deploy-proof.json
 * ab: Zeitpunkt, Pull Request und das Ergebnis seiner Prueflaeufe. Fehlt sie
 * oder gehoert sie zu einem anderen Stand, bleiben diese Felder leer; Commit
 * und Zeitpunkt kommen dann nur aus dem Marker. Geschaetzt wird nichts.
 *
 * @param string $dir Theme directory; defaults to the active child theme.
 * @return array{sha:string,deployed_at:int,pr:int,checks_passed:int,checks_total:int}
 */
function nexus_get_deploy_proof( $dir = '' ) {
	$dir   = trailingslashit( '' !== $dir ? $dir : get_stylesheet_directory() );
	$proof = [
		'sha'           => '',
		'deployed_at'   => 0,
		'pr'            => 0,
		'checks_passed' => 0,
		'checks_total'  => 0,
	];

	$marker = $dir . '.nexus-deploy-sha';
	if ( ! is_readable( $marker ) ) {
		return $proof;
	}

	$sha = strtolower( trim( (string) file_get_contents( $marker ) ) );
	if ( ! preg_match( '/^[a-f0-9]{7,40}$/', $sha ) ) {
		return $proof;
	}

	$proof['sha']         = $sha;
	$proof['deployed_at'] = (int) filemtime( $marker );

	$file = $dir . '.nexus-deploy-proof.json';
	$data = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null;
	if ( ! is_array( $data ) || ( $data['sha'] ?? '' ) !== $sha ) {
		return $proof;
	}

	$deployed = isset( $data['deployed_at'] ) ? strtotime( (string) $data['deployed_at'] ) : false;
	if ( false !== $deployed && $deployed > 0 ) {
		$proof['deployed_at'] = (int) $deployed;
	}

	$pr = (int) ( $data['pull_request'] ?? 0 );
	if ( $pr > 0 ) {
		$proof['pr'] = $pr;
	}

	$passed = (int) ( $data['checks']['passed'] ?? 0 );
	$total  = (int) ( $data['checks']['total'] ?? 0 );
	if ( $total > 0 && $passed >= 0 && $passed <= $total ) {
		$proof['checks_passed'] = $passed;
		$proof['checks_total']  = $total;
	}

	return $proof;
}

/**
 * Return the revision stamp that page provisioning has to match.
 *
 * @return string
 */
function nexus_get_route_pages_stamp() {
	$sha = nexus_get_deploy_marker_sha();

	return '' !== $sha ? $sha : 'v' . (string) wp_get_theme()->get( 'Version' );
}

/**
 * Return a server-wide advisory lock name scoped to this WordPress database.
 *
 * MySQL/MariaDB named locks are tied to the current DB connection, so they are
 * released automatically if the PHP request dies before init finishes.
 *
 * @return string
 */
function nexus_get_route_pages_lock_name() {
	global $wpdb;

	$db_name = defined( 'DB_NAME' ) ? (string) DB_NAME : '';
	$prefix  = is_object( $wpdb ) && isset( $wpdb->prefix ) ? (string) $wpdb->prefix : '';

	return 'nexus_route_pages_' . substr( hash( 'sha256', $db_name . '|' . $prefix ), 0, 32 );
}

/**
 * Acquire the provisioning lock without waiting.
 *
 * Prefer a MySQL/MariaDB advisory lock because it is atomic across PHP workers
 * and cannot become permanently orphaned when a request is terminated. If the
 * database does not expose GET_LOCK(), fall back to WordPress's atomic
 * add_option() insert for the duration of this request.
 *
 * @return bool
 */
function nexus_acquire_route_pages_lock() {
	if ( ! empty( $GLOBALS['nexus_route_pages_lock_owner'] ) ) {
		return true;
	}

	global $wpdb;

	$lock_name = nexus_get_route_pages_lock_name();

	if ( is_object( $wpdb ) && method_exists( $wpdb, 'prepare' ) && method_exists( $wpdb, 'get_var' ) ) {
		$result = $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 0)', $lock_name ) );

		if ( null !== $result ) {
			if ( 1 !== (int) $result ) {
				return false;
			}

			$GLOBALS['nexus_route_pages_lock_owner'] = [
				'driver' => 'mysql',
				'name'   => $lock_name,
			];

			if ( empty( $GLOBALS['nexus_route_pages_lock_shutdown_registered'] ) ) {
				register_shutdown_function( 'nexus_release_route_pages_lock' );
				$GLOBALS['nexus_route_pages_lock_shutdown_registered'] = true;
			}

			return true;
		}
	}

	$option_name = 'nexus_route_pages_lock';
	$token       = nexus_get_route_pages_stamp() . '|' . uniqid( '', true );

	if ( ! add_option( $option_name, $token, '', false ) ) {
		return false;
	}

	$GLOBALS['nexus_route_pages_lock_owner'] = [
		'driver' => 'option',
		'name'   => $option_name,
		'token'  => $token,
	];

	if ( empty( $GLOBALS['nexus_route_pages_lock_shutdown_registered'] ) ) {
		register_shutdown_function( 'nexus_release_route_pages_lock' );
		$GLOBALS['nexus_route_pages_lock_shutdown_registered'] = true;
	}

	return true;
}

/**
 * Release the provisioning lock owned by this request.
 *
 * @return void
 */
function nexus_release_route_pages_lock() {
	$owner = $GLOBALS['nexus_route_pages_lock_owner'] ?? null;

	if ( ! is_array( $owner ) || empty( $owner['driver'] ) || empty( $owner['name'] ) ) {
		return;
	}

	global $wpdb;

	if ( 'mysql' === $owner['driver'] ) {
		if ( is_object( $wpdb ) && method_exists( $wpdb, 'prepare' ) && method_exists( $wpdb, 'get_var' ) ) {
			$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $owner['name'] ) );
		}
	} elseif ( 'option' === $owner['driver'] && ! empty( $owner['token'] ) ) {
		$current = get_option( $owner['name'] );

		if ( is_string( $current ) && hash_equals( (string) $owner['token'], $current ) ) {
			delete_option( $owner['name'] );
		}
	}

	unset( $GLOBALS['nexus_route_pages_lock_owner'] );
}

/**
 * Whether page provisioning runs in this request.
 *
 * Provisioning runs once per deployed revision (deploy marker, else theme
 * version) on the first regular request that wins the cross-request lock.
 * Concurrent requests skip provisioning entirely and therefore cannot insert
 * the same route page more than once. Cron, AJAX and installs never consume
 * the stamp.
 *
 * @return bool
 */
function nexus_route_pages_ensure_due() {
	static $due = null;

	if ( null === $due ) {
		$due = ! ( wp_installing() || wp_doing_ajax() || wp_doing_cron() )
			&& get_option( 'nexus_route_pages_stamp' ) !== nexus_get_route_pages_stamp()
			&& nexus_acquire_route_pages_lock();
	}

	return $due;
}

/**
 * Store the stamp after every provisioning hook of this request has run.
 *
 * Only the request that acquired the provisioning lock reaches this branch.
 * inside the same critical section.
 *
 * @return void
 */
function nexus_mark_route_pages_ensured() {
	if ( ! nexus_route_pages_ensure_due() ) {
		return;
	}

	update_option( 'nexus_route_pages_stamp', nexus_get_route_pages_stamp(), true );
	nexus_release_route_pages_lock();
}
add_action( 'init', 'nexus_mark_route_pages_ensured', 99 );

/**
 * Pages the theme provisions on their canonical slug with their template.
 *
 * `legacy` slugs are renamed before a new page is inserted, so page IDs stay
 * attached to menus and internal references.
 *
 * @return array<int, array{slug: string, legacy?: array<int, string>, title: string, excerpt: string, template: string}>
 */
function nexus_get_provisioned_pages() {
	return [
		[
			'slug'     => 'technisches-seo-wordpress',
			'title'    => 'Technisches SEO für WordPress',
			'excerpt'  => 'Technischer SEO-Befund und vereinbarte Korrekturen für bestehende WordPress-Websites. Prüfumfang, Preis und Abnahme stehen vor dem Start fest.',
			'template' => 'page-technisches-seo-wordpress.php',
		],
		[
			'slug'     => 'wordpress-website-uebernehmen',
			'title'    => 'WordPress-Website übernehmen und weiterentwickeln',
			'excerpt'  => 'Bestehende WordPress-Website prüfen: Übernahme-Check mit schriftlichem Befund, danach gezielte Weiterentwicklung mit vereinbartem Umfang.',
			'template' => 'page-wordpress-website-uebernehmen.php',
		],
		[
			'slug'     => 'referenzen',
			'title'    => 'WordPress-Referenzen und Projekte',
			'excerpt'  => 'Ausgewählte WordPress-Projekte mit Ausgangslage, eigenem Beitrag und öffentlich prüfbaren Websites.',
			'template' => 'page-referenzen.php',
		],
		[
			'slug'     => 'solar-waermepumpen-leadgenerierung',
			'legacy'   => [ 'website-fuer-solar-und-waermepumpen-anbieter' ],
			'title'    => 'Leadgenerierung für Solar- und Wärmepumpen-Anbieter',
			'excerpt'  => 'B2B-Landingpage für Solar-, Wärmepumpen- und Speicher-Anbieter mit Website-, Tracking- und Conversion-Fokus.',
			'template' => 'page-solar-waermepumpen-leadgenerierung.php',
		],
		[
			'slug'     => 'hasim-uener',
			'legacy'   => [ 'uber-mich' ],
			'title'    => 'Haşim Üner',
			'excerpt'  => 'Personenseite: Stationen, Kompetenzen und Arbeitsweise von Haşim Üner.',
			'template' => 'page-hasim-uener.php',
		],
		[
			'slug'     => 'solar-leads-kaufen-alternative',
			'title'    => 'Solar Leads kaufen – Alternative: eigenes Anfragesystem',
			'excerpt'  => 'Intercept-Landingpage für Suchintent „Solar Leads kaufen": Argumentation für eigene Anfragesysteme statt Portal-Leads.',
			'template' => 'page-solar-leads-kaufen-alternative.php',
		],
		[
			'slug'     => 'waermepumpen-leads',
			'title'    => 'Wärmepumpen Leads – Alternative: eigenes Anfragesystem',
			'excerpt'  => 'Intercept-Landingpage für Suchintent „Wärmepumpen Leads kaufen": Argumentation für eigene Anfragesysteme statt Portal-Leads.',
			'template' => 'page-waermepumpen-leads.php',
		],
		[
			'slug'     => 'solar-leads-kosten-studie',
			'title'    => 'Solar-Leads-Kosten: CPL und Kosten pro Auftrag',
			'excerpt'  => 'Kosten pro Solar-Anfrage und Auftrag: CPL, Abschlussquote, Vertriebszeit und Berechnung mit eigenen Zahlen.',
			'template' => 'page-solar-leads-kosten-studie.php',
		],
		[
			'slug'     => 'server-side-tracking-b2b',
			'title'    => 'Server-Side Tracking für B2B-Leadgenerierung',
			'excerpt'  => 'Server-Side Tracking auf eigenem Server: GA4, Meta CAPI, Consent Mode v2 für B2B-Anfragesysteme.',
			'template' => 'page-server-side-tracking-b2b.php',
		],
		[
			'slug'     => 'b2b-solar-leads',
			'title'    => 'B2B Solar Leads – gewerbliche Photovoltaik-Anfragesysteme',
			'excerpt'  => 'Anfragesysteme für gewerbliche Photovoltaik, Speicher und PPA. Buying-Center-tauglicher Funnel statt B2C-Mengen-Leads.',
			'template' => 'page-b2b-solar-leads.php',
		],
		[
			'slug'     => 'landingpage-erstellen-lassen',
			'title'    => 'Landingpage erstellen lassen',
			'excerpt'  => 'Landingpage zum Festpreis: eine WordPress-Seite für ein Angebot mit Konzept, Text, Anfrageformular, seitenbezogener Herkunfts- und Conversion-Erfassung und Abnahme.',
			'template' => 'page-landingpage-erstellen-lassen.php',
		],
		[
			'slug'     => 'conversion-optimierung',
			'title'    => 'Conversion-Optimierung für B2B',
			'excerpt'  => 'Conversion-Optimierung für B2B-Websites mit wenig Traffic: schriftlicher Befund der Anfragestrecke, danach Umsetzung zu festen Preisen.',
			'template' => 'page-conversion-optimierung.php',
		],
		[
			'slug'     => 'wordpress-website-erstellen-lassen',
			'title'    => 'WordPress-Website erstellen lassen',
			'excerpt'  => 'WordPress-Website zum Festpreis: Neubau oder Relaunch auf einer Testumgebung, live nach Ihrer Abnahme. Jede alte URL bekommt eine Weiterleitung.',
			'template' => 'page-wordpress-website-erstellen-lassen.php',
		],
		[
			'slug'     => 'wordpress-wartung',
			'title'    => 'WordPress-Wartung mit Anfrage-Test',
			'excerpt'  => 'Monatliche WordPress-Wartung mit echter Testanfrage: Updates, Backups und die Prüfung, ob Anfragen ankommen und gemessen werden.',
			'template' => 'page-wordpress-wartung.php',
		],
	];
}

/**
 * Ensure one provisioned page exists on its slug with its template.
 *
 * @param array{slug: string, legacy?: array<int, string>, title: string, excerpt: string, template: string} $page Page definition.
 * @return void
 */
function nexus_ensure_provisioned_page( array $page ) {
	$page_id = nexus_get_page_id( [ $page['slug'] ] );

	if ( ! $page_id ) {
		foreach ( $page['legacy'] ?? [] as $legacy_slug ) {
			$legacy_page = get_page_by_path( $legacy_slug );

			if ( $legacy_page instanceof WP_Post ) {
				$page_id = wp_update_post(
					wp_slash(
						[
							'ID'        => (int) $legacy_page->ID,
							'post_name' => $page['slug'],
						]
					),
					true
				);
				break;
			}
		}
	}

	if ( ! $page_id ) {
		$page_id = wp_insert_post(
			wp_slash(
				[
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $page['title'],
					'post_name'    => $page['slug'],
					'post_content' => '',
					'post_excerpt' => $page['excerpt'],
				]
			),
			true
		);
	}

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return;
	}

	if ( $page['template'] !== (string) get_post_meta( (int) $page_id, '_wp_page_template', true ) ) {
		update_post_meta( (int) $page_id, '_wp_page_template', $page['template'] );
	}
}

/**
 * Provision every page from nexus_get_provisioned_pages() once per deploy.
 *
 * @return void
 */
function nexus_maybe_ensure_provisioned_pages() {
	if ( ! nexus_route_pages_ensure_due() ) {
		return;
	}

	foreach ( nexus_get_provisioned_pages() as $page ) {
		nexus_ensure_provisioned_page( $page );
	}
}
add_action( 'init', 'nexus_maybe_ensure_provisioned_pages', 27 );

/**
 * Ensure the anonymized methodology case lives on the anonymized slug.
 *
 * Redirect map, canonicals and internal links already target
 * /case-study-solar-leadgenerierung/, but the editor-owned page keeps its
 * stored post_name until it is migrated here. Without the rename the URL
 * resolver falls back to /e3-new-energy/ and the legacy 301 stays a
 * self-referencing no-op. Rename only, never insert: an intentionally
 * unpublished case must not resurface through provisioning.
 *
 * @return void
 */
function nexus_maybe_ensure_case_study_page() {
	if ( ! nexus_route_pages_ensure_due() ) {
		return;
	}

	$case_page_id = nexus_get_page_id( [ 'case-study-solar-leadgenerierung' ] );
	if ( $case_page_id ) {
		if ( 'page-e3-new-energy.php' === get_post_meta( (int) $case_page_id, '_wp_page_template', true ) ) {
			update_post_meta( (int) $case_page_id, '_wp_page_template', 'page-case-study-solar.php' );
		}
		return;
	}

	$legacy_page = null;

	foreach ( [ 'e3-new-energy', 'case-e3' ] as $legacy_slug ) {
		$legacy_page = get_page_by_path( $legacy_slug );

		if ( $legacy_page instanceof WP_Post ) {
			break;
		}
	}

	if ( ! ( $legacy_page instanceof WP_Post ) ) {
		return;
	}

	$page_id = wp_update_post(
		wp_slash(
			[
				'ID'        => (int) $legacy_page->ID,
				'post_name' => 'case-study-solar-leadgenerierung',
			]
		),
		true
	);

	if ( is_wp_error( $page_id ) ) {
		return;
	}

	update_post_meta( (int) $page_id, '_wp_page_template', 'page-case-study-solar.php' );
}
add_action( 'init', 'nexus_maybe_ensure_case_study_page', 27 );

/**
 * Map protected legacy entry slugs to their canonical targets.
 *
 * @return array<string, string>
 */
function nexus_get_consolidated_solar_redirect_map() {
	$energy_url  = nexus_get_energy_systems_url();
	$compare_url = home_url( '/solar-leads-kaufen-alternative/' );
	$cost_url    = home_url( '/solar-leads-kosten-studie/' );

	return [
		'/kunden-gewinnen-solarteure/'        => $energy_url,
		'/lead-funnel-solar/'                => $energy_url . '#strecke',
		'/eigene-leadgenerierung-vs-portale/' => $compare_url . '#vergleich',
		'/cost-per-lead-photovoltaik/'        => $cost_url . '#cpo',
		'/qualifizierte-pv-anfragen/'         => $compare_url . '#qualitaet',
	];
}

/**
 * Resolve consolidated routes, including stored templates and page-ID URLs.
 *
 * @param string $request_path Normalized current request path.
 * @return string Final URL, or an empty string for a retained route.
 */
function nexus_get_consolidated_solar_redirect_url( $request_path ) {
	foreach ( nexus_get_consolidated_solar_redirect_map() as $source => $target ) {
		$slug = trim( $source, '/' );
		if ( $source !== $request_path && ! is_page( $slug ) && ! is_page_template( 'page-' . $slug . '.php' ) ) {
			continue;
		}
		if ( trailingslashit( (string) wp_parse_url( $target, PHP_URL_PATH ) ) === $request_path ) {
			return '';
		}
		return remove_query_arg(
			[ 'page_id', 'p', 'name', 'pagename' ],
			nexus_append_current_query_to_redirect_url( $target )
		);
	}
	return '';
}

/**
 * Return protected legacy entry paths and their final destinations.
 *
 * @return array<string, string>
 */
function nexus_get_legacy_offer_redirect_map() {
	$agentur_url = nexus_get_primary_public_url( 'agentur', home_url( '/wordpress-agentur-hannover/' ) );
	$request_url = nexus_get_primary_request_url();
	$energy_url  = nexus_get_energy_systems_url();
	$portal_url  = home_url( '/solar-leads-kaufen-alternative/' );
	$case_study_url = nexus_get_primary_public_url( 'e3', home_url( '/case-study-solar-leadgenerierung/' ) );
	// Aufgeloest statt hart verdrahtet: solange der Seeder die Seite noch nicht
	// umbenannt hat, zeigt der Resolver auf /uber-mich/ — der Pfadvergleich in
	// nexus_redirect_legacy_offer_paths() unterdrueckt die Weiterleitung dann,
	// statt in eine Schleife oder auf eine 404 zu laufen.
	$about_url   = nexus_get_primary_public_url( 'about', home_url( '/hasim-uener/' ) );
	$solar_alternative_url = nexus_get_primary_public_url( 'solar_leads_alternative', home_url( '/solar-leads-kaufen-alternative/' ) );
	$whitelabel_url = function_exists( 'nexus_get_whitelabel_page_url' )
		? nexus_get_whitelabel_page_url()
		: home_url( '/whitelabel-retainer/' );

	$meta_ads_url  = home_url( '/meta-ads-fuer-b2b/' );
	$meta_ads_post = get_page_by_path( 'meta-ads-fuer-b2b', 'OBJECT', 'post' );
	if ( $meta_ads_post instanceof WP_Post ) {
		$resolved_meta_ads_url = get_permalink( $meta_ads_post );
		if ( is_string( $resolved_meta_ads_url ) && '' !== $resolved_meta_ads_url ) {
			$meta_ads_url = $resolved_meta_ads_url;
		}
	}

	return array_merge( [
		// High-probability external entry paths. Internal/historical tool,
		// Content and query ownership moved to / by the homepage consolidation.
		'/wordpress-freelancer-hannover/' => home_url( '/' ),
		'/growth-audit/'             => $request_url,
		'/audit/'                    => $request_url,
		'/customer-journey-audit/'   => $request_url,
		'/360-audit/'                => $request_url,
		'/wordpress-tech-audit/'     => $request_url,
		'/wordpress-agentur/'        => $agentur_url,
		'/alle-loesungen-im-detail/' => nexus_get_page_url( [ 'alle-loesungen' ], home_url( '/alle-loesungen/' ) ),
		// Die alte Service-Landingpage existiert nicht mehr. Der redaktionelle B2B-Beitrag
		// bleibt der fachlich naechste Inhalt; sein Permalink wird dynamisch aufgeloest.
		'/meta-ads/'                 => $meta_ads_url,
		// Personenseite: alter Slug mit Umlaut-Ersatz auf die ASCII-Route.
		'/uber-mich/'                => $about_url,
		// Anonymized case study: old company-named slugs redirect to the anonymized slug.
		'/e3-new-energy/'              => $case_study_url,
		'/case-e3/'                    => $case_study_url,
		'/case-studies/e3-new-energy/' => $case_study_url,
		// Retired (Entwurf) Blog-Slug: 301 auf die Solar-Money-Page, damit
		// nachlaufende Impressionen/Backlinks das Hub-Signal stützen statt zu
		// kannibalisieren oder ins Leere (404) zu laufen.
		'/photovoltaik-leads-kaufen-alternative/' => $energy_url,
		// Historische Kategorie, die im Live-Log weiterhin von Googlebot
		// gecrawlt wurde, aber aktuell als 404 endet.
		'/category/owned-leads/'                  => $portal_url,
		// Belegte Kannibalisierung, nicht nur Stille: Der Pillar-Post rankte im
		// GSC-Export 28d (2026-07-30) auf Pos. 45-81 fuer "leadgenerierung
		// photovoltaik", "waermepumpen leads", "solar leads" und "leads solar" —
		// vier Queries, die docs/seo/query-ownership.csv anderen Seiten zuschreibt.
		// Er selbst besitzt keine Query. Das Ziel ist der Owner von "solar leads
		// kaufen" und damit der naechstliegende Intent.
		'/solar-leads-kaufen-lohnt-sich/'         => $solar_alternative_url,
		// Alter Slug desselben Posts. Er zeigt ueber
		// hu_blog_pillar_redirect_legacy_slugs() (Prioritaet 6) auf
		// /solar-leads-kaufen-lohnt-sich/ und liefe ab jetzt in eine Kette. Dieser
		// Eintrag laeuft auf Prioritaet 2 und damit vorher, also direkt aufs Endziel.
		'/photovoltaik-leads-tco-rechnung/'       => $solar_alternative_url,
		// Der technische Agentur-Stack ist als eigener Absatz in die
		// White-Label-Seite gewandert (Infrastrukturwechsel als Einwand, dazu
		// zwei FAQ). Die Seite trug 16 Impressionen und 0 Klicks in 90 Tagen
		// (Export 2026-09-01) und besitzt laut query-ownership.csv keine Query.
		'/stack-agentur/'                         => $whitelabel_url,
		// Der Solar-Stack beschrieb dieselben fuenf Schichten, die die Money Page
		// als fuenf Stationen fuehrt — dort mit Gegenrechnung und konkreten
		// Zahlen. 31 Impressionen, 0 Klicks in 90 Tagen (Export 2026-09-01),
		// keine eigene Query in query-ownership.csv.
		'/stack-solar/'                           => $energy_url,
	], nexus_get_consolidated_solar_redirect_map() );
}

add_action( 'template_redirect', 'nexus_redirect_legacy_offer_paths', 2 );

/**
 * Return retired public paths that should disappear instead of redirecting.
 *
 * These were internal tool, ROI and service-era slugs. They are intentionally
 * not part of the protected legacy redirect map because they should no longer
 * collect public crawl demand or split canonical signals.
 *
 * @return array<int, string>
 */
function nexus_get_retired_gone_paths() {
	return [
		'/audit-linkedin/',
		'/kostenlose-tools/',
		'/tools/',
		'/website-performance-analyse/',
		'/kostenlose-tools/website-performance-analyse/',
		'/tools/website-performance-analyse/',
		'/roi-rechner/',
		'/wordpress-seo-hannover/',
		'/core-web-vitals/',
		'/conversion-rate-optimization/',
		'/wordpress-wartung-hannover/',
		'/seo/',
		'/shopify-wartungsvertrag/',
	];
}

add_action( 'template_redirect', 'nexus_send_retired_gone_paths', 3 );

/**
 * Serve 410 for retired public tool, ROI and service slugs.
 *
 * @return void
 */
function nexus_send_retired_gone_paths() {
	if ( is_admin() || wp_doing_ajax() || is_feed() ) {
		return;
	}

	$current_path = nexus_get_current_request_path();

	if ( ! in_array( $current_path, nexus_get_retired_gone_paths(), true ) ) {
		return;
	}

	global $wp_query;

	if ( is_object( $wp_query ) && method_exists( $wp_query, 'set_404' ) ) {
		$wp_query->set_404();
	}

	status_header( 410 );
	nocache_headers();

	if ( ! headers_sent() ) {
		header( 'X-Robots-Tag: noindex, nofollow', true );
	}

	$template = get_query_template( '404' );

	if ( $template ) {
		include $template;
		exit;
	}

	wp_die( esc_html__( 'Diese Seite ist nicht mehr verfügbar.', 'blocksy-child' ), esc_html__( 'Nicht mehr verfügbar', 'blocksy-child' ), [ 'response' => 410 ] );
}

/**
 * Preserve the current request query string when redirecting legacy URLs.
 *
 * @param string $target_url Absolute redirect target.
 * @return string
 */
function nexus_append_current_query_to_redirect_url( $target_url ) {
	$query_string = isset( $_SERVER['QUERY_STRING'] ) ? trim( (string) wp_unslash( $_SERVER['QUERY_STRING'] ) ) : '';

	if ( '' === $query_string ) {
		return $target_url;
	}

	$fragment = '';
	$base_url  = $target_url;

	if ( false !== strpos( $target_url, '#' ) ) {
		$parts    = explode( '#', $target_url, 2 );
		$base_url = $parts[0];
		$fragment = '#' . $parts[1];
	}

	$separator = false === strpos( $base_url, '?' ) ? '?' : '&';

	return $base_url . $separator . $query_string . $fragment;
}

/**
 * Retain campaign context without carrying WordPress routing back to the old post.
 * A redirect to /?page_id=OLD would otherwise resolve the retired page again.
 *
 * @return string
 */
function nexus_get_freelancer_redirect_url() {
	return remove_query_arg(
		[ 'page_id', 'p', 'pagename', 'name', 'attachment', 'attachment_id', 'post_type', 'page', 'paged', 'feed', 'preview', 'preview_id', 'preview_nonce', 'rest_route', 's' ],
		nexus_append_current_query_to_redirect_url( home_url( '/' ) )
	);
}

/**
 * Redirect protected legacy entry slugs to their current canonical destinations.
 *
 * @return void
 */
function nexus_redirect_legacy_offer_paths() {
	if ( is_admin() || wp_doing_ajax() || is_feed() ) {
		return;
	}

	$current_path = nexus_get_current_request_path();
	// Also handle ?page_id= and legacy template assignments before redirect_canonical.
	$is_retired_freelancer = '/wordpress-freelancer-hannover/' === $current_path
		|| ( ! is_front_page() && ( is_page( 'wordpress-freelancer-hannover' ) || is_page_template( 'page-wordpress-freelancer-hannover.php' ) ) );
	if ( $is_retired_freelancer ) {
		wp_safe_redirect( nexus_get_freelancer_redirect_url(), 301 );
		exit;
	}

	$solar_target = nexus_get_consolidated_solar_redirect_url( $current_path );
	if ( '' !== $solar_target ) {
		wp_safe_redirect( $solar_target, 301 );
		exit;
	}

	$redirect_map = nexus_get_legacy_offer_redirect_map();

	if ( empty( $redirect_map[ $current_path ] ) ) {
		return;
	}

	$target_url  = $redirect_map[ $current_path ];
	$target_path = trailingslashit( '/' . ltrim( (string) wp_parse_url( $target_url, PHP_URL_PATH ), '/' ) );

	if ( $target_path === $current_path ) {
		return;
	}

	wp_safe_redirect( nexus_append_current_query_to_redirect_url( $target_url ), 301 );
	exit;
}

/**
 * Resolve the primary contact page ID while supporting the legacy slug.
 *
 * @return int
 */
function nexus_get_contact_page_id() {
	$template_page_id = nexus_get_page_id_by_template( 'page-kontakt.php' );

	if ( $template_page_id ) {
		return $template_page_id;
	}

	return nexus_get_page_id( [ 'kontakt', 'kontaktiere-mich' ] );
}

/**
 * Resolve the primary contact URL while enforcing the new /kontakt/ path.
 *
 * @return string
 */
function nexus_get_contact_url() {
	$page_id = nexus_get_contact_page_id();

	if ( $page_id ) {
		$permalink = get_permalink( $page_id );
		$path      = trailingslashit( '/' . ltrim( (string) wp_parse_url( $permalink, PHP_URL_PATH ), '/' ) );

		if ( '/kontaktiere-mich/' !== $path ) {
			return $permalink;
		}
	}

	return home_url( '/kontakt/' );
}

/**
 * Determine whether the current request is the public contact page.
 *
 * @return bool
 */
function nexus_is_contact_page() {
	$contact_page_id = nexus_get_contact_page_id();
	$contact_path    = trailingslashit( '/' . ltrim( (string) wp_parse_url( home_url( '/kontakt/' ), PHP_URL_PATH ), '/' ) );

	if ( $contact_page_id && is_page( $contact_page_id ) ) {
		return true;
	}

	return $contact_path === nexus_get_current_request_path()
		|| is_page_template( 'page-kontakt.php' )
		|| is_page( 'kontakt' )
		|| is_page( 'kontaktiere-mich' );
}

/**
 * Determine whether the large footer audit CTA should be hidden on the current request.
 *
 * Hide the button on pages that already end with a dedicated audit CTA block.
 *
 * @return bool
 */
function nexus_should_hide_footer_primary_cta() {
	if ( (bool) get_query_var( 'nexus_hide_footer_primary_cta', false ) ) {
		return true;
	}

	if ( is_home() ) {
		return true;
	}

	if ( function_exists( 'nexus_is_service_cluster_page' ) && nexus_is_service_cluster_page() ) {
		return true;
	}

	$results_page_id = nexus_get_results_page_id();

	if ( $results_page_id && is_page( $results_page_id ) ) {
		return true;
	}

	$page_templates = [
		'page-hasim-uener.php',
		'page-wordpress-agentur.php',
		'page-wordpress-agentur-hannover.php',
		'page-ergebnisse.php',
		'page-case-studies-e-commerce.php',
		'page-case-study-solar.php',
		'page-solar-waermepumpen-leadgenerierung.php',
		'page-solar-leads-kaufen-alternative.php',
		'page-server-side-tracking-b2b.php',
		'page-b2b-solar-leads.php',
		'page-whitelabel-retainer.php',
	];

	foreach ( $page_templates as $page_template ) {
		if ( is_page_template( $page_template ) ) {
			return true;
		}
	}

	return is_page(
		[
			'hasim-uener',
			'wordpress-agentur',
			'wordpress-agentur-hannover',
			'case-studies-e-commerce',
			'case-studies',
			'ergebnisse',
			'case-study-solar-leadgenerierung',
			'e3-new-energy',
			'case-study-domdar',
			'domdar',
			'ki-integration-wordpress',
			'solar-waermepumpen-leadgenerierung',
			'website-fuer-solar-und-waermepumpen-anbieter',
			'solar-leads-kaufen-alternative',
			'server-side-tracking-b2b',
			'b2b-solar-leads',
			'loesungen',
		]
	);
}

add_action( 'template_redirect', 'nexus_redirect_legacy_results_path', 1 );
/**
 * Redirect legacy proof overview paths to the case study.
 *
 * Seit 2026-09-25 gehoert /ergebnisse/ selbst dazu. Alle drei Pfade gehen in
 * einem Schritt auf nexus_get_results_url(), also ohne Kette ueber den
 * stillgelegten Hub. Prioritaet 1 laeuft vor redirect_canonical, deshalb
 * landet auch /ergebnisse ohne Schraegstrich direkt am Ziel. Die
 * Weiterleitung greift unabhaengig davon, ob die Seite im Editor
 * veroeffentlicht oder ein Entwurf ist.
 *
 * @return void
 */
function nexus_redirect_legacy_results_path() {
	if ( is_admin() || wp_doing_ajax() || is_feed() ) {
		return;
	}

	$current_path = nexus_get_current_request_path();
	$legacy_paths = [
		'/ergebnisse/',
		'/case-studies/',
		'/case-studies-e-commerce/',
	];

	if ( ! in_array( $current_path, $legacy_paths, true ) ) {
		return;
	}

	$target_url = nexus_get_results_url();
	$target_path = trailingslashit( '/' . ltrim( (string) wp_parse_url( $target_url, PHP_URL_PATH ), '/' ) );

	if ( $target_path === $current_path ) {
		return;
	}

	// Query-String wie in der Legacy-Tabelle mitnehmen, damit alte
	// Kampagnenlinks ihre UTM-Parameter behalten.
	wp_safe_redirect( nexus_append_current_query_to_redirect_url( $target_url ), 301 );
	exit;
}

add_action( 'template_redirect', 'nexus_redirect_legacy_energy_systems_path', 1 );
/**
 * Redirect the deprecated energy landing slug to the canonical path.
 *
 * @return void
 */
function nexus_redirect_legacy_energy_systems_path() {
	if ( is_admin() || wp_doing_ajax() || is_feed() ) {
		return;
	}

	$current_path = nexus_get_current_request_path();

	if ( '/website-fuer-solar-und-waermepumpen-anbieter/' !== $current_path ) {
		return;
	}

	$target_url  = nexus_get_energy_systems_url();
	$target_path = trailingslashit( '/' . ltrim( (string) wp_parse_url( $target_url, PHP_URL_PATH ), '/' ) );

	if ( $target_path === $current_path ) {
		return;
	}

	wp_safe_redirect( $target_url, 301 );
	exit;
}

add_filter( 'template_include', 'nexus_force_results_route_templates', 98 );
/**
 * Force route-specific proof templates even if WordPress pages use another template in admin.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function nexus_force_results_route_templates( $template ) {
	if ( is_admin() || ! is_page() ) {
		return $template;
	}

	$route_templates = [
		'case-studies-e-commerce'   => get_stylesheet_directory() . '/page-ergebnisse.php',
		'ergebnisse'                => get_stylesheet_directory() . '/page-ergebnisse.php',
		'whitelabel-retainer'       => get_stylesheet_directory() . '/page-whitelabel-retainer.php',
		'whitelabel-retainer-proof' => get_stylesheet_directory() . '/page-whitelabel-retainer.php',
		'whitelabel'                => get_stylesheet_directory() . '/page-whitelabel-retainer.php',
	];

	foreach ( $route_templates as $slug => $forced_template ) {
		if ( ! is_page( $slug ) ) {
			continue;
		}

		if ( file_exists( $forced_template ) ) {
			return $forced_template;
		}
	}

	return $template;
}

add_filter( 'template_include', 'nexus_force_energy_systems_route_template', 98 );
/**
 * Force the energy systems route templates even if the page template was changed in admin.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function nexus_force_energy_systems_route_template( $template ) {
	if ( is_admin() || ! is_page() ) {
		return $template;
	}

	$route_templates = [
		'solar-waermepumpen-leadgenerierung'             => get_stylesheet_directory() . '/page-solar-waermepumpen-leadgenerierung.php',
		'solar-leads-kaufen-alternative'                 => get_stylesheet_directory() . '/page-solar-leads-kaufen-alternative.php',
		'server-side-tracking-b2b'                       => get_stylesheet_directory() . '/page-server-side-tracking-b2b.php',
		'b2b-solar-leads'                                => get_stylesheet_directory() . '/page-b2b-solar-leads.php',
	];

	foreach ( $route_templates as $slug => $forced_template ) {
		if ( ! is_page( $slug ) ) {
			continue;
		}

		if ( file_exists( $forced_template ) ) {
			return $forced_template;
		}
	}

	return $template;
}
