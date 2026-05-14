<?php
/**
 * Template Name: Solar & Wärmepumpen Leadgenerierung (SOLARA)
 * Description: Premium · cinematic · minimalistisch. Hybrid-Theme (warm-cream + Copper).
 *              Primärer Lead-Pfad: 60-Sek-Marktcheck im Hero (REST → CRM).
 *              Sekundärer Pfad für qualifizierte Fälle: /system-diagnose/.
 *              Zielgruppe: Solar-/Wärmepumpen-Anbieter im DACH-Mittelstand (10–25 MA).
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url     = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
$diagnose_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/system-diagnose/' );
$e3_url       = home_url( '/e3-new-energy/' );
$privacy_url  = home_url( '/datenschutz/' );
$cal_url      = function_exists( 'hu_get_analysis_calcom_base_url' )
	? hu_get_analysis_calcom_base_url()
	: 'https://cal.com/hasim-uener/30min?overlayCalendar=true';

// ── E3-Proof-Metriken (Canon) ──────────────────────────────────
$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? '> 85 %';
$e3_cpl_before       = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after        = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '9 Monate';
$e3_timeframe_dative = $e3_metrics['timeframe']['display_dative'] ?? '9 Monaten';

// ── Inhaltsmodelle ─────────────────────────────────────────────
$hero_metrics = [
	[ 'n' => $e3_cpl_after, 'l' => 'CPL nach 9 Monaten · ' . $e3_case_label ],
	[ 'n' => '< 24 h',      'l' => 'Persönliche Antwort · keine Pitch-Mail' ],
	[ 'n' => '100 %',       'l' => 'Asset-Eigentum · Code · Tracking · Daten' ],
];

$trust_items = [
	'B2B · DACH · Mittelstand',
	'Server in Frankfurt · DSGVO',
	'Server-Side-Tracking · CAPI',
	'Hardcoded WordPress · kein Page-Builder',
	'1:1 Senior · keine Junior-Kette',
	'Marktcheck kostenfrei',
];

$problem_cards = [
	[
		'n' => '01',
		'l' => 'Lead-Einkauf',
		't' => 'Portal-Leads kosten 80–150 €. Die Hälfte geht nicht ans Telefon.',
		's' => 'Aroundhome, Check24, DAA: Mieter ohne Dach, Preisvergleicher ohne Budget, Anfragen, die bei drei Wettbewerbern parallel landen. Ihr Vertrieb verbrennt Stunden mit Leuten, die nie kaufen werden.',
	],
	[
		'n' => '02',
		'l' => 'Blindflug',
		't' => 'Kein Überblick, welcher Kanal sich wirklich lohnt.',
		's' => 'Google Ads, Portal-Leads, Empfehlungen — niemand kann sauber sagen, woher die guten Abschlüsse kommen. Ohne diese Klarheit investieren Sie blind. Und skalieren falsch.',
	],
	[
		'n' => '03',
		'l' => 'Marktwende',
		't' => 'Seit 2024 kommen Anfragen nicht mehr von allein.',
		's' => 'Der PV-Boom ist vorbei. Der Markt normalisiert sich. Wer jetzt keine eigene Anfrage-Infrastruktur hat, ist abhängig von Portalen — und deren Preisen.',
	],
];

$method_cards = [
	[
		'n'  => 'I',
		'p'  => 'Phase 01',
		't'  => 'Diagnose & Fundament',
		's'  => 'Anfrage-Quellen, Tracking, Funnel, Vertriebsanschluss — vier Module, schriftlicher Befund, drei priorisierte Hebel. Verrechenbar auf Umsetzung.',
		'b'  => [ 'Module: Quellen · Daten · Funnel · Sales', 'Schriftlicher Befund · keine Folien', 'Auf Umsetzung 1:1 verrechenbar' ],
	],
	[
		'n'  => 'II',
		'p'  => 'Phase 02',
		't'  => 'Eigenes Anfrage-System',
		's'  => 'WordPress hardcoded — kein Page-Builder, kein Plugin-Stack. Server-Side-Tracking auf eigenem Server. Smarte Vorqualifizierung. Conversion-Pfad ohne Mietsysteme.',
		'b'  => [ 'Money-Page · Proof- & Angebotsseiten', 'Frankfurt-Server · CAPI · DSGVO', '60-Sek-Funnel mit Lead-Scoring' ],
	],
	[
		'n'  => 'III',
		'p'  => 'Phase 03',
		't'  => 'Skalieren & Übergeben',
		's'  => 'Mit sauberem Fundament rechnen sich Google Ads, Meta Ads und SEO endlich. Wöchentliches Reporting. Bei Vertragsende: dokumentierte Übergabe — Code, Tracking, Daten bleiben bei Ihnen.',
		'b'  => [ 'Google Ads · Meta Ads · SEO-Anteil', 'Wöchentliches Reporting', 'Monatlich kündbar · 100 % Asset-Übergabe' ],
	],
];

$results_qualifiers = [
	[ 'k' => 'Anfrage-Quellen',    'v' => 'Beziffert' ],
	[ 'k' => 'Tracking & CAPI',    'v' => 'Auditiert' ],
	[ 'k' => 'Funnel-Hebel',       'v' => 'Drei priorisiert' ],
	[ 'k' => 'Wirtschaftlichkeit', 'v' => 'Einordnung' ],
	[ 'k' => 'Nächster Schritt',   'v' => 'Konkret' ],
];

$guarantee_points = [
	[
		't' => 'Marktcheck ist kostenfrei',
		's' => 'Fünf Fragen, 60 Sekunden. Sie bekommen eine persönliche Ersteinschätzung — ohne Newsletter, ohne Pitch-Deck, ohne Folgekosten.',
	],
	[
		't' => 'Drei Hebel — auch bei Abrat',
		's' => 'Wenn die anschließende Diagnose zum Ergebnis kommt, dass Sie das volle System nicht brauchen, bekommen Sie trotzdem drei priorisierte Hebel mit konkretem nächstem Schritt.',
	],
	[
		't' => 'Diagnose wird verrechnet',
		's' => 'Bei Umsetzung wird die Diagnose 1:1 angerechnet. Sie zahlen sie nur dann, wenn Sie sich gegen die Umsetzung entscheiden. Keine Mindestlaufzeit, volle Asset-Übergabe.',
	],
];

$faq_items = [
	[
		'question' => 'Was passiert nach dem Marktcheck?',
		'answer'   => 'Ich lese Ihre Antworten persönlich und melde mich innerhalb von 24 Stunden per E-Mail. Wenn der Fit passt, schlage ich ein 30-minütiges Erstgespräch vor oder lade Sie in die kostenpflichtige Tiefendiagnose ein. Wenn der Fit nicht passt, sage ich das ehrlich und nenne Ihnen die realistischere Alternative.',
	],
	[
		'question' => 'Was kostet das im Vergleich zur Performance-Agentur?',
		'answer'   => 'Initiales Setup: 12.000–18.000 € einmalig. Laufend ca. 50 €/Monat für Hochleistungs-Hosting. TCO über 24 Monate: 13.200–19.200 € — und Sie besitzen Code, Tracking und Daten. Eine Performance-Agentur mit Paket „Regio+" kostet im gleichen Zeitraum rund 26.000 € und Sie besitzen am Ende nichts. Bilanziell: CAPEX statt OPEX.',
	],
	[
		'question' => 'Welche Daten brauchen Sie für die Diagnose?',
		'answer'   => 'Für den Marktcheck reichen 5 Antworten. Für die Tiefendiagnose: Lesezugriff auf Google Analytics, Google Ads und Meta Ads Manager, Einblick in den CRM-Datenbestand der letzten 90 Tage und eine 15-Minuten-Bestandsaufnahme zu Vertriebsprozess und Lead-Quellen. Wenn Tracking-Daten fehlen, ist das oft schon das erste Diagnose-Ergebnis.',
	],
	[
		'question' => 'Warum nicht einfach mehr Google Ads schalten?',
		'answer'   => 'Mehr Budget auf schlechte Seiten heißt mehr Geld für dieselben unqualifizierten Anfragen. Erst wenn Seite, Formular und serverseitiges Tracking sauber arbeiten, lohnt sich mehr Reichweite. Ohne eigene Datenebene bleiben Sie zudem in der Logik der Plattform.',
	],
	[
		'question' => 'Brauchen wir eine neue Website?',
		'answer'   => 'Meistens nicht im Komplettumfang. Was Sie brauchen: hardcoded WordPress (kein Page-Builder, kein Plugin-Stack), serverseitiges Tracking auf eigenem Server, Conversion-Pfad ohne Mietsysteme. Ob das ein Teil-Umbau oder ein sauberer Erstaufbau wird, zeigt sich im ersten Schritt.',
	],
	[
		'question' => 'Wir nutzen schon eine Performance-Agentur — warum sollten wir wechseln?',
		'answer'   => 'Müssen Sie nicht. Drei Prüffragen: 1) Wem gehört der Code Ihrer Landingpage? 2) Wem gehört das CRM, in dem Ihre Leads liegen? 3) Wem gehört der Tracking-Account? Wenn die Antwort dreimal „uns" ist, brauchen Sie mich nicht. Wenn die Antwort dreimal „der Agentur" ist, mieten Sie ein System.',
	],
	[
		'question' => 'Wie schnell sieht man Ergebnisse?',
		'answer'   => 'Erste Verbesserungen entstehen oft nach den ersten Optimierungen an Tracking, Formularen und Conversion-Pfaden. Belastbare Skalierung braucht in der Regel mehrere Wochen bis Monate — weil Daten, Tests und Kanäle zusammenspielen müssen.',
	],
	[
		'question' => 'Was unterscheidet Sie von Lead-Portalen?',
		'answer'   => 'Portale vermieten Nachfrage. Sie zahlen für jeden Kontakt, den auch 3–4 Mitbewerber erhalten. Das System hier baut eigene Nachfrage-Infrastruktur auf, die Ihrem Betrieb gehört und langfristig für exklusive Anfragen sorgt.',
	],
];

// ── Schema.org ─────────────────────────────────────────────────
$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Aufbau eigener Anfrage-Systeme für Solar- und Wärmepumpen-Anbieter',
	'serviceType' => 'System-Architektur für eigene Lead-Infrastruktur: WordPress, Tracking, Qualifizierung, CRM-Übergabe',
	'url'         => $page_url,
	'description' => sprintf( 'Eigenes Anfrage-System für Solar- und Wärmepumpen-Betriebe. Referenz %1$s: %2$s weniger Kosten pro Anfrage in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe_dative ),
	'provider'    => [
		'@type' => 'Person',
		'name'  => 'Haşim Üner',
		'url'   => home_url( '/' ),
	],
	'audience'    => [
		'@type'        => 'Audience',
		'audienceType' => 'Solar-, Wärmepumpen-, Speicher- und Energie-Anbieter im DACH-Raum',
	],
	'areaServed'  => [
		[ '@type' => 'Country', 'name' => 'Deutschland' ],
	],
	'offers'      => [
		'@type'         => 'Offer',
		'price'         => '0',
		'priceCurrency' => 'EUR',
		'description'   => 'Marktcheck in 60 Sekunden, persönliche Ersteinschätzung innerhalb von 24 Stunden.',
	],
];

$faq_schema = [
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => trailingslashit( $page_url ) . '#faq',
	'url'        => trailingslashit( $page_url ) . '#faq',
	'mainEntity' => [],
];

foreach ( $faq_items as $faq_item ) {
	$faq_schema['mainEntity'][] = [
		'@type'          => 'Question',
		'name'           => $faq_item['question'],
		'acceptedAnswer' => [
			'@type' => 'Answer',
			'text'  => $faq_item['answer'],
		],
	];
}

// ── inline SVG-Pfeil ───────────────────────────────────────────
$arrow_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" focusable="false" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';

get_header();
?>

<main id="main" class="site-main">
	<div class="solara-landing" data-track-section="energy_service_landing">

		<!-- ════════════════════════════════════════════════════════════
		     HERO mit Marktcheck-Quiz
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-hero" id="hero" data-track-section="hero">
			<div class="sol-hero-sky" aria-hidden="true"></div>
			<div class="sol-hero-sun" aria-hidden="true">
				<div class="sol-hero-sun-disc"></div>
				<div class="sol-hero-sun-halo"></div>
				<div class="sol-hero-sun-rays" data-sol-rays></div>
			</div>
			<div class="sol-hero-horizon" aria-hidden="true"></div>

			<div class="sol-wrap sol-hero-inner">
				<div class="sol-hero-left">
					<div class="sol-eyebrow">Für Solar- &amp; Wärmepumpen-Anbieter · 10–25 MA · DACH</div>
					<h1 class="sol-display sol-hero-h1">
						Hören Sie auf,<br /><em>Anfragen zu mieten.</em><br />Bauen Sie eine,<br />die <em>Ihnen gehört.</em>
					</h1>
					<p class="sol-hero-sub">
						Portal-Leads kosten 80–150 €. Die Hälfte geht nicht ans Telefon. Wir bauen Ihrem Betrieb ein eigenes Anfrage-System — WordPress hardcoded, Tracking auf eigenem Server, Vorqualifizierung mit Lead-Score. Das System gehört Ihnen. Die Anfragen sind exklusiv.
					</p>

					<div class="sol-hero-metrics">
						<?php foreach ( $hero_metrics as $m ) : ?>
							<div class="sol-metric">
								<div class="sol-metric-n sol-display"><?php echo esc_html( $m['n'] ); ?></div>
								<div class="sol-metric-l sol-mono"><?php echo esc_html( $m['l'] ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>

					<p class="sol-mono" style="margin-top:14px;color:var(--sol-fg-dim);font-size:11px;letter-spacing:.06em;">
						Bereits qualifizierter Fall?
						<a
							href="<?php echo esc_url( $diagnose_url ); ?>"
							style="color:var(--sol-accent);text-decoration:underline;text-underline-offset:3px;margin-left:6px;"
							data-track-action="cta_solar_to_deep_diagnose"
							data-track-category="lead_funnel"
							data-track-section="hero_secondary"
						>Direkt zur Tiefendiagnose →</a>
					</p>
				</div>

				<aside class="sol-hero-right" aria-labelledby="sol-quiz-title">
					<div class="sol-cta-card" id="marktcheck">
						<!--
						  Quiz mount point. JS rendert hier das 5-Step-Quiz.
						  Wenn JS fehlt, bleibt der SSR-Fallback unten als
						  funktionierende Alternative.
						-->
						<div data-sol-quiz id="sol-quiz-mount">
							<noscript>
								<div class="sol-cta-head">
									<span class="sol-cta-tag sol-mono">
										<span class="sol-cta-tag-dot" aria-hidden="true"></span>
										Marktcheck · 60 Sek
									</span>
									<span class="sol-cta-head-right sol-mono">Kostenfrei</span>
								</div>
								<h2 id="sol-quiz-title" class="sol-cta-title">
									Wo verlieren Sie heute Anfragen — und wie viel kostet Sie das?
								</h2>
								<p class="sol-cta-hint">
									Aktivieren Sie JavaScript für den 60-Sek-Marktcheck oder schreiben Sie direkt an
									<a href="mailto:hasim@hasimuener.de" style="color:var(--sol-accent);">hasim@hasimuener.de</a>.
								</p>
								<a
									class="sol-cta-submit"
									href="<?php echo esc_url( $diagnose_url ); ?>"
									data-track-action="cta_solar_to_deep_diagnose"
									data-track-category="lead_funnel"
									data-track-section="hero_noscript"
								>
									<span>Zur Tiefendiagnose</span>
									<span class="sol-cta-submit-arrow" aria-hidden="true"><?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								</a>
							</noscript>

							<!-- SSR-Fallback (sichtbar bis JS gemounted hat) -->
							<div class="sol-cta-head">
								<span class="sol-cta-tag sol-mono">
									<span class="sol-cta-tag-dot" aria-hidden="true"></span>
									Marktcheck · 60 Sek · 5 Fragen
								</span>
								<span class="sol-cta-head-right sol-mono">Kostenfrei</span>
							</div>
							<h2 id="sol-quiz-title" class="sol-cta-title">
								Wo verlieren Sie heute Anfragen — und wie viel kostet Sie das?
							</h2>
							<p class="sol-cta-hint">
								Fünf Fragen, 60 Sekunden. Persönliche Ersteinschätzung innerhalb von 24 h — keine Newsletter, keine Pitch-Mail.
							</p>
							<p class="sol-cta-fineprint" style="text-align:left;margin:0 0 14px;">Wird geladen …</p>
						</div>
					</div>
				</aside>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     TRUST STRIP
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-trust" aria-label="Vertrauenssignale" data-track-section="trust_strip">
			<div class="sol-wrap">
				<div class="sol-trust-row">
					<?php foreach ( $trust_items as $t ) : ?>
						<span class="sol-trust-item sol-mono">
							<span class="sol-trust-dot" aria-hidden="true"></span>
							<?php echo esc_html( $t ); ?>
						</span>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     PROBLEM
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section" id="problem" data-track-section="problem">
			<div class="sol-wrap">
				<div class="sol-eyebrow">Der Status Quo</div>
				<h2 class="sol-display sol-problem-h">
					Sie zahlen für <em>Anfragen, die nicht Ihnen gehören</em> — und Vertriebsstunden, die niemand kauft.
				</h2>
				<div class="sol-problem-grid">
					<?php foreach ( $problem_cards as $c ) : ?>
						<article class="sol-problem-card">
							<div class="sol-problem-card-n"><?php echo esc_html( $c['n'] ); ?> · <?php echo esc_html( $c['l'] ); ?></div>
							<div class="sol-problem-card-t"><?php echo esc_html( $c['t'] ); ?></div>
							<p class="sol-problem-card-s"><?php echo esc_html( $c['s'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     METHOD / SYSTEM
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section sol-method" id="system" data-track-section="method">
			<div class="sol-wrap">
				<div class="sol-method-head">
					<div class="sol-eyebrow">Das System</div>
					<h2 class="sol-display sol-method-h">
						Diagnose. Aufbau. Eigentum.<br />Drei Phasen — <em>ein Ergebnis</em>: exklusive Anfragen.
					</h2>
				</div>
				<div class="sol-method-grid">
					<?php foreach ( $method_cards as $card ) : ?>
						<article class="sol-method-card">
							<div class="sol-method-card-top">
								<div class="sol-method-card-n sol-display"><?php echo esc_html( $card['n'] ); ?></div>
								<div class="sol-method-card-pill"><?php echo esc_html( $card['p'] ); ?></div>
							</div>
							<h3 class="sol-method-card-t"><?php echo esc_html( $card['t'] ); ?></h3>
							<p class="sol-method-card-s"><?php echo esc_html( $card['s'] ); ?></p>
							<ul class="sol-method-card-list">
								<?php foreach ( $card['b'] as $b ) : ?>
									<li><span class="sol-method-tick">+</span><?php echo esc_html( $b ); ?></li>
								<?php endforeach; ?>
							</ul>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     RESULTS — E3-Proof + Methodik-Snapshot
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section" id="ergebnisse" data-track-section="results">
			<div class="sol-wrap sol-results-inner">
				<div class="sol-results-text">
					<div class="sol-eyebrow">Was die Diagnose liefert</div>
					<h2 class="sol-display sol-results-h">
						Klarheit, keine <em>Folien</em>.
					</h2>
					<p class="sol-results-sub">
						Vier Module · schriftlicher Befund nach 7 Werktagen · drei priorisierte Hebel · eine Wirtschaftlichkeits-Einordnung — als belastbare Entscheidungsgrundlage, nicht als Pitch-Deck.
					</p>
					<ul class="sol-results-list">
						<?php foreach ( $results_qualifiers as $row ) : ?>
							<li>
								<span><?php echo esc_html( $row['k'] ); ?></span>
								<span class="sol-mono sol-results-list-v"><?php echo esc_html( $row['v'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>

					<a
						class="sol-btn sol-btn-ghost"
						href="<?php echo esc_url( $e3_url ); ?>"
						data-track-action="cta_solar_to_e3_case"
						data-track-category="proof"
						data-track-section="results"
						style="margin-top:32px;"
					>
						Vollständige Methodik im <?php echo esc_html( $e3_case_label ); ?>-Case
						<span class="sol-btn-arrow"><?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</a>
				</div>

				<div class="sol-results-mock" aria-label="<?php echo esc_attr( $e3_case_label ); ?> — Methodik-Snapshot">
					<div class="sol-results-mock-head">
						<div class="sol-results-mock-dots" aria-hidden="true">
							<span></span><span></span><span></span>
						</div>
						<div class="sol-mono sol-results-mock-title"><?php echo esc_html( $e3_case_label ); ?> · Methodik-Snapshot</div>
						<div class="sol-mono sol-results-mock-live">
							<span class="sol-live-dot" aria-hidden="true"></span>
							<?php echo esc_html( $e3_timeframe ); ?>
						</div>
					</div>
					<div class="sol-results-mock-body">
						<div class="sol-results-mock-row">
							<div class="sol-results-mock-stat">
								<div class="sol-mono">Anfragen</div>
								<div class="sol-display sol-results-mock-big"><?php echo esc_html( $e3_lead_count ); ?></div>
								<div class="sol-results-mock-delta">qualifiziert · in <?php echo esc_html( $e3_timeframe_dative ); ?></div>
							</div>
							<div class="sol-results-mock-stat">
								<div class="sol-mono">Abschlussquote</div>
								<div class="sol-display sol-results-mock-big"><?php echo esc_html( $e3_sales_conversion ); ?></div>
								<div class="sol-results-mock-delta">Anfrage → Vertrag</div>
							</div>
							<div class="sol-results-mock-stat">
								<div class="sol-mono">CPL</div>
								<div class="sol-display sol-results-mock-big"><?php echo esc_html( $e3_cpl_after ); ?></div>
								<div class="sol-results-mock-delta">von <?php echo esc_html( $e3_cpl_before ); ?> · <?php echo esc_html( $e3_cpl_reduction ); ?></div>
							</div>
						</div>
						<div class="sol-results-mock-chart" aria-hidden="true">
							<svg viewBox="0 0 400 120" preserveAspectRatio="none" width="100%" height="120">
								<defs>
									<linearGradient id="sol-rg" x1="0" x2="0" y1="0" y2="1">
										<stop offset="0%" stop-color="currentColor" stop-opacity=".25" />
										<stop offset="100%" stop-color="currentColor" stop-opacity="0" />
									</linearGradient>
								</defs>
								<g style="color: var(--sol-accent);">
									<path d="M0 96 L40 90 L80 80 L120 72 L160 66 L200 56 L240 46 L280 38 L320 30 L360 22 L400 16 L400 120 L0 120 Z" fill="url(#sol-rg)" />
									<path d="M0 96 L40 90 L80 80 L120 72 L160 66 L200 56 L240 46 L280 38 L320 30 L360 22 L400 16" fill="none" stroke="currentColor" stroke-width="1.5" />
									<circle cx="40"  cy="90" r="3" fill="currentColor" />
									<circle cx="120" cy="72" r="3" fill="currentColor" />
									<circle cx="200" cy="56" r="3" fill="currentColor" />
									<circle cx="280" cy="38" r="3" fill="currentColor" />
									<circle cx="360" cy="22" r="3" fill="currentColor" />
								</g>
							</svg>
							<div class="sol-results-mock-axis sol-mono">
								<span>Mon 1</span><span>Mon 3</span><span>Mon 5</span><span>Mon 7</span><span>Mon 9</span>
							</div>
						</div>
						<div class="sol-results-mock-list">
							<div class="sol-results-mock-item">
								<span class="sol-results-mock-tag is-new">Neu</span>
								<span>Anfrage-Quellen quantifiziert</span>
								<span class="sol-results-mock-prod sol-mono">Modul 01</span>
								<span class="sol-results-mock-time">7 d</span>
							</div>
							<div class="sol-results-mock-item">
								<span class="sol-results-mock-tag is-call">Befund</span>
								<span>Drei priorisierte Hebel</span>
								<span class="sol-results-mock-prod sol-mono">Output</span>
								<span class="sol-results-mock-time">7 d</span>
							</div>
							<div class="sol-results-mock-item">
								<span class="sol-results-mock-tag is-booked">Übergabe</span>
								<span>30-Min-Call · optional</span>
								<span class="sol-results-mock-prod sol-mono">Wahlfrei</span>
								<span class="sol-results-mock-time">+ 1 Wo</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     GUARANTEE
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section sol-guarantee" id="garantie" data-track-section="guarantee">
			<div class="sol-wrap sol-guarantee-inner">
				<div class="sol-guarantee-glow" aria-hidden="true"></div>
				<div class="sol-eyebrow" style="justify-content:center;">Risiko-Umkehr</div>
				<h2 class="sol-display sol-guarantee-h">
					Drei Hebel — auch wenn wir <em>nicht zusammenarbeiten</em>.
				</h2>
				<p class="sol-guarantee-sub">
					Der Marktcheck ist kein Verkaufsritual. Er ist eine ehrliche Ersteinschätzung. Wenn sich daraus keine Zusammenarbeit ergibt, bekommen Sie trotzdem drei priorisierte Hebel mit konkretem nächstem Schritt — auch dann, wenn das heißt, dass Sie nicht mit mir weitermachen.
				</p>
				<div class="sol-guarantee-points">
					<?php foreach ( $guarantee_points as $p ) : ?>
						<div class="sol-guarantee-point">
							<div class="sol-guarantee-point-mark" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
									<path d="M5 12l5 5L20 7" />
								</svg>
							</div>
							<div>
								<div class="sol-guarantee-point-t"><?php echo esc_html( $p['t'] ); ?></div>
								<div class="sol-guarantee-point-s"><?php echo esc_html( $p['s'] ); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     FAQ
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section sol-faq" id="faq" data-track-section="faq">
			<div class="sol-wrap sol-faq-inner">
				<div class="sol-faq-left">
					<div class="sol-eyebrow">Häufige Fragen</div>
					<h2 class="sol-display sol-faq-h">
						Bevor Sie <em>fragen</em>.
					</h2>
					<p class="sol-faq-sub">
						Was hier nicht beantwortet wird, klären wir im Marktcheck — kurz, schriftlich, ohne Verkaufsgespräch.
					</p>
				</div>
				<ul class="sol-faq-list">
					<?php foreach ( $faq_items as $i => $item ) : ?>
						<li class="sol-faq-item<?php echo 0 === $i ? ' is-open' : ''; ?>">
							<button type="button" class="sol-faq-q" aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>">
								<span class="sol-faq-q-n"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
								<span class="sol-faq-q-t"><?php echo esc_html( $item['question'] ); ?></span>
								<span class="sol-faq-q-mark" aria-hidden="true">
									<span></span><span></span>
								</span>
							</button>
							<div class="sol-faq-a-wrap">
								<div class="sol-faq-a"><?php echo esc_html( $item['answer'] ); ?></div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     FINAL CTA — zurück zum Marktcheck im Hero
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section sol-final" data-track-section="final_cta">
			<div class="sol-wrap">
				<div class="sol-final-inner">
					<div class="sol-final-sun" aria-hidden="true">
						<div class="sol-final-sun-disc"></div>
					</div>
					<div class="sol-eyebrow" style="justify-content:center;">Letzter Schritt</div>
					<h2 class="sol-display sol-final-h">
						Anfragen <em>besitzen</em>,<br />nicht mieten.
					</h2>
					<p class="sol-final-sub">
						5 Fragen · 60 Sekunden · persönliche Antwort in 24 h.
					</p>
					<a
						class="sol-btn sol-btn-primary sol-final-btn"
						href="#marktcheck"
						data-track-action="cta_solar_final_to_marktcheck"
						data-track-category="lead_funnel"
						data-track-section="final_cta"
						data-track-funnel-stage="quiz_open"
					>
						<span>Marktcheck starten</span>
						<span class="sol-btn-arrow"><?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</a>
					<div class="sol-final-micro">Kein Pitch · kein Folien-Deck · konkreter nächster Schritt</div>
				</div>
			</div>
		</section>

		<!-- Sticky Mobile-CTA -->
		<a
			class="sol-sticky-cta"
			href="#marktcheck"
			data-track-action="cta_solar_sticky_to_marktcheck"
			data-track-category="lead_funnel"
			data-track-section="sticky_mobile"
			data-track-funnel-stage="quiz_open"
		>
			<span>Marktcheck starten · 60 Sek</span>
			<span class="sol-sticky-cta-arrow" aria-hidden="true"><?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</a>

	</div><!-- /.solara-landing -->
</main>

<script type="application/ld+json"><?php echo wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>

<?php get_footer(); ?>
