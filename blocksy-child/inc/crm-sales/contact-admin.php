<?php
/** Nexus CRM sales operations module. @package Blocksy_Child */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Add sales columns to the shared contact list.
 *
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string>
 */
function nexus_add_crm_sales_contact_columns( $columns ) {
	$result = [];
	foreach ( $columns as $key => $label ) {
		$result[ $key ] = $label;
		if ( 'contact_status' === $key ) {
			$result['sales_stage'] = 'Vertrieb';
			$result['sales_value'] = 'Pipeline';
			$result['sales_next']  = 'Nächste Aktion';
		}
	}

	return $result;
}
add_filter( 'manage_nexus_contact_posts_columns', 'nexus_add_crm_sales_contact_columns', 20 );

/**
 * Render sales columns on the shared contact list.
 *
 * @param string $column Column key.
 * @param int    $post_id Contact post ID.
 * @return void
 */
function nexus_render_crm_sales_contact_column( $column, $post_id ) {
	if ( 'sales_stage' === $column ) {
		$stage = (string) get_post_meta( $post_id, '_nexus_contact_sales_stage', true );
		if ( '' === $stage ) {
			echo '—';
			return;
		}
		echo '<span class="nexus-review-badge nexus-review-badge-' . esc_attr( $stage ) . '">' . esc_html( nexus_get_crm_sales_stage_label( $stage ) ) . '</span>';
		return;
	}

	if ( 'sales_value' === $column ) {
		$cents = (int) get_post_meta( $post_id, '_nexus_contact_pipeline_value_cents', true );
		echo $cents > 0 ? esc_html( nexus_crm_sales_format_money( $cents ) ) : '—';
		return;
	}

	if ( 'sales_next' === $column ) {
		$action = (string) get_post_meta( $post_id, '_nexus_contact_next_action', true );
		$due    = (int) get_post_meta( $post_id, '_nexus_contact_next_action_at', true );
		if ( '' === $action && $due <= 0 ) {
			echo '—';
			return;
		}
		if ( '' !== $action ) {
			echo esc_html( $action );
		}
		if ( $due > 0 ) {
			echo '<div class="nexus-review-muted">' . esc_html( wp_date( 'd.m.Y H:i', $due ) ) . '</div>';
		}
	}
}
add_action( 'manage_nexus_contact_posts_custom_column', 'nexus_render_crm_sales_contact_column', 20, 2 );

/**
 * Add a sales-stage filter to the contact list.
 *
 * @param string $post_type Current post type.
 * @return void
 */
function nexus_render_crm_sales_contact_filter( $post_type ) {
	if ( 'nexus_contact' !== $post_type ) {
		return;
	}

	$current = isset( $_GET['nexus_sales_stage'] ) ? sanitize_key( wp_unslash( $_GET['nexus_sales_stage'] ) ) : '';
	?>
	<select name="nexus_sales_stage">
		<option value="">Alle Vertriebsstufen</option>
		<?php foreach ( nexus_get_crm_sales_stages() as $value => $config ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>><?php echo esc_html( $config['label'] ); ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}
add_action( 'restrict_manage_posts', 'nexus_render_crm_sales_contact_filter', 20 );

/**
 * Apply the sales-stage contact filter.
 *
 * @param WP_Query $query Current admin query.
 * @return void
 */
function nexus_filter_crm_sales_contact_query( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'nexus_contact' !== $query->get( 'post_type' ) || empty( $_GET['nexus_sales_stage'] ) ) {
		return;
	}

	$stage = sanitize_key( wp_unslash( $_GET['nexus_sales_stage'] ) );
	if ( ! isset( nexus_get_crm_sales_stages()[ $stage ] ) ) {
		return;
	}

	$meta_query   = (array) $query->get( 'meta_query' );
	$meta_query[] = [
		'key'   => '_nexus_contact_sales_stage',
		'value' => $stage,
	];
	$query->set( 'meta_query', $meta_query );
}
add_action( 'pre_get_posts', 'nexus_filter_crm_sales_contact_query', 20 );

/**
 * Add a compact sales box to each shared CRM contact.
 *
 * @return void
 */
function nexus_register_crm_sales_contact_meta_box() {
	add_meta_box(
		'nexus-contact-sales',
		'Vertrieb',
		'nexus_render_crm_sales_contact_meta_box',
		'nexus_contact',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes_nexus_contact', 'nexus_register_crm_sales_contact_meta_box' );

/**
 * Render the contact-level sales summary.
 *
 * @param WP_Post $post Contact post.
 * @return void
 */
function nexus_render_crm_sales_contact_meta_box( $post ) {
	$opportunity_id = (int) get_post_meta( $post->ID, '_nexus_contact_sales_opportunity_id', true );
	$opportunity    = nexus_get_crm_opportunity( $opportunity_id );

	if ( null === $opportunity ) {
		echo '<p>Noch keine aktive Sales-Chance.</p>';
		if ( nexus_crm_contact_is_sales_relevant( $post->ID ) ) {
			echo '<p><a class="button" href="' . esc_url( nexus_get_crm_sales_admin_url() ) . '">Vertrieb öffnen</a></p>';
		}
		return;
	}

	echo '<p><strong>' . esc_html( nexus_get_crm_sales_stage_label( (string) $opportunity['stage'] ) ) . '</strong></p>';
	if ( (int) $opportunity['value_cents'] > 0 ) {
		echo '<p>Wert: ' . esc_html( nexus_crm_sales_format_money( (int) $opportunity['value_cents'] ) ) . '</p>';
	}
	if ( '' !== (string) $opportunity['next_action'] ) {
		echo '<p>Nächster Schritt:<br>' . esc_html( (string) $opportunity['next_action'] ) . '</p>';
	}
	if ( (int) $opportunity['next_action_at'] > 0 ) {
		echo '<p>Fällig: ' . esc_html( wp_date( 'd.m.Y H:i', (int) $opportunity['next_action_at'] ) ) . '</p>';
	}
	echo '<p><a class="button button-primary" href="' . esc_url( nexus_get_crm_sales_admin_url( [ 'opportunity' => (int) $opportunity['id'] ] ) ) . '">Sales-Chance öffnen</a></p>';
}
