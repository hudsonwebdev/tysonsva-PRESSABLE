<?php

function draw_news_card($pid,$type,$columns=1) {

$columns = 1;

$img_id = get_post_thumbnail_id($pid);

$title = get_the_title();


if(get_field('source_url',$pid)){

    $url = get_field('source_url',$pid);
    $target = "_blank";

}else{

    $url = get_the_permalink($pid);
    $target = "_self";

}


?>
<div class="flex-item   columns-<?php echo $columns; ?>">
    <div class="news-card type-<?php echo $type; ?>">
        <div class="inner-wrap">
            <div class="innerblue"></div>


            <div class="single-stack">


                <div class="card-image">
                <?php if($img_id>0){ ?>
                    <img <?php awesome_acf_responsive_image($img_id,'large','768px',$title); ?>  />
                    <?php } ?>
                </div>
                
        
                <div class="tca-card-info">
                        <div class="inner">
                            <div class="card-header">
                                <div class="card-type"><?php get_first_category($pid); ?></div>
                                <div class="card-date"><?php echo get_the_date(); ?></div>
                            </div>
                            <div class="card-title"> 
                                <h4><a href="<?php echo $url; ?>" target="<?php echo $target; ?>"><?php echo max_title_length( $title ); ?></a></h4>
                            </div>

                  

                            <div class="excerpt">
                                
                            <?php echo get_the_excerpt(); ?>
                            
                        
                            </div>

                            <div class="card-footer">
                                <a href="<?php echo $url; ?>" class="readmore" target="<?php echo $target; ?>">Read More <svg xmlns="http://www.w3.org/2000/svg" width="9" height="8" viewBox="0 0 9 8" fill="none">
                                <path d="M4.40907 0.010141L0.989859 0.0101405L4.97973 4.00002L0.989859 7.98989L4.40892 7.99004L8.39894 4.00002L4.40907 0.010141Z" />
                                </svg></a>
                            </div>
                        

                            
                
                        
                    </div>
                    
                </div>
                
            </div>

          

        </div>
    </div>
</div>
<?php
}

/****************************************************************************************************************/
/***********************************GRID VIEW************************************************/
/****************************************************************************************************************/
function draw_news_card_grid($pid,$columns=1) {



$img_id = get_post_thumbnail_id($pid);

$title = get_the_title($pid);

$medium_portrait = get_field('medium_portrait',$pid);

$additional_card_info = get_field('additional_card_info',$pid);

$category_class_array = get_post_event_categories_as_classes( $pid );

if(get_field('source_url',$pid)>""){

    $url = get_field('source_url',$pid);
    $target = "_blank";

}else{

    $url = get_the_permalink($pid);
    $target = "_self";

}


?>

<div class="flex-item  columns-<?php echo $columns; ?>">

    <div class="event-card <?php echo implode(' ', $category_class_array); ?>">

        <div class="outerblue">
            <div class="outer-card-chevron"><?php drawSVG('outer-chevron-up-card'); ?></div>
        </div> 
        <div class="inner-wrap">

            <div class="chev-up"><?php drawSVG('chevron-up-card'); ?></div>

            <div class="innerblue"></div>

                <div class="single-stack">

                    <div class="card-image">

                    <?php if($medium_portrait && $columns == 1){ ?>
                        
                            <img <?php awesome_acf_responsive_image($medium_portrait ['ID'],'medium','440px',$title); ?> />
                    
                    <?php }elseif($img_id>0){ ?>

                        <?php if($columns == 2){ ?>
                            <img <?php awesome_acf_responsive_image($img_id,'featured-image','1378px',$title); ?> />
                        <?php }else{ ?>
                            <img <?php awesome_acf_responsive_image($img_id,'large','768px',$title); ?> />
                        <?php } ?>


                        <?php } ?>
                    </div>
                    
                    
                    
                    <div class="tca-card-info">
                        <div class="inner">
                        <div class="card-header">
                                <div class="card-type"><?php get_first_category($pid); ?></div>
                                <div class="card-date"></div>
                            </div>
                            <div class="card-title">
                                <h4><a href="<?php echo $url; ?>" target="<?php echo $target; ?>"><?php echo max_title_length( $title ); ?></a></h4>
                               
                   
                                
                            </div>

                            
                        </div>
                    </div>

                </div>

         </div>
    </div>
    </div>
<?php
}
