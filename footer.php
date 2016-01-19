<?php
/**
 * Princeton 2006 template for displaying the footer
 *
 * @package WordPress
 * @subpackage Princeton 2006
 * @since Princeton 2006 1.0
 */
?>
        <div class="footer-centered-content">
          <div class="footer-bumper">
    				<ul class="footer-widgets"><?php
    					if ( function_exists( 'dynamic_sidebar' ) ) :
    						dynamic_sidebar( 'footer-sidebar' );
    					endif; ?>

              <!-- <p>Copyright &copy; <?= date('Y') ?> Princeton Class of 2006</p> -->
              <div class="home-logo"></div>
    				</ul>


          </div>
        </div>

			</div>
		<?php wp_footer(); ?>
	</body>
</html>
