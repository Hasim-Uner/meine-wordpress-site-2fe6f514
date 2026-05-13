<?php
/**
 * Single template for WGOS assets.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$audit_url = function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/anfrage-system-analyse/' );
$wgos_url  = function_exists( 'nexus_get_wgos_url' ) ? nexus_get_wgos_url() : home_url( '/wgos/' );
$hub_url   = function_exists( 'nexus_get_wgos_asset_hub_url' ) ? nexus_get_wgos_asset_hub_url() : $wgos_url . '#module';
?>

<main id="main" class="site-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		$excerpt       = has_excerpt() ? get_the_excerpt() : '';
		$hub_focus_url = $hub_url;
		$content       = get_the_content();
		$asset_def     = function_exists( 'nexus_get_wgos_asset_definition' ) ? nexus_get_wgos_asset_definition( get_post() ) : null;

		if ( '' === trim( wp_strip_all_tags( (string) $content ) ) && is_array( $asset_def ) && function_exists( 'nexus_get_wgos_asset_content_html' ) ) {
			$content = nexus_get_wgos_asset_content_html( $asset_def );
		}
		?>

		<div class="wgos-wrapper">
			<section class="wgos-hero">
				<div class="wgos-container wgos-hero__inner">
					<nav class="wgos-section-intro wgos-breadcrumb" aria-label="Breadcrumb">
						<a class="wgos-link--arrow" href="<?php echo esc_url( $wgos_url ); ?>">WGOS Hub</a>
						<span aria-hidden="true"> / </span>
						<a class="wgos-link--arrow" href="<?php echo esc_url( $hub_focus_url ); ?>">Asset-Landkarte</a>
						<span aria-hidden="true"> / </span>
						<span aria-current="page"><?php the_title(); ?></span>
					</nav>

					<span class="wgos-kicker">WGOS Asset</span>

					<h1 class="wgos-hero__title"><?php the_title(); ?></h1>

					<?php if ( $excerpt ) : ?>
						<p class="wgos-hero__subtitle"><?php echo esc_html( $excerpt ); ?></p>
					<?php endif; ?>

					<div class="wgos-hero__actions">
						<a href="<?php echo esc_url( $hub_focus_url ); ?>" class="wgos-btn wgos-btn--outline">Zur Asset-Landkarte</a>
						<a href="<?php echo esc_url( $audit_url ); ?>" class="wgos-btn wgos-btn--primary" data-track-action="cta_wgos_asset_hero_audit" data-track-category="lead_gen"><?php echo esc_html( nexus_get_audit_cta_label() ); ?></a>
					</div>

					<p class="wgos-hero__microcopy">Dieses Asset ist Teil eines dokumentierten WGOS-Systems. Struktur, interne Verlinkung und Theme-Logik sind versioniert nachvollziehbar.</p>
				</div>
			</section>

			<?php echo apply_filters( 'the_content', $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endwhile; ?>
</main>

<?php
get_footer();
