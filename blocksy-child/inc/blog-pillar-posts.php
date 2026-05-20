<?php
/**
 * One-time seeding for public pillar blog posts.
 *
 * These posts start as editorial drafts and are published once into WordPress.
 * After the first seed, the WordPress editor remains the live content owner.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the current seed version for pillar posts.
 *
 * @return string
 */
function hu_get_blog_pillar_posts_seed_version() {
	return '2026-05-21-1';
}

/**
 * Return seed data for public pillar blog posts.
 *
 * @return array<int, array<string, mixed>>
 */
function hu_get_blog_pillar_posts_seed_data() {
	return [
		[
			'title'             => 'Photovoltaik-Leads: Kaufen oder besitzen? Die ehrliche TCO-Rechnung für Fachbetriebe',
			'slug'              => 'photovoltaik-leads-kaufen-alternative',
			'seo_title'         => 'Photovoltaik-Leads: Kaufen oder besitzen?',
			'seo_description'   => 'PV-Leads kaufen wirkt billig. Entscheidend ist CPO: Abschlussquote, Tracking, Page Speed, Datenbesitz und eigene Anfrage-Infrastruktur.',
			'excerpt'           => 'Warum Photovoltaik-Betriebe nicht CPL, sondern Cost per Order rechnen müssen. Eine harte Einordnung zu Portal-Leads, gemieteten Agentur-Funnels, Tracking, Page Speed und eigener Nachfrage-Infrastruktur.',
			'categories'        => [
				[ 'name' => 'Leadgenerierung', 'slug' => 'leadgenerierung' ],
				[ 'name' => 'Solar-/Wärmepumpen Anfrage-Systeme', 'slug' => 'solar-waermepumpen-anfrage-systeme' ],
			],
			'tags'              => [ 'Photovoltaik Leads', 'Solar Leads', 'Lead-Portale', 'Cost per Order', 'Server-Side Tracking', 'Anfrage-System' ],
			'markdown_file'     => 'assets/content/blog/photovoltaik-leads-kaufen-alternative.md',
			'featured_image'    => 'assets/img/blog/photovoltaik-leads-kaufen-alternative-hero.png',
			'featured_alt_text' => 'Vergleich von gemieteten Photovoltaik-Leads und eigener Anfrage-Infrastruktur mit CPO-Rechnung.',
		],
	];
}

/**
 * Convert constrained article markdown into post HTML.
 *
 * @param string $markdown Markdown body.
 * @return string
 */
function hu_blog_pillar_markdown_to_html( $markdown ) {
	$lines             = preg_split( '/\r\n|\r|\n/', trim( (string) $markdown ) );
	$html              = '';
	$paragraph         = [];
	$list_items        = [];
	$list_type         = '';
	$blockquote_lines  = [];
	$code_lines        = [];
	$inside_code_block = false;

	$flush_list = static function() use ( &$html, &$list_items, &$list_type ) {
		if ( empty( $list_items ) || '' === $list_type ) {
			return;
		}

		$html .= '<' . $list_type . '>';
		foreach ( $list_items as $item ) {
			$html .= '<li>' . hu_blog_pillar_format_inline_markdown( $item ) . '</li>';
		}
		$html       .= '</' . $list_type . '>' . "\n";
		$list_items = [];
		$list_type  = '';
	};

	$flush_blockquote = static function() use ( &$html, &$blockquote_lines ) {
		if ( empty( $blockquote_lines ) ) {
			return;
		}

		$first_line = '';

		foreach ( $blockquote_lines as $quote_line ) {
			if ( '' !== trim( $quote_line ) ) {
				$first_line = trim( $quote_line );
				break;
			}
		}

		$is_cta = hu_blog_pillar_starts_with( $first_line, '**Stopp.' ) || hu_blog_pillar_starts_with( $first_line, '**Marktcheck-Filter:' );
		$html  .= $is_cta ? '<aside class="hu-pillar-cta" data-track-section="blog_pillar_inline_cta">' : '<blockquote>';
		$quote_section = [];

		foreach ( $blockquote_lines as $quote_line ) {
			if ( '' === trim( $quote_line ) ) {
				if ( ! empty( $quote_section ) ) {
					$html          .= '<p>' . hu_blog_pillar_format_inline_markdown( implode( ' ', $quote_section ) ) . '</p>';
					$quote_section = [];
				}
				continue;
			}

			$quote_section[] = $quote_line;
		}

		if ( ! empty( $quote_section ) ) {
			$html .= '<p>' . hu_blog_pillar_format_inline_markdown( implode( ' ', $quote_section ) ) . '</p>';
		}

		$html             .= $is_cta ? '</aside>' . "\n" : '</blockquote>' . "\n";
		$blockquote_lines = [];
	};

	$flush_para = static function() use ( &$html, &$paragraph, $flush_list, $flush_blockquote ) {
		$flush_list();
		$flush_blockquote();

		if ( empty( $paragraph ) ) {
			return;
		}

		$html     .= '<p>' . hu_blog_pillar_format_inline_markdown( implode( ' ', $paragraph ) ) . '</p>' . "\n";
		$paragraph = [];
	};

	foreach ( $lines as $raw_line ) {
		$raw_line = rtrim( (string) $raw_line );
		$line     = trim( $raw_line );

		if ( 0 === strpos( $line, '```' ) ) {
			if ( $inside_code_block ) {
				$html             .= '<pre><code>' . esc_html( implode( "\n", $code_lines ) ) . '</code></pre>' . "\n";
				$code_lines        = [];
				$inside_code_block = false;
			} else {
				$flush_para();
				$flush_list();
				$flush_blockquote();
				$inside_code_block = true;
				$code_lines        = [];
			}
			continue;
		}

		if ( $inside_code_block ) {
			$code_lines[] = $raw_line;
			continue;
		}

		if ( '' === $line ) {
			$flush_para();
			$flush_list();
			$flush_blockquote();
			continue;
		}

		if ( hu_blog_pillar_starts_with( $line, '# ' ) ) {
			$flush_para();
			$flush_list();
			$flush_blockquote();
			continue;
		}

		if ( hu_blog_pillar_starts_with( $line, '## ' ) ) {
			$flush_para();
			$flush_list();
			$flush_blockquote();
			$html .= '<h2>' . hu_blog_pillar_format_inline_markdown( substr( $line, 3 ) ) . '</h2>' . "\n";
			continue;
		}

		if ( hu_blog_pillar_starts_with( $line, '### ' ) ) {
			$flush_para();
			$flush_list();
			$flush_blockquote();
			$html .= '<h3>' . hu_blog_pillar_format_inline_markdown( substr( $line, 4 ) ) . '</h3>' . "\n";
			continue;
		}

		if ( hu_blog_pillar_starts_with( $line, '> ' ) || '>' === $line ) {
			$flush_para();
			$flush_list();
			$blockquote_lines[] = '>' === $line ? '' : substr( $line, 2 );
			continue;
		}

		if ( hu_blog_pillar_starts_with( $line, '- ' ) ) {
			$flush_para();
			$flush_blockquote();

			if ( 'ol' === $list_type ) {
				$flush_list();
			}

			$list_type    = 'ul';
			$list_items[] = substr( $line, 2 );
			continue;
		}

		if ( preg_match( '/^\d+\.\s+(.+)$/', $line, $match ) ) {
			$flush_para();
			$flush_blockquote();

			if ( 'ul' === $list_type ) {
				$flush_list();
			}

			$list_type    = 'ol';
			$list_items[] = $match[1];
			continue;
		}

		$flush_list();
		$flush_blockquote();
		$paragraph[] = $line;
	}

	if ( $inside_code_block && ! empty( $code_lines ) ) {
		$html .= '<pre><code>' . esc_html( implode( "\n", $code_lines ) ) . '</code></pre>' . "\n";
	}

	$flush_para();
	$flush_list();
	$flush_blockquote();

	return trim( $html );
}

/**
 * PHP-version-safe starts-with helper.
 *
 * @param string $haystack Full string.
 * @param string $needle   Prefix.
 * @return bool
 */
function hu_blog_pillar_starts_with( $haystack, $needle ) {
	return 0 === strpos( (string) $haystack, (string) $needle );
}

/**
 * Format constrained inline markdown for links and bold text.
 *
 * @param string $text Plain markdown text.
 * @return string
 */
function hu_blog_pillar_format_inline_markdown( $text ) {
	$links = [];
	$text  = preg_replace_callback(
		'/\[([^\]]+)\]\(([^)]+)\)/',
		static function( $matches ) use ( &$links ) {
			$href = trim( (string) $matches[2] );

			if ( ! preg_match( '~^(https?://|/|#)~', $href ) ) {
				return (string) $matches[0];
			}

			$key           = '%%HU_PILLAR_LINK_' . count( $links ) . '%%';
			$links[ $key ] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( $href ),
				esc_html( $matches[1] )
			);

			return $key;
		},
		(string) $text
	);

	$text = esc_html( (string) $text );
	$text = preg_replace( '/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text );

	foreach ( $links as $key => $link_html ) {
		$text = str_replace( esc_html( $key ), $link_html, $text );
	}

	return $text;
}

/**
 * Extract the article body from a publish-pack markdown file.
 *
 * @param string $markdown Full markdown file.
 * @return string
 */
function hu_blog_pillar_extract_article_markdown( $markdown ) {
	$markdown = str_replace( [ "\r\n", "\r" ], "\n", (string) $markdown );

	if ( preg_match( '/^#\s+.+$/m', $markdown, $match, PREG_OFFSET_CAPTURE ) ) {
		$start = (int) $match[0][1];

		$markdown = substr( $markdown, $start );
	}

	$end = strpos( $markdown, "\n## Suggested Internal Links" );
	if ( false !== $end ) {
		$markdown = substr( $markdown, 0, $end );
	}

	return trim( $markdown );
}

/**
 * Ensure a taxonomy term exists and return its ID.
 *
 * @param string $name     Term name.
 * @param string $slug     Term slug.
 * @param string $taxonomy Taxonomy name.
 * @return int
 */
function hu_blog_pillar_ensure_term_id( $name, $slug, $taxonomy ) {
	$term = term_exists( $slug, $taxonomy );

	if ( is_array( $term ) && ! empty( $term['term_id'] ) ) {
		return (int) $term['term_id'];
	}

	if ( is_int( $term ) ) {
		return $term;
	}

	$created = wp_insert_term(
		$name,
		$taxonomy,
		[
			'slug' => $slug,
		]
	);

	if ( is_wp_error( $created ) || empty( $created['term_id'] ) ) {
		return 0;
	}

	return (int) $created['term_id'];
}

/**
 * Return an administrator author ID for seeded posts.
 *
 * @return int
 */
function hu_blog_pillar_seed_author_id() {
	$users = get_users(
		[
			'role__in' => [ 'administrator' ],
			'number'   => 1,
			'fields'   => 'ID',
			'orderby'  => 'ID',
			'order'    => 'ASC',
		]
	);

	return ! empty( $users ) ? (int) $users[0] : 1;
}

/**
 * Find a post by slug across public and editor statuses.
 *
 * @param string $slug Post slug.
 * @return int
 */
function hu_blog_pillar_find_post_id_by_slug( $slug ) {
	$post_ids = get_posts(
		[
			'name'                   => (string) $slug,
			'post_type'              => 'post',
			'post_status'            => [ 'publish', 'draft', 'pending', 'future', 'private' ],
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		]
	);

	return ! empty( $post_ids ) ? (int) $post_ids[0] : 0;
}

/**
 * Find an existing seeded attachment by source asset.
 *
 * @param string $asset_relative Theme-relative asset path.
 * @return int
 */
function hu_blog_pillar_find_attachment_id_by_source_asset( $asset_relative ) {
	$attachment_ids = get_posts(
		[
			'post_type'              => 'attachment',
			'post_status'            => 'inherit',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_key'               => '_hu_blog_pillar_source_asset',
			'meta_value'             => (string) $asset_relative,
		]
	);

	return ! empty( $attachment_ids ) ? (int) $attachment_ids[0] : 0;
}

/**
 * Create or reuse a featured image attachment from a theme asset.
 *
 * @param int    $post_id        Post ID.
 * @param string $asset_relative Theme-relative source path.
 * @param string $title          Attachment title.
 * @param string $alt_text       Attachment alt text.
 * @return int
 */
function hu_blog_pillar_ensure_featured_image( $post_id, $asset_relative, $title, $alt_text ) {
	$existing_id = hu_blog_pillar_find_attachment_id_by_source_asset( $asset_relative );

	if ( $existing_id ) {
		set_post_thumbnail( $post_id, $existing_id );
		return $existing_id;
	}

	$source_path = get_stylesheet_directory() . '/' . ltrim( (string) $asset_relative, '/' );

	if ( ! is_readable( $source_path ) ) {
		return 0;
	}

	$filename    = wp_basename( $source_path );
	$file_bytes  = file_get_contents( $source_path );

	if ( false === $file_bytes ) {
		return 0;
	}

	$upload      = wp_upload_bits( $filename, null, $file_bytes );

	if ( ! empty( $upload['error'] ) || empty( $upload['file'] ) ) {
		return 0;
	}

	$filetype   = wp_check_filetype( $upload['file'] );
	$attachment = [
		'post_mime_type' => $filetype['type'] ?: 'image/png',
		'post_title'     => sanitize_text_field( (string) $title ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	];
	$attach_id  = wp_insert_attachment( wp_slash( $attachment ), $upload['file'], $post_id );

	if ( is_wp_error( $attach_id ) || ! $attach_id ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';

	$attach_data = wp_generate_attachment_metadata( (int) $attach_id, $upload['file'] );
	wp_update_attachment_metadata( (int) $attach_id, $attach_data );
	update_post_meta( (int) $attach_id, '_wp_attachment_image_alt', sanitize_text_field( (string) $alt_text ) );
	update_post_meta( (int) $attach_id, '_hu_blog_pillar_source_asset', (string) $asset_relative );
	set_post_thumbnail( $post_id, (int) $attach_id );

	return (int) $attach_id;
}

/**
 * Publish pillar posts once.
 *
 * @return void
 */
function hu_maybe_seed_blog_pillar_posts() {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = hu_get_blog_pillar_posts_seed_version();
	$option_key = 'hu_blog_pillar_posts_seed_version';

	if ( get_option( $option_key ) === $version ) {
		return;
	}

	$author_id = hu_blog_pillar_seed_author_id();
	$all_done  = true;

	foreach ( hu_get_blog_pillar_posts_seed_data() as $post ) {
		$slug          = sanitize_title( (string) $post['slug'] );
		$markdown_path = get_stylesheet_directory() . '/' . ltrim( (string) $post['markdown_file'], '/' );

		if ( ! is_readable( $markdown_path ) ) {
			$all_done = false;
			continue;
		}

		$markdown      = hu_blog_pillar_extract_article_markdown( (string) file_get_contents( $markdown_path ) );
		$content       = hu_blog_pillar_markdown_to_html( $markdown );
		$existing_id   = hu_blog_pillar_find_post_id_by_slug( $slug );
		$category_ids  = [];
		$post_data     = [
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_title'    => (string) $post['title'],
			'post_name'     => $slug,
			'post_content'  => $content,
			'post_excerpt'  => (string) $post['excerpt'],
			'post_author'   => $author_id,
			'comment_status' => 'closed',
			'ping_status'   => 'closed',
		];

		foreach ( (array) $post['categories'] as $category ) {
			$category_id = hu_blog_pillar_ensure_term_id(
				(string) $category['name'],
				(string) $category['slug'],
				'category'
			);

			if ( $category_id ) {
				$category_ids[] = $category_id;
			}
		}

		if ( empty( $category_ids ) ) {
			$all_done = false;
			continue;
		}

		if ( $existing_id ) {
			$is_seeded     = '1' === (string) get_post_meta( $existing_id, '_hu_blog_pillar_seeded', true );
			$should_update = $is_seeded || 'publish' !== (string) get_post_status( $existing_id );

			if ( $should_update ) {
				$post_data['ID'] = $existing_id;
				$result          = wp_update_post( wp_slash( $post_data ), true );
			} else {
				$result = $existing_id;
			}
		} else {
			$result = wp_insert_post( wp_slash( $post_data ), true );
		}

		if ( is_wp_error( $result ) || ! $result ) {
			$all_done = false;
			continue;
		}

		$post_id = (int) $result;

		wp_set_post_terms( $post_id, $category_ids, 'category', false );
		wp_set_post_terms( $post_id, array_map( 'sanitize_text_field', (array) $post['tags'] ), 'post_tag', false );

		update_post_meta( $post_id, 'seo_title', sanitize_text_field( (string) $post['seo_title'] ) );
		update_post_meta( $post_id, 'seo_description', sanitize_text_field( (string) $post['seo_description'] ) );
		update_post_meta( $post_id, '_hu_blog_pillar_seeded', '1' );
		update_post_meta( $post_id, '_hu_blog_pillar_seed_version', $version );

		$image_id = hu_blog_pillar_ensure_featured_image(
			$post_id,
			(string) $post['featured_image'],
			(string) $post['title'],
			(string) $post['featured_alt_text']
		);

		if ( ! $image_id ) {
			$all_done = false;
		}
	}

	if ( $all_done ) {
		update_option( $option_key, $version, false );
	}
}
add_action( 'init', 'hu_maybe_seed_blog_pillar_posts', 32 );
