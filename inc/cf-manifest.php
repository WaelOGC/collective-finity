<?php
/**
 * Dynamic web app manifest for Add to Home Screen / PWA install.
 *
 * Served at /manifest.json. No service worker — banner + manifest only.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Brand color used as PWA theme_color and the <meta name="theme-color"> value.
 *
 * Matches --primary-color / --cf-accent in style.css and cf-shell.css.
 *
 * @return string
 */
function collective_finity_pwa_theme_color() {
	return '#FFB700';
}

/**
 * Splash / background color for the installed web app.
 *
 * Matches --darker-bg / --cf-bg-darkest.
 *
 * @return string
 */
function collective_finity_pwa_background_color() {
	return '#050505';
}

/**
 * Output the web app manifest as JSON and exit.
 *
 * @return void
 */
function collective_finity_output_manifest() {
	$name = get_bloginfo( 'name' );
	if ( ! is_string( $name ) || '' === $name ) {
		$name = 'Collective Finity';
	}

	$icon_url = collective_finity_site_logo_url( 'full' );
	$path     = wp_parse_url( $icon_url, PHP_URL_PATH );
	$filetype = is_string( $path ) ? wp_check_filetype( $path ) : array();
	$icon_type = ( ! empty( $filetype['type'] ) ) ? $filetype['type'] : 'image/png';

	$icon = array(
		'src'   => $icon_url,
		'type'  => $icon_type,
		'purpose' => 'any',
	);

	$manifest = array(
		'name'             => $name,
		'short_name'       => $name,
		'start_url'        => '/',
		'scope'            => '/',
		'display'          => 'standalone',
		'background_color' => collective_finity_pwa_background_color(),
		'theme_color'      => collective_finity_pwa_theme_color(),
		'icons'            => array(
			array_merge( $icon, array( 'sizes' => '192x192' ) ),
			array_merge( $icon, array( 'sizes' => '512x512' ) ),
		),
	);

	nocache_headers();
	status_header( 200 );
	header( 'Content-Type: application/json; charset=utf-8' );
	echo wp_json_encode( $manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	exit;
}

/**
 * Serve /manifest.json from the current request URI.
 *
 * Smaller than a rewrite rule: no flush needed, works on 404s.
 *
 * @return void
 */
function collective_finity_maybe_output_manifest() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$request_path = wp_parse_url( $request_uri, PHP_URL_PATH );
	$manifest_path = wp_parse_url( home_url( '/manifest.json' ), PHP_URL_PATH );

	if ( ! is_string( $request_path ) || ! is_string( $manifest_path ) ) {
		return;
	}

	if ( untrailingslashit( $request_path ) !== untrailingslashit( $manifest_path ) ) {
		return;
	}

	collective_finity_output_manifest();
}
add_action( 'template_redirect', 'collective_finity_maybe_output_manifest', 0 );
