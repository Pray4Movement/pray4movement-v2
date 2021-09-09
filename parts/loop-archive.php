<?php
/**
 * Template part for displaying posts
 *
 * Used for single, index, archive, search.
 */
$post = get_post();
?>

<article id="post-<?php the_ID(); ?>" role="article">
    <div class="grid-x grid-padding-x grid-padding-y article-section highlight-background border-bottom padding-top-1" data-post-id="<?php the_ID(); ?>">
        <?php if ( has_post_thumbnail( $post->ID ) ) : ?>
        <div class="cell medium-2 hide-for-small-only">
            <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail() ?></a>
        </div>
        <div class="cell medium-10">
            <?php else : ?>
            <div class="cell"><!-- full width without image-->
                <?php endif; ?>

                <header class="article-header">
                    <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
                </header> <!-- end article header -->

                <section class="entry-content" itemprop="text">
                    <?php the_excerpt(); ?>
                </section> <!-- end article section -->

                <footer class="article-footer">
                    <p class="tags small-text"><?php echo get_the_date() ?></p>
                </footer> <!-- end article footer -->
            </div>
        </div>
</article> <!-- end article -->
