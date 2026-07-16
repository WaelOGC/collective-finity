<?php
/**
 * Customizer integration for Theme Options (General, Music Player, Ad Manager, Advanced).
 *
 * Settings map to the same `cf_theme_options` option used by the dashboard Theme Options page.
 * Header / Footer / Sidebar Theme Parts remain in customizer-theme-parts.php.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Build a Customizer setting ID for a top-level theme option key.
 *
 * @param string $key Option array key.
 * @return string
 */
function collective_finity_customize_option_setting_id( $key ) {
    return collective_finity_theme_options_key() . '[' . $key . ']';
}

/**
 * Build a Customizer setting ID for a nested ad zone field.
 *
 * @param string $zone_id Zone ID.
 * @param string $field   Zone field key.
 * @return string
 */
function collective_finity_customize_ad_zone_setting_id( $zone_id, $field ) {
    return collective_finity_theme_options_key() . '[ad_zones][' . $zone_id . '][' . $field . ']';
}

/**
 * Sanitize checkbox-style 0/1 theme options.
 *
 * @param mixed $value Raw value.
 * @return int
 */
function collective_finity_customize_sanitize_checkbox( $value ) {
    return empty( $value ) ? 0 : 1;
}

/**
 * Sanitize a font preset key.
 *
 * @param string $value Raw value.
 * @return string
 */
function collective_finity_customize_sanitize_font( $value ) {
    $fonts = collective_finity_get_font_choices();
    $value = sanitize_key( $value );
    return isset( $fonts[ $value ] ) ? $value : 'inter';
}

/**
 * Sanitize heading font weight.
 *
 * @param string $value Raw value.
 * @return string
 */
function collective_finity_customize_sanitize_heading_weight( $value ) {
    $allowed = array( '400', '500', '600', '700', '800' );
    $value   = sanitize_key( $value );
    return in_array( $value, $allowed, true ) ? $value : '700';
}

/**
 * Sanitize enum against an allow-list.
 *
 * @param string   $value   Raw value.
 * @param string[] $allowed Allowed keys.
 * @param string   $fallback Fallback.
 * @return string
 */
function collective_finity_customize_sanitize_choice( $value, $allowed, $fallback ) {
    $value = sanitize_key( $value );
    return in_array( $value, $allowed, true ) ? $value : $fallback;
}

/**
 * Sanitize AdSense publisher ID.
 *
 * @param string $value Raw value.
 * @return string
 */
function collective_finity_customize_sanitize_adsense_publisher( $value ) {
    $value = sanitize_text_field( $value );
    return ( $value && preg_match( '/^ca-pub-\d+$/', $value ) ) ? $value : '';
}

/**
 * Sanitize ad zone HTML/JS code.
 *
 * @param string $value Raw value.
 * @return string
 */
function collective_finity_customize_sanitize_ad_code( $value ) {
    if ( current_user_can( 'unfiltered_html' ) ) {
        return (string) $value;
    }
    return wp_kses_post( $value );
}

/**
 * Register a theme-option setting + control helper.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @param string               $key         Option key.
 * @param array                $setting     Setting args (merged with defaults).
 * @param array                $control     Control args (must include label + section; type defaults to text).
 */
function collective_finity_customize_add_theme_option( $wp_customize, $key, $setting, $control ) {
    $defaults   = collective_finity_default_theme_options();
    $setting_id = collective_finity_customize_option_setting_id( $key );

    $wp_customize->add_setting(
        $setting_id,
        wp_parse_args(
            $setting,
            array(
                'default'           => $defaults[ $key ] ?? '',
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'transport'         => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            )
        )
    );

    $control_type = $control['type'] ?? 'text';
    unset( $control['type'] );

    if ( 'color' === $control_type ) {
        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize,
                $setting_id,
                wp_parse_args(
                    $control,
                    array(
                        'settings' => $setting_id,
                    )
                )
            )
        );
        return;
    }

    if ( 'media' === $control_type ) {
        $wp_customize->add_control(
            new WP_Customize_Media_Control(
                $wp_customize,
                $setting_id,
                wp_parse_args(
                    $control,
                    array(
                        'settings'  => $setting_id,
                        'mime_type' => 'image',
                    )
                )
            )
        );
        return;
    }

    $wp_customize->add_control(
        $setting_id,
        wp_parse_args(
            $control,
            array(
                'settings' => $setting_id,
                'type'     => $control_type,
            )
        )
    );
}

/**
 * Register Theme Options panels, sections, and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function collective_finity_customize_register_theme_options( $wp_customize ) {
    $fonts        = collective_finity_get_font_choices();
    $font_choices = array();
    foreach ( $fonts as $font_key => $font ) {
        $font_choices[ $font_key ] = $font['label'];
    }

    $options_link = sprintf(
        '<a href="%s">%s</a>',
        esc_url( admin_url( 'admin.php?page=collective-finity-options' ) ),
        esc_html__( 'Theme Options dashboard', 'collective-finity' )
    );

    $wp_customize->add_panel(
        'cf_general_settings',
        array(
            'title'       => __( 'General Settings', 'collective-finity' ),
            'description' => sprintf(
                /* translators: %s: link to Theme Options dashboard */
                __( 'Same settings as the General tab on the %s page. Changes stay in sync.', 'collective-finity' ),
                $options_link
            ),
            'priority'    => 25,
        )
    );

    $general_sections = array(
        'cf_general_colors'     => __( 'Colors', 'collective-finity' ),
        'cf_general_logo'       => __( 'Logo', 'collective-finity' ),
        'cf_general_typography' => __( 'Typography', 'collective-finity' ),
        'cf_general_buttons'    => __( 'Buttons', 'collective-finity' ),
        'cf_general_cards'      => __( 'Cards', 'collective-finity' ),
        'cf_general_effects'    => __( 'Effects', 'collective-finity' ),
        'cf_general_spacing'    => __( 'Spacing', 'collective-finity' ),
        'cf_general_behavior'   => __( 'Behavior', 'collective-finity' ),
    );

    foreach ( $general_sections as $section_id => $title ) {
        $wp_customize->add_section(
            $section_id,
            array(
                'title' => $title,
                'panel' => 'cf_general_settings',
            )
        );
    }

    $wp_customize->add_section(
        'cf_music_player',
        array(
            'title'       => __( 'Music Player', 'collective-finity' ),
            'description' => sprintf(
                /* translators: %s: link to Theme Options dashboard */
                __( 'Same settings as the Music Player tab on the %s page.', 'collective-finity' ),
                $options_link
            ),
            'priority'    => 26,
        )
    );

    $wp_customize->add_section(
        'cf_ad_manager',
        array(
            'title'       => __( 'Ad Manager', 'collective-finity' ),
            'description' => sprintf(
                /* translators: %s: link to Theme Options dashboard */
                __( 'Same settings as the Ad Manager tab on the %s page. Ads are never shown on Home, Privacy, Terms, or Contact.', 'collective-finity' ),
                $options_link
            ),
            'priority'    => 27,
        )
    );

    $wp_customize->add_section(
        'cf_advanced',
        array(
            'title'       => __( 'Advanced', 'collective-finity' ),
            'description' => sprintf(
                /* translators: %s: link to Theme Options dashboard */
                __( 'Same settings as the Advanced tab on the %s page.', 'collective-finity' ),
                $options_link
            ),
            'priority'    => 28,
        )
    );

    // ——— Colors (live preview) ———
    $color_fields = array(
        'primary_color'    => array(
            'label'       => __( 'Primary Color', 'collective-finity' ),
            'description' => __( 'Brand accent used across the sidebar, footer, buttons, and active nav item.', 'collective-finity' ),
        ),
        'accent_color'     => array(
            'label'       => __( 'Dark Accent Color', 'collective-finity' ),
            'description' => __( 'Dark base tone used for the site background and side panels.', 'collective-finity' ),
        ),
        'text_color'       => array( 'label' => __( 'Text Color', 'collective-finity' ) ),
        'text_muted_color' => array( 'label' => __( 'Muted Text Color', 'collective-finity' ) ),
        'card_bg_color'    => array( 'label' => __( 'Card Background', 'collective-finity' ) ),
        'border_color'     => array( 'label' => __( 'Border Color', 'collective-finity' ) ),
        'link_color'       => array( 'label' => __( 'Link Color', 'collective-finity' ) ),
        'link_hover_color' => array( 'label' => __( 'Link Hover Color', 'collective-finity' ) ),
    );

    foreach ( $color_fields as $key => $field ) {
        $default_color = collective_finity_default_theme_options()[ $key ];
        collective_finity_customize_add_theme_option(
            $wp_customize,
            $key,
            array(
                'sanitize_callback' => function ( $value ) use ( $default_color ) {
                    $color = sanitize_hex_color( $value );
                    return $color ? $color : $default_color;
                },
                'transport'         => 'postMessage',
            ),
            array_merge(
                $field,
                array(
                    'section' => 'cf_general_colors',
                    'type'    => 'color',
                )
            )
        );
    }

    // ——— Logo ———
    collective_finity_customize_add_theme_option(
        $wp_customize,
        'sidebar_logo',
        array(
            'default'           => 0,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ),
        array(
            'label'       => __( 'Sidebar / Header Logo', 'collective-finity' ),
            'description' => __( 'Replaces the diamond mark next to the brand name in the desktop sidebar. Leave empty to use the default diamond icon.', 'collective-finity' ),
            'section'     => 'cf_general_logo',
            'type'        => 'media',
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'sidebar_logo_size',
        array(
            'sanitize_callback' => function ( $value ) {
                return min( 120, max( 16, absint( $value ) ) );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'Sidebar Logo Size (px)', 'collective-finity' ),
            'section'     => 'cf_general_logo',
            'type'        => 'number',
            'input_attrs' => array(
                'min' => 16,
                'max' => 120,
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'mobile_logo',
        array(
            'default'           => 0,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ),
        array(
            'label'       => __( 'Mobile / Tablet Logo Override', 'collective-finity' ),
            'description' => __( 'Optional. Shown in the mobile/tablet top bar. Falls back to the sidebar logo, then the diamond icon.', 'collective-finity' ),
            'section'     => 'cf_general_logo',
            'type'        => 'media',
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'mobile_logo_size',
        array(
            'sanitize_callback' => function ( $value ) {
                return min( 120, max( 16, absint( $value ) ) );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'Mobile Logo Size (px)', 'collective-finity' ),
            'section'     => 'cf_general_logo',
            'type'        => 'number',
            'input_attrs' => array(
                'min' => 16,
                'max' => 120,
            ),
        )
    );

    // ——— Typography ———
    collective_finity_customize_add_theme_option(
        $wp_customize,
        'body_font',
        array(
            'sanitize_callback' => 'collective_finity_customize_sanitize_font',
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'Body Font', 'collective-finity' ),
            'description' => __( 'Base font for body text and navigation (default: Inter).', 'collective-finity' ),
            'section'     => 'cf_general_typography',
            'type'        => 'select',
            'choices'     => $font_choices,
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'heading_font',
        array(
            'sanitize_callback' => function ( $value ) {
                $fonts = collective_finity_get_font_choices();
                $value = sanitize_key( $value );
                return isset( $fonts[ $value ] ) ? $value : 'space-mono';
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'Accent / Heading Font', 'collective-finity' ),
            'description' => __( 'Monospace/accent font for the wordmark, eyebrows, and labels (default: Space Mono).', 'collective-finity' ),
            'section'     => 'cf_general_typography',
            'type'        => 'select',
            'choices'     => $font_choices,
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'base_font_size',
        array(
            'sanitize_callback' => function ( $value ) {
                return min( 20, max( 13, absint( $value ) ) );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'Base Font Size (px)', 'collective-finity' ),
            'section'     => 'cf_general_typography',
            'type'        => 'number',
            'input_attrs' => array(
                'min' => 13,
                'max' => 20,
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'h1_font_size',
        array(
            'sanitize_callback' => function ( $value ) {
                return min( 72, max( 20, absint( $value ) ) );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'H1 Font Size (px)', 'collective-finity' ),
            'section'     => 'cf_general_typography',
            'type'        => 'number',
            'input_attrs' => array(
                'min' => 20,
                'max' => 72,
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'h2_font_size',
        array(
            'sanitize_callback' => function ( $value ) {
                return min( 56, max( 18, absint( $value ) ) );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'H2 Font Size (px)', 'collective-finity' ),
            'section'     => 'cf_general_typography',
            'type'        => 'number',
            'input_attrs' => array(
                'min' => 18,
                'max' => 56,
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'h3_font_size',
        array(
            'sanitize_callback' => function ( $value ) {
                return min( 40, max( 16, absint( $value ) ) );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'H3 Font Size (px)', 'collective-finity' ),
            'section'     => 'cf_general_typography',
            'type'        => 'number',
            'input_attrs' => array(
                'min' => 16,
                'max' => 40,
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'heading_font_weight',
        array(
            'sanitize_callback' => 'collective_finity_customize_sanitize_heading_weight',
            'transport'         => 'postMessage',
        ),
        array(
            'label'   => __( 'Heading Font Weight', 'collective-finity' ),
            'section' => 'cf_general_typography',
            'type'    => 'select',
            'choices' => array(
                '400' => '400',
                '500' => '500',
                '600' => '600',
                '700' => '700',
                '800' => '800',
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'heading_letter_spacing',
        array(
            'sanitize_callback' => function ( $value ) {
                return min( 0.15, max( 0, round( floatval( $value ), 2 ) ) );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'Heading Letter Spacing (em)', 'collective-finity' ),
            'section'     => 'cf_general_typography',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 0,
                'max'  => 0.15,
                'step' => 0.01,
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'body_line_height',
        array(
            'sanitize_callback' => function ( $value ) {
                return min( 2.0, max( 1.2, round( floatval( $value ), 2 ) ) );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'Body Line Height', 'collective-finity' ),
            'section'     => 'cf_general_typography',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 1.2,
                'max'  => 2.0,
                'step' => 0.05,
            ),
        )
    );

    // ——— Buttons ———
    collective_finity_customize_add_theme_option(
        $wp_customize,
        'button_radius',
        array(
            'sanitize_callback' => function ( $value ) {
                return min( 40, max( 0, absint( $value ) ) );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'Button Radius (px)', 'collective-finity' ),
            'section'     => 'cf_general_buttons',
            'type'        => 'number',
            'input_attrs' => array(
                'min' => 0,
                'max' => 40,
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'button_size',
        array(
            'sanitize_callback' => function ( $value ) {
                return collective_finity_customize_sanitize_choice( $value, array( 'compact', 'regular', 'large' ), 'regular' );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'   => __( 'Button Size', 'collective-finity' ),
            'section' => 'cf_general_buttons',
            'type'    => 'select',
            'choices' => array(
                'compact' => __( 'Compact', 'collective-finity' ),
                'regular' => __( 'Regular', 'collective-finity' ),
                'large'   => __( 'Large', 'collective-finity' ),
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'button_hover_effect',
        array(
            'sanitize_callback' => function ( $value ) {
                return collective_finity_customize_sanitize_choice( $value, array( 'none', 'brighten', 'scale', 'lift' ), 'brighten' );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'   => __( 'Button Hover Effect', 'collective-finity' ),
            'section' => 'cf_general_buttons',
            'type'    => 'select',
            'choices' => array(
                'none'     => __( 'None', 'collective-finity' ),
                'brighten' => __( 'Brighten', 'collective-finity' ),
                'scale'    => __( 'Scale', 'collective-finity' ),
                'lift'     => __( 'Lift', 'collective-finity' ),
            ),
        )
    );

    // ——— Cards ———
    collective_finity_customize_add_theme_option(
        $wp_customize,
        'card_radius',
        array(
            'sanitize_callback' => function ( $value ) {
                return min( 32, max( 0, absint( $value ) ) );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'Card Radius (px)', 'collective-finity' ),
            'section'     => 'cf_general_cards',
            'type'        => 'number',
            'input_attrs' => array(
                'min' => 0,
                'max' => 32,
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'card_border_width',
        array(
            'sanitize_callback' => function ( $value ) {
                return min( 4, max( 0, absint( $value ) ) );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'Card Border Width (px)', 'collective-finity' ),
            'section'     => 'cf_general_cards',
            'type'        => 'number',
            'input_attrs' => array(
                'min' => 0,
                'max' => 4,
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'card_hover_effect',
        array(
            'sanitize_callback' => function ( $value ) {
                return collective_finity_customize_sanitize_choice( $value, array( 'none', 'lift', 'glow', 'scale' ), 'lift' );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'   => __( 'Card Hover Effect', 'collective-finity' ),
            'section' => 'cf_general_cards',
            'type'    => 'select',
            'choices' => array(
                'none'  => __( 'None', 'collective-finity' ),
                'lift'  => __( 'Lift', 'collective-finity' ),
                'glow'  => __( 'Glow', 'collective-finity' ),
                'scale' => __( 'Scale', 'collective-finity' ),
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'card_shadow',
        array(
            'sanitize_callback' => function ( $value ) {
                return collective_finity_customize_sanitize_choice( $value, array( 'none', 'soft', 'strong' ), 'soft' );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'   => __( 'Card Shadow', 'collective-finity' ),
            'section' => 'cf_general_cards',
            'type'    => 'select',
            'choices' => array(
                'none'   => __( 'None', 'collective-finity' ),
                'soft'   => __( 'Soft', 'collective-finity' ),
                'strong' => __( 'Strong', 'collective-finity' ),
            ),
        )
    );

    // ——— Effects ———
    collective_finity_customize_add_theme_option(
        $wp_customize,
        'transition_speed',
        array(
            'sanitize_callback' => function ( $value ) {
                return collective_finity_customize_sanitize_choice( $value, array( 'fast', 'normal', 'slow' ), 'normal' );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'   => __( 'Transition Speed', 'collective-finity' ),
            'section' => 'cf_general_effects',
            'type'    => 'select',
            'choices' => array(
                'fast'   => __( 'Fast', 'collective-finity' ),
                'normal' => __( 'Normal', 'collective-finity' ),
                'slow'   => __( 'Slow', 'collective-finity' ),
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'enable_glow_effects',
        array(
            'sanitize_callback' => 'collective_finity_customize_sanitize_checkbox',
            'transport'         => 'postMessage',
        ),
        array(
            'label'   => __( 'Enable radial gradient glow effects', 'collective-finity' ),
            'section' => 'cf_general_effects',
            'type'    => 'checkbox',
        )
    );

    // ——— Spacing ———
    collective_finity_customize_add_theme_option(
        $wp_customize,
        'section_spacing',
        array(
            'sanitize_callback' => function ( $value ) {
                return collective_finity_customize_sanitize_choice( $value, array( 'compact', 'default', 'spacious' ), 'default' );
            },
            'transport'         => 'postMessage',
        ),
        array(
            'label'       => __( 'Section Spacing', 'collective-finity' ),
            'description' => __( 'Vertical gap between major page sections.', 'collective-finity' ),
            'section'     => 'cf_general_spacing',
            'type'        => 'select',
            'choices'     => array(
                'compact'  => __( 'Compact', 'collective-finity' ),
                'default'  => __( 'Default', 'collective-finity' ),
                'spacious' => __( 'Spacious', 'collective-finity' ),
            ),
        )
    );

    // ——— Behavior ———
    collective_finity_customize_add_theme_option(
        $wp_customize,
        'enable_preloader',
        array(
            'sanitize_callback' => 'collective_finity_customize_sanitize_checkbox',
            'transport'         => 'refresh',
        ),
        array(
            'label'   => __( 'Enable site preloader', 'collective-finity' ),
            'section' => 'cf_general_behavior',
            'type'    => 'checkbox',
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'enable_back_to_top',
        array(
            'sanitize_callback' => 'collective_finity_customize_sanitize_checkbox',
            'transport'         => 'refresh',
        ),
        array(
            'label'   => __( 'Show back-to-top button', 'collective-finity' ),
            'section' => 'cf_general_behavior',
            'type'    => 'checkbox',
        )
    );

    // ——— Music Player ———
    collective_finity_customize_add_theme_option(
        $wp_customize,
        'show_global_player',
        array(
            'sanitize_callback' => 'collective_finity_customize_sanitize_checkbox',
            'transport'         => 'postMessage',
        ),
        array(
            'label'   => __( 'Show sticky global audio player', 'collective-finity' ),
            'section' => 'cf_music_player',
            'type'    => 'checkbox',
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'default_volume',
        array(
            'sanitize_callback' => function ( $value ) {
                return min( 100, max( 0, absint( $value ) ) );
            },
            'transport'         => 'refresh',
        ),
        array(
            'label'       => __( 'Default Volume', 'collective-finity' ),
            'section'     => 'cf_music_player',
            'type'        => 'number',
            'input_attrs' => array(
                'min' => 0,
                'max' => 100,
            ),
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'popular_min_views',
        array(
            'sanitize_callback' => function ( $value ) {
                return max( 0, absint( $value ) );
            },
            'transport'         => 'refresh',
        ),
        array(
            'label'       => __( 'Popular Section Minimum Views', 'collective-finity' ),
            'description' => __( 'Tracks need at least this many views to appear in the Popular carousel and Popular archive. Default: 50.', 'collective-finity' ),
            'section'     => 'cf_music_player',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 0,
                'step' => 1,
            ),
        )
    );

    // ——— Ad Manager ———
    collective_finity_customize_add_theme_option(
        $wp_customize,
        'ad_preview_mode',
        array(
            'sanitize_callback' => 'collective_finity_customize_sanitize_checkbox',
            'transport'         => 'refresh',
        ),
        array(
            'label'       => __( 'Preview Mode', 'collective-finity' ),
            'description' => __( 'Show labeled placeholder boxes instead of real ad code on the frontend.', 'collective-finity' ),
            'section'     => 'cf_ad_manager',
            'type'        => 'checkbox',
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'adsense_publisher_id',
        array(
            'sanitize_callback' => 'collective_finity_customize_sanitize_adsense_publisher',
            'transport'         => 'refresh',
        ),
        array(
            'label'       => __( 'AdSense Publisher ID', 'collective-finity' ),
            'description' => __( 'Required for AdSense ad slots. Must match the ca-pub-XXXXXXXXXXXXXXXX format.', 'collective-finity' ),
            'section'     => 'cf_ad_manager',
            'type'        => 'text',
        )
    );

    $zone_labels       = collective_finity_ad_zone_labels();
    $zone_descriptions = collective_finity_ad_zone_descriptions();

    foreach ( collective_finity_default_ad_zones() as $zone_id => $zone_defaults ) {
        $label = $zone_labels[ $zone_id ] ?? $zone_id;
        $desc  = $zone_descriptions[ $zone_id ] ?? '';

        $enabled_id = collective_finity_customize_ad_zone_setting_id( $zone_id, 'enabled' );
        $wp_customize->add_setting(
            $enabled_id,
            array(
                'default'           => $zone_defaults['enabled'],
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'transport'         => 'refresh',
                'sanitize_callback' => 'collective_finity_customize_sanitize_checkbox',
            )
        );
        $wp_customize->add_control(
            $enabled_id,
            array(
                'label'       => sprintf(
                    /* translators: %s: ad zone label */
                    __( '%s — Enabled', 'collective-finity' ),
                    $label
                ),
                'description' => $desc,
                'section'     => 'cf_ad_manager',
                'type'        => 'checkbox',
            )
        );

        if ( isset( $zone_defaults['frequency'] ) ) {
            $freq_id = collective_finity_customize_ad_zone_setting_id( $zone_id, 'frequency' );
            $wp_customize->add_setting(
                $freq_id,
                array(
                    'default'           => $zone_defaults['frequency'],
                    'type'              => 'option',
                    'capability'        => 'edit_theme_options',
                    'transport'         => 'refresh',
                    'sanitize_callback' => function ( $value ) {
                        return min( 50, max( 2, absint( $value ) ) );
                    },
                )
            );
            $wp_customize->add_control(
                $freq_id,
                array(
                    'label'       => sprintf(
                        /* translators: %s: ad zone label */
                        __( '%s — Show every Nth card', 'collective-finity' ),
                        $label
                    ),
                    'section'     => 'cf_ad_manager',
                    'type'        => 'number',
                    'input_attrs' => array(
                        'min' => 2,
                        'max' => 50,
                    ),
                )
            );
        }

        $slot_id = collective_finity_customize_ad_zone_setting_id( $zone_id, 'adsense_slot_id' );
        $wp_customize->add_setting(
            $slot_id,
            array(
                'default'           => '',
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'transport'         => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            )
        );
        $wp_customize->add_control(
            $slot_id,
            array(
                'label'   => sprintf(
                    /* translators: %s: ad zone label */
                    __( '%s — AdSense Ad Slot ID', 'collective-finity' ),
                    $label
                ),
                'section' => 'cf_ad_manager',
                'type'    => 'text',
            )
        );

        $code_id = collective_finity_customize_ad_zone_setting_id( $zone_id, 'code' );
        $wp_customize->add_setting(
            $code_id,
            array(
                'default'           => '',
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'transport'         => 'refresh',
                'sanitize_callback' => 'collective_finity_customize_sanitize_ad_code',
            )
        );
        $wp_customize->add_control(
            $code_id,
            array(
                'label'       => sprintf(
                    /* translators: %s: ad zone label */
                    __( '%s — Ad code (HTML / JavaScript)', 'collective-finity' ),
                    $label
                ),
                'section'     => 'cf_ad_manager',
                'type'        => 'textarea',
                'input_attrs' => array(
                    'rows' => 6,
                ),
            )
        );
    }

    // ——— Advanced ———
    collective_finity_customize_add_theme_option(
        $wp_customize,
        'footer_copyright',
        array(
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ),
        array(
            'label'   => __( 'Footer Copyright Override', 'collective-finity' ),
            'section' => 'cf_advanced',
            'type'    => 'text',
        )
    );

    collective_finity_customize_add_theme_option(
        $wp_customize,
        'custom_css',
        array(
            'sanitize_callback' => function ( $value ) {
                return wp_strip_all_tags( $value );
            },
            'transport'         => 'refresh',
        ),
        array(
            'label'       => __( 'Custom CSS', 'collective-finity' ),
            'section'     => 'cf_advanced',
            'type'        => 'textarea',
            'input_attrs' => array(
                'rows'  => 10,
                'class' => 'code',
            ),
        )
    );
}
add_action( 'customize_register', 'collective_finity_customize_register_theme_options' );

/**
 * Register Donate page Lead Screen controls in the Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function collective_finity_customize_register_donate_leadscreen( $wp_customize ) {
    $option_key = collective_finity_theme_options_key();
    $defaults   = collective_finity_default_theme_options();

    $wp_customize->add_section(
        'cf_donate_leadscreen',
        array(
            'title'    => __( 'Donate Page — Lead Screen', 'collective-finity' ),
            'priority' => 160,
        )
    );

    for ( $i = 0; $i < 5; $i++ ) {
        $setting_id = $option_key . '[donate_leadscreen_messages][' . $i . ']';
        $wp_customize->add_setting(
            $setting_id,
            array(
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'default'           => $defaults['donate_leadscreen_messages'][ $i ] ?? '',
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'refresh',
            )
        );
        $wp_customize->add_control(
            'cf_leadscreen_msg_' . $i,
            array(
                'label'    => sprintf( __( 'Message %d', 'collective-finity' ), $i + 1 ),
                'section'  => 'cf_donate_leadscreen',
                'settings' => $setting_id,
                'type'     => 'text',
            )
        );
    }

    $wp_customize->add_setting(
        $option_key . '[donate_leadscreen_animation]',
        array(
            'type'              => 'option',
            'capability'        => 'edit_theme_options',
            'default'           => $defaults['donate_leadscreen_animation'],
            'sanitize_callback' => function ( $value ) {
                return in_array( $value, collective_finity_donate_leadscreen_animations(), true ) ? $value : 'scroll';
            },
            'transport'         => 'refresh',
        )
    );
    $wp_customize->add_control(
        'cf_leadscreen_animation',
        array(
            'label'    => __( 'Animation Type', 'collective-finity' ),
            'section'  => 'cf_donate_leadscreen',
            'settings' => $option_key . '[donate_leadscreen_animation]',
            'type'     => 'select',
            'choices'  => array(
                'scroll'       => __( 'Scrolling Ticker', 'collective-finity' ),
                'fade'         => __( 'Fade Cycle', 'collective-finity' ),
                'typewriter'   => __( 'Typewriter', 'collective-finity' ),
                'slide-up'     => __( 'Slide Up Carousel', 'collective-finity' ),
                'glitch'       => __( 'Glitch Flicker', 'collective-finity' ),
                'zoom-pulse'   => __( 'Zoom Pulse', 'collective-finity' ),
                'flip'         => __( 'Flip Card', 'collective-finity' ),
                'wave'         => __( 'Wave Bounce', 'collective-finity' ),
                'neon-flicker' => __( 'Neon Sign Flicker', 'collective-finity' ),
                'blur-focus'   => __( 'Blur Focus Reveal', 'collective-finity' ),
            ),
        )
    );

    $wp_customize->add_setting(
        $option_key . '[donate_leadscreen_position]',
        array(
            'type'              => 'option',
            'capability'        => 'edit_theme_options',
            'default'           => $defaults['donate_leadscreen_position'],
            'sanitize_callback' => function ( $value ) {
                return in_array( $value, array( 'top', 'middle', 'bottom' ), true ) ? $value : 'middle';
            },
            'transport'         => 'refresh',
        )
    );
    $wp_customize->add_control(
        'cf_leadscreen_position',
        array(
            'label'    => __( 'Text Position', 'collective-finity' ),
            'section'  => 'cf_donate_leadscreen',
            'settings' => $option_key . '[donate_leadscreen_position]',
            'type'     => 'select',
            'choices'  => array(
                'top'    => __( 'Top', 'collective-finity' ),
                'middle' => __( 'Middle', 'collective-finity' ),
                'bottom' => __( 'Bottom', 'collective-finity' ),
            ),
        )
    );
}
add_action( 'customize_register', 'collective_finity_customize_register_donate_leadscreen' );

/**
 * Enqueue Customizer preview script for live option updates.
 */
function collective_finity_customize_preview_enqueue() {
    $fonts     = collective_finity_get_font_choices();
    $font_data = array();
    foreach ( $fonts as $key => $font ) {
        $font_data[ $key ] = array(
            'stack'  => $font['stack'],
            'google' => $font['google'],
        );
    }

    wp_enqueue_script(
        'cf-customizer-preview',
        get_template_directory_uri() . '/assets/js/customizer-preview.js',
        array( 'customize-preview', 'jquery' ),
        wp_get_theme()->get( 'Version' ),
        true
    );

    wp_localize_script(
        'cf-customizer-preview',
        'cfCustomizerPreview',
        array(
            'optionKey' => collective_finity_theme_options_key(),
            'fonts'     => $font_data,
            'buttonPadding' => array(
                'compact' => collective_finity_get_button_size_padding( 'compact' ),
                'regular' => collective_finity_get_button_size_padding( 'regular' ),
                'large'   => collective_finity_get_button_size_padding( 'large' ),
            ),
            'cardShadows' => array(
                'none'   => collective_finity_get_card_shadow_value( 'none' ),
                'soft'   => collective_finity_get_card_shadow_value( 'soft' ),
                'strong' => collective_finity_get_card_shadow_value( 'strong' ),
            ),
            'transitions' => array(
                'fast'   => collective_finity_get_transition_speed_value( 'fast' ),
                'normal' => collective_finity_get_transition_speed_value( 'normal' ),
                'slow'   => collective_finity_get_transition_speed_value( 'slow' ),
            ),
            'sectionGaps' => array(
                'compact'  => collective_finity_get_section_gap_value( 'compact' ),
                'default'  => collective_finity_get_section_gap_value( 'default' ),
                'spacious' => collective_finity_get_section_gap_value( 'spacious' ),
            ),
        )
    );
}
add_action( 'customize_preview_init', 'collective_finity_customize_preview_enqueue' );
