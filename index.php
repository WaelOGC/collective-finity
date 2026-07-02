<?php
/**
 * Main template file.
 * Required by WordPress to recognize the theme.
 */

get_header();
?>

<div class="cf-home-shell">
    <section class="cf-home-hero">
        <div class="cf-home-hero-copy">
            <p class="cf-home-kicker">Collective Finity</p>
            <h1>Immersive music, crafted for your world.</h1>
            <p class="cf-home-subtitle">Browse the catalog, open a track, and enjoy a listening experience designed around the music.</p>
            <div class="cf-home-actions">
                <a class="cf-home-primary-btn" href="<?php echo esc_url( home_url( '/tracks/' ) ); ?>">Open music library</a>
                <a class="cf-home-secondary-btn" href="<?php echo esc_url( home_url( '/albums/' ) ); ?>">View collections</a>
            </div>
        </div>
    </section>

    <section class="cf-home-content">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                the_content();
            endwhile;
        else :
            echo '<p class="cf-empty-state">' . esc_html__( 'No content found', 'collective-finity' ) . '</p>';
        endif;
        ?>
    </section>
</div>

<style>
    .cf-home-shell {
        padding: 90px 20px 140px;
        max-width: 1200px;
        margin: 0 auto;
    }
    .cf-home-hero {
        padding: 42px 0 26px;
        margin-bottom: 28px;
    }
    .cf-home-hero-copy {
        max-width: 760px;
    }
    .cf-home-kicker {
        margin: 0 0 10px;
        color: var(--primary-color, #FFB700);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.24em;
        text-transform: uppercase;
    }
    .cf-home-hero h1 {
        margin: 0 0 12px;
        font-size: clamp(30px, 3.2vw, 48px);
        color: #fff;
        line-height: 1.08;
    }
    .cf-home-subtitle {
        margin: 0 0 22px;
        color: #aaaaaa;
        font-size: 16px;
        line-height: 1.7;
        max-width: 650px;
    }
    .cf-home-actions {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
    }
    .cf-home-primary-btn,
    .cf-home-secondary-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 20px;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 700;
        transition: transform 0.2s ease, background 0.2s ease;
    }
    .cf-home-primary-btn {
        background: linear-gradient(135deg, var(--primary-color, #FFB700), #ffd04d);
        color: #050505;
    }
    .cf-home-secondary-btn {
        background: rgba(255,255,255,0.04);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .cf-home-primary-btn:hover,
    .cf-home-secondary-btn:hover {
        transform: translateY(-1px);
    }
    .cf-home-content {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 18px;
        padding: 24px;
    }
    .cf-empty-state {
        margin: 0;
        color: #8f8f8f;
    }
</style>

<?php
get_footer();
?>