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
        'pray4movement/call-to-action',
        array(
            'title'         => esc_html__( 'Pray4Movement Single Page', 'pray4movement' ),
            'categories'    => array( 'pray4movement' ),
            'viewportWidth' => 1400,
            'content'       => implode(
                '',
                array(
                    '<!-- wp:group {"align":"wide","style":{"color":{"background":"#ffffff"}}} -->',
                    '<div class="wp-block-group alignwide has-background" style="background-color:#ffffff"><div class="wp-block-group__inner-container"><!-- wp:group -->',
                    '<div class="wp-block-group"><div class="wp-block-group__inner-container"><!-- wp:heading {"align":"center"} -->',
                    '<h2 class="has-text-align-center">' . esc_html__( 'Support the Museum and Get Exclusive Offers', 'pray4movement' ) . '</h2>',
                    '<!-- /wp:heading -->',
                    '<!-- wp:paragraph {"align":"center"} -->',
                    '<p class="has-text-align-center">' . esc_html__( 'Members get access to exclusive exhibits and sales. Our memberships cost $99.99 and are billed annually.', 'pray4movement' ) . '</p>',
                    '<!-- /wp:paragraph -->',
                    '<!-- wp:button {"align":"center","className":"is-style-outline"} -->',
                    '<div class="wp-block-button aligncenter is-style-outline"><a class="wp-block-button__link" href="#">' . esc_html__( 'Become a Member', 'pray4movement' ) . '</a></div>',
                    '<!-- /wp:button --></div></div>',
                    '<!-- /wp:group --></div></div>',
                    '<!-- /wp:group -->',
                )
            ),
        )
    );


}
