<?php
/**
 * Template Name: Whitelabel & Weiterentwicklung
 * Description: Anonymisierte Whitelabel-Arbeit, laufende Weiterentwicklung und Delivery im Hintergrund.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$whitelabel_fit_url = function_exists( 'nexus_get_whitelabel_calendar_url' ) ? nexus_get_whitelabel_calendar_url() : 'https://cal.com/hasim-uener/whitelabel-fit-gesprach?overlayCalendar=true';
$proof_anchor_url   = '#proof';
$mailto_url         = 'mailto:hallo@hasimuener.de';
$e3_cpl_before      = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before', 'display', '150 €' ) : '150 €';
$e3_cpl_after       = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after', 'display', '22 €' ) : '22 €';
$e3_lead_count      = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'lead_count', 'display', '1.750+' ) : '1.750+';

$proof_metrics = [
	[
		'label' => 'CPL vorher',
		'value' => $e3_cpl_before,
	],
	[
		'label' => 'CPL nachher',
		'value' => $e3_cpl_after,
	],
	[
		'label' => 'Generierte Leads',
		'value' => $e3_lead_count,
	],
];

$tech_stack_items = [
	'GTM Server-Side (eigener Subdomain-Container, GCP/Stape)',
	'Consent Mode V2, sauberer Cookie-Flow, TCF-kompatibel',
	'Google Ads Enhanced Conversions, Meta CAPI, TikTok Events API',
	'CRM-Attribution: Bitrix24, HubSpot, Pipedrive via Zapier/Make',
	'End-to-End Lead-Attribution von Klick bis Abschluss',
	'WordPress Performance: Core Web Vitals, Caching, kritischer Renderpfad',
];

$contract_cards = [
	[
		'title' => 'Unsichtbar',
		'copy'  => 'NDA Standard, keine Ansprache eurer Kunden, kein Branding in Reports, Kommunikation ausschließlich über euren Projektleiter.',
	],
	[
		'title' => 'Schnell',
		'copy'  => 'Onboarding in unter 14 Tagen, Reaktionszeit unter 4 Stunden (werktags), wöchentliches Delivery-Fenster.',
	],
	[
		'title' => 'Planbar',
		'copy'  => 'Monats-Retainer ab [X Stunden], feste Tagessätze für Projektarbeit, keine versteckten Zusatzkosten.',
	],
];
?>

<main id="main" class="site-main wl-proof-page" data-track-section="whitelabel_proof">
	<section class="nx-section wl-hero">
		<div class="nx-container">
			<div class="wl-hero__grid">
				<div class="wl-hero__copy">
					<span class="nx-badge nx-badge--gold">Whitelabel für Agenturen</span>
					<h1 class="wl-hero__title">Whitelabel-Tracking und Conversion für Performance-Agenturen.</h1>
					<p class="wl-hero__subtitle">Ich docke als externes System an, liefere GTM Server-Side, saubere Attribution und konvertierende Landingpages. Deine Kundenbeziehung bleibt bei dir, das technische Risiko bei mir.</p>

					<div class="wl-hero__actions">
						<a href="<?php echo esc_url( $whitelabel_fit_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_whitelabel_hero_fit_call" data-track-category="lead_gen">Whitelabel-Fit-Gespräch buchen</a>
						<a href="<?php echo esc_url( $proof_anchor_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_whitelabel_hero_case_study" data-track-category="trust">Case Study ansehen</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="nx-section wl-section-alt" id="proof">
		<div class="nx-container">
			<div class="nx-section-header">
				<span class="nx-badge nx-badge--ghost">Proof</span>
				<h2 class="nx-headline-section">Ergebnis aus einem laufenden Whitelabel-Mandat</h2>
			</div>

			<div class="results-card-grid" role="list" aria-label="Proof-Kennzahlen">
				<?php foreach ( $proof_metrics as $metric ) : ?>
					<article class="nx-card results-card results-card--gold" role="listitem">
						<p class="results-card__eyebrow"><?php echo esc_html( $metric['label'] ); ?></p>
						<h3 class="results-card__title"><?php echo esc_html( $metric['value'] ); ?></h3>
					</article>
				<?php endforeach; ?>
			</div>

			<p class="nx-subheadline">Nische: erneuerbare Energien. Hebel: GTM Server-Side, Consent Mode V2, CRM-Attribution über Bitrix24 und Zapier. Geliefert im Namen der Agentur, ohne Sichtbarkeit nach außen.</p>
		</div>
	</section>

	<section class="nx-section" id="technik-stack">
		<div class="nx-container">
			<div class="nx-section-header">
				<span class="nx-badge nx-badge--gold">Technik-Stack</span>
				<h2 class="nx-headline-section">Technik, die eure Kunden brauchen, die aber niemand liefert.</h2>
			</div>

			<div class="nx-card nx-card--flat">
				<ul class="results-bullet-list wl-hero__list">
					<?php foreach ( $tech_stack_items as $tech_stack_item ) : ?>
						<li><?php echo esc_html( $tech_stack_item ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</section>

	<section class="nx-section wl-section-alt" id="kontrakt">
		<div class="nx-container">
			<div class="nx-section-header">
				<span class="nx-badge nx-badge--ghost">Whitelabel-Kontrakt</span>
				<h2 class="nx-headline-section">Wie das Whitelabel-Setup läuft.</h2>
			</div>

			<div class="results-framework__grid">
				<?php foreach ( $contract_cards as $contract_card ) : ?>
					<article class="nx-card nx-card--flat results-framework__card">
						<h3 class="results-framework__title"><?php echo esc_html( $contract_card['title'] ); ?></h3>
						<p class="results-framework__copy"><?php echo esc_html( $contract_card['copy'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="nx-section results-cta">
		<div class="nx-container">
			<div class="results-cta__shell">
				<div>
					<span class="nx-badge nx-badge--gold">Nächster Schritt</span>
					<h2 class="results-cta__title">Passt das zu eurem Setup?</h2>
					<p class="results-cta__copy">Im Fit-Gespräch prüfen wir in 30 Minuten, ob ich technisch, operativ und vertraglich zu eurer Agentur passe. Keine Verkaufsshow, kein Pitch-Deck.</p>
				</div>
				<div class="results-cta__actions">
					<a href="<?php echo esc_url( $whitelabel_fit_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_whitelabel_footer_fit_call" data-track-category="lead_gen">Whitelabel-Fit-Gespräch buchen</a>
					<a href="<?php echo esc_url( $mailto_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_whitelabel_footer_mail" data-track-category="contact">hallo@hasimuener.de</a>
				</div>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
