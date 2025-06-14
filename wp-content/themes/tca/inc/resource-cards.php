<?php

function draw_resource_card_grid($pid,$columns=1) {

$img_id = get_post_thumbnail_id($pid);

$title = get_the_title($pid);

$url = get_the_permalink($pid);
$target = "_self";



?>
<div class="flex-item  columns-<?php echo $columns; ?>">

    <div class="event-card ">

        <div class="outerblue">
            <div class="outer-card-chevron"><?php drawSVG('outer-chevron-up-card'); ?></div>
        </div> 
        <div class="inner-wrap">

            <div class="chev-up"><?php drawSVG('chevron-up-card'); ?></div>

            <div class="innerblue"></div>

                <div class="single-stack">

                    <div class="card-image">

                    <?php if($img_id>0){ ?>

                        <?php if($columns == 2){ ?>
                            <img <?php awesome_acf_responsive_image($img_id,'featured-image','1378px',$title); ?> />
                        <?php }else{ ?>
                            <img <?php awesome_acf_responsive_image($img_id,'large','768px',$title); ?> />
                        <?php } ?>


                        <?php } ?>
                    </div>
                    
                    
                    
                    <div class="tca-card-info">
                        <div class="inner">
                        <div class="card-header">
                                <div class="card-type">Resource</div>
                                <div class="card-date"></div>
                            </div>
                            <div class="card-title">
                                <h4><a href="<?php echo $url; ?>" target="<?php echo $target; ?>"><?php echo max_title_length( $title ); ?></a></h4>
                               
                    
                                
                            </div>

                            
                        </div>
                    </div>

                </div>

         </div>
    </div>
    </div>
<?php
}




function draw_resource_card($rid,$count) {

    if($count%2==0){
        $bgclass = "even-color";
    }else{
        $bgclass = "odd-color";
    }


            $file = get_field('file',$rid);
            $url = get_field('url',$rid);
            $form_id = get_field('form_id',$rid);
            $description = get_field('description',$rid);
            $resource_type = get_field('resource_type',$rid);
            $flipbook_id = get_field('flipbook_id',$rid);

            if ($resource_type == 2) {
                $link = $url;
                $target= "_blank";
            }else{ 
                $link = get_the_permalink($rid);
                $target = "_self";
            }
            ?>

            <div class="resource-item <?php echo $bgclass; ?>">

                <div class="image-side">
                    <a href="<?php echo $link; ?>" target="<?php echo $target; ?>"><?php echo get_the_post_thumbnail($rid); ?></a>
                </div>

                <div class="text-side">
                    <h2><?php echo get_the_title($rid); ?></h2>
                    <?php if ($description) : ?>
                        <div class="description">
                            <?php echo $description; ?>
                        </div>
                    <?php endif; ?>

                    <a href="<?php echo $link; ?>" target="<?php echo $target; ?>" class="tca-button blue">View</a>

                </div>
            </div>

<?php }