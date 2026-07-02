<?php
/**
 * Front Page Template for Collective Finity.
 */
get_header();
?>

<main id="primary" class="site-main cf-home-shell">
    <div class="cf-page-container">
        <?php
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
        ?>
    </div>
</main>

<?php get_footer(); ?>
