<?php
/** One public work entry; $reference comes from works.php. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<article class="erg-project">
	<div class="erg-project-heading">
		<p class="mono"><?php echo esc_html( $reference['role'] ); ?></p>
		<h3><?php echo esc_html( $reference['name'] ); ?></h3>
		<p class="erg-note"><?php echo esc_html( $reference['discipline'] ); ?></p>
		<a class="textlink" href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="results_reference_open" data-track-category="trust" data-track-section="arbeiten">Website ansehen <span aria-hidden="true">↗</span></a>
	</div>
	<div class="erg-project-body">
		<dl class="erg-contribution"><div><dt>Mein Beitrag</dt><dd><?php echo esc_html( $reference['text'] ); ?></dd></div></dl>
	</div>
</article>
