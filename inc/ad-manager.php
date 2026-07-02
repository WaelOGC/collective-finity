<?php
/**
 * Ad Manager — frontend rendering and zone definitions.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Default ad zone structure.
 *
 * @return array<string, array<string, mixed>>
 */
function collective_finity_default_ad_zones() {
    return array(
        'library_top'              => array(
            'enabled' => 0,
            'code'    => '',
        ),
        'library_between_sections' => array(
            'enabled' => 0,
            'code'    => '',
        ),
        'track_sidebar'            => array(
            'enabled' => 0,
            'code'    => '',
        ),
        'album_sidebar'            => array(
            'enabled' => 0,
            'code'    => '',
        ),
        'archive_native'             => array(
            'enabled'   => 0,
            'code'      => '',
            'frequency' => 8,
        ),
    );
}

/**
 * Human-readable zone labels for admin UI.
 *
 * @return array<string, string>
 */
function collective_finity_ad_zone_labels() {
    return array(
        'library_top'              => __( 'Library Top', 'collective-finity' ),
        'library_between_sections' => __( 'Library Between Sections', 'collective-finity' ),
        'track_sidebar'            => __( 'Track Sidebar', 'collective-finity' ),
        'album_sidebar'            => __( 'Album Sidebar', 'collective-finity' ),
        'archive_native'           => __( 'Archive Native', 'collective-finity' ),
    );
}

/**
 * Zone descriptions for admin UI.
 *
 * @return array<string, string>
 */
function collective_finity_ad_zone_descriptions() {
    return array(
        'library_top'              => __( 'Appears below the hero on the Music Library page.', 'collective-finity' ),
        'library_between_sections' => __( 'Appears between Latest Releases and Popular Tracks on the Music Library.', 'collective-finity' ),
        'track_sidebar'            => __( 'Appears on single track pages near streaming platform links.', 'collective-finity' ),
        'album_sidebar'            => __( 'Appears on single album pages in the hero sidebar area.', 'collective-finity' ),
        'archive_native'           => __( 'Inserted every Nth card in the tracks archive grid.', 'collective-finity' ),
    );
}

/**
 * Render an ad slot on the frontend.
 *
 * @param string $zone_id Zone identifier.
 */
function collective_finity_ad_slot( $zone_id ) {
    $excluded_page_slugs = array( 'privacy-policy', 'terms-of-service', 'contact' );

    if ( is_front_page() || is_home() ) {
        return;
    }

    if ( is_page( $excluded_page_slugs ) ) {
        return;
    }

    $options = collective_finity_get_theme_options();
    $zones   = $options['ad_zones'] ?? array();

    if ( empty( $zones[ $zone_id ]['enabled'] ) ) {
        return;
    }

    $labels      = collective_finity_ad_zone_labels();
    $zone_label  = isset( $labels[ $zone_id ] ) ? $labels[ $zone_id ] : $zone_id;
    $preview_mode = ! empty( $options['ad_preview_mode'] );

    if ( $preview_mode ) {
        printf(
            '<div class="cf-ad-slot cf-ad-slot--preview" data-zone="%1$s">%2$s</div>',
            esc_attr( $zone_id ),
            esc_html( sprintf( __( 'Ad Zone: %s', 'collective-finity' ), $zone_label ) )
        );
        return;
    }

    $code = $zones[ $zone_id ]['code'] ?? '';
    if ( empty( $code ) ) {
        return;
    }

    echo '<div class="cf-ad-slot" data-zone="' . esc_attr( $zone_id ) . '">';
    echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- admin-entered ad script, capability-gated.
    echo '</div>';
}
