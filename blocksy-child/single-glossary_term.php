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

$project_url = function_exists( 'hu_get_commercial_route' )
	? hu_get_commercial_route( 'project_request', home_url( '/kontakt/' ) )
	: home_url( '/kontakt/' );
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

		/*
		 * Registry content historically carried a Solar-marketcheck CTA on every
		 * glossary term. Commercial routing now belongs to this global template:
		 * informational glossary pages must not jump into the Energy funnel.
		 * Keep stripping the legacy generated section until the registry renderer
		 * itself is simplified in a future migration.
		 */
		$content = preg_replace(
			'#<section class="wgos-section wgos-section--white wgos-final-cta">.*?</section>\s*$#s',
			'',
			(string) $content
		);
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
						<a href="<?php echo esc_url( $project_url ); ?>" class="wgos-btn wgos-btn--primary" data-track-action="cta_glossary_hero_project" data-track-category="project">Projekt anfragen</a>
					</div>

					<p class="wgos-hero__microcopy">Diese Begriffsseite ist Teil eines kontrollierten Glossar-Layers. Head Terms bleiben auf den Primary URLs, damit Definition und Angebots-Intent sauber getrennt bleiben.</p>
				</div>
			</section>

			<?php echo apply_filters( 'the_content', $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<section class="wgos-section wgos-section--white wgos-final-cta">
				<div class="wgos-container">
					<div class="wgos-final-cta__inner">
						<span class="wgos-principle-kicker">Nächster Schritt</span>
						<h2 class="wgos-h2">Begriff verstanden. Jetzt den tatsächlichen Engpass klären.</h2>
						<p class="wgos-prose">Wenn ein Thema für Ihre Website praktisch relevant wird, ist die nächste Frage nicht, welches Schlagwort als Nächstes optimiert wird. Entscheidend ist, ob der Engpass bei WordPress, Tracking, technischer SEO oder Conversion liegt und welcher Scope ihn sinnvoll löst.</p>
						<div class="wgos-hero__actions">
							<a href="<?php echo esc_url( $glossary_url ); ?>" class="wgos-btn wgos-btn--outline">Zurück zum Glossar</a>
							<a href="<?php echo esc_url( $project_url ); ?>" class="wgos-btn wgos-btn--primary" data-track-action="cta_glossary_term_project" data-track-category="project">Projekt und Scope klären</a>
						</div>
					</div>
				</div>
			</section>
		</div>
	<?php endwhile; ?>
</main>

<?php get_footer(); ?>