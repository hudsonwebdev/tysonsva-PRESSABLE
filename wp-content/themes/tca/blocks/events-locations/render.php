<?php
if ( render_block_preview_if_applicable( $block ) ) return;
$container_settings = get_field('container_settings');
$section_header = get_field('section_header');
include __DIR__ .'/../../inc/common_block_variables.php';



openSection(
    $wrap_size,
    $container_size,
    $anchor,
    $class_name,
    $container_type,
    $background_color,
    $background_image,
    $text_color,
    $disable_animation,
    $vertical_pad_top,
    $vertical_pad_bottom
);

drawSectionHeader($section_title_size,$section_title,$title_alignment,$show_underline,$section_intro,$section_button,$section_button_style); 


if ( function_exists( 'render_block_preview_if_applicable' ) && render_block_preview_if_applicable( $block ) ) {
	return;
}

$allowed_styles = array(
	'mapbox://styles/mapbox/streets-v12'            => 'Streets',
	'mapbox://styles/mapbox/outdoors-v12'          => 'Outdoors',
	'mapbox://styles/mapbox/light-v11'             => 'Light',
	'mapbox://styles/mapbox/dark-v11'              => 'Dark',
	'mapbox://styles/mapbox/satellite-v9'          => 'Satellite',
	'mapbox://styles/mapbox/satellite-streets-v12' => 'Satellite streets',
	'mapbox://styles/mapbox/navigation-day-v1'     => 'Navigation day',
	'mapbox://styles/mapbox/navigation-night-v1'   => 'Navigation night',
);

$style_choice = function_exists( 'get_field' ) ? get_field( 'mapbox_style' ) : '';
$default_style = 'mapbox://styles/mapbox/streets-v12';

if ( 'custom' === $style_choice ) {
	$custom_raw = function_exists( 'get_field' ) ? get_field( 'mapbox_style_custom' ) : '';
	$custom_raw = is_string( $custom_raw ) ? trim( $custom_raw ) : '';
	$style      = '';
	if ( '' !== $custom_raw && strlen( $custom_raw ) <= 512 ) {
		if ( preg_match( '#^mapbox://#i', $custom_raw ) ) {
			$style = $custom_raw;
		} elseif ( preg_match( '#^https://api\.mapbox\.com/#i', $custom_raw ) ) {
			$style = esc_url_raw( $custom_raw );
		}
	}
	if ( '' === $style ) {
		$style = $default_style;
	}
} else {
	$style = $style_choice;
	if ( empty( $style ) || ! isset( $allowed_styles[ $style ] ) ) {
		$style = $default_style;
	}
}

$map_height_raw = function_exists( 'get_field' ) ? get_field( 'map_height' ) : null;
$map_height     = is_numeric( $map_height_raw ) ? (int) $map_height_raw : 352;
$map_height     = max( 200, min( 1600, $map_height ) );

$allowed_pin_shapes = array( 'teardrop', 'circle', 'square', 'diamond' );
$pin_shape_raw      = function_exists( 'get_field' ) ? get_field( 'map_pin_shape' ) : '';
$map_pin_shape      = in_array( $pin_shape_raw, $allowed_pin_shapes, true ) ? $pin_shape_raw : 'teardrop';
$map_pin_size_raw = function_exists( 'get_field' ) ? get_field( 'map_pin_size' ) : null;
$map_pin_size     = is_numeric( $map_pin_size_raw ) ? (int) $map_pin_size_raw : 15;

$allowed_pin_colors = array(
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
$pin_color_key = function_exists( 'get_field' ) ? get_field( 'map_pin_color' ) : '';
if ( ! is_string( $pin_color_key ) || ! isset( $allowed_pin_colors[ $pin_color_key ] ) ) {
	$pin_color_key = 'blue';
}
$map_pin_fill = $allowed_pin_colors[ $pin_color_key ];
if ( function_exists( 'sanitize_hex_color' ) ) {
	$sanitized = sanitize_hex_color( $map_pin_fill );
	if ( $sanitized ) {
		$map_pin_fill = $sanitized;
	}
}
$map_pin_light = ( 'white' === $pin_color_key );

$show_upcoming_events = (bool) ( function_exists( 'get_field' ) ? get_field( 'show_upcoming_events' ) : false );

$no_events_behavior_raw = function_exists( 'get_field' ) ? get_field( 'no_events_behavior' ) : '';
$no_events_behavior       = ( 'Show Message' === $no_events_behavior_raw ) ? 'show_message' : 'hide';
$no_events_message_raw    = function_exists( 'get_field' ) ? get_field( 'no_events_message' ) : '';
$no_events_message_raw    = is_string( $no_events_message_raw ) ? $no_events_message_raw : '';
$no_events_message_json   = wp_json_encode(
	$no_events_message_raw,
	JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
);

if ( ! class_exists( 'EM_Events' ) || ! function_exists( 'em_get_location' ) ) : ?>
	<div class="uk-alert-warning" uk-alert>
		<p><?php echo esc_html__( 'Events Manager plugin is not available.', 'tca' ); ?></p>
	</div>
	<?php
	return;
endif;

/**
 * @param EM_Event $EM_Event Event instance.
 * @return string
 */
$tca_format_event_date = static function ( $EM_Event ) {
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
};

/**
 * For repeating events (recurrence series), keep only the next occurrence.
 * EM_Events::get is ordered by start ascending; first row per series is the earliest upcoming.
 *
 * @param array $events EM_Event instances (any array key order; values are re-indexed in order).
 * @return EM_Event[]
 */
$tca_events_locations_one_per_series = static function ( $events ) {
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
};

/**
 * Normalize ACF taxonomy field to a list of term IDs (Events Manager event-categories).
 *
 * @param mixed $raw Field value (single ID, array of IDs, etc.).
 * @return int[]
 */
$tca_events_locations_category_term_ids = static function ( $raw ) {
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
};

/**
 * Build map marker + list row from a location and its EM_Event list.
 *
 * @param EM_Location $EM_Location Location object.
 * @param EM_Event[]  $loc_events  Events at this location.
 * @param array       $markers     Markers list (by ref).
 * @param array       $list_items  List rows (by ref).
 * @param bool        $dedupe_series When true, run one-per-recurrence-series on $loc_events first.
 * @param bool        $show_upcoming When false, markers and list rows omit event payloads.
 */
$tca_events_locations_append_location = static function ( $EM_Location, $loc_events, &$markers, &$list_items, $tca_format_event_date, $tca_events_locations_one_per_series, $dedupe_series, $show_upcoming_events ) {
	if ( ! is_object( $EM_Location ) || empty( $EM_Location->location_id ) ) {
		return;
	}
	if ( ! $show_upcoming_events ) {
		$loc_events = array();
	} elseif ( $dedupe_series ) {
		$loc_events = $tca_events_locations_one_per_series( (array) $loc_events );
	}

	$lat_raw = $EM_Location->location_latitude;
	$lng_raw = $EM_Location->location_longitude;
	$lat     = is_numeric( $lat_raw ) ? (float) $lat_raw : null;
	$lng     = is_numeric( $lng_raw ) ? (float) $lng_raw : null;

	$events_payload = array();
	foreach ( (array) $loc_events as $EM_Event ) {
		if ( ! is_object( $EM_Event ) ) {
			continue;
		}
		$events_payload[] = array(
			'title' => $EM_Event->event_name,
			'url'   => $EM_Event->get_permalink(),
			'date'  => $tca_format_event_date( $EM_Event ),
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

	// Location attribute from EM settings (#_LATT{Website}); key matches text inside braces.
	$website_attr_key = apply_filters( 'tca_events_locations_website_attribute_key', 'Website', $EM_Location );
	$website_url      = '';
	$website_label    = '';
	if ( is_string( $website_attr_key ) && '' !== $website_attr_key && is_array( $EM_Location->location_attributes ) && ! empty( $EM_Location->location_attributes[ $website_attr_key ] ) ) {
		$w_raw = trim( wp_strip_all_tags( (string) $EM_Location->location_attributes[ $website_attr_key ] ) );
		if ( '' !== $w_raw ) {
			$website_label = $w_raw;
			$w_candidate     = $w_raw;
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

	$list_items[] = array(
		'location' => $EM_Location->location_name,
		'events'   => $events_payload,
		'lat'      => $lat_raw,
		'lng'      => $lng_raw,
	);

	if ( null === $lat || null === $lng ) {
		return;
	}
	if ( abs( $lat ) < 0.00001 && abs( $lng ) < 0.00001 ) {
		return;
	}

	$markers[] = array(
		'lat'           => $lat,
		'lng'           => $lng,
		'locationName'  => $EM_Location->location_name,
		'imageUrl'      => $image_url,
		'address'       => $address,
		'websiteUrl'    => $website_url,
		'websiteLabel'  => $website_label,
		'events'        => $events_payload,
	);
};

$event_selection_method = function_exists( 'get_field' ) ? get_field( 'event_selection_method' ) : '';
$use_category_mode      = ( 'Upcoming by Category' === $event_selection_method );
$filter_by_category     = (bool) ( function_exists( 'get_field' ) ? get_field( 'filter_by_category' ) : false );
$category_term_ids      = $tca_events_locations_category_term_ids( function_exists( 'get_field' ) ? get_field( 'event_category' ) : null );

$selected = function_exists( 'get_field' ) ? get_field( 'event_locations' ) : null;
$location_post_ids = array();

if ( ! empty( $selected ) && is_array( $selected ) ) {
	foreach ( $selected as $item ) {
		if ( is_object( $item ) && isset( $item->ID ) ) {
			$location_post_ids[] = (int) $item->ID;
		} elseif ( is_numeric( $item ) ) {
			$location_post_ids[] = (int) $item;
		}
	}
}
$location_post_ids = array_values( array_unique( array_filter( $location_post_ids ) ) );

$markers    = array();
$list_items = array();

$em_events_base_args = array(
	'scope'     => 'future',
	'limit'     => 0,
	'status'    => 'published',
	'orderby'   => 'event_start_date,event_start_time',
	'order'     => 'ASC',
	'recurring' => false,
);

if ( $use_category_mode ) {
	if ( ! empty( $category_term_ids ) ) {
		$all_cat_events = EM_Events::get(
			array_merge(
				$em_events_base_args,
				array( 'category' => $category_term_ids )
			)
		);
		$all_cat_events = $tca_events_locations_one_per_series( (array) $all_cat_events );
		$by_location_id = array();
		foreach ( (array) $all_cat_events as $EM_Event ) {
			if ( ! is_object( $EM_Event ) || empty( $EM_Event->location_id ) ) {
				continue;
			}
			$lid = (int) $EM_Event->location_id;
			if ( ! isset( $by_location_id[ $lid ] ) ) {
				$by_location_id[ $lid ] = array();
			}
			$by_location_id[ $lid ][] = $EM_Event;
		}
		foreach ( $by_location_id as $location_id => $loc_events ) {
			$EM_Location = em_get_location( (int) $location_id, 'location_id' );
			if ( $filter_by_category ) {
				$tca_events_locations_append_location( $EM_Location, $loc_events, $markers, $list_items, $tca_format_event_date, $tca_events_locations_one_per_series, false, $show_upcoming_events );
			} else {
				$all_at_location = array();
				if ( $show_upcoming_events ) {
					$all_at_location = EM_Events::get(
						array_merge(
							$em_events_base_args,
							array( 'location' => (int) $location_id )
						)
					);
				}
				$tca_events_locations_append_location( $EM_Location, $all_at_location, $markers, $list_items, $tca_format_event_date, $tca_events_locations_one_per_series, true, $show_upcoming_events );
			}
		}
	}
} else {
	foreach ( $location_post_ids as $post_id ) {
		$EM_Location = em_get_location( $post_id, 'post_id' );
		if ( ! is_object( $EM_Location ) || empty( $EM_Location->location_id ) ) {
			continue;
		}
		$loc_query_args = array_merge(
			$em_events_base_args,
			array( 'location' => (int) $EM_Location->location_id )
		);
		if ( $filter_by_category && ! empty( $category_term_ids ) ) {
			$loc_query_args['category'] = $category_term_ids;
		}
		$loc_events = array();
		if ( $show_upcoming_events ) {
			$loc_events = EM_Events::get( $loc_query_args );
		}
		$tca_events_locations_append_location( $EM_Location, $loc_events, $markers, $list_items, $tca_format_event_date, $tca_events_locations_one_per_series, true, $show_upcoming_events );
	}
}

$wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
	? get_block_wrapper_attributes( array( 'class' => 'tca-events-locations-block' ) )
	: 'class="tca-events-locations-block"';

$map_id = isset( $block['anchor'] ) && $block['anchor'] ? sanitize_title( $block['anchor'] ) : '';
if ( '' === $map_id ) {
	$map_id = 'tca-events-locations-map-' . ( isset( $block['id'] ) ? preg_replace( '/[^a-zA-Z0-9_-]/', '', $block['id'] ) : uniqid( '', false ) );
}

$markers_json = wp_json_encode(
	$markers,
	JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);

$tca_evloc_show_map = false;
if ( $use_category_mode ) {
	$tca_evloc_show_map = ! empty( $category_term_ids ) && ! empty( $markers );
} else {
	$tca_evloc_show_map = ! empty( $location_post_ids ) && ! empty( $markers );
}
?>

<div <?php echo $wrapper_attributes; ?>>
	<div class="uk-container">
		<?php if ( $use_category_mode && empty( $category_term_ids ) ) : ?>
			<p class="tca-events-locations-no-selection"><?php echo esc_html__( 'Select one or more event categories in the block settings.', 'tca' ); ?></p>
		<?php elseif ( ! $use_category_mode && empty( $location_post_ids ) ) : ?>
			<p class="tca-events-locations-no-selection"><?php echo esc_html__( 'Select one or more locations in the block settings.', 'tca' ); ?></p>
		<?php elseif ( $tca_evloc_show_map ) : ?>
			<div
				class="tca-events-locations-map-wrap"
				data-tca-events-locations-map
				data-map-style="<?php echo esc_attr( $style ); ?>"
				data-pin-shape="<?php echo esc_attr( $map_pin_shape ); ?>"
				data-pin-light="<?php echo $map_pin_light ? '1' : '0'; ?>"
				data-pin-size="<?php echo (int) $map_pin_size; ?>"
				data-show-upcoming-events="<?php echo $show_upcoming_events ? '1' : '0'; ?>"
				data-no-events-behavior="<?php echo esc_attr( $no_events_behavior ); ?>"
				data-no-events-message="<?php echo esc_attr( $no_events_message_json ); ?>"
				data-markers="<?php echo esc_attr( $markers_json ); ?>"
				data-label-upcoming="<?php echo esc_attr__( 'Upcoming events', 'tca' ); ?>"
				style="<?php echo esc_attr( '--tca-pin-fill:' . $map_pin_fill . ';' ); ?>"
			>
				<div
					class="tca-events-locations-map-shell"
					style="<?php echo esc_attr( sprintf( '--tca-events-map-height:%dpx;height:%1$dpx;min-height:%1$dpx;', $map_height ) ); ?>"
				>
					<aside
						class="tca-events-locations-drawer"
						id="<?php echo esc_attr( $map_id ); ?>-drawer"
						hidden
						aria-hidden="true"
					>
						<button type="button" class="tca-events-locations-drawer__close" aria-label="<?php echo esc_attr__( 'Close panel', 'tca' ); ?>">&times;</button>
						<div class="tca-events-locations-drawer__cols<?php echo $show_upcoming_events ? '' : ' tca-events-locations-drawer__cols--no-events'; ?>">
							<div class="tca-events-locations-drawer__detail">
								<div class="tca-events-locations-drawer__image-wrap">
									<img src="" alt="" width="640" height="480" decoding="async" />
								</div>
								<h3 class="tca-events-locations-drawer__title" id="<?php echo esc_attr( $map_id ); ?>-drawer-title"></h3>
								<p class="tca-events-locations-drawer__address"></p>
								<p class="tca-events-locations-drawer__website" hidden></p>
							</div>
							<div class="tca-events-locations-drawer__events-col"<?php echo $show_upcoming_events ? '' : ' hidden'; ?>>
								<h4 class="tca-events-locations-drawer__events-heading"></h4>
								<ul class="tca-events-locations-drawer__events"></ul>
								<p class="tca-events-locations-drawer__empty" hidden><?php echo esc_html__( 'No upcoming events at this location.', 'tca' ); ?></p>
							</div>
						</div>
					</aside>
					<div class="tca-events-locations-map-stage">
						<button
							type="button"
							class="tca-events-locations-map-scrim"
							hidden
							tabindex="-1"
							aria-label="<?php echo esc_attr__( 'Close location panel', 'tca' ); ?>"
						></button>
						<div
							id="<?php echo esc_attr( $map_id ); ?>"
							class="tca-events-locations-map-canvas"
							role="presentation"
						></div>
					</div>
				</div>
			</div>
		<?php elseif ( $use_category_mode ) : ?>
			<p class="tca-events-locations-no-matching-events"><?php echo esc_html__( 'No upcoming events found for the selected categories with a map location.', 'tca' ); ?></p>
		<?php else : ?>
			<p class="tca-events-locations-no-coords"><?php echo esc_html__( 'Selected locations do not have map coordinates yet.', 'tca' ); ?></p>
		<?php endif; ?>

		
	</div>
</div>
                  

   
            
<?php

closeSection($wrap_size,$container_size,$container_type,$overlapping_graphic);
