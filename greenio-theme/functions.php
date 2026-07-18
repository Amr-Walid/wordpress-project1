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

/* =========================================================================
 * CARBON FIELDS INTEGRATION
 *
 * This theme uses the free, open-source Carbon Fields library
 * (htmlburger/carbon-fields) — bundled inside the theme via Composer
 * (see composer.json + vendor/). NO plugin is required.
 *
 *  - Carbon Fields is loaded through the Composer autoloader and booted on
 *    the `after_setup_theme` hook via Carbon_Fields\Carbon_Fields::boot().
 *  - Containers/fields are defined on the `carbon_fields_register_fields`
 *    action (fired from the `carbon_fields_boot` action).
 *  - The template helpers below (greenio_field / greenio_image) wrap
 *    carbon_get_post_meta() / carbon_get_theme_option() and fall back to
 *    sensible defaults, so the site NEVER breaks even if Carbon Fields is
 *    somehow unavailable or a field is empty.
 * ========================================================================= */

/**
 * Load the Composer autoloader and boot Carbon Fields.
 *
 * Booting on `after_setup_theme` is the officially recommended hook.
 */
function greenio_boot_carbon_fields() {
	$autoload = get_template_directory() . '/vendor/autoload.php';
	if ( file_exists( $autoload ) ) {
		require_once $autoload;
	}

	if ( class_exists( '\Carbon_Fields\Carbon_Fields' ) ) {
		\Carbon_Fields\Carbon_Fields::boot();
	}
}
add_action( 'after_setup_theme', 'greenio_boot_carbon_fields' );

/**
 * Is Carbon Fields available (booted & helpers loaded)?
 *
 * @return bool
 */
function greenio_cf() {
	return function_exists( 'carbon_get_post_meta' ) && function_exists( 'carbon_get_theme_option' );
}

/**
 * Safe field getter with a graceful fallback value.
 *
 * Reads a Carbon Fields value and returns it when non-empty, otherwise the
 * supplied $default. The $scope argument mirrors the old ACF helper signature:
 *   - 'option'  → carbon_get_theme_option() (global settings)
 *   - false/int → carbon_get_post_meta( <post id>, ... ) (front-page meta)
 *
 * Carbon Fields prefixes stored meta keys with an underscore automatically,
 * so field names are passed here WITHOUT the leading underscore.
 *
 * @param string $selector Field name (no leading underscore).
 * @param mixed  $default  Fallback value.
 * @param mixed  $scope    'option' for theme options, or a post ID / false for post meta.
 * @return mixed
 */
function greenio_field( $selector, $default = '', $scope = false ) {
	if ( greenio_cf() ) {
		if ( 'option' === $scope ) {
			$value = carbon_get_theme_option( $selector );
		} else {
			$post_id = $scope ? (int) $scope : get_the_ID();
			$value   = $post_id ? carbon_get_post_meta( $post_id, $selector ) : '';
		}

		if ( is_array( $value ) ) {
			if ( ! empty( $value ) ) {
				return $value;
			}
		} elseif ( '' !== $value && null !== $value && false !== $value ) {
			return $value;
		} elseif ( '0' === (string) $value ) {
			return $value;
		}
	}
	return $default;
}

/**
 * Safe image getter.
 *
 * Carbon Fields image/media fields store an attachment ID by default
 * (value_type "id") or a URL (value_type "url"). This normalises either to a
 * usable URL and falls back to a bundled theme asset when empty.
 *
 * @param string $selector       Field name.
 * @param string $fallback_asset Relative theme asset path used when empty.
 * @param string $size           Image size for ID values.
 * @param mixed  $scope          'option' or a post ID / false.
 * @return string URL.
 */
function greenio_image( $selector, $fallback_asset, $size = 'large', $scope = false ) {
	$img = greenio_field( $selector, '', $scope );
	return greenio_image_value( $img, $fallback_asset, $size );
}

/**
 * Normalise an already-fetched image value to a usable URL.
 *
 * Handles every shape a Carbon Fields media/image sub-field may return:
 *   - attachment ID (int or numeric string)  → resolve via WP
 *   - URL string                             → use as-is
 *   - array with 'url' / 'sizes'             → defensive support
 * Falls back to a bundled theme asset if empty; an empty fallback returns ''
 * (meaning "no image", e.g. an optional logo).
 *
 * @param mixed  $img            Raw image value (ID | URL | array | '').
 * @param string $fallback_asset Relative theme asset path used when empty.
 * @param string $size           Image size for ID values.
 * @return string URL.
 */
function greenio_image_value( $img, $fallback_asset, $size = 'large' ) {
	if ( is_array( $img ) ) {
		// Defensive: array shape (e.g. ACF-style or complex value).
		if ( ! empty( $img['sizes'][ $size ] ) ) {
			return $img['sizes'][ $size ];
		}
		if ( ! empty( $img['url'] ) ) {
			return $img['url'];
		}
	} elseif ( is_numeric( $img ) ) {
		// Carbon Fields default: attachment ID.
		if ( function_exists( 'wp_get_attachment_image_url' ) ) {
			$src = wp_get_attachment_image_url( (int) $img, $size );
			if ( $src ) {
				return $src;
			}
		}
	} elseif ( is_string( $img ) && '' !== $img ) {
		// URL value_type.
		return $img;
	}

	// An empty fallback means "no image" (e.g. optional logo) — return ''.
	if ( '' === $fallback_asset || null === $fallback_asset ) {
		return '';
	}

	return greenio_asset( $fallback_asset );
}

/**
 * Register all Carbon Fields containers & fields.
 *
 * Hooked to `carbon_fields_register_fields` (fired by Carbon_Fields::boot()).
 * Two containers are created:
 *   1. A Theme Options page  → global logo / header CTA / footer settings.
 *   2. A Post Meta container → the static front page content, including three
 *      `complex` fields (Carbon Fields' free repeater): services, stats_band
 *      and projects.
 */
function greenio_register_carbon_fields() {
	if ( ! class_exists( '\Carbon_Fields\Container' ) ) {
		return;
	}

	$container = '\Carbon_Fields\Container';
	$field     = '\Carbon_Fields\Field';

	/* --------------------------------------------------------------------
	 * CONTAINER 1 — Theme Options page (global settings)
	 * ------------------------------------------------------------------ */
	call_user_func( array( $container, 'make' ), 'theme_options', 'greenio-settings', __( 'Greenio Theme Settings', 'greenio' ) )
		->set_page_menu_title( __( 'Theme Settings', 'greenio' ) )
		->set_icon( 'dashicons-superhero' )
		->set_page_menu_position( 59 )
		->add_tab(
			__( 'Logo & Header', 'greenio' ),
			array(
				call_user_func( array( $field, 'make' ), 'text', 'logo_text', __( 'Logo Text', 'greenio' ) )
					->set_help_text( __( 'Used when no logo image is set. Wrap the accent part in [g]...[/g] (e.g. Green[g]io[/g]).', 'greenio' ) )
					->set_default_value( 'Green[g]io[/g]' ),
				call_user_func( array( $field, 'make' ), 'image', 'logo_image', __( 'Logo Image', 'greenio' ) )
					->set_value_type( 'url' )
					->set_help_text( __( 'Optional. Overrides the text logo when set.', 'greenio' ) ),
				call_user_func( array( $field, 'make' ), 'text', 'header_cta_text', __( 'Header CTA Text', 'greenio' ) )
					->set_default_value( 'Get Started' ),
				call_user_func( array( $field, 'make' ), 'text', 'header_cta_link', __( 'Header CTA Link', 'greenio' ) )
					->set_default_value( '#contact' ),
			)
		)
		->add_tab(
			__( 'Footer', 'greenio' ),
			array(
				call_user_func( array( $field, 'make' ), 'textarea', 'footer_about', __( 'Footer About Text', 'greenio' ) )
					->set_rows( 3 )
					->set_default_value( 'The better source of energy for the better tomorrow. 100% clean. 100% future-ready.' ),
				call_user_func( array( $field, 'make' ), 'text', 'footer_email', __( 'Contact Email', 'greenio' ) )
					->set_default_value( 'hello@greenio.energy' ),
				call_user_func( array( $field, 'make' ), 'text', 'footer_phone', __( 'Contact Phone', 'greenio' ) )
					->set_default_value( '+1 (800) 555-0199' ),
				call_user_func( array( $field, 'make' ), 'text', 'footer_address', __( 'Address', 'greenio' ) )
					->set_default_value( '123 Clean Energy Blvd' ),
				call_user_func( array( $field, 'make' ), 'text', 'footer_copyright', __( 'Copyright Note', 'greenio' ) )
					->set_help_text( __( 'Use {year} to insert the current year automatically.', 'greenio' ) )
					->set_default_value( 'Crafted for a sustainable world.' ),
			)
		);

	/* --------------------------------------------------------------------
	 * CONTAINER 2 — Front Page content (Post Meta)
	 *
	 * Carbon Fields has no native "is front page" condition, so we target the
	 * page selected under Settings → Reading (page_on_front) by its post ID.
	 * If no static front page is configured yet, we gracefully fall back to
	 * showing the fields on any Page, so the container is never empty.
	 * ------------------------------------------------------------------ */
	$front_page_id = (int) get_option( 'page_on_front' );

	$front_container = call_user_func( array( $container, 'make' ), 'post_meta', 'greenio_front', __( 'Greenio — Front Page Content', 'greenio' ) );

	if ( $front_page_id > 0 ) {
		$front_container->where( 'post_id', '=', $front_page_id );
	} else {
		$front_container->where( 'post_type', '=', 'page' );
	}

	$front_container
		->add_tab(
			__( 'Hero Section', 'greenio' ),
			array(
				call_user_func( array( $field, 'make' ), 'text', 'hero_eyebrow', __( 'Hero Eyebrow', 'greenio' ) )
					->set_default_value( 'Welcome to Greenio' ),
				call_user_func( array( $field, 'make' ), 'text', 'hero_title', __( 'Hero Title', 'greenio' ) )
					->set_help_text( __( 'Wrap the highlighted words in [g]...[/g] to apply the green→yellow gradient.', 'greenio' ) )
					->set_default_value( 'The better source of energy for the [g]better tomorrow[/g]' ),
				call_user_func( array( $field, 'make' ), 'textarea', 'hero_subtitle', __( 'Hero Subtitle', 'greenio' ) )
					->set_rows( 3 )
					->set_default_value( 'Help protect the environment by powering your home and business with 100% clean, renewable energy — engineered for the future.' ),
				call_user_func( array( $field, 'make' ), 'text', 'hero_cta_text', __( 'Primary CTA Text', 'greenio' ) )
					->set_default_value( 'Get Started' ),
				call_user_func( array( $field, 'make' ), 'text', 'hero_cta_link', __( 'Primary CTA Link', 'greenio' ) )
					->set_default_value( '#contact' ),
				call_user_func( array( $field, 'make' ), 'text', 'hero_cta2_text', __( 'Secondary CTA Text', 'greenio' ) )
					->set_default_value( 'Discover more' ),
				call_user_func( array( $field, 'make' ), 'text', 'hero_cta2_link', __( 'Secondary CTA Link', 'greenio' ) )
					->set_default_value( '#services' ),
				call_user_func( array( $field, 'make' ), 'image', 'hero_bg', __( 'Hero Background Image', 'greenio' ) )
					->set_value_type( 'url' ),
			)
		)
		->add_tab(
			__( 'Stats Card', 'greenio' ),
			array(
				call_user_func( array( $field, 'make' ), 'text', 'stat_label', __( 'Stat Subtext (top)', 'greenio' ) )
					->set_default_value( 'Since 2010, our customers have avoided' ),
				call_user_func( array( $field, 'make' ), 'text', 'stat_number', __( 'Big Number', 'greenio' ) )
					->set_attribute( 'type', 'number' )
					->set_help_text( __( 'Digits only — the front-end animates a live count-up to this value.', 'greenio' ) )
					->set_default_value( '112845311' ),
				call_user_func( array( $field, 'make' ), 'text', 'stat_unit', __( 'Unit / Subtext (bottom)', 'greenio' ) )
					->set_default_value( 'pounds of CO₂' ),
			)
		)
		->add_tab(
			__( 'Services Grid', 'greenio' ),
			array(
				call_user_func( array( $field, 'make' ), 'complex', 'services', __( 'Service Cards', 'greenio' ) )
					->set_help_text( __( 'The icon cards shown under the hero. Leave empty to fall back to the built-in defaults.', 'greenio' ) )
					->set_layout( 'tabbed-vertical' )
					->setup_labels(
						array(
							'plural_name'   => __( 'Services', 'greenio' ),
							'singular_name' => __( 'Service', 'greenio' ),
						)
					)
					->add_fields(
						array(
							call_user_func( array( $field, 'make' ), 'text', 'title', __( 'Title', 'greenio' ) ),
							call_user_func( array( $field, 'make' ), 'textarea', 'description', __( 'Description', 'greenio' ) )
								->set_rows( 3 ),
							call_user_func( array( $field, 'make' ), 'text', 'link', __( 'Link', 'greenio' ) )
								->set_default_value( '#services' ),
							call_user_func( array( $field, 'make' ), 'image', 'icon', __( 'Icon', 'greenio' ) )
								->set_value_type( 'url' )
								->set_help_text( __( 'Optional. Leave empty to use the built-in SVG icon.', 'greenio' ) ),
							call_user_func( array( $field, 'make' ), 'checkbox', 'featured', __( 'Highlight (blue) card?', 'greenio' ) )
								->set_option_value( 'yes' ),
						)
					),
			)
		)
		->add_tab(
			__( 'About / Zig-Zag', 'greenio' ),
			array(
				call_user_func( array( $field, 'make' ), 'text', 'about_eyebrow', __( 'About Eyebrow', 'greenio' ) )
					->set_default_value( 'Who we are' ),
				call_user_func( array( $field, 'make' ), 'text', 'about_title', __( 'About Title', 'greenio' ) )
					->set_default_value( 'Keep your environment clean, make the earth green.' ),
				call_user_func( array( $field, 'make' ), 'textarea', 'about_desc', __( 'About Description', 'greenio' ) )
					->set_rows( 4 )
					->set_default_value( 'For over a decade Greenio has designed, built and maintained renewable systems that pay for themselves — while cutting millions of pounds of carbon from the atmosphere.' ),
				call_user_func( array( $field, 'make' ), 'textarea', 'about_bullets', __( 'Checklist Items', 'greenio' ) )
					->set_rows( 4 )
					->set_help_text( __( 'One item per line.', 'greenio' ) )
					->set_default_value( "Certified engineers & 25-year performance warranty\nReal-time energy dashboards on every install\nZero-emission supply from source to socket" ),
				call_user_func( array( $field, 'make' ), 'image', 'about_image', __( 'About Image', 'greenio' ) )
					->set_value_type( 'url' ),
				call_user_func( array( $field, 'make' ), 'text', 'about_overlay_tag', __( 'Overlay Card Tag', 'greenio' ) )
					->set_default_value( 'Renewable energy' ),
				call_user_func( array( $field, 'make' ), 'text', 'about_overlay_title', __( 'Overlay Card Title', 'greenio' ) )
					->set_default_value( 'Energy is the future, make it brilliant.' ),
			)
		)
		->add_tab(
			__( 'Energy Grid', 'greenio' ),
			array(
				call_user_func( array( $field, 'make' ), 'text', 'energy_eyebrow', __( 'Section Eyebrow', 'greenio' ) )
					->set_default_value( 'What we offer' ),
				call_user_func( array( $field, 'make' ), 'text', 'energy_title', __( 'Section Title', 'greenio' ) )
					->set_default_value( "A choice that's good for you and the planet." ),
				call_user_func( array( $field, 'make' ), 'textarea', 'energy_subtitle', __( 'Section Subtitle', 'greenio' ) )
					->set_rows( 2 )
					->set_default_value( 'From flowing rivers to open fields, Greenio harnesses every source of clean power with technology built for a sustainable world.' ),

				// Card 1: Wind.
				call_user_func( array( $field, 'make' ), 'text', 'energy_1_tag', __( 'Card 1 — Tag', 'greenio' ) )
					->set_default_value( 'Wind' ),
				call_user_func( array( $field, 'make' ), 'text', 'energy_1_title', __( 'Card 1 — Title', 'greenio' ) )
					->set_default_value( 'Wind Power' ),
				call_user_func( array( $field, 'make' ), 'textarea', 'energy_1_desc', __( 'Card 1 — Description', 'greenio' ) )
					->set_rows( 2 )
					->set_default_value( 'Next-generation turbines convert steady coastal winds into round-the-clock renewable electricity.' ),
				call_user_func( array( $field, 'make' ), 'image', 'energy_1_image', __( 'Card 1 — Background Image', 'greenio' ) )
					->set_value_type( 'url' ),

				// Card 2: Water.
				call_user_func( array( $field, 'make' ), 'text', 'energy_2_tag', __( 'Card 2 — Tag', 'greenio' ) )
					->set_default_value( 'Water' ),
				call_user_func( array( $field, 'make' ), 'text', 'energy_2_title', __( 'Card 2 — Title', 'greenio' ) )
					->set_default_value( 'Hydroelectric' ),
				call_user_func( array( $field, 'make' ), 'textarea', 'energy_2_desc', __( 'Card 2 — Description', 'greenio' ) )
					->set_rows( 2 )
					->set_default_value( 'Modern hydro plants deliver clean, dependable baseload power from the flow of water.' ),
				call_user_func( array( $field, 'make' ), 'image', 'energy_2_image', __( 'Card 2 — Background Image', 'greenio' ) )
					->set_value_type( 'url' ),

				// Card 3: Solar.
				call_user_func( array( $field, 'make' ), 'text', 'energy_3_tag', __( 'Card 3 — Tag', 'greenio' ) )
					->set_default_value( 'Solar' ),
				call_user_func( array( $field, 'make' ), 'text', 'energy_3_title', __( 'Card 3 — Title', 'greenio' ) )
					->set_default_value( 'Solar Power' ),
				call_user_func( array( $field, 'make' ), 'textarea', 'energy_3_desc', __( 'Card 3 — Description', 'greenio' ) )
					->set_rows( 2 )
					->set_default_value( 'High-efficiency photovoltaic arrays capture the sun across fields, rooftops and farms.' ),
				call_user_func( array( $field, 'make' ), 'image', 'energy_3_image', __( 'Card 3 — Background Image', 'greenio' ) )
					->set_value_type( 'url' ),

				// Card 4: Storage.
				call_user_func( array( $field, 'make' ), 'text', 'energy_4_tag', __( 'Card 4 — Tag', 'greenio' ) )
					->set_default_value( 'Storage' ),
				call_user_func( array( $field, 'make' ), 'text', 'energy_4_title', __( 'Card 4 — Title', 'greenio' ) )
					->set_default_value( 'Smart Battery Storage' ),
				call_user_func( array( $field, 'make' ), 'textarea', 'energy_4_desc', __( 'Card 4 — Description', 'greenio' ) )
					->set_rows( 2 )
					->set_default_value( 'Grid-scale storage banks bottle the sun and wind for a stable, always-on clean supply.' ),
				call_user_func( array( $field, 'make' ), 'image', 'energy_4_image', __( 'Card 4 — Background Image', 'greenio' ) )
					->set_value_type( 'url' ),
			)
		)
		->add_tab(
			__( 'Stats Band', 'greenio' ),
			array(
				call_user_func( array( $field, 'make' ), 'complex', 'stats_band', __( 'Stat Items', 'greenio' ) )
					->set_help_text( __( 'The numbers strip. Leave empty to fall back to the built-in defaults.', 'greenio' ) )
					->setup_labels(
						array(
							'plural_name'   => __( 'Stats', 'greenio' ),
							'singular_name' => __( 'Stat', 'greenio' ),
						)
					)
					->add_fields(
						array(
							call_user_func( array( $field, 'make' ), 'text', 'number', __( 'Number', 'greenio' ) )
								->set_attribute( 'type', 'number' )
								->set_help_text( __( 'Digits only — the front-end animates a count-up to this value.', 'greenio' ) ),
							call_user_func( array( $field, 'make' ), 'text', 'suffix', __( 'Suffix', 'greenio' ) )
								->set_help_text( __( 'e.g. +, %, k+, yrs', 'greenio' ) ),
							call_user_func( array( $field, 'make' ), 'text', 'label', __( 'Label', 'greenio' ) ),
						)
					),
			)
		)
		->add_tab(
			__( 'Projects', 'greenio' ),
			array(
				call_user_func( array( $field, 'make' ), 'text', 'projects_eyebrow', __( 'Section Eyebrow', 'greenio' ) )
					->set_default_value( 'Featured work' ),
				call_user_func( array( $field, 'make' ), 'text', 'projects_title', __( 'Section Title', 'greenio' ) )
					->set_default_value( 'Powering communities, one project at a time.' ),
				call_user_func( array( $field, 'make' ), 'complex', 'projects', __( 'Project Cards', 'greenio' ) )
					->set_help_text( __( 'The featured project cards. Leave empty to fall back to the built-in defaults.', 'greenio' ) )
					->set_layout( 'tabbed-vertical' )
					->setup_labels(
						array(
							'plural_name'   => __( 'Projects', 'greenio' ),
							'singular_name' => __( 'Project', 'greenio' ),
						)
					)
					->add_fields(
						array(
							call_user_func( array( $field, 'make' ), 'text', 'tag', __( 'Tag', 'greenio' ) ),
							call_user_func( array( $field, 'make' ), 'text', 'title', __( 'Title', 'greenio' ) ),
							call_user_func( array( $field, 'make' ), 'image', 'image', __( 'Image', 'greenio' ) )
								->set_value_type( 'url' ),
						)
					),
			)
		)
		->add_tab(
			__( 'CTA Section', 'greenio' ),
			array(
				call_user_func( array( $field, 'make' ), 'text', 'cta_eyebrow', __( 'Eyebrow Text', 'greenio' ) )
					->set_default_value( 'Ready to switch?' ),
				call_user_func( array( $field, 'make' ), 'text', 'cta_title', __( 'Main Title', 'greenio' ) )
					->set_default_value( 'Start powering your world with clean energy today.' ),
				call_user_func( array( $field, 'make' ), 'text', 'cta_placeholder', __( 'Email Input Placeholder', 'greenio' ) )
					->set_default_value( 'Enter your email address' ),
			)
		);
}
add_action( 'carbon_fields_register_fields', 'greenio_register_carbon_fields' );

/**
 * Helper: apply the [g]...[/g] gradient shortcode to a string and escape it.
 *
 * @param string $text Raw text possibly containing [g]...[/g].
 * @return string Safe HTML with <span class="grad"> wrapping.
 */
function greenio_gradient_text( $text ) {
	$text = esc_html( $text );
	$text = str_replace(
		array( '[g]', '[/g]' ),
		array( '<span class="grad">', '</span>' ),
		$text
	);
	return $text;
}

/**
 * Helper: render the text logo markup.
 *
 * Preserves the original two-tone "Green<span>io</span>" styling for the
 * default value, honours the [g]...[/g] gradient shortcode, and safely
 * escapes any custom logo text set from the Theme Options page.
 *
 * @param string $text Logo text (from theme option, with fallback).
 * @return string Safe HTML wrapped in <span class="logo-text">.
 */
function greenio_logo_text_markup( $text ) {
	$text = (string) $text;

	// Honour the gradient shortcode if the client uses it.
	if ( false !== strpos( $text, '[g]' ) ) {
		return '<span class="logo-text">' . greenio_gradient_text( $text ) . '</span>';
	}

	// Recreate the signature two-tone look for the default "Greenio".
	if ( 'Greenio' === $text ) {
		return '<span class="logo-text">Green<span>io</span></span>';
	}

	// Any other custom text: escape and render plainly.
	return '<span class="logo-text">' . esc_html( $text ) . '</span>';
}

/**
 * Admin notice shown only if the bundled Carbon Fields library is missing
 * (e.g. the theme was installed without running `composer install`).
 * Dismissible and non-fatal — the theme still renders its default content.
 */
function greenio_carbon_admin_notice() {
	if ( greenio_cf() || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning is-dismissible"><p><strong>Greenio:</strong> ';
	echo esc_html__( 'The bundled Carbon Fields library could not be loaded. Run "composer install" inside the theme directory to enable dashboard editing. The theme still works using its built-in default content.', 'greenio' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'greenio_carbon_admin_notice' );
