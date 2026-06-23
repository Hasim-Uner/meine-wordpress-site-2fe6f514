<?php
/**
 * Template Name: Über Mich (Editorial)
 * Description: Biografisch-editoriale Über-Mich-Seite mit CRO-Rückgrat.
 *              Outcome-Hero + früher CTA + E3-Proof above the fold, biografischer
 *              Pfad als Kompetenz-Beleg, Manifest, Fit-Qualifizierung, interner
 *              Experten-Cluster (E-E-A-T), Founding-Cohort-Band, mobiler Sticky-CTA.
 *
 * Parallel zu template-about.php. Auswahl pro Page im WP-Admin.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$request_url   = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$request_cta   = function_exists( 'nexus_get_primary_request_cta_label' ) ? nexus_get_primary_request_cta_label() : 'Marktcheck mit Fit-Entscheid starten';
$portrait_url  = function_exists( 'hu_get_profile_image_url' ) ? hu_get_profile_image_url() : get_stylesheet_directory_uri() . '/assets/img/hasim-portrait.png';
$portrait_path = get_stylesheet_directory() . '/assets/img/hasim-portrait.png';

// ── E3-Canon (Proof immer aus der zentralen Quelle, nie hardcodet) ──
$e3_canon     = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics   = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_cpl_before = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after  = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_cpl_red    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_leads      = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_conv       = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_timeframe  = $e3_metrics['timeframe']['display'] ?? '6 Monate';
$e3_tf_dative  = $e3_metrics['timeframe']['display_dative'] ?? '6 Monaten';

// ── Founding-Canon (für das native Cohort-Band) ──
$founding        = function_exists( 'hu_founding_canon' ) ? hu_founding_canon() : [];
$f_label         = (string) ( $founding['label'] ?? 'Founding Cohort 2026' );
$f_total         = max( 1, (int) ( $founding['slots_total'] ?? 3 ) );
$f_remaining     = max( 0, min( $f_total, (int) ( $founding['slots_remaining'] ?? $f_total ) ) );
$f_end_timestamp = strtotime( (string) ( $founding['end_date'] ?? '2026-09-30' ) );
$f_end_label     = $f_end_timestamp ? date_i18n( 'd.m.Y', $f_end_timestamp ) : (string) ( $founding['end_date'] ?? '' );
$f_slot_line     = sprintf( '%1$d von %2$d Plätzen offen', $f_remaining, $f_total );

// Hero-Proof-Zeile (ein Trust-Signal above the fold).
$hero_proof_line = sprintf(
	'E3 New Energy: %1$s → %2$s pro Anfrage · %3$s qualifizierte Anfragen · %4$s',
	$e3_cpl_before,
	$e3_cpl_after,
	$e3_leads,
	$e3_timeframe
);

// Proof-Strip (kompaktes E3-Band früh auf der Seite).
$about_proof = [
	[ 'k' => sprintf( '%s → %s', $e3_cpl_before, $e3_cpl_after ), 'l' => 'Kosten pro Anfrage (vorher gekaufte Portal-Leads → eigenes System)' ],
	[ 'k' => $e3_leads,    'l' => 'qualifizierte Anfragen im E3-Case' ],
	[ 'k' => $e3_conv,     'l' => 'Abschlussquote (vorher 1 – 5 %)' ],
	[ 'k' => $e3_timeframe, 'l' => 'Zeitraum bis zum belastbaren Ergebnis' ],
];

// Biografischer Pfad — Herkunft als Kompetenz-Beleg, nicht als weiche Story.
$about_hero_path = [
	[
		't' => 'Die Vertriebs-Schule',
		's' => 'Aufgewachsen im – bis heute aktiven – Bauunternehmen des Vaters. Danach acht Jahre B2B-Beratung am echten Entscheider: Bedarf erkennen, Einwände auflösen, verbindlich abschließen. Deshalb baue ich Anfragewege aus echten Verkaufsgesprächen, nicht aus Designgeschmack.',
	],
	[
		't' => 'Der analytische Blick',
		's' => 'Medienwissenschaft als Werkzeugkasten, nicht als Elfenbeinturm. Ich sehe, wo Daten fließen, wo Aufmerksamkeit im Funnel versickert und welches Signal über Vertrauen oder Wegklicken entscheidet.',
	],
	[
		't' => 'Der eigene Beweis',
		's' => 'Eigenen Shop von Null aufgebaut und am eigenen Geld gespürt, was Plattform-Abhängigkeit kostet. Dieselbe Logik auf Energie skaliert – und bei E3 New Energy belegt.',
	],
];

// Die drei Grundüberzeugungen (Manifest).
$about_work_principles = [
	[
		'eyebrow' => 'Überzeugung I',
		'title'   => 'Code muss verkaufen, nicht informieren.',
		'body'    => 'Die meisten Websites sind digitale Hochglanz-Prospekte. Wer selbst aus dem Vertrieb kommt, weiß: Eine B2B-Website hat eine Aufgabe — Abschlüsse vorzubereiten. Ein eigenes Anfrage-System ist kein Informationsfriedhof, sondern ein scharf kalkulierter Filter, der unqualifizierte Anfragen abweist und kaufbereite Entscheider isoliert.',
		'detail'  => 'Wer acht Jahre im B2B-Vertrieb stand, weiß: Wenn die technische Struktur die Sprache des Vertriebs nicht spricht, ist sie wertlos.',
	],
	[
		'eyebrow' => 'Überzeugung II',
		'title'   => 'Mieten ist teuer. Eigentum ist planbar.',
		'body'    => 'Im Bau- und Energiesektor gilt: Wer Maschinen, Hallen und Grundstücke nur mietet, macht sich erpressbar. Beim wichtigsten Gut — den Kundenanfragen — tun viele Betriebe genau das. Wer Leads bei Portalen kauft, füttert fremde Plattformen, teilt sich Kontakte mit Mitbewerbern und steht unter dauerhaftem Margendruck.',
		'detail'  => 'Ich habe das nicht nur beobachtet — ich habe meinen eigenen Shop aufgebaut und am eigenen Umsatz gespürt, was es heißt, die eigene Nachfrage zu besitzen statt zu mieten. Eigentum schlägt Miete. Auch im Vertrieb.',
	],
	[
		'eyebrow' => 'Überzeugung III',
		'title'   => 'Daten-Integrität schlägt Bauchgefühl.',
		'body'    => 'Wenn Tracking nur Klicks statt unterschriebene Werkverträge misst, verbrennt Werbebudget auf den falschen Kanälen. Erst wenn serverseitiges Tracking und CRM nahtlos verschmelzen, sehen die Werbekanäle, wo der echte Profit liegt — und optimieren auf Umsatz statt auf Klicks, die niemand bezahlt hat.',
		'detail'  => 'Saubere Datenarchitektur ist der einzige Hebel für planbare Skalierung.',
	],
];

// Fit-Check: drei Voraussetzungen, damit schwache Leads gar nicht erst anfragen.
$about_fit_points = [
	[
		't' => 'Fokus auf Solar, Wärmepumpe oder Speicher.',
		's' => 'Echte Spezialisierung. Keine branchenübergreifenden Experimente, keine Generalisten-Lösungen.',
	],
	[
		't' => 'Eigener, funktionierender Vertrieb.',
		's' => 'Das System erzeugt exklusive, hochpreisige Anfragen. Die müssen konsequent bearbeitet werden — durch ein Vertriebsteam oder eine abschlussstarke Geschäftsführung, nicht durch reine Vermittlung.',
	],
	[
		't' => 'Verständnis für Infrastruktur statt Landingpage.',
		's' => 'Ein eigenes Anfrage-System ist ein digitaler Vermögenswert. Wer eine günstige Landingpage sucht, ist hier falsch.',
	],
];

// Fachliche Schwerpunkte: 3 System-Ebenen, 6 interne Seiten (E-E-A-T + interne Verlinkung).
$about_expertise_structured = [
	'Infrastruktur & Funnel' => [
		[
			't'   => 'Lead-Funnel-Architektur',
			's'   => 'Fünf Stufen vom Suchwort bis zum Auftrag, mit Vorqualifizierung und Sales-Anschluss.',
			'url' => home_url( '/lead-funnel-solar/' ),
		],
		[
			't'   => 'Portal-Leads vs. eigenes System',
			's'   => '24-Monats-TCO, Exklusivität und Asset-Eigentum im strategischen Vergleich.',
			'url' => home_url( '/eigene-leadgenerierung-vs-portale/' ),
		],
	],
	'Daten & Tracking' => [
		[
			't'   => 'Server-Side Tracking',
			's'   => 'GA4, Meta CAPI und Consent Mode v2 auf eigenem Server – DSGVO-konform und cookieless-ready.',
			'url' => home_url( '/server-side-tracking-b2b/' ),
		],
		[
			't'   => 'Cost per Lead Photovoltaik',
			's'   => 'CPL-Analyse mit drei Szenarien-Vergleich und versteckten Kostentreibern.',
			'url' => home_url( '/cost-per-lead-photovoltaik/' ),
		],
	],
	'Qualität & Vertrieb' => [
		[
			't'   => 'Qualifizierte PV-Anfragen',
			's'   => 'Vier Merkmale, an denen sich eine hochwertige Solar-Anfrage erkennen lässt.',
			'url' => home_url( '/qualifizierte-pv-anfragen/' ),
		],
		[
			't'   => 'B2B Solar Leads (Gewerbe)',
			's'   => 'Buying-Center-Funnel für gewerbliche Photovoltaik-Projekte ab 50.000 €.',
			'url' => home_url( '/b2b-solar-leads/' ),
		],
	],
];

get_header();
?>

<main id="main" class="site-main">
	<div class="about-editorial" data-track-section="about_editorial_page">
		<div class="about-editorial__inner">

			<!-- HERO — Outcome + früher CTA + ein Proof-Signal -->
			<header class="about-editorial__hero">
				<span class="about-editorial__kicker">Architekt für eigene Anfrage-Systeme</span>
				<h1 class="about-editorial__h1">Ich beende Portal-Abhängigkeit — mit Anfrage-Systemen, die Ihrem Betrieb gehören.</h1>
				<p class="about-editorial__lead">
					Für inhabergeführte Solar- und Wärmepumpen-Betriebe, die geteilte Portal-Leads durch eigene Daten, saubere Vorqualifizierung und direkten CRM-Anschluss ersetzen wollen.
				</p>

				<div class="about-editorial__hero-actions">
					<a
						href="<?php echo esc_url( $request_url ); ?>"
						class="about-editorial__cta-btn"
						data-track-action="cta_about_editorial_hero"
						data-track-category="lead_gen"
						data-track-section="about_editorial_hero"
					>
						<?php echo esc_html( $request_cta ); ?>
					</a>
					<p class="about-editorial__cta-meta about-editorial__cta-meta--start">
						<span>Exklusive Erst-Analyse</span>
						<span>Prüfung auf Regions-Verfügbarkeit</span>
						<span>Händischer Befund innerhalb von 48 Stunden</span>
					</p>
				</div>

				<p class="about-editorial__hero-proof"><?php echo esc_html( $hero_proof_line ); ?></p>
			</header>

			<!-- PROOF-STRIP — E3-Canon früh, nicht erst am Seitenende -->
			<section class="about-editorial__proof" aria-label="Belege aus dem E3-Case">
				<?php foreach ( $about_proof as $proof_item ) : ?>
					<div class="about-editorial__proof-item">
						<span class="about-editorial__proof-k"><?php echo esc_html( $proof_item['k'] ); ?></span>
						<span class="about-editorial__proof-l"><?php echo esc_html( $proof_item['l'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</section>

			<!-- BIOGRAFISCHER PFAD + PORTRAIT-CARD -->
			<section class="about-editorial__split">
				<div class="about-editorial__path-col">
					<h2 class="about-editorial__section-kicker">Warum ich Anfrage-Systeme baue</h2>
					<ol class="about-editorial__path" role="list">
						<?php foreach ( $about_hero_path as $index => $step ) : ?>
							<li class="about-editorial__path-item">
								<span class="about-editorial__path-num" aria-hidden="true">0<?php echo (int) ( $index + 1 ); ?></span>
								<div class="about-editorial__path-body">
									<h3 class="about-editorial__path-title"><?php echo esc_html( $step['t'] ); ?></h3>
									<p class="about-editorial__path-text"><?php echo esc_html( $step['s'] ); ?></p>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>

				<aside class="about-editorial__portrait-card">
					<?php if ( file_exists( $portrait_path ) ) : ?>
						<img
							src="<?php echo esc_url( $portrait_url ); ?>"
							alt="Porträt von Haşim Üner"
							class="about-editorial__portrait"
							loading="lazy"
							width="340"
							height="420"
						/>
					<?php endif; ?>
					<dl class="about-editorial__meta">
						<div class="about-editorial__meta-row">
							<dt>Status</dt>
							<dd>Aktiv · Pattensen bei Hannover</dd>
						</div>
						<div class="about-editorial__meta-row">
							<dt>Fokus</dt>
							<dd>Solar &amp; Wärmepumpe</dd>
						</div>
						<div class="about-editorial__meta-row">
							<dt>Methode</dt>
							<dd>Eigenes Anfrage-System</dd>
						</div>
					</dl>
				</aside>
			</section>

			<!-- MANIFEST -->
			<section class="about-editorial__manifest" aria-labelledby="about-editorial-manifest-title">
				<h2 id="about-editorial-manifest-title" class="about-editorial__section-kicker about-editorial__section-kicker--center">
					Das Unternehmer-Manifest
				</h2>
				<div class="about-editorial__manifest-grid">
					<?php foreach ( $about_work_principles as $principle ) : ?>
						<article class="about-editorial__principle">
							<span class="about-editorial__principle-eyebrow"><?php echo esc_html( $principle['eyebrow'] ); ?></span>
							<h3 class="about-editorial__principle-title"><?php echo esc_html( $principle['title'] ); ?></h3>
							<p class="about-editorial__principle-body"><?php echo esc_html( $principle['body'] ); ?></p>
							<p class="about-editorial__principle-detail"><?php echo esc_html( $principle['detail'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</section>

			<!-- BIOGRAFISCHE ERZÄHLUNG -->
			<section class="about-editorial__bio" id="about-editorial-hintergrund">
				<h2 class="about-editorial__section-kicker">Der Hintergrund</h2>
				<div class="about-editorial__bio-prose">
					<p class="about-editorial__dropcap">Mein Vater ist Bauunternehmer. Ich bin mit der Verantwortung für Projekte, Margen und ein fest angestelltes Team aufgewachsen — und mit dem Wissen, was eine schlechte Anfrage einen Betrieb wirklich kostet.</p>
					<p>Vertrieb habe ich danach nicht in Seminaren gelernt, sondern in acht Jahren B2B-Beratung: Bedarf strukturiert aufnehmen, mit Entscheidern verbindlich kommunizieren, langfristige Beziehungen über zuverlässige Follow-ups halten.</p>
					<p>Dann habe ich selbst gegründet — einen eigenen Online-Shop, von der ersten Zeile Code bis zur letzten Conversion. Dort habe ich am eigenen Geld erlebt, was es bedeutet, seine Nachfrage selbst zu erzeugen, statt sie von Plattformen zu mieten. Genau diese Erfahrung ist der Kern dessen, was ich heute baue.</p>
					<p>Mein Studium der Medienwissenschaft an der Universität Paderborn war dabei der analytische Werkzeugkasten: Ich schaue nicht darauf, was auf einer Website schön aussieht, sondern wie Daten fließen, wo Aufmerksamkeit versickert und welche Signale zwischen Oberfläche und B2B-Entscheider übertragen werden müssen, damit Vertrauen entsteht.</p>
					<p>Die meisten WordPress-Websites scheitern, weil sie von Designern gebaut wurden, die nie ein echtes Verkaufsgespräch geführt haben. Sie informieren den Nutzer zu Tode, statt Entscheidungen zu provozieren. Ich verbinde acht Jahre Vertriebs-Pragmatismus mit analytischer Präzision — und seit dem Case bei E3 New Energy (<?php echo esc_html( sprintf( '%1$s weniger Kosten pro Anfrage, %2$s qualifizierte Anfragen in %3$s', $e3_cpl_red, $e3_leads, $e3_tf_dative ) ); ?>) wende ich diese Methode exklusiv auf den Solar- und Wärmepumpen-Mittelstand an.</p>
				</div>
			</section>

			<!-- FIT / QUALIFIZIERUNG -->
			<section class="about-editorial__fit" aria-labelledby="about-editorial-fit-title">
				<div class="about-editorial__fit-head">
					<h2 id="about-editorial-fit-title" class="about-editorial__section-kicker">Wann eine Zusammenarbeit Sinn ergibt</h2>
					<p class="about-editorial__fit-intro">Nicht jeder Betrieb braucht sofort ein eigenes Anfrage-System. Drei Dinge müssen stimmen — sonst spare ich Ihnen und mir die Zeit.</p>
				</div>
				<ul class="about-editorial__fit-list" role="list">
					<?php foreach ( $about_fit_points as $fit_item ) : ?>
						<li class="about-editorial__fit-item">
							<span class="about-editorial__fit-icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7" /></svg>
							</span>
							<div class="about-editorial__fit-body">
								<strong><?php echo esc_html( $fit_item['t'] ); ?></strong>
								<span><?php echo esc_html( $fit_item['s'] ); ?></span>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>

			<!-- FACHLICHE SCHWERPUNKTE — interner Link-Cluster (E-E-A-T) -->
			<section class="about-editorial__expertise" aria-labelledby="about-editorial-expertise-title">
				<div class="about-editorial__expertise-head">
					<h2 id="about-editorial-expertise-title" class="about-editorial__section-kicker">Fachliche Schwerpunkte</h2>
					<p class="about-editorial__fit-intro">Sechs Felder entlang der drei System-Ebenen — jedes als eigene Seite mit Methode, Beispielen und Bezug zum E3-Case.</p>
				</div>
				<div class="about-editorial__expertise-cluster">
					<?php foreach ( $about_expertise_structured as $expertise_category => $expertise_items ) : ?>
						<div class="about-editorial__expertise-column">
							<h3 class="about-editorial__expertise-title"><?php echo esc_html( $expertise_category ); ?></h3>
							<ul class="about-editorial__expertise-list" role="list">
								<?php foreach ( $expertise_items as $field ) : ?>
									<li>
										<a
											class="about-editorial__expertise-link"
											href="<?php echo esc_url( $field['url'] ); ?>"
											data-track-action="cta_about_editorial_expertise"
											data-track-category="navigation"
											data-track-section="about_editorial_expertise"
										>
											<span class="about-editorial__expertise-link-t"><?php echo esc_html( $field['t'] ); ?></span>
											<span class="about-editorial__expertise-link-s"><?php echo esc_html( $field['s'] ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endforeach; ?>
				</div>
			</section>

			<!-- FOUNDING-COHORT-BAND — Scarcity + Offer-Frame (editorial-nativ) -->
			<section class="about-editorial__cohort" aria-labelledby="about-editorial-cohort-title">
				<span class="about-editorial__cta-eyebrow"><?php echo esc_html( $f_label ); ?></span>
				<h2 id="about-editorial-cohort-title" class="about-editorial__cohort-title">E3 New Energy war der erste Case, nicht die Grenze.</h2>
				<p class="about-editorial__cohort-text">
					Dieselbe Arbeitsweise öffne ich für maximal drei passende Solar- oder Wärmepumpen-Betriebe. Der Einstieg bleibt der Marktcheck, damit vor einer Umsetzung klar ist, ob Markt, Budget und Tracking-Realität zusammenpassen.
				</p>
				<p class="about-editorial__cohort-status">
					<span class="about-editorial__cohort-dot" aria-hidden="true"></span>
					<?php echo esc_html( $f_slot_line ); ?><?php if ( '' !== $f_end_label ) : ?> · Founding-Konditionen bis <?php echo esc_html( $f_end_label ); ?><?php endif; ?>
				</p>
			</section>

			<!-- FINAL CTA -->
			<footer class="about-editorial__cta-card">
				<span class="about-editorial__cta-eyebrow">Exklusiver Marktcheck</span>
				<h2 class="about-editorial__cta-title">Bereit für ein eigenes Anfrage-System?</h2>
				<p class="about-editorial__cta-text">
					Wir analysieren, wie viel Werbebudget aktuell in Portal-Lücken versickert und wie ein eigenes Anfrage-System für Ihren Betrieb aussehen muss. Manueller, tiefer Marktcheck, händische Prüfung der Regions-Verfügbarkeit, Befund innerhalb von 48 Stunden per E-Mail. Kein Verkaufsgespräch.
				</p>
				<a
					href="<?php echo esc_url( $request_url ); ?>"
					class="about-editorial__cta-btn"
					data-track-action="cta_about_editorial_marktcheck"
					data-track-category="lead_gen"
					data-track-section="about_editorial_cta"
				>
					<?php echo esc_html( $request_cta ); ?>
				</a>
				<p class="about-editorial__cta-meta">
					<span>Exklusive Erst-Analyse</span>
					<span>Prüfung auf Regions-Verfügbarkeit</span>
					<span>Händischer Befund innerhalb von 48 Stunden</span>
				</p>
			</footer>

		</div>
	</div>

	<?php
	get_template_part(
		'template-parts/seo-subpage-sticky-cta',
		null,
		[
			'marktcheck_url' => $request_url,
			'track_category' => 'about_editorial_sticky',
		]
	);
	?>
</main>

<?php
get_footer();
