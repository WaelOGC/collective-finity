<?php
/**
 * Template Name: About Page
 * Description: Theme template for the About page.
 */

$cf_tracks_url = get_post_type_archive_link( 'tracks' );
if ( ! $cf_tracks_url ) {
    $cf_tracks_url = home_url( '/tracks/' );
}

$cf_hero_portrait_url = 'https://collectivefinity.com/wp-content/uploads/2026/07/Wael-Safan-%E2%80%94-Founder-of-Collective-Finity.jpg';
$cf_bts_image_url     = 'https://collectivefinity.com/wp-content/uploads/2026/07/Wael-Safan-behind-the-camera-Amsterdam.jpg';

$cf_about_pillars = array(
    array(
        'number' => '01',
        'title'  => 'Cinematic at Heart',
        'text'   => "'Cinematic' isn't a genre limit here — it's how I approach production. Whether it's electronic, classical, rock, or traditional Arabic vocal music, every track is built to feel like it's telling a story, not just filling space.",
        'accent' => false,
    ),
    array(
        'number' => '02',
        'title'  => 'A Real Musical Ear',
        'text'   => "I'm not a trained musician — but years of playing piano, messing with guitar, and building tracks by ear taught me what actually sounds right. Every track goes through real iteration until it holds up, not just the first AI output.",
        'accent' => true,
    ),
    array(
        'number' => '03',
        'title'  => 'Built in the Open',
        'text'   => "I write about the actual process — prompts, decisions, what worked and what didn't — so other people learning to generate music with AI can skip some of the trial and error I went through.",
        'accent' => false,
    ),
);

$cf_about_timeline = array(
    array(
        'quarter' => 'Q1 2025',
        'title'   => 'The Foundation',
        'text'    => 'Years of loving and playing music by ear, then years of hands-on experimentation with AI music generation — refining prompts and workflow long before Collective Finity had a name.',
    ),
    array(
        'quarter' => 'Q3 2025',
        'title'   => 'Collective Finity Takes Shape',
        'text'    => 'Built the catalog, the site, and started writing about the process — turning thousands of generated tracks into a curated, cinematic library.',
    ),
    array(
        'quarter' => 'Q1 2026',
        'title'   => 'Building the Platform',
        'text'    => 'The next phase: growing the music catalog and article library toward a real community, as the groundwork for an app open to other AI-assisted artists.',
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
                        <p class="cf-about-eyebrow">ABOUT COLLECTIVE FINITY</p>
                        <h1 id="cf-about-heading" class="cf-about-hero__title">About Collective Finity</h1>
                        <p class="cf-about-hero__tagline">One Musician, One Mission: Cinematic Sound Through AI</p>
                        <p class="cf-about-hero__lead">I'm Wael Safan — someone who's loved music my whole life, long before AI music generation existed, and one of the earliest people to take it seriously as a real creative tool. Collective Finity is where that comes together: a genuine ear for music, real hands-on instinct, and AI as an instrument I direct — not a shortcut.</p>
                        <div class="cf-about-hero__actions">
                            <a class="cf-about-btn cf-about-btn--primary" href="<?php echo esc_url( $cf_tracks_url ); ?>">Explore Music</a>
                            <a class="cf-about-btn cf-about-btn--ghost" href="#cf-about-who-i-am">Read My Story</a>
                        </div>
                    </div>

                    <div class="cf-about-hero__visual">
                        <div class="cf-about-hero__visual-frame">
                            <div class="cf-about-hero__portrait-wrap">
                                <div class="cf-about-hero__portrait-glow" aria-hidden="true"></div>
                                <img
                                    class="cf-about-hero__portrait"
                                    src="<?php echo esc_url( $cf_hero_portrait_url ); ?>"
                                    alt="Portrait of Wael Safan, musician and founder of Collective Finity"
                                    width="220"
                                    height="220"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="cf-about-story-grid">
                <section id="cf-about-who-i-am" class="cf-about-section">
                    <div class="cf-about-section__intro">
                        <div class="cf-about-section__intro-copy">
                            <p class="cf-about-section__label">01 / THE ARTIST</p>
                            <h2 class="cf-about-section__title">Who I Am</h2>
                        </div>
                        <figure class="cf-about-section__accent">
                            <img
                                src="<?php echo esc_url( $cf_bts_image_url ); ?>"
                                alt="Black and white photo of Wael Safan holding a camera"
                                width="140"
                                height="105"
                                loading="lazy"
                                decoding="async"
                            >
                        </figure>
                    </div>
                    <p class="cf-about-section__body">I've never been a professional or formally trained musician — but I've always had a genuine love for music and a good ear for it. I play piano, I've practiced guitar, and long before AI music generation existed, I was already building tracks myself with music software and a keyboard. That hands-on background — knowing what actually sounds right, even without formal training — is exactly what lets me get real results from AI music tools instead of just generating generic output. I've generated more than ten thousand pieces of music since I started. What you'll find on this site is the roughly 1,500 I consider genuinely worth sharing.</p>
                    <blockquote class="cf-about-quote">“AI doesn't replace the ear. It just needs someone who has one.”</blockquote>
                </section>

                <section id="cf-about-future" class="cf-about-section">
                    <p class="cf-about-section__label">02 / THE FUTURE</p>
                    <h2 class="cf-about-section__title">Where This Is Going</h2>
                    <p class="cf-about-section__body">Right now, Collective Finity is me: writing, producing, and publishing alone, plus a growing set of articles on how I actually get good results from AI music generation — real prompt techniques, not surface-level tips. Everything here is free, and stays free.</p>
                    <p class="cf-about-section__body">The bigger plan is a dedicated app — not just for my own music, but a place where any artist making AI-assisted music can publish theirs too, and listeners can discover it. This website, and the community forming around it, is the foundation that comes first.</p>
                    <blockquote class="cf-about-quote">“This site is the beginning. The platform is the plan.”</blockquote>
                </section>
            </div>

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

            <section class="cf-about-timeline" aria-labelledby="cf-about-timeline-heading">
                <header class="cf-about-section-head">
                    <p class="cf-about-section-head__eyebrow">THE CHRONOLOGY</p>
                    <h2 id="cf-about-timeline-heading" class="cf-about-section-head__title">The Journey So Far</h2>
                    <p class="cf-about-section-head__sub">Where This Started, Where It's Headed</p>
                </header>
                <div class="cf-about-timeline__track">
                    <div class="cf-about-timeline__line" aria-hidden="true"></div>
                    <ol class="cf-about-timeline__stops">
                        <?php foreach ( $cf_about_timeline as $milestone ) : ?>
                            <li class="cf-about-timeline__stop">
                                <span class="cf-about-timeline__dot" aria-hidden="true"></span>
                                <div class="cf-about-timeline__content">
                                    <p class="cf-about-milestone__quarter"><?php echo esc_html( $milestone['quarter'] ); ?></p>
                                    <h3 class="cf-about-milestone__title"><?php echo esc_html( $milestone['title'] ); ?></h3>
                                    <p class="cf-about-milestone__text"><?php echo esc_html( $milestone['text'] ); ?></p>
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

        </div>
    </div>
</main>

<style>
    .cf-about-page.cf-page-shell {
        padding: 2.5rem clamp(16px, 3vw, 20px) 140px;
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
    .cf-about-milestone__quarter {
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
        font-size: 36px;
        font-weight: 700;
        color: #fff;
        line-height: 1.15;
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
        width: min(100%, 240px);
        margin: 0 auto;
        padding: 8px 0 0;
    }

    .cf-about-hero__portrait-wrap {
        position: relative;
        width: min(100%, 220px);
        margin: 0 auto;
    }

    .cf-about-hero__portrait-glow {
        position: absolute;
        inset: 6%;
        z-index: 0;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 183, 0, 0.28) 0%, rgba(255, 183, 0, 0.1) 45%, transparent 72%);
        filter: blur(18px);
        pointer-events: none;
    }

    .cf-about-hero__portrait {
        position: relative;
        z-index: 1;
        display: block;
        width: 100%;
        max-width: 220px;
        aspect-ratio: 1;
        margin: 0 auto;
        border-radius: 50%;
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

    .cf-about-story-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 40px;
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

    .cf-about-section__accent {
        flex: 0 0 auto;
        margin: 0;
        width: min(100%, 120px);
        max-width: 140px;
    }

    .cf-about-section__accent img {
        display: block;
        width: 100%;
        height: auto;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 20px -10px rgba(0, 0, 0, 0.55);
        opacity: 0.92;
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

    .cf-about-section__body {
        margin: 0 0 16px;
        font-size: 14.5px;
        line-height: 1.7;
        color: #B3B3B3;
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

    .cf-about-timeline__track {
        position: relative;
        padding-top: 18px;
    }

    .cf-about-timeline__line {
        display: none;
        position: absolute;
        top: 23px;
        right: 10%;
        left: 10%;
        height: 1px;
        background: linear-gradient(90deg, transparent 0%, rgba(255, 183, 0, 0.28) 12%, rgba(255, 183, 0, 0.28) 88%, transparent 100%);
        box-shadow: 0 0 12px rgba(255, 183, 0, 0.12);
    }

    .cf-about-timeline__stops {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .cf-about-timeline__stop {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .cf-about-timeline__dot {
        position: relative;
        z-index: 1;
        width: 12px;
        height: 12px;
        margin-bottom: 16px;
        border-radius: 50%;
        background: var(--primary-color, #FFB700);
        box-shadow:
            0 0 0 4px rgba(255, 183, 0, 0.12),
            0 0 16px rgba(255, 183, 0, 0.38),
            0 0 28px rgba(255, 183, 0, 0.16);
    }

    .cf-about-milestone__quarter {
        margin: 0 0 6px;
        font-size: 12px;
        line-height: 1.4;
    }

    .cf-about-milestone__title {
        margin: 0 0 10px;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.35;
        color: #fff;
    }

    .cf-about-milestone__text {
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

    @media (min-width: 768px) {
        .cf-about-hero__grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(180px, 0.8fr);
            gap: 32px;
            align-items: center;
        }

        .cf-about-hero__visual {
            display: flex;
        }

        .cf-about-timeline__line {
            display: block;
        }
    }

    @media (min-width: 1024px) {
        .cf-about-story-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 40px;
            align-items: start;
        }
    }

    @media (max-width: 767px) {
        .cf-about__inner {
            gap: 40px;
        }

        .cf-about-hero__title {
            font-size: 26px;
        }

        .cf-about-hero__tagline {
            font-size: 15px;
        }

        .cf-about-hero__visual {
            display: flex;
        }

        .cf-about-pillars__grid,
        .cf-about-timeline__stops {
            grid-template-columns: 1fr;
        }

        .cf-about-timeline__stop {
            align-items: flex-start;
            text-align: left;
        }

        .cf-about-timeline__dot {
            margin-bottom: 12px;
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
