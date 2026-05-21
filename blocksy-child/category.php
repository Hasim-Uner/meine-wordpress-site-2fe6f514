<?php
/**
 * Minimal editorial category archive template.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
get_template_part( 'template-parts/blog-header' );

$primary_urls      = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$audit_url         = $primary_urls['audit'] ?? ( function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ) );
$energy_url        = $primary_urls['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' );
$agentur_url       = $primary_urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' );
$seo_url           = $primary_urls['seo'] ?? trailingslashit( $agentur_url ) . '#technisches-seo';
$cro_url           = $primary_urls['cro'] ?? trailingslashit( $agentur_url ) . '#methode';
$tracking_url      = home_url( '/server-side-tracking-b2b/' );
$portal_url        = home_url( '/eigene-leadgenerierung-vs-portale/' );
$cpl_url           = home_url( '/cost-per-lead-photovoltaik/' );
$posts_page_id     = (int) get_option( 'page_for_posts' );
$blog_url          = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' );
$current_category  = get_queried_object();
$current_term_id   = $current_category instanceof WP_Term ? (int) $current_category->term_id : 0;
$current_term_name = $current_category instanceof WP_Term ? $current_category->name : get_the_archive_title();
$current_term_slug = $current_category instanceof WP_Term ? $current_category->slug : '';
$category_text     = $current_term_id ? wp_strip_all_tags( category_description( $current_term_id ) ) : '';
$category_seo      = $current_category instanceof WP_Term && function_exists( 'hu_get_category_archive_seo' ) ? hu_get_category_archive_seo( $current_category ) : [];
$category_intro    = $category_text ?: ( $category_seo['description'] ?? '' );
$categories        = get_categories(
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
		[ 'label' => 'Portalvergleich', 'url' => $portal_url ],
		[ 'label' => 'CPL/CPO-Rechnung', 'url' => $cpl_url ],
	],
	'markteinordnung' => [
		[ 'label' => 'Portalvergleich', 'url' => $portal_url ],
		[ 'label' => 'CPL/CPO-Rechnung', 'url' => $cpl_url ],
		[ 'label' => 'Regionaler Marktcheck', 'url' => $audit_url ],
	],
	'owned-leads' => [
		[ 'label' => 'Portalvergleich', 'url' => $portal_url ],
		[ 'label' => 'Anfrage-Systeme', 'url' => $energy_url ],
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
		[ 'label' => 'Regionaler Marktcheck', 'url' => $audit_url ],
	],
	'tracking' => [
		[ 'label' => 'Server-Side Tracking', 'url' => $tracking_url ],
		[ 'label' => 'CRO-System', 'url' => $cro_url ],
		[ 'label' => 'Regionaler Marktcheck', 'url' => $audit_url ],
	],
	'cro' => [
		[ 'label' => 'CRO-System', 'url' => $cro_url ],
		[ 'label' => 'Technisches SEO', 'url' => $seo_url ],
		[ 'label' => 'WordPress Agentur Hannover', 'url' => $agentur_url ],
	],
	'wordpress-performance' => [
		[ 'label' => 'Core Web Vitals', 'url' => $primary_urls['cwv'] ?? home_url( '/wgos-assets/cwv-optimierung/' ) ],
		[ 'label' => 'Technisches SEO', 'url' => $seo_url ],
		[ 'label' => 'WordPress Agentur Hannover', 'url' => $agentur_url ],
	],
	'strategie' => [
		[ 'label' => 'Anfrage-Systeme', 'url' => $energy_url ],
		[ 'label' => 'Portalvergleich', 'url' => $portal_url ],
		[ 'label' => 'Regionaler Marktcheck', 'url' => $audit_url ],
	],
];
$category_deep_links = $category_deep_link_map[ $current_term_slug ] ?? [
	[ 'label' => 'Alle Analysen', 'url' => $blog_url ],
	[ 'label' => 'Regionaler Marktcheck', 'url' => $audit_url ],
];
?>

<main id="main" class="site-main blog-bell blog-bell--category hu-hp" data-track-section="category_archive">
	<section class="blog-bell__hero category-bell__hero" aria-labelledby="category-archive-heading" data-track-section="category_archive_hero">
		<div class="blog-bell__container">
			<span class="blog-bell__eyebrow">
				<span class="blog-bell__eyebrow-dot" aria-hidden="true"></span>
				<?php esc_html_e( 'Kategorie', 'blocksy-child' ); ?>
			</span>
			<h1 id="category-archive-heading" class="blog-bell__title category-bell__title">
				<?php echo esc_html( $current_term_name ); ?>
			</h1>
			<p class="blog-bell__lead category-bell__lead">
				<?php
				echo esc_html(
					$category_intro
						? $category_intro
						: 'Analysen mit klarem Fokus auf Priorisierung, Nachfragequalität und den nächsten sinnvollen Schritt.'
				);
				?>
			</p>

			<?php if ( ! empty( $category_deep_links ) ) : ?>
				<nav class="category-bell__deep-links" aria-label="<?php esc_attr_e( 'Passende Vertiefungen', 'blocksy-child' ); ?>" data-track-section="category_archive_deep_links">
					<span class="category-bell__deep-label"><?php esc_html_e( 'Vertiefen', 'blocksy-child' ); ?></span>
					<?php foreach ( $category_deep_links as $index => $deep_link ) : ?>
						<?php if ( empty( $deep_link['url'] ) || empty( $deep_link['label'] ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<a
							class="category-bell__deep-link"
							href="<?php echo esc_url( $deep_link['url'] ); ?>"
							data-track-action="<?php echo esc_attr( 'category_deep_link_' . ( $index + 1 ) ); ?>"
							data-track-category="internal_link"
							data-track-section="category_archive_deep_links"
						>
							<?php echo esc_html( $deep_link['label'] ); ?>
						</a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( ! empty( $categories ) ) : ?>
		<nav class="blog-bell__filter" aria-label="<?php esc_attr_e( 'Artikel nach Kategorie filtern', 'blocksy-child' ); ?>" data-track-section="category_archive_filter">
			<div class="blog-bell__filter-inner">
				<span class="blog-bell__filter-label"><?php esc_html_e( 'Themen', 'blocksy-child' ); ?></span>
				<a
					class="blog-bell__chip"
					href="<?php echo esc_url( $blog_url ); ?>"
					data-track-action="blog_filter_all"
					data-track-category="navigation"
				>
					<?php esc_html_e( 'Alle', 'blocksy-child' ); ?>
				</a>
				<?php foreach ( $categories as $category ) : ?>
					<?php
					$category_url = get_category_link( $category->term_id );
					$is_active    = (int) $category->term_id === $current_term_id;
					if ( is_wp_error( $category_url ) ) {
						continue;
					}
					?>
					<a
						class="blog-bell__chip <?php echo $is_active ? 'is-active' : ''; ?>"
						href="<?php echo esc_url( $category_url ); ?>"
						<?php echo $is_active ? 'aria-current="page"' : ''; ?>
						data-track-action="<?php echo esc_attr( 'blog_filter_' . $category->slug ); ?>"
						data-track-category="navigation"
					>
						<?php echo esc_html( $category->name ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		</nav>
	<?php endif; ?>

	<section class="blog-bell__main" aria-label="<?php esc_attr_e( 'Beiträge dieser Kategorie', 'blocksy-child' ); ?>" data-track-section="category_archive_grid">
		<div class="blog-bell__container">
			<?php if ( have_posts() ) : ?>
				<div class="blog-bell__grid category-bell__grid">
					<?php
					$post_index = 0;
					while ( have_posts() ) :
						the_post();
						++$post_index;

						$post_id          = get_the_ID();
						$post_categories  = get_the_category( $post_id );
						$primary_category = ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ? $post_categories[0] : null;
						$reading_time     = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time( $post_id ) : 0;
						$excerpt          = wp_strip_all_tags( get_the_excerpt() );
						$excerpt          = $excerpt ? wp_trim_words( $excerpt, 28, '...' ) : '';
						$card_classes     = 'blog-bell__card' . ( 1 === $post_index && ! is_paged() ? ' is-featured' : '' );
						?>
						<article class="<?php echo esc_attr( $card_classes ); ?>" data-reveal>
							<a class="blog-bell__card-link" href="<?php echo esc_url( get_permalink() ); ?>" aria-labelledby="category-card-title-<?php echo esc_attr( (string) $post_id ); ?>">
								<header class="blog-bell__card-meta">
									<?php if ( $primary_category instanceof WP_Term ) : ?>
										<span class="blog-bell__card-cat"><?php echo esc_html( $primary_category->name ); ?></span>
									<?php endif; ?>
									<time class="blog-bell__card-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
										<?php echo esc_html( get_the_date( 'd. M Y' ) ); ?>
									</time>
								</header>

								<h2 id="category-card-title-<?php echo esc_attr( (string) $post_id ); ?>" class="blog-bell__card-title">
									<?php echo esc_html( get_the_title() ); ?>
								</h2>

								<?php if ( '' !== $excerpt ) : ?>
									<p class="blog-bell__card-excerpt"><?php echo esc_html( $excerpt ); ?></p>
								<?php endif; ?>

								<footer class="blog-bell__card-footer">
									<span class="blog-bell__card-readtime">
										<?php echo $reading_time > 0 ? esc_html( sprintf( '%d Min.', $reading_time ) ) : esc_html__( 'Lesen', 'blocksy-child' ); ?>
									</span>
									<svg class="blog-bell__card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
										<path d="M5 12h14M13 6l6 6-6 6"></path>
									</svg>
								</footer>
							</a>
						</article>
					<?php endwhile; ?>
				</div>

				<nav class="blog-bell__pagination" aria-label="<?php esc_attr_e( 'Seiten', 'blocksy-child' ); ?>">
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
			<?php else : ?>
				<p class="blog-bell__empty">
					<?php esc_html_e( 'In dieser Kategorie sind aktuell keine Beiträge veröffentlicht.', 'blocksy-child' ); ?>
				</p>
			<?php endif; ?>

			<aside class="blog-bell__bottom-cta category-bell__bottom-cta" aria-labelledby="category-archive-cta-heading" data-track-section="category_archive_final_cta">
				<span class="blog-bell__eyebrow"><?php esc_html_e( 'Nächster Schritt', 'blocksy-child' ); ?></span>
				<h2 id="category-archive-cta-heading" class="blog-bell__bottom-cta-title">
					<?php esc_html_e( 'Lesen ersetzt keine Systemdiagnose.', 'blocksy-child' ); ?>
				</h2>
				<p class="blog-bell__bottom-cta-text">
					<?php esc_html_e( 'Der regionale Marktcheck prüft, ob Projektwerte, Zielgebiet, Vertrieb und Website-Fundament für ein eigenes Anfrage-System tragfähig sind.', 'blocksy-child' ); ?>
				</p>
				<a
					class="blog-bell__bottom-cta-link"
					href="<?php echo esc_url( $audit_url ); ?>"
					data-track-action="cta_category_archive_marktcheck"
					data-track-category="lead_gen"
				>
					<?php esc_html_e( 'Regionalen Marktcheck starten', 'blocksy-child' ); ?>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<path d="M5 12h14M13 6l6 6-6 6"></path>
					</svg>
				</a>
			</aside>
		</div>
	</section>
</main>

<?php get_footer(); ?>
