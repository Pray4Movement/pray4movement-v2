<?php

/**
 * DTPS_REST_API
 *
 * @class DTPS_REST_API
 * @version 0.1
 * @since 0.1
 * @package Disciple_Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly


class DTPS_REST_API {

    /**
     * DTPS_REST_API The single instance of DTPS_REST_API.
     * @var     object
     * @access  private
     * @since   0.1
     */
    private static $_instance = null;

    /**
     * Main DTPS_REST_API instance
     *
     * Ensures only one instance of DTPS_REST_API is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return DTPS_REST_API instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this,  'add_api_routes' ) );

    } // End __construct()

    public function add_api_routes() {
        $version = '4';
        $namespace = 'dtps/v' . $version;

        register_rest_route( $namespace, '/community_request', array(
            array(
                'methods'         => WP_REST_Server::CREATABLE,
                'callback'        => array( $this, 'community_request' ),
            ),
        ) );
        register_rest_route( $namespace, '/update_profile', array(
            array(
                'methods'         => WP_REST_Server::CREATABLE,
                'callback'        => array( $this, 'update_profile' ),
                "permission_callback" => function () {
                    return current_user_can( 'subscriber' );
                }
            ),
        ) );
        register_rest_route( $namespace, '/unlink_profile', array(
            array(
                'methods'         => WP_REST_Server::CREATABLE,
                'callback'        => array( $this, 'unlink_profile' ),
                "permission_callback" => function () {
                    return current_user_can( 'subscriber' );
                }
            ),
        ) );
    }

    public function unlink_profile( WP_REST_Request $request){
        $params = $request->get_json_params();
        if ( isset( $params['type'] ) ) {
            switch ( $params['type'] ) {
                case 'facebook':
                    delete_user_meta( get_current_user_id(), 'facebook_sso_email' );
                    delete_user_meta( get_current_user_id(), 'facebook_sso_id' );
                    delete_user_meta( get_current_user_id(), 'facebook_session_token' );
                    break;
                case 'google':
                    delete_user_meta( get_current_user_id(), 'google_sso_email' );
                    delete_user_meta( get_current_user_id(), 'google_sso_id' );
                    delete_user_meta( get_current_user_id(), 'google_session_token' );
                    break;
                default:
                    return new WP_Error( "tract_param_error", "Unable to unlink profile", array( 'status' => 400 ) );
                    break;
            }
        } else {
            return new WP_Error( "tract_param_error", "Unable to unlink profile", array( 'status' => 400 ) );
        }
        return true;
    }

    public function update_profile( WP_REST_Request $request){
        $params = $request->get_json_params();
        $user_info = get_userdata( get_current_user_id() );

        // update name
        $name = sanitize_text_field( wp_unslash( $params['name'] ) );
        if ( empty( $name ) ) {
            delete_user_meta( $user_info->ID, 'dtps_full_name' );
        } else {
            update_user_meta( $user_info->ID, 'dtps_full_name', $name );
        }

        // update phone
        $phone = sanitize_text_field( wp_unslash( $params['phone'] ) );
        if ( empty( $phone ) ) {
            delete_user_meta( $user_info->ID, 'dtps_phone_number' );
        } else {
            update_user_meta( $user_info->ID, 'dtps_phone_number', $phone );
        }

        // update email
        $email = sanitize_email( wp_unslash( $params['email'] ) );
        if ( $email !== $user_info->ID && ! empty( $email ) ) {
            $args = array();
            $args['ID'] = $user_info->ID;
            $args['user_email'] = $email;

            $result = wp_update_user( $args );
            if ( is_wp_error( $result ) ) {
                return new WP_Error( 'fail_update_user_data', 'Error while updating user data in user table.' );
            }
        }

        // update affiliation key
        $affiliation_key = sanitize_text_field( wp_unslash( trim( $params['affiliation_key'] ) ) );
        if ( empty( $affiliation_key ) ) {
            delete_user_meta( $user_info->ID, 'dtps_affiliation_key' );
        } else {
            update_user_meta( $user_info->ID, 'dtps_affiliation_key', $affiliation_key );
        }

        // update location_grid_meta
        if ( empty( $params['location_grid_meta'] ) ) {
            delete_user_meta( $user_info->ID, 'location_grid_meta' );
        } else {
            if ( ! class_exists( 'Location_Grid_Geocoder' ) ) {
                require_once( get_stylesheet_directory() . '/dt-mapping/geocode-api/location-grid-geocoder.php' );
            }
            $geocoder = new Location_Grid_Geocoder();

            $location_grid_meta = array_map( 'sanitize_text_field', wp_unslash( $params['location_grid_meta'] ) );
            $lng = empty( $location_grid_meta['lng'] ) ? false : $location_grid_meta['lng'];
            $lat = empty( $location_grid_meta['lat'] ) ? false : $location_grid_meta['lat'];
            if ( $lng && $lat ) {
                $grid = $geocoder->get_grid_id_by_lnglat( $lng, $lat );
                if ( isset( $grid['grid_id'] ) ) {
                    $location_grid_meta['grid_id'] = $grid['grid_id'];
                }
            }

            Location_Grid_Meta::validate_location_grid_meta( $location_grid_meta );

            update_user_meta( $user_info->ID, 'location_grid_meta', $location_grid_meta );
        }

        $dtps_user = wp_get_current_user();
        $dtps_user_meta = dtps_get_user_meta( $dtps_user->ID );

        return array(
            'id' => $dtps_user->data->ID,
            'name' => $dtps_user_meta['dtps_full_name'] ?? '',
            'email' => $dtps_user->data->user_email,
            'phone' => $dtps_user_meta['dtps_phone_number'] ?? '',
            'location_grid_meta' => maybe_unserialize( $dtps_user_meta['location_grid_meta'] ) ?? '',
            'affiliation_key' => $dtps_user_meta['dtps_affiliation_key'] ?? '',
            'facebook_sso_email' => $dtps_user_meta['facebook_sso_email'] ?? false,
            'google_sso_email' => $dtps_user_meta['google_sso_email'] ?? false,
        );
    }

    /**
     * @param WP_REST_Request $request
     * @return array|WP_Error
     */
    public function community_request( WP_REST_Request $request ) {

        $params = $request->get_params();
        if ( ! isset( $params['name'] ) ) {
            return new WP_Error( "log_param_error", "Missing parameters", array( 'status' => 400 ) );
        }

        $args = array(
            'name' => sanitize_text_field( wp_unslash( $params['name'] ) ),
            'phone' => sanitize_text_field( wp_unslash( $params['phone'] ) ),
            'email' => sanitize_text_field( wp_unslash( $params['email'] ) ),
            'preference' => sanitize_text_field( wp_unslash( $params['preference'] ) ),
        );
        $notes = array(
            'preference' => 'Requested contact method is: ' .$args['preference'],
        );

        // build fields for transfer
        $fields = array(
            "title" => $args['name'],
            "sources" => array(
                "values" => array(
                    array( "value" => "dtps_vision" ),  //add new, or make sure it exists
                ),
            ),
            "contact_phone" => array(
                array( "value" => $args['phone'] ),
            ),
            "contact_email" => array(
                array( "value" => $args['email'] ),
            ),
            "notes" => $notes,
        );

        // Additional fields that may or may not be present
        if ( ! class_exists( 'Location_Grid_Geocoder' ) ) {
            require_once( get_stylesheet_directory() . '/dt-mapping/geocode-api/location-grid-geocoder.php' );
        }
        $geocoder = new Location_Grid_Geocoder();

        if ( empty( $params['location_grid_meta'] ) ) {
            // if no provided location, get ip address location
            $ip_result = DT_Ipstack_API::geocode_current_visitor();
            $args['location_grid_meta'] = Location_Grid_Meta::convert_ip_result_to_location_grid_meta( $ip_result );
        } else if ( ! empty( $params['location_grid_meta'] ) ) {
            $args['location_grid_meta'] = $params['location_grid_meta'];
        } else {
            $args['location_grid_meta'] = array();
        }
        Location_Grid_Meta::validate_location_grid_meta( $args['location_grid_meta'] );

        if ( $args['location_grid_meta'] ) {
            $fields['location_grid_meta'] = array(
                "values" => array( $args['location_grid_meta'] )
            );
            // load address field
            $fields['contact_address'] = array(
                array( "value" => $args['location_grid_meta']['label'] ),
            );
        }

        $site = Site_Link_System::get_site_connection_vars( 189 ); // @todo remove hardcoded
        if ( ! $site ) {
            return new WP_Error( __METHOD__, 'Missing site to site data' );
        }

        $args = array(
            'method' => 'POST',
            'body' => $fields,
            'headers' => array(
                'Authorization' => 'Bearer ' . $site['transfer_token'],
            ),
        );


        $result = wp_remote_post( 'https://' . trailingslashit( $site['url'] ) . 'wp-json/dt-posts/v2/contacts', $args );
        if ( is_wp_error( $result ) ) {
            dt_write_log( $result );
            $data = array(
                'payload'   => json_encode( array(
                        "channel"       => '#errors',
                        "text"          => 'Failed Community Request: ' . maybe_serialize( $result ) . ' --- ' . maybe_serialize( $fields ),
                        "username"      => 'error-bot',
                        "icon_emoji"    => 'ghost'
                    )
                )
            );
            // Post our data via the slack webhook endpoint using wp_remote_post
            $posting_to_slack = wp_remote_post( 'https://hooks.slack.com/services/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', array(
                    'method' => 'POST',
                    'timeout' => 30,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(),
                    'body' => $data,
                    'cookies' => array()
                )
            );
            return new WP_Error( 'failed_remote_post', $result->get_error_message() );
        }

        $body = json_decode( $result['body'], true );
        if ( ! isset( $body['ID'] ) ) {
            $data = array(
                'payload'   => json_encode( array(
                        "channel"       => '#errors',
                        "text"          => 'Failed Community Request: ' . maybe_serialize( $result ) . ' --- ' . maybe_serialize( $fields ),
                        "username"      => 'error-bot',
                        "icon_emoji"    => 'ghost'
                    )
                )
            );
            // Post our data via the slack webhook endpoint using wp_remote_post
            $posting_to_slack = wp_remote_post( 'https://hooks.slack.com/services/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', array(
                    'method' => 'POST',
                    'timeout' => 30,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(),
                    'body' => $data,
                    'cookies' => array()
                )
            );
        }


        return $result;

    }
}
DTPS_REST_API::instance();
