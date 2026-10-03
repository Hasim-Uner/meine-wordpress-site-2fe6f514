<?php
/**
 * NEXUS ACF Feldgruppen-Registrierung
 *
 * Registriert alle ACF-Feldgruppen programmatisch (Owned, kein JSON-Import nötig).
 * Felder erscheinen im WordPress-Editor unter den jeweiligen Sektionen.
 *
 * [SEO] inc/acf: SEO-Felder, KPI-Blöcke, Comparison-Daten, Related Pages
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'hu_register_acf_fields' );

/**
 * Register all ACF field groups.
 *
 * @return void
 */
function hu_register_acf_fields() {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// ── 1. SEO Meta Fields ────────────────────────────────────────
	// Pluginlose SEO-Felder. Das Theme nutzt diese ACF-Felder als primaere
	// Quelle fuer Title, Description und Noindex-Steuerung.
	acf_add_local_field_group( [
		'key'      => 'group_nexus_seo',
		'title'    => 'SEO Meta (Growth Architect)',
		'fields'   => [
			[
				'key'           => 'field_seo_title',
				'label'         => 'SEO Title',
				'name'          => 'seo_title',
				'type'          => 'text',
				'instructions'  => 'Optionaler Seitentitel für Title-Tag und Social Preview. Leer = Theme-Fallback nutzen.',
				'maxlength'     => 65,
				'wrapper'       => [ 'width' => '100' ],
			],
			[
				'key'           => 'field_seo_description',
				'label'         => 'SEO Description',
				'name'          => 'seo_description',
				'type'          => 'textarea',
				'instructions'  => 'Optionale Meta Description. Leer = Excerpt/Fallback nutzen.',
				'rows'          => 3,
				'new_lines'     => '',
				'maxlength'     => 160,
				'wrapper'       => [ 'width' => '100' ],
			],
			[
				'key'          => 'field_og_image',
				'label'        => 'Open Graph Bild',
				'name'         => 'og_image',
				'type'         => 'image',
				'instructions' => '1200×630px. Überschreibt das Social-Preview-Bild für diese Seite.',
				'return_format' => 'url',
				'preview_size'  => 'medium',
				'mime_types'    => 'jpg, jpeg, png, webp',
			],
			[
				'key'          => 'field_seo_noindex',
				'label'        => 'Seite auf noindex setzen',
				'name'         => 'seo_noindex',
				'type'         => 'true_false',
				'instructions' => 'Aktivieren = Suchmaschinen indexieren diese Seite NICHT.',
				'default_value' => 0,
				'ui'           => 1,
			],
			[
				'key'           => 'field_enable_faq_schema',
				'label'         => 'FAQ-Schema erzwingen',
				'name'          => 'enable_faq_schema',
				'type'          => 'true_false',
				'instructions'  => 'Aktivieren, wenn der Beitrag F&A als Überschriften (H2–H4, die auf „?“ enden) statt als Akkordeon enthält. Erzeugt FAQPage-Schema auch ohne Standard-Markup. Nach dem Aktivieren einmal speichern.',
				'default_value' => 0,
				'ui'            => 1,
			],
		],
		'location' => [
			// Pages
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'page',
				],
			],
			// Posts
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'post',
				],
			],
		],
		'position'   => 'side',
		'style'      => 'default',
		'menu_order'  => 0,
		'description' => 'Pluginlose SEO-Felder für Title, Description, Social-Preview und noindex.',
	] );

	// ── 2. KPI Block Fields ───────────────────────────────────────
	// Für template-parts/kpi-block.php – wiederverwendbare Metriken.
	acf_add_local_field_group( [
		'key'    => 'group_nexus_kpi',
		'title'  => 'KPI Metriken',
		'fields' => [
			[
				'key'       => 'field_kpi_blocks',
				'label'     => 'KPI Blöcke',
				'name'      => 'kpi_blocks',
				'type'      => 'repeater',
				'instructions' => 'Metriken als visuelle Anker. Jeder Block zeigt eine Kennzahl.',
				'min'       => 0,
				'max'       => 8,
				'layout'    => 'table',
				'sub_fields' => [
					[
						'key'         => 'field_kpi_value',
						'label'       => 'Wert',
						'name'        => 'kpi_value',
						'type'        => 'text',
						'placeholder' => 'z.B. über 85 %, 0.8s, 98/100',
						'wrapper'     => [ 'width' => '25' ],
					],
					[
						'key'         => 'field_kpi_label',
						'label'       => 'Label',
						'name'        => 'kpi_label',
						'type'        => 'text',
						'placeholder' => 'z.B. CPL-Reduktion',
						'wrapper'     => [ 'width' => '35' ],
					],
					[
						'key'         => 'field_kpi_context',
						'label'       => 'Kontext',
						'name'        => 'kpi_context',
						'type'        => 'text',
						'placeholder' => 'z.B. via Server-Side Tracking',
						'wrapper'     => [ 'width' => '40' ],
					],
				],
			],
		],
		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'page',
				],
			],
		],
		'position'  => 'normal',
		'style'     => 'default',
		'menu_order' => 10,
	] );

	// ── 3. Comparison Table Fields ────────────────────────────────
	// Für template-parts/comparison-table.php – Vorher/Nachher Daten.
	acf_add_local_field_group( [
		'key'    => 'group_nexus_comparison',
		'title'  => 'Vorher/Nachher Vergleich',
		'fields' => [
			[
				'key'         => 'field_comparison_title',
				'label'       => 'Tabellen-Überschrift',
				'name'        => 'comparison_title',
				'type'        => 'text',
				'placeholder' => 'z.B. Ergebnisse nach 90 Tagen',
			],
			[
				'key'       => 'field_comparison_items',
				'label'     => 'Vergleichswerte',
				'name'      => 'comparison_items',
				'type'      => 'repeater',
				'min'       => 0,
				'max'       => 12,
				'layout'    => 'table',
				'sub_fields' => [
					[
						'key'         => 'field_comp_metric',
						'label'       => 'Metrik',
						'name'        => 'metric',
						'type'        => 'text',
						'placeholder' => 'z.B. LCP',
						'wrapper'     => [ 'width' => '34' ],
					],
					[
						'key'         => 'field_comp_before',
						'label'       => 'Vorher',
						'name'        => 'before',
						'type'        => 'text',
						'placeholder' => 'z.B. 4.2s',
						'wrapper'     => [ 'width' => '33' ],
					],
					[
						'key'         => 'field_comp_after',
						'label'       => 'Nachher',
						'name'        => 'after',
						'type'        => 'text',
						'placeholder' => 'z.B. 0.8s',
						'wrapper'     => [ 'width' => '33' ],
					],
				],
			],
		],
		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'page',
				],
			],
		],
		'position'  => 'normal',
		'style'     => 'default',
		'menu_order' => 20,
	] );

	// ── 4. Related Pages (manuell) ────────────────────────────────
	// Für template-parts/related-content.php – Flywheel-Verlinkung.
	acf_add_local_field_group( [
		'key'    => 'group_nexus_related',
		'title'  => 'Verwandte Seiten (Flywheel)',
		'fields' => [
			[
				'key'          => 'field_related_pages',
				'label'        => 'Verwandte Seiten',
				'name'         => 'related_pages',
				'type'         => 'relationship',
				'instructions' => 'Wähle 2-4 verwandte Seiten für die interne Verlinkung. Pillar ↔ Cluster.',
				'post_type'    => [ 'page', 'post' ],
				'filters'      => [ 'search', 'post_type' ],
				'min'          => 0,
				'max'          => 4,
				'return_format' => 'object',
			],
		],
		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'page',
				],
			],
		],
		'position'  => 'side',
		'style'     => 'default',
		'menu_order' => 30,
	] );


}

// <title> wird vom Theme verwaltet (seo-meta.php) — kein ACF-Override noetig.
