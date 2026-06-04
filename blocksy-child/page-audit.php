<?php
/**
 * Template Name: System-Diagnose
 * Description: Landing Page für die manuelle 2 Werktage-Anfrage-System-Analyse
 *
 * SEO-Meta: zentral in inc/seo-meta.php (ACF-Felder: seo_title, seo_description, og_image)
 *
 * @package Blocksy_Child
 */

get_header();
?>
<!-- nexus-audit-template-active -->
<div class="audit-page-wrapper" data-track-section="growth_audit_instant">

	<?php
	while ( have_posts() ) :
		the_post();

		// Render the versioned audit shell directly from the template.
		if ( function_exists( 'nexus_get_audit_shell_markup' ) ) {
			echo nexus_get_audit_shell_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			get_template_part( 'template-parts/audit-page-shell' );
		}
	endwhile;
	?>
</div>

<?php get_footer(); ?>
