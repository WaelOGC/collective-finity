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

$cf_blog_available = max( 0, (int) $cf_article_count );
$cf_blog_limit     = min( 8, $cf_blog_available );

$cf_recent_posts = ( $cf_blog_limit > 0 )
    ? new WP_Query(
        array(
            'post_type'           => 'post',
            'posts_per_page'      => $cf_blog_limit,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
        )
    )
    : new WP_Query( array( 'post__in' => array( 0 ) ) );

/* Preload published tracks + articles for hero live search (homepage only). */
$cf_hero_search_items = array();
if ( $cf_track_count > 0 || $cf_article_count > 0 ) {
    $cf_hero_search_query = new WP_Query(
        array(
            'post_type'              => array( 'post', 'tracks' ),
            'posts_per_page'         => 200,
            'post_status'            => 'publish',
            'ignore_sticky_posts'    => true,
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'orderby'                => 'date',
            'order'                  => 'DESC',
        )
    );
    while ( $cf_hero_search_query->have_posts() ) {
        $cf_hero_search_query->the_post();
        $cf_sid   = get_the_ID();
        $cf_stype = ( 'tracks' === get_post_type( $cf_sid ) ) ? 'track' : 'article';
        $cf_excerpt = wp_trim_words( get_the_excerpt( $cf_sid ), 14, '&hellip;' );
        $cf_content = wp_strip_all_tags( get_post_field( 'post_content', $cf_sid ) );
        $cf_hero_search_items[] = array(
            'type'    => $cf_stype,
            'title'   => get_the_title( $cf_sid ),
            'excerpt' => $cf_excerpt,
            'url'     => get_permalink( $cf_sid ),
            'search'  => wp_trim_words( $cf_content, 40, '' ),
        );
    }
    wp_reset_postdata();
}

$cf_blog_url = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : '';

$cf_home_reviews = get_comments(
    array(
        'status'     => 'approve',
        'type'       => 'comment',
        'number'     => 3,
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
        <div class="cf-hero-center-glow" aria-hidden="true"></div>
        <div class="cf-hero-freq" aria-hidden="true"></div>
        <div class="cf-hero-eq" aria-hidden="true">
            <?php
            $cf_hero_eq_bars = array( 28, 52, 38, 72, 44, 86, 34, 64, 48, 90, 40, 58, 76, 32, 68, 46, 82, 36, 60, 74, 42, 88, 30, 56, 70, 50, 84, 38, 66, 44, 78, 54 );
            foreach ( $cf_hero_eq_bars as $cf_eq_i => $cf_eq_h ) :
                $cf_eq_dur   = 1.1 + ( ( $cf_eq_i % 7 ) * 0.18 );
                $cf_eq_delay = ( $cf_eq_i % 11 ) * 0.12;
                ?>
                <span
                    class="cf-hero-eq-bar"
                    style="--cf-eq-h: <?php echo esc_attr( (string) $cf_eq_h ); ?>%; --cf-eq-dur: <?php echo esc_attr( (string) $cf_eq_dur ); ?>s; --cf-eq-delay: -<?php echo esc_attr( (string) $cf_eq_delay ); ?>s;"
                ></span>
            <?php endforeach; ?>
        </div>
        <div class="cf-hero-inner">
            <span class="cf-hero-badge">&#10022; <?php esc_html_e( 'FF Collective', 'collective-finity' ); ?></span>
            <h1 class="cf-hero-title"><?php esc_html_e( 'Experience Music Beyond Imagination', 'collective-finity' ); ?></h1>
            <p class="cf-hero-tagline"><?php esc_html_e( 'A collective universe where artists, producers, and listeners converge in harmony.', 'collective-finity' ); ?></p>
            <div class="cf-hero-search" data-cf-hero-search>
                <label class="screen-reader-text" for="cf-hero-search-input"><?php esc_html_e( 'Search tracks and articles', 'collective-finity' ); ?></label>
                <div class="cf-hero-search-field">
                    <span class="cf-hero-search-icon" aria-hidden="true"><?php echo collective_finity_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                    <input
                        type="search"
                        id="cf-hero-search-input"
                        class="cf-hero-search-input"
                        placeholder="<?php esc_attr_e( 'Search tracks & articles…', 'collective-finity' ); ?>"
                        autocomplete="off"
                        aria-autocomplete="list"
                        aria-controls="cf-hero-search-results"
                        aria-expanded="false"
                    >
                </div>
                <div id="cf-hero-search-results" class="cf-hero-search-results" role="listbox" hidden></div>
            </div>
            <script type="application/json" id="cf-hero-search-data"><?php echo wp_json_encode( $cf_hero_search_items, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
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
            <div class="cf-scroll-row-wrap cf-scroll-row-wrap--blog cf-scroll-row-wrap--static">
                <div class="cf-scroll-row cf-scroll-row--blog">
                    <div class="cf-scroll-track">
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
        </div>

        <?php
        $cf_review_cards = array();
        foreach ( $cf_home_reviews as $cf_review_comment ) {
            $cf_review_post = get_post( (int) $cf_review_comment->comment_post_ID );
            if ( ! $cf_review_post ) {
                continue;
            }

            $cf_is_article  = ( 'post' === $cf_review_post->post_type );
            $cf_is_platform = ( 'page' === $cf_review_post->post_type && function_exists( 'collective_finity_is_faq_page' ) && collective_finity_is_faq_page( $cf_review_post ) );

            // Article reviews + FAQ platform reviews only.
            if ( ! $cf_is_article && ! $cf_is_platform ) {
                continue;
            }

            $cf_review_rating = (int) get_comment_meta( $cf_review_comment->comment_ID, 'cf_rating', true );
            if ( $cf_review_rating < 1 || $cf_review_rating > 5 ) {
                continue;
            }

            $cf_review_cards[] = array(
                'rating'  => $cf_review_rating,
                'excerpt' => wp_trim_words( wp_strip_all_tags( $cf_review_comment->comment_content ), 22, '&hellip;' ),
                'author'  => $cf_review_comment->comment_author,
                'url'     => get_comment_link( $cf_review_comment ),
                'source'  => $cf_is_platform ? 'platform' : 'article',
                'comment' => $cf_review_comment,
            );
        }
        ?>

        <?php if ( ! empty( $cf_review_cards ) ) : ?>
            <div class="cf-home-reviews-carousel" data-cf-reviews-carousel data-interval="8000">
                <div class="cf-home-reviews-grid" data-cf-reviews-track>
                    <?php foreach ( $cf_review_cards as $cf_index => $cf_card ) : ?>
                        <a class="cf-home-review-card" data-cf-review-index="<?php echo esc_attr( $cf_index ); ?>" href="<?php echo esc_url( $cf_card['url'] ); ?>">
                            <?php if ( 'platform' === $cf_card['source'] ) : ?>
                                <span class="cf-home-review-tag cf-home-review-tag--platform"><?php esc_html_e( 'PLATFORM', 'collective-finity' ); ?></span>
                            <?php else : ?>
                                <span class="cf-home-review-tag cf-home-review-tag--article"><?php esc_html_e( 'ARTICLE', 'collective-finity' ); ?></span>
                            <?php endif; ?>
                            <div class="cf-home-review-stars">
                                <?php echo collective_finity_stars_markup( $cf_card['rating'], 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </div>
                            <p class="cf-home-review-excerpt"><?php echo esc_html( $cf_card['excerpt'] ); ?></p>
                            <div class="cf-home-review-author">
                                <?php echo collective_finity_review_avatar( $cf_card['comment'], 36 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <span class="cf-home-review-author__name"><?php echo esc_html( $cf_card['author'] ); ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="cf-home-reviews-cta">
                <a class="cf-home-reviews-viewall" href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>">
                    <?php esc_html_e( 'View All Reviews', 'collective-finity' ); ?> &rarr;
                </a>
            </div>
        <?php else : ?>
            <div class="cf-home-empty"><?php esc_html_e( 'No reviews yet — be the first to share your thoughts on an article or the platform.', 'collective-finity' ); ?></div>
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
    .cf-home-redesign .cf-hero {
        background: #0b0b0b;
        border-color: rgba(30, 30, 30, 0.9);
    }

    .cf-home-redesign .cf-hero-glow,
    .cf-home-redesign .cf-hero-dots,
    .cf-home-redesign .cf-hero-orb {
        display: none;
    }

    .cf-home-redesign .cf-hero-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        width: 100%;
        position: relative;
        z-index: 1;
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

    .cf-home-redesign .cf-hero-center-glow {
        position: absolute;
        left: 50%;
        top: 48%;
        width: min(72%, 560px);
        aspect-ratio: 1;
        transform: translate(-50%, -50%);
        pointer-events: none;
        z-index: 0;
        border-radius: 50%;
        background: radial-gradient(
            circle,
            rgba(255, 183, 0, 0.16) 0%,
            rgba(255, 183, 0, 0.06) 38%,
            transparent 70%
        );
        animation: cfHeroCenterGlow 8.2s ease-in-out infinite;
    }

    @keyframes cfHeroCenterGlow {
        0%, 100% { opacity: 0.4; transform: translate(-50%, -50%) scale(0.88); }
        50% { opacity: 0.75; transform: translate(-50%, -50%) scale(1.06); }
    }

    .cf-home-redesign .cf-hero-freq {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background-image: repeating-linear-gradient(
            to bottom,
            transparent 0,
            transparent 9px,
            rgba(255, 183, 0, 0.14) 9px,
            rgba(255, 183, 0, 0.14) 10px
        );
        -webkit-mask-image: radial-gradient(ellipse 62% 52% at 50% 58%, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.5) 34%, transparent 74%);
        mask-image: radial-gradient(ellipse 62% 52% at 50% 58%, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.5) 34%, transparent 74%);
        opacity: 0.85;
    }

    .cf-home-redesign .cf-hero-eq {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 78px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 3px;
        padding: 0 4%;
        pointer-events: none;
        z-index: 0;
        -webkit-mask-image: linear-gradient(to top, #000 18%, transparent 100%);
        mask-image: linear-gradient(to top, #000 18%, transparent 100%);
    }

    .cf-home-redesign .cf-hero-eq-bar {
        display: block;
        flex: 1 1 0;
        max-width: 8px;
        min-width: 2px;
        height: var(--cf-eq-h, 40%);
        border-radius: 1px 1px 0 0;
        background: var(--cf-accent, #ffb700);
        opacity: 0.28;
        transform-origin: bottom center;
        animation: cfHeroEqPulse var(--cf-eq-dur, 1.6s) ease-in-out infinite;
        animation-delay: var(--cf-eq-delay, 0s);
    }

    @keyframes cfHeroEqPulse {
        0%, 100% { transform: scaleY(0.35); opacity: 0.16; }
        35% { transform: scaleY(1); opacity: 0.34; }
        65% { transform: scaleY(0.55); opacity: 0.22; }
    }

    .cf-home-redesign .cf-hero-badge {
        animation: cfHeroBadgePulse 2.4s ease-in-out infinite;
    }

    @keyframes cfHeroBadgePulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(255, 183, 0, 0.28); }
        50% { box-shadow: 0 0 0 10px rgba(255, 183, 0, 0); }
    }

    /* ---- Hero live search ---- */
    .cf-home-redesign .cf-hero-search {
        position: relative;
        width: min(100%, 520px);
        z-index: 2;
        animation: cfFadeUp 0.6s ease both;
        animation-delay: 0.2s;
    }

    .cf-home-redesign .cf-hero-search-field {
        position: relative;
        display: flex;
        align-items: center;
    }

    .cf-home-redesign .cf-hero-search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.4);
        display: flex;
        pointer-events: none;
    }

    .cf-home-redesign .cf-hero-search-input {
        width: 100%;
        padding: 14px 18px 14px 46px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.04);
        color: #fff;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
    }

    .cf-home-redesign .cf-hero-search-input::placeholder {
        color: rgba(255, 255, 255, 0.38);
    }

    .cf-home-redesign .cf-hero-search-input:focus {
        border-color: rgba(255, 183, 0, 0.55);
        background: rgba(255, 183, 0, 0.06);
        box-shadow: 0 0 24px rgba(255, 183, 0, 0.14);
    }

    .cf-home-redesign .cf-hero-search-results {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 8px);
        max-height: 320px;
        overflow-y: auto;
        margin: 0;
        padding: 8px;
        border-radius: 14px;
        border: 1px solid rgba(255, 183, 0, 0.22);
        background: rgba(14, 14, 14, 0.96);
        box-shadow: 0 18px 40px -16px rgba(0, 0, 0, 0.7);
        text-align: left;
        z-index: 5;
    }

    .cf-home-redesign .cf-hero-search-results[hidden] {
        display: none;
    }

    .cf-home-redesign .cf-hero-search-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        text-decoration: none;
        color: #fff;
        transition: background 0.15s ease;
    }

    .cf-home-redesign .cf-hero-search-item:hover,
    .cf-home-redesign .cf-hero-search-item:focus-visible,
    .cf-home-redesign .cf-hero-search-item.is-active {
        background: rgba(255, 183, 0, 0.1);
        outline: none;
    }

    .cf-home-redesign .cf-hero-search-type {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 64px;
        padding: 4px 8px;
        margin-top: 2px;
        border-radius: 999px;
        font-family: var(--cf-mono, 'Space Mono', monospace);
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .cf-home-redesign .cf-hero-search-type--track {
        background: rgba(255, 183, 0, 0.12);
        color: #ffb700;
        border: 1px solid rgba(255, 183, 0, 0.28);
    }

    .cf-home-redesign .cf-hero-search-type--article {
        background: rgba(120, 180, 255, 0.12);
        color: #8ec5ff;
        border: 1px solid rgba(120, 180, 255, 0.28);
    }

    .cf-home-redesign .cf-hero-search-body {
        min-width: 0;
        flex: 1;
    }

    .cf-home-redesign .cf-hero-search-title {
        font-size: 13.5px;
        font-weight: 600;
        line-height: 1.35;
        margin: 0 0 4px;
    }

    .cf-home-redesign .cf-hero-search-excerpt {
        margin: 0;
        font-size: 12px;
        line-height: 1.45;
        color: var(--cf-text-3, #888);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .cf-home-redesign .cf-hero-search-empty {
        padding: 16px 12px;
        font-size: 13px;
        color: var(--cf-text-3, #888);
        text-align: center;
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

    .cf-home-redesign .cf-scroll-row-wrap--static {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }

    .cf-home-redesign .cf-scroll-row-wrap--static .cf-scroll-track {
        animation: none !important;
        width: auto;
    }

    .cf-home-redesign .cf-scroll-row:hover .cf-scroll-track {
        animation-play-state: paused;
    }

    @keyframes cfScrollAlbums {
        from { transform: translateX(0); }
        to { transform: translateX(-50%); }
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

    .cf-home-redesign .cf-home-review-tag {
        align-self: flex-start;
        padding: 4px 10px;
        border-radius: 999px;
        font-family: var(--cf-mono, 'Space Mono', monospace);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.06em;
    }

    .cf-home-redesign .cf-home-review-tag--article {
        background: rgba(255, 183, 0, 0.12);
        color: #ffb700;
        border: 1px solid rgba(255, 183, 0, 0.28);
    }

    .cf-home-redesign .cf-home-review-tag--platform {
        background: rgba(255, 255, 255, 0.06);
        color: #e4e4e4;
        border: 1px solid rgba(255, 255, 255, 0.14);
    }

    .cf-home-redesign .cf-home-review-excerpt {
        margin: 0;
        font-size: 13.5px;
        line-height: 1.6;
        color: var(--cf-text-2, #b3b3b3);
        flex: 1;
    }

    .cf-home-redesign .cf-home-review-author {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 2px;
    }

    .cf-home-redesign .cf-home-review-author .cf-review-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .cf-home-redesign .cf-home-review-author .cf-review-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .cf-home-redesign .cf-home-review-author .cf-review-avatar--initial {
        background: linear-gradient(135deg, #FFB700, #8a6200);
        color: #0D0D0D;
        font-family: var(--cf-mono, 'Space Mono', monospace);
        font-weight: 700;
        font-size: 14px;
    }

    .cf-home-redesign .cf-home-review-author__name {
        font-size: 12px;
        font-weight: 600;
        color: #fff;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .cf-home-redesign .cf-home-review-stars .cf-star {
        color: #4a4a4a;
    }

    .cf-home-redesign .cf-home-review-stars .cf-star.is-on {
        color: var(--cf-accent, #ffb700);
    }

    .cf-home-redesign .cf-home-review-card.is-hidden {
        display: none;
    }

    @keyframes cfReviewFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes cfReviewSlideLeft {
        from { opacity: 0; transform: translateX(24px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes cfReviewSlideRight {
        from { opacity: 0; transform: translateX(-24px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes cfReviewScale {
        from { opacity: 0; transform: scale(0.92); }
        to { opacity: 1; transform: scale(1); }
    }

    .cf-home-redesign .cf-home-review-card.cf-anim-fade { animation: cfReviewFadeIn 0.5s ease both; }
    .cf-home-redesign .cf-home-review-card.cf-anim-slide-left { animation: cfReviewSlideLeft 0.5s ease both; }
    .cf-home-redesign .cf-home-review-card.cf-anim-slide-right { animation: cfReviewSlideRight 0.5s ease both; }
    .cf-home-redesign .cf-home-review-card.cf-anim-scale { animation: cfReviewScale 0.5s ease both; }

    @media (prefers-reduced-motion: reduce) {
        .cf-home-redesign .cf-home-review-card.cf-anim-fade,
        .cf-home-redesign .cf-home-review-card.cf-anim-slide-left,
        .cf-home-redesign .cf-home-review-card.cf-anim-slide-right,
        .cf-home-redesign .cf-home-review-card.cf-anim-scale {
            animation: none;
        }
    }

    .cf-home-redesign .cf-home-reviews-cta {
        margin-top: 20px;
        text-align: center;
    }

    .cf-home-redesign .cf-home-reviews-viewall {
        font-size: 13px;
        color: var(--cf-accent);
        text-decoration: none;
    }

    .cf-home-redesign .cf-home-reviews-viewall:hover {
        color: var(--cf-link-hover, #ffde99);
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
        .cf-home-redesign .cf-hero-center-glow,
        .cf-home-redesign .cf-hero-eq-bar,
        .cf-home-redesign .cf-hero-badge,
        .cf-home-redesign .cf-hero-search,
        .cf-home-redesign .cf-scroll-row-wrap .cf-scroll-track,
        .cf-home-redesign .cf-cta--animated {
            animation: none !important;
        }

        .cf-home-redesign .cf-hero-search-item {
            transition: none;
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

    body.cf-glow-disabled .cf-home-redesign .cf-hero-center-glow,
    body.cf-glow-disabled .cf-home-redesign .cf-hero-freq,
    body.cf-glow-disabled .cf-home-redesign .cf-hero-eq {
        display: none !important;
    }
</style>

<script>
(function () {
    var root = document.querySelector('[data-cf-hero-search]');
    var input = document.getElementById('cf-hero-search-input');
    var results = document.getElementById('cf-hero-search-results');
    var dataEl = document.getElementById('cf-hero-search-data');
    if (!root || !input || !results || !dataEl) {
        return;
    }

    var items = [];
    try {
        items = JSON.parse(dataEl.textContent || '[]');
    } catch (e) {
        items = [];
    }
    if (!Array.isArray(items)) {
        items = [];
    }

    var labels = {
        track: <?php echo wp_json_encode( __( 'Track', 'collective-finity' ) ); ?>,
        article: <?php echo wp_json_encode( __( 'Article', 'collective-finity' ) ); ?>,
        empty: <?php echo wp_json_encode( __( 'No results found', 'collective-finity' ) ); ?>
    };

    var activeIndex = -1;
    var debounceTimer = null;
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function normalize(str) {
        return String(str || '').toLowerCase();
    }

    function hideResults() {
        results.hidden = true;
        results.innerHTML = '';
        input.setAttribute('aria-expanded', 'false');
        activeIndex = -1;
    }

    function render(matches, query) {
        if (!query) {
            hideResults();
            return;
        }

        results.innerHTML = '';
        if (!matches.length) {
            var empty = document.createElement('div');
            empty.className = 'cf-hero-search-empty';
            empty.textContent = labels.empty;
            results.appendChild(empty);
            results.hidden = false;
            input.setAttribute('aria-expanded', 'true');
            activeIndex = -1;
            return;
        }

        matches.slice(0, 8).forEach(function (item, index) {
            var link = document.createElement('a');
            link.href = item.url;
            link.className = 'cf-hero-search-item';
            link.setAttribute('role', 'option');
            link.setAttribute('data-index', String(index));

            var type = document.createElement('span');
            type.className = 'cf-hero-search-type cf-hero-search-type--' + (item.type === 'track' ? 'track' : 'article');
            type.textContent = item.type === 'track' ? labels.track : labels.article;

            var body = document.createElement('div');
            body.className = 'cf-hero-search-body';

            var title = document.createElement('div');
            title.className = 'cf-hero-search-title';
            title.textContent = item.title || '';

            var excerpt = document.createElement('p');
            excerpt.className = 'cf-hero-search-excerpt';
            excerpt.textContent = item.excerpt || '';

            body.appendChild(title);
            body.appendChild(excerpt);
            link.appendChild(type);
            link.appendChild(body);
            results.appendChild(link);
        });

        results.hidden = false;
        input.setAttribute('aria-expanded', 'true');
        activeIndex = -1;
    }

    function search(query) {
        var q = normalize(query).trim();
        if (!q) {
            hideResults();
            return;
        }

        var matches = items.filter(function (item) {
            var hay = normalize(item.title) + ' ' + normalize(item.excerpt) + ' ' + normalize(item.search);
            return hay.indexOf(q) !== -1;
        });
        render(matches, q);
    }

    function setActive(next) {
        var options = results.querySelectorAll('.cf-hero-search-item');
        if (!options.length) {
            return;
        }
        if (activeIndex >= 0 && options[activeIndex]) {
            options[activeIndex].classList.remove('is-active');
        }
        activeIndex = (next + options.length) % options.length;
        options[activeIndex].classList.add('is-active');
        options[activeIndex].scrollIntoView({ block: 'nearest', behavior: reduceMotion ? 'auto' : 'smooth' });
    }

    input.addEventListener('input', function () {
        var value = input.value;
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(function () {
            search(value);
        }, reduceMotion ? 0 : 120);
    });

    input.addEventListener('keydown', function (e) {
        if (results.hidden) {
            return;
        }
        var options = results.querySelectorAll('.cf-hero-search-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActive(activeIndex + 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActive(activeIndex - 1);
        } else if (e.key === 'Enter' && activeIndex >= 0 && options[activeIndex]) {
            e.preventDefault();
            window.location.href = options[activeIndex].href;
        } else if (e.key === 'Escape') {
            hideResults();
            input.blur();
        }
    });

    document.addEventListener('click', function (e) {
        if (!root.contains(e.target)) {
            hideResults();
        }
    });
})();

(function () {
    var carousel = document.querySelector('[data-cf-reviews-carousel]');
    if (!carousel) {
        return;
    }

    var track = carousel.querySelector('[data-cf-reviews-track]');
    if (!track) {
        return;
    }

    var cards = track.querySelectorAll('.cf-home-review-card');
    if (!cards.length) {
        return;
    }

    if (cards.length <= 3) {
        return;
    }

    var interval = parseInt(carousel.getAttribute('data-interval'), 10) || 8000;
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var animClasses = ['cf-anim-fade', 'cf-anim-slide-left', 'cf-anim-slide-right', 'cf-anim-scale'];
    var currentGroup = 0;
    var animIndex = 0;
    var groups = [];
    var i;

    for (i = 0; i < cards.length; i += 3) {
        groups.push(Array.prototype.slice.call(cards, i, i + 3));
    }

    function clearAnimClasses(card) {
        animClasses.forEach(function (cls) {
            card.classList.remove(cls);
        });
    }

    function showGroup(groupIdx) {
        var animClass = reduceMotion ? null : animClasses[animIndex % animClasses.length];
        animIndex += 1;

        groups.forEach(function (group, g) {
            group.forEach(function (card) {
                if (g === groupIdx) {
                    card.classList.remove('is-hidden');
                    clearAnimClasses(card);
                    if (animClass) {
                        void card.offsetWidth;
                        card.classList.add(animClass);
                    }
                } else {
                    card.classList.add('is-hidden');
                    clearAnimClasses(card);
                }
            });
        });
    }

    showGroup(0);

    window.setInterval(function () {
        currentGroup = (currentGroup + 1) % groups.length;
        showGroup(currentGroup);
    }, interval);
})();
</script>

<?php get_footer(); ?>
