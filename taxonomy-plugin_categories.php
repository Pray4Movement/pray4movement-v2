<?php
$page_term = get_term( get_queried_object()->term_id );
?>


<?php get_header(); ?>

<div class="page-wrapper">

    <div class="page-inner-wrapper-hd">


        <!-- Main -->
        <main role="main" id="post-main" >

            <div class="grid-x grid-margin-x grid-padding-x">

                <div class="cell medium-9 large-8">

                    <div class="grid-x">
                        <div class="cell center padding-1">
                            <h3 class="center"><?php echo esc_html( $page_term->name ) ?> Plugins</h3>
                            <?php echo esc_html( $page_term->description ) ?>
                        </div>
                    </div>

                    <div class="grid-x grid-padding-x" data-equalizer data-equalize-on="medium"> <!-- grid-->

                        <?php
                        $tax_query =[
                            [
                                'taxonomy' => 'plugin_categories',
                                'field'    => 'slug',
                                'terms'    => $page_term->slug,
                                'operator' => 'IN'
                            ],
                        ];

                        $loop = new WP_Query(
                            [
                            'post_type' => 'plugins',
                                'order' => 'ASC',
                                'orderby' => 'post_title',
                                'posts_per_page' => 50,
                                'tax_query' => $tax_query
                            ]
                        );

                        if ( $loop->have_posts() ) :
                            while ( $loop->have_posts() ) : $loop->the_post(); ?>
                                <?php get_template_part( 'parts/loop', 'plugin-tile' ); ?>
                            <?php endwhile;
                        endif;
                        wp_reset_postdata();

                        ?>

                    </div> <!-- end grid -->

                </div>

                <div class="sidebar cell medium-3 large-4" style="padding-right:20px;margin-top:75px">

                    <hr class="show-for-small-only" />

                    <?php get_template_part( 'parts/content', 'plugin-search' ); ?>

                    <hr>

                    <div>
                        <h4>What's a Plugin?</h4>
                        <div class="padding-left-1">
                            <p>
                                Plugins are ways of extending the Disciple.Tools system to meet the unique needs of your project, ministry, or movement.
                            </p>
                            <p>
                                The power of our open source model is that you don't have to wait on us! Using our starter plugin templates, complete sections can be
                                added to Disciple.Tools to track and steward ministry information important to you.
                            </p>
                        </div>
                    </div>
                    <hr>

                    <?php get_template_part( 'parts/content', 'plugin-makelist' ); ?>

                    <hr>

                    <h4>Plugin Categories</h4>
                    <div class="padding-left-1">
                        <a href="/plugins/">All Plugins</a> (<?php echo wp_count_posts( 'plugins' )->publish ?>)
                        <?php wp_list_categories(
                            [
                                'show_count' => 1,
                                'taxonomy' => 'plugin_categories',
                                'title_li' => ''
                            ] ) ?>
                    </div>

                    <hr>

                    <?php
                    /*
                    if ( !isset( $page_term->slug ) || $page_term->slug !== "featured" ) : ?>
                        <h4>Featured Plugins</h4>
                        <?php
                        $loop = new WP_Query(
                            [
                            'post_type' => 'plugins',
                                'nopaging' => true,
                                'orderby' => 'rand',
                                'tax_query' => [
                                    [
                                        'taxonomy' => 'plugin_categories',
                                        'field'    => 'slug',
                                        'terms'    => 'featured',
                                    ],
                                ]
                            ]
                        );
                    else: ?>
                        <h4>Other Plugins</h4>
                        <?php
                        $loop = new WP_Query(
                            [
                            'post_type' => 'plugins',
                                'nopaging' => true,
                                'orderby' => 'post_title',
                                'order' => 'ASC',
                                'tax_query' => [
                                    [
                                        'taxonomy' => 'plugin_categories',
                                        'field'    => 'slug',
                                        'terms'    => 'disciple-tools',
                                        'operator' => "IN"
                                    ],
                                    'relation' => 'AND',
                                ]
                            ]
                        );
                    endif ?>
                    <div class="padding-left-1">
                        <table>
                            <thead>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Author
                                </th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php

                            if ( $loop->have_posts() ) :
                                while ( $loop->have_posts() ) : $loop->the_post(); ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo esc_url( get_permalink() ) ?>"><?php the_title() ?></a>
                                        </td>
                                        <td>
                                            <?php echo esc_html( get_post_meta( get_the_ID(), 'author', true ) ) ?>
                                        </td>
                                    </tr>
                                <?php endwhile;
                            endif;
                            wp_reset_postdata();
                            ?>
                            </tbody>
                        </table>
                    </div>

                    <?php */ ?>

                </div>

            </div>

        </main> <!-- end #main -->

    </div>

</div>

<?php get_footer(); ?>
