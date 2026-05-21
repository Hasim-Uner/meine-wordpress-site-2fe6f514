<?php
/**
 * NEXUS Single Post Template
 *
 * Features: Dynamisches Hero-Bild, Breadcrumb, Share, Related Content, Footer-CTA.
 * Tracking-ready via data-track-* Attribute.
 *
 * [Flywheel] single.php: Blog Post mit Breadcrumb, Related Content, Footer-CTA
 *
 * @package Blocksy_Child
 */

get_header();
get_template_part( 'template-parts/blog-header' );
?>

<div class="nexus-reading-progress" aria-hidden="true"></div>

<aside class="nexus-share-rail" aria-label="<?php esc_attr_e( 'Artikel teilen', 'blocksy-child' ); ?>">
	<span class="nexus-share-rail__label"><?php esc_html_e( 'Teilen', 'blocksy-child' ); ?></span>
	<button class="nexus-share-rail__btn" type="button" data-nexus-share="linkedin" aria-label="LinkedIn">
		<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
	</button>
	<button class="nexus-share-rail__btn" type="button" data-nexus-share="x" aria-label="X">
		<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
	</button>
	<button class="nexus-share-rail__btn" type="button" data-nexus-share="email" aria-label="<?php esc_attr_e( 'Per E-Mail teilen', 'blocksy-child' ); ?>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
	</button>
	<button class="nexus-share-rail__btn" type="button" data-nexus-share="copy" aria-label="<?php esc_attr_e( 'Link kopieren', 'blocksy-child' ); ?>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
	</button>
</aside>

<main id="main" class="site-main nexus-single-container nexus-single-container--with-blog-header nexus-single-container--editorial hu-hp">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php
		$has_hero_image = has_post_thumbnail();
		$article_summary = wp_strip_all_tags( get_the_excerpt() );
		if ( '' === $article_summary ) {
			$article_summary = wp_trim_words( wp_strip_all_tags( get_the_content() ), 30, '...' );
		}
		$reading_time = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time() : 0;
		$primary_urls    = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
		$audit_url       = function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
		$energy_url      = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
		$agentur_url     = $primary_urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' );
		$seo_url         = $primary_urls['seo'] ?? trailingslashit( $agentur_url ) . '#technisches-seo';
		$cro_url         = $primary_urls['cro'] ?? trailingslashit( $agentur_url ) . '#methode';
		$tracking_url    = home_url( '/server-side-tracking-b2b/' );
		$portal_url      = home_url( '/eigene-leadgenerierung-vs-portale/' );
		$cpl_url         = home_url( '/cost-per-lead-photovoltaik/' );
		$post_categories = get_the_category();
		$post_cat_slugs  = wp_list_pluck( $post_categories, 'slug' );
		$primary_cat     = ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ? $post_categories[0] : null;
		$category_url    = $primary_cat instanceof WP_Term ? get_category_link( $primary_cat->term_id ) : '';
		$category_url    = is_wp_error( $category_url ) ? '' : $category_url;
		$article_context = [
			'eyebrow'         => __( 'Einordnung', 'blocksy-child' ),
			'title'           => __( 'Dieser Artikel gehört in den größeren Anfrage-Kontext.', 'blocksy-child' ),
			'text'            => __( 'Lesen Sie den Beitrag als Baustein im Zusammenspiel aus Angebot, Sichtbarkeit, Daten und Conversion.', 'blocksy-child' ),
			'primary_label'   => __( 'Anfrage-System ansehen', 'blocksy-child' ),
			'primary_url'     => $energy_url,
			'secondary_label' => __( 'Kostenfreien Marktcheck starten', 'blocksy-child' ),
			'secondary_url'   => $audit_url,
		];

		if ( in_array( 'solar-waermepumpen-anfrage-systeme', $post_cat_slugs, true ) ) {
			$article_context = [
				'eyebrow'         => __( 'Solar-Fokus', 'blocksy-child' ),
				'title'           => __( 'Teil des Anfrage-Systems für Solar & Wärmepumpe.', 'blocksy-child' ),
				'text'            => __( 'Dieser Artikel ordnet einen Baustein ein: weniger Portal-Abhängigkeit, klarere Angebotsseiten, bessere Daten und ein Anfragepfad im eigenen Besitz.', 'blocksy-child' ),
				'primary_label'   => __( 'Anfrage-System ansehen', 'blocksy-child' ),
				'primary_url'     => $energy_url,
				'secondary_label' => __( 'Kostenfreien Marktcheck starten', 'blocksy-child' ),
				'secondary_url'   => $audit_url,
			];
		} elseif ( array_intersect( [ 'markteinordnung', 'owned-leads' ], $post_cat_slugs ) ) {
			$article_context = [
				'eyebrow'         => __( 'Portal-Abhängigkeit', 'blocksy-child' ),
				'title'           => __( 'Portal-Leads sind nur ein Kostenblock. Entscheidend ist der CPO.', 'blocksy-child' ),
				'text'            => __( 'Dieser Artikel gehört in die Frage, ob Nachfrage dauerhaft gemietet oder als eigenes System aufgebaut werden sollte.', 'blocksy-child' ),
				'primary_label'   => __( 'Portalvergleich lesen', 'blocksy-child' ),
				'primary_url'     => $portal_url,
				'secondary_label' => __( 'CPL/CPO-Rechnung ansehen', 'blocksy-child' ),
				'secondary_url'   => $cpl_url,
			];
		} elseif ( in_array( 'sichtbarkeit-daten-conversion', $post_cat_slugs, true ) ) {
			$article_context = [
				'eyebrow'         => __( 'SEO · Daten · Conversion', 'blocksy-child' ),
				'title'           => __( 'Sichtbarkeit wird erst mit Daten und Conversion kaufnah.', 'blocksy-child' ),
				'text'            => __( 'Der Beitrag zeigt einen Hebel im System. Entscheidend ist, ob daraus ein belastbarer Anfragepfad für passende Solar- und Wärmepumpen-Anbieter entsteht.', 'blocksy-child' ),
				'primary_label'   => __( 'Technisches SEO ansehen', 'blocksy-child' ),
				'primary_url'     => $seo_url,
				'secondary_label' => __( 'Server-Side Tracking ansehen', 'blocksy-child' ),
				'secondary_url'   => $tracking_url,
			];
		} elseif ( in_array( 'tracking', $post_cat_slugs, true ) ) {
			$article_context = [
				'eyebrow'         => __( 'Tracking & Daten', 'blocksy-child' ),
				'title'           => __( 'Tracking ist nur wertvoll, wenn daraus bessere Entscheidungen entstehen.', 'blocksy-child' ),
				'text'            => __( 'Dieser Artikel gehört in die Datenebene: Consent, Server-Side Tracking, CRM-Rückführung und saubere Signale für Marketing-Algorithmen.', 'blocksy-child' ),
				'primary_label'   => __( 'Server-Side Tracking ansehen', 'blocksy-child' ),
				'primary_url'     => $tracking_url,
				'secondary_label' => __( 'CRO-System ansehen', 'blocksy-child' ),
				'secondary_url'   => $cro_url,
			];
		} elseif ( array_intersect( [ 'seo', 'cro', 'wordpress-performance', 'strategie' ], $post_cat_slugs ) ) {
			$article_context = [
				'eyebrow'         => __( 'WordPress-System', 'blocksy-child' ),
				'title'           => __( 'Einzelhebel wirken erst im verbundenen System.', 'blocksy-child' ),
				'text'            => __( 'Dieser Beitrag ordnet einen Hebel ein: Technik, Sichtbarkeit, Performance oder Conversion. Entscheidend ist die Reihenfolge im Gesamtsystem.', 'blocksy-child' ),
				'primary_label'   => __( 'WordPress Agentur ansehen', 'blocksy-child' ),
				'primary_url'     => $agentur_url,
				'secondary_label' => __( 'Technisches SEO ansehen', 'blocksy-child' ),
				'secondary_url'   => $seo_url,
			];
		} elseif ( in_array( 'wordpress-growth-agentur', $post_cat_slugs, true ) ) {
			$article_context = [
				'eyebrow'         => __( 'WordPress-Growth', 'blocksy-child' ),
				'title'           => __( 'WordPress als System statt als Einzelleistung.', 'blocksy-child' ),
				'text'            => __( 'Dieser Beitrag ist der allgemeinere WordPress-Kontext. Wenn es um lokale Umsetzung, Wartung, SEO und Conversion geht, ist die Agentur-Seite der passende Anschluss.', 'blocksy-child' ),
				'primary_label'   => __( 'WordPress Agentur ansehen', 'blocksy-child' ),
				'primary_url'     => $agentur_url,
				'secondary_label' => __( 'Kostenfreien Marktcheck starten', 'blocksy-child' ),
				'secondary_url'   => $audit_url,
			];
		}

		$article_next_links = [
			[
				'label'    => $article_context['primary_label'],
				'url'      => $article_context['primary_url'],
				'category' => 'internal_link',
			],
			[
				'label'    => $article_context['secondary_label'],
				'url'      => $article_context['secondary_url'],
				'category' => $audit_url === $article_context['secondary_url'] ? 'lead_gen' : 'internal_link',
			],
			[
				'label'    => __( 'Regionalen Marktcheck starten', 'blocksy-child' ),
				'url'      => $audit_url,
				'category' => 'lead_gen',
			],
		];

		if ( $category_url ) {
			array_unshift(
				$article_next_links,
				[
					'label'    => sprintf(
						/* translators: %s: category name */
						__( 'Mehr aus %s', 'blocksy-child' ),
						$primary_cat->name
					),
					'url'      => $category_url,
					'category' => 'internal_link',
				]
			);
		}

		$deduped_next_links = [];
		$seen_next_urls     = [];

		foreach ( $article_next_links as $next_link ) {
			$url_key = isset( $next_link['url'] ) ? (string) $next_link['url'] : '';

			if ( '' === $url_key || isset( $seen_next_urls[ $url_key ] ) ) {
				continue;
			}

			$seen_next_urls[ $url_key ] = true;
			$deduped_next_links[]      = $next_link;
		}

		$article_next_links = $deduped_next_links;
		?>

		<header class="nexus-article-hero nexus-article-hero--editorial" data-track-section="article_hero" aria-labelledby="nexus-article-title">
			<div class="nexus-hero-content">

				<div class="nexus-meta-top">
					<?php if ( $primary_cat instanceof WP_Term && $category_url ) : ?>
						<a class="nexus-hero-category" href="<?php echo esc_url( $category_url ); ?>">
							<?php echo esc_html( $primary_cat->name ); ?>
						</a>
					<?php endif; ?>
					<span class="nexus-date"><?php echo esc_html( get_the_date( 'd. M Y' ) ); ?></span>
					<span class="separator">|</span>
					<?php if ( $reading_time > 0 ) : ?>
						<span class="nexus-reading-time"><?php
							printf(
								/* translators: %d: reading time in minutes */
								esc_html__( '%d Min. Lesezeit', 'blocksy-child' ),
								$reading_time
							);
						?></span>
					<?php endif; ?>
				</div>

				<h1 id="nexus-article-title" class="nexus-title"><?php echo esc_html( get_the_title() ); ?></h1>

				<?php if ( '' !== $article_summary ) : ?>
					<p class="nexus-subtitle"><?php echo esc_html( $article_summary ); ?></p>
				<?php endif; ?>

				<div class="nexus-hero-footer">
					<div class="nexus-author-row">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 48 ); ?>
						<div class="nexus-author-info">
							<span class="by"><?php esc_html_e( 'Geschrieben von', 'blocksy-child' ); ?></span>
							<span class="name"><?php echo esc_html( get_the_author() ); ?></span>
						</div>
					</div>
				</div>

			</div>
		</header>

		<figure class="nexus-article-cover nexus-reveal" data-track-section="article_cover">
			<div class="nexus-hero-image<?php echo esc_attr( $has_hero_image ? '' : ' nexus-hero-image--generated' ); ?>">
				<?php if ( $has_hero_image ) : ?>
					<?php
					the_post_thumbnail(
						'full',
						[
							'loading'       => 'eager',
							'fetchpriority' => 'high',
							'decoding'      => 'async',
							'sizes'         => '(max-width: 900px) 100vw, 50vw',
						]
					);
					?>
				<?php else : ?>
					<?php
					get_template_part(
						'template-parts/post-title-visual',
						null,
						[
							'post_id' => get_the_ID(),
							'variant' => 'hero',
						]
					);
					?>
				<?php endif; ?>
			</div>
		</figure>

		<section class="nexus-article-context nexus-reveal" data-track-section="article_context_bridge" aria-label="<?php echo esc_attr( $article_context['eyebrow'] ); ?>">
			<div class="nexus-article-context__copy">
				<span class="nexus-article-context__eyebrow"><?php echo esc_html( $article_context['eyebrow'] ); ?></span>
				<h2 class="nexus-article-context__title"><?php echo esc_html( $article_context['title'] ); ?></h2>
				<p class="nexus-article-context__text"><?php echo esc_html( $article_context['text'] ); ?></p>
			</div>
			<div class="nexus-article-context__actions">
				<a
					href="<?php echo esc_url( $article_context['primary_url'] ); ?>"
					class="nexus-article-context__link nexus-article-context__link--primary"
					data-track-action="cta_article_context_primary"
					data-track-category="internal_link"
				>
					<?php echo esc_html( $article_context['primary_label'] ); ?>
				</a>
				<a
					href="<?php echo esc_url( $article_context['secondary_url'] ); ?>"
					class="nexus-article-context__link"
					data-track-action="cta_article_context_secondary"
					data-track-category="lead_gen"
				>
					<?php echo esc_html( $article_context['secondary_label'] ); ?>
				</a>
			</div>
		</section>

		<div class="nexus-post-layout">
			<?php if ( is_singular( 'post' ) ) : ?>
				<aside class="nexus-sidebar" aria-label="<?php esc_attr_e( 'Inhaltsverzeichnis', 'blocksy-child' ); ?>">
					<div class="sticky-toc">
						<h2><?php esc_html_e( 'Inhalt', 'blocksy-child' ); ?></h2>
						<ul id="toc-list"></ul>
					</div>
				</aside>
			<?php endif; ?>
			<article class="nexus-article-content nexus-reveal" id="article-content" data-track-section="article_content">
				<?php the_content(); ?>

				<div class="nexus-inline-cta" id="nexus-inline-cta" hidden aria-hidden="true">
					<div class="nexus-inline-cta__inner">
						<span class="nexus-inline-cta__tag">Kostenlose Diagnose</span>
						<h3 class="nexus-inline-cta__headline">Was bremst Ihr Wachstum?</h3>
						<p class="nexus-inline-cta__sub">Persönliche Analyse Ihrer Website - schriftliche Rückmeldung in 48 Stunden.</p>
						<a href="<?php echo esc_url( $audit_url ); ?>"
							class="nexus-btn nexus-btn--primary nexus-inline-cta__btn"
							data-track-action="cta_blog_inline"
							data-track-category="lead_gen">
							Kostenfreien Marktcheck starten
						</a>
					</div>
				</div>

				<?php
					if ( function_exists( 'nexus_get_wgos_blog_asset_bridge' ) && function_exists( 'nexus_render_wgos_blog_asset_bridge' ) ) {
						$bridge = nexus_get_wgos_blog_asset_bridge();

						if ( is_array( $bridge ) ) {
							echo nexus_render_wgos_blog_asset_bridge( $bridge ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
					}
				?>
			</article>
		</div>

		<section class="nexus-article-next" data-track-section="article_next_steps" aria-labelledby="nexus-article-next-heading">
			<span class="nexus-article-next__eyebrow"><?php esc_html_e( 'Weiterarbeiten', 'blocksy-child' ); ?></span>
			<h2 id="nexus-article-next-heading" class="nexus-article-next__title"><?php esc_html_e( 'Nächster sinnvoller Schritt.', 'blocksy-child' ); ?></h2>
			<p class="nexus-article-next__text">
				<?php esc_html_e( 'Wenn der Kontext sitzt, geht es nicht um mehr Content, sondern um die richtige Anschlussentscheidung.', 'blocksy-child' ); ?>
			</p>
			<div class="nexus-article-next__links">
				<?php foreach ( $article_next_links as $index => $next_link ) : ?>
					<?php if ( empty( $next_link['url'] ) || empty( $next_link['label'] ) ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<a
						class="nexus-article-next__link"
						href="<?php echo esc_url( $next_link['url'] ); ?>"
						data-track-action="<?php echo esc_attr( 'article_next_step_' . ( $index + 1 ) ); ?>"
						data-track-category="<?php echo esc_attr( $next_link['category'] ); ?>"
						data-track-section="article_next_steps"
					>
						<?php echo esc_html( $next_link['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		</section>

		<?php if ( is_singular( 'post' ) ) : ?>
		<section class="nexus-rating nexus-reveal" data-track-section="article_rating" aria-labelledby="nexus-rating-title">
			<div class="nexus-rating__label"><?php esc_html_e( 'Feedback', 'blocksy-child' ); ?></div>
			<h2 id="nexus-rating-title" class="nexus-rating__title"><?php esc_html_e( 'War dieser Artikel hilfreich?', 'blocksy-child' ); ?></h2>
			<p class="nexus-rating__sub"><?php esc_html_e( 'Ihre Rückmeldung verbessert die nächsten Beiträge — kein Login nötig.', 'blocksy-child' ); ?></p>

			<div class="nexus-rating__buttons">
				<button class="nexus-rating__btn" type="button" data-rating="yes" data-track-action="post_rating_yes" data-track-category="engagement">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 10v12"/><path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H7V10l5-8 1.74 1.16A2 2 0 0 1 14.5 5l.5.88z"/></svg>
					<?php esc_html_e( 'Hilfreich', 'blocksy-child' ); ?>
				</button>
				<button class="nexus-rating__btn" type="button" data-rating="no" data-track-action="post_rating_no" data-track-category="engagement">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M17 14V2"/><path d="M9 18.12 10 14H4.17a2 2 0 0 1-1.92-2.56l2.33-8A2 2 0 0 1 6.5 2H17v12l-5 8-1.74-1.16A2 2 0 0 1 9.5 19l-.5-.88z"/></svg>
					<?php esc_html_e( 'Nicht hilfreich', 'blocksy-child' ); ?>
				</button>
			</div>

			<div class="nexus-rating__feedback" aria-live="polite">
				<label class="screen-reader-text" for="nexus-rating-text"><?php esc_html_e( 'Optionale Rückmeldung', 'blocksy-child' ); ?></label>
				<textarea id="nexus-rating-text" placeholder="<?php esc_attr_e( 'Was hat gefehlt? Welcher Punkt blieb unklar? (optional)', 'blocksy-child' ); ?>"></textarea>
				<div class="nexus-rating__feedback-actions">
					<button class="nexus-rating__skip" type="button"><?php esc_html_e( 'Überspringen', 'blocksy-child' ); ?></button>
					<button class="nexus-rating__submit" type="button">
						<?php esc_html_e( 'Feedback senden', 'blocksy-child' ); ?>
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
					</button>
				</div>
			</div>

			<p class="nexus-rating__thanks" role="status"><?php esc_html_e( '✓ Danke für Ihr Feedback.', 'blocksy-child' ); ?></p>
			<p class="nexus-rating__error" role="alert"></p>
		</section>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/blog-notify', null, [ 'variant' => 'full' ] ); ?>

		<?php if ( is_singular( 'post' ) ) : ?>
		<div class="nexus-bottom-share" data-track-section="article_share">
			<h3><?php esc_html_e( 'Diesen Artikel teilen', 'blocksy-child' ); ?></h3>
			<?php if ( function_exists( 'nexus_render_share_buttons' ) ) { nexus_render_share_buttons(); } ?>
		</div>

		<?php
		$author_id          = get_the_author_meta( 'ID' );
		$author_name        = get_the_author();
		$author_description = get_the_author_meta( 'description' );
		$author_avatar      = get_avatar( $author_id, 96, '', $author_name, [ 'class' => 'nexus-author-bio__avatar-img' ] );
		$author_initials    = '';
		if ( $author_name ) {
			$parts = preg_split( '/\s+/', trim( wp_strip_all_tags( $author_name ) ) );
			foreach ( (array) $parts as $part ) {
				if ( '' === $part ) {
					continue;
				}
				$author_initials .= function_exists( 'mb_substr' ) ? mb_substr( $part, 0, 1 ) : substr( $part, 0, 1 );
				if ( strlen( $author_initials ) >= 2 ) {
					break;
				}
			}
		}
		$author_role     = trim( (string) get_the_author_meta( 'hu_author_role' ) );
		if ( '' === $author_role ) {
			$author_role = __( 'Architekt für eigene Anfrage-Systeme · Hannover', 'blocksy-child' );
		}
		if ( '' === trim( (string) $author_description ) ) {
			$author_description = __( 'Ich baue Solar- und Wärmepumpen-Anbietern im DACH-Raum eigene Anfrage-Systeme, die Portal-Abhängigkeit ablösen und Leadkosten messbar senken. Diagnose vor Pitch. Klarheit vor Feature-Count.', 'blocksy-child' );
		}
		?>
		<section class="nexus-author-bio nexus-reveal" data-track-section="article_author_bio" aria-labelledby="nexus-author-bio-name">
			<div class="nexus-author-bio__avatar" aria-hidden="true">
				<?php if ( $author_avatar ) : ?>
					<?php echo $author_avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — get_avatar() returns safe HTML. ?>
				<?php else : ?>
					<?php echo esc_html( strtoupper( $author_initials ?: 'HÜ' ) ); ?>
				<?php endif; ?>
			</div>
			<div class="nexus-author-bio__content">
				<span class="nexus-author-bio__label"><?php esc_html_e( 'Über den Autor', 'blocksy-child' ); ?></span>
				<h2 id="nexus-author-bio-name" class="nexus-author-bio__name"><?php echo esc_html( $author_name ); ?></h2>
				<p class="nexus-author-bio__role"><?php echo esc_html( $author_role ); ?></p>
				<p class="nexus-author-bio__text"><?php echo esc_html( $author_description ); ?></p>
				<div class="nexus-author-bio__links">
					<a
						href="<?php echo esc_url( $audit_url ); ?>"
						class="nexus-author-bio__link"
						data-track-action="cta_author_bio_marktcheck"
						data-track-category="lead_gen"
					>
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
						<?php esc_html_e( 'Marktcheck starten', 'blocksy-child' ); ?>
					</a>
					<?php $about_url = function_exists( 'nexus_get_page_url' ) ? nexus_get_page_url( [ 'uber-mich', 'ueber-mich', 'ueber-hasim' ], home_url( '/uber-mich/' ) ) : home_url( '/uber-mich/' ); ?>
					<a
						href="<?php echo esc_url( $about_url ); ?>"
						class="nexus-author-bio__link"
						data-track-action="cta_author_bio_about"
						data-track-category="internal_link"
					>
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
						<?php esc_html_e( 'Mehr über Haşim', 'blocksy-child' ); ?>
					</a>
					<a
						href="https://www.linkedin.com/in/hasim-uener"
						class="nexus-author-bio__link"
						rel="noopener"
						target="_blank"
						data-track-action="cta_author_bio_linkedin"
						data-track-category="external_link"
					>
						<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
						<?php esc_html_e( 'LinkedIn', 'blocksy-child' ); ?>
					</a>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<?php
		// Related Content (gleiche Kategorie)
		set_query_var( 'related_heading', __( 'Das könnte Sie auch interessieren', 'blocksy-child' ) );
		set_query_var( 'related_count', 3 );
		set_query_var( 'related_type', 'post' );
		get_template_part( 'template-parts/related-content' );
		?>

		<?php get_template_part( 'template-parts/footer-cta' ); ?>

	<?php endwhile; ?>

</main>

<button class="nexus-back-to-top" type="button" aria-label="<?php esc_attr_e( 'Zum Seitenanfang', 'blocksy-child' ); ?>">
	<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m18 15-6-6-6 6"/></svg>
</button>

<?php
get_footer();
