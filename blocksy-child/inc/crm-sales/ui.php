<?php
/** Nexus CRM sales operations module. @package Blocksy_Child */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Compute sales metrics for the dashboard.
 *
 * @param array<int, array<string, mixed>> $opportunities Opportunities.
 * @return array<string, int>
 */
function nexus_get_crm_sales_metrics( $opportunities ) {
	$metrics = [
		'open_count'     => 0,
		'pipeline_cents' => 0,
		'weighted_cents' => 0,
		'followups_due'  => 0,
		'won_month'      => 0,
	];
	$stages      = nexus_get_crm_sales_stages();
	$now         = current_time( 'timestamp', true );
	$start_month = ( new DateTimeImmutable( 'first day of this month 00:00:00', wp_timezone() ) )->getTimestamp();

	foreach ( $opportunities as $opportunity ) {
		$stage = (string) $opportunity['stage'];
		$value = (int) $opportunity['value_cents'];
		if ( nexus_is_crm_sales_stage_open( $stage ) ) {
			$metrics['open_count']++;
			$metrics['pipeline_cents'] += $value;
			$probability = isset( $stages[ $stage ] ) ? (int) $stages[ $stage ]['probability'] : 0;
			$metrics['weighted_cents'] += (int) round( $value * $probability / 100 );
			$next_at = (int) $opportunity['next_action_at'];
			if ( $next_at > 0 && $next_at <= $now ) {
				$metrics['followups_due']++;
			}
		} elseif ( 'won' === $stage && (int) $opportunity['won_at'] >= $start_month ) {
			$metrics['won_month'] += $value;
		}
	}

	return $metrics;
}

/** Render an admin notice passed through the sales redirects. */
function nexus_render_crm_sales_notice() {
	if ( empty( $_GET['crm_notice'] ) ) {
		return;
	}
	?>
	<div class="notice notice-success is-dismissible"><p><?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['crm_notice'] ) ) ); ?></p></div>
	<?php
}

/** Render the sales workspace or one opportunity detail. */
function nexus_render_crm_sales_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( 'Keine Berechtigung.' );
	}

	nexus_render_crm_sales_notice();
	$opportunity_id = isset( $_GET['opportunity'] ) ? absint( $_GET['opportunity'] ) : 0;
	if ( $opportunity_id > 0 ) {
		nexus_render_crm_sales_opportunity_detail( $opportunity_id );
		return;
	}

	$opportunities = nexus_get_crm_opportunities();
	$metrics       = nexus_get_crm_sales_metrics( $opportunities );
	$stages        = nexus_get_crm_sales_stages();
	$now           = current_time( 'timestamp', true );
	$due           = array_values(
		array_filter(
			$opportunities,
			static function ( $opportunity ) use ( $now ) {
				return nexus_is_crm_sales_stage_open( (string) $opportunity['stage'] )
					&& (int) $opportunity['next_action_at'] > 0
					&& (int) $opportunity['next_action_at'] <= $now;
			}
		)
	);
	?>
	<div class="wrap nexus-crm-sales-shell">
		<div class="nexus-crm-sales-head">
			<div>
				<p class="nexus-crm-eyebrow">Nexus CRM · Pre-Sales</p>
				<h1>Vertrieb</h1>
				<p>Von der ersten Anfrage bis zum gewonnenen Auftrag. Kundenprojekte bleiben anschließend im Kundenportal.</p>
			</div>
			<div class="nexus-crm-head-actions">
				<a class="button" href="<?php echo esc_url( nexus_get_crm_inbox_admin_url() ); ?>">Kommunikation</a>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="nexus_crm_sales_backfill">
					<?php wp_nonce_field( 'nexus_crm_sales_backfill' ); ?>
					<button class="button" type="submit">Bestehende Leads übernehmen</button>
				</form>
			</div>
		</div>

		<div class="nexus-crm-kpis" aria-label="Vertriebskennzahlen">
			<div><span>Offene Chancen</span><strong><?php echo esc_html( (string) $metrics['open_count'] ); ?></strong></div>
			<div><span>Pipeline-Wert</span><strong><?php echo esc_html( nexus_crm_sales_format_money( $metrics['pipeline_cents'] ) ); ?></strong></div>
			<div><span>Gewichtete Pipeline</span><strong><?php echo esc_html( nexus_crm_sales_format_money( $metrics['weighted_cents'] ) ); ?></strong></div>
			<div><span>Follow-ups fällig</span><strong><?php echo esc_html( (string) $metrics['followups_due'] ); ?></strong></div>
			<div><span>Gewonnen im Monat</span><strong><?php echo esc_html( nexus_crm_sales_format_money( $metrics['won_month'] ) ); ?></strong></div>
		</div>

		<div class="nexus-crm-sales-grid">
			<section class="nexus-crm-panel nexus-crm-followups" aria-labelledby="nexus-followup-title">
				<div class="nexus-crm-panel-head"><div><p class="nexus-crm-eyebrow">Heute</p><h2 id="nexus-followup-title">Fällige Follow-ups</h2></div><span class="nexus-crm-count"><?php echo esc_html( (string) count( $due ) ); ?></span></div>
				<?php if ( empty( $due ) ) : ?>
					<p class="nexus-crm-empty">Keine überfällige nächste Aktion.</p>
				<?php else : ?>
					<div class="nexus-crm-action-list">
						<?php foreach ( array_slice( $due, 0, 12 ) as $item ) : ?>
							<?php $contact = nexus_get_crm_sales_contact_data( (int) $item['contact_id'] ); ?>
							<a href="<?php echo esc_url( nexus_get_crm_sales_admin_url( [ 'opportunity' => (int) $item['id'] ] ) ); ?>">
								<strong><?php echo esc_html( $contact['company'] ?: $contact['name'] ); ?></strong>
								<span><?php echo esc_html( (string) $item['next_action'] ?: 'Nächste Aktion' ); ?> · <?php echo esc_html( wp_date( 'd.m.Y H:i', (int) $item['next_action_at'] ) ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</section>

			<section class="nexus-crm-panel" aria-labelledby="nexus-new-lead-title">
				<div class="nexus-crm-panel-head"><div><p class="nexus-crm-eyebrow">Manuell</p><h2 id="nexus-new-lead-title">Lead anlegen</h2></div></div>
				<form class="nexus-crm-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="nexus_crm_sales_create">
					<?php wp_nonce_field( 'nexus_crm_sales_create' ); ?>
					<label>Name<input type="text" name="name"></label>
					<label>Unternehmen<input type="text" name="company"></label>
					<label>E-Mail<input type="email" name="email" required></label>
					<label>Leistung / Bedarf<input type="text" name="service" placeholder="z. B. Website-Relaunch"></label>
					<label>Potenzialwert (€)<input type="text" inputmode="decimal" name="value" placeholder="8900"></label>
					<button class="button button-primary" type="submit">Lead anlegen</button>
				</form>
			</section>
		</div>

		<section class="nexus-crm-panel nexus-crm-pipeline-section" aria-labelledby="nexus-pipeline-title">
			<div class="nexus-crm-panel-head"><div><p class="nexus-crm-eyebrow">Sales Pipeline</p><h2 id="nexus-pipeline-title">Aktive Chancen</h2></div></div>
			<div class="nexus-crm-pipeline">
				<?php foreach ( $stages as $stage_key => $stage_config ) : ?>
					<?php if ( empty( $stage_config['open'] ) ) { continue; } ?>
					<?php $stage_items = array_values( array_filter( $opportunities, static function ( $item ) use ( $stage_key ) { return $stage_key === $item['stage']; } ) ); ?>
					<section class="nexus-crm-stage" aria-labelledby="nexus-stage-<?php echo esc_attr( $stage_key ); ?>">
						<header><h3 id="nexus-stage-<?php echo esc_attr( $stage_key ); ?>"><?php echo esc_html( $stage_config['label'] ); ?></h3><span><?php echo esc_html( (string) count( $stage_items ) ); ?></span></header>
						<div class="nexus-crm-stage-list">
							<?php if ( empty( $stage_items ) ) : ?><p class="nexus-crm-empty">Keine Chancen</p><?php endif; ?>
							<?php foreach ( $stage_items as $item ) : ?>
								<?php $contact = nexus_get_crm_sales_contact_data( (int) $item['contact_id'] ); ?>
								<a class="nexus-crm-deal" href="<?php echo esc_url( nexus_get_crm_sales_admin_url( [ 'opportunity' => (int) $item['id'] ] ) ); ?>">
									<strong><?php echo esc_html( $contact['company'] ?: $contact['name'] ); ?></strong>
									<span><?php echo esc_html( (string) $item['service'] ?: 'Ohne Leistungszuordnung' ); ?></span>
									<?php if ( (int) $item['value_cents'] > 0 ) : ?><b><?php echo esc_html( nexus_crm_sales_format_money( (int) $item['value_cents'] ) ); ?></b><?php endif; ?>
									<?php if ( (int) $item['next_action_at'] > 0 ) : ?><small class="<?php echo (int) $item['next_action_at'] <= $now ? 'is-overdue' : ''; ?>"><?php echo esc_html( (string) $item['next_action'] ?: 'Nächste Aktion' ); ?> · <?php echo esc_html( wp_date( 'd.m.', (int) $item['next_action_at'] ) ); ?></small><?php endif; ?>
								</a>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
		</section>
	</div>
	<?php
}

/** Render one opportunity detail with edit, email and timeline controls. */
function nexus_render_crm_sales_opportunity_detail( $opportunity_id ) {
	$opportunity = nexus_get_crm_opportunity( $opportunity_id );
	if ( null === $opportunity ) {
		echo '<div class="wrap"><h1>Sales-Chance nicht gefunden.</h1></div>';
		return;
	}

	$contact      = nexus_get_crm_sales_contact_data( (int) $opportunity['contact_id'] );
	$activities   = nexus_get_crm_activities( $opportunity_id, 60 );
	$auto_enabled = (bool) get_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_enabled', true );
	$auto_at      = (int) get_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_at', true );
	$auto_subject = (string) get_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_subject', true );
	$auto_body    = (string) get_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_body', true );
	?>
	<div class="wrap nexus-crm-sales-shell">
		<p><a href="<?php echo esc_url( nexus_get_crm_sales_admin_url() ); ?>">← Zur Pipeline</a></p>
		<div class="nexus-crm-sales-head">
			<div><p class="nexus-crm-eyebrow"><?php echo esc_html( nexus_get_crm_sales_stage_label( (string) $opportunity['stage'] ) ); ?></p><h1><?php echo esc_html( $contact['company'] ?: $contact['name'] ); ?></h1><p><?php echo esc_html( $contact['name'] ); ?> · <a href="mailto:<?php echo esc_attr( $contact['email'] ); ?>"><?php echo esc_html( $contact['email'] ); ?></a><?php echo $contact['phone'] ? ' · ' . esc_html( $contact['phone'] ) : ''; ?></p></div>
			<div class="nexus-crm-head-actions"><a class="button" href="<?php echo esc_url( get_edit_post_link( (int) $opportunity['contact_id'] ) ); ?>">Kontakt öffnen</a><?php if ( (int) $opportunity['review_request_id'] > 0 ) : ?><a class="button" href="<?php echo esc_url( get_edit_post_link( (int) $opportunity['review_request_id'] ) ); ?>">Marktcheck öffnen</a><?php endif; ?></div>
		</div>

		<div class="nexus-crm-detail-grid">
			<section class="nexus-crm-panel">
				<div class="nexus-crm-panel-head"><div><p class="nexus-crm-eyebrow">Steuerung</p><h2>Sales-Chance</h2></div></div>
				<form class="nexus-crm-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="nexus_crm_sales_save"><input type="hidden" name="opportunity_id" value="<?php echo esc_attr( (string) $opportunity_id ); ?>">
					<?php wp_nonce_field( 'nexus_crm_sales_save' ); ?>
					<label>Pipeline-Stufe<select name="stage"><?php foreach ( nexus_get_crm_sales_stages() as $value => $config ) : ?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( $opportunity['stage'], $value ); ?>><?php echo esc_html( $config['label'] ); ?></option><?php endforeach; ?></select></label>
					<label>Leistung / Bedarf<input type="text" name="service" value="<?php echo esc_attr( (string) $opportunity['service'] ); ?>"></label>
					<label>Potenzialwert (€)<input type="text" inputmode="decimal" name="value" value="<?php echo esc_attr( (string) ( (int) $opportunity['value_cents'] / 100 ) ); ?>"></label>
					<label>Nächste Aktion<input type="text" name="next_action" value="<?php echo esc_attr( (string) $opportunity['next_action'] ); ?>" placeholder="z. B. Angebot nachfassen"></label>
					<label>Fällig am<input type="datetime-local" name="next_action_at" value="<?php echo esc_attr( nexus_crm_sales_datetime_input_value( (int) $opportunity['next_action_at'] ) ); ?>"></label>
					<label>Interne Notizen<textarea name="notes" rows="6"><?php echo esc_textarea( (string) $opportunity['notes'] ); ?></textarea></label>
					<label>Verlustgrund <span class="description">nur bei „Verloren“</span><input type="text" name="loss_reason" value="<?php echo esc_attr( (string) $opportunity['loss_reason'] ); ?>"></label>
					<fieldset class="nexus-crm-automation">
						<legend>Automatisches E-Mail-Follow-up</legend>
						<label class="nexus-crm-check"><input type="checkbox" name="auto_followup_enabled" value="1" <?php checked( $auto_enabled ); ?>> Einmalig automatisch senden</label>
						<p class="description">Nur aktivieren, wenn der Kontakt eine vertriebliche Nachfasskommunikation erwartet. Die Blog-DOI-Logik bleibt davon getrennt.</p>
						<label>Senden am<input type="datetime-local" name="auto_followup_at" value="<?php echo esc_attr( nexus_crm_sales_datetime_input_value( $auto_at ) ); ?>"></label>
						<label>Betreff<input type="text" name="auto_followup_subject" value="<?php echo esc_attr( $auto_subject ); ?>"></label>
						<label>Nachricht<textarea name="auto_followup_body" rows="7"><?php echo esc_textarea( $auto_body ); ?></textarea></label>
					</fieldset>
					<button class="button button-primary" type="submit">Speichern</button>
				</form>
			</section>

			<section class="nexus-crm-panel">
				<div class="nexus-crm-panel-head"><div><p class="nexus-crm-eyebrow">E-Mail</p><h2>Direkt schreiben</h2></div></div>
				<p class="nexus-crm-muted">Versand läuft über den vorhandenen WordPress-/Brevo-Layer und wird in der Aktivitätshistorie protokolliert.</p>
				<form class="nexus-crm-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="nexus_crm_sales_email"><input type="hidden" name="opportunity_id" value="<?php echo esc_attr( (string) $opportunity_id ); ?>">
					<?php wp_nonce_field( 'nexus_crm_sales_email' ); ?>
					<label>An<input type="email" value="<?php echo esc_attr( $contact['email'] ); ?>" readonly></label>
					<label>Betreff<input type="text" name="subject" required></label>
					<label>Nachricht<textarea name="body" rows="9" required></textarea></label>
					<button class="button" type="submit">E-Mail senden</button>
				</form>
			</section>
		</div>

		<section class="nexus-crm-panel">
			<div class="nexus-crm-panel-head"><div><p class="nexus-crm-eyebrow">Timeline</p><h2>Aktivitäten</h2></div><span class="nexus-crm-count"><?php echo esc_html( (string) count( $activities ) ); ?></span></div>
			<?php nexus_render_crm_activity_list( $activities ); ?>
		</section>
	</div>
	<?php
}

/** Render an activity list shared by deal detail and communication feed. */
function nexus_render_crm_activity_list( $activities ) {
	if ( empty( $activities ) ) {
		echo '<p class="nexus-crm-empty">Noch keine Aktivitäten protokolliert.</p>';
		return;
	}

	echo '<div class="nexus-crm-timeline">';
	foreach ( $activities as $activity ) {
		$contact_id     = (int) get_post_meta( $activity->ID, '_nexus_activity_contact_id', true );
		$opportunity_id = (int) get_post_meta( $activity->ID, '_nexus_activity_opportunity_id', true );
		$channel        = (string) get_post_meta( $activity->ID, '_nexus_activity_channel', true );
		$direction      = (string) get_post_meta( $activity->ID, '_nexus_activity_direction', true );
		$status         = (string) get_post_meta( $activity->ID, '_nexus_activity_status', true );
		$occurred       = (int) get_post_meta( $activity->ID, '_nexus_activity_occurred_at', true );
		$contact        = nexus_get_crm_sales_contact_data( $contact_id );
		?>
		<article class="nexus-crm-activity">
			<div class="nexus-crm-activity-meta"><span><?php echo esc_html( strtoupper( $channel ?: 'crm' ) ); ?> · <?php echo esc_html( $direction ?: 'internal' ); ?> · <?php echo esc_html( $status ?: 'recorded' ); ?></span><time><?php echo esc_html( wp_date( 'd.m.Y H:i', $occurred ?: get_post_timestamp( $activity ) ) ); ?></time></div>
			<h3><?php echo esc_html( $activity->post_title ); ?></h3>
			<?php if ( '' !== trim( (string) $activity->post_content ) ) : ?><p><?php echo nl2br( esc_html( $activity->post_content ) ); ?></p><?php endif; ?>
			<div class="nexus-crm-activity-links"><?php if ( $opportunity_id > 0 ) : ?><a href="<?php echo esc_url( nexus_get_crm_sales_admin_url( [ 'opportunity' => $opportunity_id ] ) ); ?>"><?php echo esc_html( $contact['company'] ?: $contact['name'] ); ?></a><?php endif; ?></div>
		</article>
		<?php
	}
	echo '</div>';
}

/** Render provider-agnostic communication feed. */
function nexus_render_crm_inbox_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( 'Keine Berechtigung.' );
	}

	$activities = nexus_get_crm_activities( 0, 120 );
	?>
	<div class="wrap nexus-crm-sales-shell">
		<div class="nexus-crm-sales-head">
			<div><p class="nexus-crm-eyebrow">Nexus CRM</p><h1>Kommunikation</h1><p>Zentrale Aktivitätsschicht für E-Mail und künftige Kanal-Adapter.</p></div>
			<div class="nexus-crm-head-actions"><a class="button" href="<?php echo esc_url( nexus_get_crm_sales_admin_url() ); ?>">Zur Pipeline</a></div>
		</div>
		<div class="notice notice-info inline"><p><strong>Aktiv:</strong> CRM-Aktivitäten und aus Nexus gesendete E-Mails. Eingehende E-Mail, WhatsApp oder SMS werden erst sichtbar, sobald der jeweilige Provider einen verifizierten Webhook/Adapter liefert. Die Timeline ist dafür bereits kanalneutral aufgebaut.</p></div>
		<section class="nexus-crm-panel">
			<div class="nexus-crm-panel-head"><div><p class="nexus-crm-eyebrow">Activity Feed</p><h2>Alle Kommunikationsereignisse</h2></div><span class="nexus-crm-count"><?php echo esc_html( (string) count( $activities ) ); ?></span></div>
			<?php nexus_render_crm_activity_list( $activities ); ?>
		</section>
	</div>
	<?php
}
