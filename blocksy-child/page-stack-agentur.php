<?php
/**
 * Template Name: Stack Agentur
 * Description: Dev-Stack für Web-Agenturen, technische Inhaber und CTOs mit
 *              eigenem Dev-Team. Eigener Root-Server (DE), CI/CD via GitHub
 *              Actions + Rsync, Restricted Deploy-Shell, Multi-Site-Hosting
 *              für Care-Plans und White-Label-Modelle. Kein Affiliate.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url        = home_url( '/stack-agentur/' );
$whitelabel_url  = function_exists( 'nexus_get_whitelabel_page_url' )
	? nexus_get_whitelabel_page_url()
	: home_url( '/whitelabel-retainer/' );
$stack_solar_url = home_url( '/stack-solar/' );

// ── Inhalte: 4 Schichten ─────────────────────────────────────
$layers = [
	[
		't' => 'Eigener Root-Server (DE)',
		's' => 'Hetzner- oder Open-Stack-VPS in Deutschland mit vollem Root-Zugriff. Freie Konfiguration von Nginx-Server-Blocks, PHP-OPcache, MySQL-Tuning, System-Cron statt wp-cron — Voraussetzung für anspruchsvolle WooCommerce- und Hochlast-WordPress-Setups. Voller Schreibzugriff auf wp-config, vhost-Konfiguration und Crontab — Dinge, die Managed-Plattformen prinzipbedingt einschränken.',
	],
	[
		't' => 'CI/CD via GitHub Actions + Rsync',
		's' => 'git push → GitHub Actions → inkrementeller Rsync-Deploy via ED25519-Key in ein vordefiniertes Theme-Verzeichnis. Deploy-Dauer in Sekunden, Rollback per Git-Historie ebenso schnell. Branching-Strategie kompatibel mit Pull-Request-Reviews, Feature-Branches und Production-Hotfixes. Siehe öffentliches Setup in diesem Repo (.github/workflows/deploy.yml).',
	],
	[
		't' => 'Restricted Deploy-Shell',
		's' => 'authorized_keys mit rrsync-Wrapper: der Deploy-Key kann nur Rsync-Schreiboperationen in genau ein Zielverzeichnis ausführen. Kein Shell-Zugriff, kein Lateral Movement bei kompromittiertem Key. Erfüllt anspruchsvolle B2B-Sicherheitsaudits und reduziert die Angriffsfläche gegenüber typischen FTP- oder SSH-Push-Setups erheblich.',
	],
	[
		't' => 'White-Label Care-Plan-Infrastruktur',
		's' => 'Multi-Site-Hosting auf einem dedizierten VPS, eigene Nameserver, gebrandete Status-Reports, isolierte PHP-Pools pro Site. Für Agenturen, die wiederkehrende Umsätze (MRR) aus Pflegeverträgen bauen wollen, ohne selbst DevOps-Team und Monitoring-Stack aufzubauen. Ich übernehme den unsichtbaren technischen Unterbau, die Agentur fakturiert ihren Kunden.',
	],
];

// ── Warum-nicht-Managed-Block ────────────────────────────────
$why_root = [
	[
		't' => 'Was Managed-Plattformen blockieren',
		's' => 'Kein Root-SSH, eingeschränkter wp-config-Schreibzugriff, gebundene PHP-Versionen, fester Caching-Stack, kein System-Cron, keine eigenen Nameserver. Für ein Solar-Money-Page-Setup ist das okay — für eine Agentur mit 15+ Kunden-Sites, eigenem Tooling und individueller Compliance ist es ein Engpass.',
	],
	[
		't' => 'Was der Root-Stack ermöglicht',
		's' => 'Eigene Nginx-Konfiguration pro Site, dedizierte PHP-FPM-Pools, MySQL-Tuning, System-Cron, Custom-Mailrouting, eigene SSL-Strategie, Multi-Site auf einer Maschine ohne Plattform-Aufschlag. Volle Kontrolle, volle Verantwortung — und damit auch volle Möglichkeit, die Margen pro Care-Plan-Site zu drücken.',
	],
];

// ── FAQ ───────────────────────────────────────────────────────
$faq = [
	[
		'question' => 'Warum kein Managed Hosting für den Agentur-Stack?',
		'answer'   => 'Weil die Anforderungen anders sind. Managed-Plattformen wie HostPress oder Raidboxes liefern ein verlässliches, sicheres Setup für einzelne Money-Pages — Root-SSH, freier wp-config-Schreibzugriff und Git-Push direkt auf den Server werden aber prinzipbedingt eingeschränkt. Für Agenturen mit 15+ Kunden-Sites, eigenem Tooling und individueller Compliance ist ein eigener VPS skalierbarer und im Care-Plan-Modell deutlich margenfreundlicher.',
	],
	[
		'question' => 'Wie sicher ist der Deploy-Key wirklich?',
		'answer'   => 'Der ED25519-Key landet als Single-Purpose-Key in authorized_keys, eingegrenzt durch einen rrsync-Wrapper auf genau ein Zielverzeichnis. Kein interaktiver Shell-Zugriff, kein sudo, kein Lateral Movement im Falle einer Kompromittierung. Das ist deutlich sicherer als typische SFTP- oder Voll-SSH-Setups — und übersteht in der Regel B2B-Sicherheitsaudits ohne Nachbesserung.',
	],
	[
		'question' => 'Wie funktioniert das White-Label-Modell technisch?',
		'answer'   => 'Multi-Site-Hosting auf dediziertem VPS unter den Nameservern der Agentur. Endkunden sehen die Agentur als Anbieter, technische Wartung läuft im Hintergrund unsichtbar. Status-Reports werden im Agentur-Branding ausgeliefert. Abrechnung erfolgt zwischen Agentur und Endkunde — ich bin Subunternehmer, nicht sichtbarer Provider.',
	],
	[
		'question' => 'Kann ich mit einem Solar-Kunden auf diesen Stack umziehen?',
		'answer'   => 'Wenn der Solar-Kunde explizit eigenen Root-Server, Multi-Site-Hosting oder Compliance-Anforderungen jenseits Managed-Hosting hat — ja. Für die meisten Solar-Anbieter ist aber der Performance-Stack mit Managed Hosting die schnellere und kostengünstigere Antwort. Details dort auf der Stack-Solar-Seite.',
	],
	[
		'question' => 'Gibt es eine Liste „beste WordPress-Hoster für Agenturen"?',
		'answer'   => 'Nicht hier. Diese Seite ist eine Architektur-Entscheidung, keine Marktübersicht. Welcher Hetzner-Tarif, welche Distribution, welcher Backup-Provider — das hängt am Mengengerüst und an der Compliance-Lage der Agentur. Klären wir im Gespräch.',
	],
];

// ── Schema.org: TechArticle + FAQPage ────────────────────────
$author_person = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];

$tech_article_schema = [
	'@context'         => 'https://schema.org',
	'@type'            => 'TechArticle',
	'@id'              => trailingslashit( $page_url ) . '#article',
	'headline'         => 'Stack für Agenturen: Root-Server, Git-Deployment, Restricted-Shell, White-Label Care-Plans',
	'description'      => 'Dev-Stack für Web-Agenturen und technische Inhaber. Eigener Root-Server in Deutschland, CI/CD via GitHub Actions und Rsync, Restricted Deploy-Shell, Multi-Site-Hosting für Care-Plans und White-Label-Modelle. Kein Affiliate — diese Setups bauen wir gemeinsam.',
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

<main id="primary" class="hu-intercept" role="main" data-track-page="stack-agentur">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-stack-agentur-hero-title">
		<div class="hu-intercept__container">
			<p class="hu-intercept__eyebrow">Stack für Agenturen und technische Inhaber</p>
			<h1 class="hu-intercept__title" id="hu-stack-agentur-hero-title">
				Root-Server, Git-Deployment, White-Label — Infrastruktur, die mit deiner Agentur skaliert
			</h1>
			<p class="hu-intercept__lead">
				Eigener Root-Server statt Managed-Plattform. Git-Push-Deployment in Sekunden, Restricted-Shell für B2B-Audits, Multi-Site-Hosting für Care-Plans und White-Label-Modelle. Kein Affiliate — diese Setups bauen wir gemeinsam.
			</p>
		</div>
	</section>

	<section class="hu-intercept__system" id="layers" aria-labelledby="hu-stack-agentur-layers-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-agentur-layers-title">Die vier Schichten</h2>
			<p class="hu-intercept__section-lead">
				Open-Stack auf eigenem Root-Server. Volle Kontrolle über Konfiguration, Deployment-Pipeline und Multi-Site-Architektur — Voraussetzung für skalierbare Care-Plan-Modelle und White-Label-Setups.
			</p>
			<ol class="hu-intercept__layers">
				<?php foreach ( $layers as $i => $layer ) : ?>
					<li class="hu-intercept__layer">
						<span class="hu-intercept__layer-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="hu-intercept__layer-body">
							<h3 class="hu-intercept__layer-title"><?php echo esc_html( $layer['t'] ); ?></h3>
							<p class="hu-intercept__layer-text"><?php echo esc_html( $layer['s'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="hu-intercept__why" id="warum-root" aria-labelledby="hu-stack-agentur-why-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-agentur-why-title">Warum Root-Server statt Managed-Plattform?</h2>
			<div class="hu-intercept__grid hu-intercept__grid--two">
				<?php foreach ( $why_root as $row ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $row['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $row['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>

			<div class="hu-intercept__cta hu-intercept__cta--inline">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $whitelabel_url ); ?>"
				   data-track-action="cta_whitelabel"
				   data-track-category="stack_agentur"
				   data-track-section="warum_root">
					White-Label-Gespräch anfragen
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-stack-agentur-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-agentur-faq-title">Häufige Fragen zum Agentur-Stack</h2>
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
