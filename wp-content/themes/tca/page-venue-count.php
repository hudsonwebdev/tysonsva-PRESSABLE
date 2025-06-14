<?php
$redirect_this_page_to = get_field('redirect_this_page_to');

// Check if it is an array and contains the 'url' key
if (is_array($redirect_this_page_to) && !empty($redirect_this_page_to['url']) && filter_var($redirect_this_page_to['url'], FILTER_VALIDATE_URL)) {
    $newURL = $redirect_this_page_to['url'];
    header('Location: ' . $newURL);
    exit();  // Always call exit after the header redirection
}
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

if(current_user_can('administrator')){


get_header();

// Get all Venue posts
$venue_args = array(
    'post_type' => 'venue',
    'posts_per_page' => -1,
    'post_status' => 'publish',
);

$venues = new WP_Query($venue_args);

if ($venues->have_posts()) :
    while ($venues->have_posts()) : $venues->the_post();
        $venue_id = get_the_ID();
        ?>

        <h2><?php the_title(); ?></h2>

        <?php if(get_field('address')){

            echo get_field('address');

        }; ?><br>

        <?php if(get_field('venue_website')){

            echo get_field('venue_website');

        }; ?>

        <?php
        // Query all tysons_event posts related to this Venue
        $event_args = array(
            'post_type' => 'tysons-event',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'location', // ACF relationship field key
                    'value' =>  $venue_id , // ACF stores IDs in serialized array, so use quotes
                    'compare' => '='
                )
            ),
            'orderby' => 'meta_value', // optional: change to event date if you have one
            'order' => 'DESC' // or ASC
        );

        $events = new WP_Query($event_args);

        if ($events->have_posts()) :
            echo '<ol>';
            while ($events->have_posts()) : $events->the_post(); ?>
                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
            <?php endwhile;
            echo '</ol>';
        else :
            echo '<p>No events found for this venue.</p>';
        endif;

        wp_reset_postdata();

    endwhile;
else :
    echo '<p>No venues found.</p>';
endif;

wp_reset_postdata();

get_footer();

}else{
    wp_redirect(site_url());
}