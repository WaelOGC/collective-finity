<?php
/**
 * Music sidebar template loader.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

collective_finity_render_theme_part(
    'sidebar',
    function () {
        get_template_part( 'template-parts/sidebar', 'default' );
    }
);
