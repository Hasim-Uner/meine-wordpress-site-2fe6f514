<?php
/**
 * Template Part: Related Content
 *
 * Zeigt verwandte Inhalte (Blog-Posts, Service-Seiten) für
 * interne Verlinkung und das Anfrage-Flywheel.
 *
 * Kann per Parameter gesteuert werden:
 *   set_query_var( 'related_heading', 'Das könnte Sie auch interessieren' );
 *   set_query_var( 'related_count', 3 );
 *   set_query_var( 'related_type', 'post' ); // 'post' | 'page' | 'any'
 *   get_template_part( 'template-parts/related-content' );
 *
 * [Flywheel] template-parts/related-content: Interne Verlinkung stärken
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading       = get_query_var( 'related_heading', __( 'Verwandte Inhalte', 'blocksy-child' ) );
$count         = (int) get_query_var( 'related_count', 3 );
$related_type  = get_query_var( 'related_type', 'post' );
$current_id    = get_the_ID();
$primary_link  = [];
$categories    = 'post' === $related_type && is_singular( 'post' ) ? get_the_category( $current_id ) : [];
$category_ids  = ! empty( $categories ) && ! is_wp_error( $categories ) ? wp_list_pluck( $categories, 'term_id' ) : [];
$category_slugs = ! empty( $categories ) && ! is_wp_error( $categories ) ? wp_list_pluck( $categories, 'slug' ) : [];

// ── Query: verwandte Beiträge aus gleicher Kategorie ──────────────
$args = [
	'post_type'      => $related_type,
	'posts_per_page' => $count,
	'post__not_in'   => [ $current_id ],
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
];

// Für Posts: gleiche Kategorien bevorzugen
if ( 'post' === $related_type && is_singular( 'post' ) && ! empty( $category_ids ) ) {
	$args['category__in'] = array_map( 'absint', $category_ids );

	$primary_link_map = [
		'solar-waermepumpen-anfrage-systeme' => [
			'label' => __( 'Anfrage-Systeme ansehen', 'blocksy-child' ),
			'url'   => function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' ),
			'text'  => __( 'Passender System-Einstieg:', 'blocksy-child' ),
		],
		'markteinordnung' => [
			'label' => __( 'Portalvergleich lesen', 'blocksy-child' ),
			'url'   => home_url( '/eigene-leadgenerierung-vs-portale/' ),
			'text'  => __( 'Für die wirtschaftliche Einordnung:', 'blocksy-child' ),
		],
		'owned-leads' => [
			'label' => __( 'Portalvergleich lesen', 'blocksy-child' ),
			'url'   => home_url( '/eigene-leadgenerierung-vs-portale/' ),
			'text'  => __( 'Wenn es um Eigentum an Nachfrage geht:', 'blocksy-child' ),
		],
		'sichtbarkeit-daten-conversion' => [
			'label' => __( 'Technisches SEO ansehen', 'blocksy-child' ),
			'url'   => function_exists( 'nexus_get_primary_public_url' ) ? nexus_get_primary_public_url( 'seo', home_url( '/wordpress-agentur-hannover/#technisches-seo' ) ) : home_url( '/wordpress-agentur-hannover/#technisches-seo' ),
			'text'  => __( 'Passender Service-Einstieg:', 'blocksy-child' ),
		],
		'seo' => [
			'label' => __( 'Technisches SEO', 'blocksy-child' ),
			'url'   => function_exists( 'nexus_get_primary_public_url' ) ? nexus_get_primary_public_url( 'seo', home_url( '/wordpress-agentur-hannover/#technisches-seo' ) ) : home_url( '/wordpress-agentur-hannover/#technisches-seo' ),
			'text'  => __( 'Passender Service-Einstieg:', 'blocksy-child' ),
		],
		'tracking' => [
			'label' => __( 'Server-Side Tracking', 'blocksy-child' ),
			'url'   => home_url( '/server-side-tracking-b2b/' ),
			'text'  => __( 'Wenn Tracking, Consent oder Datenqualität das eigentliche Problem sind:', 'blocksy-child' ),
		],
		'cro' => [
			'label' => __( 'Conversion-Pfad', 'blocksy-child' ),
			'url'   => function_exists( 'nexus_get_primary_public_url' ) ? nexus_get_primary_public_url( 'cro', home_url( '/wordpress-agentur-hannover/#methode' ) ) : home_url( '/wordpress-agentur-hannover/#methode' ),
			'text'  => __( 'Wenn der nächste Hebel in Angebotslogik und Nutzerführung liegt:', 'blocksy-child' ),
		],
		'wordpress-performance' => [
			'label' => __( 'Core Web Vitals', 'blocksy-child' ),
			'url'   => function_exists( 'nexus_get_primary_public_url' ) ? nexus_get_primary_public_url( 'cwv', home_url( '/wgos-assets/cwv-optimierung/' ) ) : home_url( '/wgos-assets/cwv-optimierung/' ),
			'text'  => __( 'Wenn Ladezeit und technische Reibung im Vordergrund stehen:', 'blocksy-child' ),
		],
		'wordpress-growth-agentur' => [
			'label' => __( 'WordPress Agentur Hannover', 'blocksy-child' ),
			'url'   => function_exists( 'nexus_get_primary_public_url' ) ? nexus_get_primary_public_url( 'agentur', home_url( '/wordpress-agentur-hannover/' ) ) : home_url( '/wordpress-agentur-hannover/' ),
			'text'  => __( 'Für den allgemeinen WordPress-Kontext:', 'blocksy-child' ),
		],
		'strategie' => [
			'label' => __( 'Anfrage-System-Methode', 'blocksy-child' ),
			'url'   => function_exists( 'nexus_get_primary_public_url' ) ? nexus_get_primary_public_url( 'wgos', home_url( '/wordpress-agentur-hannover/#methode' ) ) : home_url( '/wordpress-agentur-hannover/#methode' ),
			'text'  => __( 'Wenn das Thema in ein größeres System eingeordnet werden soll:', 'blocksy-child' ),
		],
	];

	$link_priority = [
		'solar-waermepumpen-anfrage-systeme',
		'markteinordnung',
		'owned-leads',
		'sichtbarkeit-daten-conversion',
		'tracking',
		'seo',
		'cro',
		'wordpress-performance',
		'wordpress-growth-agentur',
		'strategie',
	];

	foreach ( $link_priority as $slug ) {
		if ( in_array( $slug, $category_slugs, true ) && ! empty( $primary_link_map[ $slug ] ) ) {
			$primary_link = $primary_link_map[ $slug ];
			break;
		}
	}
}

// Für Pages: manuelle ACF-Auswahl (falls vorhanden)
if ( 'page' === $related_type && function_exists( 'get_field' ) ) {
	$manual_related = get_field( 'related_pages', $current_id );
	if ( is_array( $manual_related ) && ! empty( $manual_related ) ) {
		$args = [
			'post_type'      => 'page',
			'post__in'       => wp_list_pluck( $manual_related, 'ID' ),
			'posts_per_page' => $count,
			'post_status'    => 'publish',
			'orderby'        => 'post__in',
		];
	}
}

$related_query = new WP_Query( $args );

if ( ! $related_query->have_posts() && empty( $primary_link ) ) {
	wp_reset_postdata();
	return;
}
?>

<section class="related-content" data-track-section="related_content" aria-label="<?php echo esc_attr( $heading ); ?>">
	<div class="related-content__head">
		<span class="related-content__eyebrow"><?php esc_html_e( 'Weiterlesen', 'blocksy-child' ); ?></span>
		<h2 class="related-content__heading"><?php echo esc_html( $heading ); ?></h2>
		<?php if ( ! empty( $primary_link['url'] ) && ! empty( $primary_link['label'] ) ) : ?>
			<p class="related-content__primary-link">
				<?php if ( ! empty( $primary_link['text'] ) ) : ?>
					<?php echo esc_html( (string) $primary_link['text'] ); ?>
					<?php echo ' '; ?>
				<?php endif; ?>
				<a href="<?php echo esc_url( (string) $primary_link['url'] ); ?>" data-track-action="related_primary_service_click" data-track-category="internal_link">
					<?php echo esc_html( (string) $primary_link['label'] ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>

	<?php if ( $related_query->have_posts() ) : ?>
	<div class="related-content__list">
		<?php while ( $related_query->have_posts() ) :
			$related_query->the_post();
			$related_url   = get_permalink();
			$related_title = get_the_title();
		?>
			<article class="related-content__item">
				<div class="related-content__body">
					<?php
					$cats = get_the_category();
					if ( $cats ) :
					?>
						<span class="related-content__category"><?php echo esc_html( $cats[0]->name ); ?></span>
					<?php endif; ?>

					<h3 class="related-content__title">
						<a href="<?php echo esc_url( $related_url ); ?>" data-track-action="related_click" data-track-category="internal_link">
							<?php echo esc_html( $related_title ); ?>
						</a>
					</h3>

					<p class="related-content__excerpt">
						<?php echo esc_html( wp_trim_words( get_the_excerpt(), 15, '…' ) ); ?>
					</p>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
	<?php endif; ?>
</section>

<?php
wp_reset_postdata();
