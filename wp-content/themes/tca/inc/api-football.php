<?php
/**
 * API-Football (api-sports.io) proxy for theme blocks.
 *
 * Set TCA_API_FOOTBALL_KEY in wp-config.php, or use the {@see 'tca_api_football_key'} filter.
 *
 * @package tca
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * API key for v3.football.api-sports.io requests.
 *
 * @return string
 */
function tca_api_football_get_key() {
	if ( defined( 'TCA_API_FOOTBALL_KEY' ) && TCA_API_FOOTBALL_KEY !== '' ) {
		return (string) TCA_API_FOOTBALL_KEY;
	}
	/**
	 * Filter the API-Football key when TCA_API_FOOTBALL_KEY is not defined.
	 *
	 * @param string $key Default empty.
	 */
	return (string) apply_filters( 'tca_api_football_key', '' );
}

/**
 * Legacy fallback when no key is configured (previously hard-coded in functions.php).
 * Prefer `define( 'TCA_API_FOOTBALL_KEY', '…' );` in wp-config.php and remove this filter.
 */
add_filter(
	'tca_api_football_key',
	static function ( $key ) {
		if ( is_string( $key ) && '' !== $key ) {
			return $key;
		}
		return '336bd9a6753a3a08a2f09d7aaf2c196b';
	},
	1000
);

/**
 * Fetch fixtures for a league and season (server-side only).
 *
 * @param int $league_id API-Football league ID.
 * @param int $season    Four-digit season year.
 * @return array|WP_Error Decoded API body (associative) or error.
 */
function tca_api_football_fetch_fixtures( $league_id, $season ) {
	$league_id = absint( $league_id );
	$season    = absint( $season );
	if ( ! $league_id || ! $season ) {
		return new WP_Error( 'tca_football_bad_params', __( 'Invalid league or season.', 'tca' ) );
	}

	$key = tca_api_football_get_key();
	if ( '' === $key ) {
		return new WP_Error( 'tca_football_no_key', __( 'API Football key is not configured.', 'tca' ) );
	}

	$url = add_query_arg(
		array(
			'league' => $league_id,
			'season' => $season,
		),
		'https://v3.football.api-sports.io/fixtures'
	);

	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 20,
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
			'tca_football_http',
			sprintf(
				/* translators: %d HTTP status code */
				__( 'API request failed (HTTP %d).', 'tca' ),
				(int) $code
			)
		);
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( ! is_array( $data ) ) {
		return new WP_Error( 'tca_football_json', __( 'Invalid API response.', 'tca' ) );
	}

	return $data;
}

/**
 * Get fixtures with a transient cache (works without Redis; persists between page loads).
 *
 * @param int $league_id League ID.
 * @param int $season    Season year.
 * @return array|WP_Error Same as tca_api_football_fetch_fixtures().
 */
function tca_api_football_get_fixtures_cached( $league_id, $season ) {
	$league_id = absint( $league_id );
	$season    = absint( $season );
	$cache_key = 'tca_fb_fx_' . $league_id . '_' . $season;
	/**
	 * Cache TTL in seconds (0 = no cache, always call the API).
	 * Default is one day so you typically get at most one request per league+season per 24 hours.
	 *
	 * @param int $seconds   Default `DAY_IN_SECONDS` (86400).
	 * @param int $league_id
	 * @param int $season
	 */
	$ttl = (int) apply_filters( 'tca_api_football_fixtures_cache_seconds', (int) DAY_IN_SECONDS, $league_id, $season );
	if ( $ttl > 0 ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) && isset( $cached['response'] ) ) {
			return $cached;
		}
	}
	$data = tca_api_football_fetch_fixtures( $league_id, $season );
	// Do not cache API errors.
	if ( $ttl > 0 && ! is_wp_error( $data ) && is_array( $data ) ) {
		set_transient( $cache_key, $data, $ttl );
	}
	return $data;
}

/**
 * Clear cached fixtures and teams index for a league+season (e.g. to force fresh API data).
 *
 * @param int $league_id League ID.
 * @param int $season    Season year.
 * @return bool Whether at least one transient was removed.
 */
function tca_api_football_delete_fixtures_cache( $league_id, $season ) {
	$lid = absint( $league_id );
	$sid = absint( $season );
	$fx  = delete_transient( 'tca_fb_fx_' . $lid . '_' . $sid );
	$tm  = delete_transient( 'tca_fb_teams_' . $lid . '_' . $sid );
	return (bool) ( $fx || $tm );
}

/**
 * Fetch all teams in a league season (handles paging) as id => meta.
 *
 * @param int $league_id League ID.
 * @param int $season    Season year.
 * @return array<int, array{logo: string, national: bool, country: string}>|WP_Error
 */
function tca_api_football_fetch_teams_index( $league_id, $season ) {
	$league_id = absint( $league_id );
	$season    = absint( $season );
	if ( ! $league_id || ! $season ) {
		return new WP_Error( 'tca_football_bad_params', __( 'Invalid league or season.', 'tca' ) );
	}

	$api_key = tca_api_football_get_key();
	if ( '' === $api_key ) {
		return new WP_Error( 'tca_football_no_key', __( 'API Football key is not configured.', 'tca' ) );
	}

	$by_id  = array();
	$page   = 1;
	$total_pages = 1;

	while ( $page <= $total_pages && $page <= 50 ) {
		$url = add_query_arg(
			array(
				'league' => $league_id,
				'season' => $season,
				'page'   => $page,
			),
			'https://v3.football.api-sports.io/teams'
		);

		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 20,
				'headers' => array(
					'x-apisports-key' => $api_key,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			return new WP_Error(
				'tca_football_http',
				sprintf(
					/* translators: %d: HTTP status code */
					__( 'Teams request failed (HTTP %d).', 'tca' ),
					(int) $code
				)
			);
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );
		if ( ! is_array( $data ) ) {
			return new WP_Error( 'tca_football_json', __( 'Invalid API response.', 'tca' ) );
		}

		$paging = isset( $data['paging'] ) && is_array( $data['paging'] ) ? $data['paging'] : array();
		$total_pages = isset( $paging['total'] ) ? max( 1, (int) $paging['total'] ) : 1;

		$rows = isset( $data['response'] ) && is_array( $data['response'] ) ? $data['response'] : array();
		foreach ( $rows as $row ) {
			$team = isset( $row['team'] ) && is_array( $row['team'] ) ? $row['team'] : array();
			$tid  = isset( $team['id'] ) ? (int) $team['id'] : 0;
			if ( $tid <= 0 ) {
				continue;
			}
			$by_id[ $tid ] = array(
				'logo'     => isset( $team['logo'] ) ? (string) $team['logo'] : '',
				'national' => ! empty( $team['national'] ),
				'country'  => isset( $team['country'] ) ? (string) $team['country'] : '',
			);
		}

		++$page;
	}

	return $by_id;
}

/**
 * Cached teams index for flag resolution (same TTL as fixtures by default).
 *
 * @param int $league_id League ID.
 * @param int $season    Season year.
 * @return array<int, array{logo: string, national: bool, country: string}>
 */
function tca_api_football_get_teams_index_cached( $league_id, $season ) {
	$league_id = absint( $league_id );
	$season    = absint( $season );
	$cache_key = 'tca_fb_teams_' . $league_id . '_' . $season;
	$ttl       = (int) apply_filters( 'tca_api_football_fixtures_cache_seconds', (int) DAY_IN_SECONDS, $league_id, $season );
	$ttl       = (int) apply_filters( 'tca_api_football_teams_cache_seconds', $ttl, $league_id, $season );

	if ( $ttl > 0 ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$data = tca_api_football_fetch_teams_index( $league_id, $season );
	if ( is_wp_error( $data ) || ! is_array( $data ) ) {
		return array();
	}
	if ( $ttl > 0 ) {
		set_transient( $cache_key, $data, $ttl );
	}
	return $data;
}

/**
 * Fetch countries list from API-Football (includes flag image URLs).
 *
 * @return array<int, array{name: string, code: string, flag: string}>|WP_Error
 */
function tca_api_football_fetch_countries() {
	$key = tca_api_football_get_key();
	if ( '' === $key ) {
		return new WP_Error( 'tca_football_no_key', __( 'API Football key is not configured.', 'tca' ) );
	}

	$response = wp_remote_get(
		'https://v3.football.api-sports.io/countries',
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
			'tca_football_http',
			sprintf(
				__( 'Countries request failed (HTTP %d).', 'tca' ),
				(int) $code
			)
		);
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );
	if ( ! is_array( $data ) || ! isset( $data['response'] ) || ! is_array( $data['response'] ) ) {
		return new WP_Error( 'tca_football_json', __( 'Invalid API response.', 'tca' ) );
	}

	return $data['response'];
}

/**
 * Map country name (as returned on national teams) → flag URL. Cached a long time.
 *
 * @return array<string, string>
 */
function tca_api_football_get_country_flag_url_map_cached() {
	$cache_key = 'tca_fb_country_flags';
	/**
	 * TTL for the full countries list (names + flag URLs).
	 *
	 * @param int $seconds Default 30 days.
	 */
	$ttl = (int) apply_filters( 'tca_api_football_countries_cache_seconds', 30 * (int) DAY_IN_SECONDS );
	if ( $ttl > 0 ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$list = tca_api_football_fetch_countries();
	if ( is_wp_error( $list ) || ! is_array( $list ) ) {
		return array();
	}

	$map = array();
	foreach ( $list as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$name = isset( $row['name'] ) ? (string) $row['name'] : '';
		$flag = isset( $row['flag'] ) ? (string) $row['flag'] : '';
		if ( '' !== $name && '' !== $flag ) {
			$map[ $name ] = $flag;
		}
	}

	if ( $ttl > 0 && ! empty( $map ) ) {
		set_transient( $cache_key, $map, $ttl );
	}

	return $map;
}

/**
 * Image URL for a team row in a fixture (logo, or country flag for national squads when requested).
 *
 * @param int                  $team_id        API team id.
 * @param array<string, mixed> $fixture_team   teams.home or teams.away from fixture.
 * @param array<int, array>    $teams_index    From tca_api_football_get_teams_index_cached().
 * @param array<string, string> $flag_urls     Country name => flag URL.
 * @param string               $icon_mode      logos | logos_small | flags | flags_small.
 * @return string URL (empty if none).
 */
function tca_api_football_team_display_image_url( $team_id, array $fixture_team, array $teams_index, array $flag_urls, $icon_mode ) {
	$team_id   = (int) $team_id;
	$icon_mode = is_string( $icon_mode ) ? $icon_mode : 'logos_small';
	$logo      = isset( $fixture_team['logo'] ) ? (string) $fixture_team['logo'] : '';

	$use_flags = in_array( $icon_mode, array( 'flags', 'flags_small' ), true );
	if ( ! $use_flags || $team_id <= 0 || empty( $teams_index[ $team_id ] ) || '' === $logo ) {
		return $logo;
	}

	$meta = $teams_index[ $team_id ];
	if ( empty( $meta['national'] ) ) {
		return $logo;
	}

	$country = isset( $meta['country'] ) ? trim( (string) $meta['country'] ) : '';
	if ( '' === $country || empty( $flag_urls[ $country ] ) ) {
		return $logo;
	}

	return (string) $flag_urls[ $country ];
}

/**
 * Timezone for displaying fixture times (IANA, e.g. America/New_York).
 *
 * @return string
 */
function tca_api_football_display_timezone() {
	/**
	 * Filter the timezone used when formatting fixture date/time in theme output.
	 *
	 * @param string $tz Default America/New_York.
	 */
	return (string) apply_filters( 'tca_api_football_display_timezone', 'America/New_York' );
}

/**
 * Format one fixture's date and time for display.
 *
 * @param string $iso_datetime UTC datetime from API.
 * @param string $date_order  `day_month` (numeric D/M) or `month_day` (US: month name + day, e.g. Nov 6, 2026).
 * @param string $tz_iana     IANA timezone.
 * @return string[]{ date: string, time: string }
 */
function tca_api_football_format_fixture_display_parts( $iso_datetime, $date_order, $tz_iana ) {
	$out = array(
		'date'       => '',
		'time'       => '',
		'timezone'   => '',
		'time_label' => '',
	);
	if ( ! is_string( $iso_datetime ) || '' === trim( $iso_datetime ) ) {
		return $out;
	}
	try {
		$utc = new DateTimeZone( 'UTC' );
		$dt  = new DateTimeImmutable( $iso_datetime, $utc );
	} catch ( Exception $e ) {
		return $out;
	}
	try {
		$local = $dt->setTimezone( new DateTimeZone( $tz_iana ) );
	} catch ( Exception $e2 ) {
		$local = $dt;
	}
	$y = $local->format( 'Y' );
	$m = $local->format( 'm' );
	$d = $local->format( 'd' );
	if ( 'day_month' === $date_order ) {
		// Day-first, unambiguous (6 Nov 2026) — not 11/06/2026 which looks like two different dates.
		$out['date'] = $local->format( 'j M Y' );
	} else {
		// US-style: month name, then day (e.g. Nov 6, 2026).
		$out['date'] = $local->format( 'M j, Y' );
	}
	/**
	 * After building default date string; filter receives formatted date and can replace entirely.
	 *
	 * @param string             $out_date Current date line.
	 * @param string             $date_order `day_month` or `month_day`.
	 * @param DateTimeImmutable  $local      Local time.
	 */
	$out['date'] = (string) apply_filters( 'tca_api_football_format_date_string', $out['date'], $date_order, $local );
	// 12-hour clock with AM/PM (localized English letters from PHP date()).
	$out['time']      = $local->format( 'g:i A' );
	$out['timezone']  = tca_api_football_format_timezone_label( $tz_iana, $local );
	$out['time_label'] = trim( $out['time'] . ( '' !== $out['timezone'] ? ' ' . $out['timezone'] : '' ) );
	return $out;
}

/**
 * Local calendar date (Y-m-d) for a fixture in the display timezone.
 *
 * @param string $iso_datetime API fixture date (UTC ISO).
 * @param string $tz_iana      IANA timezone.
 * @return string
 */
function tca_api_football_fixture_local_date_key( $iso_datetime, $tz_iana ) {
	if ( ! is_string( $iso_datetime ) || '' === trim( $iso_datetime ) ) {
		return '';
	}
	try {
		$dt = new DateTimeImmutable( $iso_datetime, new DateTimeZone( 'UTC' ) );
	} catch ( Exception $e ) {
		return '';
	}
	try {
		$local = $dt->setTimezone( new DateTimeZone( $tz_iana ) );
	} catch ( Exception $e2 ) {
		$local = $dt;
	}
	return $local->format( 'Y-m-d' );
}

/**
 * Short timezone label for display next to kickoff times (e.g. EDT, EST).
 *
 * @param string              $tz_iana IANA timezone.
 * @param DateTimeImmutable   $local   Local kickoff time (for DST-aware abbreviation).
 * @return string
 */
function tca_api_football_format_timezone_label( $tz_iana, DateTimeImmutable $local ) {
	if ( ! is_string( $tz_iana ) || '' === trim( $tz_iana ) ) {
		return '';
	}
	try {
		$tz = new DateTimeZone( $tz_iana );
	} catch ( Exception $e ) {
		return '';
	}

	$label = $local->setTimezone( $tz )->format( 'T' );

	/**
	 * Filter short timezone label shown beside fixture times.
	 *
	 * @param string            $label   Default PHP `T` format (e.g. EDT).
	 * @param string            $tz_iana IANA timezone.
	 * @param DateTimeImmutable $local   Local kickoff time.
	 */
	return (string) apply_filters( 'tca_api_football_format_timezone_label', $label, $tz_iana, $local );
}

/**
 * Format fixture venue/location from API-Football fixture node.
 *
 * @param array<string, mixed> $fixture Fixture node from API response row.
 * @return string[]{ venue: string, city: string, label: string }
 */
function tca_api_football_format_fixture_location( array $fixture ) {
	$venue = isset( $fixture['venue'] ) && is_array( $fixture['venue'] ) ? $fixture['venue'] : array();
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

	/**
	 * Filter formatted fixture location label.
	 *
	 * @param string               $label   Display string.
	 * @param string               $name    Venue name.
	 * @param string               $city    Venue city.
	 * @param array<string, mixed> $fixture Full fixture node.
	 */
	$label = (string) apply_filters( 'tca_api_football_format_location_label', $label, $name, $city, $fixture );

	return array(
		'venue' => $name,
		'city'  => $city,
		'label' => $label,
	);
}

/**
 * AJAX: return fixtures JSON for the Football Fixtures block.
 */
function tca_ajax_api_football_fixtures() {
	check_ajax_referer( 'tca_football_fixtures', 'nonce' );

	$league = isset( $_POST['league'] ) ? absint( wp_unslash( $_POST['league'] ) ) : 0;
	$season = isset( $_POST['season'] ) ? absint( wp_unslash( $_POST['season'] ) ) : 0;

	if ( ! $league || ! $season ) {
		wp_send_json_error(
			array( 'message' => __( 'Invalid league or season.', 'tca' ) ),
			400
		);
	}

	$result = tca_api_football_fetch_fixtures( $league, $season );

	if ( is_wp_error( $result ) ) {
		wp_send_json_error(
			array( 'message' => $result->get_error_message() ),
			502
		);
	}

	wp_send_json_success( $result );
}

/**
 * API-Football league ID for FIFA World Cup.
 *
 * @return int
 */
function tca_api_football_world_cup_league_id() {
	/**
	 * Filter the World Cup league ID used by theme football blocks.
	 *
	 * @param int $league_id Default 1 (FIFA World Cup in API-Football).
	 */
	return (int) apply_filters( 'tca_api_football_world_cup_league_id', 1 );
}

/**
 * Default season year for World Cup live scores.
 *
 * @return int
 */
function tca_api_football_world_cup_season_default() {
	/**
	 * Filter default World Cup season year.
	 *
	 * @param int $season Default 2026.
	 */
	return (int) apply_filters( 'tca_api_football_world_cup_season_default', 2026 );
}

add_action( 'wp_ajax_tca_api_football_fixtures', 'tca_ajax_api_football_fixtures' );
add_action( 'wp_ajax_nopriv_tca_api_football_fixtures', 'tca_ajax_api_football_fixtures' );

/**
 * Status codes treated as in-play for live scores.
 *
 * @return string[]
 */
function tca_api_football_in_play_status_codes() {
	$codes = array( '1H', '2H', 'HT', 'ET', 'BT', 'P', 'LIVE', 'INT' );
	/**
	 * Filter in-play fixture status short codes from API-Football.
	 *
	 * @param string[] $codes Default in-play codes.
	 */
	return (array) apply_filters( 'tca_api_football_in_play_status_codes', $codes );
}

/**
 * Whether a fixture status is in-play.
 *
 * @param array<string, mixed> $fixture Fixture node from API response row.
 * @return bool
 */
function tca_api_football_fixture_is_in_play( array $fixture ) {
	$status = isset( $fixture['status'] ) && is_array( $fixture['status'] ) ? $fixture['status'] : array();
	$short  = isset( $status['short'] ) ? (string) $status['short'] : '';
	return in_array( $short, tca_api_football_in_play_status_codes(), true );
}

/**
 * Human-readable live status for the time column (minute, HT, etc.).
 *
 * @param array<string, mixed> $fixture Fixture node from API response row.
 * @return string
 */
function tca_api_football_get_fixture_live_status_label( array $fixture ) {
	$status  = isset( $fixture['status'] ) && is_array( $fixture['status'] ) ? $fixture['status'] : array();
	$short   = isset( $status['short'] ) ? (string) $status['short'] : '';
	$elapsed = isset( $status['elapsed'] ) && is_numeric( $status['elapsed'] ) ? (int) $status['elapsed'] : 0;

	if ( is_numeric( $elapsed ) && $elapsed > 0 && in_array( $short, array( '1H', '2H', 'ET', 'P', 'LIVE' ), true ) ) {
		return $elapsed . "'";
	}
	if ( 'HT' === $short ) {
		return 'HT';
	}
	if ( 'BT' === $short ) {
		return 'Break';
	}
	if ( '' !== $short ) {
		return $short;
	}
	return '';
}

/**
 * Fetch live fixtures (all leagues or scoped to one league + season).
 *
 * @param int $league_id Optional league ID (0 = all live fixtures).
 * @param int $season    Season year when league is set.
 * @return array|WP_Error
 */
function tca_api_football_fetch_live_fixtures( $league_id = 0, $season = 0 ) {
	$league_id = absint( $league_id );
	$season    = absint( $season );

	$key = tca_api_football_get_key();
	if ( '' === $key ) {
		return new WP_Error( 'tca_football_no_key', __( 'API Football key is not configured.', 'tca' ) );
	}

	$query = array( 'live' => 'all' );
	if ( $league_id > 0 ) {
		$query['league'] = $league_id;
		if ( $season > 0 ) {
			$query['season'] = $season;
		}
	}

	$url = add_query_arg( $query, 'https://v3.football.api-sports.io/fixtures' );

	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 20,
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
			'tca_football_http',
			sprintf(
				/* translators: %d HTTP status code */
				__( 'Live scores request failed (HTTP %d).', 'tca' ),
				(int) $code
			)
		);
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( ! is_array( $data ) ) {
		return new WP_Error( 'tca_football_json', __( 'Invalid API response.', 'tca' ) );
	}

	return $data;
}

/**
 * Live fixtures with a short transient cache.
 *
 * @param int $league_id Optional league ID (0 = all).
 * @param int $season    Season year when league is set.
 * @return array|WP_Error
 */
function tca_api_football_get_live_fixtures_cached( $league_id = 0, $season = 0 ) {
	$league_id = absint( $league_id );
	$season    = absint( $season );
	$cache_key = $league_id > 0 ? 'tca_fb_live_' . $league_id . '_' . $season : 'tca_fb_live_all';
	/**
	 * Cache TTL in seconds for live fixture data.
	 *
	 * @param int $seconds   Default 45.
	 * @param int $league_id
	 * @param int $season
	 */
	$ttl = (int) apply_filters( 'tca_api_football_live_cache_seconds', 45, $league_id, $season );
	if ( $ttl > 0 ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) && isset( $cached['response'] ) ) {
			return $cached;
		}
	}

	$data = tca_api_football_fetch_live_fixtures( $league_id, $season );
	if ( $ttl > 0 && ! is_wp_error( $data ) && is_array( $data ) ) {
		set_transient( $cache_key, $data, $ttl );
	}
	return $data;
}

/**
 * Keep only in-play rows from a fixtures API response list.
 *
 * @param array<int, array<string, mixed>> $rows Response rows.
 * @return array<int, array<string, mixed>>
 */
function tca_api_football_filter_in_play_rows( array $rows ) {
	$filtered = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$fixture = isset( $row['fixture'] ) && is_array( $row['fixture'] ) ? $row['fixture'] : array();
		if ( tca_api_football_fixture_is_in_play( $fixture ) ) {
			$filtered[] = $row;
		}
	}
	return $filtered;
}

/**
 * Whether a fixture has not started yet.
 *
 * @param array<string, mixed> $fixture Fixture node.
 * @return bool
 */
function tca_api_football_fixture_is_upcoming( array $fixture ) {
	$status = isset( $fixture['status'] ) && is_array( $fixture['status'] ) ? $fixture['status'] : array();
	$short  = isset( $status['short'] ) ? (string) $status['short'] : '';
	return in_array( $short, array( 'NS', 'TBD' ), true );
}

/**
 * Next not-started fixture for a league season (uses cached season fixtures).
 *
 * @param int   $league_id            League ID.
 * @param int   $season                Season year.
 * @param int[] $exclude_fixture_ids Fixture IDs to skip (e.g. currently live).
 * @return array<string, mixed>|null Single API response row or null.
 */
function tca_api_football_get_next_upcoming_fixture( $league_id, $season, array $exclude_fixture_ids = array() ) {
	$league_id = absint( $league_id );
	$season    = absint( $season );
	if ( ! $league_id || ! $season || ! function_exists( 'tca_api_football_get_fixtures_cached' ) ) {
		return null;
	}

	$exclude = array();
	foreach ( $exclude_fixture_ids as $fid ) {
		$fid = absint( $fid );
		if ( $fid > 0 ) {
			$exclude[ $fid ] = true;
		}
	}

	$api = tca_api_football_get_fixtures_cached( $league_id, $season );
	if ( is_wp_error( $api ) || empty( $api['response'] ) || ! is_array( $api['response'] ) ) {
		return null;
	}

	$now_utc = gmdate( 'Y-m-d H:i:s' );
	$candidates = array();

	foreach ( $api['response'] as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$fixture = isset( $row['fixture'] ) && is_array( $row['fixture'] ) ? $row['fixture'] : array();
		$fid     = isset( $fixture['id'] ) ? (int) $fixture['id'] : 0;
		if ( $fid > 0 && isset( $exclude[ $fid ] ) ) {
			continue;
		}
		if ( ! tca_api_football_fixture_is_upcoming( $fixture ) ) {
			continue;
		}
		$dateiso = isset( $fixture['date'] ) ? (string) $fixture['date'] : '';
		if ( '' === $dateiso ) {
			continue;
		}
		try {
			$kickoff = new DateTimeImmutable( $dateiso, new DateTimeZone( 'UTC' ) );
		} catch ( Exception $e ) {
			continue;
		}
		if ( $kickoff->format( 'Y-m-d H:i:s' ) < $now_utc ) {
			continue;
		}
		$candidates[] = $row;
	}

	if ( empty( $candidates ) ) {
		return null;
	}

	usort(
		$candidates,
		static function ( $a, $b ) {
			$da = isset( $a['fixture']['date'] ) ? (string) $a['fixture']['date'] : '';
			$db = isset( $b['fixture']['date'] ) ? (string) $b['fixture']['date'] : '';
			return strcmp( $da, $db );
		}
	);

	return $candidates[0];
}

/**
 * Head-to-head summary between two national teams (cached).
 *
 * @param int $team_a API team ID.
 * @param int $team_b API team ID.
 * @return array{total: int, team_a_wins: int, team_b_wins: int, draws: int}|null
 */
function tca_api_football_get_head_to_head_summary_cached( $team_a, $team_b ) {
	$team_a = absint( $team_a );
	$team_b = absint( $team_b );
	if ( $team_a <= 0 || $team_b <= 0 || $team_a === $team_b ) {
		return null;
	}

	$ids = array( $team_a, $team_b );
	sort( $ids, SORT_NUMERIC );
	$cache_key = 'tca_fb_h2h_' . $ids[0] . '_' . $ids[1];
	$ttl       = (int) apply_filters( 'tca_api_football_h2h_cache_seconds', (int) DAY_IN_SECONDS, $team_a, $team_b );

	if ( $ttl > 0 ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) && isset( $cached['total'] ) ) {
			return $cached;
		}
	}

	$key = tca_api_football_get_key();
	if ( '' === $key ) {
		return null;
	}

	$url = add_query_arg(
		array( 'h2h' => $ids[0] . '-' . $ids[1] ),
		'https://v3.football.api-sports.io/fixtures/headtohead'
	);

	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 20,
			'headers' => array(
				'x-apisports-key' => $key,
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return null;
	}

	$code = wp_remote_retrieve_response_code( $response );
	if ( $code < 200 || $code >= 300 ) {
		return null;
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );
	if ( ! is_array( $data ) || empty( $data['response'] ) || ! is_array( $data['response'] ) ) {
		return null;
	}

	$summary = array(
		'total'       => 0,
		'team_a_wins' => 0,
		'team_b_wins' => 0,
		'draws'       => 0,
	);

	foreach ( $data['response'] as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$goals = isset( $row['goals'] ) && is_array( $row['goals'] ) ? $row['goals'] : array();
		$teams = isset( $row['teams'] ) && is_array( $row['teams'] ) ? $row['teams'] : array();
		$gh    = isset( $goals['home'] ) && is_numeric( $goals['home'] ) ? (int) $goals['home'] : null;
		$ga    = isset( $goals['away'] ) && is_numeric( $goals['away'] ) ? (int) $goals['away'] : null;
		$hid   = isset( $teams['home']['id'] ) ? (int) $teams['home']['id'] : 0;
		$aid   = isset( $teams['away']['id'] ) ? (int) $teams['away']['id'] : 0;

		if ( null === $gh || null === $ga ) {
			continue;
		}

		++$summary['total'];
		if ( $gh === $ga ) {
			++$summary['draws'];
			continue;
		}

		$winner_id = $gh > $ga ? $hid : $aid;
		if ( $winner_id === $team_a ) {
			++$summary['team_a_wins'];
		} elseif ( $winner_id === $team_b ) {
			++$summary['team_b_wins'];
		}
	}

	if ( $ttl > 0 && $summary['total'] > 0 ) {
		set_transient( $cache_key, $summary, $ttl );
	}

	return $summary['total'] > 0 ? $summary : null;
}

/**
 * Human-readable kickoff countdown / datetime line.
 *
 * @param string $iso_datetime UTC ISO datetime.
 * @param string $display_tz   IANA timezone.
 * @return string
 */
function tca_api_football_format_kickoff_countdown( $iso_datetime, $display_tz ) {
	if ( ! is_string( $iso_datetime ) || '' === trim( $iso_datetime ) ) {
		return '';
	}
	try {
		$kickoff = new DateTimeImmutable( $iso_datetime, new DateTimeZone( 'UTC' ) );
		$kickoff = $kickoff->setTimezone( new DateTimeZone( $display_tz ) );
		$now     = new DateTimeImmutable( 'now', new DateTimeZone( $display_tz ) );
	} catch ( Exception $e ) {
		return '';
	}

	if ( $kickoff <= $now ) {
		return '';
	}

	$diff   = $now->diff( $kickoff );
	$days   = (int) $diff->days;
	$hours  = (int) $diff->h;
	$mins   = (int) $diff->i;
	$time   = $kickoff->format( 'g:i A' );
	$pretty = $kickoff->format( 'l, F j' );

	if ( 0 === $days ) {
		if ( $hours < 1 ) {
			/* translators: %d: minutes until kickoff */
			return sprintf( _n( 'Kickoff in %d minute', 'Kickoff in %d minutes', max( 1, $mins ), 'tca' ), max( 1, $mins ) );
		}
		/* translators: 1: hours, 2: minutes */
		return sprintf( __( 'Kickoff in %1$d hours, %2$d minutes', 'tca' ), $hours, $mins );
	}
	if ( 1 === $days ) {
		/* translators: %s: local kickoff time */
		return sprintf( __( 'Kickoff tomorrow at %s', 'tca' ), $time );
	}
	/* translators: 1: day count, 2: formatted date, 3: local kickoff time */
	return sprintf( __( 'Kickoff in %1$d days (%2$s at %3$s)', 'tca' ), $days, $pretty, $time );
}

/**
 * Build interesting fact lines for an upcoming fixture preview.
 *
 * @param array<string, mixed> $match      API response row.
 * @param array<string, mixed> $config     icon_mode, league_id, season, display_tz.
 * @return string[]
 */
function tca_api_football_build_upcoming_fixture_facts( array $match, array $config ) {
	$facts      = array();
	$fix        = isset( $match['fixture'] ) && is_array( $match['fixture'] ) ? $match['fixture'] : array();
	$league     = isset( $match['league'] ) && is_array( $match['league'] ) ? $match['league'] : array();
	$teams      = isset( $match['teams'] ) && is_array( $match['teams'] ) ? $match['teams'] : array();
	$home       = isset( $teams['home'] ) && is_array( $teams['home'] ) ? $teams['home'] : array();
	$away       = isset( $teams['away'] ) && is_array( $teams['away'] ) ? $teams['away'] : array();
	$display_tz = isset( $config['display_tz'] ) ? (string) $config['display_tz'] : tca_api_football_display_timezone();

	$round = isset( $league['round'] ) ? trim( (string) $league['round'] ) : '';
	if ( '' !== $round ) {
		$facts[] = $round;
	}

	$location = tca_api_football_format_fixture_location( $fix );
	if ( ! empty( $location['label'] ) ) {
		/* translators: %s: venue and city */
		$facts[] = sprintf( __( 'Venue: %s', 'tca' ), $location['label'] );
	}

	$dateiso = isset( $fix['date'] ) ? (string) $fix['date'] : '';
	$countdown = tca_api_football_format_kickoff_countdown( $dateiso, $display_tz );
	if ( '' !== $countdown ) {
		$facts[] = $countdown;
	}

	$referee = isset( $fix['referee'] ) ? trim( (string) $fix['referee'] ) : '';
	if ( '' !== $referee ) {
		/* translators: %s: referee name */
		$facts[] = sprintf( __( 'Referee: %s', 'tca' ), $referee );
	}

	$hid   = isset( $home['id'] ) ? (int) $home['id'] : 0;
	$aid   = isset( $away['id'] ) ? (int) $away['id'] : 0;
	$hname = isset( $home['name'] ) ? (string) $home['name'] : '';
	$aname = isset( $away['name'] ) ? (string) $away['name'] : '';

	if ( $hid > 0 && $aid > 0 && '' !== $hname && '' !== $aname ) {
		$h2h = tca_api_football_get_head_to_head_summary_cached( $hid, $aid );
		if ( is_array( $h2h ) && $h2h['total'] > 0 ) {
			$facts[] = sprintf(
				/* translators: 1: total meetings, 2: home team, 3: home wins, 4: away team, 5: away wins, 6: draws */
				__( 'These teams have met %1$d times — %2$s %3$d, %4$s %5$d, %6$d draws', 'tca' ),
				(int) $h2h['total'],
				$hname,
				(int) $h2h['team_a_wins'],
				$aname,
				(int) $h2h['team_b_wins'],
				(int) $h2h['draws']
			);
		}
	}

	/**
	 * Filter fact lines shown on the upcoming-match preview when no live games are on.
	 *
	 * @param string[]             $facts  Fact lines.
	 * @param array<string, mixed> $match  API response row.
	 * @param array<string, mixed> $config Display config.
	 */
	return (array) apply_filters( 'tca_api_football_upcoming_fixture_facts', $facts, $match, $config );
}

/**
 * Render upcoming-match preview when nothing is live.
 *
 * @param array<string, mixed> $match  API response row.
 * @param array<string, mixed> $config Display config.
 * @param string              $variant `default` or `sidebar`.
 * @return string HTML.
 */
function tca_api_football_render_upcoming_fixture_preview_html( array $match, array $config, $variant = 'default' ) {
	$icon_mode  = isset( $config['icon_mode'] ) ? (string) $config['icon_mode'] : 'flags_small';
	$league_id  = isset( $config['league_id'] ) ? absint( $config['league_id'] ) : 0;
	$season     = isset( $config['season'] ) ? absint( $config['season'] ) : 0;
	$display_tz = isset( $config['display_tz'] ) ? (string) $config['display_tz'] : tca_api_football_display_timezone();

	$fix   = isset( $match['fixture'] ) && is_array( $match['fixture'] ) ? $match['fixture'] : array();
	$teams = isset( $match['teams'] ) && is_array( $match['teams'] ) ? $match['teams'] : array();
	$home  = isset( $teams['home'] ) && is_array( $teams['home'] ) ? $teams['home'] : array();
	$away  = isset( $teams['away'] ) && is_array( $teams['away'] ) ? $teams['away'] : array();

	$hname = isset( $home['name'] ) ? (string) $home['name'] : '';
	$aname = isset( $away['name'] ) ? (string) $away['name'] : '';
	$hid   = isset( $home['id'] ) ? (int) $home['id'] : 0;
	$aid   = isset( $away['id'] ) ? (int) $away['id'] : 0;

	$teams_index = array();
	$flag_urls   = array();
	if ( in_array( $icon_mode, array( 'flags', 'flags_small' ), true ) && $league_id > 0 && $season > 0 ) {
		$teams_index = tca_api_football_get_teams_index_cached( $league_id, $season );
		$flag_urls   = tca_api_football_get_country_flag_url_map_cached();
	}

	$h_src = tca_api_football_team_display_image_url( $hid, $home, $teams_index, $flag_urls, $icon_mode );
	$a_src = tca_api_football_team_display_image_url( $aid, $away, $teams_index, $flag_urls, $icon_mode );

	$icon_small = ( false !== strpos( $icon_mode, 'small' ) );
	$img_w      = $icon_small ? 32 : 48;
	$img_h      = $icon_small ? 22 : 48;
	if ( false !== strpos( $icon_mode, 'flags' ) ) {
		$img_w = $icon_small ? 40 : 56;
		$img_h = $icon_small ? 24 : 32;
	}

	$logo_wrap_class = 'tca-football-fixtures__logo-wrap';
	if ( false !== strpos( $icon_mode, 'flags' ) ) {
		$logo_wrap_class .= ' tca-football-fixtures__logo-wrap--flag';
	}

	$dateiso = isset( $fix['date'] ) ? (string) $fix['date'] : '';
	$parts   = tca_api_football_format_fixture_display_parts( $dateiso, 'month_day', $display_tz );
	$facts = tca_api_football_build_upcoming_fixture_facts( $match, $config );

	ob_start();
	?>
	<div class="tca-football-live-upcoming<?php echo 'sidebar' === $variant ? ' tca-football-live-upcoming--sidebar' : ''; ?>">
		<h3 class="tca-football-live-upcoming__heading"><?php echo esc_html__( 'Next match', 'tca' ); ?></h3>
		<div class="tca-football-live-upcoming__match">
			<div class="tca-football-live-upcoming__meta">
				<?php if ( ! empty( $parts['date'] ) ) : ?>
					<span class="tca-football-live-upcoming__date"><?php echo esc_html( $parts['date'] ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $parts['time_label'] ) ) : ?>
					<span class="tca-football-live-upcoming__time"><?php echo esc_html( $parts['time_label'] ); ?></span>
				<?php elseif ( ! empty( $parts['time'] ) ) : ?>
					<span class="tca-football-live-upcoming__time"><?php echo esc_html( $parts['time'] ); ?></span>
				<?php endif; ?>
			</div>
			<div class="tca-football-live-upcoming__showdown">
				<div class="tca-football-live-upcoming__flags">
					<div class="tca-football-live-upcoming__team tca-football-live-upcoming__team--home">
						<?php if ( '' !== $h_src ) : ?>
							<span class="<?php echo esc_attr( $logo_wrap_class ); ?>">
								<img class="tca-football-fixtures__logo" src="<?php echo esc_url( $h_src ); ?>" alt="" width="<?php echo (int) $img_w; ?>" height="<?php echo (int) $img_h; ?>" loading="lazy" decoding="async" />
							</span>
						<?php endif; ?>
					</div>
					<span class="tca-football-live-upcoming__vs"><?php echo esc_html_x( 'vs', 'versus short', 'tca' ); ?></span>
					<div class="tca-football-live-upcoming__team tca-football-live-upcoming__team--away">
						<?php if ( '' !== $a_src ) : ?>
							<span class="<?php echo esc_attr( $logo_wrap_class ); ?>">
								<img class="tca-football-fixtures__logo" src="<?php echo esc_url( $a_src ); ?>" alt="" width="<?php echo (int) $img_w; ?>" height="<?php echo (int) $img_h; ?>" loading="lazy" decoding="async" />
							</span>
						<?php endif; ?>
					</div>
				</div>
				<div class="tca-football-live-upcoming__names">
					<span class="tca-football-live-upcoming__team-name tca-football-live-upcoming__team-name--home"><?php echo esc_html( $hname ); ?></span>
					<span class="tca-football-live-upcoming__team-name tca-football-live-upcoming__team-name--away"><?php echo esc_html( $aname ); ?></span>
				</div>
			</div>
		</div>
		<?php if ( ! empty( $facts ) ) : ?>
			<ul class="tca-football-live-upcoming__facts">
				<?php foreach ( $facts as $fact ) : ?>
					<li><?php echo esc_html( $fact ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Fact lines for a live match card.
 *
 * @param array<string, mixed> $match  API response row.
 * @param array<string, mixed> $config Display config.
 * @return string[]
 */
function tca_api_football_build_live_fixture_facts( array $match, array $config ) {
	$facts  = array();
	$fix    = isset( $match['fixture'] ) && is_array( $match['fixture'] ) ? $match['fixture'] : array();
	$league = isset( $match['league'] ) && is_array( $match['league'] ) ? $match['league'] : array();
	$round  = isset( $league['round'] ) ? trim( (string) $league['round'] ) : '';

	if ( '' !== $round ) {
		$facts[] = $round;
	}

	$location = tca_api_football_format_fixture_location( $fix );
	if ( ! empty( $location['label'] ) ) {
		$facts[] = sprintf( __( 'Venue: %s', 'tca' ), $location['label'] );
	}

	$referee = isset( $fix['referee'] ) ? trim( (string) $fix['referee'] ) : '';
	if ( '' !== $referee ) {
		$facts[] = sprintf( __( 'Referee: %s', 'tca' ), $referee );
	}

	return (array) apply_filters( 'tca_api_football_live_fixture_facts', $facts, $match, $config );
}

/**
 * Render one featured live match card for the widget.
 *
 * @param array<string, mixed> $match  API response row.
 * @param array<string, mixed> $config Display config.
 * @return string HTML.
 */
function tca_api_football_render_live_featured_match_html( array $match, array $config ) {
	$icon_mode = isset( $config['icon_mode'] ) ? (string) $config['icon_mode'] : 'flags_small';
	$league_id = isset( $config['league_id'] ) ? absint( $config['league_id'] ) : 0;
	$season    = isset( $config['season'] ) ? absint( $config['season'] ) : 0;

	$fix   = isset( $match['fixture'] ) && is_array( $match['fixture'] ) ? $match['fixture'] : array();
	$teams = isset( $match['teams'] ) && is_array( $match['teams'] ) ? $match['teams'] : array();
	$goals = isset( $match['goals'] ) && is_array( $match['goals'] ) ? $match['goals'] : array();
	$home  = isset( $teams['home'] ) && is_array( $teams['home'] ) ? $teams['home'] : array();
	$away  = isset( $teams['away'] ) && is_array( $teams['away'] ) ? $teams['away'] : array();

	$hname = isset( $home['name'] ) ? (string) $home['name'] : '';
	$aname = isset( $away['name'] ) ? (string) $away['name'] : '';
	$hid   = isset( $home['id'] ) ? (int) $home['id'] : 0;
	$aid   = isset( $away['id'] ) ? (int) $away['id'] : 0;
	$gh    = isset( $goals['home'] ) && is_numeric( $goals['home'] ) ? (string) (int) $goals['home'] : '0';
	$ga    = isset( $goals['away'] ) && is_numeric( $goals['away'] ) ? (string) (int) $goals['away'] : '0';

	$teams_index = array();
	$flag_urls   = array();
	if ( in_array( $icon_mode, array( 'flags', 'flags_small' ), true ) && $league_id > 0 && $season > 0 ) {
		$teams_index = tca_api_football_get_teams_index_cached( $league_id, $season );
		$flag_urls   = tca_api_football_get_country_flag_url_map_cached();
	}

	$h_src = tca_api_football_team_display_image_url( $hid, $home, $teams_index, $flag_urls, $icon_mode );
	$a_src = tca_api_football_team_display_image_url( $aid, $away, $teams_index, $flag_urls, $icon_mode );

	$logo_wrap_class = 'tca-football-fixtures__logo-wrap';
	if ( false !== strpos( $icon_mode, 'flags' ) ) {
		$logo_wrap_class .= ' tca-football-fixtures__logo-wrap--flag';
	}

	$live_label  = tca_api_football_get_fixture_live_status_label( $fix );
	$facts       = tca_api_football_build_live_fixture_facts( $match, $config );
	$fixture_id = isset( $fix['id'] ) ? (int) $fix['id'] : 0;

	ob_start();
	?>
	<article class="tca-football-live-featured"<?php echo $fixture_id > 0 ? ' data-fixture-id="' . esc_attr( (string) $fixture_id ) . '"' : ''; ?>>
		<div class="tca-football-live-featured__meta">
			<span class="tca-football-fixtures__live-badge"><?php esc_html_e( 'Live', 'tca' ); ?></span>
			<?php if ( $live_label ) : ?>
				<span class="tca-football-fixtures__clock tca-football-fixtures__clock--live"><?php echo esc_html( $live_label ); ?></span>
			<?php endif; ?>
		</div>
		<div class="tca-football-live-upcoming__showdown">
			<div class="tca-football-live-upcoming__flags">
				<div class="tca-football-live-upcoming__team tca-football-live-upcoming__team--home">
					<?php if ( '' !== $h_src ) : ?>
						<span class="<?php echo esc_attr( $logo_wrap_class ); ?>">
							<img class="tca-football-fixtures__logo" src="<?php echo esc_url( $h_src ); ?>" alt="" loading="lazy" decoding="async" />
						</span>
					<?php endif; ?>
				</div>
				<span class="tca-football-live-featured__score" data-score-block>
					<span class="tca-football-live-featured__score-num" data-score-home><?php echo esc_html( $gh ); ?></span>
					<span class="tca-football-live-featured__score-sep" aria-hidden="true">-</span>
					<span class="tca-football-live-featured__score-num" data-score-away><?php echo esc_html( $ga ); ?></span>
				</span>
				<div class="tca-football-live-upcoming__team tca-football-live-upcoming__team--away">
					<?php if ( '' !== $a_src ) : ?>
						<span class="<?php echo esc_attr( $logo_wrap_class ); ?>">
							<img class="tca-football-fixtures__logo" src="<?php echo esc_url( $a_src ); ?>" alt="" loading="lazy" decoding="async" />
						</span>
					<?php endif; ?>
				</div>
			</div>
			<div class="tca-football-live-upcoming__names">
				<span class="tca-football-live-upcoming__team-name tca-football-live-upcoming__team-name--home"><?php echo esc_html( $hname ); ?></span>
				<span class="tca-football-live-upcoming__team-name tca-football-live-upcoming__team-name--away"><?php echo esc_html( $aname ); ?></span>
			</div>
		</div>
		<?php if ( ! empty( $facts ) ) : ?>
			<ul class="tca-football-live-upcoming__facts">
				<?php foreach ( $facts as $fact ) : ?>
					<li><?php echo esc_html( $fact ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</article>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render all featured live match cards.
 *
 * @param array<int, array<string, mixed>> $rows   Live API rows.
 * @param array<string, mixed>             $config Display config.
 * @return string HTML.
 */
function tca_api_football_render_live_featured_html( array $rows, array $config ) {
	if ( empty( $rows ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="tca-football-live-featured-panel">
		<h3 class="tca-football-live-featured-panel__heading"><?php esc_html_e( 'Live now', 'tca' ); ?></h3>
		<div class="tca-football-live-featured-list">
	<?php
	foreach ( $rows as $match ) {
		if ( ! is_array( $match ) ) {
			continue;
		}
		echo tca_api_football_render_live_featured_match_html( $match, $config );
	}
	?>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render live scores widget body (live panel + optional next-match sidebar).
 *
 * @param array{count: int, layout: string, live_html: string, upcoming_html: string} $state
 * @return string HTML.
 */
function tca_api_football_render_live_scores_body_html( array $state ) {
	$layout        = isset( $state['layout'] ) ? (string) $state['layout'] : 'upcoming_only';
	$live_html     = isset( $state['live_html'] ) ? (string) $state['live_html'] : '';
	$upcoming_html = isset( $state['upcoming_html'] ) ? (string) $state['upcoming_html'] : '';
	$live_count    = isset( $state['count'] ) ? (int) $state['count'] : 0;

	$layout_class = 'tca-football-live-scores-layout';
	if ( 'split' === $layout ) {
		$layout_class .= ' tca-football-live-scores-layout--split';
	} elseif ( 'live_only' === $layout && $live_count > 0 ) {
		$layout_class .= ' tca-football-live-scores-layout--live-only';
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr( $layout_class ); ?>">
		<div class="tca-football-live-scores-layout__live" data-live-panel<?php echo $live_count === 0 ? ' hidden' : ''; ?>>
			<?php echo $live_html; ?>
		</div>
		<div class="tca-football-live-scores-layout__next" data-live-upcoming<?php echo '' === $upcoming_html ? ' hidden' : ''; ?>>
			<?php echo $upcoming_html; ?>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Build live scores widget state (live rows + optional upcoming preview).
 *
 * @param int    $league_id  League ID.
 * @param int    $season     Season year.
 * @param string $icon_mode  Icon mode.
 * @param string $display_tz IANA timezone.
 * @return array{count: int, layout: string, live_html: string, upcoming_html: string, body_html: string}|WP_Error
 */
function tca_api_football_get_live_scores_state( $league_id, $season, $icon_mode, $display_tz ) {
	$league_id = absint( $league_id );
	$season    = absint( $season );

	$api = tca_api_football_get_live_fixtures_cached( $league_id, $season );
	if ( is_wp_error( $api ) ) {
		return $api;
	}

	$rows = isset( $api['response'] ) && is_array( $api['response'] ) ? $api['response'] : array();
	$rows = tca_api_football_filter_in_play_rows( $rows );

	if ( ! empty( $rows ) ) {
		usort(
			$rows,
			static function ( $a, $b ) {
				$ea = isset( $a['fixture']['status']['elapsed'] ) ? (int) $a['fixture']['status']['elapsed'] : 0;
				$eb = isset( $b['fixture']['status']['elapsed'] ) ? (int) $b['fixture']['status']['elapsed'] : 0;
				return $eb <=> $ea;
			}
		);
	}

	$config = array(
		'mode'        => 'live',
		'icon_mode'   => $icon_mode,
		'league_id'   => $league_id,
		'season'      => $season,
		'display_tz'  => $display_tz,
		'show_league' => false,
	);

	$live_html    = '';
	$upcoming_html = '';
	$layout       = 'upcoming_only';
	$live_ids     = array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$fixture = isset( $row['fixture'] ) && is_array( $row['fixture'] ) ? $row['fixture'] : array();
		$fid     = isset( $fixture['id'] ) ? (int) $fixture['id'] : 0;
		if ( $fid > 0 ) {
			$live_ids[] = $fid;
		}
	}

	if ( ! empty( $rows ) ) {
		$live_html = tca_api_football_render_live_featured_html( $rows, $config );
		$next       = tca_api_football_get_next_upcoming_fixture( $league_id, $season, $live_ids );
		if ( is_array( $next ) ) {
			$upcoming_html = tca_api_football_render_upcoming_fixture_preview_html( $next, $config, 'sidebar' );
			$layout       = 'split';
		} else {
			$layout = 'live_only';
		}
	} else {
		$next = tca_api_football_get_next_upcoming_fixture( $league_id, $season );
		if ( is_array( $next ) ) {
			$upcoming_html = tca_api_football_render_upcoming_fixture_preview_html( $next, $config, 'default' );
		}
	}

	$state = array(
		'count'         => count( $rows ),
		'layout'        => $layout,
		'live_html'     => $live_html,
		'upcoming_html' => $upcoming_html,
	);

	$state['body_html'] = tca_api_football_render_live_scores_body_html( $state );

	return $state;
}

/**
 * Render fixture row markup for schedule or live blocks.
 *
 * @param array<int, array<string, mixed>> $rows   API response rows.
 * @param array<string, mixed>             $config Display options.
 * @return string HTML (no wrapper list).
 */
function tca_api_football_render_fixture_rows_html( array $rows, array $config ) {
	$mode         = isset( $config['mode'] ) && 'live' === $config['mode'] ? 'live' : 'schedule';
	$icon_mode    = isset( $config['icon_mode'] ) ? (string) $config['icon_mode'] : 'logos_small';
	$league_id    = isset( $config['league_id'] ) ? absint( $config['league_id'] ) : 0;
	$season       = isset( $config['season'] ) ? absint( $config['season'] ) : 0;
	$date_order   = isset( $config['date_order'] ) ? (string) $config['date_order'] : 'month_day';
	$display_tz   = isset( $config['display_tz'] ) ? (string) $config['display_tz'] : tca_api_football_display_timezone();
	$show_league  = ! empty( $config['show_league'] );

	$icon_allowed = array( 'logos', 'logos_small', 'flags', 'flags_small' );
	if ( ! in_array( $icon_mode, $icon_allowed, true ) ) {
		$icon_mode = 'logos_small';
	}

	$teams_index = array();
	$flag_urls   = array();
	if ( in_array( $icon_mode, array( 'flags', 'flags_small' ), true ) && $league_id > 0 && $season > 0 ) {
		$teams_index = tca_api_football_get_teams_index_cached( $league_id, $season );
		$flag_urls   = tca_api_football_get_country_flag_url_map_cached();
	}

	$icon_small = ( false !== strpos( $icon_mode, 'small' ) );
	$img_w      = $icon_small ? 24 : 40;
	$img_h      = $icon_small ? 24 : 40;
	if ( false !== strpos( $icon_mode, 'flags' ) ) {
		$img_w = $icon_small ? 32 : 44;
		$img_h = $icon_small ? 22 : 28;
	}

	ob_start();
	foreach ( $rows as $match ) {
		$fix   = isset( $match['fixture'] ) && is_array( $match['fixture'] ) ? $match['fixture'] : array();
		$teams = isset( $match['teams'] ) && is_array( $match['teams'] ) ? $match['teams'] : array();
		$goals = isset( $match['goals'] ) && is_array( $match['goals'] ) ? $match['goals'] : array();
		$league = isset( $match['league'] ) && is_array( $match['league'] ) ? $match['league'] : array();
		$home  = isset( $teams['home'] ) && is_array( $teams['home'] ) ? $teams['home'] : array();
		$away  = isset( $teams['away'] ) && is_array( $teams['away'] ) ? $teams['away'] : array();
		$hname = isset( $home['name'] ) ? (string) $home['name'] : '';
		$aname = isset( $away['name'] ) ? (string) $away['name'] : '';
		$hid   = isset( $home['id'] ) ? (int) $home['id'] : 0;
		$aid   = isset( $away['id'] ) ? (int) $away['id'] : 0;
		$h_src = tca_api_football_team_display_image_url( $hid, $home, $teams_index, $flag_urls, $icon_mode );
		$a_src = tca_api_football_team_display_image_url( $aid, $away, $teams_index, $flag_urls, $icon_mode );
		$hlogo = '' !== $h_src ? esc_url( $h_src ) : '';
		$alogo = '' !== $a_src ? esc_url( $a_src ) : '';

		$logo_wrap_class = 'tca-football-fixtures__logo-wrap';
		if ( false !== strpos( $icon_mode, 'flags' ) ) {
			$logo_wrap_class .= ' tca-football-fixtures__logo-wrap--flag';
		}

		$gh = isset( $goals['home'] ) && is_numeric( $goals['home'] ) ? (string) (int) $goals['home'] : null;
		$ga = isset( $goals['away'] ) && is_numeric( $goals['away'] ) ? (string) (int) $goals['away'] : null;

		$row_classes = 'tca-football-fixtures__row';
		if ( 'live' === $mode && tca_api_football_fixture_is_in_play( $fix ) ) {
			$row_classes .= ' tca-football-fixtures__row--live';
		}

		$league_name = isset( $league['name'] ) ? (string) $league['name'] : '';
		$round       = isset( $league['round'] ) ? (string) $league['round'] : '';
		$dateiso     = isset( $fix['date'] ) ? (string) $fix['date'] : '';
		$date_key    = tca_api_football_fixture_local_date_key( $dateiso, $display_tz );
		?>
		<article class="<?php echo esc_attr( $row_classes ); ?>" role="listitem"<?php echo isset( $fix['id'] ) ? ' data-fixture-id="' . esc_attr( (string) (int) $fix['id'] ) . '"' : ''; ?><?php echo '' !== $date_key ? ' data-fixture-date="' . esc_attr( $date_key ) . '"' : ''; ?>>
			<div class="tca-football-fixtures__row-time">
				<?php if ( 'live' === $mode ) : ?>
					<?php
					$live_label = tca_api_football_get_fixture_live_status_label( $fix );
					if ( $live_label ) :
						?>
						<span class="tca-football-fixtures__live-badge"><?php esc_html_e( 'Live', 'tca' ); ?></span>
						<span class="tca-football-fixtures__clock tca-football-fixtures__clock--live"><?php echo esc_html( $live_label ); ?></span>
					<?php endif; ?>
					<?php if ( $show_league && $league_name ) : ?>
						<span class="tca-football-fixtures__league"><?php echo esc_html( $league_name ); ?></span>
					<?php endif; ?>
					<?php if ( $show_league && $round ) : ?>
						<span class="tca-football-fixtures__round"><?php echo esc_html( $round ); ?></span>
					<?php endif; ?>
				<?php else : ?>
					<?php
					$parts = tca_api_football_format_fixture_display_parts( $dateiso, $date_order, $display_tz );
					if ( $parts['date'] ) :
						?>
						<span class="tca-football-fixtures__date"><?php echo esc_html( gmdate( 'F j', strtotime( $parts['date'] ) ) ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $parts['time_label'] ) ) : ?>
						<span class="tca-football-fixtures__clock"><?php echo esc_html( $parts['time_label'] ); ?></span>
					<?php elseif ( $parts['time'] ) : ?>
						<span class="tca-football-fixtures__clock"><?php echo esc_html( $parts['time'] ); ?></span>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<div class="tca-football-fixtures__row-main">
				<div class="tca-football-fixtures__side tca-football-fixtures__side--home">
					<?php if ( $hlogo ) : ?>
						<span class="<?php echo esc_attr( $logo_wrap_class ); ?>">
							<img class="tca-football-fixtures__logo" src="<?php echo esc_url( $hlogo ); ?>" alt="" width="<?php echo (int) $img_w; ?>" height="<?php echo (int) $img_h; ?>" loading="lazy" decoding="async" />
						</span>
					<?php endif; ?>
					<span class="tca-football-fixtures__team-name"><?php echo esc_html( $hname ); ?></span>
				</div>
				<div class="tca-football-fixtures__center">
					<?php if ( null !== $gh && null !== $ga ) : ?>
						<span class="tca-football-fixtures__score">
							<span class="tca-football-fixtures__score-num" data-score-home><?php echo esc_html( $gh ); ?></span>
							<span class="tca-football-fixtures__score-sep" aria-hidden="true">-</span>
							<span class="tca-football-fixtures__score-num" data-score-away><?php echo esc_html( $ga ); ?></span>
						</span>
					<?php else : ?>
						<span class="tca-football-fixtures__vs"><?php echo esc_html_x( 'vs', 'versus short', 'tca' ); ?></span>
					<?php endif; ?>
				</div>
				<div class="tca-football-fixtures__side tca-football-fixtures__side--away">
					<span class="tca-football-fixtures__team-name"><?php echo esc_html( $aname ); ?></span>
					<?php if ( $alogo ) : ?>
						<span class="<?php echo esc_attr( $logo_wrap_class ); ?>">
							<img class="tca-football-fixtures__logo" src="<?php echo esc_url( $alogo ); ?>" alt="" width="<?php echo (int) $img_w; ?>" height="<?php echo (int) $img_h; ?>" loading="lazy" decoding="async" />
						</span>
					<?php endif; ?>
				</div>
			</div>
		</article>
		<?php
	}
	return (string) ob_get_clean();
}

/**
 * AJAX: return refreshed live scores list HTML.
 */
function tca_ajax_api_football_live_scores() {
	check_ajax_referer( 'tca_football_live_scores', 'nonce' );

	$league_raw = isset( $_POST['league'] ) ? wp_unslash( $_POST['league'] ) : '';
	$season_raw = isset( $_POST['season'] ) ? wp_unslash( $_POST['season'] ) : '';
	$league_id  = is_numeric( $league_raw ) ? max( 1, (int) $league_raw ) : tca_api_football_world_cup_league_id();
	$season     = is_numeric( $season_raw ) ? max( 1900, min( 2100, (int) $season_raw ) ) : tca_api_football_world_cup_season_default();

	if ( ! $league_id || ! $season ) {
		wp_send_json_error(
			array( 'message' => __( 'Invalid league or season.', 'tca' ) ),
			400
		);
	}

	$icon_raw   = isset( $_POST['icons'] ) ? sanitize_text_field( wp_unslash( $_POST['icons'] ) ) : '';
	$icon_allowed = array( 'logos', 'logos_small', 'flags', 'flags_small' );
	$icon_mode  = in_array( $icon_raw, $icon_allowed, true ) ? $icon_raw : 'flags_small';

	$display_tz = tca_api_football_display_timezone();

	$state = tca_api_football_get_live_scores_state( $league_id, $season, $icon_mode, $display_tz );
	if ( is_wp_error( $state ) ) {
		wp_send_json_error(
			array( 'message' => $state->get_error_message() ),
			502
		);
	}

	wp_send_json_success(
		array(
			'body_html'     => $state['body_html'],
			'layout'        => $state['layout'],
			'count'         => (int) $state['count'],
			'live_html'     => $state['live_html'],
			'upcoming_html' => $state['upcoming_html'],
		)
	);
}

add_action( 'wp_ajax_tca_api_football_live_scores', 'tca_ajax_api_football_live_scores' );
add_action( 'wp_ajax_nopriv_tca_api_football_live_scores', 'tca_ajax_api_football_live_scores' );
