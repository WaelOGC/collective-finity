<?php
/**
 * Fully Upgraded Single Track Template - "Collective Finity" Custom Theme
 * Features: 5-Style Dynamic Canvas Visualizer Dropdown, Live Play/Likes Stats bar, 
 * Styled Card with Palette, Mood/Duration/Release metadata cards, Official SVGs, 
 * Synchronized Lyrics/Story, and FULLY CUSTOM COMMENTS FORM WITH EMOJI SELECTOR.
 * Responsive Update: Aspect-ratio scaled vinyl, mobile stacked forms, tablet grids.
 */

get_header();

// Increment Play Counter on page render
$track_id = get_the_ID();
$plays_count = intval( get_post_meta( $track_id, '_cf_track_plays', true ) ) ?: 0;
update_post_meta( $track_id, '_cf_track_plays', $plays_count + 1 );
$updated_plays = $plays_count + 1;

$likes_count = intval( get_post_meta( $track_id, '_cf_total_likes_count', true ) ) ?: 0;

// Fetch existing comments manually to bypass standard comments_template requirements
$track_comments = get_comments( array(
    'post_id' => $track_id,
    'status'  => 'approve',
    'order'   => 'ASC',
) );
$comments_count = count($track_comments);
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

        // Visualizer styles enabled for this track
        $visualizer_styles  = collective_finity_track_visualizer_styles();
        $enabled_visualizers = array();
        foreach ( $visualizer_styles as $style_slug => $style_label ) {
            if ( collective_finity_track_show_visualizer( get_the_ID(), $style_slug ) ) {
                $enabled_visualizers[ $style_slug ] = $style_label;
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
                
                <!-- 2. SPINNING VINYL DISC WITH SPECTRUM AUDIO VISUALIZER CANVAS (Fully responsive aspect-ratio) -->
                <div class="cf-vinyl-wrapper">
                    <div class="cf-vinyl-inner">
                        <canvas id="cf-circular-visualizer" width="360" height="360"></canvas>
                        <img src="<?php echo esc_url($cover_url); ?>" class="cf-vinyl-disc" id="cf-track-spinning-vinyl" alt="<?php the_title(); ?>">
                    </div>
                    
                    <!-- 2. Visualizer drop down selector -->
                    <?php if ( ! empty( $enabled_visualizers ) ) : ?>
                    <div class="cf-visualizer-selector-wrapper">
                        <label for="cf-visualizer-type"><?php _e('Audio Effect Style:', 'collective-finity'); ?></label>
                        <select id="cf-visualizer-type">
                            <?php
                            $is_first_visualizer = true;
                            foreach ( $enabled_visualizers as $style_slug => $style_label ) :
                                ?>
                                <option value="<?php echo esc_attr( $style_slug ); ?>" <?php selected( $is_first_visualizer ); ?>><?php echo esc_html( $style_label ); ?></option>
                                <?php
                                $is_first_visualizer = false;
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <?php endif; ?>
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
                    <span class="cf-meta-icon dashicons dashicons-heart"></span>
                    <span class="cf-meta-title"><?php _e('MOOD / VIBE', 'collective-finity'); ?></span>
                    <span class="cf-meta-value"><?php _e('Cinematic & Emotional', 'collective-finity'); ?></span>
                </div>
                <?php if ( $show_bpm ) : ?>
                <div class="cf-meta-box cf-glass-card">
                    <span class="cf-meta-icon dashicons dashicons-clock"></span>
                    <span class="cf-meta-title"><?php _e('BPM', 'collective-finity'); ?></span>
                    <span class="cf-meta-value"><?php echo $bpm ? esc_html($bpm) : __('—', 'collective-finity'); ?></span>
                </div>
                <?php endif; ?>
                <?php if ( $show_key ) : ?>
                <div class="cf-meta-box cf-glass-card">
                    <span class="cf-meta-icon dashicons dashicons-admin-customizer"></span>
                    <span class="cf-meta-title"><?php _e('KEY', 'collective-finity'); ?></span>
                    <span class="cf-meta-value"><?php echo $track_key ? esc_html($track_key) : __('—', 'collective-finity'); ?></span>
                </div>
                <?php endif; ?>
                <div class="cf-meta-box cf-glass-card">
                    <span class="cf-meta-icon dashicons dashicons-calendar-alt"></span>
                    <span class="cf-meta-title"><?php _e('RELEASE DATE', 'collective-finity'); ?></span>
                    <span class="cf-meta-value" style="color: #FFB700;"><?php echo get_the_date('M Y'); ?></span>
                </div>
            </div>

            <!-- 5. LISTEN ON EXTERNAL PLATFORMS -->
            <?php if ( ! empty( $streaming_links ) ) : ?>
            <div class="cf-external-platforms-wrapper cf-glass-card">
                <span class="cf-platforms-title"><?php _e('Listen on External Platforms', 'collective-finity'); ?></span>
                <div class="cf-platforms-grid">
                    <?php if ( ! empty( $streaming_links['spotify'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['spotify'] ); ?>" target="_blank" class="cf-platform-icon-btn" title="Spotify">
                            <svg viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm4.586 14.424c-.18.295-.417.387-.714.207-2.39-1.46-5.4-1.79-8.946-.983-.34.078-.615-.187-.69-.47-.075-.333.15-.658.463-.733 3.882-.888 7.21-.5 9.873 1.13.292.176.31.554.114.85zm1.224-2.724c-.226.367-.626.49-.982.262-2.735-1.68-6.904-2.17-10.128-1.192-.41.124-.836-.14-.954-.537-.118-.396.11-.844.516-.96 3.694-1.12 8.283-.573 11.414 2.247.332.22.427.674.134 1.18zm.107-2.836C14.502 8.78 8.04 8.567 4.3 9.702c-.575.174-1.18-.184-1.353-.75-.173-.565.155-1.196.732-1.37 4.3-1.304 11.436-1.047 15.485 1.36.518.516 0 1 .37 1.35.37.28.84.45 1.45.45.54 0 1-.22 1.35-.45.37-.35.37-.84.37-1.35z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $streaming_links['apple'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['apple'] ); ?>" target="_blank" class="cf-platform-icon-btn" title="Apple Music">
                            <svg viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm4.333 13.914c-.39.26-.816.388-1.258.388-.34 0-.676-.08-1.008-.242l-2.067-1.034v.538c0 .858-.458 1.636-1.198 2.032-.39.21-.817.314-1.242.314-.492 0-.974-.14-1.4-.412L6.11 16.146c-.732-.472-1.144-1.272-1.144-2.138v-3.076c0-.858.458-1.636 1.198-2.032l2.054-1.11c.39-.21.817-.314 1.242-.314.492 0 .974.14 1.4.412l2.054 1.358c.732.472 1.144 1.272 1.144 2.138v.538l2.067-1.034c.332-.162.668-.242 1.008-.242.442 0 .868.128 1.258.388.756.504 1.167 1.358 1.167 2.29v1.238c0 .932-.411 1.786-1.167 2.29z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $streaming_links['soundcloud'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['soundcloud'] ); ?>" target="_blank" class="cf-platform-icon-btn" title="Soundcloud">
                            <svg viewBox="0 0 24 24"><path d="M11.56 16.5c0-.18.01-.36.03-.53l.03-.23c-1.39-.12-2.5-1.14-2.5-2.43 0-1.14.87-2.07 2.05-2.28-.01-.13-.02-.27-.02-.4 0-1.74 1.75-3.15 3.91-3.15 1.5 0 2.8 1 3.48 2.45.39-.14.8-.23 1.24-.23 1.95 0 3.53 1.41 3.53 3.15 0 1.95-1.58 3.53-3.53 3.53h-8.2zm-1.04-4.8c-.37-.5-1.02-.73-1.62-.57-.4.1-.73.38-.9.77-.1.25-.13.52-.09.78.1.53.52.92 1.06.94l1.55.08V11.7z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $streaming_links['youtube'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['youtube'] ); ?>" target="_blank" class="cf-platform-icon-btn" title="YouTube">
                            <svg viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 0 0-2.11-2.11C19.517 3.545 12 3.545 12 3.545s0 0 0 0h-.002s-7.517 0-9.388.507a3.003 3.003 0 0 0-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 0 0 2.11 2.11c1.871.507 9.388.507 9.388.507s7.517 0 9.388-.507a3.003 3.003 0 0 0 2.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $streaming_links['bandcamp'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['bandcamp'] ); ?>" target="_blank" class="cf-platform-icon-btn" title="Bandcamp">
                            <svg viewBox="0 0 24 24"><path d="M22 6H12l-2.5 4H2v8h20V6z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $streaming_links['amazon'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['amazon'] ); ?>" target="_blank" class="cf-platform-icon-btn" title="Amazon Music">
                            <svg viewBox="0 0 24 24"><path d="M.045 18.02c.072-.116.187-.124.348-.022 3.636 2.11 7.594 3.166 11.87 3.166 2.852 0 5.668-.533 8.447-1.595l.315-.14c.138-.06.234-.1.293-.13.226-.088.39-.046.525.13.12.172.09.336-.12.48-.256.19-.76.385-1.51.585-.797.252-1.597.504-2.402.754-3.158 1.006-6.626 1.51-10.406 1.51-4.324 0-8.162-.734-11.52-2.203-.176-.072-.296-.16-.36-.256-.1-.133-.076-.27.073-.41z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $streaming_links['google_play'] ) ) : ?>
                        <a href="<?php echo esc_url( $streaming_links['google_play'] ); ?>" target="_blank" class="cf-platform-icon-btn" title="Google Play Music">
                            <svg viewBox="0 0 24 24"><path d="M3.61 1.81A1.5 1.5 0 0 0 1.5 3.18v17.64a1.5 1.5 0 0 0 2.11 1.37l15.84-8.82a1.5 1.5 0 0 0 0-2.64L3.61 1.81zm1.39 3.4 10.38 5.79L5 16.79V5.21z"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

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
                <div class="cf-lyrics-tracker-container" style="margin-bottom: 40px;">
                    <h3><?php _e('Lyrics / Narrative Sync', 'collective-finity'); ?></h3>
                    <div id="cf-lyrics-sync-playlist">
                        <p class="cf-sync-line" data-start="0" data-end="10">✦ (Ambient introduction - cinematic buildup) ✦</p>
                        <p class="cf-sync-line" data-start="11" data-end="25">"We craft digital experiences that matter..."</p>
                        <p class="cf-sync-line" data-start="26" data-end="45">"On your land, under the warm, breathing sky..."</p>
                        <p class="cf-sync-line" data-start="46" data-end="68">"Land of Light... where humanity, memory, and hope are born."</p>
                        <p class="cf-sync-line" data-start="69" data-end="120">✦ (Atmospheric soundscape & synthesizers climax) ✦</p>
                    </div>
                </div>
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

/* 2. Visualizer Canvas styling (Upgraded for fluid aspect-ratio mobile responsiveness) */
.cf-vinyl-wrapper { flex: 1 1 250px; min-width: 0; max-width: 100%; display: flex; flex-direction: column; align-items: center; position: relative; }
.cf-vinyl-inner { position: relative; width: 100%; max-width: 340px; aspect-ratio: 1 / 1; display: flex; justify-content: center; align-items: center; margin-bottom: 20px; }
#cf-circular-visualizer { position: absolute; top: 0; left: 0; width: 100% !important; height: 100% !important; max-width: 360px; max-height: 360px; z-index: 1; pointer-events: none; }
.cf-vinyl-disc { width: 82% !important; height: 82% !important; max-width: 280px; max-height: 280px; border-radius: 50%; border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 0 25px rgba(0,0,0,0.8); object-fit: cover; z-index: 2; transition: transform 0.2s, box-shadow 0.2s; }
.cf-vinyl-disc.playing { animation: spin 15s linear infinite; }
.cf-visualizer-selector-wrapper { text-align: center; width: 100%; max-width: 320px; margin-top: 10px; }
.cf-visualizer-selector-wrapper label { display: block; font-size: 11px; font-weight: bold; margin-bottom: 8px; color: #888; letter-spacing: 0.08em; text-transform: uppercase; }
#cf-visualizer-type { width: 100%; background: rgba(26,26,26,0.9); border: 1px solid rgba(255,255,255,0.12); color: #fff; padding: 10px 12px; border-radius: 10px; font-size: 13px; cursor: pointer; }
#cf-visualizer-type:focus { border-color: var(--primary-color); outline: none; }

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

/* 4. Upgraded Metadata Cards */
.cf-meta-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 40px; }
.cf-meta-box { padding: 25px; border-radius: 12px; text-align: center; display: flex; flex-direction: column; align-items: center; transition: border-color 0.25s, transform 0.25s; border: 1px solid rgba(255,255,255,0.05); }
.cf-meta-box:hover { border-color: rgba(255, 183, 0, 0.4); transform: translateY(-2px); }
.cf-meta-icon { font-size: 24px; color: #FFFFFF; margin-bottom: 12px; transition: color 0.25s; }
.cf-meta-box:hover .cf-meta-icon { color: var(--primary-color); }
.cf-meta-title { display: block; font-size: 11px; color: #888; margin-bottom: 8px; font-weight: bold; letter-spacing: 1px; }
.cf-meta-value { font-size: 18px; font-weight: bold; color: #fff; }

/* 5. External Platforms Grid with Styled Icons */
.cf-external-platforms-wrapper { padding: 30px; border-radius: 12px; text-align: center; margin-bottom: 40px; }
.cf-track-ad-sidebar { margin-bottom: 40px; padding: 20px; border-radius: 12px; }
.cf-ad-slot { margin: 0 auto; max-width: 100%; text-align: center; }
.cf-ad-slot--preview { align-items: center; background: rgba(255,255,255,0.04); border: 1px dashed rgba(255,183,0,0.35); border-radius: 12px; color: rgba(255,255,255,0.55); display: flex; font-family: 'Space Mono', monospace; font-size: 13px; justify-content: center; min-height: 90px; padding: 24px; }
.cf-platforms-title { display: block; margin-bottom: 25px; font-size: 18px; color: #fff; font-weight: bold; }
.cf-platforms-grid { display: flex; justify-content: center; gap: 25px; flex-wrap: wrap; }
.cf-platform-icon-btn { display: inline-flex; width: 44px; height: 44px; justify-content: center; align-items: center; border-radius: 50%; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.03); transition: border-color 0.25s, transform 0.25s; }
.cf-platform-icon-btn svg { width: 22px; height: 22px; fill: #FFFFFF; transition: fill 0.25s; }
.cf-platform-icon-btn:hover { border-color: var(--primary-color); transform: scale(1.1); }
.cf-platform-icon-btn:hover svg { fill: var(--primary-color); }

/* 6. Lyrics Sync tracking panel */
.cf-content-area { padding: 40px; border-radius: 12px; text-align: left; margin-bottom: 60px; }
.cf-content-area h2 { font-size: 24px; margin-top: 0; margin-bottom: 20px; color: #fff; }
.cf-entry-content { font-size: 16px; line-height: 1.8; color: #ccc; margin-bottom: 40px; }
.cf-lyrics-tracker-container { background: rgba(0,0,0,0.4); padding: 25px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); }
.cf-lyrics-tracker-container h3 { margin-top: 0; color: #fff; margin-bottom: 20px; font-size: 16px; letter-spacing: 1px; }
#cf-lyrics-sync-playlist { display: flex; flex-direction: column; gap: 15px; }
.cf-sync-line { font-size: 15px; color: #555; transition: color 0.3s, filter 0.3s; margin: 0; font-family: 'Space Mono', monospace; }
.cf-sync-line.active { color: var(--primary-color); font-weight: bold; filter: drop-shadow(0 0 5px var(--primary-color)); }
.cf-copyright-text { margin-top: 40px; font-size: 12px; color: #555; }

/* Custom Comments and Emoji Picker Styles */
.cf-emoji-picker-container { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; background: rgba(255,255,255,0.03); padding: 12px 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); }
.cf-emoji-label { font-size: 13px; font-weight: bold; color: #888; font-family: 'Space Mono', monospace; }
.cf-emoji-list { display: flex; gap: 8px; flex-wrap: wrap; }
.cf-emoji-btn { background: transparent; border: none; font-size: 20px; cursor: pointer; transition: transform 0.2s; padding: 0; }
.cf-emoji-btn:hover { transform: scale(1.3); }

/* Custom Comment Meta style fixes */
.cf-comment-list-item .avatar { border-radius: 50% !important; border: 1px solid var(--primary-color) !important; }

@keyframes spin { 100% { transform: rotate(360deg); } }

/* TABLET & MOBILE MEDIA QUERIES FOR PERFECT FLUID RESPONSIVENESS */
@media(max-width: 1024px) {
    .cf-meta-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
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
        grid-template-columns: 1fr !important; /* Stacks cards cleanly on smaller phones */
        gap: 15px !important;
    }
    .cf-form-row-flex {
        flex-direction: column !important;
        gap: 10px !important;
    }
    .cf-content-area {
        padding: 24px !important;
    }
    .cf-external-platforms-wrapper {
        padding: 20px !important;
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

            audio.addEventListener('timeupdate', function() {
                var currentTime = audio.currentTime;
                
                $('.cf-sync-line').each(function() {
                    var start = parseFloat($(this).data('start'));
                    var end = parseFloat($(this).data('end'));
                    
                    if (currentTime >= start && currentTime <= end) {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });
            });

            audio.addEventListener('play', function() {
                var vinyl = document.getElementById('cf-track-spinning-vinyl');
                if (vinyl) {
                    vinyl.classList.add('playing');
                }
            });
            audio.addEventListener('pause', function() {
                var vinyl = document.getElementById('cf-track-spinning-vinyl');
                if (vinyl) {
                    vinyl.classList.remove('playing');
                }
            });
        }

        var vinylEl = document.getElementById('cf-track-spinning-vinyl');
        if (vinylEl) {
            vinylEl.classList.toggle('playing', !audio.paused && !!audio.src);
        }
    }


    // --- 5. Web Audio API circular visualizer (12 rotating multi-color styles) ---
    var audioContext;
    var analyser;
    var source;
    var canvas = document.getElementById('cf-circular-visualizer');
    var ctx = canvas ? canvas.getContext('2d') : null;
    var bufferLength;
    var dataArray;

    // Brand gold first, then bright saturated accents (never gray).
    var CF_VIZ_PALETTE = [
        [255, 183, 0],   // #FFB700 brand gold
        [255, 59, 48],   // bright red
        [191, 90, 242],  // bright purple
        [10, 132, 255],  // bright blue
        [255, 149, 0]    // bright orange
    ];
    var colorPhase = 0;
    var vizEmbers = [];
    var vizSmoke = [];
    var vizDrips = [];
    var vizCracks = null;
    var vizFrost = null;
    var vizShards = null;
    var radarAngle = 0;
    var breathePhase = 0;

    function vizEnergy(from, to) {
        var sum = 0;
        var n = 0;
        var end = Math.min(to, bufferLength);
        for (var i = from; i < end; i++) {
            sum += dataArray[i];
            n++;
        }
        return n ? sum / (n * 255) : 0;
    }

    function vizUpdateColorPhase() {
        var full = vizEnergy(0, bufferLength);
        var bass = vizEnergy(0, 10);
        colorPhase += 0.002 + full * 0.008 + bass * 0.006;
    }

    function vizRgbAtAngle(angleRad) {
        var t = (angleRad / (Math.PI * 2)) + colorPhase;
        t = t - Math.floor(t);
        var scaled = t * CF_VIZ_PALETTE.length;
        var i0 = Math.floor(scaled) % CF_VIZ_PALETTE.length;
        var i1 = (i0 + 1) % CF_VIZ_PALETTE.length;
        var f = scaled - Math.floor(scaled);
        f = f * f * (3 - 2 * f);
        var c0 = CF_VIZ_PALETTE[i0];
        var c1 = CF_VIZ_PALETTE[i1];
        return [
            Math.round(c0[0] + (c1[0] - c0[0]) * f),
            Math.round(c0[1] + (c1[1] - c0[1]) * f),
            Math.round(c0[2] + (c1[2] - c0[2]) * f)
        ];
    }

    function vizColorAtAngle(angleRad, alpha) {
        var rgb = vizRgbAtAngle(angleRad);
        var a = (typeof alpha === 'number') ? alpha : 1;
        return 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + a + ')';
    }

    function vizSeedCracks(radius) {
        var cracks = [];
        for (var i = 0; i < 14; i++) {
            var base = (Math.PI * 2 / 14) * i + (Math.random() - 0.5) * 0.25;
            var segs = [];
            var ang = base;
            var dist = radius + 2;
            segs.push({ a: ang, r: dist });
            var steps = 3 + Math.floor(Math.random() * 3);
            for (var s = 0; s < steps; s++) {
                ang += (Math.random() - 0.5) * 0.55;
                dist += 8 + Math.random() * 14;
                segs.push({ a: ang, r: dist });
            }
            cracks.push(segs);
        }
        return cracks;
    }

    function vizSeedFrost(radius) {
        var veins = [];
        for (var i = 0; i < 18; i++) {
            var base = (Math.PI * 2 / 18) * i + (Math.random() - 0.5) * 0.2;
            var branches = [];
            var main = [];
            var ang = base;
            var dist = radius + 1;
            main.push({ a: ang, r: dist });
            var len = 4 + Math.floor(Math.random() * 3);
            for (var s = 0; s < len; s++) {
                ang += (Math.random() - 0.5) * 0.35;
                dist += 6 + Math.random() * 10;
                main.push({ a: ang, r: dist });
                if (s > 0 && Math.random() > 0.55) {
                    var ba = ang + (Math.random() > 0.5 ? 0.4 : -0.4);
                    var br = dist + 4 + Math.random() * 8;
                    branches.push([
                        { a: ang, r: dist },
                        { a: ba, r: br }
                    ]);
                }
            }
            veins.push({ main: main, branches: branches });
        }
        return veins;
    }

    function vizSeedShards(radius) {
        var shards = [];
        for (var i = 0; i < 20; i++) {
            var a = (Math.PI * 2 / 20) * i + (Math.random() - 0.5) * 0.15;
            shards.push({
                a: a,
                spread: 0.06 + Math.random() * 0.08,
                baseLen: 10 + Math.random() * 16,
                tipBias: (Math.random() - 0.5) * 0.08
            });
        }
        return shards;
    }

    function vizApplyRadialFade(centerX, centerY, canvasW, canvasH) {
        var fadeRadius = Math.min(canvasW, canvasH) * 0.5;
        var fadeGrad = ctx.createRadialGradient(centerX, centerY, fadeRadius * 0.3, centerX, centerY, fadeRadius);
        fadeGrad.addColorStop(0, 'rgba(255,255,255,1)');
        fadeGrad.addColorStop(0.9, 'rgba(255,255,255,1)');
        fadeGrad.addColorStop(1, 'rgba(255,255,255,0)');
        ctx.globalCompositeOperation = 'destination-in';
        ctx.fillStyle = fadeGrad;
        ctx.fillRect(0, 0, canvasW, canvasH);
        ctx.globalCompositeOperation = 'source-over';
    }

    function initVisualizer() {
        if (!audio) {
            return;
        }

        // Reuse existing graph if MediaElementSource was already created for this element.
        if (audio.__cfVizMediaSourceConnected) {
            var existing = audio.__cfVizGraph;
            if (existing) {
                audioContext = existing.audioContext;
                analyser = existing.analyser;
                source = existing.source;
                bufferLength = existing.bufferLength;
                dataArray = existing.dataArray;
                if (audioContext && audioContext.state === 'suspended') {
                    audioContext.resume();
                }
                if (!vizDrawLoopStarted) {
                    vizDrawLoopStarted = true;
                    drawVisualizer();
                }
            }
            return;
        }

        try {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            analyser = audioContext.createAnalyser();

            source = audioContext.createMediaElementSource(audio);
            source.connect(analyser);
            analyser.connect(audioContext.destination);

            analyser.fftSize = 256;
            bufferLength = analyser.frequencyBinCount;
            dataArray = new Uint8Array(bufferLength);

            audio.__cfVizMediaSourceConnected = true;
            audio.__cfVizGraph = {
                audioContext: audioContext,
                analyser: analyser,
                source: source,
                bufferLength: bufferLength,
                dataArray: dataArray
            };
            window.cfAudioMediaSourceConnected = true;

            if (audioContext.state === 'suspended') {
                audioContext.resume();
            }
            if (!vizDrawLoopStarted) {
                vizDrawLoopStarted = true;
                drawVisualizer();
            }
        } catch(e) {
            // Web Audio API unavailable or MediaElementSource already connected elsewhere.
        }
    }

    var vizDrawLoopStarted = false;

    function drawVisualizer() {
        requestAnimationFrame(drawVisualizer);
        if (!analyser) return;

        if (!canvas || !document.body.contains(canvas)) {
            canvas = document.getElementById('cf-circular-visualizer');
            if (!canvas) {
                return;
            }
            ctx = canvas.getContext('2d');
        }

        analyser.getByteFrequencyData(dataArray);

        if (!canvas || !ctx) {
            return;
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        var centerX = canvas.width / 2;
        var centerY = canvas.height / 2;
        var radius = 142;
        var selectedStyle = $('#cf-visualizer-type').val();
        var bass = vizEnergy(0, 10);
        var mid = vizEnergy(10, 40);
        var full = vizEnergy(0, bufferLength);

        vizUpdateColorPhase();

        if (selectedStyle === 'spectrum_bars') {
            var bars = 64;
            var barWidth = (Math.PI * 2 * radius) / bars * 0.55;
            for (var i = 0; i < bars; i++) {
                var val = dataArray[i % bufferLength];
                var barHeight = 4 + (val / 255) * 28;
                var rads = (Math.PI * 2 / bars) * i;
                var cos = Math.cos(rads);
                var sin = Math.sin(rads);
                var x0 = centerX + cos * radius;
                var y0 = centerY + sin * radius;
                var x1 = centerX + cos * (radius + barHeight);
                var y1 = centerY + sin * (radius + barHeight);
                var inten = 0.45 + (val / 255) * 0.55;
                ctx.strokeStyle = vizColorAtAngle(rads, inten);
                ctx.lineWidth = Math.max(2, barWidth * 0.35);
                ctx.lineCap = 'round';
                ctx.shadowBlur = 6 + (val / 255) * 10;
                ctx.shadowColor = vizColorAtAngle(rads, 0.7);
                ctx.beginPath();
                ctx.moveTo(x0, y0);
                ctx.lineTo(x1, y1);
                ctx.stroke();
            }
            ctx.shadowBlur = 0;
            ctx.lineCap = 'butt';
        }
        else if (selectedStyle === 'aurora_fill') {
            var haloR = radius + 55 + bass * 25 + mid * 10;
            var steps = 48;
            for (var ai = 0; ai < steps; ai++) {
                var a0 = (Math.PI * 2 / steps) * ai;
                var a1 = (Math.PI * 2 / steps) * (ai + 1.15);
                var rgb = vizRgbAtAngle(a0);
                var alpha = 0.12 + bass * 0.35 + full * 0.15;
                var grad = ctx.createRadialGradient(centerX, centerY, radius * 0.85, centerX, centerY, haloR);
                grad.addColorStop(0, 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',0)');
                grad.addColorStop(0.45, 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + (alpha * 0.35) + ')');
                grad.addColorStop(0.75, 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + alpha + ')');
                grad.addColorStop(1, 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',0)');
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, haloR, a0, a1);
                ctx.closePath();
                ctx.fillStyle = grad;
                ctx.fill();
            }
        }
        else if (selectedStyle === 'ember_drift') {
            var spawnRate = 0.35 + full * 2.5 + bass * 1.5;
            for (var e = 0; e < spawnRate; e++) {
                if (vizEmbers.length > 80) break;
                var ea = Math.random() * Math.PI * 2;
                var er = radius + 4 + Math.random() * 18;
                vizEmbers.push({
                    a: ea,
                    r: er,
                    life: 1,
                    decay: 0.008 + Math.random() * 0.012,
                    drift: (Math.random() - 0.5) * 0.02,
                    rise: 0.35 + Math.random() * 0.55,
                    size: 1.2 + Math.random() * 2.2
                });
            }
            for (var ei = vizEmbers.length - 1; ei >= 0; ei--) {
                var em = vizEmbers[ei];
                em.a += em.drift + (full - 0.5) * 0.01;
                em.r += em.rise * (0.6 + bass * 1.4);
                em.life -= em.decay * (0.7 + full);
                if (em.life <= 0 || em.r > radius + 90) {
                    vizEmbers.splice(ei, 1);
                    continue;
                }
                var ex = centerX + Math.cos(em.a) * em.r;
                var ey = centerY + Math.sin(em.a) * em.r;
                ctx.beginPath();
                ctx.arc(ex, ey, em.size * em.life, 0, Math.PI * 2);
                ctx.fillStyle = vizColorAtAngle(em.a, 0.35 + em.life * 0.65);
                ctx.shadowBlur = 8 + em.life * 10;
                ctx.shadowColor = vizColorAtAngle(em.a, 0.8);
                ctx.fill();
            }
            ctx.shadowBlur = 0;
        }
        else if (selectedStyle === 'crimson_pulse_ring') {
            var pulse = 1 + bass * 0.18 + mid * 0.06;
            var ringR = radius + 6 + bass * 14;
            var segs = 72;
            ctx.lineWidth = 1.5 + bass * 2.5;
            ctx.lineCap = 'round';
            for (var pi = 0; pi < segs; pi++) {
                var pa0 = (Math.PI * 2 / segs) * pi;
                var pa1 = (Math.PI * 2 / segs) * (pi + 1);
                var pr = ringR * pulse;
                ctx.beginPath();
                ctx.arc(centerX, centerY, pr, pa0, pa1);
                ctx.strokeStyle = vizColorAtAngle(pa0, 0.55 + bass * 0.45);
                ctx.shadowBlur = 8 + bass * 22;
                ctx.shadowColor = vizColorAtAngle(pa0, 0.85);
                ctx.stroke();
            }
            ctx.shadowBlur = 0;
            ctx.lineCap = 'butt';
        }
        else if (selectedStyle === 'smoke_wisp') {
            var smokeSpawn = 0.2 + full * 1.4;
            for (var ss = 0; ss < smokeSpawn; ss++) {
                if (vizSmoke.length > 36) break;
                vizSmoke.push({
                    a: Math.random() * Math.PI * 2,
                    r: radius + 2 + Math.random() * 8,
                    life: 1,
                    decay: 0.006 + Math.random() * 0.008,
                    swirl: (Math.random() > 0.5 ? 1 : -1) * (0.012 + Math.random() * 0.02),
                    widen: 0.2 + Math.random() * 0.35,
                    phase: Math.random() * Math.PI * 2
                });
            }
            for (var si = vizSmoke.length - 1; si >= 0; si--) {
                var sm = vizSmoke[si];
                sm.phase += 0.08 + mid * 0.12;
                sm.a += sm.swirl * (0.7 + full);
                sm.r += 0.45 + bass * 0.9;
                sm.life -= sm.decay * (0.8 + full * 0.6);
                if (sm.life <= 0 || sm.r > radius + 85) {
                    vizSmoke.splice(si, 1);
                    continue;
                }
                var ribbon = 10 + sm.widen * 40 * (1 - sm.life * 0.3);
                ctx.beginPath();
                for (var t = 0; t <= 8; t++) {
                    var tt = t / 8;
                    var ra = sm.a + Math.sin(sm.phase + tt * 2.5) * 0.35 * tt;
                    var rr = sm.r + tt * ribbon * 0.35;
                    var sx = centerX + Math.cos(ra) * rr;
                    var sy = centerY + Math.sin(ra) * rr;
                    if (t === 0) ctx.moveTo(sx, sy);
                    else ctx.lineTo(sx, sy);
                }
                ctx.strokeStyle = vizColorAtAngle(sm.a, 0.08 + sm.life * 0.28);
                ctx.lineWidth = 6 + (1 - sm.life) * 10;
                ctx.lineCap = 'round';
                ctx.shadowBlur = 14;
                ctx.shadowColor = vizColorAtAngle(sm.a, 0.35);
                ctx.stroke();
            }
            ctx.shadowBlur = 0;
            ctx.lineCap = 'butt';
        }
        else if (selectedStyle === 'shard_fracture') {
            if (!vizShards) vizShards = vizSeedShards(radius);
            for (var sh = 0; sh < vizShards.length; sh++) {
                var shard = vizShards[sh];
                var sval = dataArray[sh % bufferLength] / 255;
                var len = shard.baseLen * (0.55 + sval * 1.35 + bass * 0.5);
                var aL = shard.a - shard.spread;
                var aR = shard.a + shard.spread;
                var aT = shard.a + shard.tipBias;
                var xL = centerX + Math.cos(aL) * radius;
                var yL = centerY + Math.sin(aL) * radius;
                var xR = centerX + Math.cos(aR) * radius;
                var yR = centerY + Math.sin(aR) * radius;
                var xT = centerX + Math.cos(aT) * (radius + len);
                var yT = centerY + Math.sin(aT) * (radius + len);
                ctx.beginPath();
                ctx.moveTo(xL, yL);
                ctx.lineTo(xT, yT);
                ctx.lineTo(xR, yR);
                ctx.closePath();
                ctx.fillStyle = vizColorAtAngle(shard.a, 0.25 + sval * 0.55);
                ctx.strokeStyle = vizColorAtAngle(shard.a, 0.55 + sval * 0.45);
                ctx.lineWidth = 1;
                ctx.shadowBlur = 4 + sval * 12;
                ctx.shadowColor = vizColorAtAngle(shard.a, 0.7);
                ctx.fill();
                ctx.stroke();
            }
            ctx.shadowBlur = 0;
        }
        else if (selectedStyle === 'radar_sweep') {
            radarAngle += 0.025 + full * 0.08 + bass * 0.06;
            var sweepLen = Math.PI * 0.55;
            var trailSteps = 40;
            for (var ri = 0; ri < trailSteps; ri++) {
                var frac = ri / trailSteps;
                var ra0 = radarAngle - sweepLen * (1 - frac);
                var ra1 = radarAngle - sweepLen * (1 - (ri + 1) / trailSteps);
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius + 38 + bass * 12, ra0, ra1);
                ctx.closePath();
                ctx.fillStyle = vizColorAtAngle(ra0, 0.02 + frac * 0.22 * (0.4 + full));
                ctx.fill();
            }
            ctx.beginPath();
            ctx.moveTo(centerX + Math.cos(radarAngle) * (radius - 4), centerY + Math.sin(radarAngle) * (radius - 4));
            ctx.lineTo(centerX + Math.cos(radarAngle) * (radius + 42 + bass * 14), centerY + Math.sin(radarAngle) * (radius + 42 + bass * 14));
            ctx.strokeStyle = vizColorAtAngle(radarAngle, 0.85 + bass * 0.15);
            ctx.lineWidth = 2;
            ctx.shadowBlur = 12 + bass * 16;
            ctx.shadowColor = vizColorAtAngle(radarAngle, 0.9);
            ctx.stroke();
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius + 2, 0, Math.PI * 2);
            ctx.strokeStyle = vizColorAtAngle(radarAngle, 0.2 + mid * 0.25);
            ctx.lineWidth = 1;
            ctx.shadowBlur = 0;
            ctx.stroke();
        }
        else if (selectedStyle === 'ink_bleed') {
            var bleedR = radius + 20 + bass * 40 + mid * 15;
            var inkSteps = 36;
            for (var ii = 0; ii < inkSteps; ii++) {
                var ia = (Math.PI * 2 / inkSteps) * ii;
                var wobble = 1 + Math.sin(ia * 3 + colorPhase * 4) * 0.08 * (0.5 + full);
                var ir = bleedR * wobble;
                var rgb = vizRgbAtAngle(ia);
                var alpha = 0.18 + bass * 0.4;
                var igrad = ctx.createRadialGradient(
                    centerX + Math.cos(ia) * radius,
                    centerY + Math.sin(ia) * radius,
                    2,
                    centerX + Math.cos(ia) * radius,
                    centerY + Math.sin(ia) * radius,
                    ir - radius + 8
                );
                igrad.addColorStop(0, 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + alpha + ')');
                igrad.addColorStop(0.55, 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + (alpha * 0.35) + ')');
                igrad.addColorStop(1, 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',0)');
                ctx.beginPath();
                ctx.arc(
                    centerX + Math.cos(ia) * (radius + 4),
                    centerY + Math.sin(ia) * (radius + 4),
                    (ir - radius) * 0.9,
                    0,
                    Math.PI * 2
                );
                ctx.fillStyle = igrad;
                ctx.fill();
            }
        }
        else if (selectedStyle === 'frost_veins') {
            if (!vizFrost) vizFrost = vizSeedFrost(radius);
            for (var fi = 0; fi < vizFrost.length; fi++) {
                var vein = vizFrost[fi];
                var fval = dataArray[fi % bufferLength] / 255;
                var fAlpha = 0.25 + fval * 0.55 + bass * 0.2;
                var baseA = vein.main[0].a;
                ctx.beginPath();
                for (var fm = 0; fm < vein.main.length; fm++) {
                    var mp = vein.main[fm];
                    var mx = centerX + Math.cos(mp.a) * mp.r;
                    var my = centerY + Math.sin(mp.a) * mp.r;
                    if (fm === 0) ctx.moveTo(mx, my);
                    else ctx.lineTo(mx, my);
                }
                ctx.strokeStyle = vizColorAtAngle(baseA, fAlpha);
                ctx.lineWidth = 1 + fval * 1.5;
                ctx.shadowBlur = 3 + fval * 8;
                ctx.shadowColor = vizColorAtAngle(baseA, 0.7);
                ctx.stroke();
                for (var fb = 0; fb < vein.branches.length; fb++) {
                    var br = vein.branches[fb];
                    ctx.beginPath();
                    ctx.moveTo(centerX + Math.cos(br[0].a) * br[0].r, centerY + Math.sin(br[0].a) * br[0].r);
                    ctx.lineTo(centerX + Math.cos(br[1].a) * br[1].r, centerY + Math.sin(br[1].a) * br[1].r);
                    ctx.strokeStyle = vizColorAtAngle(br[0].a, fAlpha * 0.75);
                    ctx.lineWidth = 0.8;
                    ctx.stroke();
                }
            }
            ctx.shadowBlur = 0;
        }
        else if (selectedStyle === 'blood_drip_trails') {
            var dripSpawn = 0.15 + bass * 1.8 + full * 0.6;
            for (var ds = 0; ds < dripSpawn; ds++) {
                if (vizDrips.length > 50) break;
                var da = Math.random() * Math.PI * 2;
                // Irregular clusters: bias some angles
                if (Math.random() > 0.7) da = Math.floor(Math.random() * 8) * (Math.PI / 4) + (Math.random() - 0.5) * 0.3;
                vizDrips.push({
                    a: da,
                    r: radius + 48 + Math.random() * 22,
                    target: radius + 2 + Math.random() * 6,
                    speed: 0.4 + Math.random() * 0.9,
                    life: 1,
                    thickness: 1 + Math.random() * 1.8,
                    wobble: (Math.random() - 0.5) * 0.03,
                    trail: []
                });
            }
            for (var di = vizDrips.length - 1; di >= 0; di--) {
                var dr = vizDrips[di];
                dr.a += dr.wobble * (0.5 + mid);
                dr.r -= dr.speed * (0.7 + bass * 1.5 + full * 0.5);
                dr.trail.push({ a: dr.a, r: dr.r });
                if (dr.trail.length > 12) dr.trail.shift();
                if (dr.r <= dr.target) {
                    dr.life -= 0.04;
                }
                if (dr.life <= 0) {
                    vizDrips.splice(di, 1);
                    continue;
                }
                ctx.beginPath();
                for (var dt = 0; dt < dr.trail.length; dt++) {
                    var tp = dr.trail[dt];
                    var tx = centerX + Math.cos(tp.a) * tp.r;
                    var ty = centerY + Math.sin(tp.a) * tp.r;
                    if (dt === 0) ctx.moveTo(tx, ty);
                    else ctx.lineTo(tx, ty);
                }
                ctx.strokeStyle = vizColorAtAngle(dr.a, 0.35 + dr.life * 0.55);
                ctx.lineWidth = dr.thickness;
                ctx.lineCap = 'round';
                ctx.shadowBlur = 5;
                ctx.shadowColor = vizColorAtAngle(dr.a, 0.6);
                ctx.stroke();
                ctx.beginPath();
                ctx.arc(centerX + Math.cos(dr.a) * dr.r, centerY + Math.sin(dr.a) * dr.r, dr.thickness * 0.9, 0, Math.PI * 2);
                ctx.fillStyle = vizColorAtAngle(dr.a, 0.5 + dr.life * 0.5);
                ctx.fill();
            }
            ctx.shadowBlur = 0;
            ctx.lineCap = 'butt';
        }
        else if (selectedStyle === 'halo_breathe') {
            breathePhase += 0.02 + full * 0.04;
            var breath = 0.5 + 0.5 * Math.sin(breathePhase);
            var breathBoost = breath * (0.65 + bass * 0.7 + mid * 0.25);
            var haloInner = radius + 2;
            var haloOuter = radius + 28 + breathBoost * 42;
            var hSteps = 40;
            for (var hi = 0; hi < hSteps; hi++) {
                var ha0 = (Math.PI * 2 / hSteps) * hi;
                var ha1 = (Math.PI * 2 / hSteps) * (hi + 1.2);
                var hrgb = vizRgbAtAngle(ha0);
                var hAlpha = 0.1 + breathBoost * 0.35;
                var hgrad = ctx.createRadialGradient(centerX, centerY, haloInner, centerX, centerY, haloOuter);
                hgrad.addColorStop(0, 'rgba(' + hrgb[0] + ',' + hrgb[1] + ',' + hrgb[2] + ',0)');
                hgrad.addColorStop(0.55, 'rgba(' + hrgb[0] + ',' + hrgb[1] + ',' + hrgb[2] + ',' + (hAlpha * 0.45) + ')');
                hgrad.addColorStop(0.85, 'rgba(' + hrgb[0] + ',' + hrgb[1] + ',' + hrgb[2] + ',' + hAlpha + ')');
                hgrad.addColorStop(1, 'rgba(' + hrgb[0] + ',' + hrgb[1] + ',' + hrgb[2] + ',0)');
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, haloOuter, ha0, ha1);
                ctx.closePath();
                ctx.fillStyle = hgrad;
                ctx.fill();
            }
        }
        else if (selectedStyle === 'fracture_cracks') {
            if (!vizCracks) vizCracks = vizSeedCracks(radius);
            var intensity = 0.2 + bass * 0.8 + mid * 0.25;
            for (var ci = 0; ci < vizCracks.length; ci++) {
                var crack = vizCracks[ci];
                var cval = dataArray[ci % bufferLength] / 255;
                var cAlpha = intensity * (0.35 + cval * 0.65);
                ctx.beginPath();
                for (var cs = 0; cs < crack.length; cs++) {
                    var cp = crack[cs];
                    var cx = centerX + Math.cos(cp.a) * cp.r;
                    var cy = centerY + Math.sin(cp.a) * cp.r;
                    if (cs === 0) ctx.moveTo(cx, cy);
                    else ctx.lineTo(cx, cy);
                }
                ctx.strokeStyle = vizColorAtAngle(crack[0].a, cAlpha);
                ctx.lineWidth = 1 + cval * 2 + bass * 1.5;
                ctx.shadowBlur = 2 + intensity * 14;
                ctx.shadowColor = vizColorAtAngle(crack[0].a, 0.75);
                ctx.stroke();
            }
            ctx.shadowBlur = 0;
        }

        vizApplyRadialFade(centerX, centerY, canvas.width, canvas.height);
    }

    // Trigger visualizer initialize on stream trigger
    $(document).off('click.cfViz', '.cf-play-btn-hero').on('click.cfViz', '.cf-play-btn-hero', function() {
        initVisualizer();
        if (audioContext && audioContext.state === 'suspended') {
            audioContext.resume();
        }
    });

});
</script>

<script>
(function () {
    document.querySelectorAll('[data-cf-share]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var url = btn.getAttribute('data-url');
            var title = btn.getAttribute('data-title') || document.title;
            var trackId = btn.getAttribute('data-track-id');
            var platform = navigator.share ? 'native' : 'copy';
            if (trackId && window.CF_Auth && typeof window.CF_Auth.trackShare === 'function') {
                window.CF_Auth.trackShare(trackId, 'track', platform);
            }
            if (navigator.share) {
                navigator.share({ title: title, url: url }).catch(function () {});
            } else if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function () {
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