<?php
/**
 * Template part for displaying page content in page.php
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( '' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

    <header class="article-header ">
        <h2 class="entry-title single-title vertical-padding" style="font-weight:bold;" itemprop="headline"><?php the_title(); ?></h2>
    </header> <!-- end article header -->

    <hr>

    <section class="entry-content" itemprop="text">
        <?php the_content(); ?>

        <hr class="section-contents">
        <h5 class="section-contents">Section Contents</h5>
        <div class="section-contents-list section-contents">
            <ul>
                <?php wp_list_pages(array(
                    'post_type' => get_post_type( get_the_ID() ),
                    'sort_column' => 'menu_order',
                    'echo' => true,
                    'title_li' => null,
                    'child_of' => get_the_ID(),
                )) ?>
            </ul>
        </div>
    </section> <!-- end article section -->

    <hr>

    <footer class="article-footer">
        Last Modified: <?php the_modified_date(); ?>
        <p class="tags"><?php the_tags( '<span class="tags-title">' . __( 'Tags:', 'zume' ) . '</span> ', ', ', '' ); ?></p>
    </footer> <!-- end article footer -->

    <script>
        jQuery(document).ready(function(){
            jQuery("a").filter(function() {
                return jQuery(this).text() === '<?php the_title(); ?>';
            }).css( "text-decoration", "underline" ).css("font-weight", "bold")

            if ( jQuery('.section-contents-list li').text().length > 0 ) {
                jQuery('.section-contents').show()
            }
        })
    </script>

</article> <!-- end article -->
