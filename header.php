<?php
/**
 * The Header for Collective Finity
 */
$cf_logo_url  = collective_finity_site_logo_url( 'thumbnail' );
$cf_site_name = collective_finity_brand_name();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?php echo esc_attr( $cf_site_name ); ?>">
    <link rel="apple-touch-icon" href="<?php echo esc_url( $cf_logo_url ); ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo esc_url( $cf_logo_url ); ?>">
    
    <?php wp_head(); ?>

    <style type="text/css">
        :root {
            --primary-color: #FFB700;
            --secondary-color: #0D0D0D;
            --darker-bg: #050505;
            --glass-blur: blur(15px);
            --glass-border: 1px solid rgba(255, 255, 255, 0.05);
            --neon-glow: 0 0 15px rgba(255, 183, 0, 0.4);
            --sidebar-width-collapsed: 70px;
            --sidebar-width-expanded: 280px;
        }

        body {
            background-color: var(--darker-bg) !important;
            padding-bottom: 100px !important;
            margin: 0;
            padding-left: var(--sidebar-width-collapsed) !important;
            transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.cf-sidebar-open {
            padding-left: var(--sidebar-width-expanded) !important;
        }

        /* ─── تنسيق الـ Header الثابت والمرن ─── */
        .cf-forced-header {
            background: rgba(13, 13, 13, 0.8) !important;
            backdrop-filter: var(--glass-blur) !important;
            -webkit-backdrop-filter: var(--glass-blur) !important;
            border-bottom: var(--glass-border) !important;
            padding: 0 24px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            position: sticky !important;
            top: 0;
            left: var(--sidebar-width-collapsed);
            right: 0;
            height: 70px;
            box-sizing: border-box;
            z-index: 999;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.cf-sidebar-open .cf-forced-header {
            left: var(--sidebar-width-expanded) !important;
        }

        #cf-main-app-content, .site-main, #content, .elementor-page {
            padding-top: 0 !important;
            padding-bottom: 140px !important;
            box-sizing: border-box;
        }

        .cf-forced-header a {
            color: #ffffff !important;
            text-decoration: none !important;
            font-family: 'Space Mono', sans-serif !important;
        }

        .cf-forced-nav ul {
            display: flex !important;
            align-items: center !important;
            gap: 24px !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* ─── هندسة وتصميم الـ Mini Sidebar الثابت والمتحرك ─── */
        #cf-music-sidebar {
            position: fixed !important;
            top: 0; left: 0; bottom: 85px;
            width: var(--sidebar-width-collapsed) !important;
            background: #060606 !important;
            border-right: var(--glass-border) !important;
            z-index: 10000 !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: flex !important;
            flex-direction: column !important;
        }

        body.cf-sidebar-open #cf-music-sidebar {
            width: var(--sidebar-width-expanded) !important;
            box-shadow: 10px 0 30px rgba(0,0,0,0.8) !important;
        }

        .cf-menu-text, .cf-sidebar-title-text {
            display: inline-block;
            white-space: nowrap;
            opacity: 0;
            transform: translateX(-10px);
            transition: opacity 0.2s ease, transform 0.2s ease;
            pointer-events: none;
        }

        body.cf-sidebar-open .cf-menu-text,
        body.cf-sidebar-open .cf-sidebar-title-text {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }

        .cf-sidebar-menu ul { list-style: none !important; padding: 0 !important; margin: 0 !important; }
        .cf-sidebar-menu li a {
            display: flex !important;
            align-items: center !important;
            padding: 15px 24px !important;
            color: #b3b3b3 !important;
            text-decoration: none !important;
            gap: 24px;
            height: 55px;
            box-sizing: border-box;
        }

        .cf-sidebar-menu li a:hover, .cf-sidebar-menu li.active a {
            color: #fff !important;
            background-color: rgba(255, 183, 0, 0.08) !important;
        }

        .cf-sidebar-menu li a .dashicons {
            font-size: 20px !important;
            width: 20px !important;
            height: 20px !important;
            color: var(--primary-color) !important;
        }

        .cf-sidebar-header {
            padding: 15px 22px !important;
            display: flex !important;
            align-items: center;
            height: 70px;
            box-sizing: border-box;
        }

        .cf-sidebar-brand {
            display: flex !important;
            align-items: center !important;
            padding: 10px 22px !important;
            gap: 15px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .cf-sidebar-logo { flex-shrink: 0 !important; border-radius: 6px; }

        /* ─── Global Music Player ─── */
        #cf-global-audio-player {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: 92px;
            background: linear-gradient(180deg, rgba(18, 18, 18, 0.82) 0%, rgba(8, 8, 8, 0.97) 100%);
            backdrop-filter: blur(22px) saturate(140%);
            -webkit-backdrop-filter: blur(22px) saturate(140%);
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 -8px 40px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 183, 0, 0.06);
            z-index: 10001;
            display: grid;
            grid-template-columns: minmax(200px, 1fr) minmax(280px, 2fr) minmax(160px, 1fr);
            align-items: center;
            gap: 16px;
            padding: 0 28px;
            box-sizing: border-box;
            direction: ltr;
        }
        .cf-player-track-info {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }
        .cf-player-cover {
            width: 56px;
            height: 56px;
            border-radius: 10px;
            background: #151515;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background-size: cover;
            background-position: center;
            flex-shrink: 0;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
        }
        .cf-player-meta {
            overflow: hidden;
            min-width: 0;
            text-align: left;
        }
        .cf-player-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cf-player-artist {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.45);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #player-queue-indicator {
            display: none;
            font-size: 0.68rem;
            color: var(--primary-color, #FFB700);
            margin-top: 4px;
            letter-spacing: 0.04em;
        }
        .cf-player-controls-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            width: 100%;
        }
        .cf-player-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .cf-p-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.55);
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s, background 0.2s, transform 0.15s;
            padding: 0;
        }
        .cf-p-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.06);
        }
        .cf-p-btn:active {
            transform: scale(0.94);
        }
        .cf-p-btn.cf-play-trigger {
            width: 44px;
            height: 44px;
            background: var(--primary-color, #FFB700);
            color: #0a0a0a;
            box-shadow: 0 0 20px rgba(255, 183, 0, 0.35);
        }
        .cf-p-btn.cf-play-trigger:hover {
            background: #ffc933;
            color: #000;
            box-shadow: 0 0 28px rgba(255, 183, 0, 0.5);
        }
        .cf-p-btn.cf-skip-btn {
            width: 38px;
            height: 38px;
        }
        .cf-p-btn.cf-skip-btn .cf-icon {
            width: 20px;
            height: 20px;
        }
        #player-speed-btn {
            min-width: 42px;
            height: 28px;
            border-radius: 14px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.7);
        }
        #player-speed-btn:hover {
            color: var(--primary-color, #FFB700);
            border-color: rgba(255, 183, 0, 0.3);
            background: rgba(255, 183, 0, 0.08);
        }
        #player-like-btn.is-active {
            color: var(--primary-color, #FFB700);
        }
        .cf-p-btn:disabled {
            opacity: 0.35;
            cursor: not-allowed;
            pointer-events: none;
        }
        .cf-icon-heart {
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z'/%3E%3C/svg%3E");
        }
        .cf-icon-playlist {
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M15 6H3v2h12V6zm0 4H3v2h12v-2zM3 16h8v-2H3v2zM17 6v8.18c-.31-.11-.65-.18-1-.18-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3V8h3V6h-5z'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M15 6H3v2h12V6zm0 4H3v2h12v-2zM3 16h8v-2H3v2zM17 6v8.18c-.31-.11-.65-.18-1-.18-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3V8h3V6h-5z'/%3E%3C/svg%3E");
        }
        .cf-player-progress-container {
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 560px;
            gap: 10px;
        }
        .cf-player-time {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.4);
            width: 38px;
            text-align: center;
            font-variant-numeric: tabular-nums;
        }
        .cf-player-progress-bar-bg {
            flex-grow: 1;
            height: 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 999px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .cf-player-progress-bar-bg:hover .cf-player-progress-fill {
            background: #ffc933;
        }
        .cf-player-progress-fill {
            height: 100%;
            width: 0%;
            background: var(--primary-color, #FFB700);
            border-radius: 999px;
            transition: width 0.1s linear;
            pointer-events: none;
        }
        .cf-player-utilities {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }
        .cf-volume-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cf-volume-slider-bg {
            width: 96px;
            height: 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 999px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .cf-volume-fill {
            height: 100%;
            width: 72%;
            background: var(--primary-color, #FFB700);
            border-radius: 999px;
            pointer-events: none;
        }
        #player-repeat-btn.is-active,
        #player-shuffle-btn.is-active {
            color: var(--primary-color, #FFB700);
        }
        #player-repeat-btn[data-mode="one"].is-active {
            color: var(--primary-color, #FFB700);
        }
        #player-volume-icon.is-muted {
            color: rgba(255, 255, 255, 0.3);
        }
        .cf-icon {
            display: block;
            width: 18px;
            height: 18px;
            background-color: currentColor;
            -webkit-mask-size: contain;
            mask-size: contain;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
            -webkit-mask-position: center;
            mask-position: center;
        }
        .cf-icon-play {
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M8 5v14l11-7z'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M8 5v14l11-7z'/%3E%3C/svg%3E");
        }
        .cf-icon-pause {
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M6 19h4V5H6v14zm8-14v14h4V5h-4z'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M6 19h4V5H6v14zm8-14v14h4V5h-4z'/%3E%3C/svg%3E");
        }
        .cf-icon-prev {
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M6 6h2v12H6V6zm3.5 6 8.5 6V6l-8.5 6z'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M6 6h2v12H6V6zm3.5 6 8.5 6V6l-8.5 6z'/%3E%3C/svg%3E");
        }
        .cf-icon-next {
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z'/%3E%3C/svg%3E");
        }
        .cf-icon-shuffle {
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M10.59 9.17 5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.46 20 9.5V4h-5.5zm.33 9.41-1.41 1.41 3.13 3.13L14.5 20H20v-5.5l-2.04 2.04-3.13-3.13z'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M10.59 9.17 5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.46 20 9.5V4h-5.5zm.33 9.41-1.41 1.41 3.13 3.13L14.5 20H20v-5.5l-2.04 2.04-3.13-3.13z'/%3E%3C/svg%3E");
        }
        .cf-icon-repeat {
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M7 7h8a4 4 0 0 1 4 4v1h2v-1a6 6 0 0 0-6-6H7v3l-4-3 4-3V7zm10 10H9a4 4 0 0 1-4-4v-1H3v1a6 6 0 0 0 6 6h8v-3l4 3-4 3v-3z'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M7 7h8a4 4 0 0 1 4 4v1h2v-1a6 6 0 0 0-6-6H7v3l-4-3 4-3V7zm10 10H9a4 4 0 0 1-4-4v-1H3v1a6 6 0 0 0 6 6h8v-3l4 3-4 3v-3z'/%3E%3C/svg%3E");
        }
        #player-repeat-btn {
            position: relative;
        }
        #player-repeat-btn[data-mode="one"]::after {
            content: '1';
            position: absolute;
            bottom: 2px;
            right: 2px;
            font-size: 8px;
            font-weight: 800;
            line-height: 1;
            color: var(--primary-color, #FFB700);
        }
        .cf-icon-volume {
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'%3E%3Cpath d='M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z'/%3E%3C/svg%3E");
        }
        @media (max-width: 900px) {
            #cf-global-audio-player {
                grid-template-columns: 1fr;
                height: auto;
                padding: 12px 16px 14px;
                gap: 10px;
            }
            .cf-player-track-info { justify-content: center; }
            .cf-player-meta { text-align: center; }
            .cf-player-utilities { justify-content: center; }
            .cf-volume-slider-bg { width: 120px; }
            #player-speed-btn { display: none; }
        }
        @media (min-width: 901px) and (max-width: 1100px) {
            #cf-global-audio-player {
                padding: 0 16px;
                gap: 12px;
            }
            .cf-volume-slider-bg { width: 72px; }
            .cf-player-progress-container { max-width: 420px; }
        }

        /* استعلامات الهواتف المحمولة لضمان بقاء المنيو والـ Auth معاً */
        @media (max-width: 768px) {
            body { padding-left: 0 !important; }
            #cf-music-sidebar { transform: translateX(-100%); width: var(--sidebar-width-expanded) !important; bottom: 85px; }
            body.cf-sidebar-open #cf-music-sidebar { transform: translateX(0) !important; }
            .cf-forced-header { left: 0 !important; height: 65px; padding: 0 15px !important; } 
            body.cf-sidebar-open .cf-forced-header { left: 0 !important; }
            
            /* تفعيل الهيدر كـ Flex حاوية على الجوال لعرض شعار المنيو وايقونة الدخول القادمة من البلوجن تلقائياً */
            .cf-forced-nav { 
                display: block !important; 
            }
            .cf-forced-nav ul {
                gap: 12px !important;
            }
            /* إخفاء روابط الصفحات النصية فقط على الجوال للإبقاء على زر الحساب الذكي والصورة */
            .cf-forced-nav ul li:not(.menu-item-has-children):not(:last-child) {
                display: none !important;
            }
            #cf-main-app-content, .site-main, #content, .elementor-page { padding-top: 0 !important; }
        }
    </style>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
collective_finity_render_theme_part(
    'header',
    function () {
        get_template_part( 'template-parts/header', 'default' );
    }
);
?>

<?php get_sidebar(); ?>

<div id="cf-global-audio-player">
    <audio id="cf-native-audio-element" preload="auto"></audio>
    <div class="cf-player-track-info">
        <div class="cf-player-cover" id="player-track-cover" style="background-image: url('<?php echo esc_url( $cf_logo_url ); ?>');"></div>
        <div class="cf-player-meta">
            <div class="cf-player-title" id="player-track-title"><?php esc_html_e( 'Select Track', 'collective-finity' ); ?></div>
            <div class="cf-player-artist" id="player-track-artist"><?php echo esc_html( $cf_site_name ); ?></div>
            <div id="player-queue-indicator"></div>
        </div>
    </div>
    <div class="cf-player-controls-wrapper">
        <div class="cf-player-buttons">
            <button type="button" class="cf-p-btn" id="player-shuffle-btn" title="<?php esc_attr_e( 'Shuffle', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Shuffle', 'collective-finity' ); ?>">
                <span class="cf-icon cf-icon-shuffle" aria-hidden="true"></span>
            </button>
            <button type="button" class="cf-p-btn cf-skip-btn" id="player-prev-btn" title="<?php esc_attr_e( 'Previous track', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Previous track', 'collective-finity' ); ?>">
                <span class="cf-icon cf-icon-prev" aria-hidden="true"></span>
            </button>
            <button type="button" class="cf-p-btn cf-play-trigger" id="player-toggle-btn" aria-label="<?php esc_attr_e( 'Play', 'collective-finity' ); ?>">
                <span class="cf-icon cf-icon-play" aria-hidden="true"></span>
            </button>
            <button type="button" class="cf-p-btn cf-skip-btn" id="player-next-btn" title="<?php esc_attr_e( 'Next track', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Next track', 'collective-finity' ); ?>">
                <span class="cf-icon cf-icon-next" aria-hidden="true"></span>
            </button>
            <button type="button" class="cf-p-btn" id="player-repeat-btn" title="<?php esc_attr_e( 'Repeat: off', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Repeat', 'collective-finity' ); ?>" data-mode="off">
                <span class="cf-icon cf-icon-repeat" aria-hidden="true"></span>
            </button>
        </div>
        <div class="cf-player-progress-container">
            <div class="cf-player-time" id="player-current-time">0:00</div>
            <div class="cf-player-progress-bar-bg" id="player-progress-bg">
                <div class="cf-player-progress-fill" id="player-progress-fill"></div>
            </div>
            <div class="cf-player-time" id="player-duration">0:00</div>
        </div>
    </div>
    <div class="cf-player-utilities">
        <button type="button" class="cf-p-btn" id="player-speed-btn" title="<?php esc_attr_e( 'Playback speed', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Playback speed', 'collective-finity' ); ?>">1×</button>
        <button type="button" class="cf-p-btn cf-like-btn" id="player-like-btn" disabled title="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Like', 'collective-finity' ); ?>">
            <span class="cf-icon cf-icon-heart" aria-hidden="true"></span>
        </button>
        <button type="button" class="cf-p-btn cf-playlist-btn" id="player-playlist-btn" disabled title="<?php esc_attr_e( 'Add to Playlist', 'collective-finity' ); ?>" aria-label="<?php esc_attr_e( 'Add to Playlist', 'collective-finity' ); ?>">
            <span class="cf-icon cf-icon-playlist" aria-hidden="true"></span>
        </button>
        <div class="cf-volume-wrapper">
            <button type="button" class="cf-p-btn" id="player-volume-icon" aria-label="<?php esc_attr_e( 'Mute', 'collective-finity' ); ?>">
                <span class="cf-icon cf-icon-volume" aria-hidden="true"></span>
            </button>
            <div class="cf-volume-slider-bg" id="player-volume-bg">
                <div class="cf-volume-fill" id="player-volume-fill"></div>
            </div>
        </div>
    </div>
</div>

