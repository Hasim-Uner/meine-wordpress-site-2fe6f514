<?php
/** Shared data for the results hub template parts. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$e3_canon      = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url   = $e3_canon['url'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$e3_case_label = $e3_canon['case_label'] ?? 'mittelständischer PV-Installationsbetrieb';
$metric        = static function ( $key, $field = 'display', $fallback = '' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $key, $field, $fallback ) : $fallback;
};

$routes         = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$energy_url     = $routes['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' );
$whitelabel_url = $routes['whitelabel'] ?? home_url( '/whitelabel-retainer/' );
$freelancer_url = $routes['freelancer'] ?? home_url( '/' );
$project_url    = function_exists( 'hu_get_navigation_project_request_url' ) ? hu_get_navigation_project_request_url() : home_url( '/kontakt/' );
$tracking_project_url = function_exists( 'hu_get_contact_intake_url' )
	? hu_get_contact_intake_url( 'project', 'tracking' )
	: add_query_arg(
		[
			'type'  => 'project',
			'focus' => 'tracking',
		],
		$routes['contact'] ?? home_url( '/kontakt/' )
	);
$references = function_exists( 'hu_public_reference_projects' ) ? hu_public_reference_projects() : [];

$marketcheck_url   = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$marketcheck_label = function_exists( 'nexus_get_primary_request_cta_label' ) ? nexus_get_primary_request_cta_label() : 'Marktcheck starten';
$marketcheck_reply = function_exists( 'hu_marketcheck_reply_label' ) ? hu_marketcheck_reply_label() : '';
$response_promise  = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'sentence' ) : '';
$whitelabel_task_url = add_query_arg( [ 'type' => 'whitelabel', 'case' => 'aufgabe' ], $whitelabel_url ) . '#aufgabe';

$github_url    = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514';
$pagespeed_url = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/ergebnisse/' ) );
$technical_proofs = [
	[ 'title' => 'Versionshistorie statt Black Box.', 'text' => 'Im öffentlichen Repository lässt sich nachvollziehen, wie Änderungen versioniert und schrittweise weiterentwickelt werden.', 'label' => 'Änderungsverlauf auf GitHub', 'url' => $github_url . '/commits/main/', 'action' => 'results_proof_github_history' ],
	[ 'title' => 'Qualitätsprüfungen laufen automatisiert.', 'text' => 'Der CI-Prozess prüft PHP-Syntax, statische Analyse, Asset-Referenzen, Canon-Regeln sowie CSS- und Copy-Guards vor dem Merge.', 'label' => 'Prüfregeln im Repository', 'url' => $github_url . '/blob/main/.github/workflows/ci.yml', 'action' => 'results_proof_github_ci' ],
	[ 'title' => 'Performance lässt sich neu messen.', 'text' => 'PageSpeed Insights startet eine neue Labormessung dieser Seite. Das Ergebnis ist kein statischer Marketing-Screenshot.', 'label' => 'Diese Seite bei PageSpeed prüfen', 'url' => $pagespeed_url, 'action' => 'results_proof_pagespeed' ],
];

/*
 * Der Proof-Hub bleibt für alle drei Geschäftspfade offen. Standardmäßig steht
 * das direkte Projekt zuerst. Ein kleines, rein lokales Routing-Skript darf die
 * Reihenfolge anhand des unmittelbaren Einstiegs ändern; es speichert nichts,
 * setzt keine Cookies und sendet keine Analysedaten. Die Tracking-Variante des
 * Projektwegs setzt lediglich den bereits vorhandenen Kontakt-Fokus korrekt.
 */
$next_steps = [
	[
		'kind'    => 'project',
		'primary' => true,
		'kicker'  => 'Direktes Projekt',
		'title'   => 'Projekt kurz prüfen und sauber eingrenzen.',
		'desc'    => 'Schicken Sie URL, Ausgangslage und Ziel. Ich prüfe persönlich, ob ich helfen kann und welcher Umfang sinnvoll ist.',
		'url'     => $project_url,
		'label'   => 'Projekt prüfen lassen',
		'note'    => $response_promise,
		'action'  => 'cta_results_next_project',
		'variants' => [
			'tracking' => [
				'kicker' => 'Tracking & Attribution',
				'title'  => 'Tracking-Projekt am bestehenden Setup einordnen.',
				'desc'   => 'Schicken Sie Setup, Engpass und Ziel. Die Anfrage landet mit dem Fokus Tracking direkt in der passenden Projektprüfung.',
				'url'    => $tracking_project_url,
				'label'  => 'Tracking-Projekt prüfen lassen',
				'note'   => $response_promise,
			],
		],
	],
	[
		'kind'    => 'agency',
		'primary' => false,
		'kicker'  => 'Für Ihre Agentur',
		'title'   => 'Ein Kundenprojekt zur Umsetzung übergeben.',
		'desc'    => 'Ein Briefing, Entwurf oder technisches Problem reicht für den Einstieg. Umfang und Abnahme klären wir vor dem Erstprojekt.',
		'url'     => $whitelabel_task_url,
		'label'   => 'Aufgabe beschreiben',
		'note'    => 'Laufende Kapazität ist nach dem Erstprojekt optional.',
		'action'  => 'cta_results_next_agency',
	],
	[
		'kind'    => 'energy',
		'primary' => false,
		'kicker'  => 'Solar · Wärmepumpe · Speicher',
		'title'   => 'Einen eigenen Anfragekanal prüfen.',
		'desc'    => 'Im Marktcheck betrachten wir, ob ein eigener Anfrageweg zu Betrieb, Marge und Bearbeitungskapazität passt.',
		'url'     => $marketcheck_url,
		'label'   => $marketcheck_label,
		'note'    => '' !== $marketcheck_reply ? 'Befund ' . $marketcheck_reply . '.' : '',
		'action'  => 'cta_results_next_energy',
	],
];
