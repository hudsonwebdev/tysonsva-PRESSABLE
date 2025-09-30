<?php

function get_date_display($eid) {
    if (get_field('date_display', $eid)) {
        $date_display = get_field('date_display', $eid);
      
    } else {
        $event = em_get_event($eid, 'post_id');
  

        $event_start_date = get_post_meta($eid, '_event_start_date', true); 
        $event_end_date = get_post_meta($eid, '_event_end_date', true); 



        $datestring = strtotime($event_start_date);
        $datestringend = strtotime($event_end_date);




        $event_start_date_year = date('Y', $datestring);
        $event_end_date_year = date('Y', $datestringend);



        $event_start_date = date('M d, Y', $datestring);
        $event_end_date = date('M d, Y', $datestringend);

        
        $date_display = "";

   

        if ($event_end_date != $event_start_date) { 


            if($event_start_date_year == $event_end_date_year){

                $event_start_date = date('M d', $datestring);

            }

            $date_display = $event_start_date;

            $date_display .= " - " . $event_end_date;
        }else{

            $date_display = $event_start_date;

        }

        $event_start_time = trim(get_post_meta($eid, '_event_start_time', true)); 
        $event_end_time = trim(get_post_meta($eid, '_event_end_time', true)); 

        // Format start time
        if ($event_start_time) {
            $time = DateTime::createFromFormat('H:i:s', $event_start_time);
            if ($time !== false) {
                $date_display .= " " . $time->format('g:i A');
            } else {
                $date_display .= " invalid start time";
            }
        }

        // Format end time if it's different from start time
        if ($event_end_time && $event_end_time != $event_start_time) {
            $end_time = DateTime::createFromFormat('H:i:s', $event_end_time);
            if ($end_time !== false) {
                $date_display .= " - " . $end_time->format('g:i A');
            } else {
                $date_display .= " invalid end time";
            }
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


function draw_bio_card($pid,$type=1) { ?>


    <div class="flex-item">
        <div class="bio-card">
            <div class="inner">
            <?php $image_id = get_post_thumbnail_id($pid); ?>


            <?php if($type == 1){ ?>
                <a href="<?php echo get_the_permalink($pid); ?>" class="bio-pic">
                    <img <?php awesome_acf_responsive_image($image_id,'large','1024px',get_the_title($pid)); ?>  />
                </a>
                <h3><a href="<?php echo get_the_permalink($pid); ?>"><?php echo get_the_title($pid); ?></a></h3>
            
            <?php }else{  ?>
                <a uk-toggle="target: #modal-id-<?php echo $pid; ?>" role="button" type="button"  class="bio-pic">
                 <img <?php awesome_acf_responsive_image($image_id,'large','1024px',get_the_title($pid)); ?>  />
                </a>
                <h3><a uk-toggle="target: #modal-id-<?php echo $pid; ?>" role="button" type="button" ><?php echo get_the_title($pid); ?></a></h3>
                <div id="modal-id-<?php echo $pid; ?>" class="uk-flex-top uk-modal-container bio-modal" uk-modal>
                    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical" uk-overflow-auto >
                        <button class="uk-modal-close" type="button"><span uk-icon="close"></span></button>
                        <div class="uk-child-width-1-2@s" uk-grid>
                            <div>
                                <div class="modal-image"><img <?php awesome_acf_responsive_image($image_id,'large','1024px',get_the_title($pid)); ?>  /></div>
                            </div>
                            <div>  
                                <h2 class="uk-modal-title"><?php echo get_the_title($pid); ?></h2>
                                <div><strong><?php echo get_field('job_title',$pid); ?></strong></div>
                                <?php drawCompany($pid); ?>
                                <br><br>
                                <div class="modal-bio" ><?php echo get_the_content(null,false,$pid); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
             <?php }  ?>    
                
                <p><?php echo get_field('job_title',$pid); ?></p>
                <?php drawCompany($pid); ?>
            </div>
        </div>
        
    </div>

<?php }


function drawCompany($pid){
    
    if(get_field('company_website',$pid)){ ?>
    <a href="<?php echo get_field('company_website',$pid); ?>" target="_blank">
    <?php } ?>
    <?php if(get_field('company_name',$pid)){ ?>
    <?php echo get_field('company_name',$pid); ?>
    <?php } ?>
    <?php if(get_field('company_website',$pid)){ ?>
    </a>
    <?php } 
 } 



function drawByPostType($pid,$columns,$count){



    $posttype = get_post_type($pid);

 

    switch($posttype){

        case "post":
            draw_news_card_grid($pid,$columns);
        break;
        default:
            draw_event_card($pid,$columns);
        break;
       



        

    }

   

}