<?php
/**
 * The template part for displaying offcanvas content
 *
 * For more info: http://dtps.com/docs/off-canvas-menu/
 */
?>

<div class="off-canvas position-right" id="off-canvas" data-off-canvas>
    <?php dtps_off_canvas_nav(); ?>

    <?php if ( is_active_sidebar( 'offcanvas' ) ) : ?>

        <?php dynamic_sidebar( 'offcanvas' ); ?>

    <?php endif; ?>

</div>
