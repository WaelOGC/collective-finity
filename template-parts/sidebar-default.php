<?php
/**
 * Default music sidebar markup.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$cf_logo_url   = collective_finity_site_logo_url( 'thumbnail' );
$cf_site_name  = collective_finity_brand_name();
$favorites_url = is_user_logged_in() ? home_url( '/cf-profile#favorites' ) : home_url( '/cf-login' );
$playlists_url = is_user_logged_in() ? home_url( '/cf-profile#history' ) : home_url( '/cf-register' );
?>
<div id="cf-music-sidebar" class="cf-sidebar-panel">
    <div class="cf-sidebar-brand">
        <img src="<?php echo esc_url( $cf_logo_url ); ?>"
             class="cf-sidebar-logo"
             alt="<?php echo esc_attr( $cf_site_name ); ?>"
             width="26"
             height="26">
        <span class="cf-sidebar-title-text cf-menu-text"><?php echo esc_html( $cf_site_name ); ?></span>
    </div>

    <?php if ( is_active_sidebar( 'sidebar-widget-area' ) ) : ?>
        <div class="cf-sidebar-widget-area">
            <?php dynamic_sidebar( 'sidebar-widget-area' ); ?>
        </div>
    <?php endif; ?>

    <nav class="cf-sidebar-menu" aria-label="<?php esc_attr_e( 'Main Navigation', 'collective-finity' ); ?>">
        <ul>
            <li>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <span class="dashicons dashicons-admin-home"></span>
                    <span class="cf-menu-text"><?php esc_html_e( 'Home Portal', 'collective-finity' ); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/tracks/' ) ); ?>">
                    <span class="dashicons dashicons-format-audio"></span>
                    <span class="cf-menu-text"><?php esc_html_e( 'Music Library', 'collective-finity' ); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/albums/' ) ); ?>">
                    <span class="dashicons dashicons-portfolio"></span>
                    <span class="cf-menu-text"><?php esc_html_e( 'Albums & Collections', 'collective-finity' ); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">
                    <span class="dashicons dashicons-info"></span>
                    <span class="cf-menu-text"><?php esc_html_e( 'About', 'collective-finity' ); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/join-community/' ) ); ?>">
                    <span class="dashicons dashicons-groups"></span>
                    <span class="cf-menu-text"><?php esc_html_e( 'Join Community', 'collective-finity' ); ?></span>
                </a>
            </li>
            <li class="cf-sidebar-divider"></li>
            <li>
                <a href="<?php echo esc_url( $favorites_url ); ?>">
                    <span class="dashicons dashicons-heart"></span>
                    <span class="cf-menu-text"><?php esc_html_e( 'Favorites & Liked', 'collective-finity' ); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( $playlists_url ); ?>">
                    <span class="dashicons dashicons-playlist-audio"></span>
                    <span class="cf-menu-text"><?php esc_html_e( 'Personal Playlists', 'collective-finity' ); ?></span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<style>
    .cf-sidebar-panel {
        color: #f2f2f2;
    }
    .cf-sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 22px 12px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        margin-bottom: 8px;
    }
    .cf-sidebar-title-text {
        color: #fff;
        font-weight: 700;
        font-family: 'Space Mono', sans-serif;
        font-size: 0.95rem;
    }
    .cf-sidebar-menu ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .cf-sidebar-menu li a {
        display: flex;
        align-items: center;
        gap: 14px;
        color: #bdbdbd;
        text-decoration: none;
        padding: 13px 22px;
        border-radius: 10px;
        margin: 4px 10px;
        transition: background 0.2s ease, color 0.2s ease;
    }
    .cf-sidebar-menu li a:hover,
    .cf-sidebar-menu li.active a {
        background: rgba(255, 183, 0, 0.12);
        color: #fff;
    }
    .cf-sidebar-divider {
        border-top: 1px solid rgba(255,255,255,0.05);
        margin: 12px 0;
        list-style: none;
        height: 1px;
    }
</style>
