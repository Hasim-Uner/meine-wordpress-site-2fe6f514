<?php
/** Atomic monthly cost reservations; stores numbers only, never a conversation. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Micro-USD: USD per million tokens multiplied by token count. */
function hu_chat_usage_cost( array $usage, array $prices ): int {
	return (int) ceil(
		max( 0, (int) ( $usage['input_tokens'] ?? 0 ) ) * (float) $prices['input'] +
		max( 0, (int) ( $usage['output_tokens'] ?? 0 ) ) * (float) $prices['output'] +
		max( 0, (int) ( $usage['cache_creation_input_tokens'] ?? 0 ) ) * (float) $prices['cache_write'] +
		max( 0, (int) ( $usage['cache_read_input_tokens'] ?? 0 ) ) * (float) $prices['cache_read']
	);
}

/** @return array<string, mixed>|false */
function hu_chat_spike_reserve( array $config ) {
	global $wpdb;
	$key   = 'hu_chat_cost_' . gmdate( 'Ym' );
	$cap   = (int) floor( (float) $config['budget'] * 1000000 );
	// Fixed tiny request: conservative byte-bound plus protocol overhead. Two
	// attempts cover the stream and the single non-streaming fallback together.
	$one   = (int) ceil( 1024 * max( array_map( 'floatval', $config['prices'] ) ) + 64 * (float) $config['prices']['output'] );
	$total = 2 * $one;
	if ( $total > $cap ) {
		return false;
	}
	add_option( $key, '0', '', false );
	$changed = $wpdb->query( $wpdb->prepare(
		"UPDATE {$wpdb->options} SET option_value = CAST(option_value AS UNSIGNED) + %d WHERE option_name = %s AND CAST(option_value AS UNSIGNED) <= %d",
		$total, $key, $cap - $total
	) );
	if ( 1 !== $changed ) {
		return false;
	}
	wp_cache_delete( $key, 'options' );
	return [ 'key' => $key, 'amount' => $total, 'one' => $one ];
}

/** Unknown or interrupted attempts retain their full reservation. */
function hu_chat_spike_settle( array $reservation, array $attempts, array $prices ): void {
	global $wpdb;
	$cost = 0;
	foreach ( $attempts as $usage ) {
		$cost += is_array( $usage ) ? hu_chat_usage_cost( $usage, $prices ) : $reservation['one'];
	}
	$refund = max( 0, $reservation['amount'] - $cost );
	if ( $refund ) {
		$wpdb->query( $wpdb->prepare(
			"UPDATE {$wpdb->options} SET option_value = GREATEST(0, CAST(option_value AS SIGNED) - %d) WHERE option_name = %s",
			$refund, $reservation['key']
		) );
		wp_cache_delete( $reservation['key'], 'options' );
	}
	foreach ( [ 'input_tokens', 'output_tokens', 'cache_creation_input_tokens', 'cache_read_input_tokens' ] as $field ) {
		$count = 0;
		foreach ( $attempts as $usage ) {
			$count += max( 0, (int) ( $usage[ $field ] ?? 0 ) );
		}
		if ( $count ) {
			$key = $reservation['key'] . '_' . $field;
			add_option( $key, '0', '', false );
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->options} SET option_value = CAST(option_value AS UNSIGNED) + %d WHERE option_name = %s", $count, $key ) );
			wp_cache_delete( $key, 'options' );
		}
	}
}
