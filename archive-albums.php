<?php
/**
 * Template Name: Albums Archive
 * Description: Displays all albums in a polished collection layout.
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="albums-header">
            <p class="albums-header-kicker">Collective Finity</p>
            <h1><?php _e( 'Albums & Collections', 'collective-finity' ); ?></h1>
            <p class="albums-header-copy">
                <?php _e( 'Explore our cinematic music collections, each telling a unique story through sound.', 'collective-finity' ); ?>
            </p>
        </div>

        <div class="albums-grid-container">
            <?php if ( have_posts() ) : ?>
                <div class="albums-grid">
                    <?php while ( have_posts() ) : the_post();
                        $album_id = get_the_ID();
                        $album_permalink = get_permalink( $album_id );
                        $cover_url = get_the_post_thumbnail_url( $album_id, 'full' );

                        $tracks_query = new WP_Query( array(
                            'post_type'      => 'tracks',
                            'posts_per_page' => 1,
                            'post_status'    => 'publish',
                            'meta_query'     => array(
                                array(
                                    'key'     => 'associated_album',
                                    'value'   => $album_id,
                                    'compare' => '='
                                )
                            )
                        ) );

                        if ( empty( $cover_url ) && $tracks_query->have_posts() ) {
                            while ( $tracks_query->have_posts() ) {
                                $tracks_query->the_post();
                                $first_track_cover = get_post_meta( get_the_ID(), 'track_cover_url', true );
                                if ( ! empty( $first_track_cover ) ) {
                                    $cover_url = $first_track_cover;
                                    break;
                                }
                            }
                        }
                        wp_reset_postdata();

                        if ( empty( $cover_url ) ) {
                            $cover_url = collective_finity_default_art_url();
                        }
                    ?>
                        <article class="album-card">
                            <a href="<?php echo esc_url( $album_permalink ); ?>" class="album-card-link">
                                <div class="album-cover-wrapper">
                                    <img src="<?php echo esc_url( $cover_url ); ?>" alt="<?php the_title_attribute(); ?>">
                                    <div class="album-overlay"></div>
                                </div>
                                <div class="album-info">
                                    <h3><?php the_title(); ?></h3>
                                    <span><?php _e( 'View Collection', 'collective-finity' ); ?> →</span>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>

                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="albums-empty-state">
                    <span>🎵</span>
                    <h2><?php _e( 'No Albums Yet', 'collective-finity' ); ?></h2>
                    <p><?php _e( 'Check back soon for new music collections.', 'collective-finity' ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<style>
    .albums-header {
        text-align: center;
        padding: 90px 20px 40px;
        background: linear-gradient(180deg, rgba(255, 183, 0, 0.08) 0%, transparent 100%);
    }
    .albums-header-kicker {
        margin: 0 0 8px;
        color: var(--primary-color, #FFB700);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.24em;
        text-transform: uppercase;
    }
    .albums-header h1 {
        font-size: clamp(28px, 3.2vw, 42px);
        font-weight: 700;
        color: #fff;
        margin: 0 0 12px;
    }
    .albums-header-copy {
        font-size: 15px;
        color: #9a9a9a;
        max-width: 640px;
        margin: 0 auto;
        line-height: 1.7;
    }
    .albums-grid-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px 70px;
    }
    .albums-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        margin-top: 24px;
    }
    .album-card {
        background: linear-gradient(180deg, rgba(17, 17, 17, 0.95), rgba(10, 10, 10, 0.95));
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.25);
    }
    .album-card:hover {
        transform: translateY(-6px);
        border-color: rgba(255, 183, 0, 0.4);
        box-shadow: 0 16px 42px rgba(0, 0, 0, 0.4);
    }
    .album-card-link {
        display: block;
        text-decoration: none;
    }
    .album-cover-wrapper {
        position: relative;
        width: 100%;
        aspect-ratio: 1 / 1;
        background-color: #0d0d0d;
        overflow: hidden;
    }
    .album-cover-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
        display: block;
    }
    .album-card:hover .album-cover-wrapper img {
        transform: scale(1.04);
    }
    .album-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.08) 0%, rgba(0,0,0,0.75) 100%);
    }
    .album-info {
        padding: 20px;
    }
    .album-info h3 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .album-info span {
        font-size: 12px;
        color: var(--primary-color, #FFB700);
        letter-spacing: 0.08em;
        text-transform: uppercase;
        font-weight: 700;
    }
    .albums-empty-state {
        text-align: center;
        padding: 80px 20px;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
        background: rgba(255,255,255,0.02);
    }
    .albums-empty-state span {
        font-size: 48px;
        display: block;
        margin-bottom: 18px;
    }
    .albums-empty-state h2 {
        color: #fff;
        margin: 0 0 10px;
        font-size: 24px;
    }
    .albums-empty-state p {
        color: #888;
        margin: 0;
        line-height: 1.7;
    }
</style>

<?php get_footer(); ?>