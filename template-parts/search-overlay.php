<?php
/**
 * Global search overlay (desktop + mobile triggers).
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="cf-search-overlay" class="cf-search-overlay" hidden aria-hidden="true">
    <div class="cf-search-scrim" data-cf-search-close></div>
    <div
        class="cf-search-panel"
        role="dialog"
        aria-modal="true"
        aria-labelledby="cf-search-title"
    >
        <div class="cf-search-head">
            <h2 id="cf-search-title"><?php esc_html_e( 'Search', 'collective-finity' ); ?></h2>
            <button type="button" id="cf-search-close-btn" class="cf-icon-btn" aria-label="<?php esc_attr_e( 'Close search', 'collective-finity' ); ?>">
                <?php echo collective_finity_icon( 'close', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </button>
        </div>
        <form class="cf-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <label class="screen-reader-text" for="cf-search-input"><?php esc_html_e( 'Search for:', 'collective-finity' ); ?></label>
            <div class="cf-search-field">
                <span class="cf-search-field-icon" aria-hidden="true"><?php echo collective_finity_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                <input
                    type="search"
                    id="cf-search-input"
                    class="cf-search-input"
                    name="s"
                    value="<?php echo esc_attr( get_search_query() ); ?>"
                    placeholder="<?php esc_attr_e( 'Search tracks, albums, blog posts…', 'collective-finity' ); ?>"
                    autocomplete="off"
                    required
                >
            </div>
            <button type="submit" class="cf-search-submit"><?php esc_html_e( 'Search', 'collective-finity' ); ?></button>
        </form>
    </div>
</div>
