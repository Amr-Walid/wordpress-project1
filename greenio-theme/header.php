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

<?php
// ---- Global header settings from ACF Options page (with fallbacks) ----
$logo_text       = greenio_field( 'logo_text', 'Greenio', 'option' );
$logo_image      = greenio_image( 'logo_image', '', 'medium', 'option' );
$header_cta_text = greenio_field( 'header_cta_text', __( 'Get Started', 'greenio' ), 'option' );
$header_cta_link = greenio_field( 'header_cta_link', '#contact', 'option' );
?>
<header class="site-header" id="siteHeader">
	<div class="container header-inner">

		<?php // ---- Logo: WP custom logo > ACF logo image > ACF/text logo ---- ?>
		<?php if ( has_custom_logo() ) : ?>
			<div class="logo"><?php the_custom_logo(); ?></div>
		<?php elseif ( $logo_image ) : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo" aria-label="<?php echo esc_attr( $logo_text ? $logo_text : get_bloginfo( 'name' ) ); ?>">
				<img src="<?php echo esc_url( $logo_image ); ?>" alt="<?php echo esc_attr( $logo_text ? $logo_text : get_bloginfo( 'name' ) ); ?>" class="logo-img" />
			</a>
		<?php else : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo" aria-label="<?php echo esc_attr( $logo_text ? $logo_text : get_bloginfo( 'name' ) ); ?>">
				<span class="logo-mark" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M12 2C7 6 5 10 5 14a7 7 0 0 0 14 0c0-1.5-.4-3-1.2-4.4C16.6 12 14 13 12 13c0-3 0-8 0-11Z" fill="currentColor"/>
					</svg>
				</span>
				<?php echo greenio_logo_text_markup( $logo_text ); ?>
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
			<?php if ( $header_cta_text ) : ?>
				<a href="<?php echo esc_url( $header_cta_link ); ?>" class="btn btn-yellow btn-sm"><?php echo esc_html( $header_cta_text ); ?></a>
			<?php endif; ?>
			<button class="nav-toggle" id="navToggle" aria-label="<?php esc_attr_e( 'Toggle menu', 'greenio' ); ?>" aria-expanded="false">
				<span></span><span></span><span></span>
			</button>
		</div>

	</div>
</header>

<main id="content" class="site-main">
