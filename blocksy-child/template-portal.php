<?php
/**
 * Template Name: Nexus Portal
 * Description: Client Portal / Performance Cockpit
 *
 * noindex gesteuert in inc/seo-meta.php (Slug: portal)
 *
 * @package Blocksy_Child
 */

get_header();
?>

<div class="site-main">
	<div class="nexus-portal" data-track-section="client_portal">
		<div class="nexus-portal-container">
			<?php echo do_shortcode( '[hu_performance_cockpit]' ); ?>
		</div>
	</div>
</div>

<?php
get_footer();
