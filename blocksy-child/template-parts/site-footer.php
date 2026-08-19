<?php
/**
 * Global site footer.
 *
 * Header = focused decision paths. Footer = complete commercial, proof and
 * knowledge architecture, including SEO entry pages that do not need a global
 * header slot.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_year = wp_date( 'Y' );
$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$routes       = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$home_url     = $routes['home'] ?? ( $primary_urls['home'] ?? home_url( '/' ) );
$energy_url   = $routes['energy'] ?? ( $primary_urls['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' ) );
$analysis_url = $routes['marketcheck'] ?? ( function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ) );
$e3_url       = $primary_urls['e3'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$results_url  = $routes['results'] ?? ( $primary_urls['results'] ?? home_url( '/ergebnisse/' ) );
$blog_url     = $primary_urls['blog'] ?? home_url( '/blog/' );
$glossary_url = $primary_urls['glossary'] ?? home_url( '/glossar/' );
$agentur_url  = $routes['agentur_local'] ?? ( $primary_urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' ) );
$freelancer_url = $routes['freelancer'] ?? home_url( '/wordpress-freelancer-hannover/' );
$tracking_url   = $routes['tracking_b2b'] ?? home_url( '/server-side-tracking-b2b/' );
$about_url    = $routes['about'] ?? ( $primary_urls['about'] ?? home_url( '/hasim-uener/' ) );
$contact_url  = $routes['contact'] ?? ( $primary_urls['contact'] ?? nexus_get_contact_url() );
$project_request_url = $routes['project_request'] ?? ( function_exists( 'hu_get_navigation_project_request_url' )
	? hu_get_navigation_project_request_url()
	: add_query_arg(
		[
			'type'  => 'project',
			'focus' => 'implementation_scope',
		],
		$contact_url
	) );
$imprint_url      = $primary_urls['impressum'] ?? home_url( '/impressum/' );
$privacy_url      = $primary_urls['datenschutz'] ?? home_url( '/datenschutz/' );
$whitelabel_url   = $routes['whitelabel'] ?? ( function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' ) );
$hide_primary_cta = function_exists( 'nexus_should_hide_footer_primary_cta' ) && nexus_should_hide_footer_primary_cta();
$footer_class     = $hide_primary_cta ? 'ft ft--modern ft--no-primary-cta ft--mobile-cta' : 'ft ft--modern';
$request_url      = $project_request_url;
$audit_cta_label  = 'Projekt anfragen';
$audit_cta_microcopy = 'Scope · Ziel · nächster sinnvoller Schritt';
$audit_footer_note = function_exists( 'nexus_get_audit_footer_note' ) ? nexus_get_audit_footer_note() : 'Marktcheck: Manueller Fit-Befund statt Software-Einheitsbrei — händische Einordnung von Region, Vertrieb und Anfragequalität.';

$is_whitelabel_context = is_page_template( 'page-whitelabel-retainer.php' )
	|| is_page( 'whitelabel-retainer' )
	|| is_page( 'whitelabel-retainer-proof' )
	|| is_page( 'whitelabel' );

$is_freelancer_context = is_page_template( 'page-wordpress-freelancer-hannover.php' )
	|| is_page( 'wordpress-freelancer-hannover' );

if ( $is_whitelabel_context ) {
	$brand_tagline       = 'White-Label-Partner für Agenturen — SEO, WordPress, Tracking und Conversion. Unsichtbar im Hintergrund.';
	$copyright_line      = 'Haşim Üner - White-Label-Partner für Agenturen';
	$request_url         = $whitelabel_url . '#fit-check';
	$audit_cta_label     = 'Fit-Check starten';
	$audit_cta_microcopy = 'Agenturfit · Scope · Zusammenarbeit';
} elseif ( $is_freelancer_context ) {
	$brand_tagline       = 'WordPress Freelancer aus Hannover — Entwicklung, Tracking und Funnel direkt mit Haşim Üner.';
	$copyright_line      = 'Haşim Üner - WordPress Freelancer Hannover';
	$request_url         = $project_request_url;
	$audit_cta_label     = 'WordPress-Projekt prüfen lassen';
	$audit_cta_microcopy = 'Direkt mit mir · Scope und Preis vor Umsetzung';
} else {
	$brand_tagline  = 'WordPress, Tracking und Conversion als zusammenhängendes System — direkt mit Haşim Üner.';
	$copyright_line = 'Haşim Üner - WordPress, Tracking & Conversion';
}
?>

<?php if ( function_exists( 'nexus_is_audit_page' ) && nexus_is_audit_page() ) : ?>
<footer id="footer" class="ft ft--audit-minimal" aria-labelledby="ft-heading" role="contentinfo">
	<h2 id="ft-heading" class="ft__sr">Footer-Navigation</h2>
	<div class="ft__audit-shell">
		<p class="ft__audit-note"><?php echo esc_html( $audit_footer_note ); ?></p>
		<nav class="ft__audit-links" aria-label="Marktcheck-Footer-Navigation">
			<a href="<?php echo esc_url( $analysis_url ); ?>" data-track-action="cta_audit_footer_analysis" data-track-category="lead_gen">Marktcheck mit Fit-Entscheid starten</a>
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
		<a class="ft__cta" href="<?php echo esc_url( $analysis_url ); ?>" data-track-action="cta_energy_footer_analysis" data-track-category="lead_gen" data-track-section="footer_energy" data-track-funnel-stage="energy_footer">Marktcheck mit Fit-Entscheid starten</a>
		<nav class="ft__energy-legal" aria-label="Rechtliches">
			<a href="<?php echo esc_url( $imprint_url ); ?>">Impressum</a>
			<span aria-hidden="true">·</span>
			<a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutz</a>
		</nav>
		<div class="ft__social ft__social--energy" aria-label="Profile">
			<a href="https://www.linkedin.com/in/hasim-uener/" aria-label="LinkedIn-Profil" rel="me noopener noreferrer" target="_blank">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 2h-17A1.5 1.5 0 0 0 2 3.5v17A1.5 1.5 0 0 0 3.5 22h17a1.5 1.5 0 0 0 1.5-1.5v-17A1.5 1.5 0 0 0 20.5 2zM8 19H5v-9h3zM6.5 8.25A1.75 1.75 0 1 1 8.3 6.5a1.78 1.78 0 0 1-1.8 1.75zM19 19h-3v-4.74c0-1.42-.6-1.93-1.38-1.93A1.74 1.74 0 0 0 13 14.19V19h-3v-9h2.9v1.3a3.11 3.11 0 0 1 2.7-1.4c1.55 0 3.36.86 3.36 3.66z"/></svg>
				LinkedIn
			</a>
		</div>
	</div>
</footer>
<?php return; endif; ?>

<?php
/*
 * The large standard footer sits below the useful page content. Load its
 * dedicated stylesheet at that point instead of adding another render-blocking
 * global asset to the document head. Audit and Energy footers stay untouched.
 */
$footer_style_path = get_stylesheet_directory() . '/assets/css/site-footer-modern.css';
$footer_style_url  = get_stylesheet_directory_uri() . '/assets/css/site-footer-modern.css';
if ( file_exists( $footer_style_path ) ) {
	$footer_style_version = function_exists( 'hu_get_asset_version' )
		? hu_get_asset_version( $footer_style_path )
		: (string) filemtime( $footer_style_path );
	$footer_style_url = add_query_arg( 'ver', $footer_style_version, $footer_style_url );
}
?>
<link rel="stylesheet" id="nexus-site-footer-modern-css" href="<?php echo esc_url( $footer_style_url ); ?>" media="all">

<footer id="footer" class="<?php echo esc_attr( $footer_class ); ?>" aria-labelledby="ft-heading" role="contentinfo">
	<div class="ft-modern__inner">
		<div class="ft-modern__intro">
			<div>
				<p class="ft-modern__eyebrow">Drei Wege, ein technischer Partner</p>
				<h2 id="ft-heading" class="ft-modern__title">Was soll als Nächstes funktionieren?</h2>
			</div>
			<p class="ft-modern__intro-copy">Website, Tracking oder Conversion — wir starten dort, wo gerade der größte Hebel liegt.</p>
		</div>

		<nav class="ft-modern__routes" aria-label="Hauptwege">
			<a class="ft-modern__route" href="<?php echo esc_url( $freelancer_url ); ?>" data-track-action="cta_footer_route_freelancer" data-track-category="navigation" data-track-section="footer_routes">
				<span class="ft-modern__route-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M3 9h18M7 6.5h.01M10 6.5h.01"></path></svg>
				</span>
				<span class="ft-modern__route-label">WordPress</span>
				<span class="ft-modern__route-copy">Direkte Projekte · Entwicklung, Tracking &amp; Conversion</span>
				<span class="ft-modern__route-arrow" aria-hidden="true">↗</span>
			</a>

			<a class="ft-modern__route" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="cta_footer_route_whitelabel" data-track-category="navigation" data-track-section="footer_routes">
				<span class="ft-modern__route-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24"><path d="m12 3 8 5-8 5-8-5 8-5Z"></path><path d="m4 12 8 5 8-5M4 16l8 5 8-5"></path></svg>
				</span>
				<span class="ft-modern__route-label">Für Agenturen</span>
				<span class="ft-modern__route-copy">White-Label · Umsetzung im Hintergrund</span>
				<span class="ft-modern__route-arrow" aria-hidden="true">↗</span>
			</a>

			<a class="ft-modern__route" href="<?php echo esc_url( $energy_url ); ?>" data-track-action="cta_footer_route_energy" data-track-category="navigation" data-track-section="footer_routes">
				<span class="ft-modern__route-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3.5"></circle><path d="M12 2v2.5M12 19.5V22M2 12h2.5M19.5 12H22M4.9 4.9l1.8 1.8M17.3 17.3l1.8 1.8M19.1 4.9l-1.8 1.8M6.7 17.3l-1.8 1.8"></path></svg>
				</span>
				<span class="ft-modern__route-label">Solar &amp; Wärmepumpen</span>
				<span class="ft-modern__route-copy">Anfragesysteme · Marktcheck &amp; Leadgenerierung</span>
				<span class="ft-modern__route-arrow" aria-hidden="true">↗</span>
			</a>
		</nav>

		<?php if ( ! $hide_primary_cta ) : ?>
		<section class="ft-modern__cta-band" aria-label="Projektanfrage">
			<div>
				<p class="ft-modern__cta-kicker">Nächster Schritt</p>
				<h3 class="ft-modern__cta-title">Ein konkretes Projekt?</h3>
				<p class="ft-modern__cta-note"><?php echo esc_html( $audit_cta_microcopy ); ?></p>
			</div>
			<a class="ft-modern__cta-button" href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_footer_primary" data-track-category="lead_gen" data-track-section="footer" data-track-funnel-stage="footer_primary">
				<?php echo esc_html( $audit_cta_label ); ?> <span aria-hidden="true">↗</span>
			</a>
		</section>
		<?php else : ?>
		<section class="ft-modern__cta-band ft-modern__cta-band--mobile-only" aria-label="Projektanfrage">
			<div>
				<p class="ft-modern__cta-kicker">Nächster Schritt</p>
				<h3 class="ft-modern__cta-title">Ein konkretes Projekt?</h3>
				<p class="ft-modern__cta-note"><?php echo esc_html( $audit_cta_microcopy ); ?></p>
			</div>
			<a class="ft-modern__cta-button" href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_footer_primary_mobile" data-track-category="lead_gen" data-track-section="footer" data-track-funnel-stage="footer_mobile">
				<?php echo esc_html( $audit_cta_label ); ?> <span aria-hidden="true">↗</span>
			</a>
		</section>
		<?php endif; ?>

		<div class="ft-modern__directory">
			<div class="ft-modern__brand">
				<a class="ft-modern__logo site-logo" href="<?php echo esc_url( $home_url ); ?>" aria-label="Startseite - HAŞIM ÜNER">HAŞIM ÜNER</a>
				<p><?php echo esc_html( $brand_tagline ); ?></p>
			</div>

			<section class="ft-modern__col" aria-labelledby="ft-leistungen">
				<h3 id="ft-leistungen">Leistungen</h3>
				<ul class="ft-modern__list">
					<li><a href="<?php echo esc_url( $freelancer_url ); ?>" data-track-action="cta_footer_nav_freelancer" data-track-category="navigation" data-track-section="footer">WordPress Freelancer</a></li>
					<li><a href="<?php echo esc_url( $tracking_url ); ?>" data-track-action="cta_footer_nav_tracking" data-track-category="navigation" data-track-section="footer">Server-Side Tracking B2B</a></li>
					<li><a href="<?php echo esc_url( $energy_url ); ?>" data-track-action="cta_footer_nav_energy" data-track-category="navigation" data-track-section="footer">Solar &amp; Wärmepumpen</a></li>
					<li><a href="<?php echo esc_url( $agentur_url ); ?>" data-track-action="cta_footer_nav_agentur" data-track-category="navigation" data-track-section="footer">WordPress Agentur Hannover</a></li>
				</ul>
			</section>

			<section class="ft-modern__col" aria-labelledby="ft-proof-wissen">
				<h3 id="ft-proof-wissen">Nachweise &amp; Wissen</h3>
				<ul class="ft-modern__list">
					<li><a href="<?php echo esc_url( $results_url ); ?>" data-track-action="cta_footer_nav_results" data-track-category="trust" data-track-section="footer">Ergebnisse &amp; Case Studies</a></li>
					<li><a href="<?php echo esc_url( $e3_url ); ?>" data-track-action="cta_footer_nav_case_study_proof" data-track-category="trust" data-track-section="footer">Fallstudie: Solar Leadgenerierung</a></li>
					<li><a href="<?php echo esc_url( $blog_url ); ?>" data-track-action="cta_footer_nav_insights" data-track-category="navigation" data-track-section="footer">Insights</a></li>
					<li><a href="<?php echo esc_url( $glossary_url ); ?>" data-track-action="cta_footer_nav_glossary" data-track-category="navigation" data-track-section="footer">Glossar für SEO, Tracking und Anfragesysteme</a></li>
				</ul>
			</section>

			<section class="ft-modern__col" aria-labelledby="ft-zusammenarbeit">
				<h3 id="ft-zusammenarbeit">Zusammenarbeit</h3>
				<ul class="ft-modern__list">
					<li><a href="<?php echo esc_url( $project_request_url ); ?>" data-track-action="cta_footer_nav_project" data-track-category="lead_gen" data-track-section="footer">Projekt anfragen</a></li>
					<li><a href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="cta_footer_nav_whitelabel" data-track-category="lead_gen" data-track-section="footer">Für Agenturen: White-Label</a></li>
					<li><a href="<?php echo esc_url( $about_url ); ?>" data-track-action="cta_footer_nav_about" data-track-category="navigation" data-track-section="footer">Über Haşim</a></li>
					<li><a href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_footer_nav_contact" data-track-category="navigation" data-track-section="footer">Direktkontakt</a></li>
				</ul>
			</section>
		</div>

		<div class="ft-modern__bottom">
			<p>&copy; <time class="ft__year" datetime="<?php echo esc_attr( $current_year ); ?>"><?php echo esc_html( $current_year ); ?></time> <?php echo esc_html( $copyright_line ); ?></p>

			<nav class="ft-modern__meta" aria-label="Rechtliches">
				<a href="<?php echo esc_url( $imprint_url ); ?>">Impressum</a>
				<span aria-hidden="true">·</span>
				<a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutz</a>
			</nav>

			<div class="ft-modern__social" aria-label="Profile">
				<a href="https://www.linkedin.com/in/hasim-uener/" aria-label="LinkedIn-Profil" rel="me noopener noreferrer" target="_blank">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 2h-17A1.5 1.5 0 0 0 2 3.5v17A1.5 1.5 0 0 0 3.5 22h17a1.5 1.5 0 0 0 1.5-1.5v-17A1.5 1.5 0 0 0 20.5 2zM8 19H5v-9h3zM6.5 8.25A1.75 1.75 0 1 1 8.3 6.5a1.78 1.78 0 0 1-1.8 1.75zM19 19h-3v-4.74c0-1.42-.6-1.93-1.38-1.93A1.74 1.74 0 0 0 13 14.19V19h-3v-9h2.9v1.3a3.11 3.11 0 0 1 2.7-1.4c1.55 0 3.36.86 3.36 3.66z"/></svg>
					LinkedIn
				</a>
				<?php if ( $is_whitelabel_context || $is_freelancer_context ) : ?>
				<a href="https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514" aria-label="Öffentliches GitHub-Repository" rel="me noopener noreferrer" target="_blank" data-track-action="cta_footer_social_github" data-track-category="trust" data-track-section="footer">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 .5C5.65.5.5 5.65.5 12c0 5.08 3.29 9.39 7.86 10.91.58.1.79-.25.79-.56v-2.06c-3.2.7-3.87-1.36-3.87-1.36-.52-1.33-1.27-1.68-1.27-1.68-1.04-.71.08-.7.08-.7 1.15.08 1.76 1.18 1.76 1.18 1.02 1.75 2.68 1.25 3.34.96.1-.74.4-1.25.73-1.54-2.55-.29-5.24-1.28-5.24-5.69 0-1.26.45-2.29 1.18-3.1-.12-.29-.51-1.46.11-3.04 0 0 .97-.31 3.18 1.18.92-.26 1.91-.39 2.89-.39.98 0 1.97.13 2.89.39 2.21-1.49 3.18-1.18 3.18-1.18.62 1.58.23 2.75.11 3.04.74.81 1.18 1.84 1.18 3.1 0 4.42-2.69 5.39-5.25 5.68.41.35.78 1.04.78 2.1v3.11c0 .31.21.66.79.55C20.21 21.39 23.5 17.08 23.5 12 23.5 5.65 18.35.5 12 .5z"/></svg>
					GitHub
				</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</footer>
