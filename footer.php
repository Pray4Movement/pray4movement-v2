<?php
/**
 * The template for displaying the footer
 *
 * Contains the opening of the #site-footer div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Pray4Movement 1.0
 */

?>
			<footer id="site-footer" role="contentinfo" class="header-footer-group">

				<div class="section-inner">

					<div class="footer-credits">

						<p class="footer-copyright">&copy;
							<?php
							echo date_i18n(
								/* translators: Copyright date format, see https://www.php.net/manual/datetime.format.php */
								_x( 'Y', 'copyright date format', 'pray4movement' )
							);
							?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Prayer.Tools</a> by <a href='https://GospelAmbition.org' target='_blank'>GospelAmbition.org</a>
						</p><!-- .footer-copyright -->



					</div><!-- .footer-credits -->
                    <div>
                        <p>
                            <a href="/about">About</a> | <a href="/join">Join</a> | <a href="/give" target="_blank">Give</a> | <a href="https://forum.prayer.tools/" target="_blank">Forum</a>
                        </p>
                    </div>

					<a class="to-the-top" href="#site-header">
						<span class="to-the-top-long">
							<?php
							/* translators: %s: HTML character for up arrow. */
							printf( __( 'To the top %s', 'pray4movement' ), '<span class="arrow" aria-hidden="true">&uarr;</span>' );
							?>
						</span><!-- .to-the-top-long -->
						<span class="to-the-top-short">
							<?php
							/* translators: %s: HTML character for up arrow. */
							printf( __( 'Up %s', 'pray4movement' ), '<span class="arrow" aria-hidden="true">&uarr;</span>' );
							?>
						</span><!-- .to-the-top-short -->
					</a><!-- .to-the-top -->

				</div><!-- .section-inner -->

			</footer><!-- #site-footer -->

		<style>
			helpinator-widget {
				--helpinator-primary: #DC3822;
				--helpinator-on-primary: #ffffff;
				--helpinator-bg: #ffffff;
				--helpinator-text: #000000;
				--helpinator-muted: #6d6d6d;
				--helpinator-border: #dcd7ca;
				--helpinator-bot-bubble: #f5efe0;
				--helpinator-font: inherit;
				--helpinator-radius: 0;
				--helpinator-offset-x: 20px;
				--helpinator-offset-y: 20px;
			}
		</style>
		<script src="https://apps.gospelambition.org/js/helpinator-widget.iife.js" defer></script>
		<helpinator-widget host="https://apps.gospelambition.org" widget-id="40db5f2e-f8a9-45d6-871b-05ed93146b8a"></helpinator-widget>

		<?php wp_footer(); ?>

	</body>
</html>
