<?php
/**
 * Block Patterns
 *
 * @link https://developer.wordpress.org/reference/functions/register_block_pattern/
 * @link https://developer.wordpress.org/reference/functions/register_block_pattern_category/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Pray4Movement 1.6
 */

/**
 * Register Block Pattern Category.
 */
if ( function_exists( 'register_block_pattern_category' ) ) {

    register_block_pattern_category(
        'pray4movement',
        array( 'label' => esc_html__( 'Pray4Movement', 'pray4movement' ) )
    );
}

/**
 * Register Block Patterns.
 */
if ( function_exists( 'register_block_pattern' ) ) {

    // Call to Action.
    register_block_pattern(
        'pray4movement/single-page',
        array(
            'title'         => esc_html__( 'Pray4Movement Single Page', 'pray4movement' ),
            'categories'    => array( 'pray4movement' ),
            'viewportWidth' => 1400,
            'content'       => implode(
                '',
                array(
                    '<div class="wp-block-media-text alignwide is-stacked-on-mobile"><figure class="wp-block-media-text__media"><img src="https://via.placeholder.com/1024x647?text=Pray4 Graphic" alt="" class="wp-image-128 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:heading -->',
                    '<h2>Pray4Colorado</h2>',
                    '<!-- /wp:heading -->',
                    '<!-- wp:paragraph -->',
                    '<p>Praying for a disciple making movement in Colorado.</p>',
                    '<!-- /wp:paragraph -->',
                    '<!-- wp:genesis-blocks/gb-button {"buttonText":"View Website"} -->',
                    '<div class="wp-block-genesis-blocks-gb-button gb-block-button"><a href="https://pray4colorado.org" class="gb-button gb-button-shape-rounded gb-button-size-medium" style="color:#ffffff;background-color:#3373dc">View Website</a></div>',
                    '<!-- /wp:genesis-blocks/gb-button --></div></div>',
                    '<!-- /wp:media-text -->',
                )
            ),
        )
    );


}
