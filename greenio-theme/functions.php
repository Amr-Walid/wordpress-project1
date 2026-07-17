<?php
/**
 * Greenio Theme functions and definitions.
 *
 * @package Greenio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

if ( ! defined( 'GREENIO_VERSION' ) ) {
	define( 'GREENIO_VERSION', '1.0.0' );
}

/**
 * Theme setup: supports, menus, image sizes.
 */
function greenio_setup() {
	// Let WordPress manage the <title> tag.
	add_theme_support( 'title-tag' );

	// Featured images / post thumbnails.
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'greenio-card', 800, 600, true );
	add_image_size( 'greenio-wide', 1600, 900, true );

	// Automatic feed links, HTML5 markup, responsive embeds.
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support( 'responsive-embeds' );

	// Custom logo support (falls back to text logo in header.php).
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 40,
			'width'       => 160,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Register navigation menus.
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'greenio' ),
			'footer'  => __( 'Footer Menu', 'greenio' ),
		)
	);
}
add_action( 'after_setup_theme', 'greenio_setup' );

/**
 * Enqueue styles and scripts.
 */
function greenio_scripts() {
	// Google Fonts.
	wp_enqueue_style(
		'greenio-fonts',
		'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	// Main theme stylesheet (style.css in theme root — required by WP).
	wp_enqueue_style(
		'greenio-style',
		get_stylesheet_uri(),
		array( 'greenio-fonts' ),
		GREENIO_VERSION
	);

	// Main JavaScript (deferred, in footer).
	wp_enqueue_script(
		'greenio-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		GREENIO_VERSION,
		true
	);

	// Comment reply script where relevant.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'greenio_scripts' );

/**
 * Register a widgetized footer area.
 */
function greenio_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Footer Widgets', 'greenio' ),
			'id'            => 'footer-1',
			'description'   => __( 'Appears in the footer area.', 'greenio' ),
			'before_widget' => '<div id="%1$s" class="footer-col widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);
}
add_action( 'widgets_init', 'greenio_widgets_init' );

/**
 * Fallback menu used when no menu is assigned to the "primary" location.
 * Renders anchor links that match the one-page index.php sections.
 */
function greenio_primary_fallback() {
	echo '<ul class="menu">';
	$items = array(
		'#home'     => __( 'Home', 'greenio' ),
		'#about'    => __( 'About us', 'greenio' ),
		'#services' => __( 'Services', 'greenio' ),
		'#projects' => __( 'Project', 'greenio' ),
		'#contact'  => __( 'Contact us', 'greenio' ),
		'#pages'    => __( 'Pages', 'greenio' ),
	);
	$first = true;
	foreach ( $items as $href => $label ) {
		$class = $first ? ' class="current-menu-item"' : '';
		printf(
			'<li%1$s><a href="%2$s">%3$s</a></li>',
			$class, // phpcs:ignore WordPress.Security.EscapeOutput
			esc_attr( $href ),
			esc_html( $label )
		);
		$first = false;
	}
	echo '</ul>';
}

/**
 * Helper: return a theme asset URL.
 *
 * @param string $path Relative path inside the theme.
 * @return string
 */
function greenio_asset( $path ) {
	return get_template_directory_uri() . '/' . ltrim( $path, '/' );
}

/**
 * Body classes helper (adds a marker class for the front page).
 */
function greenio_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'greenio-front';
	}
	return $classes;
}
add_filter( 'body_class', 'greenio_body_classes' );
