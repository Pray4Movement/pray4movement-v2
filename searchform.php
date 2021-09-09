<?php
/**
 * The template for displaying search form
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ) ?>">
    <label>
        <span class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'label', 'dtps' ) ?></span>
        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search...', 'dtps' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'dtps' ) ?>" />
    </label>
    <input type="submit" class="search-submit button" value="<?php echo esc_attr_x( 'Search', 'dtps' ) ?>" />
</form>
