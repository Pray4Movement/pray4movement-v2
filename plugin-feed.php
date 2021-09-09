<?php
/**
 * Special purpose file to deliver JSON response for all active DT Plugins.
 */

if ( defined( 'ABSPATH' )) {
    exit;
} // Exit if accessed directly.


define( 'DOING_AJAX', true );

// Tell WordPress to only load the basics
define( 'SHORTINIT', 1 );

// Setup
if ( ! isset( $_SERVER['DOCUMENT_ROOT'] ) ) {
    exit( 'missing server info' );
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php'; //@phpcs:ignore

// set header type
header( 'Content-type: application/json' );

// test id exists
global $wpdb;

// test hash against post_id's
$selected_plugin = [];
$list = [];
$results = $wpdb->get_results( "SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE post_id IN (SELECT ID FROM $wpdb->posts WHERE post_type = 'plugins' AND post_status = 'publish')", ARRAY_A );
$data = [];
$relevant_fields = [
    "name",
    "version",
    "download_url",
    "description",
    "installation",
    "changelog",
    "last_updated",
    "upgrade_notice",
    "requires",
    "tested",
    "low",
    "high",
    "author",
    "author_homepage",
    "homepage",
    "issues_url",
    "projects_url",
    "wiki_url",
    "license_url",
    "readme_url",
];

// Populate $list array with all available data
if ( ! empty( $results ) ) {
    foreach ( $results as $item ) {
        if ( ! isset( $list[$item['post_id']] ) ) {
            $list[$item['post_id']] = [];
        }
        $list[$item['post_id']][$item['meta_key']] = $item['meta_value'];
    }

    // Filter relevant fields for output
    foreach ( $list as $key => $values ) {
        foreach ( $values as $k => $v ) {
            if ( in_array( $k, $relevant_fields ) ) {
                $data[$key][$k] = $v;
            }
        }
    }
}



// publish json
echo json_encode( array_values( $data ) );
