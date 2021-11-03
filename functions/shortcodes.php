<?php

if ( !function_exists( "dt_cached_api_call")){
    function dt_cached_api_call( $url ){
        $data = get_transient( "dt_cached_" . esc_url( $url ) );
        if ( empty( $data ) ){
            $url = "http://ramadandemo.p4m.local/wp-json/campaign_app/v1/24hour/campaign_info?action=get&parts%5Broot%5D=campaign_app&parts%5Btype%5D=24hour&parts%5Bpublic_key%5D=bb7569b7f3ac9a0a6e984eb357b853d0100b8666d5fae8b266f2a3d8564e0e03&parts%5Bmeta_key%5D=campaign_app_24hour_magic_key&parts%5Bpost_id%5D=69&parts%5Blang%5D=en_US";
            $response = wp_remote_get( $url );
            if ( is_wp_error( $response ) ){
                return $response;
            }
            $data = wp_remote_retrieve_body( $response );

            set_transient( "dt_cached_" .  esc_url( $url ), $data, HOUR_IN_SECONDS );
        }
        return $data;
    }
}

function shortcode_247_partner( $atts, $content = null ) {

    $coverage = '';
    if ( isset( $atts["url"] ) ){
        $atts["url"] = str_replace( "&amp;", "&", $atts["url"] );
        $campaign_data = dt_cached_api_call( $atts["url"] );
        if ( !is_wp_error( $campaign_data ) ){
            $campaign_data = json_decode( $campaign_data, true );
            if ( isset( $campaign_data["coverage_percentage"] ) ){
                $coverage = $campaign_data["coverage_percentage"] . "%";
            }
        }
    }
	return '<span>' . $content . $coverage . '</span>';
}
add_shortcode( '247-partner', 'shortcode_247_partner' );
