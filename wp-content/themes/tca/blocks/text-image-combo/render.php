<?php
if ( render_block_preview_if_applicable( $block ) ) return;
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
$image_link = get_field('image_link')?get_field('image_link'):'';
$new_window = get_field('new_window')?'_blank':'_self';
$image_width = get_field('image_width')?get_field('image_width'):'50';
$text_side = get_field('text_side');
$image_position =  get_field('image_position')? get_field('image_position'):'left';
$image_is_logo =  get_field('image_is_logo')? get_field('image_is_logo'):false;
$cta_button =  get_field('cta_button')? get_field('cta_button'):'';
$single_or_gallery = get_field('single_or_gallery');


drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); ?>

<div class="text-image-combo image-size-<?php echo $image_width; ?>">
    <div class="image-text-wrap image-position-<?php echo $image_position; ?>">

       

<?php if($single_or_gallery=="gallery"){

$images = get_field('image_gallery');
$size   = 'large';

if ( $images ) : ?>
<div class="image-side">
    <div class="inner">
        <div uk-slider="center: true" uk-lightbox>

            <div class="uk-position-relative uk-visible-toggle" tabindex="-1">

                <ul class="uk-slider-items uk-child-width-1-1">
                    <?php foreach ( $images as $image_id ) :
                        $image_url = wp_get_attachment_image_url( $image_id, 'large' );
                    ?>
                        <li>
                            <div class="uk-cover-container uk-height-medium">
                                <a href="<?php echo esc_url( $image_url ); ?>">
                                    <?php
                                    echo wp_get_attachment_image(
                                        $image_id,
                                        $size,
                                        false,
                                        [ 'uk-cover' => '' ]
                                    );
                                    ?>
                                </a>
                                <canvas width="1200" height="800"></canvas>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Arrows -->
                <a class="uk-position-center-left uk-position-small uk-hidden-hover"
                   href="#"
                   uk-slidenav-previous
                   uk-slider-item="previous"></a>

                <a class="uk-position-center-right uk-position-small uk-hidden-hover"
                   href="#"
                   uk-slidenav-next
                   uk-slider-item="next"></a>

            </div>

            <!-- Dots -->
            <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>

        </div>
    </div>
</div>
<?php endif; ?>

<?php }else{ ?>

 <?php if($image_side){ ?>

            <div class="image-side ">
                <?php if($image_link > '' ){ ?>
                    <a href="<?php echo $image_link; ?>" target="<?php echo $new_window; ?>">
                <?php } ?>
                <div class="image-wrap <?php if($image_is_logo){ echo 'is-logo'; } ?>">

         
                    <img <?php awesome_acf_responsive_image( $image_side['id'], 'large', '1024px', $image_side['alt'] ); ?> />
                </div>
                <?php if( $image_side['caption'] >''){ ?>
                    <div class="caption"><?php echo $image_side['caption']; ?></div>
                <?php } ?>
                <?php if($image_link > '' ){ ?>
                    </a>
                <?php } ?>
            </div>
   <?php } ?>
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

