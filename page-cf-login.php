<?php
/**
 * Login page template (slug: cf-login).
 *
 * @package Collective_Finity
 */

$register_url = collective_finity_get_page_link( 'cf-register', '/cf-register/' );

if ( is_user_logged_in() ) {
    wp_safe_redirect( home_url( '/cf-profile/' ) );
    exit;
}

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-auth-page">
    <div class="cf-page-container">
        <div class="cf-auth-wrap">
            <form class="cf-auth-card" data-cf-auth-form="cf_login" method="post" novalidate>
                <div class="cf-auth-brand">
                    <span class="cf-auth-brand__mark" aria-hidden="true"></span>
                    <span class="cf-auth-brand__text">COLLECTIVE FINITY</span>
                </div>
                <h1><?php esc_html_e( 'Log In', 'collective-finity' ); ?></h1>
                <div class="cf-auth-message" hidden></div>
                <div class="cf-auth-field">
                    <label class="cf-label" for="cf_login_email"><?php esc_html_e( 'EMAIL', 'collective-finity' ); ?></label>
                    <input class="cf-input" type="email" id="cf_login_email" name="email" placeholder="you@example.com" autocomplete="email" required>
                </div>
                <div class="cf-auth-field">
                    <label class="cf-label" for="cf_login_password"><?php esc_html_e( 'PASSWORD', 'collective-finity' ); ?></label>
                    <input class="cf-input" type="password" id="cf_login_password" name="password" placeholder="••••••••" autocomplete="current-password" required>
                </div>
                <button type="submit" class="cf-btn cf-btn--primary cf-btn--block"><?php esc_html_e( 'Log In', 'collective-finity' ); ?></button>
                <p class="cf-auth-footer">
                    <?php esc_html_e( 'New here?', 'collective-finity' ); ?>
                    <a href="<?php echo esc_url( $register_url ); ?>"><?php esc_html_e( 'Register instead', 'collective-finity' ); ?></a>
                </p>
            </form>
        </div>
    </div>
</main>

<?php
get_footer();
