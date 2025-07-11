<?php

add_filter('em_event_output_placeholder','custom_placeholders',1,3);
function custom_placeholders($replace, $EM_Event, $result){

switch($result){


    case '#_DRAWSHARELINKS':

        ob_start();
		$rand_id = rand();

        $id = $EM_Event->ID;
		$url = get_the_permalink($id);
		$title = get_the_title($id);

	?>

    <h4 class="share-title">Share This:</h4>

    <div class="social">
   
    <a href="https://www.facebook.com/sharer/sharer.php?u=' . $url . '" uk-icon="icon: facebook"></a>
    
    <a href="https://x.com/intent/tweet?url=' . $url . '" uk-icon="icon: x"></a>
     
    <a href="https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '" uk-icon="icon: linkedin"></a>

    <a href="mailto:?subject=' . $title . '&body=' . $url . '" uk-icon="icon: mail"></a>

       
    </div>

    <?php
    $replace = ob_get_clean();

    break;


    case '#_EVENTADDTOCALENDARTYSONS':

        ob_start();
		$rand_id = rand();
		?>
		<button type="button" class="em-event-add-to-calendar em-tooltip-ddm em-clickable input" data-button-width="match" data-tooltip-class="em-add-to-calendar-tooltip" data-content="em-event-add-to-colendar-content-<?php echo $rand_id; ?>"><span class="em-icon em-icon-calendar"></span> <?php esc_html_e('Add To Calendar', 'events-manager'); ?></button>
		<div class="em-tooltip-ddm-content em-event-add-to-calendar-content" id="em-event-add-to-colendar-content-<?php echo $rand_id; ?>">
			<a class="em-a2c-download" href="<?php echo esc_url($EM_Event->get_ical_url()); ?>" target="_blank"><?php echo sprintf(esc_html__('Download %s', 'events-manager'), 'ICS'); ?></a>
			<a class="em-a2c-google" href="<?php echo esc_url($EM_Event->output('#_EVENTGCALURL')); ?>" target="_blank"><?php esc_html_e('Google Calendar', 'events-manager'); ?></a>
			<a class="em-a2c-apple" href="<?php echo esc_url(str_replace(array('http://','https://'), 'webcal://', $EM_Event->get_ical_url())); ?>" target="_blank">iCalendar</a>
			<a class="em-a2c-office" href="<?php echo esc_url($EM_Event->output('#_EVENTOFFICE365URL')); ?>" target="_blank">Office 365</a>
			<a class="em-a2c-outlook" href="<?php echo esc_url($EM_Event->output('#_EVENTOUTLOOKLIVEURL')); ?>" target="_blank">Outlook Live</a>
		</div>
		<?php
		$replace = ob_get_clean();

    break;



    case '#_EVENTLINKTYSONS':

        if(get_field('external_url',$EM_Event->ID)){

			$event_link = get_field('external_url',$EM_Event->ID);

		}else{

			$event_link = esc_url($EM_Event->get_permalink());

		}
		
		$replace = $event_link;

    break;


    case '#_LEARNMORETYSONS':

        if(get_field('external_url',$EM_Event->ID)){

			$external_ur = get_field('external_url',$EM_Event->ID);
            ob_start(); ?>
            <a href="<?php echo $external_url; ?>" class="learn-more" target="_blank"> <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path d="M6.63356 4.13525H1.93701V17.0632H14.8588V12.3605" stroke="white" stroke-width="3" stroke-miterlimit="10"/>
                            <path d="M8.51318 1.93677H17.0631V10.4867" stroke="white" stroke-width="3" stroke-miterlimit="10"/>
                            <path d="M16.6509 2.35522L6.5708 12.4353" stroke="white" stroke-width="3" stroke-miterlimit="10"/>
                            </svg> <span>Learn More</span></a>
           <?php $replace = ob_get_clean();

		}else{

            $replace = '';
            
        }
		
		

    break;

    case '#_EVENTTARGETTYSONS':  

        if(get_field('external_url',$EM_Event->ID)){

			$event_target = "_blank";

		}else{

			$event_target = "_self";

		}
		
		$replace = $event_target;

    break;

    case '#_TICKETLINKTYSONS': 

     if(get_field('ticket_link',$EM_Event->ID)){

        $ticket_link = get_field('ticket_link',$EM_Event->ID);

        $replace =  '<a href="' . $ticket_link . '" class="get-tickets"  target="_blank">Get Tickets</a>';
     }else{
        $replace = '';
     }
    break;               

    case '#_EVENTDATESTYSONS':
			if(get_field('date_display',$EM_Event->ID)){
				$replace = get_field('date_display',$EM_Event->ID);
			}else{
				$replace = $EM_Event->output_dates();
			}
			
	break;

    case '#_ADDITIONALCARDINFOTYSONS':

			if(get_field('additional_card_info',$EM_Event->ID)){
                $replace = '<div class="additional-card-info">';
				$replace .= get_field('additional_card_info',$EM_Event->ID);
                $replace .= '</div>';
			}else{
                $replace = "";
            }
			
	break;




}


    return $replace;


}


