<?php
/**
 * Template Name: About Page
 * Description: Theme template for the About page.
 */

$cf_tracks_url = get_post_type_archive_link( 'tracks' );
if ( ! $cf_tracks_url ) {
    $cf_tracks_url = home_url( '/tracks/' );
}

$cf_hero_portal_url = 'https://collectivefinity.com/wp-content/uploads/2026/06/AI-Music-Creation-Portal-FF-CHRONICLE.png';

$cf_about_pillars = array(
    array(
        'number' => '01',
        'title'  => 'Cinematic Lyrics',
        'text'   => 'Every track begins with a pure human spark. Original lyrics written by hand, conceptual storytelling, and profound narratives that establish a deep emotional connection before a single note is synthesized.',
        'accent' => false,
    ),
    array(
        'number' => '02',
        'title'  => 'Human Artistry',
        'text'   => 'Strict artistic direction and meticulous human composition guide advanced production instruments. Technology never drives our process; it executes our deep, multi-layered cinematic vision.',
        'accent' => true,
    ),
    array(
        'number' => '03',
        'title'  => 'Sonic Innovation',
        'text'   => 'Building a limitless digital ecosystem where music and technology evolve together. We push boundaries to craft immersive soundscapes and unforgettable auditory journeys.',
        'accent' => false,
    ),
);

$cf_about_timeline = array(
    array(
        'quarter' => 'Q1 2025',
        'title'   => 'The Genesis',
        'text'    => 'The human spark is ignited. Collective Finity began as a conceptual movement to challenge standard digital music production, focusing entirely on original, handwritten lyrics and deep cinematic themes.',
    ),
    array(
        'quarter' => 'Q3 2025',
        'title'   => 'Synthesis Protocol',
        'text'    => 'Integrating advanced production instruments. We developed our proprietary creative methodology, proving that modern tech can amplify raw human artistry without destroying its emotional core.',
    ),
    array(
        'quarter' => 'Q1 2026',
        'title'   => 'The Infinite Expansion',
        'text'    => 'Launching a limitless digital universe where immersive orchestration, dark-themed visual design, and multi-layered soundscapes evolve continuously beyond imagination.',
    ),
);

$cf_about_faq = array(
    array(
        'question' => 'What makes Collective Finity different from other music platforms?',
        'answer'   => 'Unlike traditional automated platforms, Collective Finity bridges raw human artistry with advanced technology. Every project begins with handcrafted, original lyrics and structured cinematic concepts, ensuring that the emotional core of independent music remains entirely authentic and deeply impactful.',
    ),
    array(
        'question' => 'Are the lyrics and musical themes fully original?',
        'answer'   => 'Yes, completely. We follow a strict creative protocol where all lyrical narratives, poetic structures, and thematic concepts are generated directly from genuine human inspiration. Technology is utilized strictly as an advanced production tool to execute and scale this cinematic vision.',
    ),
    array(
        'question' => 'How does this platform support independent digital artists?',
        'answer'   => 'We provide an immersive, limitless ecosystem tailored for independent audio creators. By offering high-tier multi-layered soundscapes, persistent web players, and sophisticated visual frameworks, we empower artists to showcase their work within a premium, high-fidelity environment.',
    ),
    array(
        'question' => 'Can I integrate these cinematic tracks into my own external projects?',
        'answer'   => 'Absolutely. Our digital compositions and orchestration parameters are designed to seamlessly integrate with modern technical agencies, web architectures, and multimedia productions looking for elite, atmospheric, and continuous sound design.',
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
                        <p class="cf-about-hero__tagline">Where Human Artistry Meets AI Innovation</p>
                        <p class="cf-about-hero__lead">Collective Finity is a cinematic music universe shaping the future of sound. By combining human emotional lyrics with advanced AI synthesis, we craft immersive sonic journeys.</p>
                        <div class="cf-about-hero__actions">
                            <a class="cf-about-btn cf-about-btn--primary" href="<?php echo esc_url( $cf_tracks_url ); ?>">Explore Music</a>
                            <a class="cf-about-btn cf-about-btn--ghost" href="#cf-about-chronicle">Our Story</a>
                        </div>
                    </div>

                    <div class="cf-about-hero__visual" aria-hidden="true">
                        <div class="cf-about-hero__visual-frame">
                            <div class="cf-about-hero__disc-wrap">
                                <div class="cf-about-hero__glow"></div>
                                <div class="cf-about-hero__disc">
                                    <img
                                        class="cf-about-hero__portal"
                                        src="<?php echo esc_url( $cf_hero_portal_url ); ?>"
                                        alt=""
                                        width="280"
                                        height="280"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>
                            </div>
                            <span class="cf-about-hero__badge cf-about-hero__badge--sound">Original Sound</span>
                            <span class="cf-about-hero__badge cf-about-hero__badge--stories">Cinematic Stories</span>
                        </div>
                    </div>
                </div>
            </section>

            <div class="cf-about-story-grid">
                <section id="cf-about-chronicle" class="cf-about-section">
                    <p class="cf-about-section__label">01 / THE CHRONICLE</p>
                    <h2 class="cf-about-section__title">The Story Behind The Sound</h2>
                    <p class="cf-about-section__body">Collective Finity was born from a desire to redefine cinematic music. Every creation begins with a pure human spark—original lyrics written by hand, conceptual themes, and strict artistic direction.</p>
                    <blockquote class="cf-about-quote">“We guide advanced AI technology not as a simple shortcut, but as a complex production instrument to execute deep, multi-layered soundscapes.”</blockquote>
                </section>

                <section id="cf-about-future" class="cf-about-section">
                    <p class="cf-about-section__label">02 / THE FUTURE</p>
                    <h2 class="cf-about-section__title">Our Creative Vision</h2>
                    <p class="cf-about-section__body">Our vision is to build a limitless digital ecosystem where music and technology evolve together. By combining meticulous human composition with AI synthesis, we prove that innovation has no boundaries.</p>
                    <blockquote class="cf-about-quote">“Every track is a curated balance of professional musical direction and modern technology.”</blockquote>
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
                    <h2 id="cf-about-timeline-heading" class="cf-about-section-head__title">The Universe &amp; Timeline</h2>
                    <p class="cf-about-section-head__sub">Our Evolutionary Journey</p>
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
        padding: 90px 20px 140px;
    }

    .cf-about-page .cf-page-container.cf-about {
        max-width: 980px;
        margin: 0 auto;
    }

    .cf-about__inner {
        display: flex;
        flex-direction: column;
        gap: 54px;
        max-width: 920px;
        margin: 0 auto;
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
        width: min(100%, 340px);
        margin: 0 auto;
        padding: 16px 0 32px;
    }

    .cf-about-hero__disc-wrap {
        position: relative;
        width: min(100%, 300px);
        margin: 0 auto;
    }

    .cf-about-hero__glow {
        position: absolute;
        inset: 8%;
        z-index: 0;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 183, 0, 0.24) 0%, rgba(255, 183, 0, 0.08) 45%, transparent 72%);
        filter: blur(20px);
        pointer-events: none;
    }

    .cf-about-hero__disc {
        position: relative;
        z-index: 1;
        width: 100%;
        aspect-ratio: 1;
        margin: 0 auto;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 24px 48px -16px rgba(0, 0, 0, 0.65);
    }

    .cf-about-hero__portal {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        animation: cf-about-disc-spin 25s linear infinite;
        transform-origin: center center;
    }

    @keyframes cf-about-disc-spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .cf-about-hero__portal {
            animation: none;
        }
    }

    .cf-about-hero__badge {
        position: absolute;
        z-index: 2;
        padding: 8px 14px;
        border: 1px solid #262626;
        border-radius: 999px;
        background: rgba(20, 20, 20, 0.94);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
        box-shadow: 0 12px 28px -10px rgba(0, 0, 0, 0.55);
    }

    .cf-about-hero__badge--sound {
        top: 4%;
        right: 0;
    }

    .cf-about-hero__badge--stories {
        bottom: 8%;
        left: 0;
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
            grid-template-columns: minmax(0, 1.05fr) minmax(240px, 0.95fr);
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
