<?php
/**
 * Virtual page for blog notification confirmation and unsubscribe states.
 *
 * @package Blocksy_Child
 */

get_header();

$state      = function_exists( 'nexus_get_blog_notify_page_state' ) ? nexus_get_blog_notify_page_state() : 'default';
$state_copy = function_exists( 'nexus_get_blog_notify_state_copy' ) ? nexus_get_blog_notify_state_copy( $state ) : [];
$blog_url   = function_exists( 'nexus_get_blog_posts_url' ) ? nexus_get_blog_posts_url() : home_url( '/blog/' );
?>

<div class="site-main nexus-blog-notify-page">
	<section class="nexus-blog-notify-page__hero">
		<div class="nexus-blog-notify-page__shell">
			<span class="nexus-blog-notify-page__eyebrow"><?php echo esc_html( $state_copy['eyebrow'] ?? 'Blog-Benachrichtigungen' ); ?></span>
			<h1 class="nexus-blog-notify-page__title"><?php echo esc_html( $state_copy['title'] ?? 'Neue Artikel per E-Mail' ); ?></h1>
			<p class="nexus-blog-notify-page__body nexus-blog-notify-page__body--<?php echo esc_attr( $state_copy['variant'] ?? 'default' ); ?>"><?php echo esc_html( $state_copy['body'] ?? '' ); ?></p>

			<div class="nexus-blog-notify-page__actions">
				<a class="nexus-blog-notify-page__link" href="<?php echo esc_url( $blog_url ); ?>">Zum Blog</a>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $state_copy['showForm'] ) ) : ?>
		<section class="nexus-blog-notify-page__form-wrap">
			<div class="nexus-blog-notify-page__shell">
				<?php get_template_part( 'template-parts/blog-notify', null, [ 'variant' => 'full' ] ); ?>
			</div>
		</section>
	<?php endif; ?>
</div>

<?php
get_footer();
