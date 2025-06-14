
<?php 

function draw_content_card($image,$content,$columns=1) {
   

   $img_id = $image['id'];


   



   ?>

   <div class="flex-item  columns-<?php echo $columns; ?>">

       <div class="grid-card">

           <div class="outerblue">
               <div class="outer-card-chevron"><?php drawSVG('outer-chevron-up-card'); ?></div>
           </div> 
           <div class="inner-wrap">

               <div class="chev-up"><?php drawSVG('chevron-up-card'); ?></div>

               <div class="innerblue"></div>

                   <div class="single-stack">

                       <div class="card-image">

                   

                           <?php if($columns == 2){ ?>
                               <img <?php awesome_acf_responsive_image($img_id,'featured-image','1378px',$image['alt']); ?>  />
                           <?php }else{ ?>
                               <img <?php awesome_acf_responsive_image($img_id,'large','768px',$image['alt']); ?>  />
                           <?php } ?>

                       </div>
                       
                       
                       
                       <div class="tca-card-info">
                           <div class="inner">
                               
                               <div class="card-content">
                              
                                  <?php echo $content; ?>

                               </div>
                               
                           </div>
                       </div>

                   </div>

            </div>
       </div>
       </div>
   
   <?php
}