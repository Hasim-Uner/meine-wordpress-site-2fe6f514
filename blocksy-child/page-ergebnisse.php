<?php
/**
 * Template Name: Ergebnisse Hub
 * Description: WordPress-Arbeiten, dokumentierter Projektfall und nachvollziehbare Übergabe.
 *
 * Evidence hub for all three commercial routes. Canonical facts stay in
 * inc/canon/. Project inspection prompts are not claims of measured impact.
 * Blocksy owns the main landmark. Native disclosures need no route JavaScript.
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
$freelancer_url = $routes['freelancer'] ?? home_url( '/' );

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

// Reading prompts add context to the canonical reference facts without inventing
// a previous state, client quote, result metric or authorship beyond that source.
$inspection_notes = [
	'civaka-azad.org' => [
		'task' => 'Viele redaktionelle Inhalte brauchen mehr als eine ansprechende Startseite: Leser müssen Themen und ältere Beiträge wiederfinden können.',
		'check' => 'Öffnen Sie die Navigation, folgen Sie einem Thema ins Archiv und anschließend in einen Beitrag. So lässt sich die Informationshierarchie an einem konkreten Weg beurteilen.',
	],
	'hasimuener.org' => [
		'task' => 'Ein redaktionelles Eigenprojekt, bei dem der Text die Gestaltung trägt: Hier zählen Lesbarkeit, Rhythmus und Orientierung über längere Beiträge hinweg.',
		'check' => 'Vergleichen Sie einen längeren Beitrag auf schmalem und breitem Bildschirm: Zeilenlänge, Überschriften und Abstände bestimmen den Lesefluss.',
	],
	'kurdischer-rat.org' => [
		'task' => 'Eine Organisationswebsite muss unterschiedliche Informationen zusammenführen und sich geordnet weiterentwickeln lassen.',
		'check' => 'Prüfen Sie die öffentlich sichtbare Gliederung und die Wege zu den Inhalten. Der interne Veröffentlichungsprozess lässt sich am Frontend allein nicht beurteilen.',
	],
];

// Stable source links document a shipped revision, not a permanently green
// live state. The deployment itself is not inferred from CI configuration.
$proof_revision = '569055ddd28214a9ff6e511369619f5a604eff07';
$source_base    = $github_url . '/blob/' . $proof_revision . '/';
$implementation_evidence = [
	[
		'title' => 'Eine Angebotsseite, klare Zuständigkeit.',
		'text' => 'Die frühere Freelancer-Seite ist in der Startseite aufgegangen. Inhalte, Angebotsanker, interne Ziele und die Zuständigkeit für Freelancer-Suchanfragen wurden zusammengeführt.',
		'label' => 'Umfang der Zusammenführung lesen',
		'path' => 'docs/decisions/homepage-freelancer-konsolidierung.md',
		'action' => 'results_proof_migration_scope',
	],
	[
		'title' => 'Alte Einstiege führen weiter.',
		'text' => 'Die Weiterleitungslogik berücksichtigt die frühere Adresse, WordPress-URL-Varianten und Kampagnenparameter. Automatisierte Prüffälle sichern diese Regeln bei weiteren Änderungen ab.',
		'label' => 'Prüffälle im Code ansehen',
		'path' => 'scripts/lint-entity-crawler-signals.php',
		'action' => 'results_proof_migration_checks',
	],
	[
		'title' => 'Das gewählte Anliegen bleibt erhalten.',
		'text' => 'Wer auf der Startseite ein Relaunch-, Landingpage- oder Tracking-Projekt auswählt, kommt mit diesem Anliegen in die Kontaktstrecke. Die Themenauswahl muss dort nicht wiederholt werden.',
		'label' => 'Anfragewege nachvollziehen',
		'path' => 'docs/architecture/CONVERSION_ROUTING.md',
		'action' => 'results_proof_request_routing',
	],
];

$next_steps = [
	[
		'kind' => 'project', 'kicker' => 'Für Ihr Unternehmen',
		'title' => 'Eine Website bauen oder gezielt verbessern.',
		'desc' => 'Beschreiben Sie Ihre Ausgangslage und was sich ändern soll. Daraus klären wir den passenden Umfang.',
		'url' => $project_url, 'label' => 'Projekt anfragen',
		'note' => $response_promise, 'action' => 'cta_results_next_project',
	],
	[
		'kind' => 'agency', 'kicker' => 'Für Ihre Agentur',
		'title' => 'Ein Kundenprojekt zur Umsetzung übergeben.',
		'desc' => 'Ein Briefing, Entwurf oder technisches Problem reicht für den Einstieg. Umfang und Abnahme klären wir vor dem Erstprojekt.',
		'url' => $whitelabel_task_url, 'label' => 'Aufgabe beschreiben',
		'note' => 'Laufende Kapazität ist nach dem Erstprojekt optional.', 'action' => 'cta_results_next_agency',
	],
	[
		'kind' => 'energy', 'kicker' => 'Solar · Wärmepumpe · Speicher',
		'title' => 'Einen eigenen Anfragekanal prüfen.',
		'desc' => 'Im Marktcheck betrachten wir, ob ein eigener Anfrageweg zu Ihrem Betrieb, Ihrer Marge und Ihrer Bearbeitungskapazität passt.',
		'url' => $marketcheck_url, 'label' => $marketcheck_label,
		'note' => '' !== $marketcheck_reply ? 'Befund ' . $marketcheck_reply . '.' : '',
		'action' => 'cta_results_next_energy',
	],
];

get_header();
?>
<div id="results-content" class="doku hu-erg" data-track-page="results_hub">
	<header class="dokument-kopf blatt" data-track-section="hero">
		<p class="dachzeile">Haşim Üner · Ausgewählte Arbeiten</p>
		<div class="erg-hero">
			<div>
				<h1 id="hu-erg-title">Was ich gebaut habe.<br><span class="stempelfarbe">Was Sie prüfen können.</span></h1>
				<p class="aufriss">WordPress-Projekte, eine dokumentierte Anfragestrecke und Einblicke in die technische Umsetzung. Mit meinem jeweiligen Anteil und den Belegen, die dazu gehören.</p>
			</div>
			<aside class="erg-register" aria-label="Belege auf dieser Seite">
				<p class="mono">Projekt · Umsetzung · Nachweis</p>
				<dl>
					<div><dt>WordPress</dt><dd>Öffentliche Arbeiten und eine technische Projektgeschichte</dd></div>
					<div><dt>Solar</dt><dd><a href="#grossprojekt" data-track-action="results_hero_to_case" data-track-category="navigation" data-track-section="hero">Ein dokumentierter Fall mit eingeordneten Kennzahlen</a></dd></div>
					<div><dt>White-Label</dt><dd>Ein Muster für Umfang, Prüfung und Übergabe</dd></div>
				</dl>
			</aside>
		</div>
		<div class="erg-hero-actions">
			<a class="tun still" href="#arbeiten" data-track-action="results_hero_to_wordpress" data-track-category="navigation" data-track-section="hero">WordPress-Arbeiten ansehen <span aria-hidden="true">↓</span></a>
			<a class="textlink" href="#technik" data-track-action="results_hero_to_proof" data-track-category="navigation" data-track-section="hero">Umsetzung im Detail <span aria-hidden="true">↓</span></a>
		</div>
	</header>

	<section id="arbeiten" aria-labelledby="arbeiten-h" data-track-section="arbeiten">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">01</span><span class="titel">WordPress-Arbeiten</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<h2 class="kopf" id="arbeiten-h">Unterschiedliche Aufgaben. Konkrete Umsetzung.</h2>
				<p class="vorspann">Die folgenden Websites zeigen meine Arbeit an Struktur, Gestaltung und Entwicklung. Das eigene redaktionelle Projekt ist als solches benannt.</p>
				<div class="erg-projects">
					<?php foreach ( $references as $reference ) :
						$inspection = $inspection_notes[ $reference['name'] ] ?? null;
						?>
						<article class="erg-project">
							<div class="erg-project-heading">
								<p class="mono"><?php echo esc_html( $reference['role'] ); ?></p>
								<h3><?php echo esc_html( $reference['name'] ); ?></h3>
								<p class="erg-note"><?php echo esc_html( $reference['discipline'] ); ?></p>
								<a class="textlink" href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="results_reference_open" data-track-category="trust" data-track-section="arbeiten">Website ansehen<span class="nur-vorlesen">: <?php echo esc_html( $reference['name'] ); ?> (neuer Tab)</span> <span aria-hidden="true">↗</span></a>
							</div>
							<div class="erg-project-body">
								<?php if ( $inspection ) : ?><p><?php echo esc_html( $inspection['task'] ); ?></p><?php endif; ?>
								<dl class="erg-contribution"><div><dt>Mein Beitrag</dt><dd><?php echo esc_html( $reference['text'] ); ?></dd></div></dl>
								<?php if ( $inspection ) : ?>
									<details class="erg-details"><summary>Worauf Sie beim Ansehen achten können</summary><div class="erg-answer"><p><?php echo esc_html( $inspection['check'] ); ?></p></div></details>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
				<p class="erg-note erg-after">Die Links zeigen den jeweiligen aktuellen Stand. Diese Arbeitsbelege dokumentieren meinen Umsetzungsumfang; für diese Projekte veröffentliche ich hier keine Vorher-Nachher-Zahlen zu Traffic oder Anfragen.</p>
			</div>
		</div>
	</section>

	<section id="technik" aria-labelledby="technik-h" data-track-section="technik">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">02</span><span class="titel">Umsetzung im Detail</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<p class="mono erg-kicker">Technische Projektgeschichte · eigenes Projekt</p>
				<h2 class="kopf" id="technik-h">Eine Website umbauen, ohne ihre Verbindungen zu verlieren.</h2>
				<p class="vorspann">Auf hasimuener.de standen Startseite und Freelancer-Angebot auf zwei getrennten Seiten. Bei der Zusammenführung mussten auch Suchmaschinen, bestehende Verweise und Anfragewege berücksichtigt werden.</p>
				<div class="tafel erg-migration">
					<p class="mono">Umgesetzte Änderung</p>
					<div class="erg-migration-path"><div><span>Früherer Einstieg</span><code>/wordpress-freelancer-hannover/</code></div><span class="erg-arrow" aria-hidden="true">→</span><div><span>Gemeinsames Ziel</span><strong>Die Startseite</strong></div></div>
					<p class="erg-note">Mein Anteil: Angebotsstruktur, WordPress-Templates, Weiterleitungsregeln, interne Verlinkung und automatisierte Prüfungen.</p>
				</div>
				<div class="erg-evidence-list">
					<?php foreach ( $implementation_evidence as $evidence ) : ?>
						<article class="erg-evidence">
							<h3><?php echo esc_html( $evidence['title'] ); ?></h3>
							<div><p><?php echo esc_html( $evidence['text'] ); ?></p><a class="textlink" href="<?php echo esc_url( $source_base . $evidence['path'] ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="<?php echo esc_attr( $evidence['action'] ); ?>" data-track-category="proof" data-track-section="technik"><?php echo esc_html( $evidence['label'] ); ?><span class="nur-vorlesen"> (GitHub, neuer Tab)</span> <span aria-hidden="true">↗</span></a></div>
						</article>
					<?php endforeach; ?>
				</div>
				<p class="erg-note erg-after">Die Quellen zeigen die umgesetzte Version. Sie belegen technische Entscheidungen und Prüfregeln; eine Verbesserung von Rankings oder Anfragen lässt sich daraus allein nicht ableiten.</p>
				<details class="erg-details erg-after">
					<summary>Quellcode, Prüfprozess und aktuelle Performance ansehen</summary>
					<div class="erg-answer">
						<p>Sie können Änderungen und automatisierte Prüfungen im öffentlichen Repository nachvollziehen. PageSpeed Insights startet eine neue Messung; Ergebnisse hängen unter anderem von Gerät und Testbedingungen ab.</p>
						<ul class="erg-source-links">
							<li><a href="<?php echo esc_url( $github_url . '/commits/main/' ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="results_proof_github_history" data-track-category="proof" data-track-section="technik">Änderungsverlauf auf GitHub<span class="nur-vorlesen"> (neuer Tab)</span> ↗</a></li>
							<li><a href="<?php echo esc_url( $source_base . '.github/workflows/ci.yml' ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="results_proof_github_ci" data-track-category="proof" data-track-section="technik">Automatisierte Prüfregeln<span class="nur-vorlesen"> (GitHub, neuer Tab)</span> ↗</a></li>
							<li><a href="<?php echo esc_url( $pagespeed_url ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="results_proof_pagespeed" data-track-category="proof" data-track-section="technik">Diese Seite bei PageSpeed Insights prüfen<span class="nur-vorlesen"> (neuer Tab)</span> ↗</a></li>
						</ul>
						<p class="erg-note">Lighthouse liefert Labormesswerte. Eine hohe Punktzahl belegt weder vollständige Barrierefreiheit noch reale Geschäftsergebnisse.</p>
					</div>
				</details>
			</div>
		</div>
	</section>

	<section id="grossprojekt" aria-labelledby="grossprojekt-h" data-track-section="grossprojekt">
		<div class="blatt reihe">
				<div class="spalte-links"><div class="kapitel"><span class="nr">03</span><span class="titel">Solar-Fall</span><span class="strich" aria-hidden="true"></span></div></div>
				<div class="voll">
					<p class="mono erg-kicker"><?php echo esc_html( $e3_case_label ); ?> · anonymisiert</p>
					<h2 class="kopf" id="grossprojekt-h">Von der Website bis zur Übergabe an den Vertrieb.</h2>
					<p class="vorspann">In diesem Projekt habe ich Website, Landingpages, Vorqualifizierung, Tracking und CRM-Übergabe mit Kampagnen und laufender Optimierung verbunden. Der Betrachtungszeitraum umfasst <?php echo esc_html( $metric( 'timeframe' ) ); ?>.</p>
					<div class="tafel erg-solar">
						<p class="mono">Kosten pro Anfrage im dokumentierten Fall</p>
						<dl class="erg-cpl"><div><dt>Gekaufte Anfrage · vorher</dt><dd><?php echo esc_html( $metric( 'cpl_before' ) ); ?></dd></div><div><dt>Eigene Anfrage · nachher</dt><dd><?php echo esc_html( $metric( 'cpl_after' ) ); ?></dd></div></dl>
						<dl class="erg-solar-context"><div><dt>Qualifizierte Anfragen</dt><dd><?php echo esc_html( $metric( 'lead_count' ) ); ?></dd></div><div><dt>Abschlussquote</dt><dd><?php echo esc_html( $metric( 'sales_conversion' ) ); ?></dd></div><div><dt>Zeitraum</dt><dd><?php echo esc_html( $metric( 'timeframe' ) ); ?></dd></div></dl>
						<p class="erg-note">Die CPL-Werte vergleichen Anfrage-Einkauf mit eigener Gewinnung. Sie sind kein vollständiger Vergleich der Kosten pro Auftrag: Aufbau, Betreuung, Software und Vertrieb müssen dafür mitgerechnet werden.</p>
					</div>
					<div class="erg-case-context">
						<div><h3>Was sich im Prozess verändert hat</h3><p>Anfragen liefen durch eine eigene Strecke: Landingpage, qualifizierende Fragen und Übergabe ins CRM. So wurden Website und Kampagnen mit der anschließenden Bearbeitung verbunden.</p></div>
						<div><h3>Wie die Ergebnisse einzuordnen sind</h3><p>Die Werte stammen aus einem einzelnen PV-Projekt. Kampagnen, Angebot und Vertrieb wirkten gemeinsam. Sie isolieren keinen WordPress-Effekt und sind keine Prognose für andere Betriebe oder Wärmepumpenprojekte.</p></div>
					</div>
					<details class="erg-details erg-after"><summary>Was Anfrage- und Abschlussquote jeweils bedeuten</summary><div class="erg-answer"><dl class="erg-definitions"><div><dt><?php echo esc_html( $metric( 'lead_conversion' ) ); ?> Lead-Conversion-Rate</dt><dd>Anteil der Besucher der Anfragestrecke, die eine Anfrage abschickten.</dd></div><div><dt><?php echo esc_html( $metric( 'sales_conversion' ) ); ?> Abschlussquote</dt><dd>Anteil der qualifizierten Anfragen, aus denen ein Auftrag wurde. Daran hatte der Vertrieb einen wesentlichen Anteil.</dd></div></dl></div></details>
					<a class="textlink erg-after" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="cta_results_case_study" data-track-category="trust" data-track-section="grossprojekt">Fallstudie und Methodik lesen <span aria-hidden="true">→</span></a>
				</div>
		</div>
	</section>

	<section id="whitelabel" aria-labelledby="whitelabel-h" data-track-section="whitelabel">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">04</span><span class="titel">Zusammenarbeit</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<h2 class="kopf" id="whitelabel-h">Eine Übergabe, mit der Ihr Team weiterarbeiten kann.</h2>
				<p class="vorspann">Bei White-Label-Projekten bleiben Kundenbeziehung und Außenauftritt bei der Agentur. Vor der Umsetzung vereinbaren wir, was geliefert wird und woran Sie die Abnahme festmachen.</p>
				<div class="erg-handover">
					<p class="mono">Übergabemuster · Beispiel Seitenzusammenführung</p>
					<p class="erg-note">Anhand der oben dokumentierten Arbeit an meiner eigenen Website. Das ist ein Muster für die Zusammenarbeit, keine veröffentlichte Agenturreferenz.</p>
					<ol class="erg-handover-list">
						<li><h3>Umfang festhalten</h3><p>Welche Inhalte ziehen um? Welche Adressen und Anfragewege müssen erhalten bleiben? Diese Entscheidungen stehen vor der Umsetzung fest.</p></li>
						<li><h3>Prüfung nachvollziehbar machen</h3><p>Änderungen, Prüffälle und offene Punkte gehören zusammen. Eine bestandene Codeprüfung ersetzt dabei keine Funktionsprüfung der veröffentlichten Website.</p></li>
						<li><h3>Weiterarbeit ermöglichen</h3><p>Quellcode, Änderungsstand und die Begründung wichtiger Entscheidungen übergeben. Damit kann Ihr Team die Umsetzung prüfen und anschließend weiterführen.</p></li>
					</ol>
				</div>
				<div class="erg-related">
					<a class="textlink" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="cta_results_whitelabel" data-track-category="segmentation" data-track-section="whitelabel">So läuft die White-Label-Zusammenarbeit <span aria-hidden="true">→</span></a>
					<a class="textlink" href="<?php echo esc_url( $tracking_url ); ?>" data-track-action="results_proof_tracking_page" data-track-category="proof" data-track-section="whitelabel">Leistungsumfang und Abnahme beim Tracking <span aria-hidden="true">→</span></a>
				</div>
			</div>
		</div>
	</section>

	<section id="weiter" aria-labelledby="weiter-h" data-track-section="weiter">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">05</span><span class="titel">Nächster Schritt</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<h2 class="kopf" id="weiter-h">Was möchten Sie umsetzen?</h2>
				<p class="vorspann">Wählen Sie den Einstieg, der zu Ihrem Vorhaben passt.</p>
				<div class="erg-next">
					<?php foreach ( $next_steps as $step ) : ?>
						<article class="erg-next-row erg-next-row--<?php echo esc_attr( $step['kind'] ); ?>">
							<div><p class="mono"><?php echo esc_html( $step['kicker'] ); ?></p><h3><?php echo esc_html( $step['title'] ); ?></h3><p><?php echo esc_html( $step['desc'] ); ?></p></div>
							<div class="erg-next-action"><a class="tun" href="<?php echo esc_url( $step['url'] ); ?>" data-track-action="<?php echo esc_attr( $step['action'] ); ?>" data-track-category="lead_gen" data-track-section="weiter"><?php echo esc_html( $step['label'] ); ?> <span aria-hidden="true">→</span></a><?php if ( '' !== $step['note'] ) : ?><p class="erg-note"><?php echo esc_html( $step['note'] ); ?></p><?php endif; ?></div>
						</article>
					<?php endforeach; ?>
				</div>
				<p class="erg-note erg-after">Leistungen und Preisrahmen finden Sie auf der <a href="<?php echo esc_url( $freelancer_url ); ?>" data-track-action="results_next_to_freelancer" data-track-category="navigation" data-track-section="weiter">Startseite</a>. Für Energieunternehmen erklärt die <a href="<?php echo esc_url( $energy_url ); ?>" data-track-action="results_next_to_energy" data-track-category="navigation" data-track-section="weiter">Branchenseite</a> den Anfrageweg genauer.</p>
			</div>
		</div>
	</section>
</div>
<?php get_footer(); ?>
