<?php
/**
 * Template Name: Albums Archive
 * Description: Displays all albums in a polished collection layout.
 */

get_header(); ?>

<div id="primary" class="content-area cf-albums-page">
    <main id="main" class="site-main">
        <section class="cf-albums-hero" aria-labelledby="cf-albums-hero-heading">
            <div class="cf-albums-hero__border" aria-hidden="true"></div>
            <div class="cf-albums-hero__center-glow" aria-hidden="true"></div>
            <div class="cf-albums-hero__content">
                <span class="cf-albums-hero__badge"><?php esc_html_e( 'Collective Finity', 'collective-finity' ); ?></span>
                <h1 id="cf-albums-hero-heading" class="cf-albums-hero__title">
                    <?php esc_html_e( 'Albums & Collections', 'collective-finity' ); ?>
                </h1>
                <p class="cf-albums-hero__lead">
                    <?php esc_html_e( 'Explore our cinematic music collections, each telling a unique story through sound.', 'collective-finity' ); ?>
                </p>
            </div>
        </section>

        <?php
        $cf_album_genre_filter = isset( $_GET['genre'] ) ? sanitize_title( wp_unslash( $_GET['genre'] ) ) : '';
        $cf_album_genres       = get_terms( array( 'taxonomy' => 'music_genre', 'hide_empty' => false ) );
        ?>

        <div class="albums-grid-container">

            <?php if ( ! empty( $cf_album_genres ) && ! is_wp_error( $cf_album_genres ) ) : ?>
                <div class="cf-filter-row-wrap">
                    <div class="cf-filter-row">
                        <a href="<?php echo esc_url( remove_query_arg( 'genre' ) ); ?>" class="cf-filter-pill<?php echo '' === $cf_album_genre_filter ? ' active' : ''; ?>"><?php esc_html_e( 'All genres', 'collective-finity' ); ?></a>
                        <?php foreach ( $cf_album_genres as $cf_ag_term ) : ?>
                            <a href="<?php echo esc_url( add_query_arg( 'genre', $cf_ag_term->slug ) ); ?>" class="cf-filter-pill<?php echo $cf_album_genre_filter === $cf_ag_term->slug ? ' active' : ''; ?>"><?php echo esc_html( $cf_ag_term->name ); ?></a>
                        <?php endforeach; ?>
                    </div>
                    <div class="cf-filter-row-fade" aria-hidden="true">
                        <svg class="cf-filter-row-fade__icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
                            <path d="M6 3.5L10.5 8L6 12.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            $cf_albums_query_args = array( 'post_type' => 'albums', 'posts_per_page' => -1, 'post_status' => 'publish' );
            if ( '' !== $cf_album_genre_filter ) {
                $cf_albums_query_args['tax_query'] = array( array( 'taxonomy' => 'music_genre', 'field' => 'slug', 'terms' => $cf_album_genre_filter ) );
            }
            $cf_albums_query = new WP_Query( $cf_albums_query_args );

            if ( $cf_albums_query->have_posts() ) : ?>
                <div class="cf-card-grid">
                    <?php while ( $cf_albums_query->have_posts() ) : $cf_albums_query->the_post();
                        $album_id        = get_the_ID();
                        $album_permalink = get_permalink( $album_id );
                        $album_title     = get_the_title( $album_id );
                        $cover_url       = get_the_post_thumbnail_url( $album_id, 'medium' );

                        $cf_album_tracks_q = new WP_Query( array( 'post_type' => 'tracks', 'posts_per_page' => -1, 'post_status' => 'publish', 'fields' => 'ids', 'meta_query' => array( array( 'key' => 'associated_album', 'value' => $album_id, 'compare' => '=' ) ) ) );
                        $cf_track_count = $cf_album_tracks_q->found_posts;

                        if ( empty( $cover_url ) && $cf_album_tracks_q->have_posts() ) {
                            foreach ( $cf_album_tracks_q->posts as $cf_t_id ) {
                                $cf_first_cover = get_post_meta( $cf_t_id, 'track_cover_url', true );
                                if ( ! empty( $cf_first_cover ) ) { $cover_url = $cf_first_cover; break; }
                            }
                        }
                        if ( empty( $cover_url ) ) { $cover_url = collective_finity_default_art_url(); }

                        $cf_album_genre_terms = get_the_terms( $album_id, 'music_genre' );
                        $cf_album_genre_name  = ( $cf_album_genre_terms && ! is_wp_error( $cf_album_genre_terms ) ) ? $cf_album_genre_terms[0]->name : '';
                    ?>
                        <a href="<?php echo esc_url( $album_permalink ); ?>" class="cf-card">
                            <div class="cf-cover"><img src="<?php echo esc_url( $cover_url ); ?>" alt="<?php echo esc_attr( $album_title ); ?>" loading="lazy"></div>
                            <div class="cf-card-title"><?php echo esc_html( $album_title ); ?></div>
                            <div class="cf-card-sub"><?php echo esc_html( sprintf( _n( '%d track', '%d tracks', $cf_track_count, 'collective-finity' ), $cf_track_count ) ); ?></div>
                            <?php if ( $cf_album_genre_name ) : ?><span class="cf-card-chip"><?php echo esc_html( $cf_album_genre_name ); ?></span><?php endif; ?>
                        </a>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            <?php else : ?>
                <div class="albums-empty-state">
                    <span>🎵</span>
                    <h2><?php _e( 'No Albums Yet', 'collective-finity' ); ?></h2>
                    <p><?php _e( 'Check back soon for new music collections.', 'collective-finity' ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<style>
    .cf-albums-page {
        padding: 48px 5px 5px;
        box-sizing: border-box;
        max-width: 100%;
        min-width: 0;
    }
    .cf-albums-hero {
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
    @property --cf-albums-hero-border-angle {
        syntax: '<angle>';
        initial-value: 0deg;
        inherits: false;
    }
    .cf-albums-hero__border {
        position: absolute;
        inset: 0;
        border-radius: inherit;
        padding: 1.5px;
        pointer-events: none;
        z-index: 2;
        background: conic-gradient(
            from var(--cf-albums-hero-border-angle),
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
        animation: cfAlbumsBorderTravel 5.5s linear infinite;
        filter: drop-shadow(0 0 6px rgba(255, 183, 0, 0.35));
    }
    @keyframes cfAlbumsBorderTravel {
        to { --cf-albums-hero-border-angle: 360deg; }
    }
    .cf-albums-hero__center-glow {
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
        animation: cfAlbumsCenterGlow 8.2s ease-in-out infinite;
        will-change: transform, opacity;
    }
    @keyframes cfAlbumsCenterGlow {
        0%, 100% {
            opacity: 0.35;
            transform: translate(-50%, -50%) scale(0.82);
        }
        50% {
            opacity: 0.7;
            transform: translate(-50%, -50%) scale(1.08);
        }
    }
    .cf-albums-hero__content {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
    }
    .cf-albums-hero__badge {
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
    .cf-albums-hero__title {
        margin: 0;
        font-family: var(--cf-mono, 'Space Mono', monospace);
        font-size: clamp(28px, 5vw, 40px);
        font-weight: 700;
        line-height: 1.15;
        color: #fff;
    }
    .cf-albums-hero__lead {
        margin: 0;
        max-width: 520px;
        font-size: 14px;
        line-height: 1.7;
        color: #B3B3B3;
    }
    @media (prefers-reduced-motion: reduce) {
        .cf-albums-hero__border,
        .cf-albums-hero__center-glow {
            animation: none;
        }
    }
    .albums-grid-container { max-width: 1200px; margin: 0 auto; padding: 0; }

    .cf-filter-row-wrap {
        position: relative;
        margin-bottom: 28px;
    }
    .cf-filter-row {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    .cf-filter-row::-webkit-scrollbar { display: none; }
    .cf-filter-row-fade {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 4px;
        width: 48px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 2px;
        box-sizing: border-box;
        background: linear-gradient(
            to right,
            transparent 0%,
            var(--cf-bg-darkest, #050505) 72%
        );
        color: #B3B3B3;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
        cursor: pointer;
    }
    .cf-filter-row-fade.is-visible {
        opacity: 1;
        pointer-events: auto;
    }
    .cf-filter-row-fade__icon {
        display: block;
        flex-shrink: 0;
        width: 16px;
        height: 16px;
    }
    .cf-filter-pill { flex-shrink: 0; padding: 8px 16px; border-radius: 20px; border: none; background: #141414; color: #B3B3B3; font-size: 13px; font-weight: 600; text-decoration: none; white-space: nowrap; }
    .cf-filter-pill.active { background: var(--primary-color, #FFB700); color: #1a1400; }

    .cf-card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 22px; }
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
    .cf-card-title { font-size: 14px; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding: 0 12px; }
    .cf-card-sub { font-size: 12px; color: #7A7A7A; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding: 0 12px; transition: color var(--cf-transition-speed, 0.2s) ease; }
    .cf-card-chip { display: inline-block; margin: 6px 12px 0; font-size: 10px; color: #B3B3B3; background: rgba(255,255,255,0.04); border: 1px solid var(--cf-border, #232323); padding: 2px 8px; border-radius: 10px; transition: border-color var(--cf-transition-speed, 0.2s) ease; }

    .cf-card:is(:hover, :focus-visible, :focus-within) {
        border-color: color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 55%, transparent);
        box-shadow:
            0 0 0 1px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 40%, transparent),
            0 14px 28px -10px rgba(0, 0, 0, 0.55),
            0 0 28px 4px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 24%, transparent),
            0 0 64px 16px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 12%, transparent);
    }
    .cf-card:is(:hover, :focus-visible, :focus-within) .cf-cover img { transform: scale(1.03); }
    .cf-card:is(:hover, :focus-visible, :focus-within) .cf-card-sub {
        color: var(--cf-link, var(--cf-accent, var(--primary-color, #FFB700)));
    }
    .cf-card:is(:hover, :focus-visible, :focus-within) .cf-card-chip {
        border-color: var(--cf-link, var(--cf-accent, var(--primary-color, #FFB700)));
    }

    @supports not (color: color-mix(in srgb, red 50%, blue)) {
        .cf-card:is(:hover, :focus-visible, :focus-within) {
            border-color: rgba(255, 183, 0, 0.55);
            box-shadow:
                0 0 0 1px rgba(255, 183, 0, 0.4),
                0 14px 28px -10px rgba(0, 0, 0, 0.55),
                0 0 28px 4px rgba(255, 183, 0, 0.24),
                0 0 64px 16px rgba(255, 183, 0, 0.12);
        }
    }

    @media (max-width: 640px) {
        .cf-card-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .cf-filter-row { padding: 0 4px 4px; }
        .cf-filter-row-fade {
            width: 40px;
            bottom: 4px;
        }
        .cf-filter-row-fade__icon {
            width: 14px;
            height: 14px;
        }
    }

    @media (hover: none) {
        .cf-card:active {
            border-color: color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 55%, transparent);
            box-shadow:
                0 0 0 1px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 40%, transparent),
                0 14px 28px -10px rgba(0, 0, 0, 0.55),
                0 0 28px 4px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 24%, transparent),
                0 0 64px 16px color-mix(in srgb, var(--cf-accent, var(--primary-color, #FFB700)) 12%, transparent);
        }
        .cf-card:active .cf-card-sub {
            color: var(--cf-link, var(--cf-accent, var(--primary-color, #FFB700)));
        }
        .cf-card:active .cf-card-chip {
            border-color: var(--cf-link, var(--cf-accent, var(--primary-color, #FFB700)));
        }
    }

    .albums-empty-state {
        text-align: center;
        padding: 80px 20px;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
        background: rgba(255,255,255,0.02);
    }
    .albums-empty-state span {
        font-size: 48px;
        display: block;
        margin-bottom: 18px;
    }
    .albums-empty-state h2 {
        color: #fff;
        margin: 0 0 10px;
        font-size: 24px;
    }
    .albums-empty-state p {
        color: #888;
        margin: 0;
        line-height: 1.7;
    }
</style>

<script>
(function () {
	var wrap = document.querySelector('.cf-albums-page .cf-filter-row-wrap');
	if (!wrap) {
		return;
	}
	var row = wrap.querySelector('.cf-filter-row');
	var fade = wrap.querySelector('.cf-filter-row-fade');
	if (!row || !fade) {
		return;
	}

	function updateFade() {
		var maxScroll = row.scrollWidth - row.clientWidth;
		var hasOverflow = maxScroll > 2;
		var atEnd = row.scrollLeft >= maxScroll - 2;
		fade.classList.toggle('is-visible', hasOverflow && !atEnd);
	}

	fade.addEventListener('click', function () {
		row.scrollBy({ left: 200, behavior: 'smooth' });
	});

	row.addEventListener('scroll', updateFade, { passive: true });
	window.addEventListener('resize', updateFade);

	if (typeof ResizeObserver !== 'undefined') {
		var ro = new ResizeObserver(updateFade);
		ro.observe(row);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', updateFade);
	} else {
		updateFade();
	}
})();
</script>

<?php get_footer(); ?>
