<?php
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
            $future_only = true;
            echo "asdfa";
			$term_name = 'Tysons Teammates'; // This would come from user input
                $query = get_content_by_term_name($term_name);

                if ($query->have_posts()) :
                    while ($query->have_posts()) : $query->the_post();
                    $pid = get_the_ID();
                    echo $pid;
                    
                        draw_event_card($pid,1);
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo 'No content found.';
                endif;
			?>
		
	</main><!-- #main -->

<?php

get_footer();
