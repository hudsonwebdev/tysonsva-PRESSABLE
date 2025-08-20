<?php


get_header();



?>

	<main id="primary" class="site-main">

	<div class="event-list">
        <div class="uk-container">

			
    <?php
     $featured_events = get_field('featured_events');
    if($featured_events){ ?>


     <div class="featured-slides">

    <?php

         $eventCount = 0;

        

         $number_of_events = count($featured_events);

        foreach( $featured_events as $event){


            $eid = $event->ID;

            display_event_featured($eid,$number_of_events, $eventCount);

            $eventCount++;

        } ?>

        </div>

  <?php  }?>

      
        
    
<div class="event-page-header">
       <h2 class="event-list-title">All Events</h2>
       
            <a class="tca-button green" href="/submit-event" >Submit Event</a>
      
    </div>


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
