<?php
/**
 * Single blog post template.
 *
 * Translated from design-reference/Blog-Post.dc.html: read-progress bar, breadcrumb,
 * category tag, title + author/meta, sticky auto-generated Table of Contents, featured
 * image, styled body (tables / pull-quotes / callout boxes), author bio, newsletter
 * (CF7 shortcode), related articles, and a Ratings & Comments section built on
 * WordPress' native comments (star rating stored as the 'cf_rating' comment meta).
 *
 * Named single-post.php so it only affects the 'post' post type and leaves the
 * generic single.php (used by other post types) untouched.
 *
 * @package Collective_Finity
 */

get_header();

while ( have_posts() ) :
    the_post();

    $cf_post_id   = get_the_ID();
    $cf_cat       = collective_finity_post_primary_category( $cf_post_id );
    $cf_cat_name  = $cf_cat ? $cf_cat->name : __( 'Article', 'collective-finity' );
    $cf_read_time = collective_finity_reading_time( $cf_post_id );
    $cf_blog_url  = collective_finity_get_page_link( 'blog', '/blog/' );
    // Category links route back to the Blog Hub filtered by category (in-place
    // filtering model) rather than the native category archive.
    $cf_cat_link  = $cf_cat ? add_query_arg( 'blog_cat', $cf_cat->slug, $cf_blog_url ) : '';

    $cf_author_id  = (int) get_the_author_meta( 'ID' );
    $cf_author     = get_the_author();
    $cf_author_bio = get_the_author_meta( 'description' );

    $cf_author_artist_term = function_exists( 'collective_finity_get_artist_term_for_user' )
        ? collective_finity_get_artist_term_for_user( $cf_author_id )
        : null;
    $cf_author_artist_link = null;
    if ( $cf_author_artist_term ) {
        $cf_author_artist_link = get_term_link( $cf_author_artist_term );
        if ( is_wp_error( $cf_author_artist_link ) ) {
            $cf_author_artist_link = null;
        }
    }

    // Process content once: inject heading IDs + build TOC.
    $cf_rendered  = apply_filters( 'the_content', get_the_content() );
    $cf_rendered  = str_replace( ']]>', ']]&gt;', $cf_rendered );
    $cf_processed = collective_finity_build_post_toc( $cf_rendered );
    $cf_body      = $cf_processed['content'];
    $cf_toc       = $cf_processed['toc'];

    $cf_rating = collective_finity_post_rating_data( $cf_post_id );

    $cf_like_count = collective_finity_post_likes_count( $cf_post_id );
    $cf_post_liked = collective_finity_user_liked_post( $cf_post_id );
    $cf_view_count = collective_finity_post_view_count( $cf_post_id );
    $cf_popular    = collective_finity_get_popular_posts( $cf_post_id, 3 );
    // Standalone singles only (excludes album_track); mirrors release-type labeling in cf-latest-releases-shortcode.php.
    $cf_latest_singles = get_posts(
        array(
            'post_type'           => 'tracks',
            'post_status'         => 'publish',
            'posts_per_page'      => 2,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
            'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
                'relation' => 'OR',
                array(
                    'key'     => 'track_release_type',
                    'value'   => 'single',
                    'compare' => '=',
                ),
                array(
                    'key'     => 'track_release_type',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        )
    );
    $cf_latest_albums = get_posts(
        array(
            'post_type'           => 'albums',
            'post_status'         => 'publish',
            'posts_per_page'      => 2,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
        )
    );
    ?>

<div class="cf-progress-bar" aria-hidden="true"><span class="cf-progress-fill" id="cf-read-progress"></span></div>

<div class="cf-blog cf-single">
    <article id="post-<?php echo esc_attr( $cf_post_id ); ?>" <?php post_class( 'cf-single-article' ); ?>>

        <nav class="cf-bh-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'collective-finity' ); ?>">
            <a href="<?php echo esc_url( $cf_blog_url ); ?>"><?php esc_html_e( 'Blog', 'collective-finity' ); ?></a>
            <span aria-hidden="true">/</span>
            <?php if ( $cf_cat_link ) : ?>
                <a href="<?php echo esc_url( $cf_cat_link ); ?>"><?php echo esc_html( $cf_cat_name ); ?></a>
                <span aria-hidden="true">/</span>
            <?php endif; ?>
            <span class="cf-bh-breadcrumb-current"><?php the_title(); ?></span>
        </nav>

        <?php if ( has_post_thumbnail() ) : ?>
            <div class="cf-single-hero">
                <?php the_post_thumbnail( 'large', array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
            </div>
        <?php else : ?>
            <div class="cf-single-hero cf-single-hero--grad" style="background:<?php echo esc_attr( collective_finity_gradient_for( $cf_post_id ) ); ?>"></div>
        <?php endif; ?>

        <div class="cf-single-intro">
            <?php if ( $cf_cat_link ) : ?>
                <a class="cf-single-cat" href="<?php echo esc_url( $cf_cat_link ); ?>"><?php echo esc_html( $cf_cat_name ); ?></a>
            <?php else : ?>
                <span class="cf-single-cat"><?php echo esc_html( $cf_cat_name ); ?></span>
            <?php endif; ?>
            <h1 class="cf-single-title"><?php the_title(); ?></h1>
        </div>

        <div class="cf-single-meta-row">
            <div class="cf-single-meta">
                <?php echo collective_finity_review_avatar( $cf_author_id, 30 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php if ( $cf_author_artist_link ) : ?>
                    <a class="cf-single-author" href="<?php echo esc_url( $cf_author_artist_link ); ?>"><?php echo esc_html( $cf_author ); ?></a>
                <?php else : ?>
                    <span class="cf-single-author"><?php echo esc_html( $cf_author ); ?></span>
                <?php endif; ?>
                <span class="cf-single-dot" aria-hidden="true">&middot;</span>
                <span class="cf-single-datetime"><?php echo esc_html( get_the_date() ); ?> &middot; <?php echo esc_html( $cf_read_time ); ?></span>
            </div>
            <div class="cf-single-engagement">
                <button type="button" class="cf-share-btn cf-post-like-btn<?php echo $cf_post_liked ? ' active' : ''; ?>" data-post-id="<?php echo esc_attr( (string) $cf_post_id ); ?>" aria-pressed="<?php echo $cf_post_liked ? 'true' : 'false'; ?>" title="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>">
                    <?php echo collective_finity_icon( 'heart', 16, $cf_post_liked ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <span class="cf-post-like-count"><?php echo esc_html( number_format_i18n( $cf_like_count ) ); ?></span>
                </button>
                <span class="cf-share-btn cf-post-view-stat" aria-label="<?php esc_attr_e( 'Views', 'collective-finity' ); ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    <span><?php echo esc_html( number_format_i18n( $cf_view_count ) ); ?></span>
                </span>
                <button type="button" class="cf-share-btn" data-cf-share data-post-id="<?php echo esc_attr( (string) $cf_post_id ); ?>" data-url="<?php echo esc_url( get_permalink() ); ?>" data-title="<?php the_title_attribute(); ?>">
                    <?php echo collective_finity_icon( 'share', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <span><?php esc_html_e( 'Share', 'collective-finity' ); ?></span>
                </button>
            </div>
        </div>

        <div class="cf-single-read-layout">
            <?php if ( ! empty( $cf_toc ) ) : ?>
                <details class="cf-toc-collapsible">
                    <summary>
                        <span><?php esc_html_e( 'Contents', 'collective-finity' ); ?></span>
                        <span class="cf-toc-chevron"><?php echo collective_finity_icon( 'chevronDown', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                    </summary>
                    <ul class="cf-toc-list">
                        <?php foreach ( $cf_toc as $cf_item ) : ?>
                            <li class="cf-toc-<?php echo esc_attr( $cf_item['level'] ); ?>"><a href="#<?php echo esc_attr( $cf_item['id'] ); ?>"><?php echo esc_html( $cf_item['text'] ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </details>
            <?php endif; ?>

            <aside class="cf-single-widget-col" aria-label="<?php esc_attr_e( 'Article sidebar', 'collective-finity' ); ?>">
                <?php if ( ! empty( $cf_popular ) ) : ?>
                    <div class="cf-sidebar-widget">
                        <div class="cf-sidebar-widget-label"><?php esc_html_e( 'Popular Articles', 'collective-finity' ); ?></div>
                        <ul class="cf-popular-articles">
                            <?php foreach ( $cf_popular as $cf_pop_post ) : ?>
                                <?php
                                $cf_pop_thumb = get_the_post_thumbnail_url( $cf_pop_post, 'thumbnail' );
                                ?>
                                <li>
                                    <a href="<?php echo esc_url( get_permalink( $cf_pop_post ) ); ?>">
                                        <?php if ( $cf_pop_thumb ) : ?>
                                            <img class="cf-popular-article-thumb" src="<?php echo esc_url( $cf_pop_thumb ); ?>" alt="" loading="lazy" width="40" height="40" />
                                        <?php else : ?>
                                            <span class="cf-popular-article-thumb cf-popular-article-thumb--grad" style="background:<?php echo esc_attr( collective_finity_gradient_for( $cf_pop_post->ID ) ); ?>" aria-hidden="true"></span>
                                        <?php endif; ?>
                                        <span class="cf-popular-article-title"><?php echo esc_html( get_the_title( $cf_pop_post ) ); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="cf-sidebar-ad">
                    <?php collective_finity_render_blog_sidebar_ad(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>

                <?php if ( ! empty( $cf_latest_singles ) ) : ?>
                    <div class="cf-sidebar-widget">
                        <div class="cf-sidebar-widget-label"><?php esc_html_e( 'Latest Singles', 'collective-finity' ); ?></div>
                        <ul class="cf-latest-releases">
                            <?php
                            foreach ( $cf_latest_singles as $cf_single_post ) :
                                // Same cover chain as inc/cf-latest-releases-shortcode.php ($cf_get_track_cover).
                                $cf_single_cover = get_post_meta( $cf_single_post->ID, 'track_cover_url', true );
                                if ( ! $cf_single_cover ) {
                                    $cf_single_cover = get_the_post_thumbnail_url( $cf_single_post->ID, 'medium' );
                                }
                                if ( ! $cf_single_cover ) {
                                    $cf_single_cover = collective_finity_default_art_url();
                                }
                                $cf_single_artists = wp_get_post_terms( $cf_single_post->ID, 'track_artist' );
                                $cf_single_artist  = ! empty( $cf_single_artists ) && ! is_wp_error( $cf_single_artists )
                                    ? $cf_single_artists[0]->name
                                    : __( 'Collective Finity', 'collective-finity' );
                                ?>
                                <li>
                                    <a href="<?php echo esc_url( get_permalink( $cf_single_post ) ); ?>">
                                        <img class="cf-latest-release-cover" src="<?php echo esc_url( $cf_single_cover ); ?>" alt="" loading="lazy" width="40" height="40" />
                                        <span class="cf-latest-release-meta">
                                            <span class="cf-latest-release-title"><?php echo esc_html( get_the_title( $cf_single_post ) ); ?></span>
                                            <span class="cf-latest-release-artist"><?php echo esc_html( $cf_single_artist ); ?></span>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $cf_latest_albums ) ) : ?>
                    <div class="cf-sidebar-widget">
                        <div class="cf-sidebar-widget-label"><?php esc_html_e( 'Latest Albums', 'collective-finity' ); ?></div>
                        <ul class="cf-latest-releases">
                            <?php
                            foreach ( $cf_latest_albums as $cf_album_post ) :
                                // Same cover chain as inc/cf-latest-releases-shortcode.php ($cf_get_album_cover).
                                $cf_album_cover = get_the_post_thumbnail_url( $cf_album_post->ID, 'medium' );
                                if ( ! $cf_album_cover ) {
                                    $cf_album_first_track = get_posts(
                                        array(
                                            'post_type'      => 'tracks',
                                            'posts_per_page' => 1,
                                            'meta_key'       => 'associated_album', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                                            'meta_value'     => $cf_album_post->ID, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
                                            'fields'         => 'ids',
                                        )
                                    );
                                    if ( ! empty( $cf_album_first_track ) ) {
                                        $cf_album_cover = get_post_meta( $cf_album_first_track[0], 'track_cover_url', true );
                                    }
                                }
                                if ( ! $cf_album_cover ) {
                                    $cf_album_cover = collective_finity_default_art_url();
                                }
                                $cf_album_artist = get_the_author_meta( 'display_name', $cf_album_post->post_author );
                                if ( ! $cf_album_artist ) {
                                    $cf_album_artist = __( 'Collective Finity', 'collective-finity' );
                                }
                                ?>
                                <li>
                                    <a href="<?php echo esc_url( get_permalink( $cf_album_post ) ); ?>">
                                        <img class="cf-latest-release-cover" src="<?php echo esc_url( $cf_album_cover ); ?>" alt="" loading="lazy" width="40" height="40" />
                                        <span class="cf-latest-release-meta">
                                            <span class="cf-latest-release-title"><?php echo esc_html( get_the_title( $cf_album_post ) ); ?></span>
                                            <span class="cf-latest-release-artist"><?php echo esc_html( $cf_album_artist ); ?></span>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="cf-sidebar-ad">
                    <?php collective_finity_render_blog_sidebar_ad(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>

                <?php if ( ! empty( $cf_toc ) ) : ?>
                    <div class="cf-single-widget-data" hidden>
                        <div class="cf-toc-card">
                            <div class="cf-toc-label"><?php esc_html_e( 'CONTENTS', 'collective-finity' ); ?></div>
                            <ul class="cf-toc-list">
                                <?php foreach ( $cf_toc as $cf_item ) : ?>
                                    <li class="cf-toc-<?php echo esc_attr( $cf_item['level'] ); ?>"><a href="#<?php echo esc_attr( $cf_item['id'] ); ?>"><?php echo esc_html( $cf_item['text'] ); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </aside>

            <div class="cf-post-body">
                <?php echo wp_kses_post( $cf_body ); ?>
            </div>
            <div class="cf-read-clear" aria-hidden="true"></div>
        </div>

            <?php if ( $cf_author_bio ) : ?>
                <div class="cf-author-bio">
                    <?php echo collective_finity_review_avatar( $cf_author_id, 48 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <div>
                        <?php if ( $cf_author_artist_link ) : ?>
                            <div class="cf-author-bio-name"><a href="<?php echo esc_url( $cf_author_artist_link ); ?>"><?php echo esc_html( $cf_author ); ?></a></div>
                        <?php else : ?>
                            <div class="cf-author-bio-name"><?php echo esc_html( $cf_author ); ?></div>
                        <?php endif; ?>
                        <div class="cf-author-bio-text"><?php echo esc_html( $cf_author_bio ); ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="cf-newsletter">
                <div class="cf-newsletter-title"><?php esc_html_e( 'Get notified about new courses and tutorials', 'collective-finity' ); ?></div>
                <div class="cf-newsletter-form">
                    <?php echo do_shortcode( '[contact-form-7 id="a1d896d" title="Subscription Form"]' ); ?>
                </div>
            </div>

            <?php
            $cf_related_args = array(
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => 3,
                'post__not_in'        => array( $cf_post_id ),
                'ignore_sticky_posts' => true,
            );
            if ( $cf_cat ) {
                $cf_related_args['category__in'] = array( $cf_cat->term_id );
            }
            $cf_related = new WP_Query( $cf_related_args );

            // Backfill with recent posts if the same-category set is short.
            if ( $cf_related->post_count < 3 ) {
                $cf_have    = wp_list_pluck( $cf_related->posts, 'ID' );
                $cf_exclude = array_merge( array( $cf_post_id ), $cf_have );
                $cf_fill    = get_posts(
                    array(
                        'post_type'           => 'post',
                        'post_status'         => 'publish',
                        'posts_per_page'      => 3 - $cf_related->post_count,
                        'post__not_in'        => $cf_exclude,
                        'ignore_sticky_posts' => true,
                    )
                );
                $cf_related_posts = array_merge( $cf_related->posts, $cf_fill );
            } else {
                $cf_related_posts = $cf_related->posts;
            }
            wp_reset_postdata();

            collective_finity_ad_slot( 'single_post' );

            if ( ! empty( $cf_related_posts ) ) :
                ?>
                <section class="cf-related">
                    <h2 class="cf-related-title"><?php esc_html_e( 'Related Articles', 'collective-finity' ); ?></h2>
                    <div class="cf-related-grid">
                        <?php
                        foreach ( $cf_related_posts as $cf_rp ) {
                            echo collective_finity_render_blog_card( $cf_rp, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                        ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php
            /* -------------------- Ratings & Comments -------------------- */
            $cf_reviews = get_comments(
                array(
                    'post_id' => $cf_post_id,
                    'status'  => 'approve',
                    'type'    => 'comment',
                    'order'   => 'DESC',
                )
            );
            ?>
            <section class="cf-reviews" id="reviews">
                <div class="cf-reviews-head">
                    <h2 class="cf-reviews-title"><?php esc_html_e( 'Ratings &amp; Comments', 'collective-finity' ); ?></h2>
                    <?php if ( $cf_rating['count'] > 0 ) : ?>
                        <div class="cf-reviews-summary">
                            <?php echo collective_finity_icon( 'star', 15, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <span class="cf-reviews-avg"><?php echo esc_html( number_format_i18n( $cf_rating['avg'], 1 ) ); ?></span>
                            <span class="cf-reviews-count">
                                <?php
                                printf(
                                    /* translators: %s: number of reviews. */
                                    esc_html( _n( '(%s review)', '(%s reviews)', $cf_rating['count'], 'collective-finity' ) ),
                                    esc_html( number_format_i18n( $cf_rating['count'] ) )
                                );
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ( ! comments_open() ) : ?>
                    <p class="cf-reviews-closed"><?php esc_html_e( 'Reviews are closed for this article.', 'collective-finity' ); ?></p>
                <?php elseif ( is_user_logged_in() ) : ?>
                    <?php $cf_current_user = wp_get_current_user(); ?>
                    <form action="<?php echo esc_url( site_url( '/wp-comments-post.php' ) ); ?>" method="post" class="cf-review-form">
                        <?php echo collective_finity_review_avatar( $cf_current_user->ID, 38 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <div class="cf-review-form-body">
                            <div class="cf-review-form-name"><?php echo esc_html( $cf_current_user->display_name ); ?></div>
                            <?php echo collective_finity_star_input_markup( 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <textarea name="comment" rows="3" required placeholder="<?php esc_attr_e( 'Share your thoughts on this article...', 'collective-finity' ); ?>"></textarea>
                            <?php comment_id_fields( $cf_post_id ); ?>
                            <button type="submit" class="cf-review-submit"><?php esc_html_e( 'Submit Review', 'collective-finity' ); ?></button>
                        </div>
                    </form>
                <?php else : ?>
                    <div class="cf-review-gate">
                        <div class="cf-review-gate-ghost" aria-hidden="true">
                            <span class="cf-review-avatar cf-review-avatar--initial" style="width:38px;height:38px;min-width:38px;">&nbsp;</span>
                            <div class="cf-review-gate-lines"><span></span><span></span></div>
                        </div>
                        <div class="cf-review-gate-overlay">
                            <div class="cf-review-gate-msg"><?php esc_html_e( 'Sign in to leave a review', 'collective-finity' ); ?></div>
                            <a class="cf-review-gate-btn" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Log In', 'collective-finity' ); ?></a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $cf_reviews ) ) : ?>
                    <div class="cf-review-list">
                        <?php foreach ( $cf_reviews as $cf_review ) : ?>
                            <?php $cf_r_rating = (int) get_comment_meta( $cf_review->comment_ID, 'cf_rating', true ); ?>
                            <div class="cf-review" id="comment-<?php echo esc_attr( $cf_review->comment_ID ); ?>">
                                <?php echo collective_finity_review_avatar( $cf_review, 36 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <div class="cf-review-body">
                                    <div class="cf-review-head">
                                        <span class="cf-review-name"><?php echo esc_html( $cf_review->comment_author ); ?></span>
                                        <?php if ( $cf_r_rating >= 1 && $cf_r_rating <= 5 ) : ?>
                                            <?php echo collective_finity_stars_markup( $cf_r_rating, 13 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                        <?php endif; ?>
                                        <span class="cf-review-date">
                                            <?php
                                            printf(
                                                /* translators: %s: human-readable time difference, e.g. "3 days". */
                                                esc_html__( '%s ago', 'collective-finity' ),
                                                esc_html( human_time_diff( (int) get_comment_time( 'U', true, false, $cf_review ), time() ) )
                                            );
                                            ?>
                                        </span>
                                    </div>
                                    <div class="cf-review-text"><?php echo wp_kses_post( wpautop( $cf_review->comment_content ) ); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ( comments_open() ) : ?>
                    <p class="cf-reviews-empty"><?php esc_html_e( 'No reviews yet. Be the first to share your thoughts.', 'collective-finity' ); ?></p>
                <?php endif; ?>
            </section>

    </article>
</div>

<style>
    .cf-blog { padding: 30px 5px 5px; max-width: 100%; min-width: 0; box-sizing: border-box; }
    .cf-blog.cf-single { width: 100%; max-width: 100%; min-width: 0; }

    /* read progress */
    .cf-progress-bar { position: fixed; top: 0; left: 0; width: 100%; height: 3px; background: var(--cf-divider); z-index: 100; }
    .cf-progress-fill { display: block; height: 100%; width: 0; background: var(--cf-accent); transition: width 0.1s linear; }

    .cf-single-article { display: flex; flex-direction: column; gap: 16px; width: 100%; max-width: 100%; min-width: 0; }

    /* breadcrumb pill */
    .cf-bh-breadcrumb { align-self: flex-start; display: inline-block; max-width: 100%; border: 1px solid var(--cf-accent); border-radius: 999px; padding: 8px 18px; font-size: 12.5px; color: var(--cf-text-3); overflow-wrap: anywhere; word-break: break-word; }
    .cf-bh-breadcrumb a { color: var(--cf-text-3); text-decoration: none; }
    .cf-bh-breadcrumb a:hover { color: var(--cf-accent); }
    .cf-bh-breadcrumb-current { color: var(--cf-text-3); }

    /* full-width hero image (image only — title sits below) */
    .cf-single-hero {
        position: relative;
        width: 100%;
        max-width: 100%;
        margin-left: 0;
        margin-right: 0;
        min-width: 0;
        border-radius: 25px;
        overflow: hidden;
        box-sizing: border-box;
    }
    .cf-single-hero > img {
        width: 100%;
        height: 100%;
        max-width: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
    }
    .cf-single-hero--grad { height: 100%; background-size: cover; background-position: center; }

    /* desktop only — fixed moderate height */
    @media (min-width: 1024px) {
        .cf-single-hero {
            display: block;
            height: 450px;
        }
        .cf-single-hero > img {
            max-height: 100%;
        }
    }

    .cf-single-intro {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        width: 100%;
        max-width: 100%;
        min-width: 0;
    }
    .cf-single-cat { font-family: var(--cf-mono); font-size: 11px; color: var(--cf-accent); padding: 4px 10px; border-radius: 999px; background: rgba(255,183,0,0.12); text-decoration: none; letter-spacing: 0.04em; text-transform: uppercase; }
    .cf-single-title { font-size: 32px; font-weight: 700; color: #fff; line-height: 1.25; margin: 0; overflow-wrap: anywhere; word-break: break-word; max-width: 100%; }

    /* meta row below hero */
    .cf-single-meta-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; width: 100%; max-width: 100%; min-width: 0; }
    .cf-single-meta { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; min-width: 0; }
    .cf-single-meta .cf-review-avatar { border: 1px solid rgba(255, 183, 0, 0.28); }
    .cf-single-author { font-size: 13.5px; font-weight: 600; color: #F0F0F0; }
    a.cf-single-author { text-decoration: none; transition: color 0.2s ease; }
    a.cf-single-author:hover { color: var(--primary-color, #FFB700); text-decoration: underline; }
    .cf-single-dot { color: rgba(255, 183, 0, 0.45); }
    .cf-single-datetime { font-size: 12.5px; color: #B8B8B8; font-family: var(--cf-mono); letter-spacing: 0.01em; }

    .cf-single-engagement { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; flex-shrink: 0; }
    .cf-single-engagement .cf-share-btn {
        display: inline-flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 999px;
        border: 1px solid rgba(255, 183, 0, 0.35);
        background: rgba(255, 183, 0, 0.08);
        color: #FFB700;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        font-family: var(--cf-body);
        transition: color 0.2s ease, border-color 0.2s ease, background 0.2s ease;
        min-height: 40px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .cf-single-engagement .cf-share-btn svg { flex-shrink: 0; color: currentColor; }
    .cf-single-engagement .cf-share-btn:hover { color: #ffde99; border-color: rgba(255, 222, 153, 0.5); background: rgba(255, 183, 0, 0.14); }
    .cf-single-engagement .cf-post-like-btn.active { color: #FFB700; border-color: rgba(255, 183, 0, 0.55); background: rgba(255, 183, 0, 0.16); }
    .cf-single-engagement .cf-post-like-btn.active:hover { color: #ffde99; }
    .cf-post-view-stat { cursor: default; pointer-events: none; }

    /* reading layout — single column with floated widget (desktop) */
    .cf-single-read-layout { width: 100%; max-width: 100%; min-width: 0; }
    .cf-read-clear { clear: both; }

    .cf-single-widget-col {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-bottom: 24px;
    }

    .cf-sidebar-widget { display: flex; flex-direction: column; gap: 10px; }
    .cf-sidebar-widget-label {
        font-family: var(--cf-mono);
        font-size: 11px;
        color: var(--cf-text-3);
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }
    .cf-popular-articles,
    .cf-latest-releases { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 10px; }
    .cf-popular-articles a,
    .cf-latest-releases a {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: var(--cf-text-2);
        font-size: 13px;
        line-height: 1.45;
        padding: 11px 12px;
        border-radius: 8px;
        border: 1px solid var(--cf-border);
        background: var(--cf-bg-card);
        transition: color 0.15s ease, border-color 0.15s ease, background 0.15s ease;
        overflow-wrap: anywhere;
        word-break: break-word;
    }
    .cf-popular-articles a:hover,
    .cf-latest-releases a:hover { color: #fff; border-color: var(--cf-border-strong); background: var(--cf-bg-card-hover); }
    .cf-popular-article-thumb {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 8px;
        object-fit: cover;
        display: block;
        flex-shrink: 0;
        background: var(--cf-bg-dark);
    }
    .cf-popular-article-thumb--grad { display: block; }
    .cf-popular-article-title { flex: 1; min-width: 0; }
    .cf-latest-release-cover {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 50%;
        object-fit: cover;
        display: block;
        flex-shrink: 0;
        background: var(--cf-bg-dark);
    }
    .cf-latest-release-meta { display: flex; flex-direction: column; gap: 3px; min-width: 0; flex: 1; }
    .cf-latest-release-title { font-size: 13px; font-weight: 600; color: #fff; }
    .cf-latest-release-artist { font-size: 11.5px; color: var(--cf-text-3); }

    .cf-sidebar-ad { max-width: 100%; min-width: 0; }
    .cf-ad-slot { margin: 0 auto; max-width: 100%; text-align: center; box-sizing: border-box; }
    .cf-ad-slot--preview {
        align-items: center;
        background: rgba(255, 255, 255, 0.04);
        border: 1px dashed rgba(255, 183, 0, 0.35);
        border-radius: 12px;
        color: rgba(255, 255, 255, 0.55);
        display: flex;
        font-family: var(--cf-mono);
        font-size: 12px;
        justify-content: center;
        min-height: 90px;
        padding: 16px;
        box-sizing: border-box;
    }

    @media (min-width: 768px) {
        .cf-single-read-layout { overflow: hidden; }
        .cf-single-widget-col {
            float: right;
            width: 220px;
            margin-left: 20px;
            margin-bottom: 20px;
        }
    }

    /* avatars (shared) */
    .cf-review-avatar { display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; overflow: hidden; flex-shrink: 0; }
    .cf-review-avatar-img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .cf-review-avatar--initial { background: linear-gradient(135deg, #FFB700, #8a6200); color: #0D0D0D; font-family: var(--cf-mono); font-weight: 700; }

    /* TOC (mobile collapsible + hidden desktop data) */
    .cf-toc-label { font-family: var(--cf-mono); font-size: 11px; color: var(--cf-text-3); letter-spacing: 0.06em; margin-bottom: 10px; padding: 0 10px; }
    .cf-toc-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 2px; }
    .cf-toc-list a { display: block; text-decoration: none; color: var(--cf-text-3); font-size: 12.5px; padding: 7px 10px; border-radius: 7px; line-height: 1.4; transition: background 0.15s ease, color 0.15s ease; overflow-wrap: anywhere; word-break: break-word; }
    .cf-toc-list a:hover { background: var(--cf-bg-card-hover); color: #fff; }
    .cf-toc-list .cf-toc-h3 a { padding-left: 22px; font-size: 12px; }
    .cf-toc-collapsible { display: none; max-width: 100%; min-width: 0; border: 1px solid var(--cf-border); border-radius: 10px; background: var(--cf-bg-card); overflow: hidden; }
    .cf-toc-collapsible summary { list-style: none; display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; color: #fff; font-weight: 600; font-size: 13.5px; cursor: pointer; max-width: 100%; min-width: 0; overflow-wrap: anywhere; word-break: break-word; }
    .cf-toc-collapsible summary::-webkit-details-marker { display: none; }
    .cf-toc-collapsible[open] .cf-toc-chevron { transform: rotate(180deg); }
    .cf-toc-chevron { display: inline-flex; transition: transform 0.15s ease; }
    .cf-toc-collapsible .cf-toc-list { padding: 4px 10px 14px; }

    /* body content — block layout so text wraps around floated sidebar; capped for readability when shell sidebars are collapsed */
    .cf-post-body { display: block; color: #C7C7C7; min-width: 0; max-width: 800px; width: 100%; box-sizing: border-box; }
    .cf-post-body > * { margin: 0; max-width: 100%; min-width: 0; box-sizing: border-box; }
    .cf-post-body > * + * { margin-top: 22px; }
    .cf-post-body p { font-size: 18px; line-height: 1.8; color: #C7C7C7; overflow-wrap: anywhere; word-break: break-word; }
    .cf-post-body h2 { font-size: 22px; font-weight: 700; color: #fff; margin-top: 8px; scroll-margin-top: 20px; }
    .cf-post-body h3 { font-size: 18px; font-weight: 700; color: #fff; scroll-margin-top: 20px; }
    .cf-post-body a { color: var(--cf-accent); }
    .cf-post-body ul, .cf-post-body ol { padding-left: 22px; color: #C7C7C7; line-height: 1.8; font-size: 17px; display: flex; flex-direction: column; gap: 6px; }
    .cf-post-body img { max-width: 100%; height: auto; border-radius: 12px; }
    .cf-post-body figure { margin: 0; }
    .cf-post-body figcaption { font-size: 12.5px; color: var(--cf-text-3); margin-top: 6px; text-align: center; }

    /* pull-quote (blockquote) */
    .cf-post-body blockquote { border-left: 3px solid var(--cf-accent); padding-left: 20px; font-size: 19px; font-style: italic; color: #fff; line-height: 1.5; }
    .cf-post-body blockquote p { font-size: 19px; font-style: italic; color: #fff; }

    /* callout / code box */
    .cf-post-body pre,
    .cf-post-body .wp-block-code,
    .cf-post-body code {
        max-width: 100%;
        box-sizing: border-box;
    }
    .cf-post-body pre,
    .cf-post-body .wp-block-code {
        border: 1px solid rgba(255,183,0,0.3);
        border-left: 3px solid var(--cf-accent);
        border-radius: 8px;
        background: var(--cf-bg-card);
        padding: 18px 20px;
        font-family: var(--cf-mono);
        font-size: 13px;
        color: #E4E4E4;
        line-height: 1.7;
        overflow-x: auto;
        min-width: 0;
    }
    .cf-post-body pre code { display: block; max-width: 100%; white-space: pre; overflow-x: auto; }
    .cf-post-body :not(pre) > code {
        display: block;
        font-family: var(--cf-mono);
        font-size: 0.9em;
        background: var(--cf-bg-card);
        border: 1px solid var(--cf-border);
        border-radius: 5px;
        padding: 10px 12px;
        color: var(--cf-accent);
        overflow-x: auto;
        white-space: pre;
        max-width: 100%;
    }

    /* tables */
    .cf-post-body table {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        border-collapse: collapse;
        font-size: 14.5px;
        border: 1px solid var(--cf-border);
        border-radius: 10px;
        overflow: hidden;
        display: block;
        overflow-x: auto;
    }
    .cf-post-body th { text-align: left; padding: 12px 16px; background: var(--cf-accent-dim); color: var(--cf-accent); font-family: var(--cf-mono); font-size: 11.5px; text-transform: uppercase; }
    .cf-post-body td { padding: 14px 16px; color: var(--cf-text-2); border-top: 1px solid var(--cf-border); }
    .cf-post-body tbody tr:nth-child(odd) { background: var(--cf-bg-card); }
    .cf-post-body tbody tr:nth-child(even) { background: var(--cf-bg-card-hover); }
    .cf-post-body .wp-block-table,
    .cf-post-body figure.wp-block-table { max-width: 100%; overflow-x: auto; display: block; box-sizing: border-box; min-width: 0; }
    .cf-post-body .wp-block-table table { display: table; width: max-content; min-width: 100%; }

    /* author bio */
    .cf-author-bio { display: flex; gap: 14px; padding: 20px; border-radius: 12px; background: var(--cf-bg-card); border: 1px solid var(--cf-border); align-items: flex-start; }
    .cf-author-bio-name { font-size: 14.5px; font-weight: 700; color: #fff; }
    .cf-author-bio-name a { color: inherit; text-decoration: none; transition: color 0.2s ease; }
    .cf-author-bio-name a:hover { color: var(--primary-color, #FFB700); text-decoration: underline; }
    .cf-author-bio-text { font-size: 12.5px; color: var(--cf-text-3); margin-top: 4px; line-height: 1.6; }

    /* newsletter */
    .cf-newsletter { padding: 24px; border-radius: 14px; background: linear-gradient(135deg, rgba(255,183,0,0.14), rgba(255,183,0,0.03)); border: 1px solid rgba(255,183,0,0.25); display: flex; flex-direction: column; gap: 14px; }
    .cf-newsletter-title { font-size: 17px; font-weight: 700; color: #fff; }
    .cf-newsletter-form input[type="email"], .cf-newsletter-form input[type="text"] { width: 100%; max-width: 320px; padding: 11px 13px; border-radius: 9px; border: 1px solid var(--cf-border); background: var(--cf-bg-card); color: #fff; font-size: 13.5px; font-family: var(--cf-body); }
    .cf-newsletter-form input[type="submit"], .cf-newsletter-form button { margin-top: 10px; padding: 11px 20px; border-radius: 9px; border: none; background: var(--cf-accent); color: #0D0D0D; font-weight: 700; font-size: 13.5px; cursor: pointer; }
    .cf-newsletter-form input[type="submit"]:hover, .cf-newsletter-form button:hover { background: var(--cf-accent-hover); }

    /* related */
    .cf-related-title { font-size: 19px; font-weight: 700; color: #fff; margin: 0 0 16px; }
    .cf-related-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }

    /* reviews */
    .cf-reviews { display: flex; flex-direction: column; gap: 20px; }
    .cf-reviews-head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .cf-reviews-title { font-size: 19px; font-weight: 700; color: #fff; margin: 0; }
    .cf-reviews-summary { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--cf-text-2); }
    .cf-reviews-summary svg { color: var(--cf-accent); }
    .cf-reviews-avg { color: #fff; font-weight: 700; }

    .cf-stars { display: inline-flex; gap: 3px; }
    .cf-star { display: inline-flex; color: #3a3a3a; }
    .cf-star.is-on { color: var(--cf-accent); }

    /* star input */
    .cf-stars-input { display: inline-flex; flex-direction: row-reverse; justify-content: flex-end; gap: 3px; }
    .cf-stars-input input { position: absolute; opacity: 0; width: 1px; height: 1px; }
    .cf-stars-input label { display: inline-flex; color: #3a3a3a; cursor: pointer; transition: color 0.12s ease; }
    .cf-stars-input label:hover, .cf-stars-input label:hover ~ label, .cf-stars-input input:checked ~ label { color: var(--cf-accent); }
    .cf-stars-input input:focus-visible + label { outline: 2px solid var(--cf-accent); outline-offset: 2px; border-radius: 4px; }

    .cf-review-form { display: flex; gap: 14px; align-items: flex-start; padding: 20px; border-radius: 12px; background: var(--cf-bg-card); border: 1px solid var(--cf-border); }
    .cf-review-form-body { flex: 1; display: flex; flex-direction: column; gap: 10px; min-width: 0; }
    .cf-review-form-name { font-size: 13.5px; font-weight: 700; color: #fff; }
    .cf-review-form textarea { width: 100%; padding: 11px 13px; border-radius: 9px; border: 1px solid var(--cf-border); background: var(--cf-bg-dark); color: #fff; font-size: 13.5px; font-family: var(--cf-body); resize: vertical; }
    .cf-review-submit { align-self: flex-start; padding: 10px 18px; border-radius: 9px; border: none; background: var(--cf-accent); color: #0D0D0D; font-weight: 700; font-size: 13px; cursor: pointer; }
    .cf-review-submit:hover { background: var(--cf-accent-hover); }

    /* gated (logged-out) */
    .cf-review-gate { position: relative; border-radius: 12px; overflow: hidden; }
    .cf-review-gate-ghost { display: flex; gap: 14px; align-items: flex-start; padding: 20px; border-radius: 12px; background: var(--cf-bg-card); border: 1px solid var(--cf-border); opacity: 0.35; filter: grayscale(0.4); pointer-events: none; }
    .cf-review-gate-ghost .cf-review-avatar--initial { background: #333; }
    .cf-review-gate-lines { flex: 1; display: flex; flex-direction: column; gap: 10px; }
    .cf-review-gate-lines span { display: block; height: 12px; border-radius: 4px; background: #262626; }
    .cf-review-gate-lines span:last-child { height: 72px; }
    .cf-review-gate-overlay { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; background: rgba(13,13,13,0.55); }
    .cf-review-gate-msg { font-size: 13.5px; color: #fff; font-weight: 600; }
    .cf-review-gate-btn { padding: 9px 18px; border-radius: 9px; background: var(--cf-accent); color: #0D0D0D; font-weight: 700; font-size: 12.5px; text-decoration: none; }
    .cf-review-gate-btn:hover { background: var(--cf-accent-hover); color: #0D0D0D; }

    /* review list */
    .cf-review-list { display: flex; flex-direction: column; gap: 14px; }
    .cf-review { display: flex; gap: 14px; align-items: flex-start; padding-bottom: 14px; border-bottom: 1px solid var(--cf-divider); }
    .cf-review-body { flex: 1; min-width: 0; }
    .cf-review-head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .cf-review-name { font-size: 13.5px; font-weight: 700; color: #fff; }
    .cf-review-date { font-size: 11.5px; color: var(--cf-text-4); font-family: var(--cf-mono); }
    .cf-review-text { font-size: 13px; color: var(--cf-text-2); line-height: 1.6; margin-top: 6px; }
    .cf-review-text p { margin: 0 0 8px; }
    .cf-reviews-empty, .cf-reviews-closed { color: var(--cf-text-3); font-size: 13.5px; }

    /* responsive */
    @media (max-width: 1023px) {
        .cf-single-hero {
            display: grid;
            height: auto;
        }
        .cf-single-hero::before {
            content: '';
            grid-area: 1 / 1;
            width: 100%;
            aspect-ratio: 16 / 9;
            pointer-events: none;
        }
        .cf-single-hero > img {
            grid-area: 1 / 1;
            min-height: 100%;
            max-height: 100%;
        }
        .cf-toc-collapsible { display: block; margin-bottom: 20px; }
    }
    @media (max-width: 767px) {
        .cf-blog { padding: 18px 5px 5px; }
        .cf-single-hero {
            width: calc(100% + 32px);
            max-width: calc(100% + 32px);
            margin-left: -16px;
            margin-right: -16px;
        }
        .cf-single-hero::before { aspect-ratio: 4 / 3; min-height: 240px; }
        .cf-single-title { font-size: 26px; line-height: 1.3; }
        .cf-single-widget-col { float: none; width: 100%; margin-left: 0; margin-bottom: 24px; }
        .cf-single-meta-row { flex-direction: column; align-items: flex-start; }
        .cf-single-engagement { width: 100%; }
        .cf-single-engagement .cf-share-btn { min-height: 44px; padding: 10px 18px; }
        .cf-post-body p { font-size: 16px; }
        .cf-related-grid { grid-template-columns: 1fr; }
        .cf-review-form { flex-direction: column; }
    }
</style>

<script>
    (function () {
        var fill = document.getElementById('cf-read-progress');
        if (fill) {
            if (window.__cfReadProgressHandler) {
                window.removeEventListener('scroll', window.__cfReadProgressHandler, { passive: true });
                window.removeEventListener('resize', window.__cfReadProgressHandler);
            }

            window.__cfReadProgressHandler = function () {
                var bar = document.getElementById('cf-read-progress');
                if (!bar) {
                    return;
                }
                var max = document.documentElement.scrollHeight - window.innerHeight;
                var p = max > 0 ? Math.min(1, Math.max(0, window.scrollY / max)) : 0;
                bar.style.width = (p * 100) + '%';
            };

            window.addEventListener('scroll', window.__cfReadProgressHandler, { passive: true });
            window.addEventListener('resize', window.__cfReadProgressHandler);
            window.__cfReadProgressHandler();
        }
        document.querySelectorAll('[data-cf-share]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var url = btn.getAttribute('data-url');
                var title = btn.getAttribute('data-title') || document.title;
                var postId = btn.getAttribute('data-post-id');
                var platform = navigator.share ? 'native' : 'copy';
                if (postId && window.CF_Auth && typeof window.CF_Auth.trackShare === 'function') {
                    window.CF_Auth.trackShare(postId, 'post', platform);
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

        function cfSetPostLikeVisual(btn, liked) {
            btn.classList.toggle('active', liked);
            btn.setAttribute('aria-pressed', liked ? 'true' : 'false');
            var path = btn.querySelector('svg path');
            if (path) {
                if (liked) {
                    path.setAttribute('fill', 'currentColor');
                } else {
                    path.removeAttribute('fill');
                }
            }
        }

        document.querySelectorAll('.cf-post-like-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (!window.CF_AUTH || window.CF_AUTH.is_logged_in !== '1') {
                    alert('Please log in to like this article.');
                    return;
                }
                if (typeof window.CF_Auth === 'undefined' || !window.CF_Auth.toggleFavorite) {
                    alert('An error occurred. Please try again.');
                    return;
                }

                var postId = btn.getAttribute('data-post-id');
                if (!postId) {
                    return;
                }

                var wasLiked = btn.classList.contains('active');
                cfSetPostLikeVisual(btn, !wasLiked);

                window.CF_Auth.toggleFavorite(postId, 'post')
                    .then(function (result) {
                        var isLiked = result.is_favorite;
                        cfSetPostLikeVisual(btn, isLiked);
                        var countEl = btn.querySelector('.cf-post-like-count');
                        if (countEl && typeof result.likes_count !== 'undefined') {
                            countEl.textContent = String(result.likes_count);
                        }
                    })
                    .catch(function (err) {
                        cfSetPostLikeVisual(btn, wasLiked);
                        var message = (err && err.message) ? err.message : 'An error occurred. Please try again.';
                        alert(message);
                    });
            });
        });
    }());
</script>

    <?php
endwhile;

get_footer();
