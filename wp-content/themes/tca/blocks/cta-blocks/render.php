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
 





drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); ?>


        <div class="cta-blocks" uk-scrollspy="target: .cta-block; cls: uk-animation-slide-bottom-medium; delay: 200;repeat:true;">
            <?php 

       
                if( have_rows('cta_blocks') ):

                    
                    while( have_rows('cta_blocks') ) : the_row(); ?>
                     <?php 
                            $big_text = get_sub_field('big_text');
                            $small_text = get_sub_field('small_text');
                            $button_link = get_sub_field('button_link');
                            $cta_image = get_sub_field('cta_image');
                    ?>

                    <div class="cta-block">
                        
                        <div class="inner">

                        <div class="chev-up"><?php drawSVG('chevron-up-card'); ?></div>
                        <?php if(isset($cta_image['id'])){ ?>
                            <img <?php echo awesome_acf_responsive_image($cta_image['id'],'medium','768px'); ?> class="background-image"/>
                        <?php } ?>
                        <div class="blue-curtain"></div>
                        <div class="tint"></div>
                            <div class="text-area">
                                <h3 class="big-text"><?php echo $big_text; ?></h3>
                                <p class="small-text"><?php echo $small_text; ?></p>
                                <?php if(isset($button_link['url'])){  ?>
                               
                                    <a href="<?php echo $button_link['url']; ?>" class="tca-button white" target="<?php echo $button_link['target']; ?>"><?php echo $button_link['title']; ?></a>
                                
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                
                   <?php endwhile;



                endif;
        
                
            ?>
                
            
        </div>
   
   
            
                        
<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);

