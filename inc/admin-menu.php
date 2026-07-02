<?php
/**
 * Collective Finity — unified wp-admin menu, dashboard, and branded screen detection.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Parent admin menu slug.
 */
function collective_finity_admin_menu_slug() {
    return 'collective-finity';
}

/**
 * Theme-owned admin.php page slugs that receive Collective Finity branding.
 */
function collective_finity_branded_admin_page_slugs() {
    return array(
        collective_finity_admin_menu_slug(),
        'collective-finity-options',
    );
}

/**
 * Register the Collective Finity top-level menu and dashboard (priority 5).
 */
function collective_finity_register_admin_menu() {
    $parent_slug = collective_finity_admin_menu_slug();
    $capability  = 'edit_theme_options';

    add_menu_page(
        __( 'Collective Finity', 'collective-finity' ),
        __( 'Collective Finity', 'collective-finity' ),
        $capability,
        $parent_slug,
        'collective_finity_render_admin_dashboard',
        'dashicons-admin-customizer',
        5
    );

    add_submenu_page(
        $parent_slug,
        __( 'Dashboard', 'collective-finity' ),
        __( 'Dashboard', 'collective-finity' ),
        $capability,
        $parent_slug,
        'collective_finity_render_admin_dashboard'
    );
}
add_action( 'admin_menu', 'collective_finity_register_admin_menu', 5 );

/**
 * Theme Options submenu (registered after parent menu exists).
 */
function collective_finity_register_theme_options_submenu() {
    add_submenu_page(
        collective_finity_admin_menu_slug(),
        __( 'Theme Options', 'collective-finity' ),
        __( 'Theme Options', 'collective-finity' ),
        'edit_theme_options',
        'collective-finity-options',
        'collective_finity_render_theme_options_page'
    );
}
add_action( 'admin_menu', 'collective_finity_register_theme_options_submenu', 6 );

/**
 * CF Auth admin URL for dashboard quick-link.
 */
function collective_finity_get_cf_auth_admin_url() {
    return admin_url( 'admin.php?page=cf-auth' );
}

/**
 * Dashboard landing page cards.
 */
function collective_finity_render_admin_dashboard() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    $parent = collective_finity_admin_menu_slug();
    $parts  = collective_finity_get_theme_parts();
    $cf_auth_url = collective_finity_get_cf_auth_admin_url();

    $cards = array(
        array(
            'icon'        => 'dashicons-admin-generic',
            'title'       => __( 'Theme Options', 'collective-finity' ),
            'description' => __( 'Colors, player, footer branding, and ad zones.', 'collective-finity' ),
            'url'         => admin_url( 'admin.php?page=collective-finity-options' ),
        ),
        array(
            'icon'        => 'dashicons-align-full-width',
            'title'       => __( 'Header Layout', 'collective-finity' ),
            'description' => __( 'Elementor-built header designs for your site.', 'collective-finity' ),
            'url'         => admin_url( 'edit.php?post_type=' . $parts['header']['post_type'] ),
        ),
        array(
            'icon'        => 'dashicons-align-wide',
            'title'       => __( 'Footer Layout', 'collective-finity' ),
            'description' => __( 'Visual footer templates built with Elementor.', 'collective-finity' ),
            'url'         => admin_url( 'edit.php?post_type=' . $parts['footer']['post_type'] ),
        ),
        array(
            'icon'        => 'dashicons-menu-alt3',
            'title'       => __( 'Side Panel Layout', 'collective-finity' ),
            'description' => __( 'Sidebar / side panel Elementor layouts.', 'collective-finity' ),
            'url'         => admin_url( 'edit.php?post_type=' . $parts['sidebar']['post_type'] ),
        ),
        array(
            'icon'        => 'dashicons-format-audio',
            'title'       => __( 'Tracks', 'collective-finity' ),
            'description' => __( 'Manage singles and album tracks.', 'collective-finity' ),
            'url'         => admin_url( 'edit.php?post_type=tracks' ),
        ),
        array(
            'icon'        => 'dashicons-portfolio',
            'title'       => __( 'Albums', 'collective-finity' ),
            'description' => __( 'Album releases and tracklists.', 'collective-finity' ),
            'url'         => admin_url( 'edit.php?post_type=albums' ),
        ),
        array(
            'icon'        => 'dashicons-megaphone',
            'title'       => __( 'Ad Manager', 'collective-finity' ),
            'description' => __( 'Configure ad zones and preview placeholders.', 'collective-finity' ),
            'url'         => admin_url( 'admin.php?page=collective-finity-options&tab=ads' ),
        ),
    );

    if ( $cf_auth_url ) {
        $cards[] = array(
            'icon'        => 'dashicons-groups',
            'title'       => __( 'CF Auth', 'collective-finity' ),
            'description' => __( 'Members, overview, and authentication settings.', 'collective-finity' ),
            'url'         => $cf_auth_url,
        );
    }
    ?>
    <div class="wrap cf-admin-dashboard-wrap">
        <div class="cf-admin-page-header">
            <h1><?php esc_html_e( 'Collective Finity', 'collective-finity' ); ?></h1>
            <p class="description"><?php esc_html_e( 'Your music theme command center — layouts, content, and monetization in one place.', 'collective-finity' ); ?></p>
        </div>

        <div class="cf-admin-dashboard-grid">
            <?php foreach ( $cards as $card ) : ?>
                <a href="<?php echo esc_url( $card['url'] ); ?>" class="cf-admin-dashboard-card">
                    <span class="cf-admin-dashboard-card__icon dashicons <?php echo esc_attr( $card['icon'] ); ?>" aria-hidden="true"></span>
                    <span class="cf-admin-dashboard-card__title"><?php echo esc_html( $card['title'] ); ?></span>
                    <span class="cf-admin-dashboard-card__desc"><?php echo esc_html( $card['description'] ); ?></span>
                </a>
            <?php endforeach; ?>

            <?php if ( ! $cf_auth_url ) : ?>
                <div class="cf-admin-dashboard-card cf-admin-dashboard-card--muted">
                    <span class="cf-admin-dashboard-card__icon dashicons dashicons-groups" aria-hidden="true"></span>
                    <span class="cf-admin-dashboard-card__title"><?php esc_html_e( 'CF Auth', 'collective-finity' ); ?></span>
                    <span class="cf-admin-dashboard-card__desc"><?php esc_html_e( 'Install and activate the CF Auth plugin to manage members here.', 'collective-finity' ); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Whether the current admin screen should receive Collective Finity branding styles.
 */
function collective_finity_is_branded_admin_screen( $hook = '' ) {
    if ( ! is_admin() ) {
        return false;
    }

    if ( ! $hook && function_exists( 'get_current_screen' ) ) {
        $screen = get_current_screen();
        if ( ! $screen ) {
            return false;
        }
        $hook = $screen->id;
    }

    if ( ! $hook ) {
        return false;
    }

    $branded_hooks = array(
        'toplevel_page_collective-finity',
        'collective-finity_page_collective-finity-options',
        'toplevel_page_collective-finity-options',
    );

    if ( in_array( $hook, $branded_hooks, true ) ) {
        return true;
    }

    if ( false !== strpos( $hook, 'cf_header' )
        || false !== strpos( $hook, 'cf_footer' )
        || false !== strpos( $hook, 'cf_sidebar' ) ) {
        return true;
    }

    $screen = get_current_screen();
    if ( ! $screen ) {
        return false;
    }

    if ( in_array( $screen->post_type, array( 'tracks', 'albums' ), true ) ) {
        return true;
    }

    if ( in_array( $screen->taxonomy, array( 'music_genre', 'track_artist' ), true ) ) {
        return true;
    }

    if ( ! empty( $_GET['page'] ) ) {
        $page = sanitize_key( wp_unslash( $_GET['page'] ) );
        if ( in_array( $page, collective_finity_branded_admin_page_slugs(), true ) ) {
            return true;
        }
    }

    return false;
}

/**
 * Enqueue branded admin styles on theme-related screens only.
 */
function collective_finity_enqueue_admin_branding( $hook ) {
    if ( ! collective_finity_is_branded_admin_screen( $hook ) ) {
        return;
    }

    $css_path = get_template_directory() . '/assets/css/admin-branding.css';
    $version  = file_exists( $css_path ) ? filemtime( $css_path ) : wp_get_theme()->get( 'Version' );

    wp_enqueue_style(
        'cf-admin-branding',
        get_template_directory_uri() . '/assets/css/admin-branding.css',
        array(),
        $version
    );

    wp_enqueue_style( 'dashicons' );
}
add_action( 'admin_enqueue_scripts', 'collective_finity_enqueue_admin_branding' );

/**
 * Add body class on branded admin screens.
 */
function collective_finity_branded_admin_body_class( $classes ) {
    if ( collective_finity_is_branded_admin_screen() ) {
        $classes .= ' cf-branded-admin-screen';
    }
    return $classes;
}
add_filter( 'admin_body_class', 'collective_finity_branded_admin_body_class' );

/**
 * Amber accent for the Collective Finity top-level wp-admin sidebar menu icon.
 */
function collective_finity_admin_menu_icon_style() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    $menu_id = 'toplevel_page_' . collective_finity_admin_menu_slug();
    ?>
    <style id="cf-admin-menu-icon">
        #<?php echo esc_attr( $menu_id ); ?> .wp-menu-image:before {
            color: #FFB700 !important;
        }
        #<?php echo esc_attr( $menu_id ); ?>:hover .wp-menu-image:before,
        #<?php echo esc_attr( $menu_id ); ?>.wp-has-current-submenu .wp-menu-image:before,
        #<?php echo esc_attr( $menu_id ); ?>.current .wp-menu-image:before {
            color: #ffd35c !important;
        }
    </style>
    <?php
}
add_action( 'admin_head', 'collective_finity_admin_menu_icon_style' );
