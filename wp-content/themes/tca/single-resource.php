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

        $mailchimp_tags = array_map(
            static fn( $term ) => $term->name,
            get_field( 'mailchimp_tags', $rid ) ?: array()
        );
        $mc_tags = tca_sanitize_mailchimp_tags( $mailchimp_tags );

        $gf_field_values = array(
            'pdfurl'  => ! empty( $file['url'] ) ? $file['url'] : '',
            'mc_tags' => implode( ',', $mc_tags ),
        );


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
                 <?php  if($resource_type==1){
                    $url = $file['url'];
                 }else{
                    $url = $url;
                 } ?>
                <a href="<?php echo $url; ?>" class="tca-button blue" role="button" type="button" target="_blank">Download PDF</a>

            <?php } ?>
           

            <div id="download-resource" uk-modal>
                <div class="uk-modal-dialog uk-modal-body uk-modal-dialog uk-margin-auto-vertical">
                    <h2 class="uk-modal-title">Confirm Download</h2>
                    <div class="dl-form">
                    <?php
                
                    if ( $form_id > 0 && ! empty( $file ) && function_exists( 'gravity_form' ) ) {
                        gravity_form( $form_id, false, false, false, $gf_field_values, true, 0, true );
                    } ?>
                    </div>
                </div>
            </div>

           
        </div>
    </div>

</div>

</section>
<section>
    <div class="additional-info">
        <div class="uk-container">
            <?php
            $additional_info = get_field( 'additional_resource_info' );
            if ( $additional_info ) {
                echo apply_filters( 'the_content', $additional_info );
            }

            if ( get_field( 'show_order_print_form' ) && function_exists( 'gravity_form' ) ) {
                $order_print_form_id = (int) get_field( 'order_print_form_id' );
                if ( $order_print_form_id > 0 ) {

                    echo "<h2>Mail Me A Copy</h2>";
                    gravity_form( $order_print_form_id, false, false, false, $gf_field_values, true, 0, true );
                }
            }
            ?>
        </div>
    </div>
</section>

			
		<?php endwhile; ?>
		
	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();
