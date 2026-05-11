<?php
if ( render_block_preview_if_applicable( $block ) ) return;
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
$vertical_text_position = get_field('vertical_text_position')?get_field('vertical_text_position'):'center';

$above_title_spacer = get_field('above_title_spacer')?get_field('above_title_spacer'):0;
$hide_decorative_chevron = get_field('hide_decorative_chevron')?get_field('hide_decorative_chevron'):false;
$image_or_video = get_field('image_or_video')?get_field('image_or_video'):"Image";
$video = get_field('video');




drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); ?>


        <?php if($image_or_video == "Image" && $banner_image){ ?>

            <div class="image-banner">
                <?php if(!$hide_decorative_chevron){ ?>
                    <div class="chevron"><svg xmlns="http://www.w3.org/2000/svg" width="432" height="432" viewBox="0 0 432 432" fill="none">
                        <path d="M129.582 0L0 129.582H302.418V432L432 302.43V0H129.582Z" fill="#385DFF"/></svg>
                    </div>
                <?php } ?>
  

                <div class="image-tint"></div>

        
                <div class="image-wrap">
                    <img <?php awesome_acf_responsive_image( $banner_image['id'], 'tca-hero', '1920px', $banner_image['alt'], true ); ?> />
                    
                </div>
            </div>    
       
        <?php } elseif ( $image_or_video == 'Video' && $video && ! empty( $video['url'] ) ) {
            $video_url = $video['url'];
            // Poster: required for LCP and for iOS (video poster attribute often doesn't show when video has no src).
            $poster_url = '';
            $poster_id  = get_field( 'video_poster' );
            if ( $poster_id && is_array( $poster_id ) && ! empty( $poster_id['url'] ) ) {
                $poster_url = $poster_id['url'];
            } elseif ( $poster_id && is_numeric( $poster_id ) ) {
                $poster_url = wp_get_attachment_image_url( (int) $poster_id, 'tca-hero' );
            }
            if ( ! $poster_url && $banner_image && ! empty( $banner_image['url'] ) ) {
                $poster_url = $banner_image['url'];
            }
            $poster_attrs = '';
            if ( $poster_url ) {
                $poster_attrs = ' src="' . esc_url( $poster_url ) . '" alt="" fetchpriority="high" loading="eager"';
                if ( $banner_image && ! empty( $banner_image['width'] ) && ! empty( $banner_image['height'] ) ) {
                    $poster_attrs .= ' width="' . esc_attr( $banner_image['width'] ) . '" height="' . esc_attr( $banner_image['height'] ) . '"';
                } elseif ( $poster_id && is_array( $poster_id ) && ! empty( $poster_id['width'] ) && ! empty( $poster_id['height'] ) ) {
                    $poster_attrs .= ' width="' . esc_attr( $poster_id['width'] ) . '" height="' . esc_attr( $poster_id['height'] ) . '"';
                }
            }
            ?>
            <div class="video-banner">
                
                <?php if ( $poster_url ) : ?>
              

                <?php
                $poster = get_field( 'video_poster' );
                if ( ! $poster && ! empty( $banner_image['id'] ) ) {
                    $poster = $banner_image;
                }
                if ( $poster ) :
                ?>
                <img class="video-banner-poster" <?php tca_video_banner_poster_attrs( $poster ); ?> />
                <?php endif; ?>


                <?php endif; ?>
                <video class="tca-video-background" autoplay muted loop playsinline preload="none"
                    <?php if ( $poster_url ) { ?> poster="<?php echo esc_url( $poster_url ); ?>"<?php } ?>
                    data-src="<?php echo esc_url( $video_url ); ?>"
                    data-object-fit="cover"></video>
                    <div class="image-tint"></div>
                    <button type="button" class="tca-video-pp-btn" aria-pressed="false" aria-label="Pause background video">
                        <svg class="tca-video-pp-icon-pause" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <rect x="6" y="5" width="4" height="14" rx="1"></rect>
                            <rect x="14" y="5" width="4" height="14" rx="1"></rect>
                        </svg>
                        <svg class="tca-video-pp-icon-play" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M8 5v14l11-7z"></path>
                        </svg>
                    </button>
            </div>
            <?php
        } ?>


        


        <?php if($additional_text || $large_title){ ?>

            <div class="text-overlay">
                <div class="uk-container">
                  
                            <div class="inner" style="justify-content:<?php echo $vertical_text_position; ?>">
                            <h1 class="banner-title"><?php echo $large_title; ?></h1>

                            <?php if(get_field('add_additional_text')){ ?>
                            <?php echo $additional_text; ?>
                            <?php } ?>
                  
                    </div>
                </div>
            </div>         
        <?php } ?>





                        
<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);

