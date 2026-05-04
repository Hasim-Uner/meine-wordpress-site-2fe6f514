<?php
/**
 * Front Page Template
 *
 * Versioned homepage structure with a fixed premium conversion flow.
 * SEO-Meta bleibt zentral in inc/seo-meta.php.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$urls = function_exists( 'hu_home_urls' ) ? hu_home_urls() : [];

$request_url         = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/solar-waermepumpen-leadgenerierung/#energie-anfrage' );
$diagnose_anchor     = '#diagnose';
$primary_cta_label   = 'System-Diagnose starten (60 Sek.)';
$secondary_cta_label = 'Schon entschieden? Direkt anfragen.';

$e3_canon                  = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics                = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label             = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_lead_count             = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_lead_count_target      = $e3_metrics['lead_count']['counter_target'] ?? '1750';
$e3_sales_conversion       = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_sales_conversion_value = $e3_metrics['sales_conversion']['counter_target'] ?? '12';
$e3_cpl_reduction          = $e3_metrics['cpl_reduction']['display'] ?? '-85,3 %';
$e3_cpl_reduction_target   = $e3_metrics['cpl_reduction']['counter_target'] ?? '-85.3';
$e3_cpl_conservative       = $e3_metrics['cpl_reduction']['conservative_display'] ?? 'über 85 %';
$e3_timeframe              = $e3_metrics['timeframe']['display'] ?? '9 Monate';
$e3_timeframe_dative       = $e3_metrics['timeframe']['display_dative'] ?? '9 Monaten';
$e3_timeframe_value        = $e3_metrics['timeframe']['counter_target'] ?? '9';

$slots_total  = 4;
$slots_booked = 3;

$faq_items = [
	[
		'question' => 'Bauen Sie nur eine Website oder kümmern Sie sich auch um den Traffic?',
		'answer'   => 'Ich baue das komplette System. Die Website ist nur der Motor. Dazu gehören Tracking, Vorqualifizierung und die exakte Steuerung der Werbekanäle, um Sie von Portal-Leads unabhängig zu machen.',
	],
	[
		'question' => 'Arbeiten Sie auch mit bestehenden Websites?',
		'answer'   => 'Ja. In den meisten Fällen reichen gezielte Eingriffe statt eines Relaunches.',
	],
];

get_header();
?>

<main id="main" class="site-main" data-track-section="homepage">
	<div class="cs-page homepage-template">

		<!-- ============== HERO ============== -->
		<section id="hero" class="wp-hero cro-hero" data-track-section="homepage_hero">
			<div class="wp-container wp-home-shell cro-hero__shell">
				<div class="cro-hero__copy">
					<span class="wp-badge">Für Solar- und Wärmepumpen-Betriebe mit 10–25 Mitarbeitern</span>
					<h1 class="wp-hero-title cro-hero__title">Schluss mit teuren Portal-Leads, die nicht ans Telefon gehen.</h1>
					<p class="cro-hero__sub">
						<?php echo esc_html( sprintf( '%1$s weniger Kosten pro Anfrage in %2$s — Referenz %3$s. Eigenes Anfrage-System statt Portal-Abhängigkeit.', $e3_cpl_conservative, $e3_timeframe_dative, $e3_case_label ) ); ?>
					</p>

					<?php
					if ( function_exists( 'hu_render_founding_cohort_block' ) ) {
						echo hu_render_founding_cohort_block(
							[
								'variant' => 'compact',
								'id'      => 'founding-cohort-home',
							]
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>

					<div class="cro-hero__kpi-ribbon" role="list" aria-label="Kennzahlen aus der Referenz E3 New Energy">
						<div class="cro-hero__kpi" role="listitem">
							<span class="cro-hero__kpi-value"><?php echo esc_html( $e3_lead_count ); ?></span>
							<span class="cro-hero__kpi-label">qualifizierte Anfragen</span>
						</div>
						<div class="cro-hero__kpi" role="listitem">
							<span class="cro-hero__kpi-value"><?php echo esc_html( $e3_sales_conversion ); ?></span>
							<span class="cro-hero__kpi-label">Abschlussquote</span>
						</div>
						<div class="cro-hero__kpi" role="listitem">
							<span class="cro-hero__kpi-value"><?php echo esc_html( $e3_cpl_reduction ); ?></span>
							<span class="cro-hero__kpi-label">Kosten pro Anfrage</span>
						</div>
					</div>

					<div class="cro-hero__cta-stack">
						<a href="<?php echo esc_url( $diagnose_anchor ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_home_hero_diagnose" data-track-category="lead_gen"><?php echo esc_html( $primary_cta_label ); ?></a>
						<a href="<?php echo esc_url( $request_url ); ?>" class="cro-hero__cta-secondary" data-track-action="cta_home_hero_request" data-track-category="lead_gen"><?php echo esc_html( $secondary_cta_label ); ?></a>
					</div>

					<div class="cro-hero__trust" aria-label="Vertrauenssignale">
						<span class="cro-hero__trust-item"><span class="cro-hero__trust-dot" aria-hidden="true"></span><?php echo esc_html( $e3_timeframe ); ?> Track Record</span>
						<span class="cro-hero__trust-item"><span class="cro-hero__trust-dot" aria-hidden="true"></span>Privacy-first Tracking</span>
						<span class="cro-hero__trust-item"><span class="cro-hero__trust-dot" aria-hidden="true"></span>Hannover &middot; remote</span>
					</div>
				</div>
			</div>
		</section>

		<!-- ============== COST OF INACTION ============== -->
		<section id="pain" class="cro-pain" data-track-section="homepage_pain">
			<div class="wp-container wp-home-shell">
				<div class="cro-pain__head cro-reveal">
					<h2 class="wp-section-h2">Was Sie der Status quo wirklich kostet.</h2>
					<p class="cro-pain__lead">Drei stille Posten, die jeder Betrieb spürt &mdash; aber selten in Euro misst.</p>
				</div>

				<div class="cro-pain__grid">
					<article class="cro-pain__card cro-reveal">
						<span class="cro-pain__damage">~ 4.800&nbsp;&euro; / Monat</span>
						<h3 class="cro-pain__title">Portal-Leads, die nicht ans Telefon gehen</h3>
						<p class="cro-pain__text">60 Leads &times; 80 &euro; CPL &times; 50 % Erreichbarkeit &mdash; Geld, das ohne Termin verbrennt.</p>
					</article>

					<article class="cro-pain__card cro-reveal">
						<span class="cro-pain__damage">Blindflug</span>
						<h3 class="cro-pain__title">Kein Kanal-Klarbild</h3>
						<p class="cro-pain__text">Welche Kampagne bringt Termine, welche nur Klicks? Ohne Tracking entscheiden Sie nach Bauchgefühl &mdash; das skaliert nicht.</p>
					</article>

					<article class="cro-pain__card cro-reveal">
						<span class="cro-pain__damage">Seit 2024</span>
						<h3 class="cro-pain__title">Anfragen kommen nicht mehr von allein</h3>
						<p class="cro-pain__text">Der Solar-Boom ist vorbei. Wer kein eigenes System hat, ist auf Portale angewiesen &mdash; mit jedem Quartal teurer.</p>
					</article>
				</div>

				<div class="cro-pain__footer cro-reveal">
					<a href="<?php echo esc_url( $diagnose_anchor ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_home_pain_diagnose" data-track-category="lead_gen"><?php echo esc_html( $primary_cta_label ); ?></a>
				</div>
			</div>
		</section>

		<!-- ============== PROOF — BEFORE / AFTER ============== -->
		<section id="proof" class="cro-proof" data-track-section="homepage_proof">
			<div class="wp-container wp-home-shell">
				<div class="cro-proof__head cro-reveal">
					<span class="wp-badge">Proof</span>
					<h2 class="wp-section-h2">Vom Lead-Einkauf zur Lead-Autonomie.</h2>
					<div class="cro-proof__brand">E3 New Energy</div>
				</div>

				<div class="cro-proof__split">
					<article class="cro-proof__column cro-proof__column--before cro-reveal">
						<span class="cro-proof__col-tag">Vorher</span>
						<h3 class="cro-proof__col-title">Portal-Lead-Welt</h3>
						<ul class="cro-proof__col-list">
							<li>Hohe Lead-Kosten, schwankende Qualität</li>
							<li>Hälfte der Leads geht nicht ans Telefon</li>
							<li>Kein Überblick &uuml;ber konvertierende Kanäle</li>
							<li>Wachstum nur durch Budget-Erhöhung möglich</li>
						</ul>
					</article>

					<div class="cro-proof__arrow" aria-hidden="true">→</div>

					<article class="cro-proof__column cro-proof__column--after cro-reveal">
						<span class="cro-proof__col-tag">Nach <?php echo esc_html( $e3_timeframe_dative ); ?></span>
						<h3 class="cro-proof__col-title">Eigenes, skalierbares System</h3>
						<ul class="cro-proof__col-list">
							<li>Vorqualifizierte Anfragen direkt im CRM</li>
							<li>Privacy-first Tracking auf Kanal-Ebene</li>
							<li>Money Pages und Proof als bleibende Assets</li>
							<li>Skalierbar ohne CPL-Explosion</li>
						</ul>
					</article>
				</div>

				<div class="cro-proof__counters cro-reveal" role="list" aria-label="Ergebniskennzahlen">
					<div class="cro-proof__counter" role="listitem">
						<span class="cro-proof__counter-value" data-counter-target="<?php echo esc_attr( $e3_timeframe_value ); ?>" data-counter-suffix=" Monate"><?php echo esc_html( $e3_timeframe ); ?></span>
						<span class="cro-proof__counter-label">Dauer</span>
					</div>
					<div class="cro-proof__counter" role="listitem">
						<span class="cro-proof__counter-value" data-counter-target="<?php echo esc_attr( $e3_lead_count_target ); ?>" data-counter-suffix="+"><?php echo esc_html( $e3_lead_count ); ?></span>
						<span class="cro-proof__counter-label">qualifizierte Anfragen</span>
					</div>
					<div class="cro-proof__counter" role="listitem">
						<span class="cro-proof__counter-value" data-counter-target="<?php echo esc_attr( $e3_sales_conversion_value ); ?>" data-counter-suffix=" %"><?php echo esc_html( $e3_sales_conversion ); ?></span>
						<span class="cro-proof__counter-label">Abschlussquote</span>
					</div>
					<div class="cro-proof__counter" role="listitem">
						<span class="cro-proof__counter-value" data-counter-target="<?php echo esc_attr( $e3_cpl_reduction_target ); ?>" data-counter-suffix=" %" data-counter-decimals="1"><?php echo esc_html( $e3_cpl_reduction ); ?></span>
						<span class="cro-proof__counter-label">Kosten pro Anfrage</span>
					</div>
				</div>

				<div class="cro-proof__footer cro-reveal">
					<a href="<?php echo esc_url( $diagnose_anchor ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_home_proof_diagnose" data-track-category="lead_gen"><?php echo esc_html( $primary_cta_label ); ?></a>
				</div>
			</div>
		</section>

		<!-- ============== MODELL A / B ============== -->
		<section class="wp-section homepage-problem-frame" data-track-section="homepage_models">
			<div class="wp-container wp-home-shell">
				<div class="wp-section-title wp-home-section-title text-center cro-reveal">
					<span class="wp-badge">Modell</span>
					<h2 class="wp-section-h2">Zwei Wege. Ein Unterschied.</h2>
				</div>

				<div class="homepage-problem-frame__grid">
					<article class="wp-success-card homepage-problem-frame__card homepage-problem-frame__card--muted cro-models__card cro-models__card--bad cro-reveal">
						<p class="wp-success-subtitle">Modell A</p>
						<h3 class="wp-success-title">Nachfrage mieten</h3>
						<ul class="premium-list" aria-label="Modell A">
							<li><span class="check-icon homepage-problem-frame__arrow">→</span><div>Klicks werden teurer, Seite konvertiert nicht mit</div></li>
							<li><span class="check-icon homepage-problem-frame__arrow">→</span><div>Reports ohne Entscheidungssignale</div></li>
							<li><span class="check-icon homepage-problem-frame__arrow">→</span><div>Budgetstop = Nachfrage-Stopp</div></li>
						</ul>
						<div class="cro-models__cost">24 Monate &approx; 26.000 &euro; &middot; 0 &euro; Asset</div>
						<span class="cro-models__verdict cro-models__verdict--bad">Kostet ohne zu skalieren</span>
					</article>

					<article class="wp-success-card homepage-problem-frame__card homepage-problem-frame__card--accent cro-models__card cro-models__card--good cro-reveal">
						<p class="wp-success-subtitle">Modell B</p>
						<h3 class="wp-success-title">Infrastruktur aufbauen</h3>
						<ul class="premium-list" aria-label="Modell B">
							<li><span class="check-icon">✓</span><div>Money Pages und Proof werden bleibende Assets</div></li>
							<li><span class="check-icon">✓</span><div>Privacy-first Tracking schafft echte Entscheidungssignale</div></li>
							<li><span class="check-icon">✓</span><div>Ads erst skalieren, wenn das Fundament steht</div></li>
						</ul>
						<div class="cro-models__cost">24 Monate &approx; 13.200&ndash;19.200 &euro; &middot; aktiviertes Asset</div>
						<span class="cro-models__verdict cro-models__verdict--good">Asset, das bleibt</span>
					</article>
				</div>

				<div class="homepage-problem-frame__cta text-center cro-reveal">
					<a href="<?php echo esc_url( $diagnose_anchor ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_home_models_diagnose" data-track-category="lead_gen"><?php echo esc_html( $primary_cta_label ); ?></a>
				</div>
			</div>
		</section>

		<!-- ============== SYSTEM TIMELINE ============== -->
		<section id="system" class="cro-system" data-track-section="homepage_system">
			<div class="wp-container wp-home-shell">
				<div class="cro-system__head cro-reveal">
					<span class="wp-badge">System</span>
					<h2 class="wp-section-h2">Der 3-Schritt-Prozess</h2>
				</div>

				<div class="cro-timeline" role="list" aria-label="Drei aufeinander aufbauende Schritte">
					<article class="cro-timeline__step cro-reveal" role="listitem">
						<div class="cro-timeline__num" aria-hidden="true">1</div>
						<h3 class="cro-timeline__title">Fundament ordnen</h3>
						<p class="cro-timeline__text">Tracking und Datenebene aufsetzen. Quellen, Termine, CRM &mdash; sauber verknüpft.</p>
					</article>
					<article class="cro-timeline__step cro-reveal" role="listitem">
						<div class="cro-timeline__num" aria-hidden="true">2</div>
						<h3 class="cro-timeline__title">Conversion-Pfade schärfen</h3>
						<p class="cro-timeline__text">Formular, Call und Buchung optimieren &mdash; aus Klicks werden qualifizierte Termine.</p>
					</article>
					<article class="cro-timeline__step cro-reveal" role="listitem">
						<div class="cro-timeline__num" aria-hidden="true">3</div>
						<h3 class="cro-timeline__title">Skalieren</h3>
						<p class="cro-timeline__text">Money Pages, Proof und Unabhängigkeit aufbauen &mdash; Ads erst, wenn das Fundament steht.</p>
					</article>
				</div>

				<div class="cro-system__footer cro-reveal">
					<a href="<?php echo esc_url( $diagnose_anchor ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_home_system_diagnose" data-track-category="lead_gen"><?php echo esc_html( $primary_cta_label ); ?></a>
				</div>
			</div>
		</section>

		<!-- ============== INTERACTIVE DIAGNOSE (Self-Check) ============== -->
		<section id="diagnose" class="cro-diagnose" data-track-section="homepage_diagnose">
			<div class="wp-container wp-home-shell">
				<div class="cro-diagnose__head cro-reveal">
					<span class="wp-badge">System-Diagnose</span>
					<h2 class="wp-section-h2">Drei Fragen. Eine ehrliche Diagnose.</h2>
					<p class="cro-diagnose__sub">
						Klären Sie in 60 Sekunden, ob Sie ein Lead-System <strong>besitzen</strong> oder <strong>mieten</strong>.
					</p>
				</div>

				<div class="cro-diagnose__quiz cro-reveal" data-cro-diagnose>

					<div class="cro-diagnose__question">
						<span class="cro-diagnose__q-label">
							<span class="cro-diagnose__q-num">1.</span>Wem gehört der Code Ihrer Landingpage?
						</span>
						<div class="cro-diagnose__answers" role="radiogroup" aria-label="Frage 1">
							<button type="button" class="cro-diagnose__answer" data-question="q1" data-value="good">Uns &mdash; eigener Code</button>
							<button type="button" class="cro-diagnose__answer" data-question="q1" data-value="bad">Der Agentur / Plattform</button>
						</div>
					</div>

					<div class="cro-diagnose__question">
						<span class="cro-diagnose__q-label">
							<span class="cro-diagnose__q-num">2.</span>Wem gehört das CRM, in dem Ihre Leads liegen?
						</span>
						<div class="cro-diagnose__answers" role="radiogroup" aria-label="Frage 2">
							<button type="button" class="cro-diagnose__answer" data-question="q2" data-value="good">Uns &mdash; eigener Account</button>
							<button type="button" class="cro-diagnose__answer" data-question="q2" data-value="bad">Der Agentur / Portal</button>
						</div>
					</div>

					<div class="cro-diagnose__question">
						<span class="cro-diagnose__q-label">
							<span class="cro-diagnose__q-num">3.</span>Wem gehört der Tracking-Account?
						</span>
						<div class="cro-diagnose__answers" role="radiogroup" aria-label="Frage 3">
							<button type="button" class="cro-diagnose__answer" data-question="q3" data-value="good">Uns &mdash; eigener Account</button>
							<button type="button" class="cro-diagnose__answer" data-question="q3" data-value="bad">Der Agentur / Plattform</button>
						</div>
					</div>

					<div class="cro-diagnose__result" data-cro-diagnose-result aria-live="polite">
						<p class="cro-diagnose__result-title" data-cro-diagnose-result-title>Beantworten Sie alle drei Fragen.</p>
						<p data-cro-diagnose-result-text>Sie erhalten in 60 Sekunden eine ehrliche Diagnose, ob Sie ein System besitzen oder eines mieten.</p>
						<a href="<?php echo esc_url( $request_url ); ?>" class="nx-btn nx-btn--primary cro-diagnose__result-cta" data-cro-diagnose-result-cta data-track-action="cta_home_diagnose_request" data-track-category="lead_gen"><?php echo esc_html( $secondary_cta_label ); ?></a>
					</div>
				</div>
			</div>
		</section>

		<!-- ============== TRUST STRIP ============== -->
		<section class="cro-trust-strip" aria-label="Vertrauen">
			<div class="wp-container wp-home-shell">
				<div class="cro-trust-strip__inner">
					<span class="cro-trust-strip__item"><span class="cro-trust-strip__icon" aria-hidden="true">✓</span><?php echo esc_html( $e3_timeframe ); ?> Referenz mit <?php echo esc_html( $e3_case_label ); ?></span>
					<span class="cro-trust-strip__item"><span class="cro-trust-strip__icon" aria-hidden="true">✓</span>Privacy-first Tracking-Setup</span>
					<span class="cro-trust-strip__item"><span class="cro-trust-strip__icon" aria-hidden="true">✓</span>Eigene Assets &mdash; keine Mietmodelle</span>
					<span class="cro-trust-strip__item"><span class="cro-trust-strip__icon" aria-hidden="true">✓</span>Hannover &middot; remote</span>
				</div>
			</div>
		</section>

		<!-- ============== FAQ ============== -->
		<section id="faq" class="homepage-faq-section homepage-faq-section--home" aria-labelledby="faq-heading">
			<div class="nx-container wp-home-shell">
				<div class="wp-home-section-title text-center cro-reveal">
					<span class="nx-badge nx-badge--gold">FAQ</span>
					<h2 id="faq-heading" class="wp-section-h2">Häufige Fragen</h2>
				</div>
				<div class="nx-faq cro-reveal">
					<?php foreach ( $faq_items as $index => $item ) : ?>
						<details class="nx-faq__item"<?php echo 0 === $index ? ' open' : ''; ?>>
							<summary><?php echo esc_html( $item['question'] ); ?></summary>
							<div class="nx-faq__content"><?php echo esc_html( $item['answer'] ); ?></div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ============== FINAL CTA ============== -->
		<section id="cta" class="cro-final" data-track-section="homepage_cta">
			<div class="wp-container wp-home-shell">
				<div class="cro-final__box cro-reveal">
					<span class="nx-badge nx-badge--gold">Nächster Schritt</span>
					<h2 class="cro-final__title">Direkt anfragen.</h2>
					<p class="cro-final__lead">Gehen Sie direkt ins qualifizierte Formular &mdash; oder starten Sie zuerst die System-Diagnose.</p>

					<div class="cro-slot-bar" aria-label="Slot-Verfügbarkeit Q2 2026">
						<div class="cro-slot-bar__label">
							<span>Q2 2026 Slots</span>
							<span><span class="cro-slot-bar__count"><?php echo esc_html( $slots_booked ); ?></span> von <?php echo esc_html( $slots_total ); ?> vergeben</span>
						</div>
						<div class="cro-slot-bar__track" aria-hidden="true">
							<?php for ( $i = 1; $i <= $slots_total; $i++ ) : ?>
								<span class="cro-slot-bar__seg<?php echo $i <= $slots_booked ? ' is-booked' : ''; ?>"></span>
							<?php endfor; ?>
						</div>
					</div>

					<div class="cro-final__cta-row">
						<a class="nx-btn nx-btn--primary" href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_home_final_request" data-track-category="lead_gen">Anfrage stellen</a>
						<a href="<?php echo esc_url( $diagnose_anchor ); ?>" class="cro-hero__cta-secondary" data-track-action="cta_home_final_diagnose" data-track-category="lead_gen">Erst System-Diagnose machen</a>
					</div>

					<p class="cro-final__risk-reversal">
						<span>Kostenlos</span>
						<span>60 Sek.</span>
						<span>Keine Verkaufsmasche</span>
					</p>
				</div>
			</div>
		</section>

	</div><!-- .cs-page -->
</main>

<!-- ============== STICKY MOBILE CTA ============== -->
<div class="cro-sticky-cta" aria-hidden="false">
	<div class="cro-sticky-cta__inner">
		<div class="cro-sticky-cta__copy">
			Direkt anfragen?
			<small><?php echo esc_html( $slots_booked ); ?>/<?php echo esc_html( $slots_total ); ?> Slots vergeben</small>
		</div>
		<a href="<?php echo esc_url( $request_url ); ?>" class="nx-btn nx-btn--primary cro-sticky-cta__btn" data-track-action="cta_home_sticky_request" data-track-category="lead_gen">Anfragen</a>
	</div>
</div>

<?php
get_footer();
