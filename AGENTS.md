# Collective Finity

Custom WordPress theme (PHP + vanilla JS + CSS) for a cinematic music‑streaming site. It registers `tracks` and `albums` custom post types, taxonomies (`music_genre`, `track_artist`), a persistent audio player, music library, likes/favorites, playlists, blog, and legal/donation pages. There is no build tooling: JS/CSS assets under `assets/` and `js/` are hand‑written and enqueued directly (no `package.json`, `composer.json`, `Makefile`, or bundler).

## Cursor Cloud specific instructions

The repo itself is only the theme. A full WordPress stack (PHP 8.3, MariaDB, WordPress core, wp-cli) is baked into the VM snapshot, so the theme is developed inside a real WordPress install.

- **WordPress root:** `/var/www/html` (WordPress core lives here, outside the repo). The repo is symlinked in at `/var/www/html/wp-content/themes/collective-finity -> /workspace`, so editing files in `/workspace` immediately updates the running theme (no copy/build step).
- **Admin login:** user `admin` / password `admin` at `http://localhost:8080/wp-admin`. Site URL is `http://localhost:8080`.
- **DB:** MySQL database `wordpress`, user `wp` / password `wp` (host `127.0.0.1`). Data (including created content and permalink settings) persists in the snapshot.

### Starting services (not done by the update script)
MariaDB and the PHP dev server must be started manually each session:

```bash
# 1. Start MariaDB (data dir already initialized in the snapshot)
sudo mkdir -p /run/mysqld && sudo chown mysql:mysql /run/mysqld
sudo mariadbd --user=mysql >/tmp/mariadb.log 2>&1 &

# 2. Start the WordPress dev server (PHP built-in server) in tmux
php -S 0.0.0.0:8080 -t /var/www/html
```

Then browse `http://localhost:8080/`. Key routes: `/tracks/`, `/albums/`, `/tracks/<slug>/`, `/albums/<slug>/`.

### Lint / test / build
- **Lint:** no configured linter. Use PHP's syntax check: `find . -name '*.php' -not -path './design-reference/*' -exec php -l {} \;`.
- **Tests:** there is no automated test suite (no PHPUnit/composer/tests). Verify changes by browsing the running site.
- **Build:** none — assets are served as‑is.

### Gotchas
- Pretty permalinks (`/%postname%/`) are required for the `tracks`/`albums` routes; this is already configured in the DB (persists in the snapshot). If routes 404 after a WP reset, run `wp --path=/var/www/html rewrite structure '/%postname%/' --hard && wp --path=/var/www/html rewrite flush --hard`.
- The theme prints `CF DEBUG:` lines to output during page loads — this is pre‑existing theme code, not an error.
- Some features depend on external plugins that are **not** in this repo: user auth (`cf-auth-script`, `/cf-login`, `/cf-register`, `/cf-profile`) and donations (`[cf_donation_form]`). Core browsing/library/album/track/playback work without them.
- To populate the library, create `tracks`/`albums` posts (e.g. via `wp post create --path=/var/www/html --post_type=tracks ...`) and set meta like `track_audio_url`, `track_bpm`, `track_spotify_url`, `associated_album`.
