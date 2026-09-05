<?php
/**
 * Performance optimizations for PageSpeed Insights and Core Web Vitals.
 *
 * - Preload main stylesheet and preconnect to third-party origins
 * - Defer non-critical scripts; move jQuery to footer
 * - Load non-critical CSS asynchronously to reduce render-blocking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add preload for main CSS and preconnect for third-party origins.
 */
function tca_performance_resource_hints() {
	$main_css = get_template_directory_uri() . '/public/css/main.css';
	$version  = defined( '_S_VERSION' ) ? _S_VERSION : '1.0.0';

	echo '<link rel="preload" href="' . esc_url( $main_css ) . '?ver=' . esc_attr( $version ) . '" as="style">' . "\n";
	echo '<link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>' . "\n";

	if ( is_singular( 'neighborhood' ) || ( function_exists( 'tca_post_has_neighborhood_map_block' ) && tca_post_has_neighborhood_map_block() ) ) {
		echo '<link rel="preconnect" href="https://api.mapbox.com" crossorigin>' . "\n";
	}
}
add_action( 'wp_head', 'tca_performance_resource_hints', 1 );

/**
 * Move jQuery and jquery-migrate to the footer so they don't block initial paint.
 */
function tca_move_jquery_to_footer() {
	wp_scripts()->add_data( 'jquery', 'group', 1 );
	wp_scripts()->add_data( 'jquery-core', 'group', 1 );
	wp_scripts()->add_data( 'jquery-migrate', 'group', 1 );
}
add_action( 'wp_enqueue_scripts', 'tca_move_jquery_to_footer', 100 );

/**
 * Add defer to theme and non-critical scripts to reduce render-blocking.
 */
function tca_defer_scripts( $tag, $handle, $src ) {
	$defer_handles = array(
		'tca-navigation',
		'tca-neighborhood',
		'mapbox-js',
		// Must defer with mapbox-js: otherwise these run before Mapbox and never init.
		'tca-events-locations-map',
		'tca-events-location-list-map',
	);

	if ( in_array( $handle, $defer_handles, true ) ) {
		return str_replace( ' src', ' defer src', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'tca_defer_scripts', 10, 3 );

/**
 * Handles of stylesheets to load asynchronously (non-render-blocking).
 * Filter 'tca_async_style_handles' to add/remove; remove if you see FOUC or broken layout.
 *
 * @return array Style handle IDs.
 */
function tca_async_style_handles() {
	$handles = array(
		'tca-style',                     // Theme style.css.
		'cmplz-cookieblocker',           // Complianz (or 'cookieblocker').
		'cookieblocker',
		'sbi-styles',                    // Smash Balloon Instagram.
		'cff',                           // Custom Facebook Feed (or 'cff-style').
		'cff-style',
		'sb-font-awesome',               // Font Awesome (Smash Balloon).
		'gravity-forms-theme-framework',
		'gravity-forms-theme-reset',
		'gravity-forms-theme-foundation',
		'gravity-forms-orbital-theme',
		'gform_theme_framework',         // Alternative GF handle.
		'gform_theme_reset',
		'gform_theme_foundation',
		'theme',                         // Events Manager theme.min.css.
		'events-manager',                // Alternative EM handle.
	);
	return apply_filters( 'tca_async_style_handles', $handles );
}

/**
 * Load selected stylesheets asynchronously (media="print" then onload swap to "all").
 */
function tca_async_styles( $html, $handle, $href, $media ) {
	$async_handles = tca_async_style_handles();
	if ( ! in_array( $handle, $async_handles, true ) ) {
		return $html;
	}
	$id = esc_attr( $handle . '-css' );
	$html = str_replace( "id='" . $handle . "-css'", "id='" . $id . "'", $html );
	$html = str_replace( "id=\"" . $handle . "-css\"", "id=\"" . $id . "\"", $html );
	$html = str_replace( "media='all'", "media='print' onload=\"this.media='all'; this.onload=null;\"", $html );
	$html = str_replace( 'media="all"', 'media="print" onload="this.media=\'all\'; this.onload=null;"', $html );
	$html .= '<noscript><link rel="stylesheet" href="' . esc_url( $href ) . '" media="all"></noscript>';
	return $html;
}
add_filter( 'style_loader_tag', 'tca_async_styles', 10, 4 );
