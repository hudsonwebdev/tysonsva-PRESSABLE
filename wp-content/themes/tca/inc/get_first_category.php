<?php

function get_first_category($post_id) {
    // Get the categories associated with the post
    $categories = get_the_category($post_id);
    
    if (!empty($categories)) {
        // Check if the first category is "Featured"
        if ($categories[0]->name === 'Featured' && isset($categories[1])) {
            // If the first category is "Featured", print the second category
            echo $categories[1]->name;
        } else {
            // Otherwise, print the first category
            echo $categories[0]->name;
        }
    } else {
        // If no categories exist, print "news"
        echo "news";
    }
}

