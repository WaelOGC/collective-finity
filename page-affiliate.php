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

$cf_dashboard = array(
	'balance'            => '',
	'referral_link'      => '',
	'total_referrals'    => 0,
	'confirmed_referrals'=> 0,
	'last_activity'      => '',
	'has_history'        => false,
	'today_earned'       => 0.0,
);

if ( is_user_logged_in() && class_exists( 'CF_Xfinity' ) && class_exists( 'CF_Referral' ) ) {
	global $wpdb;

	$cf_user_id = get_current_user_id();
	$cf_xfinity = CF_Xfinity::get_instance();
	$cf_referral = CF_Referral::get_instance();

	$cf_dashboard['balance']       = $cf_xfinity->get_balance( $cf_user_id );
	$cf_dashboard['referral_link'] = $cf_referral->get_referral_link( $cf_user_id );

	$cf_referrals_table = CF_Referral::referrals_table();
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$cf_referral_rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT status, COUNT(*) AS count FROM {$cf_referrals_table} WHERE referrer_user_id = %d GROUP BY status", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$cf_user_id
		),
		ARRAY_A
	);

	if ( is_array( $cf_referral_rows ) ) {
		foreach ( $cf_referral_rows as $cf_referral_row ) {
			$cf_status = isset( $cf_referral_row['status'] ) ? (string) $cf_referral_row['status'] : '';
			$cf_count  = isset( $cf_referral_row['count'] ) ? (int) $cf_referral_row['count'] : 0;
			$cf_dashboard['total_referrals'] += $cf_count;
			if ( 'confirmed' === $cf_status ) {
				$cf_dashboard['confirmed_referrals'] = $cf_count;
			}
		}
	}

	$cf_recent_history = $cf_xfinity->get_transaction_history( $cf_user_id, 1 );
	if ( ! empty( $cf_recent_history ) && is_array( $cf_recent_history ) ) {
		$cf_latest = reset( $cf_recent_history );
		if ( is_object( $cf_latest ) ) {
			$cf_latest = (array) $cf_latest;
		}
		if ( is_array( $cf_latest ) && ! empty( $cf_latest['created_at'] ) ) {
			$cf_dashboard['has_history']   = true;
			$cf_dashboard['last_activity'] = $cf_latest['created_at'];
		}
	}

	$cf_ledger_table = CF_Xfinity::ledger_table();
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$cf_today_sum = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COALESCE(SUM(amount), 0) FROM {$cf_ledger_table} WHERE user_id = %d AND DATE(created_at) = CURDATE() AND amount > 0", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$cf_user_id
		)
	);
	$cf_dashboard['today_earned'] = null !== $cf_today_sum ? (float) $cf_today_sum : 0.0;
}
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
				<span class="cf-affiliate-hero__badge">XFINITY REWARDS</span>
				<h1 id="cf-affiliate-hero-heading" class="cf-affiliate-hero__title">
					Listen. Share. <span class="cf-affiliate-hero__title-accent">Earn.</span>
				</h1>
				<div class="cf-affiliate-hero__lead">
					<p>Xfinity Rewards is Collective Finity's loyalty program designed to reward every meaningful interaction across the platform.</p>
					<p>Earn Xfinity by listening to music, inviting friends, and growing with the community. Save your balance for future rewards, exclusive benefits, and upcoming experiences across the Collective Finity ecosystem.</p>
				</div>
				<div class="cf-affiliate-hero__actions">
					<a href="<?php echo esc_url( $cf_tracks_url ); ?>" class="cf-affiliate-cta">Start Listening</a>
					<a href="#cf-affiliate-ways" class="cf-affiliate-cta cf-affiliate-cta--ghost">Learn More</a>
				</div>
			</div>
		</section>

		<!-- Section 2: Ways to Earn Xfinity -->
		<section id="cf-affiliate-ways" class="cf-affiliate-section" aria-labelledby="cf-affiliate-ways-heading">
			<h2 id="cf-affiliate-ways-heading" class="cf-affiliate-section__title">Ways to Earn Xfinity</h2>
			<p class="cf-affiliate-section__subtitle">There are multiple ways to grow your Xfinity balance while enjoying everything Collective Finity has to offer.</p>
			<div class="cf-affiliate-steps">
				<div class="cf-affiliate-step">
					<h3>🎵 Listen to Music</h3>
					<p>Earn Xfinity simply by listening to original music across the platform. Every genuine listening session contributes to your growing balance.</p>
				</div>
				<div class="cf-affiliate-step">
					<h3>👥 Invite Friends</h3>
					<p>Share your personal referral link with friends. When someone joins Collective Finity through your invitation, both of you receive Xfinity as a welcome reward.</p>
				</div>
				<div class="cf-affiliate-step">
					<h3>🚀 More Ways Coming Soon</h3>
					<p>As Collective Finity continues to grow, additional opportunities to earn Xfinity will become available through new features and future platform experiences.</p>
				</div>
			</div>
		</section>

		<!-- Section 3: How Referrals Work -->
		<section class="cf-affiliate-section" aria-labelledby="cf-affiliate-referrals-heading">
			<h2 id="cf-affiliate-referrals-heading" class="cf-affiliate-section__title cf-affiliate-section__title--split">
				Simple. <span class="cf-affiliate-accent">Transparent.</span> Rewarding.
			</h2>
			<div class="cf-affiliate-steps">
				<div class="cf-affiliate-step">
					<span class="cf-affiliate-step__num">1</span>
					<h3>Share your personal referral link.</h3>
				</div>
				<div class="cf-affiliate-step">
					<span class="cf-affiliate-step__num">2</span>
					<h3>A new user creates an account using your invitation.</h3>
				</div>
				<div class="cf-affiliate-step">
					<span class="cf-affiliate-step__num">3</span>
					<h3>Once the account is confirmed, both of you receive Xfinity.</h3>
				</div>
			</div>
			<p class="cf-affiliate-section__note">To keep the program fair, duplicate accounts and self-referrals are automatically excluded from rewards.</p>
		</section>

		<!-- Section 4: Your Rewards Dashboard -->
		<section class="cf-affiliate-section" aria-labelledby="cf-affiliate-dashboard-heading">
			<h2 id="cf-affiliate-dashboard-heading" class="cf-affiliate-section__title">Your Rewards Dashboard</h2>
			<p class="cf-affiliate-section__subtitle">Everything you need is available inside your account. Users can easily monitor their progress from a single dashboard.</p>

			<?php if ( is_user_logged_in() ) : ?>
				<div class="cf-affiliate-dashboard">
					<div class="cf-affiliate-tier cf-affiliate-dashboard__card">
						<span class="cf-affiliate-tier__label">Current Balance</span>
						<span class="cf-affiliate-tier__amount"><?php echo esc_html( number_format_i18n( (float) $cf_dashboard['balance'], 4 ) ); ?></span>
						<p>Xfinity</p>
					</div>

					<div class="cf-affiliate-tier cf-affiliate-dashboard__card cf-affiliate-dashboard__card--referral">
						<span class="cf-affiliate-tier__label">Referral Link</span>
						<?php if ( $cf_dashboard['referral_link'] ) : ?>
							<div class="cf-affiliate-referral-field">
								<input
									type="text"
									id="cf-referral-link-input"
									class="cf-affiliate-referral-input"
									value="<?php echo esc_attr( $cf_dashboard['referral_link'] ); ?>"
									readonly
									aria-label="<?php esc_attr_e( 'Your referral link', 'collective-finity' ); ?>"
								>
								<button type="button" id="cf-referral-link-copy" class="cf-affiliate-referral-copy">
									<?php esc_html_e( 'Copy', 'collective-finity' ); ?>
								</button>
							</div>
						<?php else : ?>
							<p><?php esc_html_e( 'Referral link unavailable.', 'collective-finity' ); ?></p>
						<?php endif; ?>
					</div>

					<div class="cf-affiliate-tier cf-affiliate-dashboard__card">
						<span class="cf-affiliate-tier__label">Referral Statistics</span>
						<div class="cf-affiliate-dashboard__stats">
							<div class="cf-affiliate-dashboard__stat">
								<span class="cf-affiliate-tier__amount"><?php echo esc_html( number_format_i18n( $cf_dashboard['total_referrals'] ) ); ?></span>
								<p>Total Referrals</p>
							</div>
							<div class="cf-affiliate-dashboard__stat">
								<span class="cf-affiliate-tier__amount"><?php echo esc_html( number_format_i18n( $cf_dashboard['confirmed_referrals'] ) ); ?></span>
								<p>Confirmed</p>
							</div>
						</div>
					</div>

					<div class="cf-affiliate-tier cf-affiliate-dashboard__card">
						<span class="cf-affiliate-tier__label">Reward History</span>
						<?php if ( $cf_dashboard['has_history'] ) : ?>
							<p class="cf-affiliate-dashboard__detail">
								<?php
								printf(
									/* translators: %s: formatted date */
									esc_html__( 'Last activity: %s', 'collective-finity' ),
									esc_html( date_i18n( get_option( 'date_format' ), strtotime( $cf_dashboard['last_activity'] ) ) )
								);
								?>
							</p>
						<?php else : ?>
							<p class="cf-affiliate-dashboard__detail">No activity yet — start listening or invite a friend.</p>
						<?php endif; ?>
					</div>

					<div class="cf-affiliate-tier cf-affiliate-dashboard__card">
						<span class="cf-affiliate-tier__label">Daily Activity</span>
						<?php if ( $cf_dashboard['today_earned'] > 0 ) : ?>
							<span class="cf-affiliate-tier__amount"><?php echo esc_html( number_format_i18n( $cf_dashboard['today_earned'], 4 ) ); ?></span>
							<p><?php esc_html_e( 'Earned today', 'collective-finity' ); ?></p>
						<?php else : ?>
							<p class="cf-affiliate-dashboard__detail">0.0000 — nothing earned yet today</p>
						<?php endif; ?>
					</div>
				</div>
			<?php else : ?>
				<div class="cf-affiliate-dashboard-guest">
					<p>Sign in to see your live Xfinity balance, referral link, and activity</p>
					<a href="<?php echo esc_url( $cf_register_url ); ?>" class="cf-affiliate-cta">Create Account</a>
				</div>
			<?php endif; ?>
		</section>

		<!-- Section 5: Why Collect Xfinity? -->
		<section class="cf-affiliate-section" aria-labelledby="cf-affiliate-why-heading">
			<h2 id="cf-affiliate-why-heading" class="cf-affiliate-section__title">Why Collect Xfinity?</h2>
			<p class="cf-affiliate-section__subtitle">Every point you earn becomes part of your future within Collective Finity. Today, Xfinity represents your activity across the platform. Tomorrow, it will unlock even more opportunities.</p>
			<div class="cf-affiliate-steps">
				<div class="cf-affiliate-step">
					<h3>🎁 Future Reward Coupons</h3>
					<p>Redeem Xfinity for exclusive promotional offers and future partner rewards.</p>
				</div>
				<div class="cf-affiliate-step">
					<h3>⭐ Exclusive Benefits</h3>
					<p>Unlock community perks and member-only experiences as the platform continues to evolve.</p>
				</div>
				<div class="cf-affiliate-step">
					<h3>🚀 Early Access</h3>
					<p>Gain priority access to selected features, events, and upcoming releases.</p>
				</div>
			</div>
		</section>

		<!-- Section 6: Keep Your Xfinity -->
		<section
			class="cf-affiliate-banner cf-affiliate-keep"
			aria-labelledby="cf-affiliate-keep-heading"
			<?php if ( $cf_keep_image_url ) : ?>
				style="--cf-affiliate-banner-image: url('<?php echo esc_url( $cf_keep_image_url ); ?>');"
			<?php endif; ?>
		>
			<div class="cf-affiliate-banner__shade" aria-hidden="true"></div>
			<div class="cf-affiliate-banner__content">
				<h2 id="cf-affiliate-keep-heading" class="cf-affiliate-banner__title">Keep Your Xfinity</h2>
				<p class="cf-affiliate-banner__lead">Don't rush to spend it.</p>
				<p class="cf-affiliate-banner__body">The more you collect today, the more opportunities you'll have tomorrow. Your Xfinity balance will continue growing with you across the Collective Finity ecosystem.</p>
			</div>
		</section>

		<!-- Section 7: NovaXfinity -->
		<section
			class="cf-affiliate-section cf-affiliate-future"
			aria-labelledby="cf-affiliate-future-heading"
			<?php if ( $cf_novax_image_url ) : ?>
				style="--cf-affiliate-future-image: url('<?php echo esc_url( $cf_novax_image_url ); ?>');"
			<?php endif; ?>
		>
			<div class="cf-affiliate-future__shade" aria-hidden="true"></div>
			<div class="cf-affiliate-future__content">
				<h2 id="cf-affiliate-future-heading" class="cf-affiliate-section__title">NovaXfinity</h2>
				<p class="cf-affiliate-future__lead">A new chapter is already being built.</p>
				<p>NovaXfinity will expand the Collective Finity ecosystem with new creative tools, premium experiences, and exclusive member benefits. Keeping your Xfinity today may unlock even greater value in the future.</p>
				<span class="cf-affiliate-future__badge">Coming Soon</span>
			</div>
		</section>

		<!-- Section 8: FAQ -->
		<section class="cf-affiliate-section" aria-labelledby="cf-affiliate-faq-heading">
			<h2 id="cf-affiliate-faq-heading" class="cf-affiliate-section__title">Frequently Asked Questions</h2>
			<div class="cf-affiliate-faq">
				<div class="cf-affiliate-faq__item">
					<h3>❓ How do I earn Xfinity?</h3>
					<p>Listen to music and invite friends to Collective Finity.</p>
				</div>
				<div class="cf-affiliate-faq__item">
					<h3>🔗 Where can I find my referral link?</h3>
					<p>Inside your Rewards Dashboard.</p>
				</div>
				<div class="cf-affiliate-faq__item">
					<h3>🛡️ Does my Xfinity expire?</h3>
					<p>Your Xfinity remains safely stored in your account.</p>
				</div>
				<div class="cf-affiliate-faq__item">
					<h3>🎁 Will more rewards be added?</h3>
					<p>Yes. The rewards ecosystem will continue expanding alongside Collective Finity.</p>
				</div>
			</div>
		</section>

		<!-- Final CTA -->
		<section class="cf-affiliate-section cf-affiliate-cta-section" aria-labelledby="cf-affiliate-final-heading">
			<h2 id="cf-affiliate-final-heading" class="cf-affiliate-section__title">Start Building Your Xfinity</h2>
			<p class="cf-affiliate-section__subtitle">Every song. Every referral. Every step forward brings you closer to future rewards.</p>
			<div class="cf-affiliate-hero__actions cf-affiliate-cta-section__actions">
				<a href="<?php echo esc_url( $cf_tracks_url ); ?>" class="cf-affiliate-cta">Start Listening</a>
				<a href="<?php echo esc_url( $cf_profile_rewards_url ); ?>" class="cf-affiliate-cta cf-affiliate-cta--ghost">
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
		linear-gradient(90deg, rgba(8, 8, 8, 0.92) 0%, rgba(8, 8, 8, 0.78) 38%, rgba(8, 8, 8, 0.28) 64%, rgba(8, 8, 8, 0.08) 100%),
		linear-gradient(180deg, rgba(8, 8, 8, 0.12) 0%, transparent 30%, rgba(8, 8, 8, 0.35) 100%);
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
.cf-affiliate-hero__title-accent,
.cf-affiliate-accent {
	color: var(--cf-accent, #FFB700);
}
.cf-affiliate-hero__lead {
	color: #B3B3B3;
	max-width: 620px;
	line-height: 1.7;
	font-size: 14px;
}
.cf-affiliate-hero__lead p {
	margin: 0 0 12px;
}
.cf-affiliate-hero__lead p:last-child {
	margin-bottom: 0;
}
.cf-affiliate-hero__actions {
	display: flex;
	flex-wrap: wrap;
	justify-content: center;
	gap: 12px;
	margin-top: 8px;
}
.cf-affiliate-hero--has-image .cf-affiliate-hero__actions {
	justify-content: flex-start;
}
.cf-affiliate-cta {
	display: inline-block;
	padding: 12px 28px;
	border-radius: 999px;
	background: #fff;
	color: #111;
	font-weight: 600;
	text-decoration: none;
	transition: opacity 0.2s ease;
	border: 1px solid transparent;
}
.cf-affiliate-cta:hover {
	opacity: 0.85;
}
.cf-affiliate-cta--ghost {
	background: transparent;
	color: #fff;
	border-color: rgba(255, 255, 255, 0.28);
}
.cf-affiliate-cta--ghost:hover {
	opacity: 1;
	border-color: rgba(255, 183, 0, 0.5);
	color: var(--cf-accent, #FFB700);
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
.cf-affiliate-section__title {
	color: #fff;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: 1.5rem;
	margin: 0 0 12px;
	text-align: center;
}
.cf-affiliate-section__title--split {
	font-size: clamp(1.35rem, 3vw, 1.75rem);
}
.cf-affiliate-section__subtitle {
	color: #b8b8b8;
	text-align: center;
	max-width: 680px;
	margin: 0 auto 28px;
	line-height: 1.7;
	font-size: 0.95rem;
}
.cf-affiliate-section__note {
	color: #999;
	font-size: 0.85rem;
	text-align: center;
	margin: 20px auto 0;
	max-width: 640px;
	line-height: 1.7;
}
.cf-affiliate-steps,
.cf-affiliate-tiers,
.cf-affiliate-faq {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 20px;
}
.cf-affiliate-faq {
	grid-template-columns: repeat(2, 1fr);
}
.cf-affiliate-step,
.cf-affiliate-tier,
.cf-affiliate-faq__item {
	background: rgba(255, 255, 255, 0.04);
	border: 1px solid rgba(255, 255, 255, 0.08);
	border-radius: 16px;
	padding: 24px;
	text-align: center;
}
.cf-affiliate-faq__item {
	text-align: left;
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
.cf-affiliate-step h3,
.cf-affiliate-faq__item h3 {
	color: #fff;
	margin: 0 0 8px;
	font-size: 1.05rem;
}
.cf-affiliate-step p,
.cf-affiliate-tier p,
.cf-affiliate-faq__item p {
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
	font-family: var(--cf-mono, 'Space Mono', monospace);
}
.cf-affiliate-tier__label {
	display: block;
	color: #999;
	font-size: 0.8rem;
	text-transform: uppercase;
	letter-spacing: 0.06em;
	margin-bottom: 10px;
}
.cf-affiliate-dashboard {
	display: grid;
	grid-template-columns: repeat(5, 1fr);
	gap: 20px;
}
.cf-affiliate-dashboard__card {
	text-align: center;
	min-width: 0;
}
.cf-affiliate-dashboard__card--referral {
	grid-column: span 1;
}
.cf-affiliate-dashboard__stats {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 12px;
}
.cf-affiliate-dashboard__stat .cf-affiliate-tier__amount {
	font-size: 1.4rem;
}
.cf-affiliate-dashboard__detail {
	color: #d2d2d2;
	font-size: 0.9rem;
	line-height: 1.6;
	margin-top: 4px;
}
.cf-affiliate-referral-field {
	display: flex;
	gap: 8px;
	margin-top: 4px;
}
.cf-affiliate-referral-input {
	flex: 1;
	min-width: 0;
	padding: 8px 10px;
	border-radius: 8px;
	border: 1px solid rgba(255, 255, 255, 0.12);
	background: rgba(0, 0, 0, 0.35);
	color: #e8e8e8;
	font-size: 0.78rem;
}
.cf-affiliate-referral-copy {
	flex-shrink: 0;
	padding: 8px 14px;
	border-radius: 8px;
	border: 1px solid rgba(255, 183, 0, 0.35);
	background: rgba(255, 183, 0, 0.1);
	color: var(--cf-accent, #FFB700);
	font-size: 0.78rem;
	font-weight: 600;
	cursor: pointer;
	transition: background 0.2s ease;
}
.cf-affiliate-referral-copy:hover {
	background: rgba(255, 183, 0, 0.18);
}
.cf-affiliate-dashboard-guest {
	background: rgba(255, 255, 255, 0.04);
	border: 1px solid rgba(255, 255, 255, 0.08);
	border-radius: 16px;
	padding: 40px 24px;
	text-align: center;
	display: grid;
	gap: 16px;
	justify-items: center;
	max-width: 520px;
	margin: 0 auto;
}
.cf-affiliate-dashboard-guest p {
	color: #b8b8b8;
	margin: 0;
	line-height: 1.7;
}
.cf-affiliate-banner {
	position: relative;
	overflow: hidden;
	border-radius: 18px;
	border: 1px solid rgba(255, 255, 255, 0.07);
	background-color: #0f0f0f;
	background-image: var(--cf-affiliate-banner-image);
	background-size: cover;
	background-position: center;
	box-shadow: 0 18px 40px -28px rgba(0, 0, 0, 0.85);
	min-height: clamp(280px, 36vw, 360px);
}
.cf-affiliate-banner__shade {
	position: absolute;
	inset: 0;
	background: linear-gradient(180deg, rgba(8, 8, 8, 0.55) 0%, rgba(8, 8, 8, 0.78) 55%, rgba(8, 8, 8, 0.9) 100%);
	pointer-events: none;
}
.cf-affiliate-banner__content {
	position: relative;
	z-index: 1;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	gap: 14px;
	text-align: center;
	padding: clamp(40px, 6vw, 64px) clamp(24px, 4vw, 48px);
	max-width: 720px;
	margin: 0 auto;
}
.cf-affiliate-banner__title {
	margin: 0;
	color: #fff;
	font-family: var(--cf-mono, 'Space Mono', monospace);
	font-size: clamp(24px, 3vw, 32px);
}
.cf-affiliate-banner__lead {
	margin: 0;
	color: var(--cf-accent, #FFB700);
	font-size: 1rem;
	font-weight: 600;
}
.cf-affiliate-banner__body {
	margin: 0;
	max-width: 36em;
	color: #D0D0D0;
	line-height: 1.7;
}
.cf-affiliate-future {
	position: relative;
	text-align: center;
	background-color: #0f0f0f;
	background-image: var(--cf-affiliate-future-image);
	background-size: cover;
	background-position: center;
	border: 1px solid rgba(255, 255, 255, 0.08);
	border-radius: 20px;
	padding: 0;
	overflow: hidden;
	min-height: clamp(280px, 34vw, 340px);
}
.cf-affiliate-future__shade {
	position: absolute;
	inset: 0;
	background: linear-gradient(180deg, rgba(8, 8, 8, 0.5) 0%, rgba(8, 8, 8, 0.82) 100%);
	pointer-events: none;
}
.cf-affiliate-future__content {
	position: relative;
	z-index: 1;
	padding: clamp(40px, 6vw, 56px) clamp(24px, 4vw, 40px);
}
.cf-affiliate-future__lead {
	color: var(--cf-accent, #FFB700);
	font-weight: 600;
	margin: 0 0 12px;
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
.cf-affiliate-cta-section__actions {
	justify-content: center;
}
@media (max-width: 1200px) {
	.cf-affiliate-dashboard {
		grid-template-columns: repeat(3, 1fr);
	}
	.cf-affiliate-dashboard__card--referral {
		grid-column: span 3;
	}
}
@media (max-width: 782px) {
	.cf-affiliate-steps,
	.cf-affiliate-tiers,
	.cf-affiliate-faq,
	.cf-affiliate-dashboard {
		grid-template-columns: 1fr;
	}
	.cf-affiliate-dashboard__card--referral {
		grid-column: span 1;
	}
	.cf-affiliate-hero--has-image {
		text-align: center;
	}
	.cf-affiliate-hero--has-image .cf-affiliate-hero__content,
	.cf-affiliate-hero--has-image .cf-affiliate-hero__actions {
		justify-items: center;
		justify-content: center;
	}
	.cf-affiliate-referral-field {
		flex-direction: column;
	}
}
</style>

<?php if ( is_user_logged_in() && ! empty( $cf_dashboard['referral_link'] ) ) : ?>
<script>
(function () {
	var input = document.getElementById('cf-referral-link-input');
	var btn = document.getElementById('cf-referral-link-copy');
	if (!input || !btn) {
		return;
	}
	var defaultLabel = btn.textContent;
	btn.addEventListener('click', function () {
		var value = input.value;
		if (!value) {
			return;
		}
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(value).then(showCopied).catch(fallbackCopy);
			return;
		}
		fallbackCopy();
		function fallbackCopy() {
			input.focus();
			input.select();
			input.setSelectionRange(0, value.length);
			try {
				document.execCommand('copy');
				showCopied();
			} catch (e) {}
		}
		function showCopied() {
			btn.textContent = '<?php echo esc_js( __( 'Copied!', 'collective-finity' ) ); ?>';
			window.setTimeout(function () {
				btn.textContent = defaultLabel;
			}, 2000);
		}
	});
})();
</script>
<?php endif; ?>

<?php get_footer(); ?>
