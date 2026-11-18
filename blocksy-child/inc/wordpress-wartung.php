<?php
/**
 * WordPress-Wartung mit Anfrage-Test: Route-Erkennung und gemeinsame Inhalte.
 *
 * Template (page-wordpress-wartung.php) und Schema (org-schema.php) lesen die
 * Fragen aus derselben Quelle, damit sichtbare und strukturierte Antworten
 * nicht auseinanderlaufen. Preise und Zusagen kommen aus pricing-canon.php,
 * die Antwortzeit auf eine Anfrage aus messaging-canon.php.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current request renders the maintenance page.
 *
 * @return bool
 */
function hu_is_maintenance_page() {
	return is_page( 'wordpress-wartung' ) || is_page_template( 'page-wordpress-wartung.php' );
}

/**
 * Visible FAQ of the maintenance page; also the FAQPage schema source.
 *
 * @return array<int, array{question: string, answer: string}>
 */
function hu_maintenance_faq_items() {
	return [
		[
			'question' => 'Was kostet die WordPress-Wartung?',
			'answer'   => sprintf(
				'Eine WordPress-Website kostet %1$s netto im Monat, ein WooCommerce-Shop %2$s netto im Monat. Der Anfrage-Test ist in beiden Varianten enthalten. Der einmalige Übernahme-Check kostet %3$s netto und wird bei Buchung der Wartung vollständig angerechnet.',
				hu_maintenance_price(),
				hu_maintenance_price( true ),
				hu_freelancer_takeover_check_price()
			),
		],
		[
			'question' => 'Was gehört zu einer WordPress-Wartung?',
			'answer'   => 'Das Angebot umfasst Updates, tägliche externe Backups, die Überwachung von Erreichbarkeit und SSL sowie einen Monatsbericht. Jeden Monat prüfe ich mit einer echten Testanfrage, ob Ihr Formular zustellt und ob das eingerichtete Conversion-Tracking sie erfasst.',
		],
		[
			'question' => 'Mein Hoster spielt Updates automatisch ein. Reicht das?',
			'answer'   => 'Die Update-Funktion übernimmt das Einspielen. Fragen Sie Ihren Hoster, ob danach auch eine Testanfrage, der Eingang im richtigen Postfach oder CRM und das Conversion-Ereignis geprüft werden.',
		],
		[
			'question' => 'Wie oft werden Updates eingespielt?',
			'answer'   => sprintf( 'Sicherheitsupdates werktags innerhalb von %d Stunden, alle anderen wöchentlich. Vor jedem Update entsteht ein Backup.', HU_MAINTENANCE_SECURITY_UPDATE_HOURS ),
		],
		[
			'question' => 'Was passiert, wenn die Website ausfällt oder gehackt wird?',
			'answer'   => sprintf( 'Ich reagiere %s und stelle aus dem Backup wieder her. Eine tiefere Bereinigung nach einem Angriff rechne ich nach Aufwand ab. Einen Notdienst nachts, am Wochenende oder an Feiertagen gibt es nicht.', hu_maintenance_outage_promise() ),
		],
		[
			'question' => 'Wie oft wird überwacht und wer sieht die Ergebnisse?',
			'answer'   => 'Erreichbarkeit und SSL-Zertifikat werden laufend überwacht. Der vollständige Anfrage-Test für Ihre Website läuft monatlich. Die Ergebnisse erhalten Sie privat per E-Mail. Der öffentliche Anfrage-Monitor zeigt die wöchentlichen Testläufe meiner eigenen Website.',
		],
		[
			'question' => 'Was ist der Unterschied zwischen Wartung und Betreuung?',
			'answer'   => sprintf( 'Die Wartung hält die Technik aktuell und prüft jeden Monat, ob Anfragen ankommen. Die Betreuung umfasst Änderungen an Inhalten und Funktionen, zum Beispiel neue Seiten oder Texte. Sie läuft getrennt über ein Stundenkontingent: %s im Monat.', hu_freelancer_retainer_display() ),
		],
		[
			'question' => 'Welche Leistungen werden separat berechnet?',
			'answer'   => 'Hosting und Plugin-Lizenzen laufen über Ihr Konto. Neue Inhalte und Funktionen vereinbaren wir separat. Eine weitergehende Bereinigung nach einem Angriff rechne ich nach Aufwand ab. Ihre Zugänge, Backups und Berichte bleiben bei Ihnen.',
		],
		[
			'question' => 'Gibt es eine Mindestlaufzeit?',
			'answer'   => 'Nein. Es ist ein Wartungsvertrag ohne Mindestlaufzeit, zum Monatsende kündbar. Zugänge, Backups und Bericht bleiben bei Ihnen.',
		],
		[
			'question' => 'Übernehmen Sie auch Websites, die jemand anderes gebaut hat?',
			'answer'   => 'Ja, nach dem Übernahme-Check. Er klärt Zugänge, Backups, Plugins, Formular und Messung, bevor ich Verantwortung übernehme. Pagebuilder wie Elementor sind kein Ausschluss.',
		],
	];
}
