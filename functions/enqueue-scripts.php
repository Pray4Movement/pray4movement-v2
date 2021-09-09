<?php
/**
 * Load scripts
 *
 * @param string $handle
 * @param string $rel_src
 * @param array  $deps
 * @param bool   $in_footer
 */
function dtps_enqueue_script( $handle, $rel_src, $deps = array(), $in_footer = false ) {
    if ( $rel_src[0] === "/" ) {
        throw new Error( "dtps_enqueue_script took \$rel_src argument which unexpectedly started with /" );
    }
    wp_enqueue_script( $handle, get_template_directory_uri() . "/$rel_src", $deps, filemtime( get_template_directory() . "/$rel_src" ), $in_footer );
}

/**
 * Load styles
 *
 * @param string $handle
 * @param string $rel_src
 * @param array  $deps
 * @param string $media
 */
function dtps_enqueue_style( $handle, $rel_src, $deps = array(), $media = 'all' ) {
    if ( $rel_src[0] === "/" ) {
        throw new Error( "dtps_enqueue_style took \$rel_src argument which unexpectedly started with /" );
    }
    wp_enqueue_style( $handle, get_template_directory_uri() . "/$rel_src", $deps, filemtime( get_template_directory() . "/$rel_src" ), $media );
}

function site_scripts() {
    global $wp_styles; // Call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way
    $dtps_user = wp_get_current_user();
    $dtps_user_meta = dtps_get_user_meta( $dtps_user->ID );

    // main minimized scripts for loaded on all pages
    wp_enqueue_script( 'site-js', get_template_directory_uri() . '/assets/scripts/scripts.js', array( 'jquery', 'lodash', 'wp-i18n' ), filemtime( get_template_directory() . '/assets/scripts/scripts.js' ), true );

    wp_register_script( 'dtps-core', get_template_directory_uri() . '/assets/scripts/api.js', array( 'jquery', 'lodash', 'wp-i18n' ), filemtime( get_theme_file_path() . '/assets/scripts/api.js' ), true );
    wp_enqueue_script( 'dtps-core' );
    wp_localize_script(
        "dtps-core", "dtpsCore", array(
            'root' => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'theme_uri' => get_stylesheet_directory_uri(),
            'logged_in' => is_user_logged_in(),
        )
    );

    // lodash load
    wp_register_script( 'lodash', 'https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js', false, '4.17.11' );
    wp_enqueue_script( 'lodash' );

    // main stylesheet
    wp_enqueue_style( 'site-css', get_template_directory_uri() . '/assets/styles/style.css', array(), filemtime( get_template_directory() . '/assets/styles/scss' ), 'all' );
    wp_style_add_data( 'site-css', 'rtl', 'replace' );

    // script for threaded comments
    if ( is_singular() && comments_open() && ( get_option( 'thread_comments' ) == 1 )) {
        wp_enqueue_script( 'comment-reply' );
    }

    // foundation styles
    wp_enqueue_style( 'foundations-icons', get_template_directory_uri() .'/assets/styles/foundation-icons/foundation-icons.css', array(), '3' );


    /**
     * Profile Page
     */
    if ( 'template-account.php' === basename( get_page_template() ) ) {
        wp_register_script( 'lodash', 'https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js', false, '4.17.11' );
        wp_enqueue_script( 'lodash' );

        wp_enqueue_script( 'dtps-profile', get_template_directory_uri() . '/assets/scripts/profile.js', array( 'jquery', 'lodash', 'wp-i18n', 'dtps-core' ), filemtime( get_theme_file_path() . '/assets/scripts/profile.js' ), true );
        wp_localize_script(
            "dtps-profile", "dtpsProfile", array(
                'root' => esc_url_raw( rest_url() ),
                'theme_uri' => get_stylesheet_directory_uri(),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'current_user_id' => get_current_user_id(),
                'user_profile_fields' => array(
                    'id' => $dtps_user->data->ID,
                    'name' => $dtps_user_meta['dtps_full_name'] ?? '',
                    'email' => $dtps_user->data->user_email,
                    'phone' => $dtps_user_meta['dtps_phone_number'] ?? '',
                    'location_grid_meta' => maybe_unserialize( $dtps_user_meta['location_grid_meta'] ) ?? '',
                    'affiliation_key' => $dtps_user_meta['dtps_affiliation_key'] ?? '',
                    'facebook_sso_email' => $dtps_user_meta['facebook_sso_email'] ?? false,
                    'google_sso_email' => $dtps_user_meta['google_sso_email'] ?? false,
                ),
                'logged_in' => is_user_logged_in(),
            )
        );
    }

    if ( 'template-join.php' === basename( get_page_template() ) ) {
        wp_register_script( 'lodash', 'https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js', false, '4.17.11' );
        wp_enqueue_script( 'lodash' );

        wp_enqueue_script( 'dtps-join', get_template_directory_uri() . '/assets/scripts/profile.js', array( 'jquery', 'lodash', 'wp-i18n', 'dtps-core' ), filemtime( get_theme_file_path() . '/assets/scripts/profile.js' ), true );
        wp_localize_script(
            "dtps-join", "dtpsJoin", array(
                'root' => esc_url_raw( rest_url() ),
                'theme_uri' => get_stylesheet_directory_uri(),
                'nonce' => wp_create_nonce( 'wp_rest' ),
            )
        );
    }

    if ( 'plugins' === dt_public_site_get_url_path() ) {
        wp_register_script( 'listjs', '//cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js', array( 'jquery' ), '2.3.1', true  );
        wp_enqueue_script( 'listjs' );
    }


}
add_action( 'wp_enqueue_scripts', 'site_scripts', 999 );

function dt_public_site_get_url_path() {
    if ( isset( $_SERVER["HTTP_HOST"] ) ) {
        $url  = ( !isset( $_SERVER["HTTPS"] ) || @( $_SERVER["HTTPS"] != 'on' ) ) ? 'http://'. sanitize_text_field( wp_unslash( $_SERVER["HTTP_HOST"] ) ) : 'https://'. sanitize_text_field( wp_unslash( $_SERVER["HTTP_HOST"] ) );
        if ( isset( $_SERVER["REQUEST_URI"] ) ) {
            $url .= sanitize_text_field( wp_unslash( $_SERVER["REQUEST_URI"] ) );
        }
        return trim( str_replace( get_site_url(), "", $url ), '/' );
    }
    return '';
}


function dtps_login_css() {
    dtps_enqueue_style( 'dtps_login_css', 'assets/styles/login.css', array() );
}
add_action( 'login_enqueue_scripts', 'dtps_login_css', 999 );


// calling it only on the login page
add_action( 'login_enqueue_scripts', 'dtps_login_css', 10 );

