<?php
/**
 * Canonical list of publicly verifiable reference projects.
 *
 * Zwei Oberflaechen zeigen dieselben Referenzen: die Freelancer-Money-Page und
 * der Ergebnisse-Hub. Bis 2026-09 stand die Liste als Literal in
 * page-wordpress-freelancer-hannover.php — eine zweite Oberflaeche haette den
 * Bestand sofort auseinanderlaufen lassen.
 *
 * Harte Regel aus docs/standards/BRAND_AND_COPY.md: oeffentliche Referenzen
 * muessen direkt pruefbar sein. Hier steht deshalb nur, was ein Besucher unter
 * der genannten Adresse selbst nachsehen kann — keine Zahlen, keine
 * Wirkungsbehauptung, keine nicht freigegebene Kundenarbeit.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the canonical publicly verifiable reference projects.
 *
 * Feldbedeutung:
 * - `name`       Domain, wie sie sichtbar verlinkt wird.
 * - `url`        Direkt pruefbare Adresse.
 * - `tag`        Schwerpunkt als Kurzlabel (Freelancer-Route).
 * - `discipline` Schwerpunkt als Ueberschrift (Ergebnisse-Hub).
 * - `role`       Was das Projekt inhaltlich ist.
 * - `text`       Ein Satz zur tatsaechlich geleisteten Arbeit.
 * - `stack`      Geleistete Gewerke als Chips.
 *
 * @return array<int, array<string, mixed>>
 */
function hu_public_reference_projects() {
	return [
		[
			'name'       => 'civaka-azad.org',
			'url'        => 'https://civaka-azad.org/',
			'tag'        => 'Informationsarchitektur',
			'discipline' => 'Informationsarchitektur',
			'role'       => 'Redaktioneller Bestand',
			'text'       => 'Redaktioneller Bestand mit vielen Inhalten: Navigation und Archive so strukturiert, dass Themen auffindbar bleiben.',
			'stack'      => [ 'WordPress', 'Informationsarchitektur', 'Content-System' ],
		],
		[
			'name'       => 'hasimuener.org',
			'url'        => 'https://hasimuener.org/',
			'tag'        => 'Eigenes Projekt · Editorial Design',
			'discipline' => 'Editorial Design',
			'role'       => 'Eigenes redaktionelles Projekt',
			'text'       => 'Eigenes redaktionelles Projekt: Typografie, Raster und Lesefluss als tragende Gestaltung statt dekorativer Effekte.',
			'stack'      => [ 'WordPress', 'Frontend', 'Performance' ],
		],
		[
			'name'       => 'kurdischer-rat.org',
			'url'        => 'https://kurdischer-rat.org/',
			'tag'        => 'Organisation · Workflow',
			'discipline' => 'Organisationsplattform',
			'role'       => 'Organisationswebsite',
			'text'       => 'Organisationswebsite mit klarer Informationshierarchie und einem versionierten Prozess für kontrollierte Veröffentlichungen.',
			'stack'      => [ 'WordPress', 'Git', 'Workflow' ],
		],
	];
}

/**
 * Project portraits approved for the website product page.
 *
 * The named E3 reference describes the work only. It must not contain metrics
 * or link to the separate anonymous case. Other reference surfaces retain
 * their existing public project selection.
 *
 * @return array<int, array<string, mixed>>
 */
function hu_website_reference_projects() {
	$public = array_column( hu_public_reference_projects(), null, 'name' );
	$portraits = [
		'hasimuener.org' => [
			'title' => 'Ein Journal, das zum Lesen einlädt.',
			'text' => 'Mein eigenes redaktionelles Projekt. Typografie, Seitenraster und Lesefluss geben Essays und längeren Texten einen ruhigen Rahmen.',
			'screenshot' => 'hasimuener-org-anfrage-website.webp',
			'alt' => 'Startseite von hasimuener.org mit dem Schriftzug Macht. Medien. Perspektive. und einem redaktionellen Titelbild',
		],
		'civaka-azad.org' => [
			'title' => 'Viele Inhalte. Klare Wege.',
			'text' => 'Ein redaktioneller Bestand braucht Orientierung. Ich habe Navigation und Archive so strukturiert, dass Leser zu den passenden Themen finden.',
			'screenshot' => 'reference-civaka-azad.webp',
			'alt' => 'Website-Ansicht von Civaka Azad mit Navigation und redaktionellen Inhalten',
		],
	];
	$references = [];
	foreach ( $portraits as $name => $portrait ) {
		$references[] = array_merge( $public[ $name ], $portrait );
	}
	$references[] = [
		'name' => 'hasimuener.de',
		'url' => home_url( '/' ),
		'tag' => 'Eigenes Projekt · Website & Anfragen',
		'role' => 'Eigene Unternehmenswebsite',
		'title' => 'Angebot, Anfrage und Übergabe.',
		'text' => 'Diese Website zeigt meine Arbeit im Einsatz: eigenständiges Design, klare Angebote und eine geprüfte Formularstrecke. Vom ersten Überblick bis zur Projektanfrage greift alles ineinander.',
		'stack' => [ 'WordPress', 'Anfrageformulare', 'Qualitätsprüfung' ],
		'screenshot' => 'reference-hasimuener-de.webp',
		'alt' => 'Startseite von hasimuener.de mit dem Website-Angebot und der Darstellung der Anfragestrecke',
	];
	$references[] = [
		'name' => 'E3 New Energy',
		'url' => 'https://e3-newenergy.de/',
		'tag' => 'B2B-Projekt · Energie',
		'role' => 'Funnel-Architektur',
		'title' => 'Vom ersten Klick bis ins CRM.',
		'text' => 'Für E3 New Energy habe ich die gesamte Funnel-Architektur aufgebaut: Website und Landingpages, Kampagnen, Anfrageformulare, Tracking und die Übergabe ins CRM.',
		'stack' => [ 'Website & Landingpages', 'Tracking', 'CRM-Anbindung' ],
		'screenshot' => 'reference-e3-new-energy.webp',
		'alt' => 'Website-Ansicht von E3 New Energy mit Navigation und dem Angebot für erneuerbare Energien',
		'flow' => [ 'Klick', 'Landingpage', 'Formular', 'Tracking', 'CRM' ],
	];
	return $references;
}
