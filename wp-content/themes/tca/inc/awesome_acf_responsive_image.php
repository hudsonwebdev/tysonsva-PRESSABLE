<?php 

/**
 * Responsive Image Helper Function
 *
 * @param string $image_id the id of the image (from ACF or similar)
 * @param string $image_size the size of the thumbnail image or custom image size
 * @param string $max_width the max width this image will be shown to build the sizes attribute
 * @param string $alt alt text (optional)
 * @param bool   $lcp when true, use loading="eager" and fetchpriority="high" for LCP (e.g. hero image)
 */

function awesome_acf_responsive_image( $image_id, $image_size, $max_width, $alt = '', $lcp = false ) {

    if ( empty( $image_id ) ) {
        return;
    }

    $image_src    = wp_get_attachment_image_url( $image_id, $image_size );
    $image_srcset = wp_get_attachment_image_srcset( $image_id, $image_size );

    if ( (string) $alt !== '' ) {
        $alt_text = $alt;
    } else {
        $alt_text = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
    }
    if ( empty( $alt_text ) ) {
        $alt_text = 'Image';
    }

    $loading = $lcp ? 'eager' : 'lazy';
    $attrs   = ' src="' . esc_url( $image_src ) . '" srcset="' . esc_attr( $image_srcset ) . '" sizes="(max-width: ' . esc_attr( $max_width ) . ') 100vw, ' . esc_attr( $max_width ) . '" alt="' . esc_attr( $alt_text ) . '" loading="' . esc_attr( $loading ) . '"';
    if ( $lcp ) {
        $attrs .= ' fetchpriority="high"';
    }



    echo $attrs;
}

