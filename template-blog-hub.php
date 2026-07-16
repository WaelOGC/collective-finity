<?php
/**
 * Template Name: Blog Hub
 * Description: Blog landing page — featured post, category filter chips wired to
 * real WordPress categories, and a paginated grid of real posts.
 *
 * Translated from design-reference/Blog-Hub.dc.html (+ Category-Archive.dc.html for
 * the filtered state). Assign this template to a Page (recommended slug: "blog"),
 * which is what the sidebar/footer "Blog" links resolve to.
 *
 * @package Collective_Finity
 */

get_header();

$cf_base_url     = get_permalink();
$cf_selected_cat = sanitize_title( get_query_var( 'blog_cat' ) );
if ( '' === $cf_selected_cat && isset( $_GET['blog_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $cf_selected_cat = sanitize_title( wp_unslash( $_GET['blog_cat'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}
$cf_paged = isset( $_GET['pg'] ) ? max( 1, (int) $_GET['pg'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$cf_active_term = $cf_selected_cat ? get_category_by_slug( $cf_selected_cat ) : null;
if ( ! $cf_active_term ) {
    $cf_selected_cat = '';
}

// Featured post (latest) — only shown on the unfiltered first page.
$cf_featured = null;
if ( '' === $cf_selected_cat && 1 === $cf_paged ) {
    $cf_featured_q = new WP_Query(
        array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => 1,
            'ignore_sticky_posts' => false,
        )
    );
    if ( $cf_featured_q->have_posts() ) {
        $cf_featured = $cf_featured_q->posts[0];
    }
    wp_reset_postdata();
}

// Ticker: 3 most recent posts (same real posts, shown above the featured card).
$cf_ticker_items = array();
if ( '' === $cf_selected_cat && 1 === $cf_paged ) {
    $cf_ticker_q = new WP_Query(
        array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 3,
            'fields'         => 'ids',
        )
    );
    foreach ( $cf_ticker_q->posts as $cf_ticker_id ) {
        $cf_ticker_items[] = array(
            'id'    => $cf_ticker_id,
            'title' => get_the_title( $cf_ticker_id ),
        );
    }
    wp_reset_postdata();
}

// Main grid query.
$cf_grid_args = array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 9,
    'paged'               => $cf_paged,
    'ignore_sticky_posts' => true,
);
if ( $cf_selected_cat ) {
    $cf_grid_args['category_name'] = $cf_selected_cat;
} elseif ( $cf_featured ) {
    $cf_grid_args['post__not_in'] = array( $cf_featured->ID );
}
$cf_grid = new WP_Query( $cf_grid_args );

$cf_categories = get_categories(
    array(
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    )
);
?>

<div class="cf-blog cf-bloghub">

    <?php if ( $cf_active_term ) : ?>
        <nav class="cf-bh-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'collective-finity' ); ?>">
            <a href="<?php echo esc_url( $cf_base_url ); ?>"><?php esc_html_e( 'Blog', 'collective-finity' ); ?></a>
            <span aria-hidden="true">/</span>
            <span class="cf-bh-breadcrumb-current"><?php echo esc_html( $cf_active_term->name ); ?></span>
        </nav>
        <header class="cf-bh-cat-head">
            <h1 class="cf-bh-title"><?php echo esc_html( $cf_active_term->name ); ?></h1>
            <?php if ( $cf_active_term->description ) : ?>
                <p class="cf-bh-cat-desc"><?php echo esc_html( $cf_active_term->description ); ?></p>
            <?php endif; ?>
        </header>
    <?php else : ?>
        <?php if ( ! empty( $cf_ticker_items ) ) : ?>
            <div class="cf-bh-ticker" aria-label="<?php esc_attr_e( 'Recent posts', 'collective-finity' ); ?>">
                <span class="cf-bh-ticker-label"><?php esc_html_e( 'Blog', 'collective-finity' ); ?></span>
                <span class="cf-bh-ticker-divider" aria-hidden="true"></span>
                <div class="cf-bh-ticker-viewport">
                    <div class="cf-bh-ticker-track">
                        <?php
                        // Output the sequence twice so the translateX(-50%) loop is seamless.
                        for ( $cf_ticker_copy = 0; $cf_ticker_copy < 2; $cf_ticker_copy++ ) :
                            foreach ( $cf_ticker_items as $cf_ticker_item ) :
                                $cf_ticker_words = preg_split( '/\s+/u', trim( (string) $cf_ticker_item['title'] ) );
                                if ( ! is_array( $cf_ticker_words ) || empty( $cf_ticker_words ) ) {
                                    $cf_ticker_words = array( (string) $cf_ticker_item['title'] );
                                }
                                ?>
                                <a class="cf-bh-ticker-item" href="<?php echo esc_url( get_permalink( $cf_ticker_item['id'] ) ); ?>"<?php echo 1 === $cf_ticker_copy ? ' aria-hidden="true" tabindex="-1"' : ''; ?>><?php
                                foreach ( $cf_ticker_words as $cf_ticker_word_i => $cf_ticker_word ) :
                                    if ( $cf_ticker_word_i > 0 ) {
                                        echo ' ';
                                    }
                                    ?><span class="cf-bh-ticker-word" style="--cf-bh-word-delay: <?php echo esc_attr( (string) ( $cf_ticker_word_i * 0.18 ) ); ?>s"><?php echo esc_html( $cf_ticker_word ); ?></span><?php
                                endforeach;
                                ?></a>
                                <span class="cf-bh-ticker-sep" aria-hidden="true">&bull;</span>
                                <?php
                            endforeach;
                        endfor;
                        ?>
                    </div>
                </div>
                <span class="cf-bh-ticker-arrow" aria-hidden="true">&rarr;</span>
            </div>
        <?php endif; ?>

        <?php if ( $cf_featured ) : ?>
            <?php
            $cf_f_cat  = collective_finity_post_primary_category( $cf_featured );
            $cf_f_name = $cf_f_cat ? $cf_f_cat->name : __( 'Article', 'collective-finity' );
            $cf_f_exc  = has_excerpt( $cf_featured ) ? get_the_excerpt( $cf_featured ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $cf_featured->post_content ) ), 34 );
            ?>
            <a class="cf-bh-featured" href="<?php echo esc_url( get_permalink( $cf_featured ) ); ?>">
                <?php echo collective_finity_post_media_markup( $cf_featured->ID, 'large', 'cf-bh-featured-art' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <div class="cf-bh-featured-body">
                    <span class="cf-bh-featured-tag"><?php echo esc_html( strtoupper( $cf_f_name ) ); ?> &middot; <?php esc_html_e( 'FEATURED', 'collective-finity' ); ?></span>
                    <h2 class="cf-bh-featured-title"><?php echo esc_html( get_the_title( $cf_featured ) ); ?></h2>
                    <p class="cf-bh-featured-excerpt"><?php echo esc_html( $cf_f_exc ); ?></p>
                    <span class="cf-bh-featured-meta"><?php echo esc_html( get_the_date( '', $cf_featured ) ); ?> &middot; <?php echo esc_html( collective_finity_reading_time( $cf_featured ) ); ?></span>
                </div>
            </a>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ( ! empty( $cf_categories ) ) : ?>
        <div class="cf-bh-chips" role="list">
            <a role="listitem" class="cf-bh-chip<?php echo '' === $cf_selected_cat ? ' is-active' : ''; ?>" href="<?php echo esc_url( $cf_base_url ); ?>"><?php esc_html_e( 'All', 'collective-finity' ); ?></a>
            <?php foreach ( $cf_categories as $cf_cat ) : ?>
                <a role="listitem"
                    class="cf-bh-chip<?php echo $cf_selected_cat === $cf_cat->slug ? ' is-active' : ''; ?>"
                    href="<?php echo esc_url( add_query_arg( 'blog_cat', $cf_cat->slug, $cf_base_url ) ); ?>">
                    <?php echo esc_html( $cf_cat->name ); ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ( $cf_grid->have_posts() ) : ?>
        <?php collective_finity_ad_slot( 'blog_listing' ); ?>
        <div class="cf-bh-grid">
            <?php
            while ( $cf_grid->have_posts() ) :
                $cf_grid->the_post();
                echo collective_finity_render_blog_card( get_post(), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            endwhile;
            ?>
        </div>

        <?php
        $cf_total = (int) $cf_grid->max_num_pages;
        if ( $cf_total > 1 ) :
            $cf_links = paginate_links(
                array(
                    'base'      => $cf_base_url . '%_%',
                    'format'    => ( false !== strpos( $cf_base_url, '?' ) ? '&' : '?' ) . 'pg=%#%',
                    'current'   => $cf_paged,
                    'total'     => $cf_total,
                    'type'      => 'array',
                    'add_args'  => $cf_selected_cat ? array( 'blog_cat' => $cf_selected_cat ) : array(),
                    'prev_text' => __( '&larr; Prev', 'collective-finity' ),
                    'next_text' => __( 'Next &rarr;', 'collective-finity' ),
                )
            );
            if ( $cf_links ) :
                ?>
                <nav class="cf-bh-pagination" aria-label="<?php esc_attr_e( 'Blog pagination', 'collective-finity' ); ?>">
                    <?php foreach ( $cf_links as $cf_link ) : ?>
                        <?php echo wp_kses_post( $cf_link ); ?>
                    <?php endforeach; ?>
                </nav>
                <?php
            endif;
        endif;
        ?>
    <?php else : ?>
        <p class="cf-bh-empty">
            <?php
            echo $cf_active_term
                ? esc_html__( 'No posts in this category yet.', 'collective-finity' )
                : esc_html__( 'No posts published yet. Check back soon.', 'collective-finity' );
            ?>
        </p>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
</div>

<style>
    .cf-blog { padding: 30px clamp(16px, 3vw, 34px); display: flex; flex-direction: column; gap: 26px; max-width: 100%; min-width: 0; box-sizing: border-box; }
    .cf-bh-title { font-size: 26px; font-weight: 700; color: #fff; margin: 0; }

    /* breadcrumb + category head */
    .cf-bh-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--cf-text-3); flex-wrap: wrap; }
    .cf-bh-breadcrumb a { color: var(--cf-text-3); text-decoration: none; }
    .cf-bh-breadcrumb a:hover { color: var(--cf-accent); }
    .cf-bh-breadcrumb-current { color: var(--cf-accent); }
    .cf-bh-cat-head { display: flex; flex-direction: column; gap: 8px; }
    .cf-bh-cat-desc { font-size: 13.5px; color: var(--cf-text-2); max-width: 560px; line-height: 1.6; margin: 0; }

    /* ticker bar */
    .cf-bh-ticker { display: flex; align-items: center; gap: 14px; border: 1px solid var(--cf-accent); background: var(--cf-bg-card); border-radius: 999px; padding: 9px 18px; overflow: hidden; }
    .cf-bh-ticker-label { flex-shrink: 0; font-family: var(--cf-mono); font-size: 12px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; color: var(--cf-accent); }
    .cf-bh-ticker-divider { flex-shrink: 0; width: 1px; height: 16px; background: var(--cf-accent); opacity: 0.5; }
    .cf-bh-ticker-viewport { flex: 1 1 auto; min-width: 0; overflow: hidden; }
    .cf-bh-ticker-track { display: inline-flex; align-items: center; white-space: nowrap; will-change: transform; animation: cf-bh-ticker-scroll 18s linear infinite; }
    .cf-bh-ticker-item { font-size: 12.5px; color: #fff; padding: 0 6px; text-decoration: none; }
    .cf-bh-ticker-item:hover { text-decoration: underline; }
    .cf-bh-ticker-word {
        display: inline-block;
        color: #fff;
        animation: cfBhTickerWordFlicker 3.2s ease-in-out infinite;
        animation-delay: var(--cf-bh-word-delay, 0s);
    }
    @keyframes cfBhTickerWordFlicker {
        0%, 12%, 100% {
            color: #fff;
            text-shadow: none;
        }
        4%, 8% {
            color: #FFD060;
            text-shadow: 0 0 8px rgba(255, 183, 0, 0.55), 0 0 16px rgba(255, 183, 0, 0.22);
        }
    }
    .cf-bh-ticker-sep { color: var(--cf-accent); opacity: 0.7; }
    .cf-bh-ticker-arrow { flex-shrink: 0; font-size: 15px; line-height: 1; color: var(--cf-accent); }
    @keyframes cf-bh-ticker-scroll { from { transform: translateX(0); } to { transform: translateX(-50%); } }
    @media (prefers-reduced-motion: reduce) {
        .cf-bh-ticker-track { animation: none; }
        .cf-bh-ticker-word {
            animation: none;
            color: #fff;
            text-shadow: none;
        }
    }

    /* featured card */
    .cf-bh-featured { display: flex; flex-direction: row; text-decoration: none; border: 1px solid var(--cf-border); background: var(--cf-bg-card); border-radius: 14px; overflow: hidden; color: #fff; transition: border-color 0.2s ease; min-width: 0; max-width: 100%; }
    .cf-bh-featured:hover { border-color: var(--cf-border-strong); }
    .cf-bh-featured-body { padding: 28px; display: flex; flex-direction: column; gap: 10px; justify-content: center; flex: 1 1 auto; min-width: 0; }
    .cf-bh-featured-tag { font-family: var(--cf-mono); font-size: 11px; color: var(--cf-accent); }
    .cf-bh-featured-title { font-size: 24px; font-weight: 700; line-height: 1.3; margin: 0; color: #fff; }
    .cf-bh-featured-excerpt { font-size: 13.5px; color: var(--cf-text-2); line-height: 1.6; margin: 0; }
    .cf-bh-featured-meta { font-size: 12px; color: var(--cf-text-3); font-family: var(--cf-mono); }

    /* chips */
    .cf-bh-chips { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; }
    .cf-bh-chip { padding: 8px 14px; border-radius: 999px; border: 1px solid var(--cf-border); background: transparent; color: var(--cf-text-2); font-size: 12.5px; font-weight: 500; text-decoration: none; white-space: nowrap; transition: color 0.2s ease, border-color 0.2s ease, background 0.2s ease; }
    .cf-bh-chip:hover { color: #fff; border-color: var(--cf-border-strong); }
    .cf-bh-chip.is-active { border-color: var(--cf-accent); background: var(--cf-accent-dim); color: var(--cf-accent); font-weight: 700; }

    /* grid — column count adapts via cf-content-layout.css @container cf-main */
    .cf-bh-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr)); gap: 18px; min-width: 0; }

    /* pagination */
    .cf-bh-pagination { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px; }
    .cf-bh-pagination a, .cf-bh-pagination span { display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 9px; border: 1px solid var(--cf-border); background: var(--cf-bg-card); color: var(--cf-text-2); text-decoration: none; font-size: 13px; }
    .cf-bh-pagination a:hover { color: #fff; border-color: var(--cf-border-strong); }
    .cf-bh-pagination .current { background: var(--cf-accent-dim); border-color: var(--cf-accent); color: var(--cf-accent); font-weight: 700; }
    .cf-bh-pagination .dots { border-color: transparent; background: transparent; }

    .cf-bh-empty { color: var(--cf-text-3); font-size: 13.5px; }

    @media (max-width: 767px) {
        .cf-blog { padding: 18px 16px; gap: 22px; }
        .cf-bh-featured { flex-direction: column; }
        .cf-bh-featured-body { padding: 20px; }
        .cf-bh-featured-title { font-size: 19px; }
    }
</style>

<?php
get_footer();
