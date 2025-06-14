<?php

$container_settings = get_field('container_settings');
$section_header = get_field('section_header');
include __DIR__ .'/../../inc/common_block_variables.php';

$colSecTtl = get_field('column_section_title');
$colCount = get_field('column_count');
$cols = get_field('columns');

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

echo '<section class="fdc-cols">';

if ($colSecTtl) {
    echo '<h2 class="script c-txt"><span>' . esc_html($colSecTtl) . '</span></h2>';
}

if ($cols) {
    // UK widthx from col count
    $widthClass = '';
    switch (intval($colCount)) {
        case 1:
            $widthClass = 'uk-width-1-1';
            break;
        case 2:
            $widthClass = 'uk-width-1-2@m';
            break;
        case 3:
            $widthClass = 'uk-width-1-3@m';
            break;
        case 4:
            $widthClass = 'uk-width-1-4@m';
            break;
        default:
            $widthClass = 'uk-width-1-2@m';
    }

    echo '<div class="uk-grid uk-child-width-expand@s uk-grid-match" data-uk-grid>';

    foreach ($cols as $col) {
        $bhImg = $col['block_header_image'];
        $flexContent = $col['flex_content'];
        $borderColor = $col['block_border_color'] ?: '#000';
        $borderThickness = intval($col['block_border_thickness']);
        $bgColor = $col['block_background_color'] ?: 'transparent';
        $ctaLink = $col['cta_link'];
        $ctaLinkType = $col['cta_link_type'];

        if ($borderThickness > 10) {
            $borderThickness = 10;
        }

        $style = sprintf(
            'style="background-color: %s; border: %dpx solid %s;"',
            esc_attr($bgColor),
            $borderThickness,
            esc_attr($borderColor)
        );

        echo '<div class="' . esc_attr($widthClass) . '">';
            echo '<div class="uk-card uk-card-default uk-card-body" ' . $style . '>';

                
                if (!empty($bhImg)) {
                    echo '<div class="block-header-image">';
                    echo wp_get_attachment_image($bhImg['ID'], 'medium', false, [
                        'alt' => esc_attr($bhImg['alt']),
                        'class' => 'uk-margin-small-bottom'
                    ]);
                    echo '</div>';
                }

                
                echo $flexContent;

                
                
                if (!empty($ctaLink)) {
                    $link_url = esc_url($ctaLink['url']);
                    $link_title = esc_html($ctaLink['title']);
                    $link_target = esc_attr($ctaLink['target'] ?: '_self');
                    $type_class = !empty($ctaLinkType) ? ' ' . esc_attr($ctaLinkType) : '';

                    // Determine if it's a centered variant
                    $isCentered = strpos($ctaLinkType, 'ctr') !== false;
                    $wrapClass = 'uk-margin-top';
                    if ($isCentered) {
                        $wrapClass .= ' uk-flex uk-flex-center';
                    }

                    echo '<div class="' . $wrapClass . '">';
                    echo '<a href="' . $link_url . '" target="' . $link_target . '" class="tca-button ' . $type_class . '">' . $link_title . '</a>';
                    echo '</div>';
                }


            echo '</div>';
        echo '</div>';
    }

    echo '</div>';
}

echo '</section>';


closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);



         
        


