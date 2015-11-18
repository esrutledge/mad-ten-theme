<?php
/**
 * Princeton 2006 template for displaying the header
 *
 * @package WordPress
 * @subpackage Princeton 2006
 * @since Princeton 2006 1.0
 */
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
		<title><?php wp_title( false ); ?></title>
		<meta name="viewport" content="width=device-width" />
		<!--[if lt IE 9]>
			<script src="<?php echo get_template_directory_uri(); ?>/js/html5shiv.js"></script>
		<![endif]-->
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<div class="site">

			<header class="site-header">

				<a class="logo" href="<?php echo home_url(); ?>" title="<?php bloginfo( 'name' ); ?>">
					<h1 class="blog-name"><?php bloginfo( 'name' ); ?></h1>
					<div class="blog-description"><?php bloginfo( 'description' ); ?></div>
				</a>

				<div class="menu">
					<?php
						$menu = wp_get_nav_menu_object( 'Main' );
						$items = wp_get_nav_menu_items($menu->term_id);
					?>

					<nav class="main-menu">
						<div class="nav-logo-item">	</div>
						<ul class="menu">
							<?php foreach($items as $item):
								$title = $item->title;
								$title_words = explode(' ', $title);
								$classes = $item->classes == '' ? '' : join($item->classes, ' '); ?>

								<li id="menu-item-<?= $item->ID ?>" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home menu-item-<?= $item->ID ?>">
									<a href="<?= $item->url ?>" class="<?= $classes ?>">
										<span class="item-title">
											<?php foreach($title_words as $word): ?>
												<span><?= $word ?></span>
											<?php endforeach; ?>
										</span>
									</a>
								</li>

							<?php endforeach; ?>

						</ul>
					</nav>
				</div>


				<div class="nav-social-links">
					<ul>
						<?php
							$social_menu = wp_get_nav_menu_object( 'Social Links');
							$social_items = wp_get_nav_menu_items($social_menu->term_id);

							foreach($social_items as $social_item):
								$classes = $social_item->classes == '' ? '' : join($social_item->classes, ' '); ?>

							<li class="social-link">
								<a href="<?= $social_item->url ?>" class="<?= $classes ?>" target="_blank"><?= $social_item->title ?></a>
							</li>

						<?php endforeach; ?>

					</ul>
				</div>

			</header>
