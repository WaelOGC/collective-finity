<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="cf-account-sidebar" class="cf-sidebar-panel cf-sidebar-panel--right">
    <div class="cf-sidebar-header">
        <button id="cf-account-sidebar-toggle-btn" class="cf-p-btn" aria-label="<?php esc_attr_e( 'Toggle account panel', 'collective-finity' ); ?>" aria-expanded="false" aria-controls="cf-account-sidebar">
            <span id="cf-account-toggle-icon" class="dashicons dashicons-arrow-left-alt2"></span>
        </button>
        <span class="cf-menu-text cf-sidebar-title-text"><?php esc_html_e( 'Account', 'collective-finity' ); ?></span>
    </div>

    <div class="cf-account-sidebar-user">
        <?php echo do_shortcode( '[cf_user_menu]' ); ?>
    </div>

    <div class="cf-sidebar-divider"></div>

    <div class="cf-account-sidebar-player">
        <div id="cf-global-audio-player">
            <audio id="cf-native-audio-element" preload="auto"></audio>
            <div class="cf-player-track-info">
                <div class="cf-player-cover" id="player-track-cover" style="background-image: url('<?php echo esc_url( $cf_logo_url ); ?>');"></div>
                <div class="cf-player-meta">
                    <div class="cf-player-title" id="player-track-title"><?php esc_html_e( 'Select Track', 'collective-finity' ); ?></div>
                    <div class="cf-player-artist" id="player-track-artist"><?php echo esc_html( $cf_site_name ); ?></div>
                    <div id="player-queue-indicator"></div>
                </div>
            </div>
            <div class="cf-player-controls-wrapper">
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
                <div class="cf-player-progress-container">
                    <div class="cf-player-time" id="player-current-time">0:00</div>
                    <div class="cf-player-progress-bar-bg" id="player-progress-bg">
                        <div class="cf-player-progress-fill" id="player-progress-fill"></div>
                    </div>
                    <div class="cf-player-time" id="player-duration">0:00</div>
                </div>
            </div>
            <div class="cf-player-utilities">
                <button type="button" class="cf-p-btn" id="player-speed-btn" title="<?php esc_attr_e( 'Playback speed', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Playback speed', 'collective-finity' ); ?>">1×</button>
                <button type="button" class="cf-p-btn cf-like-btn" id="player-like-btn" disabled title="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>">
                    <span class="cf-icon cf-icon-heart" aria-hidden="true"></span>
                </button>
                <button type="button" class="cf-p-btn cf-playlist-btn" id="player-playlist-btn" disabled title="<?php esc_attr_e( 'Add to Playlist', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Add to Playlist', 'collective-finity' ); ?>">
                    <span class="cf-icon cf-icon-playlist" aria-hidden="true"></span>
                </button>
                <div class="cf-volume-wrapper">
                    <button type="button" class="cf-p-btn" id="player-volume-icon" aria-label="<?php esc_attr_e( 'Mute', 'collective-finity' ); ?>">
                        <span class="cf-icon cf-icon-volume" aria-hidden="true"></span>
                    </button>
                    <div class="cf-volume-slider-bg" id="player-volume-bg">
                        <div class="cf-volume-fill" id="player-volume-fill"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
