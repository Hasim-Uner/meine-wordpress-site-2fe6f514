<?php
/**
 * Template Name: Whitelabel & Weiterentwicklung
 * Description: Premium Dark-Relaunch der White-Label-Partner-Seite für Agenturen.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$whitelabel_fit_url = function_exists( 'nexus_get_whitelabel_calendar_url' )
	? nexus_get_whitelabel_calendar_url()
	: 'https://cal.com/hasim-uener/whitelabel-fit-gesprach?overlayCalendar=true';
$mailto_url = 'mailto:hallo@hasimuener.de';

$cpl_before     = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before', 'display', '150 €' )      : '150 €';
$cpl_after      = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after',  'display', '22 €' )       : '22 €';
$cpl_reduction  = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_reduction', 'display', '−85 %' )   : '−85 %';
$lead_count     = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'lead_count', 'display', '1.750+' )     : '1.750+';
$sales_conv     = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'sales_conversion', 'display', '12 %' ) : '12 %';
$timeframe      = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'timeframe', 'display', '6 Monate' )    : '6 Monate';

$cpl_before_int    = function_exists( 'hu_e3_metric' ) ? (int) hu_e3_metric( 'cpl_before', 'counter_target', 150 ) : 150;
$cpl_after_int     = function_exists( 'hu_e3_metric' ) ? (int) hu_e3_metric( 'cpl_after',  'counter_target', 22 )  : 22;
$lead_count_int    = function_exists( 'hu_e3_metric' ) ? (int) hu_e3_metric( 'lead_count', 'counter_target', 1750 ) : 1750;
$sales_conv_int    = function_exists( 'hu_e3_metric' ) ? (int) hu_e3_metric( 'sales_conversion', 'counter_target', 12 ) : 12;
$cpl_reduction_int = function_exists( 'hu_e3_metric' ) ? (int) hu_e3_metric( 'cpl_reduction', 'counter_target', 85 ) : 85;

$problem_cards = [
	[
		'eyebrow' => 'Zeit',
		'title'   => 'Kalender voll, Pipeline auch.',
		'copy'    => 'Eure Kapazität ist auf Akquise, Strategie und Kunden gebunden. Die technische Umsetzung rutscht in den Abend oder verzögert das Projekt.',
	],
	[
		'eyebrow' => 'Technische Tiefe',
		'title'   => 'Server-Side, Consent V2, Core Web Vitals.',
		'copy'    => 'Themen, die euch Kunden bezahlen — die intern aber niemand sauber liefert. Halbgare Setups werden zur Reklamation, nicht zur Referenz.',
	],
	[
		'eyebrow' => 'Umsetzungskapazität',
		'title'   => 'Senior-Stunden fehlen.',
		'copy'    => 'Junioren brauchen Aufsicht, Freelancer brauchen Briefing, Agenturen-Backups brauchen Marge. Ihr braucht jemanden, der einfach liefert.',
	],
];

$solution_steps = [
	[
		'step'  => '01',
		'title' => 'Ihr briefed',
		'copy'  => 'Kurzes Setup-Gespräch mit eurem Projektleiter. NDA möglich. Keine direkte Kundenansprache.',
	],
	[
		'step'  => '02',
		'title' => 'Ich liefere',
		'copy'  => 'Umsetzung im Hintergrund: Code, Tracking, Doku. Kommunikation läuft ausschließlich über euch.',
	],
	[
		'step'  => '03',
		'title' => 'Ihr präsentiert',
		'copy'  => 'Übergabe unter eurem Branding. Reports, Code-Doku und Übergabe-Files passend für euren Kunden.',
	],
];

$stack_cards = [
	[
		'tag'    => 'SEO',
		'title'  => 'Technical SEO',
		'copy'   => 'Audits, Indexierung, Logfile-Analysen, Schema, hreflang, internationale Architektur, Cluster-Strukturen.',
		'chips'  => [ 'Audit', 'Schema', 'hreflang', 'Logfiles' ],
	],
	[
		'tag'    => 'WP',
		'title'  => 'WordPress + Core Web Vitals',
		'copy'   => 'Theme- und Plugin-Hardening, bedarfsgesteuertes Asset-Loading, Caching, kritischer Renderpfad, eigene Templates.',
		'chips'  => [ 'CWV', 'Caching', 'Hardening', 'Templates' ],
	],
	[
		'tag'    => 'Analytics',
		'title'  => 'GA4 + GTM',
		'copy'   => 'Sauberes Mess-Setup: Events, eCommerce, Funnel-Reports, klare Datenstrecke vom Klick bis zum Lead.',
		'chips'  => [ 'GA4', 'GTM', 'Events', 'Reports' ],
	],
	[
		'tag'    => 'Tracking',
		'title'  => 'Server-Side + CAPI',
		'copy'   => 'Eigener Subdomain-Container (Stape/GCP), Enhanced Conversions, Meta CAPI, TikTok Events API, Consent Mode V2.',
		'chips'  => [ 'GTM-SS', 'Consent V2', 'CAPI', 'Enhanced' ],
	],
	[
		'tag'    => 'CRO',
		'title'  => 'Landingpages',
		'copy'   => 'Konvertierende Templates statt Page-Builder-Brei. A/B-Hypothesen, klare Funnel-Logik, schnelle Ladezeit.',
		'chips'  => [ 'LP', 'A/B', 'Funnel', 'Speed' ],
	],
	[
		'tag'    => 'Ops',
		'title'  => 'Automation & CRM',
		'copy'   => 'n8n, Make, Zapier. CRM-Attribution: Bitrix24, HubSpot, Pipedrive. End-to-End-Lead-Strecke bis zum Abschluss.',
		'chips'  => [ 'n8n', 'Make', 'CRM', 'Attribution' ],
	],
];

$contract_cards = [
	[
		'eyebrow' => '01',
		'title'   => 'Unsichtbar',
		'copy'    => 'NDA Standard. Keine Ansprache eurer Kunden, kein Branding in Reports. Kommunikation ausschließlich über euren Projektleiter.',
		'bullets' => [ 'NDA inkludiert', 'Kein Branding', 'Keine Kunden-DM' ],
	],
	[
		'eyebrow' => '02',
		'title'   => 'Schnell',
		'copy'    => 'Onboarding in unter 14 Tagen. Reaktionszeit unter 4 Stunden (werktags). Wöchentliches Delivery-Fenster, klare Abnahme.',
		'bullets' => [ '< 14 Tage Onboarding', '< 4 h Reaktion', 'Wöchentlich Delivery' ],
	],
	[
		'eyebrow' => '03',
		'title'   => 'Planbar',
		'copy'    => 'Monats-Retainer mit festem Stundenkontingent oder feste Tagessätze für Projektarbeit. Saubere Doku statt Black Box.',
		'bullets' => [ 'Feste Tagessätze', 'Klare Doku', 'Keine Surprise-Rechnung' ],
	],
];

$tech_bullets = [
	'Eigene WordPress-Templates statt generischer Page-Builder',
	'Bedarfsgesteuertes Asset-Loading pro Template',
	'Theme- und Plugin-Hardening: Sicherheit, Update-Disziplin',
	'Performance-Optimierung: Core Web Vitals, kritischer Renderpfad',
	'Saubere Schema-Architektur, zentral gepflegt',
	'Versionierter Code, dokumentierte Übergabe',
];

$entry_bullets = [ 'NDA', 'Fixer Scope', 'Klare Doku', 'Keine Verlängerungsfalle' ];

$hero_chips = [ 'GA4', 'GTM', 'Server-Side', 'Consent V2', 'WordPress', 'n8n' ];
?>

<main id="main" class="site-main wl-page" data-track-section="whitelabel_proof">

	<!-- ═══════════════════════════════════════════════
	     SECTION 01 — HERO (dark, KPI-Dashboard rechts)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-hero" data-nx-theme="dark" id="hero">
		<div class="wl-hero__bg" aria-hidden="true">
			<div class="wl-hero__bg-warmth"></div>
			<div class="wl-hero__bg-vignette"></div>
			<div class="wl-hero__bg-grid"></div>
		</div>

		<div class="nx-container">
			<header class="wl-hero__top">
				<span class="wl-hero__mark">
					<span class="wl-hero__mark-rule" aria-hidden="true"></span>
					White-Label · Partner im Hintergrund
				</span>
				<span class="wl-hero__status">
					<span class="wl-status-dot" aria-hidden="true"></span>
					Aktive Whitelabel-Mandate
				</span>
			</header>

			<div class="wl-hero__grid">
				<div class="wl-hero__copy">
					<h1 class="wl-hero__title">
						<span class="wl-hero__title-line">White-Label SEO, WordPress &amp; Tracking</span>
						<span class="wl-hero__title-line wl-hero__title-line--em">für Agenturen, die sauber liefern wollen.</span>
					</h1>
					<p class="wl-hero__lede">
						Ich unterstütze euch im Hintergrund bei technischer SEO, WordPress-Performance, Tracking-Setups und conversionstarken Landingpages — ohne Kundenzugriff, ohne Branding, ohne Theater.
					</p>

					<div class="wl-hero__actions">
						<a href="<?php echo esc_url( $whitelabel_fit_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_whitelabel_hero_fit_call" data-track-category="lead_gen" data-track-section="hero">
							<span>White-Label-Gespräch buchen</span>
							<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
								<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</a>
						<a href="#proof" class="nx-btn nx-btn--ghost" data-track-action="cta_whitelabel_hero_proof" data-track-category="navigation" data-track-section="hero">
							Live-Beleg ansehen
						</a>
					</div>

					<p class="wl-hero__fineprint">
						30&nbsp;Min · NDA möglich · keine Verkaufsshow.
					</p>
				</div>

				<aside class="wl-dash" aria-label="Whitelabel-Dashboard, Beispiel-Kennzahlen">
					<header class="wl-dash__head">
						<span class="wl-dash__head-left">
							<span class="wl-dash__dot" aria-hidden="true"></span>
							Live · Whitelabel-Mandat
						</span>
						<span class="wl-dash__head-right">CPL-Verlauf</span>
					</header>

					<div class="wl-dash__primary">
						<span class="wl-dash__primary-label">Kosten pro qualifizierter Anfrage</span>
						<div class="wl-dash__primary-row">
							<span class="wl-dash__primary-before"><s><?php echo esc_html( $cpl_before ); ?></s></span>
							<span class="wl-dash__primary-arrow" aria-hidden="true">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M4 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
							<span class="wl-dash__primary-after">
								<span class="wl-counter" data-counter-target="<?php echo esc_attr( $cpl_after_int ); ?>" data-counter-suffix="&nbsp;€"><?php echo esc_html( $cpl_after ); ?></span>
							</span>
						</div>
						<span class="wl-dash__primary-delta">
							<span class="wl-counter" data-counter-target="<?php echo esc_attr( $cpl_reduction_int ); ?>" data-counter-prefix="−" data-counter-suffix="&nbsp;%"><?php echo esc_html( $cpl_reduction ); ?></span>
							<span>&nbsp;in <?php echo esc_html( $timeframe ); ?></span>
						</span>
					</div>

					<div class="wl-dash__spark" aria-hidden="true">
						<svg viewBox="0 0 320 70" preserveAspectRatio="none" width="100%" height="60">
							<defs>
								<linearGradient id="wl-spark-fill" x1="0" x2="0" y1="0" y2="1">
									<stop offset="0%" stop-color="hsl(23 50% 47% / 0.45)"/>
									<stop offset="100%" stop-color="hsl(23 50% 47% / 0)"/>
								</linearGradient>
							</defs>
							<path d="M0,12 L32,18 L64,16 L96,28 L128,34 L160,40 L192,46 L224,52 L256,56 L288,60 L320,62 L320,70 L0,70 Z" fill="url(#wl-spark-fill)"/>
							<path d="M0,12 L32,18 L64,16 L96,28 L128,34 L160,40 L192,46 L224,52 L256,56 L288,60 L320,62" fill="none" stroke="hsl(23 50% 54%)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</div>

					<dl class="wl-dash__tiles">
						<div class="wl-dash__tile">
							<dt>Qualifizierte Leads</dt>
							<dd><span class="wl-counter" data-counter-target="<?php echo esc_attr( $lead_count_int ); ?>" data-counter-suffix="+"><?php echo esc_html( $lead_count ); ?></span></dd>
						</div>
						<div class="wl-dash__tile">
							<dt>CPL-Senkung</dt>
							<dd><span class="wl-counter" data-counter-target="<?php echo esc_attr( $cpl_reduction_int ); ?>" data-counter-prefix="−" data-counter-suffix=" %"><?php echo esc_html( $cpl_reduction ); ?></span></dd>
						</div>
						<div class="wl-dash__tile">
							<dt>Zeitraum</dt>
							<dd><span class="wl-counter" data-counter-target="9" data-counter-suffix=" Mon.">9 Mon.</span></dd>
						</div>
						<div class="wl-dash__tile">
							<dt>Abschlussquote</dt>
							<dd><span class="wl-counter" data-counter-target="<?php echo esc_attr( $sales_conv_int ); ?>" data-counter-suffix=" %"><?php echo esc_html( $sales_conv ); ?></span></dd>
						</div>
					</dl>

					<div class="wl-dash__chips" role="list" aria-label="Stack-Komponenten">
						<?php foreach ( $hero_chips as $chip ) : ?>
							<span class="wl-chip" role="listitem"><?php echo esc_html( $chip ); ?></span>
						<?php endforeach; ?>
					</div>
				</aside>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 02 — PROBLEM
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-problem" id="problem">
		<div class="nx-container">
			<div class="wl-section-header">
				<span class="wl-eyebrow">Problem</span>
				<h2 class="nx-headline-section">Ihr verkauft SEO, Tracking oder Websites — und intern fehlt die Tiefe.</h2>
				<p class="wl-section-lede">Drei Engpässe tauchen bei fast jeder Performance- und Webdesign-Agentur auf. Keiner davon ist eure Schuld — alle drei sind lösbar, ohne dass ihr eigenes Personal hochziehen müsst.</p>
			</div>

			<div class="wl-problem__grid">
				<?php foreach ( $problem_cards as $card ) : ?>
					<article class="wl-problem-card">
						<span class="wl-eyebrow"><?php echo esc_html( $card['eyebrow'] ); ?></span>
						<h3 class="wl-problem-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p class="wl-problem-card__copy"><?php echo esc_html( $card['copy'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 03 — LÖSUNG (Solution Flow)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-solution" data-nx-theme="dark" id="loesung">
		<div class="nx-container">
			<div class="wl-section-header wl-section-header--center">
				<span class="wl-eyebrow">Lösung</span>
				<h2 class="nx-headline-section">Ich docke an. Ihr liefert weiter unter eurem Namen.</h2>
			</div>

			<ol class="wl-solution__flow" aria-label="Drei-Schritt-Prozess">
				<?php foreach ( $solution_steps as $step ) : ?>
					<li class="wl-solution__step">
						<span class="wl-solution__num"><?php echo esc_html( $step['step'] ); ?></span>
						<h3 class="wl-solution__title"><?php echo esc_html( $step['title'] ); ?></h3>
						<p class="wl-solution__copy"><?php echo esc_html( $step['copy'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 04 — STACK / LEISTUNGEN
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-stack" id="leistungen">
		<div class="nx-container">
			<div class="wl-section-header">
				<span class="wl-eyebrow">Stack · Leistungen</span>
				<h2 class="nx-headline-section">Was ich für euch im Hintergrund baue.</h2>
				<p class="wl-section-lede">Sechs Bereiche, alle senior umgesetzt. Kombinierbar zum Retainer, einzeln als Projekt buchbar.</p>
			</div>

			<div class="wl-stack__grid">
				<?php foreach ( $stack_cards as $card ) : ?>
					<article class="wl-stack-card">
						<header class="wl-stack-card__head">
							<span class="wl-stack-card__tag"><?php echo esc_html( $card['tag'] ); ?></span>
						</header>
						<h3 class="wl-stack-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p class="wl-stack-card__copy"><?php echo esc_html( $card['copy'] ); ?></p>
						<ul class="wl-stack-card__chips" aria-label="Komponenten">
							<?php foreach ( $card['chips'] as $chip ) : ?>
								<li><span class="wl-chip wl-chip--mono"><?php echo esc_html( $chip ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 05 — KONTRAKT / SICHERHEIT
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-contract" id="kontrakt">
		<div class="nx-container">
			<div class="wl-section-header">
				<span class="wl-eyebrow">Whitelabel-Kontrakt</span>
				<h2 class="nx-headline-section">Unsichtbar. Schnell. Planbar.</h2>
				<p class="wl-section-lede">Drei Regeln, die ich von der ersten Minute an einhalte. Schriftlich, prüfbar, ohne Sternchen.</p>
			</div>

			<div class="wl-contract__grid">
				<?php foreach ( $contract_cards as $card ) : ?>
					<article class="wl-contract-card">
						<span class="wl-contract-card__num"><?php echo esc_html( $card['eyebrow'] ); ?></span>
						<h3 class="wl-contract-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p class="wl-contract-card__copy"><?php echo esc_html( $card['copy'] ); ?></p>
						<ul class="wl-contract-card__bullets">
							<?php foreach ( $card['bullets'] as $bullet ) : ?>
								<li><?php echo esc_html( $bullet ); ?></li>
							<?php endforeach; ?>
						</ul>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 06 — PROOF (Live-Beleg)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-proof" data-nx-theme="dark" id="proof">
		<div class="nx-container">
			<div class="wl-section-header">
				<span class="wl-eyebrow">Live-Beleg · laufendes Whitelabel-Mandat</span>
				<h2 class="nx-headline-section">Ergebnis aus einem aktiven Whitelabel-Mandat.</h2>
				<p class="wl-section-lede">Nische: erneuerbare Energien. Hebel: Server-Side, Consent Mode V2, CRM-Attribution. Geliefert im Namen der Agentur — ohne Sichtbarkeit nach außen.</p>
			</div>

			<div class="wl-proof__grid" role="list" aria-label="Proof-Kennzahlen">
				<div class="wl-proof__item" role="listitem">
					<div class="wl-proof__value"><?php echo esc_html( $cpl_before ); ?>&nbsp;→&nbsp;<?php echo esc_html( $cpl_after ); ?></div>
					<div class="wl-proof__label">Kosten pro Anfrage</div>
				</div>
				<div class="wl-proof__item" role="listitem">
					<div class="wl-proof__value"><?php echo esc_html( $lead_count ); ?></div>
					<div class="wl-proof__label">Qualifizierte Anfragen</div>
				</div>
				<div class="wl-proof__item" role="listitem">
					<div class="wl-proof__value"><?php echo esc_html( $cpl_reduction ); ?></div>
					<div class="wl-proof__label">Niedrigere Kosten pro Anfrage</div>
				</div>
				<div class="wl-proof__item" role="listitem">
					<div class="wl-proof__value"><?php echo esc_html( $sales_conv ); ?></div>
					<div class="wl-proof__label">Abschlussquote</div>
				</div>
			</div>

			<p class="wl-proof__disclaimer">Stand 2024–2025 · keine pauschale Übertragbarkeitsgarantie.</p>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 07 — TECHNISCHER BELEG
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-tech" id="technik">
		<div class="nx-container">
			<div class="wl-section-header">
				<span class="wl-eyebrow">Technischer Beleg</span>
				<h2 class="nx-headline-section">Sauberer Code statt Plugin-Stack.</h2>
				<p class="wl-section-lede">Eigene Templates, bedarfsgesteuertes Asset-Loading, dokumentierte Übergabe. Keine Page-Builder-Wand, kein generisches Theme-Bloat.</p>
			</div>

			<div class="wl-tech__split">
				<ul class="wl-tech__bullets">
					<?php foreach ( $tech_bullets as $bullet ) : ?>
						<li><?php echo esc_html( $bullet ); ?></li>
					<?php endforeach; ?>
				</ul>

				<figure class="wl-tech__code" aria-hidden="true">
					<figcaption class="wl-tech__code-head">
						<span class="wl-tech__code-dot wl-tech__code-dot--r"></span>
						<span class="wl-tech__code-dot wl-tech__code-dot--y"></span>
						<span class="wl-tech__code-dot wl-tech__code-dot--g"></span>
						<span class="wl-tech__code-file">inc/enqueue.php</span>
					</figcaption>
<pre class="wl-tech__code-body"><span class="wl-tech__c">// Bedarfsgesteuertes Asset-Loading pro Template</span>
<span class="wl-tech__k">if</span> ( is_page_template( <span class="wl-tech__s">'page-whitelabel.php'</span> ) ) {
    hu_enqueue_css( <span class="wl-tech__s">'whitelabel'</span>, <span class="wl-tech__s">'whitelabel.css'</span>, [ <span class="wl-tech__s">'design-system'</span> ] );
    hu_enqueue_js(  <span class="wl-tech__s">'wl-counters'</span>,  <span class="wl-tech__s">'wl-counters.js'</span> );
}

<span class="wl-tech__c">// Server-Side Event auf Lead-Submit</span>
window.dataLayer.push({
    <span class="wl-tech__p">event</span>: <span class="wl-tech__s">'lead_qualified'</span>,
    <span class="wl-tech__p">source</span>: <span class="wl-tech__s">'whitelabel_partner'</span>,
    <span class="wl-tech__p">value</span>: leadValue
});</pre>
				</figure>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 08 — EINSTIEG (Testprojekt-Scope)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-entry" id="einstieg">
		<div class="nx-container">
			<div class="wl-section-header">
				<span class="wl-eyebrow">Einstieg</span>
				<h2 class="nx-headline-section">Kein sofortiger Retainer. Erst ein kleines Testprojekt.</h2>
			</div>

			<article class="wl-entry__card">
				<header class="wl-entry__head">
					<span class="wl-entry__kicker">Scope · Testprojekt</span>
					<h3 class="wl-entry__title">Ein klar umrissenes Mini-Projekt, bevor irgendwer „Retainer" sagt.</h3>
				</header>
				<p class="wl-entry__copy">
					Wir starten mit einem fixen, abgegrenzten Scope — typischerweise ein Tracking-Audit, ein Server-Side-Setup oder eine Landingpage. 1–2&nbsp;Wochen Laufzeit, NDA, fixe Lieferung. Erst danach entscheidet ihr, ob daraus ein Retainer wird.
				</p>
				<ul class="wl-entry__bullets" aria-label="Rahmen Testprojekt">
					<?php foreach ( $entry_bullets as $bullet ) : ?>
						<li><span class="wl-entry__bullet-dot" aria-hidden="true"></span><?php echo esc_html( $bullet ); ?></li>
					<?php endforeach; ?>
				</ul>
				<div class="wl-entry__actions">
					<a href="<?php echo esc_url( $whitelabel_fit_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_whitelabel_entry_fit_call" data-track-category="lead_gen" data-track-section="entry">
						Testprojekt besprechen
					</a>
				</div>
			</article>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 09 — FINAL CTA
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-cta" data-nx-theme="dark" id="cta">
		<div class="wl-cta__bg" aria-hidden="true">
			<div class="wl-hero__bg-warmth"></div>
			<div class="wl-hero__bg-vignette"></div>
		</div>
		<div class="nx-container">
			<div class="wl-cta__shell">
				<div class="wl-cta__copy">
					<span class="wl-eyebrow">Nächster Schritt</span>
					<h2 class="wl-cta__title">Passt das zu eurem Setup?</h2>
					<p class="wl-cta__lede">30&nbsp;Min, kein Pitch-Deck, keine Verkaufsshow. Wir prüfen, ob ich technisch und vertraglich zu eurer Agentur passe.</p>
				</div>
				<div class="wl-cta__actions">
					<a href="<?php echo esc_url( $whitelabel_fit_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_whitelabel_footer_fit_call" data-track-category="lead_gen" data-track-section="cta">
						White-Label-Gespräch buchen
					</a>
					<a href="<?php echo esc_url( $mailto_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_whitelabel_footer_mail" data-track-category="contact" data-track-section="cta">
						hallo@hasimuener.de
					</a>
				</div>
			</div>
		</div>
	</section>

	<!-- Sticky Mobile CTA -->
	<div class="wl-sticky-cta" id="wl-sticky-cta" aria-hidden="true">
		<div class="wl-sticky-cta__inner">
			<div class="wl-sticky-cta__label">
				<strong>White-Label-Partner</strong>
				<span>30&nbsp;Min Fit-Gespräch · NDA möglich</span>
			</div>
			<a href="<?php echo esc_url( $whitelabel_fit_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_sticky_whitelabel_fit_call" data-track-category="lead_gen" data-track-section="sticky_mobile">
				Gespräch buchen
			</a>
		</div>
	</div>

</main>

<script>
(function () {
	// ─── Sticky Mobile CTA visibility ───
	var sticky = document.getElementById('wl-sticky-cta');
	var hero   = document.getElementById('hero');
	var cta    = document.getElementById('cta');
	if (sticky && hero) {
		function update() {
			var heroBottom = hero.getBoundingClientRect().bottom;
			var ctaTop     = cta ? cta.getBoundingClientRect().top : Infinity;
			var shouldShow = heroBottom < 0 && ctaTop > window.innerHeight - 80;
			sticky.classList.toggle('is-visible', shouldShow);
			sticky.setAttribute('aria-hidden', shouldShow ? 'false' : 'true');
			document.body.classList.toggle('has-sticky-wl-cta', shouldShow);
		}
		window.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
		update();
	}

	// ─── Animated Counters ───
	var counters = document.querySelectorAll('.wl-counter');
	if (!counters.length) return;
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function format(n, target) {
		var s = String(Math.round(n));
		if (target >= 1000) s = s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
		return s;
	}

	function run(el) {
		var target = parseInt(el.getAttribute('data-counter-target') || '0', 10);
		var suffix = el.getAttribute('data-counter-suffix') || '';
		var prefix = el.getAttribute('data-counter-prefix') || '';
		if (reduceMotion || !('requestAnimationFrame' in window)) {
			el.innerHTML = prefix + format(target, target) + suffix;
			return;
		}
		var duration = 1400;
		var start = null;
		function tick(t) {
			if (start === null) start = t;
			var p = Math.min(1, (t - start) / duration);
			var eased = 1 - Math.pow(1 - p, 3);
			el.innerHTML = prefix + format(target * eased, target) + suffix;
			if (p < 1) requestAnimationFrame(tick);
			else el.innerHTML = prefix + format(target, target) + suffix;
		}
		requestAnimationFrame(tick);
	}

	if (!('IntersectionObserver' in window)) {
		counters.forEach(run);
		return;
	}
	var io = new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (entry.isIntersecting) {
				run(entry.target);
				io.unobserve(entry.target);
			}
		});
	}, { threshold: 0.2 });
	counters.forEach(function (el) { io.observe(el); });
})();
</script>

<?php get_footer(); ?>
