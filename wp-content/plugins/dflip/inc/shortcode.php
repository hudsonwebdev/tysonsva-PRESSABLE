<?php

/**
 * Created by PhpStorm.
 * User: Deepak
 * Date: 5/3/2016
 * Time: 2:27 PM
 */
class DFlip_ShortCode {
  
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
    
    // Load shortcode hooks and filters.
    add_shortcode( 'dflip', array( $this, 'shortcode' ) );
    
  }
  
  /**
   * Builds the dFlip Shortcode for the plugin
   *
   * @param array  $attr    Attributes of the shortcode.
   * @param string $content Content of the button or thumb
   *
   * @return string HTML content to display image-text.
   * @since 1.0.0
   *
   */
  public function shortcode( $attr, $content = '' ) {
    
    if ( $this->base->selective_script_loading == true ) {
      //enqueue script
      wp_enqueue_script( $this->base->plugin_slug . '-script' );
      
      //enqueue styles
      wp_enqueue_style( $this->base->plugin_slug . '-style' );
    }

    $is_pdf_cat = isset( $attr['pdf-cat'] ) && trim( $attr['pdf-cat'] ) !== '';
    $ismulti = isset( $attr['books'] ) && trim( $attr['books'] ) !== '';
    $atts_default = array(
        'class' => '',
        'id'    => '',
        'books' => '',
        'pdf-cat' => ''
    );
    //atts or post defaults
    $atts = shortcode_atts( $atts_default, $attr, 'dflip' );
    if ( $ismulti || $is_pdf_cat) {
      if ( isset( $attr['trigger'] ) ) {
        unset( $attr['trigger'] );
      }
      $limit = isset( $attr['limit'] ) ? (int) $attr['limit'] : (int) $this->base->get_config( "multiplePostLimit" );
      if ( $limit === 0 ) {
        $limit = '-1';
      }
      $orderby = isset( $attr['orderby'] ) ? $attr['orderby'] : 'date';
      $order = isset( $attr['order'] ) ? $attr['order'] : 'DESC';
      $ids = array();
      if($is_pdf_cat){
          $postslist = get_posts(array(
              'tax_query' => array(
                  array(
                      'taxonomy' => 'dflip_pdf_category',
                      'field' => 'slug',
                      'terms' => $atts['pdf-cat'],
                  )
              ),
              'post_type' => 'attachment',
              'posts_per_page' => -1,
              'numberposts' => $limit,
              'nopaging' => true,
              'exclude' => $ids,
              'orderby' => $orderby,
              'order' => $order
          ));
          foreach ($postslist as $post) {
              array_push($ids, $post->ID);
          }
      }
      else {
          $books = explode(',', $atts['books']);
          foreach ((array)$books as $query) {
              $query = trim($query);
              if (is_numeric($query)) {
                  array_push($ids, $query);
              } else {
                  if ($query === 'all' || $query === '*') {
                      $isdrafts = isset( $attr['drafts'] ) && trim( $attr['drafts'] ) == 'true';
                      $post_status = $isdrafts && current_user_can( 'read_private_posts' ) ? array('publish', 'draft','private') : null;
                      $postslist = get_posts(array(
                          'post_type' => 'dflip',
                          'posts_per_page' => -1,
                          'numberposts' => $limit,
                          'nopaging' => true,
                          'exclude' => $ids,
                          'orderby' => $orderby,
                          'order' => $order,
                          'post_status' => $post_status
                      ));
                      foreach ($postslist as $post) {
                          array_push($ids, $post->ID);
                      }
                  } else {
                      $postslist = get_posts(array(
                          'tax_query' => array(
                              array(
                                  'taxonomy' => 'dflip_category',
                                  'field' => 'slug',
                                  'terms' => $query,
                              )
                          ),
                          'post_type' => 'dflip',
                          'posts_per_page' => -1,
                          'numberposts' => $limit,
                          'nopaging' => true,
                          'exclude' => $ids,
                          'orderby' => $orderby,
                          'order' => $order
                      ));
                      foreach ($postslist as $post) {
                          array_push($ids, $post->ID);
                      }
                  }
              }
          }
      }

      return $this->books_wrapper($attr,$atts,$ids,$content,$limit);
      
    }
    else {
      return $this->book( $attr, $content );
    }
  }

  public function books_wrapper($attr,$atts,$ids,$content,$limit){

      $html = '<div class="dflip-books">';//can get overridden by shelf
      if(dflip_fs()->is_plan('dflip_wp_pro')) {
      //region Shelf
      $shelfImage = '';
      if ( isset( $attr['shelf-image'] ) ) {
          $shelfImage = $attr['shelf-image'];
      }
      if ( $shelfImage != 'none' ) {
          if ( !empty( $shelfImage ) ) {
              $shelfImage = $this->base->get_shelf_image( $shelfImage );
          }
          $shelfTypeClass = '';
          if ( $shelfImage != '' || $this->base->shelf_image != '' ) {
              $shelfTypeClass = 'df-has-shelf ';
          }

          if ( $shelfImage != '' ) {
              $id = 'df_shelf_' . rand();
              $html = '<div id=' . $id . ' class="dflip-books ' . $shelfTypeClass . '">';
              $html .= "<style>#" . $id . ".df-has-shelf df-post-shelf:before, #" . $id . ".df-has-shelf df-post-shelf:after{background-image: url('" . $shelfImage . "');}</style>";
          } else {
              $html = '<div class="dflip-books ' . $shelfTypeClass . '">';
          }
      }
      //endregion
      }
      $limitMax = ( $limit === '-1' || $limit === -1 ) ? 999 : (int) $limit;
      $_count = 0;
      foreach ( $ids as $id ) {
          if ( $_count >= $limitMax ) {
              break;
          }
          $attr['id'] = $id;
          $html .= $this->book( $attr, $content, true );
          $_count++;

      }

      return $html . '</div>';
  }
  
  /**
   * Helper function for dFlip Shortcode
   *
   * @param        $raw_attr
   * @param string $content Content of the button or thumb
   *
   * @param bool   $multi   checks if this is a part of multiple books request
   *
   * @return string HTML content to display image-text.
   * @since    1.0.0
   *
   * @internal param array $attr Attributes of the shortcode.
   */
  public function book( $raw_attr, $content = '', $multi = false ) {
    $base = $this->base;
    $atts_default = array(
        'class' => '',
        'id'    => '',
        'type'  => $multi ? 'thumb' : 'book'
    );
    
    //atts or post defaults
    $atts = shortcode_atts( $atts_default, $raw_attr, 'dflip' );
    
    //in PHP7 if $attr is not an array it causes issue
    if ( !is_array( $raw_attr ) ) {
      $raw_attr = array();
    }
    $html_attr = array();
    
    //default data
    $id = $atts['id'] === '' ? 'rand' . rand() : $atts['id'];
    $id = sanitize_title( $id );//xss attacks using id
    $type = $atts['type'];
    $class = $atts['class'];
    $title = do_shortcode( $content );
    $title = trim($title);
    $thumb_url = '';
    
    //get Id
    $post_id = $id;
    
    $post = trim( $post_id ) === '' ? null : get_post( $post_id );
    $post_data = array();
    
    $post_data['source'] = isset( $raw_attr['source'] ) ? $raw_attr['source'] : "";
    
    //pull post data if available for the script part only
    if ( $post != null && !empty( $post_id ) && is_numeric( $post_id ) ) {
	    
      if ( ( $post->post_status === "private" && !current_user_can( 'read_private_posts' ) ) ) {
        return '';
      }
	    if ( ( $post->post_status === "draft" && !current_user_can( 'edit_post',$post_id) ) ) {
		    return '';
	    }
      $post_data = $this->get_post_data( $post, $post_data );
      
      $id = $post->ID;
      
      if ( empty( $title ) ) {
        $title = get_the_title( $post_id );
      }
      $post_data['slug'] = $post->post_name;
      $html_attr['data-slug'] = $post->post_name; //this can be overwritten
      $html_attr['data-_slug'] = $post->post_name; //this is for fallback
      $html_attr['_slug'] = $post->post_name;
      
      if ( !empty( $post_data['thumb'] ) ) {
        $thumb_url = $post_data['thumb'];
      }
      unset( $post_data['thumb'] );
      unset( $post_data['class'] );
      
    } else {
      /*handled by new attribute support*/
    }
    
    //deep-link
    $html_attr['data-title'] = sanitize_title( $title );
    
    if ( !$multi && isset( $raw_attr['slug'] ) && !empty( $raw_attr['slug'] ) ) {
      $html_attr['data-slug'] = sanitize_title( $raw_attr['slug'] );
    }
    if ( empty( $title ) ) {
      $title = $base->get_translate( "text_open_book" );//"Open Book";
    }
    
    /*Attribute overrides*/
    $attrHTML = ' ';
    $html_attr['id'] = 'df_' . $id;
    $post_data['id'] = $id;
    $html_attr['data-df-option'] = 'df_option_' . $id;
    
    if ( !isset( $raw_attr['thumb'] ) && $thumb_url !== '' ) {
      $html_attr['thumb'] = esc_attr( $thumb_url );
    }
    
    //$attr is removed since it can contain insecure and malicious data, atts hold only required keys and sanitized values
    // keys should be small case - later converted to proper variable using getAttributes()
    $keys = array(
        'data-thumb'      => '',
        'data-page'       => 'page',
        'target'          => '',
        'height'          => '',
        'data-download'   => 'pdf-download',
        'data-source'     => 'data-source,pdf-source,source',
        'data-is3d'       => 'webgl,is3d',
        'data-viewertype' => 'viewertype,viewer-type,data-viewer-type',
        'data-pagemode'   => 'pagemode',
        'data-trigger'    => 'trigger',
    );
    
    foreach ( $keys as $key => $aliasString ) {
      $aliases = explode( ',', $key . ',' . $aliasString );
      foreach ( $aliases as $alias ) {
        if ( isset( $raw_attr[ $alias ] ) ) {
          $html_attr[ $key ] = esc_attr( $raw_attr[ $alias ] );
          break;
        }
      }
    }
    
    if ( isset( $raw_attr['open'] ) && $raw_attr['open'] == 'auto' ) {
      $class = $class . " df-auto-open-lightbox ";
    }
		if($this->has_cover_ridge($post_data)){
			$class = $class . " df-has-ridge ";
		}
    
    $href = "#";
    if ( isset( $post_data["url"] ) && $post_data["url"] != "" ) {
      $href = $post_data["url"];
      unset( $post_data["url"] );
    }
    if ( $href === "#" ) {
      unset( $html_attr['target'] );
    }
    if ( $type !== 'thumb' ) {
      unset( $html_attr['thumb'] );
    }
    
    foreach ( $html_attr as $key => $value ) {
      $attrHTML .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
    }
    
    $html = "";
	  if ( $type === 'custom') {
		  $html = '<div class="_df_' . $type . ' ' . esc_attr( $class ) . ' ' . $attrHTML . ' >' . $title  . '</div>';
	  }
    else if ( $type === 'button' || $type === 'thumb' || $type === 'hidden' || $type==="link") {
      $hidden = '';
      if ( $type === 'hidden' ) {
        $hidden = "hidden";
      }
      $html = '<a class="_df_' . $type . ' ' . esc_attr( $class ) . '"  href="' . $href . '" ' . $attrHTML . ' ' . $hidden . '>' . $title  . '</a>';
    } else {
      $html = '<div class="_df_book df-container df-loading ' . esc_attr( $class ) . '" ' . $attrHTML . '></div>';
    }
    
    if ( count( $post_data ) > 0 ) {
      
      /*Normally this occurs only when a valid post id is added*/
      
      $code = 'window.' . $html_attr['data-df-option'] . ' = ' . json_encode( $post_data ) . '; if(window.DFLIP && window.DFLIP.parseBooks){window.DFLIP.parseBooks();}';
      
      $html .= '<script class="df-shortcode-script" nowprocket type="application/javascript">' . $code . '</script>';
      
    }
    wp_enqueue_script( $base->plugin_slug . '-script' );
    
    //enqueue styles
    wp_enqueue_style( $base->plugin_slug . '-style' );
    
    return $html;
  }
  
  public function get_post_data( $post, $post_data = array() ) {
    
    $is_pdf_attachment = $post->post_mime_type == "application/pdf";
    
    if ( $post->post_type == $this->base->plugin_slug || $is_pdf_attachment ) {
      $post_meta = get_post_meta( $post->ID, '_dflip_data' );
      if ( is_array( $post_meta ) && count( $post_meta ) > 0 ) {
        $post_meta = $post_meta[0];
      }
      
      $options = array(
        //internal options
        'source_type'             => 'source_type',
        'pdf_source'              => 'pdf_source',
        'pdf_thumb'               => 'pdf_thumb',
        'pages'                   => 'pages',
        'outline'                 => 'outline',
        
        //js options
        'viewerType'              => 'viewerType',
        'enableDownload'          => 'enableDownload',
        'cover3DType'             => 'cover3DType',
        'color3DCover'            => 'color3DCover',
        'hasSpiral'               => 'hasSpiral',
        'calendarMode'            => 'calendarMode',
        'spiralColor'             => 'spiralColor',
        'flipbook3DTiltAngleUp'   => 'flipbook3DTiltAngleUp',
        'flipbook3DTiltAngleLeft' => 'flipbook3DTiltAngleLeft',
        'color3DSheets'           => 'color3DSheets',
        'backgroundColor'         => 'bg_color',
        'backgroundImage'         => 'bg_image',
        'autoEnableOutline'       => 'auto_outline',
        'autoEnableThumbnail'     => 'auto_thumbnail',
        'overwritePDFOutline'     => 'overwrite_outline',
        'showDownloadControl'     => 'enable_download',
        'showSearchControl'       => 'enable_search',
        'showPrintControl'        => 'enable_print',
        'soundEnable'             => 'auto_sound',
        'pageScale'               => 'page_scale',
        'maxTextureSize'          => 'texture_size',
        'pageMode'                => 'page_mode',
        'singlePageMode'          => 'single_page_mode',
        'pageSize'                => 'page_size',
        'controlsPosition'        => 'controls_position',
        'autoPlay'                => 'autoplay',
        'autoPlayDuration'        => 'autoplay_duration',
        'autoPlayStart'           => 'autoplay_start',
        'is3D'                    => 'webgl',
        'duration'                => 'duration',
        'height'                  => 'height',
        'hard'                    => 'hard',
        'direction'               => 'direction'
      );
      $post_data['source_type'] = ''; //else it will throw Undefined array key "source_type" below
      foreach ( $options as $key => $match_key ) {
        if ( isset( $post_meta[ $match_key ] ) ) {
          $value = $post_meta[ $match_key ];
          if ( $value === "" || $value === null || $value == "global" ) {//newly added will be null in old post
            continue;
          } else if ( $value === "true" ) {
            $post_data[ $key ] = true;
          } elseif ( $value === "false" ) {
            $post_data[ $key ] = false;
          } else {
            $post_data[ $key ] = $value;
          }
        }
      }
      
      
      //region source
      $source_type = $is_pdf_attachment ? 'pdf' : $post_data['source_type'];
      $thumb_url = '';
      $post_data['source'] = '';
      if ( $is_pdf_attachment ) {
        $post_data['source'] = wp_get_attachment_url( $post->ID );
          $thumb_url = empty( $post_data['pdf_thumb'] ) ? '' : $post_data['pdf_thumb'];
      } else if ( $source_type == 'pdf' ) {
        if ( isset( $post_data['pdf_source'] ) ) {
          $post_data['source'] = $post_data['pdf_source'];
          $thumb_url = empty( $post_data['pdf_thumb'] ) ? '' : $post_data['pdf_thumb'];
        }
      } else if ( $source_type == 'image' ) {
        $pages = array_map( 'maybe_unserialize', $post_data['pages'] );
        $source_list = array();
        $links = array();
        $index = 0;
        foreach ( $pages as $image ) {
          if ( $thumb_url === '' ) {
            $thumb_url = $image['url'];
          }
          if ( $image['url'] !== '' ) {
            array_push( $source_list, $image['url'] );
          }
          if ( isset( $image['hotspots'] ) && $image['hotspots'] !== '' ) {
            $links[ $index ] = $image['hotspots'];
          }
          $index++;
        }
        $post_data['links'] = $links;
        $post_data['source'] = $source_list;
      }
      $post_data['thumb'] = $thumb_url;
      unset( $post_data['source_type'] );
      unset( $post_data['pages'] );
      unset( $post_data['pdf_source'] );
      unset( $post_data['pdf_thumb'] );
      //endregion
      
      if ( $this->base->get_config( 'enablePostPages' ) == 'true' ) {
        $post_data['url'] = $is_pdf_attachment ? get_attachment_link( $post->ID ) : get_post_permalink( $post->ID );
      }
      
      $post_data['slug'] = $post->post_name;
      $post_data['wpOptions'] = 'true';
      
      
      foreach ( $post_data as $key => $value ) {
        if ( $value === '' || $value === null || $value === "global" ) {//}) {//newly added will be null in old post
          unset( $post_data[ $key ] );
        }
      }
    }else{
      return apply_filters('dflip_get_post_data_'.$post->post_type,$post_data,$post);
    }
    
    return $post_data;
  }

	public function has_cover_ridge($post_data){
		$value = $this->base->get_config( 'cover3DType' );
		if(isset($post_data['cover3DType']) && $post_data['cover3DType'] !='global'){
			$value = $post_data['cover3DType'];
		}
		return $value == 'ridge' || $value == 'plain' || $value == 'basic';
	}
  /**
   * Returns the singleton instance of the class.
   *
   * @return object dFlip_PostType object.
   * @since 1.0.0
   *
   */
  public static function get_instance() {
    
    if ( !isset( self::$instance )
        && !( self::$instance instanceof DFlip_ShortCode ) ) {
      self::$instance = new DFlip_ShortCode();
    }
    
    return self::$instance;
    
  }
  
}

//Load the dFlip Plugin Class
$dflip_shortcode = DFlip_ShortCode::get_instance();


