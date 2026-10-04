<?php
/**
 * SSE writer for size-buffering proxies. The live-box spike showed a ~4 KB
 * proxy buffer: small events waited until the response ended. Text deltas are
 * therefore collected and sent at most every HU_CHAT_FLUSH_INTERVAL_MS, and each
 * send is padded with an SSE comment to whole HU_CHAT_FLUSH_PAD blocks.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HU_CHAT_FLUSH_INTERVAL_MS = 120;
const HU_CHAT_FLUSH_PAD_DEFAULT = 4096;
const HU_CHAT_FLUSH_PAD_MAX     = 65536;

/** Block size from wp-config `HU_CHAT_FLUSH_PAD`; 0 disables padding, invalid values keep the default. */
function hu_chat_flush_pad(): int {
	$value = nexus_get_mail_runtime_value( [ 'HU_CHAT_FLUSH_PAD' ], (string) HU_CHAT_FLUSH_PAD_DEFAULT );
	if ( ! preg_match( '/\A\d{1,6}\z/', $value ) ) {
		return HU_CHAT_FLUSH_PAD_DEFAULT;
	}
	return min( (int) $value, HU_CHAT_FLUSH_PAD_MAX );
}

/**
 * Stream headers. `Content-Encoding: identity` is not a registered coding, but
 * nginx and most proxies skip their own gzip/brotli when any encoding is set.
 *
 * @return string[]
 */
function hu_chat_stream_headers(): array {
	return [
		'Content-Type: text/event-stream; charset=UTF-8',
		'Cache-Control: no-store, no-transform',
		'Content-Encoding: identity',
		'X-Accel-Buffering: no',
	];
}

/** Compression off before the first byte, all PHP buffers closed, SSE headers sent. */
function hu_chat_stream_begin(): void {
	@ini_set( 'zlib.output_compression', '0' );
	@ini_set( 'brotli.output_compression', '0' );
	if ( function_exists( 'apache_setenv' ) ) {
		@apache_setenv( 'no-gzip', '1' );
	}
	while ( ob_get_level() > 0 ) {
		if ( ! @ob_end_clean() ) {
			break;
		}
	}
	@ini_set( 'implicit_flush', '1' );
	if ( ! headers_sent() ) {
		header_remove( 'Content-Length' );
		foreach ( hu_chat_stream_headers() as $line ) {
			header( $line );
		}
	}
}

final class HU_Chat_SSE_Writer {
	private int $pad;
	/** @var callable(string): void */
	private $write;
	/** @var callable(): float Seconds; injectable for tests. */
	private $clock;
	private float $started;
	private float $last_send = -INF;
	private string $pending = '';
	public int $sends = 0;

	public function __construct( int $pad, ?callable $write = null, ?callable $clock = null ) {
		$this->pad     = max( 0, $pad );
		$this->write   = $write ?? static function ( string $bytes ): void {
			echo $bytes; // phpcs:ignore WordPress.Security.EscapeOutput -- JSON-encoded SSE frames and fixed spaces.
			flush();
		};
		$this->clock   = $clock ?? static fn(): float => microtime( true );
		$this->started = ( $this->clock )();
	}

	/** Text is collected; every other event first sends pending text, then itself. */
	public function emit( string $event, array $data ): void {
		if ( 'text' === $event ) {
			$this->pending .= (string) ( $data['text'] ?? '' );
			$this->tick();
			return;
		}
		$this->send( $this->pending_frame() . $this->frame( $event, $data ) );
	}

	/** Called on every delta and from the cURL progress callback while Bedrock pauses. */
	public function tick(): void {
		if ( '' !== $this->pending && ( ( $this->clock )() - $this->last_send ) * 1000 >= HU_CHAT_FLUSH_INTERVAL_MS ) {
			$this->send( $this->pending_frame() );
		}
	}

	public function finish(): void {
		if ( '' !== $this->pending ) {
			$this->send( $this->pending_frame() );
		}
	}

	private function pending_frame(): string {
		if ( '' === $this->pending ) {
			return '';
		}
		$frame         = $this->frame( 'text', [ 'text' => $this->pending ] );
		$this->pending = '';
		return $frame;
	}

	private function frame( string $event, array $data ): string {
		$data['elapsed_ms'] = (int) round( ( ( $this->clock )() - $this->started ) * 1000 );
		return 'event: ' . $event . "\n" . 'data: ' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE ) . "\n\n";
	}

	private function send( string $frames ): void {
		( $this->write )( $frames . self::padding( strlen( $frames ), $this->pad ) );
		$this->last_send = ( $this->clock )();
		$this->sends++;
	}

	/** Comment that brings $length up to the next whole block; a comment needs at least three bytes. */
	public static function padding( int $length, int $pad ): string {
		if ( $pad <= 0 ) {
			return '';
		}
		$missing = ( $pad - $length % $pad ) % $pad;
		if ( 0 === $missing ) {
			return '';
		}
		if ( $missing < 3 ) {
			$missing += $pad;
		}
		return ':' . str_repeat( ' ', $missing - 3 ) . "\n\n";
	}
}
