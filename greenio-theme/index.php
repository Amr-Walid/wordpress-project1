<?php
/**
 * The main template file — Greenio one-page landing.
 *
 * Recreates the reference design: hero with full-width background, the floating
 * hero box, the overlapping deep-blue stats card, the 4-icon service grid, the
 * zig-zag "Keep your environment clean" section, plus stats band, projects & CTA.
 *
 * @package Greenio
 */

get_header();

/*
 * Placeholder imagery.
 *
 * By default the theme uses self-contained images bundled in /assets/img so the
 * demo renders anywhere (even offline). If you prefer the live Unsplash
 * placeholders requested in the brief, drop this snippet into a child theme or
 * a mu-plugin — the array is fully filterable:
 *
 *   add_filter( 'greenio_placeholder_images', function () {
 *       return array(
 *           'hero'    => 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?auto=format&fit=crop&w=2000&q=80',
 *           'turbine' => 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?auto=format&fit=crop&w=1200&q=80',
 *           'hydro'   => 'https://images.unsplash.com/photo-1489447068241-b3490214e879?auto=format&fit=crop&w=1200&q=80',
 *           'solar'   => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?auto=format&fit=crop&w=1200&q=80',
 *           'storage' => 'https://images.unsplash.com/photo-1548337138-e87d889cc369?auto=format&fit=crop&w=1200&q=80',
 *       );
 *   } );
 */
$greenio_img = apply_filters(
	'greenio_placeholder_images',
	array(
		'hero'    => greenio_asset( 'assets/img/hero.jpg' ),    // wind farm
		'turbine' => greenio_asset( 'assets/img/wind.jpg' ),    // single turbine
		'hydro'   => greenio_asset( 'assets/img/hydro.jpg' ),   // hydro dam
		'solar'   => greenio_asset( 'assets/img/solar.jpg' ),   // solar farm
		'storage' => greenio_asset( 'assets/img/storage.jpg' ), // wind farm wide
	)
);
?>

<!-- ============ HERO ============ -->
<section class="hero" id="home">
	<div class="hero-bg" style="background-image:url('<?php echo esc_url( $greenio_img['hero'] ); ?>')"></div>
	<div class="hero-overlay"></div>

	<div class="container hero-container">
		<div class="hero-card" data-reveal>
			<span class="eyebrow eyebrow-center"><?php esc_html_e( 'Welcome to Greenio', 'greenio' ); ?></span>
			<h1 class="hero-title">
				<?php esc_html_e( 'The better source of energy', 'greenio' ); ?><br class="d-desktop">
				<?php esc_html_e( 'for the', 'greenio' ); ?> <span class="grad"><?php esc_html_e( 'better tomorrow', 'greenio' ); ?></span>
			</h1>
			<p class="hero-sub">
				<?php esc_html_e( 'Help protect the environment by powering your home and business with 100% clean, renewable energy — engineered for the future.', 'greenio' ); ?>
			</p>
			<div class="hero-cta">
				<a href="#contact" class="btn btn-yellow"><?php esc_html_e( 'Get Started', 'greenio' ); ?>
					<svg class="btn-arrow" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</a>
				<a href="#services" class="btn btn-ghost">
					<span class="play-dot" aria-hidden="true"></span> <?php esc_html_e( 'Discover more', 'greenio' ); ?>
				</a>
			</div>
		</div>
	</div>

	<!-- Overlapping foreground row: deep-blue stat card + 4 service cards -->
	<div class="container float-row-wrap">
		<div class="float-row">

			<!-- THE DEEP BLUE STAT CARD (overlaps hero + section below) -->
			<article class="stat-card" data-reveal>
				<div class="stat-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z" fill="currentColor"/></svg>
				</div>
				<p class="stat-label"><?php esc_html_e( 'Since 2010, our customers have avoided', 'greenio' ); ?></p>
				<p class="stat-number"><span class="counter" data-target="112845311">0</span><span class="plus">+</span></p>
				<p class="stat-unit"><?php esc_html_e( 'pounds of CO', 'greenio' ); ?><sub>2</sub></p>
				<div class="stat-live"><span class="live-dot"></span> <?php esc_html_e( 'Live impact counter', 'greenio' ); ?></div>
			</article>

			<!-- SERVICE ICON CARDS -->
			<article class="svc-card" data-reveal>
				<div class="svc-ico"><svg viewBox="0 0 24 24" fill="none"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z" stroke="currentColor" stroke-width="1.6"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></div>
				<h3><?php esc_html_e( 'Installation', 'greenio' ); ?></h3>
				<p><?php esc_html_e( 'Turn-key solar & wind installation, engineered and commissioned by certified specialists.', 'greenio' ); ?></p>
				<a href="#services" class="learn"><?php esc_html_e( 'Learn more', 'greenio' ); ?></a>
			</article>

			<article class="svc-card" data-reveal>
				<div class="svc-ico"><svg viewBox="0 0 24 24" fill="none"><path d="M14.7 6.3a3.5 3.5 0 0 0-4.6 4.6L3 18l3 3 7.1-7.1a3.5 3.5 0 0 0 4.6-4.6l-2.2 2.2-2-2 2.2-2.2Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg></div>
				<h3><?php esc_html_e( 'Maintenance', 'greenio' ); ?></h3>
				<p><?php esc_html_e( 'Predictive monitoring and rapid servicing keep every panel and turbine at peak output.', 'greenio' ); ?></p>
				<a href="#services" class="learn"><?php esc_html_e( 'Learn more', 'greenio' ); ?></a>
			</article>

			<article class="svc-card" data-reveal>
				<div class="svc-ico"><svg viewBox="0 0 24 24" fill="none"><path d="M4 5h16v10H8l-4 4V5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8 9h8M8 12h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></div>
				<h3><?php esc_html_e( 'Consultation', 'greenio' ); ?></h3>
				<p><?php esc_html_e( 'Data-driven energy audits map the fastest, cleanest path to your net-zero goals.', 'greenio' ); ?></p>
				<a href="#services" class="learn"><?php esc_html_e( 'Learn more', 'greenio' ); ?></a>
			</article>

			<article class="svc-card svc-card--accent" data-reveal>
				<div class="svc-ico"><svg viewBox="0 0 24 24" fill="none"><path d="M3 12h4l2-5 4 12 2-5h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
				<h3><?php esc_html_e( 'Microgrid Planning', 'greenio' ); ?></h3>
				<p><?php esc_html_e( 'Resilient, AI-optimized microgrids that keep the lights on — fully independent of the grid.', 'greenio' ); ?></p>
				<a href="#services" class="learn"><?php esc_html_e( 'Learn more', 'greenio' ); ?></a>
			</article>

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
			<span class="eyebrow"><?php esc_html_e( 'Who we are', 'greenio' ); ?></span>
			<h2 class="section-title"><?php esc_html_e( 'Keep your environment', 'greenio' ); ?><br class="d-desktop"> <?php esc_html_e( 'clean, make the earth green.', 'greenio' ); ?></h2>
			<p><?php esc_html_e( 'For over a decade Greenio has designed, built and maintained renewable systems that pay for themselves — while cutting millions of pounds of carbon from the atmosphere.', 'greenio' ); ?></p>
			<ul class="check-list">
				<li><?php esc_html_e( 'Certified engineers & 25-year performance warranty', 'greenio' ); ?></li>
				<li><?php esc_html_e( 'Real-time energy dashboards on every install', 'greenio' ); ?></li>
				<li><?php esc_html_e( 'Zero-emission supply from source to socket', 'greenio' ); ?></li>
			</ul>
			<a href="#contact" class="btn btn-yellow"><?php esc_html_e( 'Discover more', 'greenio' ); ?>
				<svg class="btn-arrow" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</a>
		</div>

		<div class="about-media" data-reveal>
			<div class="about-img parallax" data-speed="0.12" style="background-image:url('<?php echo esc_url( $greenio_img['turbine'] ); ?>')"></div>
			<!-- Grid-breaking overlay card -->
			<div class="overlay-card">
				<div class="overlay-ico" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z" fill="currentColor"/></svg>
				</div>
				<span class="overlay-tag"><?php esc_html_e( 'Renewable energy', 'greenio' ); ?></span>
				<p class="overlay-title"><?php esc_html_e( 'Energy is the future,', 'greenio' ); ?><br> <?php esc_html_e( 'make it brilliant.', 'greenio' ); ?></p>
			</div>
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
			<button type="submit" class="btn btn-yellow"><?php esc_html_e( 'Get Started', 'greenio' ); ?></button>
		</form>
	</div>
</section>

<?php get_footer(); ?>
