<?php


add_action( 'pre_get_posts', function( $query ) {
    // Target only the front-end search results page
    if ( $query->is_search() && ! is_admin() && $query->is_main_query() ) {
        // Exclude password protected posts and pages
        $query->set( 'has_password', false );
    }
});
