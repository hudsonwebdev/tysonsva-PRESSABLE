<?php
/**
 * Replace Events Manager listing Map view (calendar Map toggle) with the
 * theme Mapbox map used by the Events Locations block.
 *
 * @package tca
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current request can show EM's listing Map toggle.
 *
 * @return bool
 */
function tca_em_listing_needs_mapbox() {
	if ( is_admin() ) {
		return false;
	}
	if ( is_page_template( 'page-events.php' ) ) {
		return true;
	}
	if ( ! function_exists( 'em_get_option' ) ) {
		return false;
	}

	$current_id = (int) get_queried_object_id();
	if ( ! $current_id ) {
		return false;
	}

	$page_ids = array();
	if ( class_exists( 'EM\\Archetypes' ) ) {
		$from_archetypes = \EM\Archetypes::get_option_values( 'dbem_events_page' );
		if ( is_array( $from_archetypes ) ) {
			$page_ids = array_merge( $page_ids, $from_archetypes );
		}
	}
	$page_ids[] = em_get_option( 'dbem_events_page' );
	$page_ids[] = em_get_option( 'dbem_locations_page' );
	$page_ids   = array_values( array_unique( array_filter( array_map( 'intval', $page_ids ) ) ) );

	return in_array( $current_id, $page_ids, true );
}

/**
 * Default Mapbox studio style for the listing Map view.
 *
 * @return string
 */
function tca_em_calendar_default_mapbox_style() {
	return 'mapbox://styles/tcasoftware/cm7unqmeq01px01qo3jv83ql2';
}

/**
 * Page ID that stores listing map ACF fields (events listing).
 *
 * @return int
 */
function tca_em_listing_settings_page_id() {
	if ( function_exists( 'is_page' ) && is_page() ) {
		$qid = (int) get_queried_object_id();
		if ( $qid ) {
			return $qid;
		}
	}

	if ( function_exists( 'em_get_option' ) ) {
		$id = (int) em_get_option( 'dbem_events_page' );
		if ( $id ) {
			return $id;
		}
	}

	return 3806;
}

/**
 * Listing Map view options from the events page ACF fields.
 *
 * @return array{style: string, height: int, pin_shape: string, pin_size: int, pin_fill: string, pin_light: bool}
 */
function tca_em_listing_map_options() {
	$page_id        = tca_em_listing_settings_page_id();
	$default_style  = tca_em_calendar_default_mapbox_style();
	$style_choice   = '';
	$custom_raw     = '';
	$height         = 520;
	$pin_shape      = 'teardrop';
	$pin_size       = 15;
	$pin_color_key  = 'blue';

	if ( $page_id && function_exists( 'get_field' ) ) {
		$style_choice  = get_field( 'listing_mapbox_style', $page_id );
		$custom_raw    = get_field( 'listing_mapbox_style_custom', $page_id );
		$height_raw    = get_field( 'listing_map_height', $page_id );
		$pin_shape_raw = get_field( 'listing_map_pin_shape', $page_id );
		$pin_size_raw  = get_field( 'listing_map_pin_size', $page_id );
		$pin_color_raw = get_field( 'listing_map_pin_color', $page_id );

		if ( is_numeric( $height_raw ) ) {
			$height = (int) $height_raw;
		}
		if ( is_string( $pin_shape_raw ) && '' !== $pin_shape_raw ) {
			$pin_shape = $pin_shape_raw;
		}
		if ( is_numeric( $pin_size_raw ) ) {
			$pin_size = (int) $pin_size_raw;
		}
		if ( is_string( $pin_color_raw ) && '' !== $pin_color_raw ) {
			$pin_color_key = $pin_color_raw;
		}
	}

	$style = function_exists( 'tca_evloc_resolve_mapbox_style' )
		? tca_evloc_resolve_mapbox_style( $style_choice, $custom_raw, $default_style )
		: $default_style;

	$pin = function_exists( 'tca_evloc_resolve_pin_color' )
		? tca_evloc_resolve_pin_color( $pin_color_key )
		: array(
			'fill'  => '#385DFF',
			'light' => false,
		);

	$opts = array(
		'style'     => $style,
		'height'    => max( 200, min( 1600, $height ) ),
		'pin_shape' => $pin_shape,
		'pin_size'  => max( 5, min( 20, $pin_size ) ),
		'pin_fill'  => $pin['fill'],
		'pin_light' => ! empty( $pin['light'] ),
	);

	/**
	 * Filter listing Map view options.
	 *
	 * @param array $opts    Map options.
	 * @param int   $page_id Events page ID.
	 */
	return apply_filters( 'tca_em_listing_map_options', $opts, $page_id );
}

/**
 * Mapbox style for the EM listing map.
 *
 * @return string
 */
function tca_em_calendar_mapbox_style() {
	$opts  = tca_em_listing_map_options();
	$style = ! empty( $opts['style'] ) ? $opts['style'] : tca_em_calendar_default_mapbox_style();
	return apply_filters( 'tca_em_calendar_mapbox_style', $style );
}

/**
 * Enqueue Mapbox + Events Locations map assets (idempotent).
 *
 * @return void
 */
function tca_enqueue_events_locations_map_assets() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;

	$token = function_exists( 'tca_env' ) ? tca_env( 'MAPBOX_ACCESS_TOKEN' ) : '';
	$js    = get_template_directory() . '/blocks/events-locations/events-map.js';
	$ver   = file_exists( $js ) ? (string) filemtime( $js ) : ( defined( '_S_VERSION' ) ? _S_VERSION : null );

	wp_enqueue_script( 'mapbox-js', 'https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.js', array(), null, true );
	wp_enqueue_style( 'mapbox-css', 'https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.css', array(), null );

	if ( wp_style_is( 'block-events-locations', 'registered' ) ) {
		wp_enqueue_style( 'block-events-locations' );
	}

	wp_enqueue_script(
		'tca-events-locations-map',
		get_template_directory_uri() . '/blocks/events-locations/events-map.js',
		array( 'mapbox-js' ),
		$ver,
		true
	);
	wp_localize_script(
		'tca-events-locations-map',
		'TCA_MAP',
		array(
			'mapboxToken' => $token,
		)
	);
}

/**
 * Load Mapbox on the events listing so Map view can initialize after AJAX.
 *
 * @return void
 */
function tca_em_enqueue_calendar_mapbox() {
	if ( ! tca_em_listing_needs_mapbox() ) {
		return;
	}

	tca_enqueue_events_locations_map_assets();

	$bridge = get_template_directory() . '/js/em-mapbox-calendar.js';
	$ver    = file_exists( $bridge ) ? (string) filemtime( $bridge ) : ( defined( '_S_VERSION' ) ? _S_VERSION : null );

	wp_enqueue_script(
		'tca-em-mapbox-calendar',
		get_template_directory_uri() . '/js/em-mapbox-calendar.js',
		array( 'jquery', 'tca-events-locations-map' ),
		$ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'tca_em_enqueue_calendar_mapbox', 25 );

/**
 * ACF fields on the events listing page for Map view appearance.
 *
 * Registered in PHP so they appear even if the JSON field group has not been synced.
 *
 * @return void
 */
function tca_em_register_listing_map_field_group() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_tca_em_listing_map',
			'title'                 => 'Listing Map',
			'fields'                => array(
				array(
					'key'           => 'field_tca_emlist_map_intro',
					'label'         => 'Calendar Map view',
					'name'          => '',
					'type'          => 'message',
					'message'       => 'These settings control the Map toggle on this events listing: pin style, pin color, and map height.',
					'new_lines'     => 'wpautop',
					'esc_html'      => 0,
				),
				array(
					'key'           => 'field_tca_emlist_style',
					'label'         => 'Mapbox style',
					'name'          => 'listing_mapbox_style',
					'type'          => 'select',
					'instructions'  => 'Base map style for the listing Map view.',
					'choices'       => array(
						'mapbox://styles/tcasoftware/cm7unqmeq01px01qo3jv83ql2' => 'Tysons',
						'mapbox://styles/mapbox/streets-v12'                    => 'Streets',
						'mapbox://styles/mapbox/outdoors-v12'                   => 'Outdoors',
						'mapbox://styles/mapbox/light-v11'                      => 'Light',
						'mapbox://styles/mapbox/dark-v11'                       => 'Dark',
						'mapbox://styles/mapbox/satellite-v9'                   => 'Satellite',
						'mapbox://styles/mapbox/satellite-streets-v12'          => 'Satellite streets',
						'mapbox://styles/mapbox/navigation-day-v1'              => 'Navigation day',
						'mapbox://styles/mapbox/navigation-night-v1'            => 'Navigation night',
						'custom'                                               => 'Custom',
					),
					'default_value' => 'mapbox://styles/tcasoftware/cm7unqmeq01px01qo3jv83ql2',
					'return_format' => 'value',
					'ui'            => 1,
					'allow_null'    => 0,
				),
				array(
					'key'               => 'field_tca_emlist_style_custom',
					'label'             => 'Custom Mapbox style',
					'name'              => 'listing_mapbox_style_custom',
					'type'              => 'text',
					'instructions'      => 'Paste a mapbox://styles/… URL or an https://api.mapbox.com/styles/v1/… style URL.',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_tca_emlist_style',
								'operator' => '==',
								'value'    => 'custom',
							),
						),
					),
					'placeholder'       => 'mapbox://styles/username/styleid',
					'maxlength'         => 512,
				),
				array(
					'key'           => 'field_tca_emlist_height',
					'label'         => 'Map height',
					'name'          => 'listing_map_height',
					'type'          => 'number',
					'instructions'  => 'Height of the listing Map view in pixels.',
					'default_value' => 520,
					'min'           => 200,
					'max'           => 1600,
					'step'          => 1,
					'append'        => 'px',
				),
				array(
					'key'           => 'field_tca_emlist_pin_shape',
					'label'         => 'Pin style',
					'name'          => 'listing_map_pin_shape',
					'type'          => 'select',
					'instructions'  => 'Shape of event location pins.',
					'wrapper'       => array(
						'width' => '50',
					),
					'choices'       => array(
						'teardrop' => 'Teardrop',
						'circle'   => 'Circle',
						'square'   => 'Square',
						'diamond'  => 'Diamond',
					),
					'default_value' => 'teardrop',
					'return_format' => 'value',
					'ui'            => 1,
					'allow_null'    => 0,
				),
				array(
					'key'           => 'field_tca_emlist_pin_color',
					'label'         => 'Pin color',
					'name'          => 'listing_map_pin_color',
					'type'          => 'select',
					'instructions'  => 'Fill color for event location pins.',
					'wrapper'       => array(
						'width' => '50',
					),
					'choices'       => array(
						'blue'   => 'Blue',
						'red'    => 'Red',
						'green'  => 'Green',
						'orange' => 'Orange',
						'purple' => 'Purple',
						'teal'   => 'Teal',
						'gray'   => 'Gray',
						'black'  => 'Black',
						'white'  => 'White',
					),
					'default_value' => 'blue',
					'return_format' => 'value',
					'ui'            => 1,
					'allow_null'    => 0,
				),
				array(
					'key'           => 'field_tca_emlist_pin_size',
					'label'         => 'Pin size',
					'name'          => 'listing_map_pin_size',
					'type'          => 'range',
					'default_value' => 15,
					'min'           => 5,
					'max'           => 20,
					'step'          => 1,
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page',
						'operator' => '==',
						'value'    => '3806',
					),
				),
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-events.php',
					),
				),
			),
			'menu_order'            => 1,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'left',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/include_fields', 'tca_em_register_listing_map_field_group' );
