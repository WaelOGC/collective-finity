<?php
/**
 * Default theme header markup.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$cf_logo_url  = collective_finity_site_logo_url( 'thumbnail' );
$cf_site_name = collective_finity_brand_name();
?>
<header class="cf-forced-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <button id="cf-sidebar-toggle-btn" class="cf-p-btn" style="color: var(--primary-color); font-size: 1.5rem; display: flex; align-items: center; padding: 0; margin: 0; z-index: 10002;" aria-label="<?php esc_attr_e( 'Toggle sidebar', 'collective-finity' ); ?>" aria-expanded="false" aria-controls="cf-music-sidebar">
            <span id="cf-toggle-icon" class="dashicons dashicons-menu-alt3"></span>
        </button>

        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cf-site-brand-link">
            <img src="<?php echo esc_url( $cf_logo_url ); ?>" alt="<?php echo esc_attr( $cf_site_name ); ?>" class="cf-brand-logo" style="width:32px;height:32px;max-width:32px;max-height:32px;display:block;object-fit:contain;">
            <span class="cf-brand-text"><?php echo esc_html( $cf_site_name ); ?></span>
        </a>
    </div>

    <?php if ( is_active_sidebar( 'header-widget-area' ) ) : ?>
        <div class="cf-header-widget-area">
            <?php dynamic_sidebar( 'header-widget-area' ); ?>
        </div>
    <?php endif; ?>

    <nav class="cf-forced-nav" aria-label="<?php esc_attr_e( 'Primary menu', 'collective-finity' ); ?>">
        <?php
        if ( has_nav_menu( 'primary' ) ) {
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'fallback_cb'    => false,
                    'depth'          => 2,
                    'menu_class'     => 'cf-header-menu',
                )
            );
        } else {
            echo '<ul class="cf-header-menu">';
            echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'collective-finity' ) . '</a></li>';
            echo '<li><a href="' . esc_url( home_url( '/tracks/' ) ) . '">' . esc_html__( 'Music Library', 'collective-finity' ) . '</a></li>';
            echo '<li><a href="' . esc_url( home_url( '/albums/' ) ) . '">' . esc_html__( 'Albums', 'collective-finity' ) . '</a></li>';
            echo '<li><a href="' . esc_url( home_url( '/about/' ) ) . '">' . esc_html__( 'About', 'collective-finity' ) . '</a></li>';
            echo '<li><a href="' . esc_url( home_url( '/contact-us/' ) ) . '">' . esc_html__( 'Contact', 'collective-finity' ) . '</a></li>';
            echo '</ul>';
        }
        ?>
    </nav>
</header>
