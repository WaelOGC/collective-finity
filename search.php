<?php
/**
 * The template for displaying search results.
 */

get_header();

$search_query = get_search_query();
?>

<main id="primary" class="site-main cf-page-shell">
    <div class="cf-page-container">
        <header class="cf-search-header">
            <p class="cf-page-kicker"><?php echo esc_html( collective_finity_brand_name() ); ?></p>
            <h1>
                <?php
                printf(
                    /* translators: %s: search query */
                    esc_html__( 'Search results for: %s', 'collective-finity' ),
                    '<span>' . esc_html( $search_query ) . '</span>'
                );
                ?>
            </h1>
        </header>

        <?php if ( have_posts() ) : ?>
            <div class="cf-search-results">
                <?php
                while ( have_posts() ) :
                    the_post();
                    $post_type_obj = get_post_type_object( get_post_type() );
                    $type_label    = $post_type_obj ? $post_type_obj->labels->singular_name : get_post_type();
                    ?>
                    <article <?php post_class( 'cf-search-item' ); ?>>
                        <span class="cf-search-type"><?php echo esc_html( $type_label ); ?></span>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php if ( has_excerpt() || get_the_content() ) : ?>
                            <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="cf-search-pagination">
                <?php the_posts_pagination(); ?>
            </div>
        <?php else : ?>
            <section class="cf-page-card">
                <p><?php esc_html_e( 'No results found. Try another keyword or browse the music library.', 'collective-finity' ); ?></p>
                <a class="cf-search-link" href="<?php echo esc_url( home_url( '/tracks/' ) ); ?>"><?php esc_html_e( 'Open Music Library', 'collective-finity' ); ?></a>
            </section>
        <?php endif; ?>
    </div>
</main>

<style>
    .cf-search-header h1 { color: #fff; margin: 0 0 28px; font-size: clamp(24px, 2.5vw, 34px); }
    .cf-search-header h1 span { color: var(--primary-color, #FFB700); }
    .cf-search-results { display: grid; gap: 16px; }
    .cf-search-item {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px;
        padding: 20px;
    }
    .cf-search-type {
        display: inline-block;
        margin-bottom: 8px;
        color: #888;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }
    .cf-search-item h2 { margin: 0 0 8px; font-size: 1.2rem; }
    .cf-search-item h2 a { color: #fff; text-decoration: none; }
    .cf-search-item p { margin: 0; color: #bdbdbd; line-height: 1.6; }
    .cf-page-card, .cf-search-link {
        display: inline-block;
        margin-top: 8px;
        color: var(--primary-color, #FFB700);
        text-decoration: none;
        font-weight: 700;
    }
    .cf-page-card {
        display: block;
        background: linear-gradient(180deg, rgba(18, 18, 18, 0.95), rgba(8, 8, 8, 0.95));
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 18px;
        padding: 28px;
        color: #cfcfcf;
    }
    .cf-search-pagination { margin-top: 28px; }
</style>

<?php
get_footer();
