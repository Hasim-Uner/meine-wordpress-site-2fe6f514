<?php
/**
 * Knowledge file for the chat assistant.
 *
 * Source is the public route index (llms.txt sections) plus published blog
 * posts. Pages are rendered through a loopback request so custom templates are
 * included; only the <main> text survives. Commercial facts (prices, contact,
 * response promise) never live here; prompt.php injects them fresh.
 *
 * The file holds only public website text. Raidboxes serves through nginx, so
 * the .htaccess below is a courtesy for Apache hosts, not the protection.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HU_CHAT_KNOWLEDGE_MAX_CHARS  = 150000;
const HU_CHAT_KNOWLEDGE_PAGE_CHARS = 14000;
const HU_CHAT_KNOWLEDGE_MAX_POSTS  = 40;
const HU_CHAT_KNOWLEDGE_EVENT      = 'hu_chat_knowledge_build';
const HU_CHAT_KNOWLEDGE_META       = 'hu_chat_knowledge_meta';

/** Paths that never enter the knowledge file. */
function hu_chat_knowledge_excluded_paths(): array {
	return [ '/datenschutz/', '/impressum/', '/blog/', '/agb/' ];
}

function hu_chat_knowledge_dir(): string {
	$uploads = wp_upload_dir();
	if ( ! empty( $uploads['error'] ) ) {
		throw new RuntimeException( 'chat_knowledge_directory' );
	}
	return $uploads['basedir'] . '/hu-chat';
}

/**
 * Unique internal page URLs from the route index, in index order (money pages first).
 *
 * @return array<int, array{url: string, path: string, label: string, description: string}>
 */
function hu_chat_knowledge_sources(): array {
	if ( ! function_exists( 'nexus_get_llms_txt_sections' ) ) {
		return [];
	}
	$home_host = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );
	$sources   = [];
	foreach ( nexus_get_llms_txt_sections() as $section ) {
		foreach ( (array) ( $section['links'] ?? [] ) as $link ) {
			$parts = wp_parse_url( (string) ( $link['url'] ?? '' ) );
			if ( ! is_array( $parts ) || strtolower( (string) ( $parts['host'] ?? $home_host ) ) !== $home_host ) {
				continue;
			}
			$path = trailingslashit( '/' . ltrim( (string) ( $parts['path'] ?? '/' ), '/' ) );
			if ( isset( $sources[ $path ] ) || in_array( $path, hu_chat_knowledge_excluded_paths(), true ) ) {
				continue;
			}
			$sources[ $path ] = [
				'url'         => home_url( $path ),
				'path'        => $path,
				'label'       => (string) ( $link['label'] ?? '' ),
				'description' => (string) ( $link['description'] ?? '' ),
			];
		}
	}
	return array_values( $sources );
}

/** Collapse whitespace inside one text node run. */
function hu_chat_knowledge_clean( string $text ): string {
	$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$text = preg_replace( '/[\x{00A0}\x{2009}\x{202F}]/u', ' ', $text );
	$text = preg_replace( '/[\x{00AD}\x{200B}]/u', '', (string) $text );
	return trim( (string) preg_replace( '/\s+/u', ' ', (string) $text ) );
}

/**
 * Plain markdown from rendered HTML: headings, paragraphs, list items and
 * table rows. Navigation, forms, scripts and decoration are dropped.
 *
 * @return array{title: string, text: string}
 */
function hu_chat_html_to_markdown( string $html ): array {
	if ( '' === trim( $html ) || ! class_exists( 'DOMDocument' ) ) {
		return [ 'title' => '', 'text' => '' ];
	}
	$dom      = new DOMDocument();
	$previous = libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="UTF-8">' . $html, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );
	$xpath = new DOMXPath( $dom );

	$title_node = $xpath->query( '//title' )->item( 0 );
	$title      = $title_node ? hu_chat_knowledge_clean( $title_node->textContent ) : '';

	$root = $xpath->query( '//main' )->item( 0 ) ?? $xpath->query( '//article' )->item( 0 ) ?? $xpath->query( '//body' )->item( 0 );
	if ( ! $root ) {
		return [ 'title' => $title, 'text' => '' ];
	}

	$drop = './/script|.//style|.//noscript|.//template|.//svg|.//form|.//nav|.//header|.//footer|.//button|.//iframe|.//dialog|.//select|.//textarea|.//input|.//*[@hidden]|.//*[@aria-hidden="true"]|.//*[contains(concat(" ", normalize-space(@class), " "), " hu-chat ")]';
	$nodes = [];
	foreach ( $xpath->query( $drop, $root ) as $node ) {
		$nodes[] = $node;
	}
	foreach ( $nodes as $node ) {
		if ( $node->parentNode ) {
			$node->parentNode->removeChild( $node );
		}
	}

	$h1    = $xpath->query( './/h1', $root )->item( 0 );
	$title = $h1 ? hu_chat_knowledge_clean( $h1->textContent ) : $title;

	$lines = [];
	foreach ( $xpath->query( './/h2|.//h3|.//h4|.//p|.//li|.//tr|.//dt|.//dd|.//blockquote|.//figcaption', $root ) as $node ) {
		$name = strtolower( $node->nodeName );
		// Only outermost blocks: a paragraph inside a list item or cell is part of that row.
		if ( $xpath->query( 'ancestor::li|ancestor::tr|ancestor::blockquote|ancestor::dd|ancestor::p', $node )->length ) {
			continue;
		}
		if ( 'tr' === $name ) {
			$cells = [];
			foreach ( $xpath->query( './th|./td', $node ) as $cell ) {
				$cells[] = str_replace( '|', '/', hu_chat_knowledge_clean( $cell->textContent ) );
			}
			$cells = array_filter( $cells, static fn( string $s ): bool => '' !== $s );
			if ( $cells ) {
				$lines[] = '| ' . implode( ' | ', $cells ) . ' |';
			}
			continue;
		}
		$text = hu_chat_knowledge_clean( $node->textContent );
		if ( '' === $text ) {
			continue;
		}
		if ( preg_match( '/^h([2-4])$/', $name, $match ) ) {
			$lines[] = '';
			$lines[] = str_repeat( '#', (int) $match[1] + 1 ) . ' ' . $text;
		} elseif ( 'li' === $name || 'dd' === $name ) {
			$lines[] = '- ' . $text;
		} else {
			$lines[] = $text;
		}
	}

	// Repeated teaser lines (cards, sticky bars) add tokens, not facts.
	$seen = [];
	$out  = [];
	foreach ( $lines as $line ) {
		if ( '' !== $line && isset( $seen[ $line ] ) ) {
			continue;
		}
		$seen[ $line ] = true;
		$out[]         = $line;
	}
	$text = trim( (string) preg_replace( "/\n{3,}/", "\n\n", implode( "\n", $out ) ) );
	// Templates built from divs and spans: fall back to the plain main text.
	$parts = [];
	foreach ( $xpath->query( './/text()', $root ) as $node ) {
		$parts[] = $node->nodeValue;
	}
	$plain = hu_chat_knowledge_clean( implode( ' ', $parts ) );
	if ( mb_strlen( $text, 'UTF-8' ) < 200 && mb_strlen( $plain, 'UTF-8' ) > mb_strlen( $text, 'UTF-8' ) ) {
		$text = $plain;
	}
	return [ 'title' => $title, 'text' => $text ];
}

/** Cut at a line boundary, never mid-word. */
function hu_chat_knowledge_cut( string $text, int $limit ): string {
	if ( mb_strlen( $text, 'UTF-8' ) <= $limit ) {
		return $text;
	}
	$cut  = mb_substr( $text, 0, $limit, 'UTF-8' );
	$last = mb_strrpos( $cut, "\n", 0, 'UTF-8' );
	return rtrim( false !== $last && $last > $limit / 2 ? mb_substr( $cut, 0, $last, 'UTF-8' ) : $cut ) . "\n[gekürzt]";
}

/** @return string|null Rendered page text block, null on fetch failure. */
function hu_chat_knowledge_render_page( array $source ): ?string {
	$response = wp_remote_get(
		$source['url'],
		[
			'timeout'             => 20,
			'redirection'         => 2,
			'reject_unsafe_urls'  => false, // Own host; loopback may resolve privately.
			'user-agent'          => 'hu-chat-knowledge/1.0 (' . home_url( '/' ) . ')',
			'limit_response_size' => 3 * 1024 * 1024,
		]
	);
	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		return null;
	}
	$page  = hu_chat_html_to_markdown( (string) wp_remote_retrieve_body( $response ) );
	$title = '' !== $page['title'] ? $page['title'] : $source['label'];
	if ( '' === $page['text'] ) {
		return null;
	}
	$block = '## ' . $title . ' — ' . $source['url'] . "\n\n";
	if ( '' !== $source['description'] ) {
		$block .= 'Zweck der Seite: ' . $source['description'] . "\n\n";
	}
	return $block . hu_chat_knowledge_cut( $page['text'], HU_CHAT_KNOWLEDGE_PAGE_CHARS );
}

/** Blog posts as title, URL, excerpt and H2 outline only. @return string[] */
function hu_chat_knowledge_post_blocks(): array {
	$blocks = [];
	$posts  = get_posts(
		[
			'post_type'        => 'post',
			'post_status'      => 'publish',
			'numberposts'      => HU_CHAT_KNOWLEDGE_MAX_POSTS,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'suppress_filters' => false,
		]
	);
	foreach ( $posts as $post ) {
		if ( post_password_required( $post ) ) {
			continue;
		}
		$excerpt = hu_chat_knowledge_clean( wp_strip_all_tags( has_excerpt( $post ) ? $post->post_excerpt : wp_trim_words( strip_shortcodes( $post->post_content ), 45, ' …' ) ) );
		preg_match_all( '/<h2[^>]*>(.*?)<\/h2>/is', (string) $post->post_content, $matches );
		$outline = array_filter( array_map( static fn( $h ) => hu_chat_knowledge_clean( wp_strip_all_tags( $h ) ), $matches[1] ), static fn( string $s ): bool => '' !== $s );
		$block   = '### ' . hu_chat_knowledge_clean( get_the_title( $post ) ) . ' — ' . get_permalink( $post ) . "\n";
		if ( '' !== $excerpt ) {
			$block .= 'Kurzfassung: ' . $excerpt . "\n";
		}
		if ( $outline ) {
			$block .= 'Gliederung: ' . implode( ' · ', array_slice( $outline, 0, 12 ) ) . "\n";
		}
		$blocks[] = $block;
	}
	return $blocks;
}

/**
 * Pages first in index order; blog entries are dropped from the end first.
 *
 * @param string[] $pages
 * @param string[] $posts
 */
function hu_chat_knowledge_assemble( array $pages, array $posts, string $stamp ): string {
	$head = '# Website-Inhalt hasimuener.de' . "\n\nStand: " . $stamp . "\n\n";
	$body = $head . implode( "\n\n", $pages );
	$room = HU_CHAT_KNOWLEDGE_MAX_CHARS - mb_strlen( $body, 'UTF-8' ) - 64;
	if ( $room < 0 ) {
		return hu_chat_knowledge_cut( $body, HU_CHAT_KNOWLEDGE_MAX_CHARS - 16 );
	}
	$blog = '';
	foreach ( $posts as $post ) {
		if ( mb_strlen( $blog . $post, 'UTF-8' ) + 32 > $room ) {
			break;
		}
		$blog .= $post . "\n";
	}
	if ( '' !== $blog ) {
		$body .= "\n\n## Blogartikel (Auswahl, nur Kurzfassung)\n\n" . trim( $blog );
	}
	return $body . "\n";
}

/** Atomic write next to an index.php and an Apache deny rule. */
function hu_chat_knowledge_write( string $content ): void {
	$dir = hu_chat_knowledge_dir();
	if ( ! wp_mkdir_p( $dir ) ) {
		throw new RuntimeException( 'chat_knowledge_directory' );
	}
	foreach ( [ 'index.php' => "<?php\n// Silence.\n", '.htaccess' => "Require all denied\nDeny from all\n" ] as $file => $guard ) {
		if ( ! file_exists( $dir . '/' . $file ) ) {
			file_put_contents( $dir . '/' . $file, $guard );
		}
	}
	$tmp = $dir . '/knowledge.md.' . wp_generate_password( 8, false ) . '.tmp';
	if ( false === file_put_contents( $tmp, $content, LOCK_EX ) || ! rename( $tmp, $dir . '/knowledge.md' ) ) {
		@unlink( $tmp );
		throw new RuntimeException( 'chat_knowledge_write' );
	}
}

/**
 * Build and store the knowledge file. Keeps the previous file when too few
 * pages render, so a broken loopback never empties the assistant.
 *
 * @return array<string, mixed> Build summary (counts only).
 */
function hu_chat_build_knowledge(): array {
	$sources = hu_chat_knowledge_sources();
	$pages   = [];
	$failed  = [];
	foreach ( $sources as $source ) {
		$block = hu_chat_knowledge_render_page( $source );
		if ( null === $block ) {
			$failed[] = $source['path'];
			continue;
		}
		$pages[] = $block;
	}
	$meta = [
		'attempted_at' => time(),
		'built_at'     => (int) ( get_option( HU_CHAT_KNOWLEDGE_META, [] )['built_at'] ?? 0 ),
		'pages'        => count( $pages ),
		'failed'       => $failed,
		'chars'        => 0,
		'deploy_sha'   => function_exists( 'nexus_get_deploy_marker_sha' ) ? nexus_get_deploy_marker_sha() : '',
		'ok'           => false,
	];
	if ( count( $pages ) < max( 1, (int) ceil( count( $sources ) / 2 ) ) ) {
		update_option( HU_CHAT_KNOWLEDGE_META, $meta, false );
		error_log( '[hu-chat] knowledge_build_incomplete' );
		return $meta;
	}
	$content = hu_chat_knowledge_assemble( $pages, hu_chat_knowledge_post_blocks(), wp_date( 'Y-m-d' ) );
	hu_chat_knowledge_write( $content );
	$meta['built_at'] = time();
	$meta['chars']    = mb_strlen( $content, 'UTF-8' );
	$meta['ok']       = true;
	update_option( HU_CHAT_KNOWLEDGE_META, $meta, false );
	return $meta;
}

function hu_chat_knowledge_cron(): void {
	try {
		hu_chat_build_knowledge();
	} catch ( Throwable $error ) {
		error_log( '[hu-chat] knowledge_build_failed' );
	}
}
add_action( HU_CHAT_KNOWLEDGE_EVENT, 'hu_chat_knowledge_cron' );

/** Daily rebuild plus one rebuild after every deploy (marker SHA changed). */
function hu_chat_knowledge_schedule(): void {
	if ( ! hu_chat_assistant_enabled() ) {
		if ( wp_next_scheduled( HU_CHAT_KNOWLEDGE_EVENT ) ) {
			wp_clear_scheduled_hook( HU_CHAT_KNOWLEDGE_EVENT );
		}
		return;
	}
	if ( ! wp_next_scheduled( HU_CHAT_KNOWLEDGE_EVENT ) ) {
		wp_schedule_event( time() + 300, 'daily', HU_CHAT_KNOWLEDGE_EVENT );
	}
	$meta = get_option( HU_CHAT_KNOWLEDGE_META, [] );
	$sha  = function_exists( 'nexus_get_deploy_marker_sha' ) ? nexus_get_deploy_marker_sha() : '';
	$due  = '' !== $sha && $sha !== ( $meta['deploy_sha'] ?? '' );
	// Retry a failed or missing build at most hourly.
	$due = $due || ( empty( $meta['ok'] ) && time() - (int) ( $meta['attempted_at'] ?? 0 ) > HOUR_IN_SECONDS );
	if ( $due && ! wp_next_scheduled( HU_CHAT_KNOWLEDGE_EVENT . '_now' ) ) {
		wp_schedule_single_event( time() + 30, HU_CHAT_KNOWLEDGE_EVENT . '_now' );
	}
}
add_action( 'init', 'hu_chat_knowledge_schedule', 30 );
add_action( HU_CHAT_KNOWLEDGE_EVENT . '_now', 'hu_chat_knowledge_cron' );
