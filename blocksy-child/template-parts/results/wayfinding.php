<?php
/** Route-local replacement for the generated results TOC. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

remove_action( 'wp_body_open', 'hu_render_generated_page_toc', 31 );
add_action( 'wp_body_open', static function () {
	$items = [
		[ 'id' => 'grossprojekt', 'label' => 'Dokumentierter Fall' ],
		[ 'id' => 'arbeiten', 'label' => 'WordPress-Arbeiten' ],
		[ 'id' => 'technik', 'label' => 'Technische Belege' ],
		[ 'id' => 'weiter', 'label' => 'Nächster Schritt' ],
	];
	?>
	<nav class="hu-page-toc" aria-label="Auf dieser Seite" data-hu-rail="true" data-track-section="page_toc">
		<details open><summary>Auf dieser Seite</summary><ul role="list">
			<?php foreach ( $items as $item ) : ?>
				<li><a href="#<?php echo esc_attr( $item['id'] ); ?>" data-track-action="toc_<?php echo esc_attr( sanitize_key( $item['id'] ) ); ?>" data-track-category="navigation"><?php echo esc_html( $item['label'] ); ?></a></li>
			<?php endforeach; ?>
		</ul></details>
	</nav>
	<?php
}, 31 );
