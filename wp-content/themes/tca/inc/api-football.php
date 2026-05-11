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
		'date' => '',
		'time' => '',
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
	$out['time'] = $local->format( 'g:i A' );
	return $out;
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

add_action( 'wp_ajax_tca_api_football_fixtures', 'tca_ajax_api_football_fixtures' );
add_action( 'wp_ajax_nopriv_tca_api_football_fixtures', 'tca_ajax_api_football_fixtures' );
