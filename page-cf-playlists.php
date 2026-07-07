<?php
/**
 * Personal playlists page template (slug: cf-playlists).
 *
 * @package Collective_Finity
 */

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-playlists-page">
    <div class="cf-page-container">
        <?php if ( ! is_user_logged_in() ) : ?>
            <?php cf_render_gated_panel( 'playlist-audio', __( 'Sign in to see your Playlists', 'collective-finity' ) ); ?>
        <?php else : ?>
            <h1 class="cf-account-page-title"><?php esc_html_e( 'Personal Playlists', 'collective-finity' ); ?></h1>
            <?php cf_render_user_playlists_section(); ?>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
