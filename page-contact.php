<?php
/**
 * Template Name: Contact Page
 * Description: Theme template for Contact Us pages.
 *
 * @package Collective_Finity
 */

$cf_community_url      = collective_finity_get_page_link( 'join-community', '/join-community/' );
$cf_discord_url        = collective_finity_get_theme_option( 'social_discord' );
$cf_facebook_url       = collective_finity_get_theme_option( 'social_facebook' );
// Facebook Page (social_facebook) and Facebook Group (social_facebook_group) are separate
// theme options — both must be filled with their real (different) URLs in WP admin for these links to work.
$cf_facebook_group_url = collective_finity_get_theme_option( 'social_facebook_group' );

if ( ! $cf_discord_url ) {
	$cf_discord_url = $cf_community_url;
}
if ( ! $cf_facebook_url ) {
	$cf_facebook_url = '#';
}
if ( ! $cf_facebook_group_url ) {
	$cf_facebook_group_url = '#';
}

$cf_contact_methods = array(
	array(
		'icon'  => 'fa-solid fa-envelope',
		'title' => __( 'Email Us', 'collective-finity' ),
		'desc'  => __( 'Got questions? We\'re here to help.', 'collective-finity' ),
		'value' => 'contact@collectivefinity.com',
		'href'  => 'mailto:contact@collectivefinity.com',
	),
	array(
		'icon'  => 'fa-solid fa-location-dot',
		'title' => __( 'Our Location', 'collective-finity' ),
		'desc'  => __( 'Based in the heart of Europe.', 'collective-finity' ),
		'value' => __( 'Netherlands', 'collective-finity' ),
		'href'  => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( 'The Hague, Netherlands' ),
	),
	array(
		'icon'       => 'fa-brands fa-discord',
		'title'      => __( 'Discord Server', 'collective-finity' ),
		'desc'       => __( 'Join our vibrant community of creators.', 'collective-finity' ),
		'btn'        => __( 'Join Official Server', 'collective-finity' ),
		'href'       => $cf_discord_url,
		'highlight'  => true,
		'badge'      => __( 'Join community', 'collective-finity' ),
	),
	array(
		'icon'  => 'fa-solid fa-users',
		'title' => __( 'Facebook Group', 'collective-finity' ),
		'desc'  => __( 'Connect with fellow music lovers.', 'collective-finity' ),
		'btn'   => __( 'Join the group', 'collective-finity' ),
		'href'  => $cf_facebook_group_url,
	),
	array(
		'icon'      => 'fa-brands fa-facebook-f',
		'title'     => __( 'Facebook Page', 'collective-finity' ),
		'desc'      => __( 'Follow us for latest updates.', 'collective-finity' ),
		'btn'       => __( 'Follow Our Page', 'collective-finity' ),
		'href'      => $cf_facebook_url,
		'full_row'  => true,
	),
);

$cf_blog_url       = get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : '';
$cf_blog_available = (int) wp_count_posts( 'post' )->publish;
$cf_blog_limit     = min( 3, max( 0, $cf_blog_available ) );
$cf_recent_posts   = ( $cf_blog_limit > 0 )
	? new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => $cf_blog_limit,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	)
	: new WP_Query( array( 'post__in' => array( 0 ) ) );

wp_enqueue_style(
	'cf-contact-font-awesome',
	'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
	array(),
	'6.5.2'
);

get_header();
?>

<main id="primary" class="site-main cf-contact-page" role="main">
	<div class="cf-contact-page__inner">

		<section class="cf-contact-hero" aria-labelledby="cf-contact-hero-heading">
			<div class="cf-contact-hero__border" aria-hidden="true"></div>
			<div class="cf-contact-hero__center-glow" aria-hidden="true"></div>
			<div class="cf-contact-hero__content">
				<span class="cf-contact-hero__badge"><?php esc_html_e( 'Get In Touch', 'collective-finity' ); ?></span>
				<h1 id="cf-contact-hero-heading" class="cf-contact-hero__title">
					<?php esc_html_e( 'Let\'s Talk', 'collective-finity' ); ?>
				</h1>
				<p class="cf-contact-hero__lead">
					<?php esc_html_e( 'Have a question, feedback, or a partnership idea? Reach out through any of the channels below.', 'collective-finity' ); ?>
				</p>
				<div class="cf-contact-hero__actions">
					<a class="cf-contact-btn cf-contact-btn--primary" href="#cf-contact-form">
						<?php esc_html_e( 'Send a message', 'collective-finity' ); ?>
					</a>
				</div>
			</div>
		</section>

		<section class="cf-contact-together" aria-labelledby="cf-contact-together-heading">
			<div class="cf-contact-together__grid">
				<div class="cf-contact-together__left">
					<span class="cf-contact-eyebrow"><?php esc_html_e( 'Get in touch', 'collective-finity' ); ?></span>
					<h2 id="cf-contact-together-heading" class="cf-contact-together__title">
						<?php esc_html_e( 'Let\'s create ', 'collective-finity' ); ?><span class="cf-contact-hero__accent"><?php esc_html_e( 'together', 'collective-finity' ); ?></span>
					</h2>
					<p class="cf-contact-together__lead">
						<?php esc_html_e( 'Have a question or want to join the collective? Reach out directly through our official channels.', 'collective-finity' ); ?>
					</p>

					<div class="cf-contact-methods">
						<?php foreach ( $cf_contact_methods as $method ) : ?>
							<article class="cf-contact-method-card<?php echo ! empty( $method['highlight'] ) ? ' cf-contact-method-card--highlight' : ''; ?><?php echo ! empty( $method['full_row'] ) ? ' cf-contact-method-card--full' : ''; ?>">
								<?php if ( ! empty( $method['badge'] ) ) : ?>
									<span class="cf-contact-method-card__badge"><?php echo esc_html( $method['badge'] ); ?></span>
								<?php endif; ?>

								<div class="cf-contact-method-card__body">
									<div class="cf-contact-method-card__icon" aria-hidden="true">
										<i class="<?php echo esc_attr( $method['icon'] ); ?>"></i>
									</div>
									<div class="cf-contact-method-card__copy">
										<h3 class="cf-contact-method-card__title"><?php echo esc_html( $method['title'] ); ?></h3>
										<p class="cf-contact-method-card__desc"><?php echo esc_html( $method['desc'] ); ?></p>
										<?php if ( ! empty( $method['value'] ) ) : ?>
											<?php if ( ! empty( $method['href'] ) ) : ?>
												<a class="cf-contact-method-card__value" href="<?php echo esc_url( $method['href'] ); ?>"><?php echo esc_html( $method['value'] ); ?></a>
											<?php else : ?>
												<p class="cf-contact-method-card__value"><?php echo esc_html( $method['value'] ); ?></p>
											<?php endif; ?>
										<?php endif; ?>
										<?php if ( ! empty( $method['btn'] ) && ! empty( $method['href'] ) ) : ?>
											<a class="cf-contact-method-card__link" href="<?php echo esc_url( $method['href'] ); ?>"<?php echo '#' === $method['href'] ? '' : ' target="_blank" rel="noopener noreferrer"'; ?>>
												<?php echo esc_html( $method['btn'] ); ?>
											</a>
										<?php endif; ?>
									</div>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="cf-contact-together__right" id="cf-contact-form">
					<div class="cf-contact-form-wrap">
						<?php echo do_shortcode( '[contact-form-7 id="04d6245" title="Contact form Finity"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</div>
			</div>
		</section>

		<section class="cf-contact-releases" aria-label="<?php esc_attr_e( 'Latest releases', 'collective-finity' ); ?>">
			<?php echo do_shortcode( '[cf_latest_releases limit="4"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</section>

		<?php if ( $cf_recent_posts->have_posts() ) : ?>
			<section class="cf-from-blog" aria-labelledby="cf-contact-blog-heading">
				<div class="cf-section-head">
					<h2 id="cf-contact-blog-heading" class="cf-section-title"><?php esc_html_e( 'From the Blog', 'collective-finity' ); ?></h2>
					<?php if ( $cf_blog_url ) : ?>
						<a class="cf-section-link" href="<?php echo esc_url( $cf_blog_url ); ?>"><?php esc_html_e( 'View All', 'collective-finity' ); ?> &rarr;</a>
					<?php endif; ?>
				</div>
				<div class="cf-blog-rail">
					<?php
					while ( $cf_recent_posts->have_posts() ) :
						$cf_recent_posts->the_post();
						$cf_pid   = get_the_ID();
						$cf_thumb = get_the_post_thumbnail_url( $cf_pid, 'medium_large' );
						$cf_cats  = get_the_category();
						$cf_cat   = ! empty( $cf_cats ) ? $cf_cats[0]->name : __( 'Article', 'collective-finity' );

						if ( $cf_thumb ) {
							$cf_card_art = "background-image: url('" . esc_url( $cf_thumb ) . "');";
						} else {
							$cf_card_art = 'background: ' . esc_attr( collective_finity_gradient_for( $cf_pid + 60 ) ) . ';';
						}
						?>
						<a class="cf-blog-card" href="<?php the_permalink(); ?>">
							<div class="cf-blog-card-art-wrap">
								<div class="cf-blog-card-art" style="<?php echo $cf_card_art; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></div>
							</div>
							<div class="cf-blog-card-body">
								<span class="cf-blog-card-cat"><?php echo esc_html( strtoupper( $cf_cat ) ); ?></span>
								<div class="cf-blog-card-title"><?php the_title(); ?></div>
								<div class="cf-blog-card-meta"><?php echo esc_html( get_the_date() ); ?></div>
							</div>
						</a>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			</section>
		<?php endif; ?>

	</div>
</main>

<style>
.cf-contact-page {
	background: #0B0B0B;
	color: #fff;
	padding: 48px 5px 5px;
	box-sizing: border-box;
	max-width: 100%;
	min-width: 0;
}
.cf-contact-page__inner {
	max-width: 100%;
	min-width: 0;
	margin: 0 auto;
	display: flex;
	flex-direction: column;
	gap: 56px;
}
.cf-contact-hero {
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
@property --cf-contact-hero-border-angle {
	syntax: '<angle>';
	initial-value: 0deg;
	inherits: false;
}
.cf-contact-hero__border {
	position: absolute;
	inset: 0;
	border-radius: inherit;
	padding: 1.5px;
	pointer-events: none;
	z-index: 2;
	background: conic-gradient(
		from var(--cf-contact-hero-border-angle),
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
	animation: cfContactBorderTravel 5.5s linear infinite;
	filter: drop-shadow(0 0 6px rgba(255, 183, 0, 0.35));
}
@keyframes cfContactBorderTravel {
	to { --cf-contact-hero-border-angle: 360deg; }
}
.cf-contact-hero__center-glow {
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
	animation: cfContactCenterGlow 8.2s ease-in-out infinite;
	will-change: transform, opacity;
}
@keyframes cfContactCenterGlow {
	0%, 100% {
		opacity: 0.35;
		transform: translate(-50%, -50%) scale(0.82);
	}
	50% {
		opacity: 0.7;
		transform: translate(-50%, -50%) scale(1.08);
	}
}
.cf-contact-hero__content {
	position: relative;
	z-index: 1;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 14px;
}
.cf-contact-hero__badge,
.cf-contact-eyebrow {
	display: inline-block;
	font-family: 'Space Mono', monospace;
	font-size: 11px;
	letter-spacing: 0.1em;
	text-transform: uppercase;
	color: #FFB700;
	border: 1px solid rgba(255, 183, 0, 0.35);
	border-radius: 999px;
	padding: 7px 16px;
	background: rgba(255, 183, 0, 0.08);
}
.cf-contact-eyebrow {
	font-size: 10.5px;
	padding: 6px 14px;
	margin-bottom: 16px;
}
.cf-contact-hero__title,
.cf-contact-together__title {
	margin: 0;
	font-family: 'Space Mono', monospace;
	font-size: clamp(28px, 5vw, 40px);
	font-weight: 700;
	line-height: 1.15;
	color: #fff;
}
.cf-contact-together__title {
	font-size: clamp(26px, 4vw, 32px);
}
.cf-contact-hero__accent {
	color: #FFB700;
}
.cf-contact-hero__lead,
.cf-contact-together__lead {
	margin: 0;
	max-width: 520px;
	font-size: 14px;
	line-height: 1.7;
	color: #B3B3B3;
}
.cf-contact-together__lead {
	font-size: 13.5px;
	line-height: 1.7;
	max-width: 520px;
}
.cf-contact-hero__actions {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
	justify-content: center;
	margin-top: 8px;
}
@media (prefers-reduced-motion: reduce) {
	.cf-contact-hero__border,
	.cf-contact-hero__center-glow {
		animation: none;
	}
}
.cf-contact-btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	padding: 13px 26px;
	border-radius: 9px;
	font-family: 'Space Mono', monospace;
	font-size: 13px;
	font-weight: 700;
	letter-spacing: 0.05em;
	text-decoration: none;
	text-transform: capitalize;
	transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
}
.cf-contact-btn--primary {
	background: #FFB700;
	color: #0D0D0D;
	border: none;
}
.cf-contact-btn--primary:hover,
.cf-contact-btn--primary:focus-visible {
	background: #ffde99;
	color: #0D0D0D;
}
.cf-contact-btn--outline {
	background: transparent;
	color: #fff;
	border: 1px solid #333;
}
.cf-contact-btn--outline:hover,
.cf-contact-btn--outline:focus-visible {
	background: #161616;
	color: #fff;
}
.cf-contact-together__grid {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 40px;
	align-items: start;
}
.cf-contact-methods {
	display: grid;
	grid-template-columns: repeat(2, minmax(0, 1fr));
	gap: 16px;
	margin-top: 20px;
}
.cf-contact-method-card {
	position: relative;
	display: flex;
	flex-direction: column;
	gap: 10px;
	padding: 20px;
	border-radius: 12px;
	background: #141414;
	border: 1px solid #232323;
	transition: border-color 0.2s ease, box-shadow 0.25s ease;
}
.cf-contact-method-card:hover {
	border-color: rgba(255, 183, 0, 0.22);
	box-shadow: 0 0 28px rgba(255, 183, 0, 0.08);
}
.cf-contact-method-card--highlight {
	border-color: #FFB700;
	padding-top: 36px;
}
.cf-contact-method-card--full {
	grid-column: 1 / -1;
}
.cf-contact-method-card--full .cf-contact-method-card__body {
	flex-direction: row;
	align-items: center;
	gap: 16px;
}
.cf-contact-method-card__badge {
	position: absolute;
	top: 14px;
	right: 14px;
	font-family: 'Space Mono', monospace;
	font-size: 9.5px;
	font-weight: 700;
	letter-spacing: 0.04em;
	color: #0D0D0D;
	background: #FFB700;
	padding: 4px 9px;
	border-radius: 999px;
	text-transform: uppercase;
	white-space: nowrap;
}
.cf-contact-method-card__body {
	display: flex;
	flex-direction: column;
	gap: 10px;
}
.cf-contact-method-card__icon {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 42px;
	height: 42px;
	border-radius: 50%;
	background: rgba(255, 183, 0, 0.12);
	color: #FFB700;
	font-size: 18px;
	flex-shrink: 0;
}
.cf-contact-method-card__title {
	margin: 0;
	font-family: 'Space Mono', monospace;
	font-size: 14.5px;
	font-weight: 700;
	color: #fff;
}
.cf-contact-method-card__desc {
	margin: 0;
	font-size: 12.5px;
	line-height: 1.5;
	color: #7A7A7A;
}
.cf-contact-method-card__value {
	margin: 0;
	font-family: 'Space Mono', monospace;
	font-size: 13px;
	color: #FFB700;
	text-decoration: none;
}
.cf-contact-method-card__value:hover,
.cf-contact-method-card__link:hover {
	color: #ffde99;
}
.cf-contact-method-card__link {
	font-family: 'Space Mono', monospace;
	font-size: 12.5px;
	color: #FFB700;
	text-decoration: none;
	width: fit-content;
}
.cf-contact-form-wrap .wpcf7 form {
	display: flex;
	flex-direction: column;
	gap: 10px;
}
.cf-contact-form-wrap .wpcf7 form p {
	margin: 0;
}
.cf-contact-form-wrap .wpcf7 label {
	display: block;
	margin-bottom: 4px;
	font-family: 'Space Mono', monospace;
	font-size: 11px;
	letter-spacing: 0.06em;
	color: #ffde99;
	text-transform: uppercase;
}
.cf-contact-form-wrap .wpcf7 input[type="text"],
.cf-contact-form-wrap .wpcf7 input[type="email"],
.cf-contact-form-wrap .wpcf7 input[type="tel"],
.cf-contact-form-wrap .wpcf7 input[type="url"],
.cf-contact-form-wrap .wpcf7 textarea,
.cf-contact-form-wrap .wpcf7 select {
	width: 100%;
	padding: 11px 14px;
	border: 1px solid #FFB700;
	border-radius: 9px;
	background: #0D0D0D;
	color: #fff;
	font-size: 13.5px;
	font-family: 'Space Mono', monospace;
	box-sizing: border-box;
	outline: none;
	transition: border-color 0.2s ease;
}
.cf-contact-form-wrap .wpcf7 input[type="text"]:hover,
.cf-contact-form-wrap .wpcf7 input[type="email"]:hover,
.cf-contact-form-wrap .wpcf7 input[type="tel"]:hover,
.cf-contact-form-wrap .wpcf7 input[type="url"]:hover,
.cf-contact-form-wrap .wpcf7 textarea:hover,
.cf-contact-form-wrap .wpcf7 select:hover {
	border-color: #ffde99;
}
.cf-contact-form-wrap .wpcf7 textarea {
	resize: vertical;
	min-height: 120px;
}
.cf-contact-form-wrap .wpcf7 input:focus,
.cf-contact-form-wrap .wpcf7 textarea:focus,
.cf-contact-form-wrap .wpcf7 select:focus {
	border-color: #ffde99;
	box-shadow: none;
}
.cf-contact-form-wrap .wpcf7 input::placeholder,
.cf-contact-form-wrap .wpcf7 textarea::placeholder {
	color: #ffde99;
	opacity: 1;
}
.cf-contact-form-wrap .wpcf7 input[type="submit"],
.cf-contact-form-wrap .wpcf7 button[type="submit"] {
	width: fit-content;
	margin-top: 4px;
	padding: 14px 20px;
	border: none;
	border-radius: 9px;
	background: #FFB700;
	color: #0D0D0D;
	font-family: 'Space Mono', monospace;
	font-size: 13px;
	font-weight: 700;
	letter-spacing: 0.05em;
	cursor: pointer;
	transition: background 0.15s ease;
}
.cf-contact-form-wrap .wpcf7 input[type="submit"]:hover,
.cf-contact-form-wrap .wpcf7 button[type="submit"]:hover {
	background: #ffde99;
}
.cf-contact-form-wrap .wpcf7-response-output {
	margin: 0;
	padding: 14px 16px;
	border-radius: 9px;
	font-family: 'Space Mono', monospace;
	font-size: 13px;
}
.cf-contact-releases .cf-latest-releases-shortcode__title {
	font-family: 'Space Mono', monospace;
	font-size: 19px;
	font-weight: 700;
}
@media (max-width: 900px) {
	.cf-contact-page {
		padding-bottom: 5px;
	}
	.cf-contact-page__inner {
		gap: 40px;
	}
	.cf-contact-hero {
		padding: 40px 16px;
	}
	.cf-contact-together__grid {
		grid-template-columns: 1fr;
	}
	.cf-contact-methods {
		grid-template-columns: 1fr;
	}
	.cf-contact-method-card--full .cf-contact-method-card__body {
		flex-direction: column;
		align-items: flex-start;
	}
}
</style>

<?php
get_footer();
