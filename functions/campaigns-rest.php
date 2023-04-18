<?php
if ( !defined( 'ABSPATH' ) ){
    exit; // Exit if accessed directly
}

add_action( 'rest_api_init', function (){
    $namespace = 'dt-public/campaigns';
    register_rest_route(
        $namespace, 'campaigns-stats', [
            'methods' => 'GET',
            'callback' => 'p4m_campaigns_stats',
            'permission_callback' => '__return_true'
        ]
    );
} );

function p4m_campaigns_stats( WP_REST_Request $request ){
    $params = $request->get_params();

    $campaigns = p4m_get_all_campaigns();
    $campaigns = filter_campaigns( $campaigns, $params );

    $time_committed = 0;
    $intercessors = 0;
    $countries_prayed_for = [];
    $time_slots = 0;
    foreach ( $campaigns as &$c ){
        $time_committed += $c['minutes_committed'];
        $intercessors += $c['prayers_count'];
        foreach( $c['location_grid'] ?? [] as $location ){
            if ( !in_array( $location['country_name'], $countries_prayed_for ) ){
                $countries_prayed_for[] = $location['country_name'];
            }
        }
        $time_slots += $c['minutes_committed'] / 15;
    }


    return [
        'campaigns_count' => sizeof( $campaigns ),
        'countries_prayed_for_count' => sizeof( $countries_prayed_for ),
        'intercessors_count' => $intercessors,
        'time_committed' => $time_committed,
        'time_slots_count' => $time_slots,
    ];
}
