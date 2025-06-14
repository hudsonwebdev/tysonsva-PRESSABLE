<?php

// Function to render events in grid view
function display_event_featured($eid,$number_of_events, $eventCount) {


    
  
    $title = get_the_title();

    
    $img_id = get_post_thumbnail_id($eid);
    ?>
        <div class="featured-slide  slide-<?php echo  $eventCount; ?> <?php if($eventCount==0){ echo 'active';} ?>">
            <div class="featured-image">
                <img <?php awesome_acf_responsive_image($img_id,'featured-image','1500px',$title); ?>  />
            </div>
            <div class="event-info">
                <div class="inner">
                    <ul class="event-nav">
                        <li class="title">Featured Event</li>
                        <?php for($x=0;$x<$number_of_events;$x++){ ?>
                            <li><a href="#slide-<?php echo  $x; ?>" class="featured-slide-trigger <?php if( $eventCount==$x){ echo "active"; } ?>"><?php echo $x+1; ?></a></li>
                        <?php } ?>
                    </ul>

                    <div class="tca-card-info">
                        <div class="inner">
                            <div class="card-header">
                                <div class="card-type">Event</div>
                                <div class="card-date"><?php echo get_date_display($eid); ?></div>
                            </div>
                            <div class="card-title">
                                <h4><a href="<?php echo get_the_permalink($eid); ?>"><?php echo max_title_length( $title ); ?></a></h4>
                            </div>
                        </div>
                    </div>
                    
               
                 
                </div>
            </div>
        </div>
 
    <?php
}



function draw_event_card($eid,$columns=1) {


    $img_id = get_post_thumbnail_id($eid);

    if(get_field('short_title',$eid)>""){
        $title = get_field('short_title',$eid);

    }else{
        $title = get_the_title($eid);
    }
    

    $medium_portrait = get_field('medium_portrait',$eid);

    $additional_card_info = get_field('additional_card_info',$eid);

    $category_class_array = get_post_event_categories_as_classes( $eid );

    if(get_field('external_url',$eid)>""){

       

        $url = get_field('external_url',$eid );


        // Check if 'tysonsva.org' is in the URL
        if (strpos($url, 'tysonsva.org') !== false) {
            $target = "_self";
        } else {
            $target = "_blank";
        }



    }else{

        $url = get_the_permalink($eid);
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
                                <img <?php awesome_acf_responsive_image($img_id,'featured-image','1378px',$title); ?>  />
                            <?php }else{ ?>
                                <img <?php awesome_acf_responsive_image($img_id,'large','768px',$title); ?>  />
                            <?php } ?>


                            <?php } ?>
                        </div>
                        
                        
                        
                        <div class="tca-card-info">
                            <div class="inner">
                                <div class="card-header">
                                    <div class="card-type">Event</div>
                                    <div class="card-date"><?php echo get_date_display($eid); ?></div>
                                    
                                </div>
                                <div class="card-title">
                               
                                
                                    <h4><a href="<?php echo $url; ?>" target="<?php echo $target; ?>"><?php echo max_title_length( $title ); ?></a></h4>
                                    <?php printVenu($eid,false); ?>
                                    

                                    <?php if(in_array('tca-event',$category_class_array)){ ?>
                                    <div  class="tcaeventlogo"><img src="<?php echo get_template_directory_uri(); ?>/img/tcalogoevents.png" alt="TCA Event" width="100"/></div>
                                    <?php } ?>

                                    
                                    
                                </div>
                                

                                
                            </div>
                        </div>

                    </div>

             </div>
        </div>
        </div>
    <?php
}

function printVenu($eid,$showAddress=true,$additional_location_info=""){
     

     $venue = get_field('location',$eid); 
    
     if($venue ){  ?>

        <div class="location-info uk-flex">
            <div class="pin">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="30" viewBox="0 0 20 30" >
                <path d="M17.0739 3.06602C13.1681 -1.02201 6.83423 -1.02201 2.92848 3.06602C-0.38103 6.53 -0.951339 11.9385 1.55456 16.0808L9.99685 30L18.4391 16.0808C20.9537 11.9385 20.3834 6.53 17.0652 3.06602H17.0739ZM10.0055 14.4709C7.96621 14.4709 6.31577 12.7434 6.31577 10.609C6.31577 8.47453 7.96621 6.74706 10.0055 6.74706C12.0448 6.74706 13.6952 8.47453 13.6952 10.609C13.6952 12.7434 12.0448 14.4709 10.0055 14.4709Z" >
                </svg>
            </div>
            <div class="address">

            <?php  $venueTitle = $venue->post_title; ?>

                <div class="location-title"><?php echo $venueTitle; ?></div>


                <?php if($showAddress){ ?>
                    <?php $venueAddress =  get_field('address',$venue->ID); ?>
                    <div class="street"><?php echo $venueAddress; ?></div>

                <?php } ?>

                <?php if($additional_location_info>''){ ?>
                  
                    <div class="additional-location-info"><?php echo $additional_location_info; ?></div>

                <?php } ?>

                
                
            
        
            
            </div>
        </div>

<?php } 

}


function draw_venue_card($vid) { ?>

<div class="venue-card">

<div class="inner">

<?php if(get_field('venue_website',$vid)){ ?>
    <a href="<?php echo get_field('venue_website',$vid); ?>" target="_blank">
<?php } ?>

<div class="image-wrap">

<?php $attr = array('alt'=>get_the_title($vid)); ?>
<?php echo get_the_post_thumbnail($vid,'medium',$attr ); ?>
</div>

<?php if(get_field('venue_website',$vid)){ ?>
    </a>
<?php } ?>

<?php if(get_field('venue_website',$vid)){ ?>
    <a href="<?php echo get_field('venue_website',$vid); ?>" target="_blank">
<?php } ?>
<h3><?php echo get_the_title($vid); ?></h3>
<?php if(get_field('venue_website',$vid)){ ?>
    </a>
<?php } ?>

<?php if(get_field('address',$vid)){ ?>
<?php echo get_field('address',$vid); ?>
<?php } ?>

</div>

</div>


<?php }


