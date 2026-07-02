<?php
/**
 * Theme Parts API — Header, Footer, Sidebar.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registered theme part definitions.
 *
 * @return array<string, array<string, mixed>>
 */
function collective_finity_get_theme_parts() {
    return array(
        'header'  => array(
            'label'          => __( 'Header', 'collective-finity' ),
            'settings_tab'   => __( 'Header Settings', 'collective-finity' ),
            'menu_name'      => __( 'Header Layout', 'collective-finity' ),
            'singular'       => __( 'Header Layout', 'collective-finity' ),
            'add_new'          => __( 'Add New Header Layout', 'collective-finity' ),
            'add_new_item'     => __( 'Add New Header Layout', 'collective-finity' ),
            'edit_item'        => __( 'Edit Header Layout', 'collective-finity' ),
            'new_item'         => __( 'New Header Layout', 'collective-finity' ),
            'view_item'        => __( 'View Header Layout', 'collective-finity' ),
            'search_items'     => __( 'Search Header Layouts', 'collective-finity' ),
            'not_found'        => __( 'No header layouts found.', 'collective-finity' ),
            'not_found_in_trash' => __( 'No header layouts found in Trash.', 'collective-finity' ),
            'all_items'        => __( 'All Header Layouts', 'collective-finity' ),
            'post_type'      => 'cf_header',
            'location'       => 'header',
            'type'           => 'header',
            'menu_icon'      => 'dashicons-align-full-width',
            'menu_position'  => 59,
        ),
        'footer'  => array(
            'label'          => __( 'Footer', 'collective-finity' ),
            'settings_tab'   => __( 'Footer Settings', 'collective-finity' ),
            'menu_name'      => __( 'Footer Layout', 'collective-finity' ),
            'singular'       => __( 'Footer Layout', 'collective-finity' ),
            'add_new'          => __( 'Add New Footer Layout', 'collective-finity' ),
            'add_new_item'     => __( 'Add New Footer Layout', 'collective-finity' ),
            'edit_item'        => __( 'Edit Footer Layout', 'collective-finity' ),
            'new_item'         => __( 'New Footer Layout', 'collective-finity' ),
            'view_item'        => __( 'View Footer Layout', 'collective-finity' ),
            'search_items'     => __( 'Search Footer Layouts', 'collective-finity' ),
            'not_found'        => __( 'No footer layouts found.', 'collective-finity' ),
            'not_found_in_trash' => __( 'No footer layouts found in Trash.', 'collective-finity' ),
            'all_items'        => __( 'All Footer Layouts', 'collective-finity' ),
            'post_type'      => 'cf_footer',
            'location'       => 'footer',
            'type'           => 'footer',
            'menu_icon'      => 'dashicons-align-wide',
            'menu_position'  => 61,
        ),
        'sidebar' => array(
            'label'          => __( 'Sidebar', 'collective-finity' ),
            'settings_tab'   => __( 'Side Panel Settings', 'collective-finity' ),
            'menu_name'      => __( 'Side Panel Layout', 'collective-finity' ),
            'singular'       => __( 'Side Panel Layout', 'collective-finity' ),
            'add_new'          => __( 'Add New Side Panel Layout', 'collective-finity' ),
            'add_new_item'     => __( 'Add New Side Panel Layout', 'collective-finity' ),
            'edit_item'        => __( 'Edit Side Panel Layout', 'collective-finity' ),
            'new_item'         => __( 'New Side Panel Layout', 'collective-finity' ),
            'view_item'        => __( 'View Side Panel Layout', 'collective-finity' ),
            'search_items'     => __( 'Search Side Panel Layouts', 'collective-finity' ),
            'not_found'        => __( 'No side panel layouts found.', 'collective-finity' ),
            'not_found_in_trash' => __( 'No side panel layouts found in Trash.', 'collective-finity' ),
            'all_items'        => __( 'All Side Panel Layouts', 'collective-finity' ),
            'post_type'      => 'cf_sidebar',
            'location'       => 'cf_sidebar',
            'type'           => 'section',
            'menu_icon'      => 'dashicons-menu-alt3',
            'menu_position'  => 60,
        ),
    );
}

/**
 * Resolve part slug from CPT name.
 */
function collective_finity_get_part_by_post_type( $post_type ) {
    foreach ( collective_finity_get_theme_parts() as $slug => $config ) {
        if ( $config['post_type'] === $post_type ) {
            return $slug;
        }
    }
    return '';
}

function collective_finity_theme_part_mod_key( $part ) {
    return 'cf_theme_part_' . sanitize_key( $part );
}

function collective_finity_is_elementor_active() {
    return did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' );
}

function collective_finity_get_theme_part_template_id( $part ) {
    $parts = collective_finity_get_theme_parts();
    if ( ! isset( $parts[ $part ] ) ) {
        return 0;
    }
    return absint( get_theme_mod( collective_finity_theme_part_mod_key( $part ), 0 ) );
}

/**
 * Validate template post for a theme part.
 */
function collective_finity_is_valid_theme_part_post( $post_id, $part ) {
    $post_id = absint( $post_id );
    if ( ! $post_id ) {
        return false;
    }

    $post = get_post( $post_id );
    if ( ! $post || 'publish' !== $post->post_status ) {
        return false;
    }

    $parts = collective_finity_get_theme_parts();
    if ( ! isset( $parts[ $part ] ) ) {
        return false;
    }

    if ( $post->post_type === $parts[ $part ]['post_type'] ) {
        return true;
    }

    // Legacy elementor_library assignments.
    return 'elementor_library' === $post->post_type;
}

function collective_finity_set_theme_part_template_id( $part, $template_id ) {
    $parts = collective_finity_get_theme_parts();
    if ( ! isset( $parts[ $part ] ) ) {
        return false;
    }

    $template_id = absint( $template_id );
    if ( $template_id > 0 && ! collective_finity_is_valid_theme_part_post( $template_id, $part ) ) {
        return false;
    }

    if ( $template_id > 0 ) {
        set_theme_mod( collective_finity_theme_part_mod_key( $part ), $template_id );
    } else {
        remove_theme_mod( collective_finity_theme_part_mod_key( $part ) );
    }

    return true;
}

function collective_finity_register_elementor_locations( $elementor_theme_manager ) {
    if ( method_exists( $elementor_theme_manager, 'register_all_core_location' ) ) {
        $elementor_theme_manager->register_all_core_location();
    } else {
        $elementor_theme_manager->register_location( 'header' );
        $elementor_theme_manager->register_location( 'footer' );
    }

    $elementor_theme_manager->register_location(
        'cf_sidebar',
        array(
            'label'           => __( 'Music Sidebar', 'collective-finity' ),
            'multiple'        => false,
            'edit_in_content' => false,
        )
    );
}
add_action( 'elementor/theme/register_locations', 'collective_finity_register_elementor_locations' );

/**
 * Get all saved templates for a part (from theme CPT).
 *
 * @return array<int, string>
 */
function collective_finity_get_templates_for_part( $part ) {
    $parts = collective_finity_get_theme_parts();
    if ( ! isset( $parts[ $part ] ) ) {
        return array();
    }

    $templates = get_posts(
        array(
            'post_type'      => $parts[ $part ]['post_type'],
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        )
    );

    $choices = array( 0 => __( '— Default theme layout —', 'collective-finity' ) );
    foreach ( $templates as $template ) {
        $choices[ $template->ID ] = $template->post_title;
    }

    return $choices;
}

/** Back-compat alias. */
function collective_finity_get_elementor_templates_for_part( $part ) {
    return collective_finity_get_templates_for_part( $part );
}

function collective_finity_create_theme_part_template( $part ) {
    $parts = collective_finity_get_theme_parts();
    if ( ! isset( $parts[ $part ] ) ) {
        return new WP_Error( 'invalid_part', __( 'Invalid theme part.', 'collective-finity' ) );
    }

    if ( ! collective_finity_is_elementor_active() ) {
        return new WP_Error( 'no_elementor', __( 'Elementor must be installed and active.', 'collective-finity' ) );
    }

    $post_id = wp_insert_post(
        array(
            'post_title'  => sprintf(
                __( '%s Template', 'collective-finity' ),
                $parts[ $part ]['menu_name']
            ) . ' - ' . wp_date( 'Y-m-d H:i' ),
            'post_type'   => $parts[ $part ]['post_type'],
            'post_status' => 'publish',
        ),
        true
    );

    if ( is_wp_error( $post_id ) ) {
        return $post_id;
    }

    update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
    update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
    collective_finity_set_theme_part_template_id( $part, $post_id );

    return $post_id;
}

function collective_finity_elementor_edit_url( $post_id ) {
    return admin_url( 'post.php?post=' . absint( $post_id ) . '&action=elementor' );
}

function collective_finity_customizer_part_url( $part ) {
    return add_query_arg(
        array( 'autofocus[section]' => 'cf_theme_part_' . sanitize_key( $part ) ),
        admin_url( 'customize.php' )
    );
}

function collective_finity_theme_part_widget_area( $part ) {
    $map = array(
        'header'  => 'header-widget-area',
        'footer'  => 'footer-widget-area',
        'sidebar' => 'sidebar-widget-area',
    );
    return isset( $map[ $part ] ) ? $map[ $part ] : '';
}

function collective_finity_widgets_customizer_url( $part ) {
    $area = collective_finity_theme_part_widget_area( $part );
    if ( ! $area ) {
        return admin_url( 'customize.php?autofocus[panel]=widgets' );
    }
    return add_query_arg(
        array( 'autofocus[section]' => 'sidebar-widgets-' . $area ),
        admin_url( 'customize.php' )
    );
}

function collective_finity_render_theme_part( $part, $fallback ) {
    $parts = collective_finity_get_theme_parts();
    if ( ! isset( $parts[ $part ] ) || ! is_callable( $fallback ) ) {
        return;
    }

    $location = $parts[ $part ]['location'];

    if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( $location ) ) {
        return;
    }

    $template_id = collective_finity_get_theme_part_template_id( $part );

    if ( $template_id && collective_finity_is_valid_theme_part_post( $template_id, $part ) && collective_finity_is_elementor_active() ) {
        $content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, true );
        if ( $content ) {
            printf(
                '<div class="cf-theme-part cf-theme-part--%1$s" data-cf-part="%1$s">%2$s</div>',
                esc_attr( $part ),
                $content // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            );
            return;
        }
    }

    call_user_func( $fallback );
}

function collective_finity_theme_part_admin_bar_nodes( $wp_admin_bar ) {
    if ( is_admin() || ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    $wp_admin_bar->add_node( array(
        'id'    => 'cf-theme-parts',
        'title' => __( 'Theme Parts', 'collective-finity' ),
        'href'  => admin_url( 'admin.php?page=collective-finity-options' ),
    ) );

    foreach ( collective_finity_get_theme_parts() as $slug => $config ) {
        $wp_admin_bar->add_node( array(
            'id'     => 'cf-theme-part-' . $slug,
            'parent' => 'cf-theme-parts',
            'title'  => $config['label'],
            'href'   => admin_url( 'edit.php?post_type=' . $config['post_type'] ),
        ) );
    }
}
add_action( 'admin_bar_menu', 'collective_finity_theme_part_admin_bar_nodes', 90 );

/**
 * Frontend helpers: back-to-top script and player default volume.
 */
function collective_finity_frontend_theme_scripts() {
    $volume = absint( collective_finity_get_theme_option( 'default_volume', 72 ) );
    $pct    = $volume / 100;
    wp_add_inline_script(
        'music-player-js',
        'document.addEventListener("DOMContentLoaded",function(){var a=document.getElementById("cf-native-audio-element");if(a){a.volume=' . $pct . ';window.cfLastVolume=' . $pct . ';}var v=document.getElementById("player-volume-fill");if(v){v.style.width="' . $volume . '%";}var b=document.getElementById("cf-back-to-top");if(b){var toggleBtt=function(){if(window.scrollY>320){b.classList.add("is-visible");}else{b.classList.remove("is-visible");}};toggleBtt();window.addEventListener("scroll",toggleBtt,{passive:true});b.addEventListener("click",function(){window.scrollTo({top:0,behavior:"smooth"});});}});'
    );
}
add_action( 'wp_enqueue_scripts', 'collective_finity_frontend_theme_scripts', 30 );
