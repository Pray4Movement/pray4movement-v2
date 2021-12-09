<?php

if ( !function_exists( "dt_cached_api_call")){
    function dt_cached_api_call( $url, $type = "GET", $args = [], $duration = HOUR_IN_SECONDS ){
        $data = get_transient( "dt_cached_" . esc_url( $url ) );
        if ( empty( $data ) ){
            if ( $type === "GET" ){
                $response = wp_remote_get( $url );
            } else {
                $response = wp_remote_post( $url, $args );
            }
            if ( is_wp_error( $response ) || isset( $response["response"]["code"] ) && $response["response"]["code"] !== 200 ){
                return $response;
            }
            $data = wp_remote_retrieve_body( $response );

            set_transient( "dt_cached_" .  esc_url( $url ), $data, $duration );
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


function p4m_map_shortcode( $atts ){

    DT_Mapbox_API::load_mapbox_header_scripts();

    wp_enqueue_style( 'p4m_map_styles',get_template_directory_uri() .  '/assets/css/map.css'  );
    // Map starter Script
    wp_enqueue_script( 'dt_mapbox_script',
        get_template_directory_uri() .  '/assets/js/maps_library.js',
        [
            'jquery',
            'lodash'
        ],
        filemtime( get_theme_file_path() .  '/assets/js/maps_library.js' ),
        true
    );
    wp_localize_script(
        'dt_mapbox_script', 'dt_mapbox_metrics', [
            'settings' => [
                'map_key' => DT_Mapbox_API::get_key(),
                'map_mirror' => dt_get_location_grid_mirror( true ),
                'menu_slug' => 'prayer_initiatives',
                'post_type' => 'prayer_initiatives',
                'title' => "Select a country",
                'geocoder_url' => trailingslashit( get_stylesheet_directory_uri() ),
                'geocoder_nonce' => wp_create_nonce( 'wp_rest' ),
                'rest_base_url' => "p4m/maps",
                'rest_url' => 'cluster_geojson',
                'totals_rest_url' => 'p4m-map-stats',
                'list_by_grid_rest_url' => 'p4m-map-stats-data',
                'points_rest_url' => 'points_geojson',

                'split_by' => [],
            ],
        ]
    );
    wp_enqueue_script( 'p4m_ramadan',
        get_template_directory_uri() .  '/assets/js/ramadan.js',
        [
            'jquery',
            'lodash',
            'dt_mapbox_script'
        ],
        filemtime( get_theme_file_path() .  '/assets/js/ramadan.js' ),
        true
    );
    wp_localize_script(
        'p4m_ramadan', 'p4m_ramadan', [
            'root' => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'data' => [
                'locations' => p4m_map_stats()
            ],
        ]
    );

    return "<div id='chart' style='max-width: 100%; height: 500px'></div>";

}
add_shortcode( "p4m-map", "p4m_map_shortcode" );


function p4m_map_stats_endpoints(){
    $namespace = 'p4m/maps';
    register_rest_route(
        $namespace, '/p4m-map-stats', [
            'methods'  => 'POST',
            'callback' => "p4m_map_stats",
            'permission_callback' => '__return_true'
        ]
    );
}
add_action( 'rest_api_init', 'p4m_map_stats_endpoints' );

function p4m_map_stats( WP_REST_Request $request = null ){

    $site_link_settings = get_option( "p4m_map_site_link_data", [] );
    if ( !empty( $site_link_settings ) ){
        $site_key = md5( $site_link_settings["token"] . $site_link_settings["site_1"] . $site_link_settings["site_2"] );
        $transfer_token = md5( $site_key . current_time( 'Y-m-dH', 1 ) );
        $args = [
            'method' => 'POST',
            'body' => [ "post_type" => "prayer_initiatives" ],
            'headers' => [
                'Authorization' => 'Bearer ' . $transfer_token,
            ],
        ];
        $response = dt_cached_api_call( "http://" . $site_link_settings["site_1"] . "/wp-json/dt-metrics/prayer-initiatives/get_grid_totals", "POST", $args, DAY_IN_SECONDS );
        return json_decode( $response, true );
    }
    return [];
}

function p4m_ramadan_campaign_list(){
    $initiative_locations = p4m_map_stats();
    $initiatives = [];
    foreach ( $initiative_locations as $location_id => $location_data ){
        foreach ( $location_data["initiatives"] as $initiative ){
            $initiatives[] = $initiative;
        }
    }

    ob_start();
    ?>
    <table>
        <thead>
            <tr>
                <th>Initiative</th>
                <th>Progress</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $initiatives as $initiative ) :
                $link = $initiative["campaign_link"] ?? $initiative["initiative_link"];
                if ( !empty($initiative["campaign_progress"]) && is_numeric( $initiative["campaign_progress"] )){
                    $initiative["campaign_progress"] .= '%';
                }

                ?>
            <tr>
                <?php if ( !empty($link) ) : ?>
                    <td><a target="_blank" href="<?php echo esc_html( $link ); ?>"> <?php echo esc_html( $initiative["label"] ); ?></a></td>
                <?php else : ?>
                    <td><?php echo esc_html( $initiative["label"] ); ?></td>
                <?php endif; ?>
                <td><?php echo esc_html( $initiative["campaign_progress"] ); ?></td>
            </tr>
            <?php endforeach;  ?>
        </tbody>
    </table>
    <?php

    return ob_get_clean();
}
add_shortcode( "p4m-ramadan-campaign-list", "p4m_ramadan_campaign_list" );
