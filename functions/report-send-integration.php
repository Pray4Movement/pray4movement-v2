<?php
/**
 * News Send Integration
 */

class News_Send_Integration {

    /** Singleton @var null  */
    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor function.
     *
     * @access  public
     * @since   0.1.0
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'add_api_routes' ) );
        add_filter( 'site_link_type', array( $this, 'dt_network_report_receiving_link' ), 10, 1 );
        add_filter( 'site_link_type_capabilities', array( $this, 'dt_network_report_receiving_caps' ), 10, 1 );
    } // End __construct()

    // Adds the type of network connection to the site link system
    public function dt_network_report_receiving_link( $type ) {
        $type['network_dashboard_report'] = __( 'Movement News' );
        return $type;
    }

    // Add the specific capabilities needed for the site to site linking.
    public function dt_network_report_receiving_caps( $args ) {
        if ( 'network_dashboard_report' === $args['connection_type'] ) {
            $args['capabilities'][] = 'network_dashboard_report';
        }
        return $args;
    }

    public function add_api_routes() {

        $namespace_v1 = "dtps/v4";

        register_rest_route(
            $namespace_v1,
            '/send_report',
            array(
                'methods'  => 'POST',
                'callback' => array( $this, 'send_report' ),
            )
        );
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return int|WP_Error
     */
    public function send_report( WP_REST_Request $request ) {

        $params = $request->get_params();
        $transfer_vars = Site_Link_System::get_site_connection_vars( 89 );

        $packet = array(
            'method' => 'POST',
            'body' => array(
                'transfer_token' => $transfer_vars['transfer_token'],
                'report_data' => $params,
            )
        );

        $result = wp_remote_post( 'https://global.dtps-vision/wp-json/dt-public/v1/network/report', $packet );

        return wp_remote_retrieve_body( $result );
    }
}
News_Send_Integration::instance();
