<?php
/**
 * Targeted one-time hygiene for editor-owned flagship article content.
 *
 * The WordPress editor remains the content owner. This module never replays a
 * repo copy of an article. It only replaces known legacy passages that still
 * reference retired offers/positioning or a superseded proof value.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the current targeted content-hygiene version.
 *
 * @return string
 */
function hu_article_content_hygiene_version() : string {
	return '2026-10-06-2';
}

/**
 * Find a public article by slug without depending on a seeder module.
 *
 * @param string $slug Post slug.
 * @return int
 */
function hu_article_content_hygiene_find_post_id( $slug ) : int {
	$post_ids = get_posts(
		[
			'name'                   => sanitize_title( (string) $slug ),
			'post_type'              => 'post',
			'post_status'            => [ 'publish', 'draft', 'pending', 'future', 'private' ],
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		]
	);

	return ! empty( $post_ids ) ? (int) $post_ids[0] : 0;
}

/**
 * Replace one known legacy passage while tolerating an optional inline link.
 *
 * @param string $content     Current post HTML.
 * @param string $pattern     Unicode-safe PCRE pattern.
 * @param string $replacement Replacement HTML/text.
 * @param int    $count       Replacement count, passed by reference.
 * @return string
 */
function hu_article_content_hygiene_replace_pattern( $content, $pattern, $replacement, &$count ) : string {
	$local_count = 0;
	$updated     = preg_replace( $pattern, $replacement, (string) $content, -1, $local_count );

	if ( null === $updated ) {
		return (string) $content;
	}

	$count += (int) $local_count;
	return (string) $updated;
}

/**
 * Clean the legacy tracking article without overwriting unrelated editor work.
 *
 * Known fixes in this release:
 * - retired Journey-Audit CTAs are removed/re-routed to the active tracking path
 * - the retired WGOS/Owned-First framing becomes the current measurement model
 * - the CPL proof is corrected from 25 EUR to the canonical 22 EUR
 * - the named customer reference becomes the canonical anonymous case label
 * - the time-sensitive headline is replaced only when the exact legacy title
 *   is still present
 *
 * @return void
 */
function hu_maybe_refresh_tracking_article_content() : void {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = hu_article_content_hygiene_version();
	$option_key = 'hu_article_content_hygiene_tracking_version';

	if ( (string) get_option( $option_key, '' ) === $version ) {
		return;
	}

	$post_id = hu_article_content_hygiene_find_post_id( 'server-side-tracking-gtm' );
	if ( $post_id <= 0 ) {
		return;
	}

	$current_content = (string) get_post_field( 'post_content', $post_id );
	$current_title   = (string) get_post_field( 'post_title', $post_id );
	$content         = $current_content;
	$replacement_count = 0;

	$tracking_url = home_url( '/server-side-tracking-b2b/' );
	$case_url     = function_exists( 'nexus_get_primary_public_url' )
		? nexus_get_primary_public_url( 'e3', home_url( '/case-study-solar-leadgenerierung/' ) )
		: home_url( '/case-study-solar-leadgenerierung/' );

	$content = hu_article_content_hygiene_replace_pattern(
		$content,
		'~Wie groß der Datenverlust auf Ihrer Website tatsächlich ist, zeigt unser\s*(?:<a\b[^>]*>)?kostenloser Journey Audit(?:</a>)?\s*— inklusive einer konservativen Euro-Schätzung des monatlichen Revenue Gap\.~u',
		'Wie belastbar Ihre Messkette ist, lässt sich nicht seriös mit einem pauschalen Verlust-Prozentsatz beantworten. Prüfen Sie Browser-Events, Consent, Server-Container und CRM-Abgleich gemeinsam.',
		$replacement_count
	);

	$content = hu_article_content_hygiene_replace_pattern(
		$content,
		'~Das ist einer der häufigsten „unsichtbaren“ Umsatzbremsen, die wir in unserem\s*(?:<a\b[^>]*>)?Journey Audit(?:</a>)?\s*identifizieren: Der Kunde durchläuft die Customer Journey, konvertiert — aber das Tracking erfasst es nicht\.~u',
		'Das ist ein typisches Messproblem: Ein Nutzer konvertiert, aber das entscheidende Conversion-Signal kommt nicht vollständig oder nicht korrekt in Ads, Analytics oder CRM an.',
		$replacement_count
	);

	$content = hu_article_content_hygiene_replace_pattern(
		$content,
		'~Im\s+(?:<a\b[^>]*>)?E3 New Energy Case(?:</a>)?\s+konnten wir die Cost-per-Lead von 150\s*€ auf 25\s*€ senken\s*—\s*unter anderem durch saubere Datengrundlagen, die erst durch korrektes Tracking möglich wurden\. Ohne verlässliche Conversion-Daten wäre diese Optimierung ein Blindflug gewesen\.~u',
		sprintf(
			'Im <a href="%1$s">dokumentierten Fall eines mittelständischen PV-Installationsbetriebs</a> sank der Cost-per-Lead von 150 € auf 22 €. Tracking war dabei nicht der alleinige Hebel, sondern die Messgrundlage, um Kampagnen, Landingpages und Leadqualität sauber gegeneinander zu bewerten.',
			esc_url( $case_url )
		),
		$replacement_count
	);

	// Ein frueherer Durchlauf dieser Routine hat den Betriebsnamen selbst in
	// den Artikel geschrieben. Der Fall heisst domainweit "ein
	// mittelständischer PV-Installationsbetrieb", also wird der Name auch dort
	// ersetzt, wo das Muster oben ihn nicht mehr findet. Laeuft nach dem
	// Muster oben, damit es dessen Treffer nicht vorher zerstoert.
	$content = hu_article_content_hygiene_replace_pattern(
		$content,
		'~E3 New Energy Case~u',
		'dokumentierten Fall eines mittelständischen PV-Installationsbetriebs',
		$replacement_count
	);

	// Fallback for editor markup variants where only the numeric proof is stable.
	$numeric_count = 0;
	$content       = str_replace( '150 € auf 25 €', '150 € auf 22 €', $content, $numeric_count );
	$replacement_count += (int) $numeric_count;

	$owned_first_count = 0;
	$content = str_replace(
		'Server-Side Tracking als Teil des Owned-First-Systems',
		'Server-Side Tracking als Teil einer belastbaren Messkette',
		$content,
		$owned_first_count
	);
	$replacement_count += (int) $owned_first_count;

	$owned_first_body_count = 0;
	$content = str_replace(
		'Es ist ein zentraler Baustein einer Owned-First-Strategie — dem Ansatz, eigene Kanäle zu optimieren, bevor man Werbebudgets skaliert.',
		'Es ist ein Baustein einer belastbaren Messkette: eigene Datenpunkte sauber erfassen, Consent respektieren und Ergebnisse bis ins CRM zurückführen, bevor Budgets skaliert werden.',
		$content,
		$owned_first_body_count
	);
	$replacement_count += (int) $owned_first_body_count;

	$content = hu_article_content_hygiene_replace_pattern(
		$content,
		'~In unserem\s*(?:<a\b[^>]*>)?WordPress Growth Operating System \(WGOS\)(?:</a>)?\s*behandeln wir Tracking als eine von vier Säulen:~u',
		'Für belastbare Messketten behandeln wir Tracking nicht isoliert, sondern zusammen mit vier Ebenen:',
		$replacement_count
	);

	$next_step_heading_count = 0;
	$content = str_replace(
		'Nächster Schritt: Wie viel Datenverlust verursacht Ihr aktuelles Setup?',
		'Nächster Schritt: Messkette statt Einzeltool prüfen',
		$content,
		$next_step_heading_count
	);
	$replacement_count += (int) $next_step_heading_count;

	$content = hu_article_content_hygiene_replace_pattern(
		$content,
		'~Unser kostenloser Customer Journey Audit simuliert die komplette Reise Ihres nächsten Kunden — von der Google-Suche bis zum Lead-Formular\. Sie sehen, wo Interessenten abspringen und was das monatlich in Euro kostet\.~u',
		'Wenn Sie Server-Side Tracking sauber einordnen wollen, prüfen Sie zuerst die gesamte Messkette: Consent, Browser-Events, Server-Container, Ads-Plattformen und CRM-Rückmeldung. Erst danach lässt sich entscheiden, ob ein technischer Umbau wirklich der nächste sinnvolle Schritt ist.',
		$replacement_count
	);

	$content = hu_article_content_hygiene_replace_pattern(
		$content,
		'~<a\b[^>]*>\s*Kostenlosen Journey Audit starten\s*→\s*</a>~u',
		sprintf( '<a href="%1$s">Tracking-Setup ansehen →</a>', esc_url( $tracking_url ) ),
		$replacement_count
	);

	// Final-Cut 2026-10: Der Artikel ist technischer Supporting Content.
	// Kaufintent, Preise und Anbieter-Entscheidung gehoeren ausschliesslich der
	// Money Page /server-side-tracking-b2b/. Gleichzeitig werden pauschale,
	// unbelegte Prozent-, Compliance- und ROI-Versprechen aus dem Altartikel
	// entfernt. Die technische Einordnung folgt damit dem aktuellen Proof-Standard.
	$final_cut_claim_count = 0;
	$content = str_replace(
		[
			'In Deutschland lehnen durchschnittlich 40 % der Website-Besucher das Cookie-Banner ab. Safari löscht Tracking-Cookies nach spätestens sieben Tagen, oft bereits nach 24 Stunden. Ad-Blocker verhindern bei rund 30 % der Desktop-Nutzer, dass Tracking-Skripte überhaupt laden. Das bedeutet: Die Daten, auf deren Basis Sie Ihre Marketingentscheidungen treffen, bilden bestenfalls 60 % der Realität ab.',
			'Im B2B zählt jeder einzelne Lead. Wenn Sie 50 Leads pro Monat generieren und Ihr Tracking 20–40 % davon nicht erfasst, fehlen dem Google-Ads-Algorithmus 10–20 Conversion-Signale. Er lernt auf einem verzerrten Datensatz, optimiert falsch — und Ihr CPA steigt, ohne dass Sie den Grund erkennen.',
			'Fünf messbare Vorteile von Server-Side Tracking',
			'1. Deutlich mehr erfasste Conversions',
			'Da das Tracking von Ihrer eigenen Domain kommt (First-Party-Kontext), wird es von Ad-Blockern und Browser-Restriktionen seltener blockiert. Unternehmen berichten nach dem Umstieg konsistent von 15 bis 55 % mehr erfassten Conversions. Bei Facebook-Tracking kann die Genauigkeit von unter 65 % auf über 90 % steigen.',
			'2. Bessere Kampagnenperformance und niedrigere CPL',
			'Mehr erfasste Conversions bedeuten nicht nur bessere Berichte — sie füttern die Algorithmen von Google Ads und Meta Ads mit vollständigeren Daten. Der Algorithmus lernt auf einem repräsentativeren Datensatz und kann dadurch besser optimieren. Das Ergebnis: niedrigere Cost-per-Lead und besserer ROAS.',
			'3. Volle Datenkontrolle und DSGVO-Compliance',
			'Jedes Tracking-Skript, das im Browser lädt, kostet Ladezeit. Google Analytics, Meta Pixel, LinkedIn Insight Tag — zusammen können das schnell 500 KB bis 1 MB JavaScript sein. Beim Server-Side Tracking wird nur ein schlankes Skript im Browser ausgeführt, der Rest passiert serverseitig.',
			'Das verbessert direkt Ihre Core Web Vitals — und damit auch Ihr Google-Ranking. Denn Ladezeit ist ein Ranking-Faktor und beeinflusst die Conversion-Rate: Jede Sekunde über ~3 Sekunden LCP kostet messbar Leads.',
			'Consent Mode V2: In Kombination mit Googles Consent Mode V2 können cookielose Pings gesendet werden, die den Werbe-Algorithmen aggregierte Signale liefern — ohne personenbezogene Daten zu übertragen. So bekommt der Algorithmus ein vollständigeres Bild, ohne den Datenschutz zu verletzen.',
			'EU-Hosting: Wenn Ihr Server-Container auf einem europäischen Server läuft (z. B. Google Cloud in Frankfurt), verlassen die Daten Europa nur, wenn Sie es explizit erlauben.',
			'Server-Side Tracking verbessert die Qualität der Daten, die Sie mit Einwilligung erheben. Wer einen guten Consent-Banner hat, bekommt durch Server-Side Tracking aus den vorhandenen Opt-ins deutlich mehr heraus. Die 60 % der Nutzer, die zustimmen, liefern mit Server-Side Tracking vollständigere und genauere Daten als vorher.',
			'Das ist der Schlüssel, damit Ad-Blocker und Browser-Restriktionen Ihr Tracking nicht mehr blockieren.',
			'Server-Side Tracking ist besonders sinnvoll, wenn Sie regelmäßig Werbebudget einsetzen (ab ca. 500 € monatlich lohnt sich die Investition fast immer), wenn Ihre Cookie-Banner-Ablehnungsrate hoch ist (in Deutschland typisch: 30–50 %), wenn Sie auf präzise Conversion-Daten für Kampagnenoptimierung angewiesen sind und wenn DSGVO-Compliance für Sie mehr als ein Lippenbekenntnis ist.',
			'Stape/TAGGRS: Ab ca. 20–40 €/Monat, je nach Event-Volumen. Transparente Preise, ideal für KMUs.',
			'Google Cloud Platform: Variabel, typisch 25–50 €/Monat für mittelgroße Websites. Erfordert technisches Know-how.',
			'Die initiale Einrichtung durch einen spezialisierten Consultant liegt typisch zwischen 1.500 und 5.000 €, abhängig von der Komplexität Ihres bestehenden Tracking-Setups und der Anzahl der angebundenen Plattformen.',
			'Rechnen Sie es durch: Wenn Sie 5.000 €/Monat in Google Ads investieren und durch Server-Side Tracking 20 % mehr Conversions erfassen, bekommt der Algorithmus bessere Signale. Das kann Ihren CPA um 10–20 % senken — bei 5.000 € Budget sind das 500–1.000 € Ersparnis pro Monat. Die Hosting-Kosten amortisieren sich in der Regel innerhalb des ersten Monats.',
			'Die Frage ist nicht mehr „Server-Side Tracking ja oder nein?“, sondern: Wie schnell können wir umstellen?',
			'Ja — vorausgesetzt, die Einwilligung der Nutzer wird korrekt eingeholt und die Datenverarbeitung transparent dokumentiert. Server-Side Tracking ersetzt nicht die Consent-Pflicht, verbessert aber die Kontrolle über Datenflüsse erheblich. In Kombination mit EU-Hosting und Consent Mode V2 ist es eine der datenschutzfreundlichsten Tracking-Methoden.',
			'Die Hosting-Kosten liegen bei Anbietern wie Stape oder TAGGRS zwischen 20 und 40 € pro Monat. Bei Google Cloud Platform sind es typisch 25–50 €. Dazu kommen einmalige Setup-Kosten zwischen 1.500 und 5.000 €, abhängig von der Komplexität.',
			'Besonders. Im B2B sind Leads wertvoller und Entscheidungszyklen länger. Jedes nicht erfasste Conversion-Signal verschlechtert die Kampagnenoptimierung. Ab ca. 500 € monatlichem Werbebudget amortisiert sich die Investition in der Regel innerhalb des ersten Monats.',
			'Kostenlosen Journey Audit starten →',
			'60 Sekunden · Kein Konto · Kein Sales-Call',
		],
		[
			'Wie groß die Messlücke ist, lässt sich nicht aus allgemeinen Prozentwerten ableiten. Browser, Consent-Konfiguration, Event-Definitionen und Plattformen wirken je Setup unterschiedlich. Deshalb ist der belastbare Ausgangspunkt ein Paralleltest in den eigenen Konten.',
			'Im B2B ist nicht die theoretische Zahl verlorener Events entscheidend, sondern ob dieselbe Anfrage in Browser, Server-Container, Ads-Plattform und CRM konsistent wiederzufinden ist. Genau diese Kette muss geprüft werden.',
			'Fünf technische Hebel von Server-Side Tracking',
			'1. Messsignale kontrollierter verarbeiten',
			'Ein First-Party-Endpunkt kann die Messarchitektur robuster machen und gibt Ihnen mehr Kontrolle darüber, welche Daten weitergeleitet werden. Wie stark sich die gemessene Datenmenge verändert, muss im eigenen Setup per Paralleltest ermittelt werden.',
			'2. Konsistentere Signale für Kampagnen',
			'Server-Side Tracking kann die Datenqualität verbessern, garantiert aber weder niedrigere Leadkosten noch höheren ROAS. Entscheidend ist, ob Events sauber definiert, dedupliziert und den richtigen Conversion-Zielen zugeordnet sind.',
			'3. Mehr Kontrolle über den Datenfluss',
			'Serverseitiges Tagging kann Verarbeitung aus dem Browser verlagern und damit die Client-Last reduzieren. Der tatsächliche Performance-Effekt hängt jedoch davon ab, welche Bibliotheken und Requests clientseitig verbleiben und muss auf der konkreten Website gemessen werden.',
			'Weniger Client-Verarbeitung kann die technische Performance unterstützen. Ob sich Core Web Vitals messbar verbessern, ist eine Frage des konkreten Setups und wird nicht aus dem Tracking-Modell allein abgeleitet.',
			'Consent Mode V2 übermittelt Einwilligungszustände an unterstützte Google-Tags und beeinflusst deren Verhalten. Er ersetzt weder die Einwilligung noch das Consent-Management; welche Requests und Daten verarbeitet werden, hängt von der konkreten Konfiguration ab.',
			'EU-Hosting kann ein Baustein der technischen und organisatorischen Gestaltung sein. Es sagt allein aber nicht, welche Daten anschließend an Google, Meta oder andere Empfänger weitergeleitet werden; dafür ist die tatsächliche Tag- und Consent-Konfiguration maßgeblich.',
			'Server-Side Tracking kann die Datenverarbeitung besser kontrollierbar machen. Ob dadurch mehr nutzbare Signale entstehen, wird im Parallelbetrieb gegen die bisherige Messung geprüft — ohne pauschale Quote.',
			'Eine eigene Domain bzw. Subdomain schafft einen First-Party-Kontext und kann Vorteile bei servergesetzten Cookies bieten. Sie ist jedoch keine Garantie dafür, dass jeder Browser oder Blocker jeden Request akzeptiert.',
			'Server-Side Tracking ist sinnvoll, wenn ein konkretes Messproblem besteht: relevante Paid-Kampagnen, mehrere Conversion-Strecken, Anforderungen an Deduplizierung oder CRM-/Offline-Signale. Ohne klar definierte Conversions und operative Nutzung der Daten ist zusätzliche Infrastruktur nicht automatisch die richtige Priorität.',
			'Die laufenden Hosting-Kosten hängen von Anbieter, Region, Event-Volumen und Betriebsmodell ab. Für die Entscheidung zählt deshalb nicht ein allgemeiner Marktpreis, sondern der benötigte technische Scope.',
			'Google Cloud und spezialisierte Hosting-Anbieter haben unterschiedliche Betriebs- und Kostenmodelle. Die Infrastruktur wird vor dem Setup passend zu Volumen, Eigentum und Wartungsbedarf gewählt.',
			sprintf( 'Für die Umsetzung gilt kein pauschaler Marktpreis. Den aktuellen Leistungsumfang und die kanonischen Preise finden Sie auf der <a href="%1$s">Server-Side-Tracking-Seite</a>.', esc_url( $tracking_url ) ),
			'Ein seriöser ROI lässt sich vor der Messung nicht aus einer allgemeinen Prozentzahl berechnen. Im Paralleltest werden Event-Vollständigkeit, Dubletten, Plattformabweichungen und gegebenenfalls CRM-Rücksignale verglichen; erst daraus entsteht eine belastbare wirtschaftliche Einordnung.',
			'Die richtige Frage lautet: Welches konkrete Messproblem soll Server-Side Tracking lösen — und lässt sich dieser Effekt im Paralleltest nachweisen?',
			'Nicht automatisch. Server-Side Tracking verändert die technische Verarbeitung, ersetzt aber weder Einwilligung noch rechtliche Prüfung. Consent-Signale, Datenminimierung, Empfänger und Verträge müssen zum konkreten Setup passen.',
			sprintf( 'Hosting und Setup hängen vom technischen Umfang ab. Aktuelle Paketpreise und die Abgrenzung zwischen Browser-Messung und Server-Side finden Sie auf der <a href="%1$s">Leistungsseite</a>.', esc_url( $tracking_url ) ),
			'Nicht pauschal. Im B2B kann Server-Side Tracking sinnvoll sein, wenn relevante Paid-Kampagnen, mehrere Conversion-Wege oder CRM-/Offline-Signale vorliegen. Die Entscheidung folgt dem Messproblem, nicht einem festen Mindestbudget.',
			'Server-Side-Setup prüfen →',
			'Leistungsumfang, Preise und Abnahme ansehen.',
		],
		$content,
		$final_cut_claim_count
	);
	$replacement_count += (int) $final_cut_claim_count;

	// Der historische Zwischenstand trug die alte Systembezeichnung noch in
	// einer leicht abweichenden Fassung. Diese Variante wird separat entfernt.
	$owned_first_final_count = 0;
	$content = str_replace(
		'Es ist ein zentraler Baustein einer Owned-First-Strategie — dem Ansatz, eigene Kanäle zu optimieren, bevor man Werbebudgets skaliert.',
		'Es ist ein Baustein einer belastbaren Messkette: Consent, Browser-Events, Server-Verarbeitung und CRM-Rücksignale werden als ein System geprüft.',
		$content,
		$owned_first_final_count
	);
	$replacement_count += (int) $owned_first_final_count;

	// Editor-HTML hardening: dieselben Altclaims koennen durch <strong>,
	// <em>, &nbsp; oder Link-Markup unterbrochen sein. Deshalb werden die
	// bekannten problematischen Paragraphen auf HTML-Ebene robust ersetzt.
	$html_hardening = [
		[
			'~<p>In Deutschland lehnen durchschnittlich 40 %.*?bilden bestenfalls 60 % der Realität ab\.</strong></p>~us',
			'<p>Wie groß eine Messlücke tatsächlich ist, lässt sich nicht aus allgemeinen Prozentwerten ableiten. Browser, Consent-Konfiguration, Event-Definitionen und Plattformen wirken je Setup unterschiedlich. Deshalb ist der belastbare Ausgangspunkt ein Paralleltest in den eigenen Konten.</p>',
		],
		[
			'~<p>Da das Tracking von Ihrer eigenen Domain kommt.*?15 bis 55 % mehr erfassten Conversions.*?</p>~us',
			'<p>Ein First-Party-Endpunkt kann die Messarchitektur robuster machen und gibt Ihnen mehr Kontrolle darüber, welche Daten weitergeleitet werden. Wie stark sich die gemessene Datenmenge verändert, muss im eigenen Setup per Paralleltest ermittelt werden.</p>',
		],
		[
			'~<p><strong>Consent Mode V2:</strong>.*?</p>~us',
			'<p><strong>Consent Mode V2:</strong> Consent Mode übermittelt Einwilligungszustände an unterstützte Google-Tags und beeinflusst deren Verhalten. Er ersetzt weder die Einwilligung noch das Consent-Management; welche Requests und Daten verarbeitet werden, hängt von der konkreten Konfiguration ab.</p>',
		],
		[
			'~<p><strong>EU-Hosting:</strong>.*?</p>~us',
			'<p><strong>EU-Hosting:</strong> Der Standort des Tagging-Servers ist nur ein Teil der Architektur. Entscheidend bleibt, welche Daten anschließend an Google, Meta oder andere Empfänger weitergegeben werden und welche Consent- und Vertragsgrundlagen dafür gelten.</p>',
		],
		[
			'~<p>Server-Side Tracking ist besonders sinnvoll, wenn Sie.*?DSGVO-Compliance.*?</p>~us',
			'<p>Server-Side Tracking ist sinnvoll, wenn ein konkretes Messproblem besteht: relevante Paid-Kampagnen, mehrere Conversion-Strecken, Anforderungen an Deduplizierung oder CRM-/Offline-Signale. Ohne klar definierte Conversions und operative Nutzung der Daten ist zusätzliche Infrastruktur nicht automatisch die richtige Priorität.</p>',
		],
		[
			'~<p><strong>Stape/TAGGRS:</strong>.*?</p>~us',
			'<p><strong>Spezialisierte Hosting-Anbieter:</strong> Kosten und Betriebsmodelle hängen von Region, Event-Volumen und Funktionsumfang ab. Für die Auswahl zählt der technische Scope, nicht ein pauschaler Marktpreis.</p>',
		],
		[
			'~<p><strong>Google Cloud Platform:</strong>.*?</p>~us',
			'<p><strong>Google Cloud:</strong> Der Aufwand hängt von Architektur, Skalierung und Betrieb ab. Infrastruktur und Eigentum werden deshalb vor der Implementierung festgelegt.</p>',
		],
		[
			'~<p>Wer heute seine eigene Dateninfrastruktur aufbaut, investiert nicht nur in Compliance.*?</p>~us',
			'<p>Der Nutzen liegt in kontrollierbarer Verarbeitung, klar definierten Events und einer prüfbaren Messkette. Ob daraus wirtschaftlicher Mehrwert entsteht, zeigt erst der Vergleich mit den eigenen Ausgangsdaten.</p>',
		],
		[
			'~<p>Die Frage ist nicht mehr „Server-Side Tracking ja oder nein\?"?, sondern:.*?</p>~us',
			'<p>Die richtige Frage lautet deshalb: <strong>Welches konkrete Messproblem soll Server-Side Tracking lösen — und lässt sich dieser Effekt im Paralleltest nachweisen?</strong></p>',
		],
		[
			'~<p><a href="https://hasimuener\.de/solar-waermepumpen-leadgenerierung/#marktcheck"><strong>Server-Side-Setup prüfen →</strong></a></p>~u',
			sprintf( '<p><a href="%1$s"><strong>Server-Side-Setup prüfen →</strong></a></p>', esc_url( $tracking_url ) ),
		],
	];

	foreach ( $html_hardening as $rule ) {
		$content = hu_article_content_hygiene_replace_pattern(
			$content,
			$rule[0],
			$rule[1],
			$replacement_count
		);
	}

	$new_title = $current_title;
	if (
		in_array(
			$current_title,
			[
				'Server-Side Tracking mit GTM: Warum deutsche B2B-Unternehmen jetzt umstellen müssen',
				'Server-Side Tracking mit GTM: Setup, Consent und saubere Messketten',
			],
			true
		)
	) {
		$new_title = 'Server-Side Tracking mit GTM: Architektur, Consent und Paralleltest';
	}

	$needs_update = $content !== $current_content || $new_title !== $current_title;

	if ( $needs_update ) {
		$result = wp_update_post(
			wp_slash(
				[
					'ID'           => $post_id,
					'post_title'   => $new_title,
					'post_content' => $content,
				]
			),
			true
		);

		if ( is_wp_error( $result ) || ! $result ) {
			return;
		}

		update_post_meta( $post_id, '_hu_article_content_hygiene_version', $version );
		update_post_meta( $post_id, '_hu_article_content_hygiene_replacements', (string) $replacement_count );
	}

	// If none of the known legacy markers remain, the editor is already clean.
	$legacy_markers = [
		'150 € auf 25 €',
		'Journey Audit',
		'Customer Journey Audit',
		'WordPress Growth Operating System (WGOS)',
		'Owned-First-Systems',
		'Owned-First-Strategie',
		'bilden bestenfalls 60 % der Realität ab',
		'15 bis 55 % mehr erfassten Conversions',
		'DSGVO-Compliance',
		'ab ca. 500 € monatlich lohnt sich die Investition fast immer',
		'amortisieren sich in der Regel innerhalb des ersten Monats',
		'Kostenlosen Journey Audit starten',
	];

	$remaining_content = $needs_update ? $content : $current_content;
	foreach ( $legacy_markers as $marker ) {
		if ( false !== strpos( $remaining_content, $marker ) ) {
			return;
		}
	}

	update_option( $option_key, $version, false );
}
add_action( 'init', 'hu_maybe_refresh_tracking_article_content', 42 );
