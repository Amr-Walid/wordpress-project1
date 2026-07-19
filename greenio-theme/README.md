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
├── functions.php      # Enqueues assets, theme supports, menus, widgets + Carbon Fields
├── header.php         # <head>, wp_head(), logo, centered nav, yellow CTA (theme options)
├── footer.php         # Footer layout (theme options), wp_footer(), closing tags
├── index.php          # Landing template (Carbon Fields-powered, graceful fallbacks)
├── front-page.php     # Static front-page template (mirror of index.php)
├── composer.json      # Declares the htmlburger/carbon-fields dependency
├── composer.lock      # Locked to Carbon Fields v3.6.x
├── vendor/            # Bundled Carbon Fields library (committed → works out-of-the-box)
├── README.md
└── assets/
    ├── js/main.js     # Live counters, smooth scroll, parallax, micro-interactions
    └── img/           # Bundled demo images (self-contained)
```

## Content management — Carbon Fields (free & open-source)

All editable content is powered by **[Carbon Fields](https://carbonfields.net/)**,
a free MIT-licensed fields library bundled *inside* the theme via Composer — **no
plugin required**. It is loaded through the Composer autoloader and booted on the
`after_setup_theme` hook (`Carbon_Fields\Carbon_Fields::boot()`); containers are
declared on the `carbon_fields_register_fields` action.

Two containers are registered in `functions.php`:

1. **Theme Options page** (`Appearance/side menu → Theme Settings`) — global
   settings: Logo Text/Image, Header CTA Text/Link, and Footer About/Email/
   Phone/Address/Copyright. Read in templates with `carbon_get_theme_option()`.
2. **Front Page** post-meta container — shown on the page set under
   *Settings → Reading → page_on_front*. Includes three **`complex` fields**
   (Carbon Fields' free repeater):
   - `services` — Title, Description, Link, Icon (image), Featured (checkbox)
   - `stats_band` — Number, Suffix, Label
   - `projects` — Tag, Title, Image
   Plus Hero, Stats Card, About/Zig-Zag, Energy Grid and CTA fields. Read in
   templates with `carbon_get_post_meta()`.

Every value is wrapped by the `greenio_field()` / `greenio_image()` helpers,
which return the stored value when present and otherwise fall back to the
built-in default content — so the site renders perfectly even before any field
is filled in (or if the bundled library is ever unavailable).

> **Rebuilding the library:** `vendor/` is committed so the theme works on
> upload. To regenerate it, run `composer install` inside the theme folder.

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
