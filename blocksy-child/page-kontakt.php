<?php
/**
 * Native contact page for the canonical /kontakt/ path.
 *
 * The public surface is a direct-project intake. Request type stays in the
 * payload for REST/CRM compatibility, but visitors start with the actual
 * project topic instead of choosing an internal taxonomy first.
 *
 * @package Blocksy_Child
 */

get_header();

$privacy_url          = function_exists( 'nexus_get_page_url' ) ? nexus_get_page_url( [ 'datenschutz' ], home_url( '/datenschutz/' ) ) : home_url( '/datenschutz/' );
$rest_endpoint        = rest_url( 'nexus/v1/contact-request' );
$request_type_options = function_exists( 'nexus_get_contact_request_type_options' ) ? nexus_get_contact_request_type_options() : [];
$focus_options        = function_exists( 'nexus_get_contact_focus_options' ) ? nexus_get_contact_focus_options() : [];
$budget_options       = function_exists( 'nexus_get_contact_budget_options' ) ? nexus_get_contact_budget_options() : [];
$timeline_options     = function_exists( 'nexus_get_contact_timeline_options' ) ? nexus_get_contact_timeline_options() : [];
$calendar_url         = function_exists( 'nexus_get_audit_calendar_url' ) ? nexus_get_audit_calendar_url() : home_url( '/kontakt/' );
$agency_url           = home_url( '/whitelabel-retainer/' ) . '#aufgabe';
$energy_url           = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$contact_email        = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';
$response_window      = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'window' ) : '';
$response_sentence    = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'sentence' ) : 'Ich antworte persönlich per E-Mail.';
$requested_focus      = isset( $_GET['focus'] ) ? sanitize_key( wp_unslash( $_GET['focus'] ) ) : '';
$requested_type       = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : '';

// /kontakt/ ist der generische Direkteinstieg. Spezielle Typen bleiben für
// bestehende Deep-Links verfügbar, werden aber nicht als zusätzliche Frage
// im Formular exponiert.
$public_type_keys = [ 'project', 'implementation', 'ongoing', 'general' ];
if ( in_array( $requested_type, [ 'analysis', 'audit', 'client' ], true ) ) {
	$public_type_keys[] = $requested_type;
}
$public_type_keys    = array_values( array_unique( $public_type_keys ) );
$public_type_options = array_intersect_key( $request_type_options, array_flip( $public_type_keys ) );

$selected_type = isset( $public_type_options[ $requested_type ] ) ? $requested_type : 'project';

// Ein bestehender ?focus=-Deep-Link darf nicht an einem neuen project-Default
// zerbrechen. Ohne expliziten type wird aus dem Focus ein kompatibler Typ
// abgeleitet; mit explizitem type bleibt dieser autoritativ.
if ( '' === $requested_type && isset( $focus_options[ $requested_focus ] ) ) {
	$focus_types = isset( $focus_options[ $requested_focus ]['types'] ) ? (array) $focus_options[ $requested_focus ]['types'] : [];
	if ( ! in_array( $selected_type, $focus_types, true ) ) {
		foreach ( [ 'project', 'implementation', 'ongoing', 'general', 'analysis', 'audit', 'client' ] as $candidate_type ) {
			if ( isset( $public_type_options[ $candidate_type ] ) && in_array( $candidate_type, $focus_types, true ) ) {
				$selected_type = $candidate_type;
				break;
			}
		}
	}
}

$type_focus_options = array_filter(
	$focus_options,
	static function ( $focus_definition ) use ( $selected_type ) {
		$focus_types = isset( $focus_definition['types'] ) ? (array) $focus_definition['types'] : [];
		return in_array( $selected_type, $focus_types, true );
	}
);

$selected_focus = '';
if ( isset( $type_focus_options[ $requested_focus ] ) ) {
	$selected_focus = $requested_focus;
}

$type_copy_map = [
	'audit'          => [
		'label'               => 'Marktcheck',
		'hero_title'          => 'Was soll zuerst geprüft werden?',
		'hero_lead'           => 'Kurz einordnen, Ausgangslage beschreiben und den nächsten sinnvollen Schritt klären.',
		'focus_label'         => 'Was soll zuerst diagnostiziert werden?',
		'focus_help'          => 'Wählen Sie die Fläche, auf der aktuell die größte Unklarheit liegt.',
		'message_label'       => 'Ausgangslage und Ziel',
		'message_help'        => 'Welche URL ist relevant? Was bremst gerade? Welches Ergebnis wünschen Sie sich?',
		'message_placeholder' => "1. Seite: Welche URL ist relevant?\n2. Unklarheit: Was bremst gerade?\n3. Ziel: Was soll sich verbessern?",
		'submit_label'        => 'Marktcheck anfragen',
		'timeline_label'      => 'Zeitfenster',
	],
	'analysis'       => [
		'label'               => 'Website-Analyse',
		'hero_title'          => 'Was soll an Ihrer Website geprüft werden?',
		'hero_lead'           => 'Die Ausgangslage knapp einordnen, damit Analyse und nächster Schritt nicht bei null beginnen.',
		'focus_label'         => 'Was soll an der Website analysiert werden?',
		'focus_help'          => 'Wählen Sie den Bereich, in dem aktuell die größte Unklarheit liegt.',
		'message_label'       => 'Ausgangslage und Ziel',
		'message_help'        => 'Welche URL ist relevant? Was bremst gerade? Welche Entscheidung soll die Analyse erleichtern?',
		'message_placeholder' => "1. Seite: Welche URL ist relevant?\n2. Hürde: Was bremst gerade?\n3. Ziel: Welche Entscheidung soll danach leichter werden?",
		'submit_label'        => 'Website-Analyse anfragen',
		'timeline_label'      => 'Zeitfenster',
	],
	'project'        => [
		'label'               => 'Projektanfrage',
		'hero_title'          => 'Was soll auf Ihrer Website besser funktionieren?',
		'hero_lead'           => 'WordPress, Tracking, Conversion oder technisches SEO: Thema wählen, Ziel beschreiben und direkt bei mir landen.',
		'focus_label'         => 'Welcher Bereich soll zuerst geprüft werden?',
		'focus_help'          => 'Wählen Sie den Bereich, in dem aktuell die größte geschäftliche Unklarheit liegt.',
		'message_label'       => 'Ausgangslage und Ziel',
		'message_help'        => 'Welche Website ist betroffen, was soll besser funktionieren und woran würden Sie ein gutes Ergebnis erkennen?',
		'message_placeholder' => "1. Website: Welche URL ist relevant?\n2. Vorhaben: Was soll entstehen oder besser werden?\n3. Ziel: Woran erkennen Sie ein gutes Ergebnis?",
		'submit_label'        => 'Projekt anfragen',
		'timeline_label'      => 'Zeitfenster',
	],
	'implementation' => [
		'label'               => 'Umsetzung / Optimierung',
		'hero_title'          => 'Was soll konkret umgesetzt oder korrigiert werden?',
		'hero_lead'           => 'Ein klar umrissenes technisches oder Conversion-Thema direkt einordnen und den Scope klären.',
		'focus_label'         => 'Was soll umgesetzt oder korrigiert werden?',
		'focus_help'          => 'Wählen Sie den Hebel, der Ihrem Umsetzungsbedarf am nächsten kommt.',
		'message_label'       => 'Ausgangslage und Ziel',
		'message_help'        => 'Was ist das Ziel, was steht aktuell im Weg und welches Ergebnis wünschen Sie sich?',
		'message_placeholder' => "1. Ziel: Was soll erreicht werden?\n2. Hürde: Was steht aktuell im Weg?\n3. Ergebnis: Was soll sich konkret verbessern?",
		'submit_label'        => 'Umsetzung anfragen',
		'timeline_label'      => 'Zeitfenster',
	],
	'ongoing'        => [
		'label'               => 'Weiterentwicklung',
		'hero_title'          => 'Was soll planbar weiterentwickelt werden?',
		'hero_lead'           => 'Bestehendes Setup, Engpass und nächste Priorität kurz einordnen.',
		'focus_label'         => 'Was soll laufend weiterentwickelt werden?',
		'focus_help'          => 'Wählen Sie den Bereich, der dauerhaft sauber betreut oder weiterentwickelt werden soll.',
		'message_label'       => 'Ausgangslage und Ziel',
		'message_help'        => 'Was läuft bereits, was blockiert und was soll planbar besser werden?',
		'message_placeholder' => "1. System: Was läuft bereits?\n2. Engpass: Was blockiert oder kostet Wirkung?\n3. Weiterentwicklung: Was soll planbar besser werden?",
		'submit_label'        => 'Weiterentwicklung anfragen',
		'timeline_label'      => 'Zeitfenster',
	],
	'general'        => [
		'label'               => 'Allgemeine Anfrage',
		'hero_title'          => 'Worum geht es?',
		'hero_lead'           => 'Frage, Kooperation oder kurzes Anliegen ohne festen Projektrahmen.',
		'focus_label'         => 'Worum geht es?',
		'focus_help'          => 'Wählen Sie den Bereich, damit Ihre Nachricht direkt passend eingeordnet werden kann.',
		'message_label'       => 'Ihre Nachricht',
		'message_help'        => 'Schildern Sie kurz Anlass und gewünschte Rückmeldung.',
		'message_placeholder' => 'Worum geht es und welche Rückmeldung wäre hilfreich?',
		'submit_label'        => 'Anfrage senden',
		'timeline_label'      => 'Zeitfenster',
	],
	'client'         => [
		'label'               => 'Bestehendes Projekt',
		'hero_title'          => 'Was ist der nächste Schritt im laufenden Projekt?',
		'hero_lead'           => 'Status, Blocker oder nächste Priorität kurz beschreiben.',
		'focus_label'         => 'Wobei kann ich unterstützen?',
		'focus_help'          => 'Wählen Sie den Bereich, damit Priorisierung und Rückmeldung direkt anschließen können.',
		'message_label'       => 'Status und nächster Schritt',
		'message_help'        => 'Beschreiben Sie kurz Status, Blocker oder die nächste Entscheidung.',
		'message_placeholder' => 'Worum geht es gerade, was blockiert und was soll als Nächstes entschieden werden?',
		'submit_label'        => 'Kundenanliegen senden',
		'timeline_label'      => 'Dringlichkeit',
	],
];

$current_type_copy   = isset( $type_copy_map[ $selected_type ] ) ? $type_copy_map[ $selected_type ] : $type_copy_map['project'];
$current_type_label  = $current_type_copy['label'];
$focus_label         = $current_type_copy['focus_label'];
$focus_help          = $current_type_copy['focus_help'];
$message_label       = $current_type_copy['message_label'];
$message_help        = $current_type_copy['message_help'];
$message_placeholder = $current_type_copy['message_placeholder'];
$submit_label        = $current_type_copy['submit_label'];
$timeline_label      = $current_type_copy['timeline_label'];
$message_minlength   = 'general' === $selected_type ? 18 : 24;
$show_timeline_field = in_array( $selected_type, [ 'analysis', 'project', 'implementation', 'ongoing', 'client' ], true );
$show_budget_field   = in_array( $selected_type, [ 'implementation', 'ongoing' ], true );
$is_scoped_focus     = '' !== $selected_focus;
$visible_step_count  = 3 - ( $is_scoped_focus ? 1 : 0 );
?>

<main id="main" class="site-main doku contact-page" data-track-section="contact_page">
	<div class="contact-page__shell">
		<aside class="contact-intro" aria-labelledby="contact-title">
			<p class="contact-eyebrow"><?php echo esc_html( $current_type_label ); ?></p>
			<h1 id="contact-title" class="contact-title"><?php echo esc_html( $current_type_copy['hero_title'] ); ?></h1>
			<p class="contact-lead"><?php echo esc_html( $current_type_copy['hero_lead'] ); ?></p>

			<div class="contact-intro__facts" aria-label="Ablauf">
				<p><span>01</span><strong>Einordnen</strong> Thema und Ausgangslage statt langer Briefing-Fragebogen.</p>
				<p><span>02</span><strong>Prüfen</strong> Ich lese jede Anfrage selbst.</p>
				<p><span>03</span><strong>Antwort</strong> <?php echo esc_html( $response_sentence ); ?></p>
			</div>

			<nav class="contact-route-list" aria-label="Andere Einstiege">
				<p class="contact-route-list__label">Andere Einstiege</p>
				<a href="<?php echo esc_url( $agency_url ); ?>" data-track-action="contact_route_agency" data-track-category="contact" data-track-section="contact_routes">
					<span>Für Agenturen</span>
					<strong>White-Label-Aufgabe beschreiben</strong>
				</a>
				<a href="<?php echo esc_url( $energy_url ); ?>" data-track-action="contact_route_energy" data-track-category="contact" data-track-section="contact_routes">
					<span>Solar &amp; Wärmepumpe</span>
					<strong>Zum Marktcheck</strong>
				</a>
			</nav>

			<a class="contact-direct-mail" href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a>
		</aside>

		<section class="contact-form-panel" id="kontakt-form" aria-labelledby="contact-form-title">
			<header class="contact-section-head">
				<p class="contact-section-head__eyebrow">Projektbriefing</p>
				<h2 id="contact-form-title">Drei kurze Schritte. Keine Sales-Schleife.</h2>
				<p>Nur die Angaben, die ich für eine erste fachliche Einordnung wirklich brauche.</p>
			</header>

			<div class="contact-error-summary is-hidden" role="alert" aria-live="assertive" data-contact-error-summary tabindex="-1">
				<p class="contact-error-summary__title">Bitte prüfen Sie folgende Angaben:</p>
				<ul class="contact-error-summary__list" data-contact-error-list></ul>
			</div>

			<form
				class="contact-form contact-form--superflow"
				data-contact-form
				action="<?php echo esc_url( $rest_endpoint ); ?>"
				method="post"
				novalidate
			>
				<div class="contact-form__honeypot" aria-hidden="true">
					<label for="contact-company-website">Website</label>
					<input id="contact-company-website" type="text" name="company_website" tabindex="-1" autocomplete="off">
				</div>

				<input type="hidden" name="ads_source" id="ads_source" value="">
				<input type="hidden" name="ads_keyword" id="ads_keyword" value="">
				<input type="hidden" name="utm_medium" id="utm_medium" value="">
				<input type="hidden" name="utm_campaign" id="utm_campaign" value="">
				<input type="hidden" name="gclid" id="gclid" value="">
				<input type="hidden" name="matchtype" id="matchtype" value="">

				<input
					type="radio"
					name="request_type"
					value="<?php echo esc_attr( $selected_type ); ?>"
					data-contact-type-input
					checked
					required
					hidden
				>

				<div class="contact-flow-progress" aria-label="Fortschritt">
					<div>
						<span data-contact-step-label><?php echo esc_html( sprintf( 'Schritt 1 von %d', $visible_step_count ) ); ?></span>
						<strong data-contact-progress-value><?php echo esc_html( (string) (int) round( 100 / max( 1, $visible_step_count ) ) . '%' ); ?></strong>
					</div>
					<div class="contact-flow-progress__bar" aria-hidden="true"><span data-contact-progress-fill></span></div>
				</div>

				<div class="contact-flow-stage">
					<section
						class="contact-flow-step"
						data-contact-step="focus"
						data-contact-step-label="Thema"
						<?php echo $is_scoped_focus ? 'data-contact-step-skip="true"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static boolean attribute ?>
					>
						<div class="contact-step-head">
							<span>01</span>
							<p>Worum geht es?</p>
						</div>
						<div class="contact-field" data-contact-field="focus">
							<label for="contact-focus" data-contact-focus-label><?php echo esc_html( $focus_label ); ?></label>
							<p id="contact-focus-help" class="contact-field__help" data-contact-focus-help><?php echo esc_html( $focus_help ); ?></p>
							<select id="contact-focus" name="focus" required data-contact-focus-select aria-describedby="contact-focus-help contact-focus-error">
								<option value="" <?php selected( '', $selected_focus ); ?> disabled>Bitte auswählen</option>
								<?php foreach ( $type_focus_options as $focus_key => $focus_definition ) : ?>
									<option
										value="<?php echo esc_attr( $focus_key ); ?>"
										data-types="<?php echo esc_attr( implode( ',', array_map( 'sanitize_key', (array) $focus_definition['types'] ) ) ); ?>"
										<?php selected( $selected_focus, $focus_key ); ?>
									><?php echo esc_html( $focus_definition['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
							<p class="contact-field__error is-hidden" id="contact-focus-error" aria-live="polite"></p>
						</div>
					</section>

					<section class="contact-flow-step" data-contact-step="message" data-contact-step-label="Ausgangslage">
						<div class="contact-step-head">
							<span><?php echo esc_html( $is_scoped_focus ? '01' : '02' ); ?></span>
							<p>Was soll besser werden?</p>
						</div>
						<div class="contact-field" data-contact-field="message">
							<label for="contact-message" data-contact-message-label><?php echo esc_html( $message_label ); ?></label>
							<p id="contact-message-help" class="contact-field__help" data-contact-message-help><?php echo esc_html( $message_help ); ?></p>
							<textarea
								id="contact-message"
								name="message"
								rows="6"
								required
								minlength="<?php echo esc_attr( (string) $message_minlength ); ?>"
								aria-describedby="contact-message-help contact-message-error"
								placeholder="<?php echo esc_attr( $message_placeholder ); ?>"
								data-contact-message
								data-contact-message-placeholder="<?php echo esc_attr( $message_placeholder ); ?>"
							></textarea>
							<p class="contact-field__error is-hidden" id="contact-message-error" aria-live="polite"></p>
						</div>

						<div class="contact-brief-meta">
							<div class="contact-field">
								<label for="contact-website">Website <span>optional</span></label>
								<input id="contact-website" name="website_url" type="url" autocomplete="url" inputmode="url" placeholder="https://example.de">
							</div>

							<div class="contact-field<?php echo esc_attr( $show_timeline_field ? '' : ' is-hidden' ); ?>" data-contact-context-field="timeline">
								<label for="contact-timeline" data-contact-timeline-label><?php echo esc_html( $timeline_label ); ?> <span>optional</span></label>
								<select id="contact-timeline" name="timeline" data-contact-timeline-select>
									<option value="" selected>Optional auswählen</option>
									<?php foreach ( $timeline_options as $timeline_key => $timeline_option_label ) : ?>
										<option value="<?php echo esc_attr( $timeline_key ); ?>"><?php echo esc_html( $timeline_option_label ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</section>

					<section class="contact-flow-step" data-contact-step="identity" data-contact-step-label="Kontakt">
						<div class="contact-step-head">
							<span><?php echo esc_html( $is_scoped_focus ? '02' : '03' ); ?></span>
							<p>Wie erreiche ich Sie?</p>
						</div>
						<div class="contact-form__row">
							<div class="contact-field" data-contact-field="name">
								<label for="contact-name">Name</label>
								<input id="contact-name" name="name" type="text" autocomplete="name" required aria-describedby="contact-name-error">
								<p class="contact-field__error is-hidden" id="contact-name-error" aria-live="polite"></p>
							</div>
							<div class="contact-field" data-contact-field="email">
								<label for="contact-email">E-Mail</label>
								<input id="contact-email" name="email" type="email" autocomplete="email" required aria-describedby="contact-email-error">
								<p class="contact-field__error is-hidden" id="contact-email-error" aria-live="polite"></p>
							</div>
						</div>

						<details class="contact-optional" data-contact-optional>
							<summary class="contact-optional__toggle">
								<span>Mehr Kontext <small>optional</small></span>
								<span aria-hidden="true">+</span>
							</summary>
							<div class="contact-optional__body">
								<div class="contact-field">
									<label for="contact-linkedin">LinkedIn <span>optional</span></label>
									<input id="contact-linkedin" name="linkedin_url" type="url" autocomplete="url" inputmode="url" placeholder="https://linkedin.com/in/…">
								</div>
								<div class="contact-field<?php echo esc_attr( $show_budget_field ? '' : ' is-hidden' ); ?>" data-contact-context-field="budget">
									<label for="contact-budget">Budget <span>optional</span></label>
									<select id="contact-budget" name="budget">
										<option value="" selected>Optional auswählen</option>
										<?php foreach ( $budget_options as $budget_key => $budget_label ) : ?>
											<option value="<?php echo esc_attr( $budget_key ); ?>"><?php echo esc_html( $budget_label ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</details>

						<label class="contact-consent" data-contact-field="consent">
							<input type="checkbox" name="consent" value="1" required aria-describedby="contact-consent-error">
							<span>
								Ich stimme zu, dass meine Angaben zur Bearbeitung meiner Anfrage verarbeitet werden.
								Mehr dazu in der <a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutzerklärung</a>.
							</span>
							<p class="contact-field__error is-hidden" id="contact-consent-error" aria-live="polite"></p>
						</label>
					</section>
				</div>

				<div class="contact-form__actions contact-form__actions--flow">
					<button class="contact-btn contact-btn--ghost" type="button" data-contact-prev hidden>Zurück</button>
					<button class="contact-btn contact-btn--primary" type="button" data-contact-next hidden>Weiter</button>
					<button
						class="contact-submit"
						type="submit"
						data-contact-submit
						data-contact-submit-label="<?php echo esc_attr( $submit_label ); ?>"
						data-track-action="contact_submit"
						data-track-category="contact"
						data-track-section="contact_superflow"
					><?php echo esc_html( $submit_label ); ?></button>
					<a class="contact-form__aux-link" href="<?php echo esc_url( $calendar_url ); ?>" data-track-action="cta_click_contact_call_superflow" data-track-category="contact" data-track-section="contact_superflow">Direkt Termin buchen</a>
				</div>

				<div class="contact-form__feedback" data-contact-feedback aria-live="polite" role="status"></div>
			</form>

			<footer class="contact-form__postcopy">
				<span>Persönlich geprüft</span>
				<p><?php echo esc_html( $response_sentence ); ?> Kein Vertriebsteam.</p>
			</footer>
		</section>
	</div>
</main>

<?php get_footer(); ?>
