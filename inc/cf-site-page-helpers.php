<?php
/**
 * Shared helpers for Contact, Community, Blog, and Account page templates.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Blog category descriptions from the design reference.
 *
 * @return array<string, string>
 */
function cf_get_blog_category_descriptions() {
    return array(
        'AI Music Production'   => 'How AI tools are reshaping the process of writing, producing, and finishing tracks.',
        'AI Music Tutorials'      => 'Step-by-step guides for producing music with AI tools, from prompt engineering to full track generation.',
        'Audio Production'        => 'Mixing, mastering, and recording fundamentals for producers at every level.',
        'Industry & Resources'    => 'Licensing, distribution, and the business side of releasing independent music.',
        'Insights'                => 'Perspectives on where listener taste and music technology are headed next.',
        'Music Theory'            => 'Chords, structure, and the building blocks behind memorable songwriting.',
        'Prompt Engineering'      => 'Getting better, more controllable results out of generative music tools.',
    );
}

/**
 * Blog hub category chip order from the design reference.
 *
 * @return string[]
 */
function cf_get_blog_hub_categories() {
    return array(
        'AI Music Production',
        'AI Music Tutorials',
        'Audio Production',
        'Industry & Resources',
        'Insights',
        'Music Theory',
        'Prompt Engineering',
    );
}

/**
 * Estimated read time label.
 *
 * @param string $content Post content.
 */
function cf_get_read_time_label( $content ) {
    $word_count = str_word_count( wp_strip_all_tags( $content ) );
    $minutes    = max( 1, (int) ceil( $word_count / 220 ) );

    return sprintf(
        /* translators: %d: number of minutes */
        _n( '%d min read', '%d min read', $minutes, 'collective-finity' ),
        $minutes
    );
}

/**
 * Cover URL for a track or album release card.
 *
 * @param int    $post_id   Post ID.
 * @param string $post_type tracks|albums.
 */
function cf_get_release_cover_url( $post_id, $post_type ) {
    if ( 'tracks' === $post_type ) {
        $cover = get_post_meta( $post_id, 'track_cover_url', true );
        if ( $cover ) {
            return $cover;
        }
        $album_id = (int) get_post_meta( $post_id, 'associated_album', true );
        if ( $album_id ) {
            $thumb = get_the_post_thumbnail_url( $album_id, 'medium_large' );
            if ( $thumb ) {
                return $thumb;
            }
        }
    } else {
        $thumb = get_the_post_thumbnail_url( $post_id, 'medium_large' );
        if ( $thumb ) {
            return $thumb;
        }
        $tracks_query = new WP_Query(
            array(
                'post_type'      => 'tracks',
                'posts_per_page' => 1,
                'post_status'    => 'publish',
                'meta_query'     => array(
                    array(
                        'key'     => 'associated_album',
                        'value'   => $post_id,
                        'compare' => '=',
                    ),
                ),
            )
        );
        if ( $tracks_query->have_posts() ) {
            $tracks_query->the_post();
            $cover = get_post_meta( get_the_ID(), 'track_cover_url', true );
            wp_reset_postdata();
            if ( $cover ) {
                return $cover;
            }
        }
        wp_reset_postdata();
    }

    return collective_finity_default_art_url();
}

/**
 * Artist label for a release card.
 *
 * @param int    $post_id   Post ID.
 * @param string $post_type tracks|albums.
 */
function cf_get_release_artist_label( $post_id, $post_type ) {
    if ( 'tracks' === $post_type ) {
        $artist = get_post_meta( $post_id, 'track_artist', true );
        if ( $artist ) {
            return $artist;
        }
        $terms = get_the_terms( $post_id, 'track_artist' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            return $terms[0]->name;
        }
    }

    return collective_finity_brand_name();
}

/**
 * Release type pill label.
 *
 * @param int    $post_id   Post ID.
 * @param string $post_type tracks|albums.
 */
function cf_get_release_type_label( $post_id, $post_type ) {
    if ( 'albums' === $post_type ) {
        return __( 'Album', 'collective-finity' );
    }

    $release_type = get_post_meta( $post_id, 'track_release_type', true );
    if ( 'album_track' === $release_type ) {
        return __( 'Album Track', 'collective-finity' );
    }

    return __( 'Single', 'collective-finity' );
}

/**
 * Latest tracks + albums merged by date.
 *
 * @param int $limit Number of releases.
 * @return array<int, array<string, mixed>>
 */
function cf_get_latest_releases( $limit = 5 ) {
    $releases = array();

    $tracks = get_posts(
        array(
            'post_type'      => 'tracks',
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );

    foreach ( $tracks as $track ) {
        $releases[] = array(
            'id'        => $track->ID,
            'post_type' => 'tracks',
            'title'     => get_the_title( $track ),
            'permalink' => get_permalink( $track ),
            'timestamp' => strtotime( $track->post_date ),
        );
    }

    $albums = get_posts(
        array(
            'post_type'      => 'albums',
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );

    foreach ( $albums as $album ) {
        $releases[] = array(
            'id'        => $album->ID,
            'post_type' => 'albums',
            'title'     => get_the_title( $album ),
            'permalink' => get_permalink( $album ),
            'timestamp' => strtotime( $album->post_date ),
        );
    }

    usort(
        $releases,
        function ( $a, $b ) {
            return $b['timestamp'] <=> $a['timestamp'];
        }
    );

    return array_slice( $releases, 0, $limit );
}

/**
 * Theme option URL with fallback.
 *
 * @param string $option_key Theme option key.
 * @param string $fallback   Fallback URL.
 */
function cf_get_theme_social_url( $option_key, $fallback = '#' ) {
    $options = collective_finity_get_theme_options();
    if ( ! empty( $options[ $option_key ] ) ) {
        return $options[ $option_key ];
    }

    return $fallback;
}

/**
 * Render gated auth panel.
 *
 * @param string $icon      Dashicon slug fragment.
 * @param string $heading   Panel heading.
 */
function cf_render_gated_panel( $icon, $heading ) {
    $login_url = home_url( '/cf-login/' );
    ?>
    <div class="cf-gated-panel">
        <div class="cf-gated-panel__icon" aria-hidden="true">
            <span class="dashicons dashicons-<?php echo esc_attr( $icon ); ?>"></span>
        </div>
        <h2 class="cf-gated-panel__title"><?php echo esc_html( $heading ); ?></h2>
        <p class="cf-gated-panel__text"><?php esc_html_e( 'Sign in to your Collective Finity account to keep this personalized.', 'collective-finity' ); ?></p>
        <a class="cf-btn cf-btn--primary" href="<?php echo esc_url( $login_url ); ?>"><?php esc_html_e( 'Log In', 'collective-finity' ); ?></a>
    </div>
    <?php
}

/**
 * Liked album IDs for the current user.
 *
 * @return int[]
 */
function cf_get_user_liked_album_ids() {
    if ( ! is_user_logged_in() ) {
        return array();
    }

    $liked = get_user_meta( get_current_user_id(), '_cf_liked_albums', true );
    return is_array( $liked ) ? array_map( 'intval', $liked ) : array();
}

/**
 * Liked track IDs for the current user.
 *
 * @return int[]
 */
function cf_get_user_liked_track_ids() {
    if ( ! is_user_logged_in() ) {
        return array();
    }

    $liked = get_user_meta( get_current_user_id(), '_cf_liked_tracks', true );
    return is_array( $liked ) ? array_map( 'intval', $liked ) : array();
}

/**
 * Published liked track posts for the current user.
 *
 * @return WP_Post[]
 */
function cf_get_user_liked_track_posts() {
    $ids = cf_get_user_liked_track_ids();
    if ( empty( $ids ) ) {
        return array();
    }

    return get_posts(
        array(
            'post_type'      => 'tracks',
            'post__in'       => $ids,
            'orderby'        => 'post__in',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        )
    );
}

/**
 * Published liked album posts for the current user.
 *
 * @return WP_Post[]
 */
function cf_get_user_liked_album_posts() {
    $ids = cf_get_user_liked_album_ids();
    if ( empty( $ids ) ) {
        return array();
    }

    return get_posts(
        array(
            'post_type'      => 'albums',
            'post__in'       => $ids,
            'orderby'        => 'post__in',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        )
    );
}

/**
 * Human-readable relative time label.
 *
 * @param int $timestamp Unix timestamp.
 */
function cf_get_relative_time_label( $timestamp ) {
    $diff = time() - (int) $timestamp;
    if ( $diff < HOUR_IN_SECONDS ) {
        $mins = max( 1, (int) floor( $diff / MINUTE_IN_SECONDS ) );
        return sprintf( _n( '%d min ago', '%d mins ago', $mins, 'collective-finity' ), $mins );
    }
    if ( $diff < DAY_IN_SECONDS ) {
        $hours = max( 1, (int) floor( $diff / HOUR_IN_SECONDS ) );
        return sprintf( _n( '%d hour ago', '%d hours ago', $hours, 'collective-finity' ), $hours );
    }
    if ( $diff < WEEK_IN_SECONDS ) {
        $days = max( 1, (int) floor( $diff / DAY_IN_SECONDS ) );
        return sprintf( _n( '%d day ago', '%d days ago', $days, 'collective-finity' ), $days );
    }

    return date_i18n( get_option( 'date_format' ), $timestamp );
}

/**
 * Member since label for a user.
 *
 * @param int $user_id User ID.
 */
function cf_get_member_since_label( $user_id ) {
    $user = get_userdata( $user_id );
    if ( ! $user ) {
        return '';
    }

    return sprintf(
        /* translators: %s: month and year */
        __( 'Member since %s', 'collective-finity' ),
        date_i18n( 'F Y', strtotime( $user->user_registered ) )
    );
}

/**
 * Render liked tracks list section.
 */
function cf_render_liked_tracks_section() {
    $tracks = cf_get_user_liked_track_posts();
    ?>
    <section class="cf-profile-section" aria-labelledby="cf-liked-tracks-heading">
        <h2 id="cf-liked-tracks-heading" class="cf-form-section__title"><?php esc_html_e( 'Liked Tracks', 'collective-finity' ); ?></h2>
        <?php if ( empty( $tracks ) ) : ?>
            <p class="cf-empty-state"><?php esc_html_e( 'No liked tracks yet. Heart a track while listening to save it here.', 'collective-finity' ); ?></p>
        <?php else : ?>
            <div class="cf-track-list">
                <?php foreach ( $tracks as $track ) : ?>
                    <?php
                    $album_id   = (int) get_post_meta( $track->ID, 'associated_album', true );
                    $album_name = $album_id ? get_the_title( $album_id ) : '';
                    $artist     = cf_get_release_artist_label( $track->ID, 'tracks' );
                    ?>
                    <a class="cf-track-row" href="<?php echo esc_url( get_permalink( $track ) ); ?>">
                        <span class="cf-track-row__icon" aria-hidden="true"><span class="dashicons dashicons-heart"></span></span>
                        <span class="cf-track-row__main">
                            <span class="cf-track-row__title"><?php echo esc_html( get_the_title( $track ) ); ?></span>
                            <span class="cf-track-row__sub"><?php echo esc_html( $artist ); ?></span>
                        </span>
                        <?php if ( $album_name ) : ?>
                            <span class="cf-track-row__album"><?php echo esc_html( $album_name ); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    <?php
}

/**
 * Render liked albums grid section.
 */
function cf_render_liked_albums_section() {
    $albums = cf_get_user_liked_album_posts();
    ?>
    <section class="cf-profile-section" aria-labelledby="cf-liked-albums-heading">
        <h2 id="cf-liked-albums-heading" class="cf-form-section__title"><?php esc_html_e( 'Liked Albums', 'collective-finity' ); ?></h2>
        <?php if ( empty( $albums ) ) : ?>
            <p class="cf-empty-state"><?php esc_html_e( 'No liked albums yet.', 'collective-finity' ); ?></p>
        <?php else : ?>
            <div class="cf-album-mini-grid">
                <?php foreach ( $albums as $album ) : ?>
                    <?php $cover = cf_get_release_cover_url( $album->ID, 'albums' ); ?>
                    <a class="cf-album-mini-card" href="<?php echo esc_url( get_permalink( $album ) ); ?>">
                        <div class="cf-album-mini-card__art">
                            <img src="<?php echo esc_url( $cover ); ?>" alt="" loading="lazy" decoding="async">
                        </div>
                        <h3 class="cf-album-mini-card__title"><?php echo esc_html( get_the_title( $album ) ); ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    <?php
}

/**
 * Render personal playlists grid.
 */
function cf_render_user_playlists_section() {
    $playlists = cf_get_user_playlists();
    ?>
    <section class="cf-profile-section" aria-labelledby="cf-playlists-heading">
        <div class="cf-section-heading-row">
            <h2 id="cf-playlists-heading" class="cf-form-section__title"><?php esc_html_e( 'Personal Playlists', 'collective-finity' ); ?></h2>
            <button type="button" class="cf-btn cf-btn--primary cf-btn--sm" data-cf-open-playlist-modal>
                <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
                <?php esc_html_e( 'Create Playlist', 'collective-finity' ); ?>
            </button>
        </div>
        <?php if ( empty( $playlists ) ) : ?>
            <p class="cf-empty-state"><?php esc_html_e( 'No playlists yet. Create one to start curating tracks.', 'collective-finity' ); ?></p>
        <?php else : ?>
            <div class="cf-playlist-grid">
                <?php foreach ( $playlists as $playlist ) : ?>
                    <article class="cf-playlist-card">
                        <div class="cf-playlist-card__art" aria-hidden="true">
                            <span class="dashicons dashicons-playlist-audio"></span>
                        </div>
                        <div class="cf-playlist-card__body">
                            <h3 class="cf-playlist-card__title"><?php echo esc_html( $playlist['name'] ?? __( 'Playlist', 'collective-finity' ) ); ?></h3>
                            <p class="cf-playlist-card__meta">
                                <?php
                                $count = isset( $playlist['tracks'] ) && is_array( $playlist['tracks'] ) ? count( $playlist['tracks'] ) : 0;
                                printf(
                                    esc_html( _n( '%d track', '%d tracks', $count, 'collective-finity' ) ),
                                    (int) $count
                                );
                                ?>
                            </p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    <?php
}

/**
 * User playlists stored in user meta.
 *
 * @return array<int, array<string, mixed>>
 */
function cf_get_user_playlists() {
    if ( ! is_user_logged_in() ) {
        return array();
    }

    $playlists = get_user_meta( get_current_user_id(), '_cf_user_playlists', true );
    return is_array( $playlists ) ? $playlists : array();
}

/**
 * Listening history entries.
 *
 * @return array<int, array<string, mixed>>
 */
function cf_get_user_listening_history() {
    if ( ! is_user_logged_in() ) {
        return array();
    }

    $history = get_user_meta( get_current_user_id(), '_cf_listening_history', true );
    return is_array( $history ) ? $history : array();
}

/**
 * Inject in-content ad zones after every few H2 headings.
 *
 * @param string $content Post content.
 */
function cf_inject_post_content_ad_zones( $content ) {
    if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    if ( false === strpos( $content, '<h2' ) ) {
        return $content;
    }

    $h2_count   = 0;
    $ad_counter = 0;

    return preg_replace_callback(
        '/<\/h2>/i',
        function ( $matches ) use ( &$h2_count, &$ad_counter ) {
            $h2_count++;
            $html = $matches[0];

            if ( 0 === $h2_count % 4 ) {
                $ad_counter++;
                ob_start();
                if ( function_exists( 'collective_finity_ad_slot' ) ) {
                    echo '<div class="cf-post-ad-zone">';
                    collective_finity_ad_slot( 'post_incontent' );
                    echo '</div>';
                } else {
                    echo '<div class="cf-post-ad-zone cf-post-ad-zone--preview">AD ZONE — 728×90</div>';
                }
                $html .= ob_get_clean();
            }

            return $html;
        },
        $content
    );
}
add_filter( 'the_content', 'cf_inject_post_content_ad_zones', 20 );

/**
 * Add IDs to post H2 headings for TOC anchors.
 *
 * @param string $content Post content.
 */
function cf_add_heading_ids_for_toc( $content ) {
    if ( ! is_singular( 'post' ) ) {
        return $content;
    }

    $index = 0;
    return preg_replace_callback(
        '/<h2([^>]*)>(.*?)<\/h2>/i',
        function ( $matches ) use ( &$index ) {
            $index++;
            $attrs = $matches[1];
            if ( false !== stripos( $attrs, 'id=' ) ) {
                return $matches[0];
            }
            $id = 'cf-sec-' . $index;
            return '<h2' . $attrs . ' id="' . esc_attr( $id ) . '">' . $matches[2] . '</h2>';
        },
        $content
    );
}
add_filter( 'the_content', 'cf_add_heading_ids_for_toc', 15 );
