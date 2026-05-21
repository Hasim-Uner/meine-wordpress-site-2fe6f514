<?php
/**
 * Minimal editorial blog archive template.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$audit_url     = function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$posts_page_id = (int) get_option( 'page_for_posts' );
$blog_url      = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' );
$categories    = get_categories(
	[
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	]
);

$blog_topic_clusters = [
	'portal-kritik' => [
		'title'          => 'Portal-Kritik & Markteinordnung',
		'desc'           => 'Einordnungen zu Lead-Portalen, geteilten Anfragen, CPL-Logik und der Frage, wann ein eigener Anfrageweg wirtschaftlicher wird.',
		'category_slugs' => [ 'markteinordnung', 'owned-leads', 'solar-waermepumpen-anfrage-systeme' ],
		'keywords'       => [ 'portal', 'portale', 'portal-leads', 'leads kaufen', 'photovoltaik leads', 'aroundhome', 'wattfox', 'daa', 'leadfluss' ],
	],
	'cro'           => [
		'title'          => 'CRO & Anfragepfad',
		'desc'           => 'Beiträge zu Angebotslogik, Entscheidungsführung, Formular-Reibung und dem Weg von Traffic zu qualifizierter Anfrage.',
		'category_slugs' => [ 'cro', 'strategie' ],
		'keywords'       => [ 'cro', 'conversion', 'formular', 'funnel', 'lead-funnel', 'anfragepfad', 'qualifiziert', 'qualifizierte' ],
	],
	'tracking'      => [
		'title'          => 'Tracking & Datenqualität',
		'desc'           => 'GA4, Server-Side Tracking, Consent, Attribution und CRM-Rückführung als Grundlage für bessere Budgetentscheidungen.',
		'category_slugs' => [ 'tracking', 'sichtbarkeit-daten-conversion' ],
		'keywords'       => [ 'tracking', 'ga4', 'analytics', 'server-side', 'capi', 'consent', 'attribution', 'daten' ],
	],
	'performance'   => [
		'title'          => 'Performance & WordPress-Fundament',
		'desc'           => 'Technisches SEO, Core Web Vitals, WordPress-Struktur und Ladezeit als Fundament für Sichtbarkeit und Anfragequalität.',
		'category_slugs' => [ 'wordpress-performance', 'seo', 'wordpress-growth-agentur' ],
		'keywords'       => [ 'performance', 'core web vitals', 'cwv', 'wordpress', 'seo', 'ladezeit', 'pagespeed', 'sichtbarkeit' ],
	],
];

$blog_posts = [];
global $wp_query;
if ( isset( $wp_query->posts ) && is_array( $wp_query->posts ) ) {
	$blog_posts = array_values(
		array_filter(
			$wp_query->posts,
			static function ( $post ) {
				return $post instanceof WP_Post && 'post' === $post->post_type;
			}
		)
	);
}

$blog_posts_by_cluster = array_fill_keys( array_keys( $blog_topic_clusters ), [] );
$resolve_blog_cluster  = static function ( WP_Post $post ) use ( $blog_topic_clusters ) {
	$post_categories = get_the_category( $post->ID );
	$category_slugs  = [];
	$category_names  = [];

	if ( ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ) {
		foreach ( $post_categories as $category ) {
			$category_slugs[] = (string) $category->slug;
			$category_names[] = (string) $category->name;
		}
	}

	foreach ( $blog_topic_clusters as $cluster_key => $cluster ) {
		if ( array_intersect( $category_slugs, $cluster['category_slugs'] ) ) {
			return $cluster_key;
		}
	}

	$haystack = strtolower(
		remove_accents(
			wp_strip_all_tags(
				get_the_title( $post )
				. ' '
				. get_the_excerpt( $post )
				. ' '
				. implode( ' ', $category_slugs )
				. ' '
				. implode( ' ', $category_names )
			)
		)
	);

	foreach ( $blog_topic_clusters as $cluster_key => $cluster ) {
		foreach ( $cluster['keywords'] as $keyword ) {
			if ( false !== strpos( $haystack, strtolower( remove_accents( $keyword ) ) ) ) {
				return $cluster_key;
			}
		}
	}

	return 'performance';
};

foreach ( $blog_posts as $blog_post ) {
	$cluster_key = $resolve_blog_cluster( $blog_post );
	if ( ! isset( $blog_posts_by_cluster[ $cluster_key ] ) ) {
		$cluster_key = 'performance';
	}
	$blog_posts_by_cluster[ $cluster_key ][] = $blog_post;
}

get_header();
?>

<section class="blog-home blog-editorial blog-editorial--index" aria-labelledby="blog-archive-heading">
	<div class="blog-editorial__inner">
		<header class="blog-editorial-hero" aria-labelledby="blog-archive-heading" data-track-section="blog_archive_hero">
			<span class="blog-editorial-kicker">Blog & Markteinordnung</span>
			<h1 id="blog-archive-heading" class="blog-editorial-hero__title">
				Analysen für eigene Anfrage-Systeme.
			</h1>
			<p class="blog-editorial-hero__lead">
				Keine Magazinoptik. Keine Stockfoto-Sammlung. Hier stehen die Themen, die bei Solar-, SHK- und B2B-Websites über Sichtbarkeit, Lead-Qualität und Abschlusskosten entscheiden.
			</p>
		</header>

		<nav class="blog-editorial-filter" aria-label="<?php esc_attr_e( 'Artikel nach Kategorie filtern', 'blocksy-child' ); ?>" data-track-section="blog_archive_filter">
			<a
				class="blog-editorial-filter__link is-active"
				href="<?php echo esc_url( $blog_url ); ?>"
				aria-current="page"
				data-track-action="blog_filter_all"
				data-track-category="navigation"
				data-track-section="blog_archive_filter"
			>
				Alle
			</a>
			<?php foreach ( $categories as $category ) : ?>
				<?php $category_url = get_category_link( $category->term_id ); ?>
				<?php if ( is_wp_error( $category_url ) ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<a
					class="blog-editorial-filter__link"
					href="<?php echo esc_url( $category_url ); ?>"
					data-track-action="<?php echo esc_attr( 'blog_filter_' . $category->slug ); ?>"
					data-track-category="navigation"
					data-track-section="blog_archive_filter"
				>
					<?php echo esc_html( $category->name ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<section class="blog-editorial-clusters" aria-label="<?php esc_attr_e( 'Beiträge nach strategischen Themenclustern', 'blocksy-child' ); ?>" data-track-section="blog_archive_clusters">
			<?php if ( ! empty( $blog_posts ) ) : ?>
				<?php $rendered_posts = 0; ?>
				<?php foreach ( $blog_topic_clusters as $cluster_key => $cluster ) : ?>
					<?php if ( empty( $blog_posts_by_cluster[ $cluster_key ] ) ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<section class="blog-topic-cluster" aria-labelledby="blog-cluster-<?php echo esc_attr( $cluster_key ); ?>" data-track-section="<?php echo esc_attr( 'blog_cluster_' . $cluster_key ); ?>">
						<header class="blog-topic-cluster__head">
							<h2 id="blog-cluster-<?php echo esc_attr( $cluster_key ); ?>" class="blog-topic-cluster__title">
								<?php echo esc_html( $cluster['title'] ); ?>
							</h2>
							<p class="blog-topic-cluster__desc"><?php echo esc_html( $cluster['desc'] ); ?></p>
						</header>

						<div class="blog-editorial-list">
							<?php foreach ( $blog_posts_by_cluster[ $cluster_key ] as $cluster_post ) : ?>
								<?php
								++$rendered_posts;

								$cluster_post_id  = (int) $cluster_post->ID;
								$post_categories  = get_the_category( $cluster_post_id );
								$primary_category = ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ? $post_categories[0] : null;
								$reading_time     = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time( $cluster_post_id ) : 0;
								?>
								<article class="blog-editorial-item">
									<div class="blog-editorial-item__meta">
										<?php if ( $primary_category instanceof WP_Term ) : ?>
											<a class="blog-editorial-topic" href="<?php echo esc_url( get_category_link( $primary_category->term_id ) ); ?>">
												<?php echo esc_html( $primary_category->name ); ?>
											</a>
										<?php endif; ?>
										<time datetime="<?php echo esc_attr( get_the_date( 'c', $cluster_post_id ) ); ?>">
											<?php echo esc_html( get_the_date( 'd. M Y', $cluster_post_id ) ); ?>
										</time>
										<?php if ( $reading_time > 0 ) : ?>
											<span><?php echo esc_html( sprintf( '%d Min. Lesezeit', $reading_time ) ); ?></span>
										<?php endif; ?>
									</div>

									<h3 class="blog-editorial-item__title">
										<a href="<?php echo esc_url( get_permalink( $cluster_post_id ) ); ?>">
											<?php echo esc_html( get_the_title( $cluster_post_id ) ); ?>
										</a>
									</h3>

									<p class="blog-editorial-item__excerpt">
										<?php echo esc_html( wp_trim_words( get_the_excerpt( $cluster_post_id ), 28, '...' ) ); ?>
									</p>
								</article>

								<?php if ( 3 === $rendered_posts ) : ?>
									<aside class="blog-editorial-inline-cta" aria-labelledby="blog-inline-cta-heading" data-track-section="blog_archive_inline_cta">
										<span class="blog-editorial-kicker">60-Sekunden-Marktcheck</span>
										<h3 id="blog-inline-cta-heading" class="blog-editorial-inline-cta__title">
											Genug gelesen. Prüfen, ob der Markt überhaupt trägt.
										</h3>
										<p class="blog-editorial-inline-cta__text">
											Projektwert, Zielgebiet, Vertriebsreife und Website-Fundament werden händisch eingeordnet. Ergebnis: klare nächste Priorität statt weiterer Artikel-Warteschleife.
										</p>
										<a
											class="blog-editorial-inline-cta__link"
											href="<?php echo esc_url( $audit_url ); ?>"
											data-track-action="cta_blog_archive_inline_marktcheck"
											data-track-category="lead_gen"
											data-track-section="blog_archive_inline_cta"
										>
											Marktcheck starten
										</a>
									</aside>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endforeach; ?>

				<div class="blog-editorial-pagination">
					<?php
					the_posts_pagination(
						[
							'mid_size'  => 1,
							'prev_text' => __( 'Zurück', 'blocksy-child' ),
							'next_text' => __( 'Weiter', 'blocksy-child' ),
						]
					);
					?>
				</div>
			<?php else : ?>
				<p class="blog-editorial-empty">
					<?php esc_html_e( 'Aktuell sind keine Beiträge veröffentlicht.', 'blocksy-child' ); ?>
				</p>
			<?php endif; ?>
		</section>

		<section class="blog-editorial-cta" aria-labelledby="blog-archive-cta-heading" data-track-section="blog_archive_final_cta">
			<span class="blog-editorial-kicker">Nächster Schritt</span>
			<h2 id="blog-archive-cta-heading" class="blog-editorial-cta__title">
				Nicht mehr Inhalte lesen, bevor das System geprüft ist.
			</h2>
			<p class="blog-editorial-cta__text">
				Der regionale Marktcheck prüft Projektwert, Zielgebiet, Vertriebsreife und Website-Fundament. Wer nicht passt, bekommt keine Verkaufsstrecke, sondern eine klare Absage.
			</p>
			<a
				class="blog-editorial-cta__link"
				href="<?php echo esc_url( $audit_url ); ?>"
				data-track-action="cta_blog_archive_marktcheck"
				data-track-category="lead_gen"
				data-track-section="blog_archive_final_cta"
			>
				Regionalen Marktcheck starten
			</a>
		</section>
	</div>
</section>

<?php get_footer(); ?>
