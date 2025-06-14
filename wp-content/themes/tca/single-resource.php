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

	<main id="primary" class="site-main">
		
		<?php
		while ( have_posts() ) :
			the_post(); ?>

		





<div class="uk-container">
    <div class="resource-item">

        <div class="image-side uk-text-center">

        <?php


        $rid = get_the_ID();
        $file = get_field('file');
        $url = get_field('url')?get_field('url'):'#';
        $form_id = get_field('form_id');
        $description = get_field('description');
        $resource_type = get_field('resource_type');
        $flipbook_shortcode  = get_field('flipbook_shortcode');

        if($flipbook_shortcode>""){

            echo do_shortcode($flipbook_shortcode); ?>
            <div class="small-text">Click To View In Browser</div>

        <?php }elseif($file){

            echo  do_shortcode('[dflip source="'.$file['url'].'" type="thumb"][/dflip]'); 

        }else{ ?>

            <a href="<?php echo $url; ?>" target="_blank"><?php echo get_the_post_thumbnail(); ?></a>

        <?php } ?><br>
        
        </div>


        <div class="text-side">
                
            <h1><?php  echo get_the_title($rid); ?></h1>
            <?php echo $description;?>
            <?php if(get_field('require_contact_info')){ ?>

                <a class="tca-button blue" role="button" uk-toggle="target: #download-resource" type="button">Download PDF</a>
            
            <?php }else{ ?>

                <a href="<?php echo $url; ?>" class="tca-button blue" role="button" type="button">Download PDF</a>

            <?php } ?>
           

            <div id="download-resource" uk-modal>
                <div class="uk-modal-dialog uk-modal-body uk-modal-dialog uk-margin-auto-vertical">
                    <h2 class="uk-modal-title">Confirm Download</h2>
                    <div class="dl-form">
                    <?php
                
                    if($form_id>0 && $file>""){
                    $values = array('pdfurl'=>$file['url']);
                    gravity_form($form_id, false, false, false, $values, true, 0, true );
                    } ?>
                    </div>
                </div>
            </div>

            <?php if(get_field('additional_resource_info')){ 
                echo get_field('additional_resource_info');

            } ?>
        </div>
    </div>
</div>

</section>



			
		<?php endwhile; ?>
		
	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();
