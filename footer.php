<?php
/**
 * The footer template.
 *
 * @package Collective_Finity
 */
?>
</div><!-- #cf-app-content -->
<?php

collective_finity_render_theme_part(
    'footer',
    function () {
        get_template_part( 'template-parts/footer', 'default' );
    }
);

get_template_part( 'template-parts/playlist', 'modal' );
get_template_part( 'template-parts/search', 'overlay' );

wp_footer();
?>

</body>
</html>
