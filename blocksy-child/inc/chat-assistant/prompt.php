<?php
/** Fresh canon replacement; never cache commercial facts in the knowledge file. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @return string */
function hu_chat_price_ladder() {
	$lines = [
		'Anfrage-Website: ' . hu_freelancer_website_price( true ) . '. ' . hu_freelancer_website_scope_display(),
		'Landingpage: ' . hu_landingpage_price( true ),
		'Übernahme-Check: ' . hu_freelancer_takeover_check_price( true ),
		'Weiterentwicklung: ' . hu_freelancer_retainer_display(),
		'Kanon Energie und Website: ' . wp_json_encode( hu_pricing_canon(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ),
		'Tracking-Angebote: ' . wp_json_encode( hu_tracking_product_ladder(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ),
		'White-Label-Angebote: ' . wp_json_encode( hu_whitelabel_pricing_canon(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ),
	];
	return implode( "\n", $lines );
}

/**
 * Read every time. The builder/protected directory is added after the spike.
 * Fail closed on absent knowledge; never generate a prompt from memory.
 */
function hu_chat_load_knowledge(): string {
	$uploads = wp_upload_dir();
	if ( ! empty( $uploads['error'] ) ) {
		throw new RuntimeException( 'chat_knowledge_directory' );
	}
	$path = $uploads['basedir'] . '/hu-chat/knowledge.md';
	if ( ! is_readable( $path ) || filesize( $path ) > 600000 ) {
		throw new RuntimeException( 'chat_knowledge_unavailable' );
	}
	$knowledge = file_get_contents( $path );
	if ( false === $knowledge || '' === trim( $knowledge ) || mb_strlen( $knowledge, 'UTF-8' ) > 150000 ) {
		throw new RuntimeException( 'chat_knowledge_unavailable' );
	}
	return $knowledge;
}

function hu_chat_system_prompt(): string {
	$template = file_get_contents( __DIR__ . '/systemprompt.md' );
	if ( false === $template ) {
		throw new RuntimeException( 'chat_prompt_unavailable' );
	}
	$values = [
		'{{KONTAKT_EMAIL}}' => hu_get_contact_email(),
		'{{ANTWORTZEIT}}' => hu_response_promise( 'value' ),
		'{{PREISLEITER}}' => hu_chat_price_ladder(),
		'{{WISSEN}}' => hu_chat_load_knowledge(),
	];
	preg_match_all( '/\{\{[^{}]+\}\}/', $template, $matches );
	if ( array_diff( $matches[0], array_keys( $values ) ) || array_diff( array_keys( $values ), $matches[0] ) ) {
		throw new RuntimeException( 'chat_prompt_placeholders' );
	}
	// Single pass: placeholders inside untrusted website text are never expanded.
	return strtr( $template, $values );
}
