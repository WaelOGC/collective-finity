<?php
/**
 * Default theme footer markup.
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

$cf_logo_url      = collective_finity_site_logo_url( 'thumbnail' );
$cf_tagline       = collective_finity_get_theme_option( 'footer_tagline' );
$cf_description   = collective_finity_get_theme_option( 'footer_description' );
$cf_social_links  = collective_finity_get_footer_social_links();
$cf_menu_sections = collective_finity_get_footer_menu_sections();
$cf_copyright     = collective_finity_get_theme_option( 'footer_copyright' );
?>
<footer class="cf-site-footer" role="contentinfo">
    <div class="cf-footer-ambient" aria-hidden="true"></div>

    <div class="cf-footer-inner">
        <div class="cf-footer-main">
            <div class="cf-footer-brand">
                <a class="cf-footer-logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <span class="cf-footer-logo-frame">
                        <img class="cf-footer-logo" src="<?php echo esc_url( $cf_logo_url ); ?>" alt="<?php echo esc_attr( collective_finity_brand_name() ); ?>" width="52" height="52" loading="lazy">
                    </span>
                    <span class="cf-footer-brand-name"><?php echo esc_html( collective_finity_brand_name() ); ?></span>
                </a>

                <?php if ( $cf_tagline ) : ?>
                    <p class="cf-footer-eyebrow"><?php echo esc_html( $cf_tagline ); ?></p>
                <?php endif; ?>

                <?php if ( $cf_description ) : ?>
                    <p class="cf-footer-description"><?php echo esc_html( $cf_description ); ?></p>
                <?php endif; ?>

                <?php if ( ! empty( $cf_social_links ) ) : ?>
                    <div class="cf-footer-social-block">
                        <span class="cf-footer-social-label"><?php esc_html_e( 'Follow', 'collective-finity' ); ?></span>
                        <div class="cf-footer-social" aria-label="<?php esc_attr_e( 'Social media', 'collective-finity' ); ?>">
                            <?php foreach ( $cf_social_links as $social ) : ?>
                                <a href="<?php echo esc_url( $social['url'] ); ?>" class="cf-footer-social-link cf-footer-social-link--<?php echo esc_attr( $social['icon'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $social['label'] ); ?>">
                                    <?php echo collective_finity_footer_social_icon( $social['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cf-footer-nav-grid">
                <?php if ( has_nav_menu( 'footer' ) ) : ?>
                    <nav class="cf-footer-nav cf-footer-nav--custom" aria-label="<?php esc_attr_e( 'Footer menu', 'collective-finity' ); ?>">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'footer',
                                'container'      => false,
                                'menu_class'     => 'cf-footer-menu cf-footer-menu--custom',
                                'depth'          => 1,
                            )
                        );
                        ?>
                    </nav>
                <?php else : ?>
                    <?php foreach ( $cf_menu_sections as $section ) : ?>
                        <nav class="cf-footer-nav" aria-label="<?php echo esc_attr( $section['title'] ); ?>">
                            <h3 class="cf-footer-menu-title"><?php echo esc_html( $section['title'] ); ?></h3>
                            <ul class="cf-footer-menu">
                                <?php foreach ( $section['links'] as $link ) : ?>
                                    <li><a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </nav>
                    <?php endforeach; ?>
                <?php endif; ?>
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
    </div>
</footer>
