<?php
/** Admin status page for the assistant: knowledge file, monthly cost, rebuild. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hu_chat_admin_menu(): void {
	if ( ! hu_chat_assistant_enabled() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	add_management_page( 'KI-Assistent', 'KI-Assistent', 'manage_options', 'hu-chat-assistant', 'hu_chat_admin_page' );
}
add_action( 'admin_menu', 'hu_chat_admin_menu' );

function hu_chat_admin_rebuild(): void {
	if ( ! current_user_can( 'manage_options' ) || ! hu_chat_assistant_enabled() ) {
		wp_die( 'Nicht erlaubt.', 403 );
	}
	check_admin_referer( 'hu_chat_rebuild' );
	@set_time_limit( 300 );
	try {
		$meta   = hu_chat_build_knowledge();
		$result = ! empty( $meta['ok'] ) ? 'ok' : 'incomplete';
	} catch ( Throwable $error ) {
		error_log( '[hu-chat] knowledge_build_failed' );
		$result = 'failed';
	}
	wp_safe_redirect( add_query_arg( [ 'page' => 'hu-chat-assistant', 'built' => $result ], admin_url( 'tools.php' ) ) );
	exit;
}
add_action( 'admin_post_hu_chat_rebuild', 'hu_chat_admin_rebuild' );

function hu_chat_admin_page(): void {
	if ( ! current_user_can( 'manage_options' ) || ! hu_chat_assistant_enabled() ) {
		return;
	}
	$config = hu_chat_config();
	$meta   = get_option( HU_CHAT_KNOWLEDGE_META, [] );
	$cost   = (int) get_option( 'hu_chat_cost_' . gmdate( 'Ym' ), 0 ) / 1000000;
	$built  = isset( $_GET['built'] ) ? sanitize_key( wp_unslash( $_GET['built'] ) ) : '';
	$notes  = [
		'ok'         => [ 'success', 'Wissen neu aufgebaut.' ],
		'incomplete' => [ 'warning', 'Zu wenige Seiten abrufbar. Die bisherige Wissensdatei bleibt aktiv.' ],
		'failed'     => [ 'error', 'Aufbau fehlgeschlagen. Details im PHP-Fehlerprotokoll (nur Kategorie).' ],
	];
	?>
	<div class="wrap">
		<h1>KI-Assistent</h1>
		<?php if ( isset( $notes[ $built ] ) ) : ?>
			<div class="notice notice-<?php echo esc_attr( $notes[ $built ][0] ); ?>"><p><?php echo esc_html( $notes[ $built ][1] ); ?></p></div>
		<?php endif; ?>
		<table class="widefat striped" style="max-width:760px">
			<tbody>
				<tr><th>Modus</th><td><?php echo esc_html( 'live' === $config['mode'] ? 'live (alle Besucher)' : 'preview (nur eingeloggte Admins)' ); ?></td></tr>
				<tr><th>Modell</th><td><code><?php echo esc_html( $config['model'] ); ?></code></td></tr>
				<tr><th>Kosten diesen Monat</th><td><?php echo esc_html( number_format_i18n( $cost, 2 ) . ' USD von ' . number_format_i18n( (float) $config['budget'], 2 ) . ' USD (inkl. offener Reservierungen)' ); ?></td></tr>
				<tr><th>Wissensdatei</th><td>
					<?php if ( ! empty( $meta['built_at'] ) ) : ?>
						<?php echo esc_html( sprintf( 'Stand %s · %s Zeichen', wp_date( 'd.m.Y H:i', (int) $meta['built_at'] ), number_format_i18n( (int) ( $meta['chars'] ?? 0 ) ) ) ); ?>
					<?php else : ?>
						<strong>Noch nicht aufgebaut.</strong> Ohne Wissensdatei antwortet der Assistent nicht.
					<?php endif; ?>
				</td></tr>
				<tr><th>Letzter Versuch</th><td>
					<?php
					if ( ! empty( $meta['attempted_at'] ) ) {
						echo esc_html( sprintf( '%s · %d Seiten gelesen', wp_date( 'd.m.Y H:i', (int) $meta['attempted_at'] ), (int) ( $meta['pages'] ?? 0 ) ) );
						if ( ! empty( $meta['failed'] ) ) {
							echo '<br>Nicht abrufbar: <code>' . esc_html( implode( ', ', (array) $meta['failed'] ) ) . '</code>';
						}
					} else {
						echo '–';
					}
					?>
				</td></tr>
			</tbody>
		</table>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:16px">
			<input type="hidden" name="action" value="hu_chat_rebuild">
			<?php wp_nonce_field( 'hu_chat_rebuild' ); ?>
			<?php submit_button( 'Wissen jetzt aufbauen', 'primary', 'submit', false ); ?>
			<span class="description">&nbsp;Liest alle Seiten aus llms.txt und die letzten Blogartikel. Dauert bis zu einer Minute.</span>
		</form>
		<p class="description" style="margin-top:16px">Automatisch: täglich und nach jedem Deploy. Preise, Kontaktadresse und Antwortzeit kommen bei jeder Antwort frisch aus dem Kanon.</p>
	</div>
	<?php
}
