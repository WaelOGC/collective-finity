<?php
/**
 * The template for displaying all single posts.
 */

get_header();
?>

<main id="primary" class="site-main cf-page-shell">
    <div class="cf-page-container">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'cf-page-card' ); ?>>
                <header class="cf-page-header">
                    <p class="cf-page-kicker"><?php echo esc_html( collective_finity_brand_name() ); ?></p>
                    <h1><?php the_title(); ?></h1>
                    <p class="cf-post-meta">
                        <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                    </p>
                </header>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();
