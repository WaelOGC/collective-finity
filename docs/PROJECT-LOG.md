# Collective Finity Theme — Documentation Log

## Purpose
This file tracks every feature, fix, and pending item implemented in the collective-finity theme, so any developer (including future Cursor sessions) can understand the full history without re-reading all code.

## Completed Features

### Artist Taxonomy Pages (`track_artist`)
- Term meta fields on Tracks → Artists (Add/Edit): Artist Photo (media uploader), Artist Bio (max 150 chars + live counter + server-side trim), Instagram/Spotify/YouTube/TikTok/Facebook/X URLs, Linked WordPress User
- Professional Info term meta: Show Professional Info Section toggle (default on), Years Active, Location, Label/Crew, Genres (multi-select from `music_genre` term IDs)
- Saved via `track_artist_add_form_fields` / `track_artist_edit_form_fields` + `created_track_artist` / `edited_track_artist`
- Frontend template `taxonomy-track_artist.php` for `/artist/{slug}/`: fixed-height hero (420px desktop / 360px mobile) with wheel scroll-to-pan background, floating 3D sphere artist photo, bio + track/album stats + social icons, optional Professional Info & Stats section (info cards with gold hover, scrolling genre marquee, auto Total views / Avg likes / Most played from track meta), Tracks grid, Albums (5-per-row with `?album_page=` pagination), Blog Posts (4-per-row with `?blog_page=` pagination + gold card hover), all independent of tracks `?paged=`
- Admin media/counter/genre script: `js/admin-artist-term-meta.js`
- Helper: `collective_finity_get_artist_album_ids()`, `collective_finity_get_artist_term_for_user()`, `collective_finity_artist_bio_max_length()`, `collective_finity_render_artist_genre_field()`
- Artist name links wired on `single-tracks.php`, `single-albums.php`, `archive-tracks.php`, and blog author → artist on `single-post.php`

### FAQ & Platform Reviews
- FAQ page with platform review submission (`inc/faq.php`)
- AJAX handler `collective_finity_ajax_submit_platform_review()` creates a comment-based review and triggers a confirmation email via the cf-auth plugin (`CF_Email::send_feedback_confirmation`)
- On admin approval of a review comment, `collective_finity_notify_review_published()` (hooked to `comment_unapproved_to_approved`) triggers a second email via `CF_Email::send_review_published()` — users receive one email on submission and one when their review goes live

### User Dashboard Sidebar
- Right sidebar (`template-parts/sidebar-right-default.php`) includes account, Messages, and Notifications icons
- Notifications icon is active and connected to the plugin's polling-based notifications system
- Messages icon exists in code but remains visually hidden (not deleted) until the messaging feature is prioritized

### Notifications (Frontend)
- Sidebar notifications button + dropdown panel, polling-based (fetches on click via AJAX to the plugin's `cf_get_notifications` / `cf_mark_notifications_read` endpoints)
- JS: `assets/js/cf-shell.js` — panel open/close, fetch, mark-as-read
- CSS: `assets/css/cf-shell.css`

### Login/Register Page Design
- Removed gold radial-gradient glow from `.cf-auth-brand::before`
- Brand side shows the plugin logo (`icon-192.png`, ~64px) with welcoming copy
- Social login grid is dynamic: PHP counts enabled providers and applies a modifier class `cf-social-grid--{1|2|3|4}` with matching CSS grid layout rules
- Card hover glow override in `assets/css/cf-shell.css` excludes `.cf-auth-form-side .cf-card` from the site's global card hover effect

### Homepage Reviews Carousel
- `front-page.php`: auto-rotating carousel (3 cards visible at a time, rotates every 8 seconds)
- 4 entrance animations cycling: fade → slide-left → slide-right → scale
- "View All Reviews →" button links to `home_url('/reviews/')`
- Reuses the same card markup/classes as the All Reviews page for visual consistency

### All Reviews Page (`page-reviews.php`)
- Template Name: "All Reviews"
- Combines FAQ platform reviews and Blog article reviews into one filterable, paginated list (32 per page)
- Filters: rating (exact 1–5 or All) and type (faq / article / all) via `$_GET['rating']` and `$_GET['type']`
- Hero section (`.cf-reviews-hero`) matches the site's shared hero visual system (animated conic-gradient border + center radial glow, same pattern as `page-join-community.php`'s `.cf-community-hero`), without the dancer/equalizer decorations
- Rating and Type filter pill groups live inside the hero content, above the reviews grid
- Reuses `collective_finity_stars_markup()`, `collective_finity_review_avatar()`, and `.cf-home-review-card` styling from the homepage carousel

### User-Facing Notifications (Toast, replacing native `alert()`)
- `js/music-player.js`: all user-facing `alert()` calls (favorite-login prompt, generic errors) replaced with a single shared `showCfNotification(message)` function
- Renders a non-blocking toast (`#cf-player-toast`, `.cf-toast`) styled to match the site, auto-dismisses after ~4 seconds or via a close button
- Duplicated message strings consolidated into shared constants (`CF_NOTIFY_LOGIN`, `CF_NOTIFY_GENERIC_ERROR`) instead of being redefined per call site
- Toast CSS lives in `assets/css/cf-shell.css`

### Admin Options
- `inc/theme-options.php`: donate leadscreen message fields (`cf_leadscreen_msg_*`) have `maxlength="150"` (increased from the original 40-character limit)
- `footer_description` field remains at its own separate `maxlength="140"`, unchanged

### Track Metabox — Streaming Visibility & Lyrics Toggle
- **Streaming Links** section: per-platform Show checkbox (hide without deleting URL) plus Amazon Music and Google Play Music URL fields; visibility stored as `track_show_{platform}`, default enabled
- `single-tracks.php` only shows streaming icons when both URL is set and Show is enabled
- **Lyrics/Story visibility**: checkbox on the Lyrics File field (`track_show_lyrics`, default enabled); when off, the Story & Concept / Lyrics Narrative Sync block is not rendered on the single track page
- **Removed**: Visualizer Styles admin section and `collective_finity_track_visualizer_*` helpers (canvas visualizer retired from the track page)

### PageSpeed — Reduce Render-Blocking Requests (Homepage)
- Frontend-only: deregister/re-register core `jquery` + `jquery-migrate` to load in the footer (`collective_finity_scripts()` in `functions.php`); skips admin, admin-ajax, and wp-login
- Defer `cf-cookie-consent` CSS via `media="print"` + `onload="this.media='all'"` (`style_loader_tag` filter) with `<noscript>` fallback; critical CSS (`main-style`, `cf-shell`, `cf-content-layout`), dashicons, and Google Fonts unchanged
- `single-tracks.php` mid-page jQuery block waits for `window.jQuery` before `jQuery(fn)` so footer jQuery does not throw `jQuery is not defined`

### Single Track Page Overhaul (`single-tracks.php`)
- **Removed** canvas / Web Audio circular visualizer (frontend dropdown + JS draw loop + admin Visualizer Styles metabox + related PHP helpers)
- Cover art is now a CSS-only ambient “planet” sphere: fixed radial shade overlay + continuously sliding sheen highlight (~8s loop); image itself never spins/flips; old `.playing` spin removed
- Mood / BPM / Key / Release Date meta boxes restyled as compact horizontal pills (flex row, content-sized); external platform icons sit in the same row as a matching compact “LISTEN” pill
- External platform icons replaced with official Simple Icons SVG path data (Spotify, Apple Music, SoundCloud, YouTube, Bandcamp, Amazon Music, Google Play); fill still white / gold on hover via CSS
- Listener comments paginated at 5 per page via `?cf_comment_page=` (reuses profile `cf-pagination-*` classes; hidden when ≤1 page); emoji picker + comment form + Story/Lyrics blocks unchanged

### Single Article Sidebar (`single-post.php`)
- Split former "Latest Tracks" widget into **Latest Singles** (standalone `track_release_type=single` / missing meta only) and **Latest Albums** widgets; ad slots and empty-state `! empty()` checks unchanged
- Circular cover thumbnails reuse the exact cover fallback chains from `inc/cf-latest-releases-shortcode.php` (track: `track_cover_url` → featured → default art; album: featured → first associated track cover → default art)
- Popular Articles items show each post’s featured image thumbnail (gradient fallback) with slightly larger padding/gap
- `.cf-post-body` max-width ~800px so article text stays readable when both shell sidebars are collapsed
- Hero overlays: breadcrumb (top) + category/title/author/date/engagement (bottom, with readability gradient); single article top spacing `padding-top: 5px` so hero isn’t flush to the viewport
- Article sidebar widget column widened from 220px → 300px (desktop float)

### Shell Left Content Gutter (`assets/css/cf-shell.css`)
- Added `--cf-left-gutter: 10px`; body `padding-left` is `calc(sidebar-width + gutter)` in both expanded and collapsed left-sidebar states so page content is never flush against the sidebar edge (tablet/mobile unchanged — left sidebar hidden, padding zeroed)

### Music Library Hub (`archive-tracks.php`)
- Collections carousel retitled to **Albums** (same `albums` CPT query)
- Latest Tracks carousel restricted to standalone singles only (`track_release_type=single` or missing meta); album tracks excluded — same filter pattern as article sidebar Latest Singles
- Popular, ad slots, carousel behavior, and card rendering unchanged

### Albums Archive Genre Filter (`archive-albums.php`)
- Genre filter row uses real prev/next carousel-style nav buttons (hidden when no overflow / at that edge); removed right-only fade chevron hint
- Filter pills limited to `music_genre` terms assigned to at least one published album (`object_ids` from albums CPT); track-only / empty genres excluded
- Album grid, cards, and "All genres" pill behavior unchanged

### About Page Rebuild (`page-about.php`)
- Section order matches design mockup: Hero → Why Collective Finity Exists → Meet the Founder → Our Foundation → Roadmap → FAQ + Join the Journey (side-by-side)
- Hero is full-bleed background (`about-collective-finity-ai-music-vision.webp`) with animated conic border/glow, accent highlight on “Human Creativity.”, Explore Music + Join Community CTAs
- Why section: body copy + three icon feature cards (Meaningful Music / Real Knowledge / Creative Community)
- Founder: circular portrait with gold glow (photo only here) + quote block attributed to Wael Safan
- Our Foundation: three icon cards (Human Creativity First / Learn Through Experience / Build Together); keeps `#cf-about-pillars-heading` for homepage deep-link
- Roadmap: horizontal 6-stop timeline (01–06) with icons
- Closing row: FAQ accordion (same Q&A + behavior) beside Join the Journey card (`join-the-collective-journey.webp`)
- Copy is locked to the provided About brief; no invented wording

### Share Button (Tracks) + Share Tracking (Articles, Albums) — Theme Phase 1
- Article share (`single-post.php` `[data-cf-share]`): fire-and-forget `window.CF_Auth.trackShare(postId, 'post', 'native'|'copy')` when the helper exists; button has `data-post-id`
- Track page (`single-tracks.php`): new Share button in hero actions (native share / copy + "Link copied" label swap); tracks via `trackShare(trackId, 'track', …)` with `data-track-id`
- Album multi-platform panel (`template-parts/share-social.php` + `collective_finity_render_share_buttons()`): optional `$item_id` / `$item_type` params (backward compatible); panel gets `data-item-id` / `data-item-type`; clicks on Facebook/Twitter/LinkedIn/WhatsApp/copy fire `trackShare` with platform slug (no `preventDefault` on outbound links)
- Call site: `single-albums.php` passes album ID + `'album'`
- **Plugin phase pending**: `cf_track_share` AJAX + `window.CF_Auth.trackShare` helper in cf-auth — theme calls are guarded (`typeof` check) and no-op until then

## Known Pending Items
- Decision needed: number of reviews shown on the FAQ page (currently 4/6/8 candidates) and on the homepage carousel (3/6/9 candidates) — no final call made yet

## Future Features (Planned, Not Yet Built)
- **Share tracking backend (cf-auth plugin)**: `cf_track_share` AJAX endpoint + `window.CF_Auth.trackShare(itemId, itemType, platform)` helper — theme front-end already wired (phase 1); plugin implementation is the next phase
- **Real-time notifications** (WebSocket/push) — current system is polling-based; real-time planned for a later phase (6+ months out as of last discussion)
- **Messages system**: icon present in the sidebar markup but hidden/commented out until the admin-to-user messaging feature is built
- **Admin email broadcast dashboard**: a UI for composing and sending templated emails to users, to replace manual sending via the hosting provider's email panel

## Working Rules for This Project
- Theme and Plugin are separate repos, developed in separate Cursor windows
- Never reference Plugin files/paths inside Theme work, and vice versa
- Update this log whenever a feature is completed or a new one is planned