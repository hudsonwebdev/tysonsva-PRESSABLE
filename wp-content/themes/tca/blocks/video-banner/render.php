<?php 
$container_settings = get_field('container_settings');
$section_header = get_field('section_header');
include __DIR__ .'/../../inc/common_block_variables.php';
$video_url = get_field('video_url');

// Block preview



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
<div class="video-banner">
    <div class="uk-container">
        <div class="tca-video-wrap">
            <div class="overlay">
                <div class="inner">
                    <div class="left">
                        <h1>This<br>Way<br>Up.</h1>
                    </div>
                    <div class="right">
                        <p>Discover a new urban experience</p>
                    </div>  
                </div>
            </div>
            <div class="video-tint"></div>
            <?php if($video_url){ ?>
                <video class="tca-video-background" autoplay muted loop playsinline src="<?php echo $video_url; ?>" data-object-fit="cover"></video>
            <?php } ?>
            
        </div>
        
    </div>
  
</div>
<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);

