<?php
/**
 * Template Name: Tools
 * Description: Seite mit verschiedenen Analyse-Tools für B2B-Unternehmen
 *
 * SEO-Meta: zentral in inc/seo-meta.php (ACF-Felder: seo_title, seo_description, og_image)
 *
 * @package Blocksy_Child
 */

get_header();
?>

<div class="tools-page-wrapper" data-track-section="tools_page">
	<div class="tools-container">
		<main class="tools-content">
			<section class="tools-hero nx-reveal">
				<div class="tools-hero__inner">
					<p class="tools-kicker">Analyse-Tools</p>
					<h1>Wo Ihre Seite gerade Anfragen ausbremst.</h1>
					<p class="tools-hero__sub">
						Entdecken Sie mit unseren Tools Schwachstellen in Ihren Lead-Kosten, Conversion-Raten, 
						Tracking-Setup und Ladezeiten. Jedes Tool liefert konkrete Erkenntnisse für Ihren nächsten Schritt.
					</p>
				</div>
			</section>

			<!-- Lead-Kosten-Rechner -->
			<section class="tools-section nx-reveal" id="lead-cost-calculator">
				<div class="tools-card">
					<div class="tools-section-head">
						<p class="tools-section-kicker">Lead-Kosten-Rechner</p>
						<h2>Wie viel kosten Ihre Leads wirklich?</h2>
						<p>Vergleichen Sie die Kosten von Portal-Leads mit organischen Leads.</p>
					</div>

					<div class="tool-container">
						<div class="tool-inputs">
							<div class="tool-form__field">
								<label for="portal-leads">Monatliche Portal-Leads</label>
								<input id="portal-leads" type="number" min="0" value="100">
							</div>

							<div class="tool-form__field">
								<label for="portal-cost-per-lead">Kosten pro Portal-Lead (€)</label>
								<input id="portal-cost-per-lead" type="number" min="0" step="0.01" value="25.00">
							</div>

							<div class="tool-form__field">
								<label for="organic-leads">Monatliche organische Leads</label>
								<input id="organic-leads" type="number" min="0" value="20">
							</div>

							<div class="tool-form__field">
								<label for="organic-cost-per-lead">Kosten pro organischem Lead (€)</label>
								<input id="organic-cost-per-lead" type="number" min="0" step="0.01" value="15.00">
							</div>
						</div>

						<div class="tool-results">
							<h3>Ihr Lead-Kosten-Vergleich</h3>
							<div class="results-grid">
								<div class="result-item">
									<p class="result-label">Kosten für Portal-Leads</p>
									<p class="result-value" id="portal-total-cost">2.500,00 €</p>
								</div>
								<div class="result-item">
									<p class="result-label">Kosten für organische Leads</p>
									<p class="result-value" id="organic-total-cost">300,00 €</p>
								</div>
								<div class="result-item">
									<p class="result-label">Einsparungspotenzial</p>
									<p class="result-value result-value--highlight" id="savings-potential">2.200,00 €</p>
								</div>
								<div class="result-item">
									<p class="result-label">Kostenunterschied pro Lead</p>
									<p class="result-value" id="cost-difference-per-lead">10,00 €</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<!-- Traffic vs Conversion Simulator -->
			<section class="tools-section nx-reveal" id="conversion-simulator">
				<div class="tools-card">
					<div class="tools-section-head">
						<p class="tools-section-kicker">Conversion-Simulator</p>
						<h2>Wie viel bringt bessere Conversion?</h2>
						<p>Sehen Sie den Einfluss einer verbesserten Conversion-Rate.</p>
					</div>

					<div class="tool-container">
						<div class="tool-inputs">
							<div class="tool-form__field">
								<label for="monthly-visitors">Monatliche Besucher</label>
								<input id="monthly-visitors" type="number" min="0" value="10000">
							</div>

							<div class="tool-form__field">
								<label for="current-conversion-rate">Aktuelle Conversion-Rate (%)</label>
								<input id="current-conversion-rate" type="number" min="0" max="100" step="0.1" value="2.5">
							</div>

							<div class="tool-form__field">
								<label for="conversion-improvement">Conversion-Verbesserung (%)</label>
								<input id="conversion-improvement" type="range" min="0" max="10" step="0.1" value="1">
								<span id="conversion-improvement-value">1,0%</span>
							</div>
						</div>

						<div class="tool-results">
							<h3>Ihr Conversion-Vergleich</h3>
							<div class="results-grid">
								<div class="result-item">
									<p class="result-label">Aktuelle monatliche Konversionen</p>
									<p class="result-value" id="current-conversions">250</p>
								</div>
								<div class="result-item">
									<p class="result-label">Konversionen mit Verbesserung</p>
									<p class="result-value" id="improved-conversions">350</p>
								</div>
								<div class="result-item">
									<p class="result-label">Zusätzliche Konversionen</p>
									<p class="result-value result-value--highlight" id="additional-conversions">100</p>
								</div>
								<div class="result-item">
									<p class="result-label">Wert der Verbesserung</p>
									<p class="result-value" id="improvement-value">1.000 €</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<!-- Tracking & Consent Scanner -->
			<section class="tools-section nx-reveal" id="tracking-scanner">
				<div class="tools-card">
					<div class="tools-section-head">
						<p class="tools-section-kicker">Tracking-Scanner</p>
						<h2>Ist Ihr Tracking DSGVO-konform?</h2>
						<p>Prüfen Sie Ihr Tracking-Setup auf Risiken und Lücken.</p>
					</div>

					<div class="tool-container">
						<div class="tool-inputs">
							<div class="tool-form__field">
								<label for="website-url">Website-URL</label>
								<input id="website-url" type="url" placeholder="https://www.beispiel.de">
							</div>
							<button id="scan-button" class="tool-btn tool-btn--primary">Scan starten</button>
						</div>

						<div class="tool-results">
							<h3>Scan-Ergebnisse</h3>
							<div class="scan-results-placeholder">
								<p>Geben Sie Ihre URL ein und starten Sie den Scan, um eine Analyse Ihres Tracking-Setups zu erhalten.</p>
							</div>
							<div class="scan-results-content" hidden>
								<div class="result-item">
									<p class="result-label">Gefundene Tracking-Tools</p>
									<p class="result-value" id="tracking-tools">Google Analytics, Facebook Pixel</p>
								</div>
								<div class="result-item">
									<p class="result-label">Consent-Management</p>
									<p class="result-value" id="consent-management">Cookiebot nicht implementiert</p>
								</div>
								<div class="result-item">
									<p class="result-label">DSGVO-Risiken</p>
									<p class="result-value result-value--warning" id="gdpr-risks">3 mittlere Risiken erkannt</p>
								</div>
								<div class="result-item">
									<p class="result-label">Empfehlung</p>
									<p class="result-value" id="recommendation">Implementierung von Cookiebot empfohlen</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<!-- Speed Penalty Calculator -->
			<section class="tools-section nx-reveal" id="speed-penalty-calculator">
				<div class="tools-card">
					<div class="tools-section-head">
						<p class="tools-section-kicker">Speed-Penalty-Rechner</p>
						<h2>Wie viel kosten Sie langsame Ladezeiten?</h2>
						<p>Berechnen Sie den Umsatzverlust durch schlechte Ladezeiten.</p>
					</div>

					<div class="tool-container">
						<div class="tool-inputs">
							<div class="tool-form__field">
								<label for="monthly-visitors-speed">Monatliche Besucher</label>
								<input id="monthly-visitors-speed" type="number" min="0" value="10000">
							</div>

							<div class="tool-form__field">
								<label for="current-load-time">Aktuelle Ladezeit (Sekunden)</label>
								<input id="current-load-time" type="number" min="0" step="0.1" value="4.5">
							</div>

							<div class="tool-form__field">
								<label for="industry-type">Branche</label>
								<select id="industry-type">
									<option value="ecommerce">E-Commerce</option>
									<option value="b2b" selected>B2B</option>
									<option value="blog">Blog</option>
									<option value="saas">SaaS</option>
								</select>
							</div>
						</div>

						<div class="tool-results">
							<h3>Ihre Geschwindigkeitskosten</h3>
							<div class="results-grid">
								<div class="result-item">
									<p class="result-label">Geschätzte Abbruchrate</p>
									<p class="result-value" id="bounce-rate">45%</p>
								</div>
								<div class="result-item">
									<p class="result-label">Verlorene Besucher monatlich</p>
									<p class="result-value" id="lost-visitors">4.500</p>
								</div>
								<div class="result-item">
									<p class="result-label">Geschätzter Umsatzverlust</p>
									<p class="result-value result-value--highlight" id="revenue-loss">45.000 €</p>
								</div>
								<div class="result-item">
									<p class="result-label">Potenzielle Leads verloren</p>
									<p class="result-value" id="lost-leads">450</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<section class="tools-section tools-cta nx-reveal">
				<div class="tools-card tools-cta-card">
					<div class="tools-section-head">
						<h2>Bereit für tiefere Einblicke?</h2>
						<p>Unsere Tools zeigen erste Ansätze. Eine vollständige System-Diagnose liefert Ihnen detaillierte Empfehlungen für messbare Verbesserungen.</p>
					</div>
					<div class="tools-cta-actions">
						<a class="tool-btn tool-btn--primary" href="<?php echo esc_url( function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/anfrage-system-analyse/' ) ); ?>" data-track-action="cta_tools_page_analysis" data-track-category="lead_gen" data-track-section="tools_cta">Anfrage-System-Analyse starten</a>
					</div>
				</div>
			</section>
		</main>
	</div>
</div>

<?php get_footer(); ?>
