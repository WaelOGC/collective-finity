<?php
/**
 * Legacy Theme Builder redirects — kept for backward-compatible links.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Redirect old Theme Builder URLs to Theme Options.
 */
function collective_finity_redirect_legacy_theme_builder() {
    if ( ! is_admin() || ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
    if ( 'collective-finity-theme' === $page ) {
        wp_safe_redirect( admin_url( 'admin.php?page=collective-finity-options' ) );
        exit;
    }

    if ( 0 === strpos( $page, 'cf-theme-part-' ) ) {
        $part = str_replace( 'cf-theme-part-', '', $page );
        wp_safe_redirect( admin_url( 'admin.php?page=collective-finity-options&tab=' . $part ) );
        exit;
    }
}
add_action( 'admin_init', 'collective_finity_redirect_legacy_theme_builder' );
