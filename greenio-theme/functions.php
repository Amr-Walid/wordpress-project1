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
 * ADVANCED CUSTOM FIELDS (ACF) INTEGRATION
 *
 * The theme works with OR without ACF installed:
 *  - If ACF is active, all content is editable from the WordPress backend.
 *  - If ACF is NOT active, the template helpers below fall back to sensible
 *    defaults so the site never breaks (see greenio_field() / greenio_image()).
 *
 * Install "Advanced Custom Fields" (free) or ACF PRO to unlock editing.
 * The Repeater field used for the services grid requires ACF PRO — a graceful
 * fallback is provided for the free version.
 * ========================================================================= */

/**
 * Is ACF available?
 *
 * @return bool
 */
function greenio_acf() {
	return function_exists( 'get_field' );
}

/**
 * Safe field getter with a fallback value.
 *
 * Returns the ACF field when available & non-empty, otherwise $default.
 *
 * @param string $selector Field name.
 * @param mixed  $default  Fallback value.
 * @param mixed  $post_id  Post ID or 'option'. Default current post.
 * @return mixed
 */
function greenio_field( $selector, $default = '', $post_id = false ) {
	if ( greenio_acf() ) {
		$value = get_field( $selector, $post_id );
		if ( ! empty( $value ) || '0' === $value || 0 === $value ) {
			return $value;
		}
	}
	return $default;
}

/**
 * Safe image getter.
 *
 * ACF image fields may return an ID, URL string, or array (depending on the
 * "Return Format"). This normalises any of those to a usable URL and falls
 * back to a bundled theme asset if empty.
 *
 * @param string $selector      Field name.
 * @param string $fallback_asset Relative theme asset path used when empty.
 * @param string $size          Image size for array/ID formats.
 * @param mixed  $post_id       Post ID or 'option'.
 * @return string URL.
 */
function greenio_image( $selector, $fallback_asset, $size = 'large', $post_id = false ) {
	$img = greenio_acf() ? get_field( $selector, $post_id ) : '';

	if ( is_array( $img ) ) {
		// Array return format.
		if ( ! empty( $img['sizes'][ $size ] ) ) {
			return $img['sizes'][ $size ];
		}
		if ( ! empty( $img['url'] ) ) {
			return $img['url'];
		}
	} elseif ( is_numeric( $img ) ) {
		// ID return format.
		$src = wp_get_attachment_image_url( (int) $img, $size );
		if ( $src ) {
			return $src;
		}
	} elseif ( is_string( $img ) && '' !== $img ) {
		// URL return format.
		return $img;
	}

	// An empty fallback means "no image" (e.g. optional logo) — return ''.
	if ( '' === $fallback_asset || null === $fallback_asset ) {
		return '';
	}

	return greenio_asset( $fallback_asset );
}

/**
 * Register an ACF Options page for global (header/footer) settings.
 */
function greenio_acf_options_page() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			array(
				'page_title' => __( 'Greenio Theme Settings', 'greenio' ),
				'menu_title' => __( 'Theme Settings', 'greenio' ),
				'menu_slug'  => 'greenio-settings',
				'capability' => 'edit_theme_options',
				'icon_url'   => 'dashicons-superhero',
				'position'   => 59,
				'redirect'   => false,
			)
		);
	}
}
add_action( 'acf/init', 'greenio_acf_options_page' );

/**
 * Register all ACF field groups programmatically.
 */
function greenio_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	/* --------------------------------------------------------------------
	 * GROUP 1 — Front Page Content
	 * Shows on the page set as the static front page (Settings → Reading).
	 * ------------------------------------------------------------------ */
	acf_add_local_field_group(
		array(
			'key'      => 'group_greenio_front',
			'title'    => __( 'Greenio — Front Page Content', 'greenio' ),
			'fields'   => array(

				/* ---- HERO SECTION ---- */
				array(
					'key'     => 'field_hero_tab',
					'label'   => __( 'Hero Section', 'greenio' ),
					'type'    => 'tab',
					'placement' => 'top',
				),
				array(
					'key'          => 'field_hero_eyebrow',
					'label'        => __( 'Hero Eyebrow', 'greenio' ),
					'name'         => 'hero_eyebrow',
					'type'         => 'text',
					'default_value'=> 'Welcome to Greenio',
				),
				array(
					'key'          => 'field_hero_title',
					'label'        => __( 'Hero Title', 'greenio' ),
					'name'         => 'hero_title',
					'type'         => 'text',
					'instructions' => __( 'Wrap the highlighted words in [g]...[/g] to apply the green→yellow gradient.', 'greenio' ),
					'default_value'=> 'The better source of energy for the [g]better tomorrow[/g]',
				),
				array(
					'key'          => 'field_hero_subtitle',
					'label'        => __( 'Hero Subtitle', 'greenio' ),
					'name'         => 'hero_subtitle',
					'type'         => 'textarea',
					'rows'         => 3,
					'default_value'=> 'Help protect the environment by powering your home and business with 100% clean, renewable energy — engineered for the future.',
				),
				array(
					'key'          => 'field_hero_cta_text',
					'label'        => __( 'Primary CTA Text', 'greenio' ),
					'name'         => 'hero_cta_text',
					'type'         => 'text',
					'default_value'=> 'Get Started',
				),
				array(
					'key'          => 'field_hero_cta_link',
					'label'        => __( 'Primary CTA Link', 'greenio' ),
					'name'         => 'hero_cta_link',
					'type'         => 'text',
					'default_value'=> '#contact',
				),
				array(
					'key'          => 'field_hero_cta2_text',
					'label'        => __( 'Secondary CTA Text', 'greenio' ),
					'name'         => 'hero_cta2_text',
					'type'         => 'text',
					'default_value'=> 'Discover more',
				),
				array(
					'key'          => 'field_hero_cta2_link',
					'label'        => __( 'Secondary CTA Link', 'greenio' ),
					'name'         => 'hero_cta2_link',
					'type'         => 'text',
					'default_value'=> '#services',
				),
				array(
					'key'           => 'field_hero_bg',
					'label'         => __( 'Hero Background Image', 'greenio' ),
					'name'          => 'hero_bg',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'medium',
				),

				/* ---- STATS CARD ---- */
				array(
					'key'   => 'field_stats_tab',
					'label' => __( 'Stats Card', 'greenio' ),
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_stat_label',
					'label'        => __( 'Stat Subtext (top)', 'greenio' ),
					'name'         => 'stat_label',
					'type'         => 'text',
					'default_value'=> 'Since 2010, our customers have avoided',
				),
				array(
					'key'          => 'field_stat_number',
					'label'        => __( 'Big Number', 'greenio' ),
					'name'         => 'stat_number',
					'type'         => 'number',
					'instructions' => __( 'Digits only — the front-end animates a live count-up to this value.', 'greenio' ),
					'default_value'=> 112845311,
				),
				array(
					'key'          => 'field_stat_unit',
					'label'        => __( 'Unit / Subtext (bottom)', 'greenio' ),
					'name'         => 'stat_unit',
					'type'         => 'text',
					'default_value'=> 'pounds of CO₂',
				),

				/* ---- SERVICES GRID (4 individual flat cards — FREE ACF, no repeater) ---- */
				array(
					'key'   => 'field_services_tab',
					'label' => __( 'Services Grid', 'greenio' ),
					'type'  => 'tab',
				),
				// -- Service 1 --
				array(
					'key'          => 'field_service_1_title',
					'label'        => __( 'Service 1 — Title', 'greenio' ),
					'name'         => 'service_1_title',
					'type'         => 'text',
					'default_value'=> 'Installation',
				),
				array(
					'key'          => 'field_service_1_desc',
					'label'        => __( 'Service 1 — Description', 'greenio' ),
					'name'         => 'service_1_desc',
					'type'         => 'textarea',
					'rows'         => 3,
					'default_value'=> 'Turn-key solar & wind installation, engineered and commissioned by certified specialists.',
				),
				array(
					'key'          => 'field_service_1_link',
					'label'        => __( 'Service 1 — Link', 'greenio' ),
					'name'         => 'service_1_link',
					'type'         => 'text',
					'default_value'=> '#services',
				),
				array(
					'key'           => 'field_service_1_icon',
					'label'         => __( 'Service 1 — Icon', 'greenio' ),
					'name'          => 'service_1_icon',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'thumbnail',
					'instructions'  => __( 'Optional. Leave empty to use the built-in SVG icon.', 'greenio' ),
				),
				array(
					'key'          => 'field_service_1_featured',
					'label'        => __( 'Service 1 — Highlight (blue) card?', 'greenio' ),
					'name'         => 'service_1_featured',
					'type'         => 'true_false',
					'ui'           => 1,
					'default_value'=> 0,
				),
				// -- Service 2 --
				array(
					'key'          => 'field_service_2_title',
					'label'        => __( 'Service 2 — Title', 'greenio' ),
					'name'         => 'service_2_title',
					'type'         => 'text',
					'default_value'=> 'Maintenance',
				),
				array(
					'key'          => 'field_service_2_desc',
					'label'        => __( 'Service 2 — Description', 'greenio' ),
					'name'         => 'service_2_desc',
					'type'         => 'textarea',
					'rows'         => 3,
					'default_value'=> 'Predictive monitoring and rapid servicing keep every panel and turbine at peak output.',
				),
				array(
					'key'          => 'field_service_2_link',
					'label'        => __( 'Service 2 — Link', 'greenio' ),
					'name'         => 'service_2_link',
					'type'         => 'text',
					'default_value'=> '#services',
				),
				array(
					'key'           => 'field_service_2_icon',
					'label'         => __( 'Service 2 — Icon', 'greenio' ),
					'name'          => 'service_2_icon',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'thumbnail',
					'instructions'  => __( 'Optional. Leave empty to use the built-in SVG icon.', 'greenio' ),
				),
				array(
					'key'          => 'field_service_2_featured',
					'label'        => __( 'Service 2 — Highlight (blue) card?', 'greenio' ),
					'name'         => 'service_2_featured',
					'type'         => 'true_false',
					'ui'           => 1,
					'default_value'=> 0,
				),
				// -- Service 3 --
				array(
					'key'          => 'field_service_3_title',
					'label'        => __( 'Service 3 — Title', 'greenio' ),
					'name'         => 'service_3_title',
					'type'         => 'text',
					'default_value'=> 'Consultation',
				),
				array(
					'key'          => 'field_service_3_desc',
					'label'        => __( 'Service 3 — Description', 'greenio' ),
					'name'         => 'service_3_desc',
					'type'         => 'textarea',
					'rows'         => 3,
					'default_value'=> 'Data-driven energy audits map the fastest, cleanest path to your net-zero goals.',
				),
				array(
					'key'          => 'field_service_3_link',
					'label'        => __( 'Service 3 — Link', 'greenio' ),
					'name'         => 'service_3_link',
					'type'         => 'text',
					'default_value'=> '#services',
				),
				array(
					'key'           => 'field_service_3_icon',
					'label'         => __( 'Service 3 — Icon', 'greenio' ),
					'name'          => 'service_3_icon',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'thumbnail',
					'instructions'  => __( 'Optional. Leave empty to use the built-in SVG icon.', 'greenio' ),
				),
				array(
					'key'          => 'field_service_3_featured',
					'label'        => __( 'Service 3 — Highlight (blue) card?', 'greenio' ),
					'name'         => 'service_3_featured',
					'type'         => 'true_false',
					'ui'           => 1,
					'default_value'=> 0,
				),
				// -- Service 4 (highlighted by default) --
				array(
					'key'          => 'field_service_4_title',
					'label'        => __( 'Service 4 — Title', 'greenio' ),
					'name'         => 'service_4_title',
					'type'         => 'text',
					'default_value'=> 'Microgrid Planning',
				),
				array(
					'key'          => 'field_service_4_desc',
					'label'        => __( 'Service 4 — Description', 'greenio' ),
					'name'         => 'service_4_desc',
					'type'         => 'textarea',
					'rows'         => 3,
					'default_value'=> 'Resilient, AI-optimized microgrids that keep the lights on — fully independent of the grid.',
				),
				array(
					'key'          => 'field_service_4_link',
					'label'        => __( 'Service 4 — Link', 'greenio' ),
					'name'         => 'service_4_link',
					'type'         => 'text',
					'default_value'=> '#services',
				),
				array(
					'key'           => 'field_service_4_icon',
					'label'         => __( 'Service 4 — Icon', 'greenio' ),
					'name'          => 'service_4_icon',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'thumbnail',
					'instructions'  => __( 'Optional. Leave empty to use the built-in SVG icon.', 'greenio' ),
				),
				array(
					'key'          => 'field_service_4_featured',
					'label'        => __( 'Service 4 — Highlight (blue) card?', 'greenio' ),
					'name'         => 'service_4_featured',
					'type'         => 'true_false',
					'ui'           => 1,
					'default_value'=> 1,
				),

				/* ---- ABOUT / ZIG-ZAG ---- */
				array(
					'key'   => 'field_about_tab',
					'label' => __( 'About / Zig-Zag', 'greenio' ),
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_about_eyebrow',
					'label'        => __( 'About Eyebrow', 'greenio' ),
					'name'         => 'about_eyebrow',
					'type'         => 'text',
					'default_value'=> 'Who we are',
				),
				array(
					'key'          => 'field_about_title',
					'label'        => __( 'About Title', 'greenio' ),
					'name'         => 'about_title',
					'type'         => 'text',
					'default_value'=> 'Keep your environment clean, make the earth green.',
				),
				array(
					'key'          => 'field_about_desc',
					'label'        => __( 'About Description', 'greenio' ),
					'name'         => 'about_desc',
					'type'         => 'textarea',
					'rows'         => 4,
					'default_value'=> 'For over a decade Greenio has designed, built and maintained renewable systems that pay for themselves — while cutting millions of pounds of carbon from the atmosphere.',
				),
				array(
					'key'          => 'field_about_bullets',
					'label'        => __( 'Checklist Items', 'greenio' ),
					'name'         => 'about_bullets',
					'type'         => 'textarea',
					'instructions' => __( 'One item per line.', 'greenio' ),
					'rows'         => 4,
					'default_value'=> "Certified engineers & 25-year performance warranty\nReal-time energy dashboards on every install\nZero-emission supply from source to socket",
				),
				array(
					'key'           => 'field_about_image',
					'label'         => __( 'About Image', 'greenio' ),
					'name'          => 'about_image',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'medium',
				),
				array(
					'key'          => 'field_about_overlay_tag',
					'label'        => __( 'Overlay Card Tag', 'greenio' ),
					'name'         => 'about_overlay_tag',
					'type'         => 'text',
					'default_value'=> 'Renewable energy',
				),
				array(
					'key'          => 'field_about_overlay_title',
					'label'        => __( 'Overlay Card Title', 'greenio' ),
					'name'         => 'about_overlay_title',
					'type'         => 'text',
					'default_value'=> 'Energy is the future, make it brilliant.',
				),

				/* ---- ENERGY GRID (What we offer) ---- */
				array(
					'key'   => 'field_energy_tab',
					'label' => __( 'Energy Grid', 'greenio' ),
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_energy_eyebrow',
					'label'        => __( 'Section Eyebrow', 'greenio' ),
					'name'         => 'energy_eyebrow',
					'type'         => 'text',
					'default_value'=> 'What we offer',
				),
				array(
					'key'          => 'field_energy_title',
					'label'        => __( 'Section Title', 'greenio' ),
					'name'         => 'energy_title',
					'type'         => 'text',
					'default_value'=> "A choice that's good for you and the planet.",
				),
				array(
					'key'          => 'field_energy_subtitle',
					'label'        => __( 'Section Subtitle', 'greenio' ),
					'name'         => 'energy_subtitle',
					'type'         => 'textarea',
					'rows'         => 2,
					'default_value'=> 'From flowing rivers to open fields, Greenio harnesses every source of clean power with technology built for a sustainable world.',
				),

				// -- Card 1: Wind --
				array(
					'key'          => 'field_energy_1_tag',
					'label'        => __( 'Card 1 — Tag', 'greenio' ),
					'name'         => 'energy_1_tag',
					'type'         => 'text',
					'default_value'=> 'Wind',
				),
				array(
					'key'          => 'field_energy_1_title',
					'label'        => __( 'Card 1 — Title', 'greenio' ),
					'name'         => 'energy_1_title',
					'type'         => 'text',
					'default_value'=> 'Wind Power',
				),
				array(
					'key'          => 'field_energy_1_desc',
					'label'        => __( 'Card 1 — Description', 'greenio' ),
					'name'         => 'energy_1_desc',
					'type'         => 'textarea',
					'rows'         => 2,
					'default_value'=> 'Next-generation turbines convert steady coastal winds into round-the-clock renewable electricity.',
				),
				array(
					'key'           => 'field_energy_1_image',
					'label'         => __( 'Card 1 — Background Image', 'greenio' ),
					'name'          => 'energy_1_image',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'medium',
				),

				// -- Card 2: Water --
				array(
					'key'          => 'field_energy_2_tag',
					'label'        => __( 'Card 2 — Tag', 'greenio' ),
					'name'         => 'energy_2_tag',
					'type'         => 'text',
					'default_value'=> 'Water',
				),
				array(
					'key'          => 'field_energy_2_title',
					'label'        => __( 'Card 2 — Title', 'greenio' ),
					'name'         => 'energy_2_title',
					'type'         => 'text',
					'default_value'=> 'Hydroelectric',
				),
				array(
					'key'          => 'field_energy_2_desc',
					'label'        => __( 'Card 2 — Description', 'greenio' ),
					'name'         => 'energy_2_desc',
					'type'         => 'textarea',
					'rows'         => 2,
					'default_value'=> 'Modern hydro plants deliver clean, dependable baseload power from the flow of water.',
				),
				array(
					'key'           => 'field_energy_2_image',
					'label'         => __( 'Card 2 — Background Image', 'greenio' ),
					'name'          => 'energy_2_image',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'medium',
				),

				// -- Card 3: Solar --
				array(
					'key'          => 'field_energy_3_tag',
					'label'        => __( 'Card 3 — Tag', 'greenio' ),
					'name'         => 'energy_3_tag',
					'type'         => 'text',
					'default_value'=> 'Solar',
				),
				array(
					'key'          => 'field_energy_3_title',
					'label'        => __( 'Card 3 — Title', 'greenio' ),
					'name'         => 'energy_3_title',
					'type'         => 'text',
					'default_value'=> 'Solar Power',
				),
				array(
					'key'          => 'field_energy_3_desc',
					'label'        => __( 'Card 3 — Description', 'greenio' ),
					'name'         => 'energy_3_desc',
					'type'         => 'textarea',
					'rows'         => 2,
					'default_value'=> 'High-efficiency photovoltaic arrays capture the sun across fields, rooftops and farms.',
				),
				array(
					'key'           => 'field_energy_3_image',
					'label'         => __( 'Card 3 — Background Image', 'greenio' ),
					'name'          => 'energy_3_image',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'medium',
				),

				// -- Card 4: Storage --
				array(
					'key'          => 'field_energy_4_tag',
					'label'        => __( 'Card 4 — Tag', 'greenio' ),
					'name'         => 'energy_4_tag',
					'type'         => 'text',
					'default_value'=> 'Storage',
				),
				array(
					'key'          => 'field_energy_4_title',
					'label'        => __( 'Card 4 — Title', 'greenio' ),
					'name'         => 'energy_4_title',
					'type'         => 'text',
					'default_value'=> 'Smart Battery Storage',
				),
				array(
					'key'          => 'field_energy_4_desc',
					'label'        => __( 'Card 4 — Description', 'greenio' ),
					'name'         => 'energy_4_desc',
					'type'         => 'textarea',
					'rows'         => 2,
					'default_value'=> 'Grid-scale storage banks bottle the sun and wind for a stable, always-on clean supply.',
				),
				array(
					'key'           => 'field_energy_4_image',
					'label'         => __( 'Card 4 — Background Image', 'greenio' ),
					'name'          => 'energy_4_image',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'medium',
				),

				/* ---- STATS BAND ---- */
				array(
					'key'   => 'field_band_tab',
					'label' => __( 'Stats Band', 'greenio' ),
					'type'  => 'tab',
				),
				// -- Stat 1 --
				array(
					'key'          => 'field_band_1_number',
					'label'        => __( 'Stat 1 — Counter Target', 'greenio' ),
					'name'         => 'band_1_number',
					'type'         => 'number',
					'default_value'=> 1200,
				),
				array(
					'key'          => 'field_band_1_suffix',
					'label'        => __( 'Stat 1 — Suffix', 'greenio' ),
					'name'         => 'band_1_suffix',
					'type'         => 'text',
					'default_value'=> '+',
				),
				array(
					'key'          => 'field_band_1_label',
					'label'        => __( 'Stat 1 — Label', 'greenio' ),
					'name'         => 'band_1_label',
					'type'         => 'text',
					'default_value'=> 'Projects delivered',
				),
				// -- Stat 2 --
				array(
					'key'          => 'field_band_2_number',
					'label'        => __( 'Stat 2 — Counter Target', 'greenio' ),
					'name'         => 'band_2_number',
					'type'         => 'number',
					'default_value'=> 98,
				),
				array(
					'key'          => 'field_band_2_suffix',
					'label'        => __( 'Stat 2 — Suffix', 'greenio' ),
					'name'         => 'band_2_suffix',
					'type'         => 'text',
					'default_value'=> '%',
				),
				array(
					'key'          => 'field_band_2_label',
					'label'        => __( 'Stat 2 — Label', 'greenio' ),
					'name'         => 'band_2_label',
					'type'         => 'text',
					'default_value'=> 'Client satisfaction',
				),
				// -- Stat 3 --
				array(
					'key'          => 'field_band_3_number',
					'label'        => __( 'Stat 3 — Counter Target', 'greenio' ),
					'name'         => 'band_3_number',
					'type'         => 'number',
					'default_value'=> 45,
				),
				array(
					'key'          => 'field_band_3_suffix',
					'label'        => __( 'Stat 3 — Suffix', 'greenio' ),
					'name'         => 'band_3_suffix',
					'type'         => 'text',
					'default_value'=> 'k+',
				),
				array(
					'key'          => 'field_band_3_label',
					'label'        => __( 'Stat 3 — Label', 'greenio' ),
					'name'         => 'band_3_label',
					'type'         => 'text',
					'default_value'=> 'Homes powered',
				),
				// -- Stat 4 --
				array(
					'key'          => 'field_band_4_number',
					'label'        => __( 'Stat 4 — Counter Target', 'greenio' ),
					'name'         => 'band_4_number',
					'type'         => 'number',
					'default_value'=> 15,
				),
				array(
					'key'          => 'field_band_4_suffix',
					'label'        => __( 'Stat 4 — Suffix', 'greenio' ),
					'name'         => 'band_4_suffix',
					'type'         => 'text',
					'default_value'=> 'yrs',
				),
				array(
					'key'          => 'field_band_4_label',
					'label'        => __( 'Stat 4 — Label', 'greenio' ),
					'name'         => 'band_4_label',
					'type'         => 'text',
					'default_value'=> 'Years of expertise',
				),

				/* ---- PROJECTS (Featured work) ---- */
				array(
					'key'   => 'field_projects_tab',
					'label' => __( 'Projects', 'greenio' ),
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_projects_eyebrow',
					'label'        => __( 'Section Eyebrow', 'greenio' ),
					'name'         => 'projects_eyebrow',
					'type'         => 'text',
					'default_value'=> 'Featured work',
				),
				array(
					'key'          => 'field_projects_title',
					'label'        => __( 'Section Title', 'greenio' ),
					'name'         => 'projects_title',
					'type'         => 'text',
					'default_value'=> 'Powering communities, one project at a time.',
				),
				// -- Project 1 --
				array(
					'key'          => 'field_project_1_tag',
					'label'        => __( 'Project 1 — Tag', 'greenio' ),
					'name'         => 'project_1_tag',
					'type'         => 'text',
					'default_value'=> 'Solar Farm',
				),
				array(
					'key'          => 'field_project_1_title',
					'label'        => __( 'Project 1 — Title', 'greenio' ),
					'name'         => 'project_1_title',
					'type'         => 'text',
					'default_value'=> 'Sunfield Array — 42 MW',
				),
				array(
					'key'           => 'field_project_1_image',
					'label'         => __( 'Project 1 — Image', 'greenio' ),
					'name'          => 'project_1_image',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'medium',
				),
				// -- Project 2 --
				array(
					'key'          => 'field_project_2_tag',
					'label'        => __( 'Project 2 — Tag', 'greenio' ),
					'name'         => 'project_2_tag',
					'type'         => 'text',
					'default_value'=> 'Wind',
				),
				array(
					'key'          => 'field_project_2_title',
					'label'        => __( 'Project 2 — Title', 'greenio' ),
					'name'         => 'project_2_title',
					'type'         => 'text',
					'default_value'=> 'Coastal Breeze Park',
				),
				array(
					'key'           => 'field_project_2_image',
					'label'         => __( 'Project 2 — Image', 'greenio' ),
					'name'          => 'project_2_image',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'medium',
				),
				// -- Project 3 --
				array(
					'key'          => 'field_project_3_tag',
					'label'        => __( 'Project 3 — Tag', 'greenio' ),
					'name'         => 'project_3_tag',
					'type'         => 'text',
					'default_value'=> 'Hydro',
				),
				array(
					'key'          => 'field_project_3_title',
					'label'        => __( 'Project 3 — Title', 'greenio' ),
					'name'         => 'project_3_title',
					'type'         => 'text',
					'default_value'=> 'Riverstone Plant',
				),
				array(
					'key'           => 'field_project_3_image',
					'label'         => __( 'Project 3 — Image', 'greenio' ),
					'name'          => 'project_3_image',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'medium',
				),

				/* ---- CTA (Ready to switch?) ---- */
				array(
					'key'   => 'field_cta_tab',
					'label' => __( 'CTA Section', 'greenio' ),
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_cta_eyebrow',
					'label'        => __( 'Eyebrow Text', 'greenio' ),
					'name'         => 'cta_eyebrow',
					'type'         => 'text',
					'default_value'=> 'Ready to switch?',
				),
				array(
					'key'          => 'field_cta_title',
					'label'        => __( 'Main Title', 'greenio' ),
					'name'         => 'cta_title',
					'type'         => 'text',
					'default_value'=> 'Start powering your world with clean energy today.',
				),
				array(
					'key'          => 'field_cta_placeholder',
					'label'        => __( 'Email Input Placeholder', 'greenio' ),
					'name'         => 'cta_placeholder',
					'type'         => 'text',
					'default_value'=> 'Enter your email address',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'hide_on_screen'        => array( 'the_content' ),
			'active'                => true,
			'description'           => __( 'Editable content for the Greenio front page.', 'greenio' ),
		)
	);

	/* --------------------------------------------------------------------
	 * GROUP 2 — Global Settings (Options page): logo + header CTA + footer
	 * ------------------------------------------------------------------ */
	acf_add_local_field_group(
		array(
			'key'    => 'group_greenio_options',
			'title'  => __( 'Greenio — Global Settings', 'greenio' ),
			'fields' => array(
				array(
					'key'   => 'field_opt_header_tab',
					'label' => __( 'Logo & Header', 'greenio' ),
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_opt_logo_text',
					'label'        => __( 'Logo Text', 'greenio' ),
					'name'         => 'logo_text',
					'type'         => 'text',
					'instructions' => __( 'Used when no logo image is set. Wrap the accent part in [g]...[/g] (e.g. Green[g]io[/g]).', 'greenio' ),
					'default_value'=> 'Green[g]io[/g]',
				),
				array(
					'key'           => 'field_opt_logo_image',
					'label'         => __( 'Logo Image', 'greenio' ),
					'name'          => 'logo_image',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'thumbnail',
					'instructions'  => __( 'Optional. Overrides the text logo when set.', 'greenio' ),
				),
				array(
					'key'          => 'field_opt_header_cta_text',
					'label'        => __( 'Header CTA Text', 'greenio' ),
					'name'         => 'header_cta_text',
					'type'         => 'text',
					'default_value'=> 'Get Started',
				),
				array(
					'key'          => 'field_opt_header_cta_link',
					'label'        => __( 'Header CTA Link', 'greenio' ),
					'name'         => 'header_cta_link',
					'type'         => 'text',
					'default_value'=> '#contact',
				),
				array(
					'key'   => 'field_opt_footer_tab',
					'label' => __( 'Footer', 'greenio' ),
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_opt_footer_about',
					'label'        => __( 'Footer About Text', 'greenio' ),
					'name'         => 'footer_about',
					'type'         => 'textarea',
					'rows'         => 3,
					'default_value'=> 'The better source of energy for the better tomorrow. 100% clean. 100% future-ready.',
				),
				array(
					'key'          => 'field_opt_footer_email',
					'label'        => __( 'Contact Email', 'greenio' ),
					'name'         => 'footer_email',
					'type'         => 'text',
					'default_value'=> 'hello@greenio.energy',
				),
				array(
					'key'          => 'field_opt_footer_phone',
					'label'        => __( 'Contact Phone', 'greenio' ),
					'name'         => 'footer_phone',
					'type'         => 'text',
					'default_value'=> '+1 (800) 555-0199',
				),
				array(
					'key'          => 'field_opt_footer_address',
					'label'        => __( 'Address', 'greenio' ),
					'name'         => 'footer_address',
					'type'         => 'text',
					'default_value'=> '123 Clean Energy Blvd',
				),
				array(
					'key'          => 'field_opt_footer_copyright',
					'label'        => __( 'Copyright Note', 'greenio' ),
					'name'         => 'footer_copyright',
					'type'         => 'text',
					'default_value'=> 'Crafted for a sustainable world.',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'greenio-settings',
					),
				),
			),
			'active'      => true,
			'description' => __( 'Global header & footer settings for the Greenio theme.', 'greenio' ),
		)
	);
}
add_action( 'acf/init', 'greenio_register_acf_fields' );

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
 * escapes any custom logo text set from the ACF Options page.
 *
 * @param string $text Logo text (from ACF option, with fallback).
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
 * Admin notice nudging the user to install ACF (dismissible, non-fatal).
 */
function greenio_acf_admin_notice() {
	if ( greenio_acf() || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-info is-dismissible"><p><strong>Greenio:</strong> ';
	echo esc_html__( 'Install & activate the free "Advanced Custom Fields" plugin to edit all front-page content from the dashboard. The theme works fine without it using default content.', 'greenio' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'greenio_acf_admin_notice' );
