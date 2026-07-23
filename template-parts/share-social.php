<?php
/**
 * Social share buttons.
 *
 * @package Collective_Finity
 *
 * @var string     $share_url
 * @var string     $share_title
 * @var string     $share_context Optional wrapper class suffix.
 * @var int|string $item_id       Optional item ID for share tracking.
 * @var string     $item_type     Optional item type for share tracking.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$share_url   = isset( $share_url ) ? $share_url : get_permalink();
$share_title = isset( $share_title ) ? $share_title : get_the_title();
$share_class = isset( $share_context ) ? 'cf-share-panel cf-share-panel--' . sanitize_html_class( $share_context ) : 'cf-share-panel';
$item_id     = isset( $item_id ) ? $item_id : 0;
$item_type   = isset( $item_type ) ? $item_type : '';

$encoded_url   = rawurlencode( $share_url );
$encoded_title = rawurlencode( $share_title );
?>
<div class="<?php echo esc_attr( $share_class ); ?>"<?php echo ( $item_id && $item_type ) ? ' data-item-id="' . esc_attr( (string) $item_id ) . '" data-item-type="' . esc_attr( $item_type ) . '"' : ''; ?>>
    <span class="cf-share-label"><?php esc_html_e( 'Share', 'collective-finity' ); ?></span>
    <div class="cf-share-buttons">
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $encoded_url ); ?>" class="cf-share-btn" data-platform="facebook" target="_blank" rel="noopener noreferrer" title="<?php esc_attr_e( 'Share on Facebook', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Share on Facebook', 'collective-finity' ); ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.413c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.971H15.83c-1.491 0-1.956.93-1.956 1.886v2.268h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
        </a>
        <a href="https://twitter.com/intent/tweet?url=<?php echo esc_attr( $encoded_url ); ?>&text=<?php echo esc_attr( $encoded_title ); ?>" class="cf-share-btn" data-platform="twitter" target="_blank" rel="noopener noreferrer" title="<?php esc_attr_e( 'Share on X', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Share on X', 'collective-finity' ); ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </a>
        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo esc_attr( $encoded_url ); ?>" class="cf-share-btn" data-platform="linkedin" target="_blank" rel="noopener noreferrer" title="<?php esc_attr_e( 'Share on LinkedIn', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'collective-finity' ); ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 114.126 0 2.063 2.063 0 01-2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
        </a>
        <a href="https://wa.me/?text=<?php echo esc_attr( $encoded_title . '%20' . $encoded_url ); ?>" class="cf-share-btn" data-platform="whatsapp" target="_blank" rel="noopener noreferrer" title="<?php esc_attr_e( 'Share on WhatsApp', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'collective-finity' ); ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </a>
        <button type="button" class="cf-share-btn cf-share-copy-btn" data-platform="copy" data-share-url="<?php echo esc_url( $share_url ); ?>" title="<?php esc_attr_e( 'Copy link', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Copy link', 'collective-finity' ); ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>
        </button>
    </div>
</div>
<script>
(function () {
    if (window.__cfShareTrackBound) {
        return;
    }
    window.__cfShareTrackBound = true;
    document.addEventListener('click', function (e) {
        var btn = e.target.closest && e.target.closest('.cf-share-panel .cf-share-btn');
        if (!btn) {
            return;
        }
        var panel = btn.closest('.cf-share-panel');
        if (!panel) {
            return;
        }
        var itemId = panel.getAttribute('data-item-id');
        var itemType = panel.getAttribute('data-item-type');
        if (!itemId || !itemType) {
            return;
        }
        var platform = btn.getAttribute('data-platform') || 'copy';
        if (window.CF_Auth && typeof window.CF_Auth.trackShare === 'function') {
            window.CF_Auth.trackShare(itemId, itemType, platform);
        }
    });
}());
</script>
