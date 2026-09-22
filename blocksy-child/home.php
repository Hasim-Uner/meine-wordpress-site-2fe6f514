<?php
/**
 * Blog home — Werkstatt im gemeinsamen Gutachten-Standard.
 *
 * Der WordPress-Loop bleibt die Quelle der Beiträge. Dieses Template ordnet
 * Einstieg, Dossiers und Chronik in denselben visuellen Mantel wie Startseite,
 * Kopf und Fuß ein, ohne die kommerziellen Routen zu vermischen.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts_page_id = (int) get_option( 'page_for_posts' );
$blog_url      = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' );
$route_map     = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];

$freelancer_url = $route_map['freelancer'] ?? home_url( '/' );
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
		'category'    => $resolve_dossier_category( [ 'wordpress-performance', 'performance-marketing', 'wordpress', 'seo-sichtbarkeit' ] ),
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

/**
 * Read the display data for the current loop post without changing routing.
 *
 * @param int $post_id Current post ID.
 * @return array<string, mixed>
 */
$get_post_display = static function ( $post_id ) {
	$post_categories  = get_the_category( $post_id );
	$primary_category = ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ? $post_categories[0] : null;
	$primary_label    = $primary_category instanceof WP_Term
		? ( function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $primary_category ) : $primary_category->name )
		: '';
	$reading_time = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time( $post_id ) : 0;
	$excerpt      = wp_strip_all_tags( get_the_excerpt() );

	return [
		'category'     => $primary_category,
		'category_name' => $primary_label,
		'reading_time' => $reading_time,
		'excerpt'      => $excerpt ? wp_trim_words( $excerpt, 30, '…' ) : '',
	];
};

$blog_form_nonce  = wp_create_nonce( 'nexus_blog_notify_subscribe' );
$blog_notify_copy = function_exists( 'nexus_get_blog_notify_copy' ) ? nexus_get_blog_notify_copy() : [];
$privacy_url      = function_exists( 'nexus_get_page_url' )
	? nexus_get_page_url( [ 'datenschutz' ], home_url( '/datenschutz/' ) )
	: home_url( '/datenschutz/' );

// Der Blog-Index verwendet das gemeinsame Designsystem plus ein schmales
// Blog-Delta. blog-archive.js bleibt ausschließlich für Modalsteuerung und
// progressive Reveals erhalten; die alte Blog-Archive-CSS wird hier nicht mehr
// geladen.
if ( function_exists( 'hu_enqueue_css' ) ) {
	hu_enqueue_css( 'nexus-blog-home-v2-css', 'blog-home-v2.css', [ 'nexus-design-system' ] );
}

if ( function_exists( 'hu_enqueue_js' ) ) {
	hu_enqueue_js( 'nexus-blog-archive-js', 'blog-archive.js', [ 'nexus-core-js' ] );
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

<div class="site-main doku blog-index blog-bell" data-track-section="blog_archive">
	<header class="blatt kopfteil blog-index__kopf" aria-labelledby="blog-archive-heading">
		<p class="gegenstand">Werkstatt / Blog</p>
		<h1 id="blog-archive-heading">Was ich messe, baue und zerlege.</h1>
		<p class="aufriss">Messprotokolle, Entscheidungsmodelle und Baupläne aus echten WordPress-, Tracking- und Anfragesystemen.</p>

		<div class="meta blog-index__meta" aria-label="Einordnung des Blogs">
			<dl>
				<div>
					<dt>Fokus</dt>
					<dd>WordPress · Tracking · Conversion</dd>
				</div>
				<div>
					<dt>Format</dt>
					<dd>Analysen & Bauprotokolle</dd>
				</div>
				<div>
					<dt>Vertikale</dt>
					<dd>Solar / Wärmepumpe / Speicher</dd>
				</div>
				<div>
					<dt>Prinzip</dt>
					<dd>Proof vor Behauptung</dd>
				</div>
			</dl>
		</div>
	</header>

	<?php if ( ! is_paged() ) : ?>
		<section id="einstieg" data-track-section="blog_start_here">
			<div class="blatt reihe">
				<div class="spalte-links">
					<div class="kapitel" aria-hidden="true">
						<span class="nr">01</span>
						<span class="titel">Einstieg</span>
						<span class="strich"></span>
					</div>
				</div>

				<div class="voll">
					<p class="mono stempelfarbe">Start hier</p>
					<h2 class="kopf">Drei Wege. Ein technisches Fundament.</h2>
					<p class="vorspann">Der Blog ist keine vierte Leistung. Er zeigt, wie die drei realen Arbeitskontexte technisch zusammenhängen.</p>

					<div class="blog-index__entry-grid">
						<?php foreach ( $entry_paths as $index => $entry ) : ?>
							<a
								class="blog-index__entry"
								href="<?php echo esc_url( (string) $entry['url'] ); ?>"
								data-track-action="<?php echo esc_attr( (string) $entry['track'] ); ?>"
								data-track-category="navigation"
								data-reveal
							>
								<span class="blog-index__entry-number"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
								<span class="blog-index__entry-kicker"><?php echo esc_html( (string) $entry['kicker'] ); ?></span>
								<strong><?php echo esc_html( (string) $entry['title'] ); ?></strong>
								<span class="blog-index__entry-copy"><?php echo esc_html( (string) $entry['copy'] ); ?></span>
								<span class="blog-index__entry-arrow" aria-hidden="true">→</span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<?php if ( ! is_paged() ) : ?>
			<?php
			the_post();
			$focus_id   = get_the_ID();
			$focus_data = $get_post_display( $focus_id );
			?>
			<section id="aktuell" data-track-section="blog_focus">
				<div class="blatt reihe">
					<div class="spalte-links">
						<div class="kapitel" aria-hidden="true">
							<span class="nr">02</span>
							<span class="titel">Aktuell</span>
							<span class="strich"></span>
						</div>
					</div>

					<div class="haupt">
						<p class="mono stempelfarbe">Aktuell im Fokus</p>
						<article class="tafel blog-index__focus" data-reveal>
							<div class="blog-index__post-meta">
								<?php if ( $focus_data['category'] instanceof WP_Term ) : ?>
									<span><?php echo esc_html( (string) $focus_data['category_name'] ); ?></span>
								<?php endif; ?>
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'd. M Y' ) ); ?></time>
								<?php if ( (int) $focus_data['reading_time'] > 0 ) : ?><span><?php echo esc_html( sprintf( '%d Min.', (int) $focus_data['reading_time'] ) ); ?></span><?php endif; ?>
							</div>
							<h2><?php the_title(); ?></h2>
							<?php if ( '' !== $focus_data['excerpt'] ) : ?><p><?php echo esc_html( (string) $focus_data['excerpt'] ); ?></p><?php endif; ?>
							<a class="textlink" href="<?php the_permalink(); ?>" data-track-action="blog_focus_open" data-track-category="content">Arbeit öffnen →</a>
						</article>
					</div>

					<aside class="marg">
						<div class="note">
							<span class="label">Leselogik</span>
							<b>Der neueste Beitrag steht nicht in einer Kartenwand.</b><br>
							Er bekommt einmal Vorrang. Danach folgt die Chronik.
						</div>
					</aside>
				</div>
			</section>

			<section id="dossiers" data-track-section="blog_dossiers">
				<div class="blatt reihe">
					<div class="spalte-links">
						<div class="kapitel" aria-hidden="true">
							<span class="nr">03</span>
							<span class="titel">Dossiers</span>
							<span class="strich"></span>
						</div>
					</div>

					<div class="voll">
						<h2 class="kopf">Vier Felder, die zusammengehören.</h2>
						<p class="vorspann">Die Themen sind getrennt, die Systeme nicht. Hier wird sichtbar, wo Technik, Messung und wirtschaftliche Wirkung ineinandergreifen.</p>

						<div class="blog-index__dossier-list">
							<?php foreach ( $dossiers as $dossier ) : ?>
								<a
									class="blog-index__dossier"
									href="<?php echo esc_url( (string) $dossier['category']['url'] ); ?>"
									data-track-action="<?php echo esc_attr( 'blog_dossier_' . sanitize_title( (string) $dossier['title'] ) ); ?>"
									data-track-category="navigation"
									data-reveal
								>
									<span class="blog-index__dossier-number"><?php echo esc_html( (string) $dossier['number'] ); ?></span>
									<span class="blog-index__dossier-body">
										<strong><?php echo esc_html( (string) $dossier['title'] ); ?></strong>
										<span><?php echo esc_html( (string) $dossier['description'] ); ?></span>
									</span>
									<span class="blog-index__dossier-link"><?php echo esc_html( (string) $dossier['category']['label'] ); ?> →</span>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<section id="chronik" data-track-section="blog_latest">
			<div class="blatt reihe">
				<div class="spalte-links">
					<div class="kapitel" aria-hidden="true">
						<span class="nr"><?php echo esc_html( is_paged() ? '01' : '04' ); ?></span>
						<span class="titel"><?php echo esc_html( is_paged() ? 'Archiv' : 'Chronik' ); ?></span>
						<span class="strich"></span>
					</div>
				</div>

				<div class="voll">
					<p class="mono stempelfarbe"><?php echo esc_html( is_paged() ? 'Weitere Arbeiten' : 'Neue Arbeiten' ); ?></p>
					<h2 class="kopf"><?php echo esc_html( is_paged() ? 'Weitere Arbeiten.' : 'Chronologisch. Ohne Algorithmus.' ); ?></h2>
					<p class="vorspann">Was zuletzt veröffentlicht oder überarbeitet wurde, steht oben.</p>

					<?php if ( ! empty( $blog_categories ) ) : ?>
						<nav class="blog-index__filter" aria-label="<?php esc_attr_e( 'Artikel nach Kategorie filtern', 'blocksy-child' ); ?>">
							<span class="blog-index__filter-label">Themen</span>
							<a class="is-active" href="<?php echo esc_url( $blog_url ); ?>" aria-current="page" data-track-action="blog_filter_all" data-track-category="navigation">Alle</a>
							<?php foreach ( $blog_categories as $category ) : ?>
								<?php
								$category_url   = get_category_link( $category->term_id );
								$category_label = function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $category ) : $category->name;
								if ( is_wp_error( $category_url ) ) {
									continue;
								}
								?>
								<a href="<?php echo esc_url( $category_url ); ?>" data-track-action="<?php echo esc_attr( 'blog_filter_' . $category->slug ); ?>" data-track-category="navigation"><?php echo esc_html( $category_label ); ?></a>
							<?php endforeach; ?>
						</nav>
					<?php endif; ?>

					<div class="blog-index__latest-list">
						<?php
						$post_index = 0;
						while ( have_posts() ) :
							the_post();
							$post_index++;
							$post_id   = get_the_ID();
							$post_data = $get_post_display( $post_id );
							?>
							<article class="blog-index__post" data-reveal>
								<a href="<?php the_permalink(); ?>" aria-labelledby="blog-index-title-<?php echo esc_attr( (string) $post_id ); ?>">
									<span class="blog-index__post-index"><?php echo esc_html( sprintf( '%02d', $post_index ) ); ?></span>
									<span class="blog-index__post-body">
										<span class="blog-index__post-meta">
											<?php if ( $post_data['category'] instanceof WP_Term ) : ?><span><?php echo esc_html( (string) $post_data['category_name'] ); ?></span><?php endif; ?>
											<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'd. M Y' ) ); ?></time>
											<?php if ( (int) $post_data['reading_time'] > 0 ) : ?><span><?php echo esc_html( sprintf( '%d Min.', (int) $post_data['reading_time'] ) ); ?></span><?php endif; ?>
										</span>
										<h3 id="blog-index-title-<?php echo esc_attr( (string) $post_id ); ?>"><?php the_title(); ?></h3>
										<?php if ( '' !== $post_data['excerpt'] ) : ?><span class="blog-index__post-excerpt"><?php echo esc_html( (string) $post_data['excerpt'] ); ?></span><?php endif; ?>
									</span>
									<span class="blog-index__post-arrow" aria-hidden="true">→</span>
								</a>
							</article>
						<?php endwhile; ?>
					</div>

					<nav class="blog-index__pagination" aria-label="<?php esc_attr_e( 'Seiten', 'blocksy-child' ); ?>">
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
				</div>
			</div>
		</section>
	<?php else : ?>
		<section>
			<div class="blatt reihe">
				<div class="haupt">
					<p><?php esc_html_e( 'Aktuell sind keine Beiträge veröffentlicht.', 'blocksy-child' ); ?></p>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="abschluss blog-index__abschluss" data-track-section="blog_archive_email">
		<div class="blatt">
			<div class="tafel">
				<div class="reihe">
					<div class="spalte-links">
						<p class="mono">Benachrichtigung</p>
					</div>
					<div class="haupt">
						<h2>Nur eine Mail, wenn etwas Neues online ist.</h2>
						<p class="aufriss">Kein Newsletter-Rauschen. Keine Sales-Serie. Nur der Hinweis auf einen neuen Beitrag.</p>
						<div class="ausgang">
							<button type="button" class="tun blog-index__notify-button" data-blog-bell-open aria-haspopup="dialog" aria-controls="blog-bell-modal" aria-expanded="false">
								E-Mail-Updates aktivieren <span class="pf" aria-hidden="true">→</span>
							</button>
						</div>
					</div>
					<aside class="marg">
						<div class="note">
							<span class="label">Versprechen</span>
							Nur neue Artikel. Keine Werbemails. Jederzeit abmelden.
						</div>
					</aside>
				</div>
			</div>
		</div>
	</section>
</div>

<button
	class="blog-index__bell"
	type="button"
	id="blog-bell-trigger"
	aria-label="<?php esc_attr_e( 'Neue Artikel per E-Mail abonnieren', 'blocksy-child' ); ?>"
	aria-haspopup="dialog"
	aria-expanded="false"
	aria-controls="blog-bell-modal"
>
	<span aria-hidden="true">↗</span>
	<span class="blog-index__bell-label">E-Mail-Updates</span>
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

	<div class="blog-bell__modal-panel doku tafel" role="document" tabindex="-1">
		<button class="blog-bell__modal-close" type="button" data-blog-bell-dismiss aria-label="<?php esc_attr_e( 'Schließen', 'blocksy-child' ); ?>">×</button>

		<p class="gegenstand">Blog-Benachrichtigungen</p>
		<h2 id="blog-bell-modal-title"><?php echo esc_html( $blog_notify_copy['headline'] ?? 'Neue Artikel per E-Mail' ); ?></h2>
		<p class="aufriss"><?php echo esc_html( $blog_notify_copy['body'] ?? 'Ich schicke nur dann eine kurze Mail, wenn ein neuer Beitrag online ist. Kein Newsletter-Rauschen. Keine Sales-Mails.' ); ?></p>

		<form class="blog-bell__form" data-blog-notify-form novalidate>
			<div class="blog-bell__honeypot" aria-hidden="true">
				<label for="blog-bell-website">Website</label>
				<input id="blog-bell-website" type="text" name="website" tabindex="-1" autocomplete="off">
			</div>
			<input type="hidden" name="nonce" value="<?php echo esc_attr( $blog_form_nonce ); ?>">
			<input type="hidden" name="contextPostId" value="0">
			<label class="mono" for="blog-bell-email"><?php esc_html_e( 'E-Mail-Adresse', 'blocksy-child' ); ?></label>
			<input id="blog-bell-email" class="blog-bell__input" type="email" name="email" placeholder="<?php echo esc_attr( $blog_notify_copy['placeholder'] ?? 'Ihre E-Mail-Adresse' ); ?>" autocomplete="email" required>
			<button type="submit" class="tun blog-bell__submit">
				<?php echo esc_html( $blog_notify_copy['button'] ?? 'Artikel-Benachrichtigungen aktivieren' ); ?> <span class="pf" aria-hidden="true">→</span>
			</button>
			<p class="blog-bell__hint">
				<?php esc_html_e( 'Double-Opt-In über E-Mail.', 'blocksy-child' ); ?>
				<a class="satzlink" href="<?php echo esc_url( $privacy_url ); ?>"><?php esc_html_e( 'Datenschutz', 'blocksy-child' ); ?></a>
			</p>
			<div class="blog-bell__feedback" data-blog-notify-feedback aria-live="polite"></div>
		</form>
	</div>
</div>

<?php get_footer(); ?>