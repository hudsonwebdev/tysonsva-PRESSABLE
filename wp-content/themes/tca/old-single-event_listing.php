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

			?>
		  <div class="backing-svg"><svg xmlns="http://www.w3.org/2000/svg" width="699" height="1126" viewBox="0 0 699 1126" fill="none">
            <path d="M-23 516.388V1126L699 405.612V-204L-23 516.388Z" fill="#9ADCF7" fill-opacity="0.35"/>
            </svg>
        </div>

        <div class="uk-container">
            
        <a href="/events" class="back-to-events"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18" fill="none">
                        <path d="M10 16L3 8.99577L10 2"  stroke-width="3" stroke-miterlimit="10"/>
                        </svg> Back to Events</a>

                        
            <div class="event-header">

            



                <div class="event-info">
                    
                    <div class="inner">
		
					 <div class="card-header">
                                <div class="card-type">Event</div>
                                <div class="card-date">DATE HERE</div>
                            </div>

							<div class="card-title">
                            <h1>TITLE HERE</h1>
							</div>
							VENUE INFO

							
                       
                        

                       <a href="#" class="get-tickets"  target="_blank">Get Tickets</a>
                        

                     
                            <a href="#" class="learn-more" target="_blank"> <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path d="M6.63356 4.13525H1.93701V17.0632H14.8588V12.3605" stroke="white" stroke-width="3" stroke-miterlimit="10"/>
                            <path d="M8.51318 1.93677H17.0631V10.4867" stroke="white" stroke-width="3" stroke-miterlimit="10"/>
                            <path d="M16.6509 2.35522L6.5708 12.4353" stroke="white" stroke-width="3" stroke-miterlimit="10"/>
                            </svg> <span>Learn More</span></a>
                    


                        </div>

						<div class="featured-image">
                    <div class="aspect-ratio-container">
                            <?php  the_post_thumbnail(); ?>
                    </div>
                </div>

		<?php	the_content();
			
		endwhile; // End of the loop.
		?>


		
	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();
