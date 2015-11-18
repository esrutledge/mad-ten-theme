<?php
/**
 * Princeton 2006 template for displaying the standard Loop
 *
 * @package WordPress
 * @subpackage Princeton 2006
 * @since Princeton 2006 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>

	<h1 class="post-title"><?php

	$title = get_the_title();
	$title_formatted = '';

	$accent_text_override = get_field('accent_color_text_override');

	if($accent_text_override && $accent_text_override != '') {
		$title_formatted = '<span>' . str_replace($accent_text_override, '</span><span class="accent-color">' . $accent_text_override . '</span>', $title);
	} else {
		$title_formatted = pu06_string_to_spans($title);
	}

		if ( is_singular() ) :
			echo $title_formatted;

		else : ?>

			<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php
				echo $title_formatted; ?>
			</a><?php

		endif; ?>

	</h1>

	<!-- <div class="post-meta"><?php
		pu06_post_meta(); ?>
	</div> -->

	<div class="post-content"><?php

		if ( '' != get_the_post_thumbnail() ) : ?>
			<?php the_post_thumbnail(); ?><?php
		endif; ?>

		<?php if ( is_front_page() || is_category() || is_archive() || is_search() ) : ?>

			<?php the_excerpt(); ?>
			<a class="read-more" href="<?php the_permalink(); ?>"><?php _e( 'Read more &raquo;', 'pu06' ); ?></a>

		<?php else : ?>

			<?php the_content( __( 'Continue reading &raquo', 'pu06' ) ); ?>

		<?php endif; ?>

		<?php
			wp_link_pages(
				array(
					'before'           => '<div class="linked-page-nav"><p>'. __( 'This article has more parts: ', 'pu06' ),
					'after'            => '</p></div>',
					'next_or_number'   => 'number',
					'separator'        => ' ',
					'pagelink'         => __( '&lt;%&gt;', 'pu06' ),
				)
			);
		?>

	</div>

</article>
