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
$cf_founder_photo_url = 'https://collectivefinity.com/wp-content/uploads/2026/07/Wael-Safan-%E2%80%94-Founder-of-Collective-Finity.jpg';

$cf_about_pillars = array(
    array(
        'number' => '01',
        'title'  => 'Human Creativity First',
        'text'   => 'Artificial intelligence is our instrument—not our replacement. Every piece of music begins with imagination, emotion, and artistic direction before technology becomes part of the creative process.',
        'accent' => false,
    ),
    array(
        'number' => '02',
        'title'  => 'Learn Through Experience',
        'text'   => 'Everything published on Collective Finity comes from real experimentation. No recycled tutorials. No generic advice. Only practical knowledge gained through thousands of hours of testing, refining, and creating.',
        'accent' => true,
    ),
    array(
        'number' => '03',
        'title'  => 'Build Together',
        'text'   => 'Collective Finity is not meant to remain a personal project. It is the foundation of a future community where artists, producers, and creators can collaborate, learn, and inspire one another.',
        'accent' => false,
    ),
);

$cf_about_roadmap = array(
    array(
        'title' => 'Launch Collective Finity',
        'text'  => 'Building the first collection of cinematic AI-assisted music and educational content.',
    ),
    array(
        'title' => 'Growing the Library',
        'text'  => 'Continuously expanding the music catalog and publishing practical resources for AI music creators.',
    ),
    array(
        'title' => 'Building the Community',
        'text'  => 'Creating a collaborative environment where artists exchange knowledge, workflows, and inspiration.',
    ),
    array(
        'title' => 'Educational Resources',
        'text'  => 'Launching premium written courses, creator guides, and advanced learning materials.',
    ),
    array(
        'title' => 'Artist Platform',
        'text'  => 'Opening Collective Finity to independent AI-assisted artists to publish and showcase their own work.',
    ),
    array(
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

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-about-page">
    <div class="cf-page-container cf-about">
        <div class="cf-about__inner">

            <section class="cf-about-hero" aria-labelledby="cf-about-heading">
                <div class="cf-about-hero__grid">
                    <div class="cf-about-hero__copy">
                        <h1 id="cf-about-heading" class="cf-about-hero__title">More Than AI Music. A Vision for the Future of Human Creativity.</h1>
                        <p class="cf-about-hero__tagline">Where imagination, technology, and music converge to create something meaningful.</p>
                        <p class="cf-about-hero__lead">Collective Finity is an independent creative platform dedicated to exploring the future of music through the collaboration between human creativity and artificial intelligence. We create original AI-assisted music, publish in-depth educational content, and build a growing community for artists who believe technology should expand creativity—not replace it. This is more than a music website. It is the beginning of a creative ecosystem designed for the next generation of musicians, producers, and storytellers.</p>
                        <div class="cf-about-hero__actions">
                            <a class="cf-about-btn cf-about-btn--primary" href="<?php echo esc_url( $cf_tracks_url ); ?>">Explore Music</a>
                            <a class="cf-about-btn cf-about-btn--ghost" href="<?php echo esc_url( $cf_community_url ); ?>">Join Community</a>
                        </div>
                    </div>

                    <div class="cf-about-hero__visual">
                        <div class="cf-about-hero__visual-frame">
                            <div class="cf-about-hero__image-wrap">
                                <div class="cf-about-hero__image-glow" aria-hidden="true"></div>
                                <img
                                    class="cf-about-hero__image"
                                    src="<?php echo esc_url( $cf_hero_image_url ); ?>"
                                    alt=""
                                    width="640"
                                    height="400"
                                    loading="eager"
                                    decoding="async"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="cf-about-why" class="cf-about-section">
                <h2 class="cf-about-section__title">Why Collective Finity Exists</h2>
                <p class="cf-about-section__body">Every day, thousands of AI-generated songs are created. Most disappear within hours. Not because the technology isn't powerful... But because creativity without direction quickly becomes noise. Collective Finity was created to challenge that idea. We believe AI should never replace artistic expression. Instead, it should become an instrument that helps artists create deeper stories, stronger emotions, and more meaningful music. This platform exists to combine original music, real-world knowledge, and an open creative community into one destination.</p>
            </section>

            <section id="cf-about-founder" class="cf-about-section cf-about-founder">
                <div class="cf-about-section__intro">
                    <div class="cf-about-section__intro-copy">
                        <h2 class="cf-about-section__title">Meet the Founder</h2>
                    </div>
                    <figure class="cf-about-founder__photo">
                        <div class="cf-about-founder__photo-glow" aria-hidden="true"></div>
                        <img
                            src="<?php echo esc_url( $cf_founder_photo_url ); ?>"
                            alt="Portrait of Wael Safan, founder of Collective Finity"
                            width="160"
                            height="160"
                            loading="lazy"
                            decoding="async"
                        >
                    </figure>
                </div>
                <p class="cf-about-section__body">Music has been part of my life long before artificial intelligence entered the creative world. My name is Wael Safan, and Collective Finity is the result of years of curiosity, experimentation, and thousands of hours spent exploring AI music generation. Through prompt engineering, production workflows, composition, and continuous experimentation, I discovered that technology alone doesn't create meaningful music. Human vision does. Every article, every track, and every resource published here reflects that philosophy. Rather than keeping that knowledge private, I chose to build a place where creators can learn faster, create better music, and grow together.</p>
                <blockquote class="cf-about-quote">“Artificial intelligence doesn't replace creativity. It expands what's possible for those willing to learn.”</blockquote>
            </section>

            <section class="cf-about-pillars" aria-labelledby="cf-about-pillars-heading">
                <header class="cf-about-section-head">
                    <p class="cf-about-section-head__eyebrow">OUR FOUNDATION</p>
                    <h2 id="cf-about-pillars-heading" class="cf-about-section-head__title">Core Pillars</h2>
                    <p class="cf-about-section-head__sub">Music with Meaning</p>
                </header>
                <div class="cf-about-pillars__grid">
                    <?php foreach ( $cf_about_pillars as $pillar ) : ?>
                        <article class="cf-about-card<?php echo ! empty( $pillar['accent'] ) ? ' is-featured' : ''; ?>">
                            <p class="cf-about-card__number"><?php echo esc_html( $pillar['number'] ); ?></p>
                            <h3 class="cf-about-card__title"><?php echo esc_html( $pillar['title'] ); ?></h3>
                            <span class="cf-about-card__bar<?php echo ! empty( $pillar['accent'] ) ? ' is-accent' : ''; ?>" aria-hidden="true"></span>
                            <p class="cf-about-card__text"><?php echo esc_html( $pillar['text'] ); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="cf-about-roadmap" aria-labelledby="cf-about-roadmap-heading">
                <header class="cf-about-section-head">
                    <h2 id="cf-about-roadmap-heading" class="cf-about-section-head__title">Roadmap</h2>
                </header>
                <div class="cf-about-roadmap__year-wrap">
                    <p class="cf-about-roadmap__year">2026</p>
                    <ol class="cf-about-roadmap__list">
                        <?php foreach ( $cf_about_roadmap as $index => $item ) : ?>
                            <li class="cf-about-roadmap__item">
                                <span class="cf-about-roadmap__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
                                <div class="cf-about-roadmap__content">
                                    <h3 class="cf-about-roadmap__title"><?php echo esc_html( $item['title'] ); ?></h3>
                                    <p class="cf-about-roadmap__text"><?php echo esc_html( $item['text'] ); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </section>

            <section class="cf-about-faq" aria-labelledby="cf-about-faq-heading">
                <header class="cf-about-section-head">
                    <p class="cf-about-section-head__eyebrow">QUESTIONS</p>
                    <h2 id="cf-about-faq-heading" class="cf-about-section-head__title">Frequently Asked Questions</h2>
                    <p class="cf-about-section-head__sub">Everything You Need To Know</p>
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
            </section>

            <section class="cf-about-cta" aria-labelledby="cf-about-cta-heading">
                <header class="cf-about-section-head">
                    <h2 id="cf-about-cta-heading" class="cf-about-section-head__title">Join the Journey</h2>
                </header>
                <p class="cf-about-cta__body">Collective Finity is only getting started. Whether you're here to discover cinematic music, learn AI music production, or become part of a growing creative community, we'd love to have you with us from the very beginning.</p>
                <div class="cf-about-cta__actions">
                    <a class="cf-about-btn cf-about-btn--primary" href="<?php echo esc_url( $cf_tracks_url ); ?>">Explore Music</a>
                    <a class="cf-about-btn cf-about-btn--ghost" href="<?php echo esc_url( $cf_community_url ); ?>">Join Community</a>
                </div>
            </section>

        </div>
    </div>
</main>

<style>
    .cf-about-page.cf-page-shell {
        padding: 2.5rem 5px 5px;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    .cf-about-page .cf-page-container.cf-about {
        max-width: min(980px, 100%);
        margin: 0 auto;
        min-width: 0;
    }

    .cf-about__inner {
        display: flex;
        flex-direction: column;
        gap: 54px;
        max-width: min(920px, 100%);
        margin: 0 auto;
        min-width: 0;
    }

    .cf-about-hero__grid {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .cf-about-hero__copy {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .cf-about-eyebrow,
    .cf-about-section__label,
    .cf-about-section-head__eyebrow,
    .cf-about-roadmap__year,
    .cf-about-roadmap__num {
        margin: 0;
        font-family: 'Space Mono', monospace;
        color: var(--primary-color, #FFB700);
    }

    .cf-about-eyebrow,
    .cf-about-section-head__eyebrow {
        font-size: 11px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .cf-about-hero__title {
        margin: 0;
        font-size: 32px;
        font-weight: 700;
        color: #fff;
        line-height: 1.2;
    }

    .cf-about-hero__tagline {
        margin: 0;
        font-size: 17px;
        font-weight: 600;
        color: var(--primary-color, #FFB700);
    }

    .cf-about-hero__lead {
        margin: 0;
        max-width: 640px;
        font-size: 14.5px;
        line-height: 1.7;
        color: #B3B3B3;
    }

    .cf-about-hero__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 4px;
    }

    .cf-about-hero__visual {
        display: none;
        justify-content: center;
    }

    .cf-about-hero__visual-frame {
        position: relative;
        width: 100%;
        margin: 0 auto;
        padding: 8px 0 0;
    }

    .cf-about-hero__image-wrap {
        position: relative;
        width: 100%;
        margin: 0 auto;
    }

    .cf-about-hero__image-glow {
        position: absolute;
        inset: 8%;
        z-index: 0;
        border-radius: 18px;
        background: radial-gradient(circle, rgba(255, 183, 0, 0.28) 0%, rgba(255, 183, 0, 0.1) 45%, transparent 72%);
        filter: blur(18px);
        pointer-events: none;
    }

    .cf-about-hero__image {
        position: relative;
        z-index: 1;
        display: block;
        width: 100%;
        height: auto;
        border-radius: 14px;
        border: 1px solid rgba(255, 183, 0, 0.3);
        object-fit: cover;
        box-shadow:
            0 0 24px rgba(255, 183, 0, 0.22),
            0 0 48px rgba(255, 183, 0, 0.1),
            0 16px 32px -12px rgba(0, 0, 0, 0.55);
    }

    .cf-about-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 11px 20px;
        border-radius: 9px;
        font-size: 13.5px;
        font-weight: 600;
        line-height: 1.2;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    }

    .cf-about-btn--primary {
        border: none;
        background: var(--primary-color, #FFB700);
        color: var(--secondary-color, #0D0D0D);
        font-weight: 700;
    }

    .cf-about-btn--primary:hover,
    .cf-about-btn--primary:focus-visible {
        background: #ffc633;
        color: var(--secondary-color, #0D0D0D);
    }

    .cf-about-btn--ghost {
        border: 1px solid #262626;
        background: transparent;
        color: #fff;
    }

    .cf-about-btn--ghost:hover,
    .cf-about-btn--ghost:focus-visible {
        background: #161616;
        color: #fff;
    }

    .cf-about-section__intro {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 12px;
    }

    .cf-about-section__intro-copy {
        flex: 1 1 auto;
        min-width: 0;
    }

    .cf-about-founder__photo {
        position: relative;
        flex: 0 0 auto;
        margin: 0;
        width: 120px;
        max-width: 140px;
    }

    .cf-about-founder__photo-glow {
        position: absolute;
        inset: 6%;
        z-index: 0;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 183, 0, 0.28) 0%, rgba(255, 183, 0, 0.1) 45%, transparent 72%);
        filter: blur(14px);
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
        border: 1px solid rgba(255, 183, 0, 0.3);
        object-fit: cover;
        box-shadow:
            0 0 20px rgba(255, 183, 0, 0.2),
            0 8px 20px -10px rgba(0, 0, 0, 0.55);
    }

    .cf-about-section__label {
        margin-bottom: 8px;
        font-size: 12px;
    }

    .cf-about-section__title {
        margin: 0 0 12px;
        font-size: 19px;
        font-weight: 700;
        color: #fff;
    }

    .cf-about-section__intro .cf-about-section__title {
        margin-bottom: 0;
    }

    .cf-about-section__body {
        margin: 0 0 16px;
        font-size: 14.5px;
        line-height: 1.7;
        color: #B3B3B3;
    }

    .cf-about-section__body:last-child {
        margin-bottom: 0;
    }

    .cf-about-quote {
        margin: 0;
        padding-left: 18px;
        border-left: 3px solid var(--primary-color, #FFB700);
        font-size: 15px;
        font-style: italic;
        line-height: 1.6;
        color: #E4E4E4;
    }

    .cf-about-section-head {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        margin-bottom: 28px;
        text-align: center;
    }

    .cf-about-section-head__title {
        margin: 0;
        font-size: clamp(24px, 3vw, 30px);
        font-weight: 700;
        color: #fff;
        line-height: 1.2;
    }

    .cf-about-section-head__sub {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: var(--primary-color, #FFB700);
    }

    .cf-about-pillars__grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .cf-about-card {
        padding: 20px;
        border: 1px solid #232323;
        border-radius: 12px;
        background: #141414;
        transition: box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .cf-about-card:hover {
        border-color: rgba(255, 183, 0, 0.22);
        box-shadow: 0 0 28px rgba(255, 183, 0, 0.1), 0 16px 36px -18px rgba(0, 0, 0, 0.55);
    }

    .cf-about-card.is-featured {
        box-shadow: 0 0 22px rgba(255, 183, 0, 0.08);
    }

    .cf-about-card.is-featured:hover {
        box-shadow: 0 0 34px rgba(255, 183, 0, 0.14), 0 16px 36px -18px rgba(0, 0, 0, 0.55);
    }

    .cf-about-card__number {
        margin: 0 0 10px;
        color: #7A7A7A;
        font-family: 'Space Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.04em;
    }

    .cf-about-card__title {
        margin: 0 0 10px;
        font-size: 14.5px;
        font-weight: 700;
        color: #fff;
    }

    .cf-about-card__bar {
        display: block;
        width: 34px;
        height: 3px;
        margin-bottom: 14px;
        border-radius: 999px;
        background: #3a3a3a;
    }

    .cf-about-card__bar.is-accent {
        background: var(--primary-color, #FFB700);
        box-shadow: 0 0 14px rgba(255, 183, 0, 0.42);
    }

    .cf-about-card__text {
        margin: 0;
        font-size: 13px;
        line-height: 1.6;
        color: #7A7A7A;
    }

    .cf-about-roadmap__year-wrap {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .cf-about-roadmap__year {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        text-align: center;
    }

    .cf-about-roadmap__list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .cf-about-roadmap__item {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        padding: 18px 20px;
        border: 1px solid #232323;
        border-radius: 12px;
        background: #141414;
        transition: box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .cf-about-roadmap__item:hover {
        border-color: rgba(255, 183, 0, 0.22);
        box-shadow: 0 0 28px rgba(255, 183, 0, 0.1), 0 16px 36px -18px rgba(0, 0, 0, 0.55);
    }

    .cf-about-roadmap__num {
        flex-shrink: 0;
        font-size: 12px;
        letter-spacing: 0.04em;
        line-height: 1.4;
        padding-top: 2px;
    }

    .cf-about-roadmap__content {
        min-width: 0;
    }

    .cf-about-roadmap__title {
        margin: 0 0 8px;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.35;
        color: #fff;
    }

    .cf-about-roadmap__text {
        margin: 0;
        font-size: 13.5px;
        line-height: 1.6;
        color: #B3B3B3;
    }

    .cf-about-faq__list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .cf-about-faq__item {
        overflow: hidden;
        border: 1px solid #232323;
        border-radius: 10px;
        background: #141414;
    }

    .cf-about-faq__trigger {
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
        text-align: left;
        cursor: pointer;
    }

    .cf-about-faq__trigger:hover,
    .cf-about-faq__trigger:focus-visible {
        background: #181818;
    }

    .cf-about-faq__chevron {
        display: flex;
        flex-shrink: 0;
        transition: transform 0.15s ease;
    }

    .cf-about-faq__item.is-open .cf-about-faq__chevron {
        transform: rotate(180deg);
    }

    .cf-about-faq__panel {
        padding: 0 18px 18px;
    }

    .cf-about-faq__panel p {
        margin: 0;
        font-size: 13.5px;
        line-height: 1.6;
        color: #B3B3B3;
    }

    .cf-about-cta {
        text-align: center;
        padding: 36px 24px;
        border: 1px solid #232323;
        border-radius: 12px;
        background: #141414;
    }

    .cf-about-cta .cf-about-section-head {
        margin-bottom: 16px;
    }

    .cf-about-cta__body {
        margin: 0 auto 22px;
        max-width: 640px;
        font-size: 14.5px;
        line-height: 1.7;
        color: #B3B3B3;
    }

    .cf-about-cta__actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
    }

    @media (min-width: 768px) {
        .cf-about-hero__grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(220px, 0.85fr);
            gap: 32px;
            align-items: center;
        }

        .cf-about-hero__visual {
            display: flex;
        }

        .cf-about-founder__photo {
            width: 140px;
        }
    }

    @media (max-width: 767px) {
        .cf-about__inner {
            gap: 40px;
        }

        .cf-about-hero__title {
            font-size: 24px;
        }

        .cf-about-hero__tagline {
            font-size: 15px;
        }

        .cf-about-hero__visual {
            display: flex;
        }

        .cf-about-pillars__grid {
            grid-template-columns: 1fr;
        }

        .cf-about-section__intro {
            flex-direction: column-reverse;
            align-items: center;
            text-align: center;
        }

        .cf-about-founder__photo {
            width: 120px;
            margin-bottom: 8px;
        }
    }
</style>

<script>
(function () {
    var root = document.querySelector('[data-cf-about-faq]');
    if (!root) {
        return;
    }

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
})();
</script>

<?php
get_footer();
