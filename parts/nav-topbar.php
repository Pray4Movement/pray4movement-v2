<?php
/**
 * The off-canvas menu uses the Off-Canvas Component
 *
 * For more info: http://dtps.com/docs/responsive-navigation/
 */
?>

<div class="top-bar" id="main-menu">
    <div class="top-bar-left">
        <ul class="menu">
            <li><a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a></li>
        </ul>
    </div>
    <div class="top-bar-right">
        <?php dtps_top_nav(); ?>
    </div>
</div>
