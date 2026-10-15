<?php
/**
 * Events Manager listing map (calendar Map view).
 *
 * Theme override: Mapbox map matching the Events Locations block instead of Google Maps.
 *
 * @var array $args
 * @package tca
 */

if ( empty( $args['id'] ) ) {
	$args['id'] = wp_rand( 100, PHP_INT_MAX );
}

if ( function_exists( 'tca_enqueue_events_locations_map_assets' ) ) {
	tca_enqueue_events_locations_map_assets();
}

$markers = array();
if ( function_exists( 'tca_evloc_markers_from_em_search_args' ) ) {
	$markers = tca_evloc_markers_from_em_search_args( $args );
}

$map_id = 'tca-em-calendar-map-' . preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $args['id'] );

$map_opts = function_exists( 'tca_em_listing_map_options' )
	? tca_em_listing_map_options()
	: array(
		'style'     => 'mapbox://styles/tcasoftware/cm7unqmeq01px01qo3jv83ql2',
		'height'    => 520,
		'pin_shape' => 'teardrop',
		'pin_size'  => 15,
		'pin_fill'  => '#385DFF',
		'pin_light' => false,
	);

$height = isset( $map_opts['height'] ) ? (int) $map_opts['height'] : 0;
if ( $height < 200 && ! empty( $args['height'] ) ) {
	$height = (int) preg_replace( '/[^0-9]/', '', (string) $args['height'] );
}
if ( $height < 200 ) {
	$height = 520;
}
?>
<div class="tca-em-calendar-map">
	<?php if ( empty( $markers ) ) : ?>
		<p class="tca-em-calendar-map-empty"><?php echo esc_html__( 'No upcoming events with a map location match your filters.', 'tca' ); ?></p>
	<?php elseif ( function_exists( 'tca_evloc_render_map_markup' ) ) : ?>
		<?php
		tca_evloc_render_map_markup(
			$markers,
			array(
				'style'       => $map_opts['style'],
				'height'      => $height,
				'pin_shape'   => $map_opts['pin_shape'],
				'pin_size'    => $map_opts['pin_size'],
				'pin_fill'    => $map_opts['pin_fill'],
				'pin_light'   => $map_opts['pin_light'],
				'map_id'      => $map_id,
				'extra_class' => 'tca-em-calendar-map-wrap',
			)
		);
		?>
	<?php endif; ?>
</div>
