<?php


$blockName = 'neighborhood-map';

$className = 'inview custom-acf-block custom-acf-block-' . $blockName;


// Create id attribute allowing for custom "anchor" value.
$id = 'custom-acf-block-' . $block['id'];
if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.

if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}

if( !empty($block['align']) ) {
    $className .= ' align' . $block['align'];
}



$block_title = get_field('block_title');



if( !empty( $block['data']['__is_preview'] ) ) { ?>
    
    	<img src="<?php echo get_template_directory_uri(); ?>/custom/blocks/<?php echo $blockName; ?>/preview.jpg" alt="<?php echo $blockName; ?>-preview">

<?php }




  $args = array(  
        'post_type' => 'neighborhood',
        'post_status' => 'publish',
        'posts_per_page' => -1
    );

    $loop = new WP_Query( $args ); 
        
    




?>



<section class="<?php echo esc_attr($className); ?>">

<script src='https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css' rel='stylesheet' />

<div class="neighborhood-map-wrap">



	<div id="neighborhood-map" class="neighborhood-map" style="width:100%;
  height:500px;"></div>

</div>




</section>
