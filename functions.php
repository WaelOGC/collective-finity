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
require_once get_template_directory() . '/inc/legal-pages.php';

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
 * Track view count (page visits).
 */
function collective_finity_track_views( $track_id ) {
    return (int) get_post_meta( $track_id, '_cf_track_plays', true );
}

/**
 * Approved comment count for a track.
 */
function collective_finity_track_comments_count( $track_id ) {
    return (int) get_comments_number( $track_id );
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
                array( 'label' => __( 'Albums', 'collective-finity' ), 'url' => $albums_url ? $albums_url : home_url( '/albums/' ) ),
                array( 'label' => __( 'Music Library', 'collective-finity' ), 'url' => $tracks_url ? $tracks_url : home_url( '/tracks/' ) ),
            ),
        ),
        'community' => array(
            'title' => __( 'Community', 'collective-finity' ),
            'links' => array(
                array( 'label' => __( 'About', 'collective-finity' ), 'url' => collective_finity_get_page_link( 'about', '/about/' ) ),
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
        'instagram' => array(
            'label'  => 'Instagram',
            'option' => 'social_instagram',
        ),
        'youtube'   => array(
            'label'  => 'YouTube',
            'option' => 'social_youtube',
        ),
        'spotify'   => array(
            'label'  => 'Spotify',
            'option' => 'social_spotify',
        ),
        'facebook'  => array(
            'label'  => 'Facebook',
            'option' => 'social_facebook',
        ),
        'x'         => array(
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
        'x'         => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
    );

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
        } else {
            if ( ! empty( $page_data['meta_input']['_wp_page_template'] ) ) {
                update_post_meta( $existing_page->ID, '_wp_page_template', $page_data['meta_input']['_wp_page_template'] );
            }
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
    $footer_css_path = get_template_directory() . '/assets/css/footer.css';
    $footer_css_ver  = file_exists( $footer_css_path ) ? filemtime( $footer_css_path ) : $theme_version;
    wp_enqueue_style( 'cf-footer', get_template_directory_uri() . '/assets/css/footer.css', array( 'main-style' ), $footer_css_ver );
    wp_enqueue_style( 'dashicons' );
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'music-player-js', get_template_directory_uri() . '/js/music-player.js', array( 'jquery' ), $player_ver, true );

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

    register_taxonomy( 'music_genre', array( 'tracks' ), $args );

    // Remove outdated auto-generated genres if they still exist
    $remove_genres = array(
        'Ambient & Cinematic',
        'Cinematic & Ambient',
        'Cyberpunk & Industrial Beats',
        'Electronic & Techno',
        'High-Energy Tech & Electronic'
    );
    foreach ( $remove_genres as $term_name ) {
        $term = get_term_by( 'name', $term_name, 'music_genre' );
        if ( $term && ! is_wp_error( $term ) ) {
            wp_delete_term( $term->term_id, 'music_genre' );
        }
    }

    // Auto-populate parent genres and their subgenres
    $genres = array(
        'Ambient & Cinematic' => array(
            'Dark Ambient',
            'Emotional Ambient',
            'Cinematic Ambient',
            'Atmospheric Ambient',
        ),
        'Electronic' => array(
            'Melodic Electronic',
            'Atmospheric Electronic',
            'Techno',
            'Minimal Techno',
            'Downtempo',
            'Chill Electronic',
        ),
        'Cinematic' => array(
            'Neo Cinematic',
            'Hybrid Score',
        ),
        'Industrial' => array(
            'Industrial Electronic',
            'Cyberpunk',
        ),
        'Experimental' => array(
            'AI Art Music',
            'Experimental Sound Design',
            'Drone',
        ),
        'Hybrid Electronic' => array(
            'Futuristic Soundscape',
            'Electronic Score',
        ),
    );

    foreach ( $genres as $parent_name => $children ) {
        $parent_term = get_term_by( 'name', $parent_name, 'music_genre' );
        if ( ! $parent_term || is_wp_error( $parent_term ) ) {
            $parent_term = wp_insert_term( $parent_name, 'music_genre' );
        }

        if ( ! is_wp_error( $parent_term ) ) {
            $parent_id = is_array( $parent_term ) ? $parent_term['term_id'] : $parent_term->term_id;
            foreach ( $children as $child_name ) {
                $child_term = get_term_by( 'name', $child_name, 'music_genre' );
                if ( ! $child_term || is_wp_error( $child_term ) ) {
                    wp_insert_term( $child_name, 'music_genre', array( 'parent' => $parent_id ) );
                }
            }
        }
    }
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
    $cta_label        = get_post_meta( $post->ID, 'track_cta_label', true );
    $cta_url          = get_post_meta( $post->ID, 'track_cta_url', true );
    $copyright        = get_post_meta( $post->ID, 'track_copyright', true );

    // Fetch Streaming URLs
    $spotify_url      = get_post_meta( $post->ID, 'track_spotify_url', true );
    $apple_url        = get_post_meta( $post->ID, 'track_apple_url', true );
    $soundcloud_url   = get_post_meta( $post->ID, 'track_soundcloud_url', true );
    $youtube_url      = get_post_meta( $post->ID, 'track_youtube_url', true );
    $bandcamp_url     = get_post_meta( $post->ID, 'track_bandcamp_url', true );

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
            <h3 class="cf-meta-section-title"><span class="dashicons dashicons-share"></span><?php esc_html_e( 'Streaming Links', 'collective-finity' ); ?></h3>
            <div class="cf-streaming-box">
                <div class="cf-streaming-field">
                    <label for="track_spotify_url">Spotify</label>
                    <div class="cf-streaming-input-wrap">
                        <span class="dashicons dashicons-controls-play cf-streaming-input-icon" aria-hidden="true"></span>
                        <input type="url" name="track_spotify_url" id="track_spotify_url" class="cf-input-text cf-streaming-input" value="<?php echo esc_url( $spotify_url ); ?>" placeholder="https://open.spotify.com/..." />
                    </div>
                </div>
                <div class="cf-streaming-field">
                    <label for="track_apple_url">Apple Music</label>
                    <div class="cf-streaming-input-wrap">
                        <span class="dashicons dashicons-smartphone cf-streaming-input-icon" aria-hidden="true"></span>
                        <input type="url" name="track_apple_url" id="track_apple_url" class="cf-input-text cf-streaming-input" value="<?php echo esc_url( $apple_url ); ?>" placeholder="https://music.apple.com/..." />
                    </div>
                </div>
                <div class="cf-streaming-field">
                    <label for="track_soundcloud_url">SoundCloud</label>
                    <div class="cf-streaming-input-wrap">
                        <span class="dashicons dashicons-cloud cf-streaming-input-icon" aria-hidden="true"></span>
                        <input type="url" name="track_soundcloud_url" id="track_soundcloud_url" class="cf-input-text cf-streaming-input" value="<?php echo esc_url( $soundcloud_url ); ?>" placeholder="https://soundcloud.com/..." />
                    </div>
                </div>
                <div class="cf-streaming-field">
                    <label for="track_youtube_url">YouTube</label>
                    <div class="cf-streaming-input-wrap">
                        <span class="dashicons dashicons-video-alt3 cf-streaming-input-icon" aria-hidden="true"></span>
                        <input type="url" name="track_youtube_url" id="track_youtube_url" class="cf-input-text cf-streaming-input" value="<?php echo esc_url( $youtube_url ); ?>" placeholder="https://youtube.com/..." />
                    </div>
                </div>
                <div class="cf-streaming-field cf-streaming-field--full">
                    <label for="track_bandcamp_url">Bandcamp</label>
                    <div class="cf-streaming-input-wrap">
                        <span class="dashicons dashicons-format-audio cf-streaming-input-icon" aria-hidden="true"></span>
                        <input type="url" name="track_bandcamp_url" id="track_bandcamp_url" class="cf-input-text cf-streaming-input" value="<?php echo esc_url( $bandcamp_url ); ?>" placeholder="https://bandcamp.com/..." />
                    </div>
                </div>
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
        'track_youtube_url', 'track_bandcamp_url'
    );
    foreach ($url_fields as $field) {
        if ( isset( $_POST[$field] ) ) {
            update_post_meta( $post_id, $field, esc_url_raw( $_POST[$field] ) );
        }
    }

    update_post_meta( $post_id, 'track_show_bpm', empty( $_POST['track_show_bpm'] ) ? 0 : 1 );
    update_post_meta( $post_id, 'track_show_key', empty( $_POST['track_show_key'] ) ? 0 : 1 );
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
 * 10. SECURE AJAX HANDLER FOR TRACK LIKES / FAVORITES (With Cache-Busting retrieval)
 */
function cf_ajax_toggle_like() {
    // Check security Nonce
    check_ajax_referer( 'cf_interaction_nonce', 'security' );

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => __( 'Please log in to add tracks to your favorites.', 'collective-finity' ) ) );
    }

    $user_id  = get_current_user_id();
    $track_id = isset( $_POST['track_id'] ) ? intval( $_POST['track_id'] ) : 0;

    if ( ! $track_id ) {
        wp_send_json_error( array( 'message' => __( 'Invalid Track Selection.', 'collective-finity' ) ) );
    }

    $liked_tracks = get_user_meta( $user_id, '_cf_liked_tracks', true );
    if ( ! is_array( $liked_tracks ) ) {
        $liked_tracks = array();
    }

    $current_likes = intval( get_post_meta( $track_id, '_cf_total_likes_count', true ) ) ?: 0;

    if ( in_array( $track_id, $liked_tracks, true ) ) {
        // Unlike: Remove track ID from the array
        $liked_tracks = array_diff( $liked_tracks, array( $track_id ) );
        $status = 'unliked';
        $current_likes = max( 0, $current_likes - 1 ); // Decrement total likes counter safely
    } else {
        // Like: Add track ID to the array
        $liked_tracks[] = $track_id;
        $status = 'liked';
        $current_likes = $current_likes + 1; // Increment total likes counter
    }

    // Update both user metadata and post metadata counters
    update_user_meta( $user_id, '_cf_liked_tracks', $liked_tracks );
    update_post_meta( $track_id, '_cf_total_likes_count', $current_likes );

    wp_send_json_success( array( 
        'status' => $status, 
        'likes_count' => $current_likes,
        'message' => __( 'Success', 'collective-finity' ) 
    ) );
}
add_action( 'wp_ajax_cf_toggle_like', 'cf_ajax_toggle_like' );


// 11. CACHE BUSTER AJAX: Retrieve live states directly from Database (Bypasses LiteSpeed Caching)
function cf_ajax_get_liked_tracks() {
    check_ajax_referer( 'cf_interaction_nonce', 'security' );

    $liked_tracks = array();
    if ( is_user_logged_in() ) {
        $liked_tracks = get_user_meta( get_current_user_id(), '_cf_liked_tracks', true );
        if ( ! is_array( $liked_tracks ) ) {
            $liked_tracks = array();
        }
    }

    // Force array elements to integers and respond
    wp_send_json_success( array( 
        'liked_tracks' => array_values( array_map( 'intval', $liked_tracks ) ) 
    ) );
}
add_action( 'wp_ajax_cf_get_liked_tracks', 'cf_ajax_get_liked_tracks' );


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

require get_template_directory() . '/inc/cf-music-library-shortcode.php';