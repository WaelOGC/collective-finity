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
 * Theme-bundled legal page HTML (slug => relative path under inc/).
 *
 * @return array<string, string>
 */
function collective_finity_get_legal_page_content_paths() {
    return array(
        'privacy-policy'   => 'legal-content-privacy-policy.html',
        'terms-of-service' => 'legal-content-terms-of-service.html',
        'cookie-policy'    => 'legal-content-cookie-policy.html',
        'copyright-policy' => 'legal-content-copyright-policy.html',
    );
}

/**
 * Load finalized legal page HTML from the theme bundle.
 *
 * @param string $slug Page slug.
 * @return string
 */
function collective_finity_get_bundled_legal_page_content( $slug ) {
    $paths = collective_finity_get_legal_page_content_paths();
    if ( ! isset( $paths[ $slug ] ) ) {
        return '';
    }

    $path = get_template_directory() . '/inc/' . $paths[ $slug ];
    if ( ! is_readable( $path ) ) {
        return '';
    }

    $content = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
    return is_string( $content ) ? trim( $content ) : '';
}

/**
 * Sync bundled legal page content into WordPress pages (idempotent).
 */
function collective_finity_sync_legal_page_content() {
    $target_version = 3;
    if ( (int) get_option( 'cf_legal_pages_content_version', 0 ) >= $target_version ) {
        return;
    }

    $modified_local = '2026-07-11 12:00:00';
    $modified_gmt   = '2026-07-11 10:00:00';

    foreach ( collective_finity_get_legal_page_definitions() as $slug => $title ) {
        $content = collective_finity_get_bundled_legal_page_content( $slug );
        if ( '' === $content ) {
            continue;
        }

        $page = get_page_by_path( $slug, OBJECT, 'page' );
        if ( ! $page ) {
            wp_insert_post(
                array(
                    'post_title'        => $title,
                    'post_name'         => $slug,
                    'post_content'      => $content,
                    'post_status'       => 'publish',
                    'post_type'         => 'page',
                    'post_modified'     => $modified_local,
                    'post_modified_gmt' => $modified_gmt,
                    'meta_input'        => array(
                        '_wp_page_template' => 'page-legal.php',
                    ),
                )
            );
            continue;
        }

        wp_update_post(
            array(
                'ID'                => $page->ID,
                'post_content'      => $content,
                'post_modified'     => $modified_local,
                'post_modified_gmt' => $modified_gmt,
            )
        );

        update_post_meta( $page->ID, '_wp_page_template', 'page-legal.php' );
    }

    update_option( 'cf_legal_pages_content_version', $target_version );
}
add_action( 'after_switch_theme', 'collective_finity_sync_legal_page_content' );
add_action( 'admin_init', 'collective_finity_sync_legal_page_content' );
add_action( 'init', 'collective_finity_sync_legal_page_content', 20 );

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
 * Inline SVG icon for a legal page hero (slug-keyed).
 *
 * @param string $slug Page slug.
 * @return string
 */
function collective_finity_get_legal_page_icon_svg( $slug ) {
    $icons = array(
        'privacy-policy'   => '<path d="M12 3 4 6.5V12c0 5 3.5 8.5 8 9 4.5-.5 8-4 8-9V6.5z"/><rect x="9" y="11" width="6" height="5" rx="1"/><path d="M10.5 11V9a1.5 1.5 0 0 1 3 0v2"/>',
        'terms-of-service' => '<path d="M8 3h8l2 2v16H6V3z"/><line x1="8" y1="8" x2="16" y2="8"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="16" x2="13" y2="16"/><path d="M4 20h16"/><path d="M8 20V17"/><path d="M16 20V17"/><circle cx="8" cy="15" r="2.5"/><circle cx="16" cy="15" r="2.5"/>',
        'cookie-policy'    => '<path d="M12 3a9 9 0 1 0 9 9c0-.34-.02-.67-.05-1"/><circle cx="9" cy="10" r="0.9" fill="currentColor" stroke="none"/><circle cx="14" cy="9" r="0.9" fill="currentColor" stroke="none"/><circle cx="11" cy="14" r="0.9" fill="currentColor" stroke="none"/><circle cx="15.5" cy="13" r="0.9" fill="currentColor" stroke="none"/>',
        'copyright-policy' => '<path d="M12 3 4 6.5V12c0 5 3.5 8.5 8 9 4.5-.5 8-4 8-9V6.5z"/><circle cx="12" cy="12" r="3.5"/><path d="M14.2 10.8a2.8 2.8 0 1 0 0 4.4"/>',
    );

    if ( ! isset( $icons[ $slug ] ) ) {
        return '';
    }

    return sprintf(
        '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
        $icons[ $slug ]
    );
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
