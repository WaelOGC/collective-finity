<?php
/**
 * Template Name: Legal Page
 * Description: Shared template for Privacy Policy, Terms, Cookie Policy, and Copyright Policy.
 *
 * @package Collective_Finity
 */

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-legal-page">
    <div class="cf-legal-layout">
        <?php
        while ( have_posts() ) :
            the_post();

            $raw_content = apply_filters( 'the_content', get_the_content() );
            $toc_data      = collective_finity_build_legal_toc( $raw_content );
            $current_slug  = get_post_field( 'post_name', get_the_ID() );
            $sibling_links = array_filter(
                collective_finity_get_published_legal_links(),
                function ( $link ) use ( $current_slug ) {
                    return $link['slug'] !== $current_slug;
                }
            );
            ?>
            <nav class="cf-legal-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'collective-finity' ); ?>">
                <ol>
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'collective-finity' ); ?></a></li>
                    <li aria-current="page"><?php the_title(); ?></li>
                </ol>
            </nav>

            <article class="cf-legal-article">
                <header class="cf-legal-header">
                    <h1 class="cf-legal-title"><?php the_title(); ?></h1>
                    <p class="cf-legal-updated">
                        <?php
                        printf(
                            /* translators: %s: last modified date */
                            esc_html__( 'Last updated: %s', 'collective-finity' ),
                            esc_html( get_the_modified_date() )
                        );
                        ?>
                    </p>
                </header>

                <?php if ( $toc_data['toc'] ) : ?>
                    <?php echo $toc_data['toc']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php endif; ?>

                <div class="entry-content cf-legal-content">
                    <?php echo $toc_data['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </article>

            <?php if ( ! empty( $sibling_links ) ) : ?>
                <aside class="cf-legal-quick-nav" aria-label="<?php esc_attr_e( 'Other legal pages', 'collective-finity' ); ?>">
                    <h2 class="cf-legal-quick-nav__title"><?php esc_html_e( 'Related policies', 'collective-finity' ); ?></h2>
                    <ul class="cf-legal-quick-nav__list">
                        <?php foreach ( $sibling_links as $link ) : ?>
                            <li><a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </aside>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
