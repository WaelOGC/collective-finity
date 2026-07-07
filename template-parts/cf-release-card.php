<?php
/**
 * Release card partial.
 *
 * @package Collective_Finity
 *
 * @var array $release Release data from cf_get_latest_releases().
 */

if ( ! defined( 'ABSPATH' ) || empty( $release ) ) {
    return;
}

$cover  = cf_get_release_cover_url( $release['id'], $release['post_type'] );
$artist = cf_get_release_artist_label( $release['id'], $release['post_type'] );
$type   = cf_get_release_type_label( $release['id'], $release['post_type'] );
?>
<a class="cf-release-card" href="<?php echo esc_url( $release['permalink'] ); ?>">
    <div class="cf-release-card__art">
        <img src="<?php echo esc_url( $cover ); ?>" alt="" loading="lazy" decoding="async">
    </div>
    <h3 class="cf-release-card__title"><?php echo esc_html( $release['title'] ); ?></h3>
    <p class="cf-release-card__artist"><?php echo esc_html( $artist ); ?></p>
    <span class="cf-release-card__type"><?php echo esc_html( $type ); ?></span>
</a>
