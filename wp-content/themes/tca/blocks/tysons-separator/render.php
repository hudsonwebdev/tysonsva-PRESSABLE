<?php 
$separator_height = get_field('separator_height')?get_field('separator_height'):7;
$separator_color = get_field('separator_color')?get_field('separator_color'):'#B8D5EF';

?>
<hr style="height:<?php echo $separator_height; ?>px;display:block; background:<?php echo $separator_color; ?>;border:none;" />