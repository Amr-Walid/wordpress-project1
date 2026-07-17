# Greenio — Custom WordPress Theme

A premium, fully responsive one-page WordPress theme for renewable-energy
companies. Recreates the reference design with an **overlapping-card hero
layout** (a deep-blue CO₂ stats card that overlaps both the hero background and
the section below via CSS negative margins), a 4-icon service grid, and a
zig-zag "Keep your environment clean" section.

## Palette
- Deep professional blue `#123c8c`
- Vibrant green `#37b34a` / light `#7ed957`
- High-contrast yellow `#ffd21f` (CTAs)

## File structure
```
greenio-theme/
├── style.css          # WP theme header block + all premium CSS (overlap, responsive)
├── functions.php      # Enqueues style.css + main.js, theme supports, menus, widgets
├── header.php         # <head>, wp_head(), text logo, centered nav, yellow CTA
├── footer.php         # Footer layout, wp_footer(), closing tags
├── index.php          # Landing template: hero, stat card, service grid, zig-zag, CTA
├── README.md
└── assets/
    ├── js/main.js     # Live counters, smooth scroll, parallax, micro-interactions
    └── img/           # Bundled demo images (self-contained)
```

## Installation
1. Zip the `greenio-theme` folder (or use the provided `greenio-theme.zip`).
2. In WordPress: **Appearance → Themes → Add New → Upload Theme** → choose the zip → **Install** → **Activate**.
3. (Optional) **Appearance → Menus**: create a menu and assign it to the **Primary Menu** location. Without one, a sensible fallback menu is shown.
4. Set a static front page under **Settings → Reading** (recommended) so `index.php` is the landing page.

## Standard WordPress features used
- `get_header()`, `get_footer()`, `wp_head()`, `wp_footer()`, `wp_body_open()`
- `get_template_directory_uri()` for all assets, `get_stylesheet_uri()` for style.css
- `wp_enqueue_style()` / `wp_enqueue_script()` (proper enqueuing, no hardcoded tags)
- `add_theme_support()` for `post-thumbnails`, `title-tag`, `html5`, `custom-logo`
- `register_nav_menus()` (primary + footer) with a `fallback_cb`
- `register_sidebar()` for footer widgets
- Escaping (`esc_url`, `esc_html`, `esc_attr`) and i18n (`__`, `esc_html_e`) throughout

## Swapping images
Images are filterable. To use live Unsplash placeholders instead of the bundled
demo images, add this to a child theme or mu-plugin:

```php
add_filter( 'greenio_placeholder_images', function () {
    return array(
        'hero'    => 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?auto=format&fit=crop&w=2000&q=80',
        'turbine' => 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?auto=format&fit=crop&w=1200&q=80',
        'hydro'   => 'https://images.unsplash.com/photo-1489447068241-b3490214e879?auto=format&fit=crop&w=1200&q=80',
        'solar'   => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?auto=format&fit=crop&w=1200&q=80',
        'storage' => 'https://images.unsplash.com/photo-1548337138-e87d889cc369?auto=format&fit=crop&w=1200&q=80',
    );
} );
```

## License
GPL v2 or later.
