<?php
/**
 * Template Name: Join Community
 * Description: Theme template for the Join Community page.
 *
 * @package Collective_Finity
 */

$cf_contact_url       = collective_finity_get_page_link( 'contact', '/contact/' );
$cf_music_library_url = collective_finity_get_page_link( 'music-library', '/music-library/' );

$cf_discord_url = collective_finity_get_social_url( 'social_discord' );

$cf_channel_sections = array(
	'community' => array(
		'label' => __( 'Connect', 'collective-finity' ),
		'desc'  => __( 'Hang out, chat, and stay close to the crew.', 'collective-finity' ),
		'items' => array(
			array(
				'icon'     => 'fa-brands fa-discord',
				'title'    => __( 'Discord', 'collective-finity' ),
				'desc'     => __( 'Voice channels and daily drops with the crew.', 'collective-finity' ),
				'url'      => collective_finity_get_social_url( 'social_discord' ),
				'featured' => true,
				'cta'      => __( 'Join server', 'collective-finity' ),
			),
			array(
				'icon'  => 'fa-solid fa-users',
				'title' => __( 'Facebook Group', 'collective-finity' ),
				'desc'  => __( 'Community discussions and event announcements.', 'collective-finity' ),
				'url'   => collective_finity_get_social_url( array( 'social_facebook_group', 'social_facebook' ) ),
				'cta'   => __( 'Join group', 'collective-finity' ),
			),
		),
	),
	'social' => array(
		'label' => __( 'Follow', 'collective-finity' ),
		'desc'  => __( 'Visuals, teasers, and behind-the-scenes moments.', 'collective-finity' ),
		'items' => array(
			array(
				'icon'  => 'fa-brands fa-tiktok',
				'title' => __( 'TikTok', 'collective-finity' ),
				'desc'  => __( 'Behind-the-scenes clips and track teasers.', 'collective-finity' ),
				'url'   => collective_finity_get_social_url( 'social_tiktok' ),
				'cta'   => __( 'Follow', 'collective-finity' ),
			),
			array(
				'icon'  => 'fa-brands fa-instagram',
				'title' => __( 'Instagram — Music', 'collective-finity' ),
				'desc'  => __( 'Cover art, visuals, and release announcements.', 'collective-finity' ),
				'url'   => collective_finity_get_social_url( 'social_instagram' ),
				'cta'   => __( 'Follow', 'collective-finity' ),
			),
			array(
				'icon'  => 'fa-brands fa-instagram',
				'title' => __( 'Instagram — Community', 'collective-finity' ),
				'desc'  => __( 'Fan features and community spotlights.', 'collective-finity' ),
				'url'   => collective_finity_get_social_url( array( 'social_instagram_community', 'social_instagram' ) ),
				'cta'   => __( 'Follow', 'collective-finity' ),
			),
			array(
				'icon'  => 'fa-brands fa-youtube',
				'title' => __( 'YouTube', 'collective-finity' ),
				'desc'  => __( 'Full releases, visualizers, and session videos.', 'collective-finity' ),
				'url'   => collective_finity_get_social_url( 'social_youtube' ),
				'cta'   => __( 'Subscribe', 'collective-finity' ),
			),
		),
	),
	'streaming' => array(
		'label' => __( 'Stream', 'collective-finity' ),
		'desc'  => __( 'Listen wherever you play music.', 'collective-finity' ),
		'items' => array(
			array(
				'icon'  => 'fa-brands fa-amazon',
				'title' => __( 'Amazon Music', 'collective-finity' ),
				'desc'  => __( 'Stream the full catalog and curated playlists.', 'collective-finity' ),
				'url'   => collective_finity_get_social_url( array( 'social_amazon', 'social_amazon_music' ) ),
				'cta'   => __( 'Listen', 'collective-finity' ),
			),
			array(
				'icon'  => 'fa-brands fa-soundcloud',
				'title' => __( 'SoundCloud', 'collective-finity' ),
				'desc'  => __( 'Early demos, remixes, and DJ sets.', 'collective-finity' ),
				'url'   => collective_finity_get_social_url( 'social_soundcloud' ),
				'cta'   => __( 'Listen', 'collective-finity' ),
			),
			array(
				'icon'  => 'fa-brands fa-spotify',
				'title' => __( 'Spotify', 'collective-finity' ),
				'desc'  => __( 'Official playlists and new releases.', 'collective-finity' ),
				'url'   => collective_finity_get_social_url( 'social_spotify' ),
				'cta'   => __( 'Listen', 'collective-finity' ),
			),
		),
	),
);

$cf_channel_sections = apply_filters( 'collective_finity_community_channel_sections', $cf_channel_sections );

wp_enqueue_style(
	'cf-community-font-awesome',
	'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
	array(),
	'6.5.2'
);

get_header();
?>

<main id="primary" class="site-main cf-community-page" role="main">
	<div class="cf-community-page__inner">

		<section class="cf-community-hero" aria-labelledby="cf-community-hero-heading">
			<div class="cf-community-hero__glow cf-community-hero__glow--left" aria-hidden="true"></div>
			<div class="cf-community-hero__glow cf-community-hero__glow--right" aria-hidden="true"></div>
			<div class="cf-community-hero__dots" aria-hidden="true"></div>
			<div class="cf-community-hero__content">
				<span class="cf-community-hero__badge"><?php esc_html_e( 'The Collective', 'collective-finity' ); ?></span>
				<h1 id="cf-community-hero-heading" class="cf-community-hero__title">
					<?php esc_html_e( 'Find Your Sound Family', 'collective-finity' ); ?>
				</h1>
				<p class="cf-community-hero__lead">
					<?php esc_html_e( 'Every channel we run, in one place — pick your favorite and say hello.', 'collective-finity' ); ?>
				</p>
				<div class="cf-community-hero__actions">
					<a class="cf-community-btn cf-community-btn--primary" href="<?php echo esc_url( $cf_discord_url ); ?>"<?php echo '#' !== $cf_discord_url ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
						<i class="fa-brands fa-discord" aria-hidden="true"></i>
						<?php esc_html_e( 'Join Discord', 'collective-finity' ); ?>
					</a>
					<a class="cf-community-btn cf-community-btn--outline" href="<?php echo esc_url( $cf_music_library_url ); ?>">
						<?php esc_html_e( 'Explore music', 'collective-finity' ); ?>
					</a>
				</div>
			</div>
		</section>

		<?php foreach ( $cf_channel_sections as $section_id => $section ) : ?>
			<section class="cf-community-section" aria-labelledby="cf-community-section-<?php echo esc_attr( $section_id ); ?>">
				<header class="cf-community-section__head">
					<div>
						<p class="cf-community-section__eyebrow" id="cf-community-section-<?php echo esc_attr( $section_id ); ?>">
							<?php echo esc_html( $section['label'] ); ?>
						</p>
						<p class="cf-community-section__desc"><?php echo esc_html( $section['desc'] ); ?></p>
					</div>
				</header>

				<div class="cf-community-grid">
					<?php
					$card_index = 0;
					foreach ( $section['items'] as $channel ) :
						$card_index++;
						$channel_url = $channel['url'] ?? '#';
						$is_external = '#' !== $channel_url;
						$is_feature  = ! empty( $channel['featured'] );
						$classes     = 'cf-community-card';
						if ( $is_feature ) {
							$classes .= ' cf-community-card--featured';
						}
						?>
						<a
							class="<?php echo esc_attr( $classes ); ?>"
							style="--cf-card-delay: <?php echo esc_attr( (string) ( $card_index * 60 ) ); ?>ms"
							href="<?php echo esc_url( $channel_url ); ?>"
							<?php if ( $is_external ) : ?>
								target="_blank"
								rel="noopener noreferrer"
							<?php endif; ?>
						>
							<?php if ( $is_feature ) : ?>
								<span class="cf-community-card__ribbon"><?php esc_html_e( 'Primary hub', 'collective-finity' ); ?></span>
							<?php endif; ?>

							<div class="cf-community-card__icon" aria-hidden="true">
								<i class="<?php echo esc_attr( $channel['icon'] ); ?>"></i>
							</div>

							<div class="cf-community-card__body">
								<h2 class="cf-community-card__title"><?php echo esc_html( $channel['title'] ); ?></h2>
								<p class="cf-community-card__desc"><?php echo esc_html( $channel['desc'] ); ?></p>
								<span class="cf-community-card__cta">
									<?php echo esc_html( $channel['cta'] ?? __( 'Visit', 'collective-finity' ) ); ?>
									<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M4 12L12 4M6 4h6v6" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
								</span>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endforeach; ?>

		<section class="cf-community-cta" aria-labelledby="cf-community-cta-heading">
			<div class="cf-community-cta__dots" aria-hidden="true"></div>
			<div class="cf-community-cta__body">
				<h2 id="cf-community-cta-heading" class="cf-community-cta__title">
					<?php esc_html_e( 'Want to collaborate or reach the team?', 'collective-finity' ); ?>
				</h2>
				<p class="cf-community-cta__lead">
					<?php esc_html_e( 'For partnerships, press, or direct questions — our contact page is the fastest route.', 'collective-finity' ); ?>
				</p>
			</div>
			<div class="cf-community-cta__actions">
				<a class="cf-community-btn cf-community-btn--primary" href="<?php echo esc_url( $cf_contact_url ); ?>">
					<?php esc_html_e( 'Contact us', 'collective-finity' ); ?>
				</a>
			</div>
		</section>

	</div>
</main>

<style>
.cf-community-page {
	background: var(--cf-bg-panel, #0B0B0B);
	color: #fff;
	padding: 48px clamp(12px, 2vw, 20px) 140px;
	box-sizing: border-box;
	width: 100%;
	max-width: 100%;
	min-width: 0;
	overflow-wrap: anywhere;
}
.cf-community-page__inner {
	width: 100%;
	max-width: min(1100px, 100%);
	min-width: 0;
	margin: 0 auto;
	display: flex;
	flex-direction: column;
	gap: 52px;
}
.cf-community-hero {
	position: relative;
	text-align: center;
	padding: clamp(40px, 6vw, 68px) clamp(20px, 4vw, 40px);
	border-radius: 18px;
	background: radial-gradient(ellipse at 50% 0%, rgba(255, 183, 0, 0.14), transparent 62%), #0B0B0B;
	border: 1px solid #1E1E1E;
	overflow: hidden;
	min-width: 0;
	max-width: 100%;
	box-sizing: border-box;
}
.cf-community-hero__glow {
	position: absolute;
	border-radius: 50%;
	filter: blur(48px);
	pointer-events: none;
	opacity: 0.55;
}
.cf-community-hero__glow--left {
	width: 220px;
	height: 220px;
	top: -60px;
	left: -40px;
	background: rgba(255, 183, 0, 0.18);
}
.cf-community-hero__glow--right {
	width: 180px;
	height: 180px;
	bottom: -50px;
	right: -20px;
	background: rgba(255, 183, 0, 0.1);
}
.cf-community-hero__dots,
.cf-community-cta__dots {
	position: absolute;
	inset: 0;
	opacity: 0.42;
	pointer-events: none;
	background-image: radial-gradient(circle, rgba(255, 183, 0, 0.32) 1px, transparent 1.4px);
	background-size: 26px 26px;
}
.cf-community-hero__content {
	position: relative;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 14px;
}
.cf-community-hero__badge,
.cf-community-section__eyebrow {
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
.cf-community-section__eyebrow {
	margin: 0 0 6px;
	padding: 5px 12px;
	font-size: 10.5px;
}
.cf-community-hero__title {
	margin: 0;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: clamp(28px, 5vw, 40px);
	font-weight: 700;
	line-height: 1.15;
	color: #fff;
}
.cf-community-hero__lead,
.cf-community-section__desc,
.cf-community-cta__lead {
	margin: 0;
	font-size: 14px;
	line-height: 1.7;
	color: #B3B3B3;
}
.cf-community-hero__lead {
	max-width: 520px;
}
.cf-community-hero__actions,
.cf-community-cta__actions {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
	justify-content: center;
	margin-top: 8px;
}
.cf-community-btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 8px;
	padding: 13px 24px;
	border-radius: 9px;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 13px;
	font-weight: 700;
	letter-spacing: 0.04em;
	text-decoration: none;
	transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
}
.cf-community-btn--primary {
	background: var(--cf-accent, #FFB700);
	color: #0D0D0D;
	border: none;
}
.cf-community-btn--primary:hover,
.cf-community-btn--primary:focus-visible {
	background: #ffde99;
	color: #0D0D0D;
	transform: translateY(-1px);
	box-shadow: 0 10px 28px -8px rgba(255, 183, 0, 0.45);
}
.cf-community-btn--outline {
	background: transparent;
	color: #fff;
	border: 1px solid #333;
}
.cf-community-btn--outline:hover,
.cf-community-btn--outline:focus-visible {
	background: #161616;
	color: #fff;
	border-color: #444;
}
.cf-community-section {
	min-width: 0;
}
.cf-community-section__head {
	margin-bottom: 20px;
	min-width: 0;
}
.cf-community-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(min(100%, 220px), 1fr));
	gap: 16px;
	min-width: 0;
}
.cf-community-card {
	position: relative;
	display: flex;
	align-items: flex-start;
	gap: 14px;
	padding: 20px;
	border-radius: 14px;
	background: #141414;
	border: 1px solid #232323;
	color: inherit;
	text-decoration: none;
	text-align: left;
	min-width: 0;
	max-width: 100%;
	box-sizing: border-box;
	transition: border-color 0.22s ease, box-shadow 0.25s ease, transform 0.22s ease, background 0.22s ease;
	animation: cfCommunityFadeUp 0.55s ease both;
	animation-delay: var(--cf-card-delay, 0ms);
}
@keyframes cfCommunityFadeUp {
	from { opacity: 0; transform: translateY(14px); }
	to { opacity: 1; transform: translateY(0); }
}
@media (prefers-reduced-motion: reduce) {
	.cf-community-card { animation: none; }
}
a.cf-community-card:hover,
a.cf-community-card:focus-visible {
	border-color: rgba(255, 183, 0, 0.45);
	background: #181818;
	transform: translateY(-3px);
	box-shadow: 0 16px 36px -14px rgba(255, 183, 0, 0.22);
}
.cf-community-card--featured {
	grid-column: 1 / -1;
	padding: 24px;
	background: linear-gradient(135deg, rgba(255, 183, 0, 0.1), rgba(20, 20, 20, 0.95) 55%);
	border-color: rgba(255, 183, 0, 0.35);
}
.cf-community-card--featured .cf-community-card__icon {
	width: 52px;
	height: 52px;
	min-width: 52px;
	font-size: 22px;
}
.cf-community-card--featured .cf-community-card__title {
	font-size: 18px;
}
.cf-community-card__ribbon {
	position: absolute;
	top: 14px;
	right: 14px;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 9px;
	font-weight: 700;
	letter-spacing: 0.06em;
	text-transform: uppercase;
	color: #0D0D0D;
	background: var(--cf-accent, #FFB700);
	padding: 4px 9px;
	border-radius: 999px;
}
.cf-community-card__icon {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 44px;
	height: 44px;
	min-width: 44px;
	border-radius: 11px;
	background: rgba(255, 183, 0, 0.12);
	color: var(--cf-accent, #FFB700);
	font-size: 18px;
	flex-shrink: 0;
	transition: background 0.22s ease, transform 0.22s ease;
}
a.cf-community-card:hover .cf-community-card__icon,
a.cf-community-card:focus-visible .cf-community-card__icon {
	background: rgba(255, 183, 0, 0.22);
	transform: scale(1.05);
}
.cf-community-card__body {
	display: flex;
	flex-direction: column;
	gap: 6px;
	min-width: 0;
	flex: 1;
}
.cf-community-card__title {
	margin: 0;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 15px;
	font-weight: 700;
	color: #fff;
	overflow-wrap: anywhere;
}
.cf-community-card__desc {
	margin: 0;
	font-size: 12.5px;
	line-height: 1.55;
	color: #7A7A7A;
}
.cf-community-card__cta {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	margin-top: 4px;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 11.5px;
	font-weight: 700;
	letter-spacing: 0.04em;
	color: var(--cf-accent, #FFB700);
}
.cf-community-card__cta svg {
	width: 14px;
	height: 14px;
}
.cf-community-cta {
	position: relative;
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	justify-content: space-between;
	gap: 20px;
	padding: clamp(28px, 4vw, 40px);
	border-radius: 18px;
	background: linear-gradient(135deg, rgba(255, 183, 0, 0.14), rgba(255, 183, 0, 0.02));
	border: 1px solid rgba(255, 183, 0, 0.22);
	overflow: hidden;
	min-width: 0;
	max-width: 100%;
	box-sizing: border-box;
}
.cf-community-cta__body {
	position: relative;
	flex: 1;
	min-width: 220px;
}
.cf-community-cta__title {
	margin: 0 0 8px;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: clamp(20px, 3vw, 24px);
	font-weight: 700;
	color: #fff;
}
.cf-community-cta__actions {
	position: relative;
}
@media (max-width: 1023px) {
	.cf-community-page {
		padding-bottom: 110px;
	}
}
@container cf-main (max-width: 720px) {
	.cf-community-grid {
		grid-template-columns: 1fr;
	}
}
@media (max-width: 767px) {
	.cf-community-page {
		padding: 24px 16px 100px;
	}
	.cf-community-page__inner {
		gap: 40px;
	}
	.cf-community-hero {
		text-align: left;
	}
	.cf-community-hero__content {
		align-items: flex-start;
	}
	.cf-community-hero__actions,
	.cf-community-cta__actions {
		justify-content: flex-start;
		width: 100%;
	}
	.cf-community-grid {
		grid-template-columns: 1fr;
	}
	.cf-community-cta {
		flex-direction: column;
		align-items: flex-start;
	}
}
</style>

<?php
get_footer();
