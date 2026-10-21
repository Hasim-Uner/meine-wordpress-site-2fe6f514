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
 * References for the white-label page: E3 first, then the public projects.
 *
 * E3 steht hier namentlich und ohne Zahlen. Keine Kennzahlen, kein Zeitraum,
 * kein Link auf den anonymen Case — die Verbindung von Name und Zahlen bleibt
 * ausgeschlossen (Entscheidung 10.09.2026, bestaetigt 19.10.2026).
 *
 * @return array<int, array<string, mixed>>
 */
function hu_whitelabel_reference_projects() {
	return array_merge(
		[
			[
				'name'       => 'e3-newenergy.de',
				'url'        => 'https://e3-newenergy.de/',
				'tag'        => 'Anfragestrecke · Solar & Wärmepumpe',
				'discipline' => 'Anfragestrecke',
				'role'       => 'Funnel-Architektur',
				'text'       => 'Die komplette Anfragestrecke für Endkunden gebaut: Landingpages in WordPress, mehrstufige Formularstrecken, Server-Side-Tracking und die Übergabe jeder Anfrage ins CRM.',
				'stack'      => [ 'Landingpages', 'Server-Side-Tracking', 'CRM-Anbindung' ],
			],
		],
		hu_public_reference_projects()
	);
}

/**
 * Project portraits approved for the website product page.
 *
 * The named E3 reference describes the work only. It must not contain metrics
 * or link to the separate anonymous case. The reference overview reuses
 * these approved work descriptions, with its own compact case selection.
 *
 * @return array<int, array<string, mixed>>
 */
function hu_website_reference_projects() {
	$public = array_column( hu_public_reference_projects(), null, 'name' );
	$references = [
		[
			'name' => 'E3 New Energy',
			'url' => 'https://e3-newenergy.de/',
			'tag' => 'B2B-Projekt · Energie',
			'role' => 'Funnel-Architektur',
			'title' => 'Vom ersten Klick bis ins CRM.',
			'text' => 'Für E3 New Energy habe ich die bestehende Website technisch und conversionseitig weiterentwickelt und die Funnel-Architektur ausgebaut: Landingpages, technisches SEO, Tracking, Anfrageformulare und die Übergabe ins CRM.',
			'stack' => [ 'Landingpages', 'Tracking & technisches SEO', 'CRM-Anbindung' ],
			'screenshot' => 'reference-e3-new-energy.webp',
			'alt' => 'Website-Ansicht von E3 New Energy mit Navigation und dem Angebot für erneuerbare Energien',
			'flow' => [ 'Klick', 'Landingpage', 'Formular', 'Tracking', 'CRM' ],
		],
	];
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
	foreach ( $portraits as $name => $portrait ) {
		$references[] = array_merge( $public[ $name ], $portrait );
	}
	return $references;
}

/**
 * Compact project cases for /referenzen/.
 *
 * Project identity, screenshot and work description stay in the existing
 * portrait registry. These fields explain context and scope, not measured
 * business outcomes or sole authorship of an existing customer website.
 *
 * @return array<int, array<string, mixed>>
 */
function hu_reference_case_projects() {
	$portraits = array_column( hu_website_reference_projects(), null, 'name' );
	$cases = [
		'E3 New Energy' => [
			'id'      => 'e3-new-energy',
			'context' => 'Eine bestehende Unternehmenswebsite mit Angeboten für erneuerbare Energien. Mein Beitrag lag in der Weiterentwicklung und im Ausbau des Anfragewegs.',
			'work'    => [ 'Landingpages und Anfrageformulare', 'Technisches SEO und Tracking', 'Übergabe der Anfragen ins CRM' ],
			'limit'   => 'Die Website bestand bereits. Gezeigt wird mein Beitrag zur Weiterentwicklung, kein vollständiger Website-Neubau.',
		],
		'civaka-azad.org' => [
			'id'      => 'civaka-azad',
			'context' => 'Ein redaktioneller WordPress-Bestand mit vielen Inhalten. Die Aufgabe war, den Zugang zu Themen über Navigation und Archive zu strukturieren.',
			'work'    => [ 'Informationsarchitektur des redaktionellen Bestands', 'Struktur von Navigation und Archiven', 'WordPress als Content-System' ],
			'limit'   => 'Die öffentliche Website zeigt den heutigen Stand. Die Beschreibung benennt meinen eigenen Beitrag zur Struktur.',
		],
	];
	$projects = [];
	foreach ( $cases as $name => $case ) {
		if ( isset( $portraits[ $name ] ) ) {
			$projects[] = array_merge( $portraits[ $name ], $case );
		}
	}
	return $projects;
}
