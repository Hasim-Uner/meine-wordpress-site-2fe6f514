<?php
/**
 * NEXUS ARCHIVE TEMPLATE
 * Automatische Hero-Generierung aus Kategorie-Daten.
 */

get_header(); 
get_template_part( 'template-parts/blog-header' );
?>

<div class="site-main nexus-archive-container nexus-archive-container--with-blog-header">

    <header class="nexus-archive-hero">
        <div class="nexus-hero-inner">
            
            <span class="nexus-meta-top">Deep Dive Category</span>

            <h1 class="nexus-title"><?php single_term_title(); ?></h1>

            <div class="nexus-archive-desc">
                <?php the_archive_description(); ?>
            </div>

        </div>
    </header>

    <div class="nexus-grid-wrapper">
        <?php get_template_part( 'template-parts/blog-notify', null, [ 'variant' => 'compact' ] ); ?>

        <?php if ( have_posts() ) : ?>
            
            <div class="nexus-card-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    
                    <article class="nexus-card">
                        <a href="<?php the_permalink(); ?>" class="nexus-card-link">
                            <span class="nexus-card-date"><?php echo get_the_date('d.m.Y'); ?></span>
                            <h2 class="nexus-card-title"><?php the_title(); ?></h2>
                            <p class="nexus-card-excerpt">
                                <?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
                            </p>
                            <span class="nexus-read-more">Read Insight &rarr;</span>
                        </a>
                    </article>

                <?php endwhile; ?>
            </div>

            <div class="nexus-pagination">
                <?php the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => '&larr; Zurück',
                    'next_text' => 'Weiter &rarr;',
                ) ); ?>
            </div>

        <?php else : ?>
            <div class="nexus-empty-state">
                <p>Noch keine Einträge in dieser Kategorie.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php get_footer(); ?>
