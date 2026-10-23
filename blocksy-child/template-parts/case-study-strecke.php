<?php
/**
 * Anfragestrecke der Fallstudie als Schema (quer ab 900 px, sonst hoch).
 *
 * Reines Markup, keine Daten: Farben kommen aus e3-case-v2.css (Tokens aus
 * system.css). case-study-motion.js zeichnet die Rueckkopplung einmal beim
 * ersten Sichtkontakt; ohne JS und bei reduzierter Bewegung steht das Schema
 * sofort im Endzustand.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<svg class="fs-svg fs-svg--quer" viewBox="0 0 1000 300" role="img" aria-labelledby="fs-sq-t">
	<title id="fs-sq-t">Anfragestrecke: Nachfrage, Landingpage, Formular, CRM, Vertrieb. Vom CRM gehen qualifizierte Anfragen und Abschlüsse als Signal zurück an die Werbekonten.</title>
	<defs><marker id="fs-m1" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path class="fs-pfeil" d="M0 0L10 5L0 10z"/></marker></defs>
	<g class="fs-stn"><rect x="10" y="60" width="160" height="76" rx="3"/><text class="fs-nr" x="24" y="82">01 NACHFRAGE</text><text x="24" y="106">Google · Meta</text><text class="fs-sub" x="24" y="124">und organische Suche</text></g>
	<g class="fs-stn"><rect x="212" y="60" width="160" height="76" rx="3"/><text class="fs-nr" x="226" y="82">02 EINSTIEG</text><text x="226" y="106">Landingpage</text><text class="fs-sub" x="226" y="124">je Produkt und Bedarf</text></g>
	<g class="fs-stn"><rect x="414" y="60" width="160" height="76" rx="3"/><text class="fs-nr" x="428" y="82">03 FILTER</text><text x="428" y="106">Formular</text><text class="fs-sub" x="428" y="124">fragt vor dem Anruf ab</text></g>
	<g class="fs-stn"><rect x="616" y="60" width="160" height="76" rx="3"/><text class="fs-nr" x="630" y="82">04 ÜBERGABE</text><text x="630" y="106">CRM · Bitrix24</text><text class="fs-sub" x="630" y="124">mit Produkt und Herkunft</text></g>
	<g class="fs-stn"><rect x="818" y="60" width="172" height="76" rx="3"/><text class="fs-nr" x="832" y="82">05 VERTRIEB</text><text x="832" y="106">Rückruf, Angebot</text><text class="fs-sub" x="832" y="124">Teil des Betriebs</text></g>
	<path class="fs-fluss" d="M170 98H210" marker-end="url(#fs-m1)"/><path class="fs-fluss" d="M372 98H412" marker-end="url(#fs-m1)"/><path class="fs-fluss" d="M574 98H614" marker-end="url(#fs-m1)"/><path class="fs-fluss" d="M776 98H816" marker-end="url(#fs-m1)"/>
	<path class="fs-rueck" d="M696 136V210Q696 230 676 230H110Q90 230 90 210V142"/>
	<path class="fs-rueck-pfeil" d="M83 150L90 137L97 150z"/>
	<g class="fs-rueck-text"><text class="fs-rueck-nr" x="300" y="254">06 RÜCKMELDUNG</text><text class="fs-rueck-sub" x="300" y="274">Qualifizierte Anfragen und Abschlüsse gehen als Signal zurück an die Werbekonten.</text></g>
</svg>
<svg class="fs-svg fs-svg--hoch" viewBox="0 0 360 560" role="img" aria-labelledby="fs-sh-t">
	<title id="fs-sh-t">Anfragestrecke von oben nach unten mit Rückkopplung vom CRM zur Nachfrage.</title>
	<defs><marker id="fs-m2" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path class="fs-pfeil" d="M0 0L10 5L0 10z"/></marker></defs>
	<g class="fs-stn"><rect x="20" y="10" width="250" height="70" rx="3"/><text class="fs-nr" x="34" y="32">01 NACHFRAGE</text><text x="34" y="56">Google · Meta · Suche</text></g>
	<g class="fs-stn"><rect x="20" y="118" width="250" height="70" rx="3"/><text class="fs-nr" x="34" y="140">02 EINSTIEG</text><text x="34" y="164">Landingpage</text></g>
	<g class="fs-stn"><rect x="20" y="226" width="250" height="70" rx="3"/><text class="fs-nr" x="34" y="248">03 FILTER</text><text x="34" y="272">Formular mit Vorqualifizierung</text></g>
	<g class="fs-stn"><rect x="20" y="334" width="250" height="70" rx="3"/><text class="fs-nr" x="34" y="356">04 ÜBERGABE</text><text x="34" y="380">CRM · Bitrix24</text></g>
	<g class="fs-stn"><rect x="20" y="442" width="250" height="70" rx="3"/><text class="fs-nr" x="34" y="464">05 VERTRIEB</text><text x="34" y="488">Rückruf und Angebot</text></g>
	<path class="fs-fluss" d="M145 80V116" marker-end="url(#fs-m2)"/><path class="fs-fluss" d="M145 188V224" marker-end="url(#fs-m2)"/><path class="fs-fluss" d="M145 296V332" marker-end="url(#fs-m2)"/><path class="fs-fluss" d="M145 404V440" marker-end="url(#fs-m2)"/>
	<path class="fs-rueck" d="M270 369H318Q338 369 338 349V65Q338 45 318 45H284"/>
	<path class="fs-rueck-pfeil" d="M286 38L272 45L286 52z"/>
	<g class="fs-rueck-text"><text class="fs-rueck-nr" x="20" y="540">06 RÜCKMELDUNG ans Werbekonto</text></g>
</svg>
