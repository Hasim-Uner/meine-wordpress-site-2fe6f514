<?php
/**
 * One-time hygiene for the B2B landing-page flagship article.
 *
 * The article body remains editor-owned. This migration only removes the
 * unsupported hard conversion-rate promise from the title/SEO metadata and
 * aligns the post with the canonical Conversion dossier.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clean the legacy B2B landing-page title and canonical dossier relation.
 *
 * @return void
 */
function hu_maybe_refresh_landingpage_article_metadata() : void {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = '2026-09-05-1';
	$option_key = 'hu_article_content_hygiene_landingpage_version';

	if ( (string) get_option( $option_key, '' ) === $version ) {
		return;
	}

	if ( ! function_exists( 'hu_article_content_hygiene_find_post_id' ) ) {
		return;
	}

	$post_id = hu_article_content_hygiene_find_post_id( 'b2b-landingpage-optimieren' );
	if ( $post_id <= 0 ) {
		return;
	}

	$current_title = trim( (string) get_post_field( 'post_title', $post_id ) );
	$new_title     = 'B2B-Landingpage optimieren: Struktur, Proof und CTA systematisch verbessern';
	$legacy_title  = (bool) preg_match(
		'/^B2B-Landingpage optimieren: Das System hinter 8(?:–|-|—)12\s*% Conversion \[2026\]$/u',
		$current_title
	);

	// If an editor has already chosen another title, do not overwrite it.
	if ( ! $legacy_title && $new_title !== $current_title ) {
		return;
	}

	if ( $legacy_title ) {
		$result = wp_update_post(
			[
				'ID'         => $post_id,
				'post_title' => $new_title,
			],
			true
		);

		if ( is_wp_error( $result ) || ! $result ) {
			return;
		}

		update_post_meta( $post_id, 'seo_title', 'B2B-Landingpage optimieren: Struktur, Proof & CTA' );
		update_post_meta(
			$post_id,
			'seo_description',
			'B2B-Landingpages systematisch optimieren: Botschaft, Proof, CTA, Formular und Messung so ordnen, dass Reibung sichtbar und testbar wird.'
		);
	}

	// The Article System routes this post through Conversion &
	// Anfragearchitektur. Keep the underlying WordPress taxonomy consistent so
	// it does not also surface in the Leadökonomie dossier because of historical
	// Solar/Performance category assignments.
	if ( function_exists( 'hu_get_positioned_blog_dossier_taxonomy' ) && function_exists( 'hu_ensure_positioned_blog_dossier_term' ) ) {
		$canonical = hu_get_positioned_blog_dossier_taxonomy();
		if ( ! empty( $canonical['cro'] ) ) {
			$cro_id = hu_ensure_positioned_blog_dossier_term( 'cro', $canonical['cro'], false );
			if ( $cro_id > 0 ) {
				$current_ids   = wp_get_post_terms( $post_id, 'category', [ 'fields' => 'ids' ] );
				$canonical_ids = [];

				foreach ( $canonical as $slug => $data ) {
					$term = get_term_by( 'slug', (string) $slug, 'category' );
					if ( $term instanceof WP_Term ) {
						$canonical_ids[] = (int) $term->term_id;
					}
				}

				if ( ! is_wp_error( $current_ids ) ) {
					// Preserve unknown/editor-owned categories, but allow only CRO among
					// the four canonical dossier terms for this article.
					$kept_ids = array_values(
						array_filter(
							array_map( 'absint', (array) $current_ids ),
							static function( $term_id ) use ( $canonical_ids ) {
								return ! in_array( (int) $term_id, $canonical_ids, true );
							}
						)
					);
					$kept_ids[] = (int) $cro_id;
					$set = wp_set_post_terms( $post_id, array_values( array_unique( $kept_ids ) ), 'category', false );
					if ( is_wp_error( $set ) ) {
						return;
					}
				}
			}
		}
	}

	update_post_meta( $post_id, '_hu_article_content_hygiene_landingpage_version', $version );
	update_option( $option_key, $version, false );
}
add_action( 'init', 'hu_maybe_refresh_landingpage_article_metadata', 44 );
