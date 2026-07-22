<?php
/**
 * The template for displaying 404 (page not found) pages.
 *
 * Rendered inside the standard Shell (get_header/get_footer keep the sidebar,
 * right player and footer intact). Styling mirrors the Collective Finity design
 * system: "Page not found" heading, explanatory copy, an amber "Back to Home"
 * button and an outline "Browse Music" button.
 *
 * @package Collective_Finity
 */

get_header();

$cf_tracks_url = get_post_type_archive_link( 'tracks' );
$cf_tracks_url = $cf_tracks_url ? $cf_tracks_url : home_url( '/tracks/' );
?>

<div class="cf-blog cf-404">
    <section class="cf-404-card">
        <p class="cf-404-kicker"><?php esc_html_e( '404 ERROR', 'collective-finity' ); ?></p>
        <h1 class="cf-404-title"><?php esc_html_e( 'Page not found', 'collective-finity' ); ?></h1>
        <p class="cf-404-text"><?php esc_html_e( 'The page you are looking for does not exist or may have been moved. Let\'s get you back to the music.', 'collective-finity' ); ?></p>
        <div class="cf-404-actions">
            <a class="cf-404-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'collective-finity' ); ?></a>
            <a class="cf-404-btn cf-404-btn--ghost" href="<?php echo esc_url( $cf_tracks_url ); ?>"><?php esc_html_e( 'Browse Music', 'collective-finity' ); ?></a>
        </div>
    </section>
</div>

<style>
    .cf-404 { padding: 30px 5px; display: flex; align-items: center; justify-content: center; min-height: 60vh; }
    .cf-404-card {
        max-width: 560px;
        width: 100%;
        text-align: center;
        background: var(--cf-bg-card);
        border: 1px solid var(--cf-border);
        border-radius: 18px;
        padding: 48px 36px;
    }
    .cf-404-kicker {
        margin: 0 0 12px;
        font-family: var(--cf-mono);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.16em;
        color: var(--cf-accent);
    }
    .cf-404-title { margin: 0 0 12px; color: #fff; font-size: clamp(28px, 3vw, 40px); font-weight: 700; }
    .cf-404-text { margin: 0 auto 26px; max-width: 420px; color: var(--cf-text-2); line-height: 1.7; font-size: 14.5px; }
    .cf-404-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
    .cf-404-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 22px;
        border-radius: 9px;
        background: var(--cf-accent);
        color: #0D0D0D;
        text-decoration: none;
        font-weight: 700;
        font-size: 13.5px;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }
    .cf-404-btn:hover { background: var(--cf-accent-hover); color: #0D0D0D; }
    .cf-404-btn--ghost { background: transparent; color: #fff; border: 1px solid #333; }
    .cf-404-btn--ghost:hover { background: var(--cf-bg-card-hover); color: #fff; border-color: var(--cf-border-strong); }

    @media (max-width: 767px) {
        .cf-404 { padding: 18px 5px; }
        .cf-404-card { padding: 36px 22px; }
    }
</style>

<?php
get_footer();
