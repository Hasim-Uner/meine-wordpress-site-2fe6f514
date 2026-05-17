<?php
/**
 * Author byline + last-updated date for SEO sub-pages.
 *
 * Args (passed via set_query_var or get_template_part(..., $args)):
 * - template_path: absolute filesystem path to the calling template
 * - profile_url:   optional override for the author profile URL
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hu_byline_args        = isset( $args ) && is_array( $args ) ? $args : [];
$hu_byline_template    = isset( $hu_byline_args['template_path'] ) ? (string) $hu_byline_args['template_path'] : '';
$hu_byline_profile_url = isset( $hu_byline_args['profile_url'] ) ? (string) $hu_byline_args['profile_url'] : home_url( '/uber-mich/' );

$hu_byline_updated_iso   = function_exists( 'hu_get_subpage_last_updated_iso' ) ? hu_get_subpage_last_updated_iso( $hu_byline_template ) : '';
$hu_byline_updated_label = function_exists( 'hu_get_subpage_last_updated_label' ) ? hu_get_subpage_last_updated_label( $hu_byline_template ) : '';
?>

<div class="hu-intercept__byline" role="contentinfo" aria-label="Autor und Aktualisierung">
	<span class="hu-intercept__byline-author">
		von <a class="hu-intercept__byline-author-link" href="<?php echo esc_url( $hu_byline_profile_url ); ?>" rel="author">Haşim Üner</a>
	</span>
	<?php if ( '' !== $hu_byline_updated_label ) : ?>
		<span class="hu-intercept__byline-sep" aria-hidden="true">·</span>
		<time class="hu-intercept__byline-date" datetime="<?php echo esc_attr( $hu_byline_updated_iso ); ?>">
			Stand: <?php echo esc_html( $hu_byline_updated_label ); ?>
		</time>
	<?php endif; ?>
</div>
