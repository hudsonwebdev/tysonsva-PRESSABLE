<?php
namespace FileBird\PageBuilders\Divi;

use FileBird\PageBuilders\GalleryRenderer;

defined( 'ABSPATH' ) || exit;

/**
 * "FileBird Gallery" module for the Divi Builder.
 */
class FileBirdGalleryModule extends \ET_Builder_Module {

	public $slug       = 'filebird_divi_gallery';

	/**
	 * Full Visual Builder support requires the React bundle built from apps/divi.
	 * Without it Divi falls back to rendering the module server side.
	 *
	 * @var string
	 */
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => 'https://ninjateam.org/wordpress-media-library-folders/',
		'author'     => 'Ninja Team',
		'author_uri' => 'https://ninjateam.org',
	);

	public function init() {
		$this->name       = esc_html__( 'FileBird Gallery', 'filebird' );
		$this->vb_support = Init::hasBuilderBundle() ? 'on' : 'partial';

		/*
		 * Divi only prefixes generated CSS with `.et-db #et-boc .et-l` when the
		 * selector matches `\.et[_-](?:pb|fb)[_-]` (see
		 * et_builder_maybe_wrap_css_selector()). Left at the default
		 * `%%order_class%%` this module's selector is `.filebird_divi_gallery_0`,
		 * which never matches, so its styles stay at specificity (0,1,0) and lose
		 * to Divi's own `#et-boc .et-l div { background: transparent }` reset —
		 * Background changes then appear only after a save and reload, once the
		 * frontend emits the prefixed selector.
		 *
		 * Appending `.et_pb_module` (a class the module already carries) makes
		 * the selector match, so it gets prefixed in the builder too.
		 */
		$this->main_css_element = '%%order_class%%.et_pb_module';

		$image = "{$this->main_css_element} img";

		$this->advanced_fields = array(
			/*
			 * Divi adds a "Link" toggle (link_options) to every module with VB
			 * support. The gallery already exposes a "Link To" setting, so a
			 * second, conflicting link control would only confuse. `false` is the
			 * documented opt out; every other key is left out so Divi keeps its
			 * defaults (Background among them).
			 */
			'link_options' => false,

			/*
			 * Without an explicit group these controls only reach the module
			 * wrapper, so rounding the corners rounds the box around the gallery
			 * and leaves the photos square. The named "image" group points them at
			 * the images themselves, which is what someone setting a 10px radius
			 * is after. Same shape Divi's own Gallery module uses.
			 */
			'borders'      => array(
				'default' => array(),
				'image'   => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => $image,
							'border_styles' => $image,
						),
					),
					'label_prefix' => esc_html__( 'Image', 'filebird' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
			),
			'box_shadow'   => array(
				'default' => array(),
				'image'   => array(
					'label'       => esc_html__( 'Image Box Shadow', 'filebird' ),
					'css'         => array(
						'main' => $image,
					),
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'filters'      => array(
				'child_filters_target' => array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'image',
				),
			),
		);
	}

	/**
	 * Drops Divi's stock Admin Label field.
	 *
	 * It is removed here, at the source, rather than through the
	 * `et_pb_all_fields_unprocessed_<slug>` filter: that filter's result goes
	 * through `_set_fields_unprocessed()`, which only merges keys back in and
	 * never deletes, so a field removed there simply reappears.
	 *
	 * @return array
	 */
	public function get_complete_fields() {
		$fields = parent::get_complete_fields();

		unset( $fields['admin_label'] );

		return $fields;
	}

	/**
	 * Drops the matching "Admin Label" toggle.
	 *
	 * Divi registers it from its own constructor, after init() has run, so the
	 * merge point is the only place left to intercept it.
	 *
	 * @param string $tab_slug     Settings modal tab.
	 * @param array  $toggles_array Toggles being merged in.
	 */
	protected function _add_settings_modal_toggles( $tab_slug, $toggles_array ) {
		if ( 'general' === $tab_slug ) {
			unset( $toggles_array['admin_label'] );
		}

		parent::_add_settings_modal_toggles( $tab_slug, $toggles_array );
	}

	/**
	 * Settings modal tabs/toggles.
	 *
	 * @return array
	 */
	public function get_settings_modal_toggles() {
		return array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Gallery Settings', 'filebird' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'image' => array(
						'title' => esc_html__( 'Image', 'filebird' ),
					),
				),
			),
		);
	}

	/**
	 * Module fields.
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'folders'   => $this->getFoldersField(),

			// Legacy single folder attribute from before the picker existed.
			// Declared as `skip` so leftover values in saved layouts do not trip
			// Divi up, but it is deliberately NOT read when rendering: `folders`
			// is the single source of truth. Falling back to it would make an
			// emptied picker silently resurrect the old folder.
			'folder_id' => array(
				'type'    => 'skip',
				'default' => '',
			),
			'layout'    => array(
				'label'            => esc_html__( 'Layout', 'filebird' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'grid'     => esc_html__( 'Grid', 'filebird' ),
					'carousel' => esc_html__( 'Carousel', 'filebird' ),
				),
				'default'          => 'grid',
				'description'      => esc_html__( 'Grid wraps images onto several rows; carousel keeps them on one row the visitor can scroll', 'filebird' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array( '__gallery' ),
			),
			'columns'   => array(
				'label'            => esc_html__( 'Columns', 'filebird' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'default'          => '3',
				'description'      => esc_html__( 'Number of columns to display; in carousel mode this is how many images are visible at once', 'filebird' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array( '__gallery' ),
			),
			'crop'      => array(
				'label'            => esc_html__( 'Crop Images', 'filebird' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'off' => esc_html__( 'No', 'filebird' ),
					'on'  => esc_html__( 'Yes', 'filebird' ),
				),
				'default'          => 'off',
				'description'      => esc_html__( 'Trim every image to the same shape so the gallery lines up, instead of following each image\'s own proportions', 'filebird' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array( '__gallery' ),
			),
			'crop_ratio' => array(
				'label'            => esc_html__( 'Aspect Ratio', 'filebird' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'1-1'  => esc_html__( 'Square (1:1)', 'filebird' ),
					'4-3'  => esc_html__( 'Landscape (4:3)', 'filebird' ),
					'16-9' => esc_html__( 'Widescreen (16:9)', 'filebird' ),
				),
				'default'          => '1-1',
				'toggle_slug'      => 'main_content',
				'show_if'          => array( 'crop' => 'on' ),
				'computed_affects' => array( '__gallery' ),
			),
			'lightbox'  => array(
				'label'            => esc_html__( 'Lightbox', 'filebird' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'filebird' ),
					'on'  => esc_html__( 'Yes', 'filebird' ),
				),
				'default'          => 'off',
				'description'      => esc_html__( 'Open images in a full screen viewer the visitor can page through', 'filebird' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array( '__gallery' ),
			),
			'link_to'   => array(
				'label'            => esc_html__( 'Link To', 'filebird' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'none' => esc_html__( 'None', 'filebird' ),
					'file' => esc_html__( 'Media File', 'filebird' ),
					'post' => esc_html__( 'Attachment Page', 'filebird' ),
				),
				'default'          => 'file',
				'description'      => esc_html__( 'Link behavior when clicking on image', 'filebird' ),
				'toggle_slug'      => 'main_content',
				// A link would swallow the click before the lightbox sees it.
				'show_if'          => array( 'lightbox' => 'off' ),
				'computed_affects' => array( '__gallery' ),
			),
			'size'      => array(
				'label'            => esc_html__( 'Image Size', 'filebird' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array_merge( array( 'full' => esc_html__( 'Full', 'filebird' ) ), GalleryRenderer::getImageSizes() ),
				'default'          => 'medium',
				'description'      => esc_html__( 'Select image size', 'filebird' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array( '__gallery' ),
			),
			'orderby'   => array(
				'label'            => esc_html__( 'Order By', 'filebird' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'date'  => esc_html__( 'Date', 'filebird' ),
					'title' => esc_html__( 'Title', 'filebird' ),
					'rand'  => esc_html__( 'Random', 'filebird' ),
				),
				'default'          => 'date',
				'description'      => esc_html__( 'Order images by', 'filebird' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array( '__gallery' ),
			),
			'order'     => array(
				'label'            => esc_html__( 'Order', 'filebird' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'DESC' => esc_html__( 'Descending', 'filebird' ),
					'ASC'  => esc_html__( 'Ascending', 'filebird' ),
				),
				'default'          => 'DESC',
				'description'      => esc_html__( 'Sort order', 'filebird' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array( '__gallery' ),
			),
			'__gallery' => array(
				'type'                => 'computed',
				'computed_callback'   => array( __CLASS__, 'get_gallery_html' ),
				// No `computed_minimum`: the callback has to run even with nothing
				// selected so it can return the "pick a folder" notice.
				'computed_depends_on' => array( 'folders', 'layout', 'columns', 'crop', 'crop_ratio', 'link_to', 'size', 'orderby', 'order', 'lightbox' ),
			),
		);
	}

	/**
	 * The folder picker field.
	 *
	 * With the Visual Builder bundle in place this is a custom multi select
	 * field backed by a React component. Without it there is no component to
	 * render that type, so it degrades to Divi's native single folder select —
	 * both store a value the renderer understands.
	 *
	 * @return array
	 */
	private function getFoldersField() {
		$field = array(
			'label'            => esc_html__( 'Folders', 'filebird' ),
			'option_category'  => 'basic_option',
			'description'      => esc_html__( 'Choose one or more folders to display images from', 'filebird' ),
			'toggle_slug'      => 'main_content',
			'computed_affects' => array( '__gallery' ),
		);

		if ( Init::hasBuilderBundle() ) {
			return array_merge(
				$field,
				array(
					'type'    => Init::FOLDER_PICKER_FIELD,
					'default' => '',
				)
			);
		}

		return array_merge(
			$field,
			array(
				'label'   => esc_html__( 'Select Folder', 'filebird' ),
				'type'    => 'select',
				'options' => GalleryRenderer::getFolderOptions( esc_html__( 'Select Folder', 'filebird' ), 'f_' ),
				'default' => 'f_0',
			)
		);
	}

	/**
	 * Visual Builder AJAX callback — keeps the preview in sync with the settings.
	 *
	 * @param array $args Current module props.
	 * @return string
	 */
	public static function get_gallery_html( $args = array() ) {
		return GalleryRenderer::render(
			array(
				'folders'   => isset( $args['folders'] ) ? $args['folders'] : '',
				'columns'   => isset( $args['columns'] ) ? $args['columns'] : 3,
				'link_to'   => isset( $args['link_to'] ) ? $args['link_to'] : 'file',
				'size'      => isset( $args['size'] ) ? $args['size'] : 'medium',
				'orderby'   => isset( $args['orderby'] ) ? $args['orderby'] : 'date',
				'order'     => isset( $args['order'] ) ? $args['order'] : 'DESC',
				'lightbox'  => isset( $args['lightbox'] ) ? $args['lightbox'] : 'off',
				'layout'     => isset( $args['layout'] ) ? $args['layout'] : 'grid',
				'crop'       => isset( $args['crop'] ) ? $args['crop'] : 'off',
				'crop_ratio' => isset( $args['crop_ratio'] ) ? $args['crop_ratio'] : '1-1',
			)
		);
	}

	/**
	 * Frontend output.
	 *
	 * @param array       $attrs       Module attributes.
	 * @param string|null $content     Module content.
	 * @param string      $render_slug Module slug used for rendering.
	 * @return string
	 */
	public function render( $attrs, $content = null, $render_slug = '' ) {
		return self::get_gallery_html( $this->props );
	}
}
