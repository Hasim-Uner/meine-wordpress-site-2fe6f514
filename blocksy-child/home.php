<?php
/**
 * Blog Archive — "Bell" architecture.
 *
 * Replaces the editorial cluster layout with a card-grid + sticky filter +
 * floating newsletter bell modal. All content comes from the WP_Query loop —
 * no hard-coded post titles, dates, or numbers.
 *
 * Re-uses:
 *  - template-parts/site-header.php (global)
 *  - template-parts/site-footer.php (global)
 *  - inc/blog-notify.php REST endpoint (DOI subscription, NexusBlogNotifyConfig)
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$audit_url     = function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$posts_page_id = (int) get_option( 'page_for_posts' );
$blog_url      = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' );

$blog_categories = get_categories(
	[
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 8,
	]
);

$current_category_id = is_category() ? (int) get_queried_object_id() : 0;

$blog_form_nonce  = wp_create_nonce( 'nexus_blog_notify_subscribe' );
$blog_notify_copy = function_exists( 'nexus_get_blog_notify_copy' ) ? nexus_get_blog_notify_copy() : [];
$privacy_url      = function_exists( 'nexus_get_page_url' )
	? nexus_get_page_url( [ 'datenschutz' ], home_url( '/datenschutz/' ) )
	: home_url( '/datenschutz/' );

get_header();
?>

<main id="main" class="site-main blog-bell hu-hp" data-track-section="blog_archive">
	<section class="blog-bell__hero" aria-labelledby="blog-archive-heading">
		<div class="blog-bell__container">
			<span class="blog-bell__eyebrow">
				<span class="blog-bell__eyebrow-dot" aria-hidden="true"></span>
				Blog
			</span>
			<h1 id="blog-archive-heading" class="blog-bell__title">
				Analysen für eigene Anfrage-Systeme.
			</h1>
			<p class="blog-bell__lead">
				Portal-Abhängigkeit, Leadkosten, Tracking und Vorqualifizierung — für Solar-, Wärmepumpen- und Speicher-Anbieter im DACH-Raum, die eigene Nachfrage-Infrastruktur aufbauen wollen.
			</p>
		</div>
	</section>

	<?php if ( ! empty( $blog_categories ) ) : ?>
		<nav class="blog-bell__filter" aria-label="<?php esc_attr_e( 'Artikel nach Kategorie filtern', 'blocksy-child' ); ?>" data-track-section="blog_archive_filter">
			<div class="blog-bell__filter-inner">
				<span class="blog-bell__filter-label">Themen</span>
				<a
					class="blog-bell__chip <?php echo $current_category_id ? '' : 'is-active'; ?>"
					href="<?php echo esc_url( $blog_url ); ?>"
					<?php echo $current_category_id ? '' : 'aria-current="page"'; ?>
					data-track-action="blog_filter_all"
					data-track-category="navigation"
				>
					Alle
				</a>
				<?php foreach ( $blog_categories as $category ) : ?>
					<?php
					$category_url = get_category_link( $category->term_id );
					if ( is_wp_error( $category_url ) ) {
						continue;
					}
					$is_active = $current_category_id === (int) $category->term_id;
					?>
					<a
						class="blog-bell__chip <?php echo $is_active ? 'is-active' : ''; ?>"
						href="<?php echo esc_url( $category_url ); ?>"
						<?php echo $is_active ? 'aria-current="page"' : ''; ?>
						data-track-action="<?php echo esc_attr( 'blog_filter_' . $category->slug ); ?>"
						data-track-category="navigation"
					>
						<?php echo esc_html( $category->name ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		</nav>
	<?php endif; ?>

	<section class="blog-bell__main" data-track-section="blog_archive_grid">
		<div class="blog-bell__container">
			<?php if ( have_posts() ) : ?>
				<div class="blog-bell__grid">
					<?php
					$post_index = 0;
					while ( have_posts() ) :
						the_post();
						$post_index++;

						$post_id          = get_the_ID();
						$post_categories  = get_the_category( $post_id );
						$primary_category = ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ? $post_categories[0] : null;
						$reading_time     = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time( $post_id ) : 0;
						$excerpt          = wp_strip_all_tags( get_the_excerpt() );
						$excerpt          = $excerpt ? wp_trim_words( $excerpt, 28, '…' ) : '';
						$is_featured      = ( 1 === $post_index ) && ! is_paged() && ! $current_category_id;
						$card_classes     = 'blog-bell__card' . ( $is_featured ? ' is-featured' : '' );
						?>
						<article
							class="<?php echo esc_attr( $card_classes ); ?>"
							data-reveal
							<?php if ( $primary_category instanceof WP_Term ) : ?>
								data-category="<?php echo esc_attr( $primary_category->slug ); ?>"
							<?php endif; ?>
						>
							<a class="blog-bell__card-link" href="<?php the_permalink(); ?>" aria-labelledby="blog-bell-card-title-<?php echo esc_attr( (string) $post_id ); ?>">
								<header class="blog-bell__card-meta">
									<?php if ( $primary_category instanceof WP_Term ) : ?>
										<span class="blog-bell__card-cat"><?php echo esc_html( $primary_category->name ); ?></span>
									<?php endif; ?>
									<time class="blog-bell__card-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
										<?php echo esc_html( get_the_date( 'd. M Y' ) ); ?>
									</time>
								</header>

								<h2 id="blog-bell-card-title-<?php echo esc_attr( (string) $post_id ); ?>" class="blog-bell__card-title">
									<?php the_title(); ?>
								</h2>

								<?php if ( '' !== $excerpt ) : ?>
									<p class="blog-bell__card-excerpt"><?php echo esc_html( $excerpt ); ?></p>
								<?php endif; ?>

								<footer class="blog-bell__card-footer">
									<?php if ( $reading_time > 0 ) : ?>
										<span class="blog-bell__card-readtime"><?php echo esc_html( sprintf( '%d Min.', $reading_time ) ); ?></span>
									<?php else : ?>
										<span class="blog-bell__card-readtime">Lesen</span>
									<?php endif; ?>
									<svg class="blog-bell__card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
										<path d="M5 12h14M13 6l6 6-6 6"></path>
									</svg>
								</footer>
							</a>
						</article>
					<?php endwhile; ?>
				</div>

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
				<p class="blog-bell__empty">
					<?php esc_html_e( 'Aktuell sind keine Beiträge veröffentlicht.', 'blocksy-child' ); ?>
				</p>
			<?php endif; ?>

			<aside class="blog-bell__bottom-cta" aria-labelledby="blog-bell-bottom-cta-heading" data-track-section="blog_archive_final_cta">
				<span class="blog-bell__eyebrow">Nächster Schritt</span>
				<h2 id="blog-bell-bottom-cta-heading" class="blog-bell__bottom-cta-title">
					Nicht mehr Inhalte lesen, bevor das System geprüft ist.
				</h2>
				<p class="blog-bell__bottom-cta-text">
					Der regionale Marktcheck prüft Projektwert, Zielgebiet, Vertriebsreife und Website-Fundament. Wer nicht passt, bekommt keine Verkaufsstrecke, sondern eine klare Absage.
				</p>
				<a
					class="blog-bell__bottom-cta-link"
					href="<?php echo esc_url( $audit_url ); ?>"
					data-track-action="cta_blog_archive_marktcheck"
					data-track-category="lead_gen"
				>
					Marktcheck 48 h starten
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<path d="M5 12h14M13 6l6 6-6 6"></path>
					</svg>
				</a>
			</aside>
		</div>
	</section>
</main>

<button
	class="blog-bell__bell"
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
	class="blog-bell__modal"
	id="blog-bell-modal"
	role="dialog"
	aria-modal="true"
	aria-labelledby="blog-bell-modal-title"
	hidden
>
	<div class="blog-bell__modal-backdrop" data-blog-bell-dismiss></div>

	<div class="blog-bell__modal-panel" role="document">
		<button
			class="blog-bell__modal-close"
			type="button"
			data-blog-bell-dismiss
			aria-label="<?php esc_attr_e( 'Schließen', 'blocksy-child' ); ?>"
		>
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
				<path d="M18 6L6 18M6 6l12 12"></path>
			</svg>
		</button>

		<span class="blog-bell__eyebrow">Blog-Benachrichtigungen</span>
		<h2 id="blog-bell-modal-title" class="blog-bell__modal-title">
			<?php echo esc_html( $blog_notify_copy['headline'] ?? 'Neue Artikel per E-Mail' ); ?>
		</h2>
		<p class="blog-bell__modal-body">
			<?php echo esc_html( $blog_notify_copy['body'] ?? 'Ich schicke nur dann eine kurze Mail, wenn ein neuer Beitrag online ist. Kein Newsletter-Rauschen. Keine Sales-Mails.' ); ?>
		</p>

		<form class="blog-bell__form" data-blog-notify-form novalidate>
			<div class="blog-bell__honeypot" aria-hidden="true">
				<label for="blog-bell-website">Website</label>
				<input id="blog-bell-website" type="text" name="website" tabindex="-1" autocomplete="off">
			</div>

			<input type="hidden" name="nonce" value="<?php echo esc_attr( $blog_form_nonce ); ?>">
			<input type="hidden" name="contextPostId" value="0">

			<label class="screen-reader-text" for="blog-bell-email"><?php esc_html_e( 'E-Mail-Adresse', 'blocksy-child' ); ?></label>
			<input
				id="blog-bell-email"
				class="blog-bell__input"
				type="email"
				name="email"
				placeholder="<?php echo esc_attr( $blog_notify_copy['placeholder'] ?? 'Ihre E-Mail-Adresse' ); ?>"
				autocomplete="email"
				required
			>

			<button type="submit" class="blog-bell__submit">
				<?php echo esc_html( $blog_notify_copy['button'] ?? 'Artikel-Benachrichtigungen aktivieren' ); ?>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M5 12h14M13 6l6 6-6 6"></path>
				</svg>
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
