<?php


get_header();



?>

	<main id="primary" class="site-main">

	<div class="event-list gradient-2-bg">
 

			
    <?php
     $featured_events = get_field('featured_events');
    if($featured_events){ ?>
  <div class="uk-container">   
    <div class="grid-header showunderline">
        <div class="left-header">
            <h2 class="section-title">Featured Events</h2>
        </div>
    </div>
</div>
    <div class="featured-slides">

        <div uk-slider="finite: true">

        <div class="uk-position-relative">
            <div class="uk-slider-container">
                <div class="uk-slider-items uk-child-width-1-3@s uk-child-width-1-4@m  uk-grid uk-grid-small uk-flex-center" >
   

                    <?php

                    $eventCount = 0;

                    

                    $number_of_events = count($featured_events);

                    foreach( $featured_events as $event){


                        $eid = $event->ID;

                        draw_event_card($eid,1);

                    

                        $eventCount++;

                    } ?>

                </div>

     

              <a class="prev-arrow" href uk-slider-item="previous" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="23" viewBox="0 0 14 23" fill="none">
                            <path d="M12.1211 21.061L2.12109 11.055L12.1211 1.06104" stroke="#385DFF" stroke-width="3" stroke-miterlimit="10"/>
                            </svg>
                        </a>
                        <a class="next-arrow" href uk-slider-item="next">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="23" viewBox="0 0 14 23" fill="none">
                            <path d="M1.06055 1.0603L11.0605 11.0663L1.06054 21.0603" stroke="#385DFF" stroke-width="3" stroke-miterlimit="10"/>
                            </svg>
                        </a>
            <ul class="uk-slider-nav uk-dotnav content-dots"></ul>
        </div>

                    
        </div>
        </div>
    </div>

  <?php  }?>

      
    <div class="uk-container">   
        <div class="grid-header showunderline">
            <div class="left-header">
                <h2 class="section-title">All Events</h2>
            </div>
            <div class="right-header">
                <a class="tca-button green" href="/submit-event" >Submit Event</a>
            </div>
        </div>
    </div>
    <div class="uk-container">   
  


			<?php
			while ( have_posts() ) :
				the_post();

				the_content();

			endwhile; // End of the loop.
			?>
    </div>	

    </div>	
	</main><!-- #main -->

<?php

get_footer();
