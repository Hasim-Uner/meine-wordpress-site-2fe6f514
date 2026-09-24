<?php
/**
 * Unified single-post reader.
 *
 * Every post receives the same article header, cover, context bridge, sticky
 * table of contents, reading column and closing surfaces. Provider decision
 * posts keep their calculators/checklists as content modules only.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_aroundhome_decision_request = is_single( 'aroundhome-solar-einordnung' );
$is_checkfox_decision_request   = is_single( 'checkfox-solar-waermepumpe-einordnung' );
$is_provider_decision_request   = $is_aroundhome_decision_request || $is_checkfox_decision_request;

get_header();
get_template_part( 'template-parts/blog-header' );
?>

<div class="nexus-reading-progress" aria-hidden="true"></div>

<aside class="nexus-share-rail" aria-label="<?php esc_attr_e( 'Artikel teilen', 'blocksy-child' ); ?>">
	<span class="nexus-share-rail__label"><?php esc_html_e( 'Teilen', 'blocksy-child' ); ?></span>
	<button class="nexus-share-rail__btn" type="button" data-nexus-share="linkedin" aria-label="LinkedIn">in</button>
	<button class="nexus-share-rail__btn" type="button" data-nexus-share="x" aria-label="X">X</button>
	<button class="nexus-share-rail__btn" type="button" data-nexus-share="email" aria-label="<?php esc_attr_e( 'Per E-Mail teilen', 'blocksy-child' ); ?>">@</button>
	<button class="nexus-share-rail__btn" type="button" data-nexus-share="copy" aria-label="<?php esc_attr_e( 'Link kopieren', 'blocksy-child' ); ?>">↗</button>
</aside>

<div class="site-main nexus-single-container nexus-single-container--with-blog-header nexus-single-container--editorial hu-hp">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		$post_id         = get_the_ID();
		$post_slug       = (string) get_post_field( 'post_name', $post_id );
		$has_hero_image  = has_post_thumbnail();
		$article_summary = wp_strip_all_tags( get_the_excerpt() );
		if ( '' === $article_summary ) {
			$article_summary = wp_trim_words( wp_strip_all_tags( get_the_content() ), 30, '...' );
		}
		$reading_time = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time() : 0;

		$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
		$audit_url    = function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
		$energy_url   = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
		$agentur_url  = $primary_urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' );
		$seo_url      = $primary_urls['seo'] ?? trailingslashit( $agentur_url ) . '#zusammenarbeit';
		$cro_url      = $primary_urls['cro'] ?? home_url( '/#angebot-funnel' );
		$tracking_url = home_url( '/server-side-tracking-b2b/' );
		$portal_url   = home_url( '/eigene-leadgenerierung-vs-portale/' );
		$cpl_url      = home_url( '/cost-per-lead-photovoltaik/' );

		$is_agency_outsourcing = function_exists( 'hu_is_agency_outsourcing_article' ) && hu_is_agency_outsourcing_article();
		$is_website_relaunch   = function_exists( 'hu_is_website_relaunch_article' ) && hu_is_website_relaunch_article();
		$whitelabel_route_url  = function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' );

		$post_categories = get_the_category();
		$post_cat_slugs  = ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ? wp_list_pluck( $post_categories, 'slug' ) : [];
		$primary_cat     = ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ? $post_categories[0] : null;
		$category_url    = $primary_cat instanceof WP_Term ? get_category_link( $primary_cat->term_id ) : '';
		$category_url    = is_wp_error( $category_url ) ? '' : $category_url;

		$provider_post_slugs = [
			'aroundhome-solar-einordnung',
			'checkfox-solar-waermepumpe-einordnung',
			'wattfox-solar-leads-einordnung',
			'daa-photovoltaik-leads-einordnung',
			'leadfluss-pv-leads-einordnung',
		];
		$is_provider_post = in_array( $post_slug, $provider_post_slugs, true );

		// Neutraler Standard: Der Marktcheck gehört laut CONVERSION_ROUTING.md
		// nur in den Energie-Pfad. Beiträge ohne passende Kategorie führen
		// deshalb zu den WordPress-Leistungen, nicht in den Solar-Einstieg.
		$article_context = [
			'eyebrow'         => __( 'Einordnung', 'blocksy-child' ),
			'title'           => __( 'Dieser Artikel gehört in den größeren Anfrage-Kontext.', 'blocksy-child' ),
			'text'            => __( 'Lesen Sie den Beitrag als Baustein im Zusammenspiel aus Angebot, Sichtbarkeit, Daten und Conversion.', 'blocksy-child' ),
			'primary_label'   => __( 'WordPress-Leistungen ansehen', 'blocksy-child' ),
			'primary_url'     => home_url( '/#angebote' ),
			'secondary_label' => __( 'Projekt anfragen', 'blocksy-child' ),
			'secondary_url'   => function_exists( 'hu_get_commercial_route' ) ? hu_get_commercial_route( 'project_request' ) : home_url( '/kontakt/?type=project' ),
		];

		if ( $is_provider_post ) {
			$article_context = [
				'eyebrow'         => __( 'Kurz beantwortet', 'blocksy-child' ),
				'title'           => __( 'Seriosität und wirtschaftliche Eignung sind zwei verschiedene Fragen.', 'blocksy-child' ),
				'text'            => __( 'Der Beitrag ordnet beides getrennt ein. Für Ihren Betrieb entscheidet am Ende der reale Preis pro gewonnenem Auftrag.', 'blocksy-child' ),
				'primary_label'   => __( 'TCO-Vergleich Portal vs. eigenes System', 'blocksy-child' ),
				'primary_url'     => $portal_url,
				'secondary_label' => __( 'Regionalen Marktcheck starten', 'blocksy-child' ),
				'secondary_url'   => $audit_url,
			];
		} elseif ( array_intersect( [ 'markteinordnung', 'owned-leads' ], $post_cat_slugs ) ) {
			$article_context = [
				'eyebrow'         => __( 'Portal-Abhängigkeit', 'blocksy-child' ),
				'title'           => __( 'Portal-Leads sind nur ein Kostenblock. Entscheidend ist der CPO.', 'blocksy-child' ),
				'text'            => __( 'Dieser Artikel gehört in die Frage, ob Nachfrage dauerhaft gemietet oder als eigenes System aufgebaut werden sollte.', 'blocksy-child' ),
				'primary_label'   => __( 'TCO-Vergleich ansehen', 'blocksy-child' ),
				'primary_url'     => $portal_url,
				'secondary_label' => __( 'CPL/CPO-Rechnung ansehen', 'blocksy-child' ),
				'secondary_url'   => $cpl_url,
			];
		} elseif ( in_array( 'tracking', $post_cat_slugs, true ) ) {
			$article_context = [
				'eyebrow'         => __( 'Tracking & Daten', 'blocksy-child' ),
				'title'           => __( 'Tracking ist nur wertvoll, wenn daraus bessere Entscheidungen entstehen.', 'blocksy-child' ),
				'text'            => __( 'Dieser Artikel gehört in die Datenebene: Consent, Server-Side Tracking, CRM-Rückführung und saubere Signale.', 'blocksy-child' ),
				'primary_label'   => __( 'Server-Side Tracking ansehen', 'blocksy-child' ),
				'primary_url'     => $tracking_url,
				'secondary_label' => __( 'CRO-System ansehen', 'blocksy-child' ),
				'secondary_url'   => $cro_url,
			];
		} elseif ( array_intersect( [ 'seo', 'cro', 'wordpress-performance', 'strategie', 'wordpress-growth-agentur' ], $post_cat_slugs ) ) {
			$article_context = [
				'eyebrow'         => __( 'WordPress-System', 'blocksy-child' ),
				'title'           => __( 'Einzelhebel wirken erst im verbundenen System.', 'blocksy-child' ),
				'text'            => __( 'Dieser Beitrag ordnet Technik, Sichtbarkeit, Performance oder Conversion in den gesamten Anfragepfad ein.', 'blocksy-child' ),
				'primary_label'   => __( 'Anfrage-Architektur ansehen', 'blocksy-child' ),
				'primary_url'     => $agentur_url,
				'secondary_label' => __( 'Technisches SEO ansehen', 'blocksy-child' ),
				'secondary_url'   => $seo_url,
			];
		} elseif ( array_intersect( [ 'leadgenerierung', 'solar-waermepumpen-anfrage-systeme' ], $post_cat_slugs ) ) {
			$article_context = [
				'eyebrow'         => __( 'Einordnung', 'blocksy-child' ),
				'title'           => __( 'Dieser Artikel gehört in den größeren Anfrage-Kontext.', 'blocksy-child' ),
				'text'            => __( 'Lesen Sie den Beitrag als Baustein im Zusammenspiel aus Angebot, Sichtbarkeit, Daten und Conversion.', 'blocksy-child' ),
				'primary_label'   => __( 'Anfragesystem ansehen', 'blocksy-child' ),
				'primary_url'     => $energy_url,
				'secondary_label' => __( 'Regionalen Marktcheck starten', 'blocksy-child' ),
				'secondary_url'   => $audit_url,
			];
		}

		if ( $is_agency_outsourcing ) {
			$article_context['primary_label'] = __( 'White-Label-Zusammenarbeit ansehen', 'blocksy-child' );
			$article_context['primary_url']   = $whitelabel_route_url;
		}

		// Relaunch-Leser suchen keine Agentur-Seite, sondern haben ein Projekt:
		// direkt in die Projektanfrage mit vorgewähltem Thema.
		if ( $is_website_relaunch && function_exists( 'hu_get_contact_intake_url' ) ) {
			$article_context['primary_label'] = __( 'Relaunch-Projekt anfragen', 'blocksy-child' );
			$article_context['primary_url']   = hu_get_contact_intake_url( 'project', 'relaunch' );
		}

		$article_next_links = [];
		if ( $category_url ) {
			$article_next_links[] = [
				'label' => sprintf( __( 'Mehr aus %s', 'blocksy-child' ), function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $primary_cat ) : $primary_cat->name ),
				'url'   => $category_url,
			];
		}
		$article_next_links[] = [ 'label' => $article_context['primary_label'], 'url' => $article_context['primary_url'] ];
		$article_next_links[] = [ 'label' => $article_context['secondary_label'], 'url' => $article_context['secondary_url'] ];
		?>

		<header class="nexus-article-hero nexus-article-hero--editorial" data-track-section="article_hero" aria-labelledby="nexus-article-title">
			<div class="nexus-hero-content">
				<div class="nexus-meta-top">
					<?php if ( $primary_cat instanceof WP_Term && $category_url ) : ?>
						<a class="nexus-hero-category" href="<?php echo esc_url( $category_url ); ?>"><?php echo esc_html( function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $primary_cat ) : $primary_cat->name ); ?></a>
					<?php endif; ?>
					<span class="nexus-date"><?php echo esc_html( get_the_date( 'd. M Y' ) ); ?></span>
					<?php if ( $reading_time > 0 ) : ?><span class="nexus-reading-time"><?php echo esc_html( sprintf( '%d Min. Lesezeit', $reading_time ) ); ?></span><?php endif; ?>
				</div>
				<h1 id="nexus-article-title" class="nexus-title"><?php echo esc_html( get_the_title() ); ?></h1>
				<?php if ( '' !== $article_summary ) : ?><p class="nexus-subtitle"><?php echo esc_html( $article_summary ); ?></p><?php endif; ?>
				<div class="nexus-hero-footer">
					<div class="nexus-author-row">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 48 ); ?>
						<div class="nexus-author-info"><span class="by"><?php esc_html_e( 'Geschrieben von', 'blocksy-child' ); ?></span><span class="name"><?php echo esc_html( get_the_author() ); ?></span></div>
					</div>
				</div>
			</div>
		</header>

		<figure class="nexus-article-cover nexus-reveal" data-track-section="article_cover">
			<div class="nexus-hero-image<?php echo esc_attr( $has_hero_image ? '' : ' nexus-hero-image--generated' ); ?>">
				<?php if ( $has_hero_image ) : ?>
					<?php the_post_thumbnail( 'full', [ 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async', 'sizes' => '(max-width: 900px) 100vw, 50vw' ] ); ?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/post-title-visual', null, [ 'post_id' => $post_id, 'variant' => 'hero' ] ); ?>
				<?php endif; ?>
			</div>
		</figure>

		<section class="nexus-article-context nexus-reveal<?php echo esc_attr( $is_provider_post ? ' nexus-article-context--provider' : '' ); ?>" data-track-section="article_context_bridge" aria-labelledby="nexus-article-context-title">
			<div class="nexus-article-context__copy">
				<span class="nexus-article-context__eyebrow"><?php echo esc_html( $article_context['eyebrow'] ); ?></span>
				<h2 id="nexus-article-context-title" class="nexus-article-context__title"><?php echo esc_html( $article_context['title'] ); ?></h2>
				<p class="nexus-article-context__text"><?php echo esc_html( $article_context['text'] ); ?></p>
			</div>
			<div class="nexus-article-context__actions">
				<a class="nexus-article-context__link nexus-article-context__link--primary" href="<?php echo esc_url( $article_context['primary_url'] ); ?>"><?php echo esc_html( $article_context['primary_label'] ); ?></a>
				<a class="nexus-article-context__link" href="<?php echo esc_url( $article_context['secondary_url'] ); ?>"><?php echo esc_html( $article_context['secondary_label'] ); ?></a>
			</div>
		</section>

		<div class="nexus-post-layout">
			<aside class="nexus-sidebar nexus-reader-toc" aria-label="<?php esc_attr_e( 'Inhaltsverzeichnis', 'blocksy-child' ); ?>">
				<div class="sticky-toc nexus-reader-toc__shell">
					<h2><?php esc_html_e( 'Inhalt', 'blocksy-child' ); ?></h2>
					<ul id="toc-list"></ul>
				</div>
			</aside>

			<?php if ( $is_provider_decision_request ) : ?>
				<div class="nexus-article-content nexus-article-content--decision" data-track-section="article_content">
					<?php if ( $is_checkfox_decision_request ) : ?>
						<?php get_template_part( 'template-parts/checkfox-decision-cockpit' ); ?>
					<?php else : ?>
						<?php get_template_part( 'template-parts/aroundhome-decision-cockpit' ); ?>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<article class="nexus-article-content" id="article-content" data-track-section="article_content">
					<?php the_content(); ?>
					<?php if ( function_exists( 'nexus_get_wgos_blog_asset_bridge' ) && function_exists( 'nexus_render_wgos_blog_asset_bridge' ) ) : ?>
						<?php $bridge = nexus_get_wgos_blog_asset_bridge(); ?>
						<?php if ( is_array( $bridge ) ) : ?><?php echo nexus_render_wgos_blog_asset_bridge( $bridge ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php endif; ?>
					<?php endif; ?>
				</article>
			<?php endif; ?>
		</div>

		<section class="nexus-article-next" data-track-section="article_next_steps" aria-labelledby="nexus-article-next-heading">
			<span class="nexus-article-next__eyebrow"><?php esc_html_e( 'Weiterarbeiten', 'blocksy-child' ); ?></span>
			<h2 id="nexus-article-next-heading" class="nexus-article-next__title"><?php esc_html_e( 'Nächster sinnvoller Schritt.', 'blocksy-child' ); ?></h2>
			<div class="nexus-article-next__links">
				<?php foreach ( $article_next_links as $next_link ) : ?>
					<?php if ( empty( $next_link['url'] ) || empty( $next_link['label'] ) ) : continue; endif; ?>
					<a class="nexus-article-next__link" href="<?php echo esc_url( $next_link['url'] ); ?>"><?php echo esc_html( $next_link['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="nexus-rating nexus-reveal" data-track-section="article_rating" aria-labelledby="nexus-rating-title">
			<div class="nexus-rating__label"><?php esc_html_e( 'Feedback', 'blocksy-child' ); ?></div>
			<h2 id="nexus-rating-title" class="nexus-rating__title"><?php esc_html_e( 'War dieser Artikel hilfreich?', 'blocksy-child' ); ?></h2>
			<p class="nexus-rating__sub"><?php esc_html_e( 'Ihre Rückmeldung verbessert die nächsten Beiträge — kein Login nötig.', 'blocksy-child' ); ?></p>
			<div class="nexus-rating__buttons">
				<button class="nexus-rating__btn" type="button" data-rating="yes"><?php esc_html_e( 'Hilfreich', 'blocksy-child' ); ?></button>
				<button class="nexus-rating__btn" type="button" data-rating="no"><?php esc_html_e( 'Nicht hilfreich', 'blocksy-child' ); ?></button>
			</div>
			<div class="nexus-rating__feedback" aria-live="polite"><label class="screen-reader-text" for="nexus-rating-text"><?php esc_html_e( 'Optionale Rückmeldung', 'blocksy-child' ); ?></label><textarea id="nexus-rating-text" placeholder="<?php esc_attr_e( 'Was hat gefehlt? (optional)', 'blocksy-child' ); ?>"></textarea><div class="nexus-rating__feedback-actions"><button class="nexus-rating__skip" type="button"><?php esc_html_e( 'Überspringen', 'blocksy-child' ); ?></button><button class="nexus-rating__submit" type="button"><?php esc_html_e( 'Feedback senden', 'blocksy-child' ); ?></button></div></div>
			<p class="nexus-rating__thanks" role="status"><?php esc_html_e( '✓ Danke für Ihr Feedback.', 'blocksy-child' ); ?></p>
			<p class="nexus-rating__error" role="alert"></p>
		</section>

		<?php if ( ! $is_provider_decision_request ) : ?><?php get_template_part( 'template-parts/blog-notify', null, [ 'variant' => 'full' ] ); ?><?php endif; ?>

		<div class="nexus-bottom-share" data-track-section="article_share">
			<h3><?php esc_html_e( 'Diesen Artikel teilen', 'blocksy-child' ); ?></h3>
			<?php if ( function_exists( 'nexus_render_share_buttons' ) ) { nexus_render_share_buttons(); } ?>
		</div>

		<?php
		$canonical_author = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [];
		$author_name      = ! empty( $canonical_author['name'] ) ? (string) $canonical_author['name'] : get_the_author();
		$author_role      = ! empty( $canonical_author['jobTitle'] ) ? (string) $canonical_author['jobTitle'] . ' · Hannover' : __( 'WordPress-Entwicklung & Performance-Marketing · Hannover', 'blocksy-child' );
		$author_text      = ! empty( $canonical_author['description'] ) ? (string) $canonical_author['description'] : __( 'Ich verbinde WordPress-Entwicklung, technische Sichtbarkeit, Tracking und Conversion zu belastbaren Anfragepfaden.', 'blocksy-child' );
		$author_portrait  = function_exists( 'nexus_asset_url' ) ? nexus_asset_url( 'img/hasim-portrait-192.webp' ) : get_stylesheet_directory_uri() . '/assets/img/hasim-portrait-192.webp';
		?>
		<section class="nexus-author-bio nexus-reveal" data-track-section="article_author_bio" aria-labelledby="nexus-author-bio-name">
			<div class="nexus-author-bio__avatar" aria-hidden="true"><img class="nexus-author-bio__avatar-img" src="<?php echo esc_url( $author_portrait ); ?>" alt="" width="96" height="96" loading="lazy" decoding="async"></div>
			<div class="nexus-author-bio__content">
				<span class="nexus-author-bio__label"><?php esc_html_e( 'Über den Autor', 'blocksy-child' ); ?></span>
				<h2 id="nexus-author-bio-name" class="nexus-author-bio__name"><?php echo esc_html( $author_name ); ?></h2>
				<p class="nexus-author-bio__role"><?php echo esc_html( $author_role ); ?></p>
				<p class="nexus-author-bio__text"><?php echo esc_html( $author_text ); ?></p>
			</div>
		</section>

		<?php if ( ! $is_provider_decision_request ) : ?>
			<?php set_query_var( 'related_heading', __( 'Das könnte Sie auch interessieren', 'blocksy-child' ) ); set_query_var( 'related_count', 3 ); set_query_var( 'related_type', 'post' ); get_template_part( 'template-parts/related-content' ); ?>
			<?php get_template_part( 'template-parts/footer-cta' ); ?>
		<?php endif; ?>
	<?php endwhile; ?>
</div>

<button class="nexus-back-to-top" type="button" aria-label="<?php esc_attr_e( 'Zum Seitenanfang', 'blocksy-child' ); ?>">↑</button>

<?php get_footer(); ?>
