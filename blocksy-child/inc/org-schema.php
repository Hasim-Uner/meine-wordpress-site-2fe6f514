<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Output organization and service schemas dynamically.
 * This file centralizes all structured data logic. Include it once in your child theme.
 */
function hu_person_schema_id() {
    return home_url('/uber-mich/#person');
}

function hu_person_profile_url() {
    return home_url('/uber-mich/');
}

function hu_person_same_as_urls() {
    return [
        'https://www.linkedin.com/in/hasim-%C3%BCner/',
        'https://github.com/Hasim-Uner/',
        'https://hasimuener.org/',
        'https://www.facebook.com/hasim.uner',
    ];
}

/**
 * Canonical Google Business Profile (Maps) place URL for the brand.
 *
 * @return string
 */
function hu_brand_map_url() {
    return 'https://www.google.de/maps/place/Ha%C5%9Fim+%C3%9Cner+%7C+Architekt+f%C3%BC+eigene+Anfrage-Systeme/@52.2736456,9.7534204,17z/data=!3m1!4b1!4m6!3m5!1s0x47baa159a829529f:0x64eef00b41898f29!8m2!3d52.2736456!4d9.7559953!16s%2Fg%2F11lv7g2w9d';
}

/**
 * sameAs profile set for the business entities (Organization, LocalBusiness,
 * ProfessionalService). Mirrors the person profiles and adds the Google
 * Business Profile place, so every business node exposes one consistent set
 * instead of drifting subsets.
 *
 * @return array<int, string>
 */
function hu_business_same_as_urls() {
    return array_merge( hu_person_same_as_urls(), [ hu_brand_map_url() ] );
}

/**
 * Return a pure @id reference to the canonical Person node.
 *
 * The full Person node is emitted once site-wide by hu_output_schema(), so all
 * author/founder/reviewedBy/mainEntity usages only need to reference it by @id
 * instead of repeating an inline (and potentially divergent) Person object.
 * Parameters are kept for backward compatibility with existing call sites; the
 * canonical node owns every property.
 *
 * @param bool   $include_same_as Unused. Kept for backward compatibility.
 * @param string $name            Unused. Kept for backward compatibility.
 * @return array<string, string>
 */
function hu_person_schema_ref( $include_same_as = false, $name = 'Haşim Üner' ) {
    return [ '@id' => hu_person_schema_id() ];
}

/**
 * Build the canonical Person node for the site.
 *
 * Emitted once per request (site-wide) so every @id reference resolves to a
 * single authoritative entity.
 *
 * @return array<string, mixed>
 */
function hu_get_person_node() {
    return [
        '@context'    => 'https://schema.org',
        '@type'       => 'Person',
        '@id'         => hu_person_schema_id(),
        'name'        => 'Haşim Üner',
        'jobTitle'    => 'Architekt für eigene Anfrage-Systeme',
        'url'         => hu_person_profile_url(),
        'image'       => hu_get_profile_image_url(),
        'worksFor'    => [ '@id' => home_url( '/#organization' ) ],
        'sameAs'      => hu_person_same_as_urls(),
        'description' => 'Architekt für eigene Anfrage-Systeme mit Fokus auf Solar- und Wärmepumpen-Anbieter im DACH-Raum. Haşim Üner verbindet Bauunternehmer-DNA, Vertriebspraxis und Medienwissenschaft mit WordPress, Tracking, Vorqualifizierung und Werbekanal-Steuerung.',
        'alumniOf'    => [
            '@type'  => 'CollegeOrUniversity',
            'name'   => 'Universität Paderborn',
            'sameAs' => 'https://de.wikipedia.org/wiki/Universit%C3%A4t_Paderborn',
        ],
        'knowsAbout'  => [
            'B2B-Vertrieb',
            'Solar- und Wärmepumpen-Leadgenerierung',
            'WordPress',
            'Technisches SEO',
            'Server-Side Tracking',
            'Conversion Rate Optimization',
            'Medienwissenschaft',
        ],
    ];
}

/**
 * Build CollectionPage + ItemList schema for blog archive surfaces.
 *
 * @return array<string, mixed>|null
 */
function hu_get_blog_archive_collection_schema() {
    if ( ! is_home() && ! is_category() ) {
        return null;
    }

    global $wp_query;

    $posts = isset( $wp_query->posts ) && is_array( $wp_query->posts ) ? $wp_query->posts : [];

    if ( is_home() ) {
        $blog_page_id = (int) get_option( 'page_for_posts' );
        $page_url     = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );
        $page_name    = function_exists( 'hu_get_blog_archive_title' ) ? hu_get_blog_archive_title() : 'Blog';
        $description  = function_exists( 'hu_get_blog_archive_description' ) ? hu_get_blog_archive_description() : get_bloginfo( 'description' );
    } else {
        $term = get_queried_object();

        if ( ! ( $term instanceof WP_Term ) ) {
            return null;
        }

        $term_link = get_term_link( $term );

        if ( is_wp_error( $term_link ) ) {
            return null;
        }

        $category_seo = function_exists( 'hu_get_category_archive_seo' ) ? hu_get_category_archive_seo( $term ) : [];
        $page_url     = $term_link;
        $page_name    = ! empty( $category_seo['title'] ) ? (string) $category_seo['title'] : single_term_title( '', false );
        $description  = ! empty( $category_seo['description'] ) ? (string) $category_seo['description'] : wp_strip_all_tags( (string) $term->description );
    }

    $list_items = [];
    $position   = 1;

    foreach ( $posts as $post ) {
        if ( ! ( $post instanceof WP_Post ) || 'post' !== $post->post_type ) {
            continue;
        }

        $post_url   = get_permalink( $post );
        $post_title = get_the_title( $post );

        if ( ! $post_url || ! $post_title ) {
            continue;
        }

        $post_excerpt = get_the_excerpt( $post );
        $post_summary = '' !== trim( (string) $post_excerpt )
            ? wp_trim_words( wp_strip_all_tags( $post_excerpt ), 24, '…' )
            : wp_strip_all_tags( $post_title );

        $post_item = [
            '@type'         => 'BlogPosting',
            '@id'           => trailingslashit( $post_url ) . '#blogposting',
            'url'           => $post_url,
            'headline'      => $post_title,
            'name'          => $post_title,
            'description'   => $post_summary,
            'datePublished' => get_post_time( DATE_W3C, true, $post ),
            'dateModified'  => get_post_modified_time( DATE_W3C, true, $post ),
            'author'        => hu_person_schema_ref( true ),
            'publisher'     => [ '@id' => home_url( '/#organization' ) ],
        ];

        $post_item_image = hu_get_post_schema_image_object( $post->ID );
        if ( is_array( $post_item_image ) ) {
            $post_item['image'] = $post_item_image;
        }

        $list_items[] = [
            '@type'    => 'ListItem',
            'position' => $position++,
            'url'      => $post_url,
            'item'     => $post_item,
        ];
    }

    $collection = [
        '@context'    => 'https://schema.org',
        '@type'       => 'CollectionPage',
        '@id'         => trailingslashit( $page_url ) . '#collection',
        'url'         => $page_url,
        'name'        => $page_name,
        'headline'    => $page_name,
        'description' => $description,
        'inLanguage'  => 'de',
        'isPartOf'    => [ '@id' => home_url( '/#website' ) ],
        'publisher'   => [ '@id' => home_url( '/#organization' ) ],
        'mainEntity'  => [
            '@type'           => 'ItemList',
            '@id'             => trailingslashit( $page_url ) . '#itemlist',
            'name'            => $page_name,
            'itemListOrder'   => is_home() ? 'https://schema.org/ItemListUnordered' : 'https://schema.org/ItemListOrderDescending',
            'numberOfItems'   => count( $list_items ),
            'itemListElement' => $list_items,
        ],
    ];

    if ( is_home() ) {
        $collection['author'] = hu_person_schema_ref( true );
        $collection['about']  = [
            [
                '@type' => 'Thing',
                'name'  => 'Portal-Leads und eigene Anfrage-Systeme',
            ],
            [
                '@type' => 'Thing',
                'name'  => 'Conversion-Optimierung',
            ],
            [
                '@type' => 'Thing',
                'name'  => 'Server-Side Tracking',
            ],
            [
                '@type' => 'Thing',
                'name'  => 'WordPress Performance',
            ],
        ];
    } elseif ( is_category() ) {
        $collection['about'] = [
            '@type' => 'Thing',
            'name'  => single_term_title( '', false ),
        ];
    }

    return $collection;
}

/**
 * Determine whether the global BreadcrumbList JSON-LD should be emitted.
 *
 * Some hardcoded templates output route-specific BreadcrumbList schema with a
 * more precise hierarchy. Suppressing the generic fallback avoids duplicate
 * breadcrumb graphs for the same URL.
 *
 * @return bool
 */
function hu_should_output_global_breadcrumb_schema() {
    if ( is_front_page() ) {
        return false;
    }

    $request_path = function_exists( 'nexus_get_current_request_path' ) ? nexus_get_current_request_path() : '';

    if ( function_exists( 'hu_is_solar_seo_subpage_path' ) && hu_is_solar_seo_subpage_path( $request_path ) ) {
        return false;
    }

    if ( is_singular() ) {
        $post_id  = get_queried_object_id();
        $template = $post_id ? (string) get_page_template_slug( $post_id ) : '';

        if ( 'page-seo-cornerstone.php' === $template ) {
            return false;
        }
    }

    return true;
}

/**
 * Return the post-meta key used for cached FAQ schema entities.
 *
 * @return string
 */
function hu_get_faq_schema_cache_meta_key() {
    return '_hu_faq_schema_entities_json';
}

/**
 * Extract FAQ schema entities from post content.
 *
 * This runs on save_post so frontend requests can read cached JSON instead of
 * parsing full post content with regex on every request.
 *
 * @param string $raw_content        Raw post content.
 * @param bool   $force_heading_faq  When true, also parse heading-based Q&A (H2–H4 ending with "?").
 * @return array<int, array<string, mixed>>
 */
function hu_extract_faq_schema_entities_from_content( $raw_content, $force_heading_faq = false ) {
    $raw_content = (string) $raw_content;

    if (
        ! $force_heading_faq
        && false === stripos( $raw_content, 'faq-trigger' )
        && false === stripos( $raw_content, 'faq-content' )
        && false === stripos( $raw_content, '<details' )
        && false === stripos( $raw_content, 'hu_faq' )
        && false === stripos( $raw_content, 'faq-item' )
    ) {
        return [];
    }

    $content      = do_shortcode( $raw_content );
    $faq_entities = [];
    $dedupe       = [];

    $add_qa = static function ( $q, $a ) use ( &$faq_entities, &$dedupe ) {
        $q = trim( (string) $q );
        $a = trim( (string) $a );
        $q = preg_replace( '/\s*\+\s*$/u', '', $q );

        if ( '' === $q || '' === $a ) {
            return;
        }

        $key = mb_strtolower( preg_replace( '/\s+/u', ' ', $q ) );
        if ( isset( $dedupe[ $key ] ) ) {
            return;
        }

        $dedupe[ $key ] = true;

        $faq_entities[] = [
            '@type'          => 'Question',
            'name'           => $q,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => $a,
            ],
        ];
    };

    if ( preg_match_all( '/<details[^>]*>.*?<summary[^>]*>(.*?)<\/summary>(.*?)<\/details>/is', $content, $details_matches, PREG_SET_ORDER ) ) {
        foreach ( $details_matches as $match ) {
            $add_qa( wp_strip_all_tags( $match[1] ), wp_strip_all_tags( $match[2] ) );
        }
    }

    if ( preg_match_all( '/<button[^>]*class="[^"]*\bfaq-trigger\b[^"]*"[^>]*>(.*?)<\/button>\s*<div[^>]*class="[^"]*\bfaq-content\b[^"]*"[^>]*>(.*?)<\/div>/is', $content, $button_matches, PREG_SET_ORDER ) ) {
        foreach ( $button_matches as $match ) {
            $add_qa( wp_strip_all_tags( $match[1] ), wp_strip_all_tags( $match[2] ) );
        }
    }

    // Opt-in: Überschrift-basierte FAQ (H2–H4, deren Text auf "?" endet → Frage, Folgeinhalt → Antwort).
    if ( $force_heading_faq && preg_match_all( '/<h([2-4])[^>]*>(.*?)<\/h\1>(.*?)(?=<h[2-4][^>]*>|$)/is', $content, $heading_matches, PREG_SET_ORDER ) ) {
        foreach ( $heading_matches as $match ) {
            $question = trim( wp_strip_all_tags( $match[2] ) );

            if ( '' === $question || ! preg_match( '/\?\s*$/u', $question ) ) {
                continue;
            }

            $add_qa( $question, wp_strip_all_tags( $match[3] ) );
        }
    }

    return $faq_entities;
}

/**
 * Cache FAQ schema entities whenever a post or page is saved.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return void
 */
function hu_update_post_faq_schema_cache( $post_id, $post ) {
    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
        return;
    }

    if ( ! ( $post instanceof WP_Post ) || ! in_array( $post->post_type, [ 'post', 'page' ], true ) ) {
        return;
    }

    $force_heading_faq = (bool) get_post_meta( $post_id, 'enable_faq_schema', true );
    $faq_entities      = hu_extract_faq_schema_entities_from_content( (string) $post->post_content, $force_heading_faq );
    $meta_key          = hu_get_faq_schema_cache_meta_key();

    if ( empty( $faq_entities ) ) {
        delete_post_meta( $post_id, $meta_key );
        return;
    }

	update_post_meta(
		$post_id,
		$meta_key,
		wp_slash( wp_json_encode( $faq_entities, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) )
	);
}
add_action( 'save_post', 'hu_update_post_faq_schema_cache', 20, 2 );

/**
 * Return cached FAQ schema entities for one post.
 *
 * @param int $post_id Post ID.
 * @return array<int, array<string, mixed>>
 */
function hu_get_cached_post_faq_schema_entities( $post_id ) {
    $json = get_post_meta( absint( $post_id ), hu_get_faq_schema_cache_meta_key(), true );

    if ( ! is_string( $json ) || '' === trim( $json ) ) {
        return [];
    }

    $decoded = json_decode( $json, true );

    return is_array( $decoded ) ? $decoded : [];
}

/**
 * Build a schema ImageObject for a post, with a brand fallback.
 *
 * Article-type schema (BlogPosting/Article) should always carry an image;
 * Google flags entries without one. Prefer the featured image, fall back to
 * the brand portrait so posts without a thumbnail still resolve. Dimensions
 * are resolved via the shared social-image helper when available.
 *
 * @param int $post_id Post ID.
 * @return array<string, mixed>|null
 */
function hu_get_post_schema_image_object( $post_id ) {
    $post_id   = absint( $post_id );
    $image_url = $post_id ? get_the_post_thumbnail_url( $post_id, 'full' ) : '';

    if ( ! $image_url && function_exists( 'hu_get_portrait_image_url' ) ) {
        $image_url = hu_get_portrait_image_url();
    }

    if ( ! $image_url ) {
        return null;
    }

    // Memoize per URL: archive ItemLists resolve many posts in one request and
    // the brand fallback URL repeats, so avoid redundant dimension lookups.
    static $cache = [];

    if ( isset( $cache[ $image_url ] ) ) {
        return $cache[ $image_url ];
    }

    $image = [
        '@type' => 'ImageObject',
        'url'   => $image_url,
    ];

    if ( function_exists( 'hu_get_social_image_meta' ) ) {
        $meta = hu_get_social_image_meta( $image_url );

        if ( ! empty( $meta['width'] ) ) {
            $image['width'] = absint( $meta['width'] );
        }

        if ( ! empty( $meta['height'] ) ) {
            $image['height'] = absint( $meta['height'] );
        }
    }

    $cache[ $image_url ] = $image;

    return $image;
}

/**
 * Build a generic WebPage node that anchors a singular view in the graph.
 *
 * Pages that already emit a dedicated WebPage subtype (ProfilePage, AboutPage,
 * CollectionPage, or the bespoke agentur WebPage) provide their own page node
 * and are skipped here to avoid two page entities describing one URL.
 *
 * @param int    $post_id Queried post ID.
 * @param string $slug    Post slug.
 * @return array<string, mixed>|null
 */
function hu_build_generic_webpage_schema( $post_id, $slug ) {
    if ( ! $post_id ) {
        return null;
    }

    // Slugs whose templates already emit a WebPage-subtype page node.
    $page_subtype_slugs = [
        'uber-mich',                  // ProfilePage
        'wordpress-agentur-hannover', // WebPage
        'case-studies',               // CollectionPage
        'case-studies-e-commerce',    // CollectionPage
        'ergebnisse',                 // CollectionPage
        'whitelabel-retainer',        // AboutPage
        'whitelabel-retainer-proof',  // AboutPage
        'whitelabel',                 // AboutPage
    ];

    if ( in_array( $slug, $page_subtype_slugs, true ) ) {
        return null;
    }

    $permalink = get_permalink( $post_id );

    if ( ! $permalink ) {
        return null;
    }

    $base        = trailingslashit( $permalink );
    $title       = get_the_title( $post_id );
    $excerpt     = get_the_excerpt( $post_id );
    $description = $excerpt ? wp_strip_all_tags( $excerpt ) : wp_strip_all_tags( $title );

    $type = 'WebPage';
    if ( in_array( $slug, [ 'kontakt', 'anfrage' ], true ) ) {
        $type = 'ContactPage';
    }

    $webpage = [
        '@context'    => 'https://schema.org',
        '@type'       => $type,
        '@id'         => $base . '#webpage',
        'url'         => $permalink,
        'name'        => $title,
        'description' => $description,
        'inLanguage'  => 'de',
        'isPartOf'    => [ '@id' => home_url( '/#website' ) ],
    ];

    if ( is_front_page() ) {
        $webpage['about'] = [ '@id' => home_url( '/#organization' ) ];
    }

    $published = get_post_time( DATE_W3C, true, $post_id );
    $modified  = get_post_modified_time( DATE_W3C, true, $post_id );

    if ( $published ) {
        $webpage['datePublished'] = $published;
    }

    if ( $modified ) {
        $webpage['dateModified'] = $modified;
    }

    $image = get_the_post_thumbnail_url( $post_id, 'full' );
    if ( $image ) {
        $webpage['primaryImageOfPage'] = [
            '@type' => 'ImageObject',
            'url'   => $image,
        ];
    }

    // Reference the BreadcrumbList only when the global breadcrumb is actually
    // emitted for this view; otherwise the reference would dangle.
    if ( hu_should_output_global_breadcrumb_schema() ) {
        $webpage['breadcrumb'] = [ '@id' => $base . '#breadcrumb' ];
    }

    // Link the page container to its primary content entity where one is
    // deterministically emitted for this view. Additional links (e.g. service
    // pages) are attached by the caller, which already knows what was emitted.
    // The SEO cornerstone template owns its own Article node, so the global
    // BlogPosting is suppressed there and the reference must follow suit.
    if ( is_singular( 'post' ) ) {
        if ( function_exists( 'hu_is_seo_cornerstone_article' ) && hu_is_seo_cornerstone_article() ) {
            $webpage['mainEntity'] = [ '@id' => $base . '#article' ];
        } else {
            $webpage['mainEntity'] = [ '@id' => $base . '#blogposting' ];
        }
    }

    return $webpage;
}

function hu_output_schema()
{
    $google_maps_url = hu_brand_map_url();

    // Organization / LocalBusiness schema
    $org = [
        '@context' => 'https://schema.org',
        '@type'    => ['Organization', 'LocalBusiness'],
        '@id'      => home_url('/#organization'),
        'name'     => 'Haşim Üner | Architekt für eigene Anfrage-Systeme',
        'alternateName' => 'Haşim Üner',
        'url'      => home_url(),
        'description' => 'Architekt für eigene Anfrage-Systeme: Solar- und Wärmepumpen-Anbieter im DACH-Raum lösen Portal-Abhängigkeit ab und senken Leadkosten messbar — durch Website, Tracking, Vorqualifizierung und Kanal-Steuerung als ein verbundenes System.',
        'telephone'   => '+49 176 81407134',
        'email'       => 'info@hasimuener.de',
        'logo'        => function_exists( 'hu_get_brand_logo_url' ) ? hu_get_brand_logo_url() : content_url( '/uploads/2025/08/cropped-Logo-hasim-uener-1.webp' ),
        'image'       => hu_get_profile_image_url(),
        'founder'     => hu_person_schema_ref(),
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
            'latitude'  => '52.2736456',
            'longitude' => '9.7559953'
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
        ],
        'sameAs' => hu_business_same_as_urls(),
        'hasMap' => $google_maps_url,
        'knowsAbout' => [
            'WordPress',
            'Technisches SEO',
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
                '@type'  => 'City',
                'name'   => 'Braunschweig',
                'sameAs' => 'https://de.wikipedia.org/wiki/Braunschweig'
            ],
            [
                '@type'  => 'City',
                'name'   => 'Wolfsburg',
                'sameAs' => 'https://de.wikipedia.org/wiki/Wolfsburg'
            ],
            [
                '@type'  => 'City',
                'name'   => 'Hildesheim',
                'sameAs' => 'https://de.wikipedia.org/wiki/Hildesheim'
            ],
            [
                '@type'  => 'AdministrativeArea',
                'name'   => 'Region Hannover',
                'sameAs' => 'https://de.wikipedia.org/wiki/Region_Hannover'
            ],
            [
                '@type'  => 'AdministrativeArea',
                'name'   => 'Niedersachsen',
                'sameAs' => 'https://de.wikipedia.org/wiki/Niedersachsen'
            ],
            [
                '@type' => 'AdministrativeArea',
                'name'  => 'DACH'
            ],
        ],
        'hasOfferCatalog' => [
            '@type'           => 'OfferCatalog',
            'name'            => 'Anfrage-Systeme für Solar-, Wärmepumpen- und Speicher-Anbieter',
            'itemListElement' => [
                [
                    '@type'       => 'Offer',
                    'name'        => 'Marktcheck',
                    'description' => 'Diagnostischer Einstieg: Anfragebremsen, Datenlage, Sichtbarkeit, Region und Conversion-Reihenfolge einordnen.',
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
                    'description' => 'Sekundäre lokale B2B-Seite in Hannover: WordPress-System, technisches SEO, Tracking und Conversion als verbundenes Anfrage-System.',
                    'url'         => home_url('/wordpress-agentur-hannover/'),
                ],
                [
                    '@type'       => 'Offer',
                    'name'        => 'Speed & Core Web Vitals',
                    'description' => 'Performance-Arbeit mit Fokus auf LCP, INP, CLS, Server-Antwortzeiten und tragfähige Anfrage-System-Basis.',
                    'url'         => home_url('/wgos-assets/cwv-optimierung/'),
                ],
                [
                    '@type'       => 'Offer',
                    'name'        => 'Conversion-Optimierung',
                    'description' => 'Systematische Optimierung von Angebotsseiten und Nutzerpfaden für mehr qualifizierte Anfragen.',
                    'url'         => home_url('/wordpress-agentur-hannover/#methode'),
                ],
            ],
        ],
    ];

    // WebSite schema — root node of the knowledge graph.
    // Multiple schemas (blog/category CollectionPage, agentur WebPage,
    // Ergebnisse CollectionPage) reference this node via isPartOf, so it must
    // exist site-wide; otherwise those references dangle.
    $website = [
        '@context'    => 'https://schema.org',
        '@type'       => 'WebSite',
        '@id'         => home_url('/#website'),
        'url'         => home_url('/'),
        'name'        => 'Haşim Üner | Architekt für eigene Anfrage-Systeme',
        'description' => 'Architekt für eigene Anfrage-Systeme: Solar- und Wärmepumpen-Anbieter im DACH-Raum lösen Portal-Abhängigkeit ab und senken Leadkosten messbar — durch Website, Tracking, Vorqualifizierung und Kanal-Steuerung als ein verbundenes System.',
        'inLanguage'  => 'de',
        'publisher'   => ['@id' => home_url('/#organization')],
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'       => 'EntryPoint',
                'urlTemplate' => home_url('/?s={search_term_string}'),
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    // Canonical Person node — emitted site-wide so every author/founder @id
    // reference resolves to one authoritative entity instead of inline copies.
    $schemas = [$org, $website, hu_get_person_node()];

    $archive_collection = hu_get_blog_archive_collection_schema();
    if ( is_array( $archive_collection ) ) {
        $schemas[] = $archive_collection;
    }

    // Service definitions (slug => data)
    $service_definitions = [
        'wordpress-agentur-hannover' => [
            'name'        => 'WordPress Agentur Hannover',
            'description' => 'WordPress Agentur in Hannover für B2B-Unternehmen: technisches SEO, Wartungsvertrag, Tracking, Conversion und Angebotsseiten als ein verbundenes System mit kontrollierter Weiterentwicklung.',
            'serviceType' => 'WordPress Agentur',
            'serviceOutput' => 'Steuerbares WordPress-System mit Angebotsseiten, technischem SEO, Wartung, Datenebene, KPI-Klarheit und vollen Zugängen',
            'hasOfferCatalog' => [
                '@type'           => 'OfferCatalog',
                'name'            => 'Leistungsbereiche der WordPress Agentur Hannover',
                'itemListElement' => [
                    [
                        '@type'       => 'Offer',
                        'name'        => 'Technisches SEO und Tracking',
                        'description' => 'WordPress, technisches SEO, Tracking und Conversion in der richtigen Reihenfolge als verbundenes Anfrage-System.',
                        'url'         => home_url('/wordpress-agentur-hannover/#technisches-seo'),
                    ],
                    [
                        '@type'       => 'Offer',
                        'name'        => 'Anfrage-System-Methode',
                        'description' => 'Strategie, technisches Fundament, Messbarkeit, Sichtbarkeit, Conversion und Weiterentwicklung als zusammenhängende Methode.',
                        'url'         => home_url('/wordpress-agentur-hannover/#methode'),
                    ],
                    [
                        '@type'       => 'Offer',
                        'name'        => 'Laufende WordPress-Betreuung',
                        'description' => 'Wartung, Updates und kontrollierte Weiterentwicklung für etablierte WordPress-Systeme im Rahmen laufender Mandate.',
                        'url'         => home_url('/wordpress-agentur-hannover/#wordpress-wartung'),
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
                    'description'   => 'Händischer Marktcheck für Solar- und Wärmepumpen-Anbieter mit unklarer Anfragequalität oder Portal-Abhängigkeit'
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
                    'description'   => 'Händischer Marktcheck für Solar- und Wärmepumpen-Anbieter mit unklarer Anfragequalität oder Portal-Abhängigkeit'
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
        // Seiten sind 301 auf vorhandene Anker der Agentur-Money-Page konsolidiert,
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
            'description' => 'Conversion Rate Optimierung auf WordPress: Angebotsseiten, Proof, CTA-Führung und Formulare für mehr qualifizierte B2B-Anfragen.',
            'serviceType' => 'Conversion Rate Optimierung für WordPress',
            'serviceOutput' => 'Klarere Angebotsseiten, bessere Anfragequalität und belastbarere Leadpfade auf B2B-WordPress-Websites'
        ],

        'ga4-tracking-setup' => [
            'name'        => 'GA4 Tracking Setup für B2B-WordPress-Websites',
            'description' => 'GA4 Tracking Setup: Event-Logik, GTM, Consent Mode und Server Side Tracking für belastbare Leadsignale in WordPress.',
            'serviceType' => 'GA4 Tracking Setup & Server Side Tracking',
            'serviceOutput' => 'Belastbare Lead- und Nachfrage-Signale mit GA4, GTM, Consent und serverseitiger Messung für B2B-Websites'
        ],

        'performance-marketing' => [
            'name'        => 'Performance Marketing für B2B-WordPress-Websites',
            'description' => 'Aktivierungslayer für bezahlte Nachfrage: erst Tracking, Technik und Zielseite, dann skalierbare Kampagnen.',
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
                    'serviceType'   => 'Systembaustein',
                    'serviceOutput' => (string) ($asset['result'] ?? ''),
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
            ];

            if (isset($def['offers'])) {
                $service['offers'] = $def['offers'];
            }

            if (isset($def['hasOfferCatalog'])) {
                $service['hasOfferCatalog'] = $def['hasOfferCatalog'];
            }

            $schemas[] = $service;

            if ('wordpress-agentur-hannover' === $slug) {
                // Dedicated ProfessionalService (LocalBusiness subtype) for the
                // Hannover money page: explicit geo, address and an extended
                // areaServed array boost local-pack relevance.
                $hannover_area_served = [
                    [
                        '@type'  => 'City',
                        'name'   => 'Hannover',
                        'sameAs' => 'https://de.wikipedia.org/wiki/Hannover',
                    ],
                    [
                        '@type'  => 'City',
                        'name'   => 'Pattensen',
                        'sameAs' => 'https://de.wikipedia.org/wiki/Pattensen',
                    ],
                    [
                        '@type'  => 'City',
                        'name'   => 'Braunschweig',
                        'sameAs' => 'https://de.wikipedia.org/wiki/Braunschweig',
                    ],
                    [
                        '@type'  => 'City',
                        'name'   => 'Wolfsburg',
                        'sameAs' => 'https://de.wikipedia.org/wiki/Wolfsburg',
                    ],
                    [
                        '@type'  => 'City',
                        'name'   => 'Hildesheim',
                        'sameAs' => 'https://de.wikipedia.org/wiki/Hildesheim',
                    ],
                    [
                        '@type'  => 'AdministrativeArea',
                        'name'   => 'Region Hannover',
                        'sameAs' => 'https://de.wikipedia.org/wiki/Region_Hannover',
                    ],
                    [
                        '@type'  => 'AdministrativeArea',
                        'name'   => 'Niedersachsen',
                        'sameAs' => 'https://de.wikipedia.org/wiki/Niedersachsen',
                    ],
                ];

                $professional_service = [
                    '@context'           => 'https://schema.org',
                    '@type'              => 'ProfessionalService',
                    '@id'                => home_url('/wordpress-agentur-hannover/#localbusiness'),
                    'name'               => 'Haşim Üner – WordPress Agentur Hannover',
                    'alternateName'      => 'WordPress Agentur Hannover',
                    'description'        => $def['description'],
                    'url'                => home_url('/wordpress-agentur-hannover/'),
                    'image'              => hu_get_profile_image_url(),
                    'logo'               => function_exists( 'hu_get_brand_logo_url' ) ? hu_get_brand_logo_url() : content_url( '/uploads/2025/08/cropped-Logo-hasim-uener-1.webp' ),
                    'telephone'          => '+49 176 81407134',
                    'email'              => 'info@hasimuener.de',
                    'priceRange'         => '€€',
                    'currenciesAccepted' => 'EUR',
                    'paymentAccepted'    => 'Überweisung',
                    'parentOrganization' => ['@id' => home_url('/#organization')],
                    'address'            => [
                        '@type'           => 'PostalAddress',
                        'streetAddress'   => 'Warschauer Str. 5',
                        'addressLocality' => 'Pattensen',
                        'addressRegion'   => 'Niedersachsen',
                        'postalCode'      => '30982',
                        'addressCountry'  => 'DE',
                    ],
                    'geo' => [
                        '@type'     => 'GeoCoordinates',
                        'latitude'  => '52.2736456',
                        'longitude' => '9.7559953',
                    ],
                    'hasMap'        => $google_maps_url,
                    'areaServed'    => $hannover_area_served,
                    'knowsAbout'    => [
                        'WordPress',
                        'Technisches SEO',
                        'Core Web Vitals',
                        'Conversion Rate Optimization',
                        'GA4 Tracking',
                        'Server-Side Tagging',
                        'B2B Lead Generation',
                    ],
                    'knowsLanguage' => ['de', 'en', 'tr'],
                    'sameAs'        => hu_business_same_as_urls(),
                    'openingHoursSpecification' => [
                        [
                            '@type'     => 'OpeningHoursSpecification',
                            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                            'opens'     => '08:30',
                            'closes'    => '16:00',
                        ],
                    ],
                ];

                $schemas[] = $professional_service;

                $schemas[] = [
                    '@context'    => 'https://schema.org',
                    '@type'       => 'WebPage',
                    '@id'         => home_url('/wordpress-agentur-hannover/#webpage'),
                    'url'         => home_url('/wordpress-agentur-hannover/'),
                    'name'        => 'WordPress Agentur Hannover',
                    'description' => $def['description'],
                    'inLanguage'  => 'de',
                    'isPartOf'    => ['@id' => home_url('/#website')],
                    'about'       => [
                        ['@id' => home_url('/#organization')],
                        ['@id' => home_url('/wordpress-agentur-hannover/#localbusiness')],
                    ],
                    'mainEntity'  => ['@id' => home_url('/wordpress-agentur-hannover/#service')],
                    'author'      => hu_person_schema_ref(),
                    'reviewedBy'  => hu_person_schema_ref(),
                ];

                $agentur_article = [
                    '@context'         => 'https://schema.org',
                    '@type'            => 'Article',
                    '@id'              => home_url('/wordpress-agentur-hannover/#article'),
                    'url'              => home_url('/wordpress-agentur-hannover/'),
                    'mainEntityOfPage' => ['@id' => home_url('/wordpress-agentur-hannover/#webpage')],
                    'headline'         => 'WordPress Agentur Hannover für B2B-Websites und Anfrage-Systeme',
                    'description'      => $def['description'],
                    'inLanguage'       => 'de',
                    'articleSection'   => 'WordPress, technisches SEO und B2B-Anfrage-Systeme',
                    'author'           => hu_person_schema_ref( true ),
                    'publisher'        => ['@id' => home_url('/#organization')],
                    'about'            => [
                        ['@id' => home_url('/wordpress-agentur-hannover/#service')],
                        ['@id' => home_url('/#organization')],
                    ],
                ];

                $agentur_published_date = get_post_time( DATE_W3C, true, $post_id );
                $agentur_modified_date  = get_post_modified_time( DATE_W3C, true, $post_id );

                if ( $agentur_published_date ) {
                    $agentur_article['datePublished'] = $agentur_published_date;
                }

                if ( $agentur_modified_date ) {
                    $agentur_article['dateModified'] = $agentur_modified_date;
                }

                $agentur_article_image = hu_get_post_schema_image_object( $post_id );
                if ( is_array( $agentur_article_image ) ) {
                    $agentur_article['image'] = $agentur_article_image;
                }

                $schemas[] = $agentur_article;
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

        // Über mich: ProfilePage referencing the canonical Person node, which
        // is emitted site-wide — no second inline Person needed here.
        if ($slug === 'uber-mich') {
            $profilePage = [
                '@context' => 'https://schema.org',
                '@type'    => 'ProfilePage',
                '@id'      => home_url('/uber-mich/#profile'),
                'url'      => hu_person_profile_url(),
                'name'     => 'Über mich – Haşim Üner',
                'mainEntity' => ['@id' => hu_person_schema_id()],
                'inLanguage' => 'de',
                'about'    => ['@id' => hu_person_schema_id()]
            ];

            $schemas[] = $profilePage;
        }

        // The SEO cornerstone template emits its own Article node; a second
        // BlogPosting for the same URL would duplicate the content entity.
        if ( is_singular( 'post' ) && $post_id
            && ! ( function_exists( 'hu_is_seo_cornerstone_article' ) && hu_is_seo_cornerstone_article() ) ) {
            $author_id         = (int) get_post_field( 'post_author', $post_id );
            $author_name       = $author_id ? get_the_author_meta( 'display_name', $author_id ) : 'Haşim Üner';
            $author_name       = hu_normalize_brand_text( $author_name );
            $post_permalink    = get_permalink( $post_id );
            $post_description  = get_the_excerpt( $post_id );
            $post_description  = $post_description ? wp_strip_all_tags( $post_description ) : wp_strip_all_tags( get_the_title( $post_id ) );
            $post_image        = hu_get_post_schema_image_object( $post_id );
            $published_date    = get_post_time( DATE_W3C, true, $post_id );
            $modified_date     = get_post_modified_time( DATE_W3C, true, $post_id );

            $blog_posting = [
                '@context'         => 'https://schema.org',
                '@type'            => 'BlogPosting',
                '@id'              => trailingslashit( $post_permalink ) . '#blogposting',
                'mainEntityOfPage' => [ '@id' => trailingslashit( $post_permalink ) . '#webpage' ],
                'headline'         => get_the_title( $post_id ),
                'description'      => $post_description,
                'datePublished'    => $published_date,
                'dateModified'     => $modified_date,
                'inLanguage'       => 'de',
                'author'           => hu_person_schema_ref( true, $author_name ),
                'publisher'        => ['@id' => home_url('/#organization')],
                'isPartOf'         => [
                    '@type' => 'Blog',
                    '@id'   => home_url('/blog/#blog'),
                    'url'   => home_url('/blog/'),
                    'name'  => 'Insights',
                ],
            ];

            if ( is_array( $post_image ) ) {
                $blog_posting['image'] = $post_image;
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
                'author'           => hu_person_schema_ref( true ),
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

            $e3_article_image = hu_get_post_schema_image_object( $post_id );
            if ( is_array( $e3_article_image ) ) {
                $e3_article['image'] = $e3_article_image;
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
                'headline' => 'Ergebnisse und Case Studies von Haşim Üner',
                'description' => 'Öffentliche Case Studies und nachvollziehbare Systemlogik als Proof-Layer für WordPress-, SEO-, Tracking- und CRO-Arbeit.',
                'inLanguage' => 'de',
                'isPartOf' => ['@id' => home_url('/#website')],
                'about' => ['@id' => home_url('/#organization')],
                'hasPart' => [
                    [
                        '@type' => 'Article',
                        'name'  => 'E3 New Energy',
                        'url'   => home_url('/e3-new-energy/')
                    ],
                ],
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
                'mainEntity' => hu_person_schema_ref(),
                'image' => hu_get_portrait_image_url(),
            ];

            $schemas[] = $whitelabelPage;
        }

        /**
         * FAQ schema from save-time cache.
         *
         * Dynamic FAQ extraction is intentionally not run during frontend
         * rendering; save_post fills _hu_faq_schema_entities_json once.
         */
        global $post;
        if ( isset( $post ) && $post instanceof WP_Post ) {
            $template_owns_faq_schema = (
                in_array( $slug, [ 'wordpress-agentur-hannover', 'wgos', 'wordpress-growth-operating-system' ], true )
                || ( function_exists( 'nexus_is_wgos_cluster_page' ) && nexus_is_wgos_cluster_page( $slug ) )
                || ( function_exists( 'hu_is_seo_cornerstone_article' ) && hu_is_seo_cornerstone_article() )
            );

            if ( ! $template_owns_faq_schema ) {
                $faq_entities = hu_get_cached_post_faq_schema_entities( $post->ID );

                if ( ! empty( $faq_entities ) ) {
                    $faq_base  = is_front_page() ? home_url( '/' ) : home_url( '/' . ( $slug ?: '' ) . '/' );
                    $schemas[] = [
                        '@context'   => 'https://schema.org',
                        '@type'      => 'FAQPage',
                        '@id'        => $faq_base . '#faq',
                        'url'        => $faq_base,
                        'inLanguage' => 'de',
                        'publisher'  => ['@id' => home_url('/#organization')],
                        'mainEntity' => $faq_entities,
                    ];
                }
            }
        }

        // Generic WebPage container — anchors this view in the knowledge graph
        // for the long tail of pages without a dedicated page-type node.
        $generic_webpage = hu_build_generic_webpage_schema( $post_id, $slug );
        if ( is_array( $generic_webpage ) ) {
            if ( empty( $generic_webpage['mainEntity'] ) && $slug && array_key_exists( $slug, $service_definitions ) ) {
                $generic_webpage['mainEntity'] = [ '@id' => home_url( '/' . $slug . '/#service' ) ];
            }

            $schemas[] = $generic_webpage;
        }
    }

    // ── BreadcrumbList Schema ─────────────────────────────────────
    // Output on pages that do not already provide route-specific breadcrumbs.
    if ( hu_should_output_global_breadcrumb_schema() ) {
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
            $breadcrumb_schema = [
                '@context'        => 'https://schema.org',
                '@type'           => 'BreadcrumbList',
                'itemListElement' => $breadcrumb_items,
            ];

            // Stable @id so the generic WebPage node can reference this
            // breadcrumb on singular views.
            $bc_object_id = get_queried_object_id();
            if ( is_singular() && $bc_object_id ) {
                $bc_permalink = get_permalink( $bc_object_id );
                if ( $bc_permalink ) {
                    $breadcrumb_schema['@id'] = trailingslashit( $bc_permalink ) . '#breadcrumb';
                }
            }

            $schemas[] = $breadcrumb_schema;
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
