<?php

$container_settings = get_field('container_settings');
$section_header = get_field('section_header');
include __DIR__ .'/../../inc/common_block_variables.php';

$logos = get_field('logos');
$logoCols = get_field('logo_columns');
$logoSpc = get_field('logo_spacing');

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

drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); 

if( $logos ): ?>
    <div class="logo-list col-<?= $logoCols; ?> spc-<?= $logoSpc; ?>">
    <?php  foreach( $logos as $row ):
    $image = $row['logo'];
    $link = $row['logo_link'];
    $alt_text = $image['title'] ?? '';

    if( $image ):
      $img_tag = '<img src="' . esc_url($image['sizes']['medium']) . '" alt="' . esc_attr($alt_text) . '" style="margin: '. $logoSpc .'px">';

      if( $link && isset($link['url']) ):
        echo '<div class="logo"><a href="' . esc_url($link['url']) . '"';
        if( isset($link['target']) && $link['target'] === '_blank' ):
          echo ' target="_blank" rel="noopener"';
        endif;
        echo '>' . $img_tag . '</a></div>';
      else:
        echo '<div class="logo">' . $img_tag . '</div>';
      endif;
    endif;
  endforeach; ?>
    </div>
<?php endif;


closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);



         
        


