<?php
/**
 * Events Location and List — 50% accordion list + map (no map overlay).
 *
 * @package tca
 */

if ( render_block_preview_if_applicable( $block ) ) {
	return;
}

$container_settings = get_field( 'container_settings' );
$section_header     = get_field( 'section_header' );
include __DIR__ . '/../../inc/common_block_variables.php';

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

drawSectionHeader( $section_title_size, $section_title, $title_alignment, $show_underline, $section_intro, $section_button, $section_button_style );

if ( ! class_exists( 'EM_Events' ) || ! function_exists( 'em_get_location' ) || ! function_exists( 'tca_evloc_markers_from_location_posts' ) ) :
	?>
	<div class="uk-alert-warning" uk-alert>
		<p><?php echo esc_html__( 'Events Manager plugin is not available.', 'tca' ); ?></p>
	</div>
	<?php
	closeSection( $wrap_size, $container_size, $container_type, $overlapping_graphic );
	return;
endif;

$style = tca_evloc_resolve_mapbox_style(
	function_exists( 'get_field' ) ? get_field( 'mapbox_style' ) : '',
	function_exists( 'get_field' ) ? get_field( 'mapbox_style_custom' ) : ''
);

$map_height_raw = function_exists( 'get_field' ) ? get_field( 'map_height' ) : null;
$map_height     = is_numeric( $map_height_raw ) ? (int) $map_height_raw : 520;
$map_height     = max( 280, min( 1600, $map_height ) );

$allowed_pin_shapes = array( 'teardrop', 'circle', 'square', 'diamond' );
$pin_shape_raw      = function_exists( 'get_field' ) ? get_field( 'map_pin_shape' ) : '';
$map_pin_shape      = in_array( $pin_shape_raw, $allowed_pin_shapes, true ) ? $pin_shape_raw : 'teardrop';
$map_pin_size_raw   = function_exists( 'get_field' ) ? get_field( 'map_pin_size' ) : null;
$map_pin_size       = is_numeric( $map_pin_size_raw ) ? (int) $map_pin_size_raw : 15;

$pin_color     = tca_evloc_resolve_pin_color( function_exists( 'get_field' ) ? get_field( 'map_pin_color' ) : '' );
$map_pin_fill  = $pin_color['fill'];
$map_pin_light = $pin_color['light'];

$show_upcoming_events = (bool) ( function_exists( 'get_field' ) ? get_field( 'show_upcoming_events' ) : true );

$no_events_behavior_raw = function_exists( 'get_field' ) ? get_field( 'no_events_behavior' ) : '';
$no_events_behavior     = ( 'Show Message' === $no_events_behavior_raw ) ? 'show_message' : 'hide';
$no_events_message_raw  = function_exists( 'get_field' ) ? get_field( 'no_events_message' ) : '';
$no_events_message_raw  = is_string( $no_events_message_raw ) ? $no_events_message_raw : '';

$filter_by_category = (bool) ( function_exists( 'get_field' ) ? get_field( 'filter_by_category' ) : false );
$category_term_ids  = array();
if ( $filter_by_category && $show_upcoming_events ) {
	$category_term_ids = tca_evloc_category_term_ids( function_exists( 'get_field' ) ? get_field( 'event_category' ) : null );
}

$location_post_ids = tca_evloc_location_post_ids( function_exists( 'get_field' ) ? get_field( 'event_locations' ) : null );
$markers           = tca_evloc_markers_from_location_posts( $location_post_ids, $show_upcoming_events, $category_term_ids );

$wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
	? get_block_wrapper_attributes( array( 'class' => 'tca-evloc-list-block' ) )
	: 'class="tca-evloc-list-block"';

$map_id = isset( $block['anchor'] ) && $block['anchor'] ? sanitize_title( $block['anchor'] ) : '';
if ( '' === $map_id ) {
	$map_id = 'tca-evloc-list-map-' . ( isset( $block['id'] ) ? preg_replace( '/[^a-zA-Z0-9_-]/', '', $block['id'] ) : uniqid( '', false ) );
}

$markers_json = wp_json_encode(
	$markers,
	JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);

if ( function_exists( 'tca_enqueue_events_location_list_map_assets' ) ) {
	tca_enqueue_events_location_list_map_assets();
}
?>

<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( empty( $location_post_ids ) ) : ?>
		<p class="tca-evloc-list-empty"><?php echo esc_html__( 'Select one or more locations in the block settings.', 'tca' ); ?></p>
	<?php elseif ( empty( $markers ) ) : ?>
		<p class="tca-evloc-list-empty"><?php echo esc_html__( 'Selected locations do not have map coordinates yet.', 'tca' ); ?></p>
	<?php else : ?>
		<div
			class="tca-evloc-list-wrap"
			data-tca-evloc-list
			data-map-style="<?php echo esc_attr( $style ); ?>"
			data-mapbox-token="<?php echo esc_attr( function_exists( 'tca_env' ) ? tca_env( 'MAPBOX_ACCESS_TOKEN' ) : '' ); ?>"
			data-pin-shape="<?php echo esc_attr( $map_pin_shape ); ?>"
			data-pin-light="<?php echo $map_pin_light ? '1' : '0'; ?>"
			data-pin-size="<?php echo (int) $map_pin_size; ?>"
			data-markers="<?php echo esc_attr( $markers_json ); ?>"
			style="<?php echo esc_attr( '--tca-pin-fill:' . $map_pin_fill . ';--tca-evloc-list-height:' . (int) $map_height . 'px;' ); ?>"
		>
			<div class="tca-evloc-list-layout">
				<aside class="tca-evloc-list-sidebar" aria-label="<?php echo esc_attr__( 'Locations', 'tca' ); ?>">
					<ul class="tca-evloc-list" role="list">
						<?php foreach ( $markers as $index => $marker ) : ?>
							<?php
							$panel_id   = $map_id . '-panel-' . (int) $index;
							$button_id  = $map_id . '-btn-' . (int) $index;
							$loc_events = ( ! empty( $marker['events'] ) && is_array( $marker['events'] ) ) ? $marker['events'] : array();
							$has_image  = ! empty( $marker['imageUrl'] );
							?>
							<li
								class="tca-evloc-list__item"
								data-marker-index="<?php echo (int) $index; ?>"
							>
								<button
									type="button"
									class="tca-evloc-list__title"
									id="<?php echo esc_attr( $button_id ); ?>"
									data-marker-index="<?php echo (int) $index; ?>"
									aria-expanded="false"
									aria-controls="<?php echo esc_attr( $panel_id ); ?>"
								>
									<span class="tca-evloc-list__title-text"><?php echo esc_html( $marker['locationName'] ); ?></span>
									<span class="tca-evloc-list__chevron" aria-hidden="true"></span>
								</button>

								<div
									class="tca-evloc-list__panel"
									id="<?php echo esc_attr( $panel_id ); ?>"
									role="region"
									aria-labelledby="<?php echo esc_attr( $button_id ); ?>"
									hidden
								>
									<div class="tca-evloc-list__detail">
										<?php if ( $has_image ) : ?>
											<div class="tca-evloc-list__image-wrap">
												<img
													src="<?php echo esc_url( $marker['imageUrl'] ); ?>"
													alt="<?php echo esc_attr( $marker['locationName'] ); ?>"
													width="100"
													height="100"
													loading="lazy"
													decoding="async"
												/>
											</div>
										<?php endif; ?>

										<div class="tca-evloc-list__meta">
											<?php if ( ! empty( $marker['address'] ) ) : ?>
												<p class="tca-evloc-list__address"><?php echo esc_html( $marker['address'] ); ?></p>
											<?php endif; ?>

											<?php if ( ! empty( $marker['websiteUrl'] ) ) : ?>
												<p class="tca-evloc-list__website">
													<a href="<?php echo esc_url( $marker['websiteUrl'] ); ?>" target="_blank" rel="noopener noreferrer">
														<?php echo esc_html__( 'Website »', 'tca' ); ?>
													</a>
												</p>
											<?php elseif ( ! empty( $marker['websiteLabel'] ) ) : ?>
												<p class="tca-evloc-list__website"><?php echo esc_html( $marker['websiteLabel'] ); ?></p>
											<?php endif; ?>

											<?php if ( $show_upcoming_events ) : ?>
												<?php if ( ! empty( $loc_events ) ) : ?>
													<div class="tca-evloc-list__events">
														<h4 class="tca-evloc-list__events-heading"><?php echo esc_html__( 'Upcoming events', 'tca' ); ?></h4>
														<ul class="tca-evloc-list__events-list">
															<?php foreach ( $loc_events as $ev ) : ?>
																<li>
																	<?php if ( ! empty( $ev['url'] ) ) : ?>
																		<a href="<?php echo esc_url( $ev['url'] ); ?>"><?php echo esc_html( $ev['title'] ); ?></a>
																	<?php else : ?>
																		<span><?php echo esc_html( $ev['title'] ); ?></span>
																	<?php endif; ?>
																	<?php if ( ! empty( $ev['date'] ) ) : ?>
																		<span class="tca-evloc-list__event-date"><?php echo esc_html( $ev['date'] ); ?></span>
																	<?php endif; ?>
																</li>
															<?php endforeach; ?>
														</ul>
													</div>
												<?php elseif ( 'show_message' === $no_events_behavior ) : ?>
													<div class="tca-evloc-list__events">
														<p class="tca-evloc-list__events-empty">
															<?php
															echo esc_html(
																'' !== trim( $no_events_message_raw )
																	? $no_events_message_raw
																	: __( 'No upcoming events at this location.', 'tca' )
															);
															?>
														</p>
													</div>
												<?php endif; ?>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</aside>

				<div class="tca-evloc-list-map-panel">
					<div class="tca-evloc-list-map-shell">
						<div class="tca-evloc-list-map-stage">
							<div
								id="<?php echo esc_attr( $map_id ); ?>"
								class="tca-evloc-list-map-canvas"
								role="presentation"
							></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>

<?php
closeSection( $wrap_size, $container_size, $container_type, $overlapping_graphic );
