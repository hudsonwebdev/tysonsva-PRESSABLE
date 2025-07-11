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
 
$banner_image = get_field('banner_image');
$large_title = get_field('large_title');
$additional_text = get_field('additional_text');
$above_title_spacer = get_field('above_title_spacer')?get_field('above_title_spacer'):0;


drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); ?>
<?php if(get_field('gradient_tint')){ ?>
<style>
    .image-tint{
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:100%;
        z-index:1;
        <?php echo get_field('gradient_tint'); ?>
    }

</style>
<?php } ?>
<div class="image-banner">
<div class="chevron"><svg xmlns="http://www.w3.org/2000/svg" width="432" height="432" viewBox="0 0 432 432" fill="none">
  <path d="M129.582 0L0 129.582H302.418V432L432 302.43V0H129.582Z" fill="#385DFF"/>
</svg></div>
        <?php if($banner_image){ ?>

          
                <div class="image-wrap">
                    <img <?php awesome_acf_responsive_image($banner_image['id'],'full','2000px',$banner_image['alt']); ?>  />
                    <div class="image-tint">test</div>
                </div>
                
       
        <?php } ?>


        <?php if($additional_text || $large_title){ ?>

            <div class="text-overlay">
                <div class="inner">
                    <div style="height:<?php echo $above_title_spacer; ?>px"></div>
                    <h1 class="banner-title"><?php echo $large_title; ?></h1>

                    <?php if(get_field('add_additional_text')){ ?>
                    <?php echo $additional_text; ?>
                    <?php } ?>
                    
                </div>
            </div>
            
        <?php } ?>





                        
<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);

