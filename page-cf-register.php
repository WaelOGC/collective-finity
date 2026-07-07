<?php
/**
 * Register page template (slug: cf-register).
 *
 * @package Collective_Finity
 */

$login_url = collective_finity_get_page_link( 'cf-login', '/cf-login/' );

if ( is_user_logged_in() ) {
    wp_safe_redirect( home_url( '/cf-profile/' ) );
    exit;
}

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-auth-page">
    <div class="cf-page-container">
        <div class="cf-auth-wrap">
            <form class="cf-auth-card" data-cf-auth-form="cf_register" method="post" novalidate>
                <div class="cf-auth-brand">
                    <span class="cf-auth-brand__mark" aria-hidden="true"></span>
                    <span class="cf-auth-brand__text">COLLECTIVE FINITY</span>
                </div>
                <h1><?php esc_html_e( 'Create Account', 'collective-finity' ); ?></h1>
                <div class="cf-auth-message" hidden></div>
                <div class="cf-auth-field">
                    <label class="cf-label" for="cf_register_name"><?php esc_html_e( 'NAME', 'collective-finity' ); ?></label>
                    <input class="cf-input" type="text" id="cf_register_name" name="name" placeholder="<?php esc_attr_e( 'Your name', 'collective-finity' ); ?>" autocomplete="name" required>
                </div>
                <div class="cf-auth-field">
                    <label class="cf-label" for="cf_register_email"><?php esc_html_e( 'EMAIL', 'collective-finity' ); ?></label>
                    <input class="cf-input" type="email" id="cf_register_email" name="email" placeholder="you@example.com" autocomplete="email" required>
                </div>
                <div class="cf-auth-field">
                    <label class="cf-label" for="cf_register_password"><?php esc_html_e( 'PASSWORD', 'collective-finity' ); ?></label>
                    <input class="cf-input" type="password" id="cf_register_password" name="password" placeholder="••••••••" autocomplete="new-password" required>
                </div>
                <div class="cf-auth-field">
                    <label class="cf-label" for="cf_register_confirm"><?php esc_html_e( 'CONFIRM PASSWORD', 'collective-finity' ); ?></label>
                    <input class="cf-input" type="password" id="cf_register_confirm" name="confirm_password" placeholder="••••••••" autocomplete="new-password" required>
                </div>
                <button type="submit" class="cf-btn cf-btn--primary cf-btn--block"><?php esc_html_e( 'Create Account', 'collective-finity' ); ?></button>
                <p class="cf-auth-footer">
                    <?php esc_html_e( 'Already a member?', 'collective-finity' ); ?>
                    <a href="<?php echo esc_url( $login_url ); ?>"><?php esc_html_e( 'Login instead', 'collective-finity' ); ?></a>
                </p>
            </form>
        </div>
    </div>
</main>

<?php
get_footer();
