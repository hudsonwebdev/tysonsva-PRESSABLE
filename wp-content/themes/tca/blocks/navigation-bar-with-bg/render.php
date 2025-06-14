<?php

$container_settings = get_field('container_settings');
$section_header = get_field('section_header');
include __DIR__ .'/../../inc/common_block_variables.php';

$bgColor = get_field('bg_color');
$navLinks = get_field('links');

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

$justify = get_field('link_alignment')?get_field('link_alignment'):'left';

if (!empty($navLinks)): ?>
  <ul class="nav-bar" style="background-color: <?php echo $bgColor; ?>;justify-content:<?php echo $justify; ?>;">
    <?php 
    $current_url = home_url(add_query_arg([], $GLOBALS['wp']->request));
    $current_url = trailingslashit(esc_url_raw($current_url)); 

    foreach ($navLinks as $item): 
      $linkType = $item['link_type'];
      $link = $item['link'];
      $jumpLink = $item['jump_section_idname'];

      if ($linkType == 1 && $link && isset($link['url'])):
        $url = esc_url_raw(trailingslashit($link['url']));
        $title = $link['title'] ?? '';
        $target = $link['target'] ?? '_self';
        $is_current = $url === $current_url;
    ?>
        <li class="<?php echo $is_current ? 'current-page' : 'nav-link'; ?>">
          <a href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($target); ?>" <?php if ($target === '_blank') echo 'rel="noopener"'; ?>>
            <?php echo esc_html($title); ?>
          </a>
        </li>
      
      <?php elseif ($linkType == 0 && !empty($jumpLink)): 
        $url = '#' . sanitize_title($jumpLink);
        $title = $item['link_text'] ?? 'Jump Link';
      ?>
        <li class="nav-link">
          <a href="<?php echo esc_attr($url); ?>">
            <?php echo esc_html($title); ?>
          </a>
        </li>
      <?php endif; 
    endforeach; ?>
  </ul>
<?php endif;

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);



         
        


