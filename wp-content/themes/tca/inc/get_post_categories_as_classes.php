<?php

function get_post_event_categories_as_classes( $post_id ) {
    // Get the event categories for the post (from custom taxonomy 'event_categories')
    $event_categories = get_the_terms( $post_id, 'event-categories' );
    
    if ( $event_categories && ! is_wp_error( $event_categories ) ) {
        // Initialize an array to hold the formatted category names
        $category_classes = [];

        // Loop through event categories and format their names with hyphens
        foreach ( $event_categories as $event_category ) {
            // Sanitize category name (slug) and make it lowercase
            $category_classes[] =  sanitize_html_class( strtolower( $event_category->slug ) );
        }

        // Join the event categories into a single string with spaces for CSS class usage
        return $category_classes;
      
    }

    // Return an empty string if no event categories are found
    return array(); 
}
