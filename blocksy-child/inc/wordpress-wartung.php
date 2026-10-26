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
				'%1$s netto im Monat, für WooCommerce-Shops %2$s netto. Der Anfrage-Test ist in beiden Fällen enthalten, es gibt kein Paket, in dem er fehlt. Vor dem Start steht der Übernahme-Check für %3$s netto; er wird angerechnet, wenn Sie die Wartung buchen.',
				hu_maintenance_price(),
				hu_maintenance_price( true ),
				hu_freelancer_takeover_check_price()
			),
		],
		[
			'question' => 'Was gehört zu einer WordPress-Wartung?',
			'answer'   => 'Mindestens Updates von Kern, Theme und Plugins, Backups und eine Überwachung der Erreichbarkeit. Sinnvoll ist außerdem eine Prüfung danach, ob noch alles funktioniert. Hier gehören dazu eine echte Testanfrage über Ihr Formular und die Kontrolle, ob die Anfrage gemessen wird.',
		],
		[
			'question' => 'Mein Hoster spielt Updates automatisch ein. Reicht das?',
			'answer'   => 'Nein. Automatische Updates prüft danach niemand. Ob das Formular noch zustellt oder der Cookie-Banner die Messung blockiert, merkt dann keiner.',
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
			'question' => 'Ist das Ergebnis für meine Website öffentlich zu sehen?',
			'answer'   => 'Nein. Ihr Ergebnis steht nur in Ihrem Monatsbericht, den Sie per Mail bekommen. Öffentlich ist nur der Anfrage-Monitor meiner eigenen Website. Er zeigt, dass die Prüfung funktioniert.',
		],
		[
			'question' => 'Was ist der Unterschied zwischen Wartung und Betreuung?',
			'answer'   => sprintf( 'Die Wartung hält die Technik aktuell und prüft jeden Monat, ob Anfragen ankommen. Die Betreuung umfasst Änderungen an Inhalten und Funktionen, zum Beispiel neue Seiten oder Texte. Sie läuft getrennt über ein Stundenkontingent: %s im Monat.', hu_freelancer_retainer_display() ),
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
