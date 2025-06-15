<?php


$view = isset($_GET['view']) && ($_GET['view'] == 'list') ? 'list' : 'grid';
$start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
// Set up WP_Query arguments to fetch events







$args = array(
    'post_type' => 'tysons-event',
    'posts_per_page' => 6, // Number of events per page
    'paged' => max(1, get_query_var('paged')), // Pagination support
    'meta_key'       => 'end_date', // Key for the ACF field
    'orderby'        => 'meta_value', // Order by the custom field value
    'order'          => 'ASC', // Ascending order (upcoming first)
    'meta_type'      => 'DATE', // Make sure the field is treated as a date
     'meta_query'     => array(
        'relation' => 'OR',
        array(
            'key'     => 'start_date',
            'value'   => date('Y-m-d'),
            'compare' => '>=',
            'type'    => 'DATE',
        ),
        array(
            'key'     => 'end_date',
            'value'   => date('Y-m-d'),
            'compare' => '>=',
            'type'    => 'DATE',
        ),
    ),
);




// Apply category filter if selected
if (!empty($_GET['category'])) {

  $category_filter = array_map('intval', $_GET['category']);
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'event-category', // Correct taxonomy name for event categories
            'field'    => 'id',
            'terms'    => $category_filter, // This is now an array of category IDs
            'operator' => 'IN', // Match any of the selected categories
        ),
    );
}else{
    $category_filter = array();
}

// Apply date range filter if selected
if ($start_date || $end_date) {
    $meta_query = array('relation' => 'AND'); // Use AND relation to combine multiple conditions

    if ($start_date) {
        $meta_query[] = array(
            'key'     => 'start_date', // Ensure this is the correct custom field key for start date
            'value'   => $start_date,
            'compare' => '>=',
            'type'    => 'DATE',
        );
    }

    if ($end_date) {
        $meta_query[] = array(
            'key'     => 'start_date', // Assuming start_date is the key for the event start date
            'value'   => $end_date,
            'compare' => '<=',
            'type'    => 'DATE',
        );
    }

    // Add the meta query to the main query
    $args['meta_query'] = $meta_query;
}

// The query to fetch the events
$event_query = new WP_Query($args);

get_header(); 




$featured_args = array(
    'post_type'      => 'tysons-event',  // Custom post type
    'posts_per_page' => -1,              // Get all posts
    'meta_query'     => array(
        array(
            'key'     => 'featured',      // ACF field name
            'value'   => '1',             // '1' means the field is checked (true)
            'compare' => '=',             // Exact match
        ),
    ),
);

// The query to fetch the events
$featured_query = new WP_Query($featured_args);
$number_of_events = $featured_query->found_posts;


?>
<main id="primary" class="site-main"  uk-scrollspy="target: .event-list; cls: uk-animation-slide-bottom-medium; delay: 200;repeat:true;">
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
    
<?php wp_reset_postdata(); ?>

<!-- Breadcrumb Display -->
        <div class="uk-width-auto">
            <div class="current-filters">
                <?php
                // Start building the breadcrumb
                $breadcrumbs = [];

                // Categories filter
                if (!empty($selected_categories)) {
                    $category_names = [];
                    foreach ($selected_categories as $category_id) {
                        $category = get_term($category_id, 'event-category');
                        if ($category && !is_wp_error($category)) {
                            $category_names[] = $category->name;
                        }
                    }
                    if (!empty($category_names)) {
                        $breadcrumbs[] = 'Categories: ' . implode(', ', $category_names);
                    }
                }

                // Date range filter
                if ($start_date || $end_date) {
                    $date_range = [];
                    if ($start_date) {
                        $date_range[] = 'Start Date: ' . date('F j, Y', strtotime($start_date));
                    }
                    if ($end_date) {
                        $date_range[] = 'End Date: ' . date('F j, Y', strtotime($end_date));
                    }
                    if (!empty($date_range)) {
                        $breadcrumbs[] = implode(' - ', $date_range);
                    }
                }

                // Output breadcrumb if any filters are applied
                if (!empty($breadcrumbs)) {
                    echo '<div class="breadcrumb"><strong>Current Filters:</strong> ' . implode(' | ', $breadcrumbs) . '</div>';
                }
                ?>
            </div>
            </div>
    <!-- Filters Section -->
    

    <div class="event-page-header">
       <h2 class="event-list-title">All Events</h2>
       
            <a class="tca-button green" href="/submit-event" >Submit Event</a>
      
    </div>

    <hr>
        
    <div class="event-filters uk-margin-bottom">
    <div class="filter-bar">
        <div class="filter-side">
            <button  class="filter-toggle">
                <?php drawSVG('filter-toggle'); ?> Filter Events
                </button>
            <div class="post-filter">
                <form method="get" action="" class="uk-form">

                    <div class="uk-grid-small" uk-grid>
                    
                        <div class="uk-width-1-2@l">
                            <h4>Categories</h4>
                            <div class="category-filters">
                                <?php
                                // Fetch categories
                                $categories = get_terms(array(
                                    'taxonomy' => 'event-category', 
                                    'orderby'  => 'name',
                                    'order'    => 'ASC',
                                    'hide_empty' => false,
                                ));

                                // Get selected categories from the query string
                                $selected_categories = isset($_GET['category']) ? (array) $_GET['category'] : array();

                                foreach ($categories as $category) {
                                    $checked = in_array($category->term_id, $selected_categories) ? 'checked' : '';
                                    echo "<label><input type='checkbox' name='category[]' value='" . $category->term_id . "' {$checked}> {$category->name}</label><br>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Date Range Filters -->
                        <div class="uk-width-1-2@l">
                            <h4>Date Range</h4>
                            <div class="uk-grid-small" uk-grid>
                                <div class="uk-width-1-1 uk-flex">
                                    <label for="start_date">From:</label>
                                    <input type="date" name="start_date" id="start_date" class="uk-input" value="<?php echo esc_attr($start_date); ?>">
                                </div>
                                <div class="uk-width-1-1 uk-flex">
                                    <label for="end_date">to:</label>
                                    <input type="date" name="end_date" id="end_date" class="uk-input" value="<?php echo esc_attr($end_date); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Apply and Clear Buttons -->
                    <div class="uk-margin-top">
                        <button type="submit" class="uk-button uk-button-primary">Apply Filters</button>
                        <a href="/events" type="button" id="clear-filters" class="uk-button uk-button-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>
  

        <div class="toggle-side">
            <div class="view-toggle uk-flex">
                <a href="#grid" class="view-toggle-btn grid-toggle active"><?php drawSVG('grid-view'); ?>Grid View</a>
                <a href="#list" class="view-toggle-btn list-toggle"><?php drawSVG('list-view'); ?>List View</a>
            </div>
        </div>

    </div>

 
 

    <div class="event-container grid-view"   uk-scrollspy="target: .event-card; cls: uk-animation-slide-bottom-medium; delay: 200;repeat:true;">
        <?php
        if ($event_query->have_posts()) :
            $count = 0;
            while ($event_query->have_posts()) : $event_query->the_post();
                $eid = get_the_ID();

                if($count>0){
                    $columns = 1;
                }else{
                    $columns = 2;
                }
                draw_event_card($eid); 

                $count++;
              
            endwhile;

            ?>
            </div> <!-- /event-container -->

            <?php 

 


         

       

                // Pagination Links
                $pagination_args = array(
                   
                    'type' => 'list',
                    'prev_text' => __('<svg xmlns="http://www.w3.org/2000/svg" width="15" height="24" viewBox="0 0 15 24" fill="none">
                    <path d="M13 22L3 11.994L13 2" stroke="#385DFF" stroke-width="3" stroke-miterlimit="10"/>
                    </svg>'),
                                        'next_text' => __('<svg xmlns="http://www.w3.org/2000/svg" width="15" height="24" viewBox="0 0 15 24" fill="none">
                    <path d="M2 2L12 12.006L2 22" stroke="#385DFF" stroke-width="3" stroke-miterlimit="10"/>
                    </svg>'),
                );
                echo '<div class="pagination uk-text-center">';
                
               


                the_posts_pagination( $pagination_args );

                echo '</div>';

         

        endif;
   

        wp_reset_postdata();

       
        ?>

<br><br><br><br>
  
    </div> <!-- /uk-container -->
    </div>

    <div class="uk-container deco-wrap uk-position-relative">
        <div class="section-decoration"><?php  echo drawSVG('section-graphic-1'); ?></div>
    </div>
 </main>
<?php
get_footer(); // Include the WordPress footer

// Functions for displaying event cards in different views

?>


<script>





    document.getElementById('clear-filters').addEventListener('click', function() {
        var checkboxes = document.querySelectorAll('input[name="category[]"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = false;
        });

        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';

        // Submit the form after clearing the filters
        document.querySelector('form').submit();
    });






    // Function to compare start date and end date
    function validateDates() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        if (startDate && endDate) {
            // Convert both dates to Date objects for comparison
            const start = new Date(startDate);
            const end = new Date(endDate);

            // If the start date is after the end date, show an error
            if (start > end) {
                alert('The start date must be before the end date.');
                document.getElementById('end_date').value = ''; // Reset the end date
                return false; // Prevent form submission if the dates are invalid
            }
        }
        return true; // Dates are valid
    }

    // Add event listeners for date inputs
    document.getElementById('start_date').addEventListener('change', validateDates);
    document.getElementById('end_date').addEventListener('change', validateDates);



  // Wait for the document to load
  document.addEventListener('DOMContentLoaded', function () {
    // Select all slider navigation items
    const navItems = document.querySelectorAll('.uk-slideshow-nav.uk-dotnav li');

    // Loop through each navigation item and assign the slide number
    navItems.forEach((item, index) => {
      item.querySelector('a').textContent = index + 1; // Add 1 for 1-based index
    });
  });
</script>

