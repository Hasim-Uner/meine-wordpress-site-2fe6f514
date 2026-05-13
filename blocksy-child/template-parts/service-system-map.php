<?php
/**
 * Template Part: Service System Map
 *
 * Renders a compact 4-layer system view for service landing pages.
 *
 * Usage:
 *   set_query_var(
 *     'service_system_map',
 *     [
 *       'kicker'  => 'System in der Praxis',
 *       'title'   => 'Was Sie hier nicht nur beauftragen, sondern aufbauen',
 *       'intro'   => '...',
 *       'summary' => [ 'Angebotsseiten', 'Datenebene' ],
 *       'layers'  => [
 *         [
 *           'label'  => 'Ebene 1',
 *           'title'  => 'Sichtbare Website',
 *           'text'   => '...',
 *           'items'  => [ 'Startseite', 'Money Pages' ],
 *           'result' => '...',
 *         ],
 *       ],
 *       'aside'   => [
 *         'eyebrow' => 'Warum das relevant ist',
 *         'title'   => '...',
 *         'text'    => '...',
 *         'items'   => [ '...' ],
 *         'note'    => '...',
 *         'actions' => [
 *           [
 *             'url'      => home_url( '/kontakt/' ),
 *             'label'    => 'System-Diagnose anfragen',
 *             'class'    => 'nx-btn nx-btn--primary',
 *             'action'   => 'cta_service_system_audit',
 *             'category' => 'lead_gen',
 *           ],
 *         ],
 *       ],
 *     ]
 *   );
 *   get_template_part( 'template-parts/service-system-map' );
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = get_query_var( 'service_system_map', [] );

if ( ! is_array( $data ) ) {
	return;
}

$defaults = [
	'section_id' => 'service-system-map',
	'kicker'     => '',
	'title'      => '',
	'intro'      => '',
	'summary'    => [],
	'layers'     => [],
	'aside'      => [],
];

$data   = wp_parse_args( $data, $defaults );
$layers = is_array( $data['layers'] ) ? $data['layers'] : [];

if ( empty( $layers ) || empty( $data['title'] ) ) {
	return;
}

$aside = wp_parse_args(
	is_array( $data['aside'] ) ? $data['aside'] : [],
	[
		'eyebrow' => '',
		'title'   => '',
		'text'    => '',
		'items'   => [],
		'note'    => '',
		'actions' => [],
	]
);

$aside_has_title = ! empty( $aside['title'] );
?>

<section id="<?php echo esc_attr( $data['section_id'] ); ?>" class="nx-section wp-agentur-system" data-track-section="service_system_map" aria-labelledby="<?php echo esc_attr( $data['section_id'] . '-heading' ); ?>">
	<div class="nx-container">
		<div class="nx-section-header wp-agentur-system__header">
			<?php if ( $data['kicker'] ) : ?>
				<span class="nx-badge nx-badge--ghost"><?php echo esc_html( $data['kicker'] ); ?></span>
			<?php endif; ?>
			<h2 id="<?php echo esc_attr( $data['section_id'] . '-heading' ); ?>" class="nx-headline-section"><?php echo esc_html( $data['title'] ); ?></h2>
			<?php if ( $data['intro'] ) : ?>
				<p class="wp-agentur-system__intro"><?php echo esc_html( $data['intro'] ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $data['summary'] ) ) : ?>
			<div class="wp-agentur-system__summary" role="list" aria-label="<?php esc_attr_e( 'Kernelemente des Systems', 'blocksy-child' ); ?>">
				<?php foreach ( $data['summary'] as $summary_item ) : ?>
					<span class="wp-agentur-system__summary-item" role="listitem"><?php echo esc_html( $summary_item ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="wp-agentur-system__layout">
			<ol class="wp-agentur-system__flow" aria-label="<?php esc_attr_e( 'Vier Ebenen eines professionellen WordPress-Systems', 'blocksy-child' ); ?>">
				<?php foreach ( $layers as $index => $layer ) : ?>
					<?php
					$layer = wp_parse_args(
						is_array( $layer ) ? $layer : [],
						[
							'label'  => '',
							'title'  => '',
							'text'   => '',
							'items'  => [],
							'result' => '',
						]
					);
					?>
					<li class="wp-agentur-system-card">
						<div class="wp-agentur-system-card__rail" aria-hidden="true">
							<span class="wp-agentur-system-card__number"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<?php if ( $index < count( $layers ) - 1 ) : ?>
								<span class="wp-agentur-system-card__line"></span>
							<?php endif; ?>
						</div>

						<div class="wp-agentur-system-card__body">
							<div class="wp-agentur-system-card__header">
								<?php if ( $layer['label'] ) : ?>
									<span class="wp-agentur-system-card__eyebrow"><?php echo esc_html( $layer['label'] ); ?></span>
								<?php endif; ?>
								<h3><?php echo esc_html( $layer['title'] ); ?></h3>
							</div>

							<?php if ( $layer['text'] ) : ?>
								<p class="wp-agentur-system-card__text"><?php echo esc_html( $layer['text'] ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $layer['items'] ) ) : ?>
								<ul class="wp-agentur-system-card__items" aria-label="<?php echo esc_attr( $layer['title'] . ' Inhalte' ); ?>">
									<?php foreach ( $layer['items'] as $item ) : ?>
										<li><?php echo esc_html( $item ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>

						<?php if ( $layer['result'] ) : ?>
							<p class="wp-agentur-system-card__result"><?php echo esc_html( $layer['result'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>

			<?php if ( $aside['title'] || $aside['text'] || ! empty( $aside['items'] ) || ! empty( $aside['actions'] ) ) : ?>
				<aside
					class="wp-agentur-system__aside"
					<?php if ( $aside_has_title ) : ?>
						aria-labelledby="<?php echo esc_attr( $data['section_id'] . '-aside-title' ); ?>"
					<?php else : ?>
						aria-label="<?php esc_attr_e( 'Geschäftlicher Nutzen des Systems', 'blocksy-child' ); ?>"
					<?php endif; ?>
				>
					<?php if ( $aside['eyebrow'] ) : ?>
						<span class="wp-agentur-system__aside-eyebrow"><?php echo esc_html( $aside['eyebrow'] ); ?></span>
					<?php endif; ?>

					<?php if ( $aside['title'] ) : ?>
						<h3 id="<?php echo esc_attr( $data['section_id'] . '-aside-title' ); ?>"><?php echo esc_html( $aside['title'] ); ?></h3>
					<?php endif; ?>

					<?php if ( $aside['text'] ) : ?>
						<p><?php echo esc_html( $aside['text'] ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $aside['items'] ) ) : ?>
						<ul class="wp-agentur-system__benefits" aria-label="<?php esc_attr_e( 'Geschäftlicher Nutzen', 'blocksy-child' ); ?>">
							<?php foreach ( $aside['items'] as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( ! empty( $aside['actions'] ) ) : ?>
						<div class="wp-agentur-system__actions">
							<?php foreach ( $aside['actions'] as $action ) : ?>
								<?php
								$action = wp_parse_args(
									is_array( $action ) ? $action : [],
									[
										'url'      => '',
										'label'    => '',
										'class'    => 'nx-btn nx-btn--ghost',
										'action'   => '',
										'category' => '',
									]
								);
								?>
								<?php if ( $action['url'] && $action['label'] ) : ?>
									<a
										href="<?php echo esc_url( $action['url'] ); ?>"
										class="<?php echo esc_attr( $action['class'] ); ?>"
										<?php if ( $action['action'] ) : ?>
											data-track-action="<?php echo esc_attr( $action['action'] ); ?>"
										<?php endif; ?>
										<?php if ( $action['category'] ) : ?>
											data-track-category="<?php echo esc_attr( $action['category'] ); ?>"
										<?php endif; ?>
									><?php echo esc_html( $action['label'] ); ?></a>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $aside['note'] ) : ?>
						<p class="wp-agentur-system__note"><?php echo esc_html( $aside['note'] ); ?></p>
					<?php endif; ?>
				</aside>
			<?php endif; ?>
		</div>
	</div>
</section>
