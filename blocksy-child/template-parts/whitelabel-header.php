<?php
/**
 * Header of the White-Label landing page.
 *
 * Die Seite ist die Landeseite der Akquise-Mails und traegt deshalb eine
 * reduzierte Seitennavigation statt des globalen Hauptmenues: Wortmarke, fuenf
 * Anker der Seite (whitelabel.js markiert sie mit aria-current="location") und
 * rechts die Tuer der Route. Die Tuer kommt wie in site-header.php aus
 * hu_funnel_doors() (Test-Sprint, Betrag aus dem Kanon, data-door), behaelt
 * aber ihre bisherige Tracking-Action cta_whitelabel_header_task_brief, damit
 * die Zeitreihe der Landeseite nicht abreisst. Unter 768 px traegt die
 * Sticky-Leiste am unteren Rand den Weg zum Formular; die Tuer tritt dann zurueck
 * (whitelabel.css).
 *
 * Args: home_url, brand, home_label, form_url, nav (Liste aus [ Anker, Label, Track ]).
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wl_args = array_merge(
	[
		'home_url'   => home_url( '/' ),
		'brand'      => 'HAŞIM ÜNER',
		'home_label' => '',
		'form_url'   => '',
		'nav'        => [],
	],
	isset( $args ) && is_array( $args ) ? $args : []
);

$wl_doors = function_exists( 'hu_funnel_doors' ) ? hu_funnel_doors() : [];
$wl_door  = $wl_doors['aufgabe'] ?? null;
?>
<header class="leiste wl-site-header st-messkopf tafel" role="banner" data-track-section="whitelabel_header">
	<div class="blatt in">
		<a
			class="sig site-logo"
			href="<?php echo esc_url( (string) $wl_args['home_url'] ); ?>"
			rel="home"
			aria-label="<?php echo esc_attr( (string) $wl_args['home_label'] ); ?>"
			data-track-action="nav_whitelabel_home"
			data-track-category="navigation"
			data-track-section="whitelabel_header"
		><?php echo esc_html( (string) $wl_args['brand'] ); ?><i aria-hidden="true">.</i></a>
		<div class="rechts">
			<nav aria-label="Navigation auf dieser Seite" data-wl-anker>
				<?php foreach ( (array) $wl_args['nav'] as $wl_nav_item ) : ?>
					<a href="<?php echo esc_attr( $wl_nav_item[0] ); ?>" data-track-action="<?php echo esc_attr( $wl_nav_item[2] ); ?>" data-track-category="navigation" data-track-section="whitelabel_header"><?php echo esc_html( $wl_nav_item[1] ); ?></a>
				<?php endforeach; ?>
			</nav>
			<?php
			if ( is_array( $wl_door ) ) {
				echo hu_funnel_door_link( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside hu_funnel_door_link().
					$wl_door,
					[
						'href'       => (string) $wl_args['form_url'],
						'track'      => 'cta_whitelabel_header_task_brief',
						'section'    => 'whitelabel_header',
						'attributes' => [ 'data-wl-form-link' => '' ],
					]
				);
			}
			?>
		</div>
	</div>
</header>
