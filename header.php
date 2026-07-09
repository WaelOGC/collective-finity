<?php
/**
 * The Header for Collective Finity.
 *
 * Shell translated from design-reference/Shell.dc.html:
 * left sidebar, right sidebar (account icons + persistent player), and the
 * tablet/mobile header + drawer. Layout offsets and collapse state live in
 * assets/css/cf-shell.css + assets/js/cf-shell.js.
 *
 * @package Collective_Finity
 */

$cf_logo_url  = collective_finity_site_logo_url( 'thumbnail' );
$cf_site_name = collective_finity_brand_name();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?php echo esc_attr( $cf_site_name ); ?>">
    <link rel="apple-touch-icon" href="<?php echo esc_url( $cf_logo_url ); ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo esc_url( $cf_logo_url ); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php /* Body + accent fonts are enqueued dynamically from Theme Options → General → Typography (collective_finity_enqueue_google_fonts). */ ?>

    <script>
        /* Apply persisted collapse state before paint (mirrors design loadSharedState). */
        (function () {
            try {
                var r = document.documentElement;
                if (window.localStorage.getItem('cf_sidebar_collapsed') === '1') { r.classList.add('cf-sidebar-collapsed'); }
                if (window.localStorage.getItem('cf_player_collapsed') === '1') { r.classList.add('cf-player-collapsed'); }
            } catch (e) {}
        }());
    </script>

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?><?php collective_finity_render_body_design_attributes(); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/mobile', 'header' ); ?>

<?php get_sidebar(); ?>
<?php get_template_part( 'template-parts/sidebar-right', 'default' ); ?>

<div id="cf-app-content" class="cf-app-content">
