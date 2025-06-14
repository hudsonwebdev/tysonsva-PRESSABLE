<?php

function openSection(
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
    ){ 





if($container_type=="div"){ ?>

        <div <?php echo esc_attr( $anchor ); ?> class="<?php echo esc_attr( $class_name ); ?>" <?php if(!$disable_animation){ echo ' uk-scrollspy="target: .content-wrap; cls: uk-animation-slide-bottom-medium; delay: 200;repeat:true;"';} ?>>

        <?php }else{ ?>

        <section <?php echo esc_attr( $anchor ); ?> class="<?php echo esc_attr( $class_name ); ?>"   <?php if(!$disable_animation){ echo ' uk-scrollspy="target: .content-wrap; cls: uk-animation-slide-bottom-medium; delay: 200;repeat:true;"';} ?>>

    <?php } ?>

    
    <?php if( $wrap_size == "full-page-wrap"){ ?>

      
        <div class="content-wrap top-pad-<?php echo $vertical_pad_top; ?> botton-pad-<?php echo $vertical_pad_bottom; ?> " style="position:relative;background-color:<?php echo $background_color; ?>">
        <div class="uk-container <?php echo $container_size; ?>">

    <?php }else{ ?>

        <div class="uk-container <?php echo $container_size; ?>">
      
        <div class="content-wrap  top-pad-<?php echo $vertical_pad_top; ?> botton-pad-<?php echo $vertical_pad_bottom; ?>" style="position:relative;%;background-color:<?php echo $background_color; ?>">
        <?php } ?>


    <div class="section-content">
            
            
            
<?php }


function closeSection($wrap_size,$container_size,$container_type,$graphic){ ?>
                </div>
            
        </div>
    </div>

    <?php if($graphic>0){ ?>

        <div class="uk-container deco-wrap <?php echo $container_size; ?>" >
            <div class="section-decoration"><?php drawSVG('section-graphic-' . $graphic); ?></div>
        </div>
    
    <?php } ?>
    
    <?php if($container_type=="div"){ ?>

        </div>

    <?php }else{ ?>

        </section>

    <?php } ?>
    
<?php }


function drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style){ ?>
<?php 


if($section_title>"" || $section_button>"") { ?>
    <div class="grid-header <?php if($show_underline){ echo "showunderline"; }?> ">
        <div class="left-header"><h2 class="section-title title-size-<?php echo $section_title_size; ?> uk-text-<?php echo $title_alignment; ?>"><?php echo $section_title; ?></h2></div>
        
        <?php if( $section_button > ''){ ?>
            <div class="right-header">
                <a class="tca-button <?php echo strtolower($section_button_style); ?>" href="<?php echo $section_button['url']; ?>" target="<?php echo $section_button['target']; ?>"><?php echo $section_button['title']; ?></a>
                </div>
        <?php } ?>
       
        
    </div>
    <?php 
         if($section_intro){
            echo $section_intro;
        } 
       ?>

   
<?php } ?>
  
<?php }