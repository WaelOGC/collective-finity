<?php
/**
 * Tablet / mobile top bar + navigation drawer.
 *
 * Mirrors the mobile render path in design-reference/Shell.dc.html
 * (hamburger left, brand center, account right, slide-in nav drawer).
 * Only visible below 1024px via cf-shell.css.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$cf_site_name = collective_finity_brand_name();
$cf_nav       = collective_finity_get_shell_nav();
?>
<header class="cf-mobile-topbar">
    <button type="button" id="cf-mobile-menu-btn" class="cf-icon-btn" aria-label="<?php esc_attr_e( 'Open menu', 'collective-finity' ); ?>">
        <?php echo collective_finity_icon( 'menu', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </button>
    <a class="cf-mobile-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <?php echo collective_finity_brand_logo_markup( 'mobile' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <span class="cf-mobile-brand-word"><?php echo esc_html( strtoupper( $cf_site_name ) ); ?></span>
    </a>
    <div class="cf-mobile-topbar-actions">
        <?php collective_finity_render_search_trigger(); ?>
        <a class="cf-icon-btn" href="<?php echo esc_url( is_user_logged_in() ? admin_url( 'profile.php' ) : wp_login_url() ); ?>" aria-label="<?php esc_attr_e( 'Account', 'collective-finity' ); ?>">
            <?php echo collective_finity_icon( 'user', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </a>
    </div>
</header>

<div id="cf-mobile-scrim" class="cf-mobile-scrim"></div>

<aside class="cf-mobile-drawer" aria-label="<?php esc_attr_e( 'Mobile navigation', 'collective-finity' ); ?>">
    <div class="cf-mobile-drawer-head">
        <span><?php esc_html_e( 'MENU', 'collective-finity' ); ?></span>
        <button type="button" id="cf-mobile-close-btn" class="cf-icon-btn" aria-label="<?php esc_attr_e( 'Close menu', 'collective-finity' ); ?>">
            <?php echo collective_finity_icon( 'close', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </button>
    </div>

    <nav class="cf-nav-group" aria-label="<?php esc_attr_e( 'Primary', 'collective-finity' ); ?>">
        <?php foreach ( $cf_nav['main'] as $cf_item ) {
            collective_finity_render_nav_row( $cf_item );
        } ?>
    </nav>

    <div class="cf-nav-divider"></div>

    <nav class="cf-nav-group" aria-label="<?php esc_attr_e( 'Library', 'collective-finity' ); ?>">
        <?php foreach ( $cf_nav['secondary'] as $cf_item ) {
            collective_finity_render_nav_row( $cf_item );
        } ?>
    </nav>
</aside>
