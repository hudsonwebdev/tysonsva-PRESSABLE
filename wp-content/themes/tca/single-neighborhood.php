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

	<main id="primary" class="site-main">
	<div class="uk-container">
	
	<p id="breadcrumbs"><a href="/discover-tysons/neighborhood-guide">Neighborhood Guide</a> / <?php the_title(); ?></p>


</div>
		
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
