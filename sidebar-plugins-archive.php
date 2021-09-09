<?php
/**
 * The sidebar containing the main widget area
 */
?>

<hr class="show-for-small-only" />

<?php get_template_part( 'parts/content', 'plugin-search' ); ?>
<hr>
<div>
    <h2>What's a Plugin?</h2>
    <p>
        Plugins are ways of extending the Disciple Tools system to meet the unique needs of your project, ministry, or movement.
    </p>
    <p>
        The power of our open source model is that you don't have to wait on us! Using our starter plugin templates, complete sections can be
        added to Disciple Tools to track and steward ministry information important to you.
    </p>

</div>
<hr>
<?php get_template_part( 'parts/content', 'plugin-makelist' ); ?>
<hr>

<div class="grid-x">
    <h4>Community Plugins</h4>
    <div class="cell">
        <table>
            <thead>
            <tr>
                <th>
                    Name
                </th>
            </tr>
            </thead>
            <tbody>

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
                            'terms'    => 'community',
                            'operator' => 'IN'
                        ],
                    ]
                ]
            );
            if ( $loop->have_posts() ) :
                while ( $loop->have_posts() ) : $loop->the_post(); ?>
                    <tr>
                        <td>
                            <a href="<?php echo esc_url( get_permalink() ) ?>"><?php the_title() ?></a>
                        </td>
                    </tr>
                <?php endwhile;
            endif;
            wp_reset_postdata();
            ?>
            </tbody>
        </table>
    </div>
</div>
