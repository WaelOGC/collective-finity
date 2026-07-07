<?php
/**
 * Template Name: About Page
 * Description: Theme template for the About page.
 */

$cf_tracks_url = get_post_type_archive_link( 'tracks' );
if ( ! $cf_tracks_url ) {
    $cf_tracks_url = home_url( '/tracks/' );
}

$cf_about_pillars = array(
    array(
        'title' => 'Cinematic Lyrics',
        'text'  => 'Every track begins with a pure human spark. Original lyrics written by hand, conceptual storytelling, and profound narratives that establish a deep emotional connection before a single note is synthesized.',
    ),
    array(
        'title' => 'Human Artistry',
        'text'  => 'Strict artistic direction and meticulous human composition guide advanced production instruments. Technology never drives our process; it executes our deep, multi-layered cinematic vision.',
    ),
    array(
        'title' => 'Sonic Innovation',
        'text'  => 'Building a limitless digital ecosystem where music and technology evolve together. We push boundaries to craft immersive soundscapes and unforgettable auditory journeys.',
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
                <p class="cf-about-eyebrow">ABOUT COLLECTIVE FINITY</p>
                <h1 id="cf-about-heading" class="cf-about-hero__title">About Collective Finity</h1>
                <p class="cf-about-hero__tagline">Where Human Artistry Meets AI Innovation</p>
                <p class="cf-about-hero__lead">Collective Finity is a cinematic music universe shaping the future of sound. By combining human emotional lyrics with advanced AI synthesis, we craft immersive sonic journeys.</p>
                <div class="cf-about-hero__actions">
                    <a class="cf-about-btn cf-about-btn--primary" href="<?php echo esc_url( $cf_tracks_url ); ?>">Explore Music</a>
                    <a class="cf-about-btn cf-about-btn--ghost" href="#cf-about-chronicle">Our Story</a>
                </div>
            </section>

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

            <section class="cf-about-pillars" aria-labelledby="cf-about-pillars-heading">
                <h2 id="cf-about-pillars-heading" class="cf-about-block-title">Core Pillars</h2>
                <div class="cf-about-pillars__grid">
                    <?php foreach ( $cf_about_pillars as $pillar ) : ?>
                        <article class="cf-about-card">
                            <h3 class="cf-about-card__title"><?php echo esc_html( $pillar['title'] ); ?></h3>
                            <p class="cf-about-card__text"><?php echo esc_html( $pillar['text'] ); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="cf-about-timeline" aria-labelledby="cf-about-timeline-heading">
                <h2 id="cf-about-timeline-heading" class="cf-about-block-title cf-about-block-title--timeline">Timeline</h2>
                <ol class="cf-about-timeline__list">
                    <?php foreach ( $cf_about_timeline as $milestone ) : ?>
                        <li class="cf-about-timeline__item">
                            <span class="cf-about-timeline__dot" aria-hidden="true"></span>
                            <div class="cf-about-timeline__content">
                                <p class="cf-about-timeline__label"><?php echo esc_html( $milestone['quarter'] ); ?> — <?php echo esc_html( $milestone['title'] ); ?></p>
                                <p class="cf-about-timeline__text"><?php echo esc_html( $milestone['text'] ); ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </section>

            <section class="cf-about-faq" aria-labelledby="cf-about-faq-heading">
                <h2 id="cf-about-faq-heading" class="cf-about-block-title">FAQ</h2>
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
        max-width: 820px;
    }

    .cf-about-hero {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .cf-about-eyebrow,
    .cf-about-section__label,
    .cf-about-timeline__label {
        margin: 0;
        font-family: 'Space Mono', monospace;
        color: var(--primary-color, #FFB700);
    }

    .cf-about-eyebrow {
        font-size: 11px;
        letter-spacing: 0.1em;
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

    .cf-about-section__label {
        margin-bottom: 8px;
        font-size: 12px;
    }

    .cf-about-section__title,
    .cf-about-block-title {
        margin: 0 0 12px;
        font-size: 19px;
        font-weight: 700;
        color: #fff;
    }

    .cf-about-block-title {
        margin-bottom: 16px;
    }

    .cf-about-block-title--timeline {
        margin-bottom: 20px;
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
    }

    .cf-about-card__title {
        margin: 0 0 8px;
        font-size: 14.5px;
        font-weight: 700;
        color: #fff;
    }

    .cf-about-card__text {
        margin: 0;
        font-size: 13px;
        line-height: 1.6;
        color: #7A7A7A;
    }

    .cf-about-timeline__list {
        display: flex;
        flex-direction: column;
        gap: 22px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .cf-about-timeline__item {
        display: flex;
        gap: 16px;
    }

    .cf-about-timeline__dot {
        width: 10px;
        height: 10px;
        min-width: 10px;
        margin-top: 6px;
        border-radius: 50%;
        background: var(--primary-color, #FFB700);
    }

    .cf-about-timeline__label {
        margin: 0 0 4px;
        font-size: 12px;
    }

    .cf-about-timeline__text {
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

        .cf-about-pillars__grid {
            grid-template-columns: 1fr;
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
