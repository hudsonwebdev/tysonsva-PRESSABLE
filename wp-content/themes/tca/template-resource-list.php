<?php
/*
Template Name: Resources List
*/

get_header(); 

the_content(); ?>

<div class="uk-container">
<div class="tca-resources" uk-scrollspy="target: .resource-item; cls: uk-animation-slide-bottom-medium; delay: 200;repeat:true;">

    <?php
    // Query the 'resources' custom post type
    $args = array(
        'post_type' => 'resource',
        'posts_per_page' => -1, // Show all resources
    );
    
   $resources_query = get_field('resource_picker');
     if ($resources_query) :
   
        foreach($resources_query as $rid){
            
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

            <div class="resource-item">

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
        

        <?php } ?>
        
    <?php endif; ?>
    


    <?php wp_reset_postdata(); ?>
</div>
</div>
<?php get_footer(); ?>
