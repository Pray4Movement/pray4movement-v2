<?php
/**
 * The sidebar containing the main widget area
 */
?>

<div id="report" class="sidebar cell" role="complementary">

    <div>
        <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url() ) ?>">
            <div class="input-group large">
                <input type="search" class="input-group-field search-field" placeholder="Search News ..." value="" name="s" title="Search for:">
                <input type="hidden" name="post_type[]" value="news" />
                <div class="input-group-button">
                    <input type="submit" class="search-submit button" value="Search">
                </div>
            </div>
        </form>
    </div>

    <hr>
    <?php get_template_part( 'parts/content', 'news-subscribe' ); ?>
    <hr>

    <div class="padding-horizontal-1">
        <h3>Sort By</h3>
        <p>By Month<br>
            <select onchange="window.location = jQuery(this).val()">
                <option></option>
                <?php wp_get_archives(array(
                    'type' => 'monthly',
                    'show_post_count' => true,
                    'post_type' => 'news',
                    'format' => 'option'
                )) ?>
            </select>
        </p>

        <p>By Category<br>
            <select onchange="window.location = jQuery(this).val()">
                <option></option>
                <?php
                $categories = get_categories(array(
                    'hide_empty' => true,
                    'taxonomy' => 'news_categories',
                ));
                foreach ( $categories as $category ) {
                    echo '<option value="'.esc_url( site_url() ).'/news-categories/'. esc_attr( $category->slug ).'">' . esc_html( $category->name ) . '</option>';
                }
                ?>
            </select>
        </p>

    </div>

    <hr>

    <div class="padding-horizontal-1 center">
        <a href="/news/feed">RSS Feed</a>
    </div>
    <div class="padding-horizontal-1 center">
        <a href="/news/?format=compact">Compact Format</a>
    </div>

</div>
