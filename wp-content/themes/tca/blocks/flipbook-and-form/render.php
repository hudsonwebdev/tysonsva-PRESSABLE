<?php 
if ( render_block_preview_if_applicable( $block ) ) return;
$container_settings = get_field('container_settings');
$section_header = get_field('section_header');
include __DIR__ .'/../../inc/common_block_variables.php';

openSection(
    $wrap_size,
    $container_size,
    $anchor,
    $class_name,
    $container_type,
    $background_color,
    $background_image,
    $text_color,
    $disable_animation,
    $vertical_pad_top,
    $vertical_pad_bottom
    );
 

  


drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style);

?>
<div class="flipbook-form">
<?php
        $flipbook_position = get_field('flipbook_position')?get_field('flipbook_position'):'left';
        $flipbook_shortcode = get_field('flipbook_shortcode');
        $pdf_file = get_field('pdf');
        
?>
<div class="uk-container">
    <div class="flipbook-form-wrap">
    <?php if($flipbook_position == 'left'){ ?>
            <div class="thumbside">
            <?php echo $flipbook_shortcode; ?>
            </div>
       
        <?php } ?>
<?php
 $align_description = get_field('align_description')?get_field('align_description'):'flex-start';

 ?>

        <div class="textside" style="justify-content:<?php echo $align_description; ?>">
            <div>
            <?php
            $description = get_field('description');
            echo $description;

           
            ?>

            <?php 

            $access_forms = get_field('access_forms');

           $mailchimp_tags = get_field( 'mailchimp_tags' );



            $mailchimp_tags = array_map(
                static fn( $term ) => $term->name,
                get_field('mailchimp_tags') ?: array()
            );


            $mc_tags = tca_sanitize_mailchimp_tags( $mailchimp_tags );

            
            $form_button_color = get_field('form_button_color')?get_field('form_button_color'):'blue';

            $download = false;
            $mail = false;

            switch($access_forms){


                case 'Download':

                    $download = true;
                    

                break;

                case 'Mail':

                    $mail = true;

                break;


                case 'Download & Mail':

                    $download = true;
                    $mail = true;

                break;

            }

            ?>

            <?php if($download && $pdf_file){ ?>

                <button uk-toggle="target: #dl-form" type="button" class="tca-button <?php echo $form_button_color; ?>">Download PDF</button>

                <div id="dl-form" uk-modal>
                    <div class="uk-modal-dialog uk-modal-body uk-modal-dialog uk-margin-auto-vertical">
                        <h2 class="uk-modal-title">Download PDF</h2>
                        <div class="dl-form">
                        <?php

          
                    
                       $values = array(
                            'pdfurl'  => $pdf_file['url'],
                            'mc_tags' => implode( ',', $mc_tags ),
                        );
                        gravity_form( 7, false, false, false, $values, true, 0, true );
                        ?>
                        </div>
                    </div>
                </div>

            <?php } ?>


            <?php if($mail && $pdf_file){ ?>

                <button uk-toggle="target: #mail-form" type="button" class="tca-button <?php echo $form_button_color; ?>">Mail Me A Copy</button>

                <div id="mail-form" uk-modal>
                    <div class="uk-modal-dialog uk-modal-body uk-modal-dialog uk-margin-auto-vertical">
                        <h2 class="uk-modal-title">Mail Me a Copy</h2>
                        <div class="dl-form">
                        <?php
                       
                       
                        $values = array(
                            'pdfurl'  => $pdf_file['url'],
                            'mc_tags' => implode( ',', $mc_tags ),
                        );
                        gravity_form( 13, false, false, false, $values, true, 0, true );
                      ?>
                        </div>
                    </div>
                </div>

            <?php } ?>

            </div>
        </div>
        <?php if($flipbook_position == 'right'){ ?>
            <div class="thumbside">
            <?php echo $flipbook_shortcode; ?>
            </div>
        <?php } ?>
    </div>
</div>
</div>
<?php


closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);


