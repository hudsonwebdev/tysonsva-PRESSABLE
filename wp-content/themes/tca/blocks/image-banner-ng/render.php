<?php

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


$args = array(
    'post_type' => 'neighborhood',
    'posts_per_page' => -1

);

$neighborhoods = get_posts($args);





drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); ?>

<div class="image-banner-ng">
<div class="chevron"><svg xmlns="http://www.w3.org/2000/svg" width="432" height="432" viewBox="0 0 432 432" fill="none">
  <path d="M129.582 0L0 129.582H302.418V432L432 302.43V0H129.582Z" fill="#385DFF"/>
</svg></div>
    
<div id="neighborhood-map" class="neighborhood-map" style="width:100%;
  min-height:830px;"></div>      


       

            <div class="text-overlay">
                <div class="inner">


                    <div class="page-selector">
                    <label for="neighborhoodForm">Select a Neighborhood</label>
                    <form action="<?php echo get_the_permalink(); ?>" method="post" id="neighborhoodForm">
                        <select name="ng-page" id="neighborhoodSelect" class="tca-select">

                        <?php foreach($neighborhoods as $n){ ?>
                            <option value="<?php echo get_the_permalink($n->ID);?>"><?php echo get_the_title($n->ID);?></option>

                        <?php   }  ?>
                        
                            
                        </select>
                    </form>

                    </div>

                    <?php if($large_title>""){ ?>
                    <h1 class="banner-title"><?php echo $large_title; ?></h1>
                    <?php   }  ?>
              
                    
                </div>
            </div>
            
       



              
<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);

