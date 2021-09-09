<?php
// SIDEBARS AND WIDGETIZED AREAS
function dtps_register_sidebars() {
//    register_sidebar(array(
//        'id' => 'sidebar1',
//        'name' => __( 'Sidebar 1', 'dtps' ),
//        'description' => __( 'The first (primary) sidebar.', 'dtps' ),
//        'before_widget' => '<div id="%1$s" class="widget %2$s">',
//        'after_widget' => '</div>',
//        'before_title' => '<h4 class="widgettitle">',
//        'after_title' => '</h4>',
//    ));
//
//    register_sidebar(array(
//        'id' => 'sidebar2',
//        'name' => __( 'Sidebar 2', 'dtps' ),
//        'description' => __( 'Sidebar for single posts.', 'dtps' ),
//        'before_widget' => '<div id="%1$s" class="widget %2$s">',
//        'after_widget' => '</div>',
//        'before_title' => '<h4 class="widgettitle">',
//        'after_title' => '</h4>',
//    ));

//    register_sidebar(array(
//        'id' => 'playbook',
//        'name' => __( 'Playbook', 'dtps' ),
//        'description' => __( 'Sidebar for playbook', 'dtps' ),
//        'before_widget' => '<div id="%1$s" class="widget %2$s">',
//        'after_widget' => '</div>',
//        'before_title' => '<h4 class="widgettitle">',
//        'after_title' => '</h4>',
//    ));
    register_sidebar(array(
        'id' => 'report',
        'name' => __( 'News', 'dtps' ),
        'description' => __( 'Sidebar for news', 'dtps' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));

//    register_sidebar(array(
//        'id' => 'offcanvas',
//        'name' => __( 'Offcanvas', 'dtps' ),
//        'description' => __( 'The offcanvas sidebar.', 'dtps' ),
//        'before_widget' => '<div id="%1$s" class="widget %2$s">',
//        'after_widget' => '</div>',
//        'before_title' => '<h4 class="widgettitle">',
//        'after_title' => '</h4>',
//    ));
} /* end register sidebars */

add_action( 'widgets_init', 'dtps_register_sidebars' );
