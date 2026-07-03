<?php
/**
 * Homepage-Themen-Hub — Deeper-Cluster-Konfiguration.
 *
 * Quelle für die "Vertiefung"-Sektion auf front-page.php. Wird per
 * include-return ohne Funktionswrapper geladen, um Kontext-Token in
 * der Template-Datei zu sparen.
 *
 * Hinweis zur Pfadwahl: `wgos-cluster-pages.php` ist bereits durch die
 * SEO-Routen-Registry belegt (nexus_get_wgos_cluster_page_data). Daher
 * liegt diese Konfiguration unter `home-deeper-clusters.php` im selben
 * Verzeichnis.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	[
		'group' => 'Strategie & Vergleich',
		'items' => [
			[ 't' => 'Photovoltaik Leads kaufen? CPL-Rechnung pro Anfrage',  's' => 'Markteinordnung der Lead-Anbieter und Kosten pro Anfrage.',                     'url' => home_url( '/solar-leads-kaufen-alternative/' ) ],
			[ 't' => 'TCO 24/36 Monate: Portal vs. eigenes System',   's' => 'Strategischer 8-Kriterien-Vergleich mit Asset-Eigentum-Logik.',                 'url' => home_url( '/eigene-leadgenerierung-vs-portale/' ) ],
		],
	],
	[
		'group' => 'Lead-Qualität & CPL',
		'items' => [
			[ 't' => 'Was kosten Solar-Leads? (Marktstudie)', 's' => 'DACH-Preise je Modell und warum Cost-per-Order statt Cost-per-Lead zählt.', 'url' => home_url( '/solar-leads-kosten-studie/' ) ],
			[ 't' => 'Cost per Lead Photovoltaik',           's' => 'Drei Szenarien im CPL-Vergleich und versteckte Kostentreiber.',         'url' => home_url( '/cost-per-lead-photovoltaik/' ) ],
			[ 't' => 'Qualifizierte PV-Anfragen',            's' => 'Vier Merkmale für hochwertige Solar-Anfragen plus Warnsignale.',       'url' => home_url( '/qualifizierte-pv-anfragen/' ) ],
		],
	],
	[
		'group' => 'Funnel & Tracking',
		'items' => [
			[ 't' => 'Lead-Funnel Solar',                    's' => 'Fünf Stufen einer belastbaren Solar-Funnel-Architektur.',              'url' => home_url( '/lead-funnel-solar/' ) ],
			[ 't' => 'Server-Side Tracking für B2B',         's' => 'GA4, Meta CAPI und Consent Mode v2 auf eigenem Server.',               'url' => home_url( '/server-side-tracking-b2b/' ) ],
		],
	],
	[
		'group' => 'Zielgruppen & Marktbild',
		'items' => [
			[ 't' => 'Wärmepumpen Leads kaufen? Die Alternative',  's' => 'Marktmodelle, Vorqualifizierung und CPL-Vergleich für den Heizungstausch.', 'url' => home_url( '/waermepumpen-leads/' ) ],
			[ 't' => 'B2B Solar Leads (Gewerbe)',            's' => 'Buying-Center-Funnel für gewerbliche Photovoltaik-Projekte.',          'url' => home_url( '/b2b-solar-leads/' ) ],
			[ 't' => 'Kunden gewinnen für Solarteure',       's' => 'Mythen-Aufklärung und fünf systematische Hebel im DACH-Mittelstand.',  'url' => home_url( '/kunden-gewinnen-solarteure/' ) ],
		],
	],
];
