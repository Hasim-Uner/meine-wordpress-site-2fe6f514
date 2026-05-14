<?php
/**
 * Template Name: 360° Deep-Dive
 * Description: Landing Page — 360° Deep-Dive Qualifizierungs-Formular
 *
 * SEO-Meta: zentral in inc/seo-meta.php (ACF-Felder: seo_title, seo_description, og_image)
 *
 * @package Blocksy_Child
 */

get_header();

$audit_url = nexus_get_audit_url();
?>

<div class="deepdive-page" data-track-section="deepdive_landing">

	<!-- 1. HERO SECTION -->
	<section class="deepdive-hero">
		<span class="deepdive-pill">Nur nach Marktcheck und Kontakt</span>
		<h1 class="deepdive-hero-title">Vertiefte Folgeanalyse nach dem Marktcheck</h1>
		<p class="deepdive-hero-sub">
			Sie kennen die groben Engpässe aus dem Marktcheck.<br>
			Mit wenigen gezielten Angaben verdichte ich das Bild zu einer persönlichen Analyse<br>
			mit klarer Reihenfolge statt losem Maßnahmenstapel.
		</p>
		<div class="deepdive-trust">
			<span>✓ Diagnose statt Pitch</span>
			<span>✓ Persönliche Analyse in 48h</span>
			<span>✓ Priorisierte Entscheidungsvorlage</span>
		</div>
	</section>

	<!-- 2. FORMULAR SECTION -->
	<section class="deepdive-form-section">
		<div class="deepdive-form-wrap">
			<?php echo do_shortcode( '[fluentform id="33"]' ); ?>
		</div>
	</section>

	<!-- 3. ERWARTUNGSMANAGEMENT -->
	<section class="deepdive-steps-section">
		<div class="deepdive-steps">
			<div class="deepdive-step">
				<span class="deepdive-step-num">1</span>
				<div class="deepdive-step-text">
					<strong>Sie beantworten wenige gezielte Fragen</strong>
					<span>(60 Sek.)</span>
				</div>
			</div>
			<div class="deepdive-step">
				<span class="deepdive-step-num">2</span>
				<div class="deepdive-step-text">
					<strong>Ich verbinde Audit-Daten und Kontext</strong>
					<span>damit Positionierung, Technik und Funnel zusammen lesbar werden</span>
				</div>
			</div>
			<div class="deepdive-step">
				<span class="deepdive-step-num">3</span>
				<div class="deepdive-step-text">
					<strong>Sie erhalten eine priorisierte Entscheidungsvorlage</strong>
					<span>mit Hebeln, Reihenfolge und nächster Entscheidung</span>
				</div>
			</div>
		</div>
	</section>

	<!-- 4. VERTRAUENS-SECTION -->
		<section class="deepdive-trust-section">
			<h2 class="deepdive-section-title">Warum dieser Folgeschritt?</h2>
			<p class="deepdive-trust-text">
			Weil die meisten Teams nicht an fehlenden Ideen scheitern, sondern an fehlender Reihenfolge.
			Die vertiefte Analyse macht sichtbar, was zuerst gelöst werden muss und ob eine Umsetzung wirtschaftlich Sinn ergibt.
			Kein Follow-up-Spam, kein Leistungsdumping, sondern eine belastbare Entscheidungsvorlage.
			</p>
		</section>

	<!-- 5. ALTERNATIVE CTA -->
	<section class="deepdive-alt-cta">
			<p class="deepdive-alt-label">Wenn der Kontext doch noch sortiert werden soll:</p>
			<a href="<?php echo esc_url( $audit_url ); ?>" class="deepdive-alt-link">
			-> Erst den Marktcheck starten
			</a>
		</section>

	<!-- 6. RÜCKLINK ZUM CJA -->
	<section class="deepdive-cja-link">
			<p class="deepdive-cja-label">Noch keinen Marktcheck gemacht?</p>
			<a href="<?php echo esc_url( $audit_url ); ?>" class="deepdive-cja-btn">
			-> Erst mit dem Audit starten
			</a>
		</section>

</div>

<?php get_footer(); ?>
