<?php
$format = false;
if ( isset( $_GET['format'] ) && $_GET['format'] === 'compact' ) {
    $format = 'compact';
}
?>

<?php get_header(); ?>

<div class="page-wrapper">
    <div class="page-inner-wrapper">

        <!-- Statistics Section-->
        <div class="grid-x grid-padding-x deep-blue-section padding-vertical-1">
            <div class="cell center" style="cursor:pointer;" onclick="window.location = '<?php site_url() ?>/news'">
                <h1 class="center title">News</h1>
            </div>
        </div>

        <!-- Main -->
        <main role="main" id="post-main" >

            <div class="grid-x grid-margin-x grid-padding-x">

                <div class="cell large-8">

                    <?php /** Show Category Bread Crumb */
                    global $wp;
                    $url_parts = explode( '/', $wp->request );
                    if ( 'news-categories' === $url_parts[0] ) {
                        the_archive_title();
                    } ?>

                    <?php /* Show default full view*/
                    if ( ! $format ) : if (have_posts()) : while (have_posts()) : the_post(); ?>
                        <hr><?php get_template_part( 'parts/loop', 'news-archive' ); ?>
                    <?php endwhile; ?>
                            <?php dtps_page_navi(); ?>
                    <?php else : ?>
                        <?php get_template_part( 'parts/content', 'missing' ); ?>
                    <?php endif;
endif; /* no format */ ?>


                    <?php /* Show compressed view */
                    if ( $format ) : if (have_posts()) : ?>
                        <table class=""><thead><tr><th>Date</th><th></th></tr></thead><tbody>
                            <?php while (have_posts()) : the_post(); ?>
                            <tr>
                                <td><span class="small-text"><?php echo get_the_date() ?></span></td>
                                <td><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" style="text-decoration: none;"><?php the_title(); ?></a></td>
                            </tr>
                            <?php endwhile; ?>
                            </tbody></table>
                            <?php dtps_page_navi(); ?>
                    <?php else : ?>
                        <?php get_template_part( 'parts/content', 'missing' ); ?>
                    <?php endif;
        /* have posts*/ endif; /* has format */  ?>

                </div>

                <div class="sidebar cell large-4">

                    <hr class="show-for-small-only" />

                    <?php get_sidebar( 'news' ); ?>

                </div>


            </div>

        </main> <!-- end #main -->

    </div>
</div>

<hr>

<?php get_footer(); ?>
