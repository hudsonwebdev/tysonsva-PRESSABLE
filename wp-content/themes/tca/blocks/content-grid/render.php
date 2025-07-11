<?php

$container_settings = get_field('container_settings');
$section_header = get_field('section_header');
include __DIR__ .'/../../inc/common_block_variables.php';

$feature_first_post = get_field('feature_first_post')?get_field('feature_first_post'):false;
$content_selection = get_field('content_selection')?get_field('content_selection'):'Upcoming Events';
$stick_to_top = get_field('stick_to_top')?get_field('stick_to_top'):array();



$total_posts = get_field('total_posts')?get_field('total_posts'):6;

$total_posts = intval($total_posts - count($stick_to_top));

$column_count_desktop = get_field('column_count_desktop')?get_field('column_count_desktop'):3;



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
 

switch($content_selection){





case "Upcoming Events":



    $args = array(
        'post_type' => 'event',
        'posts_per_page' => $total_posts, // Number of events per page
        'paged' => get_query_var('paged', 1), // Pagination support
        'orderby'        => 'meta_value', // Order by the custom field value
        'order'          => 'ASC', // Ascending order (upcoming first)
        'meta_type'      => 'DATE', // Make sure the field is treated as a date
        'meta_query'     => array(
            array(
                'key'     => '_event_start_date', // Start date field
                'value'   => date('Y-m-d'), // Current date
                'compare' => '>=', // Only future events
                'type'    => 'DATE',
            ),
        ),
        'post__not_in'   => $stick_to_top,
        );

        $containerClass = "event-container grid-view";

        $post_query = new WP_Query($args);
   
break;
case "Latest News":

    $args = array(
        'post_type'      => 'post',       // Get posts (not pages, etc.)
        'posts_per_page' => $total_posts,            // Number of posts to retrieve
        'orderby'        => 'date',       // Order by post date
        'order'          => 'DESC',       // Show the latest first
    );

    $containerClass = "news-container";

    $post_query = new WP_Query($args);
    
break;
case "Mixed Content":

    $containerClass = "event-container grid-view";

    $post_query = get_field('post_picker');
    
break;
case "Resource List":

    $containerClass = "event-container grid-view";

    $post_query = get_field('resource_picker');
    
break;
case "Bio List":

    $containerClass = "bio-container";

    $post_query = get_field('bio_picker');
    
    break;
    case "Venue List":

    $containerClass = "post-container";

    $post_query = get_field('venue_picker');
    
    break;
}

$containerClass .= " column-count-" . $column_count_desktop;

drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); ?>

<div class="<?php echo $containerClass; ?>"  uk-scrollspy="target: .flex-item; cls: uk-animation-slide-bottom-medium; delay: 200;repeat:true;">
        
            <?php 

        
        switch($content_selection){

            case "Latest News":

     
            if ($post_query->have_posts()) :
                $count = 0;
                while ($post_query->have_posts()) : $post_query->the_post();

                    $pid = get_the_ID();

                    if($feature_first_post && $count==0){
                        $columns = 2;
                    }else{
                        $columns = 1;
                    }    

                   
                            draw_news_card($pid,2,$columns);
                      


                

                    $count++;
                endwhile;


            endif;

            break;

            case "Upcoming Events":

                $count = 0;
                if(!empty($stick_to_top)){

                    foreach($stick_to_top as $pid){

                   
                        if($feature_first_post && $count==0){
                            $columns = 2;
                        }else{
                            $columns = 1;
                        }    
    
                       
                        draw_event_card($pid,$columns);

                        $count++;
                    }
        
                }
            

     
                if ($post_query->have_posts()) :
                    
                    while ($post_query->have_posts()) : $post_query->the_post();
    
                        $pid = get_the_ID();
    
                        if($feature_first_post && $count==0){
                            $columns = 2;
                        }else{
                            $columns = 1;
                        }    
    
                       
                         draw_event_card($pid,$columns);
                          
                        $count++;
                    endwhile;
    
    
                endif;
    
                break;


            case "Bio List":

    

            if(!empty($post_query)){

              
                $count = 0;
                foreach($post_query as $post){

            

                    draw_bio_card($post->ID);

                    $count++;
                
                }

            }

            break;

            case "Venue List":

    

            if(!empty($post_query)){

              
                $count = 0;
                foreach($post_query as $post){

                    

                    draw_venue_card($post->ID);

                    $count++;
                
                }

            }

            break;



            case "Mixed Content":

    

            if(!empty($post_query)){

       

                $count = 0;
                foreach($post_query as $post){

                    if($feature_first_post && $count==0){
                        $columns = 2;
                    }else{
                        $columns = 1;
                    }    

                   drawByPostType($post->ID,$columns,$count);

                    $count++;
                
                }

            }

            break;



            case "Resource List":

            

            if(!empty($post_query)){

     

                $count = 0;
                foreach($post_query as $post){

                    if($feature_first_post && $count==0){
                        $columns = 2;
                    }else{
                        $columns = 1;
                    }    

                   drawByPostType($post->ID,$columns,$count);

                    $count++;
                
                }

            }

        }
      
                
        ?>
                  
</div>
   
            
<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);

