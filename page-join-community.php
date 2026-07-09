<?php
/**
 * Template Name: Join Community
 * Description: Theme template for the Join Community page.
 */
get_header();
?>

<main id="primary" class="site-main cf-page-shell">
    <div class="cf-page-container">
        <?php
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
        ?>
    </div>
</main>

<?php get_footer(); ?>
