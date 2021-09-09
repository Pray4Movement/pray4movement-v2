<!-- By default, this menu will use off-canvas for small
     and a topbar for medium-up -->

<?php
    $dtps_is_logged_in = is_user_logged_in();
?>

<div id="top-bar-wrapper">
    <div id="top-bar-inner-wrapper">
        <div class="grid-x top-bar">

            <div class="cell small-6 large-2" id="top-logo-div">
                <a href="<?php echo esc_url( site_url() ) ?>">
                    <div class="dtps-logo-in-top-bar"></div>
                </a>
            </div>

            <div class="cell large-8 hide-for-small show-for-large" id="top-full-menu-div-wrapper">
                <div id="top-full-menu-div">
                    <?php dtps_top_nav(); ?>
                </div>
            </div>
            <div class="cell large-2 hide-for-small show-for-large">
                <a class="green-button float-right" href="/launch-demo/">Demo</a>
            </div>

            <div class="cell small-6 show-for-small hide-for-large" id="top-mobile-menu-div">
                <div class="mobile-menu">
                    <a href="javascript:void(0)" data-open="search-box" rel="nofollow" title="Search" style="padding-right:15px;"><i class="fi-magnifying-glass large-text"></i></a>
                    <a data-toggle="off-canvas" style="cursor:pointer; float: right;"><img src="<?php echo esc_url( dtps_images_uri() . 'hamburger.svg' ) ?>" alt="menu" /></a>
                </div>
            </div>

        </div>
    </div>
</div>

