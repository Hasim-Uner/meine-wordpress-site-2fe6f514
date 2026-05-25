<?php
/**
 * Template Name: Stack Solar
 * Description: Performance-Stack für Solar-/SHK-Anbieter und Performance-Marketer.
 *              Frontend, Managed Hosting (HostPress als Partnerempfehlung mit
 *              transparenter Werbekennzeichnung), Server-Side Tracking, CRM und
 *              Marktcheck-Vorqualifizierung in fünf Schichten. Closed-Loop-Block
 *              erklärt den überproportionalen Compound-Effekt aus SST + Marktcheck.
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
$stack_agentur_url = home_url( '/stack-agentur/' );

// ── Affiliate ─────────────────────────────────────────────────
$hostpress_url        = function_exists( 'hu_get_affiliate_url' ) ? hu_get_affiliate_url( 'hostpress' ) : 'https://www.hostpress.de/wordpress-hosting/';
$hostpress_link_attrs = function_exists( 'hu_render_affiliate_anchor_attrs' ) ? hu_render_affiliate_anchor_attrs( 'hostpress', 'stack_solar_hosting' ) : ' rel="sponsored nofollow noopener" target="_blank"';

// ── Inhalte: 5 Schichten ─────────────────────────────────────
$layers = [
	[
		't' => 'Frontend & Page Speed',
		's' => 'WordPress ohne Page-Builder, Blocksy-Child-Theme, Vanilla JS, conditional Asset-Loading pro Template. Diese Seite läuft selbst auf diesem Setup — TTFB im Ziel unter 200 ms, LCP unter 1,5 s. Das ist die Schwelle, ab der Google Ads den Qualitätsfaktor spürbar belohnt: niedrigerer CPC bei gleichem Gebot, mehr Klicks pro €.',
	],
	[
		't' => 'Managed Hosting (DE)',
		's' => 'Managed WordPress Hosting bei HostPress: deutsche Serverstandorte, NVMe-Storage, Redis-Cache, CDN, tägliche Backups. Liefert dieselbe First-Party-Domain, die Layer 03 für Server-Side Tracking braucht — eine technische Voraussetzung, keine reine Hosting-Frage.',
	],
	[
		't' => 'Server-Side Tracking',
		's' => 'GTM Server-Side-Container, GA4 Measurement Protocol, Meta CAPI, Consent Mode v2 — alles auf eigener First-Party-Domain. Effekt aus eigenen Projekten: Match-Rate der Meta CAPI verdoppelt sich typischerweise von 40–60 % auf 80–90 %. Das heißt nicht, dass Kunden plötzlich doppelt so oft kaufen — sondern dass das Werbekonto endlich sieht, was tatsächlich passiert ist. Folge zweiter Ordnung: Algorithmen optimieren auf echte Daten, der CPL sinkt nachhaltig.',
	],
	[
		't' => 'CRM-Anbindung',
		's' => 'Webhook vom Server ins CRM (HubSpot, Bitrix24, Pipedrive, Brevo) inkl. Lead-Score, Quelle und Kampagne. Time-to-Lead in Sekunden statt Stunden — der Vertrieb ruft an, solange der Interessent das Formular-Tab noch offen hat. Keine Excel-Übergaben, keine verlorenen Anfragen.',
	],
	[
		't' => 'Vorqualifizierung im Funnel',
		's' => 'Marktcheck-Funnel filtert Mieter, Falschadressen, Liefergebiet-Misses und „nur-mal-gucken"-Anfragen vor dem Vertriebskontakt. SQL-Quote in Solar-Projekten typischerweise von branchenüblichen 20–35 % auf 60–80 %. Das Vertriebsteam telefoniert nur noch mit Hauseigentümern, die wirklich bauen wollen — gleicher Werbe-Spend, deutlich mehr Abschlüsse.',
	],
];

// ── Beweis-Block: Zahlen ──────────────────────────────────────
$proof_metrics = [
	[
		't' => 'CPL pro qualifiziertem Lead',
		's' => '40–60 % günstiger im typischen Fall. Im voll ausgereiften Closed-Loop-Setup bis zu ×4–×5 effizienter gegenüber dem Vorher-Setup mit clientseitigem Tracking.',
		'b' => 'Branchenschnitt 80–150 € pro Solar-Lead, je nach Region.',
	],
	[
		't' => 'Match-Rate Meta CAPI',
		's' => 'Verdopplung von typisch 40–60 % auf 80–90 % nach Wechsel von clientseitigem auf serverseitiges Tracking.',
		'b' => 'Effekt: Algorithmen sehen die echten Conversions, nicht nur die clientseitig durchgekommenen.',
	],
	[
		't' => 'SQL-Quote nach Marktcheck',
		's' => '×2 bis ×3 — von branchenüblichen 20–35 % auf 60–80 % qualifizierte Anfragen pro 100 Leads.',
		'b' => 'Mieter, Falschadressen und „nur-mal-gucken"-Anfragen werden vor dem Vertriebskontakt gefiltert.',
	],
	[
		't' => 'TTFB',
		's' => 'Im Ziel unter 200 ms. Diese Seite läuft selbst auf dem Stack — messbar via WebPageTest.',
		'b' => 'Voraussetzung für Google-Ads-Qualitätsfaktor und mobile Vorqualifizierung.',
	],
	[
		't' => 'Time-to-Lead',
		's' => 'In Sekunden statt Stunden. Webhook direkt vom GTM-Server ins CRM, kein Plugin-Umweg, kein Mail-Polling.',
		'b' => 'Vertrieb meldet sich, solange der Interessent das Tab noch offen hat.',
	],
];

// ── FAQ ───────────────────────────────────────────────────────
$faq = [
	[
		'question' => 'Wie ist „CPL 40–60 % günstiger" gemessen?',
		'answer'   => 'Vergleichswert ist der durchschnittliche CPL des Kunden im selben Werbekonto vor dem Stack-Wechsel — gleicher Markt, gleiche Kampagnen, vergleichbares Budget. Der Branchenschnitt von 80–150 € basiert auf Erfahrungswerten aus Solar-/SHK-Performance-Marketing und variiert je nach Region und Saison. Erfahrungswerte aus eigenen Kundenprojekten, Detaildaten auf Anfrage.',
	],
	[
		'question' => 'Verdoppeln sich die Conversions wirklich, oder nur die Messung?',
		'answer'   => 'Die Käufe verdoppeln sich nicht — die im Werbekonto messbaren Conversions verdoppeln sich. Vorher fehlen typischerweise 30–50 % der Conversions wegen ITP, Ad-Blockern und Consent-Ablehnung. Server-Side Tracking holt sie zurück. Folge zweiter Ordnung: die Algorithmen lernen besser, der echte CPL pro qualifiziertem Lead sinkt nachhaltig.',
	],
	[
		'question' => 'Warum wirkt der Compound-Effekt überproportional (bis zu ×4–×5)?',
		'answer'   => 'Weil der Marktcheck definiert, was als Conversion zählt — und das Server-Side Tracking genau diese gefilterte Conversion zurück ans Werbekonto spielt. Meta und Google trainieren ihre Algorithmen dann nicht mehr auf „irgendwer, der das Formular ausgefüllt hat", sondern auf den tatsächlichen Käufer-Avatar: Hauseigentümer im Liefergebiet mit Bedarf und Budget. Lookalike-Audiences werden scharf, Optimization-Pools lernen die Richtigen. Jeder neue qualifizierte Lead schärft das Modell weiter — die Kosten pro qualifiziertem Lead sinken nicht linear, sondern in einer Kurve.',
	],
	[
		'question' => 'Was, wenn mein Setup andere Werte liefert?',
		'answer'   => 'Möglich. Schlechtes Creative kompensiert kein Stack. Schlechte Zielgruppen-Definition auch nicht. Der Stack liefert die Infrastruktur, damit Marketing-Optimierung überhaupt funktioniert — er ersetzt kein Handwerk vor dem Klick.',
	],
	[
		'question' => 'Ist die HostPress-Empfehlung ein Affiliate-Link?',
		'answer'   => 'Ja. Bei einem Abschluss über meinen Link entsteht eine Vergütung — ohne Mehrkosten für dich. Die Empfehlung basiert auf eigenem Einsatz im Stack und auf Eignung für die Zielgruppe. Wäre HostPress technisch ungeeignet, würde es hier nicht stehen — egal welche Vergütung.',
	],
	[
		'question' => 'Ich bin Agentur, nicht Solar-Anbieter — welcher Stack passt dann?',
		'answer'   => 'Für Agenturen, technische Inhaber und CTOs mit eigenem Dev-Team gibt es einen separaten Dev-Stack auf eigenem Root-Server mit Git-Deployment, Restricted-Shell und Multi-Site-Hosting für Care-Plans. Details auf der Stack-Agentur-Seite.',
	],
];

// ── Schema.org: TechArticle + FAQPage ────────────────────────
$author_person = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];

$tech_article_schema = [
	'@context'         => 'https://schema.org',
	'@type'            => 'TechArticle',
	'@id'              => trailingslashit( $page_url ) . '#article',
	'headline'         => 'Stack für Solar-/SHK-Anfrage-Systeme: Performance, Tracking und Vorqualifizierung',
	'description'      => 'Performance-Stack für Solar-/SHK-Anbieter und Performance-Marketer. Frontend, Managed Hosting (DE), Server-Side Tracking, CRM-Anbindung und Marktcheck-Vorqualifizierung in fünf Schichten. Mit Closed-Loop-Effekt aus SST + Marktcheck: bis zu ×4–×5 Effizienz pro qualifiziertem Lead.',
	'url'              => $page_url,
	'mainEntityOfPage' => $page_url,
	'author'           => $author_person,
	'publisher'        => $author_person,
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
			<p class="hu-intercept__eyebrow">Stack für Solar-/SHK-Anfrage-Systeme</p>
			<h1 class="hu-intercept__title" id="hu-stack-solar-hero-title">
				Der Stack, der CPL halbiert und 80 % der Schrott-Anfragen filtert
			</h1>
			<p class="hu-intercept__lead">
				Page Speed unter 1,5 Sekunden, server-seitiges Conversion-Tracking auf eigener Domain, Marktcheck als Vorfilter. Was Solar-Anbieter mit Ad-Budget tatsächlich brauchen — aus eigenen Projekten, nicht aus dem Werbeprospekt.
			</p>
		</div>
	</section>

	<section class="hu-intercept__system" id="layers" aria-labelledby="hu-stack-solar-layers-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-solar-layers-title">Die fünf Schichten</h2>
			<p class="hu-intercept__section-lead">
				Managed-Setup, das in 2–4 Wochen produktiv ist. Fokus: Page Speed, DSGVO-konformes Tracking, saubere CRM-Übergabe, vorgefilterte Anfragen. Kein Dev-Team nötig.
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
		</div>
	</section>

	<section class="hu-intercept__why" id="closed-loop" aria-labelledby="hu-stack-solar-loop-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-solar-loop-title">Warum der Effekt überproportional ist — der Closed Loop</h2>
			<p class="hu-intercept__section-lead">
				Die einzelnen Schichten sind nichts Besonderes. Server-Side Tracking allein liefert bessere Match-Rates. Ein Vorqualifizierungs-Funnel allein hebt die SQL-Quote. Erst die Verbindung macht den Unterschied.
			</p>
			<div class="hu-intercept__card">
				<p class="hu-intercept__card-text">
					<strong>Der Marktcheck definiert, was als Conversion zählt. Das Server-Side Tracking spielt genau diese gefilterte Conversion zurück ans Werbekonto.</strong> Meta und Google trainieren ihre Algorithmen damit nicht mehr auf „irgendwer, der das Formular ausgefüllt hat", sondern auf den tatsächlichen Käufer-Avatar: Hauseigentümer im Liefergebiet mit Bedarf und Budget.
				</p>
				<p class="hu-intercept__card-text">
					Lookalike-Audiences werden scharf. Optimization-Pools lernen die Richtigen. Jeder neue qualifizierte Lead schärft das Modell weiter.
				</p>
				<p class="hu-intercept__card-text">
					Effekt: Die Werbekosten pro qualifiziertem Lead sinken nicht linear, sondern in einer Kurve. In voll ausgereiften Setups landen wir bei <strong>×4 bis ×5 Effizienz</strong> gegenüber dem Vorher-Setup mit clientseitigem Tracking und ungefiltertem Lead-Flow.
				</p>
			</div>
		</div>
	</section>

	<section class="hu-intercept__system" id="zahlen" aria-labelledby="hu-stack-solar-numbers-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-solar-numbers-title">Was sich in Zahlen niederschlägt</h2>
			<p class="hu-intercept__section-lead">
				Erfahrungswerte aus eigenen Solar-/SHK-Projekten mit diesem Stack. Bereiche, weil jeder Markt anders ist. Detaildaten auf Anfrage unter NDA.
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
				Keine Werbeversprechen — Erfahrungswerte. Vergleichbare Größenordnung in deinem Markt ist nicht garantiert, aber realistisch, wenn der Stack vollständig umgesetzt wird.
			</p>

			<div class="hu-intercept__cta hu-intercept__cta--inline">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $analysis_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="stack_solar"
				   data-track-section="zahlen">
					Marktcheck starten — sieh, wo dein Anfrage-System Geld liegen lässt
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-stack-solar-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-solar-faq-title">Häufige Fragen zum Solar-Stack</h2>
			<div class="hu-intercept__faq-list">
				<?php foreach ( $faq as $item ) : ?>
					<details class="hu-intercept__faq-item">
						<summary class="hu-intercept__faq-q"><?php echo esc_html( $item['question'] ); ?></summary>
						<p class="hu-intercept__faq-a"><?php echo esc_html( $item['answer'] ); ?></p>
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
