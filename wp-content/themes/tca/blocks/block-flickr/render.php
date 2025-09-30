<?php
if ( render_block_preview_if_applicable( $block ) ) return;
$container_settings = get_field('container_settings');
$section_header = get_field('section_header');
include __DIR__ .'/../../inc/common_block_variables.php';

openSection(
    $wrap_size,
    $container_size,
    $anchor,
    $class_name,
    $container_type,
    $background_color,
    $background_image,
    $text_color,
    $disable_animation,
    $vertical_pad_top,
    $vertical_pad_bottom
    );
 
$block_image = get_field('block_image');




drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style);



// Fetch the Flickr album URL from the block's attributes
$flickr_url = get_field('flickr_url');
$display_choice = get_field('flickr_display_choice')?get_field('flickr_display_choice'):'grid'; // Get the user's choice (slider/grid)

if ($flickr_url):

    // Extract the album ID from the Flickr URL
    preg_match('/flickr\.com\/photos\/[^\/]+\/albums\/(\d+)/', $flickr_url, $matches);

    if (isset($matches[1])) {
        $album_id = $matches[1];

        // Construct the Flickr API URL to fetch the album's photos
        $flickr_api_url = "https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=30dea2295c12136247e11e999a8b5f1b&photoset_id={$album_id}&format=json&nojsoncallback=1";

        // Fetch the JSON data from the Flickr API
        $response = wp_remote_get($flickr_api_url);
        $data = wp_remote_retrieve_body($response);

        if (!is_wp_error($response) && $data) {
            $photos = json_decode($data)->photoset->photo;

            // Check if there are any photos in the album
            if (!empty($photos)):
                // Limit to the first 10 images
                $photos = array_slice($photos, 0, 10);

                // Display either as slider or grid based on the user's choice
                if ($display_choice === 'slider') {
                    include 'flickr-slider.php'; // Include the slider layout
                } else {
                    include 'flickr-grid.php'; // Include the grid layout
                }

            else:
                echo '<p>No photos found in this album.</p>';
            endif;
        } else {
            echo '<p>Unable to fetch Flickr album. Please check the URL or try again later.</p>';
        }
    } else {
        echo '<p>Invalid Flickr album URL. Please make sure the URL is correct.</p>';
    }

else:
    echo '<p>Please provide a Flickr album URL in the block settings.</p>';
endif;




closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);


