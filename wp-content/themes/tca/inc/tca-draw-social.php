<?php
function drawSocial(){ ?>

    <div class="social">
        <a href="https://www.instagram.com/tysons_va/" target="_blank" uk-icon="icon: instagram"></a>
        <a href="https://www.facebook.com/TysonsCommunityAlliance" target="_blank"  uk-icon="icon: facebook"></a>
        <a href="https://www.flickr.com/photos/tysonscommunityalliance/albums"  target="_blank" uk-icon="icon:flickr"></a>
        <a href="https://www.youtube.com/@tysonscommunityalliance"  target="_blank" uk-icon="icon: youtube"></a>
        <a href="https://www.linkedin.com/company/tysons-community-alliance" target="_blank"  uk-icon="icon: linkedin"></a>

    </div>

<?php }

/**
 * Get social icons HTML formatted as menu list items for utility menu
 * 
 * @return string HTML for social icons as menu items
 */
function getUtilityMenuSocialIcons() {
    $social_links = array(
        array(
            'url' => 'https://www.instagram.com/tysons_va/',
            'icon' => 'instagram',
            'label' => 'Instagram'
        ),
        array(
            'url' => 'https://www.facebook.com/TysonsCommunityAlliance',
            'icon' => 'facebook',
            'label' => 'Facebook'
        ),
        array(
            'url' => 'https://www.flickr.com/photos/tysonscommunityalliance/albums',
            'icon' => 'flickr',
            'label' => 'Flickr'
        ),
        array(
            'url' => 'https://www.youtube.com/@tysonscommunityalliance',
            'icon' => 'youtube',
            'label' => 'YouTube'
        ),
        array(
            'url' => 'https://www.linkedin.com/company/tysons-community-alliance',
            'icon' => 'linkedin',
            'label' => 'LinkedIn'
        ),
    );
    
    $output = '';
    foreach ( $social_links as $link ) {
        $output .= '<li class="utility-social-icon">';
        $output .= '<a href="' . esc_url( $link['url'] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $link['label'] ) . '">';
        $output .= '<span uk-icon="icon: ' . esc_attr( $link['icon'] ) . '"></span>';
        $output .= '</a>';
        $output .= '</li>';
    }
    
    return $output;
}

/**
 * Prepend social icons to utility menu items
 * 
 * @param string $items HTML list content for the menu items
 * @param stdClass $args An object containing wp_nav_menu() arguments
 * @return string Modified menu items HTML
 */
function prependSocialIconsToUtilityMenu( $items, $args ) {
    // Only apply to utility menu
    if ( isset( $args->theme_location ) && $args->theme_location === 'utility-menu' ) {
        $social_icons = getUtilityMenuSocialIcons();
        $items = $social_icons . $items;
    }
    
    return $items;
}
add_filter( 'wp_nav_menu_items', 'prependSocialIconsToUtilityMenu', 10, 2 );



function drawSocialShare($title="",$url=""){ ?>
    <?php if($title>""){ ?>
        <h4 class="share-title">Share This:</h4>
    <?php } ?>
    <div class="social">
   
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" uk-icon="icon: facebook"></a>
     
        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>" uk-icon="icon: linkedin"></a>

        <a href="mailto:?subject=<?php echo $title; ?>&body=<?php echo $url; ?>" uk-icon="icon: mail"></a>

       
    </div>
    
<?php }