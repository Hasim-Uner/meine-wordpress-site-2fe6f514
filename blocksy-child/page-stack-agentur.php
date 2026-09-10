<?php
/**
 * Template Name: Stack Agentur
 * Description: Technische Zusammenarbeit für White-Label-WordPress-Projekte:
 *              vorhandene Setups übernehmen, Git/Staging sauber nutzen und
 *              Projekte nachvollziehbar dokumentieren und übergeben.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url       = home_url( '/stack-agentur/' );
$whitelabel_url = function_exists( 'nexus_get_whitelabel_page_url' )
	? nexus_get_whitelabel_page_url()
	: home_url( '/whitelabel-retainer/' );
$outsourcing_url = home_url( '/wordpress-projekte-auslagern/' );

// ── Inhalte: Zusammenarbeit statt Technik-Theater ─────────────
$layers = [
	[
		't' => 'Bestehenden Stack zuerst verstehen',
		's' => 'Hosting, Theme oder Builder, Plugins, Git, Staging und Deployment werden vor dem Start geprüft. Was funktioniert, bleibt bestehen. Ich baue nicht um, nur um meinen eigenen Stack durchzusetzen.',
	],
	[
		't' => 'Git & Deployment nachvollziehbar halten',
		's' => 'Ich arbeite im vorhandenen Repository und passe mich an euren Workflow an. Änderungen bleiben versioniert und prüfbar. Falls noch kein sauberer Ablauf existiert, definieren wir gemeinsam einen einfachen Weg für Entwicklung, Review und Livegang.',
	],
	[
		't' => 'Staging, Zugänge & Rollen sauber trennen',
		's' => 'Entwicklung und Live-System werden sinnvoll getrennt. Zugänge, Verantwortlichkeiten und Freigaben sind vor dem ersten Eingriff klar, damit niemand im Projekt rätseln muss, wer was ändern darf oder wo getestet wird.',
	],
	[
		't' => 'Dokumentation & Übergabe ohne Lock-in',
		's' => 'Besonderheiten, technische Entscheidungen und relevante Änderungen werden nachvollziehbar festgehalten. Code, Zugänge und Projektwissen bleiben bei Agentur und Kunde — die Zusammenarbeit darf nicht von mir als Einzelperson abhängig werden.',
	],
];

$principles = [
	[
		't' => 'Managed Hosting ist völlig okay',
		's' => 'Raidboxes, HostPress oder andere Managed-Setups sind kein Nachteil, wenn sie zum Projekt passen. Ein Infrastrukturwechsel ist keine Voraussetzung für die Zusammenarbeit.',
	],
	[
		't' => 'Individuelle Infrastruktur nur bei echtem Bedarf',
		's' => 'Eigene Server, spezielle Deployments oder individuelle Server-Konfigurationen sind möglich — aber nur dann sinnvoll, wenn Last, Sicherheit, Integrationen oder Betriebsmodell sie tatsächlich rechtfertigen.',
	],
];

// ── FAQ ───────────────────────────────────────────────────────
$faq = [
	[
		'question' => 'Muss unsere Agentur ihren bestehenden Stack ändern?',
		'answer'   => 'Nein. Ich versuche zuerst, mich in euren vorhandenen Workflow einzufügen. Hosting, Theme-Stack, Builder, Git-Strategie und Deployment bleiben bestehen, wenn sie technisch sinnvoll funktionieren.',
	],
	[
		'question' => 'Arbeitest du auch in bestehenden WordPress-Installationen?',
		'answer'   => 'Ja. Ein großer Teil der White-Label-Arbeit besteht aus Weiterentwicklung, Fehlerbehebung, Performance-Optimierung und technischen Erweiterungen in bestehenden Installationen — nicht aus Greenfield-Projekten.',
	],
	[
		'question' => 'Wie läuft die Zusammenarbeit mit Git?',
		'answer'   => 'Wenn ein Repository und ein Branching- oder Review-Prozess vorhanden sind, arbeite ich darin. Falls nicht, reicht oft ein schlanker Workflow mit klarer Trennung zwischen Entwicklung und Produktion. Entscheidend ist Nachvollziehbarkeit, nicht Prozess-Theater.',
	],
	[
		'question' => 'Wie funktioniert White-Label in der Praxis?',
		'answer'   => 'Die Agentur bleibt gegenüber ihrem Kunden Ansprechpartnerin. Ich arbeite im Hintergrund an den vereinbarten technischen Aufgaben, stimme mich mit dem Agenturteam ab und trete gegenüber dem Endkunden nur auf, wenn das ausdrücklich gewünscht ist.',
	],
	[
		'question' => 'Was bleibt nach Projektende bei uns?',
		'answer'   => 'Code, Dokumentation, Zugänge und relevante technische Entscheidungen bleiben bei Agentur beziehungsweise Kunde. Es gibt keinen proprietären Sonderweg und keinen künstlichen Lock-in.',
	],
];

// ── SEO: alte Root-Server-Positionierung überschreiben ───────
function hu_stack_agentur_grounded_seo_override( $map ) {
	$map = is_array( $map ) ? $map : [];
	$map['stack-agentur'] = array_merge(
		isset( $map['stack-agentur'] ) && is_array( $map['stack-agentur'] ) ? $map['stack-agentur'] : [],
		[
			'title'       => 'White-Label WordPress: Git, Staging & Übergabe für Agenturen',
			'description' => 'Technische Zusammenarbeit für Agenturen: vorhandene WordPress-Setups übernehmen, Git und Staging sauber nutzen, Änderungen dokumentieren und ohne Lock-in übergeben.',
		]
	);

	return $map;
}
add_filter( 'hu_forced_singular_seo_map', 'hu_stack_agentur_grounded_seo_override', 99 );

// ── Schema.org: TechArticle + FAQPage ────────────────────────
$author_person = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];

$tech_article_schema = [
	'@context'         => 'https://schema.org',
	'@type'            => 'TechArticle',
	'@id'              => trailingslashit( $page_url ) . '#article',
	'headline'         => 'White-Label WordPress: Git, Staging, Dokumentation und Übergabe für Agenturen',
	'description'      => 'Technische Zusammenarbeit für Agenturen: bestehende WordPress-Setups übernehmen, Git und Staging sauber nutzen, Rollen klären und Projekte nachvollziehbar übergeben.',
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
			<p class="hu-intercept__eyebrow">Technische Zusammenarbeit im White-Label</p>
			<h1 class="hu-intercept__title" id="hu-stack-agentur-hero-title">
				So füge ich mich in bestehende Agentur-Setups ein.
			</h1>
			<p class="hu-intercept__lead">
				Git, Staging, Zugänge, Deployment und Übergabe werden vor Projektstart geklärt. Ich übernehme vorhandene Prozesse, statt einer Agentur einen neuen Technik-Stack aufzudrücken.
			</p>
		</div>
	</section>

	<section class="hu-intercept__system" id="zusammenarbeit" aria-labelledby="hu-stack-agentur-layers-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-agentur-layers-title">Vier Dinge, die vor dem ersten Projekt geklärt sind</h2>
			<p class="hu-intercept__section-lead">
				Die technische Zusammenarbeit soll für euer Team möglichst unspektakulär sein: vorhandenen Ablauf verstehen, sauber darin arbeiten und das Projekt nachvollziehbar hinterlassen.
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

	<section class="hu-intercept__why" id="infrastruktur" aria-labelledby="hu-stack-agentur-why-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-agentur-why-title">Kein Infrastrukturwechsel als Voraussetzung</h2>
			<p class="hu-intercept__section-lead">
				Die richtige technische Lösung hängt vom Projekt ab — nicht davon, welches Setup ich persönlich bevorzuge.
			</p>
			<div class="hu-intercept__grid hu-intercept__grid--two">
				<?php foreach ( $principles as $row ) : ?>
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
				   data-track-section="infrastruktur">
					White-Label-Zusammenarbeit ansehen
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-stack-agentur-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-agentur-faq-title">Häufige Fragen zur technischen Zusammenarbeit</h2>
			<div class="hu-intercept__faq-list">
				<?php foreach ( $faq as $item ) : ?>
					<details class="hu-intercept__faq-item" name="hu-faq-stack-agentur">
						<summary class="hu-intercept__faq-q"><?php echo esc_html( $item['question'] ); ?></summary>
						<p class="hu-intercept__faq-a"><?php echo esc_html( $item['answer'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="auslagerung" aria-labelledby="hu-stack-agentur-outsourcing-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-stack-agentur-outsourcing-title">Wenn ihr Projekte regelmäßig auslagert</h2>
			<p class="hu-intercept__section-lead">
				Der technische Ablauf ist nur ein Teil. Rollen, Briefing, Freigaben und Qualitätskontrolle habe ich im Auslagerungs-Leitfaden separat zusammengefasst.
			</p>
			<div class="hu-intercept__cta hu-intercept__cta--inline">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $outsourcing_url ); ?>"
				   data-track-action="cta_outsourcing_guide"
				   data-track-category="stack_agentur"
				   data-track-section="auslagerung">
					Projekte sauber auslagern
				</a>
			</div>
		</div>
	</section>

	<script type="application/ld+json"><?php echo wp_json_encode( $tech_article_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
</main>

<?php
get_footer();
