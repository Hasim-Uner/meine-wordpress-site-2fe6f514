<?php
/**
 * One-time evidence migration for the legacy Core Web Vitals article.
 *
 * The article stays editor-owned. This module only replaces known deterministic
 * ranking/revenue claims with the evidence standard used by the Final-Cut
 * SEO/GEO system.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hu_cwv_evidence_migration_version() : string {
	return '2026-10-07-1';
}

function hu_maybe_refresh_cwv_article_evidence() : void {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = hu_cwv_evidence_migration_version();
	$option_key = 'hu_cwv_evidence_migration_version';

	if ( (string) get_option( $option_key, '' ) === $version ) {
		return;
	}

	$post = get_page_by_path( 'core-web-vitals-wachstum-seo-und-roas', OBJECT, 'post' );
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	$content = (string) $post->post_content;
	$title   = (string) $post->post_title;
	$count   = 0;

	$replacements = [
		[
			'<p>Eine <strong>langsame Website</strong> ist eines der teuersten Probleme im digitalen Geschäft. Es geht dabei nicht um technische Eitelkeiten, sondern um harte Währung: Jeder Wimpernschlag zusätzlicher Ladezeit kostet Besucher, schmälert die Conversion-Rate und sendet negative Signale an Google. In diesem Guide erhalten Sie den Fahrplan, um Ihre WordPress-Instanz von einer Bremse in einen Wachstumsmotor zu verwandeln.</p>',
			'<p>Eine <strong>langsame Website</strong> kann Nutzung, Conversion und Crawling unnötig erschweren. Wie groß der Effekt tatsächlich ist, hängt von Seitentyp, Gerät, Netzwerk, Inhalt und Nutzerintention ab. Dieser Leitfaden zeigt deshalb, wie Sie WordPress-Performance messen, technische Engpässe priorisieren und Verbesserungen mit Felddaten statt mit pauschalen Umsatzversprechen bewerten.</p>',
		],
		[
			'<h2 id="warum-ladezeit-geld-ist">Warum Ladezeit bares Geld ist: Die brutale Wahrheit</h2><p>Betrachten wir Ladezeit aus der einzigen Perspektive, die zählt: <strong>Profit</strong>. Eine schnelle Website ist keine Option, sondern eine betriebswirtschaftliche Notwendigkeit.</p>',
			'<h2 id="warum-ladezeit-geld-ist">Warum Ladezeit geschäftlich relevant sein kann</h2><p>Performance ist kein Selbstzweck. Entscheidend ist, ob Nutzer den Hauptinhalt schnell sehen, ohne Verzögerung interagieren können und das Layout stabil bleibt. Diese technische Qualität sollte anschließend gegen echte Geschäftsmetriken wie Formularstarts, abgeschickte Anfragen oder Abbrüche geprüft werden.</p>',
		],
		[
			'<p>Google hat eine klare Mission: Nutzerzufriedenheit. Seit der Einführung der <strong>Core Web Vitals</strong> straft der Algorithmus langsame Seiten systematisch ab. Wer hier patzt, verliert Sichtbarkeit – noch bevor der erste Kunde die Seite gesehen hat.</p>',
			'<p><strong>Core Web Vitals</strong> sind Messwerte für reale Nutzungserfahrungen und Teil der gesamten Page Experience. Gute Werte sind sinnvoll, garantieren aber weder Rankings noch Traffic. Für SEO zählen sie gemeinsam mit Relevanz, Inhalt, Crawlbarkeit und vielen weiteren Signalen.</p>',
		],
		[
			'<h3>Conversion-Killer Ladezeit</h3><p>Studien von Amazon bis Deloitte belegen es: Eine Verzögerung von nur <strong>0,1 Sekunden</strong> kann die Conversion-Rate im E-Commerce messbar senken. Bei einer Ladezeit von über 3 Sekunden springen <strong>32% der Besucher</strong> sofort ab. Rechnen Sie das auf Ihren Jahresumsatz hoch – das ist das Geld, das Sie aktuell auf der Straße liegen lassen.</p>',
			'<h3>Performance und Conversion sauber messen</h3><p>Allgemeine Benchmarks lassen sich nicht seriös in den Umsatz Ihrer Website übersetzen. Prüfen Sie deshalb eigene Felddaten und Conversion-Ereignisse: Welche Seitentypen sind langsam, auf welchen Geräten tritt das Problem auf und verändert eine technische Korrektur tatsächlich Interaktion, Formularstarts oder Abschlüsse?</p>',
		],
		[
			'<p><strong>Profi-Tipp:</strong> Achten Sie im PageSpeed-Report weniger auf den Score (0-100) im Labor, sondern primär auf die <strong>"Felddaten" (Core Web Vitals)</strong>. Das sind die echten Erfahrungen Ihrer Nutzer aus den letzten 28 Tagen. Nur diese zählen für das Ranking.</p>',
			'<p><strong>Praxis-Tipp:</strong> Trennen Sie Labor- und Felddaten. Labordaten helfen bei reproduzierbarer Fehlersuche; Felddaten zeigen aggregierte reale Nutzung. Für Core Web Vitals bewertet Google Felddaten über ein rollierendes 28-Tage-Fenster. Beides beantwortet unterschiedliche Fragen und sollte gemeinsam gelesen werden.</p>',
		],
		[
			'<p>80% der Performance-Probleme lassen sich durch fünf fundamentale Optimierungen lösen. Packen wir das Problem an der Wurzel.</p>',
			'<p>Viele WordPress-Performance-Probleme liegen in denselben technischen Bereichen. Welche davon bei Ihrer Website relevant sind, sollte jedoch gemessen werden, bevor Plugins oder Hosting gewechselt werden.</p>',
		],
		[
			'<p>Das ist ein Investment, keine Ausgabe. Professionelle Audits und Optimierungen amortisieren sich oft innerhalb weniger Monate durch bessere Rankings und höhere Conversion-Raten.</p>',
			'<p>Das hängt vom technischen Scope ab. Ein seriöser Audit sollte zuerst Engpässe und Messwerte dokumentieren; erst danach lässt sich abschätzen, welche Optimierung wirtschaftlich sinnvoll ist. Bessere Rankings oder Conversion-Raten sind mögliche Ergebnisse, aber keine garantierte Folge einer Performance-Maßnahme.</p>',
		],
	];

	foreach ( $replacements as $replacement ) {
		$local = 0;
		$content = str_replace( $replacement[0], $replacement[1], $content, $local );
		$count += (int) $local;
	}

	$new_title = 'Core Web Vitals in WordPress: messen, einordnen, optimieren';
	$needs_update = $content !== (string) $post->post_content || $title !== $new_title;

	if ( $needs_update ) {
		$result = wp_update_post(
			wp_slash(
				[
					'ID'           => (int) $post->ID,
					'post_title'   => $new_title,
					'post_excerpt' => 'Core Web Vitals in WordPress sauber messen: LCP, INP und CLS mit Feld- und Labordaten einordnen, Engpässe priorisieren und Optimierungen ohne Ranking- oder Umsatzversprechen prüfen.',
					'post_content' => $content,
				]
			),
			true
		);

		if ( is_wp_error( $result ) || ! $result ) {
			return;
		}
	}

	$legacy_markers = [
		'straft der Algorithmus langsame Seiten systematisch ab',
		'0,1 Sekunden',
		'32% der Besucher',
		'Nur diese zählen für das Ranking',
		'amortisieren sich oft innerhalb weniger Monate',
	];

	foreach ( $legacy_markers as $marker ) {
		if ( false !== strpos( $content, $marker ) ) {
			return;
		}
	}

	update_option( $option_key, $version, false );
	update_post_meta( (int) $post->ID, '_hu_cwv_evidence_replacements', (string) $count );
}
add_action( 'init', 'hu_maybe_refresh_cwv_article_evidence', 42 );
