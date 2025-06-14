<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package tca
 */

get_header();
?>

	<main id="primary" class="site-main event-page">
<?php
		while ( have_posts() ) :
			the_post(); ?>

        <div class="backing-svg"><svg xmlns="http://www.w3.org/2000/svg" width="699" height="1126" viewBox="0 0 699 1126" fill="none">
            <path d="M-23 516.388V1126L699 405.612V-204L-23 516.388Z" fill="#9ADCF7" fill-opacity="0.35"/>
            </svg>
        </div>

        <div class="uk-container">
            
        <a href="/events" class="back-to-events"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18" fill="none">
                        <path d="M10 16L3 8.99577L10 2"  stroke-width="3" stroke-miterlimit="10"/>
                        </svg> Back to Events</a>

                        
            <div class="event-header">

            



                <div class="event-info">
                    
                    <div class="inner">

                        <?php $additional_card_info = get_field('additional_card_info'); ?>
                            <div class="card-header">
                                <div class="card-type">Event</div>
                                <div class="card-date"><?php echo get_date_display(get_the_ID()); ?></div>
                            </div>
                            
                        <div class="card-title">
                            <h1><?php echo get_the_title(); ?></h1>
                        </div>
                        

                        <?php 
                        
                        $additional_location_info = get_field('additional_location_info');
                        $pid = get_the_ID();
                        printVenu($pid,true,$additional_location_info);

                        ?>
                        <?php if($additional_card_info>""){ ?>
                                <div class="additional-card-info">
                                    <?php echo $additional_card_info; ?>
                                </div>
                            <?php } ?>
                        
                       <?php 
                        $ticket_link = get_field('ticket_link');
                        $external_url = get_field('external_url');
                        

                        if($ticket_link>""){ ?> <a href="<?php echo $ticket_link; ?>" class="get-tickets"  target="_blank">Get Tickets</a><?php } ?>
                        

                        <?php if($external_url>""){ ?>
                            <a href="<?php echo $external_url; ?>" class="learn-more" target="_blank"> <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path d="M6.63356 4.13525H1.93701V17.0632H14.8588V12.3605" stroke="white" stroke-width="3" stroke-miterlimit="10"/>
                            <path d="M8.51318 1.93677H17.0631V10.4867" stroke="white" stroke-width="3" stroke-miterlimit="10"/>
                            <path d="M16.6509 2.35522L6.5708 12.4353" stroke="white" stroke-width="3" stroke-miterlimit="10"/>
                            </svg> <span>Learn More</span></a>
                        <?php } ?>    

                    </div>
                      
                </div>
            
                <div class="featured-image">
                    <div class="aspect-ratio-container">
                            <?php  the_post_thumbnail(); ?>
                    </div>
                </div>
            </div>
           
        </div>

        <div class="uk-container uk-container-small">    
            <div class="event-content">
                <?php $event_description = get_field('event_description'); ?>
                <?php if($event_description>""){ ?>

                    <?php echo $event_description; ?>


                <?php } ?>
                <hr>
                <div class="share">
                <?php drawSocialShare(get_the_title(),get_the_permalink()); ?>
                </div>
            </div>
		</div>
      
        <div class="uk-container  deco-wrap uk-position-relative">
            <div class="section-decoration"><?php drawSVG('section-graphic-1'); ?></div>
        </div>        




        <?php endwhile;?>
  
	</main><!-- #main -->

<?php

get_footer();
