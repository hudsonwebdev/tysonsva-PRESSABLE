<?php
/**
 * Events Manager Google Maps: skip invalid coordinates so map view does not crash.
 *
 * EM's maps.js only skips 0,0. Null / empty / non-numeric lat or lng becomes NaN
 * and Google Maps throws: LatLngAltitude: `lat` invalid: NaN is not an accepted value.
 *
 * @package tca
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coerce location lat/lng to numbers, or 0 so EM's JS will skip the pin.
 *
 * @param array $array Object as array (locations, events, etc.).
 * @return array
 */
function tca_em_sanitize_location_coords_array( $array ) {
	if ( ! is_array( $array ) || ! array_key_exists( 'location_latitude', $array ) ) {
		return $array;
	}

	$lat = $array['location_latitude'];
	$lng = isset( $array['location_longitude'] ) ? $array['location_longitude'] : null;

	if ( ! is_numeric( $lat ) || ! is_numeric( $lng ) ) {
		$array['location_latitude']  = 0;
		$array['location_longitude'] = 0;
		return $array;
	}

	$lat = (float) $lat;
	$lng = (float) $lng;

	if ( abs( $lat ) < 0.00001 && abs( $lng ) < 0.00001 ) {
		$array['location_latitude']  = 0;
		$array['location_longitude'] = 0;
		return $array;
	}

	if ( $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180 ) {
		$array['location_latitude']  = 0;
		$array['location_longitude'] = 0;
		return $array;
	}

	$array['location_latitude']  = $lat;
	$array['location_longitude'] = $lng;

	return $array;
}
add_filter( 'em_to_array', 'tca_em_sanitize_location_coords_array' );

/**
 * Load the map-view guard after Events Manager's script.
 *
 * @param array $deps Unused.
 * @param mixed $min  Unused.
 */
function tca_em_enqueue_maps_guard( $deps = array(), $min = true ) {
	unset( $deps, $min );

	if ( ! wp_script_is( 'events-manager', 'enqueued' ) && ! wp_script_is( 'events-manager', 'registered' ) ) {
		return;
	}

	$path = get_template_directory() . '/js/em-maps-guard.js';
	$uri  = get_template_directory_uri() . '/js/em-maps-guard.js';
	$ver  = file_exists( $path ) ? (string) filemtime( $path ) : ( defined( '_S_VERSION' ) ? _S_VERSION : null );

	wp_enqueue_script(
		'tca-em-maps-guard',
		$uri,
		array( 'jquery', 'events-manager' ),
		$ver,
		true
	);
}
add_action( 'em_enqueue_scripts', 'tca_em_enqueue_maps_guard', 20, 2 );
add_action( 'em_enqueue_admin_scripts', 'tca_em_enqueue_maps_guard', 20, 2 );
