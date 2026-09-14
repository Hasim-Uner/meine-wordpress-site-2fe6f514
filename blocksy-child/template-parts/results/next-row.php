<?php
/** One segmented next step; $step comes from next.php. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<article class="erg-next-row erg-next-row--<?php echo esc_attr( $step['kind'] ); ?>">
	<div><p class="mono"><?php echo esc_html( $step['kicker'] ); ?></p><h3><?php echo esc_html( $step['title'] ); ?></h3><p><?php echo esc_html( $step['desc'] ); ?></p></div>
	<div class="erg-next-action">
		<a class="tun" href="<?php echo esc_url( $step['url'] ); ?>" data-track-action="<?php echo esc_attr( $step['action'] ); ?>" data-track-category="lead_gen" data-track-section="weiter"><?php echo esc_html( $step['label'] ); ?> <span aria-hidden="true">→</span></a>
		<?php if ( '' !== $step['note'] ) : ?><p class="erg-note"><?php echo esc_html( $step['note'] ); ?></p><?php endif; ?>
	</div>
</article>
