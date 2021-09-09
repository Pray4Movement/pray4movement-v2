<?php

add_action( 'init', 'dt_register_shortcodes');
function dt_register_shortcodes(){
    add_shortcode( 'dt-load-github-md', 'DT_load_github_markdown' );
    add_shortcode( 'dt-load-github-release-md', 'DT_load_github_release_markdown' );
}

function DT_load_github_markdown( $atts ){
    $url = null;
    extract(shortcode_atts(array(
      'url' => null,
   ), $atts));


    if ( $url ) { /* If readme url is present, then the Readme markdown is used */
        $string = file_get_contents( $url );
    }
    // end check on readme existence
    if ( !empty( $string )) {
        $Parsedown = new Parsedown();
        echo $Parsedown->text( $string );
    }

}

function DT_load_github_release_markdown( $atts ){
    $repo = null;
    $tag = null;
    extract(shortcode_atts(array(
      'repo' => null,
      'tag' => null
   ), $atts) );

    if ( empty( $repo ) || empty( $tag ) ){
        return false;
    }

    $url = "https://api.github.com/repos/" . esc_attr( $repo ) . "/releases/tags/" . esc_attr( $tag );
    $response = wp_remote_get( $url );

    $data_result = wp_remote_retrieve_body( $response );

    if ( ! $data_result ) {
        return false;
    }
    $release = json_decode( $data_result, true );

    // end check on readme existence
    if ( !empty( $release["body"] )) {
        ob_start();
        $Parsedown = new Parsedown();
        echo $Parsedown->text( $release["body"] );
        return ob_get_clean();
    }
}
