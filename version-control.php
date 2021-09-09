<?php
/**
 * Special purpose file to deliver JSON response for DT Plugin Updaters.
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
if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) {
    $hash = sanitize_text_field( wp_unslash( $_GET['id'] ) );
} else {
    echo json_encode( [
        'status' => 'Error',
        'error_message' => 'Missing valid id.'
    ] );
    return;
}

// test hash against post_id's
$selected_plugin = [];
$list = [];
$results = $wpdb->get_results( "SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE post_id IN (SELECT ID FROM $wpdb->posts WHERE post_type = 'plugins' AND post_status = 'publish')", ARRAY_A );
if ( ! empty( $results ) ) {
    foreach ( $results as $item ) {
        if ( ! isset( $list[$item['post_id']] ) ) {
            $list[$item['post_id']] = [];
        }
        $list[$item['post_id']][$item['meta_key']] = $item['meta_value'];
    }
    foreach ( $list as $key => $value ) {
        if ( $hash === hash( 'SHA256', $key ) ) {
            $selected_plugin = $value;
            break;
        }
    }
}
if ( empty( $selected_plugin ) ) {
    echo json_encode( [
        'status' => 'Error',
        'error_message' => 'No plugin found with that id.'
    ] );
}

// load array
$data = [
    "name" => $selected_plugin['name'] ?? '',
    "version" => $selected_plugin['version'] ?? '',
    "download_url" => $selected_plugin['download_url'] ?? '',
    "sections" => [
        "description" => $selected_plugin['description'] ?? '',
        "installation" => $selected_plugin['installation'] ?? '',
        "changelog" => $selected_plugin['changelog'] ?? ''
    ],
    "last_updated" => $selected_plugin['last_updated'] ?? '',
    "upgrade_notice" => '',
    "requires" => $selected_plugin['requires'] ?? '',
    "tested" => $selected_plugin['tested'] ?? '',
    "banners" => [
        "low" => $selected_plugin['low'] ?? '',
        "high" => $selected_plugin['high'] ?? ''
    ],
    "author" => $selected_plugin['author'] ?? '',
    "author_homepage" => $selected_plugin['author_homepage'] ?? '',
    "homepage" => $selected_plugin['homepage'] ?? '',
    "issues_url" => $selected_plugin['issues_url'] ?? '',
    "projects_url" => $selected_plugin['projects_url'] ?? '',
    "wiki_url" => $selected_plugin['wiki_url'] ?? '',
    "license_url" => $selected_plugin['license_url'] ?? '',
    "readme_url" => $selected_plugin['readme_url'] ?? ''
];

// publish json
echo json_encode( $data );
