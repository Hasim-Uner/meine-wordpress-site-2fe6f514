<?php
/**
 * Native contact page for /kontakt/.
 *
 * Publicly this is the direct-project intake. `request_type` remains part of
 * the payload for REST/CRM compatibility, but it is no longer an extra user
 * decision before the actual topic.
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
$response_window      = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'window' ) : 'zeitnah';
$requested_focus      = isset( $_GET['focus'] ) ? sanitize_key( wp_unslash( $_GET['focus'] ) ) : '';
$requested_type       = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : '';
$selected_type        = isset( $request_type_options[ $requested_type ] ) ? $requested_type : 'project';

// Alte ?focus=-Deep-Links bleiben funktionsfähig. Ohne expliziten Typ wird
// aus dem Fokus ein kompatibler Backend-Typ abgeleitet; sichtbar bleibt nur
// die eigentliche Themenwahl.
if ( '' === $requested_type && isset( $focus_options[ $requested_focus ] ) ) {
	$focus_types = isset( $focus_options[ $requested_focus ]['types'] ) ? (array) $focus_options[ $requested_focus ]['types'] : [];
	if ( ! in_array( $selected_type, $focus_types, true ) ) {
		foreach ( [ 'project', 'implementation', 'ongoing', 'general', 'analysis', 'audit', 'client' ] as $candidate_type ) {
			if ( isset( $request_type_options[ $candidate_type ] ) && in_array( $candidate_type, $focus_types, true ) ) {
				$selected_type = $candidate_type;
				break;
			}
		}
	}
}

$public_focus_options = array_filter(
	$focus_options,
	static function ( $focus_definition ) use ( $selected_type ) {
		$focus_types = isset( $focus_definition['types'] ) ? (array) $focus_definition['types'] : [];
		return in_array( $selected_type, $focus_types, true );
	}
);
$selected_focus = isset( $public_focus_options[ $requested_focus ] ) ? $requested_focus : '';

$hero_titles = [
	'audit'          => 'Was soll zuerst fachlich geprüft werden?',
	'analysis'       => 'Was soll auf Ihrer Website klarer werden?',
	'project'        => 'Was soll auf Ihrer Website messbar besser werden?',
	'implementation' => 'Was soll technisch sauber umgesetzt werden?',
	'ongoing'        => 'Was soll planbar besser werden?',
	'general'        => 'Worum geht es?',
	'client'         => 'Was ist der nächste sinnvolle Schritt?',
];
$submit_labels = [
	'audit'          => 'Marktcheck anfragen',
	'analysis'       => 'Website-Analyse anfragen',
	'project'        => 'Projekt anfragen',
	'implementation' => 'Umsetzung anfragen',
	'ongoing'        => 'Weiterentwicklung anfragen',
	'general'        => 'Anfrage senden',
	'client'         => 'Kundenanliegen senden',
];

$current_type_label  = isset( $request_type_options[ $selected_type ]['label'] ) ? (string) $request_type_options[ $selected_type ]['label'] : 'Projektanfrage';
$hero_title          = isset( $hero_titles[ $selected_type ] ) ? $hero_titles[ $selected_type ] : $hero_titles['project'];
$submit_label        = isset( $submit_labels[ $selected_type ] ) ? $submit_labels[ $selected_type ] : $submit_labels['project'];
$message_minlength   = 'general' === $selected_type ? 18 : 24;
$show_timeline_field = in_array( $selected_type, [ 'analysis', 'project', 'implementation', 'ongoing', 'client' ], true );
$show_budget_field   = in_array( $selected_type, [ 'implementation', 'ongoing' ], true );
$is_scoped_focus     = '' !== $selected_focus;
$visible_step_count  = 3 - ( $is_scoped_focus ? 1 : 0 );
$form_title          = $is_scoped_focus ? 'Zwei kurze Schritte. Direkte fachliche Einordnung.' : 'Drei kurze Schritte. Klare Anfrage statt langem Briefing.';
$form_intro          = $is_scoped_focus ? 'Seite, Engpass, Ziel – mehr brauche ich für den ersten fachlichen Check nicht.' : 'Nur die Angaben, die ich für eine erste fachliche Einordnung wirklich brauche.';
$message_step_title  = $is_scoped_focus ? 'Wo liegt der Engpass?' : 'Was soll messbar besser werden?';
$page_classes        = 'site-main doku contact-page' . ( $is_scoped_focus ? ' contact-page--scoped' : '' );
?>

<main id="main" class="<?php echo esc_attr( $page_classes ); ?>" data-track-section="contact_page">
	<div class="contact-page__shell">
		<aside class="contact-intro" aria-labelledby="contact-title">
			<p class="contact-eyebrow"><?php echo esc_html( $current_type_label ); ?></p>
			<h1 id="contact-title" class="contact-title"><?php echo esc_html( $hero_title ); ?></h1>
			<p class="contact-lead">WordPress, Tracking, Conversion oder technisches SEO: kurz einordnen, Engpass benennen, direkt bei mir landen.</p>

			<div class="contact-intro__facts" aria-label="Ablauf">
				<p><span>01</span><strong>Einordnen</strong> Kontext statt langem Briefing-Fragebogen.</p>
				<p><span>02</span><strong>Prüfen</strong> Ich lese jede Anfrage selbst.</p>
				<p><span>03</span><strong>Antwort</strong> Persönlich <?php echo esc_html( $response_window ); ?>.</p>
			</div>

			<?php if ( ! $is_scoped_focus ) : ?>
				<nav class="contact-route-list" aria-label="Andere Einstiege">
					<p class="contact-route-list__label">Andere Einstiege</p>
					<a href="<?php echo esc_url( $agency_url ); ?>" data-track-action="contact_route_agency" data-track-category="contact" data-track-section="contact_routes">
						<span>Für Agenturen</span><strong>White-Label-Aufgabe beschreiben</strong>
					</a>
					<a href="<?php echo esc_url( $energy_url ); ?>" data-track-action="contact_route_energy" data-track-category="contact" data-track-section="contact_routes">
						<span>Solar &amp; Wärmepumpe</span><strong>Zum Marktcheck</strong>
					</a>
				</nav>
			<?php endif; ?>

			<a class="contact-direct-mail" href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a>
		</aside>

		<section class="contact-form-panel" id="kontakt-form" aria-labelledby="contact-form-title">
			<header class="contact-section-head">
				<div class="contact-section-head__meta">
					<p class="contact-section-head__eyebrow">Projektbriefing</p>
					<p class="contact-section-head__trust"><span aria-hidden="true">●</span> Persönlich geprüft · Antwort <?php echo esc_html( $response_window ); ?></p>
				</div>
				<h2 id="contact-form-title"><?php echo esc_html( $form_title ); ?></h2>
				<p><?php echo esc_html( $form_intro ); ?></p>
			</header>

			<div class="contact-error-summary is-hidden" role="alert" aria-live="assertive" data-contact-error-summary tabindex="-1">
				<p class="contact-error-summary__title">Bitte prüfen Sie folgende Angaben:</p>
				<ul class="contact-error-summary__list" data-contact-error-list></ul>
			</div>

			<form class="contact-form contact-form--superflow" data-contact-form action="<?php echo esc_url( $rest_endpoint ); ?>" method="post" novalidate>
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
				<input type="radio" name="request_type" value="<?php echo esc_attr( $selected_type ); ?>" data-contact-type-input checked required hidden>

				<div class="contact-flow-progress" aria-label="Fortschritt">
					<div>
						<span data-contact-step-label><?php echo esc_html( sprintf( 'Schritt 1 von %d', $visible_step_count ) ); ?></span>
						<strong data-contact-progress-value><?php echo esc_html( (string) (int) round( 100 / max( 1, $visible_step_count ) ) . '%' ); ?></strong>
					</div>
					<div class="contact-flow-progress__bar" aria-hidden="true"><span data-contact-progress-fill></span></div>
				</div>

				<div class="contact-flow-stage">
					<section class="contact-flow-step" data-contact-step="focus" data-contact-step-label="Thema" <?php echo $is_scoped_focus ? 'data-contact-step-skip="true"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static boolean attribute ?>>
						<div class="contact-step-head"><span>01</span><p>Worum geht es?</p></div>
						<div class="contact-field" data-contact-field="focus">
							<label for="contact-focus" data-contact-focus-label>Welcher Bereich soll zuerst geprüft werden?</label>
							<p id="contact-focus-help" class="contact-field__help" data-contact-focus-help>Wählen Sie den Bereich, der Ihrem Anliegen am nächsten kommt.</p>
							<select id="contact-focus" name="focus" required data-contact-focus-select aria-describedby="contact-focus-help contact-focus-error">
								<option value="" <?php selected( '', $selected_focus ); ?> disabled>Bitte auswählen</option>
								<?php foreach ( $public_focus_options as $focus_key => $focus_definition ) : ?>
									<option value="<?php echo esc_attr( $focus_key ); ?>" data-types="<?php echo esc_attr( implode( ',', array_map( 'sanitize_key', (array) $focus_definition['types'] ) ) ); ?>" <?php selected( $selected_focus, $focus_key ); ?>><?php echo esc_html( $focus_definition['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
							<p class="contact-field__error is-hidden" id="contact-focus-error" aria-live="polite"></p>
						</div>
					</section>

					<section class="contact-flow-step" data-contact-step="message" data-contact-step-label="Ausgangslage">
						<div class="contact-step-head"><span><?php echo esc_html( $is_scoped_focus ? '01' : '02' ); ?></span><p><?php echo esc_html( $message_step_title ); ?></p></div>
						<div class="contact-field" data-contact-field="message">
							<label for="contact-message" data-contact-message-label>Ausgangslage und Ziel</label>
							<p id="contact-message-help" class="contact-field__help" data-contact-message-help>Nennen Sie Seite, Angebot und Engpass. Das reicht für eine erste fachliche Einordnung.</p>
							<textarea id="contact-message" name="message" rows="6" required minlength="<?php echo esc_attr( (string) $message_minlength ); ?>" aria-describedby="contact-message-help contact-message-error" data-contact-message></textarea>
							<p class="contact-field__error is-hidden" id="contact-message-error" aria-live="polite"></p>
						</div>

						<div class="contact-brief-meta">
							<div class="contact-field">
								<label for="contact-website">Website <span>optional</span></label>
								<input id="contact-website" name="website_url" type="url" autocomplete="url" inputmode="url" placeholder="https://example.de">
							</div>
							<div class="contact-field<?php echo esc_attr( $show_timeline_field ? '' : ' is-hidden' ); ?>" data-contact-context-field="timeline">
								<label for="contact-timeline" data-contact-timeline-label>Zeitfenster <span>optional</span></label>
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
						<div class="contact-step-head"><span><?php echo esc_html( $is_scoped_focus ? '02' : '03' ); ?></span><p>Wie erreiche ich Sie?</p></div>
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
							<summary class="contact-optional__toggle"><span>Mehr Kontext <small>optional</small></span><span aria-hidden="true">+</span></summary>
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
							<span>Ich stimme zu, dass meine Angaben zur Bearbeitung meiner Anfrage verarbeitet werden. Mehr dazu in der <a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutzerklärung</a>.</span>
							<p class="contact-field__error is-hidden" id="contact-consent-error" aria-live="polite"></p>
						</label>
					</section>
				</div>

				<div class="contact-form__actions contact-form__actions--flow">
					<div class="contact-form__assurance" aria-label="Bearbeitung">
						<strong>Direkt bei mir.</strong>
						<span>Kein Vertriebsteam, keine Sales-Schleife.</span>
					</div>
					<div class="contact-form__button-row">
						<button class="contact-btn contact-btn--ghost" type="button" data-contact-prev hidden>Zurück</button>
						<button class="contact-btn contact-btn--primary" type="button" data-contact-next hidden>Weiter</button>
						<button class="contact-submit" type="submit" data-contact-submit data-contact-submit-label="<?php echo esc_attr( $submit_label ); ?>" data-track-action="contact_submit" data-track-category="contact" data-track-section="contact_superflow"><?php echo esc_html( $submit_label ); ?></button>
					</div>
					<p class="contact-form__promise">Sie bekommen <?php echo esc_html( $response_window ); ?> eine Antwort von mir persönlich. Keine automatische Mailserie.</p>
					<a class="contact-form__aux-link" href="<?php echo esc_url( $calendar_url ); ?>" data-track-action="cta_click_contact_call_superflow" data-track-category="contact" data-track-section="contact_superflow">Lieber direkt Termin buchen</a>
				</div>

				<div class="contact-form__feedback" data-contact-feedback aria-live="polite" role="status"></div>
			</form>
		</section>
	</div>
</main>

<?php get_footer(); ?>
