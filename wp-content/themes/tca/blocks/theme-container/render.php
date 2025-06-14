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
?>



  

        <InnerBlocks />


         

<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);



         
        


