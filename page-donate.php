<?php
/**
 * Template Name: Donate
 * Description: Theme template for the Donation page.
 *
 * @package Collective_Finity
 */

$cf_hero_title = __( 'Support Collective Finity', 'collective-finity' );
$cf_hero_words = preg_split( '/\s+/u', trim( $cf_hero_title ) );
if ( ! is_array( $cf_hero_words ) ) {
	$cf_hero_words = array( $cf_hero_title );
}

get_header();
?>

<main id="primary" class="site-main cf-donate-page" role="main">

	<section class="cf-donate-hero" aria-labelledby="cf-donate-hero-heading">
		<div class="cf-donate-hero__grid" aria-hidden="true"></div>
		<div class="cf-donate-hero__border" aria-hidden="true"></div>
		<div class="cf-donate-hero__center-glow" aria-hidden="true"></div>
		<div class="cf-donate-hero__icons" aria-hidden="true">
			<?php
			$cf_donate_icon_uri  = get_template_directory_uri() . '/assets/images/donate/';
			$cf_donate_icon_imgs = array(
				'donate-icon-hand-heart-coin.png',
				'donate-icon-handshake-linear.png',
				'donate-icon-tap-donate.png',
				'donate-icon-donate-button-solid.png',
				'donate-icon-support-handshake-solid.png',
			);
			foreach ( $cf_donate_icon_imgs as $cf_icon_i => $cf_icon_file ) :
				?>
				<span
					class="cf-donate-hero__icon cf-donate-hero__icon--<?php echo esc_attr( (string) ( $cf_icon_i + 1 ) ); ?>"
					style="--cf-donate-icon-img: url('<?php echo esc_url( $cf_donate_icon_uri . $cf_icon_file ); ?>');"
				></span>
			<?php endforeach; ?>
		</div>
		<div class="cf-donate-hero__content">
			<span class="cf-donate-hero__badge"><?php esc_html_e( 'Support the platform', 'collective-finity' ); ?></span>
			<h1 id="cf-donate-hero-heading" class="cf-donate-hero__title">
				<?php foreach ( $cf_hero_words as $cf_word_i => $cf_word ) : ?>
					<?php if ( $cf_word_i > 0 ) : ?>
						<?php echo ' '; ?>
					<?php endif; ?>
					<span class="cf-donate-hero__word" style="--cf-word-delay: <?php echo esc_attr( (string) ( $cf_word_i * 0.5 ) ); ?>s"><?php echo esc_html( $cf_word ); ?></span>
				<?php endforeach; ?>
			</h1>
			<p class="cf-donate-hero__lead">
				<?php esc_html_e( 'Your contribution helps keep the platform running — hosting releases, maintaining the music library, and growing the community for creators and listeners alike.', 'collective-finity' ); ?>
			</p>
		</div>
	</section>

	<section class="cf-donate-split" aria-label="<?php esc_attr_e( 'Support Collective Finity', 'collective-finity' ); ?>">
		<div class="cf-donate-split__form">
			<?php echo do_shortcode( '[cf_donation_form]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="cf-donate-split__right">
			<div class="cf-donate-split__copy">
				<h2 class="cf-donate-split__title"><?php esc_html_e( 'Make Music Infinite', 'collective-finity' ); ?></h2>
				<p class="cf-donate-split__text">
					<?php esc_html_e( 'Every contribution you make fuels something bigger than music — it fuels freedom, creativity, and boundless possibility.', 'collective-finity' ); ?>
				</p>
				<p class="cf-donate-split__text">
					<?php esc_html_e( 'Your support keeps Collective Finity alive. It means we can host thousands of tracks, publish in-depth guides for producers navigating AI composition, and build a thriving community where creators and listeners connect without walls or limits.', 'collective-finity' ); ?>
				</p>
				<p class="cf-donate-split__text">
					<?php esc_html_e( 'When you support us, you\'re not just enabling a platform — you\'re saying "I believe in a world where imagination has no ceiling." You\'re part of something that matters.', 'collective-finity' ); ?>
				</p>
				<p class="cf-donate-split__text cf-donate-split__text--signoff">
					<?php esc_html_e( 'Thank you for making music infinite.', 'collective-finity' ); ?>
				</p>
			</div>
			<?php collective_finity_render_donate_leadscreen(); ?>
		</div>
	</section>

	<div class="cf-donate-page__inner">
		<section class="cf-donate-wall" aria-label="<?php esc_attr_e( 'Donor wall', 'collective-finity' ); ?>">
			<h2 class="cf-donate-wall__title"><?php esc_html_e( 'Thank You', 'collective-finity' ); ?></h2>
			<p class="cf-donate-wall__subtitle"><?php esc_html_e( 'Every one of these supporters helps keep Collective Finity alive.', 'collective-finity' ); ?></p>
			<?php echo do_shortcode( '[cf_donor_wall]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</section>
	</div>

</main>

<style>
.cf-donate-page {
	background: var(--darker-bg, #050505);
	color: #fff;
	padding: 48px 5px 5px;
	box-sizing: border-box;
	width: 100%;
	max-width: 100%;
	min-width: 0;
	overflow-wrap: anywhere;
}
.cf-donate-page__inner {
	width: 100%;
	max-width: min(720px, 100%);
	min-width: 0;
	margin: 40px auto 0;
	display: flex;
	flex-direction: column;
	gap: 40px;
}
.cf-donate-hero {
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
.cf-donate-hero__grid {
	position: absolute;
	inset: 0;
	z-index: 0;
	pointer-events: none;
	opacity: 0.5;
	background-image:
		linear-gradient(rgba(255, 183, 0, 0.07) 1px, transparent 1px),
		linear-gradient(90deg, rgba(255, 183, 0, 0.07) 1px, transparent 1px);
	background-size: 32px 32px;
	-webkit-mask-image: radial-gradient(ellipse at center, black 35%, transparent 78%);
	mask-image: radial-gradient(ellipse at center, black 35%, transparent 78%);
}
@property --cf-donate-border-angle {
	syntax: '<angle>';
	initial-value: 0deg;
	inherits: false;
}
.cf-donate-hero__border {
	position: absolute;
	inset: 0;
	border-radius: inherit;
	padding: 1.5px;
	pointer-events: none;
	z-index: 2;
	background: conic-gradient(
		from var(--cf-donate-border-angle),
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
	animation: cfDonateBorderTravel 5.5s linear infinite;
	filter: drop-shadow(0 0 6px rgba(255, 183, 0, 0.35));
}
@keyframes cfDonateBorderTravel {
	to { --cf-donate-border-angle: 360deg; }
}
.cf-donate-hero__center-glow {
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
	animation: cfDonateCenterGlow 8.2s ease-in-out infinite;
	will-change: transform, opacity;
}
@keyframes cfDonateCenterGlow {
	0%, 100% {
		opacity: 0.35;
		transform: translate(-50%, -50%) scale(0.82);
	}
	50% {
		opacity: 0.7;
		transform: translate(-50%, -50%) scale(1.08);
	}
}
.cf-donate-hero__icons {
	position: absolute;
	inset: 0;
	pointer-events: none;
	z-index: 0;
	overflow: hidden;
}
.cf-donate-hero__icon {
	position: absolute;
	display: block;
	width: clamp(52px, 9vw, 88px);
	aspect-ratio: 1;
	opacity: 0;
	background: linear-gradient(160deg, #c45a00 0%, var(--cf-accent, #FFB700) 48%, #FFE08A 100%);
	-webkit-mask-image: var(--cf-donate-icon-img);
	mask-image: var(--cf-donate-icon-img);
	-webkit-mask-size: contain;
	mask-size: contain;
	-webkit-mask-repeat: no-repeat;
	mask-repeat: no-repeat;
	-webkit-mask-position: center;
	mask-position: center;
	filter: saturate(1.15) brightness(0.95);
	animation: cfDonateIconPulse var(--cf-donate-icon-dur, 7.5s) ease-in-out infinite;
	animation-delay: var(--cf-donate-icon-delay, 0s);
}
@keyframes cfDonateIconPulse {
	0%, 100% { opacity: 0; }
	18%, 42% { opacity: 0.14; }
	30% { opacity: 0.2; }
	55%, 100% { opacity: 0; }
}
.cf-donate-hero__icon--1 {
	top: 10%;
	left: 4%;
	--cf-donate-icon-dur: 8.4s;
	--cf-donate-icon-delay: 0s;
}
.cf-donate-hero__icon--2 {
	top: 8%;
	right: 5%;
	width: clamp(48px, 8vw, 78px);
	--cf-donate-icon-dur: 9.6s;
	--cf-donate-icon-delay: -2.1s;
}
.cf-donate-hero__icon--3 {
	bottom: 14%;
	left: 7%;
	width: clamp(46px, 7.5vw, 72px);
	--cf-donate-icon-dur: 7.2s;
	--cf-donate-icon-delay: -4.4s;
}
.cf-donate-hero__icon--4 {
	bottom: 12%;
	right: 6%;
	width: clamp(50px, 8.5vw, 80px);
	--cf-donate-icon-dur: 10.1s;
	--cf-donate-icon-delay: -1.2s;
}
.cf-donate-hero__icon--5 {
	top: 42%;
	left: 2%;
	width: clamp(44px, 7vw, 68px);
	--cf-donate-icon-dur: 8.8s;
	--cf-donate-icon-delay: -5.6s;
}
.cf-donate-hero__content {
	position: relative;
	z-index: 1;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 14px;
}
.cf-donate-hero__badge {
	display: inline-block;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 11px;
	letter-spacing: 0.1em;
	text-transform: uppercase;
	color: var(--primary-color, #FFB700);
	border: 1px solid rgba(255, 183, 0, 0.35);
	border-radius: 999px;
	padding: 7px 16px;
	background: rgba(255, 183, 0, 0.08);
}
.cf-donate-hero__title {
	margin: 0;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: clamp(28px, 5vw, 40px);
	font-weight: 700;
	line-height: 1.15;
	color: #fff;
}
.cf-donate-hero__word {
	display: inline-block;
	color: #fff;
	animation: cfDonateWordFlicker 7s ease-in-out infinite;
	animation-delay: var(--cf-word-delay, 0s);
}
@keyframes cfDonateWordFlicker {
	0%, 10%, 100% {
		color: #fff;
		text-shadow: none;
	}
	4%, 7% {
		color: #FFD060;
		text-shadow: 0 0 16px rgba(255, 183, 0, 0.55), 0 0 32px rgba(255, 183, 0, 0.22);
	}
}
.cf-donate-hero__lead {
	margin: 0;
	max-width: 520px;
	font-size: 14px;
	line-height: 1.7;
	color: var(--cf-text-2, #B3B3B3);
}
.cf-donate-split {
	width: 100%;
	max-width: 100%;
	box-sizing: border-box;
	margin-top: 40px;
	display: grid;
	grid-template-columns: 1fr;
	gap: 32px;
}
@media (min-width: 860px) {
	.cf-donate-split {
		grid-template-columns: minmax(0, 460px) minmax(0, 1fr);
		align-items: stretch;
	}
}
.cf-donate-split__form {
	min-width: 0;
}
.cf-donate-split__right {
	display: flex;
	flex-direction: column;
	gap: 16px;
	min-width: 0;
	height: 100%;
}
.cf-donate-split__copy {
	flex: 0 0 auto;
	min-width: 0;
	display: flex;
	flex-direction: column;
	gap: 16px;
	padding: clamp(28px, 4vw, 40px);
	border-radius: 18px;
	background: linear-gradient(160deg, rgba(255, 183, 0, 0.1), rgba(255, 183, 0, 0.02));
	border: 1px solid rgba(255, 183, 0, 0.18);
}
.cf-donate-split__title {
	margin: 0;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: clamp(22px, 2.6vw, 30px);
	color: #fff;
}
.cf-donate-split__text {
	margin: 0;
	font-size: 15px;
	line-height: 1.7;
	color: rgba(255, 255, 255, 0.72);
}
.cf-donate-split__text--signoff {
	color: var(--cf-accent, #FFB700);
	font-weight: 600;
}

/* Lead Screen panel — LED display look */
.cf-leadscreen {
	position: relative;
	flex: 1 1 auto;
	min-height: 140px;
	border-radius: 18px;
	overflow: hidden;
	background: radial-gradient(ellipse at center, #0d0d0d 0%, #060606 70%, #030303 100%);
	border: 1px solid rgba(255, 183, 0, 0.2);
	display: flex;
	box-shadow:
		inset 0 0 50px rgba(0, 0, 0, 0.85),
		inset 0 0 3px rgba(255, 183, 0, 0.25),
		inset 0 1px 0 rgba(255, 255, 255, 0.05),
		0 10px 30px rgba(0, 0, 0, 0.45);
}

/* Pixel-sized grid — LED matrix feel */
.cf-leadscreen__grid {
	position: absolute;
	inset: 0;
	background-image:
		linear-gradient(rgba(255, 183, 0, 0.12) 1px, transparent 1px),
		linear-gradient(90deg, rgba(255, 183, 0, 0.12) 1px, transparent 1px);
	background-size: 8px 8px;
	-webkit-mask-image: radial-gradient(ellipse at center, black 40%, transparent 88%);
	mask-image: radial-gradient(ellipse at center, black 40%, transparent 88%);
	pointer-events: none;
	z-index: 1;
}

/* Fine CRT scanlines for extra depth */
.cf-leadscreen__scanlines {
	position: absolute;
	inset: 0;
	background: repeating-linear-gradient(
		to bottom,
		rgba(0, 0, 0, 0.4) 0px,
		rgba(0, 0, 0, 0.4) 1px,
		transparent 2px,
		transparent 3px
	);
	mix-blend-mode: multiply;
	pointer-events: none;
	opacity: 0.55;
	z-index: 2;
}

/* Glass reflection sheen — top-left light, bottom-right shadow */
.cf-leadscreen__glass {
	position: absolute;
	inset: 0;
	background: linear-gradient(135deg, rgba(255, 255, 255, 0.06) 0%, transparent 30%, transparent 70%, rgba(0, 0, 0, 0.3) 100%);
	pointer-events: none;
	z-index: 3;
}

.cf-leadscreen__track,
.cf-leadscreen__cycle {
	position: relative;
	z-index: 4;
	width: 100%;
	overflow: hidden;
	display: flex;
	flex-direction: column;
}

.cf-leadscreen--pos-top .cf-leadscreen__track,
.cf-leadscreen--pos-top .cf-leadscreen__cycle { justify-content: flex-start; padding-top: 20px; }
.cf-leadscreen--pos-middle .cf-leadscreen__track,
.cf-leadscreen--pos-middle .cf-leadscreen__cycle { justify-content: center; }
.cf-leadscreen--pos-bottom .cf-leadscreen__track,
.cf-leadscreen--pos-bottom .cf-leadscreen__cycle { justify-content: flex-end; padding-bottom: 20px; }

.cf-leadscreen__row {
	display: flex;
	align-items: center;
	gap: 48px;
	white-space: nowrap;
	animation: cfLeadscreenScroll 22s linear infinite;
	padding: 0 24px;
}

.cf-leadscreen__msg {
	font-family: var(--cf-mono-font, 'Space Mono', monospace);
	font-size: 15px;
	letter-spacing: 0.06em;
	color: #FFC94D;
	text-shadow: 0 0 6px rgba(255, 183, 0, 0.65), 0 0 14px rgba(255, 183, 0, 0.35);
}

@keyframes cfLeadscreenScroll {
	from { transform: translateX(0); }
	to   { transform: translateX(-50%); }
}

.cf-leadscreen__cycle {
	align-items: center;
	justify-content: center;
	padding: 0 24px;
	text-align: center;
	perspective: 600px;
}
.cf-leadscreen__cycle .cf-leadscreen__msg {
	position: absolute;
	font-size: 16px;
	opacity: 0;
}

/* Fade */
.cf-leadscreen--fade .cf-leadscreen__msg {
	animation: cfLeadscreenFade 15s ease-in-out infinite;
}
@keyframes cfLeadscreenFade {
	0%   { opacity: 0; }
	3%   { opacity: 1; }
	16%  { opacity: 1; }
	18%  { opacity: 0; }
	100% { opacity: 0; }
}

/* Glitch */
.cf-leadscreen--glitch .cf-leadscreen__msg {
	animation: cfLeadscreenGlitch 15s steps(1) infinite;
}
@keyframes cfLeadscreenGlitch {
	0%    { opacity: 0; transform: translate(0,0); text-shadow: none; }
	3%    { opacity: 1; transform: translate(-2px, 1px); text-shadow: 2px 0 #ff2b6b, -2px 0 #00e5ff; }
	4%    { transform: translate(2px, -1px); text-shadow: -2px 0 #ff2b6b, 2px 0 #00e5ff; }
	5%    { transform: translate(0,0); text-shadow: 0 0 8px rgba(255,183,0,0.7); }
	16%   { opacity: 1; transform: translate(0,0); text-shadow: 0 0 8px rgba(255,183,0,0.7); }
	17%   { opacity: 1; transform: translate(2px,0); text-shadow: 2px 0 #ff2b6b, -2px 0 #00e5ff; }
	18%   { opacity: 0; transform: translate(0,0); text-shadow: none; }
	100%  { opacity: 0; }
}

/* Slide up */
.cf-leadscreen--slide-up .cf-leadscreen__msg {
	animation: cfLeadscreenSlideUp 15s ease-in-out infinite;
}
@keyframes cfLeadscreenSlideUp {
	0%   { opacity: 0; transform: translateY(24px); }
	3%   { opacity: 1; transform: translateY(0); }
	16%  { opacity: 1; transform: translateY(0); }
	18%  { opacity: 0; transform: translateY(-24px); }
	100% { opacity: 0; }
}

/* Zoom pulse */
.cf-leadscreen--zoom-pulse .cf-leadscreen__msg {
	animation: cfLeadscreenZoomPulse 15s ease-in-out infinite;
}
@keyframes cfLeadscreenZoomPulse {
	0%   { opacity: 0; transform: scale(0.7); }
	3%   { opacity: 1; transform: scale(1.08); }
	5%   { transform: scale(1); }
	16%  { opacity: 1; transform: scale(1); }
	18%  { opacity: 0; transform: scale(1.15); }
	100% { opacity: 0; }
}

/* Flip */
.cf-leadscreen--flip .cf-leadscreen__msg {
	animation: cfLeadscreenFlip 15s ease-in-out infinite;
	transform-origin: center;
	backface-visibility: hidden;
}
@keyframes cfLeadscreenFlip {
	0%   { opacity: 0; transform: rotateX(90deg); }
	3%   { opacity: 1; transform: rotateX(0deg); }
	16%  { opacity: 1; transform: rotateX(0deg); }
	18%  { opacity: 0; transform: rotateX(-90deg); }
	100% { opacity: 0; }
}

/* Neon flicker */
.cf-leadscreen--neon-flicker .cf-leadscreen__msg {
	animation: cfLeadscreenNeonFlicker 15s ease-in-out infinite;
	text-shadow: 0 0 6px rgba(255,183,0,0.85), 0 0 16px rgba(255,183,0,0.5);
}
@keyframes cfLeadscreenNeonFlicker {
	0%    { opacity: 0; }
	3%    { opacity: 1; }
	3.6%  { opacity: 0.4; }
	4.2%  { opacity: 1; }
	4.5%  { opacity: 0.3; }
	4.8%  { opacity: 1; }
	16%   { opacity: 1; }
	16.6% { opacity: 0.3; }
	17%   { opacity: 1; }
	18%   { opacity: 0; }
	100%  { opacity: 0; }
}

/* Blur focus */
.cf-leadscreen--blur-focus .cf-leadscreen__msg {
	animation: cfLeadscreenBlurFocus 15s ease-in-out infinite;
}
@keyframes cfLeadscreenBlurFocus {
	0%   { opacity: 0; filter: blur(8px); }
	3%   { opacity: 1; filter: blur(0); }
	16%  { opacity: 1; filter: blur(0); }
	18%  { opacity: 0; filter: blur(8px); }
	100% { opacity: 0; }
}

/* Wave (letter-by-letter bounce) */
.cf-leadscreen__msg--wave {
	position: absolute;
	animation: cfLeadscreenWaveShow 15s ease-in-out infinite;
}
@keyframes cfLeadscreenWaveShow {
	0%   { opacity: 0; }
	3%   { opacity: 1; }
	16%  { opacity: 1; }
	18%  { opacity: 0; }
	100% { opacity: 0; }
}
.cf-leadscreen__letter {
	display: inline-block;
	animation: cfLeadscreenWaveBounce 1.2s ease-in-out infinite;
	animation-delay: calc(var(--i, 0) * 0.05s);
}
@keyframes cfLeadscreenWaveBounce {
	0%, 100% { transform: translateY(0); }
	50%      { transform: translateY(-6px); }
}

/* Typewriter (character reveal) */
.cf-leadscreen__msg--typewriter {
	position: absolute;
	display: inline-block;
	overflow: hidden;
	white-space: nowrap;
	max-width: 0;
	border-right: 2px solid #FFC94D;
	animation: cfLeadscreenTypewriter 15s steps(var(--cf-chars, 24), end) infinite;
}
@keyframes cfLeadscreenTypewriter {
	0%   { opacity: 0; max-width: 0; }
	3%   { opacity: 1; max-width: 0; }
	12%  { opacity: 1; max-width: 100%; }
	16%  { opacity: 1; max-width: 100%; }
	18%  { opacity: 0; max-width: 100%; }
	100% { opacity: 0; max-width: 0; }
}

@media (max-width: 859px) {
	.cf-donate-split__right {
		height: auto;
	}
	.cf-leadscreen {
		min-height: 90px;
	}
}
.cf-donate-wall {
	text-align: center;
	display: flex;
	flex-direction: column;
	gap: 8px;
}
.cf-donate-wall__title {
	margin: 0;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: clamp(22px, 2.6vw, 30px);
	color: #fff;
}
.cf-donate-wall__subtitle {
	margin: 0 0 12px;
	font-size: 14px;
	color: rgba(255, 255, 255, 0.55);
}

/* Donation form — styles target plugin shortcode markup only */
.cf-donation-page.cf-page-wrap {
	padding: 0;
	max-width: 100%;
	min-width: 0;
}
.cf-donation-page .cf-card {
	background: var(--cf-bg-card, #141414);
	border: var(--cf-card-border-width, 1px) solid var(--cf-border, #232323);
	border-radius: var(--cf-card-radius, 14px);
	padding: clamp(24px, 4vw, 36px);
	box-shadow: var(--cf-card-shadow, 0 14px 28px -12px rgba(0, 0, 0, 0.55));
	box-sizing: border-box;
	min-width: 0;
}
.cf-donation-page .cf-card h2,
.cf-donation-page .cf-card h3 {
	margin: 0 0 20px;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: clamp(18px, 3vw, 22px);
	font-weight: 700;
	color: #fff;
}
.cf-donation-page #cf-donation-form {
	display: flex;
	flex-direction: column;
	gap: 16px;
}
.cf-donation-page .cf-field {
	display: flex;
	flex-direction: column;
	gap: 6px;
	min-width: 0;
}
.cf-donation-page .cf-field label {
	display: block;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 11px;
	font-weight: 700;
	letter-spacing: 0.06em;
	text-transform: uppercase;
	color: var(--primary-color, #FFB700);
}
.cf-donation-page .cf-field input[type="text"],
.cf-donation-page .cf-field input[type="email"],
.cf-donation-page .cf-field input[type="number"],
.cf-donation-page .cf-field input[type="tel"],
.cf-donation-page .cf-field textarea,
.cf-donation-page .cf-field select {
	width: 100%;
	padding: 11px 14px;
	border: 1px solid var(--primary-color, #FFB700);
	border-radius: 9px;
	background: var(--secondary-color, #0D0D0D);
	color: #fff;
	font-size: 13.5px;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	box-sizing: border-box;
	outline: none;
	transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.cf-donation-page .cf-field input:hover,
.cf-donation-page .cf-field textarea:hover,
.cf-donation-page .cf-field select:hover {
	border-color: var(--cf-accent-hover, #ffde99);
}
.cf-donation-page .cf-field input:focus,
.cf-donation-page .cf-field textarea:focus,
.cf-donation-page .cf-field select:focus {
	border-color: var(--cf-accent-hover, #ffde99);
	box-shadow: 0 0 0 2px rgba(255, 183, 0, 0.15);
}
.cf-donation-page .cf-field input::placeholder,
.cf-donation-page .cf-field textarea::placeholder {
	color: var(--cf-accent-hover, #ffde99);
	opacity: 0.7;
}
.cf-donation-page .cf-field textarea {
	resize: vertical;
	min-height: 100px;
}
.cf-donation-page .cf-donation-amount label {
	font-size: 12px;
}
.cf-donation-page .cf-donation-amount input {
	font-size: clamp(18px, 3vw, 22px);
	font-weight: 700;
	padding: 14px 16px;
	letter-spacing: 0.02em;
}
.cf-donation-page .cf-checkbox-label {
	display: flex;
	align-items: flex-start;
	gap: 10px;
	cursor: pointer;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 12.5px;
	line-height: 1.5;
	color: var(--cf-text-2, #B3B3B3);
	text-transform: none;
	letter-spacing: normal;
	font-weight: 400;
}
.cf-donation-page .cf-checkbox-label input[type="checkbox"] {
	width: 18px;
	height: 18px;
	min-width: 18px;
	margin: 2px 0 0;
	padding: 0;
	accent-color: var(--primary-color, #FFB700);
	cursor: pointer;
	flex-shrink: 0;
}
.cf-donation-page #cf-paypal-buttons {
	margin-top: 8px;
	padding-top: 8px;
	min-height: 48px;
}
.cf-donation-page .cf-message,
.cf-donation-page #cf-donation-message {
	margin: 0;
	padding: 12px 16px;
	border-radius: 9px;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 13px;
	line-height: 1.5;
}
.cf-donation-page .cf-message.is-error,
.cf-donation-page #cf-donation-message.is-error {
	background: rgba(220, 53, 69, 0.12);
	border: 1px solid rgba(220, 53, 69, 0.45);
	color: #ff8a96;
}
.cf-donation-page .cf-message.is-success,
.cf-donation-page #cf-donation-message.is-success {
	background: rgba(255, 183, 0, 0.1);
	border: 1px solid rgba(255, 183, 0, 0.35);
	color: var(--primary-color, #FFB700);
}
.cf-donation-page .cf-donation-thankyou,
.cf-donation-page #cf-donation-thankyou,
.cf-donation-page .cf-donation-processing,
.cf-donation-page #cf-donation-processing {
	text-align: center;
	padding: clamp(32px, 5vw, 48px) clamp(20px, 4vw, 32px);
	font-family: var(--cf-mono, 'Space Mono', monospace);
}
.cf-donation-page .cf-donation-thankyou,
.cf-donation-page #cf-donation-thankyou {
	color: #fff;
}
.cf-donation-page .cf-donation-thankyou h2,
.cf-donation-page .cf-donation-thankyou h3,
.cf-donation-page #cf-donation-thankyou h2,
.cf-donation-page #cf-donation-thankyou h3 {
	color: var(--primary-color, #FFB700);
	margin-bottom: 12px;
}
.cf-donation-page .cf-donation-thankyou p,
.cf-donation-page #cf-donation-thankyou p {
	margin: 0;
	font-size: 14px;
	line-height: 1.7;
	color: var(--cf-text-2, #B3B3B3);
}
.cf-donation-page .cf-donation-processing,
.cf-donation-page #cf-donation-processing {
	color: var(--cf-text-2, #B3B3B3);
	font-size: 14px;
	line-height: 1.6;
}

@media (prefers-reduced-motion: reduce) {
	.cf-donate-hero__word {
		animation: none;
		color: #fff;
		text-shadow: none;
	}
	.cf-donate-hero__border {
		animation: none;
		--cf-donate-border-angle: 210deg;
		filter: drop-shadow(0 0 3px rgba(255, 183, 0, 0.2));
		opacity: 0.55;
	}
	.cf-donate-hero__center-glow {
		animation: none;
		opacity: 0.4;
		transform: translate(-50%, -50%) scale(0.95);
	}
	.cf-donate-hero__icon {
		animation: none;
		opacity: 0.08;
	}
	.cf-leadscreen__row,
	.cf-leadscreen__cycle .cf-leadscreen__msg,
	.cf-leadscreen__letter {
		animation: none !important;
	}
	.cf-leadscreen__cycle .cf-leadscreen__msg {
		position: static;
		opacity: 1;
		display: none;
		max-width: none;
		filter: none;
		transform: none;
	}
	.cf-leadscreen__cycle .cf-leadscreen__msg:first-child {
		display: block;
	}
}
@media (max-width: 767px) {
	.cf-donate-page {
		padding: 24px 5px 5px;
	}
	.cf-donate-page__inner {
		gap: 32px;
	}
	.cf-donate-hero {
		text-align: left;
	}
	.cf-donate-hero__content {
		align-items: flex-start;
	}
	.cf-donate-hero__icon--5 {
		display: none;
	}
	.cf-donate-hero__icon {
		width: clamp(40px, 14vw, 64px);
	}
	.cf-donation-page .cf-card {
		padding: 20px 16px;
	}
	.cf-donation-page .cf-donation-amount input {
		font-size: 18px;
	}
}
</style>

<?php
get_footer();
