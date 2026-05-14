<?php
/**
 * Template Name: Glossar Hub
 * Description: Strukturierter Glossar-Hub für definitorische Begriffe.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$audit_url   = nexus_get_primary_public_url( 'audit', home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ) );
$wgos_url    = nexus_get_primary_public_url( 'wgos', home_url( '/wordpress-agentur-hannover/#wgos' ) );
$summary     = function_exists( 'nexus_get_glossary_hub_summary' ) ? nexus_get_glossary_hub_summary() : [];
$hub_sections = function_exists( 'nexus_get_glossary_hub_sections' ) ? nexus_get_glossary_hub_sections() : [];
?>

<main id="main" class="site-main">
	<div class="wgos-wrapper glossary-wrapper">
		<section class="wgos-hero">
			<div class="wgos-container">
				<div class="wgos-hero-grid">
					<div class="wgos-hero-copy">
						<span class="wgos-kicker">Glossar</span>
						<h1 class="wgos-hero__title">Begriffe erklären, ohne die Primary URLs zu verdoppeln.</h1>
						<p class="wgos-hero__subtitle">Dieses Glossar sammelt definitorische Begriffe für SEO, Tracking, Performance und Conversion. Es ist bewusst als Intent-Layer unterhalb der Cluster- und Angebotsseiten gebaut: Sub-Terms hier, Head Terms auf den Primary URLs.</p>

						<ul class="wgos-hero__bullets">
							<li>Eigener Namespace für definitorische Suchintentionen</li>
							<li>Alias-Einträge leiten auf die richtige Primary URL</li>
							<li>Jeder Begriff führt in den nächsten sinnvollen Schritt</li>
						</ul>

						<div class="wgos-hero__actions">
								<a href="<?php echo esc_url( $audit_url ); ?>" class="wgos-btn wgos-btn--primary" data-track-action="cta_glossary_hub_audit" data-track-category="lead_gen">Marktcheck starten</a>
						</div>

						<p class="wgos-hero__microcopy">Glossar bedeutet hier nicht „zweites Lexikon neben dem Angebot“, sondern ein kontrollierter Begriffs-Layer mit klarer Rückführung auf die richtige Seite.</p>
					</div>

					<aside class="wgos-hero-card" aria-label="Arbeitsprinzip des Glossars">
						<span class="wgos-principle-kicker">Arbeitsprinzip</span>
						<dl class="wgos-hero-card__list">
							<div class="wgos-hero-card__row">
								<dt>Head Terms</dt>
								<dd>Bleiben auf Cluster-, Tool- oder Angebotsseiten.</dd>
							</div>
							<div class="wgos-hero-card__row">
								<dt>Sub-Terms</dt>
								<dd>Bekommen hier ihre definitorische Tiefe.</dd>
							</div>
							<div class="wgos-hero-card__row">
								<dt>Alias</dt>
								<dd>Glossar-Einträge dürfen bewusst nur auf die Primary URL verweisen.</dd>
							</div>
						</dl>
					</aside>
				</div>

				<div class="wgos-trust-strip" aria-label="Glossar Kennzahlen">
					<div class="wgos-trust-item">
						<span class="wgos-trust-value"><?php echo esc_html( (string) ( $summary['totalTerms'] ?? 0 ) ); ?></span>
						<span class="wgos-trust-label">definierte Begriffe</span>
					</div>
					<div class="wgos-trust-item">
						<span class="wgos-trust-value"><?php echo esc_html( (string) ( $summary['indexTerms'] ?? 0 ) ); ?></span>
						<span class="wgos-trust-label">indexierbare Begriffsseiten</span>
					</div>
					<div class="wgos-trust-item">
						<span class="wgos-trust-value"><?php echo esc_html( (string) ( $summary['aliasTerms'] ?? 0 ) ); ?></span>
						<span class="wgos-trust-label">Alias-Einträge</span>
					</div>
				</div>
			</div>
		</section>

		<section class="wgos-section wgos-section--white">
			<div class="wgos-container">
				<div class="wgos-section-head">
					<span class="wgos-principle-kicker">Begriffs-Layer</span>
					<h2 class="wgos-h2">Strukturiert nach denselben Kernbereichen wie das System.</h2>
					<p class="wgos-section-intro">Die Begriffe sind nicht alphabetisch zufällig gesammelt, sondern nach Strategie, Fundament, Messbarkeit, Sichtbarkeit und Conversion geordnet. So bleibt die Einordnung systemisch statt lexikalisch beliebig.</p>
				</div>

				<div class="glossary-area-stack">
					<?php foreach ( $hub_sections as $section ) : ?>
						<section class="glossary-area-card" aria-labelledby="glossary-area-<?php echo esc_attr( $section['id'] ); ?>" style="--glossary-accent: <?php echo esc_attr( (string) $section['accent'] ); ?>;">
							<header class="glossary-area-card__head">
								<div>
									<span class="glossary-area-card__eyebrow"><?php echo esc_html( (string) $section['label'] ); ?></span>
									<h3 id="glossary-area-<?php echo esc_attr( $section['id'] ); ?>"><?php echo esc_html( (string) $section['summary'] ); ?></h3>
								</div>
								<p><?php echo esc_html( (string) $section['description'] ); ?></p>
							</header>

							<div class="glossary-term-grid">
								<?php foreach ( (array) $section['items'] as $item ) : ?>
									<article class="glossary-term-card">
										<div class="glossary-term-card__top">
											<div>
												<span class="glossary-term-card__badge"><?php echo esc_html( (string) $item['policy_label'] ); ?></span>
												<h4><?php echo esc_html( (string) $item['title'] ); ?></h4>
											</div>
										</div>

										<p><?php echo esc_html( (string) $item['excerpt'] ); ?></p>

										<?php if ( ! empty( $item['is_primary'] ) ) : ?>
											<p class="glossary-term-card__hint">
												<?php echo esc_html( (string) $item['primary_reason'] ); ?>
											</p>
										<?php elseif ( ! empty( $item['primary_label'] ) ) : ?>
											<p class="glossary-term-card__hint">
												Starker Rückverweis auf: <?php echo esc_html( (string) $item['primary_label'] ); ?>
											</p>
										<?php endif; ?>

										<div class="glossary-term-card__actions">
											<a href="<?php echo esc_url( (string) $item['url'] ); ?>" class="wgos-btn wgos-btn--outline"><?php echo esc_html( (string) $item['cta_label'] ); ?></a>
										</div>
									</article>
								<?php endforeach; ?>
							</div>
						</section>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="wgos-section wgos-section--gray">
			<div class="wgos-container">
				<div class="wgos-contrast-grid">
					<article class="wgos-contrast-card">
						<h3>Was das Glossar leisten soll</h3>
						<ul class="wgos-checklist wgos-checklist--compact">
							<li>Begriffe klar definieren, die auf Angebotsseiten zu granular wären.</li>
							<li>Technische und strategische Sprache im System vereinheitlichen.</li>
							<li>Von Definitions-Intent sauber in Audit, Tool oder Cluster überleiten.</li>
						</ul>
					</article>

					<article class="wgos-contrast-card">
						<h3>Was es bewusst nicht leisten soll</h3>
						<ul class="wgos-checklist wgos-checklist--compact">
							<li>Keine zweite Rankingerklärung für Head Terms wie Core Web Vitals oder CRO.</li>
							<li>Keine unverbundenen Wörterbuchseiten ohne Primary-URL-Mapping.</li>
							<li>Keine Content-Drift weg von der eigentlichen Angebots- und Cluster-Struktur.</li>
						</ul>
					</article>
				</div>
			</div>
		</section>
	</div>
</main>

<?php get_footer(); ?>
