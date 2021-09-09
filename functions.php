<?php
require_once( get_template_directory().'/functions/global-functions.php' );

require_once( get_template_directory().'/functions/common-functions.php' );

// Theme support options
require_once( get_template_directory().'/functions/default-theme-configuration/theme-support.php' );

// WP Head and other cleanup functions
require_once( get_template_directory().'/functions/default-theme-configuration/cleanup.php' );

// Register custom menus and menu walkers
require_once( get_template_directory().'/functions/default-theme-configuration/menu.php' );

// Register sidebars/widget areas
require_once( get_template_directory().'/functions/default-theme-configuration/sidebar.php' );

// Makes WordPress comments suck less
require_once( get_template_directory().'/functions/default-theme-configuration/comments.php' );

// Adds support for multiple languages
require_once( get_template_directory().'/functions/translation/translation.php' );

// Remove Emoji Support
require_once( get_template_directory().'/functions/default-theme-configuration/disable-emoji.php' );

// Related post function - no need to rely on plugins
require_once( get_template_directory().'/functions/default-theme-configuration/related-posts.php' );

// Customize the WordPress admin
require_once( get_template_directory().'/functions/admin/admin.php' );
require_once( get_template_directory().'/functions/admin/admin-page.php' );

// Custom Login
require_once( get_template_directory().'/functions/urls.php' );


require_once( get_template_directory().'/functions/post-type-news.php' );
require_once( get_template_directory().'/functions/post-type-user-documentation.php' );
require_once( get_template_directory().'/functions/post-type-dev-documentation.php' );
require_once( get_template_directory().'/functions/post-type-plugins.php' );

// Integrations
require_once( get_template_directory().'/functions/report-send-integration.php' );
require_once( get_template_directory().'/functions/site-link-post-type.php' );
Site_Link_System::instance();

// Register scripts and stylesheets
require_once( get_template_directory().'/functions/enqueue-scripts.php' );
require_once( get_template_directory().'/functions/rest-api.php' );
require_once( get_template_directory().'/functions/multi-role/multi-role.php' );

// Parsedown
require_once( get_template_directory().'/functions/parsedown/Parsedown.php' );

/**
 * GLOBAL FUNCTIONS
 */


function dtps_get_user_meta( $user_id = null ) {

    if ( ! is_user_logged_in() ) {
        return array();
    }
    if ( is_null( $user_id ) ) {
        $user_id = get_current_user_id();
    }
    return array_map( function ( $a ) { return maybe_unserialize( $a[0] );
    }, get_user_meta( $user_id ) );
}

function dtps_filter_meta( $meta ) {
    return array_map( function ( $a ) { return maybe_unserialize( $a[0] );
    }, $meta );
}
