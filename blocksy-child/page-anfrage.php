<?php
/**
 * Native page template for the canonical slug:
 * /anfrage/
 *
 * Hosts the multi-step energy-intake form previously embedded inline
 * on the Solar/Wärmepumpen landing page. Single-purpose page — no
 * marketing copy, only the qualified request flow.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$privacy_url = function_exists( 'nexus_get_page_url' ) ? nexus_get_page_url( [ 'datenschutz' ], home_url( '/datenschutz/' ) ) : home_url( '/datenschutz/' );
$page_url    = function_exists( 'nexus_get_anfrage_url' ) ? nexus_get_anfrage_url() : home_url( '/anfrage/' );
$flow_steps  = function_exists( 'nexus_get_energy_intake_flow_definition' ) ? nexus_get_energy_intake_flow_definition() : [];
$e3_summary  = function_exists( 'hu_e3_summary' ) ? hu_e3_summary( 'proof' ) : 'Referenz E3 New Energy: eigene Anfragen, Abschlussquote und niedrigere Kosten pro Anfrage.';

$form_values  = [];
$form_error   = '';
$form_success = null;

if (
	'POST' === $_SERVER['REQUEST_METHOD']
	&& isset( $_POST['intake_variant'] )
	&& 'energy_systems' === sanitize_key( (string) wp_unslash( $_POST['intake_variant'] ) )
) {
	$form_values = wp_unslash( $_POST );
	$submission  = function_exists( 'nexus_process_review_request_submission' )
		? nexus_process_review_request_submission( $form_values )
		: new WP_Error( 'missing_handler', 'Die Anfrage konnte gerade nicht verarbeitet werden.' );

	if ( is_wp_error( $submission ) ) {
		$form_error = $submission->get_error_message();
	} else {
		unset( $submission['http_status'] );
		$form_success = $submission;
		$form_values  = [];
	}
}

$option_labels = [];
foreach ( $flow_steps as $step ) {
	if ( empty( $step['name'] ) || empty( $step['options'] ) || ! is_array( $step['options'] ) ) {
		continue;
	}

	$option_labels[ $step['name'] ] = [];
	foreach ( $step['options'] as $option_value => $option_definition ) {
		$option_labels[ $step['name'] ][ $option_value ] = isset( $option_definition['label'] ) ? (string) $option_definition['label'] : (string) $option_value;
	}
}

$get_value = static function( $field_name ) use ( $form_values ) {
	$value = $form_values[ $field_name ] ?? '';

	if ( is_array( $value ) ) {
		return '';
	}

	return trim( (string) $value );
};

$get_summary_value = static function( $field_name ) use ( $get_value, $option_labels ) {
	$value = $get_value( $field_name );

	if ( '' === $value ) {
		if ( 'measurement_state' === $field_name ) {
			return 'Nur wenn relevant';
		}

		return 'Noch offen';
	}

	if ( isset( $option_labels[ $field_name ][ $value ] ) ) {
		return $option_labels[ $field_name ][ $value ];
	}

	if ( 'page_url' === $field_name ) {
		$host = wp_parse_url( $value, PHP_URL_HOST );

		return is_string( $host ) && '' !== $host ? $host : $value;
	}

	if ( 'current_challenge' === $field_name ) {
		return function_exists( 'nexus_truncate' ) ? nexus_truncate( $value, 72 ) : $value;
	}

	return $value;
};

get_header();
?>
<main id="main" class="site-main">
	<div class="energy-page-wrapper" data-track-section="energy_request" data-track-funnel-stage="energy_request">
		<section class="nx-section nx-hero energy-hero energy-hero--compact" id="hero">
			<div class="nx-container">
				<div class="energy-hero__copy energy-hero__copy--centered">
					<span class="nx-badge nx-badge--gold">Standortbestimmung</span>
					<h1 class="nx-hero__title">5 Fragen, ca. 90 Sekunden &mdash; Antwort innerhalb von 48 Stunden per E-Mail.</h1>
					<p class="nx-hero__subtitle">Region, Lead-Volumen, CPL, Engpass, Kontakt. Keine Pflicht-Calls, keine Sales-Hotline im Nachgang. Wir prüfen, ob Infrastruktur statt Miete für Ihren Betrieb ein realistischer Hebel ist &mdash; bei Nicht-Eignung erhalten Sie einen ehrlichen Hinweis auf eine realistischere Alternative.</p>
					<p class="nx-cta-microcopy"><?php echo esc_html( $e3_summary ); ?></p>
				</div>
			</div>
		</section>

		<?php
		if ( function_exists( 'hu_render_founding_cohort_block' ) ) {
			echo hu_render_founding_cohort_block(
				[
					'variant' => 'full',
					'id'      => 'founding-cohort-anfrage',
				]
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>

		<section class="nx-section energy-section energy-form-section" id="energie-anfrage">
			<div class="nx-container">
				<div class="energy-form-shell">
					<div class="energy-form-shell__flow">
						<div class="energy-form-shell__main">
							<?php if ( $form_success ) : ?>
								<div id="energy-request-success" class="review-success energy-review-success is-server-success" role="status" aria-live="polite" aria-atomic="true">
									<div class="review-success-pill">Eingegangen</div>
									<h3>Ihre Standortbestimmung liegt in der Bearbeitung.</h3>
									<p class="review-success-copy"><?php echo esc_html( $form_success['message'] ?? 'Eingegangen. Ihre Standortbestimmung liegt in der Bearbeitung.' ); ?></p>
									<p class="review-success-timeline">Sie erhalten innerhalb von <strong>48 Werktagsstunden</strong> eine E-Mail von <strong>hasim@hasimuener.de</strong>.</p>
									<ul class="review-success-list">
										<li>ehrliche Einschätzung, ob WGOS für Ihren Betrieb der richtige Hebel ist</li>
										<li>bei Eignung: Vorschlag für ein 30-minütiges Erstgespräch per Telefon oder Video</li>
										<li>bei Nicht-Eignung: konkreter Hinweis, welche Alternative für Ihre Situation realistischer ist</li>
									</ul>
									<p class="review-success-inbox-hint">Falls die E-Mail nicht ankommt, prüfen Sie bitte den Spam-Ordner oder schreiben direkt an <a href="mailto:hasim@hasimuener.de">hasim@hasimuener.de</a>.</p>
								</div>
							<?php else : ?>
								<form
									id="energy-intake-form"
									class="review-funnel energy-intake-form"
									action="<?php echo esc_url( trailingslashit( $page_url ) . '#energie-anfrage' ); ?>"
									method="post"
									data-energy-form
									novalidate
								>
									<input type="hidden" name="company_website" value="">
									<input type="hidden" name="started_at" value="">
									<input type="hidden" name="audit_type" value="growth_audit">
									<input type="hidden" name="intake_variant" value="energy_systems">
									<input type="hidden" name="ads_source" value="">
									<input type="hidden" name="ads_keyword" value="">

									<div class="review-progress energy-progress" aria-label="Fortschritt im Branchen-Flow">
										<div class="review-progress-head">
											<div class="review-progress-copy">
												<strong id="energy-progress-current" aria-live="polite" aria-atomic="true">Schritt 1 von <?php echo esc_html( (string) count( $flow_steps ) ); ?> &mdash; <?php echo ! empty( $flow_steps[0]['title_short'] ) ? esc_html( $flow_steps[0]['title_short'] ) : 'Region'; ?></strong>
											</div>
										</div>
										<div
											class="review-progress-track"
											id="energy-progress-track"
											role="progressbar"
											aria-label="Fortschritt im Anfrage-Flow"
											aria-valuemin="1"
											aria-valuemax="<?php echo esc_attr( (string) count( $flow_steps ) ); ?>"
											aria-valuenow="1"
											aria-valuetext="Abschnitt 1 von <?php echo esc_attr( (string) count( $flow_steps ) ); ?>: <?php echo ! empty( $flow_steps[0]['title_short'] ) ? esc_attr( $flow_steps[0]['title_short'] ) : 'Region'; ?>"
										>
											<div class="review-progress-fill" id="energy-progress-fill"></div>
										</div>
									</div>

									<div class="screen-reader-text" aria-live="assertive" aria-atomic="true" data-energy-step-live></div>

									<details class="review-mobile-summary">
										<summary>Ihre Angaben bisher</summary>
										<dl class="review-brief-list review-brief-list--mobile">
											<?php foreach ( $flow_steps as $step ) : ?>
												<?php if ( empty( $step['name'] ) || empty( $step['summary_label'] ) ) : ?>
													<?php continue; ?>
												<?php endif; ?>
												<div class="review-brief-row">
													<dt><?php echo esc_html( $step['summary_label'] ); ?></dt>
													<dd data-energy-summary="<?php echo esc_attr( $step['name'] ); ?>"><?php echo esc_html( $get_summary_value( $step['name'] ) ); ?></dd>
												</div>
											<?php endforeach; ?>
										</dl>
									</details>

									<noscript>
										<p class="energy-noscript-note">Ohne JavaScript bleibt das Formular vollständig nutzbar, aber ohne Schrittlogik und Auto-Advance.</p>
									</noscript>

									<?php foreach ( $flow_steps as $index => $step ) : ?>
										<?php
										$step_id         = isset( $step['id'] ) ? (string) $step['id'] : 'step-' . (string) $index;
										$field_key       = isset( $step['name'] ) ? (string) $step['name'] : '';
										$choice_error_id = 'energy-error-' . $step_id;
										$is_active       = 0 === $index;
										?>
										<section
											id="<?php echo esc_attr( 'energy-step-' . $step_id ); ?>"
											class="review-step energy-step<?php echo esc_attr( $is_active ? ' is-active' : '' ); ?>"
											data-energy-step-id="<?php echo esc_attr( $step_id ); ?>"
											data-energy-step-index="<?php echo esc_attr( (string) $index ); ?>"
											data-energy-step-label="<?php echo esc_attr( $step['title_short'] ); ?>"
											data-energy-field="<?php echo esc_attr( $field_key ); ?>"
											data-energy-kind="<?php echo esc_attr( $step['kind'] ); ?>"
											<?php if ( ! empty( $step['next'] ) ) : ?>
												data-energy-next-step="<?php echo esc_attr( $step['next'] ); ?>"
											<?php endif; ?>
											<?php if ( ! empty( $step['auto_advance'] ) ) : ?>
												data-energy-auto-advance="true"
											<?php endif; ?>
											<?php if ( ! empty( $step['show_when']['field'] ) && ! empty( $step['show_when']['values'] ) ) : ?>
												data-energy-show-field="<?php echo esc_attr( $step['show_when']['field'] ); ?>"
												data-energy-show-values="<?php echo esc_attr( implode( ',', array_map( 'sanitize_key', (array) $step['show_when']['values'] ) ) ); ?>"
											<?php endif; ?>
											<?php if ( ! empty( $step['next_by_value'] ) ) : ?>
												data-energy-next-map="<?php echo esc_attr( wp_json_encode( $step['next_by_value'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ); ?>"
											<?php endif; ?>
										>
											<h3 class="energy-step__title"><?php echo esc_html( $step['question'] ); ?></h3>

											<?php if ( 'single_choice' === $step['kind'] ) : ?>
												<fieldset class="review-choice-block energy-choice-block" aria-describedby="<?php echo esc_attr( trim( 'energy-help-' . $step_id . ' ' . $choice_error_id ) ); ?>">
													<legend><?php echo esc_html( $step['summary_label'] ); ?></legend>
													<p class="review-choice-help" id="<?php echo esc_attr( 'energy-help-' . $step_id ); ?>"><?php echo esc_html( $step['description'] ); ?></p>
													<div class="review-option-group energy-option-group">
														<?php foreach ( $step['options'] as $option_value => $option_definition ) : ?>
															<label class="review-option energy-option">
																<input
																	type="radio"
																	name="<?php echo esc_attr( $field_key ); ?>"
																	value="<?php echo esc_attr( $option_value ); ?>"
																	data-energy-label="<?php echo esc_attr( $option_definition['label'] ); ?>"
																	<?php checked( $get_value( $field_key ), $option_value ); ?>
																	required
																>
																<div class="review-option-copy">
																	<strong data-energy-option-label><?php echo esc_html( $option_definition['label'] ); ?></strong>
																	<span><?php echo esc_html( $option_definition['description'] ); ?></span>
																</div>
															</label>
														<?php endforeach; ?>
													</div>
													<p class="energy-field-error energy-choice-error" id="<?php echo esc_attr( $choice_error_id ); ?>" data-energy-choice-error="<?php echo esc_attr( $field_key ); ?>"></p>
												</fieldset>
											<?php elseif ( 'text_input' === $step['kind'] && ! empty( $step['field'] ) ) : ?>
												<?php
												$field         = $step['field'];
												$field_name    = (string) $field['name'];
												$field_id      = 'energy-field-' . $field_name;
												$field_value   = $get_value( $field_name );
												$field_help_id = $field_id . '-help';
												$field_error_id = $field_id . '-error';
												?>
												<div class="energy-text-input-step">
													<p class="review-choice-help" id="<?php echo esc_attr( $field_help_id ); ?>"><?php echo esc_html( $step['description'] ); ?></p>
													<div class="review-field energy-text-input-step__field">
														<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
														<input
															id="<?php echo esc_attr( $field_id ); ?>"
															name="<?php echo esc_attr( $field_name ); ?>"
															type="<?php echo esc_attr( $field['type'] ); ?>"
															value="<?php echo esc_attr( $field_value ); ?>"
															<?php echo ! empty( $field['autocomplete'] ) ? 'autocomplete="' . esc_attr( $field['autocomplete'] ) . '"' : ''; ?>
															<?php echo ! empty( $field['inputmode'] ) ? 'inputmode="' . esc_attr( $field['inputmode'] ) . '"' : ''; ?>
															<?php echo ! empty( $field['pattern'] ) ? 'pattern="' . esc_attr( $field['pattern'] ) . '"' : ''; ?>
															<?php echo ! empty( $field['maxlength'] ) ? 'maxlength="' . esc_attr( (string) $field['maxlength'] ) . '"' : ''; ?>
															<?php echo ! empty( $field['placeholder'] ) ? 'placeholder="' . esc_attr( $field['placeholder'] ) . '"' : ''; ?>
															<?php echo ! empty( $field['required'] ) ? 'required' : ''; ?>
															aria-describedby="<?php echo esc_attr( trim( $field_help_id . ' ' . $field_error_id ) ); ?>"
														>
														<p class="energy-field-error" id="<?php echo esc_attr( $field_error_id ); ?>" data-energy-field-error="<?php echo esc_attr( $field_name ); ?>"></p>
													</div>
												</div>
											<?php elseif ( 'contact' === $step['kind'] ) : ?>
												<div class="review-field-grid energy-field-grid">
													<?php foreach ( $step['fields'] as $field ) : ?>
														<?php
														$field_name        = (string) $field['name'];
														$field_id          = 'energy-field-' . $field_name;
														$field_value       = $get_value( $field_name );
														$field_help_id     = ! empty( $field['help'] ) ? $field_id . '-help' : '';
														$field_error_id    = $field_id . '-error';
														$field_description = trim( implode( ' ', array_filter( [ $field_help_id, $field_error_id ] ) ) );
														$is_checkbox       = 'checkbox' === $field['type'];
														$is_textarea       = 'textarea' === $field['type'];
														?>
														<?php if ( $is_checkbox ) : ?>
															<div class="review-consent-card energy-consent-card">
																<label class="review-consent" for="<?php echo esc_attr( $field_id ); ?>">
																	<input
																		id="<?php echo esc_attr( $field_id ); ?>"
																		name="<?php echo esc_attr( $field_name ); ?>"
																		type="checkbox"
																		value="<?php echo esc_attr( $field['value'] ); ?>"
																		<?php checked( $field_value, $field['value'] ); ?>
																		<?php echo ! empty( $field['required'] ) ? 'required' : ''; ?>
																		aria-describedby="<?php echo esc_attr( $field_error_id ); ?>"
																	>
																	<span>
																		<?php echo esc_html( $field['label'] ); ?>
																		<a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutzerklärung</a>.
																	</span>
																</label>
																<p class="energy-field-error" id="<?php echo esc_attr( $field_error_id ); ?>" data-energy-field-error="<?php echo esc_attr( $field_name ); ?>"></p>
															</div>
														<?php else : ?>
															<div class="review-field<?php echo esc_attr( $is_textarea || 'page_url' === $field_name ? ' review-field-full' : '' ); ?>">
																<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
																<?php if ( ! empty( $field['help'] ) ) : ?>
																	<p class="review-field-help" id="<?php echo esc_attr( $field_help_id ); ?>"><?php echo esc_html( $field['help'] ); ?></p>
																<?php endif; ?>
																<?php if ( $is_textarea ) : ?>
																	<textarea
																		id="<?php echo esc_attr( $field_id ); ?>"
																		name="<?php echo esc_attr( $field_name ); ?>"
																		rows="<?php echo esc_attr( (string) ( $field['rows'] ?? 4 ) ); ?>"
																		<?php echo ! empty( $field['maxlength'] ) ? 'maxlength="' . esc_attr( (string) $field['maxlength'] ) . '"' : ''; ?>
																		<?php echo ! empty( $field['required'] ) ? 'required' : ''; ?>
																		<?php echo '' !== $field_description ? 'aria-describedby="' . esc_attr( $field_description ) . '"' : ''; ?>
																		placeholder="<?php echo esc_attr( $field['placeholder'] ?? '' ); ?>"
																	><?php echo esc_textarea( $field_value ); ?></textarea>
																<?php else : ?>
																	<input
																		id="<?php echo esc_attr( $field_id ); ?>"
																		name="<?php echo esc_attr( $field_name ); ?>"
																		type="<?php echo esc_attr( $field['type'] ); ?>"
																		value="<?php echo esc_attr( $field_value ); ?>"
																		<?php echo ! empty( $field['autocomplete'] ) ? 'autocomplete="' . esc_attr( $field['autocomplete'] ) . '"' : ''; ?>
																		<?php echo ! empty( $field['inputmode'] ) ? 'inputmode="' . esc_attr( $field['inputmode'] ) . '"' : ''; ?>
																		<?php echo ! empty( $field['required'] ) ? 'required' : ''; ?>
																		<?php echo '' !== $field_description ? 'aria-describedby="' . esc_attr( $field_description ) . '"' : ''; ?>
																		<?php echo ! empty( $field['placeholder'] ) ? 'placeholder="' . esc_attr( $field['placeholder'] ) . '"' : ''; ?>
																	>
																<?php endif; ?>
																<p class="energy-field-error" id="<?php echo esc_attr( $field_error_id ); ?>" data-energy-field-error="<?php echo esc_attr( $field_name ); ?>"></p>
															</div>
														<?php endif; ?>
													<?php endforeach; ?>
												</div>
											<?php endif; ?>
										</section>
									<?php endforeach; ?>

									<div
										id="energy-form-feedback"
										class="review-form-feedback<?php echo '' !== $form_error ? ' is-visible is-error' : ''; ?>"
										aria-live="<?php echo '' !== $form_error ? 'assertive' : 'polite'; ?>"
										aria-atomic="true"
										<?php echo '' !== $form_error ? 'role="alert"' : ''; ?>
									><?php echo esc_html( $form_error ); ?></div>

									<div class="review-actions energy-actions">
										<button type="button" class="review-prev-btn" data-energy-prev hidden>Zurück</button>
										<button type="button" class="audit-submit-btn" data-energy-next-button>Weiter</button>
										<button type="submit" class="audit-submit-btn" data-energy-submit hidden>Standortbestimmung absenden</button>
									</div>

									<p class="energy-form-meta">Ihre Antworten werden ausschließlich für die Vorbereitung der Standortbestimmung verwendet. Keine Weitergabe an Dritte, kein Newsletter, kein automatisierter Anruf.</p>
								</form>

								<div id="energy-request-success" class="review-success energy-review-success" role="status" aria-live="polite" aria-atomic="true" hidden>
									<div class="review-success-pill">Eingegangen</div>
									<h3>Ihre Standortbestimmung liegt in der Bearbeitung.</h3>
									<p id="energy-success-message" class="review-success-copy">Eingegangen. Ihre Standortbestimmung liegt in der Bearbeitung.</p>
									<p class="review-success-timeline">Sie erhalten innerhalb von <strong>48 Werktagsstunden</strong> eine E-Mail von <strong>hasim@hasimuener.de</strong>.</p>
									<ul class="review-success-list">
										<li>ehrliche Einschätzung, ob WGOS für Ihren Betrieb der richtige Hebel ist</li>
										<li>bei Eignung: Vorschlag für ein 30-minütiges Erstgespräch per Telefon oder Video</li>
										<li>bei Nicht-Eignung: konkreter Hinweis, welche Alternative für Ihre Situation realistischer ist</li>
									</ul>
									<p class="review-success-inbox-hint">Falls die E-Mail nicht ankommt, prüfen Sie bitte den Spam-Ordner oder schreiben direkt an <a href="mailto:hasim@hasimuener.de">hasim@hasimuener.de</a>.</p>
								</div>
							<?php endif; ?>
						</div>

						<aside class="energy-form-shell__aside" aria-labelledby="energy-aside-title">
							<h3 id="energy-aside-title">Ihre Angaben im Überblick</h3>
							<p>Region, Lead-Volumen, CPL und Engpass zusammen ergeben die Grundlage für eine ehrliche Einordnung &mdash; ohne Pflicht-Call und ohne generische Antwort.</p>
							<dl class="review-brief-list">
								<?php foreach ( $flow_steps as $step ) : ?>
									<?php if ( empty( $step['name'] ) || empty( $step['summary_label'] ) ) : ?>
										<?php continue; ?>
									<?php endif; ?>
									<div class="review-brief-row">
										<dt><?php echo esc_html( $step['summary_label'] ); ?></dt>
										<dd data-energy-summary="<?php echo esc_attr( $step['name'] ); ?>"><?php echo esc_html( $get_summary_value( $step['name'] ) ); ?></dd>
									</div>
								<?php endforeach; ?>
							</dl>
						</aside>
					</div>
				</div>
			</div>
		</section>
	</div>
</main>

<?php get_footer(); ?>
