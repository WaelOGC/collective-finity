<?php
/**
 * Theme Options admin page.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function collective_finity_theme_options_key() {
    return 'cf_theme_options';
}

function collective_finity_default_theme_options() {
    return array(
        'primary_color'      => '#FFB700',
        'accent_color'       => '#0D0D0D',
        'text_color'         => '#FFFFFF',
        'text_muted_color'   => '#B3B3B3',
        'card_bg_color'      => '#141414',
        'border_color'       => '#232323',
        'link_color'         => '#FFB700',
        'link_hover_color'   => '#ffc633',
        'base_font_size'     => 16,
        'h1_font_size'       => 52,
        'h2_font_size'       => 22,
        'h3_font_size'       => 18,
        'heading_font_weight' => '700',
        'heading_letter_spacing' => 0,
        'body_line_height'   => 1.6,
        'button_radius'      => 9,
        'button_size'        => 'regular',
        'button_hover_effect' => 'brighten',
        'card_radius'        => 12,
        'card_border_width'  => 1,
        'card_hover_effect'  => 'lift',
        'card_shadow'        => 'soft',
        'transition_speed'   => 'normal',
        'enable_glow_effects' => 1,
        'section_spacing'    => 'default',
        'sidebar_logo'       => 0,
        'sidebar_logo_size'  => 40,
        'mobile_logo'        => 0,
        'mobile_logo_size'   => 32,
        'adsense_publisher_id' => '',
        'body_font'          => 'inter',
        'heading_font'       => 'space-mono',
        'enable_preloader'   => 0,
        'enable_back_to_top' => 1,
        'show_global_player' => 1,
        'default_volume'     => 72,
        'popular_min_views'  => 50,
        'footer_copyright'   => '',
        'footer_tagline'     => 'Experience Music Beyond Imagination',
        'footer_description' => 'Welcome to Collective Finity — a cinematic world where emotional sound, visual stories and creativity connect in one immersive universe.',
        'footer_logo'        => '',
        'footer_logo_size'   => 40,
        'social_instagram'           => '',
        'social_instagram_community' => '',
        'social_youtube'             => '',
        'social_spotify'             => '',
        'social_facebook'            => '',
        'social_facebook_group'      => '',
        'social_discord'             => '',
        'social_tiktok'              => '',
        'social_soundcloud'          => '',
        'social_amazon'              => '',
        'social_amazon_music'      => '',
        'social_x'                   => '',
        'custom_css'         => '',
        'ad_preview_mode'    => 0,
        'ad_zones'           => collective_finity_default_ad_zones(),
        'donate_leadscreen_messages'  => array(
            'AI Music Guides',
            'Free Listening',
            'Community First',
            'Support Creators',
            'Music Beyond Imagination',
        ),
        'donate_leadscreen_animation' => 'scroll',
        'donate_leadscreen_position'  => 'middle',
    );
}

function collective_finity_get_theme_options() {
    $saved    = get_option( collective_finity_theme_options_key(), array() );
    if ( ! is_array( $saved ) ) {
        $saved = array();
    }
    $defaults = collective_finity_default_theme_options();
    $merged   = wp_parse_args( $saved, $defaults );

    if ( isset( $defaults['ad_zones'] ) ) {
        $merged['ad_zones'] = isset( $saved['ad_zones'] ) && is_array( $saved['ad_zones'] ) ? $saved['ad_zones'] : array();
        $merged['ad_zones'] = wp_parse_args( $merged['ad_zones'], $defaults['ad_zones'] );
        foreach ( $defaults['ad_zones'] as $zone_id => $zone_defaults ) {
            $merged['ad_zones'][ $zone_id ] = wp_parse_args( $merged['ad_zones'][ $zone_id ] ?? array(), $zone_defaults );
        }
    }

    return $merged;
}

function collective_finity_get_theme_option( $key, $default = null ) {
    $options = collective_finity_get_theme_options();
    if ( array_key_exists( $key, $options ) ) {
        return $options[ $key ];
    }
    if ( null !== $default ) {
        return $default;
    }
    $defaults = collective_finity_default_theme_options();
    return isset( $defaults[ $key ] ) ? $defaults[ $key ] : null;
}

/**
 * URLs embedded in the Join Community page editor content (legacy fallback).
 *
 * @return array<string, string>
 */
function collective_finity_get_community_urls_from_page_content() {
    static $cache = null;

    if ( null !== $cache ) {
        return $cache;
    }

    $cache = array();
    $page  = get_page_by_path( 'join-community' );

    if ( ! $page ) {
        return $cache;
    }

    $content = (string) $page->post_content;
    if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
        return $cache;
    }

    $patterns = array(
        'social_discord'             => '/discord\.(gg|com|app)/i',
        'social_facebook'            => '/facebook\.com/i',
        'social_facebook_group'      => '/facebook\.com\/groups/i',
        'social_tiktok'              => '/tiktok\.com/i',
        'social_instagram'           => '/instagram\.com/i',
        'social_instagram_community' => '/instagram\.com/i',
        'social_youtube'             => '/youtube\.com|youtu\.be/i',
        'social_amazon'              => '/music\.amazon|amazon\./i',
        'social_soundcloud'          => '/soundcloud\.com/i',
        'social_spotify'             => '/open\.spotify\.com|spotify\.com/i',
    );

    if ( ! preg_match_all( '/href=["\']([^"\']+)["\']/i', $content, $matches ) ) {
        return $cache;
    }

    $instagram_urls = array();

    foreach ( $matches[1] as $href ) {
        $href = trim( (string) $href );
        if ( ! $href || '#' === $href || 0 === strpos( $href, 'mailto:' ) ) {
            continue;
        }

        if ( preg_match( '/instagram\.com/i', $href ) ) {
            $instagram_urls[] = esc_url_raw( $href );
        }

        foreach ( $patterns as $key => $pattern ) {
            if ( ! empty( $cache[ $key ] ) ) {
                continue;
            }
            if ( preg_match( $pattern, $href ) ) {
                $cache[ $key ] = esc_url_raw( $href );
            }
        }
    }

    if ( ! empty( $instagram_urls ) ) {
        if ( empty( $cache['social_instagram'] ) ) {
            $cache['social_instagram'] = $instagram_urls[0];
        }
        if ( empty( $cache['social_instagram_community'] ) ) {
            $cache['social_instagram_community'] = $instagram_urls[ count( $instagram_urls ) > 1 ? 1 : 0 ];
        }
    }

    return $cache;
}

/**
 * Resolve a social URL from theme options, legacy keys, and page content.
 *
 * @param string|string[] $option_keys Option key or keys to try in order.
 * @param string          $fallback    Fallback URL when nothing is configured.
 */
function collective_finity_get_social_url( $option_keys, $fallback = '#' ) {
    if ( ! is_array( $option_keys ) ) {
        $option_keys = array( $option_keys );
    }

    $options       = collective_finity_get_theme_options();
    $content_urls  = collective_finity_get_community_urls_from_page_content();

    foreach ( $option_keys as $key ) {
        if ( ! empty( $options[ $key ] ) ) {
            return esc_url( $options[ $key ] );
        }
        if ( ! empty( $content_urls[ $key ] ) ) {
            return esc_url( $content_urls[ $key ] );
        }
    }

    return $fallback;
}

/**
 * Back-compat alias used by older page templates.
 *
 * @param string $option_key Theme option key.
 * @param string $fallback   Fallback URL.
 */
function cf_get_theme_social_url( $option_key, $fallback = '#' ) {
    return collective_finity_get_social_url( $option_key, $fallback );
}

/**
 * Selectable font presets for the body and heading/accent typography.
 *
 * Each entry: label (admin), stack (CSS font-family value), google (families
 * segment for the css2 API, empty for system fonts).
 *
 * @return array<string, array<string, string>>
 */
function collective_finity_get_font_choices() {
    return array(
        'inter'          => array( 'label' => 'Inter',          'stack' => "'Inter', -apple-system, BlinkMacSystemFont, sans-serif", 'google' => 'Inter:wght@400;500;600;700' ),
        'roboto'         => array( 'label' => 'Roboto',         'stack' => "'Roboto', sans-serif",       'google' => 'Roboto:wght@400;500;700' ),
        'open-sans'      => array( 'label' => 'Open Sans',      'stack' => "'Open Sans', sans-serif",    'google' => 'Open+Sans:wght@400;500;600;700' ),
        'lato'           => array( 'label' => 'Lato',           'stack' => "'Lato', sans-serif",         'google' => 'Lato:wght@400;700' ),
        'montserrat'     => array( 'label' => 'Montserrat',     'stack' => "'Montserrat', sans-serif",   'google' => 'Montserrat:wght@400;500;600;700' ),
        'poppins'        => array( 'label' => 'Poppins',        'stack' => "'Poppins', sans-serif",      'google' => 'Poppins:wght@400;500;600;700' ),
        'nunito-sans'    => array( 'label' => 'Nunito Sans',    'stack' => "'Nunito Sans', sans-serif",  'google' => 'Nunito+Sans:wght@400;600;700' ),
        'work-sans'      => array( 'label' => 'Work Sans',      'stack' => "'Work Sans', sans-serif",    'google' => 'Work+Sans:wght@400;500;600;700' ),
        'space-grotesk'  => array( 'label' => 'Space Grotesk',  'stack' => "'Space Grotesk', sans-serif", 'google' => 'Space+Grotesk:wght@400;500;600;700' ),
        'space-mono'     => array( 'label' => 'Space Mono',     'stack' => "'Space Mono', monospace",    'google' => 'Space+Mono:wght@400;700' ),
        'jetbrains-mono' => array( 'label' => 'JetBrains Mono', 'stack' => "'JetBrains Mono', monospace", 'google' => 'JetBrains+Mono:wght@400;700' ),
        'ibm-plex-mono'  => array( 'label' => 'IBM Plex Mono',  'stack' => "'IBM Plex Mono', monospace", 'google' => 'IBM+Plex+Mono:wght@400;500;700' ),
        'system'         => array( 'label' => 'System Default',  'stack' => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif", 'google' => '' ),
    );
}

/**
 * Resolve the CSS font-family stack for a saved font key.
 *
 * @param string $key      Saved font option key.
 * @param string $fallback Fallback font key.
 * @return string
 */
function collective_finity_get_font_stack( $key, $fallback = 'inter' ) {
    $fonts = collective_finity_get_font_choices();
    if ( isset( $fonts[ $key ] ) ) {
        return $fonts[ $key ]['stack'];
    }
    return isset( $fonts[ $fallback ] ) ? $fonts[ $fallback ]['stack'] : "'Inter', sans-serif";
}

/**
 * Convert a hex color into an rgba() string.
 *
 * @param string $hex   Hex color (#rgb or #rrggbb).
 * @param float  $alpha Alpha channel 0–1.
 * @return string
 */
function collective_finity_hex_to_rgba( $hex, $alpha = 1 ) {
    $hex = ltrim( (string) $hex, '#' );
    if ( 3 === strlen( $hex ) ) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if ( 6 !== strlen( $hex ) ) {
        return 'rgba(255,183,0,' . $alpha . ')';
    }
    $r = hexdec( substr( $hex, 0, 2 ) );
    $g = hexdec( substr( $hex, 2, 2 ) );
    $b = hexdec( substr( $hex, 4, 2 ) );
    return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, $alpha );
}

/**
 * Lighten or darken a hex color by an absolute RGB step.
 *
 * @param string $hex   Hex color.
 * @param int    $steps Positive lightens, negative darkens.
 * @return string
 */
function collective_finity_adjust_hex_brightness( $hex, $steps ) {
    $hex = ltrim( (string) $hex, '#' );
    if ( 3 === strlen( $hex ) ) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if ( 6 !== strlen( $hex ) ) {
        return '#' . $hex;
    }
    $r = max( 0, min( 255, hexdec( substr( $hex, 0, 2 ) ) + $steps ) );
    $g = max( 0, min( 255, hexdec( substr( $hex, 2, 2 ) ) + $steps ) );
    $b = max( 0, min( 255, hexdec( substr( $hex, 4, 2 ) ) + $steps ) );
    return sprintf( '#%02x%02x%02x', $r, $g, $b );
}


function collective_finity_register_theme_options_settings() {
    register_setting(
        'collective_finity_theme_options_group',
        collective_finity_theme_options_key(),
        array(
            'type'              => 'array',
            'sanitize_callback' => 'collective_finity_sanitize_theme_options',
            'default'           => collective_finity_default_theme_options(),
        )
    );
}
add_action( 'admin_init', 'collective_finity_register_theme_options_settings' );

function collective_finity_sanitize_theme_options( $input ) {
    $defaults = collective_finity_default_theme_options();
    $output   = collective_finity_get_theme_options();

    if ( ! is_array( $input ) ) {
        return $output;
    }

    $submitted_tab = isset( $input['_submitted_tab'] ) ? sanitize_key( $input['_submitted_tab'] ) : '';

    if ( 'general' === $submitted_tab ) {
        $output['primary_color']      = sanitize_hex_color( $input['primary_color'] ?? $defaults['primary_color'] ) ?: $defaults['primary_color'];
        $output['accent_color']       = sanitize_hex_color( $input['accent_color'] ?? $defaults['accent_color'] ) ?: $defaults['accent_color'];
        $output['text_color']         = sanitize_hex_color( $input['text_color'] ?? $defaults['text_color'] ) ?: $defaults['text_color'];
        $output['text_muted_color']   = sanitize_hex_color( $input['text_muted_color'] ?? $defaults['text_muted_color'] ) ?: $defaults['text_muted_color'];
        $output['card_bg_color']      = sanitize_hex_color( $input['card_bg_color'] ?? $defaults['card_bg_color'] ) ?: $defaults['card_bg_color'];
        $output['border_color']       = sanitize_hex_color( $input['border_color'] ?? $defaults['border_color'] ) ?: $defaults['border_color'];
        $output['link_color']         = sanitize_hex_color( $input['link_color'] ?? $defaults['link_color'] ) ?: $defaults['link_color'];
        $output['link_hover_color']   = sanitize_hex_color( $input['link_hover_color'] ?? $defaults['link_hover_color'] ) ?: $defaults['link_hover_color'];

        $output['base_font_size']     = min( 20, max( 13, absint( $input['base_font_size'] ?? $defaults['base_font_size'] ) ) );
        $output['h1_font_size']       = min( 72, max( 20, absint( $input['h1_font_size'] ?? $defaults['h1_font_size'] ) ) );
        $output['h2_font_size']       = min( 56, max( 18, absint( $input['h2_font_size'] ?? $defaults['h2_font_size'] ) ) );
        $output['h3_font_size']       = min( 40, max( 16, absint( $input['h3_font_size'] ?? $defaults['h3_font_size'] ) ) );

        $heading_weights = array( '400', '500', '600', '700', '800' );
        $heading_weight  = isset( $input['heading_font_weight'] ) ? sanitize_key( $input['heading_font_weight'] ) : $defaults['heading_font_weight'];
        $output['heading_font_weight'] = in_array( $heading_weight, $heading_weights, true ) ? $heading_weight : $defaults['heading_font_weight'];

        $output['heading_letter_spacing'] = min( 0.15, max( 0, round( floatval( $input['heading_letter_spacing'] ?? $defaults['heading_letter_spacing'] ), 2 ) ) );
        $output['body_line_height']     = min( 2.0, max( 1.2, round( floatval( $input['body_line_height'] ?? $defaults['body_line_height'] ), 2 ) ) );

        $output['button_radius'] = min( 40, max( 0, absint( $input['button_radius'] ?? $defaults['button_radius'] ) ) );

        $button_sizes = array( 'compact', 'regular', 'large' );
        $button_size  = isset( $input['button_size'] ) ? sanitize_key( $input['button_size'] ) : $defaults['button_size'];
        $output['button_size'] = in_array( $button_size, $button_sizes, true ) ? $button_size : $defaults['button_size'];

        $button_hovers = array( 'none', 'brighten', 'scale', 'lift' );
        $button_hover  = isset( $input['button_hover_effect'] ) ? sanitize_key( $input['button_hover_effect'] ) : $defaults['button_hover_effect'];
        $output['button_hover_effect'] = in_array( $button_hover, $button_hovers, true ) ? $button_hover : $defaults['button_hover_effect'];

        $output['card_radius']       = min( 32, max( 0, absint( $input['card_radius'] ?? $defaults['card_radius'] ) ) );
        $output['card_border_width'] = min( 4, max( 0, absint( $input['card_border_width'] ?? $defaults['card_border_width'] ) ) );

        $card_hovers = array( 'none', 'lift', 'glow', 'scale' );
        $card_hover  = isset( $input['card_hover_effect'] ) ? sanitize_key( $input['card_hover_effect'] ) : $defaults['card_hover_effect'];
        $output['card_hover_effect'] = in_array( $card_hover, $card_hovers, true ) ? $card_hover : $defaults['card_hover_effect'];

        $card_shadows = array( 'none', 'soft', 'strong' );
        $card_shadow  = isset( $input['card_shadow'] ) ? sanitize_key( $input['card_shadow'] ) : $defaults['card_shadow'];
        $output['card_shadow'] = in_array( $card_shadow, $card_shadows, true ) ? $card_shadow : $defaults['card_shadow'];

        $transition_speeds = array( 'fast', 'normal', 'slow' );
        $transition_speed  = isset( $input['transition_speed'] ) ? sanitize_key( $input['transition_speed'] ) : $defaults['transition_speed'];
        $output['transition_speed'] = in_array( $transition_speed, $transition_speeds, true ) ? $transition_speed : $defaults['transition_speed'];

        $output['enable_glow_effects'] = empty( $input['enable_glow_effects'] ) ? 0 : 1;

        $section_spacings = array( 'compact', 'default', 'spacious' );
        $section_spacing  = isset( $input['section_spacing'] ) ? sanitize_key( $input['section_spacing'] ) : $defaults['section_spacing'];
        $output['section_spacing'] = in_array( $section_spacing, $section_spacings, true ) ? $section_spacing : $defaults['section_spacing'];

        $output['sidebar_logo']       = absint( $input['sidebar_logo'] ?? 0 );
        $output['mobile_logo']        = absint( $input['mobile_logo'] ?? 0 );
        $output['sidebar_logo_size']  = min( 120, max( 16, absint( $input['sidebar_logo_size'] ?? $defaults['sidebar_logo_size'] ) ) );
        $output['mobile_logo_size']   = min( 120, max( 16, absint( $input['mobile_logo_size'] ?? $defaults['mobile_logo_size'] ) ) );

        $fonts                        = collective_finity_get_font_choices();
        $body_font                    = isset( $input['body_font'] ) ? sanitize_key( $input['body_font'] ) : $defaults['body_font'];
        $heading_font                 = isset( $input['heading_font'] ) ? sanitize_key( $input['heading_font'] ) : $defaults['heading_font'];
        $output['body_font']          = isset( $fonts[ $body_font ] ) ? $body_font : $defaults['body_font'];
        $output['heading_font']       = isset( $fonts[ $heading_font ] ) ? $heading_font : $defaults['heading_font'];

        $output['enable_preloader']   = empty( $input['enable_preloader'] ) ? 0 : 1;
        $output['enable_back_to_top'] = empty( $input['enable_back_to_top'] ) ? 0 : 1;
    }

    if ( 'player' === $submitted_tab ) {
        $output['show_global_player'] = empty( $input['show_global_player'] ) ? 0 : 1;
        $output['default_volume']     = min( 100, max( 0, absint( $input['default_volume'] ?? $defaults['default_volume'] ) ) );
        $output['popular_min_views']  = max( 0, absint( $input['popular_min_views'] ?? $defaults['popular_min_views'] ) );
    }

    if ( 'footer' === $submitted_tab ) {
        $output['footer_copyright']   = sanitize_text_field( $input['footer_copyright'] ?? '' );
        $output['footer_tagline']     = sanitize_text_field( $input['footer_tagline'] ?? $defaults['footer_tagline'] );
        $desc                         = sanitize_text_field( $input['footer_description'] ?? '' );
        $output['footer_description'] = mb_substr( $desc ?: $defaults['footer_description'], 0, 140 );
        $output['footer_logo']        = absint( $input['footer_logo'] ?? 0 );
        $output['footer_logo_size']   = min( 120, max( 16, absint( $input['footer_logo_size'] ?? $defaults['footer_logo_size'] ) ) );
        $social_fields                = array(
            'social_instagram',
            'social_instagram_community',
            'social_youtube',
            'social_spotify',
            'social_facebook',
            'social_facebook_group',
            'social_discord',
            'social_tiktok',
            'social_soundcloud',
            'social_amazon',
            'social_amazon_music',
            'social_x',
        );
        foreach ( $social_fields as $field ) {
            $output[ $field ] = esc_url_raw( $input[ $field ] ?? '' );
        }
    }

    if ( 'advanced' === $submitted_tab ) {
        $output['footer_copyright'] = sanitize_text_field( $input['footer_copyright'] ?? '' );
        $output['custom_css']       = wp_strip_all_tags( $input['custom_css'] ?? '' );
    }

    if ( 'ads' === $submitted_tab ) {
        $output['ad_preview_mode'] = empty( $input['ad_preview_mode'] ) ? 0 : 1;

        $publisher_id = sanitize_text_field( $input['adsense_publisher_id'] ?? '' );
        $output['adsense_publisher_id'] = ( $publisher_id && preg_match( '/^ca-pub-\d+$/', $publisher_id ) ) ? $publisher_id : '';

        $default_zones = collective_finity_default_ad_zones();
        $input_zones   = isset( $input['ad_zones'] ) && is_array( $input['ad_zones'] ) ? $input['ad_zones'] : array();
        $output['ad_zones'] = $output['ad_zones'] ?? $default_zones;

        foreach ( $default_zones as $zone_id => $zone_defaults ) {
            $zone_input = isset( $input_zones[ $zone_id ] ) && is_array( $input_zones[ $zone_id ] ) ? $input_zones[ $zone_id ] : array();

            $output['ad_zones'][ $zone_id ]['enabled'] = empty( $zone_input['enabled'] ) ? 0 : 1;

            if ( current_user_can( 'unfiltered_html' ) ) {
                $output['ad_zones'][ $zone_id ]['code'] = isset( $zone_input['code'] ) ? $zone_input['code'] : '';
            } else {
                $output['ad_zones'][ $zone_id ]['code'] = wp_kses_post( $zone_input['code'] ?? '' );
            }

            $output['ad_zones'][ $zone_id ]['adsense_slot_id'] = sanitize_text_field( $zone_input['adsense_slot_id'] ?? '' );

            if ( isset( $zone_defaults['frequency'] ) ) {
                $output['ad_zones'][ $zone_id ]['frequency'] = min( 50, max( 2, absint( $zone_input['frequency'] ?? $zone_defaults['frequency'] ) ) );
            }
        }
    }

    if ( 'donate' === $submitted_tab ) {
        $raw_messages = isset( $input['donate_leadscreen_messages'] ) && is_array( $input['donate_leadscreen_messages'] )
            ? $input['donate_leadscreen_messages']
            : array();
        $clean_messages = array();
        foreach ( array_slice( $raw_messages, 0, 5 ) as $msg ) {
            $msg = sanitize_text_field( $msg );
            if ( '' !== $msg ) {
                $clean_messages[] = $msg;
            }
        }
        $output['donate_leadscreen_messages'] = $clean_messages ?: $defaults['donate_leadscreen_messages'];

        $output['donate_leadscreen_animation'] = in_array( $input['donate_leadscreen_animation'] ?? '', collective_finity_donate_leadscreen_animations(), true )
            ? $input['donate_leadscreen_animation']
            : $defaults['donate_leadscreen_animation'];

        $output['donate_leadscreen_position'] = in_array( $input['donate_leadscreen_position'] ?? '', array( 'top', 'middle', 'bottom' ), true )
            ? $input['donate_leadscreen_position']
            : $defaults['donate_leadscreen_position'];
    }

    if ( 'header' === $submitted_tab && isset( $input['active_header'] ) ) {
        collective_finity_set_theme_part_template_id( 'header', absint( $input['active_header'] ) );
    }
    if ( 'footer' === $submitted_tab && isset( $input['active_footer'] ) ) {
        collective_finity_set_theme_part_template_id( 'footer', absint( $input['active_footer'] ) );
    }
    if ( 'sidebar' === $submitted_tab && isset( $input['active_sidebar'] ) ) {
        collective_finity_set_theme_part_template_id( 'sidebar', absint( $input['active_sidebar'] ) );
    }

    return $output;
}

function collective_finity_theme_options_tabs() {
    return array(
        'general'  => __( 'General', 'collective-finity' ),
        'header'   => __( 'Header Settings', 'collective-finity' ),
        'footer'   => __( 'Footer Settings', 'collective-finity' ),
        'sidebar'  => __( 'Side Panel Settings', 'collective-finity' ),
        'player'   => __( 'Music Player', 'collective-finity' ),
        'ads'      => __( 'Ad Manager', 'collective-finity' ),
        'donate'   => __( 'Donate Page', 'collective-finity' ),
        'advanced' => __( 'Advanced', 'collective-finity' ),
    );
}

function collective_finity_theme_options_assets( $hook ) {
    if ( 'collective-finity_page_collective-finity-options' !== $hook && 'toplevel_page_collective-finity-options' !== $hook ) {
        return;
    }
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_media();
    wp_enqueue_style( 'cf-theme-options-admin', get_template_directory_uri() . '/assets/css/theme-options-admin.css', array(), wp_get_theme()->get( 'Version' ) );
    wp_enqueue_script( 'cf-theme-options-admin', get_template_directory_uri() . '/assets/js/theme-options-admin.js', array( 'jquery', 'wp-color-picker', 'media-editor' ), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'admin_enqueue_scripts', 'collective_finity_theme_options_assets' );

function collective_finity_render_theme_options_page() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    $options    = collective_finity_get_theme_options();
    $tabs       = collective_finity_theme_options_tabs();
    $active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general';
    if ( ! isset( $tabs[ $active_tab ] ) ) {
        $active_tab = 'general';
    }
    ?>
    <div class="wrap cf-theme-options-wrap cf-branded-admin-wrap">
        <div class="cf-admin-page-header">
            <h1><?php esc_html_e( 'Theme Options', 'collective-finity' ); ?></h1>
            <p class="description"><?php esc_html_e( 'Control your Collective Finity theme from one place.', 'collective-finity' ); ?></p>
        </div>

        <nav class="nav-tab-wrapper cf-theme-options-tabs">
            <?php foreach ( $tabs as $slug => $label ) : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=collective-finity-options&tab=' . $slug ) ); ?>" class="nav-tab <?php echo $active_tab === $slug ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $label ); ?></a>
            <?php endforeach; ?>
        </nav>

        <form method="post" action="options.php" class="cf-theme-options-form">
            <?php settings_fields( 'collective_finity_theme_options_group' ); ?>
            <input type="hidden" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[_submitted_tab]" value="<?php echo esc_attr( $active_tab ); ?>">
            <div class="cf-theme-options-panel">
                <?php
                switch ( $active_tab ) {
                    case 'header':
                        collective_finity_render_theme_options_part_tab( 'header' );
                        break;
                    case 'footer':
                        collective_finity_render_theme_options_footer_content_tab( $options );
                        collective_finity_render_theme_options_part_tab( 'footer' );
                        break;
                    case 'sidebar':
                        collective_finity_render_theme_options_part_tab( 'sidebar' );
                        break;
                    case 'player':
                        collective_finity_render_theme_options_player_tab( $options );
                        break;
                    case 'ads':
                        collective_finity_render_theme_options_ads_tab( $options );
                        break;
                    case 'donate':
                        collective_finity_render_theme_options_donate_tab( $options );
                        break;
                    case 'advanced':
                        collective_finity_render_theme_options_advanced_tab( $options );
                        break;
                    default:
                        collective_finity_render_theme_options_general_tab( $options );
                        break;
                }
                ?>
            </div>
            <?php if ( ! in_array( $active_tab, array( 'header', 'footer', 'sidebar' ), true ) ) : ?>
                <?php submit_button( __( 'Save Changes', 'collective-finity' ) ); ?>
            <?php endif; ?>
        </form>
    </div>
    <?php
}

function collective_finity_render_theme_options_logo_field( $field, $label, $value, $description ) {
    $option_key = collective_finity_theme_options_key();
    $attachment = $value ? wp_get_attachment_image_url( absint( $value ), 'thumbnail' ) : '';
    ?>
    <tr>
        <th scope="row"><?php echo esc_html( $label ); ?></th>
        <td>
            <div class="cf-logo-field" data-cf-media-field>
                <input type="hidden" class="cf-logo-input" name="<?php echo esc_attr( $option_key ); ?>[<?php echo esc_attr( $field ); ?>]" value="<?php echo esc_attr( $value ); ?>">
                <div class="cf-logo-preview">
                    <?php if ( $attachment ) : ?>
                        <img src="<?php echo esc_url( $attachment ); ?>" alt="">
                    <?php endif; ?>
                </div>
                <div class="cf-logo-actions">
                    <button type="button" class="button cf-logo-upload"><?php esc_html_e( 'Select Logo', 'collective-finity' ); ?></button>
                    <button type="button" class="button cf-logo-remove"<?php disabled( empty( $value ) ); ?>><?php esc_html_e( 'Remove', 'collective-finity' ); ?></button>
                </div>
            </div>
            <p class="description"><?php echo esc_html( $description ); ?></p>
        </td>
    </tr>
    <?php
}

function collective_finity_render_theme_options_general_tab( $options ) {
    $option_key   = collective_finity_theme_options_key();
    $font_choices = collective_finity_get_font_choices();
    ?>
    <h2><?php esc_html_e( 'General Settings', 'collective-finity' ); ?></h2>
    <h3 class="cf-options-subhead"><?php esc_html_e( 'Colors', 'collective-finity' ); ?></h3>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="cf_primary_color"><?php esc_html_e( 'Primary Color', 'collective-finity' ); ?></label></th>
            <td>
                <input type="text" class="cf-color-field" id="cf_primary_color" name="<?php echo esc_attr( $option_key ); ?>[primary_color]" value="<?php echo esc_attr( $options['primary_color'] ); ?>" data-default-color="#FFB700">
                <p class="description"><?php esc_html_e( 'Brand accent used across the sidebar, footer, buttons, and active nav item.', 'collective-finity' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_accent_color"><?php esc_html_e( 'Dark Accent Color', 'collective-finity' ); ?></label></th>
            <td>
                <input type="text" class="cf-color-field" id="cf_accent_color" name="<?php echo esc_attr( $option_key ); ?>[accent_color]" value="<?php echo esc_attr( $options['accent_color'] ); ?>" data-default-color="#0D0D0D">
                <p class="description"><?php esc_html_e( 'Dark base tone used for the site background and side panels.', 'collective-finity' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_text_color"><?php esc_html_e( 'Text Color', 'collective-finity' ); ?></label></th>
            <td>
                <input type="text" class="cf-color-field" id="cf_text_color" name="<?php echo esc_attr( $option_key ); ?>[text_color]" value="<?php echo esc_attr( $options['text_color'] ); ?>" data-default-color="#FFFFFF">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_text_muted_color"><?php esc_html_e( 'Muted Text Color', 'collective-finity' ); ?></label></th>
            <td>
                <input type="text" class="cf-color-field" id="cf_text_muted_color" name="<?php echo esc_attr( $option_key ); ?>[text_muted_color]" value="<?php echo esc_attr( $options['text_muted_color'] ); ?>" data-default-color="#B3B3B3">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_card_bg_color"><?php esc_html_e( 'Card Background', 'collective-finity' ); ?></label></th>
            <td>
                <input type="text" class="cf-color-field" id="cf_card_bg_color" name="<?php echo esc_attr( $option_key ); ?>[card_bg_color]" value="<?php echo esc_attr( $options['card_bg_color'] ); ?>" data-default-color="#141414">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_border_color"><?php esc_html_e( 'Border Color', 'collective-finity' ); ?></label></th>
            <td>
                <input type="text" class="cf-color-field" id="cf_border_color" name="<?php echo esc_attr( $option_key ); ?>[border_color]" value="<?php echo esc_attr( $options['border_color'] ); ?>" data-default-color="#232323">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_link_color"><?php esc_html_e( 'Link Color', 'collective-finity' ); ?></label></th>
            <td>
                <input type="text" class="cf-color-field" id="cf_link_color" name="<?php echo esc_attr( $option_key ); ?>[link_color]" value="<?php echo esc_attr( $options['link_color'] ); ?>" data-default-color="#FFB700">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_link_hover_color"><?php esc_html_e( 'Link Hover Color', 'collective-finity' ); ?></label></th>
            <td>
                <input type="text" class="cf-color-field" id="cf_link_hover_color" name="<?php echo esc_attr( $option_key ); ?>[link_hover_color]" value="<?php echo esc_attr( $options['link_hover_color'] ); ?>" data-default-color="#ffc633">
            </td>
        </tr>
    </table>

    <h3 class="cf-options-subhead"><?php esc_html_e( 'Logo', 'collective-finity' ); ?></h3>
    <table class="form-table" role="presentation">
        <?php
        collective_finity_render_theme_options_logo_field(
            'sidebar_logo',
            __( 'Sidebar / Header Logo', 'collective-finity' ),
            $options['sidebar_logo'],
            __( 'Replaces the diamond mark next to the brand name in the desktop sidebar. Leave empty to use the default diamond icon.', 'collective-finity' )
        );
        ?>
        <tr>
            <th scope="row"><label for="cf_sidebar_logo_size"><?php esc_html_e( 'Sidebar Logo Size (px)', 'collective-finity' ); ?></label></th>
            <td>
                <input type="number" min="16" max="120" id="cf_sidebar_logo_size" name="<?php echo esc_attr( $option_key ); ?>[sidebar_logo_size]" value="<?php echo esc_attr( $options['sidebar_logo_size'] ); ?>">
            </td>
        </tr>
        <?php
        collective_finity_render_theme_options_logo_field(
            'mobile_logo',
            __( 'Mobile / Tablet Logo Override', 'collective-finity' ),
            $options['mobile_logo'],
            __( 'Optional. Shown in the mobile/tablet top bar. Falls back to the sidebar logo, then the diamond icon.', 'collective-finity' )
        );
        ?>
        <tr>
            <th scope="row"><label for="cf_mobile_logo_size"><?php esc_html_e( 'Mobile Logo Size (px)', 'collective-finity' ); ?></label></th>
            <td>
                <input type="number" min="16" max="120" id="cf_mobile_logo_size" name="<?php echo esc_attr( $option_key ); ?>[mobile_logo_size]" value="<?php echo esc_attr( $options['mobile_logo_size'] ); ?>">
            </td>
        </tr>
    </table>

    <h3 class="cf-options-subhead"><?php esc_html_e( 'Typography', 'collective-finity' ); ?></h3>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="cf_body_font"><?php esc_html_e( 'Body Font', 'collective-finity' ); ?></label></th>
            <td>
                <select id="cf_body_font" name="<?php echo esc_attr( $option_key ); ?>[body_font]">
                    <?php foreach ( $font_choices as $key => $font ) : ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $options['body_font'], $key ); ?>><?php echo esc_html( $font['label'] ); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php esc_html_e( 'Base font for body text and navigation (default: Inter).', 'collective-finity' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_heading_font"><?php esc_html_e( 'Accent / Heading Font', 'collective-finity' ); ?></label></th>
            <td>
                <select id="cf_heading_font" name="<?php echo esc_attr( $option_key ); ?>[heading_font]">
                    <?php foreach ( $font_choices as $key => $font ) : ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $options['heading_font'], $key ); ?>><?php echo esc_html( $font['label'] ); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php esc_html_e( 'Monospace/accent font for the wordmark, eyebrows, and labels (default: Space Mono).', 'collective-finity' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_base_font_size"><?php esc_html_e( 'Base Font Size (px)', 'collective-finity' ); ?></label></th>
            <td>
                <input type="number" min="13" max="20" id="cf_base_font_size" name="<?php echo esc_attr( $option_key ); ?>[base_font_size]" value="<?php echo esc_attr( $options['base_font_size'] ); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_h1_font_size"><?php esc_html_e( 'H1 Font Size (px)', 'collective-finity' ); ?></label></th>
            <td>
                <input type="number" min="20" max="72" id="cf_h1_font_size" name="<?php echo esc_attr( $option_key ); ?>[h1_font_size]" value="<?php echo esc_attr( $options['h1_font_size'] ); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_h2_font_size"><?php esc_html_e( 'H2 Font Size (px)', 'collective-finity' ); ?></label></th>
            <td>
                <input type="number" min="18" max="56" id="cf_h2_font_size" name="<?php echo esc_attr( $option_key ); ?>[h2_font_size]" value="<?php echo esc_attr( $options['h2_font_size'] ); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_h3_font_size"><?php esc_html_e( 'H3 Font Size (px)', 'collective-finity' ); ?></label></th>
            <td>
                <input type="number" min="16" max="40" id="cf_h3_font_size" name="<?php echo esc_attr( $option_key ); ?>[h3_font_size]" value="<?php echo esc_attr( $options['h3_font_size'] ); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_heading_font_weight"><?php esc_html_e( 'Heading Font Weight', 'collective-finity' ); ?></label></th>
            <td>
                <select id="cf_heading_font_weight" name="<?php echo esc_attr( $option_key ); ?>[heading_font_weight]">
                    <?php foreach ( array( '400', '500', '600', '700', '800' ) as $weight ) : ?>
                        <option value="<?php echo esc_attr( $weight ); ?>" <?php selected( $options['heading_font_weight'], $weight ); ?>><?php echo esc_html( $weight ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_heading_letter_spacing"><?php esc_html_e( 'Heading Letter Spacing (em)', 'collective-finity' ); ?></label></th>
            <td>
                <input type="number" min="0" max="0.15" step="0.01" id="cf_heading_letter_spacing" name="<?php echo esc_attr( $option_key ); ?>[heading_letter_spacing]" value="<?php echo esc_attr( $options['heading_letter_spacing'] ); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_body_line_height"><?php esc_html_e( 'Body Line Height', 'collective-finity' ); ?></label></th>
            <td>
                <input type="number" min="1.2" max="2.0" step="0.05" id="cf_body_line_height" name="<?php echo esc_attr( $option_key ); ?>[body_line_height]" value="<?php echo esc_attr( $options['body_line_height'] ); ?>">
            </td>
        </tr>
    </table>

    <h3 class="cf-options-subhead"><?php esc_html_e( 'Buttons', 'collective-finity' ); ?></h3>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="cf_button_radius"><?php esc_html_e( 'Button Radius (px)', 'collective-finity' ); ?></label></th>
            <td>
                <input type="number" min="0" max="40" id="cf_button_radius" name="<?php echo esc_attr( $option_key ); ?>[button_radius]" value="<?php echo esc_attr( $options['button_radius'] ); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_button_size"><?php esc_html_e( 'Button Size', 'collective-finity' ); ?></label></th>
            <td>
                <select id="cf_button_size" name="<?php echo esc_attr( $option_key ); ?>[button_size]">
                    <option value="compact" <?php selected( $options['button_size'], 'compact' ); ?>><?php esc_html_e( 'Compact', 'collective-finity' ); ?></option>
                    <option value="regular" <?php selected( $options['button_size'], 'regular' ); ?>><?php esc_html_e( 'Regular', 'collective-finity' ); ?></option>
                    <option value="large" <?php selected( $options['button_size'], 'large' ); ?>><?php esc_html_e( 'Large', 'collective-finity' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_button_hover_effect"><?php esc_html_e( 'Button Hover Effect', 'collective-finity' ); ?></label></th>
            <td>
                <select id="cf_button_hover_effect" name="<?php echo esc_attr( $option_key ); ?>[button_hover_effect]">
                    <option value="none" <?php selected( $options['button_hover_effect'], 'none' ); ?>><?php esc_html_e( 'None', 'collective-finity' ); ?></option>
                    <option value="brighten" <?php selected( $options['button_hover_effect'], 'brighten' ); ?>><?php esc_html_e( 'Brighten', 'collective-finity' ); ?></option>
                    <option value="scale" <?php selected( $options['button_hover_effect'], 'scale' ); ?>><?php esc_html_e( 'Scale', 'collective-finity' ); ?></option>
                    <option value="lift" <?php selected( $options['button_hover_effect'], 'lift' ); ?>><?php esc_html_e( 'Lift', 'collective-finity' ); ?></option>
                </select>
            </td>
        </tr>
    </table>

    <h3 class="cf-options-subhead"><?php esc_html_e( 'Cards', 'collective-finity' ); ?></h3>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="cf_card_radius"><?php esc_html_e( 'Card Radius (px)', 'collective-finity' ); ?></label></th>
            <td>
                <input type="number" min="0" max="32" id="cf_card_radius" name="<?php echo esc_attr( $option_key ); ?>[card_radius]" value="<?php echo esc_attr( $options['card_radius'] ); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_card_border_width"><?php esc_html_e( 'Card Border Width (px)', 'collective-finity' ); ?></label></th>
            <td>
                <input type="number" min="0" max="4" id="cf_card_border_width" name="<?php echo esc_attr( $option_key ); ?>[card_border_width]" value="<?php echo esc_attr( $options['card_border_width'] ); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_card_hover_effect"><?php esc_html_e( 'Card Hover Effect', 'collective-finity' ); ?></label></th>
            <td>
                <select id="cf_card_hover_effect" name="<?php echo esc_attr( $option_key ); ?>[card_hover_effect]">
                    <option value="none" <?php selected( $options['card_hover_effect'], 'none' ); ?>><?php esc_html_e( 'None', 'collective-finity' ); ?></option>
                    <option value="lift" <?php selected( $options['card_hover_effect'], 'lift' ); ?>><?php esc_html_e( 'Lift', 'collective-finity' ); ?></option>
                    <option value="glow" <?php selected( $options['card_hover_effect'], 'glow' ); ?>><?php esc_html_e( 'Glow', 'collective-finity' ); ?></option>
                    <option value="scale" <?php selected( $options['card_hover_effect'], 'scale' ); ?>><?php esc_html_e( 'Scale', 'collective-finity' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_card_shadow"><?php esc_html_e( 'Card Shadow', 'collective-finity' ); ?></label></th>
            <td>
                <select id="cf_card_shadow" name="<?php echo esc_attr( $option_key ); ?>[card_shadow]">
                    <option value="none" <?php selected( $options['card_shadow'], 'none' ); ?>><?php esc_html_e( 'None', 'collective-finity' ); ?></option>
                    <option value="soft" <?php selected( $options['card_shadow'], 'soft' ); ?>><?php esc_html_e( 'Soft', 'collective-finity' ); ?></option>
                    <option value="strong" <?php selected( $options['card_shadow'], 'strong' ); ?>><?php esc_html_e( 'Strong', 'collective-finity' ); ?></option>
                </select>
            </td>
        </tr>
    </table>

    <h3 class="cf-options-subhead"><?php esc_html_e( 'Effects', 'collective-finity' ); ?></h3>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="cf_transition_speed"><?php esc_html_e( 'Transition Speed', 'collective-finity' ); ?></label></th>
            <td>
                <select id="cf_transition_speed" name="<?php echo esc_attr( $option_key ); ?>[transition_speed]">
                    <option value="fast" <?php selected( $options['transition_speed'], 'fast' ); ?>><?php esc_html_e( 'Fast', 'collective-finity' ); ?></option>
                    <option value="normal" <?php selected( $options['transition_speed'], 'normal' ); ?>><?php esc_html_e( 'Normal', 'collective-finity' ); ?></option>
                    <option value="slow" <?php selected( $options['transition_speed'], 'slow' ); ?>><?php esc_html_e( 'Slow', 'collective-finity' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Glow Effects', 'collective-finity' ); ?></th>
            <td><label><input type="checkbox" name="<?php echo esc_attr( $option_key ); ?>[enable_glow_effects]" value="1" <?php checked( $options['enable_glow_effects'], 1 ); ?>> <?php esc_html_e( 'Enable radial gradient glow effects', 'collective-finity' ); ?></label></td>
        </tr>
    </table>

    <h3 class="cf-options-subhead"><?php esc_html_e( 'Spacing', 'collective-finity' ); ?></h3>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="cf_section_spacing"><?php esc_html_e( 'Section Spacing', 'collective-finity' ); ?></label></th>
            <td>
                <select id="cf_section_spacing" name="<?php echo esc_attr( $option_key ); ?>[section_spacing]">
                    <option value="compact" <?php selected( $options['section_spacing'], 'compact' ); ?>><?php esc_html_e( 'Compact', 'collective-finity' ); ?></option>
                    <option value="default" <?php selected( $options['section_spacing'], 'default' ); ?>><?php esc_html_e( 'Default', 'collective-finity' ); ?></option>
                    <option value="spacious" <?php selected( $options['section_spacing'], 'spacious' ); ?>><?php esc_html_e( 'Spacious', 'collective-finity' ); ?></option>
                </select>
                <p class="description"><?php esc_html_e( 'Vertical gap between major page sections.', 'collective-finity' ); ?></p>
            </td>
        </tr>
    </table>

    <h3 class="cf-options-subhead"><?php esc_html_e( 'Behavior', 'collective-finity' ); ?></h3>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><?php esc_html_e( 'Preloader', 'collective-finity' ); ?></th>
            <td><label><input type="checkbox" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[enable_preloader]" value="1" <?php checked( $options['enable_preloader'], 1 ); ?>> <?php esc_html_e( 'Enable site preloader', 'collective-finity' ); ?></label></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Back to Top', 'collective-finity' ); ?></th>
            <td><label><input type="checkbox" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[enable_back_to_top]" value="1" <?php checked( $options['enable_back_to_top'], 1 ); ?>> <?php esc_html_e( 'Show back-to-top button', 'collective-finity' ); ?></label></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Customizer', 'collective-finity' ); ?></th>
            <td><a class="button" href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=cf_theme_parts' ) ); ?>"><?php esc_html_e( 'Open Theme Parts in Customizer', 'collective-finity' ); ?></a></td>
        </tr>
    </table>
    <?php
}

function collective_finity_render_theme_options_footer_content_tab( $options ) {
    ?>
    <h2><?php esc_html_e( 'Footer Branding', 'collective-finity' ); ?></h2>
    <p class="description"><?php esc_html_e( 'Logo uses your site logo from Customizer. Description is limited to 140 characters.', 'collective-finity' ); ?></p>
    <table class="form-table" role="presentation">
        <?php
        collective_finity_render_theme_options_logo_field(
            'footer_logo',
            __( 'Footer Logo', 'collective-finity' ),
            $options['footer_logo'],
            __( 'Replaces the diamond mark next to the brand name in the footer. Leave empty to use the default diamond icon.', 'collective-finity' )
        );
        ?>
        <tr>
            <th scope="row"><label for="cf_footer_logo_size"><?php esc_html_e( 'Footer Logo Size (px)', 'collective-finity' ); ?></label></th>
            <td>
                <input type="number" min="16" max="120" id="cf_footer_logo_size" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[footer_logo_size]" value="<?php echo esc_attr( $options['footer_logo_size'] ); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_footer_tagline"><?php esc_html_e( 'Tagline', 'collective-finity' ); ?></label></th>
            <td><input type="text" class="regular-text" id="cf_footer_tagline" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[footer_tagline]" value="<?php echo esc_attr( $options['footer_tagline'] ); ?>"></td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_footer_description"><?php esc_html_e( 'Description', 'collective-finity' ); ?></label></th>
            <td>
                <textarea id="cf_footer_description" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[footer_description]" rows="3" class="large-text" maxlength="140"><?php echo esc_textarea( $options['footer_description'] ); ?></textarea>
                <p class="description"><?php esc_html_e( 'Maximum 140 characters.', 'collective-finity' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_footer_copyright"><?php esc_html_e( 'Copyright Override', 'collective-finity' ); ?></label></th>
            <td><input type="text" class="regular-text" id="cf_footer_copyright" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[footer_copyright]" value="<?php echo esc_attr( $options['footer_copyright'] ); ?>" placeholder="<?php echo esc_attr( '© ' . gmdate( 'Y' ) . ' Collective Finity' ); ?>"></td>
        </tr>
    </table>
    <h2><?php esc_html_e( 'Social Media Links', 'collective-finity' ); ?></h2>
    <table class="form-table" role="presentation">
        <tr><th scope="row"><label for="cf_social_discord">Discord</label></th><td><input type="url" class="regular-text" id="cf_social_discord" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_discord]" value="<?php echo esc_url( $options['social_discord'] ); ?>" placeholder="https://discord.gg/..."></td></tr>
        <tr><th scope="row"><label for="cf_social_facebook_group">Facebook Group</label></th><td><input type="url" class="regular-text" id="cf_social_facebook_group" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_facebook_group]" value="<?php echo esc_url( $options['social_facebook_group'] ); ?>" placeholder="https://facebook.com/groups/..."></td></tr>
        <tr><th scope="row"><label for="cf_social_facebook">Facebook Page</label></th><td><input type="url" class="regular-text" id="cf_social_facebook" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_facebook]" value="<?php echo esc_url( $options['social_facebook'] ); ?>" placeholder="https://facebook.com/..."></td></tr>
        <tr><th scope="row"><label for="cf_social_instagram">Instagram — Music</label></th><td><input type="url" class="regular-text" id="cf_social_instagram" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_instagram]" value="<?php echo esc_url( $options['social_instagram'] ); ?>" placeholder="https://instagram.com/..."></td></tr>
        <tr><th scope="row"><label for="cf_social_instagram_community">Instagram — Community</label></th><td><input type="url" class="regular-text" id="cf_social_instagram_community" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_instagram_community]" value="<?php echo esc_url( $options['social_instagram_community'] ); ?>" placeholder="https://instagram.com/..."></td></tr>
        <tr><th scope="row"><label for="cf_social_tiktok">TikTok</label></th><td><input type="url" class="regular-text" id="cf_social_tiktok" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_tiktok]" value="<?php echo esc_url( $options['social_tiktok'] ); ?>" placeholder="https://tiktok.com/@..."></td></tr>
        <tr><th scope="row"><label for="cf_social_youtube">YouTube</label></th><td><input type="url" class="regular-text" id="cf_social_youtube" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_youtube]" value="<?php echo esc_url( $options['social_youtube'] ); ?>" placeholder="https://youtube.com/..."></td></tr>
        <tr><th scope="row"><label for="cf_social_spotify">Spotify</label></th><td><input type="url" class="regular-text" id="cf_social_spotify" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_spotify]" value="<?php echo esc_url( $options['social_spotify'] ); ?>" placeholder="https://open.spotify.com/..."></td></tr>
        <tr><th scope="row"><label for="cf_social_soundcloud">SoundCloud</label></th><td><input type="url" class="regular-text" id="cf_social_soundcloud" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_soundcloud]" value="<?php echo esc_url( $options['social_soundcloud'] ); ?>" placeholder="https://soundcloud.com/..."></td></tr>
        <tr><th scope="row"><label for="cf_social_amazon">Amazon Music</label></th><td><input type="url" class="regular-text" id="cf_social_amazon" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_amazon]" value="<?php echo esc_url( $options['social_amazon'] ?: ( $options['social_amazon_music'] ?? '' ) ); ?>" placeholder="https://music.amazon.com/..."></td></tr>
        <tr><th scope="row"><label for="cf_social_x">X (Twitter)</label></th><td><input type="url" class="regular-text" id="cf_social_x" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_x]" value="<?php echo esc_url( $options['social_x'] ); ?>" placeholder="https://x.com/..."></td></tr>
    </table>
    <p class="description"><?php esc_html_e( 'Assign footer menu links under Appearance → Menus → Footer Menu. Suggested sections: Explore, Community, Legal.', 'collective-finity' ); ?></p>
    <?php
}

function collective_finity_render_theme_options_part_tab( $part ) {
    $parts      = collective_finity_get_theme_parts();
    $config     = $parts[ $part ];
    $choices    = collective_finity_get_templates_for_part( $part );
    $current_id = collective_finity_get_theme_part_template_id( $part );
    $field_key  = 'active_' . $part;
    $tab_title  = isset( $config['settings_tab'] ) ? $config['settings_tab'] : $config['menu_name'];
    ?>
    <h2><?php echo esc_html( $tab_title ); ?></h2>
    <p class="description cf-settings-tab-help">
        <?php
        printf(
            /* translators: %s: layout menu name, e.g. Header Layout */
            esc_html__( 'These are simple toggles and template assignment. To edit the actual visual layout, go to Collective Finity → %s.', 'collective-finity' ),
            esc_html( $config['menu_name'] )
        );
        ?>
    </p>
    <p class="description"><?php printf( esc_html__( 'Create unlimited %s designs and edit them with Elementor.', 'collective-finity' ), esc_html( strtolower( $config['menu_name'] ) ) ); ?></p>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="cf_<?php echo esc_attr( $part ); ?>_template"><?php esc_html_e( 'Active Template', 'collective-finity' ); ?></label></th>
            <td>
                <select name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[<?php echo esc_attr( $field_key ); ?>]" id="cf_<?php echo esc_attr( $part ); ?>_template">
                    <?php foreach ( $choices as $id => $label ) : ?>
                        <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $current_id, $id ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <p class="cf-theme-options-actions">
        <a class="button button-primary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . $config['post_type'] ) ); ?>"><?php echo esc_html( $config['add_new'] ); ?></a>
        <a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . $config['post_type'] ) ); ?>"><?php echo esc_html( $config['all_items'] ); ?></a>
        <?php if ( $current_id ) : ?>
            <a class="button" href="<?php echo esc_url( collective_finity_elementor_edit_url( $current_id ) ); ?>"><?php esc_html_e( 'Edit Active with Elementor', 'collective-finity' ); ?></a>
        <?php endif; ?>
        <a class="button" href="<?php echo esc_url( collective_finity_customizer_part_url( $part ) ); ?>"><?php esc_html_e( 'Open in Customizer', 'collective-finity' ); ?></a>
    </p>
    <?php submit_button( __( 'Save Assignment', 'collective-finity' ) ); ?>
    <?php
}

function collective_finity_render_theme_options_player_tab( $options ) {
    $option_key = collective_finity_theme_options_key();
    ?>
    <h2><?php esc_html_e( 'Music Player', 'collective-finity' ); ?></h2>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><?php esc_html_e( 'Global Player', 'collective-finity' ); ?></th>
            <td><label><input type="checkbox" name="<?php echo esc_attr( $option_key ); ?>[show_global_player]" value="1" <?php checked( $options['show_global_player'], 1 ); ?>> <?php esc_html_e( 'Show sticky global audio player', 'collective-finity' ); ?></label></td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_default_volume"><?php esc_html_e( 'Default Volume', 'collective-finity' ); ?></label></th>
            <td><input type="number" min="0" max="100" id="cf_default_volume" name="<?php echo esc_attr( $option_key ); ?>[default_volume]" value="<?php echo esc_attr( $options['default_volume'] ); ?>"></td>
        </tr>
    </table>

    <h3 class="cf-options-subhead"><?php esc_html_e( 'Music Library', 'collective-finity' ); ?></h3>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="cf_popular_min_views"><?php esc_html_e( 'Popular Section Minimum Views', 'collective-finity' ); ?></label></th>
            <td>
                <input type="number" min="0" step="1" id="cf_popular_min_views" name="<?php echo esc_attr( $option_key ); ?>[popular_min_views]" value="<?php echo esc_attr( (string) ( $options['popular_min_views'] ?? 50 ) ); ?>" class="small-text">
                <p class="description"><?php esc_html_e( 'Tracks need at least this many views to appear in the Popular carousel and Popular archive. Default: 50.', 'collective-finity' ); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

function collective_finity_render_theme_options_advanced_tab( $options ) {
    ?>
    <h2><?php esc_html_e( 'Advanced', 'collective-finity' ); ?></h2>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="cf_footer_copyright"><?php esc_html_e( 'Footer Copyright Override', 'collective-finity' ); ?></label></th>
            <td><input type="text" class="regular-text" id="cf_footer_copyright" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[footer_copyright]" value="<?php echo esc_attr( $options['footer_copyright'] ); ?>"></td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_custom_css"><?php esc_html_e( 'Custom CSS', 'collective-finity' ); ?></label></th>
            <td><textarea id="cf_custom_css" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[custom_css]" rows="10" class="large-text code"><?php echo esc_textarea( $options['custom_css'] ); ?></textarea></td>
        </tr>
    </table>
    <?php
}

function collective_finity_render_theme_options_donate_tab( $options ) {
    $option_key = collective_finity_theme_options_key();
    $messages   = (array) ( $options['donate_leadscreen_messages'] ?? array() );
    $messages   = array_pad( array_slice( $messages, 0, 5 ), 5, '' );
    ?>
    <h2><?php esc_html_e( 'Donate Page — Lead Screen', 'collective-finity' ); ?></h2>
    <p class="description"><?php esc_html_e( 'The scrolling message panel shown under "Make Music Infinite" on the Donate page.', 'collective-finity' ); ?></p>
    <table class="form-table" role="presentation">
        <?php foreach ( $messages as $i => $message ) : ?>
        <tr>
            <th scope="row"><label for="cf_leadscreen_msg_<?php echo esc_attr( $i ); ?>"><?php printf( esc_html__( 'Message %d', 'collective-finity' ), $i + 1 ); ?></label></th>
            <td><input type="text" class="regular-text" id="cf_leadscreen_msg_<?php echo esc_attr( $i ); ?>" maxlength="40" name="<?php echo esc_attr( $option_key ); ?>[donate_leadscreen_messages][]" value="<?php echo esc_attr( $message ); ?>"></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <th scope="row"><label for="cf_leadscreen_animation"><?php esc_html_e( 'Animation Type', 'collective-finity' ); ?></label></th>
            <td>
                <?php $cf_leadscreen_choices = array(
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
                ); ?>
                <select id="cf_leadscreen_animation" name="<?php echo esc_attr( $option_key ); ?>[donate_leadscreen_animation]">
                    <?php foreach ( $cf_leadscreen_choices as $cf_val => $cf_label ) : ?>
                        <option value="<?php echo esc_attr( $cf_val ); ?>" <?php selected( $options['donate_leadscreen_animation'] ?? 'scroll', $cf_val ); ?>><?php echo esc_html( $cf_label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_leadscreen_position"><?php esc_html_e( 'Text Position', 'collective-finity' ); ?></label></th>
            <td>
                <select id="cf_leadscreen_position" name="<?php echo esc_attr( $option_key ); ?>[donate_leadscreen_position]">
                    <option value="top" <?php selected( $options['donate_leadscreen_position'] ?? 'middle', 'top' ); ?>><?php esc_html_e( 'Top', 'collective-finity' ); ?></option>
                    <option value="middle" <?php selected( $options['donate_leadscreen_position'] ?? 'middle', 'middle' ); ?>><?php esc_html_e( 'Middle', 'collective-finity' ); ?></option>
                    <option value="bottom" <?php selected( $options['donate_leadscreen_position'] ?? 'middle', 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'collective-finity' ); ?></option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

function collective_finity_render_theme_options_ads_tab( $options ) {
    $zones       = $options['ad_zones'] ?? collective_finity_default_ad_zones();
    $labels      = collective_finity_ad_zone_labels();
    $descriptions = collective_finity_ad_zone_descriptions();
    $option_key  = collective_finity_theme_options_key();
    ?>
    <div class="cf-ad-manager-tab">
        <div class="notice notice-warning cf-ad-manager-notice">
            <p><strong><?php esc_html_e( 'Automatic ad exclusions', 'collective-finity' ); ?></strong> — <?php esc_html_e( 'Ads are automatically disabled on the Home page, Privacy Policy, Terms of Service, and Contact page — this cannot be changed here, by design.', 'collective-finity' ); ?></p>
        </div>

        <h2><?php esc_html_e( 'Preview Mode', 'collective-finity' ); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Preview Mode', 'collective-finity' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr( $option_key ); ?>[ad_preview_mode]" value="1" <?php checked( ! empty( $options['ad_preview_mode'] ) ); ?>>
                        <?php esc_html_e( 'Show labeled placeholder boxes instead of real ad code on the frontend', 'collective-finity' ); ?>
                    </label>
                </td>
            </tr>
        </table>

        <h2><?php esc_html_e( 'Google AdSense', 'collective-finity' ); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="cf_adsense_publisher_id"><?php esc_html_e( 'AdSense Publisher ID', 'collective-finity' ); ?></label></th>
                <td>
                    <input type="text" class="regular-text" id="cf_adsense_publisher_id" name="<?php echo esc_attr( $option_key ); ?>[adsense_publisher_id]" value="<?php echo esc_attr( $options['adsense_publisher_id'] ?? '' ); ?>" placeholder="ca-pub-XXXXXXXXXXXXXXXX">
                    <p class="description"><?php esc_html_e( 'Required for AdSense ad slots. Must match the ca-pub-XXXXXXXXXXXXXXXX format.', 'collective-finity' ); ?></p>
                </td>
            </tr>
        </table>

        <h2><?php esc_html_e( 'Ad Zones', 'collective-finity' ); ?></h2>
        <p class="description"><?php esc_html_e( 'Only users with permission to edit theme options can save ad code. Scripts and HTML are stored as entered.', 'collective-finity' ); ?></p>

        <div class="cf-ad-zone-cards">
            <?php foreach ( collective_finity_default_ad_zones() as $zone_id => $zone_defaults ) :
                $zone = wp_parse_args( $zones[ $zone_id ] ?? array(), $zone_defaults );
                $label = $labels[ $zone_id ] ?? $zone_id;
                $desc  = $descriptions[ $zone_id ] ?? '';
                ?>
                <div class="cf-ad-zone-card">
                    <div class="cf-ad-zone-card__header">
                        <h3><?php echo esc_html( $label ); ?></h3>
                        <label class="cf-ad-zone-toggle">
                            <input type="checkbox" name="<?php echo esc_attr( $option_key ); ?>[ad_zones][<?php echo esc_attr( $zone_id ); ?>][enabled]" value="1" <?php checked( ! empty( $zone['enabled'] ) ); ?>>
                            <?php esc_html_e( 'Enabled', 'collective-finity' ); ?>
                        </label>
                    </div>
                    <?php if ( $desc ) : ?>
                        <p class="description"><?php echo esc_html( $desc ); ?></p>
                    <?php endif; ?>
                    <?php if ( isset( $zone_defaults['frequency'] ) ) : ?>
                        <p>
                            <label for="cf_ad_freq_<?php echo esc_attr( $zone_id ); ?>"><?php esc_html_e( 'Show every Nth card in archive grid', 'collective-finity' ); ?></label><br>
                            <input type="number" min="2" max="50" id="cf_ad_freq_<?php echo esc_attr( $zone_id ); ?>" name="<?php echo esc_attr( $option_key ); ?>[ad_zones][<?php echo esc_attr( $zone_id ); ?>][frequency]" value="<?php echo esc_attr( (string) ( $zone['frequency'] ?? 8 ) ); ?>" class="small-text">
                        </p>
                    <?php endif; ?>
                    <p>
                        <label for="cf_ad_slot_<?php echo esc_attr( $zone_id ); ?>"><?php esc_html_e( 'AdSense Ad Slot ID', 'collective-finity' ); ?></label><br>
                        <input type="text" class="large-text" id="cf_ad_slot_<?php echo esc_attr( $zone_id ); ?>" name="<?php echo esc_attr( $option_key ); ?>[ad_zones][<?php echo esc_attr( $zone_id ); ?>][adsense_slot_id]" value="<?php echo esc_attr( $zone['adsense_slot_id'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. 1234567890', 'collective-finity' ); ?>">
                    </p>
                    <p>
                        <label for="cf_ad_code_<?php echo esc_attr( $zone_id ); ?>"><?php esc_html_e( 'Ad code (HTML / JavaScript)', 'collective-finity' ); ?></label>
                        <textarea id="cf_ad_code_<?php echo esc_attr( $zone_id ); ?>" name="<?php echo esc_attr( $option_key ); ?>[ad_zones][<?php echo esc_attr( $zone_id ); ?>][code]" rows="6" class="large-text code"><?php echo esc_textarea( $zone['code'] ?? '' ); ?></textarea>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function collective_finity_get_button_size_padding( $size ) {
    $map = array(
        'compact' => array( 'y' => 8, 'x' => 16 ),
        'regular' => array( 'y' => 12, 'x' => 24 ),
        'large'   => array( 'y' => 16, 'x' => 32 ),
    );
    return $map[ $size ] ?? $map['regular'];
}

function collective_finity_get_card_shadow_value( $shadow ) {
    $map = array(
        'none'   => 'none',
        'soft'   => '0 14px 28px -12px rgba(0,0,0,0.55)',
        'strong' => '0 24px 48px -8px rgba(0,0,0,0.72)',
    );
    return $map[ $shadow ] ?? $map['soft'];
}

function collective_finity_get_transition_speed_value( $speed ) {
    $map = array(
        'fast'   => '150ms',
        'normal' => '250ms',
        'slow'   => '400ms',
    );
    return $map[ $speed ] ?? $map['normal'];
}

function collective_finity_get_section_gap_value( $spacing ) {
    $map = array(
        'compact'  => '40px',
        'default'  => '60px',
        'spacious' => '84px',
    );
    return $map[ $spacing ] ?? $map['default'];
}

/**
 * Output data attributes for design-control body toggles.
 */
function collective_finity_render_body_design_attributes() {
    $options = collective_finity_get_theme_options();
    $attrs   = array(
        'data-btn-hover'  => sanitize_key( $options['button_hover_effect'] ),
        'data-card-hover' => sanitize_key( $options['card_hover_effect'] ),
    );
    $parts = array();
    foreach ( $attrs as $name => $value ) {
        if ( $value ) {
            $parts[] = sprintf( '%s="%s"', esc_attr( $name ), esc_attr( $value ) );
        }
    }
    if ( $parts ) {
        echo ' ' . implode( ' ', $parts );
    }
}

function collective_finity_output_theme_option_styles() {
    $options = collective_finity_get_theme_options();

    $primary      = $options['primary_color'];
    $accent       = $options['accent_color'];
    $accent_hover = collective_finity_adjust_hex_brightness( $primary, 40 );
    $accent_dim   = collective_finity_hex_to_rgba( $primary, 0.14 );
    $body_stack   = collective_finity_get_font_stack( $options['body_font'], 'inter' );
    $head_stack   = collective_finity_get_font_stack( $options['heading_font'], 'space-mono' );
    $btn_padding  = collective_finity_get_button_size_padding( $options['button_size'] );

    $vars  = '--primary-color:' . esc_attr( $primary ) . ';';
    $vars .= '--secondary-color:' . esc_attr( $accent ) . ';';
    $vars .= '--cf-accent:' . esc_attr( $primary ) . ';';
    $vars .= '--cf-accent-hover:' . esc_attr( $accent_hover ) . ';';
    $vars .= '--cf-accent-dim:' . esc_attr( $accent_dim ) . ';';
    $vars .= '--cf-bg-darkest:' . esc_attr( $accent ) . ';';
    $vars .= '--cf-bg-dark:' . esc_attr( $accent ) . ';';
    $vars .= '--cf-bg-panel:' . esc_attr( $accent ) . ';';
    $vars .= '--cf-text:' . esc_attr( $options['text_color'] ) . ';';
    $vars .= '--cf-text-2:' . esc_attr( $options['text_muted_color'] ) . ';';
    $vars .= '--cf-bg-card:' . esc_attr( $options['card_bg_color'] ) . ';';
    $vars .= '--cf-border:' . esc_attr( $options['border_color'] ) . ';';
    $vars .= '--cf-link:' . esc_attr( $options['link_color'] ) . ';';
    $vars .= '--cf-link-hover:' . esc_attr( $options['link_hover_color'] ) . ';';
    $vars .= '--cf-font-size-base:' . absint( $options['base_font_size'] ) . 'px;';
    $vars .= '--cf-h1-size:' . absint( $options['h1_font_size'] ) . 'px;';
    $vars .= '--cf-h2-size:' . absint( $options['h2_font_size'] ) . 'px;';
    $vars .= '--cf-h3-size:' . absint( $options['h3_font_size'] ) . 'px;';
    $vars .= '--cf-heading-weight:' . esc_attr( $options['heading_font_weight'] ) . ';';
    $vars .= '--cf-heading-tracking:' . esc_attr( $options['heading_letter_spacing'] ) . 'em;';
    $vars .= '--cf-body-line-height:' . esc_attr( $options['body_line_height'] ) . ';';
    $vars .= '--cf-btn-radius:' . absint( $options['button_radius'] ) . 'px;';
    $vars .= '--cf-btn-padding-y:' . absint( $btn_padding['y'] ) . 'px;';
    $vars .= '--cf-btn-padding-x:' . absint( $btn_padding['x'] ) . 'px;';
    $vars .= '--cf-card-radius:' . absint( $options['card_radius'] ) . 'px;';
    $vars .= '--cf-card-border-width:' . absint( $options['card_border_width'] ) . 'px;';
    $vars .= '--cf-card-shadow:' . esc_attr( collective_finity_get_card_shadow_value( $options['card_shadow'] ) ) . ';';
    $vars .= '--cf-transition-speed:' . esc_attr( collective_finity_get_transition_speed_value( $options['transition_speed'] ) ) . ';';
    $vars .= '--cf-section-gap:' . esc_attr( collective_finity_get_section_gap_value( $options['section_spacing'] ) ) . ';';
    $vars .= '--cf-body:' . $body_stack . ';';
    $vars .= '--cf-mono:' . $head_stack . ';';
    $vars .= '--cf-sidebar-logo-size:' . absint( $options['sidebar_logo_size'] ) . 'px;';
    $vars .= '--cf-mobile-logo-size:' . absint( $options['mobile_logo_size'] ) . 'px;';

    $css = ':root{' . $vars . '}';
    if ( ! empty( $options['custom_css'] ) ) {
        $css .= "\n" . $options['custom_css'];
    }
    if ( ! $options['show_global_player'] ) {
        $css .= "\n#cf-global-audio-player{display:none!important;}body{padding-bottom:0!important;}";
    }

    // Attach to the shell stylesheet so these :root overrides win over cf-shell.css defaults.
    $handle = wp_style_is( 'cf-shell', 'enqueued' ) ? 'cf-shell' : 'main-style';
    wp_add_inline_style( $handle, $css );
}
add_action( 'wp_enqueue_scripts', 'collective_finity_output_theme_option_styles', 20 );

function collective_finity_render_back_to_top() {
    if ( ! collective_finity_get_theme_option( 'enable_back_to_top' ) ) {
        return;
    }
    echo '<button type="button" id="cf-back-to-top" class="cf-back-to-top" aria-label="' . esc_attr__( 'Back to top', 'collective-finity' ) . '">';
    echo '<svg class="cf-back-to-top-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4l-7 7h4v9h6v-9h4z"/></svg>';
    echo '</button>';
}
add_action( 'wp_footer', 'collective_finity_render_back_to_top', 5 );

function collective_finity_theme_options_admin_bar( $wp_admin_bar ) {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }
    $wp_admin_bar->add_node( array(
        'id'    => 'cf-theme-options',
        'title' => __( 'Collective Finity', 'collective-finity' ),
        'href'  => admin_url( 'admin.php?page=' . collective_finity_admin_menu_slug() ),
    ) );
    $wp_admin_bar->add_node( array(
        'id'     => 'cf-theme-options-settings',
        'parent' => 'cf-theme-options',
        'title'  => __( 'Theme Options', 'collective-finity' ),
        'href'   => admin_url( 'admin.php?page=collective-finity-options' ),
    ) );
}
add_action( 'admin_bar_menu', 'collective_finity_theme_options_admin_bar', 75 );

/**
 * Allowed Donate page Lead Screen animation types.
 *
 * @return string[]
 */
function collective_finity_donate_leadscreen_animations() {
    return array( 'scroll', 'fade', 'typewriter', 'slide-up', 'glitch', 'zoom-pulse', 'flip', 'wave', 'neon-flicker', 'blur-focus' );
}

/**
 * Render the Donate page "Lead Screen" — an admin-editable scrolling message
 * strip that fills the space below the Make Music Infinite card.
 */
function collective_finity_render_donate_leadscreen() {
    $options   = collective_finity_get_theme_options();
    $messages  = array_filter( array_map( 'trim', (array) ( $options['donate_leadscreen_messages'] ?? array() ) ) );
    $animation = in_array( $options['donate_leadscreen_animation'] ?? 'scroll', collective_finity_donate_leadscreen_animations(), true )
        ? $options['donate_leadscreen_animation']
        : 'scroll';
    $position  = in_array( $options['donate_leadscreen_position'] ?? 'middle', array( 'top', 'middle', 'bottom' ), true )
        ? $options['donate_leadscreen_position']
        : 'middle';

    if ( empty( $messages ) ) {
        return;
    }

    $cf_leadscreen_slot     = 3; // seconds each message occupies
    $cf_leadscreen_duration = max( 1, count( $messages ) ) * $cf_leadscreen_slot;
    ?>
    <div class="cf-leadscreen cf-leadscreen--<?php echo esc_attr( $animation ); ?> cf-leadscreen--pos-<?php echo esc_attr( $position ); ?>" aria-hidden="false">
        <div class="cf-leadscreen__grid" aria-hidden="true"></div>
        <div class="cf-leadscreen__scanlines" aria-hidden="true"></div>
        <div class="cf-leadscreen__glass" aria-hidden="true"></div>

        <?php if ( 'scroll' === $animation ) : ?>

            <div class="cf-leadscreen__track">
                <div class="cf-leadscreen__row">
                    <?php foreach ( array_merge( $messages, $messages ) as $message ) : ?>
                        <span class="cf-leadscreen__msg"><?php echo esc_html( $message ); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif ( 'wave' === $animation ) : ?>

            <div class="cf-leadscreen__cycle">
                <?php foreach ( $messages as $i => $message ) : ?>
                    <span class="cf-leadscreen__msg cf-leadscreen__msg--wave" style="animation-duration: <?php echo esc_attr( $cf_leadscreen_duration ); ?>s; animation-delay: <?php echo esc_attr( $i * $cf_leadscreen_slot ); ?>s;">
                        <?php foreach ( preg_split( '//u', $message, -1, PREG_SPLIT_NO_EMPTY ) as $li => $letter ) : ?>
                            <span class="cf-leadscreen__letter" style="--i: <?php echo (int) $li; ?>;"><?php echo esc_html( ' ' === $letter ? "\xC2\xA0" : $letter ); ?></span>
                        <?php endforeach; ?>
                    </span>
                <?php endforeach; ?>
            </div>

        <?php elseif ( 'typewriter' === $animation ) : ?>

            <div class="cf-leadscreen__cycle">
                <?php foreach ( $messages as $i => $message ) :
                    $chars = function_exists( 'mb_strlen' ) ? mb_strlen( $message ) : strlen( $message );
                    ?>
                    <span class="cf-leadscreen__msg cf-leadscreen__msg--typewriter" style="animation-duration: <?php echo esc_attr( $cf_leadscreen_duration ); ?>s; animation-delay: <?php echo esc_attr( $i * $cf_leadscreen_slot ); ?>s; --cf-chars: <?php echo (int) max( 1, $chars ); ?>;"><?php echo esc_html( $message ); ?></span>
                <?php endforeach; ?>
            </div>

        <?php else : ?>

            <div class="cf-leadscreen__cycle">
                <?php foreach ( $messages as $i => $message ) : ?>
                    <span class="cf-leadscreen__msg" style="animation-duration: <?php echo esc_attr( $cf_leadscreen_duration ); ?>s; animation-delay: <?php echo esc_attr( $i * $cf_leadscreen_slot ); ?>s"><?php echo esc_html( $message ); ?></span>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>
    <?php
}
