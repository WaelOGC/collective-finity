<?php
/**
 * [cf_footer_player] — Compact mobile/tablet footer music player bar.
 *
 * Mirrors Shell.dc.html footerPlayerContent: cover, title, progress, transport
 * controls. Synced with the main player via music-player.js (distinct IDs).
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'cf_footer_player', 'collective_finity_footer_player_shortcode' );

/**
 * @param array<string, string> $atts Shortcode attributes.
 */
function collective_finity_footer_player_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'id' => '',
		),
		$atts,
		'cf_footer_player'
	);

	// id="" is accepted for backward compatibility with saved widgets; default
	// empty state matches sidebar-right-default.php (no forced default track).
	$cf_logo_url = collective_finity_site_logo_url( 'thumbnail' );

	static $body_class_hooked = false;
	if ( ! $body_class_hooked ) {
		add_filter( 'body_class', 'collective_finity_footer_player_body_class' );
		$body_class_hooked = true;
	}

	ob_start();
	?>
	<div id="cf-footer-player-bar" class="cf-footer-player-bar" role="region" aria-label="<?php esc_attr_e( 'Music player', 'collective-finity' ); ?>">
		<div class="cf-footer-player-cover" id="cf-footer-player-cover" style="background-image: url('<?php echo esc_url( $cf_logo_url ); ?>');"></div>
		<div class="cf-footer-player-mid">
			<div class="cf-footer-player-title" id="cf-footer-player-title"><?php esc_html_e( 'Select a track', 'collective-finity' ); ?></div>
			<div class="cf-footer-player-progress-bg" id="cf-footer-player-progress-bg">
				<div class="cf-footer-player-progress-fill" id="cf-footer-player-progress-fill"></div>
			</div>
		</div>
		<button type="button" class="cf-footer-player-btn" id="cf-footer-player-prev-btn" title="<?php esc_attr_e( 'Previous track', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Previous track', 'collective-finity' ); ?>">
			<span class="cf-icon cf-icon-prev" aria-hidden="true"></span>
		</button>
		<button type="button" class="cf-footer-player-btn cf-footer-player-btn--play" id="cf-footer-player-toggle-btn" aria-label="<?php esc_attr_e( 'Play', 'collective-finity' ); ?>">
			<span class="cf-icon cf-icon-play" aria-hidden="true"></span>
		</button>
		<button type="button" class="cf-footer-player-btn" id="cf-footer-player-next-btn" title="<?php esc_attr_e( 'Next track', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Next track', 'collective-finity' ); ?>">
			<span class="cf-icon cf-icon-next" aria-hidden="true"></span>
		</button>
		<button type="button" class="cf-footer-player-btn cf-footer-player-like" id="cf-footer-player-like-btn" disabled title="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>">
			<span class="cf-icon cf-icon-heart" aria-hidden="true"></span>
		</button>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * @param string[] $classes Body classes.
 * @return string[]
 */
function collective_finity_footer_player_body_class( $classes ) {
	$classes[] = 'cf-has-footer-player';
	return $classes;
}
