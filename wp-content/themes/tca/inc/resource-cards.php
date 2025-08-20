<?php

function draw_resource_card($rid,$count) { ?>

   <?php

            $url = get_field('url',$rid);
       
            $resource_cover_image = get_field('resource_cover_image',$rid);

            if($resource_cover_image){
                 
                 $image_id = $resource_cover_image['id'];

            }else{

                $image_id = get_post_thumbnail_id($rid);

            }
           
      
            $resource_type = get_field('resource_type',$rid);

            if ($resource_type == 2) {
                $link = $url;
                $target= "_blank";
            }else{ 
                $link = get_the_permalink($rid);
                $target = "_self";
            }

    ?>
    <div class="flex-item ">
        <div class="inner">
        <div class="resource-card">
            <div class="outerblue">
                    <div class="outer-card-chevron"><?php drawSVG('outer-chevron-up-card'); ?></div>
            </div> 
            <div class="inner-wrap">

      
        
        
                <a href="<?php echo $link; ?>" target="<?php echo $target; ?>">
                    <div class="cover-image">
                        <img <?php awesome_acf_responsive_image($image_id,'large','768px',get_the_title($rid)); ?>  />
                    </div>
                </a>
            </div>
        </div>
    </div>
    </div>
   
<?php }

function draw_resource_list($rid,$count) {

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