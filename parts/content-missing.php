<?php
/**
 * The template part for displaying a message that posts cannot be found
 */
?>

<div class="post-not-found">

    <?php if ( is_search() ) : ?>

        <header class="article-header">
            <h3><?php esc_html_e( 'Sorry, No Results.', 'dtps' );?></h3>
        </header>

        <section class="entry-content">
            <p><?php esc_html_e( 'Try your search again.', 'dtps' );?></p>
        </section>

        <section class="search">
            <p><?php get_search_form(); ?></p>
        </section> <!-- end search section -->

        <footer class="article-footer">

        </footer>

    <?php else : ?>

        <header class="article-header">
            <h3><?php esc_html_e( 'Oops, Post Not Found!', 'dtps' ); ?></h3>
        </header>

        <section class="entry-content">
            <p><?php esc_html_e( 'Uh Oh. Something is missing. Try double checking things.', 'dtps' ); ?></p>
        </section>

        <section class="search">
            <p><?php get_search_form(); ?></p>
        </section> <!-- end search section -->

        <footer class="article-footer">

        </footer>

    <?php endif; ?>

</div>
