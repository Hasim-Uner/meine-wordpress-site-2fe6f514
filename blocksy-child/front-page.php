<?php
/**
 * Front Page Template — Redesign
 *
 * Portiert aus dem Claude-Design-Export „Hasimuener Startseite.html".
 * Visuelle Sprache: dunkel (#0B0F12), warm-weiß, Kupfer-Akzent.
 * Alle PHP-Datenbindungen bleiben erhalten.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── PHP-Datenbindungen ──────────────────────────────────── */
$analysis_url      = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/system-diagnose/' );
$e3_canon          = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url       = isset( $e3_canon['url'] ) ? (string) $e3_canon['url'] : home_url( '/e3-new-energy/' );
$e3_metrics        = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_lead_count     = $e3_metrics['lead_count']['display']        ?? '1.750+';
$e3_sales_conv     = $e3_metrics['sales_conversion']['display']  ?? '12 %';
$e3_cpl_reduction  = $e3_metrics['cpl_reduction']['display']     ?? 'über 85 %';
$e3_cpl_cons       = $e3_metrics['cpl_reduction']['conservative_display'] ?? 'über 85 %';
$e3_timeframe      = $e3_metrics['timeframe']['display']         ?? '9 Monate';
$e3_timeframe_dat  = $e3_metrics['timeframe']['display_dative']  ?? '9 Monaten';
$contact_url       = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' );
$portrait_url      = get_stylesheet_directory_uri() . '/assets/img/hasim-portrait.png';

get_header();
?>

<div class="hu-hp" id="top" data-track-section="homepage">

	<!-- ═══════════════════════════════════════════════════
	     HERO
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-hero" id="hero" data-track-section="homepage_hero">
		<div class="hu-hero__grid-bg" aria-hidden="true"></div>
		<div class="hu-container hu-hero__container">

			<!-- Left: copy -->
			<div>
				<div class="hu-hero__eyebrow">
					<span class="hu-tag">
						<span class="hu-dot hu-dot--live"></span>
						<span class="hu-mono">FÜR SOLAR- & WÄRMEPUMPEN-BETRIEBE</span>
					</span>
				</div>

				<h1 class="hu-display hu-hero__title">
					Hören Sie auf,<br>
					Anfragen<br>
					zu&nbsp;<span class="hu-hero__title-2">mieten.</span>
				</h1>

				<p class="hu-hero__sub">
					Portale liefern dieselbe Anfrage an drei Wettbewerber. Sie zahlen — und müssen sich trotzdem rechtfertigen.
					Bauen Sie stattdessen einen Anfrageweg, der Ihnen gehört.
					<strong>Wie <?php echo esc_html( 'E3 New Energy' ); ?>: <?php echo esc_html( $e3_lead_count ); ?> Anfragen in <?php echo esc_html( $e3_timeframe_dat ); ?>, <?php echo esc_html( $e3_sales_conv ); ?> Abschluss, <?php echo esc_html( $e3_cpl_cons ); ?> weniger Kosten pro Anfrage.</strong>
				</p>

				<div class="hu-hero__stats">
					<div>
						<div class="hu-stat-num"><?php echo esc_html( $e3_lead_count ); ?></div>
						<div class="hu-stat-label">Anfragen / 9 Monate</div>
					</div>
					<div class="hu-stat-divider"></div>
					<div>
						<div class="hu-stat-num"><?php echo esc_html( $e3_sales_conv ); ?></div>
						<div class="hu-stat-label">Abschlussquote</div>
					</div>
					<div class="hu-stat-divider"></div>
					<div>
						<div class="hu-stat-num" style="color:var(--accent)"><?php echo esc_html( $e3_cpl_reduction ); ?></div>
						<div class="hu-stat-label">Kosten / Anfrage</div>
					</div>
				</div>

				<div class="hu-hero__ctas">
					<a href="<?php echo esc_url( $analysis_url ); ?>" class="hu-btn hu-btn-primary"
					   data-track-action="cta_home_hero_analysis" data-track-category="lead_gen">
						Prüfen, ob es passt
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
					</a>
					<a href="<?php echo esc_url( $e3_case_url ); ?>" class="hu-btn hu-btn-link"
					   data-track-action="cta_home_hero_e3_methodology" data-track-category="lead_gen">Methodik-Case lesen</a>
				</div>

				<ul class="hu-hero__bullets">
					<li><span class="hu-bullet-dot"></span>Keine Kontaktdaten im ersten Schritt</li>
					<li><span class="hu-bullet-dot"></span>Diagnose vor Angebot</li>
					<li><span class="hu-bullet-dot"></span>Hannover · remote bundesweit</li>
				</ul>
			</div>

			<!-- Right: diagram -->
			<div aria-label="Skizze des eigenen Anfrage-Wegs">
				<div class="hu-diagram">
					<div class="hu-diagram__head">
						<span class="hu-eyebrow">Anfrageweg</span>
						<span class="hu-diagram__head-r">Vom Marktrauschen zur klaren Anfrage</span>
					</div>

					<div class="hu-diagram__body">
						<!-- Left col: Markt-Standard -->
						<div class="hu-diagram__col">
							<div class="hu-diagram__col-label hu-eyebrow">Markt-Standard</div>
							<div class="hu-diagram__card">
								<div class="hu-diagram__card-title">Portal-Lead</div>
								<div class="hu-diagram__card-note">4 Anbieter erhalten dieselbe Anfrage</div>
							</div>
							<div class="hu-diagram__card">
								<div class="hu-diagram__card-title">Generischer Funnel</div>
								<div class="hu-diagram__card-note">Klickbericht ohne Projekt-Signal</div>
							</div>
							<div class="hu-diagram__card">
								<div class="hu-diagram__card-title">Empfehlung</div>
								<div class="hu-diagram__card-note">vertrauenswürdig, aber nicht skalierbar</div>
							</div>
						</div>

						<!-- Center: Fit-Check -->
						<div class="hu-diagram__center">
							<div class="hu-diagram__analyse">
								<div class="hu-eyebrow" style="color:var(--accent)">Fit-Check</div>
								<div class="hu-diagram__analyse-title">grün · gelb · rot</div>
								<div class="hu-diagram__analyse-tag hu-mono">vor Anruf</div>
							</div>
							<svg class="hu-diagram__lines" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
								<path d="M 0 18 L 50 50"/>
								<path d="M 0 50 L 50 50"/>
								<path d="M 0 82 L 50 50"/>
								<path d="M 50 50 L 100 18"/>
								<path d="M 50 50 L 100 50"/>
								<path d="M 50 50 L 100 82"/>
							</svg>
						</div>

						<!-- Right col: Eigener Weg -->
						<div class="hu-diagram__col">
							<div class="hu-diagram__col-label hu-eyebrow">Eigener Weg</div>
							<div class="hu-diagram__card">
								<div class="hu-diagram__card-title">Eigene Money Page</div>
								<div class="hu-diagram__card-note">Region, Angebot, Beweis – sichtbar</div>
							</div>
							<div class="hu-diagram__card">
								<div class="hu-diagram__card-title">Vorqualifizierung</div>
								<div class="hu-diagram__card-note">60-Sek-Funnel statt Formular-Marathon</div>
							</div>
							<div class="hu-diagram__card">
								<div class="hu-diagram__card-title">Privacy-first Tracking</div>
								<div class="hu-diagram__card-note">Kanal sehen, ohne Datenchaos</div>
							</div>
						</div>
					</div>

					<div class="hu-diagram__foot">
						<div>
							<div class="hu-eyebrow">Was am Ende ankommt</div>
							<div class="hu-diagram__foot-title">Solar oder WP · Region passt · Projektwert klar</div>
						</div>
						<div class="hu-diagram__foot-r hu-mono">Daten erst nach Fit & Consent</div>
					</div>

					<div class="hu-diagram__metrics">
						<div class="hu-diagram__metric">
							<div class="hu-diagram__metric-num" style="color:var(--accent)"><?php echo esc_html( $e3_cpl_reduction ); ?></div>
							<div class="hu-diagram__metric-lbl">Kosten / Anfrage</div>
						</div>
						<div class="hu-diagram__metric">
							<div class="hu-diagram__metric-num"><?php echo esc_html( $e3_lead_count ); ?></div>
							<div class="hu-diagram__metric-lbl">Anfragen</div>
						</div>
						<div class="hu-diagram__metric">
							<div class="hu-diagram__metric-num"><?php echo esc_html( $e3_sales_conv ); ?></div>
							<div class="hu-diagram__metric-lbl">Abschluss</div>
						</div>
					</div>
				</div>
			</div>

		</div><!-- .hu-hero__container -->
	</section>

	<!-- ═══════════════════════════════════════════════════
	     01 / STATUS QUO
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--cream" id="kostet" data-track-section="homepage_pain">
		<div class="hu-container">
			<div class="hu-proof-headline hu-reveal">
				<span class="hu-eyebrow">01 / Status quo</span>
				<h2 style="color:var(--ink)">Was Sie gerade jeden Monat verlieren.</h2>
				<p>Drei Posten, die jeder Solar-Betrieb spürt — selten in Euro misst.</p>
			</div>

			<div class="hu-cost-grid">
				<article class="hu-cost-card hu-reveal">
					<div class="hu-cost-card__big">~ 4.800 € / Monat</div>
					<div class="hu-cost-card__sub">Sie füttern Wettbewerber</div>
					<p class="hu-cost-card__body">Portal-Leads kennen das Portal — nicht Sie. Ihr Vertrieb startet jedes Gespräch von null.</p>
				</article>
				<article class="hu-cost-card hu-reveal">
					<div class="hu-cost-card__big">Blindflug</div>
					<div class="hu-cost-card__sub">Klicks ≠ Anfragen ≠ Termine</div>
					<p class="hu-cost-card__body">Drei getrennte Tools, kein gemeinsames Bild. Sie wissen nicht, welcher Kanal Umsatz produziert.</p>
				</article>
				<article class="hu-cost-card hu-reveal">
					<div class="hu-cost-card__big">Seit 2024 härter</div>
					<div class="hu-cost-card__sub">Boom trägt schwache Setups nicht mehr</div>
					<p class="hu-cost-card__body">Der Markt wird teurer. Eine schöne Website reicht nicht. Es braucht einen eigenen Qualifizierungsweg.</p>
				</article>
			</div>

			<div style="text-align:center;margin-top:48px" class="hu-reveal">
				<a href="<?php echo esc_url( $analysis_url ); ?>" class="hu-btn hu-btn-primary"
				   data-track-action="cta_home_pain_analysis" data-track-category="lead_gen">
					Prüfen, ob es passt
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
				</a>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     02 / MARKT VS. EIGENER WEG
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="system" data-track-section="homepage_compare">
		<div class="hu-container">
			<div class="hu-section-head hu-reveal">
				<span class="hu-eyebrow">02 / Markt vs. eigener Weg</span>
				<div>
					<h2>Sie kaufen keine Leads mehr. Sie bauen den Weg, auf dem sie entstehen.</h2>
					<p class="hu-lead">Der Ausweg ist nicht ein besseres Portal. Es ist ein Anfrageweg, der Ihnen gehört — und sich messen lässt.</p>
				</div>
			</div>

			<div class="hu-compare hu-reveal">
				<!-- Bad column -->
				<div class="hu-compare__col hu-compare__col--bad">
					<div class="hu-compare__head">
						<span class="hu-compare__icon">✕</span>
						<span>Markt-Standard</span>
					</div>
					<div class="hu-compare__row">
						<div class="hu-compare__row-t">Portal-Leads</div>
						<div class="hu-compare__row-d">Drei Wettbewerber bekommen dieselbe Anfrage. Sie bieten gegen den Preis.</div>
					</div>
					<div class="hu-compare__row">
						<div class="hu-compare__row-t">Klickberichte</div>
						<div class="hu-compare__row-d">Reportings über Impressionen — nicht über Projektwert.</div>
					</div>
					<div class="hu-compare__row">
						<div class="hu-compare__row-t">5-Felder-Formular</div>
						<div class="hu-compare__row-d">Verliert Interessenten, bevor sie qualifiziert sind.</div>
					</div>
					<div class="hu-compare__row">
						<div class="hu-compare__row-t">Black-Box-Agentur</div>
						<div class="hu-compare__row-d">Kein Mensch weiß, woher die nächste Anfrage kommt.</div>
					</div>
				</div>

				<!-- Divider -->
				<div class="hu-compare__divider" aria-hidden="true">
					<span class="hu-compare__divider-icon">→</span>
				</div>

				<!-- Good column -->
				<div class="hu-compare__col hu-compare__col--good">
					<div class="hu-compare__head">
						<span class="hu-compare__icon hu-compare__icon--good">✓</span>
						<span>Eigener Weg</span>
					</div>
					<div class="hu-compare__row">
						<div class="hu-compare__row-t">Eigene Anfragestrecke</div>
						<div class="hu-compare__row-d">Anfragen, die Ihrem Betrieb gehören — nicht dem Portal.</div>
					</div>
					<div class="hu-compare__row">
						<div class="hu-compare__row-t">Anfragequalität messbar</div>
						<div class="hu-compare__row-d">Region, Heizart, Dach, Projektwert — vor dem Anruf.</div>
					</div>
					<div class="hu-compare__row">
						<div class="hu-compare__row-t">60-Sek-Vorqualifizierung</div>
						<div class="hu-compare__row-d">Daten erst, wenn der Fit klar ist.</div>
					</div>
					<div class="hu-compare__row">
						<div class="hu-compare__row-t">Dokumentiertes System</div>
						<div class="hu-compare__row-d">Sie verstehen, warum es funktioniert. Sie können es weitergeben.</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     03 / DER WEG (Process, 3 Schritte)
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--cream" data-track-section="homepage_process">
		<div class="hu-container">
			<div class="hu-proof-headline hu-reveal">
				<span class="hu-eyebrow">03 / Der Weg</span>
				<h2 style="color:var(--ink)">Drei Schritte. Keine Reise.</h2>
				<p>Sie starten mit Klarheit, nicht mit einem Vertrag.</p>
			</div>

			<div class="hu-process-grid">
				<!-- Left: asset visual -->
				<div class="hu-process-asset hu-reveal">
					<div class="hu-process-asset__row">
						<div class="hu-eyebrow">Analyse</div>
						<div class="hu-process-asset__row-title">Marktbild + Fit-Ampel</div>
					</div>
					<div class="hu-process-asset__row">
						<div class="hu-eyebrow">Architektur</div>
						<div class="hu-process-asset__row-title">Money Page · Funnel · Tracking</div>
					</div>
					<div class="hu-process-asset__row">
						<div class="hu-eyebrow">Output</div>
						<div class="hu-process-asset__row-title">Eigener Anfrageweg</div>
					</div>
					<div class="hu-process-asset__metrics">
						<div class="hu-process-asset__metric">
							<div class="hu-process-asset__metric-num" style="color:var(--accent)"><?php echo esc_html( $e3_cpl_reduction ); ?></div>
							<div class="hu-process-asset__metric-lbl">Kosten / Anfrage</div>
						</div>
						<div class="hu-process-asset__metric">
							<div class="hu-process-asset__metric-num"><?php echo esc_html( $e3_timeframe ); ?></div>
							<div class="hu-process-asset__metric-lbl">Referenz-Zeitraum</div>
						</div>
					</div>
				</div>

				<!-- Right: steps -->
				<div class="hu-steps hu-reveal">
					<div class="hu-step">
						<div class="hu-step__num">1</div>
						<div class="hu-step__title">Analysieren</div>
						<p class="hu-step__body">Markt, Region, Projektwert, bestehende Kanäle. Wir schauen ehrlich, ob ein eigener Weg sich rechnet — ohne Verkaufsgespräch.</p>
						<div class="hu-step__out">→ Marktbild + Fit-Ampel</div>
					</div>
					<div class="hu-step">
						<div class="hu-step__num">2</div>
						<div class="hu-step__title">Qualifizieren</div>
						<p class="hu-step__body">Wenn grün oder gelb: Fit-Ampel pro Anfrage, Leadkosten-Korridor, nächster Schritt. Bauchgefühl raus, Signal rein.</p>
						<div class="hu-step__out">→ Klare Entscheidung statt Bauchgefühl</div>
					</div>
					<div class="hu-step">
						<div class="hu-step__num">3</div>
						<div class="hu-step__title">Umsetzen</div>
						<p class="hu-step__body">Nur passende Founding-Partner gehen in den Aufbau: Money Page, Funnel, Tracking, Ads-Setup. 9 Monate Begleitung.</p>
						<div class="hu-step__out">→ Eigener Anfrageweg — bleibt Ihnen.</div>
					</div>
				</div>
			</div>

			<div style="text-align:center;margin-top:48px" class="hu-reveal">
				<a href="<?php echo esc_url( $analysis_url ); ?>" class="hu-btn hu-btn-primary"
				   data-track-action="cta_home_process_analysis" data-track-category="lead_gen">
					Prüfen, ob es passt
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
				</a>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     04 / ZWEI WEGE (Models)
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" data-track-section="homepage_models">
		<div class="hu-container">
			<div class="hu-section-head hu-reveal">
				<span class="hu-eyebrow">04 / Zwei Wege</span>
				<div>
					<h2>Mieten oder besitzen.<br>Eine Entscheidung über 24 Monate.</h2>
					<p class="hu-lead">Beide Wege kosten Geld. Nur einer hinterlässt am Ende ein Asset, das Ihnen gehört.</p>
				</div>
			</div>

			<div class="hu-models hu-reveal">
				<div class="hu-model hu-model--a">
					<div class="hu-model__label">Modell A · Status quo</div>
					<div class="hu-model__title">Nachfrage mieten</div>
					<ul class="hu-model__list">
						<li><span class="hu-model__bullet">✕</span><span>Klicks werden teurer, Ihre Seite konvertiert nicht mit.</span></li>
						<li><span class="hu-model__bullet">✕</span><span>Reports ohne klares Entscheidungssignal.</span></li>
						<li><span class="hu-model__bullet">✕</span><span>Budget aus = Anfragen aus.</span></li>
					</ul>
					<div class="hu-model__foot">24 Monate ≈ 26.000 € · 0 € Asset am Ende</div>
					<span class="hu-model__pill">Kostet, ohne zu skalieren</span>
				</div>
				<div class="hu-model hu-model--b">
					<div class="hu-model__label">Modell B · Empfohlen</div>
					<div class="hu-model__title">Anfrageweg besitzen</div>
					<ul class="hu-model__list">
						<li><span class="hu-model__bullet">✓</span><span>Money Page und Proof werden bleibende Assets.</span></li>
						<li><span class="hu-model__bullet">✓</span><span>Privacy-first Tracking liefert echte Entscheidungssignale.</span></li>
						<li><span class="hu-model__bullet">✓</span><span>Skalieren, wenn das Fundament steht — nicht vorher.</span></li>
					</ul>
					<div class="hu-model__foot">24 Monate ≈ 13.200 – 19.200 € · aktiviertes Asset</div>
					<span class="hu-model__pill">Asset, das bleibt</span>
				</div>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     05 / E3 PROOF
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="proof" data-track-section="homepage_proof">
		<div class="hu-container">
			<div class="hu-proof-headline hu-reveal">
				<span class="hu-eyebrow">05 / E3 New Energy</span>
				<h2>Vom Lead-Einkauf zur eigenen Pipeline.</h2>
				<p><?php echo esc_html( $e3_timeframe ); ?>. Eine Referenz, die nicht auf Folien steht.</p>
			</div>

			<div class="hu-proof-cards hu-reveal">
				<div class="hu-proof-card">
					<div class="hu-proof-card__lbl">Vorher</div>
					<div class="hu-proof-card__title">Portal-Lead-Welt</div>
					<ul class="hu-proof-list">
						<li><span class="x">✕</span> Hohe Lead-Kosten, schwankende Qualität</li>
						<li><span class="x">✕</span> Hälfte der Leads geht nicht ans Telefon</li>
						<li><span class="x">✕</span> Kein Überblick über konvertierende Kanäle</li>
						<li><span class="x">✕</span> Wachstum nur durch mehr Budget möglich</li>
					</ul>
				</div>
				<div class="hu-proof-arrow" aria-hidden="true">→</div>
				<div class="hu-proof-card hu-proof-card--after">
					<div class="hu-proof-card__lbl">Nach <?php echo esc_html( $e3_timeframe_dat ); ?></div>
					<div class="hu-proof-card__title">Eigene, skalierbare Pipeline</div>
					<ul class="hu-proof-list">
						<li><span class="v">✓</span> Vorqualifizierte Anfragen unter eigener Kontrolle</li>
						<li><span class="v">✓</span> Kanal-Ebene messbar — sauber per Consent</li>
						<li><span class="v">✓</span> Money Page und Proof als bleibende Assets</li>
						<li><span class="v">✓</span> Skaliert, ohne dass die Kosten explodieren</li>
					</ul>
				</div>
			</div>

			<div class="hu-proof-stats hu-reveal">
				<div class="hu-proof-stat">
					<div class="hu-proof-stat__num"><?php echo esc_html( $e3_timeframe ); ?></div>
					<div class="hu-proof-stat__lbl">Dauer</div>
				</div>
				<div class="hu-proof-stat">
					<div class="hu-proof-stat__num"><?php echo esc_html( $e3_lead_count ); ?></div>
					<div class="hu-proof-stat__lbl">Anfragen</div>
				</div>
				<div class="hu-proof-stat">
					<div class="hu-proof-stat__num"><?php echo esc_html( $e3_sales_conv ); ?></div>
					<div class="hu-proof-stat__lbl">Abschluss</div>
				</div>
				<div class="hu-proof-stat">
					<div class="hu-proof-stat__num" style="color:var(--accent)"><?php echo esc_html( $e3_cpl_reduction ); ?></div>
					<div class="hu-proof-stat__lbl">Kosten / Anfrage</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     06 / WANN ES SICH LOHNT (Fit)
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--cream" id="fit" data-track-section="homepage_fit">
		<div class="hu-container">
			<div class="hu-proof-headline hu-reveal" style="margin-bottom:48px">
				<span class="hu-eyebrow">06 / Wann es sich lohnt</span>
				<h2 style="color:var(--ink)">Ehrliche Vorauswahl, bevor wir reden.</h2>
				<p>Lieber jetzt klären, ob es passt — als später ein Setup zu bauen, das ins Leere läuft.</p>
			</div>

			<div class="hu-fit-grid hu-reveal">
				<div class="hu-fit-col hu-fit-col--yes">
					<div class="hu-fit-col__head">
						<span class="hu-fit-col__badge hu-fit-col__badge--yes">✓</span>
						<span>Passt, wenn …</span>
					</div>
					<ul class="hu-fit-list">
						<li>
							<div class="hu-fit-list__t">Solar oder Wärmepumpe im DACH-Mittelstand</div>
							<div class="hu-fit-list__d">Mit eigenem Vertrieb, nicht reine Vermittlung.</div>
						</li>
						<li>
							<div class="hu-fit-list__t">Klares Zielgebiet</div>
							<div class="hu-fit-list__d">Region oder Bundesland definiert — kein „bundesweit, alles".</div>
						</li>
						<li>
							<div class="hu-fit-list__t">Hohe Projektwerte</div>
							<div class="hu-fit-list__d">B2C ab ~15 k €, B2B ab ~50 k € pro Projekt.</div>
						</li>
						<li>
							<div class="hu-fit-list__t">Geschäftsführung entscheidet</div>
							<div class="hu-fit-list__d">Über Aufbau, Marke und Positionierung — kurze Wege.</div>
						</li>
						<li>
							<div class="hu-fit-list__t">24-Monate-Horizont</div>
							<div class="hu-fit-list__d">Bereit, ein Asset aufzubauen statt nur Anfragen einzukaufen.</div>
						</li>
					</ul>
				</div>
				<div class="hu-fit-col hu-fit-col--no">
					<div class="hu-fit-col__head">
						<span class="hu-fit-col__badge hu-fit-col__badge--no">✕</span>
						<span>Passt nicht, wenn …</span>
					</div>
					<ul class="hu-fit-list">
						<li>
							<div class="hu-fit-list__t">Reines Vermittlungsgeschäft</div>
							<div class="hu-fit-list__d">Wer Leads weiterverkauft, braucht kein eigenes System.</div>
						</li>
						<li>
							<div class="hu-fit-list__t">„Nächste Woche brauchen wir Leads."</div>
							<div class="hu-fit-list__d">Tragfähige Pipelines wachsen über Monate, nicht Tage.</div>
						</li>
						<li>
							<div class="hu-fit-list__t">Kein Vertrieb am Telefon</div>
							<div class="hu-fit-list__d">Anfragen sterben, wenn niemand zurückruft.</div>
						</li>
						<li>
							<div class="hu-fit-list__t">Keine Marke gewollt</div>
							<div class="hu-fit-list__d">Eigener Anfrageweg lebt davon, dass Sie sichtbar werden.</div>
						</li>
					</ul>
				</div>
			</div>

			<div style="text-align:center;margin-top:48px" class="hu-reveal">
				<a href="<?php echo esc_url( $analysis_url ); ?>" class="hu-btn hu-btn-primary"
				   data-track-action="cta_home_fit_analysis" data-track-category="lead_gen">
					Analyse-Termin sichern
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
				</a>
				<div class="hu-mono" style="margin-top:16px;font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--ink-2)">
					30 Minuten · keine Verkaufspräsentation · ehrliche Einordnung
				</div>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     07 / FAQ
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="faq" data-track-section="homepage_faq">
		<div class="hu-container" style="max-width:880px">
			<div class="hu-proof-headline hu-reveal" style="margin-bottom:48px">
				<span class="hu-eyebrow">07 / FAQ</span>
				<h2>Was Geschäftsführer wirklich fragen.</h2>
			</div>

			<div class="hu-faq">

				<div class="hu-faq-item is-open">
					<button class="hu-faq-item__q" type="button">
						<span>Bauen Sie nur eine Website oder kümmern Sie sich auch um den Traffic?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">−</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Beides. Die Website ist nur der Motor. Tracking, Vorqualifizierung und Steuerung der Werbekanäle gehören dazu — sonst hängen Sie weiter in Portal-Leads fest.</div>
					</div>
				</div>

				<div class="hu-faq-item">
					<button class="hu-faq-item__q" type="button">
						<span>Arbeiten Sie auch mit bestehenden Websites?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">+</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Ja, wenn die Substanz reicht. Manchmal ist ein Money-Page-Slot auf einer bestehenden Domain der schnellere Hebel als ein kompletter Relaunch. Das klären wir in der Analyse.</div>
					</div>
				</div>

				<div class="hu-faq-item">
					<button class="hu-faq-item__q" type="button">
						<span>Was kostet das Ganze?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">+</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Die System-Diagnose ist 0 €. Der Aufbau danach liegt — abhängig vom Setup — bei 13.200 – 19.200 € verteilt auf 24 Monate. Zum Vergleich: Portal-Leads in derselben Größenordnung kosten ca. 26.000 €. Sie zahlen weniger und behalten das Asset.</div>
					</div>
				</div>

				<div class="hu-faq-item">
					<button class="hu-faq-item__q" type="button">
						<span>Wie lange dauert es bis zu den ersten Anfragen?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">+</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Bei E3 New Energy: erste qualifizierte Anfragen nach 4–6 Wochen, voller Effekt nach 9 Monaten. Schnellere Versprechen sind unseriös — Leadkosten brauchen einen sauberen Trichter, nicht nur ein Logo-Update.</div>
					</div>
				</div>

				<div class="hu-faq-item">
					<button class="hu-faq-item__q" type="button">
						<span>Bin ich gebunden?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">+</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Nein. Es gibt keinen Knebelvertrag. Wir starten mit einer Analyse, dann entscheiden beide — Sie, ob es sich lohnt; ich, ob ich passe. Founding-Cohort-Plätze sind aktuell auf drei pro Jahr begrenzt.</div>
					</div>
				</div>

				<div class="hu-faq-item">
					<button class="hu-faq-item__q" type="button">
						<span>Was, wenn ich keine eigene Marke aufbauen will?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">+</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Dann ist ein eigener Anfrageweg nicht der richtige Hebel für Sie. Sagen Sie es mir früh — ich verkaufe Ihnen nichts, was Sie nicht brauchen.</div>
					</div>
				</div>

			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     08 / ÜBER MICH
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--cream" id="about" data-track-section="homepage_about">
		<div class="hu-container">
			<div class="hu-about-grid">
				<div class="hu-about-photo hu-reveal">
					<img src="<?php echo esc_url( $portrait_url ); ?>" alt="Haşim Üner" width="380" height="380" loading="lazy">
					<div class="hu-about-photo__tag hu-mono">HANNOVER · 2026</div>
				</div>
				<div class="hu-about-text hu-reveal">
					<span class="hu-eyebrow">Wer steht dahinter</span>
					<h2>Ich bohre Brunnen.<br>Digital.</h2>
					<p class="hu-lead" style="color:var(--ink-2)">
						Für Solar- und Wärmepumpen-Betriebe, die ihre Anfragen nicht dauerhaft über Portale
						mieten wollen — sondern eine eigene Nachfrage-Infrastruktur aufbauen.
					</p>
					<p style="color:var(--ink-2);margin-top:16px">
						Mein Zugang ist Medienwissenschaft, nicht Webdesign. Ich denke zuerst über Sprache,
						Entscheidung und Signal nach — und erst danach über Code. Seit E3 New Energy als
						erstem Solar-Case weiß ich, wo diese Methode am stärksten greift.
					</p>
					<ul class="hu-about-bullets">
						<li><span class="hu-about-bullet-dot"></span>Medienwissenschaftlicher Hintergrund — Sprache vor Code</li>
						<li><span class="hu-about-bullet-dot"></span>Jahrelange Arbeit an digitalen Strukturen für erklärungsbedürftige B2B-Angebote</li>
						<li><span class="hu-about-bullet-dot"></span>Fokus auf Solar & Wärmepumpen — seit dem E3-Case</li>
						<li><span class="hu-about-bullet-dot"></span>Founder seit 2026 · Hannover, remote</li>
						<li><span class="hu-about-bullet-dot"></span>Nimmt 2026 maximal 3 Founding-Partner auf</li>
					</ul>
					<a href="<?php echo esc_url( $analysis_url ); ?>" class="hu-btn hu-btn-primary"
					   style="margin-top:8px"
					   data-track-action="cta_home_about_analysis" data-track-category="lead_gen">
						Analyse-Termin sichern
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
					</a>
				</div>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     09 / FINAL CTA
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="cta" data-track-section="homepage_cta">
		<div class="hu-container">
			<div class="hu-final-cta hu-reveal">
				<div class="hu-final-cta__avatar">
					<img src="<?php echo esc_url( $portrait_url ); ?>" alt="Haşim Üner" width="72" height="72" loading="lazy">
				</div>
				<span class="hu-eyebrow" style="color:var(--accent)">09 / Nächster Schritt</span>
				<h2 class="hu-display">Bereit, den Anfrageweg zu besitzen?</h2>
				<p>30 Minuten Analyse. Kein Pitch. Sie bekommen eine ehrliche Einordnung — und gehen mit Klarheit raus, egal wie wir uns entscheiden.</p>

				<div class="hu-cta-flow">

					<a href="<?php echo esc_url( $analysis_url ); ?>" class="hu-cta-option hu-cta-option--primary"
					   data-track-action="cta_home_final_qualify" data-track-category="lead_gen">
						<div class="hu-cta-option__step">SCHRITT 1</div>
						<div class="hu-cta-option__title">Kurz qualifizieren</div>
						<div class="hu-cta-option__desc">3 Fragen — damit das Gespräch nicht bei Null startet.</div>
						<div class="hu-cta-option__cta">
							Formular starten
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
						</div>
					</a>

					<div class="hu-cta-flow__then" aria-hidden="true">
						<span class="hu-cta-flow__line"></span>
						<span class="hu-cta-flow__then-label hu-mono">DANN — wählen Sie den Folgekanal</span>
						<span class="hu-cta-flow__line"></span>
					</div>

					<div class="hu-cta-option-group">
						<a href="<?php echo esc_url( $analysis_url ); ?>" class="hu-cta-option"
						   data-track-action="cta_home_final_termin" data-track-category="lead_gen">
							<div class="hu-cta-option__step">A · TERMIN</div>
							<div class="hu-cta-option__title">Online buchen</div>
							<div class="hu-cta-option__desc">Cal.com — freier Slot, 30 Min.</div>
						</a>
						<a href="<?php echo esc_url( $contact_url ); ?>" class="hu-cta-option"
						   data-track-action="cta_home_final_contact" data-track-category="lead_gen">
							<div class="hu-cta-option__step">B · NACHRICHT</div>
							<div class="hu-cta-option__title">Kontaktformular</div>
							<div class="hu-cta-option__desc">Kurz beschreiben — Antwort innerhalb 24 h.</div>
						</a>
					</div>
				</div>

				<div class="hu-final-cta__signature">— <strong>Haşim Üner</strong> · persönlich, nicht durch ein Vertriebsteam</div>
			</div>
		</div>
	</section>

	<?php /* Eigener Footer entfernt — die Seite nutzt den globalen Blocksy-Footer (siehe get_footer()). */ ?>

</div><!-- .hu-hp -->

<?php get_footer(); ?>
