<?php
/**
 * Versioned growth audit landing page shell.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$request_url     = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$privacy_url     = nexus_get_page_url( [ 'datenschutz' ], home_url( '/datenschutz/' ) );
$audit_cta_label = 'Marktcheck starten';
$e3_summary      = function_exists( 'hu_e3_summary' ) ? hu_e3_summary( 'proof' ) : 'E3 New Energy: eigene Anfragen, Abschlussquote und niedrigere Kosten pro Anfrage.';
?>

<div class="audit-wrapper" id="audit-main-wrapper">
	<div class="audit-container">
		<main class="audit-content">
			<section id="start" class="audit-section ga-hero nx-reveal" aria-labelledby="ga-hero-title">
				<div class="ga-hero__inner">
					<p class="ga-kicker">Marktcheck für Solar- und Wärmepumpen-Anbieter</p>
					<h1 id="ga-hero-title">Wo Ihre Seite gerade Anfragen ausbremst.</h1>
					<p class="ga-hero__sub">
						Ich prüfe Ihre Startseite oder Angebotsseite manuell und KI-unterstützt.
						Sie erhalten in 48 Stunden die 3 stärksten Bremsen, die wichtigste Priorität
						und einen klaren nächsten Schritt.
					</p>

						<div class="ga-trust-badges" aria-label="Marktcheck im Überblick">
						<span>0 € Einstieg</span>
						<span>manuell geprüft</span>
						<span>Rückmeldung in 48h</span>
					</div>

					<div class="ga-hero__actions">
						<a class="ga-btn ga-btn--primary" href="#form" data-track-action="cta_hero_growth_audit" data-track-category="lead_gen" data-track-section="hero">
							<?php echo esc_html( $audit_cta_label ); ?>
						</a>
					</div>

					<p class="ga-hero__microcopy">Eine konkrete Seite reicht. Kein Pflicht-Call.</p>
				</div>
			</section>

			<section id="proof" class="audit-section ga-proof nx-reveal" aria-labelledby="ga-proof-title">
				<div class="ga-card ga-proof-card">
					<div class="ga-section-head ga-section-head--compact">
						<p class="ga-section-kicker">Proof</p>
						<h2 id="ga-proof-title">Ein öffentlicher Kontext.</h2>
					</div>

					<p class="ga-proof__lead">
						<?php echo esc_html( $e3_summary ); ?>
					</p>

					<div class="ga-proof__foot">
						<a class="ga-text-link" href="<?php echo esc_url( $request_url ); ?>">Anfrage stellen &rarr;</a>
						<p class="ga-proof__note">Persönlich geprüft. Keine automatisierte Standardauswertung.</p>
					</div>
				</div>
			</section>

			<section id="form" class="audit-section ga-form-section nx-reveal" aria-labelledby="ga-form-title">
				<div class="ga-form-shell">
					<div class="ga-form-intro">
						<div class="ga-section-head ga-section-head--compact">
							<p class="ga-section-kicker">Marktcheck starten</p>
							<h2 id="ga-form-title">Seite einreichen.</h2>
							<p>Startseite oder Angebotsseite genügt. Ich melde mich schriftlich zurück.</p>
						</div>
					</div>

					<div class="ga-card ga-form-card">
						<form id="ga-request-form" class="ga-form" novalidate>
							<div class="ga-form__hp" aria-hidden="true" tabindex="-1">
								<label for="ga-company-website">Website</label>
								<input id="ga-company-website" name="company_website" type="text" autocomplete="off" tabindex="-1">
							</div>

							<input type="hidden" name="audit_type" value="growth_audit">
							<input type="hidden" name="intake_variant" value="growth_audit_simple">
							<input type="hidden" name="ads_source" id="ga-ads-source" value="">
							<input type="hidden" name="ads_keyword" id="ga-ads-keyword" value="">

							<div class="ga-form__field">
								<label for="ga-page-url">Seiten-URL <span class="ga-form__req" aria-label="Pflichtfeld">*</span></label>
								<input id="ga-page-url" name="page_url" type="url" required autocomplete="url" inputmode="url" autocapitalize="off" spellcheck="false" placeholder="https://www.beispiel.de/angebot">
								<span class="ga-form__error" role="alert" aria-live="polite"></span>
							</div>

							<div class="ga-form__field ga-form__field--half">
								<label for="ga-name">Name <span class="ga-form__req" aria-label="Pflichtfeld">*</span></label>
								<input id="ga-name" name="name" type="text" required autocomplete="name" placeholder="Ihr Name">
								<span class="ga-form__error" role="alert" aria-live="polite"></span>
							</div>

							<div class="ga-form__field ga-form__field--half">
								<label for="ga-email">Geschäftliche E-Mail <span class="ga-form__req" aria-label="Pflichtfeld">*</span></label>
								<input id="ga-email" name="email" type="email" required autocomplete="email" inputmode="email" autocapitalize="off" spellcheck="false" placeholder="name@unternehmen.de">
								<span class="ga-form__error" role="alert" aria-live="polite"></span>
							</div>

							<div class="ga-form__field">
								<label for="ga-current-challenge">Was sollte ich nicht übersehen? <span class="ga-form__opt">(optional)</span></label>
								<textarea id="ga-current-challenge" name="current_challenge" rows="3" placeholder="Zum Beispiel: Kampagne läuft bereits, neue Angebotsseite ist live oder die Seite soll in einen Relaunch einfließen."></textarea>
								<span class="ga-form__error" role="alert" aria-live="polite"></span>
							</div>

							<div class="ga-form__field ga-form__field--consent">
								<label class="ga-form__consent">
									<input id="ga-consent-privacy" name="consent_privacy" type="checkbox" value="accepted" required>
									<span>
										Ich stimme zu, dass meine Angaben zur Bearbeitung des Marktchecks verarbeitet werden.
										Details stehen in der <a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutzerklärung</a>.
									</span>
								</label>
								<span class="ga-form__error" role="alert" aria-live="polite"></span>
							</div>

							<div id="ga-form-feedback" class="ga-form__feedback" aria-live="polite" aria-atomic="true"></div>

							<div class="ga-form__actions">
								<button type="submit" class="ga-btn ga-btn--primary ga-btn--full" data-track-action="cta_form_growth_audit" data-track-category="lead_gen" data-track-section="form">
									<span class="ga-btn__label">Marktcheck starten</span>
									<span class="ga-btn__spinner" aria-hidden="true"></span>
								</button>
								<p class="ga-form__microcopy">Schriftliche Rückmeldung in 48h. Kein Pflicht-Call.</p>
							</div>
						</form>

						<div id="ga-request-success" class="ga-success" role="status" aria-live="polite" aria-atomic="true" hidden>
							<div class="ga-success__pill">Anfrage eingegangen.</div>
							<h3>Ich prüfe die Seite.</h3>
							<p id="ga-success-message">Die Rückmeldung kommt innerhalb von 48 Stunden per E-Mail.</p>
							<div class="ga-success__next">
								<p class="ga-success__next-label">In der Zwischenzeit:</p>
								<a href="<?php echo esc_url( $request_url ); ?>" class="ga-success__link" data-track-action="cta_audit_success_request" data-track-category="lead_gen">Anfrage stellen</a>
							</div>
						</div>
					</div>
				</div>
			</section>

			<section id="faq" class="audit-section audit-faq-section ga-faq nx-reveal" aria-labelledby="ga-faq-title">
				<div class="ga-section-head ga-section-head--compact">
					<p class="ga-section-kicker">FAQ</p>
					<h2 id="ga-faq-title" class="audit-faq-headline">Häufige Fragen</h2>
				</div>

				<details>
					<summary>Was genau bekomme ich innerhalb von 48 Stunden?</summary>
					<div class="faq-ans">Eine manuell geprüfte Ersteinschätzung mit klarer Priorisierung und einem sinnvollen nächsten Schritt.</div>
				</details>
				<details>
					<summary>Für welche Seiten ist der Audit gedacht?</summary>
					<div class="faq-ans">Vor allem für Startseiten und kaufnahe Angebotsseiten in WordPress, die bereits Anfragen tragen sollen oder genau daran gerade scheitern.</div>
				</details>
				<details>
					<summary>Muss ich danach einen Call buchen?</summary>
					<div class="faq-ans">Nein. Die Rückmeldung kommt schriftlich. Ein Gespräch ergibt sich nur, wenn es sinnvoll ist.</div>
				</details>
			</section>

			<section id="cta" class="audit-section ga-final-cta nx-reveal" aria-labelledby="ga-cta-title">
				<div class="ga-card ga-cta-card">
					<div class="ga-section-head ga-section-head--compact">
						<p class="ga-section-kicker">Nächster Schritt</p>
						<h2 id="ga-cta-title">Eine URL reicht für den Anfang.</h2>
						<p>Schriftlich. Manuell geprüft. Innerhalb von 48 Stunden.</p>
					</div>

					<div class="ga-cta-actions">
						<a class="ga-btn ga-btn--primary" href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_final_request" data-track-category="lead_gen" data-track-section="final_cta">Anfrage stellen</a>
						<a class="ga-btn ga-btn--secondary" href="#form" data-track-action="cta_final_growth_audit" data-track-category="lead_gen" data-track-section="final_cta">Audit starten</a>
					</div>
				</div>
			</section>
		</main>
	</div>
</div>
