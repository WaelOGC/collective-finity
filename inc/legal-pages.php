<?php
/**
 * Legal pages — auto-creation, helpers, and assets.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Legal page definitions (slug => title).
 *
 * @return array<string, string>
 */
function collective_finity_get_legal_page_definitions() {
    return array(
        'privacy-policy'   => __( 'Privacy Policy', 'collective-finity' ),
        'terms-of-service' => __( 'Terms of Service', 'collective-finity' ),
        'cookie-policy'    => __( 'Cookie Policy', 'collective-finity' ),
        'copyright-policy' => __( 'Copyright Policy', 'collective-finity' ),
    );
}

/**
 * Placeholder content for auto-created legal pages.
 */
function collective_finity_legal_page_placeholder_content() {
    return __( '[Placeholder — final content will be pasted here after legal review. Do not publish until this placeholder is replaced.]', 'collective-finity' );
}

/**
 * Create draft legal pages when missing (idempotent).
 */
function collective_finity_create_legal_pages() {
    $placeholder = collective_finity_legal_page_placeholder_content();

    foreach ( collective_finity_get_legal_page_definitions() as $slug => $title ) {
        if ( get_page_by_path( $slug, OBJECT, 'page' ) ) {
            continue;
        }

        wp_insert_post(
            array(
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_content' => $placeholder,
                'post_status'  => 'draft',
                'post_type'    => 'page',
                'meta_input'   => array(
                    '_wp_page_template' => 'page-legal.php',
                ),
            )
        );
    }
}

/**
 * Run legal page creation once (live-site safe).
 */
function collective_finity_maybe_create_legal_pages() {
    if ( get_option( 'cf_legal_pages_created' ) ) {
        return;
    }

    collective_finity_create_legal_pages();
    update_option( 'cf_legal_pages_created', 1 );
}
add_action( 'after_switch_theme', 'collective_finity_maybe_create_legal_pages' );
add_action( 'admin_init', 'collective_finity_maybe_create_legal_pages' );

/**
 * Published legal page links formatted for footer menus.
 *
 * @return array<int, array{label: string, url: string}>
 */
function collective_finity_get_published_legal_links_for_footer() {
    $footer_links = array();

    foreach ( collective_finity_get_published_legal_links() as $link ) {
        $footer_links[] = array(
            'label' => $link['label'],
            'url'   => $link['url'],
        );
    }

    return $footer_links;
}

/**
 * Published legal page links for footer and quick-nav.
 *
 * @return array<int, array{label: string, url: string, slug: string}>
 */
function collective_finity_get_published_legal_links() {
    $links = array();

    foreach ( collective_finity_get_legal_page_definitions() as $slug => $label ) {
        $page = get_page_by_path( $slug, OBJECT, 'page' );
        if ( $page && 'publish' === $page->post_status ) {
            $links[] = array(
                'label' => $label,
                'url'   => get_permalink( $page ),
                'slug'  => $slug,
            );
        }
    }

    return $links;
}

/**
 * Build a table of contents from H2 headings in HTML content.
 *
 * @param string $content Post content HTML.
 * @return array{toc: string, content: string}
 */
function collective_finity_build_legal_toc( $content ) {
    if ( ! $content || false === stripos( $content, '<h2' ) ) {
        return array(
            'toc'     => '',
            'content' => $content,
        );
    }

    $used_ids = array();
    $items    = array();

    $content = preg_replace_callback(
        '/<h2\b([^>]*)>(.*?)<\/h2>/is',
        function ( $matches ) use ( &$used_ids, &$items ) {
            $attrs   = $matches[1];
            $heading = wp_strip_all_tags( $matches[2] );

            if ( preg_match( '/\bid=(["\'])([^"\']+)\1/i', $attrs, $id_match ) ) {
                $id = sanitize_title( $id_match[2] );
            } else {
                $id = sanitize_title( $heading );
            }

            $base_id = $id;
            $suffix  = 2;
            while ( isset( $used_ids[ $id ] ) ) {
                $id = $base_id . '-' . $suffix;
                ++$suffix;
            }
            $used_ids[ $id ] = true;

            $items[] = array(
                'id'    => $id,
                'label' => $heading,
            );

            if ( preg_match( '/\bid=/i', $attrs ) ) {
                $attrs = preg_replace( '/\bid=(["\'])[^"\']*\1/i', ' id="' . esc_attr( $id ) . '"', $attrs, 1 );
            } else {
                $attrs .= ' id="' . esc_attr( $id ) . '"';
            }

            return '<h2' . $attrs . '>' . $matches[2] . '</h2>';
        },
        $content
    );

    if ( empty( $items ) ) {
        return array(
            'toc'     => '',
            'content' => $content,
        );
    }

    $toc  = '<nav class="cf-legal-toc" aria-label="' . esc_attr__( 'Table of contents', 'collective-finity' ) . '">';
    $toc .= '<h2 class="cf-legal-toc__title">' . esc_html__( 'On this page', 'collective-finity' ) . '</h2>';
    $toc .= '<ol class="cf-legal-toc__list">';

    foreach ( $items as $item ) {
        $toc .= '<li><a href="#' . esc_attr( $item['id'] ) . '">' . esc_html( $item['label'] ) . '</a></li>';
    }

    $toc .= '</ol></nav>';

    return array(
        'toc'     => $toc,
        'content' => $content,
    );
}

/**
 * Enqueue legal page stylesheet on the legal template.
 */
function collective_finity_enqueue_legal_page_assets() {
    if ( ! is_page_template( 'page-legal.php' ) ) {
        return;
    }

    $css_path = get_template_directory() . '/assets/css/legal-page.css';
    $css_ver  = file_exists( $css_path ) ? filemtime( $css_path ) : wp_get_theme()->get( 'Version' );

    wp_enqueue_style(
        'cf-legal-page',
        get_template_directory_uri() . '/assets/css/legal-page.css',
        array( 'main-style' ),
        $css_ver
    );
}
add_action( 'wp_enqueue_scripts', 'collective_finity_enqueue_legal_page_assets', 25 );
