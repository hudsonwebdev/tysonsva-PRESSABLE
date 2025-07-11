<?php
/**
 * Stats Tracking_Pixel
 *
 * @package automattic/jetpack-stats
 */

namespace Automattic\Jetpack\Stats;

use Jetpack_Options;
use WP_Post;

/**
 * Stats Tracking_Pixel class.
 *
 * Responsible for embedding the Stats tracking pixel.
 *
 * @since 0.1.0
 */
class Tracking_Pixel {

	/**
	 * Array name.
	 *
	 * @var string $array_name The 'stats' array name
	 */
	const STATS_ARRAY_TO_STRING_FILTER = 'stats_array';

	const TRACKED_UTM_PARAMETERS = array(
		'utm_id',
		'utm_source',
		'utm_medium',
		'utm_campaign',
		'utm_term',
		'utm_content',
		'utm_source_platform',
		'utm_creative_format',
		'utm_marketing_tactic',
	);

	/**
	 * Stats Build View Data.
	 *
	 * @access public
	 * @return array
	 */
	public static function build_view_data() {
		global $wp_the_query;

		$blog        = Jetpack_Options::get_option( 'id' );
		$tz          = get_option( 'gmt_offset' );
		$v           = 'ext';
		$blog_url    = wp_parse_url( site_url() );
		$srv         = $blog_url['host'];
		$is_not_post = false;
		if ( $wp_the_query->is_single || $wp_the_query->is_page || $wp_the_query->is_posts_page ) {
			// Store and reset the queried_object and queried_object_id
			// Otherwise, redirect_canonical() will redirect to home_url( '/' ) for show_on_front = page sites where home_url() is not all lowercase.
			// Repro:
			// 1. Set home_url = https://ExamPle.com/
			// 2. Set show_on_front = page
			// 3. Set page_on_front = something
			// 4. Visit https://example.com/ !
			$queried_object    = isset( $wp_the_query->queried_object ) ? $wp_the_query->queried_object : null;
			$queried_object_id = isset( $wp_the_query->queried_object_id ) ? $wp_the_query->queried_object_id : null;
			try {
				$post_obj = $wp_the_query->get_queried_object();
				$post     = $post_obj instanceof WP_Post ? $post_obj->ID : '0';
			} finally {
				$wp_the_query->queried_object    = $queried_object;
				$wp_the_query->queried_object_id = $queried_object_id;
			}
		} else {
			$post        = '0';
			$is_not_post = true;
		}
		$view_data = compact( 'v', 'blog', 'post', 'tz', 'srv' );
		// Batcache removes some of the UTM params from $_GET, we need to extract them from uri directly instead.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- We're sanitizing individual params in the loop.
		$url_query = wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_QUERY );
		parse_str( (string) $url_query, $url_params );
		foreach ( self::TRACKED_UTM_PARAMETERS as $utm_parameter ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- UTMs are standardized parameters coming from outside WordPress, adding nonce is not possible
			if ( isset( $url_params[ $utm_parameter ] ) && is_scalar( $url_params[ $utm_parameter ] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- UTMs are standardized parameters coming from outside WordPress, adding nonce is not possible
				$view_data[ $utm_parameter ] = substr( sanitize_textarea_field( wp_unslash( $url_params[ $utm_parameter ] ) ), 0, 255 );
			}
		}

		if ( $is_not_post ) {
			if ( $wp_the_query->is_home() ) {
				$view_data['arch_home'] = '1';
			} elseif ( $wp_the_query->is_search() ) {
				$search_term               = $wp_the_query->query['s'] ?? $wp_the_query->query_vars['s'] ?? '';
				$view_data['arch_search']  = sanitize_text_field( $search_term );
				$view_data['arch_filters'] = sanitize_text_field( self::build_search_filters( $wp_the_query ) );
				$view_data['arch_results'] = $wp_the_query->posts ? $wp_the_query->post_count : 0;
			} elseif ( $wp_the_query->is_archive() ) {
				if ( $wp_the_query->is_date ) {
					$query                  = $wp_the_query->query;
					$date_parts             = array_filter( array( $query['year'] ?? null, $query['monthnum'] ?? null, $query['day'] ?? null ) );
					$date                   = implode( '/', $date_parts );
					$view_data['arch_date'] = $date;
				}
				if ( $wp_the_query->is_category ) {
					$view_data['arch_cat'] = $wp_the_query->query['category_name'] ?? $wp_the_query->query_vars['category_name'] ?? '';
				}
				if ( $wp_the_query->is_tag ) {
					$view_data['arch_tag'] = $wp_the_query->query['tag'] ?? $wp_the_query->query_vars['tag'] ?? '';
				}
				if ( $wp_the_query->is_author ) {
					$view_data['arch_author'] = $wp_the_query->query['author_name'] ?? '';
				}
				if ( $wp_the_query->is_tax ) {
					$query = $wp_the_query->query;
					if ( is_array( $query ) && count( $query ) === 1 ) {
						$view_data[ 'arch_tax_' . array_keys( $query )[0] ] = array_values( $query )[0];
					}
				}
				$view_data['arch_results'] = $wp_the_query->posts ? $wp_the_query->post_count : 0;
			} elseif ( $wp_the_query->is_404() ) {
				$view_data['arch_err'] = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) );
			} else {
				$view_data['arch_other'] = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) );
			}
		}
		return $view_data;
	}

	/**
	 * Collect the tracking data for a search page.
	 *
	 * @access private
	 * @param  \WP_Query $query The WP_Query object to parse all the filters from.
	 * @return string The search filters in a URL query string format.
	 */
	private static function build_search_filters( $query ) {
		$data = array(
			'posts_per_page' => $query->get( 'posts_per_page' ),
			'paged'          => ( $query->get( 'paged' ) ) ? absint( $query->get( 'paged' ) ) : 1,
			'orderby'        => $query->get( 'orderby' ),
			'order'          => $query->get( 'order' ),
		);

		if ( $query->get( 'author_name' ) ) {
			$data['author_name'] = $query->get( 'author_name' );
		}
		$filters = http_build_query( $data );

		$the_tax_query = $query->tax_query;
		$terms         = array();
		if ( ! empty( $the_tax_query->queried_terms ) && is_array( $the_tax_query->queried_terms ) ) {
			foreach ( $the_tax_query->queries as $tax_query ) {
				if ( ! is_array( $tax_query ) || ! isset( $tax_query['taxonomy'] ) ) {
					continue;
				}
				$taxonomy = $tax_query['taxonomy'];
				if ( ! isset( $terms[ $taxonomy ] ) || ! is_array( $terms[ $taxonomy ] ) ) {
					$terms[ $taxonomy ] = array();
				}
				$terms[ $taxonomy ] = array_merge( $terms[ $taxonomy ], $tax_query['terms'] );
			}
		}
		if ( ! empty( $terms ) ) {
			$filters .= '&terms=' . wp_json_encode( $terms );
		}
		return $filters;
	}

	/**
	 * Build the Stats tracking details.
	 *
	 * @since 0.6.0
	 *
	 * @access private
	 * @param array $data Array of data for the AMP pixel tracker.
	 * @return string
	 */
	private static function build_stats_details( $data ) {
		$data_stats_array = self::stats_array_to_string( $data );

		return sprintf(
			'_stq = window._stq || [];
_stq.push([ "view", JSON.parse(%1$s) ]);
_stq.push([ "clickTrackerInit", "%2$s", "%3$s" ]);',
			$data_stats_array,
			$data['blog'],
			$data['post']
		);
	}

	/**
	 * Enqueue the Stats pixel.
	 * Do not use this function directly, it is hooked into `wp_enqueue_scripts`.
	 *
	 * @access public
	 * @return void
	 */
	public static function enqueue_stats_script() {
		if ( self::is_amp_request() ) {
			return;
		}

		wp_enqueue_script(
			'jetpack-stats',
			'https://stats.wp.com/e-' . gmdate( 'YW' ) . '.js',
			array(),
			null, // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- The version is set in the URL.
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		$data = self::build_view_data();

		/**
		 * Filter the parameters added to the JavaScript stats tracking code.
		 *
		 * @module stats
		 *
		 * @since-jetpack 10.9
		 *
		 * @param array $data Array of options about the site and page you're on.
		 */
		$data = (array) apply_filters( 'jetpack_stats_footer_js_data', $data );

		$triggers = self::build_stats_details( $data );
		wp_add_inline_script(
			'jetpack-stats',
			$triggers,
			'before'
		);
	}

	/**
	 * Gets the stats footer for AMP output.
	 *
	 * @access private
	 * @param array $data Array of data for the AMP pixel tracker.
	 * @return string Returns the footer to add for the Stats tracker in an AMP scenario.
	 */
	private static function get_amp_footer( $data ) {
		/**
		 * Filter the parameters added to the AMP pixel tracking code.
		 *
		 * @module stats
		 *
		 * @since-jetpack 10.9
		 *
		 * @param array $data Array of options about the site and page you're on.
		 */
		$data = (array) apply_filters( 'jetpack_stats_footer_amp_data', $data );

		$data['host'] = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : ''; // input var ok.
		$data['rand'] = 'RANDOM'; // AMP placeholder.
		$data['ref']  = 'DOCUMENT_REFERRER'; // AMP placeholder.
		$data         = array_map( 'rawurlencode', $data );
		$pixel_url    = add_query_arg( $data, 'https://pixel.wp.com/g.gif' );
		return '<amp-pixel src="' . esc_url( $pixel_url ) . '"></amp-pixel>';
	}

	/**
	 * Build an AMP pixel.
	 * Do not use this function directly, it is hooked into `wp_footer`.
	 *
	 * @access public
	 * @return void
	 */
	public static function add_amp_pixel() {
		$data = self::build_view_data();
		if ( ! self::is_amp_request() ) {
			return;
		}

		$pixel = self::get_amp_footer( $data );
		echo $pixel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Stats Footer.
	 *
	 * @deprecated 0.6.0
	 *
	 * @access public
	 * @return void
	 */
	public static function add_to_footer() {
		_deprecated_function( __METHOD__, '0.6.0' );
	}

	/**
	 * Gets the footer to add for the Stats tracker.
	 *
	 * @deprecated 0.6.0
	 *
	 * @access public
	 * @param array $data Array of data for the JS stats tracker.
	 * @return void
	 */
	public static function get_footer_to_add( $data ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		_deprecated_function( __METHOD__, '0.6.0' );
	}

	/**
	 * Render the stats footer. Kept for backward compatibility on legacy AMF views.
	 *
	 * @deprecated 0.6.0
	 *
	 * @access public
	 * @param array $data Array of data for the JS stats tracker.
	 */
	public static function render_footer( $data ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		_deprecated_function( __METHOD__, '0.6.0' );
	}

	/**
	 * Render the stats footer for AMP output. Kept for backward compatibility.
	 *
	 * @access public
	 * @param array $data Array of data for the AMP pixel tracker.
	 */
	public static function render_amp_footer( $data ) {
		print self::get_amp_footer( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Creates the "array" string used as part of the JS tracker.
	 *
	 * @access private
	 * @param array $kvs Array of options about the site and page you're on.
	 * @return string
	 */
	private static function stats_array_to_string( $kvs ) {
		/**
		 * Filters the options added to the JavaScript Stats tracking code.
		 *
		 * @since-jetpack 1.1.0
		 *
		 * @param array $kvs Array of options about the site and page you're on.
		 */
		$kvs = (array) apply_filters( self::STATS_ARRAY_TO_STRING_FILTER, $kvs );
		$kvs = array_map( 'strval', $kvs );

		// Encode into JSON object, and then encode it into a string that's safe to embed into Javascript.
		// We will then use JSON.parse method in JS to read the array.
		return wp_json_encode( wp_json_encode( $kvs ) );
	}

	/**
	 * Does the page return AMP content.
	 *
	 * @return bool $is_amp_request Are we on AMP view.
	 */
	private static function is_amp_request() {
		$is_amp_request = ( function_exists( 'amp_is_request' ) && amp_is_request() );
		$is_amp_request = $is_amp_request || ( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() );

		/**
		 * Returns true if the current request should return valid AMP content.
		 *
		 * @since 6.2.0
		 *
		 * @param boolean $is_amp_request Is this request supposed to return valid AMP content?
		 */
		return apply_filters( 'jetpack_is_amp_request', $is_amp_request );
	}
}
