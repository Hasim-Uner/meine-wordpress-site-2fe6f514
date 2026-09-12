<?php
/**
 * Kontextuelle Querverlinkung fuer SEO-Cluster.
 *
 * Die Solar-Subpages sammeln Suchnachfrage, hingen aber bislang ohne
 * gegenseitige Kontextlinks im internen Linkgraph (Position 50+). Dieses Modul
 * vernetzt sie untereinander mit exakt benannten Ankern. Zusaetzlich verbindet
 * es die Agentur-Money-Page mit technischen Vertiefungen und hält intern
 * erzeugte Links von bekannten Redirect-Quellen fern.
 *
 * Eine Quelle der Wahrheit:
 *  - rendert die Links sichtbar vor dem Footer auf den Cluster-Seiten
 *  - liefert dieselben Ziele an das SEO-Cockpit (template-injizierte Kontextlinks)
 *  - nutzt die zentrale Redirect-Matrix für interne Permalink-Hygiene
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Anker-Registry des Solar/B2B-Clusters: Slug => Label + Pfad.
 *
 * @return array<string, array{label: string, path: string}>
 */
function hu_get_solar_cluster_link_map() {
	return [
		'solar-leads-kaufen-alternative'    => [ 'label' => 'Photovoltaik & Solar Leads kaufen – die Alternative', 'path' => '/solar-leads-kaufen-alternative/' ],
		'waermepumpen-leads'                => [ 'label' => 'Wärmepumpen Leads kaufen – die Alternative', 'path' => '/waermepumpen-leads/' ],
		'b2b-solar-leads'                   => [ 'label' => 'PV-Termine B2B für Gewerbe-PV', 'path' => '/b2b-solar-leads/' ],
		'eigene-leadgenerierung-vs-portale' => [ 'label' => 'Portal-Leads vs. eigenes System (TCO)', 'path' => '/eigene-leadgenerierung-vs-portale/' ],
		'lead-funnel-solar'                 => [ 'label' => 'Lead-Funnel für Solar & Wärmepumpe', 'path' => '/lead-funnel-solar/' ],
		'kunden-gewinnen-solarteure'        => [ 'label' => 'Kunden gewinnen für Solarteure', 'path' => '/kunden-gewinnen-solarteure/' ],
		'cost-per-lead-photovoltaik'        => [ 'label' => 'Cost per Lead Photovoltaik', 'path' => '/cost-per-lead-photovoltaik/' ],
		'qualifizierte-pv-anfragen'         => [ 'label' => 'Qualifizierte PV-Anfragen erkennen', 'path' => '/qualifizierte-pv-anfragen/' ],
		'solar-leads-kosten-studie'         => [ 'label' => 'Solar-Leads-Kosten: Marktstudie DACH', 'path' => '/solar-leads-kosten-studie/' ],
	];
}

/**
 * Kuratiert die thematisch nächsten Geschwister-Seiten je Cluster-Slug.
 *
 * @param string $slug Aktueller Seiten-Slug.
 * @return array<int, string> Liste verwandter Slugs.
 */
function hu_get_solar_cluster_related_slugs( $slug ) {
	$relations = [
		'solar-leads-kaufen-alternative'    => [ 'waermepumpen-leads', 'solar-leads-kosten-studie', 'eigene-leadgenerierung-vs-portale' ],
		'waermepumpen-leads'                => [ 'solar-leads-kaufen-alternative', 'cost-per-lead-photovoltaik', 'eigene-leadgenerierung-vs-portale' ],
		'b2b-solar-leads'                   => [ 'qualifizierte-pv-anfragen', 'solar-leads-kaufen-alternative', 'kunden-gewinnen-solarteure' ],
		'eigene-leadgenerierung-vs-portale' => [ 'solar-leads-kosten-studie', 'cost-per-lead-photovoltaik', 'solar-leads-kaufen-alternative' ],
		'lead-funnel-solar'                 => [ 'qualifizierte-pv-anfragen', 'b2b-solar-leads', 'kunden-gewinnen-solarteure' ],
		'kunden-gewinnen-solarteure'        => [ 'solar-leads-kaufen-alternative', 'lead-funnel-solar', 'eigene-leadgenerierung-vs-portale' ],
		'cost-per-lead-photovoltaik'        => [ 'solar-leads-kosten-studie', 'eigene-leadgenerierung-vs-portale', 'solar-leads-kaufen-alternative' ],
		'qualifizierte-pv-anfragen'         => [ 'cost-per-lead-photovoltaik', 'lead-funnel-solar', 'b2b-solar-leads' ],
		'solar-leads-kosten-studie'         => [ 'cost-per-lead-photovoltaik', 'eigene-leadgenerierung-vs-portale', 'solar-leads-kaufen-alternative' ],
	];

	return isset( $relations[ $slug ] ) ? (array) $relations[ $slug ] : [];
}

/**
 * Aufgelöste verwandte Cluster-Links (Label + absolute URL) für einen Slug.
 *
 * @param string $slug Aktueller Seiten-Slug.
 * @return array<int, array{label: string, url: string}>
 */
function hu_get_solar_cluster_related_links( $slug ) {
	$slug = sanitize_title( (string) $slug );
	$map  = hu_get_solar_cluster_link_map();
	$out  = [];

	foreach ( hu_get_solar_cluster_related_slugs( $slug ) as $related_slug ) {
		if ( isset( $map[ $related_slug ] ) ) {
			$out[] = [
				'label' => (string) $map[ $related_slug ]['label'],
				'url'   => home_url( (string) $map[ $related_slug ]['path'] ),
			];
		}
	}

	return $out;
}

/**
 * Ermittelt den Cluster-Slug der aktuellen Seite, falls vorhanden.
 *
 * @return string Slug oder Leerstring.
 */
function hu_get_current_solar_cluster_slug() {
	if ( is_admin() || ! is_page() ) {
		return '';
	}

	$queried = get_queried_object();

	if ( ! ( $queried instanceof WP_Post ) ) {
		return '';
	}

	$slug = sanitize_title( (string) $queried->post_name );

	return isset( hu_get_solar_cluster_link_map()[ $slug ] ) ? $slug : '';
}

/**
 * Rendert die Cluster-Querverlinkung vor dem Footer der Cluster-Seiten.
 *
 * Greift am `get_footer`-Hook, sodass alle Cluster-Subpages ohne Template-Eingriff
 * abgedeckt sind. Guard verhindert Ausgabe außerhalb des Clusters.
 *
 * @return void
 */
function hu_render_solar_cluster_links() {
	$slug = hu_get_current_solar_cluster_slug();

	if ( '' === $slug ) {
		return;
	}

	$links = hu_get_solar_cluster_related_links( $slug );

	if ( empty( $links ) ) {
		return;
	}

	// Einbahn-Hublink: jede Cluster-Seite verweist mit term-nahem Anker
	// zurück auf die Money Page, um „leadgenerierung photovoltaik" dort zu
	// bündeln (Hub → Cluster existiert bereits über die Vertiefung-Sektion).
	$hub_url = function_exists( 'nexus_get_energy_systems_url' )
		? nexus_get_energy_systems_url()
		: home_url( '/solar-waermepumpen-leadgenerierung/' );
	?>
	<section class="related-content seo-cluster-links" aria-label="Weiterführende Themen im Solar-Cluster" data-track-section="solar_cluster_links">
		<div class="related-content__head">
			<span class="related-content__eyebrow"><?php esc_html_e( 'Weiterlesen', 'blocksy-child' ); ?></span>
			<h2 class="related-content__heading"><?php esc_html_e( 'Passende Themen im Solar- & Wärmepumpen-Cluster', 'blocksy-child' ); ?></h2>
		</div>
		<ul class="seo-cluster-links__list">
			<li class="seo-cluster-links__item seo-cluster-links__item--hub">
				<a class="seo-cluster-links__link seo-cluster-links__link--hub"
				   href="<?php echo esc_url( $hub_url ); ?>"
				   data-track-action="solar_cluster_to_hub_click"
				   data-track-category="internal_link">
					<?php esc_html_e( 'Leadgenerierung für Photovoltaik & Wärmepumpe', 'blocksy-child' ); ?>
				</a>
			</li>
			<?php foreach ( $links as $link ) : ?>
				<li class="seo-cluster-links__item">
					<a class="seo-cluster-links__link"
					   href="<?php echo esc_url( $link['url'] ); ?>"
					   data-track-action="solar_cluster_link_click"
					   data-track-category="internal_link">
						<?php echo esc_html( $link['label'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php
}
add_action( 'get_footer', 'hu_render_solar_cluster_links' );

/**
 * Normalize the central legacy redirect map for internal-link lookups.
 *
 * @return array<string,string> Normalized source path => final absolute URL.
 */
function hu_get_internal_redirect_canonical_map() {
	static $map = null;

	if ( null !== $map ) {
		return $map;
	}

	$map = [];
	if ( ! function_exists( 'nexus_get_legacy_offer_redirect_map' ) ) {
		return $map;
	}

	foreach ( (array) nexus_get_legacy_offer_redirect_map() as $source => $target ) {
		$source_path = (string) wp_parse_url( (string) $source, PHP_URL_PATH );
		if ( '' === $source_path ) {
			$source_path = (string) $source;
		}

		$source_path = '/' . ltrim( $source_path, '/' );
		if ( '/' !== $source_path ) {
			$source_path = trailingslashit( $source_path );
		}

		$target = esc_url_raw( (string) $target );
		if ( '' !== $source_path && '' !== $target ) {
			$map[ $source_path ] = $target;
		}
	}

	return $map;
}

/**
 * Return the final URL when an internal href points at a known redirect source.
 *
 * @param string $url Candidate URL or root-relative href.
 * @return string Final URL, or an empty string when no rewrite applies.
 */
function hu_get_internal_redirect_target( $url ) {
	$url = trim( html_entity_decode( (string) $url, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	if ( '' === $url || 0 === strpos( $url, '#' ) || preg_match( '#^(?:mailto|tel|javascript|data):#i', $url ) ) {
		return '';
	}

	$parts = wp_parse_url( $url );
	if ( ! is_array( $parts ) ) {
		return '';
	}

	$host = strtolower( (string) ( $parts['host'] ?? '' ) );
	$home = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );
	if ( '' !== $host && '' !== $home && $host !== $home ) {
		return '';
	}

	$path = (string) ( $parts['path'] ?? '' );
	if ( '' === $path ) {
		return '';
	}

	$path = '/' . ltrim( $path, '/' );
	if ( '/' !== $path ) {
		$path = trailingslashit( $path );
	}

	$map = hu_get_internal_redirect_canonical_map();
	if ( ! isset( $map[ $path ] ) ) {
		return '';
	}

	$target = (string) $map[ $path ];
	if ( ! empty( $parts['query'] ) && false === strpos( $target, '?' ) ) {
		$target .= '?' . (string) $parts['query'];
	}
	if ( ! empty( $parts['fragment'] ) && false === strpos( $target, '#' ) ) {
		$target .= '#' . rawurlencode( rawurldecode( (string) $parts['fragment'] ) );
	}

	return esc_url_raw( $target );
}

/**
 * Keep frontend-generated WordPress permalinks off known redirect sources.
 *
 * Admin/background permalink resolution intentionally stays untouched because
 * the Site Audit discovers the real published URL inventory there and must
 * continue seeing redirect nodes as redirect nodes.
 *
 * @param string $permalink Generated permalink.
 * @return string
 */
function hu_canonicalize_generated_internal_permalink( $permalink ) {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return $permalink;
	}

	$target = hu_get_internal_redirect_target( (string) $permalink );
	return '' !== $target ? $target : $permalink;
}
add_filter( 'post_link', 'hu_canonicalize_generated_internal_permalink', 20 );
add_filter( 'page_link', 'hu_canonicalize_generated_internal_permalink', 20 );
add_filter( 'post_type_link', 'hu_canonicalize_generated_internal_permalink', 20 );

/**
 * Rewrite exact legacy redirect hrefs inside one editor-owned content blob.
 *
 * @param string $content Post content.
 * @return string
 */
function hu_rewrite_internal_redirect_hrefs( $content ) {
	$content = (string) $content;
	if ( '' === $content || false === stripos( $content, 'href=' ) ) {
		return $content;
	}

	$rewritten = preg_replace_callback(
		'/((?:<a\b[^>]*?)\bhref\s*=\s*)(["\'])([^"\']+)(\2)/i',
		static function ( $match ) {
			$target = hu_get_internal_redirect_target( (string) $match[3] );
			if ( '' === $target ) {
				return (string) $match[0];
			}

			return (string) $match[1] . (string) $match[2] . esc_url( $target ) . (string) $match[4];
		},
		$content
	);

	return is_string( $rewritten ) ? $rewritten : $content;
}

/**
 * One-time migration for stored editor links that still target known redirects.
 *
 * The redirect matrix is authoritative. Only href values whose normalized path
 * exactly matches a current redirect source are touched; visible copy and all
 * other editor content remain editor-owned.
 *
 * @return void
 */
function hu_maybe_migrate_internal_redirect_links() {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = '2026-09-09-1';
	$option_key = 'hu_internal_redirect_link_hygiene_version';
	if ( (string) get_option( $option_key, '' ) === $version ) {
		return;
	}

	$post_types = get_post_types( [ 'public' => true ], 'names' );
	unset( $post_types['attachment'] );

	$ids = get_posts(
		[
			'post_type'              => array_values( $post_types ),
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		]
	);

	$failed = false;
	foreach ( $ids as $post_id ) {
		$post_id = absint( $post_id );
		$content = (string) get_post_field( 'post_content', $post_id, 'raw' );
		if ( '' === $content ) {
			continue;
		}

		$rewritten = hu_rewrite_internal_redirect_hrefs( $content );
		if ( $rewritten === $content ) {
			continue;
		}

		$result = wp_update_post(
			wp_slash(
				[
					'ID'           => $post_id,
					'post_content' => $rewritten,
				]
			),
			true
		);

		if ( is_wp_error( $result ) || ! $result ) {
			$failed = true;
		}
	}

	if ( ! $failed ) {
		update_option( $option_key, $version, false );
	}
}
add_action( 'init', 'hu_maybe_migrate_internal_redirect_links', 45 );
