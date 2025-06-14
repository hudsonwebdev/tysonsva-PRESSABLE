<?php 

function draw_no_image_banner($title){ ?>

<div class="page-header">
<div class="uk-container">
	<?php
    if(is_page('news')){  ?>
        <div id="breadcrumbs">News & Events / News</div>
		<?php 
        
	}elseif(!is_front_page()){ 
		if ( function_exists('yoast_breadcrumb') ) {
		yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
		}
	}
?>
</div>
        <div class="uk-container">
            <div class="page-header-text">
                <h1><?php echo $title; ?></h1>
            </div>
        </div>
        <div class="page-header-graphic">
            <svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 805 932">
            <defs>
                <style>
                .news0 {
                    fill: #260d66;
                }

                .news1 {
                    fill: #00b852;
                }

                .news2 {
                    fill: #9adcf7;
                }

                @media(max-width:768px){
                    .news1{
                        display:none;
                    }
                }
                </style>
            </defs>
            <path class="news1" d="M0,262v174l174-174H0Z"/>
            <path class="news0" d="M271.4,0l-130.4,131h359.6L631,0h-359.6Z"/>
            <path class="news0" d="M10,623.2v304.8l360-360.2v-304.8L10,623.2Z"/>
            <path class="news2" d="M500.5,132l-130.5,130.5h304.5v304.5l130.5-130.5V132h-304.5Z"/>
            </svg>
        </div>
    </div>

    <?php
}