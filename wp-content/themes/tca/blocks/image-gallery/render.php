<?php

$container_settings = get_field('container_settings');
$section_header = get_field('section_header');
include __DIR__ .'/../../inc/common_block_variables.php';


$column_count_desktop = get_field('column_count_desktop')?get_field('column_count_desktop'):3;

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
 

    $containerClass = " column-count-" . $column_count_desktop;


drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); ?>

<div class="image-gallery">

<?php 
$images = get_field('image_gallery');
if( $images ): ?>
    <div class="image-wrapper <?php echo $containerClass; ?>">
        <?php foreach( $images as $image ): ?>
            <div  class="gallery-image">
                <span>
                    
                     <img <?php awesome_acf_responsive_image($image['id'],'full','600px',$image['alt']); ?>  />
        </span>
            </div>  
        <?php endforeach; ?>
    </div>
<?php endif; ?>
            
</div>





                        
<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);

