<?php
/** One segmented next step; $step comes from next.php. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** @var array{kind:string,primary:bool,kicker:string,title:string,desc:string,url:string,label:string,note:string,action:string,variants?:array<string,array<string,string>>} $step */
$classes = [ 'erg-next-row', 'erg-next-row--' . sanitize_html_class( $step['kind'] ) ];
if ( ! empty( $step['primary'] ) ) {
	$classes[] = 'erg-next-row--primary';
}
$tracking_variant = isset( $step['variants']['tracking'] ) && is_array( $step['variants']['tracking'] )
	? $step['variants']['tracking']
	: [];
?>
<article
	class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
	data-funnel-kind="<?php echo esc_attr( $step['kind'] ); ?>"
	<?php if ( ! empty( $tracking_variant ) ) : ?>
		data-context-kicker-tracking="<?php echo esc_attr( $tracking_variant['kicker'] ?? '' ); ?>"
		data-context-title-tracking="<?php echo esc_attr( $tracking_variant['title'] ?? '' ); ?>"
		data-context-desc-tracking="<?php echo esc_attr( $tracking_variant['desc'] ?? '' ); ?>"
		data-context-url-tracking="<?php echo esc_url( $tracking_variant['url'] ?? '' ); ?>"
		data-context-label-tracking="<?php echo esc_attr( $tracking_variant['label'] ?? '' ); ?>"
		data-context-note-tracking="<?php echo esc_attr( $tracking_variant['note'] ?? '' ); ?>"
	<?php endif; ?>
>
	<div>
		<p class="mono erg-next-kicker"><?php echo esc_html( $step['kicker'] ); ?></p>
		<h3 class="erg-next-title"><?php echo esc_html( $step['title'] ); ?></h3>
		<p class="erg-next-desc"><?php echo esc_html( $step['desc'] ); ?></p>
	</div>
	<div class="erg-next-action">
		<a class="tun erg-next-link" href="<?php echo esc_url( $step['url'] ); ?>" data-track-action="<?php echo esc_attr( $step['action'] ); ?>" data-track-category="lead_gen" data-track-section="weiter" data-funnel-kind="<?php echo esc_attr( $step['kind'] ); ?>"><?php echo esc_html( $step['label'] ); ?> <span aria-hidden="true">→</span></a>
		<?php if ( '' !== $step['note'] ) : ?><p class="erg-note erg-next-note"><?php echo esc_html( $step['note'] ); ?></p><?php endif; ?>
	</div>
</article>
