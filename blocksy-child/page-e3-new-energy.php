<?php
/**
 * Template Name: Solar Case Study Methodik-Case
 * Description: Methodik-Case fuer /case-study-solar-leadgenerierung/.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$diagnostic_url = function_exists( 'hu_get_request_analysis_url' )
	? hu_get_request_analysis_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );

$tracking_attrs = 'data-track-section="case_solar_methodology" data-track-funnel-stage="proof"';

$e3 = static function ( $metric, $field = 'display', $fallback = '' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $metric, $field, $fallback ) : $fallback;
};

$e3_canon      = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_label = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'mittelständischer PV-Installationsbetrieb';

$e3_cpl_before   = $e3( 'cpl_before', 'display', '150 €' );
$e3_cpl_after    = $e3( 'cpl_after', 'display', '22 €' );
$e3_cpl_ramp     = $e3( 'cpl_ramp', 'display', '70 – 100 €' );
$e3_lead_count   = $e3( 'lead_count', 'display', '1.750+' );
$e3_conv_before  = $e3( 'sales_conversion_before', 'display', '1 – 5 %' );
$e3_conv_after   = $e3( 'sales_conversion_after', 'display', '15 %' );
$e3_timeframe    = $e3( 'timeframe', 'display', '6 Monate' );
$e3_timeframe_dt = $e3( 'timeframe', 'display_dative', '6 Monaten' );

$diagnosis = [
	[
		'number' => '01',
		'title'  => 'Kaufabsicht',
		'body'   => 'Menschen mit konkretem Bedarf wurden näher an ihrer tatsächlichen Kaufentscheidung abgeholt — statt nur möglichst viele Kontakte zu erzeugen.',
	],
	[
		'number' => '02',
		'title'  => 'Exklusivität',
		'body'   => 'Eigene Anfragen blieben beim Betrieb. Kein paralleler Weiterverkauf, keine fremde Datenhoheit, kein Wettlauf gegen mehrere Anbieter.',
	],
	[
		'number' => '03',
		'title'  => 'Vorqualifizierung',
		'body'   => 'Relevante Angaben wurden vor dem Erstkontakt strukturiert erfasst und im CRM priorisiert. Der Vertrieb sah schneller, welche Fälle Substanz hatten.',
	],
	[
		'number' => '04',
		'title'  => 'Geschwindigkeit',
		'body'   => 'Neue Anfragen liefen direkt in den Vertriebsprozess. Weniger manuelle Zwischenschritte, weniger verlorene Zeit zwischen Interesse und Erstkontakt.',
	],
];

$system_steps = [
	[
		'eyebrow' => '01 · Nachfrage',
		'title'   => 'Google · Meta · SEO',
		'body'    => 'Kaufnahe Suchintentionen und Kampagnen statt isolierter Reichweite.',
	],
	[
		'eyebrow' => '02 · Einstieg',
		'title'   => 'Landingpage',
		'body'    => 'Klare Seiten für konkrete Bedürfnisse, Produkte und Regionen.',
	],
	[
		'eyebrow' => '03 · Filter',
		'title'   => 'Multi-Step',
		'body'    => 'Bedarf, Objekt und Umsetzungsreife werden vor dem Gespräch erfasst.',
	],
	[
		'eyebrow' => '04 · Priorität',
		'title'   => 'Lead-Score',
		'body'    => 'Qualifizierte Fälle werden für den Vertrieb sichtbar priorisiert.',
	],
	[
		'eyebrow' => '05 · Übergabe',
		'title'   => 'CRM',
		'body'    => 'Anfragen landen strukturiert im System statt in einzelnen Postfächern.',
	],
	[
		'eyebrow' => '06 · Bearbeitung',
		'title'   => 'Vertrieb',
		'body'    => 'Der Außendienst arbeitet mit Kontext statt mit einer nackten Telefonnummer.',
	],
	[
		'eyebrow' => '07 · Ergebnis',
		'title'   => 'Auftrag',
		'body'    => 'Nicht nur Leads, sondern tatsächliche Vertriebsresultate werden relevant.',
	],
	[
		'eyebrow' => '08 · Lernsignal',
		'title'   => 'Daten zurück',
		'body'    => 'Conversion-Signale fließen zurück in Messung und Optimierung.',
	],
];

$work_blocks = [
	[
		'tag'   => 'Website',
		'title' => 'Landingpages & Anfragewege',
		'body'  => 'Eigene Conversion-Strecken für unterschiedliche Bedürfnisse und Suchintentionen.',
	],
	[
		'tag'   => 'CRO',
		'title' => 'Multi-Step-Qualifizierung',
		'body'  => 'Filterfragen und Formularlogik für bessere Vertriebsfälle statt möglichst vieler Formulareingänge.',
	],
	[
		'tag'   => 'CRM',
		'title' => 'Lead-Scoring & Übergabe',
		'body'  => 'Strukturierte Übergabe ins CRM und Priorisierung nach Relevanz für den Vertrieb.',
	],
	[
		'tag'   => 'Tracking',
		'title' => 'Attribution & Server-Side',
		'body'  => 'GA4, GTM und serverseitige Signale als belastbare Datenbasis für die Optimierung.',
	],
	[
		'tag'   => 'Paid',
		'title' => 'Google & Meta',
		'body'  => 'Kampagnen, Zielgruppen, Creatives und Suchintentionen wurden auf Basis der entstehenden Daten nachgeschärft.',
	],
	[
		'tag'   => 'Organic',
		'title' => 'Technisches SEO',
		'body'  => 'Eigene kaufnahe Einstiegspunkte ergänzten die bezahlte Nachfrage und reduzierten die Portalabhängigkeit.',
	],
];

$deeper_links = [
	[
		'title' => 'Cost per Lead Photovoltaik',
		'body'  => 'Wie CPL und tatsächliche Kosten pro Auftrag auseinanderliegen können.',
		'url'   => home_url( '/cost-per-lead-photovoltaik/' ),
	],
	[
		'title' => 'Lead-Funnel Solar',
		'body'  => 'Die Architektur vom ersten Suchsignal bis zur qualifizierten Anfrage.',
		'url'   => home_url( '/lead-funnel-solar/' ),
	],
	[
		'title' => 'Server-Side-Tracking B2B',
		'body'  => 'Wie Marketing-, Website- und CRM-Daten technisch verbunden werden.',
		'url'   => home_url( '/server-side-tracking-b2b/' ),
	],
	[
		'title' => 'Portal-Leads vs. eigenes System',
		'body'  => 'TCO-Logik und Kostenvergleich über einen längeren Zeitraum.',
		'url'   => home_url( '/eigene-leadgenerierung-vs-portale/' ),
	],
];

$faq_items = function_exists( 'nexus_get_e3_case_faq_items' ) ? nexus_get_e3_case_faq_items() : [];

$case_css_path = get_stylesheet_directory() . '/assets/css/e3-case-v2.css';
wp_enqueue_style(
	'hu-e3-case-v2',
	get_stylesheet_directory_uri() . '/assets/css/e3-case-v2.css',
	[],
	file_exists( $case_css_path ) ? (string) filemtime( $case_css_path ) : wp_get_theme()->get( 'Version' )
);

get_header();
?>

<main id="main" class="site-main">
	<article class="e3-case-v2" <?php echo $tracking_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<section class="e3v2-hero" id="hero" aria-labelledby="e3v2-hero-title">
			<div class="e3v2-container e3v2-hero__grid">
				<div class="e3v2-hero__copy" data-reveal>
					<p class="e3v2-kicker">Case Study · Photovoltaik &amp; Wärmepumpe</p>
					<h1 id="e3v2-hero-title">Von gekauften Leads zum eigenen Anfragesystem.</h1>
					<p class="e3v2-hero__lead">Ein <?php echo esc_html( $e3_case_label ); ?> kaufte Anfragen für rund <?php echo esc_html( $e3_cpl_before ); ?> pro Stück. Gleichzeitig kamen über ein Hersteller-Partnerprogramm kostenlose Anfragen herein, die deutlich häufiger zu Aufträgen wurden.</p>
					<p class="e3v2-hero__thesis">Aus diesem Unterschied entstand Schritt für Schritt eine eigene Strecke — von Landingpage und Vorqualifizierung über Tracking und CRM bis zum Vertrieb.</p>
					<a class="e3v2-text-link" href="#system" data-track-action="case_v2_hero_system" data-track-category="navigation">System hinter dem Ergebnis ansehen <span aria-hidden="true">↓</span></a>
				</div>

				<aside class="e3v2-result-card" aria-label="Ergebnisse des Referenzfalls" data-reveal>
					<p class="e3v2-result-card__label">Dokumentierter Referenzfall</p>
					<div class="e3v2-result-card__primary">
						<span><?php echo esc_html( $e3_cpl_before ); ?></span>
						<i aria-hidden="true">→</i>
						<strong><?php echo esc_html( $e3_cpl_after ); ?></strong>
					</div>
					<p class="e3v2-result-card__caption">Kosten pro Anfrage</p>
					<div class="e3v2-result-card__stats">
						<div><strong><?php echo esc_html( $e3_lead_count ); ?></strong><span>qualifizierte Anfragen</span></div>
						<div><strong><?php echo esc_html( $e3_conv_after ); ?></strong><span>Abschlussquote</span></div>
						<div><strong><?php echo esc_html( $e3_timeframe ); ?></strong><span>Aufbau &amp; Optimierung</span></div>
					</div>
					<p class="e3v2-result-card__note">Die Werte zeigen die Entwicklung des Gesamtsystems inklusive Kampagnen und Vertrieb. Keine Prognose für andere Betriebe.</p>
				</aside>
			</div>
		</section>

		<section class="e3v2-section e3v2-section--light" id="ausgangspunkt" aria-labelledby="e3v2-ausgangspunkt-title">
			<div class="e3v2-container">
				<header class="e3v2-heading" data-reveal>
					<p class="e3v2-kicker">Ausgangspunkt</p>
					<h2 id="e3v2-ausgangspunkt-title">Der stärkste Hinweis kam nicht aus dem Werbekonto. Sondern aus dem Vertrieb.</h2>
					<p>Zwei Anfragequellen liefen parallel. Der Vertrieb dahinter war derselbe — die Ergebnisse waren es nicht.</p>
				</header>

				<div class="e3v2-source-grid">
					<article class="e3v2-source-card e3v2-source-card--portal" data-reveal>
						<div class="e3v2-source-card__top"><span>Portal-Leads</span><span>vorher</span></div>
						<strong><?php echo esc_html( $e3_cpl_before ); ?></strong>
						<p>pro gekaufter Anfrage</p>
						<div class="e3v2-source-card__foot"><b><?php echo esc_html( $e3_conv_before ); ?></b> Abschlussquote auf Auftrag</div>
					</article>

					<div class="e3v2-source-vs" aria-hidden="true">vs.</div>

					<article class="e3v2-source-card e3v2-source-card--maker" data-reveal>
						<div class="e3v2-source-card__top"><span>Hersteller-Anfragen</span><span>Parallelquelle</span></div>
						<strong>0 €</strong>
						<p>direkter Einkaufspreis für den Betrieb</p>
						<div class="e3v2-source-card__foot"><b>deutlich höher</b> in der Abschlussqualität</div>
					</article>
				</div>

				<blockquote class="e3v2-statement" data-reveal>
					<p>Gleiche Mannschaft. Gleiche Produkte. Andere Anfragequalität.</p>
					<footer>Damit verschob sich die Frage von „Wie bekommen wir mehr Leads?“ zu „Was macht eine Anfrage tatsächlich wertvoll?“</footer>
				</blockquote>
			</div>
		</section>

		<section class="e3v2-section e3v2-section--dark" id="diagnose" aria-labelledby="e3v2-diagnose-title">
			<div class="e3v2-container">
				<header class="e3v2-heading e3v2-heading--dark" data-reveal>
					<p class="e3v2-kicker">Diagnose</p>
					<h2 id="e3v2-diagnose-title">Vier Unterschiede wurden zur Bauanleitung.</h2>
					<p>Nicht ein einzelner Kanal war die Lösung. Entscheidend waren vier Eigenschaften der Anfrage.</p>
				</header>

				<div class="e3v2-factor-grid">
					<?php foreach ( $diagnosis as $factor ) : ?>
						<article class="e3v2-factor-card" data-reveal>
							<span class="e3v2-factor-card__number"><?php echo esc_html( $factor['number'] ); ?></span>
							<h3><?php echo esc_html( $factor['title'] ); ?></h3>
							<p><?php echo esc_html( $factor['body'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="e3v2-section e3v2-section--system" id="system" aria-labelledby="e3v2-system-title">
			<div class="e3v2-container">
				<header class="e3v2-heading" data-reveal>
					<p class="e3v2-kicker">Das System</p>
					<h2 id="e3v2-system-title">Nicht Ads optimiert. Die komplette Strecke verbunden.</h2>
					<p>Marketing, Website, Qualifizierung, CRM und Vertrieb wurden nicht länger als getrennte Maßnahmen behandelt.</p>
				</header>

				<ol class="e3v2-system-flow" aria-label="Strecke vom Nachfrage-Signal bis zur Rückführung der Conversion-Daten">
					<?php foreach ( $system_steps as $step ) : ?>
						<li class="e3v2-system-step" data-reveal>
							<span><?php echo esc_html( $step['eyebrow'] ); ?></span>
							<h3><?php echo esc_html( $step['title'] ); ?></h3>
							<p><?php echo esc_html( $step['body'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>

				<div class="e3v2-system-caption" data-reveal>
					<span>Website</span><i>+</i><span>Qualifizierung</span><i>+</i><span>Tracking</span><i>+</i><span>CRM</span><i>+</i><span>Vertrieb</span>
				</div>
			</div>
		</section>

		<section class="e3v2-section e3v2-section--trajectory" id="verlauf" aria-labelledby="e3v2-verlauf-title">
			<div class="e3v2-container">
				<header class="e3v2-heading e3v2-heading--dark" data-reveal>
					<p class="e3v2-kicker">Entwicklung</p>
					<h2 id="e3v2-verlauf-title">Der Weg zu <?php echo esc_html( $e3_cpl_after ); ?> verlief in Etappen.</h2>
					<p>Drei belegte Zustände, keine geglättete Erfolgskurve. Für die Zwischenmonate liegen keine getrennten CPL-Werte vor.</p>
				</header>

				<div class="e3v2-trajectory" data-reveal>
					<article class="e3v2-stage">
						<span class="e3v2-stage__index">01</span>
						<p class="e3v2-stage__label">Ausgangslage</p>
						<strong><?php echo esc_html( $e3_cpl_before ); ?></strong>
						<h3>Portal-Einkauf</h3>
						<p>Zugekaufte Anfragen bildeten einen wesentlichen Teil der Leadversorgung.</p>
					</article>
					<article class="e3v2-stage e3v2-stage--middle">
						<span class="e3v2-stage__index">02</span>
						<p class="e3v2-stage__label">Monate 1–2</p>
						<strong><?php echo esc_html( $e3_cpl_ramp ); ?></strong>
						<h3>Aufbau &amp; Tests</h3>
						<p>Schon die erste eigene Strecke senkte die Kosten deutlich — bevor das System vollständig optimiert war.</p>
					</article>
					<article class="e3v2-stage e3v2-stage--final">
						<span class="e3v2-stage__index">03</span>
						<p class="e3v2-stage__label">Monate 4–6</p>
						<strong><?php echo esc_html( $e3_cpl_after ); ?></strong>
						<h3>Optimiertes System</h3>
						<p>Kampagnen, Formulare, Zielgruppen und Conversion-Wege wurden auf Basis belastbarer Daten nachgeschärft.</p>
					</article>
				</div>

				<div class="e3v2-outcome-strip" data-reveal>
					<div><strong><?php echo esc_html( $e3_lead_count ); ?></strong><span>qualifizierte Anfragen in <?php echo esc_html( $e3_timeframe_dt ); ?></span></div>
					<div><strong><?php echo esc_html( $e3_conv_after ); ?></strong><span>Abschlussquote auf Auftrag</span></div>
					<div><strong><?php echo esc_html( $e3_cpl_before ); ?> → <?php echo esc_html( $e3_cpl_after ); ?></strong><span>nicht durch einen einzelnen Hebel, sondern durch das Gesamtsystem</span></div>
				</div>
			</div>
		</section>

		<section class="e3v2-section e3v2-section--light" id="umsetzung" aria-labelledby="e3v2-umsetzung-title">
			<div class="e3v2-container">
				<header class="e3v2-heading" data-reveal>
					<p class="e3v2-kicker">Was konkret verändert wurde</p>
					<h2 id="e3v2-umsetzung-title">Sechs Bausteine. Eine gemeinsame Daten- und Vertriebsstrecke.</h2>
					<p>Server-Side-Tracking war dabei wichtig — aber nicht die alleinige Erklärung für das Ergebnis.</p>
				</header>

				<div class="e3v2-work-grid">
					<?php foreach ( $work_blocks as $block ) : ?>
						<article class="e3v2-work-card" data-reveal>
							<span><?php echo esc_html( $block['tag'] ); ?></span>
							<h3><?php echo esc_html( $block['title'] ); ?></h3>
							<p><?php echo esc_html( $block['body'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="e3v2-section e3v2-section--transfer" id="einordnung" aria-labelledby="e3v2-einordnung-title">
			<div class="e3v2-container e3v2-transfer-grid">
				<div data-reveal>
					<p class="e3v2-kicker">Einordnung</p>
					<h2 id="e3v2-einordnung-title"><?php echo esc_html( $e3_cpl_after ); ?> sind kein Versprechen. Die Architektur ist übertragbar.</h2>
				</div>
				<div class="e3v2-transfer-copy" data-reveal>
					<p>Der konkrete CPL hängt unter anderem von Region, Wettbewerb, Produkt, Werbekosten, Marke und Vertrieb ab. Deshalb wäre es unseriös, den Endwert dieses Referenzfalls für einen anderen Betrieb zu versprechen.</p>
					<p>Übertragbar ist die Logik dahinter:</p>
					<p class="e3v2-formula">Kaufabsicht erkennen <span>→</span> eigene Nachfrage erzeugen <span>→</span> qualifizieren <span>→</span> schnell übergeben <span>→</span> Ergebnis messen <span>→</span> optimieren.</p>
				</div>
			</div>
		</section>

		<section class="e3v2-section e3v2-section--cta" id="cta" aria-labelledby="e3v2-cta-title">
			<div class="e3v2-container e3v2-cta-grid" data-reveal>
				<div>
					<p class="e3v2-kicker">Nächster Schritt</p>
					<h2 id="e3v2-cta-title">Wo verliert Ihr Anfrageweg heute Geld?</h2>
				</div>
				<div class="e3v2-cta-copy">
					<p>Im Marktcheck prüfe ich Anfragequellen, Qualifizierung, Tracking und Übergabe an den Vertrieb. Keine pauschale CPL-Prognose — sondern eine Einordnung, wo im bestehenden System der größte Hebel liegt und ob ein eigener Anfrageweg wirtschaftlich sinnvoll ist.</p>
					<a class="e3v2-button" href="<?php echo esc_url( $diagnostic_url ); ?>" data-track-action="cta_case_study_to_diagnostic_request" data-track-category="lead_gen" data-track-section="case_solar_methodology">Marktcheck starten <span aria-hidden="true">→</span></a>
					<p class="e3v2-cta-micro">Händisch geprüft · Befund <?php echo esc_html( hu_marketcheck_reply_label() ); ?> · kein Pflicht-Termin · keine Zahlungsdaten</p>
				</div>
			</div>
		</section>

		<section class="e3v2-section e3v2-section--deeper" id="vertiefung" aria-labelledby="e3v2-vertiefung-title">
			<div class="e3v2-container">
				<header class="e3v2-heading" data-reveal>
					<p class="e3v2-kicker">Methodik vertiefen</p>
					<h2 id="e3v2-vertiefung-title">Die Fachseiten hinter dem Referenzfall.</h2>
				</header>
				<div class="e3v2-deeper-grid">
					<?php foreach ( $deeper_links as $link ) : ?>
						<a class="e3v2-deeper-card" href="<?php echo esc_url( $link['url'] ); ?>" data-track-action="cta_case_study_deeper_link" data-track-category="navigation" data-track-section="vertiefung" data-reveal>
							<h3><?php echo esc_html( $link['title'] ); ?></h3>
							<p><?php echo esc_html( $link['body'] ); ?></p>
							<span aria-hidden="true">↗</span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<?php if ( ! empty( $faq_items ) ) : ?>
			<section class="e3v2-section e3v2-section--faq" id="fragen" aria-labelledby="e3v2-fragen-title">
				<div class="e3v2-container e3v2-faq-layout">
					<header class="e3v2-heading" data-reveal>
						<p class="e3v2-kicker">Fragen zum Referenzfall</p>
						<h2 id="e3v2-fragen-title">Details, die häufig nachgefragt werden.</h2>
					</header>
					<div class="e3v2-faq-list">
						<?php foreach ( $faq_items as $faq_item ) : ?>
							<details class="e3v2-faq-item" data-reveal>
								<summary><?php echo esc_html( $faq_item['question'] ); ?></summary>
								<div><p><?php echo esc_html( $faq_item['answer'] ); ?></p></div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	</article>
</main>

<?php
get_template_part(
	'template-parts/seo-subpage-sticky-cta',
	null,
	[
		'cta_url'        => $diagnostic_url,
		'track_category' => 'case_solar_methodology',
		'lead'           => 'Marktcheck',
		'sub'            => 'Fit-Befund statt Standardbericht',
		'label'          => 'Eigene Quellen prüfen',
		'track_action'   => 'cta_sticky_case_study_marktcheck',
	]
);

get_footer();
