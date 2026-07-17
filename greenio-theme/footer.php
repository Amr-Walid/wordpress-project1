<?php
/**
 * The footer for the Greenio theme.
 *
 * Closes the main content area and renders the footer + closing tags.
 *
 * @package Greenio
 */
?>
</main><!-- #content -->

<footer class="site-footer" id="pages">
	<div class="container footer-grid">

		<div class="footer-brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo logo--light">
				<span class="logo-mark" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M12 2C7 6 5 10 5 14a7 7 0 0 0 14 0c0-1.5-.4-3-1.2-4.4C16.6 12 14 13 12 13c0-3 0-8 0-11Z" fill="currentColor"/>
					</svg>
				</span>
				<span class="logo-text">Green<span>io</span></span>
			</a>
			<p><?php echo esc_html( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : __( 'The better source of energy for the better tomorrow. 100% clean. 100% future-ready.', 'greenio' ) ); ?></p>
		</div>

		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
			<?php dynamic_sidebar( 'footer-1' ); ?>
		<?php else : ?>

			<div class="footer-col">
				<h4><?php esc_html_e( 'Company', 'greenio' ); ?></h4>
				<a href="#about"><?php esc_html_e( 'About us', 'greenio' ); ?></a>
				<a href="#services"><?php esc_html_e( 'Services', 'greenio' ); ?></a>
				<a href="#projects"><?php esc_html_e( 'Projects', 'greenio' ); ?></a>
				<a href="#contact"><?php esc_html_e( 'Contact', 'greenio' ); ?></a>
			</div>

			<div class="footer-col">
				<h4><?php esc_html_e( 'Services', 'greenio' ); ?></h4>
				<a href="#services"><?php esc_html_e( 'Solar Power', 'greenio' ); ?></a>
				<a href="#services"><?php esc_html_e( 'Wind Power', 'greenio' ); ?></a>
				<a href="#services"><?php esc_html_e( 'Hydroelectric', 'greenio' ); ?></a>
				<a href="#services"><?php esc_html_e( 'Microgrid Planning', 'greenio' ); ?></a>
			</div>

			<div class="footer-col">
				<h4><?php esc_html_e( 'Get in touch', 'greenio' ); ?></h4>
				<a href="mailto:hello@greenio.energy">hello@greenio.energy</a>
				<a href="tel:+18005550199">+1 (800) 555-0199</a>
				<a href="#">123 Clean Energy Blvd</a>
			</div>

		<?php endif; ?>

	</div>

	<div class="container footer-bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'greenio' ); ?></p>
		<p><?php esc_html_e( 'Crafted for a sustainable world.', 'greenio' ); ?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
