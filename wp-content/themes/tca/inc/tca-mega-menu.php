<?php 
function drawMegaMenu(){ ?>
<div class="menu-search">
    <div class="inner">
    <button class="mobile-hide-search"  aria-label="Close Search"><span uk-icon="icon: close"></button>
    <?php echo get_search_form(); ?>
    </div>
</div>
<?php
    if( have_rows('menu_section','option') ): ?>
        <div class="menu-section">
            <div class="mobile-menu-header">
                <div class="mobile-logo">
                    <a href="<?php echo site_url(); ?>"><?php drawSVG('tcalogo'); ?></a>
                </div>
                <div class="mobile-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                    <path d="M1 1L20.7405 20.7405" stroke="white" stroke-width="2" stroke-miterlimit="10"/>
                    <path d="M1 21.0002L20.7405 1.25973" stroke="white" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
            </div>
            <ul>
        <?php 
        while( have_rows('menu_section','option') ): the_row();

                $top_level_link = get_sub_field('top_level_link'); 
                $cta_type = get_sub_field('cta_type');
                $cta_image = get_sub_field('cta_image');
                $cta_link = get_sub_field('cta_link');
                $cta_post = get_sub_field('cta_post');
           ?>

           <?php 
             if( $top_level_link): 
            $link_url = $top_level_link['url'];
            $link_title = $top_level_link['title'];
            $link_target = $top_level_link['target'] ? $link['target'] : '_self';
            ?>
            <li class="top-level">
                <a href="#" target=""><span><?php echo esc_html( $link_title ); ?> <?php drawSVG('chevron-down'); ?></span>
                <span class="mobile-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                        <path d="M6.27666 0.808887L0.627852 0.777504L7.18323 7.33289L0.555421 13.815L6.20398 13.8467L12.832 7.36427L6.27666 0.808887Z" fill="white"/>
                    </svg>
                </span>
            </a>
            <?php endif; ?>
             <div class="sub-menu-area">   
                <div class="uk-container">

                

             <?php $sub_menu_title = get_sub_field('sub_menu_title'); ?>
             <div class="sub-menu-header">   
                <div class="arrow-left">
             <svg xmlns="http://www.w3.org/2000/svg" width="13" height="22" viewBox="0 0 13 22" fill="none">
                <path d="M12 21L2 11L12 1" stroke="white" stroke-width="2" stroke-miterlimit="10"/>
                </svg>
             </div>
                <h2 class="menu-section-title"><?php echo $sub_menu_title; ?></h2>
                <div class="mobile-sub-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                    <path d="M1 1L20.7405 20.7405" stroke="white" stroke-width="2" stroke-miterlimit="10"/>
                    <path d="M1 21.0002L20.7405 1.25973" stroke="white" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
            </div>


            <div class="section-row">

            

           <?php if ($cta_type == "Post"){ ?>

           <?php if($cta_post){ ?>
                <div class="menu-cta">
                    <div class="image">
                        <a href="<?php echo get_the_permalink($cta_post->ID); ?>"><?php echo get_the_post_thumbnail($cta_post->ID,'medium'); ?></a>
                    </div>
                    <div class="text">

                    <?php 
                    $type = get_post_type($cta_post); 
                    if($type=="tysons-event"){
                        $label = "Featured Event";

                    }elseif($type=="post"){
                        $label = "Featured News";

                    }else{
                        $label = "Featured";
                        
                    }?>
                        <div class="card-header">
                            <div class="card-type"><?php echo $label; ?></div>
                            <div class="card-date"></div>
                        </div>
                        <h4><a href="<?php echo get_the_permalink($cta_post->ID); ?>"><?php echo $cta_post->post_title; ?></a></h4>

                        <?php if(get_sub_field('cta_text_override')){ ?>
                            <div class="excerpt"><?php echo get_sub_field('cta_text_override'); ?></div>
                        <?php }else{ ?>
                            <div class="excerpt"><?php echo get_the_excerpt($cta_post->ID); ?></div>
                        <?php } ?>
                        
                        
                    </div>
                </div>
            <?php } ?>
            
           <?php }else{ ?>
            <div class="image-only">
                <a href="<?php echo $cta_link; ?>"><img class="menu-image" <?php awesome_acf_responsive_image($cta_image['id'],'thumb-640','640px',$cta_image['alt']); ?>   /></a>
            </div>
           <?php } ?>
           

           <div class="menu-list-section">
                <?php   if( have_rows('link_lists') ): ?>
                    
                    <?php  while( have_rows('link_lists') ): the_row(); ?>
                    <div class="menu-list">
                        <ul>
                            <?php  while( have_rows('link_list') ): the_row(); ?>
                            <?php  $link = get_sub_field('link'); ?>
                            <?php 
                            if( $link ): 
                                $link_url = $link['url'];
                                $link_title = $link['title'];
                                $link_target = $link['target'] ? $link['target'] : '_self';
                                ?>
                                <li>
                                    <a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                                    
                                </li>
                            <?php endif; ?>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                    <?php endwhile; ?>
                   
                <?php endif;  ?>
            </div>    
            </div>
            </div>
          </div><!--row2-->  
        </li>    
                
        <?php endwhile; ?>
        <li class="search-link">
            <a href="#" class="show-search" role="button" aria-label="Show Search"><span class="mobile-text">Search</span> <span class="mo"><span uk-icon="icon: search"></span></span></a>
            <a href="#" class="hide-search" role="button" aria-label="Hide Search"><span uk-icon="icon: close"></span></a>
        </li>
        <li class="contact-link"><a href="/contact">Contact</a></li>
        
        </ul>
        
        </div>
    <?php endif; ?>
<?php }


