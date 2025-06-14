<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package tca
 */

get_header();
?>

	<main id="primary" class="site-main">
	<?php draw_no_image_banner("404"); ?>
		<section class="error-404 not-found">

			<div class="uk-container">
	

			<div class="page-content">
				<h2><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'tca' ); ?></h2>

					<?php
					get_search_form();

					
					?>

				

					
				</div>
			</div><!-- .page-content -->
		</section><!-- .error-404 -->

	</main><!-- #main -->

<?php
get_footer();
