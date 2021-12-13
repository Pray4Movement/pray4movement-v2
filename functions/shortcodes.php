<?php

if ( !function_exists( "dt_cached_api_call")){
    function dt_cached_api_call( $url, $type = "GET", $args = [], $duration = HOUR_IN_SECONDS, $use_cache = true ){
        $data = get_transient( "dt_cached_" . esc_url( $url ) );
        if ( !$use_cache || empty( $data ) ){
            if ( $type === "GET" ){
                $response = wp_remote_get( $url );
            } else {
                $response = wp_remote_post( $url, $args );
            }
            if ( is_wp_error( $response ) || isset( $response["response"]["code"] ) && $response["response"]["code"] !== 200 ){
                return false;
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

    $type = "ramadan";
    if ( isset( $atts["type"])){
        $type = $atts["type"];
    }

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

                'totals_rest_url' => 'p4m-map-stats',
                'list_by_grid_rest_url' => 'p4m-map-stats-data',

            ],
        ]
    );
    $map = "ramadan.js";
    if ( $type === "usa-states" ){
        $map = "country-map.js";
    }
    wp_enqueue_script( 'p4m_ramadan',
        get_template_directory_uri() .  '/assets/js/' . $map,
        [
            'jquery',
            'lodash',
            'dt_mapbox_script'
        ],
        filemtime( get_theme_file_path() .  '/assets/js/'  . $map ),
        true
    );

    $map_data = [];
    $country_grid_ids =[];
    if ( $type === "ramadan" ){
        $map_data = p4m_map_stats_ramadan();
    } elseif ( $type === "world-networks" ){
        $map_data = p4m_map_stats_world_networks();
    } elseif ( $type === "usa-states" ){
        $map_data = p4m_map_stats_usa_states();
        $grid_response = Disciple_Tools_Mapping_Queries::get_children_by_grid_id( "100364199" );
        $country_grid_ids = $grid_response;
    }

    wp_localize_script(
        'p4m_ramadan', 'p4m_ramadan', [
            'root' => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'data' => [
                'locations' => $map_data,
                'country_grid_ids' => $country_grid_ids,
            ],
            "type" => $type
        ]
    );

    $return = "<div id='chart' style='max-width: 100%; height: 500px'></div>";
    if ( is_user_logged_in() ){
        $return .= "<div style='text-align: center'><button id='refresh_map_data'>refresh data</button></div>";
    }
    return $return;

}
add_shortcode( "p4m-map", "p4m_map_shortcode" );


function p4m_map_stats_endpoints(){
    $namespace = 'p4m/maps';
    register_rest_route(
        $namespace, '/p4m-map-stats', [
            'methods'  => 'POST',
            'callback' => "refresh_stats",
            'permission_callback' => '__return_true'
        ]
    );
}
add_action( 'rest_api_init', 'p4m_map_stats_endpoints' );

function refresh_stats(WP_REST_Request $request = null){
    $params = $request->get_params();
    $type = "ramadan";
    if ( isset( $params["type"])){
        $type = $params["type"];
    }
    if ( $type === "ramadan"){
        p4m_map_stats_ramadan( true );
    } elseif ( $type === "world-networks" ){
        p4m_map_stats_world_networks( true );
    } elseif ( $type === "usa-states" ){
        p4m_map_stats_usa_states( true );
    }
}

function p4m_map_stats_ramadan( $refresh = false ){

    $site_link_settings = get_option( "p4m_map_site_link_data", [] );
    if ( !empty( $site_link_settings ) ){
        $site_key = md5( $site_link_settings["token"] . $site_link_settings["site_1"] . $site_link_settings["site_2"] );
        $transfer_token = md5( $site_key . current_time( 'Y-m-dH', 1 ) );
        $args = [
            'method' => 'POST',
            'body' => [ "post_type" => "prayer_initiatives", "query" => [ 'initiative_type' => [ "247_campaign" ] ] ],
            'headers' => [
                'Authorization' => 'Bearer ' . $transfer_token,
            ],
        ];
        $refresh = WP_DEBUG || $refresh;
        $response = dt_cached_api_call( "http://" . $site_link_settings["site_1"] . "/wp-json/dt-metrics/prayer-initiatives/get_grid_totals", "POST", $args, DAY_IN_SECONDS, !$refresh );
        return json_decode( $response, true );
    }
    return [];
}

function p4m_map_stats_world_networks(  $refresh = false ){
    $site_link_settings = get_option( "p4m_map_site_link_data", [] );
    if ( !empty( $site_link_settings ) ){
        $site_key = md5( $site_link_settings["token"] . $site_link_settings["site_1"] . $site_link_settings["site_2"] );
        $transfer_token = md5( $site_key . current_time( 'Y-m-dH', 1 ) );
        $args = [
            'method' => 'POST',
            'body' => [ "post_type" => "prayer_initiatives", "query" => [ 'initiative_type' => [ "ongoing" ] ] ],
            'headers' => [
                'Authorization' => 'Bearer ' . $transfer_token,
            ],
        ];
        $refresh = WP_DEBUG || $refresh;
        $response = dt_cached_api_call( "http://" . $site_link_settings["site_1"] . "/wp-json/dt-metrics/prayer-initiatives/get_grid_totals?type=ongoing", "POST", $args, DAY_IN_SECONDS, !$refresh );
        return json_decode( $response, true );
    }
    return [];
}

function p4m_map_stats_usa_states( $refresh = false ){
    $site_link_settings = get_option( "p4m_map_site_link_data", [] );
    if ( !empty( $site_link_settings ) ){
        $site_key = md5( $site_link_settings["token"] . $site_link_settings["site_1"] . $site_link_settings["site_2"] );
        $transfer_token = md5( $site_key . current_time( 'Y-m-dH', 1 ) );
        $args = [
            'method' => 'POST',
            'body' => [ "post_type" => "prayer_initiatives", "query" => [ 'initiative_type' => [ "ongoing" ], 'location_grid' => [ "100364199" ] ] ],
            'headers' => [
                'Authorization' => 'Bearer ' . $transfer_token,
            ],
        ];
        $refresh = WP_DEBUG || $refresh;
        $response = dt_cached_api_call( "http://" . $site_link_settings["site_1"] . "/wp-json/dt-metrics/prayer-initiatives/get_country_grid_totals?type=ongoing", "POST", $args, DAY_IN_SECONDS, !$refresh );
        return json_decode( $response, true );
    }
    return [];
}

function p4m_ramadan_campaign_list(){
    $initiative_locations = p4m_map_stats_ramadan();
    $initiatives = [];
    foreach ( $initiative_locations as $location_id => $location_data ){
        foreach ( $location_data["initiatives"] as $initiative ){
            if ( !isset( $initiatives[$initiative["initiative_id"]])){
                $initiatives[$initiative["initiative_id"]] = $initiative;
            } else {
                $initiatives[$initiative["initiative_id"]]["location"] .= ( ", " . $initiative["location"] );
            }
        }
    }

    ob_start();
    ?>
    <table>
        <thead>
            <tr>
                <th>Initiative</th>
                <th>Focus</th>
                <th>Progress</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $initiatives as $initiative ) :
                $link = !empty( $initiative["campaign_link"] ) ? $initiative["campaign_link"] : $initiative["initiative_link"];
                $background_color = "white";
                if ( !empty( $initiative["campaign_progress"] ) && is_numeric( $initiative["campaign_progress"] ) ){
                    if ( $initiative["campaign_progress"] > 0){
                        $background_color = "#FFCCCDFF";
                    }
                    $initiative["campaign_progress"] .= '%';
                }
                if ( empty( $initiative["campaign_progress"] ) && $initiative["status"] === "forming" ){
                    $background_color = "#FFC84959";
                    $initiative["campaign_progress"] = "Setup in progress";
                }
                if ( empty( $initiative["campaign_progress"] ) && $initiative["status"] === "active" ){
                    $initiative["campaign_progress"] = "0%";
                }
                ?>
            <tr style="background-color: <?php echo esc_html( $background_color ); ?>">
                <?php if ( !empty($link) ) : ?>
                    <td><a target="_blank" href="<?php echo esc_html( $link ); ?>"> <?php echo esc_html( $initiative["label"] ); ?></a></td>
                <?php else : ?>
                    <td><?php echo esc_html( $initiative["label"] ); ?></td>
                <?php endif; ?>
                <td>
                    <?php
                        if ( !empty( $initiative["people_group"] ) ){
                            echo esc_html( $initiative["people_group"] );
                        } else if ( !empty( $initiative["location"] ) ){
                            echo esc_html( $initiative["location"] );
                        } else{
                            echo esc_html( $initiative["label"] );
                        }
                    ?>
                </td>
                <td><?php echo esc_html( $initiative["campaign_progress"] ); ?></td>
            </tr>
            <?php endforeach;  ?>
        </tbody>
    </table>
    <?php

    return ob_get_clean();
}
add_shortcode( "p4m-ramadan-campaign-list", "p4m_ramadan_campaign_list" );

function pm4_initiatives_list( $atts ){
    $initiative_locations = p4m_map_stats_world_networks();
    $initiatives = [];
    foreach ( $initiative_locations as $location_id => $location_data ){
        foreach ( $location_data["initiatives"] as $initiative ){
            if ( !isset( $initiatives[$initiative["initiative_id"]])){
                $initiatives[$initiative["initiative_id"]] = $initiative;
            } else {
                $initiatives[$initiative["initiative_id"]]["location"] .= ( ", " . $initiative["location"] );
            }
        }
    }

    ob_start();
    ?>
    <table>
        <thead>
        <tr>
            <th>Initiative</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ( $initiatives as $initiative ) :
            $link = !empty( $initiative["initiative_link"] ) ? $initiative["initiative_link"] : "";
            ?>
            <tr>
                <?php if ( !empty($link) ) : ?>
                    <td><a target="_blank" href="<?php echo esc_html( $link ); ?>"> <?php echo esc_html( $initiative["label"] ); ?></a></td>
                <?php else : ?>
                    <td><?php echo esc_html( $initiative["label"] ); ?></td>
                <?php endif; ?>

            </tr>
        <?php endforeach;  ?>
        </tbody>
    </table>
    <?php

    return ob_get_clean();
}

add_shortcode( "p4m-initiatives-list", "pm4_initiatives_list" );