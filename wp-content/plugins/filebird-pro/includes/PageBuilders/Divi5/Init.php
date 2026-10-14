<?php
namespace FileBird\PageBuilders\Divi5;

use FileBird\PageBuilders\GalleryRenderer;

defined( 'ABSPATH' ) || exit;

/**
 * Divi 5 integration: registers the native "FileBird Gallery" module.
 *
 * Divi 5 still runs Divi 4 modules, by wrapping them in its ShortcodeModule
 * compatibility layer, but doing so forces the whole Divi 4 shortcode framework
 * to load — which is what Divi's own "D5 Readiness" screen warns about. This
 * class is the native counterpart; on Divi 5 the Divi 4 module in ../Divi is not
 * registered at all, so nothing here drags that framework in.
 */
class Init {
	private static $instance = null;

	/**
	 * Relative path of the Visual Builder bundle built from apps/divi5.
	 */
	const BUILDER_BUNDLE = 'includes/PageBuilders/Divi5/assets/js/builder.js';

	/**
	 * Stylesheet emitted alongside the bundle.
	 */
	const BUILDER_STYLES = 'includes/PageBuilders/Divi5/assets/js/builder.css';

	/**
	 * Package build name. Divi localises the script's data under the pascal cased
	 * name plus "Data", so this decides the `FilebirdGalleryBuilderData` global
	 * the bundle reads.
	 */
	const PACKAGE = 'filebird-gallery-builder';

	public static function getInstance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Whether this site runs the Divi 5 builder.
	 *
	 * Divi 5 defines `et_builder_d5_enabled()` and lets it be filtered off, in
	 * which case the site falls back to the Divi 4 builder and the Divi 4 module
	 * is the one that should register.
	 *
	 * @return bool
	 */
	public static function isActive() {
		return function_exists( 'et_builder_d5_enabled' ) && \et_builder_d5_enabled();
	}

	public function __construct() {
		/*
		 * Registered unconditionally, and deliberately not behind isActive(): this
		 * runs on `plugins_loaded`, where the theme's functions.php has not been
		 * read yet and et_builder_d5_enabled() therefore does not exist. Gating
		 * here would switch the module off on every request.
		 *
		 * None of these hooks exist outside Divi 5, so on a Divi 4 site they
		 * simply never fire. The one hook whose registration has to be conditional
		 * is the Divi 4 `et_builder_ready` in ../Divi, and that one waits for
		 * `after_setup_theme` to decide.
		 */

		/*
		 * Array callbacks, never closures. Divi's D5 readiness check walks
		 * $wp_filter and reflects over each callback to work out which plugin it
		 * belongs to (see PluginHooksCheck::_get_plugin_name() in the theme); it
		 * handles arrays and named functions and returns false for anything else.
		 * A closure here would leave FileBird reported as a Divi 4 only plugin
		 * even though it registers a native Divi 5 module.
		 */
		add_action( 'divi_module_library_modules_dependency_tree', array( $this, 'registerModule' ) );
		add_action( 'divi_visual_builder_assets_before_enqueue_scripts', array( $this, 'registerBuilderBundle' ) );
		add_action( 'rest_api_init', array( $this, 'registerRoutes' ) );
	}

	/**
	 * Adds the module to the dependency tree Divi builds on every request.
	 *
	 * @param mixed $dependency_tree Divi's DependencyTree instance.
	 */
	public function registerModule( $dependency_tree ) {
		if ( ! is_object( $dependency_tree ) || ! method_exists( $dependency_tree, 'add_dependency' ) ) {
			return;
		}

		// FileBirdGalleryModule implements a Divi interface, so it can only be
		// loaded once builder-5 is on the autoloader — which this hook guarantees.
		if ( ! interface_exists( '\ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface' ) ) {
			return;
		}

		$dependency_tree->add_dependency( new FileBirdGalleryModule() );
	}

	/**
	 * Registers the REST preview endpoint used by the builder.
	 */
	public function registerRoutes() {
		$controller = new Controller();
		$controller->registerRoutes();
	}

	/**
	 * Whether the Visual Builder bundle has been built.
	 *
	 * The bundle is a build artifact (see apps/divi5).
	 *
	 * @return bool
	 */
	public static function hasBuilderBundle() {
		return file_exists( NJFB_PLUGIN_PATH . self::BUILDER_BUNDLE );
	}

	/**
	 * Hands Divi the builder bundle to enqueue.
	 *
	 * Divi owns the enqueue order in the Visual Builder, so third party scripts
	 * are registered through its PackageBuildManager rather than with
	 * wp_enqueue_script: that is what lets us depend on `divi-module-library` and
	 * `divi-field-library` and still land after them.
	 */
	public function registerBuilderBundle() {
		if ( ! self::hasBuilderBundle() ) {
			return;
		}

		$manager = '\ET\Builder\VisualBuilder\Assets\PackageBuildManager';

		if ( ! is_callable( array( $manager, 'register_package_build' ) ) ) {
			return;
		}

		/*
		 * The stylesheet, unlike the script, goes into both windows. The module
		 * renders in the app window, but Divi draws the settings panel in the top
		 * window — the folder picker's markup ends up there even though the React
		 * that produces it runs in the app window — so styling it needs the CSS in
		 * both. The script cannot follow: the Divi packages it depends on are
		 * registered for the app window alone.
		 */
		$has_styles = file_exists( NJFB_PLUGIN_PATH . self::BUILDER_STYLES );

		$style = array(
			'enqueue_top_window' => $has_styles,
			'enqueue_app_window' => $has_styles,
		);

		if ( $has_styles ) {
			$style['src'] = NJFB_PLUGIN_URL . self::BUILDER_STYLES;
		}

		$data = array(
			'restUrl'    => esc_url_raw( rest_url( Controller::REST_NAMESPACE . Controller::REST_ROUTE ) ),
			'nonce'      => wp_create_nonce( 'wp_rest' ),
			'folders'    => self::getFolderTree(),
			'imageSizes' => self::getImageSizes(),
			'i18n'       => array(
				'loading'      => esc_html__( 'Loading gallery…', 'filebird' ),
				'selectFolder' => esc_html__( 'Please select a folder', 'filebird' ),
				'noFolders'    => esc_html__( 'No folders yet.', 'filebird' ),
				'selectAll'    => esc_html__( 'Select all', 'filebird' ),
				'clearAll'     => esc_html__( 'Clear', 'filebird' ),
				'searchHint'   => esc_html__( 'Search folders…', 'filebird' ),
				// translators: %d is the number of folders currently ticked.
				'selected'     => esc_html__( '%d selected', 'filebird' ),
			),
		);

		call_user_func(
			array( $manager, 'register_package_build' ),
			array(
				'name'    => self::PACKAGE,
				'version' => NJFB_VERSION,
				'script'  => array(
					'src'                => NJFB_PLUGIN_URL . self::BUILDER_BUNDLE,
					'deps'               => array(
						// The three globals the bundle registers itself against, plus
						// the React it is compiled against. Its own strings come from
						// `data_app_window` below rather than wp.i18n, so that
						// package is deliberately not a dependency.
						'divi-module-library',
						'divi-field-library',
						'divi-module',
						'divi-vendor-react',
					),
					'args'               => array( 'in_footer' => true ),
					'data_app_window'    => $data,
					/*
					 * App window only. Divi registers divi-module-library,
					 * divi-field-library and divi-module for that window alone, so
					 * enqueueing here in the top window as well would name
					 * dependencies WordPress has never registered there — which is
					 * both a "script was enqueued with dependencies that are not
					 * registered" notice and a bundle with nothing to register
					 * itself against.
					 */
					'enqueue_top_window' => false,
					'enqueue_app_window' => true,
				),
				'style'   => $style,
			)
		);

		/*
		 * The carousel controls bind on DOM ready on the frontend; in the builder
		 * the markup arrives later, from the preview endpoint, so the script is
		 * loaded here and the module component re-runs its init after each render.
		 * The lightbox is deliberately left out: clicking a thumbnail in the
		 * builder should select the module, not open a viewer over it.
		 */
		call_user_func(
			array( $manager, 'register_package_build' ),
			array(
				'name'    => 'filebird-gallery-carousel',
				'version' => NJFB_VERSION,
				'script'  => array(
					'src'                => NJFB_PLUGIN_URL . 'assets/js/filebird-gallery-carousel.js',
					'args'               => array( 'in_footer' => true ),
					'enqueue_top_window' => false,
					'enqueue_app_window' => true,
				),
				'style'   => array(
					'enqueue_top_window' => false,
					'enqueue_app_window' => false,
				),
			)
		);
	}

	/**
	 * Flat folder list for the picker, each entry carrying its depth so the
	 * component can indent without having to parse the label.
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
	 * Registered image sizes in the shape Divi's select field expects.
	 *
	 * module.json can only carry the one size that is always there ("full"), so
	 * the rest are merged into the field's options by the bundle at registration
	 * time.
	 *
	 * @return array
	 */
	private static function getImageSizes() {
		$options = array(
			'full' => array( 'label' => esc_html__( 'Full', 'filebird' ) ),
		);

		foreach ( GalleryRenderer::getImageSizes() as $size ) {
			$options[ $size ] = array( 'label' => $size );
		}

		return $options;
	}
}
