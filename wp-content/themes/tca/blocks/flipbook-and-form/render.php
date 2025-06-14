<?php 
$container_settings = get_field('container_settings');
$section_header = get_field('section_header');
include __DIR__ .'/../../inc/common_block_variables.php';
?>
<section class="flipbook-form">





<div class="uk-container">
    <div class="flipbook-form-wrap">
        <div class="thumbside">

        <?php
        $flipbook_shortcode = get_field('flipbook_shortcode');
        $pdf_file = get_field('pdf');
        echo $flipbook_shortcode;
        ?><br>
        <p>View Online</p>
        </div>


        <div class="textside">
            <?php
            $form_intro = get_field('form_intro');
            echo $form_intro;
            ?>

            <button uk-toggle="target: #my-id" type="button">Download PDF</button>

            <div id="my-id" uk-modal>
                <div class="uk-modal-dialog uk-modal-body uk-modal-dialog uk-margin-auto-vertical">
                    <h2 class="uk-modal-title"></h2>
                    <div class="dl-form">
                    <?php
                    $gravity_form_id = get_field('gravity_form_id');
                    if($gravity_form_id>0 && $pdf_file>""){
                    $values = array('pdfurl'=>$pdf_file['url']);
                    gravity_form( $gravity_form_id, false, false, false, $values, true, 0, true );
                    } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</section>



