<?php
/**
 * The template for displaying all single posts.
 *
 * @package Collective_Finity
 */

get_header();

while ( have_posts() ) :
    the_post();

    $post_id       = get_the_ID();
    $blog_url      = get_post_type_archive_link( 'post' );
    if ( ! $blog_url ) {
        $posts_page_id = (int) get_option( 'page_for_posts' );
        $blog_url      = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' );
    }
    $categories    = get_the_category();
    $primary_cat   = ! empty( $categories ) ? $categories[0] : null;
    $author_id     = (int) get_the_author_meta( 'ID' );
    $likes_count   = (int) get_post_meta( $post_id, '_cf_total_likes_count', true );
    $liked_posts   = is_user_logged_in() ? get_user_meta( get_current_user_id(), '_cf_liked_posts', true ) : array();
    $is_liked      = is_array( $liked_posts ) && in_array( $post_id, $liked_posts, true );
    $read_time     = cf_get_read_time_label( get_post_field( 'post_content', $post_id ) );
    $post_faq      = get_post_meta( $post_id, '_cf_post_faq', true );
    $faq_items     = is_array( $post_faq ) ? $post_faq : array();

    $related_query = new WP_Query(
        array(
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'post__not_in'   => array( $post_id ),
            'category__in'   => $primary_cat ? array( $primary_cat->term_id ) : array(),
        )
    );

    $review_comments = get_comments(
        array(
            'post_id' => $post_id,
            'status'  => 'approve',
            'type'    => 'comment',
        )
    );
    $rating_total    = 0;
    $rating_count    = 0;
    foreach ( $review_comments as $review_comment ) {
        $rating = (int) get_comment_meta( $review_comment->comment_ID, '_cf_review_rating', true );
        if ( $rating > 0 ) {
            $rating_total += $rating;
            $rating_count++;
        }
    }
    $avg_rating = $rating_count ? round( $rating_total / $rating_count, 1 ) : 0;
    ?>

    <div class="cf-read-progress" aria-hidden="true"><div class="cf-read-progress__bar"></div></div>

    <main id="primary" class="site-main cf-page-shell cf-single-post">
        <div class="cf-page-container">
            <div class="cf-post-layout">
                <article class="cf-post-main">
                    <nav class="cf-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'collective-finity' ); ?>">
                        <a href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( 'Blog', 'collective-finity' ); ?></a>
                        <span aria-hidden="true">/</span>
                        <?php if ( $primary_cat ) : ?>
                            <a href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>"><?php echo esc_html( $primary_cat->name ); ?></a>
                            <span aria-hidden="true">/</span>
                        <?php endif; ?>
                        <span class="cf-breadcrumb__current"><?php the_title(); ?></span>
                    </nav>

                    <header class="cf-post-hero-meta">
                        <?php if ( $primary_cat ) : ?>
                            <a class="cf-post-category-pill" href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>"><?php echo esc_html( strtoupper( $primary_cat->name ) ); ?></a>
                        <?php endif; ?>
                        <h1 class="cf-post-title"><?php the_title(); ?></h1>
                        <div class="cf-post-byline">
                            <?php echo get_avatar( $author_id, 60, '', '', array( 'class' => '' ) ); ?>
                            <span><?php the_author(); ?> · <?php echo esc_html( get_the_date() ); ?> · <?php echo esc_html( $read_time ); ?></span>
                        </div>
                    </header>

                    <div class="cf-engagement-bar">
                        <button
                            type="button"
                            class="cf-engagement-btn cf-like-btn<?php echo $is_liked ? ' is-active' : ''; ?>"
                            data-cf-post-like
                            data-post-id="<?php echo esc_attr( (string) $post_id ); ?>"
                            <?php echo is_user_logged_in() ? '' : 'disabled'; ?>
                        >
                            <span class="dashicons dashicons-heart"></span>
                            <span data-like-count><?php echo esc_html( (string) $likes_count ); ?></span>
                        </button>
                        <?php
                        if ( function_exists( 'collective_finity_render_share_buttons' ) ) {
                            collective_finity_render_share_buttons( get_permalink(), get_the_title(), 'post' );
                        }
                        ?>
                    </div>

                    <div class="cf-post-toc-accordion cf-faq__item" data-cf-toc-accordion>
                        <button type="button" class="cf-faq__trigger" aria-expanded="false">
                            <span><?php esc_html_e( 'Contents', 'collective-finity' ); ?></span>
                            <span class="cf-faq__chevron" aria-hidden="true"><span class="dashicons dashicons-arrow-down-alt2"></span></span>
                        </button>
                        <div class="cf-faq__panel" hidden>
                            <nav class="cf-post-toc__list" data-cf-post-toc" aria-label="<?php esc_attr_e( 'Table of contents', 'collective-finity' ); ?>"></nav>
                        </div>
                    </div>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="cf-post-featured-image"><?php the_post_thumbnail( 'large' ); ?></div>
                    <?php endif; ?>

                    <div class="cf-post-content entry-content">
                        <?php the_content(); ?>
                    </div>

                    <section class="cf-author-bio" aria-labelledby="cf-author-bio-heading">
                        <h2 id="cf-author-bio-heading" class="screen-reader-text"><?php esc_html_e( 'Author', 'collective-finity' ); ?></h2>
                        <?php echo get_avatar( $author_id, 96 ); ?>
                        <div>
                            <h3 style="margin:0 0 6px;font-size:16px;color:#fff;"><?php the_author(); ?></h3>
                            <p style="margin:0;font-size:13.5px;line-height:1.6;color:#B3B3B3;"><?php echo esc_html( get_the_author_meta( 'description' ) ?: __( 'Producer and writer on the Collective Finity team, covering the intersection of AI tools and independent music production.', 'collective-finity' ) ); ?></p>
                        </div>
                    </section>

                    <section class="cf-newsletter-box" aria-labelledby="cf-newsletter-heading">
                        <h2 id="cf-newsletter-heading" style="margin:0 0 12px;font-size:18px;color:#fff;"><?php esc_html_e( 'Get notified about new courses and tutorials', 'collective-finity' ); ?></h2>
                        <div class="cf-form-wrap">
                            <?php echo do_shortcode( '[contact-form-7 id="a1d896d" title="Subscription Form"]' ); ?>
                        </div>
                    </section>

                    <?php if ( $related_query->have_posts() ) : ?>
                        <section class="cf-related-wrap" aria-labelledby="cf-related-heading">
                            <h2 id="cf-related-heading" class="cf-form-section__title"><?php esc_html_e( 'Related Articles', 'collective-finity' ); ?></h2>
                            <div class="cf-related-grid">
                                <?php
                                while ( $related_query->have_posts() ) :
                                    $related_query->the_post();
                                    $cats = get_the_category();
                                    $cat  = ! empty( $cats ) ? $cats[0]->name : '';
                                    ?>
                                    <a class="cf-blog-card" href="<?php the_permalink(); ?>">
                                        <div class="cf-blog-card__media">
                                            <?php if ( has_post_thumbnail() ) : ?>
                                                <?php the_post_thumbnail( 'medium_large' ); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="cf-blog-card__body">
                                            <?php if ( $cat ) : ?>
                                                <span class="cf-blog-card__category"><?php echo esc_html( $cat ); ?></span>
                                            <?php endif; ?>
                                            <h3 class="cf-blog-card__title"><?php the_title(); ?></h3>
                                            <p class="cf-blog-card__meta"><?php echo esc_html( get_the_date() ); ?></p>
                                        </div>
                                    </a>
                                <?php endwhile; ?>
                                <?php wp_reset_postdata(); ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <?php if ( ! empty( $faq_items ) ) : ?>
                        <section class="cf-post-faq" aria-labelledby="cf-post-faq-heading">
                            <h2 id="cf-post-faq-heading" class="cf-form-section__title"><?php esc_html_e( 'Frequently Asked Questions', 'collective-finity' ); ?></h2>
                            <div class="cf-faq__list" data-cf-accordion>
                                <?php foreach ( $faq_items as $index => $faq_item ) : ?>
                                    <div class="cf-faq__item<?php echo 0 === $index ? ' is-open' : ''; ?>">
                                        <button type="button" class="cf-faq__trigger" aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>">
                                            <span><?php echo esc_html( $faq_item['question'] ?? '' ); ?></span>
                                            <span class="cf-faq__chevron" aria-hidden="true"><span class="dashicons dashicons-arrow-down-alt2"></span></span>
                                        </button>
                                        <div class="cf-faq__panel" <?php echo 0 === $index ? '' : 'hidden'; ?>>
                                            <p><?php echo esc_html( $faq_item['answer'] ?? '' ); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <section class="cf-reviews-section" aria-labelledby="cf-reviews-heading">
                        <h2 id="cf-reviews-heading" class="cf-form-section__title"><?php esc_html_e( 'Ratings & Comments', 'collective-finity' ); ?></h2>
                        <?php if ( $rating_count ) : ?>
                            <div class="cf-reviews-summary">
                                <strong><?php echo esc_html( (string) $avg_rating ); ?>/5</strong>
                                <span><?php printf( esc_html( _n( '%d review', '%d reviews', $rating_count, 'collective-finity' ) ), (int) $rating_count ); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="cf-review-form-gate<?php echo is_user_logged_in() ? '' : ' is-locked'; ?>">
                            <div class="cf-review-form-gate__inner">
                                <?php if ( is_user_logged_in() ) : ?>
                                    <?php
                                    comment_form(
                                        array(
                                            'title_reply'          => '',
                                            'label_submit'         => __( 'Submit Review', 'collective-finity' ),
                                            'comment_notes_before' => '',
                                            'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'collective-finity' ) . '</label><textarea id="comment" name="comment" cols="45" rows="5" maxlength="65525" required></textarea></p>',
                                            'fields'               => array(
                                                'author'  => '',
                                                'email'   => '',
                                                'url'     => '',
                                                'cookies' => '',
                                            ),
                                        )
                                    );
                                    ?>
                                <?php endif; ?>
                            </div>
                            <div class="cf-review-form-gate__overlay">
                                <p><?php esc_html_e( 'Sign in to leave a review', 'collective-finity' ); ?></p>
                                <a class="cf-btn cf-btn--primary" href="<?php echo esc_url( home_url( '/cf-login/' ) ); ?>"><?php esc_html_e( 'Log In', 'collective-finity' ); ?></a>
                            </div>
                        </div>

                        <?php if ( ! empty( $review_comments ) ) : ?>
                            <div class="cf-reviews-list">
                                <?php foreach ( $review_comments as $review_comment ) : ?>
                                    <?php $rating = (int) get_comment_meta( $review_comment->comment_ID, '_cf_review_rating', true ); ?>
                                    <article class="cf-review-item">
                                        <div class="cf-post-byline" style="margin-bottom:8px;">
                                            <?php echo get_avatar( $review_comment, 36 ); ?>
                                            <span><?php echo esc_html( $review_comment->comment_author ); ?><?php echo $rating ? ' · ' . esc_html( (string) $rating ) . '/5' : ''; ?></span>
                                        </div>
                                        <p style="margin:0;color:#B3B3B3;line-height:1.6;"><?php echo esc_html( $review_comment->comment_content ); ?></p>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>
                </article>

                <aside class="cf-post-toc" aria-label="<?php esc_attr_e( 'Table of contents', 'collective-finity' ); ?>">
                    <p class="cf-post-toc__label"><?php esc_html_e( 'CONTENTS', 'collective-finity' ); ?></p>
                    <nav class="cf-post-toc__list" data-cf-post-toc></nav>
                </aside>
            </div>
        </div>
    </main>

    <?php
endwhile;

get_footer();
