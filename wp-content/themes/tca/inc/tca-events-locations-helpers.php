<?php
/**
 * Shared helpers for Events Locations map blocks (markers + upcoming events).
 *
 * @package tca
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format an EM event start for display.
 *
 * @param object $EM_Event Event instance.
 * @return string
 */
function tca_evloc_format_event_date( $EM_Event ) {
	if ( ! is_object( $EM_Event ) || empty( $EM_Event->event_start_date ) ) {
		return '';
	}
	$time = ! empty( $EM_Event->event_start_time ) ? $EM_Event->event_start_time : '00:00:00';
	$ts   = strtotime( trim( $EM_Event->event_start_date . ' ' . $time ) );
	if ( ! $ts ) {
		return '';
	}
	$fmt = get_option( 'date_format' );
	if ( empty( $EM_Event->event_all_day ) && ! empty( $EM_Event->event_start_time ) ) {
		$fmt .= ' ' . get_option( 'time_format' );
	}
	return date_i18n( $fmt, $ts );
}

/**
 * Keep only the next occurrence per recurrence series.
 *
 * @param array $events EM_Event instances.
 * @return array
 */
function tca_evloc_one_per_series( $events ) {
	if ( empty( $events ) || ! is_array( $events ) ) {
		return array();
	}
	$ordered = array_values( $events );
	$seen    = array();
	$out     = array();
	foreach ( $ordered as $EM_Event ) {
		if ( ! is_object( $EM_Event ) ) {
			continue;
		}
		$series_key = null;
		if ( ! empty( $EM_Event->recurrence_set_id ) ) {
			$series_key = 'rs-' . (int) $EM_Event->recurrence_set_id;
		} elseif ( ! empty( $EM_Event->recurrence_id ) ) {
			$series_key = 'rid-' . (int) $EM_Event->recurrence_id;
		}
		if ( null !== $series_key ) {
			if ( isset( $seen[ $series_key ] ) ) {
				continue;
			}
			$seen[ $series_key ] = true;
		}
		$out[] = $EM_Event;
	}
	return $out;
}

/**
 * Normalize ACF taxonomy field to term IDs.
 *
 * @param mixed $raw Field value.
 * @return int[]
 */
function tca_evloc_category_term_ids( $raw ) {
	$ids = array();
	if ( is_numeric( $raw ) ) {
		$ids[] = (int) $raw;
	} elseif ( is_array( $raw ) ) {
		foreach ( $raw as $tid ) {
			if ( is_numeric( $tid ) ) {
				$ids[] = (int) $tid;
			}
		}
	}
	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Normalize ACF relationship field to location post IDs.
 *
 * @param mixed $selected Field value.
 * @return int[]
 */
function tca_evloc_location_post_ids( $selected ) {
	$ids = array();
	if ( empty( $selected ) || ! is_array( $selected ) ) {
		return $ids;
	}
	foreach ( $selected as $item ) {
		if ( is_object( $item ) && isset( $item->ID ) ) {
			$ids[] = (int) $item->ID;
		} elseif ( is_numeric( $item ) ) {
			$ids[] = (int) $item;
		}
	}
	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Build a map marker payload from an EM location + events.
 *
 * @param object $EM_Location          Location object.
 * @param array  $loc_events           EM_Event list.
 * @param bool   $show_upcoming_events Include events in payload.
 * @param bool   $dedupe_series        Dedupe recurrence series.
 * @return array|null Marker array or null if no coordinates.
 */
function tca_evloc_build_marker( $EM_Location, $loc_events, $show_upcoming_events = true, $dedupe_series = true ) {
	if ( ! is_object( $EM_Location ) || empty( $EM_Location->location_id ) ) {
		return null;
	}

	if ( ! $show_upcoming_events ) {
		$loc_events = array();
	} elseif ( $dedupe_series ) {
		$loc_events = tca_evloc_one_per_series( (array) $loc_events );
	}

	$lat_raw = $EM_Location->location_latitude;
	$lng_raw = $EM_Location->location_longitude;
	$lat     = is_numeric( $lat_raw ) ? (float) $lat_raw : null;
	$lng     = is_numeric( $lng_raw ) ? (float) $lng_raw : null;

	if ( null === $lat || null === $lng ) {
		return null;
	}
	if ( abs( $lat ) < 0.00001 && abs( $lng ) < 0.00001 ) {
		return null;
	}

	$events_payload = array();
	foreach ( (array) $loc_events as $EM_Event ) {
		if ( ! is_object( $EM_Event ) ) {
			continue;
		}
		$events_payload[] = array(
			'title' => $EM_Event->event_name,
			'url'   => $EM_Event->get_permalink(),
			'date'  => tca_evloc_format_event_date( $EM_Event ),
		);
	}

	$image_url   = '';
	$loc_post_id = (int) $EM_Location->post_id;
	if ( $loc_post_id && get_post( $loc_post_id ) && has_post_thumbnail( $loc_post_id ) ) {
		$thumb = get_the_post_thumbnail_url( $loc_post_id, 'large' );
		if ( $thumb ) {
			$image_url = $thumb;
		}
	}

	$address = method_exists( $EM_Location, 'get_full_address' ) ? $EM_Location->get_full_address( ', ', true ) : '';

	$website_attr_key = apply_filters( 'tca_events_locations_website_attribute_key', 'Website', $EM_Location );
	$website_url      = '';
	$website_label    = '';
	if ( is_string( $website_attr_key ) && '' !== $website_attr_key && is_array( $EM_Location->location_attributes ) && ! empty( $EM_Location->location_attributes[ $website_attr_key ] ) ) {
		$w_raw = trim( wp_strip_all_tags( (string) $EM_Location->location_attributes[ $website_attr_key ] ) );
		if ( '' !== $w_raw ) {
			$website_label = $w_raw;
			$w_candidate   = $w_raw;
			if ( ! preg_match( '#^https?://#i', $w_candidate ) ) {
				$w_candidate = 'https://' . ltrim( $w_candidate, '/' );
			}
			if ( function_exists( 'wp_http_validate_url' ) ) {
				$validated = wp_http_validate_url( $w_candidate );
				if ( $validated ) {
					$website_url = $validated;
				}
			}
		}
	}

	return array(
		'lat'          => $lat,
		'lng'          => $lng,
		'locationName' => $EM_Location->location_name,
		'imageUrl'     => $image_url,
		'address'      => $address,
		'websiteUrl'   => $website_url,
		'websiteLabel' => $website_label,
		'events'       => $events_payload,
	);
}

/**
 * Build markers for selected location posts.
 *
 * @param int[] $location_post_ids Location post IDs.
 * @param bool  $show_upcoming     Include upcoming events.
 * @param int[] $category_term_ids Optional category filter.
 * @return array[]
 */
function tca_evloc_markers_from_location_posts( array $location_post_ids, $show_upcoming = true, array $category_term_ids = array() ) {
	$markers = array();
	if ( ! class_exists( 'EM_Events' ) || ! function_exists( 'em_get_location' ) ) {
		return $markers;
	}

	$em_events_base_args = array(
		'scope'     => 'future',
		'limit'     => 0,
		'status'    => 'published',
		'orderby'   => 'event_start_date,event_start_time',
		'order'     => 'ASC',
		'recurring' => false,
	);

	foreach ( $location_post_ids as $post_id ) {
		$EM_Location = em_get_location( (int) $post_id, 'post_id' );
		if ( ! is_object( $EM_Location ) || empty( $EM_Location->location_id ) ) {
			continue;
		}
		$loc_query_args = array_merge(
			$em_events_base_args,
			array( 'location' => (int) $EM_Location->location_id )
		);
		if ( ! empty( $category_term_ids ) ) {
			$loc_query_args['category'] = $category_term_ids;
		}
		$loc_events = array();
		if ( $show_upcoming ) {
			$loc_events = EM_Events::get( $loc_query_args );
		}
		$marker = tca_evloc_build_marker( $EM_Location, $loc_events, $show_upcoming, true );
		if ( $marker ) {
			$markers[] = $marker;
		}
	}

	return $markers;
}

/**
 * Resolve Mapbox style URL from ACF fields.
 *
 * @param string $style_choice Selected style or "custom".
 * @param string $custom_raw   Custom style URL.
 * @return string
 */
function tca_evloc_resolve_mapbox_style( $style_choice, $custom_raw = '' ) {
	$allowed_styles = array(
		'mapbox://styles/mapbox/streets-v12',
		'mapbox://styles/mapbox/outdoors-v12',
		'mapbox://styles/mapbox/light-v11',
		'mapbox://styles/mapbox/dark-v11',
		'mapbox://styles/mapbox/satellite-v9',
		'mapbox://styles/mapbox/satellite-streets-v12',
		'mapbox://styles/mapbox/navigation-day-v1',
		'mapbox://styles/mapbox/navigation-night-v1',
	);
	$default_style = 'mapbox://styles/mapbox/streets-v12';

	if ( 'custom' === $style_choice ) {
		$custom_raw = is_string( $custom_raw ) ? trim( $custom_raw ) : '';
		$style      = '';
		if ( '' !== $custom_raw && strlen( $custom_raw ) <= 512 ) {
			if ( preg_match( '#^mapbox://#i', $custom_raw ) ) {
				$style = $custom_raw;
			} elseif ( preg_match( '#^https://api\.mapbox\.com/#i', $custom_raw ) ) {
				$style = esc_url_raw( $custom_raw );
			}
		}
		return '' !== $style ? $style : $default_style;
	}

	if ( empty( $style_choice ) || ! in_array( $style_choice, $allowed_styles, true ) ) {
		return $default_style;
	}
	return $style_choice;
}

/**
 * Resolve pin color hex + light flag from ACF color key.
 *
 * @param string $pin_color_key Color key.
 * @return array{fill: string, light: bool, key: string}
 */
function tca_evloc_resolve_pin_color( $pin_color_key ) {
	$allowed = array(
		'blue'   => '#4264fb',
		'red'    => '#dc2626',
		'green'  => '#16a34a',
		'orange' => '#ea580c',
		'purple' => '#7c3aed',
		'teal'   => '#0d9488',
		'gray'   => '#64748b',
		'black'  => '#1e293b',
		'white'  => '#ffffff',
	);
	if ( ! is_string( $pin_color_key ) || ! isset( $allowed[ $pin_color_key ] ) ) {
		$pin_color_key = 'blue';
	}
	$fill = $allowed[ $pin_color_key ];
	if ( function_exists( 'sanitize_hex_color' ) ) {
		$sanitized = sanitize_hex_color( $fill );
		if ( $sanitized ) {
			$fill = $sanitized;
		}
	}
	return array(
		'fill'  => $fill,
		'light' => ( 'white' === $pin_color_key ),
		'key'   => $pin_color_key,
	);
}
