<?php
/**
 * Noindex homepage test route for the visual "wow" variant.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the canonical request path for the homepage test route.
 *
 * @return string
 */
function hu_get_homepage_wow_request_path() {
	return trailingslashit( '/' . ltrim( (string) wp_parse_url( home_url( '/startseite-wow/' ), PHP_URL_PATH ), '/' ) );
}

/**
 * Check whether the current request targets the homepage test route.
 *
 * @return bool
 */
function hu_is_homepage_wow_request() {
	return function_exists( 'nexus_get_current_request_path' )
		&& nexus_get_current_request_path() === hu_get_homepage_wow_request_path();
}

/**
 * Prevent canonical redirects from fighting the virtual test route.
 *
 * @param string|false $redirect_url Redirect target.
 * @return string|false
 */
function hu_disable_canonical_redirect_for_homepage_wow( $redirect_url ) {
	if ( hu_is_homepage_wow_request() ) {
		return false;
	}

	return $redirect_url;
}
add_filter( 'redirect_canonical', 'hu_disable_canonical_redirect_for_homepage_wow' );

/**
 * Turn /startseite-wow/ into a virtual page without requiring an editor page.
 *
 * @param bool     $preempt Existing preempt flag.
 * @param WP_Query $wp_query Current query.
 * @return bool
 */
function hu_preempt_homepage_wow_404( $preempt, $wp_query ) {
	if ( is_admin() || wp_doing_ajax() || ! ( $wp_query instanceof WP_Query ) || ! hu_is_homepage_wow_request() ) {
		return $preempt;
	}

	$wp_query->is_404             = false;
	$wp_query->is_page            = true;
	$wp_query->is_singular        = true;
	$wp_query->is_home            = false;
	$wp_query->is_archive         = false;
	$wp_query->is_posts_page      = false;
	$wp_query->queried_object     = null;
	$wp_query->queried_object_id  = 0;
	$wp_query->query_vars['pagename'] = 'startseite-wow';
	unset( $wp_query->query['error'], $wp_query->query_vars['error'] );

	status_header( 200 );

	return true;
}
add_filter( 'pre_handle_404', 'hu_preempt_homepage_wow_404', 10, 2 );

/**
 * Use the visual homepage test template for the virtual route.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function hu_use_homepage_wow_template( $template ) {
	if ( ! hu_is_homepage_wow_request() ) {
		return $template;
	}

	$virtual_template = get_stylesheet_directory() . '/page-startseite-wow.php';

	if ( file_exists( $virtual_template ) ) {
		return $virtual_template;
	}

	return $template;
}
add_filter( 'template_include', 'hu_use_homepage_wow_template', 99 );

/**
 * Enqueue the scoped assets for the visual homepage test route.
 *
 * @return void
 */
function hu_enqueue_homepage_wow_assets() {
	if ( ! hu_is_homepage_wow_request() ) {
		return;
	}

	hu_enqueue_css( 'nexus-home-redesign-css', 'homepage-redesign.css', [ 'nexus-design-system' ] );
	hu_enqueue_css( 'nexus-homepage-wow-css', 'homepage-wow.css', [ 'nexus-home-redesign-css' ] );
	hu_enqueue_js( 'nexus-homepage-wow-js', 'homepage-wow.js', [ 'nexus-core-js' ] );
}
add_action( 'wp_enqueue_scripts', 'hu_enqueue_homepage_wow_assets', 30 );

/**
 * Remove block-editor CSS on the fully versioned test route.
 *
 * @return void
 */
function hu_dequeue_block_styles_on_homepage_wow() {
	if ( ! hu_is_homepage_wow_request() ) {
		return;
	}

	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
}
add_action( 'wp_enqueue_scripts', 'hu_dequeue_block_styles_on_homepage_wow', 101 );

/**
 * Add body classes for route-level styling and debugging.
 *
 * @param array<int, string> $classes Existing body classes.
 * @return array
 */
function hu_add_homepage_wow_body_class( $classes ) {
	if ( ! hu_is_homepage_wow_request() ) {
		return $classes;
	}

	$classes   = array_diff( $classes, [ 'error404' ] );
	$classes[] = 'page';
	$classes[] = 'page-startseite-wow';
	$classes[] = 'homepage-wow-test';

	return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'hu_add_homepage_wow_body_class', 20 );

/**
 * Output route-specific noindex metadata and avoid duplicate default meta.
 *
 * @return void
 */
function hu_setup_homepage_wow_meta() {
	if ( ! hu_is_homepage_wow_request() ) {
		return;
	}

	remove_action( 'wp_head', 'hu_seo_meta_tags', 1 );
	add_action( 'wp_head', 'hu_output_homepage_wow_meta', 1 );
}
add_action( 'wp', 'hu_setup_homepage_wow_meta', 1 );

/**
 * Print minimal route-specific meta tags for the noindex test page.
 *
 * @return void
 */
function hu_output_homepage_wow_meta() {
	$title       = 'Startseiten-Variante | Haşim Üner';
	$description = 'Noindex-Testseite fuer eine visuell staerkere Homepage-Variante des Anfragesystems fuer Solar- und Waermepumpen-Anbieter.';
	$canonical   = home_url( '/startseite-wow/' );

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );
	echo '<meta name="robots" content="noindex,nofollow">' . "\n";
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $canonical ) );
	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta property="og:locale" content="de_DE">' . "\n";
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	echo '<meta name="twitter:card" content="summary">' . "\n";
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );
}

/**
 * Send an HTTP noindex header for crawlers that respect X-Robots-Tag.
 *
 * @return void
 */
function hu_send_homepage_wow_robots_header() {
	if ( ! is_admin() && ! wp_doing_ajax() && hu_is_homepage_wow_request() ) {
		header( 'X-Robots-Tag: noindex, nofollow', true );
	}
}
add_action( 'send_headers', 'hu_send_homepage_wow_robots_header' );

/**
 * Override the document title for the test route.
 *
 * @param string $title Current document title.
 * @return string
 */
function hu_homepage_wow_document_title( $title ) {
	if ( hu_is_homepage_wow_request() ) {
		return 'Startseiten-Variante | Haşim Üner';
	}

	return $title;
}
add_filter( 'pre_get_document_title', 'hu_homepage_wow_document_title', 99 );

/**
 * Production-safe homepage hero visual.
 *
 * This intentionally ships inline in the rendered HTML. Production asset
 * versions are static on this project, so an external JS file can remain in
 * browser/CDN cache after a deploy. The inline fallback replaces any stale
 * image node with a native SVG system graphic.
 *
 * @return void
 */
function hu_output_inline_home_system_visual() {
	if ( ! is_front_page() || is_admin() ) {
		return;
	}
	?>
	<style id="hu-home-system-inline-css">
		.startseite .home-hero::before{content:none!important;display:none!important}
		.startseite .home-system-visual{grid-column:2;grid-row:1;position:relative;z-index:1;width:min(100%,760px);aspect-ratio:1200/760;align-self:center;justify-self:center;perspective:1200px;transform-style:preserve-3d;filter:drop-shadow(0 28px 55px color-mix(in srgb,var(--tinte) 10%,transparent));transition:transform .18s ease-out}
		.startseite .home-system-visual svg{display:block;width:100%;height:100%;overflow:visible}
		.startseite .home-system-visual .sys-shell{fill:color-mix(in srgb,var(--papier) 96%,var(--stempel) 4%);stroke:var(--haar);stroke-width:1.2}
		.startseite .home-system-visual .sys-grid{stroke:color-mix(in srgb,var(--tinte) 7%,transparent);stroke-width:1}
		.startseite .home-system-visual .sys-card{fill:var(--papier);stroke:color-mix(in srgb,var(--tinte) 16%,transparent);stroke-width:1.2;transform-box:fill-box;transform-origin:center}
		.startseite .home-system-visual .sys-card--accent{fill:color-mix(in srgb,var(--stempel) 8%,var(--papier));stroke:color-mix(in srgb,var(--stempel) 55%,transparent)}
		.startseite .home-system-visual .sys-label{font-family:var(--mono);font-size:18px;letter-spacing:.16em;fill:var(--grau)}
		.startseite .home-system-visual .sys-title{font-family:var(--sans);font-size:31px;font-weight:650;fill:var(--tinte)}
		.startseite .home-system-visual .sys-small{font-family:var(--mono);font-size:15px;letter-spacing:.08em;fill:var(--grau)}
		.startseite .home-system-visual .sys-copper{fill:var(--stempel)}
		.startseite .home-system-visual .sys-flow-base{fill:none;stroke:color-mix(in srgb,var(--tinte) 15%,transparent);stroke-width:2}
		.startseite .home-system-visual .sys-flow{fill:none;stroke:var(--stempel);stroke-width:3;stroke-linecap:round;stroke-dasharray:16 18}
		.startseite .home-system-visual .sys-orbit{fill:none;stroke:color-mix(in srgb,var(--stempel) 28%,transparent);stroke-width:1.6;stroke-dasharray:4 11;transform-box:fill-box;transform-origin:center}
		.startseite .home-system-visual .sys-node{fill:var(--papier);stroke:var(--stempel);stroke-width:3}
		.startseite .home-system-visual .sys-core{fill:var(--stempel);transform-box:fill-box;transform-origin:center}
		.startseite .home-system-visual .sys-chip{fill:color-mix(in srgb,var(--tinte) 5%,var(--papier));stroke:color-mix(in srgb,var(--tinte) 12%,transparent);stroke-width:1}
		.startseite .home-system-visual .sys-bars rect{fill:color-mix(in srgb,var(--stempel) 72%,var(--papier))}
		.startseite .home-system-visual .sys-live{fill:var(--stempel)}
		.startseite .home-system-visual::after{content:"";position:absolute;inset:5% 2%;pointer-events:none;background:linear-gradient(108deg,transparent 28%,color-mix(in srgb,var(--stempel) 12%,transparent) 48%,transparent 67%);transform:translateX(-115%);mix-blend-mode:multiply}
		.startseite .home-portrait{z-index:4}
		@media(max-width:820px){.startseite .home-system-visual{grid-column:1;grid-row:2;width:min(100%,44rem);margin-top:var(--s1)}.startseite .home-portrait{grid-row:3}}
		@media(max-width:520px){.startseite .home-system-visual{width:calc(100% + var(--s2));margin-inline:calc(var(--s1) * -1)}}
		@media(prefers-reduced-motion:no-preference){.startseite .home-system-visual .sys-flow{animation:huSysDash 3.2s linear infinite}.startseite .home-system-visual .sys-orbit{animation:huSysOrbit 18s linear infinite}.startseite .home-system-visual .sys-core{animation:huSysPulse 2.5s ease-in-out infinite}.startseite .home-system-visual .sys-card--one{animation:huSysFloatA 6s ease-in-out infinite}.startseite .home-system-visual .sys-card--two{animation:huSysFloatB 7.4s ease-in-out infinite}.startseite .home-system-visual .sys-live{animation:huSysLive 1.7s ease-in-out infinite}.startseite .home-system-visual::after{animation:huSysSheen 8s ease-in-out infinite}}
		@keyframes huSysDash{to{stroke-dashoffset:-68}}
		@keyframes huSysOrbit{to{transform:rotate(360deg)}}
		@keyframes huSysPulse{0%,100%{transform:scale(.82);opacity:.72}50%{transform:scale(1.22);opacity:1}}
		@keyframes huSysFloatA{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
		@keyframes huSysFloatB{0%,100%{transform:translateY(0)}50%{transform:translateY(8px)}}
		@keyframes huSysLive{0%,100%{opacity:.38}50%{opacity:1}}
		@keyframes huSysSheen{0%,55%{transform:translateX(-115%)}78%,100%{transform:translateX(115%)}}
	</style>
	<script id="hu-home-system-inline-js">
	(function(){
		'use strict';
		var hero=document.querySelector('.startseite .home-hero');
		if(!hero){return;}
		hero.querySelectorAll('.home-system-visual').forEach(function(node){node.remove();});
		var visual=document.createElement('div');
		visual.className='home-system-visual';
		visual.setAttribute('aria-hidden','true');
		visual.innerHTML='<svg viewBox="0 0 1200 760" focusable="false" aria-hidden="true">'+
		'<defs><radialGradient id="huSysHalo"><stop offset="0" stop-color="var(--stempel)" stop-opacity=".16"/><stop offset="1" stop-color="var(--stempel)" stop-opacity="0"/></radialGradient><filter id="huSysShadow" x="-30%" y="-30%" width="160%" height="160%"><feDropShadow dx="0" dy="18" stdDeviation="18" flood-color="#000" flood-opacity=".08"/></filter></defs>'+
		'<rect class="sys-shell" x="32" y="34" width="1136" height="676" rx="34"/>'+
		'<g opacity=".9"><path class="sys-grid" d="M92 142H1108M92 254H1108M92 366H1108M92 478H1108M92 590H1108"/><path class="sys-grid" d="M218 92V650M420 92V650M622 92V650M824 92V650M1026 92V650"/></g>'+
		'<ellipse cx="626" cy="370" rx="278" ry="238" fill="url(#huSysHalo)"/>'+
		'<g filter="url(#huSysShadow)"><g class="sys-card--one"><rect class="sys-card" x="88" y="146" width="310" height="220" rx="24"/><text class="sys-label" x="122" y="190">01 · WEBSITE</text><rect class="sys-chip" x="122" y="220" width="236" height="22" rx="11"/><rect class="sys-chip" x="122" y="258" width="188" height="15" rx="7.5"/><rect class="sys-chip" x="122" y="288" width="216" height="15" rx="7.5"/><rect class="sys-card--accent" x="122" y="326" width="112" height="18" rx="9"/></g><g class="sys-card--two"><rect class="sys-card" x="812" y="397" width="300" height="206" rx="24"/><text class="sys-label" x="846" y="441">04 · CRM</text><text class="sys-title" x="846" y="492">Lead erkannt.</text><text class="sys-small" x="846" y="526">Quelle · Intent · Status</text><circle class="sys-live" cx="858" cy="562" r="7"/><text class="sys-small" x="878" y="568">LIVE HANDOFF</text></g></g>'+
		'<path class="sys-flow-base" d="M398 257 C478 257 471 330 548 345 S715 334 782 286 S870 282 918 397"/><path class="sys-flow" d="M398 257 C478 257 471 330 548 345 S715 334 782 286 S870 282 918 397"/>'+
		'<circle class="sys-orbit" cx="631" cy="350" r="145"/><circle class="sys-orbit" cx="631" cy="350" r="108" opacity=".7"/><circle class="sys-node" cx="631" cy="350" r="66"/><circle class="sys-core" cx="631" cy="350" r="16"/><text class="sys-label" text-anchor="middle" x="631" y="315">02 · TRACKING</text><text class="sys-small" text-anchor="middle" x="631" y="398">SIGNAL VERKNÜPFT</text>'+
		'<rect class="sys-chip" x="486" y="480" width="86" height="42" rx="14"/><text class="sys-small" text-anchor="middle" x="529" y="507">GA4</text><rect class="sys-chip" x="587" y="480" width="86" height="42" rx="14"/><text class="sys-small" text-anchor="middle" x="630" y="507">GTM</text><rect class="sys-chip" x="688" y="480" width="98" height="42" rx="14"/><text class="sys-small" text-anchor="middle" x="737" y="507">SERVER</text>'+
		'<rect class="sys-card--accent" x="794" y="154" width="252" height="142" rx="22"/><text class="sys-label" x="826" y="194">03 · ANFRAGE</text><text class="sys-title" x="826" y="240">qualifiziert</text><g class="sys-bars"><rect x="826" y="261" width="44" height="11" rx="5.5"/><rect x="880" y="261" width="74" height="11" rx="5.5"/><rect x="965" y="261" width="46" height="11" rx="5.5"/></g>'+
		'<circle class="sys-copper" cx="442" cy="267" r="5"/><circle class="sys-copper" cx="492" cy="316" r="5"/><circle class="sys-copper" cx="748" cy="305" r="5"/><circle class="sys-copper" cx="850" cy="310" r="5"/><text class="sys-small" x="90" y="675">WEBSITE → SIGNAL → QUALIFIZIERUNG → VERTRIEB</text></svg>';
		var portrait=hero.querySelector('.home-portrait');
		if(portrait){hero.insertBefore(visual,portrait);}else{hero.appendChild(visual);}
		var reduced=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		if(!reduced&&window.innerWidth>820){
			visual.addEventListener('pointermove',function(event){var rect=visual.getBoundingClientRect();var px=(event.clientX-rect.left)/rect.width-.5;var py=(event.clientY-rect.top)/rect.height-.5;visual.style.transform='rotateX('+(-py*4.5).toFixed(2)+'deg) rotateY('+(px*5.5).toFixed(2)+'deg) translate3d('+(px*8).toFixed(1)+'px,'+(py*6).toFixed(1)+'px,0)';});
			visual.addEventListener('pointerleave',function(){visual.style.transform='';});
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'hu_output_inline_home_system_visual', 100 );
