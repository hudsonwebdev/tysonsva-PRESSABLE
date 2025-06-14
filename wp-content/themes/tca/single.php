<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package tca
 */

 if(get_field('source_url')){

	$url = get_field('source_url');
	header("Location:" . $url . "?" . $_SERVER['QUERY_STRING'],true,301);
	exit;
 }

get_header();
?>

	<main id="primary" class="site-main">
		
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );
			
		endwhile; // End of the loop.
		?>


		
	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();
