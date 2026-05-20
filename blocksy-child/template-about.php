<?php
/**
 * Template Name: Nexus Über Mich
 * Description: Storytelling-Positionsseite für Solar- und Wärmepumpen-Anbieter
 *
 * Redesign nach Claude-Design-Bundle (uber-mich.html):
 * - Warm-cream Farbschema (#FAF7F2 / #F4EDDD / #2A261B)
 * - Brunnen-Visual als AHA-Element im Hero (scrollbasiert)
 * - Section-Number-Grid (Eyebrow links, H2 rechts)
 * - Inline Fit-Check-Card statt Negativ-Liste
 * - Zahlen als Proof-Ebene nach der Arbeitsweise statt Hero-Dominanz
 * - Mikro-Copy unter primären CTAs
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$audit_url    = function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$request_url  = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$request_cta  = function_exists( 'nexus_get_primary_request_cta_label' ) ? nexus_get_primary_request_cta_label() : 'Marktcheck starten';
$portrait_url = function_exists( 'hu_get_profile_image_url' ) ? hu_get_profile_image_url() : get_stylesheet_directory_uri() . '/assets/img/hasim-portrait.png';

// E3-Canon
$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '9 Monate';
$e3_conv_uplift      = $e3_metrics['sales_conversion_uplift']['display'] ?? '1 – 2 % → 12 %';
$e3_conv_uplift_lbl  = $e3_metrics['sales_conversion_uplift']['label'] ?? 'Anstieg der Abschlussquote durch eigenes System';

// Hero-Pfad: synchron mit den Brunnen-Schichten (Schichten-Analyse → Filter → Eigene Quelle).
$about_hero_path = [
	[
		't' => '1. Schichten-Analyse',
		's' => 'Suchintention, Tracking und CRM-Anschluss als ein System verstehen.',
	],
	[
		't' => '2. Filter-Infrastruktur',
		's' => 'Funnel und Vorqualifizierung, die Fit, Region und Projektwert früh sichtbar machen.',
	],
	[
		't' => '3. Eigene Quelle',
		's' => 'Skalieren erst, wenn Code, Daten und Vertriebsanschluss bei Ihnen liegen.',
	],
];

// Arbeitsweise: konsistent mit den Brunnen-Labels (20 m / 40 m / 60 – 80 m).
$about_work_principles = [
	[
		'eyebrow' => 'Schicht 1 / 20 Meter',
		'title'   => 'Portal-Leads trockenlegen.',
		'body'    => 'Wer Anfragen über Portale kauft, teilt sich denselben Kontakt mit drei Wettbewerbern und zahlt für Klicks ohne Fit. Ich schaue zuerst, wie viel Ihres Vertriebsaufwands in diesen geteilten Kontakten versickert — und wo eigene Nachfrage realistisch ist.',
		'detail'  => 'Erst wenn klar ist, warum echte Anfragen heute nicht zuverlässig bei Ihnen ankommen, lohnt sich der nächste Schritt.',
	],
	[
		'eyebrow' => 'Schicht 2 / 40 Meter',
		'title'   => 'Tracking, das Abschlüsse zeigt — nicht Klicks.',
		'body'    => 'Standard-Tracking misst Bewegung. Was Sie brauchen, ist ein Signal, das zwischen Formular-Klick und unterschriebenem Vertrag unterscheidet. Server-seitiges GA4, Meta CAPI und Consent Mode v2 — DSGVO-konform, cookieless-ready, mit den Daten auf Ihrer Infrastruktur.',
		'detail'  => 'Nur wer sauber misst, kann Budget auf die rentablen Anfragen lenken — und unrentable Kanäle abschalten, bevor sie wehtun.',
	],
	[
		'eyebrow' => 'Schicht 3 / 60 – 80 Meter',
		'title'   => 'Eigene Quelle. Ihr Eigentum.',
		'body'    => 'Wenn Karte und Messung stimmen, wird gebaut: sauberes WordPress, Vorqualifizierungs-Funnel, CRM-Anschluss und Inhalte, die Gespräche vorbereiten statt nur Klicks zu sammeln. Code, Daten und Tracking bleiben zu 100 % im Firmenbesitz.',
		'detail'  => 'Eine Anfrage-Infrastruktur, die liefert — anstatt eines weiteren Kanals, der monatlich neu gefüttert werden muss.',
	],
];

// E3 bleibt als Beleg, aber nicht als Einstieg der Über-mich-Seite.
$about_evidence = [
	[
		'k' => $e3_conv_uplift,
		'l' => $e3_conv_uplift_lbl,
	],
	[
		'k' => $e3_cpl_reduction,
		'l' => sprintf( 'CPL-Senkung in %s bei E3 New Energy.', $e3_timeframe ),
	],
	[
		'k' => '100 %',
		'l' => 'Eigene Infrastruktur: Code, Daten, CRM-Anschluss und Tracking bleiben beim Betrieb.',
	],
];

// Fit-Check: 3 Voraussetzungen, schärfer auf Qualität und Asset-Verständnis.
$about_fit_points = [
	[
		't' => 'Fokus auf Solar, Wärmepumpe oder Speicher.',
		's' => 'Echte Spezialisierung. Keine branchenübergreifenden Experimente, keine B2B-Generalisten-Lösungen.',
	],
	[
		't' => 'Fest angestelltes Vertriebsteam (min. 2 Personen).',
		's' => 'Das System erzeugt exklusive, hochpreisige Anfragen. Das verlangt strukturierte Bearbeitung — keine Ein-Mann-Betriebe.',
	],
	[
		't' => 'Verständnis für Infrastruktur statt Landingpage.',
		's' => 'Ein eigenes Anfrage-System ist ein digitaler Vermögenswert. Wer eine günstige Landingpage sucht, ist hier falsch.',
	],
];

// Brunnen-Labels: 4 Schichten von oberflächlich zu tief
$about_well_labels = [
	[ 'depth' => 20, 'label' => 'Portal-Leads',  'highlight' => false ],
	[ 'depth' => 40, 'label' => 'Tracking',      'highlight' => false ],
	[ 'depth' => 60, 'label' => 'Infrastruktur', 'highlight' => false ],
	[ 'depth' => 80, 'label' => 'Eigene Quelle', 'highlight' => true  ],
];

// Fachliche Schwerpunkte: System-Architektur in 3 Cluster für besseres Scanning + E-E-A-T-Signal.
$about_expertise_structured = [
	'Infrastruktur & Funnel' => [
		[
			't'   => 'Lead-Funnel-Architektur',
			's'   => 'Fünf Stufen vom Suchwort bis zum Auftrag, mit Vorqualifizierung und Sales-Anschluss.',
			'url' => home_url( '/lead-funnel-solar/' ),
		],
		[
			't'   => 'Mieten vs. Besitzen',
			's'   => 'Vergleichsmatrix Portal-Leads gegen eigenes Anfrage-System mit TCO-Überschlag.',
			'url' => home_url( '/eigene-leadgenerierung-vs-portale/' ),
		],
	],
	'Daten & Tracking' => [
		[
			't'   => 'Server-Side Tracking',
			's'   => 'GA4, Meta CAPI und Consent Mode v2 auf eigenem Server – DSGVO-konform und cookieless-ready.',
			'url' => home_url( '/server-side-tracking-b2b/' ),
		],
		[
			't'   => 'Cost per Lead Photovoltaik',
			's'   => 'CPL-Analyse mit drei Szenarien-Vergleich und versteckten Kostentreibern.',
			'url' => home_url( '/cost-per-lead-photovoltaik/' ),
		],
	],
	'Qualität & Vertrieb' => [
		[
			't'   => 'Qualifizierte PV-Anfragen',
			's'   => 'Vier Merkmale, an denen sich eine hochwertige Solar-Anfrage erkennen lässt.',
			'url' => home_url( '/qualifizierte-pv-anfragen/' ),
		],
		[
			't'   => 'B2B Solar Leads (Gewerbe)',
			's'   => 'Buying-Center-Funnel für gewerbliche Photovoltaik-Projekte ab 50.000 €.',
			'url' => home_url( '/b2b-solar-leads/' ),
		],
	],
];

get_header();
?>

<main id="main" class="site-main">
	<div class="nexus-about" data-track-section="about_page">

		<!-- ════════════════════════════════════════════════════════
		     HERO — mit Brunnen-Visual und Arbeitsweise
		     ════════════════════════════════════════════════════════ -->
		<section id="about-hero" class="about-hero">
			<div class="about-container">
				<div class="about-hero__grid">
					<div class="about-hero__copy">
						<p class="about-eyebrow">
							<span class="about-live-dot" aria-hidden="true"></span>
							ÜBER MICH
						</p>
						<h1 class="about-h1">Ich bohre Brunnen. Für Ihre eigenen Anfragen.</h1>
						<p class="about-hero__lead">
							Für Solar- und Wärmepumpen-Betriebe, die unqualifizierte Portal-Leads durch eine herstellerunabhängige Nachfrage-Infrastruktur ersetzen.
						</p>

						<ul class="about-hero-path" role="list" aria-label="Arbeitsweise von der Analyse bis zur Skalierung">
							<?php foreach ( $about_hero_path as $path_item ) : ?>
								<li class="about-hero-path__item">
									<span class="about-hero-path__title"><?php echo esc_html( $path_item['t'] ); ?></span>
									<span class="about-hero-path__text"><?php echo esc_html( $path_item['s'] ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>

						<div class="about-cta-wrap">
							<a href="<?php echo esc_url( $request_url ); ?>"
							   class="about-cta-primary"
							   data-track-action="cta_about_hero_marktcheck"
							   data-track-category="lead_gen"
							   data-track-section="about_hero">
								<?php echo esc_html( $request_cta ); ?>
								<svg class="about-cta-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
									<path d="M5 12h14M13 6l6 6-6 6"/>
								</svg>
							</a>
							<p class="about-cta-meta">
								<span>Exklusive Erst-Analyse</span>
								<span>Prüfung auf Regions-Verfügbarkeit</span>
								<span>Antwort in 24 h</span>
							</p>
						</div>
					</div>

					<div class="about-hero__visual" aria-hidden="true">
						<div class="about-well-stage">
							<div class="about-well-stage__strata">
								<span></span>
								<span></span>
								<span></span>
								<span></span>
							</div>
							<div class="about-well">
								<div class="about-well__surface"></div>
								<div class="about-well__shaft">
									<div class="about-well__depth-marks">
										<span></span>
										<span></span>
										<span></span>
										<span></span>
									</div>
									<div class="about-well__water" id="aboutWaterLevel">
										<div class="about-well__water-surface"></div>
									</div>
								</div>
								<div class="about-well__base"></div>
								<div class="about-well__labels">
									<?php foreach ( $about_well_labels as $label_item ) : ?>
										<div class="about-well__label<?php echo $label_item['highlight'] ? ' is-highlight' : ''; ?>"
										     data-depth="<?php echo (int) $label_item['depth']; ?>">
											<?php echo esc_html( $label_item['label'] ); ?>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════
		     WIE ICH ARBEITE — Story vor Zahlen
		     ════════════════════════════════════════════════════════ -->
		<section id="about-arbeit" class="about-section">
			<div class="about-container">
				<header class="about-section__head" data-reveal>
					<p class="about-section__number">Wie ich arbeite</p>
					<div class="about-section__head-body">
						<h2 class="about-h2">Drei Schichten. Eine eigene Quelle.</h2>
						<p class="about-section__lead">Meine Arbeit ist keine Design-Schicht über einer alten Vertriebslogik. Sie folgt dem Aufbau eines Brunnens — von den geteilten Portal-Anfragen an der Oberfläche bis zur eigenen Nachfrage-Infrastruktur in der Tiefe.</p>
					</div>
				</header>

				<div class="about-work-grid">
					<?php foreach ( $about_work_principles as $principle ) : ?>
						<article class="about-work-card" data-reveal>
							<p class="about-work-card__eyebrow"><?php echo esc_html( $principle['eyebrow'] ); ?></p>
							<h3 class="about-work-card__title"><?php echo esc_html( $principle['title'] ); ?></h3>
							<p class="about-work-card__body"><?php echo esc_html( $principle['body'] ); ?></p>
							<p class="about-work-card__detail"><?php echo esc_html( $principle['detail'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>

				<div class="about-evidence-band" data-reveal>
					<div class="about-evidence-band__intro">
						<p class="about-evidence-band__eyebrow">Beleg, nicht Aufhänger</p>
						<h3 class="about-evidence-band__title">Die Zahlen kommen nach der Karte.</h3>
						<p>Beim E3-Case sieht man, warum diese Reihenfolge wichtig ist. Die Kennzahlen sind kein Versprechen für jeden Betrieb, sondern ein Beleg dafür, was möglich wird, wenn Nachfrage, Qualifizierung und Vertriebsanschluss zusammenpassen.</p>
					</div>
					<ul class="about-evidence-band__list" role="list" aria-label="Ausgewählte E3-Belege">
						<?php foreach ( $about_evidence as $proof_item ) : ?>
							<li class="about-evidence-band__item">
								<span class="about-evidence-band__k"><?php echo esc_html( $proof_item['k'] ); ?></span>
								<span class="about-evidence-band__l"><?php echo esc_html( $proof_item['l'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════
		     01 / INFRASTRUKTUR STATT MIETE (cream)
		     ════════════════════════════════════════════════════════ -->
		<section id="about-infrastruktur" class="about-section about-section--cream">
			<div class="about-container">
				<header class="about-section__head" data-reveal>
					<p class="about-section__number">01 / Infrastruktur statt Miete</p>
					<div class="about-section__head-body">
						<h2 class="about-h2">Was Sie Ihren Kunden verkaufen, gilt auch für Ihren Vertrieb.</h2>
						<p class="about-section__lead">Eigene Anlagen statt Netz-Abhängigkeit. Eigene Anfragen statt Portal-Miete.</p>
					</div>
				</header>

				<div class="about-prose" data-reveal>
					<p>Solar- und Wärmepumpen-Anbieter verkaufen Unabhängigkeit. Die Anlage auf dem eigenen Dach, nicht die Rechnung vom Versorger. Dieses Prinzip gilt genauso für Ihre Anfrage-Infrastruktur.</p>
					<p>Portale liefern dieselbe Anfrage an drei Wettbewerber. Sie zahlen für Klicks, die nie konvertieren. Sie bauen Listen in fremden Systemen auf, ohne Kontrolle über Daten, Prozesse oder Qualität. Das funktioniert, solange der Markt läuft. Sobald er sich dreht, steigen die Kosten und sinkt die Qualität.</p>
					<p>Eine eigene Anfrage-Infrastruktur macht Region, Projektwert und Fit sichtbar, bevor Ihr Vertrieb Zeit in falsche Gespräche steckt. Sie besitzen die Daten. Sie kontrollieren den Prozess. Sie entscheiden, welche Leads Ihr Team bearbeitet.</p>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════
		     02 / METHODE (main)
		     ════════════════════════════════════════════════════════ -->
		<section id="about-methode" class="about-section">
			<div class="about-container">
				<header class="about-section__head" data-reveal>
					<p class="about-section__number">02 / Methode</p>
					<div class="about-section__head-body">
						<h2 class="about-h2">Diagnose vor Umsetzung.</h2>
						<p class="about-section__lead">Wer bohrt, ohne den Untergrund zu kennen, trifft Stein oder zieht Schlamm.</p>
					</div>
				</header>

				<div class="about-prose" data-reveal>
					<p>Die Arbeit beginnt nicht mit WordPress oder Tracking-Code. Sie beginnt damit, Ihren Untergrund zu lesen: Welche Suchanfragen kommen an? Wo versickert Aufmerksamkeit? Wo ist das Tracking taub? An welchen Stellen erklärt die Seite Technik, statt Entscheidungen zu ermöglichen?</p>
					<p>Manchmal liegt das Problem in der Positionierung. Manchmal im Formular. Manchmal im CRM-Anschluss. Manchmal daran, dass die Website zwar informiert, aber keine Entscheidung vorbereitet. Ich trenne diese Dinge, bevor ich eine Lösung empfehle.</p>
					<p>Erst wenn diese Fragen beantwortet sind, wird gebaut. Sauber strukturiertes WordPress. Serverseitiges Tracking unter Ihrer Kontrolle. Eine Pipeline, die zeigt, welcher Kontakt wertvoll wurde — nicht nur, wer auf einen Button geklickt hat.</p>
					<p>Dann, wenn die Infrastruktur steht, wird skaliert. Vorher Budgets auf Anzeigen zu setzen, erzeugt nur teurere Fehler.</p>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════
		     03 / VORAUSSETZUNGEN (cream + Fit-Check-Card)
		     ════════════════════════════════════════════════════════ -->
		<section id="about-fit" class="about-section about-section--cream">
			<div class="about-container">
				<header class="about-section__head" data-reveal>
					<p class="about-section__number">03 / Voraussetzungen</p>
					<div class="about-section__head-body">
						<h2 class="about-h2">Drei Dinge müssen stimmen.</h2>
						<p class="about-section__lead">Nicht jedes Grundstück trägt Wasser. Manchmal ist die ehrliche Antwort: Hier nicht.</p>
					</div>
				</header>

				<div class="about-prose" data-reveal>
					<p>Damit die Zusammenarbeit funktioniert, müssen drei Voraussetzungen erfüllt sein:</p>
				</div>

				<div class="about-fit-card" data-reveal>
					<p class="about-fit-card__head">Das muss passen:</p>
					<ul class="about-fit-card__list">
						<?php foreach ( $about_fit_points as $fit_item ) : ?>
							<li class="about-fit-card__item">
								<span class="about-fit-card__icon" aria-hidden="true">
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
										<path d="M5 12l5 5L20 7" />
									</svg>
								</span>
								<div class="about-fit-card__body">
									<strong><?php echo esc_html( $fit_item['t'] ); ?></strong>
									<span><?php echo esc_html( $fit_item['s'] ); ?></span>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
					<p class="about-fit-card__match">
						Wenn diese drei Punkte zutreffen, macht der Marktcheck Sinn. Wenn nicht, spare ich Ihnen und mir die Zeit.
					</p>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════
		     04 / HINTERGRUND (main + Portrait + Cohort)
		     ════════════════════════════════════════════════════════ -->
		<section id="about-hintergrund" class="about-section">
			<div class="about-container">
				<header class="about-section__head" data-reveal>
					<p class="about-section__number">04 / Hintergrund</p>
					<div class="about-section__head-body">
						<h2 class="about-h2">Wer ich bin.</h2>
						<p class="about-section__lead">Medienwissenschaft, nicht Webdesign. Sprache und Signal vor Code.</p>
					</div>
				</header>

				<div class="about-portrait-card" data-reveal>
					<figure class="about-portrait-card__media">
						<img src="<?php echo esc_url( $portrait_url ); ?>"
						     alt="Porträt von Haşim Üner"
						     loading="lazy"
						     width="340"
						     height="420">
					</figure>
					<figcaption class="about-portrait-card__caption">Haşim Üner · Hannover</figcaption>
				</div>

				<div class="about-prose" data-reveal>
					<p>Mein Zugang zu dieser Arbeit ist Medienwissenschaft, nicht Webdesign. Ich denke zuerst über Sprache, Entscheidung und Signal nach — und erst danach über Code. Über Jahre habe ich an digitalen Strukturen für erklärungsbedürftige B2B-Angebote gearbeitet. Die technische Schicht war selten das eigentliche Problem. Das Problem war fast immer: jemand hat gebohrt, ohne vorher die Karte zu lesen.</p>
					<p>In der Zusammenarbeit heißt das: Ich höre nicht nur auf Klickzahlen, sondern auf die Fragen, die im Vertrieb wirklich wiederkommen. Ich schaue mir an, welche Einwände Kunden haben, welche Begriffe sie benutzen und welche Informationen fehlen, bevor ein Gespräch produktiv wird.</p>
					<p>Seit E3 New Energy als erstem Solar-Case weiß ich, wo diese Methode am stärksten greift. Seitdem liegt mein Fokus auf Solar- und Wärmepumpen-Betrieben.</p>
				</div>

				<div class="about-cohort-card" data-reveal>
					<p class="about-cohort-card__eyebrow">FOUNDING COHORT 2026</p>
					<h3 class="about-cohort-card__title">E3 New Energy war der erste Case, nicht die Grenze.</h3>
					<p class="about-cohort-card__status">
						<span class="about-cohort-card__dot" aria-hidden="true"></span>
						3 von 3 Plätzen offen
					</p>
					<p class="about-cohort-card__text">
						Die Cohort erweitert diese Arbeitsweise auf maximal drei passende Solar- oder Wärmepumpen-Betriebe. Der Einstieg bleibt der Marktcheck, damit vor einer Umsetzung klar ist, ob Markt, Budget und Tracking-Realität zusammenpassen.
					</p>
					<a href="<?php echo esc_url( $request_url ); ?>"
					   class="about-cta-primary"
					   data-track-action="cta_about_cohort_marktcheck"
					   data-track-category="lead_gen"
					   data-track-section="about_cohort">
						Analyse anfragen
						<svg class="about-cta-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12h14M13 6l6 6-6 6"/>
						</svg>
					</a>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════
		     05 / FACHLICHE SCHWERPUNKTE (cream, Sub-Page-Cluster)
		     ════════════════════════════════════════════════════════ -->
		<section id="about-expertise" class="about-section about-section--cream" aria-labelledby="about-expertise-title">
			<div class="about-container">
				<header class="about-section__head" data-reveal>
					<p class="about-section__number">05 / Fachliche Schwerpunkte</p>
					<div class="about-section__head-body">
						<h2 class="about-h2" id="about-expertise-title">Sechs Felder. Drei System-Schichten.</h2>
						<p class="about-section__lead">Jedes Feld ist als eigene Seite mit Methode, Beispielen und Bezug zum E3-Case aufgeschrieben — gruppiert entlang der Architektur, in der sie wirken.</p>
					</div>
				</header>

				<div class="about-expertise-cluster" data-reveal>
					<?php foreach ( $about_expertise_structured as $expertise_category => $expertise_items ) : ?>
						<div class="about-expertise-column">
							<h3 class="about-expertise-column__title"><?php echo esc_html( $expertise_category ); ?></h3>
							<ul class="about-expertise-list" role="list">
								<?php foreach ( $expertise_items as $field ) : ?>
									<li class="about-expertise-item">
										<a class="about-expertise-link"
										   href="<?php echo esc_url( $field['url'] ); ?>"
										   data-track-action="cta_about_expertise_link"
										   data-track-category="navigation"
										   data-track-section="about_expertise">
											<span class="about-expertise-link__t"><?php echo esc_html( $field['t'] ); ?></span>
											<span class="about-expertise-link__s"><?php echo esc_html( $field['s'] ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<?php
		if ( function_exists( 'hu_render_founding_cohort_block' ) ) {
			echo hu_render_founding_cohort_block(
				[
					'variant' => 'about',
					'id'      => 'founding-cohort-about',
				]
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>

		<!-- ════════════════════════════════════════════════════════
		     FINAL CTA
		     ════════════════════════════════════════════════════════ -->
		<section id="about-close" class="about-section about-section--cream about-final" data-reveal>
			<div class="about-container about-container--centered">
				<h2 class="about-h2">Der nächste Schritt.</h2>
				<p class="about-final__lead">
					Wenn Sie wissen, dass Sie bohren wollen, gehen Sie direkt ins qualifizierte Formular. Exklusive Erst-Analyse, Prüfung auf Regions-Verfügbarkeit, Antwort innerhalb von 24 Stunden per E-Mail. Kein Verkaufsgespräch.
				</p>
				<div class="about-cta-wrap about-cta-wrap--centered">
					<a href="<?php echo esc_url( $request_url ); ?>"
					   class="about-cta-primary"
					   data-track-action="cta_about_final_marktcheck"
					   data-track-category="lead_gen"
					   data-track-section="about_final">
						<?php echo esc_html( $request_cta ); ?>
						<svg class="about-cta-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12h14M13 6l6 6-6 6"/>
						</svg>
					</a>
					<p class="about-cta-meta about-cta-meta--centered">
						<span>Exklusive Erst-Analyse</span>
						<span>Prüfung auf Regions-Verfügbarkeit</span>
						<span>Antwort in 24 h</span>
					</p>
				</div>
			</div>
		</section>

	</div>
</main>

<?php get_footer(); ?>
