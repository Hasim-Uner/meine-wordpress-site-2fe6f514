<?php
/**
 * Editorial category archive in the shared Gutachten design system.
 *
 * SEO ownership, deep links and route-aware next steps stay unchanged. The
 * template only replaces the legacy blog shell with the global site header and
 * the same document grammar used by the homepage.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
// Category archives are still excluded from the automatic global-header hook
// while generic legacy archives use the older fallback. Render the canonical
// shared header explicitly until that final compatibility gate is retired.
get_template_part( 'template-parts/site-header' );

$primary_urls       = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$audit_url          = $primary_urls['audit'] ?? ( function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ) );
$energy_url         = $primary_urls['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' );
$agentur_url        = $primary_urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' );
$seo_url            = $primary_urls['seo'] ?? trailingslashit( $agentur_url ) . '#technisches-seo';
$cro_url            = $primary_urls['cro'] ?? trailingslashit( $agentur_url ) . '#methode';
$tracking_url       = home_url( '/server-side-tracking-b2b/' );
$portal_url         = home_url( '/eigene-leadgenerierung-vs-portale/' );
$cpl_url            = home_url( '/cost-per-lead-photovoltaik/' );
$project_url        = function_exists( 'hu_get_navigation_project_request_url' )
	? hu_get_navigation_project_request_url()
	: add_query_arg( [ 'type' => 'project', 'focus' => 'followup_scope' ], home_url( '/kontakt/' ) );
$posts_page_id      = (int) get_option( 'page_for_posts' );
$blog_url           = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' );
$current_category   = get_queried_object();
$current_term_id    = $current_category instanceof WP_Term ? (int) $current_category->term_id : 0;
$current_term_name  = $current_category instanceof WP_Term ? $current_category->name : get_the_archive_title();
$current_term_slug  = $current_category instanceof WP_Term ? $current_category->slug : '';
$current_term_label = function_exists( 'hu_get_public_category_label' )
	? hu_get_public_category_label( $current_category instanceof WP_Term ? $current_category : $current_term_name )
	: $current_term_name;
$category_text      = $current_term_id ? wp_strip_all_tags( category_description( $current_term_id ) ) : '';
$category_seo       = $current_category instanceof WP_Term && function_exists( 'hu_get_category_archive_seo' )
	? hu_get_category_archive_seo( $current_category )
	: [];
$category_intro     = $category_text ?: ( $category_seo['description'] ?? '' );

if ( 'wordpress-growth-agentur' === $current_term_slug && ! empty( $category_seo['description'] ) ) {
	$category_intro = $category_seo['description'];
}

$categories = get_categories(
	[
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 10,
	]
);

$category_deep_link_map = [
	'solar-waermepumpen-anfrage-systeme' => [
		[ 'label' => 'Regionaler Marktcheck', 'url' => $audit_url ],
		[ 'label' => 'Portal vs. eigenes System (TCO)', 'url' => $portal_url ],
		[ 'label' => 'CPL/CPO-Rechnung', 'url' => $cpl_url ],
	],
	'markteinordnung' => [
		[ 'label' => 'TCO-Vergleich 24 Monate', 'url' => $portal_url ],
		[ 'label' => 'CPL/CPO-Rechnung', 'url' => $cpl_url ],
		[ 'label' => 'Regionaler Marktcheck', 'url' => $audit_url ],
	],
	'owned-leads' => [
		[ 'label' => 'Asset-Eigentum Vergleichsmatrix', 'url' => $portal_url ],
		[ 'label' => 'Anfragesysteme', 'url' => $energy_url ],
		[ 'label' => 'Regionaler Marktcheck', 'url' => $audit_url ],
	],
	'sichtbarkeit-daten-conversion' => [
		[ 'label' => 'Technisches SEO', 'url' => $seo_url ],
		[ 'label' => 'Server-Side Tracking', 'url' => $tracking_url ],
		[ 'label' => 'CRO-System', 'url' => $cro_url ],
	],
	'wordpress-growth-agentur' => [
		[ 'label' => 'WordPress Agentur Hannover', 'url' => $agentur_url ],
		[ 'label' => 'Technisches SEO', 'url' => $seo_url ],
		[ 'label' => 'CRO-System', 'url' => $cro_url ],
	],
	'seo' => [
		[ 'label' => 'Technisches SEO', 'url' => $seo_url ],
		[ 'label' => 'WordPress Agentur Hannover', 'url' => $agentur_url ],
	],
	'tracking' => [
		[ 'label' => 'Server-Side Tracking', 'url' => $tracking_url ],
		[ 'label' => 'CRO-System', 'url' => $cro_url ],
	],
	'cro' => [
		[ 'label' => 'CRO-System', 'url' => $cro_url ],
		[ 'label' => 'Technisches SEO', 'url' => $seo_url ],
	],
	'wordpress-performance' => [
		[ 'label' => 'Core Web Vitals', 'url' => $primary_urls['cwv'] ?? home_url( '/wgos-assets/cwv-optimierung/' ) ],
		[ 'label' => 'Technisches SEO', 'url' => $seo_url ],
	],
	'strategie' => [
		[ 'label' => 'Anfragesysteme', 'url' => $energy_url ],
		[ 'label' => 'Strategischer Portal-Vergleich', 'url' => $portal_url ],
	],
];

$category_deep_links = $category_deep_link_map[ $current_term_slug ] ?? [
	[ 'label' => 'Alle Analysen', 'url' => $blog_url ],
];

$energy_categories = [
	'solar-waermepumpen-anfrage-systeme',
	'markteinordnung',
	'owned-leads',
	'strategie',
	'leadgenerierung',
	'performance-marketing',
];
$is_energy_category = in_array( $current_term_slug, $energy_categories, true );

$category_cta = $is_energy_category
	? [
		'title' => 'Von der Analyse zur eigenen Anfrage-Infrastruktur.',
		'text'  => 'Der regionale Marktcheck prüft, ob Zielgebiet, Vertrieb, Website und Projektwerte für ein eigenes Anfragesystem tragfähig sind.',
		'label' => 'Regionalen Marktcheck starten',
		'url'   => $audit_url,
	]
	: [
		'title' => 'Das Thema auf der eigenen Website sauber lösen.',
		'text'  => 'Wenn WordPress, Tracking, SEO oder Conversion nicht isoliert betrachtet werden sollen, reicht eine kurze Beschreibung des aktuellen Problems.',
		'label' => 'Projekt anfragen',
		'url'   => $project_url,
	];
?>

<main id="main" tabindex="-1" class="site-main doku blog-category" data-track-section="category_archive">
	<header class="blatt kopfteil blog-category__kopf" aria-labelledby="category-archive-heading" data-track-section="category_archive_hero">
		<p class="gegenstand">Werkstatt / Kategorie</p>
		<h1 id="category-archive-heading"><?php echo esc_html( $current_term_label ); ?></h1>
		<p class="aufriss">
			<?php
			echo esc_html(
				$category_intro
					? $category_intro
					: 'Analysen, Einordnungen und technische Notizen zu diesem Themenbereich.'
			);
			?>
		</p>

		<?php if ( ! empty( $category_deep_links ) ) : ?>
			<nav class="blog-category__deep" aria-label="<?php esc_attr_e( 'Passende Vertiefungen', 'blocksy-child' ); ?>" data-track-section="category_archive_deep_links">
				<span class="mono">Vertiefen</span>
				<?php foreach ( $category_deep_links as $index => $deep_link ) : ?>
					<?php if ( empty( $deep_link['url'] ) ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<a
						class="textlink"
						href="<?php echo esc_url( $deep_link['url'] ); ?>"
						data-track-action="<?php echo esc_attr( 'category_deep_link_' . ( $index + 1 ) ); ?>"
						data-track-category="internal_link"
					>
						<?php echo esc_html( $deep_link['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
	</header>

	<section id="beitraege" data-track-section="category_archive_list">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true">
					<span class="nr">01</span>
					<span class="titel">Beiträge</span>
					<span class="strich"></span>
				</div>
			</div>

			<div class="voll">
				<p class="mono stempelfarbe">Dossier</p>
				<h2 class="kopf">Alle Arbeiten zu diesem Thema.</h2>

				<?php if ( ! empty( $categories ) ) : ?>
					<nav class="blog-category__filter" aria-label="<?php esc_attr_e( 'Artikel nach Kategorie filtern', 'blocksy-child' ); ?>" data-track-section="category_archive_filter">
						<a class="blog-category__filter-link" href="<?php echo esc_url( $blog_url ); ?>">Alle Analysen</a>
						<?php foreach ( $categories as $category ) : ?>
							<?php
							$category_url = get_category_link( $category->term_id );
							$is_active    = (int) $category->term_id === $current_term_id;

							if ( is_wp_error( $category_url ) ) {
								continue;
							}
							?>
							<a
								class="blog-category__filter-link<?php echo esc_attr( $is_active ? ' is-active' : '' ); ?>"
								href="<?php echo esc_url( $category_url ); ?>"
								<?php if ( $is_active ) : ?>aria-current="page"<?php endif; ?>
							>
								<?php echo esc_html( function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $category ) : $category->name ); ?>
							</a>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>

				<div class="blog-category__list">
					<?php if ( have_posts() ) : ?>
						<?php $article_index = 0; ?>
						<?php while ( have_posts() ) : ?>
							<?php
							the_post();
							$article_index++;
							$post_id          = get_the_ID();
							$post_categories  = get_the_category( $post_id );
							$primary_category = ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ? $post_categories[0] : null;
							$reading_time     = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time( $post_id ) : 0;
							$excerpt          = wp_strip_all_tags( get_the_excerpt() );
							$excerpt          = $excerpt ? wp_trim_words( $excerpt, 30, '…' ) : '';
							?>
							<article class="blog-category__item">
								<a href="<?php echo esc_url( get_permalink() ); ?>" aria-labelledby="category-post-<?php echo esc_attr( (string) $post_id ); ?>">
									<span class="blog-category__index"><?php echo esc_html( sprintf( '%02d', $article_index ) ); ?></span>
									<span class="blog-category__body">
										<span class="blog-category__meta">
											<?php if ( $primary_category instanceof WP_Term ) : ?>
												<span><?php echo esc_html( function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $primary_category ) : $primary_category->name ); ?></span>
											<?php endif; ?>
											<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'd. M Y' ) ); ?></time>
											<?php if ( $reading_time > 0 ) : ?><span><?php echo esc_html( sprintf( '%d Min.', $reading_time ) ); ?></span><?php endif; ?>
										</span>
										<h3 id="category-post-<?php echo esc_attr( (string) $post_id ); ?>"><?php echo esc_html( get_the_title() ); ?></h3>
										<?php if ( '' !== $excerpt ) : ?><span class="blog-category__excerpt"><?php echo esc_html( $excerpt ); ?></span><?php endif; ?>
									</span>
									<span class="blog-category__arrow" aria-hidden="true">→</span>
								</a>
							</article>
						<?php endwhile; ?>
					<?php else : ?>
						<p class="blog-category__empty"><?php esc_html_e( 'In dieser Kategorie sind aktuell keine Beiträge veröffentlicht.', 'blocksy-child' ); ?></p>
					<?php endif; ?>
				</div>

				<nav class="blog-category__pagination" aria-label="<?php esc_attr_e( 'Seiten', 'blocksy-child' ); ?>">
					<?php
					the_posts_pagination(
						[
							'mid_size'  => 1,
							'prev_text' => __( 'Zurück', 'blocksy-child' ),
							'next_text' => __( 'Weiter', 'blocksy-child' ),
						]
					);
					?>
				</nav>
			</div>
		</div>
	</section>

	<section class="abschluss blog-category__abschluss" data-track-section="category_archive_final_cta">
		<div class="blatt">
			<div class="tafel">
				<div class="reihe">
					<div class="spalte-links">
						<p class="mono">Nächster Schritt</p>
					</div>
					<div class="haupt">
						<h2><?php echo esc_html( $category_cta['title'] ); ?></h2>
						<p class="aufriss"><?php echo esc_html( $category_cta['text'] ); ?></p>
						<div class="ausgang">
							<a
								class="tun"
								href="<?php echo esc_url( $category_cta['url'] ); ?>"
								data-track-action="cta_category_archive_next_step"
								data-track-category="lead_gen"
							>
								<?php echo esc_html( $category_cta['label'] ); ?> <span class="pf" aria-hidden="true">→</span>
							</a>
						</div>
					</div>
					<aside class="marg">
						<div class="note">
							<span class="label">Routing</span>
							<?php echo esc_html( $is_energy_category ? 'Energie-Intent bleibt im Marktcheck-Funnel.' : 'Fachlicher Intent führt in die direkte Projektanfrage.' ); ?>
						</div>
					</aside>
				</div>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>