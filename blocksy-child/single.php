<?php
/**
 * NEXUS Single Post Template
 *
 * Features: Dynamisches Hero-Bild, Breadcrumb, Share, Related Content, Footer-CTA.
 * Tracking-ready via data-track-* Attribute.
 *
 * [Flywheel] single.php: Blog Post mit Breadcrumb, Related Content, Footer-CTA
 *
 * @package Blocksy_Child
 */

get_header();
get_template_part( 'template-parts/blog-header' );
?>

<main id="main" class="site-main nexus-single-container nexus-single-container--with-blog-header">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php $has_hero_image = has_post_thumbnail(); ?>

		<header class="nexus-article-hero<?php echo esc_attr( $has_hero_image ? '' : ' nexus-article-hero--text-only' ); ?>" data-track-section="article_hero">

			<?php if ( $has_hero_image ) : ?>
			<div class="nexus-hero-image">
					<?php the_post_thumbnail( 'full' ); ?>
			</div>
			<?php endif; ?>

			<div class="nexus-hero-content">

				<div class="nexus-meta-top">
					<span class="nexus-date"><?php echo esc_html( get_the_date( 'd. M Y' ) ); ?></span>
					<span class="separator">|</span>
					<span class="nexus-reading-time"><?php
						printf(
							/* translators: %d: reading time in minutes */
							esc_html__( '⏱ %d Min. Lesezeit', 'blocksy-child' ),
							nexus_get_reading_time()
						);
					?></span>
				</div>

				<h1 class="nexus-title"><?php the_title(); ?></h1>

				<div class="nexus-hero-footer">
					<div class="nexus-author-row">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 48 ); ?>
						<div class="nexus-author-info">
							<span class="by"><?php esc_html_e( 'Written by', 'blocksy-child' ); ?></span>
							<span class="name"><?php the_author(); ?></span>
						</div>
					</div>

					<?php if ( is_singular( 'post' ) && function_exists( 'nexus_render_share_buttons' ) ) { nexus_render_share_buttons(); } ?>
				</div>

			</div>
		</header>

		   <div class="nexus-post-layout">
			   <?php if ( is_singular('post') ) : ?>
			   <aside class="nexus-sidebar">
				   <div class="sticky-toc">
					   <h3>Inhalt</h3>
					   <ul id="toc-list"></ul>
				   </div>
			   </aside>
			   <?php endif; ?>
			   <article class="nexus-article-content" id="article-content" data-track-section="article_content">
				   <?php the_content(); ?>

				   <div class="nexus-inline-cta" id="nexus-inline-cta" hidden aria-hidden="true">
					   <div class="nexus-inline-cta__inner">
						   <span class="nexus-inline-cta__tag">Kostenlose Diagnose</span>
						   <h3 class="nexus-inline-cta__headline">Was bremst Ihr Wachstum?</h3>
						   <p class="nexus-inline-cta__sub">Persönliche Analyse Ihrer Website — schriftliche Rückmeldung in 48 Stunden.</p>
						   <a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>"
							  class="nexus-btn nexus-btn--primary nexus-inline-cta__btn"
							  data-track-action="cta_blog_inline"
							  data-track-category="lead_gen">
							  System-Diagnose anfragen
						   </a>
					   </div>
				   </div>

				   <?php
					if ( function_exists( 'nexus_get_wgos_blog_asset_bridge' ) && function_exists( 'nexus_render_wgos_blog_asset_bridge' ) ) {
						$bridge = nexus_get_wgos_blog_asset_bridge();

						if ( is_array( $bridge ) ) {
							echo nexus_render_wgos_blog_asset_bridge( $bridge ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
					}
				   ?>
			   </article>
		   </div>

		<?php get_template_part( 'template-parts/blog-notify', null, [ 'variant' => 'full' ] ); ?>

		<?php if ( is_singular( 'post' ) ) : ?>
		<div class="nexus-bottom-share" data-track-section="article_share">
			<h3><?php esc_html_e( 'Diesen Artikel teilen', 'blocksy-child' ); ?></h3>
			<?php if ( function_exists( 'nexus_render_share_buttons' ) ) { nexus_render_share_buttons(); } ?>
		</div>
		<?php endif; ?>

		<?php
		// Related Content (gleiche Kategorie)
		set_query_var( 'related_heading', __( 'Das könnte Sie auch interessieren', 'blocksy-child' ) );
		set_query_var( 'related_count', 3 );
		set_query_var( 'related_type', 'post' );
		get_template_part( 'template-parts/related-content' );
		?>

		<?php get_template_part( 'template-parts/footer-cta' ); ?>

	<?php endwhile; ?>

</main>

<?php
get_footer();
