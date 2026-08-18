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
 * Resolve the generic project-request destination used outside the dedicated
 * Solar and White-Label funnels. Those two routes keep their own local CTAs.
 *
 * @return string
 */
function hu_get_navigation_project_request_url() {
    $contact_url = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' );

    return add_query_arg(
        [
            'type'  => 'project',
            'focus' => 'followup_scope',
        ],
        $contact_url
    );
}

/**
 * NEXUS HEADER CTA
 * Shortcode: [nexus_header_cta]
 * Gold-Button für die allgemeine Hauptnavigation.
 */
add_shortcode( 'nexus_header_cta', function() {
    return '<a href="' . esc_url( hu_get_navigation_project_request_url() ) . '" class="nexus-header-cta">Projekt anfragen</a>';
} );

/**
 * Build one deterministic primary-navigation item.
 *
 * The stored WordPress menu is intentionally treated as persistence only. The
 * public header has a strategic contract: audience routes first, proof/person
 * second, one generic project CTA last. This prevents old editor state from
 * reintroducing the former "WordPress Agentur" header route.
 *
 * @param string   $title   Public link label.
 * @param string   $url     Public destination.
 * @param string[] $classes Additional menu classes.
 * @param bool     $current Whether this route is the current page.
 * @return object
 */
function hu_make_strategy_nav_item( $title, $url, $classes = [], $current = false ) {
    $classes = array_values( array_unique( array_merge( [ 'menu-item' ], $classes ) ) );

    if ( $current ) {
        $classes[] = 'current-menu-item';
        $classes[] = 'current_page_item';
    }

    return (object) [
        'ID'                    => 0,
        'db_id'                 => 0,
        'menu_item_parent'      => 0,
        'object_id'             => 0,
        'object'                => 'custom',
        'type'                  => 'custom',
        'type_label'            => 'Custom Link',
        'title'                 => $title,
        'url'                   => $url,
        'target'                => '',
        'attr_title'            => '',
        'description'           => '',
        'xfn'                   => '',
        'classes'               => $classes,
        'current'               => $current,
        'current_item_ancestor' => false,
        'current_item_parent'   => false,
        'menu_order'            => 0,
        'post_type'             => 'nav_menu_item',
        'post_status'           => 'publish',
    ];
}

/**
 * Canonical public header architecture.
 *
 * Header = business/navigation priorities, not the complete SEO inventory.
 * `/wordpress-agentur-hannover/` remains indexable and internally linked, but
 * its local search intent no longer occupies a global navigation slot.
 *
 * @param array           $items Stored menu items.
 * @param stdClass|string $args  Menu arguments.
 * @return array
 */
function hu_normalize_primary_strategy_navigation( $items, $args ) {
    if (
        is_admin()
        || ! function_exists( 'nexus_is_primary_header_menu_args' )
        || ! nexus_is_primary_header_menu_args( $args )
    ) {
        return $items;
    }

    $primary_urls   = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
    $solar_url      = $primary_urls['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' );
    $freelancer_url = home_url( '/wordpress-freelancer-hannover/' );
    $whitelabel_url = function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' );
    $results_url    = function_exists( 'nexus_get_results_url' ) ? nexus_get_results_url() : ( $primary_urls['results'] ?? home_url( '/ergebnisse/' ) );
    $about_url      = $primary_urls['about'] ?? home_url( '/hasim-uener/' );
    $project_url    = hu_get_navigation_project_request_url();

    $is_solar      = function_exists( 'nexus_is_energy_systems_context' ) && nexus_is_energy_systems_context();
    $is_freelancer = is_page( 'wordpress-freelancer-hannover' ) || is_page_template( 'page-wordpress-freelancer-hannover.php' );
    $is_whitelabel = function_exists( 'nexus_is_agency_nav_context' ) && nexus_is_agency_nav_context();
    $is_results    = function_exists( 'nexus_is_results_context' ) && nexus_is_results_context();
    $is_about      = is_page( 'hasim-uener' ) || is_page( 'uber-mich' ) || is_page_template( 'page-hasim-uener.php' );

    return [
        hu_make_strategy_nav_item( __( 'Solar & Wärmepumpen', 'blocksy-child' ), $solar_url, [ 'nav-solar-link' ], $is_solar ),
        hu_make_strategy_nav_item( __( 'WordPress Freelancer', 'blocksy-child' ), $freelancer_url, [ 'nav-freelancer-link' ], $is_freelancer ),
        hu_make_strategy_nav_item( __( 'Für Agenturen', 'blocksy-child' ), $whitelabel_url, [ 'nav-agency-link' ], $is_whitelabel ),
        hu_make_strategy_nav_item( __( 'Ergebnisse', 'blocksy-child' ), $results_url, [ 'nav-results-link' ], $is_results ),
        hu_make_strategy_nav_item( __( 'Über Haşim', 'blocksy-child' ), $about_url, [ 'nav-about-link' ], $is_about ),
        hu_make_strategy_nav_item( __( 'Projekt anfragen', 'blocksy-child' ), $project_url, [ 'nav-cta-button', 'nav-project-link' ], false ),
    ];
}
add_filter( 'wp_nav_menu_objects', 'hu_normalize_primary_strategy_navigation', 90, 2 );

/**
 * Keep navigation analytics aligned with the canonical public labels.
 *
 * @param array    $atts Link attributes.
 * @param WP_Post  $item Menu item.
 * @param stdClass $args Menu arguments.
 * @return array
 */
function hu_strategy_nav_tracking_attributes( $atts, $item, $args ) {
    if (
        ! function_exists( 'nexus_is_primary_header_menu_args' )
        || ! nexus_is_primary_header_menu_args( $args )
        || ! isset( $item->classes )
        || ! is_array( $item->classes )
    ) {
        return $atts;
    }

    $class_map = [
        'nav-solar-link'      => [ 'nav_header_solar', 'navigation' ],
        'nav-freelancer-link' => [ 'nav_header_freelancer', 'navigation' ],
        'nav-agency-link'     => [ 'nav_header_whitelabel', 'navigation' ],
        'nav-results-link'    => [ 'nav_header_results', 'navigation' ],
        'nav-about-link'      => [ 'nav_header_about', 'navigation' ],
        'nav-project-link'    => [ 'nav_header_project', 'lead_gen' ],
    ];

    foreach ( $class_map as $class_name => $tracking ) {
        if ( ! in_array( $class_name, $item->classes, true ) ) {
            continue;
        }

        $atts['data-track-action']   = $tracking[0];
        $atts['data-track-category'] = $tracking[1];
        break;
    }

    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'hu_strategy_nav_tracking_attributes', 90, 3 );

/**
 * Give the local Agentur SEO page an explicit route to direct freelancer work.
 *
 * The link sits directly below the global header and above the Agentur hero:
 * visible enough to correct intent, small enough not to dilute the page's
 * primary "WordPress Agentur Hannover" search focus.
 */
add_action( 'wp_enqueue_scripts', function() {
    if ( ! is_page( 'wordpress-agentur-hannover' ) && ! is_page_template( 'page-wordpress-agentur.php' ) ) {
        return;
    }

    $asset_path = get_stylesheet_directory() . '/assets/css/agentur-route-switch.css';
    $asset_url  = get_stylesheet_directory_uri() . '/assets/css/agentur-route-switch.css';
    $asset_ver  = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $asset_path ) : wp_get_theme()->get( 'Version' );

    wp_enqueue_style( 'hu-agentur-route-switch', $asset_url, [], $asset_ver );
}, 30 );

add_action( 'wp_body_open', function() {
    if ( ! is_page( 'wordpress-agentur-hannover' ) && ! is_page_template( 'page-wordpress-agentur.php' ) ) {
        return;
    }
    ?>
    <aside class="hu-route-switch" aria-label="Passender WordPress-Einstieg">
        <div class="nx-container hu-route-switch__inner">
            <span>Direkte Zusammenarbeit statt Agentur-Setup?</span>
            <a href="<?php echo esc_url( home_url( '/wordpress-freelancer-hannover/' ) ); ?>" data-track-action="link_agentur_to_freelancer" data-track-category="navigation" data-track-section="intent_switch">WordPress Freelancer Hannover →</a>
        </div>
    </aside>
    <?php
}, 25 );

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