<?php
/**
 * The template for displaying all single posts and attachments
 */

get_header(); ?>

<div id="documentation">

    <!-- Main -->
    <main role="main" id="post-main">

        <div class="grid-x grid-margin-x grid-padding-x">

            <div class="cell large-4 callout show-for-small-only">

                <?php get_template_part( "parts/sidebar", "user-documentation" ); ?>

            </div>

            <div class="cell large-8">

                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                        <?php get_template_part( 'parts/loop', 'documentation' ); ?>

                <?php endwhile; else : ?>

                    <?php get_template_part( 'parts/content', 'missing' ); ?>

                <?php endif; ?>

            </div>

            <div class="cell large-4 callout hide-for-small-only">

                <?php get_template_part( "parts/sidebar", "user-documentation" ); ?>

            </div>

        </div>

    </main> <!-- end #main -->

</div>

<?php get_footer(); ?>
