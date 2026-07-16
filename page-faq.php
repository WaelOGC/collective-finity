<?php
/**
 * Template Name: FAQ
 * Description: Frequently Asked Questions with accordion sections and platform reviews.
 *
 * @package Collective_Finity
 */

$cf_faq_sections   = collective_finity_get_faq_sections();
$cf_faq_categories = collective_finity_platform_review_categories();
$cf_faq_post_id    = get_queried_object_id();
$cf_login_url      = wp_login_url( get_permalink() . '#cf-platform-reviews' );
$cf_is_logged_in   = is_user_logged_in();
$cf_current_user   = $cf_is_logged_in ? wp_get_current_user() : null;

$cf_platform_reviews = get_comments(
	array(
		'post_id'    => $cf_faq_post_id,
		'status'     => 'approve',
		'type'       => 'comment',
		'orderby'    => 'comment_date',
		'order'      => 'DESC',
		'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'   => 'cf_platform_review',
				'value' => '1',
			),
			array(
				'key'     => 'cf_rating',
				'value'   => array( 1, 5 ),
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC',
			),
		),
	)
);

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-faq-page">
	<div class="cf-page-container cf-faq">
		<div class="cf-faq__inner">

			<section class="cf-faq-hero" aria-labelledby="cf-faq-heading">
				<div class="cf-faq-hero__border" aria-hidden="true"></div>
				<div class="cf-faq-hero__center-glow" aria-hidden="true"></div>
				<div class="cf-faq-hero__icons" aria-hidden="true">
					<?php
					$cf_faq_icon_uri  = get_template_directory_uri() . '/assets/images/dancing/';
					$cf_faq_icon_imgs = array(
						'faq-person-thought-bubble.png',
						'faq-question-mark-circle.png',
						'faq-stick-figure-leaning.png',
						'faq-thinking-shadow.png',
						'faq-mixed-reactions.png',
					);
					foreach ( $cf_faq_icon_imgs as $cf_faq_icon_i => $cf_faq_icon_file ) :
						?>
						<span
							class="cf-faq-hero__icon cf-faq-hero__icon--<?php echo esc_attr( (string) ( $cf_faq_icon_i + 1 ) ); ?>"
							style="--cf-faq-icon-img: url('<?php echo esc_url( $cf_faq_icon_uri . $cf_faq_icon_file ); ?>');"
						></span>
					<?php endforeach; ?>
				</div>
				<div class="cf-faq-hero__freq" aria-hidden="true"></div>
				<div class="cf-faq-hero__content">
					<p class="cf-faq-eyebrow"><?php esc_html_e( 'HELP CENTER', 'collective-finity' ); ?></p>
					<h1 id="cf-faq-heading" class="cf-faq-hero__title"><?php esc_html_e( 'Frequently Asked Questions', 'collective-finity' ); ?></h1>
					<p class="cf-faq-hero__lead">
						<?php esc_html_e( 'Answers about Collective Finity — the project, how to use the site, and how the platform works. Still stuck? Leave a review or reach out through Contact.', 'collective-finity' ); ?>
					</p>
					<nav class="cf-faq-jump" aria-label="<?php esc_attr_e( 'FAQ sections', 'collective-finity' ); ?>">
						<?php foreach ( $cf_faq_sections as $cf_jump_i => $cf_jump_section ) : ?>
							<a class="cf-faq-jump__link" href="#cf-faq-section-<?php echo esc_attr( (string) $cf_jump_i ); ?>">
								<?php echo esc_html( $cf_jump_section['title'] ); ?>
							</a>
						<?php endforeach; ?>
						<a class="cf-faq-jump__link cf-faq-jump__link--accent" href="#cf-platform-reviews">
							<?php esc_html_e( 'Platform Reviews', 'collective-finity' ); ?>
						</a>
					</nav>
				</div>
			</section>

			<?php foreach ( $cf_faq_sections as $cf_section_i => $cf_section ) : ?>
				<section
					id="cf-faq-section-<?php echo esc_attr( (string) $cf_section_i ); ?>"
					class="cf-faq-section"
					aria-labelledby="cf-faq-section-title-<?php echo esc_attr( (string) $cf_section_i ); ?>"
				>
					<header class="cf-faq-section__head">
						<p class="cf-faq-section__eyebrow"><?php echo esc_html( $cf_section['eyebrow'] ); ?></p>
						<h2 id="cf-faq-section-title-<?php echo esc_attr( (string) $cf_section_i ); ?>" class="cf-faq-section__title">
							<?php echo esc_html( $cf_section['title'] ); ?>
						</h2>
					</header>

					<div
						class="cf-faq-accordion"
						data-cf-faq-accordion
						role="list"
						aria-label="<?php echo esc_attr( $cf_section['title'] ); ?>"
					>
						<?php foreach ( $cf_section['items'] as $cf_item_i => $cf_item ) : ?>
							<?php
							$cf_trigger_id = 'cf-faq-t-' . $cf_section_i . '-' . $cf_item_i;
							$cf_panel_id   = 'cf-faq-p-' . $cf_section_i . '-' . $cf_item_i;
							?>
							<div class="cf-faq-accordion__item" role="listitem">
								<button
									type="button"
									id="<?php echo esc_attr( $cf_trigger_id ); ?>"
									class="cf-faq-accordion__trigger"
									aria-expanded="false"
									aria-controls="<?php echo esc_attr( $cf_panel_id ); ?>"
								>
									<span class="cf-faq-accordion__question"><?php echo esc_html( $cf_item['question'] ); ?></span>
									<span class="cf-faq-accordion__chevron" aria-hidden="true">
										<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
									</span>
								</button>
								<div
									id="<?php echo esc_attr( $cf_panel_id ); ?>"
									class="cf-faq-accordion__panel"
									role="region"
									aria-labelledby="<?php echo esc_attr( $cf_trigger_id ); ?>"
									hidden
								>
									<p><?php echo esc_html( $cf_item['answer'] ); ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endforeach; ?>

			<section id="cf-platform-reviews" class="cf-faq-reviews" aria-labelledby="cf-faq-reviews-heading">
				<header class="cf-faq-section__head">
					<p class="cf-faq-section__eyebrow"><?php esc_html_e( '04 / FEEDBACK', 'collective-finity' ); ?></p>
					<h2 id="cf-faq-reviews-heading" class="cf-faq-section__title"><?php esc_html_e( 'Platform Reviews', 'collective-finity' ); ?></h2>
					<p class="cf-faq-reviews__sub">
						<?php esc_html_e( 'Share what you think about Collective Finity — design, ease of use, features, audio, and more.', 'collective-finity' ); ?>
					</p>
				</header>

				<?php if ( ! comments_open( $cf_faq_post_id ) ) : ?>
					<p class="cf-faq-reviews__closed"><?php esc_html_e( 'Reviews are currently closed.', 'collective-finity' ); ?></p>
				<?php elseif ( $cf_is_logged_in && $cf_current_user ) : ?>
					<form
						id="cf-platform-review-form"
						class="cf-faq-review-form"
						method="post"
						action="#"
						novalidate
						data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
						data-nonce="<?php echo esc_attr( wp_create_nonce( 'cf_platform_review' ) ); ?>"
						data-post-id="<?php echo esc_attr( (string) $cf_faq_post_id ); ?>"
					>
						<div class="cf-faq-review-form__top">
							<?php echo collective_finity_review_avatar( $cf_current_user->ID, 40 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<div class="cf-faq-review-form__identity">
								<span class="cf-faq-review-form__name"><?php echo esc_html( $cf_current_user->display_name ); ?></span>
								<span class="cf-faq-review-form__hint"><?php esc_html_e( 'Your review helps shape the platform.', 'collective-finity' ); ?></span>
							</div>
						</div>

						<fieldset class="cf-faq-review-form__fieldset">
							<legend class="cf-faq-review-form__legend"><?php esc_html_e( 'Star rating', 'collective-finity' ); ?></legend>
							<?php echo collective_finity_star_input_markup( 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</fieldset>

						<div class="cf-faq-review-form__fieldset">
							<label class="cf-faq-review-form__legend" for="cf-faq-review-topic">
								<?php esc_html_e( 'Topics (optional)', 'collective-finity' ); ?>
							</label>
							<div class="cf-faq-select-wrap">
								<select
									id="cf-faq-review-topic"
									name="cf_review_categories[]"
									class="cf-faq-select"
									aria-label="<?php esc_attr_e( 'Review topic', 'collective-finity' ); ?>"
								>
									<option value=""><?php esc_html_e( 'Select a topic…', 'collective-finity' ); ?></option>
									<?php foreach ( $cf_faq_categories as $cf_cat_slug => $cf_cat_label ) : ?>
										<option value="<?php echo esc_attr( $cf_cat_slug ); ?>"><?php echo esc_html( $cf_cat_label ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<label class="cf-faq-review-form__message-label" for="cf-faq-review-message">
							<?php esc_html_e( 'Your review', 'collective-finity' ); ?>
						</label>
						<textarea
							id="cf-faq-review-message"
							name="comment"
							rows="4"
							required
							minlength="10"
							placeholder="<?php esc_attr_e( 'Tell us what works, what could improve, and how Collective Finity feels to use…', 'collective-finity' ); ?>"
							aria-describedby="cf-faq-review-message-hint"
						></textarea>
						<p id="cf-faq-review-message-hint" class="cf-faq-review-form__char-hint">
							<?php esc_html_e( 'Minimum 10 characters.', 'collective-finity' ); ?>
						</p>

						<input type="hidden" name="cf_platform_review" value="1">
						<input type="hidden" name="comment_post_ID" value="<?php echo esc_attr( (string) $cf_faq_post_id ); ?>">

						<div class="cf-faq-review-form__actions">
							<button type="submit" class="cf-faq-btn cf-faq-btn--primary" id="cf-faq-review-submit">
								<?php esc_html_e( 'Submit Review', 'collective-finity' ); ?>
							</button>
							<p class="cf-faq-review-form__status" id="cf-faq-review-status" role="status" aria-live="polite" hidden></p>
						</div>
					</form>
				<?php else : ?>
					<div class="cf-faq-review-gate" role="region" aria-label="<?php esc_attr_e( 'Sign in to review', 'collective-finity' ); ?>">
						<p class="cf-faq-review-gate__msg"><?php esc_html_e( 'Please log in to leave a review.', 'collective-finity' ); ?></p>
						<a class="cf-faq-btn cf-faq-btn--primary" href="<?php echo esc_url( $cf_login_url ); ?>">
							<?php esc_html_e( 'Log In', 'collective-finity' ); ?>
						</a>
					</div>
				<?php endif; ?>

				<div class="cf-faq-review-list" id="cf-faq-review-list" aria-live="polite">
					<?php if ( ! empty( $cf_platform_reviews ) ) : ?>
						<?php foreach ( $cf_platform_reviews as $cf_review ) : ?>
							<?php echo collective_finity_platform_review_card_markup( $cf_review ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endforeach; ?>
					<?php else : ?>
						<p class="cf-faq-reviews__empty" id="cf-faq-reviews-empty">
							<?php esc_html_e( 'No platform reviews yet. Be the first to share your experience.', 'collective-finity' ); ?>
						</p>
					<?php endif; ?>
				</div>
			</section>

		</div>
	</div>
</main>

<style>
	.cf-faq-page.cf-page-shell {
		padding: 2.5rem clamp(16px, 3vw, 20px) 140px;
		max-width: 100%;
		min-width: 0;
		box-sizing: border-box;
	}

	.cf-faq-page .cf-page-container.cf-faq {
		max-width: min(820px, 100%);
		margin: 0 auto;
		min-width: 0;
	}

	.cf-faq__inner {
		display: flex;
		flex-direction: column;
		gap: 48px;
		min-width: 0;
	}

	.cf-faq-eyebrow,
	.cf-faq-section__eyebrow {
		margin: 0;
		font-family: var(--cf-mono, 'Space Mono', monospace);
		font-size: 11px;
		letter-spacing: 0.1em;
		text-transform: uppercase;
		color: var(--cf-accent, #FFB700);
	}

	.cf-faq-hero {
		position: relative;
		text-align: center;
		padding: clamp(48px, 7vw, 80px) clamp(20px, 4vw, 40px) clamp(56px, 8vw, 88px);
		border-radius: 18px;
		background: #0B0B0B;
		border: 1px solid rgba(30, 30, 30, 0.9);
		overflow: hidden;
		min-width: 0;
		max-width: 100%;
		box-sizing: border-box;
	}

	@property --cf-faq-hero-border-angle {
		syntax: '<angle>';
		initial-value: 0deg;
		inherits: false;
	}

	.cf-faq-hero__border {
		position: absolute;
		inset: 0;
		border-radius: inherit;
		padding: 1.5px;
		pointer-events: none;
		z-index: 2;
		background: conic-gradient(
			from var(--cf-faq-hero-border-angle),
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
		animation: cfFaqBorderTravel 5.5s linear infinite;
		filter: drop-shadow(0 0 6px rgba(255, 183, 0, 0.35));
	}

	@keyframes cfFaqBorderTravel {
		to { --cf-faq-hero-border-angle: 360deg; }
	}

	.cf-faq-hero__center-glow {
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
		animation: cfFaqCenterGlow 8.2s ease-in-out infinite;
		will-change: transform, opacity;
	}

	@keyframes cfFaqCenterGlow {
		0%, 100% {
			opacity: 0.35;
			transform: translate(-50%, -50%) scale(0.82);
		}
		50% {
			opacity: 0.7;
			transform: translate(-50%, -50%) scale(1.08);
		}
	}

	.cf-faq-hero__freq {
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

	.cf-faq-hero__icons {
		position: absolute;
		inset: 0;
		pointer-events: none;
		z-index: 0;
		overflow: hidden;
	}

	.cf-faq-hero__icon {
		position: absolute;
		display: block;
		width: clamp(48px, 8vw, 78px);
		aspect-ratio: 1;
		opacity: 0;
		background-image: linear-gradient(
			160deg,
			#c45a00 0%,
			var(--cf-accent, #FFB700) 48%,
			#FFE08A 100%
		);
		-webkit-mask-image: var(--cf-faq-icon-img);
		mask-image: var(--cf-faq-icon-img);
		-webkit-mask-size: contain;
		mask-size: contain;
		-webkit-mask-repeat: no-repeat;
		mask-repeat: no-repeat;
		-webkit-mask-position: center;
		mask-position: center;
		filter: saturate(1.15) brightness(0.95);
		animation: cfFaqIconPulse var(--cf-faq-icon-dur, 7.5s) ease-in-out infinite;
		animation-delay: var(--cf-faq-icon-delay, 0s);
	}

	@keyframes cfFaqIconPulse {
		0%, 100% { opacity: 0; }
		18%, 42% { opacity: 0.14; }
		30% { opacity: 0.2; }
		55%, 100% { opacity: 0; }
	}

	.cf-faq-hero__icon--1 {
		top: 9%;
		left: 3%;
		--cf-faq-icon-dur: 8.4s;
		--cf-faq-icon-delay: 0s;
	}

	.cf-faq-hero__icon--2 {
		top: 7%;
		right: 4%;
		width: clamp(44px, 7.5vw, 70px);
		--cf-faq-icon-dur: 9.6s;
		--cf-faq-icon-delay: -2.1s;
	}

	.cf-faq-hero__icon--3 {
		bottom: 16%;
		left: 5%;
		width: clamp(42px, 7vw, 66px);
		--cf-faq-icon-dur: 7.2s;
		--cf-faq-icon-delay: -4.4s;
	}

	.cf-faq-hero__icon--4 {
		bottom: 14%;
		right: 5%;
		width: clamp(46px, 8vw, 74px);
		--cf-faq-icon-dur: 10.1s;
		--cf-faq-icon-delay: -1.2s;
	}

	.cf-faq-hero__icon--5 {
		top: 44%;
		left: 1.5%;
		width: clamp(40px, 6.5vw, 62px);
		--cf-faq-icon-dur: 8.8s;
		--cf-faq-icon-delay: -5.6s;
	}

	.cf-faq-hero__content {
		position: relative;
		z-index: 1;
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 14px;
	}

	.cf-faq-hero .cf-faq-eyebrow {
		display: inline-block;
		border: 1px solid rgba(255, 183, 0, 0.35);
		border-radius: 999px;
		padding: 7px 16px;
		background: rgba(255, 183, 0, 0.08);
	}

	.cf-faq-hero__title {
		margin: 0;
		font-size: clamp(28px, 4vw, 36px);
		font-weight: 700;
		color: #fff;
		line-height: 1.15;
	}

	.cf-faq-hero__lead {
		margin: 0;
		max-width: 620px;
		font-size: 14.5px;
		line-height: 1.7;
		color: var(--cf-text-2, #B3B3B3);
	}

	.cf-faq-jump {
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		gap: 8px;
		margin-top: 6px;
	}

	.cf-faq-jump__link {
		display: inline-flex;
		align-items: center;
		padding: 8px 12px;
		border: 1px solid rgba(255, 255, 255, 0.14);
		border-radius: 9px;
		background: rgba(20, 20, 20, 0.72);
		color: #fff;
		font-size: 12.5px;
		font-weight: 600;
		text-decoration: none;
		backdrop-filter: blur(6px);
		-webkit-backdrop-filter: blur(6px);
		transition: border-color 0.15s ease, background 0.15s ease, color 0.15s ease;
	}

	.cf-faq-jump__link:hover,
	.cf-faq-jump__link:focus-visible {
		border-color: rgba(255, 183, 0, 0.45);
		background: rgba(24, 24, 24, 0.88);
		color: #fff;
	}

	.cf-faq-jump__link--accent {
		border-color: rgba(255, 183, 0, 0.4);
		color: var(--cf-accent, #FFB700);
		background: rgba(255, 183, 0, 0.08);
	}

	@media (prefers-reduced-motion: reduce) {
		.cf-faq-hero__border {
			animation: none;
			--cf-faq-hero-border-angle: 210deg;
			filter: drop-shadow(0 0 3px rgba(255, 183, 0, 0.2));
			opacity: 0.55;
		}

		.cf-faq-hero__center-glow {
			animation: none;
			opacity: 0.4;
			transform: translate(-50%, -50%) scale(0.95);
		}

		.cf-faq-hero__icon {
			animation: none;
			opacity: 0.08;
		}
	}

	@media (max-width: 767px) {
		.cf-faq-hero {
			padding: 36px 18px 48px;
		}

		.cf-faq-hero__icon--5 {
			display: none;
		}

		.cf-faq-hero__icon {
			width: clamp(38px, 13vw, 58px);
		}

		.cf-faq-jump {
			justify-content: center;
		}
	}

	.cf-faq-section__head {
		display: flex;
		flex-direction: column;
		gap: 8px;
		margin-bottom: 18px;
	}

	.cf-faq-section__title {
		margin: 0;
		font-size: clamp(20px, 2.5vw, 24px);
		font-weight: 700;
		color: #fff;
		line-height: 1.25;
	}

	.cf-faq-accordion {
		display: flex;
		flex-direction: column;
		gap: 10px;
	}

	.cf-faq-accordion__item {
		overflow: hidden;
		border: 1px solid var(--cf-border, #232323);
		border-radius: 10px;
		background: var(--cf-bg-card, #141414);
		transition: border-color 0.2s ease, box-shadow 0.2s ease;
	}

	.cf-faq-accordion__item.is-open {
		border-color: rgba(255, 183, 0, 0.28);
		box-shadow: 0 0 24px rgba(255, 183, 0, 0.06);
	}

	.cf-faq-accordion__trigger {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 12px;
		width: 100%;
		padding: 16px 18px;
		border: none;
		background: transparent;
		color: #fff;
		font-size: 14px;
		font-weight: 600;
		font-family: inherit;
		text-align: left;
		cursor: pointer;
		min-height: 48px;
		touch-action: manipulation;
	}

	.cf-faq-accordion__trigger:hover,
	.cf-faq-accordion__trigger:focus-visible {
		background: #181818;
	}

	.cf-faq-accordion__trigger:focus-visible {
		outline: 2px solid var(--cf-accent, #FFB700);
		outline-offset: -2px;
	}

	.cf-faq-accordion__question {
		flex: 1;
		min-width: 0;
	}

	.cf-faq-accordion__chevron {
		display: flex;
		flex-shrink: 0;
		color: var(--cf-text-3, #7A7A7A);
		transition: transform 0.2s ease, color 0.15s ease;
	}

	.cf-faq-accordion__item.is-open .cf-faq-accordion__chevron {
		transform: rotate(180deg);
		color: var(--cf-accent, #FFB700);
	}

	.cf-faq-accordion__panel {
		padding: 0 18px 18px;
	}

	.cf-faq-accordion__panel p {
		margin: 0;
		font-size: 13.5px;
		line-height: 1.65;
		color: var(--cf-text-2, #B3B3B3);
	}

	.cf-faq-reviews__sub {
		margin: 4px 0 0;
		font-size: 14px;
		line-height: 1.6;
		color: var(--cf-text-2, #B3B3B3);
	}

	.cf-faq-reviews__empty,
	.cf-faq-reviews__closed {
		margin: 0;
		color: var(--cf-text-3, #7A7A7A);
		font-size: 13.5px;
	}

	.cf-faq-review-form {
		display: flex;
		flex-direction: column;
		gap: 16px;
		padding: 20px;
		border: 1px solid var(--cf-border, #232323);
		border-radius: 12px;
		background: var(--cf-bg-card, #141414);
		margin-bottom: 24px;
	}

	.cf-faq-review-form__top {
		display: flex;
		align-items: center;
		gap: 12px;
	}

	.cf-faq-review-form__identity {
		display: flex;
		flex-direction: column;
		gap: 2px;
		min-width: 0;
	}

	.cf-faq-review-form__name {
		font-size: 14px;
		font-weight: 700;
		color: #fff;
	}

	.cf-faq-review-form__hint {
		font-size: 12px;
		color: var(--cf-text-3, #7A7A7A);
	}

	.cf-faq-review-form__fieldset {
		margin: 0;
		padding: 0;
		border: none;
	}

	.cf-faq-review-form__legend,
	.cf-faq-review-form__message-label {
		display: block;
		margin-bottom: 8px;
		font-size: 12.5px;
		font-weight: 600;
		color: #fff;
	}

	.cf-faq-select-wrap {
		position: relative;
	}

	.cf-faq-select-wrap::after {
		content: '';
		position: absolute;
		top: 50%;
		right: 14px;
		width: 8px;
		height: 8px;
		border-right: 2px solid var(--cf-text-3, #7A7A7A);
		border-bottom: 2px solid var(--cf-text-3, #7A7A7A);
		transform: translateY(-60%) rotate(45deg);
		pointer-events: none;
	}

	.cf-faq-select {
		display: block;
		width: 100%;
		max-width: 100%;
		padding: 12px 40px 12px 14px;
		border-radius: 9px;
		border: 1px solid var(--cf-border, #232323);
		background: var(--cf-bg-dark, #0D0D0D);
		color: #fff;
		font-size: 13.5px;
		font-family: inherit;
		line-height: 1.4;
		min-height: 44px;
		cursor: pointer;
		appearance: none;
		-webkit-appearance: none;
		box-sizing: border-box;
		transition: border-color 0.15s ease;
	}

	.cf-faq-select:hover,
	.cf-faq-select:focus {
		outline: none;
		border-color: rgba(255, 183, 0, 0.45);
	}

	.cf-faq-select option {
		background: #141414;
		color: #fff;
	}

	.cf-faq-review-form textarea {
		width: 100%;
		padding: 12px 14px;
		border-radius: 9px;
		border: 1px solid var(--cf-border, #232323);
		background: var(--cf-bg-dark, #0D0D0D);
		color: #fff;
		font-size: 13.5px;
		font-family: var(--cf-body, inherit);
		line-height: 1.5;
		resize: vertical;
		box-sizing: border-box;
	}

	.cf-faq-review-form textarea:focus {
		outline: none;
		border-color: rgba(255, 183, 0, 0.45);
	}

	.cf-faq-review-form__char-hint {
		margin: -6px 0 0;
		font-size: 11.5px;
		color: var(--cf-text-4, #5a5a5a);
	}

	.cf-faq-review-form__actions {
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		gap: 12px;
	}

	.cf-faq-btn {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		padding: 11px 20px;
		border-radius: 9px;
		font-size: 13.5px;
		font-weight: 700;
		line-height: 1.2;
		text-decoration: none;
		border: none;
		cursor: pointer;
		min-height: 44px;
		touch-action: manipulation;
		transition: background 0.15s ease, color 0.15s ease;
	}

	.cf-faq-btn--primary {
		background: var(--cf-accent, #FFB700);
		color: #0D0D0D;
	}

	.cf-faq-btn--primary:hover,
	.cf-faq-btn--primary:focus-visible {
		background: var(--cf-accent-hover, #ffc633);
		color: #0D0D0D;
	}

	.cf-faq-btn--primary:disabled {
		opacity: 0.55;
		cursor: not-allowed;
	}

	.cf-faq-review-form__status {
		margin: 0;
		font-size: 13px;
		color: var(--cf-text-2, #B3B3B3);
	}

	.cf-faq-review-form__status.is-error {
		color: #ff8a8a;
	}

	.cf-faq-review-form__status.is-success {
		color: var(--cf-accent, #FFB700);
	}

	.cf-faq-review-gate {
		display: flex;
		flex-direction: column;
		align-items: flex-start;
		gap: 12px;
		padding: 24px 20px;
		margin-bottom: 24px;
		border: 1px solid var(--cf-border, #232323);
		border-radius: 12px;
		background: linear-gradient(160deg, rgba(255, 183, 0, 0.06), transparent 55%), var(--cf-bg-card, #141414);
	}

	.cf-faq-review-gate__msg {
		margin: 0;
		font-size: 14.5px;
		font-weight: 600;
		color: #fff;
	}

	.cf-faq-review-list {
		display: flex;
		flex-direction: column;
		gap: 14px;
	}

	.cf-faq-review {
		display: flex;
		gap: 14px;
		align-items: flex-start;
		padding-bottom: 14px;
		border-bottom: 1px solid var(--cf-divider, #1a1a1a);
	}

	.cf-faq-review__body {
		flex: 1;
		min-width: 0;
	}

	.cf-faq-review__head {
		display: flex;
		align-items: center;
		gap: 10px;
		flex-wrap: wrap;
	}

	.cf-faq-review__name {
		font-size: 13.5px;
		font-weight: 700;
		color: #fff;
	}

	.cf-faq-review__date {
		font-size: 11.5px;
		color: var(--cf-text-4, #5a5a5a);
		font-family: var(--cf-mono, 'Space Mono', monospace);
	}

	.cf-faq-review__cats {
		display: flex;
		flex-wrap: wrap;
		gap: 6px;
		margin: 8px 0 0;
		padding: 0;
		list-style: none;
	}

	.cf-faq-review__cats li {
		padding: 3px 8px;
		border-radius: 6px;
		border: 1px solid rgba(255, 183, 0, 0.22);
		background: rgba(255, 183, 0, 0.08);
		font-size: 10.5px;
		font-weight: 600;
		color: var(--cf-accent, #FFB700);
	}

	.cf-faq-review__text {
		margin-top: 8px;
		font-size: 13px;
		color: var(--cf-text-2, #B3B3B3);
		line-height: 1.6;
	}

	.cf-faq-review__text p {
		margin: 0 0 8px;
	}

	/* Reuse article star input styles on this page */
	.cf-faq-page .cf-stars { display: inline-flex; gap: 3px; }
	.cf-faq-page .cf-star { display: inline-flex; color: #3a3a3a; }
	.cf-faq-page .cf-star.is-on { color: var(--cf-accent, #FFB700); }
	.cf-faq-page .cf-stars-input { display: inline-flex; flex-direction: row-reverse; justify-content: flex-end; gap: 4px; }
	.cf-faq-page .cf-stars-input input { position: absolute; opacity: 0; width: 1px; height: 1px; }
	.cf-faq-page .cf-stars-input label { display: inline-flex; color: #3a3a3a; cursor: pointer; transition: color 0.12s ease; padding: 4px; min-width: 32px; min-height: 32px; align-items: center; justify-content: center; }
	.cf-faq-page .cf-stars-input label:hover,
	.cf-faq-page .cf-stars-input label:hover ~ label,
	.cf-faq-page .cf-stars-input input:checked ~ label { color: var(--cf-accent, #FFB700); }
	.cf-faq-page .cf-stars-input input:focus-visible + label { outline: 2px solid var(--cf-accent, #FFB700); outline-offset: 2px; border-radius: 4px; }

	@media (max-width: 767px) {
		.cf-faq__inner {
			gap: 36px;
		}

		.cf-faq-review-form {
			padding: 16px;
		}
	}
</style>

<script>
(function () {
	function initAccordion(root) {
		var triggers = Array.prototype.slice.call(root.querySelectorAll('.cf-faq-accordion__trigger'));
		if (!triggers.length) {
			return;
		}

		function setOpen(item, open) {
			var trigger = item.querySelector('.cf-faq-accordion__trigger');
			var panel = item.querySelector('.cf-faq-accordion__panel');
			if (!trigger || !panel) {
				return;
			}
			item.classList.toggle('is-open', open);
			trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
			panel.hidden = !open;
		}

		function closeAll(except) {
			root.querySelectorAll('.cf-faq-accordion__item').forEach(function (item) {
				if (item !== except) {
					setOpen(item, false);
				}
			});
		}

		root.addEventListener('click', function (event) {
			var trigger = event.target.closest('.cf-faq-accordion__trigger');
			if (!trigger || !root.contains(trigger)) {
				return;
			}
			var item = trigger.closest('.cf-faq-accordion__item');
			if (!item) {
				return;
			}
			var willOpen = !item.classList.contains('is-open');
			closeAll(item);
			setOpen(item, willOpen);
		});

		root.addEventListener('keydown', function (event) {
			var trigger = event.target.closest('.cf-faq-accordion__trigger');
			if (!trigger || !root.contains(trigger)) {
				return;
			}

			var index = triggers.indexOf(trigger);
			if (index < 0) {
				return;
			}

			var next = -1;
			if (event.key === 'ArrowDown' || event.key === 'ArrowRight') {
				next = (index + 1) % triggers.length;
			} else if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') {
				next = (index - 1 + triggers.length) % triggers.length;
			} else if (event.key === 'Home') {
				next = 0;
			} else if (event.key === 'End') {
				next = triggers.length - 1;
			} else if (event.key === 'Enter' || event.key === ' ') {
				event.preventDefault();
				trigger.click();
				return;
			}

			if (next >= 0) {
				event.preventDefault();
				triggers[next].focus();
			}
		});
	}

	document.querySelectorAll('[data-cf-faq-accordion]').forEach(initAccordion);

	var form = document.getElementById('cf-platform-review-form');
	if (!form) {
		return;
	}

	var statusEl = document.getElementById('cf-faq-review-status');
	var submitBtn = document.getElementById('cf-faq-review-submit');
	var listEl = document.getElementById('cf-faq-review-list');

	function showStatus(message, type) {
		if (!statusEl) {
			return;
		}
		statusEl.hidden = false;
		statusEl.textContent = message;
		statusEl.classList.remove('is-error', 'is-success');
		if (type) {
			statusEl.classList.add(type);
		}
	}

	form.addEventListener('submit', function (event) {
		event.preventDefault();

		var ratingInput = form.querySelector('input[name="cf_rating"]:checked');
		var message = (form.querySelector('[name="comment"]') || {}).value || '';
		message = message.trim();

		if (!ratingInput) {
			showStatus('Please select a star rating.', 'is-error');
			return;
		}
		if (message.length < 10) {
			showStatus('Please write at least 10 characters.', 'is-error');
			return;
		}

		var topicSelect = form.querySelector('#cf-faq-review-topic');
		var topicValue = topicSelect ? String(topicSelect.value || '').trim() : '';

		var body = new FormData();
		body.append('action', 'cf_submit_platform_review');
		body.append('nonce', form.getAttribute('data-nonce') || '');
		body.append('comment_post_ID', form.getAttribute('data-post-id') || '');
		body.append('cf_rating', ratingInput.value);
		body.append('comment', message);
		body.append('cf_platform_review', '1');
		if (topicValue) {
			body.append('cf_review_categories[]', topicValue);
		}

		if (submitBtn) {
			submitBtn.disabled = true;
		}
		showStatus('Submitting…', '');

		fetch(form.getAttribute('data-ajax-url') || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			credentials: 'same-origin',
			body: body
		})
			.then(function (response) {
				return response.json().then(function (data) {
					return { ok: response.ok, data: data };
				});
			})
			.then(function (result) {
				var payload = result.data || {};
				if (!payload.success) {
					var err = (payload.data && payload.data.message) || 'Something went wrong. Please try again.';
					showStatus(err, 'is-error');
					return;
				}

				var data = payload.data || {};
				showStatus(data.message || 'Review submitted.', 'is-success');
				form.reset();

				if (data.html && listEl) {
					var empty = document.getElementById('cf-faq-reviews-empty');
					if (empty) {
						empty.remove();
					}
					listEl.insertAdjacentHTML('afterbegin', data.html);
				}
			})
			.catch(function () {
				showStatus('Network error. Please try again.', 'is-error');
			})
			.finally(function () {
				if (submitBtn) {
					submitBtn.disabled = false;
				}
			});
	});
})();
</script>

<?php
get_footer();
