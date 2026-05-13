<?php
/**
 * Single template for glossary terms.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$audit_url    = nexus_get_primary_public_url( 'audit', home_url( '/system-diagnose/' ) );
$glossary_url = function_exists( 'nexus_get_glossary_hub_url' ) ? nexus_get_glossary_hub_url() : home_url( '/glossar/' );
?>

<main id="main" class="site-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		$excerpt    = has_excerpt() ? get_the_excerpt() : '';
		$content    = get_the_content();
		$definition = function_exists( 'nexus_get_glossary_definition' ) ? nexus_get_glossary_definition( get_post() ) : null;
		$sync_meta  = function_exists( 'nexus_get_glossary_sync_observability' ) ? nexus_get_glossary_sync_observability( get_post() ) : [];

		if ( '' === trim( wp_strip_all_tags( (string) $content ) ) && is_array( $definition ) && function_exists( 'nexus_get_glossary_term_content_html' ) ) {
			$content = nexus_get_glossary_term_content_html( $definition );
		}
		?>

		<div class="wgos-wrapper glossary-wrapper"
			<?php if ( ! empty( $sync_meta['registry_version'] ) ) : ?>
				data-nexus-glossary-registry="<?php echo esc_attr( (string) $sync_meta['registry_version'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $sync_meta['post_synced_at_gmt'] ) ) : ?>
				data-nexus-glossary-synced-at="<?php echo esc_attr( (string) $sync_meta['post_synced_at_gmt'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $sync_meta['last_sync_run_gmt'] ) ) : ?>
				data-nexus-glossary-sync-last-run="<?php echo esc_attr( (string) $sync_meta['last_sync_run_gmt'] ); ?>"
			<?php endif; ?>
		>
			<section class="wgos-hero">
				<div class="wgos-container wgos-hero__inner">
					<nav class="wgos-section-intro wgos-breadcrumb" aria-label="Breadcrumb">
						<a class="wgos-link--arrow" href="<?php echo esc_url( $glossary_url ); ?>">Glossar</a>
						<span aria-hidden="true"> / </span>
						<span aria-current="page"><?php the_title(); ?></span>
					</nav>

					<span class="wgos-kicker">Glossar-Begriff</span>
					<h1 class="wgos-hero__title"><?php the_title(); ?></h1>

					<?php if ( $excerpt ) : ?>
						<p class="wgos-hero__subtitle"><?php echo esc_html( $excerpt ); ?></p>
					<?php endif; ?>

					<div class="wgos-hero__actions">
						<a href="<?php echo esc_url( $glossary_url ); ?>" class="wgos-btn wgos-btn--outline">Zum Glossar</a>
						<a href="<?php echo esc_url( $audit_url ); ?>" class="wgos-btn wgos-btn--primary" data-track-action="cta_glossary_hero_audit" data-track-category="lead_gen"><?php echo esc_html( nexus_get_audit_cta_label() ); ?></a>
					</div>

					<p class="wgos-hero__microcopy">Diese Begriffsseite ist Teil eines kontrollierten Glossar-Layers. Head Terms bleiben auf den Primary URLs, damit Definition und Angebots-Intent sauber getrennt bleiben.</p>
				</div>
			</section>

			<?php echo apply_filters( 'the_content', $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endwhile; ?>
</main>

<?php get_footer(); ?>
