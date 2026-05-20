<?php
/**
 * Blog archive template.
 *
 * Positions the blog as an SEO/CRO support hub for the public funnel:
 * Solar/SHK Anfrage-Systeme first, WordPress-Growth context second.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$audit_url    = function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$energy_url   = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
$e3_url       = $primary_urls['e3'] ?? home_url( '/e3-new-energy/' );
$agentur_url  = $primary_urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' );
$compare_url  = home_url( '/eigene-leadgenerierung-vs-portale/' );
$cpl_url      = home_url( '/cost-per-lead-photovoltaik/' );
$proof_stats  = function_exists( 'nexus_get_public_proof_metric_list' )
	? nexus_get_public_proof_metric_list( [ 'lead_count', 'sales_conversion', 'cpl_reduction' ] )
	: [];
$solar_term   = get_category_by_slug( 'solar-waermepumpen-anfrage-systeme' );
$solar_url    = $solar_term instanceof WP_Term ? get_category_link( $solar_term->term_id ) : $energy_url;

$hub_links = [
	[
		'eyebrow' => 'Fokus-Cluster',
		'title'   => 'Solar-/Wärmepumpen-Anfrage-Systeme',
		'text'    => 'Alle Beiträge, die Portal-Abhängigkeit, Lead-Qualität und eigene Anfrage-Infrastruktur einordnen.',
		'url'     => $solar_url,
		'label'   => 'Solar-Cluster öffnen',
	],
	[
		'eyebrow' => 'Vergleich',
		'title'   => 'Eigene Leadgenerierung vs. Portale',
		'text'    => 'Der direkte Vergleich zwischen gekauften Datensätzen und eigener Nachfrage-Infrastruktur.',
		'url'     => $compare_url,
		'label'   => 'Portalvergleich lesen',
	],
	[
		'eyebrow' => 'Proof',
		'title'   => 'E3 New Energy: Methodik-Case',
		'text'    => 'Konkreter Nachweis, wie sich CPL, Anfragevolumen und Abschlussquote im eigenen System verändert haben.',
		'url'     => $e3_url,
		'label'   => 'E3-Case ansehen',
	],
	[
		'eyebrow' => 'Lokale Money Page',
		'title'   => 'WordPress Agentur Hannover',
		'text'    => 'Der passende Einstieg für B2B-Websites, SEO, Tracking, Core Web Vitals und CRO außerhalb des Solar-Fokus.',
		'url'     => $agentur_url,
		'label'   => 'Agentur-Seite ansehen',
	],
];

get_header();
?>

<main id="main" class="site-main blog-home">

	<section class="blog-hub-hero" aria-labelledby="blog-hub-heading" data-track-section="blog_hub_hero">
		<div class="blog-hub-hero__inner">
			<div class="blog-hub-hero__copy">
				<span class="blog-hub-eyebrow">Blog & Markteinordnung</span>
				<h1 id="blog-hub-heading" class="blog-hub-hero__title">
					Wissen, das aus Traffic eigene Anfragen macht.
				</h1>
				<p class="blog-hub-hero__lead">
					Analysen zu Solar-Leadgenerierung, WordPress-SEO, Tracking, CRO und Portal-Alternativen. Der Fokus: weniger Miet-Leads, mehr eigene Anfrage-Infrastruktur.
				</p>
				<div class="blog-hub-hero__actions" aria-label="Empfohlene nächste Schritte">
					<a
						href="<?php echo esc_url( $audit_url ); ?>"
						class="blog-hub-btn blog-hub-btn--primary"
						data-track-action="cta_blog_hero_marktcheck"
						data-track-category="lead_gen"
						data-track-section="blog_hub_hero"
					>
						Solar-Marktcheck starten
					</a>
					<a
						href="<?php echo esc_url( $e3_url ); ?>"
						class="blog-hub-btn"
						data-track-action="cta_blog_hero_e3_case"
						data-track-category="trust"
						data-track-section="blog_hub_hero"
					>
						E3-Case lesen
					</a>
				</div>
				<?php if ( ! empty( $proof_stats ) ) : ?>
					<dl class="blog-hub-proof" aria-label="Beleg aus dem E3-Case">
						<?php foreach ( $proof_stats as $stat ) : ?>
							<div class="blog-hub-proof__item">
								<dt><?php echo esc_html( $stat['label'] ?? '' ); ?></dt>
								<dd><?php echo esc_html( $stat['value'] ?? '' ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>

			<aside class="blog-hub-board" aria-label="Blog-Routing">
				<div class="blog-hub-board__top">
					<span>Suchintention</span>
					<strong>Portal-Leads hinterfragen</strong>
				</div>
				<div class="blog-hub-board__flow" aria-hidden="true">
					<span>Artikel</span>
					<span>Vergleich</span>
					<span>Marktcheck</span>
				</div>
				<ul class="blog-hub-board__list">
					<li>Markteinordnungen zu Lead-Anbietern</li>
					<li>SEO-Cluster für Solar- und Wärmepumpen-Anfragen</li>
					<li>Technik-, Tracking- und CRO-Hebel mit Anschluss an Money Pages</li>
				</ul>
				<a
					href="<?php echo esc_url( $energy_url ); ?>"
					class="blog-hub-board__link"
					data-track-action="cta_blog_board_money_page"
					data-track-category="navigation"
					data-track-section="blog_hub_hero"
				>
					Anfrage-Systeme ansehen
				</a>
			</aside>
		</div>
	</section>

	<section class="blog-hub-start" aria-labelledby="blog-hub-start-heading" data-track-section="blog_hub_startpunkte">
		<div class="blog-hub-section-head">
			<span class="blog-hub-eyebrow">Startpunkte</span>
			<h2 id="blog-hub-start-heading">Schnell zum passenden Kontext.</h2>
		</div>
		<div class="blog-hub-start__grid">
			<?php foreach ( $hub_links as $index => $link ) : ?>
				<a
					class="blog-hub-start-card"
					href="<?php echo esc_url( $link['url'] ); ?>"
					data-track-action="<?php echo esc_attr( 'blog_startpunkt_' . ( $index + 1 ) ); ?>"
					data-track-category="internal_link"
					data-track-section="blog_hub_startpunkte"
				>
					<span class="blog-hub-start-card__eyebrow"><?php echo esc_html( $link['eyebrow'] ); ?></span>
					<strong class="blog-hub-start-card__title"><?php echo esc_html( $link['title'] ); ?></strong>
					<span class="blog-hub-start-card__text"><?php echo esc_html( $link['text'] ); ?></span>
					<span class="blog-hub-start-card__link"><?php echo esc_html( $link['label'] ); ?> &rarr;</span>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="blog-archive-shell" aria-labelledby="blog-archive-heading" data-track-section="blog_archive_grid">
		<div class="blog-hub-section-head blog-hub-section-head--split">
			<div>
				<span class="blog-hub-eyebrow">Alle Analysen</span>
				<h2 id="blog-archive-heading">Aktuelle Beiträge.</h2>
			</div>
			<a
				href="<?php echo esc_url( $cpl_url ); ?>"
				class="blog-hub-text-link"
				data-track-action="cta_blog_archive_cpl_context"
				data-track-category="internal_link"
				data-track-section="blog_archive_grid"
			>
				CPL-Kontext lesen &rarr;
			</a>
		</div>

		<div class="hu-blog-wrapper" data-blog-filter-root>

			<div class="blog-archive-tools">
				<label class="blog-archive-search" for="blog-archive-search">
					<span class="screen-reader-text">Beiträge durchsuchen</span>
					<input id="blog-archive-search" type="search" placeholder="Artikel, Thema oder Anbieter suchen" data-blog-search>
				</label>

				<nav class="blog-archive-filter" aria-label="Artikel nach Kategorie filtern">
					<button class="hu-filter-btn is-active" data-filter="all" aria-pressed="true">
						Alle Beiträge
					</button>
					<?php foreach ( get_categories( [ 'hide_empty' => true ] ) as $cat ) : ?>
						<button
							class="hu-filter-btn"
							data-filter="<?php echo esc_attr( $cat->slug ); ?>"
							aria-pressed="false"
						>
							<?php echo esc_html( $cat->name ); ?>
						</button>
					<?php endforeach; ?>
				</nav>
			</div>

			<div class="blog-archive-grid">

				<?php
				$post_index = 0;

				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();
						$post_index++;

						$cats        = get_the_category();
						$cat_slugs   = wp_list_pluck( $cats, 'slug' );
						$cat_names   = wp_list_pluck( $cats, 'name' );
						$thumb_url   = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
						$has_thumb   = ! empty( $thumb_url );
						$is_featured = ( 1 === $post_index );
						$search_text = wp_strip_all_tags( get_the_title() . ' ' . get_the_excerpt() . ' ' . implode( ' ', $cat_names ) );
						$search_text = function_exists( 'remove_accents' ) ? remove_accents( strtolower( $search_text ) ) : strtolower( $search_text );
				?>

				<article
					class="post-card<?php echo esc_attr( $is_featured ? ' post-card--featured' : '' ); ?><?php echo esc_attr( $has_thumb ? '' : ' post-card--no-thumb' ); ?>"
					data-categories="<?php echo esc_attr( wp_json_encode( $cat_slugs ) ); ?>"
					data-search="<?php echo esc_attr( $search_text ); ?>"
				>
					<?php if ( $has_thumb ) : ?>
						<a
							href="<?php the_permalink(); ?>"
							class="post-card__thumb-link"
							tabindex="-1"
							aria-hidden="true"
						>
							<div class="post-card__thumb">
								<img
									src="<?php echo esc_url( $thumb_url ); ?>"
									alt="<?php the_title_attribute(); ?>"
									loading="<?php echo esc_attr( $is_featured ? 'eager' : 'lazy' ); ?>"
									<?php if ( $is_featured ) : ?>fetchpriority="high"<?php endif; ?>
									width="600"
									height="338"
								>
							</div>
						</a>
					<?php endif; ?>

					<div class="post-card__body">

						<?php if ( ! empty( $cats ) ) : ?>
							<a
								href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>"
								class="post-card__cat"
							>
								<?php echo esc_html( $cats[0]->name ); ?>
							</a>
						<?php endif; ?>

						<h3 class="post-card__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>

						<p class="post-card__excerpt">
							<?php echo esc_html( wp_trim_words( get_the_excerpt(), $is_featured ? 38 : 21 ) ); ?>
						</p>

						<div class="post-card__meta">
							<time class="post-card__date" datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>">
								<?php echo esc_html( get_the_date( 'd. M Y' ) ); ?>
							</time>
							<?php if ( function_exists( 'nexus_get_reading_time' ) && nexus_get_reading_time() >= 3 ) : ?>
								<span class="post-card__reading-time">
									<?php printf( esc_html__( '%d Min. Lesezeit', 'blocksy-child' ), (int) nexus_get_reading_time() ); ?>
								</span>
							<?php endif; ?>
						</div>

						<a
							class="post-card__read"
							href="<?php the_permalink(); ?>"
							data-track-action="cta_blog_card_read"
							data-track-category="content"
							data-track-section="blog_archive_grid"
						>
							Analyse lesen &rarr;
						</a>

					</div>
				</article>

				<?php
					endwhile;
				endif;
				?>

				<div class="blog-archive-empty" data-blog-empty hidden>
					<strong>Keine passende Analyse gefunden.</strong>
					<span>Setzen Sie den Filter zurück oder suchen Sie nach Solar, SEO, Tracking, CRO oder Portal.</span>
				</div>

				<div class="blog-archive-infeed-cta" aria-label="Solar-Marktcheck">
					<div class="blog-archive-infeed-cta__inner">
						<span class="blog-archive-infeed-cta__tag">Nächster Schritt</span>
						<h2 class="blog-archive-infeed-cta__headline">Nicht nur lesen. Anfrage-System prüfen.</h2>
						<p class="blog-archive-infeed-cta__sub">
							Der Marktcheck zeigt, ob Ihr Betrieb mit Projektwert, Zielgebiet, Vertrieb und Website-Fundament für ein eigenes Anfrage-System geeignet ist.
						</p>
						<a
							href="<?php echo esc_url( $audit_url ); ?>"
							class="nexus-btn nexus-btn--primary blog-archive-infeed-cta__btn"
							data-track-action="cta_blog_archive_end_marktcheck"
							data-track-category="lead_gen"
							data-track-section="blog_archive_grid"
						>
							Marktcheck starten
						</a>
					</div>
				</div>

			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
