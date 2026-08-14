<?php
/**
 * Template Name: Stack Solar
 * Description: Technischer Unterbau eigener Anfragesysteme für Solar- und
 *              Wärmepumpen-Anbieter. Frontend, Hosting, Server-Side Tracking,
 *              CRM und projektspezifische Vorqualifizierung in fünf Schichten.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url        = home_url( '/stack-solar/' );
$analysis_url    = function_exists( 'hu_get_request_analysis_url' )
	? hu_get_request_analysis_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$sst_url         = home_url( '/server-side-tracking-b2b/' );
$solar_money_url = home_url( '/solar-waermepumpen-leadgenerierung/' );
$whitelabel_url  = function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' );

// ── Zitierfähiger Proof: ausschließlich Werte aus dem E3-Canon ──
$e3_canon          = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics        = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_url       = isset( $e3_canon['url'] ) ? (string) $e3_canon['url'] : home_url( '/case-study-solar-leadgenerierung/' );
$e3_cpl_before     = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after      = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_lead_count     = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_conv_before    = $e3_metrics['sales_conversion_before']['display'] ?? '1 – 5 %';
$e3_conv_after     = $e3_metrics['sales_conversion_after']['display'] ?? '12 %';
$e3_timeframe      = $e3_metrics['timeframe']['display'] ?? '6 Monate';
$e3_build_months   = $e3_metrics['build_months']['display'] ?? '3 Monate';
$e3_tuning_months  = $e3_metrics['tuning_months']['display'] ?? '3 Monate';

// ── Affiliate ─────────────────────────────────────────────────
$hostpress_url        = function_exists( 'hu_get_affiliate_url' ) ? hu_get_affiliate_url( 'hostpress' ) : 'https://www.hostpress.de/wordpress-hosting/';
$hostpress_link_attrs = function_exists( 'hu_render_affiliate_anchor_attrs' ) ? hu_render_affiliate_anchor_attrs( 'hostpress', 'stack_solar_hosting' ) : ' rel="sponsored nofollow noopener" target="_blank"';

// ── Inhalte: 5 Schichten ─────────────────────────────────────
$layers = [
	[
		't' => 'Frontend & Page Speed',
		's' => 'Schlankes WordPress ohne Page-Builder, bedarfsgeladenes CSS und JavaScript sowie eine klar begrenzte Frontend-Schicht. Ziel ist eine stabile, schnelle Anfragestrecke; konkrete Ladezeiten lassen sich auf der jeweiligen Domain vor und nach einer Änderung vergleichen.',
	],
	[
		't' => 'Managed Hosting (DE)',
		's' => 'Managed Hosting bündelt Betrieb, Backups, Updates und Caching in einer verantworteten Umgebung. Die Hosting-Empfehlung ist ein separater Infrastrukturbaustein; Server-Side Tracking läuft über eine eigene First-Party-Subdomain.',
	],
	[
		't' => 'Server-Side Tracking',
		's' => 'Ein Server-Side-Container verbindet GA4, Werbeplattformen und CRM über eine kontrollierte First-Party-Datenstrecke. Der Nutzen ist eine belastbarere Zuordnung von Anfragequelle und Vertriebsstatus; wie stark sich die Datenqualität verbessert, muss je Setup gemessen werden.',
	],
	[
		't' => 'CRM-Anbindung',
		's' => 'Anfragen werden mit Quelle, Kampagne und Qualifizierungsdaten an das vorhandene CRM übergeben. Der Vertrieb erhält den Kontext an einem Ort; Reaktionszeit und Übergabefehler können als eigene Prozesswerte gemessen werden.',
	],
	[
		't' => 'Vorqualifizierung im Anfrageweg',
		's' => 'Die Vorqualifizierung erfasst die Kriterien, die Ihr Vertrieb vor dem Erstkontakt braucht. Welche Fragen, Mindestkriterien und Ausschlussregeln passen, wird erst in der Anfragesystem-Analyse festgelegt — nicht pauschal auf dieser Seite.',
	],
];

// ── Beweis-Block: ausschließlich kanonische Fallwerte ─────────
$proof_metrics = [
	[
		't' => 'Kosten pro Anfrage im dokumentierten Fall',
		's' => sprintf( '%s vorher für gekaufte Anfragen → %s nachher für eigene Anfragen.', $e3_cpl_before, $e3_cpl_after ),
		'b' => sprintf( 'Anonymisierter mittelständischer PV-Installationsbetrieb · %s · keine Übertragbarkeitsgarantie.', $e3_timeframe ),
	],
	[
		't' => 'Qualifizierte Anfragen',
		's' => sprintf( '%s im dokumentierten Zeitraum.', $e3_lead_count ),
		'b' => 'Ergebnis des Falls, kein Mengenversprechen für andere Regionen oder Budgets.',
	],
	[
		't' => 'Abschlussquote',
		's' => sprintf( '%s bei gekauften Portal-Leads → %s beim eigenen Anfragesystem.', $e3_conv_before, $e3_conv_after ),
		'b' => 'Gleicher dokumentierter Fall; Vertrieb, Angebot und Marktbedingungen bleiben relevante Voraussetzungen.',
	],
	[
		't' => 'Umsetzung und Optimierung',
		's' => sprintf( '%s Implementierung, anschließend %s Optimierung.', $e3_build_months, $e3_tuning_months ),
		'b' => 'Der Zeitraum beschreibt den Fall, nicht eine pauschale Lieferzeit.',
	],
];

// ── FAQ ───────────────────────────────────────────────────────
$faq = [
	[
		'question' => sprintf( 'Sind die %s auf %s auf andere Betriebe übertragbar?', $e3_cpl_before, $e3_cpl_after ),
		'answer'   => sprintf( 'Nein. Die Werte dokumentieren einen mittelständischen PV-Installationsbetrieb über %s. Region, Angebot, Werbebudget und Vertriebsprozess unterscheiden sich. Der Fall belegt den Mechanismus aus eigenem Anfrageweg, Vorqualifizierung, Tracking und CRM — keinen Zielwert für Ihren Betrieb.', $e3_timeframe ),
	],
	[
		'question' => 'Was verändert Server-Side Tracking tatsächlich?',
		'answer'   => 'Server-Side Tracking erzeugt nicht automatisch mehr Nachfrage. Es verbessert die Verbindung zwischen Anfragequelle, Qualifizierung und späterem Vertriebsstatus. Ob sich dadurch Datenqualität oder Kosten verändern, muss im jeweiligen Setup gemessen werden.',
	],
	[
		'question' => 'Warum werden Tracking und Vorqualifizierung gemeinsam geplant?',
		'answer'   => 'Die Vorqualifizierung definiert die betrieblichen Fit-Kriterien. Tracking und CRM zeigen anschließend, aus welcher Quelle passende Anfragen und weitergeführte Chancen kamen. Der Nutzen liegt in einer besseren Entscheidungsgrundlage; einen festen Multiplikator leitet die Seite daraus nicht ab.',
	],
	[
		'question' => 'Ist der Stack ein eigenständiges Paket?',
		'answer'   => 'Nein. Die Seite beschreibt eine mögliche Umsetzungsarchitektur. Der konkrete Umfang entsteht erst nach Marktcheck und Anfragesystem-Analyse; bestehende Website, Hosting, Tracking und CRM werden dabei auf Wiederverwendung geprüft.',
	],
	[
		'question' => 'Ist die HostPress-Empfehlung ein Affiliate-Link?',
		'answer'   => 'Ja. Der Link ist als Werbung gekennzeichnet. Bei einem Abschluss kann eine Vergütung entstehen; die Wahl des Hosting-Anbieters bleibt vom Marktcheck und vom Umsetzungsumfang getrennt.',
	],
	[
		'question'   => 'Ich bin Agentur — ist diese Route der richtige Einstieg?',
		'answer'     => 'Nein. Diese Seite richtet sich an Solar- und Wärmepumpen-Anbieter. Für Agenturen bleibt der getrennte White-Label-Einstieg.',
		'url'        => $whitelabel_url,
		'link_label' => 'Zum White-Label-Einstieg für Agenturen',
	],
];

// ── Schema.org: TechArticle + FAQPage ────────────────────────
$author_person = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];

$tech_article_schema = [
	'@context'         => 'https://schema.org',
	'@type'            => 'TechArticle',
	'@id'              => trailingslashit( $page_url ) . '#article',
	'headline'         => 'Technischer Unterbau eines Anfragesystems für Solar und Wärmepumpe',
	'description'      => 'Frontend, Hosting, Server-Side Tracking, CRM-Übergabe und projektspezifische Vorqualifizierung als technischer Unterbau eines eigenen Anfragesystems.',
	'url'              => $page_url,
	'mainEntityOfPage' => $page_url,
	'author'           => $author_person,
	'publisher'        => [ '@id' => home_url( '/#organization' ) ],
	'inLanguage'       => 'de-DE',
];

$faq_schema = [
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => trailingslashit( $page_url ) . '#faq',
	'url'        => trailingslashit( $page_url ) . '#faq',
	'mainEntity' => [],
];

foreach ( $faq as $faq_item ) {
	$faq_schema['mainEntity'][] = [
		'@type'          => 'Question',
		'name'           => $faq_item['question'],
		'acceptedAnswer' => [
			'@type' => 'Answer',
			'text'  => $faq_item['answer'],
		],
	];
}

get_header();
?>

<main id="primary" class="hu-intercept" role="main" data-track-page="stack-solar">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-stack-solar-hero-title">
		<div class="hu-intercept__container">
			<p class="hu-intercept__eyebrow">Technischer Unterbau für eigene Anfragesysteme</p>
			<h1 class="hu-intercept__title" id="hu-stack-solar-hero-title">
				Wie Website, Tracking, Vorqualifizierung und CRM zu einem eigenen Anfrageweg werden.
			</h1>
			<p class="hu-intercept__lead">
				Diese Seite zeigt die fünf technischen Schichten hinter einem <a class="hu-intercept__inline-link" href="<?php echo esc_url( $solar_money_url ); ?>" data-track-action="stack_solar_to_money_page" data-track-category="internal_link" data-track-section="hero">eigenen Anfragesystem für Photovoltaik- und Wärmepumpen-Anbieter</a>. Welche davon Ihr Betrieb braucht, entscheidet erst der Marktcheck und anschließend die Anfragesystem-Analyse.
			</p>
		</div>
	</section>

	<section class="hu-intercept__system" id="layers" aria-labelledby="hu-stack-solar-layers-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-solar-layers-title">Die fünf Schichten</h2>
			<p class="hu-intercept__section-lead">
				Die fünf Schichten sind kein Paket von der Stange. Nach dem Marktcheck klärt die Analyse, welche bestehende Infrastruktur bleiben kann, wo Daten fehlen und welche Übergabe der Vertrieb tatsächlich braucht.
			</p>

			<?php
			set_query_var( 'affiliate_notice_provider_label', 'HostPress' );
			set_query_var( 'affiliate_notice_context', 'stack' );
			get_template_part( 'template-parts/affiliate-notice' );
			?>

			<ol class="hu-intercept__layers">
				<?php foreach ( $layers as $i => $layer ) : ?>
					<li class="hu-intercept__layer">
						<span class="hu-intercept__layer-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="hu-intercept__layer-body">
							<h3 class="hu-intercept__layer-title"><?php echo esc_html( $layer['t'] ); ?></h3>
							<p class="hu-intercept__layer-text"><?php echo esc_html( $layer['s'] ); ?></p>
							<?php if ( 'Managed Hosting (DE)' === $layer['t'] ) : ?>
								<p class="hu-intercept__layer-text">
									<a class="hu-intercept__inline-link" href="<?php echo esc_url( $hostpress_url ); ?>"<?php echo $hostpress_link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — bereits in helper-funktion escaped ?>>
										HostPress ansehen
									</a>
									<span class="hu-intercept__layer-meta">Werbung · Partnerlink</span>
								</p>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
			<p class="hu-intercept__section-lead">
				Technische Vertiefung: <a class="hu-intercept__inline-link" href="<?php echo esc_url( $sst_url ); ?>" data-track-action="stack_solar_to_sst" data-track-category="internal_link" data-track-section="layers">Server-Side Tracking im Anfrageweg</a>.
			</p>
		</div>
	</section>

	<section class="hu-intercept__why" id="closed-loop" aria-labelledby="hu-stack-solar-loop-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-solar-loop-title">Warum die Verbindung wichtiger ist als ein einzelnes Tool</h2>
			<p class="hu-intercept__section-lead">
				Tracking kann nur dann eine sinnvolle Budgetentscheidung unterstützen, wenn klar definiert ist, welche Anfrage zum Betrieb passt. Vorqualifizierung liefert diese Kriterien; CRM und Tracking verbinden sie mit Quelle und späterem Vertriebsstatus.
			</p>
			<div class="hu-intercept__card">
				<p class="hu-intercept__card-text">
					<strong>Die Vorqualifizierung legt nicht pauschal einen „guten Lead“ fest.</strong> Sie erfasst die Kriterien, die der Vertrieb tatsächlich nutzt — etwa Zielgebiet, Projektart, Projektwert, Rolle und Zeithorizont.
				</p>
				<p class="hu-intercept__card-text">
					So wird sichtbar, welche Quelle nicht nur Formulare, sondern passende Anfragen erzeugt. Auswirkungen auf Kosten pro Anfrage, Abschlussquote und Reaktionszeit müssen im jeweiligen Setup gemessen werden.
				</p>
			</div>
		</div>
	</section>

	<section class="hu-intercept__system" id="zahlen" aria-labelledby="hu-stack-solar-numbers-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-solar-numbers-title">Was der dokumentierte Fall belegt — und was nicht.</h2>
			<p class="hu-intercept__section-lead">
				Die einzige zitierfähige Ergebnisbasis ist der anonymisierte Fall eines mittelständischen PV-Installationsbetriebs. Er zeigt den Mechanismus aus eigenem Anfrageweg, Vorqualifizierung, Tracking und CRM über <?php echo esc_html( $e3_timeframe ); ?>; er ist keine Prognose für Ihren Betrieb.
			</p>
			<ol class="hu-intercept__layers">
				<?php foreach ( $proof_metrics as $i => $m ) : ?>
					<li class="hu-intercept__layer">
						<span class="hu-intercept__layer-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="hu-intercept__layer-body">
							<h3 class="hu-intercept__layer-title"><?php echo esc_html( $m['t'] ); ?></h3>
							<p class="hu-intercept__layer-text"><?php echo esc_html( $m['s'] ); ?></p>
							<p class="hu-intercept__layer-text"><span class="hu-intercept__layer-meta"><?php echo esc_html( $m['b'] ); ?></span></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
			<p class="hu-intercept__section-lead">
				Ob dieselbe Architektur wirtschaftlich passt, hängt unter anderem von Region, Projektwert, Vertriebskapazität, bestehender Datenlage und Werbebudget ab. Das klärt zuerst der Marktcheck, danach die Anfragesystem-Analyse.
			</p>
			<p class="hu-intercept__section-lead">
				<a class="hu-intercept__inline-link" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="stack_solar_to_case" data-track-category="trust" data-track-section="zahlen">Dokumentierten Solar-Case mit allen Zahlen lesen</a>
			</p>

			<div class="hu-intercept__cta hu-intercept__cta--inline">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $analysis_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="stack_solar"
				   data-track-section="zahlen">
					Marktcheck mit Fit-Entscheid starten
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-stack-solar-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-solar-faq-title">Häufige Fragen zum Solar-Stack</h2>
			<div class="hu-intercept__faq-list">
				<?php foreach ( $faq as $item ) : ?>
					<details class="hu-intercept__faq-item" name="hu-faq-stack-solar">
						<summary class="hu-intercept__faq-q"><?php echo esc_html( $item['question'] ); ?></summary>
						<p class="hu-intercept__faq-a"><?php echo esc_html( $item['answer'] ); ?></p>
						<?php if ( ! empty( $item['url'] ) ) : ?>
							<p class="hu-intercept__faq-a"><a class="hu-intercept__inline-link" href="<?php echo esc_url( $item['url'] ); ?>" data-track-action="stack_solar_to_whitelabel" data-track-category="internal_link" data-track-section="faq"><?php echo esc_html( $item['link_label'] ); ?></a></p>
						<?php endif; ?>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<script type="application/ld+json"><?php echo wp_json_encode( $tech_article_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
</main>

<?php
get_footer();
