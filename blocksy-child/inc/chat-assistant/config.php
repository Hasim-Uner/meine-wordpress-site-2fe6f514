<?php
/** Secrets are read only from the existing runtime config boundary. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @return array<string, mixed> */
function hu_chat_config() {
	$config = [
		'mode'       => nexus_get_mail_runtime_value( [ 'HU_CHAT_MODE' ], 'off' ),
		'access_key' => nexus_get_mail_runtime_value( [ 'HU_BEDROCK_ACCESS_KEY_ID' ] ),
		'secret_key' => nexus_get_mail_runtime_value( [ 'HU_BEDROCK_SECRET_ACCESS_KEY' ] ),
		'region'     => nexus_get_mail_runtime_value( [ 'HU_BEDROCK_REGION' ], 'eu-central-1' ),
		'model'      => nexus_get_mail_runtime_value( [ 'HU_BEDROCK_MODEL_ID' ] ),
		'budget'     => nexus_get_mail_runtime_value( [ 'HU_CHAT_MONTHLY_BUDGET_USD' ], '20' ),
		'prices'     => [],
	];
	foreach ( [ 'INPUT', 'OUTPUT', 'CACHE_WRITE', 'CACHE_READ' ] as $type ) {
		$config['prices'][ strtolower( $type ) ] = nexus_get_mail_runtime_value( [ 'HU_CHAT_PRICE_' . $type ] );
	}
	return $config;
}

/** Validate configuration without echoing secrets or choosing an undocumented model. */
function hu_chat_config_valid( array $config ): bool {
	if ( 'preview' !== $config['mode'] || 'eu-central-1' !== $config['region'] ) {
		return false;
	}
	if ( ! preg_match( '/\Aeu\.anthropic\.[a-z0-9.:-]+\z/', $config['model'] ) ) {
		return false;
	}
	foreach ( [ 'access_key', 'secret_key' ] as $key ) {
		if ( '' === $config[ $key ] || preg_match( '/[\x00-\x20\x7f]/', $config[ $key ] ) ) {
			return false;
		}
	}
	foreach ( array_merge( [ $config['budget'] ], array_values( $config['prices'] ) ) as $number ) {
		if ( ! is_numeric( $number ) || ! is_finite( (float) $number ) || (float) $number <= 0 || (float) $number > 100000 ) {
			return false;
		}
	}
	return true;
}

/** The admin probe is available only with a complete preview configuration. */
function hu_chat_spike_enabled(): bool {
	return hu_chat_config_valid( hu_chat_config() ) && function_exists( 'curl_init' );
}
