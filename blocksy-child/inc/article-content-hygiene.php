<?php
/**
 * Targeted one-time hygiene for editor-owned flagship article content.
 *
 * The WordPress editor remains the content owner. This module never replays a
 * repo copy of an article. It only replaces known legacy passages that still
 * reference retired offers/positioning or a superseded proof value.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the current targeted content-hygiene version.
 *
 * @return string
 */
function hu_article_content_hygiene_version() : string {
	return '2026-09-05-1';
}

/**
 * Find a public article by slug without depending on a seeder module.
 *
 * @param string $slug Post slug.
 * @return int
 */
function hu_article_content_hygiene_find_post_id( $slug ) : int {
	$post_ids = get_posts(
		[
			'name'                   => sanitize_title( (string) $slug ),
			'post_type'              => 'post',
			'post_status'            => [ 'publish', 'draft', 'pending', 'future', 'private' ],
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		]
	);

	return ! empty( $post_ids ) ? (int) $post_ids[0] : 0;
}

/**
 * Replace one known legacy passage while tolerating an optional inline link.
 *
 * @param string $content     Current post HTML.
 * @param string $pattern     Unicode-safe PCRE pattern.
 * @param string $replacement Replacement HTML/text.
 * @param int    $count       Replacement count, passed by reference.
 * @return string
 */
function hu_article_content_hygiene_replace_pattern( $content, $pattern, $replacement, &$count ) : string {
	$local_count = 0;
	$updated     = preg_replace( $pattern, $replacement, (string) $content, -1, $local_count );

	if ( null === $updated ) {
		return (string) $content;
	}

	$count += (int) $local_count;
	return (string) $updated;
}

/**
 * Clean the legacy tracking article without overwriting unrelated editor work.
 *
 * Known fixes in this release:
 * - retired Journey-Audit CTAs are removed/re-routed to the active tracking path
 * - the retired WGOS/Owned-First framing becomes the current measurement model
 * - the E3 CPL proof is corrected from 25 EUR to the canonical 22 EUR
 * - the time-sensitive headline is replaced only when the exact legacy title
 *   is still present
 *
 * @return void
 */
function hu_maybe_refresh_tracking_article_content() : void {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = hu_article_content_hygiene_version();
	$option_key = 'hu_article_content_hygiene_tracking_version';

	if ( (string) get_option( $option_key, '' ) === $version ) {
		return;
	}

	$post_id = hu_article_content_hygiene_find_post_id( 'server-side-tracking-gtm' );
	if ( $post_id <= 0 ) {
		return;
	}

	$current_content = (string) get_post_field( 'post_content', $post_id );
	$current_title   = (string) get_post_field( 'post_title', $post_id );
	$content         = $current_content;
	$replacement_count = 0;

	$tracking_url = home_url( '/server-side-tracking-b2b/' );
	$case_url     = function_exists( 'nexus_get_primary_public_url' )
		? nexus_get_primary_public_url( 'e3', home_url( '/case-study-solar-leadgenerierung/' ) )
		: home_url( '/case-study-solar-leadgenerierung/' );

	$content = hu_article_content_hygiene_replace_pattern(
		$content,
		'~Wie groß der Datenverlust auf Ihrer Website tatsächlich ist, zeigt unser\s*(?:<a\b[^>]*>)?kostenloser Journey Audit(?:</a>)?\s*— inklusive einer konservativen Euro-Schätzung des monatlichen Revenue Gap\.~u',
		'Wie belastbar Ihre Messkette ist, lässt sich nicht seriös mit einem pauschalen Verlust-Prozentsatz beantworten. Prüfen Sie Browser-Events, Consent, Server-Container und CRM-Abgleich gemeinsam.',
		$replacement_count
	);

	$content = hu_article_content_hygiene_replace_pattern(
		$content,
		'~Das ist einer der häufigsten „unsichtbaren“ Umsatzbremsen, die wir in unserem\s*(?:<a\b[^>]*>)?Journey Audit(?:</a>)?\s*identifizieren: Der Kunde durchläuft die Customer Journey, konvertiert — aber das Tracking erfasst es nicht\.~u',
		'Das ist ein typisches Messproblem: Ein Nutzer konvertiert, aber das entscheidende Conversion-Signal kommt nicht vollständig oder nicht korrekt in Ads, Analytics oder CRM an.',
		$replacement_count
	);

	$content = hu_article_content_hygiene_replace_pattern(
		$content,
		'~Im\s+(?:<a\b[^>]*>)?E3 New Energy Case(?:</a>)?\s+konnten wir die Cost-per-Lead von 150\s*€ auf 25\s*€ senken\s*—\s*unter anderem durch saubere Datengrundlagen, die erst durch korrektes Tracking möglich wurden\. Ohne verlässliche Conversion-Daten wäre diese Optimierung ein Blindflug gewesen\.~u',
		sprintf(
			'Im <a href="%1$s">E3 New Energy Case</a> sank der Cost-per-Lead von 150 € auf 22 €. Tracking war dabei nicht der alleinige Hebel, sondern die Messgrundlage, um Kampagnen, Landingpages und Leadqualität sauber gegeneinander zu bewerten.',
			esc_url( $case_url )
		),
		$replacement_count
	);

	// Fallback for editor markup variants where only the numeric proof is stable.
	$numeric_count = 0;
	$content       = str_replace( '150 € auf 25 €', '150 € auf 22 €', $content, $numeric_count );
	$replacement_count += (int) $numeric_count;

	$owned_first_count = 0;
	$content = str_replace(
		'Server-Side Tracking als Teil des Owned-First-Systems',
		'Server-Side Tracking als Teil einer belastbaren Messkette',
		$content,
		$owned_first_count
	);
	$replacement_count += (int) $owned_first_count;

	$owned_first_body_count = 0;
	$content = str_replace(
		'Es ist ein zentraler Baustein einer Owned-First-Strategie — dem Ansatz, eigene Kanäle zu optimieren, bevor man Werbebudgets skaliert.',
		'Es ist ein Baustein einer belastbaren Messkette: eigene Datenpunkte sauber erfassen, Consent respektieren und Ergebnisse bis ins CRM zurückführen, bevor Budgets skaliert werden.',
		$content,
		$owned_first_body_count
	);
	$replacement_count += (int) $owned_first_body_count;

	$content = hu_article_content_hygiene_replace_pattern(
		$content,
		'~In unserem\s*(?:<a\b[^>]*>)?WordPress Growth Operating System \(WGOS\)(?:</a>)?\s*behandeln wir Tracking als eine von vier Säulen:~u',
		'Für belastbare Messketten behandeln wir Tracking nicht isoliert, sondern zusammen mit vier Ebenen:',
		$replacement_count
	);

	$next_step_heading_count = 0;
	$content = str_replace(
		'Nächster Schritt: Wie viel Datenverlust verursacht Ihr aktuelles Setup?',
		'Nächster Schritt: Messkette statt Einzeltool prüfen',
		$content,
		$next_step_heading_count
	);
	$replacement_count += (int) $next_step_heading_count;

	$content = hu_article_content_hygiene_replace_pattern(
		$content,
		'~Unser kostenloser Customer Journey Audit simuliert die komplette Reise Ihres nächsten Kunden — von der Google-Suche bis zum Lead-Formular\. Sie sehen, wo Interessenten abspringen und was das monatlich in Euro kostet\.~u',
		'Wenn Sie Server-Side Tracking sauber einordnen wollen, prüfen Sie zuerst die gesamte Messkette: Consent, Browser-Events, Server-Container, Ads-Plattformen und CRM-Rückmeldung. Erst danach lässt sich entscheiden, ob ein technischer Umbau wirklich der nächste sinnvolle Schritt ist.',
		$replacement_count
	);

	$content = hu_article_content_hygiene_replace_pattern(
		$content,
		'~<a\b[^>]*>\s*Kostenlosen Journey Audit starten\s*→\s*</a>~u',
		sprintf( '<a href="%1$s">Tracking-Setup ansehen →</a>', esc_url( $tracking_url ) ),
		$replacement_count
	);

	$new_title = $current_title;
	if ( 'Server-Side Tracking mit GTM: Warum deutsche B2B-Unternehmen jetzt umstellen müssen' === $current_title ) {
		$new_title = 'Server-Side Tracking mit GTM: Setup, Consent und saubere Messketten';
	}

	$needs_update = $content !== $current_content || $new_title !== $current_title;

	if ( $needs_update ) {
		$result = wp_update_post(
			wp_slash(
				[
					'ID'           => $post_id,
					'post_title'   => $new_title,
					'post_content' => $content,
				]
			),
			true
		);

		if ( is_wp_error( $result ) || ! $result ) {
			return;
		}

		update_post_meta( $post_id, '_hu_article_content_hygiene_version', $version );
		update_post_meta( $post_id, '_hu_article_content_hygiene_replacements', (string) $replacement_count );
	}

	// If none of the known legacy markers remain, the editor is already clean.
	$legacy_markers = [
		'150 € auf 25 €',
		'Journey Audit',
		'Customer Journey Audit',
		'WordPress Growth Operating System (WGOS)',
		'Owned-First-Systems',
		'Owned-First-Strategie',
	];

	$remaining_content = $needs_update ? $content : $current_content;
	foreach ( $legacy_markers as $marker ) {
		if ( false !== strpos( $remaining_content, $marker ) ) {
			return;
		}
	}

	update_option( $option_key, $version, false );
}
add_action( 'init', 'hu_maybe_refresh_tracking_article_content', 42 );
