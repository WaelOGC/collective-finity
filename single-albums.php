<?php
/**
 * Single Album template.
 *
 * @package Collective_Finity
 */

get_header();
?>

<div class="cf-single-album-page">
    <?php
    while ( have_posts() ) :
        the_post();
        $album_id = get_the_ID();

        $tracks_query = new WP_Query(
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
                        'value'   => $album_id,
                        'compare' => '=',
                    ),
                ),
            )
        );

        $album_queue   = array();
        $playable_count = 0;
        $tracks_data   = array();
        $cover_url     = get_the_post_thumbnail_url( $album_id, 'full' );

        if ( empty( $cover_url ) && $tracks_query->have_posts() ) {
            while ( $tracks_query->have_posts() ) {
                $tracks_query->the_post();
                $first_track_cover = get_post_meta( get_the_ID(), 'track_cover_url', true );
                if ( ! empty( $first_track_cover ) ) {
                    $cover_url = $first_track_cover;
                    break;
                }
            }
            $tracks_query->rewind_posts();
        }

        if ( empty( $cover_url ) ) {
            $cover_url = collective_finity_default_art_url();
        }

        $album_date = get_the_date( 'M Y' );
        $track_total = (int) $tracks_query->found_posts;
        $tracks_data = array();

        if ( $tracks_query->have_posts() ) {
            while ( $tracks_query->have_posts() ) {
                $tracks_query->the_post();
                $track_id      = get_the_ID();
                $track_audio   = get_post_meta( $track_id, 'track_audio_url', true );
                $track_preview = get_post_meta( $track_id, 'track_preview_url', true );
                $playback_url  = ! empty( $track_preview ) ? $track_preview : $track_audio;
                $track_cover   = get_post_meta( $track_id, 'track_cover_url', true );

                if ( empty( $track_cover ) ) {
                    $track_cover = get_the_post_thumbnail_url( $track_id, 'medium' );
                }
                if ( empty( $track_cover ) ) {
                    $track_cover = $cover_url;
                }

                $artists      = wp_get_post_terms( $track_id, 'track_artist' );
                $track_artist = ! empty( $artists ) ? $artists[0]->name : collective_finity_brand_name();

                if ( $playback_url ) {
                    $album_queue[] = array(
                        'url'    => $playback_url,
                        'title'  => get_the_title(),
                        'artist' => $track_artist,
                        'art'    => $track_cover,
                        'id'     => $track_id,
                    );
                    ++$playable_count;
                }

                $tracks_data[] = array(
                    'id'           => $track_id,
                    'title'        => get_the_title(),
                    'permalink'    => get_permalink(),
                    'artist'       => $track_artist,
                    'bpm'          => get_post_meta( $track_id, 'track_bpm', true ) ?: '—',
                    'key'          => get_post_meta( $track_id, 'track_key', true ) ?: '—',
                    'show_bpm'     => collective_finity_track_show_bpm( $track_id ),
                    'show_key'     => collective_finity_track_show_key( $track_id ),
                    'views'        => collective_finity_track_views( $track_id ),
                    'comments'     => collective_finity_track_comments_count( $track_id ),
                    'playback_url' => $playback_url,
                    'queue_index'  => $playback_url ? count( $album_queue ) - 1 : null,
                );
            }
            wp_reset_postdata();
        }

        $show_bpm = false;
        $show_key = false;
        foreach ( $tracks_data as $track_row ) {
            if ( ! empty( $track_row['show_bpm'] ) ) {
                $show_bpm = true;
            }
            if ( ! empty( $track_row['show_key'] ) ) {
                $show_key = true;
            }
        }
        ?>

    <div class="cf-album-ambient" style="--cf-album-art: url('<?php echo esc_url( $cover_url ); ?>');"></div>

    <div class="cf-album-shell">
        <div class="cf-container">

            <header class="cf-album-hero">
                <div class="cf-album-art-frame">
                    <img src="<?php echo esc_url( $cover_url ); ?>" alt="<?php the_title_attribute(); ?>" class="cf-album-cover-img" loading="eager">
                    <div class="cf-album-art-glow" aria-hidden="true"></div>
                </div>

                <div class="cf-album-hero-content">
                    <span class="cf-album-eyebrow"><?php esc_html_e( 'Album', 'collective-finity' ); ?></span>
                    <h1 class="cf-album-title"><?php the_title(); ?></h1>
                    <p class="cf-album-artist"><?php echo esc_html( collective_finity_brand_name() ); ?></p>

                    <?php if ( get_the_content() ) : ?>
                        <div class="cf-album-description">
                            <?php the_content(); ?>
                        </div>
                    <?php endif; ?>

                    <div class="cf-album-meta-row">
                        <span class="cf-album-meta-pill">
                            <span class="dashicons dashicons-format-audio"></span>
                            <?php
                            printf(
                                /* translators: %d: number of tracks */
                                esc_html( _n( '%d track', '%d tracks', $track_total, 'collective-finity' ) ),
                                esc_html( (string) $track_total )
                            );
                            ?>
                        </span>
                        <span class="cf-album-meta-pill">
                            <span class="dashicons dashicons-calendar-alt"></span>
                            <?php echo esc_html( $album_date ); ?>
                        </span>
                        <span class="cf-album-meta-pill">
                            <span class="dashicons dashicons-admin-users"></span>
                            <?php echo esc_html( collective_finity_brand_name() ); ?>
                        </span>
                    </div>

                    <?php if ( ! empty( $tracks_data ) ) : ?>
                        <div class="cf-album-actions">
                            <button type="button" class="cf-play-album-btn cf-btn-primary" <?php echo empty( $album_queue ) ? 'disabled' : ''; ?>>
                                <span class="cf-icon cf-icon-play" aria-hidden="true"></span>
                                <?php esc_html_e( 'Play All', 'collective-finity' ); ?>
                            </button>
                            <button type="button" class="cf-interaction-btn cf-playlist-btn"
                                data-item-id="<?php echo esc_attr( (string) $album_id ); ?>"
                                data-item-type="album"
                                title="<?php esc_attr_e( 'Add Album to Playlist', 'collective-finity' ); ?>"
                                aria-label="<?php esc_attr_e( 'Add Album to Playlist', 'collective-finity' ); ?>">
                                <span class="dashicons dashicons-playlist-audio"></span>
                            </button>
                            <?php if ( $playable_count < $track_total && $track_total > 0 ) : ?>
                                <span class="cf-album-play-note"><?php esc_html_e( 'Some tracks have no audio file yet.', 'collective-finity' ); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="cf-album-share-wrap">
                            <?php collective_finity_render_share_buttons( get_permalink(), get_the_title(), 'album' ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( function_exists( 'collective_finity_ad_slot' ) ) : ?>
                        <div class="cf-album-ad-sidebar">
                            <?php collective_finity_ad_slot( 'album_sidebar' ); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </header>

            <section class="cf-album-tracklist-section" aria-label="<?php esc_attr_e( 'Album tracklist', 'collective-finity' ); ?>">
                <div class="cf-tracklist-header">
                    <h2><?php esc_html_e( 'Tracklist', 'collective-finity' ); ?></h2>
                    <?php if ( $track_total > 0 ) : ?>
                        <span class="cf-tracklist-count"><?php echo esc_html( (string) $track_total ); ?> <?php echo esc_html( _n( 'song', 'songs', $track_total, 'collective-finity' ) ); ?></span>
                    <?php endif; ?>
                </div>

                <?php if ( ! empty( $tracks_data ) ) : ?>
                    <div class="cf-tracklist-scroll">
                    <div class="cf-tracklist-grid" role="table">
                        <div class="cf-tracklist-row cf-tracklist-head" role="row">
                            <div class="cf-col-index" role="columnheader"><span>#</span></div>
                            <div class="cf-col-title" role="columnheader"><?php esc_html_e( 'Title', 'collective-finity' ); ?></div>
                            <?php if ( $show_bpm ) : ?>
                                <div class="cf-col-bpm" role="columnheader"><?php esc_html_e( 'BPM', 'collective-finity' ); ?></div>
                            <?php endif; ?>
                            <?php if ( $show_key ) : ?>
                                <div class="cf-col-key" role="columnheader"><?php esc_html_e( 'Key', 'collective-finity' ); ?></div>
                            <?php endif; ?>
                            <div class="cf-col-views" role="columnheader"><?php esc_html_e( 'Views', 'collective-finity' ); ?></div>
                            <div class="cf-col-comments" role="columnheader"><?php esc_html_e( 'Comments', 'collective-finity' ); ?></div>
                            <div class="cf-col-playlist" role="columnheader"><?php esc_html_e( 'Add to List', 'collective-finity' ); ?></div>
                            <div class="cf-col-like" role="columnheader"><?php esc_html_e( 'Like', 'collective-finity' ); ?></div>
                            <div class="cf-col-view" role="columnheader"><?php esc_html_e( 'View', 'collective-finity' ); ?></div>
                        </div>

                        <?php
                        $track_counter = 1;
                        foreach ( $tracks_data as $track ) :
                            ?>
                            <div class="cf-tracklist-row cf-album-track-row" role="row" data-track-id="<?php echo esc_attr( (string) $track['id'] ); ?>">
                                <div class="cf-col-index" role="cell">
                                    <?php if ( $track['playback_url'] ) : ?>
                                        <button type="button" class="cf-list-play-trigger" data-queue-index="<?php echo esc_attr( (string) $track['queue_index'] ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Play %s', 'collective-finity' ), $track['title'] ) ); ?>">
                                            <span class="cf-track-num"><?php echo esc_html( (string) $track_counter ); ?></span>
                                            <span class="cf-icon cf-icon-play cf-track-play-icon" aria-hidden="true"></span>
                                        </button>
                                    <?php else : ?>
                                        <span class="cf-track-num cf-track-num-static"><?php echo esc_html( (string) $track_counter ); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="cf-col-title" role="cell">
                                    <a href="<?php echo esc_url( $track['permalink'] ); ?>" class="cf-track-name"><?php echo esc_html( $track['title'] ); ?></a>
                                    <span class="cf-track-artist-name"><?php echo esc_html( $track['artist'] ); ?></span>
                                </div>

                                <?php if ( $show_bpm ) : ?>
                                    <div class="cf-col-bpm" role="cell"><?php echo ! empty( $track['show_bpm'] ) ? esc_html( $track['bpm'] ) : '—'; ?></div>
                                <?php endif; ?>

                                <?php if ( $show_key ) : ?>
                                    <div class="cf-col-key" role="cell"><?php echo ! empty( $track['show_key'] ) ? esc_html( $track['key'] ) : '—'; ?></div>
                                <?php endif; ?>

                                <div class="cf-col-views" role="cell">
                                    <span class="cf-stat-chip" title="<?php esc_attr_e( 'Views', 'collective-finity' ); ?>">
                                        <span class="dashicons dashicons-visibility"></span>
                                        <?php echo esc_html( number_format_i18n( (int) $track['views'] ) ); ?>
                                    </span>
                                </div>

                                <div class="cf-col-comments" role="cell">
                                    <a href="<?php echo esc_url( $track['permalink'] . '#cf-track-comments' ); ?>" class="cf-stat-chip cf-stat-chip--link" title="<?php esc_attr_e( 'Comments', 'collective-finity' ); ?>">
                                        <span class="dashicons dashicons-admin-comments"></span>
                                        <?php echo esc_html( number_format_i18n( (int) $track['comments'] ) ); ?>
                                    </a>
                                </div>

                                <div class="cf-col-playlist" role="cell">
                                    <button type="button" class="cf-interaction-btn cf-playlist-btn" data-track-id="<?php echo esc_attr( (string) $track['id'] ); ?>" title="<?php esc_attr_e( 'Add to Playlist', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Add to Playlist', 'collective-finity' ); ?>">
                                        <span class="dashicons dashicons-playlist-audio"></span>
                                    </button>
                                </div>

                                <div class="cf-col-like" role="cell">
                                    <button type="button" class="cf-interaction-btn cf-like-btn" data-track-id="<?php echo esc_attr( (string) $track['id'] ); ?>" title="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>">
                                        <span class="dashicons dashicons-heart"></span>
                                    </button>
                                </div>

                                <div class="cf-col-view" role="cell">
                                    <a href="<?php echo esc_url( $track['permalink'] ); ?>" class="cf-view-track-btn"><?php esc_html_e( 'View', 'collective-finity' ); ?></a>
                                </div>
                            </div>
                            <?php
                            ++$track_counter;
                        endforeach;
                        ?>
                    </div>
                    </div>
                <?php else : ?>
                    <p class="cf-empty-tracklist-msg"><?php esc_html_e( 'No tracks have been assigned to this album yet.', 'collective-finity' ); ?></p>
                <?php endif; ?>
            </section>

        </div>
    </div>
        <?php
    endwhile;
    ?>
</div>

<script type="text/javascript">
window.cfAlbumQueue = <?php echo wp_json_encode( $album_queue ); ?>;
</script>

<style>
.cf-single-album-page {
    position: relative;
    min-height: 100vh;
    background: #050505;
    overflow: hidden;
}
.cf-album-ambient {
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(ellipse 80% 50% at 20% -10%, rgba(255, 183, 0, 0.18), transparent 55%),
        radial-gradient(ellipse 60% 40% at 90% 10%, rgba(255, 183, 0, 0.08), transparent 50%),
        linear-gradient(180deg, rgba(0, 0, 0, 0.3) 0%, #050505 70%);
    pointer-events: none;
}
.cf-album-ambient::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: var(--cf-album-art);
    background-size: cover;
    background-position: center;
    opacity: 0.12;
    filter: blur(60px) saturate(120%);
    transform: scale(1.2);
}
.cf-album-shell {
    position: relative;
    z-index: 1;
    padding: 48px 0 80px;
}
.cf-container {
    width: min(1120px, 92%);
    margin: 0 auto;
}
.cf-album-hero {
    display: grid;
    grid-template-columns: minmax(220px, 280px) 1fr;
    gap: 48px;
    align-items: end;
    margin-bottom: 48px;
}
.cf-album-art-frame {
    position: relative;
}
.cf-album-cover-img {
    width: 100%;
    aspect-ratio: 1;
    object-fit: cover;
    border-radius: 16px;
    display: block;
    position: relative;
    z-index: 1;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.06);
}
.cf-album-art-glow {
    position: absolute;
    inset: 20%;
    background: var(--primary-color, #FFB700);
    filter: blur(50px);
    opacity: 0.25;
    z-index: 0;
}
.cf-album-eyebrow {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--primary-color, #FFB700);
    margin-bottom: 12px;
}
.cf-album-title {
    font-size: clamp(2rem, 4.5vw, 3.4rem);
    font-weight: 800;
    line-height: 1.05;
    color: #fff;
    margin: 0 0 8px;
    letter-spacing: -0.02em;
}
.cf-album-artist {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.55);
    margin: 0 0 20px;
    font-weight: 500;
}
.cf-album-description {
    font-size: 0.95rem;
    line-height: 1.75;
    color: rgba(255, 255, 255, 0.6);
    max-width: 560px;
    margin-bottom: 20px;
}
.cf-album-meta-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 24px;
}
.cf-album-meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 14px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.07);
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.78rem;
}
.cf-album-meta-pill .dashicons {
    color: var(--primary-color, #FFB700);
    font-size: 15px;
    width: 15px;
    height: 15px;
}
.cf-album-actions {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}
.cf-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--primary-color, #FFB700);
    color: #0a0a0a;
    border: none;
    border-radius: 999px;
    padding: 14px 32px;
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 24px rgba(255, 183, 0, 0.35);
    transition: transform 0.15s, box-shadow 0.2s, opacity 0.2s;
}
.cf-btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(255, 183, 0, 0.45);
}
.cf-btn-primary:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}
.cf-btn-primary .cf-icon {
    width: 16px;
    height: 16px;
}
.cf-album-play-note {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.4);
}
.cf-album-tracklist-section {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 20px;
    padding: 28px 24px;
    backdrop-filter: blur(12px);
}
.cf-tracklist-header {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 20px;
    padding: 0 8px;
}
.cf-tracklist-header h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
}
.cf-tracklist-count {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.4);
}
.cf-tracklist-grid {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 720px;
}
.cf-tracklist-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin: 0 -4px;
    padding: 0 4px 4px;
}
.cf-tracklist-row {
    display: grid;
    grid-template-columns: 52px minmax(140px, 1fr) <?php echo $show_bpm ? '64px ' : ''; ?><?php echo $show_key ? '72px ' : ''; ?>80px 96px 72px 72px 84px;
    align-items: center;
    gap: 20px;
    padding: 12px 10px;
    border-radius: 10px;
    transition: background 0.15s;
}
.cf-tracklist-head {
    padding: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 0;
    margin-bottom: 4px;
    gap: 20px;
}
.cf-tracklist-head .cf-col-index,
.cf-tracklist-head .cf-col-title,
.cf-tracklist-head .cf-col-bpm,
.cf-tracklist-head .cf-col-key,
.cf-tracklist-head .cf-col-views,
.cf-tracklist-head .cf-col-comments,
.cf-tracklist-head .cf-col-playlist,
.cf-tracklist-head .cf-col-like,
.cf-tracklist-head .cf-col-view {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.35);
    text-align: center;
}
.cf-tracklist-head .cf-col-title {
    text-align: left;
}
.cf-col-views,
.cf-col-comments,
.cf-col-playlist,
.cf-col-like,
.cf-col-view {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}
.cf-stat-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.5);
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
}
.cf-stat-chip .dashicons {
    font-size: 15px;
    width: 15px;
    height: 15px;
    color: rgba(255, 183, 0, 0.7);
}
.cf-stat-chip--link {
    text-decoration: none;
    color: rgba(255, 255, 255, 0.5);
    transition: color 0.15s;
}
.cf-stat-chip--link:hover {
    color: var(--primary-color, #FFB700);
}
.cf-album-share-wrap {
    margin-top: 20px;
}
.cf-album-ad-sidebar {
    margin-top: 24px;
}
.cf-ad-slot {
    margin: 0 auto;
    max-width: 100%;
    text-align: center;
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
    min-height: 90px;
    padding: 24px;
}
.cf-share-panel {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
}
.cf-share-label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.45);
}
.cf-share-buttons {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.cf-share-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.04);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s, transform 0.15s;
    padding: 0;
    color: #fff;
    text-decoration: none;
}
.cf-share-btn svg {
    width: 16px;
    height: 16px;
    fill: currentColor;
}
.cf-share-btn:hover {
    background: rgba(255, 183, 0, 0.12);
    border-color: rgba(255, 183, 0, 0.35);
    color: var(--primary-color, #FFB700);
    transform: translateY(-1px);
}
.cf-share-copy-btn.is-copied {
    background: rgba(255, 183, 0, 0.2);
    border-color: var(--primary-color, #FFB700);
    color: var(--primary-color, #FFB700);
}
.cf-album-track-row:hover {
    background: rgba(255, 255, 255, 0.04);
}
.cf-album-track-row.cf-queue-active,
.cf-album-track-row.cf-is-playing {
    background: rgba(255, 183, 0, 0.08);
}
.cf-album-track-row.cf-is-playing .cf-track-name {
    color: var(--primary-color, #FFB700);
}
.cf-col-index {
    text-align: center;
}
.cf-list-play-trigger {
    background: none;
    border: none;
    cursor: pointer;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
    color: rgba(255, 255, 255, 0.45);
    transition: color 0.15s, background 0.15s;
    padding: 0;
}
.cf-list-play-trigger:hover,
.cf-album-track-row.cf-is-playing .cf-list-play-trigger {
    color: var(--primary-color, #FFB700);
    background: rgba(255, 183, 0, 0.1);
}
.cf-track-num {
    font-size: 0.85rem;
    font-variant-numeric: tabular-nums;
    color: rgba(255, 255, 255, 0.35);
    transition: opacity 0.15s;
}
.cf-track-play-icon {
    position: absolute;
    width: 14px;
    height: 14px;
    opacity: 0;
    transition: opacity 0.15s;
}
.cf-list-play-trigger:hover .cf-track-num,
.cf-album-track-row.cf-is-playing .cf-track-num {
    opacity: 0;
}
.cf-list-play-trigger:hover .cf-track-play-icon,
.cf-album-track-row.cf-is-playing .cf-track-play-icon {
    opacity: 1;
}
.cf-track-num-static {
    display: inline-block;
    width: 36px;
    text-align: center;
    color: rgba(255, 255, 255, 0.25);
    font-size: 0.85rem;
}
.cf-track-name {
    display: block;
    color: #fff;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 2px;
    transition: color 0.15s;
}
.cf-track-name:hover {
    color: var(--primary-color, #FFB700);
}
.cf-track-artist-name {
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.38);
}
.cf-col-bpm,
.cf-col-key {
    font-size: 0.82rem;
    color: rgba(255, 255, 255, 0.45);
    text-align: center;
    font-variant-numeric: tabular-nums;
}
.cf-col-key {
    color: rgba(255, 183, 0, 0.75);
    font-weight: 600;
}
.cf-interaction-btn {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.35);
    cursor: pointer;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: color 0.15s, background 0.15s;
    padding: 0;
}
.cf-interaction-btn:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.06);
}
.cf-interaction-btn .dashicons {
    font-size: 18px;
    width: 18px;
    height: 18px;
}
.cf-like-btn.active {
    color: var(--primary-color, #FFB700) !important;
}
.cf-view-track-btn {
    font-size: 0.72rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.5);
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: background 0.15s, color 0.15s, border-color 0.15s;
    white-space: nowrap;
}
.cf-view-track-btn:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
    border-color: rgba(255, 255, 255, 0.2);
}
.cf-empty-tracklist-msg {
    text-align: center;
    color: rgba(255, 255, 255, 0.4);
    padding: 32px 16px;
    font-style: italic;
}
.cf-icon {
    display: block;
    background-color: currentColor;
    -webkit-mask-size: contain;
    mask-size: contain;
    -webkit-mask-repeat: no-repeat;
    mask-repeat: no-repeat;
    -webkit-mask-position: center;
    mask-position: center;
}
.cf-icon-play {
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M8 5v14l11-7z'/%3E%3C/svg%3E");
    mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M8 5v14l11-7z'/%3E%3C/svg%3E");
}
@media (max-width: 768px) {
    .cf-album-hero {
        grid-template-columns: 1fr;
        gap: 28px;
        text-align: center;
    }
    .cf-album-art-frame {
        max-width: 260px;
        margin: 0 auto;
    }
    .cf-album-description,
    .cf-album-meta-row,
    .cf-album-actions,
    .cf-album-share-wrap {
        justify-content: center;
    }
    .cf-album-share-wrap .cf-share-panel {
        justify-content: center;
    }
    .cf-album-description {
        margin-left: auto;
        margin-right: auto;
    }
    .cf-tracklist-row {
        grid-template-columns: 44px minmax(0, 1fr) 56px 56px 48px 48px 64px;
        gap: 10px;
        padding: 10px 6px;
    }
    .cf-tracklist-head .cf-col-bpm,
    .cf-tracklist-head .cf-col-key,
    .cf-col-bpm,
    .cf-col-key {
        display: none;
    }
    .cf-tracklist-head .cf-col-views,
    .cf-tracklist-head .cf-col-comments,
    .cf-tracklist-head .cf-col-playlist,
    .cf-tracklist-head .cf-col-like,
    .cf-tracklist-head .cf-col-view {
        font-size: 0.58rem;
    }
    .cf-stat-chip {
        font-size: 0.7rem;
        gap: 3px;
    }
    .cf-stat-chip .dashicons {
        font-size: 13px;
        width: 13px;
        height: 13px;
    }
    .cf-view-track-btn {
        padding: 5px 10px;
        font-size: 0.65rem;
    }
}
@media (min-width: 769px) and (max-width: 1024px) {
    .cf-album-hero {
        grid-template-columns: 220px 1fr;
        gap: 32px;
    }
    .cf-tracklist-row {
        gap: 10px;
        padding: 10px 6px;
    }
}
</style>

<?php get_footer(); ?>
