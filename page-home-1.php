<?php
/**
 * Template Name: Landing Page (Simple Black)
 * Princeton 2006 template for displaying the Front-Page
 *
 * @package WordPress
 * @subpackage Princeton 2006
 * @since Princeton 2006 1.0
 */

get_header(); ?>

	<section class="page-content primary" role="main">
		<div class="home-widgets-container">
			<ul class="home-widgets">
			<?php
				if ( function_exists( 'dynamic_sidebar' ) ) :
					dynamic_sidebar( 'home-sidebar' );
				endif;
			?>
			</ul>
		</div>
	</section>

<?php get_footer(); ?>
