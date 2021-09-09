<?php
/**
 * The template for displaying search results pages
 *
 * For more info: https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 */

get_header(); ?>

<main class="main white-section" role="main" style="max-width:1100px; padding: 1em; margin: 0 auto;">

    <header><h1 class="center"><?php esc_html_e( 'Results for:', 'dtps' ); ?> <?php echo esc_attr( get_search_query() ); ?></h1></header>

    <?php if (have_posts()) : while ( have_posts() ) : the_post(); ?>

        <!-- To see additional archive styles, visit the /parts directory -->
            <?php get_template_part( 'parts/loop', 'archive' ); ?>

    <?php endwhile; ?>

        <?php dtps_page_navi(); ?>

    <?php else : ?>

        <?php get_template_part( 'parts/content', 'missing' ); ?>

    <?php endif; ?>

</main> <!-- end #main -->

<?php get_footer(); ?>
