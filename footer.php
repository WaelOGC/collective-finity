<?php
/**
 * The footer template.
 *
 * @package Collective_Finity
 */
?>
</div><!-- #cf-app-content -->
<?php

get_template_part( 'template-parts/footer', 'default' );

get_template_part( 'template-parts/playlist', 'modal' );
get_template_part( 'template-parts/search', 'overlay' );

wp_footer();
?>

</body>
</html>
