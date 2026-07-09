<?php
/**
 * Left navigation sidebar (desktop).
 *
 * Translated from design-reference/Shell.dc.html leftSidebar: brand, main nav,
 * divider, secondary nav, and a single collapse toggle. Collapse is one shared
 * state (html.cf-sidebar-collapsed) driving both width and label visibility.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$cf_site_name = collective_finity_brand_name();
$cf_nav       = collective_finity_get_shell_nav();
?>
<aside class="cf-left-sidebar" aria-label="<?php esc_attr_e( 'Primary navigation', 'collective-finity' ); ?>">
    <div class="cf-brand">
        <a class="cf-brand-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <?php echo collective_finity_brand_logo_markup( 'sidebar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <span class="cf-brand-word"><?php echo esc_html( strtoupper( $cf_site_name ) ); ?></span>
        </a>
    </div>

    <nav class="cf-nav-group" aria-label="<?php esc_attr_e( 'Main', 'collective-finity' ); ?>">
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

    <div class="cf-sidebar-spacer"></div>

    <button type="button" id="cf-sidebar-collapse-btn" class="cf-collapse-btn" aria-expanded="true" aria-label="<?php esc_attr_e( 'Collapse sidebar', 'collective-finity' ); ?>">
        <span class="cf-chevron"><?php echo collective_finity_icon( 'chevronLeft', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
        <span><?php esc_html_e( 'Collapse', 'collective-finity' ); ?></span>
    </button>
</aside>
