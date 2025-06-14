<?php


function set_editor_font_sizes()
{
    add_theme_support('editor-font-sizes', array(
        array(
            'name' => __('Small', 'your-textdomain'),
            'size' => 14,
            'slug' => 'small'
        ),
        array(
            'name' => __('Medium', 'your-textdomain'),
            'size' => 24,
            'slug' => 'medium'
        ),
        array(
            'name' => __('Large', 'your-textdomain'),
            'size' => 44,
            'slug' => 'large'
        ),
        array(
            'name' => __('Huge', 'your-textdomain'),
            'size' => 105,
            'slug' => 'bonkers'
        )
    ));
}

/**
 * Hook: after_setup_theme.
 *
 * @uses after_setup_theme https://developer.wordpress.org/reference/hooks/after_setup_theme/
 * @uses add_action() https://developer.wordpress.org/reference/functions/add_action/
 */

// Enable font size, font family, and line height select fields in WYSIWYG editors
if ( ! function_exists( 'tssupp_add_mce_font_buttons' ) ) {
    function tssupp_add_mce_font_buttons( $buttons ) {
       // array_unshift( $buttons, 'fontsizeselect' ); // Add Font Size Select
        array_unshift( $buttons, 'fontselect' ); // Add Font Family Select
       // array_unshift( $buttons, 'lineheightselect' ); // Add Line Height Select (Custom)
        return $buttons;
    }
}
add_filter( 'mce_buttons_2', 'tssupp_add_mce_font_buttons' );

// Add custom font formats, font sizes, and line height to the editor
if ( ! function_exists( 'tssupp_add_mce_font_formats' ) ) {
    function tssupp_add_mce_font_formats( $init_array ) {
        // Define available font sizes for the editor
        $init_array['fontsize_formats'] = implode(' ', generateFontSizeArray(8, 180, 2));
        // Define the available fonts for the editor (can add more fonts as needed)
        $fontThin = 'silkathin, Arial, Helvetica, sans-serif';
        $fontRegular = 'silkaregular, Arial, Helvetica, sans-serif';
        $fontMedium = 'silkamedium, Arial, Helvetica, sans-serif';
        $fontSemiBold = 'silkasemibold, Arial, Helvetica, sans-serif';
        $fontBold = 'silkabold, Arial, Helvetica, sans-serif';
        $fontBlack = 'silkablack, Arial, Helvetica, sans-serif';

        // Combine the font formats into a single string
        $fontString = 'Silka Thin=' . $fontThin . ';';
        $fontString .= 'Silka Regular=' . $fontRegular . ';';
        $fontString .= 'Silka Medium=' . $fontMedium . ';';
        $fontString .= 'Silka Semi Bold=' . $fontSemiBold . ';';
        $fontString .= 'Silka Bold=' . $fontBold . ';';
        $fontString .= 'Silka Black=' . $fontBlack . ';';

        // Assign custom font formats to TinyMCE
        $init_array['font_formats'] = $fontString;

        // Add line height options (adjustable)
        $init_array['lineheight_formats'] = '1=1;1.2=1.2;1.5=1.5;1.8=1.8;2=2;2.5=2.5'; // Customize line heights
       


        return $init_array;
    }
}
add_filter( 'tiny_mce_before_init', 'tssupp_add_mce_font_formats' );


// Add custom TinyMCE plugin for line height dropdown
if ( ! function_exists( 'tssupp_add_lineheight_plugin' ) ) {
    function tssupp_add_lineheight_plugin( $plugins ) {
        // Adding a custom plugin for line height
        $plugins['lineheight'] = get_template_directory_uri() . '/js/tinymce.js';
        return $plugins;
    }
}
//add_filter( 'mce_external_plugins', 'tssupp_add_lineheight_plugin' );


function generateFontSizeArray($min, $max, $increment) {
    $fontSizes = [];
    
    // Loop from min to max with the given increment
    for ($i = $min; $i <= $max; $i += $increment) {
        $fontSizes[] = $i . 'px';
    }

    return $fontSizes;
}




function add_style_select_buttons( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}
// Register our callback to the appropriate filter
add_filter( 'mce_buttons_2', 'add_style_select_buttons' );


//add custom styles to the WordPress editor
function my_custom_styles( $init_array ) {  
 
    $style_formats = array(  

        array(  
            'title' => 'Lead Paragraph',  
            'block' => 'span',  
            'classes' => 'lead',
            'wrapper' => true,
        ),

		array(  
            'title' => 'Large Lead Paragraph',  
            'block' => 'span',  
            'classes' => 'large-lead',
            'wrapper' => true,
        ),

        array(  
            'title' => 'small Title',  
            'block' => 'span',  
            'classes' => 'small-title',
            'wrapper' => true,
        ),

        array(  
            'title' => 'Medium Title',  
            'block' => 'span',  
            'classes' => 'medium-title',
            'wrapper' => true,
        ),

        array(  
            'title' => 'Large Title',  
            'block' => 'span',  
            'classes' => 'large-title',
            'wrapper' => true,
        ),











        array(  
            'title' => 'Small Text',  
            'block' => 'span',  
            'classes' => 'small-text',
            'wrapper' => true,
        ),
        array(  
            'title' => 'Medium Text',  
            'block' => 'span',  
            'classes' => 'medium-text',
            'wrapper' => true,
        ),
        array(  
            'title' => 'Medium Large Text',  
            'block' => 'span',  
            'classes' => 'medium-large-text',
            'wrapper' => true,
        ),
        array(  
            'title' => 'Large Text',  
            'block' => 'span',  
            'classes' => 'large-text',
            'wrapper' => true,
        ),
        array(  
            'title' => 'Huge Text',  
            'block' => 'span',  
            'classes' => 'huge-text',
            'wrapper' => true,
        ),
        
    );  
    // Insert the array, JSON ENCODED, into 'style_formats'
    $init_array['style_formats'] = json_encode( $style_formats );  
    
    return $init_array;  
  
} 
// Attach callback to 'tiny_mce_before_init' 
add_filter( 'tiny_mce_before_init', 'my_custom_styles' );
