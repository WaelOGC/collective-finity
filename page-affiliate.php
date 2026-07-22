<?php
/**
 * Template Name: Affiliate & Rewards Program
 * Description: Theme template for the Affiliate & Rewards Program page.
 *
 * @package Collective_Finity
 */

get_header();

$cf_profile_rewards_url = is_user_logged_in()
	? home_url( '/cf-profile#rewards' )
	: home_url( '/cf-register' );
?>

<main id="primary" class="site-main cf-page-shell cf-affiliate-page">
	<div class="cf-page-container cf-affiliate">

		<!-- Hero -->
		<section class="cf-affiliate-hero" aria-labelledby="cf-affiliate-hero-heading">
			<div class="cf-affiliate-hero__border" aria-hidden="true"></div>
			<div class="cf-affiliate-hero__center-glow" aria-hidden="true"></div>
			<div class="cf-affiliate-hero__content">
				<span class="cf-affiliate-hero__badge"><?php esc_html_e( 'Earn as you listen', 'collective-finity' ); ?></span>
				<h1 id="cf-affiliate-hero-heading" class="cf-affiliate-hero__title">
					<?php esc_html_e( 'Affiliate & Rewards Program', 'collective-finity' ); ?>
				</h1>
				<p class="cf-affiliate-hero__lead">
					<?php esc_html_e( 'Earn Xfinity points by listening, reading, and inviting friends to Collective Finity — then turn them into real rewards.', 'collective-finity' ); ?>
				</p>
				<a href="<?php echo esc_url( $cf_profile_rewards_url ); ?>" class="cf-affiliate-cta">
					<?php echo is_user_logged_in()
						? esc_html__( 'Manage Your Referrals', 'collective-finity' )
						: esc_html__( 'Create Your Free Account', 'collective-finity' ); ?>
				</a>
			</div>
		</section>

		<!-- How it works -->
		<section class="cf-affiliate-section" aria-labelledby="cf-affiliate-how-heading">
			<h2 id="cf-affiliate-how-heading" class="cf-affiliate-section__title">
				<?php esc_html_e( 'How it works', 'collective-finity' ); ?>
			</h2>
			<div class="cf-affiliate-steps">
				<div class="cf-affiliate-step">
					<span class="cf-affiliate-step__num">1</span>
					<h3><?php esc_html_e( 'Earn Xfinity', 'collective-finity' ); ?></h3>
					<p><?php esc_html_e( 'Listen to tracks and read articles on the platform. Every minute of genuine activity earns you Xfinity points automatically.', 'collective-finity' ); ?></p>
				</div>
				<div class="cf-affiliate-step">
					<span class="cf-affiliate-step__num">2</span>
					<h3><?php esc_html_e( 'Invite friends', 'collective-finity' ); ?></h3>
					<p><?php esc_html_e( 'Share your personal referral link from your profile. When someone joins using your link, you both earn Xfinity.', 'collective-finity' ); ?></p>
				</div>
				<div class="cf-affiliate-step">
					<span class="cf-affiliate-step__num">3</span>
					<h3><?php esc_html_e( 'Unlock rewards', 'collective-finity' ); ?></h3>
					<p><?php esc_html_e( 'Reach point milestones to unlock exclusive gift codes and partner offers — no cash, just real perks.', 'collective-finity' ); ?></p>
				</div>
			</div>
		</section>

		<!-- Reward tiers -->
		<section class="cf-affiliate-section" aria-labelledby="cf-affiliate-tiers-heading">
			<h2 id="cf-affiliate-tiers-heading" class="cf-affiliate-section__title">
				<?php esc_html_e( 'Reward tiers', 'collective-finity' ); ?>
			</h2>
			<div class="cf-affiliate-tiers">
				<div class="cf-affiliate-tier">
					<span class="cf-affiliate-tier__amount">1,000</span>
					<span class="cf-affiliate-tier__label"><?php esc_html_e( 'Xfinity', 'collective-finity' ); ?></span>
					<p><?php esc_html_e( 'Unlock Tier 1 rewards', 'collective-finity' ); ?></p>
				</div>
				<div class="cf-affiliate-tier">
					<span class="cf-affiliate-tier__amount">5,000</span>
					<span class="cf-affiliate-tier__label"><?php esc_html_e( 'Xfinity', 'collective-finity' ); ?></span>
					<p><?php esc_html_e( 'Unlock Tier 2 rewards', 'collective-finity' ); ?></p>
				</div>
				<div class="cf-affiliate-tier">
					<span class="cf-affiliate-tier__amount">10,000</span>
					<span class="cf-affiliate-tier__label"><?php esc_html_e( 'Xfinity', 'collective-finity' ); ?></span>
					<p><?php esc_html_e( 'Unlock Tier 3 rewards', 'collective-finity' ); ?></p>
				</div>
			</div>
			<p class="cf-affiliate-tiers__note">
				<?php esc_html_e( 'Rewards are gift codes and exclusive offers from partner platforms — never cash. Exact rewards are added as new partnerships go live.', 'collective-finity' ); ?>
			</p>
		</section>

		<!-- Future: NovaXfinity -->
		<section class="cf-affiliate-section cf-affiliate-future" aria-labelledby="cf-affiliate-future-heading">
			<h2 id="cf-affiliate-future-heading" class="cf-affiliate-section__title">
				<?php esc_html_e( 'Keep your Xfinity for what\'s next', 'collective-finity' ); ?>
			</h2>
			<p>
				<?php esc_html_e( 'Collective Finity is building NovaXfinity, a future music and AI platform. If you hold on to your Xfinity instead of exchanging it now, you\'ll be able to claim exclusive VIP access and artist benefits when NovaXfinity launches.', 'collective-finity' ); ?>
			</p>
			<span class="cf-affiliate-future__badge"><?php esc_html_e( 'Coming soon', 'collective-finity' ); ?></span>
		</section>

	</div>
</main>

<style>
.cf-affiliate-page .cf-page-container.cf-affiliate {
	max-width: 100%;
}
.cf-affiliate-page .cf-affiliate {
	display: grid;
	gap: 56px;
}
.cf-affiliate-hero {
	position: relative;
	text-align: center;
	display: grid;
	gap: 16px;
	justify-items: center;
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
@property --cf-affiliate-hero-border-angle {
	syntax: '<angle>';
	initial-value: 0deg;
	inherits: false;
}
.cf-affiliate-hero__border {
	position: absolute;
	inset: 0;
	border-radius: inherit;
	padding: 1.5px;
	pointer-events: none;
	z-index: 2;
	background: conic-gradient(
		from var(--cf-affiliate-hero-border-angle),
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
	animation: cfAffiliateBorderTravel 5.5s linear infinite;
	filter: drop-shadow(0 0 6px rgba(255, 183, 0, 0.35));
}
@keyframes cfAffiliateBorderTravel {
	to { --cf-affiliate-hero-border-angle: 360deg; }
}
.cf-affiliate-hero__center-glow {
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
	animation: cfAffiliateCenterGlow 8.2s ease-in-out infinite;
	will-change: transform, opacity;
}
@keyframes cfAffiliateCenterGlow {
	0%, 100% {
		opacity: 0.35;
		transform: translate(-50%, -50%) scale(0.82);
	}
	50% {
		opacity: 0.7;
		transform: translate(-50%, -50%) scale(1.08);
	}
}
.cf-affiliate-hero__content {
	position: relative;
	z-index: 1;
	display: grid;
	gap: 16px;
	justify-items: center;
}
.cf-affiliate-hero__badge {
	display: inline-block;
	padding: 7px 16px;
	border-radius: 999px;
	background: rgba(255, 183, 0, 0.08);
	border: 1px solid rgba(255, 183, 0, 0.35);
	color: var(--cf-accent, #FFB700);
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 11px;
	letter-spacing: 0.1em;
	text-transform: uppercase;
}
.cf-affiliate-hero__title {
	color: #fff;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: clamp(28px, 5vw, 40px);
	font-weight: 700;
	line-height: 1.15;
	margin: 0;
}
.cf-affiliate-hero__lead {
	color: #B3B3B3;
	max-width: 620px;
	line-height: 1.7;
	font-size: 14px;
	margin: 0;
}
.cf-affiliate-cta {
	display: inline-block;
	margin-top: 8px;
	padding: 12px 28px;
	border-radius: 999px;
	background: #fff;
	color: #111;
	font-weight: 600;
	text-decoration: none;
	transition: opacity 0.2s ease;
}
.cf-affiliate-cta:hover {
	opacity: 0.85;
}
@media (prefers-reduced-motion: reduce) {
	.cf-affiliate-hero__border,
	.cf-affiliate-hero__center-glow {
		animation: none;
	}
}
.cf-affiliate-section__title {
	color: #fff;
	font-size: 1.5rem;
	margin: 0 0 24px;
	text-align: center;
}
.cf-affiliate-steps,
.cf-affiliate-tiers {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 20px;
}
.cf-affiliate-step,
.cf-affiliate-tier {
	background: rgba(255, 255, 255, 0.04);
	border: 1px solid rgba(255, 255, 255, 0.08);
	border-radius: 16px;
	padding: 24px;
	text-align: center;
}
.cf-affiliate-step__num {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 32px;
	height: 32px;
	border-radius: 50%;
	background: rgba(255, 255, 255, 0.1);
	color: #fff;
	font-weight: 700;
	margin-bottom: 12px;
}
.cf-affiliate-step h3 {
	color: #fff;
	margin: 0 0 8px;
	font-size: 1.05rem;
}
.cf-affiliate-step p,
.cf-affiliate-tier p {
	color: #b8b8b8;
	line-height: 1.7;
	margin: 0;
	font-size: 0.92rem;
}
.cf-affiliate-tier__amount {
	display: block;
	color: #fff;
	font-size: 1.8rem;
	font-weight: 700;
}
.cf-affiliate-tier__label {
	display: block;
	color: #999;
	font-size: 0.8rem;
	text-transform: uppercase;
	letter-spacing: 0.06em;
	margin-bottom: 10px;
}
.cf-affiliate-tiers__note {
	color: #999;
	font-size: 0.85rem;
	text-align: center;
	margin: 20px auto 0;
	max-width: 640px;
	line-height: 1.7;
}
.cf-affiliate-future {
	text-align: center;
	background: rgba(255, 255, 255, 0.03);
	border: 1px solid rgba(255, 255, 255, 0.08);
	border-radius: 20px;
	padding: 40px 24px;
}
.cf-affiliate-future p {
	color: #d2d2d2;
	max-width: 620px;
	margin: 0 auto 16px;
	line-height: 1.7;
}
.cf-affiliate-future__badge {
	display: inline-block;
	padding: 6px 14px;
	border-radius: 999px;
	background: rgba(255, 255, 255, 0.08);
	color: #fff;
	font-size: 0.75rem;
	letter-spacing: 0.04em;
	text-transform: uppercase;
}
@media (max-width: 782px) {
	.cf-affiliate-steps,
	.cf-affiliate-tiers {
		grid-template-columns: 1fr;
	}
}
</style>

<?php get_footer(); ?>
