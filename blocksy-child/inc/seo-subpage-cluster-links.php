<?php
/**
 * Solar/B2B-Cluster: kontextuelle Querverlinkung der SEO-Subpages.
 *
 * Die acht Solar-Subpages sammeln Suchnachfrage, hingen aber bislang ohne
 * gegenseitige Kontextlinks im internen Linkgraph (Position 50+). Dieses Modul
 * vernetzt sie untereinander mit exakt benannten Ankern.
 *
 * Eine Quelle der Wahrheit:
 *  - rendert die Links sichtbar vor dem Footer auf den Cluster-Seiten
 *  - liefert dieselben Ziele an das SEO-Cockpit (template-injizierte Kontextlinks)
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
		'solar-leads-kaufen-alternative'    => [ 'label' => 'Solar Leads kaufen – die Alternative', 'path' => '/solar-leads-kaufen-alternative/' ],
		'server-side-tracking-b2b'          => [ 'label' => 'Server-Side Tracking für B2B', 'path' => '/server-side-tracking-b2b/' ],
		'b2b-solar-leads'                   => [ 'label' => 'B2B Solar Leads für PV-Projekte', 'path' => '/b2b-solar-leads/' ],
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
		'solar-leads-kaufen-alternative'    => [ 'solar-leads-kosten-studie', 'cost-per-lead-photovoltaik', 'eigene-leadgenerierung-vs-portale' ],
		'server-side-tracking-b2b'          => [ 'lead-funnel-solar', 'qualifizierte-pv-anfragen', 'cost-per-lead-photovoltaik' ],
		'b2b-solar-leads'                   => [ 'qualifizierte-pv-anfragen', 'solar-leads-kaufen-alternative', 'kunden-gewinnen-solarteure' ],
		'eigene-leadgenerierung-vs-portale' => [ 'solar-leads-kosten-studie', 'cost-per-lead-photovoltaik', 'solar-leads-kaufen-alternative' ],
		'lead-funnel-solar'                 => [ 'qualifizierte-pv-anfragen', 'server-side-tracking-b2b', 'kunden-gewinnen-solarteure' ],
		'kunden-gewinnen-solarteure'        => [ 'solar-leads-kaufen-alternative', 'lead-funnel-solar', 'eigene-leadgenerierung-vs-portale' ],
		'cost-per-lead-photovoltaik'        => [ 'solar-leads-kosten-studie', 'eigene-leadgenerierung-vs-portale', 'solar-leads-kaufen-alternative' ],
		'qualifizierte-pv-anfragen'         => [ 'cost-per-lead-photovoltaik', 'lead-funnel-solar', 'server-side-tracking-b2b' ],
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
 * Greift am `get_footer`-Hook, sodass alle acht Subpages ohne Template-Eingriff
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
	?>
	<section class="related-content seo-cluster-links" aria-label="Weiterführende Themen im Solar-Cluster" data-track-section="solar_cluster_links">
		<div class="related-content__head">
			<span class="related-content__eyebrow"><?php esc_html_e( 'Weiterlesen', 'blocksy-child' ); ?></span>
			<h2 class="related-content__heading"><?php esc_html_e( 'Passende Themen im Solar- & Wärmepumpen-Cluster', 'blocksy-child' ); ?></h2>
		</div>
		<ul class="seo-cluster-links__list">
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
