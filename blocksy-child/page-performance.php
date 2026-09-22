<?php
/**
 * Template Name: Performance Marketing
 * Description: Performance Marketing für B2B – erst Messung, dann Zielseite, dann Budget.
 *
 * Direktkunden-Money-Page für bezahlte Nachfrage (Google Ads, Meta). Der Kern
 * ist die Reihenfolge Messung → Zielseite → Budget; Kampagnenbetreuung gibt es
 * dort, wo sie an WordPress, Tracking und Conversion hängt. Performance-
 * Agenturen bekommen einen eigenen Weg zu White-Label.
 *
 * Seit 2026-09-22 im Gutachten-Layout (system.css) statt im generischen
 * WGOS-Cluster-Renderer. Titel, Beschreibung und FAQ kommen weiter aus
 * nexus_get_wgos_cluster_page_data(), damit sichtbare Fragen und FAQPage-Schema
 * nicht auseinanderlaufen; das Service-Schema steht in inc/org-schema.php.
 * Zahlen kommen ausschließlich aus dem Kanon.
 *
 * Die Route wird über nexus_get_wgos_cluster_route_templates() unabhängig von
 * der Template-Zuordnung in der Datenbank auf diese Datei gelegt.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'hu-wayfinding-active';
		$classes[] = 'hu-performance-page';
		return array_values( array_unique( $classes ) );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		if ( function_exists( 'hu_enqueue_css' ) ) {
			hu_enqueue_css( 'hu-navigation-ecosystem', 'navigation-ecosystem.css', [ 'nexus-system-css' ] );
		}

		if ( function_exists( 'hu_enqueue_js' ) ) {
			hu_enqueue_js( 'hu-navigation-ecosystem', 'navigation-ecosystem.js', [] );
		}
	},
	90
);

add_action(
	'wp_body_open',
	static function () {
		?>
		<nav class="hu-wayfinding-breadcrumb" aria-label="Breadcrumb" data-track-section="breadcrumb">
			<ol>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" data-track-action="breadcrumb_home" data-track-category="navigation">Startseite</a></li>
				<li><span aria-current="page">Performance Marketing</span></li>
			</ol>
		</nav>
		<?php
	},
	30
);

$page            = nexus_get_wgos_cluster_page( 'performance-marketing' );
$faq_items       = isset( $page['faq_items'] ) && is_array( $page['faq_items'] ) ? $page['faq_items'] : [];
$contact_url     = hu_get_commercial_route( 'project_request' );
$tracking_url    = nexus_get_page_url( [ 'ga4-tracking-setup' ], home_url( '/ga4-tracking-setup/' ) );
$sst_url         = hu_get_commercial_route( 'tracking_b2b' );
$funnel_url      = home_url( '/#angebot-funnel' );
$results_url     = hu_get_commercial_route( 'results' );
$whitelabel_url  = hu_get_commercial_route( 'whitelabel' );
$wl_task_url     = add_query_arg( [ 'type' => 'whitelabel', 'case' => 'aufgabe' ], $whitelabel_url ) . '#aufgabe';
$response        = hu_response_promise( 'compact' );
$response_window = hu_response_promise( 'window' );
$tracking_price  = hu_tracking_price( 'standard', 'setup', 'display' );
$e3_canon        = hu_e3_canon();
$case_url        = $e3_canon['url'];
$case_label      = $e3_canon['case_label_accusative'] ?? HU_E3_CASE_LABEL_ACCUSATIVE;
$cpl_before      = hu_e3_metric( 'cpl_before' );
$cpl_after       = hu_e3_metric( 'cpl_after' );
$lead_count      = hu_e3_metric( 'lead_count' );
$timeframe       = hu_e3_metric( 'timeframe', 'display_dative' );

get_header();
?>

<div id="performance-content" class="doku perf-page" data-track-page="performance_marketing">
	<header class="kopfteil" data-track-section="perf_hero">
		<div class="blatt">
			<p class="gegenstand">Performance Marketing · B2B · Google Ads &amp; Meta</p>
			<h1>Performance Marketing für B2B: erst die Messung, dann das Budget.</h1>
			<p class="aufriss">
				<span class="erst">Klicks sind selten das Problem.</span>
				Wenn Google Ads oder Meta Anfragen liefern, mit denen der Vertrieb nichts anfangen kann, liegt es meist an dem, was als Conversion zählt, und an der Seite hinter dem Klick. Beides baue ich so, dass die Kampagne auf echte Anfragen optimiert.
			</p>

			<div class="ausgang">
				<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_perf_hero_project" data-track-category="lead_gen" data-track-section="perf_hero">Ausgangslage prüfen lassen <span class="pf" aria-hidden="true">→</span></a>
				<a class="tun still" href="#reihenfolge" data-track-action="perf_hero_to_order" data-track-category="navigation" data-track-section="perf_hero">Vorgehen ansehen</a>
			</div>
			<p class="mono"><?php echo esc_html( $response ); ?> · Messung ab <?php echo esc_html( $tracking_price ); ?> netto · jeder Schritt einzeln beauftragbar</p>

			<div class="meta" aria-label="Vorgehen im Überblick">
				<dl>
					<div><dt>01 · Messung</dt><dd>Was zählt als Anfrage – und was nur als Klick?</dd></div>
					<div><dt>02 · Zielseite</dt><dd>Landingpage und Formular, die das Anzeigenversprechen einlösen</dd></div>
					<div><dt>03 · Budget</dt><dd>Skalieren, wenn die Signale stimmen</dd></div>
					<div><dt>Für Agenturen</dt><dd><a class="satzlink" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="perf_hero_whitelabel" data-track-category="navigation" data-track-section="perf_hero">Umsetzung unter Ihrem Namen</a></dd></div>
				</dl>
			</div>
		</div>
	</header>

	<section id="diagnose" data-track-section="perf_diagnosis">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">01</span><span class="titel">Diagnose</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Wenn Kampagnen laufen, aber der Vertrieb nichts davon hat</p>
				<h2 class="kopf">Die Kampagne wird besser. Nur in die falsche Richtung.</h2>
				<p class="vorspann">Google Ads und Meta optimieren auf das, was als Conversion gemeldet wird. Zählt jedes abgeschickte Formular gleich viel, lernt der Algorithmus, günstige Kontakte einzukaufen statt passender Projektanfragen. Mehr Budget verstärkt diesen Fehler, es korrigiert ihn nicht.</p>
				<div class="protokoll" aria-label="Typische Ursachen">
					<div class="z"><span>01</span><b>Das Konto meldet Conversions, der Vertrieb sieht keine passenden Anfragen.</b></div>
					<div class="z"><span>02</span><b>Newsletter, Kontaktformular und Projektanfrage lösen dasselbe Signal aus.</b></div>
					<div class="z"><span>03</span><b>Nach dem Consent-Banner fehlen Conversions oder werden doppelt gezählt.</b></div>
					<div class="z"><span>04</span><b>Der bezahlte Klick landet auf einer Seite, die das Anzeigenversprechen nicht einlöst.</b></div>
				</div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Ursache</span><b>Selten die Anzeige.</b> Meist sind es falsche Conversion-Ziele, fehlende Consent-Logik oder eine Zielseite ohne klaren nächsten Schritt.</p></aside>
		</div>
	</section>

	<section id="reihenfolge" data-track-section="perf_order">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">02</span><span class="titel">Vorgehen</span><span class="strich"></span></div></div>
			<div class="voll">
				<div class="tafel">
					<p class="mono stempelfarbe">Messung → Zielseite → Budget</p>
					<h2 class="kopf">Drei Schritte, in dieser Reihenfolge.</h2>
					<p class="vorspann">Jeder Schritt ist einzeln beauftragbar. Wenn nur die Signale klemmen, braucht es keine neue Landingpage – und wenn die Messung stimmt, keinen neuen Tracking-Umbau.</p>
					<div class="protokoll" aria-label="Vorgehen in drei Schritten">
						<div class="z"><span>01 · Messung</span><b>Messplan, GA4 und Google Tag Manager, Consent Mode, Conversion-Import in Google Ads und Meta. Bei Bedarf Server-Side und Rücksignale aus dem CRM.</b></div>
						<div class="z"><span>02 · Zielseite</span><b>Landingpage und Formular in WordPress, die das Versprechen der Anzeige ohne Bruch in eine Anfrage überführen – mit Qualifizierung statt Masse.</b></div>
						<div class="z"><span>03 · Budget</span><b>Kampagnen in Google Ads und Meta dort, wo sie an WordPress, Tracking und Conversion hängen. Skaliert wird, wenn die Signale belastbar sind.</b></div>
					</div>
					<div class="ausgang">
						<a class="textlink" href="<?php echo esc_url( $tracking_url ); ?>" data-track-action="perf_to_tracking_setup" data-track-category="navigation" data-track-section="perf_order">Tracking-Setup im Detail</a>
						<a class="textlink" href="<?php echo esc_url( $funnel_url ); ?>" data-track-action="perf_to_landingpages" data-track-category="navigation" data-track-section="perf_order">Landingpages und Anfragestrecken</a>
						<a class="textlink" href="<?php echo esc_url( $sst_url ); ?>" data-track-action="perf_to_server_side" data-track-category="navigation" data-track-section="perf_order">Server-Side Tracking</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section id="umfang" data-track-section="perf_scope">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">03</span><span class="titel">Umfang</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Was dazugehört – und was nicht</p>
				<h2 class="kopf">Kampagnen dort, wo sie an Ihrer Website hängen.</h2>
				<p class="vorspann">Ich arbeite an der Stelle, an der Anzeige, Website und Vertrieb zusammenkommen. Kampagnenbetreuung gehört dazu, soweit sie an WordPress, Tracking und Conversion hängt – nicht als Mediaplanung für jedes Budget.</p>
				<div class="protokoll" aria-label="Umfang">
					<div class="z"><span>Dazu</span><b>Conversion-Messung, Consent und Conversion-Import in Google Ads und Meta</b></div>
					<div class="z"><span>Dazu</span><b>Landingpages und Formulare in WordPress, inklusive Qualifizierung</b></div>
					<div class="z"><span>Dazu</span><b>Google Ads und Meta Ads im B2B-Kontext, abgestimmt auf Messung und Zielseite</b></div>
					<div class="z"><span>Dazu</span><b>Rücksignale aus dem CRM, wenn Leadqualität statt Formularanzahl zählen soll</b></div>
					<div class="z"><span>Nicht dazu</span><b>Mediaplanung für große Multi-Markt-Budgets, Kreativproduktion für Bewegtbild, Marktplatz-Werbung</b></div>
				</div>
			</div>
			<aside class="marg">
				<p class="note"><span class="label">Preis</span><b>Messung ab <?php echo esc_html( $tracking_price ); ?> netto.</b> Zielseite und Kampagnenbetreuung nach Umfang, vor dem Start schriftlich festgelegt.</p>
				<p class="note"><span class="label">Konten</span>Werbekonten, Tag Manager und Website bleiben bei Ihnen. Übergeben werden Messplan, Prüfprotokoll und Dokumentation.</p>
			</aside>
		</div>
	</section>

	<section id="beleg" data-track-section="perf_proof">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">04</span><span class="titel">Beleg</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Dokumentierter Fall</p>
				<h2 class="kopf">Der Hebel lag vor der Kampagne.</h2>
				<p class="vorspann">
					<?php
					echo esc_html(
						sprintf(
							'Für einen %1$s sanken die Kosten pro Anfrage in %2$s von %3$s auf %4$s; über das eigene System kamen %5$s qualifizierte Anfragen. Der Hebel lag nicht in der Kampagne, sondern in Messung und Anfragestrecke davor.',
							$case_label,
							$timeframe,
							$cpl_before,
							$cpl_after,
							$lead_count
						)
					);
					?>
				</p>
				<div class="ausgang">
					<a class="textlink" href="<?php echo esc_url( $case_url ); ?>" data-track-action="perf_proof_case" data-track-category="proof" data-track-section="perf_proof">Fallstudie und Herleitung ansehen</a>
					<a class="textlink" href="<?php echo esc_url( $results_url ); ?>" data-track-action="perf_proof_results" data-track-category="proof" data-track-section="perf_proof">Weitere Arbeiten und Ergebnisse</a>
				</div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Übertragbar?</span>Das hängt an Marktgröße, Angebot und Wettbewerb. Die Fallstudie legt die Herleitung offen, statt die Zahl allein zu zeigen.</p></aside>
		</div>
	</section>

	<section id="agenturen" data-track-section="perf_agencies">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">05</span><span class="titel">Agenturen</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Für Performance-Agenturen</p>
				<h2 class="kopf">Die Kampagnen laufen bei Ihnen. Messung und Landingpage fehlen im Team?</h2>
				<p class="vorspann">Dann setze ich Tracking, Server-Side und Landingpages unter Ihrem Namen um: mit abgestimmter Übergabe und Code und Zugängen in Ihren Accounts. Die Kundenbeziehung bleibt bei Ihnen.</p>
				<div class="ausgang">
					<a class="tun still" href="<?php echo esc_url( $wl_task_url ); ?>" data-track-action="perf_to_whitelabel_task" data-track-category="lead_gen" data-track-section="perf_agencies">White-Label-Aufgabe beschreiben <span class="pf" aria-hidden="true">→</span></a>
					<a class="textlink" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="perf_to_whitelabel" data-track-category="navigation" data-track-section="perf_agencies">So läuft White-Label ab</a>
				</div>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $faq_items ) ) : ?>
		<section id="fragen" data-track-section="perf_faq">
			<div class="blatt reihe">
				<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">06</span><span class="titel">Fragen</span><span class="strich"></span></div></div>
				<div class="haupt">
					<p class="mono stempelfarbe">Vor der Beauftragung</p>
					<h2 class="kopf leise">Häufige Fragen zum Performance Marketing.</h2>
					<div class="fragen">
						<?php foreach ( $faq_items as $item ) : ?>
							<?php
							$question = isset( $item['question'] ) ? (string) $item['question'] : '';
							$answer   = isset( $item['answer'] ) ? (string) $item['answer'] : '';
							if ( '' === $question || '' === $answer ) {
								continue;
							}
							?>
							<details name="perf-faq"><summary><?php echo esc_html( $question ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $answer ); ?></p></div></div></details>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="abschluss" id="anfrage" data-track-section="perf_close">
		<div class="blatt reihe"><div class="ganz"><div class="tafel"><div class="reihe">
			<div class="haupt">
				<p class="mono stempelfarbe">Nächster Schritt</p>
				<h2>Was kostet Sie heute eine brauchbare Anfrage?</h2>
				<p class="aufriss">Schreiben Sie kurz, welche Kanäle laufen, was eine Anfrage aktuell kostet und wo es hakt. Sie bekommen eine Einschätzung, welcher Schritt zuerst zählt – auch dann, wenn sie gegen mehr Budget spricht.</p>
				<div class="ausgang"><a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_perf_close_project" data-track-category="lead_gen" data-track-section="perf_close">Ausgangslage prüfen lassen <span class="pf" aria-hidden="true">→</span></a></div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Antwort</span>Persönlich <?php echo esc_html( $response_window ); ?>, mit einer Einschätzung oder gezielten Rückfragen.</p></aside>
		</div></div></div></div>
	</section>
</div>

<?php
get_footer();
