<?php
/**
 * Right sidebar (desktop) + persistent player.
 *
 * Translated from design-reference/Shell.dc.html rightSidebar:
 * account / messages / notifications icons, a login-aware account dropdown,
 * the persistent player card, and an independent collapse toggle
 * (html.cf-player-collapsed). Below 1024px cf-shell.css re-flows this panel
 * into the fixed footer player bar.
 *
 * The player markup keeps every #player-* id / .cf-* class that js/music-player.js
 * binds to, so audio playback is untouched — only the presentation changed.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$cf_logo_url  = collective_finity_site_logo_url( 'thumbnail' );
$cf_site_name = collective_finity_brand_name();

$cf_logged_in   = is_user_logged_in();
$cf_current     = $cf_logged_in ? wp_get_current_user() : null;
$cf_user_name   = $cf_current ? $cf_current->display_name : '';
$cf_user_email  = $cf_current ? $cf_current->user_email : '';
$cf_user_avatar = $cf_current ? get_avatar_url( $cf_current->ID, array( 'size' => 64 ) ) : '';
$cf_initial     = $cf_user_name ? strtoupper( substr( $cf_user_name, 0, 1 ) ) : 'U';

$cf_profile_url  = home_url( '/cf-profile' );
$cf_settings_url = home_url( '/cf-profile#settings' );
$cf_logout_url   = wp_logout_url( home_url( '/' ) );
$cf_login_url    = home_url( '/cf-login' );
$cf_register_url = home_url( '/cf-register' );
?>
<aside class="cf-right-sidebar" aria-label="<?php esc_attr_e( 'Account and player', 'collective-finity' ); ?>">
    <div class="cf-right-icons">
        <?php collective_finity_render_search_trigger(); ?>
        <div class="cf-account">
            <button type="button" id="cf-account-btn" class="cf-icon-btn" aria-haspopup="true" aria-expanded="false" aria-label="<?php esc_attr_e( 'Account', 'collective-finity' ); ?>">
                <?php echo collective_finity_icon( 'user', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </button>
            <div id="cf-account-dropdown" class="cf-dropdown">
                <?php if ( $cf_logged_in ) : ?>
                    <div class="cf-dropdown-head">
                        <span class="cf-dropdown-avatar">
                            <?php if ( $cf_user_avatar ) : ?>
                                <img src="<?php echo esc_url( $cf_user_avatar ); ?>" alt="">
                            <?php else : ?>
                                <?php echo esc_html( $cf_initial ); ?>
                            <?php endif; ?>
                        </span>
                        <span>
                            <span class="cf-dropdown-name"><?php echo esc_html( $cf_user_name ); ?></span>
                            <span class="cf-dropdown-email"><?php echo esc_html( $cf_user_email ); ?></span>
                        </span>
                    </div>
                    <a class="cf-dropdown-item" href="<?php echo esc_url( $cf_profile_url ); ?>"><?php echo collective_finity_icon( 'user', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Profile', 'collective-finity' ); ?></a>
                    <a class="cf-dropdown-item" href="<?php echo esc_url( $cf_settings_url ); ?>"><?php echo collective_finity_icon( 'lock', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Settings', 'collective-finity' ); ?></a>
                    <a class="cf-dropdown-item cf-dropdown-item--danger" href="<?php echo esc_url( $cf_logout_url ); ?>"><?php echo collective_finity_icon( 'close', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Logout', 'collective-finity' ); ?></a>
                <?php else : ?>
                    <a class="cf-dropdown-item cf-dropdown-item--primary" href="<?php echo esc_url( $cf_login_url ); ?>"><?php echo collective_finity_icon( 'user', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Login', 'collective-finity' ); ?></a>
                    <a class="cf-dropdown-item" href="<?php echo esc_url( $cf_register_url ); ?>"><?php echo collective_finity_icon( 'community', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Register', 'collective-finity' ); ?></a>
                <?php endif; ?>
            </div>
        </div>
        <?php // Messages icon hidden from frontend until admin-to-user messaging feature is prioritized. Do not delete — see /docs/PROJECT-LOG.md ?>
        <!--
        <button type="button" class="cf-icon-btn" disabled title="<?php esc_attr_e( 'Messages — coming soon', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Messages', 'collective-finity' ); ?>">
            <?php echo collective_finity_icon( 'mail', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </button>
        -->
        <div class="cf-notifications">
            <button type="button" class="cf-icon-btn" data-cf-notifications-toggle aria-haspopup="true" aria-expanded="false" aria-label="<?php esc_attr_e( 'Notifications', 'collective-finity' ); ?>">
                <?php echo collective_finity_icon( 'bell', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <span class="cf-notif-badge" style="display:none;">0</span>
            </button>
            <div class="cf-notifications-panel">
                <div class="cf-notifications-header">
                    <span class="cf-notifications-title"><?php esc_html_e( 'Notifications', 'collective-finity' ); ?></span>
                    <button type="button" class="cf-notifications-mark-all" data-cf-mark-all-read><?php esc_html_e( 'Mark all as read', 'collective-finity' ); ?></button>
                </div>
                <div class="cf-notifications-list"></div>
                <p class="cf-notifications-empty" style="display:none;"><?php esc_html_e( 'No notifications yet', 'collective-finity' ); ?></p>
            </div>
        </div>
    </div>

    <div id="cf-dropdown-scrim" class="cf-dropdown-scrim" style="display:none;"></div>

    <div class="cf-player-wrap">
        <div id="cf-global-audio-player" class="cf-player">
            <audio id="cf-native-audio-element" preload="auto"></audio>

            <div class="cf-player-cover" id="player-track-cover" style="background-image: url('<?php echo esc_url( $cf_logo_url ); ?>');"></div>

            <div class="cf-player-mid">
                <div class="cf-player-headline">
                    <div class="cf-player-meta">
                        <div class="cf-player-title" id="player-track-title"><?php esc_html_e( 'Select a track', 'collective-finity' ); ?></div>
                        <div class="cf-player-artist" id="player-track-artist"><?php echo esc_html( $cf_site_name ); ?></div>
                        <div id="player-queue-indicator"></div>
                    </div>
                    <button type="button" class="cf-p-btn cf-like-btn" id="player-like-btn" disabled title="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>">
                        <span class="cf-icon cf-icon-heart" aria-hidden="true"></span>
                    </button>
                </div>

                <div class="cf-player-progress-container">
                    <span class="cf-player-time" id="player-current-time">0:00</span>
                    <div class="cf-player-progress-bar-bg" id="player-progress-bg">
                        <div class="cf-player-progress-fill" id="player-progress-fill"></div>
                    </div>
                    <span class="cf-player-time" id="player-duration">0:00</span>
                </div>
            </div>

            <div class="cf-player-buttons">
                <button type="button" class="cf-p-btn" id="player-shuffle-btn" title="<?php esc_attr_e( 'Shuffle', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Shuffle', 'collective-finity' ); ?>">
                    <span class="cf-icon cf-icon-shuffle" aria-hidden="true"></span>
                </button>
                <button type="button" class="cf-p-btn cf-skip-btn" id="player-prev-btn" title="<?php esc_attr_e( 'Previous track', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Previous track', 'collective-finity' ); ?>">
                    <span class="cf-icon cf-icon-prev" aria-hidden="true"></span>
                </button>
                <button type="button" class="cf-p-btn cf-play-trigger" id="player-toggle-btn" aria-label="<?php esc_attr_e( 'Play', 'collective-finity' ); ?>">
                    <span class="cf-icon cf-icon-play" aria-hidden="true"></span>
                </button>
                <button type="button" class="cf-p-btn cf-skip-btn" id="player-next-btn" title="<?php esc_attr_e( 'Next track', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Next track', 'collective-finity' ); ?>">
                    <span class="cf-icon cf-icon-next" aria-hidden="true"></span>
                </button>
                <button type="button" class="cf-p-btn" id="player-repeat-btn" title="<?php esc_attr_e( 'Repeat: off', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Repeat', 'collective-finity' ); ?>" data-mode="off">
                    <span class="cf-icon cf-icon-repeat" aria-hidden="true"></span>
                </button>
            </div>

            <div class="cf-player-utilities">
                <button type="button" class="cf-p-btn" id="player-volume-icon" aria-label="<?php esc_attr_e( 'Mute', 'collective-finity' ); ?>">
                    <span class="cf-icon cf-icon-volume" aria-hidden="true"></span>
                </button>
                <div class="cf-volume-slider-bg" id="player-volume-bg">
                    <div class="cf-volume-fill" id="player-volume-fill"></div>
                </div>
                <button type="button" class="cf-p-btn" id="player-speed-btn" title="<?php esc_attr_e( 'Playback speed', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Playback speed', 'collective-finity' ); ?>">1×</button>
                <button type="button" class="cf-p-btn cf-playlist-btn" id="player-playlist-btn" disabled title="<?php esc_attr_e( 'Add to Playlist', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Add to Playlist', 'collective-finity' ); ?>">
                    <span class="cf-icon cf-icon-playlist" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="cf-sidebar-spacer"></div>

    <button type="button" id="cf-player-collapse-btn" class="cf-right-collapse-btn" aria-expanded="true" aria-label="<?php esc_attr_e( 'Collapse player', 'collective-finity' ); ?>">
        <span class="cf-chevron"><?php echo collective_finity_icon( 'chevronLeft', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
        <span><?php esc_html_e( 'Collapse', 'collective-finity' ); ?></span>
    </button>
</aside>
