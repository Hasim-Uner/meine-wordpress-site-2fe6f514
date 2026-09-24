<?php
/**
 * Shared reading layout for glossary terms.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$project_url  = hu_get_commercial_route( 'project_request', home_url( '/kontakt/' ) );
$glossary_url = nexus_get_glossary_hub_url();
$areas        = nexus_get_glossary_area_catalog();
?>
<div class="site-main doku glossary glossary-single">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		$definition = nexus_get_glossary_definition( get_post() );
		$sync_meta  = nexus_get_glossary_sync_observability( get_post() );
		$area       = is_array( $definition ) ? ( $areas[ $definition['core_area'] ] ?? [] ) : [];
		$excerpt    = is_array( $definition ) ? $definition['short_definition'] : get_the_excerpt();
		?>
		<article class="blatt" data-nexus-glossary-registry="<?php echo esc_attr( $sync_meta['registry_version'] ); ?>" data-nexus-glossary-synced-at="<?php echo esc_attr( $sync_meta['post_synced_at_gmt'] ); ?>" data-nexus-glossary-sync-last-run="<?php echo esc_attr( $sync_meta['last_sync_run_gmt'] ); ?>">
			<nav class="glossary-breadcrumb" aria-label="Brotkrümelnavigation">
				<a href="<?php echo esc_url( $glossary_url ); ?>">Glossar</a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php the_title(); ?></span>
			</nav>
			<header class="glossary-head">
				<p class="glossary-eyebrow"><?php echo esc_html( $area['label'] ?? 'Einfach erklärt' ); ?></p>
				<h1><?php the_title(); ?><span class="stempelfarbe">.</span></h1>
				<?php if ( $excerpt ) : ?>
					<p class="glossary-definition"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>
			</header>
			<div class="glossary-body">
				<?php
				if ( is_array( $definition ) ) {
					// Resolve managed content on this request: related destinations must
					// not be frozen to the state of other posts at sync time.
					echo nexus_get_glossary_term_content_html( $definition, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					the_content();
				}
				?>
			</div>
			<footer class="glossary-close">
				<a class="satzlink" href="<?php echo esc_url( $glossary_url ); ?>">← Alle Begriffe</a>
				<a class="satzlink" href="<?php echo esc_url( $project_url ); ?>" data-track-action="cta_glossary_term_project" data-track-category="project" data-track-section="glossary_close">Frage zu Ihrem Projekt? →</a>
			</footer>
		</article>
	<?php endwhile; ?>
</div>
<?php get_footer(); ?>
