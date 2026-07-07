<?php
/**
 * Template Name: Join Community
 * Description: Theme template for the Join Community page.
 */

$cf_channels = array(
    array( 'icon' => 'format-chat', 'name' => 'Discord', 'desc' => 'Voice channels and daily drops with the crew.', 'url' => cf_get_theme_social_url( 'social_discord', '#' ) ),
    array( 'icon' => 'groups', 'name' => 'Facebook Group', 'desc' => 'Community discussions and event announcements.', 'url' => cf_get_theme_social_url( 'social_facebook', '#' ) ),
    array( 'icon' => 'video-alt3', 'name' => 'TikTok', 'desc' => 'Behind-the-scenes clips and track teasers.', 'url' => cf_get_theme_social_url( 'social_tiktok', '#' ) ),
    array( 'icon' => 'camera', 'name' => 'Instagram — Music', 'desc' => 'Cover art, visuals, and release announcements.', 'url' => cf_get_theme_social_url( 'social_instagram', '#' ) ),
    array( 'icon' => 'camera', 'name' => 'Instagram — Community', 'desc' => 'Fan features and community spotlights.', 'url' => cf_get_theme_social_url( 'social_instagram', '#' ) ),
    array( 'icon' => 'video-alt3', 'name' => 'YouTube', 'desc' => 'Full releases, visualizers, and session videos.', 'url' => cf_get_theme_social_url( 'social_youtube', '#' ) ),
    array( 'icon' => 'cart', 'name' => 'Amazon Music', 'desc' => 'Stream the full catalog and curated playlists.', 'url' => cf_get_theme_social_url( 'social_amazon', '#' ) ),
    array( 'icon' => 'chart-bar', 'name' => 'SoundCloud', 'desc' => 'Early demos, remixes, and DJ sets.', 'url' => cf_get_theme_social_url( 'social_soundcloud', '#' ) ),
    array( 'icon' => 'controls-volumeon', 'name' => 'Spotify', 'desc' => 'Official playlists and new releases.', 'url' => cf_get_theme_social_url( 'social_spotify', '#' ) ),
);

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-community-page">
    <div class="cf-page-container">
        <div class="cf-page-inner">
            <section class="cf-community-hero">
                <h1 class="cf-hero-title">Join the Community</h1>
                <p class="cf-hero-lead" style="max-width:520px;margin-bottom:26px;">Every channel we run, in one place — pick your favorite and say hello.</p>
            </section>

            <div class="cf-card-grid cf-card-grid--channels">
                <?php foreach ( $cf_channels as $channel ) : ?>
                    <a class="cf-channel-card" href="<?php echo esc_url( $channel['url'] ); ?>" target="_blank" rel="noopener noreferrer">
                        <div class="cf-channel-card__icon" aria-hidden="true">
                            <span class="dashicons dashicons-<?php echo esc_attr( $channel['icon'] ); ?>"></span>
                        </div>
                        <div>
                            <h2 class="cf-channel-card__title"><?php echo esc_html( $channel['name'] ); ?></h2>
                            <p class="cf-channel-card__desc"><?php echo esc_html( $channel['desc'] ); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
