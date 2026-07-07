<?php
/**
 * Favorites page template (slug: cf-favorites).
 *
 * @package Collective_Finity
 */

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-favorites-page">
    <div class="cf-page-container">
        <?php if ( ! is_user_logged_in() ) : ?>
            <?php cf_render_gated_panel( 'heart', __( 'Sign in to see your Favorites', 'collective-finity' ) ); ?>
        <?php else : ?>
            <h1 class="cf-account-page-title"><?php esc_html_e( 'Favorites & Liked', 'collective-finity' ); ?></h1>
            <?php cf_render_liked_tracks_section(); ?>
            <?php cf_render_liked_albums_section(); ?>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
