<?php
/**
 * Template Name: Ergebnisse Hub
 * Description: Vertrauensschicht fuer alle drei kommerziellen Wege — Belege zuerst, Auswahl am Ende.
 *
 * Rolle laut docs/architecture/CONVERSION_ROUTING.md: Proof-/Evaluierungs-Hub,
 * kein vierter Angebotsweg. Die Seite gehoert Besuchern aus allen drei Routen
 * (Energy, direkte WordPress-Projekte, Agenturen) und darf deshalb weder im
 * Hero noch im Abschluss einen davon zum Standard machen.
 *
 * Bis 2026-09 hiess die Datei page-case-studies-e-commerce.php und verdrahtete
 * den primaeren CTA mit dem Solar-Marktcheck. Der Marktcheck steht jetzt nur
 * noch in der Energy-Karte des Abschlussblocks, ausdruecklich benannt — die
 * erlaubte Segmentierung aus CONVERSION_ROUTING, kein globaler CTA.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$e3_canon      = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url   = $e3_canon['url'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$e3_case_label = $e3_canon['case_label'] ?? 'mittelständischer PV-Installationsbetrieb';
$metric        = static function ( $key, $field = 'display', $fallback = '' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $key, $field, $fallback ) : $fallback;
};

$routes         = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$energy_url     = $routes['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' );
$whitelabel_url = $routes['whitelabel'] ?? home_url( '/whitelabel-retainer/' );
$tracking_url   = $routes['tracking_b2b'] ?? home_url( '/server-side-tracking-b2b/' );
$freelancer_url = $routes['freelancer'] ?? home_url( '/wordpress-freelancer-hannover/' );

$marketcheck_url   = function_exists( 'hu_get_request_analysis_url' )
	? hu_get_request_analysis_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$marketcheck_label = function_exists( 'nexus_get_primary_request_cta_label' )
	? nexus_get_primary_request_cta_label()
	: 'Marktcheck starten';
$marketcheck_reply = function_exists( 'hu_marketcheck_reply_label' ) ? hu_marketcheck_reply_label() : '';

$project_url = function_exists( 'hu_get_navigation_project_request_url' )
	? hu_get_navigation_project_request_url()
	: home_url( '/kontakt/' );

// Dasselbe Ziel wie "Aufgabe beschreiben" auf der White-Label-Route.
$whitelabel_task_url = add_query_arg(
	[
		'type' => 'whitelabel',
		'case' => 'aufgabe',
	],
	$whitelabel_url
) . '#aufgabe';

$response_promise = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'sentence' ) : '';

$github_url    = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514';
$pagespeed_url = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/ergebnisse/' ) );

$references = function_exists( 'hu_public_reference_projects' ) ? hu_public_reference_projects() : [];

/*
 * Vorher/Nachher des dokumentierten Falls. Zwei Zeilen tragen Zahlen aus dem
 * Canon, zwei beschreiben die strukturelle Veraenderung — ohne die beiden
 * letzten liest sich der Fall wie ein reiner Rabatt auf Leadkosten.
 */
$case_ledger = [
	[
		'label'  => 'Kosten pro Anfrage',
		'before' => $metric( 'cpl_before', 'display', '150 €' ),
		'after'  => $metric( 'cpl_after', 'display', '22 €' ),
	],
	[
		'label'  => 'Abschlussquote',
		'before' => $metric( 'sales_conversion_before', 'display', '1 – 5 %' ),
		'after'  => $metric( 'sales_conversion', 'display', '15 %' ),
	],
	[
		'label'  => 'Anfragequelle',
		'before' => 'gekaufte Portal-Leads',
		'after'  => 'eigener Anfrageweg',
	],
	[
		'label'  => 'Zuordnung',
		'before' => 'keine durchgängige Attribution',
		'after'  => 'Tracking bis ins CRM',
	],
];

$case_scope = [ 'Website', 'Landingpages', 'Tracking', 'CRM-Übergabe', 'Vorqualifizierung', 'Kampagnen', 'laufende Optimierung' ];

/*
 * Begriffsdefinition direkt am Fall. Der Hub zeigte bis 2026-09 dieselbe Zahl
 * als "Sales-Conversion", waehrend die Startseite sie "Abschlussquote" nannte
 * und daneben eine zweite Quote fuehrte. Auf einer Seite, die Attribution
 * verkauft, ist das der teuerste kleine Fehler.
 */
$case_definitions = [
	[
		'term' => $metric( 'lead_conversion', 'label', 'Lead-Conversion-Rate' ),
		'text' => 'Besucher der Anfragestrecke, die eine Anfrage abschicken.',
	],
	[
		'term' => $metric( 'sales_conversion', 'label', 'Abschlussquote' ),
		'text' => 'Qualifizierte Anfragen, aus denen ein Auftrag wird.',
	],
];

// Labormesswerte dieser Route. Werte aus dem Canon, Einordnung in der Copy.
$live_proof_tiles = [
	[
		'value' => $metric( 'site_lighthouse_performance', 'display', '99' ),
		'max'   => $metric( 'site_lighthouse_performance', 'max', '/100' ),
		'label' => 'PageSpeed mobil',
	],
	[
		'value' => $metric( 'site_lighthouse_accessibility', 'display', '100' ),
		'max'   => $metric( 'site_lighthouse_accessibility', 'max', '/100' ),
		'label' => 'Barrierefreiheit',
	],
	[
		'value' => $metric( 'site_lighthouse_seo', 'display', '100' ),
		'max'   => $metric( 'site_lighthouse_seo', 'max', '/100' ),
		'label' => 'SEO',
	],
	[
		'value' => $metric( 'site_lighthouse_best_practices', 'display', '100' ),
		'max'   => $metric( 'site_lighthouse_best_practices', 'max', '/100' ),
		'label' => 'Best Practices',
	],
];

$measurement_chain = [
	[ 'title' => 'Browser', 'note' => 'Einwilligung entscheidet' ],
	[ 'title' => 'WordPress', 'note' => 'Seite und Formular' ],
	[ 'title' => 'Tag Manager', 'note' => 'im Browser' ],
	[ 'title' => 'Server-Container', 'note' => 'eigene Domain' ],
	[ 'title' => 'GA4 und Ads', 'note' => 'Konten des Kunden' ],
];

$whitelabel_delivery = [ 'WordPress-Umsetzung', 'Landingpages', 'Tracking & Attribution', 'Performance', 'Technisches SEO', 'Dokumentation', 'Git-Workflow' ];

/*
 * Der Abschluss. Drei Wege, drei verschiedene naechste Schritte — nicht drei
 * Varianten desselben Formulars. Die Energy-Karte benennt die Vertikale vor
 * dem Marktcheck-Link, sonst waere er hier ein fachfremder Default-CTA.
 */
$next_steps = [
	[
		'kind'   => 'energy',
		'index'  => '01',
		'kicker' => 'Solar · Wärmepumpe · Speicher',
		'title'  => 'Eigene Anfragen statt gekaufter Portal-Leads.',
		'desc'   => 'Sie kaufen Anfragen zu und wollen wissen, ob sich ein eigener Anfrageweg für Ihren Betrieb rechnet.',
		'url'    => $marketcheck_url,
		'label'  => $marketcheck_label,
		'note'   => '' !== $marketcheck_reply ? 'Befund ' . $marketcheck_reply . '.' : '',
		'action' => 'cta_results_next_energy',
	],
	[
		'kind'   => 'project',
		'index'  => '02',
		'kicker' => 'Direktes WordPress-Projekt',
		'title'  => 'Relaunch, Weiterentwicklung oder Tracking.',
		'desc'   => 'Sie haben eine Website oder planen eine neue und brauchen jemanden, der Umsetzung und Messung zusammen verantwortet.',
		'url'    => $project_url,
		'label'  => 'Projekt anfragen',
		'note'   => $response_promise,
		'action' => 'cta_results_next_project',
	],
	[
		'kind'   => 'agency',
		'index'  => '03',
		'kicker' => 'Agentur',
		'title'  => 'Technische Umsetzung unter Ihrem Namen.',
		'desc'   => 'Sie haben ein Kundenprojekt und brauchen Umsetzungskapazität, die im Hintergrund bleibt.',
		'url'    => $whitelabel_task_url,
		'label'  => 'Aufgabe beschreiben',
		'note'   => 'Erstprojekt mit festem Scope, Retainer erst danach.',
		'action' => 'cta_results_next_agency',
	],
];

get_header();
?>

<main id="main" class="site-main hu-erg" data-track-page="results_hub">

	<!-- 01 — Hero: Belege, keine Verkaufsfläche -->
	<section class="hu-erg-hero" data-track-section="hero" aria-labelledby="hu-erg-title">
		<div class="hu-erg__shell">
			<p class="hu-erg__eyebrow">Arbeitsbelege</p>
			<h1 class="hu-erg-hero__title" id="hu-erg-title">Ergebnisse und Arbeitsbelege.</h1>
			<p class="hu-erg-hero__lede">Was ich gebaut habe, was sich dadurch verändert hat — und welcher Teil davon ohne mein Zutun überprüfbar ist.</p>
			<p class="hu-erg-hero__scope">WordPress · Technisches SEO · Tracking · Conversion</p>

			<ul class="hu-erg-inventory" role="list">
				<li>
					<strong>1 dokumentiertes Großprojekt</strong>
					<span>Anfragesystem im Solar-Bereich, über sechs Monate belegt</span>
				</li>
				<li>
					<strong>3 öffentlich prüfbare Websites</strong>
					<span>direkt aufrufbar, ohne Freigabe durch mich</span>
				</li>
				<li>
					<strong>1 laufendes System</strong>
					<span>diese Website: Messwerte und Quellcode einsehbar</span>
				</li>
			</ul>

			<div class="hu-erg-hero__actions">
				<a class="hu-erg__link" href="#grossprojekt" data-track-action="results_hero_to_case" data-track-category="navigation" data-track-section="hero">Großprojekt ansehen <span aria-hidden="true">↓</span></a>
				<a class="hu-erg__link hu-erg__link--quiet" href="#technik" data-track-action="results_hero_to_proof" data-track-category="navigation" data-track-section="hero">Technische Belege ansehen <span aria-hidden="true">↓</span></a>
			</div>

			<p class="hu-erg__note hu-erg-hero__note">Diese Seite ist zum Prüfen gebaut. Welcher nächste Schritt zu Ihnen passt, entscheiden Sie am Ende selbst.</p>
		</div>
	</section>

	<!-- 02 — Flagship Case -->
	<section class="hu-erg-section hu-erg-case" id="grossprojekt" data-track-section="grossprojekt" aria-labelledby="hu-erg-case-title">
		<div class="hu-erg__shell">
			<div class="hu-erg__head nx-reveal">
				<p class="hu-erg__eyebrow">01 — Dokumentiertes Großprojekt</p>
				<p class="hu-erg-case__client"><?php echo esc_html( ucfirst( $e3_case_label ) ); ?></p>
				<h2 id="hu-erg-case-title">Vom gekauften Portal-Lead zum eigenen Anfrageweg.</h2>
				<p class="hu-erg__lede">Website, Landingpages, Kampagnen, Tracking, Vorqualifizierung und die Übergabe an den Vertrieb wurden als eine zusammenhängende Strecke aufgebaut und anschließend nachgeschärft.</p>
			</div>

			<ol class="hu-erg-ledger nx-reveal" role="list" aria-label="Veränderung im dokumentierten Fall">
				<?php foreach ( $case_ledger as $row ) : ?>
					<li class="hu-erg-ledger__row">
						<span class="hu-erg-ledger__label"><?php echo esc_html( $row['label'] ); ?></span>
						<span class="hu-erg-ledger__cell hu-erg-ledger__cell--before">
							<span class="hu-erg-ledger__state">vorher</span>
							<span class="hu-erg-ledger__value"><?php echo esc_html( $row['before'] ); ?></span>
						</span>
						<span class="hu-erg-ledger__arrow" aria-hidden="true">→</span>
						<span class="hu-erg-ledger__cell hu-erg-ledger__cell--after">
							<span class="hu-erg-ledger__state">nachher</span>
							<span class="hu-erg-ledger__value"><?php echo esc_html( $row['after'] ); ?></span>
						</span>
					</li>
				<?php endforeach; ?>
			</ol>

			<p class="hu-erg-case__band nx-reveal">
				<strong><?php echo esc_html( $metric( 'lead_count', 'display', '1.750+' ) ); ?></strong> qualifizierte Anfragen
				<span aria-hidden="true">·</span>
				<strong><?php echo esc_html( $metric( 'lead_conversion', 'display', '12 %' ) ); ?></strong> Lead-Conversion-Rate
				<span aria-hidden="true">·</span>
				<strong><?php echo esc_html( $metric( 'timeframe', 'display', '6 Monate' ) ); ?></strong> dokumentierter Zeitraum
			</p>

			<div class="hu-erg-case__foot nx-reveal">
				<div class="hu-erg-case__scope">
					<h3>Mein Anteil an der Umsetzung</h3>
					<ul class="hu-erg-chips">
						<?php foreach ( $case_scope as $scope_item ) : ?>
							<li><?php echo esc_html( $scope_item ); ?></li>
						<?php endforeach; ?>
					</ul>
					<p class="hu-erg__note">Die Werte entstanden aus dem Gesamtsystem inklusive Vertrieb und Kampagnen. Sie isolieren keinen WordPress-Effekt und sind keine Prognose für andere Unternehmen.</p>
					<a class="hu-erg__link" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="cta_results_case_study" data-track-category="trust" data-track-section="grossprojekt">Vollständige Case Study lesen <span aria-hidden="true">→</span></a>
				</div>

				<div class="hu-erg-defs">
					<h3 class="hu-erg-defs__title">Damit die Zahlen vergleichbar bleiben</h3>
					<dl class="hu-erg-defs__list">
						<?php foreach ( $case_definitions as $definition ) : ?>
							<div>
								<dt><?php echo esc_html( $definition['term'] ); ?></dt>
								<dd><?php echo esc_html( $definition['text'] ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
					<p class="hu-erg__note">Beide Werte stammen aus demselben Zeitraum und heißen auf dieser Website überall gleich.</p>
				</div>
			</div>
		</div>
	</section>

	<!-- 03 — Weitere öffentlich prüfbare Arbeiten -->
	<?php if ( ! empty( $references ) ) : ?>
	<section class="hu-erg-section hu-erg-section--alt" id="arbeiten" data-track-section="arbeiten" aria-labelledby="hu-erg-works-title">
		<div class="hu-erg__shell">
			<div class="hu-erg__head nx-reveal">
				<p class="hu-erg__eyebrow">02 — Öffentlich prüfbar</p>
				<h2 id="hu-erg-works-title">Drei Websites, die Sie direkt aufrufen können.</h2>
				<p class="hu-erg__lede">Kein Zahlenmaterial, das ich selbst liefere. Struktur, Typografie, Ladeverhalten und Pflegbarkeit sehen Sie an der laufenden Website.</p>
			</div>

			<div class="hu-erg-works nx-reveal">
				<?php foreach ( $references as $reference ) : ?>
					<article class="hu-erg-work">
						<p class="hu-erg__eyebrow"><?php echo esc_html( $reference['discipline'] ); ?></p>
						<h3>
							<a href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="results_reference_open" data-track-category="trust" data-track-section="arbeiten">
								<?php echo esc_html( $reference['name'] ); ?> <span aria-hidden="true">↗</span>
							</a>
						</h3>
						<p class="hu-erg-work__role"><?php echo esc_html( $reference['role'] ); ?></p>
						<p><?php echo esc_html( $reference['text'] ); ?></p>
						<ul class="hu-erg-chips hu-erg-chips--quiet">
							<?php foreach ( $reference['stack'] as $stack_item ) : ?>
								<li><?php echo esc_html( $stack_item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- 04 — Technischer Eigenbeweis -->
	<section class="hu-erg-section hu-erg-tech" id="technik" data-track-section="technik" aria-labelledby="hu-erg-tech-title">
		<div class="hu-erg__shell">
			<div class="hu-erg__head nx-reveal">
				<p class="hu-erg__eyebrow">03 — Diese Website als Arbeitsprobe</p>
				<h2 id="hu-erg-tech-title">Nicht behauptet. Hier im Produktivbetrieb.</h2>
				<p class="hu-erg__lede">Performance, Barrierefreiheit, technisches SEO und Server-Side Tracking sind auf dieser Seite keine Folie im Angebot, sondern der laufende Zustand.</p>
			</div>

			<div class="hu-erg-tiles nx-reveal">
				<?php foreach ( $live_proof_tiles as $tile ) : ?>
					<div class="hu-erg-tile">
						<strong><?php echo esc_html( $tile['value'] ); ?><?php if ( '' !== $tile['max'] ) : ?><small><?php echo esc_html( $tile['max'] ); ?></small><?php endif; ?></strong>
						<span><?php echo esc_html( $tile['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
				<a class="hu-erg-tile hu-erg-tile--live" href="<?php echo esc_url( $tracking_url ); ?>" data-track-action="results_proof_tracking_page" data-track-category="proof" data-track-section="technik">
					<strong><span class="hu-erg-tile__pulse" aria-hidden="true"></span>Aktiv</strong>
					<span>Server-Side Tracking</span>
					<em>GA4 + Server-GTM</em>
				</a>
			</div>

			<p class="hu-erg__note hu-erg-tiles__note">Lighthouse liefert Labormesswerte, gemessen an dieser Seite. Sie ersetzen keine Felddaten aus echtem Nutzerverkehr — prüfen können Sie beides selbst.</p>

			<div class="hu-erg-chain nx-reveal">
				<h3 class="hu-erg-chain__title">Die Messkette dieser Seite</h3>
				<ol class="hu-erg-chain__flow" role="list">
					<?php foreach ( $measurement_chain as $index => $step ) : ?>
						<li>
							<span class="hu-erg-chain__index" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<strong><?php echo esc_html( $step['title'] ); ?></strong>
							<small><?php echo esc_html( $step['note'] ); ?></small>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>

			<div class="hu-erg-tech__links nx-reveal">
				<a class="hu-erg__link" href="<?php echo esc_url( $pagespeed_url ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="results_proof_pagespeed" data-track-category="proof" data-track-section="technik">Diese Seite bei PageSpeed Insights prüfen <span aria-hidden="true">↗</span></a>
				<a class="hu-erg__link" href="<?php echo esc_url( $github_url . '/commits/main/' ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="results_proof_github_history" data-track-category="proof" data-track-section="technik">Änderungsverlauf auf GitHub <span aria-hidden="true">↗</span></a>
				<a class="hu-erg__link" href="<?php echo esc_url( $github_url . '/blob/main/.github/workflows/ci.yml' ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="results_proof_github_ci" data-track-category="proof" data-track-section="technik">Automatisierte Prüfungen ansehen <span aria-hidden="true">↗</span></a>
			</div>
		</div>
	</section>

	<!-- 05 — White-Label: erklärte Lücke statt erfundener Referenz -->
	<section class="hu-erg-section hu-erg-section--alt" id="whitelabel" data-track-section="whitelabel" aria-labelledby="hu-erg-wl-title">
		<div class="hu-erg__shell hu-erg-wl">
			<div class="nx-reveal">
				<p class="hu-erg__eyebrow">04 — Nicht öffentliche Arbeit</p>
				<h2 id="hu-erg-wl-title">White-Label heißt: Der Endkunde muss meinen Namen nicht kennen.</h2>
				<p class="hu-erg__lede">Ein Teil meiner Arbeit entsteht für Agenturen und Partner. Diese Projekte laufen unter deren Namen und können hier nicht mit Kundenlogo stehen. Sichtbar bleibt, was geliefert wird.</p>
				<p class="hu-erg__note">Keine erfundenen Agentur-Referenzen. Gibt ein Partner eine Nennung frei, steht sie hier — vorher nicht.</p>
			</div>
			<div class="nx-reveal">
				<h3 class="hu-erg-wl__subtitle">Lieferobjekte</h3>
				<ul class="hu-erg-chips">
					<?php foreach ( $whitelabel_delivery as $delivery_item ) : ?>
						<li><?php echo esc_html( $delivery_item ); ?></li>
					<?php endforeach; ?>
				</ul>
				<a class="hu-erg__link" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="cta_results_whitelabel" data-track-category="segmentation" data-track-section="whitelabel">White-Label-Zusammenarbeit ansehen <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</section>

	<!-- 06 — Drei nächste Wege -->
	<section class="hu-erg-section hu-erg-next" id="weiter" data-track-section="weiter" aria-labelledby="hu-erg-next-title">
		<div class="hu-erg__shell">
			<div class="hu-erg__head nx-reveal">
				<p class="hu-erg__eyebrow">05 — Nächster Schritt</p>
				<h2 id="hu-erg-next-title">Was möchten Sie als Nächstes klären?</h2>
				<p class="hu-erg__lede">Die Belege sind für alle drei Wege dieselben. Der sinnvolle nächste Schritt ist es nicht.</p>
			</div>

			<div class="hu-erg-next__grid nx-reveal">
				<?php foreach ( $next_steps as $step ) : ?>
					<article class="hu-erg-next__card hu-erg-next__card--<?php echo esc_attr( $step['kind'] ); ?>">
						<span class="hu-erg-next__index" aria-hidden="true"><?php echo esc_html( $step['index'] ); ?></span>
						<p class="hu-erg__eyebrow"><?php echo esc_html( $step['kicker'] ); ?></p>
						<h3><?php echo esc_html( $step['title'] ); ?></h3>
						<p><?php echo esc_html( $step['desc'] ); ?></p>
						<a class="nx-btn nx-btn--primary hu-erg-next__cta" href="<?php echo esc_url( $step['url'] ); ?>" data-track-action="<?php echo esc_attr( $step['action'] ); ?>" data-track-category="lead_gen" data-track-section="weiter">
							<?php echo esc_html( $step['label'] ); ?> <span aria-hidden="true">→</span>
						</a>
						<?php if ( '' !== $step['note'] ) : ?>
							<p class="hu-erg__note"><?php echo esc_html( $step['note'] ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>

			<p class="hu-erg__note hu-erg-next__foot">Sie sind unsicher, welcher Weg passt? Beschreiben Sie kurz Ihre Ausgangslage über den <a href="<?php echo esc_url( $project_url ); ?>" data-track-action="cta_results_next_unsure" data-track-category="lead_gen" data-track-section="weiter">Projektweg</a> — ich ordne ein, was sinnvoll ist. Wenn es ein WordPress-Projekt ist, finden Sie Leistungen und Preise auf der <a href="<?php echo esc_url( $freelancer_url ); ?>" data-track-action="results_next_to_freelancer" data-track-category="navigation" data-track-section="weiter">Freelancer-Seite</a>; für Solar- und Wärmepumpenbetriebe steht die <a href="<?php echo esc_url( $energy_url ); ?>" data-track-action="results_next_to_energy" data-track-category="navigation" data-track-section="weiter">Branchenseite</a> davor.</p>
		</div>
	</section>

</main>

<?php get_footer(); ?>
