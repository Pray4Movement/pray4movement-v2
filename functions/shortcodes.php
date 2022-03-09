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

    $small = isset( $atts["size"] );

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
                'menu_slug' => $type . ( $small ? "-small" : '' ),
                'post_type' => 'prayer_initiatives',
                'title' => "Click a country",
                'geocoder_url' => trailingslashit( get_stylesheet_directory_uri() ),
                'geocoder_nonce' => wp_create_nonce( 'wp_rest' ),
                'rest_base_url' => "p4m/maps",

                'totals_rest_url' => 'p4m-map-stats',
                'list_by_grid_rest_url' => 'p4m-map-stats-data',
                "small" => $small

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
        $grid_response = array_merge( $grid_response, Disciple_Tools_Mapping_Queries::get_children_by_grid_id( "100041471" ) );
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
            "type" => $type,
            "small" => $small
        ]
    );

    $return = "<div id='chart' style='max-width: 100%;'></div>";
    if ( is_user_logged_in() && !$small){
        $return .= "<div style='text-align: right'><button id='refresh_map_data' style='background-color: white; color: #dc3822; text-transform: lowercase;'>refresh data</button></div>";
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
    return true;
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
        $response = dt_cached_api_call( "http://" . $site_link_settings["site_1"] . "/wp-json/dt-metrics/prayer-initiatives/get_grid_totals", "POST", $args, HOUR_IN_SECONDS, !$refresh );
        return json_decode( $response, true ) ?? [];
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
            'body' => [ "post_type" => "prayer_initiatives", "query" => [ 'initiative_type' => [ "ongoing" ], 'location_grid' => [ "100364199", "100041471" ] ] ],
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

function p4m_ramadan_campaign_list( $args ){
    $initiative_locations = p4m_map_stats_ramadan();
    $initiatives = [];
    $total_percent = 0;
    foreach ( $initiative_locations as $location_id => $location_data ){
        foreach ( $location_data["initiatives"] as $initiative ){
            if ( !isset( $initiatives[$initiative["initiative_id"]])){
                $initiatives[$initiative["initiative_id"]] = $initiative;
            } else {
                $initiatives[$initiative["initiative_id"]]["location"] .= ( ", " . $initiative["location"] );
            }
            $total_percent += (int) $initiative["campaign_progress"];
        }
    }


    $goal_progress = round( $total_percent / sizeof( $initiatives ), 2 );



    $sort = "country_name";
    if ( isset( $_GET["sort_table"] ) ) {
        $sort = sanitize_text_field( wp_unslash( $_GET["sort_table"] ) );
    }

    uasort( $initiatives, function ( $a, $b ) use ( $sort ){
        return $a[$sort] <=> $b[$sort];
    });
    if ( $sort === "campaign_progress" ){
        $initiatives = array_reverse( $initiatives );
    }

    $with_progress = 0;
    $active = 0;
    $setup_in_progress = 0;
    $time_committed = 0;
    foreach ( $initiatives as $initiative ){
        if ( isset( $initiative["minutes_committed"] ) ){
            $time_committed += (int) $initiative["minutes_committed"];
        }

        if ( !empty( $initiative["campaign_progress"] ) && is_numeric( $initiative["campaign_progress"] ) && $initiative["campaign_progress"] > 0 ){
            $with_progress++;
        } else if ( $initiative["status"] === "active") {
            $active++;
        } else {
            $setup_in_progress++;
        }
    }
    $days_committed = round( $time_committed / 60 / 24, 2) % 365;
    $years_committed = floor( $time_committed / 60 / 24 / 365 ) ;

    ob_start();
    ?>
    <style>
        .sort-button {
            padding: 5px 7px;
            border-radius: 5px;
            background-color: transparent;
            color: black;
            text-transform: none;
        }
        .ramadan-stats {
            display: flex; flex-direction: row;
            justify-content: space-around;
        }
        .ramadan-stats div {
            flex-basis: 33%;
        }
        .ramadan-stats .stats-title {
            text-transform: uppercase;
            color: #dc3822;
            font-size: 3rem;
        }
        .ramadan-stats .stats-title h4 {
            margin: 10px 0;
        }
        .ramadan-stats .stats-content {
            font-weight: bold;
        }
        .ramadan-stats div div {
            text-align: center;
        }
        @media (max-width: 782px) {
           .ramadan-stats {
               flex-direction: column;
           }
        }
    </style>
    <!-- CAMPAIGNS STATUS: START -->
    <div class="ramadan-stats">
        <div>
            <div class="stats-title"><h4>Active 24/7 campaigns</h4></div>
            <div class="stats-content"><?php echo esc_html( $active + $with_progress ); ?><br> goal: 100+</div>
        </div>
        <div>
            <div class="stats-title"><h4>100% Coverage Status</h4></div>
            <div class="stats-content"><?php echo esc_html( $goal_progress ); ?>%</div>
        </div>

        <div>
            <div class="stats-title"><h4>Total Time Committed</h4></div>
            <div class="stats-content">
                <?php if ( !empty( $years_committed ) ) :
                    echo esc_html( $years_committed . " years <br>" );
                endif;
                echo esc_html( $days_committed ); ?> days
            </div>
        </div>
    </div>
    <!-- CAMPAIGNS STATUS: END -->

    <table id="campaigns-list">
        <thead>
            <tr>
                <th style="width:60px"></th>
                <th><form action="#campaigns-list"><button class="sort-button" name="sort_table" value="label">Campaign <span style="color:#dc3822">&#9650;</span></button></form></th>
                <th><form action="#campaigns-list"><button class="sort-button" name="sort_table" value="country_name">Focus <span style="color:#dc3822">&#9650;</span></button></form></th>
                <th><form action="#campaigns-list"><button class="sort-button" name="sort_table" value="campaign_progress">Progress <span style="color:#dc3822">&#9660;</span></button></form></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $row_index = 0;
            foreach ( $initiatives as $initiative ) :
                $row_index++;
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
                <td>
                    <?php echo esc_html( $row_index ); ?>.
                </td>
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
                    } else if ( $initiative["country"] === "other" ){
                        echo "World/other";
                    } else {
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
            <th>Focus</th>
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

            </tr>
        <?php endforeach;  ?>
        </tbody>
    </table>
    <?php

    return ob_get_clean();
}

add_shortcode( "p4m-initiatives-list", "pm4_initiatives_list" );