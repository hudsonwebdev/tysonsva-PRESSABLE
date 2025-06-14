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

$image_height = get_field('image_height')?get_field('image_height'):60;



drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); ?>

<div class="block-image">

        <?php if($block_image){ ?>

          
                <div class="image-wrap" style="height:<?php echo $image_height; ?>vh;">
                    <img <?php awesome_acf_responsive_image($block_image['id'],'full','2000px',$block_image['alt']); ?> />
                </div>

                <?php if( $block_image['caption'] >''){ ?>
                    <div class="caption"><?php echo $block_image['caption']; ?></div>
                <?php } ?>
       
        <?php } 

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);

