<?php
/**
 * Front Page template — Greenio one-page landing (ACF-powered).
 *
 * All content is editable from the WordPress dashboard when Advanced Custom
 * Fields is active (see the field groups registered in functions.php). Every
 * field is wrapped with the greenio_field() / greenio_image() helpers, which
 * fall back to sensible defaults so the layout NEVER breaks when a field is
 * empty or when ACF is not installed.
 *
 * @package Greenio
 */

get_header();

/* -------------------------------------------------------------------------
 * Resolve all fields up-front (with defaults). $post_id defaults to the
 * current (front) page for get_field().
 * ---------------------------------------------------------------------- */

// Hero.
$hero_eyebrow   = greenio_field( 'hero_eyebrow', __( 'Welcome to Greenio', 'greenio' ) );
$hero_title     = greenio_field( 'hero_title', 'The better source of energy for the [g]better tomorrow[/g]' );
$hero_subtitle  = greenio_field( 'hero_subtitle', __( 'Help protect the environment by powering your home and business with 100% clean, renewable energy — engineered for the future.', 'greenio' ) );
$hero_cta_text  = greenio_field( 'hero_cta_text', __( 'Get Started', 'greenio' ) );
$hero_cta_link  = greenio_field( 'hero_cta_link', '#contact' );
$hero_cta2_text = greenio_field( 'hero_cta2_text', __( 'Discover more', 'greenio' ) );
$hero_cta2_link = greenio_field( 'hero_cta2_link', '#services' );
$hero_bg        = greenio_image( 'hero_bg', 'assets/img/hero.jpg' );

// Stats card.
$stat_label  = greenio_field( 'stat_label', __( 'Since 2010, our customers have avoided', 'greenio' ) );
$stat_number = (int) greenio_field( 'stat_number', 112845311 );
$stat_unit   = greenio_field( 'stat_unit', 'pounds of CO₂' );

// Services grid (repeater). Fall back to a hardcoded set if empty / no ACF PRO.
$services = greenio_field( 'services', array() );
if ( empty( $services ) || ! is_array( $services ) ) {
	$services = array(
		array( 'title' => __( 'Installation', 'greenio' ),        'description' => __( 'Turn-key solar & wind installation, engineered and commissioned by certified specialists.', 'greenio' ), 'link' => '#services', 'featured' => false, 'icon' => '' ),
		array( 'title' => __( 'Maintenance', 'greenio' ),         'description' => __( 'Predictive monitoring and rapid servicing keep every panel and turbine at peak output.', 'greenio' ),      'link' => '#services', 'featured' => false, 'icon' => '' ),
		array( 'title' => __( 'Consultation', 'greenio' ),        'description' => __( 'Data-driven energy audits map the fastest, cleanest path to your net-zero goals.', 'greenio' ),           'link' => '#services', 'featured' => false, 'icon' => '' ),
		array( 'title' => __( 'Microgrid Planning', 'greenio' ),  'description' => __( 'Resilient, AI-optimized microgrids that keep the lights on — fully independent of the grid.', 'greenio' ),   'link' => '#services', 'featured' => true,  'icon' => '' ),
	);
}

// Built-in SVG icons cycled through service cards that have no custom icon.
$greenio_default_icons = array(
	'<svg viewBox="0 0 24 24" fill="none"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z" stroke="currentColor" stroke-width="1.6"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
	'<svg viewBox="0 0 24 24" fill="none"><path d="M14.7 6.3a3.5 3.5 0 0 0-4.6 4.6L3 18l3 3 7.1-7.1a3.5 3.5 0 0 0 4.6-4.6l-2.2 2.2-2-2 2.2-2.2Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
	'<svg viewBox="0 0 24 24" fill="none"><path d="M4 5h16v10H8l-4 4V5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8 9h8M8 12h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
	'<svg viewBox="0 0 24 24" fill="none"><path d="M3 12h4l2-5 4 12 2-5h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
);

// About / zig-zag.
$about_eyebrow       = greenio_field( 'about_eyebrow', __( 'Who we are', 'greenio' ) );
$about_title         = greenio_field( 'about_title', __( 'Keep your environment clean, make the earth green.', 'greenio' ) );
$about_desc          = greenio_field( 'about_desc', __( 'For over a decade Greenio has designed, built and maintained renewable systems that pay for themselves — while cutting millions of pounds of carbon from the atmosphere.', 'greenio' ) );
$about_bullets_raw   = greenio_field( 'about_bullets', "Certified engineers & 25-year performance warranty\nReal-time energy dashboards on every install\nZero-emission supply from source to socket" );
$about_bullets       = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $about_bullets_raw ) ) );
$about_image         = greenio_image( 'about_image', 'assets/img/wind.jpg' );
$about_overlay_tag   = greenio_field( 'about_overlay_tag', __( 'Renewable energy', 'greenio' ) );
$about_overlay_title = greenio_field( 'about_overlay_title', __( 'Energy is the future, make it brilliant.', 'greenio' ) );

/* Energy grid images (kept theme-managed & filterable, as before). */
$greenio_img = apply_filters(
	'greenio_placeholder_images',
	array(
		'turbine' => greenio_asset( 'assets/img/wind.jpg' ),
		'hydro'   => greenio_asset( 'assets/img/hydro.jpg' ),
		'solar'   => greenio_asset( 'assets/img/solar.jpg' ),
		'storage' => greenio_asset( 'assets/img/storage.jpg' ),
	)
);
?>

<!-- ============ HERO ============ -->
<section class="hero" id="home">
	<?php if ( $hero_bg ) : ?>
		<div class="hero-bg" style="background-image:url('<?php echo esc_url( $hero_bg ); ?>')"></div>
	<?php endif; ?>
	<div class="hero-overlay"></div>

	<div class="container hero-container">
		<div class="hero-card" data-reveal>
			<?php if ( $hero_eyebrow ) : ?>
				<span class="eyebrow eyebrow-center"><?php echo esc_html( $hero_eyebrow ); ?></span>
			<?php endif; ?>

			<?php if ( $hero_title ) : ?>
				<h1 class="hero-title"><?php echo greenio_gradient_text( $hero_title ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
			<?php endif; ?>

			<?php if ( $hero_subtitle ) : ?>
				<p class="hero-sub"><?php echo esc_html( $hero_subtitle ); ?></p>
			<?php endif; ?>

			<div class="hero-cta">
				<?php if ( $hero_cta_text ) : ?>
					<a href="<?php echo esc_url( $hero_cta_link ); ?>" class="btn btn-yellow"><?php echo esc_html( $hero_cta_text ); ?>
						<svg class="btn-arrow" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</a>
				<?php endif; ?>
				<?php if ( $hero_cta2_text ) : ?>
					<a href="<?php echo esc_url( $hero_cta2_link ); ?>" class="btn btn-ghost">
						<span class="play-dot" aria-hidden="true"></span> <?php echo esc_html( $hero_cta2_text ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Overlapping foreground row: deep-blue stat card + service cards -->
	<div class="container float-row-wrap">
		<div class="float-row">

			<!-- THE DEEP BLUE STAT CARD -->
			<article class="stat-card" data-reveal>
				<div class="stat-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z" fill="currentColor"/></svg>
				</div>
				<?php if ( $stat_label ) : ?>
					<p class="stat-label"><?php echo esc_html( $stat_label ); ?></p>
				<?php endif; ?>
				<p class="stat-number"><span class="counter" data-target="<?php echo esc_attr( $stat_number ); ?>">0</span><span class="plus">+</span></p>
				<?php if ( $stat_unit ) : ?>
					<p class="stat-unit"><?php echo esc_html( $stat_unit ); ?></p>
				<?php endif; ?>
				<div class="stat-live"><span class="live-dot"></span> <?php esc_html_e( 'Live impact counter', 'greenio' ); ?></div>
			</article>

			<!-- SERVICE ICON CARDS (repeater) -->
			<?php
			$i = 0;
			foreach ( $services as $svc ) :
				$svc_title    = isset( $svc['title'] ) ? $svc['title'] : '';
				$svc_desc     = isset( $svc['description'] ) ? $svc['description'] : '';
				$svc_link     = ! empty( $svc['link'] ) ? $svc['link'] : '#services';
				$svc_featured = ! empty( $svc['featured'] );
				$svc_icon     = '';
				// Custom icon image (URL/array/ID) if supplied.
				if ( ! empty( $svc['icon'] ) ) {
					if ( is_array( $svc['icon'] ) ) {
						$svc_icon = ! empty( $svc['icon']['url'] ) ? $svc['icon']['url'] : '';
					} elseif ( is_numeric( $svc['icon'] ) ) {
						$svc_icon = wp_get_attachment_image_url( (int) $svc['icon'], 'thumbnail' );
					} else {
						$svc_icon = $svc['icon'];
					}
				}
				if ( ! $svc_title && ! $svc_desc ) {
					continue; // skip fully-empty rows.
				}
				?>
				<article class="svc-card<?php echo $svc_featured ? ' svc-card--accent' : ''; ?>" data-reveal>
					<div class="svc-ico">
						<?php if ( $svc_icon ) : ?>
							<img src="<?php echo esc_url( $svc_icon ); ?>" alt="<?php echo esc_attr( $svc_title ); ?>" width="28" height="28" />
						<?php else : ?>
							<?php echo $greenio_default_icons[ $i % count( $greenio_default_icons ) ]; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php endif; ?>
					</div>
					<?php if ( $svc_title ) : ?>
						<h3><?php echo esc_html( $svc_title ); ?></h3>
					<?php endif; ?>
					<?php if ( $svc_desc ) : ?>
						<p><?php echo esc_html( $svc_desc ); ?></p>
					<?php endif; ?>
					<a href="<?php echo esc_url( $svc_link ); ?>" class="learn"><?php esc_html_e( 'Learn more', 'greenio' ); ?></a>
				</article>
				<?php
				$i++;
			endforeach;
			?>

		</div>
	</div>
</section>

<!-- ============ SERVICES / ENERGY GRID ============ -->
<section class="services" id="services">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow eyebrow-center"><?php esc_html_e( 'What we offer', 'greenio' ); ?></span>
			<h2 class="section-title"><?php esc_html_e( "A choice that's good for you", 'greenio' ); ?><br class="d-desktop"> <?php esc_html_e( 'and the planet.', 'greenio' ); ?></h2>
			<p class="section-lead"><?php esc_html_e( 'From flowing rivers to open fields, Greenio harnesses every source of clean power with technology built for a sustainable world.', 'greenio' ); ?></p>
		</div>

		<div class="energy-grid">
			<article class="energy-card energy-card--tall" data-reveal>
				<div class="energy-img" style="background-image:url('<?php echo esc_url( $greenio_img['turbine'] ); ?>')"></div>
				<div class="energy-body">
					<span class="tag"><?php esc_html_e( 'Wind', 'greenio' ); ?></span>
					<h3><?php esc_html_e( 'Wind Power', 'greenio' ); ?></h3>
					<p><?php esc_html_e( 'Next-generation turbines convert steady coastal winds into round-the-clock renewable electricity.', 'greenio' ); ?></p>
				</div>
			</article>

			<article class="energy-card" data-reveal>
				<div class="energy-img" style="background-image:url('<?php echo esc_url( $greenio_img['hydro'] ); ?>')"></div>
				<div class="energy-body">
					<span class="tag"><?php esc_html_e( 'Water', 'greenio' ); ?></span>
					<h3><?php esc_html_e( 'Hydroelectric', 'greenio' ); ?></h3>
					<p><?php esc_html_e( 'Modern hydro plants deliver clean, dependable baseload power from the flow of water.', 'greenio' ); ?></p>
				</div>
			</article>

			<article class="energy-card" data-reveal>
				<div class="energy-img" style="background-image:url('<?php echo esc_url( $greenio_img['solar'] ); ?>')"></div>
				<div class="energy-body">
					<span class="tag"><?php esc_html_e( 'Solar', 'greenio' ); ?></span>
					<h3><?php esc_html_e( 'Solar Power', 'greenio' ); ?></h3>
					<p><?php esc_html_e( 'High-efficiency photovoltaic arrays capture the sun across fields, rooftops and farms.', 'greenio' ); ?></p>
				</div>
			</article>

			<article class="energy-card energy-card--wide" data-reveal>
				<div class="energy-img" style="background-image:url('<?php echo esc_url( $greenio_img['storage'] ); ?>')"></div>
				<div class="energy-body">
					<span class="tag"><?php esc_html_e( 'Storage', 'greenio' ); ?></span>
					<h3><?php esc_html_e( 'Smart Battery Storage', 'greenio' ); ?></h3>
					<p><?php esc_html_e( 'Grid-scale storage banks bottle the sun and wind for a stable, always-on clean supply.', 'greenio' ); ?></p>
				</div>
			</article>
		</div>
	</div>
</section>

<!-- ============ ABOUT / ZIG-ZAG ============ -->
<section class="about" id="about">
	<div class="container about-inner">
		<div class="about-text" data-reveal>
			<?php if ( $about_eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $about_eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $about_title ) : ?>
				<h2 class="section-title"><?php echo esc_html( $about_title ); ?></h2>
			<?php endif; ?>
			<?php if ( $about_desc ) : ?>
				<p><?php echo esc_html( $about_desc ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $about_bullets ) ) : ?>
				<ul class="check-list">
					<?php foreach ( $about_bullets as $bullet ) : ?>
						<li><?php echo esc_html( $bullet ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<a href="<?php echo esc_url( $hero_cta_link ); ?>" class="btn btn-yellow"><?php esc_html_e( 'Discover more', 'greenio' ); ?>
				<svg class="btn-arrow" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</a>
		</div>

		<div class="about-media" data-reveal>
			<?php if ( $about_image ) : ?>
				<div class="about-img parallax" data-speed="0.12" style="background-image:url('<?php echo esc_url( $about_image ); ?>')"></div>
			<?php endif; ?>

			<?php if ( $about_overlay_tag || $about_overlay_title ) : ?>
				<div class="overlay-card">
					<div class="overlay-ico" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z" fill="currentColor"/></svg>
					</div>
					<?php if ( $about_overlay_tag ) : ?>
						<span class="overlay-tag"><?php echo esc_html( $about_overlay_tag ); ?></span>
					<?php endif; ?>
					<?php if ( $about_overlay_title ) : ?>
						<p class="overlay-title"><?php echo esc_html( $about_overlay_title ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<!-- ============ STATS BAND ============ -->
<section class="band">
	<div class="container band-grid">
		<div class="band-item" data-reveal><span class="band-num"><span class="counter" data-target="1200" data-suffix="+">0</span></span><span class="band-label"><?php esc_html_e( 'Projects delivered', 'greenio' ); ?></span></div>
		<div class="band-item" data-reveal><span class="band-num"><span class="counter" data-target="98" data-suffix="%">0</span></span><span class="band-label"><?php esc_html_e( 'Client satisfaction', 'greenio' ); ?></span></div>
		<div class="band-item" data-reveal><span class="band-num"><span class="counter" data-target="45" data-suffix="k+">0</span></span><span class="band-label"><?php esc_html_e( 'Homes powered', 'greenio' ); ?></span></div>
		<div class="band-item" data-reveal><span class="band-num"><span class="counter" data-target="15" data-suffix="yrs">0</span></span><span class="band-label"><?php esc_html_e( 'Years of expertise', 'greenio' ); ?></span></div>
	</div>
</section>

<!-- ============ PROJECTS ============ -->
<section class="projects" id="projects">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow eyebrow-center"><?php esc_html_e( 'Featured work', 'greenio' ); ?></span>
			<h2 class="section-title"><?php esc_html_e( 'Powering communities,', 'greenio' ); ?><br class="d-desktop"> <?php esc_html_e( 'one project at a time.', 'greenio' ); ?></h2>
		</div>
		<div class="project-grid">
			<article class="project-card" data-reveal>
				<div class="project-img" style="background-image:url('<?php echo esc_url( $greenio_img['solar'] ); ?>')"></div>
				<div class="project-meta"><span class="tag"><?php esc_html_e( 'Solar Farm', 'greenio' ); ?></span><h3><?php esc_html_e( 'Sunfield Array — 42 MW', 'greenio' ); ?></h3></div>
			</article>
			<article class="project-card" data-reveal>
				<div class="project-img" style="background-image:url('<?php echo esc_url( $greenio_img['storage'] ); ?>')"></div>
				<div class="project-meta"><span class="tag"><?php esc_html_e( 'Wind', 'greenio' ); ?></span><h3><?php esc_html_e( 'Coastal Breeze Park', 'greenio' ); ?></h3></div>
			</article>
			<article class="project-card" data-reveal>
				<div class="project-img" style="background-image:url('<?php echo esc_url( $greenio_img['hydro'] ); ?>')"></div>
				<div class="project-meta"><span class="tag"><?php esc_html_e( 'Hydro', 'greenio' ); ?></span><h3><?php esc_html_e( 'Riverstone Plant', 'greenio' ); ?></h3></div>
			</article>
		</div>
	</div>
</section>

<!-- ============ CTA ============ -->
<section class="cta" id="contact">
	<div class="container cta-inner" data-reveal>
		<div class="cta-copy">
			<span class="eyebrow eyebrow-light"><?php esc_html_e( 'Ready to switch?', 'greenio' ); ?></span>
			<h2><?php esc_html_e( 'Start powering your world with clean energy today.', 'greenio' ); ?></h2>
		</div>
		<form class="cta-form" onsubmit="return false;">
			<input type="email" placeholder="<?php esc_attr_e( 'Enter your email address', 'greenio' ); ?>" aria-label="<?php esc_attr_e( 'Email', 'greenio' ); ?>" required />
			<button type="submit" class="btn btn-yellow"><?php echo esc_html( $hero_cta_text ); ?></button>
		</form>
	</div>
</section>

<?php get_footer(); ?>
