<?php
/**
 * Alternate Neighborhood Guide map (overlay). Isolated from the live guide.
 *
 * Pretty URLs: /neighborhood-guide/ and /neighborhood-guide/{slug}/
 *
 * @package tca
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public path for the overlay map (does not change live CPT permalinks).
 *
 * @return string
 */
function tca_ng_overlay_map_base_path() {
	return 'neighborhood-guide';
}

/**
 * Page assigned the overlay map template, if any.
 *
 * @return WP_Post|null
 */
function tca_ng_overlay_map_page() {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'template-neighborhood-map.php',
			'posts_per_page' => 1,
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);
	return ! empty( $pages[0] ) ? $pages[0] : null;
}

/**
 * Whether the current request is the overlay map (template or pretty URL).
 *
 * @return bool
 */
function tca_is_neighborhood_overlay_map_page() {
	if ( is_page_template( 'template-neighborhood-map.php' ) ) {
		return true;
	}
	return (bool) get_query_var( 'tca_ng_map' ) || (bool) get_query_var( 'tca_ng_hood' );
}

/**
 * Overlay permalink for a neighborhood slug.
 *
 * @param string $slug Neighborhood post_name.
 * @return string
 */
function tca_ng_overlay_map_url( $slug = '' ) {
	$base = trailingslashit( home_url( '/' . tca_ng_overlay_map_base_path() ) );
	$slug = sanitize_title( $slug );
	return $slug ? $base . $slug . '/' : $base;
}

/**
 * Register query vars and /neighborhood-guide/{slug}/ rewrites.
 *
 * @return void
 */
function tca_ng_overlay_map_rewrite() {
	add_rewrite_tag( '%tca_ng_map%', '([0-9]+)' );
	add_rewrite_tag( '%tca_ng_hood%', '([^/]+)' );

	$base = tca_ng_overlay_map_base_path();
	add_rewrite_rule(
		'^' . $base . '/([^/]+)/?$',
		'index.php?tca_ng_map=1&tca_ng_hood=$matches[1]',
		'top'
	);
	add_rewrite_rule(
		'^' . $base . '/?$',
		'index.php?tca_ng_map=1',
		'top'
	);

	if ( '1' !== get_option( 'tca_ng_overlay_rewrite_ver' ) ) {
		flush_rewrite_rules( false );
		update_option( 'tca_ng_overlay_rewrite_ver', '1' );
	}
}
add_action( 'init', 'tca_ng_overlay_map_rewrite' );

/**
 * @param string[] $vars Query vars.
 * @return string[]
 */
function tca_ng_overlay_map_query_vars( $vars ) {
	$vars[] = 'tca_ng_map';
	$vars[] = 'tca_ng_hood';
	return $vars;
}
add_filter( 'query_vars', 'tca_ng_overlay_map_query_vars' );

/**
 * Point pretty URLs at the overlay page so the loop still has a title.
 *
 * @param WP_Query $query Query.
 * @return void
 */
function tca_ng_overlay_map_parse_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( ! $query->get( 'tca_ng_map' ) && ! $query->get( 'tca_ng_hood' ) ) {
		return;
	}

	$page = tca_ng_overlay_map_page();
	if ( $page ) {
		$query->set( 'page_id', (int) $page->ID );
		$query->set( 'post_type', 'page' );
		$query->is_page     = true;
		$query->is_singular = true;
		$query->is_home     = false;
		$query->is_archive  = false;
		$query->is_404      = false;
	}
}
add_action( 'parse_query', 'tca_ng_overlay_map_parse_query' );

/**
 * Force the overlay template for pretty URLs.
 *
 * @param string $template Template path.
 * @return string
 */
function tca_ng_overlay_map_template( $template ) {
	if ( ! get_query_var( 'tca_ng_map' ) && ! get_query_var( 'tca_ng_hood' ) ) {
		return $template;
	}
	$found = locate_template( 'template-neighborhood-map.php' );
	return $found ? $found : $template;
}
add_filter( 'template_include', 'tca_ng_overlay_map_template', 99 );

/**
 * Avoid a 404 when the overlay page has not been created yet.
 *
 * @param bool     $preempt Whether to short-circuit.
 * @param WP_Query $query   Query.
 * @return bool
 */
function tca_ng_overlay_map_pre_404( $preempt, $query ) {
	if ( $query->get( 'tca_ng_map' ) || $query->get( 'tca_ng_hood' ) ) {
		$query->is_404 = false;
		return true;
	}
	return $preempt;
}
add_filter( 'pre_handle_404', 'tca_ng_overlay_map_pre_404', 10, 2 );

/**
 * Read an ACF block field from attrs.data (including clone-prefixed names).
 *
 * @param array  $data Block attrs.data.
 * @param string $name Field name.
 * @return mixed
 */
function tca_ng_overlay_acf_raw( $data, $name ) {
	if ( ! is_array( $data ) || '' === $name ) {
		return null;
	}
	if ( isset( $data[ $name ] ) ) {
		return $data[ $name ];
	}
	$suffix = '_' . $name;
	foreach ( $data as $key => $value ) {
		$key = (string) $key;
		if ( '' === $key || '_' === $key[0] ) {
			continue;
		}
		if ( substr( $key, -strlen( $suffix ) ) === $suffix ) {
			return $value;
		}
	}
	return null;
}

/**
 * Attachment URL from an ACF image field value.
 *
 * @param mixed $value Image ID, array, or URL.
 * @return string
 */
function tca_ng_overlay_image_url_from_value( $value ) {
	if ( is_array( $value ) ) {
		if ( ! empty( $value['sizes']['large'] ) && is_string( $value['sizes']['large'] ) ) {
			return $value['sizes']['large'];
		}
		if ( ! empty( $value['url'] ) && is_string( $value['url'] ) ) {
			return $value['url'];
		}
		if ( ! empty( $value['id'] ) ) {
			$value = $value['id'];
		} elseif ( ! empty( $value['ID'] ) ) {
			$value = $value['ID'];
		}
	}
	if ( is_numeric( $value ) && (int) $value > 0 ) {
		$url = wp_get_attachment_image_url( (int) $value, 'large' );
		return $url ? $url : '';
	}
	if ( is_string( $value ) && preg_match( '#^https?://#i', $value ) ) {
		return $value;
	}
	return '';
}

/**
 * Plain text from a WYSIWYG / HTML field.
 *
 * @param mixed $value Field value.
 * @return string
 */
function tca_ng_overlay_plain_text( $value ) {
	if ( is_array( $value ) ) {
		$value = implode( ' ', $value );
	}
	if ( ! is_string( $value ) ) {
		return '';
	}
	$text = wp_strip_all_tags( $value );
	$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$text = preg_replace( '/\s+/u', ' ', $text );
	return is_string( $text ) ? trim( $text ) : '';
}

/**
 * Whether stripped text looks like a neighborhood description.
 *
 * @param string $text Plain text.
 * @return bool
 */
function tca_ng_overlay_is_description( $text ) {
	$text = trim( (string) $text );
	if ( strlen( $text ) < 40 ) {
		return false;
	}
	if ( false !== stripos( $text, 'Select a Neighborhood' ) ) {
		return false;
	}
	return true;
}

/**
 * Featured photo + intro copy from a neighborhood post's ACF blocks.
 *
 * Neighborhoods do not use the native featured image / excerpt. The photo is
 * typically tca/block-image (`block_image`) and the intro is tca/wysiwyg-editor
 * (`html`), with image-banner fields as fallbacks.
 *
 * @param WP_Post $hood Neighborhood post.
 * @return array{image: string, description: string}
 */
function tca_ng_overlay_hood_content( $hood ) {
	$image       = get_the_post_thumbnail_url( $hood, 'large' ) ?: '';
	$description = trim( (string) $hood->post_excerpt );
	$block_image = '';
	$banner      = '';
	$additional  = '';
	$html_desc   = '';
	$intro       = '';

	$walk = function ( $blocks ) use ( &$walk, &$block_image, &$banner, &$additional, &$html_desc, &$intro ) {
		foreach ( (array) $blocks as $block ) {
			if ( ! is_array( $block ) ) {
				continue;
			}

			$data = ( isset( $block['attrs']['data'] ) && is_array( $block['attrs']['data'] ) )
				? $block['attrs']['data']
				: array();
			$name = isset( $block['blockName'] ) ? (string) $block['blockName'] : '';

			if ( $data ) {
				if ( '' === $banner ) {
					$banner = tca_ng_overlay_image_url_from_value( tca_ng_overlay_acf_raw( $data, 'banner_image' ) );
				}
				if ( '' === $block_image ) {
					$block_image = tca_ng_overlay_image_url_from_value( tca_ng_overlay_acf_raw( $data, 'block_image' ) );
				}
				if ( '' === $additional ) {
					$candidate = tca_ng_overlay_plain_text( tca_ng_overlay_acf_raw( $data, 'additional_text' ) );
					if ( tca_ng_overlay_is_description( $candidate ) ) {
						$additional = $candidate;
					}
				}
				if ( '' === $html_desc ) {
					$candidate = tca_ng_overlay_plain_text( tca_ng_overlay_acf_raw( $data, 'html' ) );
					if ( tca_ng_overlay_is_description( $candidate ) ) {
						$html_desc = $candidate;
					}
				}
				if ( '' === $intro ) {
					$intro = tca_ng_overlay_plain_text( tca_ng_overlay_acf_raw( $data, 'section_intro' ) );
				}
			}

			if ( '' === $block_image && 'core/image' === $name ) {
				$id = isset( $block['attrs']['id'] ) ? (int) $block['attrs']['id'] : 0;
				if ( $id ) {
					$url = wp_get_attachment_image_url( $id, 'large' );
					$block_image = $url ? $url : '';
				}
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$walk( $block['innerBlocks'] );
			}
		}
	};

	$walk( parse_blocks( (string) $hood->post_content ) );

	if ( '' === $image ) {
		$image = $block_image ? $block_image : $banner;
	}
	if ( '' === $description ) {
		if ( $additional ) {
			$description = $additional;
		} elseif ( $html_desc ) {
			$description = $html_desc;
		} elseif ( tca_ng_overlay_is_description( $intro ) ) {
			$description = $intro;
		}
	}

	return array(
		'image'       => $image,
		'description' => $description,
	);
}

/**
 * Neighborhood payload for the overlay map (geojson + image + description).
 *
 * @return array{type: string, features: array}
 */
function tca_neighborhood_overlay_map_data() {
	$posts = get_posts(
		array(
			'post_type'      => 'neighborhood',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	$collection = array(
		'type'     => 'FeatureCollection',
		'features' => array(),
	);

	foreach ( $posts as $hood ) {
		$area_raw = function_exists( 'get_field' ) ? get_field( 'area_geojson', $hood->ID ) : '';
		if ( ! $area_raw ) {
			continue;
		}

		$coords = json_decode( (string) $area_raw, true );
		if ( ! $coords ) {
			continue;
		}

		if ( isset( $coords['geometry']['coordinates'] ) ) {
			$coords = $coords['geometry']['coordinates'];
		} elseif ( isset( $coords['coordinates'] ) ) {
			$coords = $coords['coordinates'];
		}

		$content = tca_ng_overlay_hood_content( $hood );
		$color   = function_exists( 'get_field' ) ? get_field( 'color', $hood->ID ) : '';
		if ( ! is_string( $color ) || '' === $color ) {
			$color = '#00B852';
		}

		$collection['features'][] = array(
			'coords'     => $coords,
			'properties' => array(
				'id'          => (int) $hood->ID,
				'title'       => get_the_title( $hood ),
				'slug'        => $hood->post_name,
				'color'       => $color,
				'permalink'   => tca_ng_overlay_map_url( $hood->post_name ),
				'image'       => $content['image'],
				'description' => $content['description'],
			),
		);
	}

	return $collection;
}

/**
 * Enqueue Mapbox + overlay map script on the alternate template only.
 *
 * @return void
 */
function tca_enqueue_neighborhood_overlay_map() {
	if ( ! tca_is_neighborhood_overlay_map_page() ) {
		return;
	}

	$token = function_exists( 'tca_env' ) ? tca_env( 'MAPBOX_ACCESS_TOKEN' ) : '';
	$js    = get_template_directory() . '/public/js/neighborhood-overlay-map.js';
	$ver   = file_exists( $js ) ? (string) filemtime( $js ) : ( defined( '_S_VERSION' ) ? _S_VERSION : null );

	wp_enqueue_script( 'mapbox-js', 'https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.js', array(), null, true );
	wp_enqueue_style( 'mapbox-css', 'https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.css', array(), null );

	wp_add_inline_style(
		'tca-custom-style',
		'.tca-ng-overlay-map__image-wrap{width:100%;height:280px;overflow:hidden;margin:0 0 16px;background:#385DFF;}'
		. '.tca-ng-overlay-map__image-wrap img{display:block;width:100%;height:100%;max-height:none;object-fit:cover;}'
		. '.tca-ng-overlay-map__image-wrap--empty{margin:0 0 16px;background:#385DFF;}'
		. '.tca-ng-overlay-map__image-wrap--empty img{display:none;}'
	);

	wp_enqueue_script(
		'tca-neighborhood-overlay-map',
		get_template_directory_uri() . '/public/js/neighborhood-overlay-map.js',
		array( 'mapbox-js' ),
		$ver,
		true
	);

	wp_localize_script(
		'tca-neighborhood-overlay-map',
		'TCA_NG_OVERLAY',
		array(
			'mapboxToken' => $token,
			'style'       => 'mapbox://styles/tcasoftware/cm7unqmeq01px01qo3jv83ql2',
			'center'      => array( -77.227, 38.926 ),
			'zoom'        => 13.2,
			'mobileZoom'  => 12.3,
			'basePath'    => trailingslashit( home_url( '/' . tca_ng_overlay_map_base_path() ) ),
			'activeSlug'  => sanitize_title( (string) get_query_var( 'tca_ng_hood' ) ),
			'data'        => tca_neighborhood_overlay_map_data(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'tca_enqueue_neighborhood_overlay_map', 26 );
