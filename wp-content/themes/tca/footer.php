<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package tca
 */

?>


<footer id="colophon" class="site-footer">

	<div class="footer-inner">
	<div class="uk-container footer-wrap">

	

		<div class="rect1">
			<svg xmlns="http://www.w3.org/2000/svg" width="588" height="1058" viewBox="0 0 588 1058" fill="none">
			<path d="M-138 617.805V1232L588 506.196V-108L-138 617.805Z" fill="#385DFF" fill-opacity="0.75"/>
			</svg>
		</div>



		<div class="footer-content">

			<div class="footer-form-wrap">
				<h2 class="form-title">Get the Scoop</h2>
				<p>Sign Up for the Tysons Newsletter:</p>
				<?php gravity_form( 1, false, false); ?>
			</div>
			<hr class="mobile-line">
			<a href="<?php echo site_url(); ?>" class="mobile-logo"><?php drawSVG('tcalogo'); ?></a>
			
			<div class="footer-nav">
				<?php 
				wp_nav_menu( 
						array( 
							'theme_location' => 'footer-menu'
						) 
					); 
				?>
				<?php drawSocial(); ?>

			</div>
			<div class="footer-bottom">
				<div class="site-branding desktop-logo">
				<a href="<?php echo site_url(); ?>"><?php drawSVG('tcalogo'); ?></a>
				</div><!-- .site-branding -->

				<div class="tca-address">1961 Chain Bridge Road, Suite C205B<br>Tysons, VA 22102 <br>
				<a href="tel:+17037380064" style="color:#ffffff">1 703.738.0064</a>
				</div>
			</div>

			<div class="copyright">
				&copy; <?php echo date('Y'); ?> All Rights Reserved | Tysons Community Alliance
			</div>

		
		</div>


	</div>


	
</div>

	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
