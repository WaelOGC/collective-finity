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
        $release_type = get_post_meta( get_the_ID(), 'track_release_type', true );
        $playback_url = ! empty( $preview_url ) ? $preview_url : $audio_url;

        // Streaming Links
        $spotify      = get_post_meta( get_the_ID(), 'track_spotify_url', true );
        $apple        = get_post_meta( get_the_ID(), 'track_apple_url', true );
        $soundcloud   = get_post_meta( get_the_ID(), 'track_soundcloud_url', true );
        $youtube      = get_post_meta( get_the_ID(), 'track_youtube_url', true );
        $bandcamp     = get_post_meta( get_the_ID(), 'track_bandcamp_url', true );

        // Genre Taxonomy
        $genres = wp_get_post_terms( get_the_ID(), 'music_genre' );
        $genre_name = ! empty( $genres ) ? $genres[0]->name : 'Ambient';

        // Artist Taxonomy
        $artists = wp_get_post_terms( get_the_ID(), 'track_artist' );
        $artist_name = ! empty( $artists ) ? $artists[0]->name : 'Collective Finity';

        // Smart Cover Fallback
        if ( empty( $cover_url ) ) {
            $associated_album = get_post_meta( get_the_ID(), 'associated_album', true );
            if ( $associated_album ) {
                $cover_url = get_the_post_thumbnail_url( $associated_album, 'full' );
            }
        }
        if ( empty( $cover_url ) ) {
            $cover_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
        }
        if ( empty( $cover_url ) ) {
            $cover_url = collective_finity_default_art_url();
        }
    ?>

    <!-- Background blurred album art with dynamic visual options -->
    <div class="cf-cinematic-hero" style="background-image: linear-gradient(to bottom, rgba(0,0,0,0.85), #000000), url('<?php echo esc_url($cover_url); ?>');">
        <div class="cf-container">
            
            <div class="cf-track-header-layout">
                
                <!-- 2. SPINNING VINYL DISC WITH SPECTRUM AUDIO VISUALIZER CANVAS (Fully responsive aspect-ratio) -->
                <div class="cf-vinyl-wrapper">
                    <div class="cf-vinyl-inner">
                        <canvas id="cf-circular-visualizer" width="360" height="360"></canvas>
                        <img src="<?php echo esc_url($cover_url); ?>" class="cf-vinyl-disc" id="cf-track-spinning-vinyl" alt="<?php the_title(); ?>">
                    </div>
                    
                    <!-- 2. Visualizer drop down selector -->
                    <div class="cf-visualizer-selector-wrapper">
                        <label for="cf-visualizer-type"><?php _e('Audio Effect Style:', 'collective-finity'); ?></label>
                        <select id="cf-visualizer-type">
                            <option value="neon_ring" selected><?php _e( 'Neon Glow Ring', 'collective-finity' ); ?></option>
                            <option value="spectrum_bars"><?php _e( 'Spectrum Equalizer Bars', 'collective-finity' ); ?></option>
                            <option value="glowing_sine"><?php _e( 'Glowing Sine Wave', 'collective-finity' ); ?></option>
                            <option value="beat_pulse"><?php _e( 'Cosmic Beat Pulse', 'collective-finity' ); ?></option>
                            <option value="dual_ring"><?php _e( 'Dual Orbit Ring', 'collective-finity' ); ?></option>
                            <option value="particle_burst"><?php _e( 'Particle Burst', 'collective-finity' ); ?></option>
                            <option value="frequency_wave"><?php _e( 'Frequency Wave', 'collective-finity' ); ?></option>
                            <option value="starfield"><?php _e( 'Starfield Pulse', 'collective-finity' ); ?></option>
                            <option value="aurora_fill"><?php _e( 'Aurora Fill', 'collective-finity' ); ?></option>
                            <option value="hexagon_grid"><?php _e( 'Hexagon Grid', 'collective-finity' ); ?></option>
                        </select>
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
                        <p class="cf-hero-artist"><?php echo esc_html($artist_name); ?></p>
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
                    </div>
                </div>
            </div>

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
            <div class="cf-external-platforms-wrapper cf-glass-card">
                <span class="cf-platforms-title"><?php _e('Listen on External Platforms', 'collective-finity'); ?></span>
                <div class="cf-platforms-grid">
                    <?php if($spotify): ?>
                        <a href="<?php echo esc_url($spotify); ?>" target="_blank" class="cf-platform-icon-btn" title="Spotify">
                            <svg viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm4.586 14.424c-.18.295-.417.387-.714.207-2.39-1.46-5.4-1.79-8.946-.983-.34.078-.615-.187-.69-.47-.075-.333.15-.658.463-.733 3.882-.888 7.21-.5 9.873 1.13.292.176.31.554.114.85zm1.224-2.724c-.226.367-.626.49-.982.262-2.735-1.68-6.904-2.17-10.128-1.192-.41.124-.836-.14-.954-.537-.118-.396.11-.844.516-.96 3.694-1.12 8.283-.573 11.414 2.247.332.22.427.674.134 1.18zm.107-2.836C14.502 8.78 8.04 8.567 4.3 9.702c-.575.174-1.18-.184-1.353-.75-.173-.565.155-1.196.732-1.37 4.3-1.304 11.436-1.047 15.485 1.36.518.516 0 1 .37 1.35.37.28.84.45 1.45.45.54 0 1-.22 1.35-.45.37-.35.37-.84.37-1.35z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if($apple): ?>
                        <a href="<?php echo esc_url($apple); ?>" target="_blank" class="cf-platform-icon-btn" title="Apple Music">
                            <svg viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm4.333 13.914c-.39.26-.816.388-1.258.388-.34 0-.676-.08-1.008-.242l-2.067-1.034v.538c0 .858-.458 1.636-1.198 2.032-.39.21-.817.314-1.242.314-.492 0-.974-.14-1.4-.412L6.11 16.146c-.732-.472-1.144-1.272-1.144-2.138v-3.076c0-.858.458-1.636 1.198-2.032l2.054-1.11c.39-.21.817-.314 1.242-.314.492 0 .974.14 1.4.412l2.054 1.358c.732.472 1.144 1.272 1.144 2.138v.538l2.067-1.034c.332-.162.668-.242 1.008-.242.442 0 .868.128 1.258.388.756.504 1.167 1.358 1.167 2.29v1.238c0 .932-.411 1.786-1.167 2.29z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if($soundcloud): ?>
                        <a href="<?php echo esc_url($soundcloud); ?>" target="_blank" class="cf-platform-icon-btn" title="Soundcloud">
                            <svg viewBox="0 0 24 24"><path d="M11.56 16.5c0-.18.01-.36.03-.53l.03-.23c-1.39-.12-2.5-1.14-2.5-2.43 0-1.14.87-2.07 2.05-2.28-.01-.13-.02-.27-.02-.4 0-1.74 1.75-3.15 3.91-3.15 1.5 0 2.8 1 3.48 2.45.39-.14.8-.23 1.24-.23 1.95 0 3.53 1.41 3.53 3.15 0 1.95-1.58 3.53-3.53 3.53h-8.2zm-1.04-4.8c-.37-.5-1.02-.73-1.62-.57-.4.1-.73.38-.9.77-.1.25-.13.52-.09.78.1.53.52.92 1.06.94l1.55.08V11.7z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if($youtube): ?>
                        <a href="<?php echo esc_url($youtube); ?>" target="_blank" class="cf-platform-icon-btn" title="YouTube">
                            <svg viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 0 0-2.11-2.11C19.517 3.545 12 3.545 12 3.545s0 0 0 0h-.002s-7.517 0-9.388.507a3.003 3.003 0 0 0-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 0 0 2.11 2.11c1.871.507 9.388.507 9.388.507s7.517 0 9.388-.507a3.003 3.003 0 0 0 2.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if($bandcamp): ?>
                        <a href="<?php echo esc_url($bandcamp); ?>" target="_blank" class="cf-platform-icon-btn" title="Bandcamp">
                            <svg viewBox="0 0 24 24"><path d="M22 6H12l-2.5 4H2v8h20V6z"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ( function_exists( 'collective_finity_ad_slot' ) ) : ?>
            <div class="cf-track-ad-sidebar cf-glass-card">
                <?php collective_finity_ad_slot( 'track_sidebar' ); ?>
            </div>
            <?php endif; ?>

            <!-- 6. STORY & AUDIO-TRACKED LYRICS PLAYER SECTION -->
            <div class="cf-content-area cf-glass-card">
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

                <!-- 2. STYLIZED COMMENTS SECTION WITH EMOJI SELECTOR AND FORCED INPUT FORM -->
                <div id="cf-track-comments" class="cf-comments-section-wrapper" style="border-top:1px solid rgba(255,255,255,0.1); padding-top:40px;">
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

<script type="text/javascript">window.cfPageTrackId = <?php echo (int) $track_id; ?>;</script>

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
    padding-bottom: 40px;
    max-width: 100%;
    overflow-x: hidden;
}
.cf-cinematic-hero {
    min-height: 100vh;
    background-size: cover;
    background-position: center;
    padding: 108px 0 70px;
    position: relative;
}
.cf-cinematic-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(140deg, rgba(0, 0, 0, 0.88) 0%, rgba(0, 0, 0, 0.72) 45%, rgba(0, 0, 0, 0.9) 100%);
    z-index: 0;
}
.cf-cinematic-hero > .cf-container {
    position: relative;
    z-index: 1;
}
.cf-container { width: 90%; max-width: 1100px; margin: 0 auto; box-sizing: border-box; }
.cf-track-header-layout { display: flex; align-items: center; gap: 50px; margin-bottom: 50px; flex-wrap: wrap; min-width: 0; max-width: 100%; }

/* 2. Visualizer Canvas styling (Upgraded for fluid aspect-ratio mobile responsiveness) */
.cf-vinyl-wrapper { flex: 1 1 250px; min-width: 0; max-width: 100%; display: flex; flex-direction: column; align-items: center; position: relative; }
.cf-vinyl-inner { position: relative; width: 100%; max-width: 340px; aspect-ratio: 1 / 1; display: flex; justify-content: center; align-items: center; margin-bottom: 20px; }
#cf-circular-visualizer { position: absolute; top: -10px; left: -10px; width: 100% !important; height: 100% !important; max-width: 360px; max-height: 360px; z-index: 1; pointer-events: none; }
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
.cf-hero-subline { margin: 0 0 24px; font-size: 13px; letter-spacing: 0.14em; text-transform: uppercase; color: #b7b7b7; }

.cf-hero-actions-row { display: flex; gap: 15px; flex-wrap: wrap; }
.cf-play-btn-hero { background: linear-gradient(135deg, var(--primary-color), #ffce4d); color: #050505; border: none; padding: 14px 32px; font-size: 16px; font-weight: bold; border-radius: 999px; cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.25s ease; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 8px 20px rgba(255, 183, 0, 0.22); }
.cf-play-btn-hero:hover { background: linear-gradient(135deg, #ffd460, var(--primary-color)); transform: translateY(-1px); box-shadow: 0 10px 24px rgba(255, 183, 0, 0.3); }
.cf-cta-btn-hero { background: transparent; color: #FFFFFF; border: 2px solid rgba(255,255,255,0.75); padding: 12px 30px; font-size: 16px; font-weight: bold; border-radius: 999px; text-decoration: none; transition: background 0.25s, color 0.25s, border-color 0.25s; }
.cf-cta-btn-hero:hover { background: rgba(255, 183, 0, 0.12); border-color: var(--primary-color); color: var(--primary-color); }

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
        padding-left: 16px;
        padding-right: 16px;
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
jQuery(document).ready(function($) {
    
    // --- 2. Live Stats Sync Cache-Buster on Load (Bypasses LiteSpeed Caching for Stats) ---
    if ( cf_ajax.logged_in ) {
        $.ajax({
            url: cf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cf_get_liked_tracks',
                security: cf_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data.liked_tracks) {
                    var likedTracks = response.data.liked_tracks;
                    var trackId = <?php echo intval($track_id); ?>;
                    if (likedTracks.indexOf(trackId) !== -1) {
                        $('.live-likes-btn').addClass('active');
                    }
                }
            }
        });
    }


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


    // --- 5. Web Audio API Circular Frequency Analyzer visualizer (With 5 Live Style Options) ---
    var audioContext;
    var analyser;
    var source;
    var canvas = document.getElementById('cf-circular-visualizer');
    var ctx = canvas ? canvas.getContext('2d') : null;
    var bufferLength;
    var dataArray;

    function initVisualizer() {
        if (audioContext || window.cfAudioMediaSourceConnected) {
            return;
        }

        try {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            analyser = audioContext.createAnalyser();
            
            // Connect global player element securely
            source = audioContext.createMediaElementSource(audio);
            source.connect(analyser);
            analyser.connect(audioContext.destination);
            window.cfAudioMediaSourceConnected = true;

            analyser.fftSize = 256; 
            bufferLength = analyser.frequencyBinCount;
            dataArray = new Uint8Array(bufferLength);
            
            drawVisualizer();
        } catch(e) {
            console.log("Web Audio API not supported on this browser context.");
        }
    }

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
        var radius = 142; // Surrounds the 280px cover disc neatly
        var bars = 64;
        
        // Grab current dynamic visualizer selection from dropdown
        var selectedStyle = $('#cf-visualizer-type').val();

        if (selectedStyle === 'neon_ring') {
            // Style 1: Outward Neon Glow Ring
            for (var i = 0; i < bars; i++) {
                var val = dataArray[i % bufferLength];
                var barHeight = (val / 255) * 22; // Height scaling factor

                var rads = (Math.PI * 2) / bars * i;
                var x_start = centerX + Math.cos(rads) * radius;
                var y_start = centerY + Math.sin(rads) * radius;
                
                var x_end = centerX + Math.cos(rads) * (radius + barHeight);
                var y_end = centerY + Math.sin(rads) * (radius + barHeight);

                ctx.strokeStyle = i % 2 === 0 ? '#FFB700' : '#00FFFF';
                ctx.lineWidth = 3;
                ctx.beginPath();
                ctx.moveTo(x_start, y_start);
                ctx.lineTo(x_end, y_end);
                ctx.stroke();
            }
        } 
        else if (selectedStyle === 'spectrum_bars') {
            // Style 2: Dense Linear Spectrum Equalizer Bars
            var linearBarWidth = (canvas.width / bufferLength) * 1.5;
            var barX = 0;

            for(var i = 0; i < bufferLength; i++) {
                var val = dataArray[i];
                var barHeight = (val / 255) * 60;

                ctx.fillStyle = 'rgba(255, 183, 0, 0.8)';
                ctx.fillRect(barX, canvas.height - barHeight, linearBarWidth - 2, barHeight);
                barX += linearBarWidth;
            }
        } 
        else if (selectedStyle === 'glowing_sine') {
            ctx.beginPath();
            for (var i = 0; i <= bars; i++) {
                var val = dataArray[i % bufferLength];
                var waveScale = (val / 255) * 15;
                var rads = (Math.PI * 2) / bars * i;
                var x = centerX + Math.cos(rads) * (radius + waveScale);
                var y = centerY + Math.sin(rads) * (radius + waveScale);
                if (i === 0) ctx.moveTo(x, y);
                else ctx.lineTo(x, y);
            }
            ctx.closePath();
            ctx.strokeStyle = '#00FFFF';
            ctx.lineWidth = 4;
            ctx.shadowBlur = 10;
            ctx.shadowColor = '#00FFFF';
            ctx.stroke();
            ctx.shadowBlur = 0;
        }
        else if (selectedStyle === 'beat_pulse') {
            var totalBass = 0;
            for (var bi = 0; bi < 10; bi++) totalBass += dataArray[bi];
            var avgBass = totalBass / 10;
            var pulseScale = (avgBass / 255) * 35;
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius - 2, 0, 2 * Math.PI);
            ctx.fillStyle = 'rgba(255, 183, 0, 0.05)';
            ctx.fill();
            ctx.lineWidth = 2;
            ctx.strokeStyle = '#FFB700';
            ctx.shadowBlur = 15 + pulseScale;
            ctx.shadowColor = '#FFB700';
            ctx.stroke();
            ctx.shadowBlur = 0;
        }
        else if (selectedStyle === 'dual_ring') {
            var timeRotation = Date.now() * 0.001;
            for (var di = 0; di < 30; di++) {
                var dval = dataArray[di % bufferLength];
                var dbar = (dval / 255) * 12;
                var drads = ((Math.PI * 2) / 30 * di) + timeRotation;
                ctx.fillStyle = '#FFB700';
                ctx.fillRect(centerX + Math.cos(drads) * (radius - 15 - dbar) - 2, centerY + Math.sin(drads) * (radius - 15 - dbar) - 2, 4, 4);
            }
            for (var dj = 0; dj < 40; dj++) {
                var dval2 = dataArray[dj % bufferLength];
                var dbar2 = (dval2 / 255) * 12;
                var drads2 = ((Math.PI * 2) / 40 * dj) - timeRotation;
                ctx.fillStyle = '#00FFFF';
                ctx.fillRect(centerX + Math.cos(drads2) * (radius + 15 + dbar2) - 2, centerY + Math.sin(drads2) * (radius + 15 + dbar2) - 2, 4, 4);
            }
        }
        else if (selectedStyle === 'particle_burst') {
            for (var p = 0; p < 48; p++) {
                var pval = dataArray[p % bufferLength];
                var dist = radius + (pval / 255) * 40;
                var prads = (Math.PI * 2 / 48) * p;
                ctx.beginPath();
                ctx.arc(centerX + Math.cos(prads) * dist, centerY + Math.sin(prads) * dist, 2 + (pval / 255) * 3, 0, Math.PI * 2);
                ctx.fillStyle = p % 2 === 0 ? 'rgba(255,183,0,0.85)' : 'rgba(0,255,255,0.65)';
                ctx.fill();
            }
        }
        else if (selectedStyle === 'frequency_wave') {
            ctx.beginPath();
            for (var w = 0; w < bufferLength; w++) {
                var wval = dataArray[w];
                var wx = (w / bufferLength) * canvas.width;
                var wy = centerY + Math.sin(w * 0.15) * (wval / 255) * 50;
                if (w === 0) ctx.moveTo(wx, wy);
                else ctx.lineTo(wx, wy);
            }
            ctx.strokeStyle = '#FFB700';
            ctx.lineWidth = 2;
            ctx.shadowBlur = 8;
            ctx.shadowColor = '#FFB700';
            ctx.stroke();
            ctx.shadowBlur = 0;
        }
        else if (selectedStyle === 'starfield') {
            for (var s = 0; s < 60; s++) {
                var sval = dataArray[s % bufferLength];
                var srads = (Math.PI * 2 / 60) * s + Date.now() * 0.0005;
                var sr = radius - 20 + (s % 5) * 8 + (sval / 255) * 10;
                ctx.beginPath();
                ctx.arc(centerX + Math.cos(srads) * sr, centerY + Math.sin(srads) * sr, 1 + (sval / 255) * 2, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(255,255,255,' + (0.3 + sval / 400) + ')';
                ctx.fill();
            }
        }
        else if (selectedStyle === 'aurora_fill') {
            var grad = ctx.createRadialGradient(centerX, centerY, radius * 0.4, centerX, centerY, radius + 30);
            var bassAvg = 0;
            for (var b = 0; b < 8; b++) bassAvg += dataArray[b];
            bassAvg /= 8;
            grad.addColorStop(0, 'rgba(255,183,0,' + (bassAvg / 600) + ')');
            grad.addColorStop(0.5, 'rgba(0,255,255,' + (bassAvg / 800) + ')');
            grad.addColorStop(1, 'rgba(0,0,0,0)');
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius + 25, 0, Math.PI * 2);
            ctx.fillStyle = grad;
            ctx.fill();
        }
        else if (selectedStyle === 'hexagon_grid') {
            for (var h = 0; h < 24; h++) {
                var hval = dataArray[h % bufferLength];
                var hrads = (Math.PI * 2 / 24) * h;
                var hx = centerX + Math.cos(hrads) * (radius + (hval / 255) * 18);
                var hy = centerY + Math.sin(hrads) * (radius + (hval / 255) * 18);
                var size = 5 + (hval / 255) * 4;
                ctx.beginPath();
                for (var side = 0; side < 6; side++) {
                    var angle = (Math.PI / 3) * side + hrads;
                    var sx = hx + Math.cos(angle) * size;
                    var sy = hy + Math.sin(angle) * size;
                    if (side === 0) ctx.moveTo(sx, sy);
                    else ctx.lineTo(sx, sy);
                }
                ctx.closePath();
                ctx.strokeStyle = h % 2 === 0 ? '#FFB700' : '#00FFFF';
                ctx.lineWidth = 1.5;
                ctx.stroke();
            }
        }
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

<?php get_footer(); ?>