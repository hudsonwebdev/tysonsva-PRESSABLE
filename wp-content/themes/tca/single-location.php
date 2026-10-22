<?php
/**
 * Single Events Manager location.
 *
 * Avoids the news single template (large header graphic).
 *
 * @package tca
 */

get_header();
?>

<main id="primary" class="site-main location-page">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>

<?php
get_footer();
