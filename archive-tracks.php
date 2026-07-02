<?php
/**
 * Template Name: Tracks Archive (Music Library)
 * Description: Displays all tracks in a polished music-library layout.
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="tracks-header">
            <p class="tracks-header-kicker">Collective Finity</p>
            <h1><?php _e( 'Music Library', 'collective-finity' ); ?></h1>
            <p class="tracks-header-copy">
                <?php _e( 'Discover premium cinematic tracks, each crafted with emotion and innovation.', 'collective-finity' ); ?>
            </p>
        </div>

        <div class="tracks-grid-container">
            <?php
            $tracks_query = new WP_Query( array(
                'post_type'      => 'tracks',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ) );

            if ( $tracks_query->have_posts() ) : ?>
                <div class="tracks-grid">
                    <?php
                    $card_index = 0;
                    $ad_options = collective_finity_get_theme_options();
                    $ad_zones   = $ad_options['ad_zones'] ?? array();
                    $ad_frequency = max( 2, absint( $ad_zones['archive_native']['frequency'] ?? 8 ) );
                    while ( $tracks_query->have_posts() ) : $tracks_query->the_post();
                        ++$card_index;
                        $cover_image = get_post_meta( get_the_ID(), 'track_cover_url', true );
                        if ( ! $cover_image ) {
                            $cover_image = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                        }
                        if ( ! $cover_image ) {
                            $cover_image = collective_finity_default_art_url();
                        }

                        $track_audio   = get_post_meta( get_the_ID(), 'track_audio_url', true );
                        $track_preview = get_post_meta( get_the_ID(), 'track_preview_url', true );
                        $audio_url     = ! empty( $track_preview ) ? $track_preview : $track_audio;
                        $artists = wp_get_post_terms( get_the_ID(), 'track_artist', array( 'fields' => 'names' ) );
                        $artist_name = ! empty( $artists ) ? $artists[0] : 'Collective Finity';
                        $bpm = get_post_meta( get_the_ID(), 'track_bpm', true );
                        $key = get_post_meta( get_the_ID(), 'track_key', true );
                        $show_bpm = collective_finity_track_show_bpm( get_the_ID() );
                        $show_key = collective_finity_track_show_key( get_the_ID() );
                        $release_type = get_post_meta( get_the_ID(), 'track_release_type', true );
                        $genres = wp_get_post_terms( get_the_ID(), 'music_genre', array( 'fields' => 'names' ) );
                        $genre_name = ! empty( $genres ) ? $genres[0] : '';
                    ?>
                        <article class="track-card">
                            <div class="track-card-media">
                                <img src="<?php echo esc_url( $cover_image ); ?>" alt="<?php the_title_attribute(); ?>">
                                <div class="track-card-overlay">
                                    <button type="button" class="track-card-play-btn" onclick="if (window.playTrack) { window.playTrack('<?php echo esc_js( $audio_url ); ?>', '<?php echo esc_js( get_the_title() ); ?>', '<?php echo esc_js( $artist_name ); ?>', '<?php echo esc_js( $cover_image ); ?>'); }">
                                        <span class="dashicons dashicons-controls-play"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="track-card-body">
                                <div class="track-card-pill-row">
                                    <span class="track-card-pill track-card-pill-accent">
                                        <?php echo esc_html( $release_type === 'album_track' ? __( 'Album Track', 'collective-finity' ) : __( 'Single', 'collective-finity' ) ); ?>
                                    </span>
                                    <?php if ( $genre_name ) : ?>
                                        <span class="track-card-pill"><?php echo esc_html( $genre_name ); ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <p class="track-card-artist"><?php echo esc_html( $artist_name ); ?></p>

                                <?php if ( ( $show_bpm && $bpm ) || ( $show_key && $key ) ) : ?>
                                    <div class="track-card-meta">
                                        <?php if ( $show_bpm && $bpm ) : ?><span><?php echo esc_html( $bpm ); ?> BPM</span><?php endif; ?>
                                        <?php if ( $show_key && $key ) : ?><span><?php echo $show_bpm && $bpm ? '· ' : ''; ?><?php echo esc_html( $key ); ?></span><?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <a class="track-card-link" href="<?php the_permalink(); ?>"><?php _e( 'Listen now', 'collective-finity' ); ?></a>
                            </div>
                        </article>
                    <?php
                    if ( function_exists( 'collective_finity_ad_slot' ) && 0 === $card_index % $ad_frequency ) :
                        ?>
                        <article class="track-card track-card--ad">
                            <?php collective_finity_ad_slot( 'archive_native' ); ?>
                        </article>
                    <?php endif; ?>
                    <?php endwhile; ?>
                </div>

                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="tracks-empty-state">
                    <span>🎶</span>
                    <h2><?php _e( 'No Tracks Yet', 'collective-finity' ); ?></h2>
                    <p><?php _e( 'New music is on its way. Stay tuned!', 'collective-finity' ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<style>
    .tracks-header {
        text-align: center;
        padding: 90px 20px 40px;
        background: linear-gradient(180deg, rgba(255, 183, 0, 0.08) 0%, transparent 100%);
    }
    .tracks-header-kicker {
        margin: 0 0 8px;
        color: var(--primary-color, #FFB700);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.24em;
        text-transform: uppercase;
    }
    .tracks-header h1 {
        font-size: clamp(28px, 3.2vw, 42px);
        font-weight: 700;
        color: #fff;
        margin: 0 0 12px;
    }
    .tracks-header-copy {
        font-size: 15px;
        color: #9a9a9a;
        max-width: 640px;
        margin: 0 auto;
        line-height: 1.7;
    }
    .tracks-grid-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px 70px;
    }
    .tracks-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 24px;
        margin-top: 24px;
    }
    .track-card {
        background: linear-gradient(180deg, rgba(17, 17, 17, 0.95), rgba(10, 10, 10, 0.95));
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.25);
    }
    .track-card:hover {
        transform: translateY(-6px);
        border-color: rgba(255, 183, 0, 0.4);
        box-shadow: 0 16px 42px rgba(0, 0, 0, 0.4);
    }
    .track-card-media {
        position: relative;
        aspect-ratio: 1 / 1;
        overflow: hidden;
        background: #080808;
    }
    .track-card-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.4s ease;
    }
    .track-card:hover .track-card-media img {
        transform: scale(1.05);
    }
    .track-card-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.12) 0%, rgba(0,0,0,0.6) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    .track-card:hover .track-card-overlay {
        opacity: 1;
    }
    .track-card-play-btn {
        width: 52px;
        height: 52px;
        border: none;
        border-radius: 50%;
        background: rgba(255, 183, 0, 0.95);
        color: #050505;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 10px 24px rgba(0,0,0,0.25);
    }
    .track-card-play-btn .dashicons {
        font-size: 22px;
        width: 22px;
        height: 22px;
    }
    .track-card-body {
        padding: 18px;
    }
    .track-card-pill-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    .track-card-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 999px;
        background: rgba(255,255,255,0.06);
        color: #b8b8b8;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .track-card-pill-accent {
        background: rgba(255, 183, 0, 0.14);
        color: var(--primary-color, #FFB700);
    }
    .track-card h3 {
        margin: 0 0 6px;
        font-size: 16px;
        font-weight: 700;
    }
    .track-card h3 a {
        color: #fff;
        text-decoration: none;
    }
    .track-card-artist {
        margin: 0 0 10px;
        color: #9a9a9a;
        font-size: 13px;
    }
    .track-card-meta {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        color: #6f6f6f;
        font-size: 11px;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .track-card-link {
        color: var(--primary-color, #FFB700);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }
    .tracks-empty-state {
        text-align: center;
        padding: 80px 20px;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
        background: rgba(255,255,255,0.02);
    }
    .tracks-empty-state span {
        font-size: 48px;
        display: block;
        margin-bottom: 18px;
    }
    .tracks-empty-state h2 {
        color: #fff;
        margin: 0 0 10px;
        font-size: 24px;
    }
    .tracks-empty-state p {
        color: #888;
        margin: 0;
        line-height: 1.7;
    }
    .track-card--ad {
        align-items: center;
        display: flex;
        justify-content: center;
        min-height: 220px;
        padding: 16px;
    }
    .cf-ad-slot {
        margin: 0 auto;
        max-width: 100%;
        text-align: center;
        width: 100%;
    }
    .cf-ad-slot--preview {
        align-items: center;
        background: rgba(255, 255, 255, 0.04);
        border: 1px dashed rgba(255, 183, 0, 0.35);
        border-radius: 12px;
        color: rgba(255, 255, 255, 0.55);
        display: flex;
        font-family: "Space Mono", monospace;
        font-size: 13px;
        justify-content: center;
        min-height: 120px;
        padding: 24px;
    }
</style>

<?php get_footer(); ?>