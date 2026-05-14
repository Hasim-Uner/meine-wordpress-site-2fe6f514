<?php
/**
 * Startseiten-Shortcodes für den fokussierten Growth-Architect-Ansatz.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format a canon value for shortcodes.
 *
 * @param mixed  $value  Canon value.
 * @param string $format Output format.
 * @return string
 */
function hu_format_canon_shortcode_value( $value, $format = 'text' ) {
	if ( is_array( $value ) ) {
		$value = implode( ', ', array_map( 'strval', $value ) );
	}

	if ( is_numeric( $value ) && 'price' === $format ) {
		return esc_html( number_format_i18n( (float) $value, 0 ) . ' €' );
	}

	return esc_html( (string) $value );
}

/**
 * Resolve a scalar canon value by key.
 *
 * @param array<string, mixed> $canon  Canon map.
 * @param string              $key    Requested key.
 * @param string              $format Output format.
 * @return string
 */
function hu_render_canon_shortcode_value( $canon, $key, $format = 'text' ) {
	$key = sanitize_key( (string) $key );

	if ( '' === $key || ! array_key_exists( $key, $canon ) ) {
		return '';
	}

	return hu_format_canon_shortcode_value( $canon[ $key ], $format );
}

/**
 * Render canonical prices and diagnosis prices.
 *
 * @param array<string, mixed> $atts Shortcode attributes.
 * @return string
 */
function hu_price_shortcode( $atts ) {
	$atts = shortcode_atts(
		[
			'key' => '',
		],
		$atts,
		'hu_price'
	);

	$canon = [];

	if ( function_exists( 'hu_pricing_canon' ) ) {
		$canon = array_merge( $canon, hu_pricing_canon() );
	}

	if ( function_exists( 'hu_diagnose_canon' ) ) {
		$canon = array_merge( $canon, hu_diagnose_canon() );
	}

	$key    = sanitize_key( (string) $atts['key'] );
	$format = preg_match( '/(price|retainer|setup|market)/', $key ) ? 'price' : 'text';

	return hu_render_canon_shortcode_value( $canon, $key, $format );
}
add_shortcode( 'hu_price', 'hu_price_shortcode' );

/**
 * Render canonical Founding Cohort values.
 *
 * @param array<string, mixed> $atts Shortcode attributes.
 * @return string
 */
function hu_founding_shortcode( $atts ) {
	$atts = shortcode_atts(
		[
			'key' => '',
		],
		$atts,
		'hu_founding'
	);

	$canon = function_exists( 'hu_founding_canon' ) ? hu_founding_canon() : [];

	return hu_render_canon_shortcode_value( $canon, $atts['key'] );
}
add_shortcode( 'hu_founding', 'hu_founding_shortcode' );

/**
 * Render canonical messaging values.
 *
 * @param array<string, mixed> $atts Shortcode attributes.
 * @return string
 */
function hu_message_shortcode( $atts ) {
	$atts = shortcode_atts(
		[
			'key' => '',
		],
		$atts,
		'hu_message'
	);

	$canon = function_exists( 'hu_messaging_canon' ) ? hu_messaging_canon() : [];

	return hu_render_canon_shortcode_value( $canon, $atts['key'] );
}
add_shortcode( 'hu_message', 'hu_message_shortcode' );

/**
 * Resolve key URLs once for homepage shortcodes.
 *
 * @return array<string, string>
 */
function hu_home_urls() {
	static $urls = null;

	if ( null !== $urls ) {
		return $urls;
	}

	$urls = [
		'audit'       => nexus_get_primary_public_url( 'audit', home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ) ),
		'wgos'        => nexus_get_primary_public_url( 'wgos', home_url( '/wordpress-agentur-hannover/#wgos' ) ),
		'cases'       => nexus_get_primary_public_url( 'results', home_url( '/ergebnisse/' ) ),
		'agentur'     => nexus_get_primary_public_url( 'agentur', home_url( '/wordpress-agentur-hannover/' ) ),
		'seo'         => nexus_get_primary_public_url( 'seo', home_url( '/wordpress-agentur-hannover/#technisches-seo' ) ),
		'wartung'     => nexus_get_primary_public_url( 'wartung', home_url( '/wordpress-wartung-hannover/' ) ),
		'tracking'    => nexus_get_primary_public_url( 'tracking', home_url( '/ga4-tracking-setup/' ) ),
		'cwv'         => nexus_get_primary_public_url( 'cwv', home_url( '/wgos-assets/cwv-optimierung/' ) ),
		'cro'         => nexus_get_primary_public_url( 'cro', home_url( '/wordpress-agentur-hannover/#wgos' ) ),
		'about'       => nexus_get_primary_public_url( 'about', home_url( '/uber-mich/' ) ),
		'blog'        => nexus_get_primary_public_url( 'blog', home_url( '/blog/' ) ),
		'e3'          => nexus_get_primary_public_url( 'e3', home_url( '/e3-new-energy/' ) ),
		'contact'     => nexus_get_primary_public_url( 'contact', home_url( '/kontakt/' ) ),
		'github_repo' => 'https://github.com/Hasim-hannover/meine-wordpress-site',
		'linkedin'    => 'https://www.linkedin.com/in/hasim-%C3%BCner/',
	];

	return $urls;
}

/**
 * Resolve public trust metrics used on the homepage.
 *
 * Numbers are intentionally versioned here so the trust layer remains stable
 * even if the live theme is deployed without a Git checkout.
 *
 * @return array<string, int>
 */
function hu_home_public_proof_data() {
	static $proof = null;

	if ( null !== $proof ) {
		return $proof;
	}

	$proof = [
		'github_commits'    => (int) apply_filters( 'hu_home_github_commit_count', 1500 ),
		'linkedin_followers'=> (int) apply_filters( 'hu_home_linkedin_followers', 600 ),
		'linkedin_posts'    => (int) apply_filters( 'hu_home_linkedin_post_count', 20 ),
	];

	return $proof;
}

/**
 * Homepage system architecture section.
 *
 * @return string
 */
function hu_home_system_section_markup() {
	$urls                  = hu_home_urls();
	$platform_capabilities = [
		'Leistungen sichtbar machen',
		'Anfragen führen',
		'Signale sammeln',
		'Prioritäten steuern',
		'kontrolliert weiterentwickeln',
	];
	$layers                = [
		[
			'label'  => 'Sichtbarkeit und Conversion',
			'title'  => 'Öffentliche Website',
			'text'   => 'Startseite, Angebotsseiten, SEO-/Money-Pages und Proof-Seiten bilden die sichtbare Oberfläche. Hier werden Leistungen verständlich, Vertrauen aufgebaut und Anfragen in klare nächste Schritte geführt.',
			'result' => 'Die sichtbare Angebots- und Vertriebsoberfläche Ihres Systems.',
			'items'  => [
				'Startseite',
				'Angebotsseiten',
				'SEO-/Money-Pages',
				'Case-/Proof-Seiten',
				'klare Nutzerführung',
				'Formulare und Conversion-Pfade',
			],
		],
		[
			'label'  => 'Signale und Messbarkeit',
			'title'  => 'Mess- und Datenebene',
			'text'   => 'Consent, Events, Conversion-Signale sowie SEO- und Performance-Daten schaffen eine belastbare Datenlage. So entstehen verwertbare Signale statt Reporting-Rauschen oder Bauchgefühl.',
			'result' => 'Belastbare Signale statt Reporting-Rauschen.',
			'items'  => [
				'Tracking',
				'Consent',
				'Events',
				'Conversion-Signale',
				'SEO-/Performance-Daten',
				'privacy-first Measurement',
			],
		],
		[
			'label'  => 'Steuerung und Prioritäten',
			'title'  => 'Kundencockpit',
			'text'   => 'Leads, Conversion-Entwicklung, SEO-/Performance-Indikatoren, Engpässe und nächste Prioritäten laufen in einer lesbaren Steueransicht zusammen. So sehen Sie nicht nur Zahlen, sondern was sie bedeuten.',
			'result' => 'Klarheit über Leads, KPIs, Engpässe und nächste Schritte.',
			'items'  => [
				'Leads und Anfragen',
				'SEO-/Performance-Indikatoren',
				'Conversion-Entwicklung',
				'Engpässe',
				'Prioritäten',
				'nächste sinnvolle Maßnahmen',
			],
		],
		[
			'label'  => 'Betrieb und Ownership',
			'title'  => 'Kontrollierte Weiterentwicklung',
			'text'   => 'Saubere Codebasis, GitHub-Versionierung, kontrollierte Deployments und ein schlanker Stack halten das System wartbar. So bleiben Daten, Abläufe und Weiterentwicklung nachvollziehbar, ausbaufähig und nicht an eine Blackbox gebunden.',
			'result' => 'Mehr Ownership und weniger unnötige Abhängigkeiten.',
			'items'  => [
				'saubere Codebasis',
				'GitHub und Versionierung',
				'kontrollierte Deployments',
				'Eigentum, Zugänge und Setups bleiben bei Ihnen',
				'keine unnötigen Plugin-Abhängigkeiten',
				'wartbare Umgebung',
			],
		],
	];
	$benefits = [
		'Klarere Daten statt Reporting-Rauschen',
		'Weniger Reibung im Anfrageprozess',
		'Mehr Ownership und weniger unnötige Abhängigkeiten',
		'Priorisierte Weiterentwicklung statt WordPress-Chaos',
	];
	$layer_count = count( $layers );

	ob_start();
	?>
	<section id="systembild" class="wp-section homepage-system-section" data-track-section="homepage_system_map" aria-labelledby="systembild-heading">
		<div class="wp-container">
			<div class="wp-section-title text-center nx-reveal">
				<span class="wp-badge">System in der Praxis</span>
				<h2 id="systembild-heading" class="wp-section-h2">Ihre WordPress-Website sollte nicht nur gut aussehen. Sie sollte steuerbar sein.</h2>
				<p class="wp-section-p homepage-system-section__intro">
					Eine professionelle WordPress-Seite ist keine isolierte Online-Präsenz. Sie ist Business-Plattform:
					sichtbar für den Markt, nützlich für Vertrieb und Marketing, verwertbar für Entscheidungen und technisch so gebaut,
					dass sie kontrolliert weiterentwickelt werden kann.
				</p>
			</div>

			<div class="homepage-system-layout">
				<div class="homepage-system-visual">
					<p class="homepage-system-visual__lead nx-reveal">Nicht nur eine Website. Sondern ein steuerbares System aus Seiten, Daten, Leads, SEO und sauberer Weiterentwicklung.</p>
					<div class="homepage-platform-strip nx-reveal" role="list" aria-label="Was die Website operativ leisten muss">
						<?php foreach ( $platform_capabilities as $capability ) : ?>
							<span class="homepage-platform-strip__item" role="listitem"><?php echo esc_html( $capability ); ?></span>
						<?php endforeach; ?>
					</div>
					<ol class="homepage-system-flow" aria-label="Architektur eines professionellen WordPress-Systems">
						<?php foreach ( $layers as $index => $layer ) : ?>
							<li class="homepage-system-card nx-reveal">
								<div class="homepage-system-card__rail" aria-hidden="true">
									<span class="homepage-system-card__number"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
									<?php if ( $index < $layer_count - 1 ) : ?>
										<span class="homepage-system-card__line"></span>
									<?php endif; ?>
								</div>

								<div class="homepage-system-card__body">
									<div class="homepage-system-card__header">
										<span class="homepage-system-card__eyebrow"><?php echo esc_html( $layer['label'] ); ?></span>
										<h3><?php echo esc_html( $layer['title'] ); ?></h3>
									</div>
									<p class="homepage-system-card__text"><?php echo esc_html( $layer['text'] ); ?></p>

									<ul class="homepage-system-card__items" aria-label="<?php echo esc_attr( $layer['title'] . ' Inhalte' ); ?>">
										<?php foreach ( $layer['items'] as $item ) : ?>
											<li><?php echo esc_html( $item ); ?></li>
										<?php endforeach; ?>
									</ul>
								</div>

								<p class="homepage-system-card__result"><?php echo esc_html( $layer['result'] ); ?></p>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>

				<aside class="homepage-system-economics nx-reveal" aria-labelledby="system-benefits-heading">
					<span class="homepage-system-economics__eyebrow">Wirtschaftlicher Nutzen</span>
					<h3 id="system-benefits-heading">Vier Ebenen, ein klarerer Hebel für Nachfrage und Entscheidungen.</h3>
					<p>Wenn Website, Datenebene, Kundencockpit und Weiterentwicklung sauber zusammenspielen, sehen Sie früher, welche Seiten tragen, wo Reibung entsteht und was als Nächstes priorisiert werden sollte.</p>

					<ul class="homepage-system-benefits" aria-label="Nutzen eines professionellen WordPress-Systems">
						<?php foreach ( $benefits as $benefit ) : ?>
							<li><?php echo esc_html( $benefit ); ?></li>
						<?php endforeach; ?>
					</ul>

					<div class="homepage-system-economics__actions">
							<a href="<?php echo esc_url( $urls['audit'] ); ?>" class="wp-btn wp-btn-primary" data-track-action="cta_system_map_audit" data-track-category="lead_gen">Marktcheck starten</a>
					</div>

					<p class="homepage-system-economics__note">
						Sie sehen nicht nur, ob etwas online ist. Sie sehen, ob die Plattform trägt, wo sie bremst
						und welcher nächste Schritt wirtschaftlich Sinn ergibt.
					</p>
				</aside>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}

/**
 * Homepage hero.
 *
 * @return string
 */
function hu_hero_section_shortcode() {
	$urls          = hu_home_urls();
	$public_proof  = function_exists( 'nexus_get_public_proof_data' ) ? nexus_get_public_proof_data() : [];
	$primary_term  = function_exists( 'nexus_get_public_primary_term' ) ? nexus_get_public_primary_term() : 'WordPress als Nachfrage-System für B2B';
	$ownership_copy = function_exists( 'nexus_get_public_ownership_sentence' ) ? nexus_get_public_ownership_sentence() : 'Code, Inhalte, Zugänge und Setups bleiben bei Ihnen. Laufende Zusammenarbeit bedeutet Weiterentwicklung, nicht Abhängigkeit.';
	$lead_count    = $public_proof['metrics']['lead_count']['value'] ?? '1.750+';
	$sales_rate    = $public_proof['metrics']['sales_conversion']['value'] ?? '12 %';
	$cpl_reduction = $public_proof['metrics']['cpl_reduction']['value'] ?? 'über 85 %';
	$lead_target   = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'lead_count', 'counter_target', '1750' ) : '1750';
	$sales_target  = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'sales_conversion', 'counter_target', '12' ) : '12';
	$cpl_target    = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_reduction', 'counter_target', '85' ) : '85';

	ob_start();
	?>
	<div class="cs-page" style="display:flex; flex-direction:column;">
		<nav class="smart-nav" aria-label="Seitennavigation">
			<ul>
				<li><a href="#hero" title="Start"><div class="nav-dot"></div><span class="nav-text">Start</span></a></li>
				<li><a href="#owned" title="Prinzip"><div class="nav-dot"></div><span class="nav-text">Prinzip</span></a></li>
				<li><a href="#systembild" title="System"><div class="nav-dot"></div><span class="nav-text">System</span></a></li>
				<li><a href="#angebot" title="Angebot"><div class="nav-dot"></div><span class="nav-text">Angebot</span></a></li>
				<li><a href="#erfolge" title="Ergebnisse"><div class="nav-dot"></div><span class="nav-text">Proof</span></a></li>
				<li><a href="#faq" title="FAQ"><div class="nav-dot"></div><span class="nav-text">FAQ</span></a></li>
			</ul>
		</nav>

		<section class="wp-hero" id="hero" role="banner">
			<div class="wp-container">
				<div class="wp-hero-grid">
					<div class="wp-hero-copy">
						<span class="wp-badge nx-reveal">Architekt für eigene Anfrage-Systeme</span>
						<h1 class="wp-hero-title nx-reveal">
							Ich mache aus Ihrer<br><span>WordPress-Website ein planbares Nachfrage-System.</span>
						</h1>
						<p class="wp-hero-subtitle nx-reveal">
							Für B2B-Unternehmen, die aus WordPress mehr brauchen als eine digitale Visitenkarte:
							klare Positionierung, technische SEO, privacy-first Messbarkeit und Conversion-Logik.
						</p>
						<p class="wp-hero-platform-note nx-reveal"><?php echo esc_html( $primary_term ); ?> statt digitaler Visitenkarte: Sichtbarkeit, Lead-Führung, Datensignale und kontrollierte Weiterentwicklung greifen als ein sauberes System zusammen.</p>
						<div class="wp-hero-proof nx-reveal" role="list" aria-label="Vertrauenssignale">
							<span class="wp-hero-proof-item" role="listitem"><?php echo esc_html( $lead_count . ' qualifizierte Anfragen im aufgebauten System' ); ?></span>
							<span class="wp-hero-proof-item" role="listitem"><?php echo esc_html( $sales_rate . ' Sales-Conversion' ); ?></span>
							<span class="wp-hero-proof-item" role="listitem"><?php echo esc_html( $cpl_reduction . ' CPL gegenüber Lead-Einkauf' ); ?></span>
						</div>

							<div class="wp-hero-actions nx-reveal">
								<a href="<?php echo esc_url( $urls['audit'] ); ?>" class="wp-btn wp-btn-primary" data-track-action="cta_hero_primary_audit" data-track-category="lead_gen">Audit starten</a>
								<a href="<?php echo esc_url( $urls['cases'] ); ?>" class="wp-btn wp-btn-secondary">Ergebnisse ansehen</a>
							</div>
							<p class="wp-hero-supporting-link nx-reveal">Erst das Vorgehen verstehen? <a href="<?php echo esc_url( $urls['about'] ); ?>">Meine Arbeitsweise ansehen</a></p>
							<p class="wp-hero-supporting-link nx-reveal">Lokaler Einstieg für Hannover: <a href="<?php echo esc_url( $urls['agentur'] ); ?>">WordPress Agentur Hannover</a></p>
							<div class="wp-hero-direct-links nx-reveal" aria-label="Direkteinstiege zu zentralen Leistungen">
								<span class="wp-hero-direct-links__label">Direkteinstiege</span>
								<a href="<?php echo esc_url( $urls['seo'] ); ?>">WordPress SEO Hannover</a>
								<a href="<?php echo esc_url( $urls['wartung'] ); ?>">WordPress Wartung Hannover</a>
								<a href="<?php echo esc_url( $urls['tracking'] ); ?>">GA4 Tracking Setup</a>
								<a href="<?php echo esc_url( $urls['cwv'] ); ?>">Core Web Vitals</a>
							</div>
						</div>

					<div class="wp-hero-panel nx-reveal">
						<div class="audit-card-premium" id="audit">
							<div class="audit-card-premium__intro">
								<span class="audit-card-premium__eyebrow">Marktcheck</span>
							</div>
							<h2 class="audit-card-premium__title">Erster Schritt: Diagnose statt Pitch</h2>
							<p class="audit-card-premium__copy">
								Wir prüfen, wo Ihre WordPress-Website Nachfrage verliert:
								bei Sichtbarkeit, Vertrauen, Datensignalen oder im nächsten Conversion-Schritt.
							</p>
							<ul class="premium-list">
								<li><span class="check-icon">✓</span> <div><strong>Sichtbarkeit:</strong> Kaufnahe SEO- und Einstiegsseiten im Realitätscheck</div></li>
								<li><span class="check-icon">✓</span> <div><strong>Messbarkeit:</strong> Tracking- und Consent-Reibung erkennen, bevor Daten teuer werden</div></li>
								<li><span class="check-icon">✓</span> <div><strong>Conversion:</strong> Proof, CTA-Führung und Angebotslogik auf echte Anfragen ausrichten</div></li>
							</ul>
							<div class="price-box text-center">
								Kein Verkaufsgespräch. Klare Einschätzung. Sinnvolle nächste Entscheidung.
							</div>
							<div class="wp-btn-wrapper audit-card-premium__actions">
									<a href="<?php echo esc_url( $urls['audit'] ); ?>" class="wp-btn wp-btn-primary full-width" data-track-action="cta_hero_audit" data-track-category="lead_gen">Marktcheck starten</a>
							</div>
						</div>
					</div>
				</div>

				<div class="vertical-metrics nx-reveal" role="group" aria-label="Erfolgsmetriken">
					<div class="wp-metric">
						<span class="wp-metric-value" data-value="<?php echo esc_attr( $lead_target ); ?>"><?php echo esc_html( $lead_count ); ?></span>
						<span class="wp-metric-label">qualifizierte Anfragen</span>
					</div>
					<div class="wp-metric">
						<span class="wp-metric-value" data-value="<?php echo esc_attr( $sales_target ); ?>"><?php echo esc_html( $sales_rate ); ?></span>
						<span class="wp-metric-label">Sales-Conversion</span>
					</div>
					<div class="wp-metric">
						<span class="wp-metric-value" data-value="<?php echo esc_attr( $cpl_target ); ?>"><?php echo esc_html( $cpl_reduction ); ?></span>
						<span class="wp-metric-label">CPL gegenüber Lead-Einkauf</span>
					</div>
					<div class="wp-metric">
						<span class="wp-metric-value" data-value="100">100%</span>
						<span class="wp-metric-label">Eigentum an Code und Setups</span>
					</div>
				</div>
			</div>
		</section>
		<?php

		return ob_get_clean();
	}
add_shortcode( 'hu_hero', 'hu_hero_section_shortcode' );

/**
 * Homepage fit section.
 *
 * @return string
 */
function hu_partner_section_shortcode() {
	$urls = hu_home_urls();

	ob_start();
	?>
	<section id="fit" class="wp-section" aria-labelledby="fit-heading">
		<div class="wp-container">
			<div class="wp-section-title text-center nx-reveal">
				<span class="wp-badge">Fokus</span>
				<h2 id="fit-heading" class="wp-section-h2">Für wen das passt</h2>
				<p class="wp-section-p">Nicht für jeden. Genau deshalb funktioniert die Seite besser für die Richtigen.</p>
			</div>

			<div class="wp-cards">
				<article class="wp-success-card nx-reveal">
					<h3 class="wp-success-title">WordPress ist Kernsystem</h3>
					<p class="wp-success-subtitle">Keine CMS-Spielwiese</p>
					<p>Ihre Website soll nicht nur gepflegt werden, sondern als Nachfrage- und Vertriebsinfrastruktur arbeiten.</p>
				</article>

				<article class="wp-success-card nx-reveal">
					<h3 class="wp-success-title">B2B mit echtem Vertriebsziel</h3>
					<p class="wp-success-subtitle">Anfragen statt Image-only</p>
					<p>Es gibt ein klares Leistungsversprechen, kaufnahe Suchintentionen und den Anspruch, aus Besuchern qualifizierte Gespräche zu machen.</p>
				</article>

				<article class="wp-success-card nx-reveal">
					<h3 class="wp-success-title">Bereitschaft für Prioritäten</h3>
					<p class="wp-success-subtitle">Kein Aktionismus</p>
					<p>Sie wollen keine zehn Einzelmaßnahmen parallel, sondern die richtige Reihenfolge aus Diagnose, Architektur und Umsetzung.</p>
				</article>
			</div>

			<div style="text-align:center; margin-top:2.5rem;">
				<a href="<?php echo esc_url( $urls['about'] ); ?>" class="wp-btn wp-btn-secondary">Meine Arbeitsweise kennenlernen</a>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}
add_shortcode( 'hu_partner', 'hu_partner_section_shortcode' );

/**
 * Homepage principle section.
 *
 * @return string
 */
function hu_owned_section_shortcode() {
	$urls  = hu_home_urls();
	$proof = hu_home_public_proof_data();

	ob_start();
	?>
	<section id="owned" class="wp-section" style="border-top:1px solid rgba(255,255,255,0.05);" aria-labelledby="owned-heading">
		<div class="container">
			<div class="section-title text-center nx-reveal">
				<span class="badge">Das Prinzip</span>
				<h2 id="owned-heading">Eigene Anfragen statt gemieteter Nachfrage.</h2>
				<p>Die bessere Website ist nicht die schönere. Sondern die, die Nachfrage halten, Signale erzeugen und im Unternehmen nutzbar machen kann.</p>
			</div>

			<div class="wp-cards" style="margin-top:2.5rem;">
				<div class="wp-success-card modell-a nx-reveal" style="border-top:3px dashed rgba(255,255,255,0.14); opacity:0.82;">
					<h3 class="wp-success-title">Modell A: Externe Nachfrage mieten</h3>
					<p class="wp-success-subtitle">Teurer, sobald Reibung im System bleibt</p>
					<ul class="premium-list">
						<li><span class="check-icon" style="color:#888;">-></span> <div>Klicks einkaufen, während die Seite intern Anfragen verliert</div></li>
						<li><span class="check-icon" style="color:#888;">-></span> <div>Steigende Medienkosten kaschieren schwache Positionierung und schlechte Daten</div></li>
						<li><span class="check-icon" style="color:#888;">-></span> <div>Budgetstop = Sichtbarkeitstop</div></li>
					</ul>
				</div>

				<div class="wp-success-card modell-b nx-reveal" style="border-top:4px solid var(--gold);">
					<h3 class="wp-success-title" style="color:var(--gold);">Modell B: WordPress als Growth-Infrastruktur</h3>
					<p class="wp-success-subtitle">Eigene Nachfrage, messbar, priorisiert</p>
					<ul class="premium-list">
						<li><span class="check-icon">✓</span> <div>Money Pages, Proof und interne Verlinkung als bleibende Assets</div></li>
						<li><span class="check-icon">✓</span> <div>Privacy-first Measurement für Entscheidungen statt Reporting-Rauschen</div></li>
						<li><span class="check-icon">✓</span> <div>Ads nur als Booster, wenn Fundament und Conversion-Pfade stehen</div></li>
					</ul>
				</div>
			</div>

			<div class="text-center" style="margin-top:2rem;">
				<a href="<?php echo esc_url( $urls['audit'] ); ?>" class="wp-btn wp-btn-secondary" data-track-action="cta_owned_audit" data-track-category="lead_gen">Prüfen, wo Ihre Seite heute Reibung erzeugt</a>
			</div>

			<div class="homepage-github-proof nx-reveal" data-track-section="homepage_github_proof" aria-label="Öffentliches GitHub-Repo als Vertrauenssignal">
				<div class="homepage-github-proof__copy">
					<span class="wp-success-subtitle">Öffentliche Transparenz</span>
					<h3 class="homepage-github-proof__title">Sehen Sie mir bei der Arbeit zu. Live und versioniert.</h3>
					<p class="homepage-github-proof__text">
						<?php
						echo esc_html(
							sprintf(
								'Jeder Code, jedes System, jede Entscheidung für meine eigene Positionierung liegt offen auf GitHub. %s Commits, vollständige Historie. Eigentum und Nachvollziehbarkeit statt Blackbox. So arbeite ich auch für Sie.',
								number_format_i18n( $proof['github_commits'] )
							)
						);
						?>
					</p>

					<div class="homepage-github-proof__meta" role="list" aria-label="GitHub-Vertrauenssignale">
						<span class="homepage-github-proof__pill" role="listitem"><?php echo esc_html( number_format_i18n( $proof['github_commits'] ) ); ?> Commits</span>
						<span class="homepage-github-proof__pill" role="listitem">vollständige Historie</span>
						<span class="homepage-github-proof__pill" role="listitem">Eigentum statt Abhängigkeit</span>
					</div>
				</div>

				<div class="homepage-github-proof__actions">
					<span class="homepage-github-proof__badge" aria-hidden="true">
						<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
							<path d="M12 1.8a10.2 10.2 0 0 0-3.23 19.88c.51.1.69-.22.69-.49 0-.24-.01-1.04-.01-1.88-2.81.61-3.4-1.19-3.4-1.19-.46-1.16-1.12-1.47-1.12-1.47-.91-.62.07-.61.07-.61 1 .07 1.53 1.04 1.53 1.04.89 1.52 2.34 1.08 2.91.83.09-.65.35-1.08.63-1.33-2.24-.25-4.59-1.12-4.59-4.97 0-1.1.39-2 1.03-2.71-.1-.26-.45-1.29.1-2.68 0 0 .84-.27 2.75 1.03a9.5 9.5 0 0 1 5 0c1.91-1.3 2.75-1.03 2.75-1.03.55 1.39.2 2.42.1 2.68.64.71 1.03 1.61 1.03 2.71 0 3.86-2.35 4.71-4.6 4.96.36.31.68.92.68 1.86 0 1.34-.01 2.42-.01 2.75 0 .27.18.6.69.49A10.2 10.2 0 0 0 12 1.8Z" fill="currentColor"/>
						</svg>
						<?php echo esc_html( number_format_i18n( $proof['github_commits'] ) ); ?> Commits
					</span>
					<a
						href="<?php echo esc_url( $urls['github_repo'] ); ?>"
						class="wp-btn wp-btn-primary homepage-github-proof__button"
						target="_blank"
						rel="noopener noreferrer"
						data-track-action="cta_github_repo"
						data-track-category="trust"
					>
						<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
							<path d="M12 1.8a10.2 10.2 0 0 0-3.23 19.88c.51.1.69-.22.69-.49 0-.24-.01-1.04-.01-1.88-2.81.61-3.4-1.19-3.4-1.19-.46-1.16-1.12-1.47-1.12-1.47-.91-.62.07-.61.07-.61 1 .07 1.53 1.04 1.53 1.04.89 1.52 2.34 1.08 2.91.83.09-.65.35-1.08.63-1.33-2.24-.25-4.59-1.12-4.59-4.97 0-1.1.39-2 1.03-2.71-.1-.26-.45-1.29.1-2.68 0 0 .84-.27 2.75 1.03a9.5 9.5 0 0 1 5 0c1.91-1.3 2.75-1.03 2.75-1.03.55 1.39.2 2.42.1 2.68.64.71 1.03 1.61 1.03 2.71 0 3.86-2.35 4.71-4.6 4.96.36.31.68.92.68 1.86 0 1.34-.01 2.42-.01 2.75 0 .27.18.6.69.49A10.2 10.2 0 0 0 12 1.8Z" fill="currentColor"/>
						</svg>
						Auf GitHub ansehen
					</a>
				</div>
			</div>
		</div>
	</section>
	<?php echo hu_home_system_section_markup(); ?>
	<?php

	return ob_get_clean();
}
add_shortcode( 'hu_owned', 'hu_owned_section_shortcode' );

/**
 * Homepage offer architecture section.
 *
 * @return string
 */
function hu_wgos_block_shortcode() {
	$urls = hu_home_urls();

	ob_start();
	?>
	<section class="wp-section homepage-wgos-section" id="angebot" aria-labelledby="angebot-heading">
		<div class="wp-container">
			<div class="wp-section-title text-center nx-reveal">
				<span class="wp-badge">Angebotsarchitektur</span>
				<h2 id="angebot-heading" class="wp-section-h2">Drei Stufen statt zehn Einzelleistungen.</h2>
				<p class="wp-section-p">
					Der Ablauf ist bewusst eng geführt: erst Diagnose, dann klare Priorisierung,
					dann kontrollierte Umsetzung und Weiterentwicklung auf WordPress-Basis.
				</p>
			</div>

			<div class="wp-process">
				<div class="wp-step nx-reveal">
					<div class="wp-step-num">1</div>
					<h3>Marktcheck</h3>
					<p>Der niedrigschwellige Einstieg. Wir machen sichtbar, wo Sichtbarkeit, Vertrauen, Datenqualität oder Lead-Capture wegbrechen und ob sich ein tieferer Eingriff lohnt.</p>
				</div>
				<div class="wp-step highlight-step nx-reveal">
					<div class="wp-step-num highlight-num">2</div>
					<h3>Priorisierung im direkten Austausch</h3>
					<p>Aus Audit, Signalen und Geschäftslogik entsteht eine belastbare Reihenfolge: welche Seiten, welche Datenlücken und welche Conversion-Bremsen zuerst angefasst werden.</p>
				</div>
				<div class="wp-step nx-reveal">
					<div class="wp-step-num">3</div>
					<h3>Kontrollierte Umsetzung und laufende Weiterentwicklung</h3>
					<p>Dann folgt die kontrollierte Umsetzung: Seiten, Datenlogik, Tracking, SEO und Weiterentwicklung in einer Reihenfolge, die Reibung senkt und bessere Anfragen erzeugt.</p>
				</div>
			</div>

			<div id="homepage-mindmap-teaser-root" class="homepage-mindmap-section" aria-label="WGOS Mindmap Teaser"></div>

			<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:1rem; margin-top:2.5rem;">
				<a href="<?php echo esc_url( $urls['audit'] ); ?>" class="wp-btn wp-btn-primary" data-track-action="cta_wgos_audit" data-track-category="lead_gen">Marktcheck starten</a>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}
add_shortcode( 'hu_wgos_block', 'hu_wgos_block_shortcode' );

/**
 * Homepage proof section.
 *
 * @return string
 */
function hu_erfolge_section_shortcode() {
	$urls             = hu_home_urls();
	$proof            = hu_home_public_proof_data();
	$canonical_metrics = function_exists( 'nexus_get_public_proof_metric_list' ) ? nexus_get_public_proof_metric_list( [ 'lead_count', 'cpl_reduction', 'sales_conversion' ] ) : [];
	$e3_timeframe     = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'timeframe', 'display', '9 Monate' ) : '9 Monate';

	ob_start();
	?>
	<section id="erfolge" aria-labelledby="cases-heading" style="padding:5rem 0; background:var(--nx-bg-glass); border-top:1px solid var(--nx-border); border-bottom:1px solid var(--nx-border);">
		<div class="nx-container">
			<div class="nx-reveal" style="text-align:center; margin-bottom:3rem;">
				<span class="nx-badge nx-badge--gold">Proof</span>
				<h2 id="cases-heading" style="font-size:clamp(1.8rem,3vw,2.4rem); margin:1rem 0 0.5rem; color:#fff;">Beispielhafte Wirkung des Systems.</h2>
				<p style="color:var(--nx-text-muted);">E3 New Energy zeigt die Logik im sichtbaren Ausschnitt. Der Hebel liegt selten in einer einzelnen Maßnahme, sondern fast immer in besserer Reihenfolge.</p>
			</div>

			<div class="homepage-proof-grid">
				<article class="nx-card nx-reveal homepage-proof-card" style="border-top:3px solid var(--nx-success);">
					<p class="nx-card__subtitle"><?php echo esc_html( sprintf( 'Beispielhafte Wirkung des Systems · B2B Leadgen · WordPress System · %s', $e3_timeframe ) ); ?></p>
					<h3 class="nx-card__title">E3 New Energy</h3>
					<p style="color:var(--nx-text-muted); margin:0.8rem 0 0;">Eingekaufte Leads, keine saubere Datenlage, hohe Reibung nach dem Klick. Nach systematischer Neuordnung von Positionierung, Tracking und Conversion-Pfad:</p>
					<div class="nx-metrics" style="margin-top:1.5rem; display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
						<?php foreach ( $canonical_metrics as $metric ) : ?>
							<div class="nx-metric">
								<span class="nx-metric__value" style="color:var(--nx-success);"><?php echo esc_html( $metric['value'] ); ?></span>
								<span class="nx-metric__label"><?php echo esc_html( $metric['label'] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
					<p class="homepage-proof-note">Ähnliche Effekte erzielt in weiteren, nicht öffentlichen Projekten – fragen Sie mich danach.</p>
					<a href="<?php echo esc_url( $urls['e3'] ); ?>" style="display:inline-block; margin-top:1.5rem; color:var(--nx-gold); font-size:0.85rem; font-weight:700; text-decoration:none;">Case Study lesen -></a>
				</article>

				<article class="nx-card nx-reveal homepage-proof-card" style="border-top:3px solid var(--nx-gold);">
					<p class="nx-card__subtitle">Öffentliche Resonanz statt erfundener Testimonials</p>
					<h3 class="nx-card__title">Bestätigt durch meine Community</h3>
					<p style="color:var(--nx-text-muted); margin:0.8rem 0 0;">Transparente Arbeit erzeugt Resonanz. Sichtbar auf LinkedIn und im öffentlichen Repo, nicht nur in internen Sales-Folien.</p>
					<div class="homepage-community-proof__metrics" role="list" aria-label="Community-Signale">
						<div class="homepage-community-proof__metric" role="listitem">
							<span class="homepage-community-proof__value"><?php echo esc_html( number_format_i18n( $proof['linkedin_followers'] ) ); ?></span>
							<span class="homepage-community-proof__label">Follower auf LinkedIn, die meinen Ansatz verfolgen</span>
						</div>
						<div class="homepage-community-proof__metric" role="listitem">
							<span class="homepage-community-proof__value"><?php echo esc_html( number_format_i18n( $proof['linkedin_posts'] ) ); ?></span>
							<span class="homepage-community-proof__label">öffentliche LinkedIn-Beiträge, an denen die Community mitliest und diskutiert</span>
						</div>
					</div>
					<div class="homepage-community-proof__actions">
						<a
							href="<?php echo esc_url( $urls['linkedin'] ); ?>"
							class="wp-btn wp-btn-secondary"
							target="_blank"
							rel="noopener noreferrer"
							data-track-action="cta_proof_linkedin"
							data-track-category="trust"
						>
							LinkedIn ansehen
						</a>
					</div>
				</article>

				<article class="nx-card nx-reveal homepage-proof-card" style="border-top:3px solid var(--nx-gold);">
					<p class="nx-card__subtitle">Typische Wirkungen nach der ersten Priorisierungsrunde</p>
					<h3 class="nx-card__title">Was sich zuerst verbessert</h3>
					<ul class="premium-list" style="margin-top:1rem;">
						<li><span class="check-icon">✓</span> <div>Money Pages werden klarer, schneller und argumentativ sauberer</div></li>
						<li><span class="check-icon">✓</span> <div>Tracking und Consent liefern endlich belastbare Signale</div></li>
						<li><span class="check-icon">✓</span> <div>SEO, Proof und CTA-Führung ziehen in dieselbe Richtung</div></li>
					</ul>
					<p style="color:var(--nx-text-muted); margin:0;">Keine Hype-Metriken. Erst die Bremsen lösen, dann skalieren.</p>
				</article>
			</div>

			<p class="homepage-proof-footnote nx-reveal">Viele Projekte laufen im Whitelabel oder ohne öffentliche Freigabe. Der sichtbare Case ist deshalb kein Zufallstreffer, sondern der offengelegte Ausschnitt einer wiederholbaren Arbeitsweise.</p>

			<div class="selection-card nx-reveal" style="margin:0 auto 2.5rem;">
				<span class="selection-card-label">Passt nicht</span>
				<p style="margin:0; color:var(--nx-text-muted);">Für reine Visitenkarten, Billig-Umsetzungen oder Teams, die zehn Taktiken parallel starten wollen. Das System ist für B2B-Unternehmen, die WordPress als operativ nützliche Business-Plattform ernst nehmen.</p>
			</div>

			<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:1rem;">
				<a href="<?php echo esc_url( $urls['audit'] ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_erfolge_audit" data-track-category="lead_gen">Audit starten</a>
				<a href="<?php echo esc_url( $urls['cases'] ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_erfolge_cases" data-track-category="lead_gen">Weitere Ergebnisse</a>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}
add_shortcode( 'hu_erfolge', 'hu_erfolge_section_shortcode' );

/**
 * Homepage working method section.
 *
 * @return string
 */
function hu_prozess_section_shortcode() {
	$urls = hu_home_urls();

	ob_start();
	?>
	<section id="prozess" aria-labelledby="process-heading">
		<div class="container">
			<div class="section-title">
				<span class="badge">Wie ich arbeite</span>
				<h2 id="process-heading">Weniger Aktionismus. Mehr Reihenfolge.</h2>
				<p>Die stärksten Hebel sitzen fast nie da, wo klassische Agentur-Scopes anfangen. Deshalb arbeite ich nach Abhängigkeiten, nicht nach Popularität.</p>
			</div>
			<div class="process">
				<article class="step"><div class="num">1</div><h3>Diagnose vor Pitch</h3><p class="muted">Erst prüfen wir, wo Sichtbarkeit, Vertrauen oder Conversion wegbrechen. Dann entscheiden wir, ob ein tieferer Eingriff wirtschaftlich Sinn ergibt.</p></article>
				<article class="step"><div class="num">2</div><h3>WordPress als Infrastruktur</h3><p class="muted">Ich denke nicht in Einzelseiten, sondern in Seitenrollen, internen Verbindungen, Measurement und dem nächsten sinnvollen Schritt für den Nutzer.</p></article>
				<article class="step"><div class="num">3</div><h3>Measurement mit Maß</h3><p class="muted">Privacy-first heißt nicht blind. Wir messen nur das, was Entscheidungen verbessert und Teams handlungsfähig macht.</p></article>
				<article class="step"><div class="num">4</div><h3>Eigentum statt Abhängigkeit</h3><p class="muted">Code, Content, Tracking-Logik und Priorisierung bleiben nachvollziehbar. Keine Blackbox, kein künstlicher Agenturbedarf.</p></article>
			</div>
			<div style="text-align:center; margin-top:2rem;">
				<a href="<?php echo esc_url( $urls['about'] ); ?>" class="btn btn-ghost">Mehr über meine Arbeitsweise</a>
			</div>
			<p style="margin:1rem auto 0; max-width:44rem; text-align:center; color:var(--muted);">
				Wenn Sie einen lokalen Einstieg suchen, finden Sie hier die passende Seite zur
				<a href="<?php echo esc_url( $urls['agentur'] ); ?>">WordPress-Agentur in Hannover</a>.
			</p>
		</div>
	</section>
	<?php

	return ob_get_clean();
}
add_shortcode( 'hu_prozess', 'hu_prozess_section_shortcode' );

/**
 * Homepage FAQ section.
 *
 * @return string
 */
function hu_faq_section_shortcode() {
	ob_start();
	?>
	<section id="faq" class="homepage-faq-section" aria-labelledby="faq-heading" style="background:var(--glass-bg); border-top:1px solid var(--glass-border); padding:5rem 0; order:95;">
		<div class="nx-container">
			<div class="nx-reveal" style="text-align:center; margin-bottom:3rem;">
				<span class="nx-badge nx-badge--gold">FAQ</span>
				<h2 id="faq-heading" style="font-size:clamp(1.8rem,3vw,2.4rem); margin:1rem 0 0.5rem; color:#fff;">Häufige Fragen</h2>
				<p style="color:var(--nx-text-muted);">Klarheit vor dem nächsten Schritt.</p>
			</div>
			<div class="nx-faq">
				<details class="nx-faq__item nx-reveal">
					<summary>Was unterscheidet Sie von einer klassischen WordPress-Agentur?</summary>
					<div class="nx-faq__content">Ich verkaufe nicht zuerst Seiten oder Leistungslisten. Ich baue WordPress als steuerbare Business-Plattform für Sichtbarkeit, Anfrageführung, Messbarkeit und kontrollierte Weiterentwicklung.</div>
				</details>
				<details class="nx-faq__item nx-reveal">
					<summary>Ist das eher SEO, Tracking oder CRO?</summary>
					<div class="nx-faq__content">Genau diese Trennung ist meist das Problem. In der Praxis greifen technische SEO, privacy-first Measurement und Conversion-Logik ineinander. Ich arbeite an der Schnittstelle.</div>
				</details>
				<details class="nx-faq__item nx-reveal">
					<summary>Brauchen wir danach noch Ads?</summary>
					<div class="nx-faq__content">Möglicherweise. Aber erst dann, wenn die Seite Nachfrage halten und sauber messen kann. Ads sind bei mir ein Verstärker, nicht das Betriebssystem.</div>
				</details>
				<details class="nx-faq__item nx-reveal">
					<summary>Wie startet die Zusammenarbeit?</summary>
					<div class="nx-faq__content">Mit dem Marktcheck. Danach gibt es eine klare Priorität und den nächsten sinnvollen Schritt. Größere Folgeprojekte ergeben sich erst nach der Rückmeldung und persönlichem Kontakt.</div>
				</details>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}
add_shortcode( 'hu_faq', 'hu_faq_section_shortcode' );

/**
 * Homepage blog section.
 *
 * @return string
 */
function hu_blog_section_shortcode() {
	$urls = hu_home_urls();

	ob_start();
	?>
	<section id="blog" class="wp-section homepage-blog-section" style="border-top:1px solid rgba(255,255,255,0.05); order:99;">
		<div class="wp-container">
			<div class="wp-section-title text-center" style="margin-bottom:4rem;">
				<span class="wp-badge">Insights</span>
				<h2 style="font-size:2.5rem; margin-bottom:1rem; color:#fff;">WordPress, Nachfrage, Infrastruktur</h2>
				<p class="wp-hero-subtitle">Für Teams, die verstehen wollen, warum Websites trotz Traffic keine belastbaren Anfragen erzeugen.</p>
			</div>

			<div class="wp-cards">
				<?php
				$blog_query = new WP_Query(
					[
						'post_type'           => 'post',
						'posts_per_page'      => 3,
						'post_status'         => 'publish',
						'ignore_sticky_posts' => true,
					]
				);

				if ( $blog_query->have_posts() ) :
					while ( $blog_query->have_posts() ) :
						$blog_query->the_post();
						$thumb_url  = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
						$categories = get_the_category();
						$cat_name   = ! empty( $categories ) ? $categories[0]->name : 'Insights';
						?>
						<article class="wp-success-card" style="position:relative; display:flex; flex-direction:column;">
							<a href="<?php the_permalink(); ?>" class="card-link-wrapper" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; height:100%;">
								<?php if ( $thumb_url ) : ?>
									<div class="card-image-wrapper" style="border-radius:12px; overflow:hidden; margin-bottom:1.5rem; border:1px solid var(--border);">
										<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" style="width:100%; height:200px; object-fit:cover; transition:transform 0.4s ease;" loading="lazy">
									</div>
								<?php endif; ?>

								<div class="card-content" style="flex-grow:1; display:flex; flex-direction:column;">
									<span class="wp-metric-label" style="display:block; margin-bottom:0.5rem; text-transform:uppercase; font-size:0.75rem; color:var(--gold); font-weight:700;">
										<?php echo esc_html( $cat_name ); ?>
									</span>
									<h3 class="wp-success-title" style="min-height:3.5rem; line-height:1.3; font-size:1.4rem; color:#fff; margin:0 0 1rem 0;"><?php the_title(); ?></h3>
									<p style="color:var(--text-dim); font-size:0.95rem; line-height:1.6; margin:0 0 1.5rem 0;">
										<?php echo esc_html( wp_trim_words( get_the_excerpt(), 16 ) ); ?>
									</p>
									<span class="text-gold" style="font-weight:700; font-size:0.9rem; margin-top:auto; display:inline-block;">Analyse lesen -></span>
								</div>
							</a>
						</article>
						<?php
					endwhile;
					wp_reset_postdata();
				else :
					echo '<p style="text-align:center; width:100%; opacity:0.6;">Aktuell werden neue Analysen vorbereitet.</p>';
				endif;
				?>
			</div>

			<div style="text-align:center; margin-top:4rem;">
				<a href="<?php echo esc_url( $urls['blog'] ); ?>" class="wp-btn wp-btn-secondary">Zum Insights-Bereich</a>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}
add_shortcode( 'hu_blog', 'hu_blog_section_shortcode' );

/**
 * Homepage final CTA.
 *
 * @return string
 */
function hu_cta_section_shortcode() {
	$urls            = hu_home_urls();
	$ownership_copy = function_exists( 'nexus_get_public_ownership_sentence' ) ? nexus_get_public_ownership_sentence() : 'Code, Inhalte, Zugänge und Setups bleiben bei Ihnen. Laufende Zusammenarbeit bedeutet Weiterentwicklung, nicht Abhängigkeit.';

	ob_start();
	?>
	<section id="cta" aria-labelledby="cta-heading" style="padding:5rem 0; order:90;">
		<div class="nx-container">
			<div class="nx-cta-box nx-reveal">
				<span class="nx-badge nx-badge--gold" style="display:inline-block; margin-bottom:1.5rem;">Nächster Schritt</span>
				<h2 id="cta-heading" style="font-size:clamp(1.8rem,3vw,2.4rem); margin-bottom:1rem; color:#fff;">Prüfen wir, wo Ihre WordPress-Seite Anfragen verliert.</h2>
					<p>Im Marktcheck sehen Sie, wo Sichtbarkeit, Vertrauen, Datensignale oder Conversion wegbrechen und ob Ihre Website heute schon als Plattform trägt.</p>

				<div role="group" aria-label="Audit-Merkmale" style="display:flex; flex-wrap:wrap; justify-content:center; gap:0.75rem 1.5rem; margin-bottom:2rem;">
					<span style="font-size:0.85rem; color:var(--nx-text-muted);">✓ klare Einschätzung statt Bauchgefühl</span>
					<span style="font-size:0.85rem; color:var(--nx-text-muted);">✓ Sichtbarkeit, Datensignale, Anfragepfade und Ownership gemeinsam betrachtet</span>
					<span style="font-size:0.85rem; color:var(--nx-text-muted);">✓ sinnvoller nächster Schritt statt Leistungsverkauf</span>
				</div>

				<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:1rem;">
					<a class="nx-btn nx-btn--primary" href="<?php echo esc_url( $urls['audit'] ); ?>" data-track-action="cta_footer_audit" data-track-category="lead_gen">Audit starten</a>
					<a class="nx-btn nx-btn--ghost" href="<?php echo esc_url( $urls['cases'] ); ?>" data-track-action="cta_footer_cases" data-track-category="lead_gen">Ergebnisse ansehen</a>
				</div>

			</div>
		</div>
	</section>

	<section id="followup" class="homepage-pilot-section" aria-labelledby="followup-heading" data-track-section="homepage_followup_offer">
		<div class="nx-container">
			<div class="homepage-pilot-card nx-reveal">
				<div class="homepage-pilot-card__copy">
					<span class="nx-badge nx-badge--gold">Fokussierte Folgeanalyse</span>
					<h2 id="followup-heading" class="homepage-pilot-card__title">Wenn nach dem Audit ein tieferer Blick sinnvoll ist</h2>
					<p class="homepage-pilot-card__lead">Die Folgeanalyse ist kein öffentlicher Erstschritt. Sie entsteht erst, wenn der Audit zeigt, dass eine vertiefte Analyse oder eine fokussierte Korrektur fachlich sinnvoll ist.</p>
					<p class="homepage-pilot-card__lead">Dann wird nicht pauschal verkauft, sondern die kritischste Seite, der wichtigste Engpass oder die nächste fachlich sinnvolle Korrektur vertieft.</p>
				</div>

				<div class="homepage-pilot-card__details">
					<div class="homepage-pilot-card__price">Nur nach Audit-Fit</div>
					<ul class="premium-list" style="margin:1.25rem 0 0;">
						<li><span class="check-icon">✓</span> <div>kritischste Seite oder stärkster Engpass vertieft</div></li>
						<li><span class="check-icon">✓</span> <div>klare Reihenfolge für den nächsten wirtschaftlich sinnvollen Schritt</div></li>
						<li><span class="check-icon">✓</span> <div>Weiterentwicklung nur dann, wenn sie fachlich wirklich passt</div></li>
					</ul>
					<a
						class="wp-btn wp-btn-primary full-width"
						href="<?php echo esc_url( $urls['audit'] ); ?>"
						data-track-action="cta_followup_audit"
						data-track-category="lead_gen"
					>
						Erst mit dem Audit starten
					</a>
					<p class="homepage-pilot-card__note"><?php echo esc_html( $ownership_copy ); ?></p>
				</div>
			</div>
		</div>
	</section>

	</div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'hu_cta', 'hu_cta_section_shortcode' );
