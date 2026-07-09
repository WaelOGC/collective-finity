<?php
/**
 * [cf_latest_releases] — Dynamic Latest Releases grid.
 *
 * Pulls the most recent published items from the `tracks` and `albums` CPTs.
 * Reuses the same cover-image fallback chain as cf-music-library-shortcode.php.
 *
 * INSTALL:
 * 1. Save this file as: wp-content/themes/collective-finity/inc/cf-latest-releases-shortcode.php
 * 2. In functions.php add:
 *      require get_template_directory() . '/inc/cf-latest-releases-shortcode.php';
 * 3. Drop the shortcode anywhere: [cf_latest_releases] or [cf_latest_releases limit="5"]
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'cf_latest_releases', 'collective_finity_latest_releases_shortcode' );

function collective_finity_latest_releases_shortcode( $atts ) {

	$atts = shortcode_atts(
		array(
			'limit' => 5,
			'title' => 'Latest Releases',
		),
		$atts,
		'cf_latest_releases'
	);

	$limit = max( 1, min( 12, (int) $atts['limit'] ) );

	// ── Helper: resolve a track's cover image with the same fallback chain used elsewhere ──
	$cf_get_track_cover = function ( $track_id ) {
		$cover = get_post_meta( $track_id, 'track_cover_url', true );
		if ( ! $cover ) $cover = get_the_post_thumbnail_url( $track_id, 'medium' );
		if ( ! $cover ) $cover = collective_finity_default_art_url();
		return $cover;
	};

	// ── Helper: resolve an album's cover the same way archive-albums.php does ──
	$cf_get_album_cover = function ( $album_id ) {
		$cover = get_the_post_thumbnail_url( $album_id, 'medium' );
		if ( ! $cover ) {
			$first_track = get_posts( array(
				'post_type'      => 'tracks',
				'posts_per_page' => 1,
				'meta_key'       => 'associated_album',
				'meta_value'     => $album_id,
				'fields'         => 'ids',
			) );
			if ( ! empty( $first_track ) ) {
				$cover = get_post_meta( $first_track[0], 'track_cover_url', true );
			}
		}
		if ( ! $cover ) $cover = collective_finity_default_art_url();
		return $cover;
	};

	// ── Latest Releases — most recent tracks + albums combined, newest first ──
	$latest_tracks = get_posts( array(
		'post_type'      => 'tracks',
		'posts_per_page' => $limit,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
	$latest_albums = get_posts( array(
		'post_type'      => 'albums',
		'posts_per_page' => $limit,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
	$latest_releases = array_merge(
		array_map( function ( $p ) { return array( 'post' => $p, 'type' => 'track' ); }, $latest_tracks ),
		array_map( function ( $p ) { return array( 'post' => $p, 'type' => 'album' ); }, $latest_albums )
	);
	usort( $latest_releases, function ( $a, $b ) {
		return strtotime( $b['post']->post_date ) <=> strtotime( $a['post']->post_date );
	} );
	$latest_releases = array_slice( $latest_releases, 0, $limit );

	if ( empty( $latest_releases ) ) {
		return '';
	}

	ob_start();
	?>
	<section class="cf-latest-releases-shortcode">
		<?php if ( $atts['title'] ) : ?>
		<h2 class="cf-latest-releases-shortcode__title"><?php echo esc_html( $atts['title'] ); ?></h2>
		<?php endif; ?>
		<div class="cf-latest-releases-shortcode__grid">
			<?php foreach ( $latest_releases as $item ) :
				$p = $item['post'];
				if ( $item['type'] === 'track' ) {
					$cover      = $cf_get_track_cover( $p->ID );
					$link       = get_permalink( $p->ID );
					$artists    = wp_get_post_terms( $p->ID, 'track_artist', array( 'fields' => 'names' ) );
					$artist     = ! empty( $artists ) ? $artists[0] : 'Collective Finity';
					$type_label = get_post_meta( $p->ID, 'track_release_type', true ) === 'album_track' ? 'Album Track' : 'Single';
				} else {
					$cover      = $cf_get_album_cover( $p->ID );
					$link       = get_permalink( $p->ID );
					$artist     = get_the_author_meta( 'display_name', $p->post_author );
					$type_label = 'Album';
				}
			?>
			<a href="<?php echo esc_url( $link ); ?>" class="cf-latest-release-card">
				<div class="cf-latest-release-card__art">
					<img src="<?php echo esc_url( $cover ); ?>" alt="<?php echo esc_attr( $p->post_title ); ?>" loading="lazy">
				</div>
				<h3 class="cf-latest-release-card__title"><?php echo esc_html( $p->post_title ); ?></h3>
				<p class="cf-latest-release-card__artist"><?php echo esc_html( $artist ); ?></p>
				<span class="cf-latest-release-card__type"><?php echo esc_html( $type_label ); ?></span>
			</a>
			<?php endforeach; ?>
		</div>
	</section>

	<style>
	.cf-latest-releases-shortcode{width:100%;box-sizing:border-box;font-family:inherit;color:#fff}
	.cf-latest-releases-shortcode__title{font-size:1.8rem;font-weight:800;letter-spacing:1px;margin:0 0 20px 0;color:#fff}
	.cf-latest-releases-shortcode__grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:18px}
	.cf-latest-release-card{display:block;text-decoration:none;color:#fff;transition:transform .2s ease}
	.cf-latest-release-card:hover{transform:translateY(-2px)}
	.cf-latest-release-card__art{width:100%;aspect-ratio:1;margin-bottom:10px;border-radius:12px;overflow:hidden;background:#161616;box-shadow:0 14px 28px -12px rgba(0,0,0,.55)}
	.cf-latest-release-card__art img{display:block;width:100%;height:100%;object-fit:cover}
	.cf-latest-release-card__title{margin:0;font-size:14px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
	.cf-latest-release-card__artist{margin:2px 0 0;font-size:12px;color:#7A7A7A}
	.cf-latest-release-card__type{display:inline-block;margin-top:6px;padding:2px 8px;border-radius:5px;background:rgba(255,183,0,.12);color:#FFB700;font-family:'Space Mono',monospace;font-size:10.5px}
	@media (max-width:768px){.cf-latest-releases-shortcode__grid{grid-template-columns:repeat(2,1fr)}}
	@media (max-width:480px){.cf-latest-releases-shortcode__grid{grid-template-columns:1fr}}
	</style>
	<?php
	return ob_get_clean();
}
