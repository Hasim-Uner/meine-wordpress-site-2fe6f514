<?php
/**
 * Template Name: Anfrage-System Stack
 * Description: Kuratierte Architektur-Übersicht der Bausteine, die in eigenen
 *              Anfrage-Systemen für Solar-/SHK-Anbieter und im White-Label-
 *              Modell für Agenturen zum Einsatz kommen. Zwei klar getrennte
 *              Stack-Tracks (Performance-Track & Dev-Track), damit jede
 *              Zielgruppe den richtigen Einstieg findet. Hosting-Track-A nennt
 *              HostPress als Partnerempfehlung mit transparenter Werbekenn-
 *              zeichnung.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url        = home_url( '/anfrage-system-stack/' );
$analysis_url    = function_exists( 'hu_get_request_analysis_url' )
	? hu_get_request_analysis_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$whitelabel_url  = function_exists( 'nexus_get_whitelabel_page_url' )
	? nexus_get_whitelabel_page_url()
	: home_url( '/whitelabel-retainer/' );
$sst_url         = home_url( '/server-side-tracking-b2b/' );
$solar_money_url = home_url( '/solar-waermepumpen-leadgenerierung/' );

// ── Affiliate ─────────────────────────────────────────────────
$hostpress_url        = function_exists( 'hu_get_affiliate_url' ) ? hu_get_affiliate_url( 'hostpress' ) : 'https://www.hostpress.de/wordpress-hosting/';
$hostpress_link_attrs = function_exists( 'hu_render_affiliate_anchor_attrs' ) ? hu_render_affiliate_anchor_attrs( 'hostpress', 'stack_hosting' ) : ' rel="sponsored nofollow noopener" target="_blank"';

// ── Inhalte: Track A — Performance / Solar-/SHK / B2B-Marketer ───
$track_a_layers = [
	[
		't' => 'Frontend & Page Speed',
		's' => 'WordPress hardcoded, Blocksy-Child-Theme, Vanilla JS, conditional Asset-Loading pro Template. Kein Page-Builder. Ziel: TTFB unter 200 ms, LCP unter 1,5 s — Voraussetzung für Google-Ads-Qualitätsfaktor und mobile Vorqualifizierung.',
	],
	[
		't' => 'Managed Hosting (DE)',
		's' => 'Managed WordPress Hosting bei HostPress: deutsche Serverstandorte, NVMe-Storage, Redis-Cache, CDN, tägliche Backups, WordPress-Optimierung. Reduziert TTFB-Risiko und liefert die rechtliche Grundlage für First-Party-Tracking.',
	],
	[
		't' => 'Server-Side Tracking',
		's' => 'GTM Server-Side-Container, GA4 Measurement Protocol, Meta CAPI, Consent Mode v2 — auf eigener First-Party-Domain. Conversions kommen auch bei ITP, Ad-Blockern und ablehnendem Consent (pseudonymisierte Pings) sauber an.',
	],
	[
		't' => 'CRM-Anbindung',
		's' => 'Webhook vom Server ins CRM (HubSpot, Bitrix24, Pipedrive, Brevo) inkl. Lead-Score, Quelle und Kampagne. Keine Excel-Übergaben, keine verlorenen Anfragen.',
	],
	[
		't' => 'Vorqualifizierung im Funnel',
		's' => 'Marktcheck und Anfrage-System-Analyse filtern unpassende Anfragen vor dem Vertriebskontakt. Hebt SQL-Quote, senkt CPL ohne Mehrwerbe-Budget.',
	],
];

// ── Inhalte: Track B — Scaling Agencies / Dev-Power-User ────────
$track_b_layers = [
	[
		't' => 'Eigener Root-Server (DE)',
		's' => 'Hetzner / Open-Stack-VPS in Deutschland mit vollem Root-Zugriff. Freie Konfiguration von Nginx-Server-Blocks, PHP-OPcache, MySQL-Tuning, System-Cron statt wp-cron — Voraussetzung für anspruchsvolle WooCommerce- und Hochlast-WordPress-Setups.',
	],
	[
		't' => 'CI/CD via GitHub Actions + Rsync',
		's' => 'git push → GitHub Actions → inkrementeller Rsync-Deploy via ED25519-Key in ein vordefiniertes Theme-Verzeichnis. Rollback in Sekunden über die Git-Historie. Siehe öffentliches Setup in diesem Repo (.github/workflows/deploy.yml).',
	],
	[
		't' => 'Restricted Deploy-Shell',
		's' => 'authorized_keys mit rrsync-Wrapper: der Deploy-Key kann nur Rsync-Schreiboperationen in genau ein Zielverzeichnis ausführen. Kein Shell-Zugriff, kein Lateral Movement bei kompromittiertem Key. Erfüllt anspruchsvolle B2B-Sicherheitsaudits.',
	],
	[
		't' => 'White-Label Care-Plan-Infrastruktur',
		's' => 'Für Agenturen, die wiederkehrende Umsätze (MRR) aus Pflegeverträgen bauen wollen: Multi-Site-Hosting auf einem dedizierten VPS, eigene Nameserver, gebrandete Status-Reports. Ich übernehme den unsichtbaren technischen Unterbau, die Agentur fakturiert.',
	],
];

// ── Argumente: warum welcher Track ──────────────────────────────
$track_picker = [
	[
		't' => 'Performance-Track (A)',
		'who' => 'Solar-, Wärmepumpen- und SHK-Anbieter mit Ad-Budget. Performance-Marketer, die saubere Attribution und niedrigen CPL brauchen.',
		'why' => 'Managed-Setup mit HostPress + Server-Side-Tracking liefert in 2–4 Wochen ein produktives Anfrage-System. Kein Dev-Team nötig.',
	],
	[
		't' => 'Dev-Track (B)',
		'who' => 'Web-Agenturen, technische Inhaber, CTOs mit eigenem Dev-Team. Bedarf: Root-SSH, Git-Deployment, kein wp-config-Schreibschutz, Multi-Site-Hosting für Care-Plans.',
		'why' => 'Open-Stack auf eigenem Root-Server bietet die Skalierbarkeit und Kontrolle, die Managed-Plattformen wie HostPress oder Raidboxes für diese Zielgruppe nicht abdecken.',
	],
];

// ── FAQ ───────────────────────────────────────────────────────
$faq = [
	[
		'question' => 'Warum HostPress im Performance-Track empfohlen, aber nicht im Dev-Track?',
		'answer'   => 'HostPress ist Managed WordPress Hosting aus Deutschland — ideal für Solar-/SHK-Anbieter, die eine schnelle, DSGVO-konforme Plattform ohne eigenen DevOps wollen. Im Dev-Track brauchen Agenturen aber Root-SSH, freies wp-config-Schreibrecht und Git-Push direkt auf den Server — Anforderungen, die Managed-Plattformen prinzipbedingt einschränken. Hier passt ein eigener Root-Server besser.',
	],
	[
		'question' => 'Ist die HostPress-Empfehlung ein Affiliate-Link?',
		'answer'   => 'Ja. Bei einem Abschluss über meinen Link entsteht eine Vergütung — ohne Mehrkosten für dich. Die Empfehlung basiert auf eigenem Einsatz im Stack und auf Eignung für die Zielgruppe. Wäre HostPress technisch ungeeignet, würde es hier nicht stehen — egal welche Vergütung.',
	],
	[
		'question' => 'Kann ich beide Tracks kombinieren?',
		'answer'   => 'Ja. Eine typische Konstellation: Solar-Betrieb fährt die eigene Money-Page bei HostPress (Track A), während die Agentur, die das Setup betreut, ihre internen Tooling-Sites auf einem Root-VPS hält (Track B). Tracking-, CRM- und Funnel-Architektur bleiben in beiden Setups identisch.',
	],
	[
		'question' => 'Warum keine Liste „beste WordPress-Hoster 2026"?',
		'answer'   => 'Weil eine Vergleichsliste eine andere Empfehlungsklasse wäre. Hier steht der Stack, der in echten Projekten läuft — eine kuratierte Entscheidung, keine Marktübersicht. Andere Hoster können in Einzelfällen besser passen; diese Seite ist kein Hoster-Vergleich, sondern ein Architektur-Statement.',
	],
];

// ── Schema.org: TechArticle + FAQPage + BreadcrumbList ───────
$author_person = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];

$tech_article_schema = [
	'@context'         => 'https://schema.org',
	'@type'            => 'TechArticle',
	'@id'              => trailingslashit( $page_url ) . '#article',
	'headline'         => 'Anfrage-System-Stack: Architektur für eigene Lead-Infrastruktur',
	'description'      => 'Kuratierter Tech-Stack für eigene Anfrage-Systeme im Solar-/SHK-B2B und White-Label-Modell für Agenturen. Hosting, Tracking, CRM und Deployment in zwei klar getrennten Tracks.',
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

<main id="primary" class="hu-intercept" role="main" data-track-page="anfrage-system-stack">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-stack-hero-title">
		<div class="hu-intercept__container">
			<p class="hu-intercept__eyebrow">Stack hinter eigenen Anfrage-Systemen</p>
			<h1 class="hu-intercept__title" id="hu-stack-hero-title">
				Der Stack, den ich in Projekten einsetze — und warum
			</h1>
			<p class="hu-intercept__lead">
				Keine Tool-Liste. Eine Architektur-Entscheidung, die sich in Solar-/SHK-Projekten und im White-Label-Modell für Agenturen bewährt hat. Zwei Tracks, je nach Zielgruppe und Reifegrad.
			</p>
		</div>
	</section>

	<section class="hu-intercept__why" id="track-picker" aria-labelledby="hu-stack-picker-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-picker-title">Welcher Track passt zu dir?</h2>
			<div class="hu-intercept__grid hu-intercept__grid--two">
				<?php foreach ( $track_picker as $picker ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $picker['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><strong>Für wen:</strong> <?php echo esc_html( $picker['who'] ); ?></p>
						<p class="hu-intercept__card-text"><strong>Warum:</strong> <?php echo esc_html( $picker['why'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__system" id="track-a" aria-labelledby="hu-stack-a-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-a-title">Track A — Performance-Stack für Solar-/SHK-Anbieter und Performance-Marketer</h2>
			<p class="hu-intercept__section-lead">
				Managed-Setup, das in 2–4 Wochen produktiv ist. Fokus: Page Speed, DSGVO-konformes Tracking, saubere CRM-Übergabe. Kein Dev-Team nötig.
			</p>

			<?php
			set_query_var( 'affiliate_notice_provider_label', 'HostPress' );
			set_query_var( 'affiliate_notice_context', 'stack' );
			get_template_part( 'template-parts/affiliate-notice' );
			?>

			<ol class="hu-intercept__layers">
				<?php foreach ( $track_a_layers as $i => $layer ) : ?>
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

			<div class="hu-intercept__cta hu-intercept__cta--inline">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $analysis_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="anfrage_system_stack"
				   data-track-section="track_a">
					Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $sst_url ); ?>"
				   data-track-action="cta_sst_detail"
				   data-track-category="anfrage_system_stack"
				   data-track-section="track_a">
					Server-Side Tracking im Detail
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__system" id="track-b" aria-labelledby="hu-stack-b-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-b-title">Track B — Dev-Stack für Agenturen und technische Inhaber</h2>
			<p class="hu-intercept__section-lead">
				Eigener Root-Server statt Managed-Plattform. Git-Push-Deployment, Restricted-Shell, Multi-Site-Hosting für Care-Plans und White-Label-Modelle. Kein Affiliate — diese Setups bauen wir gemeinsam.
			</p>
			<ol class="hu-intercept__layers">
				<?php foreach ( $track_b_layers as $i => $layer ) : ?>
					<li class="hu-intercept__layer">
						<span class="hu-intercept__layer-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="hu-intercept__layer-body">
							<h3 class="hu-intercept__layer-title"><?php echo esc_html( $layer['t'] ); ?></h3>
							<p class="hu-intercept__layer-text"><?php echo esc_html( $layer['s'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
			<div class="hu-intercept__cta hu-intercept__cta--inline">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $whitelabel_url ); ?>"
				   data-track-action="cta_whitelabel"
				   data-track-category="anfrage_system_stack"
				   data-track-section="track_b">
					White-Label-Gespräch anfragen
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-stack-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-faq-title">Häufige Fragen zum Stack</h2>
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
