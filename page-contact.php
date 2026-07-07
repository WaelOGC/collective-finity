<?php
/**
 * Template Name: Contact Page
 * Description: Theme template for Contact Us pages.
 */

$cf_community_url = collective_finity_get_page_link( 'join-community', '/join-community/' );
$cf_tracks_url    = get_post_type_archive_link( 'tracks' );
if ( ! $cf_tracks_url ) {
    $cf_tracks_url = home_url( '/tracks/' );
}

$cf_contact_methods = array(
    array(
        'icon'  => 'email-alt',
        'title' => 'Email Us',
        'desc'  => 'Got questions? We\'re here to help.',
        'value' => 'contact@collectivefinity.com',
        'href'  => 'mailto:contact@collectivefinity.com',
    ),
    array(
        'icon'  => 'location',
        'title' => 'Our Location',
        'desc'  => 'Based in the heart of Europe.',
        'value' => 'Netherlands',
    ),
    array(
        'icon'  => 'format-chat',
        'title' => 'Discord Server',
        'desc'  => 'Join our vibrant community of creators.',
        'btn'   => 'Join Official Server',
        'href'  => cf_get_theme_social_url( 'social_discord', '#' ),
    ),
    array(
        'icon'  => 'groups',
        'title' => 'Facebook Group',
        'desc'  => 'Connect with fellow music lovers.',
        'btn'   => 'Join The Group',
        'href'  => cf_get_theme_social_url( 'social_facebook', '#' ),
    ),
    array(
        'icon'  => 'facebook',
        'title' => 'Facebook Page',
        'desc'  => 'Follow us for latest updates.',
        'btn'   => 'Follow Our Page',
        'href'  => cf_get_theme_social_url( 'social_facebook', '#' ),
    ),
);

$cf_latest_releases = cf_get_latest_releases( 5 );

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-contact-page">
    <div class="cf-page-container">
        <div class="cf-page-inner">

            <section class="cf-contact-hero">
                <h1 class="cf-hero-title">Where Music Meets Infinite Imagination</h1>
                <p class="cf-hero-lead">A vibrant global community for music producers, creators, and sonic artists. Connect, collaborate, and push the boundaries of sound with infinite possibilities.</p>
                <div class="cf-hero-actions">
                    <a class="cf-btn cf-btn--primary" href="<?php echo esc_url( $cf_community_url ); ?>">Join Collective</a>
                    <a class="cf-btn cf-btn--ghost" href="<?php echo esc_url( $cf_tracks_url ); ?>">Explore Music</a>
                </div>
            </section>

            <section class="cf-contact-methods" aria-labelledby="cf-contact-methods-heading">
                <h2 id="cf-contact-methods-heading" class="cf-form-section__title">Get In Touch — Let's Create Together</h2>
                <p class="cf-hero-lead" style="margin-bottom:20px;max-width:520px;">Have a question or want to join the collective? Reach out directly through our official channels.</p>
                <div class="cf-card-grid">
                    <?php foreach ( $cf_contact_methods as $method ) : ?>
                        <article class="cf-info-card">
                            <div class="cf-info-card__icon" aria-hidden="true">
                                <span class="dashicons dashicons-<?php echo esc_attr( $method['icon'] ); ?>"></span>
                            </div>
                            <h3 class="cf-info-card__title"><?php echo esc_html( $method['title'] ); ?></h3>
                            <p class="cf-info-card__desc"><?php echo esc_html( $method['desc'] ); ?></p>
                            <?php if ( ! empty( $method['value'] ) ) : ?>
                                <?php if ( ! empty( $method['href'] ) ) : ?>
                                    <a class="cf-info-card__value" href="<?php echo esc_url( $method['href'] ); ?>"><?php echo esc_html( $method['value'] ); ?></a>
                                <?php else : ?>
                                    <p class="cf-info-card__value"><?php echo esc_html( $method['value'] ); ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ( ! empty( $method['btn'] ) ) : ?>
                                <a class="cf-btn cf-btn--outline" href="<?php echo esc_url( $method['href'] ?? '#' ); ?>"><?php echo esc_html( $method['btn'] ); ?></a>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="cf-contact-form-row">
                <section class="cf-form-section" aria-labelledby="cf-contact-form-heading">
                    <h2 id="cf-contact-form-heading" class="cf-form-section__title">Send Us a Message</h2>
                    <div class="cf-form-wrap">
                        <?php echo do_shortcode( '[contact-form-7 id="04d6245" title="Contact form 1"]' ); ?>
                    </div>
                </section>
            </div>

            <?php if ( ! empty( $cf_latest_releases ) ) : ?>
                <section class="cf-latest-releases" aria-labelledby="cf-latest-releases-heading">
                    <h2 id="cf-latest-releases-heading" class="cf-form-section__title">Latest Releases</h2>
                    <div class="cf-release-grid">
                        <?php foreach ( $cf_latest_releases as $release ) : ?>
                            <?php
                            get_template_part(
                                'template-parts/cf',
                                'release-card',
                                array( 'release' => $release )
                            );
                            ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php
get_footer();
