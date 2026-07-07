<?php
/**
 * Profile / account page template (slug: cf-profile).
 *
 * @package Collective_Finity
 */

get_header();

$user_id = get_current_user_id();
?>

<main id="primary" class="site-main cf-page-shell cf-profile-page">
    <div class="cf-page-container">
        <?php if ( ! is_user_logged_in() ) : ?>
            <?php cf_render_gated_panel( 'admin-users', __( 'Sign in to view your Profile', 'collective-finity' ) ); ?>
        <?php else : ?>
            <?php
            $user          = wp_get_current_user();
            $liked_tracks  = cf_get_user_liked_track_ids();
            $liked_albums  = cf_get_user_liked_album_ids();
            $playlists     = cf_get_user_playlists();
            $history       = cf_get_user_listening_history();
            $notif_prefs   = get_user_meta( $user_id, '_cf_notif_prefs', true );
            if ( ! is_array( $notif_prefs ) ) {
                $notif_prefs = array();
            }
            $active_tab = 'overview';
            ?>

            <h1 class="cf-account-page-title"><?php esc_html_e( 'My Account', 'collective-finity' ); ?></h1>

            <nav class="cf-profile-tabs" aria-label="<?php esc_attr_e( 'Account sections', 'collective-finity' ); ?>">
                <a href="#overview" class="cf-profile-tab is-active" data-cf-profile-tab="overview"><?php esc_html_e( 'Overview', 'collective-finity' ); ?></a>
                <a href="#favorites" class="cf-profile-tab" data-cf-profile-tab="favorites"><?php esc_html_e( 'Favorites', 'collective-finity' ); ?></a>
                <a href="#history" class="cf-profile-tab" data-cf-profile-tab="history"><?php esc_html_e( 'History', 'collective-finity' ); ?></a>
                <a href="#settings" class="cf-profile-tab" data-cf-profile-tab="settings"><?php esc_html_e( 'Settings', 'collective-finity' ); ?></a>
            </nav>

            <div class="cf-profile-panel" data-cf-profile-panel="overview">
                <div class="cf-profile-overview">
                    <div class="cf-profile-header-card">
                        <?php echo get_avatar( $user_id, 136 ); ?>
                        <div>
                            <h2 style="margin:0 0 4px;font-size:19px;color:#fff;"><?php echo esc_html( $user->display_name ); ?></h2>
                            <p style="margin:0;font-size:13px;color:#7A7A7A;"><?php echo esc_html( $user->user_email ); ?></p>
                            <p style="margin:4px 0 0;font-family:'Space Mono',monospace;font-size:12px;color:#4a4a4a;"><?php echo esc_html( cf_get_member_since_label( $user_id ) ); ?></p>
                        </div>
                    </div>
                    <div class="cf-stat-grid">
                        <div class="cf-stat-card">
                            <p class="cf-stat-card__label"><?php esc_html_e( 'FAV TRACKS', 'collective-finity' ); ?></p>
                            <p class="cf-stat-card__value"><?php echo esc_html( (string) count( $liked_tracks ) ); ?></p>
                        </div>
                        <div class="cf-stat-card">
                            <p class="cf-stat-card__label"><?php esc_html_e( 'FAV ALBUMS', 'collective-finity' ); ?></p>
                            <p class="cf-stat-card__value"><?php echo esc_html( (string) count( $liked_albums ) ); ?></p>
                        </div>
                        <div class="cf-stat-card">
                            <p class="cf-stat-card__label"><?php esc_html_e( 'PLAYLISTS', 'collective-finity' ); ?></p>
                            <p class="cf-stat-card__value"><?php echo esc_html( (string) count( $playlists ) ); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cf-profile-panel" data-cf-profile-panel="favorites" hidden>
                <?php cf_render_liked_tracks_section(); ?>
                <?php cf_render_liked_albums_section(); ?>
            </div>

            <div class="cf-profile-panel" data-cf-profile-panel="history" hidden>
                <section class="cf-profile-section" aria-labelledby="cf-history-heading">
                    <h2 id="cf-history-heading" class="cf-form-section__title"><?php esc_html_e( 'Recently Played', 'collective-finity' ); ?></h2>
                    <?php if ( empty( $history ) ) : ?>
                        <p class="cf-empty-state"><?php esc_html_e( 'Your listening history will appear here as you play tracks.', 'collective-finity' ); ?></p>
                    <?php else : ?>
                        <?php foreach ( array_slice( array_reverse( $history ), 0, 12 ) as $index => $entry ) : ?>
                            <?php
                            $track_id = isset( $entry['track_id'] ) ? (int) $entry['track_id'] : 0;
                            if ( ! $track_id || 'tracks' !== get_post_type( $track_id ) ) {
                                continue;
                            }
                            $artist = cf_get_release_artist_label( $track_id, 'tracks' );
                            $played = isset( $entry['played_at'] ) ? (int) $entry['played_at'] : 0;
                            ?>
                            <a class="cf-history-row" href="<?php echo esc_url( get_permalink( $track_id ) ); ?>">
                                <span class="cf-history-row__index"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
                                <span class="cf-track-row__main">
                                    <span class="cf-track-row__title"><?php echo esc_html( get_the_title( $track_id ) ); ?></span>
                                    <span class="cf-track-row__sub"><?php echo esc_html( $artist ); ?></span>
                                </span>
                                <span class="cf-track-row__album" style="text-align:right;font-family:'Space Mono',monospace;font-size:11.5px;">
                                    <?php echo esc_html( $played ? cf_get_relative_time_label( $played ) : '' ); ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </div>

            <div class="cf-profile-panel" data-cf-profile-panel="settings" hidden>
                <form class="cf-settings-group" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <?php wp_nonce_field( 'cf_update_profile', 'cf_profile_nonce' ); ?>
                    <input type="hidden" name="action" value="cf_update_profile">
                    <h3><?php esc_html_e( 'Profile Details', 'collective-finity' ); ?></h3>
                    <div class="cf-settings-stack">
                        <div>
                            <label class="cf-label" for="cf_display_name"><?php esc_html_e( 'DISPLAY NAME', 'collective-finity' ); ?></label>
                            <input class="cf-input" type="text" id="cf_display_name" name="display_name" value="<?php echo esc_attr( $user->display_name ); ?>">
                        </div>
                        <div>
                            <label class="cf-label" for="cf_user_email"><?php esc_html_e( 'EMAIL', 'collective-finity' ); ?></label>
                            <input class="cf-input" type="email" id="cf_user_email" name="user_email" value="<?php echo esc_attr( $user->user_email ); ?>">
                        </div>
                        <button type="submit" class="cf-btn cf-btn--primary"><?php esc_html_e( 'Save Profile', 'collective-finity' ); ?></button>
                    </div>
                </form>

                <div class="cf-settings-group">
                    <h3><?php esc_html_e( 'Notifications', 'collective-finity' ); ?></h3>
                    <div class="cf-settings-stack">
                        <?php
                        $notif_options = array(
                            'email_updates' => __( 'Email updates', 'collective-finity' ),
                            'new_releases'  => __( 'New release alerts', 'collective-finity' ),
                            'community'     => __( 'Community activity', 'collective-finity' ),
                        );
                        foreach ( $notif_options as $key => $label ) :
                            $is_on = ! empty( $notif_prefs[ $key ] );
                            ?>
                            <div class="cf-toggle-row">
                                <span><?php echo esc_html( $label ); ?></span>
                                <button type="button" class="cf-toggle<?php echo $is_on ? ' is-on' : ''; ?>" data-cf-notif-toggle data-notif-key="<?php echo esc_attr( $key ); ?>" aria-pressed="<?php echo $is_on ? 'true' : 'false'; ?>"></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
