<?php 

/**
 * Responsive Image Helper Function
 *
 * @param string $image_id the id of the image (from ACF or similar)
 * @param string $image_size the size of the thumbnail image or custom image size
 * @param string $max_width the max width this image will be shown to build the sizes attribute 
 */



function awesome_acf_responsive_image($image_id, $image_size, $max_width,$alt = "") {

    // Check if the image ID is not blank
    if ($image_id != '') {

        // Get the image source URL for the specified size
        $image_src = wp_get_attachment_image_url($image_id, $image_size);

        // Get the image srcset for responsive images
        $image_srcset = wp_get_attachment_image_srcset($image_id, $image_size);

        // Get the alt text for the image

        if( $alt > ""){

            $alt_text = $alt;

        }else{

            $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);

        }
        

        // Set a fallback alt text if none is found
        if (empty($alt_text)) {
            $alt_text = 'Image'; // You can customize this fallback value as needed
        }

        // Generate the markup for the responsive image with alt text
        echo ' loading="lazy" src="' . esc_url($image_src) . '" srcset="' . esc_attr($image_srcset) . '" sizes="(max-width: ' . esc_attr($max_width) . ') 100vw, ' . esc_attr($max_width) . '" alt="' . esc_attr($alt_text) . '" ';
    }
}

