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
            'enabled'         => 0,
            'code'            => '',
            'adsense_slot_id' => '',
        ),
        'library_between_sections' => array(
            'enabled'         => 0,
            'code'            => '',
            'adsense_slot_id' => '',
        ),
        'track_sidebar'            => array(
            'enabled'         => 0,
            'code'            => '',
            'adsense_slot_id' => '',
        ),
        'album_sidebar'            => array(
            'enabled'         => 0,
            'code'            => '',
            'adsense_slot_id' => '',
        ),
        'archive_native'             => array(
            'enabled'         => 0,
            'code'            => '',
            'adsense_slot_id' => '',
            'frequency'       => 8,
        ),
        'blog_listing'               => array(
            'enabled'         => 0,
            'code'            => '',
            'adsense_slot_id' => '',
        ),
        'single_post'                => array(
            'enabled'         => 0,
            'code'            => '',
            'adsense_slot_id' => '',
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
        'blog_listing'             => __( 'Blog Listing Top', 'collective-finity' ),
        'single_post'              => __( 'Single Post In-Content', 'collective-finity' ),
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
        'blog_listing'             => __( 'Appears above the post grid on the Blog Hub page.', 'collective-finity' ),
        'single_post'              => __( 'Appears after the article body, before Related Articles, on single blog posts.', 'collective-finity' ),
    );
}

/**
 * Output the AdSense loader script when a Publisher ID is configured.
 */
function collective_finity_adsense_head_script() {
    $publisher_id = collective_finity_get_theme_option( 'adsense_publisher_id', '' );
    if ( empty( $publisher_id ) ) {
        return;
    }

    printf(
        '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=%s" crossorigin="anonymous"></script>' . "\n",
        esc_attr( $publisher_id )
    );
}
add_action( 'wp_head', 'collective_finity_adsense_head_script' );

/**
 * Capture ad-slot markup. Empty string when the zone is disabled or has no content.
 *
 * @param string $zone_id Zone identifier.
 * @return string
 */
function collective_finity_get_ad_slot( $zone_id ) {
    ob_start();
    collective_finity_ad_slot( $zone_id );
    return trim( (string) ob_get_clean() );
}

/**
 * Echo an ad slot only when it produces markup; optionally wrap that markup.
 * Prevents empty wrapper boxes when a zone is disabled.
 *
 * @param string $zone_id Zone identifier.
 * @param string $before  Markup printed before the slot (only if slot has content).
 * @param string $after   Markup printed after the slot (only if slot has content).
 * @return bool True when content was printed.
 */
function collective_finity_ad_slot_wrapped( $zone_id, $before = '', $after = '' ) {
    $html = collective_finity_get_ad_slot( $zone_id );
    if ( '' === $html ) {
        return false;
    }
    echo $before; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- caller-controlled structural wrappers.
    echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- from collective_finity_ad_slot.
    echo $after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- caller-controlled structural wrappers.
    return true;
}

/**
 * Render an ad slot on the frontend.
 *
 * @param string $zone_id Zone identifier.
 */
function collective_finity_ad_slot( $zone_id ) {
    $excluded_page_slugs = array( 'privacy-policy', 'terms-of-service', 'cookie-policy', 'copyright-policy', 'contact' );

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

    $publisher_id = $options['adsense_publisher_id'] ?? '';
    $slot_id      = $zones[ $zone_id ]['adsense_slot_id'] ?? '';

    if ( $publisher_id && $slot_id ) {
        echo '<div class="cf-ad-slot" data-zone="' . esc_attr( $zone_id ) . '">';
        printf(
            '<ins class="adsbygoogle" style="display:block" data-ad-client="%1$s" data-ad-slot="%2$s" data-ad-format="auto" data-full-width-responsive="true"></ins>',
            esc_attr( $publisher_id ),
            esc_attr( $slot_id )
        );
        echo '<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
        echo '</div>';
        return;
    }

    $code = $zones[ $zone_id ]['code'] ?? '';
    if ( empty( $code ) ) {
        return;
    }

    $safe_code = preg_replace( '#</script>#i', '<\/script>', $code );

    echo '<div class="cf-ad-slot" data-zone="' . esc_attr( $zone_id ) . '">';
    printf(
        '<script type="text/plain" class="cf-ad-consent-gate" data-zone="%1$s">%2$s</script>',
        esc_attr( $zone_id ),
        $safe_code // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- admin-entered ad script, capability-gated; deferred until consent.
    );
    echo '</div>';
}
