<?php
/**
 * Functions used in the Zúme implementation
 *
 * @since 0.1
 */

/* Require Authentication for Zúme */
function dtps_force_login() {
    // if user is not logged in redirect to login
    if ( ! is_user_logged_in() ) {
        wp_safe_redirect( dtps_login_url() );
        exit;
    }
}

// Remove admin bar on the front end.
if ( ! current_user_can( 'administrator' ) ) {
    add_filter( 'show_admin_bar', '__return_false' );
}


/**
 * Remove menu items for coaches in the admin dashboard.
 */
function dtps_custom_menu_page_removing() {

    if (is_admin() && current_user_can( 'coach' ) && !current_user_can( 'administrator' ) ) {

        remove_menu_page( 'index.php' );                  //Dashboard
        remove_menu_page( 'jetpack' );                    //Jetpack*
        remove_menu_page( 'edit.php' );                   //Posts
        remove_menu_page( 'upload.php' );                 //Media
        remove_menu_page( 'edit.php?post_type=page' );    //Pages
        remove_menu_page( 'edit.php?post_type=steplog' );    //Pages
        remove_menu_page( 'edit-comments.php' );          //Comments
        remove_menu_page( 'themes.php' );                 //Appearance
        remove_menu_page( 'plugins.php' );                //Plugins
    //    remove_menu_page( 'users.php' );                  //Users
        remove_menu_page( 'tools.php' );                  //Tools
        remove_menu_page( 'options-general.php' );        //Settings

    }
}
add_action( 'admin_menu', 'dtps_custom_menu_page_removing' );



function dtps_home_url( $current_language = null ) {
    return site_url();
}

// changing the logo link from wordpress.org to your site
function dtps_login_url() {  return site_url() . '/login'; }
add_filter( 'login_headerurl', 'dtps_login_url' );

function dtps_lostpassword_url( $current_language = null ) {
    return site_url() . '/login/?action=lostpassword';
}

function dtps_register_url( $current_language = null ) {
    return $url = site_url() . '/login/?action=register';
}

function dtps_profile_url() {
    return site_url( '/account' );
}

/**
 * Returns the full URI of the images folder with the ending slash, either as images/ or as images/sub_folder/.
 *
 * @param string $sub_folder
 * @return string
 */
function dtps_images_uri( $sub_folder = '' ) {
    $dtps_images_uri = site_url( '/wp-content/themes/disciple-tools-public-site/assets/images/' );
    if ( empty( $sub_folder ) ) {
        return $dtps_images_uri;
    } else {
        return $dtps_images_uri . $sub_folder . '/';
    }
}

function dtps_home_id() {
    return site_url();
}
