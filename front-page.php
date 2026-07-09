<?php
/**
 * Front Page (site homepage) for Collective Finity.
 *
 * Hero, Albums & Singles, From the Blog, Why Collective Finity, Reviews,
 * and CTA banner. Albums and blog posts use real WordPress data (WP_Query);
 * reviews pull from native comments with cf_rating meta.
 *
 * @package Collective_Finity
 */

get_header();

$cf_albums_url = get_post_type_archive_link( 'albums' );
$cf_tracks_url = get_post_type_archive_link( 'tracks' );
$cf_albums_url = $cf_albums_url ? $cf_albums_url : home_url( '/albums/' );
$cf_tracks_url = $cf_tracks_url ? $cf_tracks_url : home_url( '/tracks/' );
$cf_community_url = collective_finity_get_page_link( 'join-community', '/join-community/' );
$cf_contact_url   = collective_finity_get_page_link( array( 'contact', 'contact-us' ), '/contact/' );
$cf_about_url     = collective_finity_get_page_link( 'about', '/about/' );
$cf_about_pillars = trailingslashit( $cf_about_url ) . '#cf-about-pillars-heading';

$cf_album_count   = (int) wp_count_posts( 'albums' )->publish;
$cf_track_count   = (int) wp_count_posts( 'tracks' )->publish;
$cf_article_count = (int) wp_count_posts( 'post' )->publish;

$cf_featured_albums = new WP_Query(
    array(
        'post_type'      => 'albums',
        'posts_per_page' => 8,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    )
);

$cf_recent_posts = new WP_Query(
    array(
        'post_type'           => 'post',
        'posts_per_page'      => 8,
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    )
);

$cf_blog_url = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : '';

$cf_home_reviews = get_comments(
    array(
        'status'     => 'approve',
        'type'       => 'comment',
        'number'     => 24,
        'orderby'    => 'comment_date',
        'order'      => 'DESC',
        'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
            array(
                'key'     => 'cf_rating',
                'value'   => array( 1, 5 ),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            ),
        ),
    )
);

/**
 * Build inline cover style for a home-page album tile.
 *
 * @param int $album_id Album post ID.
 * @return string CSS declaration string for background.
 */
function collective_finity_home_album_cover_style( $album_id ) {
    $cf_cover = get_the_post_thumbnail_url( $album_id, 'large' );

    if ( empty( $cf_cover ) ) {
        $cf_track_ids = get_posts(
            array(
                'post_type'      => 'tracks',
                'posts_per_page' => 1,
                'post_status'    => 'publish',
                'fields'         => 'ids',
                'no_found_rows'  => true,
                'meta_query'     => array(
                    array(
                        'key'     => 'associated_album',
                        'value'   => $album_id,
                        'compare' => '=',
                    ),
                ),
            )
        );
        if ( ! empty( $cf_track_ids ) ) {
            $cf_cover = get_post_meta( $cf_track_ids[0], 'track_cover_url', true );
        }
    }

    if ( $cf_cover ) {
        return "background-image: url('" . esc_url( $cf_cover ) . "');";
    }

    return 'background: ' . esc_attr( collective_finity_gradient_for( $album_id ) ) . ';';
}
?>

<main id="cf-main-app-content" class="site-main cf-home cf-home-redesign" role="main">

    <!-- HERO -->
    <section class="cf-hero">
        <div class="cf-hero-orb cf-hero-orb--1" aria-hidden="true"></div>
        <div class="cf-hero-orb cf-hero-orb--2" aria-hidden="true"></div>
        <div class="cf-hero-dots cf-home-dotbg" aria-hidden="true"></div>
        <div class="cf-hero-inner">
            <span class="cf-hero-badge">&#10022; <?php esc_html_e( 'Music Beyond Imagination', 'collective-finity' ); ?></span>
            <h1 class="cf-hero-title"><?php esc_html_e( 'Where Sound Becomes ', 'collective-finity' ); ?><span class="cf-hero-cinema"><?php esc_html_e( 'Cinema', 'collective-finity' ); ?></span></h1>
            <p class="cf-hero-tagline"><?php esc_html_e( 'Collective Finity is a cinematic music universe pairing human songwriting with AI-assisted production — new releases, deep-dive articles, and a growing catalog built for late-night listening.', 'collective-finity' ); ?></p>
            <div class="cf-hero-actions">
                <a class="cf-btn-primary-lg cf-home-btn" href="<?php echo esc_url( $cf_tracks_url ); ?>"><?php esc_html_e( 'Start Listening', 'collective-finity' ); ?></a>
                <a class="cf-btn-ghost-lg cf-home-btn" href="<?php echo esc_url( $cf_albums_url ); ?>"><?php esc_html_e( 'Explore Albums', 'collective-finity' ); ?></a>
            </div>
            <div class="cf-hero-stats">
                <div class="cf-hero-stat">
                    <span class="cf-hero-stat-num"><?php echo esc_html( number_format_i18n( $cf_track_count ) ); ?></span>
                    <span class="cf-hero-stat-label"><?php esc_html_e( 'TRACKS', 'collective-finity' ); ?></span>
                </div>
                <div class="cf-hero-stat">
                    <span class="cf-hero-stat-num"><?php echo esc_html( number_format_i18n( $cf_album_count ) ); ?></span>
                    <span class="cf-hero-stat-label"><?php esc_html_e( 'ALBUMS', 'collective-finity' ); ?></span>
                </div>
                <div class="cf-hero-stat">
                    <span class="cf-hero-stat-num"><?php echo esc_html( number_format_i18n( $cf_article_count ) ); ?></span>
                    <span class="cf-hero-stat-label"><?php esc_html_e( 'ARTICLES', 'collective-finity' ); ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- ALBUMS & SINGLES -->
    <section class="cf-featured-albums">
        <div class="cf-section-head">
            <h2 class="cf-section-title"><?php esc_html_e( 'Albums & Singles', 'collective-finity' ); ?></h2>
            <a class="cf-section-link" href="<?php echo esc_url( $cf_albums_url ); ?>"><?php esc_html_e( 'View All', 'collective-finity' ); ?> &rarr;</a>
        </div>

        <?php if ( $cf_featured_albums->have_posts() ) : ?>
            <?php ob_start(); ?>
            <?php
            while ( $cf_featured_albums->have_posts() ) :
                $cf_featured_albums->the_post();
                $cf_id        = get_the_ID();
                $cf_art_style = collective_finity_home_album_cover_style( $cf_id );
                ?>
                <a class="cf-album-tile cf-home-scroll-card" href="<?php the_permalink(); ?>">
                    <div class="cf-album-tile-art-wrap">
                        <div class="cf-album-tile-art" style="<?php echo $cf_art_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></div>
                    </div>
                    <div class="cf-album-tile-title"><?php the_title(); ?></div>
                    <div class="cf-album-tile-artist"><?php echo esc_html( collective_finity_brand_name() ); ?></div>
                </a>
                <?php
            endwhile;
            wp_reset_postdata();
            $cf_album_tiles_html = ob_get_clean();
            ?>
            <div class="cf-scroll-row-wrap cf-scroll-row-wrap--albums">
                <div class="cf-scroll-row cf-scroll-row--albums">
                    <div class="cf-scroll-track">
                        <?php echo $cf_album_tiles_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <?php echo $cf_album_tiles_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="cf-home-empty"><?php esc_html_e( 'New albums are on the way — check back soon.', 'collective-finity' ); ?></div>
        <?php endif; ?>
    </section>

    <!-- FROM THE BLOG -->
    <?php if ( $cf_recent_posts->have_posts() ) : ?>
        <section class="cf-from-blog">
            <div class="cf-section-head">
                <h2 class="cf-section-title"><?php esc_html_e( 'From the Blog', 'collective-finity' ); ?></h2>
                <?php if ( $cf_blog_url ) : ?>
                    <a class="cf-section-link" href="<?php echo esc_url( $cf_blog_url ); ?>"><?php esc_html_e( 'View All', 'collective-finity' ); ?> &rarr;</a>
                <?php endif; ?>
            </div>

            <?php ob_start(); ?>
            <?php
            while ( $cf_recent_posts->have_posts() ) :
                $cf_recent_posts->the_post();
                $cf_pid   = get_the_ID();
                $cf_thumb = get_the_post_thumbnail_url( $cf_pid, 'medium_large' );
                $cf_cats  = get_the_category();
                $cf_cat   = ! empty( $cf_cats ) ? $cf_cats[0]->name : __( 'Article', 'collective-finity' );

                if ( $cf_thumb ) {
                    $cf_card_art = "background-image: url('" . esc_url( $cf_thumb ) . "');";
                } else {
                    $cf_card_art = 'background: ' . esc_attr( collective_finity_gradient_for( $cf_pid + 60 ) ) . ';';
                }
                ?>
                <a class="cf-blog-card cf-home-scroll-card" href="<?php the_permalink(); ?>">
                    <div class="cf-blog-card-art-wrap">
                        <div class="cf-blog-card-art" style="<?php echo $cf_card_art; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></div>
                    </div>
                    <div class="cf-blog-card-body">
                        <span class="cf-blog-card-cat"><?php echo esc_html( strtoupper( $cf_cat ) ); ?></span>
                        <div class="cf-blog-card-title"><?php the_title(); ?></div>
                        <div class="cf-blog-card-meta"><?php echo esc_html( get_the_date() ); ?></div>
                    </div>
                </a>
                <?php
            endwhile;
            wp_reset_postdata();
            $cf_blog_cards_html = ob_get_clean();
            ?>
            <div class="cf-scroll-row-wrap cf-scroll-row-wrap--blog">
                <div class="cf-scroll-row cf-scroll-row--blog">
                    <div class="cf-scroll-track">
                        <?php echo $cf_blog_cards_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <?php echo $cf_blog_cards_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- WHY COLLECTIVE FINITY -->
    <section class="cf-why">
        <div class="cf-why-head">
            <span class="cf-why-eyebrow"><?php esc_html_e( 'OUR FOUNDATION', 'collective-finity' ); ?></span>
            <h2 class="cf-why-title"><?php esc_html_e( 'Why Collective Finity', 'collective-finity' ); ?></h2>
        </div>
        <div class="cf-pillars">
            <div class="cf-pillar">
                <div class="cf-pillar-title"><?php esc_html_e( 'Cinematic Lyrics', 'collective-finity' ); ?></div>
                <div class="cf-pillar-desc"><?php esc_html_e( 'Original lyrics, written by hand, before a single note is synthesized.', 'collective-finity' ); ?></div>
                <a class="cf-pillar-more" href="<?php echo esc_url( $cf_about_pillars ); ?>"><?php esc_html_e( 'Read more', 'collective-finity' ); ?> <span aria-hidden="true">&rarr;</span></a>
            </div>
            <div class="cf-pillar">
                <div class="cf-pillar-title"><?php esc_html_e( 'Human Artistry', 'collective-finity' ); ?></div>
                <div class="cf-pillar-desc"><?php esc_html_e( 'Strict creative direction guiding every production instrument we use.', 'collective-finity' ); ?></div>
                <a class="cf-pillar-more" href="<?php echo esc_url( $cf_about_pillars ); ?>"><?php esc_html_e( 'Read more', 'collective-finity' ); ?> <span aria-hidden="true">&rarr;</span></a>
            </div>
            <div class="cf-pillar">
                <div class="cf-pillar-title"><?php esc_html_e( 'Sonic Innovation', 'collective-finity' ); ?></div>
                <div class="cf-pillar-desc"><?php esc_html_e( 'A limitless digital ecosystem where music and technology evolve together.', 'collective-finity' ); ?></div>
                <a class="cf-pillar-more" href="<?php echo esc_url( $cf_about_pillars ); ?>"><?php esc_html_e( 'Read more', 'collective-finity' ); ?> <span aria-hidden="true">&rarr;</span></a>
            </div>
        </div>
        <div class="cf-why-more">
            <a href="<?php echo esc_url( $cf_about_url ); ?>"><?php esc_html_e( 'Learn more about us', 'collective-finity' ); ?> &rarr;</a>
        </div>
    </section>

    <!-- REVIEWS -->
    <section class="cf-home-reviews" aria-labelledby="cf-home-reviews-heading">
        <div class="cf-home-reviews-head">
            <h2 id="cf-home-reviews-heading" class="cf-section-title"><?php esc_html_e( 'Reviews', 'collective-finity' ); ?></h2>
            <div class="cf-home-reviews-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Filter reviews', 'collective-finity' ); ?>">
                <button type="button" class="cf-home-reviews-tab is-active" role="tab" aria-selected="true" data-cf-review-filter="all"><?php esc_html_e( 'All', 'collective-finity' ); ?></button>
                <button type="button" class="cf-home-reviews-tab" role="tab" aria-selected="false" data-cf-review-filter="album"><?php esc_html_e( 'Albums', 'collective-finity' ); ?></button>
                <button type="button" class="cf-home-reviews-tab" role="tab" aria-selected="false" data-cf-review-filter="article"><?php esc_html_e( 'Articles', 'collective-finity' ); ?></button>
            </div>
        </div>

        <?php
        $cf_review_cards = array();
        foreach ( $cf_home_reviews as $cf_review_comment ) {
            $cf_review_post = get_post( (int) $cf_review_comment->comment_post_ID );
            if ( ! $cf_review_post || ! in_array( $cf_review_post->post_type, array( 'post', 'albums' ), true ) ) {
                continue;
            }

            $cf_review_rating = (int) get_comment_meta( $cf_review_comment->comment_ID, 'cf_rating', true );
            if ( $cf_review_rating < 1 || $cf_review_rating > 5 ) {
                continue;
            }

            $cf_review_type = ( 'albums' === $cf_review_post->post_type ) ? 'album' : 'article';
            $cf_review_cards[] = array(
                'type'    => $cf_review_type,
                'rating'  => $cf_review_rating,
                'excerpt' => wp_trim_words( wp_strip_all_tags( $cf_review_comment->comment_content ), 22, '&hellip;' ),
                'author'  => $cf_review_comment->comment_author,
                'url'     => get_comment_link( $cf_review_comment ),
            );
        }
        ?>

        <?php if ( ! empty( $cf_review_cards ) ) : ?>
            <div class="cf-home-reviews-grid" data-cf-reviews-grid>
                <?php foreach ( $cf_review_cards as $cf_card ) : ?>
                    <a class="cf-home-review-card" href="<?php echo esc_url( $cf_card['url'] ); ?>" data-cf-review-type="<?php echo esc_attr( $cf_card['type'] ); ?>">
                        <span class="cf-home-review-tag cf-home-review-tag--<?php echo esc_attr( $cf_card['type'] ); ?>">
                            <?php echo esc_html( 'album' === $cf_card['type'] ? __( 'ALBUM', 'collective-finity' ) : __( 'ARTICLE', 'collective-finity' ) ); ?>
                        </span>
                        <div class="cf-home-review-stars">
                            <?php echo collective_finity_stars_markup( $cf_card['rating'], 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                        <p class="cf-home-review-excerpt"><?php echo esc_html( $cf_card['excerpt'] ); ?></p>
                        <span class="cf-home-review-author"><?php echo esc_html( $cf_card['author'] ); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="cf-home-empty"><?php esc_html_e( 'No reviews yet — be the first to share your thoughts on an article.', 'collective-finity' ); ?></div>
        <?php endif; ?>
    </section>

    <!-- CTA BANNER -->
    <section class="cf-cta cf-cta--animated">
        <div class="cf-cta-dots cf-home-dotbg" aria-hidden="true"></div>
        <div class="cf-cta-body">
            <h2 class="cf-cta-title"><?php esc_html_e( 'Ready to join the collective?', 'collective-finity' ); ?></h2>
            <p class="cf-cta-sub"><?php esc_html_e( 'Get involved with a community of producers, creators, and sonic artists pushing the boundaries of sound.', 'collective-finity' ); ?></p>
        </div>
        <div class="cf-cta-actions">
            <a class="cf-btn-primary-lg cf-home-btn" href="<?php echo esc_url( $cf_community_url ); ?>"><?php esc_html_e( 'Join Community', 'collective-finity' ); ?></a>
            <a class="cf-cta-secondary" href="<?php echo esc_url( $cf_contact_url ); ?>"><?php esc_html_e( 'Contact Us', 'collective-finity' ); ?></a>
        </div>
    </section>

</main>

<style>
    /* ---- Hero motion & depth ---- */
    .cf-home-redesign .cf-hero-glow { display: none; }

    .cf-home-redesign .cf-hero-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        width: 100%;
    }

    .cf-home-redesign .cf-hero-title,
    .cf-home-redesign .cf-hero-tagline {
        text-align: center;
    }

    .cf-home-redesign .cf-hero-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        align-self: center;
        width: 100%;
        margin-inline: auto;
    }

    .cf-home-redesign .cf-hero-orb {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        filter: blur(72px);
        opacity: 0.55;
        z-index: 0;
    }

    .cf-home-redesign .cf-hero-orb--1 {
        width: 420px;
        height: 420px;
        left: 8%;
        top: 12%;
        background: radial-gradient(circle, rgba(255, 183, 0, 0.42) 0%, rgba(255, 140, 0, 0.18) 42%, transparent 72%);
        animation: cfHeroOrbFloat1 10s ease-in-out infinite;
    }

    .cf-home-redesign .cf-hero-orb--2 {
        width: 480px;
        height: 480px;
        right: 6%;
        bottom: 4%;
        background: radial-gradient(circle, rgba(255, 160, 40, 0.36) 0%, rgba(255, 100, 0, 0.14) 45%, transparent 70%);
        animation: cfHeroOrbFloat2 11s ease-in-out infinite;
    }

    @keyframes cfHeroOrbFloat1 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(28px, -22px) scale(1.08); }
    }

    @keyframes cfHeroOrbFloat2 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-24px, 18px) scale(1.06); }
    }

    .cf-home-redesign .cf-hero-badge {
        animation: cfHeroBadgePulse 2.4s ease-in-out infinite;
    }

    @keyframes cfHeroBadgePulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(255, 183, 0, 0.28); }
        50% { box-shadow: 0 0 0 10px rgba(255, 183, 0, 0); }
    }

    .cf-home-redesign .cf-hero-cinema {
        display: inline-block;
        background: linear-gradient(90deg, #ffb700 0%, #fff4cc 35%, #ff8c00 65%, #ffb700 100%);
        background-size: 200% auto;
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        -webkit-text-fill-color: transparent;
        animation: cfHeroCinemaShimmer 4s linear infinite;
    }

    @keyframes cfHeroCinemaShimmer {
        0% { background-position: 0% center; }
        100% { background-position: 200% center; }
    }

    .cf-home-redesign .cf-home-btn {
        transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }

    .cf-home-redesign .cf-home-btn:hover,
    .cf-home-redesign .cf-home-btn:focus-visible {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(255, 183, 0, 0.35);
    }

    .cf-home-redesign .cf-btn-ghost-lg.cf-home-btn:hover,
    .cf-home-redesign .cf-btn-ghost-lg.cf-home-btn:focus-visible {
        box-shadow: 0 8px 24px rgba(255, 183, 0, 0.18);
    }

    /* ---- Scrolling rows ---- */
    .cf-home-redesign .cf-scroll-row-wrap {
        overflow: hidden;
        margin: 0 -4px;
        padding: 4px;
    }

    .cf-home-redesign .cf-scroll-track {
        display: flex;
        gap: 18px;
        width: max-content;
        will-change: transform;
    }

    .cf-home-redesign .cf-scroll-row--albums .cf-scroll-track {
        animation: cfScrollAlbums 30s linear infinite;
    }

    .cf-home-redesign .cf-scroll-row--blog .cf-scroll-track {
        animation: cfScrollBlog 34s linear infinite;
    }

    .cf-home-redesign .cf-scroll-row:hover .cf-scroll-track {
        animation-play-state: paused;
    }

    @keyframes cfScrollAlbums {
        from { transform: translateX(0); }
        to { transform: translateX(-50%); }
    }

    @keyframes cfScrollBlog {
        from { transform: translateX(-50%); }
        to { transform: translateX(0); }
    }

    .cf-home-redesign .cf-album-tile.cf-home-scroll-card,
    .cf-home-redesign .cf-blog-card.cf-home-scroll-card {
        flex: 0 0 220px;
        min-width: 220px;
        max-width: 220px;
    }

    .cf-home-redesign .cf-blog-card.cf-home-scroll-card {
        flex-basis: 260px;
        min-width: 260px;
        max-width: 260px;
    }

    .cf-home-redesign .cf-album-tile-art-wrap,
    .cf-home-redesign .cf-blog-card-art-wrap {
        overflow: hidden;
        border-radius: 12px 12px 0 0;
    }

    .cf-home-redesign .cf-album-tile-art-wrap {
        border-radius: 12px;
        margin-bottom: 10px;
    }

    .cf-home-redesign .cf-album-tile-art,
    .cf-home-redesign .cf-blog-card-art {
        transition: transform 0.35s ease;
    }

    .cf-home-redesign .cf-album-tile:hover,
    .cf-home-redesign .cf-blog-card:hover {
        transform: translateY(-4px);
        border-color: rgba(255, 183, 0, 0.45);
    }

    .cf-home-redesign .cf-album-tile:hover .cf-album-tile-art,
    .cf-home-redesign .cf-blog-card:hover .cf-blog-card-art {
        transform: scale(1.06);
    }

    /* ---- Pillar read-more links ---- */
    .cf-home-redesign .cf-pillar {
        display: flex;
        flex-direction: column;
    }

    .cf-home-redesign .cf-pillar-more {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 14px;
        font-size: 13px;
        font-weight: 600;
        color: var(--cf-accent, #ffb700);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .cf-home-redesign .cf-pillar-more span {
        display: inline-block;
        transition: transform 0.2s ease;
    }

    .cf-home-redesign .cf-pillar-more:hover,
    .cf-home-redesign .cf-pillar-more:focus-visible {
        color: #ffde99;
    }

    .cf-home-redesign .cf-pillar-more:hover span,
    .cf-home-redesign .cf-pillar-more:focus-visible span {
        transform: translateX(4px);
    }

    /* ---- Reviews section ---- */
    .cf-home-redesign .cf-home-reviews-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .cf-home-redesign .cf-home-reviews-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .cf-home-redesign .cf-home-reviews-tab {
        padding: 8px 16px;
        border-radius: 999px;
        border: 1px solid rgba(255, 183, 0, 0.35);
        background: transparent;
        color: var(--cf-text-2, #b3b3b3);
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .cf-home-redesign .cf-home-reviews-tab:hover,
    .cf-home-redesign .cf-home-reviews-tab:focus-visible {
        color: #fff;
        border-color: rgba(255, 183, 0, 0.55);
    }

    .cf-home-redesign .cf-home-reviews-tab.is-active {
        background: var(--cf-accent, #ffb700);
        border-color: var(--cf-accent, #ffb700);
        color: #0d0d0d;
    }

    .cf-home-redesign .cf-home-reviews-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .cf-home-redesign .cf-home-review-card {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 20px;
        border: 1px solid var(--cf-border, #232323);
        border-radius: 12px;
        background: var(--cf-bg-card, #141414);
        text-decoration: none;
        color: #fff;
        transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    .cf-home-redesign .cf-home-review-card:hover,
    .cf-home-redesign .cf-home-review-card:focus-visible {
        transform: translateY(-3px);
        border-color: rgba(255, 183, 0, 0.35);
        box-shadow: 0 14px 28px -14px rgba(255, 183, 0, 0.2);
    }

    .cf-home-redesign .cf-home-review-card.is-hidden {
        display: none;
    }

    .cf-home-redesign .cf-home-review-tag {
        align-self: flex-start;
        padding: 4px 10px;
        border-radius: 999px;
        font-family: var(--cf-mono, 'Space Mono', monospace);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.06em;
    }

    .cf-home-redesign .cf-home-review-tag--album {
        background: rgba(120, 180, 255, 0.14);
        color: #8ec5ff;
        border: 1px solid rgba(120, 180, 255, 0.28);
    }

    .cf-home-redesign .cf-home-review-tag--article {
        background: rgba(255, 183, 0, 0.12);
        color: #ffb700;
        border: 1px solid rgba(255, 183, 0, 0.28);
    }

    .cf-home-redesign .cf-home-review-excerpt {
        margin: 0;
        font-size: 13.5px;
        line-height: 1.6;
        color: var(--cf-text-2, #b3b3b3);
        flex: 1;
    }

    .cf-home-redesign .cf-home-review-author {
        font-size: 12px;
        font-weight: 600;
        color: #fff;
    }

    .cf-home-redesign .cf-home-review-stars .cf-star {
        color: #4a4a4a;
    }

    .cf-home-redesign .cf-home-review-stars .cf-star.is-on {
        color: var(--cf-accent, #ffb700);
    }

    /* ---- CTA animated gradient ---- */
    .cf-home-redesign .cf-cta--animated {
        background: linear-gradient(135deg, rgba(255, 183, 0, 0.2), rgba(255, 120, 0, 0.06), rgba(255, 183, 0, 0.14), rgba(255, 80, 0, 0.04));
        background-size: 200% 200%;
        animation: cfCtaGradient 6s ease-in-out infinite;
    }

    @keyframes cfCtaGradient {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    /* ---- Mobile / tablet: manual swipe instead of auto-scroll ---- */
    @media (max-width: 1023px) {
        .cf-home-redesign .cf-scroll-row-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        .cf-home-redesign .cf-scroll-row-wrap .cf-scroll-track {
            animation: none !important;
            width: auto;
        }

        .cf-home-redesign .cf-home-reviews-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .cf-home-redesign .cf-home-reviews-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .cf-home-redesign .cf-hero-orb,
        .cf-home-redesign .cf-hero-badge,
        .cf-home-redesign .cf-hero-cinema,
        .cf-home-redesign .cf-scroll-row-wrap .cf-scroll-track,
        .cf-home-redesign .cf-cta--animated {
            animation: none !important;
        }

        .cf-home-redesign .cf-hero-cinema {
            background: none;
            -webkit-text-fill-color: var(--cf-accent, #ffb700);
            color: var(--cf-accent, #ffb700);
        }

        .cf-home-redesign .cf-scroll-row-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .cf-home-redesign .cf-scroll-row-wrap .cf-scroll-track {
            width: auto;
        }

        .cf-home-redesign .cf-home-btn:hover,
        .cf-home-redesign .cf-home-btn:focus-visible {
            transform: none;
        }
    }
</style>

<script>
(function () {
    var tabs = document.querySelectorAll('[data-cf-review-filter]');
    var grid = document.querySelector('[data-cf-reviews-grid]');
    if (!tabs.length || !grid) {
        return;
    }

    var cards = grid.querySelectorAll('[data-cf-review-type]');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var filter = tab.getAttribute('data-cf-review-filter');

            tabs.forEach(function (btn) {
                var active = btn === tab;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            cards.forEach(function (card) {
                var type = card.getAttribute('data-cf-review-type');
                var show = filter === 'all' || type === filter;
                card.classList.toggle('is-hidden', !show);
            });
        });
    });
})();
</script>

<?php get_footer(); ?>
