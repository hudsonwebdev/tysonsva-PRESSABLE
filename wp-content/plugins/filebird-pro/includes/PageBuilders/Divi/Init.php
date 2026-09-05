<?php
namespace FileBird\PageBuilders\Divi;

use FileBird\PageBuilders\GalleryRenderer;

defined( 'ABSPATH' ) || exit;

/**
 * Divi Builder integration: registers the FileBird Gallery module and its
 * Visual Builder counterpart.
 */
class Init {
	private static $instance = null;

	/**
	 * Relative path of the Visual Builder bundle built from apps/divi.
	 */
	const BUILDER_BUNDLE = 'includes/PageBuilders/Divi/assets/js/builder.js';

	/**
	 * Stylesheet emitted alongside the bundle.
	 */
	const BUILDER_STYLES = 'includes/PageBuilders/Divi/assets/js/builder.css';

	/**
	 * Field type of the custom folder picker, shared between the PHP field
	 * definition and the React component that renders it.
	 */
	const FOLDER_PICKER_FIELD = 'filebird_folder_picker';

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		// Divi fires this once ET_Builder_Element and the field definitions are available.
		add_action( 'et_builder_ready', array( $this, 'registerModules' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueBuilderBundle' ) );
	}

	/**
	 * Loads and instantiates the custom modules.
	 */
	public function registerModules() {
		if ( ! class_exists( '\ET_Builder_Module' ) ) {
			return;
		}

		require_once __DIR__ . '/modules/FileBirdGalleryModule.php';

		new FileBirdGalleryModule();
	}

	/**
	 * Flat folder list for the picker, each entry carrying its depth so the
	 * component can indent without having to parse the label.
	 *
	 * Passed through wp_localize_script rather than the field definition, so it
	 * does not depend on Divi preserving unknown keys.
	 *
	 * @return array
	 */
	private static function getFolderTree() {
		$folders = array();

		foreach ( GalleryRenderer::getFolderOptions( '', 'f_' ) as $key => $label ) {
			$depth = 0;

			while ( 0 === strpos( $label, '— ' ) ) {
				$label = substr( $label, strlen( '— ' ) );
				++$depth;
			}

			$folders[] = array(
				'value' => (string) $key,
				'label' => $label,
				'depth' => $depth,
			);
		}

		return $folders;
	}

	/**
	 * Whether the Visual Builder bundle has been built.
	 *
	 * The bundle is a build artifact (see apps/divi); without it the modules
	 * fall back to Divi's server side rendering.
	 *
	 * @return bool
	 */
	public static function hasBuilderBundle() {
		return file_exists( NJFB_PLUGIN_PATH . self::BUILDER_BUNDLE );
	}

	/**
	 * Enqueues the React components that render the modules inside the Visual Builder.
	 *
	 * Divi exposes React on `window.React` through WordPress' own `react`/`react-dom`
	 * handles, which is what the bundle is compiled against.
	 */
	public function enqueueBuilderBundle() {
		if ( ! function_exists( 'et_core_is_fb_enabled' ) || ! \et_core_is_fb_enabled() ) {
			return;
		}

		if ( ! self::hasBuilderBundle() ) {
			return;
		}

		wp_enqueue_script(
			'filebird-divi-builder',
			NJFB_PLUGIN_URL . self::BUILDER_BUNDLE,
			array( 'jquery', 'react', 'react-dom' ),
			NJFB_VERSION,
			true
		);

		if ( file_exists( NJFB_PLUGIN_PATH . self::BUILDER_STYLES ) ) {
			wp_enqueue_style(
				'filebird-divi-builder',
				NJFB_PLUGIN_URL . self::BUILDER_STYLES,
				array(),
				NJFB_VERSION
			);
		}

		/*
		 * The carousel controls are bound on DOM ready on the frontend; in the
		 * builder the markup arrives later as a computed value, so the script is
		 * loaded here and the module component re-runs its init after each
		 * render. The lightbox is deliberately left out: clicking a thumbnail in
		 * the builder should select the module, not open a viewer over it.
		 */
		wp_enqueue_script(
			'filebird-gallery-carousel',
			NJFB_PLUGIN_URL . 'assets/js/filebird-gallery-carousel.js',
			array(),
			NJFB_VERSION,
			true
		);

		wp_localize_script(
			'filebird-divi-builder',
			'filebirdDiviBuilder',
			array(
				'folderPickerField' => self::FOLDER_PICKER_FIELD,
				'folders'           => self::getFolderTree(),
				'i18n'              => array(
					'loading'      => esc_html__( 'Loading gallery…', 'filebird' ),
					'selectFolder' => esc_html__( 'Please select a folder', 'filebird' ),
					'noFolders'    => esc_html__( 'No folders yet.', 'filebird' ),
					'selectAll'    => esc_html__( 'Select all', 'filebird' ),
					'clearAll'     => esc_html__( 'Clear', 'filebird' ),
					'searchHint'   => esc_html__( 'Search folders…', 'filebird' ),
					// %d is the number of folders currently ticked.
					'selected'     => esc_html__( '%d selected', 'filebird' ),
				),
			)
		);
	}
}
