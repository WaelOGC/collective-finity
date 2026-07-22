<?php
/**
 * Template Name: Join Community
 * Description: Theme template for the Join Community page.
 *
 * @package Collective_Finity
 */

$cf_contact_url       = collective_finity_get_page_link( 'contact', '/contact/' );
$cf_music_library_url = collective_finity_get_page_link( 'music-library', '/music-library/' );

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

$cf_hero_title = __( 'Find Your Sound Family', 'collective-finity' );
$cf_hero_words = preg_split( '/\s+/u', trim( $cf_hero_title ) );
if ( ! is_array( $cf_hero_words ) ) {
	$cf_hero_words = array( $cf_hero_title );
}

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
			<div class="cf-community-hero__border" aria-hidden="true"></div>
			<div class="cf-community-hero__center-glow" aria-hidden="true"></div>
			<div class="cf-community-hero__dancers" aria-hidden="true">
				<?php
				$cf_dancer_uri  = get_template_directory_uri() . '/assets/images/dancing/';
				$cf_dancer_imgs = array(
					'dancing-arms-raised.png',
					'dancing-spin-twirl.png',
					'dancing-jump-leap.png',
					'dancing-backbend-dip.png',
					'dancing-groove-sway.png',
				);
				foreach ( $cf_dancer_imgs as $cf_dancer_i => $cf_dancer_file ) :
					?>
					<span
						class="cf-community-hero__dancer cf-community-hero__dancer--<?php echo esc_attr( (string) ( $cf_dancer_i + 1 ) ); ?>"
						style="--cf-dancer-img: url('<?php echo esc_url( $cf_dancer_uri . $cf_dancer_file ); ?>');"
					></span>
				<?php endforeach; ?>
			</div>
			<div class="cf-community-hero__freq" aria-hidden="true"></div>
			<div class="cf-community-hero__eq" aria-hidden="true">
				<?php
				$cf_eq_bars = array( 28, 52, 38, 72, 44, 86, 34, 64, 48, 90, 40, 58, 76, 32, 68, 46, 82, 36, 60, 74, 42, 88, 30, 56, 70, 50, 84, 38, 66, 44, 78, 54 );
				foreach ( $cf_eq_bars as $cf_eq_i => $cf_eq_h ) :
					$cf_eq_dur   = 1.1 + ( ( $cf_eq_i % 7 ) * 0.18 );
					$cf_eq_delay = ( $cf_eq_i % 11 ) * 0.12;
					?>
					<span
						class="cf-community-hero__eq-bar"
						style="--cf-eq-h: <?php echo esc_attr( (string) $cf_eq_h ); ?>%; --cf-eq-dur: <?php echo esc_attr( (string) $cf_eq_dur ); ?>s; --cf-eq-delay: -<?php echo esc_attr( (string) $cf_eq_delay ); ?>s;"
					></span>
				<?php endforeach; ?>
			</div>
			<div class="cf-community-hero__content">
				<span class="cf-community-hero__badge"><?php esc_html_e( 'FF Collective', 'collective-finity' ); ?></span>
				<h1 id="cf-community-hero-heading" class="cf-community-hero__title">
					<?php foreach ( $cf_hero_words as $cf_word_i => $cf_word ) : ?>
						<?php if ( $cf_word_i > 0 ) : ?>
							<?php echo ' '; ?>
						<?php endif; ?>
						<span class="cf-community-hero__word" style="--cf-word-delay: <?php echo esc_attr( (string) ( $cf_word_i * 0.5 ) ); ?>s"><?php echo esc_html( $cf_word ); ?></span>
					<?php endforeach; ?>
				</h1>
				<p class="cf-community-hero__lead">
					<?php esc_html_e( 'Every channel we run, in one place — pick your favorite and say hello.', 'collective-finity' ); ?>
				</p>
				<div class="cf-community-hero__actions">
					<a class="cf-btn-primary-lg" href="<?php echo esc_url( $cf_music_library_url ); ?>">
						<?php esc_html_e( 'Explore music', 'collective-finity' ); ?>
					</a>
				</div>
			</div>
		</section>

		<?php foreach ( $cf_channel_sections as $section_id => $section ) : ?>
			<section class="cf-community-section" aria-labelledby="cf-community-section-<?php echo esc_attr( $section_id ); ?>">
				<header class="cf-community-section__head">
					<div class="cf-community-section__label-row">
						<p class="cf-community-section__eyebrow" id="cf-community-section-<?php echo esc_attr( $section_id ); ?>">
							<?php echo esc_html( $section['label'] ); ?>
						</p>
						<span class="cf-community-section__line" aria-hidden="true"></span>
					</div>
					<p class="cf-community-section__desc"><?php echo esc_html( $section['desc'] ); ?></p>
				</header>

				<div class="cf-community-list">
					<?php
					foreach ( $section['items'] as $channel ) :
						$channel_url = $channel['url'] ?? '#';
						$is_external = '#' !== $channel_url;
						$is_feature  = ! empty( $channel['featured'] );
						$classes     = 'cf-community-row';
						if ( $is_feature ) {
							$classes .= ' cf-community-row--featured';
						}
						?>
						<a
							class="<?php echo esc_attr( $classes ); ?>"
							href="<?php echo esc_url( $channel_url ); ?>"
							<?php if ( $is_external ) : ?>
								target="_blank"
								rel="noopener noreferrer"
							<?php endif; ?>
						>
							<div class="cf-community-row__icon" aria-hidden="true">
								<i class="<?php echo esc_attr( $channel['icon'] ); ?>"></i>
							</div>

							<div class="cf-community-row__body">
								<div class="cf-community-row__title-wrap">
									<h2 class="cf-community-row__title"><?php echo esc_html( $channel['title'] ); ?></h2>
									<?php if ( $is_feature ) : ?>
										<span class="cf-community-row__badge"><?php esc_html_e( 'Primary hub', 'collective-finity' ); ?></span>
									<?php endif; ?>
								</div>
								<p class="cf-community-row__desc"><?php echo esc_html( $channel['desc'] ); ?></p>
							</div>

							<span class="cf-community-row__cta">
								<?php echo esc_html( $channel['cta'] ?? __( 'Visit', 'collective-finity' ) ); ?>
								<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M4 12L12 4M6 4h6v6" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
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
	padding: 48px 5px 5px;
	box-sizing: border-box;
	width: 100%;
	max-width: 100%;
	min-width: 0;
	overflow-wrap: anywhere;
}
.cf-community-page__inner {
	width: 100%;
	max-width: 100%;
	min-width: 0;
	margin: 0 auto;
	display: flex;
	flex-direction: column;
	gap: 52px;
}
.cf-community-hero {
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
	margin: 0 auto;
	box-sizing: border-box;
}
@property --cf-hero-border-angle {
	syntax: '<angle>';
	initial-value: 0deg;
	inherits: false;
}
.cf-community-hero__border {
	position: absolute;
	inset: 0;
	border-radius: inherit;
	padding: 1.5px;
	pointer-events: none;
	z-index: 2;
	background: conic-gradient(
		from var(--cf-hero-border-angle),
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
	animation: cfCommunityBorderTravel 5.5s linear infinite;
	filter: drop-shadow(0 0 6px rgba(255, 183, 0, 0.35));
}
@keyframes cfCommunityBorderTravel {
	to { --cf-hero-border-angle: 360deg; }
}
.cf-community-hero__center-glow {
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
	animation: cfCommunityCenterGlow 8.2s ease-in-out infinite;
	will-change: transform, opacity;
}
@keyframes cfCommunityCenterGlow {
	0%, 100% {
		opacity: 0.35;
		transform: translate(-50%, -50%) scale(0.82);
	}
	50% {
		opacity: 0.7;
		transform: translate(-50%, -50%) scale(1.08);
	}
}
.cf-community-hero__dancers {
	position: absolute;
	inset: 0;
	pointer-events: none;
	z-index: 0;
	overflow: hidden;
}
.cf-community-hero__dancer {
	position: absolute;
	display: block;
	width: clamp(52px, 9vw, 88px);
	aspect-ratio: 1 / 1.35;
	opacity: 0;
	background-image: linear-gradient(
		160deg,
		#c45a00 0%,
		var(--cf-accent, #FFB700) 48%,
		#FFE08A 100%
	);
	-webkit-mask-image: var(--cf-dancer-img);
	mask-image: var(--cf-dancer-img);
	-webkit-mask-size: contain;
	mask-size: contain;
	-webkit-mask-repeat: no-repeat;
	mask-repeat: no-repeat;
	-webkit-mask-position: center bottom;
	mask-position: center bottom;
	filter: saturate(1.15) brightness(0.95);
	animation: cfCommunityDancerPulse var(--cf-dancer-dur, 7.5s) ease-in-out infinite;
	animation-delay: var(--cf-dancer-delay, 0s);
}
@keyframes cfCommunityDancerPulse {
	0%, 100% { opacity: 0; }
	18%, 42% { opacity: 0.14; }
	30% { opacity: 0.2; }
	55%, 100% { opacity: 0; }
}
.cf-community-hero__dancer--1 {
	top: 10%;
	left: 4%;
	--cf-dancer-dur: 8.4s;
	--cf-dancer-delay: 0s;
}
.cf-community-hero__dancer--2 {
	top: 8%;
	right: 5%;
	width: clamp(48px, 8vw, 78px);
	--cf-dancer-dur: 9.6s;
	--cf-dancer-delay: -2.1s;
}
.cf-community-hero__dancer--3 {
	bottom: 14%;
	left: 7%;
	width: clamp(46px, 7.5vw, 72px);
	--cf-dancer-dur: 7.2s;
	--cf-dancer-delay: -4.4s;
}
.cf-community-hero__dancer--4 {
	bottom: 12%;
	right: 6%;
	width: clamp(50px, 8.5vw, 80px);
	--cf-dancer-dur: 10.1s;
	--cf-dancer-delay: -1.2s;
}
.cf-community-hero__dancer--5 {
	top: 42%;
	left: 2%;
	width: clamp(44px, 7vw, 68px);
	--cf-dancer-dur: 8.8s;
	--cf-dancer-delay: -5.6s;
}
.cf-community-hero__freq {
	position: absolute;
	inset: 0;
	pointer-events: none;
	z-index: 0;
	background-image: repeating-linear-gradient(
		to bottom,
		transparent 0,
		transparent 9px,
		rgba(255, 183, 0, 0.14) 9px,
		rgba(255, 183, 0, 0.14) 10px
	);
	-webkit-mask-image: radial-gradient(ellipse 58% 48% at 50% 52%, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.55) 32%, transparent 72%);
	mask-image: radial-gradient(ellipse 58% 48% at 50% 52%, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.55) 32%, transparent 72%);
	opacity: 0.85;
}
.cf-community-hero__eq {
	position: absolute;
	left: 0;
	right: 0;
	bottom: 0;
	height: 72px;
	display: flex;
	align-items: flex-end;
	justify-content: center;
	gap: 3px;
	padding: 0 4%;
	pointer-events: none;
	z-index: 0;
	-webkit-mask-image: linear-gradient(to top, #000 18%, transparent 100%);
	mask-image: linear-gradient(to top, #000 18%, transparent 100%);
}
.cf-community-hero__eq-bar {
	display: block;
	flex: 1 1 0;
	max-width: 8px;
	min-width: 2px;
	height: var(--cf-eq-h, 40%);
	border-radius: 1px 1px 0 0;
	background: var(--cf-accent, #FFB700);
	opacity: 0.28;
	transform-origin: bottom center;
	animation: cfCommunityEqPulse var(--cf-eq-dur, 1.6s) ease-in-out infinite;
	animation-delay: var(--cf-eq-delay, 0s);
}
@keyframes cfCommunityEqPulse {
	0%, 100% { transform: scaleY(0.35); opacity: 0.16; }
	35% { transform: scaleY(1); opacity: 0.34; }
	65% { transform: scaleY(0.55); opacity: 0.22; }
}
.cf-community-hero__content {
	position: relative;
	z-index: 1;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 14px;
}
.cf-community-hero__badge {
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
.cf-community-hero__title {
	margin: 0;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: clamp(28px, 5vw, 40px);
	font-weight: 700;
	line-height: 1.15;
	color: #fff;
}
.cf-community-hero__word {
	display: inline-block;
	color: #fff;
	animation: cfCommunityWordFlicker 7s ease-in-out infinite;
	animation-delay: var(--cf-word-delay, 0s);
}
@keyframes cfCommunityWordFlicker {
	0%, 10%, 100% {
		color: #fff;
		text-shadow: none;
	}
	4%, 7% {
		color: #FFD060;
		text-shadow: 0 0 16px rgba(255, 183, 0, 0.55), 0 0 32px rgba(255, 183, 0, 0.22);
	}
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
	margin-bottom: 8px;
	min-width: 0;
}
.cf-community-section__label-row {
	display: flex;
	align-items: center;
	gap: 14px;
	margin-bottom: 8px;
}
.cf-community-section__eyebrow {
	margin: 0;
	flex-shrink: 0;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 11px;
	letter-spacing: 0.1em;
	text-transform: uppercase;
	color: var(--cf-accent, #FFB700);
}
.cf-community-section__line {
	display: block;
	flex: 1 1 auto;
	height: 1px;
	background: #2a2a2a;
	min-width: 24px;
}
.cf-community-section__desc {
	margin-bottom: 4px;
}
.cf-community-list {
	display: flex;
	flex-direction: column;
	min-width: 0;
}
.cf-community-row {
	position: relative;
	display: flex;
	align-items: center;
	gap: 16px;
	padding: 18px 4px;
	border-bottom: 1px solid #1e1e1e;
	color: inherit;
	text-decoration: none;
	text-align: left;
	min-width: 0;
	max-width: 100%;
	box-sizing: border-box;
	transition: background 0.18s ease, border-color 0.18s ease;
}
.cf-community-row:first-child {
	border-top: 1px solid #1e1e1e;
}
a.cf-community-row:hover,
a.cf-community-row:focus-visible {
	background: rgba(255, 183, 0, 0.04);
}
a.cf-community-row:hover .cf-community-row__icon,
a.cf-community-row:focus-visible .cf-community-row__icon,
a.cf-community-row:hover .cf-community-row__cta,
a.cf-community-row:focus-visible .cf-community-row__cta {
	color: var(--cf-accent, #FFB700);
}
.cf-community-row--featured {
	margin: 4px 0 8px;
	padding: 18px 14px;
	border: 1px solid rgba(255, 183, 0, 0.32);
	border-radius: 10px;
	background: rgba(255, 183, 0, 0.06);
}
.cf-community-row--featured:first-child {
	border-top: 1px solid rgba(255, 183, 0, 0.32);
}
.cf-community-row--featured + .cf-community-row {
	border-top: none;
}
a.cf-community-row--featured:hover,
a.cf-community-row--featured:focus-visible {
	background: rgba(255, 183, 0, 0.1);
	border-color: rgba(255, 183, 0, 0.45);
}
.cf-community-row__icon {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 36px;
	height: 36px;
	min-width: 36px;
	color: #9a9a9a;
	font-size: 18px;
	flex-shrink: 0;
	transition: color 0.18s ease;
}
.cf-community-row--featured .cf-community-row__icon {
	color: var(--cf-accent, #FFB700);
}
.cf-community-row__body {
	display: flex;
	flex-direction: column;
	gap: 4px;
	min-width: 0;
	flex: 1;
}
.cf-community-row__title-wrap {
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	gap: 8px 10px;
}
.cf-community-row__title {
	margin: 0;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 14.5px;
	font-weight: 700;
	color: #fff;
	overflow-wrap: anywhere;
}
.cf-community-row__badge {
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 9px;
	font-weight: 700;
	letter-spacing: 0.06em;
	text-transform: uppercase;
	color: #0D0D0D;
	background: var(--cf-accent, #FFB700);
	padding: 3px 8px;
	border-radius: 999px;
	white-space: nowrap;
}
.cf-community-row__desc {
	margin: 0;
	font-size: 12.5px;
	line-height: 1.55;
	color: #7A7A7A;
}
.cf-community-row__cta {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	flex-shrink: 0;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 11.5px;
	font-weight: 700;
	letter-spacing: 0.04em;
	color: #8a8a8a;
	transition: color 0.18s ease;
}
.cf-community-row__cta svg {
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
.cf-community-cta__dots {
	position: absolute;
	inset: 0;
	opacity: 0.42;
	pointer-events: none;
	background-image: radial-gradient(circle, rgba(255, 183, 0, 0.32) 1px, transparent 1.4px);
	background-size: 26px 26px;
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
@media (prefers-reduced-motion: reduce) {
	.cf-community-hero__eq-bar {
		animation: none;
		transform: scaleY(0.65);
		opacity: 0.22;
	}
	.cf-community-hero__word {
		animation: none;
		color: #fff;
		text-shadow: none;
	}
	.cf-community-hero__border {
		animation: none;
		--cf-hero-border-angle: 210deg;
		filter: drop-shadow(0 0 3px rgba(255, 183, 0, 0.2));
		opacity: 0.55;
	}
	.cf-community-hero__center-glow {
		animation: none;
		opacity: 0.4;
		transform: translate(-50%, -50%) scale(0.95);
	}
	.cf-community-hero__dancer {
		animation: none;
		opacity: 0.08;
	}
}
@media (max-width: 1023px) {
	.cf-community-page {
		padding-bottom: 5px;
	}
}
@media (max-width: 767px) {
	.cf-community-page {
		padding: 24px 5px 5px;
	}
	.cf-community-page__inner {
		gap: 40px;
	}
	.cf-community-hero {
		text-align: center;
	}
	.cf-community-hero__content {
		align-items: center;
	}
	.cf-community-hero__dancer--5 {
		display: none;
	}
	.cf-community-hero__dancer {
		width: clamp(40px, 14vw, 64px);
	}
	.cf-community-hero__actions,
	.cf-community-cta__actions {
		justify-content: center;
		width: 100%;
	}
	.cf-community-row {
		flex-wrap: wrap;
		gap: 12px;
		padding: 16px 8px;
	}
	.cf-community-row--featured {
		padding: 16px 12px;
	}
	.cf-community-row__cta {
		margin-left: 52px;
		width: calc(100% - 52px);
	}
	.cf-community-cta {
		flex-direction: column;
		align-items: flex-start;
	}
	.cf-community-cta__actions {
		justify-content: flex-start;
	}
}
</style>

<?php
get_footer();
