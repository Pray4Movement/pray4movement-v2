<?php

if ( !function_exists( "dt_cached_api_call" ) ){
    function dt_cached_api_call( $url, $type = "GET", $args = [], $duration = HOUR_IN_SECONDS, $use_cache = true ){
        $data = get_transient( "dt_cached_" . esc_url( $url ) );
        if ( !$use_cache || empty( $data ) ){
            if ( $type === "GET" ){
                $response = wp_remote_get( $url, $args );
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
if ( ! function_exists( 'dt_recursive_sanitize_array' ) ) {
    function dt_recursive_sanitize_array( array $array ) : array {
        foreach ( $array as $key => &$value ) {
            if ( is_array( $value ) ) {
                $value = dt_recursive_sanitize_array( $value );
            }
            else {
                $value = sanitize_text_field( wp_unslash( $value ) );
            }
        }
        return $array;
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
    if ( isset( $atts["type"] ) ){
        $type = $atts["type"];
    }

    DT_Mapbox_API::load_mapbox_header_scripts();

    wp_enqueue_style( 'p4m_map_styles', get_template_directory_uri() . '/assets/css/map.css' );
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

                'totals_rest_url' => 'p4m-refresh-stats',
                'list_by_grid_rest_url' => 'p4m-refresh-stats-data',
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
    if ( is_user_logged_in() && !$small ){
        $return .= "<div style='text-align: right'><button id='refresh_map_data' style='background-color: white; color: #dc3822; text-transform: lowercase;'>refresh data</button></div>";
    }
    return $return;

}
add_shortcode( "p4m-map", "p4m_map_shortcode" );


function p4m_map_stats_endpoints(){
    $namespace = 'p4m/maps';
    register_rest_route(
        $namespace, '/p4m-refresh-stats', [
            'methods'  => 'POST',
            'callback' => "refresh_stats",
            'permission_callback' => function(){
                return is_user_logged_in();
            }
        ]
    );
    register_rest_route(
        $namespace, '/p4m-campaigns-refresh-stats', [
            'methods'  => 'POST',
            'callback' => "refresh_campaigns",
            'permission_callback' => function(){
                return is_user_logged_in();
            }
        ]
    );
    register_rest_route(
        $namespace, '/p4m-stats', [
            'methods'  => 'GET',
            'callback' => "p4m_stats",
            'permission_callback' => '__return_true'
        ]
    );
}
add_action( 'rest_api_init', 'p4m_map_stats_endpoints' );


function refresh_campaigns(){
    p4m_get_all_campaigns( true );
    return true;
}
function refresh_stats( WP_REST_Request $request = null ){
    $params = $request->get_params();
    $type = "ramadan";
    if ( isset( $params["type"] ) ){
        $type = $params["type"];
    }
    if ( $type === "ramadan" ){
        p4m_map_stats_ramadan( true );
    } elseif ( $type === "world-networks" ){
        p4m_map_stats_world_networks( true );
    } elseif ( $type === "usa-states" ){
        p4m_map_stats_usa_states( true );
    }
    return true;
}
function p4m_stats( WP_REST_Request $request = null ){
    $type = "ramadan";
    if ( $type === "ramadan" ){
        $initiative_locations = p4m_map_stats_ramadan( false );
        $active_campaigns = 0;
        $total_prayer_time_minutes = 0;
        $countries_prayed_for = [];
        $number_of_prayers = 0;

        $initiatives = [];
        foreach ( $initiative_locations as $location_id => $location_data ){
            if ( !empty( $location_data["name"] ) ){
                $countries_prayed_for[] = $location_data["name"];
            }
            foreach ( $location_data["initiatives"] as $initiative ){
                if ( !isset( $initiatives[$initiative["initiative_id"]] ) ){
                    $initiatives[$initiative["initiative_id"]] = $initiative;
                } else {
                    $initiatives[$initiative["initiative_id"]]["location"] .= ( ", " . $initiative["location"] );
                }
            }
        }
        foreach ( $initiatives as $initiative ){
            if ( $initiative["status"] === "active" ){
                $total_prayer_time_minutes += $initiative["minutes_committed"] ?? 0;
                $active_campaigns++;
                if ( !empty( $initiative["prayers_count"] ) ){
                    $number_of_prayers += $initiative["prayers_count"];
                }
            }
        }
        return [
            "campaigns" => $active_campaigns,
            "minutes_prayed" => $total_prayer_time_minutes,
            "countries_prayed_for" => sizeof( array_unique( $countries_prayed_for ) ),
            "prayers_count" => $number_of_prayers,
        ];
    }
    return true;
}

function p4m_map_stats_ramadan( $refresh = false ){
    $site_link = p4m_get_site_link();

    if ( !empty( $site_link['token'] ) && !empty( $site_link['url'] ) ){
        $args = [
            'method' => 'POST',
            'body' => [ "post_type" => "prayer_initiatives", "query" => [ 'initiative_type' => [ "247_campaign" ] ] ],
            'headers' => [
                'Authorization' => 'Bearer ' . $site_link['token'],
            ],
        ];
        $refresh = WP_DEBUG || $refresh;
        $response = dt_cached_api_call( "https://" . $site_link['url'] . "/wp-json/dt-metrics/prayer-initiatives/get_grid_totals", "POST", $args, MINUTE_IN_SECONDS * 5, !$refresh );
        return json_decode( $response, true ) ?? [];
    }
    return [];
}

function p4m_map_stats_world_networks( $refresh = false ){
    $site_link = p4m_get_site_link();

    if ( !empty( $site_link['token'] ) && !empty( $site_link['url'] ) ){
        $args = [
            'method' => 'POST',
            'body' => [ "post_type" => "prayer_initiatives", "query" => [ 'initiative_type' => [ "ongoing" ] ] ],
            'headers' => [
                'Authorization' => 'Bearer ' . $site_link['token'],
            ],
        ];
        $refresh = WP_DEBUG || $refresh;
        $response = dt_cached_api_call( "https://" . $site_link['url'] . "/wp-json/dt-metrics/prayer-initiatives/get_grid_totals?type=ongoing", "POST", $args, DAY_IN_SECONDS, !$refresh );
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

function p4m_ramadan_campaign_list( $atts ){
    $campaigns = p4m_get_all_campaigns();
    $campaigns = filter_campaigns( $campaigns, $atts );

    $total_percent = 0;
    $time_committed = 0;
    $intercessors = 0;
    $countries_prayed_for = [];
    $time_slots = 0;
    foreach ( $campaigns as &$c ){
        $c['focus'] = $c['people_group'];
        $campaign_locations = '';
        foreach ( $c['location_grid'] ?? [] as $location ){
            if ( !empty( $campaign_locations ) ){
                $campaign_locations .= ', ';
            }
            $campaign_locations .= $location['matched_search'] ?? $location['label'];
        }
        if ( empty( $c['focus'] ) ){
            $c['focus'] = $campaign_locations;
        }
        $total_percent += (int)$c['campaign_progress'];
        $time_committed += $c['minutes_committed'];
        $intercessors += $c['prayers_count'];
        foreach( $c['location_grid'] ?? [] as $location ){
            if ( !in_array( $location['country_name'], $countries_prayed_for ) ){
                $countries_prayed_for[] = $location['country_name'];
            }
        }
//        $time_slots += $c['time_slots_covered'];
        $time_slots += $c['minutes_committed'] / 15;
    }
    $goal_progress = sizeof( $campaigns ) > 0 ? round( $total_percent / sizeof( $campaigns ), 2 ) : 0;


    $sort = 'campaign_progress';
    if ( isset( $_GET['sort_table'] ) ){
        $sort = sanitize_text_field( wp_unslash( $_GET['sort_table'] ) );
    }

    uasort( $campaigns, function ( $a, $b ) use ( $sort ){
        return $a[$sort] <=> $b[$sort];
    } );
    if ( $sort === 'campaign_progress' || $sort === 'minutes_committed' ){
        $campaigns = array_reverse( $campaigns );
    }

    ob_start();
    ?>
    <!-- CAMPAIGNS STATUS: START -->
    <div class='campaigns-stats'>
        <div>
            <div class='stats-title'><h4>Campaigns</h4></div>
            <div class='stats-content'>
                <?php echo esc_html( sizeof( $campaigns ) ); ?> for <?php echo esc_html( sizeof( $countries_prayed_for ) ); ?> countries
            </div>
        </div>
        <div>
            <div class='stats-title'><h4>Intercessors</h4></div>
            <div class='stats-content'><?php echo esc_html( number_format( $intercessors ) ); ?></div>
        </div>
    </div>
    <div class='campaigns-stats'>
        <div>
            <div class='stats-title'><h4>15 Minute Time Slots Filled</h4></div>
            <div class='stats-content'><?php echo esc_html( number_format( $time_slots ) ); ?></div>
        </div>
        <div>
            <div class="stats-title"><h4>Total Time Committed</h4></div>
            <div class="stats-content">
                <span class="p4m-carousel">
                    <?php echo esc_html( p4m_display_minutes( $time_committed ) ); ?>
                </span>
            </div>
        </div>
    </div>
    <!-- CAMPAIGNS STATUS: END -->
    <div class="campaign-list-wrapper">
        <table id="campaigns-list" style="overflow-x:scroll">
            <thead>
            <tr>
                <th style="width:60px" class="hide-mobile"></th>
                <th>
                    <form action="#campaigns-list">
                        <button class="sort-button" name="sort_table" value="label">Campaign <span
                                style="color:#dc3822">&#9650;</span></button>
                    </form>
                </th>
                <th>
                    <form action="#campaigns-list">
                        <button class="sort-button" name="sort_table" value="focus">Focus <span style="color:#dc3822">&#9650;</span>
                        </button>
                    </form>
                </th>
                <th style="min-width: 66px">
                    <form action="#campaigns-list">
                        <button class="sort-button" name="sort_table" value="campaign_progress"><span
                                class="hide-mobile">Coverage</span><span class="show-mobile">%</span> <span
                                style="color:#dc3822">&#9660;</span></button>
                    </form>
                </th>
                <th style="min-width: 66px">
                    <form action="#campaigns-list">
                        <button class="sort-button" name="sort_table" value="minutes_committed">
                            <span class="hide-mobile">Time Committed</span><span class="show-mobile">Committed</span>
                            <span style="color:#dc3822">&#9660;</span>
                        </button>
                    </form>
                </th>
                <th style="min-width: 80px;" class="wrap-header hide-mobile">
                    <button type="button" class="sort-button">Prayer Fuel</button>
                </th>
                <th class="hide-mobile" style="min-width: 70px;">Video</th>
                <th style="min-width: 70px;">Join<span class="hide-mobile"> in Prayer</span></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $row_index = 0;
            $languages = p4m_languages_list();
            foreach ( $campaigns as $campaign ) :
                $flags = '';
                $row_index++;
                $background_color = 'white';
                if ( !empty( $campaign['campaign_progress'] ) && is_numeric( $campaign['campaign_progress'] ) ){
                    if ( $campaign['campaign_progress'] > 0 ){
                        $background_color = 'lightyellow';
                    }
                    if ( $campaign['minutes_committed'] >= 43200 ){ //minutes in 30 days
                        $background_color = 'lightgreen';
                    }
                    $campaign['campaign_progress'] .= '%';
                }
                foreach ( $campaign['prayer_fuel_languages'] ?? [] as $installed_fuel ){
                    if ( !empty( $languages[$installed_fuel]['flag'] ) ){
                        $flags .= $languages[$installed_fuel]['flag'];
                    }
                }

                ?>
                <tr style="background-color: <?php echo esc_html( $background_color ); ?>">
                    <td class="hide-mobile">
                        <?php echo esc_html( $row_index ); ?><span class="hide-mobile">.</span>
                    </td>
                    <td>
                        <?php if ( !empty( $campaign['campaign_link'] ) ) : ?>
                            <a target="_blank"
                               href="<?php echo esc_html( $campaign['campaign_link'] ); ?>"> <?php echo esc_html( $campaign['label'] ); ?></a>
                        <?php else : ?>
                            <?php echo esc_html( $campaign['label'] ); ?>
                        <?php endif; ?>
                        <span class="show-mobile"><?php echo esc_html( $flags ); ?></span>

                    </td>
                    <td><?php echo esc_html( $campaign['focus'] ); ?></td>
                    <td><?php echo esc_html( $campaign['campaign_progress'] ); ?></td>
                    <td><?php echo esc_html( p4m_display_minutes( $campaign['minutes_committed'] ) ); ?></td>
                    <td class="hide-mobile"><?php echo esc_html( $flags ); ?></td>
                    <td class="hide-mobile">
                        <?php if ( !empty( $campaign['promo_video'] ) ) : ?>
                            <a target="_blank" class="video-button" href="<?php echo esc_html( $campaign['promo_video'] ); ?>">
                                <img class="video-icon" src="<?php echo esc_html( get_template_directory_uri() . '/assets/images/video.svg' ) ?>"/>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ( !empty( $campaign['campaign_link'] ) ) : ?>
                            <a target="_blank" href="<?php echo esc_html( $campaign['campaign_link'] ); ?>#sign-up" style="display: block">
                                <span class="hide-mobile">Sign Up to </span>Pray</a>
                                <?php if ( !empty( $campaign['promo_video'] ) ) : ?>
                                    <span class="show-mobile">
                                        <a target="_blank" class="video-button" href="<?php echo esc_html( $campaign['promo_video'] ); ?>">
                                            <img class="video-icon" src="<?php echo esc_html( get_template_directory_uri() . '/assets/images/video.svg' ) ?>"/>
                                        </a>
                                    </span>
                                <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div style="text-align: center">
            Don’t see a city, country, or people groups you have on your heart? Champion one with us <a
                href="https://campaigns.prayer.tools/">here</a>.
        </div>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode( "p4m-ramadan-campaign-list", "p4m_ramadan_campaign_list" );


function p4m_languages_list(){
    return dt_get_global_languages_list();
}

function pm4_initiatives_list( $atts ){
    $initiative_locations = p4m_map_stats_world_networks();
    $initiatives = [];
    foreach ( $initiative_locations as $location_id => $location_data ){
        foreach ( $location_data["initiatives"] as $initiative ){
            if ( !isset( $initiatives[$initiative["initiative_id"]] ) ){
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
                <?php if ( !empty( $link ) ) : ?>
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
                    } else {
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


function p4m_get_site_link(){
    $site_keys = get_option( 'site_link_system_api_keys', [] );
    $token = null;
    $url = null;
    foreach( $site_keys as $key ){
        if ( $key['dev_key'] === 'campaigns_stats' ){
            $token = md5( md5( $key['token'] . $key['site1'] . $key['site2'] ) . current_time( 'Y-m-dH', 1 ) );
            $url = strpos( home_url(), $key['site1'] ) !== false ? $key['site2'] : $key['site1'];
        }
    }
    return [
        'token' => $token,
        'url' => $url
    ];
}

function p4m_get_all_campaigns( $refresh = false ){
    $site_link = p4m_get_site_link();

    if ( !empty( $site_link['token'] ) && !empty( $site_link['url'] ) ){
        $args = [
            'method' => 'GET',
            'body' => [],
            'headers' => [
                'Authorization' => 'Bearer ' . $site_link['token'],
            ],
        ];
        $refresh = strpos( home_url(), "pray4movement" ) === false || $refresh;
        $response = dt_cached_api_call(
            "https://" . $site_link['url'] . "/wp-json/dt-metrics/prayer-campaigns/all_campaigns",
            "GET", $args,
            MINUTE_IN_SECONDS * 5,
            !$refresh );
        return json_decode( $response, true ) ?? [];
    }
    return [];
}

function filter_campaigns( $campaigns, $atts ){
    if ( empty( $atts ) ){
        $atts = [];
    }
    $atts = dt_recursive_sanitize_array( $atts );

    $campaigns = array_filter( $campaigns, function ( $campaign ) use ( $atts ){
        $in_filter = empty( $atts['focus'] ) || in_array( $atts['focus'], $campaign["focus"] ?? [] );
        $in_filter = $in_filter && ( empty( $atts['type'] ) || $atts['type'] === $campaign["campaign_type"]['key'] ?? '' );
        $in_filter = $in_filter && ( empty( $atts['start_date'] ) || $campaign["start_date"] >= strtotime( $atts['start_date'] ) );
        $in_filter = $in_filter && ( empty( $atts['end_date'] ) || $campaign["end_date"] <= strtotime( $atts['end_date'] ) );
        $in_filter = $in_filter && ( empty( $atts['scheduled'] ) || $campaign["end_date"] > time() );
        return $in_filter;
    } );
    return $campaigns;

}

function p4m_display_minutes( $time_committed ){
    $years_committed = floor( $time_committed / 60 / 24 / 365 );
    $days_committed = floor( fmod( $time_committed / 60 / 24, 365 ) );
    $hours_committed = round( fmod( $time_committed / 60, 24 ) );
    $string = '';
    if ( !empty( $years_committed ) ){
        $string .= $years_committed . ' ' .( $years_committed > 1 ? __( 'years', 'disciple-tools-prayer-campaigns' ) : __( 'year', 'disciple-tools-prayer-campaigns' ) );
        $string  .= ' ';
    }
    if ( $days_committed >= 1 ){
        $string .= $days_committed . ' ' . ( (int) $days_committed === 1 ? __( 'day', 'disciple-tools-prayer-campaigns' ) : __( 'days', 'disciple-tools-prayer-campaigns' ) );
        $string  .= ' ';
    }
    if ( empty( $years_committed ) && !empty( $hours_committed ) ){
        $string .= $hours_committed . ' ' . ( (int) $hours_committed === 1 ? __( 'hour', 'disciple-tools-prayer-campaigns' ) : __( 'hours', 'disciple-tools-prayer-campaigns' ) );
    }
    if ( empty( $string ) ){
        $string = "0 " . __( 'hours', 'disciple-tools-prayer-campaigns' );
    }
    return $string;
}

add_shortcode( 'p4m-campaigns-list', function ( $atts ){

    $campaigns = p4m_get_all_campaigns();
    $campaigns = filter_campaigns( $campaigns, $atts );

    foreach ( $campaigns as &$c ){
        $c['location_focus'] = $c['people_group'];
        $campaign_locations = "";
        foreach ( $c["location_grid"] ?? [] as $location ){
            if ( !empty( $campaign_locations ) ){
                $campaign_locations .= ", ";
            }
            $campaign_locations .= $location["label"];
        }
        if ( empty( $c['location_focus'] ) ){
            $c['location_focus'] = $campaign_locations;
        }
    }


    $sort = "label";
    if ( isset( $_GET["sort_table"] ) ) {
        $sort = sanitize_text_field( wp_unslash( $_GET["sort_table"] ) );
    }

    uasort( $campaigns, function ( $a, $b ) use ( $sort ){
        return $a[$sort] <=> $b[$sort];
    });
    if ( $sort === "campaign_progress" || $sort === 'minutes_committed'){
        $campaigns = array_reverse( $campaigns );
    }

    ob_start();
    ?>

    <div class="campaign-list-wrapper">
        <table id="campaigns-list" style="overflow-x:scroll">
            <thead>
            <tr>
                <th style="width:60px" class="hide-mobile"></th>
                <th><form action="#campaigns-list"><button class="sort-button" name="sort_table" value="label">Campaign <span style="color:#dc3822">&#9650;</span></button></form></th>
                <th><form action="#campaigns-list"><button class="sort-button" name="sort_table" value="focus">Focus <span style="color:#dc3822">&#9650;</span></button></form></th>
                <th style="min-width: 66px">
                    <form action="#campaigns-list">
                        <button class="sort-button" name="sort_table" value="campaign_progress"><span
                                class="hide-mobile">Progress</span><span class="show-mobile">%</span> <span
                                style="color:#dc3822">&#9660;</span></button>
                    </form>
                </th>
                <th style="min-width: 66px">
                    <form action="#campaigns-list">
                        <button class="sort-button" name="sort_table" value="minutes_committed">
                            <span class="hide-mobile">Time Committed</span><span class="show-mobile">Committed</span>
                            <span style="color:#dc3822">&#9660;</span>
                        </button>
                    </form>
                </th>
                <th>
                    <form action='#campaigns-list'>
                        <button class='sort-button' name='sort_table' value='focus'>Type <span style='color:#dc3822'>&#9650;</span>
                        </button>
                    </form>
                </th>
                <th>
                    <form action='#campaigns-list'>
                        <button class='sort-button' name='sort_table' value='start_date'>Start <span style='color:#dc3822'>&#9650;</span>
                        </button>
                    </form>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
                $row_index = 0;
                $languages = p4m_languages_list();
                foreach ( $campaigns as $campaign ) :
                    $flags = '';
                    $row_index++;
                    $background_color = "white";
                    if ( !empty( $campaign["campaign_progress"] ) && is_numeric( $campaign["campaign_progress"] ) ){
                        if ( $campaign["campaign_progress"] > 0 ){
                            $background_color = "lightyellow";
                        }
                        if ( $campaign["campaign_progress"] >= 100 ){
                            $background_color = "lightgreen";
                        }
                        $campaign["campaign_progress"] .= '%';
                    }
                    foreach ( $campaign["prayer_fuel_languages"] ?? [] as $installed_fuel ){
                        if ( !empty( $languages[$installed_fuel]['flag'] ) ){
                            $flags .= $languages[$installed_fuel]['flag'];
                        }
                    }

                    ?>
                    <tr style="background-color: <?php echo esc_html( $background_color ); ?>">
                        <td class="hide-mobile">
                            <?php echo esc_html( $row_index ); ?><span class="hide-mobile">.</span>
                        </td>
                        <td>
                            <?php if ( !empty( $campaign['campaign_link'] ) ) : ?>
                                <a target="_blank" href="<?php echo esc_html( $campaign['campaign_link'] ); ?>"> <?php echo esc_html( $campaign["label"] ); ?></a>
                            <?php else : ?>
                                <?php echo esc_html( $campaign["label"] ); ?>
                            <?php endif; ?>
                            <span class="show-mobile"><?php echo esc_html( $flags ); ?></span>

                        </td>
                        <td><?php echo esc_html( $campaign['location_focus'] ); ?></td>
                        <td><?php echo esc_html( $campaign["campaign_progress"] ); ?></td>
                        <td><?php echo esc_html( p4m_display_minutes( $campaign['minutes_committed']) ); ?></td>
                        <td><?php echo esc_html( join(', ', $campaign["focus"] ?? '' ) ); ?></td>
                        <td><?php echo esc_html( date_i18n( 'Y-m-d', $campaign["start_date"] ) ); ?></td>

                    </tr>
                <?php endforeach;  ?>
            </tbody>
        </table>
        <div style="text-align: center">
            Don’t see a city, country, or people groups you have on your heart? Champion one with us <a href="https://campaigns.prayer.tools/">here</a>.
        </div>
    </div>
    <?php

    return ob_get_clean();

} );

add_shortcode('p4m-campaigns-map', function ( $atts ){

    $campaigns = p4m_get_all_campaigns();
    $focus = isset( $atts['focus'] ) ? esc_attr( wp_unslash( $atts['focus'] ) ) : '';

    $campaigns = filter_campaigns( $campaigns, $atts );

    $small = isset( $atts["size"] );


    DT_Mapbox_API::load_mapbox_header_scripts();

    wp_enqueue_style( 'p4m_map_styles', get_template_directory_uri() . '/assets/css/map.css' );
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
                'menu_slug' => $focus . ( $small ? "-small" : '' ),
                'post_type' => 'prayer_initiatives',
                'title' => "Click a country",
                'geocoder_url' => trailingslashit( get_stylesheet_directory_uri() ),
                'geocoder_nonce' => wp_create_nonce( 'wp_rest' ),
                'rest_base_url' => "p4m/maps",
                'totals_rest_url' => 'p4m-campaigns-refresh-stats',
                "small" => $small

            ],
        ]
    );
    $map = "campaigns-map.js";

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

    foreach( $campaigns as $campaign ){
        foreach( $campaign['location_grid'] ?? [] as $location_grid ){
            if ( !isset( $map_data[$location_grid['country_id']])){
                $map_data[$location_grid['country_id']] = [
                    'grid_id' => $location_grid['country_id'],
                    'count' => 0,
                    'name' => $location_grid['country_name'],
                    'initiatives' => []
                ];
            }
            $map_data[$location_grid['country_id']]['count']++;
            $map_data[$location_grid['country_id']]['initiatives'][] = [
                'label' => $campaign['label'],
                'link' => $campaign['campaign_link'],
                'progress' => $campaign['campaign_progress'],
                'prayer_fuel_languages' => $campaign['prayer_fuel_languages'] ?? [],
                'status' => $campaign['status']['key'] ?? '',
            ];
        }
    }


    wp_localize_script(
        'p4m_ramadan', 'p4m_ramadan', [
            'root' => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'data' => [
                'locations' => $map_data,
                'country_grid_ids' => $country_grid_ids,
            ],
            "type" => $focus,
            "small" => $small
        ]
    );

    $return = "<div id='chart' ></div>";
    if ( is_user_logged_in() && !$small ){
        $return .= "<div style='text-align: right'><button id='refresh_map_data' style='background-color: white; color: #dc3822; text-transform: lowercase;'>refresh data</button></div>";
    }
    return $return;

});