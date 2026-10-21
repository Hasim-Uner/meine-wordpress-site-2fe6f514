<?php
/**
 * Template Name: WordPress-Referenzen
 * Description: Kompakte Projektfälle mit eigenem Beitrag und öffentlichem Beleg.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$projects = hu_reference_case_projects();
$routes   = hu_get_commercial_route_map();
$img_url  = get_stylesheet_directory_uri() . '/assets/img/';

get_header();
?>
<div class="doku referenzen" id="references-content" data-track-page="references">
	<header class="blatt kopfteil">
		<p class="gegenstand">Referenzen · WordPress, Tracking & Conversion</p>
		<h1>Projekte. Und mein Beitrag dazu.</h1>
		<p class="aufriss">Was war die Aufgabe, was habe ich umgesetzt, was können Sie selbst prüfen? Diese Arbeiten zeigen unterschiedliche Seiten meiner WordPress-Entwicklung.</p>
		<nav class="referenzen-index" aria-label="Projektfälle auf dieser Seite">
			<?php foreach ( $projects as $project ) : ?>
				<a class="textlink" href="#<?php echo esc_attr( $project['id'] ); ?>" data-track-action="references_open_<?php echo esc_attr( $project['id'] ); ?>" data-track-category="navigation" data-track-section="references_intro"><?php echo esc_html( $project['name'] ); ?> <span aria-hidden="true">↓</span></a>
			<?php endforeach; ?>
		</nav>
	</header>

	<?php foreach ( $projects as $project ) : ?>
		<section id="<?php echo esc_attr( $project['id'] ); ?>" aria-labelledby="<?php echo esc_attr( $project['id'] ); ?>-title" data-track-section="reference_<?php echo esc_attr( $project['id'] ); ?>">
			<div class="blatt referenzen-projekt">
				<div class="referenzen-copy">
					<p class="gegenstand"><?php echo esc_html( $project['name'] ); ?> · <?php echo esc_html( $project['role'] ); ?></p>
					<h2 class="kopf" id="<?php echo esc_attr( $project['id'] ); ?>-title"><?php echo esc_html( $project['title'] ); ?></h2>
					<dl class="referenzen-befund">
						<div><dt>Ausgangslage</dt><dd><?php echo esc_html( $project['context'] ); ?></dd></div>
						<div>
							<dt>Mein Beitrag</dt>
							<dd>
								<p><?php echo esc_html( $project['text'] ); ?></p>
								<ul><?php foreach ( $project['work'] as $work ) : ?><li><?php echo esc_html( $work ); ?></li><?php endforeach; ?></ul>
							</dd>
						</div>
					</dl>
					<p class="referenzen-grenze"><?php echo esc_html( $project['limit'] ); ?></p>
					<a class="textlink" href="<?php echo esc_url( $project['url'] ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="references_visit_<?php echo esc_attr( $project['id'] ); ?>" data-track-category="trust" data-track-section="reference_<?php echo esc_attr( $project['id'] ); ?>">Website selbst ansehen <span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
				</div>
				<figure class="referenzen-bild">
					<img src="<?php echo esc_url( $img_url . $project['screenshot'] ); ?>" alt="<?php echo esc_attr( $project['alt'] ); ?>" width="960" height="600" loading="lazy" decoding="async">
					<figcaption>Ansicht der öffentlichen Website. Der eigene Projektanteil steht in der Beschreibung.</figcaption>
				</figure>
			</div>
		</section>
	<?php endforeach; ?>

	<section aria-labelledby="weitere-arbeiten-title">
		<div class="blatt">
			<p class="gegenstand">Weitere öffentliche Arbeiten</p>
			<h2 class="kopf leise" id="weitere-arbeiten-title">Weitere Einblicke.</h2>
			<ul class="referenzen-weitere">
				<?php foreach ( hu_public_reference_projects() as $project ) : ?>
					<?php if ( 'civaka-azad.org' === $project['name'] ) { continue; } ?>
					<li><strong><?php echo esc_html( $project['name'] ); ?></strong><p><?php echo esc_html( $project['text'] ); ?></p><a class="textlink" href="<?php echo esc_url( $project['url'] ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="references_visit_public" data-track-category="trust" data-track-section="references_more">Website öffnen <span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section id="zusammenarbeit" class="tafel" aria-labelledby="zusammenarbeit-title">
		<div class="blatt referenzen-abschluss">
			<div>
				<p class="gegenstand">Zusammenarbeit</p>
				<h2 class="kopf" id="zusammenarbeit-title">Was soll Ihre Website als Nächstes können?</h2>
				<p class="vorspann">Beschreiben Sie kurz die Website, die Aufgabe und Ihr Ziel. Daraus klären wir den passenden Umfang.</p>
			</div>
			<div class="referenzen-aktionen">
				<a class="tun" href="<?php echo esc_url( $routes['project_request'] ); ?>" data-track-action="references_request_project" data-track-category="lead_gen" data-track-section="references_close">Projekt beschreiben <span aria-hidden="true">→</span></a>
				<a class="textlink" href="<?php echo esc_url( $routes['whitelabel'] ); ?>" data-track-action="references_agency_collaboration" data-track-category="navigation" data-track-section="references_close">Für Agenturen: Zusammenarbeit ansehen <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</section>
</div>
<?php get_footer(); ?>
