<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Output organization and service schemas dynamically.
 * This file centralizes all structured data logic. Include it once in your child theme.
 */
function hu_output_schema()
{
    // Organization / LocalBusiness schema
    $org = [
        '@context' => 'https://schema.org',
        '@type'    => ['Organization', 'LocalBusiness'],
        '@id'      => home_url('/#organization'),
        'name'     => 'Haşim Üner – Architekt für eigene Anfrage-Systeme',
        'alternateName' => 'Haşim Üner',
        'url'      => home_url(),
        'description' => 'Architekt für eigene Anfrage-Systeme: Solar- und Wärmepumpen-Anbieter im DACH-Raum lösen Portal-Abhängigkeit ab und senken Leadkosten messbar — durch Website, Tracking, Vorqualifizierung und Kanal-Steuerung als ein verbundenes System.',
        'telephone'   => '+49 176 81407134',
        'email'       => 'info@hasimuener.de',
        'logo'        => function_exists( 'hu_get_brand_logo_url' ) ? hu_get_brand_logo_url() : content_url( '/uploads/2025/08/cropped-Logo-hasim-uener-1.webp' ),
        'image'       => hu_get_profile_image_url(),
        'address'     => [
            '@type' => 'PostalAddress',
            'streetAddress'   => 'Warschauer Str. 5',
            'addressLocality' => 'Pattensen',
            'addressRegion'   => 'Niedersachsen',
            'postalCode'      => '30982',
            'addressCountry'  => 'DE'
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => '52.264',
            'longitude' => '9.761'
        ],
        'priceRange'  => '€€',
        'currenciesAccepted' => 'EUR',
        'paymentAccepted'    => 'Überweisung',
        'openingHoursSpecification' => [
            [
                '@type'    => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                'opens'     => '08:30',
                'closes'    => '16:00'
            ],
            [
                '@type'    => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Friday'],
                'opens'     => '08:30',
                'closes'    => '13:00'
            ]
        ],
        'sameAs' => [
            'https://www.linkedin.com/in/hasim-%C3%BCner/',
            'https://github.com/Hasim-hannover/'
        ],
        'knowsAbout' => [
            'WordPress',
            'Technische SEO',
            'Core Web Vitals',
            'Conversion Rate Optimization',
            'GA4 Tracking',
            'Server-Side Tagging',
            'B2B Lead Generation',
        ],
        'knowsLanguage' => ['de', 'en', 'tr'],
        'areaServed' => [
            [
                '@type'  => 'City',
                'name'   => 'Hannover',
                'sameAs' => 'https://de.wikipedia.org/wiki/Hannover'
            ],
            [
                '@type'  => 'City',
                'name'   => 'Pattensen',
                'sameAs' => 'https://de.wikipedia.org/wiki/Pattensen'
            ],
            [
                '@type' => 'AdministrativeArea',
                'name'  => 'Niedersachsen'
            ],
            [
                '@type' => 'AdministrativeArea',
                'name'  => 'DACH'
            ],
        ],
        'hasOfferCatalog' => [
            '@type'           => 'OfferCatalog',
            'name'            => 'Leistungen für Solar-, Wärmepumpen- und B2B-Anbieter',
            'itemListElement' => [
                [
                    '@type'       => 'Offer',
                    'name'        => 'Marktcheck',
                    'description' => 'Kostenfreier Einstieg: Anfragebremsen, Datenlage, Sichtbarkeit und Conversion-Reihenfolge einordnen.',
                    'url'         => function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ),
                ],
                [
                    '@type'       => 'Offer',
                    'name'        => 'Eigenes Anfrage-System für Solar- und Wärmepumpen-Anbieter',
                    'description' => 'Aufbau eigener Anfrage-Systeme zur Ablösung von Portal-Leads: Website, Tracking, Vorqualifizierung und Kanal-Steuerung als verbundenes System.',
                    'url'         => home_url('/solar-waermepumpen-leadgenerierung/'),
                ],
                [
                    '@type'       => 'Offer',
                    'name'        => 'WordPress Agentur Hannover',
                    'description' => 'WordPress-Entwicklung für B2B in Hannover: technisches SEO, Wartungsvertrag, Tracking und Conversion als verbundenes System.',
                    'url'         => home_url('/wordpress-agentur-hannover/'),
                ],
                [
                    '@type'       => 'Offer',
                    'name'        => 'Speed & Core Web Vitals',
                    'description' => 'Performance-Arbeit mit Fokus auf LCP, INP, CLS, Server-Antwortzeiten und tragfähige WordPress-Basis.',
                    'url'         => home_url('/wgos-assets/cwv-optimierung/'),
                ],
                [
                    '@type'       => 'Offer',
                    'name'        => 'Conversion-Optimierung',
                    'description' => 'Systematische Optimierung von Angebotsseiten und Nutzerpfaden für mehr qualifizierte Anfragen.',
                    'url'         => home_url('/wordpress-agentur-hannover/#wgos'),
                ],
            ],
        ],
    ];

    $schemas = [$org];

    // Service definitions (slug => data)
    $service_definitions = [
        'wordpress-agentur-hannover' => [
            'name'        => 'WordPress Agentur Hannover',
            'description' => 'WordPress Agentur in Hannover für B2B-Unternehmen: technisches SEO, Wartungsvertrag, Tracking, Conversion und Angebotsseiten als ein verbundenes System mit kontrollierter Weiterentwicklung.',
            'serviceType' => 'WordPress Agentur',
            'serviceOutput' => 'Steuerbares WordPress-System mit Angebotsseiten, technischem SEO, Wartung, Datenebene, KPI-Klarheit und vollen Zugängen',
            'hasOfferCatalog' => [
                '@type'           => 'OfferCatalog',
                'name'            => 'SEO-Subcluster der WordPress Agentur Hannover',
                'itemListElement' => [
                    [
                        '@type'       => 'Offer',
                        'name'        => 'Technisches SEO Fundament',
                        'description' => 'Crawlability, LCP, interne Verlinkung, Indexierungslogik und technische WordPress-Basis für kaufnahe Seiten.',
                        'url'         => home_url('/wordpress-agentur-hannover/#technisches-seo-foundation'),
                    ],
                    [
                        '@type'       => 'Offer',
                        'name'        => 'Local SEO Hannover',
                        'description' => 'Google Business Profile, Maps-Pack-Signale, lokale Citations, NAP-Konsistenz und regionale Vertrauenssignale.',
                        'url'         => home_url('/wordpress-agentur-hannover/#technisches-seo-local'),
                    ],
                    [
                        '@type'       => 'Offer',
                        'name'        => 'Schema & Rich Results',
                        'description' => 'JSON-LD für Organization, LocalBusiness, Service, FAQPage und BreadcrumbList mit Validierung über den Rich Results Test.',
                        'url'         => home_url('/wordpress-agentur-hannover/#technisches-seo-schema'),
                    ],
                ],
            ],
        ],

        'customer-journey-audit' => [
            'name'        => 'Marktcheck',
            'description' => 'Persönlicher Marktcheck für Solar- und Wärmepumpen-Anbieter: erste Einordnung der Anfragebremsen, eine klare Priorität und der nächste sinnvolle Schritt.',
            'serviceType' => 'Marktcheck',
            'serviceOutput' => 'Persönliche Einschätzung der größten Anfragebremsen auf einer Startseite oder kaufnahen Angebotsseite',
            'offers'      => [
                [
                    '@type'         => 'Offer',
                    'name'          => 'Marktcheck',
                    'price'         => 0,
                    'priceCurrency' => 'EUR',
                    'isAccessibleForFree' => true,
                    'description'   => 'Kostenloser Ersteinstieg für Solar- und Wärmepumpen-Anbieter mit unklarer Lead-Performance'
                ]
            ]
        ],

        'growth-audit' => [
            'name'        => 'Marktcheck',
            'description' => 'Persönlicher Marktcheck für Solar- und Wärmepumpen-Anbieter: erste Einordnung der Anfragebremsen, eine klare Priorität und der nächste sinnvolle Schritt.',
            'serviceType' => 'Marktcheck',
            'serviceOutput' => 'Persönliche Einschätzung der größten Anfragebremsen auf einer Startseite oder kaufnahen Angebotsseite',
            'offers'      => [
                [
                    '@type'         => 'Offer',
                    'name'          => 'Marktcheck',
                    'price'         => 0,
                    'priceCurrency' => 'EUR',
                    'isAccessibleForFree' => true,
                    'description'   => 'Kostenloser Ersteinstieg für Solar- und Wärmepumpen-Anbieter mit unklarer Lead-Performance'
                ]
            ]
        ],

        'audit' => [
            'name'        => 'Marktcheck',
            'description' => 'Persönlicher Marktcheck für Solar- und Wärmepumpen-Anbieter: erste Einordnung der Anfragebremsen, eine klare Priorität und der nächste sinnvolle Schritt.',
            'serviceType' => 'Marktcheck',
            'serviceOutput' => 'Persönliche Einschätzung der größten Anfragebremsen auf einer Startseite oder kaufnahen Angebotsseite'
        ],

        '360-deep-dive' => [
            'name'        => 'Vertiefte Folgeanalyse nach dem Marktcheck',
            'description' => 'Persönliche Folgeanalyse nach dem Audit mit priorisierter Entscheidungsvorlage für Positionierung, IA, Measurement und Conversion.',
            'serviceType' => 'Folgeanalyse nach dem Marktcheck',
            'serviceOutput' => 'Priorisierte Entscheidungsvorlage für die nächsten sinnvollen Struktur- und Umsetzungsentscheidungen'
        ],

        // Legacy-Services wordpress-wartung-hannover, wordpress-seo, wordpress-seo-hannover entfernt:
        // Seiten sind 301 auf /wordpress-agentur-hannover/#wordpress-wartung bzw. #technisches-seo konsolidiert,
        // daher keine eigenständigen Service-Schemas mehr — gehören jetzt in die Agentur-Service-Beschreibung.

        'core-web-vitals-optimierung' => [
            'name'        => 'Speed & Core Web Vitals Optimierung',
            'description' => 'Performance-Optimierung mit Fokus auf Ladezeit, LCP, INP, CLS und tragfähige WordPress-Basis.',
            'serviceType' => 'Performance Optimierung',
            'serviceOutput' => 'Stabilere Core Web Vitals, bessere Ladezeiten und priorisierte technische Korrekturen'
        ],

        'core-web-vitals' => [
            'name'        => 'Speed & Core Web Vitals Optimierung',
            'description' => 'Performance-Optimierung mit Fokus auf Ladezeit, LCP, INP, CLS und tragfähige WordPress-Basis.',
            'serviceType' => 'Performance Optimierung',
            'serviceOutput' => 'Stabilere Core Web Vitals, bessere Ladezeiten und priorisierte technische Korrekturen'
        ],

        'tracking-data' => [
            'name'        => 'GA4 & Server-Side Tracking',
            'description' => 'Implementation von GA4, Server-Side GTM und DSGVO-konformem Tracking.',
            'serviceType' => 'Tracking & Analytics',
            'serviceOutput' => 'Saubere Daten & Conversion-Insights'
        ],

        'conversion-optimierung' => [
            'name'        => 'Conversion Optimierung',
            'description' => 'Optimierung von Angebotsseiten, Proof, CTA-Führung und Formularen für qualifiziertere Anfragen.',
            'serviceType' => 'Conversion Rate Optimization',
            'serviceOutput' => 'Klarere Nutzerführung und belastbarere Anfragepfade'
        ],

        'wordpress-tech-audit' => [
            'name'        => 'WordPress Tech-Audit',
            'description' => 'KI-gestütztes Tech-Audit, das in 5 Minuten die nackten Zahlen zu Technik, PageSpeed und Tracking-Lücken liefert.',
            'serviceType' => 'Technisches Audit & Analyse',
            'serviceOutput' => 'Objektives Protokoll mit Core Web Vitals, Data-Integrity und Conversion-Insights',
            'offers'      => [
                [
                    '@type'         => 'Offer',
                    'name'          => 'Kostenloses Tech-Audit',
                    'price'         => 0,
                    'priceCurrency' => 'EUR',
                    'isAccessibleForFree' => true,
                    'description'   => 'Kostenlose 5-Minuten-Analyse, die Technik, Speed und Tracking-Lücken aufdeckt'
                ]
            ]
        ],

        'conversion-rate-optimization' => [
            'name'        => 'Conversion Rate Optimierung für WordPress',
            'description' => 'WGOS-Cluster für Conversion Rate Optimierung auf WordPress: Angebotsseiten, Proof, CTA-Führung und Formulare für mehr qualifizierte B2B-Anfragen.',
            'serviceType' => 'Conversion Rate Optimierung für WordPress',
            'serviceOutput' => 'Klarere Angebotsseiten, bessere Anfragequalität und belastbarere Leadpfade auf B2B-WordPress-Websites'
        ],

        'ga4-tracking-setup' => [
            'name'        => 'GA4 Tracking Setup für B2B-WordPress-Websites',
            'description' => 'WGOS-Cluster für GA4 Tracking Setup: Event-Logik, GTM, Consent Mode und Server Side Tracking für belastbare Leadsignale in WordPress.',
            'serviceType' => 'GA4 Tracking Setup & Server Side Tracking',
            'serviceOutput' => 'Belastbare Lead- und Nachfrage-Signale mit GA4, GTM, Consent und serverseitiger Messung für B2B-Websites'
        ],

        'performance-marketing' => [
            'name'        => 'Performance Marketing für B2B-WordPress-Websites',
            'description' => 'WGOS-Aktivierungslayer für bezahlte Nachfrage: erst Tracking, Technik und Zielseite, dann skalierbare Kampagnen.',
            'serviceType' => 'Performance Marketing',
            'serviceOutput' => 'Kampagnenfähige Zielseiten und belastbare Tracking-Signale für effiziente Paid-Aktivierung'
        ],

        // Legacy-Services wordpress-growth-operating-system + wgos entfernt:
        // WGOS ist in der neuen Positionierung Hard-Ban, Seite ist noindex,
        // daher keine Service-Schema-Signale mehr zur WGOS-URL.
    ];

    /**
     * WICHTIGER FIX:
     * Viele deiner “Seiten” sind Posts (Insights). Daher NICHT nur is_page(),
     * sondern is_singular() (Pages + Posts + CPTs).
     */
    if (is_singular() && !is_attachment()) {

        $post_id = get_queried_object_id();
        $slug = $post_id ? get_post_field('post_name', $post_id) : '';

        if (!$slug && function_exists('nexus_get_current_wgos_cluster_route_slug')) {
            $slug = nexus_get_current_wgos_cluster_route_slug();
        }

        if ( function_exists( 'nexus_is_glossary_hub_page' ) && nexus_is_glossary_hub_page() && function_exists( 'nexus_get_glossary_registry' ) ) {
            $term_set = [
                '@context'    => 'https://schema.org',
                '@type'       => 'DefinedTermSet',
                '@id'         => trailingslashit( get_permalink( $post_id ) ) . '#definedtermset',
                'name'        => get_the_title( $post_id ),
                'description' => get_the_excerpt( $post_id ) ? wp_strip_all_tags( get_the_excerpt( $post_id ) ) : 'Glossar für SEO, Tracking, Performance und Conversion mit sauberer Rückführung auf die passenden Primary URLs.',
                'url'         => get_permalink( $post_id ),
                'inLanguage'  => 'de',
            ];

            $defined_terms = [];

            foreach ( nexus_get_glossary_registry() as $term ) {
                if ( 'publish' !== ( $term['status'] ?? '' ) || 'alias' === ( $term['index_policy'] ?? '' ) ) {
                    continue;
                }

                if ( ! function_exists( 'nexus_get_glossary_term_detail_url' ) ) {
                    continue;
                }

                $term_url = nexus_get_glossary_term_detail_url( $term );

                if ( '' === $term_url ) {
                    continue;
                }

                $defined_terms[] = [
                    '@type'       => 'DefinedTerm',
                    '@id'         => trailingslashit( $term_url ) . '#definedterm',
                    'name'        => (string) $term['title'],
                    'description' => (string) ( $term['short_definition'] ?? $term['excerpt'] ?? '' ),
                    'url'         => $term_url,
                    'termCode'    => (string) $term['slug'],
                ];
            }

            if ( ! empty( $defined_terms ) ) {
                $term_set['hasDefinedTerm'] = $defined_terms;
            }

            $schemas[] = $term_set;
        }

        if ( is_singular( 'glossary_term' ) && function_exists( 'nexus_get_glossary_definition' ) ) {
            $term = nexus_get_glossary_definition( get_post( $post_id ) );

            if ( is_array( $term ) ) {
                $term_schema = [
                    '@context'         => 'https://schema.org',
                    '@type'            => 'DefinedTerm',
                    '@id'              => trailingslashit( get_permalink( $post_id ) ) . '#definedterm',
                    'name'             => (string) $term['title'],
                    'description'      => (string) ( $term['short_definition'] ?? $term['excerpt'] ?? '' ),
                    'url'              => get_permalink( $post_id ),
                    'termCode'         => (string) $term['slug'],
                    'inDefinedTermSet' => [
                        '@id' => trailingslashit( function_exists( 'nexus_get_glossary_hub_url' ) ? nexus_get_glossary_hub_url() : home_url( '/glossar/' ) ) . '#definedtermset',
                    ],
                ];

                $schemas[] = $term_schema;
            }
        }

        if (is_singular('wgos_asset') && function_exists('nexus_get_wgos_asset_definition')) {
            $asset = nexus_get_wgos_asset_definition(get_post($post_id));
            $schema_type = $asset['schema_type'] ?? 'Service';

            if ($asset && $schema_type !== 'none') {
                $description = '';

                if (!empty($asset['result'])) {
                    $description = (string) $asset['result'];
                } elseif (!empty($asset['excerpt'])) {
                    $description = (string) $asset['excerpt'];
                }

                $service = [
                    '@context'      => 'https://schema.org',
                    '@type'         => $schema_type,
                    '@id'           => trailingslashit(get_permalink($post_id)) . '#service',
                    'name'          => (string) $asset['title'],
                    'description'   => $description,
                    'url'           => get_permalink($post_id),
                    'provider'      => ['@id' => home_url('/#organization')],
                    'serviceType'   => 'WGOS Asset',
                    'serviceOutput' => (string) ($asset['result'] ?? ''),
                    'areaServed'    => ['@type' => 'AdministrativeArea', 'name' => 'DACH'],
                    // isPartOf-Referenz auf WGOS-Hub entfernt (noindex);
                    // Asset wird via provider an Organization gebunden.
                ];

                $schemas[] = $service;
            }
        }

        // Service schema, falls slug matcht
        if ($slug && array_key_exists($slug, $service_definitions)) {
            $def = $service_definitions[$slug];

            $service = [
                '@context'   => 'https://schema.org',
                '@type'      => 'Service',
                '@id'        => home_url('/' . $slug . '/#service'),
                'name'       => $def['name'],
                'description'=> $def['description'],
                'provider'   => ['@id' => home_url('/#organization')],
                'serviceType'=> $def['serviceType'],
                'serviceOutput' => $def['serviceOutput'],
                'areaServed' => ['@type' => 'AdministrativeArea', 'name' => 'DACH'],
            ];

            if (isset($def['offers'])) {
                $service['offers'] = $def['offers'];
            }

            if (isset($def['hasOfferCatalog'])) {
                $service['hasOfferCatalog'] = $def['hasOfferCatalog'];
            }

            $schemas[] = $service;

            if ('wordpress-agentur-hannover' === $slug) {
                $schemas[] = [
                    '@context'    => 'https://schema.org',
                    '@type'       => 'WebPage',
                    '@id'         => home_url('/wordpress-agentur-hannover/#webpage'),
                    'url'         => home_url('/wordpress-agentur-hannover/'),
                    'name'        => 'WordPress Agentur Hannover',
                    'description' => $def['description'],
                    'inLanguage'  => 'de',
                    'isPartOf'    => ['@id' => home_url('/#website')],
                    'about'       => ['@id' => home_url('/#organization')],
                    'mainEntity'  => ['@id' => home_url('/wordpress-agentur-hannover/#service')],
                ];
            }
        }

        if ('wordpress-agentur-hannover' === $slug && function_exists('nexus_get_agentur_faq_items')) {
            $agentur_faq_items = array_slice(nexus_get_agentur_faq_items(), 0, 8);
            $faq_entities = array_map(
                static function ($item) {
                    return [
                        '@type'          => 'Question',
                        'name'           => (string) $item['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text'  => (string) $item['answer'],
                        ],
                    ];
                },
                $agentur_faq_items
            );

            if (!empty($faq_entities)) {
                $schemas[] = [
                    '@context'   => 'https://schema.org',
                    '@type'      => 'FAQPage',
                    '@id'        => home_url('/wordpress-agentur-hannover/#faq'),
                    'url'        => home_url('/wordpress-agentur-hannover/'),
                    'inLanguage' => 'de',
                    'publisher'  => ['@id' => home_url('/#organization')],
                    'mainEntity' => $faq_entities,
                ];
            }
        }

        if ( $slug && function_exists( 'nexus_get_wgos_cluster_page_faq_entities' ) ) {
            $cluster_faq_entities = nexus_get_wgos_cluster_page_faq_entities( $slug );

            if ( ! empty( $cluster_faq_entities ) ) {
                $faq_schema = [
                    '@context'   => 'https://schema.org',
                    '@type'      => 'FAQPage',
                    '@id'        => home_url( '/' . $slug . '/#faq' ),
                    'url'        => home_url( '/' . $slug . '/' ),
                    'inLanguage' => 'de',
                    'publisher'  => [ '@id' => home_url( '/#organization' ) ],
                    'mainEntity' => $cluster_faq_entities,
                ];

                $schemas[] = $faq_schema;
            }
        }

        // Über mich: Person + ProfilePage
        if ($slug === 'uber-mich') {
            $person = [
                '@context' => 'https://schema.org',
                '@type'    => 'Person',
                '@id'      => home_url('/uber-mich/#person'),
                'name'     => 'Haşim Üner',
                'jobTitle' => 'Architekt für eigene Anfrage-Systeme',
                'url'      => home_url('/uber-mich/'),
                'image'    => hu_get_profile_image_url(),
                'worksFor' => ['@id' => home_url('/#organization')],
                'sameAs'   => [
                    'https://www.linkedin.com/in/hasim-%C3%BCner/',
                    'https://github.com/Hasim-hannover/'
                ],
                'description' => 'Architekt für eigene Anfrage-Systeme mit Fokus auf Solar- und Wärmepumpen-Anbieter im DACH-Raum: Website, Tracking, Vorqualifizierung und Werbekanal-Steuerung als ein verbundenes System zur Ablösung von Portal-Abhängigkeit.'
            ];

            $profilePage = [
                '@context' => 'https://schema.org',
                '@type'    => 'ProfilePage',
                '@id'      => home_url('/uber-mich/#profile'),
                'url'      => home_url('/uber-mich/'),
                'name'     => 'Über mich – Haşim Üner',
                'mainEntity' => ['@id' => home_url('/uber-mich/#person')],
                'inLanguage' => 'de',
                'about'    => ['@id' => home_url('/uber-mich/#person')]
            ];

            $schemas[] = $person;
            $schemas[] = $profilePage;
            $schemas[0]['founder'] = ['@id' => home_url('/uber-mich/#person')];
        }

        if ( is_singular( 'post' ) && $post_id ) {
            $author_id         = (int) get_post_field( 'post_author', $post_id );
            $author_name       = $author_id ? get_the_author_meta( 'display_name', $author_id ) : 'Haşim Üner';
            $author_name       = hu_normalize_brand_text( $author_name );
            $author_profile    = home_url( '/uber-mich/' );
            $post_permalink    = get_permalink( $post_id );
            $post_description  = get_the_excerpt( $post_id );
            $post_description  = $post_description ? wp_strip_all_tags( $post_description ) : wp_strip_all_tags( get_the_title( $post_id ) );
            $post_image        = get_the_post_thumbnail_url( $post_id, 'full' );
            $published_date    = get_post_time( DATE_W3C, true, $post_id );
            $modified_date     = get_post_modified_time( DATE_W3C, true, $post_id );

            $blog_posting = [
                '@context'         => 'https://schema.org',
                '@type'            => 'BlogPosting',
                '@id'              => trailingslashit( $post_permalink ) . '#blogposting',
                'mainEntityOfPage' => $post_permalink,
                'headline'         => get_the_title( $post_id ),
                'description'      => $post_description,
                'datePublished'    => $published_date,
                'dateModified'     => $modified_date,
                'inLanguage'       => 'de',
                'author'           => [
                    '@type'  => 'Person',
                    'name'   => $author_name,
                    'url'    => $author_profile,
                    'sameAs' => [
                        'https://www.linkedin.com/in/hasim-%C3%BCner/',
                    ],
                ],
                'publisher'        => ['@id' => home_url('/#organization')],
                'isPartOf'         => [
                    '@type' => 'Blog',
                    '@id'   => home_url('/blog/#blog'),
                    'url'   => home_url('/blog/'),
                    'name'  => 'Insights',
                ],
            ];

            if ( $post_image ) {
                $blog_posting['image'] = [
                    '@type' => 'ImageObject',
                    'url'   => $post_image,
                ];
            }

            $schemas[] = $blog_posting;
        }

        if ( function_exists( 'hu_is_e3_methodology_case_post' ) && hu_is_e3_methodology_case_post( $post_id ) ) {
            $e3_article = [
                '@context'         => 'https://schema.org',
                '@type'            => 'Article',
                '@id'              => home_url('/e3-new-energy/#article'),
                'url'              => home_url('/e3-new-energy/'),
                'mainEntityOfPage' => home_url('/e3-new-energy/'),
                'headline'         => function_exists( 'hu_get_e3_methodology_case_title' ) ? hu_get_e3_methodology_case_title() : 'Methodik-Case E3 New Energy',
                'description'      => function_exists( 'hu_get_e3_methodology_case_description' ) ? hu_get_e3_methodology_case_description() : '',
                'inLanguage'       => 'de',
                'author'           => [
                    '@type' => 'Person',
                    'name'  => 'Haşim Üner',
                    'url'   => home_url('/uber-mich/'),
                ],
                'publisher'        => ['@id' => home_url('/#organization')],
                'about'            => [
                    [
                        '@type' => 'Thing',
                        'name'  => 'Anfrage-Systeme für Solar- und Wärmepumpen-Anbieter',
                    ],
                    [
                        '@type' => 'Organization',
                        'name'  => 'E3 New Energy',
                    ],
                ],
            ];

            $modified_date = get_post_modified_time( DATE_W3C, true, $post_id );

            if ( $modified_date ) {
                $e3_article['dateModified'] = $modified_date;
            }

            $schemas[] = $e3_article;
        }

        // Ergebnisse hub: CollectionPage schema
        if ($slug === 'case-studies' || $slug === 'case-studies-e-commerce' || $slug === 'ergebnisse') {
            $collection = [
                '@context' => 'https://schema.org',
                '@type'    => 'CollectionPage',
                '@id'      => home_url('/' . $slug . '/#collection'),
                'url'      => home_url('/' . $slug . '/'),
                'name'     => 'Ergebnisse',
                'headline' => 'Ergebnisse, Case Studies und Whitelabel-Proof von Haşim Üner',
                'description' => 'Öffentliche Case Studies, anonymisierte Whitelabel-Arbeit und laufende Weiterentwicklung als gemeinsamer Proof-Layer für WordPress-Systemarbeit.',
                'inLanguage' => 'de',
                'isPartOf' => ['@id' => home_url('/#website')],
                'about' => ['@id' => home_url('/#organization')],
                'hasPart' => array_values(array_filter([
                    [
                        '@type' => 'Article',
                        'name'  => 'E3 New Energy',
                        'url'   => home_url('/e3-new-energy/')
                    ],
                    nexus_get_page_id([ 'case-study-domdar', 'domdar' ]) ? [
                        '@type' => 'Article',
                        'name'  => 'DOMDAR',
                        'url'   => nexus_get_page_url([ 'case-study-domdar', 'domdar' ], home_url('/case-study-domdar/'))
                    ] : null,
                    nexus_get_whitelabel_page_id() ? [
                        '@type' => 'WebPage',
                        'name'  => 'Whitelabel & Weiterentwicklung',
                        'url'   => nexus_get_whitelabel_page_url()
                    ] : null,
                ])),
            ];

            $schemas[] = $collection;
        }

        if ($slug === 'whitelabel-retainer' || $slug === 'whitelabel-retainer-proof' || $slug === 'whitelabel') {
            $whitelabelPage = [
                '@context' => 'https://schema.org',
                '@type'    => 'AboutPage',
                '@id'      => home_url('/' . $slug . '/#about'),
                'url'      => home_url('/' . $slug . '/'),
                'name'     => 'Whitelabel & Weiterentwicklung',
                'headline' => 'Whitelabel-Arbeit und laufende Weiterentwicklung von Haşim Üner',
                'description' => 'Anonymisierte Einblicke in Whitelabel-Projekte, laufende Weiterentwicklung und typische Eingriffstiefen für WordPress, SEO, Tracking und CRO.',
                'inLanguage' => 'de',
                'about'    => ['@id' => home_url('/#organization')],
                'mainEntity' => [
                    '@type' => 'Person',
                    'name'  => 'Haşim Üner',
                    'url'   => home_url('/uber-mich/')
                ],
                'image' => hu_get_portrait_image_url(),
            ];

            $schemas[] = $whitelabelPage;
        }

        /**
         * FAQ SCHEMA (NEU, robust):
         * - erkennt <details><summary>…</summary>…</details>
         * - erkennt button.faq-trigger + div.faq-content (deine häufigste Struktur)
         * - dedupliziert Fragen
         */
        global $post;
        if (isset($post)) {

            $raw = (string) $post->post_content;

            // Performance: nur wenn es nach FAQ aussieht
            $maybe_has_faq =
                (stripos($raw, 'faq-trigger') !== false) ||
                (stripos($raw, 'faq-content') !== false) ||
                (stripos($raw, '<details') !== false) ||
                (stripos($raw, 'hu_faq') !== false) ||
                (stripos($raw, 'faq-item') !== false);

            // Einige Templates rendern ihr FAQPage JSON-LD bewusst direkt im Template.
            if (
                in_array($slug, ['wordpress-agentur-hannover', 'wgos', 'wordpress-growth-operating-system'], true) ||
                ( function_exists( 'nexus_is_wgos_cluster_page' ) && nexus_is_wgos_cluster_page( $slug ) )
            ) {
                $maybe_has_faq = false;
            }

            if ($maybe_has_faq) {

                // do_shortcode statt apply_filters('the_content') um Nebeneffekte
                // durch andere Plugins oder Filter zu vermeiden.
                $content = do_shortcode($raw);

                $faq_entities = [];
                $dedupe = [];

                // Helper: add QA safely
                $add_qa = function ($q, $a) use (&$faq_entities, &$dedupe) {
                    $q = trim((string)$q);
                    $a = trim((string)$a);

                    // Entferne häufiges " + " am Ende (kommt durch <span>+</span> im Button)
                    $q = preg_replace('/\s*\+\s*$/u', '', $q);

                    if ($q === '' || $a === '') {
                        return;
                    }

                    $key = mb_strtolower(preg_replace('/\s+/u', ' ', $q));
                    if (isset($dedupe[$key])) {
                        return;
                    }
                    $dedupe[$key] = true;

                    $faq_entities[] = [
                        '@type' => 'Question',
                        'name'  => $q,
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text'  => $a,
                        ],
                    ];
                };

                // 1) DETAILS/SUMMARY Muster (flexibler)
                if (preg_match_all('/<details[^>]*>.*?<summary[^>]*>(.*?)<\/summary>(.*?)<\/details>/is', $content, $m1, PREG_SET_ORDER)) {
                    foreach ($m1 as $match) {
                        $q = wp_strip_all_tags($match[1]);

                        // Answer: alles innerhalb details nach summary, HTML raus
                        $a = wp_strip_all_tags($match[2]);

                        $add_qa($q, $a);
                    }
                }

                // 2) BUTTON + FAQ-CONTENT Muster (dein Wartung-Layout)
                if (preg_match_all('/<button[^>]*class="[^"]*\bfaq-trigger\b[^"]*"[^>]*>(.*?)<\/button>\s*<div[^>]*class="[^"]*\bfaq-content\b[^"]*"[^>]*>(.*?)<\/div>/is', $content, $m2, PREG_SET_ORDER)) {
                    foreach ($m2 as $match) {
                        $q = wp_strip_all_tags($match[1]);
                        $a = wp_strip_all_tags($match[2]);

                        $add_qa($q, $a);
                    }
                }

                // Output FAQPage schema if found
                if (!empty($faq_entities)) {
                    // Auf der Startseite kein Seiten-Slug in der ID verwenden
                    $faq_base = is_front_page() ? home_url('/') : home_url('/' . ($slug ?: '') . '/');
                    $faq_schema = [
                        '@context'    => 'https://schema.org',
                        '@type'       => 'FAQPage',
                        '@id'         => $faq_base . '#faq',
                        'url'         => $faq_base,
                        'inLanguage'  => 'de',
                        'publisher'   => ['@id' => home_url('/#organization')],
                        'mainEntity'  => $faq_entities,
                    ];
                    $schemas[] = $faq_schema;
                }
            }
        }
    }

    // ── BreadcrumbList Schema ─────────────────────────────────────
    // Output on all pages except the homepage.
    if ( ! is_front_page() ) {
        $breadcrumb_items = [];
        $bc_position      = 1;

        // Always start with Home
        $breadcrumb_items[] = [
            '@type'    => 'ListItem',
            'position' => $bc_position++,
            'name'     => 'Start',
            'item'     => home_url( '/' ),
        ];

        if ( is_singular( 'post' ) ) {
            // Blog > Category > Post
            $blog_page_id = get_option( 'page_for_posts' );
            $breadcrumb_items[] = [
                '@type'    => 'ListItem',
                'position' => $bc_position++,
                'name'     => $blog_page_id ? get_the_title( $blog_page_id ) : 'Blog',
                'item'     => $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' ),
            ];

            $categories = get_the_category();
            if ( $categories ) {
                $primary = $categories[0];
                $breadcrumb_items[] = [
                    '@type'    => 'ListItem',
                    'position' => $bc_position++,
                    'name'     => $primary->name,
                    'item'     => get_category_link( $primary->term_id ),
                ];
            }

            $breadcrumb_items[] = [
                '@type'    => 'ListItem',
                'position' => $bc_position++,
                'name'     => get_the_title(),
            ];

        } elseif ( is_singular( 'glossary_term' ) ) {
            // Glossar > Term
            $glossar_hub_url = function_exists( 'nexus_get_glossary_hub_url' ) ? nexus_get_glossary_hub_url() : home_url( '/glossar/' );
            $breadcrumb_items[] = [
                '@type'    => 'ListItem',
                'position' => $bc_position++,
                'name'     => 'Glossar',
                'item'     => $glossar_hub_url,
            ];
            $breadcrumb_items[] = [
                '@type'    => 'ListItem',
                'position' => $bc_position++,
                'name'     => get_the_title(),
            ];

        } elseif ( is_singular( 'wgos_asset' ) ) {
            // Agentur > Asset — WGOS-Hub-Crumb entfernt (noindex).
            $agentur_url = function_exists( 'nexus_get_primary_public_url' )
                ? nexus_get_primary_public_url( 'agentur', home_url( '/wordpress-agentur-hannover/' ) )
                : home_url( '/wordpress-agentur-hannover/' );
            $breadcrumb_items[] = [
                '@type'    => 'ListItem',
                'position' => $bc_position++,
                'name'     => 'WordPress Agentur Hannover',
                'item'     => $agentur_url,
            ];
            $breadcrumb_items[] = [
                '@type'    => 'ListItem',
                'position' => $bc_position++,
                'name'     => get_the_title(),
            ];

        } elseif ( is_page() ) {
            // Pages with parent hierarchy
            $page_id   = get_queried_object_id();
            $ancestors = array_reverse( get_post_ancestors( $page_id ) );
            foreach ( $ancestors as $ancestor_id ) {
                $breadcrumb_items[] = [
                    '@type'    => 'ListItem',
                    'position' => $bc_position++,
                    'name'     => get_the_title( $ancestor_id ),
                    'item'     => get_permalink( $ancestor_id ),
                ];
            }
            $breadcrumb_items[] = [
                '@type'    => 'ListItem',
                'position' => $bc_position++,
                'name'     => get_the_title(),
            ];

        } elseif ( is_category() ) {
            $breadcrumb_items[] = [
                '@type'    => 'ListItem',
                'position' => $bc_position++,
                'name'     => single_cat_title( '', false ),
            ];

        } elseif ( is_home() ) {
            $breadcrumb_items[] = [
                '@type'    => 'ListItem',
                'position' => $bc_position++,
                'name'     => 'Blog',
            ];

        } elseif ( is_tag() || is_tax() ) {
            $breadcrumb_items[] = [
                '@type'    => 'ListItem',
                'position' => $bc_position++,
                'name'     => single_term_title( '', false ),
            ];
        }

        if ( count( $breadcrumb_items ) > 1 ) {
            $schemas[] = [
                '@context'        => 'https://schema.org',
                '@type'           => 'BreadcrumbList',
                'itemListElement' => $breadcrumb_items,
            ];
        }
    }

    // Output each schema as JSON-LD
    foreach ($schemas as $schema) {
        $json = wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        if ($json) {
            echo '<script type="application/ld+json">' . $json . '</script>';
        }
    }
}

add_action('wp_head', 'hu_output_schema', 10);
