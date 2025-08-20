<?php

/**
 * dFlip CUSTOM POST
 *
 * Initializes and Registers the required custom post for dFlip
 *
 * @since   1.0.0
 *
 * @package dFlip
 * @author  Deepak Ghimire
 */
class DFlip_Post_Type {
  
  /**
   * Holds the singleton class object.
   *
   * @since 1.0.0
   *
   * @var object
   */
  public static $instance;
  
  /**
   * Holds the base DFlip class object.
   *
   * @since 1.0.0
   *
   * @var object
   */
  public $base;
  
  /**
   * Primary class constructor.
   *
   * @since 1.0.0
   */
  public function __construct() {
    
    // Load the base class object.
    $this->base = DFlip::get_instance();
    
    $labels = array(
        'name'               => __( 'dFlip Book', 'DFLIP' ),
        'singular_name'      => __( 'dFlip Book', 'DFLIP' ),
        'menu_name'          => __( 'dFlip Books', 'DFLIP' ),
        'name_admin_bar'     => __( 'dFlip Book', 'DFLIP' ),
        'add_new'            => __( 'Add New Book', 'DFLIP' ),
        'add_new_item'       => __( 'Add New Book', 'DFLIP' ),
        'new_item'           => __( 'New dFlip Book', 'DFLIP' ),
        'edit_item'          => __( 'Edit dFlip Book', 'DFLIP' ),
        'view_item'          => __( 'View dFlip Book', 'DFLIP' ),
        'all_items'          => __( 'All Books', 'DFLIP' ),
        'search_items'       => __( 'Search dFlip Books', 'DFLIP' ),
        'parent_item_colon'  => __( 'Parent dFlip Books:', 'DFLIP' ),
        'not_found'          => __( 'No dFlip-Books found.', 'DFLIP' ),
        'not_found_in_trash' => __( 'No dFlip Books found in Trash.', 'DFLIP' )
    );
    
    $args = array(
        'labels'              => $labels,
        'description'         => __( 'Description.', 'DFLIP' ),
        'public'              => $this->base->get_config( 'enablePostPages' ) == 'true',  //this removes the permalink option
        'publicly_queryable'  => $this->base->get_config( 'enablePostPages' ) == 'true',
        'exclude_from_search' => true, // if not excluded, posts will be displayed in normal search. This will hide it from other archive and taxonomy listing, and needs to be fetched manually.
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => false, //array('slug' => $this->base->slug),
        'capability_type'     => 'post',
        'has_archive'         => true,//$this->base->get_config( 'enablePostPages' ) == 'true',
        'hierarchical'        => false,
        'menu_position'       => null,
        'menu_icon'           => 'dashicons-book',
        'supports'            => array( 'title' ),
        'rewrite'             => array( 'slug' => 'books' ),
    );
    
    register_post_type( 'dflip', $args );
    
    register_taxonomy( 'dflip_category', 'dflip', array(
        'hierarchical'       => true,
        'public'             => false,
        'publicly_queryable' => $this->base->get_config( 'enablePostPages' ) == 'true',
        'show_ui'            => true, //display the category admin page
        'show_admin_column'  => true,
        'show_in_nav_menus'  => true,
        'rewrite'            => array( 'slug' => 'book-category' ),
    ) );
    
    if(dflip_fs()->is_plan('dflip_wp_pro')) {
    $media_category_labels = array(
        'name' => 'PDF Categories'
    );
    
    register_taxonomy( 'dflip_pdf_category', 'attachment', array(
        'labels'            => $media_category_labels,
        'hierarchical'      => true,
        'public'            => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'rewrite'           => array( 'slug' => 'dflip-pdf-category', 'hierarchical' => true ),
    ) );
    }
    if ( is_admin() && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
      $this->init_admin();
    } else {// Load frontend only components.
      $this->init_front();
    }
    
    
  }
  
  /**
   * Loads all admin related files into scope.
   *
   * @since 1.0.0
   */
  public function init_admin() {
    $isPro = dflip_fs()->is_plan('dflip_wp_pro');
    
    if($isPro) {
    add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
    }
    
    // Remove quick editing from the dFlip post type row actions.
    add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 1 );
    
    // Manage post type columns.
    add_filter( 'manage_dflip_posts_columns', array( $this, 'dflip_columns' ) );
    add_action( 'manage_dflip_posts_custom_column', array( $this, 'dflip_columns_content' ), 10, 2 );
    
    add_filter( 'manage_edit-dflip_category_columns', array( $this, 'dflip_cat_columns' ) );
    add_filter( 'manage_dflip_category_custom_column', array( $this, 'dflip_cat_columns_content' ), 10, 3 );
    add_action( 'restrict_manage_posts',array($this,'dflip_category_filter'), 10, 2 );
    
    if($isPro) {
    add_filter( 'manage_edit-dflip_pdf_category_columns', array( $this, 'dflip_pdf_category_shortcode_columns' ) );
    add_action( 'manage_dflip_pdf_category_custom_column', array( $this, 'dflip_pdf_category_shortcode__column_data' ), 10, 3 );
    }
    
  }
  public function action_admin_menu() {
    add_media_page(
        'PDF Files (Beta)',
        'PDF Files (Beta)',
        'upload_files',
        'dflip-pdfs',
        array( $this, 'renderMediaPDFTable' ), 2
    );
  }
  public function renderMediaPDFTable() {
    $_REQUEST['mode'] = 'list';
    $_REQUEST['attachment-filter'] = 'post_mime_type:application/pdf';
    //PDF attachments
    add_filter( 'post_mime_types', array( $this, 'add_pdf_option_to_media_filters' ) );
    add_filter( 'manage_media_columns', array( $this, 'manage_media_columns' ), 999 );
    add_action( 'manage_media_custom_column', array( $this, 'manage_media_columns_content' ), 10, 2 );
    
    $wp_list_table = _get_list_table( 'WP_Media_List_Table', [ 'screen' => 'upload' ] );
    $pagenum = $wp_list_table->get_pagenum();
    $wp_list_table->prepare_items();
    ?>
      <div class="wrap">
          <h1 class="wp-heading-inline">PDF Files</h1>
          <style>
            .view-grid, .attachment-filters {display: none !important;}
          </style>
          <script>
              jQuery(function(){
                jQuery(".column-taxonomy-dflip_pdf_category a").each(function(){
                  var el = jQuery(this);
                  var href = el.attr("href");
                  if(href.indexOf("page=dflip-pdfs")<0){
                    el.attr("href",href+"&page=dflip-pdfs");
                  }
                });
              });
          </script>
          <form id="posts-filter" method="get">
              <input type="hidden" name="page" class="post_type_page" value="dflip-pdfs">
            <?php
            $wp_list_table->screen->post_type="attachment";
            $wp_list_table->views();
            $wp_list_table->display();
            ?>
          </form>
      </div>
    <?php
  }
  
  public function add_pdf_option_to_media_filters( $post_mime_types ) {
    $post_mime_types['application/pdf'] = array( __( 'PDFs' ), __( 'Manage PDFs' ), _n_noop( 'PDF <span class="count">(%s)</span>', 'PDFs <span class="count">(%s)</span>' ) );
    return $post_mime_types;
  }
  
  public function dflip_pdf_category_shortcode_columns( $columns ) {
    $offset = array_search( 'posts', array_keys( $columns ) );
    
    return array_merge(
        array_slice( $columns, 0, $offset ),
        array( 'shortcode' => __( 'Shortcode', 'DFLIP' ) ),
        array_slice( $columns, $offset, null )
    );
  }
  
  public function dflip_pdf_category_shortcode__column_data( $content, $column_name, $term_id ) {
    if ( $column_name === 'shortcode' ) {
      $term = get_term( $term_id );
      // Output the shortcode data
      echo sprintf( '[dflip pdf-cat="%s"][/dflip]', esc_html( $term->slug ) );
    }
  }
  
  public function init_front() {
    
    if ( $this->base->get_config( 'enablePostPages' ) == 'true' ) {
      add_filter( 'attachment_template', array( $this, 'attachment_template' ) );
      
      add_filter( 'single_template', array( $this, 'single_template' ) );
      //      add_action( "dflip_single_content", array( $this, "single_template_content" ), 10, 1 );
      
      add_filter( 'taxonomy_template', array( $this, 'category_template' ) );
      add_action( "dflip_category_content", array( $this, "category_template_content" ), 10, 1 );
      
      add_action( "dflip_archive_content", array( $this, "archive_template_content" ), 10, 1 );
      
      add_action( 'wp_head', array( $this, 'fb_opengraph' ), 1 );//facebook uses firt seen meta
      add_action( 'wp_head', array( $this, 'twitter_card' ), 11 );//twitter uses last seen meta
      
    } else {
      add_filter( 'the_content', array( $this, 'filter_the_pdf_attachment_content' ) );
    }
    
    add_filter( 'home_template', array( $this, 'archive_template' ) );
    add_filter( 'archive_template', array( $this, 'archive_template' ) );
    add_action( "dflip_single_content", array( $this, "single_template_content" ), 10, 1 );
    
    add_filter( 'query_vars', array( $this, 'dflip_query_vars' ) );
  }
  
  function dflip_query_vars( $qvars ) {
    $qvars[] = 'dearflip-id';
	  $qvars[] = 'dearflip-slug';
	  $qvars[] = 'pdf-slug';
    $qvars[] = 'header-footer';
    return $qvars;
  }
  
  public function single_template( $single_template ) {
    
    global $post;
    
    if ( $post->post_type === "dflip" ) {
      $template = plugin_dir_path( __FILE__ ) . '../assets/templates/single.php';
      if ( file_exists( $template ) ) {
        $single_template = $template;
      }
      
    }
    
    return $single_template;
  }
  
  public function fb_opengraph() {
    global $post;
    if ( isset($post) && $post->post_type === "dflip" ) {
      $post_meta = get_post_meta( $post->ID, '_dflip_data' );
      if ( is_array( $post_meta ) && count( $post_meta ) > 0 ) {
        $post_meta = $post_meta[0];
      }
      ?>
        <meta property="og:title" content="<?php echo the_title() . " - " . get_bloginfo(); ?>"/>
        <meta property="og:url" content="<?php echo the_permalink(); ?>"/>
        <meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/>
      <?php
      if ( !empty( $post_meta['pdf_thumb'] ) ) {
        ?>
          <meta property="og:image" content="<?php echo $post_meta['pdf_thumb']; ?>"/>
          <meta property="og:image:secure_url" content="<?php echo $post_meta['pdf_thumb']; ?>"/>
        <?php
      }
    }
  }
  public function twitter_card() {
    global $post;
    if ( isset($post) && $post->post_type === "dflip" ) {
      $post_meta = get_post_meta( $post->ID, '_dflip_data' );
      if ( is_array( $post_meta ) && count( $post_meta ) > 0 ) {
        $post_meta = $post_meta[0];
      }
      ?>
        <meta name="twitter:card" content="summary_large_image"/>
        <meta name="twitter:site" content="<?php echo get_bloginfo(); ?>"/>
        <meta name="twitter:title" content="<?php echo the_title() . " - " . get_bloginfo(); ?>"/>
      <?php
      if ( !empty( $post_meta['pdf_thumb'] ) ) {
        ?>
          <meta name="twitter:image" content="<?php echo $post_meta['pdf_thumb']; ?>"/>
        <?php
      }
    }
  }
  
  public function attachment_template( $attachment_template ) {
    
    global $post;
    
    if ( $post->post_mime_type == "application/pdf" ) {
      $template = plugin_dir_path( __FILE__ ) . '../assets/templates/single.php';
      if ( file_exists( $template ) ) {
        $attachment_template = $template;
      }
    }
    
    return $attachment_template;
  }
  
  public function category_template( $category_template ) {
    
    if ( is_tax( 'dflip_category' ) ) {
      $template = plugin_dir_path( __FILE__ ) . '../assets/templates/category.php';
      if ( file_exists( $template ) ) {
        $category_template = $template;
      }
    }
    
    return $category_template;
  }
  
  public function archive_template( $archive_template ) {
    global $post;
    $_post = null;
    $dflip_slug = get_query_var( 'dearflip-slug' );
    $pdf_slug = get_query_var( 'pdf-slug' );
    if($dflip_slug || $pdf_slug) {
	    if ( $dflip_slug ) {
		    $flip_posts = get_posts( array(
			    'name' => $dflip_slug,
			    'post_type' => 'dflip'
		    ) );
		    if ( $flip_posts ) {
			    $_post = $flip_posts[0];
		    }
	    } else if ( $pdf_slug ) {
		    $flip_posts = get_posts( array(
			    'name' => $pdf_slug,
			    'post_status' => 'inherit',
			    'post_type' => 'attachment'
		    ) );
		    if ( $flip_posts ) {
			    $_post = $flip_posts[0];
		    }
	    }
	    if ( $_post ) {
		    $post = $_post;
		    $template = plugin_dir_path( __FILE__ ) . '../assets/templates/single.php';
		    if ( file_exists( $template ) ) {
			    $archive_template = $template;
		    }
	    }else
        {
	        get_template_part( 404 );
	        exit();
        }
    }
    return $archive_template;
  }
  
  public function category_template_content() {
    
    $current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
    echo do_shortcode( '[dflip books="' . $current_term->slug . '"][/dflip]' );
  }
  
  public function archive_template_content() {
    echo "<h1>Latest Books</h1>";
    echo do_shortcode( '[dflip books="*" limit =5][/dflip]' );
    
  }
  
  
  public function single_template_content() {
    global $post;
    
    $html = "";
    $lightbox = $this->base->get_config( 'attachment_lightbox' );
    
    if ( $lightbox == 'true' ) {
      $html = do_shortcode( '[dflip open="auto" type="thumb" target="_popup" books="' . $post->ID . '"]Open ' . get_the_title( $post ) . '[/dflip]' );
    } else {
      $html = do_shortcode( '[dflip class="df-hash-focused" type="embed" id="' . $post->ID . '"][/dflip]' );
    }
    
    echo $html;
  }
  
  /**
   * Filter out unnecessary row actions dFlip post table.
   *
   * @param array $actions Default row actions.
   *
   * @return array $actions Amended row actions.
   * @since 1.0.0
   *
   */
  public function remove_quick_edit( $actions ) {
    if ( isset( get_current_screen()->post_type ) && 'dflip' == get_current_screen()->post_type ) {
      unset( $actions['inline hide-if-no-js'] );
    }
    
    return $actions;
  }
  
  /**
   * Customize the post columns for the dFlip post type.
   *
   * @return array $columns New Updated columns.
   * @since 1.0.0
   *
   */
  public function dflip_columns( $columns ) {
    
    $columns['shortcode'] = __( 'Shortcode', 'DFLIP' );
    $columns['modified'] = __( 'Last Modified', 'DFLIP' );
    
    return $columns;
  }
  
  /**
   * Customize the post columns for the dFlip post type category page
   *
   * @param array $defaults columns.
   *
   * @return array $defaults default columns.
   * @since 1.2.9
   *
   */
  public function dflip_cat_columns( $defaults ) {
    $defaults['shortcode'] = 'Shortcode';
    
    return $defaults;
  }
  
  /**
   * Customize the post columns for the dFlip post type category page
   *
   * @param array $defaults columns.
   *
   * @return array $defaults default columns.
   * @since 1.2.9
   *
   */
  public function manage_media_columns( $defaults ) {
    $arr = array(
        'cb'                          => $defaults['cb'],
        'title'                       => $defaults['title'],
//        'author'                      => $defaults['author'],
        'taxonomy-dflip_pdf_category' => $defaults['taxonomy-dflip_pdf_category'],
        'date'                        => $defaults['date'],
        'shortcode'                   => 'Shortcode'
    );
    return $arr;
  }
  
  /**
   * Add data to the custom columns added to the dFlip post type.
   *
   * @param string $column_name Name of the custom column.
   * @param int    $post_id     Current post ID.
   *
   * @since 1.0.0
   *
   */
  public function dflip_columns_content( $column_name, $post_id ) {
    $post_id = absint( $post_id );
    
    switch ( $column_name ) {
      case 'shortcode':
        echo '[dflip id="' . esc_attr( $post_id ) . '"][/dflip]';
        break;
      
      case 'modified' :
        the_modified_date();
        break;
    }
  }
  
  /**
   * Add data to the custom columns added to the dFlip post type category page.
   *
   * @param        $c
   * @param string $column_name Name of the custom column.
   * @param        $term_id
   *
   * @return string
   * @since 1.2.9
   *
   */
  public function dflip_cat_columns_content( $c, $column_name, $term_id = "" ) {
    
    return '[dflip books="' . get_term( $term_id, 'dflip_category' )->slug . '" limit="-1"][/dflip]';
    
  }
  
  
  /**
   * Add data to the custom columns added to the dFlip post type.
   *
   * @param string $column_name Name of the custom column.
   * @param int    $post_id     Current post ID.
   *
   * @since 1.0.0
   *
   */
  public function manage_media_columns_content( $column_name, $post_id ) {
    $post_id = absint( $post_id );
    
    switch ( $column_name ) {
      case 'shortcode':
        echo '[dflip id="' . esc_attr( $post_id ) . '" type="thumb"][/dflip]';
        break;
      
      case 'modified' :
        the_modified_date();
        break;
        
    }
  }

  
  public function filter_the_pdf_attachment_content( $content ) {
    global $post;
    
    // Check if we're inside the main loop in a single post page.
    if ( is_single() && in_the_loop() && is_main_query() && $post->post_mime_type == "application/pdf" ) {
      $html = "";
      $lightbox = $this->base->get_config( 'attachment_lightbox' );
      
      if ( $lightbox == 'true' ) {
        $html = do_shortcode( '[dflip class="df-auto-open-lightbox" type="thumb" target="_popup" books="' . $post->ID . '"]Open ' . get_the_title( $post ) . '[/dflip]' );
      } else {
        $html = do_shortcode( '[dflip class="df-hash-focused" type="embed" id="' . $post->ID . '"][/dflip]' );
      }
      
      return $html;
    }
    
    return $content;
  }
	
	// $which (the position of the filters form) is either 'top' or 'bottom'
	function dflip_category_filter( $post_type, $which ) {
		if (( 'top' === $which && 'dflip' === $post_type) ||
          ('bar' === $which && 'attachment' === $post_type && isset($_REQUEST['page']) && $_REQUEST['page']==='dflip-pdfs') ) {
			$taxonomy = $post_type === 'dflip' ? 'dflip_category':'dflip_pdf_category';
			$tax = get_taxonomy( $taxonomy );            // get the taxonomy object/data
			$cat = filter_input( INPUT_GET, $taxonomy ); // get the selected category slug
			
			echo '<label class="screen-reader-text" for="my_tax">Filter by ' .
				esc_html( $tax->labels->singular_name ) . '</label>';
			
			wp_dropdown_categories( [
				'show_option_all' => $tax->labels->all_items,
				'hide_empty' => 0, // include categories that have no posts
				'hierarchical' => $tax->hierarchical,
				'show_count' => 0, // don't show the category's posts count
				'orderby' => 'name',
				'selected' => $cat,
				'taxonomy' => $taxonomy,
				'name' => $taxonomy,
				'value_field' => 'slug',
			] );
		}
	}
  
  /**
   * Returns the singleton instance of the class.
   *
   * @return object DFlip_Post_Type object.
   * @since 1.0.0
   *
   */
  public static function get_instance() {
    
    if ( !isset( self::$instance ) && !( self::$instance instanceof DFlip_Post_Type ) ) {
      self::$instance = new DFlip_Post_Type();
    }
    
    return self::$instance;
    
  }
}

// Load the post-type class.
$dflip_post_type = DFlip_Post_Type::get_instance();

