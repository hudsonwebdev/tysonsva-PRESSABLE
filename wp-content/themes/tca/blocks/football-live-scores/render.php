<?php
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

if ( ! function_exists( 'tca_api_football_get_live_scores_state' ) ) {
	?>
	<div class="tca-football-fixtures-block">
		<p class="tca-football-fixtures__message tca-football-fixtures__message--error"><?php echo esc_html__( 'Football API helpers are not loaded.', 'tca' ); ?></p>
	</div>
	<?php
	closeSection( $wrap_size, $container_size, $container_type, $overlapping_graphic );
	return;
}

$league_id = function_exists( 'tca_api_football_world_cup_league_id' )
	? tca_api_football_world_cup_league_id()
	: 1;

$season_raw     = get_field( 'season_year' );
$season_default = function_exists( 'tca_api_football_world_cup_season_default' )
	? tca_api_football_world_cup_season_default()
	: 2026;
$season = is_numeric( $season_raw ) ? max( 1900, min( 2100, (int) $season_raw ) ) : $season_default;

$poll_raw     = get_field( 'poll_interval_seconds' );
$poll_seconds = is_numeric( $poll_raw ) ? max( 15, min( 120, (int) $poll_raw ) ) : 45;

$max_h_raw          = get_field( 'list_max_height' );
$list_max_height_px = 0;
if ( is_numeric( $max_h_raw ) ) {
	$list_max_height_px = max( 0, min( 3000, (int) $max_h_raw ) );
}

$icon_raw     = get_field( 'fixtures_team_icons' );
$icon_allowed = array( 'logos', 'logos_small', 'flags', 'flags_small' );
$icon_mode    = in_array( $icon_raw, $icon_allowed, true ) ? $icon_raw : 'flags_small';

$display_tz = tca_api_football_display_timezone();

$wrapper_classes = array( 'tca-football-fixtures-block', 'tca-football-live-scores-block' );
if ( false !== strpos( $icon_mode, 'small' ) ) {
	$wrapper_classes[] = 'tca-football-fixtures-block--icons-sm';
}
if ( false !== strpos( $icon_mode, 'flags' ) ) {
	$wrapper_classes[] = 'tca-football-fixtures-block--icons-flags';
}

$state = tca_api_football_get_live_scores_state( $league_id, $season, $icon_mode, $display_tz );

$wrapper = function_exists( 'get_block_wrapper_attributes' )
	? get_block_wrapper_attributes(
		array(
			'class'                => implode( ' ', $wrapper_classes ),
			'data-tca-live-scores' => '1',
			'data-league'          => (string) $league_id,
			'data-season'          => (string) $season,
			'data-poll'            => (string) $poll_seconds,
			'data-icons'           => $icon_mode,
			'data-scope'           => 'league',
		)
	)
	: 'class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . ' wp-block-tca-football-live-scores" data-tca-live-scores="1" data-league="' . esc_attr( (string) $league_id ) . '" data-season="' . esc_attr( (string) $season ) . '" data-poll="' . esc_attr( (string) $poll_seconds ) . '" data-icons="' . esc_attr( $icon_mode ) . '" data-scope="league"';

$list_classes = 'tca-football-fixtures__list';
$list_style   = '';
if ( $list_max_height_px > 0 ) {
	$list_classes .= ' tca-football-fixtures__list--scroll';
	$list_style    = 'max-height:' . (int) $list_max_height_px . 'px;';
}

$live_count    = 0;
$layout        = 'upcoming_only';
$body_html     = '';
$has_error     = false;
$error_message = '';

if ( is_wp_error( $state ) ) {
	$has_error     = true;
	$error_message = $state->get_error_message();
} else {
	$live_count = (int) $state['count'];
	$layout     = isset( $state['layout'] ) ? (string) $state['layout'] : 'upcoming_only';
	$body_html  = isset( $state['body_html'] ) ? (string) $state['body_html'] : '';
}
?>

<div <?php echo $wrapper; ?>>
	<?php if ( $has_error ) : ?>
		<p class="tca-football-fixtures__message tca-football-fixtures__message--error" role="alert">
			<?php echo esc_html( $error_message ); ?>
		</p>
	<?php else : ?>
		<div class="<?php echo esc_attr( $list_classes ); ?>" data-live-body data-tca-football-schedule="1" data-display-tz="<?php echo esc_attr( $display_tz ); ?>" data-layout="<?php echo esc_attr( $layout ); ?>" data-live-count="<?php echo (int) $live_count; ?>"<?php echo $list_style ? ' style="' . esc_attr( $list_style ) . '"' : ''; ?>>
			<?php echo $body_html; ?>
		</div>
		<p class="tca-football-live-scores__updated" aria-live="polite">
			<?php echo esc_html__( 'Scores refresh automatically.', 'tca' ); ?>
		</p>
	<?php endif; ?>
</div>

<?php
closeSection( $wrap_size, $container_size, $container_type, $overlapping_graphic );
