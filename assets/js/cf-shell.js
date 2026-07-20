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

    function positionDropdown(triggerBtn) {
        var dd = document.getElementById('cf-account-dropdown');
        var btn = triggerBtn || document.getElementById('cf-account-btn');
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
        var scrim = document.getElementById('cf-dropdown-scrim');
        if (dd) { dd.classList.remove('is-open'); }
        if (scrim && !isNotificationsOpen()) { scrim.style.display = 'none'; }
        ['cf-account-btn', 'cf-account-btn-mobile'].forEach(function (id) {
            var btn = document.getElementById(id);
            if (btn) { btn.classList.remove('is-open'); btn.setAttribute('aria-expanded', 'false'); }
        });
    }

    function ensureNotificationsPortal() {
        var panel = document.querySelector('.cf-notifications-panel');
        if (!panel) { return; }

        var portal = document.getElementById('cf-portal-root');
        if (!portal) {
            portal = document.createElement('div');
            portal.id = 'cf-portal-root';
            document.body.appendChild(portal);
        }

        if (panel.parentNode !== portal) { portal.appendChild(panel); }
    }

    function isNotificationsOpen() {
        var panel = document.querySelector('.cf-notifications-panel');
        return !!(panel && panel.classList.contains('is-open'));
    }

    function positionNotificationsPanel(triggerBtn) {
        var panel = document.querySelector('.cf-notifications-panel');
        var btn = triggerBtn || document.querySelector('[data-cf-notifications-toggle]');
        if (!panel || !btn) { return; }

        var rect = btn.getBoundingClientRect();
        var gap = 8;
        var margin = 8;
        var width = panel.offsetWidth || 320;

        var left = rect.right - width;
        var maxLeft = window.innerWidth - width - margin;
        if (left > maxLeft) { left = maxLeft; }
        if (left < margin) { left = margin; }

        panel.style.right = 'auto';
        panel.style.left = Math.round(left) + 'px';
        panel.style.top = Math.round(rect.bottom + gap) + 'px';
    }

    function closeNotificationsPanel() {
        var panel = document.querySelector('.cf-notifications-panel');
        var scrim = document.getElementById('cf-dropdown-scrim');
        var btn = document.querySelector('[data-cf-notifications-toggle]');
        if (panel) {
            panel.classList.remove('is-open');
        }
        if (scrim && !isDropdownOpen()) { scrim.style.display = 'none'; }
        if (btn) {
            btn.classList.remove('is-open');
            btn.setAttribute('aria-expanded', 'false');
        }
    }

    function openNotificationsPanel(triggerBtn) {
        var panel = document.querySelector('.cf-notifications-panel');
        var scrim = document.getElementById('cf-dropdown-scrim');
        var btn = triggerBtn || document.querySelector('[data-cf-notifications-toggle]');
        closeDropdown();
        if (scrim) { scrim.style.display = 'block'; }
        if (panel) {
            panel.classList.add('is-open');
        }
        if (btn) {
            btn.classList.add('is-open');
            btn.setAttribute('aria-expanded', 'true');
        }
        positionNotificationsPanel(btn);
    }

    function cfAjaxPost(action, extraData) {
        if (typeof cf_ajax === 'undefined' || !cf_ajax.ajax_url) {
            return Promise.reject(new Error('cf_ajax unavailable'));
        }

        var body = new URLSearchParams();
        body.append('action', action);
        body.append('security', cf_ajax.nonce);
        if (extraData) {
            Object.keys(extraData).forEach(function (key) {
                body.append(key, extraData[key]);
            });
        }

        return fetch(cf_ajax.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body.toString()
        }).then(function (res) { return res.json(); });
    }

    function updateNotificationBadge(count) {
        var badge = document.querySelector('.cf-notif-badge');
        if (!badge) { return; }

        var unread = parseInt(count, 10) || 0;
        if (unread > 0) {
            badge.textContent = unread > 99 ? '99+' : String(unread);
            badge.style.display = '';
        } else {
            badge.textContent = '0';
            badge.style.display = 'none';
        }
    }

    function getNotificationField(item, keys) {
        for (var i = 0; i < keys.length; i++) {
            if (item && item[keys[i]] != null && item[keys[i]] !== '') {
                return item[keys[i]];
            }
        }
        return '';
    }

    function renderNotificationsList(notifications) {
        var list = document.querySelector('.cf-notifications-list');
        var empty = document.querySelector('.cf-notifications-empty');
        if (!list) { return; }

        list.innerHTML = '';
        var items = Array.isArray(notifications) ? notifications : [];

        if (!items.length) {
            if (empty) { empty.style.display = ''; }
            return;
        }

        if (empty) { empty.style.display = 'none'; }

        items.forEach(function (item) {
            var title = getNotificationField(item, ['title']);
            var message = getNotificationField(item, ['message', 'body', 'content']);
            var time = getNotificationField(item, ['relative_time', 'time_ago', 'time', 'created_at']);
            var link = getNotificationField(item, ['link', 'url']);
            var isUnread = item && (item.read === false || item.is_read === false || item.unread === true);

            var el = document.createElement(link ? 'a' : 'button');
            el.className = 'cf-notif-item' + (isUnread ? ' cf-notif-item--unread' : '');
            if (!link) {
                el.type = 'button';
            } else {
                el.href = link;
            }

            if (title) {
                var titleEl = document.createElement('span');
                titleEl.className = 'cf-notif-item-title';
                titleEl.textContent = title;
                el.appendChild(titleEl);
            }

            if (message) {
                var messageEl = document.createElement('span');
                messageEl.className = 'cf-notif-item-message';
                messageEl.textContent = message;
                el.appendChild(messageEl);
            }

            if (time) {
                var timeEl = document.createElement('span');
                timeEl.className = 'cf-notif-item-time';
                timeEl.textContent = time;
                el.appendChild(timeEl);
            }

            el.addEventListener('click', function () {
                closeNotificationsPanel();
            });

            list.appendChild(el);
        });
    }

    function loadNotifications() {
        var list = document.querySelector('.cf-notifications-list');
        var empty = document.querySelector('.cf-notifications-empty');
        if (list) { list.innerHTML = ''; }
        if (empty) { empty.style.display = 'none'; }

        return cfAjaxPost('cf_get_notifications').then(function (response) {
            if (!response || !response.success || !response.data) {
                renderNotificationsList([]);
                updateNotificationBadge(0);
                return;
            }

            var data = response.data;
            var notifications = data.notifications || data.items || [];
            var unreadCount = data.unread_count != null ? data.unread_count : data.unreadCount;

            renderNotificationsList(notifications);
            if (unreadCount == null) {
                unreadCount = notifications.filter(function (item) {
                    return item && (item.read === false || item.is_read === false || item.unread === true);
                }).length;
            }
            updateNotificationBadge(unreadCount);
        }).catch(function () {
            renderNotificationsList([]);
            updateNotificationBadge(0);
        });
    }

    function markAllNotificationsRead() {
        return cfAjaxPost('cf_mark_notifications_read').then(function () {
            return loadNotifications();
        });
    }

    function openDropdown(triggerBtn) {
        var dd = document.getElementById('cf-account-dropdown');
        var scrim = document.getElementById('cf-dropdown-scrim');
        var btn = triggerBtn || document.getElementById('cf-account-btn');
        closeNotificationsPanel();
        if (scrim) { scrim.style.display = 'block'; }
        if (dd) { dd.classList.add('is-open'); }
        if (btn) { btn.classList.add('is-open'); btn.setAttribute('aria-expanded', 'true'); }
        positionDropdown(btn);
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
        ensureNotificationsPortal();

        window.addEventListener('resize', function () {
            if (isDropdownOpen()) { positionDropdown(); }
            if (isNotificationsOpen()) { positionNotificationsPanel(); }
        });
        window.addEventListener('scroll', function () {
            if (isDropdownOpen()) { positionDropdown(); }
            if (isNotificationsOpen()) { positionNotificationsPanel(); }
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

        var accountBtnMobile = document.getElementById('cf-account-btn-mobile');
        if (accountBtnMobile) {
            accountBtnMobile.addEventListener('click', function (e) {
                e.preventDefault();
                var dd = document.getElementById('cf-account-dropdown');
                if (dd && dd.classList.contains('is-open')) {
                    closeDropdown();
                } else {
                    openDropdown(accountBtnMobile);
                }
            });
        }

        var scrim = document.getElementById('cf-dropdown-scrim');
        if (scrim) {
            scrim.addEventListener('click', function () {
                closeDropdown();
                closeNotificationsPanel();
            });
        }

        var notificationsToggle = document.querySelector('[data-cf-notifications-toggle]');
        if (notificationsToggle) {
            notificationsToggle.addEventListener('click', function (e) {
                e.preventDefault();
                if (isNotificationsOpen()) {
                    closeNotificationsPanel();
                } else {
                    openNotificationsPanel(notificationsToggle);
                    loadNotifications();
                }
            });
        }

        var markAllReadBtn = document.querySelector('[data-cf-mark-all-read]');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                markAllNotificationsRead();
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (isSearchOpen()) {
                    closeSearch();
                    return;
                }
                closeDropdown();
                closeNotificationsPanel();
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
        [].forEach.call(document.querySelectorAll('.cf-dropdown-item'), function (link) {
            link.addEventListener('click', function () { closeDropdown(); });
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
        document.addEventListener('cf:page-loaded', function (e) {
            bindBlogRailArrows(e);
            closeDropdown();
            closeNotificationsPanel();
            ensureNotificationsPortal();
        });
    });
}());
