<?php
/**
 * The header for the Greenio theme.
 *
 * Displays the <head> section and opens the site container + fixed header.
 *
 * @package Greenio
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="<?php bloginfo( 'description' ); ?>" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<link rel="icon" type="image/svg+xml" href="<?php echo esc_url( get_template_directory_uri() . '/assets/img/favicon.svg' ); ?>" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) { wp_body_open(); } ?>

<a class="screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'greenio' ); ?></a>

<header class="site-header" id="siteHeader">
	<div class="container header-inner">

		<?php // ---- Logo (custom logo if set, else text logo) ---- ?>
		<?php if ( has_custom_logo() ) : ?>
			<div class="logo"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<span class="logo-mark" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M12 2C7 6 5 10 5 14a7 7 0 0 0 14 0c0-1.5-.4-3-1.2-4.4C16.6 12 14 13 12 13c0-3 0-8 0-11Z" fill="currentColor"/>
					</svg>
				</span>
				<span class="logo-text">Green<span>io</span></span>
			</a>
		<?php endif; ?>

		<?php // ---- Primary navigation (centered) ---- ?>
		<nav class="main-nav" id="mainNav" aria-label="<?php esc_attr_e( 'Primary Menu', 'greenio' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'fallback_cb'    => 'greenio_primary_fallback',
					'depth'          => 2,
				)
			);
			?>
		</nav>

		<?php // ---- CTA + mobile toggle ---- ?>
		<div class="header-actions">
			<a href="#contact" class="btn btn-yellow btn-sm"><?php esc_html_e( 'Get Started', 'greenio' ); ?></a>
			<button class="nav-toggle" id="navToggle" aria-label="<?php esc_attr_e( 'Toggle menu', 'greenio' ); ?>" aria-expanded="false">
				<span></span><span></span><span></span>
			</button>
		</div>

	</div>
</header>

<main id="content" class="site-main">
