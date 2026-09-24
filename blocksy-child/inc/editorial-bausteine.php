<?php
/**
 * Editorial building blocks for article content.
 *
 * Wiederkehrende Elemente eines Fachartikels als Shortcodes: Kurzantwort,
 * Randnotiz, Abbildung, Fall, Pruefliste, Abschluss-Tafel und Quellen. Dazu
 * CSS-Klassen fuer reines HTML im Inhalt (Leitsatz, Aufgaben, Tabellen …).
 * Referenz mit Beispielen: agents/skills/pillar-cornerstone-writer/references/bausteine.md
 *
 * Die Bausteine sind additiv. CSS und JS laden nur auf Beitraegen, die einen
 * Baustein enthalten; alle anderen Beitraege rendern unveraendert.
 *
 * Zahlen stehen nie im Inhalt oder in einem Partial, sondern kommen aus dem
 * Kanon (inc/canon/). Einzige Ausnahme sind schematische Abbildungen, deren
 * Werte eine Rechnung und keine Messung sind (abb-relaunch-besser.php).
 * Notiz, Abbildung, Fall, Abschluss-Tafel und Quellen tragen das Attribut
 * aus hu_faq_schema_skip_attribute(), damit ihr Text nie in einer
 * acceptedAnswer des FAQ-Schemas landet.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode tags provided by this file.
 *
 * @return array<int, string>
 */
function hu_editorial_bausteine_shortcode_tags() : array {
	return [ 'hu_kurz', 'hu_notiz', 'hu_abb', 'hu_fall', 'hu_pruefliste', 'hu_abschluss', 'hu_quellen' ];
}

/**
 * Enclosing shortcodes whose content usually holds block HTML.
 *
 * @return array<int, string>
 */
function hu_editorial_bausteine_enclosing_tags() : array {
	return [ 'hu_kurz', 'hu_notiz', 'hu_fall', 'hu_pruefliste', 'hu_abschluss', 'hu_quellen' ];
}

/**
 * Whether a post uses at least one editorial building block.
 *
 * Shortcodes werden per has_shortcode() erkannt, die reinen HTML-Bausteine
 * an ihrer Klasse.
 *
 * @param int|WP_Post|null $post Post, defaults to the current post.
 * @return bool
 */
function hu_post_uses_editorial_bausteine( $post = null ) : bool {
	$post = get_post( $post );

	if ( ! ( $post instanceof WP_Post ) ) {
		return false;
	}

	$content = (string) $post->post_content;

	if ( '' === $content ) {
		return false;
	}

	foreach ( hu_editorial_bausteine_shortcode_tags() as $tag ) {
		if ( has_shortcode( $content, $tag ) ) {
			return true;
		}
	}

	return 1 === preg_match( '/class="[^"]*\bhu-(?:leitsatz|aufgaben|praxis|verweis|phase|liste|tabelle|gruppe)\b/', $content );
}

/**
 * Whether the current request is a single post that uses the building blocks.
 *
 * @return bool
 */
function hu_is_editorial_bausteine_request() : bool {
	if ( ! is_singular( 'post' ) ) {
		return false;
	}

	$post_id = get_queried_object_id();

	return $post_id > 0 && hu_post_uses_editorial_bausteine( $post_id );
}

/**
 * Load the building-block assets after the shared reader contract.
 *
 * Prioritaet 105: hu_enforce_shared_article_reader_assets() legt das letzte
 * Reader-Stylesheet bei 100 ab, die Bausteine muessen danach stehen.
 *
 * @return void
 */
function hu_enqueue_editorial_bausteine_assets() : void {
	if ( ! hu_is_editorial_bausteine_request() || ! function_exists( 'hu_enqueue_css' ) || ! function_exists( 'hu_enqueue_js' ) ) {
		return;
	}

	$deps = [ 'nexus-system-css' ];

	if ( wp_style_is( 'nexus-single-reader-unified-css', 'enqueued' ) ) {
		$deps[] = 'nexus-single-reader-unified-css';
	}

	hu_enqueue_css( 'hu-editorial-bausteine-css', 'editorial-bausteine.css', $deps );
	hu_enqueue_js( 'hu-editorial-bausteine-js', 'editorial-bausteine.js' );
}
add_action( 'wp_enqueue_scripts', 'hu_enqueue_editorial_bausteine_assets', 105 );

/**
 * Mark posts with building blocks, so the reader can reserve the margin column.
 *
 * @param array<int, string> $classes Body classes.
 * @return array<int, string>
 */
function hu_editorial_bausteine_body_class( $classes ) : array {
	$classes = is_array( $classes ) ? $classes : [];

	if ( hu_is_editorial_bausteine_request() ) {
		$classes[] = 'hu-bausteine';
	}

	return $classes;
}
add_filter( 'body_class', 'hu_editorial_bausteine_body_class' );

/**
 * Remove the paragraphs wpautop wraps around lone building-block tags.
 *
 * Steht `[hu_kurz]` oder `[/hu_kurz]` allein auf einer Zeile, macht wpautop
 * daraus `<p>[hu_kurz]</p>`. shortcode_unautop() entfernt das nur fuer
 * oeffnende Tags; der schliessende bliebe als `<p>[/hu_kurz]</p>` stehen und
 * hinterliesse nach do_shortcode() ein verwaistes `<p>` und `</p>`. Laeuft
 * nach wpautop (gleiche Prioritaet, spaeter registriert) und vor
 * do_shortcode (11).
 *
 * @param string $content Post content after wpautop.
 * @return string
 */
function hu_editorial_bausteine_unautop( $content ) {
	$content = (string) $content;

	if ( false === strpos( $content, '[hu_' ) && false === strpos( $content, '[/hu_' ) ) {
		return $content;
	}

	$tags = implode( '|', array_map( 'preg_quote', hu_editorial_bausteine_enclosing_tags() ) );

	$cleaned = preg_replace( '#<p>\s*(\[/?(?:' . $tags . ')(?:\s[^\]]*)?\])\s*</p>#', '$1', $content );

	return is_string( $cleaned ) ? $cleaned : $content;
}
add_filter( 'the_content', 'hu_editorial_bausteine_unautop', 10 );

/**
 * Normalise the inner content of an enclosing building block.
 *
 * Entfernt verwaiste Absatz-Tags an den Raendern und rendert verschachtelte
 * Shortcodes (etwa [hu_price] in der Zusatzzeile des Abschlusses).
 *
 * @param string|null $content Raw shortcode content.
 * @return string
 */
function hu_editorial_bausteine_inner( $content ) : string {
	$content = trim( (string) $content );
	$content = (string) preg_replace( '#^(?:\s*</p>|\s*<br\s*/?>)+|(?:<p>\s*|<br\s*/?>\s*)+$#', '', $content );
	$content = (string) preg_replace( '#^<p>\s*</p>|<p>\s*</p>$#', '', trim( $content ) );

	return trim( do_shortcode( $content ) );
}

/**
 * Put the FAQ skip marker on the outermost element of a rendered block.
 *
 * @param string $html Rendered block.
 * @return string
 */
function hu_editorial_bausteine_skip_faq( $html ) : string {
	$html = (string) $html;

	if ( '' === trim( $html ) ) {
		return '';
	}

	$marked = preg_replace( '/^(\s*<[a-z][a-z0-9]*)(?=[\s>])/i', '$1 ' . hu_faq_schema_skip_attribute(), $html, 1 );

	return is_string( $marked ) ? trim( $marked ) : trim( $html );
}

/**
 * Stable, unique DOM id for one block instance on the page.
 *
 * @param string $prefix Id prefix.
 * @param string $key    Optional block key (the `id` attribute).
 * @return string
 */
function hu_editorial_bausteine_dom_id( $prefix, $key = '' ) : string {
	static $counts = [];

	$base = sanitize_html_class( $prefix . ( '' !== $key ? '-' . $key : '' ) );

	$counts[ $base ] = isset( $counts[ $base ] ) ? $counts[ $base ] + 1 : 1;

	return 1 === $counts[ $base ] ? $base : $base . '-' . $counts[ $base ];
}

/**
 * Render a theme partial into a string.
 *
 * @param string               $slug Partial slug below template-parts/editorial/.
 * @param string               $name Partial name (the `id` attribute).
 * @param array<string, mixed> $args Arguments for the partial.
 * @return string
 */
function hu_editorial_bausteine_partial( $slug, $name, $args = [] ) : string {
	$name = sanitize_key( (string) $name );

	if ( '' === $name || '' === locate_template( 'template-parts/editorial/' . $slug . '-' . $name . '.php' ) ) {
		return '';
	}

	ob_start();
	get_template_part( 'template-parts/editorial/' . $slug, $name, $args );

	return (string) ob_get_clean();
}

/**
 * [hu_kurz]<ol>…</ol>[/hu_kurz] — "Kurz beantwortet".
 *
 * @param array<string, string>|string $atts    Shortcode attributes.
 * @param string|null                  $content Numbered statements.
 * @return string
 */
function hu_kurz_shortcode( $atts, $content = null ) : string {
	$atts  = shortcode_atts( [ 'label' => 'Kurz beantwortet' ], $atts, 'hu_kurz' );
	$inner = hu_editorial_bausteine_inner( $content );

	if ( '' === $inner ) {
		return '';
	}

	$id = hu_editorial_bausteine_dom_id( 'hu-kurz' );

	return sprintf(
		'<div class="hu-kurz" role="note" aria-labelledby="%1$s"><p class="hu-kurz__label" id="%1$s">%2$s</p>%3$s</div>',
		esc_attr( $id ),
		esc_html( (string) $atts['label'] ),
		$inner
	);
}
add_shortcode( 'hu_kurz', 'hu_kurz_shortcode' );

/**
 * [hu_notiz label="…"]…[/hu_notiz] — Randnotiz.
 *
 * Ab dem breiten Reader schwebt sie in die Marginalspalte, darunter steht sie
 * als Block im Fluss.
 *
 * @param array<string, string>|string $atts    Shortcode attributes.
 * @param string|null                  $content Note text, inline HTML allowed.
 * @return string
 */
function hu_notiz_shortcode( $atts, $content = null ) : string {
	$atts  = shortcode_atts( [ 'label' => 'Notiz' ], $atts, 'hu_notiz' );
	$inner = hu_editorial_bausteine_inner( $content );

	if ( '' === $inner ) {
		return '';
	}

	return hu_editorial_bausteine_skip_faq(
		sprintf(
			'<aside class="hu-notiz"><span class="hu-notiz__label">%1$s</span> %2$s</aside>',
			esc_html( (string) $atts['label'] ),
			$inner
		)
	);
}
add_shortcode( 'hu_notiz', 'hu_notiz_shortcode' );

/**
 * [hu_abb id="…" nr="…"] — Abbildung aus template-parts/editorial/abb-{id}.php.
 *
 * Ohne `nr` gilt die Nummer, die das Partial als Standard traegt.
 *
 * @param array<string, string>|string $atts Shortcode attributes.
 * @return string
 */
function hu_abb_shortcode( $atts ) : string {
	$atts = shortcode_atts(
		[
			'id' => '',
			'nr' => '',
		],
		$atts,
		'hu_abb'
	);

	$id   = sanitize_key( (string) $atts['id'] );
	$nr   = sanitize_text_field( (string) $atts['nr'] );
	$args = [ 'dom_id' => hu_editorial_bausteine_dom_id( 'abb', $id ) ];

	if ( '' !== $nr ) {
		$args['nr'] = $nr;
	}

	return hu_editorial_bausteine_skip_faq( hu_editorial_bausteine_partial( 'abb', $id, $args ) );
}
add_shortcode( 'hu_abb', 'hu_abb_shortcode' );

/**
 * [hu_fall id="…"] oder [hu_fall id="…"]Nachsatz[/hu_fall] — Fall aus
 * template-parts/editorial/fall-{id}.php.
 *
 * Text steht im Partial, Zahlen und Bezeichnung kommen aus dem Kanon.
 * Umschliesst der Shortcode Text, ersetzt dieser den Standard-Nachsatz des
 * Partials; der Link zur Fallstudie bleibt dahinter stehen. Ohne Inhalt gilt
 * der Standard-Nachsatz.
 *
 * @param array<string, string>|string $atts    Shortcode attributes.
 * @param string|null                  $content Optional closing line, inline HTML allowed.
 * @return string
 */
function hu_fall_shortcode( $atts, $content = null ) : string {
	$atts = shortcode_atts( [ 'id' => '' ], $atts, 'hu_fall' );
	$id   = sanitize_key( (string) $atts['id'] );

	return hu_editorial_bausteine_skip_faq(
		hu_editorial_bausteine_partial(
			'fall',
			$id,
			[
				'dom_id'   => hu_editorial_bausteine_dom_id( 'fall', $id ),
				'nachsatz' => hu_editorial_bausteine_inner( $content ),
			]
		)
	);
}
add_shortcode( 'hu_fall', 'hu_fall_shortcode' );

/**
 * [hu_pruefliste id="…" titel="…"]<ol>…</ol>[/hu_pruefliste].
 *
 * Ohne JS eine nummerierte Liste. editorial-bausteine.js ergaenzt
 * Checkboxen, Zaehler, Fortschrittslinie, Kopieren und Zuruecksetzen.
 * Kein Speichern im Browser.
 *
 * @param array<string, string>|string $atts    Shortcode attributes.
 * @param string|null                  $content Ordered list.
 * @return string
 */
function hu_pruefliste_shortcode( $atts, $content = null ) : string {
	$atts  = shortcode_atts(
		[
			'id'    => '',
			'titel' => 'Prüfliste',
		],
		$atts,
		'hu_pruefliste'
	);
	$inner = hu_editorial_bausteine_inner( $content );

	if ( '' === $inner ) {
		return '';
	}

	$id = hu_editorial_bausteine_dom_id( 'pruefliste', sanitize_key( (string) $atts['id'] ) );

	return sprintf(
		'<div class="hu-pruef" id="%1$s" role="group" aria-labelledby="%1$s-titel" data-hu-pruef><div class="hu-pruef__kopf"><p class="hu-pruef__titel" id="%1$s-titel">%2$s</p></div>%3$s</div>',
		esc_attr( $id ),
		esc_html( (string) $atts['titel'] ),
		$inner
	);
}
add_shortcode( 'hu_pruefliste', 'hu_pruefliste_shortcode' );

/**
 * [hu_abschluss …]Zusatzzeile[/hu_abschluss] — Abschluss-Tafel.
 *
 * Intro, Button-Text, "Sie schicken / Sie bekommen" und Antwortzeit kommen
 * aus hu_first_assessment_text() und hu_response_promise(). Ist der Versuch
 * Ersteinschaetzung ausgeschaltet, bleibt nur die Projektanfrage als
 * primaerer Button.
 *
 * Tracking: `{track}_close_ersteinschaetzung` und `{track}_close_project`,
 * Kategorie `lead_gen`, Abschnitt `abschluss` — dieselbe Form wie
 * `home_close_ersteinschaetzung` auf der Startseite. Die Tafel traegt den
 * FAQ-Skip-Marker, damit ihr Text nie in einer acceptedAnswer landet.
 *
 * @param array<string, string>|string $atts    Shortcode attributes.
 * @param string|null                  $content Optional additional line.
 * @return string
 */
function hu_abschluss_shortcode( $atts, $content = null ) : string {
	$atts = shortcode_atts(
		[
			'variante'      => 'ersteinschaetzung',
			'fokus'         => '',
			'kicker'        => '',
			'titel'         => '',
			'projekt_label' => 'Projekt anfragen',
			'track'         => 'blog',
		],
		$atts,
		'hu_abschluss'
	);

	$titel = trim( (string) $atts['titel'] );

	if ( '' === $titel ) {
		return '';
	}

	$track       = sanitize_key( (string) $atts['track'] );
	$track       = '' !== $track ? $track : 'blog';
	$first_on    = 'ersteinschaetzung' === (string) $atts['variante'] && hu_first_assessment_enabled();
	$project_url = function_exists( 'hu_get_contact_intake_url' )
		? hu_get_contact_intake_url( 'project', (string) $atts['fokus'] )
		: add_query_arg( 'type', 'project', home_url( '/kontakt/' ) );
	$id          = hu_editorial_bausteine_dom_id( 'hu-abschluss' );
	$zusatz      = hu_editorial_bausteine_inner( $content );

	$html  = '<aside class="hu-abschluss tafel" aria-labelledby="' . esc_attr( $id ) . '" data-track-section="abschluss">';
	$html .= '<div class="hu-abschluss__raster"><div class="hu-abschluss__haupt">';

	if ( '' !== trim( (string) $atts['kicker'] ) ) {
		$html .= '<p class="hu-abschluss__kicker">' . esc_html( (string) $atts['kicker'] ) . '</p>';
	}

	$html .= '<p class="hu-abschluss__titel" id="' . esc_attr( $id ) . '">' . esc_html( $titel ) . '</p>';

	if ( $first_on ) {
		$html .= '<p class="hu-abschluss__intro">' . esc_html( hu_first_assessment_text( 'intro_short' ) ) . '</p>';
	}

	$html .= '<div class="hu-abschluss__wege">';

	if ( $first_on ) {
		$html .= sprintf(
			'<a class="hu-tun hu-tun--pfeil" href="%1$s" data-track-action="%2$s" data-track-category="lead_gen" data-track-section="abschluss">%3$s</a>',
			esc_url( hu_first_assessment_url() ),
			esc_attr( $track . '_close_ersteinschaetzung' ),
			esc_html( hu_first_assessment_text( 'cta' ) )
		);
	}

	$html .= sprintf(
		'<a class="%1$s" href="%2$s" data-track-action="%3$s" data-track-category="lead_gen" data-track-section="abschluss">%4$s</a>',
		esc_attr( $first_on ? 'hu-tun hu-tun--still' : 'hu-tun' ),
		esc_url( $project_url ),
		esc_attr( $track . '_close_project' ),
		esc_html( (string) $atts['projekt_label'] )
	);

	$html .= '</div>';

	if ( '' !== $zusatz ) {
		$html .= '<p class="hu-abschluss__zusatz">' . $zusatz . '</p>';
	}

	$html .= '</div>';

	if ( $first_on ) {
		$html .= '<dl class="hu-abschluss__ablauf">';
		$html .= '<div><dt>Sie schicken</dt><dd>' . esc_html( hu_first_assessment_text( 'card_send' ) ) . '</dd></div>';
		$html .= '<div><dt>Sie bekommen</dt><dd>' . esc_html( hu_first_assessment_text( 'card_get' ) ) . '</dd></div>';
		$html .= '<div><dt>Antwort</dt><dd>' . esc_html( hu_response_promise( 'value' ) ) . '</dd></div>';
		$html .= '</dl>';
	}

	$html .= '</div></aside>';

	return hu_editorial_bausteine_skip_faq( $html );
}
add_shortcode( 'hu_abschluss', 'hu_abschluss_shortcode' );

/**
 * [hu_quellen]<ol>…</ol>[/hu_quellen] — nummerierte Quellenliste am Ende.
 *
 * @param array<string, string>|string $atts    Shortcode attributes.
 * @param string|null                  $content Ordered list of sources.
 * @return string
 */
function hu_quellen_shortcode( $atts, $content = null ) : string {
	$atts  = shortcode_atts( [ 'titel' => 'Quellen' ], $atts, 'hu_quellen' );
	$inner = hu_editorial_bausteine_inner( $content );

	if ( '' === $inner ) {
		return '';
	}

	$id = hu_editorial_bausteine_dom_id( 'hu-quellen' );

	return hu_editorial_bausteine_skip_faq(
		sprintf(
			'<section class="hu-quellen" aria-labelledby="%1$s"><p class="hu-quellen__titel" id="%1$s">%2$s</p>%3$s</section>',
			esc_attr( $id ),
			esc_html( (string) $atts['titel'] ),
			$inner
		)
	);
}
add_shortcode( 'hu_quellen', 'hu_quellen_shortcode' );
