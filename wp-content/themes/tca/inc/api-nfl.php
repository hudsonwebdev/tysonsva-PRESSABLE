<?php
/**
 * API-Sports American Football / NFL (v1) proxy for theme blocks.
 *
 * Docs: https://api-sports.io/documentation/nfl/v1
 * Host: https://v1.american-football.api-sports.io
 *
 * Key resolution order:
 * 1. TCA_API_NFL_KEY constant (wp-config.php)
 * 2. TCA_API_NFL_KEY from .env via tca_env()
 * 3. Filter {@see 'tca_api_nfl_key'}
 * 4. Shared soccer API-Football key (same dashboard key often works across products)
 *
 * @package tca
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * API key for v1.american-football.api-sports.io requests.
 *
 * @return string
 */
function tca_api_nfl_get_key() {
	if ( defined( 'TCA_API_NFL_KEY' ) && TCA_API_NFL_KEY !== '' ) {
		return (string) TCA_API_NFL_KEY;
	}

	if ( function_exists( 'tca_env' ) ) {
		$from_env = (string) tca_env( 'TCA_API_NFL_KEY', '' );
		if ( '' !== $from_env ) {
			return $from_env;
		}
	}

	/**
	 * Filter the NFL / American Football API key when not set via constant or .env.
	 *
	 * @param string $key Default empty.
	 */
	$filtered = (string) apply_filters( 'tca_api_nfl_key', '' );
	if ( '' !== $filtered ) {
		return $filtered;
	}

	// Same API-Sports account key can cover multiple sports if the product is enabled.
	if ( function_exists( 'tca_api_football_get_key' ) ) {
		return (string) tca_api_football_get_key();
	}

	return '';
}

/**
 * Default NFL league ID (1 = NFL per API-Sports docs).
 *
 * @return int
 */
function tca_api_nfl_default_league_id() {
	return (int) apply_filters( 'tca_api_nfl_default_league_id', 1 );
}

/**
 * Default season year for NFL schedule (season start year, e.g. 2025 for 2025–26).
 *
 * @return int
 */
function tca_api_nfl_default_season() {
	$year = (int) gmdate( 'Y' );
	// NFL season typically spans Sep–Feb; before March use previous calendar year as season key.
	$month = (int) gmdate( 'n' );
	if ( $month < 3 ) {
		--$year;
	}
	return (int) apply_filters( 'tca_api_nfl_default_season', $year );
}

/**
 * Display timezone for NFL kickoff times.
 *
 * @return string
 */
function tca_api_nfl_display_timezone() {
	if ( function_exists( 'tca_api_football_display_timezone' ) ) {
		return (string) tca_api_football_display_timezone();
	}
	return (string) apply_filters( 'tca_api_nfl_display_timezone', 'America/New_York' );
}

/**
 * Low-level GET against the American Football API.
 *
 * @param string               $path  Path beginning with / (e.g. /games).
 * @param array<string, mixed> $query Query args.
 * @return array|WP_Error Decoded associative body or error.
 */
function tca_api_nfl_request( $path, array $query = array() ) {
	$key = tca_api_nfl_get_key();
	if ( '' === $key ) {
		return new WP_Error( 'tca_nfl_no_key', __( 'NFL API key is not configured. Add TCA_API_NFL_KEY to .env or wp-config.php.', 'tca' ) );
	}

	$path = '/' . ltrim( (string) $path, '/' );
	$url  = 'https://v1.american-football.api-sports.io' . $path;
	if ( ! empty( $query ) ) {
		$url = add_query_arg( $query, $url );
	}

	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 25,
			'headers' => array(
				'x-apisports-key' => $key,
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = wp_remote_retrieve_response_code( $response );
	if ( $code < 200 || $code >= 300 ) {
		return new WP_Error(
			'tca_nfl_http',
			sprintf(
				/* translators: %d HTTP status code */
				__( 'NFL API request failed (HTTP %d). Confirm American Football is enabled on your API-Sports plan.', 'tca' ),
				(int) $code
			)
		);
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( ! is_array( $data ) ) {
		return new WP_Error( 'tca_nfl_json', __( 'Invalid NFL API response.', 'tca' ) );
	}

	if ( isset( $data['errors'] ) && ! empty( $data['errors'] ) ) {
		$msg = is_array( $data['errors'] ) ? wp_json_encode( $data['errors'] ) : (string) $data['errors'];
		return new WP_Error( 'tca_nfl_api_error', $msg ? $msg : __( 'NFL API returned an error.', 'tca' ) );
	}

	return $data;
}

/**
 * Fetch games for a league and season.
 *
 * @param int $league_id League ID (1 = NFL).
 * @param int $season    Season year.
 * @return array|WP_Error
 */
function tca_api_nfl_fetch_games( $league_id, $season ) {
	$league_id = absint( $league_id );
	$season    = absint( $season );
	if ( ! $league_id || ! $season ) {
		return new WP_Error( 'tca_nfl_bad_params', __( 'Invalid league or season.', 'tca' ) );
	}

	return tca_api_nfl_request(
		'/games',
		array(
			'league' => $league_id,
			'season' => $season,
		)
	);
}

/**
 * Cached season schedule.
 *
 * @param int $league_id League ID.
 * @param int $season    Season year.
 * @return array|WP_Error
 */
function tca_api_nfl_get_games_cached( $league_id, $season ) {
	$league_id = absint( $league_id );
	$season    = absint( $season );
	$cache_key = 'tca_nfl_games_' . $league_id . '_' . $season;
	$ttl       = (int) apply_filters( 'tca_api_nfl_games_cache_seconds', (int) DAY_IN_SECONDS, $league_id, $season );

	if ( $ttl > 0 ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) && isset( $cached['response'] ) ) {
			return $cached;
		}
	}

	$data = tca_api_nfl_fetch_games( $league_id, $season );
	if ( $ttl > 0 && ! is_wp_error( $data ) && is_array( $data ) ) {
		set_transient( $cache_key, $data, $ttl );
	}

	return $data;
}

/**
 * Fetch live games (optional league/season filters).
 *
 * @param int $league_id League ID or 0 for all.
 * @param int $season    Season year or 0.
 * @return array|WP_Error
 */
function tca_api_nfl_fetch_live_games( $league_id = 0, $season = 0 ) {
	$query = array( 'live' => 'all' );
	$league_id = absint( $league_id );
	$season    = absint( $season );
	if ( $league_id ) {
		$query['league'] = $league_id;
	}
	if ( $season ) {
		$query['season'] = $season;
	}
	return tca_api_nfl_request( '/games', $query );
}

/**
 * Cached live games (short TTL).
 *
 * @param int $league_id League ID or 0.
 * @param int $season    Season year or 0.
 * @return array|WP_Error
 */
function tca_api_nfl_get_live_games_cached( $league_id = 0, $season = 0 ) {
	$league_id = absint( $league_id );
	$season    = absint( $season );
	$cache_key = 'tca_nfl_live_' . $league_id . '_' . $season;
	$ttl       = (int) apply_filters( 'tca_api_nfl_live_cache_seconds', 45, $league_id, $season );

	if ( $ttl > 0 ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) && isset( $cached['response'] ) ) {
			return $cached;
		}
	}

	$data = tca_api_nfl_fetch_live_games( $league_id, $season );
	if ( $ttl > 0 && ! is_wp_error( $data ) && is_array( $data ) ) {
		set_transient( $cache_key, $data, $ttl );
	}

	return $data;
}

/**
 * Clear cached schedule for a league+season.
 *
 * @param int $league_id League ID.
 * @param int $season    Season year.
 * @return bool
 */
function tca_api_nfl_delete_games_cache( $league_id, $season ) {
	return (bool) delete_transient( 'tca_nfl_games_' . absint( $league_id ) . '_' . absint( $season ) );
}

/**
 * Normalize NFL game date node into a UTC ISO-ish string for DateTimeImmutable.
 *
 * API may return either a string or { date, time, timezone }.
 *
 * @param mixed $date_node game.date from API.
 * @return string
 */
function tca_api_nfl_normalize_game_datetime( $date_node ) {
	if ( is_string( $date_node ) && '' !== trim( $date_node ) ) {
		return trim( $date_node );
	}
	if ( ! is_array( $date_node ) ) {
		return '';
	}

	$date = isset( $date_node['date'] ) ? trim( (string) $date_node['date'] ) : '';
	$time = isset( $date_node['time'] ) ? trim( (string) $date_node['time'] ) : '';
	$tz   = isset( $date_node['timezone'] ) ? trim( (string) $date_node['timezone'] ) : '';

	if ( '' === $date ) {
		return '';
	}

	if ( '' === $time ) {
		$time = '00:00:00';
	} elseif ( 1 === preg_match( '/^\d{2}:\d{2}$/', $time ) ) {
		$time .= ':00';
	}

	$combined = $date . ' ' . $time;
	if ( '' === $tz ) {
		$tz = 'UTC';
	}

	try {
		$local = new DateTimeImmutable( $combined, new DateTimeZone( $tz ) );
		return $local->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d\TH:i:sP' );
	} catch ( Exception $e ) {
		return $date . 'T' . $time . '+00:00';
	}
}

/**
 * Format kickoff date/time parts (reuses soccer helpers when available).
 *
 * @param string $iso_datetime UTC ISO datetime.
 * @param string $date_order   month_day|day_month.
 * @param string $tz_iana      IANA timezone.
 * @return string[]{ date: string, time: string, timezone: string, time_label: string }
 */
function tca_api_nfl_format_game_display_parts( $iso_datetime, $date_order, $tz_iana ) {
	if ( function_exists( 'tca_api_football_format_fixture_display_parts' ) ) {
		return tca_api_football_format_fixture_display_parts( $iso_datetime, $date_order, $tz_iana );
	}
	return array(
		'date'       => '',
		'time'       => '',
		'timezone'   => '',
		'time_label' => '',
	);
}

/**
 * Local Y-m-d key for schedule scroll-to-today.
 *
 * @param string $iso_datetime UTC ISO.
 * @param string $tz_iana      IANA timezone.
 * @return string
 */
function tca_api_nfl_game_local_date_key( $iso_datetime, $tz_iana ) {
	if ( function_exists( 'tca_api_football_fixture_local_date_key' ) ) {
		return tca_api_football_fixture_local_date_key( $iso_datetime, $tz_iana );
	}
	return '';
}

/**
 * Venue / city from an NFL game row.
 *
 * @param array<string, mixed> $game Game node.
 * @return string[]{ venue: string, city: string, label: string }
 */
function tca_api_nfl_format_game_location( array $game ) {
	$venue = isset( $game['venue'] ) && is_array( $game['venue'] ) ? $game['venue'] : array();
	$name  = isset( $venue['name'] ) ? trim( (string) $venue['name'] ) : '';
	$city  = isset( $venue['city'] ) ? trim( (string) $venue['city'] ) : '';

	$label = '';
	if ( '' !== $name && '' !== $city ) {
		$label = $name . ', ' . $city;
	} elseif ( '' !== $name ) {
		$label = $name;
	} elseif ( '' !== $city ) {
		$label = $city;
	}

	return array(
		'venue' => $name,
		'city'  => $city,
		'label' => $label,
	);
}

/**
 * Extract home/away total scores from an NFL row.
 *
 * @param array<string, mixed> $match Full response row.
 * @return array{0: ?string, 1: ?string} Home and away totals as strings, or nulls.
 */
function tca_api_nfl_extract_totals( array $match ) {
	$scores = isset( $match['scores'] ) && is_array( $match['scores'] ) ? $match['scores'] : array();
	$home   = isset( $scores['home'] ) && is_array( $scores['home'] ) ? $scores['home'] : array();
	$away   = isset( $scores['away'] ) && is_array( $scores['away'] ) ? $scores['away'] : array();

	$gh = null;
	$ga = null;
	if ( isset( $home['total'] ) && is_numeric( $home['total'] ) ) {
		$gh = (string) (int) $home['total'];
	}
	if ( isset( $away['total'] ) && is_numeric( $away['total'] ) ) {
		$ga = (string) (int) $away['total'];
	}

	return array( $gh, $ga );
}

/**
 * Sort key datetime for a game row.
 *
 * @param array<string, mixed> $match Response row.
 * @return string
 */
function tca_api_nfl_row_datetime( array $match ) {
	$game = isset( $match['game'] ) && is_array( $match['game'] ) ? $match['game'] : array();
	return tca_api_nfl_normalize_game_datetime( isset( $game['date'] ) ? $game['date'] : '' );
}
