<?php
/**
 * Global site footer.
 *
 * Focused footer for CRO: one primary CTA, a small set of decision paths
 * and direct trust/support links.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_year = wp_date( 'Y' );
$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$home_url     = $primary_urls['home'] ?? home_url( '/' );
$analysis_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$request_url  = $analysis_url;
$e3_url       = $primary_urls['e3'] ?? home_url( '/e3-new-energy/' );
$results_url  = $primary_urls['results'] ?? home_url( '/ergebnisse/' );
$blog_url     = $primary_urls['blog'] ?? home_url( '/blog/' );
$agentur_url  = $primary_urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' );
$about_url    = $primary_urls['about'] ?? home_url( '/uber-mich/' );
$contact_url  = $primary_urls['contact'] ?? nexus_get_contact_url();
$project_request_url = add_query_arg(
	[
		'type' => 'implementation',
	],
	$contact_url
);
$imprint_url      = $primary_urls['impressum'] ?? home_url( '/impressum/' );
$privacy_url      = $primary_urls['datenschutz'] ?? home_url( '/datenschutz/' );
$hide_primary_cta    = function_exists( 'nexus_should_hide_footer_primary_cta' ) && nexus_should_hide_footer_primary_cta();
$footer_class        = $hide_primary_cta ? 'ft ft--no-primary-cta ft--mobile-cta' : 'ft';
$audit_cta_label     = function_exists( 'nexus_get_audit_cta_label' ) ? nexus_get_audit_cta_label() : 'Marktcheck starten';
$audit_cta_microcopy = function_exists( 'nexus_get_audit_compact_microcopy' ) ? nexus_get_audit_compact_microcopy() : '60 Sek. · 5 Fragen · Antwort in 24 h';
$audit_footer_note   = function_exists( 'nexus_get_audit_footer_note' ) ? nexus_get_audit_footer_note() : 'Marktcheck: persönliche Ersteinschätzung, schriftliche Rückmeldung in 24 Stunden, kein Pflicht-Call.';
$diagnose_model      = function_exists( 'hu_diagnose_canon' ) ? hu_diagnose_canon() : [];
$diagnose_access     = isset( $diagnose_model['access_policy'] ) ? (string) $diagnose_model['access_policy'] : 'Kein Admin-Zugang in der Diagnose.';
$footer_trust_items  = array_values(
	array_filter(
		[
			'Keine Cookies bei öffentlichen Seitenaufrufen.',
			$diagnose_access,
			'Diagnose vor Umsetzungs-Pitch.',
		]
	)
);
?>

<?php if ( function_exists( 'nexus_is_audit_linkedin_page' ) && nexus_is_audit_linkedin_page() ) : ?>
<?php /* Footer rendered inline in audit-linkedin-shell.php */ ?>
<?php return; endif; ?>

<?php if ( function_exists( 'nexus_is_audit_page' ) && nexus_is_audit_page() ) : ?>
<footer id="footer" class="ft ft--audit-minimal" aria-labelledby="ft-heading" role="contentinfo">
	<h2 id="ft-heading" class="ft__sr">Footer-Navigation</h2>
	<div class="ft__audit-shell">
		<p class="ft__audit-note"><?php echo esc_html( $audit_footer_note ); ?></p>
		<nav class="ft__audit-links" aria-label="Marktcheck-Footer-Navigation">
			<a href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_audit_footer_analysis" data-track-category="lead_gen">Marktcheck starten</a>
			<a href="<?php echo esc_url( $imprint_url ); ?>" rel="nofollow">Impressum</a>
			<a href="<?php echo esc_url( $privacy_url ); ?>" rel="nofollow">Datenschutz</a>
		</nav>
	</div>
</footer>
<?php return; endif; ?>

<?php if ( is_page( 'solar-waermepumpen-leadgenerierung' ) || is_page( 'website-fuer-solar-und-waermepumpen-anbieter' ) || is_page_template( 'page-solar-waermepumpen-leadgenerierung.php' ) ) : ?>
<footer id="footer" class="ft ft--energy-minimal" aria-labelledby="ft-heading" role="contentinfo">
	<h2 id="ft-heading" class="ft__sr">Footer-Navigation</h2>
	<div class="ft__energy-shell">
			<div class="ft__energy-brand">
				<a class="ft__logo site-logo site-logo--accent" href="<?php echo esc_url( $home_url ); ?>" aria-label="Startseite - HAŞIM ÜNER">HAŞIM ÜNER</a>
				<p class="ft__energy-tag">Leadgenerierung für Solar- und Wärmepumpen-Betriebe.</p>
			</div>
			<a class="ft__cta" href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_energy_footer_analysis" data-track-category="lead_gen" data-track-section="footer_energy" data-track-funnel-stage="energy_footer">Marktcheck starten</a>
			<nav class="ft__energy-legal" aria-label="Rechtliches">
			<a href="<?php echo esc_url( $imprint_url ); ?>" rel="nofollow">Impressum</a>
			<span aria-hidden="true">·</span>
			<a href="<?php echo esc_url( $privacy_url ); ?>" rel="nofollow">Datenschutz</a>
		</nav>
		<div class="ft__social ft__social--energy" aria-label="Profile">
			<a href="https://www.linkedin.com/in/hasim-%C3%BCner/" aria-label="LinkedIn-Profil" rel="me noopener noreferrer" target="_blank">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 2h-17A1.5 1.5 0 0 0 2 3.5v17A1.5 1.5 0 0 0 3.5 22h17a1.5 1.5 0 0 0 1.5-1.5v-17A1.5 1.5 0 0 0 20.5 2zM8 19H5v-9h3zM6.5 8.25A1.75 1.75 0 1 1 8.3 6.5a1.78 1.78 0 0 1-1.8 1.75zM19 19h-3v-4.74c0-1.42-.6-1.93-1.38-1.93A1.74 1.74 0 0 0 13 14.19V19h-3v-9h2.9v1.3a3.11 3.11 0 0 1 2.7-1.4c1.55 0 3.36.86 3.36 3.66z"/></svg>
				LinkedIn
			</a>
		</div>
	</div>
</footer>
<?php return; endif; ?>

<footer id="footer" class="<?php echo esc_attr( $footer_class ); ?>" aria-labelledby="ft-heading" role="contentinfo">
	<h2 id="ft-heading" class="ft__sr">Footer-Navigation</h2>

	<div class="ft__top">
		<div class="ft__brand">
			<a class="ft__logo site-logo site-logo--accent" href="<?php echo esc_url( $home_url ); ?>" aria-label="Startseite - HAŞIM ÜNER">HAŞIM ÜNER</a>
			<p class="ft__tag">Eigene Anfrage-Systeme für Solar-, Wärmepumpen- und Speicher-Anbieter, die Portal-Abhängigkeit messbar senken wollen.</p>
			<?php if ( ! $hide_primary_cta ) : ?>
			<div class="ft__cta-group">
				<a class="ft__cta" href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_footer_analysis" data-track-category="lead_gen" data-track-section="footer" data-track-funnel-stage="footer_primary"><?php echo esc_html( $audit_cta_label ); ?></a>
				<p class="ft__cta-note"><?php echo esc_html( $audit_cta_microcopy ); ?></p>
			</div>
			<?php else : ?>
			<div class="ft__cta-group ft__cta-group--mobile-only">
				<a class="ft__cta ft__cta--mobile-only" href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_footer_analysis_mobile" data-track-category="lead_gen" data-track-section="footer" data-track-funnel-stage="footer_mobile"><?php echo esc_html( $audit_cta_label ); ?></a>
				<p class="ft__cta-note ft__cta-note--mobile-only"><?php echo esc_html( $audit_cta_microcopy ); ?></p>
			</div>
			<?php endif; ?>
			<ul class="ft__trust-list" aria-label="Vertrauenshinweise">
				<?php foreach ( $footer_trust_items as $footer_trust_item ) : ?>
					<li><?php echo esc_html( $footer_trust_item ); ?></li>
				<?php endforeach; ?>
			</ul>
			<p class="ft__privacy-link"><a href="<?php echo esc_url( $privacy_url ); ?>" rel="nofollow">Datenschutz ansehen</a></p>
		</div>

		<nav class="ft__cols" aria-label="Footer-Navigation">
			<section class="ft__col" aria-labelledby="ft-ergebnisse">
				<h3 id="ft-ergebnisse">Ergebnisse</h3>
				<ul class="ft__list">
					<li><a class="ft__link-strong" href="<?php echo esc_url( $results_url ); ?>" data-track-action="cta_footer_nav_results" data-track-category="trust" data-track-section="footer">Referenzen ansehen</a></li>
					<li><a href="<?php echo esc_url( $e3_url ); ?>" data-track-action="cta_footer_nav_e3_proof" data-track-category="trust" data-track-section="footer">Fallstudie: E3 New Energy</a></li>
				</ul>
			</section>

			<section class="ft__col" aria-labelledby="ft-wissen">
				<h3 id="ft-wissen">Wissen</h3>
				<ul class="ft__list">
					<li><a class="ft__link-strong" href="<?php echo esc_url( $blog_url ); ?>" data-track-action="cta_footer_nav_insights" data-track-category="navigation" data-track-section="footer">Insights</a></li>
					<li><a href="<?php echo esc_url( $agentur_url ); ?>" data-track-action="cta_footer_nav_agentur" data-track-category="navigation" data-track-section="footer">WordPress Agentur Hannover</a></li>
				</ul>
			</section>

			<section class="ft__col" aria-labelledby="ft-kontakt">
				<h3 id="ft-kontakt">Kontakt</h3>
				<ul class="ft__list">
					<li><a class="ft__link-strong" href="<?php echo esc_url( $project_request_url ); ?>" data-track-action="cta_footer_nav_project" data-track-category="lead_gen" data-track-section="footer">Umsetzung besprechen</a></li>
					<li><a href="<?php echo esc_url( $about_url ); ?>" data-track-action="cta_footer_nav_about" data-track-category="navigation" data-track-section="footer">Über Haşim</a></li>
					<li><a href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_footer_nav_contact" data-track-category="navigation" data-track-section="footer">Direktkontakt</a></li>
				</ul>

				<nav class="ft__legal" aria-label="Rechtliches">
					<a href="<?php echo esc_url( $imprint_url ); ?>" rel="nofollow">Impressum</a>
					<span aria-hidden="true">·</span>
					<a href="<?php echo esc_url( $privacy_url ); ?>" rel="nofollow">Datenschutz</a>
				</nav>
			</section>
		</nav>
	</div>

	<div class="ft__bottom">
		<p>&copy; <time class="ft__year" datetime="<?php echo esc_attr( $current_year ); ?>"><?php echo esc_html( $current_year ); ?></time> Haşim Üner - Anfrage-Systeme für Solar &amp; Wärmepumpe</p>
		<div class="ft__social" aria-label="Profile">
			<a href="https://www.linkedin.com/in/hasim-%C3%BCner/" aria-label="LinkedIn-Profil" rel="me noopener noreferrer" target="_blank">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 2h-17A1.5 1.5 0 0 0 2 3.5v17A1.5 1.5 0 0 0 3.5 22h17a1.5 1.5 0 0 0 1.5-1.5v-17A1.5 1.5 0 0 0 20.5 2zM8 19H5v-9h3zM6.5 8.25A1.75 1.75 0 1 1 8.3 6.5a1.78 1.78 0 0 1-1.8 1.75zM19 19h-3v-4.74c0-1.42-.6-1.93-1.38-1.93A1.74 1.74 0 0 0 13 14.19V19h-3v-9h2.9v1.3a3.11 3.11 0 0 1 2.7-1.4c1.55 0 3.36.86 3.36 3.66z"/></svg>
				LinkedIn
			</a>
		</div>
	</div>
</footer>
