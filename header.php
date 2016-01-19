<?php
/**
 * Princeton 2006 template for displaying the header
 *
 * @package WordPress
 * @subpackage Princeton 2006
 * @since Princeton 2006 1.0
 */

$page_title = trim(wp_title( false, false ));
if( empty($page_title) && (is_home() || is_front_page())) {
	$page_title = get_bloginfo( 'name' );
}
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="ie ie-no-support" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>         <html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>         <html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9]>         <html class="ie ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?php echo $page_title; ?></title>
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
		<!--[if lt IE 9]>
			<script src="<?php echo get_template_directory_uri(); ?>/js/html5shiv.js"></script>
		<![endif]-->
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<div class="site">

			<span class="mobile-title modal">
				<span class="modal-toggle light"></span>
				<h2 class="blog-name"><?php bloginfo( 'name' ); ?></h2>
			</span>

			<header class="site-header">

				<div class="nav-logo-item">	</div>

				<a class="logo" href="<?php echo home_url(); ?>" title="<?php bloginfo( 'name' ); ?>">
					<h2 class="blog-name"><?php bloginfo( 'name' ); ?></h2>
					<div class="blog-description"><?php bloginfo( 'description' ); ?></div>
				</a>

				<!-- <div class="navigation-menu"> -->
					<?php
						$menu = wp_get_nav_menu_object( 'Main' );
						$items = wp_get_nav_menu_items($menu->term_id);
					?>

				<!-- </div> -->


				<div class="top-bar-links">
					<?php
						wp_nav_menu(array(
							'theme_location'  => 'give-back-menu',
							'menu_class'      => 'give-back-links',
							'container'       => false,
							'fallback_cb'     => false,
							'items_wrap'      => '<ul id="%1$s" class="%2$s"><li class="label">Give Back:</li>%3$s</ul>',
						));

						wp_nav_menu(array(
							'theme_location'  => 'social-links-menu',
							'menu_class'      => 'nav-social-links',
							'container'       => false,
							'fallback_cb'     => false,
							'items_wrap'      => '<ul id="%1$s" class="%2$s"><li class="label">Follow Us:</li>%3$s</ul>',
						));

						wp_nav_menu(array(
							'theme_location'  => 'profile-links-menu',
							'menu_class'      => 'profile-links',
							'container'       => false,
							'fallback_cb'     => false,
							'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						));

					?>
				</div>
			</header>

			<nav class="main-menu">
				<ul class="menu">
					<?php
					$counter = 0;
					foreach($items as $item):
						$title = $item->title;
						$classes = $item->classes;
						$classes_string = $classes == '' ? '' : join($classes, ' ');
						$outer_classes = in_array('mobile-only', $classes) ? 'mobile-only' : '';
						
						if($counter % 2 == 0){
							echo "<span>";
						}?>

						<li id="menu-item-<?= $item->ID ?>" class="<?= $outer_classes ?> menu-item menu-item-type-custom menu-item-object-custom menu-item-home menu-item-<?= $item->ID ?>">
							<span class="item-wrapper">
								<a href="<?= $item->url ?>" class="<?= $classes_string ?>">
									<span class="item-title"><?= pu06_string_to_spans($title) ?></span>
								</a>
							</span>
						</li>

					<?php
						if($counter % 2 > 0){
							echo "</span>";
						}
						$counter++;
						endforeach;
					?>

				</ul>
			</nav>

			<div class="quick-links-modal modal">
				<ul class="quick-links">
					<span class="modal-toggle"></span>
					<?php
						if ( function_exists( 'dynamic_sidebar' ) ) :
							dynamic_sidebar( 'pu06-dues-widgets' );
						endif;
					?>
				</ul>
			</div>
