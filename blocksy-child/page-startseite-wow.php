<?php
/**
 * Visual homepage test route.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$analysis_url     = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$contact_url      = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' );
$energy_url       = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
$e3_canon         = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url      = isset( $e3_canon['url'] ) ? (string) $e3_canon['url'] : home_url( '/case-study-solar-leadgenerierung/' );
$e3_metrics       = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_lead_count    = $e3_metrics['lead_count']['display']       ?? '1.750+';
$e3_sales_conv    = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_cpl_reduction = $e3_metrics['cpl_reduction']['display']    ?? 'über 85 %';
$e3_timeframe     = $e3_metrics['timeframe']['display']        ?? '6 Monate';
$e3_cpl_before    = $e3_metrics['cpl_before']['display']       ?? '150 €';
$e3_cpl_after     = $e3_metrics['cpl_after']['display']        ?? '22 €';
$portrait_url     = get_stylesheet_directory_uri() . '/assets/img/hasim-portrait.png';

$system_nodes = [
	[
		'num'    => '01',
		'kicker' => 'Marktnachfrage',
		'title'  => 'Region, Bedarf und Projektwert werden sichtbar.',
		'desc'   => 'Kaufnahe Besucher landen nicht in einer allgemeinen Website, sondern in einer Strecke, die ihren Anlass und ihr Gebiet sauber aufnimmt.',
	],
	[
		'num'    => '02',
		'kicker' => 'Eigene Strecke',
		'title'  => 'Money Page, Beweis und Marktcheck greifen zusammen.',
		'desc'   => 'Die Seite beantwortet zuerst die wirtschaftliche Frage: Lohnt sich ein eigenes Anfrage-System in dieser Region und mit diesem Vertrieb?',
	],
	[
		'num'    => '03',
		'kicker' => 'Daten-Integrität',
		'title'  => 'Tracking trennt Klicks von echten Anfragen.',
		'desc'   => 'Server-Side Tracking, Consent und CRM-Rückführung zeigen, welche Quelle verwertbare Projekte bringt.',
	],
	[
		'num'    => '04',
		'kicker' => 'Vertrieb',
		'title'  => 'Der Vertrieb bekommt Kontext statt anonyme Kontakte.',
		'desc'   => 'Fit-Signal, Anliegen und Quelle kommen zusammen an. Das senkt Streuverlust und macht Nachfassen schneller.',
	],
];

$loss_markers = [
	[ 'label' => 'Blindflug', 'title' => 'Klicks werden gezählt, Aufträge nicht.', 'desc' => 'Budget wandert in Kanäle, die sichtbar wirken, aber keine belastbaren Abschlüsse tragen.' ],
	[ 'label' => 'Portal-Druck', 'title' => 'Ein Lead, mehrere Wettbewerber.', 'desc' => 'Der Betrieb zahlt für Aufmerksamkeit, besitzt aber weder Kontaktweg noch Datenlogik.' ],
	[ 'label' => 'Website-Stau', 'title' => 'Besucher finden keinen kaufnahen nächsten Schritt.', 'desc' => 'Traffic wird nicht gefiltert. Das Anfrageformular bekommt zu wenig Kontext und zu viele schwache Signale.' ],
];

$proof_metrics = [
	[ 'value' => $e3_lead_count, 'label' => 'qualifizierte Anfragen', 'detail' => 'eigenes System' ],
	[ 'value' => $e3_sales_conv, 'label' => 'Abschlussquote', 'detail' => 'vom Lead zum Auftrag' ],
	[ 'value' => $e3_cpl_before . ' auf ' . $e3_cpl_after, 'label' => 'Kosten pro Anfrage', 'detail' => 'vorher/nachher' ],
	[ 'value' => $e3_timeframe, 'label' => 'Validierungszeitraum', 'detail' => 'Implementierung und Optimierung' ],
];

$deep_links = [
	[ 'title' => 'Solar Leads kaufen Alternative', 'desc' => 'Portal-Kosten gegen eigenes System einordnen.', 'url' => home_url( '/solar-leads-kaufen-alternative/' ) ],
	[ 'title' => 'Lead-Funnel Solar', 'desc' => 'Fünf Stufen vom Suchintent bis zum Auftrag.', 'url' => home_url( '/lead-funnel-solar/' ) ],
	[ 'title' => 'Server-Side Tracking', 'desc' => 'GA4, Meta CAPI und CRM-Signal sauber verbinden.', 'url' => home_url( '/server-side-tracking-b2b/' ) ],
];

get_header();
?>

<main class="hu-hp hu-wow" id="main" data-track-section="homepage_wow_test">
	<section class="hu-wow-hero" id="hero" data-track-section="wow_hero">
		<div class="hu-wow-hero__system" aria-hidden="true">
			<div class="hu-wow-grid"></div>
			<div class="hu-wow-circuit hu-wow-circuit--a"></div>
			<div class="hu-wow-circuit hu-wow-circuit--b"></div>
			<div class="hu-wow-signal hu-wow-signal--one"></div>
			<div class="hu-wow-signal hu-wow-signal--two"></div>
			<div class="hu-wow-signal hu-wow-signal--three"></div>
			<div class="hu-wow-rail hu-wow-rail--top"></div>
			<div class="hu-wow-rail hu-wow-rail--bottom"></div>
		</div>

		<div class="hu-container hu-wow-hero__inner">
			<div class="hu-wow-hero__copy" data-wow-reveal>
				<div class="hu-hero__eyebrow">
					<span class="hu-tag">
						<span class="hu-dot hu-dot--live"></span>
						<span class="hu-mono">ANFRAGE-SYSTEM · SOLAR · SHK · DACH</span>
					</span>
				</div>

				<h1 class="hu-display hu-wow-hero__title">Das eigene Anfrage-System als sichtbare Maschine.</h1>
				<p class="hu-wow-hero__lead">
					Für Solar-, Wärmepumpen- und Speicher-Anbieter, die nicht mehr raten wollen,
					wo Anfragen verloren gehen. Nachfrage, Vorqualifizierung, Tracking und Vertrieb
					werden als ein System gebaut.
				</p>

				<div class="hu-wow-hero__actions">
					<a href="<?php echo esc_url( $analysis_url ); ?>" class="hu-btn hu-btn-primary"
						data-track-action="cta_wow_hero_marktcheck" data-track-category="lead_gen" data-track-section="wow_hero">
						Marktcheck starten - 60 Sekunden
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
					</a>
					<a href="<?php echo esc_url( $e3_case_url ); ?>" class="hu-btn hu-btn-ghost"
						data-track-action="cta_wow_hero_case_study" data-track-category="proof" data-track-section="wow_hero">
						Case Study prüfen
					</a>
				</div>

				<p class="hu-wow-hero__note">Erst Diagnose, dann Entscheidung. Kein Relaunch auf Verdacht.</p>
			</div>

			<div class="hu-wow-kpi-strip" aria-label="Case Study Proof Kennzahlen" data-wow-reveal>
				<?php foreach ( $proof_metrics as $metric ) : ?>
					<div class="hu-wow-kpi">
						<strong><?php echo esc_html( $metric['value'] ); ?></strong>
						<span><?php echo esc_html( $metric['label'] ); ?></span>
						<small><?php echo esc_html( $metric['detail'] ); ?></small>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-wow-section hu-wow-section--system" id="system" data-track-section="wow_system">
		<div class="hu-container">
			<div class="hu-wow-section-head" data-wow-reveal>
				<span class="hu-eyebrow">01 / System-Cockpit</span>
				<h2>So sieht das Anfrage-System aus, wenn es nicht als Website, sondern als Infrastruktur gedacht wird.</h2>
				<p>Die Oberfläche bleibt einfach. Darunter laufen vier Ebenen zusammen: Nachfrage, eigener Filter, Daten-Integrität und Vertrieb.</p>
			</div>

			<div class="hu-wow-engine" aria-label="Vier Ebenen des Anfrage-Systems">
				<div class="hu-wow-engine__visual" aria-hidden="true" data-wow-reveal>
					<div class="hu-wow-engine__lane hu-wow-engine__lane--one"></div>
					<div class="hu-wow-engine__lane hu-wow-engine__lane--two"></div>
					<div class="hu-wow-engine__core">
						<span class="hu-mono">ACTIVE FILTER</span>
						<strong>Eigene Strecke</strong>
						<small>Money Page · Marktcheck · Tracking · CRM</small>
					</div>
					<div class="hu-wow-engine__node hu-wow-engine__node--demand">Nachfrage</div>
					<div class="hu-wow-engine__node hu-wow-engine__node--proof">Beweis</div>
					<div class="hu-wow-engine__node hu-wow-engine__node--fit">Fit-Score</div>
					<div class="hu-wow-engine__node hu-wow-engine__node--crm">Vertrieb</div>
				</div>

				<ol class="hu-wow-engine__steps" data-wow-reveal>
					<?php foreach ( $system_nodes as $node ) : ?>
						<li class="hu-wow-step" tabindex="0">
							<span class="hu-wow-step__num hu-mono"><?php echo esc_html( $node['num'] ); ?></span>
							<div>
								<span class="hu-wow-step__kicker hu-mono"><?php echo esc_html( $node['kicker'] ); ?></span>
								<h3><?php echo esc_html( $node['title'] ); ?></h3>
								<p><?php echo esc_html( $node['desc'] ); ?></p>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</div>
	</section>

	<section class="hu-wow-section hu-wow-section--cream" id="verlust" data-track-section="wow_loss">
		<div class="hu-container">
			<div class="hu-wow-section-head hu-wow-section-head--dark" data-wow-reveal>
				<span class="hu-eyebrow">02 / Wo Geld verschwindet</span>
				<h2>Drei Lecks, die auf normalen Homepages unsichtbar bleiben.</h2>
				<p>Mehr Traffic löst das Problem nicht, wenn der Pfad danach nicht beweisbar, exklusiv und vertriebsnah ist.</p>
			</div>

			<div class="hu-wow-loss-grid">
				<?php foreach ( $loss_markers as $loss ) : ?>
					<article class="hu-wow-loss" data-wow-reveal>
						<span class="hu-wow-loss__label hu-mono"><?php echo esc_html( $loss['label'] ); ?></span>
						<h3><?php echo esc_html( $loss['title'] ); ?></h3>
						<p><?php echo esc_html( $loss['desc'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-wow-section" id="proof" data-track-section="wow_proof">
		<div class="hu-container">
			<div class="hu-wow-proof">
				<div class="hu-wow-proof__copy" data-wow-reveal>
					<span class="hu-eyebrow">03 / Beweis-Schicht</span>
					<h2>Diese Case Study zeigt den Mechanismus: eigene Strecke statt Lead-Einkauf.</h2>
					<p>
						Der Case ist kein Versprechen für jeden Markt. Er zeigt, welche Mechanik funktioniert,
						wenn Projektwert, Vertrieb und Region zur eigenen Nachfrage-Infrastruktur passen.
					</p>
					<a href="<?php echo esc_url( $e3_case_url ); ?>" class="hu-btn hu-btn-link"
						data-track-action="cta_wow_proof_case_study" data-track-category="proof" data-track-section="wow_proof">
						Methodik mit Zahlen lesen
					</a>
				</div>

				<div class="hu-wow-proof__board" data-wow-reveal>
					<div class="hu-wow-proof__row">
						<span>Portal-Lead vorher</span>
						<strong><?php echo esc_html( $e3_cpl_before ); ?></strong>
					</div>
					<div class="hu-wow-proof__row hu-wow-proof__row--active">
						<span>Eigene Anfrage nachher</span>
						<strong><?php echo esc_html( $e3_cpl_after ); ?></strong>
					</div>
					<div class="hu-wow-proof__row">
						<span>Kostenreduktion</span>
						<strong><?php echo esc_html( $e3_cpl_reduction ); ?></strong>
					</div>
					<div class="hu-wow-proof__result">
						<span class="hu-mono">VALIDIERUNG</span>
						<strong><?php echo esc_html( $e3_lead_count ); ?> Anfragen · <?php echo esc_html( $e3_sales_conv ); ?> Abschluss</strong>
						<small><?php echo esc_html( $e3_timeframe ); ?> Case Study</small>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="hu-wow-section hu-wow-section--cream" id="route" data-track-section="wow_route">
		<div class="hu-container">
			<div class="hu-wow-route">
				<div class="hu-wow-route__person" data-wow-reveal>
					<img src="<?php echo esc_url( $portrait_url ); ?>" alt="Haşim Üner" width="380" height="380" loading="lazy" decoding="async">
				</div>
				<div class="hu-wow-route__copy" data-wow-reveal>
					<span class="hu-eyebrow">04 / Nächster Schritt</span>
					<h2>Erst prüfen, ob das System für Ihre Region Sinn ergibt.</h2>
					<p>
						Der Marktcheck trennt gute Ausgangslagen von Projekten, die erst später Sinn ergeben.
						Sie bekommen eine persönliche Einordnung zu Region, Anfragequalität, Vertrieb und erstem Hebel.
					</p>
					<div class="hu-wow-route__actions">
						<a href="<?php echo esc_url( $analysis_url ); ?>" class="hu-btn hu-btn-primary"
							data-track-action="cta_wow_final_marktcheck" data-track-category="lead_gen" data-track-section="wow_route">
							Region und Anfrage-System prüfen
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
						</a>
						<a href="<?php echo esc_url( $energy_url ); ?>" class="hu-btn hu-btn-ghost"
							data-track-action="cta_wow_final_solar_page" data-track-category="navigation" data-track-section="wow_route">
							Branchen-Seite ansehen
						</a>
						<a href="<?php echo esc_url( $contact_url ); ?>" class="hu-btn hu-btn-link"
							data-track-action="cta_wow_final_contact" data-track-category="lead_gen" data-track-section="wow_route">
							Direkt schreiben
						</a>
					</div>
				</div>
			</div>

			<nav class="hu-wow-deep-links" aria-label="Vertiefende Seiten" data-wow-reveal>
				<?php foreach ( $deep_links as $link ) : ?>
					<a href="<?php echo esc_url( $link['url'] ); ?>"
						data-track-action="homepage_wow_deep_link"
						data-track-category="navigation"
						data-track-section="wow_route">
						<strong><?php echo esc_html( $link['title'] ); ?></strong>
						<span><?php echo esc_html( $link['desc'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		</div>
	</section>
</main>

<?php get_footer(); ?>
