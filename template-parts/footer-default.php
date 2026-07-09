<?php
/**
 * Default theme footer markup.
 *
 * Translated from design-reference/Shell.dc.html siteFooter: brand block +
 * Explore / Community / Legal columns + copyright bar. Column links and social
 * links come from real theme data (collective_finity_get_footer_menu_sections()).
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( is_active_sidebar( 'footer-widget-area' ) ) :
    dynamic_sidebar( 'footer-widget-area' );
    return;
endif;

$cf_tagline       = collective_finity_get_theme_option( 'footer_tagline' );
$cf_description   = collective_finity_get_theme_option( 'footer_description' );
$cf_social_links  = collective_finity_get_footer_social_links();
$cf_menu_sections = collective_finity_get_footer_menu_sections();
$cf_copyright     = collective_finity_get_theme_option( 'footer_copyright' );

$cf_tagline     = $cf_tagline ? $cf_tagline : __( 'Experience Music Beyond Imagination', 'collective-finity' );
$cf_description = $cf_description ? $cf_description : __( 'Welcome to Collective Finity — a cinematic world where emotional sound, visual stories and creativity connect in one immersive universe.', 'collective-finity' );
?>
<footer class="cf-site-footer" role="contentinfo">
    <div class="cf-footer-inner">
        <div class="cf-footer-brand">
            <a class="cf-footer-brand-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <span class="cf-footer-brand-mark" aria-hidden="true"></span>
                <span class="cf-footer-brand-word"><?php echo esc_html( strtoupper( collective_finity_brand_name() ) ); ?></span>
            </a>
            <p class="cf-footer-eyebrow"><?php echo esc_html( $cf_tagline ); ?></p>
            <p class="cf-footer-desc"><?php echo esc_html( $cf_description ); ?></p>

            <?php if ( ! empty( $cf_social_links ) ) : ?>
                <div class="cf-footer-social-block">
                    <span class="cf-footer-social-label"><?php esc_html_e( 'Follow', 'collective-finity' ); ?></span>
                    <div class="cf-footer-social" aria-label="<?php esc_attr_e( 'Social media', 'collective-finity' ); ?>">
                        <?php foreach ( $cf_social_links as $cf_social ) : ?>
                            <a href="<?php echo esc_url( $cf_social['url'] ); ?>" class="cf-footer-social-link cf-footer-social-link--<?php echo esc_attr( $cf_social['icon'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $cf_social['label'] ); ?>">
                                <?php echo collective_finity_footer_social_icon( $cf_social['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="cf-footer-cols">
            <?php foreach ( $cf_menu_sections as $cf_section ) : ?>
                <nav class="cf-footer-col" aria-label="<?php echo esc_attr( $cf_section['title'] ); ?>">
                    <h3 class="cf-footer-col-title"><?php echo esc_html( $cf_section['title'] ); ?></h3>
                    <ul>
                        <?php foreach ( $cf_section['links'] as $cf_link ) : ?>
                            <li><a href="<?php echo esc_url( $cf_link['url'] ); ?>"><?php echo esc_html( $cf_link['label'] ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="cf-footer-bottom">
        <p class="cf-footer-copy">
            <?php
            if ( $cf_copyright ) {
                echo esc_html( $cf_copyright );
            } else {
                printf(
                    '&copy; %s %s. %s',
                    esc_html( gmdate( 'Y' ) ),
                    esc_html( collective_finity_brand_name() ),
                    esc_html__( 'All rights reserved.', 'collective-finity' )
                );
            }
            ?>
        </p>
        <button type="button" class="cf-cookie-settings-link" data-cf-cookie-settings>
            <?php esc_html_e( 'Cookie Settings', 'collective-finity' ); ?>
        </button>
    </div>
</footer>
