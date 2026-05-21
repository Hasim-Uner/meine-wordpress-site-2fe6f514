<?php
/**
 * Post rating endpoint + admin meta-box.
 *
 * Lightweight "was this article helpful?" widget. Stores aggregated
 * counts in post meta and the last few free-text feedbacks so the
 * author can read them without leaving WP-Admin. No third-party
 * dependency, no PII beyond what visitors voluntarily type.
 *
 * REST: POST /wp-json/nexus/v1/post-rating
 *   body: { postId, rating ("yes" | "no"), feedback, nonce }
 *
 * Action hook for downstream automation (CRM, n8n, Brevo):
 *   do_action( 'nexus_post_rating_received', $post_id, $rating, $feedback, $context );
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NEXUS_POST_RATING_META_COUNT_YES = '_nexus_rating_count_yes';
const NEXUS_POST_RATING_META_COUNT_NO  = '_nexus_rating_count_no';
const NEXUS_POST_RATING_META_FEEDBACK  = '_nexus_rating_feedback';
const NEXUS_POST_RATING_FEEDBACK_LIMIT = 25;
const NEXUS_POST_RATING_TEXT_LIMIT     = 1500;
const NEXUS_POST_RATING_RATE_WINDOW    = 600;   // 10 min
const NEXUS_POST_RATING_RATE_LIMIT     = 6;     // max submissions per window per IP+post

/**
 * Register the REST endpoint.
 *
 * @return void
 */
function nexus_register_post_rating_route() {
	register_rest_route(
		'nexus/v1',
		'/post-rating',
		[
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'nexus_handle_post_rating_submission',
			'permission_callback' => '__return_true',
		]
	);
}
add_action( 'rest_api_init', 'nexus_register_post_rating_route' );

/**
 * Handle rating submissions.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response
 */
function nexus_handle_post_rating_submission( WP_REST_Request $request ) {
	$payload = $request->get_json_params();
	if ( ! is_array( $payload ) || empty( $payload ) ) {
		$payload = $request->get_body_params();
	}

	$post_id  = isset( $payload['postId'] ) ? absint( $payload['postId'] ) : 0;
	$rating   = isset( $payload['rating'] ) ? sanitize_key( $payload['rating'] ) : '';
	$feedback = isset( $payload['feedback'] ) ? (string) $payload['feedback'] : '';

	if ( ! $post_id || 'publish' !== get_post_status( $post_id ) ) {
		return new WP_REST_Response(
			[ 'ok' => false, 'error' => 'invalid_post' ],
			400
		);
	}

	if ( 'yes' !== $rating && 'no' !== $rating ) {
		return new WP_REST_Response(
			[ 'ok' => false, 'error' => 'invalid_rating' ],
			400
		);
	}

	if ( nexus_post_rating_is_rate_limited( $post_id ) ) {
		return new WP_REST_Response(
			[ 'ok' => false, 'error' => 'rate_limited' ],
			429
		);
	}

	// Counter increment.
	$meta_key = 'yes' === $rating ? NEXUS_POST_RATING_META_COUNT_YES : NEXUS_POST_RATING_META_COUNT_NO;
	$current  = (int) get_post_meta( $post_id, $meta_key, true );
	update_post_meta( $post_id, $meta_key, $current + 1 );

	// Optional feedback (only stored if non-empty).
	$feedback = trim( wp_strip_all_tags( $feedback ) );
	if ( '' !== $feedback ) {
		if ( function_exists( 'mb_substr' ) ) {
			$feedback = mb_substr( $feedback, 0, NEXUS_POST_RATING_TEXT_LIMIT );
		} else {
			$feedback = substr( $feedback, 0, NEXUS_POST_RATING_TEXT_LIMIT );
		}

		$entries = get_post_meta( $post_id, NEXUS_POST_RATING_META_FEEDBACK, true );
		if ( ! is_array( $entries ) ) {
			$entries = [];
		}

		array_unshift(
			$entries,
			[
				'rating'    => $rating,
				'text'      => $feedback,
				'timestamp' => time(),
				'ip_hash'   => nexus_post_rating_hash_ip(),
			]
		);

		$entries = array_slice( $entries, 0, NEXUS_POST_RATING_FEEDBACK_LIMIT );
		update_post_meta( $post_id, NEXUS_POST_RATING_META_FEEDBACK, $entries );
	}

	/**
	 * Fires after a rating is recorded so downstream automations
	 * (CRM, Brevo, n8n) can react. Receives:
	 *   $post_id, $rating ('yes'|'no'), $feedback (string), $context (array)
	 */
	do_action(
		'nexus_post_rating_received',
		$post_id,
		$rating,
		$feedback,
		[
			'post_title' => get_the_title( $post_id ),
			'permalink'  => get_permalink( $post_id ),
		]
	);

	return new WP_REST_Response(
		[
			'ok'      => true,
			'message' => __( 'Danke für Ihr Feedback.', 'blocksy-child' ),
		],
		200
	);
}

/**
 * Hash the requester IP so we can rate-limit without storing PII.
 *
 * @return string
 */
function nexus_post_rating_hash_ip() {
	$ip = '';
	if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = (string) $_SERVER['REMOTE_ADDR']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	}
	if ( '' === $ip ) {
		return '';
	}
	return substr( hash( 'sha256', $ip . wp_salt( 'auth' ) ), 0, 16 );
}

/**
 * Rate limit check per IP+post using a transient.
 *
 * @param int $post_id Post ID.
 * @return bool True if blocked.
 */
function nexus_post_rating_is_rate_limited( $post_id ) {
	$ip_hash = nexus_post_rating_hash_ip();
	if ( '' === $ip_hash ) {
		return false;
	}
	$key   = 'nexus_rate_post_rating_' . $post_id . '_' . $ip_hash;
	$count = (int) get_transient( $key );
	if ( $count >= NEXUS_POST_RATING_RATE_LIMIT ) {
		return true;
	}
	set_transient( $key, $count + 1, NEXUS_POST_RATING_RATE_WINDOW );
	return false;
}

/**
 * Return rating totals for a post.
 *
 * @param int $post_id Post ID.
 * @return array{yes:int, no:int, total:int, ratio:float}
 */
function nexus_get_post_rating_totals( $post_id ) {
	$yes = (int) get_post_meta( $post_id, NEXUS_POST_RATING_META_COUNT_YES, true );
	$no  = (int) get_post_meta( $post_id, NEXUS_POST_RATING_META_COUNT_NO, true );
	$total = $yes + $no;
	$ratio = $total > 0 ? round( ( $yes / $total ) * 100, 1 ) : 0.0;
	return [
		'yes'   => $yes,
		'no'    => $no,
		'total' => $total,
		'ratio' => $ratio,
	];
}

/**
 * Register admin meta-box on posts.
 *
 * @return void
 */
function nexus_register_post_rating_metabox() {
	add_meta_box(
		'nexus-post-rating',
		__( 'Artikel-Bewertungen', 'blocksy-child' ),
		'nexus_render_post_rating_metabox',
		'post',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'nexus_register_post_rating_metabox' );

/**
 * Render the admin meta-box.
 *
 * @param WP_Post $post Post object.
 * @return void
 */
function nexus_render_post_rating_metabox( $post ) {
	$totals   = nexus_get_post_rating_totals( $post->ID );
	$entries  = get_post_meta( $post->ID, NEXUS_POST_RATING_META_FEEDBACK, true );
	$entries  = is_array( $entries ) ? $entries : [];

	echo '<p style="margin:0 0 0.6rem;font-size:13px;color:#555;">';
	echo esc_html__( 'Aggregierte Lesermeinung zu diesem Artikel.', 'blocksy-child' );
	echo '</p>';

	echo '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;text-align:center;margin-bottom:0.8rem;">';
	echo '<div style="padding:8px 6px;border:1px solid #ddd;border-radius:8px;"><div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.08em;">Hilfreich</div><div style="font-size:18px;font-weight:700;color:#1e7a3f;">' . esc_html( (string) $totals['yes'] ) . '</div></div>';
	echo '<div style="padding:8px 6px;border:1px solid #ddd;border-radius:8px;"><div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.08em;">Nicht</div><div style="font-size:18px;font-weight:700;color:#a83434;">' . esc_html( (string) $totals['no'] ) . '</div></div>';
	echo '<div style="padding:8px 6px;border:1px solid #ddd;border-radius:8px;"><div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.08em;">Ratio</div><div style="font-size:18px;font-weight:700;color:#b46a3c;">' . esc_html( (string) $totals['ratio'] ) . '%</div></div>';
	echo '</div>';

	if ( empty( $entries ) ) {
		echo '<p style="margin:0;font-size:12px;color:#888;">' . esc_html__( 'Noch kein Freitext-Feedback eingegangen.', 'blocksy-child' ) . '</p>';
		return;
	}

	echo '<p style="margin:0 0 0.4rem;font-size:12px;color:#555;font-weight:600;">' . esc_html__( 'Letzte Rückmeldungen', 'blocksy-child' ) . '</p>';
	echo '<ul style="list-style:none;margin:0;padding:0;max-height:320px;overflow:auto;">';
	foreach ( $entries as $entry ) {
		$rating = isset( $entry['rating'] ) ? (string) $entry['rating'] : '';
		$text   = isset( $entry['text'] ) ? (string) $entry['text'] : '';
		$time   = isset( $entry['timestamp'] ) ? (int) $entry['timestamp'] : 0;
		$badge  = 'yes' === $rating ? '👍' : '👎';
		$color  = 'yes' === $rating ? '#1e7a3f' : '#a83434';
		echo '<li style="padding:8px;border:1px solid #eee;border-radius:6px;margin-bottom:6px;background:#fafafa;">';
		echo '<div style="font-size:11px;color:' . esc_attr( $color ) . ';margin-bottom:4px;">' . esc_html( $badge ) . ' ';
		echo esc_html( $time ? wp_date( 'd.m.Y · H:i', $time ) : '' );
		echo '</div>';
		echo '<div style="font-size:13px;line-height:1.4;color:#222;white-space:pre-wrap;">' . esc_html( $text ) . '</div>';
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * Add a sortable "Bewertung" column to the posts list table.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function nexus_post_rating_columns( $columns ) {
	$columns['nexus_rating'] = __( 'Bewertung', 'blocksy-child' );
	return $columns;
}
add_filter( 'manage_post_posts_columns', 'nexus_post_rating_columns' );

/**
 * Render the rating column.
 *
 * @param string $column  Column name.
 * @param int    $post_id Post ID.
 * @return void
 */
function nexus_post_rating_render_column( $column, $post_id ) {
	if ( 'nexus_rating' !== $column ) {
		return;
	}
	$totals = nexus_get_post_rating_totals( $post_id );
	if ( 0 === $totals['total'] ) {
		echo '<span style="color:#aaa;">—</span>';
		return;
	}
	printf(
		'<span title="%1$s">%2$d&nbsp;👍 / %3$d&nbsp;👎 · %4$s%%</span>',
		esc_attr__( 'Hilfreich / Nicht hilfreich / Ratio', 'blocksy-child' ),
		(int) $totals['yes'],
		(int) $totals['no'],
		esc_html( (string) $totals['ratio'] )
	);
}
add_action( 'manage_post_posts_custom_column', 'nexus_post_rating_render_column', 10, 2 );
