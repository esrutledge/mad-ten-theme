<?php
/**
 * Princeton 2006 functions file
 *
 * @package WordPress
 * @subpackage Princeton 2006
 * @since Princeton 2006 1.0
 */


/******************************************************************************\
	Theme support, standard settings, menus and widgets
\******************************************************************************/

// eff youuuuu admin bar
add_filter('show_admin_bar', '__return_false');

add_theme_support( 'post-formats', array( 'image', 'quote', 'status', 'link' ) );
add_theme_support( 'post-thumbnails' );
add_theme_support( 'automatic-feed-links' );

$custom_header_args = array(
	'width'         => 980,
	'height'        => 300,
	'default-image' => get_template_directory_uri() . '/images/header.png',
);
add_theme_support( 'custom-header', $custom_header_args );

/**
 * Print custom header styles
 * @return void
 */
function pu06_custom_header() {
	$styles = '';
	if ( $color = get_header_textcolor() ) {
		echo '<style type="text/css"> ' .
				'.site-header .logo .blog-name, .site-header .logo .blog-description {' .
					'color: #' . $color . ';' .
				'}' .
			 '</style>';
	}
}
add_action( 'wp_head', 'pu06_custom_header', 11 );

$custom_bg_args = array(
	'default-color' => 'fba919',
	'default-image' => '',
);
add_theme_support( 'custom-background', $custom_bg_args );

register_nav_menu( 'main-menu', __( 'Main Menu', 'pu06' ) );
register_nav_menu( 'social-links-menu', __( 'Social Links', 'pu06' ) );
register_nav_menu( 'give-back-menu', __( 'Give Back Links', 'pu06' ) );
register_nav_menu( 'profile-links-menu', __( 'Profile Links', 'pu06' ) );

if ( function_exists( 'register_sidebars' ) ) {
	register_sidebar(
		array(
			'id' => 'home-sidebar',
			'name' => __( 'Home widgets', 'pu06' ),
			'description' => __( 'Shows on home page', 'pu06' )
		)
	);

	register_sidebar(
		array(
			'id' => 'footer-sidebar',
			'name' => __( 'Footer widgets', 'pu06' ),
			'description' => __( 'Shows in the sites footer', 'pu06' )
		)
	);

	register_sidebar(
		array(
			'id' => 'pu06-dues-widgets',
			'name' => __( '06 Dues Widgets', 'pu06' ),
			'description' => __( 'Shows in a modal or sidebar, used for dues widgets', 'pu06' )
		)
	);

}

if ( ! isset( $content_width ) ) $content_width = 650;

/**
 * Include editor stylesheets
 * @return void
 */
function pu06_editor_style() {
    add_editor_style( 'css/wp-editor-style.css' );
}
add_action( 'init', 'pu06_editor_style' );


/******************************************************************************\
	Scripts and Styles
\******************************************************************************/

/**
 * Enqueue pu06 scripts
 * @return void
 */
function pu06_enqueue_scripts() {
	wp_enqueue_style( 'pu06-styles', get_stylesheet_uri(), array(), '1.0' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'default-scripts', get_template_directory_uri() . '/js/scripts.min.js', array(), '1.0', true );
	if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'pu06_enqueue_scripts' );


/******************************************************************************\
	Content functions
\******************************************************************************/

/**
 * Displays meta information for a post
 * @return void
 */
function pu06_post_meta() {
	if ( get_post_type() == 'post' ) {
		echo sprintf(
			__( 'Posted %s in %s%s by %s. ', 'pu06' ),
			get_the_time( get_option( 'date_format' ) ),
			get_the_category_list( ', ' ),
			get_the_tag_list( __( ', <b>Tags</b>: ', 'pu06' ), ', ' ),
			get_the_author_link()
		);
	}
	edit_post_link( __( ' (edit)', 'pu06' ), '<span class="edit-link">', '</span>' );
}

/**
 * Split string into sections (first by colon then by count)
 * @return void
 */
function pu06_string_to_spans($string) {
	$words = explode(' ', $string);
	$string_sections = explode(':', $string);
	$string_formatted = '';

	if(count($string_sections) > 1) {
		$string_sections[0] = '<span class="accent-color">' . $string_sections[0] . ':</span><span>';
		$string_formatted = join('', $string_sections) . '</span>';
	} else {
		$count = count($words);
		$shift = $count > 7 ? 2 : ( $count > 5 ? 1 : 0 );
		$first_section_count = floor($count/2) - $shift - 1;
		$words[0] = '<span class="accent-color">' . $words[0];
		$words[$first_section_count] = $words[$first_section_count] . '</span><span>';
		$string_formatted = join(' ', $words) . '</span>';
	}

	return $string_formatted;
}


