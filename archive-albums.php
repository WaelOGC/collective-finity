<?php
/**
 * Template Name: Albums Archive
 * Description: Displays all albums in a polished collection layout.
 *
 * @package Collective_Finity
 */

get_header();

$cf_album_genre_filter = isset( $_GET['genre'] ) ? sanitize_title( wp_unslash( $_GET['genre'] ) ) : '';

/**
 * Format seconds as mm:ss.
 *
 * @param int $seconds Duration in seconds.
 * @return string
 */
$cf_format_track_time = static function ( $seconds ) {
	$seconds = max( 0, (int) $seconds );
	return sprintf( '%02d:%02d', (int) floor( $seconds / 60 ), $seconds % 60 );
};

/**
 * Best-effort audio duration (seconds) from a media URL attachment.
 *
 * @param string $audio_url Audio URL.
 * @return int
 */
$cf_audio_duration_seconds = static function ( $audio_url ) {
	if ( ! $audio_url ) {
		return 0;
	}
	$attachment_id = attachment_url_to_postid( $audio_url );
	if ( ! $attachment_id ) {
		return 0;
	}
	$meta = wp_get_attachment_metadata( $attachment_id );
	if ( ! empty( $meta['length'] ) ) {
		return (int) $meta['length'];
	}
	if ( ! empty( $meta['length_formatted'] ) && preg_match( '/^(\d+):(\d{2})$/', $meta['length_formatted'], $m ) ) {
		return ( (int) $m[1] * 60 ) + (int) $m[2];
	}
	return 0;
};

/**
 * Resolve duration in seconds for a track (meta first, then attachment metadata).
 *
 * @param int $track_id Track post ID.
 * @return int
 */
$cf_resolve_track_duration_seconds = static function ( $track_id ) use ( $cf_audio_duration_seconds ) {
	$meta_duration = get_post_meta( $track_id, 'track_duration', true );
	if ( is_string( $meta_duration ) && preg_match( '/^(\d{1,2}):(\d{2})$/', trim( $meta_duration ), $m ) ) {
		return ( (int) $m[1] * 60 ) + (int) $m[2];
	}
	if ( is_numeric( $meta_duration ) && (int) $meta_duration > 0 ) {
		return (int) $meta_duration;
	}

	$track_audio   = get_post_meta( $track_id, 'track_audio_url', true );
	$track_preview = get_post_meta( $track_id, 'track_preview_url', true );
	$audio_url     = ! empty( $track_preview ) ? $track_preview : $track_audio;
	return $cf_audio_duration_seconds( $audio_url );
};

/**
 * Resolve album cover URL.
 *
 * @param int   $album_id  Album post ID.
 * @param array $track_ids Track IDs for the album.
 * @return string
 */
$cf_resolve_album_cover = static function ( $album_id, $track_ids = array() ) {
	$cover_url = get_the_post_thumbnail_url( $album_id, 'medium' );
	if ( empty( $cover_url ) && ! empty( $track_ids ) ) {
		foreach ( $track_ids as $cf_t_id ) {
			$cf_first_cover = get_post_meta( $cf_t_id, 'track_cover_url', true );
			if ( ! empty( $cf_first_cover ) ) {
				$cover_url = $cf_first_cover;
				break;
			}
		}
	}
	if ( empty( $cover_url ) ) {
		$cover_url = collective_finity_default_art_url();
	}
	return $cover_url;
};

/**
 * Build playable listen meta for the first track in an album.
 *
 * @param int   $album_id  Album post ID.
 * @param array $track_ids Track IDs for the album.
 * @return array{audio:string,title:string,artist:string,cover:string,permalink:string}
 */
$cf_build_album_listen_meta = static function ( $album_id, $track_ids ) use ( $cf_resolve_album_cover ) {
	$listen = array(
		'audio'      => '',
		'title'      => '',
		'artist'     => collective_finity_brand_name(),
		'cover'      => $cf_resolve_album_cover( $album_id, $track_ids ),
		'permalink'  => get_permalink( $album_id ),
	);

	if ( empty( $track_ids ) ) {
		return $listen;
	}

	$cf_tid = (int) $track_ids[0];
	$cf_audio    = get_post_meta( $cf_tid, 'track_audio_url', true );
	$cf_preview  = get_post_meta( $cf_tid, 'track_preview_url', true );
	$cf_playback = ! empty( $cf_preview ) ? $cf_preview : $cf_audio;

	$cf_artists = wp_get_post_terms( $cf_tid, 'track_artist' );
	$cf_artist  = ( ! is_wp_error( $cf_artists ) && ! empty( $cf_artists ) ) ? $cf_artists[0]->name : collective_finity_brand_name();

	$cf_tcover = get_post_meta( $cf_tid, 'track_cover_url', true );
	if ( ! $cf_tcover ) {
		$cf_tcover = get_the_post_thumbnail_url( $cf_tid, 'medium' );
	}
	if ( ! $cf_tcover ) {
		$cf_tcover = $listen['cover'];
	}

	$listen['audio']      = $cf_playback ? $cf_playback : '';
	$listen['title']      = get_the_title( $cf_tid );
	$listen['artist']     = $cf_artist;
	$listen['cover']      = $cf_tcover;
	$listen['permalink']  = get_permalink( $cf_tid );

	return $listen;
};

/**
 * Sum album duration from track IDs.
 *
 * @param array $track_ids Track IDs.
 * @return int Total seconds.
 */
$cf_album_duration_seconds = static function ( $track_ids ) use ( $cf_resolve_track_duration_seconds ) {
	$total = 0;
	foreach ( $track_ids as $cf_track_id ) {
		$total += $cf_resolve_track_duration_seconds( (int) $cf_track_id );
	}
	return $total;
};

// Only genres assigned to at least one published album (not track-only genres).
$cf_album_ids_for_genres = get_posts(
	array(
		'post_type'      => 'albums',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	)
);
$cf_album_genres = ! empty( $cf_album_ids_for_genres )
	? get_terms(
		array(
			'taxonomy'   => 'music_genre',
			'hide_empty' => false,
			'object_ids' => $cf_album_ids_for_genres,
		)
	)
	: array();
if ( is_wp_error( $cf_album_genres ) ) {
	$cf_album_genres = array();
}

$cf_total_albums = (int) wp_count_posts( 'albums' )->publish;
$cf_total_tracks = (int) wp_count_posts( 'tracks' )->publish;
$cf_total_genres = count( $cf_album_genres );

$cf_albums_query_args = array(
	'post_type'      => 'albums',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
);
if ( '' !== $cf_album_genre_filter ) {
	$cf_albums_query_args['tax_query'] = array(
		array(
			'taxonomy' => 'music_genre',
			'field'    => 'slug',
			'terms'    => $cf_album_genre_filter,
		),
	);
}
$cf_albums_query = new WP_Query( $cf_albums_query_args );

$cf_album_posts = $cf_albums_query->posts;
$cf_album_ids   = wp_list_pluck( $cf_album_posts, 'ID' );

$cf_tracks_by_album = array();
if ( ! empty( $cf_album_ids ) ) {
	$cf_tracks_batch = get_posts(
		array(
			'post_type'      => 'tracks',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'associated_album',
					'value'   => $cf_album_ids,
					'compare' => 'IN',
				),
			),
		)
	);
	foreach ( $cf_tracks_batch as $cf_track_post ) {
		$cf_assoc_album = (int) get_post_meta( $cf_track_post->ID, 'associated_album', true );
		if ( $cf_assoc_album > 0 ) {
			if ( ! isset( $cf_tracks_by_album[ $cf_assoc_album ] ) ) {
				$cf_tracks_by_album[ $cf_assoc_album ] = array();
			}
			$cf_tracks_by_album[ $cf_assoc_album ][] = (int) $cf_track_post->ID;
		}
	}
}

$cf_featured_album_id = 0;
$cf_featured_query    = new WP_Query(
	array(
		'post_type'              => 'albums',
		'posts_per_page'         => 1,
		'post_status'            => 'publish',
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
	)
);
if ( $cf_featured_query->have_posts() ) {
	$cf_featured_query->the_post();
	$cf_featured_album_id = get_the_ID();
	wp_reset_postdata();
}

$cf_featured_data = null;
if ( $cf_featured_album_id ) {
	$cf_feat_track_ids = isset( $cf_tracks_by_album[ $cf_featured_album_id ] ) ? $cf_tracks_by_album[ $cf_featured_album_id ] : array();
	if ( empty( $cf_feat_track_ids ) ) {
		$cf_feat_track_ids = get_posts(
			array(
				'post_type'      => 'tracks',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				),
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'     => 'associated_album',
						'value'   => $cf_featured_album_id,
						'compare' => '=',
					),
				),
			)
		);
		$cf_feat_track_ids = array_map( 'intval', $cf_feat_track_ids );
	}
	$cf_feat_genre_terms = get_the_terms( $cf_featured_album_id, 'music_genre' );
	$cf_feat_genre_names = ( $cf_feat_genre_terms && ! is_wp_error( $cf_feat_genre_terms ) ) ? wp_list_pluck( $cf_feat_genre_terms, 'name' ) : array();
	$cf_feat_seconds     = $cf_album_duration_seconds( $cf_feat_track_ids );
	$cf_feat_excerpt     = get_the_excerpt( $cf_featured_album_id );

	$cf_featured_data = array(
		'id'          => $cf_featured_album_id,
		'title'       => get_the_title( $cf_featured_album_id ),
		'permalink'   => get_permalink( $cf_featured_album_id ),
		'cover'       => $cf_resolve_album_cover( $cf_featured_album_id, $cf_feat_track_ids ),
		'cover_large' => get_the_post_thumbnail_url( $cf_featured_album_id, 'large' ) ?: $cf_resolve_album_cover( $cf_featured_album_id, $cf_feat_track_ids ),
		'track_count' => count( $cf_feat_track_ids ),
		'track_ids'   => $cf_feat_track_ids,
		'genres'      => $cf_feat_genre_names,
		'excerpt'     => $cf_feat_excerpt ? wp_strip_all_tags( $cf_feat_excerpt ) : '',
		'duration'    => $cf_feat_seconds > 0 ? $cf_format_track_time( $cf_feat_seconds ) : '',
		'listen'      => $cf_build_album_listen_meta( $cf_featured_album_id, $cf_feat_track_ids ),
	);
}

$cf_exclude_featured_from_grid = $cf_featured_album_id && count( $cf_album_posts ) > 1;
?>

<div id="primary" class="content-area cf-albums-page">
	<main id="main" class="site-main">

		<section class="cf-albums-hero" aria-labelledby="cf-albums-hero-heading">
			<div class="cf-albums-hero__wave" aria-hidden="true"></div>
			<div class="cf-albums-hero__glow" aria-hidden="true"></div>
			<div class="cf-albums-hero__content">
				<span class="cf-albums-hero__badge"><?php esc_html_e( 'Collective Finity', 'collective-finity' ); ?></span>
				<h1 id="cf-albums-hero-heading" class="cf-albums-hero__title">
					<?php esc_html_e( 'Albums & Collections', 'collective-finity' ); ?>
				</h1>
				<p class="cf-albums-hero__lead">
					<?php esc_html_e( 'Explore our cinematic music collections, each telling a unique story through sound.', 'collective-finity' ); ?>
				</p>

				<dl class="cf-albums-hero__stats">
					<div class="cf-albums-hero__stat">
						<dt class="cf-albums-hero__stat-value"><?php echo esc_html( number_format_i18n( $cf_total_albums ) ); ?></dt>
						<dd class="cf-albums-hero__stat-label"><?php echo esc_html( _n( 'Album', 'Albums', $cf_total_albums, 'collective-finity' ) ); ?></dd>
					</div>
					<div class="cf-albums-hero__stat">
						<dt class="cf-albums-hero__stat-value"><?php echo esc_html( number_format_i18n( $cf_total_tracks ) ); ?></dt>
						<dd class="cf-albums-hero__stat-label"><?php echo esc_html( _n( 'Track', 'Tracks', $cf_total_tracks, 'collective-finity' ) ); ?></dd>
					</div>
					<div class="cf-albums-hero__stat">
						<dt class="cf-albums-hero__stat-value"><?php echo esc_html( number_format_i18n( $cf_total_genres ) ); ?></dt>
						<dd class="cf-albums-hero__stat-label"><?php echo esc_html( _n( 'Genre', 'Genres', $cf_total_genres, 'collective-finity' ) ); ?></dd>
					</div>
				</dl>

				<div class="cf-albums-search">
					<span class="dashicons dashicons-search cf-albums-search__icon" aria-hidden="true"></span>
					<label class="screen-reader-text" for="cf-albums-search-input"><?php esc_html_e( 'Search albums', 'collective-finity' ); ?></label>
					<input
						type="search"
						id="cf-albums-search-input"
						class="cf-albums-search__input"
						placeholder="<?php esc_attr_e( 'Search albums…', 'collective-finity' ); ?>"
						data-cf-albums-search
						autocomplete="off"
					>
				</div>
			</div>
		</section>

		<?php if ( $cf_featured_data ) : ?>
			<section class="cf-albums-featured" aria-labelledby="cf-albums-featured-heading">
				<div class="cf-albums-featured__panel">
					<p class="cf-albums-featured__eyebrow">
						<span class="dashicons dashicons-star-filled" aria-hidden="true"></span>
						<?php esc_html_e( 'Featured Collection', 'collective-finity' ); ?>
					</p>
					<div class="cf-albums-featured__grid">
						<div class="cf-albums-featured__art">
							<a href="<?php echo esc_url( $cf_featured_data['permalink'] ); ?>">
								<img
									src="<?php echo esc_url( $cf_featured_data['cover_large'] ); ?>"
									alt="<?php echo esc_attr( $cf_featured_data['title'] ); ?>"
									width="640"
									height="640"
									loading="eager"
									decoding="async"
								>
							</a>
						</div>

						<div class="cf-albums-featured__main">
							<?php if ( ! empty( $cf_featured_data['genres'] ) ) : ?>
								<p class="cf-albums-featured__genres">
									<?php echo esc_html( strtoupper( implode( ' • ', array_slice( $cf_featured_data['genres'], 0, 2 ) ) ) ); ?>
								</p>
							<?php endif; ?>

							<h2 id="cf-albums-featured-heading" class="cf-albums-featured__title">
								<?php echo esc_html( $cf_featured_data['title'] ); ?>
							</h2>

							<ul class="cf-albums-featured__meta">
								<?php if ( $cf_featured_data['track_count'] > 0 ) : ?>
									<li>
										<span class="dashicons dashicons-playlist-audio" aria-hidden="true"></span>
										<?php
										echo esc_html(
											sprintf(
												_n( '%d Track', '%d Tracks', $cf_featured_data['track_count'], 'collective-finity' ),
												$cf_featured_data['track_count']
											)
										);
										?>
									</li>
								<?php endif; ?>
								<?php if ( $cf_featured_data['duration'] ) : ?>
									<li>
										<span class="dashicons dashicons-clock" aria-hidden="true"></span>
										<?php echo esc_html( $cf_featured_data['duration'] ); ?>
									</li>
								<?php endif; ?>
							</ul>

							<?php if ( $cf_featured_data['excerpt'] ) : ?>
								<p class="cf-albums-featured__desc"><?php echo esc_html( $cf_featured_data['excerpt'] ); ?></p>
							<?php endif; ?>

							<div class="cf-albums-featured__actions">
								<?php if ( ! empty( $cf_featured_data['listen']['audio'] ) ) : ?>
									<button
										type="button"
										class="cf-btn-primary-lg cf-albums-featured__listen"
										data-audio="<?php echo esc_url( $cf_featured_data['listen']['audio'] ); ?>"
										data-title="<?php echo esc_attr( $cf_featured_data['listen']['title'] ); ?>"
										data-artist="<?php echo esc_attr( $cf_featured_data['listen']['artist'] ); ?>"
										data-cover="<?php echo esc_url( $cf_featured_data['listen']['cover'] ); ?>"
									><?php esc_html_e( 'Listen Now', 'collective-finity' ); ?></button>
								<?php else : ?>
									<a class="cf-btn-primary-lg" href="<?php echo esc_url( $cf_featured_data['permalink'] ); ?>"><?php esc_html_e( 'Listen Now', 'collective-finity' ); ?></a>
								<?php endif; ?>
								<a class="cf-btn-ghost-lg" href="<?php echo esc_url( $cf_featured_data['permalink'] ); ?>"><?php esc_html_e( 'View Album', 'collective-finity' ); ?></a>
							</div>
						</div>

						<?php if ( ! empty( $cf_featured_data['track_ids'] ) ) : ?>
							<ol class="cf-albums-featured__tracks">
								<?php
								$cf_feat_preview_tracks = array_slice( $cf_featured_data['track_ids'], 0, 6 );
								foreach ( $cf_feat_preview_tracks as $cf_feat_track_index => $cf_feat_track_id ) :
									$cf_feat_track_title = get_the_title( $cf_feat_track_id );
									$cf_feat_track_secs  = $cf_resolve_track_duration_seconds( $cf_feat_track_id );
									$cf_feat_track_dur     = $cf_feat_track_secs > 0 ? $cf_format_track_time( $cf_feat_track_secs ) : '';
									?>
									<li>
										<span class="cf-albums-featured__track-num"><?php echo esc_html( (string) ( $cf_feat_track_index + 1 ) ); ?></span>
										<a class="cf-albums-featured__track-title" href="<?php echo esc_url( get_permalink( $cf_feat_track_id ) ); ?>">
											<?php echo esc_html( $cf_feat_track_title ); ?>
										</a>
										<?php if ( $cf_feat_track_dur ) : ?>
											<span class="cf-albums-featured__track-dur"><?php echo esc_html( $cf_feat_track_dur ); ?></span>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ol>
							<?php if ( $cf_featured_data['track_count'] > 6 ) : ?>
								<a class="cf-albums-featured__view-all" href="<?php echo esc_url( $cf_featured_data['permalink'] ); ?>">
									<?php esc_html_e( 'View full album', 'collective-finity' ); ?>
									<span aria-hidden="true">→</span>
								</a>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<div class="albums-grid-container">

			<?php if ( ! empty( $cf_album_genres ) ) : ?>
				<div class="cf-albums-genres">
					<p class="cf-albums-genres__label"><?php esc_html_e( 'Browse by Genre', 'collective-finity' ); ?></p>
					<div class="cf-filter-row-wrap" data-cf-filter-carousel>
						<button type="button" class="cf-filter-nav-btn cf-filter-nav-prev" aria-label="<?php esc_attr_e( 'Previous genres', 'collective-finity' ); ?>" hidden>
							<span class="dashicons dashicons-arrow-left-alt2"></span>
						</button>
						<div class="cf-filter-row">
							<a href="<?php echo esc_url( remove_query_arg( 'genre' ) ); ?>" class="cf-filter-pill<?php echo '' === $cf_album_genre_filter ? ' active' : ''; ?>">
								<?php
								printf(
									/* translators: %d: album count */
									esc_html__( 'All (%d)', 'collective-finity' ),
									(int) $cf_total_albums
								);
								?>
							</a>
							<?php foreach ( $cf_album_genres as $cf_ag_term ) : ?>
								<a href="<?php echo esc_url( add_query_arg( 'genre', $cf_ag_term->slug ) ); ?>" class="cf-filter-pill<?php echo $cf_album_genre_filter === $cf_ag_term->slug ? ' active' : ''; ?>">
									<?php echo esc_html( $cf_ag_term->name ); ?>
									<span class="cf-filter-pill__count">(<?php echo esc_html( (string) (int) $cf_ag_term->count ); ?>)</span>
								</a>
							<?php endforeach; ?>
						</div>
						<button type="button" class="cf-filter-nav-btn cf-filter-nav-next" aria-label="<?php esc_attr_e( 'Next genres', 'collective-finity' ); ?>" hidden>
							<span class="dashicons dashicons-arrow-right-alt2"></span>
						</button>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $cf_albums_query->have_posts() ) : ?>
				<div class="cf-albums-toolbar">
					<h2 class="cf-albums-toolbar__heading"><?php esc_html_e( 'Latest Albums', 'collective-finity' ); ?></h2>
					<div class="cf-albums-toolbar__controls">
						<label class="cf-albums-sort">
							<span class="cf-albums-sort__label"><?php esc_html_e( 'Sort by:', 'collective-finity' ); ?></span>
							<select class="cf-albums-sort__select" data-cf-albums-sort aria-label="<?php esc_attr_e( 'Sort albums', 'collective-finity' ); ?>">
								<option value="newest"><?php esc_html_e( 'Newest', 'collective-finity' ); ?></option>
								<option value="oldest"><?php esc_html_e( 'Oldest', 'collective-finity' ); ?></option>
								<option value="alpha"><?php esc_html_e( 'Alphabetical', 'collective-finity' ); ?></option>
							</select>
						</label>
						<div class="cf-albums-view-switch" role="group" aria-label="<?php esc_attr_e( 'View mode', 'collective-finity' ); ?>">
							<span class="cf-albums-view-switch__label"><?php esc_html_e( 'View:', 'collective-finity' ); ?></span>
							<button type="button" class="cf-albums-view-btn" data-cf-view-mode="grid" aria-pressed="true" title="<?php esc_attr_e( 'Grid view', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Grid view', 'collective-finity' ); ?>">
								<span class="dashicons dashicons-grid-view" aria-hidden="true"></span>
							</button>
							<button type="button" class="cf-albums-view-btn" data-cf-view-mode="list" aria-pressed="false" title="<?php esc_attr_e( 'List view', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'List view', 'collective-finity' ); ?>">
								<span class="dashicons dashicons-list-view" aria-hidden="true"></span>
							</button>
						</div>
					</div>
				</div>

				<div class="cf-albums-grid cf-card-grid" data-cf-view="grid" data-cf-albums-grid>
					<?php
					while ( $cf_albums_query->have_posts() ) :
						$cf_albums_query->the_post();
						$album_id        = get_the_ID();
						if ( $cf_exclude_featured_from_grid && $album_id === $cf_featured_album_id ) {
							continue;
						}

						$album_permalink = get_permalink( $album_id );
						$album_title     = get_the_title( $album_id );
						$cf_track_ids    = isset( $cf_tracks_by_album[ $album_id ] ) ? $cf_tracks_by_album[ $album_id ] : array();
						$cf_track_count  = count( $cf_track_ids );
						$cover_url       = $cf_resolve_album_cover( $album_id, $cf_track_ids );
						$listen_meta     = $cf_build_album_listen_meta( $album_id, $cf_track_ids );

						$cf_album_genre_terms = get_the_terms( $album_id, 'music_genre' );
						$cf_album_genre_name  = ( $cf_album_genre_terms && ! is_wp_error( $cf_album_genre_terms ) ) ? $cf_album_genre_terms[0]->name : '';

						$cf_sort_date  = get_post_time( 'U', true, $album_id );
						$cf_sort_title = mb_strtolower( $album_title );
						?>
						<article
							class="cf-albums-card cf-card"
							data-cf-search-title="<?php echo esc_attr( $cf_sort_title ); ?>"
							data-cf-sort-date="<?php echo esc_attr( (string) $cf_sort_date ); ?>"
							data-cf-sort-title="<?php echo esc_attr( $cf_sort_title ); ?>"
						>
							<div class="cf-albums-card__cover-wrap">
								<a href="<?php echo esc_url( $album_permalink ); ?>" class="cf-albums-card__cover-link" tabindex="-1" aria-hidden="true">
									<div class="cf-cover">
										<img src="<?php echo esc_url( $cover_url ); ?>" alt="<?php echo esc_attr( $album_title ); ?>" loading="lazy">
										<span class="cf-albums-card__overlay" aria-hidden="true"></span>
									</div>
								</a>
								<?php if ( ! empty( $listen_meta['audio'] ) ) : ?>
									<button
										type="button"
										class="cf-play-btn"
										aria-label="<?php echo esc_attr( sprintf( __( 'Play %s', 'collective-finity' ), $album_title ) ); ?>"
										data-audio="<?php echo esc_url( $listen_meta['audio'] ); ?>"
										data-title="<?php echo esc_attr( $listen_meta['title'] ); ?>"
										data-artist="<?php echo esc_attr( $listen_meta['artist'] ); ?>"
										data-cover="<?php echo esc_url( $listen_meta['cover'] ); ?>"
									>
										<span class="dashicons dashicons-controls-play" aria-hidden="true"></span>
									</button>
								<?php endif; ?>
							</div>
							<a href="<?php echo esc_url( $album_permalink ); ?>" class="cf-card-title cf-albums-card__title"><?php echo esc_html( $album_title ); ?></a>
							<div class="cf-card-sub cf-albums-card__sub"><?php echo esc_html( sprintf( _n( '%d track', '%d tracks', $cf_track_count, 'collective-finity' ), $cf_track_count ) ); ?></div>
							<?php if ( $cf_album_genre_name ) : ?>
								<span class="cf-card-chip cf-albums-card__chip"><?php echo esc_html( $cf_album_genre_name ); ?></span>
							<?php endif; ?>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>

				<div class="cf-albums-list cf-library-list" data-cf-view="list" data-cf-albums-list hidden>
					<?php
					$cf_albums_query->rewind_posts();
					while ( $cf_albums_query->have_posts() ) :
						$cf_albums_query->the_post();
						$album_id = get_the_ID();
						if ( $cf_exclude_featured_from_grid && $album_id === $cf_featured_album_id ) {
							continue;
						}

						$album_title    = get_the_title( $album_id );
						$cf_track_ids   = isset( $cf_tracks_by_album[ $album_id ] ) ? $cf_tracks_by_album[ $album_id ] : array();
						$cf_track_count = count( $cf_track_ids );
						$cover_url      = $cf_resolve_album_cover( $album_id, $cf_track_ids );
						$cf_seconds     = $cf_album_duration_seconds( $cf_track_ids );
						$duration_label = $cf_seconds > 0
							? sprintf(
								/* translators: %d: album duration in minutes */
								__( '%d min', 'collective-finity' ),
								max( 1, (int) round( $cf_seconds / 60 ) )
							)
							: '';

						$cf_album_genre_terms = get_the_terms( $album_id, 'music_genre' );
						$cf_album_genre_name  = ( $cf_album_genre_terms && ! is_wp_error( $cf_album_genre_terms ) ) ? $cf_album_genre_terms[0]->name : '';
						$cf_sort_date         = get_post_time( 'U', true, $album_id );
						$cf_sort_title        = mb_strtolower( $album_title );
						?>
						<a
							class="cf-library-list-row cf-library-list-row--album cf-albums-list-row"
							href="<?php echo esc_url( get_permalink( $album_id ) ); ?>"
							data-cf-search-title="<?php echo esc_attr( $cf_sort_title ); ?>"
							data-cf-sort-date="<?php echo esc_attr( (string) $cf_sort_date ); ?>"
							data-cf-sort-title="<?php echo esc_attr( $cf_sort_title ); ?>"
						>
							<span class="cf-library-list-row__thumb">
								<img src="<?php echo esc_url( $cover_url ); ?>" alt="" width="48" height="48" loading="lazy">
							</span>
							<span class="cf-library-list-row__main">
								<span class="cf-library-list-row__title"><?php echo esc_html( $album_title ); ?></span>
								<span class="cf-card-chip cf-card-chip--type"><?php esc_html_e( 'Album', 'collective-finity' ); ?></span>
							</span>
							<span class="cf-library-list-row__artist"><?php echo esc_html( sprintf( _n( '%d track', '%d tracks', $cf_track_count, 'collective-finity' ), $cf_track_count ) ); ?></span>
							<span class="cf-library-list-row__duration"><?php echo $duration_label ? esc_html( $duration_label ) : '&mdash;'; ?></span>
							<span class="cf-library-list-row__genre"><?php echo $cf_album_genre_name ? esc_html( $cf_album_genre_name ) : '&mdash;'; ?></span>
							<span class="cf-library-list-row__date"><?php echo esc_html( get_the_date( get_option( 'date_format' ), $album_id ) ); ?></span>
						</a>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>

				<p class="cf-albums-empty-search" data-cf-albums-empty-search hidden><?php esc_html_e( 'No albums match your search.', 'collective-finity' ); ?></p>
			<?php else : ?>
				<div class="albums-empty-state">
					<span aria-hidden="true">🎵</span>
					<h2><?php esc_html_e( 'No Albums Yet', 'collective-finity' ); ?></h2>
					<p><?php esc_html_e( 'Check back soon for new music collections.', 'collective-finity' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</main>
</div>

<?php get_footer(); ?>
