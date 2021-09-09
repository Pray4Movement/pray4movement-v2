<div>
    <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url() ) ?>">
        <div class="input-group large">
            <input type="search" class="input-group-field search-field" placeholder="Search Documentation ..." value="" name="s" title="Search for:">
            <input type="hidden" name="post_type[]" value="news" />
            <div class="input-group-button">
                <input type="submit" class="search-submit button" value="Search">
            </div>
        </div>
    </form>
</div>
