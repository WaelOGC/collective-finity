<?php
/**
 * The template for displaying 404 pages.
 */

get_header();
?>

<main id="primary" class="site-main cf-page-shell">
    <div class="cf-page-container">
        <section class="cf-page-card cf-error-card">
            <p class="cf-page-kicker"><?php echo esc_html( collective_finity_brand_name() ); ?></p>
            <h1><?php esc_html_e( 'Page not found', 'collective-finity' ); ?></h1>
            <p><?php esc_html_e( 'The page you are looking for does not exist or may have been moved.', 'collective-finity' ); ?></p>
            <div class="cf-error-actions">
                <a class="cf-error-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'collective-finity' ); ?></a>
                <a class="cf-error-btn cf-error-btn--ghost" href="<?php echo esc_url( home_url( '/tracks/' ) ); ?>"><?php esc_html_e( 'Browse Music', 'collective-finity' ); ?></a>
            </div>
        </section>
    </div>
</main>

<style>
    .cf-page-shell { padding: 90px 20px 140px; }
    .cf-page-container { max-width: 720px; margin: 0 auto; }
    .cf-page-card {
        background: linear-gradient(180deg, rgba(18, 18, 18, 0.95), rgba(8, 8, 8, 0.95));
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 18px;
        padding: 36px;
        text-align: center;
    }
    .cf-page-kicker {
        margin: 0 0 8px;
        color: var(--primary-color, #FFB700);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.24em;
        text-transform: uppercase;
    }
    .cf-error-card h1 { color: #fff; margin: 0 0 12px; font-size: clamp(28px, 3vw, 40px); }
    .cf-error-card p { color: #bdbdbd; line-height: 1.7; margin: 0 0 24px; }
    .cf-error-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
    .cf-error-btn {
        display: inline-block;
        padding: 12px 20px;
        border-radius: 999px;
        background: var(--primary-color, #FFB700);
        color: #000;
        text-decoration: none;
        font-weight: 700;
    }
    .cf-error-btn--ghost {
        background: transparent;
        color: #fff;
        border: 1px solid rgba(255,255,255,0.15);
    }
</style>

<?php
get_footer();
