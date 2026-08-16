<?php
/**
 * Fully Upgraded Single Track Template - "Collective Finity" Custom Theme
 * Features: Ambient CSS planet cover art, Live Play/Likes Stats bar,
 * compact Mood/BPM/Key/Release pills, official platform SVGs,
 * Synchronized Lyrics/Story, and custom comments with emoji picker + pagination.
 * Responsive Update: Aspect-ratio scaled vinyl, mobile stacked forms, tablet grids.
 */

get_header();

// Increment Play Counter on page render
$track_id = get_the_ID();
$plays_count = intval( get_post_meta( $track_id, '_cf_track_plays', true ) ) ?: 0;
update_post_meta( $track_id, '_cf_track_plays', $plays_count + 1 );
$updated_plays = $plays_count + 1;

$likes_count = intval( get_post_meta( $track_id, '_cf_total_likes_count', true ) ) ?: 0;

// Fetch comments with fixed 5-per-page pagination (cf_comment_page query var)
$comments_per_page = 5;
$comments_count    = (int) get_comments( array(
    'post_id' => $track_id,
    'status'  => 'approve',
    'count'   => true,
) );
$comment_page = isset( $_GET['cf_comment_page'] ) ? max( 1, absint( wp_unslash( $_GET['cf_comment_page'] ) ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$comment_total_pages = max( 1, (int) ceil( $comments_count / $comments_per_page ) );
if ( $comment_page > $comment_total_pages ) {
    $comment_page = $comment_total_pages;
}
$track_comments = get_comments( array(
    'post_id' => $track_id,
    'status'  => 'approve',
    'order'   => 'ASC',
    'number'  => $comments_per_page,
    'offset'  => ( $comment_page - 1 ) * $comments_per_page,
) );
?>

<div class="cf-single-track-page">
    <?php while ( have_posts() ) : the_post(); 
        // Retrieve Upgraded Metadata
        $audio_url    = get_post_meta( get_the_ID(), 'track_audio_url', true );
        $preview_url  = get_post_meta( get_the_ID(), 'track_preview_url', true );
        $cover_url    = get_post_meta( get_the_ID(), 'track_cover_url', true );
        $copyright    = get_post_meta( get_the_ID(), 'track_copyright', true ) ?: '© ' . date('Y') . ' Collective Finity. All Rights Reserved.';
        $cta_label    = get_post_meta( get_the_ID(), 'track_cta_label', true );
        $cta_url      = get_post_meta( get_the_ID(), 'track_cta_url', true );
        $bpm          = get_post_meta( get_the_ID(), 'track_bpm', true );
        $track_key    = get_post_meta( get_the_ID(), 'track_key', true );
        $show_bpm     = collective_finity_track_show_bpm( get_the_ID() );
        $show_key     = collective_finity_track_show_key( get_the_ID() );
        $show_lyrics  = collective_finity_track_show_lyrics( get_the_ID() );
        $lyrics_url   = get_post_meta( get_the_ID(), 'track_lyrics_url', true );
        $release_type = get_post_meta( get_the_ID(), 'track_release_type', true );
        $playback_url = ! empty( $preview_url ) ? $preview_url : $audio_url;
        $associated_album_id = get_post_meta( get_the_ID(), 'associated_album', true );
        $cf_album_queue      = array();

        if ( $associated_album_id ) {
            $album_tracks_query = new WP_Query(
                array(
                    'post_type'      => 'tracks',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'orderby'        => array(
                        'menu_order' => 'ASC',
                        'title'      => 'ASC',
                    ),
                    'meta_query'     => array(
                        array(
                            'key'     => 'associated_album',
                            'value'   => $associated_album_id,
                            'compare' => '=',
                        ),
                    ),
                )
            );

            if ( $album_tracks_query->have_posts() ) {
                $album_cover = get_the_post_thumbnail_url( $associated_album_id, 'full' );
                if ( empty( $album_cover ) ) {
                    $album_cover = $cover_url;
                }

                while ( $album_tracks_query->have_posts() ) {
                    $album_tracks_query->the_post();
                    $album_track_id      = get_the_ID();
                    $album_track_audio   = get_post_meta( $album_track_id, 'track_audio_url', true );
                    $album_track_preview = get_post_meta( $album_track_id, 'track_preview_url', true );
                    $album_playback_url  = ! empty( $album_track_preview ) ? $album_track_preview : $album_track_audio;
                    $album_track_cover   = get_post_meta( $album_track_id, 'track_cover_url', true );

                    if ( empty( $album_track_cover ) ) {
                        $album_track_cover = get_the_post_thumbnail_url( $album_track_id, 'medium' );
                    }
                    if ( empty( $album_track_cover ) ) {
                        $album_track_cover = $album_cover;
                    }

                    $album_artists = wp_get_post_terms( $album_track_id, 'track_artist' );
                    $album_artist  = ! empty( $album_artists ) ? $album_artists[0]->name : collective_finity_brand_name();

                    if ( $album_playback_url ) {
                        $cf_album_queue[] = array(
                            'url'       => $album_playback_url,
                            'title'     => get_the_title(),
                            'artist'    => $album_artist,
                            'art'       => $album_track_cover,
                            'id'        => $album_track_id,
                            'permalink' => get_permalink( $album_track_id ),
                        );
                    }
                }
                wp_reset_postdata();
            }
        }

        // Streaming Links (URL + admin visibility toggle)
        $streaming_platforms = collective_finity_track_streaming_platforms();
        $streaming_links     = array();
        foreach ( $streaming_platforms as $platform_slug => $platform ) {
            $platform_url = get_post_meta( get_the_ID(), $platform['meta'], true );
            if ( $platform_url && collective_finity_track_show_streaming( get_the_ID(), $platform_slug ) ) {
                $streaming_links[ $platform_slug ] = $platform_url;
            }
        }

        // Genre Taxonomy
        $genres = wp_get_post_terms( get_the_ID(), 'music_genre' );
        $genre_name = ! empty( $genres ) ? $genres[0]->name : 'Ambient';

        // Artist Taxonomy
        $artists = wp_get_post_terms( get_the_ID(), 'track_artist' );
        $artist_name = ! empty( $artists ) ? $artists[0]->name : 'Collective Finity';

        // Smart Cover Fallback
        if ( empty( $cover_url ) ) {
            if ( $associated_album_id ) {
                $cover_url = get_the_post_thumbnail_url( $associated_album_id, 'full' );
            }
        }
        if ( empty( $cover_url ) ) {
            $cover_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
        }
        if ( empty( $cover_url ) ) {
            $cover_url = collective_finity_default_art_url();
        }
    ?>

    <!-- Frosted cover-art hero with brand gold tint -->
    <div class="cf-cinematic-hero" style="--cf-hero-art: url('<?php echo esc_url($cover_url); ?>');">
        <div class="cf-container">
            
            <div class="cf-track-header-layout">
                
                <!-- 2. Cover art as ambient CSS "planet" sphere -->
                <div class="cf-vinyl-wrapper">
                    <div class="cf-vinyl-inner">
                        <div class="cf-vinyl-planet">
                            <img src="<?php echo esc_url($cover_url); ?>" class="cf-vinyl-disc" id="cf-track-spinning-vinyl" alt="<?php the_title(); ?>">
                            <span class="cf-vinyl-sphere-shade" aria-hidden="true"></span>
                            <span class="cf-vinyl-sphere-sheen" aria-hidden="true"></span>
                        </div>
                    </div>
                </div>

                <!-- 3. MAIN DETAILS CARD -->
                <div class="cf-details-panel cf-glass-card">
                    <span class="cf-tag"><?php echo esc_html($genre_name); ?></span>
                    
                    <h1 class="cf-hero-title"><?php the_title(); ?></h1>
                    
                    <!-- 3. Dynamic Stats row -->
                    <div class="cf-track-stats-row">
                        <span class="cf-stat-item"><i class="dashicons dashicons-visibility"></i> <strong class="live-views"><?php echo number_format($updated_plays); ?></strong> <?php _e('views', 'collective-finity'); ?></span>
                        <span class="cf-stat-item"><i class="dashicons dashicons-heart"></i> <strong class="live-likes"><?php echo number_format($likes_count); ?></strong> <?php _e('likes', 'collective-finity'); ?></span>
                        <a href="#cf-track-comments" class="cf-stat-item cf-stat-item--link"><i class="dashicons dashicons-admin-comments"></i> <strong class="live-comments"><?php echo number_format($comments_count); ?></strong> <?php _e('comments', 'collective-finity'); ?></a>
                    </div>

                    <!-- Artist Info Box with Avatar -->
                    <div class="cf-artist-profile-row">
                        <span class="cf-artist-avatar-icon dashicons dashicons-admin-users"></span>
                        <p class="cf-hero-artist">
                            <?php
                            if ( ! empty( $artists ) && ! is_wp_error( $artists ) ) {
                                $cf_artist_term_link = get_term_link( $artists[0] );
                                if ( ! is_wp_error( $cf_artist_term_link ) ) {
                                    printf(
                                        '<a href="%1$s">%2$s</a>',
                                        esc_url( $cf_artist_term_link ),
                                        esc_html( $artists[0]->name )
                                    );
                                } else {
                                    echo esc_html( $artist_name );
                                }
                            } else {
                                echo esc_html( $artist_name );
                            }
                            ?>
                        </p>
                    </div>
                    <p class="cf-hero-subline">
                        <?php echo esc_html( $release_type === 'album_track' ? __( 'Album track', 'collective-finity' ) : __( 'Featured single', 'collective-finity' ) ); ?>
                        <?php if ( $show_bpm && $bpm ) : ?> · <?php echo esc_html( $bpm ); ?> BPM<?php endif; ?>
                        <?php if ( $show_key && $track_key ) : ?> · <?php echo esc_html( $track_key ); ?><?php endif; ?>
                    </p>
                    
                    <div class="cf-hero-actions-row">
                        <!-- Play Action Button -->
                        <?php if ( $playback_url ) : ?>
                            <button class="cf-play-btn-hero" onclick="playTrack('<?php echo esc_url($playback_url); ?>', '<?php echo esc_js(get_the_title()); ?>', '<?php echo esc_js($artist_name); ?>', '<?php echo esc_url($cover_url); ?>', <?php echo (int) $track_id; ?>)">
                                <span class="dashicons dashicons-controls-play"></span> <?php echo ! empty( $preview_url ) ? __( 'PREVIEW / STREAM', 'collective-finity' ) : __( 'STREAM NOW', 'collective-finity' ); ?>
                            </button>
                        <?php endif; ?>

                        <!-- CTA Button -->
                        <?php if ( $cta_url && $cta_label ) : ?>
                            <a href="<?php echo esc_url($cta_url); ?>" class="cf-cta-btn-hero" target="_blank"><?php echo esc_html($cta_label); ?></a>
                        <?php endif; ?>

                        <button type="button" class="cf-share-btn cf-track-share-btn" data-cf-share data-track-id="<?php echo esc_attr( (string) $track_id ); ?>" data-url="<?php echo esc_url( get_permalink() ); ?>" data-title="<?php the_title_attribute(); ?>" title="<?php esc_attr_e( 'Share', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Share', 'collective-finity' ); ?>">
                            <?php echo collective_finity_icon( 'share', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <span><?php esc_html_e( 'Share', 'collective-finity' ); ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="cf-track-body">
        <div class="cf-container">
            <!-- 4. VISITOR-FRIENDLY EMOTIONAL METADATA CARDS -->
            <div class="cf-meta-grid">
                <div class="cf-meta-box cf-glass-card">
                    <span class="cf-meta-left">
                        <span class="cf-meta-icon dashicons dashicons-heart"></span>
                        <span class="cf-meta-title"><?php _e('MOOD / VIBE', 'collective-finity'); ?></span>
                    </span>
                    <span class="cf-meta-value"><?php _e('Cinematic & Emotional', 'collective-finity'); ?></span>
                </div>
                <?php if ( $show_bpm ) : ?>
                <div class="cf-meta-box cf-glass-card">
                    <span class="cf-meta-left">
                        <span class="cf-meta-icon dashicons dashicons-clock"></span>
                        <span class="cf-meta-title"><?php _e('BPM', 'collective-finity'); ?></span>
                    </span>
                    <span class="cf-meta-value"><?php echo $bpm ? esc_html($bpm) : __('—', 'collective-finity'); ?></span>
                </div>
                <?php endif; ?>
                <?php if ( $show_key ) : ?>
                <div class="cf-meta-box cf-glass-card">
                    <span class="cf-meta-left">
                        <span class="cf-meta-icon dashicons dashicons-admin-customizer"></span>
                        <span class="cf-meta-title"><?php _e('KEY', 'collective-finity'); ?></span>
                    </span>
                    <span class="cf-meta-value"><?php echo $track_key ? esc_html($track_key) : __('—', 'collective-finity'); ?></span>
                </div>
                <?php endif; ?>
                <div class="cf-meta-box cf-glass-card">
                    <span class="cf-meta-left">
                        <span class="cf-meta-icon dashicons dashicons-calendar-alt"></span>
                        <span class="cf-meta-title"><?php _e('RELEASE DATE', 'collective-finity'); ?></span>
                    </span>
                    <span class="cf-meta-value"><?php echo get_the_date('M Y'); ?></span>
                </div>

                <?php if ( ! empty( $streaming_links ) ) : ?>
                <div class="cf-external-platforms-wrapper cf-glass-card">
                    <span class="cf-platforms-title"><?php _e('LISTEN', 'collective-finity'); ?></span>
                    <div class="cf-platforms-grid">
                    <?php if ( ! empty( $streaming_links['spotify'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['spotify'] ); ?>" target="_blank" class="cf-platform-icon-btn" title="Spotify">
                            <svg viewBox="0 0 24 24"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $streaming_links['apple'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['apple'] ); ?>" target="_blank" class="cf-platform-icon-btn" title="Apple Music">
                            <svg viewBox="0 0 24 24"><path d="M23.994 6.124a9.23 9.23 0 00-.24-2.19c-.317-1.31-1.062-2.31-2.18-3.043a5.022 5.022 0 00-1.877-.726 10.496 10.496 0 00-1.564-.15c-.04-.003-.083-.01-.124-.013H5.986c-.152.01-.303.017-.455.026-.747.043-1.49.123-2.193.4-1.336.53-2.3 1.452-2.865 2.78-.192.448-.292.925-.363 1.408-.056.392-.088.785-.1 1.18 0 .032-.007.062-.01.093v12.223c.01.14.017.283.027.424.05.815.154 1.624.497 2.373.65 1.42 1.738 2.353 3.234 2.801.42.127.856.187 1.293.228.555.053 1.11.06 1.667.06h11.03a12.5 12.5 0 001.57-.1c.822-.106 1.596-.35 2.295-.81a5.046 5.046 0 001.88-2.207c.186-.42.293-.87.37-1.324.113-.675.138-1.358.137-2.04-.002-3.8 0-7.595-.003-11.393zm-6.423 3.99v5.712c0 .417-.058.827-.244 1.206-.29.59-.76.962-1.388 1.14-.35.1-.706.157-1.07.173-.95.045-1.773-.6-1.943-1.536a1.88 1.88 0 011.038-2.022c.323-.16.67-.25 1.018-.324.378-.082.758-.153 1.134-.24.274-.063.457-.23.51-.516a.904.904 0 00.02-.193c0-1.815 0-3.63-.002-5.443a.725.725 0 00-.026-.185c-.04-.15-.15-.243-.304-.234-.16.01-.318.035-.475.066-.76.15-1.52.303-2.28.456l-2.325.47-1.374.278c-.016.003-.032.01-.048.013-.277.077-.377.203-.39.49-.002.042 0 .086 0 .13-.002 2.602 0 5.204-.003 7.805 0 .42-.047.836-.215 1.227-.278.64-.77 1.04-1.434 1.233-.35.1-.71.16-1.075.172-.96.036-1.755-.6-1.92-1.544-.14-.812.23-1.685 1.154-2.075.357-.15.73-.232 1.108-.31.287-.06.575-.116.86-.177.383-.083.583-.323.6-.714v-.15c0-2.96 0-5.922.002-8.882 0-.123.013-.25.042-.37.07-.285.273-.448.546-.518.255-.066.515-.112.774-.165.733-.15 1.466-.296 2.2-.444l2.27-.46c.67-.134 1.34-.27 2.01-.403.22-.043.442-.088.663-.106.31-.025.523.17.554.482.008.073.012.148.012.223.002 1.91.002 3.822 0 5.732z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $streaming_links['soundcloud'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['soundcloud'] ); ?>" target="_blank" class="cf-platform-icon-btn" title="Soundcloud">
                            <svg viewBox="0 0 24 24"><path d="M23.999 14.165c-.052 1.796-1.612 3.169-3.4 3.169h-8.18a.68.68 0 0 1-.675-.683V7.862a.747.747 0 0 1 .452-.724s.75-.513 2.333-.513a5.364 5.364 0 0 1 2.763.755 5.433 5.433 0 0 1 2.57 3.54c.282-.08.574-.121.868-.12.884 0 1.73.358 2.347.992s.948 1.49.922 2.373ZM10.721 8.421c.247 2.98.427 5.697 0 8.672a.264.264 0 0 1-.53 0c-.395-2.946-.22-5.718 0-8.672a.264.264 0 0 1 .53 0ZM9.072 9.448c.285 2.659.37 4.986-.006 7.655a.277.277 0 0 1-.55 0c-.331-2.63-.256-5.02 0-7.655a.277.277 0 0 1 .556 0Zm-1.663-.257c.27 2.726.39 5.171 0 7.904a.266.266 0 0 1-.532 0c-.38-2.69-.257-5.21 0-7.904a.266.266 0 0 1 .532 0Zm-1.647.77a26.108 26.108 0 0 1-.008 7.147.272.272 0 0 1-.542 0 27.955 27.955 0 0 1 0-7.147.275.275 0 0 1 .55 0Zm-1.67 1.769c.421 1.865.228 3.5-.029 5.388a.257.257 0 0 1-.514 0c-.21-1.858-.398-3.549 0-5.389a.272.272 0 0 1 .543 0Zm-1.655-.273c.388 1.897.26 3.508-.01 5.412-.026.28-.514.283-.54 0-.244-1.878-.347-3.54-.01-5.412a.283.283 0 0 1 .56 0Zm-1.668.911c.4 1.268.257 2.292-.026 3.572a.257.257 0 0 1-.514 0c-.241-1.262-.354-2.312-.023-3.572a.283.283 0 0 1 .563 0Z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $streaming_links['youtube'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['youtube'] ); ?>" target="_blank" class="cf-platform-icon-btn" title="YouTube">
                            <svg viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $streaming_links['bandcamp'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['bandcamp'] ); ?>" target="_blank" class="cf-platform-icon-btn cf-platform-icon-btn--lg" title="Bandcamp">
                            <svg viewBox="0 0 24 24"><path d="M0 18.75l7.437-13.5H24l-7.438 13.5H0z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $streaming_links['amazon'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['amazon'] ); ?>" target="_blank" class="cf-platform-icon-btn" title="Amazon Music">
                            <svg viewBox="0 0 448 512"><path d="M257.7 162.7c-48.7 1.8-169.5 15.5-169.5 117.5 0 109.5 138.3 114 183.5 43.2 6.5 10.2 35.4 37.5 45.3 46.8l56.8-56s-32.3-25.3-32.3-52.8l0-147.1C341.5 89 317 32 229.2 32 141.2 32 94.5 87 94.5 136.3l73.5 6.8c16.3-49.5 54.2-49.5 54.2-49.5 40.7-.1 35.5 29.8 35.5 69.1zm0 86.8c0 80-84.2 68-84.2 17.2 0-47.2 50.5-56.7 84.2-57.8l0 40.6zM393.7 413c-7.7 10-70 67-174.5 67S34.7 408.5 10.2 379c-6.8-7.7 1-11.3 5.5-8.3 73.3 44.5 187.8 117.8 372.5 30.3 7.5-3.7 13.3 2 5.5 12zm39.8 2.2c-6.5 15.8-16 26.8-21.2 31-5.5 4.5-9.5 2.7-6.5-3.8s19.3-46.5 12.7-55c-6.5-8.3-37-4.3-48-3.2-10.8 1-13 2-14-.3-2.3-5.7 21.7-15.5 37.5-17.5 15.7-1.8 41-.8 46 5.7 3.7 5.1 0 27.1-6.5 43.1z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $streaming_links['google_play'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['google_play'] ); ?>" target="_blank" class="cf-platform-icon-btn cf-platform-icon-btn--lg" title="Google Play Music">
                            <svg viewBox="0 0 24 24"><path d="M22.018 13.298l-3.919 2.218-3.515-3.493 3.543-3.521 3.891 2.202a1.49 1.49 0 0 1 0 2.594zM1.337.924a1.486 1.486 0 0 0-.112.568v21.017c0 .217.045.419.124.6l11.155-11.087L1.337.924zm12.207 10.065l3.258-3.238L3.45.195a1.466 1.466 0 0 0-.946-.179l11.04 10.973zm0 2.067l-11 10.933c.298.036.612-.016.906-.183l13.324-7.54-3.23-3.21z"/></svg>
                        </a>
                    <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php if ( function_exists( 'collective_finity_ad_slot_wrapped' ) ) : ?>
            <?php collective_finity_ad_slot_wrapped( 'track_sidebar', '<div class="cf-track-ad-sidebar cf-glass-card">', '</div>' ); ?>
            <?php endif; ?>

            <!-- 6. STORY & AUDIO-TRACKED LYRICS PLAYER SECTION -->
            <div class="cf-content-area cf-glass-card">
                <?php if ( $show_lyrics ) : ?>
                <h2><?php _e('Story & Concept Behind the Track', 'collective-finity'); ?></h2>
                
                <div class="cf-entry-content">
                    <?php the_content(); ?>
                </div>

                <!-- 6. Dynamic Audio Voice Tracking Lyrics System -->
                <?php if ( ! empty( $lyrics_url ) ) : ?>
                <div class="cf-lyrics-tracker-container" style="margin-bottom: 40px;">
                    <h3><?php _e('Lyrics / Narrative Sync', 'collective-finity'); ?></h3>
                    <?php $cf_lyric_cues = collective_finity_parse_lyrics_file( $lyrics_url ); ?>
                    <div id="cf-lyrics-sync-playlist">
                        <?php if ( ! empty( $cf_lyric_cues ) ) : ?>
                            <?php foreach ( $cf_lyric_cues as $cue ) : ?>
                                <p class="cf-sync-line" data-start="<?php echo esc_attr( $cue['start'] ); ?>" data-end="<?php echo esc_attr( $cue['end'] ); ?>"><?php echo esc_html( $cue['text'] ); ?></p>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="cf-sync-line-empty"><?php esc_html_e( 'No synced lyrics available for this track.', 'collective-finity' ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>

                <!-- 2. STYLIZED COMMENTS SECTION WITH EMOJI SELECTOR AND FORCED INPUT FORM -->
                <div id="cf-track-comments" class="cf-comments-section-wrapper" style="<?php echo $show_lyrics ? 'border-top:1px solid rgba(255,255,255,0.1); padding-top:40px;' : ''; ?>">
                    <h3><?php _e('Listener Discussion', 'collective-finity'); ?></h3>
                    
                    <!-- Emoji Picker Bar -->
                    <div class="cf-emoji-picker-container" style="margin-bottom: 25px;">
                        <span class="cf-emoji-label"><?php _e('Add quick emoji:', 'collective-finity'); ?></span>
                        <div class="cf-emoji-list">
                            <button type="button" class="cf-emoji-btn" data-emoji="🎵">🎵</button>
                            <button type="button" class="cf-emoji-btn" data-emoji="🔥">🔥</button>
                            <button type="button" class="cf-emoji-btn" data-emoji="❤️">❤️</button>
                            <button type="button" class="cf-emoji-btn" data-emoji="✨">✨</button>
                            <button type="button" class="cf-emoji-btn" data-emoji="🎧">🎧</button>
                            <button type="button" class="cf-emoji-btn" data-emoji="🌟">🌟</button>
                            <button type="button" class="cf-emoji-btn" data-emoji="🚀">🚀</button>
                            <button type="button" class="cf-emoji-btn" data-emoji="🎼">🎼</button>
                        </div>
                    </div>

                    <!-- Pure Customized Comment Input Form (Bypasses comments_template / comments.php requirements) -->
                    <div class="cf-custom-comment-form-container cf-glass-card" style="padding: 25px; border-radius: 8px; margin-bottom: 40px;">
                        <?php if ( is_user_logged_in() ) : 
                            $current_user = wp_get_current_user(); ?>
                            <p class="cf-logged-in-user-label" style="color: #888; font-size: 13px; font-family: 'Space Mono', monospace; margin-top: 0; margin-bottom: 15px;">
                                <?php printf( __( 'Logged in as <strong style="color:var(--primary-color);">%s</strong>.', 'collective-finity' ), esc_html( $current_user->display_name ) ); ?>
                            </p>
                        <?php endif; ?>

                        <form action="<?php echo esc_url( site_url( '/wp-comments-post.php' ) ); ?>" method="post" id="commentform">
                            <div class="cf-form-field">
                                <label for="comment" style="display:block; font-weight:bold; margin-bottom: 8px; font-size:14px;"><?php _e( 'Your Comment:', 'collective-finity' ); ?></label>
                                <textarea name="comment" id="comment" required placeholder="<?php esc_attr_e( 'Share your listener connection or thoughts here...', 'collective-finity' ); ?>" style="width:100%; height:100px; padding: 10px; background: rgba(0,0,0,0.5); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:4px; resize:vertical;"></textarea>
                            </div>

                            <?php if ( ! is_user_logged_in() ) : ?>
                                <div class="cf-form-row-flex" style="display:flex; gap: 15px; margin-top: 15px;">
                                    <div class="cf-form-field" style="flex:1;">
                                        <label for="author" style="display:block; font-size:12px; color:#888; margin-bottom:5px;"><?php _e( 'Name *', 'collective-finity' ); ?></label>
                                        <input type="text" name="author" id="author" required style="width:100%; padding:8px; background:rgba(0,0,0,0.5); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:4px;" />
                                    </div>
                                    <div class="cf-form-field" style="flex:1;">
                                        <label for="email" style="display:block; font-size:12px; color:#888; margin-bottom:5px;"><?php _e( 'Email *', 'collective-finity' ); ?></label>
                                        <input type="email" name="email" id="email" required style="width:100%; padding:8px; background:rgba(0,0,0,0.5); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:4px;" />
                                    </div>
                                </div>
                            <?php endif; ?>

                            <input type="hidden" name="comment_post_ID" value="<?php echo get_the_ID(); ?>" id="comment_post_ID">
                            <input type="hidden" name="comment_parent" id="comment_parent" value="0">
                            
                            <button type="submit" id="cf-submit-comment-btn" class="button" style="margin-top: 20px; background: var(--primary-color); border:none; color:#000; padding:10px 25px; border-radius:30px; font-weight:bold; cursor:pointer;"><?php _e( 'Post Comment', 'collective-finity' ); ?></button>
                        </form>
                    </div>

                    <!-- Custom-rendered Comments List -->
                    <div class="cf-custom-comments-list-wrapper">
                        <?php if ( ! empty( $track_comments ) ) : ?>
                            <ul class="cf-custom-comments-ul" style="list-style:none; padding:0; margin:0;">
                                <?php foreach ( $track_comments as $comment ) : 
                                    $comment_avatar = get_avatar( $comment->comment_author_email, 48 );
                                ?>
                                    <li class="cf-comment-list-item cf-glass-card" style="padding: 20px; border-radius: 8px; margin-bottom: 15px; border:1px solid rgba(255,255,255,0.05);">
                                        <div class="cf-comment-author-meta" style="display:flex; align-items:center; gap: 15px; margin-bottom:12px;">
                                            <div class="cf-commenter-avatar"><?php echo $comment_avatar; ?></div>
                                            <div>
                                                <span class="cf-commenter-name" style="display:block; font-weight:bold; color:#fff; font-size:14px;"><?php echo esc_html($comment->comment_author); ?></span>
                                                <span class="cf-comment-time-stamp" style="font-size:11px; color:#555; font-family:'Space Mono', monospace;"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $comment->comment_date ) ) ); ?></span>
                                            </div>
                                        </div>
                                        <div class="cf-comment-text-body" style="font-size:14px; line-height:1.6; color:#ccc;">
                                            <?php echo wp_kses_post( wpautop( $comment->comment_content ) ); ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if ( $comment_total_pages > 1 ) :
                                $comment_pages = array();
                                if ( $comment_total_pages <= 9 ) {
                                    for ( $i = 1; $i <= $comment_total_pages; $i++ ) {
                                        $comment_pages[] = $i;
                                    }
                                } elseif ( $comment_page <= 5 ) {
                                    for ( $i = 1; $i <= 8; $i++ ) {
                                        $comment_pages[] = $i;
                                    }
                                    $comment_pages[] = '…';
                                    $comment_pages[] = $comment_total_pages;
                                } elseif ( $comment_page >= $comment_total_pages - 4 ) {
                                    $comment_pages[] = 1;
                                    $comment_pages[] = '…';
                                    for ( $i = $comment_total_pages - 7; $i <= $comment_total_pages; $i++ ) {
                                        $comment_pages[] = $i;
                                    }
                                } else {
                                    $comment_pages[] = 1;
                                    $comment_pages[] = '…';
                                    for ( $i = $comment_page - 2; $i <= $comment_page + 2; $i++ ) {
                                        $comment_pages[] = $i;
                                    }
                                    $comment_pages[] = '…';
                                    $comment_pages[] = $comment_total_pages;
                                }
                                $comment_base = get_permalink( $track_id );
                                ?>
                            <div class="cf-pagination-wrap">
                                <nav class="cf-pagination" aria-label="<?php esc_attr_e( 'Comments pagination', 'collective-finity' ); ?>">
                                    <div class="cf-pagination-pages">
                                        <?php foreach ( $comment_pages as $p ) : ?>
                                            <?php if ( $p === '…' ) : ?>
                                                <span class="cf-pagination-ellipsis" aria-hidden="true">…</span>
                                            <?php elseif ( (int) $p === $comment_page ) : ?>
                                                <span class="cf-pagination-page is-active" aria-current="page"><?php echo (int) $p; ?></span>
                                            <?php else : ?>
                                                <a class="cf-pagination-page" href="<?php echo esc_url( add_query_arg( 'cf_comment_page', (int) $p, $comment_base ) . '#cf-track-comments' ); ?>"><?php echo (int) $p; ?></a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <?php if ( $comment_page < $comment_total_pages ) : ?>
                                            <a class="cf-pagination-page cf-pagination-next" href="<?php echo esc_url( add_query_arg( 'cf_comment_page', $comment_page + 1, $comment_base ) . '#cf-track-comments' ); ?>">
                                                <?php esc_html_e( 'Next', 'collective-finity' ); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </nav>
                            </div>
                            <?php endif; ?>
                        <?php else : ?>
                            <p class="cf-empty-comments-message" style="color:#666; font-style:italic; padding: 20px; background:rgba(255,255,255,0.01); border-radius:6px; border:1px solid rgba(255,255,255,0.03);"><?php _e( 'No comments yet. Share your cinematic connection with this track first!', 'collective-finity' ); ?></p>
                        <?php endif; ?>
                    </div>

                </div>

                <p class="cf-copyright-text"><?php echo esc_html($copyright); ?></p>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<script type="text/javascript">
window.cfPageTrackId = <?php echo (int) $track_id; ?>;
window.cfPageTrackPermalink = <?php echo wp_json_encode( get_permalink( $track_id ) ); ?>;
<?php if ( ! empty( $cf_album_queue ) ) : ?>
window.cfAlbumQueue = <?php echo wp_json_encode( $cf_album_queue ); ?>;
<?php endif; ?>
</script>

<style>
/* CSS Styles with requested #FFB700 Color Scheme */

:root {
    --primary-color: #FFB700;       /* Requested Warm Yellow-Orange */
    --hover-bg-trans: #FFB70026;    /* Requested Transparent Hover Opacity */
    --accent-color:rgb(255, 219, 74);
    --secondary-bg:rgb(22, 20, 20);
    --bg-color: #000000;
    --text-color: #FFFFFF;
}

/* Main Track Page Styling */
.cf-single-track-page {
    background: radial-gradient(circle at top, rgba(255, 183, 0, 0.16), transparent 32%), #000;
    min-height: 100vh;
    padding: 0 5px 5px;
    max-width: 100%;
    overflow-x: hidden;
    box-sizing: border-box;
}
.cf-cinematic-hero {
    background: #0a0a0a;
    padding: 108px 0 56px;
    position: relative;
    overflow: hidden;
    border-bottom: 1px solid rgba(255, 183, 0, 0.16);
}
.cf-cinematic-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: var(--cf-hero-art);
    background-size: cover;
    background-position: center;
    filter: blur(18px) saturate(115%);
    transform: scale(1.08);
    opacity: 1;
    z-index: 0;
}
.cf-cinematic-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 80% 55% at 18% -8%, rgba(255, 183, 0, 0.22), transparent 58%),
        radial-gradient(ellipse 55% 40% at 92% 8%, rgba(255, 183, 0, 0.12), transparent 52%),
        linear-gradient(180deg, transparent 55%, rgba(0, 0, 0, 0.45) 100%),
        linear-gradient(140deg, rgba(0, 0, 0, 0.38) 0%, rgba(0, 0, 0, 0.32) 45%, rgba(0, 0, 0, 0.4) 100%);
    z-index: 0;
    pointer-events: none;
}
.cf-cinematic-hero > .cf-container {
    position: relative;
    z-index: 1;
}
.cf-track-body {
    position: relative;
    z-index: 1;
    padding: 40px 0 20px;
}
.cf-container { width: 90%; max-width: 1100px; margin: 0 auto; box-sizing: border-box; }
.cf-track-header-layout { display: flex; align-items: center; gap: 50px; margin-bottom: 0; flex-wrap: wrap; min-width: 0; max-width: 100%; }

/* 2. Cover art planet sphere (CSS-only ambient motion) */
.cf-vinyl-wrapper { flex: 1 1 250px; min-width: 0; max-width: 100%; display: flex; flex-direction: column; align-items: center; position: relative; }
.cf-vinyl-inner { position: relative; width: 100%; max-width: 340px; aspect-ratio: 1 / 1; display: flex; justify-content: center; align-items: center; margin-bottom: 20px; }
.cf-vinyl-planet { position: relative; width: 82%; max-width: 280px; aspect-ratio: 1 / 1; border-radius: 50%; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 0 25px rgba(0,0,0,0.8), inset 0 0 40px rgba(0,0,0,0.35); }
.cf-vinyl-disc { display: block; width: 100% !important; height: 100% !important; max-width: none; border-radius: 50%; object-fit: cover; position: relative; z-index: 1; }
.cf-vinyl-sphere-shade { position: absolute; inset: 0; border-radius: 50%; pointer-events: none; z-index: 2; background: radial-gradient(circle at 32% 28%, rgba(255,255,255,0.32) 0%, rgba(255,255,255,0.08) 28%, transparent 48%, rgba(0,0,0,0.25) 72%, rgba(0,0,0,0.55) 100%); }
.cf-vinyl-sphere-sheen { position: absolute; inset: 0; border-radius: 50%; pointer-events: none; z-index: 3; overflow: hidden; }
.cf-vinyl-sphere-sheen::before { content: ''; position: absolute; top: -12%; bottom: -12%; width: 26%; left: -35%; background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.08) 35%, rgba(255,255,255,0.28) 50%, rgba(255,255,255,0.08) 65%, transparent 100%); transform: skewX(-8deg); animation: cf-planet-sheen 8s linear infinite; }
@keyframes cf-planet-sheen { 0% { transform: skewX(-8deg) translateX(0); } 100% { transform: skewX(-8deg) translateX(480%); } }

/* 3. Central Details Panel Styles */
.cf-details-panel { flex: 1.5 1 300px; min-width: 0; max-width: 100%; box-sizing: border-box; padding: 36px; border-radius: 18px; text-align: left; }
.cf-glass-card { background: rgba(16, 16, 16, 0.72); backdrop-filter: blur(14px); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 10px 35px rgba(0, 0, 0, 0.45); box-sizing: border-box; max-width: 100%; }
.cf-tag { display: inline-block; background-color: var(--primary-color); color: #000; padding: 5px 12px; font-size: 11px; font-weight: bold; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
.cf-hero-title { font-size: clamp(32px, 3.2vw, 42px); line-height: 1.08; margin: 0 0 10px 0; color: #fff; }

/* 3. Stats Row styling */
.cf-track-stats-row { display: flex; gap: 20px; margin-bottom: 20px; font-size: 13px; color: #888; font-family: 'Space Mono', monospace; flex-wrap: wrap; }
.cf-stat-item { display: flex; align-items: center; gap: 6px; }
.cf-stat-item--link {
    text-decoration: none;
    color: #888;
    transition: color 0.2s;
}
.cf-stat-item--link:hover {
    color: var(--primary-color);
}
.cf-stat-item .dashicons { font-size: 16px; width: 16px; height: 16px; color: var(--accent-color); }

/* Artist Info Box with Avatar */
.cf-artist-profile-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
.cf-artist-avatar-icon { font-size: 24px; width: 24px; height: 24px; color: #888; }
.cf-hero-artist { font-size: 16px; color: #aaa; margin: 0; font-weight: bold; }
.cf-hero-artist a { color: inherit; text-decoration: none; transition: color 0.2s ease; }
.cf-hero-artist a:hover { color: var(--primary-color, #FFB700); text-decoration: underline; }
.cf-hero-subline { margin: 0 0 24px; font-size: 13px; letter-spacing: 0.14em; text-transform: uppercase; color: #b7b7b7; }

.cf-hero-actions-row { display: flex; gap: 15px; flex-wrap: wrap; align-items: center; }
.cf-play-btn-hero { background: linear-gradient(135deg, var(--primary-color), #ffce4d); color: #050505; border: none; padding: 14px 32px; font-size: 16px; font-weight: bold; border-radius: 999px; cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.25s ease; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 8px 20px rgba(255, 183, 0, 0.22); }
.cf-play-btn-hero:hover { background: linear-gradient(135deg, #ffd460, var(--primary-color)); transform: translateY(-1px); box-shadow: 0 10px 24px rgba(255, 183, 0, 0.3); }
.cf-cta-btn-hero { background: transparent; color: #FFFFFF; border: 2px solid rgba(255,255,255,0.75); padding: 12px 30px; font-size: 16px; font-weight: bold; border-radius: 999px; text-decoration: none; transition: background 0.25s, color 0.25s, border-color 0.25s; }
.cf-cta-btn-hero:hover { background: rgba(255, 183, 0, 0.12); border-color: var(--primary-color); color: var(--primary-color); }
.cf-track-share-btn {
    display: inline-flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 22px;
    border-radius: 999px;
    border: 1px solid rgba(255, 183, 0, 0.35);
    background: rgba(255, 183, 0, 0.08);
    color: #FFB700;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: color 0.2s ease, border-color 0.2s ease, background 0.2s ease;
    min-height: 48px;
    white-space: nowrap;
}
.cf-track-share-btn svg { flex-shrink: 0; color: currentColor; stroke: currentColor; fill: none; }
.cf-track-share-btn:hover { color: #ffde99; border-color: rgba(255, 222, 153, 0.5); background: rgba(255, 183, 0, 0.14); }

/* Text & Description Links Hover Rules */
.cf-entry-content a { color: var(--text-color); text-decoration: underline; transition: color 0.25s; }
.cf-entry-content a:hover { color: var(--primary-color); }

/* 4. Compact metadata pills + listen platforms (one flex row) */
.cf-meta-grid { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; margin-bottom: 40px; }
.cf-meta-box { width: fit-content; max-width: 100%; padding: 8px 14px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: space-between; gap: 10px; transition: border-color 0.25s, transform 0.25s; border: 1px solid rgba(255,255,255,0.08); }
.cf-meta-box:hover { border-color: rgba(255, 183, 0, 0.4); transform: translateY(-1px); }
.cf-meta-left { display: inline-flex; align-items: center; gap: 6px; min-width: 0; }
.cf-meta-icon { font-size: 14px; width: 14px; height: 14px; color: #FFFFFF; margin: 0; transition: color 0.25s; }
.cf-meta-box:hover .cf-meta-icon { color: var(--primary-color); }
.cf-meta-title { display: inline; font-size: 10px; color: #888; margin: 0; font-weight: bold; letter-spacing: 0.06em; white-space: nowrap; }
.cf-meta-value { font-size: 13px; font-weight: bold; color: #fff; white-space: nowrap; }

/* 5. External platforms as compact pill in the same row */
.cf-external-platforms-wrapper { width: fit-content; max-width: 100%; padding: 8px 14px; border-radius: 999px; display: inline-flex; align-items: center; gap: 10px; margin: 0; text-align: left; transition: border-color 0.25s, transform 0.25s; border: 1px solid rgba(255,255,255,0.08); }
.cf-external-platforms-wrapper:hover { border-color: rgba(255, 183, 0, 0.4); transform: translateY(-1px); }
.cf-track-ad-sidebar { margin-bottom: 40px; padding: 20px; border-radius: 12px; }
.cf-ad-slot { margin: 0 auto; max-width: 100%; text-align: center; }
.cf-ad-slot--preview { align-items: center; background: rgba(255,255,255,0.04); border: 1px dashed rgba(255,183,0,0.35); border-radius: 12px; color: rgba(255,255,255,0.55); display: flex; font-family: 'Space Mono', monospace; font-size: 13px; justify-content: center; min-height: 90px; padding: 24px; }
.cf-platforms-title { display: inline; margin: 0; font-size: 10px; color: #888; font-weight: bold; letter-spacing: 0.06em; text-transform: uppercase; white-space: nowrap; }
.cf-platforms-grid { display: flex; align-items: center; justify-content: flex-start; gap: 8px; flex-wrap: nowrap; }
.cf-platform-icon-btn { display: inline-flex; width: 28px; height: 28px; justify-content: center; align-items: center; border-radius: 50%; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.03); transition: border-color 0.25s, transform 0.25s; }
.cf-platform-icon-btn svg { width: 14px; height: 14px; fill: #FFFFFF; transition: fill 0.25s; }
.cf-platform-icon-btn--lg svg { width: 20px; height: 20px; }
.cf-platform-icon-btn:hover { border-color: var(--primary-color); transform: scale(1.1); }
.cf-platform-icon-btn:hover svg { fill: var(--primary-color); }

/* 6. Lyrics Sync tracking panel */
.cf-content-area { padding: 40px; border-radius: 12px; text-align: left; margin-bottom: 60px; }
.cf-content-area h2 { font-size: 24px; margin-top: 0; margin-bottom: 20px; color: #fff; }
.cf-entry-content { font-size: 16px; line-height: 1.8; color: #ccc; margin-bottom: 40px; }
.cf-lyrics-tracker-container { background: rgba(0,0,0,0.4); padding: 25px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); }
.cf-lyrics-tracker-container h3 { margin-top: 0; color: #fff; margin-bottom: 16px; font-size: 16px; letter-spacing: 1px; }
#cf-lyrics-sync-playlist {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 14px;
    max-height: 260px;
    overflow-y: auto;
    scroll-behavior: smooth;
    position: relative;
    padding: 20px 6px;
    -webkit-mask-image: linear-gradient(to bottom, transparent 0, #000 28px, #000 calc(100% - 28px), transparent 100%);
    mask-image: linear-gradient(to bottom, transparent 0, #000 28px, #000 calc(100% - 28px), transparent 100%);
}
#cf-lyrics-sync-playlist::-webkit-scrollbar { width: 5px; }
#cf-lyrics-sync-playlist::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 3px; }
.cf-sync-line { font-size: 15px; color: #555; opacity: 0.5; transition: color 0.3s, opacity 0.3s, filter 0.3s, transform 0.3s; margin: 0; font-family: 'Space Mono', monospace; }
.cf-sync-line.active { color: var(--primary-color); font-weight: bold; opacity: 1; filter: drop-shadow(0 0 5px var(--primary-color)); transform: scale(1.03); }
.cf-sync-line-empty { font-size: 14px; color: #777; font-style: italic; }
.cf-copyright-text { margin-top: 40px; font-size: 12px; color: #555; }

/* Custom Comments and Emoji Picker Styles */
.cf-emoji-picker-container { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; background: rgba(255,255,255,0.03); padding: 12px 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); }
.cf-emoji-label { font-size: 13px; font-weight: bold; color: #888; font-family: 'Space Mono', monospace; }
.cf-emoji-list { display: flex; gap: 8px; flex-wrap: wrap; }
.cf-emoji-btn { background: transparent; border: none; font-size: 20px; cursor: pointer; transition: transform 0.2s; padding: 0; }
.cf-emoji-btn:hover { transform: scale(1.3); }

/* Custom Comment Meta style fixes */
.cf-comment-list-item .avatar { border-radius: 50% !important; border: 1px solid var(--primary-color) !important; }

/* TABLET & MOBILE MEDIA QUERIES FOR PERFECT FLUID RESPONSIVENESS */
@media(max-width: 1024px) {
    .cf-track-header-layout {
        gap: 36px;
    }
}

@media(max-width: 768px) {
    .cf-cinematic-hero {
        padding: 88px 0 50px;
    }
    .cf-container {
        width: 100%;
        max-width: 100%;
        padding-left: 5px;
        padding-right: 5px;
    }
    .cf-track-header-layout { 
        flex-direction: column !important; 
        text-align: center !important; 
        gap: 30px !important;
        width: 100%;
        min-width: 0;
    }
    .cf-details-panel { 
        flex: 1 1 auto !important;
        text-align: center !important; 
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
        padding: 20px 16px !important;
    }
    .cf-hero-title { 
        font-size: 32px !important; 
    }
    .cf-hero-actions-row {
        justify-content: center !important;
    }
    .cf-track-stats-row {
        justify-content: center !important;
    }
    .cf-artist-profile-row {
        justify-content: center !important;
    }
    .cf-meta-grid {
        gap: 10px !important;
    }
    .cf-meta-box {
        width: 100%;
        justify-content: space-between;
    }
    .cf-external-platforms-wrapper {
        width: 100%;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    .cf-platforms-grid {
        flex-wrap: wrap;
    }
    .cf-form-row-flex {
        flex-direction: column !important;
        gap: 10px !important;
    }
    .cf-content-area {
        padding: 24px !important;
    }
}
@media (min-width: 769px) and (max-width: 1024px) {
    .cf-vinyl-inner {
        max-width: 300px;
    }
    .cf-details-panel {
        padding: 28px !important;
    }
}
</style>

<!-- Upgraded jQuery Interaction Controller Script -->
<script type="text/javascript">
/* jQuery loads in the footer; wait until it exists before binding. */
(function cfWaitForJQuery(fn) {
    if (window.jQuery) {
        jQuery(fn);
        return;
    }
    var timer = setInterval(function () {
        if (window.jQuery) {
            clearInterval(timer);
            jQuery(fn);
        }
    }, 20);
})(function($) {
    
    // --- 3. Interactive Emojis Click Handler ---
    $(document).on('click', '.cf-emoji-btn', function(e) {
        e.preventDefault();
        var emoji = $(this).data('emoji');
        var commentBox = $('#comment');
        
        if(commentBox.length) {
            var currentVal = commentBox.val();
            commentBox.val(currentVal + emoji);
            commentBox.focus(); // Keeps focus on textarea
        }
    });


    // --- 4. Advanced Audio Tracker - Synchronized Lyrics Highlighter ---
    var audio = document.getElementById('cf-native-audio-element');
    
    if (audio) {
        if (!window.__cfTrackAudioUiBound) {
            window.__cfTrackAudioUiBound = true;

            var cfLyricsBox = document.getElementById('cf-lyrics-sync-playlist');
            var cfLastActiveLine = null;

            audio.addEventListener('timeupdate', function() {
                var currentTime = audio.currentTime;
                var newActiveLine = null;

                $('.cf-sync-line').each(function() {
                    var start = parseFloat($(this).data('start'));
                    var end = parseFloat($(this).data('end'));

                    if (currentTime >= start && currentTime <= end) {
                        $(this).addClass('active');
                        newActiveLine = this;
                    } else {
                        $(this).removeClass('active');
                    }
                });

                if (newActiveLine && newActiveLine !== cfLastActiveLine && cfLyricsBox) {
                    cfLastActiveLine = newActiveLine;

                    var targetScrollTop = newActiveLine.offsetTop
                        - (cfLyricsBox.clientHeight / 2)
                        + (newActiveLine.offsetHeight / 2);

                    var maxScrollTop = cfLyricsBox.scrollHeight - cfLyricsBox.clientHeight;
                    targetScrollTop = Math.max(0, Math.min(targetScrollTop, maxScrollTop));

                    cfLyricsBox.scrollTop = targetScrollTop;
                }
            });

        }
    }

});
</script>

<script>
(function () {
    document.querySelectorAll('[data-cf-share]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var url = btn.getAttribute('data-url');
            var title = btn.getAttribute('data-title') || document.title;
            var trackId = btn.getAttribute('data-track-id');
            if (navigator.share) {
                navigator.share({ title: title, url: url }).then(function () {
                    if (trackId && window.CF_Auth && typeof window.CF_Auth.trackShare === 'function') {
                        window.CF_Auth.trackShare(trackId, 'track', 'native');
                    }
                }).catch(function () {});
            } else if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function () {
                    if (trackId && window.CF_Auth && typeof window.CF_Auth.trackShare === 'function') {
                        window.CF_Auth.trackShare(trackId, 'track', 'copy');
                    }
                    var label = btn.querySelector('span');
                    if (label) {
                        var prev = label.textContent;
                        label.textContent = 'Link copied';
                        setTimeout(function () { label.textContent = prev; }, 1600);
                    }
                }).catch(function () {});
            }
        });
    });
}());
</script>

<?php get_footer(); ?>