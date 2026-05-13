<?php
/**
 * /solar-waermepumpen-leadgenerierung/
 *
 * Ingenieur-Ästhetik. Kein Dekor. Nur Argument.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$request_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/anfrage-system-analyse/' );
$page_url    = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
$e3_url      = home_url( '/e3-new-energy/' );
$e3_canon    = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics  = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];

$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? '-85,3 %';
$e3_cpl_before       = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after        = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '9 Monate';
$e3_timeframe_dative = $e3_metrics['timeframe']['display_dative'] ?? '9 Monaten';

$pain_items = [
	[
		'n'    => '01',
		'lbl'  => 'Lead-Einkauf',
		'q'    => '80–150 € pro Lead — und die Hälfte geht nicht ans Telefon.',
		'd'    => 'Sie kaufen bei Aroundhome, Check24 oder DAA. Aber: Mieter ohne Dach, Preisvergleicher ohne Budget, Kontakte, die schon bei drei Mitbewerbern angefragt haben. Ihr Vertrieb verbrennt Stunden mit Leuten, die nie kaufen werden.',
	],
	[
		'n'    => '02',
		'lbl'  => 'Blindflug',
		'q'    => 'Kein Überblick, welcher Kanal sich wirklich lohnt.',
		'd'    => 'Google Ads, Portal-Leads, Empfehlungen — niemand kann sauber sagen, woher die guten Abschlüsse kommen. Ohne diese Klarheit investieren Sie blind. Und skalieren falsch.',
	],
	[
		'n'    => '03',
		'lbl'  => 'Marktwende',
		'q'    => 'Seit 2024 kommen Anfragen nicht mehr von allein.',
		'd'    => 'Der PV-Boom ist vorbei. Der Markt normalisiert sich. Wer jetzt keine eigene Anfrage-Infrastruktur hat, ist abhängig von Portalen — und deren Preisen.',
	],
];

$arch_full = [
	[
		'n'   => '01',
		't'   => 'Landingpage',
		'd'   => 'Besucher kommt über Google, Ads oder Empfehlung. WordPress mit hardcoded HTML, kein Page-Builder, kein Plugin-Sumpf.',
		'tag' => '8-Sekunden-Regel',
	],
	[
		'n'   => '02',
		't'   => 'Qualifizierung',
		'd'   => '60-Sekunden-Funnel prüft Budget, Betriebsgröße und Bedarf. Mieter ohne Dach werden vor dem Anruf aussortiert.',
		'tag' => 'React-Funnel',
	],
	[
		'n'   => '03',
		't'   => 'Segmentierung',
		'd'   => 'n8n routet die Anfrage nach Region, Gewerk und Dringlichkeit in den richtigen Topf — automatisch.',
		'tag' => 'n8n · Routing',
	],
	[
		'n'   => '04',
		't'   => 'Benachrichtigung',
		'd'   => 'Vertrieb bekommt Slack oder Mail mit Lead-Score und Kontakt — in Echtzeit. Kein Lead liegt länger als 5 Minuten.',
		'tag' => 'Realtime · CRM',
	],
	[
		'n'   => '05',
		't'   => 'Bewertung',
		'd'   => 'Lead-Scoring priorisiert: heiß, warm, kalt. Ihr Vertrieb sieht auf einen Blick, wen er zuerst anruft.',
		'tag' => 'Scoring-Engine',
	],
];

$twoways_a = [
	[ 'k' => 'bis 150 €',     'v' => 'pro Lead — und 50 % gehen nicht ans Telefon.' ],
	[ 'k' => 'Kein Überblick', 'v' => 'Welcher Kanal lohnt sich wirklich? Niemand sagt es Ihnen.' ],
	[ 'k' => 'Abhängigkeit',   'v' => 'Budget-Stopp = Nachfrage-Stopp. Sie mieten — Sie besitzen nichts.' ],
];

$twoways_b = [
	[ 'n' => '01', 'k' => 'Fundament ordnen',           'v' => 'Tracking & Datenebene · Privacy-first · saubere Entscheidungssignale.' ],
	[ 'n' => '02', 'k' => 'Conversion-Pfade schärfen',  'v' => 'Formular · Call · Buchung · 8-Sekunden-Regel auf jeder Money-Page.' ],
	[ 'n' => '03', 'k' => 'Skalieren',                  'v' => 'Money Pages & Proof · bleibende Assets · Unabhängigkeit von Portalen.' ],
];

$tco_rows = [
	[
		'lbl' => 'Initiales Setup',
		'a'   => [ '~ 2.000 €', 'Setup-Pauschale, Funnel-Lizenz' ],
		'b'   => [ '12.000 – 18.000 €', 'einmalig — eigene Code-Basis' ],
	],
	[
		'lbl' => 'Monatlich (ohne Werbebudget)',
		'a'   => [ '~ 850 € + 150 €', 'Honorar + SaaS / CRM-Lizenz' ],
		'b'   => [ '~ 50 €', 'Hochleistungs-Hosting' ],
	],
	[
		'lbl' => 'TCO über 24 Monate',
		'a'   => [ '~ 26.000 €', 'fließt durch die GuV ab' ],
		'b'   => [ '13.200 – 19.200 €', 'günstiger UND aktivierbar' ],
	],
	[
		'lbl' => 'Bilanzwirkung',
		'a'   => [ 'OPEX', 'fließt vollständig durch die GuV' ],
		'b'   => [ 'CAPEX', 'aktivierbares Asset' ],
	],
	[
		'lbl' => 'Eigentum nach Kündigung',
		'a'   => [ '0 %', 'Funnel · CRM · Tracking abgeschaltet' ],
		'b'   => [ '100 %', 'Code · Tracking · Daten bleiben' ],
	],
];

$owned_items = [
	[
		'n' => '01',
		't' => 'Eigene WordPress-Assets',
		'd' => 'Eine conversion-optimierte Money-Page sowie Proof- und Angebotsseiten. Hardcoded — kein Page-Builder, kein Plugin-Stack. Bleibt langfristig in Ihrem Eigentum.',
	],
	[
		'n' => '02',
		't' => 'Vorqualifizierung & Übergabe',
		'd' => 'Smarte Formulare filtern unpassende Anfragen automatisch heraus. CRM-Anbindung folgt erst nach Contract, Consent und klarer Datenlogik.',
	],
	[
		'n' => '03',
		't' => 'Echtes Tracking',
		'd' => 'Sie sehen, welche Kampagne welche Anfragen und Termine erzeugt. Volle Messbarkeit der Leadquellen — auf Ihrem Server, in Ihrem Account.',
	],
	[
		'n' => '04',
		't' => 'Grundlage für Skalierung',
		'd' => 'Mit diesem Fundament skalieren Sie Google Ads, Meta Ads und SEO endlich wirtschaftlich. Mehr Reichweite zahlt jetzt aufs eigene System ein.',
	],
];

$tracking_items = [
	[
		'k' => 'Frankfurt · DSGVO',
		't' => 'First-Party-Kontext',
		'd' => 'Der Tracking-Server läuft auf Ihrer Subdomain. Ad-Blocker und ITP erkennen keine blockierbaren Drittanbieter-Skripte — Ihre Conversion-Daten bleiben vollständig.',
	],
	[
		'k' => 'S2S · CAPI',
		't' => 'Algorithmisches Training',
		'd' => 'Saubere, serverseitige Conversion-Signale fließen direkt per Server-to-Server-API an Meta und Google Ads. Die Algorithmen lernen präziser — und senken Ihren CPL.',
	],
	[
		'k' => 'Privacy-First',
		't' => 'DSGVO & Consent',
		'd' => 'IP-Adressen werden auf Ihrem Server anonymisiert, bevor Daten externe Netzwerke erreichen. Conversion-Pfade bleiben auch bei Cookie-Ablehnung statistisch valide messbar.',
	],
];

$proof_phases = [
	[
		'k' => 'Vorher',
		'v' => 'Hohe Abhängigkeit von externen Leadquellen (~150 €/Lead). Leads schwer erreichbar, kaum qualifiziert. Kein Überblick, welche Kanäle Termine und Abschlüsse erzeugen.',
	],
	[
		'k' => 'Umsetzung',
		'v' => 'Tracking-Fundament aufgebaut, Anfragepfade optimiert, smarte Vorqualifizierung eingeführt. Saubere CRM-Übergabe vorbereitet.',
	],
	[
		'k' => 'Ergebnis · 9 Monate',
		'v' => '1.750+ qualifizierte Anfragen · 12 % Abschlussquote · −85,3 % CPL (auf 22 € gesenkt).',
	],
];

$about_metrics = [
	[ 'v' => '9',   'l' => 'Jahre WordPress' ],
	[ 'v' => 'B2B', 'l' => 'Ausschließlich' ],
	[ 'v' => '1:1', 'l' => 'Einzelperson' ],
];

$faq_items = [
	[
		'question' => 'Was kostet das im Vergleich zur Performance-Agentur?',
		'answer'   => 'Initiales Setup: 12.000–18.000 € einmalig. Laufend ca. 50 €/Monat für Hochleistungs-Hosting. TCO über 24 Monate: 13.200–19.200 € — und Sie besitzen Code, Tracking und Daten. Eine Performance-Agentur mit Paket „Regio+" kostet im gleichen Zeitraum rund 26.000 € und Sie besitzen am Ende nichts. Bilanziell: CAPEX statt OPEX.',
	],
	[
		'question' => 'Funktioniert das auch für kleinere Betriebe mit 5–10 Mitarbeitern?',
		'answer'   => 'Belastbar ab 10 Mitarbeitern und mindestens 20 qualifizierten Anfragen pro Monat. Darunter trägt die Investition den TCO-Vorteil nicht — ehrlicher Hinweis: ein schlankerer Ansatz mit klarer Landingpage und sauberem Tracking ist dann der bessere Hebel.',
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
	[
		'question' => 'Arbeiten Sie mit unserem bestehenden CRM?',
		'answer'   => 'Ja, sofern technisch sinnvoll. CRM- und Automations-Anbindung kommt aber erst nach sauberer Contract-, Consent- und Datenklärung. Der erste Schritt ist die Anfrage-System-Analyse ohne personenbezogene Daten im Default-Pfad.',
	],
];

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
		'description'   => 'Diagnostische System-Einordnung. Keine E-Mail-Pflicht im Default-Pfad.',
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

$arch_steps = [
	[ 'n' => '01', 'lbl' => 'Landing',        'note' => 'Google → WordPress' ],
	[ 'n' => '02', 'lbl' => 'Qualifizierung', 'note' => '60s-Funnel · React' ],
	[ 'n' => '03', 'lbl' => 'Segmentierung',  'note' => 'n8n · Routing' ],
	[ 'n' => '04', 'lbl' => 'Scoring',        'note' => 'Heiß / Warm / Kalt' ],
	[ 'n' => '05', 'lbl' => 'Vertrieb',       'note' => 'Slack · CRM · 5 Min.' ],
];

get_header();
?>
<main id="main" class="site-main">
	<div class="energy-page-wrapper solar-page" data-track-section="energy_service_landing">

		<section class="nx-section solar-hero" id="hero">
			<div class="solar-hero__inner">
				<div class="solar-hero__eyebrow-row">
					<span class="solar-hero__eyebrow">Für Solar- und Wärmepumpen-Betriebe · 10–25 Mitarbeiter</span>
					<span class="solar-hero__meta">Pattensen · Region Hannover · Q2 / 2026</span>
				</div>

				<h1 class="solar-hero__headline">
					Hören Sie auf, <em>Anfragen zu mieten.</em><br />
					Bauen Sie eine, <span class="solar-hero__nowrap">die <em>Ihnen gehört.</em></span>
				</h1>

				<div class="solar-hero__grid">
					<div class="solar-hero__lede-col">
						<p class="solar-hero__lede">
							Portal-Leads kosten 80–150 €. Die Hälfte geht nicht ans Telefon.
							Wir bauen Ihrem Betrieb stattdessen ein eigenes Anfrage-System &mdash;
							mit WordPress-Infrastruktur, serverseitigem Tracking und Vorqualifizierung.
							Das System gehört Ihnen. Die Anfragen sind exklusiv.
						</p>

						<div class="solar-hero__cta-row">
							<a href="<?php echo esc_url( $request_url ); ?>" class="solar-hero__btn" data-track-action="cta_solar_hero_primary" data-track-category="lead_gen">
								<span>Anfrage-System-Analyse starten</span>
								<span class="solar-hero__btn-arrow" aria-hidden="true">
									<svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
										<path d="M3 9h12m0 0l-5-5m5 5l-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</span>
							</a>
						</div>

						<p class="solar-hero__micro">
							<span class="solar-hero__dot" aria-hidden="true"></span>
							8 Schritte · lokales Ergebnis · keine E-Mail im Default-Pfad
						</p>
					</div>

					<aside class="solar-arch" aria-label="System-Architektur — Vorschau">
						<div class="solar-arch__head">
							<span class="solar-arch__label">System · Live</span>
							<span class="solar-arch__status">
								<span class="solar-hero__dot solar-hero__dot--signal" aria-hidden="true"></span>
								Routing OK
							</span>
						</div>

						<div class="solar-arch__rows" data-solar-arch-rows>
							<?php foreach ( $arch_steps as $i => $step ) : ?>
								<div class="solar-arch__row<?php echo 0 === $i ? ' is-active' : ''; ?>">
									<span class="solar-arch__num"><?php echo esc_html( $step['n'] ); ?></span>
									<span class="solar-arch__name"><?php echo esc_html( $step['lbl'] ); ?></span>
									<span class="solar-arch__note"><?php echo esc_html( $step['note'] ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>

						<div class="solar-arch__foot">
							<span class="solar-arch__label">Ø 4 Min · Anfrage → Anruf</span>
							<span class="solar-arch__cpl"><?php echo esc_html( $e3_cpl_after ); ?><span>CPL</span></span>
						</div>
					</aside>
				</div>

				<div class="solar-hero__hairline" role="presentation"></div>

				<div class="solar-proof">
					<div class="solar-proof__cell">
						<div class="solar-proof__num"><?php echo esc_html( $e3_lead_count ); ?></div>
						<div class="solar-proof__lbl">qualifizierte Anfragen</div>
						<div class="solar-proof__sub">in <?php echo esc_html( $e3_timeframe_dative ); ?></div>
					</div>
					<div class="solar-proof__cell">
						<div class="solar-proof__num"><?php echo esc_html( $e3_sales_conversion ); ?></div>
						<div class="solar-proof__lbl">Abschlussquote</div>
						<div class="solar-proof__sub">von Anfrage zu Vertrag</div>
					</div>
					<div class="solar-proof__cell">
						<div class="solar-proof__num"><?php echo esc_html( $e3_cpl_reduction ); ?></div>
						<div class="solar-proof__lbl">Kosten pro Anfrage</div>
						<div class="solar-proof__sub"><?php echo esc_html( $e3_cpl_before ); ?> &rarr; <?php echo esc_html( $e3_cpl_after ); ?></div>
					</div>
					<div class="solar-proof__cell">
						<div class="solar-proof__num">100&nbsp;%</div>
						<div class="solar-proof__lbl">Eigentum am System</div>
						<div class="solar-proof__sub">Code · Tracking · Daten</div>
					</div>
				</div>
			</div>
		</section>

		<!-- 02 · Pain (dark) ──────────────────────────────────────── -->
		<section class="solar-section solar-section--dark" id="pain" data-screen-label="02 Pain" aria-labelledby="pain-title">
			<div class="solar-section__inner">
				<div class="solar-secthead">
					<div class="solar-secthead__row">
						<span class="solar-secnum">02 / Alltag im Vertrieb</span>
						<span class="solar-secthead__rule" aria-hidden="true"></span>
					</div>
					<h2 id="pain-title" class="solar-h2">Kommt Ihnen das <em>bekannt vor?</em></h2>
				</div>

				<div class="solar-pain-grid">
					<?php foreach ( $pain_items as $item ) : ?>
						<article class="solar-pain-card" data-reveal>
							<div class="solar-pain-card__num"><?php echo esc_html( $item['n'] ); ?></div>
							<h3 class="solar-pain-card__q"><?php echo esc_html( $item['q'] ); ?></h3>
							<p class="solar-pain-card__d"><?php echo esc_html( $item['d'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- 03 · Architektur (paper) ─────────────────────────────── -->
		<section class="solar-section solar-section--paper" id="system" data-screen-label="03 System" aria-labelledby="system-title">
			<div class="solar-section__inner">
				<div class="solar-secthead">
					<div class="solar-secthead__row">
						<span class="solar-secnum">03 / System-Architektur</span>
						<span class="solar-secthead__rule" aria-hidden="true"></span>
					</div>
					<h2 id="system-title" class="solar-h2">Das ist keine Website. <em>Das ist eine Lead-Verarbeitungsmaschine.</em></h2>
					<p class="solar-lede">Sehen Sie den kompletten Weg einer Anfrage — von der Landung bis zur priorisierten Vertriebsübergabe. Jede Stufe automatisiert. Jede Stufe in Ihrem Eigentum.</p>
				</div>

				<div class="solar-arch-list" role="list">
					<?php foreach ( $arch_full as $step ) : ?>
						<div class="solar-arch-list__row" role="listitem" data-reveal>
							<div class="solar-arch-list__num"><?php echo esc_html( $step['n'] ); ?></div>
							<div class="solar-arch-list__body">
								<h3 class="solar-arch-list__t"><?php echo esc_html( $step['t'] ); ?></h3>
								<p class="solar-arch-list__d"><?php echo esc_html( $step['d'] ); ?></p>
							</div>
							<div class="solar-arch-list__tag">
								<span class="solar-tag"><?php echo esc_html( $step['tag'] ); ?></span>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<div class="solar-aha" data-reveal>
					<div class="solar-aha__icon" aria-hidden="true">↺</div>
					<div class="solar-aha__body">
						<div class="solar-aha__kicker">Der Aha-Moment</div>
						<p class="solar-aha__text">
							Wettbewerber telefonieren jeden Lead einzeln ab und hoffen, dass er passt.
							Ihr System sortiert vor. Sie rufen <em>nur noch die an, die wirklich kaufen wollen</em> &mdash;
							und zwar in der richtigen Reihenfolge.
						</p>
					</div>
				</div>
			</div>
		</section>

		<!-- 04 · Zwei Wege (paper, main comparison) ───────────────── -->
		<section class="solar-section solar-section--paper-2" id="modelle" data-screen-label="04 Zwei Wege" aria-labelledby="modelle-title">
			<div class="solar-section__inner">
				<div class="solar-secthead">
					<div class="solar-secthead__row">
						<span class="solar-secnum">04 / Zwei Wege</span>
						<span class="solar-secthead__rule" aria-hidden="true"></span>
					</div>
					<h2 id="modelle-title" class="solar-h2">Nachfrage <em>mieten</em> &mdash; oder <em>eigene Anfrage-Infrastruktur</em> aufbauen.</h2>
					<p class="solar-lede">Zwei Modelle, zwei Wirtschaftlichkeiten. Das eine zahlt jeden Monat neu auf Portal-Konten ein. Das andere baut ein Anfrage-System, das in 9 Monaten über 85&nbsp;% günstiger arbeitet.</p>
				</div>

				<div class="solar-ways">
					<!-- Modell A -->
					<div class="solar-way solar-way--a" data-reveal>
						<div class="solar-way__head">
							<span class="solar-way__label">Modell A</span>
							<span class="solar-way__status solar-way__status--warn">
								<span class="solar-dot solar-dot--warn" aria-hidden="true"></span>
								Status Quo
							</span>
						</div>
						<h3 class="solar-way__title">Nachfrage mieten</h3>
						<div class="solar-way__sub">Aroundhome · Check24 · DAA</div>

						<div class="solar-way__items">
							<?php foreach ( $twoways_a as $idx => $row ) : ?>
								<div class="solar-way__item solar-way__item--a<?php echo 0 === $idx ? ' is-first' : ''; ?>">
									<div class="solar-way__k"><?php echo esc_html( $row['k'] ); ?></div>
									<div class="solar-way__v"><?php echo esc_html( $row['v'] ); ?></div>
								</div>
							<?php endforeach; ?>
						</div>

						<div class="solar-pill solar-pill--warn">
							<span class="solar-dot solar-dot--warn" aria-hidden="true"></span>
							<span>Teuer · Kurzlebig</span>
						</div>
					</div>

					<!-- Modell B -->
					<div class="solar-way solar-way--b" data-reveal>
						<div class="solar-way__glow" aria-hidden="true"></div>
						<div class="solar-way__head">
							<span class="solar-way__label">Modell B</span>
							<span class="solar-way__status solar-way__status--signal">
								<span class="solar-dot solar-dot--signal" aria-hidden="true"></span>
								Empfohlen
							</span>
						</div>
						<h3 class="solar-way__title solar-way__title--bone">Eigenes Anfrage-System</h3>
						<div class="solar-way__sub solar-way__sub--bone">Fundament · Conversion · Skalierung</div>

						<div class="solar-way__items">
							<?php foreach ( $twoways_b as $idx => $row ) : ?>
								<div class="solar-way__item solar-way__item--b<?php echo 0 === $idx ? ' is-first' : ''; ?>">
									<span class="solar-way__num"><?php echo esc_html( $row['n'] ); ?></span>
									<div>
										<div class="solar-way__k solar-way__k--bone"><?php echo esc_html( $row['k'] ); ?></div>
										<div class="solar-way__v solar-way__v--bone"><?php echo esc_html( $row['v'] ); ?></div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>

						<div class="solar-pill solar-pill--solar">
							<span class="solar-dot solar-dot--signal" aria-hidden="true"></span>
							<span>Bleibendes Anfrage-Asset</span>
						</div>
					</div>
				</div>

				<div class="solar-ref" data-reveal>
					<div class="solar-ref__label">Referenz <?php echo esc_html( $e3_case_label ); ?> · <?php echo esc_html( $e3_timeframe ); ?></div>
					<div class="solar-ref__stats">
						<div class="solar-ref__stat">
							<span class="solar-ref__num"><?php echo esc_html( $e3_cpl_reduction ); ?></span>
							<span class="solar-ref__lbl">CPL</span>
						</div>
						<div class="solar-ref__stat">
							<span class="solar-ref__num"><?php echo esc_html( $e3_lead_count ); ?></span>
							<span class="solar-ref__lbl">Anfragen</span>
						</div>
						<div class="solar-ref__stat">
							<span class="solar-ref__num"><?php echo esc_html( $e3_sales_conversion ); ?></span>
							<span class="solar-ref__lbl">Abschluss</span>
						</div>
					</div>
					<a href="<?php echo esc_url( $request_url ); ?>" class="solar-hero__btn" data-track-action="cta_solar_ref" data-track-category="lead_gen">
						<span>Analyse starten</span>
						<span class="solar-hero__btn-arrow" aria-hidden="true">
							<svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false"><path d="M3 9h12m0 0l-5-5m5 5l-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</span>
					</a>
				</div>
			</div>
		</section>

		<!-- 05 · TCO (dark, CAPEX vs OPEX) ───────────────────────── -->
		<section class="solar-section solar-section--dark" id="tco" data-screen-label="05 TCO" aria-labelledby="tco-title">
			<div class="solar-section__inner">
				<div class="solar-secthead">
					<div class="solar-secthead__row">
						<span class="solar-secnum">05 / CAPEX statt OPEX</span>
						<span class="solar-secthead__rule" aria-hidden="true"></span>
					</div>
					<h2 id="tco-title" class="solar-h2">Der gleiche Hebel &mdash; <em>zwei Bilanzwirkungen.</em></h2>
					<p class="solar-lede">Performance-Agenturen verkaufen Ihnen Reichweite zur Miete: Funnel auf deren Server, CRM unter deren Lizenz, Tracking unter deren Account. Vertrag endet &mdash; Hebel weg. Der gleiche monatliche Betrag fließt 24 Monate in ein System, das Ihnen nicht gehört.</p>
				</div>

				<div class="solar-tco-wrap" data-reveal>
					<table class="solar-tco">
						<caption class="screen-reader-text">Kostenvergleich Mietsystem vs. Infrastruktur-Aufbau über 24 Monate.</caption>
						<thead>
							<tr>
								<th scope="col" class="solar-tco__col-lbl">Kostenpunkt</th>
								<th scope="col">
									<span class="solar-tco__col-h">Mietsystem</span>
									<span class="solar-tco__col-s">Performance-Agentur · Paket „Regio+"</span>
								</th>
								<th scope="col" class="solar-tco__col-own">
									<span class="solar-tco__col-h">Infrastruktur-Aufbau</span>
									<span class="solar-tco__col-s solar-tco__col-s--solar">WGOS · Code im Eigentum</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $tco_rows as $row ) : ?>
								<tr>
									<th scope="row" class="solar-tco__lbl"><?php echo esc_html( $row['lbl'] ); ?></th>
									<td class="solar-tco__rent">
										<span class="solar-tco__b"><?php echo esc_html( $row['a'][0] ); ?></span>
										<span class="solar-tco__s"><?php echo esc_html( $row['a'][1] ); ?></span>
									</td>
									<td class="solar-tco__own">
										<span class="solar-tco__b solar-tco__b--solar"><?php echo esc_html( $row['b'][0] ); ?></span>
										<span class="solar-tco__s"><?php echo esc_html( $row['b'][1] ); ?></span>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<p class="solar-tco-coda" data-reveal>
					Nach 24 Monaten haben Sie im Mietmodell rund <span class="solar-tco-coda__warn">26.000&nbsp;€</span> ausgegeben und besitzen <em>nichts</em>.
					Im Infrastruktur-Modell haben Sie <span class="solar-tco-coda__solar">weniger</span> ausgegeben und ein <em>laufendes System</em> in Ihrer Bilanz.
				</p>
			</div>
		</section>

		<!-- 06 · Owned Assets (paper-2) ──────────────────────────── -->
		<section class="solar-section solar-section--paper-2" id="owned" data-screen-label="06 Owned" aria-labelledby="owned-title">
			<div class="solar-section__inner">
				<div class="solar-secthead">
					<div class="solar-secthead__row">
						<span class="solar-secnum">06 / Keine Miet-Leads</span>
						<span class="solar-secthead__rule" aria-hidden="true"></span>
					</div>
					<h2 id="owned-title" class="solar-h2">Was Ihr Betrieb <em>danach besitzt.</em></h2>
					<p class="solar-lede">Kein Lead-Paket. Keine gemietete Landingpage. Kein Agentur-Blindflug. Ein eigenes Anfrage-System &mdash; vier Bestandteile, alle in Ihrem Eigentum.</p>
				</div>

				<div class="solar-owned">
					<?php foreach ( $owned_items as $item ) : ?>
						<article class="solar-owned__cell" data-reveal>
							<div class="solar-owned__head">
								<span class="solar-owned__num"><?php echo esc_html( $item['n'] ); ?> / 04</span>
								<span class="solar-dot solar-dot--signal" aria-hidden="true"></span>
							</div>
							<h3 class="solar-owned__t"><?php echo esc_html( $item['t'] ); ?></h3>
							<p class="solar-owned__d"><?php echo esc_html( $item['d'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- 07 · Server-Side Tracking (dark) ─────────────────────── -->
		<section class="solar-section solar-section--dark" id="tracking" data-screen-label="07 Tracking" aria-labelledby="tracking-title">
			<div class="solar-section__inner">
				<div class="solar-secthead">
					<div class="solar-secthead__row">
						<span class="solar-secnum">07 / Unfairer Vorteil</span>
						<span class="solar-secthead__rule" aria-hidden="true"></span>
					</div>
					<h2 id="tracking-title" class="solar-h2">
						Server-Side Tracking:<br>
						Der Unterschied zwischen <em>Blindflug</em> und <em>messbarem Wachstum.</em>
					</h2>
					<p class="solar-lede">Die meisten Agenturen tracken clientseitig &mdash; und verlieren bis zu 40&nbsp;% der Conversion-Daten an Ad-Blocker, ITP und Firmen-Firewalls. Ihr System bekommt eine eigene Tracking-Infrastruktur auf einem Server in Frankfurt. Die Daten gehören Ihnen.</p>
				</div>

				<div class="solar-track-grid">
					<?php foreach ( $tracking_items as $item ) : ?>
						<article class="solar-track-card" data-reveal>
							<div class="solar-track-card__k"><?php echo esc_html( $item['k'] ); ?></div>
							<h3 class="solar-track-card__t"><?php echo esc_html( $item['t'] ); ?></h3>
							<p class="solar-track-card__d"><?php echo esc_html( $item['d'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>

				<p class="solar-track-coda" data-reveal>
					Wer im Jahr 2026 den Algorithmen von Meta und Google die <em>saubersten</em> Conversion-Signale liefert,
					gewinnt die automatisierten Auktionen um die <em>günstigsten CPLs</em>.
					Das ist kein technisches Detail &mdash; das ist Ihr geldwerter Vorteil gegenüber lokalen Mitbewerbern, die weiter blind kaufen.
				</p>
			</div>
		</section>

		<!-- 08 · Proof / E3 Case (paper) ─────────────────────────── -->
		<section class="solar-section solar-section--paper" id="proof" data-screen-label="08 Proof" aria-labelledby="proof-title">
			<div class="solar-section__inner">
				<div class="solar-secthead">
					<div class="solar-secthead__row">
						<span class="solar-secnum">08 / Proof · Case Study</span>
						<span class="solar-secthead__rule" aria-hidden="true"></span>
					</div>
					<h2 id="proof-title" class="solar-h2"><?php echo esc_html( $e3_case_label ); ?>. <em>Vorher → Nachher.</em></h2>
				</div>

				<div class="solar-proof-grid">
					<div class="solar-proof-grid__col" data-reveal>
						<div class="solar-proof-phases">
							<?php foreach ( $proof_phases as $phase ) : ?>
								<div class="solar-proof-phase">
									<div class="solar-proof-phase__k"><?php echo esc_html( $phase['k'] ); ?></div>
									<p class="solar-proof-phase__v"><?php echo esc_html( $phase['v'] ); ?></p>
								</div>
							<?php endforeach; ?>
						</div>

						<blockquote class="solar-quote">
							<p>„Seit dem System sehen wir endlich, welche Kanäle wirklich Anfragen und Abschlüsse bringen. Wir konnten den teuren Lead-Einkauf massiv reduzieren &mdash; und haben jetzt eine eigene Pipeline."</p>
							<footer class="solar-quote__cite">Geschäftsführung · <?php echo esc_html( $e3_case_label ); ?></footer>
						</blockquote>

						<a href="<?php echo esc_url( $e3_url ); ?>" class="solar-hero__btn solar-hero__btn--ghost" data-track-action="cta_solar_proof_e3_methodology" data-track-category="trust">
							<span>Methodik im Detail lesen</span>
							<span class="solar-hero__btn-arrow" aria-hidden="true">
								<svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false"><path d="M3 9h12m0 0l-5-5m5 5l-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
						</a>
					</div>

					<div class="solar-proof-grid__col" data-reveal>
						<div class="solar-proof-cells">
							<div class="solar-proof-cells__cell">
								<div class="solar-proof-cells__num"><?php echo esc_html( $e3_lead_count ); ?></div>
								<div class="solar-proof-cells__lbl">Qualifizierte Anfragen</div>
							</div>
							<div class="solar-proof-cells__cell">
								<div class="solar-proof-cells__num"><?php echo esc_html( $e3_sales_conversion ); ?></div>
								<div class="solar-proof-cells__lbl">Abschlussquote</div>
							</div>
							<div class="solar-proof-cells__cell solar-proof-cells__cell--hl">
								<div class="solar-proof-cells__num solar-proof-cells__num--solar"><?php echo esc_html( $e3_cpl_reduction ); ?></div>
								<div class="solar-proof-cells__lbl">CPL</div>
							</div>
							<div class="solar-proof-cells__cell">
								<div class="solar-proof-cells__num"><?php echo esc_html( $e3_cpl_after ); ?></div>
								<div class="solar-proof-cells__lbl">Ziel-CPL erreicht</div>
							</div>
						</div>

						<div class="solar-cpl-curve">
							<div class="solar-cpl-curve__lbl">CPL-Verlauf · <?php echo esc_html( $e3_timeframe ); ?></div>
							<svg viewBox="0 0 400 120" preserveAspectRatio="none" aria-hidden="true">
								<line x1="20" y1="100" x2="380" y2="100" stroke="rgba(12,13,14,0.14)"/>
								<path d="M 20 20 Q 120 25, 200 60 T 380 95" fill="none" stroke="#c97a1a" stroke-width="2.5"/>
								<circle cx="20" cy="20" r="5" fill="#0c0d0e"/>
								<circle cx="380" cy="95" r="5" fill="#f0b540" stroke="#0c0d0e" stroke-width="1"/>
							</svg>
							<div class="solar-cpl-curve__ends">
								<span><?php echo esc_html( $e3_cpl_before ); ?></span>
								<span><?php echo esc_html( $e3_cpl_after ); ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- 09 · FAQ (paper-2, objection handler before CTA) ────── -->
		<section class="solar-section solar-section--paper-2" id="faq" data-screen-label="09 FAQ" aria-labelledby="faq-title">
			<div class="solar-section__inner">
				<div class="solar-secthead">
					<div class="solar-secthead__row">
						<span class="solar-secnum">09 / Häufige Fragen</span>
						<span class="solar-secthead__rule" aria-hidden="true"></span>
					</div>
					<h2 id="faq-title" class="solar-h2">Was Solar- und Wärmepumpen-Betriebe <em>vor dem Erstgespräch wissen wollen.</em></h2>
				</div>

				<div class="solar-faq" data-solar-faq>
					<?php foreach ( $faq_items as $idx => $faq_item ) : ?>
						<details class="solar-faq__item"<?php echo 0 === $idx ? ' open' : ''; ?>>
							<summary class="solar-faq__sum">
								<span class="solar-faq__n"><?php echo esc_html( str_pad( (string) ( $idx + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
								<span class="solar-faq__q"><?php echo esc_html( $faq_item['question'] ); ?></span>
								<span class="solar-faq__plus" aria-hidden="true">
									<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 5v14M5 12h14"/></svg>
								</span>
							</summary>
							<div class="solar-faq__a"><p><?php echo esc_html( $faq_item['answer'] ); ?></p></div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- 10 · About Hasim (dark) ──────────────────────────────── -->
		<section class="solar-section solar-section--dark" id="ueber" data-screen-label="10 About" aria-labelledby="about-title">
			<div class="solar-section__inner">
				<div class="solar-about">
					<div class="solar-about__media" data-reveal>
						<div class="solar-about__portrait">
							<img class="solar-about__portrait-img" src="https://hasimuener.de/wp-content/uploads/2026/04/Hasim_Uener_Portrait.png" alt="Haşim Üner — Umsetzer für Anfrage-Systeme" loading="lazy" decoding="async" />
							<div class="solar-about__portrait-inner" aria-hidden="true"></div>
							<div class="solar-about__portrait-stamp">
								<span>Profil · 2026</span>
							</div>
							<div class="solar-about__portrait-foot">
								<div>
									<div class="solar-about__portrait-kicker">Umsetzer</div>
									<div class="solar-about__portrait-name">Haşim Üner</div>
								</div>
								<div class="solar-about__portrait-loc">Pattensen · Region Hannover</div>
							</div>
						</div>
						<div class="solar-about__slot">
							<div class="solar-about__slot-lbl">Verfügbare Slots</div>
							<div class="solar-about__slot-num">3 / Q2</div>
						</div>
					</div>

					<div class="solar-about__body" data-reveal>
						<div class="solar-secnum solar-secnum--bone">10 / Über den Umsetzer</div>
						<h2 id="about-title" class="solar-h2 solar-h2--bone">
							Messbare Infrastruktur statt <em>digitaler Broschüren.</em>
						</h2>
						<p class="solar-about__p">
							Ich bin Haşim Üner und baue WordPress-basierte Anfrage-Systeme für Betriebe in der Erneuerbaren-Energien-Branche.
							Mein Fokus liegt nicht auf „schönem Webdesign", sondern auf echter Nachfrage-Architektur:
							Tracking, Vorqualifizierung, saubere Übergabe und Conversion-Optimierung.
						</p>
						<p class="solar-about__p solar-about__p--strong">
							Mit Sitz in Pattensen, Region Hannover. Spezialisiert auf <em>Lead-Autonomie</em> im B2B-Handwerk.
						</p>
						<div class="solar-about__metrics">
							<?php foreach ( $about_metrics as $m ) : ?>
								<div class="solar-about__metric">
									<div class="solar-about__metric-v"><?php echo esc_html( $m['v'] ); ?></div>
									<div class="solar-about__metric-l"><?php echo esc_html( $m['l'] ); ?></div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- 11 · Final CTA (paper-2) ─────────────────────────────── -->
		<section class="solar-section solar-section--paper-2" id="abschluss" data-screen-label="11 CTA" aria-labelledby="cta-title">
			<div class="solar-section__inner solar-cta">
				<span class="solar-cta__eyebrow">Nächster Schritt · Anfrage-System-Analyse</span>
				<h2 id="cta-title" class="solar-cta__h">
					Eigene Infrastruktur statt <em>geteilter Portal-Leads.</em>
				</h2>
				<p class="solar-cta__lede">
					Starten Sie mit der 8-Schritte-Analyse. Sie bekommen zuerst eine lokale Ampel-Einordnung mit Begründung und nächstem Schritt.
					Bei Nicht-Eignung bremst der Funnel &mdash; statt Sie in ein generisches Gespräch zu drücken.
				</p>
				<div class="solar-cta__actions">
					<a href="<?php echo esc_url( $request_url ); ?>" class="solar-hero__btn solar-cta__btn" data-track-action="cta_solar_final" data-track-category="lead_gen">
						<span>Analyse starten</span>
						<span class="solar-hero__btn-arrow" aria-hidden="true">
							<svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false"><path d="M3 9h12m0 0l-5-5m5 5l-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</span>
					</a>
					<div class="solar-cta__micro">
						<span><span class="solar-dot solar-dot--signal" aria-hidden="true"></span> 8 Schritte</span>
						<span aria-hidden="true">·</span>
						<span>Lokale Fit-Einordnung</span>
						<span aria-hidden="true">·</span>
						<span>Keine personenbezogenen Daten</span>
					</div>
				</div>

				<div class="solar-cta__slots">
					<span class="solar-cta__slots-chip" aria-hidden="true">HÜ</span>
					<div class="solar-cta__slots-body">
						<div class="solar-cta__slots-t">Einzelperson &mdash; begrenzte Slots pro Quartal.</div>
						<div class="solar-cta__slots-s">Aktuell ehrlich kommuniziert · 3 Slots Q2 / 2026</div>
					</div>
				</div>
			</div>
		</section>

	</div>

	<!-- Sticky CTA bar (mobile) -->
	<div class="solar-sticky" data-solar-sticky aria-hidden="true">
		<div class="solar-sticky__inner">
			<div class="solar-sticky__copy">
				<div class="solar-sticky__t">Anfrage-System-Analyse</div>
				<div class="solar-sticky__s">8 Schritte · lokales Ergebnis · keine E-Mail</div>
			</div>
			<a href="<?php echo esc_url( $request_url ); ?>" class="solar-hero__btn solar-sticky__btn" data-track-action="cta_solar_sticky" data-track-category="lead_gen">
				<span>Analyse starten</span>
				<span class="solar-hero__btn-arrow" aria-hidden="true">
					<svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false"><path d="M3 9h12m0 0l-5-5m5 5l-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</span>
			</a>
		</div>
	</div>
</main>

<script type="application/ld+json"><?php echo wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>

<?php get_footer(); ?>
