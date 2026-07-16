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

$e3_case_url  = function_exists( 'hu_e3_canon' ) ? (string) ( hu_e3_canon()['url'] ?? home_url( '/case-study-solar-leadgenerierung/' ) ) : home_url( '/case-study-solar-leadgenerierung/' );
$portrait_url = function_exists( 'hu_get_portrait_image_url' )
	? hu_get_portrait_image_url()
	: home_url( '/wp-content/uploads/2026/01/Hasim-Uener-Prtraeit_Startseite.webp' );

$cpl_before     = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before', 'display', '150 €' )      : '150 €';
$cpl_after      = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after',  'display', '22 €' )       : '22 €';
$cpl_reduction  = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_reduction', 'display', '−85 %' )   : '−85 %';
$lead_count     = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'lead_count', 'display', '1.750+' )     : '1.750+';
$sales_conv     = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'sales_conversion', 'display', '12 %' ) : '12 %';
$timeframe      = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'timeframe', 'display_dative', '6 Monaten' ) : '6 Monaten';
$roas           = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'roas', 'display', '34×' )              : '34×';

$cpl_after_int     = function_exists( 'hu_e3_metric' ) ? (int) hu_e3_metric( 'cpl_after',  'counter_target', 22 )  : 22;
$cpl_reduction_int = function_exists( 'hu_e3_metric' ) ? (int) hu_e3_metric( 'cpl_reduction', 'counter_target', 85 ) : 85;
$lead_count_int    = function_exists( 'hu_e3_metric' ) ? (int) hu_e3_metric( 'lead_count', 'counter_target', '1750' ) : 1750;
$roas_int          = function_exists( 'hu_e3_metric' ) ? (int) hu_e3_metric( 'roas', 'counter_target', '34' )         : 34;
$sales_conv_int    = function_exists( 'hu_e3_metric' ) ? (int) hu_e3_metric( 'sales_conversion', 'counter_target', '12' ) : 12;

$problem_cards = [
	[
		'eyebrow' => 'Kapazität',
		'title'   => 'Deadline steht. Team ist voll.',
		'copy'    => 'Akquise, Strategie, Kundentermine — euer Kalender ist dicht. Die technische Umsetzung rutscht in den Abend, und die Deadline rückt trotzdem näher.',
	],
	[
		'eyebrow' => 'Tiefe',
		'title'   => 'Der Kunde zahlt Senior. Intern liefert der Junior.',
		'copy'    => 'Server-Side, Consent Mode V2, Core Web Vitals: verkauft als Expertise, gebaut auf Anschlag. Halbgare Setups kommen als Reklamation zurück — nicht als Referenz.',
	],
	[
		'eyebrow' => 'Marge',
		'title'   => 'Senior einstellen frisst die Marge.',
		'copy'    => 'Ein Senior kostet Gehalt, Suche und Auslastungsrisiko. Freelancer kosten Briefing und Nerven. Whitelabel dreht die Rechnung: Ihr verkauft zu eurem Satz, die Umsetzung bleibt darunter.',
	],
];

$solution_modes = [
	'hintergrund' => [
		'label' => 'Im Hintergrund',
		'copy'  => 'Komplett unsichtbar. Code, Tracking, Doku laufen über euch — euer Ton, euer Branding. Euer Kunde sieht nur das Ergebnis.',
	],
	'kundencall'  => [
		'label' => 'Mit im Kunden-Call',
		'copy'  => 'Als euer Senior-Tech-Lead direkt im Call. Technikfragen sofort beantwortet, ohne Übersetzungsschleife — der Kunde hört Kompetenz, ihr erntet sie.',
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
		'title'   => 'Diskret',
		'copy'    => 'NDA standardmäßig, kein eigenes Branding, keine Akquise in eurem Kundenstamm. Die Sichtbarkeit legt ihr fest — vom unsichtbaren Backoffice bis zum Technik-Lead in eurem Call.',
		'bullets' => [ 'NDA inkludiert', 'Kein eigenes Branding', 'Sichtbarkeit: ihr entscheidet' ],
	],
	[
		'eyebrow' => '02',
		'title'   => 'Schnell',
		'copy'    => 'Onboarding in unter 14 Tagen, Reaktion in unter 4 Stunden (werktags). Und wenn ein Projekt brennt: Eskalation ist eingepreist, kein Sonderfall.',
		'bullets' => [ '< 14 Tage Onboarding', '< 4 h Reaktion', 'Eskalation eingepreist' ],
	],
	[
		'eyebrow' => '03',
		'title'   => 'Planbar',
		'copy'    => 'Monats-Retainer mit festem Stundenkontingent oder feste Tagessätze für Projektarbeit. Saubere Doku statt Black Box.',
		'bullets' => [ 'Feste Tagessätze', 'Klare Doku', 'Keine Surprise-Rechnung' ],
	],
];

$comparison_columns = [ 'Senior einstellen', 'Freelancer-Pool', 'White-Label-Partner' ];

$comparison_rows = [
	[
		'label' => 'Verfügbar ab',
		'cells' => [
			'Monate: Suche, Kündigungsfrist, Einarbeitung',
			'Tage bis Wochen — wenn gerade jemand frei ist',
			'Nach einem Fit-Gespräch und NDA',
		],
	],
	[
		'label' => 'Fixkostenrisiko',
		'cells' => [
			'Volles Gehalt, auch in schwachen Monaten',
			'Keins — aber auch keine Verbindlichkeit',
			'Retainer oder Tagessatz — skaliert mit eurer Auslastung',
		],
	],
	[
		'label' => 'Skill-Breite',
		'cells' => [
			'Eine Person, ein Schwerpunkt',
			'Pro Skill ein neues Briefing',
			'SEO, WordPress, Tracking, CRO, Automation aus einer Hand',
		],
	],
	[
		'label' => 'Qualität',
		'cells' => [
			'Zeigt sich nach der Probezeit',
			'Wechselnde Standards, wechselnde Doku',
			'Ein Standard — prüfbar am Erstprojekt',
		],
	],
	[
		'label' => 'Diskretion',
		'cells' => [
			'Intern — Know-how geht mit der Person',
			'Oft ungeklärt, selten mit NDA',
			'NDA standardmäßig, Sichtbarkeit wählbar',
		],
	],
	[
		'label' => 'Verantwortung',
		'cells' => [
			'Führung und QA liegen bei euch',
			'Koordination liegt bei euch',
			'Lieferung inklusive Doku und Abnahme',
		],
	],
];

$founder_chips = [ 'Hannover', 'NDA standardmäßig', 'Antwort < 4 h werktags' ];

$faq_items = [
	[
		'key' => 'abrechnung',
		'q'   => 'Wie rechnet ihr ab — Retainer oder Projekt?',
		'a'   => 'Beides: Monats-Retainer mit festem Stundenkontingent für laufende Arbeit oder feste Tagessätze für abgegrenzte Projekte. Konkrete Konditionen klären wir im Fit-Gespräch — abhängig von Volumen und Reaktionszeit. Jede Position ist dokumentiert, keine Surprise-Rechnung.',
	],
	[
		'key' => 'sichtbarkeit',
		'q'   => 'Was passiert, wenn unser Kunde fragt, wer die Technik macht?',
		'a'   => 'Das legt ihr vorab fest. Standard: Backoffice — ihr antwortet als Team. Auf Wunsch stellt ihr euren Technik-Lead vor, und die Fragen werden direkt im Call beantwortet, unter eurem Branding. Was nie passiert: Akquise in eurem Kundenstamm. Das steht im Kontrakt.',
	],
	[
		'key' => 'pitch',
		'q'   => 'Gibt es Support schon im Pitch — bevor der Kunde unterschrieben hat?',
		'a'   => 'Ja. Scope, Aufwandsschätzung, technische Machbarkeit — ihr bekommt belastbare Zahlen für euer Angebot, bevor ihr etwas verkauft, das nachher niemand bauen kann. Den Rahmen dafür klären wir im Fit-Gespräch.',
	],
	[
		'key' => 'zugaenge',
		'q'   => 'Wie laufen Zugänge zu GA4, GTM, Server & Co.?',
		'a'   => 'Über eure Accounts — nie andersherum. Ihr vergebt den Zugang (eigene E-Mail, 2FA) und entzieht ihn, wann ihr wollt. Properties, Container und Server-Setups gehören euch oder eurem Kunden. Gebaut wird darin, nicht daneben.',
	],
	[
		'key' => 'kapazitaet',
		'q'   => 'Wie schnell reagierst du — und was ist mit Kapazität?',
		'a'   => 'Reaktionszeit unter 4 Stunden werktags, wöchentliches Delivery-Fenster, klare Abnahme. Wenige Mandate parallel statt Junior-Team dazwischen — deshalb bleibt die Qualität konstant. Und wenn die Kapazität nicht reicht, hört ihr das im Fit-Gespräch, nicht drei Wochen nach Kickoff.',
	],
	[
		'key' => 'ownership',
		'q'   => 'Wem gehören Code, Setups und Daten?',
		'a'   => 'Euch beziehungsweise eurem Kunden. Versionierter Code, dokumentierte Übergabe, Zugänge in euren Accounts. Es gibt keine Black Box und keinen Lock-in — das ist Absicht.',
	],
	[
		'key' => 'exit',
		'q'   => 'Wie kommen wir wieder raus?',
		'a'   => 'Projekte enden mit der Abnahme, Retainer ohne Verlängerungsfalle. Durch Doku und Ownership in euren Accounts könnt ihr jederzeit intern übernehmen oder wechseln. Partner bleiben, weil die Lieferung stimmt — nicht, weil der Ausstieg wehtut.',
	],
	[
		'key' => 'start',
		'q'   => 'Wie schnell können wir starten?',
		'a'   => 'Onboarding in unter 14 Tagen — so steht es im Kontrakt. Nach dem Fit-Gespräch: NDA, Zugänge, Erstprojekt mit fixem Scope. Typisch 1–2 Wochen Laufzeit, danach entscheidet ihr über mehr.',
	],
	[
		'key' => 'recht',
		'q'   => 'Arbeitest du als Subunternehmer — und wie sauber ist das rechtlich?',
		'a'   => 'Ja, klassisches Subunternehmer-Verhältnis: Vertrag mit eurer Agentur, nicht mit eurem Kunden. NDA gehört zum Standard, Auftragsverarbeitung nach DSGVO, wo personenbezogene Daten im Spiel sind. Rechnung an euch — eure Marge bleibt eure Sache.',
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

	<noscript>
		<style>
			.wl-page .nx-reveal,
			.wl-page .reveal-stagger > * { opacity: 1; transform: none; }
		</style>
	</noscript>

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
					White-Label-Partner für Agenturen
				</span>
				<span class="wl-hero__status">
					<span class="wl-status-dot" aria-hidden="true"></span>
					Aktive Whitelabel-Mandate
				</span>
			</header>

			<div class="wl-hero__grid">
				<div class="wl-hero__copy">
					<h1 class="wl-hero__title">
						<span class="wl-hero__title-line">Ihr verkauft es.</span>
						<span class="wl-hero__title-line">Ich liefere es.</span>
						<span class="wl-hero__title-line wl-hero__title-line--em">Euer Name steht drauf.</span>
					</h1>
					<p class="wl-hero__lede">
						Technical SEO, Server-Side-Tracking, WordPress &amp; Core Web Vitals, Landingpages — senior umgesetzt, unter eurem Branding, zu eurer Marge. Unsichtbar im Hintergrund oder als euer Technik-Lead im Kunden-Call: Ihr entscheidet, pro Projekt.
					</p>

					<div class="wl-hero__actions">
						<a href="<?php echo esc_url( $whitelabel_fit_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_whitelabel_hero_fit_call" data-track-category="lead_gen" data-track-section="hero">
							<span>White-Label-Gespräch buchen</span>
							<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
								<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</a>
						<a href="#proof" class="nx-btn nx-btn--ghost" data-track-action="cta_whitelabel_hero_proof" data-track-category="navigation" data-track-section="hero">
							Case &amp; Zahlen ansehen
						</a>
					</div>

					<p class="wl-hero__fineprint">
						30&nbsp;Min direkt mit mir · NDA möglich · keine Verkaufsshow.
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
							<path class="wl-spark__fill" d="M0,12 L32,18 L64,16 L96,28 L128,34 L160,40 L192,46 L224,52 L256,56 L288,60 L320,62 L320,70 L0,70 Z" fill="url(#wl-spark-fill)"/>
							<path class="wl-spark__line" pathLength="100" d="M0,12 L32,18 L64,16 L96,28 L128,34 L160,40 L192,46 L224,52 L256,56 L288,60 L320,62" fill="none" stroke="hsl(23 50% 54%)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</div>

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
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Problem</span>
				<h2 class="nx-headline-section">Verkaufen könnt ihr. Liefern ist der Engpass.</h2>
				<p class="wl-section-lede">Drei Engpässe, die jede Performance- und Webdesign-Agentur kennt. Alle drei lösbar — ohne eine einzige Neueinstellung.</p>
			</div>

			<div class="wl-problem__grid reveal-stagger">
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
	     SECTION 03 — LÖSUNG (Arbeitsmodus-Toggle)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-solution" data-nx-theme="dark" id="loesung">
		<div class="nx-container">
			<div class="wl-section-header wl-section-header--center nx-reveal">
				<span class="wl-eyebrow">Arbeitsmodus</span>
				<h2 class="nx-headline-section">Zwei Modi. Volle Kontrolle.</h2>
				<p class="wl-section-lede">Sichtbarkeit ist eine Einstellung, keine Grenze — ihr entscheidet pro Projekt.</p>
			</div>

			<div class="wl-mode" id="wl-mode" data-wl-mode="hintergrund">
				<div class="wl-mode__control nx-reveal">
					<fieldset class="wl-mode__switch">
						<legend class="wl-visually-hidden">Arbeitsmodus wählen</legend>
						<?php foreach ( $solution_modes as $mode_key => $mode ) : ?>
							<input
								type="radio"
								name="wl-mode"
								id="wl-mode-<?php echo esc_attr( $mode_key ); ?>"
								class="wl-mode__radio"
								value="<?php echo esc_attr( $mode_key ); ?>"
								<?php checked( 'hintergrund' === $mode_key ); ?>
							/>
							<label
								for="wl-mode-<?php echo esc_attr( $mode_key ); ?>"
								class="wl-mode__label"
								data-track-action="toggle_whitelabel_mode_<?php echo esc_attr( $mode_key ); ?>"
								data-track-category="engagement"
								data-track-section="loesung"
							><?php echo esc_html( $mode['label'] ); ?></label>
						<?php endforeach; ?>
					</fieldset>
					<p class="wl-mode__hint">Pro Projekt wählbar, jederzeit umstellbar.</p>
				</div>

				<div class="wl-mode__panels">
					<?php foreach ( $solution_modes as $mode_key => $mode ) : ?>
						<div class="wl-mode__panel wl-mode__statement" data-mode-panel="<?php echo esc_attr( $mode_key ); ?>" aria-label="<?php echo esc_attr( 'Modus: ' . $mode['label'] ); ?>">
							<p><?php echo esc_html( $mode['copy'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>

				<p class="wl-mode__footer">Egal wie: euer Branding, eure Marge, eure Kundenbeziehung bleiben bei euch.</p>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 04 — STACK / LEISTUNGEN
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-stack" id="leistungen">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Stack · Leistungen</span>
				<h2 class="nx-headline-section">Die Tiefe, die ihr ab sofort verkaufen könnt.</h2>
				<p class="wl-section-lede">Sechs Bereiche, senior umgesetzt. Einzeln als Projekt, kombiniert als Retainer — immer unter eurem Namen.</p>
			</div>

			<div class="wl-stack__grid reveal-stagger">
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
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Whitelabel-Kontrakt</span>
				<h2 class="nx-headline-section">Diskret. Schnell. Planbar.</h2>
				<p class="wl-section-lede">Drei Regeln, schriftlich im Kontrakt — prüfbar ab der ersten Minute, ohne Sternchen.</p>
			</div>

			<div class="wl-contract__grid reveal-stagger">
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
	     SECTION 06 — VERGLEICH (Einordnung)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-compare-section" id="vergleich">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Einordnung</span>
				<h2 class="nx-headline-section">Senior einstellen, Freelancer suchen — oder andocken?</h2>
				<p class="wl-section-lede">Drei Wege zu Senior-Kapazität. Ohne Fantasiezahlen — die Unterschiede liegen in Anlaufzeit, Risiko und Tiefe.</p>
			</div>

			<div class="wl-compare-wrap nx-reveal">
				<table class="wl-compare">
					<caption class="wl-visually-hidden">Vergleich der drei Wege zu Senior-Kapazität: Senior einstellen, Freelancer-Pool, White-Label-Partner</caption>
					<thead>
						<tr>
							<th scope="col" class="wl-compare__crit"><span class="wl-visually-hidden">Kriterium</span></th>
							<?php foreach ( $comparison_columns as $i => $column ) : ?>
								<th scope="col"<?php echo 2 === $i ? ' class="wl-compare__col--hl"' : ''; ?>>
									<?php echo esc_html( $column ); ?>
									<?php if ( 2 === $i ) : ?>
										<span class="wl-compare__chip">Dieses Modell</span>
									<?php endif; ?>
								</th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $comparison_rows as $row ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( $row['label'] ); ?></th>
								<?php foreach ( $row['cells'] as $i => $cell ) : ?>
									<td data-label="<?php echo esc_attr( $comparison_columns[ $i ] ); ?>"<?php echo 2 === $i ? ' class="wl-compare__cell--hl"' : ''; ?>><?php echo esc_html( $cell ); ?></td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 07 — PROOF (offengelegter Case)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-proof" data-nx-theme="dark" id="proof">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Beleg · offengelegter Case</span>
				<h2 class="nx-headline-section">Die meisten Mandate bleiben unter NDA. Eines ist offengelegt.</h2>
				<p class="wl-section-lede">Genau dafür ist Whitelabel da. Der eine Case, der öffentlich sein darf: ein mittelständischer PV-Installationsbetrieb, erneuerbare Energien — Server-Side-Tracking, Consent Mode V2, CRM-Attribution. Meine Arbeit, offengelegt bis in die Zahlen.</p>
			</div>

			<div class="wl-proof__grid reveal-stagger" role="list" aria-label="Proof-Kennzahlen">
				<div class="wl-proof__item" role="listitem">
					<div class="wl-proof__value"><?php echo esc_html( $cpl_before ); ?>&nbsp;→&nbsp;<?php echo esc_html( $cpl_after ); ?></div>
					<div class="wl-proof__label">Kosten pro Anfrage</div>
				</div>
				<div class="wl-proof__item" role="listitem">
					<div class="wl-proof__value"><span class="wl-counter wl-counter--proof-leads" data-counter-target="<?php echo esc_attr( (string) $lead_count_int ); ?>" data-counter-suffix="+"><?php echo esc_html( $lead_count ); ?></span></div>
					<div class="wl-proof__label">Qualifizierte Anfragen</div>
				</div>
				<div class="wl-proof__item" role="listitem">
					<div class="wl-proof__value"><span class="wl-counter wl-counter--proof-roas" data-counter-target="<?php echo esc_attr( (string) $roas_int ); ?>" data-counter-suffix="×"><?php echo esc_html( $roas ); ?></span></div>
					<div class="wl-proof__label">Return on Ad Spend (ROAS)</div>
				</div>
				<div class="wl-proof__item" role="listitem">
					<div class="wl-proof__value"><span class="wl-counter wl-counter--proof-conv" data-counter-target="<?php echo esc_attr( (string) $sales_conv_int ); ?>" data-counter-suffix="&nbsp;%"><?php echo esc_html( $sales_conv ); ?></span></div>
					<div class="wl-proof__label">Abschlussquote</div>
				</div>
			</div>

			<p class="wl-proof__case-link nx-reveal">
				<a href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="link_whitelabel_case_e3" data-track-category="navigation" data-track-section="proof">
					Case im Detail ansehen
					<svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
						<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</a>
			</p>

			<p class="wl-proof__disclaimer">Stand 2024–2025 · keine pauschale Übertragbarkeitsgarantie.</p>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 08 — WER LIEFERT (Founder-Strip)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-founder" id="person">
		<div class="nx-container">
			<div class="wl-founder__grid nx-reveal">
				<figure class="wl-founder__media">
					<img
						class="wl-founder__photo"
						src="<?php echo esc_url( $portrait_url ); ?>"
						alt="Haşim Üner, White-Label-Partner für Agenturen"
						width="640"
						height="800"
						loading="lazy"
						decoding="async"
					/>
				</figure>
				<div class="wl-founder__body">
					<span class="wl-eyebrow">Wer liefert</span>
					<h2 class="nx-headline-section">Eine Person. Kein Ticketsystem.</h2>
					<p class="wl-founder__copy">
						Kein Delivery-Team, keine Vermittlungsplattform. Haşim Üner — Technical SEO, Server-Side-Tracking, WordPress &amp; Core Web Vitals, Landingpages, Automation. Der Case oben: von der Tracking-Architektur bis zur letzten Zeile Code aus einer Hand. Im Fit-Gespräch sitzt ihr mit genau der Person, die nachher euren Code schreibt.
					</p>
					<ul class="wl-founder__chips" aria-label="Rahmendaten">
						<?php foreach ( $founder_chips as $chip ) : ?>
							<li><span class="wl-chip"><?php echo esc_html( $chip ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 09 — TECHNISCHER BELEG
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-tech" id="technik">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Technischer Beleg</span>
				<h2 class="nx-headline-section">Sauberer Code statt Plugin-Stack.</h2>
				<p class="wl-section-lede">Eigene Templates, bedarfsgesteuertes Asset-Loading, dokumentierte Übergabe. Keine Page-Builder-Wand, kein generisches Theme-Bloat.</p>
			</div>

			<div class="wl-tech__split nx-reveal">
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
    hu_enqueue_js(  <span class="wl-tech__s">'whitelabel'</span>,  <span class="wl-tech__s">'whitelabel.js'</span> );
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
	     SECTION 10 — EINSTIEG (Erstprojekt-Scope)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-entry" id="einstieg">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Einstieg</span>
				<h2 class="nx-headline-section">Kein sofortiger Retainer. Erst ein Erstprojekt mit fixem Scope.</h2>
			</div>

			<article class="wl-entry__card nx-reveal">
				<header class="wl-entry__head">
					<span class="wl-entry__kicker">Scope · Erstprojekt</span>
					<h3 class="wl-entry__title">Ein klar umrissenes Erstprojekt, bevor irgendwer „Retainer" sagt.</h3>
				</header>
				<p class="wl-entry__copy">
					Ein fixer, abgegrenzter Scope: Tracking-Audit, Server-Side-Setup oder eine Landingpage. 1–2&nbsp;Wochen Laufzeit, NDA, fixe Lieferung, Preis vorab vereinbart. Danach entscheidet ihr: Retainer, nächstes Projekt oder sauberer Abschluss.
				</p>
				<ul class="wl-entry__bullets" aria-label="Rahmen Erstprojekt">
					<?php foreach ( $entry_bullets as $bullet ) : ?>
						<li><span class="wl-entry__bullet-dot" aria-hidden="true"></span><?php echo esc_html( $bullet ); ?></li>
					<?php endforeach; ?>
				</ul>
				<div class="wl-entry__actions">
					<a href="<?php echo esc_url( $whitelabel_fit_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_whitelabel_entry_fit_call" data-track-category="lead_gen" data-track-section="entry">
						Erstprojekt besprechen
					</a>
				</div>
			</article>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 11 — FAQ (Einwände vor dem Gespräch)
	     nexus-core initFaqAccordion() wirkt dokument-weit auf <details>:
	     diese FAQ muss die einzige <details>-Gruppe der Seite bleiben.
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-faq-section" id="faq">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">FAQ · Zusammenarbeit</span>
				<h2 class="nx-headline-section">Was Agenturen vor dem ersten Projekt wissen wollen.</h2>
			</div>

			<div class="wl-faq reveal-stagger">
				<?php foreach ( $faq_items as $item ) : ?>
					<details class="wl-faq__item">
						<summary class="wl-faq__summary" data-track-action="faq_whitelabel_open" data-track-label="<?php echo esc_attr( $item['key'] ); ?>" data-track-category="engagement" data-track-section="faq">
							<span class="wl-faq__q"><?php echo esc_html( $item['q'] ); ?></span>
							<span class="wl-faq__icon" aria-hidden="true"></span>
						</summary>
						<div class="wl-faq__answer">
							<p><?php echo esc_html( $item['a'] ); ?></p>
						</div>
					</details>
				<?php endforeach; ?>
			</div>

			<p class="wl-faq__more">
				Eure Frage fehlt? Direkter Draht:
				<a href="<?php echo esc_url( $mailto_url ); ?>" data-track-action="mail_whitelabel_faq" data-track-category="contact" data-track-section="faq">hallo@hasimuener.de</a>
			</p>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 12 — FINAL CTA
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-cta" data-nx-theme="dark" id="cta">
		<div class="wl-cta__bg" aria-hidden="true">
			<div class="wl-hero__bg-warmth"></div>
			<div class="wl-hero__bg-vignette"></div>
		</div>
		<div class="nx-container">
			<div class="wl-cta__shell nx-reveal">
				<div class="wl-cta__copy">
					<span class="wl-eyebrow">Nächster Schritt</span>
					<h2 class="wl-cta__title">Passt das zu eurem Setup?</h2>
					<p class="wl-cta__lede">30&nbsp;Min, kein Pitch-Deck, keine Verkaufsshow. Stack, Kapazität, Sichtbarkeits-Modus, Konditionen — danach wisst ihr, ob es passt. Und wenn nicht, wisst ihr das auch.</p>
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

<?php get_footer(); ?>
