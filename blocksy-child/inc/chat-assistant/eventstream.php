<?php
/** Incremental AWS binary event-stream framing, both CRCs checked. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class HU_Chat_Eventstream {
	private string $buffer = '';

	/** @return array<int, array{headers: array<string, mixed>, payload: string}> */
	public function push( string $chunk ): array {
		$this->buffer .= $chunk;
		$frames        = [];
		while ( strlen( $this->buffer ) >= 12 ) {
			$prelude = unpack( 'Ntotal/Nheaders', substr( $this->buffer, 0, 8 ) );
			if ( hash( 'crc32b', substr( $this->buffer, 0, 8 ), true ) !== substr( $this->buffer, 8, 4 ) ) {
				throw new RuntimeException( 'chat_prelude_crc' );
			}
			if ( $prelude['total'] < 16 || $prelude['headers'] > $prelude['total'] - 16 ) {
				throw new RuntimeException( 'chat_frame_length' );
			}
			// Local resource guard, independent of the protocol's service-side limits.
			if ( $prelude['total'] > 32 * 1024 * 1024 ) {
				throw new RuntimeException( 'chat_frame_resource_limit' );
			}
			if ( strlen( $this->buffer ) < $prelude['total'] ) {
				break;
			}
			$frame = substr( $this->buffer, 0, $prelude['total'] );
			if ( hash( 'crc32b', substr( $frame, 0, -4 ), true ) !== substr( $frame, -4 ) ) {
				throw new RuntimeException( 'chat_frame_crc' );
			}
			$frames[]     = [
				'headers' => $this->headers( substr( $frame, 12, $prelude['headers'] ) ),
				'payload' => substr( $frame, 12 + $prelude['headers'], $prelude['total'] - $prelude['headers'] - 16 ),
			];
			$this->buffer = substr( $this->buffer, $prelude['total'] );
		}
		return $frames;
	}

	public function finish(): void {
		if ( '' !== $this->buffer ) {
			throw new RuntimeException( 'chat_truncated_frame' );
		}
	}

	/** @return array<string, mixed> */
	private function headers( string $bytes ): array {
		$headers = [];
		$length  = strlen( $bytes );
		$offset  = 0;
		while ( $offset < $length ) {
			$name_length = ord( $bytes[ $offset++ ] );
			if ( 0 === $name_length || $offset + $name_length + 1 > $length ) {
				throw new RuntimeException( 'chat_header_length' );
			}
			$name    = substr( $bytes, $offset, $name_length );
			$offset += $name_length;
			$type    = ord( $bytes[ $offset++ ] );
			if ( array_key_exists( $name, $headers ) ) {
				throw new RuntimeException( 'chat_duplicate_header' );
			}
			if ( 0 === $type || 1 === $type ) {
				$headers[ $name ] = 0 === $type;
				continue;
			}
			$sizes = [ 2 => 1, 3 => 2, 4 => 4, 5 => 8, 8 => 8, 9 => 16 ];
			if ( 6 === $type || 7 === $type ) {
				if ( $offset + 2 > $length ) {
					throw new RuntimeException( 'chat_header_length' );
				}
				$size    = unpack( 'n', substr( $bytes, $offset, 2 ) )[1];
				$offset += 2;
			} elseif ( isset( $sizes[ $type ] ) ) {
				$size = $sizes[ $type ];
			} else {
				throw new RuntimeException( 'chat_header_type' );
			}
			if ( $offset + $size > $length ) {
				throw new RuntimeException( 'chat_header_length' );
			}
			$headers[ $name ] = substr( $bytes, $offset, $size );
			$offset         += $size;
		}
		return $headers;
	}
}
