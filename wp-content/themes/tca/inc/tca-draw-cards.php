<?php


function get_date_display($eid){

    if(get_field('date_display',$eid)){

        $date_display = get_field('date_display',$eid);


    }else{

        $event = em_get_event($eid, 'post_id');

        $event_date = get_post_meta($eid, '_event_start_date', true); 
        $event_end_date = get_post_meta($eid, '_event_end_date', true); 

        $datestring = strtotime($event_date);
        $datestringend = strtotime($event_end_date);

        $event_date = date('M d, Y', $datestring);
        $event_end_date = date('M d, Y', $datestringend);

        $date_display =  $event_date;

        if($event_end_date != $event_date){ 
            $date_display .= " - " . $event_end_date;
        } 

    }


    return $date_display;

}




function draw_datahub_card($label,$value,$small_text,$source) { ?>

    <div class="datahub-card">
        <div class="datahub-card-header">
            <div class="label"><?php echo $label; ?></div>
            <div class="lablecolor">&nbsp;</div>
        </div>
        <div class="inner">
        
            <div>
                
                <h3><?php echo $value; ?></h3>
                <p class="small-text"><?php echo $small_text; ?></p>
            </div>
            <div>
                <hr>
                <p class="source"><?php echo $source; ?></p>
            </div>
        </div>
    </div>

<?php }


function draw_bio_card($pid) { ?>
    <div class="flex-item">
        <div class="bio-card">
            <div class="inner">
                <a href="<?php echo get_the_permalink($pid); ?>" class="bio-pic">
                    <?php $image_id = get_post_thumbnail_id($pid); ?>
                    <img <?php awesome_acf_responsive_image($image_id,'large','1024px',get_the_title($pid)); ?>  />
                </a>
                <h3><a href="<?php echo get_the_permalink($pid); ?>"><?php echo get_the_title($pid); ?></a></h3>
                <p><?php echo get_field('job_title',$pid); ?></p>
                
            </div>
        </div>
    </div>

<?php }







function drawByPostType($pid,$columns,$count){

;

    $posttype = get_post_type($pid);

 

    switch($posttype){

        case "post":
            draw_news_card_grid($pid,$columns);
        break;
        case "tysons-event":
            draw_event_card($pid,$columns);
        break;

        case "resource":
            draw_resource_card_grid($pid,$columns);
        break;

    }

   

}