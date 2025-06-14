<?php

$container_settings = get_field('container_settings');
$section_header = get_field('section_header');
include __DIR__ .'/../../inc/common_block_variables.php';
$feature_first_post = get_field('feature_first_post')?get_field('feature_first_post'):false;

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
$image_width = get_field('image_width')?get_field('image_width'):'50';
$text_side = get_field('text_side');
$image_position =  get_field('image_position')? get_field('image_position'):'left';
$image_is_logo =  get_field('image_is_logo')? get_field('image_is_logo'):false;
$cta_button =  get_field('cta_button')? get_field('cta_button'):'';
 


drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); ?>

<div class="text-image-combo image-size-<?php echo $image_width; ?>">
    <div class="image-text-wrap image-position-<?php echo $image_position; ?>">

        <?php if($image_side){ ?>

            <div class="image-side ">
                <div class="image-wrap <?php if($image_is_logo){ echo 'is-logo'; } ?>">
                    <img <?php awesome_acf_responsive_image($image_side['id'],'large','1024px',$image_side['alt']); ?>  />
                </div>
                <?php if( $image_side['caption'] >''){ ?>
                    <div class="caption"><?php echo $image_side['caption']; ?></div>
                <?php } ?>
            </div>
        <?php } ?>


        <?php if($text_side){ ?>

            <div class="text-side">
                <div class="inner">
                    <?php echo $text_side; ?>
                    <?php if($cta_button>''){ ?>
                      
                    <a href="<?php echo $cta_button['url']; ?>" target="<?php echo $cta_button['target']; ?>" class="tca-button blue"><?php echo $cta_button['title']; ?></a>
                   <?php } ?>
                    
                </div>
            </div>
            
        <?php } ?>

    </div>
</div>
        


                        
<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);

