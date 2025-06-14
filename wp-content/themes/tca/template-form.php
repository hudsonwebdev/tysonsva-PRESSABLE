<?php
/**
 * Template Name: Form Page
 */

get_header();



?>
<div class="form-backer">
    <svg xmlns="http://www.w3.org/2000/svg" width="699" height="1126" viewBox="0 0 699 1126" fill="none">
    <path d="M-23 516.388V1126L699 405.612V-204L-23 516.388Z" fill="#9ADCF7" fill-opacity="0.35"/>
    </svg>
</div>
<div class="form-page">

	<main id="primary" class="site-main">
        
        <div class="form-section">
            <div class="uk-container">
			<?php
			while ( have_posts() ) :
				the_post();

				$page_title = get_field('page_title'); 
                $page_intro = get_field('page_intro'); 
                $form_guidelines = get_field('form_guidelines'); 
                $form_id = get_field('form_id'); 

                ?>

                <h1 class="form-title"><?php echo $page_title; ?></h1>
                <?php echo $page_intro; ?>
                <hr class="bluehr">
                <div class="form-layout">
                <?php if($form_guidelines>""){ ?>
                    <div class="guidelines">

                    <?php echo $form_guidelines; ?>
                    </div>
                    <?php } ?>
                    <div class="form-side">
                            <?php if($form_id ){ ?>
                            <?php  echo gravity_form( $form_id , false, false, false, false, true, false, false ); ?>
                            <?php } ?>

                    </div>
                </div>

			<?php 
            endwhile; // End of the loop.
			?>
            </div>
        </div>
      
		
	</main><!-- #main -->
</div>
<?php

get_footer();
