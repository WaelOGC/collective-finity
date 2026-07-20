<?php
/**
 * Collective Finity Custom Theme Functions and Definitions.
 * Fully upgraded with advanced Music Meta Boxes, Plays/Likes Counters, Elementor Areas, and secure handlers.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

require_once get_template_directory() . '/inc/theme-parts.php';
require_once get_template_directory() . '/inc/admin-menu.php';
require_once get_template_directory() . '/inc/cpt-theme-templates.php';
require_once get_template_directory() . '/inc/ad-manager.php';
require_once get_template_directory() . '/inc/theme-options.php';
require_once get_template_directory() . '/inc/admin-theme-builder.php';
require_once get_template_directory() . '/inc/customizer-theme-parts.php';
require_once get_template_directory() . '/inc/customizer-theme-options.php';
require_once get_template_directory() . '/inc/legal-pages.php';
require_once get_template_directory() . '/inc/blog.php';
require_once get_template_directory() . '/inc/faq.php';

/**
 * 1. BASIC THEME SUPPORT & MENUS
 */
function collective_finity_setup() {
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'menus' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'customize-selective-refresh-widgets' );

    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'collective-finity' ),
        'footer'  => __( 'Footer Menu', 'collective-finity' ),
    ) );
}
add_action( 'after_setup_theme', 'collective_finity_setup' );

/**
 * Body classes for layout features.
 */
function collective_finity_body_classes( $classes ) {
    $classes[] = 'cf-has-right-player';
    if ( ! collective_finity_get_theme_option( 'enable_glow_effects', 1 ) ) {
        $classes[] = 'cf-glow-disabled';
    }
    return $classes;
}
add_filter( 'body_class', 'collective_finity_body_classes' );

/**
 * Load theme translations.
 */
function collective_finity_load_textdomain() {
    load_theme_textdomain( 'collective-finity', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'collective_finity_load_textdomain' );

/**
 * Default cover art URL (bundled SVG fallback).
 */
function collective_finity_default_art_url() {
    $jpg_path = get_template_directory() . '/images/default-art.jpg';
    if ( file_exists( $jpg_path ) ) {
        return get_template_directory_uri() . '/images/default-art.jpg';
    }

    return get_template_directory_uri() . '/images/default-art.svg';
}

/**
 * Whether a track should display BPM on the frontend.
 */
function collective_finity_track_show_bpm( $track_id ) {
    return (bool) get_post_meta( $track_id, 'track_show_bpm', true );
}

/**
 * Whether a track should display Key on the frontend.
 */
function collective_finity_track_show_key( $track_id ) {
    return (bool) get_post_meta( $track_id, 'track_show_key', true );
}

/**
 * Whether a track should display the Lyrics/Story section on the frontend.
 * Defaults to enabled when unset so existing tracks keep the section visible.
 *
 * @param int $track_id Post ID.
 * @return bool
 */
function collective_finity_track_show_lyrics( $track_id ) {
    return collective_finity_track_meta_enabled_by_default( $track_id, 'track_show_lyrics' );
}

/**
 * Track meta visibility toggle that defaults to enabled when unset.
 * Used for visualizer styles and streaming platform visibility so existing
 * tracks keep their current frontend behavior until an admin opts out.
 *
 * @param int    $track_id Post ID.
 * @param string $meta_key Post meta key.
 * @return bool
 */
function collective_finity_track_meta_enabled_by_default( $track_id, $meta_key ) {
    $value = get_post_meta( $track_id, $meta_key, true );
    if ( $value === '' || $value === false ) {
        return true;
    }
    return (string) $value === '1';
}

/**
 * Available audio visualizer styles for single track pages.
 *
 * @return array<string, string> Style slug => label.
 */
function collective_finity_track_visualizer_styles() {
    return array(
        'spectrum_bars'       => __( 'Spectrum Equalizer Bars', 'collective-finity' ),
        'aurora_fill'         => __( 'Aurora Fill', 'collective-finity' ),
        'ember_drift'         => __( 'Ember Drift', 'collective-finity' ),
        'crimson_pulse_ring'  => __( 'Crimson Pulse Ring', 'collective-finity' ),
        'smoke_wisp'          => __( 'Smoke Wisp', 'collective-finity' ),
        'shard_fracture'      => __( 'Shard Fracture', 'collective-finity' ),
        'radar_sweep'         => __( 'Radar Sweep', 'collective-finity' ),
        'ink_bleed'           => __( 'Ink Bleed', 'collective-finity' ),
        'frost_veins'         => __( 'Frost Veins', 'collective-finity' ),
        'blood_drip_trails'   => __( 'Blood Drip Trails', 'collective-finity' ),
        'halo_breathe'        => __( 'Halo Breathe', 'collective-finity' ),
        'fracture_cracks'     => __( 'Fracture Cracks', 'collective-finity' ),
    );
}

/**
 * Post meta key for a visualizer style show/hide toggle.
 *
 * @param string $style_slug Visualizer style slug.
 * @return string
 */
function collective_finity_track_visualizer_meta_key( $style_slug ) {
    return 'track_show_visualizer_' . $style_slug;
}

/**
 * Whether a visualizer style is enabled for a track (defaults to on).
 *
 * @param int    $track_id   Post ID.
 * @param string $style_slug Visualizer style slug.
 * @return bool
 */
function collective_finity_track_show_visualizer( $track_id, $style_slug ) {
    return collective_finity_track_meta_enabled_by_default(
        $track_id,
        collective_finity_track_visualizer_meta_key( $style_slug )
    );
}

/**
 * Streaming platforms available on track pages.
 *
 * @return array<string, array{label:string, meta:string, show_meta:string, icon:string, placeholder:string}>
 */
function collective_finity_track_streaming_platforms() {
    return array(
        'spotify'     => array(
            'label'       => 'Spotify',
            'meta'        => 'track_spotify_url',
            'show_meta'   => 'track_show_spotify',
            'icon'        => 'dashicons-controls-play',
            'placeholder' => 'https://open.spotify.com/...',
        ),
        'apple'       => array(
            'label'       => 'Apple Music',
            'meta'        => 'track_apple_url',
            'show_meta'   => 'track_show_apple',
            'icon'        => 'dashicons-smartphone',
            'placeholder' => 'https://music.apple.com/...',
        ),
        'soundcloud'  => array(
            'label'       => 'SoundCloud',
            'meta'        => 'track_soundcloud_url',
            'show_meta'   => 'track_show_soundcloud',
            'icon'        => 'dashicons-cloud',
            'placeholder' => 'https://soundcloud.com/...',
        ),
        'youtube'     => array(
            'label'       => 'YouTube',
            'meta'        => 'track_youtube_url',
            'show_meta'   => 'track_show_youtube',
            'icon'        => 'dashicons-video-alt3',
            'placeholder' => 'https://youtube.com/...',
        ),
        'bandcamp'    => array(
            'label'       => 'Bandcamp',
            'meta'        => 'track_bandcamp_url',
            'show_meta'   => 'track_show_bandcamp',
            'icon'        => 'dashicons-format-audio',
            'placeholder' => 'https://bandcamp.com/...',
        ),
        'amazon'      => array(
            'label'       => 'Amazon Music',
            'meta'        => 'track_amazon_url',
            'show_meta'   => 'track_show_amazon',
            'icon'        => 'dashicons-cart',
            'placeholder' => 'https://music.amazon.com/...',
        ),
        'google_play' => array(
            'label'       => 'Google Play Music',
            'meta'        => 'track_google_play_url',
            'show_meta'   => 'track_show_google_play',
            'icon'        => 'dashicons-playlist-audio',
            'placeholder' => 'https://play.google.com/music/...',
        ),
    );
}

/**
 * Whether a streaming platform link should display for a track (defaults to on).
 *
 * @param int    $track_id  Post ID.
 * @param string $platform  Platform slug.
 * @return bool
 */
function collective_finity_track_show_streaming( $track_id, $platform ) {
    $platforms = collective_finity_track_streaming_platforms();
    if ( empty( $platforms[ $platform ]['show_meta'] ) ) {
        return true;
    }
    return collective_finity_track_meta_enabled_by_default( $track_id, $platforms[ $platform ]['show_meta'] );
}

/**
 * Track view count (page visits).
 */
function collective_finity_track_views( $track_id ) {
    return (int) get_post_meta( $track_id, '_cf_track_plays', true );
}

/**
 * Admin-configurable minimum views for Popular Music Library section.
 *
 * @return int
 */
function collective_finity_popular_min_views() {
    return max( 0, absint( collective_finity_get_theme_option( 'popular_min_views', 50 ) ) );
}

/**
 * Current tracks-archive sub-view: '', 'all', or 'popular'.
 *
 * @return string
 */
function collective_finity_get_tracks_archive_view() {
    $view = get_query_var( 'cf_tracks_view', '' );
    if ( ! is_string( $view ) || '' === $view ) {
        $view = isset( $_GET['view'] ) ? sanitize_key( wp_unslash( $_GET['view'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    }
    return in_array( $view, array( 'all', 'popular' ), true ) ? $view : '';
}

/**
 * URL for the full tracks listing ("Show all" on Latest Tracks).
 *
 * @return string
 */
function collective_finity_get_tracks_all_url() {
    $base = get_post_type_archive_link( 'tracks' );
    if ( ! $base ) {
        $base = home_url( '/tracks/' );
    }
    return trailingslashit( $base ) . 'all/';
}

/**
 * URL for the Popular tracks archive ("Show all" on Popular).
 *
 * @return string
 */
function collective_finity_get_tracks_popular_url() {
    $base = get_post_type_archive_link( 'tracks' );
    if ( ! $base ) {
        $base = home_url( '/tracks/' );
    }
    return trailingslashit( $base ) . 'popular/';
}

/**
 * Pretty URL rewrites for Music Library sub-views.
 */
function collective_finity_register_tracks_view_rewrites() {
    add_rewrite_rule( '^tracks/all/?$', 'index.php?post_type=tracks&cf_tracks_view=all', 'top' );
    add_rewrite_rule( '^tracks/popular/?$', 'index.php?post_type=tracks&cf_tracks_view=popular', 'top' );
}
add_action( 'init', 'collective_finity_register_tracks_view_rewrites', 20 );

/**
 * @param array<int, string> $vars Query vars.
 * @return array<int, string>
 */
function collective_finity_tracks_view_query_vars( $vars ) {
    $vars[] = 'cf_tracks_view';
    return $vars;
}
add_filter( 'query_vars', 'collective_finity_tracks_view_query_vars' );

/**
 * Flush rewrite rules once after adding tracks view endpoints.
 */
function collective_finity_maybe_flush_tracks_view_rewrites() {
    if ( '1' === get_option( 'cf_tracks_view_rewrite_v1' ) ) {
        return;
    }
    flush_rewrite_rules( false );
    update_option( 'cf_tracks_view_rewrite_v1', '1' );
}
add_action( 'init', 'collective_finity_maybe_flush_tracks_view_rewrites', 99 );

/**
 * Document titles for Music Library sub-views.
 *
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function collective_finity_tracks_view_document_title( $parts ) {
    if ( ! is_post_type_archive( 'tracks' ) ) {
        return $parts;
    }
    $view = collective_finity_get_tracks_archive_view();
    if ( 'all' === $view ) {
        $parts['title'] = __( 'All Tracks', 'collective-finity' );
    } elseif ( 'popular' === $view ) {
        $parts['title'] = __( 'Popular Tracks', 'collective-finity' );
    }
    return $parts;
}
add_filter( 'document_title_parts', 'collective_finity_tracks_view_document_title' );

/**
 * Approved comment count for a track.
 */
function collective_finity_track_comments_count( $track_id ) {
    return (int) get_comments_number( $track_id );
}

/**
 * Build a normalized playable queue from all published tracks (newest first).
 * Used by the player for continuous library playback and the track-library AJAX endpoint.
 *
 * @return array<int, array{url:string,title:string,artist:string,art:string,id:int}>
 */
function collective_finity_get_published_track_queue() {
    $tracks = get_posts(
        array(
            'post_type'      => 'tracks',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );

    $queue = array();

    foreach ( $tracks as $track ) {
        $track_id = $track->ID;
        $url      = get_post_meta( $track_id, 'track_preview_url', true );
        if ( ! $url ) {
            $url = get_post_meta( $track_id, 'track_audio_url', true );
        }
        if ( ! $url ) {
            continue;
        }

        $cover = get_post_meta( $track_id, 'track_cover_url', true );
        if ( ! $cover ) {
            $cover = get_the_post_thumbnail_url( $track_id, 'medium' );
        }
        if ( ! $cover ) {
            $cover = collective_finity_default_art_url();
        }

        $artists = wp_get_post_terms( $track_id, 'track_artist', array( 'fields' => 'names' ) );
        $artist  = ! empty( $artists ) ? $artists[0] : 'Collective Finity';

        $queue[] = array(
            'url'    => $url,
            'title'  => $track->post_title,
            'artist' => $artist,
            'art'    => $cover,
            'id'     => $track_id,
        );
    }

    return $queue;
}

/**
 * Render social share buttons.
 *
 * @param string $url   Share URL.
 * @param string $title Share title.
 * @param string $context Optional CSS context slug.
 */
function collective_finity_render_share_buttons( $url, $title, $context = 'default' ) {
    $share_url    = $url;
    $share_title  = $title;
    $share_context = $context;
    get_template_part( 'template-parts/share', 'social' );
}

/**
 * Site logo URL: Custom Logo, then site icon, then default art.
 */
function collective_finity_site_logo_url( $size = 'thumbnail' ) {
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $logo_image = wp_get_attachment_image_src( $custom_logo_id, $size );
        if ( ! empty( $logo_image[0] ) ) {
            return $logo_image[0];
        }
    }

    $site_icon_id = get_option( 'site_icon' );
    if ( $site_icon_id ) {
        $icon_image = wp_get_attachment_image_src( $site_icon_id, $size );
        if ( ! empty( $icon_image[0] ) ) {
            return $icon_image[0];
        }
    }

    return collective_finity_default_art_url();
}

/**
 * Display name for the artist / site brand.
 */
function collective_finity_brand_name() {
    $name = get_bloginfo( 'name' );
    return $name ? $name : 'Collective Finity';
}

/**
 * Brand mark markup for the shell (left sidebar + mobile header).
 *
 * Returns a custom uploaded logo image when the matching Theme Option is set,
 * otherwise falls back to the default CSS diamond mark. Mobile falls back to the
 * sidebar logo, then to the diamond.
 *
 * @param string $context 'sidebar' or 'mobile'.
 * @return string
 */
function collective_finity_brand_logo_markup( $context = 'sidebar' ) {
    $sidebar_id = absint( collective_finity_get_theme_option( 'sidebar_logo' ) );
    $mobile_id  = absint( collective_finity_get_theme_option( 'mobile_logo' ) );

    if ( 'mobile' === $context ) {
        $logo_id = $mobile_id ? $mobile_id : $sidebar_id;
    } else {
        $logo_id = $sidebar_id;
    }

    if ( $logo_id ) {
        $src = wp_get_attachment_image_url( $logo_id, 'thumbnail' );
        if ( $src ) {
            return sprintf(
                '<img class="cf-brand-logo-img" src="%s" alt="" aria-hidden="true">',
                esc_url( $src )
            );
        }
    }

    return '<span class="cf-brand-mark" aria-hidden="true"></span>';
}

/**
 * Brand mark markup for the site footer.
 *
 * Returns a custom uploaded logo image when the Footer Logo Theme Option is set,
 * otherwise falls back to the default CSS diamond mark.
 *
 * @return string
 */
function collective_finity_footer_logo_markup() {
    $logo_id = absint( collective_finity_get_theme_option( 'footer_logo' ) );
    $size    = absint( collective_finity_get_theme_option( 'footer_logo_size' ) ) ?: 40;

    if ( $logo_id ) {
        $src = wp_get_attachment_image_url( $logo_id, 'thumbnail' );
        if ( $src ) {
            return sprintf(
                '<img class="cf-footer-brand-logo-img" src="%s" alt="" aria-hidden="true" style="width:%dpx;height:%dpx;">',
                esc_url( $src ),
                $size,
                $size
            );
        }
    }

    return '<span class="cf-footer-brand-mark" aria-hidden="true"></span>';
}

/**
 * Build the Google Fonts stylesheet URL for the selected body + heading fonts.
 *
 * @return string Empty string when both selections are system fonts.
 */
function collective_finity_google_fonts_url() {
    $fonts   = collective_finity_get_font_choices();
    $body    = collective_finity_get_theme_option( 'body_font' );
    $heading = collective_finity_get_theme_option( 'heading_font' );

    $body    = isset( $fonts[ $body ] ) ? $body : 'inter';
    $heading = isset( $fonts[ $heading ] ) ? $heading : 'space-mono';

    $families = array();
    foreach ( array( $body, $heading ) as $key ) {
        $family = $fonts[ $key ]['google'];
        if ( $family && ! in_array( $family, $families, true ) ) {
            $families[] = $family;
        }
    }

    if ( empty( $families ) ) {
        return '';
    }

    $url = 'https://fonts.googleapis.com/css2';
    foreach ( $families as $index => $family ) {
        $url .= ( 0 === $index ? '?' : '&' ) . 'family=' . $family;
    }
    $url .= '&display=swap';

    return $url;
}

/**
 * Enqueue the dynamic Google Fonts stylesheet based on Theme Options.
 */
function collective_finity_enqueue_google_fonts() {
    $url = collective_finity_google_fonts_url();
    if ( $url ) {
        wp_enqueue_style( 'cf-google-fonts', $url, array(), null );
    }
}
add_action( 'wp_enqueue_scripts', 'collective_finity_enqueue_google_fonts' );

/**
 * Inline SVG icon set (ported from the design system in design-reference/shared-ui.js).
 *
 * Returns line-based SVG markup so the shell matches the multi-page design export
 * instead of relying on dashicons.
 *
 * @param string $name   Icon slug.
 * @param int    $size   Pixel size.
 * @param bool   $filled Whether fillable icons (heart/star) render filled.
 * @return string
 */
function collective_finity_icon( $name, $size = 18, $filled = false ) {
    $icons = array(
        'home'        => '<path d="M4 11.5 12 4l8 7.5"/><path d="M6 10v9a1 1 0 0 0 1 1h4v-6h2v6h4a1 1 0 0 0 1-1v-9"/>',
        'library'     => '<rect x="4" y="4" width="6" height="16" rx="1"/><rect x="14" y="4" width="6" height="16" rx="1"/>',
        'blog'        => '<rect x="4" y="4" width="16" height="16" rx="2"/><line x1="7.5" y1="9" x2="16.5" y2="9"/><line x1="7.5" y1="12.5" x2="16.5" y2="12.5"/><line x1="7.5" y1="16" x2="13" y2="16"/>',
        'albums'      => '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="2"/>',
        'about'       => '<circle cx="12" cy="12" r="8.5"/><line x1="12" y1="11" x2="12" y2="16"/><circle cx="12" cy="7.5" r="0.6" fill="currentColor"/>',
        'community'   => '<circle cx="9" cy="9" r="3"/><path d="M3.5 19c0-3 2.5-5 5.5-5s5.5 2 5.5 5"/><circle cx="17" cy="9" r="2.4"/><path d="M15.5 14.2c2.4.3 4.2 2.1 4.2 4.8"/>',
        'heart'       => '<path d="M12 20s-7-4.35-9.5-8.8C.8 8 2 4.5 5.5 4.5c2 0 3.3 1.2 6.5 3.9C15.2 5.7 16.5 4.5 18.5 4.5 22 4.5 23.2 8 21.5 11.2 19 15.65 12 20 12 20z"/>',
        'playlist'    => '<line x1="4" y1="6" x2="16" y2="6"/><line x1="4" y1="12" x2="16" y2="12"/><line x1="4" y1="18" x2="11" y2="18"/><circle cx="19" cy="15" r="2.4"/><line x1="21.4" y1="15" x2="21.4" y2="8"/>',
        'user'        => '<circle cx="12" cy="8" r="3.4"/><path d="M4.5 20c1-4 4-6 7.5-6s6.5 2 7.5 6"/>',
        'bell'        => '<path d="M6 16v-4.5A6 6 0 0 1 12 5.5v0a6 6 0 0 1 6 6V16l1.6 2.2H4.4z"/><path d="M9.5 19.5a2.5 2.5 0 0 0 5 0"/>',
        'mail'        => '<rect x="3" y="5.5" width="18" height="13" rx="1.5"/><path d="M3.5 6.5 12 13l8.5-6.5"/>',
        'menu'        => '<line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/>',
        'close'       => '<line x1="6" y1="6" x2="18" y2="18"/><line x1="18" y1="6" x2="6" y2="18"/>',
        'chevronLeft' => '<polyline points="15 6 9 12 15 18"/>',
        'chevronDown' => '<polyline points="6 9 12 15 18 9"/>',
        'star'        => '<path d="M12 3.5 14.5 9l6 .6-4.5 4 1.3 5.9-5.3-3.1-5.3 3.1 1.3-5.9-4.5-4 6-.6z"/>',
        'share'       => '<circle cx="18" cy="5" r="2.2"/><circle cx="6" cy="12" r="2.2"/><circle cx="18" cy="19" r="2.2"/><line x1="8.2" y1="10.8" x2="15.8" y2="6.2"/><line x1="8.2" y1="13.2" x2="15.8" y2="17.8"/>',
        'play'        => '<path d="M7 5.5v13l11-6.5z" fill="currentColor" stroke="none"/>',
        'pause'       => '<rect x="6.5" y="5" width="4" height="14" fill="currentColor" stroke="none"/><rect x="13.5" y="5" width="4" height="14" fill="currentColor" stroke="none"/>',
        'skipBack'    => '<polygon points="18 5 8 12 18 19" fill="currentColor" stroke="none"/><line x1="6" y1="5" x2="6" y2="19" stroke-width="2.4"/>',
        'skipFwd'     => '<polygon points="6 5 16 12 6 19" fill="currentColor" stroke="none"/><line x1="18" y1="5" x2="18" y2="19" stroke-width="2.4"/>',
        'shuffle'     => '<polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/><polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/><line x1="4" y1="4" x2="9" y2="9"/>',
        'repeat'      => '<polyline points="17 2 21 6 17 10"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 22 3 18 7 14"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>',
        'volume'      => '<path d="M4 9v6h4l5 4V5L8 9z"/><path d="M17 8.5a5 5 0 0 1 0 7"/><path d="M19.5 6a8.5 8.5 0 0 1 0 12"/>',
        'lock'        => '<rect x="5.5" y="10.5" width="13" height="9" rx="1.5"/><path d="M8 10.5V8a4 4 0 0 1 8 0v2.5"/>',
        'search'      => '<circle cx="10.5" cy="10.5" r="6.5"/><line x1="15.5" y1="15.5" x2="21" y2="21"/>',
    );

    if ( ! isset( $icons[ $name ] ) ) {
        return '';
    }

    $inner = $icons[ $name ];
    if ( $filled && in_array( $name, array( 'heart', 'star' ), true ) ) {
        $inner = str_replace( '<path d=', '<path fill="currentColor" d=', $inner );
    }

    return sprintf(
        '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
        (int) $size,
        $inner
    );
}

/**
 * Deterministic gradient fallback for album art (mirrors design-reference/shared-data.js gradientFor()).
 *
 * @param int $seed Numeric seed (e.g. post ID).
 * @return string CSS gradient value.
 */
function collective_finity_gradient_for( $seed ) {
    $seed    = (int) $seed;
    $px      = 15 + ( $seed * 31 ) % 65;
    $py      = 10 + ( $seed * 53 ) % 70;
    $light1  = number_format( 0.30 + ( ( $seed * 13 ) % 18 ) / 100, 2 );
    $chroma1 = number_format( 0.10 + ( ( $seed * 7 ) % 5 ) / 100, 2 );

    return sprintf(
        'radial-gradient(circle at %1$d%% %2$d%%, oklch(%3$s %4$s 85) 0%%, oklch(0.15 0.025 60) 45%%, oklch(0.06 0.01 40) 100%%)',
        $px,
        $py,
        $light1,
        $chroma1
    );
}

/**
 * Shell navigation model (left sidebar + mobile drawer share this single source).
 *
 * Mirrors the nav/secondaryNav lists in design-reference/Shell.dc.html, scoped to the
 * real theme routes. Each item: id, label, url, icon, active.
 *
 * @return array{main: array<int, array<string, mixed>>, secondary: array<int, array<string, mixed>>}
 */
function collective_finity_get_shell_nav() {
    $tracks_url = get_post_type_archive_link( 'tracks' );
    $albums_url = get_post_type_archive_link( 'albums' );

    $is_library = is_post_type_archive( 'tracks' ) || is_singular( 'tracks' )
        || is_tax( 'music_genre' ) || is_tax( 'track_artist' );
    $is_albums = is_post_type_archive( 'albums' ) || is_singular( 'albums' );

    $favorites_url = is_user_logged_in() ? home_url( '/cf-profile#favorites' ) : home_url( '/cf-login' );
    $playlists_url = is_user_logged_in() ? home_url( '/cf-profile#playlists' ) : home_url( '/cf-register' );

    $main = array(
        array(
            'id'     => 'home',
            'label'  => __( 'Home', 'collective-finity' ),
            'url'    => home_url( '/' ),
            'icon'   => 'home',
            'active' => is_front_page(),
        ),
        array(
            'id'     => 'library',
            'label'  => __( 'Music Library', 'collective-finity' ),
            'url'    => $tracks_url ? $tracks_url : home_url( '/tracks/' ),
            'icon'   => 'library',
            'active' => $is_library,
        ),
        array(
            'id'     => 'blog',
            'label'  => __( 'Blog', 'collective-finity' ),
            'url'    => collective_finity_get_page_link( 'blog', '/blog/' ),
            'icon'   => 'blog',
            'active' => is_home() || is_singular( 'post' ) || is_category() || is_tag() || is_page( 'blog' ),
        ),
        array(
            'id'     => 'community',
            'label'  => __( 'Join Community', 'collective-finity' ),
            'url'    => collective_finity_get_page_link( 'join-community', '/join-community/' ),
            'icon'   => 'community',
            'active' => is_page( 'join-community' ),
        ),
        array(
            'id'     => 'about',
            'label'  => __( 'About', 'collective-finity' ),
            'url'    => collective_finity_get_page_link( 'about', '/about/' ),
            'icon'   => 'about',
            'active' => is_page( 'about' ),
        ),
    );

    $secondary = array(
        array(
            'id'     => 'favorites',
            'label'  => __( 'Favorites & Liked', 'collective-finity' ),
            'url'    => $favorites_url,
            'icon'   => 'heart',
            'active' => false,
        ),
        array(
            'id'     => 'playlists',
            'label'  => __( 'Personal Playlists', 'collective-finity' ),
            'url'    => $playlists_url,
            'icon'   => 'playlist',
            'active' => false,
        ),
    );

    return array( 'main' => $main, 'secondary' => $secondary );
}

/**
 * Render the shared search trigger button (desktop right rail + mobile topbar).
 *
 * @param string $extra_class Optional class names appended to the button.
 */
function collective_finity_render_search_trigger( $extra_class = '' ) {
    $class = 'cf-icon-btn cf-search-trigger';
    if ( $extra_class ) {
        $class .= ' ' . $extra_class;
    }

    printf(
        '<button type="button" class="%1$s" aria-label="%2$s" aria-controls="cf-search-overlay" aria-expanded="false" aria-haspopup="dialog">%3$s</button>',
        esc_attr( $class ),
        esc_attr__( 'Search', 'collective-finity' ),
        collective_finity_icon( 'search', 18 ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    );
}

/**
 * Render a single shell nav row.
 *
 * @param array $item Nav item from collective_finity_get_shell_nav().
 */
function collective_finity_render_nav_row( $item ) {
    printf(
        '<a href="%1$s" class="cf-nav-row%2$s"%3$s>%4$s<span class="cf-nav-label">%5$s</span></a>',
        esc_url( $item['url'] ),
        ! empty( $item['active'] ) ? ' is-active' : '',
        ! empty( $item['active'] ) ? ' aria-current="page"' : '',
        collective_finity_icon( $item['icon'], 18 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        esc_html( $item['label'] )
    );
}

/**
 * Resolve a published page URL by slug (tries multiple slugs).
 *
 * @param string|array $slugs     Page slug or list of slugs to try.
 * @param string       $fallback  Path appended to home_url when no page is found.
 */
function collective_finity_get_page_link( $slugs, $fallback = '' ) {
    $slugs = (array) $slugs;
    foreach ( $slugs as $slug ) {
        $page = get_page_by_path( $slug );
        if ( $page && 'publish' === $page->post_status ) {
            return get_permalink( $page );
        }
    }
    if ( $fallback ) {
        return home_url( $fallback );
    }
    return home_url( '/' . end( $slugs ) . '/' );
}

/**
 * Footer navigation columns (Explore, Community, Legal).
 */
function collective_finity_get_footer_menu_sections() {
    $albums_url = get_post_type_archive_link( 'albums' );
    $tracks_url = get_post_type_archive_link( 'tracks' );

    $sections = array(
        'explore' => array(
            'title' => __( 'Explore', 'collective-finity' ),
            'links' => array(
                array( 'label' => __( 'Home', 'collective-finity' ), 'url' => home_url( '/' ) ),
                array( 'label' => __( 'Blog', 'collective-finity' ), 'url' => collective_finity_get_page_link( 'blog', '/blog/' ) ),
                array( 'label' => __( 'Albums', 'collective-finity' ), 'url' => $albums_url ? $albums_url : home_url( '/albums/' ) ),
                array( 'label' => __( 'Music Library', 'collective-finity' ), 'url' => $tracks_url ? $tracks_url : home_url( '/tracks/' ) ),
                array( 'label' => __( 'Donate', 'collective-finity' ), 'url' => collective_finity_get_page_link( 'donate', '/donate/' ) ),
            ),
        ),
		'community' => array(
			'title' => __( 'Community', 'collective-finity' ),
			'links' => array(
				array( 'label' => __( 'About', 'collective-finity' ), 'url' => collective_finity_get_page_link( 'about', '/about/' ) ),
				array( 'label' => __( 'FAQ', 'collective-finity' ), 'url' => collective_finity_get_page_link( 'faq', '/faq/' ) ),
				array( 'label' => __( 'Join Community', 'collective-finity' ), 'url' => collective_finity_get_page_link( 'join-community', '/join-community/' ) ),
				array( 'label' => __( 'Contact', 'collective-finity' ), 'url' => collective_finity_get_page_link( array( 'contact', 'contact-us' ), '/contact/' ) ),
			),
		),
        'legal' => array(
            'title' => __( 'Legal', 'collective-finity' ),
            'links' => collective_finity_get_published_legal_links_for_footer(),
        ),
    );

    if ( empty( $sections['legal']['links'] ) ) {
        unset( $sections['legal'] );
    }

    return apply_filters( 'collective_finity_footer_menu_sections', $sections );
}

/**
 * Configured social profile links for the footer.
 */
function collective_finity_get_footer_social_links() {
    $map = array(
        'instagram'           => array(
            'label'  => 'Instagram',
            'option' => 'social_instagram',
        ),
        'instagram_community' => array(
            'label'  => 'Instagram Community',
            'option' => 'social_instagram_community',
        ),
        'youtube'             => array(
            'label'  => 'YouTube',
            'option' => 'social_youtube',
        ),
        'spotify'             => array(
            'label'  => 'Spotify',
            'option' => 'social_spotify',
        ),
        'facebook'            => array(
            'label'  => 'Facebook',
            'option' => 'social_facebook',
        ),
        'facebook_group'      => array(
            'label'  => 'Facebook Group',
            'option' => 'social_facebook_group',
        ),
        'discord'             => array(
            'label'  => 'Discord',
            'option' => 'social_discord',
        ),
        'tiktok'              => array(
            'label'  => 'TikTok',
            'option' => 'social_tiktok',
        ),
        'soundcloud'          => array(
            'label'  => 'SoundCloud',
            'option' => 'social_soundcloud',
        ),
        'amazon'              => array(
            'label'  => 'Amazon Music',
            'option' => 'social_amazon',
        ),
        'x'                   => array(
            'label'  => 'X',
            'option' => 'social_x',
        ),
    );

    $links = array();
    foreach ( $map as $icon => $config ) {
        $url = collective_finity_get_theme_option( $config['option'] );
        if ( $url ) {
            $links[] = array(
                'icon'  => $icon,
                'label' => $config['label'],
                'url'   => $url,
            );
        }
    }

    return apply_filters( 'collective_finity_footer_social_links', $links );
}

/**
 * SVG icon markup for footer social links.
 *
 * @param string $icon Icon slug.
 */
function collective_finity_footer_social_icon( $icon ) {
    $icons = array(
        'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
        'youtube'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
        'spotify'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>',
        'facebook'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.413c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.971H15.83c-1.491 0-1.956.93-1.956 1.886v2.268h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>',
        'discord'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.664-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>',
        'tiktok'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
        'soundcloud' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M1.175 12.225c-.051 0-.094.046-.101.1l-.233 2.154.233 2.105c.007.058.05.098.101.098.05 0 .09-.04.099-.098l.255-2.105-.27-2.154c0-.057-.045-.1-.09-.1m-.9.828c-.05 0-.09.04-.099.098L0 14.479l.176 1.378c.008.06.05.098.099.098.05 0 .09-.04.099-.098l.39-1.378-.39-1.376c0-.06-.045-.1-.09-.1m1.83-.789c-.061 0-.105.045-.112.104l-.215 2.416.215 2.45c.007.06.051.104.112.104.061 0 .105-.045.112-.104l.24-2.45-.24-2.416c-.007-.06-.051-.104-.112-.104m.95-.859c-.069 0-.119.054-.13.122l-.193 3.275.193 3.325c.011.071.061.122.13.122.069 0 .119-.054.13-.122l.217-3.325-.217-3.275c-.011-.068-.061-.122-.13-.122m.949-.748c-.079 0-.135.063-.147.144l-.18 4.023.18 4.078c.012.081.068.144.147.144.079 0 .135-.063.147-.144l.203-4.078-.203-4.023c-.012-.081-.068-.144-.147-.144m.94-.671c-.085 0-.146.071-.158.162l-.17 4.694.17 4.748c.012.091.073.162.158.162.085 0 .146-.071.158-.162l.192-4.748-.192-4.694c-.012-.091-.073-.162-.158-.162m.96-.584c-.093 0-.16.078-.173.176l-.157 5.278.157 5.332c.013.098.08.176.173.176.093 0 .16-.078.173-.176l.178-5.332-.178-5.278c-.013-.098-.08-.176-.173-.176m.96-.49c-.097 0-.167.082-.18.187l-.14 5.768.14 5.821c.013.105.083.187.18.187.097 0 .167-.082.18-.187l.158-5.821-.158-5.768c-.013-.105-.083-.187-.18-.187m.99-.403c-.105 0-.18.088-.194.199l-.121 6.171.121 6.223c.014.111.089.199.194.199.105 0 .18-.088.194-.199l.14-6.223-.14-6.171c-.014-.111-.089-.199-.194-.199m1-.3c-.114 0-.195.096-.208.217l-.1 6.471.1 6.523c.013.121.094.217.208.217.114 0 .195-.096.208-.217l.114-6.523-.114-6.471c-.013-.121-.094-.217-.208-.217m1.01-.187c-.119 0-.204.1-.218.229l-.079 6.658.079 6.709c.014.129.099.229.218.229.119 0 .204-.1.218-.229l.09-6.709-.09-6.658c-.014-.129-.099-.229-.218-.229m1.06-.088c-.127 0-.217.107-.232.241l-.058 6.746.058 6.798c.015.134.105.241.232.241.127 0 .217-.107.232-.241l.066-6.798-.066-6.746c-.015-.134-.105-.241-.232-.241m1.1-.072c-.136 0-.232.114-.248.256l-.042 6.818.042 6.87c.016.142.112.256.248.256.136 0 .232-.114.248-.256l.048-6.87-.048-6.818c-.016-.142-.112-.256-.248-.256m2.89 4.857c-.4 0-.76.118-1.07.313-.45-.87-1.36-1.47-2.4-1.47-1.5 0-2.71 1.22-2.71 2.72 0 1.5 1.21 2.72 2.71 2.72 1.04 0 1.95-.6 2.4-1.47.31.195.67.313 1.07.313 1.04 0 1.88-.84 1.88-1.87 0-1.03-.84-1.87-1.88-1.87m4.62.01c-.8 0-1.45.65-1.45 1.45s.65 1.45 1.45 1.45 1.45-.65 1.45-1.45-.65-1.45-1.45-1.45"/></svg>',
        'amazon'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M.045 18.02c.072-.116.187-.124.348-.022 3.636 2.11 7.594 3.166 11.87 3.166 2.852 0 5.668-.533 8.447-1.595l.315-.14c.138-.06.234-.1.293-.13.226-.088.39-.046.525.13.12.172.09.336-.12.48-.256.19-.76.385-1.51.585-.797.252-1.597.504-2.402.754-3.158 1.006-6.626 1.51-10.406 1.51-4.324 0-8.162-.734-11.52-2.203-.176-.072-.296-.16-.36-.256-.1-.133-.076-.27.073-.41 3.23-2.89 6.746-4.81 10.552-5.78 3.753-.988 7.626-1.48 11.617-1.48 4.124 0 7.82.615 11.088 1.85 3.266 1.231 5.868 2.88 7.802 4.94.173.19.26.37.26.54 0 .13-.058.195-.174.195-.046 0-.14-.03-.28-.088-3.026-1.34-6.5-2.01-10.42-2.01-4.29 0-8.04.8-11.25 2.4-3.01 1.51-5.65 3.55-7.92 6.12-.12.14-.2.21-.24.21-.07 0-.07-.09 0-.27z"/></svg>',
        'x'         => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
    );

    $icons['instagram_community'] = $icons['instagram']; // same glyph, different link.
    $icons['facebook_group']      = $icons['facebook'];  // same glyph, different link.

    return isset( $icons[ $icon ] ) ? $icons[ $icon ] : $icons['x'];
}

function collective_finity_elementor_support() {
    add_theme_support( 'elementor' );
}
add_action( 'after_setup_theme', 'collective_finity_elementor_support' );

function collective_finity_create_default_pages() {
    $default_pages = array(
        array(
            'post_title'   => 'Home',
            'post_name'    => 'home',
            'post_content' => 'Welcome to Collective Finity. Discover music, albums, and the community experience.',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'meta_input'   => array(
                '_wp_page_template' => 'front-page.php',
            ),
        ),
        array(
            'post_title'   => 'About',
            'post_name'    => 'about',
            'post_content' => 'Collective Finity is a music-first theme built for cinematic tracks, albums, and community discovery.',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'meta_input'   => array(
                '_wp_page_template' => 'page-about.php',
            ),
        ),
        array(
            'post_title'   => 'Contact Us',
            'post_name'    => 'contact-us',
            'post_content' => 'Need help or want to connect? Use the form on this page to reach Collective Finity.',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'meta_input'   => array(
                '_wp_page_template' => 'page-contact.php',
            ),
        ),
        array(
            'post_title'   => 'Join Community',
            'post_name'    => 'join-community',
            'post_content' => 'Join the Collective Finity community for updates, new releases, and exclusive access.',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'meta_input'   => array(
                '_wp_page_template' => 'page-join-community.php',
            ),
        ),
        array(
            'post_title'   => 'Privacy Policy',
            'post_name'    => 'privacy-policy',
            'post_content' => 'Privacy Policy content is managed by the theme for a smooth user experience.',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'meta_input'   => array(
                '_wp_page_template' => 'page-privacy-policy.php',
            ),
        ),
        array(
            'post_title'   => 'Terms of Service',
            'post_name'    => 'terms-of-service',
            'post_content' => 'Terms of Service content is managed by the theme to protect users of the platform.',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'meta_input'   => array(
                '_wp_page_template' => 'page-terms.php',
            ),
        ),
    );

    foreach ( $default_pages as $page_data ) {
        $existing_page = get_page_by_title( $page_data['post_title'], OBJECT, 'page' );
        if ( ! $existing_page ) {
            wp_insert_post( $page_data );
        }
    }
}
add_action( 'after_setup_theme', 'collective_finity_create_default_pages' );


/**
 * 3. ENQUEUE FRONTEND STYLES AND SCRIPTS (Localized for Secure AJAX Requests)
 */
function collective_finity_scripts() {
    $theme_version = wp_get_theme()->get( 'Version' );
    $player_path   = get_template_directory() . '/js/music-player.js';
    $player_ver    = file_exists( $player_path ) ? filemtime( $player_path ) : $theme_version;

    wp_enqueue_style( 'main-style', get_stylesheet_uri(), array(), $theme_version );

    $shell_css_path = get_template_directory() . '/assets/css/cf-shell.css';
    $shell_css_ver  = file_exists( $shell_css_path ) ? filemtime( $shell_css_path ) : $theme_version;
    wp_enqueue_style( 'cf-shell', get_template_directory_uri() . '/assets/css/cf-shell.css', array( 'main-style' ), $shell_css_ver );

    $layout_css_path = get_template_directory() . '/assets/css/cf-content-layout.css';
    $layout_css_ver  = file_exists( $layout_css_path ) ? filemtime( $layout_css_path ) : $theme_version;
    wp_enqueue_style( 'cf-content-layout', get_template_directory_uri() . '/assets/css/cf-content-layout.css', array( 'cf-shell' ), $layout_css_ver );

    wp_enqueue_style( 'dashicons' );
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'music-player-js', get_template_directory_uri() . '/js/music-player.js', array( 'jquery', 'cf-auth-script' ), $player_ver, true );

    $shell_js_path = get_template_directory() . '/assets/js/cf-shell.js';
    $shell_js_ver  = file_exists( $shell_js_path ) ? filemtime( $shell_js_path ) : $theme_version;
    wp_enqueue_script( 'cf-shell-js', get_template_directory_uri() . '/assets/js/cf-shell.js', array(), $shell_js_ver, true );

    $soft_nav_path = get_template_directory() . '/js/cf-soft-nav.js';
    $soft_nav_ver  = file_exists( $soft_nav_path ) ? filemtime( $soft_nav_path ) : $theme_version;
    wp_enqueue_script( 'cf-soft-nav-js', get_template_directory_uri() . '/js/cf-soft-nav.js', array(), $soft_nav_ver, true );

    $cookie_css_path = get_template_directory() . '/assets/css/cookie-consent.css';
    $cookie_js_path  = get_template_directory() . '/assets/js/cookie-consent.js';
    $cookie_css_ver  = file_exists( $cookie_css_path ) ? filemtime( $cookie_css_path ) : $theme_version;
    $cookie_js_ver   = file_exists( $cookie_js_path ) ? filemtime( $cookie_js_path ) : $theme_version;

    wp_enqueue_style( 'cf-cookie-consent', get_template_directory_uri() . '/assets/css/cookie-consent.css', array( 'main-style' ), $cookie_css_ver );
    wp_enqueue_script( 'cf-cookie-consent', get_template_directory_uri() . '/assets/js/cookie-consent.js', array(), $cookie_js_ver, true );

    $cookie_policy_page = get_page_by_path( 'cookie-policy', OBJECT, 'page' );
    $cookie_policy_url  = ( $cookie_policy_page && 'publish' === $cookie_policy_page->post_status )
        ? get_permalink( $cookie_policy_page )
        : home_url( '/cookie-policy/' );

    wp_localize_script(
        'cf-cookie-consent',
        'cfCookieConsentConfig',
        array(
            'cookiePolicyUrl' => $cookie_policy_url,
        )
    );

    wp_localize_script( 'music-player-js', 'cf_ajax', array(
        'ajax_url'  => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'cf_interaction_nonce' ),
        'logged_in' => is_user_logged_in(),
    ) );
}
add_action( 'wp_enqueue_scripts', 'collective_finity_scripts' );


/**
 * 4. CUSTOM POST TYPES (CPT) REGISTRATION
 */
function register_tracks_custom_post_type() {
    $labels = array(
        'name'               => _x( 'Tracks', 'post type general name' ),
        'singular_name'      => _x( 'Track', 'post type singular name' ),
        'menu_name'          => _x( 'Tracks', 'admin menu' ),
        'add_new_item'       => __( 'Add New Track' ),
        'edit_item'          => __( 'Edit Track' ),
        'all_items'          => __( 'All Tracks' ),
    );
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'comments' ),
        'menu_icon'          => 'dashicons-format-audio',
        'show_in_rest'       => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
    );
    register_post_type( 'tracks', $args );
}
add_action( 'init', 'register_tracks_custom_post_type' );

function register_albums_custom_post_type() {
    $labels = array(
        'name'               => _x( 'Albums', 'post type general name' ),
        'singular_name'      => _x( 'Album', 'post type singular name' ),
        'menu_name'          => _x( 'Albums', 'admin menu' ),
        'add_new_item'       => __( 'Add New Album' ),
        'edit_item'          => __( 'Edit Album' ),
        'all_items'          => __( 'All Albums' ),
    );
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'supports'           => array( 'title', 'editor', 'thumbnail' ),
        'menu_icon'          => 'dashicons-portfolio',
        'show_in_rest'       => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
    );
    register_post_type( 'albums', $args );
}
add_action( 'init', 'register_albums_custom_post_type' );


/**
 * 5. CUSTOM TAXONOMIES & AUTO-POPULATE DEFAULT GENRES
 */

/**
 * Canonical Music Genres parent → children map.
 *
 * @return array<string, string[]>
 */
function collective_finity_music_genre_defaults() {
    return array(
        'Ambient' => array(
            'Dark Ambient',
            'Emotional Ambient',
            'Atmospheric Ambient',
        ),
        'Cinematic' => array(
            'Cinematic Ambient',
            'Neo Cinematic',
            'Hybrid Score',
        ),
        'Electronic' => array(
            'Melodic Electronic',
            'Atmospheric Electronic',
            'Techno',
            'Minimal Techno',
            'Downtempo',
            'Chill Electronic',
        ),
        'Industrial' => array(
            'Industrial Electronic',
            'Cyberpunk',
        ),
        'Experimental' => array(
            'AI Music',
            'Sound Design',
            'Drone',
        ),
        'Hybrid Electronic' => array(
            'Futuristic Soundscape',
            'Electronic Score',
        ),
        'Arabic' => array(
            'Arabic Classical',
        ),
        'Metal' => array(
            'Symphonic Metal',
            'Gothic Metal',
            'Doom Metal',
            'Industrial Metal',
            'Cinematic Metal',
        ),
        'Mood & Focus' => array(
            'Meditation',
            'Study',
            'Focus',
            'Relaxation',
            'Sleep',
        ),
    );
}

/**
 * Flat list of every allowed genre name (parents + children).
 *
 * @return string[]
 */
function collective_finity_music_genre_allowed_names() {
    $allowed = array();
    foreach ( collective_finity_music_genre_defaults() as $parent => $children ) {
        $allowed[] = $parent;
        foreach ( $children as $child ) {
            $allowed[] = $child;
        }
    }
    return $allowed;
}

/**
 * Ensure a music_genre term exists by name; return its term_id.
 *
 * @param string $name   Term name.
 * @param int    $parent Parent term ID (0 for top-level).
 * @return int Term ID, or 0 on failure.
 */
function collective_finity_ensure_music_genre_term( $name, $parent = 0 ) {
    $term = get_term_by( 'name', $name, 'music_genre' );
    if ( $term && ! is_wp_error( $term ) ) {
        $updates = array();
        if ( (int) $term->parent !== (int) $parent ) {
            $updates['parent'] = (int) $parent;
        }
        if ( ! empty( $updates ) ) {
            $updated = wp_update_term( (int) $term->term_id, 'music_genre', $updates );
            if ( is_wp_error( $updated ) ) {
                return (int) $term->term_id;
            }
        }
        return (int) $term->term_id;
    }

    $inserted = wp_insert_term( $name, 'music_genre', array( 'parent' => (int) $parent ) );
    if ( is_wp_error( $inserted ) ) {
        // Name/slug collision — fall back to lookup by slug.
        $by_slug = get_term_by( 'slug', sanitize_title( $name ), 'music_genre' );
        return ( $by_slug && ! is_wp_error( $by_slug ) ) ? (int) $by_slug->term_id : 0;
    }

    return (int) $inserted['term_id'];
}

/**
 * Move all object relationships from one music_genre term onto another,
 * preserving any additional genres already on those objects.
 *
 * @param int $from_term_id Source term ID.
 * @param int $to_term_id   Destination term ID.
 */
function collective_finity_reassign_music_genre_term( $from_term_id, $to_term_id ) {
    $from_term_id = (int) $from_term_id;
    $to_term_id   = (int) $to_term_id;
    if ( $from_term_id < 1 || $to_term_id < 1 || $from_term_id === $to_term_id ) {
        return;
    }

    $object_ids = get_objects_in_term( $from_term_id, 'music_genre' );
    if ( empty( $object_ids ) || is_wp_error( $object_ids ) ) {
        return;
    }

    foreach ( $object_ids as $object_id ) {
        $current = wp_get_object_terms( (int) $object_id, 'music_genre', array( 'fields' => 'ids' ) );
        if ( is_wp_error( $current ) ) {
            continue;
        }
        $current   = array_map( 'intval', (array) $current );
        $current   = array_diff( $current, array( $from_term_id ) );
        $current[] = $to_term_id;
        wp_set_object_terms( (int) $object_id, array_values( array_unique( $current ) ), 'music_genre', false );
    }
}

/**
 * Rename a genre in place (keeps term_id / assignments). Merges if the new name already exists.
 *
 * @param string $old_name Current term name.
 * @param string $new_name Desired term name.
 * @return int Resulting term ID, or 0 if old term missing.
 */
function collective_finity_rename_music_genre( $old_name, $new_name ) {
    if ( $old_name === $new_name ) {
        $term = get_term_by( 'name', $new_name, 'music_genre' );
        return ( $term && ! is_wp_error( $term ) ) ? (int) $term->term_id : 0;
    }

    $old = get_term_by( 'name', $old_name, 'music_genre' );
    if ( ! $old || is_wp_error( $old ) ) {
        return 0;
    }

    $existing = get_term_by( 'name', $new_name, 'music_genre' );
    if ( $existing && ! is_wp_error( $existing ) && (int) $existing->term_id !== (int) $old->term_id ) {
        collective_finity_reassign_music_genre_term( (int) $old->term_id, (int) $existing->term_id );
        // Promote children of the old term under the surviving term.
        $children = get_terms( array(
            'taxonomy'   => 'music_genre',
            'hide_empty' => false,
            'parent'     => (int) $old->term_id,
            'fields'     => 'ids',
        ) );
        if ( ! is_wp_error( $children ) ) {
            foreach ( $children as $child_id ) {
                wp_update_term( (int) $child_id, 'music_genre', array( 'parent' => (int) $existing->term_id ) );
            }
        }
        wp_delete_term( (int) $old->term_id, 'music_genre' );
        return (int) $existing->term_id;
    }

    $updated = wp_update_term(
        (int) $old->term_id,
        'music_genre',
        array(
            'name' => $new_name,
            'slug' => sanitize_title( $new_name ),
        )
    );

    return is_wp_error( $updated ) ? (int) $old->term_id : (int) $updated['term_id'];
}

/**
 * One-time migration onto the cleaned Music Genres structure.
 */
function collective_finity_migrate_music_genres_v2() {
    $flagged = array();

    // Renames that preserve existing track/album assignments via the same term_id.
    $renames = array(
        'AI Art Music'              => 'AI Music',
        'Experimental Sound Design' => 'Sound Design',
    );
    foreach ( $renames as $old_name => $new_name ) {
        collective_finity_rename_music_genre( $old_name, $new_name );
    }

    // Seed the canonical tree first so reassignment targets exist.
    foreach ( collective_finity_music_genre_defaults() as $parent_name => $children ) {
        $parent_id = collective_finity_ensure_music_genre_term( $parent_name, 0 );
        if ( $parent_id < 1 ) {
            continue;
        }
        foreach ( $children as $child_name ) {
            collective_finity_ensure_music_genre_term( $child_name, $parent_id );
        }
    }

    // Old umbrella parent → Ambient; keep assignments, then remove the old term.
    $legacy_reassignments = array(
        'Ambient & Cinematic'            => 'Ambient',
        'Cinematic & Ambient'            => 'Cinematic',
        'Cyberpunk & Industrial Beats'   => 'Industrial',
        'Electronic & Techno'            => 'Electronic',
        'High-Energy Tech & Electronic'  => 'Electronic',
    );
    foreach ( $legacy_reassignments as $old_name => $new_name ) {
        $old = get_term_by( 'name', $old_name, 'music_genre' );
        $new = get_term_by( 'name', $new_name, 'music_genre' );
        if ( ! $old || is_wp_error( $old ) || ! $new || is_wp_error( $new ) ) {
            continue;
        }
        collective_finity_reassign_music_genre_term( (int) $old->term_id, (int) $new->term_id );
        $children = get_terms( array(
            'taxonomy'   => 'music_genre',
            'hide_empty' => false,
            'parent'     => (int) $old->term_id,
            'fields'     => 'ids',
        ) );
        if ( ! is_wp_error( $children ) ) {
            foreach ( $children as $child_id ) {
                // Leave children under 0 for now; ensure_term sync below will reparent them.
                wp_update_term( (int) $child_id, 'music_genre', array( 'parent' => 0 ) );
            }
        }
        wp_delete_term( (int) $old->term_id, 'music_genre' );
    }

    // Re-apply correct nesting (moves Cinematic Ambient under Cinematic, etc.).
    foreach ( collective_finity_music_genre_defaults() as $parent_name => $children ) {
        $parent_id = collective_finity_ensure_music_genre_term( $parent_name, 0 );
        if ( $parent_id < 1 ) {
            continue;
        }
        foreach ( $children as $child_name ) {
            collective_finity_ensure_music_genre_term( $child_name, $parent_id );
        }
    }

    // Remove empty leftover terms; flag any non-canonical terms that still have content.
    $allowed = collective_finity_music_genre_allowed_names();
    $all     = get_terms( array(
        'taxonomy'   => 'music_genre',
        'hide_empty' => false,
    ) );
    if ( ! is_wp_error( $all ) ) {
        foreach ( $all as $term ) {
            if ( in_array( $term->name, $allowed, true ) ) {
                continue;
            }
            $count = (int) $term->count;
            if ( $count < 1 ) {
                // Also catch terms whose count cache is stale but still linked.
                $objects = get_objects_in_term( (int) $term->term_id, 'music_genre' );
                if ( empty( $objects ) || is_wp_error( $objects ) ) {
                    wp_delete_term( (int) $term->term_id, 'music_genre' );
                    continue;
                }
                $count = count( $objects );
            }
            if ( $count > 0 ) {
                $flagged[] = sprintf(
                    /* translators: 1: genre name, 2: assigned item count */
                    __( '"%1$s" (%2$d items) — not in the new genre list; reassign manually.', 'collective-finity' ),
                    $term->name,
                    $count
                );
            }
        }
    }

    if ( ! empty( $flagged ) ) {
        update_option( 'cf_music_genres_flagged_v2', $flagged, false );
    } else {
        delete_option( 'cf_music_genres_flagged_v2' );
    }
}

/**
 * Ensure the canonical genre tree exists and parents are correct.
 */
function collective_finity_sync_music_genres() {
    if ( '1' !== get_option( 'cf_music_genres_structure_v2' ) ) {
        collective_finity_migrate_music_genres_v2();
        update_option( 'cf_music_genres_structure_v2', '1', false );
    }

    foreach ( collective_finity_music_genre_defaults() as $parent_name => $children ) {
        $parent_id = collective_finity_ensure_music_genre_term( $parent_name, 0 );
        if ( $parent_id < 1 ) {
            continue;
        }
        foreach ( $children as $child_name ) {
            collective_finity_ensure_music_genre_term( $child_name, $parent_id );
        }
    }
}

/**
 * Admin notice when migration left non-canonical genres that still have content.
 */
function collective_finity_music_genres_migration_admin_notice() {
    if ( ! current_user_can( 'manage_categories' ) ) {
        return;
    }
    $flagged = get_option( 'cf_music_genres_flagged_v2', array() );
    if ( empty( $flagged ) || ! is_array( $flagged ) ) {
        return;
    }
    echo '<div class="notice notice-warning is-dismissible"><p><strong>';
    echo esc_html__( 'Music Genres migration needs review:', 'collective-finity' );
    echo '</strong></p><ul style="list-style:disc;margin-left:1.5em;">';
    foreach ( $flagged as $line ) {
        echo '<li>' . esc_html( $line ) . '</li>';
    }
    echo '</ul><p>';
    echo esc_html__( 'These terms were kept so track/album assignments are not lost. Reassign them under Tracks → Genres, then remove the old terms.', 'collective-finity' );
    echo '</p></div>';
}
add_action( 'admin_notices', 'collective_finity_music_genres_migration_admin_notice' );

function collective_finity_register_music_genre_taxonomy() {
    $labels = array(
        'name'              => _x( 'Music Genres', 'taxonomy general name', 'collective-finity' ),
        'singular_name'     => _x( 'Music Genre', 'taxonomy singular name', 'collective-finity' ),
        'search_items'      => __( 'Search Genres', 'collective-finity' ),
        'all_items'         => __( 'All Genres', 'collective-finity' ),
        'parent_item'       => __( 'Parent Genre', 'collective-finity' ),
        'parent_item_colon' => __( 'Parent Genre:', 'collective-finity' ),
        'edit_item'         => __( 'Edit Genre', 'collective-finity' ),
        'update_item'       => __( 'Update Genre', 'collective-finity' ),
        'add_new_item'      => __( 'Add New Genre', 'collective-finity' ),
        'new_item_name'     => __( 'New Genre Name', 'collective-finity' ),
        'menu_name'         => __( 'Genres', 'collective-finity' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'genre' ),
        'show_in_rest'      => true,
    );

    register_taxonomy( 'music_genre', array( 'tracks', 'albums' ), $args );
    collective_finity_sync_music_genres();
}
add_action( 'init', 'collective_finity_register_music_genre_taxonomy' );

function collective_finity_register_track_artist_taxonomy() {
    $labels = array(
        'name'                       => _x( 'Track Artists', 'taxonomy general name', 'collective-finity' ),
        'singular_name'              => _x( 'Track Artist', 'taxonomy singular name', 'collective-finity' ),
        'search_items'               => __( 'Search Artists', 'collective-finity' ),
        'all_items'                  => __( 'All Artists', 'collective-finity' ),
        'edit_item'                  => __( 'Edit Artist', 'collective-finity' ),
        'update_item'                => __( 'Update Artist', 'collective-finity' ),
        'add_new_item'               => __( 'Add New Artist', 'collective-finity' ),
        'new_item_name'              => __( 'New Artist Name', 'collective-finity' ),
        'menu_name'                  => __( 'Artists', 'collective-finity' ),
    );

    $args = array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'           => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'artist' ),
        'show_in_rest'          => true,
    );

    register_taxonomy( 'track_artist', array( 'tracks' ), $args );
}
add_action( 'init', 'collective_finity_register_track_artist_taxonomy' );


/**
 * 6. ENQUEUE ADMIN SCRIPTS & MEDIA FRAME FOR TRACKS (With Cache Busting & Screen Verification)
 */
function collective_finity_enqueue_admin_track_scripts( $hook ) {
    $screen = get_current_screen();

    if ( ! $screen || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }

    $css_file_path = get_template_directory() . '/assets/css/admin-track-meta.css';
    $css_version   = file_exists( $css_file_path ) ? filemtime( $css_file_path ) : '1.0.0';

    if ( 'tracks' === $screen->post_type ) {
        wp_enqueue_media();

        $js_file_path = get_template_directory() . '/js/admin-track-settings.js';
        $js_file_uri  = get_template_directory_uri() . '/js/admin-track-settings.js';
        $js_version   = file_exists( $js_file_path ) ? filemtime( $js_file_path ) : '1.3.0';

        wp_enqueue_script(
            'collective-finity-admin-track-js',
            $js_file_uri,
            array( 'jquery' ),
            $js_version,
            true
        );

        wp_enqueue_style(
            'collective-finity-admin-track-css',
            get_template_directory_uri() . '/assets/css/admin-track-meta.css',
            array( 'dashicons' ),
            $css_version
        );
    } elseif ( 'albums' === $screen->post_type ) {
        wp_enqueue_style(
            'collective-finity-admin-track-css',
            get_template_directory_uri() . '/assets/css/admin-track-meta.css',
            array( 'dashicons' ),
            $css_version
        );
    }
}
add_action( 'admin_enqueue_scripts', 'collective_finity_enqueue_admin_track_scripts' );


/**
 * 7. HIGHLY UPGRADED TRACK SETTINGS META BOX (WITH STYLED GRID)
 */
function collective_finity_add_tracks_meta_box() {
    add_meta_box(
        'track_settings_meta_box',
        __( 'Track Custom Audio Settings', 'collective-finity' ),
        'collective_finity_render_tracks_meta_box',
        'tracks',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'collective_finity_add_tracks_meta_box' );

function collective_finity_render_tracks_meta_box( $post ) {
    wp_nonce_field( 'collective_finity_save_track_meta_action', 'collective_finity_track_nonce' );

    // Fetch values
    $release_type     = get_post_meta( $post->ID, 'track_release_type', true ) ?: 'single';
    $associated_album = get_post_meta( $post->ID, 'associated_album', true );
    $audio_url        = get_post_meta( $post->ID, 'track_audio_url', true );
    $preview_url      = get_post_meta( $post->ID, 'track_preview_url', true );
    $cover_url        = get_post_meta( $post->ID, 'track_cover_url', true );
    $bpm              = get_post_meta( $post->ID, 'track_bpm', true );
    $track_key        = get_post_meta( $post->ID, 'track_key', true );
    $show_bpm         = (bool) get_post_meta( $post->ID, 'track_show_bpm', true );
    $show_key         = (bool) get_post_meta( $post->ID, 'track_show_key', true );
    $lyrics_url       = get_post_meta( $post->ID, 'track_lyrics_url', true );
    $show_lyrics      = collective_finity_track_show_lyrics( $post->ID );
    $cta_label        = get_post_meta( $post->ID, 'track_cta_label', true );
    $cta_url          = get_post_meta( $post->ID, 'track_cta_url', true );
    $copyright        = get_post_meta( $post->ID, 'track_copyright', true );

    // Fetch Streaming URLs + visibility
    $streaming_platforms = collective_finity_track_streaming_platforms();
    $streaming_values    = array();
    foreach ( $streaming_platforms as $platform_slug => $platform ) {
        $streaming_values[ $platform_slug ] = array(
            'url'  => get_post_meta( $post->ID, $platform['meta'], true ),
            'show' => collective_finity_track_show_streaming( $post->ID, $platform_slug ),
        );
    }

    // Visualizer style visibility (default enabled)
    $visualizer_styles = collective_finity_track_visualizer_styles();
    $visualizer_shown  = array();
    foreach ( array_keys( $visualizer_styles ) as $style_slug ) {
        $visualizer_shown[ $style_slug ] = collective_finity_track_show_visualizer( $post->ID, $style_slug );
    }

    // Fetch current Genre taxonomy term assigned to this track
    $assigned_genres = wp_get_post_terms( $post->ID, 'music_genre', array( 'fields' => 'ids' ) );
    $current_genre_id = ! empty( $assigned_genres ) ? $assigned_genres[0] : '';

    $albums_query = new WP_Query( array(
        'post_type' => 'albums', 'posts_per_page' => -1, 'post_status' => 'publish'
    ) );
    ?>
    <div class="cf-track-meta-panel">

        <section class="cf-meta-section">
            <h3 class="cf-meta-section-title"><span class="dashicons dashicons-album"></span><?php esc_html_e( 'Release Info', 'collective-finity' ); ?></h3>
            <div class="cf-admin-grid">
                <div class="cf-field-group cf-row-full">
                    <span class="cf-label"><?php esc_html_e( 'Release Type', 'collective-finity' ); ?></span>
                    <div class="cf-release-type-group">
                        <label><input type="radio" name="track_release_type" value="single" <?php checked( $release_type, 'single' ); ?> class="cf-release-type-radio" /> <?php esc_html_e( 'Single', 'collective-finity' ); ?></label>
                        <label><input type="radio" name="track_release_type" value="album_track" <?php checked( $release_type, 'album_track' ); ?> class="cf-release-type-radio" /> <?php esc_html_e( 'Album Track', 'collective-finity' ); ?></label>
                    </div>
                </div>
                <div id="cf-associated-album-wrapper" class="cf-field-group cf-row-full">
                    <label class="cf-label" for="associated_album"><?php esc_html_e( 'Associated Album', 'collective-finity' ); ?></label>
                    <select name="associated_album" id="associated_album">
                        <option value=""><?php esc_html_e( '— Select Album —', 'collective-finity' ); ?></option>
                        <?php
                        if ( $albums_query->have_posts() ) {
                            while ( $albums_query->have_posts() ) {
                                $albums_query->the_post();
                                printf(
                                    '<option value="%1$s" %2$s>%3$s</option>',
                                    esc_attr( (string) get_the_ID() ),
                                    selected( $associated_album, get_the_ID(), false ),
                                    esc_html( get_the_title() )
                                );
                            }
                            wp_reset_postdata();
                        }
                        ?>
                    </select>
                </div>
                <div class="cf-field-group">
                    <label class="cf-label" for="track_genre_dropdown"><?php esc_html_e( 'Music Genre', 'collective-finity' ); ?></label>
                    <select name="track_genre_dropdown" id="track_genre_dropdown">
                        <option value=""><?php esc_html_e( '— Select Genre —', 'collective-finity' ); ?></option>
                        <?php
                        $genres = get_terms( array( 'taxonomy' => 'music_genre', 'hide_empty' => false ) );
                        foreach ( $genres as $genre ) {
                            printf(
                                '<option value="%1$s" %2$s>%3$s</option>',
                                esc_attr( (string) $genre->term_id ),
                                selected( $current_genre_id, $genre->term_id, false ),
                                esc_html( $genre->name )
                            );
                        }
                        ?>
                    </select>
                </div>
            </div>
        </section>

        <section class="cf-meta-section">
            <h3 class="cf-meta-section-title"><span class="dashicons dashicons-format-audio"></span><?php esc_html_e( 'Audio Files', 'collective-finity' ); ?></h3>
            <div class="cf-admin-grid">
                <div class="cf-field-group cf-audio-file-row" data-media-target="track_audio_url">
                    <label class="cf-label" for="track_audio_url"><?php esc_html_e( 'Primary Audio File', 'collective-finity' ); ?> *</label>
                    <div class="cf-audio-visual" aria-hidden="true">
                        <div class="cf-audio-waveform">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <div class="cf-audio-progress"><div class="cf-audio-progress__fill"></div></div>
                    </div>
                    <input type="text" name="track_audio_url" id="track_audio_url" class="cf-input-text" value="<?php echo esc_url( $audio_url ); ?>" readonly />
                    <div class="cf-media-actions">
                        <button type="button" class="button button-primary cf-media-upload-btn" data-target="track_audio_url" data-type="audio"><?php esc_html_e( 'Select File', 'collective-finity' ); ?></button>
                        <button type="button" class="button cf-media-clear-btn<?php echo empty( $audio_url ) ? ' is-disabled' : ''; ?>" data-target="track_audio_url" <?php disabled( empty( $audio_url ) ); ?>><?php esc_html_e( 'Remove', 'collective-finity' ); ?></button>
                    </div>
                </div>
                <div class="cf-field-group cf-audio-file-row" data-media-target="track_preview_url">
                    <label class="cf-label" for="track_preview_url"><?php esc_html_e( 'Preview / Watermarked File', 'collective-finity' ); ?></label>
                    <div class="cf-audio-visual" aria-hidden="true">
                        <div class="cf-audio-waveform">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <div class="cf-audio-progress"><div class="cf-audio-progress__fill"></div></div>
                    </div>
                    <input type="text" name="track_preview_url" id="track_preview_url" class="cf-input-text" value="<?php echo esc_url( $preview_url ); ?>" readonly />
                    <div class="cf-media-actions">
                        <button type="button" class="button button-primary cf-media-upload-btn" data-target="track_preview_url" data-type="audio"><?php esc_html_e( 'Select File', 'collective-finity' ); ?></button>
                        <button type="button" class="button cf-media-clear-btn<?php echo empty( $preview_url ) ? ' is-disabled' : ''; ?>" data-target="track_preview_url" <?php disabled( empty( $preview_url ) ); ?>><?php esc_html_e( 'Remove', 'collective-finity' ); ?></button>
                    </div>
                </div>
                <div class="cf-field-group cf-cover-field-group">
                    <label class="cf-label" for="track_cover_url"><?php esc_html_e( 'Cover Art', 'collective-finity' ); ?></label>
                    <input type="text" name="track_cover_url" id="track_cover_url" class="cf-input-text" value="<?php echo esc_url( $cover_url ); ?>" readonly />
                    <div class="cf-media-actions">
                        <button type="button" class="button button-primary cf-media-upload-btn" data-target="track_cover_url" data-type="image"><?php esc_html_e( 'Upload Image', 'collective-finity' ); ?></button>
                        <button type="button" class="button cf-media-clear-btn<?php echo empty( $cover_url ) ? ' is-disabled' : ''; ?>" data-target="track_cover_url" <?php disabled( empty( $cover_url ) ); ?>><?php esc_html_e( 'Remove', 'collective-finity' ); ?></button>
                    </div>
                    <div class="cf-cover-preview-wrap">
                        <img id="track_cover_url_preview" src="<?php echo $cover_url ? esc_url( $cover_url ) : esc_url( collective_finity_default_art_url() ); ?>" class="cf-cover-preview" alt="" />
                        <span class="cf-cover-preview-overlay"><?php esc_html_e( 'Change Image', 'collective-finity' ); ?></span>
                    </div>
                </div>
                <div class="cf-field-group cf-audio-file-row" data-media-target="track_lyrics_url">
                    <label class="cf-label" for="track_lyrics_url"><?php esc_html_e( 'Lyrics File (.VTT or .LRC)', 'collective-finity' ); ?></label>
                    <div class="cf-audio-visual cf-audio-visual--text" aria-hidden="true">
                        <div class="cf-audio-waveform">
                            <span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <div class="cf-audio-progress"><div class="cf-audio-progress__fill"></div></div>
                    </div>
                    <input type="text" name="track_lyrics_url" id="track_lyrics_url" class="cf-input-text" value="<?php echo esc_url( $lyrics_url ); ?>" readonly />
                    <div class="cf-media-actions">
                        <button type="button" class="button button-primary cf-media-upload-btn" data-target="track_lyrics_url" data-type="text"><?php esc_html_e( 'Select File', 'collective-finity' ); ?></button>
                        <button type="button" class="button cf-media-clear-btn<?php echo empty( $lyrics_url ) ? ' is-disabled' : ''; ?>" data-target="track_lyrics_url" <?php disabled( empty( $lyrics_url ) ); ?>><?php esc_html_e( 'Remove', 'collective-finity' ); ?></button>
                    </div>
                    <label class="cf-visibility-toggle">
                        <input type="checkbox" name="track_show_lyrics" value="1" <?php checked( $show_lyrics ); ?> />
                        <span><?php esc_html_e( 'Show Lyrics/Story section on the frontend', 'collective-finity' ); ?></span>
                    </label>
                    <p class="cf-field-hint"><?php esc_html_e( 'Enabled by default. Disable to hide the Story & Concept and Lyrics sections on the track page.', 'collective-finity' ); ?></p>
                </div>
            </div>
        </section>

        <section class="cf-meta-section">
            <h3 class="cf-meta-section-title"><span class="dashicons dashicons-chart-bar"></span><?php esc_html_e( 'Track Properties', 'collective-finity' ); ?></h3>
            <div class="cf-admin-grid">
                <div class="cf-field-group">
                    <label class="cf-label" for="track_bpm"><?php esc_html_e( 'BPM', 'collective-finity' ); ?></label>
                    <input type="number" name="track_bpm" id="track_bpm" class="cf-input-text" value="<?php echo esc_attr( $bpm ); ?>" placeholder="<?php esc_attr_e( 'e.g. 128', 'collective-finity' ); ?>" min="0" />
                    <label class="cf-visibility-toggle">
                        <input type="checkbox" name="track_show_bpm" value="1" <?php checked( $show_bpm ); ?> />
                        <span><?php esc_html_e( 'Show BPM on the frontend', 'collective-finity' ); ?></span>
                    </label>
                    <p class="cf-field-hint"><?php esc_html_e( 'Hidden by default. Enable to display BPM on the track page and album tracklist.', 'collective-finity' ); ?></p>
                </div>
                <div class="cf-field-group">
                    <label class="cf-label" for="track_key"><?php esc_html_e( 'Track Key', 'collective-finity' ); ?></label>
                    <input type="text" name="track_key" id="track_key" class="cf-input-text" value="<?php echo esc_attr( $track_key ); ?>" placeholder="<?php esc_attr_e( 'e.g. 4A, Am, C#m', 'collective-finity' ); ?>" />
                    <label class="cf-visibility-toggle">
                        <input type="checkbox" name="track_show_key" value="1" <?php checked( $show_key ); ?> />
                        <span><?php esc_html_e( 'Show Key on the frontend', 'collective-finity' ); ?></span>
                    </label>
                    <p class="cf-field-hint"><?php esc_html_e( 'Hidden by default. Enable to display Key on the track page and album tracklist.', 'collective-finity' ); ?></p>
                </div>
            </div>
        </section>

        <section class="cf-meta-section">
            <h3 class="cf-meta-section-title"><span class="dashicons dashicons-megaphone"></span><?php esc_html_e( 'Call to Action', 'collective-finity' ); ?></h3>
            <div class="cf-admin-grid">
                <div class="cf-field-group">
                    <label class="cf-label" for="track_cta_label"><?php esc_html_e( 'Button Label', 'collective-finity' ); ?></label>
                    <input type="text" name="track_cta_label" id="track_cta_label" class="cf-input-text" value="<?php echo esc_attr( $cta_label ); ?>" placeholder="<?php esc_attr_e( 'e.g. Buy License', 'collective-finity' ); ?>" />
                </div>
                <div class="cf-field-group">
                    <label class="cf-label" for="track_cta_url"><?php esc_html_e( 'Button URL', 'collective-finity' ); ?></label>
                    <input type="url" name="track_cta_url" id="track_cta_url" class="cf-input-text" value="<?php echo esc_url( $cta_url ); ?>" placeholder="https://..." />
                </div>
            </div>
        </section>

        <section class="cf-meta-section">
            <h3 class="cf-meta-section-title"><span class="dashicons dashicons-art"></span><?php esc_html_e( 'Visualizer Styles', 'collective-finity' ); ?></h3>
            <p class="cf-field-hint"><?php esc_html_e( 'Choose which audio visualizer styles appear in the dropdown on this track page. All styles are enabled by default.', 'collective-finity' ); ?></p>
            <div class="cf-visualizer-styles-box">
                <?php foreach ( $visualizer_styles as $style_slug => $style_label ) : ?>
                    <?php $style_meta_key = collective_finity_track_visualizer_meta_key( $style_slug ); ?>
                    <label class="cf-visibility-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( $style_meta_key ); ?>" value="1" <?php checked( ! empty( $visualizer_shown[ $style_slug ] ) ); ?> />
                        <span><?php echo esc_html( $style_label ); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="cf-meta-section">
            <h3 class="cf-meta-section-title"><span class="dashicons dashicons-share"></span><?php esc_html_e( 'Streaming Links', 'collective-finity' ); ?></h3>
            <p class="cf-field-hint"><?php esc_html_e( 'Uncheck Show to hide a platform on the track page without deleting its URL.', 'collective-finity' ); ?></p>
            <div class="cf-streaming-box">
                <?php foreach ( $streaming_platforms as $platform_slug => $platform ) : ?>
                    <div class="cf-streaming-field">
                        <div class="cf-streaming-field-header">
                            <label for="<?php echo esc_attr( $platform['meta'] ); ?>"><?php echo esc_html( $platform['label'] ); ?></label>
                            <label class="cf-visibility-toggle cf-visibility-toggle--inline">
                                <input type="checkbox" name="<?php echo esc_attr( $platform['show_meta'] ); ?>" value="1" <?php checked( ! empty( $streaming_values[ $platform_slug ]['show'] ) ); ?> />
                                <span><?php esc_html_e( 'Show', 'collective-finity' ); ?></span>
                            </label>
                        </div>
                        <div class="cf-streaming-input-wrap">
                            <span class="dashicons <?php echo esc_attr( $platform['icon'] ); ?> cf-streaming-input-icon" aria-hidden="true"></span>
                            <input type="url" name="<?php echo esc_attr( $platform['meta'] ); ?>" id="<?php echo esc_attr( $platform['meta'] ); ?>" class="cf-input-text cf-streaming-input" value="<?php echo esc_url( $streaming_values[ $platform_slug ]['url'] ); ?>" placeholder="<?php echo esc_attr( $platform['placeholder'] ); ?>" />
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="cf-meta-section">
            <h3 class="cf-meta-section-title"><span class="dashicons dashicons-shield"></span><?php esc_html_e( 'Copyright', 'collective-finity' ); ?></h3>
            <div class="cf-field-group">
                <label class="cf-label" for="track_copyright"><?php esc_html_e( 'Copyright / Licensing', 'collective-finity' ); ?></label>
                <input type="text" name="track_copyright" id="track_copyright" class="cf-input-text" value="<?php echo esc_attr( $copyright ); ?>" placeholder="<?php esc_attr_e( '© 2026 Collective Finity. All Rights Reserved.', 'collective-finity' ); ?>" />
            </div>
        </section>

    </div>
    <?php
}


/**
 * 8. SECURE DATA SAVING AND SANITIZATION (Upgraded with Live Likes counter update)
 */
function collective_finity_save_tracks_metadata( $post_id ) {
    if ( ! isset( $_POST['collective_finity_track_nonce'] ) || ! wp_verify_nonce( $_POST['collective_finity_track_nonce'], 'collective_finity_save_track_meta_action' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save Genre Dropdown to Taxonomy
    if ( isset( $_POST['track_genre_dropdown'] ) ) {
        $genre_term_id = intval( $_POST['track_genre_dropdown'] );
        if ( $genre_term_id > 0 ) {
            wp_set_object_terms( $post_id, $genre_term_id, 'music_genre', false );
        } else {
            wp_set_object_terms( $post_id, array(), 'music_genre', false ); // clear terms
        }
    }

    // Save Release Type
    if ( isset( $_POST['track_release_type'] ) ) {
        $release_type = sanitize_text_field( $_POST['track_release_type'] );
        if ( in_array( $release_type, array( 'single', 'album_track' ), true ) ) {
            update_post_meta( $post_id, 'track_release_type', $release_type );
        }
    }

    // Save Associated Album ID
    if ( isset( $_POST['associated_album'] ) ) {
        $associated_album = absint( $_POST['associated_album'] );
        if ( isset( $_POST['track_release_type'] ) && $_POST['track_release_type'] === 'single' ) {
            $associated_album = 0;
        }
        if ( $associated_album > 0 ) {
            update_post_meta( $post_id, 'associated_album', $associated_album );
        } else {
            delete_post_meta( $post_id, 'associated_album' );
        }
    }

    // Single Fields
    $text_fields = array('track_bpm', 'track_key', 'track_cta_label', 'track_copyright');
    foreach ($text_fields as $field) {
        if ( isset( $_POST[$field] ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
        }
    }

    // URL Fields
    $url_fields = array(
        'track_audio_url', 'track_preview_url', 'track_cover_url', 'track_lyrics_url',
        'track_cta_url', 'track_spotify_url', 'track_apple_url', 'track_soundcloud_url',
        'track_youtube_url', 'track_bandcamp_url', 'track_amazon_url', 'track_google_play_url',
    );
    foreach ($url_fields as $field) {
        if ( isset( $_POST[$field] ) ) {
            update_post_meta( $post_id, $field, esc_url_raw( $_POST[$field] ) );
        }
    }

    update_post_meta( $post_id, 'track_show_bpm', empty( $_POST['track_show_bpm'] ) ? 0 : 1 );
    update_post_meta( $post_id, 'track_show_key', empty( $_POST['track_show_key'] ) ? 0 : 1 );
    update_post_meta( $post_id, 'track_show_lyrics', empty( $_POST['track_show_lyrics'] ) ? 0 : 1 );

    foreach ( array_keys( collective_finity_track_visualizer_styles() ) as $style_slug ) {
        $style_meta_key = collective_finity_track_visualizer_meta_key( $style_slug );
        update_post_meta( $post_id, $style_meta_key, empty( $_POST[ $style_meta_key ] ) ? 0 : 1 );
    }

    foreach ( collective_finity_track_streaming_platforms() as $platform ) {
        $show_meta = $platform['show_meta'];
        update_post_meta( $post_id, $show_meta, empty( $_POST[ $show_meta ] ) ? 0 : 1 );
    }
}
add_action( 'save_post_tracks', 'collective_finity_save_tracks_metadata' );


/**
 * 9. ALBUM TRACKLIST MANAGER META BOX
 */

// Register the Tracklist Meta Box for Albums Post Type
function collective_finity_add_album_tracks_meta_box() {
    add_meta_box(
        'album_tracks_meta_box',
        __( 'Album Tracklist', 'collective-finity' ),
        'collective_finity_render_album_tracks_meta_box',
        'albums',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'collective_finity_add_album_tracks_meta_box' );

/**
 * Inject track-count badge beside the Album Tracklist meta box title.
 */
function collective_finity_album_tracklist_title_badge_script() {
    $screen = get_current_screen();
    if ( ! $screen || 'albums' !== $screen->post_type || 'post' !== $screen->base ) {
        return;
    }
    ?>
    <script>
    (function() {
        var wrap = document.querySelector('.cf-album-tracklist-wrapper[data-track-count]');
        if (!wrap) {
            return;
        }
        var count = parseInt(wrap.getAttribute('data-track-count'), 10) || 0;
        var hndle = document.querySelector('#album_tracks_meta_box .postbox-header .hndle, #album_tracks_meta_box > .hndle');
        if (!hndle || hndle.querySelector('.cf-badge')) {
            return;
        }
        var badge = document.createElement('span');
        badge.className = 'cf-badge cf-badge--active';
        badge.textContent = count === 1 ? '1 track' : count + ' tracks';
        hndle.appendChild(document.createTextNode(' '));
        hndle.appendChild(badge);
    })();
    </script>
    <?php
}
add_action( 'admin_footer', 'collective_finity_album_tracklist_title_badge_script' );

// Render Dynamic Tracks Table inside Album editing screen
function collective_finity_render_album_tracks_meta_box( $post ) {
    // Query all Tracks that have this Album selected as 'associated_album'
    $tracks_query = new WP_Query( array(
        'post_type'      => 'tracks',
        'posts_per_page' => -1,
        'post_status'    => array( 'publish', 'draft', 'pending' ),
        'meta_query'     => array(
            array(
                'key'     => 'associated_album',
                'value'   => $post->ID,
                'compare' => '=',
            ),
        ),
    ) );

    $track_count = (int) $tracks_query->found_posts;
    $add_new_url = admin_url( 'post-new.php?post_type=tracks&preselect_album=' . $post->ID );
    ?>
    <div class="cf-album-tracklist-wrapper" data-track-count="<?php echo esc_attr( (string) $track_count ); ?>">
        <?php if ( $tracks_query->have_posts() ) : ?>
            <table class="wp-list-table widefat fixed striped posts cf-album-tracklist-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Track Title', 'collective-finity' ); ?></th>
                        <th class="cf-album-tracklist-col-bpm"><?php esc_html_e( 'BPM', 'collective-finity' ); ?></th>
                        <th class="cf-album-tracklist-col-key"><?php esc_html_e( 'Key', 'collective-finity' ); ?></th>
                        <th class="cf-album-tracklist-col-actions"><?php esc_html_e( 'Actions', 'collective-finity' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ( $tracks_query->have_posts() ) :
                        $tracks_query->the_post();
                        $track_id  = get_the_ID();
                        $bpm       = get_post_meta( $track_id, 'track_bpm', true ) ?: '-';
                        $key       = get_post_meta( $track_id, 'track_key', true ) ?: '-';
                        $edit_link = get_edit_post_link( $track_id );
                        ?>
                        <tr class="cf-album-tracklist-row">
                            <td>
                                <a href="<?php echo esc_url( $edit_link ); ?>" class="cf-album-tracklist-title"><?php echo esc_html( get_the_title() ); ?></a>
                            </td>
                            <td><?php echo esc_html( $bpm ); ?></td>
                            <td><?php echo esc_html( $key ); ?></td>
                            <td class="cf-album-tracklist-col-actions">
                                <a href="<?php echo esc_url( $edit_link ); ?>" class="button button-small"><?php esc_html_e( 'Edit Track', 'collective-finity' ); ?></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <div class="cf-album-tracklist-empty">
                <p class="cf-album-tracklist-empty__message"><?php esc_html_e( 'No tracks have been assigned to this album yet.', 'collective-finity' ); ?></p>
                <a href="<?php echo esc_url( $add_new_url ); ?>" class="button button-primary"><?php esc_html_e( 'Add New Track', 'collective-finity' ); ?></a>
            </div>
        <?php endif; ?>

        <?php if ( $track_count > 0 ) : ?>
            <div class="cf-album-tracklist-actions">
                <a href="<?php echo esc_url( $add_new_url ); ?>" class="button button-primary"><?php esc_html_e( '+ Add New Track to this Album', 'collective-finity' ); ?></a>
            </div>
        <?php endif; ?>
    </div>
    <?php
}


/**
 * Return the full published track library for continuous player queue playback.
 */
function cf_ajax_get_track_library() {
    check_ajax_referer( 'cf_interaction_nonce', 'security' );

    wp_send_json_success(
        array(
            'tracks' => collective_finity_get_published_track_queue(),
        )
    );
}
add_action( 'wp_ajax_cf_get_track_library', 'cf_ajax_get_track_library' );
add_action( 'wp_ajax_nopriv_cf_get_track_library', 'cf_ajax_get_track_library' );


/**
 * 12. FORCE COMMENTS TO BE OPEN FOR TRACKS (Bypasses manual WP database & dashboard toggles)
 */
function collective_finity_force_comments_open_for_tracks( $open, $post_id ) {
    $post = get_post( $post_id );
    if ( $post && 'tracks' === $post->post_type ) {
        return true; // Overrides database 'closed' status and opens comments dynamically!
    }
    return $open;
}
add_filter( 'comments_open', 'collective_finity_force_comments_open_for_tracks', 10, 2 );

/**
 * 13. REGISTER ELEMENTOR-COMPATIBLE CUSTOM THEME WIDGET AREAS
 * Allows creating Header, Footer, and Sidebar layouts in Elementor Free 
 * and injecting them via shortcodes inside simple widgets.
 */
function collective_finity_register_theme_widget_areas() {
    register_sidebar( array(
        'name'          => __( 'Header Widget Area', 'collective-finity' ),
        'id'            => 'header-widget-area',
        'description'   => __( 'Add widgets here to render inside the theme header. Manage this area in Theme Builder or assign an Elementor Header template.', 'collective-finity' ),
        'before_widget' => '<div class="cf-header-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="cf-widget-title">',
        'after_title'   => '</h4>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Sidebar Widget Area', 'collective-finity' ),
        'id'            => 'sidebar-widget-area',
        'description'   => __( 'Add widgets here to render in the theme sidebar. Manage this area in Theme Builder or assign an Elementor Sidebar template.', 'collective-finity' ),
        'before_widget' => '<div class="cf-sidebar-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="cf-widget-title">',
        'after_title'   => '</h4>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer Widget Area', 'collective-finity' ),
        'id'            => 'footer-widget-area',
        'description'   => __( 'Add widgets here to render inside the theme footer. Manage this area in Theme Builder or assign an Elementor Footer template.', 'collective-finity' ),
        'before_widget' => '<div class="cf-footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="cf-widget-title">',
        'after_title'   => '</h4>',
    ) );
}
add_action( 'widgets_init', 'collective_finity_register_theme_widget_areas' );

/**
 * Include tracks and albums in front-end search results.
 */
function collective_finity_extend_search( $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
        return;
    }

    $query->set( 'post_type', array( 'post', 'page', 'tracks', 'albums' ) );
}
add_action( 'pre_get_posts', 'collective_finity_extend_search' );

error_log( 'CF DEBUG: before music library require' );
require get_template_directory() . '/inc/cf-music-library-shortcode.php';
error_log( 'CF DEBUG: after music library require, before latest releases require' );
require get_template_directory() . '/inc/cf-latest-releases-shortcode.php';
require get_template_directory() . '/inc/cf-footer-player-shortcode.php';