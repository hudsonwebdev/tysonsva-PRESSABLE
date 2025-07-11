<?php


get_header();


$featured_args = array(
    'post_type'      => 'event',  // Custom post type
    'posts_per_page' => -1,       // Get all posts
    'meta_query'     => array(
        array(
            'key'     => 'featured',      // ACF field name
            'value'   => '1',             // '1' means the field is checked (true)
            'compare' => '=',             // Exact match
        ),
    ),
    'meta_key'       => '_event_start_date', // Meta key to sort by
    'orderby'        => 'meta_value',        // Sort by custom field value
    'order'          => 'ASC',               // Change to 'DESC' if needed
);


// The query to fetch the events
$featured_query = new WP_Query($featured_args);
$number_of_events = $featured_query->found_posts;
?>

	<main id="primary" class="site-main">

	<div class="event-list">
        <div class="uk-container">

			
    <div class="featured-slides">

    <?php

         $eventCount = 0;

        if ($featured_query->have_posts()) :

            while ($featured_query->have_posts()) : $featured_query->the_post();

            $eid = get_the_ID();

            display_event_featured($eid,$number_of_events, $eventCount);

            $eventCount++;

            endwhile; ?>

        <?php endif; ?>  
        
    </div>
        
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
