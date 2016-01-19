<?php
/**
 * Template Name: Profile Page
 * Princeton 2006 template for displaying Pages
 *
 * @package WordPress
 * @subpackage Princeton 2006
 * @since Princeton 2006 1.0
 */

get_header(); ?>

	<section class="page-content primary" role="main">

		<?php
			if ( have_posts() ) : the_post();

				get_template_part( 'loop-profile' ); ?>

				<aside class="post-aside"><?php

					wp_link_pages(
						array(
							'before'           => '<div class="linked-page-nav"><p>' . sprintf( __( '<em>%s</em> is separated in multiple parts:', 'pu06' ), get_the_title() ) . '<br />',
							'after'	           => '</p></div>',
							'next_or_number'   => 'number',
							'separator'	       => ' ',
							'pagelink'	       => __( '&raquo; Part %', 'pu06' ),
						)
					); ?>

					<?php
						if ( comments_open() || get_comments_number() > 0 ) :
							comments_template( '', true );
						endif;
					?>

				</aside><?php

			else :

				get_template_part( 'loop-profile', 'empty' );

			endif;
		?>

	</section>

<?php get_footer(); ?>
