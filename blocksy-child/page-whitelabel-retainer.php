<?php
/**
 * Template Name: Whitelabel & Weiterentwicklung
 * Description: White-Label WordPress für Agenturen. Klare Einstiegsaufgabe,
 *              nachvollziehbare Übergabe und Weiterentwicklung nach Erstprojekt.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$whitelabel_fit_url = function_exists( 'nexus_get_whitelabel_calendar_url' )
	? nexus_get_whitelabel_calendar_url()
	: 'https://cal.com/hasim-uener/whitelabel-fit-gesprach?overlayCalendar=true';

// Bestehenden Anfrageweg und die beiden Posteingangs-Kontexte erhalten.
$wl_page_url = get_permalink();
if ( ! $wl_page_url ) {
	$wl_page_url = function_exists( 'nexus_get_whitelabel_page_url' )
		? nexus_get_whitelabel_page_url()
		: home_url( '/whitelabel-retainer/' );
}

$wl_form_anchor = '#aufgabe';
$wl_form_url    = static function ( $case ) use ( $wl_page_url, $wl_form_anchor ) {
	return add_query_arg(
		[
			'type' => 'whitelabel',
			'case' => $case,
		],
		$wl_page_url
	) . $wl_form_anchor;
};
$wl_form_task_url  = $wl_form_url( 'aufgabe' );
$wl_form_offer_url = $wl_form_url( 'angebotsphase' );
$wl_form_endpoint  = rest_url( 'nexus/v1/whitelabel-request' );

$task_brief_response = hu_response_promise();

$imprint_url  = home_url( '/impressum/' );
$privacy_url  = home_url( '/datenschutz/' );
$current_year = wp_date( 'Y' );

$wl_routes     = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$wl_home_url   = $wl_routes['home'] ?? home_url( '/' );
$wl_brand_text = function_exists( 'hu_get_site_wordmark_text' ) ? hu_get_site_wordmark_text() : 'HAŞIM ÜNER';
$wl_home_label = sprintf(
	/* translators: %s: site or brand name. */
	__( 'Startseite - %s', 'blocksy-child' ),
	$wl_brand_text
);

// Diese Route hat eine eigene, geschlossene Seitennavigation. Der globale
// Header bleibt für alle anderen Requests unverändert registriert.
remove_action( 'wp_body_open', 'nexus_render_site_header', 20 );
add_action(
	'wp_body_open',
	static function () use ( $wl_home_url, $wl_brand_text, $wl_home_label, $wl_form_task_url ) {
		?>
		<a class="wl-skip-link" href="#main">Direkt zum Inhalt</a>
		<header class="wl-site-header" role="banner" data-track-section="whitelabel_header">
			<div class="nx-container">
				<div class="wl-site-header__shell">
					<a
						class="site-logo wl-site-header__brand"
						href="<?php echo esc_url( $wl_home_url ); ?>"
						rel="home"
						aria-label="<?php echo esc_attr( $wl_home_label ); ?>"
						data-track-action="nav_whitelabel_home"
						data-track-category="navigation"
						data-track-section="whitelabel_header"
					>
						<?php echo esc_html( $wl_brand_text ); ?>
					</a>

					<nav class="wl-site-header__nav" aria-label="Navigation auf dieser Seite">
						<ul role="list">
							<li><a href="#lieferfelder" data-track-action="nav_whitelabel_services" data-track-category="navigation" data-track-section="whitelabel_header">Leistungen</a></li>
							<li><a href="#einstieg" data-track-action="nav_whitelabel_pricing" data-track-category="navigation" data-track-section="whitelabel_header">Einstieg</a></li>
							<li><a href="#proof" data-track-action="nav_whitelabel_proof" data-track-category="navigation" data-track-section="whitelabel_header">Übergabe</a></li>
							<li><a href="#faq" data-track-action="nav_whitelabel_faq" data-track-category="navigation" data-track-section="whitelabel_header">FAQ</a></li>
						</ul>
					</nav>

					<a class="wl-site-header__cta" aria-label="Aufgabe beschreiben" href="<?php echo esc_url( $wl_form_task_url ); ?>" data-wl-form-link data-track-action="cta_whitelabel_header_task_brief" data-track-category="lead_gen" data-track-section="whitelabel_header">
						<span class="wl-site-header__cta-full">Aufgabe beschreiben</span>
						<span class="wl-site-header__cta-short" aria-hidden="true">Aufgabe</span>
					</a>
				</div>
			</div>
		</header>
		<?php
	},
	20
);

get_header();

$test_sprint_price = hu_whitelabel_price( 'test_sprint', 'display_fixed', hu_whitelabel_price( 'test_sprint' ) );
$retainer_price    = hu_whitelabel_price( 'retainer', 'display_hours', hu_whitelabel_price( 'retainer' ) );
$contact_email     = hu_get_contact_email();
$about_url         = nexus_get_page_url( [ 'hasim-uener', 'uber-mich' ], home_url( '/hasim-uener/' ) );
$portrait_url      = get_stylesheet_directory_uri() . '/assets/img/hasim-freelancer-portrait-480x600.webp';
$faq_items         = nexus_get_whitelabel_faq_items();
$wl_access_options = hu_whitelabel_request_access_options();
$entry_projects    = [
	[ 'title' => 'Tracking-Audit', 'price' => hu_whitelabel_price( 'tracking_audit' ), 'copy' => 'GA4, GTM und Consent prüfen. Ihr erhaltet einen schriftlichen Befund und eine priorisierte Fixliste.' ],
	[ 'title' => 'Server-Side-Setup', 'price' => hu_whitelabel_price( 'server_side' ), 'copy' => 'Messstrecke aufsetzen, Events prüfen und das Setup dokumentiert in euren Accounts übergeben.' ],
	[ 'title' => 'Landingpage', 'price' => hu_whitelabel_price( 'landingpage' ), 'copy' => 'Eine abgestimmte Anfragestrecke umsetzen: Seite, Formular und vereinbarte Conversion-Messung.' ],
];
// Öffentliche Projektverweise sind keine Agentur-Testimonials. Keine Kopplung
// der namentlichen Referenz mit Kennzahlen aus dem anonymisierten Solar-Case.
$proof_references = [
	[ 'label' => 'civaka-azad.org', 'url' => 'https://civaka-azad.org/', 'type' => 'Informationsarchitektur', 'copy' => 'Navigation, Archive und interne Verweise für einen gewachsenen redaktionellen Bestand.' ],
	[ 'label' => 'e3-newenergy.de', 'url' => 'https://e3-newenergy.de/', 'type' => 'Anfragestrecke', 'copy' => 'Beratung, Angebotsanfrage und Kontakt für ein erklärungsbedürftiges Energieangebot verbinden.' ],
	[ 'label' => 'hasimuener.org', 'url' => 'https://hasimuener.org/', 'type' => 'Eigenes Editorial-Projekt', 'copy' => 'Typografie, Raster und Leseführung als Schwerpunkt einer inhaltsorientierten Website.' ],
];
?>

<div class="wl-page" data-track-section="whitelabel_page">
	<section class="wl-section wl-hero" id="hero" aria-labelledby="wl-title">
		<div class="nx-container">
			<div class="wl-hero__grid">
				<div class="wl-hero__copy">
					<p class="wl-eyebrow">White-Label WordPress für Agenturen</p>
					<h1 id="wl-title">Eure Projekte.<br>Meine Umsetzung.<br><span>Euer Name.</span></h1>
					<p class="wl-lede">Euer Kunde ist bereit. Eurem Team fehlt die technische Kapazität? Ich setze WordPress, Landingpages und Tracking für euch um – mit direkter Abstimmung und einer Übergabe, mit der ihr weiterarbeiten könnt.</p>
					<div class="wl-actions">
						<a class="wl-button" href="<?php echo esc_url( $wl_form_task_url ); ?>" data-wl-form-link data-track-action="cta_whitelabel_hero_task_brief" data-track-category="lead_gen" data-track-section="hero">Aufgabe beschreiben <span aria-hidden="true">↗</span></a>
						<a class="wl-text-link" href="#proof">Zusammenarbeit ansehen <span aria-hidden="true">↓</span></a>
					</div>
					<p class="wl-small"><?php echo esc_html( $task_brief_response ); ?>. Starttermin nach Abstimmung.</p>
				</div>
				<aside class="wl-dossier" aria-label="Leistungsbeispiel für ein Agenturprojekt">
					<div class="wl-dossier__top"><span>Agentur → Umsetzung → Übergabe</span><span class="wl-dossier__tag">Beispiel</span></div>
					<p class="wl-dossier__title">Vom Briefing bis zum letzten getesteten Event.</p>
					<p class="wl-dossier__intro">Eine Landingpage. Eine abgestimmte Messstrecke. Ein technischer Ansprechpartner.</p>
					<ol class="wl-deliverables">
						<li><span class="wl-number">01</span><div><strong>WordPress &amp; Formular</strong><span>Umsetzung nach eurem Briefing</span></div><span class="wl-deliverables__mark" aria-hidden="true">↗</span></li>
						<li><span class="wl-number">02</span><div><strong>Tracking &amp; Lead-Übergabe</strong><span>Events und Routing nach Vereinbarung</span></div><span class="wl-deliverables__mark" aria-hidden="true">↗</span></li>
						<li><span class="wl-number">03</span><div><strong>Abnahme &amp; Dokumentation</strong><span>Prüfschritte, Code und offene Punkte</span></div><span class="wl-deliverables__mark" aria-hidden="true">↗</span></li>
					</ol>
					<div class="wl-dossier__footer"><span class="wl-seal" aria-hidden="true">HÜ</span><p><strong>Haşim Üner</strong><br>Direkt mit dem Entwickler sprechen.</p></div>
				</aside>
			</div>
			<ul class="wl-assurances" aria-label="Rahmen der Zusammenarbeit">
				<li><span aria-hidden="true">↳</span> Kundenbeziehung bleibt bei euch</li>
				<li><span aria-hidden="true">↳</span> NDA als Standard</li>
				<li><span aria-hidden="true">↳</span> Code und Zugänge in euren Accounts</li>
			</ul>
		</div>
	</section>

	<section class="wl-section wl-services" id="lieferfelder" aria-labelledby="wl-services-title">
		<div class="nx-container">
			<header class="wl-section-heading wl-section-heading--split">
				<div><p class="wl-eyebrow">01 / Wobei ich euer Team entlaste</p><h2 id="wl-services-title">Die Lücke zwischen<br>versprochen und geliefert.</h2></div>
				<p>Für Web-, Design- und Performance-Agenturen, die ihre Kunden selbst führen und die technische Umsetzung gezielt ergänzen möchten.</p>
			</header>
			<div class="wl-service-row">
				<span class="wl-number">01 / WordPress</span><div><h3>Der Entwurf steht.<br>Die Umsetzung fehlt.</h3><p>Templates, bestehende Installationen und Landingpages weiterentwickeln. Performance-Probleme und technisches SEO dort bearbeiten, wo sie entstehen: im System.</p></div><div class="wl-service-row__result"><span class="wl-label">Eure Übergabe</span><p>Abgestimmte Umsetzung auf Staging, Funktionstest und nachvollziehbare Änderungen.</p></div>
			</div>
			<div class="wl-service-row">
				<span class="wl-number">02 / Tracking</span><div><h3>Die Kampagne läuft.<br>Die Messung wirft Fragen auf.</h3><p>GA4, GTM, Consent Mode und bei Bedarf Server-Side Tracking oder Meta CAPI prüfen und verbinden. Messabweichungen einordnen und Fehler gezielt beheben.</p></div><div class="wl-service-row__result"><span class="wl-label">Eure Übergabe</span><p>Prüfbare Events, dokumentiertes Setup und Klarheit über verbleibende Messgrenzen.</p></div>
			</div>
			<div class="wl-service-row">
				<span class="wl-number">03 / Anfragestrecken</span><div><h3>Die Anfrage kommt.<br>Danach wird es unübersichtlich.</h3><p>Formulare, Leadqualifizierung und CRM-Routing zusammenführen. Damit ihr vor der Übergabe prüfen könnt, ob eine Anfrage am richtigen Ort ankommt.</p></div><div class="wl-service-row__result"><span class="wl-label">Eure Übergabe</span><p>Ein getesteter Weg vom Formular bis zum vereinbarten Empfänger oder CRM.</p></div>
			</div>
		</div>
	</section>

	<section class="wl-section wl-proof" id="proof" aria-labelledby="wl-proof-title">
		<div class="nx-container">
			<header class="wl-section-heading"><p class="wl-eyebrow">02 / Nachvollziehbare Übergabe</p><h2 id="wl-proof-title">Ihr bekommt mehr als<br>„ist jetzt fertig“.</h2><p>Vor dem Start legen wir fest, woran ihr die Lieferung abnehmt. Die Dokumentation gehört zur Aufgabe – damit euer Team danach übernehmen kann.</p></header>
			<div class="wl-handover">
				<div class="wl-handover__brief"><span class="wl-label">Illustratives Übergabemuster</span><h3>Formular → CRM</h3><p>So kann eine Übergabe für eine abgegrenzte Formular-Aufgabe aufgebaut sein. Der konkrete Umfang wird vor Projektbeginn vereinbart.</p><span class="wl-handover__note">Muster zur Orientierung, kein Kundenbeleg.</span></div>
				<dl class="wl-handover__list">
					<div><dt>Aufgabe &amp; Umfang</dt><dd>Welche Felder, Prüfungen und Empfänger sind vereinbart?</dd></div>
					<div><dt>Änderungen</dt><dd>Was wurde wo umgesetzt? Mit Version oder Export, soweit für das Setup relevant.</dd></div>
					<div><dt>Abnahmeschritte</dt><dd>Pflichtfelder, Fehlermeldung, erfolgreiche Anfrage und Eingang beim Empfänger prüfen.</dd></div>
					<div><dt>Betrieb &amp; offene Punkte</dt><dd>Zugänge, Zuständigkeiten und bekannte Grenzen für die weitere Betreuung festhalten.</dd></div>
				</dl>
			</div>
			<div class="wl-reference-heading"><h3>Einblick in öffentliche Arbeiten</h3><p>Projektbeispiele aus meinem Portfolio. Keine Agentur-Testimonials.</p></div>
			<div class="wl-references">
				<?php foreach ( $proof_references as $reference ) : ?>
					<article class="wl-reference"><p class="wl-label"><?php echo esc_html( $reference['type'] ); ?></p><h4><a href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $reference['label'] ); ?> <span aria-hidden="true">↗</span><span class="wl-visually-hidden"> (öffnet in neuem Tab)</span></a></h4><p><?php echo esc_html( $reference['copy'] ); ?></p></article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="wl-section wl-dark wl-pricing" id="einstieg" aria-labelledby="wl-pricing-title">
		<div class="nx-container">
			<header class="wl-section-heading wl-section-heading--split"><div><p class="wl-eyebrow">03 / Klein starten. Zusammenarbeit prüfen.</p><h2 id="wl-pricing-title">Erst eine gute Lieferung.<br>Dann der nächste Schritt.</h2></div><p>Ein erstes Projekt zeigt euch, wie ich arbeite. Ein Retainer wird erst relevant, wenn ihr regelmäßig Unterstützung braucht und die Zusammenarbeit passt.</p></header>
			<div class="wl-entry">
				<div class="wl-entry__main"><p class="wl-label">Der kleinste bezahlte Einstieg</p><h3>WordPress-Test-Sprint</h3><p class="wl-price"><?php echo esc_html( $test_sprint_price ); ?></p><p>Eine technische WordPress-Aufgabe, die wir vorab schriftlich abgrenzen. Zum Beispiel einen konkreten Formularfehler beheben oder eine vorhandene Komponente anpassen.</p><a class="wl-button wl-button--light" href="<?php echo esc_url( $wl_form_task_url ); ?>" data-wl-form-link data-track-action="cta_whitelabel_entry_task_brief" data-track-category="lead_gen" data-track-section="entry">Aufgabe für den Einstieg beschreiben <span aria-hidden="true">↗</span></a></div>
				<div class="wl-entry__scope"><h4>Das gehört dazu</h4><ul class="wl-checklist"><li>Schriftlicher Umfang und Abnahmekriterien</li><li>Umsetzung und Funktionstest</li><li>Technische Dokumentation</li><li>Eine Korrekturrunde im vereinbarten Umfang</li></ul><p class="wl-small">Maximal ein Arbeitstag Umsetzungsaufwand; Termin nach Abstimmung. Kein Relaunch, keine vollständige Landingpage oder komplette Tracking-Einrichtung. Lizenzen und Erweiterungen separat.</p></div>
			</div>
			<details class="wl-project-options"><summary>Ihr habt bereits ein größeres Vorhaben? <span aria-hidden="true">+</span></summary><div class="wl-project-options__grid"><?php foreach ( $entry_projects as $project ) : ?><article><h3><?php echo esc_html( $project['title'] ); ?></h3><p class="wl-project-options__price"><?php echo esc_html( $project['price'] ); ?></p><p><?php echo esc_html( $project['copy'] ); ?></p></article><?php endforeach; ?></div><p class="wl-small">Der verbindliche Festpreis folgt nach der Umfangsklärung. Beschreibt euer Vorhaben im selben Formular.</p></details>
			<div class="wl-retainer"><div><p class="wl-label">Wenn aus einem Projekt Zusammenarbeit wird</p><h3>Regelmäßige Weiterentwicklung</h3><p>Prioritäten, Monatskontingent, Verfügbarkeit und Kündigungsbedingungen werden vor Beginn schriftlich vereinbart. Das Antwortfenster ersetzt keine Lieferfrist.</p></div><div class="wl-retainer__price"><strong><?php echo esc_html( $retainer_price ); ?></strong><span>Nach erfolgreichem Erstprojekt</span></div></div>
			<p class="wl-pricing__note">Alle Preise netto zzgl. USt. Euren Kundenpreis kalkuliert ihr selbst. Berücksichtigt dabei neben der Umsetzung auch Projektsteuerung, Vertrieb und weitere eigene Kosten.</p>
		</div>
	</section>

	<section class="wl-section wl-partner" id="zusammenarbeit" aria-labelledby="wl-partner-title">
		<div class="nx-container wl-partner__grid">
			<div class="wl-founder"><img src="<?php echo esc_url( $portrait_url ); ?>" alt="Haşim Üner, WordPress-Entwickler und White-Label-Partner" width="480" height="600" loading="lazy" decoding="async"><div><h3>Haşim Üner</h3><p>WordPress-Entwicklung, Tracking<br>und technische Anfragestrecken.</p><a href="<?php echo esc_url( $about_url ); ?>">Mehr über mich <span aria-hidden="true">↗</span></a></div></div>
			<div><header class="wl-section-heading"><p class="wl-eyebrow">04 / Direkt. Verbindlich. In eurem Team.</p><h2 id="wl-partner-title">Ihr führt den Kunden.<br>Wir klären die Technik.</h2></header>
				<ol class="wl-process"><li><span class="wl-number">01</span><div><h3>Aufgabe &amp; Machbarkeit</h3><p>Ihr beschreibt Vorhaben, Bestand und Wunschzeitraum. Ich melde mich mit Rückfragen und einer ersten Einschätzung zu Passung und Kapazität.</p></div></li><li><span class="wl-number">02</span><div><h3>Umfang &amp; Start</h3><p>Wir vereinbaren Preis, Termin und Abnahme. NDA, benötigte Zugänge und Ansprechpartner stehen vor der Umsetzung fest.</p></div></li><li><span class="wl-number">03</span><div><h3>Umsetzung &amp; Übergabe</h3><p>Ihr prüft die vereinbarte Lieferung. Danach erhaltet ihr Dokumentation und Änderungen in eurem bestehenden Workflow.</p></div></li></ol>
				<details class="wl-working-details"><summary>Wie ich in eurer Agentur auftrete</summary><p>Standardmäßig arbeite ich im Hintergrund. Bei Bedarf bin ich als technischer Ansprechpartner in euren Kundenterminen dabei – nach eurer Freigabe und unter eurem Branding. Vertrag und Rechnung laufen über eure Agentur; keine Akquise in eurem Kundenstamm.</p></details>
				<p class="wl-fit-note"><strong>Passt besonders gut:</strong> Ihr sucht einen direkten technischen Partner für konkrete Projekte oder laufende Weiterentwicklung. Wenn ihr mehrere parallele Entwickler oder eine garantierte Rund-um-die-Uhr-Bereitschaft braucht, klären wir diese Anforderung vor einem Angebot.</p>
			</div>
		</div>
	</section>

	<section class="wl-section wl-faq-section" id="faq" aria-labelledby="wl-faq-title">
		<div class="nx-container wl-faq-layout"><header class="wl-section-heading"><p class="wl-eyebrow">05 / Vor dem ersten Projekt</p><h2 id="wl-faq-title">Die Fragen<br>dahinter.</h2><p>Zugänge, Kapazität, Abrechnung und was nach der Übergabe passiert.</p></header><div class="wl-faq">
			<?php foreach ( $faq_items as $item ) : ?>
				<details class="wl-faq__item" name="hu-faq-whitelabel"><summary id="wl-faq-summary-<?php echo esc_attr( $item['key'] ); ?>" class="wl-faq__summary" aria-controls="wl-faq-answer-<?php echo esc_attr( $item['key'] ); ?>" data-track-action="faq_whitelabel_open" data-track-label="<?php echo esc_attr( $item['key'] ); ?>" data-track-category="engagement" data-track-section="faq"><span><?php echo esc_html( $item['question'] ); ?></span><span class="wl-faq__icon" aria-hidden="true"></span></summary><div id="wl-faq-answer-<?php echo esc_attr( $item['key'] ); ?>" class="wl-faq__answer"><p><?php echo esc_html( $item['answer'] ); ?></p></div></details>
			<?php endforeach; ?>
		</div></div>
	</section>

	<section class="wl-section wl-dark wl-cta" id="naechster-schritt" aria-labelledby="wl-cta-title">
		<div class="nx-container wl-contact-grid">
			<div class="wl-contact-copy"><p class="wl-eyebrow">06 / Der nächste Schritt</p><h2 id="wl-cta-title">Was soll ich<br>euch abnehmen?</h2><p class="wl-lede">Ein paar Sätze reichen. Was soll entstehen oder besser funktionieren? Was ist schon da? Bis wann braucht ihr Unterstützung?</p><div class="wl-response"><strong><?php echo esc_html( $task_brief_response ); ?>.</strong><p>Ich antworte persönlich mit einer ersten Einschätzung oder gezielten Rückfragen. Ein Auftrag entsteht erst durch ein vereinbartes Angebot.</p></div><p>Ihr seid noch in der Angebotsphase?<br><a href="<?php echo esc_url( $wl_form_offer_url ); ?>" data-wl-form-link data-track-action="cta_whitelabel_way_offer" data-track-category="lead_gen" data-track-section="naechster_schritt">Vorhaben zur technischen Einschätzung beschreiben <span aria-hidden="true">↗</span></a></p><p class="wl-small">Lieber direkt sprechen?<br><a href="<?php echo esc_url( $whitelabel_fit_url ); ?>" data-track-action="cta_whitelabel_form_call" data-track-category="lead_gen" data-track-section="naechster_schritt">30 Minuten zur Zusammenarbeit buchen</a></p></div>
			<div class="wl-request" id="aufgabe">
				<div class="wl-request__head"><p class="wl-label" data-wl-case-label aria-live="polite">Konkrete Aufgabe</p><h3>Beschreibt euer Vorhaben.</h3><p>Nur Aufgabe und E-Mail sind Pflichtfelder.</p></div>
				<div id="wl-form-errors" class="wl-request__error-summary is-hidden" role="alert" tabindex="-1" data-wl-error-summary><strong>Bitte prüft eure Angaben.</strong><ul data-wl-error-list></ul></div>
				<form class="wl-request__form" data-wl-request-form action="<?php echo esc_url( $wl_form_endpoint ); ?>" method="post" novalidate>
					<div class="wl-request__honeypot" aria-hidden="true"><label for="wl-company-website">Website</label><input id="wl-company-website" name="company_website" type="text" tabindex="-1" autocomplete="off"></div>
					<input type="hidden" name="case" value="aufgabe" data-wl-case>
					<div class="wl-request__field"><label for="wl-task">Was soll umgesetzt oder geklärt werden? <span>(Pflichtfeld)</span></label><textarea id="wl-task" name="task" rows="5" required minlength="12" maxlength="4000" aria-describedby="wl-task-hint" placeholder="Zum Beispiel: Unser Kunde braucht ein Anfrageformular in WordPress. Die Anfragen sollen ins vorhandene CRM gelangen …"></textarea><p id="wl-task-hint" class="wl-request__hint">Aufgabe, vorhandenes Setup und gewünschtes Ergebnis. Bitte keine Passwörter oder Kundendaten senden.</p></div>
					<div class="wl-request__field"><label for="wl-email">Eure geschäftliche E-Mail <span>(Pflichtfeld)</span></label><input id="wl-email" name="email" type="email" required autocomplete="email" inputmode="email" placeholder="name@agentur.de"></div>
					<div class="wl-request__field"><label for="wl-timeframe">Gewünschter Zeitraum <span>(optional)</span></label><input id="wl-timeframe" name="timeframe" type="text" maxlength="160" placeholder="Zum Beispiel: Start im kommenden Monat" autocomplete="off"></div>
					<details class="wl-request__optional"><summary>Schon Informationen zu den Zugängen? <span>(optional)</span></summary><fieldset class="wl-request__access"><legend>Sind die benötigten Zugänge verfügbar?</legend><div class="wl-request__choices"><?php foreach ( $wl_access_options as $value => $label ) : ?><label for="wl-access-<?php echo esc_attr( $value ); ?>"><input id="wl-access-<?php echo esc_attr( $value ); ?>" name="access" type="radio" value="<?php echo esc_attr( $value ); ?>"><span><?php echo esc_html( $label ); ?></span></label><?php endforeach; ?></div></fieldset></details>
					<button class="wl-button" type="submit" data-wl-submit disabled>Aufgabe senden</button>
					<p class="wl-request__legal">Mit dem Absenden fragt ihr unverbindlich an. Eure Angaben nutze ich zur Beantwortung. Details in der <a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutzerklärung</a>.</p>
					<div class="wl-request__feedback" data-wl-feedback aria-live="polite" role="status" tabindex="-1"></div>
				</form>
				<noscript><p class="wl-request__fallback">Für das Formular ist JavaScript erforderlich. Ihr könnt eure Aufgabe stattdessen per E-Mail schicken.</p></noscript>
				<p class="wl-request__fallback">Auch per E-Mail erreichbar: <a href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a></p>
			</div>
		</div>
	</section>
	<div class="wl-sticky-cta" id="wl-sticky-cta" aria-hidden="true"><a href="<?php echo esc_url( $wl_form_task_url ); ?>" class="wl-button" data-wl-form-link data-track-action="cta_sticky_whitelabel_task_brief" data-track-category="lead_gen" data-track-section="sticky_mobile">Aufgabe beschreiben <span aria-hidden="true">↗</span></a></div>
</div>

<?php
if ( function_exists( 'blocksy_after_current_template' ) ) {
	blocksy_after_current_template();
}

do_action( 'blocksy:content:bottom' );
?>
	</main>
<?php
do_action( 'blocksy:content:after' );
do_action( 'blocksy:footer:before' );
?>
	<footer id="footer" class="wl-page-footer" aria-labelledby="wl-page-footer-heading" role="contentinfo">
		<h2 id="wl-page-footer-heading" class="wl-visually-hidden">Seitenabschluss</h2>
		<div class="nx-container wl-page-footer__inner">
			<nav class="wl-page-footer__site" aria-label="Weitere Seiten">
				<a href="<?php echo esc_url( $wl_home_url ); ?>" rel="home" data-track-action="nav_whitelabel_footer_home" data-track-category="navigation" data-track-section="whitelabel_footer">Startseite</a>
			</nav>
			<p>&copy; <time datetime="<?php echo esc_attr( $current_year ); ?>"><?php echo esc_html( $current_year ); ?></time> Haşim Üner · White-Label-Partner für Agenturen</p>
			<nav class="wl-page-footer__legal" aria-label="Rechtliches">
				<a href="<?php echo esc_url( $imprint_url ); ?>" data-track-action="nav_whitelabel_footer_imprint" data-track-category="navigation" data-track-section="whitelabel_footer">Impressum</a>
				<span aria-hidden="true">·</span>
				<a href="<?php echo esc_url( $privacy_url ); ?>" data-track-action="nav_whitelabel_footer_privacy" data-track-category="navigation" data-track-section="whitelabel_footer">Datenschutz</a>
			</nav>
		</div>
	</footer>
<?php
do_action( 'blocksy:footer:after' );
?>
</div>

<?php wp_footer(); ?>
</body>
</html>
