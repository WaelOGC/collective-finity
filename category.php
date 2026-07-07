<?php
/**
 * Category archive template.
 *
 * @package Collective_Finity
 */

get_header();

$category      = get_queried_object();
$descriptions  = cf_get_blog_category_descriptions();
$description   = $category && ! empty( $category->description ) ? $category->description : ( $category && isset( $descriptions[ $category->name ] ) ? $descriptions[ $category->name ] : '' );
$blog_url      = get_post_type_archive_link( 'post' );
if ( ! $blog_url ) {
    $posts_page_id = (int) get_option( 'page_for_posts' );
    $blog_url      = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' );
}
?>

<main id="primary" class="site-main cf-page-shell cf-blog-page">
    <div class="cf-page-container">
        <div class="cf-page-inner cf-page-inner--wide">

            <nav class="cf-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'collective-finity' ); ?>">
                <a href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( 'Blog', 'collective-finity' ); ?></a>
                <span aria-hidden="true">/</span>
                <span class="cf-breadcrumb__current"><?php single_cat_title(); ?></span>
            </nav>

            <header class="cf-category-header">
                <h1 class="cf-hero-title"><?php single_cat_title(); ?></h1>
                <?php if ( $description ) : ?>
                    <p class="cf-hero-lead" style="max-width:560px;"><?php echo esc_html( $description ); ?></p>
                <?php endif; ?>
            </header>

            <div class="cf-blog-grid">
                <?php if ( have_posts() ) : ?>
                    <?php
                    while ( have_posts() ) :
                        the_post();
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
                                <h2 class="cf-blog-card__title"><?php the_title(); ?></h2>
                                <p class="cf-blog-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
                                <p class="cf-blog-card__meta"><?php echo esc_html( get_the_date() ); ?> · <?php echo esc_html( cf_get_read_time_label( get_post_field( 'post_content', get_the_ID() ) ) ); ?></p>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p><?php esc_html_e( 'No posts found in this category yet.', 'collective-finity' ); ?></p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</main>

<?php
get_footer();
