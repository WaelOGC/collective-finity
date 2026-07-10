<?php
/**
 * Template Name: Donate
 * Description: Theme template for the Donation page.
 *
 * @package Collective_Finity
 */

get_header();
?>

<main id="primary" class="site-main cf-donate-page" role="main">
	<div class="cf-donate-page__inner">

		<section class="cf-donate-hero" aria-labelledby="cf-donate-hero-heading">
			<div class="cf-donate-hero__glow cf-donate-hero__glow--left" aria-hidden="true"></div>
			<div class="cf-donate-hero__glow cf-donate-hero__glow--right" aria-hidden="true"></div>
			<div class="cf-donate-hero__dots" aria-hidden="true"></div>
			<div class="cf-donate-hero__content">
				<span class="cf-donate-hero__badge"><?php esc_html_e( 'Support the platform', 'collective-finity' ); ?></span>
				<h1 id="cf-donate-hero-heading" class="cf-donate-hero__title">
					<?php esc_html_e( 'Support Collective Finity', 'collective-finity' ); ?>
				</h1>
				<p class="cf-donate-hero__lead">
					<?php esc_html_e( 'Your contribution helps keep the platform running — hosting releases, maintaining the music library, and growing the community for creators and listeners alike.', 'collective-finity' ); ?>
				</p>
			</div>
		</section>

		<section class="cf-donate-content" aria-label="<?php esc_attr_e( 'Donation form', 'collective-finity' ); ?>">
			<?php echo do_shortcode( '[cf_donation_form]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</section>

	</div>
</main>

<style>
.cf-donate-page {
	background: var(--darker-bg, #050505);
	color: #fff;
	padding: 48px clamp(12px, 2vw, 20px) 140px;
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
	margin: 0 auto;
	display: flex;
	flex-direction: column;
	gap: 40px;
}
.cf-donate-hero {
	position: relative;
	text-align: center;
	padding: clamp(40px, 6vw, 68px) clamp(20px, 4vw, 40px);
	border-radius: 18px;
	background: radial-gradient(ellipse at 50% 0%, rgba(255, 183, 0, 0.14), transparent 62%), var(--secondary-color, #0D0D0D);
	border: 1px solid rgba(255, 255, 255, 0.08);
	overflow: hidden;
	min-width: 0;
	max-width: 100%;
	box-sizing: border-box;
}
.cf-donate-hero__glow {
	position: absolute;
	border-radius: 50%;
	filter: blur(48px);
	pointer-events: none;
	opacity: 0.55;
}
.cf-donate-hero__glow--left {
	width: 220px;
	height: 220px;
	top: -60px;
	left: -40px;
	background: rgba(255, 183, 0, 0.18);
}
.cf-donate-hero__glow--right {
	width: 180px;
	height: 180px;
	bottom: -50px;
	right: -20px;
	background: rgba(255, 183, 0, 0.1);
}
.cf-donate-hero__dots {
	position: absolute;
	inset: 0;
	opacity: 0.42;
	pointer-events: none;
	background-image: radial-gradient(circle, rgba(255, 183, 0, 0.32) 1px, transparent 1.4px);
	background-size: 26px 26px;
}
.cf-donate-hero__content {
	position: relative;
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
.cf-donate-hero__lead {
	margin: 0;
	max-width: 520px;
	font-size: 14px;
	line-height: 1.7;
	color: var(--cf-text-2, #B3B3B3);
}
.cf-donate-content {
	min-width: 0;
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

@media (max-width: 767px) {
	.cf-donate-page {
		padding: 24px 16px 100px;
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
