<?php
/**
 * Playlist selection modal.
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
        <input type="hidden" id="cf-target-item-type" value="track">
        <div class="cf-playlists-list"></div>
        <p id="cf-playlist-list-error" class="cf-playlist-modal-error" role="alert" hidden></p>
        <div class="cf-create-playlist-row">
            <input type="text" id="cf-new-playlist-input" placeholder="<?php esc_attr_e( 'Create new playlist...', 'collective-finity' ); ?>">
            <button type="button" id="cf-create-playlist-btn" class="cf-btn-secondary"><?php esc_html_e( 'Create', 'collective-finity' ); ?></button>
        </div>
        <p id="cf-playlist-create-error" class="cf-playlist-modal-error" role="alert" hidden></p>
    </div>
</div>
