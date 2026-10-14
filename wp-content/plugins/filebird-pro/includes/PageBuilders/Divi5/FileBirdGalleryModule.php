<?php
namespace FileBird\PageBuilders\Divi5;

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module;
use ET\Builder\Packages\Module\Options\Element\ElementClassnames;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;
use WP_Block;

defined( 'ABSPATH' ) || exit;

/**
 * Native Divi 5 "FileBird Gallery" module.
 *
 * The Divi 4 counterpart in ../Divi stays for sites still on Divi 4.x; on Divi 5
 * only this one is registered, so the Divi 4 shortcode framework is never pulled
 * in on its account. See Init for how the two are kept apart.
 *
 * The gallery markup itself is not reimplemented here: both renderers call
 * GalleryRenderer, the same class the Gutenberg block and the WPBakery and Divi 4
 * elements use, so the four cannot drift.
 */
class FileBirdGalleryModule implements DependencyInterface {

	/**
	 * Registers the module with Divi.
	 *
	 * Called by Divi through the dependency tree, so by this point the whole
	 * builder-5 namespace is loaded.
	 */
	public function load(): void {
		ModuleRegistration::register_module(
			__DIR__ . '/metadata/',
			array(
				'render_callback' => array( self::class, 'render_callback' ),
			)
		);
	}

	/**
	 * Frontend render callback.
	 *
	 * @param array          $attrs                       Module attributes.
	 * @param string         $content                     Child modules content.
	 * @param WP_Block       $block                       Block instance.
	 * @param ModuleElements $elements                    Element renderer.
	 * @param array          $default_printed_style_attrs Defaults already printed.
	 * @return string
	 */
	public static function render_callback( array $attrs, string $content, WP_Block $block, ModuleElements $elements, array $default_printed_style_attrs = array() ): string {
		$gallery_html = Attrs::html( $attrs );
		$inner        = '';

		if ( '' !== $gallery_html ) {
			$inner = HTMLUtility::render(
				array(
					'tag'               => 'div',
					'tagEscaped'        => true,
					'attributes'        => array(
						'class' => 'et_pb_module_inner',
					),
					// GalleryRenderer builds and escapes its own markup.
					'childrenSanitizer' => 'et_core_esc_previously',
					'children'          => $gallery_html,
				)
			);
		}

		$parent = BlockParserStore::get_parent( $block->parsed_block['id'], $block->parsed_block['storeInstance'] );

		return Module::render(
			array(
				// Frontend only.
				'orderIndex'               => $block->parsed_block['orderIndex'],
				'storeInstance'            => $block->parsed_block['storeInstance'],

				// Shared with the Visual Builder.
				'attrs'                    => $attrs,
				'elements'                 => $elements,
				'defaultPrintedStyleAttrs' => $default_printed_style_attrs,
				'id'                       => $block->parsed_block['id'],
				'name'                     => $block->block_type->name,
				'moduleCategory'           => $block->block_type->category,
				'classnamesFunction'       => array( self::class, 'module_classnames' ),
				'stylesComponent'          => array( self::class, 'module_styles' ),
				'scriptDataComponent'      => array( self::class, 'module_script_data' ),
				'parentAttrs'              => $parent->attrs ?? array(),
				'parentId'                 => $parent->id ?? '',
				'parentName'               => $parent->blockName ?? '',
				'children'                 => $elements->style_components(
					array(
						'attrName' => 'module',
					)
				) . $inner,
			)
		);
	}

	/**
	 * Wrapper classnames.
	 *
	 * @param array $args Divi classnames args.
	 */
	public static function module_classnames( array $args ): void {
		$args['classnamesInstance']->add(
			ElementClassnames::classnames(
				array(
					'attrs' => $args['attrs']['module']['decoration'] ?? array(),
				)
			),
			true
		);
	}

	/**
	 * Emits the module's CSS.
	 *
	 * @param array $args Divi styles args.
	 */
	public static function module_styles( array $args ): void {
		$elements = $args['elements'];
		$settings = $args['settings'] ?? array();

		Style::add(
			array(
				'id'            => $args['id'],
				'name'          => $args['name'],
				'orderIndex'    => $args['orderIndex'],
				'storeInstance' => $args['storeInstance'],
				'styles'        => array(
					$elements->style(
						array(
							'attrName'   => 'module',
							'styleProps' => array(
								'disabledOn' => array(
									'disabledModuleVisibility' => $settings['disabledModuleVisibility'] ?? null,
								),
							),
						)
					),

					// Border, box shadow and filters aimed at the thumbnails rather
					// than the box around them, the same split the Divi 4 module
					// made with its "Image" toggle.
					$elements->style(
						array(
							'attrName' => 'image',
						)
					),
				),
			)
		);
	}

	/**
	 * Registers the responsive/hover script data for the module's elements.
	 *
	 * @param array $args Divi script data args.
	 */
	public static function module_script_data( array $args ): void {
		$elements = $args['elements'];

		$elements->script_data( array( 'attrName' => 'module' ) );
		$elements->script_data( array( 'attrName' => 'image' ) );
	}
}
