<?php
$redirect_this_page_to = get_field('redirect_this_page_to');

// Check if it is an array and contains the 'url' key
if (is_array($redirect_this_page_to) && !empty($redirect_this_page_to['url']) && filter_var($redirect_this_page_to['url'], FILTER_VALIDATE_URL)) {
    $newURL = $redirect_this_page_to['url'];
    header('Location: ' . $newURL);
    exit();  // Always call exit after the header redirection
}
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package tca
 */



get_header();

?>

	<main id="primary" class="site-main">
	
			<?php
			while ( have_posts() ) :
				the_post();
				
			   
				the_content();

			endwhile; // End of the loop.
			?>
		
	</main><!-- #main -->

<?php

get_footer();
