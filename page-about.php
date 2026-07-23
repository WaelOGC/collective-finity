<?php
/**
 * Template Name: About Page
 * Description: Theme template for the About page.
 */

$cf_tracks_url = get_post_type_archive_link( 'tracks' );
if ( ! $cf_tracks_url ) {
	$cf_tracks_url = home_url( '/tracks/' );
}

$cf_community_url = function_exists( 'collective_finity_get_page_link' )
	? collective_finity_get_page_link( 'join-community', '/join-community/' )
	: home_url( '/join-community/' );

$cf_hero_image_url = get_template_directory_uri() . '/assets/images/hero-section/about-collective-finity-ai-music-vision.webp';
$cf_cta_image_url  = get_template_directory_uri() . '/assets/images/section-background/join-the-collective-journey.webp';
$cf_founder_photo_url = 'https://collectivefinity.com/wp-content/uploads/2026/07/Wael-Safan-%E2%80%94-Founder-of-Collective-Finity.jpg';

$cf_about_why_cards = array(
	array(
		'icon'  => 'music',
		'title' => 'Meaningful Music',
		'text'  => 'We focus on emotion, storytelling, and cinematic sound.',
	),
	array(
		'icon'  => 'book',
		'title' => 'Real Knowledge',
		'text'  => 'Practical articles and guides based on thousands of hours of experimentation.',
	),
	array(
		'icon'  => 'people',
		'title' => 'Creative Community',
		'text'  => 'A place for artists, producers, and creators to learn and grow together.',
	),
);

$cf_about_pillars = array(
	array(
		'icon'  => 'brain',
		'title' => 'Human Creativity First',
		'text'  => 'Artificial intelligence is our instrument—not our replacement. Every piece of music begins with imagination, emotion, and artistic direction before technology becomes part of the creative process.',
	),
	array(
		'icon'  => 'flask',
		'title' => 'Learn Through Experience',
		'text'  => 'Everything published on Collective Finity comes from real experimentation. No recycled tutorials. No generic advice. Only practical knowledge gained through thousands of hours of testing, refining, and creating.',
	),
	array(
		'icon'  => 'people',
		'title' => 'Build Together',
		'text'  => 'Collective Finity is not meant to remain a personal project. It is the foundation of a future community where artists, producers, and creators can collaborate, learn, and inspire one another.',
	),
);

$cf_about_roadmap = array(
	array(
		'icon'  => 'rocket',
		'title' => 'Launch Collective Finity',
		'text'  => 'Building the first collection of cinematic AI-assisted music and educational content.',
	),
	array(
		'icon'  => 'music',
		'title' => 'Growing the Library',
		'text'  => 'Continuously expanding the music catalog and publishing practical resources for AI music creators.',
	),
	array(
		'icon'  => 'people',
		'title' => 'Building the Community',
		'text'  => 'Creating a collaborative environment where artists exchange knowledge, workflows, and inspiration.',
	),
	array(
		'icon'  => 'book',
		'title' => 'Educational Resources',
		'text'  => 'Launching premium written courses, creator guides, and advanced learning materials.',
	),
	array(
		'icon'  => 'artist',
		'title' => 'Artist Platform',
		'text'  => 'Opening Collective Finity to independent AI-assisted artists to publish and showcase their own work.',
	),
	array(
		'icon'  => 'infinity',
		'title' => 'Nova Xfinity',
		'text'  => 'Transforming Collective Finity into a complete streaming ecosystem with dedicated web and mobile applications built specifically for AI-assisted music creators.',
	),
);

$cf_about_faq = array(
	array(
		'question' => 'Is Collective Finity made by one person or a team?',
		'answer'   => "Right now, it's just me — Wael Safan. I write, produce, and publish everything myself. The name 'Collective' reflects where this is headed: a platform other AI-assisted artists can eventually join, not where it is today.",
	),
	array(
		'question' => 'Do you only make cinematic music?',
		'answer'   => "No — I produce across genres: electronic, classical, rock, metal, traditional Arabic vocal music, and more. 'Cinematic' describes the emotional, story-driven approach I bring to all of it, not a single genre.",
	),
	array(
		'question' => 'Are you a trained musician?',
		'answer'   => "No — I'm self-taught. I play piano, I've practiced guitar, and I've always had a good ear for music, but I have no formal training. That's part of why I care about getting AI-generated music right: I know what sounds real even without the theory to explain it.",
	),
	array(
		'question' => 'Will other artists be able to join Collective Finity?',
		'answer'   => "That's the plan. The long-term goal is a dedicated app where any artist using AI to make music can publish their own work and reach listeners. This site and its community come first.",
	),
	array(
		'question' => 'Do you offer courses or paid content?',
		'answer'   => "Not yet. Everything — the music and the articles — is free. Down the line, once there's a solid library of articles and tracks here, I plan to add affordable courses on AI music prompt engineering for people who want to go deeper.",
	),
);

/**
 * Inline SVG icon for About page cards / timeline.
 *
 * @param string $name Icon key.
 * @return string
 */
$cf_about_icon = static function ( $name ) {
	$icons = array(
		'music'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>',
		'book'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
		'people'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
		'brain'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5a3 3 0 1 0-5.997.125 4 4 0 0 0-2.526 5.77 4 4 0 0 0 .556 6.588A4 4 0 1 0 12 18Z"/><path d="M12 5a3 3 0 1 1 5.997.125 4 4 0 0 1 2.526 5.77 4 4 0 0 1-.556 6.588A4 4 0 1 1 12 18Z"/><path d="M15 13a4.5 4.5 0 0 1-3-4 4.5 4.5 0 0 1-3 4"/><path d="M12 18v4"/></svg>',
		'flask'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 3h6"/><path d="M10 9V3"/><path d="M14 9V3"/><path d="M9 9h6"/><path d="M6.5 21h11a1 1 0 0 0 .9-1.45L14 9H10L5.6 19.55A1 1 0 0 0 6.5 21z"/></svg>',
		'rocket'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="M12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>',
		'artist'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><circle cx="18" cy="8" r="2.5" stroke-dasharray="2 2"/></svg>',
		'infinity' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 12c-2-2.67-4-4-6-4a4 4 0 1 0 0 8c2 0 4-1.33 6-4Zm0 0c2 2.67 4 4 6 4a4 4 0 0 0 0-8c-2 0-4 1.33-6 4Z"/></svg>',
		'arrow'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7"/><path d="M8 7h9v9"/></svg>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
};

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-about-page">
	<div class="cf-page-container cf-about">
		<div class="cf-about__inner">

			<section class="cf-about-hero" aria-labelledby="cf-about-heading" style="--cf-about-hero-image: url('<?php echo esc_url( $cf_hero_image_url ); ?>');">
				<div class="cf-about-hero__border" aria-hidden="true"></div>
				<div class="cf-about-hero__center-glow" aria-hidden="true"></div>
				<div class="cf-about-hero__media" aria-hidden="true"></div>
				<div class="cf-about-hero__shade" aria-hidden="true"></div>
				<div class="cf-about-hero__copy">
					<p class="cf-about-eyebrow">ABOUT COLLECTIVE FINITY</p>
					<h1 id="cf-about-heading" class="cf-about-hero__title">
						More Than AI Music. A Vision for the Future of <span class="cf-about-hero__accent">Human Creativity.</span>
					</h1>
					<p class="cf-about-hero__tagline">Where imagination, technology, and music converge to create something meaningful.</p>
					<p class="cf-about-hero__lead">Collective Finity is an independent creative platform dedicated to exploring the future of music through the collaboration between human creativity and artificial intelligence. We create original AI-assisted music, publish in-depth educational content, and build a growing community for artists who believe technology should expand creativity—not replace it. This is more than a music website. It is the beginning of a creative ecosystem designed for the next generation of musicians, producers, and storytellers.</p>
					<div class="cf-about-hero__actions">
						<a class="cf-about-btn cf-about-btn--primary" href="<?php echo esc_url( $cf_tracks_url ); ?>">
							<span>Explore Music</span>
							<span class="cf-about-btn__icon" aria-hidden="true"><?php echo $cf_about_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</a>
						<a class="cf-about-btn cf-about-btn--ghost" href="<?php echo esc_url( $cf_community_url ); ?>">
							<span class="cf-about-btn__icon" aria-hidden="true"><?php echo $cf_about_icon( 'people' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span>Join Community</span>
						</a>
					</div>
				</div>
			</section>

			<section id="cf-about-why" class="cf-about-section cf-about-why" data-cf-about-reveal>
				<div class="cf-about-why__atmosphere" aria-hidden="true"></div>
				<div class="cf-about-why__grid">
					<div class="cf-about-why__copy">
						<p class="cf-about-section__label">01 / WHY WE EXIST</p>
						<h2 class="cf-about-section__title">Why Collective Finity Exists</h2>
						<div class="cf-about-section__body-stack">
							<p class="cf-about-section__body">Every day, thousands of AI-generated songs are created. Most disappear within hours.</p>
							<p class="cf-about-section__body">Not because the technology isn't powerful... But because creativity without direction quickly becomes noise.</p>
							<p class="cf-about-section__body">Collective Finity was created to challenge that idea.</p>
							<p class="cf-about-section__body">We believe AI should never replace artistic expression. Instead, it should become an instrument that helps artists create deeper stories, stronger emotions, and more meaningful music.</p>
							<p class="cf-about-section__body cf-about-section__body--accent">This platform exists to combine original music, real-world knowledge, and an open creative community into one destination.</p>
						</div>
					</div>
					<div class="cf-about-why__cards">
						<?php foreach ( $cf_about_why_cards as $card ) : ?>
							<article class="cf-about-feature-card">
								<span class="cf-about-feature-card__icon" aria-hidden="true"><?php echo $cf_about_icon( $card['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<div class="cf-about-feature-card__content">
									<h3 class="cf-about-feature-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
									<p class="cf-about-feature-card__text"><?php echo esc_html( $card['text'] ); ?></p>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</section>

			<section id="cf-about-founder" class="cf-about-section cf-about-founder" data-cf-about-reveal>
				<div class="cf-about-founder__grid">
					<div class="cf-about-founder__copy">
						<p class="cf-about-section__label">02 / MEET THE FOUNDER</p>
						<h2 class="cf-about-section__title">Meet the Founder</h2>
						<span class="cf-about-founder__title-bar" aria-hidden="true"></span>
						<div class="cf-about-section__body-stack">
							<p class="cf-about-section__body">Music has been part of my life long before artificial intelligence entered the creative world. My name is Wael Safan, and Collective Finity is the result of years of curiosity, experimentation, and thousands of hours spent exploring AI music generation.</p>
							<p class="cf-about-section__body">Through prompt engineering, production workflows, composition, and continuous experimentation, I discovered that technology alone doesn't create meaningful music. Human vision does.</p>
							<p class="cf-about-section__body">Every article, every track, and every resource published here reflects that philosophy. Rather than keeping that knowledge private, I chose to build a place where creators can learn faster, create better music, and grow together.</p>
						</div>
						<blockquote class="cf-about-quote">
							<span class="cf-about-quote__mark" aria-hidden="true">“</span>
							<p class="cf-about-quote__text">Artificial intelligence doesn't replace creativity. It expands what's possible for those willing to learn.</p>
							<footer class="cf-about-quote__attr">
								<span class="cf-about-quote__attr-icon" aria-hidden="true"><?php echo $cf_about_icon( 'music' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span>— Wael Safan</span>
							</footer>
						</blockquote>
					</div>
					<figure class="cf-about-founder__photo">
						<div class="cf-about-founder__photo-frame">
							<div class="cf-about-founder__photo-glow" aria-hidden="true"></div>
							<img
								src="<?php echo esc_url( $cf_founder_photo_url ); ?>"
								alt="Portrait of Wael Safan, founder of Collective Finity"
								width="280"
								height="280"
								loading="lazy"
								decoding="async"
							>
						</div>
						<figcaption class="cf-about-founder__caption">
							<span class="cf-about-founder__name">Wael Safan</span>
							<span class="cf-about-founder__role">Founder of Collective Finity</span>
						</figcaption>
					</figure>
				</div>
			</section>

			<section class="cf-about-pillars" aria-labelledby="cf-about-pillars-heading" data-cf-about-reveal>
				<header class="cf-about-section-head">
					<p class="cf-about-section__label">03 / OUR FOUNDATION</p>
					<h2 id="cf-about-pillars-heading" class="cf-about-section-head__title">Our Foundation</h2>
				</header>
				<div class="cf-about-pillars__grid">
					<?php foreach ( $cf_about_pillars as $pillar ) : ?>
						<article class="cf-about-card">
							<span class="cf-about-card__icon" aria-hidden="true"><?php echo $cf_about_icon( $pillar['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<h3 class="cf-about-card__title"><?php echo esc_html( $pillar['title'] ); ?></h3>
							<p class="cf-about-card__text"><?php echo esc_html( $pillar['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</section>

			<section class="cf-about-roadmap" aria-labelledby="cf-about-roadmap-heading" data-cf-about-reveal>
				<header class="cf-about-section-head">
					<p class="cf-about-section__label">04 / ROADMAP</p>
					<h2 id="cf-about-roadmap-heading" class="cf-about-section-head__title">Roadmap</h2>
				</header>
				<div class="cf-about-timeline">
					<div class="cf-about-timeline__glow" aria-hidden="true"></div>
					<ol class="cf-about-timeline__stops">
						<?php foreach ( $cf_about_roadmap as $index => $item ) : ?>
							<li class="cf-about-timeline__stop">
								<span class="cf-about-timeline__node" aria-hidden="true">
									<span class="cf-about-timeline__node-icon"><?php echo $cf_about_icon( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								</span>
								<span class="cf-about-timeline__num"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
								<h3 class="cf-about-timeline__title"><?php echo esc_html( $item['title'] ); ?></h3>
								<p class="cf-about-timeline__text"><?php echo esc_html( $item['text'] ); ?></p>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			</section>

			<section class="cf-about-closing" aria-labelledby="cf-about-faq-heading" data-cf-about-reveal>
				<div class="cf-about-closing__grid">
					<div class="cf-about-faq">
						<header class="cf-about-section-head cf-about-section-head--left">
							<p class="cf-about-section__label">05 / FAQ</p>
							<h2 id="cf-about-faq-heading" class="cf-about-section-head__title">Frequently Asked Questions</h2>
						</header>
						<div class="cf-about-faq__list" data-cf-about-faq>
							<?php foreach ( $cf_about_faq as $index => $item ) : ?>
								<div class="cf-about-faq__item<?php echo 0 === $index ? ' is-open' : ''; ?>">
									<button
										type="button"
										class="cf-about-faq__trigger"
										aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>"
										aria-controls="cf-about-faq-panel-<?php echo esc_attr( (string) $index ); ?>"
										id="cf-about-faq-trigger-<?php echo esc_attr( (string) $index ); ?>"
									>
										<span><?php echo esc_html( $item['question'] ); ?></span>
										<span class="cf-about-faq__chevron" aria-hidden="true">
											<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
										</span>
									</button>
									<div
										id="cf-about-faq-panel-<?php echo esc_attr( (string) $index ); ?>"
										class="cf-about-faq__panel"
										role="region"
										aria-labelledby="cf-about-faq-trigger-<?php echo esc_attr( (string) $index ); ?>"
										<?php echo 0 === $index ? '' : 'hidden'; ?>
									>
										<p><?php echo esc_html( $item['answer'] ); ?></p>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<aside class="cf-about-cta" aria-labelledby="cf-about-cta-heading" style="--cf-about-cta-image: url('<?php echo esc_url( $cf_cta_image_url ); ?>');">
						<div class="cf-about-cta__shade" aria-hidden="true"></div>
						<div class="cf-about-cta__content">
							<h2 id="cf-about-cta-heading" class="cf-about-cta__title">Join the Journey</h2>
							<p class="cf-about-cta__body">Collective Finity is only getting started. Whether you're here to discover cinematic music, learn AI music production, or become part of a growing creative community, we'd love to have you with us from the very beginning.</p>
							<div class="cf-about-cta__actions">
								<a class="cf-about-btn cf-about-btn--primary" href="<?php echo esc_url( $cf_tracks_url ); ?>">
									<span>Explore Music</span>
									<span class="cf-about-btn__icon" aria-hidden="true"><?php echo $cf_about_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								</a>
								<a class="cf-about-btn cf-about-btn--ghost" href="<?php echo esc_url( $cf_community_url ); ?>">
									<span class="cf-about-btn__icon" aria-hidden="true"><?php echo $cf_about_icon( 'people' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
									<span>Join Community</span>
								</a>
							</div>
						</div>
					</aside>
				</div>
			</section>

		</div>
	</div>
</main>

<style>
	.cf-about-page.cf-page-shell {
		padding: 2.5rem 5px 2rem;
		max-width: 100%;
		min-width: 0;
		box-sizing: border-box;
	}

	.cf-about-page .cf-page-container.cf-about {
		max-width: min(1100px, 100%);
		margin: 0 auto;
		min-width: 0;
	}

	.cf-about__inner {
		display: flex;
		flex-direction: column;
		gap: clamp(80px, 10vw, 104px);
		max-width: min(1100px, 100%);
		margin: 0 auto;
		min-width: 0;
	}

	@property --cf-about-border-angle {
		syntax: '<angle>';
		initial-value: 0deg;
		inherits: false;
	}

	.cf-about-hero {
		position: relative;
		overflow: hidden;
		min-height: clamp(480px, 62vw, 620px);
		border-radius: 18px;
		border: 1px solid rgba(255, 255, 255, 0.06);
		background: #0B0B0B;
		box-sizing: border-box;
		box-shadow: 0 24px 64px -36px rgba(0, 0, 0, 0.7);
	}

	.cf-about-hero__border {
		position: absolute;
		inset: 0;
		border-radius: inherit;
		padding: 1.5px;
		pointer-events: none;
		z-index: 4;
		background: conic-gradient(
			from var(--cf-about-border-angle),
			transparent 0%,
			transparent 72%,
			rgba(255, 183, 0, 0.05) 80%,
			rgba(255, 183, 0, 0.35) 86%,
			var(--primary-color, #FFB700) 90%,
			#FFD060 93%,
			rgba(255, 183, 0, 0.2) 96%,
			transparent 100%
		);
		-webkit-mask:
			linear-gradient(#fff 0 0) content-box,
			linear-gradient(#fff 0 0);
		-webkit-mask-composite: xor;
		mask-composite: exclude;
		animation: cfAboutBorderTravel 5.5s linear infinite;
		filter: drop-shadow(0 0 8px rgba(255, 183, 0, 0.28));
	}

	@keyframes cfAboutBorderTravel {
		to { --cf-about-border-angle: 360deg; }
	}

	.cf-about-hero__center-glow {
		position: absolute;
		inset: 14% 24%;
		z-index: 1;
		border-radius: 50%;
		background: radial-gradient(circle, rgba(255, 183, 0, 0.2) 0%, rgba(255, 183, 0, 0.07) 42%, transparent 70%);
		pointer-events: none;
		filter: blur(10px);
	}

	.cf-about-hero__media {
		position: absolute;
		inset: 0;
		z-index: 0;
		background-image: var(--cf-about-hero-image);
		background-size: cover;
		background-position: center right;
		background-repeat: no-repeat;
	}

	.cf-about-hero__shade {
		position: absolute;
		inset: 0;
		z-index: 1;
		background:
			linear-gradient(90deg, rgba(8, 8, 8, 0.94) 0%, rgba(8, 8, 8, 0.78) 42%, rgba(8, 8, 8, 0.35) 68%, rgba(8, 8, 8, 0.18) 100%),
			linear-gradient(180deg, rgba(8, 8, 8, 0.2) 0%, transparent 28%, rgba(8, 8, 8, 0.55) 100%);
		pointer-events: none;
	}

	.cf-about-hero__copy {
		position: relative;
		z-index: 3;
		display: flex;
		flex-direction: column;
		gap: 20px;
		max-width: 640px;
		padding: clamp(52px, 7vw, 84px) clamp(24px, 4.5vw, 56px) clamp(56px, 7vw, 88px);
	}

	.cf-about-eyebrow,
	.cf-about-section__label,
	.cf-about-timeline__num {
		margin: 0;
		font-family: 'Space Mono', monospace;
		color: var(--primary-color, #FFB700);
	}

	.cf-about-eyebrow,
	.cf-about-section__label {
		font-size: 11px;
		letter-spacing: 0.12em;
		text-transform: uppercase;
	}

	.cf-about-hero__title {
		margin: 0;
		font-size: clamp(28px, 4vw, 40px);
		font-weight: 700;
		color: #fff;
		line-height: 1.18;
		letter-spacing: -0.015em;
	}

	.cf-about-hero__accent {
		color: var(--primary-color, #FFB700);
	}

	.cf-about-hero__tagline {
		margin: 2px 0 0;
		font-size: 17px;
		font-weight: 600;
		color: #F0F0F0;
		line-height: 1.5;
	}

	.cf-about-hero__lead {
		margin: 2px 0 0;
		max-width: 580px;
		font-size: 14.5px;
		line-height: 1.85;
		color: #B3B3B3;
	}

	.cf-about-hero__actions,
	.cf-about-cta__actions {
		display: flex;
		flex-wrap: wrap;
		gap: 12px;
		margin-top: 10px;
	}

	.cf-about-btn {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		gap: 8px;
		padding: 12px 22px;
		border-radius: 10px;
		font-size: 13.5px;
		font-weight: 600;
		line-height: 1.2;
		text-decoration: none;
		white-space: nowrap;
		cursor: pointer;
		transition: background 0.22s ease, color 0.22s ease, border-color 0.22s ease, transform 0.22s ease, box-shadow 0.22s ease;
	}

	.cf-about-btn__icon {
		display: inline-flex;
		width: 16px;
		height: 16px;
		flex-shrink: 0;
	}

	.cf-about-btn__icon svg {
		width: 100%;
		height: 100%;
	}

	.cf-about-btn--primary {
		border: none;
		background: var(--primary-color, #FFB700);
		color: var(--secondary-color, #0D0D0D);
		font-weight: 700;
		box-shadow: 0 8px 20px -10px rgba(255, 183, 0, 0.45);
	}

	.cf-about-btn--primary:hover,
	.cf-about-btn--primary:focus-visible {
		background: #ffc633;
		color: var(--secondary-color, #0D0D0D);
		transform: translateY(-2px);
		box-shadow: 0 14px 28px -10px rgba(255, 183, 0, 0.5);
	}

	.cf-about-btn--ghost {
		border: 1px solid rgba(255, 255, 255, 0.45);
		background: transparent;
		color: #fff;
	}

	.cf-about-btn--ghost:hover,
	.cf-about-btn--ghost:focus-visible {
		background: rgba(255, 255, 255, 0.06);
		border-color: rgba(255, 255, 255, 0.8);
		color: #fff;
		transform: translateY(-2px);
	}

	.cf-about-section__label {
		margin-bottom: 12px;
	}

	.cf-about-section__title {
		margin: 0 0 22px;
		font-size: clamp(22px, 3vw, 28px);
		font-weight: 700;
		color: #fff;
		line-height: 1.2;
		letter-spacing: -0.01em;
	}

	.cf-about-section__body-stack {
		display: flex;
		flex-direction: column;
		gap: 18px;
	}

	.cf-about-section__body {
		margin: 0;
		font-size: 14.5px;
		line-height: 1.85;
		color: #B3B3B3;
	}

	.cf-about-section__body--accent {
		color: var(--primary-color, #FFB700);
		font-weight: 500;
	}

	.cf-about-why {
		position: relative;
		isolation: isolate;
		padding: clamp(8px, 1.5vw, 16px) 0;
	}

	.cf-about-why__atmosphere {
		position: absolute;
		inset: -8% -4%;
		z-index: 0;
		pointer-events: none;
		opacity: 0.045;
		background:
			radial-gradient(ellipse 55% 40% at 18% 30%, rgba(255, 183, 0, 0.9) 0%, transparent 70%),
			radial-gradient(ellipse 40% 35% at 82% 70%, rgba(255, 183, 0, 0.7) 0%, transparent 72%),
			url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='640' height='360' viewBox='0 0 640 360' fill='none'%3E%3Cpath d='M0 180c40-28 80 28 120 0s80-28 120 0 80 28 120 0 80-28 120 0 80 28 120 0' stroke='%23FFB700' stroke-width='1.2'/%3E%3Cpath d='M0 210c40-20 80 20 120 0s80-20 120 0 80 20 120 0 80-20 120 0 80 20 120 0' stroke='%23FFB700' stroke-width='1' opacity='0.7'/%3E%3Cpath d='M0 150c40-22 80 22 120 0s80-22 120 0 80 22 120 0 80-22 120 0 80 22 120 0' stroke='%23FFB700' stroke-width='1' opacity='0.55'/%3E%3Ccircle cx='96' cy='96' r='1.4' fill='%23FFB700'/%3E%3Ccircle cx='220' cy='64' r='1.1' fill='%23FFB700'/%3E%3Ccircle cx='360' cy='120' r='1.3' fill='%23FFB700'/%3E%3Ccircle cx='480' cy='72' r='1' fill='%23FFB700'/%3E%3Ccircle cx='560' cy='160' r='1.2' fill='%23FFB700'/%3E%3Ccircle cx='160' cy='260' r='1' fill='%23FFB700'/%3E%3Ccircle cx='420' cy='250' r='1.3' fill='%23FFB700'/%3E%3C/svg%3E") center / cover no-repeat;
	}

	.cf-about-why__grid {
		position: relative;
		z-index: 1;
	}

	.cf-about-why__grid,
	.cf-about-founder__grid,
	.cf-about-closing__grid {
		display: grid;
		gap: 40px;
		align-items: start;
	}

	.cf-about-why__cards {
		display: flex;
		flex-direction: column;
		gap: 16px;
	}

	.cf-about-feature-card {
		display: flex;
		align-items: flex-start;
		gap: 14px;
		padding: 18px 20px;
		border: 1px solid rgba(255, 255, 255, 0.06);
		border-radius: 12px;
		background: rgba(20, 20, 20, 0.92);
		box-shadow: 0 10px 28px -22px rgba(0, 0, 0, 0.8);
		transition: border-color 0.28s ease, box-shadow 0.28s ease, transform 0.28s ease, background 0.28s ease;
	}

	.cf-about-feature-card:hover {
		border-color: rgba(255, 183, 0, 0.24);
		background: rgba(22, 22, 22, 0.98);
		box-shadow: 0 0 32px rgba(255, 183, 0, 0.08), 0 18px 36px -20px rgba(0, 0, 0, 0.6);
		transform: translateY(-2px);
	}

	.cf-about-feature-card__icon,
	.cf-about-card__icon {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		flex-shrink: 0;
		width: 42px;
		height: 42px;
		border: 1px solid rgba(255, 183, 0, 0.5);
		border-radius: 10px;
		color: var(--primary-color, #FFB700);
		background: rgba(255, 183, 0, 0.04);
		box-shadow: inset 0 0 12px rgba(255, 183, 0, 0.06);
	}

	.cf-about-feature-card__icon svg,
	.cf-about-card__icon svg {
		width: 20px;
		height: 20px;
	}

	.cf-about-feature-card__content {
		min-width: 0;
		padding-top: 1px;
	}

	.cf-about-feature-card__title {
		margin: 0 0 6px;
		font-size: 14.5px;
		font-weight: 700;
		color: #fff;
	}

	.cf-about-feature-card__text {
		margin: 0;
		font-size: 13px;
		line-height: 1.7;
		color: #8A8A8A;
	}

	.cf-about-founder__title-bar {
		display: block;
		width: 42px;
		height: 3px;
		margin: -10px 0 22px;
		border-radius: 999px;
		background: var(--primary-color, #FFB700);
		box-shadow: 0 0 14px rgba(255, 183, 0, 0.42);
	}

	.cf-about-founder__photo {
		position: relative;
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 18px;
		margin: 0 auto;
		width: min(280px, 72vw);
		justify-self: center;
	}

	.cf-about-founder__photo-frame {
		position: relative;
		width: 100%;
	}

	.cf-about-founder__photo-glow {
		position: absolute;
		inset: -12%;
		z-index: 0;
		border-radius: 50%;
		background: radial-gradient(circle, rgba(255, 183, 0, 0.36) 0%, rgba(255, 183, 0, 0.12) 42%, transparent 72%);
		filter: blur(18px);
		pointer-events: none;
	}

	.cf-about-founder__photo img {
		position: relative;
		z-index: 1;
		display: block;
		width: 100%;
		aspect-ratio: 1;
		height: auto;
		border-radius: 50%;
		border: 1px solid rgba(255, 183, 0, 0.32);
		object-fit: cover;
		box-shadow:
			0 0 28px rgba(255, 183, 0, 0.24),
			0 0 56px rgba(255, 183, 0, 0.1),
			0 18px 36px -14px rgba(0, 0, 0, 0.55);
	}

	.cf-about-founder__caption {
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 5px;
		text-align: center;
	}

	.cf-about-founder__name {
		font-size: 15px;
		font-weight: 600;
		letter-spacing: 0.01em;
		color: rgba(240, 240, 240, 0.88);
		line-height: 1.3;
	}

	.cf-about-founder__role {
		font-size: 12.5px;
		font-weight: 500;
		letter-spacing: 0.02em;
		color: rgba(255, 183, 0, 0.78);
		line-height: 1.35;
	}

	.cf-about-quote {
		position: relative;
		margin: 28px 0 0;
		padding: 20px 20px 20px 22px;
		border-left: 3px solid var(--primary-color, #FFB700);
		border-radius: 0 12px 12px 0;
		background: rgba(255, 255, 255, 0.028);
		box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.02);
	}

	.cf-about-quote__mark {
		display: block;
		margin-bottom: 6px;
		font-family: Georgia, 'Times New Roman', serif;
		font-size: 42px;
		line-height: 0.7;
		color: var(--primary-color, #FFB700);
	}

	.cf-about-quote__text {
		margin: 0 0 14px;
		font-size: 15px;
		font-style: italic;
		line-height: 1.75;
		color: #E4E4E4;
	}

	.cf-about-quote__attr {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		font-family: 'Space Mono', monospace;
		font-size: 12px;
		letter-spacing: 0.04em;
		color: var(--primary-color, #FFB700);
	}

	.cf-about-quote__attr-icon {
		display: inline-flex;
		width: 14px;
		height: 14px;
	}

	.cf-about-quote__attr-icon svg {
		width: 100%;
		height: 100%;
	}

	.cf-about-section-head {
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 12px;
		margin-bottom: 36px;
		text-align: center;
	}

	.cf-about-section-head--left {
		align-items: flex-start;
		text-align: left;
		margin-bottom: 24px;
	}

	.cf-about-section-head .cf-about-section__label {
		margin-bottom: 0;
	}

	.cf-about-section-head__title {
		margin: 0;
		font-size: clamp(24px, 3vw, 30px);
		font-weight: 700;
		color: #fff;
		line-height: 1.2;
		letter-spacing: -0.01em;
	}

	.cf-about-pillars__grid {
		display: grid;
		grid-template-columns: repeat(3, minmax(0, 1fr));
		gap: 18px;
	}

	.cf-about-card {
		padding: 24px;
		border: 1px solid rgba(255, 255, 255, 0.06);
		border-radius: 12px;
		background: rgba(20, 20, 20, 0.96);
		box-shadow: 0 12px 30px -24px rgba(0, 0, 0, 0.85);
		transition: box-shadow 0.28s ease, border-color 0.28s ease, transform 0.28s ease, background 0.28s ease;
	}

	.cf-about-card:hover {
		border-color: rgba(255, 183, 0, 0.24);
		background: rgba(22, 22, 22, 0.98);
		box-shadow: 0 0 30px rgba(255, 183, 0, 0.08), 0 18px 36px -20px rgba(0, 0, 0, 0.55);
		transform: translateY(-3px);
	}

	.cf-about-card__icon {
		margin-bottom: 18px;
	}

	.cf-about-card__title {
		margin: 0 0 12px;
		font-size: 15px;
		font-weight: 700;
		color: #fff;
	}

	.cf-about-card__text {
		margin: 0;
		font-size: 13.5px;
		line-height: 1.8;
		color: #8A8A8A;
	}

	.cf-about-timeline {
		position: relative;
		padding: 16px 0 0;
	}

	.cf-about-timeline__glow {
		position: absolute;
		left: 8%;
		right: 8%;
		top: 30px;
		height: 30px;
		border-radius: 999px;
		background: radial-gradient(ellipse at center, rgba(255, 183, 0, 0.24) 0%, rgba(255, 183, 0, 0.07) 45%, transparent 75%);
		filter: blur(12px);
		pointer-events: none;
	}

	.cf-about-timeline__stops {
		position: relative;
		display: grid;
		grid-template-columns: repeat(6, minmax(0, 1fr));
		gap: 14px;
		margin: 0;
		padding: 0;
		list-style: none;
	}

	.cf-about-timeline__stops::before {
		content: '';
		position: absolute;
		left: 4%;
		right: 4%;
		top: 27px;
		height: 2px;
		background: linear-gradient(90deg, transparent, rgba(255, 183, 0, 0.5) 8%, rgba(255, 183, 0, 0.5) 92%, transparent);
		pointer-events: none;
	}

	.cf-about-timeline__stop {
		position: relative;
		display: flex;
		flex-direction: column;
		align-items: center;
		text-align: center;
		gap: 10px;
		min-width: 0;
	}

	.cf-about-timeline__node {
		position: relative;
		z-index: 1;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 54px;
		height: 54px;
		border-radius: 50%;
		border: 1px solid rgba(255, 183, 0, 0.5);
		background: #121212;
		color: var(--primary-color, #FFB700);
		box-shadow: 0 0 20px rgba(255, 183, 0, 0.16);
		transition: box-shadow 0.28s ease, border-color 0.28s ease, transform 0.28s ease;
	}

	.cf-about-timeline__stop:hover .cf-about-timeline__node {
		border-color: rgba(255, 183, 0, 0.72);
		box-shadow: 0 0 26px rgba(255, 183, 0, 0.24);
		transform: translateY(-2px);
	}

	.cf-about-timeline__node-icon {
		display: inline-flex;
		width: 22px;
		height: 22px;
	}

	.cf-about-timeline__node-icon svg {
		width: 100%;
		height: 100%;
	}

	.cf-about-timeline__num {
		margin-top: 2px;
		font-size: 12px;
		letter-spacing: 0.08em;
	}

	.cf-about-timeline__title {
		margin: 0;
		font-size: 13.5px;
		font-weight: 700;
		line-height: 1.4;
		color: #fff;
	}

	.cf-about-timeline__text {
		margin: 0;
		font-size: 12.5px;
		line-height: 1.7;
		color: #8A8A8A;
	}

	.cf-about-faq__list {
		display: flex;
		flex-direction: column;
		gap: 12px;
	}

	.cf-about-faq__item {
		overflow: hidden;
		border: 1px solid rgba(255, 255, 255, 0.06);
		border-radius: 12px;
		background: rgba(20, 20, 20, 0.96);
		transition: border-color 0.22s ease, box-shadow 0.22s ease;
	}

	.cf-about-faq__item.is-open {
		border-left: 3px solid var(--primary-color, #FFB700);
		box-shadow: 0 10px 28px -22px rgba(0, 0, 0, 0.75);
	}

	.cf-about-faq__trigger {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 14px;
		width: 100%;
		padding: 17px 20px;
		border: none;
		background: transparent;
		color: #fff;
		font-size: 14px;
		font-weight: 600;
		text-align: left;
		cursor: pointer;
		transition: background 0.2s ease;
	}

	.cf-about-faq__trigger:hover,
	.cf-about-faq__trigger:focus-visible {
		background: rgba(255, 255, 255, 0.03);
	}

	.cf-about-faq__chevron {
		display: flex;
		flex-shrink: 0;
		transition: transform 0.22s ease;
	}

	.cf-about-faq__item.is-open .cf-about-faq__chevron {
		transform: rotate(180deg);
	}

	.cf-about-faq__panel {
		padding: 0 20px 20px;
	}

	.cf-about-faq__panel p {
		margin: 0;
		font-size: 13.5px;
		line-height: 1.8;
		color: #B3B3B3;
	}

	.cf-about-cta {
		position: relative;
		overflow: hidden;
		min-height: 100%;
		border: 1px solid rgba(255, 255, 255, 0.07);
		border-radius: 14px;
		background-color: #0f0f0f;
		background-image: var(--cf-about-cta-image);
		background-size: cover;
		background-position: center;
		box-shadow: 0 18px 40px -28px rgba(0, 0, 0, 0.85);
	}

	.cf-about-cta__shade {
		position: absolute;
		inset: 0;
		background: linear-gradient(180deg, rgba(8, 8, 8, 0.55) 0%, rgba(8, 8, 8, 0.78) 55%, rgba(8, 8, 8, 0.88) 100%);
		pointer-events: none;
	}

	.cf-about-cta__content {
		position: relative;
		z-index: 1;
		display: flex;
		flex-direction: column;
		justify-content: center;
		gap: 18px;
		height: 100%;
		padding: clamp(32px, 4.5vw, 44px) clamp(24px, 3.5vw, 36px);
		text-align: center;
	}

	.cf-about-cta__title {
		margin: 0;
		font-size: clamp(22px, 2.5vw, 28px);
		font-weight: 700;
		color: #fff;
		line-height: 1.2;
		letter-spacing: -0.01em;
	}

	.cf-about-cta__body {
		margin: 0;
		font-size: 14.5px;
		line-height: 1.85;
		color: #D0D0D0;
	}

	.cf-about-cta__actions {
		justify-content: center;
		margin-top: 4px;
	}

	[data-cf-about-reveal] {
		opacity: 0;
		transform: translateY(16px);
		transition: opacity 0.45s ease, transform 0.45s ease;
		will-change: opacity, transform;
	}

	[data-cf-about-reveal].is-visible {
		opacity: 1;
		transform: none;
		will-change: auto;
	}

	body.page-template-page-about .cf-site-footer {
		position: relative;
		margin-top: 12px;
		border-top-color: rgba(255, 183, 0, 0.1);
		box-shadow: 0 -28px 48px -36px rgba(255, 183, 0, 0.07);
	}

	body.page-template-page-about .cf-site-footer::before {
		content: '';
		position: absolute;
		top: 0;
		left: 8%;
		right: 8%;
		height: 1px;
		background: linear-gradient(90deg, transparent, rgba(255, 183, 0, 0.22), transparent);
		pointer-events: none;
	}

	@media (min-width: 900px) {
		.cf-about-why__grid {
			grid-template-columns: minmax(0, 1.15fr) minmax(260px, 0.85fr);
			gap: 52px;
			align-items: center;
		}

		.cf-about-founder__grid {
			grid-template-columns: minmax(0, 1.2fr) minmax(240px, 0.8fr);
			gap: 52px;
			align-items: center;
		}

		.cf-about-founder__photo {
			justify-self: end;
			width: min(300px, 100%);
		}

		.cf-about-closing__grid {
			grid-template-columns: minmax(0, 1.15fr) minmax(280px, 0.85fr);
			gap: 32px;
			align-items: stretch;
		}
	}

	@media (max-width: 999px) {
		.cf-about-timeline__stops {
			grid-template-columns: repeat(3, minmax(0, 1fr));
			row-gap: 32px;
		}

		.cf-about-timeline__stops::before {
			display: none;
		}

		.cf-about-timeline__glow {
			display: none;
		}
	}

	@media (max-width: 767px) {
		.cf-about__inner {
			gap: 56px;
		}

		.cf-about-hero {
			min-height: 0;
		}

		.cf-about-hero__copy {
			gap: 16px;
			padding: clamp(40px, 8vw, 56px) clamp(20px, 5vw, 28px) clamp(44px, 8vw, 60px);
		}

		.cf-about-hero__media {
			background-position: center;
		}

		.cf-about-hero__shade {
			background:
				linear-gradient(180deg, rgba(8, 8, 8, 0.72) 0%, rgba(8, 8, 8, 0.82) 45%, rgba(8, 8, 8, 0.92) 100%),
				linear-gradient(90deg, rgba(8, 8, 8, 0.55), rgba(8, 8, 8, 0.3));
		}

		.cf-about-hero__tagline {
			font-size: 15px;
		}

		.cf-about-pillars__grid {
			grid-template-columns: 1fr;
		}

		.cf-about-timeline__stops {
			grid-template-columns: 1fr;
		}

		.cf-about-timeline__stop {
			align-items: flex-start;
			text-align: left;
			flex-direction: row;
			flex-wrap: wrap;
			gap: 10px 14px;
			padding: 16px 18px;
			border: 1px solid rgba(255, 255, 255, 0.06);
			border-radius: 12px;
			background: rgba(20, 20, 20, 0.96);
		}

		.cf-about-timeline__num,
		.cf-about-timeline__title,
		.cf-about-timeline__text {
			width: calc(100% - 68px);
		}

		.cf-about-timeline__num {
			margin-top: 0;
			order: 1;
			width: auto;
		}

		.cf-about-timeline__title {
			order: 2;
			width: calc(100% - 68px);
		}

		.cf-about-timeline__text {
			order: 3;
			width: 100%;
		}

		.cf-about-cta__actions,
		.cf-about-hero__actions {
			flex-direction: column;
			align-items: stretch;
		}

		.cf-about-btn {
			width: 100%;
		}
	}

	@media (prefers-reduced-motion: reduce) {
		.cf-about-hero__border {
			animation: none;
		}

		.cf-about-card:hover,
		.cf-about-feature-card:hover,
		.cf-about-btn--primary:hover,
		.cf-about-btn--ghost:hover,
		.cf-about-timeline__stop:hover .cf-about-timeline__node {
			transform: none;
		}

		[data-cf-about-reveal] {
			opacity: 1;
			transform: none;
			transition: none;
		}
	}
</style>

<script>
(function () {
	var root = document.querySelector('[data-cf-about-faq]');
	if (root) {
		root.addEventListener('click', function (event) {
			var trigger = event.target.closest('.cf-about-faq__trigger');
			if (!trigger || !root.contains(trigger)) {
				return;
			}

			var item = trigger.closest('.cf-about-faq__item');
			var panel = item ? item.querySelector('.cf-about-faq__panel') : null;
			if (!item || !panel) {
				return;
			}

			var isOpen = item.classList.contains('is-open');

			root.querySelectorAll('.cf-about-faq__item').forEach(function (faqItem) {
				faqItem.classList.remove('is-open');
				var faqTrigger = faqItem.querySelector('.cf-about-faq__trigger');
				var faqPanel = faqItem.querySelector('.cf-about-faq__panel');
				if (faqTrigger) {
					faqTrigger.setAttribute('aria-expanded', 'false');
				}
				if (faqPanel) {
					faqPanel.hidden = true;
				}
			});

			if (!isOpen) {
				item.classList.add('is-open');
				trigger.setAttribute('aria-expanded', 'true');
				panel.hidden = false;
			}
		});
	}

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var revealNodes = document.querySelectorAll('[data-cf-about-reveal]');
	if (!revealNodes.length) {
		return;
	}

	if (reduceMotion || !('IntersectionObserver' in window)) {
		revealNodes.forEach(function (node) {
			node.classList.add('is-visible');
		});
		return;
	}

	var observer = new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (!entry.isIntersecting) {
				return;
			}
			entry.target.classList.add('is-visible');
			observer.unobserve(entry.target);
		});
	}, {
		threshold: 0.14,
		rootMargin: '0px 0px -6% 0px'
	});

	revealNodes.forEach(function (node) {
		observer.observe(node);
	});
})();
</script>

<?php
get_footer();
