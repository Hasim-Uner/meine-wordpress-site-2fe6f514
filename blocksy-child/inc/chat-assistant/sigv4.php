<?php
/** AWS Signature V4 without an SDK. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class HU_Chat_SigV4 {
	/**
	 * The caller passes the actual (once-encoded) URI; AWS services except S3
	 * require another URI encoding in the canonical request. Query pairs retain
	 * duplicates. S3's single encoding is used only by official-vector tests.
	 * @param array<string, string> $headers
	 * @return array<string, string>
	 */
	public static function sign( string $method, string $url, string $body, array $headers, string $access_key, string $secret_key, string $region, string $service, string $timestamp, bool $double_encode = true ): array {
		$parts = parse_url( $url );
		if ( false === $parts || empty( $parts['host'] ) ) {
			throw new RuntimeException( 'chat_sign_url' );
		}
		$normalized = [];
		foreach ( $headers as $name => $value ) {
			$normalized[ strtolower( $name ) ] = preg_replace( '/\s+/', ' ', trim( $value ) );
		}
		$normalized['host']       = $parts['host'];
		$normalized['x-amz-date'] = $timestamp;
		ksort( $normalized, SORT_STRING );
		$canonical_headers = '';
		foreach ( $normalized as $name => $value ) {
			$canonical_headers .= $name . ':' . $value . "\n";
		}
		$path = $parts['path'] ?? '/';
		if ( $double_encode ) {
			$path = implode( '/', array_map( 'rawurlencode', explode( '/', $path ) ) );
		}
		$pairs = [];
		foreach ( explode( '&', $parts['query'] ?? '' ) as $pair ) {
			if ( '' === $pair ) {
				continue;
			}
			$kv      = explode( '=', $pair, 2 );
			$pairs[] = [ rawurlencode( rawurldecode( $kv[0] ) ), rawurlencode( rawurldecode( $kv[1] ?? '' ) ) ];
		}
		usort( $pairs, static fn( $a, $b ) => strcmp( $a[0], $b[0] ) ?: strcmp( $a[1], $b[1] ) );
		$query = implode( '&', array_map( static fn( $pair ) => $pair[0] . '=' . $pair[1], $pairs ) );
		$signed_headers = implode( ';', array_keys( $normalized ) );
		$canonical      = implode( "\n", [ strtoupper( $method ), $path, $query, $canonical_headers, $signed_headers, hash( 'sha256', $body ) ] );
		$date           = substr( $timestamp, 0, 8 );
		$scope          = $date . '/' . $region . '/' . $service . '/aws4_request';
		$to_sign        = "AWS4-HMAC-SHA256\n" . $timestamp . "\n" . $scope . "\n" . hash( 'sha256', $canonical );
		$key            = hash_hmac( 'sha256', $date, 'AWS4' . $secret_key, true );
		foreach ( [ $region, $service, 'aws4_request' ] as $part ) {
			$key = hash_hmac( 'sha256', $part, $key, true );
		}
		$normalized['authorization'] = 'AWS4-HMAC-SHA256 Credential=' . $access_key . '/' . $scope . ', SignedHeaders=' . $signed_headers . ', Signature=' . hash_hmac( 'sha256', $to_sign, $key );
		return $normalized;
	}
}
