<?php
/**
 * Playlist selection modal (UI placeholder).
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="cf-playlist-modal" class="cf-playlist-modal-overlay" style="display:none;">
    <div class="cf-playlist-modal-content">
        <div class="cf-playlist-modal-header">
            <h3><?php esc_html_e( 'Add to Playlist', 'collective-finity' ); ?></h3>
            <button type="button" id="cf-close-playlist-modal" class="cf-close-modal-btn" aria-label="<?php esc_attr_e( 'Close', 'collective-finity' ); ?>">&times;</button>
        </div>
        <input type="hidden" id="cf-target-track-id" value="">
        <div class="cf-playlists-list">
            <div class="cf-playlist-item" data-playlist-id="1">
                <span class="dashicons dashicons-media-text"></span>
                <span class="cf-playlist-name"><?php esc_html_e( 'My Chill Vibes', 'collective-finity' ); ?></span>
                <span class="cf-playlist-status dashicons dashicons-no"></span>
            </div>
            <div class="cf-playlist-item" data-playlist-id="2">
                <span class="dashicons dashicons-media-text"></span>
                <span class="cf-playlist-name"><?php esc_html_e( 'Late Night Cinematic', 'collective-finity' ); ?></span>
                <span class="cf-playlist-status dashicons dashicons-no"></span>
            </div>
        </div>
        <div class="cf-create-playlist-row">
            <input type="text" id="cf-new-playlist-input" placeholder="<?php esc_attr_e( 'Create new playlist...', 'collective-finity' ); ?>">
            <button type="button" id="cf-create-playlist-btn" class="cf-btn-secondary"><?php esc_html_e( 'Create', 'collective-finity' ); ?></button>
        </div>
    </div>
</div>
