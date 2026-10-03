<?php
/**
 * One-time retirement of the former asset system. Never registers its CPT.
 * Run only after the replacement glossary has synced successfully.
 *
 * @package Blocksy_Child
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HU_LEGACY_SYSTEM_RETIREMENT_VERSION = '2026-10-03-1';

/** @return array<string, string> Exact replacements; an empty target means Gone. */
function hu_get_retired_system_routes() {
	return [
		'/wgos/' => '',
		'/wordpress-growth-operating-system/' => '',
		'/wgos-assets/' => '',
		'/wgos-systemlandkarte/' => '',
		'/wgos-asset-hub/' => '',
		'/systemlandkarte/' => '',
		'/wgos-assets/growth-audit/' => '',
		'/wgos-assets/system-diagnose/' => '',
		'/wgos-assets/positionierungs-check/' => '/glossar/positionierung/',
		'/wgos-assets/seitenrollen-mapping/' => '/glossar/seitenrollen/',
		'/wgos-assets/wettbewerbs-analyse/' => '/glossar/wettbewerbsanalyse/',
		'/wgos-assets/keyword-strategie/' => '/glossar/keyword-strategie/',
		'/wgos-assets/pillar-page/' => '/glossar/pillar-page/',
		'/wgos-assets/content-hub/' => '/glossar/content-hub/',
		'/wgos-assets/interne-verlinkung/' => '/glossar/interne-verlinkung/',
		'/wgos-assets/schema-markup/' => '/glossar/strukturierte-daten/',
		'/wgos-assets/social-proof/' => '/glossar/social-proof/',
		'/wgos-assets/lead-magnet/' => '/glossar/lead-magnet/',
		'/wgos-assets/utm-framework/' => '/glossar/utm-parameter/',
		'/wgos-assets/consent-mode-v2/' => '/glossar/consent-mode/',
		'/wgos-assets/conversion-testing/' => '/glossar/ab-test/',
		'/wgos-assets/ki-lead-qualifizierung/' => '/glossar/lead-qualifizierung/',
		'/wgos-assets/server-side-tracking/' => '/server-side-tracking-b2b/',
		'/wgos-assets/server-side-tracking-sgtm-matomo/' => '/server-side-tracking-b2b/',
		'/wgos-assets/tracking-audit/' => '/ga4-tracking-setup/',
		'/wgos-assets/ga4-event-blueprint/' => '/ga4-tracking-setup/',
		'/wgos-assets/cwv-speed-audit/' => '/wordpress-agentur-hannover/#zusammenarbeit',
		'/wgos-assets/cwv-optimierung/' => '/wordpress-agentur-hannover/#zusammenarbeit',
		'/wgos-assets/technical-seo-audit/' => '/wordpress-agentur-hannover/#zusammenarbeit',
		'/wgos-assets/local-seo/' => '/wordpress-agentur-hannover/',
		'/wgos-assets/landing-page-neu/' => '/landingpage-erstellen-lassen/',
		'/wgos-assets/landing-page-optimierung/' => '/conversion-optimierung/',
		'/wgos-assets/cta-formular-optimierung/' => '/conversion-optimierung/',
		'/wgos-assets/angebotsseiten-architektur/' => '/wordpress-website-erstellen-lassen/',
	];
}

/**
 * Resolve only local URLs. Unknown asset slugs are also retired.
 * null = unrelated; '' = retired without replacement; otherwise a local path.
 *
 * @param string $url URL or path.
 * @return string|null
 */
function hu_get_retired_system_target( $url ) {
	$host = wp_parse_url( $url, PHP_URL_HOST );
	if ( $host && strtolower( $host ) !== strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) ) ) {
		return null;
	}
	if ( 'wgos' === wp_parse_url( $url, PHP_URL_FRAGMENT ) && '/wordpress-agentur-hannover/' === wp_parse_url( $url, PHP_URL_PATH ) ) {
		return '/wordpress-agentur-hannover/#zusammenarbeit';
	}
	$path   = trailingslashit( '/' . ltrim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' ) );
	$routes = hu_get_retired_system_routes();
	if ( array_key_exists( $path, $routes ) ) {
		return $routes[ $path ];
	}
	return 0 === strpos( $path, '/wgos-assets/' ) ? '' : null;
}

/** @return void */
function hu_handle_retired_system_request() {
	$target = hu_get_retired_system_target( nexus_get_current_request_path() );
	if ( null === $target ) {
		return;
	}
	// A definition is a replacement only when its published detail really exists.
	if ( 0 === strpos( $target, '/glossar/' ) ) {
		$term = nexus_get_glossary_definition( trim( substr( $target, strlen( '/glossar/' ) ), '/' ) );
		if ( ! is_array( $term ) || '' === nexus_get_glossary_term_detail_url( $term ) ) {
			$target = '';
		}
	}
	if ( '' !== $target ) {
		wp_safe_redirect( home_url( $target ), 301, 'Retired system' );
		exit;
	}
	nocache_headers();
	header( 'X-Robots-Tag: noindex, follow', true );
	wp_die( 'Diese Seite wurde entfernt.', 'Seite entfernt', [ 'response' => 410 ] );
}
add_action( 'template_redirect', 'hu_handle_retired_system_request', -5 );

/**
 * Keep shared ACF/SCF groups; discard only location branches for retired content.
 * @param array $locations OR groups of AND rules.
 * @param array $retired_ids IDs of exclusively retired content.
 * @return array
 */
function hu_remove_retired_field_locations( $locations, $retired_ids = [] ) {
	$kept = [];
	foreach ( $locations as $rules ) {
		$branch = [];
		$retired = false;
		$removed_exclusion = false;
		foreach ( $rules as $rule ) {
			$matches = ( 'post_type' === ( $rule['param'] ?? '' ) && 'wgos_asset' === ( $rule['value'] ?? '' ) )
				|| ( 'page_template' === ( $rule['param'] ?? '' ) && in_array( $rule['value'] ?? '', [ 'page-wgos.php', 'page-wgos-assets.php' ], true ) )
				|| ( in_array( $rule['param'] ?? '', [ 'post', 'page' ], true ) && in_array( (int) ( $rule['value'] ?? 0 ), $retired_ids, true ) );
			if ( $matches && '==' === ( $rule['operator'] ?? '' ) ) {
				$retired = true;
				break;
			}
			if ( ! $matches ) {
				$branch[] = $rule;
			} elseif ( '!=' === ( $rule['operator'] ?? '' ) ) {
				$removed_exclusion = true;
			}
		}
		if ( ! $retired && ! $branch && $removed_exclusion ) {
			// Preserve a formerly global group for all editable post types.
			$branch[] = [ 'param' => 'post_type', 'operator' => '!=', 'value' => 'revision' ];
		}
		if ( ! $retired && $branch ) {
			$kept[] = $branch;
		}
	}
	return $kept;
}

/**
 * Remove old shortcode placements and repair links in stored editor content.
 * The migration touches only explicit old identifiers and local destinations.
 * @param string $content Stored HTML.
 * @return string
 */
function hu_clean_retired_system_content( $content ) {
	$content = preg_replace( '~\[hu_wgos_block\b[^\]]*\](?:.*?\[/hu_wgos_block\])?~s', '', $content );
	$content = preg_replace_callback(
		'~<a\b([^>]*\bhref\s*=\s*)([\x22\x27])(.*?)\2([^>]*)>(.*?)</a>~is',
		static function ( $match ) {
			$target = hu_get_retired_system_target( html_entity_decode( $match[3], ENT_QUOTES, 'UTF-8' ) );
			if ( null === $target ) {
				return $match[0];
			}
			if ( '' === $target ) {
				return $match[5];
			}
			return '<a' . $match[1] . $match[2] . esc_url( home_url( $target ) ) . $match[2] . $match[4] . '>' . $match[5] . '</a>';
		},
		$content
	);
	return str_replace( [ 'WordPress Growth Operating System (WGOS)', 'WordPress Growth Operating System', 'WGOS' ], 'WordPress, Tracking und Conversion', $content );
}

/** @return void */
function hu_maybe_retire_legacy_system() {
	if ( HU_LEGACY_SYSTEM_RETIREMENT_VERSION === get_option( 'hu_legacy_system_retirement_version' )
		|| nexus_get_glossary_registry_version() !== get_option( 'nexus_glossary_sync_version' )
		|| 'pass' !== nexus_get_glossary_last_assert_status()
		|| get_transient( 'hu_legacy_system_retirement_lock' ) ) {
		return;
	}
	set_transient( 'hu_legacy_system_retirement_lock', '1', 5 * MINUTE_IN_SECONDS );
	$errors = [];
	$retired_groups = (array) get_option( 'hu_legacy_system_retirement_field_parents', [] );
	$retired_ids = (array) get_option( 'hu_legacy_system_retirement_content_ids', [] );
	$delete = static function ( $id ) use ( &$errors ) {
		if ( ! wp_delete_post( (int) $id, true ) || get_post( (int) $id ) ) {
			$errors[] = (int) $id;
		}
	};
	$delete_group = static function ( $id ) use ( &$retired_groups, $delete ) {
		$retired_groups[] = (int) $id;
		update_option( 'hu_legacy_system_retirement_field_parents', array_values( array_unique( $retired_groups ) ), false );
		$delete( $id );
	};
	$statuses = array_values( get_post_stati() ); // Include drafts, private and trash.
	foreach ( get_posts( [ 'post_type' => 'wgos_asset', 'post_status' => $statuses, 'numberposts' => -1 ] ) as $asset ) {
		$retired_ids[] = (int) $asset->ID;
		update_option( 'hu_legacy_system_retirement_content_ids', array_values( array_unique( $retired_ids ) ), false );
		$delete( $asset->ID ); // WP also removes revisions, comments and ACF post meta.
	}
	foreach ( get_posts( [ 'post_type' => 'page', 'post_status' => $statuses, 'numberposts' => -1 ] ) as $page ) {
		$path = '/' . trim( (string) get_page_uri( $page ), '/' ) . '/';
		if ( null !== hu_get_retired_system_target( $path )
			|| in_array( get_page_template_slug( $page->ID ), [ 'page-wgos.php', 'page-wgos-assets.php' ], true ) ) {
			$retired_ids[] = (int) $page->ID;
			update_option( 'hu_legacy_system_retirement_content_ids', array_values( array_unique( $retired_ids ) ), false );
			$delete( $page->ID );
		}
	}
	// ACF Pro and SCF store imported definitions in these same WordPress types.
	foreach ( get_posts( [ 'post_type' => [ 'acf-field-group', 'acf-post-type' ], 'post_status' => $statuses, 'numberposts' => -1 ] ) as $definition ) {
		$data = maybe_unserialize( $definition->post_content );
		$data = is_array( $data ) ? $data : [];
		if ( 'group_nexus_wgos_asset' === $definition->post_name
			|| ( 'acf-post-type' === $definition->post_type && 'wgos_asset' === ( $data['post_type'] ?? '' ) ) ) {
			$delete_group( $definition->ID );
			continue;
		}
		if ( 'acf-field-group' !== $definition->post_type || empty( $data['location'] ) ) {
			continue;
		}
		$locations = hu_remove_retired_field_locations( (array) $data['location'], $retired_ids );
		if ( $locations === $data['location'] ) {
			continue;
		}
		if ( ! $locations ) {
			$delete_group( $definition->ID );
		} else {
			$data['location'] = $locations;
			if ( is_wp_error( wp_update_post( wp_slash( [ 'ID' => $definition->ID, 'post_content' => maybe_serialize( $data ) ] ), true ) ) ) {
				$errors[] = $definition->ID;
			}
		}
	}
	// Delete children of removed imported groups, including nested repeater fields.
	do {
		$removed = 0;
		foreach ( get_posts( [ 'post_type' => 'acf-field', 'post_status' => $statuses, 'numberposts' => -1 ] ) as $field ) {
			if ( in_array( (int) $field->post_parent, $retired_groups, true ) && ! get_post( $field->post_parent ) ) {
				$delete_group( $field->ID );
				++$removed;
			}
		}
	} while ( $removed && ! $errors );
	foreach ( get_posts( [ 'post_type' => [ 'post', 'page' ], 'post_status' => $statuses, 'numberposts' => -1 ] ) as $post ) {
		$content = hu_clean_retired_system_content( $post->post_content );
		if ( $content !== $post->post_content && is_wp_error( wp_update_post( wp_slash( [ 'ID' => $post->ID, 'post_content' => $content ] ), true ) ) ) {
			$errors[] = $post->ID;
		}
	}
	foreach ( get_posts( [ 'post_type' => 'nav_menu_item', 'post_status' => $statuses, 'numberposts' => -1 ] ) as $item ) {
		$url = get_post_meta( $item->ID, '_menu_item_url', true );
		$object_type = get_post_meta( $item->ID, '_menu_item_object', true );
		$object_id   = (int) get_post_meta( $item->ID, '_menu_item_object_id', true );
		if ( 'custom' !== get_post_meta( $item->ID, '_menu_item_type', true ) ) {
			$url = get_permalink( $object_id );
		}
		if ( 'wgos_asset' === $object_type || in_array( $object_id, $retired_ids, true ) || null !== hu_get_retired_system_target( (string) $url ) ) {
			$delete( $item->ID );
		}
	}
	foreach ( get_users( [ 'role' => 'wgos_client' ] ) as $user ) {
		$user->remove_role( 'wgos_client' );
		if ( ! $user->roles ) {
			$user->add_role( 'subscriber' ); // Keep the account and basic access.
		}
	}
	foreach ( wp_roles()->roles as $role_name => $role_data ) {
		$role = get_role( $role_name );
		if ( $role ) {
			$role->remove_cap( 'view_wgos_dashboard' );
		}
	}
	foreach ( get_users() as $user ) {
		if ( array_key_exists( 'view_wgos_dashboard', $user->caps ) ) {
			$user->remove_cap( 'view_wgos_dashboard' );
		}
	}
	remove_role( 'wgos_client' );
	foreach ( [ 'hu_wgos_access_version', 'nexus_wgos_asset_sync_version', 'nexus_wgos_asset_sync_errors' ] as $option ) {
		delete_option( $option );
	}
	delete_transient( 'nexus_wgos_asset_sync_lock' );
	if ( ! $errors ) {
		if ( post_type_exists( 'wgos_asset' ) ) {
			unregister_post_type( 'wgos_asset' );
		}
		flush_rewrite_rules( false );
		update_option( 'hu_legacy_system_retirement_version', HU_LEGACY_SYSTEM_RETIREMENT_VERSION, false );
		delete_option( 'hu_legacy_system_retirement_errors' );
		delete_option( 'hu_legacy_system_retirement_field_parents' );
		delete_option( 'hu_legacy_system_retirement_content_ids' );
		do_action( 'litespeed_purge_all' );
	} else {
		update_option( 'hu_legacy_system_retirement_errors', $errors, false );
	}
	delete_transient( 'hu_legacy_system_retirement_lock' );
}
add_action( 'init', 'hu_maybe_retire_legacy_system', 40 );
