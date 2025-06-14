<?php
/**
 * tca functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package tca
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.3.8' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function tca_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on tca, use a find and replace
		* to change 'tca' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'tca', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'footer-menu' => esc_html__( 'Footer Menu', 'tca' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'tca_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'tca_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function tca_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'tca_content_width', 640 );
}
add_action( 'after_setup_theme', 'tca_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function tca_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'tca' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'tca' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'tca_widgets_init' );




/**
 * Enqueue scripts and styles.
 */
function tca_scripts() {


	

	wp_enqueue_style( 'tca-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'tca-style', 'rtl', 'replace' );

	wp_enqueue_style( 'tca-custom-style', get_template_directory_uri() . '/public/css/main.css', array(), _S_VERSION );

	wp_enqueue_script( 'tca-navigation', get_template_directory_uri() . '/public/js/main.js', array('jquery'), _S_VERSION, true );

	if(is_singular('neighborhood')){

		 // Mapbox JS
		 wp_enqueue_script('mapbox-js', 'https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js', array(), null, true);

		 // Mapbox CSS
		 wp_enqueue_style('mapbox-css', 'https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css', array(), null);

		wp_enqueue_script( 'tca-neighborhood', get_template_directory_uri() . '/public/js/neighborhood.js', array('jquery'), _S_VERSION, true );
	}
	

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'tca_scripts',20 );



/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}



function include_all_php_files_in_directory($directory) {
    // Get the absolute path to the directory
    $directory_path = get_template_directory() . '/' . $directory;

    // Check if the directory exists
    if (is_dir($directory_path)) {
        // Get all PHP files in the directory
        $files = glob($directory_path . '/*.php');

        // Include each PHP file
        foreach ($files as $file) {
            include_once $file;
        }
    } else {
        // Optionally log an error if the directory doesn't exist
        error_log('Directory not found: ' . $directory_path);
    }
}


include_all_php_files_in_directory('inc');





add_filter( 'gform_submit_button', 'add_custom_css_classes', 10, 2 );
function add_custom_css_classes( $button, $form ) {
    $fragment = WP_HTML_Processor::create_fragment( $button );
    $fragment->next_token();
    $fragment->add_class( 'tca-button' );
 
    return $fragment->get_updated_html();
}


//add_image_size( 'featured-image', 1378, 820 ); 



function max_title_length( $title ) {

$max = 80;
if( strlen( $title ) > $max ) {
return substr( $title, 0, $max ). " …";
} else {
return $title;
}
}




//if imagify does not fix this flash of unstyled content


add_action( 'admin_footer', function() {

    echo '<style>#imagify-pricing-modal{display: none;}</style>';
}, 9 );

function my_excerpt_length($length){ return 20; } 
add_filter('excerpt_length', 'my_excerpt_length');







function remove_home_from_yoast_breadcrumb($breadcrumbs) {
    // Check if the first breadcrumb is "Home"
    if (isset($breadcrumbs[0]) && $breadcrumbs[0]['text'] === 'Home') {
        // Remove the first breadcrumb element (Home)
        array_shift($breadcrumbs);
    }
    return $breadcrumbs;
}
add_filter('wpseo_breadcrumb_links', 'remove_home_from_yoast_breadcrumb');





function custom_search_form( $form ) {
	$form = '<form role="search" method="get" id="searchform" class="searchform" action="' . home_url( '/' ) . '" >
	  <div class="custom-form">
	  <div class="searchbox"><label class="screen-reader-text" for="s">' . __( 'Search:' ) . '</label>
	  <input type="text" class="tca-input" value="' . get_search_query() . '" name="s" id="s" /></div>
	  <div>
	  <button><span uk-icon="icon: search"></span></button></div>
	</div>
	</form>';

	return $form;
  }
  add_filter( 'get_search_form', 'custom_search_form', 40 );




  function new_excerpt_more($more) {

    return '...'; // Replace the ellipsis with an empty space

}

add_filter('excerpt_more', 'new_excerpt_more'); 

// Redirect category archives to homepage
function disable_category_archives() {
    if (is_category()) {
        wp_redirect(home_url(), 301);
        exit;
    }
}
add_action('template_redirect', 'disable_category_archives');
