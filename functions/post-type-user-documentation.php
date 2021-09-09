<?php
/**
 * Post Type User Documentation
 */

if ( !defined( 'ABSPATH' )) {
    exit;
} // Exit if accessed directly.

/**
 * DTPS_User_Documentation_Post_Type Post Type Class
 * All functionality pertaining to project update post types in DTPS_User_Documentation_Post_Type.
 *
 * @package  Disciple_Tools
 * @since    0.1.0
 */
class DTPS_User_Documentation_Post_Type
{
    /**
     * The post type token.
     *
     * @access public
     * @since  0.1.0
     * @var    string
     */
    public $post_type;

    /**
     * The post type singular label.
     *
     * @access public
     * @since  0.1.0
     * @var    string
     */
    public $singular;

    /**
     * The post type plural label.
     *
     * @access public
     * @since  0.1.0
     * @var    string
     */
    public $plural;

    /**
     * The post type args.
     *
     * @access public
     * @since  0.1.0
     * @var    array
     */
    public $args;

    /**
     * The taxonomies for this post type.
     *
     * @access public
     * @since  0.1.0
     * @var    array
     */
    public $taxonomies;

    /**
     * DTPS_User_Documentation_Post_Type The single instance of DTPS_User_Documentation_Post_Type.
     * @var     object
     * @access  private
     * @since   0.1
     */
    private static $_instance = null;

    /**
     * Main DTPS_User_Documentation_Post_Type Instance
     *
     * Ensures only one instance of DTPS_User_Documentation_Post_Type is loaded or can be loaded.
     *
     * @return DTPS_User_Documentation_Post_Type instance
     * @since 0.1
     * @static
     */
    public static function instance() {
        if (is_null( self::$_instance )) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Disciple_Tools_Prayer_Post_Type constructor.
     *
     * @param string $post_type
     * @param string $singular
     * @param string $plural
     * @param array $args
     * @param array $taxonomies
     */
    public function __construct( $post_type = 'user_documentation', $singular = 'User Docs', $plural = 'User Docs', $args = array(), $taxonomies = array()) {
        $this->post_type = $post_type;
        $this->singular = $singular;
        $this->plural = $plural;
        $this->args = $args;
        $this->taxonomies = $taxonomies;

        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'init', array( $this, 'create_tag_taxonomies' ), 0 );

        if (is_admin()) {
            global $pagenow;

            add_action( 'admin_menu', array( $this, 'meta_box_setup' ), 20 );
            add_action( 'save_post', array( $this, 'meta_box_save' ) );
            add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) );
            add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );

            if ($pagenow == 'edit.php' && isset( $_GET['post_type'] )) {
                $pt = sanitize_text_field( wp_unslash( $_GET['post_type'] ) );
                if ($pt === $this->post_type) {
                    add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'register_custom_column_headings' ), 10, 1 );
                    add_action( 'manage_pages_custom_column', array( $this, 'register_custom_columns' ), 10, 2 );
                }
            }
        }
    } // End __construct()

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
                    'name' => 'User Docs', /* This is the Title of the Group */
                    'singular_name' => 'User Docs', /* This is the individual type */
                    'all_items' => 'All User Docs', /* the all items menu item */
                    'add_new' => 'Add New', /* The add new menu item */
                    'add_new_item' => 'Add New User Doc', /* Add New Display Title */
                    'edit' => 'Edit', /* Edit Dialog */
                    'edit_item' => 'Edit User Documentation', /* Edit Display Title */
                    'new_item' => 'New User Documentation', /* New Display Title */
                    'view_item' => 'View User Documentation', /* View Display Title */
                    'search_items' => 'Search User Documentation', /* Search Custom Type Title */
                    'not_found' => 'Nothing found in the Database.', /* This displays if there are no entries yet */
                    'not_found_in_trash' => 'Nothing found in Trash', /* This displays if there is nothing in the trash */
                    'parent_item_colon' => 'Parent'
                ), /* end of arrays */
                'description' => 'User Documentation', /* Custom Type Description */
                'public' => true,
                'publicly_queryable' => true,
                'exclude_from_search' => false,
                'show_ui' => true,
                'query_var' => true,
                'menu_position' => 10, /* this is what order you want it to appear in on the left hand side menu */
                'menu_icon' => 'dashicons-book', /* the icon for the custom post type menu. uses built-in dashicons (CSS class name) */
                'rewrite' => array(
                    'slug' => 'user-docs',
                    'with_front' => true
                ), /* you can specify its url slug */
                'has_archive' => 'user-docs', /* you can rename the slug here */
                'capability_type'       => 'user_doc',
                'capabilities'          => [
                    'create_posts'        => 'create_user_docs',
                    'edit_post'           => 'edit_user_docs',
                    'read_post'           => 'read_user_docs',
                    'delete_post'         => 'delete_user_docs',
                    'delete_others_posts' => 'delete_user_docs',
                    'delete_posts'        => 'delete_user_docs',
                    'edit_posts'          => 'edit_user_docs',
                    'edit_others_posts'   => 'edit_user_docs',
                    'publish_posts'       => 'publish_user_docs',
                    'read_private_posts'  => 'read_user_docs',
                ],
                'hierarchical' => true,
                /* the next one is important, it tells what's enabled in the post editor */
                'show_in_rest' => true,
                'supports' => array( 'title', 'editor', 'revisions',  'thumbnail', 'page-attributes', 'excerpt')
            ) /* end of options */
        ); /* end of register post type */
    } // End register_post_type()

    public function create_tag_taxonomies() {
        // Add new taxonomy, NOT hierarchical (like tags)

        register_taxonomy('user_categories', 'user_documentation', array(
            'hierarchical' => true,
            'labels' => array(
                'name' => _x( 'User Documentation Categories', 'taxonomy general name' ),
                'singular_name' => _x( 'Category', 'taxonomy singular name' ),
                'menu_name' => __( 'Categories' ),
            ),
            'show_ui' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => array( 'slug' => 'user-categories' ),
            'show_in_rest' => true,
        ));
    }


    /**
     * Add custom columns for the "manage" screen of this post type.
     *
     * @access public
     *
     * @param string $column_name
     *
     * @return void
     * @since  0.1.0
     */
    public function register_custom_columns( $column_name, $post_id ) {
        global $post;

        switch ($column_name) {
            case 'order':
                echo esc_html( $post->menu_order );
                break;
            case 'phone':
                echo '';
                break;

            default:
                break;
        }
    }

    /**
     * Add custom column headings for the "manage" screen of this post type.
     *
     * @access public
     *
     * @param array $defaults
     *
     * @return mixed/void
     * @since  0.1.0
     */
    public function register_custom_column_headings( $defaults) {

        $new_columns = array( 'order' => __( 'Order', 'dtps' ) );

        $last_item = array();

        if (count( $defaults ) > 2) {
            $last_item = array_slice( $defaults, -1 );

            array_pop( $defaults );
        }
        $defaults = array_merge( $defaults, $new_columns );

        if (is_array( $last_item ) && 0 < count( $last_item )) {
            foreach ($last_item as $k => $v) {
                $defaults[$k] = $v;
                break;
            }
        }

        return $defaults;
    } // End register_custom_column_headings()

    /**
     * Update messages for the post type admin.
     *
     * @param array $messages Array of messages for all post types.
     *
     * @return array           Modified array.
     * @since  0.1.0
     *
     */
    public function updated_messages( $messages) {
        global $post;

        $messages[$this->post_type] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf(
                '%3$s updated. %1$sView %4$s%2$s',
                '<a href="' . esc_url( get_permalink( $post->ID ) ) . '">',
                '</a>',
                $this->singular,
                strtolower( $this->singular )
            ),
            2 => 'User Documentation updated.',
            3 => 'User Documentation deleted.',
            4 => sprintf( '%s updated.', $this->singular ),
            /* translators: %s: date and time of the revision */
            5 => isset( $_GET['revision'] ) ? sprintf( '%1$s restored to revision from %2$s', $this->singular, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6 => sprintf( '%1$s published. %3$sView %2$s%4$s', $this->singular, strtolower( $this->singular ), '<a href="' . esc_url( get_permalink( $post->ID ) ) . '">', '</a>' ),
            7 => sprintf( '%s saved.', $this->singular ),
            8 => sprintf( '%1$s submitted. %2$sPreview %3$s%4$s', $this->singular, strtolower( $this->singular ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '">', '</a>' ),
            9 => sprintf(
                '%1$s scheduled for: %1$s. %2$sPreview %2$s%3$6$s',
                $this->singular,
                strtolower( $this->singular ),
                // translators: Publish box date format, see http://php.net/date
                '<strong>' . date_i18n('M j, Y @ G:i',
                strtotime( $post->post_date )) . '</strong>',
                '<a target="_blank" href="' . esc_url( get_permalink( $post->ID ) ) . '">',
                '</a>'
            ),
            10 => sprintf( '%1$s draft updated. %2$sPreview %3$s%4$s', $this->singular, strtolower( $this->singular ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '">', '</a>' ),
        );

        return $messages;
    } // End updated_messages()

    /**
     * Setup the meta box.
     *
     * @access public
     * @return void
     * @since  0.1.0
     */
    public function meta_box_setup() {
//        add_meta_box( $this->post_type . '_scribes', 'User Documentation', array( $this, 'load_report_meta_box' ), $this->post_type, 'normal', 'high' );
    } // End meta_box_setup()

    /**
     * Meta box for Status Information
     *
     * @access public
     * @since  0.1.0
     */
    public function load_report_meta_box() {
        $this->meta_box_content( 'description' ); // prints
    }

    /**
     * The contents of our meta box.
     *
     * @param string $section
     */
    public function meta_box_content( $section = 'scribe') {
        global $post_id;
        $fields = get_post_custom( $post_id );
        $field_data = $this->get_custom_fields_settings();

        echo '<input type="hidden" name="' . esc_attr( $this->post_type ) . '_noonce" id="' . esc_attr( $this->post_type ) . '_noonce" value="' . esc_attr( wp_create_nonce( 'report_noonce_action' ) ) . '" />';

        if (0 < count( $field_data )) {
            echo '<table class="form-table">' . "\n";
            echo '<tbody>' . "\n";

            foreach ($field_data as $k => $v) {

                if ($v['section'] == $section) {

                    $data = $v['default'];
                    if (isset( $fields[$k] ) && isset( $fields[$k][0] )) {
                        $data = $fields[$k][0];
                    }

                    $type = $v['type'];

                    switch ($type) {

                        case 'url':
                            echo '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . esc_html( $v['name'] ) . '</label></th><td><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
                            echo '<p class="description">' . esc_html( $v['description'] ) . '</p>' . "\n";
                            echo '</td><tr/>' . "\n";
                            break;
                        case 'text':
                            echo '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . esc_html( $v['name'] ) . '</label></th>
                                <td><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
                            echo '<p class="description">' . esc_html( $v['description'] ) . '</p>' . "\n";
                            echo '</td><tr/>' . "\n";
                            break;
                        case 'link':
                            echo '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . esc_html( $v['name'] ) . '</label></th>
                                <td><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
                            $video_id = esc_attr( $k ) . 'video';
                            echo '<p class="description"><a onclick="show_video( \'' . esc_attr( $video_id ) . '\', \'' . esc_attr( $data ) . '\' )">verify link</a><span id="' . esc_attr( $video_id ) . '"></span></p>' . "\n";
                            echo '</td><tr/>' . "\n";
                            break;
                        case 'alt_link':
                            echo '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . esc_html( $v['name'] ) . '</label></th>
                                <td><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
                            $video_id = esc_attr( $k ) . 'video';
                            echo '<p class="description"><a onclick="show_alt_video( \'' . esc_attr( $video_id ) . '\', \'' . esc_attr( $data ) . '\' )">verify link</a><span id="' . esc_attr( $video_id ) . '"></span></p>' . "\n";
                            echo '</td><tr/>' . "\n";
                            break;
                        case 'select':
                            echo '<tr valign="top"><th scope="row">
                                <label for="' . esc_attr( $k ) . '">' . esc_html( $v['name'] ) . '</label></th>
                                <td>
                                <select name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" class="regular-text">';
                            // Iterate the options
                            foreach ($v['default'] as $vv) {
                                echo '<option value="' . esc_attr( $vv ) . '" ';
                                if ($vv == $data) {
                                    echo 'selected';
                                }
                                echo '>' . esc_html( $vv ) . '</option>';
                            }
                            echo '</select>' . "\n";
                            echo '<p class="description">' . esc_html( $v['description'] ) . '</p>' . "\n";
                            echo '</td><tr/>' . "\n";
                            break;

                        default:
                            break;
                    }
                }
            }
            echo '</tbody>' . "\n";
            echo '</table>' . "\n";

        }
    } // End meta_box_content()

    /**
     * Save meta box fields.
     *
     * @access public
     * @param int $post_id
     *
     * @return int $post_id
     * @since  0.1.0
     *
     */
    public function meta_box_save( $post_id) {

        // Verify
        if (get_post_type() != $this->post_type) {
            return $post_id;
        }

        $key = $this->post_type . '_noonce';
        if (isset( $_POST[$key] ) && !wp_verify_nonce( sanitize_key( $_POST[$key] ), 'report_noonce_action' )) {
            return $post_id;
        }

        if (isset( $_POST['post_type'] ) && 'page' == sanitize_text_field( wp_unslash( $_POST['post_type'] ) )) {
            if ( !current_user_can( 'edit_page', $post_id )) {
                return $post_id;
            }
        } else {
            if ( !current_user_can( 'edit_post', $post_id )) {
                return $post_id;
            }
        }

        if (isset( $_GET['action'] )) {
            if ($_GET['action'] == 'trash' || $_GET['action'] == 'untrash' || $_GET['action'] == 'delete') {
                return $post_id;
            }
        }

        $field_data = $this->get_custom_fields_settings();
        $fields = array_keys( $field_data );

        foreach ($fields as $f) {
            if ( !isset( $_POST[$f] )) {
                continue;
            }

            ${$f} = strip_tags( trim( sanitize_text_field( wp_unslash( $_POST[$f] ) ) ) );

            // Escape the URLs.
            if ('url' == $field_data[$f]['type']) {
                ${$f} = esc_url( ${$f} );
            }

            if (get_post_meta( $post_id, $f ) == '') {
                add_post_meta( $post_id, $f, ${$f}, true );
            } elseif (${$f} != get_post_meta( $post_id, $f, true )) {
                update_post_meta( $post_id, $f, ${$f} );
            } elseif (${$f} == '') {
                delete_post_meta( $post_id, $f, get_post_meta( $post_id, $f, true ) );
            }
        }
        return $post_id;
    } // End meta_box_save()


    /**
     * Customise the "Enter title here" text.
     *
     * @access public
     * @param string $title
     *
     * @return string
     * @since  0.1.0
     *
     */
    public function enter_title_here( $title) {
        if (get_post_type() == $this->post_type) {
            $title = 'Enter the title here';
        }

        return $title;
    } // End enter_title_here()

    /**
     * Get the settings for the custom fields.
     *
     * @access public
     * @return array
     * @since  0.1.0
     */
    public function get_custom_fields_settings() {
        $fields = array();

        $fields['1'] = array(
            'name' => 'Welcome to User Documentation',
            'description' => '',
            'type' => 'link',
            'default' => '247062938',
            'section' => 'description',
        );

        return apply_filters( 'dtps_user_documentation_fields_settings', $fields );
    } // End get_custom_fields_settings()

    /**
     * Run on activation.
     *
     * @access public
     * @since  0.1.0
     */
    public function activation() {
        $this->flush_rewrite_rules();
    } // End activation()

    /**
     * Flush the rewrite rules
     *
     * @access public
     * @since  0.1.0
     */
    private function flush_rewrite_rules() {
        $this->register_post_type();
        flush_rewrite_rules();
    } // End flush_rewrite_rules()

} // End Class
DTPS_User_Documentation_Post_Type::instance();
