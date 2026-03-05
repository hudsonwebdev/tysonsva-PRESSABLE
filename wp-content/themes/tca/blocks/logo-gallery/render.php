<?php
if ( render_block_preview_if_applicable( $block ) ) return;
$container_settings = get_field('container_settings');
$section_header = get_field('section_header');
include __DIR__ .'/../../inc/common_block_variables.php';

$logos = get_field('logos');
$logoSpc = get_field('logo_spacing')?get_field('logo_spacing'):'10';
$logoSize = get_field('logo_size')?get_field('logo_size'):'150';
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
    <div class="logo-list" >
    <?php  foreach( $logos as $row ):
    $image = $row['logo'];
    $link = $row['logo_link'];
    $logo_size_override = $row['logo_size_override']?$row['logo_size_override']:$logoSize;
    $alt_text = $image['title'] ?? '';

    if( $image ):
      $img_id = isset( $image['id'] ) ? $image['id'] : $image['ID'];
      $logo_style = 'padding:' . esc_attr( $logoSpc ) . 'px;width:' . esc_attr( $logo_size_override ) . 'px;height:auto;';

      if( $link && isset($link['url']) ):
        echo '<div class="logo" style="max-width:' . esc_attr( $logo_size_override ) . 'px"><a href="' . esc_url( $link['url'] ) . '"';
        if( isset( $link['target'] ) && $link['target'] === '_blank' ):
          echo ' target="_blank" rel="noopener"';
        endif;
        echo '>';
      else:
        echo '<div class="logo">';
      endif;
      echo '<img ';
      if ( $img_id ) {
          awesome_acf_responsive_image( $img_id, 'medium', '320px', $alt_text );
      } else {
          echo 'src="' . esc_url( $image['sizes']['medium'] ) . '" alt="' . esc_attr( $alt_text ) . '"';
      }
      echo ' style="' . $logo_style . '" loading="lazy">';
      if( $link && isset($link['url']) ):
        echo '</a></div>';
      else:
        echo '</div>';
      endif;
    endif;
  endforeach; ?>
    </div>
<?php endif;


closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);




         
        


