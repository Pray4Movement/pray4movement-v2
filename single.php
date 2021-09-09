<?php
/**
 * The template for displaying all single posts and attachments
 */

get_header(); ?>

<div class="content">

    <div class="inner-content grid-x grid-margin-x grid-padding-x">

        <div class="cell medium-1"></div>

        <main class="main small-12 medium-10 large-10 cell" role="main">

            <div class="grid-x grid-margin-x">

                <div class="cell medium-8">


                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                            <?php get_template_part( 'parts/loop', 'single' ); ?>

                    <?php endwhile; else : ?>

                        <?php get_template_part( 'parts/content', 'missing' ); ?>

                    <?php endif; ?>

                </div>

                <div class="cell medium-4">

                    <?php get_sidebar( 'single' ); ?>

                </div>

        </main> <!-- end #main -->

        <div class="cell medium-1"></div>

    </div> <!-- end #inner-content -->

</div> <!-- end #content -->

<?php get_footer(); ?>
