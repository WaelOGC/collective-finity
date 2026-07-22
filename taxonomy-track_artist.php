<?php
/**
 * Track Artist taxonomy archive — Artist page.
 *
 * Loaded for URLs like /artist/{slug}/ via rewrite slug "artist".
 *
 * @package Collective_Finity
 */

get_header();

$artist = get_queried_object();
if ( ! $artist || is_wp_error( $artist ) || empty( $artist->term_id ) ) {
	get_footer();
	return;
}

$term_id     = (int) $artist->term_id;
$artist_name = $artist->name;

$photo_id  = absint( get_term_meta( $term_id, 'artist_photo_id', true ) );
$photo_url = $photo_id ? wp_get_attachment_image_url( $photo_id, 'large' ) : '';
if ( ! $photo_url ) {
	$photo_url = collective_finity_default_art_url();
}

$artist_bio = (string) get_term_meta( $term_id, 'artist_bio', true );
if ( '' === $artist_bio && ! empty( $artist->description ) ) {
	$artist_bio = $artist->description;
}
$bio_max = function_exists( 'collective_finity_artist_bio_max_length' ) ? collective_finity_artist_bio_max_length() : 150;
if ( function_exists( 'mb_substr' ) ) {
	$artist_bio = mb_substr( $artist_bio, 0, $bio_max );
} else {
	$artist_bio = substr( $artist_bio, 0, $bio_max );
}

$social_links = array(
	'instagram' => array(
		'label' => __( 'Instagram', 'collective-finity' ),
		'url'   => (string) get_term_meta( $term_id, 'artist_instagram_url', true ),
	),
	'spotify'   => array(
		'label' => __( 'Spotify', 'collective-finity' ),
		'url'   => (string) get_term_meta( $term_id, 'artist_spotify_url', true ),
	),
	'youtube'   => array(
		'label' => __( 'YouTube', 'collective-finity' ),
		'url'   => (string) get_term_meta( $term_id, 'artist_youtube_url', true ),
	),
	'tiktok'    => array(
		'label' => __( 'TikTok', 'collective-finity' ),
		'url'   => (string) get_term_meta( $term_id, 'artist_tiktok_url', true ),
	),
	'facebook'  => array(
		'label' => __( 'Facebook', 'collective-finity' ),
		'url'   => (string) get_term_meta( $term_id, 'artist_facebook_url', true ),
	),
	'x'         => array(
		'label' => __( 'X (Twitter)', 'collective-finity' ),
		'url'   => (string) get_term_meta( $term_id, 'artist_x_url', true ),
	),
);

$linked_user_id = absint( get_term_meta( $term_id, 'artist_user_id', true ) );
$album_ids      = function_exists( 'collective_finity_get_artist_album_ids' )
	? collective_finity_get_artist_album_ids( $term_id )
	: array();

$track_count = (int) $artist->count;
$album_count = count( $album_ids );

// Independent section pagination (does not use main ?paged=).
$tracks_paged = max( 1, (int) get_query_var( 'paged' ) );
if ( $tracks_paged < 2 && isset( $_GET['paged'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$tracks_paged = max( 1, absint( wp_unslash( $_GET['paged'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

$albums_per_page   = 5;
$album_page        = isset( $_GET['album_page'] ) ? max( 1, absint( wp_unslash( $_GET['album_page'] ) ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$album_total_pages = max( 1, (int) ceil( $album_count / $albums_per_page ) );
if ( $album_page > $album_total_pages ) {
	$album_page = $album_total_pages;
}
$album_page_ids = array_slice( $album_ids, ( $album_page - 1 ) * $albums_per_page, $albums_per_page );

$blogs_per_page = 4;
$blog_page      = isset( $_GET['blog_page'] ) ? max( 1, absint( wp_unslash( $_GET['blog_page'] ) ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

/**
 * Render section pagination matching .cf-artist-pagination / .nav-links style.
 *
 * @param string $param   Query param name (album_page|blog_page).
 * @param int    $current Current page.
 * @param int    $total   Total pages.
 * @param array  $preserve Extra query args to keep (e.g. other section pages + tracks paged).
 * @param string $aria    Accessible label.
 */
$cf_render_section_pagination = static function ( $param, $current, $total, $preserve, $aria ) {
	if ( $total < 2 ) {
		return;
	}

	$links = array();
	if ( $current > 1 ) {
		$links[] = array(
			'url'   => add_query_arg( array_merge( $preserve, array( $param => $current - 1 ) ) ),
			'label' => __( '← Previous', 'collective-finity' ),
			'class' => 'prev',
		);
	}

	for ( $i = 1; $i <= $total; $i++ ) {
		$links[] = array(
			'url'     => add_query_arg( array_merge( $preserve, array( $param => $i ) ) ),
			'label'   => (string) $i,
			'class'   => ( $i === $current ) ? 'current' : '',
			'current' => ( $i === $current ),
		);
	}

	if ( $current < $total ) {
		$links[] = array(
			'url'   => add_query_arg( array_merge( $preserve, array( $param => $current + 1 ) ) ),
			'label' => __( 'Next →', 'collective-finity' ),
			'class' => 'next',
		);
	}
	?>
	<div class="cf-artist-pagination">
		<nav class="navigation pagination" aria-label="<?php echo esc_attr( $aria ); ?>">
			<div class="nav-links">
				<?php foreach ( $links as $link ) : ?>
					<?php if ( ! empty( $link['current'] ) ) : ?>
						<span aria-current="page" class="page-numbers current"><?php echo esc_html( $link['label'] ); ?></span>
					<?php else : ?>
						<a class="page-numbers <?php echo esc_attr( $link['class'] ); ?>" href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</nav>
	</div>
	<?php
};

// Professional Info & Stats.
$show_pro_raw = get_term_meta( $term_id, 'artist_show_pro_info', true );
$show_pro     = ( '' === $show_pro_raw || '1' === (string) $show_pro_raw );
$years_active = (string) get_term_meta( $term_id, 'artist_years_active', true );
$location     = (string) get_term_meta( $term_id, 'artist_location', true );
$label_crew   = (string) get_term_meta( $term_id, 'artist_label', true );
$genre_ids    = get_term_meta( $term_id, 'artist_genre_ids', true );
$genre_ids    = is_array( $genre_ids ) ? array_map( 'absint', $genre_ids ) : array();
$genre_terms  = array();
if ( ! empty( $genre_ids ) ) {
	foreach ( $genre_ids as $genre_id ) {
		$g = get_term( $genre_id, 'music_genre' );
		if ( $g && ! is_wp_error( $g ) ) {
			$genre_terms[] = $g;
		}
	}
}

$total_views       = 0;
$avg_likes         = 0;
$most_played_id    = 0;
$most_played_views = -1;
$most_played_title = '';
$most_played_link  = '';

if ( $show_pro ) {
	$all_track_ids = get_posts(
		array(
			'post_type'      => 'tracks',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'track_artist',
					'field'    => 'term_id',
					'terms'    => $term_id,
				),
			),
		)
	);

	$like_sum   = 0;
	$like_count = 0;
	foreach ( $all_track_ids as $stat_track_id ) {
		$views = function_exists( 'collective_finity_track_views' )
			? (int) collective_finity_track_views( $stat_track_id )
			: (int) get_post_meta( $stat_track_id, '_cf_track_plays', true );
		$likes = (int) get_post_meta( $stat_track_id, '_cf_total_likes_count', true );

		$total_views += $views;
		$like_sum    += $likes;
		$like_count++;

		if ( $views > $most_played_views ) {
			$most_played_views = $views;
			$most_played_id    = (int) $stat_track_id;
		}
	}

	if ( $like_count > 0 ) {
		$avg_likes = (int) round( $like_sum / $like_count );
	}

	if ( $most_played_id > 0 ) {
		$most_played_title = get_the_title( $most_played_id );
		$most_played_link  = get_permalink( $most_played_id );
	}
}

/**
 * Render a track card matching archive-tracks.php style.
 *
 * @param int $track_id Track post ID.
 */
$cf_render_track_card = static function ( $track_id ) {
	$cover_image = get_post_meta( $track_id, 'track_cover_url', true );
	if ( ! $cover_image ) {
		$cover_image = get_the_post_thumbnail_url( $track_id, 'medium' );
	}
	if ( ! $cover_image ) {
		$cover_image = collective_finity_default_art_url();
	}

	$track_audio   = get_post_meta( $track_id, 'track_audio_url', true );
	$track_preview = get_post_meta( $track_id, 'track_preview_url', true );
	$audio_url     = ! empty( $track_preview ) ? $track_preview : $track_audio;
	$artists       = wp_get_post_terms( $track_id, 'track_artist', array( 'fields' => 'names' ) );
	$artist_name   = ! empty( $artists ) ? $artists[0] : 'Collective Finity';
	$genres        = wp_get_post_terms( $track_id, 'music_genre', array( 'fields' => 'names' ) );
	$genre_name    = ! empty( $genres ) ? $genres[0] : '';
	$title         = get_the_title( $track_id );
	$permalink     = get_permalink( $track_id );
	?>
	<a href="<?php echo esc_url( $permalink ); ?>" class="cf-card">
		<div class="cf-cover">
			<img src="<?php echo esc_url( $cover_image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
			<button type="button" class="cf-interaction-btn cf-like-btn cf-heart-btn" data-track-id="<?php echo esc_attr( (string) $track_id ); ?>" title="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>" onclick="event.preventDefault();">
				<span class="dashicons dashicons-heart"></span>
			</button>
			<button type="button" class="cf-play-btn" aria-label="<?php echo esc_attr( sprintf( __( 'Play %s', 'collective-finity' ), $title ) ); ?>" onclick="event.preventDefault(); event.stopPropagation(); if (window.playTrack) { window.playTrack('<?php echo esc_js( $audio_url ); ?>', '<?php echo esc_js( $title ); ?>', '<?php echo esc_js( $artist_name ); ?>', '<?php echo esc_js( $cover_image ); ?>'); }">
				<span class="dashicons dashicons-controls-play"></span>
			</button>
		</div>
		<div class="cf-card-title"><?php echo esc_html( $title ); ?></div>
		<div class="cf-card-sub"><?php echo esc_html( $artist_name ); ?></div>
		<?php if ( $genre_name ) : ?><span class="cf-card-chip"><?php echo esc_html( $genre_name ); ?></span><?php endif; ?>
	</a>
	<?php
};

/**
 * Render an album card.
 *
 * @param int $album_id Album post ID.
 */
$cf_render_album_card = static function ( $album_id ) {
	$link  = get_permalink( $album_id );
	$cover = get_the_post_thumbnail_url( $album_id, 'medium' );
	$count_q = new WP_Query(
		array(
			'post_type'      => 'tracks',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'associated_album',
					'value'   => $album_id,
					'compare' => '=',
				),
			),
		)
	);
	$count = (int) $count_q->found_posts;

	if ( empty( $cover ) && ! empty( $count_q->posts ) ) {
		foreach ( $count_q->posts as $cf_t_id ) {
			$cf_first_cover = get_post_meta( $cf_t_id, 'track_cover_url', true );
			if ( ! empty( $cf_first_cover ) ) {
				$cover = $cf_first_cover;
				break;
			}
		}
	}
	if ( empty( $cover ) ) {
		$cover = collective_finity_default_art_url();
	}

	$title = get_the_title( $album_id );
	?>
	<a href="<?php echo esc_url( $link ); ?>" class="cf-card">
		<div class="cf-cover"><img src="<?php echo esc_url( $cover ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy"></div>
		<div class="cf-card-title"><?php echo esc_html( $title ); ?></div>
		<div class="cf-card-sub"><?php echo esc_html( sprintf( _n( 'Album · %d track', 'Album · %d tracks', $count, 'collective-finity' ), $count ) ); ?></div>
	</a>
	<?php
};
?>

<div id="primary" class="content-area cf-artist-page">
	<main id="main" class="site-main">

		<header class="cf-artist-hero">
			<div
				class="cf-artist-hero-bg"
				style="background-image: url('<?php echo esc_url( $photo_url ); ?>');"
				aria-hidden="true"
			></div>
			<div class="cf-artist-hero-inner">
				<div class="cf-artist-photo-wrap">
					<img
						src="<?php echo esc_url( $photo_url ); ?>"
						alt="<?php echo esc_attr( $artist_name ); ?>"
						class="cf-artist-photo"
						loading="eager"
						width="280"
						height="280"
					>
				</div>

				<div class="cf-artist-hero-content">
					<p class="cf-artist-eyebrow"><?php esc_html_e( 'Artist', 'collective-finity' ); ?></p>
					<h1 class="cf-artist-name"><?php echo esc_html( $artist_name ); ?></h1>

					<?php if ( $artist_bio ) : ?>
						<p class="cf-artist-bio"><?php echo esc_html( $artist_bio ); ?></p>
					<?php endif; ?>

					<div class="cf-artist-stats">
						<span class="cf-artist-stat cf-glass-card">
							<span class="cf-artist-stat-num"><?php echo esc_html( number_format_i18n( $track_count ) ); ?></span>
							<span class="cf-artist-stat-label"><?php echo esc_html( _n( 'Track', 'Tracks', $track_count, 'collective-finity' ) ); ?></span>
						</span>
						<span class="cf-artist-stat cf-glass-card">
							<span class="cf-artist-stat-num"><?php echo esc_html( number_format_i18n( $album_count ) ); ?></span>
							<span class="cf-artist-stat-label"><?php echo esc_html( _n( 'Album', 'Albums', $album_count, 'collective-finity' ) ); ?></span>
						</span>
					</div>

					<?php
					$has_social = false;
					foreach ( $social_links as $social ) {
						if ( ! empty( $social['url'] ) ) {
							$has_social = true;
							break;
						}
					}
					?>
					<?php if ( $has_social ) : ?>
						<nav class="cf-artist-social" aria-label="<?php esc_attr_e( 'Artist social links', 'collective-finity' ); ?>">
							<?php foreach ( $social_links as $icon_slug => $social ) : ?>
								<?php if ( empty( $social['url'] ) ) : ?>
									<?php continue; ?>
								<?php endif; ?>
								<a
									href="<?php echo esc_url( $social['url'] ); ?>"
									class="cf-artist-social-btn cf-glass-card"
									target="_blank"
									rel="noopener noreferrer"
									title="<?php echo esc_attr( $social['label'] ); ?>"
									aria-label="<?php echo esc_attr( $social['label'] ); ?>"
								>
									<?php echo collective_finity_footer_social_icon( $icon_slug ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted SVG markup. ?>
								</a>
							<?php endforeach; ?>
						</nav>
					<?php endif; ?>
				</div>
			</div>
		</header>

		<div class="cf-artist-body">

			<?php if ( $show_pro ) : ?>
				<section class="cf-artist-section cf-artist-pro" aria-labelledby="cf-artist-pro-heading">
					<div class="cf-artist-section-head">
						<h2 id="cf-artist-pro-heading"><?php esc_html_e( 'Professional Info & Stats', 'collective-finity' ); ?></h2>
					</div>

					<?php if ( $years_active || $location || $label_crew ) : ?>
						<div class="cf-artist-pro-info">
							<?php if ( $years_active ) : ?>
								<div class="cf-artist-pro-card cf-glass-card">
									<span class="cf-artist-pro-label"><?php esc_html_e( 'Years Active', 'collective-finity' ); ?></span>
									<span class="cf-artist-pro-value"><?php echo esc_html( $years_active ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( $location ) : ?>
								<div class="cf-artist-pro-card cf-glass-card">
									<span class="cf-artist-pro-label"><?php esc_html_e( 'Location', 'collective-finity' ); ?></span>
									<span class="cf-artist-pro-value"><?php echo esc_html( $location ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( $label_crew ) : ?>
								<div class="cf-artist-pro-card cf-glass-card">
									<span class="cf-artist-pro-label"><?php esc_html_e( 'Label / Crew', 'collective-finity' ); ?></span>
									<span class="cf-artist-pro-value"><?php echo esc_html( $label_crew ); ?></span>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $genre_terms ) ) : ?>
						<div class="cf-artist-genre-badges" aria-label="<?php esc_attr_e( 'Genres', 'collective-finity' ); ?>">
							<div class="cf-artist-genre-track">
								<div class="cf-artist-genre-group">
									<?php foreach ( $genre_terms as $genre_term ) : ?>
										<?php
										$genre_link = get_term_link( $genre_term );
										if ( is_wp_error( $genre_link ) ) {
											$genre_link = '';
										}
										?>
										<?php if ( $genre_link ) : ?>
											<a class="cf-artist-genre-badge" href="<?php echo esc_url( $genre_link ); ?>"><?php echo esc_html( $genre_term->name ); ?></a>
										<?php else : ?>
											<span class="cf-artist-genre-badge"><?php echo esc_html( $genre_term->name ); ?></span>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
								<div class="cf-artist-genre-group" aria-hidden="true">
									<?php foreach ( $genre_terms as $genre_term ) : ?>
										<?php
										$genre_link = get_term_link( $genre_term );
										if ( is_wp_error( $genre_link ) ) {
											$genre_link = '';
										}
										?>
										<?php if ( $genre_link ) : ?>
											<a class="cf-artist-genre-badge" href="<?php echo esc_url( $genre_link ); ?>" tabindex="-1"><?php echo esc_html( $genre_term->name ); ?></a>
										<?php else : ?>
											<span class="cf-artist-genre-badge"><?php echo esc_html( $genre_term->name ); ?></span>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<div class="cf-artist-auto-stats">
						<div class="cf-artist-pro-card cf-glass-card">
							<span class="cf-artist-pro-label"><?php esc_html_e( 'Total views', 'collective-finity' ); ?></span>
							<span class="cf-artist-pro-value"><?php echo esc_html( number_format_i18n( $total_views ) ); ?></span>
						</div>
						<div class="cf-artist-pro-card cf-glass-card">
							<span class="cf-artist-pro-label"><?php esc_html_e( 'Avg likes / track', 'collective-finity' ); ?></span>
							<span class="cf-artist-pro-value"><?php echo esc_html( number_format_i18n( $avg_likes ) ); ?></span>
						</div>
						<div class="cf-artist-pro-card cf-glass-card">
							<span class="cf-artist-pro-label"><?php esc_html_e( 'Most played', 'collective-finity' ); ?></span>
							<span class="cf-artist-pro-value">
								<?php if ( $most_played_link && $most_played_title ) : ?>
									<a href="<?php echo esc_url( $most_played_link ); ?>" class="cf-artist-most-played-link"><?php echo esc_html( $most_played_title ); ?></a>
								<?php else : ?>
									<?php esc_html_e( '—', 'collective-finity' ); ?>
								<?php endif; ?>
							</span>
						</div>
					</div>
				</section>
			<?php endif; ?>

			<section class="cf-artist-section" aria-labelledby="cf-artist-tracks-heading">
				<div class="cf-artist-section-head">
					<h2 id="cf-artist-tracks-heading"><?php esc_html_e( 'Tracks', 'collective-finity' ); ?></h2>
					<?php if ( $track_count > 0 ) : ?>
						<span class="cf-artist-section-count"><?php echo esc_html( (string) $track_count ); ?></span>
					<?php endif; ?>
				</div>

				<?php if ( have_posts() ) : ?>
					<div class="cf-card-grid">
						<?php
						while ( have_posts() ) :
							the_post();
							$cf_render_track_card( get_the_ID() );
						endwhile;
						?>
					</div>

					<div class="cf-artist-pagination">
						<?php
						$track_add_args = array();
						if ( $album_page > 1 ) {
							$track_add_args['album_page'] = $album_page;
						}
						if ( $blog_page > 1 ) {
							$track_add_args['blog_page'] = $blog_page;
						}
						the_posts_pagination(
							array(
								'mid_size'  => 2,
								'prev_text' => __( '← Previous', 'collective-finity' ),
								'next_text' => __( 'Next →', 'collective-finity' ),
								'add_args'  => $track_add_args,
							)
						);
						?>
					</div>
				<?php else : ?>
					<div class="cf-artist-empty cf-glass-card">
						<h3><?php esc_html_e( 'No Tracks Yet', 'collective-finity' ); ?></h3>
						<p><?php esc_html_e( 'Tracks tagged with this artist will appear here.', 'collective-finity' ); ?></p>
					</div>
				<?php endif; ?>
			</section>

			<?php if ( ! empty( $album_ids ) ) : ?>
				<section class="cf-artist-section" aria-labelledby="cf-artist-albums-heading">
					<div class="cf-artist-section-head">
						<h2 id="cf-artist-albums-heading"><?php esc_html_e( 'Albums', 'collective-finity' ); ?></h2>
						<span class="cf-artist-section-count"><?php echo esc_html( (string) $album_count ); ?></span>
					</div>
					<div class="cf-card-grid cf-artist-albums-grid">
						<?php foreach ( $album_page_ids as $album_id ) : ?>
							<?php $cf_render_album_card( (int) $album_id ); ?>
						<?php endforeach; ?>
					</div>
					<?php
					$album_preserve = array();
					if ( $tracks_paged > 1 ) {
						$album_preserve['paged'] = $tracks_paged;
					}
					if ( $blog_page > 1 ) {
						$album_preserve['blog_page'] = $blog_page;
					}
					$cf_render_section_pagination(
						'album_page',
						$album_page,
						$album_total_pages,
						$album_preserve,
						__( 'Albums pagination', 'collective-finity' )
					);
					?>
				</section>
			<?php endif; ?>

			<?php
			if ( $linked_user_id > 0 ) :
				$blog_q = new WP_Query(
					array(
						'post_type'      => 'post',
						'post_status'    => 'publish',
						'author'         => $linked_user_id,
						'posts_per_page' => $blogs_per_page,
						'paged'          => $blog_page,
						'orderby'        => 'date',
						'order'          => 'DESC',
					)
				);

				if ( $blog_q->have_posts() ) :
					$blog_total_pages = max( 1, (int) $blog_q->max_num_pages );
					?>
					<section class="cf-artist-section" aria-labelledby="cf-artist-blog-heading">
						<div class="cf-artist-section-head">
							<h2 id="cf-artist-blog-heading"><?php esc_html_e( 'Blog Posts', 'collective-finity' ); ?></h2>
							<span class="cf-artist-section-count"><?php echo esc_html( (string) (int) $blog_q->found_posts ); ?></span>
						</div>
						<div class="cf-artist-blog-grid">
							<?php
							while ( $blog_q->have_posts() ) :
								$blog_q->the_post();
								if ( function_exists( 'collective_finity_render_blog_card' ) ) {
									echo collective_finity_render_blog_card( get_post(), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper returns escaped HTML.
								}
							endwhile;
							wp_reset_postdata();
							?>
						</div>
						<?php
						$blog_preserve = array();
						if ( $tracks_paged > 1 ) {
							$blog_preserve['paged'] = $tracks_paged;
						}
						if ( $album_page > 1 ) {
							$blog_preserve['album_page'] = $album_page;
						}
						$cf_render_section_pagination(
							'blog_page',
							min( $blog_page, $blog_total_pages ),
							$blog_total_pages,
							$blog_preserve,
							__( 'Blog posts pagination', 'collective-finity' )
						);
						?>
					</section>
					<?php
				endif;
			endif;
			?>

		</div>
	</main>
</div>

<style>
	.cf-artist-page {
		--cf-artist-gold: #FFB700;
	}

	.cf-glass-card {
		background: rgba(16, 16, 16, 0.72);
		backdrop-filter: blur(14px);
		border: 1px solid rgba(255, 255, 255, 0.08);
		box-shadow: 0 10px 35px rgba(0, 0, 0, 0.45);
		box-sizing: border-box;
	}

	.cf-artist-hero {
		position: relative;
		height: 420px;
		padding: 0 5px;
		overflow: hidden;
		display: flex;
		align-items: center;
		box-sizing: border-box;
	}
	.cf-artist-hero-bg {
		position: absolute;
		inset: 0;
		z-index: 0;
		background-position: 50% 50%;
		background-size: cover;
		background-repeat: no-repeat;
		will-change: background-position;
	}
	.cf-artist-hero-bg.is-resetting {
		transition: background-position 0.45s ease;
	}
	.cf-artist-hero::before {
		content: '';
		position: absolute;
		inset: 0;
		z-index: 1;
		background:
			linear-gradient(180deg, rgba(0, 0, 0, 0.55) 0%, rgba(10, 10, 10, 0.92) 70%, #0a0a0a 100%),
			radial-gradient(ellipse 70% 60% at 20% 40%, rgba(255, 183, 0, 0.12) 0%, transparent 60%);
		pointer-events: none;
	}
	.cf-artist-hero-inner {
		position: relative;
		z-index: 2;
		max-width: 1100px;
		width: 100%;
		margin: 0 auto;
		display: flex;
		align-items: center;
		gap: 36px;
	}

	.cf-artist-photo-wrap {
		position: relative;
		flex-shrink: 0;
		width: 200px;
		height: 200px;
		border-radius: 50%;
		padding: 0;
		overflow: visible;
		background: transparent;
		border: none;
		box-shadow:
			0 22px 40px rgba(0, 0, 0, 0.55),
			0 10px 18px rgba(0, 0, 0, 0.35),
			0 28px 48px -8px rgba(0, 0, 0, 0.5);
		filter: drop-shadow(0 18px 14px rgba(0, 0, 0, 0.45));
	}
	.cf-artist-photo-wrap::before {
		content: '';
		position: absolute;
		left: 12%;
		right: 12%;
		bottom: -14px;
		height: 22px;
		border-radius: 50%;
		background: radial-gradient(ellipse at center, rgba(0, 0, 0, 0.55) 0%, transparent 70%);
		z-index: 0;
		pointer-events: none;
	}
	.cf-artist-photo-wrap::after {
		content: '';
		position: absolute;
		inset: 0;
		border-radius: 50%;
		z-index: 2;
		pointer-events: none;
		box-shadow:
			inset -10px -14px 28px rgba(0, 0, 0, 0.55),
			inset 8px 10px 22px rgba(255, 183, 0, 0.28),
			inset 0 0 0 1px rgba(255, 183, 0, 0.22);
	}
	.cf-artist-photo {
		position: relative;
		z-index: 1;
		width: 100%;
		height: 100%;
		object-fit: cover;
		border-radius: 50%;
		display: block;
	}

	.cf-artist-hero-content { flex: 1; min-width: 0; }
	.cf-artist-eyebrow {
		margin: 0 0 6px;
		font-size: 12px;
		font-weight: 700;
		letter-spacing: 0.24em;
		text-transform: uppercase;
		color: var(--cf-artist-gold, #FFB700);
	}
	.cf-artist-name {
		margin: 0 0 10px;
		font-size: clamp(28px, 4.5vw, 48px);
		font-weight: 800;
		line-height: 1.1;
		color: #fff;
		letter-spacing: -0.02em;
	}
	.cf-artist-bio {
		margin: 0 0 14px;
		max-width: 560px;
		font-size: 14px;
		line-height: 1.55;
		color: #b0b0b0;
	}

	.cf-artist-stats {
		display: flex;
		flex-wrap: wrap;
		gap: 10px;
		margin-bottom: 14px;
	}
	.cf-artist-stat {
		display: inline-flex;
		flex-direction: column;
		align-items: flex-start;
		gap: 2px;
		padding: 10px 16px;
		border-radius: 12px;
		min-width: 88px;
		transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
	}
	.cf-artist-stat:hover {
		transform: translateY(-2px);
		border-color: rgba(255, 183, 0, 0.55);
		box-shadow:
			0 0 0 1px rgba(255, 183, 0, 0.35),
			0 14px 28px -10px rgba(0, 0, 0, 0.55),
			0 0 28px 4px rgba(255, 183, 0, 0.18);
	}
	.cf-artist-stat-num {
		font-size: 20px;
		font-weight: 700;
		color: #fff;
		line-height: 1.2;
	}
	.cf-artist-stat-label {
		font-size: 11px;
		letter-spacing: 0.08em;
		text-transform: uppercase;
		color: #888;
	}

	.cf-artist-social {
		display: flex;
		flex-wrap: wrap;
		gap: 10px;
	}
	.cf-artist-social-btn {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 42px;
		height: 42px;
		border-radius: 50%;
		color: #fff;
		text-decoration: none;
		transition: border-color 0.2s ease, color 0.2s ease, background 0.2s ease, transform 0.2s ease;
	}
	.cf-artist-social-btn svg {
		width: 18px;
		height: 18px;
		fill: currentColor;
	}
	.cf-artist-social-btn:hover {
		border-color: rgba(255, 183, 0, 0.55);
		color: var(--cf-artist-gold, #FFB700);
		transform: translateY(-2px);
	}

	.cf-artist-body {
		max-width: 1200px;
		margin: 0 auto;
		padding: 36px 5px 0;
	}
	.cf-artist-section { margin-bottom: 48px; }
	.cf-artist-section-head {
		display: flex;
		align-items: baseline;
		justify-content: space-between;
		gap: 12px;
		margin-bottom: 20px;
	}
	.cf-artist-section-head h2 {
		margin: 0;
		font-size: 22px;
		font-weight: 700;
		color: #fff;
	}
	.cf-artist-section-count {
		font-size: 13px;
		color: #777;
		font-family: var(--cf-mono, 'Space Mono', monospace);
	}

	.cf-artist-pro-info,
	.cf-artist-auto-stats {
		display: grid;
		grid-template-columns: repeat(3, minmax(0, 1fr));
		gap: 14px;
		margin-bottom: 18px;
	}
	.cf-artist-pro-card {
		display: flex;
		flex-direction: column;
		gap: 6px;
		padding: 16px 18px;
		border-radius: 14px;
		min-width: 0;
		transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
	}
	.cf-artist-pro-card:hover {
		transform: translateY(-2px);
		border-color: rgba(255, 183, 0, 0.55);
		box-shadow:
			0 0 0 1px rgba(255, 183, 0, 0.35),
			0 14px 28px -10px rgba(0, 0, 0, 0.55),
			0 0 28px 4px rgba(255, 183, 0, 0.18);
	}
	.cf-artist-pro-label {
		font-size: 11px;
		letter-spacing: 0.1em;
		text-transform: uppercase;
		color: #888;
	}
	.cf-artist-pro-value {
		font-size: 16px;
		font-weight: 600;
		color: #fff;
		line-height: 1.35;
		word-break: break-word;
	}
	.cf-artist-most-played-link {
		color: var(--cf-artist-gold, #FFB700);
		text-decoration: none;
	}
	.cf-artist-most-played-link:hover {
		text-decoration: underline;
	}
	.cf-artist-genre-badges {
		position: relative;
		overflow: hidden;
		margin-bottom: 18px;
		mask-image: linear-gradient(90deg, transparent 0%, #000 8%, #000 92%, transparent 100%);
		-webkit-mask-image: linear-gradient(90deg, transparent 0%, #000 8%, #000 92%, transparent 100%);
	}
	.cf-artist-genre-track {
		display: flex;
		width: max-content;
		align-items: center;
		animation: cf-artist-genre-marquee 36s linear infinite;
		will-change: transform;
	}
	.cf-artist-genre-badges:hover .cf-artist-genre-track {
		animation-play-state: paused;
	}
	.cf-artist-genre-group {
		display: flex;
		flex-wrap: nowrap;
		align-items: center;
		gap: 8px;
		padding-right: 8px;
	}
	.cf-artist-genre-badge {
		display: inline-flex;
		align-items: center;
		flex-shrink: 0;
		padding: 6px 14px;
		border-radius: 999px;
		font-size: 12px;
		font-weight: 600;
		letter-spacing: 0.02em;
		color: #1a1400;
		background: rgba(255, 183, 0, 0.92);
		border: 1px solid rgba(255, 183, 0, 0.55);
		box-shadow: 0 0 16px rgba(255, 183, 0, 0.18);
		text-decoration: none;
		white-space: nowrap;
		transition: transform 0.2s ease, box-shadow 0.2s ease;
	}
	a.cf-artist-genre-badge:hover {
		transform: translateY(-1px);
		box-shadow: 0 0 20px rgba(255, 183, 0, 0.32);
		color: #1a1400;
	}
	@keyframes cf-artist-genre-marquee {
		from { transform: translateX(0); }
		to { transform: translateX(-50%); }
	}

	.cf-artist-albums-grid {
		grid-template-columns: repeat(5, minmax(0, 1fr));
	}

	.cf-card-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
		gap: 22px;
	}
	.cf-card {
		display: block;
		text-decoration: none;
		color: inherit;
		background: var(--cf-bg-card, #141414);
		border: var(--cf-card-border-width, 1px) solid var(--cf-border, #232323);
		border-radius: var(--cf-card-radius, 12px);
		box-shadow: var(--cf-card-shadow, 0 14px 28px -12px rgba(0,0,0,0.55));
		overflow: hidden;
		padding: 0 0 12px;
		transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
	}
	.cf-cover {
		position: relative;
		width: 100%;
		aspect-ratio: 1;
		overflow: hidden;
		background: #0c0c0c;
		margin-bottom: 10px;
	}
	.cf-cover img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		display: block;
		transition: transform 0.2s ease;
	}
	.cf-play-btn {
		position: absolute;
		right: 8px;
		bottom: 8px;
		width: 40px;
		height: 40px;
		border: none;
		border-radius: 50%;
		background: var(--primary-color, #FFB700);
		display: flex;
		align-items: center;
		justify-content: center;
		opacity: 0;
		transform: translateY(6px);
		transition: all 0.18s ease;
		cursor: pointer;
		box-shadow: 0 8px 16px rgba(0,0,0,.4);
	}
	.cf-play-btn .dashicons { color: #1a1400; font-size: 18px; width: 18px; height: 18px; }
	.cf-heart-btn.cf-interaction-btn {
		position: absolute;
		left: 8px;
		top: 8px;
		width: 28px;
		height: 28px;
		border: none;
		border-radius: 50%;
		background: rgba(0,0,0,.55);
		display: flex;
		align-items: center;
		justify-content: center;
		opacity: 0;
		transition: opacity 0.18s ease;
		cursor: pointer;
		padding: 0;
	}
	.cf-heart-btn .dashicons { color: #fff; font-size: 14px; width: 14px; height: 14px; }
	.cf-heart-btn.cf-interaction-btn.active { opacity: 1; }
	.cf-heart-btn.cf-interaction-btn.active .dashicons { color: var(--primary-color, #FFB700); }
	.cf-card-title {
		font-size: 14px;
		font-weight: 600;
		color: #fff;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		padding: 0 12px;
	}
	.cf-card-sub {
		font-size: 12px;
		color: #7A7A7A;
		margin-top: 3px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		padding: 0 12px;
	}
	.cf-card-chip {
		display: inline-block;
		margin: 6px 12px 0;
		font-size: 10px;
		color: #B3B3B3;
		background: rgba(255,255,255,0.04);
		border: 1px solid var(--cf-border, #232323);
		padding: 2px 8px;
		border-radius: 10px;
	}
	.cf-card:is(:hover, :focus-visible, :focus-within) {
		border-color: rgba(255, 183, 0, 0.55);
		box-shadow:
			0 0 0 1px rgba(255, 183, 0, 0.35),
			0 14px 28px -10px rgba(0, 0, 0, 0.55),
			0 0 28px 4px rgba(255, 183, 0, 0.18);
	}
	.cf-card:is(:hover, :focus-visible, :focus-within) .cf-cover img { transform: scale(1.03); }
	.cf-card:is(:hover, :focus-visible, :focus-within) .cf-play-btn { opacity: 1; transform: translateY(0); }
	.cf-card:is(:hover, :focus-visible, :focus-within) .cf-heart-btn.cf-interaction-btn { opacity: 1; }
	.cf-card:is(:hover, :focus-visible, :focus-within) .cf-card-sub { color: var(--primary-color, #FFB700); }

	.cf-artist-blog-grid {
		display: grid;
		grid-template-columns: repeat(4, minmax(0, 1fr));
		gap: 22px;
	}
	.cf-artist-page .cf-bh-card {
		transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
	}
	.cf-artist-page .cf-bh-card:hover,
	.cf-artist-page .cf-bh-card:focus-visible {
		border-color: rgba(255, 183, 0, 0.55);
		transform: translateY(-2px);
		box-shadow:
			0 0 0 1px rgba(255, 183, 0, 0.35),
			0 14px 28px -10px rgba(0, 0, 0, 0.55),
			0 0 28px 4px rgba(255, 183, 0, 0.18);
	}

	.cf-artist-empty {
		text-align: center;
		padding: 48px 24px;
		border-radius: 16px;
	}
	.cf-artist-empty h3 { margin: 0 0 8px; color: #fff; font-size: 20px; }
	.cf-artist-empty p { margin: 0; color: #888; }

	.cf-artist-pagination { margin-top: 36px; text-align: center; }
	.cf-artist-pagination .nav-links {
		display: flex;
		justify-content: center;
		flex-wrap: wrap;
		gap: 10px;
	}
	.cf-artist-pagination .nav-links a,
	.cf-artist-pagination .nav-links span {
		padding: 8px 16px;
		border: 1px solid #2a2a2a;
		border-radius: 8px;
		color: #888;
		text-decoration: none;
		transition: all 0.2s ease;
	}
	.cf-artist-pagination .nav-links a:hover {
		border-color: #FFB700;
		color: #FFB700;
	}
	.cf-artist-pagination .nav-links .current {
		background: rgba(255, 183, 0, 0.1);
		border-color: #FFB700;
		color: #FFB700;
	}

	@media (max-width: 767px) {
		.cf-artist-hero {
			height: 360px;
			padding: 0 5px;
		}
		.cf-artist-hero-inner {
			flex-direction: column;
			align-items: center;
			text-align: center;
			gap: 16px;
		}
		.cf-artist-photo-wrap { width: 128px; height: 128px; }
		.cf-artist-name { font-size: clamp(24px, 7vw, 34px); margin-bottom: 8px; }
		.cf-artist-bio {
			margin-left: auto;
			margin-right: auto;
			margin-bottom: 10px;
			font-size: 13px;
			line-height: 1.45;
			display: -webkit-box;
			-webkit-line-clamp: 2;
			-webkit-box-orient: vertical;
			overflow: hidden;
		}
		.cf-artist-stats { margin-bottom: 10px; justify-content: center; }
		.cf-artist-social { justify-content: center; }
		.cf-artist-stat { align-items: center; padding: 8px 12px; }
		.cf-artist-stat-num { font-size: 18px; }
		.cf-artist-pro-info,
		.cf-artist-auto-stats {
			grid-template-columns: 1fr;
		}
		.cf-card-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
		.cf-artist-albums-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
		.cf-artist-blog-grid { grid-template-columns: 1fr; }
		.cf-artist-genre-track { animation-duration: 28s; }
		.cf-play-btn { opacity: 1; transform: none; width: 32px; height: 32px; }
		.cf-heart-btn.cf-interaction-btn { opacity: 1; }
	}

	@media (min-width: 768px) and (max-width: 1024px) {
		.cf-artist-albums-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
		.cf-artist-blog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
	}
</style>

<script>
(function () {
	var hero = document.querySelector('.cf-artist-hero');
	if (!hero) {
		return;
	}

	var bg = hero.querySelector('.cf-artist-hero-bg');
	if (!bg) {
		return;
	}

	var posY = 50;
	var defaultY = 50;
	var step = 2.5;
	var resetTimer = null;

	function clamp(value, min, max) {
		return Math.min(max, Math.max(min, value));
	}

	function applyPos(animate) {
		if (animate) {
			bg.classList.add('is-resetting');
		} else {
			bg.classList.remove('is-resetting');
		}
		bg.style.backgroundPosition = '50% ' + posY + '%';
	}

	function onWheel(e) {
		e.preventDefault();
		if (resetTimer) {
			clearTimeout(resetTimer);
			resetTimer = null;
		}
		bg.classList.remove('is-resetting');

		// Wheel up (deltaY < 0) → pan toward top (lower %).
		// Wheel down (deltaY > 0) → pan toward bottom (higher %).
		var delta = e.deltaY;
		if (e.deltaMode === 1) {
			delta *= 16;
		} else if (e.deltaMode === 2) {
			delta *= hero.clientHeight;
		}

		posY = clamp(posY + (delta > 0 ? step : -step) * Math.min(Math.abs(delta) / 40, 3), 0, 100);
		applyPos(false);
	}

	function onLeave() {
		bg.classList.add('is-resetting');
		posY = defaultY;
		applyPos(true);
		resetTimer = setTimeout(function () {
			bg.classList.remove('is-resetting');
			resetTimer = null;
		}, 450);
	}

	hero.addEventListener('wheel', onWheel, { passive: false });
	hero.addEventListener('mouseleave', onLeave);
})();
</script>

<?php get_footer(); ?>
