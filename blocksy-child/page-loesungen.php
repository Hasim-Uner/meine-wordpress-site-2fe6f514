<?php
/**
 * Template Name: Alle Lösungen
 *
 * Interne Angebotsübersicht mit fokussierter 3-Stufen-Logik.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Legacy-Seite: aus Index entkoppeln, damit keine Mischsignale zur neuen Solar/Wärmepumpen-Positionierung entstehen.
if ( ! headers_sent() ) {
	header( 'X-Robots-Tag: noindex, nofollow', true );
}
add_action( 'wp_head', static function () {
	echo '<meta name="robots" content="noindex,nofollow" />' . "\n";
}, 1 );

$audit_url     = nexus_get_audit_url();
$wgos_url      = nexus_get_primary_public_url( 'wgos', home_url( '/wordpress-agentur-hannover/#wgos' ) );
$cases_url     = nexus_get_results_url();

get_header();
?>
<main class="solutions-overview">
	<section class="container" style="max-width:1120px; padding:5rem 20px 6rem;">
		<div style="text-align:center; max-width:760px; margin:0 auto 3rem;">
			<span class="nx-badge nx-badge--gold">Angebotsarchitektur</span>
			<h1 style="margin:1rem 0 1rem;">Drei Schritte. Ein klarer Einstieg.</h1>
			<p style="font-size:1.12rem; color:#64748b; line-height:1.7;">
				Diese Seite ist keine Leistungsbibliothek. Sie zeigt den sinnvollen Weg,
				mit dem aus Ihrer Website ein planbares Anfrage-System wird. Der öffentliche Fokus bleibt bewusst auf der System-Diagnose.
			</p>
		</div>

		<div class="solutions-list">
			<ul style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1.5rem; list-style:none; padding:0; margin:0;">
				<li class="solution-item">
					<a href="<?php echo esc_url( $audit_url ); ?>">
						<h2>1. System-Diagnose</h2>
						<p>Diagnose-Einstieg für Solar- und Wärmepumpen-Anbieter mit bestehender Website. Wir machen sichtbar, wo Sichtbarkeit, Vertrauen oder Conversion wegbrechen.</p>
						<span class="cta-btn">System-Diagnose anfragen</span>
					</a>
				</li>
				<li class="solution-item">
					<a href="<?php echo esc_url( $audit_url ); ?>">
						<h2>2. Priorisierte Folgeentscheidung</h2>
						<p>Kein öffentlicher Einstieg. Wenn der Fall tiefer geht, ergibt sich der nächste vertiefte Schritt erst nach System-Diagnose, Rückmeldung und persönlichem Kontakt.</p>
						<span class="cta-btn">Ergibt sich nach der Diagnose</span>
					</a>
				</li>
				<li class="solution-item">
					<a href="<?php echo esc_url( $wgos_url ); ?>">
						<h2>3. Kontrollierte Umsetzung &amp; laufende Weiterentwicklung</h2>
						<p>Umsetzung und laufende Optimierung in der richtigen Reihenfolge: Technik, SEO, Tracking, Content und Conversion.</p>
						<span class="cta-btn">System ansehen</span>
					</a>
				</li>
			</ul>
		</div>

		<div style="max-width:760px; margin:3rem auto 0; text-align:center;">
			<p style="color:#64748b; font-size:1rem; line-height:1.7; margin-bottom:1.5rem;">
				Wenn Sie erst sehen wollen, wie sich dieser Ansatz in der Praxis auswirkt, starten Sie nicht bei Einzelleistungen, sondern bei den dokumentierten Ergebnissen.
			</p>
			<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:1rem;">
				<a href="<?php echo esc_url( $cases_url ); ?>" class="cta-btn" style="font-size:1rem; padding:14px 28px;">Ergebnisse ansehen</a>
				<a href="<?php echo esc_url( $audit_url ); ?>" class="cta-btn" style="font-size:1rem; padding:14px 28px;">Direkt mit dem Audit starten</a>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
