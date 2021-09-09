<div class="sidebar cell" role="complementary">
    <div class="show-for-small-only center">
        <a onclick="jQuery('.user-guide-menu-items').toggle()"><i class="fi-list"></i> Menu</a>
    </div>
    <div class="hide-for-small-only">
        <h3 class="center title"><a href="/dev-docs/">Developer Docs</a><span style="float:right;"><a href="/dev-docs/"><i class="fi-magnifying-glass"></i></a></span></h3>
    </div>
    <div class="user-guide-menu-items">
        <hr>
        <?php wp_list_pages(array(
            'post_type' => get_post_type( get_the_ID() ),
            'sort_column' => 'menu_order',
            'echo' => true,
            'title_li' => null,
        )) ?>
    </div>
</div>
