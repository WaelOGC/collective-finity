<?php
/**
 * Front Page (site homepage) for Collective Finity.
 *
 * Section order: Hero → Featured Release → Latest Releases → Featured Tracks →
 * More Than Music → Latest Articles → Join the Collective → The Future Begins →
 * Vision / Newsletter panel.
 *
 * @package Collective_Finity
 */

get_header();

$cf_theme_uri = get_template_directory_uri();
$cf_theme_dir = get_template_directory();

$cf_albums_url = get_post_type_archive_link( 'albums' );
$cf_tracks_url = get_post_type_archive_link( 'tracks' );
$cf_albums_url = $cf_albums_url ? $cf_albums_url : home_url( '/albums/' );
$cf_tracks_url = $cf_tracks_url ? $cf_tracks_url : home_url( '/tracks/' );
$cf_community_url = collective_finity_get_page_link( 'join-community', '/join-community/' );
$cf_about_url     = collective_finity_get_page_link( 'about', '/about/' );

$cf_hero_image_url = $cf_theme_uri . '/assets/images/section-background/the-spark-of-creation.webp';
$cf_join_image_url = $cf_theme_uri . '/assets/images/section-background/join-the-collective.webp';
$cf_mtm_image_url  = $cf_theme_uri . '/assets/images/section-background/cf-more-than-music.webp';

$cf_future_rel = '/assets/images/section-background/the-future-begins.webp';
if ( ! file_exists( $cf_theme_dir . $cf_future_rel ) && file_exists( $cf_theme_dir . $cf_future_rel . '.jpg' ) ) {
	$cf_future_rel .= '.jpg';
}
$cf_future_image_url = $cf_theme_uri . $cf_future_rel;

$cf_track_count   = (int) wp_count_posts( 'tracks' )->publish;
$cf_article_count = (int) wp_count_posts( 'post' )->publish;

/* Featured Release: Land Of Light */
$cf_featured_album_id = 0;
$cf_featured_candidates = get_posts(
	array(
		'post_type'              => 'albums',
		'posts_per_page'         => 12,
		'post_status'            => 'publish',
		's'                      => 'Land Of Light',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	)
);
foreach ( $cf_featured_candidates as $cf_candidate ) {
	if ( 0 === strcasecmp( $cf_candidate->post_title, 'Land Of Light' ) ) {
		$cf_featured_album_id = (int) $cf_candidate->ID;
		break;
	}
}
if ( ! $cf_featured_album_id ) {
	$cf_fallback_album = get_posts(
		array(
			'post_type'      => 'albums',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	if ( ! empty( $cf_fallback_album ) ) {
		$cf_featured_album_id = (int) $cf_fallback_album[0];
	}
}

$cf_featured_album_url   = $cf_featured_album_id ? get_permalink( $cf_featured_album_id ) : $cf_albums_url;
$cf_featured_album_title = $cf_featured_album_id ? get_the_title( $cf_featured_album_id ) : 'Land Of Light';
$cf_featured_cover       = $cf_featured_album_id ? get_the_post_thumbnail_url( $cf_featured_album_id, 'large' ) : '';
$cf_featured_listen_url  = '';
$cf_featured_listen_meta = array(
	'audio'  => '',
	'title'  => '',
	'artist' => '',
	'cover'  => '',
);
$cf_featured_meta = array(
	'type'     => 'Album',
	'tracks'   => '10 Tracks',
	'duration' => '42 min',
	'genre'    => 'Cinematic • Orchestral',
	'year'     => '2024',
);

/**
 * Format seconds as m:ss / mm:ss.
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

if ( $cf_featured_album_id ) {
	$cf_album_track_ids = get_posts(
		array(
			'post_type'      => 'tracks',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order date',
			'order'          => 'ASC',
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'associated_album',
					'value'   => $cf_featured_album_id,
					'compare' => '=',
				),
			),
		)
	);

	if ( empty( $cf_featured_cover ) && ! empty( $cf_album_track_ids ) ) {
		$cf_featured_cover = get_post_meta( $cf_album_track_ids[0], 'track_cover_url', true );
		if ( ! $cf_featured_cover ) {
			$cf_featured_cover = get_the_post_thumbnail_url( $cf_album_track_ids[0], 'large' );
		}
	}

	$cf_album_track_count = count( $cf_album_track_ids );
	if ( $cf_album_track_count > 0 ) {
		$cf_featured_meta['tracks'] = sprintf(
			_n( '%d Track', '%d Tracks', $cf_album_track_count, 'collective-finity' ),
			$cf_album_track_count
		);
	}

	$cf_album_seconds = 0;
	$cf_genre_names   = array();
	foreach ( $cf_album_track_ids as $cf_album_tid ) {
		$cf_t_audio   = get_post_meta( $cf_album_tid, 'track_audio_url', true );
		$cf_t_preview = get_post_meta( $cf_album_tid, 'track_preview_url', true );
		$cf_t_play    = ! empty( $cf_t_preview ) ? $cf_t_preview : $cf_t_audio;
		$cf_album_seconds += $cf_audio_duration_seconds( $cf_t_play );
		$cf_t_genres = wp_get_post_terms( $cf_album_tid, 'music_genre', array( 'fields' => 'names' ) );
		if ( ! is_wp_error( $cf_t_genres ) && ! empty( $cf_t_genres ) ) {
			foreach ( $cf_t_genres as $cf_gname ) {
				$cf_genre_names[ $cf_gname ] = true;
			}
		}
	}
	if ( $cf_album_seconds > 0 ) {
		$cf_featured_meta['duration'] = sprintf(
			/* translators: %d: album duration in minutes */
			__( '%d min', 'collective-finity' ),
			max( 1, (int) round( $cf_album_seconds / 60 ) )
		);
	}
	if ( ! empty( $cf_genre_names ) ) {
		$cf_featured_meta['genre'] = implode( ' • ', array_slice( array_keys( $cf_genre_names ), 0, 2 ) );
	}

	$cf_featured_meta['year'] = get_the_date( 'Y', $cf_featured_album_id );

	if ( ! empty( $cf_album_track_ids ) ) {
		$cf_tid = (int) $cf_album_track_ids[0];
		$cf_featured_listen_url = get_permalink( $cf_tid );
		$cf_audio               = get_post_meta( $cf_tid, 'track_audio_url', true );
		$cf_preview             = get_post_meta( $cf_tid, 'track_preview_url', true );
		$cf_playback            = ! empty( $cf_preview ) ? $cf_preview : $cf_audio;
		$cf_artists             = wp_get_post_terms( $cf_tid, 'track_artist' );
		$cf_artist_name         = ( ! is_wp_error( $cf_artists ) && ! empty( $cf_artists ) ) ? $cf_artists[0]->name : collective_finity_brand_name();
		$cf_tcover              = get_post_meta( $cf_tid, 'track_cover_url', true );
		if ( ! $cf_tcover ) {
			$cf_tcover = get_the_post_thumbnail_url( $cf_tid, 'medium' );
		}
		if ( ! $cf_tcover ) {
			$cf_tcover = $cf_featured_cover ? $cf_featured_cover : collective_finity_default_art_url();
		}
		$cf_featured_listen_meta = array(
			'audio'  => $cf_playback ? $cf_playback : '',
			'title'  => get_the_title( $cf_tid ),
			'artist' => $cf_artist_name,
			'cover'  => $cf_tcover,
		);
	}
}

if ( empty( $cf_featured_cover ) ) {
	$cf_featured_cover = collective_finity_default_art_url();
}

$cf_latest_albums = new WP_Query(
	array(
		'post_type'      => 'albums',
		'posts_per_page' => 8,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	)
);

$cf_featured_tracks = new WP_Query(
	array(
		'post_type'      => 'tracks',
		'posts_per_page' => 4,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
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

$cf_blog_limit   = min( 4, max( 0, (int) $cf_article_count ) );
$cf_recent_posts = ( $cf_blog_limit > 0 )
	? new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => $cf_blog_limit,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	)
	: new WP_Query( array( 'post__in' => array( 0 ) ) );

/* Preload published tracks + articles for hero live search (homepage only). */
$cf_hero_search_items = array();
if ( $cf_track_count > 0 || $cf_article_count > 0 ) {
	$cf_hero_search_query = new WP_Query(
		array(
			'post_type'              => array( 'post', 'tracks' ),
			'posts_per_page'         => 200,
			'post_status'            => 'publish',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'orderby'                => 'date',
			'order'                  => 'DESC',
		)
	);
	while ( $cf_hero_search_query->have_posts() ) {
		$cf_hero_search_query->the_post();
		$cf_sid     = get_the_ID();
		$cf_stype   = ( 'tracks' === get_post_type( $cf_sid ) ) ? 'track' : 'article';
		$cf_excerpt = wp_trim_words( get_the_excerpt( $cf_sid ), 14, '&hellip;' );
		$cf_content = wp_strip_all_tags( get_post_field( 'post_content', $cf_sid ) );
		$cf_hero_search_items[] = array(
			'type'    => $cf_stype,
			'title'   => get_the_title( $cf_sid ),
			'excerpt' => $cf_excerpt,
			'url'     => get_permalink( $cf_sid ),
			'search'  => wp_trim_words( $cf_content, 40, '' ),
		);
	}
	wp_reset_postdata();
}

/**
 * Render a library-style album card.
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
	<a href="<?php echo esc_url( $link ); ?>" class="cf-card">
		<div class="cf-cover"><img src="<?php echo esc_url( $cover ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy"></div>
		<div class="cf-card-title"><?php echo esc_html( $title ); ?></div>
		<div class="cf-card-sub"><?php echo esc_html( sprintf( _n( 'Album · %d track', 'Album · %d tracks', $count, 'collective-finity' ), $count ) ); ?></div>
	</a>
	<?php
};

/**
 * Render a compact featured-track list row.
 *
 * @param int $track_id Track post ID.
 */
$cf_render_track_row = static function ( $track_id ) use ( $cf_format_track_time, $cf_audio_duration_seconds ) {
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
	$title        = get_the_title( $track_id );
	$permalink    = get_permalink( $track_id );
	$seconds      = $cf_audio_duration_seconds( $audio_url );
	$duration     = $seconds > 0 ? $cf_format_track_time( $seconds ) : '—:—';
	?>
	<div class="cf-home-track-row">
		<button
			type="button"
			class="cf-home-track-row__play"
			aria-label="<?php echo esc_attr( sprintf( __( 'Play %s', 'collective-finity' ), $title ) ); ?>"
			onclick="if (window.playTrack) { window.playTrack('<?php echo esc_js( $audio_url ); ?>', '<?php echo esc_js( $title ); ?>', '<?php echo esc_js( $artist_name ); ?>', '<?php echo esc_js( $cover_image ); ?>'); }"
		>
			<span class="dashicons dashicons-controls-play" aria-hidden="true"></span>
		</button>
		<a class="cf-home-track-row__thumb" href="<?php echo esc_url( $permalink ); ?>">
			<img src="<?php echo esc_url( $cover_image ); ?>" alt="" width="44" height="44" loading="lazy">
		</a>
		<a class="cf-home-track-row__info" href="<?php echo esc_url( $permalink ); ?>">
			<span class="cf-home-track-row__title"><?php echo esc_html( $title ); ?></span>
			<span class="cf-home-track-row__artist"><?php echo esc_html( $artist_name ); ?></span>
		</a>
		<span class="cf-home-track-row__duration"><?php echo esc_html( $duration ); ?></span>
		<button type="button" class="cf-home-track-row__icon cf-interaction-btn cf-like-btn cf-heart-btn" data-track-id="<?php echo esc_attr( (string) $track_id ); ?>" title="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>">
			<span class="dashicons dashicons-heart" aria-hidden="true"></span>
		</button>
		<a class="cf-home-track-row__icon cf-home-track-row__more" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Open %s', 'collective-finity' ), $title ) ); ?>">
			<span class="dashicons dashicons-ellipsis" aria-hidden="true"></span>
		</a>
	</div>
	<?php
};
?>

<main id="cf-main-app-content" class="site-main cf-home cf-home-redesign" role="main">

	<!-- 1. HERO -->
	<section class="cf-home-hero" aria-labelledby="cf-home-hero-heading" style="--cf-home-hero-image: url('<?php echo esc_url( $cf_hero_image_url ); ?>');">
		<div class="cf-home-hero__border" aria-hidden="true"></div>
		<div class="cf-home-hero__media" aria-hidden="true"></div>
		<div class="cf-home-hero__shade" aria-hidden="true"></div>
		<div class="cf-home-hero__copy">
			<p class="cf-home-eyebrow">COLLECTIVE FINITY</p>
			<h1 id="cf-home-hero-heading" class="cf-home-hero__title">Music Beyond Algorithms. Human Creativity Without Limits.</h1>
			<p class="cf-home-hero__lead">Discover original AI-assisted music, immersive albums, educational resources, and a growing creative community where technology expands imagination—not replaces it.</p>

			<div class="cf-hero-search" data-cf-hero-search>
				<label class="screen-reader-text" for="cf-hero-search-input"><?php esc_html_e( 'Search tracks and articles', 'collective-finity' ); ?></label>
				<div class="cf-hero-search-field">
					<span class="cf-hero-search-icon" aria-hidden="true"><?php echo collective_finity_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<input
						type="search"
						id="cf-hero-search-input"
						class="cf-hero-search-input"
						placeholder="<?php esc_attr_e( 'Search tracks & articles…', 'collective-finity' ); ?>"
						autocomplete="off"
						aria-autocomplete="list"
						aria-controls="cf-hero-search-results"
						aria-expanded="false"
					>
				</div>
				<div id="cf-hero-search-results" class="cf-hero-search-results" role="listbox" hidden></div>
			</div>
			<script type="application/json" id="cf-hero-search-data"><?php echo wp_json_encode( $cf_hero_search_items, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>

			<div class="cf-home-hero__actions">
				<a class="cf-home-btn cf-home-btn--primary" href="<?php echo esc_url( $cf_tracks_url ); ?>">Explore Music</a>
				<a class="cf-home-btn cf-home-btn--ghost" href="<?php echo esc_url( $cf_community_url ); ?>">Join Community</a>
			</div>
		</div>
	</section>

	<!-- 2. FEATURED RELEASE -->
	<section class="cf-home-section cf-home-featured-release" aria-labelledby="cf-home-featured-release-heading" data-cf-home-reveal>
		<div class="cf-home-featured-release__panel">
			<div class="cf-home-featured-release__grid">
				<div class="cf-home-featured-release__art">
					<img
						src="<?php echo esc_url( $cf_featured_cover ); ?>"
						alt="<?php echo esc_attr( $cf_featured_album_title ); ?>"
						width="640"
						height="640"
						loading="lazy"
						decoding="async"
					>
				</div>
				<div class="cf-home-featured-release__content">
					<p class="cf-home-eyebrow">FEATURED RELEASE</p>
					<h2 id="cf-home-featured-release-heading" class="cf-home-featured-release__title">Land Of Light</h2>
					<p class="cf-home-featured-release__artist"><?php echo esc_html( collective_finity_brand_name() ); ?></p>
					<p class="cf-home-prose">A cinematic journey through hope, discovery, and imagination, blending orchestral emotion with modern AI-assisted composition.</p>
					<div class="cf-home-featured-release__actions">
						<?php if ( ! empty( $cf_featured_listen_meta['audio'] ) ) : ?>
							<button
								type="button"
								class="cf-home-btn cf-home-btn--primary"
								data-cf-home-listen
								data-audio="<?php echo esc_url( $cf_featured_listen_meta['audio'] ); ?>"
								data-title="<?php echo esc_attr( $cf_featured_listen_meta['title'] ); ?>"
								data-artist="<?php echo esc_attr( $cf_featured_listen_meta['artist'] ); ?>"
								data-cover="<?php echo esc_url( $cf_featured_listen_meta['cover'] ); ?>"
							>Listen Now</button>
						<?php else : ?>
							<a class="cf-home-btn cf-home-btn--primary" href="<?php echo esc_url( $cf_featured_listen_url ? $cf_featured_listen_url : $cf_featured_album_url ); ?>">Listen Now</a>
						<?php endif; ?>
						<a class="cf-home-btn cf-home-btn--ghost" href="<?php echo esc_url( $cf_featured_album_url ); ?>">View Album</a>
					</div>
				</div>
				<ul class="cf-home-featured-release__meta" aria-label="<?php esc_attr_e( 'Album details', 'collective-finity' ); ?>">
					<li>
						<span class="cf-home-featured-release__meta-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="2.2"/></svg>
						</span>
						<span><?php echo esc_html( $cf_featured_meta['type'] ); ?></span>
					</li>
					<li>
						<span class="cf-home-featured-release__meta-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
						</span>
						<span><?php echo esc_html( $cf_featured_meta['tracks'] ); ?></span>
					</li>
					<li>
						<span class="cf-home-featured-release__meta-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
						</span>
						<span><?php echo esc_html( $cf_featured_meta['duration'] ); ?></span>
					</li>
					<li>
						<span class="cf-home-featured-release__meta-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18"/><path d="M12 3a14 14 0 0 0 0 18"/></svg>
						</span>
						<span><?php echo esc_html( $cf_featured_meta['genre'] ); ?></span>
					</li>
					<li class="cf-home-featured-release__meta-item--year">
						<span class="cf-home-featured-release__meta-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M8 3v4"/><path d="M16 3v4"/><path d="M3.5 10h17"/></svg>
						</span>
						<span><?php echo esc_html( $cf_featured_meta['year'] ); ?></span>
					</li>
				</ul>
			</div>
		</div>
	</section>

	<!-- 3. LATEST RELEASES -->
	<section class="cf-home-section cf-home-latest-releases" aria-labelledby="cf-home-latest-releases-heading" data-cf-home-reveal>
		<div class="cf-home-section-head">
			<div class="cf-home-section-head__row">
				<h2 id="cf-home-latest-releases-heading" class="cf-home-section-title">Latest Releases</h2>
				<a class="cf-home-section-link" href="<?php echo esc_url( $cf_albums_url ); ?>">View All</a>
			</div>
			<p class="cf-home-section-desc">Discover the newest music released on Collective Finity.</p>
		</div>

		<?php if ( $cf_latest_albums->have_posts() ) : ?>
			<div class="cf-home-card-grid cf-home-card-grid--albums">
				<?php
				while ( $cf_latest_albums->have_posts() ) :
					$cf_latest_albums->the_post();
					$cf_render_album_card( get_the_ID() );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<div class="cf-home-empty"><?php esc_html_e( 'New albums are on the way — check back soon.', 'collective-finity' ); ?></div>
		<?php endif; ?>
	</section>

	<!-- 4. FEATURED TRACKS -->
	<section class="cf-home-section cf-home-featured-tracks" aria-labelledby="cf-home-featured-tracks-heading" data-cf-home-reveal>
		<div class="cf-home-section-head">
			<div class="cf-home-section-head__row">
				<h2 id="cf-home-featured-tracks-heading" class="cf-home-section-title">Featured Tracks</h2>
				<a class="cf-home-section-link" href="<?php echo esc_url( $cf_tracks_url ); ?>">View All</a>
			</div>
			<p class="cf-home-section-desc">A curated selection of cinematic tracks crafted to inspire every journey.</p>
		</div>

		<?php if ( $cf_featured_tracks->have_posts() ) : ?>
			<div class="cf-home-track-list">
				<?php
				while ( $cf_featured_tracks->have_posts() ) :
					$cf_featured_tracks->the_post();
					$cf_render_track_row( get_the_ID() );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<div class="cf-home-empty"><?php esc_html_e( 'New tracks are on the way — check back soon.', 'collective-finity' ); ?></div>
		<?php endif; ?>
	</section>

	<!-- 5. MORE THAN MUSIC -->
	<section class="cf-home-section cf-home-more-than-music" aria-labelledby="cf-home-more-heading" data-cf-home-reveal>
		<div class="cf-home-more-than-music__grid">
			<div class="cf-home-more-than-music__copy">
				<h2 id="cf-home-more-heading" class="cf-home-section-title">More Than Music</h2>
				<p class="cf-home-prose">Collective Finity is more than a music platform. It is a place where artists, producers, storytellers, and curious creators explore the future of music through the collaboration between human creativity and artificial intelligence. From original cinematic releases to practical educational content, every project is designed to inspire meaningful creation.</p>
				<div class="cf-home-more-than-music__actions">
					<a class="cf-home-btn cf-home-btn--primary" href="<?php echo esc_url( $cf_about_url ); ?>">Learn More</a>
				</div>
			</div>
			<div class="cf-home-more-than-music__visual">
				<img
					class="cf-home-more-than-music__img"
					src="<?php echo esc_url( $cf_mtm_image_url ); ?>"
					alt=""
					width="640"
					height="480"
					loading="lazy"
					decoding="async"
				>
			</div>
		</div>
	</section>

	<!-- 6. LATEST ARTICLES -->
	<?php if ( $cf_recent_posts->have_posts() ) : ?>
		<section class="cf-home-section cf-home-latest-articles" aria-labelledby="cf-home-articles-heading" data-cf-home-reveal>
			<div class="cf-home-section-head">
				<h2 id="cf-home-articles-heading" class="cf-home-section-title">Latest Articles</h2>
				<p class="cf-home-section-desc">Insights, tutorials, and creative resources exploring AI music, production, storytelling, and the future of creativity.</p>
			</div>
			<div class="cf-home-article-grid">
				<?php
				while ( $cf_recent_posts->have_posts() ) :
					$cf_recent_posts->the_post();
					echo collective_finity_render_blog_card( get_post(), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>

	<!-- 7. JOIN THE COLLECTIVE -->
	<section class="cf-home-banner cf-home-join" aria-labelledby="cf-home-join-heading" data-cf-home-reveal style="--cf-home-banner-image: url('<?php echo esc_url( $cf_join_image_url ); ?>');">
		<div class="cf-home-banner__shade" aria-hidden="true"></div>
		<div class="cf-home-banner__content">
			<h2 id="cf-home-join-heading" class="cf-home-banner__title">Join the Collective</h2>
			<p class="cf-home-banner__body">Collective Finity is built for creators who believe technology should inspire imagination. Whether you're here to discover music, learn new skills, or shape the future of AI-assisted creativity, your journey begins here.</p>
			<div class="cf-home-banner__actions">
				<a class="cf-home-btn cf-home-btn--primary" href="<?php echo esc_url( $cf_community_url ); ?>">Join Community</a>
				<a class="cf-home-btn cf-home-btn--ghost" href="<?php echo esc_url( $cf_tracks_url ); ?>">Explore Music</a>
			</div>
		</div>
	</section>

	<!-- 8. THE FUTURE BEGINS -->
	<section class="cf-home-banner cf-home-future" aria-labelledby="cf-home-future-heading" data-cf-home-reveal style="--cf-home-banner-image: url('<?php echo esc_url( $cf_future_image_url ); ?>');">
		<div class="cf-home-banner__shade" aria-hidden="true"></div>
		<div class="cf-home-banner__content">
			<h2 id="cf-home-future-heading" class="cf-home-banner__title">The Future Begins With Creativity</h2>
			<p class="cf-home-banner__body">Artificial intelligence is changing how music is created—but technology alone has never inspired the world. The future belongs to creators who combine imagination, emotion, and innovation to build something meaningful. Collective Finity exists to help shape that future.</p>
		</div>
	</section>

	<!-- 9. VISION + NEWSLETTER / SOCIAL -->
	<section class="cf-home-section cf-home-vision-panel" aria-labelledby="cf-home-vision-heading" data-cf-home-reveal>
		<div class="cf-home-vision-panel__inner">
			<div class="cf-home-vision">
				<p class="cf-home-eyebrow cf-home-vision__label">OUR VISION</p>
				<h2 id="cf-home-vision-heading" class="cf-home-vision__title">The Future Isn't Automated. It's Collaborative.</h2>
				<p class="cf-home-vision__body">Artificial intelligence doesn't replace creativity. It expands what's possible.</p>
			</div>

			<div class="cf-home-vision-panel__divider" aria-hidden="true"></div>

			<div class="cf-home-inspire">
				<div class="cf-home-inspire__intro">
					<span class="cf-home-inspire__mail-icon" aria-hidden="true"><?php echo collective_finity_icon( 'mail', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<div class="cf-home-inspire__copy">
						<h3 class="cf-home-inspire__title">Stay Inspired</h3>
						<p class="cf-home-inspire__desc">Receive new music, articles, and creative insights.</p>
					</div>
				</div>

				<div class="cf-home-inspire__form-wrap">
					<?php
					$cf_newsletter_html = shortcode_exists( 'contact-form-7' )
						? trim( (string) do_shortcode( '[contact-form-7 id="a1d896d" title="Subscription Form"]' ) )
						: '';
					$cf_newsletter_ok = $cf_newsletter_html && false === strpos( $cf_newsletter_html, '[contact-form-7' );
					if ( $cf_newsletter_ok ) :
						echo $cf_newsletter_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					else :
						?>
						<form class="cf-home-inspire__form" action="#" method="post">
							<label class="screen-reader-text" for="cf-home-newsletter-email"><?php esc_html_e( 'Email address', 'collective-finity' ); ?></label>
							<input type="email" id="cf-home-newsletter-email" name="email" placeholder="<?php esc_attr_e( 'Enter your email', 'collective-finity' ); ?>" required autocomplete="email">
							<button type="submit" class="cf-home-btn cf-home-btn--primary">Subscribe</button>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

</main>

<style>
	.cf-home.cf-home-redesign {
		padding: 1.75rem 5px 1.5rem;
		display: flex;
		flex-direction: column;
		gap: clamp(36px, 4.5vw, 52px);
		max-width: 100%;
		min-width: 0;
		box-sizing: border-box;
	}

	@property --cf-home-border-angle {
		syntax: '<angle>';
		initial-value: 0deg;
		inherits: false;
	}

	/* ---- Shared type / buttons ---- */
	.cf-home-eyebrow {
		margin: 0;
		font-family: var(--cf-mono, 'Space Mono', monospace);
		font-size: 11px;
		letter-spacing: 0.12em;
		text-transform: uppercase;
		color: var(--cf-accent, #FFB700);
	}

	.cf-home-section-title,
	.cf-home-featured-release__title,
	.cf-home-banner__title {
		margin: 0;
		font-weight: 700;
		color: #fff;
		letter-spacing: -0.01em;
		line-height: 1.2;
	}

	.cf-home-section-title {
		font-size: clamp(22px, 3vw, 28px);
	}

	.cf-home-prose,
	.cf-home-section-desc,
	.cf-home-hero__lead,
	.cf-home-banner__body {
		margin: 0;
		font-size: 14.5px;
		line-height: 1.85;
		color: #B3B3B3;
		max-width: 38em;
	}

	.cf-home-section-desc {
		margin-top: 10px;
		color: #9a9a9a;
	}

	.cf-home-btn {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		gap: 8px;
		padding: 12px 22px;
		border-radius: 10px;
		font-size: 13.5px;
		font-weight: 600;
		line-height: 1.2;
		text-decoration: none;
		white-space: nowrap;
		cursor: pointer;
		border: none;
		font-family: inherit;
		transition: background 0.22s ease, color 0.22s ease, border-color 0.22s ease, transform 0.22s ease, box-shadow 0.22s ease;
	}

	.cf-home-btn--primary {
		background: var(--cf-accent, #FFB700);
		color: #0D0D0D;
		font-weight: 700;
		box-shadow: 0 8px 20px -10px rgba(255, 183, 0, 0.45);
	}

	.cf-home-btn--primary:hover,
	.cf-home-btn--primary:focus-visible {
		background: #ffc633;
		color: #0D0D0D;
		transform: translateY(-2px);
		box-shadow: 0 14px 28px -10px rgba(255, 183, 0, 0.5);
	}

	.cf-home-btn--ghost {
		border: 1px solid rgba(255, 255, 255, 0.45);
		background: transparent;
		color: #fff;
	}

	.cf-home-btn--ghost:hover,
	.cf-home-btn--ghost:focus-visible {
		background: rgba(255, 255, 255, 0.06);
		border-color: rgba(255, 255, 255, 0.8);
		color: #fff;
		transform: translateY(-2px);
	}

	.cf-home-section-head__row {
		display: flex;
		align-items: baseline;
		justify-content: space-between;
		gap: 16px;
		flex-wrap: wrap;
	}

	.cf-home-section-link {
		font-size: 13px;
		font-weight: 600;
		color: var(--cf-accent, #FFB700);
		text-decoration: none;
		white-space: nowrap;
		transition: color 0.2s ease;
	}

	.cf-home-section-link:hover {
		color: #ffde99;
	}

	.cf-home-empty {
		padding: 40px;
		border: 1px dashed var(--cf-border, #232323);
		border-radius: 14px;
		text-align: center;
		color: var(--cf-text-3, #888);
		font-size: 14px;
	}

	/* ---- Hero ---- */
	.cf-home-hero {
		position: relative;
		overflow: hidden;
		min-height: clamp(420px, 52vw, 540px);
		border-radius: 18px;
		border: 1px solid rgba(255, 255, 255, 0.06);
		background: #0B0B0B;
		box-sizing: border-box;
		box-shadow: 0 24px 64px -36px rgba(0, 0, 0, 0.7);
	}

	.cf-home-hero__border {
		position: absolute;
		inset: 0;
		border-radius: inherit;
		padding: 1.5px;
		pointer-events: none;
		z-index: 4;
		background: conic-gradient(
			from var(--cf-home-border-angle),
			transparent 0%,
			transparent 72%,
			rgba(255, 183, 0, 0.05) 80%,
			rgba(255, 183, 0, 0.35) 86%,
			var(--cf-accent, #FFB700) 90%,
			#FFD060 93%,
			rgba(255, 183, 0, 0.2) 96%,
			transparent 100%
		);
		-webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
		-webkit-mask-composite: xor;
		mask-composite: exclude;
		animation: cfHomeBorderTravel 5.5s linear infinite;
		filter: drop-shadow(0 0 8px rgba(255, 183, 0, 0.28));
	}

	@keyframes cfHomeBorderTravel {
		to { --cf-home-border-angle: 360deg; }
	}

	.cf-home-hero__media {
		position: absolute;
		inset: 0;
		z-index: 0;
		background-image: var(--cf-home-hero-image);
		background-size: cover;
		background-position: center right;
		background-repeat: no-repeat;
	}

	.cf-home-hero__shade {
		position: absolute;
		inset: 0;
		z-index: 1;
		background:
			linear-gradient(90deg, rgba(8, 8, 8, 0.92) 0%, rgba(8, 8, 8, 0.78) 38%, rgba(8, 8, 8, 0.28) 64%, rgba(8, 8, 8, 0.08) 100%),
			linear-gradient(180deg, rgba(8, 8, 8, 0.12) 0%, transparent 30%, rgba(8, 8, 8, 0.35) 100%);
		pointer-events: none;
	}

	.cf-home-hero__copy {
		position: relative;
		z-index: 3;
		display: flex;
		flex-direction: column;
		align-items: flex-start;
		text-align: left;
		gap: 16px;
		max-width: 560px;
		padding: clamp(48px, 6.5vw, 76px) clamp(24px, 4.5vw, 56px) clamp(52px, 6.5vw, 80px);
	}

	.cf-home-hero__title {
		margin: 0;
		font-size: clamp(26px, 3.8vw, 40px);
		font-weight: 700;
		color: #fff;
		line-height: 1.18;
		letter-spacing: -0.015em;
		max-width: 14.5em;
	}

	.cf-home-hero__lead {
		max-width: 34em;
		color: #D0D0D0;
	}

	.cf-home-hero__actions {
		display: flex;
		flex-wrap: wrap;
		justify-content: flex-start;
		gap: 12px;
		margin-top: 8px;
	}

	/* ---- Hero search (spacing polish only) ---- */
	.cf-home-redesign .cf-hero-search {
		position: relative;
		width: min(100%, 480px);
		z-index: 5;
		margin: 6px 0 2px;
		align-self: stretch;
	}

	.cf-home-redesign .cf-hero-search-field {
		position: relative;
		display: flex;
		align-items: center;
	}

	.cf-home-redesign .cf-hero-search-icon {
		position: absolute;
		left: 16px;
		top: 50%;
		transform: translateY(-50%);
		color: rgba(255, 255, 255, 0.4);
		display: flex;
		pointer-events: none;
	}

	.cf-home-redesign .cf-hero-search-input {
		width: 100%;
		padding: 15px 18px 15px 46px;
		border-radius: 999px;
		border: 1px solid rgba(255, 255, 255, 0.12);
		background: rgba(255, 255, 255, 0.05);
		color: #fff;
		font-size: 14px;
		outline: none;
		transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
	}

	.cf-home-redesign .cf-hero-search-input::placeholder {
		color: rgba(255, 255, 255, 0.38);
	}

	.cf-home-redesign .cf-hero-search-input:focus {
		border-color: rgba(255, 183, 0, 0.55);
		background: rgba(255, 183, 0, 0.06);
		box-shadow: 0 0 24px rgba(255, 183, 0, 0.14);
	}

	.cf-home-redesign .cf-hero-search-results {
		position: absolute;
		left: 0;
		right: 0;
		top: calc(100% + 8px);
		max-height: 320px;
		overflow-y: auto;
		margin: 0;
		padding: 8px;
		border-radius: 14px;
		border: 1px solid rgba(255, 183, 0, 0.22);
		background: rgba(14, 14, 14, 0.96);
		box-shadow: 0 18px 40px -16px rgba(0, 0, 0, 0.7);
		text-align: left;
		z-index: 6;
	}

	.cf-home-redesign .cf-hero-search-results[hidden] {
		display: none;
	}

	.cf-home-redesign .cf-hero-search-item {
		display: flex;
		align-items: flex-start;
		gap: 12px;
		padding: 10px 12px;
		border-radius: 10px;
		text-decoration: none;
		color: #fff;
		transition: background 0.15s ease;
	}

	.cf-home-redesign .cf-hero-search-item:hover,
	.cf-home-redesign .cf-hero-search-item:focus-visible,
	.cf-home-redesign .cf-hero-search-item.is-active {
		background: rgba(255, 183, 0, 0.1);
		outline: none;
	}

	.cf-home-redesign .cf-hero-search-type {
		flex: 0 0 auto;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		min-width: 64px;
		padding: 4px 8px;
		margin-top: 2px;
		border-radius: 999px;
		font-family: var(--cf-mono, 'Space Mono', monospace);
		font-size: 9.5px;
		font-weight: 700;
		letter-spacing: 0.05em;
		text-transform: uppercase;
	}

	.cf-home-redesign .cf-hero-search-type--track {
		background: rgba(255, 183, 0, 0.12);
		color: #ffb700;
		border: 1px solid rgba(255, 183, 0, 0.28);
	}

	.cf-home-redesign .cf-hero-search-type--article {
		background: rgba(255, 255, 255, 0.06);
		color: #e4e4e4;
		border: 1px solid rgba(255, 255, 255, 0.14);
	}

	.cf-home-redesign .cf-hero-search-body {
		min-width: 0;
		flex: 1;
	}

	.cf-home-redesign .cf-hero-search-title {
		font-size: 13.5px;
		font-weight: 600;
		line-height: 1.35;
		margin: 0 0 4px;
	}

	.cf-home-redesign .cf-hero-search-excerpt {
		margin: 0;
		font-size: 12px;
		line-height: 1.45;
		color: var(--cf-text-3, #888);
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}

	.cf-home-redesign .cf-hero-search-empty {
		padding: 16px 12px;
		font-size: 13px;
		color: var(--cf-text-3, #888);
		text-align: center;
	}

	/* ---- Featured Release ---- */
	.cf-home-featured-release__panel {
		border: 1px solid rgba(255, 255, 255, 0.07);
		border-radius: 16px;
		background: rgba(18, 18, 18, 0.96);
		padding: clamp(16px, 2.2vw, 24px);
		box-shadow: 0 18px 40px -28px rgba(0, 0, 0, 0.85);
	}

	.cf-home-featured-release__grid {
		display: grid;
		gap: 20px;
		align-items: center;
	}

	.cf-home-featured-release__art {
		position: relative;
		border-radius: 14px;
		overflow: hidden;
		border: 1px solid rgba(255, 255, 255, 0.08);
		box-shadow: 0 28px 56px -28px rgba(0, 0, 0, 0.85);
		aspect-ratio: 1;
		max-width: min(100%, 280px);
		background: #0c0c0c;
		justify-self: start;
	}

	.cf-home-featured-release__art img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		display: block;
		transition: transform 0.25s ease;
	}

	.cf-home-featured-release__art:hover img {
		transform: scale(1.03);
	}

	.cf-home-featured-release__content {
		display: flex;
		flex-direction: column;
		gap: 10px;
		min-width: 0;
	}

	.cf-home-featured-release__title {
		font-size: clamp(24px, 3vw, 32px);
	}

	.cf-home-featured-release__artist {
		margin: 0;
		font-size: 13px;
		font-weight: 600;
		color: var(--cf-accent, #FFB700);
	}

	.cf-home-featured-release__content .cf-home-prose {
		max-width: 34em;
	}

	.cf-home-featured-release__actions {
		display: flex;
		flex-wrap: wrap;
		gap: 12px;
		margin-top: 4px;
	}

	.cf-home-featured-release__meta {
		list-style: none;
		margin: 0;
		padding: 14px 16px;
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 10px 16px;
		border-radius: 12px;
		border: 1px solid rgba(255, 255, 255, 0.06);
		background: rgba(255, 255, 255, 0.025);
		box-shadow: inset 0 0 0 1px rgba(255, 183, 0, 0.04);
		min-width: 0;
	}

	.cf-home-featured-release__meta li {
		display: flex;
		align-items: center;
		gap: 8px;
		font-size: 12.5px;
		color: #B0B0B0;
		line-height: 1.3;
		min-width: 0;
	}

	.cf-home-featured-release__meta li > span:last-child {
		min-width: 0;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.cf-home-featured-release__meta-item--year {
		grid-column: 1 / -1;
		border-top: 1px solid rgba(255, 255, 255, 0.06);
		margin-top: 2px;
		padding-top: 10px;
	}

	.cf-home-featured-release__meta-icon {
		display: inline-flex;
		width: 16px;
		height: 16px;
		flex-shrink: 0;
		color: rgba(255, 183, 0, 0.55);
	}

	.cf-home-featured-release__meta-icon svg {
		width: 100%;
		height: 100%;
	}

	@media (min-width: 900px) {
		.cf-home-featured-release__grid {
			grid-template-columns: minmax(200px, 260px) minmax(0, 1fr) minmax(210px, 250px);
			gap: 18px 20px;
			align-items: center;
		}

		.cf-home-featured-release__art {
			max-width: none;
			width: 100%;
		}

		.cf-home-featured-release__meta {
			justify-self: stretch;
			align-self: center;
			height: fit-content;
		}
	}

	/* ---- Card grids (reuse library card markup) ---- */
	.cf-home-card-grid {
		display: grid;
		grid-template-columns: repeat(4, minmax(0, 1fr));
		gap: 18px;
		margin-top: 20px;
	}

	.cf-home-redesign .cf-card {
		display: block;
		text-decoration: none;
		color: inherit;
		background: var(--cf-bg-card, #141414);
		border: var(--cf-card-border-width, 1px) solid var(--cf-border, #232323);
		border-radius: var(--cf-card-radius, 12px);
		box-shadow: var(--cf-card-shadow, 0 14px 28px -12px rgba(0, 0, 0, 0.55));
		overflow: hidden;
		padding: 0 0 12px;
		transition: border-color 0.22s ease, transform 0.22s ease, box-shadow 0.22s ease;
	}

	.cf-home-redesign .cf-card-primary {
		display: block;
		text-decoration: none;
		color: inherit;
	}

	.cf-home-redesign .cf-cover {
		position: relative;
		width: 100%;
		aspect-ratio: 1;
		overflow: hidden;
		background: #0c0c0c;
		margin-bottom: 10px;
	}

	.cf-home-redesign .cf-cover img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		display: block;
		transition: transform 0.25s ease;
	}

	.cf-home-redesign .cf-play-btn {
		position: absolute;
		right: 8px;
		bottom: 8px;
		width: 40px;
		height: 40px;
		border: none;
		border-radius: 50%;
		background: var(--cf-accent, #FFB700);
		display: flex;
		align-items: center;
		justify-content: center;
		opacity: 0;
		transform: translateY(4px);
		transition: opacity 0.22s ease, transform 0.22s ease;
		cursor: pointer;
		box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
	}

	.cf-home-redesign .cf-play-btn .dashicons {
		color: #1a1400;
		font-size: 18px;
		width: 18px;
		height: 18px;
	}

	.cf-home-redesign .cf-heart-btn.cf-interaction-btn {
		position: absolute;
		left: 8px;
		top: 8px;
		width: 28px;
		height: 28px;
		border: none;
		border-radius: 50%;
		background: rgba(0, 0, 0, 0.55);
		display: flex;
		align-items: center;
		justify-content: center;
		opacity: 0;
		transition: opacity 0.22s ease;
		cursor: pointer;
		padding: 0;
	}

	.cf-home-redesign .cf-heart-btn .dashicons {
		color: #fff;
		font-size: 14px;
		width: 14px;
		height: 14px;
	}

	.cf-home-redesign .cf-heart-btn.cf-interaction-btn.active {
		opacity: 1;
	}

	.cf-home-redesign .cf-heart-btn.cf-interaction-btn.active .dashicons {
		color: var(--cf-accent, #FFB700);
	}

	.cf-home-redesign .cf-card-title {
		font-size: 14px;
		font-weight: 600;
		color: #fff;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		padding: 0 12px;
		transition: color 0.22s ease;
	}

	.cf-home-redesign .cf-card-sub {
		font-size: 12px;
		color: #7A7A7A;
		margin-top: 3px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		padding: 0 12px;
		transition: color 0.22s ease;
	}

	.cf-home-redesign .cf-artist-link {
		color: inherit;
		text-decoration: none;
		transition: color 0.22s ease;
	}

	.cf-home-redesign .cf-artist-link:hover {
		color: var(--cf-accent, #FFB700);
		text-decoration: underline;
	}

	.cf-home-redesign .cf-card-chip {
		display: inline-block;
		margin: 6px 12px 0;
		font-size: 10px;
		color: #B3B3B3;
		background: rgba(255, 255, 255, 0.04);
		border: 1px solid var(--cf-border, #232323);
		padding: 2px 8px;
		border-radius: 10px;
		transition: border-color 0.22s ease, color 0.22s ease;
	}

	.cf-home-redesign .cf-card:not(.cf-card--ad):is(:hover, :focus-visible, :focus-within) {
		transform: translateY(-3px);
		border-color: rgba(255, 183, 0, 0.35);
		box-shadow: 0 16px 32px -14px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 183, 0, 0.12);
	}

	.cf-home-redesign .cf-card:not(.cf-card--ad):is(:hover, :focus-visible, :focus-within) .cf-cover img {
		transform: scale(1.03);
	}

	.cf-home-redesign .cf-card:not(.cf-card--ad):is(:hover, :focus-visible, :focus-within) .cf-play-btn {
		opacity: 1;
		transform: translateY(0);
	}

	.cf-home-redesign .cf-card:not(.cf-card--ad):is(:hover, :focus-visible, :focus-within) .cf-heart-btn.cf-interaction-btn {
		opacity: 1;
	}

	.cf-home-redesign .cf-card:not(.cf-card--ad):is(:hover, :focus-visible, :focus-within) .cf-card-title {
		color: var(--cf-accent, #FFB700);
	}

	.cf-home-redesign .cf-card:not(.cf-card--ad):is(:hover, :focus-visible, :focus-within) .cf-card-sub {
		color: #a8a8a8;
	}

	/* ---- Featured Tracks (compact list) ---- */
	.cf-home-track-list {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		grid-template-rows: auto auto;
		grid-auto-flow: column;
		gap: 12px 20px;
		margin-top: 22px;
	}

	.cf-home-track-row {
		display: grid;
		grid-template-columns: 36px 44px minmax(0, 1fr) auto auto auto;
		align-items: center;
		gap: 12px;
		padding: 10px 12px;
		border-radius: 12px;
		border: 1px solid rgba(255, 255, 255, 0.06);
		background: rgba(20, 20, 20, 0.92);
		transition: border-color 0.22s ease, background 0.22s ease, transform 0.22s ease;
	}

	.cf-home-track-row:hover {
		border-color: rgba(255, 183, 0, 0.28);
		background: rgba(24, 24, 24, 0.98);
		transform: translateY(-1px);
	}

	.cf-home-track-row__play {
		width: 34px;
		height: 34px;
		border-radius: 50%;
		border: 1px solid rgba(255, 255, 255, 0.18);
		background: rgba(255, 255, 255, 0.04);
		color: #fff;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		padding: 0;
		transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
	}

	.cf-home-track-row__play:hover,
	.cf-home-track-row__play:focus-visible {
		background: var(--cf-accent, #FFB700);
		border-color: var(--cf-accent, #FFB700);
		color: #0D0D0D;
		outline: none;
	}

	.cf-home-track-row__play .dashicons {
		font-size: 16px;
		width: 16px;
		height: 16px;
		margin-left: 1px;
	}

	.cf-home-track-row__thumb {
		width: 44px;
		height: 44px;
		border-radius: 8px;
		overflow: hidden;
		flex-shrink: 0;
		background: #0c0c0c;
	}

	.cf-home-track-row__thumb img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		display: block;
	}

	.cf-home-track-row__info {
		min-width: 0;
		display: flex;
		flex-direction: column;
		gap: 2px;
		text-decoration: none;
		color: inherit;
	}

	.cf-home-track-row__title {
		font-size: 13.5px;
		font-weight: 600;
		color: #fff;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		transition: color 0.2s ease;
	}

	.cf-home-track-row__artist {
		font-size: 12px;
		color: #7A7A7A;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.cf-home-track-row:hover .cf-home-track-row__title {
		color: var(--cf-accent, #FFB700);
	}

	.cf-home-track-row__duration {
		font-family: var(--cf-mono, 'Space Mono', monospace);
		font-size: 11.5px;
		color: #8A8A8A;
		white-space: nowrap;
	}

	.cf-home-track-row__icon {
		width: 30px;
		height: 30px;
		border: none;
		background: transparent;
		color: #8A8A8A;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		padding: 0;
		text-decoration: none;
		border-radius: 8px;
		transition: color 0.2s ease, background 0.2s ease;
	}

	.cf-home-track-row__icon:hover,
	.cf-home-track-row__icon:focus-visible {
		color: var(--cf-accent, #FFB700);
		background: rgba(255, 183, 0, 0.08);
		outline: none;
	}

	.cf-home-track-row__icon .dashicons {
		font-size: 16px;
		width: 16px;
		height: 16px;
	}

	.cf-home-track-row__icon.cf-heart-btn.active,
	.cf-home-track-row__icon.cf-heart-btn.active .dashicons {
		color: var(--cf-accent, #FFB700);
	}

	/* ---- More Than Music ---- */
	.cf-home-more-than-music__grid {
		display: grid;
		gap: 36px;
		align-items: center;
	}

	.cf-home-more-than-music__copy {
		display: flex;
		flex-direction: column;
		gap: 18px;
	}

	.cf-home-more-than-music__actions {
		margin-top: 4px;
	}

	.cf-home-more-than-music__visual {
		position: relative;
		min-height: 240px;
		border-radius: 16px;
		border: 1px solid rgba(255, 255, 255, 0.06);
		overflow: hidden;
		background: #0c0c0c;
	}

	.cf-home-more-than-music__img {
		width: 100%;
		height: 100%;
		min-height: 240px;
		object-fit: cover;
		object-position: center;
		display: block;
		transition: transform 0.35s ease, filter 0.35s ease;
	}

	.cf-home-more-than-music__visual:hover .cf-home-more-than-music__img {
		transform: scale(1.025);
		filter: brightness(1.04);
	}

	@media (min-width: 900px) {
		.cf-home-more-than-music__grid {
			grid-template-columns: minmax(0, 1.25fr) minmax(220px, 0.75fr);
			gap: 44px;
		}

		.cf-home-more-than-music__visual,
		.cf-home-more-than-music__img {
			min-height: 280px;
			height: 100%;
		}
	}

	/* ---- Articles ---- */
	.cf-home-article-grid {
		display: grid;
		grid-template-columns: repeat(4, minmax(0, 1fr));
		gap: 18px;
		margin-top: 20px;
	}

	.cf-home-redesign .cf-bh-card {
		transition: border-color 0.22s ease, transform 0.22s ease, box-shadow 0.22s ease;
	}

	.cf-home-redesign .cf-bh-card:hover {
		transform: translateY(-3px);
		border-color: rgba(255, 183, 0, 0.3);
		box-shadow: 0 16px 32px -14px rgba(0, 0, 0, 0.65);
	}

	.cf-home-redesign .cf-bh-card:hover .cf-bh-card-title {
		color: var(--cf-accent, #FFB700);
	}

	.cf-home-redesign .cf-bh-card-art {
		overflow: hidden;
	}

	.cf-home-redesign .cf-bh-card-art img {
		transition: transform 0.25s ease;
	}

	.cf-home-redesign .cf-bh-card:hover .cf-bh-card-art img {
		transform: scale(1.03);
	}

	.cf-home-redesign .cf-bh-card-title {
		transition: color 0.22s ease;
	}

	/* ---- Banner sections ---- */
	.cf-home-banner {
		position: relative;
		overflow: hidden;
		border-radius: 18px;
		border: 1px solid rgba(255, 255, 255, 0.07);
		background-color: #0f0f0f;
		background-image: var(--cf-home-banner-image);
		background-size: cover;
		background-position: center;
		box-shadow: 0 18px 40px -28px rgba(0, 0, 0, 0.85);
		min-height: clamp(280px, 36vw, 360px);
	}

	.cf-home-banner__shade {
		position: absolute;
		inset: 0;
		background: linear-gradient(180deg, rgba(8, 8, 8, 0.55) 0%, rgba(8, 8, 8, 0.78) 55%, rgba(8, 8, 8, 0.9) 100%);
		pointer-events: none;
	}

	.cf-home-banner__content {
		position: relative;
		z-index: 1;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		gap: 18px;
		text-align: center;
		padding: clamp(40px, 6vw, 64px) clamp(24px, 4vw, 48px);
		max-width: 720px;
		margin: 0 auto;
	}

	.cf-home-banner__title {
		font-size: clamp(24px, 3vw, 32px);
	}

	.cf-home-banner__body {
		max-width: 36em;
		color: #D0D0D0;
	}

	.cf-home-banner__actions {
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		gap: 12px;
		margin-top: 4px;
	}

	.cf-home-future {
		min-height: clamp(240px, 30vw, 300px);
	}

	/* ---- Vision + Newsletter ---- */
	.cf-home-vision-panel__inner {
		border: 1px solid rgba(255, 255, 255, 0.08);
		border-radius: 16px;
		background:
			radial-gradient(ellipse 70% 55% at 50% 0%, rgba(255, 183, 0, 0.06) 0%, transparent 60%),
			rgba(14, 14, 14, 0.98);
		padding: clamp(18px, 2.5vw, 26px) clamp(18px, 3vw, 32px);
	}

	.cf-home-vision {
		display: flex;
		flex-direction: column;
		align-items: center;
		text-align: center;
		gap: 12px;
		max-width: 640px;
		margin: 0 auto;
	}

	.cf-home-vision__label {
		margin-bottom: 0;
	}

	.cf-home-vision__title {
		margin: 0;
		font-size: clamp(22px, 3vw, 30px);
		font-weight: 700;
		color: #fff;
		line-height: 1.25;
		letter-spacing: -0.01em;
	}

	.cf-home-vision__body {
		margin: 0;
		font-size: 14.5px;
		line-height: 1.7;
		color: #A8A8A8;
		max-width: 34em;
	}

	.cf-home-vision-panel__divider {
		height: 1px;
		margin: 14px 0;
		background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.12), transparent);
	}

	.cf-home-inspire {
		display: grid;
		gap: 12px;
		align-items: center;
	}

	.cf-home-inspire__intro {
		display: flex;
		align-items: flex-start;
		gap: 12px;
		min-width: 0;
	}

	.cf-home-inspire__mail-icon {
		display: inline-flex;
		color: var(--cf-accent, #FFB700);
		margin-top: 2px;
		flex-shrink: 0;
	}

	.cf-home-inspire__title {
		margin: 0 0 4px;
		font-size: 16px;
		font-weight: 700;
		color: var(--cf-accent, #FFB700);
	}

	.cf-home-inspire__desc {
		margin: 0;
		font-size: 13.5px;
		line-height: 1.55;
		color: #A8A8A8;
	}

	.cf-home-inspire__form-wrap {
		min-width: 0;
		width: 100%;
	}

	.cf-home-inspire__form,
	.cf-home-inspire__form-wrap .wpcf7-form {
		display: flex;
		align-items: stretch;
		flex-direction: row;
		flex-wrap: nowrap;
		gap: 0;
		width: 100%;
		max-width: 480px;
		margin-left: auto;
	}

	.cf-home-inspire__form input[type="email"],
	.cf-home-inspire__form-wrap .wpcf7-form input[type="email"],
	.cf-home-inspire__form-wrap .wpcf7-form input[type="text"] {
		flex: 1 1 auto;
		min-width: 0;
		width: 100%;
		height: 44px;
		padding: 0 14px;
		border: 1px solid rgba(255, 255, 255, 0.12);
		border-right: none;
		border-radius: 10px 0 0 10px;
		background: rgba(255, 255, 255, 0.04);
		color: #fff;
		font-size: 13.5px;
		font-family: inherit;
		outline: none;
		box-sizing: border-box;
	}

	.cf-home-inspire__form input[type="email"]:focus,
	.cf-home-inspire__form-wrap .wpcf7-form input[type="email"]:focus,
	.cf-home-inspire__form-wrap .wpcf7-form input[type="text"]:focus {
		border-color: rgba(255, 183, 0, 0.45);
	}

	.cf-home-inspire__form button,
	.cf-home-inspire__form-wrap .wpcf7-form input[type="submit"],
	.cf-home-inspire__form-wrap .wpcf7-form button {
		flex: 0 0 auto;
		border: none;
		border-radius: 0 10px 10px 0;
		height: 44px;
		padding: 0 20px;
		background: var(--cf-accent, #FFB700);
		color: #0D0D0D;
		font-weight: 700;
		font-size: 13.5px;
		cursor: pointer;
		white-space: nowrap;
		font-family: inherit;
		margin: 0;
		box-shadow: none;
		transform: none;
	}

	.cf-home-inspire__form button:hover,
	.cf-home-inspire__form-wrap .wpcf7-form input[type="submit"]:hover,
	.cf-home-inspire__form-wrap .wpcf7-form button:hover {
		background: #ffc633;
		transform: none;
		box-shadow: none;
	}

	.cf-home-inspire__form-wrap .wpcf7-form > p {
		margin: 0;
		display: flex;
		flex: 1 1 auto;
		min-width: 0;
		width: auto;
		align-items: stretch;
	}

	.cf-home-inspire__form-wrap .wpcf7-form > p:has(input[type="submit"]),
	.cf-home-inspire__form-wrap .wpcf7-form > p:has(button) {
		flex: 0 0 auto;
	}

	.cf-home-inspire__form-wrap .wpcf7-form .wpcf7-form-control-wrap {
		flex: 1;
		min-width: 0;
		display: block;
	}

	.cf-home-inspire__form-wrap .wpcf7-form .wpcf7-spinner,
	.cf-home-inspire__form-wrap .wpcf7-form .wpcf7-response-output {
		display: none;
	}

	@media (min-width: 900px) {
		.cf-home-inspire {
			grid-template-columns: minmax(220px, 0.9fr) minmax(280px, 1.1fr);
			gap: 20px;
			align-items: center;
		}
	}

	/* About-matching gold footer divider (homepage only) */
	body.home .cf-site-footer,
	body.front-page .cf-site-footer {
		position: relative;
		margin-top: 12px;
		border-top-color: rgba(255, 183, 0, 0.1);
		box-shadow: 0 -28px 48px -36px rgba(255, 183, 0, 0.07);
	}

	body.home .cf-site-footer::before,
	body.front-page .cf-site-footer::before {
		content: '';
		position: absolute;
		top: 0;
		left: 8%;
		right: 8%;
		height: 1px;
		background: linear-gradient(90deg, transparent, rgba(255, 183, 0, 0.22), transparent);
		pointer-events: none;
	}

	/* ---- Reveal ---- */
	[data-cf-home-reveal] {
		opacity: 0;
		transform: translateY(16px);
		transition: opacity 0.6s ease, transform 0.6s ease;
		will-change: opacity, transform;
	}

	[data-cf-home-reveal].is-visible {
		opacity: 1;
		transform: none;
		will-change: auto;
	}

	[data-cf-home-reveal].is-visible .cf-card,
	[data-cf-home-reveal].is-visible .cf-bh-card,
	[data-cf-home-reveal].is-visible .cf-home-featured-release__art,
	[data-cf-home-reveal].is-visible .cf-home-featured-release__content > *,
	[data-cf-home-reveal].is-visible .cf-home-more-than-music__copy > *,
	[data-cf-home-reveal].is-visible .cf-home-banner__content > * {
		animation: cfHomeFadeUp 0.6s ease both;
	}

	[data-cf-home-reveal].is-visible .cf-card:nth-child(1),
	[data-cf-home-reveal].is-visible .cf-bh-card:nth-child(1),
	[data-cf-home-reveal].is-visible .cf-home-featured-release__content > *:nth-child(1),
	[data-cf-home-reveal].is-visible .cf-home-more-than-music__copy > *:nth-child(1),
	[data-cf-home-reveal].is-visible .cf-home-banner__content > *:nth-child(1) { animation-delay: 0.08s; }
	[data-cf-home-reveal].is-visible .cf-card:nth-child(2),
	[data-cf-home-reveal].is-visible .cf-bh-card:nth-child(2),
	[data-cf-home-reveal].is-visible .cf-home-featured-release__content > *:nth-child(2),
	[data-cf-home-reveal].is-visible .cf-home-more-than-music__copy > *:nth-child(2),
	[data-cf-home-reveal].is-visible .cf-home-banner__content > *:nth-child(2) { animation-delay: 0.16s; }
	[data-cf-home-reveal].is-visible .cf-card:nth-child(3),
	[data-cf-home-reveal].is-visible .cf-bh-card:nth-child(3),
	[data-cf-home-reveal].is-visible .cf-home-featured-release__content > *:nth-child(3),
	[data-cf-home-reveal].is-visible .cf-home-more-than-music__copy > *:nth-child(3),
	[data-cf-home-reveal].is-visible .cf-home-banner__content > *:nth-child(3) { animation-delay: 0.24s; }
	[data-cf-home-reveal].is-visible .cf-card:nth-child(4),
	[data-cf-home-reveal].is-visible .cf-bh-card:nth-child(4),
	[data-cf-home-reveal].is-visible .cf-home-featured-release__content > *:nth-child(4),
	[data-cf-home-reveal].is-visible .cf-home-more-than-music__copy > *:nth-child(4),
	[data-cf-home-reveal].is-visible .cf-home-banner__content > *:nth-child(4) { animation-delay: 0.32s; }
	[data-cf-home-reveal].is-visible .cf-card:nth-child(5),
	[data-cf-home-reveal].is-visible .cf-card:nth-child(6),
	[data-cf-home-reveal].is-visible .cf-card:nth-child(7),
	[data-cf-home-reveal].is-visible .cf-card:nth-child(8) { animation-delay: 0.4s; }

	@keyframes cfHomeFadeUp {
		from { opacity: 0; transform: translateY(16px); }
		to { opacity: 1; transform: translateY(0); }
	}

	/* ---- Responsive ---- */
	@media (max-width: 1100px) {
		.cf-home-card-grid,
		.cf-home-article-grid {
			grid-template-columns: repeat(3, minmax(0, 1fr));
		}
	}

	@media (max-width: 767px) {
		.cf-home.cf-home-redesign {
			gap: 32px;
			padding: 1.25rem 5px 1.25rem;
		}

		.cf-home-hero {
			min-height: 0;
		}

		.cf-home-hero__title {
			max-width: none;
		}

		.cf-home-hero__copy {
			padding: clamp(36px, 8vw, 52px) clamp(18px, 5vw, 28px) clamp(40px, 8vw, 56px);
			gap: 14px;
			max-width: none;
		}

		.cf-home-hero__media {
			background-position: center;
		}

		.cf-home-hero__shade {
			background:
				linear-gradient(180deg, rgba(8, 8, 8, 0.72) 0%, rgba(8, 8, 8, 0.82) 45%, rgba(8, 8, 8, 0.9) 100%),
				linear-gradient(90deg, rgba(8, 8, 8, 0.55), rgba(8, 8, 8, 0.25));
		}

		.cf-home-card-grid,
		.cf-home-article-grid {
			grid-template-columns: repeat(2, minmax(0, 1fr));
			gap: 14px;
			margin-top: 18px;
		}

		.cf-home-track-list {
			grid-template-columns: 1fr;
			grid-template-rows: none;
			grid-auto-flow: row;
			gap: 10px;
			margin-top: 18px;
		}

		.cf-home-track-row {
			grid-template-columns: 32px 40px minmax(0, 1fr) auto auto auto;
			gap: 8px;
			padding: 8px 10px;
		}

		.cf-home-redesign .cf-play-btn {
			opacity: 1;
			transform: none;
			width: 32px;
			height: 32px;
		}

		.cf-home-redesign .cf-heart-btn.cf-interaction-btn {
			opacity: 1;
		}

		.cf-home-featured-release__content {
			max-width: none;
		}

		.cf-home-more-than-music__visual {
			min-height: 180px;
		}

		.cf-home-inspire__form,
		.cf-home-inspire__form-wrap .wpcf7-form {
			max-width: none;
			margin-left: 0;
		}
	}

	@media (max-width: 480px) {
		.cf-home-card-grid--albums {
			grid-template-columns: repeat(2, minmax(0, 1fr));
		}

		.cf-home-article-grid {
			grid-template-columns: 1fr;
		}
	}

	@media (prefers-reduced-motion: reduce) {
		.cf-home-hero__border,
		[data-cf-home-reveal],
		[data-cf-home-reveal].is-visible .cf-card,
		[data-cf-home-reveal].is-visible .cf-bh-card,
		[data-cf-home-reveal].is-visible .cf-home-featured-release__art,
		[data-cf-home-reveal].is-visible .cf-home-featured-release__content > *,
		[data-cf-home-reveal].is-visible .cf-home-more-than-music__copy > *,
		[data-cf-home-reveal].is-visible .cf-home-banner__content > * {
			animation: none !important;
			transition: none !important;
		}

		[data-cf-home-reveal] {
			opacity: 1;
			transform: none;
		}

		.cf-home-btn:hover,
		.cf-home-btn:focus-visible,
		.cf-home-redesign .cf-card:hover,
		.cf-home-more-than-music__visual:hover .cf-home-more-than-music__img {
			transform: none;
			filter: none;
		}
	}

	body.cf-glow-disabled .cf-home-hero__border {
		display: none !important;
	}
</style>

<script>
(function () {
	var root = document.querySelector('[data-cf-hero-search]');
	var input = document.getElementById('cf-hero-search-input');
	var results = document.getElementById('cf-hero-search-results');
	var dataEl = document.getElementById('cf-hero-search-data');
	if (!root || !input || !results || !dataEl) {
		return;
	}

	var items = [];
	try {
		items = JSON.parse(dataEl.textContent || '[]');
	} catch (e) {
		items = [];
	}
	if (!Array.isArray(items)) {
		items = [];
	}

	var labels = {
		track: <?php echo wp_json_encode( __( 'Track', 'collective-finity' ) ); ?>,
		article: <?php echo wp_json_encode( __( 'Article', 'collective-finity' ) ); ?>,
		empty: <?php echo wp_json_encode( __( 'No results found', 'collective-finity' ) ); ?>
	};

	var activeIndex = -1;
	var debounceTimer = null;
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function normalize(str) {
		return String(str || '').toLowerCase();
	}

	function hideResults() {
		results.hidden = true;
		results.innerHTML = '';
		input.setAttribute('aria-expanded', 'false');
		activeIndex = -1;
	}

	function render(matches, query) {
		if (!query) {
			hideResults();
			return;
		}

		results.innerHTML = '';
		if (!matches.length) {
			var empty = document.createElement('div');
			empty.className = 'cf-hero-search-empty';
			empty.textContent = labels.empty;
			results.appendChild(empty);
			results.hidden = false;
			input.setAttribute('aria-expanded', 'true');
			activeIndex = -1;
			return;
		}

		matches.slice(0, 8).forEach(function (item, index) {
			var link = document.createElement('a');
			link.href = item.url;
			link.className = 'cf-hero-search-item';
			link.setAttribute('role', 'option');
			link.setAttribute('data-index', String(index));

			var type = document.createElement('span');
			type.className = 'cf-hero-search-type cf-hero-search-type--' + (item.type === 'track' ? 'track' : 'article');
			type.textContent = item.type === 'track' ? labels.track : labels.article;

			var body = document.createElement('div');
			body.className = 'cf-hero-search-body';

			var title = document.createElement('div');
			title.className = 'cf-hero-search-title';
			title.textContent = item.title || '';

			var excerpt = document.createElement('p');
			excerpt.className = 'cf-hero-search-excerpt';
			excerpt.textContent = item.excerpt || '';

			body.appendChild(title);
			body.appendChild(excerpt);
			link.appendChild(type);
			link.appendChild(body);
			results.appendChild(link);
		});

		results.hidden = false;
		input.setAttribute('aria-expanded', 'true');
		activeIndex = -1;
	}

	function search(query) {
		var q = normalize(query).trim();
		if (!q) {
			hideResults();
			return;
		}

		var matches = items.filter(function (item) {
			var hay = normalize(item.title) + ' ' + normalize(item.excerpt) + ' ' + normalize(item.search);
			return hay.indexOf(q) !== -1;
		});
		render(matches, q);
	}

	function setActive(next) {
		var options = results.querySelectorAll('.cf-hero-search-item');
		if (!options.length) {
			return;
		}
		if (activeIndex >= 0 && options[activeIndex]) {
			options[activeIndex].classList.remove('is-active');
		}
		activeIndex = (next + options.length) % options.length;
		options[activeIndex].classList.add('is-active');
		options[activeIndex].scrollIntoView({ block: 'nearest', behavior: reduceMotion ? 'auto' : 'smooth' });
	}

	input.addEventListener('input', function () {
		var value = input.value;
		window.clearTimeout(debounceTimer);
		debounceTimer = window.setTimeout(function () {
			search(value);
		}, reduceMotion ? 0 : 120);
	});

	input.addEventListener('keydown', function (e) {
		if (results.hidden) {
			return;
		}
		var options = results.querySelectorAll('.cf-hero-search-item');
		if (e.key === 'ArrowDown') {
			e.preventDefault();
			setActive(activeIndex + 1);
		} else if (e.key === 'ArrowUp') {
			e.preventDefault();
			setActive(activeIndex - 1);
		} else if (e.key === 'Enter' && activeIndex >= 0 && options[activeIndex]) {
			e.preventDefault();
			window.location.href = options[activeIndex].href;
		} else if (e.key === 'Escape') {
			hideResults();
			input.blur();
		}
	});

	document.addEventListener('click', function (e) {
		if (!root.contains(e.target)) {
			hideResults();
		}
	});
})();

(function () {
	var listenBtn = document.querySelector('[data-cf-home-listen]');
	if (!listenBtn) {
		return;
	}
	listenBtn.addEventListener('click', function () {
		if (!window.playTrack) {
			return;
		}
		window.playTrack(
			listenBtn.getAttribute('data-audio') || '',
			listenBtn.getAttribute('data-title') || '',
			listenBtn.getAttribute('data-artist') || '',
			listenBtn.getAttribute('data-cover') || ''
		);
	});
})();

(function () {
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var revealNodes = document.querySelectorAll('[data-cf-home-reveal]');
	if (!revealNodes.length) {
		return;
	}

	if (reduceMotion || !('IntersectionObserver' in window)) {
		revealNodes.forEach(function (node) {
			node.classList.add('is-visible');
		});
		return;
	}

	var observer = new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (!entry.isIntersecting) {
				return;
			}
			entry.target.classList.add('is-visible');
			observer.unobserve(entry.target);
		});
	}, {
		threshold: 0.12,
		rootMargin: '0px 0px -6% 0px'
	});

	revealNodes.forEach(function (node) {
		observer.observe(node);
	});
})();
</script>

<?php
get_footer();
