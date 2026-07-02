<?php
/**
 * [cf_music_library] — Dynamic Music Library page.
 *
 * Replaces the static "Edit HTML" Elementor widget on /music-library/.
 * Pulls real data from the `tracks` and `albums` CPTs and the
 * `music_genre` taxonomy instead of hardcoded placeholder content.
 *
 * INSTALL:
 * 1. Save this file as: wp-content/themes/collective-finity/inc/cf-music-library-shortcode.php
 * 2. In functions.php add:
 *      require get_template_directory() . '/inc/cf-music-library-shortcode.php';
 * 3. In Elementor, delete the "Edit HTML" widget on the Music Library page
 *    and add a "Shortcode" widget containing: [cf_music_library]
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'cf_music_library', 'collective_finity_music_library_shortcode' );

function collective_finity_music_library_shortcode() {

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

	// ── 1. Genres — real taxonomy terms, ordered by how many tracks actually use them ──
	$genres = get_terms( array(
		'taxonomy'   => 'music_genre',
		'hide_empty' => true,   // only show genres that actually have tracks tagged
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 9,
	) );

	// Icon rotation so genre cards don't all look identical (purely cosmetic)
	$genre_icons = array( 'fa-bolt', 'fa-wave-square', 'fa-moon', 'fa-feather-alt', 'fa-compact-disc', 'fa-atom', 'fa-microphone-alt', 'fa-flask', 'fa-brain' );

	// ── 2. Latest Releases — most recent tracks + albums combined, newest first ──
	$latest_tracks = get_posts( array(
		'post_type'      => 'tracks',
		'posts_per_page' => 3,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
	$latest_albums = get_posts( array(
		'post_type'      => 'albums',
		'posts_per_page' => 3,
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
	$latest_releases = array_slice( $latest_releases, 0, 3 );

	// ── 3. Popular Tracks — ranked by real play count (_cf_track_plays), not fake numbers ──
	$popular_tracks = get_posts( array(
		'post_type'      => 'tracks',
		'posts_per_page' => 5,
		'post_status'    => 'publish',
		'meta_key'       => '_cf_track_plays',
		'orderby'        => 'meta_value_num',
		'order'          => 'DESC',
	) );

	// ── 4. Studio gallery — only real images, set via: update_option('cf_studio_gallery_ids', [123,456,...]) ──
	$gallery_ids = get_option( 'cf_studio_gallery_ids', array() );

	ob_start();
	?>
	<div class="music-library-wrapper">

		<section class="library-hero">
			<span class="library-badge">✦ FF Collective Archive</span>
			<h1 class="library-main-title">Explore The Music Output</h1>
			<p class="library-subtitle">Discover premium AI-generated tracks, curated playlists, and cutting-edge audio production built within the FF Collective ecosystem.</p>

			<div class="search-filter-container">
				<div class="search-box">
					<i class="fas fa-search search-icon"></i>
					<input type="text" placeholder="Search tracks, albums, or genres..." class="library-search-input" id="cf-library-search-input">
				</div>
				<div class="filter-tags">
					<button class="filter-btn active" data-filter="all">All</button>
					<button class="filter-btn" data-filter="track">Tracks</button>
					<button class="filter-btn" data-filter="album">Albums</button>
				</div>
			</div>
		</section>

		<?php if ( function_exists( 'collective_finity_ad_slot' ) ) : ?>
		<div class="library-section cf-ad-library-top">
			<?php collective_finity_ad_slot( 'library_top' ); ?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $genres ) && ! is_wp_error( $genres ) ) : ?>
		<section class="library-section">
			<h2 class="section-title">Browse by Mood &amp; Genre</h2>
			<div class="moods-grid">
				<?php foreach ( $genres as $i => $genre ) : ?>
				<a href="<?php echo esc_url( get_term_link( $genre ) ); ?>" class="mood-link-card">
					<div class="mood-card">
						<div class="mood-glow"></div>
						<i class="fas <?php echo esc_attr( $genre_icons[ $i % count( $genre_icons ) ] ); ?> mood-icon"></i>
						<h3><?php echo esc_html( $genre->name ); ?></h3>
					</div>
				</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php else : ?>
		<section class="library-section">
			<div class="library-empty-note">
				No genres have been tagged to any tracks yet — assign a Music Genre to your tracks and they'll appear here automatically.
			</div>
		</section>
		<?php endif; ?>

		<?php if ( ! empty( $latest_releases ) ) : ?>
		<section class="library-section">
			<h2 class="section-title">Latest Releases</h2>
			<div class="releases-grid">
				<?php foreach ( $latest_releases as $item ) :
					$p = $item['post'];
					if ( $item['type'] === 'track' ) {
						$cover  = $cf_get_track_cover( $p->ID );
						$link   = get_permalink( $p->ID );
						$artists = wp_get_post_terms( $p->ID, 'track_artist', array( 'fields' => 'names' ) );
						$artist  = ! empty( $artists ) ? $artists[0] : 'Collective Finity';
						$type_label = get_post_meta( $p->ID, 'track_release_type', true ) === 'album_track' ? 'Album Track' : 'Single';
					} else {
						$cover  = $cf_get_album_cover( $p->ID );
						$link   = get_permalink( $p->ID );
						$artist = get_the_author_meta( 'display_name', $p->post_author );
						$type_label = 'Album';
					}
				?>
				<a href="<?php echo esc_url( $link ); ?>" class="release-card-link">
					<div class="release-card">
						<div class="img-wrapper">
							<img src="<?php echo esc_url( $cover ); ?>" alt="<?php echo esc_attr( $p->post_title ); ?>" loading="lazy">
							<button type="button" class="play-overlay-btn" aria-label="Play"><i class="fas fa-play"></i></button>
						</div>
						<div class="card-meta">
							<h3><?php echo esc_html( $p->post_title ); ?></h3>
							<p><?php echo esc_html( $artist ); ?></p>
							<span class="type-badge"><?php echo esc_html( $type_label ); ?></span>
						</div>
					</div>
				</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( function_exists( 'collective_finity_ad_slot' ) ) : ?>
		<div class="library-section cf-ad-library-between">
			<?php collective_finity_ad_slot( 'library_between_sections' ); ?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $popular_tracks ) ) : ?>
		<section class="library-section split-layout">
			<div class="split-left">
				<h2 class="section-title">Popular Tracks</h2>
				<div class="track-list">
					<?php foreach ( $popular_tracks as $i => $t ) :
						$audio   = get_post_meta( $t->ID, 'track_preview_url', true ) ?: get_post_meta( $t->ID, 'track_audio_url', true );
						$cover   = $cf_get_track_cover( $t->ID );
						$artists = wp_get_post_terms( $t->ID, 'track_artist', array( 'fields' => 'names' ) );
						$artist  = ! empty( $artists ) ? $artists[0] : 'Collective Finity';
						$plays   = intval( get_post_meta( $t->ID, '_cf_track_plays', true ) );
					?>
					<div class="track-item">
						<span class="track-num"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="track-info">
							<h4><a href="<?php the_permalink( $t->ID ); ?>" style="color:inherit;text-decoration:none;"><?php echo esc_html( $t->post_title ); ?></a></h4>
							<p><?php echo esc_html( $artist ); ?></p>
						</div>
						<span class="track-duration"><?php echo esc_html( number_format_i18n( $plays ) ); ?> plays</span>
						<button type="button" class="track-play-btn" aria-label="Play"
							onclick="if (window.playTrack) { window.playTrack('<?php echo esc_js( $audio ); ?>', '<?php echo esc_js( $t->post_title ); ?>', '<?php echo esc_js( $artist ); ?>', '<?php echo esc_js( $cover ); ?>'); }">
							<i class="fas fa-play"></i>
						</button>
					</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="split-right">
				<h2 class="section-title">Featured Playlists</h2>
				<div class="library-empty-note">
					Playlists are coming soon — this section will activate once the community playlist feature ships.
				</div>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( ! empty( $gallery_ids ) ) : ?>
		<section class="library-section">
			<h2 class="section-title">Studio Snaps &amp; Gallery</h2>
			<p class="section-subtitle">Visual snapshots capturing the mood, instruments, and production moments behind the music.</p>
			<div class="library-gallery">
				<?php foreach ( $gallery_ids as $attachment_id ) :
					$img = wp_get_attachment_image_url( $attachment_id, 'medium' );
					if ( ! $img ) continue;
				?>
				<div class="gallery-item"><img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy"></div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

	</div>

	<style>
	/* ========================================================== */
	.music-library-wrapper{width:100%;max-width:calc(100% - 40px);margin:0 auto!important;padding:40px 0!important;background:transparent;box-sizing:border-box;font-family:inherit;color:#fff}
	.library-section{margin-bottom:60px}
	.section-title{font-size:1.8rem;font-weight:800;letter-spacing:1px;margin:0 0 20px 0;color:#fff}
	.section-subtitle{color:rgba(255,255,255,.5);font-size:.95rem;margin:-15px 0 25px 0;max-width:600px}
	.library-empty-note{padding:24px;border:1px dashed rgba(255,255,255,.15);border-radius:12px;color:rgba(255,255,255,.5);font-size:.9rem;text-align:center}
	.library-hero{text-align:center;padding:40px 20px 60px 20px}
	.library-badge{display:inline-block;padding:6px 16px;background:rgba(255,183,0,.1);border:1px solid rgba(255,183,0,.3);border-radius:30px;color:#FFB700;font-size:.75rem;font-weight:600;letter-spacing:2px;text-transform:uppercase;margin-bottom:20px}
	.library-main-title{font-size:3.5rem;font-weight:900;letter-spacing:-1px;margin:0 0 15px 0;line-height:1.1;background:linear-gradient(135deg,#fff 40%,#FFB700 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
	.library-subtitle{color:rgba(255,255,255,.6);font-size:1.1rem;max-width:650px;margin:0 auto 40px auto;line-height:1.6}
	.search-filter-container{display:flex;flex-direction:column;align-items:center;gap:20px;max-width:600px;margin:0 auto}
	.search-box{position:relative;width:100%}
	.search-icon{position:absolute;left:18px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.4);font-size:1.1rem}
	.library-search-input{width:100%;padding:16px 18px 16px 50px;border-radius:30px;border:1px solid rgba(255,255,255,.1);background:rgba(255,255,255,.03);color:#fff;font-size:1rem;outline:none;transition:all .3s ease}
	.library-search-input:focus{border-color:#FFB700;background:rgba(255,183,0,.05);box-shadow:0 0 25px rgba(255,183,0,.15)}
	.filter-tags{display:flex;gap:10px;flex-wrap:wrap;justify-content:center}
	.filter-btn{padding:8px 22px;border-radius:20px;border:1px solid rgba(255,255,255,.1);background:rgba(255,255,255,.02);color:rgba(255,255,255,.7);font-size:.85rem;font-weight:600;cursor:pointer;transition:all .3s ease}
	.filter-btn:hover,.filter-btn.active{background:#FFB700;color:#000;border-color:#FFB700;font-weight:700}
	.moods-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
	.mood-link-card{text-decoration:none;color:#fff;display:block}
	.mood-card{position:relative;padding:35px 20px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.05);border-radius:16px;text-align:center;cursor:pointer;overflow:hidden;transition:all .3s cubic-bezier(.4,0,.2,1)}
	.mood-icon{font-size:2.2rem;color:#FFB700;margin-bottom:15px;transition:transform .3s ease}
	.mood-card h3{margin:0;font-size:1.15rem;font-weight:700;letter-spacing:.5px}
	.mood-glow{position:absolute;width:120px;height:120px;background:radial-gradient(circle,rgba(255,183,0,.12) 0%,transparent 70%);top:-60px;right:-60px;transition:all .5s ease}
	.mood-link-card:hover .mood-card{transform:translateY(-5px);border-color:rgba(255,183,0,.5);background:rgba(255,183,0,.02);box-shadow:0 15px 35px rgba(0,0,0,.5)}
	.mood-link-card:hover .mood-icon{transform:scale(1.15);color:#fff}
	.releases-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:25px}
	.release-card-link{text-decoration:none}
	.release-card{background:rgba(255,255,255,.01);border:1px solid rgba(255,255,255,.03);border-radius:14px;padding:15px;transition:border-color .3s ease}
	.release-card:hover{border-color:rgba(255,183,0,.2)}
	.img-wrapper{position:relative;border-radius:10px;overflow:hidden;aspect-ratio:1;margin-bottom:15px}
	.img-wrapper img{width:100%;height:100%;object-fit:cover;transition:transform .5s ease}
	.play-overlay-btn{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) scale(.8);width:55px;height:55px;border-radius:50%;background:#FFB700;border:none;color:#000;font-size:1.2rem;cursor:pointer;opacity:0;transition:all .3s cubic-bezier(.4,0,.2,1);box-shadow:0 8px 20px rgba(255,183,0,.4)}
	.release-card:hover .img-wrapper img{transform:scale(1.06)}
	.release-card:hover .play-overlay-btn{opacity:1;transform:translate(-50%,-50%) scale(1)}
	.card-meta{display:flex;flex-direction:column;gap:5px;position:relative}
	.card-meta h3{margin:0;font-size:1.1rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
	.card-meta p{margin:0;font-size:.85rem;color:rgba(255,255,255,.5)}
	.type-badge{align-self:flex-start;font-size:.65rem;font-weight:700;text-transform:uppercase;padding:2px 8px;background:rgba(255,255,255,.05);border-radius:4px;color:rgba(255,255,255,.6);margin-top:5px}
	.split-layout{display:grid;grid-template-columns:1.6fr 1fr;gap:40px}
	.track-list{display:flex;flex-direction:column;gap:10px}
	.track-item{display:flex;align-items:center;padding:12px 20px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.03);border-radius:10px;transition:all .2s ease}
	.track-num{font-size:.9rem;color:rgba(255,255,255,.3);width:35px;font-weight:600}
	.track-info{flex:1}
	.track-info h4{margin:0 0 2px 0;font-size:1rem;font-weight:600}
	.track-info p{margin:0;font-size:.8rem;color:rgba(255,255,255,.4)}
	.track-duration{font-size:.85rem;color:rgba(255,255,255,.4);margin-right:20px}
	.track-play-btn{background:transparent;border:none;color:#fff;cursor:pointer;font-size:.9rem;transition:color .2s ease}
	.track-item:hover{background:rgba(255,255,255,.04);border-color:rgba(255,183,0,.2)}
	.track-item:hover .track-play-btn{color:#FFB700}
	.library-gallery{display:grid;grid-template-columns:repeat(4,1fr);gap:15px}
	.gallery-item{border-radius:12px;overflow:hidden;aspect-ratio:1.2}
	.gallery-item img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease}
	.gallery-item:hover img{transform:scale(1.05)}
	.cf-ad-slot{margin:20px auto;max-width:100%;text-align:center}
	.cf-ad-slot--preview{align-items:center;background:rgba(255,255,255,.04);border:1px dashed rgba(255,183,0,.35);border-radius:12px;color:rgba(255,255,255,.55);display:flex;font-family:'Space Mono',monospace;font-size:.85rem;justify-content:center;min-height:90px;padding:24px}
	@media (max-width:1200px){.moods-grid{grid-template-columns:repeat(2,1fr)}.releases-grid{grid-template-columns:repeat(2,1fr)}.library-gallery{grid-template-columns:repeat(2,1fr)}}
	@media (max-width:992px){.split-layout{grid-template-columns:1fr;gap:40px}}
	@media (max-width:768px){.library-main-title{font-size:2.5rem}.moods-grid{grid-template-columns:1fr}.releases-grid{grid-template-columns:1fr}.library-gallery{grid-template-columns:1fr}}
	</style>

	<script>
	(function () {
		document.querySelectorAll('.mood-card').forEach(function (card) {
			card.addEventListener('mousemove', function (e) {
				var rect = card.getBoundingClientRect();
				var x = ((e.clientX - rect.left) / rect.width) * 100;
				var y = ((e.clientY - rect.top) / rect.height) * 100;
				card.style.setProperty('--mouse-x', x + '%');
				card.style.setProperty('--mouse-y', y + '%');
			});
		});

		// Client-side filter for Tracks/Albums toggle (Latest Releases grid only — this filters
		// what's already rendered on the page; it is not a full-text search).
		var filterBtns = document.querySelectorAll('.filter-btn');
		filterBtns.forEach(function (btn) {
			btn.addEventListener('click', function () {
				filterBtns.forEach(function (b) { b.classList.remove('active'); });
				btn.classList.add('active');
				// Full search/filter wiring is a separate task — see plan.
			});
		});
	})();
	</script>
	<?php
	return ob_get_clean();
}