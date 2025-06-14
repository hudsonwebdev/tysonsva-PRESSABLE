<?php

/**
 * Template Name: Download Thank You
 */



get_header();

?>

	<main id="primary" class="site-main">


			<?php
			while ( have_posts() ) :
				the_post();
				
			   
				the_content();

                $file = isset($_GET['file'])?$_GET['file']:'';
                ?>
                <div class="uk-container">
                    <div style="padding:10% 0;">

                <?php if($file > ""){ ?>

                    <h1>Thank you!</h1>
                    <p>You may download your resource now</p>
                   	 <a href="<?php echo $file; ?>" class="tca-button blue">Download Now</a>

                <?php } else{ ?>

                    Are you looking for a resource?  Please visit our <a href="/data-info/tysons-reports-plans/"> Reports & Plans</a> page.

                <?php } ?>


                    </div>
                </div>
                

                <?php endwhile; ?>
		
	</main><!-- #main -->

<?php

get_footer();
