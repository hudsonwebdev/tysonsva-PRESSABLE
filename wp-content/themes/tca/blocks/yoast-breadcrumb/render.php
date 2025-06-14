<div class="uk-container">
	<?php
	if(!is_front_page()){ 
		if ( function_exists('yoast_breadcrumb') ) {
		yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
		}
	}
?>
</div>