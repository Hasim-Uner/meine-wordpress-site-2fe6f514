<?php
/**
 * Audit CRM and intake funnel for the marketcheck.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the internal review status map.
 *
 * @return array<string, string>
 */
function nexus_get_review_status_options() {
	return [
		'new'       => 'Neu',
		'in_review' => 'In Bearbeitung',
		'sent'      => 'Rückmeldung gesendet',
		'won'       => 'Gespräch / Auftrag',
		'archived'  => 'Archiviert',
	];
}

/**
 * Return the priority map for review requests.
 *
 * @return array<string, string>
 */
function nexus_get_review_priority_options() {
	return [
		'normal' => 'Normal',
		'high'   => 'Hoch',
	];
}

/**
 * Return the selectable focus areas from the intake form.
 *
 * @return array<string, string>
 */
function nexus_get_review_focus_area_options() {
	return [
		'seo_visibility'            => 'SEO / Sichtbarkeit',
		'performance_cwv'           => 'Performance / Core Web Vitals',
		'conversion_inquiry_flow'   => 'Conversion / Anfrageführung',
		'tracking_data_quality'     => 'Tracking / Datenqualität',
		'positioning_page_message'  => 'Positionierung / Seitenbotschaft',
		'not_sure_yet'              => 'Ich bin mir noch nicht sicher',
	];
}

/**
 * Return the selectable primary goals from the intake form.
 *
 * @return array<string, string>
 */
function nexus_get_review_primary_goal_options() {
	return [
		'more_qualified_inquiries'         => 'mehr qualifizierte Anfragen',
		'clearer_positioning'              => 'klarere Positionierung',
		'better_google_visibility'         => 'bessere Sichtbarkeit bei Google',
		'better_user_guidance_conversion'  => 'bessere Nutzerführung / Conversion',
		'better_technical_quality'         => 'bessere technische Qualität',
		'clearer_decision_data'            => 'klarere Datenbasis für Entscheidungen',
	];
}

/**
 * Return the supported audit request types.
 *
 * @return array<string, string>
 */
function nexus_get_audit_request_type_options() {
	return [
		'growth_audit'     => 'Marktcheck',
		'growth_blueprint' => 'Growth Blueprint',
		'implementation'   => 'Umsetzung / Weiterentwicklung',
	];
}

/**
 * Return the public label for the energy-systems intake variant.
 *
 * @return string
 */
function nexus_get_energy_intake_variant_label() {
	return 'Branchen-Landingpage: Solar / Wärmepumpe / Speicher';
}

/**
 * Check whether the request comes from the simplified marketcheck landing page.
 *
 * @param string $variant Intake variant slug.
 * @return bool
 */
function nexus_is_growth_audit_simple_intake_variant( $variant ) {
	return 'growth_audit_simple' === sanitize_key( (string) $variant );
}

/**
 * Return the public label for the simplified marketcheck intake.
 *
 * @return string
 */
function nexus_get_growth_audit_simple_intake_variant_label() {
	return 'Marktcheck Landingpage';
}

/**
 * Return field option definitions for the energy-systems intake.
 *
 * @return array<string, array<string, array<string, string>>>
 */
function nexus_get_energy_intake_field_options() {
	return [
		'lead_volume' => [
			'unter_20'    => [
				'label'       => 'Unter 20 pro Monat',
				'description' => 'Vertrieb hat freie Kapazität, aber zu wenige qualifizierte Anfragen kommen rein.',
			],
			'20_bis_50'   => [
				'label'       => '20–50 pro Monat',
				'description' => 'Volumen vorhanden, aber unklar, welcher Anteil tragend ist und welcher verpufft.',
			],
			'51_bis_120'  => [
				'label'       => '51–120 pro Monat',
				'description' => 'Stabiler Anfrage-Korridor — der Hebel liegt bei Qualität, Zuordnung und Eigentum.',
			],
			'ueber_120'   => [
				'label'       => 'Über 120 pro Monat',
				'description' => 'Hohes Volumen — Skalierung greift nur, wenn Tracking, Datenebene und Vertriebsfluss sauber sind.',
			],
		],
		'cpl_range' => [
			'unter_80'    => [
				'label'       => 'Unter 80 €',
				'description' => 'Niedriger CPL — meist organisch, Empfehlungen oder bestehende Eigenkanäle.',
			],
			'80_bis_150'  => [
				'label'       => '80–150 €',
				'description' => 'Typischer Korridor für Portal-Zukauf oder ungeöffnete Performance-Kampagnen.',
			],
			'151_bis_300' => [
				'label'       => '151–300 €',
				'description' => 'Spürbar hoher CPL — meist Mischung aus Portal-Leads, Streuverlust und schwacher Conversion.',
			],
			'ueber_300'   => [
				'label'       => 'Über 300 €',
				'description' => 'Akut unwirtschaftlich — Skalierung ist ohne strukturellen Eingriff nicht tragfähig.',
			],
		],
		'primary_bottleneck' => [
			'lead_menge'           => [
				'label'       => 'Lead-Menge',
				'description' => 'Vertrieb hat freie Kapazität, aber zu wenige Anfragen.',
			],
			'lead_qualitaet'       => [
				'label'       => 'Lead-Qualität',
				'description' => 'Anfragen kommen rein, aber zu viele unpassende Kontakte landen beim Vertrieb.',
			],
			'tracking_klarheit'    => [
				'label'       => 'Tracking-Klarheit',
				'description' => 'Unklar, welcher Kanal welche Abschlüsse bringt — Entscheidungen im Nebel.',
			],
			'eigentum_abhaengigkeit' => [
				'label'       => 'Eigentum / Abhängigkeit',
				'description' => 'System läuft, aber liegt bei Portal oder Agentur — Sie mieten den Hebel.',
			],
		],
		'solution_focus' => [
			'photovoltaik' => [
				'label'       => 'Photovoltaik',
				'description' => 'PV-Anlagen, Planung oder Vertrieb mit erklärungsbedürftigem Anfrageprozess.',
			],
			'waermepumpen' => [
				'label'       => 'Wärmepumpen',
				'description' => 'Wärmepumpen-Angebote mit Beratungs- und Vertriebsdruck vor Ort oder regional.',
			],
			'speicher' => [
				'label'       => 'Speicher',
				'description' => 'Speicherlösungen, die Vertrauen, Klarheit und bessere Vorqualifizierung brauchen.',
			],
			'mehrere_loesungen' => [
				'label'       => 'Mehrere Lösungen',
				'description' => 'Wenn PV, Wärmepumpe, Speicher oder ergänzende Leistungen gemeinsam verkauft werden.',
			],
			'sonstiges' => [
				'label'       => 'Sonstiges',
				'description' => 'Angrenzende Energielösungen oder ein hybrides Setup mit mehreren Angebotslinien.',
			],
		],
		'sales_audience' => [
			'privatkunden' => [
				'label'       => 'Privatkunden',
				'description' => 'Der Anfragepfad muss schnell Vertrauen und nächsten Schritt für B2C schaffen.',
			],
			'unternehmen' => [
				'label'       => 'Unternehmen / Gewerbe',
				'description' => 'Komplexere B2B-Entscheidungen brauchen saubere Struktur, Proof und klare Führung.',
			],
			'beides' => [
				'label'       => 'Privatkunden + Unternehmen',
				'description' => 'Die Website muss unterschiedliche Entscheidungslogiken sauber auseinanderhalten.',
			],
		],
		'region_scope' => [
			'lokal' => [
				'label'       => 'Lokal',
				'description' => 'Ein lokaler Markt mit starkem Vertrauens- und Verfügbarkeitsfaktor.',
			],
			'regional' => [
				'label'       => 'Regional',
				'description' => 'Mehrere Orte oder Regionen mit wiederkehrendem Vertriebs- und Routing-Bedarf.',
			],
			'deutschlandweit' => [
				'label'       => 'Deutschlandweit',
				'description' => 'Höhere Anforderungen an Landingpages, Segmentierung und klare Anfrageprozesse.',
			],
		],
		'primary_challenge' => [
			'zu_wenig_anfragen' => [
				'label'       => 'Zu wenige Anfragen',
				'description' => 'Es kommt zu wenig qualifizierte Nachfrage über die Website an.',
			],
			'zu_viele_unpassende_anfragen' => [
				'label'       => 'Zu viele unpassende Anfragen',
				'description' => 'Der Vertrieb verliert Zeit, weil Vorqualifizierung und Nutzerführung zu schwach sind.',
			],
			'website_konvertiert_schlecht' => [
				'label'       => 'Website konvertiert schlecht',
				'description' => 'Es gibt Aufmerksamkeit, aber der nächste sinnvolle Schritt ist zu schwach gebaut.',
			],
			'tracking_fehlt' => [
				'label'       => 'Tracking / Messbarkeit fehlt',
				'description' => 'Niemand kann sauber sehen, welche Seiten und Schritte Anfragen wirklich erzeugen.',
			],
			'seo_zu_schwach' => [
				'label'       => 'SEO / Sichtbarkeit zu schwach',
				'description' => 'Die Seite erreicht die richtige Suchintention noch nicht stabil genug.',
			],
			'potenzial_unklar' => [
				'label'       => 'Unklar, wo Potenzial verloren geht',
				'description' => 'Das Problem ist sichtbar, aber die eigentliche Bremse noch nicht klar priorisiert.',
			],
		],
		'measurement_state' => [
			'klar' => [
				'label'       => 'Schon recht klar',
				'description' => 'Es gibt bereits brauchbare Signale, aber sie greifen noch nicht sauber genug.',
			],
			'teilweise' => [
				'label'       => 'Teilweise messbar',
				'description' => 'Einzelne Datenpunkte existieren, aber Entscheidungen bleiben zu oft im Nebel.',
			],
			'kaum' => [
				'label'       => 'Kaum messbar',
				'description' => 'Es fehlt eine saubere Grundlage, um Nachfrage, Qualität und Verluste einzuordnen.',
			],
		],
		'acquisition_mix' => [
			'empfehlungen' => [
				'label'       => 'Empfehlungen',
				'description' => 'Die Website soll endlich mehr aus bestehendem Vertrauen und Mundpropaganda machen.',
			],
			'google_ads' => [
				'label'       => 'Google Ads',
				'description' => 'Die Zielseiten und Formulare müssen vorhandenes Mediabudget besser verwerten.',
			],
			'seo_organisch' => [
				'label'       => 'SEO / organisch',
				'description' => 'Suchintention, Angebotsstruktur und Conversion sollen stärker zusammenarbeiten.',
			],
			'portale_leadanbieter' => [
				'label'       => 'Portale / Lead-Anbieter',
				'description' => 'Eigene Nachfrage und bessere Lead-Qualität sollen unabhängiger vom Zukauf werden.',
			],
			'mehrere_kanaele' => [
				'label'       => 'Mehrere Kanäle',
				'description' => 'Die Website muss unterschiedlich eintreffende Nachfrage sauber bündeln und qualifizieren.',
			],
			'kaum_strukturiert' => [
				'label'       => 'Kaum strukturiert',
				'description' => 'Es fehlt noch ein belastbarer digitaler Anfrageprozess über mehrere Touchpoints hinweg.',
			],
		],
		'site_state' => [
			'ja_aktiv' => [
				'label'       => 'Ja, aktiv',
				'description' => 'Es gibt bereits eine Website oder Landingpage, die Anfragen mitträgt.',
			],
			'teilweise' => [
				'label'       => 'Teilweise',
				'description' => 'Ein Setup ist da, aber es arbeitet noch nicht konsistent für Vertrieb und Qualifizierung.',
			],
			'nein' => [
				'label'       => 'Nein',
				'description' => 'Der digitale Anfragepfad ist noch nicht als echtes Vertriebssystem aufgebaut.',
			],
		],
		'improvement_goal' => [
			'mehr_anfragen' => [
				'label'       => 'Mehr Anfragen',
				'description' => 'Mehr passende Nachfrage statt unklarer Streuung.',
			],
			'bessere_lead_qualitaet' => [
				'label'       => 'Bessere Lead-Qualität',
				'description' => 'Weniger Streuverlust und klarere Übergabe an Vertrieb oder Innendienst.',
			],
			'bessere_conversion' => [
				'label'       => 'Bessere Conversion',
				'description' => 'Mehr Wirkung aus vorhandenem Traffic und bestehenden Landingpages.',
			],
			'sauberes_tracking' => [
				'label'       => 'Sauberes Tracking',
				'description' => 'Messbare Anfragepfade statt Vermutungen und Reporting-Rauschen.',
			],
			'klarere_struktur' => [
				'label'       => 'Klarere Struktur',
				'description' => 'Schneller verständliche Nutzerführung, Angebotslogik und CTA-Reihenfolge.',
			],
			'alles_zusammen' => [
				'label'       => 'Alles zusammen',
				'description' => 'Wenn Website, Messbarkeit und Anfrageprozess sichtbar als Systemproblem auftreten.',
			],
		],
		'project_timing' => [
			'sofort' => [
				'label'       => 'Sofort',
				'description' => 'Es besteht akuter Handlungsdruck oder laufender Vertriebsbedarf.',
			],
			'naechste_4_wochen' => [
				'label'       => 'In den nächsten 4 Wochen',
				'description' => 'Das Thema ist konkret, aber muss noch sauber priorisiert werden.',
			],
			'eins_bis_drei_monate' => [
				'label'       => 'In 1-3 Monaten',
				'description' => 'Die Entscheidung steht bevor, braucht aber noch etwas Vorbereitung.',
			],
			'erstmal_orientierung' => [
				'label'       => 'Erstmal Orientierung',
				'description' => 'Zuerst soll klar werden, wo Nachfrage und Conversion heute verloren gehen.',
			],
		],
		'contact_preference' => [
			'email_only' => [
				'label'       => 'Einordnung per E-Mail reicht',
				'description' => 'Sie bekommen die Ersteinschätzung schriftlich — klar, konkret, ohne Pflicht-Call.',
			],
			'email_und_telefon' => [
				'label'       => 'Gerne auch telefonisch',
				'description' => 'Falls ein kurzer Austausch die Einordnung beschleunigt, rufe ich Sie an.',
			],
		],
	];
}

/**
 * Return the flow definition for the energy-systems intake.
 *
 * @return array<int, array<string, mixed>>
 */
function nexus_get_energy_intake_flow_definition() {
	$options = nexus_get_energy_intake_field_options();

	return [
		[
			'id'            => 'region',
			'name'          => 'postal_code',
			'kind'          => 'text_input',
			'title_short'   => 'Region',
			'question'      => 'In welcher Region arbeitet Ihr Betrieb?',
			'description'   => 'Wir prüfen, ob Ihr Einzugsgebiet im aktuellen Kapazitätsfenster liegt. Eine PLZ reicht — Filialstandorte besprechen wir im Erstgespräch.',
			'summary_label' => 'Region (PLZ)',
			'next'          => 'lead-volume',
			'field'         => [
				'name'         => 'postal_code',
				'label'        => 'Postleitzahl',
				'type'         => 'text',
				'inputmode'    => 'numeric',
				'autocomplete' => 'postal-code',
				'pattern'      => '[0-9]{5}',
				'maxlength'    => 5,
				'placeholder'  => 'z. B. 30159',
				'required'     => true,
			],
		],
		[
			'id'            => 'lead-volume',
			'name'          => 'lead_volume',
			'kind'          => 'single_choice',
			'title_short'   => 'Lead-Volumen',
			'question'      => 'Wie viele qualifizierte Anfragen erreichen Ihren Vertrieb aktuell pro Monat?',
			'description'   => 'Gemeint sind ausschließlich Anfragen, die Ihr Vertrieb tatsächlich kontaktieren kann — nicht Klicks, nicht Newsletter-Abonnenten.',
			'summary_label' => 'Lead-Volumen',
			'auto_advance'  => true,
			'next'          => 'cpl-range',
			'options'       => $options['lead_volume'],
		],
		[
			'id'            => 'cpl-range',
			'name'          => 'cpl_range',
			'kind'          => 'single_choice',
			'title_short'   => 'CPL',
			'question'      => 'Was kostet Sie eine qualifizierte Anfrage im Schnitt aktuell?',
			'description'   => 'Wenn Sie überwiegend Portal-Leads einkaufen, nehmen Sie den durchschnittlichen Einkaufspreis. Wenn Sie selbst Werbung schalten: Werbebudget geteilt durch qualifizierte Anfragen.',
			'summary_label' => 'CPL',
			'auto_advance'  => true,
			'next'          => 'bottleneck',
			'options'       => $options['cpl_range'],
		],
		[
			'id'            => 'bottleneck',
			'name'          => 'primary_bottleneck',
			'kind'          => 'single_choice',
			'title_short'   => 'Engpass',
			'question'      => 'Wo liegt aktuell Ihr größter Engpass?',
			'description'   => 'Eine Auswahl. Wenn mehrere zutreffen, nennen Sie den, dessen Lösung Ihrem Betrieb in den nächsten 6 Monaten am meisten bringt.',
			'summary_label' => 'Engpass',
			'auto_advance'  => true,
			'next'          => 'contact',
			'options'       => $options['primary_bottleneck'],
		],
		[
			'id'            => 'contact',
			'kind'          => 'contact',
			'title_short'   => 'Kontakt',
			'question'      => 'An wen senden wir die Standortbestimmung?',
			'description'   => 'Ihre Antworten werden ausschließlich für die Vorbereitung der Standortbestimmung verwendet. Keine Weitergabe an Dritte, kein Newsletter, kein automatisierter Anruf.',
			'summary_label' => 'Kontakt',
			'fields'        => [
				[
					'name'         => 'company',
					'label'        => 'Unternehmen',
					'type'         => 'text',
					'autocomplete' => 'organization',
					'required'     => true,
				],
				[
					'name'         => 'name',
					'label'        => 'Vor- und Nachname',
					'type'         => 'text',
					'autocomplete' => 'name',
					'required'     => true,
				],
				[
					'name'         => 'email',
					'label'        => 'Geschäftliche E-Mail',
					'type'         => 'email',
					'autocomplete' => 'email',
					'inputmode'    => 'email',
					'help'         => 'Bitte Firmen-Domain angeben. Bei Freemail-Adressen (gmail, gmx, web.de) verzögert sich die Antwort.',
					'required'     => true,
				],
				[
					'name'         => 'phone',
					'label'        => 'Telefon (optional)',
					'type'         => 'tel',
					'autocomplete' => 'tel',
					'inputmode'    => 'tel',
					'required'     => false,
				],
				[
					'name'         => 'current_challenge',
					'label'        => 'Anmerkung — falls Sie etwas vorab wissen sollten (optional)',
					'type'         => 'textarea',
					'rows'         => 4,
					'maxlength'    => 500,
					'placeholder'  => 'Zum Beispiel regionale Besonderheiten, laufende Kampagnen oder bestehende Vertragsbindungen.',
					'required'     => false,
				],
				[
					'name'         => 'consent_privacy',
					'label'        => 'Ich stimme zu, dass meine Angaben zur Bearbeitung dieser Anfrage verarbeitet werden.',
					'type'         => 'checkbox',
					'value'        => 'accepted',
					'required'     => true,
				],
			],
		],
	];
}

/**
 * Normalize a public Cal.com URL into modal embed config fragments.
 *
 * @param string $calendar_url Public booking URL.
 * @return array<string, string>
 */
function nexus_build_calendar_embed_entry( $calendar_url ) {
	$calendar_url = esc_url_raw( (string) $calendar_url, [ 'https' ] );

	if ( '' === $calendar_url ) {
		return [
			'url'      => '',
			'origin'   => '',
			'cal_link' => '',
		];
	}

	$parsed_url = wp_parse_url( $calendar_url );
	$scheme     = isset( $parsed_url['scheme'] ) ? strtolower( (string) $parsed_url['scheme'] ) : 'https';
	$host       = isset( $parsed_url['host'] ) ? strtolower( (string) $parsed_url['host'] ) : '';
	$path       = isset( $parsed_url['path'] ) ? trim( (string) $parsed_url['path'], '/' ) : '';

	if ( 'http' !== $scheme && 'https' !== $scheme ) {
		$scheme = 'https';
	}

	return [
		'url'      => $calendar_url,
		'origin'   => $host ? $scheme . '://' . $host : '',
		'cal_link' => $path,
	];
}

/**
 * Resolve the public calendar URL for audit-related flows.
 *
 * @return string
 */
function nexus_get_audit_calendar_url() {
	$default_url = 'https://cal.com/hasim-uener/30min?overlayCalendar=true';

	$resolved_url = (string) apply_filters(
		'nexus_audit_calendar_url',
		apply_filters( 'nexus_review_calendar_url', $default_url )
	);

	$sanitized_url = esc_url_raw( $resolved_url, [ 'https' ] );

	if ( '' !== $sanitized_url ) {
		return $sanitized_url;
	}

	return $default_url;
}

/**
 * Resolve the public calendar URL for whitelabel fit conversations.
 *
 * @return string
 */
function nexus_get_whitelabel_calendar_url() {
	$default_url = 'https://cal.com/hasim-uener/whitelabel-fit-gesprach?overlayCalendar=true';

	$resolved_url = (string) apply_filters(
		'nexus_whitelabel_calendar_url',
		$default_url
	);

	$sanitized_url = esc_url_raw( $resolved_url, [ 'https' ] );

	if ( '' !== $sanitized_url ) {
		return $sanitized_url;
	}

	return $default_url;
}

/**
 * Return normalized Cal.com embed config for direct booking CTAs.
 *
 * @return array<string, mixed>
 */
function nexus_get_audit_calendar_embed_config() {
	$audit_entry      = nexus_build_calendar_embed_entry( nexus_get_audit_calendar_url() );
	$whitelabel_entry = nexus_build_calendar_embed_entry( nexus_get_whitelabel_calendar_url() );
	$booking_links    = [];

	foreach ( [ $audit_entry, $whitelabel_entry ] as $entry ) {
		if ( empty( $entry['url'] ) || empty( $entry['origin'] ) || empty( $entry['cal_link'] ) ) {
			continue;
		}

		$booking_links[] = [
			'url'      => $entry['url'],
			'cal_link' => $entry['cal_link'],
		];
	}

	return [
		'url'           => $audit_entry['url'],
		'origin'        => $audit_entry['origin'],
		'cal_link'      => $audit_entry['cal_link'],
		'booking_links' => $booking_links,
	];
}

/**
 * Resolve the internal notification recipient for audit requests.
 *
 * @return string
 */
function nexus_get_audit_notification_email() {
	$default = (string) apply_filters( 'nexus_review_notification_email', get_option( 'admin_email' ) );

	return (string) apply_filters( 'nexus_audit_notification_email', $default );
}

/**
 * Return the first non-empty meta value from a list of keys.
 *
 * @param int          $post_id  Current post ID.
 * @param array|string $keys     Meta key or fallback key list.
 * @param string       $default  Default value.
 * @return string
 */
function nexus_get_review_meta_value( $post_id, $keys, $default = '' ) {
	foreach ( (array) $keys as $key ) {
		$value = (string) get_post_meta( $post_id, $key, true );
		if ( '' !== trim( $value ) ) {
			return $value;
		}
	}

	return $default;
}

/**
 * Register the internal CRM post type.
 *
 * @return void
 */
function nexus_register_review_request_post_type() {
	register_post_type(
		'nexus_review_request',
		[
			'labels' => [
				'name'               => 'Audit-Anfragen',
				'singular_name'      => 'Audit-Anfrage',
				'menu_name'          => 'Audit-Anfragen',
				'name_admin_bar'     => 'Audit-Anfrage',
				'add_new'            => 'Neu',
				'add_new_item'       => 'Neue Audit-Anfrage',
				'edit_item'          => 'Audit-Anfrage bearbeiten',
				'new_item'           => 'Neue Audit-Anfrage',
				'view_item'          => 'Audit-Anfrage ansehen',
				'search_items'       => 'Audit-Anfragen suchen',
				'not_found'          => 'Keine Audit-Anfragen gefunden.',
				'not_found_in_trash' => 'Keine Audit-Anfragen im Papierkorb.',
				'all_items'          => 'Alle Audit-Anfragen',
			],
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => function_exists( 'nexus_get_crm_menu_slug' ) ? nexus_get_crm_menu_slug() : 'nexus-crm',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'map_meta_cap'        => true,
			'menu_icon'           => 'dashicons-clipboard',
			'supports'            => [ 'title' ],
		]
	);
}
add_action( 'init', 'nexus_register_review_request_post_type' );

/**
 * Register the top-level CRM menu.
 *
 * @return void
 */
function nexus_register_review_crm_menu() {
	add_menu_page(
		'Nexus CRM',
		'Nexus CRM',
		'edit_pages',
		function_exists( 'nexus_get_crm_menu_slug' ) ? nexus_get_crm_menu_slug() : 'nexus-crm',
		'nexus_render_review_crm_dashboard',
		'dashicons-clipboard',
		58
	);

	add_submenu_page(
		function_exists( 'nexus_get_crm_menu_slug' ) ? nexus_get_crm_menu_slug() : 'nexus-crm',
		'Nexus CRM',
		'Übersicht',
		'edit_pages',
		function_exists( 'nexus_get_crm_menu_slug' ) ? nexus_get_crm_menu_slug() : 'nexus-crm',
		'nexus_render_review_crm_dashboard'
	);
}
add_action( 'admin_menu', 'nexus_register_review_crm_menu' );

/**
 * Redirect the previous audit-only CRM slug to the shared CRM dashboard.
 *
 * @return void
 */
function nexus_redirect_legacy_review_crm_menu() {
	if ( ! is_admin() ) {
		return;
	}

	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';

	if ( 'nexus-review-crm' !== $page ) {
		return;
	}

	wp_safe_redirect( admin_url( 'admin.php?page=' . ( function_exists( 'nexus_get_crm_menu_slug' ) ? nexus_get_crm_menu_slug() : 'nexus-crm' ) ) );
	exit;
}
add_action( 'admin_init', 'nexus_redirect_legacy_review_crm_menu' );

/**
 * Enqueue the admin styles for CRM screens.
 *
 * @param string $hook Current admin hook suffix.
 * @return void
 */
function nexus_enqueue_review_crm_admin_assets( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$is_crm_dashboard = 'toplevel_page_nexus-crm' === $hook || 'nexus-crm_page_edit-php' === $hook;
	$is_review_screen = in_array( $screen->post_type, [ 'nexus_review_request', 'nexus_contact' ], true );
	$is_wp_dashboard  = 'dashboard' === $screen->base;

	if ( ! $is_crm_dashboard && ! $is_review_screen && ! $is_wp_dashboard ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/css/review-crm-admin.css';
	if ( file_exists( $path ) ) {
		wp_enqueue_style(
			'nexus-review-crm-admin',
			get_stylesheet_directory_uri() . '/assets/css/review-crm-admin.css',
			[],
			filemtime( $path )
		);
	}
}
add_action( 'admin_enqueue_scripts', 'nexus_enqueue_review_crm_admin_assets' );

/**
 * Resolve a best-effort domain from a validated email address.
 *
 * @param string $email Email address.
 * @return string
 */
function nexus_get_review_request_domain_from_email( $email ) {
	$email = sanitize_email( (string) $email );

	if ( '' === $email || ! is_email( $email ) ) {
		return '';
	}

	$parts = explode( '@', $email );
	$host  = strtolower( trim( (string) end( $parts ) ) );

	return sanitize_text_field( $host );
}

/**
 * Register the public REST route for audit request submissions.
 *
 * @return void
 */
function nexus_register_review_crm_rest_routes() {
	$routes = [
		'/audit-request',
		'/review-request',
	];

	foreach ( $routes as $route ) {
		register_rest_route(
			'nexus/v1',
			$route,
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'nexus_handle_review_request_submission',
				'permission_callback' => '__return_true',
			]
		);
	}
}
add_action( 'rest_api_init', 'nexus_register_review_crm_rest_routes' );

/**
 * Return the public success message for a validated request.
 *
 * @param array $payload Validated payload.
 * @return string
 */
function nexus_get_review_request_success_message( $payload ) {
	$variant = isset( $payload['intake_variant'] ) ? sanitize_key( (string) $payload['intake_variant'] ) : '';

	if ( 'energy_systems' === $variant ) {
		return 'Eingegangen. Ihre Standortbestimmung liegt in der Bearbeitung. Sie erhalten innerhalb von 48 Werktagsstunden eine E-Mail von hasim@hasimuener.de — bei Eignung mit Vorschlag für ein 30-minütiges Erstgespräch, bei Nicht-Eignung mit konkretem Hinweis auf eine realistischere Alternative.';
	}

	if ( nexus_is_growth_audit_simple_intake_variant( $variant ) ) {
		return 'Die Rückmeldung kommt innerhalb von 48 Stunden per E-Mail.';
	}

	return 'Ich prüfe Ihre Seite manuell und ergänze die Einschätzung durch KI-gestützte Analyse. Sie erhalten innerhalb von 48 Stunden eine persönliche Rückmeldung.';
}

/**
 * Process a public review request submission.
 *
 * Shared by the REST callback and page-level fallback handling.
 *
 * @param array $payload Raw request payload.
 * @return array|WP_Error
 */
function nexus_process_review_request_submission( $payload ) {
	$honeypot = isset( $payload['company_website'] ) ? trim( (string) $payload['company_website'] ) : '';
	if ( '' !== $honeypot ) {
		return [
			'ok'          => true,
			'requestId'   => 0,
			'message'     => 'Anfrage gespeichert.',
			'status'      => 'ignored',
			'statusLabel' => 'Gespeichert',
			'auditType'   => isset( $payload['audit_type'] ) ? sanitize_key( (string) $payload['audit_type'] ) : 'growth_audit',
			'http_status' => 200,
		];
	}

	$rate_limit_error = nexus_validate_review_request_rate_limit();
	if ( is_wp_error( $rate_limit_error ) ) {
		return $rate_limit_error;
	}

	$validated = nexus_validate_review_request_payload( $payload );
	if ( is_wp_error( $validated ) ) {
		return $validated;
	}

	$post_id = nexus_create_review_request_post( $validated );
	if ( is_wp_error( $post_id ) ) {
		return new WP_Error(
			'request_storage_failed',
			'Die Anfrage konnte gerade nicht gespeichert werden. Bitte später erneut versuchen.'
		);
	}

	nexus_send_review_request_admin_notification( $post_id, $validated );
	nexus_send_review_request_confirmation( $validated );

	return [
		'ok'          => true,
		'requestId'   => $post_id,
		'message'     => nexus_get_review_request_success_message( $validated ),
		'editUrl'     => get_edit_post_link( $post_id, 'raw' ),
		'status'      => 'received',
		'statusLabel' => 'Neu',
		'auditType'   => $validated['audit_type'],
		'http_status' => 201,
	];
}

/**
 * Handle incoming review requests from public audit landing pages.
 *
 * @param WP_REST_Request $request REST request object.
 * @return WP_REST_Response
 */
function nexus_handle_review_request_submission( WP_REST_Request $request ) {
	$payload = $request->get_json_params();
	if ( ! is_array( $payload ) || empty( $payload ) ) {
		$payload = $request->get_body_params();
	}

	$result = nexus_process_review_request_submission( is_array( $payload ) ? $payload : [] );
	if ( is_wp_error( $result ) ) {
		$status = 400;

		if ( 'rate_limited' === $result->get_error_code() ) {
			$status = 429;
		} elseif ( 'request_storage_failed' === $result->get_error_code() ) {
			$status = 500;
		}

		return new WP_REST_Response(
			[
				'ok'    => false,
				'error' => $result->get_error_message(),
			],
			$status
		);
	}

	$response_status = isset( $result['http_status'] ) ? (int) $result['http_status'] : 201;
	unset( $result['http_status'] );

	return new WP_REST_Response( $result, $response_status );
}

/**
 * Sanitize one optional internal attribution URL for audit requests.
 *
 * @param string $url Raw URL.
 * @return string
 */
function nexus_sanitize_review_request_internal_url( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '';
	}

	$url = esc_url_raw( $url );
	if ( ! $url || ! wp_http_validate_url( $url ) ) {
		return '';
	}

	$scheme = wp_parse_url( $url, PHP_URL_SCHEME );
	$host   = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
	$home   = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );

	if ( ! in_array( $scheme, [ 'http', 'https' ], true ) || '' === $host || $host !== $home ) {
		return '';
	}

	$path = (string) wp_parse_url( $url, PHP_URL_PATH );
	$path = '/' . ltrim( $path, '/' );

	if ( '/' !== $path ) {
		$path = trailingslashit( $path );
	}

	return home_url( $path );
}

/**
 * Sanitize one optional referrer URL for audit requests.
 *
 * @param string $url Raw URL.
 * @return string
 */
function nexus_sanitize_review_request_referrer_url( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '';
	}

	$url = esc_url_raw( $url );
	if ( ! $url || ! wp_http_validate_url( $url ) ) {
		return '';
	}

	$scheme = wp_parse_url( $url, PHP_URL_SCHEME );

	if ( ! in_array( $scheme, [ 'http', 'https' ], true ) ) {
		return '';
	}

	return $url;
}

/**
 * Validate and sanitize the review request payload.
 *
 * @param array $payload Raw request payload.
 * @return array|WP_Error
 */
function nexus_validate_review_request_payload( $payload ) {
	$intake_variant = isset( $payload['intake_variant'] ) ? sanitize_key( (string) $payload['intake_variant'] ) : '';

	if ( nexus_is_growth_audit_simple_intake_variant( $intake_variant ) ) {
		return nexus_validate_growth_audit_simple_review_request_payload( $payload );
	}

	if ( 'energy_systems' === $intake_variant ) {
		return nexus_validate_energy_review_request_payload( $payload );
	}

	$type_options      = nexus_get_audit_request_type_options();
	$focus_area_map    = nexus_get_review_focus_area_options();
	$primary_goal_map  = nexus_get_review_primary_goal_options();
	$audit_type        = isset( $payload['audit_type'] ) ? sanitize_key( (string) $payload['audit_type'] ) : 'growth_audit';
	$page_url          = isset( $payload['page_url'] ) ? trim( (string) $payload['page_url'] ) : '';
	$company           = isset( $payload['company'] ) ? sanitize_text_field( (string) $payload['company'] ) : '';
	$focus_area        = isset( $payload['focus_area'] ) ? sanitize_key( (string) $payload['focus_area'] ) : '';
	$current_challenge = isset( $payload['current_challenge'] ) ? sanitize_textarea_field( (string) $payload['current_challenge'] ) : '';
	$primary_goal      = isset( $payload['primary_goal'] ) ? sanitize_key( (string) $payload['primary_goal'] ) : '';
	$extra_context     = isset( $payload['extra_context'] ) ? sanitize_textarea_field( (string) $payload['extra_context'] ) : '';
	$name              = isset( $payload['name'] ) ? sanitize_text_field( (string) $payload['name'] ) : '';
	$email             = isset( $payload['email'] ) ? sanitize_email( (string) $payload['email'] ) : '';
	$linkedin          = isset( $payload['linkedin'] ) ? trim( (string) $payload['linkedin'] ) : '';
	$consent_privacy   = isset( $payload['consent_privacy'] ) ? sanitize_key( (string) $payload['consent_privacy'] ) : '';

	if ( empty( $page_url ) ) {
		return new WP_Error( 'missing_page_url', 'Bitte die URL der Seite angeben.' );
	}

	$page_url = esc_url_raw( $page_url );
	if ( ! $page_url || ! wp_http_validate_url( $page_url ) ) {
		return new WP_Error( 'invalid_page_url', 'Bitte eine gültige URL angeben, z. B. https://example.de.' );
	}

	$scheme = wp_parse_url( $page_url, PHP_URL_SCHEME );
	if ( ! in_array( $scheme, [ 'http', 'https' ], true ) ) {
		return new WP_Error( 'invalid_scheme', 'Nur http- oder https-URLs sind erlaubt.' );
	}

	if ( empty( $focus_area ) || ! isset( $focus_area_map[ $focus_area ] ) ) {
		return new WP_Error( 'missing_focus_area', 'Bitte den Bereich mit dem größten Klärungsbedarf auswählen.' );
	}

	if ( empty( $primary_goal ) || ! isset( $primary_goal_map[ $primary_goal ] ) ) {
		return new WP_Error( 'missing_primary_goal', 'Bitte das wichtigste Ziel für diese Seite auswählen.' );
	}

	if ( empty( $name ) ) {
		return new WP_Error( 'missing_name', 'Bitte Ihren Namen angeben.' );
	}

	if ( empty( $email ) || ! is_email( $email ) ) {
		return new WP_Error( 'invalid_email', 'Bitte eine gültige geschäftliche E-Mail-Adresse angeben.' );
	}

	if ( 'accepted' !== $consent_privacy ) {
		return new WP_Error( 'missing_consent_privacy', 'Bitte bestätigen Sie den Datenschutzhinweis, damit ich Ihre Anfrage bearbeiten darf.' );
	}

	if ( '' !== $linkedin ) {
		$linkedin = esc_url_raw( $linkedin );

		if ( ! $linkedin || ! wp_http_validate_url( $linkedin ) ) {
			return new WP_Error( 'invalid_linkedin', 'Bitte eine gültige LinkedIn-URL angeben oder das Feld leer lassen.' );
		}

		$linkedin_scheme = wp_parse_url( $linkedin, PHP_URL_SCHEME );
		if ( ! in_array( $linkedin_scheme, [ 'http', 'https' ], true ) ) {
			return new WP_Error( 'invalid_linkedin_scheme', 'Bitte eine gültige LinkedIn-URL mit http oder https angeben.' );
		}
	}

	if ( empty( $audit_type ) || ! isset( $type_options[ $audit_type ] ) ) {
		$audit_type = 'growth_audit';
	}

	$ads_source            = isset( $payload['ads_source'] ) ? sanitize_text_field( (string) $payload['ads_source'] ) : '';
	$ads_keyword           = isset( $payload['ads_keyword'] ) ? sanitize_text_field( (string) $payload['ads_keyword'] ) : '';
	$landing_page_url      = nexus_sanitize_review_request_internal_url( $payload['landing_page_url'] ?? '' );
	$entry_page_url        = nexus_sanitize_review_request_internal_url( $payload['entry_page_url'] ?? '' );
	$previous_internal_url = nexus_sanitize_review_request_internal_url( $payload['previous_internal_url'] ?? '' );
	$referrer_url          = nexus_sanitize_review_request_referrer_url( $payload['referrer_url'] ?? '' );
	$referrer_host         = $referrer_url ? sanitize_text_field( strtolower( (string) wp_parse_url( $referrer_url, PHP_URL_HOST ) ) ) : '';

	return [
		'intake_variant'       => '',
		'intake_variant_label' => '',
		'audit_type'        => $audit_type,
		'audit_type_label'  => $type_options[ $audit_type ],
		'page_url'          => $page_url,
		'domain'            => (string) wp_parse_url( $page_url, PHP_URL_HOST ),
		'company'           => $company,
		'focus_area'        => $focus_area,
		'focus_area_label'  => $focus_area_map[ $focus_area ],
		'current_challenge' => $current_challenge,
		'primary_goal'      => $primary_goal,
		'primary_goal_label' => $primary_goal_map[ $primary_goal ],
		'extra_context'     => $extra_context,
		'name'              => $name,
		'email'             => $email,
		'phone'             => '',
		'linkedin'          => $linkedin,
		'consent_privacy'   => $consent_privacy,
		'ads_source'        => $ads_source,
		'ads_keyword'       => $ads_keyword,
		'landing_page_url'  => $landing_page_url,
		'entry_page_url'    => $entry_page_url,
		'previous_internal_url' => $previous_internal_url,
		'referrer_url'      => $referrer_url,
		'referrer_host'     => $referrer_host,
	];
}

/**
 * Validate and sanitize the simplified marketcheck landing page payload.
 *
 * @param array $payload Raw request payload.
 * @return array|WP_Error
 */
function nexus_validate_growth_audit_simple_review_request_payload( $payload ) {
	$type_options        = nexus_get_audit_request_type_options();
	$audit_type          = isset( $payload['audit_type'] ) ? sanitize_key( (string) $payload['audit_type'] ) : 'growth_audit';
	$page_url            = isset( $payload['page_url'] ) ? trim( (string) $payload['page_url'] ) : '';
	$current_challenge   = isset( $payload['current_challenge'] ) ? sanitize_textarea_field( (string) $payload['current_challenge'] ) : '';
	$name                = isset( $payload['name'] ) ? sanitize_text_field( (string) $payload['name'] ) : '';
	$email               = isset( $payload['email'] ) ? sanitize_email( (string) $payload['email'] ) : '';
	$consent_privacy     = isset( $payload['consent_privacy'] ) ? sanitize_key( (string) $payload['consent_privacy'] ) : '';

	if ( empty( $page_url ) ) {
		return new WP_Error( 'missing_page_url', 'Bitte die URL der Seite angeben.' );
	}

	$page_url = esc_url_raw( $page_url );
	if ( ! $page_url || ! wp_http_validate_url( $page_url ) ) {
		return new WP_Error( 'invalid_page_url', 'Bitte eine gültige URL angeben, z. B. https://example.de.' );
	}

	$scheme = wp_parse_url( $page_url, PHP_URL_SCHEME );
	if ( ! in_array( $scheme, [ 'http', 'https' ], true ) ) {
		return new WP_Error( 'invalid_scheme', 'Nur http- oder https-URLs sind erlaubt.' );
	}

	if ( empty( $name ) ) {
		return new WP_Error( 'missing_name', 'Bitte Ihren Namen angeben.' );
	}

	if ( empty( $email ) || ! is_email( $email ) ) {
		return new WP_Error( 'invalid_email', 'Bitte eine gültige geschäftliche E-Mail-Adresse angeben.' );
	}

	if ( 'accepted' !== $consent_privacy ) {
		return new WP_Error( 'missing_consent_privacy', 'Bitte bestätigen Sie den Datenschutzhinweis, damit ich Ihre Anfrage bearbeiten darf.' );
	}

	if ( empty( $audit_type ) || ! isset( $type_options[ $audit_type ] ) ) {
		$audit_type = 'growth_audit';
	}

	$ads_source            = isset( $payload['ads_source'] ) ? sanitize_text_field( (string) $payload['ads_source'] ) : '';
	$ads_keyword           = isset( $payload['ads_keyword'] ) ? sanitize_text_field( (string) $payload['ads_keyword'] ) : '';
	$landing_page_url      = nexus_sanitize_review_request_internal_url( $payload['landing_page_url'] ?? '' );
	$entry_page_url        = nexus_sanitize_review_request_internal_url( $payload['entry_page_url'] ?? '' );
	$previous_internal_url = nexus_sanitize_review_request_internal_url( $payload['previous_internal_url'] ?? '' );
	$referrer_url          = nexus_sanitize_review_request_referrer_url( $payload['referrer_url'] ?? '' );
	$referrer_host         = $referrer_url ? sanitize_text_field( strtolower( (string) wp_parse_url( $referrer_url, PHP_URL_HOST ) ) ) : '';

	return [
		'intake_variant'         => 'growth_audit_simple',
		'intake_variant_label'   => nexus_get_growth_audit_simple_intake_variant_label(),
		'audit_type'             => $audit_type,
		'audit_type_label'       => $type_options[ $audit_type ],
		'page_url'               => $page_url,
		'domain'                 => (string) wp_parse_url( $page_url, PHP_URL_HOST ),
		'company'                => '',
		'focus_area'             => '',
		'focus_area_label'       => '',
		'current_challenge'      => $current_challenge,
		'primary_goal'           => '',
		'primary_goal_label'     => '',
		'extra_context'          => '',
		'name'                   => $name,
		'email'                  => $email,
		'phone'                  => '',
		'linkedin'               => '',
		'consent_privacy'        => $consent_privacy,
		'ads_source'             => $ads_source,
		'ads_keyword'            => $ads_keyword,
		'landing_page_url'       => $landing_page_url,
		'entry_page_url'         => $entry_page_url,
		'previous_internal_url'  => $previous_internal_url,
		'referrer_url'           => $referrer_url,
		'referrer_host'          => $referrer_host,
	];
}

/**
 * Validate and sanitize the energy-systems landing page payload.
 *
 * @param array $payload Raw request payload.
 * @return array|WP_Error
 */
function nexus_validate_energy_review_request_payload( $payload ) {
	$type_options          = nexus_get_audit_request_type_options();
	$field_options         = nexus_get_energy_intake_field_options();
	$audit_type            = isset( $payload['audit_type'] ) ? sanitize_key( (string) $payload['audit_type'] ) : 'growth_audit';
	$postal_code           = isset( $payload['postal_code'] ) ? preg_replace( '/\D/', '', (string) $payload['postal_code'] ) : '';
	$lead_volume           = isset( $payload['lead_volume'] ) ? sanitize_key( (string) $payload['lead_volume'] ) : '';
	$cpl_range             = isset( $payload['cpl_range'] ) ? sanitize_key( (string) $payload['cpl_range'] ) : '';
	$primary_bottleneck    = isset( $payload['primary_bottleneck'] ) ? sanitize_key( (string) $payload['primary_bottleneck'] ) : '';
	$solution_focus        = isset( $payload['solution_focus'] ) ? sanitize_key( (string) $payload['solution_focus'] ) : '';
	$sales_audience        = isset( $payload['sales_audience'] ) ? sanitize_key( (string) $payload['sales_audience'] ) : '';
	$region_scope          = isset( $payload['region_scope'] ) ? sanitize_key( (string) $payload['region_scope'] ) : '';
	$primary_challenge     = isset( $payload['primary_challenge'] ) ? sanitize_key( (string) $payload['primary_challenge'] ) : '';
	$measurement_state     = isset( $payload['measurement_state'] ) ? sanitize_key( (string) $payload['measurement_state'] ) : '';
	$acquisition_mix       = isset( $payload['acquisition_mix'] ) ? sanitize_key( (string) $payload['acquisition_mix'] ) : '';
	$site_state            = isset( $payload['site_state'] ) ? sanitize_key( (string) $payload['site_state'] ) : '';
	$improvement_goal      = isset( $payload['improvement_goal'] ) ? sanitize_key( (string) $payload['improvement_goal'] ) : '';
	$project_timing        = isset( $payload['project_timing'] ) ? sanitize_key( (string) $payload['project_timing'] ) : '';
	$page_url              = isset( $payload['page_url'] ) ? trim( (string) $payload['page_url'] ) : '';
	$current_challenge     = isset( $payload['current_challenge'] ) ? sanitize_textarea_field( (string) $payload['current_challenge'] ) : '';
	$company               = isset( $payload['company'] ) ? sanitize_text_field( (string) $payload['company'] ) : '';
	$name                  = isset( $payload['name'] ) ? sanitize_text_field( (string) $payload['name'] ) : '';
	$email                 = isset( $payload['email'] ) ? sanitize_email( (string) $payload['email'] ) : '';
	$phone                 = isset( $payload['phone'] ) ? sanitize_text_field( (string) $payload['phone'] ) : '';
	$consent_privacy       = isset( $payload['consent_privacy'] ) ? sanitize_key( (string) $payload['consent_privacy'] ) : '';
	// Solution focus, sales audience, site state, project timing, contact preference,
	// measurement state, acquisition mix, improvement goal and region scope are
	// not collected in the 5-step v1 flow. They remain optional in the payload
	// so legacy entries and future variants stay compatible.

	if ( '' === $postal_code || ! preg_match( '/^[0-9]{5}$/', $postal_code ) ) {
		return new WP_Error( 'invalid_postal_code', 'Bitte eine gültige fünfstellige deutsche Postleitzahl angeben.' );
	}

	if ( (int) $postal_code < 1000 || (int) $postal_code > 99999 ) {
		return new WP_Error( 'invalid_postal_code_range', 'Bitte eine deutsche Postleitzahl im gültigen Bereich angeben.' );
	}

	if ( empty( $lead_volume ) || ! isset( $field_options['lead_volume'][ $lead_volume ] ) ) {
		return new WP_Error( 'missing_lead_volume', 'Bitte das aktuelle Lead-Volumen auswählen.' );
	}

	if ( empty( $cpl_range ) || ! isset( $field_options['cpl_range'][ $cpl_range ] ) ) {
		return new WP_Error( 'missing_cpl_range', 'Bitte den aktuellen CPL-Bereich auswählen.' );
	}

	if ( empty( $primary_bottleneck ) || ! isset( $field_options['primary_bottleneck'][ $primary_bottleneck ] ) ) {
		return new WP_Error( 'missing_primary_bottleneck', 'Bitte den größten aktuellen Engpass auswählen.' );
	}

	if ( '' !== $solution_focus && ! isset( $field_options['solution_focus'][ $solution_focus ] ) ) {
		$solution_focus = '';
	}

	if ( '' !== $sales_audience && ! isset( $field_options['sales_audience'][ $sales_audience ] ) ) {
		$sales_audience = '';
	}

	if ( '' !== $region_scope && ! isset( $field_options['region_scope'][ $region_scope ] ) ) {
		$region_scope = '';
	}

	if ( '' !== $primary_challenge && ! isset( $field_options['primary_challenge'][ $primary_challenge ] ) ) {
		$primary_challenge = '';
	}

	if ( '' !== $measurement_state && ! isset( $field_options['measurement_state'][ $measurement_state ] ) ) {
		$measurement_state = '';
	}

	if ( '' !== $acquisition_mix && ! isset( $field_options['acquisition_mix'][ $acquisition_mix ] ) ) {
		$acquisition_mix = '';
	}

	if ( '' !== $site_state && ! isset( $field_options['site_state'][ $site_state ] ) ) {
		$site_state = '';
	}

	if ( '' !== $improvement_goal && ! isset( $field_options['improvement_goal'][ $improvement_goal ] ) ) {
		$improvement_goal = '';
	}

	if ( '' !== $project_timing && ! isset( $field_options['project_timing'][ $project_timing ] ) ) {
		$project_timing = '';
	}

	if ( '' !== $page_url ) {
		$page_url = esc_url_raw( $page_url );
		if ( ! $page_url || ! wp_http_validate_url( $page_url ) ) {
			return new WP_Error( 'invalid_page_url', 'Bitte eine gültige Website- oder Landingpage-URL angeben oder das Feld leer lassen.' );
		}

		$scheme = wp_parse_url( $page_url, PHP_URL_SCHEME );
		if ( ! in_array( $scheme, [ 'http', 'https' ], true ) ) {
			return new WP_Error( 'invalid_scheme', 'Nur http- oder https-URLs sind erlaubt.' );
		}
	}

	if ( empty( $name ) ) {
		return new WP_Error( 'missing_name', 'Bitte Ihren Namen angeben.' );
	}

	if ( empty( $company ) ) {
		return new WP_Error( 'missing_company', 'Bitte Ihr Unternehmen angeben.' );
	}

	if ( empty( $email ) || ! is_email( $email ) ) {
		return new WP_Error( 'invalid_email', 'Bitte eine gültige geschäftliche E-Mail-Adresse angeben.' );
	}

	if ( 'accepted' !== $consent_privacy ) {
		return new WP_Error( 'missing_consent_privacy', 'Bitte bestätigen Sie den Datenschutzhinweis, damit ich Ihre Anfrage bearbeiten darf.' );
	}

	if ( empty( $audit_type ) || ! isset( $type_options[ $audit_type ] ) ) {
		$audit_type = 'growth_audit';
	}

	$ads_source            = isset( $payload['ads_source'] ) ? sanitize_text_field( (string) $payload['ads_source'] ) : '';
	$ads_keyword           = isset( $payload['ads_keyword'] ) ? sanitize_text_field( (string) $payload['ads_keyword'] ) : '';
	$landing_page_url      = nexus_sanitize_review_request_internal_url( $payload['landing_page_url'] ?? '' );
	$entry_page_url        = nexus_sanitize_review_request_internal_url( $payload['entry_page_url'] ?? '' );
	$previous_internal_url = nexus_sanitize_review_request_internal_url( $payload['previous_internal_url'] ?? '' );
	$referrer_url          = nexus_sanitize_review_request_referrer_url( $payload['referrer_url'] ?? '' );
	$resolved_domain = $page_url
		? (string) wp_parse_url( $page_url, PHP_URL_HOST )
		: nexus_get_review_request_domain_from_email( $email );

	$lookup_label = static function( $group, $key ) use ( $field_options ) {
		if ( '' === $key || ! isset( $field_options[ $group ][ $key ]['label'] ) ) {
			return '';
		}

		return (string) $field_options[ $group ][ $key ]['label'];
	};

	return [
		'intake_variant'               => 'energy_systems',
		'intake_variant_label'         => nexus_get_energy_intake_variant_label(),
		'audit_type'                  => $audit_type,
		'audit_type_label'            => $type_options[ $audit_type ],
		'page_url'                    => $page_url,
		'domain'                      => $resolved_domain,
		'company'                     => $company,
		'focus_area'                  => $primary_bottleneck,
		'focus_area_label'            => $lookup_label( 'primary_bottleneck', $primary_bottleneck ),
		'current_challenge'           => $current_challenge,
		'primary_goal'                => $improvement_goal,
		'primary_goal_label'          => $lookup_label( 'improvement_goal', $improvement_goal ),
		'extra_context'               => '',
		'name'                        => $name,
		'email'                       => $email,
		'phone'                       => $phone,
		'linkedin'                    => '',
		'consent_privacy'             => $consent_privacy,
		'ads_source'                  => $ads_source,
		'ads_keyword'                 => $ads_keyword,
		'landing_page_url'            => $landing_page_url,
		'entry_page_url'              => $entry_page_url,
		'previous_internal_url'       => $previous_internal_url,
		'referrer_url'                => $referrer_url,
		'referrer_host'               => $referrer_url ? sanitize_text_field( strtolower( (string) wp_parse_url( $referrer_url, PHP_URL_HOST ) ) ) : '',
		'postal_code'                 => $postal_code,
		'lead_volume'                 => $lead_volume,
		'lead_volume_label'           => $lookup_label( 'lead_volume', $lead_volume ),
		'cpl_range'                   => $cpl_range,
		'cpl_range_label'             => $lookup_label( 'cpl_range', $cpl_range ),
		'primary_bottleneck'          => $primary_bottleneck,
		'primary_bottleneck_label'    => $lookup_label( 'primary_bottleneck', $primary_bottleneck ),
		'solution_focus'              => $solution_focus,
		'solution_focus_label'        => $lookup_label( 'solution_focus', $solution_focus ),
		'sales_audience'              => $sales_audience,
		'sales_audience_label'        => $lookup_label( 'sales_audience', $sales_audience ),
		'region_scope'                => $region_scope,
		'region_scope_label'          => $lookup_label( 'region_scope', $region_scope ),
		'primary_challenge'           => $primary_challenge,
		'primary_challenge_label'     => $lookup_label( 'primary_challenge', $primary_challenge ),
		'measurement_state'           => $measurement_state,
		'measurement_state_label'     => $lookup_label( 'measurement_state', $measurement_state ),
		'acquisition_mix'             => $acquisition_mix,
		'acquisition_mix_label'       => $lookup_label( 'acquisition_mix', $acquisition_mix ),
		'site_state'                  => $site_state,
		'site_state_label'            => $lookup_label( 'site_state', $site_state ),
		'improvement_goal'            => $improvement_goal,
		'improvement_goal_label'      => $lookup_label( 'improvement_goal', $improvement_goal ),
		'project_timing'              => $project_timing,
		'project_timing_label'        => $lookup_label( 'project_timing', $project_timing ),
	];
}

/**
 * Create the CRM post entry for a new request.
 *
 * @param array $payload Validated payload.
 * @return int|WP_Error
 */
function nexus_create_review_request_post( $payload ) {
	$title_parts = array_filter(
		[
			$payload['company'],
			$payload['domain'],
		]
	);

	$post_id = wp_insert_post(
		[
			'post_type'   => 'nexus_review_request',
			'post_status' => 'private',
			'post_title'  => implode( ' - ', $title_parts ),
		],
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	$now = current_time( 'timestamp' );

	update_post_meta( $post_id, '_nexus_review_status', 'new' );
	update_post_meta( $post_id, '_nexus_review_priority', 'normal' );
	update_post_meta( $post_id, '_nexus_review_due_at', $now + ( 48 * HOUR_IN_SECONDS ) );
	update_post_meta( $post_id, '_nexus_review_intake_variant', sanitize_key( (string) ( $payload['intake_variant'] ?? '' ) ) );
	update_post_meta( $post_id, '_nexus_review_intake_variant_label', sanitize_text_field( (string) ( $payload['intake_variant_label'] ?? '' ) ) );
	update_post_meta( $post_id, '_nexus_review_audit_type', $payload['audit_type'] );
	update_post_meta( $post_id, '_nexus_review_audit_type_label', $payload['audit_type_label'] );
	update_post_meta( $post_id, '_nexus_review_page_url', $payload['page_url'] );
	update_post_meta( $post_id, '_nexus_review_domain', $payload['domain'] );
	update_post_meta( $post_id, '_nexus_review_focus_area', $payload['focus_area'] );
	update_post_meta( $post_id, '_nexus_review_focus_area_label', $payload['focus_area_label'] );
	update_post_meta( $post_id, '_nexus_review_current_challenge', $payload['current_challenge'] );
	update_post_meta( $post_id, '_nexus_review_primary_goal', $payload['primary_goal'] );
	update_post_meta( $post_id, '_nexus_review_primary_goal_label', $payload['primary_goal_label'] );
	update_post_meta( $post_id, '_nexus_review_extra_context', $payload['extra_context'] );
	update_post_meta( $post_id, '_nexus_review_name', $payload['name'] );
	update_post_meta( $post_id, '_nexus_review_email', $payload['email'] );
	update_post_meta( $post_id, '_nexus_review_company', $payload['company'] );
	update_post_meta( $post_id, '_nexus_review_phone', sanitize_text_field( (string) ( $payload['phone'] ?? '' ) ) );
	update_post_meta( $post_id, '_nexus_review_linkedin', $payload['linkedin'] );
	update_post_meta( $post_id, '_nexus_review_consent_privacy', $payload['consent_privacy'] );
	update_post_meta(
		$post_id,
		'_nexus_review_source',
		'energy_systems' === ( $payload['intake_variant'] ?? '' )
			? 'energy_systems_landing'
			: ( nexus_is_growth_audit_simple_intake_variant( $payload['intake_variant'] ?? '' ) ? 'growth_audit_landing' : 'growth_audit_funnel' )
	);
	update_post_meta( $post_id, '_nexus_review_ads_source', sanitize_text_field( (string) ( $payload['ads_source'] ?? '' ) ) );
	update_post_meta( $post_id, '_nexus_review_ads_keyword', sanitize_text_field( (string) ( $payload['ads_keyword'] ?? '' ) ) );
	update_post_meta( $post_id, '_nexus_review_landing_page_url', sanitize_text_field( (string) ( $payload['landing_page_url'] ?? '' ) ) );
	update_post_meta( $post_id, '_nexus_review_entry_page_url', sanitize_text_field( (string) ( $payload['entry_page_url'] ?? '' ) ) );
	update_post_meta( $post_id, '_nexus_review_previous_internal_url', sanitize_text_field( (string) ( $payload['previous_internal_url'] ?? '' ) ) );
	update_post_meta( $post_id, '_nexus_review_referrer_url', sanitize_text_field( (string) ( $payload['referrer_url'] ?? '' ) ) );
	update_post_meta( $post_id, '_nexus_review_referrer_host', sanitize_text_field( (string) ( $payload['referrer_host'] ?? '' ) ) );

	if ( 'energy_systems' === ( $payload['intake_variant'] ?? '' ) ) {
		update_post_meta( $post_id, '_nexus_review_energy_postal_code', sanitize_text_field( (string) ( $payload['postal_code'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_lead_volume', sanitize_key( (string) ( $payload['lead_volume'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_lead_volume_label', sanitize_text_field( (string) ( $payload['lead_volume_label'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_cpl_range', sanitize_key( (string) ( $payload['cpl_range'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_cpl_range_label', sanitize_text_field( (string) ( $payload['cpl_range_label'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_primary_bottleneck', sanitize_key( (string) ( $payload['primary_bottleneck'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_primary_bottleneck_label', sanitize_text_field( (string) ( $payload['primary_bottleneck_label'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_solution_focus', sanitize_key( (string) ( $payload['solution_focus'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_solution_focus_label', sanitize_text_field( (string) ( $payload['solution_focus_label'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_sales_audience', sanitize_key( (string) ( $payload['sales_audience'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_sales_audience_label', sanitize_text_field( (string) ( $payload['sales_audience_label'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_region_scope', sanitize_key( (string) ( $payload['region_scope'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_region_scope_label', sanitize_text_field( (string) ( $payload['region_scope_label'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_primary_challenge', sanitize_key( (string) ( $payload['primary_challenge'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_primary_challenge_label', sanitize_text_field( (string) ( $payload['primary_challenge_label'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_measurement_state', sanitize_key( (string) ( $payload['measurement_state'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_measurement_state_label', sanitize_text_field( (string) ( $payload['measurement_state_label'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_acquisition_mix', sanitize_key( (string) ( $payload['acquisition_mix'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_acquisition_mix_label', sanitize_text_field( (string) ( $payload['acquisition_mix_label'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_site_state', sanitize_key( (string) ( $payload['site_state'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_site_state_label', sanitize_text_field( (string) ( $payload['site_state_label'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_improvement_goal', sanitize_key( (string) ( $payload['improvement_goal'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_improvement_goal_label', sanitize_text_field( (string) ( $payload['improvement_goal_label'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_project_timing', sanitize_key( (string) ( $payload['project_timing'] ?? '' ) ) );
		update_post_meta( $post_id, '_nexus_review_energy_project_timing_label', sanitize_text_field( (string) ( $payload['project_timing_label'] ?? '' ) ) );
	}

	if ( function_exists( 'nexus_bump_seo_cockpit_cache_version' ) ) {
		nexus_bump_seo_cockpit_cache_version();
	}

	return (int) $post_id;
}

/**
 * Basic per-IP rate limit for public submissions.
 *
 * @return true|WP_Error
 */
function nexus_validate_review_request_rate_limit() {
	$ip_address = nexus_get_review_request_ip();
	if ( '' === $ip_address ) {
		return true;
	}

	$key   = 'nexus_review_rl_' . md5( $ip_address . gmdate( 'YmdH' ) );
	$count = (int) get_transient( $key );

	if ( $count >= 10 ) {
		return new WP_Error( 'rate_limited', 'Zu viele Anfragen in kurzer Zeit. Bitte später erneut versuchen.' );
	}

	set_transient( $key, $count + 1, HOUR_IN_SECONDS );

	return true;
}

/**
 * Resolve the current request IP for transient-based rate limiting.
 *
 * @return string
 */
function nexus_get_review_request_ip() {
	$candidates = [
		'HTTP_X_FORWARDED_FOR',
		'REMOTE_ADDR',
	];

	foreach ( $candidates as $candidate ) {
		if ( empty( $_SERVER[ $candidate ] ) ) {
			continue;
		}

		$value = (string) wp_unslash( $_SERVER[ $candidate ] );
		if ( 'HTTP_X_FORWARDED_FOR' === $candidate ) {
			$parts = explode( ',', $value );
			$value = trim( (string) reset( $parts ) );
		}

		$value = sanitize_text_field( $value );
		if ( '' !== $value ) {
			return $value;
		}
	}

	return '';
}

/**
 * Append Brevo mail tags to a wp_mail header array.
 *
 * @param array $headers Existing headers.
 * @param array $tags    Tag list.
 * @return array<int, string>
 */
function nexus_append_mail_tags_header( $headers, $tags ) {
	$headers = (array) $headers;
	$tags    = array_values(
		array_unique(
			array_filter(
				array_map(
					static function( $tag ) {
						return sanitize_key( (string) $tag );
					},
					(array) $tags
				)
			)
		)
	);

	if ( empty( $tags ) ) {
		return $headers;
	}

	$headers[] = 'X-Nexus-Brevo-Tags: ' . implode( ',', $tags );

	return $headers;
}

/**
 * Send an HTML transactional email via wp_mail.
 *
 * @param string $recipient Recipient email.
 * @param string $subject   Email subject.
 * @param string $html      Email HTML body.
 * @param array  $headers   Optional additional headers.
 * @return void
 */
function nexus_send_transactional_html_mail( $recipient, $subject, $html, $headers = [] ) {
	if ( ! $recipient || ! is_email( $recipient ) || '' === trim( (string) $html ) ) {
		return;
	}

	$headers   = (array) $headers;
	$headers[] = 'Content-Type: text/html; charset=UTF-8';

	wp_mail( $recipient, $subject, $html, $headers );
}

/**
 * Backward-compatible wrapper for audit mail sends.
 *
 * @param string $recipient Recipient email.
 * @param string $subject   Email subject.
 * @param string $html      Email HTML body.
 * @param array  $headers   Optional additional headers.
 * @return void
 */
function nexus_send_audit_html_mail( $recipient, $subject, $html, $headers = [] ) {
	nexus_send_transactional_html_mail( $recipient, $subject, $html, $headers );
}

/**
 * Wrap transactional emails in a consistent branded shell.
 *
 * @param array $args Email arguments.
 * @return string
 */
function nexus_get_transactional_email_shell( $args = [] ) {
	$args = wp_parse_args(
		$args,
		[
			'preheader' => '',
			'eyebrow'   => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
			'headline'  => '',
			'intro'     => '',
			'content'   => '',
			'footer'    => 'Antworten Sie bei Rückfragen einfach auf diese E-Mail.',
		]
	);

	ob_start();
	?>
	<!doctype html>
	<html lang="de">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo esc_html( $args['headline'] ); ?></title>
	</head>
	<body style="margin:0; padding:0; background:#f3ede6; color:#15181d;">
		<div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">
			<?php echo esc_html( $args['preheader'] ); ?>
		</div>
		<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f3ede6; padding:24px 12px;">
			<tr>
				<td align="center">
					<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:640px;">
						<tr>
							<td style="padding:0 0 12px 0; text-align:left; font-family:Helvetica, Arial, sans-serif; font-size:12px; line-height:1.5; letter-spacing:0.12em; text-transform:uppercase; color:#7f756b;">
								<?php echo esc_html( $args['eyebrow'] ); ?>
							</td>
						</tr>
						<tr>
							<td style="border:1px solid #ddd2c6; border-radius:24px; background:#14191f; padding:32px 28px; box-shadow:0 18px 50px rgba(20, 25, 31, 0.14);">
								<div style="display:inline-block; margin:0 0 16px 0; padding:6px 12px; border-radius:999px; background:rgba(180,106,60,0.12); border:1px solid rgba(180,106,60,0.25); font-family:Helvetica, Arial, sans-serif; font-size:11px; line-height:1.4; letter-spacing:0.1em; text-transform:uppercase; color:#d3a98c;">
									<?php echo esc_html( $args['eyebrow'] ); ?>
								</div>
								<h1 style="margin:0 0 12px 0; font-family:Helvetica, Arial, sans-serif; font-size:30px; line-height:1.12; font-weight:800; color:#f7f3ee;">
									<?php echo esc_html( $args['headline'] ); ?>
								</h1>
								<p style="margin:0 0 24px 0; font-family:Helvetica, Arial, sans-serif; font-size:16px; line-height:1.7; color:#c5ced7;">
									<?php echo esc_html( $args['intro'] ); ?>
								</p>
								<?php echo $args['content']; // raw-ok -- pre-sanitized HTML ?>
							</td>
						</tr>
						<tr>
							<td style="padding:14px 6px 0; font-family:Helvetica, Arial, sans-serif; font-size:12px; line-height:1.7; color:#7f756b; text-align:left;">
								<?php echo esc_html( $args['footer'] ); ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
	</html>
	<?php

	return trim( ob_get_clean() );
}

/**
 * Backward-compatible wrapper for audit email shell rendering.
 *
 * @param array $args Email arguments.
 * @return string
 */
function nexus_get_audit_email_shell( $args = [] ) {
	return nexus_get_transactional_email_shell( $args );
}

/**
 * Return compact detail rows for notification and confirmation emails.
 *
 * @param array $payload Validated payload.
 * @return array<int, array<string, string>>
 */
function nexus_get_review_request_detail_rows( $payload ) {
	$variant = isset( $payload['intake_variant'] ) ? sanitize_key( (string) $payload['intake_variant'] ) : '';
	$rows    = [];

	if ( nexus_is_growth_audit_simple_intake_variant( $variant ) ) {
		$rows = [
			[
				'label' => 'URL',
				'value' => (string) ( $payload['page_url'] ?? '' ),
			],
			[
				'label' => 'Nicht übersehen',
				'value' => (string) ( $payload['current_challenge'] ?? '' ),
			],
		];
	} elseif ( 'energy_systems' === $variant ) {
		$rows = [
			[
				'label' => 'Region (PLZ)',
				'value' => (string) ( $payload['postal_code'] ?? '' ),
			],
			[
				'label' => 'Lead-Volumen',
				'value' => (string) ( $payload['lead_volume_label'] ?? '' ),
			],
			[
				'label' => 'CPL',
				'value' => (string) ( $payload['cpl_range_label'] ?? '' ),
			],
			[
				'label' => 'Engpass',
				'value' => (string) ( $payload['primary_bottleneck_label'] ?? $payload['focus_area_label'] ?? '' ),
			],
			[
				'label' => 'Leistung',
				'value' => (string) ( $payload['solution_focus_label'] ?? '' ),
			],
			[
				'label' => 'Zielmarkt',
				'value' => (string) ( $payload['sales_audience_label'] ?? '' ),
			],
			[
				'label' => 'Aktueller Status',
				'value' => (string) ( $payload['site_state_label'] ?? '' ),
			],
			[
				'label' => 'Timing',
				'value' => (string) ( $payload['project_timing_label'] ?? '' ),
			],
			[
				'label' => 'Website',
				'value' => (string) ( $payload['page_url'] ?? '' ),
			],
			[
				'label' => 'Anmerkung',
				'value' => (string) ( $payload['current_challenge'] ?? '' ),
			],
		];
	} else {
		$rows = [
			[
				'label' => 'URL',
				'value' => (string) ( $payload['page_url'] ?? '' ),
			],
			[
				'label' => 'Bereich',
				'value' => (string) ( $payload['focus_area_label'] ?? '' ),
			],
			[
				'label' => 'Kurzkontext',
				'value' => (string) ( $payload['current_challenge'] ?? '' ),
			],
			[
				'label' => 'Wichtigstes Ziel',
				'value' => (string) ( $payload['primary_goal_label'] ?? '' ),
			],
			[
				'label' => 'Nicht übersehen',
				'value' => (string) ( $payload['extra_context'] ?? '' ),
			],
		];
	}

	return array_values(
		array_filter(
			$rows,
			static function( $row ) {
				return ! empty( $row['value'] );
			}
		)
	);
}

/**
 * Render compact email HTML rows for a validated request.
 *
 * @param array $rows Detail rows.
 * @return string
 */
function nexus_render_review_request_detail_rows_html( $rows ) {
	$markup = [];

	foreach ( (array) $rows as $row ) {
		$label = isset( $row['label'] ) ? trim( (string) $row['label'] ) : '';
		$value = isset( $row['value'] ) ? trim( (string) $row['value'] ) : '';

		if ( '' === $label || '' === $value ) {
			continue;
		}

		$markup[] = sprintf(
			'<strong style="color:#f7f3ee;">%1$s:</strong> %2$s',
			esc_html( $label ),
			esc_html( $value )
		);
	}

	return implode( '<br>', $markup );
}

/**
 * Send the internal notification email for a new request.
 *
 * @param int   $post_id Request post ID.
 * @param array $payload Validated payload.
 * @return void
 */
function nexus_send_review_request_admin_notification( $post_id, $payload ) {
	$recipient = nexus_get_audit_notification_email();
	if ( ! $recipient || ! is_email( $recipient ) ) {
		return;
	}

	$lead_label = $payload['company'] ? $payload['company'] : ( $payload['domain'] ? $payload['domain'] : $payload['name'] );
	$subject    = sprintf(
		'[%s] Neue Anfrage - %s',
		$payload['audit_type_label'],
		$lead_label
	);
	$edit_url = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
	$page_url = $payload['page_url'];
	$headers  = [];
	$detail_rows = nexus_get_review_request_detail_rows( $payload );
	$detail_html = nexus_render_review_request_detail_rows_html( $detail_rows );

	if ( ! empty( $payload['email'] ) && is_email( $payload['email'] ) ) {
		$headers[] = 'Reply-To: ' . $payload['email'];
	}

	$headers = nexus_append_mail_tags_header(
		$headers,
		[
			'audit_request',
			'internal_notification',
			sanitize_key( (string) $payload['audit_type'] ),
		]
	);

	$lead_meta_parts = [ esc_html( $payload['email'] ) ];

	if ( ! empty( $payload['phone'] ) ) {
		$lead_meta_parts[] = esc_html( (string) $payload['phone'] );
	}

	if ( ! empty( $payload['intake_variant_label'] ) ) {
		$lead_meta_parts[] = esc_html( (string) $payload['intake_variant_label'] );
	}

	$lead_meta_html = implode( '<br>', array_filter( $lead_meta_parts ) );
	if ( ! empty( $payload['linkedin'] ) ) {
		$lead_meta_html .= '<br><a href="' . esc_url( $payload['linkedin'] ) . '" style="color:#d3a98c; text-decoration:none;">LinkedIn</a>';
	}

	$content = sprintf(
		'<table role="presentation" width="100%%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 20px 0;">
			<tr>
				<td style="padding:0 0 12px 0;">
					<table role="presentation" width="100%%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:separate; border-spacing:0 10px;">
						<tr>
							<td style="width:50%%; padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
								<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:6px;">Lead</div>
								<div style="font-size:16px; line-height:1.5; color:#f7f3ee; font-weight:700;">%1$s</div>
								<div style="font-size:14px; line-height:1.6; color:#c5ced7;">%2$s<br>%3$s</div>
							</td>
							<td style="width:50%%; padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
								<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:6px;">Audit</div>
								<div style="font-size:16px; line-height:1.5; color:#f7f3ee; font-weight:700;">%4$s</div>
								<div style="font-size:14px; line-height:1.6; color:#c5ced7;">%5$s</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table role="presentation" width="100%%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 18px 0; border-collapse:collapse;">
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Intake</div>
					<div style="font-size:14px; line-height:1.75; color:#c5ced7;">%6$s</div>
				</td>
			</tr>
		</table>
		<table role="presentation" width="100%%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td style="padding:0 12px 0 0;">
					<a href="%7$s" style="display:inline-block; padding:14px 18px; border-radius:14px; background:#b46a3c; color:#fff8f3; text-decoration:none; font-family:Helvetica, Arial, sans-serif; font-size:14px; font-weight:700;">Im CRM öffnen</a>
				</td>
				<td>
					<a href="%8$s" style="display:inline-block; padding:14px 18px; border-radius:14px; border:1px solid rgba(255,255,255,0.12); color:#f7f3ee; text-decoration:none; font-family:Helvetica, Arial, sans-serif; font-size:14px; font-weight:700;">Seite ansehen</a>
				</td>
			</tr>
		</table>',
		esc_html( $lead_label ),
		esc_html( $payload['name'] ),
		$lead_meta_html,
		esc_html( $payload['audit_type_label'] ),
		esc_html( 'Persönliche Rückmeldung spätestens in 48h' ),
		$detail_html,
		esc_url( $edit_url ),
		esc_url( $page_url ? $page_url : admin_url( 'post.php?post=' . $post_id . '&action=edit' ) )
	);

	$ads_source_label  = ! empty( $payload['ads_source'] ) ? $payload['ads_source'] : 'Organisch/Direkt';
	$ads_keyword_label = ! empty( $payload['ads_keyword'] ) ? $payload['ads_keyword'] : 'Keines';

	$content .= sprintf(
		'<table role="presentation" width="100%%%%" cellspacing="0" cellpadding="0" border="0" style="margin:18px 0 0 0; border-collapse:separate; border-spacing:0 10px;">
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Tracking Info</div>
					<div style="font-size:14px; line-height:1.75; color:#c5ced7;">
						<strong style="color:#f7f3ee;">Quelle:</strong> %1$s<br>
						<strong style="color:#f7f3ee;">Keyword:</strong> %2$s
					</div>
				</td>
			</tr>
		</table>',
		esc_html( $ads_source_label ),
		esc_html( $ads_keyword_label )
	);

	$html = nexus_get_transactional_email_shell(
		[
			'preheader' => 'Neue Audit-Anfrage von ' . $lead_label,
			'eyebrow'   => $payload['audit_type_label'],
			'headline'  => 'Neue Audit-Anfrage',
			'intro'     => 'Ein neuer Lead ist eingegangen. Seite und Intake sind unten kompakt zusammengefasst.',
			'content'   => $content,
			'footer'    => 'Sie können direkt auf diese E-Mail antworten. Reply-To zeigt bereits auf den Lead.',
		]
	);

	nexus_send_audit_html_mail( $recipient, $subject, $html, $headers );
}

/**
 * Send a short confirmation email to the requester.
 *
 * @param array $payload Validated payload.
 * @return void
 */
function nexus_send_review_request_confirmation( $payload ) {
	if ( empty( $payload['email'] ) || ! is_email( $payload['email'] ) ) {
		return;
	}

	$reply_to = nexus_get_audit_notification_email();
	$subject  = sprintf( '[%s] %s angefragt', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $payload['audit_type_label'] );
	$detail_rows = nexus_get_review_request_detail_rows( $payload );
	$detail_html = nexus_render_review_request_detail_rows_html( $detail_rows );

	$content  = sprintf(
		'<table role="presentation" width="100%%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 20px 0; border-collapse:separate; border-spacing:0 10px;">
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:6px;">Was jetzt passiert</div>
					<div style="font-size:14px; line-height:1.8; color:#c5ced7;">
						<strong style="color:#f7f3ee;">1.</strong> Ihre Anfrage ist sauber im System.<br>
						<strong style="color:#f7f3ee;">2.</strong> Ich prüfe die Seite manuell und ergänze die Einschätzung durch KI-Unterstützung.<br>
						<strong style="color:#f7f3ee;">3.</strong> Innerhalb von 48 Stunden erhalten Sie die Rückmeldung per E-Mail.
					</div>
				</td>
			</tr>
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Ihre Anfrage</div>
					<div style="font-size:14px; line-height:1.75; color:#c5ced7;">%1$s</div>
				</td>
			</tr>
		</table>
		<p style="margin:0; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:1.8; color:#c5ced7;">
			Sie müssen jetzt nichts weiter vorbereiten. Wenn es eine Frist, Kampagne oder interne Deadline gibt,
			antworten Sie einfach kurz auf diese E-Mail.
		</p>',
		$detail_html
	);

	$html = nexus_get_audit_email_shell(
		[
			'preheader' => 'Ihre Anfrage für den ' . $payload['audit_type_label'] . ' ist eingegangen.',
			'eyebrow'   => $payload['audit_type_label'],
			'headline'  => 'Ihr ' . $payload['audit_type_label'] . ' ist im System.',
			'intro'     => 'Danke, ' . $payload['name'] . '. Ich prüfe die Seite und melde mich innerhalb von 48 Stunden per E-Mail.',
			'content'   => $content,
			'footer'    => 'Viele Grüße, Haşim Üner',
		]
	);

	$headers = [];
	if ( $reply_to && is_email( $reply_to ) ) {
		$headers[] = 'Reply-To: ' . $reply_to;
	}

	$headers = nexus_append_mail_tags_header(
		$headers,
		[
			'audit_request',
			'lead_confirmation',
			sanitize_key( (string) $payload['audit_type'] ),
		]
	);

	nexus_send_audit_html_mail( $payload['email'], $subject, $html, $headers );
}

/**
 * Register the admin meta boxes for request handling.
 *
 * @return void
 */
function nexus_register_review_request_meta_boxes() {
	add_meta_box(
		'nexus-review-request-details',
		'Anfrage',
		'nexus_render_review_request_details_meta_box',
		'nexus_review_request',
		'normal',
		'high'
	);

	add_meta_box(
		'nexus-review-request-workflow',
		'CRM-Workflow',
		'nexus_render_review_request_workflow_meta_box',
		'nexus_review_request',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes_nexus_review_request', 'nexus_register_review_request_meta_boxes' );

/**
 * Render the read-only request detail box.
 *
 * @param WP_Post $post Current post object.
 * @return void
 */
function nexus_render_review_request_details_meta_box( $post ) {
	$audit_type        = (string) get_post_meta( $post->ID, '_nexus_review_audit_type_label', true );
	$intake_variant    = (string) get_post_meta( $post->ID, '_nexus_review_intake_variant', true );
	$variant_label     = (string) get_post_meta( $post->ID, '_nexus_review_intake_variant_label', true );
	$page_url          = (string) get_post_meta( $post->ID, '_nexus_review_page_url', true );
	$landing_page_url  = (string) get_post_meta( $post->ID, '_nexus_review_landing_page_url', true );
	$entry_page_url    = (string) get_post_meta( $post->ID, '_nexus_review_entry_page_url', true );
	$previous_page_url = (string) get_post_meta( $post->ID, '_nexus_review_previous_internal_url', true );
	$referrer_url      = (string) get_post_meta( $post->ID, '_nexus_review_referrer_url', true );
	$focus_area_label  = nexus_get_review_meta_value( $post->ID, '_nexus_review_focus_area_label' );
	$current_challenge = nexus_get_review_meta_value( $post->ID, '_nexus_review_current_challenge' );
	$primary_goal_label = nexus_get_review_meta_value( $post->ID, '_nexus_review_primary_goal_label' );
	$linkedin          = nexus_get_review_meta_value( $post->ID, '_nexus_review_linkedin' );
	$extra_context     = (string) get_post_meta( $post->ID, '_nexus_review_extra_context', true );
	$name              = (string) get_post_meta( $post->ID, '_nexus_review_name', true );
	$email             = (string) get_post_meta( $post->ID, '_nexus_review_email', true );
	$company           = (string) get_post_meta( $post->ID, '_nexus_review_company', true );
	$phone             = (string) get_post_meta( $post->ID, '_nexus_review_phone', true );
	$consent_privacy   = (string) get_post_meta( $post->ID, '_nexus_review_consent_privacy', true );
	$offer             = (string) get_post_meta( $post->ID, '_nexus_review_offer', true );
	$audience          = (string) get_post_meta( $post->ID, '_nexus_review_audience', true );
	$issue_label       = (string) get_post_meta( $post->ID, '_nexus_review_biggest_issue_label', true );
	$is_simple_intake  = nexus_is_growth_audit_simple_intake_variant( $intake_variant );
	$has_new_intake    = $is_simple_intake || '' !== trim( $focus_area_label . $current_challenge . $primary_goal_label . $linkedin );
	$is_energy_intake  = 'energy_systems' === $intake_variant;
	$energy_solution   = (string) get_post_meta( $post->ID, '_nexus_review_energy_solution_focus_label', true );
	$energy_audience   = (string) get_post_meta( $post->ID, '_nexus_review_energy_sales_audience_label', true );
	$energy_region     = (string) get_post_meta( $post->ID, '_nexus_review_energy_region_scope_label', true );
	$energy_measurement = (string) get_post_meta( $post->ID, '_nexus_review_energy_measurement_state_label', true );
	$energy_acquisition = (string) get_post_meta( $post->ID, '_nexus_review_energy_acquisition_mix_label', true );
	$energy_site_state  = (string) get_post_meta( $post->ID, '_nexus_review_energy_site_state_label', true );
	$energy_timing      = (string) get_post_meta( $post->ID, '_nexus_review_energy_project_timing_label', true );
	?>
	<div class="nexus-review-meta">
		<div class="nexus-review-meta-group">
			<strong>Audit-Typ</strong>
			<p><?php echo esc_html( $audit_type ?: 'Marktcheck' ); ?></p>
		</div>
		<div class="nexus-review-meta-group">
			<strong>Unternehmen</strong>
			<p><?php echo esc_html( $company ?: 'Nicht angegeben' ); ?></p>
		</div>
		<div class="nexus-review-meta-group">
			<strong>Kontakt</strong>
			<p><?php echo esc_html( $name ); ?><br><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
		</div>
		<?php if ( '' !== $phone ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Telefon</strong>
				<p><?php echo esc_html( $phone ); ?></p>
			</div>
		<?php endif; ?>
		<div class="nexus-review-meta-group">
			<strong>Datenschutzhinweis</strong>
			<p><?php echo esc_html( 'accepted' === $consent_privacy ? 'Bestätigt' : 'Fehlt' ); ?></p>
		</div>
		<?php if ( '' !== $variant_label ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Quelle</strong>
				<p><?php echo esc_html( $variant_label ); ?></p>
			</div>
		<?php endif; ?>
		<div class="nexus-review-meta-group">
			<strong>Seite</strong>
			<?php if ( '' !== $page_url ) : ?>
				<p><a href="<?php echo esc_url( $page_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $page_url ); ?></a></p>
			<?php else : ?>
				<p>Nicht angegeben</p>
			<?php endif; ?>
		</div>
		<?php if ( '' !== $landing_page_url || '' !== $entry_page_url || '' !== $previous_page_url || '' !== $referrer_url ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Attribution</strong>
				<p>
					<?php if ( '' !== $landing_page_url ) : ?>
						Form-Seite: <a href="<?php echo esc_url( $landing_page_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $landing_page_url ); ?></a><br>
					<?php endif; ?>
					<?php if ( '' !== $entry_page_url ) : ?>
						Einstieg intern: <a href="<?php echo esc_url( $entry_page_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $entry_page_url ); ?></a><br>
					<?php endif; ?>
					<?php if ( '' !== $previous_page_url ) : ?>
						Vorherige interne Seite: <a href="<?php echo esc_url( $previous_page_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $previous_page_url ); ?></a><br>
					<?php endif; ?>
					<?php if ( '' !== $referrer_url ) : ?>
						Referrer: <a href="<?php echo esc_url( $referrer_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $referrer_url ); ?></a>
					<?php endif; ?>
				</p>
			</div>
		<?php endif; ?>

		<?php if ( $has_new_intake ) : ?>
			<?php if ( $is_simple_intake ) : ?>
				<?php if ( '' !== $current_challenge ) : ?>
					<div class="nexus-review-meta-group">
						<strong>Nicht übersehen</strong>
						<p><?php echo nl2br( esc_html( $current_challenge ) ); ?></p>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<div class="nexus-review-meta-group">
					<strong>Bereich mit Klärungsbedarf</strong>
					<p><?php echo esc_html( $focus_area_label ); ?></p>
				</div>
				<?php if ( '' !== $current_challenge ) : ?>
					<div class="nexus-review-meta-group">
						<strong>Kurzkontext</strong>
						<p><?php echo nl2br( esc_html( $current_challenge ) ); ?></p>
					</div>
				<?php endif; ?>
				<div class="nexus-review-meta-group">
					<strong>Wichtigstes Ziel</strong>
					<p><?php echo esc_html( $primary_goal_label ); ?></p>
				</div>
				<?php if ( '' !== $linkedin ) : ?>
					<div class="nexus-review-meta-group">
						<strong>LinkedIn</strong>
						<p><a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $linkedin ); ?></a></p>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ( $is_energy_intake ) : ?>
				<div class="nexus-review-meta-group">
					<strong>Leistung</strong>
					<p><?php echo esc_html( $energy_solution ?: 'Nicht angegeben' ); ?></p>
				</div>
				<div class="nexus-review-meta-group">
					<strong>Zielmarkt</strong>
					<p><?php echo esc_html( $energy_audience ?: 'Nicht angegeben' ); ?></p>
				</div>
				<div class="nexus-review-meta-group">
					<strong>Region</strong>
					<p><?php echo esc_html( $energy_region ?: 'Nicht angegeben' ); ?></p>
				</div>
				<?php if ( '' !== $energy_measurement ) : ?>
					<div class="nexus-review-meta-group">
						<strong>Messbarkeit</strong>
						<p><?php echo esc_html( $energy_measurement ); ?></p>
					</div>
				<?php endif; ?>
				<div class="nexus-review-meta-group">
					<strong>Anfragemix</strong>
					<p><?php echo esc_html( $energy_acquisition ?: 'Nicht angegeben' ); ?></p>
				</div>
				<div class="nexus-review-meta-group">
					<strong>Aktueller Status</strong>
					<p><?php echo esc_html( $energy_site_state ?: 'Nicht angegeben' ); ?></p>
				</div>
				<div class="nexus-review-meta-group">
					<strong>Timing</strong>
					<p><?php echo esc_html( $energy_timing ?: 'Nicht angegeben' ); ?></p>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<div class="nexus-review-meta-group">
				<strong>Was soll die Seite verkaufen?</strong>
				<p><?php echo nl2br( esc_html( $offer ) ); ?></p>
			</div>
			<div class="nexus-review-meta-group">
				<strong>Wen soll die Seite überzeugen?</strong>
				<p><?php echo nl2br( esc_html( $audience ) ); ?></p>
			</div>
			<div class="nexus-review-meta-group">
				<strong>Größter Blocker</strong>
				<p><?php echo esc_html( $issue_label ); ?></p>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $extra_context ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Nicht übersehen</strong>
				<p><?php echo nl2br( esc_html( $extra_context ) ); ?></p>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render the editable workflow meta box.
 *
 * @param WP_Post $post Current post object.
 * @return void
 */
function nexus_render_review_request_workflow_meta_box( $post ) {
	$status_options   = nexus_get_review_status_options();
	$priority_options = nexus_get_review_priority_options();
	$current_status   = (string) get_post_meta( $post->ID, '_nexus_review_status', true );
	$current_priority = (string) get_post_meta( $post->ID, '_nexus_review_priority', true );
	$delivery_url     = (string) get_post_meta( $post->ID, '_nexus_review_delivery_url', true );
	$internal_notes   = (string) get_post_meta( $post->ID, '_nexus_review_internal_notes', true );
	$due_at           = (int) get_post_meta( $post->ID, '_nexus_review_due_at', true );
	$sent_at          = (int) get_post_meta( $post->ID, '_nexus_review_sent_at', true );

	wp_nonce_field( 'nexus_save_review_request_workflow', 'nexus_review_request_workflow_nonce' );
	?>
	<p>
		<label for="nexus-review-status"><strong>Status</strong></label><br>
		<select id="nexus-review-status" name="nexus_review_status" class="widefat">
			<?php foreach ( $status_options as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_status, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="nexus-review-priority"><strong>Priorität</strong></label><br>
		<select id="nexus-review-priority" name="nexus_review_priority" class="widefat">
			<?php foreach ( $priority_options as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_priority, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<strong>Fälligkeitsziel</strong><br>
		<span><?php echo esc_html( $due_at ? wp_date( 'd.m.Y H:i', $due_at ) : 'n/a' ); ?></span>
	</p>
	<p>
		<strong>Rückmeldung gesendet</strong><br>
		<span><?php echo esc_html( $sent_at ? wp_date( 'd.m.Y H:i', $sent_at ) : 'Noch nicht markiert' ); ?></span>
	</p>
	<p>
		<label for="nexus-review-delivery-url"><strong>Audit-Link / Loom</strong></label><br>
		<input id="nexus-review-delivery-url" name="nexus_review_delivery_url" type="url" class="widefat" value="<?php echo esc_attr( $delivery_url ); ?>" placeholder="https://...">
	</p>
	<p>
		<label for="nexus-review-internal-notes"><strong>Interne Notizen</strong></label><br>
		<textarea id="nexus-review-internal-notes" name="nexus_review_internal_notes" class="widefat" rows="8"><?php echo esc_textarea( $internal_notes ); ?></textarea>
	</p>
	<?php
}

/**
 * Persist the workflow fields from the CRM meta box.
 *
 * @param int $post_id Current post ID.
 * @return void
 */
function nexus_save_review_request_workflow_meta( $post_id ) {
	if ( empty( $_POST['nexus_review_request_workflow_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nexus_review_request_workflow_nonce'] ) ), 'nexus_save_review_request_workflow' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$status_options   = nexus_get_review_status_options();
	$priority_options = nexus_get_review_priority_options();
	$old_status       = (string) get_post_meta( $post_id, '_nexus_review_status', true );
	$new_status       = isset( $_POST['nexus_review_status'] ) ? sanitize_key( (string) wp_unslash( $_POST['nexus_review_status'] ) ) : $old_status;
	$new_priority     = isset( $_POST['nexus_review_priority'] ) ? sanitize_key( (string) wp_unslash( $_POST['nexus_review_priority'] ) ) : 'normal';
	$delivery_url     = isset( $_POST['nexus_review_delivery_url'] ) ? esc_url_raw( (string) wp_unslash( $_POST['nexus_review_delivery_url'] ) ) : '';
	$internal_notes   = isset( $_POST['nexus_review_internal_notes'] ) ? sanitize_textarea_field( (string) wp_unslash( $_POST['nexus_review_internal_notes'] ) ) : '';

	if ( isset( $status_options[ $new_status ] ) ) {
		update_post_meta( $post_id, '_nexus_review_status', $new_status );
	}

	if ( isset( $priority_options[ $new_priority ] ) ) {
		update_post_meta( $post_id, '_nexus_review_priority', $new_priority );
	}

	update_post_meta( $post_id, '_nexus_review_delivery_url', $delivery_url );
	update_post_meta( $post_id, '_nexus_review_internal_notes', $internal_notes );

	if ( 'sent' === $new_status && 'sent' !== $old_status ) {
		update_post_meta( $post_id, '_nexus_review_sent_at', current_time( 'timestamp' ) );
	}
}
add_action( 'save_post_nexus_review_request', 'nexus_save_review_request_workflow_meta' );

/**
 * Customize the list table columns.
 *
 * @param array $columns Default columns.
 * @return array
 */
function nexus_filter_review_request_columns( $columns ) {
	$columns = [
		'cb'              => $columns['cb'],
		'title'           => 'Lead',
		'audit_type'      => 'Audit',
		'review_status'   => 'Status',
		'review_priority' => 'Priorität',
		'review_page'     => 'Seite',
		'review_goal'     => 'Audit-Fokus / Hinweis',
		'review_contact'  => 'Kontakt',
		'review_due'      => 'Fällig',
		'date'            => $columns['date'],
	];

	return $columns;
}
add_filter( 'manage_nexus_review_request_posts_columns', 'nexus_filter_review_request_columns' );

/**
 * Render the custom list table columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Current post ID.
 * @return void
 */
function nexus_render_review_request_columns( $column, $post_id ) {
	$status_options   = nexus_get_review_status_options();
	$priority_options = nexus_get_review_priority_options();

	switch ( $column ) {
		case 'audit_type':
			echo esc_html( (string) get_post_meta( $post_id, '_nexus_review_audit_type_label', true ) ?: 'Marktcheck' );
			break;

		case 'review_status':
			$status = (string) get_post_meta( $post_id, '_nexus_review_status', true );
			printf(
				'<span class="nexus-review-badge nexus-review-badge-%1$s">%2$s</span>',
				esc_attr( $status ),
				esc_html( $status_options[ $status ] ?? 'Unbekannt' )
			);
			break;

		case 'review_priority':
			$priority = (string) get_post_meta( $post_id, '_nexus_review_priority', true );
			printf(
				'<span class="nexus-review-badge nexus-review-badge-priority-%1$s">%2$s</span>',
				esc_attr( $priority ),
				esc_html( $priority_options[ $priority ] ?? 'Normal' )
			);
			break;

		case 'review_page':
			$page_url = (string) get_post_meta( $post_id, '_nexus_review_page_url', true );
			$domain   = (string) get_post_meta( $post_id, '_nexus_review_domain', true );
			if ( $page_url ) {
				printf(
					'<a href="%1$s" target="_blank" rel="noopener">%2$s</a><div class="nexus-review-muted">%3$s</div>',
					esc_url( $page_url ),
					esc_html( $domain ?: $page_url ),
					esc_html( $page_url )
				);
			} elseif ( $domain ) {
				echo esc_html( $domain );
				echo '<div class="nexus-review-muted">keine URL angegeben</div>';
			}
			break;

		case 'review_goal':
			$variant            = (string) get_post_meta( $post_id, '_nexus_review_intake_variant', true );
			$focus_area_label   = nexus_get_review_meta_value( $post_id, '_nexus_review_focus_area_label' );
			$primary_goal_label = nexus_get_review_meta_value( $post_id, '_nexus_review_primary_goal_label' );
			$current_challenge  = nexus_get_review_meta_value( $post_id, '_nexus_review_current_challenge' );

			if ( nexus_is_growth_audit_simple_intake_variant( $variant ) ) {
				echo esc_html( $current_challenge ? wp_trim_words( $current_challenge, 12 ) : '1-Schritt-Intake' );
				break;
			}

			if ( $focus_area_label || $primary_goal_label ) {
				echo esc_html( $focus_area_label ?: $primary_goal_label );
				if ( $focus_area_label && $primary_goal_label ) {
					echo '<div class="nexus-review-muted">' . esc_html( $primary_goal_label ) . '</div>';
				}
			} else {
				echo esc_html( wp_trim_words( (string) get_post_meta( $post_id, '_nexus_review_offer', true ), 12 ) );
			}
			break;

		case 'review_contact':
			$company = (string) get_post_meta( $post_id, '_nexus_review_company', true );
			$domain  = (string) get_post_meta( $post_id, '_nexus_review_domain', true );
			$name    = (string) get_post_meta( $post_id, '_nexus_review_name', true );
			$email   = (string) get_post_meta( $post_id, '_nexus_review_email', true );
			echo '<strong>' . esc_html( $company ?: $domain ) . '</strong><br>';
			echo esc_html( $name ) . '<br>';
			echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
			break;

		case 'review_due':
			$due_at = (int) get_post_meta( $post_id, '_nexus_review_due_at', true );
			if ( $due_at ) {
				$is_overdue = $due_at < current_time( 'timestamp' );
				printf(
					'<span class="%1$s">%2$s</span>',
					esc_attr( $is_overdue ? 'nexus-review-overdue' : 'nexus-review-due' ),
					esc_html( wp_date( 'd.m.Y H:i', $due_at ) )
				);
			}
			break;
	}
}
add_action( 'manage_nexus_review_request_posts_custom_column', 'nexus_render_review_request_columns', 10, 2 );

/**
 * Add CRM filters above the request list table.
 *
 * @param string $post_type Current post type.
 * @return void
 */
function nexus_render_review_request_filters( $post_type ) {
	if ( 'nexus_review_request' !== $post_type ) {
		return;
	}

	$current_status   = isset( $_GET['nexus_review_status'] ) ? sanitize_key( (string) wp_unslash( $_GET['nexus_review_status'] ) ) : '';
	$current_priority = isset( $_GET['nexus_review_priority'] ) ? sanitize_key( (string) wp_unslash( $_GET['nexus_review_priority'] ) ) : '';
	?>
	<select name="nexus_review_status">
		<option value="">Alle Status</option>
		<?php foreach ( nexus_get_review_status_options() as $value => $label ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_status, $value ); ?>><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>
	<select name="nexus_review_priority">
		<option value="">Alle Prioritäten</option>
		<?php foreach ( nexus_get_review_priority_options() as $value => $label ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_priority, $value ); ?>><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}
add_action( 'restrict_manage_posts', 'nexus_render_review_request_filters' );

/**
 * Apply CRM filters to the request list query.
 *
 * @param WP_Query $query Query object.
 * @return void
 */
function nexus_filter_review_request_admin_query( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$post_type = $query->get( 'post_type' );
	if ( 'nexus_review_request' !== $post_type ) {
		return;
	}

	$meta_query = (array) $query->get( 'meta_query' );

	if ( ! empty( $_GET['nexus_review_status'] ) ) {
		$meta_query[] = [
			'key'   => '_nexus_review_status',
			'value' => sanitize_key( (string) wp_unslash( $_GET['nexus_review_status'] ) ),
		];
	}

	if ( ! empty( $_GET['nexus_review_priority'] ) ) {
		$meta_query[] = [
			'key'   => '_nexus_review_priority',
			'value' => sanitize_key( (string) wp_unslash( $_GET['nexus_review_priority'] ) ),
		];
	}

	if ( ! empty( $meta_query ) ) {
		$query->set( 'meta_query', $meta_query );
	}

	$query->set( 'orderby', 'date' );
	$query->set( 'order', 'DESC' );
}
add_action( 'pre_get_posts', 'nexus_filter_review_request_admin_query' );

/**
 * Render the custom CRM dashboard page.
 *
 * @return void
 */
function nexus_render_review_crm_dashboard() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( 'Keine Berechtigung.' );
	}

	$counts = [
		'audit_new'    => nexus_count_review_requests_by_status( 'new' ),
		'projects'     => function_exists( 'nexus_count_project_requests' ) ? nexus_count_project_requests() : 0,
		'blog_active'  => function_exists( 'nexus_count_active_blog_subscribers' ) ? nexus_count_active_blog_subscribers() : 0,
		'blog_pending' => function_exists( 'nexus_count_pending_blog_subscribers' ) ? nexus_count_pending_blog_subscribers() : 0,
	];

	$recent_audits = get_posts(
		[
			'post_type'              => 'nexus_review_request',
			'post_status'            => 'private',
			'posts_per_page'         => 6,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		]
	);

	$recent_projects = function_exists( 'nexus_get_recent_crm_contacts' )
		? nexus_get_recent_crm_contacts(
			[
				'posts_per_page' => 6,
				'meta_query'     => [
					[
						'key'   => '_nexus_contact_segment_project_request',
						'value' => 1,
					],
				],
			]
		)
		: [];

	$recent_blog_subscribers = function_exists( 'nexus_get_recent_crm_contacts' )
		? nexus_get_recent_crm_contacts(
			[
				'posts_per_page' => 6,
				'meta_query'     => [
					[
						'key'   => '_nexus_contact_segment_blog_notify',
						'value' => 1,
					],
				],
			]
		)
		: [];
	?>
	<div class="wrap nexus-review-dashboard">
		<h1>Nexus CRM</h1>
		<p class="nexus-review-dashboard-intro">Hier laufen Audit-Anfragen, Projektanfragen und Blog-Abos zusammen. WordPress bleibt die zentrale Daten- und Logikschicht; Brevo bleibt reine Zustellungsebene für Transaktionsmails.</p>

		<div class="nexus-review-stats">
			<a class="nexus-review-stat-card" href="<?php echo esc_url( admin_url( 'edit.php?post_type=nexus_review_request&nexus_review_status=new' ) ); ?>">
				<span class="nexus-review-stat-label">Audit-Anfragen neu</span>
				<strong class="nexus-review-stat-value"><?php echo esc_html( (string) $counts['audit_new'] ); ?></strong>
			</a>
			<a class="nexus-review-stat-card" href="<?php echo esc_url( admin_url( 'edit.php?post_type=nexus_contact&nexus_contact_segment=project_request' ) ); ?>">
				<span class="nexus-review-stat-label">Projektanfragen</span>
				<strong class="nexus-review-stat-value"><?php echo esc_html( (string) $counts['projects'] ); ?></strong>
			</a>
			<a class="nexus-review-stat-card" href="<?php echo esc_url( admin_url( 'edit.php?post_type=nexus_contact&nexus_contact_segment=blog_notify&nexus_contact_blog_status=active' ) ); ?>">
				<span class="nexus-review-stat-label">Blog-Abos aktiv</span>
				<strong class="nexus-review-stat-value"><?php echo esc_html( (string) $counts['blog_active'] ); ?></strong>
			</a>
			<div class="nexus-review-stat-card nexus-review-stat-card-warning">
				<span class="nexus-review-stat-label">DOI ausstehend</span>
				<strong class="nexus-review-stat-value"><?php echo esc_html( (string) $counts['blog_pending'] ); ?></strong>
			</div>
		</div>

		<div class="nexus-review-panel">
			<div class="nexus-review-panel-head">
				<h2>Letzte Audit-Anfragen</h2>
				<a class="button button-secondary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=nexus_review_request' ) ); ?>">Alle Audit-Anfragen</a>
			</div>

			<?php if ( empty( $recent_audits ) ) : ?>
				<p>Noch keine Audit-Anfragen vorhanden.</p>
			<?php else : ?>
				<table class="widefat fixed striped nexus-review-table">
					<thead>
						<tr>
							<th>Lead</th>
							<th>Status</th>
							<th>Seite</th>
							<th>Fällig</th>
							<th>Aktion</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $recent_audits as $request_post ) : ?>
							<?php
							$status   = (string) get_post_meta( $request_post->ID, '_nexus_review_status', true );
							$company  = (string) get_post_meta( $request_post->ID, '_nexus_review_company', true );
							$domain   = (string) get_post_meta( $request_post->ID, '_nexus_review_domain', true );
							$name     = (string) get_post_meta( $request_post->ID, '_nexus_review_name', true );
							$page_url = (string) get_post_meta( $request_post->ID, '_nexus_review_page_url', true );
							$due_at   = (int) get_post_meta( $request_post->ID, '_nexus_review_due_at', true );
							?>
							<tr>
								<td>
									<strong><?php echo esc_html( $company ?: $domain ?: $name ); ?></strong><br>
									<span class="nexus-review-muted"><?php echo esc_html( $name ); ?></span>
								</td>
								<td>
									<span class="nexus-review-badge nexus-review-badge-<?php echo esc_attr( $status ); ?>">
										<?php echo esc_html( nexus_get_review_status_options()[ $status ] ?? 'Unbekannt' ); ?>
									</span>
								</td>
								<td>
									<?php if ( '' !== $page_url ) : ?>
										<a href="<?php echo esc_url( $page_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $page_url ); ?></a>
									<?php else : ?>
										<span class="nexus-review-muted"><?php echo esc_html( $domain ? $domain . ' (keine URL)' : 'keine URL angegeben' ); ?></span>
									<?php endif; ?>
								</td>
								<td>
									<?php if ( $due_at ) : ?>
										<span class="<?php echo esc_attr( $due_at < current_time( 'timestamp' ) ? 'nexus-review-overdue' : 'nexus-review-due' ); ?>">
											<?php echo esc_html( wp_date( 'd.m.Y H:i', $due_at ) ); ?>
										</span>
									<?php endif; ?>
								</td>
								<td><a class="button button-small" href="<?php echo esc_url( get_edit_post_link( $request_post->ID ) ); ?>">Öffnen</a></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>

		<div class="nexus-review-panel">
			<div class="nexus-review-panel-head">
				<h2>Letzte Projektanfragen</h2>
				<a class="button button-secondary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=nexus_contact&nexus_contact_segment=project_request' ) ); ?>">Alle Projektanfragen</a>
			</div>

			<?php if ( empty( $recent_projects ) ) : ?>
				<p>Noch keine Projektanfragen im CRM vorhanden.</p>
			<?php else : ?>
				<table class="widefat fixed striped nexus-review-table">
					<thead>
						<tr>
							<th>Kontakt</th>
							<th>Thema</th>
							<th>Status</th>
							<th>Aktualisiert</th>
							<th>Aktion</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $recent_projects as $contact_post ) : ?>
							<?php
							$name       = (string) get_post_meta( $contact_post->ID, '_nexus_contact_name', true );
							$email      = (string) get_post_meta( $contact_post->ID, '_nexus_contact_email', true );
							$focus      = (string) get_post_meta( $contact_post->ID, '_nexus_contact_focus_label', true );
							$status     = (string) get_post_meta( $contact_post->ID, '_nexus_contact_status', true );
							$updated_at = (int) get_post_meta( $contact_post->ID, '_nexus_contact_updated_at', true );
							?>
							<tr>
								<td>
									<strong><?php echo esc_html( $name ?: get_the_title( $contact_post ) ); ?></strong><br>
									<span class="nexus-review-muted"><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></span>
								</td>
								<td><?php echo esc_html( $focus ?: 'Projektanfrage' ); ?></td>
								<td>
									<span class="nexus-review-badge <?php echo esc_attr( function_exists( 'nexus_get_crm_contact_status_badge_class' ) ? nexus_get_crm_contact_status_badge_class( $status ) : 'nexus-review-badge-new' ); ?>">
										<?php echo esc_html( function_exists( 'nexus_get_crm_contact_status_options' ) ? ( nexus_get_crm_contact_status_options()[ $status ] ?? 'Unbekannt' ) : $status ); ?>
									</span>
								</td>
								<td><?php echo esc_html( $updated_at ? wp_date( 'd.m.Y H:i', $updated_at ) : 'n/a' ); ?></td>
								<td><a class="button button-small" href="<?php echo esc_url( get_edit_post_link( $contact_post->ID ) ); ?>">Öffnen</a></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>

		<div class="nexus-review-panel">
			<div class="nexus-review-panel-head">
				<h2>Letzte Blog-Abos</h2>
				<a class="button button-secondary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=nexus_contact&nexus_contact_segment=blog_notify' ) ); ?>">Alle Blog-Abos</a>
			</div>

			<?php if ( empty( $recent_blog_subscribers ) ) : ?>
				<p>Noch keine Blog-Abos im CRM vorhanden.</p>
			<?php else : ?>
				<table class="widefat fixed striped nexus-review-table">
					<thead>
						<tr>
							<th>E-Mail</th>
							<th>Status</th>
							<th>Consent</th>
							<th>Aktualisiert</th>
							<th>Aktion</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $recent_blog_subscribers as $contact_post ) : ?>
							<?php
							$email      = (string) get_post_meta( $contact_post->ID, '_nexus_contact_email', true );
							$status     = (string) get_post_meta( $contact_post->ID, '_nexus_contact_blog_status', true );
							$consent    = (string) get_post_meta( $contact_post->ID, '_nexus_contact_consent_blog_email', true );
							$updated_at = (int) get_post_meta( $contact_post->ID, '_nexus_contact_updated_at', true );
							?>
							<tr>
								<td><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></td>
								<td>
									<span class="nexus-review-badge <?php echo esc_attr( function_exists( 'nexus_get_crm_contact_status_badge_class' ) ? nexus_get_crm_contact_status_badge_class( $status ) : 'nexus-review-badge-new' ); ?>">
										<?php echo esc_html( function_exists( 'nexus_get_crm_contact_status_options' ) ? ( nexus_get_crm_contact_status_options()[ $status ] ?? 'Unbekannt' ) : $status ); ?>
									</span>
								</td>
								<td><?php echo esc_html( $consent ?: 'n/a' ); ?></td>
								<td><?php echo esc_html( $updated_at ? wp_date( 'd.m.Y H:i', $updated_at ) : 'n/a' ); ?></td>
								<td><a class="button button-small" href="<?php echo esc_url( get_edit_post_link( $contact_post->ID ) ); ?>">Öffnen</a></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Add a CRM snapshot widget to the default WordPress dashboard.
 *
 * @return void
 */
function nexus_register_review_crm_dashboard_widget() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	wp_add_dashboard_widget(
		'nexus_review_crm_dashboard_widget',
		'Nexus CRM Snapshot',
		'nexus_render_review_crm_dashboard_widget'
	);
}
add_action( 'wp_dashboard_setup', 'nexus_register_review_crm_dashboard_widget' );

/**
 * Render the dashboard widget markup.
 *
 * @return void
 */
function nexus_render_review_crm_dashboard_widget() {
	$new_count          = nexus_count_review_requests_by_status( 'new' );
	$project_count      = function_exists( 'nexus_count_project_requests' ) ? nexus_count_project_requests() : 0;
	$blog_pending_count = function_exists( 'nexus_count_pending_blog_subscribers' ) ? nexus_count_pending_blog_subscribers() : 0;
	$latest_request     = get_posts(
		[
			'post_type'              => 'nexus_review_request',
			'post_status'            => 'private',
			'posts_per_page'         => 1,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		]
	);
	?>
	<div class="nexus-review-widget">
		<p><strong>Audit neu:</strong> <?php echo esc_html( (string) $new_count ); ?> | <strong>Projektanfragen:</strong> <?php echo esc_html( (string) $project_count ); ?> | <strong>DOI ausstehend:</strong> <?php echo esc_html( (string) $blog_pending_count ); ?></p>
		<?php if ( ! empty( $latest_request ) ) : ?>
			<?php
			$latest  = $latest_request[0];
			$company = (string) get_post_meta( $latest->ID, '_nexus_review_company', true );
			$domain  = (string) get_post_meta( $latest->ID, '_nexus_review_domain', true );
			$page_url = (string) get_post_meta( $latest->ID, '_nexus_review_page_url', true );
			?>
			<p>
				<strong>Letzte Audit-Anfrage:</strong> <?php echo esc_html( $company ?: $domain ?: 'Ohne Unternehmensname' ); ?><br>
				<?php if ( '' !== $page_url ) : ?>
					<a href="<?php echo esc_url( $page_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $page_url ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $domain ? $domain . ' (keine URL)' : 'keine URL angegeben' ); ?>
				<?php endif; ?>
			</p>
		<?php endif; ?>
		<p><a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=' . ( function_exists( 'nexus_get_crm_menu_slug' ) ? nexus_get_crm_menu_slug() : 'nexus-crm' ) ) ); ?>">Zum Nexus CRM</a></p>
	</div>
	<?php
}

/**
 * Count requests by a given workflow status.
 *
 * @param string $status Status key.
 * @return int
 */
function nexus_count_review_requests_by_status( $status ) {
	$query = new WP_Query(
		[
			'post_type'      => 'nexus_review_request',
			'post_status'    => 'private',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => [
				[
					'key'   => '_nexus_review_status',
					'value' => $status,
				],
			],
		]
	);

	return (int) $query->found_posts;
}

/**
 * Count requests that have not been processed within the target window.
 *
 * @return int
 */
function nexus_count_overdue_review_requests() {
	$query = new WP_Query(
		[
			'post_type'      => 'nexus_review_request',
			'post_status'    => 'private',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'     => '_nexus_review_due_at',
					'value'   => current_time( 'timestamp' ),
					'type'    => 'NUMERIC',
					'compare' => '<',
				],
				[
					'key'     => '_nexus_review_status',
					'value'   => [ 'new', 'in_review' ],
					'compare' => 'IN',
				],
			],
		]
	);

	return (int) $query->found_posts;
}
