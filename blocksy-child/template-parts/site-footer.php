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
$whitelabel_url   = function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' );
$hide_primary_cta    = function_exists( 'nexus_should_hide_footer_primary_cta' ) && nexus_should_hide_footer_primary_cta();
$footer_class        = $hide_primary_cta ? 'ft ft--no-primary-cta ft--mobile-cta' : 'ft';
$audit_cta_label     = function_exists( 'nexus_get_audit_cta_label' ) ? nexus_get_audit_cta_label() : 'Marktcheck mit Fit-Entscheid starten';
$audit_cta_microcopy = function_exists( 'nexus_get_audit_compact_microcopy' ) ? nexus_get_audit_compact_microcopy() : 'Region · Vertrieb · Anfragequalität prüfen';
$audit_footer_note   = function_exists( 'nexus_get_audit_footer_note' ) ? nexus_get_audit_footer_note() : 'Marktcheck: Manueller Fit-Befund statt Software-Einheitsbrei — händische Einordnung von Region, Vertrieb und Anfragequalität.';

$is_whitelabel_context = is_page_template( 'page-whitelabel-retainer.php' )
	|| is_page( 'whitelabel-retainer' )
	|| is_page( 'whitelabel-retainer-proof' )
	|| is_page( 'whitelabel' );

if ( $is_whitelabel_context ) {
	$brand_tagline   = 'White-Label-Partner für Agenturen — SEO, WordPress, Tracking und Conversion. Unsichtbar im Hintergrund.';
	$copyright_line  = 'Haşim Üner - White-Label-Partner für Agenturen';
} else {
	$brand_tagline   = 'Eigene Anfrage-Systeme für Solar-, Wärmepumpen- und Speicher-Anbieter, die Portal-Abhängigkeit messbar senken wollen.';
	$copyright_line  = 'Haşim Üner - Anfrage-Systeme für Solar & Wärmepumpe';
}
?>

<?php if ( function_exists( 'nexus_is_audit_page' ) && nexus_is_audit_page() ) : ?>
<footer id="footer" class="ft ft--audit-minimal" aria-labelledby="ft-heading" role="contentinfo">
	<h2 id="ft-heading" class="ft__sr">Footer-Navigation</h2>
	<div class="ft__audit-shell">
		<p class="ft__audit-note"><?php echo esc_html( $audit_footer_note ); ?></p>
		<nav class="ft__audit-links" aria-label="Marktcheck-Footer-Navigation">
			<a href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_audit_footer_analysis" data-track-category="lead_gen">Marktcheck mit Fit-Entscheid starten</a>
			<a href="<?php echo esc_url( $imprint_url ); ?>">Impressum</a>
			<a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutz</a>
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
			<a class="ft__cta" href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_energy_footer_analysis" data-track-category="lead_gen" data-track-section="footer_energy" data-track-funnel-stage="energy_footer">Marktcheck mit Fit-Entscheid starten</a>
			<nav class="ft__energy-legal" aria-label="Rechtliches">
			<a href="<?php echo esc_url( $imprint_url ); ?>">Impressum</a>
			<span aria-hidden="true">·</span>
			<a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutz</a>
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
			<p class="ft__tag"><?php echo esc_html( $brand_tagline ); ?></p>
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
					<li><a href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="cta_footer_nav_whitelabel" data-track-category="lead_gen" data-track-section="footer">Für Agenturen: White-Label</a></li>
					<li><a href="<?php echo esc_url( $about_url ); ?>" data-track-action="cta_footer_nav_about" data-track-category="navigation" data-track-section="footer">Über Haşim</a></li>
					<li><a href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_footer_nav_contact" data-track-category="navigation" data-track-section="footer">Direktkontakt</a></li>
				</ul>

				<nav class="ft__legal" aria-label="Rechtliches">
					<a href="<?php echo esc_url( $imprint_url ); ?>">Impressum</a>
					<span aria-hidden="true">·</span>
					<a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutz</a>
				</nav>
			</section>
		</nav>
	</div>

	<div class="ft__bottom">
		<p>&copy; <time class="ft__year" datetime="<?php echo esc_attr( $current_year ); ?>"><?php echo esc_html( $current_year ); ?></time> <?php echo esc_html( $copyright_line ); ?></p>
		<div class="ft__social" aria-label="Profile">
			<a href="https://www.linkedin.com/in/hasim-%C3%BCner/" aria-label="LinkedIn-Profil" rel="me noopener noreferrer" target="_blank">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 2h-17A1.5 1.5 0 0 0 2 3.5v17A1.5 1.5 0 0 0 3.5 22h17a1.5 1.5 0 0 0 1.5-1.5v-17A1.5 1.5 0 0 0 20.5 2zM8 19H5v-9h3zM6.5 8.25A1.75 1.75 0 1 1 8.3 6.5a1.78 1.78 0 0 1-1.8 1.75zM19 19h-3v-4.74c0-1.42-.6-1.93-1.38-1.93A1.74 1.74 0 0 0 13 14.19V19h-3v-9h2.9v1.3a3.11 3.11 0 0 1 2.7-1.4c1.55 0 3.36.86 3.36 3.66z"/></svg>
				LinkedIn
			</a>
			<?php if ( $is_whitelabel_context ) : ?>
			<a href="https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514" aria-label="Öffentliches GitHub-Repository" rel="me noopener noreferrer" target="_blank" data-track-action="cta_footer_social_github" data-track-category="trust" data-track-section="footer">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 .5C5.65.5.5 5.65.5 12c0 5.08 3.29 9.39 7.86 10.91.58.1.79-.25.79-.56v-2.06c-3.2.7-3.87-1.36-3.87-1.36-.52-1.33-1.27-1.68-1.27-1.68-1.04-.71.08-.7.08-.7 1.15.08 1.76 1.18 1.76 1.18 1.02 1.75 2.68 1.25 3.34.96.1-.74.4-1.25.73-1.54-2.55-.29-5.24-1.28-5.24-5.69 0-1.26.45-2.29 1.18-3.1-.12-.29-.51-1.46.11-3.04 0 0 .97-.31 3.18 1.18.92-.26 1.91-.39 2.89-.39.98 0 1.97.13 2.89.39 2.21-1.49 3.18-1.18 3.18-1.18.62 1.58.23 2.75.11 3.04.74.81 1.18 1.84 1.18 3.1 0 4.42-2.69 5.39-5.25 5.68.41.35.78 1.04.78 2.1v3.11c0 .31.21.66.79.55C20.21 21.39 23.5 17.08 23.5 12 23.5 5.65 18.35.5 12 .5z"/></svg>
				GitHub
			</a>
			<?php endif; ?>
		</div>
	</div>
</footer>
