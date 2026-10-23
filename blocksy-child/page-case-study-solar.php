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

// Seit /ergebnisse/ stillgelegt ist (2026-09-25), ist dieser Fall der Beleg
// aller Wege: Startseite, Menüpunkt „Ergebnisse“, Tracking- und Agentur-Seite
// führen hierher. Allgemeine B2B-Projekte erhalten bereits in der Einordnung
// einen leisen Anfrageweg. Der folgende Marktcheck benennt den Energie-Fokus.
$project_url = hu_get_commercial_route( 'project_request' );

$tracking_attrs = 'data-track-section="case_solar_methodology" data-track-funnel-stage="proof"';

$e3 = static function ( $metric, $field = 'display', $fallback = '' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $metric, $field, $fallback ) : $fallback;
};

$e3_canon      = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_label = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'mittelständischer PV-Installationsbetrieb';

$e3_cpl_before   = $e3( 'cpl_before', 'display', '150 €' );
$e3_cpl_after    = $e3( 'cpl_after', 'display', '22 €' );
$e3_cpl_ramp     = $e3( 'cpl_ramp', 'display', '70 – 100 €' );
$e3_lead_count   = $e3( 'lead_count', 'display', '1.750' );
// Die Vorher-Quote ist die einzige nicht gemessene Zahl des Falls (Marktannahme
// über gekaufte Portal-Leads). Neben gemessenen Werten steht sie deshalb nur
// vorsichtig und ausdrücklich als Annahme.
$e3_conv_before  = $e3( 'sales_conversion_before', 'display_hedged', 'einstellig' );
$e3_conv_after   = $e3( 'sales_conversion_after', 'display', '15 %' );
$e3_timeframe    = $e3( 'timeframe', 'display', '6 Monate' );
$e3_timeframe_dt = $e3( 'timeframe', 'display_dative', '6 Monaten' );
$e3_timeline     = $e3_canon['timeline'];
$e3_cpl_late     = $e3( 'cpl_range_late', 'display', '15–30 €' );
$e3_sst_effect   = $e3( 'sst_effect', 'display', 'über 40 %' );
$e3_crm_share    = $e3( 'crm_capture_share', 'display', '95 %' );
$e3_reduction    = (string) HU_E3_CPL_REDUCTION_PERCENT;
$e3_reached      = isset( $e3_canon['cpl_reached_label'] ) ? (string) $e3_canon['cpl_reached_label'] : 'ab Monat 3';
$e3_cpl_share    = round( HU_E3_CPL_AFTER / HU_E3_CPL_BEFORE, 4 );

// Seit 2026-10-23 Kompetenzbeleg fuer alle Wege: Hauptaktion ist die
// Projektanfrage mit Fokus Anfragestrecke, weil der Fall genau dieses Produkt
// zeigt. Der Marktcheck bleibt ein leiser Weg fuer Solar-/Waermepumpenbetriebe.
$project_url  = hu_get_contact_intake_url( 'project', 'anfragestrecke' );
$offer_url    = home_url( '/#angebot-anfragestrecke' );
$offer_price  = function_exists( 'hu_anfragestrecke_price' ) ? hu_anfragestrecke_price() : '';
$offer_crm    = function_exists( 'hu_anfragestrecke_price' ) ? hu_anfragestrecke_price( true ) : '';
$reply_label  = defined( 'HU_RESPONSE_PROMISE_SENTENCE' ) ? HU_RESPONSE_PROMISE_SENTENCE : 'Antwort innerhalb von 24 Stunden werktags.';

$role_own = [
	'Audit der Betriebsdaten: Nachfrage, Förderlage, Fragen und Einwände der Zielgruppe',
	'Konzept der Strecke',
	'Landingpages und Formulare mit Vorqualifizierung',
	'Kampagnen bei Google und Meta',
	'Tracking, auch serverseitig',
	'Anbindung an Bitrix24 und Rückmeldung an die Werbekonten',
	'Technisches SEO',
];
$role_business = [
	'Rückruf und Beratung',
	'Angebot und Abschluss',
	'Freigaben durch die Geschäftsführung',
];

$comparison_rows = [
	[ 'Menge', 'hängt am Kontingent des Portals', 'über eigenes Budget und eigene Kampagnen steuerbar' ],
	[ 'Exklusivität', 'dieselbe Anfrage geht an mehrere Betriebe', 'wird nicht weiterverkauft; vergleichen kann der Interessent trotzdem' ],
	[ 'Angaben vorab', 'Name, Telefonnummer, grobe Angaben', 'Produktinteresse und Herkunft vor dem ersten Anruf im CRM' ],
	[ 'Lernen', 'keine Rückmeldung, welche Anfrage zum Auftrag wurde', 'Abschlüsse gehen als Signal zurück an die Werbekonten' ],
];

$work_areas = [
	[
		'stations' => 'Stationen 01–02',
		'title'    => 'Nachfrage und Einstieg',
		'done'     => [ 'Kampagnen bei Meta und Google', 'Landingpages je Produkt: Photovoltaik, Wärmepumpe, Kombination', 'technisches SEO für kaufnahe Suchanfragen' ],
		'benefit'  => 'Anfragen kamen über eigene Kanäle statt über das Kontingent eines Portals. Die Menge ließ sich über Budget und Kampagnen steuern.',
		'proof'    => sprintf( 'Beleg: %s Anfragen in sechs Monaten, vor allem über Meta, organische Suche und Google Ads.', $e3_lead_count ),
	],
	[
		'stations' => 'Stationen 03–04',
		'title'    => 'Anfrage und Übergabe',
		'done'     => [ 'mehrstufige Formulare mit Vorqualifizierung', 'Übergabe in Bitrix24 mit Produktinteresse und Herkunft' ],
		'benefit'  => 'Vor dem ersten Anruf stand im CRM, worum es geht und woher die Anfrage kam. Der Vertrieb konnte priorisieren, statt jede Nummer gleich zu behandeln.',
		'proof'    => sprintf( 'Beleg: rund %s der Anfragen kamen mit Produktinteresse und Herkunft im CRM an.', $e3_crm_share ),
	],
	[
		'stations' => 'Station 06',
		'title'    => 'Messung und Optimierung',
		'done'     => [ 'Tracking im Browser und serverseitig', 'Rückmeldung qualifizierter Anfragen und Abschlüsse aus dem CRM an die Werbekonten' ],
		'benefit'  => 'Die Kampagnen wurden auf Anfragen optimiert, die tatsächlich zu Aufträgen führten, nicht auf möglichst viele Formulareingänge.',
		'proof'    => sprintf( 'Beleg: Drei bis vier Tage nach dem Livegang des Server-Side-Trackings lagen die Kosten pro Anfrage %s niedriger, bei durchgehend laufenden Kampagnen. Mit der CRM-Rückmeldung ging es weiter bis rund %s.', $e3_sst_effect, $e3_cpl_after ),
	],
];

$phases = [
	[
		'label' => 'Monat 1',
		'value' => '–',
		'body'  => $e3_timeline['preparation'],
	],
	[
		'label' => 'Monat 2 · Kampagnenstart',
		'value' => $e3_cpl_ramp,
		'body'  => $e3_timeline['campaign'],
	],
	[
		'label' => $e3_reached,
		'value' => $e3_cpl_after,
		'body'  => sprintf( 'Server-Side-Tracking und CRM-Anbindung gehen live, die Rückmeldung steuert die Kampagnen. Danach lagen die Monate zwischen %s, meist um %s.', str_replace( '–', ' und ', $e3_cpl_late ), $e3_cpl_after ),
	],
];

$basis_items = [
	sprintf( 'Kosten pro Anfrage: reine Werbekosten, ohne Aufbau, Betreuung und Vertriebszeit. %s ist der Einkaufspreis einer Portal-Anfrage.', $e3_cpl_before ),
	sprintf( '%s: Abschlussquote der vorqualifizierten Anfragen im CRM', $e3_conv_after ),
	sprintf( '%s: alle eigenen Anfragen der ersten sechs Monate', $e3_lead_count ),
];
$limit_items = [
	sprintf( 'Die %s beim Server-Side-Tracking sind ein Vorher-nachher-Vergleich bei laufenden Kampagnen. Gleichzeitig mit und ohne Server-Side gemessen wurde nicht. Die übrigen Maßnahmen sind nicht einzeln gemessen.', $e3_sst_effect ),
	sprintf( '%s sind ein erreichter Wert, kein Durchschnitt und kein Tarif. Region, Produkt und Wettbewerb setzen den Wert in jedem Betrieb neu.', $e3_cpl_after ),
	'Ohne Vertrieb, der zügig zurückruft, wird aus keiner Anfrage ein Auftrag.',
];

$transfer_items = [
	'Landingpages und WordPress-Seiten, die anfragen',
	'Formulare, die vor dem ersten Anruf das Wichtige erfassen',
	'Tracking bis ins CRM und zurück an die Werbekonten',
];

$deeper_links = [
	[
		'title' => 'Server-Side-Tracking B2B',
		'body'  => 'Wie Marketing-, Website- und CRM-Daten technisch verbunden werden.',
		'url'   => home_url( '/server-side-tracking-b2b/' ),
	],
	[
		'title' => 'Anfrage- und Auftragskosten berechnen',
		'body'  => 'Wie Kosten pro Anfrage und Kosten pro Auftrag auseinanderliegen können.',
		'url'   => home_url( '/solar-leads-kosten-studie/#cpo' ),
	],
	[
		'title' => 'Der eigene Anfrageweg im Betrieb',
		'body'  => 'Vom Zielgebiet über die Projektangaben bis zur Übergabe an den Vertrieb.',
		'url'   => home_url( '/solar-waermepumpen-leadgenerierung/#strecke' ),
	],
	[
		'title' => 'Portal-Leads vs. eigenes System',
		'body'  => 'Beschaffungsmodelle, Eigentum und Qualität gemeinsam einordnen.',
		'url'   => home_url( '/solar-leads-kaufen-alternative/#vergleich' ),
	],
];

$faq_items = function_exists( 'nexus_get_e3_case_faq_items' ) ? nexus_get_e3_case_faq_items() : [];

$case_css_path = get_stylesheet_directory() . '/assets/css/e3-case-v2.css';
wp_enqueue_style(
	'hu-e3-case-v2',
	get_stylesheet_directory_uri() . '/assets/css/e3-case-v2.css',
	[ 'nexus-system-css' ],
	file_exists( $case_css_path ) ? (string) filemtime( $case_css_path ) : wp_get_theme()->get( 'Version' )
);

$track = static function ( $action, $category = 'lead_gen', $section = 'case_solar_methodology' ) {
	return sprintf(
		'data-track-action="%s" data-track-category="%s" data-track-section="%s"',
		esc_attr( $action ),
		esc_attr( $category ),
		esc_attr( $section )
	);
};

get_header();
?>

<div class="site-main">
	<article class="e3-case-v2 fs" <?php echo $tracking_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

		<section class="fs-abschnitt fs-abschnitt--start" id="fall" aria-labelledby="fs-titel">
			<div class="fs-rand"><b>Fallstudie</b>B2B · Photovoltaik<br><?php echo esc_html( $e3_timeframe ); ?></div>
			<div class="fs-inhalt fs-einstieg">
				<div class="fs-stapel">
					<p class="fs-kicker"><?php echo esc_html( ucfirst( $e3_case_label ) ); ?> · DACH</p>
					<h1 id="fs-titel">Von gekauften Leads zum eigenen Anfragesystem.</h1>
					<p class="fs-lead">Der Betrieb kaufte seine Anfragen bei Portalen, zu <?php echo esc_html( $e3_cpl_before ); ?> pro Stück. Ich habe ihm eine eigene Strecke gebaut: von der Anzeige über Landingpage und Formular bis ins CRM und von dort als Signal zurück in die Werbekonten.</p>
					<div class="fs-rolle">
						<div>
							<h2 class="fs-label">Mein Teil</h2>
							<ul>
								<?php foreach ( $role_own as $item ) : ?>
									<li><?php echo esc_html( $item ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
						<div>
							<h2 class="fs-label">Teil des Betriebs</h2>
							<ul>
								<?php foreach ( $role_business as $item ) : ?>
									<li><?php echo esc_html( $item ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>

				<aside class="tafel fs-beweis" id="fs-beweis" aria-label="Ergebnisse des Falls">
					<p class="fs-beweis__kopf">Werbekosten pro Anfrage · maßstäblich</p>
					<div class="fs-balken">
						<div class="fs-balken__zeile" style="--anteil:1">
							<span class="fs-balken__lab">Portal-Einkauf, vorher</span><span class="fs-balken__wert"><?php echo esc_html( $e3_cpl_before ); ?></span>
							<div class="fs-balken__spur" aria-hidden="true"><div class="fs-balken__fuell"></div></div>
						</div>
						<div class="fs-balken__zeile fs-balken__zeile--eigen" style="--anteil:<?php echo esc_attr( (string) $e3_cpl_share ); ?>">
							<span class="fs-balken__lab">Eigene Anfrage, <?php echo esc_html( $e3_reached ); ?></span><span class="fs-balken__wert"><?php echo esc_html( $e3_cpl_after ); ?></span>
							<div class="fs-balken__spur" aria-hidden="true"><div class="fs-balken__fuell"></div></div>
						</div>
						<div class="fs-balken__skala" aria-hidden="true"><span>0 €</span><span>50 €</span><span>100 €</span><span>150 €</span></div>
					</div>
					<div class="fs-kennz">
						<div><strong class="fs-zaehler" data-ziel="<?php echo esc_attr( (string) HU_E3_LEAD_COUNT ); ?>" data-format="de"><?php echo esc_html( $e3_lead_count ); ?></strong><span>Anfragen in <?php echo esc_html( $e3_timeframe_dt ); ?></span></div>
						<div><strong class="fs-zaehler" data-ziel="<?php echo esc_attr( (string) HU_E3_SALES_CONVERSION_PERCENT ); ?>" data-suffix=" %"><?php echo esc_html( $e3_conv_after ); ?></strong><span>Abschlussquote der vorqualifizierten CRM-Anfragen</span></div>
						<div><strong>−<?php echo esc_html( $e3_reduction ); ?> %</strong><span>Kosten pro Anfrage, Portal gegen eigene Werbekosten</span></div>
					</div>
					<p class="fs-klein"><?php echo esc_html( $e3_cpl_before ); ?> ist der Einkaufspreis einer Portal-Anfrage, <?php echo esc_html( $e3_cpl_after ); ?> sind reine Werbekosten einer eigenen Anfrage, ohne Aufbau und Betreuung. <?php echo esc_html( $e3_cpl_after ); ?> ist ein erreichter Wert, kein Durchschnitt über sechs Monate.</p>
				</aside>
			</div>
		</section>

		<section class="fs-abschnitt" id="ausgangslage" aria-labelledby="fs-ausgang">
			<div class="fs-rand"><b>Ausgangslage</b>Was vorher galt</div>
			<div class="fs-inhalt fs-stapel">
				<h2 id="fs-ausgang">Die Anfragen kamen. Gesteuert hat sie jemand anderes.</h2>
				<p>Den ersten Hinweis gab der Vertrieb: Anfragen aus einem Hersteller-Partnerprogramm wurden deutlich häufiger zu Aufträgen als die gekauften. Gleiches Team, gleiche Produkte. Der Unterschied lag in der Anfrage selbst, also in Kaufabsicht, Vorqualifizierung und Tempo.</p>
				<div class="fs-tabelle">
					<table>
						<thead><tr><td></td><th scope="col">Portal-Einkauf</th><th scope="col">Eigene Strecke</th></tr></thead>
						<tbody>
							<?php foreach ( $comparison_rows as $row ) : ?>
								<tr><th scope="row"><?php echo esc_html( $row[0] ); ?></th><td class="fs-alt"><?php echo esc_html( $row[1] ); ?></td><td><?php echo esc_html( $row[2] ); ?></td></tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</section>

		<section class="fs-abschnitt" id="system" aria-labelledby="fs-umsetzung">
			<div class="fs-rand"><b>Umsetzung</b>Drei Arbeitsbereiche</div>
			<div class="fs-inhalt">
				<h2 id="fs-umsetzung">Eine Strecke, die aus jedem Abschluss lernt.</h2>

				<figure class="fs-strecke" id="fs-strecke">
					<figcaption><span>Schema · vereinfacht, keine Originaldaten</span><span>Akzentlinie: die Rückkopplung aus dem CRM</span></figcaption>
					<?php get_template_part( 'template-parts/case-study-strecke' ); ?>
				</figure>

				<div class="fs-bereiche">
					<?php foreach ( $work_areas as $area ) : ?>
						<article class="fs-bereich">
							<div class="fs-bereich__titel"><span><?php echo esc_html( $area['stations'] ); ?></span><h3><?php echo esc_html( $area['title'] ); ?></h3></div>
							<div class="fs-feld">
								<h4>Umgesetzt</h4>
								<ul>
									<?php foreach ( $area['done'] as $item ) : ?>
										<li><?php echo esc_html( $item ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
							<div class="fs-feld">
								<h4>Im Betrieb</h4>
								<p><?php echo esc_html( $area['benefit'] ); ?></p>
								<p class="fs-beleg"><?php echo esc_html( $area['proof'] ); ?></p>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<div class="fs-schema">
					<div class="fs-formular" role="img" aria-label="Schema des Formulars: Schritt 1 fragt das Produktinteresse ab, Schritt 2 Objekt und Zeitrahmen, Schritt 3 den Kontakt.">
						<span class="fs-formular__schritt">Schema · Formular, Schritt 1 von 3</span>
						<p class="fs-formular__frage">Wofür interessieren Sie sich?</p>
						<div class="fs-formular__optionen"><span class="is-an">Photovoltaik</span><span>Wärmepumpe</span><span>Beides</span></div>
						<span class="fs-formular__schritt">Schritt 2 · Objekt und Zeitrahmen</span>
						<span class="fs-formular__schritt">Schritt 3 · Kontakt</span>
					</div>
					<dl class="fs-crm" aria-label="Schema eines CRM-Eintrags">
						<div><dt>Produktinteresse</dt><dd>Photovoltaik</dd></div>
						<div><dt>Herkunft</dt><dd>Meta · Kampagne Region</dd></div>
						<div><dt>Einstiegsseite</dt><dd>Landingpage Photovoltaik</dd></div>
						<div><dt>Status</dt><dd>qualifiziert → Termin</dd></div>
						<div><dt>Rückmeldung</dt><dd class="fs-hervor">Signal an Werbekonto</dd></div>
					</dl>
					<p class="fs-klein fs-schema__hinweis">Beide Darstellungen sind schematisch. Originalseiten und CRM-Daten des Betriebs werden nicht veröffentlicht.</p>
				</div>
			</div>
		</section>

		<section class="fs-abschnitt" id="ergebnisse" aria-labelledby="fs-ergebnis">
			<div class="fs-rand"><b>Ergebnisse</b>Verlauf und Grenzen</div>
			<div class="fs-inhalt fs-stapel">
				<h2 id="fs-ergebnis">Die Technik stand schnell. Die Zeit ging in Prüfen und Abstimmen.</h2>
				<p>Gebaut wurde erst, als klar war, was die Daten des Betriebs sagen: welche Produkte gerade gefragt waren, wie die Förderung wirkte und woran Interessenten zweifelten. Daraus entstanden Landingpages und Formulare. Dazu kam die Abstimmung mit der Geschäftsführung. Das kostete den ersten Monat und war die Voraussetzung dafür, dass <?php echo esc_html( $e3_reached ); ?> die Zahlen trugen.</p>
				<ol class="fs-phasen">
					<?php foreach ( $phases as $phase ) : ?>
						<li><span class="fs-phasen__zeit"><?php echo esc_html( $phase['label'] ); ?></span><span class="fs-phasen__wert"><?php echo esc_html( $phase['value'] ); ?></span><p><?php echo esc_html( $phase['body'] ); ?></p></li>
					<?php endforeach; ?>
				</ol>
				<div class="fs-grenzen">
					<div>
						<h3>Worauf sich die Zahlen beziehen</h3>
						<ul>
							<?php foreach ( $basis_items as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div>
						<h3>Was die Zahlen nicht sagen</h3>
						<ul>
							<?php foreach ( $limit_items as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</section>

		<section class="fs-abschnitt" id="cta" aria-labelledby="fs-abschluss">
			<div class="fs-rand"><b>Übertragbar</b>Auch außerhalb Solar</div>
			<div class="fs-inhalt">
				<div class="tafel fs-abschluss">
					<div class="fs-stapel">
						<h2 id="fs-abschluss">Die Branche war Solar. Die Aufgabe hat jeder B2B-Betrieb mit Vertrieb.</h2>
						<p>Diese Teile übernehme ich auch bei Ihnen, einzeln oder als ganze Strecke:</p>
						<ul class="fs-aufgaben">
							<?php foreach ( $transfer_items as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="fs-aktion">
						<a class="fs-knopf" href="<?php echo esc_url( $project_url ); ?>" <?php echo $track( 'cta_case_study_to_project' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>Projekt anfragen <span aria-hidden="true">→</span></a>
						<p class="fs-klein"><?php echo esc_html( $reply_label ); ?></p>
						<?php if ( '' !== $offer_price ) : ?>
							<a class="fs-link" href="<?php echo esc_url( $offer_url ); ?>" <?php echo $track( 'case_transfer_offer_anfragestrecke', 'navigation' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>Diese Strecke als Festpreis: <?php echo esc_html( $offer_price ); ?>, mit CRM-Anbindung <?php echo esc_html( $offer_crm ); ?> netto</a>
						<?php endif; ?>
						<a class="fs-link fs-link--leise" href="<?php echo esc_url( $diagnostic_url ); ?>" <?php echo $track( 'cta_case_study_to_diagnostic_request' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>Solar- oder Wärmepumpenbetrieb? Zum kostenlosen Marktcheck</a>
					</div>
				</div>
			</div>
		</section>

		<section class="fs-abschnitt" id="vertiefung" aria-labelledby="fs-vertiefung">
			<div class="fs-rand"><b>Vertiefen</b>Methodik</div>
			<div class="fs-inhalt">
				<h2 class="fs-h2-leise" id="fs-vertiefung">Die Fachseiten hinter dem Fall.</h2>
				<ul class="fs-verweise">
					<?php foreach ( $deeper_links as $link ) : ?>
						<li><a href="<?php echo esc_url( $link['url'] ); ?>" <?php echo $track( 'cta_case_study_deeper_link', 'navigation', 'vertiefung' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><strong><?php echo esc_html( $link['title'] ); ?></strong><span><?php echo esc_html( $link['body'] ); ?></span></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>

		<?php if ( ! empty( $faq_items ) ) : ?>
			<section class="fs-abschnitt" id="fragen" aria-labelledby="fs-fragen">
				<div class="fs-rand"><b>Fragen</b>Zum Fall</div>
				<div class="fs-inhalt">
					<h2 class="fs-h2-leise" id="fs-fragen">Was häufig nachgefragt wird.</h2>
					<div class="fs-faq">
						<?php foreach ( $faq_items as $faq_item ) : ?>
							<details>
								<summary><?php echo esc_html( $faq_item['question'] ); ?></summary>
								<p><?php echo esc_html( $faq_item['answer'] ); ?></p>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	</article>
</div>

<?php
get_template_part(
	'template-parts/seo-subpage-sticky-cta',
	null,
	[
		'cta_url'        => $project_url,
		'track_category' => 'case_solar_methodology',
		'lead'           => 'Diese Strecke für Ihr Angebot',
		'sub'            => $reply_label,
		'label'          => 'Projekt anfragen',
		'track_action'   => 'cta_sticky_case_study_project',
	]
);

get_footer();
