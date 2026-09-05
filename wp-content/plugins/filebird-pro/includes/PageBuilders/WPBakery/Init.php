<?php
namespace FileBird\PageBuilders\WPBakery;

use FileBird\Classes\Tree;
use FileBird\Model\Folder as FolderModel;
use FileBird\Classes\Helpers;
use FileBird\PageBuilders\GalleryRenderer;

defined('ABSPATH') || exit;

class Init {
    private static $instance = null;

    public static function getInstance() {
        if (null == self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        if (function_exists('\vc_map')) {
            add_action('vc_before_init', array($this, 'register_vc_element'));
            add_shortcode('filebird_gallery', array($this, 'render_filebird_gallery'));
        }
    }

    public function register_vc_element() {
        $folders = Tree::getFolders( 'ord', 'ASC' );
        $folder_options = array(
            __('Select Folder', 'filebird') => 0
        );

        // Build options including all children
        $this->build_folder_options($folders, $folder_options);
        
        \vc_map(array(
            'name' => __('FileBird Gallery', 'filebird'),
            'base' => 'filebird_gallery',
            'category' => __('Content', 'filebird'),
            'icon' => 'icon-wpb-images-stack',
            'params' => array(
                array(
                    'type' => 'dropdown',
                    'heading' => __('Select Folder', 'filebird'),
                    'param_name' => 'folder_id',
                    'value' => $folder_options,
                    'description' => __('Choose a folder to display images from', 'filebird'),
                    'admin_label' => true,
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Columns', 'filebird'),
                    'param_name' => 'columns',
                    'value' => array(
                        '1' => 1,
                        '2' => 2,
                        '3' => 3,
                        '4' => 4,
                        '5' => 5,
                        '6' => 6,
                    ),
                    'std' => 3,
                    'description' => __('Number of columns to display', 'filebird'),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Link To', 'filebird'),
                    'param_name' => 'link_to',
                    'value' => array(
                        __('None', 'filebird') => 'none',
                        __('Media File', 'filebird') => 'file',
                        __('Attachment Page', 'filebird') => 'post',
                    ),
                    'std' => 'file',
                    'description' => __('Link behavior when clicking on image', 'filebird'),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Image Size', 'filebird'),
                    'param_name' => 'size',
                    'value' => array_merge(array('Full' => 'full'), $this->get_image_sizes()),
                    'std' => 'medium',
                    'description' => __('Select image size', 'filebird'),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Order By', 'filebird'),
                    'param_name' => 'orderby',
                    'value' => array(
                        __('Date', 'filebird') => 'date',
                        __('Title', 'filebird') => 'title',
                        __('Random', 'filebird') => 'rand',
                    ),
                    'std' => 'date',
                    'description' => __('Order images by', 'filebird'),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Order', 'filebird'),
                    'param_name' => 'order',
                    'value' => array(
                        __('Descending', 'filebird') => 'DESC',
                        __('Ascending', 'filebird') => 'ASC',
                    ),
                    'std' => 'DESC',
                    'description' => __('Sort order', 'filebird'),
                ),
            )
        ));
    }

    /**
     * Recursively builds folder options including all children
     * 
     * @param array $folders Array of folders
     * @param array &$options Reference to options array to be filled
     * @param string $prefix Optional prefix for child folders
     */
    private function build_folder_options($folders, &$options, $prefix = '') {
        if (!is_array($folders)) {
            return;
        }
        
        foreach ($folders as $folder) {
            $options[$prefix . '#' . $folder['id'] . ' ' . $folder['text']] = $folder['id'];
            
            // Process children recursively if they exist
            if (!empty($folder['children']) && is_array($folder['children'])) {
                $this->build_folder_options($folder['children'], $options, $prefix . '— ');
            }
        }
    }

    public function render_filebird_gallery($atts) {
        $atts = shortcode_atts(array(
            'folder_id' => 0,
            'columns' => 3,
            'link_to' => 'file',
            'size' => 'medium',
            'orderby' => 'date',
            'order' => 'DESC',
        ), $atts, 'filebird_gallery');

        return GalleryRenderer::render($atts);
    }

    private function get_image_sizes() {
        return GalleryRenderer::getImageSizes();
    }

}
