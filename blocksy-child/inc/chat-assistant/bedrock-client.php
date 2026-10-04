<?php
/** cURL transport; no SDK, redirects, provider logs, or request-body diagnostics. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class HU_Chat_Anthropic_Stream {
	/** @var array<string, int> */
	public array $usage = [];
	public bool $complete = false;
	public bool $visible = false;
	private bool $usage_complete = false;

	/** @param array<string, mixed> $event */
	public function accept( array $event, callable $emit ): void {
		$type = $event['type'] ?? '';
		if ( 'error' === $type ) {
			throw new RuntimeException( 'chat_model_error' );
		}
		if ( 'message_start' === $type ) {
			$this->usage = $event['message']['usage'] ?? [];
			$emit( 'probe', [ 'stage' => 'bedrock_start' ] );
		} elseif ( 'message_delta' === $type ) {
			$this->usage          = array_merge( $this->usage, $event['usage'] ?? [] );
			$this->usage_complete = isset( $event['usage']['output_tokens'] );
		} elseif ( 'content_block_delta' === $type && 'text_delta' === ( $event['delta']['type'] ?? '' ) ) {
			$text = $event['delta']['text'] ?? '';
			if ( is_string( $text ) && '' !== $text ) {
				$this->visible = true;
				$emit( 'text', [ 'text' => $text ] );
			}
		} elseif ( 'message_stop' === $type ) {
			$this->complete = $this->usage_complete && isset( $this->usage['input_tokens'] );
		}
	}
}

final class HU_Chat_Bedrock_Client {
	/** @var array<string, mixed> */
	private array $config;
	/** Internal transport seam for network-free tests; never read from runtime config. @var callable|null */
	private $transport;
	/** Each started request has an accounting slot, null if billing is uncertain. @var array<int, array<string, int>|null> */
	public array $attempts = [];

	public function __construct( array $config, ?callable $transport = null ) {
		$this->config = $config;
		$this->transport = $transport;
	}

	/**
	 * Return transport name; never retry after a visible partial response.
	 * $tick runs from cURL's progress callback so collected text leaves on time while Bedrock pauses.
	 */
	public function invoke( string $body, callable $emit, ?callable $tick = null ): string {
		$stream = new HU_Chat_Anthropic_Stream();
		$this->attempts[] = null;
		try {
			$this->stream( $body, $stream, $emit, $tick );
			$this->attempts[0] = $stream->usage;
			return 'stream';
		} catch ( Throwable $error ) {
			if ( $stream->visible ) {
				throw new RuntimeException( 'chat_partial_stream' );
			}
		}
		$emit( 'probe', [ 'stage' => 'fallback' ] );
		$this->attempts[] = null;
		$reply = $this->request( $body, false );
		$data  = json_decode( $reply, true, 32, JSON_THROW_ON_ERROR );
		if ( ! isset( $data['usage']['input_tokens'], $data['usage']['output_tokens'] ) || ! is_array( $data['content'] ?? null ) ) {
			throw new RuntimeException( 'chat_fallback_shape' );
		}
		$text = '';
		foreach ( $data['content'] as $block ) {
			if ( 'text' === ( $block['type'] ?? '' ) && is_string( $block['text'] ?? null ) ) {
				$text .= $block['text'];
			}
		}
		if ( '' === $text ) {
			throw new RuntimeException( 'chat_fallback_empty' );
		}
		$this->attempts[1] = $data['usage'];
		$emit( 'text', [ 'text' => $text ] );
		return 'fallback';
	}

	private function stream( string $body, HU_Chat_Anthropic_Stream $stream, callable $emit, ?callable $tick ): void {
		$decoder = new HU_Chat_Eventstream();
		$this->request( $body, true, $tick, static function ( string $chunk ) use ( $decoder, $stream, $emit ): void {
			foreach ( $decoder->push( $chunk ) as $frame ) {
				if ( 'event' !== ( $frame['headers'][':message-type'] ?? '' ) || 'chunk' !== ( $frame['headers'][':event-type'] ?? '' ) ) {
					throw new RuntimeException( 'chat_bedrock_event_error' );
				}
				$envelope = json_decode( $frame['payload'], true, 32, JSON_THROW_ON_ERROR );
				$bytes    = isset( $envelope['bytes'] ) && is_string( $envelope['bytes'] ) ? base64_decode( $envelope['bytes'], true ) : false;
				if ( false === $bytes ) {
					throw new RuntimeException( 'chat_bedrock_envelope' );
				}
				$event = json_decode( $bytes, true, 32, JSON_THROW_ON_ERROR );
				if ( ! is_array( $event ) ) {
					throw new RuntimeException( 'chat_bedrock_event' );
				}
				$stream->accept( $event, $emit );
			}
		} );
		$decoder->finish();
		if ( ! $stream->complete || ! $stream->visible ) {
			throw new RuntimeException( 'chat_incomplete_stream' );
		}
	}

	private function request( string $body, bool $stream, ?callable $tick = null, ?callable $consume = null ): string {
		if ( null !== $this->transport ) {
			return ( $this->transport )( $body, $stream, $consume, $tick );
		}
		$url = 'https://bedrock-runtime.eu-central-1.amazonaws.com/model/' . rawurlencode( $this->config['model'] ) . ( $stream ? '/invoke-with-response-stream' : '/invoke' );
		$headers = HU_Chat_SigV4::sign( 'POST', $url, $body, [
			'content-type' => 'application/json',
			'accept' => $stream ? 'application/vnd.amazon.eventstream' : 'application/json',
			'x-amzn-bedrock-accept' => 'application/json',
		], $this->config['access_key'], $this->config['secret_key'], 'eu-central-1', 'bedrock', gmdate( 'Ymd\THis\Z' ) );
		$header_lines = [];
		foreach ( $headers as $name => $value ) {
			$header_lines[] = $name . ': ' . $value;
		}
		$handle = curl_init( $url );
		if ( false === $handle ) {
			throw new RuntimeException( 'chat_curl_init' );
		}
		$status       = 0;
		$content_type = '';
		$response     = '';
		$received     = 0;
		$failed       = false;
		curl_setopt_array( $handle, [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $body,
			CURLOPT_HTTPHEADER => $header_lines,
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => 35,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_NOPROGRESS => null === $tick,
			CURLOPT_XFERINFOFUNCTION => static function () use ( $tick ): int {
				if ( null !== $tick ) {
					$tick();
				}
				return 0;
			},
			CURLOPT_HEADERFUNCTION => static function ( $curl, string $line ) use ( &$status, &$content_type ): int {
				if ( preg_match( '/^HTTP\/\S+\s+(\d{3})/', $line, $match ) ) {
					$status = (int) $match[1];
					$content_type = '';
				} elseif ( 0 === stripos( $line, 'content-type:' ) ) {
					$content_type = strtolower( trim( substr( $line, 13 ) ) );
				}
				return strlen( $line );
			},
			CURLOPT_WRITEFUNCTION => static function ( $curl, string $chunk ) use ( $stream, $consume, &$status, &$content_type, &$response, &$received, &$failed ): int {
				$received += strlen( $chunk );
				if ( $received > 2 * 1024 * 1024 ) {
					$failed = true;
					return 0;
				}
				if ( 200 !== $status ) {
					return strlen( $chunk ); // Discard AWS error bodies, including echoed inputs.
				}
				try {
					if ( $stream ) {
						if ( 0 !== strpos( $content_type, 'application/vnd.amazon.eventstream' ) || null === $consume ) {
							throw new RuntimeException( 'chat_stream_content_type' );
						}
						$consume( $chunk );
					} else {
						$response .= $chunk;
					}
				} catch ( Throwable $error ) {
					$failed = true;
					return 0;
				}
				return strlen( $chunk );
			},
		] );
		$ok = curl_exec( $handle );
		curl_close( $handle );
		if ( false === $ok || $failed || 200 !== $status ) {
			throw new RuntimeException( 'chat_bedrock_transport' );
		}
		return $response;
	}
}
