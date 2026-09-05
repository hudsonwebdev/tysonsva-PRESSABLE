<?php

use FileBird\I18n;

defined( 'ABSPATH' ) || exit;

if (!function_exists('filebird_gallery_block_assets')) {
    function filebird_gallery_block_assets() {
        wp_enqueue_style( 'filebird_gallery-fb-block-css' );
    
        wp_register_style( 'fbv-photoswipe', NJFB_PLUGIN_URL . 'assets/css/photoswipe/photoswipe.css', array(), NJFB_VERSION );
        wp_register_style( 'fbv-photoswipe-default-skin', NJFB_PLUGIN_URL . 'assets/css/photoswipe/default-skin.css', array(), NJFB_VERSION );
    
        wp_register_script( 'fbv-photoswipe', NJFB_PLUGIN_URL . 'assets/js/photoswipe/photoswipe.min.js', array(), NJFB_VERSION, true );
        wp_register_script( 'fbv-photoswipe-ui-default', NJFB_PLUGIN_URL . 'assets/js/photoswipe/photoswipe-ui-default.min.js', array(), NJFB_VERSION, true );
        wp_register_script( 'filebird-gallery', NJFB_PLUGIN_URL . 'assets/js/photoswipe/fbv-photoswipe.js', array(), NJFB_VERSION, true );
    
        register_block_type( __DIR__ . '/build' );
    }
}

if (!function_exists('filebird_gutenberg_get_images')) {
    function filebird_gutenberg_get_images() {
        register_rest_route(
            NJFB_REST_URL,
            'gutenberg-get-images',
            array(
                'methods'             => 'POST',
                'callback'            => 'filebird_gutenberg_render_callback',
                'permission_callback' => function(){
                    return current_user_can( 'upload_files' );
                }
            )
        );
    }
}

if (!function_exists('filebird_gutenberg_render_callback')) {
    function filebird_gutenberg_render_callback( $request ) {
        $attributes = $request->get_params();

        ob_start();
        include NJFB_PLUGIN_PATH . '/blocks/filebird-gallery/build/render.php';
        $html = ob_get_clean();
        wp_send_json(
            array(
                'html' => $html,
            )
        );
    }
}

if (!function_exists('filebird_gutenberg_get_image_list')) {
    function filebird_gutenberg_get_image_list() {
        register_rest_route(
            NJFB_REST_URL,
            'gutenberg-get-image-list',
            array(
                'methods'             => 'POST',
                'callback'            => 'filebird_gutenberg_image_list_callback',
                'permission_callback' => function(){
                    return current_user_can( 'upload_files' );
                }
            )
        );
    }
}

if (!function_exists('filebird_gutenberg_image_list_callback')) {
    function filebird_gutenberg_image_list_callback( $request ) {
        global $wpdb;

        $folders = (array) $request->get_param( 'selectedFolder' );
        $ids     = array_filter( array_map( 'intval', $folders ) );

        if ( empty( $ids ) ) {
            wp_send_json( array( 'images' => array() ) );
        }

        $where_arr   = array( '1 = 1' );
        $where_arr[] = '`folder_id` IN (' . implode( ',', $ids ) . ')';
        $attachment_ids = $wpdb->get_col( "SELECT `attachment_id` FROM {$wpdb->prefix}fbv_attachment_folder" . ' WHERE ' . implode( ' AND ', apply_filters( 'fbv_in_not_in_where_query', $where_arr, $ids ) ) );

        if ( empty( $attachment_ids ) ) {
            wp_send_json( array( 'images' => array() ) );
        }

        $query = new \WP_Query(
            array(
                'post_type'      => 'attachment',
                'posts_per_page' => -1,
                'post__in'       => $attachment_ids,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post_status'    => 'inherit',
            )
        );

        $images = array();
        foreach ( $query->get_posts() as $post ) {
            $thumb = wp_get_attachment_image_src( $post->ID, 'thumbnail' );
            $images[] = array(
                'id'    => $post->ID,
                'thumb' => $thumb ? $thumb[0] : wp_get_attachment_url( $post->ID ),
                'title' => $post->post_title,
            );
        }

        wp_send_json( array( 'images' => $images ) );
    }
}

add_action( 'init', 'filebird_gallery_block_assets', PHP_INT_MAX );
add_action( 'rest_api_init', 'filebird_gutenberg_get_images' );
add_action( 'rest_api_init', 'filebird_gutenberg_get_image_list' );