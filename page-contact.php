<?php
/**
 * Template Name: Contact Page
 * Description: Theme template for Contact Us pages.
 */

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-contact-page">
    <div class="cf-page-container">
        <div class="cf-contact-grid">
            <section class="cf-contact-copy">
                <?php while ( have_posts() ) : the_post(); ?>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                <?php endwhile; ?>
            </section>

            <aside class="cf-contact-details">
                    <div class="cf-contact-panel cf-glass-card">
                        <h2><?php _e( 'Contact Details', 'collective-finity' ); ?></h2>
                        <ul class="cf-contact-list">
                            <li><strong><?php _e( 'Email:', 'collective-finity' ); ?></strong> <a href="mailto:hello@collectivefinity.com">hello@collectivefinity.com</a></li>
                            <li><strong><?php _e( 'Phone:', 'collective-finity' ); ?></strong> +1 (555) 123-4567</li>
                            <li><strong><?php _e( 'Location:', 'collective-finity' ); ?></strong> Global / Remote</li>
                        </ul>
                    </div>

                    <div class="cf-contact-panel cf-glass-card">
                        <h2><?php _e( 'Follow Us', 'collective-finity' ); ?></h2>
                        <div class="cf-social-buttons">
                            <a href="#" class="cf-social-link"><?php _e( 'Discord', 'collective-finity' ); ?></a>
                            <a href="#" class="cf-social-link"><?php _e( 'Instagram', 'collective-finity' ); ?></a>
                            <a href="#" class="cf-social-link"><?php _e( 'SoundCloud', 'collective-finity' ); ?></a>
                        </div>
                    </div>
                </aside>
            </div>
        </article>
    </div>
</main>

<style>
.cf-contact-page .cf-contact-grid {
    display: grid;
    grid-template-columns: 2.2fr 1fr;
    gap: 28px;
    align-items: start;
}
.cf-contact-details {
    display: grid;
    gap: 20px;
}
.cf-contact-panel {
    padding: 26px;
    border-radius: 18px;
}
.cf-contact-panel h2 {
    margin-top: 0;
    color: #fff;
    font-size: 20px;
}
.cf-contact-list {
    list-style: none;
    margin: 0;
    padding: 0;
    color: #cfcfcf;
}
.cf-contact-list li {
    margin-bottom: 14px;
    line-height: 1.8;
}
.cf-social-buttons {
    display: grid;
    gap: 12px;
}
.cf-social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 16px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    text-decoration: none;
    color: #fff;
    transition: transform 0.2s ease, border-color 0.2s ease;
}
.cf-social-link:hover {
    transform: translateY(-1px);
    border-color: rgba(255, 183, 0, 0.45);
}
@media (max-width: 900px) {
    .cf-contact-page .cf-contact-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php get_footer(); ?>