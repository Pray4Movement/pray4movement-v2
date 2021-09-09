<?php
/**
 * The template for displaying the footer.
 *
 * Comtains closing divs for header.php.
 *
 * For more info: https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */
?>

                <footer class="footer" role="contentinfo">

                    <!-- Footer Menu -->
                    <div class="page-wrapper" id="footer-menu-section">
                        <div class="page-inner-wrapper">
                            <div class="grid-x grid-padding-x">
                                <div class="cell medium-4">
                                    <img class="hfe-retina-img elementor-animation-" src="/wp-content/uploads/2020/04/dt-emblem.png" srcset="/wp-content/uploads/2020/04/dt-emblem.png 1x,/wp-content/uploads/2020/04/dt-emblem-150px.png 2x">
                                </div>
                                <div class="cell medium-2">
                                    <p>
                                        <strong>Product</strong><br>
                                        <a href="/features/">Features</a><br>
                                        <a href="/security/">Security</a><br>
                                        <a href="/pricing/">Pricing</a><br>
                                        <a href="/launch-demo/">Demo</a>
                                    </p>
                                </div>
                                <div class="cell medium-2">
                                    <p>
                                        <strong>Solutions</strong><br>
                                        <a href="/for-online-strategies/">For Online Strategies</a><br>
                                        <a href="/for-small-teams/">For Small Teams</a><br>
                                        <a href="/for-multiple-teams/">For Multiple Teams</a><br>
                                        <a href="https://kingdom.training">Kingdom.Training</a>
                                    </p>
                                </div>
                                <div class="cell medium-2">
                                    <p>
                                        <strong>Resources</strong><br>
                                        <a href="/news/">News</a><br>
                                        <a href="/user-docs/">User Docs</a><br>
                                        <a href="/plugins/">Plugins</a><br>
                                        <a href="https://www.youtube.com/channel/UCwQQSXUJunyqnj1bL_Fh6mQ/playlists">Youtube Training</a><br>
                                    </p>
                                </div>
                                <div class="cell medium-2">
                                    <p>
                                        <strong>Developers</strong><br>
                                        <a href="/dev-docs/">Developer Docs</a><br>
                                        <a href="/open-source/">Open Source</a><br>
                                        <a href="https://github.com/DiscipleTools">Github Project</a><br>
                                        <a href="/join-the-community/">Join the Community</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Footer Contact Bar-->
                    <div class="page-wrapper " id="footer-contact-bar">
                        <div class="page-inner-wrapper">
                            <div class="grid-x grid-padding-x">
                                <div class="cell small-6" style="padding-top:15px;"><span><a href="/join-the-community/">Contact Us</a></span></div>
                                <div class="cell small-6 "><span class="float-right"><a href="https://www.youtube.com/channel/UCwQQSXUJunyqnj1bL_Fh6mQ"><i class="fi-social-youtube"></i></a> <a href="https://github.com/DiscipleTools"><i class="fi-social-github"></i></a></span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Contact Bar-->
                    <div class="page-wrapper" id="footer-copyright-bar">
                        <div class="page-inner-wrapper">
                            <div class="grid-x">
                                <div class="cell copyright-content">&copy; <?php echo esc_html( gmdate( 'Y' ) ) ?> Disciple.Tools</div>
                            </div>
                        </div>
                    </div>

                    <!-- Search modal-->
                    <div class="reveal" id="search-box" data-reveal>
                        <h1>Search</h1>
                        <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url() ) ?>">
                            <div class="input-group large">
                                <input type="search" class="input-group-field search-field" placeholder="Search..." value="" name="s" title="Search for:">
                                <div class="input-group-button">
                                    <input type="submit" class="search-submit button" value="Search">
                                </div>
                            </div>
                            <button class="close-button" data-close aria-label="Close modal" type="button">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </form>
                    </div>

                </footer> <!-- end .footer -->

            </div>  <!-- end .off-canvas-content -->

        </div> <!-- end .off-canvas-wrapper -->

        <?php wp_footer(); ?>

    </body>

</html> <!-- end page -->
