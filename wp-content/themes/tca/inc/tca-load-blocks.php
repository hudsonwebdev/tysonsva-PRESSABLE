<?php

namespace TCA\Blocks;

add_filter( 'allowed_block_types_all', __NAMESPACE__ . '\\tca_allowed_block_types', 25, 2 );
 
function tca_allowed_block_types( $allowed_blocks, $editor_context ) {

    $block_directories = glob(get_template_directory() . '/blocks/*/block.json');

 
	$allowed =  array(
		'core/image',
		'core/paragraph',
		'core/heading',
		'core/list',
		'core/list-item',
        'tca/testimonial',
		'core/cover',
		'core/buttons',
		'core/button',
		'core/spacer',
		'core/separator',
		'core/shortcode',
		'core/html',
		'core/columns',
	);

    $allowed_block_types = array_merge( $allowed, get_custom_blocks($block_directories) );
 
     return $allowed_block_types;
 
}


// Get block names from block.json files
function get_custom_blocks($block_directories) {
	$custom_blocks = array();
	foreach ($block_directories as $block_directory) {
		$block_data = json_decode(file_get_contents($block_directory), true);
		if(isset($block_data['name'])) {
			$custom_blocks[] = '' . $block_data['name'];
		}
	}
	return $custom_blocks;
}



/**
 * Load Blocks
 */
function load_blocks() {
	$blocks = get_blocks();
	foreach( $blocks as $block ) {
		if ( file_exists( get_template_directory() . '/blocks/' . $block . '/block.json' ) ) {
			register_block_type( get_template_directory() . '/blocks/' . $block . '/block.json',array('priority' => 10) );
			if ( file_exists( get_template_directory() . '/blocks/' . $block . '/style.css' ) ) {
				wp_register_style( 'block-' . $block, get_template_directory_uri() . '/blocks/' . $block . '/style.css', array(), filemtime( get_template_directory() . '/blocks/' . $block . '/style.css' ) );
			}
			if ( file_exists( get_template_directory() . '/blocks/' . $block . '/init.php' ) ) {
				include_once get_template_directory() . '/blocks/' . $block . '/init.php';
			}
		}
	}
}

add_action( 'init', __NAMESPACE__ . '\\load_blocks', 5 );

/**
 * Load ACF field groups for blocks
 */
function load_acf_field_group( $paths ) {
	$blocks = get_blocks();
	foreach( $blocks as $block ) {
		$paths[] = get_template_directory() . '/blocks/' . $block;
	}
	return $paths;
}
add_filter( 'acf/settings/load_json', __NAMESPACE__ . '\\load_acf_field_group' );

/**
 * Get Blocks
 */
function get_blocks() {
	$blocks = scandir( get_template_directory() . '/blocks/' );
	$blocks = array_values( array_diff( $blocks, array( '..', '.', '.DS_Store', '_base-block' ) ) );
	return $blocks;
}




function mytheme_register_block_category( $categories ) {
    // Insert your custom category at the beginning of the categories array
    $categories = array_merge(
        array(
            array(
                "slug"  => "tca-custom", // Unique slug for the category
                "title" => "TCA Custom Blocks", // Title of the category
              
            ),
        ),
        $categories
    );

    return $categories;
}

add_filter( 'block_categories_all',  __NAMESPACE__ . '\\mytheme_register_block_category', 10, 1 );
