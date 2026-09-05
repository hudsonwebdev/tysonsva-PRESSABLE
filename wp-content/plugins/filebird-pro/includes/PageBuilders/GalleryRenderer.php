<?php
namespace FileBird\PageBuilders;

use FileBird\Classes\Tree;
use FileBird\Classes\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Shared gallery renderer for page builder integrations (WPBakery, Divi, ...).
 */
class GalleryRenderer {

	/**
	 * Whether the inline stylesheet has already been printed on this request.
	 *
	 * @var bool
	 */
	private static $style_printed = false;

	/**
	 * Crop ratios offered to the page builders, keyed by the value stored in the
	 * markup class. Kept here so the CSS and the field options cannot drift.
	 */
	const RATIOS = array(
		'1-1'  => '1 / 1',
		'4-3'  => '4 / 3',
		'16-9' => '16 / 9',
	);

	/**
	 * Renders a gallery of the images stored in a FileBird folder.
	 *
	 * The markup deliberately mirrors the Gutenberg gallery block
	 * (ul > li.blocks-gallery-item > figure > img) because the two share the
	 * PhotoSwipe script in assets/js/photoswipe/fbv-photoswipe.js. That script
	 * walks the list's direct children, so nothing may be printed between the
	 * items: a single newline becomes a text node and breaks the walk.
	 *
	 * @param array $atts folders, folder_id, columns, link_to, size, orderby, order, lightbox.
	 * @return string
	 */
	public static function render( $atts ) {
		$atts = wp_parse_args(
			$atts,
			array(
				'folders'   => '',
				'folder_id' => 0,
				'columns'   => 3,
				'link_to'   => 'file',
				'size'      => 'medium',
				'orderby'   => 'date',
				'order'     => 'DESC',
				'lightbox'  => 'off',
				'layout'    => 'grid',
				'crop'      => 'off',
				'crop_ratio' => '1-1',
			)
		);

		$has_lightbox = in_array( $atts['lightbox'], array( 'on', true, '1', 1 ), true );
		$is_carousel  = 'carousel' === $atts['layout'];
		$is_cropped   = in_array( $atts['crop'], array( 'on', true, '1', 1 ), true );

		// `folders` (multiple) takes precedence; `folder_id` is the single folder
		// form still used by the WPBakery element.
		$raw_folders = ( '' !== $atts['folders'] && array() !== $atts['folders'] ) ? $atts['folders'] : $atts['folder_id'];
		$folder_ids  = self::parseFolderIds( $raw_folders );

		if ( empty( $folder_ids ) ) {
			return '<div class="filebird-gallery-error">' . esc_html__( 'Please select a folder', 'filebird' ) . '</div>';
		}

		$attachment_ids = self::getAttachmentIds( $folder_ids );

		if ( empty( $attachment_ids ) ) {
			return '<div class="filebird-gallery-empty">' . esc_html__( 'No images found in this folder', 'filebird' ) . '</div>';
		}

		$query = new \WP_Query(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'post__in'       => $attachment_ids,
				'posts_per_page' => -1,
				'orderby'        => $atts['orderby'],
				'order'          => $atts['order'],
			)
		);

		if ( ! $query->have_posts() ) {
			return '';
		}

		$classes = array(
			'filebird-gallery',
			'filebird-gallery-columns-' . $atts['columns'],
		);

		if ( $has_lightbox ) {
			// The class the shared PhotoSwipe script binds to.
			$classes[] = 'filebird-block-filebird-gallery';
			self::enqueueLightbox();
		}

		if ( $is_carousel ) {
			$classes[] = 'filebird-gallery--carousel';
			self::enqueueCarousel();
		}

		if ( $is_cropped ) {
			$ratio     = array_key_exists( $atts['crop_ratio'], self::RATIOS ) ? $atts['crop_ratio'] : '1-1';
			$classes[] = 'filebird-gallery--cropped';
			$classes[] = 'filebird-gallery--ratio-' . $ratio;
		}

		$output = '<ul class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		while ( $query->have_posts() ) {
			$query->the_post();
			$attachment_id = get_the_ID();
			$image_src     = wp_get_attachment_image_src( $attachment_id, $atts['size'] );

			if ( ! $image_src ) {
				continue;
			}

			$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			$alt = '' !== $alt ? $alt : get_the_title();

			$img  = '<img src="' . esc_url( $image_src[0] ) . '"';
			$img .= ' width="' . esc_attr( $image_src[1] ) . '" height="' . esc_attr( $image_src[2] ) . '"';
			$img .= ' alt="' . esc_attr( $alt ) . '" class="wp-image-' . esc_attr( $attachment_id ) . '"';

			if ( $has_lightbox ) {
				// Point the lightbox at the full size file while the grid keeps the
				// smaller one the user picked, instead of loading full size images
				// into the page just so the lightbox has something to show.
				$full = wp_get_attachment_image_src( $attachment_id, 'full' );

				if ( $full ) {
					$img .= ' data-pswp-src="' . esc_url( $full[0] ) . '"';
					$img .= ' data-pswp-w="' . esc_attr( $full[1] ) . '" data-pswp-h="' . esc_attr( $full[2] ) . '"';
				}
			}

			$img .= ' />';

			// A link would swallow the click before the lightbox sees it, so the
			// two are mutually exclusive.
			$link = '';

			if ( ! $has_lightbox ) {
				switch ( $atts['link_to'] ) {
					case 'file':
						$image_full = wp_get_attachment_image_src( $attachment_id, 'full' );
						$link       = $image_full ? $image_full[0] : '';
						break;
					case 'post':
						$link = get_attachment_link( $attachment_id );
						break;
				}
			}

			$output .= '<li class="filebird-gallery-item blocks-gallery-item"><figure>';
			$output .= $link ? '<a href="' . esc_url( $link ) . '">' . $img . '</a>' : $img;
			$output .= '</figure></li>';
		}

		$output .= '</ul>';

		if ( $is_carousel ) {
			$output = self::wrapCarousel( $output );
		}

		$output .= self::getStyle();

		wp_reset_postdata();

		return $output;
	}

	/**
	 * Wraps the track in the carousel shell.
	 *
	 * The arrows and dots stay outside the list: the PhotoSwipe script treats
	 * every direct child of the list as a slide, so a button in there would be
	 * parsed as one.
	 *
	 * @param string $track Rendered <ul>.
	 * @return string
	 */
	private static function wrapCarousel( $track ) {
		$arrow = static function ( $direction, $label, $path ) {
			return sprintf(
				'<button type="button" class="filebird-carousel__nav filebird-carousel__nav--%1$s" aria-label="%2$s">'
					. '<svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false">'
					. '<path d="%3$s" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'
					. '</svg></button>',
				esc_attr( $direction ),
				esc_attr( $label ),
				esc_attr( $path )
			);
		};

		return '<div class="filebird-carousel" data-filebird-carousel data-dot-label="'
			// translators: %d is the slide number.
			. esc_attr__( 'Go to slide %d', 'filebird' ) . '">'
			. $track
			. $arrow( 'prev', esc_html__( 'Previous images', 'filebird' ), 'M15 5l-7 7 7 7' )
			. $arrow( 'next', esc_html__( 'Next images', 'filebird' ), 'M9 5l7 7-7 7' )
			. '<div class="filebird-carousel__dots"></div>'
			. '</div>';
	}

	/**
	 * Loads the carousel controls script.
	 */
	private static function enqueueCarousel() {
		if ( ! wp_script_is( 'filebird-gallery-carousel', 'registered' ) ) {
			wp_register_script(
				'filebird-gallery-carousel',
				NJFB_PLUGIN_URL . 'assets/js/filebird-gallery-carousel.js',
				array(),
				NJFB_VERSION,
				true
			);
		}

		wp_enqueue_script( 'filebird-gallery-carousel' );
	}

	/**
	 * Loads the PhotoSwipe assets shared with the Gutenberg gallery block.
	 *
	 * The block registers these handles from its own init.php; they are
	 * registered here too so the page builders do not silently depend on the
	 * block having been loaded first.
	 */
	private static function enqueueLightbox() {
		if ( ! wp_style_is( 'fbv-photoswipe', 'registered' ) ) {
			wp_register_style( 'fbv-photoswipe', NJFB_PLUGIN_URL . 'assets/css/photoswipe/photoswipe.css', array(), NJFB_VERSION );
			wp_register_style( 'fbv-photoswipe-default-skin', NJFB_PLUGIN_URL . 'assets/css/photoswipe/default-skin.css', array(), NJFB_VERSION );
			wp_register_script( 'fbv-photoswipe', NJFB_PLUGIN_URL . 'assets/js/photoswipe/photoswipe.min.js', array(), NJFB_VERSION, true );
			wp_register_script( 'fbv-photoswipe-ui-default', NJFB_PLUGIN_URL . 'assets/js/photoswipe/photoswipe-ui-default.min.js', array(), NJFB_VERSION, true );
			wp_register_script( 'filebird-gallery', NJFB_PLUGIN_URL . 'assets/js/photoswipe/fbv-photoswipe.js', array(), NJFB_VERSION, true );
		}

		wp_enqueue_style( 'fbv-photoswipe' );
		wp_enqueue_style( 'fbv-photoswipe-default-skin' );
		wp_enqueue_script( 'fbv-photoswipe' );
		wp_enqueue_script( 'fbv-photoswipe-ui-default' );
		wp_enqueue_script( 'filebird-gallery' );
	}

	/**
	 * Inline stylesheet, printed once per request.
	 *
	 * @return string
	 */
	private static function getStyle() {
		if ( self::$style_printed ) {
			return '';
		}
		self::$style_printed = true;

		return '<style>
			ul.filebird-gallery {
				display: flex;
				flex-wrap: wrap;
				margin: -10px;
				padding: 0;
				list-style: none;
			}
			ul.filebird-gallery > li.filebird-gallery-item {
				box-sizing: border-box;
				margin: 0;
				padding: 10px;
				list-style: none;
			}
			.filebird-gallery-columns-1 > .filebird-gallery-item { width: 100%; }
			.filebird-gallery-columns-2 > .filebird-gallery-item { width: 50%; }
			.filebird-gallery-columns-3 > .filebird-gallery-item { width: 33.33%; }
			.filebird-gallery-columns-4 > .filebird-gallery-item { width: 25%; }
			.filebird-gallery-columns-5 > .filebird-gallery-item { width: 20%; }
			.filebird-gallery-columns-6 > .filebird-gallery-item { width: 16.66%; }
			.filebird-gallery-item figure {
				margin: 0;
			}
			.filebird-gallery-item img {
				width: 100%;
				height: auto;
				display: block;
			}
			/* Cropping is what makes a carousel line up; the ratio comes from
			   self::RATIOS so the class and the CSS cannot drift apart. */
			.filebird-gallery--cropped .filebird-gallery-item img {
				object-fit: cover;
			}'
			. self::getRatioRules()
			. '
			.filebird-block-filebird-gallery .filebird-gallery-item img {
				cursor: zoom-in;
			}

			/* Carousel: the browser does the scrolling, scroll-snap does the
			   stopping, the script only drives the arrows and dots. */
			.filebird-carousel {
				position: relative;
				/* Padding, not a margin on the dots: a bottom margin collapses
				   straight out of the wrapper and the dots end up glued to the
				   bottom edge of the module. Flagged because the Divi reset zeroes
				   padding on every div inside the builder wrapper. */
				padding-bottom: 10px !important;
			}
			ul.filebird-gallery--carousel {
				flex-wrap: nowrap;
				/* Divi ships `.et-db #et-boc .et-l .et_pb_module ul { overflow: visible }`,
				   an ID selector no plugin class can outrank, and a carousel that
				   cannot scroll is just a clipped row. */
				overflow-x: auto !important;
				overflow-y: hidden !important;
				scroll-snap-type: x mandatory;
				scroll-behavior: smooth;
				-webkit-overflow-scrolling: touch;
				scrollbar-width: none;
			}
			ul.filebird-gallery--carousel::-webkit-scrollbar {
				display: none;
			}
			ul.filebird-gallery--carousel {
				/* The grid pulls its outer edge flush with a -10px margin, but in a
				   carousel that only steals the gap the dots sit in, and it is
				   dropped anyway wherever the Divi reset on ul applies, so the
				   spacing would otherwise differ per context. */
				margin-bottom: 0 !important;
			}
			ul.filebird-gallery--carousel > .filebird-gallery-item {
				flex: 0 0 auto;
				scroll-snap-align: start;
				/* Without this every slide stretches to the tallest image in the
				   whole strip, leaving a gap under the shorter ones. */
				align-self: flex-start;
			}
			.filebird-carousel__nav {
				position: absolute;
				top: 50%;
				transform: translateY(-50%);
				display: flex;
				align-items: center;
				justify-content: center;
				width: 40px;
				height: 40px;
				margin: 0;
				padding: 0;
				border: 0;
				border-radius: 50%;
				background: rgba(255, 255, 255, 0.9);
				box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
				color: #333;
				cursor: pointer;
			}
			.filebird-carousel__nav--prev { left: 0; }
			.filebird-carousel__nav--next { right: 0; }
			.filebird-carousel__nav[disabled] {
				opacity: 0.35;
				cursor: default;
			}
			.filebird-carousel__dots {
				display: flex;
				justify-content: center;
				gap: 8px;
				/* Flagged for the same reason as the track overflow: the Divi reset
				   #et-boc .et-l div { margin: 0 } is an ID selector, and without
				   this the dots end up flush against the bottom of the module. */
				margin: 12px 0 0 !important;
			}
			.filebird-carousel__dot {
				width: 9px;
				height: 9px;
				margin: 0;
				padding: 0;
				border: 0;
				border-radius: 50%;
				background: currentColor;
				opacity: 0.3;
				cursor: pointer;
			}
			.filebird-carousel__dot.is-active {
				opacity: 1;
			}
			.filebird-carousel--static .filebird-carousel__nav,
			.filebird-carousel--static .filebird-carousel__dots {
				display: none;
			}
			@media (prefers-reduced-motion: reduce) {
				ul.filebird-gallery--carousel { scroll-behavior: auto; }
			}
		</style>';
	}

	/**
	 * One `aspect-ratio` rule per entry in self::RATIOS.
	 *
	 * @return string
	 */
	private static function getRatioRules() {
		$css = '';

		foreach ( self::RATIOS as $slug => $ratio ) {
			$css .= sprintf(
				'.filebird-gallery--ratio-%1$s .filebird-gallery-item img{aspect-ratio:%2$s;}',
				$slug,
				$ratio
			);
		}

		return $css;
	}

	/**
	 * Flat "id => indented name" list of every FileBird folder, in tree order.
	 *
	 * @param string $placeholder Optional label for the empty value.
	 * @param string $key_prefix  Prefix for the array keys. Builders that hand the
	 *                            options over to JavaScript need a non numeric key,
	 *                            otherwise the object gets reordered numerically and
	 *                            the tree order (and its indentation) is lost.
	 * @return array
	 */
	public static function getFolderOptions( $placeholder = '', $key_prefix = '' ) {
		$options = array();

		if ( '' !== $placeholder ) {
			$options[ $key_prefix . '0' ] = $placeholder;
		}

		self::buildFolderOptions( Tree::getFolders( 'ord', 'ASC' ), $options, '', $key_prefix );

		return $options;
	}

	/**
	 * Turns a folder option key back into a folder id.
	 *
	 * @param string|int $value Raw option value, prefixed or not.
	 * @return int
	 */
	public static function parseFolderId( $value ) {
		return (int) preg_replace( '/\D/', '', (string) $value );
	}

	/**
	 * Turns a folder option value into a list of folder ids.
	 *
	 * Accepts an array, a single id, or the comma separated form the Divi
	 * folder picker stores ("f_32,f_35"). Empty and zero entries are dropped.
	 *
	 * @param array|string|int $value Raw option value.
	 * @return int[]
	 */
	public static function parseFolderIds( $value ) {
		$parts = is_array( $value ) ? $value : explode( ',', (string) $value );
		$ids   = array();

		foreach ( $parts as $part ) {
			$id = self::parseFolderId( $part );

			if ( $id > 0 ) {
				$ids[] = $id;
			}
		}

		return array_values( array_unique( $ids ) );
	}

	/**
	 * Attachment ids contained in any of the given folders.
	 *
	 * @param int[] $folder_ids Folder ids.
	 * @return array
	 */
	private static function getAttachmentIds( array $folder_ids ) {
		if ( count( $folder_ids ) === 1 ) {
			return Helpers::getAttachmentIdsByFolderId( $folder_ids[0] );
		}

		global $wpdb;

		$placeholders = implode( ',', array_fill( 0, count( $folder_ids ), '%d' ) );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $placeholders is a generated list of %d.
		return $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT `attachment_id` FROM {$wpdb->prefix}fbv_attachment_folder WHERE `folder_id` IN ( $placeholders )",
				$folder_ids
			)
		);
		// phpcs:enable
	}

	/**
	 * @param array  $folders    Folder tree.
	 * @param array  $options    Options being built, by reference.
	 * @param string $indent     Indentation for the current depth.
	 * @param string $key_prefix Prefix for the array keys.
	 */
	private static function buildFolderOptions( $folders, &$options, $indent = '', $key_prefix = '' ) {
		if ( ! is_array( $folders ) ) {
			return;
		}

		foreach ( $folders as $folder ) {
			$options[ $key_prefix . $folder['id'] ] = $indent . $folder['text'];

			if ( ! empty( $folder['children'] ) && is_array( $folder['children'] ) ) {
				self::buildFolderOptions( $folder['children'], $options, $indent . '— ', $key_prefix );
			}
		}
	}

	/**
	 * Registered intermediate image sizes as "size => size".
	 *
	 * @return array
	 */
	public static function getImageSizes() {
		$sizes = array();

		foreach ( get_intermediate_image_sizes() as $size ) {
			$sizes[ $size ] = $size;
		}

		return $sizes;
	}
}
