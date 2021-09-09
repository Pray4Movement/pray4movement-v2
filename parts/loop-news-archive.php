<?php
/**
 * Template part for displaying posts
 *
 * Used for single, index, archive, search.
 */
//$post = get_post();
?>

<article id="post-<?php the_ID(); ?>" role="article">

    <div class="grid-x grid-padding-x grid-padding-y article-section highlight-background" data-post-id="<?php the_ID(); ?>">
        <div class="cell">
            <span class="small-text"><?php echo get_the_date() ?></span>
            <header class="article-header">
                <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
            </header> <!-- end article header -->

            <section class="entry-content news-content" itemprop="text">
                <?php the_content(); ?>
            </section> <!-- end article section -->

            <?php
            $categories = wp_get_object_terms( $post->ID, 'news_categories' );

            if ( ! empty( $categories ) ) {
                echo '<footer class="article-footer padding-bottom-1">Categories: ';

                $i = 0;
                foreach ( $categories as $category ) {
                    if ( $i > 0 ) {
                        echo ', ';
                    }
                    echo '<a href="'.esc_url( site_url() ).'/news-categories/'.esc_html( $category->slug ).'">'. esc_html( $category->name ) . '</a>';
                    $i++;
                }
                echo '</footer> <!-- end article footer -->';
            }
            ?>

        </div>
    </div>

</article> <!-- end article -->
