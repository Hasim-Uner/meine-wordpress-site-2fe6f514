<?php
/**
 * Template Name: Solar-Leads-Kosten – CPL und Kosten pro Auftrag
 * Description: Kosten- und Rechenleitfaden für Photovoltaik- und Wärmepumpen-Anfragen.
 * Übernimmt die CPL-Vertiefung; Preisannahmen und Fallwerte bleiben getrennt.
 * @package Blocksy_Child
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$page_url = home_url( '/solar-leads-kosten-studie/' );
$solar_money_url = nexus_get_energy_systems_url();
$marktcheck_url = trailingslashit( $solar_money_url ) . '#marktcheck';
$compare_url = home_url( '/solar-leads-kaufen-alternative/' );
$e3_url = home_url( '/case-study-solar-leadgenerierung/' );
$marketcheck_price = hu_marketcheck_price();
$foundation_price = hu_foundation_price_display();
$hosting_price = hu_foundation_hosting_display();
$e3_cpl = hu_e3_metric( 'cpl_after' );
$e3_conversion = hu_e3_metric( 'sales_conversion' );

$cost_factors = [
	[ 't' => 'Was wird abgerechnet?', 's' => 'Kontakt, qualifizierte Anfrage oder vereinbarter Termin: Preis und Leistung sind erst vergleichbar, wenn das abrechenbare Ereignis feststeht.' ],
	[ 't' => 'Welche Arbeit bleibt im Vertrieb?', 's' => 'Rückrufversuche, fehlende Projektangaben, Angebote und Nachfassen gehören in dieselbe Kostenrechnung wie der Einkauf.' ],
	[ 't' => 'Welche Aufträge sind daraus entstanden?', 's' => 'Ordnen Sie abgeschlossene Projekte der ursprünglichen Anfragequelle zu. Offene Angebote sind noch keine gewonnenen Aufträge.' ],
	[ 't' => 'Welcher Zeitraum ist vergleichbar?', 's' => 'Verwenden Sie eine ausreichend nachverfolgte Anfragegruppe. Neue Kontakte und Abschlüsse aus älteren Anfragen dürfen nicht in einer Quote vermischt werden.' ],
];
$faqs = [
	[ 'question' => 'Was kosten Solar- und Photovoltaik-Leads?', 'answer' => 'Es gibt keinen einheitlichen Preis für einen Solar-Lead. Region, Exklusivität, Qualifizierung, Menge und Reklamationsregeln gehören in das schriftliche Angebot. Rechnen Sie anschließend mit Ihren gewonnenen Aufträgen und Ihrer Vertriebszeit; eine allgemeine Preisspanne ersetzt diese Prüfung nicht.' ],
	[ 'question' => 'Wie berechnet man den Cost per Lead für Photovoltaik?', 'answer' => 'Teilen Sie die berücksichtigten Anfragekosten durch die Anzahl der gewonnenen Anfragen. Benennen Sie, ob nur Einkauf oder Werbebudget enthalten ist oder auch Aufbau, Betreuung und Vertrieb. Der CPL beschreibt Kosten pro Kontakt; über dessen wirtschaftlichen Wert entscheidet erst die weitere Bearbeitung.' ],
	[ 'question' => 'Wie werden die Kosten pro Auftrag berechnet?', 'answer' => 'Die reinen Anfragekosten pro Auftrag ergeben sich aus CPL geteilt durch Abschlussquote. Anfragekosten und Quote müssen sich auf dieselbe Anfragegruppe beziehen. Für eine vollständigere Rechnung addieren Sie Vertriebszeit, Betreuung und gegebenenfalls den anteiligen Aufbau, bevor Sie durch die gewonnenen Aufträge teilen.' ],
	[ 'question' => 'Sind exklusive Leads automatisch wirtschaftlicher?', 'answer' => 'Exklusivität verringert die direkte Parallelverteilung, belegt aber weder Projektpassung noch Erreichbarkeit oder einen Abschluss. Ein höherer Preis kann sich tragen, wenn Ihre tatsächlich gemessene Abschlussquote und Ihr Vertriebseinsatz dazu passen.' ],
	[ 'question' => 'Kann man aus dem PV-Referenzfall einen Preis pro Auftrag ableiten?', 'answer' => sprintf( 'Nein. Die erreichten %s Kampagnen-CPL und die %s Abschlussquote der vorqualifizierten CRM-Leads haben unterschiedliche Bezugsgrößen. Daraus lässt sich kein gemessener CPO berechnen. Der Fall dokumentiert den Aufbau und die beobachteten Ergebnisse dieses Betriebs.', $e3_cpl, $e3_conversion ) ],
	[ 'question' => 'Was kommt beim eigenen Anfragesystem zum Aufbaupreis hinzu?', 'answer' => sprintf( 'Der Aufbau beginnt bei %s netto für eine Produktstrecke. Rund %s Hosting im Monat und das Werbebudget kommen hinzu. Betreuung, zusätzliche Integrationen und der interne Vertrieb müssen nach dem vereinbarten Umfang separat berücksichtigt werden. Der Vergleich braucht auf beiden Seiten denselben Zeitraum und vollständige Kosten.', $foundation_price, $hosting_price ) ],
];
$resources = [
	[ 't' => 'Zukauf oder eigener Anfrageweg?', 's' => 'Beschaffungsmodelle, Eigentum und Anfragequalität zusammen prüfen.', 'url' => $compare_url . '#vergleich' ],
	[ 't' => 'Wärmepumpen-Anfragen einordnen', 's' => 'Welche Projektangaben der SHK-Vertrieb vor dem Rückruf braucht.', 'url' => home_url( '/waermepumpen-leads/#vorqualifizierung' ) ],
	[ 't' => 'PV-Termine für Gewerbeprojekte', 's' => 'Projektwert, Technik und Entscheidungsrollen vor der Terminvergabe.', 'url' => home_url( '/b2b-solar-leads/' ) ],
];
$faq_schema = [ '@context' => 'https://schema.org', '@type' => 'FAQPage', '@id' => $page_url . '#faq', 'url' => $page_url . '#faq', 'mainEntity' => [] ];
foreach ( $faqs as $item ) {
	$faq_schema['mainEntity'][] = [ '@type' => 'Question', 'name' => $item['question'], 'acceptedAnswer' => [ '@type' => 'Answer', 'text' => $item['answer'] ] ];
}
$article_schema = [
	'@context' => 'https://schema.org', '@type' => 'Article', '@id' => $page_url . '#article',
	'headline' => 'Solar-Leads-Kosten: CPL und Kosten pro gewonnenem Auftrag', 'url' => $page_url,
	'description' => 'Kostenleitfaden für Photovoltaik- und Wärmepumpen-Anfragen: CPL, Vertriebseinsatz, Abschlussquote und eigener Anfrageweg mit transparenten Bezugsgrößen.',
	'author' => hu_get_canonical_author_person(), 'publisher' => [ '@id' => home_url( '/#organization' ) ],
	'mainEntityOfPage' => [ '@type' => 'WebPage', '@id' => $page_url ],
];
$breadcrumb_schema = hu_get_solar_subpage_breadcrumb_schema( $page_url, 'Solar-Leads-Kosten' );
get_header();
?>
<div id="primary" class="hu-intercept hu-buy hu-cost" data-track-page="solar-leads-kosten-studie">
	<section class="hu-buy__band hu-buy__band--dark hu-buy__band--hero" data-nx-theme="dark" aria-labelledby="hu-study-title" data-track-section="hero">
		<div class="hu-buy__container">
			<p class="hu-buy__eyebrow">Für Photovoltaik- und Wärmepumpen-Betriebe</p>
			<h1 class="hu-buy__h1" id="hu-study-title">Solar-Leads-Kosten.<br><em>Rechnen Sie bis zum Auftrag.</em></h1>
			<p class="hu-buy__lead">Der Preis pro Anfrage ist der Einstieg in die Rechnung. Für Ihre Kanalentscheidung zählen zusätzlich Vertriebszeit, Abschlussquote und die Kosten des gesamten Anfragewegs.</p>
			<div class="hu-buy__actions">
				<a class="hu-buy__button hu-buy__button--primary" href="#cpo" data-track-action="cta_cost_calculator" data-track-category="engagement" data-track-section="hero">Mit eigenen Zahlen rechnen <span aria-hidden="true">→</span></a>
				<a class="hu-buy__button hu-buy__button--secondary" href="<?php echo esc_url( $compare_url ); ?>" data-track-action="cta_solar_buy_comparison" data-track-category="internal_link" data-track-section="hero">Zukauf und eigenen Kanal vergleichen</a>
			</div>
			<p class="hu-buy__microcopy">CPL = Kosten pro Anfrage · CPO = Kosten pro gewonnenem Auftrag · keine pauschale Amortisationszusage</p>
			<nav class="hu-buy__jump" aria-label="Auf dieser Seite"><a href="#cpo">Berechnen</a><a href="#kostenfaktoren">Kosten vollständig erfassen</a><a href="#methodik">Fallwerte einordnen</a></nav>
		</div>
	</section>
	<section class="hu-buy__band hu-buy__band--light hu-buy__band--white" id="kostenfaktoren" aria-labelledby="hu-study-costs-title" data-track-section="kostenfaktoren">
		<div class="hu-buy__container">
			<header class="hu-buy__section-head"><p class="hu-buy__eyebrow">Vor dem Preisvergleich</p><h2 class="hu-buy__h2" id="hu-study-costs-title">Ein Kontaktpreis beantwortet noch keine Kostenfrage.</h2><p class="hu-buy__section-lead">Legen Sie für jeden Anbieter und Ihren eigenen Kanal dieselben Maßstäbe an. So werden aus unterschiedlich verpackten Leistungen vergleichbare Kosten.</p></header>
			<div class="hu-buy__model-grid">
				<?php foreach ( $cost_factors as $item ) : ?>
				<article class="hu-buy__model-card"><h3><?php echo esc_html( $item['t'] ); ?></h3><p><?php echo esc_html( $item['s'] ); ?></p></article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<section class="hu-buy__band hu-buy__band--light hu-buy__band--cream" id="cpo" aria-labelledby="hu-study-cpo-title" data-track-section="cpo">
		<div class="hu-buy__container">
			<header class="hu-buy__section-head"><p class="hu-buy__eyebrow">Ihr Preis und Ihr Vertrieb</p><h2 class="hu-buy__h2" id="hu-study-cpo-title">Was kostet Ihr Anfragekanal pro gewonnenem Auftrag?</h2><p class="hu-buy__section-lead">Anfragekosten und Abschlussquote müssen zur gleichen Anfragegruppe gehören. Setzen Sie für offene Vertriebszyklen bewusst eine Annahme an und prüfen Sie sie später gegen die tatsächlichen Abschlüsse.</p></header>
			<aside class="hu-buy__equation" aria-label="Formel für die Anfragekosten pro Auftrag"><span>CPL</span><b aria-hidden="true">÷</b><span>Abschlussquote</span><b aria-hidden="true">=</b><strong>reine Anfragekosten je Auftrag</strong></aside>
			<?php
			echo hu_render_portal_cpo_calculator( [ // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared renderer escapes every value.
				'provider_label' => 'Ihren Anfragekanal', 'tracking_prefix' => 'solar_costs', 'tracking_section' => 'cpo',
				'defaults' => [ 'cpl' => '', 'leads' => '', 'close_rate' => '', 'sales_minutes' => '', 'hourly_rate' => '' ],
				'require_complete' => true,
				'heading' => 'Anfrage- und Vertriebskosten zusammen rechnen',
				'description' => 'Tragen Sie Preis und Menge aus Ihrem Vertrag oder Ihrer Auswertung ein. Abschlussquote, Bearbeitungszeit und interner Stundensatz ergänzen die Rechnung.',
				'example_text' => 'Keine Anbieterpreise oder Branchenquoten voreingestellt. Es werden keine Eingaben gespeichert oder übertragen.',
				'cta_label' => 'Ausgangslage im Marktcheck einordnen',
				'cta_note' => sprintf( 'Marktcheck: %s netto, Befund per E-Mail %s. Aufbau, Betreuung und weitere Kanal-Kosten sind in dieser Rechnung nicht enthalten.', $marketcheck_price, hu_marketcheck_reply_label() ),
			] );
			?>
			<noscript><p class="hu-buy__section-lead">Der Rechner benötigt JavaScript. Sie können mit der Formel rechnen: Anfragegebühren plus interne Vertriebskosten, geteilt durch gewonnene Aufträge. Der Marktcheck bleibt über den Abschluss dieser Seite erreichbar.</p></noscript>
		</div>
	</section>
	<section class="hu-buy__band hu-buy__band--light hu-buy__band--white" id="gesamtkosten" aria-labelledby="hu-study-total-title" data-track-section="gesamtkosten">
		<div class="hu-buy__container">
			<header class="hu-buy__section-head"><p class="hu-buy__eyebrow">Derselbe Zeitraum für beide Wege</p><h2 class="hu-buy__h2" id="hu-study-total-title">Aufbaupreis und Lead-Einkauf sind verschiedene Kostenbestandteile.</h2><p class="hu-buy__section-lead">Ein eigener Anfrageweg beginnt bei <?php echo esc_html( $foundation_price ); ?> netto für eine Produktstrecke. Hinzu kommen rund <?php echo esc_html( $hosting_price ); ?> monatlich für Hosting sowie Werbebudget, vereinbarte Betreuung und interner Vertrieb.</p></header>
			<div class="hu-buy__compare-grid">
				<article class="hu-buy__compare-card hu-buy__compare-card--rent"><h3>Portal- oder Kampagnen-Zukauf</h3><p>Anfragegebühren, eventuelle Grund- und Servicegebühren, Reklamationen sowie Bearbeitungszeit und Angebote berücksichtigen. Ein CRM kann auch bei gekauften Kontakten eigene Kundendaten erhalten.</p></article>
				<article class="hu-buy__compare-card hu-buy__compare-card--own"><h3>Eigener Anfrageweg</h3><p>Aufbau auf denselben Betrachtungszeitraum verteilen. Hosting, Werbung, Optimierung und Vertrieb ergänzen. Der Wert übergebener Seiten und Konten ist ein eigener Entscheidungsgrund, kein garantierter Kostenvorteil.</p></article>
			</div>
			<a class="hu-buy__text-link" href="<?php echo esc_url( $compare_url . '#vergleich' ); ?>" data-track-action="related_solar_comparison" data-track-category="internal_link" data-track-section="gesamtkosten">Eigentum, Vertragsregeln und Qualität vergleichen →</a>
		</div>
	</section>
	<section class="hu-buy__band hu-buy__band--dark hu-buy__band--deep" id="methodik" aria-labelledby="hu-study-method-title" data-track-section="methodik">
		<div class="hu-buy__container">
			<header class="hu-buy__section-head"><p class="hu-buy__eyebrow">Ein dokumentierter PV-Fall</p><h2 class="hu-buy__h2" id="hu-study-method-title">Fallwerte sind ein Beleg. Keine Preisliste für Ihren Betrieb.</h2><p class="hu-buy__section-lead">Im Referenzfall wurden <?php echo esc_html( $e3_cpl ); ?> Kampagnen-CPL erreicht. Unter den vorqualifizierten CRM-Leads lag die Abschlussquote bei <?php echo esc_html( $e3_conversion ); ?>. Die Bezugsgrößen unterscheiden sich; ein gemessener Preis pro Auftrag lässt sich daraus nicht berechnen.</p></header>
			<p class="hu-buy__section-lead">Für Gewerbe-PV und Wärmepumpen ist daraus ebenfalls keine Ergebnisprognose ableitbar. Verwenden Sie im Rechner ausschließlich Ihre Daten oder klar gekennzeichnete Planungsannahmen.</p>
			<a class="hu-buy__button hu-buy__button--secondary" href="<?php echo esc_url( $e3_url ); ?>" data-track-action="cta_case_study" data-track-category="proof" data-track-section="methodik">Herleitung und Grenzen des Falls lesen</a>
		</div>
	</section>
	<section class="hu-buy__band hu-buy__band--light hu-buy__band--white" id="vertiefung" aria-labelledby="hu-study-resources-title" data-track-section="vertiefung">
		<div class="hu-buy__container"><h2 class="hu-buy__h2" id="hu-study-resources-title">Die nächste Frage gezielt vertiefen.</h2><div class="hu-buy__resource-grid">
			<?php foreach ( $resources as $item ) : ?>
			<article class="hu-buy__resource-card"><h3><a href="<?php echo esc_url( $item['url'] ); ?>" data-track-action="related_content" data-track-category="internal_link" data-track-section="vertiefung"><?php echo esc_html( $item['t'] ); ?></a></h3><p><?php echo esc_html( $item['s'] ); ?></p></article>
			<?php endforeach; ?>
		</div></div>
	</section>
	<section class="hu-buy__band hu-buy__band--dark hu-buy__band--warm" id="faq" aria-labelledby="hu-study-faq-title" data-track-section="faq">
		<div class="hu-buy__container hu-buy__container--narrow"><h2 class="hu-buy__h2" id="hu-study-faq-title">Fragen zu Solar-Leads, CPL und Abschlusskosten.</h2><div class="hu-buy__faq-list">
			<?php foreach ( $faqs as $item ) : ?>
			<details class="hu-buy__faq-item" name="hu-faq-kosten-studie"><summary><?php echo esc_html( $item['question'] ); ?></summary><p><?php echo esc_html( $item['answer'] ); ?></p></details>
			<?php endforeach; ?>
		</div></div>
	</section>
	<section class="hu-buy__band hu-buy__band--light hu-buy__band--final" id="final-cta" aria-labelledby="hu-study-final-title" data-track-section="final">
		<div class="hu-buy__container hu-buy__container--narrow hu-buy__final-inner"><p class="hu-buy__eyebrow">Vor der Investition</p><h2 class="hu-buy__h2" id="hu-study-final-title">Klären Sie, welcher Anfrageweg zu Ihrem Betrieb passt.</h2><p class="hu-buy__final-text">Der Marktcheck ordnet Angebot, Zielgebiet, Projektwerte und Bearbeitung ein. Sie erhalten einen Befund per E-Mail <?php echo esc_html( hu_marketcheck_reply_label() ); ?> mit einer Empfehlung und priorisierten Ansatzpunkten.</p><a class="hu-buy__button hu-buy__button--primary" href="<?php echo esc_url( $marktcheck_url ); ?>" data-track-action="cta_marktcheck" data-track-category="study_solar_leads_kosten" data-track-section="final">Marktcheck anfragen · <?php echo esc_html( $marketcheck_price ); ?> <span aria-hidden="true">→</span></a><p class="hu-buy__final-note">Netto · Absenden ist noch keine Buchung · bei Beauftragung des Anfragesystems anrechenbar</p></div>
	</section>
	<script type="application/ld+json"><?php echo wp_json_encode( $article_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<script type="application/ld+json"><?php echo wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
</div>
<?php
get_template_part( 'template-parts/seo-subpage-sticky-cta', null, [ 'marktcheck_url' => $marktcheck_url, 'track_category' => 'study_solar_leads_kosten', 'label' => 'Marktcheck · ' . $marketcheck_price, 'hide_when_visible' => '#final-cta' ] );
get_footer();
