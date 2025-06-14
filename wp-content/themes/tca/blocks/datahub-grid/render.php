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
?>


    <?php drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); ?>
        <div class="grid-content  datahub-grid column-count-<?php echo $column_count_desktop; ?>" uk-scrollspy="target: .datahub-card; cls: uk-animation-slide-bottom-medium; delay: 200;repeat:true;">
            <div class="uk-flex uk-flex-wrap uk-flex-center">

            <?php

     
                if( have_rows('metric_box') ):

                    while( have_rows('metric_box') ) : the_row();

           
                        $label = get_sub_field('label');
                        $value = get_sub_field('value');
                        $small_text = get_sub_field('small_text');
                        $source = get_sub_field('source');
                        draw_datahub_card($label,$value,$small_text,$source);
                        
                      

                        
                    

                    endwhile;

      
                endif;

                ?>
                
            </div>
        </div>
       
<?php 
closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);