<?php
/**
 * Template Name: Nexus Über Mich
 * Description: Storytelling-Positionsseite für Solar- und Wärmepumpen-Anbieter
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$audit_url    = function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/kontakt/' );
$request_url  = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/anfrage/#energie-anfrage' );
$request_cta  = function_exists( 'nexus_get_primary_request_cta_label' ) ? nexus_get_primary_request_cta_label() : 'Anfrage stellen';

$not_fit_points = [
	'Reinen Design-Relaunches ohne Vertriebsziel.',
	'Betrieben, die ausschließlich auf Leadportale setzen wollen.',
	'Projekten außerhalb von Solar und Wärmepumpe.',
	'Setups, in denen Tracking gewünscht ist, aber Consent-Konsequenzen nicht akzeptiert werden.',
];

get_header();
?>

<main id="main" class="site-main">
	<div class="nexus-about" data-track-section="about_page">

		<!-- Hero -->
		<section id="about-hero" class="nx-section about-hero">
			<div class="nx-container">
				<div class="about-hero__grid">
					<div class="about-hero__copy">
						<span class="nx-badge nx-badge--gold">ÜBER MICH</span>
						<h1 class="about-hero__title">Ich bohre Brunnen. Digital.</h1>
						<p class="about-hero__lead">
							Für Solar- und Wärmepumpen-Betriebe, die ihre Anfragen nicht dauerhaft über Portale mieten wollen — sondern eine eigene Nachfrage-Infrastruktur aufbauen.
						</p>
					</div>

					<aside class="about-profile-card" aria-label="Profil">
						<figure class="about-profile-card__media">
							<img
								src="<?php echo esc_url( hu_get_profile_image_url() ); ?>"
								alt="Porträt von Haşim Üner"
								loading="eager"
								width="340"
								height="420"
							/>
						</figure>
						<div class="about-profile-card__body">
							<span class="about-profile-card__eyebrow">Haşim Üner</span>
						</div>
					</aside>
				</div>
			</div>
		</section>

		<!-- Warum jetzt. -->
		<section id="about-warum" class="nx-section about-section about-narrative">
			<div class="nx-container">
				<div class="about-narrative__inner">
					<h2 class="nx-headline-section">Warum jetzt.</h2>
					<p>Solar- und Wärmepumpen-Anbieter verkaufen ihren Kunden eine einfache Idee: weg von der Versorgung, die jemand anderem gehört. Hin zur Anlage auf dem eigenen Dach.</p>
					<p>Ich übertrage denselben Gedanken auf Ihren Vertrieb: weg von Portal-Leads, die jemand anderem gehören. Hin zu einer Anfrage-Infrastruktur auf Ihrer eigenen Website — mit Ihren Daten, Ihren Prozessen und Ihrer Kontrolle.</p>
					<p>Bis 2023 lief der Markt von selbst. Wer eine Website hatte und ein Portal-Abo, bekam Anfragen. Seit dem Boom-Ende ist diese Logik gebrochen. Anfragen werden teurer, schlechter und unvorhersehbarer. Wer wachsen will, kann nicht mehr Wasser nachkaufen. Er braucht einen Brunnen.</p>
				</div>
			</div>
		</section>

		<!-- Wie ich arbeite. -->
		<section id="about-arbeit" class="nx-section about-section about-narrative">
			<div class="nx-container">
				<div class="about-narrative__inner">
					<h2 class="nx-headline-section">Wie ich arbeite.</h2>
					<p>Wenn ein neuer Brunnen gebohrt werden soll, beginnt die Arbeit nicht mit dem Bohrer. Sie beginnt mit Geologie. Wer ohne Karte bohrt, trifft Stein oder zieht Schlamm. Diese Arbeit sieht aus wie nichts: Karten lesen, Boden prüfen, mit Nachbarn reden, die schon gebohrt haben.</p>
					<p>Genau das ist die erste Phase mit mir. Bevor irgendetwas an Ihrer Website verändert wird, lese ich Ihren Untergrund: Welche Suchanfragen kommen wirklich an? An welchen Stellen versickert Aufmerksamkeit? Wo ist das Tracking taub? Wo erklärt die Seite Technik, statt Entscheidungen zu führen?</p>
					<p>Erst danach wird gebohrt: sauber gebautes WordPress statt Page-Builder-Abhängigkeit. Serverseitiges Tracking unter Ihrer Kontrolle. Eine Pipeline, die zeigt, welcher Kontakt später wirklich wertvoll wurde.</p>
					<p>Und erst, wenn das Wasser sauber kommt, drehen wir die Pumpe hoch. Vorher Geld auf Anzeigen zu kippen, ist wie mehr Strom auf eine kaputte Pumpe geben. Es macht das Problem lauter, nicht besser.</p>
				</div>
			</div>
		</section>

		<!-- Was ich nicht bin. -->
		<section id="about-nicht" class="nx-section about-section about-narrative">
			<div class="nx-container">
				<div class="about-narrative__inner">
					<h2 class="nx-headline-section">Was ich nicht bin.</h2>
					<p>Ich bin nicht der, der Ihnen mehr Klicks verkauft. Ich bin nicht der, der jeden Monat einen neuen Funnel mietet. Ich bin nicht der, der einen Design-Relaunch verkauft, ohne zu fragen, was an der alten Seite eigentlich kaputt war.</p>
					<p>Ich passe nicht zu:</p>
					<ul class="about-not-fit__list">
						<?php foreach ( $not_fit_points as $point ) : ?>
							<li><?php echo esc_html( $point ); ?></li>
						<?php endforeach; ?>
					</ul>
					<p>Ich bohre deshalb nicht blind. Ich prüfe zuerst, ob auf Ihrem Grundstück überhaupt Wasser liegt. Manchmal ist die ehrliche Antwort: Nein, hier nicht. Dann sage ich das, und Sie sparen sich die Bohrung.</p>
				</div>
			</div>
		</section>

		<!-- Wer ich bin. -->
		<section id="about-wer" class="nx-section about-section about-narrative">
			<div class="nx-container">
				<div class="about-narrative__inner">
					<h2 class="nx-headline-section">Wer ich bin.</h2>
					<p>Mein Zugang zu dieser Arbeit ist Medienwissenschaft, nicht Webdesign. Ich denke zuerst über Sprache, Entscheidung und Signal nach — und erst danach über Code. Über Jahre habe ich an digitalen Strukturen für erklärungsbedürftige B2B-Angebote gearbeitet. Die technische Schicht war selten das eigentliche Problem. Das Problem war fast immer: jemand hat gebohrt, ohne vorher die Karte zu lesen.</p>
					<p>Seit E3 New Energy als erstem Solar-Case weiß ich, wo diese Methode am stärksten greift. Seitdem liegt mein Fokus auf Solar- und Wärmepumpen-Betrieben.</p>
				</div>
			</div>
		</section>

		<?php
		if ( function_exists( 'hu_render_founding_cohort_block' ) ) {
			echo hu_render_founding_cohort_block(
				[
					'variant' => 'about',
					'id'      => 'founding-cohort-about',
				]
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>

		<!-- Der nächste Schritt. -->
		<section id="about-close" class="nx-section about-close">
			<div class="nx-container">
				<div class="about-close__inner">
					<h2 class="nx-headline-section">Der nächste Schritt.</h2>
					<p>Wenn Sie wissen, dass Sie bohren wollen, gehen Sie direkt ins qualifizierte Formular. Fünf Fragen, etwa 90 Sekunden. Antwort innerhalb von 48 Stunden per E-Mail. Kein Verkaufsgespräch.</p>
					<p class="about-close__actions">
						<a
							href="<?php echo esc_url( $request_url ); ?>"
							class="nx-btn nx-btn--primary"
							data-track-action="cta_anfrage_uber_mich"
							data-track-category="cta"
							data-track-section="final"
						>
							<?php echo esc_html( $request_cta ); ?>
						</a>
					</p>
				</div>
			</div>
		</section>

	</div>
</main>

<?php get_footer(); ?>
