<?php
/**
 * Template Name: Privacy Policy
 * Description: Theme template for Privacy Policy pages.
 */

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-privacy-page">
    <div class="cf-page-container">
        <div class="entry-content cf-legal-content">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php the_content(); ?>
            <?php endwhile; ?>
        </div>
    </div>
</main>

<style>
.cf-privacy-page .cf-legal-content {
    display: grid;
    gap: 24px;
}
.cf-privacy-page .cf-legal-content h2,
.cf-privacy-page .cf-legal-content h3 {
    color: #fff;
}
.cf-privacy-page .cf-legal-content p,
.cf-privacy-page .cf-legal-content li {
    color: #d2d2d2;
    line-height: 1.85;
}
.cf-privacy-page .cf-legal-content ul,
.cf-privacy-page .cf-legal-content ol {
    margin-left: 1.4em;
}
</style>

<?php get_footer(); ?>