<?php
/**
 * Princeton 2006 template for displaying the Front-Page
 *
 * @package WordPress
 * @subpackage Princeton 2006
 * @since Princeton 2006 1.0
 */

get_header(); ?>

	<section class="page-content primary" role="main">
		<?php
			if ( have_posts() ) :

				while ( have_posts() ) : the_post();

					get_template_part( 'loop', get_post_format() );

				endwhile;

			else :

				get_template_part( 'loop', 'empty' );

			endif;
		?>
		<div class="pagination">

			<?php get_template_part( 'template-part', 'pagination' ); ?>

		</div>
	</section>

<?php get_footer(); ?>
