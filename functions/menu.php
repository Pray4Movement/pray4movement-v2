<?php
function add_description_to_menu($item_output, $item, $depth, $args) {

    if (strlen($item->description) > 0 ) {
        // append description after link
//        $item_output .= sprintf('<span class="description">%s</span>', esc_html($item->description));

        // or.. insert description as last item inside the link ($item_output ends with "</a>{$args->after}")
         $item_output = substr($item_output, 0, -strlen("</a>{$args->after}")) . sprintf('<span class="description">%s</span >', esc_html($item->description)) . "</a>{$args->after}";
    }
    return $item_output;
}
add_filter('walker_nav_menu_start_el', 'add_description_to_menu', 10, 4);