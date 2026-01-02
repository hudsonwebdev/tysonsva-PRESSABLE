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


?>
<style>
<?php if(get_field('gradient_tint')){ ?>
    .image-tint{
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:100%;
        z-index:1;
        <?php echo get_field('gradient_tint'); ?>
    }
<?php }


    if(get_field('additional_css')){ 

     
        echo get_field('additional_css'); 
 

    }

?>

</style>



   <?php
 
    
if($container_size != "full-page"){
$container_size = "uk-container " . $container_size;
}

if($container_type=="div"){ ?>

        <div <?php echo esc_attr( $anchor ); ?> class="<?php echo esc_attr( $class_name ); ?>" <?php if(!$disable_animation){ echo ' uk-scrollspy="target: .content-wrap; cls: uk-animation-slide-bottom-medium; delay: 200;repeat:true;"';} ?>>

        <?php }else{ ?>

        <section <?php echo esc_attr( $anchor ); ?> class="<?php echo esc_attr( $class_name ); ?>"   <?php if(!$disable_animation){ echo ' uk-scrollspy="target: .content-wrap; cls: uk-animation-slide-bottom-medium; delay: 200;repeat:true;"';} ?>>

    <?php } ?>

    
    <?php if( $wrap_size == "full-page-wrap"){ ?>

    
        <div class="content-wrap top-pad-<?php echo $vertical_pad_top; ?> botton-pad-<?php echo $vertical_pad_bottom; ?> " style="position:relative;background-color:<?php echo $background_color; ?>">
        <div class="<?php echo $container_size; ?>">

    <?php }else{ ?>

        <div class="<?php echo $container_size; ?>">
      
        <div class="content-wrap  top-pad-<?php echo $vertical_pad_top; ?> botton-pad-<?php echo $vertical_pad_bottom; ?>" style="position:relative;%;background-color:<?php echo $background_color; ?>">
        <?php } ?>


    <div class="section-content">
            
            
            
<?php }


function closeSection($wrap_size,$container_size,$container_type,$graphic){ ?>
                </div>
            
        </div>
    </div>
    <?php
    if($container_size != "full-page"){
        $container_size = "uk-container " . $container_size;
    }
    ?>

    <?php if($graphic>0){ ?>

        <div class="deco-wrap <?php echo $container_size; ?>" >
            <div class="section-decoration"><?php drawSVG('section-graphic-' . $graphic); ?></div>
        </div>
    
    <?php } ?>
    
    <?php if($container_type=="div"){ ?>

        </div>

    <?php }else{ ?>

        </section>

    <?php } ?>
    
<?php }


function drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style,$title_container_size="uk-container"){ ?>

<div class="uk-container <?php echo $title_container_size; ?>">
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
    </div>
<?php }



function render_block_preview_if_applicable( $block ) {
    if ( isset( $block['data']['preview_image_help'] ) && $block['data']['preview_image_help'] ) {

        // Get current block directory path
        $block_dir_path = dirname( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 1 )[0]['file'] );

        // Get current block directory URL
        $theme_dir_path = get_template_directory();
        $theme_dir_url  = get_template_directory_uri();
        $relative_path  = str_replace( $theme_dir_path, '', $block_dir_path );
        $block_dir_url  = $theme_dir_url . $relative_path;

        // Look for preview.png or preview.jpg
        $preview_image = '';
        if ( file_exists( $block_dir_path . '/preview.png' ) ) {
            $preview_image = $block_dir_url . '/preview.png';
        } elseif ( file_exists( $block_dir_path . '/preview.jpg' ) ) {
            $preview_image = $block_dir_url . '/preview.jpg';
        }

        // Output image if found
        if ( $preview_image ) {
            echo '<img src="' . esc_url( $preview_image ) . '" alt="Block Preview" style="width:100%; height:auto;" />';
        } else {
            echo '<div style="padding:1em; background:#eee;">Preview image not found.</div>';
        }

        // Stop further rendering
        return true;
    }

    return false;
}
