<?php
namespace FileBird\PageBuilders\Divi5;

defined( 'ABSPATH' ) || exit;

/**
 * Visual Builder preview endpoint for the Divi 5 module.
 *
 * Divi 5 modules render their own markup in the builder, but a FileBird gallery
 * is built from a database query the browser has no access to, so the builder
 * asks the server for it — the same shape Divi's own plugin backed modules use
 * (see ImagelyGalleryController in the theme).
 *
 * The route lives in FileBird's namespace rather than `divi/v1`: Divi's REST
 * registration is a fixed list in RESTRegistration.php with no hook for third
 * party routes.
 */
class Controller {

	const REST_NAMESPACE = 'filebird/v1';
	const REST_ROUTE     = '/divi5/gallery-preview';

	/**
	 * Registers the preview route. Hooked on `rest_api_init`.
	 */
	public function registerRoutes() {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'preview' ),
				'permission_callback' => array( $this, 'permission' ),
				'args'                => array(
					'attrs' => array(
						'type'     => 'object',
						'required' => true,
					),
				),
			)
		);
	}

	/**
	 * Only people who could open the builder in the first place.
	 *
	 * @return bool
	 */
	public function permission() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Renders a gallery for the attributes the builder currently holds.
	 *
	 * @param \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function preview( \WP_REST_Request $request ) {
		$attrs = $request->get_param( 'attrs' );

		if ( ! is_array( $attrs ) ) {
			$attrs = array();
		}

		/*
		 * GalleryRenderer enqueues the lightbox and carousel assets as a side
		 * effect. In a REST request those enqueues go nowhere, which is what we
		 * want: the builder bundle loads the carousel script itself, and the
		 * lightbox is deliberately left out so clicking a thumbnail selects the
		 * module instead of opening a viewer over it.
		 */
		return rest_ensure_response(
			array(
				'html' => Attrs::html( $attrs ),
			)
		);
	}
}
