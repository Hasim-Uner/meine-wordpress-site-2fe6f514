<?php
/**
 * Template Name: Agentur Service (Hannover)
 * Description: Lokale SEO-Landingpage für WordPress Growth Architect Hannover
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$audit_url = nexus_get_audit_url();
$wgos_url  = nexus_get_page_url( [ 'wordpress-growth-operating-system', 'wgos' ] );
$cases_url = nexus_get_results_url();
$about_url = nexus_get_page_url( [ 'uber-mich' ] );
$contact_url = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' );
$implementation_contact_url = add_query_arg(
	[
		'type' => 'implementation',
	],
	$contact_url
);
$e3_url    = nexus_get_page_url( [ 'e3-new-energy' ] );
$seo_url   = nexus_get_primary_public_url( 'seo', home_url( '/wordpress-seo-hannover/' ) );
$tracking_url = nexus_get_primary_public_url( 'tracking', home_url( '/ga4-tracking-setup/' ) );
$wartung_url = nexus_get_primary_public_url( 'wartung', home_url( '/wordpress-wartung-hannover/' ) );
$measurement_url = nexus_get_wgos_asset_anchor_url( 'tracking-audit' );
$cro_url   = nexus_get_primary_public_url( 'cro', home_url( '/conversion-rate-optimization/' ) );
$proof_metrics = function_exists( 'nexus_get_public_proof_metric_list' ) ? nexus_get_public_proof_metric_list( [ 'lead_count', 'sales_conversion', 'cpl_reduction' ] ) : [];
$e3_cpl_reduction = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_reduction', 'display', 'über 85 %' ) : 'über 85 %';
$e3_lead_count = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'lead_count', 'display', '1.750+' ) : '1.750+';
$e3_sales_conversion = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'sales_conversion', 'display', '12 %' ) : '12 %';
$canonical_ownership_sentence = function_exists( 'nexus_get_public_ownership_sentence' ) ? nexus_get_public_ownership_sentence() : 'Code, Inhalte, Zugänge und Setups bleiben bei Ihnen. Laufende Zusammenarbeit bedeutet Weiterentwicklung, nicht Abhängigkeit.';
$primary_term                = function_exists( 'nexus_get_public_primary_term' ) ? nexus_get_public_primary_term() : 'WordPress als Nachfrage-System für B2B';
$audit_cta_label             = function_exists( 'nexus_get_audit_cta_label' ) ? nexus_get_audit_cta_label() : 'System-Diagnose starten';
$audit_compact_microcopy     = function_exists( 'nexus_get_audit_compact_microcopy' ) ? nexus_get_audit_compact_microcopy() : '60 Sek. · priorisierte Hebel · keine E-Mail';

$pain_cards = [
	[
		'icon'  => '01',
		'title' => 'Sichtbarkeit ohne Richtung',
		'text'  => 'Es gibt Inhalte, aber keine saubere Verbindung zwischen Suchintention, Angebotsseite und nächstem Schritt. Genau dort greifen <a href="#technisches-seo">technisches SEO</a> und Angebotslogik ineinander.',
	],
	[
		'icon'  => '02',
		'title' => 'Daten ohne Entscheidungswert',
		'text'  => 'Tracking ist installiert, aber nicht belastbar. Consent, Events und Attribution erzeugen Rauschen statt Klarheit. Deshalb ist <a href="' . esc_url( $measurement_url ) . '">privacy-first Measurement</a> Fundament und kein Add-on.',
	],
	[
		'icon'  => '03',
		'title' => 'Seiten ohne Conversion-Führung',
		'text'  => 'Kontaktformulare am Seitenende sind keine Funnel-Logik. Wenn Proof, CTA-Reihenfolge und Einwandabbau fehlen, verliert die Seite Nachfrage genau dann, wenn sie wertvoll werden könnte. Dort setzt <a href="' . esc_url( $cro_url ) . '">Conversion-Architektur</a> an.',
	],
];

$case_teaser_cards = [
	[
		'eyebrow' => 'Ausgangslage',
		'title'   => 'Hoher CPL, schwache Daten, Reibung nach dem Klick',
		'text'    => 'E3 New Energy kaufte Leads teuer ein, ohne saubere Leadqualität und ohne robuste Conversion-Führung auf der Website.',
	],
	[
		'eyebrow' => 'Maßnahme',
		'title'   => 'Erst Fundament, dann Aktivierung',
		'text'    => 'Speed, Tracking, Seitenstruktur und Conversion-Pfade wurden geordnet, bevor neue Skalierung auf das Setup geschaltet wurde.',
	],
];

$fit_items = [
	'WordPress ist ein echter Geschäftskanal und nicht nur ein Nebenprojekt.',
	'Es gibt ein belastbares Leistungsversprechen und kaufnahe Nachfrage.',
	'Sie wollen Prioritäten für Angebotsseiten, Tracking, Performance und Conversion statt Maßnahmensammlung.',
	'Messbarkeit, Ownership und kontrollierte Weiterentwicklung sind wichtiger als reine Kosmetik.',
];

$service_items = [
	'WordPress-Websites, die qualifizierte B2B-Anfragen generieren — nicht nur gut aussehen',
	'Technische SEO für lokale und kaufnahe Sichtbarkeit in WordPress',
	'GA4-, GTM- und Server-Side-Tracking-Setups, die DSGVO-konform echte Entscheidungen ermöglichen',
	'Conversion Rate Optimierung für bestehende WordPress-Landingpages und Angebotsseiten',
	'Laufende Weiterentwicklung mit klarer Priorisierung statt Relaunch-Zyklen',
];

$faq_items = [
	[
		'question' => 'Welche WordPress Agentur in Hannover hilft bei Leadgenerierung für B2B?',
		'answer'   => 'Die passende Agentur verbindet nicht nur Design und Entwicklung, sondern auch Angebotsseiten, technische SEO, Tracking und Conversion-Führung. Genau darauf ist diese Seite ausgerichtet: WordPress als Nachfrage-System für B2B statt isolierter Einzelleistungen.',
	],
	[
		'question' => 'Arbeiten Sie nur in Hannover oder auch in Pattensen und der Region Hannover?',
		'answer'   => 'Der Standort ist Pattensen bei Hannover. Workshops, Reviews und persönliche Termine sind in Hannover und der Region problemlos möglich. Die Umsetzung funktioniert zusätzlich DACH-weit remote.',
	],
	[
		'question' => 'Unterstützen Sie auch Tracking und Conversion-Optimierung oder nur WordPress-Entwicklung?',
		'answer'   => 'Ja. Die Arbeit umfasst nicht nur WordPress-Entwicklung, sondern auch technische SEO, GA4- und Tracking-Setups, Conversion Rate Optimierung und die Priorisierung kaufnaher Seiten. Genau diese Kombination ist für Leadgenerierung im B2B meist entscheidend.',
	],
	[
		'question' => 'Wann ist die Agentur-Seite der richtige Einstieg und wann die System-Diagnose?',
		'answer'   => 'Die Agentur-Seite ist richtig, wenn Sie den Gesamtkontext verstehen wollen. Die System-Diagnose ist der bessere nächste Schritt, wenn klar werden soll, ob zuerst SEO, Tracking, Performance oder Conversion den größten Hebel hat.',
	],
	[
		'question' => 'Warum rankt eine WordPress-Seite, liefert aber keine Anfragen?',
		'answer'   => 'Ranking und Konversion sind zwei verschiedene Probleme. Oft rankt eine Seite für Begriffe mit Informations-Intent, nicht für kaufnahe Suchen. Dazu kommen fehlende Proof-Signale, kein klarer nächster Schritt und Formulare, die zu spät im Seitenfluss erscheinen. Genau dort setzt die Kombination aus technischer SEO, Angebotsseiten-Struktur und Conversion-Führung an.',
	],
	[
		'question' => 'Bieten Sie WordPress-Optimierung auch in Pattensen und der Region Hannover an?',
		'answer'   => 'Ja. Der Standort ist Pattensen bei Hannover. Persönliche Termine, Workshops und Reviews sind in der gesamten Region Hannover möglich. Gleichzeitig funktioniert die Zusammenarbeit vollständig remote für Kunden in ganz Deutschland, Österreich und der Schweiz.',
	],
];

get_header();
?>

<main id="main" class="site-main">
	<div class="cs-page wp-agentur-page-wrapper" data-track-section="agentur_landing">

		<section id="hero" class="nx-section nx-hero wp-agentur-hero">
			<div class="nx-container">
				<div class="wp-agentur-hero__content">
					<span class="nx-badge nx-badge--gold">Hannover · B2B · WordPress</span>
					<h1 class="nx-hero__title">Anfragen statt nur Sichtbarkeit.</h1>
					<p class="nx-hero__subtitle">
						Angebotsseiten, SEO, Tracking und Conversion als ein System — damit Ihre WordPress-Website qualifizierte B2B-Leads liefert.
					</p>
					<div class="wp-agentur-actions wp-agentur-actions--hero">
						<a href="<?php echo esc_url( $audit_url ); ?>" class="nx-btn nx-btn--primary wp-agentur-hero__primary" data-track-action="cta_agentur_hero_audit" data-track-category="lead_gen"><?php echo esc_html( $audit_cta_label ); ?></a>
						<a href="<?php echo esc_url( $cases_url ); ?>" class="wp-agentur-text-link" data-track-action="cta_agentur_hero_results" data-track-category="trust">Ergebnisse ansehen</a>
					</div>
					<p class="nx-cta-microcopy"><?php echo esc_html( $audit_compact_microcopy ); ?></p>
					<div class="wp-agentur-hero__proof" role="list" aria-label="Proof-Signale">
						<?php foreach ( $proof_metrics as $proof_metric ) : ?>
							<span role="listitem"><?php echo esc_html( $proof_metric['value'] . ' ' . $proof_metric['label'] ); ?></span>
						<?php endforeach; ?>
					</div>
					<figure class="wp-agentur-hero-portrait">
						<img
							src="<?php echo esc_url( hu_get_profile_image_url() ); ?>"
							alt="Haşim Üner – WordPress Growth Architect Hannover"
							loading="eager"
							fetchpriority="high"
							width="120"
							height="148"
						/>
						<figcaption>Haşim Üner · Growth Architect · Hannover</figcaption>
					</figure>
				</div>
			</div>
		</section>

		<section id="problem" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Warum viele WordPress-Seiten trotz Sichtbarkeit keine belastbaren Anfragen liefern</h2>
					<p class="wp-agentur-section-intro">Das Problem ist selten nur Design. Meist fehlt die Verbindung zwischen Angebotsseiten, sauberer Messung, Proof und dem nächsten sinnvollen Schritt.</p>
				</div>
				<div class="wp-agentur-pain-grid">
					<?php foreach ( $pain_cards as $pain_card ) : ?>
						<article class="wp-agentur-pain-card nx-card">
							<span class="wp-agentur-pain-card__icon" aria-hidden="true"><?php echo esc_html( $pain_card['icon'] ); ?></span>
							<h3><?php echo esc_html( $pain_card['title'] ); ?></h3>
							<p><?php echo wp_kses_post( $pain_card['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
				<div class="wp-agentur-solution-card">
					<span class="wp-agentur-solution-card__eyebrow">Die Lösung</span>
					<h3>Erst die Bremsen ordnen. Dann erst über Umsetzungstiefe sprechen.</h3>
					<p>Wenn Angebotsseiten, Datensignale, Proof und CTA-Führung wieder zusammenarbeiten, entsteht kein schöneres Webprojekt, sondern eine Website mit klarerem Nachfrageweg.</p>
					<ul>
						<li>klare Prioritäten statt Relaunch-Reflex</li>
						<li>belastbare Signale statt Tool-Rauschen</li>
						<li>bessere Anfrageführung auf kaufnahen Seiten</li>
					</ul>
					<p><?php echo esc_html( $canonical_ownership_sentence ); ?></p>
				</div>
			</div>
		</section>

		<section id="unterschied" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Was hier anders läuft.</h2>
				</div>
				<div class="nx-prose wp-agentur-prose">
					<div class="wp-agentur-table-wrap">
						<table class="wp-agentur-table">
							<thead>
								<tr>
									<th></th>
									<th>Agentur-Logik</th>
									<th>Growth-Architect-Logik</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Ziel</td>
									<td>Website liefern</td>
									<td>WordPress als Nachfrage-System aufbauen</td>
								</tr>
								<tr>
									<td>Lieferbild</td>
									<td>Seitenpaket und Übergabe</td>
									<td>Angebotsseiten, Datenebene, KPI-Klarheit und Weiterentwicklung</td>
								</tr>
								<tr>
									<td>Reihenfolge</td>
									<td>Design, dann später Optimierung</td>
									<td>Diagnose, Priorisierung, dann Umsetzung</td>
								</tr>
								<tr>
									<td>SEO</td>
									<td>Plugin und Grundoptimierung</td>
									<td>IA, Money Pages, Proof und Suchintention als Verbund</td>
								</tr>
								<tr>
									<td>Tracking</td>
									<td>Einrichtung für Reports</td>
									<td>Conversion-Signale und Klarheit für echte Entscheidungen</td>
								</tr>
								<tr>
									<td>Conversion</td>
									<td>CTA am Ende</td>
									<td>Argumentationsstruktur über die ganze Seite</td>
								</tr>
								<tr>
									<td>Betrieb</td>
									<td>Tool- und Plugin-Mix mit hoher Abhängigkeit</td>
									<td>Kontrollierter Stack, nachvollziehbare Änderungen, Ownership</td>
								</tr>
								<tr>
									<td>Nach Go-Live</td>
									<td>Projekt abgeschlossen</td>
									<td>Gezielte Iteration auf die größten Hebel</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>

		<section id="angebot" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Was ich für B2B-Unternehmen umsetze.</h2>
				</div>
				<div class="wp-agentur-solution-card">
					<ul>
						<?php foreach ( $service_items as $service_item ) : ?>
							<li><?php echo esc_html( $service_item ); ?></li>
						<?php endforeach; ?>
					</ul>
					<div class="wp-agentur-actions wp-agentur-actions--center">
						<a href="<?php echo esc_url( $audit_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_agentur_services_audit" data-track-category="lead_gen">Audit starten</a>
						<a href="<?php echo esc_url( $wgos_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_agentur_services_wgos" data-track-category="navigation">WGOS ansehen</a>
					</div>
				</div>
			</div>
		</section>

		<section id="fit" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Für wen ich arbeite.</h2>
				</div>
				<div class="nx-prose wp-agentur-prose">
					<p>Die Zusammenarbeit passt für B2B-Unternehmen, die WordPress bereits einsetzen oder bewusst als Kernsystem nutzen wollen und deren Website Anfragen liefern soll, nicht nur Präsenz.</p>
					<ul>
						<?php foreach ( $fit_items as $fit_item ) : ?>
							<li><?php echo esc_html( $fit_item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</section>

		<section id="wgos" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Mein Ansatz: Das WordPress Growth Operating System (WGOS)</h2>
				</div>
				<div class="nx-prose wp-agentur-prose">
					<p>WGOS ist kein Plugin und kein Seitenpaket, sondern eine Arbeitsweise: Angebotsseiten, technische SEO, Tracking, Conversion und Betrieb werden als zusammenhängendes System behandelt, nicht als isolierte Maßnahmen. Für B2B-Unternehmen heißt das: Erst Diagnose, dann Priorisierung, dann kontrollierte Umsetzung auf die größten Hebel.</p>
					<p>Jeder Baustein hat eine klare Reihenfolge und wird erst dann aktiviert, wenn das Fundament trägt. So entsteht kein Projektrisiko, sondern ein steuerbares Nachfrage-System.</p>
					<a href="<?php echo esc_url( $wgos_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_agentur_wgos_detail" data-track-category="navigation">WGOS kennenlernen</a>
				</div>
			</div>
		</section>

		<section id="leistungen" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Spezialisierte Leistungen</h2>
				</div>
				<div class="wp-agentur-spoke-grid">
					<a href="#technisches-seo" class="wp-agentur-spoke-card" data-track-action="cta_agentur_spoke_seo" data-track-category="navigation">
						<span class="wp-agentur-spoke-card__title">Technisches SEO</span>
						<span class="wp-agentur-spoke-card__desc">Crawlability, Seitenstruktur, interne Verlinkung und technische Fixes für kaufnahe Rankings.</span>
					</a>
					<a href="<?php echo esc_url( $tracking_url ); ?>" class="wp-agentur-spoke-card" data-track-action="cta_agentur_spoke_tracking" data-track-category="navigation">
						<span class="wp-agentur-spoke-card__title">GA4 Tracking Setup</span>
						<span class="wp-agentur-spoke-card__desc">GA4, GTM, Consent und serverseitige Signale für belastbare Lead- und Nachfrage-Daten.</span>
					</a>
					<a href="<?php echo esc_url( $cro_url ); ?>" class="wp-agentur-spoke-card" data-track-action="cta_agentur_spoke_cro" data-track-category="navigation">
						<span class="wp-agentur-spoke-card__title">Conversion Rate Optimierung für WordPress</span>
						<span class="wp-agentur-spoke-card__desc">Angebotsseiten, Proof, CTA-Führung und Formulare so ordnen, dass aus Besuchern Anfragen werden.</span>
					</a>
					<a href="#wordpress-wartung" class="wp-agentur-spoke-card" data-track-action="cta_agentur_spoke_wartung" data-track-category="navigation">
						<span class="wp-agentur-spoke-card__title">WordPress Wartung Hannover</span>
						<span class="wp-agentur-spoke-card__desc">Updates, Sicherheit, Backups und stabile Betriebsroutinen als Fundament für alles andere.</span>
					</a>
				</div>
			</div>
		</section>

		<section id="technisches-seo" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Technisches WordPress-SEO für Hannover und DACH</h2>
					<p class="wp-agentur-section-intro">
						Technisches SEO ist hier kein Meta-Feintuning, sondern Arbeit an Crawlability, Seitenstruktur und interner Verlinkung — priorisiert für kaufnahe Seitentypen wie Angebotsseiten, Audit-Einstiege und zentrale Proof-Seiten.
					</p>
				</div>
				<div class="nx-prose wp-agentur-prose">
					<h3>Was typischerweise klemmt</h3>
					<ul class="premium-list">
						<li><span class="check-icon">→</span><div>Kaufnahe Seiten indexieren schlecht, weil Crawlability und interne Verlinkung unklar sind.</div></li>
						<li><span class="check-icon">→</span><div>Meta-Titel und Beschreibungen treffen die Suchintention nicht — Impressionen ohne Klicks.</div></li>
						<li><span class="check-icon">→</span><div>Pillar- und Cluster-Logik fehlt: Inhalte bleiben Einzelseiten ohne Themen-Rückgrat.</div></li>
						<li><span class="check-icon">→</span><div>Schema-Markup, Core Web Vitals und Server-Tuning sind isoliert statt als ein System verzahnt.</div></li>
					</ul>

					<h3>Wie ich das als WordPress-Agentur löse</h3>
					<ul class="premium-list">
						<li><span class="check-icon">✓</span><div>Technical SEO Audit mit priorisierter Hebel-Liste statt generischer Checkliste.</div></li>
						<li><span class="check-icon">✓</span><div>Keyword-Strategie nach Suchintention und Seitentypen, nicht nach Suchvolumen.</div></li>
						<li><span class="check-icon">✓</span><div>Pillar Pages und Content Hubs mit stringenter interner Verlinkung.</div></li>
						<li><span class="check-icon">✓</span><div>On-Page-SEO: Snippets, Seitentitel und Headlines gegen die Suchintention geschärft.</div></li>
						<li><span class="check-icon">✓</span><div>Local SEO für Hannover, wenn regionale Nachfrage relevant ist.</div></li>
					</ul>
					<p class="wp-agentur-section-intro">
						Ob technisches SEO bei Ihrer Website heute den größten Hebel hat, zeigt die <a href="<?php echo esc_url( $audit_url ); ?>" data-track-action="cta_agentur_seo_audit" data-track-category="lead_gen">System-Diagnose</a> in ca. 60 Sekunden.
					</p>
				</div>
			</div>
		</section>

		<section id="wordpress-wartung" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">WordPress Wartung und Wartungsvertrag in Hannover</h2>
					<p class="wp-agentur-section-intro">
						Wartung ist hier kein Ticket-System, sondern der Betriebsblock aus Updates, Sicherheit, Backups, Performance und klaren Rollback-Prozessen — Fundament für alles, was darauf an SEO, Conversion und bezahlter Nachfrage aufbaut.
					</p>
				</div>
				<div class="nx-prose wp-agentur-prose">
					<h3>Was ein belastbarer WordPress-Wartungsvertrag abdeckt</h3>
					<ul class="premium-list">
						<li><span class="check-icon">✓</span><div>Security Hardening: Zugriffe, Rechte und Wiederherstellungswege abgesichert.</div></li>
						<li><span class="check-icon">✓</span><div>Update-Management: planbar, testbar, rollback-fähig — kein Ad-hoc-Risiko.</div></li>
						<li><span class="check-icon">✓</span><div>Backups und Monitoring mit verifizierter Wiederherstellung, nicht nur Dump-Dateien.</div></li>
						<li><span class="check-icon">✓</span><div>Plugin-Audit: Reduktion von Wartungslast und Konflikten.</div></li>
						<li><span class="check-icon">✓</span><div>Performance-Diagnose und bei Bedarf Server-Tuning.</div></li>
					</ul>

					<h3>Warum das kein generischer Wartungsvertrag ist</h3>
					<ul class="premium-list">
						<li><span class="check-icon">→</span><div>Direkter Ansprechpartner statt Support-Pipeline.</div></li>
						<li><span class="check-icon">→</span><div>Wartung als Fundament — erst stabiler Betrieb, dann SEO, Conversion und Ads.</div></li>
						<li><span class="check-icon">→</span><div>Für Unternehmen mit relevanter Website und regelmäßigem Traffic, nicht für Low-Traffic-Projekte.</div></li>
					</ul>
					<p class="wp-agentur-section-intro">
						Wartungsvertrag anfragen: über den <a href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_agentur_wartung_contact" data-track-category="lead_gen">Kontaktpfad</a> oder direkt in der <a href="<?php echo esc_url( $audit_url ); ?>" data-track-action="cta_agentur_wartung_audit" data-track-category="lead_gen">System-Diagnose</a> einordnen.
					</p>
				</div>
			</div>
		</section>

		<section id="case-study" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">WordPress Agentur Hannover: Was passiert, wenn die Reihenfolge stimmt</h2>
					<p class="wp-agentur-section-intro">Kurzer Blick auf die gleiche Logik in der Praxis: Ausgangslage, Eingriff, Ergebnis und nächster sinnvoller Vertiefungsschritt.</p>
				</div>
				<div class="wp-agentur-case-grid">
					<?php foreach ( $case_teaser_cards as $case_teaser_card ) : ?>
						<article class="wp-agentur-case-card">
							<span class="wp-agentur-case-card__eyebrow"><?php echo esc_html( $case_teaser_card['eyebrow'] ); ?></span>
							<h3><?php echo esc_html( $case_teaser_card['title'] ); ?></h3>
							<p><?php echo esc_html( $case_teaser_card['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
					<article class="wp-agentur-case-card wp-agentur-case-card--result">
						<span class="wp-agentur-case-card__eyebrow">Ergebnis</span>
						<h3>Mehr Wirkung aus denselben Kanälen</h3>
						<div class="wp-agentur-case-card__metrics" role="list" aria-label="Case Kennzahlen">
							<div role="listitem">
								<strong><?php echo esc_html( $e3_cpl_reduction ); ?></strong>
								<span>CPL</span>
							</div>
							<div role="listitem">
								<strong><?php echo esc_html( $e3_lead_count ); ?></strong>
								<span>Leads im System</span>
							</div>
							<div role="listitem">
								<strong><?php echo esc_html( $e3_sales_conversion ); ?></strong>
								<span>Sales-Conversion</span>
							</div>
						</div>
					</article>
					<article class="wp-agentur-case-card wp-agentur-case-card--cta">
						<span class="wp-agentur-case-card__eyebrow">CTA</span>
						<h3>Die Fallstudie im Detail lesen</h3>
						<p>Wenn Sie sehen wollen, wie Reihenfolge, Tracking und Conversion-Pfad zusammengewirkt haben, gehen Sie in die offene E3 Case Study.</p>
						<a href="<?php echo esc_url( $e3_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_agentur_case_e3" data-track-category="trust">Case Study lesen</a>
						<p class="wp-agentur-case-card__support"><a href="<?php echo esc_url( $cases_url ); ?>" data-track-action="cta_agentur_case_results" data-track-category="trust">Weitere Ergebnisse ansehen</a></p>
					</article>
				</div>
			</div>
		</section>

		<section id="hannover" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Standort Hannover. Arbeitsgebiet DACH.</h2>
				</div>
				<p class="wp-agentur-location-note">Persönliche Termine, Workshops und Reviews sind in Hannover, Pattensen und der Region Hannover jederzeit möglich. Die Zusammenarbeit funktioniert genauso sauber remote.</p>
				<p class="wp-agentur-location-note"><strong>Standort:</strong> Pattensen bei Hannover. Das hilft für lokale Abstimmung, ohne die Arbeit künstlich auf Stadtgrenzen zu begrenzen.</p>
			</div>
		</section>

		<section id="faq" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Häufige Fragen</h2>
				</div>
				<div class="nx-faq wp-faq">
					<?php foreach ( $faq_items as $index => $item ) : ?>
						<details class="nx-faq__item"<?php echo 0 === $index ? ' open' : ''; ?>>
							<summary><?php echo esc_html( $item['question'] ); ?></summary>
							<div class="nx-faq__content"><?php echo esc_html( $item['answer'] ); ?></div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section id="cta-final" class="nx-section">
			<div class="nx-container">
				<div class="nx-cta-box wp-agentur-cta-box">
					<h2>Prüfen wir, an welcher Stelle Ihr WordPress-System heute Nachfrage verliert.</h2>
					<p>Die System-Diagnose zeigt, ob Angebotsseiten, Datenlage, CTA-Führung oder technische Reibung zuerst angegangen werden sollten und ob ein tieferer Umbau überhaupt sinnvoll ist.</p>
					<a href="<?php echo esc_url( $audit_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_agentur_final_audit" data-track-category="lead_gen"><?php echo esc_html( $audit_cta_label ); ?></a>
					<p class="wp-cta-desc mt-1">Kein Pitch. Klare Priorisierung. Wenn fachlich sinnvoll, kann daraus als nächster Schritt eine vertiefte Analyse, eine fokussierte Korrektur oder eine laufende Weiterentwicklung entstehen.</p>
					<p class="wp-cta-desc mb-0">
						<a href="<?php echo esc_url( $about_url ); ?>" data-track-action="cta_agentur_final_about" data-track-category="navigation">Mehr über meine Arbeitsweise</a>
						<span aria-hidden="true"> · </span>
						Wenn der Scope schon klar ist:
						<a href="<?php echo esc_url( $implementation_contact_url ); ?>" data-track-action="cta_agentur_final_contact" data-track-category="navigation">direkt Kontakt</a>
						<span aria-hidden="true"> · </span>
						Für Betrieb und Stabilisierung:
						<a href="<?php echo esc_url( $wartung_url ); ?>" data-track-action="cta_agentur_final_wartung" data-track-category="navigation">WordPress Wartung Hannover</a>
					</p>
				</div>
			</div>
		</section>

	</div>
</main>

<?php
$current_permalink = get_permalink();
$faq_schema        = [
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => trailingslashit( $current_permalink ) . '#faq',
	'url'        => $current_permalink,
	'inLanguage' => 'de',
	'publisher'  => [ '@id' => home_url( '/#organization' ) ],
	'mainEntity' => array_map(
		static function ( $item ) {
			return [
				'@type'          => 'Question',
				'name'           => $item['question'],
				'acceptedAnswer' => [
					'@type' => 'Answer',
					'text'  => $item['answer'],
				],
			];
		},
		$faq_items
	),
];
?>
<script type="application/ld+json">
<?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
</script>

<?php
get_footer();
