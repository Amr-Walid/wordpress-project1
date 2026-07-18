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

<?php
// ---- Global footer settings from Carbon Fields Theme Options (with fallbacks) ----
$footer_logo_text = greenio_field( 'logo_text', 'Greenio', 'option' );
$footer_logo_img  = greenio_image( 'logo_image', '', 'medium', 'option' );
$footer_about     = greenio_field( 'footer_about', ( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : __( 'The better source of energy for the better tomorrow. 100% clean. 100% future-ready.', 'greenio' ) ), 'option' );
$footer_email     = greenio_field( 'footer_email', 'hello@greenio.energy', 'option' );
$footer_phone     = greenio_field( 'footer_phone', '+1 (800) 555-0199', 'option' );
$footer_address   = greenio_field( 'footer_address', '123 Clean Energy Blvd', 'option' );
$footer_copyright = greenio_field( 'footer_copyright', __( 'Crafted for a sustainable world.', 'greenio' ), 'option' );
?>
<footer class="site-footer" id="pages">
	<div class="container footer-grid">

		<div class="footer-brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo logo--light">
				<?php if ( $footer_logo_img ) : ?>
					<img src="<?php echo esc_url( $footer_logo_img ); ?>" alt="<?php echo esc_attr( $footer_logo_text ? $footer_logo_text : get_bloginfo( 'name' ) ); ?>" class="logo-img" />
				<?php else : ?>
					<span class="logo-mark" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12 2C7 6 5 10 5 14a7 7 0 0 0 14 0c0-1.5-.4-3-1.2-4.4C16.6 12 14 13 12 13c0-3 0-8 0-11Z" fill="currentColor"/>
						</svg>
					</span>
					<?php echo greenio_logo_text_markup( $footer_logo_text ); ?>
				<?php endif; ?>
			</a>
			<?php if ( $footer_about ) : ?>
				<p><?php echo esc_html( $footer_about ); ?></p>
			<?php endif; ?>
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
				<?php if ( $footer_email ) : ?>
					<a href="mailto:<?php echo esc_attr( antispambot( $footer_email ) ); ?>"><?php echo esc_html( $footer_email ); ?></a>
				<?php endif; ?>
				<?php if ( $footer_phone ) : ?>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $footer_phone ) ); ?>"><?php echo esc_html( $footer_phone ); ?></a>
				<?php endif; ?>
				<?php if ( $footer_address ) : ?>
					<a href="#"><?php echo esc_html( $footer_address ); ?></a>
				<?php endif; ?>
			</div>

		<?php endif; ?>

	</div>

	<div class="container footer-bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'greenio' ); ?></p>
		<?php if ( $footer_copyright ) : ?>
			<p><?php echo esc_html( str_replace( '{year}', gmdate( 'Y' ), $footer_copyright ) ); ?></p>
		<?php endif; ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
