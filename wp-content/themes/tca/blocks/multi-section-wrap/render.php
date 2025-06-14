<?php
// Support custom "anchor" values.
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
    $anchor = 'id="' . esc_attr( $block['anchor'] ) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'tca-block';
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
    $class_name .= ' align' . $block['align'];
}

$background_color = get_field('background_color')?get_field('background_color'):'transparent';
$background_image = get_field('background_image');
$parallax_speed = get_field('parallax_speed')?get_field('parallax_speed'):200;


$page_slant = get_field('page_slant');

if($page_slant>0){

    $class_name .= ' page-slant-' . $page_slant;

}

if ( ! empty( $block['data'] ) && ! empty( $block['data']['image'] ) ) {
       echo $block['data']['image']; 
       return;
}


?>
<div class="color-backer <?php echo $background_color; ?>-bg">
<div <?php echo esc_attr( $anchor ); ?> class="<?php echo esc_attr( $class_name ); ?>" <?php if($page_slant>0){ echo 'uk-parallax="bgy: ' . $parallax_speed . '"'; } ?> >

<InnerBlocks />
</div>
</div>





         
        


