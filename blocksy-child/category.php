<?php
/**
 * NEXUS PILLAR HUB: Kategorie-Archiv als Content-Hub
 * Features: Hero mit Intro, Featured Post, Grid, Service-CTA, Pillar-Sidebar
 * 
 * @package Blocksy Child – Nexus Edition
 */
get_header();
get_template_part( 'template-parts/blog-header' );

$audit_url = nexus_get_audit_url();

// --- Kategorie-Daten ---
$current_category = get_queried_object();
$cat_slug         = $current_category->slug;
$cat_name         = $current_category->name;
$cat_description  = category_description();
$cat_count        = $current_category->count;
$primary_urls     = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];

// --- Kategorie-Mapping: Kategorie → passende Zielseite + CTA ---
$pillar_map = [
    'strategie' => [
        'icon'        => '🧭',
        'badge'       => 'Strategie & Growth',
        'subtitle'    => 'Eigene Anfrage-Systeme für Solar- und Wärmepumpen-Anbieter. Keine Portal-Abhängigkeit, messbar niedrigere Leadkosten.',
        'cta_label'   => 'Marktcheck starten',
        'cta_url'     => $audit_url,
        'cta_text'    => 'Der Marktcheck zeigt in 60 Sekunden, wo Ihre Website Anfragen verliert und welche Hebel zuerst Wirkung versprechen.',
    ],
    'seo' => [
        'icon'        => '🔍',
        'title'       => 'SEO & Sichtbarkeit',
        'badge'       => 'SEO & Sichtbarkeit',
        'subtitle'    => 'Technisches SEO, CRO und Performance-Synergie — für planbare Sichtbarkeit, bessere Lead-Qualität und effizientere Akquisekosten.',
        'cta_label'   => 'SEO-Diagnose anfragen',
        'cta_url'     => $primary_urls['seo'] ?? home_url( '/wordpress-agentur-hannover/#technisches-seo' ),
        'cta_text'    => 'Prüfen Sie zuerst das Fundament: Technik, Seitenstruktur, Tracking und Conversion-Reibung vor der nächsten Budgeterhöhung.',
    ],
    'tracking' => [
        'icon'        => '📊',
        'badge'       => 'Tracking & Analytics',
        'subtitle'    => 'GA4, Server-Side Tagging, Consent Management — Privacy-first Daten, die Entscheidungen ermöglichen.',
        'cta_label'   => 'GA4 Tracking Setup',
        'cta_url'     => $primary_urls['tracking'] ?? home_url( '/ga4-tracking-setup/' ),
        'cta_text'    => 'Die Tracking-Seite bündelt Audit, Consent, Event-Logik und serverseitige Messbarkeit in einem sauberen Einstieg.',
    ],
    'cro' => [
        'icon'        => '🎯',
        'badge'       => 'Conversion (CRO) & UX',
        'subtitle'    => 'A/B-Tests, UX-Optimierung und Conversion-Führung — mehr qualifizierte Anfragen aus dem gleichen Traffic.',
        'cta_label'   => 'CRO-Einstieg ansehen',
        'cta_url'     => $primary_urls['cro'] ?? home_url( '/wordpress-agentur-hannover/#wgos' ),
        'cta_text'    => 'Die CRO-Seite zeigt, wie Angebotslogik, Proof und CTA-Hierarchie in den naechsten sinnvollen Schritt uebergehen.',
    ],
    'wordpress-performance' => [
        'icon'        => '⚡',
        'badge'       => 'WordPress Performance',
        'subtitle'    => 'Core Web Vitals, Caching, Hosting, Theme-/Plugin-Optimierung — Geschwindigkeit als Wettbewerbsvorteil.',
        'cta_label'   => 'Performance-Check starten',
        'cta_url'     => $primary_urls['cwv'] ?? home_url( '/wgos-assets/cwv-optimierung/' ),
        'cta_text'    => 'Ihre Core Web Vitals entscheiden über Rankings und Conversions.',
    ],
];

// Fallback für nicht-gemappte Kategorien
$pillar = $pillar_map[$cat_slug] ?? [
    'icon'        => '📄',
    'badge'       => $cat_name,
    'subtitle'    => wp_strip_all_tags($cat_description) ?: 'Analysen und Insights zu ' . esc_html($cat_name) . '.',
    'cta_label'   => 'Marktcheck',
    'cta_url'     => $audit_url,
    'cta_text'    => 'Finden Sie heraus, wo Ihre Website Potenzial liegen lässt.',
];

$pillar_title = $pillar['title'] ?? $cat_name;

// --- Featured Post: Sticky oder neuester ---
$featured_args = [
    'category__in'   => [$current_category->term_id],
    'posts_per_page' => 1,
    'post__in'       => get_option('sticky_posts'),
];
$featured_query = new WP_Query($featured_args);

// Fallback: Wenn kein Sticky Post → neuester Post
if (!$featured_query->have_posts()) {
    $featured_args = [
        'category__in'   => [$current_category->term_id],
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    $featured_query = new WP_Query($featured_args);
}

// IDs der Featured Posts sammeln (um sie im Grid nicht zu doppeln)
$exclude_ids = [];
if ($featured_query->have_posts()) {
    foreach ($featured_query->posts as $fp) {
        $exclude_ids[] = $fp->ID;
    }
}
?>

<main id="main" class="site-main pillar-hub pillar-hub--with-blog-header">

    <!-- ══════════════════════════════════════
         SECTION 1: PILLAR HERO
         ══════════════════════════════════════ -->
    <header class="pillar-hero">
        <div class="nx-container">
            <span class="nx-badge nx-badge--gold"><?php echo esc_html($pillar['icon'] . ' ' . $pillar['badge']); ?></span>
            
            <h1 class="pillar-hero__title"><?php echo esc_html($pillar_title); ?></h1>
            
            <p class="pillar-hero__subtitle">
                <?php echo esc_html($pillar['subtitle']); ?>
            </p>

            <div class="pillar-hero__meta">
                <span class="pillar-meta-item">
                    <strong><?php echo (int) $cat_count; ?></strong> <?php echo esc_html( $cat_count === 1 ? 'Beitrag' : 'Beiträge' ); ?>
                </span>
                <span class="pillar-meta-divider">·</span>
                <span class="pillar-meta-item">Pillar Content Hub</span>
            </div>
        </div>
    </header>

    <!-- ══════════════════════════════════════
         SECTION 2: FEATURED POST ("Start here")
         ══════════════════════════════════════ -->
    <?php if ($featured_query->have_posts()) : while ($featured_query->have_posts()) : $featured_query->the_post();
        $feat_thumb = get_the_post_thumbnail_url(get_the_ID(), 'large');
    ?>
    <section class="pillar-featured">
        <div class="nx-container">
            <div class="pillar-featured__label">
                <span class="nx-badge nx-badge--gold">Hier starten</span>
            </div>
            
            <a href="<?php the_permalink(); ?>" class="pillar-featured__card">
                <?php if ($feat_thumb) : ?>
                    <div class="pillar-featured__image">
                        <img src="<?php echo esc_url($feat_thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                    </div>
                <?php endif; ?>
                
                <div class="pillar-featured__content">
                    <span class="pillar-featured__date"><?php echo get_the_date(); ?> · <?php echo function_exists('nexus_get_reading_time') ? nexus_get_reading_time() . ' Min.' : ''; ?></span>
                    <h2 class="pillar-featured__title"><?php the_title(); ?></h2>
                    <p class="pillar-featured__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p>
                    <span class="pillar-featured__cta">Analyse lesen →</span>
                </div>
            </a>
        </div>
    </section>
    <?php endwhile; endif; wp_reset_postdata(); ?>

    <!-- ══════════════════════════════════════
         SECTION 3: ALLE BEITRÄGE (Grid + Sidebar)
         ══════════════════════════════════════ -->
    <section class="pillar-content nx-section">
        <div class="nx-container">
            <div class="pillar-layout">
                
                <!-- MAIN: Post Grid -->
                <div class="pillar-main">
                    <h2 class="pillar-section-title">Alle Beiträge</h2>
                    
                    <div class="pillar-grid">
                        <?php 
                        // Eigenen Query für verbleibende Posts
                        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                        $grid_args = [
                            'category__in'   => [$current_category->term_id],
                            'posts_per_page' => 9,
                            'paged'          => $paged,
                            'post__not_in'   => $exclude_ids,
                        ];
                        $grid_query = new WP_Query($grid_args);
                        
                        if ($grid_query->have_posts()) : while ($grid_query->have_posts()) : $grid_query->the_post();
                            $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
                        ?>
                            <article class="pillar-card">
                                <a class="pillar-card__linkwrap" href="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php echo esc_attr( sprintf( 'Analyse lesen: %s', get_the_title() ) ); ?>">
                                    <?php if ($thumb) : ?>
                                        <div class="pillar-card__image">
                                            <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="pillar-card__body">
                                        <span class="pillar-card__date"><?php echo get_the_date(); ?></span>
                                        <h3 class="pillar-card__title"><?php the_title(); ?></h3>
                                        <p class="pillar-card__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 18); ?></p>
                                        <span class="pillar-card__link">Analyse lesen →</span>
                                    </div>
                                </a>
                            </article>
                        <?php endwhile; else : ?>
                            <p class="pillar-empty">In diesem Pillar werden aktuell neue Analysen vorbereitet.</p>
                        <?php endif; ?>
                    </div>

                    <?php if ($grid_query->max_num_pages > 1) : ?>
                        <div class="pillar-pagination">
                            <?php 
                            echo paginate_links([
                                'total'     => $grid_query->max_num_pages,
                                'current'   => $paged,
                                'prev_text' => '← Zurück',
                                'next_text' => 'Weiter →',
                            ]); 
                            ?>
                        </div>
                    <?php endif; wp_reset_postdata(); ?>
                </div>

                <!-- SIDEBAR: Pillar-Navigation + CTA -->
                <aside class="pillar-sidebar">

                    <?php get_template_part( 'template-parts/blog-notify', null, [ 'variant' => 'compact' ] ); ?>
                    
                    <!-- Service CTA -->
                    <div class="pillar-sidebar__cta">
                        <span class="pillar-sidebar__cta-icon"><?php echo $pillar['icon']; // raw-ok -- SVG icon markup ?></span>
                        <h3 class="pillar-sidebar__cta-title"><?php echo esc_html($pillar['cta_label']); ?></h3>
                        <p class="pillar-sidebar__cta-text"><?php echo esc_html($pillar['cta_text']); ?></p>
                        <a href="<?php echo esc_url($pillar['cta_url']); ?>" class="nx-btn nx-btn--primary nx-btn--full nx-btn--sm">
                            <?php echo esc_html($pillar['cta_label']); ?> →
                        </a>
                    </div>

                    <!-- Andere Pillars -->
                    <div class="pillar-sidebar__nav">
                        <h4 class="pillar-sidebar__nav-title">Weitere Pillars</h4>
                        <ul class="pillar-sidebar__nav-list">
                            <?php 
                            foreach ($pillar_map as $slug => $data) :
                                if ($slug === $cat_slug) continue;
                                $term = get_term_by('slug', $slug, 'category');
                                if (!$term) continue;
                            ?>
                                <li>
                                    <a href="<?php echo esc_url(get_term_link($term)); ?>" class="pillar-sidebar__nav-link">
                                        <span class="pillar-sidebar__nav-icon"><?php echo $data['icon']; // raw-ok -- SVG icon markup ?></span>
                                        <span class="pillar-sidebar__nav-name"><?php echo esc_html($data['badge']); ?></span>
                                        <span class="pillar-sidebar__nav-count"><?php echo (int) $term->count; ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Newsletter / Diagnose CTA -->
                    <div class="pillar-sidebar__audit">
                        <h4>Wo verbrennt Ihre Website Geld?</h4>
                        <p>Der Marktcheck zeigt, wo Technik, SEO und Conversion im Zusammenspiel Reibung erzeugen.</p>
                        <a href="<?php echo esc_url($audit_url); ?>" class="nx-btn nx-btn--ghost nx-btn--full nx-btn--sm">
                            Marktcheck starten →
                        </a>
                    </div>
                </aside>

            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
