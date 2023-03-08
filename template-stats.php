<?php /* Template Name: Stats */ ?>

<?php get_header(); ?>

<?php

$use_cache = !isset( $_GET['nocache'] );

$campaigns_data = get_transient( 'p4m-campaigns-stats' );
if ( empty( $campaigns_data ) || !$use_cache ) {
    $campaigns_data = [
        'stats' => apply_filters( 'go_stats_endpoint', [] ),
        'time' => time(),
    ];
    $prayer_global_request = wp_remote_post( 'https://prayer.global/wp-json/go/v1/stats' );
    if ( !is_wp_error( $prayer_global_request ) ) {
        $prayer_global = json_decode( $prayer_global_request['body'], true );
        if ( !empty( $prayer_global ) && !empty( $prayer_global['stats'] ) ) {
            $campaigns_data['prayer_global'] = $prayer_global['stats'];
        }
    }

    set_transient( 'p4m-campaigns-stats', $campaigns_data, DAY_IN_SECONDS );
}

$stats = $campaigns_data['stats'];
$stats['minutes_of_prayer']['value'] = p4m_display_minutes( $stats['minutes_of_prayer']['value'] );

?>


<style>
    #main {
        max-width:1000px;
        padding: 20px;
        margin:auto;
        min-height: 100vh;
    }
    .p4m-cards {
        display: flex;
        flex-wrap: wrap;
    }

    .p4m-card {
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        border-radius: 5px;
        margin: 10px;
        flex-basis: 30%;
    }
    @media only screen and (max-width: 768px) {
        .p4m-card {
            flex-basis: 100%;
        }
    }

    .p4m-card:hover {
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }

    .p4m-card-container {
        padding: 16px;
    }
    .p4m-stat-desc {
        font-size: 1rem;
        color: #666;
    }
    .p4m-card-title {
        font-size: 2rem;
        margin-top: 10px;
        min-height: 30px;
    }
</style>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <div class="page-inner-wrapper">
            <h2>Prayer Stats</h2>

            <div class="p4m-cards">
                <? foreach ( $stats as $stat ) : ?>
                    <div class="p4m-card">
                        <div class="p4m-card-container">
                            <h4 class="p4m-card-title"><? echo esc_html( $stat['label'] ) ?></h4>
                            <p><strong><? echo esc_html( $stat['value'] ) ?></strong></p>
                            <p class="p4m-stat-desc"><? echo esc_html( $stat['description']  ?? '')  ?></p>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>

            <br>
            <br>
            <p>Stats as of <?php echo esc_html( round( ( time() - $campaigns_data['time'] ) / 60 / 60, 1 ) ); ?> hour(s) ago</p>
        </div>


        <h3>Prayer Campaign Locations</h3>
        <?php echo do_shortcode( '[p4m-campaigns-map][/p4m-campaigns-map]' ); ?>
    </main><!-- .site-main -->

</div><!-- .content-area -->
<?php get_footer(); ?>
