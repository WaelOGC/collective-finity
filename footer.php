<?php
/**
 * The footer template.
 *
 * @package Collective_Finity
 */

collective_finity_render_theme_part(
    'footer',
    function () {
        get_template_part( 'template-parts/footer', 'default' );
    }
);

get_template_part( 'template-parts/playlist', 'modal' );

wp_footer();
?>

</body>
</html>
