<?php
namespace FileBird\PageBuilders\Divi5;

use FileBird\PageBuilders\GalleryRenderer;

defined( 'ABSPATH' ) || exit;

/**
 * Turns a Divi 5 attribute tree into gallery markup.
 *
 * Deliberately free of any Divi class: the Visual Builder preview endpoint runs
 * on requests where builder-5 may not have been loaded, and a `use` of a Divi
 * type at class level would be a fatal error there. FileBirdGalleryModule, which
 * does implement a Divi interface, calls through to this.
 */
class Attrs {

	/**
	 * Reads one of the module's own settings out of the D5 attribute tree.
	 *
	 * Every attribute is stored as `<path>.<breakpoint>.<state>`; these fields are
	 * all declared non responsive and non hover in module.json, so only
	 * `desktop.value` is ever written and the default stands in for a value the
	 * user has never touched.
	 *
	 * @param array  $attrs   Module attributes.
	 * @param string $name    Attribute name under `module.advanced`.
	 * @param mixed  $default Value to use when the attribute is unset.
	 * @return mixed
	 */
	public static function setting( array $attrs, $name, $default ) {
		$value = $attrs['module']['advanced'][ $name ]['desktop']['value'] ?? null;

		return ( null === $value || '' === $value ) ? $default : $value;
	}

	/**
	 * The gallery HTML for a set of module attributes.
	 *
	 * Shared between the frontend render callback and the Visual Builder preview
	 * endpoint so the builder and the frontend cannot render different galleries
	 * from the same settings.
	 *
	 * @param array $attrs Module attributes.
	 * @return string
	 */
	public static function html( array $attrs ) {
		return GalleryRenderer::render(
			array(
				'folders'    => self::setting( $attrs, 'folders', '' ),
				'layout'     => self::setting( $attrs, 'layout', 'grid' ),
				'columns'    => self::setting( $attrs, 'columns', 3 ),
				'crop'       => self::setting( $attrs, 'crop', 'off' ),
				'crop_ratio' => self::setting( $attrs, 'cropRatio', '1-1' ),
				'lightbox'   => self::setting( $attrs, 'lightbox', 'off' ),
				'link_to'    => self::setting( $attrs, 'linkTo', 'file' ),
				'size'       => self::setting( $attrs, 'size', 'medium' ),
				'orderby'    => self::setting( $attrs, 'orderby', 'date' ),
				'order'      => self::setting( $attrs, 'order', 'DESC' ),
			)
		);
	}
}
