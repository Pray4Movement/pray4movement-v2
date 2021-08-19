<?php
/**
 * Template Name: Pray4Networks
 * Template Post Type: page
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Pray4Movement 1.0
 */

$args = array(
    'post_type' => 'p4network',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'post_title',
    'order' => 'ASC'
);
$list = new WP_Query( $args );




get_header();
?>
<style>
    .wp-block-columns {
        display: flex;
    }
    .box-border {
        border: 1px solid lightgrey;
        border-radius: 15px;
        flex-basis:33.33%;
        padding:0 1em;
    }
</style>
<main id="site-content" role="main">

    <!-- wp:genesis-blocks/gb-container {"containerPaddingTop":15,"containerPaddingRight":5,"containerPaddingBottom":15,"containerPaddingLeft":5,"containerWidth":"full","containerMaxWidth":850,"containerBackgroundColor":"#0b0b0b","containerImgID":3854,"className":"gb-layout-hero-1"} -->
    <div style="background-color:#0b0b0b;padding-left:5%;padding-right:5%;padding-bottom:15%;padding-top:15%" class="wp-block-genesis-blocks-gb-container gb-layout-hero-1 gb-block-container alignfull">
        <div class="gb-container-inside">
            <div class="gb-container-image-wrap">
                <img class="gb-container-image has-background-dim" src="<?php echo trailingslashit( get_stylesheet_directory_uri() ) ?>assets/images/1900x1200_img_4.jpg" alt="hero header placeholder"/></div><div class="gb-container-content" style="max-width:850px"><!-- wp:heading {"textAlign":"center","className":"gb-white-text"} -->
                <h2 class="has-text-align-center gb-white-text">Pray4 Network</h2>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"align":"center","style":{"color":{"text":"#ffffff"}}} -->
                <p class="has-text-align-center has-text-color" style="color:#ffffff">A community of prayer mobilization asking God for a disciple making movement.</p>
                <!-- /wp:paragraph -->

                <!-- /wp:genesis-blocks/gb-button --></div></div></div>
    <!-- /wp:genesis-blocks/gb-container -->

    <div class="post-inner thin" style="margin:0 auto; max-width: 1200px;">
        <!-- wp:group -->
        <div class="wp-block-group"><!-- wp:columns -->

            <?php
            if ( ! empty( $list->posts ) ) {
                $posts = array_chunk( $list->posts, 3 );
                foreach( $posts as $post_block ) {
                    ?>
                    <div class="wp-block-columns">
                    <?php
                    foreach( $post_block as $p ) {
                        dt_write_log($p);
                        $meta = get_post_meta($p->ID);
                        ?>
                        <div class="wp-block-column box-border"><!-- wp:group -->
                            <div class="wp-block-group"><!-- wp:post-featured-image /-->

                                <img src="<?php echo get_the_post_thumbnail_url($p->ID) ?>" /><br>
                                <!-- wp:paragraph -->
                                <p>
                                    <strong><?php echo $p->post_title ?></strong><br>
                                    <?php echo $meta['network_tagline'][0] ?? '' ?><br>
                                    <a href="<?php echo $meta['network_url'][0] ?? '' ?>"><?php echo $meta['network_url'][0] ?? '' ?></a>

                                </p>

                               </div>

                        </div>
                        <?php
                    }
                    ?>
                    </div>
                    <?php
                }
            }
            ?>

            </div>
        </div>
    <!-- /wp:group -->

    </div>
</main><!-- #site-content -->

<?php get_footer(); ?>
