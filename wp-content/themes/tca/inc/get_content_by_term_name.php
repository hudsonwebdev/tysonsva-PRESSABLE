<?php

function get_content_by_term_ids($term_ids = [], $post_types = [], $future_only = true, $total_posts = 6) {
    if (empty($term_ids) || !is_array($term_ids)) {
        return new WP_Query(); // No terms selected
    }


    // Validate post types or use default
    if (empty($post_types) || !is_array($post_types)) {
        $post_types = ['post', 'event','resource'];
    }

    $taxonomies = ['category', 'event-categories'];
    $term_matches = [];

    foreach ($taxonomies as $taxonomy) {
        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'include'    => $term_ids,
            
        ]);

        if (!is_wp_error($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                $term_matches[] = [
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => [$term->term_id],
                ];
            }
        }
    }

    if (empty($term_matches)) {
        return new WP_Query(); // No matching terms
    }

    $tax_query = array_merge(['relation' => 'OR'], $term_matches);
    $meta_query = [];
   
    if ($future_only) {
       
        $current_date = current_time('Y-m-d');
        $meta_query[] = [
            'relation' => 'OR',
            [
                'key'     => '_event_start_date',
                'compare' => 'NOT EXISTS',
            ],
            [
                'key'     => '_event_start_date',
                'value'   => $current_date,
                'compare' => '>=',
                'type'    => 'DATE',
            ]
        ];
    }

    $args = [
        'post_type'   => $post_types,
        'post_status' => 'publish',
        'tax_query'   => $tax_query,
        'posts_per_page' => $total_posts,
        
    ];

    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }

    // Always set ordering by event start date, even when future_only is false
    // This ensures dates stay in order regardless of whether meta_query is defined
    $args['orderby'] = 'meta_value';
    $args['meta_key'] = '_event_start_date';
    $args['order'] = 'ASC'; // ASC for chronological order (earliest first)
    $args['meta_type'] = 'DATE';

    return new WP_Query($args);
}
