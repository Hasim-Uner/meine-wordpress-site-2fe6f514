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
$posts_page_id     = (int) get_option( 'page_for_posts' );
$blog_url          = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' );
$current_category  = get_queried_object();
$current_term_id   = $current_category instanceof WP_Term ? (int) $current_category->term_id : 0;
$current_term_name = $current_category instanceof WP_Term ? $current_category->name : get_the_archive_title();
$category_text     = $current_term_id ? wp_strip_all_tags( category_description( $current_term_id ) ) : '';
$categories        = get_categories(
	[
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	]
);
?>

<main id="main" class="site-main blog-editorial blog-editorial--category blog-editorial--with-blog-header">
	<div class="blog-editorial__inner">
		<header class="blog-editorial-hero" aria-labelledby="category-archive-heading" data-track-section="category_archive_hero">
			<span class="blog-editorial-kicker">Kategorie</span>
			<h1 id="category-archive-heading" class="blog-editorial-hero__title">
				<?php echo esc_html( $current_term_name ); ?>
			</h1>
			<p class="blog-editorial-hero__lead">
				<?php
				echo esc_html(
					$category_text
						? $category_text
						: 'Beiträge mit klarem Fokus auf Analyse, Priorisierung und verwertbare Entscheidungen statt Content-Deko.'
				);
				?>
			</p>
		</header>

		<nav class="blog-editorial-filter" aria-label="<?php esc_attr_e( 'Artikel nach Kategorie filtern', 'blocksy-child' ); ?>" data-track-section="category_archive_filter">
			<a
				class="blog-editorial-filter__link"
				href="<?php echo esc_url( $blog_url ); ?>"
				data-track-action="blog_filter_all"
				data-track-category="navigation"
				data-track-section="category_archive_filter"
			>
				Alle
			</a>
			<?php foreach ( $categories as $category ) : ?>
				<?php
				$category_url = get_category_link( $category->term_id );
				$is_active    = (int) $category->term_id === $current_term_id;
				?>
				<?php if ( is_wp_error( $category_url ) ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<a
					class="blog-editorial-filter__link<?php echo esc_attr( $is_active ? ' is-active' : '' ); ?>"
					href="<?php echo esc_url( $category_url ); ?>"
					<?php if ( $is_active ) : ?>
						aria-current="page"
					<?php endif; ?>
					data-track-action="<?php echo esc_attr( 'blog_filter_' . $category->slug ); ?>"
					data-track-category="navigation"
					data-track-section="category_archive_filter"
				>
					<?php echo esc_html( $category->name ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<section class="blog-editorial-list" aria-label="<?php esc_attr_e( 'Beiträge dieser Kategorie', 'blocksy-child' ); ?>" data-track-section="category_archive_list">
			<?php if ( have_posts() ) : ?>
				<?php
				while ( have_posts() ) :
					the_post();

					$post_categories  = get_the_category();
					$primary_category = ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ? $post_categories[0] : null;
					$reading_time     = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time() : 0;
					?>
					<article class="blog-editorial-item">
						<div class="blog-editorial-item__meta">
							<?php if ( $primary_category instanceof WP_Term ) : ?>
								<a class="blog-editorial-topic" href="<?php echo esc_url( get_category_link( $primary_category->term_id ) ); ?>">
									<?php echo esc_html( $primary_category->name ); ?>
								</a>
							<?php endif; ?>
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
								<?php echo esc_html( get_the_date( 'd. M Y' ) ); ?>
							</time>
							<?php if ( $reading_time > 0 ) : ?>
								<span><?php echo esc_html( sprintf( '%d Min. Lesezeit', $reading_time ) ); ?></span>
							<?php endif; ?>
						</div>

						<h2 class="blog-editorial-item__title">
							<a href="<?php echo esc_url( get_permalink() ); ?>">
								<?php echo esc_html( get_the_title() ); ?>
							</a>
						</h2>

						<p class="blog-editorial-item__excerpt">
							<?php echo esc_html( wp_trim_words( get_the_excerpt(), 28, '...' ) ); ?>
						</p>
					</article>
				<?php endwhile; ?>

				<div class="blog-editorial-pagination">
					<?php
					the_posts_pagination(
						[
							'mid_size'  => 1,
							'prev_text' => __( 'Zurück', 'blocksy-child' ),
							'next_text' => __( 'Weiter', 'blocksy-child' ),
						]
					);
					?>
				</div>
			<?php else : ?>
				<p class="blog-editorial-empty">
					<?php esc_html_e( 'In dieser Kategorie sind aktuell keine Beiträge veröffentlicht.', 'blocksy-child' ); ?>
				</p>
			<?php endif; ?>
		</section>

		<section class="blog-editorial-cta" aria-labelledby="category-archive-cta-heading" data-track-section="category_archive_final_cta">
			<span class="blog-editorial-kicker">Nächster Schritt</span>
			<h2 id="category-archive-cta-heading" class="blog-editorial-cta__title">
				Lesen ersetzt keine Systemdiagnose.
			</h2>
			<p class="blog-editorial-cta__text">
				Der regionale Marktcheck prüft, ob Projektwerte, Zielgebiet, Vertrieb und Website-Fundament für ein eigenes Anfrage-System tragfähig sind.
			</p>
			<a
				class="blog-editorial-cta__link"
				href="<?php echo esc_url( $audit_url ); ?>"
				data-track-action="cta_category_archive_marktcheck"
				data-track-category="lead_gen"
				data-track-section="category_archive_final_cta"
			>
				Regionalen Marktcheck starten
			</a>
		</section>
	</div>
</main>

<?php get_footer(); ?>
