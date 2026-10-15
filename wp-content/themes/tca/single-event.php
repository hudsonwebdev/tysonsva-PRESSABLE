<?php
/**
 * Single Events Manager event.
 *
 * Avoids the news single template (large header graphic).
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
	endwhile;
	?>
</main>

<?php
get_footer();
