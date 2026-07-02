<?php
/**
 * Custom post types for Header, Footer, and Sidebar templates.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register theme template CPTs.
 */
function collective_finity_register_theme_template_cpts() {
    foreach ( collective_finity_get_theme_parts() as $slug => $config ) {
        $post_type = $config['post_type'];

        register_post_type(
            $post_type,
            array(
                'labels'              => array(
                    'name'               => $config['menu_name'],
                    'singular_name'      => $config['singular'],
                    'menu_name'          => $config['menu_name'],
                    'add_new'            => $config['add_new'],
                    'add_new_item'       => $config['add_new_item'],
                    'edit_item'          => $config['edit_item'],
                    'new_item'           => $config['new_item'],
                    'view_item'          => $config['view_item'],
                    'search_items'       => $config['search_items'],
                    'not_found'          => $config['not_found'],
                    'not_found_in_trash' => $config['not_found_in_trash'],
                    'all_items'          => $config['all_items'],
                ),
                'public'              => true,
                'publicly_queryable'  => true,
                'show_ui'             => true,
                'show_in_menu'        => collective_finity_admin_menu_slug(),
                'show_in_nav_menus'   => false,
                'menu_position'       => $config['menu_position'],
                'menu_icon'           => $config['menu_icon'],
                'capability_type'     => 'page',
                'map_meta_cap'        => true,
                'hierarchical'        => false,
                'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions', 'elementor' ),
                'has_archive'         => false,
                'rewrite'             => array(
                    'slug'       => str_replace( '_', '-', $post_type ),
                    'with_front' => false,
                ),
                'query_var'           => true,
                'show_in_rest'        => true,
                'exclude_from_search' => true,
            )
        );
    }
}
add_action( 'init', 'collective_finity_register_theme_template_cpts', 5 );

/**
 * Enable Elementor for theme template CPTs.
 */
function collective_finity_enable_elementor_for_template_cpts() {
    if ( ! collective_finity_is_elementor_active() ) {
        return;
    }

    foreach ( collective_finity_get_theme_parts() as $config ) {
        add_post_type_support( $config['post_type'], 'elementor' );
    }

    collective_finity_sync_elementor_cpt_support_option();
}
add_action( 'elementor/init', 'collective_finity_enable_elementor_for_template_cpts', 5 );
add_action( 'after_switch_theme', 'collective_finity_enable_elementor_for_template_cpts' );
add_action( 'admin_init', 'collective_finity_enable_elementor_for_template_cpts' );

/**
 * Always include theme template CPTs in Elementor post type support.
 */
function collective_finity_sync_elementor_cpt_support_option() {
    $supported = get_option( 'elementor_cpt_support', array( 'page', 'post' ) );
    if ( ! is_array( $supported ) ) {
        $supported = array( 'page', 'post' );
    }

    $changed = false;
    foreach ( collective_finity_get_theme_parts() as $config ) {
        if ( ! in_array( $config['post_type'], $supported, true ) ) {
            $supported[] = $config['post_type'];
            $changed      = true;
        }
    }

    if ( $changed ) {
        update_option( 'elementor_cpt_support', array_values( array_unique( $supported ) ) );
    }
}

/**
 * Ensure Elementor settings UI lists our CPTs.
 */
function collective_finity_elementor_valid_post_types( $post_types ) {
    foreach ( collective_finity_get_theme_parts() as $config ) {
        if ( ! in_array( $config['post_type'], $post_types, true ) ) {
            $post_types[] = $config['post_type'];
        }
    }
    return $post_types;
}
add_filter( 'elementor/settings/valid_post_types', 'collective_finity_elementor_valid_post_types' );

/**
 * Runtime filter so Elementor always treats our CPTs as supported.
 */
function collective_finity_elementor_cpt_support_option( $value ) {
    if ( ! is_array( $value ) ) {
        $value = array( 'page', 'post' );
    }

    foreach ( collective_finity_get_theme_parts() as $config ) {
        if ( ! in_array( $config['post_type'], $value, true ) ) {
            $value[] = $config['post_type'];
        }
    }

    return array_values( array_unique( $value ) );
}
add_filter( 'option_elementor_cpt_support', 'collective_finity_elementor_cpt_support_option' );

/**
 * Prepare new templates for Elementor editing.
 */
function collective_finity_prepare_template_for_elementor( $post_id, $post, $update ) {
    if ( $update || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
        return;
    }

    $valid_types = wp_list_pluck( collective_finity_get_theme_parts(), 'post_type' );
    if ( ! in_array( $post->post_type, $valid_types, true ) ) {
        return;
    }

    if ( ! get_post_meta( $post_id, '_elementor_edit_mode', true ) ) {
        update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
    }

    // Use standard page document type — NOT theme-builder library types.
    if ( ! get_post_meta( $post_id, '_elementor_template_type', true ) ) {
        update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
    }
}
add_action( 'wp_insert_post', 'collective_finity_prepare_template_for_elementor', 20, 3 );

/**
 * Fix legacy templates that were saved with header/footer/section types.
 */
function collective_finity_fix_legacy_elementor_template_types( $post_id ) {
    if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
        return;
    }

    $post = get_post( $post_id );
    if ( ! $post || ! collective_finity_get_part_by_post_type( $post->post_type ) ) {
        return;
    }

    $type = get_post_meta( $post_id, '_elementor_template_type', true );
    if ( in_array( $type, array( 'header', 'footer', 'section' ), true ) ) {
        update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
    }

    if ( ! get_post_meta( $post_id, '_elementor_edit_mode', true ) ) {
        update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
    }
}
add_action( 'save_post_cf_header', 'collective_finity_fix_legacy_elementor_template_types' );
add_action( 'save_post_cf_footer', 'collective_finity_fix_legacy_elementor_template_types' );
add_action( 'save_post_cf_sidebar', 'collective_finity_fix_legacy_elementor_template_types' );

/**
 * Prominent Elementor edit button on template edit screens.
 */
function collective_finity_template_edit_metabox() {
    if ( ! collective_finity_is_elementor_active() ) {
        return;
    }

    foreach ( collective_finity_get_theme_parts() as $config ) {
        add_meta_box(
            'cf-elementor-edit',
            __( 'Elementor Builder', 'collective-finity' ),
            'collective_finity_render_elementor_edit_metabox',
            $config['post_type'],
            'side',
            'high'
        );
    }
}
add_action( 'add_meta_boxes', 'collective_finity_template_edit_metabox' );

function collective_finity_render_elementor_edit_metabox( $post ) {
    if ( ! collective_finity_is_elementor_active() ) {
        echo '<p>' . esc_html__( 'Elementor is not active.', 'collective-finity' ) . '</p>';
        return;
    }

    $url = collective_finity_elementor_edit_url( $post->ID );
    echo '<p><a class="button button-primary button-hero" href="' . esc_url( $url ) . '" style="width:100%;text-align:center;">' . esc_html__( 'Edit with Elementor', 'collective-finity' ) . '</a></p>';
    echo '<p class="description">' . esc_html__( 'Design this template visually, then set it as Active from the list table or Theme Options.', 'collective-finity' ) . '</p>';
}

/**
 * One-time migration for existing template posts.
 */
function collective_finity_migrate_existing_template_posts_for_elementor() {
    if ( get_option( 'cf_elementor_templates_migrated' ) ) {
        return;
    }

    foreach ( collective_finity_get_theme_parts() as $config ) {
        $posts = get_posts(
            array(
                'post_type'      => $config['post_type'],
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            )
        );

        foreach ( $posts as $post_id ) {
            collective_finity_fix_legacy_elementor_template_types( $post_id );
        }
    }

    collective_finity_sync_elementor_cpt_support_option();
    flush_rewrite_rules( false );
    update_option( 'cf_elementor_templates_migrated', 1 );
}
add_action( 'admin_init', 'collective_finity_migrate_existing_template_posts_for_elementor' );

/**
 * Admin list table columns.
 */
function collective_finity_template_columns( $columns ) {
    $new = array();
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;
        if ( 'title' === $key ) {
            $new['cf_elementor'] = __( 'Elementor', 'collective-finity' );
            $new['cf_active']    = __( 'Status', 'collective-finity' );
        }
    }
    return $new;
}

/**
 * Render custom admin columns.
 */
function collective_finity_template_column_content( $column, $post_id ) {
    $post = get_post( $post_id );
    if ( ! $post ) {
        return;
    }

    $part = collective_finity_get_part_by_post_type( $post->post_type );
    if ( ! $part ) {
        return;
    }

    if ( 'cf_elementor' === $column ) {
        if ( collective_finity_is_elementor_active() ) {
            echo '<a class="button button-small" href="' . esc_url( collective_finity_elementor_edit_url( $post_id ) ) . '">';
            esc_html_e( 'Edit with Elementor', 'collective-finity' );
            echo '</a>';
        } else {
            esc_html_e( 'Elementor inactive', 'collective-finity' );
        }
    }

    if ( 'cf_active' === $column ) {
        $active_id = collective_finity_get_theme_part_template_id( $part );
        if ( (int) $active_id === (int) $post_id ) {
            echo '<span class="cf-badge cf-badge--active">' . esc_html__( 'Active site-wide', 'collective-finity' ) . '</span>';
        } else {
            echo '<span class="cf-badge">' . esc_html__( 'Draft layout', 'collective-finity' ) . '</span>';
        }
    }
}

foreach ( array( 'cf_header', 'cf_footer', 'cf_sidebar' ) as $cpt ) {
    add_filter( 'manage_' . $cpt . '_posts_columns', 'collective_finity_template_columns' );
    add_action( 'manage_' . $cpt . '_posts_custom_column', 'collective_finity_template_column_content', 10, 2 );
}

/**
 * Row action: set as active site-wide template.
 */
function collective_finity_template_row_actions( $actions, $post ) {
    $part = collective_finity_get_part_by_post_type( $post->post_type );
    if ( ! $part || ! current_user_can( 'edit_theme_options' ) ) {
        return $actions;
    }

    $active_id = collective_finity_get_theme_part_template_id( $part );
    if ( (int) $active_id !== (int) $post->ID && 'publish' === $post->post_status ) {
        $url = wp_nonce_url(
            add_query_arg(
                array(
                    'cf_set_active'  => $part,
                    'cf_template_id' => $post->ID,
                ),
                admin_url( 'edit.php?post_type=' . $post->post_type )
            ),
            'cf_set_active_' . $part . '_' . $post->ID
        );

        $actions['cf_set_active'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Set as Active', 'collective-finity' ) . '</a>';
    }

    if ( collective_finity_is_elementor_active() ) {
        $actions['cf_elementor'] = '<a href="' . esc_url( collective_finity_elementor_edit_url( $post->ID ) ) . '">' . esc_html__( 'Edit with Elementor', 'collective-finity' ) . '</a>';
    }

    return $actions;
}
add_filter( 'post_row_actions', 'collective_finity_template_row_actions', 10, 2 );

/**
 * Handle set-active action from list table.
 */
function collective_finity_handle_set_active_template() {
    if ( empty( $_GET['cf_set_active'] ) || empty( $_GET['cf_template_id'] ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    $part        = sanitize_key( wp_unslash( $_GET['cf_set_active'] ) );
    $template_id = absint( $_GET['cf_template_id'] );

    check_admin_referer( 'cf_set_active_' . $part . '_' . $template_id );

    collective_finity_set_theme_part_template_id( $part, $template_id );

    $parts     = collective_finity_get_theme_parts();
    $post_type = isset( $parts[ $part ]['post_type'] ) ? $parts[ $part ]['post_type'] : 'cf_header';

    wp_safe_redirect(
        add_query_arg(
            array(
                'post_type' => $post_type,
                'cf_active' => '1',
            ),
            admin_url( 'edit.php' )
        )
    );
    exit;
}
add_action( 'admin_init', 'collective_finity_handle_set_active_template' );

/**
 * Admin notices.
 */
function collective_finity_template_admin_notices() {
    if ( ! empty( $_GET['cf_active'] ) ) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Template set as active site-wide.', 'collective-finity' ) . '</p></div>';
    }
}
add_action( 'admin_notices', 'collective_finity_template_admin_notices' );

/**
 * Admin styles for template list tables.
 */
function collective_finity_template_admin_styles( $hook ) {
    if ( false === strpos( $hook, 'cf_header' ) && false === strpos( $hook, 'cf_footer' ) && false === strpos( $hook, 'cf_sidebar' ) ) {
        return;
    }

    wp_enqueue_style(
        'cf-theme-builder-admin',
        get_template_directory_uri() . '/assets/css/theme-builder-admin.css',
        array(),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'admin_enqueue_scripts', 'collective_finity_template_admin_styles' );

/**
 * Flush rewrite rules after theme switch so CPT menus work cleanly.
 */
function collective_finity_flush_rewrite_on_switch() {
    collective_finity_register_theme_template_cpts();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'collective_finity_flush_rewrite_on_switch' );
