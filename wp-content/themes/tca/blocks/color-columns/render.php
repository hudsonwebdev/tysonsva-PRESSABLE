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

<div class="color-columns">
<div class="uk-child-width-expand@m" uk-grid>
    <?php
        if( have_rows('columns') ):

            while( have_rows('columns') ) : the_row();

                $title = get_sub_field('title')?get_sub_field('title'):"";
                $html = get_sub_field('html')?get_sub_field('html'):"";
                $background_color = get_sub_field('background_color')?get_sub_field('background_color'):"#385DFF";

                ?>
                <div>
                    <div class="tca-column" style="background-color:<?php echo $background_color; ?>">
                    <?php if($title>""){  ?>
                        <h2 class="column-title"><?php echo $title; ?></h2>
                    <?php  } ?>
                    <?php
                    if($html){
                        echo $html;
                    } ?>

                    </div>
                </div>
            <?php
            endwhile;

        endif;
        ?>
</div>

            
</div>





                        
<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);

