<?php
/**
 * The template for displaying all pages.
 */

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-page-template">
    <div class="cf-page-container">
        <?php
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();
?>