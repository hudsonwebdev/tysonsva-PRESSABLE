<?php
/**
 * Template Name: Legal Document
 *
 * Centered UIkit container for Complianz and other legal pages
 * that do not use ACF blocks (which wrap themselves).
 *
 * @package tca
 */

get_header();
?>

<main id="primary" class="site-main legal-document-page">
	<section>
		<div class="uk-container">
			<div class="content-wrap top-pad-medium botton-pad-medium">
				<div class="section-content">
					<?php
					while ( have_posts() ) :
						the_post();
						the_content();
					endwhile;
					?>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
