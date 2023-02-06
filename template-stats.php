<?php /* Template Name: Stats */ ?>

<?php get_header(); ?>

<?php

$use_cache = !isset( $_GET['nocache'] );

$campaigns_data = get_transient( 'p4m-campaigns-stats' );
if ( empty( $campaigns_data ) || !$use_cache ) {
    $campaigns_data = [
        'campaigns' => p4m_get_all_campaigns( true ),
        'time' => time(),
    ];
    set_transient( 'p4m-campaigns-stats', $campaigns_data, DAY_IN_SECONDS );
}
$campaigns = $campaigns_data['campaigns'];



$stats = [
    'prayer_committed' => [ 'count' => 0, 'label' => 'Total Time Prayed and Time Committed', 'desc' => 'Total time committed to pray for all campaigns past and scheduled' ],
    'warriors' => [ 'count' => 0, 'label' => 'Intercessors', 'desc' => 'This number may be too high because some people may pray for multiple campaigns, or it may be too low because sometimes groups are praying together and we’re only counting them as individuals.' ],
    'campaigns' => [ 'count' => count( $campaigns ), 'label' => 'Campaigns', 'desc' => 'Total number of campaigns' ],
    'locations' => [ 'count' => 0, 'label' => 'Countries', 'desc' => 'Countries with a campaign' ],
];

$locations = [];

foreach( $campaigns as $campaign ) {
    $stats['prayer_committed']['count'] += $campaign['minutes_committed'];
    $stats['warriors']['count'] += $campaign['prayers_count'];
    foreach( $campaign['location_grid'] ?? [] as $location ) {
        if ( !in_array( $location['country_id'], $locations ) ) {
            $locations[] = $location['country_id'];
        }
    }
}

$stats['prayer_committed']['count'] = p4m_display_minutes( $stats['prayer_committed']['count'] );
$stats['locations']['count'] = count( $locations );

?>


<style>
    #main {
        max-width:1000px;
        padding: 20px;
        margin:auto;
        min-height: 100vh;
    }
    .cards {
        display: flex;
        flex-wrap: wrap;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        border-radius: 5px;
        margin: 10px;
        flex-basis: 30%;
    }

    .card:hover {
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }

    .card-container {
        padding: 16px;
    }
    .p4m-stat-desc {
        font-size: 1rem;
        color: #666;
    }
</style>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <div class="page-inner-wrapper">
            <h1>Stats</h1>

            <div class="cards">
                <? foreach ($stats as $stat) : ?>
                    <div class="card">
                        <div class="card-container">
                            <h4><b><? echo esc_html( $stat['count'] ) ?></b></h4>
                            <p><? echo esc_html( $stat['label'] ) ?></p>
                            <p class="p4m-stat-desc"><? echo esc_html( $stat['desc']  ?? '')  ?></p>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>

            <br>
            <br>
            <p>Stats as of <?php echo esc_html( round( ( time() - $campaigns_data['time'] ) / 60 / 60, 1 ) ); ?> hour(s) ago</p>
        </div>


        <?php echo do_shortcode( '[p4m-campaigns-map][/p4m-campaigns-map]' ); ?>
    </main><!-- .site-main -->

</div><!-- .content-area -->
<?php get_footer(); ?>
