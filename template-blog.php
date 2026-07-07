<?php
/**
 * Template Name: Blog Hub
 * Description: Blog landing page with featured post, category chips, and post grid.
 */

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-blog-page">
    <div class="cf-page-container">
        <?php get_template_part( 'template-parts/cf', 'blog-hub' ); ?>
    </div>
</main>

<?php
get_footer();
