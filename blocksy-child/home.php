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

$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$audit_url    = function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$energy_url   = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
$e3_url       = $primary_urls['e3'] ?? home_url( '/e3-new-energy/' );
$solar_term   = get_category_by_slug( 'solar-waermepumpen-anfrage-systeme' );
$solar_url    = $solar_term instanceof WP_Term ? get_category_link( $solar_term->term_id ) : $energy_url;

get_header();
?>

<main id="main" class="site-main blog-home">

	<section class="blog-archive-intro" aria-labelledby="blog-archive-heading">
		<div class="blog-archive-intro__inner">
			<h1 id="blog-archive-heading" class="blog-archive-intro__headline">
				Insights für eigene Anfrage-Systeme
				<span class="blog-archive-intro__headline-accent">im Solar- und Wärmepumpenmarkt.</span>
			</h1>
			<p class="blog-archive-intro__sub">
				Analysen zu SEO, Tracking, Angebotsseiten und Conversion — damit aus Traffic ein eigener Anfragekanal statt Portal-Abhängigkeit wird.
			</p>
			<div class="blog-archive-intro__actions" aria-label="Empfohlene Einstiege">
				<a
					href="<?php echo esc_url( $solar_url ); ?>"
					class="blog-archive-intro__link blog-archive-intro__link--primary"
					data-track-action="cta_blog_intro_solar_category"
					data-track-category="navigation"
				>
					Solar-Insights lesen
				</a>
				<a
					href="<?php echo esc_url( $energy_url ); ?>"
					class="blog-archive-intro__link"
					data-track-action="cta_blog_intro_money_page"
					data-track-category="navigation"
				>
					Anfrage-Systeme ansehen
				</a>
				<a
					href="<?php echo esc_url( $e3_url ); ?>"
					class="blog-archive-intro__link"
					data-track-action="cta_blog_intro_e3_case"
					data-track-category="trust"
				>
					E3-Case lesen
				</a>
			</div>
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
				<div class="blog-archive-infeed-cta" aria-label="Kostenloser Marktcheck">
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
							Marktcheck starten
						</a>
					</div>
				</div>

			</div><!-- .blog-archive-grid -->

		</div><!-- .hu-blog-wrapper -->

	</div><!-- .blog-archive-shell -->

</main>

<?php get_footer(); ?>
