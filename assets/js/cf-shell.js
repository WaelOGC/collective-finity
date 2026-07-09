/**
 * Collective Finity — Shell interactions.
 * Single-source-of-truth collapse state for the left sidebar and the right/player sidebar,
 * persisted to localStorage (mirrors the design export's saveSharedState/loadSharedState).
 */
(function () {
    'use strict';

    var LS_SIDEBAR = 'cf_sidebar_collapsed';
    var LS_PLAYER = 'cf_player_collapsed';
    var root = document.documentElement;

    function readBool(key) {
        try {
            return window.localStorage.getItem(key) === '1';
        } catch (e) {
            return false;
        }
    }

    function writeBool(key, value) {
        try {
            window.localStorage.setItem(key, value ? '1' : '0');
        } catch (e) {}
    }

    function setSidebarCollapsed(collapsed) {
        root.classList.toggle('cf-sidebar-collapsed', collapsed);
        writeBool(LS_SIDEBAR, collapsed);
        var btn = document.getElementById('cf-sidebar-collapse-btn');
        if (btn) {
            btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        }
    }

    function setPlayerCollapsed(collapsed) {
        root.classList.toggle('cf-player-collapsed', collapsed);
        writeBool(LS_PLAYER, collapsed);
        var btn = document.getElementById('cf-player-collapse-btn');
        if (btn) {
            btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        }
    }

    /*
     * Portal the account dropdown (and its scrim) to a body-level container so it
     * escapes the right sidebar's overflow:auto clipping box and any ancestor
     * stacking context. Positioned with position:fixed + coordinates from the
     * account icon's bounding rect (see .cf-dropdown in cf-shell.css).
     */
    function ensureDropdownPortal() {
        var dd = document.getElementById('cf-account-dropdown');
        var scrim = document.getElementById('cf-dropdown-scrim');
        if (!dd) { return; }

        var portal = document.getElementById('cf-portal-root');
        if (!portal) {
            portal = document.createElement('div');
            portal.id = 'cf-portal-root';
            document.body.appendChild(portal);
        }

        if (dd.parentNode !== portal) { portal.appendChild(dd); }
        if (scrim && scrim.parentNode !== portal) { portal.appendChild(scrim); }
    }

    function isDropdownOpen() {
        var dd = document.getElementById('cf-account-dropdown');
        return !!(dd && dd.classList.contains('is-open'));
    }

    function positionDropdown() {
        var dd = document.getElementById('cf-account-dropdown');
        var btn = document.getElementById('cf-account-btn');
        if (!dd || !btn) { return; }

        var rect = btn.getBoundingClientRect();
        var gap = 8;
        var margin = 8;
        var width = dd.offsetWidth || 210;

        var left = rect.right - width;
        var maxLeft = window.innerWidth - width - margin;
        if (left > maxLeft) { left = maxLeft; }
        if (left < margin) { left = margin; }

        dd.style.right = 'auto';
        dd.style.left = Math.round(left) + 'px';
        dd.style.top = Math.round(rect.bottom + gap) + 'px';
    }

    function closeDropdown() {
        var dd = document.getElementById('cf-account-dropdown');
        var btn = document.getElementById('cf-account-btn');
        var scrim = document.getElementById('cf-dropdown-scrim');
        if (dd) { dd.classList.remove('is-open'); }
        if (btn) { btn.classList.remove('is-open'); btn.setAttribute('aria-expanded', 'false'); }
        if (scrim) { scrim.style.display = 'none'; }
    }

    function openDropdown() {
        var dd = document.getElementById('cf-account-dropdown');
        var btn = document.getElementById('cf-account-btn');
        var scrim = document.getElementById('cf-dropdown-scrim');
        if (scrim) { scrim.style.display = 'block'; }
        if (dd) { dd.classList.add('is-open'); }
        if (btn) { btn.classList.add('is-open'); btn.setAttribute('aria-expanded', 'true'); }
        positionDropdown();
    }

    function setMobileNav(open) {
        document.body.classList.toggle('cf-mobilenav-open', open);
    }

    function setSearchTriggersExpanded(expanded) {
        [].forEach.call(document.querySelectorAll('.cf-search-trigger'), function (btn) {
            btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        });
    }

    function isSearchOpen() {
        var overlay = document.getElementById('cf-search-overlay');
        return !!(overlay && overlay.classList.contains('is-open'));
    }

    function closeSearch() {
        var overlay = document.getElementById('cf-search-overlay');
        if (!overlay) { return; }

        overlay.classList.remove('is-open');
        overlay.setAttribute('hidden', '');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('cf-search-open');
        setSearchTriggersExpanded(false);
    }

    function openSearch() {
        var overlay = document.getElementById('cf-search-overlay');
        var input = document.getElementById('cf-search-input');
        if (!overlay) { return; }

        closeDropdown();
        setMobileNav(false);

        overlay.classList.add('is-open');
        overlay.removeAttribute('hidden');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('cf-search-open');
        setSearchTriggersExpanded(true);

        if (input) {
            window.setTimeout(function () {
                input.focus();
                input.select();
            }, 0);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        ensureDropdownPortal();

        window.addEventListener('resize', function () {
            if (isDropdownOpen()) { positionDropdown(); }
        });
        window.addEventListener('scroll', function () {
            if (isDropdownOpen()) { positionDropdown(); }
        }, true);

        var sidebarBtn = document.getElementById('cf-sidebar-collapse-btn');
        if (sidebarBtn) {
            sidebarBtn.addEventListener('click', function () {
                setSidebarCollapsed(!root.classList.contains('cf-sidebar-collapsed'));
            });
        }

        var playerBtn = document.getElementById('cf-player-collapse-btn');
        if (playerBtn) {
            playerBtn.addEventListener('click', function () {
                setPlayerCollapsed(!root.classList.contains('cf-player-collapsed'));
            });
        }

        var accountBtn = document.getElementById('cf-account-btn');
        if (accountBtn) {
            accountBtn.addEventListener('click', function (e) {
                e.preventDefault();
                var dd = document.getElementById('cf-account-dropdown');
                if (dd && dd.classList.contains('is-open')) {
                    closeDropdown();
                } else {
                    openDropdown();
                }
            });
        }

        var scrim = document.getElementById('cf-dropdown-scrim');
        if (scrim) { scrim.addEventListener('click', closeDropdown); }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (isSearchOpen()) {
                    closeSearch();
                    return;
                }
                closeDropdown();
                setMobileNav(false);
            }
        });

        [].forEach.call(document.querySelectorAll('.cf-search-trigger'), function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (isSearchOpen()) {
                    closeSearch();
                } else {
                    openSearch();
                }
            });
        });

        var searchCloseBtn = document.getElementById('cf-search-close-btn');
        if (searchCloseBtn) {
            searchCloseBtn.addEventListener('click', closeSearch);
        }

        var searchOverlay = document.getElementById('cf-search-overlay');
        if (searchOverlay) {
            searchOverlay.addEventListener('click', function (e) {
                if (e.target && e.target.hasAttribute('data-cf-search-close')) {
                    closeSearch();
                }
            });
        }

        var menuBtn = document.getElementById('cf-mobile-menu-btn');
        if (menuBtn) {
            menuBtn.addEventListener('click', function () { setMobileNav(true); });
        }
        var closeBtn = document.getElementById('cf-mobile-close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () { setMobileNav(false); });
        }
        var mobileScrim = document.getElementById('cf-mobile-scrim');
        if (mobileScrim) {
            mobileScrim.addEventListener('click', function () { setMobileNav(false); });
        }
        [].forEach.call(document.querySelectorAll('.cf-mobile-drawer .cf-nav-row'), function (link) {
            link.addEventListener('click', function () { setMobileNav(false); });
        });

        /* Home "From the Blog" horizontal rail arrows. */
        function bindBlogRailArrows() {
            [].forEach.call(document.querySelectorAll('[data-cf-rail-dir]'), function (btn) {
                if (btn.getAttribute('data-cf-rail-bound') === '1') {
                    return;
                }
                btn.setAttribute('data-cf-rail-bound', '1');
                btn.addEventListener('click', function () {
                    var rail = document.getElementById('cf-blog-rail');
                    if (!rail) { return; }
                    var dir = parseInt(btn.getAttribute('data-cf-rail-dir'), 10) || 1;
                    rail.scrollBy({ left: dir * 320, behavior: 'smooth' });
                });
            });
        }

        bindBlogRailArrows();
        document.addEventListener('cf:page-loaded', bindBlogRailArrows);
    });
}());
