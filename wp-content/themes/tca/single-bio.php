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
<?php draw_no_image_banner("Our Team"); ?>

	
        <div class="uk-container">
            <?php
            while ( have_posts() ) :
                the_post(); ?>

                <div class="bio-frame">

                    <div class="bio-pic">
                        <?php the_post_thumbnail();?>
                    </div>

                    <div class="bio-text">
                        <h1><?php echo get_the_title(); ?></h1>
                        <h3><?php echo get_field('job_title'); ?></h3>
                        <?php the_content();?>
                    </div>
                </div>
                
            <?php endwhile;   ?>
		</div>
	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();
