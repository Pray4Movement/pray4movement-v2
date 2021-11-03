<?php
/**
 * Post Type Template
 */

if ( !defined( 'ABSPATH' )) {
    exit;
} // Exit if accessed directly.


/**
 * P4_Network_Post_Type Class
 * All functionality pertaining to project update post types in P4_Network_Post_Type.
 *
 * @package  Disciple_Tools
 * @since    0.1.0
 */
class P4_Network_Post_Type
{

    public $post_type;
    public $singular;
    public $plural;
    public $args;
    public $taxonomies;
    private static $_instance = null;
    public static function instance() {
        if (is_null( self::$_instance )) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Disciple_Tools_Prayer_Post_Type constructor.
     *
     * @param array $args
     * @param array $taxonomies
     */
    public function __construct( $args = [], $taxonomies = []) {
        $this->post_type = 'p4network';
        $this->singular = 'Pray4 Network';
        $this->plural = 'Pray4 Network';
        $this->args = $args;
        $this->taxonomies = $taxonomies;

        add_action( 'init', [ $this, 'register_post_type' ] );
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
        add_action( 'save_post', [ $this, 'save_meta_box' ] );

    } // End __construct()

    public function add_meta_box( $post_type ) {
        if ( $this->post_type === $post_type ) {
            add_meta_box( $this->post_type . '_custom_fields', 'Network Fields', [ $this, 'meta_box_custom_fields'], $this->post_type, 'side', 'high' );
            add_meta_box( $this->post_type . '_instructions', 'Instructions', [ $this, 'meta_box_instructions'], $this->post_type, 'side', 'low' );
        }
    }


    function save_meta_box( $post_id ) {

        // Check if our nonce is set.
        if ( ! isset( $_POST['network_fields_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['network_fields_nonce'], 'network_fields_nonce' ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['network_tagline'] ) ) {
            update_post_meta( $post_id, 'network_tagline', sanitize_text_field( $_POST['network_tagline'] ) );
        }
        if ( isset( $_POST['network_url'] ) ) {
            update_post_meta( $post_id, 'network_url', sanitize_text_field( $_POST['network_url'] ) );
        }
        if ( isset( $_POST['network_focus'] ) ) {
            update_post_meta( $post_id, 'network_focus', sanitize_text_field( $_POST['network_focus'] ) );
        }
        if ( isset( $_POST['network_lng'] ) ) {
            update_post_meta( $post_id, 'network_lng', sanitize_text_field( $_POST['network_lng'] ) );
        }
        if ( isset( $_POST['network_lat'] ) ) {
            update_post_meta( $post_id, 'network_lat', sanitize_text_field( $_POST['network_lat'] ) );
        }
        if ( isset( $_POST['network_lead_form'] ) ) {
            update_post_meta( $post_id, 'network_lead_form', sanitize_text_field( $_POST['network_lead_form'] ) );
        }
    }

    public function meta_box_custom_fields( $post ) {
        $fields = get_post_meta( $post->ID );
        $network_tagline = $fields['network_tagline'][0] ?? '';
        $network_url = $fields['network_url'][0] ?? '';
        $network_focus = $fields['network_focus'][0] ?? '';
        $network_lng = $fields['network_lng'][0] ?? '';
        $network_lat = $fields['network_lat'][0] ?? '';
        $network_lead_form = $fields['network_lead_form'][0] ?? '';

        wp_nonce_field( 'network_fields_nonce', 'network_fields_nonce' );
        ?>
        <p><label for="network_tagline"><strong>Description:</strong></label><br><input id="network_tagline" name="network_tagline" type="text" style="width:100%;" placeholder="Tag Line" value="<?php echo esc_attr($network_tagline) ?>"/></p>
        <p><label for="network_url"><strong>URL:</strong></label><br><input id="network_url" name="network_url" type="text" style="width:100%;" placeholder="URL" value="<?php echo esc_attr($network_url) ?>" /></p>
        <p><label for="network_focus"><strong>Focus:</strong></label><br><input id="network_focus" name="network_focus" type="text" style="width:100%;" placeholder="Location or Target Group" value="<?php echo esc_attr($network_focus) ?>" /></p>
        <p><label for="network_lng"><strong>Longitude:</strong></label><br><input id="network_lng" name="network_lng" type="text" style="width:100%;" placeholder="Longitude" value="<?php echo esc_attr($network_lng) ?>" /></p>
        <p><label for="network_lat"><strong>Latitude:</strong></label><br><input id="network_lat" name="network_lat" type="text" style="width:100%;" placeholder="Latitude" value="<?php echo esc_attr($network_lat) ?>" /></p>
        <p><label for="network_lead_form"><strong>Lead Form URl</strong></label><br><input id="network_lead_form" name="network_lead_form" type="text" style="width:100%;" placeholder="Lead Form URL" value="<?php echo esc_attr($network_lead_form) ?>" /></p>
        <?php
    }

    public function meta_box_instructions( $post ) {
        ?>
        <p>Requirements for setting up a network location:</p>
        <ul>
            <li>1. Post Title should be the name the network.</li>
            <li>2. Content can be anything, but an image and content block would be a minimum.</li>
            <li>3. Featured image. (This is used on the list page)</li>
            <li>4. Network Fields:
                <ul>
                    <li> - a. Tagline for the website.</li>
                    <li> - b. Full URL including https:</li>
                    <li> - c. Focus of the prayer effort</li>
                    <li> - d. Longitude and Latitude drive the point on the general map.</li>
                    <li> - e. Lead Form URL from DT Webform (if used)</li>
                </ul>
            </li>
        </ul>
        <?php
    }

    /**
     * Register the post type.
     *
     * @access public
     * @return void
     */
    public function register_post_type() {
        register_post_type($this->post_type, /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
            // let's now add all the options for this post type
            array(
                'labels' => array(
                    'name' => $this->plural, /* This is the Title of the Group */
                    'singular_name' => $this->singular, /* This is the individual type */
                    'all_items' => 'All '.$this->plural, /* the all items menu item */
                    'add_new' => 'Add New', /* The add new menu item */
                    'add_new_item' => 'Add New '.$this->singular, /* Add New Display Title */
                    'edit' => 'Edit', /* Edit Dialog */
                    'edit_item' => 'Edit '.$this->singular, /* Edit Display Title */
                    'new_item' => 'New '.$this->singular, /* New Display Title */
                    'view_item' => 'View '.$this->singular, /* View Display Title */
                    'search_items' => 'Search '.$this->plural, /* Search Custom Type Title */
                    'not_found' => 'Nothing found in the Database.', /* This displays if there are no entries yet */
                    'not_found_in_trash' => 'Nothing found in Trash', /* This displays if there is nothing in the trash */
                    'parent_item_colon' => ''
                ), /* end of arrays */
                'description' => $this->singular, /* Custom Type Description */
                'public' => true,
                'publicly_queryable' => true,
                'exclude_from_search' => false,
                'show_ui' => true,
                'query_var' => true,
                'show_in_nav_menus' => true,
                'menu_position' => 5, /* this is what order you want it to appear in on the left hand side menu */
                'menu_icon' => 'dashicons-book', /* the icon for the custom post type menu. uses built-in dashicons (CSS class name) */
                'rewrite' => true, /* you can specify its url slug */
                'has_archive' => true, /* you can rename the slug here */
                'capability_type' => 'post',
                'hierarchical' => false,
                'show_in_rest' => true,
                'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' )
            ) /* end of options */
        ); /* end of register post type */
    } // End register_post_type()

} // End Class
P4_Network_Post_Type::instance();
