<?php
/**
 * Customizer integration for Theme Parts.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register customizer panel and sections.
 */
function collective_finity_customize_register_theme_parts( $wp_customize ) {
    $wp_customize->add_panel(
        'cf_theme_parts',
        array(
            'title'       => __( 'Theme Parts', 'collective-finity' ),
            'description' => __( 'Control your Header, Footer, and Sidebar. Assign Elementor templates or manage widget areas.', 'collective-finity' ),
            'priority'    => 30,
        )
    );

    foreach ( collective_finity_get_theme_parts() as $slug => $config ) {
        $section_id = 'cf_theme_part_' . $slug;

        $wp_customize->add_section(
            $section_id,
            array(
                'title'       => $config['label'],
                'panel'       => 'cf_theme_parts',
                'description' => collective_finity_customizer_part_description( $slug, $config ),
            )
        );

        $setting_id = collective_finity_theme_part_mod_key( $slug );

        $wp_customize->add_setting(
            $setting_id,
            array(
                'default'           => 0,
                'sanitize_callback' => 'absint',
                'transport'         => 'refresh',
            )
        );

        $wp_customize->add_control(
            $setting_id,
            array(
                'label'       => __( 'Active Template', 'collective-finity' ),
                'section'     => $section_id,
                'type'        => 'select',
                'choices'     => collective_finity_get_templates_for_part( $slug ),
                'description' => __( 'Choose a saved template for this area, or use the default theme layout.', 'collective-finity' ),
            )
        );
    }
}
add_action( 'customize_register', 'collective_finity_customize_register_theme_parts' );

/**
 * Section descriptions with quick links.
 */
function collective_finity_customizer_part_description( $slug, $config ) {
    $links = array();

    $links[] = sprintf(
        '<a href="%s">%s</a>',
        esc_url( admin_url( 'edit.php?post_type=' . $config['post_type'] ) ),
        esc_html__( 'Manage all templates', 'collective-finity' )
    );

    $links[] = sprintf(
        '<a href="%s">%s</a>',
        esc_url( admin_url( 'admin.php?page=collective-finity-options&tab=' . $slug ) ),
        esc_html__( 'Theme Options', 'collective-finity' )
    );

    if ( collective_finity_is_elementor_active() ) {
        $template_id = collective_finity_get_theme_part_template_id( $slug );
        if ( $template_id ) {
            $links[] = sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
                esc_url( collective_finity_elementor_edit_url( $template_id ) ),
                esc_html__( 'Edit with Elementor', 'collective-finity' )
            );
        }

        $create_url = admin_url( 'post-new.php?post_type=' . $config['post_type'] );

        $links[] = sprintf(
            '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
            esc_url( $create_url ),
            esc_html__( 'Create New Template', 'collective-finity' )
        );
    }

    $links[] = sprintf(
        '<a href="%s">%s</a>',
        esc_url( collective_finity_widgets_customizer_url( $slug ) ),
        esc_html__( 'Manage Widget Area', 'collective-finity' )
    );

    return sprintf(
        /* translators: %s: theme part label */
        __( 'Customize the %s for your site.', 'collective-finity' ),
        esc_html( strtolower( $config['label'] ) )
    ) . '<br>' . implode( ' · ', $links );
}

/**
 * Refresh template choices when customizer loads.
 */
function collective_finity_customize_controls_enqueue_scripts() {
    wp_add_inline_script(
        'customize-controls',
        'wp.customize.section.each(function(section){if(section.id.indexOf("cf_theme_part_")===0){section.expanded.bind(function(){section.notifications.remove("cf_refresh_templates");});}});'
    );
}
add_action( 'customize_controls_enqueue_scripts', 'collective_finity_customize_controls_enqueue_scripts' );
