<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 */

get_header(); ?>

    <div class="content white-section">

        <div class="inner-content grid-x grid-margin-x grid-padding-x padding-vertical-1">

            <div class="cell medium-1"></div>

            <main class="main small-12 medium-10 large-10 cell" role="main">

                <div class="grid-x grid-margin-x">

                    <div class="cell medium-8">

                        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                            <!-- To see additional archive styles, visit the /parts directory -->
                                <?php get_template_part( 'parts/loop', 'archive' ); ?>

                        <?php endwhile; ?>

                            <?php dtps_page_navi(); ?>

                        <?php else : ?>

                            <?php get_template_part( 'parts/content', 'missing' ); ?>

                        <?php endif; ?>
                    </div>

                    <div class="cell medium-4">

                        <!-- other sidebar elements -->
                        <?php get_sidebar(); ?>


                    </div>

                </div>

            </main> <!-- end #main -->

            <div class="cell medium-1"></div>

        </div> <!-- end #inner-content -->

    </div> <!-- end #content -->

<?php get_footer(); ?>
