<?php
/**
 * Template Name: Qualifizierte PV-Anfragen – 4 Merkmale
 * Description: Quality-Page fuer hochwertige Photovoltaik-Anfragen.
 *              Vier Merkmale: Intent, Exklusivitaet, Vorqualifizierung,
 *              Echtzeit. Primaerer CTA: Marktcheck.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page_url        = home_url( '/qualifizierte-pv-anfragen/' );
$solar_money_url = function_exists( 'nexus_get_energy_systems_url' )
	? nexus_get_energy_systems_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/' );
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$e3_url          = home_url( '/e3-new-energy/' );
$cpl_url         = home_url( '/cost-per-lead-photovoltaik/' );
$sst_url         = home_url( '/server-side-tracking-b2b/' );
$intercept_url   = home_url( '/solar-leads-kaufen-alternative/' );

$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '9 Monate';

$quality_marks = [
	[
		't' => '01 · Intent-Stärke',
		's' => 'Was hat der Endkunde tatsächlich gesucht? Ein generischer Förder-Vergleich ist schwacher Intent. Eine konkrete Konfiguration mit Dachfläche, Speicher-Wunsch und Investitionsbereitschaft ist starker Intent. Je näher am Kauf, desto wertvoller die Anfrage.',
	],
	[
		't' => '02 · Exklusivität',
		's' => 'Wie viele Wettbewerber bekommen denselben Datensatz? Bei drei oder mehr Anbietern wird die Anfrage faktisch zum Bieter-Wettlauf. Exklusive Anfragen sind teurer pro Stück, aber wirtschaftlich relevanter pro Abschluss.',
	],
	[
		't' => '03 · Vorqualifizierung',
		's' => 'Sind Region, Dachfläche, Heizart, Bestandsanlage und ungefährer Projektwert vor dem Anruf bekannt? Ohne diese Daten qualifizieren Sie selbst nach – und verlieren Zeit an Nicht-Passende, ohne sie in der CPL-Rechnung zu sehen.',
	],
	[
		't' => '04 · Echtzeit-Übermittlung',
		's' => 'Kommt die Anfrage innerhalb von Minuten nach Endkundenklick? Leads, die Stunden oder Tage alt sind, sind faktisch tot – der Endkunde hat längst andere Angebote eingeholt. Reaktionszeit ist der häufigste Funnel-Killer.',
	],
];

$warning_signals = [
	[
		't' => 'Datensatz ohne Region',
		's' => 'Ohne Postleitzahl oder zumindest Bundesland-Information ist die Anfrage praktisch nicht qualifizierbar.',
	],
	[
		't' => 'Keine Heizart oder Dach-Info',
		's' => 'Bei Wärmepumpen-Anfragen ohne Bestandsheizung oder PV-Anfragen ohne Dachfläche fehlen die Grundlagen für eine seriöse Erstberatung.',
	],
	[
		't' => 'Alter Lead (>24 h)',
		's' => 'Datensätze, die mehr als 24 Stunden alt sind, haben in der Regel bereits 5–10 Wettbewerber kontaktiert. Abschlusswahrscheinlichkeit < 1 %.',
	],
	[
		't' => 'Generisches Interesse',
		's' => '„Möchte sich informieren" ist kein Kaufintent. Solche Leads kosten gleich viel, schließen aber kaum.',
	],
];

$how_to_measure = [
	[
		't' => 'CRM-Felder beim Eingang',
		's' => 'Lead-Score-Felder direkt im CRM: Region, Heizart, Dachfläche, Projektwert, Bestandsanlage. Anfragen ohne Mindestdaten werden automatisch markiert.',
	],
	[
		't' => 'Reaktionszeit-Reporting',
		's' => 'Pro Anfrage: Zeit zwischen Eingang und erstem Telefonkontakt. Drop-Off nach > 4 h, fast 0 % nach > 24 h.',
	],
	[
		't' => 'Quelle-zu-Auftrag-Attribution',
		's' => 'Server-Side-Tracking misst pro Lead-Quelle, wie viele Aufträge mit welchem Wert herauskommen – nicht nur Klicks.',
	],
	[
		't' => 'Abschlussquoten-Vergleich',
		's' => 'Portal-Leads vs. eigene Anfragen nebeneinander im Reporting. Quote macht Wirtschaftlichkeit sichtbar, nicht Volumen.',
	],
];

$linked_assets = [
	[ 't' => 'Cost per Lead Photovoltaik', 's' => 'Drei Szenarien im CPL-Vergleich und versteckte Kostentreiber.', 'url' => $cpl_url ],
	[ 't' => 'Server-Side Tracking für B2B', 's' => 'Wie man Quelle-zu-Auftrag-Attribution sauber misst.', 'url' => $sst_url ],
	[ 't' => 'Solar Leads kaufen – Alternative', 's' => 'Markteinordnung der Lead-Anbieter und Modelle.', 'url' => $intercept_url ],
	[ 't' => 'E3 New Energy – Methodik-Case', 's' => sprintf( '%1$s, %2$s Abschlussquote in %3$s.', $e3_lead_count, $e3_sales_conversion, $e3_timeframe ), 'url' => $e3_url ],
];

$faq = [
	[
		'question' => 'Woran erkenne ich einen qualifizierten Photovoltaik-Lead?',
		'answer'   => 'An vier Merkmalen: starker Kaufintent (konkrete Konfiguration, nicht nur Informationssuche), Exklusivität (Datensatz nicht parallel an Wettbewerber), Vorqualifizierung (Region, Dach, Heizart, Projektwert bekannt) und Echtzeit-Übermittlung (Anfrage innerhalb von Minuten nach Endkunden-Eingabe). Fehlt eines dieser Merkmale, sinkt die Abschlusswahrscheinlichkeit drastisch.',
	],
	[
		'question' => 'Sind exklusive Leads automatisch besser?',
		'answer'   => 'Exklusivität ist eine notwendige, aber keine hinreichende Bedingung. Ein exklusiver Lead ohne Vorqualifizierung ist trotzdem schwach. Erst die Kombination aller vier Merkmale macht eine Anfrage wirtschaftlich.',
	],
	[
		'question' => 'Wie misst man die Lead-Qualität in der Praxis?',
		'answer'   => 'Vier Messpunkte: Lead-Score im CRM (Vollständigkeit der Daten), Reaktionszeit-Reporting, Server-Side-Tracking für Quelle-zu-Auftrag-Attribution und ein laufender Abschlussquoten-Vergleich zwischen verschiedenen Lead-Quellen.',
	],
	[
		'question' => 'Welche Warnsignale gibt es bei Portal-Leads?',
		'answer'   => 'Vier Warnsignale: fehlende Region, fehlende Heizart oder Dach-Information, Datensatz älter als 24 Stunden und generische Interessens-Anfragen ohne erkennbaren Kaufintent.',
	],
	[
		'question' => sprintf( 'Welche Abschlussquote ist im Solar-Bereich realistisch?', $e3_case_label ),
		'answer'   => sprintf( 'Im B2C-PV-Markt liegen Abschlussquoten bei Portal-Leads typischerweise zwischen 2 %% und 5 %%. Bei eigenen, qualifizierten Anfragen sind 8 %% bis 15 %% realistisch – die E3-Referenz liegt bei %s über alle Anfragen hinweg.', $e3_sales_conversion ),
	],
];

$author_person     = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];
$breadcrumb_schema = function_exists( 'hu_get_solar_subpage_breadcrumb_schema' )
	? hu_get_solar_subpage_breadcrumb_schema( $page_url, 'Qualifizierte PV-Anfragen' )
	: [];

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Qualifizierung von Photovoltaik- und Wärmepumpen-Anfragen',
	'serviceType' => 'Lead-Qualität messen und steigern',
	'url'         => $page_url,
	'description' => sprintf( 'Vier Merkmale für qualifizierte PV-Anfragen. Referenz %1$s: %2$s Abschlussquote bei %3$s qualifizierten Anfragen in %4$s.', $e3_case_label, $e3_sales_conversion, $e3_lead_count, $e3_timeframe ),
	'provider'    => $author_person,
	'author'      => $author_person,
];

$faq_schema = [
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => trailingslashit( $page_url ) . '#faq',
	'url'        => trailingslashit( $page_url ) . '#faq',
	'mainEntity' => [],
];

foreach ( $faq as $faq_item ) {
	$faq_schema['mainEntity'][] = [
		'@type'          => 'Question',
		'name'           => $faq_item['question'],
		'acceptedAnswer' => [ '@type' => 'Answer', 'text' => $faq_item['answer'] ],
	];
}

get_header();
?>

<main id="primary" class="hu-intercept" role="main" data-track-page="qualifizierte-pv-anfragen">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-quality-hero-title">
		<div class="hu-intercept__container">
			<p class="hu-intercept__eyebrow">Lead-Qualität für Photovoltaik, Wärmepumpe & Speicher</p>
			<h1 class="hu-intercept__title" id="hu-quality-hero-title">
				Qualifizierte PV-Anfragen erkennen: Vier Merkmale, die zählen
			</h1>
			<p class="hu-intercept__lead">
				Zwischen einer hochwertigen und einer schwachen Photovoltaik-Anfrage liegen oft <strong>10 %-Punkte Abschlussquote</strong>. Diese Seite zeigt vier Merkmale, an denen sich die Qualität messen lässt – und welche Warnsignale ein toter Datensatz aussendet.
			</p>
			<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="qualifizierte_pv_anfragen"
				   data-track-section="hero">
					Kostenfreien Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $e3_url ); ?>"
				   data-track-action="cta_e3_case"
				   data-track-category="qualifizierte_pv_anfragen"
				   data-track-section="hero">
					E3-Case ansehen
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__system" id="merkmale" aria-labelledby="hu-quality-marks-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-quality-marks-title">Die vier Merkmale einer qualifizierten Anfrage</h2>
			<p class="hu-intercept__section-lead">
				Jedes Merkmal ist einzeln messbar. Eine Anfrage, die alle vier erfüllt, schließt im Schnitt fünfmal häufiger ab als eine, die nur zwei erfüllt.
			</p>
			<ol class="hu-intercept__layers">
				<?php foreach ( $quality_marks as $i => $mark ) : ?>
					<li class="hu-intercept__layer">
						<span class="hu-intercept__layer-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="hu-intercept__layer-body">
							<h3 class="hu-intercept__layer-title"><?php echo esc_html( $mark['t'] ); ?></h3>
							<p class="hu-intercept__layer-text"><?php echo esc_html( $mark['s'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="hu-intercept__why" id="warnsignale" aria-labelledby="hu-quality-warnings-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-quality-warnings-title">Vier Warnsignale: Wann ein Lead bereits tot ist</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $warning_signals as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $item['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="messen" aria-labelledby="hu-quality-measure-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-quality-measure-title">Wie man Lead-Qualität in der Praxis misst</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $how_to_measure as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $item['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="vertiefung" aria-labelledby="hu-quality-linked-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-quality-linked-title">Vertiefende Ressourcen</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $linked_assets as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['t'] ); ?></a></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-quality-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-quality-faq-title">Häufige Fragen zur Lead-Qualität</h2>
			<div class="hu-intercept__faq-list">
				<?php foreach ( $faq as $item ) : ?>
					<details class="hu-intercept__faq-item">
						<summary class="hu-intercept__faq-q"><?php echo esc_html( $item['question'] ); ?></summary>
						<p class="hu-intercept__faq-a"><?php echo esc_html( $item['answer'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__final" id="final-cta" aria-labelledby="hu-quality-final-title">
		<div class="hu-intercept__container hu-intercept__container--centered">
			<h2 class="hu-intercept__h2" id="hu-quality-final-title">Eigene Lead-Qualität einordnen lassen</h2>
			<p class="hu-intercept__final-text">
				Manueller, tiefer Marktcheck statt Software-Einheitsbrei. Händische Analyse deiner Region innerhalb von 48 Stunden per E-Mail — mit klarer Aussage, wo Ihre aktuelle Lead-Qualität schwächelt und welche Hebel kurzfristig die Abschlussquote heben.
			</p>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="qualifizierte_pv_anfragen"
				   data-track-section="final">
					Kostenfreien Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $solar_money_url ); ?>"
				   data-track-action="cta_money_page"
				   data-track-category="qualifizierte_pv_anfragen"
				   data-track-section="final">
					System & Methode ansehen
				</a>
			</div>
		</div>
	</section>

	<script type="application/ld+json"><?php echo wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<?php if ( ! empty( $breadcrumb_schema ) ) : ?>
	<script type="application/ld+json"><?php echo wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<?php endif; ?>
</main>

<?php
get_template_part(
	'template-parts/seo-subpage-sticky-cta',
	null,
	[
		'marktcheck_url' => $marktcheck_url,
		'track_category' => 'qualifizierte_pv_anfragen',
	]
);

get_footer();
