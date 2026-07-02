<?php
/**
 * Template Name: Terms of Service
 * Description: Theme template for Terms of Service pages.
 */

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-terms-page">
    <div class="cf-page-container">
        <div class="entry-content cf-legal-content">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php the_content(); ?>
            <?php endwhile; ?>
        </div>
    </div>
</main>

<style>
.cf-terms-page .cf-legal-content {
    display: grid;
    gap: 24px;
}
.cf-terms-page .cf-legal-content h2,
.cf-terms-page .cf-legal-content h3 {
    color: #fff;
}
.cf-terms-page .cf-legal-content p,
.cf-terms-page .cf-legal-content li {
    color: #d2d2d2;
    line-height: 1.85;
}
.cf-terms-page .cf-legal-content ul,
.cf-terms-page .cf-legal-content ol {
    margin-left: 1.4em;
}
</style>

<?php get_footer(); ?>