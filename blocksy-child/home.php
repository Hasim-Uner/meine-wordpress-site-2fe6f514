<?php
/**
 * Minimal editorial blog archive template.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$audit_url     = function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$posts_page_id = (int) get_option( 'page_for_posts' );
$blog_url      = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' );
$categories    = get_categories(
	[
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	]
);

get_header();
?>

<main id="main" class="site-main blog-home blog-editorial blog-editorial--index">
	<div class="blog-editorial__inner">
		<header class="blog-editorial-hero" aria-labelledby="blog-archive-heading" data-track-section="blog_archive_hero">
			<span class="blog-editorial-kicker">Blog & Markteinordnung</span>
			<h1 id="blog-archive-heading" class="blog-editorial-hero__title">
				Analysen für eigene Anfrage-Systeme.
			</h1>
			<p class="blog-editorial-hero__lead">
				Keine Magazinoptik. Keine Stockfoto-Sammlung. Hier stehen die Themen, die bei Solar-, SHK- und B2B-Websites über Sichtbarkeit, Lead-Qualität und Abschlusskosten entscheiden.
			</p>
		</header>

		<nav class="blog-editorial-filter" aria-label="<?php esc_attr_e( 'Artikel nach Kategorie filtern', 'blocksy-child' ); ?>" data-track-section="blog_archive_filter">
			<a
				class="blog-editorial-filter__link is-active"
				href="<?php echo esc_url( $blog_url ); ?>"
				aria-current="page"
				data-track-action="blog_filter_all"
				data-track-category="navigation"
				data-track-section="blog_archive_filter"
			>
				Alle
			</a>
			<?php foreach ( $categories as $category ) : ?>
				<?php $category_url = get_category_link( $category->term_id ); ?>
				<?php if ( is_wp_error( $category_url ) ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<a
					class="blog-editorial-filter__link"
					href="<?php echo esc_url( $category_url ); ?>"
					data-track-action="<?php echo esc_attr( 'blog_filter_' . $category->slug ); ?>"
					data-track-category="navigation"
					data-track-section="blog_archive_filter"
				>
					<?php echo esc_html( $category->name ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<section class="blog-editorial-list" aria-label="<?php esc_attr_e( 'Aktuelle Beiträge', 'blocksy-child' ); ?>" data-track-section="blog_archive_list">
			<?php if ( have_posts() ) : ?>
				<?php
				while ( have_posts() ) :
					the_post();

					$post_categories = get_the_category();
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
					<?php esc_html_e( 'Aktuell sind keine Beiträge veröffentlicht.', 'blocksy-child' ); ?>
				</p>
			<?php endif; ?>
		</section>

		<section class="blog-editorial-cta" aria-labelledby="blog-archive-cta-heading" data-track-section="blog_archive_final_cta">
			<span class="blog-editorial-kicker">Nächster Schritt</span>
			<h2 id="blog-archive-cta-heading" class="blog-editorial-cta__title">
				Nicht mehr Inhalte lesen, bevor das System geprüft ist.
			</h2>
			<p class="blog-editorial-cta__text">
				Der regionale Marktcheck prüft Projektwert, Zielgebiet, Vertriebsreife und Website-Fundament. Wer nicht passt, bekommt keine Verkaufsstrecke, sondern eine klare Absage.
			</p>
			<a
				class="blog-editorial-cta__link"
				href="<?php echo esc_url( $audit_url ); ?>"
				data-track-action="cta_blog_archive_marktcheck"
				data-track-category="lead_gen"
				data-track-section="blog_archive_final_cta"
			>
				Regionalen Marktcheck starten
			</a>
		</section>
	</div>
</main>

<?php get_footer(); ?>
