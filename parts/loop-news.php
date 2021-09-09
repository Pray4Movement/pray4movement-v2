<?php
/**
 * Template part for displaying a single post
 */

$thumb_id = get_post_thumbnail_id();
$thumb_url_array = wp_get_attachment_image_src( $thumb_id, 'thumbnail-size', true );
$thumb_url = $thumb_url_array[0];
?>
<hr />
<article id="post-<?php the_ID(); ?>" <?php post_class( '' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

    <header class="article-header">

        <?php if ( has_post_thumbnail() ) : ?>
            <div class="hero-image" style="background: url('<?php echo esc_url( $thumb_url ) ?>');background-repeat:no-repeat;"></div>
        <?php endif; ?>

        <h1 class="entry-title single-title" itemprop="headline"><?php the_title(); ?></h1>

    </header> <!-- end article header -->

    <section class="entry-content" itemprop="text">

        <?php the_content(); ?>

    </section> <!-- end article section -->

    <footer class="article-footer">

        <p><?php echo get_the_date() ?></p>
        <p class="tags"><?php the_tags( '<span class="tags-title">' . __( 'Tags:', 'dtps' ) . '</span> ', ', ', '' ); ?></p>

    </footer> <!-- end article footer -->

</article> <!-- end article -->
