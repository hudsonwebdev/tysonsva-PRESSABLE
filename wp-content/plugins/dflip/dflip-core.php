<?php
// @formatter:off
/**
 * Common core of DearFlip
 *
 * @since           1.7.6.1
 *
 * @package         dflip
 * @author          DearHive
 * @copyright   (c) Copyright by DearHive
 * @link            https://dearhive.com
 */
// @formatter:on

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) {
  exit;
}

if ( !class_exists( 'DFlip' ) ) {
  /**
   * Main dFlip plugin class.
   *
   * @since   1.0.0
   *
   * @package DFlip
   * @author  Deepak Ghimire
   */
  class DFlip {
    
    /**
     * Holds the singleton class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;
    
    /**
     * Plugin version
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = '2.4.37';
    
    /**
     * The name of the plugin.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_name = 'dFlip';
    
    /**
     * Unique plugin slug identifier.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_slug = 'dflip';
    /**
     * Unique plugin slug identifier.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $settings_help_page = 'https://dearflip.com/docs/dearflip-wordpress/features/settings/';
    
    public $plugin_url = "https://wordpress.org/plugins/3d-flipbook-dflip-lite/";
    /**
     * Plugin file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;
    
    /**
     * Default values.
     *
     * @since 1.2.6
     *
     * @var string
     */
    public $defaults;
    
    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    
    public $settings_text;
    public $external_translate;
    public $shelf_image;
    public $selective_script_loading;
    
    public function __construct() {
      
      $this->settings_text = array();
      $this->external_translate = false;
      $this->shelf_image = '';
      // Load the plugin.
      add_action( 'init', array( $this, 'init' ), 0 );
      
    }
    
    /**
     * Loads the plugin into WordPress.
     *
     * @since 1.0.0
     */
    public function init() {
      
      $this->defaults = array(
          
          'text_toggle_sound'      => array(
              'std'   => __( "Turn on/off Sound", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_toggle_thumbnails' => array(
              'std'   => __( "Toggle Thumbnails", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_thumbnails_title' => array(
              'std'   => __( "Thumbnails", 'DFLIP' ),
              'title' => __( 'Thumnbails Box Title', 'DFLIP' ),
          ),
          'text_outline_title' => array(
              'std'   => __( "Table of Contents", 'DFLIP' ),
              'title' => __( 'Outline Box Title', 'DFLIP' ),
          ),
          'text_search_title' => array(
              'std'   => __( "Search", 'DFLIP' ),
              'title' => __( 'Search Box Title', 'DFLIP' ),
          ),
          'text_search_placeholder' => array(
	          'std'   => __( "Search", 'DFLIP' ),
	          'title' => __( 'Search Text Placeholder', 'DFLIP' ),
          ),
          'text_search_clear' => array(
	          'std'   => __( "Clear", 'DFLIP' ),
	          'title' => __( 'Search Clear', 'DFLIP' ),
          ),
          'text_search_searching_info' => array(
	          'std'   => __( "Searching Page:", 'DFLIP' ),
	          'title' => __( 'Search - Searching Info', 'DFLIP' ),
          ),
          'text_search_results_found' => array(
	          'std'   => __( "results found", 'DFLIP' ),
	          'title' => __( 'Search Results Found', 'DFLIP' ),
          ),
          'text_search_results_not_found' => array(
	          'std'   => __( "No results Found!", 'DFLIP' ),
	          'title' => __( 'Search No Results', 'DFLIP' ),
          ),
          'text_search_result_page' => array(
	          'std'   => __( "Page", 'DFLIP' ),
	          'title' => __( 'Search Page', 'DFLIP' ),
          ),
          'text_search_result' => array(
	          'std'   => __( "result", 'DFLIP' ),
	          'title' => __( 'Search Result count(singular)', 'DFLIP' ),
          ),
          'text_search_results' => array(
	          'std'   => __( "results", 'DFLIP' ),
	          'title' => __( 'Search Result count(plural)', 'DFLIP' ),
          ),
          'text_search_minimum' => array(
	          'std'   => __( "Minimum 3 letters required!", 'DFLIP' ),
	          'title' => __( 'Search Minimum Text Info', 'DFLIP' ),
          ),
          'text_play'    => array(
	          'std'   => __( "Start AutoPlay", 'DFLIP' ),
	          'title' => 'std'
          ),
          'text_pause'    => array(
	          'std'   => __( "Pause AutoPlay", 'DFLIP' ),
	          'title' => 'std'
          ),
          'text_toggle_outline'    => array(
	          'std'   => __( "Toggle Outline/Bookmark", 'DFLIP' ),
	          'title' => 'std'
          ),
          'text_previous_page'     => array(
              'std'   => __( "Previous Page", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_next_page'         => array(
              'std'   => __( "Next Page", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_toggle_fullscreen' => array(
              'std'   => __( "Toggle Fullscreen", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_zoom_in'           => array(
              'std'   => __( "Zoom In", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_zoom_out'          => array(
              'std'   => __( "Zoom Out", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_toggle_help'       => array(
              'std'   => __( "Toggle Help", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_single_page_mode'  => array(
              'std'   => __( "Single Page Mode", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_double_page_mode'  => array(
              'std'   => __( "Double Page Mode", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_download_PDF_file' => array(
              'std'   => __( "Download PDF File", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_goto_first_page'   => array(
              'std'   => __( "Goto First Page", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_goto_last_page'    => array(
              'std'   => __( "Goto Last Page", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_share'             => array(
              'std'   => __( "Share", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_search'            => array(
              'std'   => __( "Search", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_print'             => array(
              'std'   => __( "Print", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_mail_subject'      => array(
              'std'   => __( "I wanted you to see this FlipBook", 'DFLIP' ),
              'title' => __( 'Share: Mail Subject', 'DFLIP' ),
              'desc'  => 'Translate/Alternative text share mail subject'
          ),
          'text_mail_body'         => array(
              'std'   => __( "Check out this site {{url}}", 'DFLIP' ),
              'title' => __( 'Share: Mail Message', 'DFLIP' ),
              'desc'  => 'Translate/Alternative text for share mail Message. <code>{{url}}</code>will be replaced with the shareURL.'
          ),
          'text_loading'           => array(
              'std'   => __( "Loading", 'DFLIP' ),
              'title' => 'std'
          ),
          'text_open_book'         => array(
              'std'   => __( "Open Book", 'DFLIP' ),
              'title' => 'std',
              'desc'  => 'Default text in lightboxes Popup button or images if the flipbook title is not available.'
          ),
          
          'external_translate' => array(
              'std'     => 'false',
              'choices' => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Use External Translate',
              'desc'    => 'Use translations from other plugins and skip the translate from dFlip settings. <br><strong>NOTE: Below translations will not works when External Translate is used/enabled!!</strong>'
          ),
          
          'viewerType'                 => array(
              'std'     => 'flipbook',
              'choices' => array(
                  'global'   => __( 'Global Setting', 'DFLIP' ),
                  'reader'   => __( 'Vertical Reader', 'DFLIP' ),
                  'flipbook' => __( 'Flipbook', 'DFLIP' ),
                  'slider'   => __( 'Slider', 'DFLIP' )
              ),
              'title'   => __( 'Viewer Type', 'DFLIP' ),
              'desc'    => __( 'Choose the Viewer Type. Flipbook or normal viewer', 'DFLIP' )
          ),
          'mobileViewerType'           => array(
              'std'     => 'auto',
              'choices' => array(
                  'auto'     => __( 'Same as Viewer Type', 'DFLIP' ),
                  'reader'   => __( 'Vertical Reader', 'DFLIP' ),
                  'flipbook' => __( 'Flipbook', 'DFLIP' ),
                  'slider'   => __( 'Slider', 'DFLIP' )
              ),
              'title'   => 'Mobile Viewer Type',
              'desc'    => 'Choose the fallback Mobile Viewer Type. "Same as Viewer Type" will follow individual post setting "Viewer Type".'
          ),
          'more_controls'              => array(
              'std'         => "download,pageMode,startPage,endPage,sound",
              'title'       => 'More Controls - CASE SENSITIVE',
              'desc'        => 'Names of Controls in more Control Bar<br><code>altPrev, pageNumber, altNext, outline, thumbnail, zoomIn, zoomOut, fullScreen,share, more, download,pageMode,startPage,endPage,sound</code>',
              'placeholder' => '',
              'type'        => 'textarea'
          ),
          'hide_controls'              => array(
              'std'         => "",
              'title'       => 'Hide Controls - CASE SENSITIVE',
              'desc'        => 'Names of controls to be hidden.. ',
              'placeholder' => '',
              'type'        => 'textarea'
          ),
          'leftControls'               => array(
              'std'         => "outline,thumbnail",
              'title'       => 'Left Controls - CASE SENSITIVE',
              'desc'        => 'Names of Controls to be in left side.. ',
              'placeholder' => '',
              'type'        => 'textarea',
              'condition'   => 'dflip_controlsFloating:is(false)'
          ),
          'rightControls'              => array(
              'std'         => "fullScreen,share,download,more",
              'title'       => 'Right Controls - CASE SENSITIVE',
              'desc'        => 'Names of Controls to be in right side.. ',
              'placeholder' => '',
              'type'        => 'textarea',
              'condition'   => 'dflip_controlsFloating:is(false)'
          ),
          'hideShareControls'          => array(
              'std'         => "",
              'title'       => 'Hide Share Controls - CASE SENSITIVE',
              'desc'        => 'Names of share controls to be hidden.. <br>Valid names:<code>facebook,twitter,mail,whatsapp,linkedin,pinterest</code>',
              'placeholder' => '',
              'type'        => 'textarea'
          ),
          'scroll_wheel'               => array(
              'std'     => 'false',
              'choices' => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'title'   => 'Enable Zoom on Scroll',
              'desc'    => 'Select if zoom on mouse scroll should be active.'
          ),
          '2dFlipbookMidShadowOpacity' => array(
              'std'         => 0.5,
              'title'       => 'Mid Shadow darkness for 2D Flipbook',
              'desc'        => 'Mid Shadow Darkness for 2D flipbook',
              'placeholder' => 'Example: 0.5',
              'type'        => 'number',
              'attr'        => array(
                  'step' => 0.1,
                  'min'  => 0,
                  'max'  => 1
              )
          ),
          'bg_color'                   => array(
              'std'         => "transparent",
              'title'       => 'Background Color',
              'desc'        => 'Background color in hexadecimal format eg:<code>#FFF</code> or <code>#666666</code>',
              'placeholder' => 'Example: #ffffff',
              'type'        => 'text',
              'class'       => 'df-color-alpha-input'
          ),
          'bg_image'                   => array(
              'std'            => "",
              'class'          => '',
              'title'          => 'Background Image',
              'desc'           => 'Background image JPEG or PNG format:',
              'placeholder'    => 'Select an image',
              'type'           => 'upload',
              'class'          => 'df-upload-preview',
              'button-tooltip' => 'Select Background Image',
              'button-text'    => 'Select Image'
          ),
          'height'                     => array(
              'std'         => "auto",
              'title'       => 'Container Height',
              'desc'        => 'Height of the flipbook container when in normal mode.<br> <code>500</code>for 500px <br> <code>auto</code>for autofit height <br> <code>100%</code>for 100% height (of parent element, else it will be 320px).',
              'placeholder' => 'Example: 500',
              'type'        => 'text'
          ),
          'padding_top'                => array(
              'std'         => "20",
              'title'       => 'Padding Top',
              'desc'        => 'Gap between book and top-side of container.',
              'placeholder' => 'Example: 50',
              'type'        => 'number'
          ),
          'padding_bottom'             => array(
              'std'         => "20",
              'title'       => 'Padding Bottom',
              'desc'        => 'Gap between book and bottom-side of container.',
              'placeholder' => 'Example: 50',
              'type'        => 'number'
          ),
          'padding_left'               => array(
              'std'         => "20",
              'title'       => 'Padding Left',
              'desc'        => 'Gap between book and left-side of container.',
              'placeholder' => 'Example: 50',
              'type'        => 'number'
          ),
          'padding_right'              => array(
              'std'         => "20",
              'title'       => 'Padding Right',
              'desc'        => 'Gap between book and right-side of container.',
              'placeholder' => 'Example: 50',
              'type'        => 'number'
          ),
          'duration'                   => array(
              'std'         => 800,
              'class'       => '',
              'title'       => 'Flip Duration',
              'desc'        => 'Time in milliseconds eg:<code>1000</code>for 1second',
              'placeholder' => 'Example: 1000',
              'type'        => 'number'
          ),
          'zoom_ratio'                 => array(
              'std'         => 1.5,
              'title'       => 'Zoom Ratio',
              'desc'        => 'Multiplier for zoom recommended (1.1 - 2)',
              'placeholder' => 'Example: 1.5',
              'type'        => 'number',
              'attr'        => array(
                  'step' => 0.1,
                  'min'  => 1,
                  'max'  => 20
              )
          ),
          'fakeZoom'                 => array(
              'std'         => 1,
              'title'       => 'Psuedo Zoom(Fake Zoom)',
              'desc'        => 'This is extra zoom without generating higher resolution page. Only stretches the page.',
              'placeholder' => 'Example: 2',
              'type'        => 'number',
              'attr'        => array(
                  'step' => 1,
                  'min'  => 1,
                  'max'  => 3
              )
          ),
          'flexibility'                => array(
              'std'         => 1,
              'title'       => '3D Paper Flexibility',
              'desc'        => '<code>0</code> makes pages flat, greater values pages curvier and flexible, Recommended 1-2, or 0 ',
              'placeholder' => 'Example: 1',
              'type'        => 'number',
              'attr'        => array(
                  'step' => 0.1,
                  'min'  => 0,
                  'max'  => 10
              )
          ),
          'auto_sound'                 => array(
              'std'     => 'true',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Auto Enable Sound',
              'desc'    => 'Sound will play from the start.'
          ),
          'enable_download'            => array(
              'std'       => 'true',
              'choices'   => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'     => '',
              'title'     => 'Enable PDF Download',
              'condition' => 'dflip_source_type:is(pdf)'
          ),
          'enable_search'              => array(
              'std'       => 'false',
              'choices'   => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'     => '',
              'title'     => 'Enable PDF Search',
              'condition' => 'dflip_source_type:is(pdf)'
          ),
          'enable_print'               => array(
              'std'       => 'false',
              'choices'   => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'     => '',
              'title'     => 'Enable PDF Print',
              'condition' => 'dflip_source_type:is(pdf)'
          ),
          'enable_analytics'           => array(
              'std'     => 'false',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Enable Analytics',
              'desc'    => 'Enable Google Analytics. Analytics code should be added to site before ths can be used.'
          ),
          'hashNavigationEnabled'           => array(
              'std'     => 'false',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Enable Hash Navigation',
              'desc'    => 'When page changes, the URL is auto updated with the page number. Make sure there is no other hash conflict with hash functions.'
          ),
          'webgl'                      => array(
              'std'     => 'true',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'WebGL 3D', 'DFLIP' ),
                  'false'  => __( 'CSS 3D/2D', 'DFLIP' ),
                  'flat3D' => __( 'Hybrid 3D', 'DFLIP' )
              ),
              'title'   => '3D or 2D',
              'desc'    => 'Choose the mode of display. WebGL for realistic 3d'
          ),
          'hard'                       => array(
              'std'     => 'none',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'cover'  => __( 'Cover Pages', 'DFLIP' ),
                  'all'    => __( 'All Pages', 'DFLIP' ),
                  'none'   => __( 'None', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Hard Pages',
              'desc'    => 'Choose which pages to act as hard.(Only in CSS mode)'
          ),
          'direction'                  => array(
              'std'     => 1,
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  1        => __( 'Left to Right', 'DFLIP' ),
                  2        => __( 'Right to Left', 'DFLIP' )
              ),
              'title'   => 'Direction',
              'desc'    => 'Left to Right or Right to Left.'
          ),
          'source_type'                => array(
              'std'     => 'pdf',
              'choices' => array(
                  'pdf'   => __( 'PDF File', 'DFLIP' ),
                  'image' => __( 'Images', 'DFLIP' )
              ),
              'title'   => 'Book Source Type',
              'desc'    => 'Choose the source of this book. "PDF" for pdf files. "Images" for image files.'
          ),
          'pdf_source'                 => array(
              'std'            => "",
              'title'          => 'PDF File',
              'desc'           => 'Choose a PDF File to use as source for the book.',
              'placeholder'    => 'Select a PDF File',
              'type'           => 'upload',
              'button-tooltip' => 'Select a PDF File',
              'button-text'    => 'Select PDF',
              'condition'      => 'dflip_source_type:is(pdf)',
              'class'          => 'hide-on-fail'
          ),
          'pdf_thumb'                  => array(
              'std'            => "",
              'title'          => 'PDF Thumbnail Image',
              'desc'           => 'Choose an image file for PDF thumb.',
              'placeholder'    => 'Select an image',
              'type'           => 'upload',
              'button-tooltip' => 'Select PDF Thumb Image',
              'button-text'    => 'Select Thumb',
              'condition'      => 'dflip_source_type:is(pdf)',
              'class'          => 'hide-on-fail'
          ),
          'popupThumbPlaceholder'      => array(
              'std'            => "",
              'title'          => 'PopUp Thumbnail Default',
              'desc'           => 'Choose an image file for Default image in thumb popup',
              'placeholder'    => 'Select an image',
              'type'           => 'upload',
              'class'          => 'df-upload-preview',
              'button-tooltip' => 'PopUp Thumbnail Default',
              'button-text'    => 'Select Image'
          ),
          'overwrite_outline'          => array(
              'std'       => 'false', //isset mis-interprets 0 and false differently than expected
              'choices'   => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'class'     => '',
              'title'     => 'Overwrite PDF Outline',
              'desc'      => 'Choose if PDF Outline will overwritten.',
              'condition' => 'dflip_source_type:is(pdf)'
          ),
          'auto_outline'               => array(
              'std'     => 'false', //isset mis-interprets 0 and false differently than expected
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Auto Enable Outline',
              'desc'    => 'Choose if outline will be auto enabled on start.'
          ),
          'auto_thumbnail'             => array(
              'std'     => 'false', //isset mis-interprets 0 and false differently than expected
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Auto Enable Thumbnail',
              'desc'    => 'Choose if thumbnail will be auto enabled on start.Note : Either thumbnail or outline will be active at a time.)'
          ),
          'page_mode'                  => array(
              'std'     => '0',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  '0'      => __( 'Auto', 'DFLIP' ),
                  '1'      => __( 'Single Page', 'DFLIP' ),
                  '2'      => __( 'Double Page', 'DFLIP' ),
              ),
              'class'   => '',
              'title'   => 'Page Mode',
              'desc'    => 'Choose whether you want single mode or double page mode. Recommended Auto'
          ),
          
          'page_size'         => array(
              'std'     => '0',
              'choices' => array(
                  '0' => __( 'Auto', 'DFLIP' ),
                  '1' => __( 'Single Page', 'DFLIP' ),
                  '2' => __( 'Double Internal Page', 'DFLIP' ),
              ),
              'class'   => '',
              'title'   => 'Page Size',
              'desc'    => 'Choose whether Layout is single page mode or double internal. Recommended Auto if PDF file'
          ),
          'page_scale'        => array(
              'std'     => 'fit',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'auto'   => __( 'Auto', 'DFLIP' ),
                  'fit'    => __( 'Page Fit', 'DFLIP' ),
                  'width'  => __( 'Page Width', 'DFLIP' ),
                  'actual' => __( 'Actual', 'DFLIP' ),
              ),
              'class'   => '',
              'title'   => 'Reader Page Scale',
              'desc'    => 'Choose how the page scale is fit to the Reader viewer area.'
          ),
          'single_page_mode'  => array(
              'std'     => '0',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  '0'      => __( 'Auto', 'DFLIP' ),
                  '1'      => __( 'Normal Zoom', 'DFLIP' ),
                  '2'      => __( 'Booklet Mode', 'DFLIP' ),
              ),
              'class'   => '',
              'title'   => 'Single Page Mode',
              'desc'    => 'Choose how the single page will behave. If set to Auto, then in mobiles single page mode will be in Booklet mode.'
          ),
          'controls_position' => array(
              'std'     => 'bottom',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'bottom' => __( 'Bottom', 'DFLIP' ),
                  'top'    => __( 'Top', 'DFLIP' ),
                  'hide'   => __( 'Hidden', 'DFLIP' ),
              ),
              'class'   => '',
              'title'   => 'Controls Position',
              'desc'    => 'Choose where you want to display the controls bar or not display at all.'
          ),
          'controlsFloating'  => array(
              'std'     => 'true',
              'choices' => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Controls Floating',
              'desc'    => 'Choose if the controls are floating or spread out'
          ),
          'texture_size'      => array(
              'std'     => '1600',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  '1024'   => '1024 px',
                  '1400'   => '1400 px',
                  '1600'   => '1600 px',
                  '1800'   => '1800 px',
                  '2048'   => '2048 px',
                  '3200'   => '3200 px',
                  '4096'   => '4096 (caution)',
              ),
              'class'   => '',
              'title'   => 'Page Render Size',
              'desc'    => 'Choose the size of image to be generated.',
          ),
          'link_target'       => array(
              'std'     => '2',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  '1'      => "Same Tab",
                  '2'      => "New Tab"
              ),
              'class'   => '',
              'title'   => 'PDF Links Open Target',
              'desc'    => 'Open links inside PDF page in same tab/window or new tab.',
          ),
          'share_prefix'      => array(
              'std'         => "flipbook-",
              'title'       => 'Share: Prefix',
              'desc'        => 'List of share prefix to support, separated by comma. First prefix is actively used to share, older are used for backward compatibility with older prefix, dflip- is used if not set',
              'placeholder' => 'Example: book-,flipbook-',
              'type'        => 'text'
          ),
          
          'share_slug' => array(
              'std'     => 'false',
              'choices' => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Share: Use post slugs',
              'desc'    => 'While sharing the flipbook, Post slug will be used instead of Post id. <strong>Flipbook share links won\'t work if the slug is changed in future</strong>'
          ),
          
          'attachment_lightbox' => array(
              'std'     => 'false',
              'choices' => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Single Post Lightbox',
              'desc'    => 'When opening dFlip single post page or attachment page for PDF, display lightbox instead of embedded flipbook'
          ),
          
          'range_size' => array(
              'std'     => '524288',
              'choices' => array(
                  'global'  => __( 'Global Setting', 'DFLIP' ),
                  '65536'   => '64KB',
                  '131072'  => '128KB',
                  '262144'  => '256KB',
                  '524288'  => '512KB',
                  '1048576' => '1024KB'
              ),
              'title'   => 'PDF Partial Loading Chunk Size',
              'desc'    => 'Choose the size chunk size to be loaded on demand'
          ),
          'autoplay'   => array(
              'std'     => 'false',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Enable AutoPlay',
              'desc'    => 'Enable AutoPlay in Flipbook'
          ),
          
          'disable_range' => array(
              'std'     => 'false',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Disable Partial Loading',
              'desc'    => 'Partial loading, Chunk issues are discovered in some servers and browsers. Disabling range can fix that issue.'
          ),
          
          'autoplay_start'    => array(
              'std'     => 'false',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Enable AutoPlay Automatically',
              'desc'    => 'Enable AutoPlay automatically when flipbook loads. Note: Sound only works when user starts to interact with web page!'
          ),
          'autoplay_duration' => array(
              'std'         => 5000,
              'class'       => '',
              'title'       => 'Autoplay Duration',
              'desc'        => 'Time in milliseconds eg:<code>1000</code>for 1second',
              'placeholder' => 'Example: 5000',
              'type'        => 'number'
          ),
          'thumb_tag_type'    => array(
              'std'     => 'bg',
              'choices' => array(
                  'bg'  => __( 'Div Background', 'DFLIP' ),
                  'img' => __( 'Image', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Book Thumb Type',
              'desc'    => 'Choose Div Background for uniform thumb size, Image for adaptive size.'
          ),
          'pdf_version'       => array(
              'std'     => 'default',
              'choices' => array(
                  'default' => 'Default Version (3.7.107)',
                  'latest'  => 'Latest (3.7.107)',
                  'stable'  => 'Stable Candidate (2.5.207)'
              ),
              'title'   => 'PDF.js Version',
              'desc'    => 'Choose which version of PDF.js rendering Engine to use.'
          ),
          'pages'             => array()
      );
      $this->defaults['cover3DType'] = array(
          'std'     => 'none',
          'choices' => array(
              'global' => 'Global Setting',
              'none'   => 'None',
              'plain'  => 'Plain Flat',
              'basic'  => 'Basic',
              'ridge'  => 'Ridge',
          ),
          'title'   => 'Enable 3D Cover',
          'desc'    => 'Enable 3D Thick cover. Happens only when there are more than 10 pages.'
      );
      $this->defaults['color3DCover'] = array(
          'std'         => "#aaaaaa",
          'title'       => '3D Book Cover Color',
          'desc'        => 'Color in hexadecimal format eg:<code>#FFF</code> or <code>#666666</code>',
          'placeholder' => 'Example: #aaaaaa',
          'type'        => 'text',
          'class'       => 'df-color-input'
      );
      $this->defaults['color3DSheets'] = array(
          'std'         => "#fff",
          'title'       => '3D Book Pages Color',
          'desc'        => 'Color in hexadecimal format eg:<code>#FFF</code> or <code>#666666</code>',
          'placeholder' => 'Example: #fff',
          'type'        => 'text',
          'class'       => 'df-color-input'
      );
      
      $this->defaults['spiralColor'] = array(
          'std'         => "#eee",
          'title'       => '3D Spiral color',
          'desc'        => 'Color in hexadecimal format eg:<code>#FFF</code> or <code>#666666</code>',
          'placeholder' => 'Example: #fff',
          'type'        => 'text',
          'class'       => 'df-color-input'
      );
      $this->defaults['hasSpiral'] = array(
          'std'     => 'false',
          'choices' => array(
              'global' => __( 'Global Setting', 'DFLIP' ),
              'true'   => 'True',
              'false'  => 'False',
          ),
          'title'   => 'Has Spiral in 3D Book',
          'desc'    => '3D Book spiral, Hybrid 3D book doesn\'t support spiral.',
      );
      $this->defaults['calendarMode'] = array(
          'std'     => 'false',
          'choices' => array(
              'global' => __( 'Global Setting', 'DFLIP' ),
              'true'   => 'True',
              'false'  => 'False',
          ),
          'title'   => 'Enable Calendar Mode',
          'desc'    => 'Calendar Layout - Vertical flipbook. Best for Landscape layouts',
      );
      //not yet ready
      $this->defaults['flipbook3DTiltAngleUp'] = array(
          'std'         => 0,
          'title'       => '3D Flipbook Up Tilt',
          'desc'        => '',
          'placeholder' => 'Example: 1',
          'type'        => 'number',
          'attr'        => array(
              'step' => 1,
              'min'  => 0,
              'max'  => 45
          ),
          'desc'        => '3D Book Upward tilt Angle. Book will lean backwards. Will increase Distortion.'
      );
      //not yet ready
      $this->defaults['flipbook3DTiltAngleLeft'] = array(
          'std'         => 0,
          'title'       => '3D Flipbook Rotate',
          'desc'        => '',
          'placeholder' => 'Example: 1',
          'type'        => 'number',
          'attr'        => array(
              'step' => 1,
              'min'  => -90,
              'max'  => 90
          ),
          'desc'        => '3D Book Rotation. Book will rotate left for negative value, right for positive value. Will increase Distortion.'
      );
      $this->defaults['canvasWillReadFrequently'] = array(
          'std'     => 'true',
          'choices' => array(
              'true'  => 'True',
              'false' => 'False',
          ),
          'title'   => 'Disable Canvas Hardware Acceleration',
          'desc'    => 'If set to True, disable harware accelartion when redering canvas. Sometimes fixes issues when there are browser and driver issues.',
      );
      
      $this->defaults['sideMenuOverlay'] = array(
          'std'     => 'true',
          'choices' => array(
              'true'  => 'True',
              'false' => 'False',
          ),
          'title'   => 'Overlay SideMenu',
          'desc'    => 'If set to True, Table of contents and Thumbnail will float above the viewer.',
      );
      
      $this->defaults['thumbLayout'] = array(
          'std'     => 'book-title-hover',
          'choices' => array(
              'book-title-hover'  => 'Title on Hover',
              'book-title-fixed'  => 'Title Fixed',
              'book-title-bottom' => 'Title Bottom (Beta)',
              'book-title-top' => 'Title Top (Beta)',
              //              'cover-title'       => 'Flat Cover with Title Bottom (Beta)'
          ),
          'title'   => 'Thumb Popup Display',
          'desc'    => 'Select the layout of thumb popup display. Note: Beta can change in future!'
      );
      $this->defaults['displayLightboxPlayIcon'] = array(
          'std'     => 'false',
          'choices' => array(
              'true'  => 'True',
              'false' => 'False',
          ),
          'title'   => 'Thumb Popup Play icon',
          'desc'    => 'If set to True, a play icon will be displayed over Thumb popup',
      );
      
      $this->defaults['lightboxPlayIconBGColor'] = array(
          'std'         => "#777",
          'title'       => 'Thumb Popup Play Icon Background Color',
          'desc'        => '',
          'placeholder' => 'Example: #777',
          'type'        => 'text',
          'class'       => 'df-color-alpha-input'
      );
      
      $this->defaults['lightboxPlayIconColor'] = array(
          'std'         => "#fff",
          'title'       => 'Thumb Popup Play Icon Color',
          'desc'        => '',
          'placeholder' => 'Example: #fff',
          'type'        => 'text',
          'class'       => 'df-color-alpha-input'
      );
      
      $this->defaults['popupBackGroundColor'] = array(
          'std'         => "#eee",
          'title'       => 'Popup Background Color',
          'desc'        => 'Fallback background color for popup in hexadecimal format eg:<code>#FFF</code> or <code>#666666</code>. Viewer Background is used if available',
          'placeholder' => 'Example: #eee',
          'type'        => 'text',
          'class'       => 'df-color-alpha-input'
      );
      
      $this->defaults['popupBackgroundColorOpacity'] = array(
          'std'         => 1,
          'title'       => 'Popup Background Opacity',
          'desc'        => 'Opacity for Popup Background',
          'placeholder' => 'Example: 0.1',
          'type'        => 'number',
          'attr'        => array(
              'step' => 0.1,
              'min'  => 0,
              'max'  => 1
          )
      );
      
      $this->defaults['shelfImage'] = array(
          'std'         => "",
          'title'       => 'Book Shelf Custom Image',
          'desc'        => 'Choose an image file for Book shelf',
          'placeholder' => 'Select an image',
          'type'        => 'upload',
          'class'       => 'df-upload-preview',
          'button-text' => 'Select Image'
      );
      
      $this->defaults['linkColor'] = array(
          'std'         => "#ff0",
          'title'       => 'Link Color',
          'desc'        => 'Highlight color for the link in flipbook pages',
          'placeholder' => 'Example: #ff0',
          'type'        => 'text',
          'class'       => 'df-color-input'
      );
      $this->defaults['linkColorOpacity'] = array(
          'std'         => 0.2,
          'title'       => 'Link Color Opacity',
          'desc'        => 'Highlight color opacity for the link in flipbook pages. 0 makes full transparent, 1 makes full opaque highlight.',
          'placeholder' => 'Example: 0.1',
          'type'        => 'number',
          'attr'        => array(
              'step' => 0.1,
              'min'  => 0,
              'max'  => 1
          )
      );
      $this->defaults['linkColorHover'] = array(
          'std'         => "#2196F3",
          'title'       => 'Link Color Hover',
          'desc'        => 'Highlight color for the link during mouse hover in flipbook pages',
          'placeholder' => 'Example: #ff0',
          'type'        => 'text',
          'class'       => 'df-color-input'
      );
      $this->defaults['linkColorHoverOpacity'] = array(
          'std'         => 0.5,
          'title'       => 'Link Color Hover Opacity',
          'desc'        => 'Highlight color opacity for the link on mouse hover in flipbook pages. 0 makes full transparent, 1 makes full opaque highlight.',
          'placeholder' => 'Example: 0.4',
          'type'        => 'number',
          'attr'        => array(
              'step' => 0.1,
              'min'  => 0,
              'max'  => 1
          )
      );
      $this->defaults['multiplePostLimit'] = array(
          'std'         => 5,
          'title'       => 'Posts limit when using multiple post/category shortcode',
          'desc'        => '<code>0</code> or <code>-1</code> will display all items',
          'placeholder' => 'Example: 5',
          'type'        => 'number',
          'attr'        => array(
              'step' => 1,
              'min'  => -1,
              'max'  => 100
          )
      );
      //skiping this in 2.1 cause maybe we can use sliders.
      $this->defaults['loadMoreCount'] = array(
          'std'         => 5,
          'title'       => 'Post display by pagination',
          'desc'        => '<code>0</code> or <code>-1</code> will display all items',
          'placeholder' => 'Example: 5',
          'type'        => 'number',
          'attr'        => array(
              'step' => 1,
              'min'  => -1,
              'max'  => 100
          )
      );
      
      $this->defaults['enablePostPages'] = array(
          'std'     => 'false',
          'choices' => array(
              'true'  => 'True (Enable)',
              'false' => 'False (Disable)',
          ),
          'title'   => 'Enable dFlip Post Pages',
          'desc'    => 'Post page and attachment PDF pages are converted to flipbook.<br><strong>NOTE: Goto Permalinks and click on save after changing this value.</strong>',
      );
      
      //todo framename	Opens the linked document in the named iframe
      //this can be used to replace flipbook in a selected frame, creating switchig flipbooks in a page - same as lightbox though.
      $this->defaults['targetWindow'] = array(
          'std'     => '_popup',
          'choices' => array(
              '_popup' => 'Popup(Same Page)',
              '_self'  => 'Same Tab/Window (New Page)',
              '_blank' => 'New Tab',
          ),
          'title'   => 'Popup Target',
          'desc'    => 'Open Flipbook in a popup, new page or new tab. This requires <strong>Advanced-> Enable dFlip Post Pages</strong> to be enabled',
      );
      
      $this->defaults['buttonClass'] = array(
          'std'         => "",
          'title'       => 'Button ClassNames',
          'desc'        => '',
          'placeholder' => '',
          'type'        => 'text',
          'desc'        => 'Classname can be used to match style to your theme. <code>button button-primary</code> should work in most cases/themes.',
      );
      
      
      $this->defaults['enableAutoLinks'] = array(
          'std'     => 'true',
          'choices' => array(
              'true'  => 'True (Enable)',
              'false' => 'False (Disable)',
          ),
          'title'   => 'Convert URL Text to Links',
          'desc'    => 'Tries to detect PDF text that are like proper links and converts them to link.',
      );
      $this->defaults['selectiveScriptLoading'] = array(
          'std'     => 'false',
          'choices' => array(
              'true'  => 'True (Enable)',
              'false' => 'False (Disable)',
          ),
          'title'   => 'Selective Script Loading',
          'desc'    => 'Load Scripts only on pages where shortcodes are added. May not work properly in AJAX based themes. Also clear your CACHE PLUGIN CACHE!',
      );
      $this->defaults['autoPDFLinktoViewer'] = array(
          'std'     => 'false',
          'choices' => array(
              'true'  => 'True',
              'false' => 'False',
          ),
          'title'   => 'Auto convert PDF links on page to viewers',
          'desc'    => 'If the link is set to open in new tab or download, conversion will not happen.'
      );
      
      $this->selective_script_loading = $this->get_config( 'selectiveScriptLoading' ) == "true";
      $external_translate = $this->get_config( 'external_translate' );
      $this->external_translate = $external_translate == "true";
      $this->shelf_image = $this->get_shelf_image( $this->get_config( 'shelfImage' ) );
      
      // Load admin only components.
      if ( is_admin() && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        $this->init_admin();
      } else { // Load frontend only components.
        $this->init_front();
      }
      
      // Load global components.
      $this->init_global();
      
    }
    
    /**
     * Loads all admin related files into scope.
     *
     * @since 1.0.0
     */
    public function init_admin() {
      
      include_once( dirname( __FILE__ ) . '/inc/settings.php' );
      
      //include the metaboxes file
      include_once dirname( __FILE__ ) . "/inc/metaboxes.php";
      
    }
    
    /**
     * Loads all frontend user related files
     *
     * @since 1.0.0
     */
    public function init_front() {
      
      //include the shortcode parser
      include_once dirname( __FILE__ ) . "/inc/shortcode.php";
      
      //include the scripts and styles for front end
      add_action( 'wp_enqueue_scripts', array( $this, 'init_front_scripts' ) );
      
      //some custom js that need to be passed
      add_action( 'wp_print_footer_scripts', array( $this, 'hook_script' ) );
      
    }
    
    /**
     * Loads all global files into scope.
     *
     * @since 1.0.0
     */
    public function init_global() {
      
      //include the post-type that manages the custom post
      include_once dirname( __FILE__ ) . '/inc/post-type.php';
      
      include_once( dirname( __FILE__ ) . '/inc/migration.php' );
      
    }
    
    /**
     * Loads all script and style sheets for frontend into scope.
     *
     * @since 1.0.0
     */
    public function init_front_scripts() {
      
      //register scripts and style
      wp_register_script( $this->plugin_slug . '-script', plugins_url( 'assets/js/dflip.min.js', __FILE__ ), array( "jquery" ), $this->version, true );
      wp_register_style( $this->plugin_slug . '-style', plugins_url( 'assets/css/dflip.min.css', __FILE__ ), array(), $this->version );
      
      if ( $this->selective_script_loading != true ) {
        //enqueue scripts and style
        wp_enqueue_script( $this->plugin_slug . '-script' );
        wp_enqueue_style( $this->plugin_slug . '-style' );
      }
      
    }
    
    public function add_defer_attribute( $tag, $handle ) {
      // add script handles to the array below
      //cache for plugin_slug
      $_slug = $this->plugin_slug;
      $scripts_to_defer = array( 'jquery-core', $_slug . '-script', $_slug . '-parse-script' );
      
      foreach ( $scripts_to_defer as $defer_script ) {
        if ( $defer_script === $handle ) {
          return str_replace( ' src', ' data-cfasync="false" src', $tag );
        }
      }
      
      return $tag;
    }
    
    /**
     * Registers a javascript variable into HTML DOM for url access
     *
     * @since 1.0.0
     */
    public function hook_script() {
      
      $data = array(
          'text'                => array(
              'toggleSound'      => $this->get_translate( 'text_toggle_sound' ),
              'toggleThumbnails' => $this->get_translate( 'text_toggle_thumbnails' ),
              'thumbTitle'       => $this->get_translate( 'text_thumbnails_title' ),
              'outlineTitle'     => $this->get_translate( 'text_outline_title' ),
              'searchTitle'      => $this->get_translate( 'text_search_title' ),
              'searchPlaceHolder'=> $this->get_translate( 'text_search_placeholder' ),
              'searchClear'          => $this->get_translate( 'text_search_clear' ),
              'searchSearchingInfo'  => $this->get_translate( 'text_search_searching_info' ),
              'searchResultsFound'   => $this->get_translate( 'text_search_results_found' ),
              'searchResultsNotFound'=> $this->get_translate( 'text_search_results_not_found' ),
              'searchResultPage'     => $this->get_translate( 'text_search_result_page' ),
              'searchResult'         => $this->get_translate( 'text_search_result' ),
              'searchResults'        => $this->get_translate( 'text_search_results' ),
              'searchMinimum'        => $this->get_translate( 'text_search_minimum' ),
              'play'                 => $this->get_translate( 'text_play' ),
              'pause'                => $this->get_translate( 'text_pause' ),
              'toggleOutline'    => $this->get_translate( 'text_toggle_outline' ),
              'previousPage'     => $this->get_translate( 'text_previous_page' ),
              'nextPage'         => $this->get_translate( 'text_next_page' ),
              'toggleFullscreen' => $this->get_translate( 'text_toggle_fullscreen' ),
              'zoomIn'           => $this->get_translate( 'text_zoom_in' ),
              'zoomOut'          => $this->get_translate( 'text_zoom_out' ),
              'toggleHelp'       => $this->get_translate( 'text_toggle_help' ),
              'singlePageMode'   => $this->get_translate( 'text_single_page_mode' ),
              'doublePageMode'   => $this->get_translate( 'text_double_page_mode' ),
              'downloadPDFFile'  => $this->get_translate( 'text_download_PDF_file' ),
              'gotoFirstPage'    => $this->get_translate( 'text_goto_first_page' ),
              'gotoLastPage'     => $this->get_translate( 'text_goto_last_page' ),
              'share'            => $this->get_translate( 'text_share' ),
              'search'           => $this->get_translate( 'text_search' ),
              'print'            => $this->get_translate( 'text_print' ),
              'mailSubject'      => $this->get_translate( 'text_mail_subject' ),
              'mailBody'         => $this->get_translate( 'text_mail_body' ),
              'loading'          => $this->get_translate( 'text_loading' )
          ),
          'viewerType'          => $this->get_config( 'viewerType' ),
          'mobileViewerType'    => $this->get_config( 'mobileViewerType' ),
          'moreControls'        => $this->get_config( 'more_controls' ),
          'hideControls'        => $this->get_config( 'hide_controls' ),
          'leftControls'        => $this->get_config( 'leftControls' ),
          'rightControls'       => $this->get_config( 'rightControls' ),
          'hideShareControls'   => $this->get_config( 'hideShareControls' ),
          'scrollWheel'         => $this->get_config( 'scroll_wheel' ),
          'backgroundColor'     => $this->get_config( 'bg_color' ),
          'backgroundImage'     => $this->get_config( 'bg_image' ),
          'height'              => $this->get_config( 'height' ),
          'paddingTop'          => $this->get_config( 'padding_top' ),
          'paddingBottom'       => $this->get_config( 'padding_bottom' ),
          'paddingLeft'         => $this->get_config( 'padding_left' ),
          'paddingRight'        => $this->get_config( 'padding_right' ),
          'controlsPosition'    => $this->get_config( 'controls_position' ),
          'controlsFloating'    => $this->get_config( 'controlsFloating' ) == "true",
          'direction'           => $this->get_config( 'direction' ),
          'duration'            => $this->get_config( 'duration' ),
          'soundEnable'         => $this->get_config( 'auto_sound' ),
          'showDownloadControl' => $this->get_config( 'enable_download' ),
          'showSearchControl'   => $this->get_config( 'enable_search' ),
          'showPrintControl'    => $this->get_config( 'enable_print' ),
          'hashNavigationEnabled'     => $this->get_config( 'hashNavigationEnabled' ) == "true",
          'enableAnalytics'     => $this->get_config( 'enable_analytics' ) == "true",
          'webgl'               => $this->get_config( 'webgl' ),
          'hard'                => $this->get_config( 'hard' ),
          'autoEnableOutline'   => $this->get_config( 'auto_outline' ),
          'autoEnableThumbnail' => $this->get_config( 'auto_thumbnail' ),
          'pageScale'           => $this->get_config( 'page_scale' ),
          'maxTextureSize'      => $this->get_config( 'texture_size' ),
          'rangeChunkSize'      => $this->get_config( 'range_size' ),
          'disableRange'        => $this->get_config( 'disable_range' ) == "true",
          'zoomRatio'           => $this->get_config( 'zoom_ratio' ),
          'fakeZoom'            => $this->get_config( 'fakeZoom' ),
          'flexibility'         => $this->get_config( 'flexibility' ),
          'pageMode'            => $this->get_config( 'page_mode' ),
          'singlePageMode'      => $this->get_config( 'single_page_mode' ),
          'pageSize'            => $this->get_config( 'page_size' ),
          'autoPlay'            => $this->get_config( 'autoplay' ),
          'autoPlayDuration'    => $this->get_config( 'autoplay_duration' ),
          'autoPlayStart'       => $this->get_config( 'autoplay_start' ),
          'linkTarget'          => $this->get_config( 'link_target' ),
          'sharePrefix'         => $this->get_config( 'share_prefix' ),
          'pdfVersion'          => $this->get_config( 'pdf_version' ),
          'thumbLayout'         => $this->get_config( 'thumbLayout' ),
          //          'loadMoreCount'       => $this->get_config( 'loadMoreCount' ),
          'targetWindow'        => $this->get_config( 'targetWindow' ),
          'buttonClass'         => $this->get_config( 'buttonClass' ),
      
      );
      
      
      $data['hasSpiral'] = $this->get_config( 'hasSpiral' ) == 'true';
      $data['calendarMode'] = $this->get_config( 'calendarMode' ) == 'true';
      $data['spiralColor'] = $this->get_config( 'spiralColor' );
      $data['cover3DType'] = $this->get_config( 'cover3DType' );
      $data['color3DCover'] = $this->get_config( 'color3DCover' );
      $data['color3DSheets'] = $this->get_config( 'color3DSheets' );
      $data['flipbook3DTiltAngleUp'] = $this->get_config( 'flipbook3DTiltAngleUp' ); //not yet ready
      $data['flipbook3DTiltAngleLeft'] = $this->get_config( 'flipbook3DTiltAngleLeft' );
      $data['autoPDFLinktoViewer'] = $this->get_config( 'autoPDFLinktoViewer' ) == "true";
      $data['sideMenuOverlay'] = $this->get_config( 'sideMenuOverlay' ) == "true";
      $data['displayLightboxPlayIcon'] = $this->get_config( 'displayLightboxPlayIcon' ) == "true";
      $data['popupBackGroundColor'] = $this->get_config( 'popupBackGroundColor' );
      $data['shelfImage'] = $this->shelf_image;
      
      $data['enableAutoLinks'] = $this->get_config( 'enableAutoLinks' ) == 'true';
      
      $popupThumbPlaceholder = $this->get_config( 'popupThumbPlaceholder' );
      if ( !empty( $popupThumbPlaceholder ) && trim( $popupThumbPlaceholder ) != '' ) {
        $data['popupThumbPlaceholder'] = $popupThumbPlaceholder;
      }
      
      //registers a variable that stores the location of plugin
      $output = '<script data-cfasync="false"> window.dFlipLocation = "' . plugins_url( 'assets/', __FILE__ ) . '"; window.dFlipWPGlobal = ' . json_encode( $data ) . ';</script>';
      
      $output .= '<style>';
      
      $output .= '.df-sheet .df-page:before { opacity: ' . $this->get_config( '2dFlipbookMidShadowOpacity' ) . ';}';
      
      $output .= 'section.linkAnnotation a, a.linkAnnotation, .buttonWidgetAnnotation a, a.customLinkAnnotation, .customHtmlAnnotation, .customVideoAnnotation, a.df-autolink{background-color: ' . $this->get_config( 'linkColor' ) . '; opacity: ' . $this->get_config( 'linkColorOpacity' ) . ';}
        section.linkAnnotation a:hover, a.linkAnnotation:hover, .buttonWidgetAnnotation a:hover, a.customLinkAnnotation:hover, .customHtmlAnnotation:hover, .customVideoAnnotation:hover, a.df-autolink:hover{background-color: ' . $this->get_config( 'linkColorHover' ) . '; opacity: ' . $this->get_config( 'linkColorHoverOpacity' ) . ';}';
      
      if ( $data['displayLightboxPlayIcon'] ) {
        $output .= '.df-icon-play-popup:before{background-color: ' . $this->get_config( 'lightboxPlayIconBGColor' ) . ';}
        .df-icon-play-popup:before{color: ' . $this->get_config( 'lightboxPlayIconColor' ) . ';}';
      }
      if ( $this->get_config( 'popupBackgroundColorOpacity' ) != 1 ) {
        $output .= '.df-lightbox-bg{opacity: ' . $this->get_config( 'popupBackgroundColorOpacity' ) . ';}         .df-lightbox-wrapper .df-bg {background-color: transparent;}';
      }
      $output .= '.df-container.df-transparent.df-fullscreen{background-color: ' . $data['popupBackGroundColor'] . ';}  ';
      
      $output .= '</style>';
      echo $output;
      
    }
    
    public function get_shelf_image( $val ) {
      //check if val is numeric
      if ( is_numeric( $val ) ) {
        $post = get_post( $val );
        if ( $post !== null && $post->post_mime_type !== null && strpos( $post->post_mime_type, 'image/' ) !== false ) {
          return wp_get_attachment_url( $post->ID );
        } else {
          return '';
        }
      }
      //if yes fetch the val of link from post
      
      return $val;
    }
    /**
     * Helper method for retrieving config values.
     *
     * @param string $key The config key to retrieve.
     *
     * @return string Key value on success, empty string on failure.
     * @since 1.2.6
     *
     */
    public function get_config( $key ) {
      
      $values = is_multisite() ? get_blog_option( null, '_dflip_settings', true ) : get_option( '_dflip_settings', true );
      $value = isset( $values[ $key ] ) ? $values[ $key ] : '';
      
      $default = $this->get_default( $key );
      
      /* set standard value */
      if ( $default !== null ) {
        $value = $this->filter_std_value( $value, $default );
      }
      
      return $value;
      
    }
    
    public function get_global_config( $key ) {
      return $this->get_config( $key );
    }
    
    
    /**
     * Helper method for retrieving global check values.
     *
     * @param string $key The config key to retrieve.
     *
     * @return string Key value on success, empty string on failure.
     * @since 1.0.0
     *
     */
    public function global_config( $key ) {//todo name is not proper
      
      $global_value = $this->get_global_config( $key );
      $value = isset( $this->defaults[ $key ] ) ? is_array( $this->defaults[ $key ] ) ? isset( $this->defaults[ $key ]['choices'][ $global_value ] )
          ? $this->defaults[ $key ]['choices'][ $global_value ] : $global_value : $global_value : $global_value;
      
      return $value; //returns Text not key value
      
    }
    
    public function get_translate( $key ) {
      if ( $this->external_translate == true ) {
        return $this->get_default( $key );
      } else {
        return $this->get_config( $key );
      }
    }
    
    /**
     * Helper method for retrieving default values.
     *
     * @param string $key The config key to retrieve.
     *
     * @return string Key value on success, empty string on failure.
     * @since 1.0.0
     *
     */
    public function get_default( $key ) {
      
      $default = isset( $this->defaults[ $key ] ) ? is_array( $this->defaults[ $key ] ) ? isset( $this->defaults[ $key ]['std'] ) ? $this->defaults[ $key ]['std'] : '' : $this->defaults[ $key ] : '';
      
      return $default;
      
    }
    
    /**
     * Helper function to filter standard option values.
     *
     * @param mixed $value Saved string or array value
     * @param mixed $std   Standard string or array value
     *
     * @return    mixed     String or array
     *
     * @access    public
     * @since     1.0.0
     */
    public function filter_std_value( $value = '', $std = '' ) {
      
      $std = maybe_unserialize( $std );
      
      if ( is_array( $value ) && is_array( $std ) ) {
        
        foreach ( $value as $k => $v ) {
          
          if ( '' === $value[ $k ] && isset( $std[ $k ] ) ) {
            
            $value[ $k ] = $std[ $k ];
            
          }
          
        }
        
      } else {
        if ( '' === $value && $std !== null ) {
          
          $value = $std;
          
        }
      }
      
      return $value;
      
    }
    
    /**
     * Helper function to create settings boxes
     *
     * @access    public
     *
     * @param        $key
     * @param null   $setting
     * @param null   $value
     * @param null   $global_key
     * @param string $global_value
     *
     * @since     1.2.6
     *
     */
    public function create_setting( $key, $setting = null, $value = null, $global_key = null, $global_value = '' ) {
      
      $slug = $this->plugin_slug;
      $setting = is_null( $setting ) ? $this->defaults[ $key ] : $setting;
      if ( is_null( $setting ) ) {
        echo "<!--    " . esc_html( $key ) . " Not found   -->";
        
        return;
      }
      $type = isset( $setting['type'] ) ? $setting['type'] : '';
      $value = is_null( $value ) ? $this->get_global_config( $key ) : $value;
      $condition = isset( $setting['condition'] ) ? $setting['condition'] : '';
      $class = isset( $setting['class'] ) ? $setting['class'] : '';
      $placeholder = isset( $setting['placeholder'] ) ? $setting['placeholder'] : '';
      $desc = isset( $setting['desc'] ) ? $setting['desc'] : '';
      $title = isset( $setting['title'] ) ? $setting['title'] : '';
      if ( $title == 'std' ) {//useful in translate settings
        $title = $this->get_default( $key );
      }
      $global_attr = !is_null( $global_key ) ? $global_key : "";
      $global_face_value = $global_value;
      
      echo '<div id="' . $slug . '_' . esc_attr( $key ) . '_box" class="df-box ' . esc_attr( $class ) . '" data-condition="' . esc_attr( $condition ) . '">
      <div class="df-label"><label for="' . $slug . '_' . esc_attr( $key ) . '" >
				' . esc_attr( $title ) . '
			</label></div>';
      echo '<div class="df-option">';
      if ( isset( $setting['choices'] ) && is_array( $setting['choices'] ) ) {
        
        echo '<div class="df-select">
				<select name="_' . $slug . '[' . esc_attr( $key ) . ']" id="' . $slug . '_' . esc_attr( $key ) . '" class="" data-global="' . esc_attr( $global_attr ) . '">';
        
        /** @noinspection PhpCastIsUnnecessaryInspection */
        foreach ( (array) $setting['choices'] as $val => $label ) {
          
          if ( is_null( $global_key ) && $val === "global" ) {
            continue;
          }
          
          echo '<option value="' . esc_attr( $val ) . '" ' . selected( $value, $val, false ) . '>' . esc_attr( $label ) . '</option>';
          
          //				}
        }
        echo '</select>';
        $global_face_value = $this->global_config( $key );
        
      } else if ( $type == 'upload' ) {
        $tooltip = isset( $setting['button-tooltip'] ) ? $setting['button-tooltip'] : 'Select';
        $button_text = isset( $setting['button-text'] ) ? $setting['button-text'] : 'Select';
        echo '<div class="df-upload">
				<input placeholder="' . esc_attr( $placeholder ) . '" type="text" name="_' . $slug . '[' . esc_attr( $key ) . ']" id="' . $slug . '_' . esc_attr( $key ) . '"
				       value="' . esc_attr( $value ) . '"
				       class="widefat df-upload-input " data-global="' . esc_attr( $global_attr ) . '"/>
				<a href="javascript:void(0);" id="' . $slug . '_upload_' . esc_attr( $key ) . '"
				   class="df-upload-media df-button button button-primary light"
				   title="' . esc_attr( $tooltip ) . '">
					' . esc_attr( $button_text ) . '
				</a>';
      
      } else if ( $type == 'textarea' ) {
        echo '<div class="">
				<textarea rows="3" cols="40" name="_' . $slug . '[' . esc_attr( $key ) . ']" id="' . $slug . '_' . esc_attr( $key ) . '"
				          class="" data-global="' . esc_attr( $global_attr ) . '">' . esc_attr( $value ) . '</textarea>';
      } else {
        $attrHTML = ' ';
        
        if ( isset( $setting['attr'] ) ) {
          foreach ( $setting['attr'] as $attr_key => $attr_value ) {
            $attrHTML .= $attr_key . "=" . $attr_value . " ";
          }
        }
        
        echo '<div class="">
				<input  placeholder="' . esc_attr( $placeholder ) . '" value="' . esc_attr( $value ) . '" type="' . esc_attr( $type ) . '" ' . esc_attr( $attrHTML ) . ' name="_' . $slug . '[' . esc_attr( $key ) . ']" id="' . $slug . '_' . esc_attr( $key ) . '" class="" data-global="' . esc_attr( $global_attr ) . '"/>';
      }
      
      if ( !is_null( $global_key ) ) {
        echo '<div class="df-global-value" data-global-value="' . esc_attr( $global_value ) . '"><i>Default:</i>
					<code>' . esc_attr( $global_face_value ) . '</code></div>';
      }
      echo '</div>
			<div class="df-desc">
				' . $desc . '
				<a class="df-help-link" target="_blank" href="' . $this->settings_help_page . '#' . esc_attr( strtolower( $key ) ) . '">More Info >> </a>
			</div></div>
		</div>';
    
    }
    
    public function create_separator( $title = '' ) {
      echo '<div class="df-box df-box-separator">' . $title . '</div>';
    }

    //function create_setting_pro
    public function create_setting_pro( $key, $setting = null, $value = null, $global_key = null, $global_value = '' ) {
        if( dflip_fs()->is_plan( 'dflip_wp_pro' )) {
            $this->create_setting( $key, $setting, $value, $global_key, $global_value );
            return;
        }
        $setting = is_null( $setting ) ? $this->defaults[ $key ] : $setting;
        $title = isset( $setting['title'] ) ? $setting['title'] : '';
        echo '<div class="df-box df-pro">
        <div class="df-label"><label for="dflip_' . esc_attr( $key ) . '" >
        ' . esc_attr( $title ) . '
        </label></div>';
        echo '<div class="df-option">';
        echo '<div class="df-desc">
                This <strong>PRO FEATURE</strong> is available in the PRO version of the plugin.
                <a class="df-help-link" target="_blank" href="' . $this->settings_help_page . '#' . esc_attr( strtolower( $key ) ) . '">More Info >> </a>
              </div>';
        echo '</div>';
        echo '</div>';
    }

    /**
     * Returns the singleton instance of the class.
     *
     * @return object DFlip object.
     * @since 1.0.0
     *
     */
    public static function get_instance() {
      
      if ( !isset( self::$instance ) && !( self::$instance instanceof DFlip ) ) {
        self::$instance = new DFlip();
      }
      
      return self::$instance;
      
    }
    
  }
  
  //Load the dFlip Plugin Class
  $dflip = DFlip::get_instance();
}




/*Avoid PHP closing tag to prevent "Headers already sent"*/
