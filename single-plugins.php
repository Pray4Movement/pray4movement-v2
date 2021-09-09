<?php
/**
 * The template for displaying all single posts and attachments
 */

// build defaults
$plugin_post_type = DTPS_Plugins_Post_Type::instance();
$raw_defaults = array_keys( $plugin_post_type->get_custom_fields_settings() );
$plugin_defaults = [];
foreach ( $raw_defaults as $row ) {
    $plugin_defaults[$row] = '';
}

// get post values
$post = get_post();
$post_meta = wp_parse_args( dtps_filter_meta( get_post_meta( $post->ID ) ), $plugin_defaults );

$release = [];
$version_control_url = get_post_meta( $post->ID, 'version_control_url', true );
if ( isset( $version_control_url ) && ! empty( $version_control_url ) ) {
    $result = wp_remote_get( $version_control_url );
    if ( ! is_wp_error( $result ) ) {
        $release = json_decode( $result['body'], true );
    }
}

get_header(); ?>

<div class="page-wrapper">
    <div class="page-inner-wrapper">

        <!-- Bread Crumbs-->
        <nav id="post-nav" class="padding-bottom-1">
            <div class="breadcrumb hide-for-small-only">
                <a href="<?php echo esc_url( home_url() ); ?>" rel="nofollow">Home</a>&nbsp;&nbsp;&#187;&nbsp;&nbsp;
                <a href="<?php echo esc_url( home_url() ); ?>/plugins">Plugins</a>&nbsp;&nbsp;&#187;&nbsp;&nbsp;
                <?php echo esc_html( the_title() ) ?>
            </div>
            <div class="breadcrumb-mobile show-for-small-only"><a href="<?php echo esc_url( home_url() ); ?>/plugins">Plugins</a></div>
        </nav>

        <!-- Main -->
        <main role="main" id="post-main">

            <div class="grid-x grid-margin-x grid-padding-x">

                    <div class="blog cell large-8">

                        <article id="post-<?php the_ID(); ?>" <?php post_class( '' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

                            <header class="article-header center">

                                <?php if ( has_post_thumbnail() ) : ?>
                                    <div><?php the_post_thumbnail( 'full' ); ?></div>
                                <?php endif; ?>

                                <h2 class="entry-title single-title vertical-padding" itemprop="headline"><?php the_title(); ?></h2>

                                <?php if ( is_object_in_term( get_the_ID(), 'plugin_categories','beta' ) ) : ?>
                                    <a class="button small warning">BETA</a>
                                <?php endif; ?>
                                <?php if ( is_object_in_term( get_the_ID(), 'plugin_categories','proof-of-concept' ) ) : ?>
                                    <a class="button small warning">Proof of Concept</a>
                                <?php endif; ?>

                                <div class="center"><a href="https://github.com/<?php echo esc_attr( $post_meta['github_owner'] ) ?>/<?php echo esc_attr( $post_meta['github_repo'] ) ?>/releases/latest/download/<?php echo esc_attr( $post_meta['github_repo'] ) ?>.zip" class="button"><i class="fi-download"></i> Download</a> </div>

                            </header> <!-- end article header -->

                            <hr>

                            <section class="entry-content" itemprop="text">

                                <?php
                                // STRING
                                $string = FALSE;
                                if ( $post_meta['readme_url'] ) { /* If readme url is present, then the Readme markdown is used */
                                    $string = file_get_contents( $post_meta['readme_url'] );
                                }
                                // end check on readme existence


                                // if string exists
                                if( $string !== FALSE ) {  /* Use the content section of the post */
                                    $Parsedown = new Parsedown();
                                    echo $Parsedown->text( $string );
                                }
                                // end readme render


                                // render the post content
                                if( $string === FALSE ) {

                                    ?>

                                    <h2><?php echo esc_html( $post_meta['name'] ) ?></h2>

                                    <p><strong>Description</strong></p>

                                    <?php if (have_posts()) : ?>

                                        <?php while (have_posts()) : the_post(); ?>

                                            <?php the_content(); ?>

                                        <?php endwhile; ?>

                                    <?php else : ?>

                                        <p><?php echo nl2br( esc_html( $post_meta['description'] ) ) ?></p>

                                    <?php endif; ?>

                                    <p><strong>Installation</strong></p>

                                    <p><?php echo esc_html( nl2br( $post_meta['installation'] ) ) ?></p>

                                <?php } // end render post content ?>

                            </section> <!-- end article section -->

                            <footer class="article-footer">

                                <p class="tags"><?php the_tags( '<span class="tags-title">' . __( 'Tags:', 'dtps' ) . '</span> ', ', ', '' ); ?></p>

                            </footer> <!-- end article footer -->

                        </article>

                    </div>

                    <div class="sidebar cell large-4 ">

                        <hr class="show-for-small-only" />

                        <?php get_template_part( 'parts/content', 'plugin-search' ); ?>

                        <hr>

                        <h4>Plugin Author</h4>
                        <div class="padding-left-1">
                            <a href="<?php echo esc_url( get_post_meta( get_the_ID(), 'author_homepage', true ) ) ?>">
                                <?php echo esc_html( get_post_meta( get_the_ID(), 'author', true ) ) ?>
                            </a>
                        </div>

                        <hr>

                        <h4>Plugin Links</h4>
                        <div class="grid-x">
                            <div class="cell">
                                <?php if ( isset( $post_meta['wiki_url'] ) && ! empty( $post_meta['wiki_url'] ) ) : ?>
                                    <a href="<?php echo esc_url( $post_meta['wiki_url'] ) ?>" class="button primary-button-hollow expanded"> Documentation</a>
                                <?php endif; ?>
                            </div>
                            <div class="cell">
                                <?php if ( isset( $post_meta['discussions_url'] ) && ! empty( $post_meta['discussions_url'] ) ) : ?>
                                    <a href="<?php echo esc_url( $post_meta['discussions_url'] ) ?>" class="button primary-button-hollow expanded"> Discussions</a>
                                <?php endif; ?>
                            </div>
                            <div class="cell">
                                <?php if ( isset( $post_meta['homepage'] ) && ! empty( $post_meta['homepage'] ) ) : ?>
                                    <a href="<?php echo esc_url( $post_meta['homepage'] ) ?>" class="button primary-button-hollow expanded"> Source Code</a>
                                <?php endif; ?>
                            </div>

                            <?php if ( isset( $post_meta['issues_url'] ) && ! empty( $post_meta['issues_url'] ) ) : ?>
                                <div class="cell medium-4" style="padding-right:5px;">
                                   <a href="<?php echo esc_url( $post_meta['issues_url'] ) ?>" class="button primary-button-hollow expanded"> Issues</a>
                                </div>
                            <?php endif; ?>
                            <?php if ( isset( $post_meta['projects_url'] ) && ! empty( $post_meta['projects_url'] ) ) : ?>
                                <div class="cell medium-4">
                                   <a href="<?php echo esc_url( $post_meta['projects_url'] ) ?>" class="button primary-button-hollow expanded"> Projects</a>
                                </div>
                            <?php endif; ?>
                            <?php if ( isset( $post_meta['license_url'] ) && ! empty( $post_meta['license_url'] ) ) : ?>
                                <div class="cell medium-4" style="padding-left:5px;">
                                   <a href="<?php echo esc_url( $post_meta['license_url'] ) ?>" class="button primary-button-hollow expanded"> License</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <?php if ( ! empty( $post_meta['github_owner'] ) ) : ?>
                        <h4>Version Info</h4>
                        <div class="padding-left-1">
                            <div id="current_version"></div>
                            <p id="past_versions" style="display:none;"><a href="javascript:void(0)" onclick="jQuery('#releases').toggle()">Show Previous Versions</a></p>
                        </div>

                        <div id="releases" style="display:none;"></div>
                        <script>
                            jQuery(document).ready(function(){
                                jQuery.getJSON('https://api.github.com/repos/<?php echo esc_attr( $post_meta['github_owner'] ) ?>/<?php echo esc_attr( $post_meta['github_repo'] ) ?>/releases', function(data) {
                                    let r = jQuery('#releases')

                                    r.append(`<p><h4>Past Versions</h4></p><table class="hover">
                                            <tbody id="release_list"></tbody></table>`)

                                    let rl = jQuery('#release_list')
                                    let cv = jQuery('#current_version')
                                    let pv = jQuery('#past_versions')

                                    jQuery.each( data, function(i,v){
                                        if ( v.draft === true ) {
                                            return
                                        }

                                        let str = nl2br(v.body)
                                        if ( 0 === i ) {
                                            cv.append(`<strong>${v.tag_name}</strong><br>${str}`)
                                        } else {
                                            pv.show()
                                            rl.append(`<tr><td><strong>${v.tag_name}</strong><br>${str}</td></tr>`)
                                        }
                                    })
                                })
                            })
                            function nl2br (str, is_xhtml) {
                                var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
                                return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
                            }
                        </script>
                        <hr>
                        <?php endif; ?>

                        <?php get_template_part( 'parts/content', 'plugin-makelist' ); ?>

                    </div>

            </div>

        </main> <!-- end #main -->

    </div>
</div>

<?php get_footer(); ?>
