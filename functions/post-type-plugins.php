<?php
/**
 * Post Type Plugins
 */

if ( !defined( 'ABSPATH' )) {
    exit;
} // Exit if accessed directly.

/**
 * DTPS_Plugins_Post_Type Post Type Class
 * All functionality pertaining to project update post types in DTPS_Plugins_Post_Type.
 *
 * @package  Disciple_Tools
 * @since    0.1.0
 */
class DTPS_Plugins_Post_Type
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
     * DTPS_Plugins_Post_Type The single instance of DTPS_Plugins_Post_Type.
     * @var     object
     * @access  private
     * @since   0.1
     */
    private static $_instance = null;

    /**
     * Main DTPS_Plugins_Post_Type Instance
     *
     * Ensures only one instance of DTPS_Plugins_Post_Type is loaded or can be loaded.
     *
     * @return DTPS_Plugins_Post_Type instance
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
    public function __construct( $post_type = 'plugins', $singular = 'Plugin', $plural = 'Plugins', $args = array(), $taxonomies = array()) {
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
                    'name' => 'Plugins', /* This is the Title of the Group */
                    'singular_name' => 'Plugins', /* This is the individual type */
                    'all_items' => 'All Plugins', /* the all items menu item */
                    'add_new' => 'Add New', /* The add new menu item */
                    'add_new_item' => 'Add New User Doc', /* Add New Display Title */
                    'edit' => 'Edit', /* Edit Dialog */
                    'edit_item' => 'Edit Plugins', /* Edit Display Title */
                    'new_item' => 'New Plugins', /* New Display Title */
                    'view_item' => 'View Plugins', /* View Display Title */
                    'search_items' => 'Search Plugins', /* Search Custom Type Title */
                    'not_found' => 'Nothing found in the Database.', /* This displays if there are no entries yet */
                    'not_found_in_trash' => 'Nothing found in Trash', /* This displays if there is nothing in the trash */
                    'parent_item_colon' => 'Parent'
                ), /* end of arrays */
                'description' => $this->singular, /* Custom Type Description */
                'public' => true,
                'publicly_queryable' => true,
                'exclude_from_search' => false,
                'show_ui' => true,
                'query_var' => true,
                'menu_position' => 10, /* this is what order you want it to appear in on the left hand side menu */
                'menu_icon' => 'dashicons-book', /* the icon for the custom post type menu. uses built-in dashicons (CSS class name) */
                'rewrite' => array(
                    'slug' => 'plugins',
                    'with_front' => true
                ), /* you can specify its url slug */
                'has_archive' => 'plugins', /* you can rename the slug here */
                'capability_type'       => 'plugin_posts',
                'capabilities'          => [
                    'create_posts'        => 'create_plugin_posts',
                    'edit_post'           => 'edit_plugin_posts',
                    'read_post'           => 'read_plugin_posts',
                    'delete_post'         => 'delete_plugin_posts',
                    'delete_others_posts' => 'delete_plugin_posts',
                    'delete_posts'        => 'delete_plugin_posts',
                    'edit_posts'          => 'edit_plugin_posts',
                    'edit_others_posts'   => 'edit_plugin_posts',
                    'publish_posts'       => 'publish_plugin_posts',
                    'read_private_posts'  => 'read_plugin_posts',
                ],
                'hierarchical' => false,
                /* the next one is important, it tells what's enabled in the post editor */
                'show_in_rest' => true,
                'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'excerpt', 'custom-header', 'revisions' )
            ) /* end of options */
        ); /* end of register post type */
    } // End register_post_type()

    public function create_tag_taxonomies() {
        register_taxonomy('plugin_categories', 'plugins', array(
            'hierarchical' => true,
            'labels' => array(
                'name' => _x( 'Plugin Categories', 'taxonomy general name' ),
                'singular_name' => _x( 'Category', 'taxonomy singular name' ),
                'menu_name' => __( 'Categories' ),
            ),
            'show_ui' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => array( 'slug' => 'plugin-categories' ),
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
            2 => 'Plugins updated.',
            3 => 'Plugins deleted.',
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
        add_meta_box( $this->post_type . '_repo_connections', 'Repo Connection Options', array( $this, 'load_report_meta_box' ), $this->post_type, 'normal', 'high' );
    } // End meta_box_setup()

    /**
     * Meta box for Status Information
     *
     * @access public
     * @since  0.1.0
     */
    public function load_report_meta_box( $post ) {
        ?>
        <?php $this->meta_box_content( 'plugin_config' ); ?>

        <hr>

        <h3>Remote Sync (optional) </h3>

        <?php $this->meta_box_content( 'plugin_remote_sync' ); ?>


        <div id="json-button-wrapper">
            <p><button type="button" class="components-button is-secondary" onclick="sync_remote_json()">Trigger Remote Sync</button></p>
            <span id="json-error"></span>
        </div>
        <script>
            function sync_remote_json() {
                let url = jQuery('#version_control_json_url').val()
                let error = jQuery('#json-error')
                error.empty()

                if ( '' === url ) {
                    error.append('No json url available. Please, add json url.')
                    return
                }

                jQuery.ajax({
                    url: url,
                    dataType: 'json'
                })
                    .done(function(data) {
                        jQuery.each( data, function(i,v) {
                            if ( i === 'banners' || i === 'sections' ) {
                                jQuery.each(v, function(ii,vv) {
                                    jQuery('#'+ii).val(vv)
                                })
                            } else {
                                jQuery('#'+i).val(v)
                            }
                        })
                    })
                .fail(function(e) {
                    error.append('Failed to retrieve json. Check url.')
                })

                jQuery('.editor-post-publish-button').click()
            }
        </script>

        <hr>
        <h3>Version Control Data</h3>
        <br>NEW RELEASE FIELDS: These fields are generally the ones updated with a new version
        <?php $this->meta_box_content( 'plugin_version_control_fields_dynamic' ); ?>

        <hr>
        <br>DESCRIPTION FIELDS: These fields are generally stable from version to version and can be left alone when updating.
        <?php $this->meta_box_content( 'plugin_version_control_fields' ); ?>

        <hr>
        <h3>Rendered JSON URL </h3>
        <a href="<?php echo esc_url( WP_CONTENT_URL ) . '/themes/disciple-tools-public-site/version-control.php?id=' . esc_attr( hash( 'SHA256', $post->ID ) ) ?>" target="_blank"><?php echo esc_url( WP_CONTENT_URL ) . '/themes/disciple-tools-public-site/version-control.php?id=' . esc_attr( hash( 'SHA256', $post->ID ) ) ?></a>

        <?php
    }


    public static function update_version_info() {
        global $post;
        $url = get_post_meta( $post->ID, 'releases_url', true );
        if ( $url ) {
            $result = wp_remote_get( $url );
            $releases = [];
            if ( isset( $result['body'] ) ) {
                $releases = json_decode( $result['body'], true );
            }
            print_r( $releases );
        }

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
                        case 'date':
                            echo '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . esc_attr( $v['name'] ) . '</label></th><td>
                                    <input name="' . esc_attr( $k ) . '" class="datepicker regular-text" type="date" id="' . esc_attr( $k ) . '"  value="' . esc_attr( $data ) . '" />' . "\n";
                            echo '<p class="description">' . esc_html( $v['description'] ) . '</p>' . "\n";
                            echo '</td><tr/>' . "\n";

                            break;
                        case 'key_select':
                            echo '<tr class="' . esc_attr( $v['section'] ) . '" id="row_' . esc_attr( $k ) . '" valign="top"><th scope="row">
                                <label for="' . esc_attr( $k ) . '">' . esc_attr( $v['name'] ) . '</label></th>
                                <td>
                                <select name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" class="regular-text">';
                            // Iterate the options
                            foreach ( $v['default'] as $kk => $vv ) {
                                echo '<option value="' . esc_attr( $kk ) . '" ';
                                if ( $kk == $data ) {
                                    echo 'selected';
                                }
                                echo '>' . esc_attr( $vv ) . '</option>';
                            }
                            echo '</select>' . "\n";
                            echo '<p class="description">' . esc_attr( $v['description'] ) . '</p>' . "\n";
                            echo '</td><tr/>' . "\n";
                            break;
                        case 'textarea':
                            echo '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . esc_attr( $v['name'] ) . '</label></th><td><textarea name="' . esc_attr( $k ) . '" style="width:100%;" type="text" id="' . esc_attr( $k ) . '" class="regular-text" rows="5" />' . esc_attr( $data ) . '</textarea>' . "\n";
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


        $fields['github_owner'] = array(
            'name' => 'Github Owner',
            'description' => 'Account or organization hosting the repo',
            'type' => 'text',
            'default' => 'DiscipleTools',
            'section' => 'plugin_config',
        );
        $fields['github_repo'] = array(
            'name' => 'Github Repo',
            'description' => 'Repo Name (no spaces). Ex. disciple-tools-training',
            'type' => 'text',
            'default' => '',
            'section' => 'plugin_config',
        );



        $fields['version_control_json_url'] = array(
            'name' => 'Remote Version Control JSON URL',
            'description' => 'If url is present, hourly cron job will attempt to sync details from remote VC JSON.',
            'type' => 'text',
            'default' => '',
            'section' => 'plugin_remote_sync',
        );

        // dynamic vc
        $fields['version'] = array(
            'name' => 'Current Version Number',
            'description' => '',
            'type' => 'text',
            'default' => '',
            'section' => 'plugin_version_control_fields_dynamic',
        );
        $fields['last_updated'] = array(
            'name' => 'Release Date',
            'description' => '',
            'type' => 'text',
            'default' => '',
            'section' => 'plugin_version_control_fields_dynamic',
        );
        $fields['download_url'] = array(
            'name' => 'Current Download URL (optional)',
            'description' => '',
            'type' => 'text',
            'default' => '',
            'section' => 'plugin_version_control_fields_dynamic',
        );
        $fields['changelog'] = array(
            'name' => 'Current Change Log Message (optional)',
            'description' => '',
            'type' => 'textarea',
            'default' => '',
            'section' => 'plugin_version_control_fields_dynamic',
        );

        // stable vc

        $fields['name'] = array(
            'name' => 'Name',
            'description' => '',
            'type' => 'text',
            'default' => '',
            'section' => 'plugin_version_control_fields',
        );
        $fields['description'] = array(
            'name' => 'Description',
            'description' => '',
            'type' => 'textarea',
            'default' => '',
            'section' => 'plugin_version_control_fields',
        );
        $fields['installation'] = array(
            'name' => 'Installation Notes',
            'description' => '',
            'type' => 'textarea',
            'default' => '(Required) You must have Disciple.Tools Theme installed before using this plugin. Once Disciple.Tools is installed, you can use the normal Plugin system to install the plugin.',
            'section' => 'plugin_version_control_fields',
        );
        $fields['requires'] = array(
            'name' => 'WP Minimum Version Required',
            'description' => '',
            'type' => 'text',
            'default' => '4.7',
            'section' => 'plugin_version_control_fields',
        );
        global $wp_version;
        $fields['tested'] = array(
            'name' => 'WP Version Tested',
            'description' => '',
            'type' => 'text',
            'default' => $wp_version,
            'section' => 'plugin_version_control_fields',
        );
        $fields['low'] = array(
            'name' => 'Banners Low Image',
            'description' => '',
            'type' => 'text',
            'default' => 'https://raw.githubusercontent.com/DiscipleTools/disciple-tools-version-control/master/images/dt-placeholder-772x250.jpg',
            'section' => 'plugin_version_control_fields',
        );
        $fields['high'] = array(
            'name' => 'Banners High Image',
            'description' => '',
            'type' => 'text',
            'default' => 'https://raw.githubusercontent.com/DiscipleTools/disciple-tools-version-control/master/images/dt-placeholder-1544x500.jpg',
            'section' => 'plugin_version_control_fields',
        );
        $fields['author'] = array(
            'name' => 'Author',
            'description' => '',
            'type' => 'text',
            'default' => 'DiscipleTools',
            'section' => 'plugin_version_control_fields',
        );
        $fields['author_homepage'] = array(
            'name' => 'Author Homepage',
            'description' => '',
            'type' => 'text',
            'default' => 'https://disciple.tools',
            'section' => 'plugin_version_control_fields',
        );


        $fields['homepage'] = array(
            'name' => 'Repo URL',
            'description' => 'If empty, button will not display.',
            'type' => 'text',
            'default' => '',
            'section' => 'plugin_version_control_fields',
        );
        $fields['issues_url'] = array(
            'name' => 'Issues URL',
            'description' => 'If empty, button will not display.',
            'type' => 'text',
            'default' => '',
            'section' => 'plugin_version_control_fields',
        );
        $fields['projects_url'] = array(
            'name' => 'Projects URL',
            'description' => 'If empty, button will not display.',
            'type' => 'text',
            'default' => '',
            'section' => 'plugin_version_control_fields',
        );
        $fields['discussions_url'] = array(
            'name' => 'Discussions URL',
            'description' => 'If empty, button will not display.',
            'type' => 'text',
            'default' => '',
            'section' => 'plugin_version_control_fields',
        );
        $fields['wiki_url'] = array(
            'name' => 'Wiki URL',
            'description' => 'If empty, button will not display.',
            'type' => 'text',
            'default' => '',
            'section' => 'plugin_version_control_fields',
        );
        $fields['license_url'] = array(
            'name' => 'License URL',
            'description' => 'If empty, button will not display.',
            'type' => 'text',
            'default' => '',
            'section' => 'plugin_version_control_fields',
        );
        $fields['readme_url'] = array(
            'name' => 'Readme URL',
            'description' => 'If empty, button will not display. WARNING: Must be a raw markdown file. Use raw access url for github hosted readme. EX: https://raw.githubusercontent.com/DiscipleTools/disciple-tools-metrics-export/master/README.md',
            'type' => 'text',
            'default' => '',
            'section' => 'plugin_version_control_fields',
        );

        return apply_filters( 'dtps_plugins_fields_settings', $fields );
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
DTPS_Plugins_Post_Type::instance();
