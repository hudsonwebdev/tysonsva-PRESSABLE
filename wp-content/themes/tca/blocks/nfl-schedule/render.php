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

if ( ! function_exists( 'tca_api_nfl_get_games_cached' ) || ! function_exists( 'tca_api_nfl_format_game_display_parts' ) ) {
	?>
	<div class="tca-football-fixtures-block tca-nfl-schedule-block">
		<p class="tca-football-fixtures__message tca-football-fixtures__message--error"><?php echo esc_html__( 'NFL API helpers are not loaded.', 'tca' ); ?></p>
	</div>
	<?php
	closeSection( $wrap_size, $container_size, $container_type, $overlapping_graphic );
	return;
}

$league_raw = function_exists( 'get_field' ) ? get_field( 'league_id' ) : null;
$season_raw = function_exists( 'get_field' ) ? get_field( 'season_year' ) : null;

$league_id = is_numeric( $league_raw ) ? max( 1, (int) $league_raw ) : tca_api_nfl_default_league_id();
$season    = is_numeric( $season_raw ) ? max( 1900, min( 2100, (int) $season_raw ) ) : tca_api_nfl_default_season();

$date_order_raw      = function_exists( 'get_field' ) ? get_field( 'fixtures_date_order' ) : null;
$fixtures_date_order = ( 'day_month' === $date_order_raw ) ? 'day_month' : 'month_day';

$tz_raw     = function_exists( 'get_field' ) ? get_field( 'display_timezone' ) : '';
$tz_raw     = is_string( $tz_raw ) ? trim( $tz_raw ) : '';
$display_tz = '' !== $tz_raw ? $tz_raw : tca_api_nfl_display_timezone();
try {
	new DateTimeZone( $display_tz );
} catch ( Exception $e ) {
	$display_tz = 'America/New_York';
}

$max_h_raw          = function_exists( 'get_field' ) ? get_field( 'schedule_max_height' ) : null;
$list_max_height_px = 0;
if ( is_numeric( $max_h_raw ) ) {
	$list_max_height_px = max( 0, min( 3000, (int) $max_h_raw ) );
}

$wrapper_classes = array( 'tca-football-fixtures-block', 'tca-nfl-schedule-block', 'tca-football-fixtures-block--has-location', 'tca-football-fixtures-block--icons-sm' );

$api = tca_api_nfl_get_games_cached( $league_id, $season );

$wrapper = function_exists( 'get_block_wrapper_attributes' )
	? get_block_wrapper_attributes(
		array(
			'class' => implode( ' ', $wrapper_classes ),
		)
	)
	: 'class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . ' wp-block-tca-nfl-schedule"';

?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<?php
	if ( is_wp_error( $api ) ) :
		?>
		<p class="tca-football-fixtures__message tca-football-fixtures__message--error" role="alert">
			<?php echo esc_html( $api->get_error_message() ); ?>
		</p>
		<?php
	else :
		$rows = isset( $api['response'] ) && is_array( $api['response'] ) ? $api['response'] : array();
		if ( ! empty( $rows ) ) {
			usort(
				$rows,
				static function ( $a, $b ) {
					$da = is_array( $a ) ? tca_api_nfl_row_datetime( $a ) : '';
					$db = is_array( $b ) ? tca_api_nfl_row_datetime( $b ) : '';
					return strcmp( (string) $da, (string) $db );
				}
			);
		}
		if ( empty( $rows ) ) :
			?>
			<p class="tca-football-fixtures__message"><?php echo esc_html__( 'No games found.', 'tca' ); ?></p>
			<?php
		else :
			$list_classes = 'tca-football-fixtures__list';
			$list_style   = '';
			if ( $list_max_height_px > 0 ) {
				$list_classes .= ' tca-football-fixtures__list--scroll';
				$list_style    = 'max-height:' . (int) $list_max_height_px . 'px;';
			}

			$img_w = 24;
			$img_h = 24;
			?>
			<div
				class="<?php echo esc_attr( $list_classes ); ?>"
				role="list"
				data-tca-football-schedule="1"
				data-tca-nfl-schedule="1"
				data-display-tz="<?php echo esc_attr( $display_tz ); ?>"
				<?php echo $list_style ? ' style="' . esc_attr( $list_style ) . '"' : ''; ?>
			>
				<?php
				foreach ( $rows as $match ) :
					if ( ! is_array( $match ) ) {
						continue;
					}
					$game  = isset( $match['game'] ) && is_array( $match['game'] ) ? $match['game'] : array();
					$teams = isset( $match['teams'] ) && is_array( $match['teams'] ) ? $match['teams'] : array();
					$home  = isset( $teams['home'] ) && is_array( $teams['home'] ) ? $teams['home'] : array();
					$away  = isset( $teams['away'] ) && is_array( $teams['away'] ) ? $teams['away'] : array();
					$hname = isset( $home['name'] ) ? (string) $home['name'] : '';
					$aname = isset( $away['name'] ) ? (string) $away['name'] : '';
					$hlogo = isset( $home['logo'] ) ? esc_url( (string) $home['logo'] ) : '';
					$alogo = isset( $away['logo'] ) ? esc_url( (string) $away['logo'] ) : '';

					$dateiso  = tca_api_nfl_normalize_game_datetime( isset( $game['date'] ) ? $game['date'] : '' );
					$date_key = tca_api_nfl_game_local_date_key( $dateiso, $display_tz );
					list( $gh, $ga ) = tca_api_nfl_extract_totals( $match );
					$parts    = tca_api_nfl_format_game_display_parts( $dateiso, $fixtures_date_order, $display_tz );
					$location = tca_api_nfl_format_game_location( $game );

					$week = isset( $game['week'] ) ? trim( (string) $game['week'] ) : '';
					?>
					<article class="tca-football-fixtures__row" role="listitem"<?php echo '' !== $date_key ? ' data-fixture-date="' . esc_attr( $date_key ) . '"' : ''; ?>>
						<div class="tca-football-fixtures__row-time">
							<?php if ( '' !== $week ) : ?>
								<span class="tca-football-fixtures__date"><?php echo esc_html( $week ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $parts['date'] ) ) : ?>
								<span class="tca-football-fixtures__<?php echo '' !== $week ? 'clock' : 'date'; ?>"><?php echo esc_html( $parts['date'] ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $parts['time_label'] ) ) : ?>
								<span class="tca-football-fixtures__clock"><?php echo esc_html( $parts['time_label'] ); ?></span>
							<?php elseif ( ! empty( $parts['time'] ) ) : ?>
								<span class="tca-football-fixtures__clock"><?php echo esc_html( $parts['time'] ); ?></span>
							<?php endif; ?>
						</div>
						<div class="tca-football-fixtures__row-main">
							<div class="tca-football-fixtures__side tca-football-fixtures__side--home">
								<?php if ( $hlogo ) : ?>
									<span class="tca-football-fixtures__logo-wrap">
										<img class="tca-football-fixtures__logo" src="<?php echo esc_url( $hlogo ); ?>" alt="" width="<?php echo (int) $img_w; ?>" height="<?php echo (int) $img_h; ?>" loading="lazy" decoding="async" />
									</span>
								<?php endif; ?>
								<span class="tca-football-fixtures__team-name"><?php echo esc_html( $hname ); ?></span>
							</div>
							<div class="tca-football-fixtures__center">
								<?php if ( null !== $gh && null !== $ga ) : ?>
									<span class="tca-football-fixtures__score">
										<span class="tca-football-fixtures__score-num"><?php echo esc_html( $gh ); ?></span>
										<span class="tca-football-fixtures__score-sep" aria-hidden="true">-</span>
										<span class="tca-football-fixtures__score-num"><?php echo esc_html( $ga ); ?></span>
									</span>
								<?php else : ?>
									<span class="tca-football-fixtures__vs"><?php echo esc_html_x( 'vs', 'versus short', 'tca' ); ?></span>
								<?php endif; ?>
							</div>
							<div class="tca-football-fixtures__side tca-football-fixtures__side--away">
								<span class="tca-football-fixtures__team-name"><?php echo esc_html( $aname ); ?></span>
								<?php if ( $alogo ) : ?>
									<span class="tca-football-fixtures__logo-wrap">
										<img class="tca-football-fixtures__logo" src="<?php echo esc_url( $alogo ); ?>" alt="" width="<?php echo (int) $img_w; ?>" height="<?php echo (int) $img_h; ?>" loading="lazy" decoding="async" />
									</span>
								<?php endif; ?>
							</div>
						</div>
						<?php if ( ! empty( $location['label'] ) ) : ?>
							<div class="tca-football-fixtures__row-location">
								<?php if ( ! empty( $location['venue'] ) ) : ?>
									<span class="tca-football-fixtures__venue"><?php echo esc_html( $location['venue'] ); ?></span>
								<?php endif; ?>
								<?php if ( ! empty( $location['city'] ) ) : ?>
									<span class="tca-football-fixtures__city"><?php echo esc_html( $location['city'] ); ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</article>
					<?php
				endforeach;
				?>
			</div>
			<?php
		endif;
	endif;
	?>
</div>

<?php
closeSection( $wrap_size, $container_size, $container_type, $overlapping_graphic );
