<?php
/** One technical proof entry; $proof comes from technical.php. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** @var array{title:string,text:string,url:string,action:string,label:string} $proof */
?>
<article class="erg-evidence">
	<h3><?php echo esc_html( $proof['title'] ); ?></h3>
	<div>
		<p><?php echo esc_html( $proof['text'] ); ?></p>
		<a class="textlink" href="<?php echo esc_url( $proof['url'] ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="<?php echo esc_attr( $proof['action'] ); ?>" data-track-category="proof" data-track-section="technik"><?php echo esc_html( $proof['label'] ); ?><span class="nur-vorlesen"> (neuer Tab)</span> <span aria-hidden="true">↗</span></a>
	</div>
</article>
