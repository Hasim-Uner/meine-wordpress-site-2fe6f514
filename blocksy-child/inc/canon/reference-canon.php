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
