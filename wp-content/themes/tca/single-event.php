<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package tca
 */



get_header();
?>

	<main id="primary" class="site-main event-page">
		
		<?php
		while ( have_posts() ) :
			the_post();

			the_content();
			
		endwhile; // End of the loop.
		?>


		
	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();
