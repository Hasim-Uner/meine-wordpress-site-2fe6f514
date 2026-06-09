<?php
/**
 * NEXUS SMART NAV BUTTON
 * Shortcode: [nexus_header_btn]
 * Zeigt "Login" oder "Cockpit" je nach Status.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_shortcode( 'nexus_header_btn', function() {
    $portal_page = get_page_by_path( 'portal' );
    $link = $portal_page ? get_permalink( $portal_page ) : home_url( '/portal' );
    $is_preview = is_customize_preview();

    if ( is_user_logged_in() && ! $is_preview ) {
        return '<a href="' . esc_url( $link ) . '" class="nexus-nav-btn active">Zum Cockpit <span class="indicator">●</span></a>';
    }

    return '<a href="' . esc_url( $link ) . '" class="nexus-nav-btn">Kunden-Login 🔒</a>';
} );

/**
 * NEXUS HEADER CTA
 * Shortcode: [nexus_header_cta]
 * Gold-Button für die Hauptnavigation.
 */
add_shortcode( 'nexus_header_cta', function() {
    $request_url = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );

    return '<a href="' . esc_url( $request_url ) . '" class="nexus-header-cta">Marktcheck mit Fit-Entscheid starten</a>';
} );

/**
 * Legacy-Kompatibilitaet fuer alten Theme-Toggle-Shortcode.
 *
 * Das Frontend ist fest auf Dark gesetzt; der Shortcode bleibt als no-op,
 * damit Altinhalte kein sichtbares Raw-Shortcode-Fragment rendern.
 */
add_shortcode( 'nexus_theme_toggle', function() {
    return '';
} );

/**
 * Redirect default wp-login.php view to the portal page.
 */
add_action( 'login_init', function() {
    $action = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : 'login';
    $bypass_actions = [ 'logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp' ];

    if ( in_array( $action, $bypass_actions, true ) ) {
        return;
    }

    $portal_page = get_page_by_path( 'portal' );
    if ( ! $portal_page ) {
        return;
    }

    wp_safe_redirect( get_permalink( $portal_page ) );
    exit;
} );

/* =========================================
   NEXUS SECURITY: HIDE ADMIN FOR CLIENTS
   ========================================= */

// 1. Admin-Leiste (schwarzer Balken) für Kunden ausblenden
add_action( 'after_setup_theme', function() {
    if ( ! current_user_can( 'manage_options' ) ) {
        show_admin_bar( false );
    }
} );

add_filter( 'show_admin_bar', function( $show ) {
    if ( is_page_template( 'template-portal.php' ) ) {
        return false;
    }
    if ( ! current_user_can( 'manage_options' ) ) {
        return false;
    }
    return $show;
}, 999 );

// 2. Zugriff auf /wp-admin blockieren
add_action( 'admin_init', function() {
    if ( ! is_user_logged_in() || current_user_can( 'manage_options' ) || wp_doing_ajax() ) {
        return;
    }

    $portal_page = get_page_by_path( 'portal' );
    $target = $portal_page ? get_permalink( $portal_page ) : home_url( '/portal' );

    wp_safe_redirect( $target );
    exit;
} );

// 3. Login-Redirect korrigieren (Falls WP-Login genutzt wird)
add_filter( 'login_redirect', function( $redirect_to, $request, $user ) {
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        if ( ! in_array( 'administrator', $user->roles, true ) ) {
            $portal_page = get_page_by_path( 'portal' );
            return $portal_page ? get_permalink( $portal_page ) : home_url( '/portal' );
        }
    }
    return $redirect_to;
}, 10, 3 );

// Legacy route redirects and retired 410 paths live in inc/helpers.php:
// nexus_get_legacy_offer_redirect_map() and nexus_get_retired_gone_paths().
