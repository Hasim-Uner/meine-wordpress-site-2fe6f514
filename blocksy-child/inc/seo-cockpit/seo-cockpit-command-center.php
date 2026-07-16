<?php
/**
 * Revenue Command Center helpers for the SEO Cockpit.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the option name for command-center row statuses.
 *
 * @return string
 */
function nexus_get_revenue_command_center_status_option_name() {
	return 'nexus_revenue_command_center_statuses';
}

/**
 * Return the allowed command-center statuses.
 *
 * @return array<string, string>
 */
function nexus_get_revenue_command_center_status_labels() {
	return [
		'new'         => 'Neu',
		'today'       => 'Heute',
		'in_progress' => 'In Arbeit',
		'done'        => 'Erledigt',
		'ignored'     => 'Ignorieren',
	];
}

/**
 * Return stored row statuses.
 *
 * @return array<string, string>
 */
function nexus_get_revenue_command_center_statuses() {
	$statuses = get_option( nexus_get_revenue_command_center_status_option_name(), [] );
	$labels   = nexus_get_revenue_command_center_status_labels();
	$clean    = [];

	if ( ! is_array( $statuses ) ) {
		return [];
	}

	foreach ( $statuses as $item_id => $status ) {
		$item_id = sanitize_key( (string) $item_id );
		$status  = sanitize_key( (string) $status );

		if ( '' === $item_id || ! isset( $labels[ $status ] ) ) {
			continue;
		}

		$clean[ $item_id ] = $status;
	}

	return $clean;
}

/**
 * Persist one command-center row status from the admin UI.
 *
 * @return void
 */
function nexus_handle_revenue_command_center_status_action() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( esc_html__( 'Du hast keine Berechtigung für diese Aktion.', 'blocksy-child' ) );
	}

	check_admin_referer( 'nexus_revenue_command_center_status' );

	$item_id  = isset( $_POST['item_id'] ) ? sanitize_key( (string) wp_unslash( $_POST['item_id'] ) ) : '';
	$status   = isset( $_POST['item_status'] ) ? sanitize_key( (string) wp_unslash( $_POST['item_status'] ) ) : '';
	$range    = isset( $_POST['range'] ) ? absint( wp_unslash( $_POST['range'] ) ) : 28;
	$range    = in_array( $range, nexus_get_seo_cockpit_allowed_ranges(), true ) ? $range : 28;
	$detail   = isset( $_POST['detail_url'] ) ? nexus_normalize_seo_cockpit_url( (string) wp_unslash( $_POST['detail_url'] ) ) : '';
	$redirect = '' !== $detail ? nexus_get_seo_cockpit_detail_url( $detail, [ 'range' => $range ] ) : nexus_get_seo_cockpit_dashboard_url( [ 'range' => $range ] );
	$labels   = nexus_get_revenue_command_center_status_labels();

	if ( '' !== $item_id && isset( $labels[ $status ] ) ) {
		$statuses             = nexus_get_revenue_command_center_statuses();
		$statuses[ $item_id ] = $status;
		update_option( nexus_get_revenue_command_center_status_option_name(), $statuses, false );
	}

	wp_safe_redirect( add_query_arg( 'nexus_seo_notice', 'revenue_status_saved', $redirect ) );
	exit;
}
add_action( 'admin_post_nexus_revenue_command_center_status', 'nexus_handle_revenue_command_center_status_action' );

/**
 * Return one stable row id.
 *
 * @param string $type   Row type.
 * @param string $target Row target.
 * @return string
 */
function nexus_get_revenue_command_center_item_id( $type, $target ) {
	return substr( sanitize_key( (string) $type . '_' . md5( (string) $target ) ), 0, 64 );
}

/**
 * Return one score bucket.
 *
 * @param int $score Revenue score.
 * @return string
 */
function nexus_get_revenue_command_center_priority_bucket( $score ) {
	$score = absint( $score );

	if ( $score >= 78 ) {
		return 'critical';
	}

	if ( $score >= 60 ) {
		return 'high';
	}

	if ( $score >= 42 ) {
		return 'medium';
	}

	return 'low';
}

/**
 * Return one confidence label.
 *
 * @param int $score Confidence score.
 * @return string
 */
function nexus_get_revenue_command_center_confidence_label( $score ) {
	$score = absint( $score );

	if ( $score >= 8 ) {
		return 'hoch';
	}

	if ( $score >= 5 ) {
		return 'mittel';
	}

	return 'niedrig';
}

/**
 * Return the funnel role for a page role.
 *
 * @param string $page_role Page role.
 * @return string
 */
function nexus_get_revenue_command_center_funnel_role( $page_role ) {
	$map = [
		'audit'       => 'Marktcheck',
		'contact'     => 'Handoff',
		'service'     => 'Money Page',
		'seo_subpage' => 'Money/Cluster',
		'results'     => 'Proof',
		'system'      => 'Delivery/System',
		'home'        => 'Entry',
		'hub'         => 'Assist',
		'blog'        => 'Assist',
		'about'       => 'Trust',
		'page'        => 'Support',
		'utility'     => 'Utility',
		'legacy'      => 'Legacy',
		'legal'       => 'Legal',
		'unknown'     => 'Unklar',
	];

	return $map[ sanitize_key( (string) $page_role ) ] ?? 'Unklar';
}

/**
 * Return whether a URL is close to the E3 proof layer.
 *
 * @param string $url Frontend URL.
 * @return bool
 */
function nexus_revenue_command_center_is_e3_adjacent_url( $url ) {
	$path = (string) wp_parse_url( (string) $url, PHP_URL_PATH );

	return false !== strpos( $path, 'case-study-solar-leadgenerierung' ) || false !== strpos( $path, 'e3' ) || false !== strpos( $path, 'ergebnisse' ) || false !== strpos( $path, 'case' );
}

/**
 * Resolve a concrete next-best action for one SEO insight.
 *
 * @param array<string, mixed> $insight Insight row.
 * @param array<string, mixed> $context Page context.
 * @return array<string, string>
 */
function nexus_resolve_revenue_command_center_insight_action( $insight, $context = [] ) {
	$type      = strtoupper( (string) ( $insight['type'] ?? '' ) );
	$page_role = sanitize_key( (string) ( $insight['page_role'] ?? '' ) );
	$label     = (string) ( $insight['label'] ?? '' );
	$action    = [
		'next_action'       => (string) ( $insight['recommended_action'] ?? 'URL manuell prüfen und nächsten sinnvollen Revenue-Hebel festlegen.' ),
		'expected_leverage' => 'mittlerer Hebel',
		'effort'            => 'M',
		'risk'              => 'mittel',
		'type'              => 'Page',
		'repo_fixable'      => 'ja',
		'manual'            => 'nein',
	];

	if ( 'MONEY_PAGE_UNDERPERFORMING' === $type ) {
		$action['type']              = 'CTA';
		$action['next_action']       = 'CTA, Proof/E3-Nähe und Marktcheck-Brücke auf dieser Seite prüfen.';
		$action['expected_leverage'] = 'hoher Anfrage-Hebel';
		$action['risk']              = 'mittel';
	} elseif ( 'WEAK_FUNNEL_BRIDGE' === $type ) {
		$action['next_action']       = 'Bridge-Link zum Marktcheck und passenden Proof-Anker im Content ergänzen.';
		$action['expected_leverage'] = 'Assist- zu Anfrage-Hebel';
		$action['effort']            = 'S';
	} elseif ( 'ORPHAN_VALUE_PAGE' === $type ) {
		$action['next_action']       = 'Interne Links aus passenden Hubs, Blog-Bridges oder Proof-Seiten stärken.';
		$action['expected_leverage'] = 'Ranking- und Funnel-Hebel';
		$action['effort']            = 'S';
	} elseif ( 'CTR_OPPORTUNITY' === $type || 'SNIPPET_WEAKNESS' === $type || 'QUICK_WIN' === $type ) {
		$action['next_action']       = 'Snippet gegen Suchintention, Marktcheck-Nutzen und Proof-Signal testen.';
		$action['expected_leverage'] = 'qualifizierter Klick-Hebel';
		$action['risk']              = 'niedrig';
	} elseif ( 'DECAY' === $type ) {
		$action['next_action']       = 'Seite manuell in GSC prüfen: Query-Verschiebung, Snippet, interne Links, keine blinde Inhaltsänderung.';
		$action['expected_leverage'] = 'Schadensbegrenzung';
		$action['risk']              = 'mittel';
		$action['manual']            = 'ja';
	} elseif ( 'INDEXING_MISMATCH' === $type ) {
		$action['next_action']       = 'noindex, Canonical, Sitemap und Legacy-Status kontrollieren.';
		$action['expected_leverage'] = 'technischer Schutz';
		$action['risk']              = 'hoch';
	} elseif ( 'POSSIBLE_CANNIBALIZATION' === $type ) {
		$action['next_action']       = 'Primärziel festlegen; interne Links und Snippets auf diese URL konzentrieren.';
		$action['expected_leverage'] = 'Intent-Bündelung';
		$action['risk']              = 'mittel';
	} elseif ( 'LOW_SIGNAL' === $type ) {
		$action['next_action']       = 'URL beobachten; nur Links oder Proof-Brücke stärken, wenn sie funnelnah ist.';
		$action['expected_leverage'] = 'niedriger bis mittlerer Hebel';
		$action['effort']            = 'S';
		$action['risk']              = 'niedrig';
	}

	if ( in_array( $page_role, [ 'blog', 'hub' ], true ) && false === strpos( (string) $action['next_action'], 'Marktcheck' ) ) {
		$action['next_action'] .= ' Marktcheck-Brücke nur ergänzen, wenn der Kontext wirklich kaufnah ist.';
	}

	if ( ! empty( $context['edit_link'] ) && in_array( $type, [ 'SNIPPET_WEAKNESS', 'CTR_OPPORTUNITY' ], true ) ) {
		$action['manual'] = 'ja';
	}

	if ( '' === $label && 'Page' === $action['type'] ) {
		$action['type'] = 'Manual';
	}

	return $action;
}

/**
 * Score one page/insight action by revenue relevance.
 *
 * @param array<string, mixed> $insight Insight row.
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @return array<string, mixed>
 */
function nexus_score_revenue_command_center_insight( $insight, $snapshot ) {
	$url        = nexus_normalize_seo_cockpit_url( (string) ( $insight['url'] ?? '' ) );
	$context    = isset( $snapshot['page_contexts'][ $url ] ) && is_array( $snapshot['page_contexts'][ $url ] ) ? $snapshot['page_contexts'][ $url ] : [];
	$page_role  = sanitize_key( (string) ( $insight['page_role'] ?? ( function_exists( 'nexus_get_seo_cockpit_page_role' ) ? nexus_get_seo_cockpit_page_role( $context, $url ) : 'unknown' ) ) );
	$role_score = function_exists( 'nexus_get_seo_cockpit_page_role_scores' ) ? nexus_get_seo_cockpit_page_role_scores( $page_role ) : [ 'business' => 5, 'funnel' => 3 ];
	$lead_page  = function_exists( 'nexus_get_seo_cockpit_lead_page_for_url' ) ? nexus_get_seo_cockpit_lead_page_for_url( $snapshot, $url ) : [];
	$metrics    = is_array( $insight['metrics'] ?? null ) ? $insight['metrics'] : [];
	$type       = strtoupper( (string) ( $insight['type'] ?? '' ) );
	$impressions = (float) ( $metrics['impressions'] ?? $metrics['total_impressions'] ?? 0 );
	$clicks      = (float) ( $metrics['clicks'] ?? $metrics['current_clicks'] ?? 0 );
	$lead_signal = function_exists( 'nexus_get_seo_cockpit_lead_signal_score' ) ? nexus_get_seo_cockpit_lead_signal_score( $lead_page ) : 0;
	$conversion_gap = 0;

	if ( $impressions >= 100 && empty( $lead_page['lifetime']['requests'] ) && in_array( $page_role, [ 'audit', 'service', 'seo_subpage', 'home', 'results' ], true ) ) {
		$conversion_gap = 16;
	} elseif ( $impressions >= 60 && empty( $lead_page['lifetime']['requests'] ) && in_array( $page_role, [ 'blog', 'hub' ], true ) ) {
		$conversion_gap = 8;
	}

	$assist = 0;
	if ( ! empty( $lead_page['attribution_modes']['entry'] ) ) {
		$assist += 7;
	}
	if ( ! empty( $lead_page['attribution_modes']['previous'] ) ) {
		$assist += 5;
	}

	$proof = nexus_revenue_command_center_is_e3_adjacent_url( $url ) || 'results' === $page_role ? 8 : 0;
	$decay = 'DECAY' === $type ? 8 : 0;
	$risk_penalty = 0;

	if ( in_array( $type, [ 'INDEXING_MISMATCH', 'POSSIBLE_CANNIBALIZATION' ], true ) ) {
		$risk_penalty = 6;
	} elseif ( in_array( $page_role, [ 'audit', 'service', 'seo_subpage' ], true ) ) {
		$risk_penalty = 3;
	}

	$confidence = (int) ( $insight['priority_parts']['confidence'] ?? nexus_get_seo_cockpit_confidence_score( $impressions, $clicks, $context ) );
	$components = [
		'lead_signal'    => min( 24, (int) round( $lead_signal * 1.5 ) ),
		'funnel'         => min( 18, (int) round( (int) ( $role_score['funnel'] ?? 0 ) * 1.2 ) ),
		'business'       => min( 18, (int) ( $role_score['business'] ?? 0 ) ),
		'search_demand'  => min( 10, (int) round( nexus_get_seo_cockpit_demand_score( $impressions ) / 2 ) ),
		'conversion_gap' => $conversion_gap,
		'assist'         => min( 12, $assist ),
		'proof'          => $proof,
		'decay'          => $decay,
		'confidence'     => $confidence,
		'risk_penalty'   => -1 * $risk_penalty,
	];
	$score = max( 0, min( 100, array_sum( $components ) ) );

	return [
		'score'      => $score,
		'bucket'     => nexus_get_revenue_command_center_priority_bucket( $score ),
		'components' => $components,
		'confidence' => $confidence,
		'lead_page'  => $lead_page,
		'context'    => $context,
		'page_role'  => $page_role,
	];
}

/**
 * Build one normalized command-center row.
 *
 * @param array<string, mixed> $row Raw row.
 * @param array<string, string> $stored_statuses Stored statuses.
 * @return array<string, mixed>
 */
function nexus_build_revenue_command_center_row( $row, $stored_statuses ) {
	$type   = sanitize_key( (string) ( $row['type'] ?? 'manual' ) );
	$target = (string) ( $row['target_url'] ?? $row['target_label'] ?? $row['problem'] ?? '' );
	$id     = ! empty( $row['id'] ) ? sanitize_key( (string) $row['id'] ) : nexus_get_revenue_command_center_item_id( $type, $target );
	$status = isset( $stored_statuses[ $id ] ) ? $stored_statuses[ $id ] : sanitize_key( (string) ( $row['status'] ?? 'new' ) );

	return array_merge(
		[
			'id'                => $id,
			'priority_score'    => 0,
			'priority_bucket'   => 'low',
			'type'              => 'Manual',
			'target_label'      => '',
			'target_url'        => '',
			'admin_url'         => '',
			'funnel_role'       => 'Unklar',
			'problem'           => '',
			'why_now'           => '',
			'next_action'       => '',
			'expected_leverage' => 'unklar',
			'effort'            => 'M',
			'risk'              => 'mittel',
			'repo_fixable'      => 'nein',
			'manual'            => 'ja',
			'status'            => $status,
			'confidence'        => 'niedrig',
			'data_basis'        => '',
			'section'           => 'manual_checks',
		],
		$row,
		[
			'id'     => $id,
			'status' => $status,
		]
	);
}

/**
 * Build lead follow-up rows from the Audit CRM.
 *
 * @param array<string, string> $stored_statuses Stored row statuses.
 * @param int                  $limit Max rows.
 * @return array<int, array<string, mixed>>
 */
function nexus_get_revenue_command_center_lead_rows( $stored_statuses, $limit = 12 ) {
	if ( ! post_type_exists( 'nexus_review_request' ) ) {
		return [];
	}

	$posts = get_posts(
		[
			'post_type'              => 'nexus_review_request',
			'post_status'            => 'private',
			'posts_per_page'         => max( 1, absint( $limit ) ),
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		]
	);
	$rows  = [];
	$now   = current_time( 'timestamp' );

	foreach ( $posts as $post ) {
		if ( ! ( $post instanceof WP_Post ) ) {
			continue;
		}

		$status        = sanitize_key( (string) get_post_meta( $post->ID, '_nexus_review_status', true ) );
		$source        = sanitize_key( (string) get_post_meta( $post->ID, '_nexus_review_source', true ) );
		$company       = (string) get_post_meta( $post->ID, '_nexus_review_company', true );
		$name          = (string) get_post_meta( $post->ID, '_nexus_review_name', true );
		$domain        = (string) get_post_meta( $post->ID, '_nexus_review_domain', true );
		$due_at        = (int) get_post_meta( $post->ID, '_nexus_review_due_at', true );
		$landing_url   = (string) get_post_meta( $post->ID, '_nexus_review_landing_page_url', true );
		$entry_url     = (string) get_post_meta( $post->ID, '_nexus_review_entry_page_url', true );
		$previous_url  = (string) get_post_meta( $post->ID, '_nexus_review_previous_internal_url', true );
		$qualification = sanitize_key( (string) get_post_meta( $post->ID, '_nexus_review_qualification_status', true ) );
		$target        = nexus_get_seo_cockpit_review_request_attribution_target( $entry_url, $previous_url, $landing_url, $source );
		$target_url    = (string) ( $target['url'] ?? '' );
		$target_mode   = sanitize_key( (string) ( $target['mode'] ?? '' ) );
		$is_overdue    = $due_at > 0 && $due_at < $now && ! in_array( $status, [ 'sent', 'won', 'archived' ], true );
		$is_unmapped   = '' === $target_url;
		$score         = 58;

		if ( '' === $status || 'new' === $status ) {
			$score += 18;
		} elseif ( 'in_review' === $status ) {
			$score += 12;
		} elseif ( 'sent' === $status ) {
			$score += 4;
		} elseif ( 'won' === $status ) {
			$score -= 18;
		}

		if ( $is_overdue ) {
			$score += 12;
		}

		if ( in_array( $qualification, [ 'green', 'yellow' ], true ) ) {
			$score += 8;
		}

		if ( $is_unmapped ) {
			$score += 6;
		}

		$score = max( 0, min( 100, $score ) );
		$label = trim( $company ?: $domain ?: $name ?: get_the_title( $post ) );

		if ( '' === $label ) {
			$label = 'Marktcheck #' . $post->ID;
		}

		$next_action = 'Lead heute nachfassen';
		if ( '' === $status ) {
			$next_action = 'Lead-Status aktualisieren';
		} elseif ( 'in_review' === $status ) {
			$next_action = 'Marktcheck-Follow-up prüfen';
		} elseif ( 'sent' === $status ) {
			$next_action = 'Antwort oder nächsten Kontaktpunkt nachhalten';
		} elseif ( 'won' === $status ) {
			$next_action = 'Won-Signal für Attribution und Proof-Architektur auswerten';
		}

		$problem_parts = [];
		if ( '' === $status ) {
			$problem_parts[] = 'kein Status';
		} else {
			$problem_parts[] = 'Status: ' . nexus_get_seo_cockpit_lead_status_label( $status );
		}
		if ( $is_overdue ) {
			$problem_parts[] = 'fällig';
		}
		if ( $is_unmapped ) {
			$problem_parts[] = 'nicht attribuiert';
		}

		$rows[] = nexus_build_revenue_command_center_row(
			[
				'id'                => nexus_get_revenue_command_center_item_id( 'lead', (string) $post->ID ),
				'priority_score'    => $score,
				'priority_bucket'   => nexus_get_revenue_command_center_priority_bucket( $score ),
				'type'              => 'Lead',
				'target_label'      => $label,
				'target_url'        => $target_url,
				'admin_url'         => get_edit_post_link( $post->ID, '' ),
				'funnel_role'       => 'Marktcheck / Sales-Handoff',
				'problem'           => implode( ' / ', $problem_parts ),
				'why_now'           => $is_overdue ? 'Offener Marktcheck kann Nachfrage verlieren, wenn Follow-up zu spät kommt.' : 'Lead-Signal ist näher am Umsatz als reine Sichtbarkeit.',
				'next_action'       => $next_action,
				'expected_leverage' => 'direkter Umsatzhebel',
				'effort'            => 'S',
				'risk'              => 'niedrig',
				'repo_fixable'      => 'nein',
				'manual'            => 'ja',
				'confidence'        => 'hoch',
				'data_basis'        => sprintf(
					'CRM: %s%s%s',
					nexus_get_seo_cockpit_lead_source_label( $source ),
					'' !== $target_mode ? ' / ' . nexus_get_seo_cockpit_lead_mode_label( $target_mode ) : '',
					$due_at > 0 ? ' / fällig ' . wp_date( 'd.m.Y', $due_at ) : ''
				),
				'section'           => 'lead_followup',
			],
			$stored_statuses
		);
	}

	return $rows;
}

/**
 * Build page, conversion-leak and manual rows.
 *
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @param array<string, string> $stored_statuses Stored row statuses.
 * @return array<int, array<string, mixed>>
 */
function nexus_get_revenue_command_center_page_rows( $snapshot, $stored_statuses ) {
	$rows          = [];
	$gap_seen_urls = [];

	foreach ( array_slice( (array) ( $snapshot['insights'] ?? [] ), 0, 16 ) as $insight ) {
		if ( ! is_array( $insight ) ) {
			continue;
		}

		$url = nexus_normalize_seo_cockpit_url( (string) ( $insight['url'] ?? '' ) );
		if ( '' === $url ) {
			continue;
		}

		$score_data  = nexus_score_revenue_command_center_insight( $insight, $snapshot );
		$context     = is_array( $score_data['context'] ?? null ) ? $score_data['context'] : [];
		$page_role   = (string) ( $score_data['page_role'] ?? 'unknown' );
		$action      = nexus_resolve_revenue_command_center_insight_action( $insight, $context );
		$lead_page   = is_array( $score_data['lead_page'] ?? null ) ? $score_data['lead_page'] : [];
		$section     = (int) ( $score_data['components']['conversion_gap'] ?? 0 ) > 0 ? 'conversion_leaks' : 'page_queue';
		$page_label  = ! empty( $context['post_title'] ) ? (string) $context['post_title'] : nexus_get_seo_cockpit_short_url( $url );
		$lead_note   = ! empty( $lead_page['lifetime']['requests'] ) ? sprintf( ' / Leads: %d gesamt', (int) $lead_page['lifetime']['requests'] ) : ' / keine Lead-Signale';

		$rows[] = nexus_build_revenue_command_center_row(
			[
				'id'                => nexus_get_revenue_command_center_item_id( 'page_' . (string) ( $insight['type'] ?? 'insight' ), $url . '|' . (string) ( $insight['query'] ?? '' ) ),
				'priority_score'    => (int) $score_data['score'],
				'priority_bucket'   => (string) $score_data['bucket'],
				'type'              => (string) $action['type'],
				'target_label'      => $page_label,
				'target_url'        => $url,
				'admin_url'         => nexus_get_seo_cockpit_detail_url( $url ),
				'funnel_role'       => nexus_get_revenue_command_center_funnel_role( $page_role ),
				'problem'           => (string) ( $insight['label'] ?? 'Revenue-Auffälligkeit' ),
				'why_now'           => (string) ( $insight['reason'] ?? 'Die Seite hat ein auswertbares Signal.' ),
				'next_action'       => (string) $action['next_action'],
				'expected_leverage' => (string) $action['expected_leverage'],
				'effort'            => (string) $action['effort'],
				'risk'              => (string) $action['risk'],
				'repo_fixable'      => (string) $action['repo_fixable'],
				'manual'            => (string) $action['manual'],
				'confidence'        => nexus_get_revenue_command_center_confidence_label( (int) $score_data['confidence'] ),
				'data_basis'        => sprintf( 'GSC/SEO-Insight: %s%s', (string) ( $insight['type'] ?? 'unknown' ), $lead_note ),
				'section'           => $section,
			],
			$stored_statuses
		);

		if ( 'conversion_leaks' === $section ) {
			$gap_seen_urls[ $url ] = true;
		}
	}

	foreach ( (array) ( $snapshot['current_page_rows'] ?? [] ) as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$url = nexus_normalize_seo_cockpit_url( function_exists( 'nexus_get_seo_cockpit_row_key' ) ? nexus_get_seo_cockpit_row_key( $row, 0 ) : (string) ( $row['keys'][0] ?? '' ) );
		if ( '' === $url || isset( $gap_seen_urls[ $url ] ) ) {
			continue;
		}

		$impressions = (float) ( $row['impressions'] ?? 0 );
		$clicks      = (float) ( $row['clicks'] ?? 0 );
		$ctr         = (float) ( $row['ctr'] ?? 0 );
		$position    = (float) ( $row['position'] ?? 0 );
		$context     = isset( $snapshot['page_contexts'][ $url ] ) && is_array( $snapshot['page_contexts'][ $url ] ) ? $snapshot['page_contexts'][ $url ] : [];
		$page_role   = function_exists( 'nexus_get_seo_cockpit_page_role' ) ? nexus_get_seo_cockpit_page_role( $context, $url ) : 'unknown';
		$lead_page   = function_exists( 'nexus_get_seo_cockpit_lead_page_for_url' ) ? nexus_get_seo_cockpit_lead_page_for_url( $snapshot, $url ) : [];
		$has_leads   = (int) ( $lead_page['lifetime']['requests'] ?? 0 ) > 0;
		$is_high_value_gap = in_array( $page_role, [ 'audit', 'service', 'seo_subpage', 'home', 'results' ], true ) && $impressions >= 80;
		$is_assist_gap     = in_array( $page_role, [ 'blog', 'hub' ], true ) && $impressions >= 150;

		if ( $has_leads || ( ! $is_high_value_gap && ! $is_assist_gap ) ) {
			continue;
		}

		$role_scores = function_exists( 'nexus_get_seo_cockpit_page_role_scores' ) ? nexus_get_seo_cockpit_page_role_scores( $page_role ) : [ 'business' => 5, 'funnel' => 3 ];
		$confidence  = function_exists( 'nexus_get_seo_cockpit_confidence_score' ) ? nexus_get_seo_cockpit_confidence_score( $impressions, $clicks, $context ) : 4;
		$score       = min(
			100,
			(int) ( $role_scores['business'] ?? 0 )
			+ (int) ( $role_scores['funnel'] ?? 0 )
			+ min( 16, (int) round( nexus_get_seo_cockpit_demand_score( $impressions ) * 0.8 ) )
			+ ( $is_high_value_gap ? 18 : 10 )
			+ $confidence
			- ( in_array( $page_role, [ 'audit', 'service', 'seo_subpage' ], true ) ? 3 : 0 )
		);
		$page_label = ! empty( $context['post_title'] ) ? (string) $context['post_title'] : nexus_get_seo_cockpit_short_url( $url );

		$rows[] = nexus_build_revenue_command_center_row(
			[
				'id'                => nexus_get_revenue_command_center_item_id( 'conversion_gap', $url ),
				'priority_score'    => $score,
				'priority_bucket'   => nexus_get_revenue_command_center_priority_bucket( $score ),
				'type'              => 'CTA',
				'target_label'      => $page_label,
				'target_url'        => $url,
				'admin_url'         => nexus_get_seo_cockpit_detail_url( $url ),
				'funnel_role'       => nexus_get_revenue_command_center_funnel_role( $page_role ),
				'problem'           => 'Sichtbarkeit vorhanden, aber kein Lead-Signal',
				'why_now'           => sprintf( 'Die URL sammelt %s Impressionen, aber das Audit-CRM ordnet ihr bisher keine Marktcheck-Anfrage zu.', number_format_i18n( $impressions, 0 ) ),
				'next_action'       => $is_high_value_gap ? 'CTA, Marktcheck-Brücke und E3-/Proof-Nähe manuell prüfen.' : 'Assist-Content auf passende Marktcheck- oder Proof-Brücke prüfen.',
				'expected_leverage' => $is_high_value_gap ? 'hoher Conversion-Hebel' : 'Assist-Hebel',
				'effort'            => 'S',
				'risk'              => $is_high_value_gap ? 'mittel' : 'niedrig',
				'repo_fixable'      => 'ja',
				'manual'            => 'ja',
				'confidence'        => nexus_get_revenue_command_center_confidence_label( $confidence ),
				'data_basis'        => sprintf( 'GSC: %s Impr. / %s Klicks / %.1f%% CTR / Pos. %s', number_format_i18n( $impressions, 0 ), number_format_i18n( $clicks, 0 ), $ctr * 100, number_format_i18n( $position, 1 ) ),
				'section'           => 'conversion_leaks',
			],
			$stored_statuses
		);

		$gap_seen_urls[ $url ] = true;
	}

	$lead_pages = (array) ( $snapshot['leads']['top_pages'] ?? [] );
	foreach ( $lead_pages as $lead_page ) {
		if ( ! is_array( $lead_page ) || empty( $lead_page['url'] ) ) {
			continue;
		}

		$url = nexus_normalize_seo_cockpit_url( (string) $lead_page['url'] );
		if ( '' === $url ) {
			continue;
		}

		$score = 55 + min( 25, function_exists( 'nexus_get_seo_cockpit_lead_signal_score' ) ? nexus_get_seo_cockpit_lead_signal_score( $lead_page ) : 0 );
		$lifetime_requests = (int) ( $lead_page['lifetime']['requests'] ?? 0 );
		$lifetime_inferred = (int) ( $lead_page['lifetime']['inferred_requests'] ?? 0 );
		$data_basis        = sprintf( 'CRM-Attribution: %d aktuell / %d gesamt', (int) ( $lead_page['current']['requests'] ?? 0 ), $lifetime_requests );
		if ( $lifetime_inferred > 0 ) {
			$data_basis .= sprintf( ' / %d abgeleitet', $lifetime_inferred );
		}

		$rows[] = nexus_build_revenue_command_center_row(
			[
				'id'                => nexus_get_revenue_command_center_item_id( 'lead_page', $url ),
				'priority_score'    => min( 100, $score ),
				'priority_bucket'   => nexus_get_revenue_command_center_priority_bucket( $score ),
				'type'              => 'Page',
				'target_label'      => (string) ( $lead_page['label'] ?? nexus_get_seo_cockpit_short_url( $url ) ),
				'target_url'        => $url,
				'admin_url'         => nexus_get_seo_cockpit_detail_url( $url ),
				'funnel_role'       => (string) ( $lead_page['page_role_label'] ?? 'Lead-Attribution' ),
				'problem'           => 'Seite erzeugt oder assistiert Marktcheck-Anfragen',
				'why_now'           => 'Lead-Signal schlägt Vanity-Traffic; diese URL ist bereits im Umsatzpfad sichtbar.',
				'next_action'       => 'CTA-, Proof- und Follow-up-Kette dieser Seite prüfen, bevor neue Traffic-Arbeit startet.',
				'expected_leverage' => 'gesicherter Anfrage-Hebel',
				'effort'            => 'S',
				'risk'              => 'niedrig',
				'repo_fixable'      => 'ja',
				'manual'            => 'ja',
				'confidence'        => $lifetime_requests > 0 && $lifetime_inferred >= $lifetime_requests ? 'mittel' : 'hoch',
				'data_basis'        => $data_basis,
				'section'           => 'page_queue',
			],
			$stored_statuses
		);
	}

	return $rows;
}

/**
 * Build manual check rows for weak or missing data.
 *
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @param array<string, string> $stored_statuses Stored row statuses.
 * @param bool                 $snapshot_error Whether the SEO snapshot failed.
 * @return array<int, array<string, mixed>>
 */
function nexus_get_revenue_command_center_manual_rows( $snapshot, $stored_statuses, $snapshot_error = false ) {
	$rows         = [];
	$setup        = nexus_get_seo_cockpit_setup_state();
	$lead_data    = is_array( $snapshot['leads'] ?? null ) ? $snapshot['leads'] : [];
	$lead_current = is_array( $lead_data['overview']['current'] ?? null ) ? $lead_data['overview']['current'] : [];

	if ( $snapshot_error || empty( $setup['is_ready'] ) ) {
		$rows[] = nexus_build_revenue_command_center_row(
			[
				'id'                => nexus_get_revenue_command_center_item_id( 'manual', 'search_console_missing' ),
				'priority_score'    => 64,
				'priority_bucket'   => 'high',
				'type'              => 'Manual',
				'target_label'      => 'Search Console',
				'funnel_role'       => 'Tracking',
				'problem'           => 'Search Console nicht verbunden',
				'why_now'           => 'Ohne GSC fehlen Sichtbarkeits-, Query- und Conversion-Gap-Signale.',
				'next_action'       => 'Search-Console-Property und OAuth-Verbindung im SEO-Cockpit prüfen.',
				'expected_leverage' => 'bessere Priorisierung',
				'effort'            => 'S',
				'risk'              => 'niedrig',
				'repo_fixable'      => 'nein',
				'manual'            => 'ja',
				'confidence'        => 'hoch',
				'data_basis'        => 'Setup-State',
				'section'           => 'manual_checks',
			],
			$stored_statuses
		);
	}

	if ( empty( $lead_data['available'] ) || (int) ( $lead_current['requests'] ?? 0 ) <= 0 ) {
		$rows[] = nexus_build_revenue_command_center_row(
			[
				'id'                => nexus_get_revenue_command_center_item_id( 'manual', 'no_lead_data' ),
				'priority_score'    => 58,
				'priority_bucket'   => 'medium',
				'type'              => 'Manual',
				'target_label'      => 'Audit-CRM',
				'funnel_role'       => 'Marktcheck',
				'problem'           => 'Noch keine Lead-Daten vorhanden',
				'why_now'           => 'Ohne Lead-Signal muss die Tagesqueue aus Funnel-Rolle und manuellen Checks ableiten.',
				'next_action'       => 'Formulareingang, CRM-Post-Type und Attribution nach dem nächsten Marktcheck prüfen.',
				'expected_leverage' => 'Tracking-Sicherheit',
				'effort'            => 'S',
				'risk'              => 'niedrig',
				'repo_fixable'      => 'nein',
				'manual'            => 'ja',
				'confidence'        => 'mittel',
				'data_basis'        => 'Audit-CRM Empty State',
				'section'           => 'manual_checks',
			],
			$stored_statuses
		);
	} elseif ( (int) ( $lead_current['unmapped_requests'] ?? 0 ) > 0 ) {
		$rows[] = nexus_build_revenue_command_center_row(
			[
				'id'                => nexus_get_revenue_command_center_item_id( 'tracking', 'unmapped_leads' ),
				'priority_score'    => 70,
				'priority_bucket'   => 'high',
				'type'              => 'Tracking',
				'target_label'      => 'Unmapped Leads',
				'funnel_role'       => 'Attribution',
				'problem'           => 'Marktcheck-Anfragen ohne Seitenzuordnung',
				'why_now'           => 'Unmapped Leads verhindern, dass echte Anfrage-Wirkung auf Seitenebene sichtbar wird.',
				'next_action'       => 'Landing-, Entry- und Previous-URL der neuesten Leads im CRM kontrollieren.',
				'expected_leverage' => 'bessere Revenue-Zuordnung',
				'effort'            => 'S',
				'risk'              => 'niedrig',
				'repo_fixable'      => 'nein',
				'manual'            => 'ja',
				'confidence'        => 'hoch',
				'data_basis'        => sprintf( 'CRM: %d unmapped im Zeitraum', (int) $lead_current['unmapped_requests'] ),
				'section'           => 'manual_checks',
			],
			$stored_statuses
		);
	}

	return $rows;
}

/**
 * Build the full Revenue Command Center payload.
 *
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @param int                  $range_days Active range in days.
 * @param bool                 $snapshot_error Whether the SEO snapshot failed.
 * @return array<string, mixed>
 */
function nexus_get_revenue_command_center_data( $snapshot = [], $range_days = 28, $snapshot_error = false ) {
	$snapshot        = is_array( $snapshot ) ? $snapshot : [];
	$stored_statuses = nexus_get_revenue_command_center_statuses();
	$rows            = array_merge(
		nexus_get_revenue_command_center_lead_rows( $stored_statuses, 16 ),
		nexus_get_revenue_command_center_page_rows( $snapshot, $stored_statuses ),
		nexus_get_revenue_command_center_manual_rows( $snapshot, $stored_statuses, $snapshot_error )
	);
	$deduped         = [];

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) || empty( $row['id'] ) ) {
			continue;
		}

		if ( isset( $deduped[ $row['id'] ] ) && (int) ( $deduped[ $row['id'] ]['priority_score'] ?? 0 ) >= (int) ( $row['priority_score'] ?? 0 ) ) {
			continue;
		}

		$deduped[ $row['id'] ] = $row;
	}

	$rows = array_values( $deduped );
	usort(
		$rows,
		static function ( $left, $right ) {
			$status_weight = [
				'today'       => 12,
				'in_progress' => 8,
				'new'         => 0,
				'done'        => -40,
				'ignored'     => -45,
			];
			$left_score = (int) ( $left['priority_score'] ?? 0 ) + (int) ( $status_weight[ (string) ( $left['status'] ?? 'new' ) ] ?? 0 );
			$right_score = (int) ( $right['priority_score'] ?? 0 ) + (int) ( $status_weight[ (string) ( $right['status'] ?? 'new' ) ] ?? 0 );

			return $right_score <=> $left_score;
		}
	);

	$active_rows = array_values(
		array_filter(
			$rows,
			static function ( $row ) {
				return ! in_array( (string) ( $row['status'] ?? 'new' ), [ 'done', 'ignored' ], true );
			}
		)
	);

	$sections = [
		'today_first'      => array_slice( $active_rows, 0, 5 ),
		'lead_followup'    => [],
		'page_queue'       => [],
		'conversion_leaks' => [],
		'manual_checks'    => [],
	];

	foreach ( $rows as $row ) {
		$section = (string) ( $row['section'] ?? 'manual_checks' );
		if ( isset( $sections[ $section ] ) ) {
			$sections[ $section ][] = $row;
		}
	}

	return [
		'range_days' => absint( $range_days ),
		'rows'       => $rows,
		'sections'   => $sections,
		'summary'    => [
			'total'            => count( $rows ),
			'active'           => count( $active_rows ),
			'lead_followup'    => count( $sections['lead_followup'] ),
			'conversion_leaks' => count( $sections['conversion_leaks'] ),
			'manual_checks'    => count( $sections['manual_checks'] ),
		],
	];
}
