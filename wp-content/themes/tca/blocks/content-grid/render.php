<?php
if ( render_block_preview_if_applicable( $block ) ) return;
$container_settings = get_field('container_settings');
$section_header = get_field('section_header');
include __DIR__ .'/../../inc/common_block_variables.php';


$feature_first_post = get_field('feature_first_post')?get_field('feature_first_post'):false;
$content_selection = get_field('content_selection')?get_field('content_selection'):'Upcoming Events';
$stick_to_top = get_field('stick_to_top')?get_field('stick_to_top'):array();



$total_posts = get_field('total_posts')?get_field('total_posts'):6;

$total_posts = intval($total_posts - count($stick_to_top));

$column_count_desktop = get_field('column_count_desktop')?get_field('column_count_desktop'):3;

$containerClass = "";

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
       'meta_query' => array(
        'relation' => 'OR',
            array(
                'key' => '_event_start_date',
                'value' => date('Y-m-d'),
                'compare' => '>=',
                'type' => 'DATE',
            ),
            array(
                'key' => '_event_end_date',
                'value' => date('Y-m-d'),
                'compare' => '>=',
                'type' => 'DATE',
            ),
        ),
        'post__not_in'   => $stick_to_top,
        );

        $containerClass = "event-container grid-view";

        $post_query = new WP_Query($args);
   
break;
case "Latest News":

    $args = array(
    'post_type'      => 'post',
    'posts_per_page' => $total_posts,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'tax_query'      => array(
        array(
            'taxonomy' => 'category',     // Taxonomy name
            'field'    => 'slug',         // Could also be 'term_id' or 'name'
            'terms'    => 'featured',     // Term to filter by
        ),
    ),
);


    $containerClass = "news-container";

    $post_query = new WP_Query($args);
    
break;
case "Mixed Content":

    $containerClass = "event-container  grid-view";

    $post_query = get_field('post_picker');
    
break;

case "Type And Category":

    $containerClass = "event-container grid-view";


$post_types = get_field('post_type'); // Array like ['post', 'event', 'resource']


$selected_terms = get_field('tags_and_categories'); // Could be array of term IDs or objects




    
break;


case "Resource List":
    case "Resource Grid":   

    $containerClass = "resource-container list-view";

    $post_query = get_field('resource_picker');
    
break;
case "Resource List":

    $containerClass = "resource-container grid-view";

    $post_query = get_field('resource_picker');
    
break;
case "Bio List":

    $containerClass = "bio-container";



    $post_query = get_field('bio_picker');
    
    break;
case "Profile List":

    $containerClass = "bio-container";



    $post_query = get_field('profile_picker');
    
    break;
    
}

$containerClass .= " column-count-" . $column_count_desktop;

drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); ?>

    <?php $display_type = get_field('display_type'); ?>

    <?php if($display_type == "Slider"){ ?>

    <div uk-slider>

        <div class="uk-position-relative">

            <div class="uk-slider-container">
                <div class="uk-slider-items uk-child-width-1-3@s uk-child-width-1-<?php echo $column_count_desktop; ?>@  uk-grid uk-grid-small" >
    <?php  }else{ ?>

    <div class="<?php echo $containerClass; ?>"  uk-scrollspy="target: .flex-item; cls: uk-animation-slide-bottom-medium; delay: 200;repeat:true;">
  
    <?php 
    }
        
        switch($content_selection){

            case "Latest News":

                $count = 0;
                if(!empty($stick_to_top)){

                    foreach($stick_to_top as $pid){

                   
                        if($feature_first_post && $count==0){
                            $columns = 2;
                        }else{
                            $columns = 1;
                        }    
    
                       
                        draw_news_card($pid,2,$columns);

                        $count++;
                    }
        
                }

     
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

            

                    draw_bio_card($post->ID,1);

                    $count++;
                
                }

            }

            break;


            case "Profile List":

    

            if(!empty($post_query)){

              
                $count = 0;
                foreach($post_query as $post){

            

                    draw_bio_card($post->ID,2);

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

            case "Type And Category":

                $term_ids = get_field('tags_and_categories');
              
                $future_only = get_field('only_include_future_events')?get_field('only_include_future_events'):false;

                $query = get_content_by_term_ids($term_ids, $post_types,$future_only,$total_posts);

                 if ($query->have_posts()) {

                $count = 0;
                $columns  = 1;
                while ($query->have_posts()) {

                    $query->the_post();

                    drawByPostType(get_the_ID(),$columns,$count);

                    $count++;

                }
            } 

    
            break;



            case "Resource List":
             
            

            if(!empty($post_query)){

     

                $count = 0;
                foreach($post_query as $post){

    
                   draw_resource_list($post->ID,$count);

                    $count++;
                
                }

            }

            break;


            case "Resource Grid":

        

            if(!empty($post_query)){

     

                $count = 0;
                foreach($post_query as $post){

  
                   draw_resource_card($post->ID,$count);

                    $count++;
                
                }

            }

            break;

        }
      
                
        ?>

        <?php if($display_type == "Slider"){ ?>

                </div>

                        <a class="uk-position-center-left-out prev-arrow" href uk-slider-item="previous">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="23" viewBox="0 0 14 23" fill="none">
                            <path d="M12.1211 21.061L2.12109 11.055L12.1211 1.06104" stroke="#385DFF" stroke-width="3" stroke-miterlimit="10"/>
                            </svg>
                        </a>
                        <a class="uk-position-center-right-out next-arrow" href uk-slider-item="next">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="23" viewBox="0 0 14 23" fill="none">
                            <path d="M1.06055 1.0603L11.0605 11.0663L1.06054 21.0603" stroke="#385DFF" stroke-width="3" stroke-miterlimit="10"/>
                            </svg>
                        </a>

                    </div>

                    <ul class="uk-slider-nav uk-dotnav content-dots"></ul>

                </div>

        <?php }else{ ?>

            </div>

        


<?php } ?>




                  
</div>
   
            
<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);

