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
            <?php
            $user_playlists = is_user_logged_in() ? cf_get_user_playlists() : array();
            if ( ! empty( $user_playlists ) ) :
                foreach ( $user_playlists as $index => $playlist ) :
                    ?>
                    <div class="cf-playlist-item" data-playlist-id="<?php echo esc_attr( (string) $index ); ?>">
                        <span class="dashicons dashicons-media-text"></span>
                        <span class="cf-playlist-name"><?php echo esc_html( $playlist['name'] ?? __( 'Playlist', 'collective-finity' ) ); ?></span>
                        <span class="cf-playlist-status dashicons dashicons-no"></span>
                    </div>
                    <?php
                endforeach;
            else :
                ?>
                <p class="cf-playlist-empty-note"><?php esc_html_e( 'No playlists yet. Create one below.', 'collective-finity' ); ?></p>
            <?php endif; ?>
        </div>
        <div class="cf-create-playlist-row">
            <input type="text" id="cf-new-playlist-input" placeholder="<?php esc_attr_e( 'Create new playlist...', 'collective-finity' ); ?>">
            <button type="button" id="cf-create-playlist-btn" class="cf-btn-secondary"><?php esc_html_e( 'Create', 'collective-finity' ); ?></button>
        </div>
    </div>
</div>
