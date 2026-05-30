<?php
/**
 * Template Name: Solar-Leads-Kosten – Marktstudie (Pillar)
 * Description: Zitierfähige Marktstudie zu den tatsächlichen Kosten von Solar-,
 *              Wärmepumpen- und Speicher-Leads im DACH-Raum. Kernthese:
 *              Cost-per-Order statt Cost-per-Lead. Methodik transparent,
 *              eigene Fallzahl (E3) als Referenz-Benchmark.
 *              Primärer Pfad: Marktcheck auf /solar-waermepumpen-leadgenerierung/#marktcheck.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url        = home_url( '/solar-leads-kosten-studie/' );
$solar_money_url = function_exists( 'nexus_get_energy_systems_url' )
	? nexus_get_energy_systems_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/' );
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$e3_url          = home_url( '/e3-new-energy/' );

// ── E3-Proof-Canon (Referenz-Benchmark) ───────────────────────
$e3_canon         = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics       = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label    = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_cpl_after     = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_conv_after    = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_timeframe     = $e3_metrics['timeframe']['display'] ?? '6 Monate';

$study_year    = '2026';
$study_updated = '2026-05-30';

// ── Key Findings (zitierfähige Kernaussagen) ──────────────────
$key_findings = [
	[
		'k' => '40 – 150 €',
		'l' => 'Spanne pro gekauftem Photovoltaik-/Wärmepumpen-Lead im DACH-Portalmarkt — je nach Exklusivität und Produkt.',
	],
	[
		'k' => '3 – 5×',
		'l' => 'übliche Mehrfach-Vergabe desselben geteilten Datensatzes an konkurrierende Fachbetriebe.',
	],
	[
		'k' => 'bis 18×',
		'l' => 'Unterschied im Cost-per-Order zwischen gekauftem Portal-Lead und eigener Anfrage (Modellrechnung, s. u.).',
	],
	[
		'k' => 'CPO ≠ CPL',
		'l' => 'Der Cost-per-Lead verschleiert die wahren Kosten. Entscheidend sind die Kosten pro Abschluss (Cost-per-Order).',
	],
];

// ── Lead-Kosten nach Markt-Modell ─────────────────────────────
$cost_models = [
	[
		't'    => 'Geteilte Datensätze',
		'cpl'  => '40 – 90 €',
		'excl' => '3 – 5 Abnehmer',
		's'    => 'Eine Endkundenanfrage wird parallel an mehrere Fachbetriebe verkauft. Niedriger Stückpreis, hoher Wettbewerb, Geschwindigkeit entscheidet.',
	],
	[
		't'    => 'Exklusive Datensätze',
		'cpl'  => '80 – 150 €',
		'excl' => '1 Abnehmer',
		's'    => 'Anfrage geht nur an einen Betrieb. Höherer Stückpreis, tendenziell bessere Abschlussquote, aber weiterhin kein Asset-Eigentum.',
	],
	[
		't'    => 'Wärmepumpen-Leads',
		'cpl'  => '60 – 160 €',
		'excl' => 'gemischt',
		's'    => 'Durch Förderdynamik und höhere Projektwerte oft teurer als reine PV-Leads. Qualität schwankt stark mit der Vorqualifizierung.',
	],
	[
		't'    => 'Eigenes Anfrage-System',
		'cpl'  => $e3_cpl_after . ' *',
		'excl' => '100 % exklusiv',
		's'    => 'Money Page + Vorqualifizierung + Tracking im eigenen Eigentum. * Referenzwert ' . $e3_case_label . ' nach Einschwingen, nicht garantiert reproduzierbar.',
	],
];

// ── CPO-Modellrechnung: der eigentliche Kostenvergleich ───────
// Formel: CPO = CPL / Abschlussquote. Transparent, nachrechenbar.
$cpo_rows = [
	[
		'modell' => 'Geteilter Portal-Lead',
		'cpl'    => '70 €',
		'conv'   => '2 %',
		'cpo'    => '3.500 €',
	],
	[
		'modell' => 'Exklusiver Portal-Lead',
		'cpl'    => '120 €',
		'conv'   => '4 %',
		'cpo'    => '3.000 €',
	],
	[
		'modell' => 'Eigenes Anfrage-System (E3-Referenz)',
		'cpl'    => $e3_cpl_after,
		'conv'   => $e3_conv_after,
		'cpo'    => '≈ 183 €',
	],
];

// ── Versteckte Kosten ─────────────────────────────────────────
$hidden_costs = [
	[
		't' => 'Vertriebszeit auf tote Leads',
		's' => 'Mehrfach verkaufte Datensätze sind oft schon „abtelefoniert", bevor Sie anrufen. Jede Minute auf einen nicht erreichbaren oder bereits versorgten Kontakt ist verbrannte Vertriebskapazität.',
	],
	[
		't' => 'Nachqualifizierung',
		's' => 'Ohne Region, Dachfläche, Heizart und Budget qualifizieren Sie nach dem Kauf selbst — ein versteckter Personalkostenblock, der im CPL nicht auftaucht.',
	],
	[
		't' => 'Kein Asset-Aufbau',
		's' => 'Gemietete Leads hinterlassen nichts. Nach 24 Monaten Portalbindung steht kein eigenes System, keine Datenhistorie, kein Tracking — der CPL fällt nie.',
	],
	[
		't' => 'Steigende Marktpreise',
		's' => 'Je mehr Anbieter um dieselbe Endkundenzahl konkurrieren, desto teurer der Datensatz — ohne Qualitätsgewinn. Der CPL kennt im Portalmodell nur eine Richtung.',
	],
];

// ── FAQ ───────────────────────────────────────────────────────
$faqs = [
	[
		'question' => 'Was kostet ein Solar-Lead im DACH-Raum 2026?',
		'answer'   => 'Gekaufte Photovoltaik- und Wärmepumpen-Leads liegen je nach Exklusivität und Produkt zwischen rund 40 € (geteilte Datensätze) und 150 € (exklusive Datensätze). Wärmepumpen-Leads liegen tendenziell höher. Diese Preise beziehen sich auf den reinen Datensatz — die Kosten pro Abschluss liegen ein Vielfaches darüber.',
	],
	[
		'question' => 'Warum ist der Cost-per-Lead die falsche Kennzahl?',
		'answer'   => 'Der CPL misst nur den Einkaufspreis eines Datensatzes, nicht dessen Wert. Entscheidend ist der Cost-per-Order (CPO) — die Kosten pro tatsächlichem Abschluss. Bei niedrigen Abschlussquoten auf mehrfach verkaufte Portal-Leads kann ein 70-€-Lead pro Abschluss mehrere tausend Euro kosten.',
	],
	[
		'question' => 'Wie wird der Cost-per-Order berechnet?',
		'answer'   => 'CPO = Cost-per-Lead geteilt durch die Abschlussquote. Beispiel: 70 € Lead-Preis bei 2 % Abschlussquote ergibt 3.500 € pro Abschluss. Ein eigenes System mit 22 € pro Anfrage und 12 % Abschlussquote ergibt rund 183 € pro Abschluss (Referenzwerte E3 New Energy).',
	],
	[
		'question' => 'Sind die Zahlen dieser Studie repräsentativ?',
		'answer'   => 'Die Preisspannen basieren auf öffentlich beobachtbaren Marktpreispunkten und Brancheneinordnung. Die Abschluss- und CPO-Werte des eigenen Systems sind ein dokumentierter Einzelfall (E3 New Energy) und ausdrücklich als Referenz, nicht als Garantie zu lesen. Die CPO-Tabelle ist eine transparente Modellrechnung, keine Erhebung.',
	],
	[
		'question' => 'Lohnt sich ein eigenes Anfrage-System gegenüber Lead-Kauf?',
		'answer'   => 'Das hängt von Marktregion, Projektwert und Vertriebskapazität ab. Faustregel: Wer dauerhaft skaliert und exklusive, vorqualifizierte Anfragen braucht, fährt mit einem eigenen System pro Abschluss meist deutlich günstiger — bei gleichzeitigem Asset-Aufbau. Der kostenfreie Marktcheck prüft das für Ihre Region.',
	],
];

// ── Schema.org: Article + Dataset + FAQPage + Breadcrumb ──────
$author_person     = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];
$breadcrumb_schema = function_exists( 'hu_get_solar_subpage_breadcrumb_schema' )
	? hu_get_solar_subpage_breadcrumb_schema( $page_url, 'Solar-Leads-Kosten: Marktstudie' )
	: [];

$article_schema = [
	'@context'         => 'https://schema.org',
	'@type'            => 'Article',
	'@id'              => trailingslashit( $page_url ) . '#article',
	'headline'         => 'Was kosten Solar- und Wärmepumpen-Leads? Marktstudie DACH ' . $study_year,
	'url'              => $page_url,
	'datePublished'    => $study_updated,
	'dateModified'     => $study_updated,
	'inLanguage'       => 'de-DE',
	'author'           => $author_person,
	'publisher'        => $author_person,
	'about'            => [ 'Lead-Kosten', 'Cost per Lead', 'Cost per Order', 'Photovoltaik', 'Wärmepumpe' ],
	'description'      => 'Marktstudie zu den tatsächlichen Kosten von Solar-, Wärmepumpen- und Speicher-Leads im DACH-Raum: Preisspannen je Modell, Cost-per-Order statt Cost-per-Lead, versteckte Kosten und Benchmark eines eigenen Anfrage-Systems.',
];

$dataset_schema = [
	'@context'      => 'https://schema.org',
	'@type'         => 'Dataset',
	'@id'           => trailingslashit( $page_url ) . '#dataset',
	'name'          => 'Solar- & Wärmepumpen-Lead-Kosten DACH ' . $study_year,
	'description'   => 'Preisspannen für gekaufte Photovoltaik- und Wärmepumpen-Leads nach Markt-Modell sowie Cost-per-Order-Modellrechnung im DACH-Raum.',
	'url'           => $page_url,
	'dateModified'  => $study_updated,
	'inLanguage'    => 'de-DE',
	'creator'       => $author_person,
	'license'       => home_url( '/impressum/' ),
	'isAccessibleForFree' => true,
	'variableMeasured' => [ 'Cost per Lead', 'Abschlussquote', 'Cost per Order', 'Exklusivität' ],
];

$faq_schema = [
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => trailingslashit( $page_url ) . '#faq',
	'url'        => trailingslashit( $page_url ) . '#faq',
	'mainEntity' => [],
];
foreach ( $faqs as $faq_item ) {
	$faq_schema['mainEntity'][] = [
		'@type'          => 'Question',
		'name'           => $faq_item['question'],
		'acceptedAnswer' => [
			'@type' => 'Answer',
			'text'  => $faq_item['answer'],
		],
	];
}

get_header();
?>

<main id="primary" class="hu-intercept" role="main" data-track-page="solar-leads-kosten-studie">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-study-hero-title">
		<div class="hu-intercept__container">
			<p class="hu-intercept__eyebrow">Marktstudie · DACH-Raum · Stand <?php echo esc_html( $study_year ); ?></p>
			<h1 class="hu-intercept__title" id="hu-study-hero-title">
				Was kosten Solar- und Wärmepumpen-Leads wirklich?
			</h1>
			<p class="hu-intercept__lead">
				Gekaufte Photovoltaik- und Wärmepumpen-Leads kosten im DACH-Markt <strong>40 – 150 €</strong> pro Datensatz. Doch der Lead-Preis ist die falsche Kennzahl: Wer mehrfach verkaufte Datensätze einkalkuliert, zahlt <strong>pro Abschluss</strong> schnell mehrere tausend Euro. Diese Studie rechnet vom <strong>Cost-per-Lead</strong> zum <strong>Cost-per-Order</strong> — und zeigt, wo die wahren Kosten liegen.
			</p>
			<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="study_solar_leads_kosten"
				   data-track-section="hero">
					Kostenfreien Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="#cpo"
				   data-track-action="cta_to_cpo"
				   data-track-category="study_solar_leads_kosten"
				   data-track-section="hero">
					Direkt zur Cost-per-Order-Rechnung
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__compare" id="key-findings" aria-labelledby="hu-study-findings-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-study-findings-title">Die wichtigsten Ergebnisse auf einen Blick</h2>
			<div class="hu-intercept__panel hu-intercept__panel--positive">
				<ul class="hu-intercept__facts">
					<?php foreach ( $key_findings as $fact ) : ?>
						<li>
							<span class="hu-intercept__fact-key"><?php echo esc_html( $fact['k'] ); ?></span>
							<span class="hu-intercept__fact-label"><?php echo esc_html( $fact['l'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="methodik" aria-labelledby="hu-study-method-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-study-method-title">Methodik &amp; Datenbasis — transparent</h2>
			<p class="hu-intercept__section-lead">
				Damit diese Studie nachvollziehbar bleibt, hier offen, woher die Zahlen stammen und was sie aussagen — und was nicht.
			</p>
			<div class="hu-intercept__grid hu-intercept__grid--two">
				<div class="hu-intercept__panel">
					<h3 class="hu-intercept__panel-title">Woher die Preisspannen kommen</h3>
					<p class="hu-intercept__card-text">
						Die Lead-Preise (40 – 150 €) basieren auf öffentlich beobachtbaren Marktpreispunkten gängiger Lead-Anbieter sowie auf Brancheneinordnung im DACH-Raum. Es handelt sich um typische Spannen, nicht um Einzelangebote eines bestimmten Anbieters.
					</p>
				</div>
				<div class="hu-intercept__panel">
					<h3 class="hu-intercept__panel-title">Woher die Abschluss- und CPO-Werte kommen</h3>
					<p class="hu-intercept__card-text">
						Die Werte des eigenen Systems (<?php echo esc_html( $e3_cpl_after ); ?> pro Anfrage, <?php echo esc_html( $e3_conv_after ); ?> Abschlussquote) stammen aus einem dokumentierten Einzelfall (<a href="<?php echo esc_url( $e3_url ); ?>"><?php echo esc_html( $e3_case_label ); ?></a>, <?php echo esc_html( $e3_timeframe ); ?>). Sie sind Referenz, keine Garantie. Die Cost-per-Order-Tabelle ist eine transparente Modellrechnung mit offengelegter Formel.
					</p>
				</div>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="modelle" aria-labelledby="hu-study-models-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-study-models-title">Lead-Kosten nach Markt-Modell</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $cost_models as $model ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $model['t'] ); ?></h3>
						<p class="hu-intercept__fact-key" style="font-size:1.4rem;"><?php echo esc_html( $model['cpl'] ); ?></p>
						<p class="hu-intercept__card-text" style="margin:.2rem 0 .6rem;opacity:.7;"><strong><?php echo esc_html( $model['excl'] ); ?></strong></p>
						<p class="hu-intercept__card-text"><?php echo esc_html( $model['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__compare" id="cpo" aria-labelledby="hu-study-cpo-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-study-cpo-title">Der teure Denkfehler: Cost-per-Lead vs. Cost-per-Order</h2>
			<p class="hu-intercept__section-lead">
				Ein Lead ist kein Abschluss. Erst die Abschlussquote zeigt, was eine Anfrage <em>pro gewonnenem Kunden</em> kostet. Formel: <strong>Cost-per-Order = Cost-per-Lead ÷ Abschlussquote</strong>. Modellrechnung mit typischen Werten:
			</p>
			<div class="hu-study-table-wrap" style="overflow-x:auto;">
				<table class="hu-study-table" style="width:100%;border-collapse:collapse;text-align:left;">
					<thead>
						<tr>
							<th style="padding:.7rem 1rem;border-bottom:2px solid currentColor;">Modell</th>
							<th style="padding:.7rem 1rem;border-bottom:2px solid currentColor;">Cost-per-Lead</th>
							<th style="padding:.7rem 1rem;border-bottom:2px solid currentColor;">Abschlussquote</th>
							<th style="padding:.7rem 1rem;border-bottom:2px solid currentColor;">Cost-per-Order</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $cpo_rows as $row ) : ?>
							<tr>
								<td style="padding:.7rem 1rem;border-bottom:1px solid rgba(128,128,128,.25);"><?php echo esc_html( $row['modell'] ); ?></td>
								<td style="padding:.7rem 1rem;border-bottom:1px solid rgba(128,128,128,.25);"><?php echo esc_html( $row['cpl'] ); ?></td>
								<td style="padding:.7rem 1rem;border-bottom:1px solid rgba(128,128,128,.25);"><?php echo esc_html( $row['conv'] ); ?></td>
								<td style="padding:.7rem 1rem;border-bottom:1px solid rgba(128,128,128,.25);"><strong><?php echo esc_html( $row['cpo'] ); ?></strong></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<p class="hu-intercept__compare-note">
				Lesart: Ein 70-€-Lead bei 2 % Abschlussquote kostet <strong>3.500 € pro gewonnenem Kunden</strong>. Ein eigenes System mit <?php echo esc_html( $e3_cpl_after ); ?> und <?php echo esc_html( $e3_conv_after ); ?> Abschlussquote liegt bei rund <strong>183 €</strong> — Größenordnungen darunter. Eigene Werte je Region selbst durchrechnen: <a href="<?php echo esc_url( home_url( '/cost-per-lead-photovoltaik/' ) ); ?>">Cost per Lead Photovoltaik — Szenarien &amp; Rechner</a>.
			</p>
		</div>
	</section>

	<section class="hu-intercept__why" id="versteckte-kosten" aria-labelledby="hu-study-hidden-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-study-hidden-title">Die versteckten Kosten, die kein Lead-Preis zeigt</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $hidden_costs as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $item['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
			<p class="hu-intercept__compare-note">
				Tiefer in die Gesamtkostenrechnung: <a href="<?php echo esc_url( home_url( '/eigene-leadgenerierung-vs-portale/' ) ); ?>">Portal-Leads vs. eigenes System — TCO-Vergleich über 24 Monate</a>.
			</p>
		</div>
	</section>

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-study-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-study-faq-title">Häufige Fragen zu Solar-Lead-Kosten</h2>
			<div class="hu-intercept__faq-list">
				<?php foreach ( $faqs as $item ) : ?>
					<details class="hu-intercept__faq-item">
						<summary class="hu-intercept__faq-q"><?php echo esc_html( $item['question'] ); ?></summary>
						<p class="hu-intercept__faq-a"><?php echo esc_html( $item['answer'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__final" id="final-cta" aria-labelledby="hu-study-final-title">
		<div class="hu-intercept__container hu-intercept__container--centered">
			<h2 class="hu-intercept__h2" id="hu-study-final-title">Was kostet ein Abschluss in Ihrer Region?</h2>
			<p class="hu-intercept__final-text">
				Manueller, tiefer Marktcheck statt Software-Einheitsbrei: händische Analyse Ihrer Region innerhalb von 48 Stunden per E-Mail — mit klarer Aussage, ob ein eigenes Anfrage-System pro Abschluss wirtschaftlicher ist als der Weiterkauf von Portal-Leads. Ohne Pflicht-Call, ohne Newsletter.
			</p>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="study_solar_leads_kosten"
				   data-track-section="final">
					Kostenfreien Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( home_url( '/solar-leads-kaufen-alternative/' ) ); ?>"
				   data-track-action="cta_to_alternative"
				   data-track-category="study_solar_leads_kosten"
				   data-track-section="final">
					Alternative zum Lead-Kauf ansehen
				</a>
			</div>
		</div>
	</section>

	<script type="application/ld+json"><?php echo wp_json_encode( $article_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<script type="application/ld+json"><?php echo wp_json_encode( $dataset_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
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
		'track_category' => 'study_solar_leads_kosten',
	]
);

get_footer();
