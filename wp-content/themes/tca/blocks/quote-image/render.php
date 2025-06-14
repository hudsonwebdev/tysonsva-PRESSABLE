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
 

 
$image_side = get_field('image_side');
$quote = get_field('quote');
$attribute = get_field('attribute');
$image_position =  get_field('image_position')? get_field('image_position'):'left';
 


drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); ?>

<div class="quote-image">
    <div class="image-text-wrap image-position-<?php echo $image_position; ?>">

        <?php if($image_side){ ?>

            <div class="image-side">
                <div class="image-wrap">
                    <div class="image-shift">
                        <img <?php awesome_acf_responsive_image($image_side['id'],'large','1024px',$image_side['alt']); ?>  />
                    </div>
                </div>
                <?php if( $image_side['caption'] >''){ ?>
                    <div class="caption"><?php echo $image_side['caption']; ?></div>
                <?php } ?>
            </div>
        <?php } ?>


        <?php if($quote){ ?>

            <div class="quote-side">
                <div class="inner">
                   
                    <div class="quote-text"><?php echo $quote; ?></div>
                    <div class="attribute">
                        <div class="line"></div>
                        <div class="at"><?php echo $attribute; ?></div>
                    
                </div>
            </div>
            
        <?php } ?>

    </div>
</div>
        


                        
<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);

