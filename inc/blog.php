<?php
/**
 * Blog helpers: reading time, TOC generation, star ratings (native comments),
 * primary category, and review avatars.
 *
 * These back the Blog Hub (template-blog-hub.php) and Single Post (single-post.php)
 * templates translated from design-reference/Blog-Hub.dc.html + Blog-Post.dc.html.
 *
 * Ratings are stored on WordPress' NATIVE comments system as a per-comment meta
 * value ('cf_rating', 1-5). This keeps reviews, moderation, avatars and author
 * data consistent with anything else that reads WP comments later.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Estimate reading time for a post from its word count (~200 wpm).
 *
 * @param int|WP_Post|null $post Post to measure (defaults to current).
 * @return string Localized string, e.g. "6 min read".
 */
function collective_finity_reading_time( $post = null ) {
    $post = get_post( $post );
    if ( ! $post ) {
        return '';
    }

    $text    = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
    $words   = max( 1, str_word_count( $text ) );
    $minutes = max( 1, (int) ceil( $words / 200 ) );

    /* translators: %d: estimated reading time in minutes. */
    return sprintf( _n( '%d min read', '%d min read', $minutes, 'collective-finity' ), $minutes );
}

/**
 * Primary (first) category term for a post.
 *
 * @param int|WP_Post|null $post Post.
 * @return WP_Term|null
 */
function collective_finity_post_primary_category( $post = null ) {
    $post = get_post( $post );
    if ( ! $post ) {
        return null;
    }
    $cats = get_the_category( $post->ID );
    return ! empty( $cats ) ? $cats[0] : null;
}

/**
 * Aggregate rating data for a post from its approved comments' 'cf_rating' meta.
 *
 * @param int|WP_Post|null $post Post.
 * @return array{avg: float, count: int}
 */
function collective_finity_post_rating_data( $post = null ) {
    $post = get_post( $post );
    if ( ! $post ) {
        return array( 'avg' => 0.0, 'count' => 0 );
    }

    $comments = get_comments(
        array(
            'post_id' => $post->ID,
            'status'  => 'approve',
            'type'    => 'comment',
            'meta_key' => 'cf_rating', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
        )
    );

    $sum   = 0;
    $count = 0;
    foreach ( $comments as $comment ) {
        $rating = (int) get_comment_meta( $comment->comment_ID, 'cf_rating', true );
        if ( $rating >= 1 && $rating <= 5 ) {
            $sum += $rating;
            $count++;
        }
    }

    return array(
        'avg'   => $count ? round( $sum / $count, 1 ) : 0.0,
        'count' => $count,
    );
}

/**
 * Render 5 stars filled up to $rating (read-only display).
 *
 * @param float $rating Rating value 0-5.
 * @param int   $size   Icon pixel size.
 * @return string HTML.
 */
function collective_finity_stars_markup( $rating, $size = 16 ) {
    $rounded = (int) round( $rating );
    $out     = '<span class="cf-stars" role="img" aria-label="' . esc_attr(
        sprintf(
            /* translators: %s: rating value out of 5. */
            __( '%s out of 5 stars', 'collective-finity' ),
            number_format_i18n( $rating, 1 )
        )
    ) . '">';
    for ( $i = 1; $i <= 5; $i++ ) {
        $on   = $i <= $rounded;
        $out .= '<span class="cf-star' . ( $on ? ' is-on' : '' ) . '">'
            . collective_finity_icon( 'star', $size, true )
            . '</span>';
    }
    $out .= '</span>';
    return $out;
}

/**
 * Render an interactive 5-star radio input (works without JS; enhanced by
 * a tiny inline hover script in the single-post template).
 *
 * @param int $size Icon pixel size.
 * @return string HTML.
 */
function collective_finity_star_input_markup( $size = 22 ) {
    $out = '<span class="cf-stars-input" role="radiogroup" aria-label="' . esc_attr__( 'Your rating', 'collective-finity' ) . '">';
    // Reverse order so CSS sibling selectors can light up lower stars on hover/check.
    for ( $i = 5; $i >= 1; $i-- ) {
        $id   = 'cf-star-' . $i;
        $out .= '<input type="radio" name="cf_rating" id="' . esc_attr( $id ) . '" value="' . esc_attr( $i ) . '">';
        $out .= '<label for="' . esc_attr( $id ) . '" title="' . esc_attr(
            sprintf(
                /* translators: %d: number of stars. */
                _n( '%d star', '%d stars', $i, 'collective-finity' ),
                $i
            )
        ) . '">' . collective_finity_icon( 'star', $size, true ) . '<span class="screen-reader-text">' . esc_html( $i ) . '</span></label>';
    }
    $out .= '</span>';
    return $out;
}

/**
 * Avatar markup for a review author: real avatar if available, else a
 * gradient tile with their initial (matches the design system).
 *
 * @param WP_Comment|WP_User|int $source Comment, user, or user ID.
 * @param int                    $size   Pixel size.
 * @return string HTML.
 */
function collective_finity_review_avatar( $source, $size = 38 ) {
    $name   = '';
    $avatar = '';

    if ( $source instanceof WP_Comment ) {
        $name   = $source->comment_author;
        $avatar = get_avatar( $source, $size, '', $name, array( 'class' => 'cf-review-avatar-img' ) );
    } elseif ( $source instanceof WP_User ) {
        $name   = $source->display_name;
        $avatar = get_avatar( $source->ID, $size, '', $name, array( 'class' => 'cf-review-avatar-img' ) );
    } elseif ( is_numeric( $source ) ) {
        $user   = get_userdata( (int) $source );
        $name   = $user ? $user->display_name : '';
        $avatar = get_avatar( (int) $source, $size, '', $name, array( 'class' => 'cf-review-avatar-img' ) );
    }

    $initial = $name ? strtoupper( mb_substr( $name, 0, 1 ) ) : '?';

    $style = sprintf( 'width:%1$dpx;height:%1$dpx;min-width:%1$dpx;', (int) $size );

    if ( $avatar ) {
        return '<span class="cf-review-avatar" style="' . esc_attr( $style ) . '">' . $avatar . '</span>';
    }

    return '<span class="cf-review-avatar cf-review-avatar--initial" style="' . esc_attr( $style ) . '">' . esc_html( $initial ) . '</span>';
}

/**
 * Parse rendered post HTML, inject IDs on H2/H3 headings, and build a TOC.
 *
 * @param string $html Rendered post content (post-the_content filters).
 * @return array{content: string, toc: array<int, array{id:string,text:string,level:string}>}
 */
function collective_finity_build_post_toc( $html ) {
    $toc = array();

    if ( '' === trim( (string) $html ) || ! class_exists( 'DOMDocument' ) ) {
        return array( 'content' => $html, 'toc' => $toc );
    }

    $dom      = new DOMDocument();
    $previous = libxml_use_internal_errors( true );
    // Force UTF-8 handling and wrap so we can extract inner HTML cleanly.
    $dom->loadHTML(
        '<?xml encoding="utf-8"?><div id="cf-toc-root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();
    libxml_use_internal_errors( $previous );

    $xpath = new DOMXPath( $dom );
    $nodes = $xpath->query( '//h2 | //h3' );

    $used = array();
    foreach ( $nodes as $node ) {
        $text = trim( $node->textContent );
        if ( '' === $text ) {
            continue;
        }

        $id = $node->getAttribute( 'id' );
        if ( '' === $id ) {
            $base = sanitize_title( $text );
            if ( '' === $base ) {
                $base = 'section';
            }
            $id = $base;
            $n  = 2;
            while ( isset( $used[ $id ] ) ) {
                $id = $base . '-' . $n;
                $n++;
            }
            $node->setAttribute( 'id', $id );
        }
        $used[ $id ] = true;

        $toc[] = array(
            'id'    => $id,
            'text'  => $text,
            'level' => strtolower( $node->nodeName ),
        );
    }

    // Rebuild inner HTML of the wrapper.
    $root    = $dom->getElementById( 'cf-toc-root' );
    $content = '';
    if ( $root ) {
        foreach ( $root->childNodes as $child ) {
            $content .= $dom->saveHTML( $child );
        }
    } else {
        $content = $html;
    }

    return array( 'content' => $content, 'toc' => $toc );
}

/**
 * Media (thumbnail or gradient fallback) markup for a blog card.
 *
 * @param int    $post_id   Post ID.
 * @param string $size      Image size.
 * @param string $extra_cls Extra CSS class for the wrapper.
 * @return string HTML.
 */
function collective_finity_post_media_markup( $post_id, $size = 'large', $extra_cls = '' ) {
    $cls = trim( 'cf-bh-card-art ' . $extra_cls );
    if ( has_post_thumbnail( $post_id ) ) {
        return '<div class="' . esc_attr( $cls ) . '">'
            . get_the_post_thumbnail( $post_id, $size, array( 'loading' => 'lazy', 'alt' => the_title_attribute( array( 'echo' => false, 'post' => $post_id ) ) ) )
            . '</div>';
    }

    $gradient = function_exists( 'collective_finity_gradient_for' )
        ? collective_finity_gradient_for( $post_id )
        : 'linear-gradient(135deg,#1a1a1a,#0d0d0d)';

    return '<div class="' . esc_attr( $cls ) . ' cf-bh-card-art--grad" style="background:' . esc_attr( $gradient ) . '"></div>';
}

/**
 * Render a blog post card (used by the Blog Hub grid and Related Articles).
 *
 * @param int|WP_Post $post         Post.
 * @param bool        $show_excerpt Whether to include excerpt + meta row.
 * @return string HTML.
 */
function collective_finity_render_blog_card( $post, $show_excerpt = true ) {
    $post = get_post( $post );
    if ( ! $post ) {
        return '';
    }

    $cat      = collective_finity_post_primary_category( $post );
    $cat_name = $cat ? $cat->name : __( 'Article', 'collective-finity' );

    $html  = '<a class="cf-bh-card" href="' . esc_url( get_permalink( $post ) ) . '">';
    $html .= collective_finity_post_media_markup( $post->ID, 'large' );
    $html .= '<div class="cf-bh-card-body">';
    $html .= '<span class="cf-bh-card-cat">' . esc_html( strtoupper( $cat_name ) ) . '</span>';
    $html .= '<h3 class="cf-bh-card-title">' . esc_html( get_the_title( $post ) ) . '</h3>';

    if ( $show_excerpt ) {
        $excerpt = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 22 );
        $html   .= '<p class="cf-bh-card-excerpt">' . esc_html( $excerpt ) . '</p>';
        $html   .= '<span class="cf-bh-card-meta">' . esc_html( get_the_date( '', $post ) ) . ' &middot; ' . esc_html( collective_finity_reading_time( $post ) ) . '</span>';
    }

    $html .= '</div></a>';
    return $html;
}

/**
 * Persist the star rating submitted with a comment as comment meta.
 *
 * @param int        $comment_id The new comment ID.
 * @param int|string $approved   Approval status.
 */
function collective_finity_save_comment_rating( $comment_id, $approved ) {
    unset( $approved );
    if ( isset( $_POST['cf_rating'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- core handles comment nonce/flood in wp-comments-post.php.
        $rating = (int) wp_unslash( $_POST['cf_rating'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if ( $rating >= 1 && $rating <= 5 ) {
            add_comment_meta( $comment_id, 'cf_rating', $rating, true );
        }
    }
}
add_action( 'comment_post', 'collective_finity_save_comment_rating', 10, 2 );

/**
 * Register a private query var used by the Blog Hub to filter by category slug.
 *
 * @param array $vars Query vars.
 * @return array
 */
function collective_finity_blog_query_vars( $vars ) {
    $vars[] = 'blog_cat';
    return $vars;
}
add_filter( 'query_vars', 'collective_finity_blog_query_vars' );

/**
 * Total like count for a blog post (stored in post meta).
 *
 * @param int|WP_Post|null $post Post.
 * @return int
 */
function collective_finity_post_likes_count( $post = null ) {
    $post = get_post( $post );
    if ( ! $post ) {
        return 0;
    }

    return max( 0, (int) get_post_meta( $post->ID, '_cf_total_likes_count', true ) );
}

/**
 * Whether the given user has liked a blog post.
 *
 * @param int $post_id Post ID.
 * @param int $user_id User ID (defaults to current user).
 * @return bool
 */
function collective_finity_user_liked_post( $post_id, $user_id = 0 ) {
    $post_id = (int) $post_id;
    if ( ! $post_id ) {
        return false;
    }

    if ( ! $user_id ) {
        $user_id = get_current_user_id();
    }
    if ( ! $user_id ) {
        return false;
    }

    $liked_posts = get_user_meta( $user_id, 'cf_favorite_posts', true );
    if ( ! is_array( $liked_posts ) ) {
        return false;
    }

    return in_array( $post_id, array_map( 'intval', $liked_posts ), true );
}

/**
 * Enqueue shared blog card styles on Blog Hub and single posts.
 */
function collective_finity_enqueue_blog_card_styles() {
    if ( ! is_singular( 'post' ) && ! is_page_template( 'template-blog-hub.php' ) ) {
        return;
    }

    $path = get_template_directory() . '/assets/css/cf-blog-cards.css';
    $ver  = file_exists( $path ) ? filemtime( $path ) : wp_get_theme()->get( 'Version' );

    wp_enqueue_style(
        'cf-blog-cards',
        get_template_directory_uri() . '/assets/css/cf-blog-cards.css',
        array( 'cf-shell' ),
        $ver
    );
}
add_action( 'wp_enqueue_scripts', 'collective_finity_enqueue_blog_card_styles', 20 );

/**
 * View count for a blog post.
 *
 * @param int|WP_Post|null $post Post.
 * @return int
 */
function collective_finity_post_view_count( $post = null ) {
    $post = get_post( $post );
    if ( ! $post ) {
        return 0;
    }

    return max( 0, (int) get_post_meta( $post->ID, '_cf_view_count', true ) );
}

/**
 * Increment a post's view counter once per browser session (cookie dedup).
 *
 * @param int $post_id Post ID.
 */
function collective_finity_track_post_view( $post_id ) {
    $post_id = (int) $post_id;
    if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
        return;
    }

    $cookie_name = 'cf_pv_' . $post_id;
    if ( isset( $_COOKIE[ $cookie_name ] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
        return;
    }

    $count = collective_finity_post_view_count( $post_id ) + 1;
    update_post_meta( $post_id, '_cf_view_count', $count );

    if ( ! headers_sent() ) {
        setcookie( $cookie_name, '1', time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
    }
}

/**
 * Track views on single blog posts (before template output).
 */
function collective_finity_maybe_track_post_view() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }

    collective_finity_track_post_view( get_queried_object_id() );
}
add_action( 'template_redirect', 'collective_finity_maybe_track_post_view' );

/**
 * Popular blog posts for sidebar widgets (by view count, backfilled by recency).
 *
 * @param int $exclude_id Post ID to exclude.
 * @param int $limit      Number of posts.
 * @return WP_Post[]
 */
function collective_finity_get_popular_posts( $exclude_id, $limit = 3 ) {
    $exclude_id = (int) $exclude_id;
    $limit      = max( 1, (int) $limit );

    $popular = get_posts(
        array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => $limit,
            'post__not_in'        => array( $exclude_id ),
            'orderby'             => 'meta_value_num',
            'meta_key'            => '_cf_view_count', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
        )
    );

    if ( count( $popular ) >= $limit ) {
        return $popular;
    }

    $have    = wp_list_pluck( $popular, 'ID' );
    $exclude = array_merge( array( $exclude_id ), $have );

    $recent = get_posts(
        array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => $limit - count( $popular ),
            'post__not_in'        => $exclude,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
        )
    );

    return array_merge( $popular, $recent );
}

/**
 * Sidebar ad slot for blog posts (uses the single_post zone from Ad Manager).
 */
function collective_finity_render_blog_sidebar_ad() {
    if ( function_exists( 'collective_finity_ad_slot' ) ) {
        collective_finity_ad_slot( 'single_post' );
    }
}
