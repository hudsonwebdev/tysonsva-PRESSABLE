<?php
/**
 * .env loader for the TCA theme.
 *
 * Reads simple KEY=VALUE pairs from a .env file located one directory above
 * the webroot. Path resolution is identical in both environments:
 *
 *   Local:     app/public/  ->  app/.env
 *   Pressable: htdocs/      ->  ~/.env
 *
 * The .env file must be created manually on each environment and is never
 * committed (app/public/.gitignore already excludes it; ~/.env on Pressable
 * sits outside the git tree entirely).
 *
 * Values are exposed via getenv(), $_ENV, and $_SERVER so any of the
 * standard PHP access patterns work:
 *
 *   $token = getenv( 'MAPBOX_ACCESS_TOKEN' );
 *   $token = $_ENV['MAPBOX_ACCESS_TOKEN'] ?? '';
 *   $token = tca_env( 'MAPBOX_ACCESS_TOKEN' );   // helper below
 *
 * @package tca
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'tca_load_env_file' ) ) {
	/**
	 * Parse a .env file and populate $_ENV / $_SERVER / putenv().
	 *
	 * Format: KEY=VALUE per line. Blank lines and lines starting with # are
	 * ignored. A single set of matching surrounding single or double quotes
	 * is stripped from the value. Pre-existing environment variables are
	 * never overwritten, so real server-set env vars take precedence.
	 *
	 * @param string $path Absolute path to the .env file.
	 * @return bool True if the file was loaded, false if not readable.
	 */
	function tca_load_env_file( $path ) {
		if ( ! is_readable( $path ) ) {
			return false;
		}
		$lines = file( $path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		if ( ! is_array( $lines ) ) {
			return false;
		}
		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( $line === '' || $line[0] === '#' || strpos( $line, '=' ) === false ) {
				continue;
			}
			list( $name, $value ) = explode( '=', $line, 2 );
			$name  = trim( $name );
			$value = trim( $value );
			if ( $name === '' ) {
				continue;
			}
			if ( strlen( $value ) >= 2 ) {
				$first = $value[0];
				$last  = substr( $value, -1 );
				if ( ( $first === '"' && $last === '"' ) || ( $first === "'" && $last === "'" ) ) {
					$value = substr( $value, 1, -1 );
				}
			}
			if ( getenv( $name ) === false ) {
				putenv( "$name=$value" );
			}
			if ( ! array_key_exists( $name, $_ENV ) ) {
				$_ENV[ $name ] = $value;
			}
			if ( ! array_key_exists( $name, $_SERVER ) ) {
				$_SERVER[ $name ] = $value;
			}
		}
		return true;
	}
}

if ( ! function_exists( 'tca_env' ) ) {
	/**
	 * Read an environment variable with an optional fallback.
	 *
	 * @param string $name    Variable name.
	 * @param string $default Returned when the variable is missing/empty.
	 * @return string
	 */
	function tca_env( $name, $default = '' ) {
		$value = getenv( $name );
		if ( $value === false || $value === '' ) {
			if ( isset( $_ENV[ $name ] ) && $_ENV[ $name ] !== '' ) {
				return (string) $_ENV[ $name ];
			}
			return $default;
		}
		return (string) $value;
	}
}

/**
 * Resolve and load .env from the first readable candidate path.
 *
 * Candidate paths, in order of priority:
 *   1. TCA_ENV_PATH constant (define in wp-config.php to pin an explicit path)
 *   2. dirname( ABSPATH ) . '/.env'   — works on Local (app/.env)
 *   3. PHP's $HOME env var . '/.env'  — works on most managed hosts (incl.
 *      Pressable, where dirname(ABSPATH) is the unwritable /home and the
 *      site user's actual home is one level deeper).
 *   4. dirname( dirname( ABSPATH ) ) . '/.env' — two-up fallback.
 */
( function () {
	$candidates = array();

	if ( defined( 'TCA_ENV_PATH' ) && TCA_ENV_PATH ) {
		$candidates[] = (string) TCA_ENV_PATH;
	}

	$candidates[] = dirname( ABSPATH ) . '/.env';

	$home = getenv( 'HOME' );
	if ( $home ) {
		$candidates[] = rtrim( $home, '/' ) . '/.env';
	}

	$candidates[] = dirname( dirname( ABSPATH ) ) . '/.env';

	foreach ( array_unique( $candidates ) as $candidate ) {
		if ( tca_load_env_file( $candidate ) ) {
			return;
		}
	}
} )();
