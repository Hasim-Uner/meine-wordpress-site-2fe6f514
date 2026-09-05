<?php
/**
 * Blog area header fallback.
 *
 * Rendered on blog index, archive and single post views when the global
 * Blocksy header is intentionally disabled for the blog section.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Article System V2: expand the reader shell one dossier at a time. Alongside
// the provider pilots, WordPress & Performance, Tracking & Messbarkeit and
// Conversion & Anfragearchitektur now each have a representative article.
$article_system_reader_slugs = [
	'aroundhome-solar-einordnung',
	'checkfox-solar-waermepumpe-einordnung',
	'wattfox-solar-leads-einordnung',
	'wordpress-ttfb-google-ads-ladezeit',
	'server-side-tracking-gtm',
	'b2b-landingpage-optimieren',
];

if ( is_singular( 'post' ) ) {
	$article_system_reader_slug = (string) get_post_field( 'post_name', get_queried_object_id() );

	if ( in_array( $article_system_reader_slug, $article_system_reader_slugs, true ) ) {
		get_template_part(
			'template-parts/article-reader-header',
			null,
			[
				'slug' => $article_system_reader_slug,
			]
		);
		return;
	}
}

$primary_urls       = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$home_url           = $primary_urls['home'] ?? home_url( '/' );
$blog_url           = $primary_urls['blog'] ?? home_url( '/blog/' );
$energy_url         = $primary_urls['energy'] ?? ( function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' ) );
$freelancer_url     = home_url( '/wordpress-freelancer-hannover/' );
$whitelabel_url     = function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' );
$cases_url          = $primary_urls['results'] ?? ( function_exists( 'nexus_get_results_url' ) ? nexus_get_results_url() : home_url( '/ergebnisse/' ) );
$about_url          = $primary_urls['about'] ?? home_url( '/hasim-uener/' );
$project_url        = function_exists( 'hu_get_navigation_project_request_url' ) ? hu_get_navigation_project_request_url() : add_query_arg( [ 'type' => 'project', 'focus' => 'followup_scope' ], home_url( '/kontakt/' ) );
$brand_text         = function_exists( 'hu_get_site_wordmark_text' ) ? hu_get_site_wordmark_text() : 'HAŞIM ÜNER';
$panel_id           = 'nx-blog-header-panel';
$about_page_id      = function_exists( 'nexus_get_page_id' ) ? nexus_get_page_id( [ 'hasim-uener', 'uber-mich' ] ) : 0;
$is_blog_area       = is_home() || is_archive() || is_singular( 'post' );
$is_energy_context  = function_exists( 'nexus_is_energy_systems_context' ) && nexus_is_energy_systems_context();
$is_freelancer_context = is_page( 'wordpress-freelancer-hannover' ) || is_page_template( 'page-wordpress-freelancer-hannover.php' );
$is_whitelabel_context = function_exists( 'nexus_is_agency_nav_context' ) && nexus_is_agency_nav_context();
$is_results_context = function_exists( 'nexus_is_results_context' ) && nexus_is_results_context();
$home_label         = sprintf(
	/* translators: %s: site or brand name. */
	__( 'Startseite - %s', 'blocksy-child' ),
	$brand_text
);

// Die Checkfox-Entscheidungsseite ersetzt die Kontextzeile durch ihre eigene
// sticky Sprungnavigation im Artikel. Zwei Orientierungsleisten uebereinander
// waeren dort nur doppelte Hoehe ohne zusaetzliche Information.
$suppress_context_links = is_single( 'checkfox-solar-waermepumpe-einordnung' );

$context_title = __( 'Blog', 'blocksy-child' );
$context_text  = __( 'Analysen zu Anfragesystemen, Portal-Kosten, Tracking und Conversion.', 'blocksy-child' );
$context_links = [
	[
		'label'  => __( 'Alle Analysen', 'blocksy-child' ),
		'url'    => $blog_url,
		'active' => is_home(),
	],
];

if ( is_category() ) {
	$queried_term  = get_queried_object();
	$context_title = $queried_term instanceof WP_Term && function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $queried_term ) : single_cat_title( '', false );
	$context_text  = __( 'Beiträge zu einem Thema, mit Rückweg zur Übersicht und direktem nächsten Schritt.', 'blocksy-child' );
	$context_links[] = [
		'label'  => $context_title,
		'url'    => get_category_link( get_queried_object_id() ),
		'active' => true,
	];
} elseif ( is_tag() ) {
	$context_title = single_tag_title( '', false );
	$context_text  = __( 'Tag-Archiv mit Rückweg zur Übersicht und klarer Hauptnavigation.', 'blocksy-child' );
	$context_links[] = [
		'label'  => single_tag_title( '', false ),
		'url'    => get_tag_link( get_queried_object_id() ),
		'active' => true,
	];
} elseif ( is_archive() && ! is_home() ) {
	$context_title = get_the_archive_title();
	$context_text  = __( 'Archivansicht mit Überblick, Lesefluss und nächstem Schritt.', 'blocksy-child' );
	$context_links[] = [
		'label'  => get_the_archive_title(),
		'url'    => get_post_type_archive_link( 'post' ) ?: $blog_url,
		'active' => true,
	];
} elseif ( is_singular( 'post' ) ) {
	$context_title = __( 'Artikel', 'blocksy-child' );
	$context_text  = __( 'Zurück zur Übersicht, passende Kategorie öffnen oder ein Projekt anfragen.', 'blocksy-child' );

	$post_categories = get_the_category();

	if ( ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ) {
		$primary_category = $post_categories[0];
		$context_links[]  = [
			'label'  => function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $primary_category ) : $primary_category->name,
			'url'    => get_category_link( $primary_category->term_id ),
			'active' => false,
		];
	}
}

// Der Blog ist bereits über Kontextzeile und Unterleiste als Bereich markiert.
// Die knappe Hauptnavigation bleibt deshalb auf die fünf strategischen Wege
// beschränkt, statt einen sechsten "Blog"-Punkt in die Desktop-Leiste zu drücken.
$primary_items = [
	[
		'label'   => __( 'Solar & Wärmepumpen', 'blocksy-child' ),
		'url'     => $energy_url,
		'active'  => $is_energy_context,
		'current' => $is_energy_context,
	],
	[
		'label'   => __( 'WordPress Freelancer', 'blocksy-child' ),
		'url'     => $freelancer_url,
		'active'  => $is_freelancer_context,
		'current' => $is_freelancer_context,
	],
	[
		'label'   => __( 'Für Agenturen', 'blocksy-child' ),
		'url'     => $whitelabel_url,
		'active'  => $is_whitelabel_context,
		'current' => $is_whitelabel_context,
	],
	[
		'label'   => __( 'Ergebnisse', 'blocksy-child' ),
		'url'     => $cases_url,
		'active'  => $is_results_context,
		'current' => $is_results_context,
	],
	[
		'label'   => __( 'Über Haşim', 'blocksy-child' ),
		'url'     => $about_url,
		'active'  => $about_page_id ? is_page( $about_page_id ) : false,
		'current' => $about_page_id ? is_page( $about_page_id ) : false,
	],
];
?>

<header
	class="nexus-blog-header"
	data-site-header
	<?php
	// Auf der Checkfox-Entscheidungsseite uebernimmt die seiteninterne Leiste die
	// Orientierung. Der Header blendet sich dort nicht mehr beim Scrollen ein;
	// Tastaturfokus und Zeiger an der Oberkante holen ihn weiterhin hervor, und
	// am Seitenende pinnt ihn die Seite selbst wieder ein.
	echo $suppress_context_links ? ' data-site-header-scroll-reveal="off"' : ''; // raw-ok -- static attribute
	?>
	role="banner"
>
	<div class="nx-container nexus-blog-header__frame">
		<div class="nexus-blog-header__shell">
			<div class="nexus-blog-header__brand-block">
				<a
					class="nexus-blog-header__brand site-logo"
					href="<?php echo esc_url( $home_url ); ?>"
					rel="home"
					aria-label="<?php echo esc_attr( $home_label ); ?>"
				>
					<?php echo esc_html( $brand_text ); ?>
				</a>

				<div class="nexus-blog-header__intro" aria-label="<?php esc_attr_e( 'Blog-Kontext', 'blocksy-child' ); ?>">
					<span class="nexus-blog-header__eyebrow"><?php esc_html_e( 'Analysen', 'blocksy-child' ); ?></span>
					<p class="nexus-blog-header__title"><?php echo esc_html( $context_title ); ?></p>
				</div>
			</div>

			<nav class="nexus-blog-header__nav" aria-label="<?php esc_attr_e( 'Primäre Blog-Navigation', 'blocksy-child' ); ?>">
				<ul class="nexus-blog-header__menu">
					<?php foreach ( $primary_items as $item ) : ?>
						<?php
						$is_active  = ! empty( $item['active'] );
						$is_current = ! empty( $item['current'] );
						?>
						<li class="nexus-blog-header__menu-item">
							<a
								class="nexus-blog-header__menu-link<?php echo esc_attr( $is_active ? ' is-active' : '' ); ?>"
								href="<?php echo esc_url( $item['url'] ); ?>"
								<?php echo $is_current ? 'aria-current="page"' : ''; // raw-ok -- static attribute ?>
							>
								<?php echo esc_html( $item['label'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<div class="nexus-blog-header__actions">
				<a
					class="nexus-blog-header__cta nexus-blog-header__desktop-cta"
					href="<?php echo esc_url( $project_url ); ?>"
					data-track-action="cta_blog_header_project"
					data-track-category="lead_gen"
				>
					<?php esc_html_e( 'Projekt anfragen', 'blocksy-child' ); ?>
				</a>

				<button
					type="button"
					class="nexus-blog-header__toggle"
					data-site-header-toggle
					aria-expanded="false"
					aria-controls="<?php echo esc_attr( $panel_id ); ?>"
					aria-label="<?php esc_attr_e( 'Navigation öffnen', 'blocksy-child' ); ?>"
				>
					<span class="nexus-blog-header__toggle-lines" aria-hidden="true">
						<span class="nexus-blog-header__toggle-line"></span>
						<span class="nexus-blog-header__toggle-line"></span>
						<span class="nexus-blog-header__toggle-line"></span>
					</span>
					<span class="nexus-blog-header__toggle-label"><?php esc_html_e( 'Menü', 'blocksy-child' ); ?></span>
				</button>
			</div>
		</div>

		<div id="<?php echo esc_attr( $panel_id ); ?>" class="nexus-blog-header__mobile-panel" data-site-header-panel hidden>
			<p class="nexus-blog-header__mobile-context"><?php echo esc_html( $context_text ); ?></p>

			<nav class="nexus-blog-header__mobile-nav" aria-label="<?php esc_attr_e( 'Mobiles Blog-Menü', 'blocksy-child' ); ?>">
				<?php foreach ( $primary_items as $item ) : ?>
					<?php
					$is_active  = ! empty( $item['active'] );
					$is_current = ! empty( $item['current'] );
					?>
					<a
						class="nexus-blog-header__mobile-link<?php echo esc_attr( $is_active ? ' is-active' : '' ); ?>"
						href="<?php echo esc_url( $item['url'] ); ?>"
						<?php echo $is_current ? 'aria-current="page"' : ''; // raw-ok -- static attribute ?>
					>
						<?php echo esc_html( $item['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<div class="nexus-blog-header__mobile-actions">
				<a
					class="nexus-blog-header__cta"
					href="<?php echo esc_url( $project_url ); ?>"
					data-track-action="cta_blog_header_mobile_project"
					data-track-category="lead_gen"
				>
					<?php esc_html_e( 'Projekt anfragen', 'blocksy-child' ); ?>
				</a>
			</div>
		</div>
	</div>

	<?php if ( count( $context_links ) > 1 && ! $suppress_context_links ) : ?>
		<nav class="nx-container nexus-blog-header__context-links" aria-label="<?php esc_attr_e( 'Blog-Kontext', 'blocksy-child' ); ?>">
			<?php foreach ( $context_links as $link ) : ?>
				<a
					class="nexus-blog-header__context-link<?php echo ! empty( $link['active'] ) ? ' is-active' : ''; ?>"
					href="<?php echo esc_url( $link['url'] ); ?>"
					<?php echo ! empty( $link['active'] ) ? 'aria-current="page"' : ''; ?>
				>
					<?php echo esc_html( $link['label'] ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>
</header>