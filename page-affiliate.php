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

$cf_tracks_url   = collective_finity_get_tracks_archive_base_url();
$cf_register_url = home_url( '/cf-register' );

$cf_theme_uri = get_template_directory_uri();
$cf_theme_dir = get_template_directory();

$cf_hero_rel = '/assets/images/section-background/cf-xfinity-rewards-hero.webp';
if ( ! file_exists( $cf_theme_dir . $cf_hero_rel ) ) {
	$cf_hero_rel = '';
}
$cf_hero_image_url = $cf_hero_rel ? $cf_theme_uri . $cf_hero_rel : '';

$cf_keep_rel = '/assets/images/section-background/cf-keep-your-xfinity.webp';
if ( ! file_exists( $cf_theme_dir . $cf_keep_rel ) ) {
	$cf_keep_rel = '';
}
$cf_keep_image_url = $cf_keep_rel ? $cf_theme_uri . $cf_keep_rel : '';

$cf_novax_rel = '/assets/images/section-background/cf-novaxfinity-coming-soon.webp';
if ( ! file_exists( $cf_theme_dir . $cf_novax_rel ) ) {
	$cf_novax_rel = '';
}
$cf_novax_image_url = $cf_novax_rel ? $cf_theme_uri . $cf_novax_rel : '';
?>

<main id="primary" class="site-main cf-page-shell cf-affiliate-page">
	<div class="cf-page-container cf-affiliate">

		<!-- Section 1: Hero -->
		<section
			class="cf-affiliate-hero<?php echo $cf_hero_image_url ? ' cf-affiliate-hero--has-image' : ''; ?>"
			aria-labelledby="cf-affiliate-hero-heading"
			<?php if ( $cf_hero_image_url ) : ?>
				style="--cf-affiliate-hero-image: url('<?php echo esc_url( $cf_hero_image_url ); ?>');"
			<?php endif; ?>
		>
			<?php if ( $cf_hero_image_url ) : ?>
				<div class="cf-affiliate-hero__media" aria-hidden="true"></div>
				<div class="cf-affiliate-hero__shade" aria-hidden="true"></div>
			<?php endif; ?>
			<div class="cf-affiliate-hero__border" aria-hidden="true"></div>
			<div class="cf-affiliate-hero__center-glow" aria-hidden="true"></div>
			<div class="cf-affiliate-hero__content">
				<span class="cf-affiliate-hero__badge">Xfinity Rewards</span>
				<h1 id="cf-affiliate-hero-heading" class="cf-affiliate-hero__title">
					Listen. Share. <span class="cf-affiliate-accent">Earn.</span>
				</h1>
				<p class="cf-affiliate-hero__lead">
					Xfinity Rewards is Collective Finity's loyalty program, built to reward every meaningful interaction across the platform. Every track you listen to and every friend you bring in moves your balance forward, laying the foundation for real rewards as the ecosystem grows.
				</p>
				<div class="cf-affiliate-actions">
					<a href="<?php echo esc_url( $cf_tracks_url ); ?>" class="cf-btn-primary-lg">Start Listening</a>
					<a href="#cf-affiliate-ways" class="cf-btn-ghost-lg">Learn More</a>
				</div>
			</div>
		</section>

		<!-- Section 2: Ways to Earn Xfinity -->
		<section id="cf-affiliate-ways" class="cf-affiliate-section" aria-labelledby="cf-affiliate-ways-heading">
			<h2 id="cf-affiliate-ways-heading" class="cf-affiliate-section__title">Ways to Earn Xfinity</h2>
			<p class="cf-affiliate-section__subtitle">Your Xfinity balance grows through genuine activity on the platform. Here is exactly how each channel works today.</p>
			<div class="cf-affiliate-grid cf-affiliate-grid--3">
				<div class="cf-affiliate-card">
					<h3>Listen to Music</h3>
					<p>Every genuine listening session on an original track contributes to your balance automatically in the background, so your everyday listening is never wasted.</p>
				</div>
				<div class="cf-affiliate-card">
					<h3>Invite Friends</h3>
					<p>Share your personal referral link from your account. Once your friend signs up and confirms their account, Xfinity is credited to both of you.</p>
				</div>
				<div class="cf-affiliate-card">
					<h3>More Ways Coming Soon</h3>
					<p>As Collective Finity grows, new features will open new ways to earn, from community activities to platform milestones.</p>
				</div>
			</div>
		</section>

		<!-- Section 3: How Referrals Work -->
		<section class="cf-affiliate-section" aria-labelledby="cf-affiliate-referrals-heading">
			<h2 id="cf-affiliate-referrals-heading" class="cf-affiliate-section__title">
				Simple. <span class="cf-affiliate-accent">Transparent.</span> Rewarding.
			</h2>
			<p class="cf-affiliate-section__subtitle">How the referral program works, step by step.</p>
			<div class="cf-affiliate-grid cf-affiliate-grid--3">
				<div class="cf-affiliate-card">
					<span class="cf-affiliate-step__num" aria-hidden="true">1</span>
					<h3>Share your link</h3>
					<p>Copy your unique referral link from your account and send it to friends however you like.</p>
				</div>
				<div class="cf-affiliate-card">
					<span class="cf-affiliate-step__num" aria-hidden="true">2</span>
					<h3>They create an account</h3>
					<p>Your friend signs up using your link and confirms their new Collective Finity account.</p>
				</div>
				<div class="cf-affiliate-card">
					<span class="cf-affiliate-step__num" aria-hidden="true">3</span>
					<h3>Both of you earn</h3>
					<p>As soon as the account is confirmed, Xfinity is credited automatically to both accounts.</p>
				</div>
			</div>
			<p class="cf-affiliate-section__note">To keep the program fair, duplicate accounts and self-referrals are automatically excluded from rewards.</p>
		</section>

		<!-- Section 4: Why Collect Xfinity? -->
		<section class="cf-affiliate-section" aria-labelledby="cf-affiliate-why-heading">
			<h2 id="cf-affiliate-why-heading" class="cf-affiliate-section__title">Why Collect Xfinity?</h2>
			<p class="cf-affiliate-section__subtitle">Every point you earn becomes part of your future on Collective Finity. Today it reflects your activity, tomorrow it unlocks more.</p>
			<div class="cf-affiliate-grid cf-affiliate-grid--3">
				<div class="cf-affiliate-card">
					<h3>Future Reward Coupons</h3>
					<p>Redeem your accumulated Xfinity for promotional offers and future partner rewards as the redemption system rolls out.</p>
				</div>
				<div class="cf-affiliate-card">
					<h3>Exclusive Benefits</h3>
					<p>Unlock community perks and member-only experiences reserved for active Xfinity holders.</p>
				</div>
				<div class="cf-affiliate-card">
					<h3>Early Access</h3>
					<p>Get priority access to new features, releases, and events before they open to everyone else.</p>
				</div>
			</div>
		</section>

		<!-- Section 5: Keep Your Xfinity -->
		<section class="cf-affiliate-section" aria-labelledby="cf-affiliate-keep-heading">
			<div class="cf-affiliate-split">
				<div
					class="cf-affiliate-split__image"
					aria-hidden="true"
					<?php if ( $cf_keep_image_url ) : ?>
						style="background-image: url('<?php echo esc_url( $cf_keep_image_url ); ?>');"
					<?php endif; ?>
				></div>
				<div class="cf-affiliate-split__copy">
					<h2 id="cf-affiliate-keep-heading" class="cf-affiliate-split__title">Keep Your Xfinity</h2>
					<p class="cf-affiliate-split__kicker">Don't rush to spend it</p>
					<p class="cf-affiliate-split__text">The more you collect today, the more opportunities you unlock tomorrow. Your Xfinity balance carries forward with you across every corner of Collective Finity, growing quietly in the background while you simply keep listening, sharing, and showing up. There is no expiry pressure and no reason to rush — patience here pays off as new ways to spend Xfinity are introduced.</p>
				</div>
			</div>
		</section>

		<!-- Section 6: NovaXfinity -->
		<section class="cf-affiliate-section" aria-labelledby="cf-affiliate-future-heading">
			<div class="cf-affiliate-split">
				<div class="cf-affiliate-split__copy">
					<span class="cf-affiliate-split__badge">Coming Soon</span>
					<h2 id="cf-affiliate-future-heading" class="cf-affiliate-split__title">NovaXfinity</h2>
					<p class="cf-affiliate-split__text">A new chapter of the Xfinity ecosystem is already being built behind the scenes. NovaXfinity will expand what your balance can do with new creative tools, premium experiences, and exclusive member benefits designed around real community feedback. Keeping your Xfinity today, rather than spending it the moment it arrives, may unlock even greater value once this next phase goes live.</p>
				</div>
				<div
					class="cf-affiliate-split__image"
					aria-hidden="true"
					<?php if ( $cf_novax_image_url ) : ?>
						style="background-image: url('<?php echo esc_url( $cf_novax_image_url ); ?>');"
					<?php endif; ?>
				></div>
			</div>
		</section>

		<!-- Section 7: FAQ -->
		<section class="cf-affiliate-section" aria-labelledby="cf-affiliate-faq-heading">
			<h2 id="cf-affiliate-faq-heading" class="cf-affiliate-section__title">Frequently Asked Questions</h2>
			<div class="cf-affiliate-grid cf-affiliate-grid--2">
				<div class="cf-affiliate-card cf-affiliate-card--faq">
					<h3>How do I earn Xfinity?</h3>
					<p>You earn Xfinity automatically while listening to music, and by inviting friends who join and confirm their accounts through your referral link.</p>
				</div>
				<div class="cf-affiliate-card cf-affiliate-card--faq">
					<h3>Where is my referral link?</h3>
					<p>Your personal referral link, along with your live balance and referral history, is always available in your account's Rewards tab.</p>
				</div>
				<div class="cf-affiliate-card cf-affiliate-card--faq">
					<h3>Does my Xfinity expire?</h3>
					<p>No. Your Xfinity remains safely stored in your account for as long as your account stays active.</p>
				</div>
				<div class="cf-affiliate-card cf-affiliate-card--faq">
					<h3>Will more rewards be added?</h3>
					<p>Yes. The rewards ecosystem, including NovaXfinity, will keep expanding as Collective Finity grows.</p>
				</div>
			</div>
		</section>

		<!-- Section 8: Final CTA -->
		<section class="cf-affiliate-section cf-affiliate-cta-section" aria-labelledby="cf-affiliate-final-heading">
			<h2 id="cf-affiliate-final-heading" class="cf-affiliate-section__title">Start Building Your Xfinity</h2>
			<p class="cf-affiliate-section__subtitle">Every song you listen to and every friend you invite brings you one step closer to real future rewards.</p>
			<div class="cf-affiliate-actions cf-affiliate-actions--center">
				<a href="<?php echo esc_url( $cf_tracks_url ); ?>" class="cf-btn-primary-lg">Start Listening</a>
				<a href="<?php echo esc_url( $cf_profile_rewards_url ); ?>" class="cf-btn-ghost-lg">
					<?php echo is_user_logged_in()
						? esc_html__( 'Manage Your Referrals', 'collective-finity' )
						: esc_html__( 'Create Account', 'collective-finity' ); ?>
				</a>
			</div>
		</section>

	</div>
</main>

<style>
.cf-affiliate-page {
	scroll-behavior: smooth;
}
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
	border-radius: 16px;
	background: var(--cf-bg-dark);
	border: var(--cf-card-border-width) solid var(--cf-border);
	overflow: hidden;
	min-width: 0;
	max-width: 100%;
	width: 100%;
	margin: 0 auto;
	box-sizing: border-box;
	box-shadow: var(--cf-card-shadow);
}
.cf-affiliate-hero--has-image {
	text-align: left;
	justify-items: stretch;
	min-height: clamp(420px, 52vw, 540px);
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
		var(--cf-accent-dim) 80%,
		var(--cf-accent) 90%,
		var(--cf-accent-hover) 93%,
		var(--cf-accent-dim) 96%,
		transparent 100%
	);
	-webkit-mask:
		linear-gradient(#fff 0 0) content-box,
		linear-gradient(#fff 0 0);
	-webkit-mask-composite: xor;
	mask-composite: exclude;
	animation: cfAffiliateBorderTravel 5.5s linear infinite;
}
@keyframes cfAffiliateBorderTravel {
	to { --cf-affiliate-hero-border-angle: 360deg; }
}
.cf-affiliate-hero__media {
	position: absolute;
	inset: 0;
	z-index: 0;
	background-image: var(--cf-affiliate-hero-image);
	background-size: cover;
	background-position: center right;
	background-repeat: no-repeat;
}
.cf-affiliate-hero__shade {
	position: absolute;
	inset: 0;
	z-index: 1;
	background:
		linear-gradient(90deg, color-mix(in srgb, var(--cf-bg-darkest) 92%, transparent) 0%, color-mix(in srgb, var(--cf-bg-darkest) 78%, transparent) 38%, color-mix(in srgb, var(--cf-bg-darkest) 28%, transparent) 64%, color-mix(in srgb, var(--cf-bg-darkest) 8%, transparent) 100%),
		linear-gradient(180deg, color-mix(in srgb, var(--cf-bg-darkest) 12%, transparent) 0%, transparent 30%, color-mix(in srgb, var(--cf-bg-darkest) 35%, transparent) 100%);
	pointer-events: none;
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
	background: radial-gradient(circle, var(--cf-accent-dim) 0%, transparent 70%);
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
	z-index: 3;
	display: grid;
	gap: 16px;
	justify-items: center;
	max-width: 640px;
	margin: 0 auto;
}
.cf-affiliate-hero--has-image .cf-affiliate-hero__content {
	justify-items: flex-start;
	margin: 0;
	max-width: 560px;
}
.cf-affiliate-hero__badge {
	display: inline-block;
	padding: 7px 16px;
	border-radius: 999px;
	background: var(--cf-accent-dim);
	border: var(--cf-card-border-width) solid color-mix(in srgb, var(--cf-accent) 35%, transparent);
	color: var(--cf-accent);
	font-family: var(--cf-mono);
	font-size: 11px;
	letter-spacing: 0.1em;
	text-transform: uppercase;
}
.cf-affiliate-hero__title,
.cf-affiliate-section__title,
.cf-affiliate-split__title {
	color: var(--cf-text);
	font-family: var(--cf-mono);
	font-weight: 700;
	line-height: 1.15;
	margin: 0;
}
.cf-affiliate-hero__title {
	font-size: clamp(28px, 5vw, 40px);
}
.cf-affiliate-accent {
	color: var(--cf-accent);
}
.cf-affiliate-hero__lead {
	color: var(--cf-text-2);
	max-width: 620px;
	line-height: 1.7;
	font-size: 14px;
	margin: 0;
	font-family: var(--cf-body);
}
.cf-affiliate-actions {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
	margin-top: 8px;
}
.cf-affiliate-hero--has-image .cf-affiliate-actions {
	justify-content: flex-start;
}
.cf-affiliate-actions--center {
	justify-content: center;
}
.cf-affiliate-section__title {
	font-size: clamp(1.35rem, 3vw, 1.75rem);
	margin: 0 0 12px;
	text-align: center;
}
.cf-affiliate-section__subtitle {
	color: var(--cf-text-2);
	text-align: center;
	max-width: 680px;
	margin: 0 auto 28px;
	line-height: 1.7;
	font-size: 0.95rem;
	font-family: var(--cf-body);
}
.cf-affiliate-section__note {
	color: var(--cf-text-3);
	font-size: 0.85rem;
	text-align: center;
	margin: 20px auto 0;
	max-width: 640px;
	line-height: 1.7;
	font-family: var(--cf-body);
}
.cf-affiliate-grid {
	display: grid;
	gap: 20px;
}
.cf-affiliate-grid--3 {
	grid-template-columns: repeat(3, 1fr);
}
.cf-affiliate-grid--2 {
	grid-template-columns: repeat(2, 1fr);
}
.cf-affiliate-card {
	background: var(--cf-bg-card);
	border: var(--cf-card-border-width) solid var(--cf-border);
	border-radius: var(--cf-card-radius);
	box-shadow: var(--cf-card-shadow);
	padding: 20px;
	text-align: center;
}
.cf-affiliate-card--faq {
	text-align: left;
}
.cf-affiliate-step__num {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 32px;
	height: 32px;
	border-radius: 50%;
	background: var(--cf-bg-card-hover);
	border: var(--cf-card-border-width) solid var(--cf-border-strong);
	color: var(--cf-text);
	font-weight: 700;
	font-family: var(--cf-mono);
	margin-bottom: 12px;
}
.cf-affiliate-card h3 {
	color: var(--cf-text);
	margin: 0 0 8px;
	font-size: 1.05rem;
	font-family: var(--cf-mono);
}
.cf-affiliate-card p {
	color: var(--cf-text-2);
	line-height: 1.7;
	margin: 0;
	font-size: 0.92rem;
	font-family: var(--cf-body);
}
.cf-affiliate-split {
	display: grid;
	grid-template-columns: 1fr;
	gap: 24px;
	align-items: stretch;
}
@media (min-width: 860px) {
	.cf-affiliate-split {
		grid-template-columns: 1fr 1fr;
	}
}
.cf-affiliate-split__image {
	border-radius: 16px;
	border: var(--cf-card-border-width) solid var(--cf-border);
	background-color: var(--cf-bg-card);
	background-size: cover;
	background-position: center;
	min-height: 260px;
}
.cf-affiliate-split__copy {
	border-radius: 16px;
	padding: clamp(24px, 3vw, 36px);
	background: linear-gradient(160deg, var(--cf-accent-dim), color-mix(in srgb, var(--cf-accent) 2%, transparent));
	border: var(--cf-card-border-width) solid color-mix(in srgb, var(--cf-accent) 18%, transparent);
	display: flex;
	flex-direction: column;
	justify-content: center;
	gap: 12px;
}
.cf-affiliate-split__title {
	font-size: clamp(22px, 2.6vw, 30px);
}
.cf-affiliate-split__kicker {
	margin: 0;
	color: var(--cf-accent);
	font-weight: 600;
	font-family: var(--cf-body);
}
.cf-affiliate-split__text {
	margin: 0;
	color: var(--cf-text-2);
	line-height: 1.7;
	font-size: 15px;
	font-family: var(--cf-body);
}
.cf-affiliate-split__badge {
	display: inline-block;
	align-self: flex-start;
	padding: 6px 14px;
	border-radius: 999px;
	background: var(--cf-bg-card-hover);
	border: var(--cf-card-border-width) solid var(--cf-border);
	color: var(--cf-text);
	font-size: 0.75rem;
	letter-spacing: 0.04em;
	text-transform: uppercase;
	font-family: var(--cf-mono);
}
@media (prefers-reduced-motion: reduce) {
	.cf-affiliate-page {
		scroll-behavior: auto;
	}
	.cf-affiliate-hero__border,
	.cf-affiliate-hero__center-glow {
		animation: none;
	}
}
@media (max-width: 782px) {
	.cf-affiliate-grid--3,
	.cf-affiliate-grid--2 {
		grid-template-columns: 1fr;
	}
	.cf-affiliate-hero--has-image {
		text-align: center;
	}
	.cf-affiliate-hero--has-image .cf-affiliate-hero__content,
	.cf-affiliate-hero--has-image .cf-affiliate-actions {
		justify-items: center;
		justify-content: center;
	}
}
</style>

<?php get_footer(); ?>
