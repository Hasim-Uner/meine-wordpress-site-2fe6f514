<?php
/**
 * Blog-Archiv-Template (Blog-Startseite)
 *
 * Sauber aus dem Design-System gebaut.
 * Kategorie-Filter via blog-archive.js (.hu-blog-wrapper, .hu-filter-btn, .post-card).
 *
 * CRO-Reihenfolge: Featured → Filter → 3 Artikel → Blog-Notify → Rest → Audit-CTA
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$audit_url = function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/anfrage-system-analyse/' );

get_header();
?>

<main id="main" class="site-main blog-home">

	<section class="blog-archive-intro" aria-labelledby="blog-archive-heading">
		<div class="blog-archive-intro__inner">
			<h1 id="blog-archive-heading" class="blog-archive-intro__headline">
				Was B2B-Websites wirklich bremst —
				<span class="blog-archive-intro__headline-accent">und wie man es löst.</span>
			</h1>
			<p class="blog-archive-intro__sub">
				Analysen zu SEO, Conversion und Tracking für WordPress-Websites.
			</p>
		</div>
	</section>

	<div class="blog-archive-shell">

		<div class="hu-blog-wrapper">

			<nav class="blog-archive-filter" aria-label="Artikel nach Kategorie filtern">
				<button class="hu-filter-btn is-active" data-filter="all" aria-pressed="true">
					Alle Beiträge
				</button>
				<?php foreach ( get_categories( [ 'hide_empty' => true ] ) as $cat ) : ?>
					<button
						class="hu-filter-btn"
						data-filter="<?php echo esc_attr( $cat->slug ); ?>"
						aria-pressed="false"
					>
						<?php echo esc_html( $cat->name ); ?>
					</button>
				<?php endforeach; ?>
			</nav>

			<div class="blog-archive-grid">

				<?php
				$post_index   = 0;
				$notify_shown = false;

				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();
						$post_index++;

						$cats        = get_the_category();
						$cat_slugs   = wp_list_pluck( $cats, 'slug' );
						$thumb_url   = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
						$is_featured = ( 1 === $post_index );

						// Blog-Notify nach Artikel 3 (Nutzer hat Wert gesehen)
						if ( $post_index === 4 && ! $notify_shown ) :
							$notify_shown = true;
				?>
					<div class="blog-archive-notify-slot">
						<?php get_template_part( 'template-parts/blog-notify', null, [ 'variant' => 'compact' ] ); ?>
					</div>
				<?php
						endif;
				?>

				<article
					class="post-card<?php echo esc_attr( $is_featured ? ' post-card--featured' : '' ); ?>"
					data-categories="<?php echo esc_attr( wp_json_encode( $cat_slugs ) ); ?>"
				>
					<?php if ( $thumb_url ) : ?>
						<a
							href="<?php the_permalink(); ?>"
							class="post-card__thumb-link"
							tabindex="-1"
							aria-hidden="true"
						>
							<div class="post-card__thumb">
								<img
									src="<?php echo esc_url( $thumb_url ); ?>"
									alt="<?php the_title_attribute(); ?>"
									loading="<?php echo esc_attr( $is_featured ? 'eager' : 'lazy' ); ?>"
									<?php if ( $is_featured ) : ?>fetchpriority="high"<?php endif; ?>
									width="600"
									height="338"
								>
							</div>
						</a>
					<?php endif; ?>

					<div class="post-card__body">

						<?php if ( ! empty( $cats ) ) : ?>
							<a
								href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>"
								class="post-card__cat"
							>
								<?php echo esc_html( $cats[0]->name ); ?>
							</a>
						<?php endif; ?>

						<h2 class="post-card__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>

						<p class="post-card__excerpt">
							<?php echo wp_trim_words( get_the_excerpt(), $is_featured ? 40 : 20 ); ?>
						</p>

						<div class="post-card__meta">
							<time class="post-card__date" datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>">
								<?php echo esc_html( get_the_date( 'd. M Y' ) ); ?>
							</time>
							<?php if ( function_exists( 'nexus_get_reading_time' ) && nexus_get_reading_time() >= 3 ) : ?>
								<span class="post-card__reading-time">
									<?php printf( '%d Min. Lesezeit', nexus_get_reading_time() ); ?>
								</span>
							<?php endif; ?>
						</div>

					</div>
				</article>

				<?php
					endwhile;
				endif;

				// Falls weniger als 4 Artikel: Notify trotzdem zeigen
				if ( ! $notify_shown ) :
				?>
					<div class="blog-archive-notify-slot">
						<?php get_template_part( 'template-parts/blog-notify', null, [ 'variant' => 'compact' ] ); ?>
					</div>
				<?php endif; ?>

				<!-- Diagnose CTA am Ende aller Artikel -->
				<div class="blog-archive-infeed-cta" aria-label="Kostenlose System-Diagnose">
					<div class="blog-archive-infeed-cta__inner">
						<span class="blog-archive-infeed-cta__tag">Kostenlose Diagnose</span>
						<h2 class="blog-archive-infeed-cta__headline">Lassen Sie es uns konkret machen.</h2>
						<p class="blog-archive-infeed-cta__sub">
							Persönliche Analyse Ihrer Website. Schriftliche Rückmeldung mit den 3 stärksten Bremsen — in 48 Stunden.
						</p>
						<a
							href="<?php echo esc_url( $audit_url ); ?>"
							class="nexus-btn nexus-btn--primary blog-archive-infeed-cta__btn"
							data-track-action="cta_blog_archive_end"
							data-track-category="lead_gen"
						>
							System-Diagnose starten
						</a>
					</div>
				</div>

			</div><!-- .blog-archive-grid -->

		</div><!-- .hu-blog-wrapper -->

	</div><!-- .blog-archive-shell -->

</main>

<?php get_footer(); ?>
