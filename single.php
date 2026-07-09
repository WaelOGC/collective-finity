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

<style>
    .cf-page-shell {
        padding: 90px 20px 140px;
    }
    .cf-page-container {
        max-width: 980px;
        margin: 0 auto;
    }
    .cf-page-card {
        background: linear-gradient(180deg, rgba(18, 18, 18, 0.95), rgba(8, 8, 8, 0.95));
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 18px;
        padding: 36px;
        box-shadow: 0 12px 36px rgba(0,0,0,0.3);
    }
    .cf-page-header {
        margin-bottom: 24px;
        padding-bottom: 18px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .cf-page-kicker {
        margin: 0 0 8px;
        color: var(--primary-color, #FFB700);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.24em;
        text-transform: uppercase;
    }
    .cf-page-header h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(26px, 2.5vw, 34px);
    }
    .cf-post-meta {
        margin: 12px 0 0;
        color: #888;
        font-size: 0.85rem;
    }
    .entry-content p,
    .entry-content li {
        line-height: 1.8;
        color: #cfcfcf;
    }
    .entry-content a {
        color: var(--primary-color, #FFB700);
    }
</style>

<?php
get_footer();
