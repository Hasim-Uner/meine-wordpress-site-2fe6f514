<?php
/**
 * Template Name: Case Study – DOMDAR
 * Description: Sustainable Commerce Case Study: DOMDAR – vom 54-Euro-Warenkorb zur Profit-Architektur
 *
 * Design: Nutzt Nexus Design System (design-system.css + case-study.css)
 * SEO-Meta: inc/seo-meta.php (ACF-Felder: seo_title, seo_description, og_image)
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$audit_url    = nexus_get_audit_url();
$cases_url    = nexus_get_results_url();
$wgos_url     = nexus_get_primary_public_url( 'wgos', home_url( '/wordpress-agentur-hannover/#wgos' ) );
$local_wp_url = nexus_get_primary_public_url( 'agentur', home_url( '/wordpress-agentur-hannover/' ) );
$instagram_url = 'https://www.instagram.com/domdar.de/';
$portrait_url = home_url( '/wp-content/uploads/2025/09/Wordpress_Bild_Hero.webp' );

$hero_kpis = [
	[
		'value' => '120 €',
		'label' => 'AOV in 6 Wochen',
	],
	[
		'value' => '4,6 %',
		'label' => 'Conversion Rate',
	],
	[
		'value' => '0 €',
		'label' => 'mehr Ad-Spend',
	],
];

$fact_items = [
	[
		'label' => 'Branche',
		'value' => 'Sustainable Commerce / D2C + B2B-Handel',
	],
	[
		'label' => 'Startpunkt',
		'value' => '54 € AOV · 1,5 % CR · 4,2 s Ladezeit',
	],
	[
		'label' => 'Ziel',
		'value' => 'Profitabilität ohne Budget-Erhöhung',
	],
	[
		'label' => 'Hebel',
		'value' => 'Bundles · Recovery-Flows · Retourenportal',
	],
	[
		'label' => 'Vertrieb',
		'value' => 'Endkunden, Händler, Shops und Großhändler',
	],
	[
		'label' => 'Zeitraum',
		'value' => '6 Wochen',
	],
	[
		'label' => 'Fokus',
		'value' => 'AOV · CRO · Operations',
	],
];

$strengths = [
	[
		'label' => 'Deckungsbeitrag',
		'text'  => 'Mit 54 € durchschnittlichem Warenkorb deckte der Shop Kosten, aber nicht das nächste Wachstumslevel.',
	],
	[
		'label' => 'Funnel',
		'text'  => 'Warenkorbabbrecher und Bestandskunden wurden nicht systematisch zurückgeführt oder weiterentwickelt.',
	],
	[
		'label' => 'Operations',
		'text'  => 'Retouren liefen zu manuell und erzeugten Support-Reibung an einem Punkt, an dem Vertrauen entscheidend ist.',
	],
];

$problem_cards = [
	[
		'number'   => '01',
		'title'    => 'Zu wenig Marge pro Bestellung',
		'copy'     => 'Der Warenkorb war zu klein, um Akquise wirklich skalierbar zu machen. Wachstum wäre mit gleichem Setup nur teurer geworden.',
		'accented' => false,
	],
	[
		'number'   => '02',
		'title'    => 'Löchriger Funnel nach dem ersten Besuch',
		'copy'     => 'Es fehlten Recovery-Mails für Abbrecher, Cross-Sells im Checkout und Post-Purchase-Upsells für Bestandskunden.',
		'accented' => true,
	],
	[
		'number'   => '03',
		'title'    => 'Retouren frassen Zeit und Vertrauen',
		'copy'     => 'Manuelle Retourenscheine und E-Mail-Ping-Pong passten nicht zu den Erwartungen moderner E-Commerce-Kunden.',
		'accented' => false,
	],
];

$results = [
	[
		'value'   => '120 €',
		'label'   => 'Average Order Value',
		'note'    => 'Ausgangswert: 54 €',
		'primary' => true,
	],
	[
		'value'   => '4,6 %',
		'label'   => 'Conversion Rate',
		'note'    => 'vorher 1,5 %',
		'primary' => false,
	],
	[
		'value'   => '0 €',
		'label'   => 'mehr Ad-Spend',
		'note'    => 'Wachstum aus bestehendem Traffic',
		'primary' => false,
	],
	[
		'value'   => '4,7 ★',
		'label'   => 'Kundenzufriedenheit',
		'note'    => 'stabil trotz Retourenprozess',
		'primary' => false,
	],
	[
		'value'   => 'Automatisiert',
		'label'   => 'Retourenabwicklung',
		'note'    => 'weniger Support-Reibung, mehr Klarheit',
		'primary' => false,
	],
	[
		'value'   => 'Recovery-Loops',
		'label'   => 'Retention-System',
		'note'    => 'mehr Wert pro bestehendem Besucher',
		'primary' => false,
	],
];

$architecture = [
	[
		'step'      => '1',
		'kicker'    => 'Säule 01',
		'title'     => 'Bundle-Psychologie und AOV-Boost',
		'copy'      => 'Der Shop verkaufte nicht länger einzelne Produkte, sondern klarere Lösungspakete. Bundles, Vorratspacks und Checkout-Cross-Sells erhöhten den Bestellwert ohne mehr Traffic einzukaufen.',
		'outcomes'  => [
			'Lösungs-Pakete statt Einzelprodukte',
			'Cross-Sells direkt im Checkout',
			'Mehr Marge pro Versandvorgang',
		],
		'highlight' => false,
	],
	[
		'step'      => '2',
		'kicker'    => 'Säule 02',
		'title'     => 'Recovery- und Retention-Loops',
		'copy'      => 'Warenkorbabbrecher-Flows und Post-Purchase-Upsells holten Nachfrage zurück, die vorher verloren ging. Dadurch wurde jeder bestehende Besucher wirtschaftlich wertvoller.',
		'outcomes'  => [
			'Abbruch-Flows für verlorene Warenkörbe',
			'Post-Purchase-Upsells nach dem Kauf',
			'Mehr Lifetime Value ohne neue Klickkosten',
		],
		'highlight' => true,
	],
	[
		'step'      => '3',
		'kicker'    => 'Säule 03',
		'title'     => 'Amazon-Level Operations',
		'copy'      => 'Ein automatisches Retourenportal inklusive Status-Logik reduzierte Support-Aufwand und Kaufbarrieren zugleich. Der operative Prozess wurde zum Vertrauensfaktor statt zum Risiko.',
		'outcomes'  => [
			'Automatisches Retourenportal',
			'Klarer Status für Kunde und Team',
			'Vertrauen auch im Worst Case',
		],
		'highlight' => false,
	],
];

$faq_items = [
	[
		'question' => 'Wie wurde der Warenkorbwert fast verdreifacht?',
		'answer'   => 'Durch konsequentes Bundling, Checkout-Cross-Sells und Vorratspacks. Statt nur das Basisprodukt zu verkaufen, wurde die Kaufentscheidung als Komplettlösung strukturiert.',
	],
	[
		'question' => 'Warum stieg die Conversion Rate ohne mehr Budget?',
		'answer'   => 'Weil Reibung an mehreren Stellen verschwand: klarere Angebotsstruktur, Recovery-Flows für Abbrecher, bessere Performance und weniger Unsicherheit bei Retouren.',
	],
	[
		'question' => 'Warum war das Retourenportal so wichtig?',
		'answer'   => 'Weil Vertrauen nicht nur vor dem Kauf entsteht. Ein klarer Rückgabeprozess senkt die Kaufbarriere, spart Support-Zeit und erhöht die wahrgenommene Professionalität des Shops.',
	],
	[
		'question' => 'Ist dieser Ansatz nur für Shops relevant?',
		'answer'   => 'Nein. Das Prinzip ist allgemeiner: mehr Wert pro bestehendem Besucher, weniger Reibung im Prozess und bessere Nutzbarkeit vor und nach dem Kauf. Der genaue Hebel hängt vom Geschäftsmodell ab.',
	],
];

$author_points = [
	'Ich optimiere keine hübschen Oberflächen, sondern Nachfrage-Systeme mit klarer wirtschaftlicher Funktion.',
	'WordPress, CRO, Performance und Prozesse greifen bei mir als ein Verbund statt als getrennte Disziplinen.',
	'Der Einstieg bleibt bewusst diagnosegetrieben: erst die System-Diagnose, dann Prioritäten und erst danach Umsetzung.',
];

get_header();
?>

<div class="cs-case-wrapper cs-case-wrapper--domdar">

<div id="nx-progress-bar"></div>

<nav class="nx-sidenav nx-hide-mobile" id="case-nav" aria-label="Seitennavigation">
	<ul>
		<li><a href="#hero"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">Start</span></a></li>
		<li><a href="#kontext"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">Kontext</span></a></li>
		<li><a href="#problem"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">Probleme</span></a></li>
		<li><a href="#ergebnisse"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">Ergebnis</span></a></li>
		<li><a href="#architektur"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">Architektur</span></a></li>
		<li><a href="#partner"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">Über mich</span></a></li>
		<li><a href="#faq"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">FAQ</span></a></li>
	</ul>
</nav>

<section class="cs-case-hero cs-case-hero--domdar nx-section nx-hero--compact" id="hero">
	<div class="nx-container">
		<span class="nx-badge nx-badge--gold">Case Study: DOMDAR</span>

		<h1 class="cs-case-hero__title">
			Nachhaltigkeit skaliert nicht mit Ideologie, sondern mit <span class="nx-text-gold">System.</span>
		</h1>

		<p class="cs-case-hero__subtitle">
			Vom 54-Euro-Warenkorb zur 120-Euro-Profit-Architektur in 6 Wochen:
			Wie DOMDAR mit Bundles, Recovery-Loops und operativer Entlastung
			mehr Deckungsbeitrag aus bestehendem Traffic holte.
		</p>

		<div class="cs-kpi-row cs-kpi-row--domdar">
			<?php foreach ( $hero_kpis as $hero_kpi ) : ?>
				<div class="cs-kpi-tile">
					<span class="cs-kpi-value nx-text-gold"><?php echo esc_html( $hero_kpi['value'] ); ?></span>
					<span class="cs-kpi-label"><?php echo esc_html( $hero_kpi['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="cs-hero-ctas">
			<a
				href="<?php echo esc_url( $audit_url ); ?>"
				class="nx-btn nx-btn--primary"
				data-track-action="cta_domdar_hero_audit"
				data-track-category="lead_gen"
			>
				<?php echo esc_html( nexus_get_audit_cta_label() ); ?>
			</a>
			<a href="#architektur" class="nx-btn nx-btn--ghost">
				Profit-Architektur ansehen
			</a>
		</div>

		<div class="cs-hero-meta">
			<span class="cs-meta-item">Sustainable Commerce</span>
			<span class="cs-meta-sep">·</span>
			<span class="cs-meta-item">B2C + B2B-Handel</span>
			<span class="cs-meta-sep">·</span>
			<span class="cs-meta-item">54 € AOV am Start</span>
			<span class="cs-meta-sep">·</span>
			<span class="cs-meta-item">4,2 s Ladezeit vor dem Relaunch</span>
			<span class="cs-meta-sep">·</span>
			<span class="cs-meta-item">6 Wochen Umsetzung</span>
			<span class="cs-meta-sep">·</span>
			<span class="cs-meta-item">kein höheres Werbebudget</span>
		</div>
	</div>
</section>

<section class="nx-section cs-section-alt" id="kontext">
	<div class="nx-container">

		<div class="nx-section-header">
			<span class="nx-badge nx-badge--ghost">Ausgangssituation</span>
			<h2 class="nx-headline-section" style="margin-top:1rem;">
				Ein Shop mit gutem Produkt, aber zu wenig Hebel pro Bestellung
			</h2>
			<p class="nx-subheadline" style="margin:1rem auto 0;">
				DOMDAR war kein kaputter Shop. Das Problem lag in der Mechanik dahinter:
				zu wenig Deckungsbeitrag, zu viele verlorene Warenkörbe und zu viel
				manuelle Reibung nach dem Kauf.
			</p>
		</div>

		<div class="cs-kontext-grid" style="margin-top:3rem;">
			<div class="cs-kontext-text nx-prose">
				<p>
					Die Produkte funktionierten. Nachfrage war da. Trotzdem blieb der Shop
					wirtschaftlich unter seinen Möglichkeiten, weil jeder Verkauf zu wenig
					Marge trug und nachgelagerte Prozesse zu viel manuelle Energie banden.
				</p>
				<p>
					DOMDAR war dabei nicht nur ein Endkunden-Shop. Neben dem D2C-Geschäft
					kauften auch Händler, Shops und Großhändler ein, digital wie physisch.
					Genau diese Mischform aus B2C und B2B machte das Setup anspruchsvoller
					als einen reinen Consumer-Shop.
				</p>
				<p>
					Das Ziel war deshalb kein kosmetischer Relaunch. Ziel war ein Setup,
					das denselben Traffic profitabler macht: höherer Warenkorb, bessere
					Conversion, weniger Support-Aufwand und ein Rückgabeprozess, der
					Vertrauen stärkt statt Zweifel auszulösen. Sichtbar war die Marke auch
					über <a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener noreferrer">Instagram</a>,
					nicht nur über den Shop selbst.
				</p>
				<ul class="cs-strength-list">
					<?php foreach ( $strengths as $strength ) : ?>
						<li>
							<strong><?php echo esc_html( $strength['label'] ); ?>:</strong>
							<?php echo esc_html( $strength['text'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="cs-kontext-card nx-card cs-domdar-facts-card">
				<p class="nx-card__subtitle">Eckdaten</p>
				<ul class="cs-fact-list">
					<?php foreach ( $fact_items as $fact_item ) : ?>
						<li>
							<span class="cs-fact-label"><?php echo esc_html( $fact_item['label'] ); ?></span>
							<span class="cs-fact-value"><?php echo esc_html( $fact_item['value'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

	</div>
</section>

<section class="nx-section" id="problem">
	<div class="nx-container">

		<div class="nx-section-header">
			<span class="nx-badge nx-badge--gold">Die echten Bremsen</span>
			<h2 class="nx-headline-section" style="margin-top:1rem;">
				Drei Probleme, die Profitabilität verhindert haben
			</h2>
			<p class="nx-subheadline" style="margin:1rem auto 0;">
				Nicht mehr Traffic war zuerst die Antwort, sondern bessere Struktur rund
				um Warenkorb, Funnel und Service-Prozess.
			</p>
		</div>

		<div class="nx-grid nx-grid-3" style="margin-top:3rem;">
			<?php foreach ( $problem_cards as $problem_card ) : ?>
				<article class="nx-card nx-card--flat cs-constraint-card<?php echo esc_attr( $problem_card['accented'] ? ' cs-constraint-card--highlight' : '' ); ?>">
					<div class="cs-constraint-num"><?php echo esc_html( $problem_card['number'] ); ?></div>
					<h3 class="nx-card__title"><?php echo esc_html( $problem_card['title'] ); ?></h3>
					<p class="nx-card__text"><?php echo esc_html( $problem_card['copy'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>

	</div>
</section>

<section class="nx-section cs-section-results" id="ergebnisse">
	<div class="nx-container">

		<div class="nx-section-header">
			<span class="nx-badge nx-badge--gold">Der Beweis</span>
			<h2 class="nx-headline-section" style="margin-top:1rem;">
				System schlägt Budget
			</h2>
			<p class="nx-subheadline" style="margin:1rem auto 0;">
				Trotz konstantem Marketingbudget kamen die wirtschaftlichen Sprünge aus
				besserem Warenkorb-Design, sauberem Recovery-Setup und robusterem Betrieb.
			</p>
		</div>

		<div class="cs-results-grid" style="margin-top:3rem;">
			<?php foreach ( $results as $result ) : ?>
				<article class="cs-result-card<?php echo esc_attr( $result['primary'] ? ' cs-result-card--primary' : '' ); ?>">
					<span class="cs-result-value"><?php echo esc_html( $result['value'] ); ?></span>
					<span class="cs-result-label"><?php echo esc_html( $result['label'] ); ?></span>
					<span class="cs-result-note"><?php echo esc_html( $result['note'] ); ?></span>
				</article>
			<?php endforeach; ?>
		</div>

		<article class="cs-budget-proof">
			<p class="cs-budget-proof__eyebrow">Warum das funktioniert hat</p>
			<h3 class="cs-budget-proof__title">Profitabilität entstand aus Reihenfolge, nicht aus Mehrbudget.</h3>
			<p class="cs-budget-proof__intro">
				Die 4,2-Sekunden-Reibung, der kleine Warenkorb und die operativen Brüche
				waren keine isolierten Probleme. Erst das Zusammenspiel aus Performance,
				Preispsychologie und Prozesssicherheit machte den Hebel wirklich wirksam.
			</p>
			<ul class="cs-budget-proof__list">
				<li>
					<strong>Performance reduzierte Kauf-Reibung:</strong> Weniger Wartezeit
					und klarere Produktpfade halfen, die Conversion von 1,5 Prozent auf
					4,6 Prozent zu heben.
				</li>
				<li>
					<strong>Bundles verschoben den Deckungsbeitrag:</strong> Lösungspakete,
					Cross-Sells und Vorratspacks machten denselben Besucher wirtschaftlich
					deutlich wertvoller.
				</li>
				<li>
					<strong>Operations stärkten Vertrauen:</strong> Rückgabe und
					Nachkauf-Logik wurden vom Support-Problem zu einem systemischen Teil
					des Verkaufserlebnisses.
				</li>
			</ul>
		</article>

	</div>
</section>

<section class="nx-section" id="architektur">
	<div class="nx-container">

		<div class="nx-section-header">
			<span class="nx-badge nx-badge--ghost">Die Lösung</span>
			<h2 class="nx-headline-section" style="margin-top:1rem;">
				Die 3-Säulen Profit-Architektur
			</h2>
			<p class="nx-subheadline" style="margin:1rem auto 0;">
				Kein hübscher Relaunch und keine Tool-Sammlung, sondern drei Hebel, die
				deutlich mehr Marge pro bestehendem Besucher erzeugen.
			</p>
		</div>

		<div class="cs-phases" style="margin-top:3rem;">
			<?php foreach ( $architecture as $phase ) : ?>
				<article class="cs-phase-item<?php echo esc_attr( $phase['highlight'] ? ' cs-phase-item--highlight' : '' ); ?>">
					<div class="cs-phase-header">
						<div class="nx-step__number"><?php echo esc_html( $phase['step'] ); ?></div>
						<div>
							<span class="cs-phase-label"><?php echo esc_html( $phase['kicker'] ); ?></span>
							<h3 class="cs-phase-title"><?php echo esc_html( $phase['title'] ); ?></h3>
						</div>
					</div>
					<div class="cs-phase-body">
						<p class="nx-card__text"><?php echo esc_html( $phase['copy'] ); ?></p>
						<ul class="cs-phase-outcomes">
							<?php foreach ( $phase['outcomes'] as $outcome ) : ?>
								<li><?php echo esc_html( $outcome ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

	</div>
</section>

<section class="nx-section cs-section-alt" id="partner">
	<div class="nx-container">

		<div class="cs-domdar-partner-grid">
			<div class="cs-domdar-portrait-card">
				<div class="cs-domdar-portrait-shell">
					<img src="<?php echo esc_url( $portrait_url ); ?>" alt="Haşim Üner" loading="lazy">
				</div>
				<div class="cs-domdar-portrait-meta">
					<span class="cs-domdar-portrait-label">Haşim Üner</span>
					<span class="cs-domdar-portrait-text">Architekt für eigene Anfrage-Systeme: Solar, Wärmepumpe und systemische Nachfrage-Logik</span>
				</div>
			</div>

			<div class="cs-domdar-partner-copy">
				<span class="nx-badge nx-badge--gold">Der Stratege</span>
				<h2 class="nx-headline-section">Haşim Üner</h2>
				<p class="nx-subheadline cs-domdar-partner-intro">
					Ich baue keine hübschen Websites. Ich baue Systeme, die mehr Wert aus
					bestehender Nachfrage holen. Bei DOMDAR zeigte sich genau das:
					Profitabilität entsteht, wenn Angebot, Prozess und Vertrauen endlich
					als ein System gedacht werden.
				</p>
				<ul class="cs-strength-list cs-domdar-author-list">
					<?php foreach ( $author_points as $author_point ) : ?>
						<li><?php echo esc_html( $author_point ); ?></li>
					<?php endforeach; ?>
				</ul>
				<div class="cs-hero-ctas cs-hero-ctas--left">
					<a
						href="<?php echo esc_url( $audit_url ); ?>"
						class="nx-btn nx-btn--primary"
						data-track-action="cta_domdar_partner_audit"
						data-track-category="lead_gen"
					>
						<?php echo esc_html( nexus_get_audit_cta_label() ); ?>
					</a>
					<a href="<?php echo esc_url( $wgos_url ); ?>" class="nx-btn nx-btn--ghost">
						WGOS ansehen
					</a>
				</div>
			</div>
		</div>

	</div>
</section>

<section class="nx-section" id="faq">
	<div class="nx-container">

		<div class="nx-section-header">
			<span class="nx-badge nx-badge--ghost">FAQ</span>
			<h2 class="nx-headline-section" style="margin-top:1rem;">
				Häufige Fragen zur Umsetzung
			</h2>
		</div>

		<div class="nx-faq" style="margin-top:2rem;">
			<?php foreach ( $faq_items as $index => $faq_item ) : ?>
				<details class="nx-faq__item"<?php echo 0 === $index ? ' open' : ''; ?>>
					<summary><?php echo esc_html( $faq_item['question'] ); ?></summary>
					<div class="nx-faq__content">
						<?php echo esc_html( $faq_item['answer'] ); ?>
					</div>
				</details>
			<?php endforeach; ?>
		</div>

	</div>
</section>

<section class="nx-section cs-section-cta" id="next-step">
	<div class="nx-container">

		<div class="nx-cta-box">
			<span class="nx-badge nx-badge--gold" style="margin-bottom:1.5rem;display:inline-block;">Nächster Schritt</span>
			<h2 style="font-size:clamp(1.8rem,3.5vw,2.8rem);margin-bottom:1rem;">
				Welche Reibung kostet Sie gerade Marge?
			</h2>
			<p style="color:var(--nx-text-muted);max-width:560px;margin:0 auto 2rem;line-height:1.6;">
				Im System-Diagnose sehen Sie, wo Warenkorb, Conversion oder operative
				Prozesse aktuell Ertrag kosten und welche Reihenfolge für Ihr Setup
				wirklich Sinn ergibt.
			</p>

			<div class="cs-cta-buttons">
				<a
					href="<?php echo esc_url( $audit_url ); ?>"
					class="nx-btn nx-btn--primary"
					data-track-action="cta_domdar_nextstep_audit"
					data-track-category="lead_gen"
				>
					<?php echo esc_html( nexus_get_audit_cta_label() ); ?>
				</a>
			</div>

			<div class="cs-internal-links">
				<a href="<?php echo esc_url( $cases_url ); ?>" class="cs-internal-link">Weitere Ergebnisse</a>
				<a href="<?php echo esc_url( $wgos_url ); ?>" class="cs-internal-link">WGOS: Das System dahinter</a>
				<a href="<?php echo esc_url( $local_wp_url ); ?>" class="cs-internal-link">WordPress Agentur Hannover</a>
			</div>
		</div>

	</div>
</section>

</div>

<?php get_footer(); ?>
