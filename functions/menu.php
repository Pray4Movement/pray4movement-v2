<?php
function add_description_to_menu($item_output, $item, $depth, $args) {

    if (strlen($item->description) > 0 ) {
         $item_output = "
         <a href='{$item->url}' >
             <div class='image-wrapper'>
                <img src='{$item->menu_image}' alt=''>
             </div>
             <div class='text-wrapper'>
                <span>{$item->title}</span>
                <span class='description'>{$item->description}</span>
            </div>
        </a>
         ";
    }
    return $item_output;
}
add_filter('walker_nav_menu_start_el', 'add_description_to_menu', 10, 4);

add_action( 'wp_nav_menu_item_custom_fields',function ($id, $item, $depth, $args){
    ?>
        <label>
            Icon
            <input name="menu-item-image[<?php echo esc_html( $id ); ?>]" value="<?php echo esc_html( $item->menu_image ?? '' ); ?>">
        </label>
    <?php
}, 10, 4 );



add_filter( 'wp_setup_nav_menu_item', function ( $menu_item ) {
    $menu_item->menu_image = get_post_meta( $menu_item->ID, '_menu_item_image', true );
    return $menu_item;

} );

add_action( 'wp_update_nav_menu_item', function ( $menu_id, $menu_item_db_id, $args ){

    if ( isset( $_POST['menu-item-image'][$menu_item_db_id] ) ){
        $image_value = $_POST['menu-item-image'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_image', $image_value );
    }

}, 10, 3 );



