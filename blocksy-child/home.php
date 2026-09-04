<?php
/**
 * Blog home — editorial Werkstatt.
 *
 * Reader-first entry for WordPress, Tracking, Conversion and the
 * Solar/Wärmepumpe specialization. The regular WordPress loop stays the source
 * of truth; this template only gives it a deliberate editorial hierarchy.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts_page_id = (int) get_option( 'page_for_posts' );
$blog_url      = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' );
$route_map     = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];

$freelancer_url = $route_map['freelancer'] ?? home_url( '/wordpress-freelancer-hannover/' );
$whitelabel_url = $route_map['whitelabel'] ?? home_url( '/whitelabel-retainer/' );
$energy_url     = $route_map['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' );

$blog_categories = get_categories(
	[
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 8,
	]
);

/**
 * Resolve the first existing category from an editorial candidate list.
 *
 * @param array<int, string> $slugs Candidate category slugs.
 * @return array<string, string>
 */
$resolve_dossier_category = static function ( $slugs ) use ( $blog_url ) {
	foreach ( $slugs as $slug ) {
		$term = get_category_by_slug( sanitize_title( (string) $slug ) );
		if ( ! $term instanceof WP_Term ) {
			continue;
		}

		$url = get_category_link( $term->term_id );
		if ( is_wp_error( $url ) ) {
			continue;
		}

		return [
			'url'   => (string) $url,
			'label' => function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $term ) : $term->name,
		];
	}

	return [
		'url'   => (string) $blog_url,
		'label' => 'Alle Arbeiten',
	];
};

$dossiers = [
	[
		'number'      => '01',
		'title'       => 'Eigene Anfragen & Leadökonomie',
		'description' => 'Portale, CPL, CPO, Vorqualifizierung und die Frage, wann eigene Nachfrage wirtschaftlich besser wird.',
		'category'    => $resolve_dossier_category( [ 'leadgenerierung', 'markteinordnung' ] ),
	],
	[
		'number'      => '02',
		'title'       => 'WordPress & Performance',
		'description' => 'Relaunch, technische Architektur, Ladezeit und Messprotokolle — ohne Labwerte mit Felddaten zu verwechseln.',
		'category'    => $resolve_dossier_category( [ 'performance-marketing', 'wordpress', 'seo-sichtbarkeit' ] ),
	],
	[
		'number'      => '03',
		'title'       => 'Tracking & Messbarkeit',
		'description' => 'Server-Side Tracking, Attribution, Consent und die Stellen, an denen Datenketten in echten Setups brechen.',
		'category'    => $resolve_dossier_category( [ 'tracking', 'analytics' ] ),
	],
	[
		'number'      => '04',
		'title'       => 'Conversion & Anfragearchitektur',
		'description' => 'Landingpages, Formulare, CRM-Übergaben und Entscheidungswege zwischen Klick und qualifizierter Anfrage.',
		'category'    => $resolve_dossier_category( [ 'strategie', 'conversion', 'cro' ] ),
	],
];

$entry_paths = [
	[
		'kicker' => 'Direkte Projekte',
		'title'  => 'WordPress, Tracking & Conversion',
		'copy'   => 'Für Unternehmen, die Website, Messung und Conversion nicht auf drei Dienstleister verteilen wollen.',
		'url'    => $freelancer_url,
		'track'  => 'blog_start_freelancer',
	],
	[
		'kicker' => 'Für Agenturen',
		'title'  => 'White-Label im Hintergrund',
		'copy'   => 'WordPress, Tracking, CRO und technisches SEO als Umsetzungskapazität im Hintergrund der Agentur.',
		'url'    => $whitelabel_url,
		'track'  => 'blog_start_whitelabel',
	],
	[
		'kicker' => 'Solar & Wärmepumpe',
		'title'  => 'Eigene Anfragesysteme',
		'copy'   => 'Leadökonomie, Marktmodelle und Infrastruktur für Betriebe, die nicht von Portalleads abhängig bleiben wollen.',
		'url'    => $energy_url,
		'track'  => 'blog_start_energy',
	],
];

$blog_form_nonce  = wp_create_nonce( 'nexus_blog_notify_subscribe' );
$blog_notify_copy = function_exists( 'nexus_get_blog_notify_copy' ) ? nexus_get_blog_notify_copy() : [];
$privacy_url      = function_exists( 'nexus_get_page_url' )
	? nexus_get_page_url( [ 'datenschutz' ], home_url( '/datenschutz/' ) )
	: home_url( '/datenschutz/' );

// The blog index owns one additional presentation layer and reuses the existing
// subscription endpoint. Enqueue before get_header() so wp_head() can print the
// assets without widening the global frontend bundle.
if ( function_exists( 'hu_enqueue_css' ) ) {
	hu_enqueue_css( 'nexus-blog-home-v2-css', 'blog-home-v2.css', [ 'nexus-blog-archive-css' ] );
}

if ( function_exists( 'hu_enqueue_js' ) ) {
	hu_enqueue_js( 'nexus-blog-notify-js', 'blog-notify.js', [ 'nexus-core-js' ] );
	wp_localize_script(
		'nexus-blog-notify-js',
		'NexusBlogNotifyConfig',
		[
			'restEndpoint'   => esc_url_raw( rest_url( 'nexus/v1/blog-subscribe' ) ),
			'nonce'          => $blog_form_nonce,
			'successMessage' => 'Fast geschafft. Bitte bestätigen Sie Ihre Anmeldung über die E-Mail in Ihrem Postfach.',
			'errorMessage'   => 'Das hat gerade nicht funktioniert. Bitte prüfen Sie Ihre E-Mail-Adresse oder versuchen Sie es gleich noch einmal.',
		]
	);
}

get_header();
?>

<main id="main" class="site-main blog-bell blog-bell--werkstatt hu-hp" data-track-section="blog_archive">
	<section class="blog-bell__hero blog-workshop__hero" aria-labelledby="blog-archive-heading">
		<div class="blog-bell__container blog-workshop__hero-grid">
			<div class="blog-workshop__hero-copy">
				<span class="blog-bell__eyebrow">
					<span class="blog-bell__eyebrow-dot" aria-hidden="true"></span>
					Werkstatt
				</span>
				<h1 id="blog-archive-heading" class="blog-bell__title">Was ich messe, baue und zerlege.</h1>
				<p class="blog-bell__lead">Messprotokolle, Entscheidungsmodelle und Baupläne aus echten WordPress-, Tracking- und Anfragesystemen.</p>
			</div>
			<div class="blog-workshop__hero-index" aria-label="Thematische Schwerpunkte">
				<span>WordPress</span>
				<span>Tracking</span>
				<span>Conversion</span>
				<span>Solar / SHK</span>
			</div>
		</div>
	</section>

	<?php if ( ! is_paged() ) : ?>
		<section class="blog-workshop__section blog-workshop__section--start" aria-labelledby="blog-start-heading" data-track-section="blog_start_here">
			<div class="blog-bell__container">
				<div class="blog-workshop__section-head">
					<span class="blog-workshop__section-kicker">Start hier</span>
					<h2 id="blog-start-heading">Drei Wege. Ein technisches Fundament.</h2>
				</div>

				<div class="blog-workshop__entry-grid">
					<?php foreach ( $entry_paths as $index => $entry ) : ?>
						<a
							class="blog-workshop__entry"
							href="<?php echo esc_url( (string) $entry['url'] ); ?>"
							data-track-action="<?php echo esc_attr( (string) $entry['track'] ); ?>"
							data-track-category="navigation"
							data-reveal
						>
							<span class="blog-workshop__entry-number"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
							<span class="blog-workshop__entry-kicker"><?php echo esc_html( (string) $entry['kicker'] ); ?></span>
							<strong><?php echo esc_html( (string) $entry['title'] ); ?></strong>
							<span class="blog-workshop__entry-copy"><?php echo esc_html( (string) $entry['copy'] ); ?></span>
							<span class="blog-workshop__entry-arrow" aria-hidden="true">→</span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="blog-bell__main blog-workshop__main" data-track-section="blog_archive_grid">
		<div class="blog-bell__container">
			<?php if ( have_posts() ) : ?>
				<?php if ( ! is_paged() ) : ?>
					<?php
					$post_index = 0;
					while ( have_posts() ) :
						the_post();
						$post_index++;

						$post_id          = get_the_ID();
						$post_categories  = get_the_category( $post_id );
						$primary_category = ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ? $post_categories[0] : null;
						$primary_label    = $primary_category instanceof WP_Term
							? ( function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $primary_category ) : $primary_category->name )
							: '';
						$reading_time = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time( $post_id ) : 0;
						$excerpt      = wp_strip_all_tags( get_the_excerpt() );
						$excerpt      = $excerpt ? wp_trim_words( $excerpt, 30, '…' ) : '';
						?>

						<?php if ( 1 === $post_index ) : ?>
							<section class="blog-workshop__focus" aria-labelledby="blog-focus-heading" data-track-section="blog_focus" data-reveal>
								<div class="blog-workshop__focus-label">
									<span class="blog-workshop__section-kicker">Aktuell im Fokus</span>
									<span class="blog-workshop__focus-rule" aria-hidden="true"></span>
								</div>
								<a class="blog-workshop__focus-link" href="<?php the_permalink(); ?>" data-track-action="blog_focus_open" data-track-category="content">
									<div class="blog-workshop__focus-meta">
										<?php if ( $primary_category instanceof WP_Term ) : ?><span><?php echo esc_html( $primary_label ); ?></span><?php endif; ?>
										<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'd. M Y' ) ); ?></time>
										<?php if ( $reading_time > 0 ) : ?><span><?php echo esc_html( sprintf( '%d Min.', $reading_time ) ); ?></span><?php endif; ?>
									</div>
									<h2 id="blog-focus-heading"><?php the_title(); ?></h2>
									<?php if ( '' !== $excerpt ) : ?><p><?php echo esc_html( $excerpt ); ?></p><?php endif; ?>
									<span class="blog-workshop__focus-action">Arbeit öffnen <span aria-hidden="true">→</span></span>
								</a>
							</section>

							<section class="blog-workshop__dossiers" aria-labelledby="blog-dossiers-heading" data-track-section="blog_dossiers">
								<div class="blog-workshop__section-head blog-workshop__section-head--split">
									<div>
										<span class="blog-workshop__section-kicker">Dossiers</span>
										<h2 id="blog-dossiers-heading">Vier Felder, die zusammengehören.</h2>
									</div>
									<p>Die Themen sind getrennt, die Systeme nicht. Hier wird sichtbar, wo Technik, Messung und wirtschaftliche Wirkung ineinandergreifen.</p>
								</div>

								<div class="blog-workshop__dossier-list">
									<?php foreach ( $dossiers as $dossier ) : ?>
										<a
											class="blog-workshop__dossier"
											href="<?php echo esc_url( (string) $dossier['category']['url'] ); ?>"
											data-track-action="<?php echo esc_attr( 'blog_dossier_' . sanitize_title( (string) $dossier['title'] ) ); ?>"
											data-track-category="navigation"
											data-reveal
										>
											<span class="blog-workshop__dossier-number"><?php echo esc_html( (string) $dossier['number'] ); ?></span>
											<span class="blog-workshop__dossier-body">
												<strong><?php echo esc_html( (string) $dossier['title'] ); ?></strong>
												<span><?php echo esc_html( (string) $dossier['description'] ); ?></span>
											</span>
											<span class="blog-workshop__dossier-link"><?php echo esc_html( (string) $dossier['category']['label'] ); ?> <span aria-hidden="true">→</span></span>
										</a>
									<?php endforeach; ?>
								</div>
							</section>

							<section class="blog-workshop__latest" aria-labelledby="blog-latest-heading" data-track-section="blog_latest">
								<div class="blog-workshop__section-head blog-workshop__section-head--split">
									<div>
										<span class="blog-workshop__section-kicker">Neue Arbeiten</span>
										<h2 id="blog-latest-heading">Chronologisch. Ohne Algorithmus.</h2>
									</div>
									<p>Was zuletzt veröffentlicht oder überarbeitet wurde, steht oben.</p>
								</div>

								<?php if ( ! empty( $blog_categories ) ) : ?>
									<nav class="blog-bell__filter blog-workshop__filter" aria-label="<?php esc_attr_e( 'Artikel nach Kategorie filtern', 'blocksy-child' ); ?>">
										<div class="blog-bell__filter-inner">
											<span class="blog-bell__filter-label">Themen</span>
											<a class="blog-bell__chip is-active" href="<?php echo esc_url( $blog_url ); ?>" aria-current="page" data-track-action="blog_filter_all" data-track-category="navigation">Alle</a>
											<?php foreach ( $blog_categories as $category ) : ?>
												<?php
												$category_url   = get_category_link( $category->term_id );
												$category_label = function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $category ) : $category->name;
												if ( is_wp_error( $category_url ) ) {
													continue;
												}
												?>
												<a class="blog-bell__chip" href="<?php echo esc_url( $category_url ); ?>" data-track-action="<?php echo esc_attr( 'blog_filter_' . $category->slug ); ?>" data-track-category="navigation"><?php echo esc_html( $category_label ); ?></a>
											<?php endforeach; ?>
										</div>
									</nav>
								<?php endif; ?>

								<div class="blog-workshop__latest-list">
						<?php else : ?>
							<article class="blog-workshop__latest-item" data-reveal>
								<a href="<?php the_permalink(); ?>" aria-labelledby="blog-workshop-title-<?php echo esc_attr( (string) $post_id ); ?>">
									<div class="blog-workshop__latest-meta">
										<span class="blog-workshop__latest-index"><?php echo esc_html( sprintf( '%02d', $post_index - 1 ) ); ?></span>
										<?php if ( $primary_category instanceof WP_Term ) : ?><span><?php echo esc_html( $primary_label ); ?></span><?php endif; ?>
										<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'd. M Y' ) ); ?></time>
										<?php if ( $reading_time > 0 ) : ?><span><?php echo esc_html( sprintf( '%d Min.', $reading_time ) ); ?></span><?php endif; ?>
									</div>
									<h3 id="blog-workshop-title-<?php echo esc_attr( (string) $post_id ); ?>"><?php the_title(); ?></h3>
									<?php if ( '' !== $excerpt ) : ?><p><?php echo esc_html( $excerpt ); ?></p><?php endif; ?>
									<span class="blog-workshop__latest-arrow" aria-hidden="true">→</span>
								</a>
							</article>
						<?php endif; ?>
					<?php endwhile; ?>
								</div>
							</section>
				<?php else : ?>
					<section class="blog-workshop__latest blog-workshop__latest--paged" aria-labelledby="blog-latest-heading">
						<div class="blog-workshop__section-head">
							<span class="blog-workshop__section-kicker">Archiv</span>
							<h2 id="blog-latest-heading">Weitere Arbeiten.</h2>
						</div>
						<div class="blog-workshop__latest-list">
							<?php
							$post_index = 0;
							while ( have_posts() ) :
								the_post();
								$post_index++;
								$post_id          = get_the_ID();
								$post_categories  = get_the_category( $post_id );
								$primary_category = ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ? $post_categories[0] : null;
								$primary_label    = $primary_category instanceof WP_Term ? ( function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $primary_category ) : $primary_category->name ) : '';
								$reading_time     = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time( $post_id ) : 0;
								$excerpt          = wp_strip_all_tags( get_the_excerpt() );
								$excerpt          = $excerpt ? wp_trim_words( $excerpt, 24, '…' ) : '';
								?>
								<article class="blog-workshop__latest-item" data-reveal>
									<a href="<?php the_permalink(); ?>">
										<div class="blog-workshop__latest-meta">
											<span class="blog-workshop__latest-index"><?php echo esc_html( sprintf( '%02d', $post_index ) ); ?></span>
											<?php if ( $primary_category instanceof WP_Term ) : ?><span><?php echo esc_html( $primary_label ); ?></span><?php endif; ?>
											<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'd. M Y' ) ); ?></time>
											<?php if ( $reading_time > 0 ) : ?><span><?php echo esc_html( sprintf( '%d Min.', $reading_time ) ); ?></span><?php endif; ?>
										</div>
										<h3><?php the_title(); ?></h3>
										<?php if ( '' !== $excerpt ) : ?><p><?php echo esc_html( $excerpt ); ?></p><?php endif; ?>
										<span class="blog-workshop__latest-arrow" aria-hidden="true">→</span>
									</a>
								</article>
							<?php endwhile; ?>
						</div>
					</section>
				<?php endif; ?>

				<nav class="blog-bell__pagination" aria-label="<?php esc_attr_e( 'Seiten', 'blocksy-child' ); ?>">
					<?php
					the_posts_pagination(
						[
							'mid_size'  => 1,
							'prev_text' => esc_html__( 'Zurück', 'blocksy-child' ),
							'next_text' => esc_html__( 'Weiter', 'blocksy-child' ),
						]
					);
					?>
				</nav>
			<?php else : ?>
				<p class="blog-bell__empty"><?php esc_html_e( 'Aktuell sind keine Beiträge veröffentlicht.', 'blocksy-child' ); ?></p>
			<?php endif; ?>

			<aside class="blog-workshop__notify" aria-labelledby="blog-workshop-notify-heading" data-track-section="blog_archive_email">
				<div>
					<span class="blog-workshop__section-kicker">Neue Arbeiten</span>
					<h2 id="blog-workshop-notify-heading">Nur eine Mail, wenn etwas Neues online ist.</h2>
					<p>Kein Newsletter-Rauschen. Keine Sales-Serie. Nur der Hinweis auf einen neuen Beitrag.</p>
				</div>
				<button type="button" class="blog-workshop__notify-button" data-blog-bell-open aria-haspopup="dialog" aria-controls="blog-bell-modal">
					E-Mail-Updates aktivieren <span aria-hidden="true">→</span>
				</button>
			</aside>
		</div>
	</section>
</main>

<button
	class="blog-bell__bell blog-workshop__bell"
	type="button"
	id="blog-bell-trigger"
	aria-label="<?php esc_attr_e( 'Neue Artikel per E-Mail abonnieren', 'blocksy-child' ); ?>"
	aria-haspopup="dialog"
	aria-expanded="false"
	aria-controls="blog-bell-modal"
>
	<svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
		<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
		<path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
	</svg>
	<span class="blog-bell__bell-label">E-Mail-Updates</span>
</button>

<div
	class="blog-bell__modal blog-workshop__modal"
	id="blog-bell-modal"
	role="dialog"
	aria-modal="true"
	aria-labelledby="blog-bell-modal-title"
	hidden
>
	<div class="blog-bell__modal-backdrop" data-blog-bell-dismiss></div>

	<div class="blog-bell__modal-panel" role="document">
		<button class="blog-bell__modal-close" type="button" data-blog-bell-dismiss aria-label="<?php esc_attr_e( 'Schließen', 'blocksy-child' ); ?>">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
				<path d="M18 6L6 18M6 6l12 12"></path>
			</svg>
		</button>

		<span class="blog-bell__eyebrow">Blog-Benachrichtigungen</span>
		<h2 id="blog-bell-modal-title" class="blog-bell__modal-title"><?php echo esc_html( $blog_notify_copy['headline'] ?? 'Neue Artikel per E-Mail' ); ?></h2>
		<p class="blog-bell__modal-body"><?php echo esc_html( $blog_notify_copy['body'] ?? 'Ich schicke nur dann eine kurze Mail, wenn ein neuer Beitrag online ist. Kein Newsletter-Rauschen. Keine Sales-Mails.' ); ?></p>

		<form class="blog-bell__form" data-blog-notify-form novalidate>
			<div class="blog-bell__honeypot" aria-hidden="true">
				<label for="blog-bell-website">Website</label>
				<input id="blog-bell-website" type="text" name="website" tabindex="-1" autocomplete="off">
			</div>
			<input type="hidden" name="nonce" value="<?php echo esc_attr( $blog_form_nonce ); ?>">
			<input type="hidden" name="contextPostId" value="0">
			<label class="screen-reader-text" for="blog-bell-email"><?php esc_html_e( 'E-Mail-Adresse', 'blocksy-child' ); ?></label>
			<input id="blog-bell-email" class="blog-bell__input" type="email" name="email" placeholder="<?php echo esc_attr( $blog_notify_copy['placeholder'] ?? 'Ihre E-Mail-Adresse' ); ?>" autocomplete="email" required>
			<button type="submit" class="blog-bell__submit">
				<?php echo esc_html( $blog_notify_copy['button'] ?? 'Artikel-Benachrichtigungen aktivieren' ); ?>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
			</button>
			<ul class="blog-bell__trust">
				<li>Nur neue Artikel</li>
				<li>Keine Werbemails</li>
				<li>Jederzeit abmelden</li>
			</ul>
			<p class="blog-bell__hint">
				<?php esc_html_e( 'Double-Opt-In über E-Mail.', 'blocksy-child' ); ?>
				<a href="<?php echo esc_url( $privacy_url ); ?>"><?php esc_html_e( 'Datenschutz', 'blocksy-child' ); ?></a>
			</p>
			<div class="blog-bell__feedback" data-blog-notify-feedback aria-live="polite"></div>
		</form>
	</div>
</div>

<?php get_footer(); ?>
