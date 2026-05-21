<?php
/**
 * Template Name: Nexus Über Mich
 * Description: Storytelling-Positionsseite für Solar- und Wärmepumpen-Anbieter
 *
 * Redesign nach Claude-Design-Bundle (uber-mich.html):
 * - Warm-cream Farbschema (#FAF7F2 / #F4EDDD / #2A261B)
 * - Infrastruktur-Blueprint als AHA-Element im Hero (scrollbasiert)
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
$request_cta  = function_exists( 'nexus_get_primary_request_cta_label' ) ? nexus_get_primary_request_cta_label() : 'Kostenfreien Marktcheck starten';
$portrait_url = function_exists( 'hu_get_profile_image_url' ) ? hu_get_profile_image_url() : get_stylesheet_directory_uri() . '/assets/img/hasim-portrait.png';

// E3-Canon
$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '9 Monate';
$e3_conv_uplift      = $e3_metrics['sales_conversion_uplift']['display'] ?? '1 – 2 % → 12 %';
$e3_conv_uplift_lbl  = $e3_metrics['sales_conversion_uplift']['label'] ?? 'Anstieg der Abschlussquote durch eigenes System';

// Hero-Pfad: synchron mit den Infrastruktur-Ebenen (Audit -> Signalführung -> Eigentum).
$about_hero_path = [
	[
		't' => '1. System-Auditing',
		's' => 'Website, Tracking, Angebot und CRM-Anschluss werden gegen echte Vertriebssignale geprüft.',
	],
	[
		't' => '2. Signal-Orchestrierung',
		's' => 'Funnel, Server-Side Tracking und Vorqualifizierung trennen Kaufabsicht von bloßer Neugier.',
	],
	[
		't' => '3. Asset-Ownership',
		's' => 'Code, Daten und Anfrageprozess bleiben im Eigentum Ihres Betriebs statt in Portal-Miete.',
	],
];

// Arbeitsweise: konsistent mit dem Infrastruktur-Stack im Hero-Blueprint.
$about_work_principles = [
	[
		'eyebrow' => 'Ebene 1 / Fundament-Check',
		'title'   => 'Portal-Abhängigkeit beenden.',
		'body'    => 'Wer dauerhaft Portal-Leads einkauft, mietet Nachfrage auf fremdem Grund und teilt Kaufinteressenten mit mehreren Wettbewerbern. Ich analysiere, wie viel Vertriebsbudget durch unqualifizierte Kontakte gebunden wird und wo ein eigenes Anfrage-System wirtschaftlich ansetzen muss.',
		'detail'  => 'Erst wenn das digitale Fundament belastbar ist, macht zusätzlicher Traffic Sinn.',
	],
	[
		'eyebrow' => 'Ebene 2 / Daten-Integrität',
		'title'   => 'Messbarkeit, die Verträge zeigt, nicht Klicks.',
		'body'    => 'Standard-Tracking misst Bewegung. Ein echtes Anfrage-System unterscheidet zwischen Formular-Abbruch, qualifizierter Anfrage und unterschriebenem Werksvertrag. Serverseitiges GA4, Meta CAPI und Consent Mode v2 schaffen eine attributionsfähige Datenebene auf Ihrer eigenen Infrastruktur.',
		'detail'  => 'Belastbare First-Party-Daten machen Marketingbudgets steuerbar.',
	],
	[
		'eyebrow' => 'Ebene 3 / System-Architektur',
		'title'   => 'Ein autarkes Anfrage-System.',
		'body'    => 'Nach der Diagnose folgt die technische Umsetzung: ein geschlossenes WordPress-System, intelligente Vorqualifizierungs-Funnel und direkte CRM-Schnittstellen. Keine Standard-Templates, kein Plugin-Bloat. Code, Datenkontrolle und Eigentum liegen vollständig in Ihrer Hand.',
		'detail'  => 'Ein digitaler Vermögenswert, der planbar exklusive Anfragen vorbereitet.',
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

$about_background_points = [
	[
		'k' => 'Bauunternehmer-DNA',
		't' => 'Projektgeschäft ist kein abstraktes Marketingthema.',
		's' => 'Aufgewachsen im Bauunternehmen meines Vaters. Margen, Verantwortung für Teams und der Druck echter Projektentscheidungen waren früh greifbar.',
	],
	[
		'k' => 'Vertrieb',
		't' => 'Eine Website muss Abschlüsse vorbereiten.',
		's' => 'Der digitale Anfrageweg wird aus echten Einwänden, Entscheidungslogik und Vertriebsrealität gebaut — nicht aus Designgeschmack.',
	],
	[
		'k' => 'Medienwissenschaft',
		't' => 'Signal, Kontext und Aufmerksamkeit vor Code.',
		's' => 'Komplexe Informationssysteme werden zerlegt, bis klar ist, wo Vertrauen entsteht, wo Kaufabsicht versickert und welche Daten fehlen.',
	],
];

// Blueprint-Labels: 4 Stationen vom Mietsignal zum eigenen Anfrage-System.
$about_well_labels = [
	[ 'depth' => 20, 'label' => 'Miet-Leads (Verlustzone)', 'highlight' => false ],
	[ 'depth' => 40, 'label' => 'First-Party-Datenebene',   'highlight' => false ],
	[ 'depth' => 60, 'label' => 'Vorqualifizierungs-Funnel', 'highlight' => false ],
	[ 'depth' => 80, 'label' => 'Autarkes Anfrage-System',   'highlight' => true  ],
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
		     HERO — mit Infrastruktur-Blueprint und Arbeitsweise
		     ════════════════════════════════════════════════════════ -->
		<section id="about-hero" class="about-hero">
			<div class="about-container">
				<div class="about-hero__grid">
					<div class="about-hero__copy">
						<p class="about-eyebrow">
							<span class="about-live-dot" aria-hidden="true"></span>
							ÜBER MICH
						</p>
						<h1 class="about-h1">Ich baue Anfrage-Systeme, die Ihrem Betrieb gehören.</h1>
						<p class="about-hero__lead">
							Für Solar- und Wärmepumpen-Betriebe, die Portal-Abhängigkeit durch eigene Daten, bessere Vorqualifizierung und sauberen CRM-Anschluss ersetzen.
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
								<span>Händischer Befund in 48 h</span>
							</p>
						</div>
					</div>

					<div class="about-hero__visual" aria-hidden="true">
						<div class="about-well-stage about-blueprint-stage">
							<div class="about-blueprint__grid"></div>
							<div class="about-well about-blueprint" id="aboutDemandGrid">
								<div class="about-blueprint__header">
									<span>Nachfrage-Infrastruktur</span>
									<strong>Eigentum statt Portal-Miete</strong>
								</div>

								<div class="about-blueprint__node about-blueprint__node--source">
									<span>Traffic</span>
									<small>SEO / Ads / Local</small>
								</div>

								<div class="about-blueprint__node about-blueprint__node--sales">
									<span>CRM / Vertrieb</span>
									<small>Fit, Region, Projektwert</small>
								</div>

								<svg class="about-blueprint__diagram" viewBox="0 0 520 420" focusable="false" aria-hidden="true">
									<defs>
										<filter id="aboutBlueprintGlow" x="-20%" y="-20%" width="140%" height="140%">
											<feGaussianBlur stdDeviation="3.5" result="blur" />
											<feMerge>
												<feMergeNode in="blur" />
												<feMergeNode in="SourceGraphic" />
											</feMerge>
										</filter>
									</defs>
									<path class="about-blueprint__line about-blueprint__line--base" d="M88 116 C150 116 163 86 238 86" />
									<path class="about-blueprint__line about-blueprint__line--base" d="M88 116 C156 116 166 164 238 164" />
									<path class="about-blueprint__line about-blueprint__line--base" d="M88 116 C156 116 166 242 238 242" />
									<path class="about-blueprint__line about-blueprint__line--base" d="M88 116 C158 116 160 320 238 320 C330 320 354 282 432 282" />
									<path class="about-blueprint__line" data-depth="20" d="M88 116 C150 116 163 86 238 86" />
									<path class="about-blueprint__line" data-depth="40" d="M88 116 C156 116 166 164 238 164" />
									<path class="about-blueprint__line" data-depth="60" d="M88 116 C156 116 166 242 238 242" />
									<path class="about-blueprint__line" data-depth="80" d="M88 116 C158 116 160 320 238 320 C330 320 354 282 432 282" />
								</svg>

								<div class="about-well__labels about-blueprint__layers">
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
						<h2 class="about-h2">Drei Ebenen. Ein eigenes Anfrage-System.</h2>
						<p class="about-section__lead">Meine Arbeit ist keine neue Oberfläche über einer alten Vertriebslogik. Sie folgt dem Aufbau einer kontrollierbaren Infrastruktur: von Portal-Abhängigkeit über Daten-Integrität bis zum eigenen Vertriebsanschluss.</p>
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
						<h3 class="about-evidence-band__title">Die Zahlen kommen nach der Diagnose.</h3>
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
						<p class="about-section__lead">Wer skaliert, bevor Angebot, Daten und Vertriebssystem verstanden sind, macht bestehende Fehler nur teurer.</p>
					</div>
				</header>

				<div class="about-prose" data-reveal>
					<p>Die Arbeit beginnt nicht mit WordPress oder Tracking-Code. Sie beginnt mit einer Systemdiagnose: Welche Suchanfragen kommen an? Wo bricht Kaufabsicht ab? Wo fehlt dem Vertrieb die Information, um gute Gespräche schnell zu erkennen?</p>
					<p>Manchmal liegt das Problem in der Positionierung. Manchmal im Formular. Manchmal im CRM-Anschluss. Manchmal daran, dass die Website zwar informiert, aber keine Entscheidung vorbereitet. Ich trenne diese Ursachen, bevor ich eine Lösung empfehle.</p>
					<p>Erst wenn diese Fragen beantwortet sind, wird gebaut. Sauber strukturiertes WordPress. Serverseitiges Tracking unter Ihrer Kontrolle. Eine Pipeline, die zeigt, welcher Kontakt wertvoll wurde, nicht nur, wer auf einen Button geklickt hat.</p>
					<p>Dann, wenn die Infrastruktur steht, wird skaliert. Vorher Budgets auf Anzeigen zu setzen, macht die falschen Signale nur lauter.</p>
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
						<p class="about-section__lead">Nicht jeder Betrieb braucht sofort ein eigenes Anfrage-System. Manchmal ist die ehrliche Antwort: noch nicht.</p>
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
						<p class="about-section__lead">Bauunternehmer-DNA, Vertrieb und Medienwissenschaft. Sprache und Signal vor Code.</p>
					</div>
				</header>

				<div class="about-background-grid" data-reveal>
					<?php foreach ( $about_background_points as $point ) : ?>
						<article class="about-background-card">
							<p class="about-background-card__kicker"><?php echo esc_html( $point['k'] ); ?></p>
							<h3 class="about-background-card__title"><?php echo esc_html( $point['t'] ); ?></h3>
							<p class="about-background-card__text"><?php echo esc_html( $point['s'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>

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
					<p>Mein Vater war Bauunternehmer. Ich bin mit dem Wissen aufgewachsen, was es bedeutet, Verantwortung für Projekte, Margen und ein fest angestelltes Team zu tragen. Vertrieb und Unternehmertum wurden mir nicht in Seminaren beigebracht — sie sind der Kontext, aus dem ich Websites bewerte.</p>
					<p>Mein Studium der Medienwissenschaft an der Universität Paderborn war dafür der analytische Werkzeugkasten. Ich analysiere, wie Daten fließen, wo Aufmerksamkeit im Funnel versickert und welche unsichtbaren Signale zwischen digitaler Oberfläche und B2B-Entscheider übertragen werden müssen, damit Vertrauen entsteht.</p>
					<p>Die meisten WordPress-Websites scheitern nicht am Design oder an fehlenden Plugins. Sie scheitern daran, dass die technische Architektur isoliert von der vertrieblichen Realität gebaut wurde. Ich entwickle Systeme auf Basis echter Vertriebsgespräche, damit Einwände, Sprachmuster und Entscheidungshürden direkt in Landingpages, Tracking und Anfragepfad übersetzt werden.</p>
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
						<h2 class="about-h2" id="about-expertise-title">Sechs Felder. Drei System-Ebenen.</h2>
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
					Wenn Sie Portal-Abhängigkeit durch ein eigenes Anfrage-System ersetzen wollen, gehen Sie direkt ins qualifizierte Formular. Manueller, tiefer Marktcheck, händische Prüfung der Regions-Verfügbarkeit, Befund innerhalb von 48 Stunden per E-Mail. Kein Verkaufsgespräch.
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
						<span>Händischer Befund in 48 h</span>
					</p>
				</div>
			</div>
		</section>

	</div>
</main>

<?php get_footer(); ?>
