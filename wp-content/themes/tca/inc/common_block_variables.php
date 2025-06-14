<?php

if ( is_admin() && ! empty( $block['data'] ) && ! empty( $block['data']['image'] ) ) {

       echo $block['data']['image']; 

       return;
}

if(!empty($container_settings)){
    $container_size = isset($container_settings['container_size'])?$container_settings['container_size']:'standard';
    $background_color = (isset($container_settings['background_color']) && $container_settings['background_color']>'')?$container_settings['background_color']:'transparent';
    $text_color = isset($container_settings['text_color'])?$container_settings['text_color']:'transparent';
    $background_image = isset($container_settings['background_image'])?$container_settings['background_image']:array('url'=>'');
    $wrap_size = isset($container_settings['wrap_size'])?$container_settings['wrap_size']:'full-page-section-wrap';
    $overlapping_graphic = isset($container_settings['overlapping_graphic'])?$container_settings['overlapping_graphic']:0;
    $container_type = $container_settings['container_type']? $container_settings['container_type']:'section';
    $disable_animation = $container_settings['disable_animation']? $container_settings['disable_animation']:false;
    $vertical_pad_top = $container_settings['vertical_pad_top']? $container_settings['vertical_pad_top']:'vpad-top-medium';
    $vertical_pad_bottom = $container_settings['vertical_pad_bottom']? $container_settings['vertical_pad_bottom']:'vpad-bottom-medium';

    
}else{
    $container_size = "standard";
    $background_color = "transparent";
    $text_color = "#000000";
    $background_image = array('url'=>'');
    $wrap_size = "container-wrap";
    $overlapping_graphic = 0;
    $container_type = "section";
    $disable_animation = false;
    $vertical_pad_top = "vpad-top-medium";
    $vertical_pad_bottom = "vpad-bottom-medium";
}






if(!empty($section_header)){
   
    $section_title = isset($section_header['section_title'])?$section_header['section_title']:'';
    $title_alignment = isset($section_header['title_alignment'])?$section_header['title_alignment']:'';
    $show_underline = isset($section_header['show_underline'])?$section_header['show_underline']:'';
    $section_intro = isset($section_header['section_intro'])?$section_header['section_intro']:'';
    $section_button = isset($section_header['section_button'])?$section_header['section_button']:'';
    $section_button_style = isset($section_header['section_button_style'])?$section_header['section_button_style']:'white';
    $section_title_size = isset($section_header['section_title_size'])?$section_header['section_title_size']:'medium';
    
    
}else{
    $section_title = '';
    $title_alignment = 'left';
    $show_underline = true;
    $section_intro ='';
    $section_button = false;
    $section_button_style = 'white';
    $section_title_size = 'medium';
}




// Support custom "anchor" values.
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
    $anchor = 'id=' . esc_attr( $block['anchor'] ) . ' ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'tca-block';
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
    $class_name .= ' align' . $block['align'];
}

