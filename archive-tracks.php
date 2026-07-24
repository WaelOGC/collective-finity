<?php
/**
 * Template Name: Tracks Archive (Music Library)
 * Description: Music Library hub with carousels, filter tabs, grid/list views, and search.
 */

get_header();

$cf_tracks_view = function_exists( 'collective_finity_get_tracks_archive_view' )
	? collective_finity_get_tracks_archive_view()
	: '';

$cf_popular_min = function_exists( 'collective_finity_popular_min_views' )
	? collective_finity_popular_min_views()
	: 50;

$cf_header_title = __( 'Music Library', 'collective-finity' );
$cf_header_copy  = __( 'Discover premium cinematic tracks, each crafted with emotion and innovation.', 'collective-finity' );

if ( 'all' === $cf_tracks_view ) {
	$cf_header_title = __( 'All Tracks', 'collective-finity' );
	$cf_header_copy  = __( 'Browse every published track in the library.', 'collective-finity' );
} elseif ( 'albums' === $cf_tracks_view ) {
	$cf_header_title = __( 'Albums', 'collective-finity' );
	$cf_header_copy  = __( 'Browse every published album in the library.', 'collective-finity' );
} elseif ( 'popular' === $cf_tracks_view ) {
	$cf_header_title = __( 'Popular Tracks', 'collective-finity' );
	$cf_header_copy  = __( 'Tracks that have reached the popular view threshold.', 'collective-finity' );
} elseif ( 'playlists' === $cf_tracks_view ) {
	$cf_header_title = __( 'Playlists', 'collective-finity' );
	$cf_header_copy  = __( 'Community playlists are on the way.', 'collective-finity' );
}

$cf_show_artists_tab   = (bool) collective_finity_get_theme_option( 'library_show_artists_tab', 1 );
$cf_show_playlists_tab = (bool) collective_finity_get_theme_option( 'library_show_playlists_tab', 1 );

$cf_tracks_base_url = function_exists( 'collective_finity_get_tracks_archive_base_url' )
	? collective_finity_get_tracks_archive_base_url()
	: trailingslashit( get_post_type_archive_link( 'tracks' ) ?: home_url( '/tracks/' ) );

$cf_url_all = function_exists( 'collective_finity_get_tracks_all_url' )
	? collective_finity_get_tracks_all_url()
	: $cf_tracks_base_url . 'all/';
$cf_url_albums = function_exists( 'collective_finity_get_tracks_albums_url' )
	? collective_finity_get_tracks_albums_url()
	: $cf_tracks_base_url . 'albums/';
$cf_url_popular = function_exists( 'collective_finity_get_tracks_popular_url' )
	? collective_finity_get_tracks_popular_url()
	: $cf_tracks_base_url . 'popular/';
$cf_url_playlists = function_exists( 'collective_finity_get_tracks_playlists_url' )
	? collective_finity_get_tracks_playlists_url()
	: $cf_tracks_base_url . 'playlists/';

// Artists: no taxonomy index page — fall back to most-used artist term archive.
$cf_artists_url = '';
$cf_artist_terms = get_terms(
	array(
		'taxonomy'   => 'track_artist',
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 1,
		'hide_empty' => true,
	)
);
if ( ! is_wp_error( $cf_artist_terms ) && ! empty( $cf_artist_terms ) ) {
	$cf_artist_link = get_term_link( $cf_artist_terms[0] );
	if ( ! is_wp_error( $cf_artist_link ) ) {
		$cf_artists_url = $cf_artist_link;
	}
}
if ( ! $cf_artists_url ) {
	$cf_artists_url = home_url( '/artist/' );
}

/**
 * Album / Single label for a track.
 *
 * @param int $track_id Track post ID.
 * @return string
 */
$cf_track_type_label = static function ( $track_id ) {
	$release_type = get_post_meta( $track_id, 'track_release_type', true );
	$associated   = get_post_meta( $track_id, 'associated_album', true );
	if ( ! empty( $associated ) || ( $release_type && 'single' !== $release_type ) ) {
		return __( 'Album', 'collective-finity' );
	}
	return __( 'Single', 'collective-finity' );
};

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
 * Resolve display duration for a track (meta first, then attachment metadata).
 *
 * @param int $track_id Track post ID.
 * @return string
 */
$cf_resolve_track_duration = static function ( $track_id ) use ( $cf_format_track_time, $cf_audio_duration_seconds ) {
	$meta_duration = get_post_meta( $track_id, 'track_duration', true );
	if ( is_string( $meta_duration ) && preg_match( '/^\d{1,2}:\d{2}$/', trim( $meta_duration ) ) ) {
		return trim( $meta_duration );
	}
	if ( is_numeric( $meta_duration ) && (int) $meta_duration > 0 ) {
		return $cf_format_track_time( (int) $meta_duration );
	}

	$track_audio   = get_post_meta( $track_id, 'track_audio_url', true );
	$track_preview = get_post_meta( $track_id, 'track_preview_url', true );
	$audio_url     = ! empty( $track_preview ) ? $track_preview : $track_audio;
	$seconds       = $cf_audio_duration_seconds( $audio_url );
	return $seconds > 0 ? $cf_format_track_time( $seconds ) : '—:—';
};

/**
 * Render a track card (like + play overlay preserved).
 *
 * @param int $track_id Track post ID.
 */
$cf_render_track_card = static function ( $track_id ) use ( $cf_track_type_label ) {
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
	$artists       = wp_get_post_terms( $track_id, 'track_artist' );
	if ( is_wp_error( $artists ) ) {
		$artists = array();
	}
	$artist_names = ! empty( $artists ) ? wp_list_pluck( $artists, 'name' ) : array();
	$artist_name  = ! empty( $artist_names ) ? implode( ', ', $artist_names ) : 'Collective Finity';
	$genres       = wp_get_post_terms( $track_id, 'music_genre', array( 'fields' => 'names' ) );
	$genre_name   = ! empty( $genres ) ? $genres[0] : '';
	$title        = get_the_title( $track_id );
	$permalink    = get_permalink( $track_id );
	$type_label   = $cf_track_type_label( $track_id );
	?>
	<div class="cf-card" data-cf-search-title="<?php echo esc_attr( mb_strtolower( $title ) ); ?>">
		<a href="<?php echo esc_url( $permalink ); ?>" class="cf-card-primary">
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
		</a>
		<div class="cf-card-sub">
			<?php
			if ( ! empty( $artists ) ) {
				$cf_artist_link_parts = array();
				foreach ( $artists as $cf_artist_term ) {
					$cf_term_url = get_term_link( $cf_artist_term );
					if ( ! is_wp_error( $cf_term_url ) ) {
						$cf_artist_link_parts[] = sprintf(
							'<a class="cf-artist-link" href="%1$s">%2$s</a>',
							esc_url( $cf_term_url ),
							esc_html( $cf_artist_term->name )
						);
					} else {
						$cf_artist_link_parts[] = esc_html( $cf_artist_term->name );
					}
				}
				echo implode( ', ', $cf_artist_link_parts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pieces escaped above.
			} else {
				echo esc_html( $artist_name );
			}
			?>
		</div>
		<div class="cf-card-chips">
			<span class="cf-card-chip cf-card-chip--type"><?php echo esc_html( $type_label ); ?></span>
			<?php if ( $genre_name ) : ?><span class="cf-card-chip"><?php echo esc_html( $genre_name ); ?></span><?php endif; ?>
		</div>
	</div>
	<?php
};

/**
 * Render an album card for the Albums shelf / Albums tab.
 *
 * @param int $album_id Album post ID.
 */
$cf_render_album_card = static function ( $album_id ) {
	$link  = get_permalink( $album_id );
	$cover = get_the_post_thumbnail_url( $album_id, 'medium' );
	if ( empty( $cover ) ) {
		$cover = collective_finity_default_art_url();
	}
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
	$title = get_the_title( $album_id );
	?>
	<a href="<?php echo esc_url( $link ); ?>" class="cf-card" data-cf-search-title="<?php echo esc_attr( mb_strtolower( $title ) ); ?>">
		<div class="cf-cover"><img src="<?php echo esc_url( $cover ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy"></div>
		<div class="cf-card-title"><?php echo esc_html( $title ); ?></div>
		<div class="cf-card-sub"><?php echo esc_html( sprintf( _n( 'Album · %d track', 'Album · %d tracks', $count, 'collective-finity' ), $count ) ); ?></div>
	</a>
	<?php
};

/**
 * Render a track list-view row.
 *
 * @param int $track_id Track post ID.
 */
$cf_render_track_list_row = static function ( $track_id ) use ( $cf_track_type_label, $cf_resolve_track_duration ) {
	$cover_image = get_post_meta( $track_id, 'track_cover_url', true );
	if ( ! $cover_image ) {
		$cover_image = get_the_post_thumbnail_url( $track_id, 'thumbnail' );
	}
	if ( ! $cover_image ) {
		$cover_image = collective_finity_default_art_url();
	}

	$track_audio   = get_post_meta( $track_id, 'track_audio_url', true );
	$track_preview = get_post_meta( $track_id, 'track_preview_url', true );
	$audio_url     = ! empty( $track_preview ) ? $track_preview : $track_audio;
	$artists       = wp_get_post_terms( $track_id, 'track_artist' );
	if ( is_wp_error( $artists ) ) {
		$artists = array();
	}
	$artist_names = ! empty( $artists ) ? wp_list_pluck( $artists, 'name' ) : array();
	$artist_name  = ! empty( $artist_names ) ? implode( ', ', $artist_names ) : 'Collective Finity';
	$genres       = wp_get_post_terms( $track_id, 'music_genre', array( 'fields' => 'names' ) );
	$genre_name   = ! empty( $genres ) ? $genres[0] : '';
	$title        = get_the_title( $track_id );
	$permalink    = get_permalink( $track_id );
	$type_label   = $cf_track_type_label( $track_id );
	$duration     = $cf_resolve_track_duration( $track_id );
	$release_date = get_the_date( get_option( 'date_format' ), $track_id );
	?>
	<div class="cf-library-list-row" data-cf-search-title="<?php echo esc_attr( mb_strtolower( $title ) ); ?>">
		<button
			type="button"
			class="cf-library-list-row__play"
			aria-label="<?php echo esc_attr( sprintf( __( 'Play %s', 'collective-finity' ), $title ) ); ?>"
			onclick="if (window.playTrack) { window.playTrack('<?php echo esc_js( $audio_url ); ?>', '<?php echo esc_js( $title ); ?>', '<?php echo esc_js( $artist_name ); ?>', '<?php echo esc_js( $cover_image ); ?>'); }"
		>
			<span class="dashicons dashicons-controls-play" aria-hidden="true"></span>
		</button>
		<a class="cf-library-list-row__thumb" href="<?php echo esc_url( $permalink ); ?>">
			<img src="<?php echo esc_url( $cover_image ); ?>" alt="" width="48" height="48" loading="lazy">
		</a>
		<div class="cf-library-list-row__main">
			<a class="cf-library-list-row__title" href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
			<span class="cf-card-chip cf-card-chip--type"><?php echo esc_html( $type_label ); ?></span>
		</div>
		<div class="cf-library-list-row__artist">
			<?php
			if ( ! empty( $artists ) ) {
				$cf_artist_link_parts = array();
				foreach ( $artists as $cf_artist_term ) {
					$cf_term_url = get_term_link( $cf_artist_term );
					if ( ! is_wp_error( $cf_term_url ) ) {
						$cf_artist_link_parts[] = sprintf(
							'<a class="cf-artist-link" href="%1$s">%2$s</a>',
							esc_url( $cf_term_url ),
							esc_html( $cf_artist_term->name )
						);
					} else {
						$cf_artist_link_parts[] = esc_html( $cf_artist_term->name );
					}
				}
				echo implode( ', ', $cf_artist_link_parts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pieces escaped above.
			} else {
				echo esc_html( $artist_name );
			}
			?>
		</div>
		<div class="cf-library-list-row__duration"><?php echo esc_html( $duration ); ?></div>
		<div class="cf-library-list-row__genre"><?php echo $genre_name ? esc_html( $genre_name ) : '&mdash;'; ?></div>
		<div class="cf-library-list-row__date"><?php echo esc_html( $release_date ); ?></div>
		<button type="button" class="cf-library-list-row__icon cf-interaction-btn cf-like-btn cf-heart-btn" data-track-id="<?php echo esc_attr( (string) $track_id ); ?>" title="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>">
			<span class="dashicons dashicons-heart" aria-hidden="true"></span>
		</button>
	</div>
	<?php
};

/**
 * Render an album list-view row.
 *
 * @param int $album_id Album post ID.
 */
$cf_render_album_list_row = static function ( $album_id ) {
	$link  = get_permalink( $album_id );
	$cover = get_the_post_thumbnail_url( $album_id, 'thumbnail' );
	if ( empty( $cover ) ) {
		$cover = collective_finity_default_art_url();
	}
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
	$count        = (int) $count_q->found_posts;
	$title        = get_the_title( $album_id );
	$release_date = get_the_date( get_option( 'date_format' ), $album_id );
	$genres       = wp_get_post_terms( $album_id, 'music_genre', array( 'fields' => 'names' ) );
	$genre_name   = ( ! is_wp_error( $genres ) && ! empty( $genres ) ) ? $genres[0] : '';
	?>
	<a class="cf-library-list-row cf-library-list-row--album" href="<?php echo esc_url( $link ); ?>" data-cf-search-title="<?php echo esc_attr( mb_strtolower( $title ) ); ?>">
		<span class="cf-library-list-row__thumb">
			<img src="<?php echo esc_url( $cover ); ?>" alt="" width="48" height="48" loading="lazy">
		</span>
		<span class="cf-library-list-row__main">
			<span class="cf-library-list-row__title"><?php echo esc_html( $title ); ?></span>
			<span class="cf-card-chip cf-card-chip--type"><?php esc_html_e( 'Album', 'collective-finity' ); ?></span>
		</span>
		<span class="cf-library-list-row__artist"><?php echo esc_html( sprintf( _n( '%d track', '%d tracks', $count, 'collective-finity' ), $count ) ); ?></span>
		<span class="cf-library-list-row__duration">&mdash;</span>
		<span class="cf-library-list-row__genre"><?php echo $genre_name ? esc_html( $genre_name ) : '&mdash;'; ?></span>
		<span class="cf-library-list-row__date"><?php echo esc_html( $release_date ); ?></span>
	</a>
	<?php
};

/**
 * Open a carousel section shell.
 *
 * @param string $title Section title.
 * @param string $show_all_url Show all link URL.
 */
$cf_carousel_open = static function ( $title, $show_all_url ) {
	?>
	<section class="cf-carousel-section">
		<div class="cf-section-label">
			<span><?php echo esc_html( $title ); ?></span>
			<a href="<?php echo esc_url( $show_all_url ); ?>"><?php esc_html_e( 'Show all', 'collective-finity' ); ?></a>
		</div>
		<div class="cf-carousel" data-cf-carousel>
			<button type="button" class="cf-carousel-btn cf-carousel-prev" aria-label="<?php esc_attr_e( 'Previous', 'collective-finity' ); ?>" hidden>
				<span class="dashicons dashicons-arrow-left-alt2"></span>
			</button>
			<div class="cf-carousel-viewport">
				<div class="cf-carousel-track">
	<?php
};

/**
 * Close a carousel section shell.
 */
$cf_carousel_close = static function () {
	?>
				</div>
			</div>
			<button type="button" class="cf-carousel-btn cf-carousel-next" aria-label="<?php esc_attr_e( 'Next', 'collective-finity' ); ?>" hidden>
				<span class="dashicons dashicons-arrow-right-alt2"></span>
			</button>
		</div>
	</section>
	<?php
};

/*
 * Tab definitions — add a future tab by appending one entry + whitelist value + WP_Query branch.
 * type: 'view' = in-page filter via cf_tracks_view; 'link' = navigates away.
 */
$cf_library_tabs = array(
	array(
		'id'     => 'all',
		'label'  => __( 'All Tracks', 'collective-finity' ),
		'url'    => $cf_url_all,
		'active' => ( 'all' === $cf_tracks_view ),
		'type'   => 'view',
	),
	array(
		'id'     => 'albums',
		'label'  => __( 'Albums', 'collective-finity' ),
		'url'    => $cf_url_albums,
		'active' => ( 'albums' === $cf_tracks_view ),
		'type'   => 'view',
	),
);

if ( $cf_show_artists_tab ) {
	$cf_library_tabs[] = array(
		'id'     => 'artists',
		'label'  => __( 'Artists', 'collective-finity' ),
		'url'    => $cf_artists_url,
		'active' => false,
		'type'   => 'link',
	);
}

if ( $cf_show_playlists_tab ) {
	$cf_library_tabs[] = array(
		'id'     => 'playlists',
		'label'  => __( 'Playlists', 'collective-finity' ),
		'url'    => $cf_url_playlists,
		'active' => ( 'playlists' === $cf_tracks_view ),
		'type'   => 'view',
	);
}

$cf_library_tabs[] = array(
	'id'     => 'popular',
	'label'  => __( 'Popular', 'collective-finity' ),
	'url'    => $cf_url_popular,
	'active' => ( 'popular' === $cf_tracks_view ),
	'type'   => 'view',
);
?>

<div id="primary" class="content-area cf-library-page">
	<main id="main" class="site-main">
		<section class="cf-library-hero" aria-labelledby="cf-library-hero-heading">
			<div class="cf-library-hero__border" aria-hidden="true"></div>
			<div class="cf-library-hero__center-glow" aria-hidden="true"></div>
			<div class="cf-library-hero__content">
				<div class="cf-library-search">
					<span class="dashicons dashicons-search cf-library-search__icon" aria-hidden="true"></span>
					<label class="screen-reader-text" for="cf-library-search-input"><?php esc_html_e( 'Search library', 'collective-finity' ); ?></label>
					<input
						type="search"
						id="cf-library-search-input"
						class="cf-library-search__input"
						placeholder="<?php esc_attr_e( 'Search for tracks, albums, artists...', 'collective-finity' ); ?>"
						data-cf-library-search
						autocomplete="off"
					>
					<kbd class="cf-library-search__hint" aria-hidden="true">⌘K</kbd>
				</div>

				<span class="cf-library-hero__badge"><?php esc_html_e( 'Collective Finity', 'collective-finity' ); ?></span>
				<h1 id="cf-library-hero-heading" class="cf-library-hero__title"><?php echo esc_html( $cf_header_title ); ?></h1>
				<p class="cf-library-hero__lead"><?php echo esc_html( $cf_header_copy ); ?></p>

				<div class="cf-library-hero__controls">
					<nav class="cf-library-tabs" aria-label="<?php esc_attr_e( 'Library filters', 'collective-finity' ); ?>" data-cf-library-tabs>
						<?php foreach ( $cf_library_tabs as $cf_tab ) : ?>
							<a
								href="<?php echo esc_url( $cf_tab['url'] ); ?>"
								class="cf-library-tab<?php echo ! empty( $cf_tab['active'] ) ? ' active' : ''; ?>"
								data-cf-library-tab="<?php echo esc_attr( $cf_tab['id'] ); ?>"
								<?php echo ! empty( $cf_tab['active'] ) ? ' aria-current="page"' : ''; ?>
							><?php echo esc_html( $cf_tab['label'] ); ?></a>
						<?php endforeach; ?>
					</nav>

					<div class="cf-library-view-switch" role="group" aria-label="<?php esc_attr_e( 'View mode', 'collective-finity' ); ?>" data-cf-view-switch>
						<button type="button" class="cf-library-view-btn" data-cf-view-mode="list" aria-pressed="false" title="<?php esc_attr_e( 'List view', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'List view', 'collective-finity' ); ?>">
							<span class="dashicons dashicons-list-view" aria-hidden="true"></span>
						</button>
						<button type="button" class="cf-library-view-btn" data-cf-view-mode="grid" aria-pressed="true" title="<?php esc_attr_e( 'Grid view', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Grid view', 'collective-finity' ); ?>">
							<span class="dashicons dashicons-grid-view" aria-hidden="true"></span>
						</button>
					</div>
				</div>
			</div>
		</section>

		<div class="tracks-grid-container">

			<?php if ( '' === $cf_tracks_view ) : ?>

				<?php
				if ( function_exists( 'collective_finity_ad_slot_wrapped' ) ) {
					collective_finity_ad_slot_wrapped( 'library_top', '<div class="cf-library-ad">', '</div>' );
				}

				// ── 1. Albums ───────────────────────────────────────────
				$cf_albums_q = new WP_Query(
					array(
						'post_type'      => 'albums',
						'posts_per_page' => 24,
						'post_status'    => 'publish',
						'orderby'        => 'date',
						'order'          => 'DESC',
					)
				);
				if ( $cf_albums_q->have_posts() ) :
					$cf_carousel_open( __( 'Albums', 'collective-finity' ), $cf_url_albums );
					while ( $cf_albums_q->have_posts() ) :
						$cf_albums_q->the_post();
						$cf_render_album_card( get_the_ID() );
					endwhile;
					wp_reset_postdata();
					$cf_carousel_close();
				endif;

				// ── 2. Latest Tracks (standalone singles only) ──────────
				$cf_latest_q = new WP_Query(
					array(
						'post_type'      => 'tracks',
						'posts_per_page' => 24,
						'post_status'    => 'publish',
						'orderby'        => 'date',
						'order'          => 'DESC',
						'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
							'relation' => 'OR',
							array(
								'key'     => 'track_release_type',
								'value'   => 'single',
								'compare' => '=',
							),
							array(
								'key'     => 'track_release_type',
								'compare' => 'NOT EXISTS',
							),
						),
					)
				);
				if ( $cf_latest_q->have_posts() ) :
					$cf_carousel_open(
						__( 'Latest Tracks', 'collective-finity' ),
						$cf_url_all
					);
					while ( $cf_latest_q->have_posts() ) :
						$cf_latest_q->the_post();
						$cf_render_track_card( get_the_ID() );
					endwhile;
					wp_reset_postdata();
					$cf_carousel_close();
				endif;

				if ( function_exists( 'collective_finity_ad_slot_wrapped' ) ) {
					collective_finity_ad_slot_wrapped( 'library_between_sections', '<div class="cf-library-ad">', '</div>' );
				}

				// ── 3. Popular ──────────────────────────────────────────
				$cf_popular_q = new WP_Query(
					array(
						'post_type'      => 'tracks',
						'posts_per_page' => 24,
						'post_status'    => 'publish',
						'meta_key'       => '_cf_track_plays', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
						'orderby'        => 'meta_value_num',
						'order'          => 'DESC',
						'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
							array(
								'key'     => '_cf_track_plays',
								'value'   => $cf_popular_min,
								'compare' => '>=',
								'type'    => 'NUMERIC',
							),
						),
					)
				);
				if ( $cf_popular_q->have_posts() ) :
					$cf_carousel_open(
						__( 'Popular', 'collective-finity' ),
						$cf_url_popular
					);
					while ( $cf_popular_q->have_posts() ) :
						$cf_popular_q->the_post();
						$cf_render_track_card( get_the_ID() );
					endwhile;
					wp_reset_postdata();
					$cf_carousel_close();
				endif;
				?>

			<?php elseif ( 'playlists' === $cf_tracks_view ) : ?>

				<div class="tracks-empty-state" data-cf-library-placeholder="playlists">
					<span>🎶</span>
					<h2><?php esc_html_e( 'Playlists Coming Soon', 'collective-finity' ); ?></h2>
					<p><?php esc_html_e( 'Playlists are coming soon — this section will activate once the community playlist feature ships.', 'collective-finity' ); ?></p>
				</div>

			<?php else : ?>

				<?php
				$cf_is_albums_view = ( 'albums' === $cf_tracks_view );

				if ( $cf_is_albums_view ) {
					$cf_list_args = array(
						'post_type'      => 'albums',
						'posts_per_page' => -1,
						'post_status'    => 'publish',
						'orderby'        => 'date',
						'order'          => 'DESC',
					);
				} else {
					$cf_list_args = array(
						'post_type'      => 'tracks',
						'posts_per_page' => -1,
						'post_status'    => 'publish',
						'orderby'        => 'date',
						'order'          => 'DESC',
					);

					if ( 'popular' === $cf_tracks_view ) {
						$cf_list_args['meta_key'] = '_cf_track_plays'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
						$cf_list_args['orderby']  = 'meta_value_num';
						$cf_list_args['order']    = 'DESC';
						$cf_list_args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
							array(
								'key'     => '_cf_track_plays',
								'value'   => $cf_popular_min,
								'compare' => '>=',
								'type'    => 'NUMERIC',
							),
						);
					}
				}

				$cf_list_q = new WP_Query( $cf_list_args );

				if ( $cf_list_q->have_posts() ) :
					$ad_options   = collective_finity_get_theme_options();
					$ad_zones     = $ad_options['ad_zones'] ?? array();
					$ad_enabled   = ! empty( $ad_zones['archive_native']['enabled'] ) && ! $cf_is_albums_view;
					$ad_frequency = max( 2, absint( $ad_zones['archive_native']['frequency'] ?? 8 ) );
					$card_index   = 0;
					?>
					<div class="cf-library-results" data-cf-library-results>
						<div class="cf-card-grid" data-cf-view="grid">
							<?php
							while ( $cf_list_q->have_posts() ) :
								$cf_list_q->the_post();
								++$card_index;
								if ( $cf_is_albums_view ) {
									$cf_render_album_card( get_the_ID() );
								} else {
									$cf_render_track_card( get_the_ID() );
									if ( $ad_enabled && 0 === $card_index % $ad_frequency && function_exists( 'collective_finity_ad_slot_wrapped' ) ) {
										collective_finity_ad_slot_wrapped( 'archive_native', '<div class="cf-card cf-card--ad">', '</div>' );
									}
								}
							endwhile;
							$cf_list_q->rewind_posts();
							?>
						</div>
						<div class="cf-library-list" data-cf-view="list" hidden>
							<div class="cf-library-list__head" aria-hidden="true">
								<span class="cf-library-list__col cf-library-list__col--track"><?php esc_html_e( 'Track', 'collective-finity' ); ?></span>
								<span class="cf-library-list__col cf-library-list__col--artist"><?php esc_html_e( 'Artist', 'collective-finity' ); ?></span>
								<span class="cf-library-list__col cf-library-list__col--duration"><span class="dashicons dashicons-clock"></span></span>
								<span class="cf-library-list__col cf-library-list__col--genre"><?php esc_html_e( 'Genre', 'collective-finity' ); ?></span>
								<span class="cf-library-list__col cf-library-list__col--date"><?php esc_html_e( 'Released', 'collective-finity' ); ?></span>
								<span class="cf-library-list__col cf-library-list__col--actions"></span>
							</div>
							<?php
							while ( $cf_list_q->have_posts() ) :
								$cf_list_q->the_post();
								if ( $cf_is_albums_view ) {
									$cf_render_album_list_row( get_the_ID() );
								} else {
									$cf_render_track_list_row( get_the_ID() );
								}
							endwhile;
							wp_reset_postdata();
							?>
						</div>
					</div>
				<?php else : ?>
					<div class="tracks-empty-state">
						<span>🎶</span>
						<?php if ( 'popular' === $cf_tracks_view ) : ?>
							<h2><?php esc_html_e( 'No Popular Tracks Yet', 'collective-finity' ); ?></h2>
							<p><?php esc_html_e( 'No tracks have reached the popular view threshold yet. Check back soon!', 'collective-finity' ); ?></p>
						<?php elseif ( $cf_is_albums_view ) : ?>
							<h2><?php esc_html_e( 'No Albums Yet', 'collective-finity' ); ?></h2>
							<p><?php esc_html_e( 'New albums are on their way. Stay tuned!', 'collective-finity' ); ?></p>
						<?php else : ?>
							<h2><?php esc_html_e( 'No Tracks Yet', 'collective-finity' ); ?></h2>
							<p><?php esc_html_e( 'New music is on its way. Stay tuned!', 'collective-finity' ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			<?php endif; ?>

		</div>
	</main>
</div>

<style>
	.cf-library-page {
		padding: 48px 5px 5px;
		box-sizing: border-box;
		max-width: 100%;
		min-width: 0;
	}
	.cf-library-hero {
		position: relative;
		text-align: center;
		padding: clamp(48px, 7vw, 80px) clamp(20px, 4vw, 40px) clamp(56px, 8vw, 88px);
		border-radius: 18px;
		background: #0B0B0B;
		border: 1px solid rgba(30, 30, 30, 0.9);
		overflow: hidden;
		min-width: 0;
		max-width: 100%;
		width: 100%;
		margin: 0 auto 32px;
		box-sizing: border-box;
	}
	@property --cf-library-hero-border-angle {
		syntax: '<angle>';
		initial-value: 0deg;
		inherits: false;
	}
	.cf-library-hero__border {
		position: absolute;
		inset: 0;
		border-radius: inherit;
		padding: 1.5px;
		pointer-events: none;
		z-index: 2;
		background: conic-gradient(
			from var(--cf-library-hero-border-angle),
			transparent 0%,
			transparent 72%,
			rgba(255, 183, 0, 0.05) 80%,
			rgba(255, 183, 0, 0.35) 86%,
			var(--cf-accent, #FFB700) 90%,
			#FFD060 93%,
			rgba(255, 183, 0, 0.2) 96%,
			transparent 100%
		);
		-webkit-mask:
			linear-gradient(#fff 0 0) content-box,
			linear-gradient(#fff 0 0);
		-webkit-mask-composite: xor;
		mask-composite: exclude;
		animation: cfLibraryBorderTravel 5.5s linear infinite;
		filter: drop-shadow(0 0 6px rgba(255, 183, 0, 0.35));
	}
	@keyframes cfLibraryBorderTravel {
		to { --cf-library-hero-border-angle: 360deg; }
	}
	.cf-library-hero__center-glow {
		position: absolute;
		left: 50%;
		top: 46%;
		width: min(70%, 520px);
		aspect-ratio: 1;
		transform: translate(-50%, -50%);
		pointer-events: none;
		z-index: 0;
		border-radius: 50%;
		background: radial-gradient(
			circle,
			rgba(255, 183, 0, 0.14) 0%,
			rgba(255, 183, 0, 0.05) 38%,
			transparent 70%
		);
		animation: cfLibraryCenterGlow 8.2s ease-in-out infinite;
		will-change: transform, opacity;
	}
	@keyframes cfLibraryCenterGlow {
		0%, 100% {
			opacity: 0.35;
			transform: translate(-50%, -50%) scale(0.82);
		}
		50% {
			opacity: 0.7;
			transform: translate(-50%, -50%) scale(1.08);
		}
	}
	.cf-library-hero__content {
		position: relative;
		z-index: 1;
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 14px;
	}
	.cf-library-hero__badge {
		display: inline-block;
		font-family: var(--cf-mono, 'Space Mono', monospace);
		font-size: 11px;
		letter-spacing: 0.1em;
		text-transform: uppercase;
		color: var(--cf-accent, #FFB700);
		border: 1px solid rgba(255, 183, 0, 0.35);
		border-radius: 999px;
		padding: 7px 16px;
		background: rgba(255, 183, 0, 0.08);
	}
	.cf-library-hero__title {
		margin: 0;
		font-family: var(--cf-mono, 'Space Mono', monospace);
		font-size: clamp(28px, 5vw, 40px);
		font-weight: 700;
		line-height: 1.15;
		color: #fff;
	}
	.cf-library-hero__lead {
		margin: 0;
		max-width: 520px;
		font-size: 14px;
		line-height: 1.7;
		color: #B3B3B3;
	}

	.cf-library-search {
		position: relative;
		width: min(100%, 560px);
		margin-bottom: 8px;
	}
	.cf-library-search__icon {
		position: absolute;
		left: 16px;
		top: 50%;
		transform: translateY(-50%);
		color: #7A7A7A;
		font-size: 16px;
		width: 16px;
		height: 16px;
		pointer-events: none;
	}
	.cf-library-search__input {
		width: 100%;
		box-sizing: border-box;
		padding: 14px 56px 14px 44px;
		border-radius: 999px;
		border: 1px solid rgba(255, 255, 255, 0.1);
		background: rgba(255, 255, 255, 0.03);
		color: #fff;
		font-size: 14px;
		outline: none;
		transition: border-color var(--cf-transition-speed, 0.2s) ease, background var(--cf-transition-speed, 0.2s) ease, box-shadow var(--cf-transition-speed, 0.2s) ease;
	}
	.cf-library-search__input::placeholder { color: #7A7A7A; }
	.cf-library-search__input:focus {
		border-color: var(--cf-accent, #FFB700);
		background: rgba(255, 183, 0, 0.05);
		box-shadow: 0 0 20px rgba(255, 183, 0, 0.12);
	}
	.cf-library-search__hint {
		position: absolute;
		right: 14px;
		top: 50%;
		transform: translateY(-50%);
		font-family: var(--cf-mono, 'Space Mono', monospace);
		font-size: 11px;
		color: #7A7A7A;
		border: 1px solid rgba(255, 255, 255, 0.12);
		border-radius: 6px;
		padding: 2px 6px;
		pointer-events: none;
	}

	.cf-library-hero__controls {
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		justify-content: center;
		gap: 14px 18px;
		width: 100%;
		max-width: 920px;
		margin-top: 10px;
	}
	.cf-library-tabs {
		display: flex;
		flex-wrap: wrap;
		gap: 10px;
		justify-content: center;
	}
	.cf-library-tab {
		display: inline-flex;
		align-items: center;
		padding: 8px 20px;
		border-radius: 999px;
		border: 1px solid rgba(255, 255, 255, 0.1);
		background: rgba(255, 255, 255, 0.02);
		color: rgba(255, 255, 255, 0.7);
		font-size: 13px;
		font-weight: 600;
		text-decoration: none;
		transition: background var(--cf-transition-speed, 0.2s) ease, color var(--cf-transition-speed, 0.2s) ease, border-color var(--cf-transition-speed, 0.2s) ease;
	}
	.cf-library-tab:hover {
		border-color: rgba(255, 183, 0, 0.45);
		color: #fff;
	}
	.cf-library-tab.active,
	.cf-library-tab:hover.active {
		background: var(--cf-accent, var(--primary-color, #FFB700));
		color: #000;
		border-color: var(--cf-accent, var(--primary-color, #FFB700));
		font-weight: 700;
	}

	.cf-library-view-switch {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		padding: 4px;
		border-radius: 12px;
		border: 1px solid rgba(255, 255, 255, 0.1);
		background: rgba(255, 255, 255, 0.02);
	}
	.cf-library-view-btn {
		width: 36px;
		height: 36px;
		border-radius: 9px;
		border: 1px solid transparent;
		background: transparent;
		color: #B3B3B3;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		padding: 0;
		transition: border-color var(--cf-transition-speed, 0.2s) ease, color var(--cf-transition-speed, 0.2s) ease, background var(--cf-transition-speed, 0.2s) ease;
	}
	.cf-library-view-btn:hover {
		color: var(--cf-accent, #FFB700);
	}
	.cf-library-view-btn[aria-pressed="true"] {
		border-color: var(--cf-accent, #FFB700);
		color: var(--cf-accent, #FFB700);
		background: rgba(255, 183, 0, 0.08);
	}
	.cf-library-view-btn .dashicons {
		font-size: 18px;
		width: 18px;
		height: 18px;
	}

	@media (prefers-reduced-motion: reduce) {
		.cf-library-hero__border,
		.cf-library-hero__center-glow {
			animation: none;
		}
	}

	.tracks-grid-container { max-width: 1200px; margin: 0 auto; padding: 0; }

	.cf-section-label { font-size: 18px; font-weight: 700; color: #fff; margin: 0 0 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
	.cf-section-label a { font-size: 12px; font-weight: 600; color: #7A7A7A; text-decoration: none; white-space: nowrap; }
	.cf-section-label a:hover { color: var(--primary-color, #FFB700); }

	.cf-library-ad { margin: 0 0 28px; }

	.cf-carousel-section { margin-bottom: 40px; }

	.cf-carousel {
		--cf-visible: 6;
		--cf-gap: 18px;
		position: relative;
		display: flex;
		align-items: center;
		gap: 8px;
	}
	.cf-carousel-viewport {
		flex: 1;
		min-width: 0;
		overflow-x: hidden;
		overflow-y: visible;
	}
	.cf-carousel-track {
		display: flex;
		gap: var(--cf-gap);
		width: 100%;
		overflow-x: auto;
		overflow-y: visible;
		scroll-snap-type: x mandatory;
		scroll-behavior: smooth;
		scrollbar-width: none;
		-webkit-overflow-scrolling: touch;
		padding: 10px 2px 14px;
		box-sizing: border-box;
	}
	.cf-carousel-track::-webkit-scrollbar { display: none; }

	.cf-carousel-track > .cf-card {
		flex: 0 0 calc((100% - (var(--cf-visible) - 1) * var(--cf-gap)) / var(--cf-visible));
		max-width: calc((100% - (var(--cf-visible) - 1) * var(--cf-gap)) / var(--cf-visible));
		scroll-snap-align: start;
	}

	.cf-carousel-btn {
		flex-shrink: 0;
		width: 36px;
		height: 36px;
		border-radius: 50%;
		border: 1px solid #2c2c2c;
		background: #141414;
		color: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		padding: 0;
		transition: border-color .15s ease, color .15s ease, background .15s ease;
	}
	.cf-carousel-btn:hover {
		border-color: var(--primary-color, #FFB700);
		color: var(--primary-color, #FFB700);
	}
	.cf-carousel-btn[hidden] { display: none !important; }
	.cf-carousel-btn .dashicons { font-size: 18px; width: 18px; height: 18px; }

	.cf-card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 22px; }
	.cf-card-grid[hidden],
	.cf-library-list[hidden] { display: none !important; }

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
		transition: border-color var(--cf-transition-speed, 0.2s) ease, transform var(--cf-transition-speed, 0.2s) ease, box-shadow var(--cf-transition-speed, 0.2s) ease;
	}
	.cf-card-primary {
		display: block;
		text-decoration: none;
		color: inherit;
	}
	.cf-cover {
		position: relative;
		width: 100%;
		aspect-ratio: 1;
		border-radius: 0;
		overflow: hidden;
		background: #0c0c0c;
		margin-bottom: 10px;
	}
	.cf-cover img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .2s ease; }

	.cf-play-btn { position: absolute; right: 8px; bottom: 8px; width: 40px; height: 40px; border: none; border-radius: 50%; background: var(--primary-color, #FFB700); display: flex; align-items: center; justify-content: center; opacity: 0; transform: translateY(6px); transition: all .18s ease; cursor: pointer; box-shadow: 0 8px 16px rgba(0,0,0,.4); }
	.cf-play-btn .dashicons { color: #1a1400; font-size: 18px; width: 18px; height: 18px; }

	.cf-heart-btn.cf-interaction-btn { position: absolute; left: 8px; top: 8px; width: 28px; height: 28px; border: none; border-radius: 50%; background: rgba(0,0,0,.55); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity .18s ease; cursor: pointer; padding: 0; }
	.cf-heart-btn .dashicons { color: #fff; font-size: 14px; width: 14px; height: 14px; }
	.cf-heart-btn.cf-interaction-btn.active { opacity: 1; }
	.cf-heart-btn.cf-interaction-btn.active .dashicons { color: var(--primary-color, #FFB700); }

	.cf-card-title { font-size: 14px; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding: 0 12px; }
	.cf-card-sub { font-size: 12px; color: #7A7A7A; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding: 0 12px; transition: color var(--cf-transition-speed, 0.2s) ease; }
	.cf-artist-link {
		color: inherit;
		text-decoration: none;
		transition: color var(--cf-transition-speed, 0.2s) ease;
	}
	.cf-artist-link:hover {
		color: var(--primary-color, #FFB700);
		text-decoration: underline;
	}
	.cf-card-chips {
		display: flex;
		flex-wrap: wrap;
		gap: 6px;
		padding: 6px 12px 0;
	}
	.cf-card-chip { display: inline-block; margin: 0; font-size: 10px; color: #B3B3B3; background: rgba(255,255,255,0.04); border: 1px solid var(--cf-border, #232323); padding: 2px 8px; border-radius: 10px; transition: border-color var(--cf-transition-speed, 0.2s) ease, color var(--cf-transition-speed, 0.2s) ease; }
	.cf-card-chip--type { color: #8a8a8a; }

	/* Soft accent spotlight behind the card + orange outline (hover / focus / press). */
	.cf-card:not(.cf-card--ad):is(:hover, :focus-visible, :focus-within) {
		border-color: color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 55%, transparent);
		box-shadow:
			0 0 0 1px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 40%, transparent),
			0 14px 28px -10px rgba(0, 0, 0, 0.55),
			0 0 28px 4px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 24%, transparent),
			0 0 64px 16px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 12%, transparent);
	}
	.cf-card:not(.cf-card--ad):is(:hover, :focus-visible, :focus-within) .cf-cover img { transform: scale(1.03); }
	.cf-card:not(.cf-card--ad):is(:hover, :focus-visible, :focus-within) .cf-play-btn { opacity: 1; transform: translateY(0); }
	.cf-card:not(.cf-card--ad):is(:hover, :focus-visible, :focus-within) .cf-heart-btn.cf-interaction-btn { opacity: 1; }
	.cf-card:not(.cf-card--ad):is(:hover, :focus-visible, :focus-within) .cf-card-sub {
		color: var(--cf-link, var(--cf-accent, var(--primary-color, #FFB700)));
	}
	.cf-card:not(.cf-card--ad):is(:hover, :focus-visible, :focus-within) .cf-card-chip {
		border-color: var(--cf-link, var(--cf-accent, var(--primary-color, #FFB700)));
	}

	@supports not (color: color-mix(in srgb, red 50%, blue)) {
		.cf-card:not(.cf-card--ad):is(:hover, :focus-visible, :focus-within) {
			border-color: rgba(255, 183, 0, 0.55);
			box-shadow:
				0 0 0 1px rgba(255, 183, 0, 0.4),
				0 14px 28px -10px rgba(0, 0, 0, 0.55),
				0 0 28px 4px rgba(255, 183, 0, 0.24),
				0 0 64px 16px rgba(255, 183, 0, 0.12);
		}
	}

	.cf-card--ad { grid-column: 1 / -1; display: flex; align-items: center; justify-content: center; min-height: 160px; padding: 16px; }
	.cf-ad-slot { margin: 0 auto; max-width: 100%; text-align: center; }
	.cf-ad-slot--preview { align-items: center; background: rgba(255,255,255,0.04); border: 1px dashed rgba(255,183,0,0.35); border-radius: 12px; color: rgba(255,255,255,0.55); display: flex; font-family: 'Space Mono', monospace; font-size: 13px; justify-content: center; min-height: 90px; padding: 24px; }

	/* List view */
	.cf-library-list {
		display: flex;
		flex-direction: column;
		gap: 8px;
	}
	.cf-library-list__head {
		display: grid;
		grid-template-columns: minmax(0, 2.2fr) minmax(0, 1.2fr) 64px minmax(0, 1fr) minmax(0, 0.9fr) 40px;
		gap: 12px;
		align-items: center;
		padding: 0 14px 8px 56px;
		font-size: 10px;
		font-weight: 600;
		letter-spacing: 0.08em;
		text-transform: uppercase;
		color: #7A7A7A;
	}
	.cf-library-list__col--duration { text-align: center; }
	.cf-library-list__col--duration .dashicons { font-size: 14px; width: 14px; height: 14px; }
	.cf-library-list-row {
		display: grid;
		grid-template-columns: 40px 48px minmax(0, 2.2fr) minmax(0, 1.2fr) 64px minmax(0, 1fr) minmax(0, 0.9fr) 40px;
		gap: 12px;
		align-items: center;
		padding: 10px 14px;
		text-decoration: none;
		color: inherit;
		background: var(--cf-bg-card, #141414);
		border: var(--cf-card-border-width, 1px) solid var(--cf-border, #232323);
		border-radius: var(--cf-card-radius, 12px);
		transition: border-color var(--cf-transition-speed, 0.2s) ease, box-shadow var(--cf-transition-speed, 0.2s) ease;
	}
	.cf-library-list-row--album {
		grid-template-columns: 48px minmax(0, 2.2fr) minmax(0, 1.2fr) 64px minmax(0, 1fr) minmax(0, 0.9fr);
		padding-left: 14px;
	}
	.cf-library-list-row:is(:hover, :focus-within) {
		border-color: color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 55%, transparent);
		box-shadow:
			0 0 0 1px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 40%, transparent),
			0 0 28px 4px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 18%, transparent);
	}
	.cf-library-list-row:is(:hover, :focus-within) .cf-library-list-row__title {
		color: var(--cf-accent, var(--primary-color, #FFB700));
	}
	.cf-library-list-row:is(:hover, :focus-within) .cf-library-list-row__play {
		opacity: 1;
	}
	.cf-library-list-row__play {
		width: 36px;
		height: 36px;
		border: none;
		border-radius: 50%;
		background: var(--primary-color, #FFB700);
		color: #1a1400;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		opacity: 0;
		padding: 0;
		transition: opacity .18s ease;
	}
	.cf-library-list-row__play .dashicons { font-size: 16px; width: 16px; height: 16px; }
	.cf-library-list-row__thumb {
		display: block;
		width: 48px;
		height: 48px;
		border-radius: 8px;
		overflow: hidden;
		background: #0c0c0c;
		flex-shrink: 0;
	}
	.cf-library-list-row__thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
	.cf-library-list-row__main {
		display: flex;
		flex-direction: column;
		align-items: flex-start;
		gap: 6px;
		min-width: 0;
	}
	.cf-library-list-row__title {
		font-size: 14px;
		font-weight: 600;
		color: #fff;
		text-decoration: none;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: 100%;
		transition: color var(--cf-transition-speed, 0.2s) ease;
	}
	.cf-library-list-row__artist,
	.cf-library-list-row__duration,
	.cf-library-list-row__genre,
	.cf-library-list-row__date {
		font-size: 12px;
		color: #7A7A7A;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		min-width: 0;
	}
	.cf-library-list-row__duration { text-align: center; font-variant-numeric: tabular-nums; }
	.cf-library-list-row__icon {
		position: static;
		width: 32px;
		height: 32px;
		border: none;
		border-radius: 50%;
		background: transparent;
		color: #B3B3B3;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		opacity: 1;
		padding: 0;
	}
	.cf-library-list-row__icon .dashicons { font-size: 16px; width: 16px; height: 16px; color: inherit; }
	.cf-library-list-row__icon.active,
	.cf-library-list-row__icon.active .dashicons { color: var(--primary-color, #FFB700); }

	.tracks-empty-state {
		text-align: center;
		padding: 80px 20px;
		border: 1px solid rgba(255,255,255,0.06);
		border-radius: 16px;
		background: rgba(255,255,255,0.02);
	}
	.tracks-empty-state span { font-size: 48px; display: block; margin-bottom: 18px; }
	.tracks-empty-state h2 { color: #fff; margin: 0 0 10px; font-size: 24px; }
	.tracks-empty-state p { color: #888; margin: 0; line-height: 1.7; }

	@media (max-width: 1023px) {
		.cf-carousel { --cf-visible: 4; --cf-gap: 14px; }
		.cf-library-list__head,
		.cf-library-list-row__genre,
		.cf-library-list-row__date { display: none; }
		.cf-library-list-row {
			grid-template-columns: 40px 48px minmax(0, 1.6fr) minmax(0, 1fr) 56px 36px;
		}
		.cf-library-list-row--album {
			grid-template-columns: 48px minmax(0, 1.6fr) minmax(0, 1fr) 56px;
		}
	}
	@media (max-width: 640px) {
		.cf-carousel { --cf-visible: 2; --cf-gap: 12px; }
		.cf-carousel-btn { display: none !important; }
		.cf-card-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
		.cf-play-btn { opacity: 1; transform: none; width: 32px; height: 32px; }
		.cf-heart-btn.cf-interaction-btn { opacity: 1; }
		.cf-library-hero__controls { flex-direction: column; }
		.cf-library-list-row {
			grid-template-columns: 36px 40px minmax(0, 1fr) 36px;
			gap: 8px;
		}
		.cf-library-list-row__artist,
		.cf-library-list-row__duration { display: none; }
		.cf-library-list-row--album {
			grid-template-columns: 40px minmax(0, 1fr);
		}
		.cf-library-list-row__play { opacity: 1; width: 32px; height: 32px; }
		.cf-library-search__hint { display: none; }
	}

	/* Touch: brief press glow without sticky hover state. */
	@media (hover: none) {
		.cf-card:not(.cf-card--ad):active {
			border-color: color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 55%, transparent);
			box-shadow:
				0 0 0 1px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 40%, transparent),
				0 14px 28px -10px rgba(0, 0, 0, 0.55),
				0 0 28px 4px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 24%, transparent),
				0 0 64px 16px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 12%, transparent);
		}
		.cf-card:not(.cf-card--ad):active .cf-card-sub {
			color: var(--cf-link, var(--cf-accent, var(--primary-color, #FFB700)));
		}
		.cf-card:not(.cf-card--ad):active .cf-card-chip {
			border-color: var(--cf-link, var(--cf-accent, var(--primary-color, #FFB700)));
		}
		.cf-library-list-row__play { opacity: 1; }
	}
</style>

<script>
(function () {
	var VIEW_STORAGE_KEY = 'cf_library_view_mode';

	function visibleCount() {
		var w = window.innerWidth || document.documentElement.clientWidth;
		if (w <= 640) return 2;
		if (w <= 1023) return 4;
		return 6;
	}

	function setupCarousel(root) {
		var track = root.querySelector('.cf-carousel-track');
		var prev = root.querySelector('.cf-carousel-prev');
		var next = root.querySelector('.cf-carousel-next');
		var viewport = root.querySelector('.cf-carousel-viewport');
		if (!track || !prev || !next || !viewport) return;

		function pageWidth() {
			return track.clientWidth;
		}

		function updateButtons() {
			if (visibleCount() <= 2) {
				prev.hidden = true;
				next.hidden = true;
				return;
			}
			var maxScroll = Math.max(0, track.scrollWidth - track.clientWidth - 2);
			prev.hidden = track.scrollLeft <= 2;
			next.hidden = track.scrollLeft >= maxScroll;
		}

		prev.addEventListener('click', function () {
			track.scrollBy({ left: -pageWidth(), behavior: 'smooth' });
		});
		next.addEventListener('click', function () {
			track.scrollBy({ left: pageWidth(), behavior: 'smooth' });
		});
		track.addEventListener('scroll', updateButtons, { passive: true });
		window.addEventListener('resize', updateButtons);
		updateButtons();
	}

	document.querySelectorAll('[data-cf-carousel]').forEach(setupCarousel);

	function applyViewMode(mode) {
		mode = mode === 'list' ? 'list' : 'grid';
		try { localStorage.setItem(VIEW_STORAGE_KEY, mode); } catch (e) {}

		document.querySelectorAll('[data-cf-view]').forEach(function (el) {
			var isMatch = el.getAttribute('data-cf-view') === mode;
			if (isMatch) {
				el.removeAttribute('hidden');
			} else {
				el.setAttribute('hidden', 'hidden');
			}
		});

		document.querySelectorAll('[data-cf-view-mode]').forEach(function (btn) {
			var pressed = btn.getAttribute('data-cf-view-mode') === mode;
			btn.setAttribute('aria-pressed', pressed ? 'true' : 'false');
		});
	}

	var savedMode = 'grid';
	try {
		var stored = localStorage.getItem(VIEW_STORAGE_KEY);
		if (stored === 'list' || stored === 'grid') savedMode = stored;
	} catch (e) {}
	applyViewMode(savedMode);

	document.querySelectorAll('[data-cf-view-mode]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			applyViewMode(btn.getAttribute('data-cf-view-mode'));
		});
	});

	var searchInput = document.querySelector('[data-cf-library-search]');
	if (searchInput) {
		var hint = document.querySelector('.cf-library-search__hint');
		if (hint && !(navigator.platform && /Mac/i.test(navigator.platform))) {
			hint.textContent = 'Ctrl K';
		}

		function filterLibrary() {
			var q = (searchInput.value || '').trim().toLowerCase();
			document.querySelectorAll('[data-cf-search-title]').forEach(function (el) {
				var title = el.getAttribute('data-cf-search-title') || '';
				el.style.display = (!q || title.indexOf(q) !== -1) ? '' : 'none';
			});
		}

		searchInput.addEventListener('input', filterLibrary);

		document.addEventListener('keydown', function (event) {
			if ((event.metaKey || event.ctrlKey) && (event.key === 'k' || event.key === 'K')) {
				var tag = (event.target && event.target.tagName) ? event.target.tagName.toLowerCase() : '';
				if (tag === 'input' || tag === 'textarea' || (event.target && event.target.isContentEditable)) {
					return;
				}
				event.preventDefault();
				searchInput.focus();
				searchInput.select();
			}
		});
	}
})();
</script>

<?php get_footer(); ?>
