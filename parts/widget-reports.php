<div class="grid-x news-section">
    <?php
    $the_query = new WP_Query( 'posts_per_page=6' );
    while ($the_query->have_posts()) : $the_query->the_post();
        ?>
        <div class="cell medium-2 card margin-horizontal-1">
            <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_permalink() ?>"><?php the_post_thumbnail( '200' ); ?></a>
            <?php endif; ?>
            <div class="card-section">
                <a href="<?php the_permalink() ?>"><?php the_title(); ?></a>
            </div>
        </div>
        <?php
    endwhile;
    wp_reset_postdata();
    ?>
</div>
