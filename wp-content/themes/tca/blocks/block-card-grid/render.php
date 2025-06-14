<?php

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




drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); ?>


<div class="card-grid">
   
    <?php
    if( have_rows('cards') ):

    
        while( have_rows('cards') ) : the_row();


            $image = get_sub_field('image');
            $content = get_sub_field('content');

           
            draw_content_card($image,$content,1);
    
        endwhile;

    endif;
?>
</div>


<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);

