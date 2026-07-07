<?php
/**
 * Blog hub layout partial.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$blog_url      = get_post_type_archive_link( 'post' );
if ( ! $blog_url ) {
    $posts_page_id = (int) get_option( 'page_for_posts' );
    $blog_url      = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' );
}

$featured_query = new WP_Query(
    array(
        'post_type'      => 'post',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    )
);

$posts_query = new WP_Query(
    array(
        'post_type'      => 'post',
        'posts_per_page' => 12,
        'post_status'    => 'publish',
        'offset'         => 1,
    )
);

$categories = cf_get_blog_hub_categories();
$active_cat = '';
?>
<div class="cf-page-inner cf-page-inner--wide cf-blog-hub">
    <h1 class="cf-blog-hub__title"><?php esc_html_e( 'Blog', 'collective-finity' ); ?></h1>

    <?php if ( $featured_query->have_posts() ) : ?>
        <?php
        while ( $featured_query->have_posts() ) :
            $featured_query->the_post();
            $cats = get_the_category();
            $cat  = ! empty( $cats ) ? $cats[0]->name : '';
            ?>
            <a class="cf-blog-featured" href="<?php the_permalink(); ?>">
                <div class="cf-blog-featured__media">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'large' ); ?>
                    <?php endif; ?>
                </div>
                <div class="cf-blog-featured__body">
                    <?php if ( $cat ) : ?>
                        <span class="cf-mono-label"><?php echo esc_html( strtoupper( $cat ) ); ?> · <?php esc_html_e( 'FEATURED', 'collective-finity' ); ?></span>
                    <?php endif; ?>
                    <h2 class="cf-blog-card__title" style="font-size:clamp(19px,2.5vw,24px);"><?php the_title(); ?></h2>
                    <p class="cf-blog-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
                    <p class="cf-blog-card__meta"><?php echo esc_html( get_the_date() ); ?> · <?php echo esc_html( cf_get_read_time_label( get_post_field( 'post_content', get_the_ID() ) ) ); ?></p>
                </div>
            </a>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>

    <div class="cf-blog-chip-row">
        <a class="cf-blog-chip is-active" href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( 'All', 'collective-finity' ); ?></a>
        <?php foreach ( $categories as $category_name ) : ?>
            <?php
            $term = get_term_by( 'name', $category_name, 'category' );
            if ( ! $term ) {
                continue;
            }
            ?>
            <a class="cf-blog-chip" href="<?php echo esc_url( get_category_link( $term->term_id ) ); ?>"><?php echo esc_html( $category_name ); ?></a>
        <?php endforeach; ?>
    </div>

    <div class="cf-blog-grid">
        <?php if ( $posts_query->have_posts() ) : ?>
            <?php
            while ( $posts_query->have_posts() ) :
                $posts_query->the_post();
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
                        <p class="cf-blog-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
                        <p class="cf-blog-card__meta"><?php echo esc_html( get_the_date() ); ?> · <?php echo esc_html( cf_get_read_time_label( get_post_field( 'post_content', get_the_ID() ) ) ); ?></p>
                    </div>
                </a>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p><?php esc_html_e( 'No posts published yet.', 'collective-finity' ); ?></p>
        <?php endif; ?>
    </div>
</div>
