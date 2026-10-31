<?php
/**
 * Shared editorial card for the blog index and category archives.
 *
 * The main WordPress loop owns ordering and pagination. This view reads the
 * current post without advancing it or replacing its editorial content.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$card_post_id    = get_the_ID();
$card_featured   = 'featured' === ( $args['variant'] ?? '' );
$card_categories = get_the_category( $card_post_id );
$card_category   = ! empty( $card_categories ) && ! is_wp_error( $card_categories ) ? $card_categories[0] : null;
$card_label      = $card_category instanceof WP_Term
	? ( function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $card_category ) : $card_category->name )
	: '';
$card_minutes    = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time( $card_post_id ) : 0;
$card_excerpt    = wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 30, '…' );
$card_title_id   = 'blog-card-title-' . $card_post_id;
$card_image      = get_the_post_thumbnail(
	$card_post_id,
	'large',
	[
		'alt'           => '', // The enclosing link is named by the full article title.
		'loading'       => $card_featured ? 'eager' : 'lazy',
		'decoding'      => 'async',
		'sizes'         => $card_featured
			? '(min-width: 1200px) 576px, (min-width: 720px) 46vw, calc(100vw - 40px)'
			: '(min-width: 1280px) 384px, (min-width: 1100px) 30vw, (min-width: 720px) 46vw, calc(100vw - 40px)',
	]
);
?>
<article class="blog-card<?php echo esc_attr( $card_featured ? ' blog-card--featured' : '' ); ?>">
	<a class="blog-card__link" href="<?php the_permalink(); ?>" aria-labelledby="<?php echo esc_attr( $card_title_id ); ?>" data-track-action="<?php echo esc_attr( $card_featured ? 'blog_focus_open' : 'blog_card_open' ); ?>" data-track-category="content">
		<span class="blog-card__media<?php echo esc_attr( $card_image ? '' : ' blog-card__media--placeholder' ); ?>" aria-hidden="true">
			<?php if ( $card_image ) : ?>
				<?php echo $card_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress thumbnail markup. ?>
			<?php else : ?>
				<span class="blog-card__placeholder-label"><?php echo esc_html( $card_label ?: 'Aus der Praxis' ); ?></span>
				<span class="blog-card__placeholder-mark">Aa<span>→</span></span>
			<?php endif; ?>
		</span>
		<div class="blog-card__body">
			<span class="blog-card__meta">
				<?php if ( $card_label ) : ?><span><?php echo esc_html( $card_label ); ?></span><?php endif; ?>
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'd. M Y' ) ); ?></time>
				<?php if ( $card_minutes > 0 ) : ?><span><?php echo esc_html( sprintf( '%d Min.', $card_minutes ) ); ?></span><?php endif; ?>
			</span>
			<?php if ( $card_featured ) : ?>
				<h2 id="<?php echo esc_attr( $card_title_id ); ?>"><?php echo esc_html( get_the_title() ); ?></h2>
			<?php else : ?>
				<h3 id="<?php echo esc_attr( $card_title_id ); ?>"><?php echo esc_html( get_the_title() ); ?></h3>
			<?php endif; ?>
			<?php if ( $card_excerpt ) : ?><span class="blog-card__excerpt"><?php echo esc_html( $card_excerpt ); ?></span><?php endif; ?>
			<span class="blog-card__read" aria-hidden="true">Artikel lesen <span>→</span></span>
		</div>
	</a>
</article>
