<?php
/**
 * Affiliate Link Registry
 *
 * Zentrale Single Source of Truth für Partnerlinks. Templates rufen
 * hu_get_affiliate_url('hostpress') statt URLs zu hardcoden. Tracking-Attribute
 * werden gemeinsam mit hu_render_affiliate_anchor_attrs() erzeugt.
 *
 * Affiliate-Referenz wird über die Konstante HU_HOSTPRESS_AFFILIATE_REF gesetzt
 * (z.B. in wp-config.php) oder via Filter `hu_affiliate_ref_hostpress`.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provider-Registry.
 *
 * @return array<string, array<string, string>>
 */
function hu_get_affiliate_providers() {
	return [
		'hostpress' => [
			'base'        => 'https://www.hostpress.de/wordpress-hosting/',
			'ref_param'   => 'aff',
			'ref_default' => defined( 'HU_HOSTPRESS_AFFILIATE_REF' ) ? (string) HU_HOSTPRESS_AFFILIATE_REF : '',
			'label'       => 'HostPress',
			'category'    => 'hostpress',
		],
	];
}

/**
 * Baue die Affiliate-URL für einen Provider.
 *
 * @param string $provider Slug aus hu_get_affiliate_providers().
 * @return string
 */
function hu_get_affiliate_url( $provider ) {
	$providers = hu_get_affiliate_providers();
	if ( ! isset( $providers[ $provider ] ) ) {
		return '';
	}

	$config = $providers[ $provider ];
	$ref    = apply_filters( 'hu_affiliate_ref_' . $provider, $config['ref_default'] );

	if ( '' === trim( (string) $ref ) ) {
		return $config['base'];
	}

	return add_query_arg( [ $config['ref_param'] => sanitize_text_field( (string) $ref ) ], $config['base'] );
}

/**
 * Render der Standard-Anchor-Attribute für einen Affiliate-Link.
 *
 * @param string $provider Slug aus hu_get_affiliate_providers().
 * @param string $section  Tracking-Section (z.B. 'stack_hosting', 'blog_inline').
 * @return string HTML-Attribute, bereits escaped.
 */
function hu_render_affiliate_anchor_attrs( $provider, $section ) {
	$providers = hu_get_affiliate_providers();
	$category  = isset( $providers[ $provider ]['category'] ) ? $providers[ $provider ]['category'] : $provider;

	$attrs = [
		'rel'                  => 'sponsored nofollow noopener',
		'target'               => '_blank',
		'data-track-action'    => 'affiliate_click',
		'data-track-category'  => $category,
		'data-track-section'   => $section,
	];

	$out = '';
	foreach ( $attrs as $key => $value ) {
		$out .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
	}

	return $out;
}
