(function () {
    'use strict';

    var COOKIE_NAME = 'cf_cookie_consent';
    var COOKIE_DAYS = 180;
    var banner = null;

    function getConfig() {
        return window.cfCookieConsentConfig || {};
    }

    function readCookie(name) {
        var match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : null;
    }

    function writeCookie(name, value, days) {
        var expires = new Date();
        expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
        document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires.toUTCString() + '; path=/; SameSite=Lax';
    }

    function setConsent(value) {
        writeCookie(COOKIE_NAME, value, COOKIE_DAYS);
        window.cfCookieConsent = value;

        if (value === 'accepted') {
            activateAdGates();
        }

        hideBanner();
    }

    function hideBanner() {
        if (!banner) {
            return;
        }
        banner.classList.remove('is-visible');
        banner.setAttribute('aria-hidden', 'true');
    }

    function showBanner() {
        if (!banner) {
            banner = buildBanner();
            document.body.appendChild(banner);
        }

        banner.classList.add('is-visible');
        banner.setAttribute('aria-hidden', 'false');
    }

    function cloneScriptNode(oldScript) {
        var newScript = document.createElement('script');
        var i;
        var attr;

        for (i = 0; i < oldScript.attributes.length; i++) {
            attr = oldScript.attributes[i];
            newScript.setAttribute(attr.name, attr.value);
        }

        if (oldScript.textContent) {
            newScript.textContent = oldScript.textContent;
        }

        return newScript;
    }

    function injectGateContent(gate) {
        var container = document.createElement('div');
        var scripts;
        var i;

        container.innerHTML = gate.textContent;
        scripts = container.querySelectorAll('script');

        for (i = 0; i < scripts.length; i++) {
            scripts[i].parentNode.replaceChild(cloneScriptNode(scripts[i]), scripts[i]);
        }

        if (gate.parentNode) {
            gate.parentNode.replaceChild(container, gate);
        }
    }

    function activateAdGates() {
        var gates = document.querySelectorAll('.cf-ad-consent-gate');
        var i;

        for (i = 0; i < gates.length; i++) {
            injectGateContent(gates[i]);
        }
    }

    function buildBanner() {
        var config = getConfig();
        var policyUrl = config.cookiePolicyUrl || '/cookie-policy/';
        var root = document.createElement('div');
        root.className = 'cf-cookie-banner';
        root.setAttribute('role', 'dialog');
        root.setAttribute('aria-live', 'polite');
        root.setAttribute('aria-label', 'Cookie consent');
        root.setAttribute('aria-hidden', 'true');

        root.innerHTML =
            '<div class="cf-cookie-banner__inner">' +
                '<p class="cf-cookie-banner__text">We use cookies to improve your experience and to show relevant content. You can accept all cookies or allow only those necessary for the site to function.</p>' +
                '<div class="cf-cookie-banner__actions">' +
                    '<button type="button" class="cf-cookie-banner__btn cf-cookie-banner__btn--primary" data-cf-consent="accepted">Accept All</button>' +
                    '<button type="button" class="cf-cookie-banner__btn" data-cf-consent="necessary_only">Necessary Only</button>' +
                    '<a class="cf-cookie-banner__link" href="' + policyUrl + '">Cookie Policy</a>' +
                '</div>' +
            '</div>';

        root.addEventListener('click', function (event) {
            var target = event.target;
            var consent;

            if (!target || !target.getAttribute) {
                return;
            }

            consent = target.getAttribute('data-cf-consent');
            if (consent) {
                setConsent(consent);
            }
        });

        return root;
    }

    function bindSettingsTriggers() {
        document.addEventListener('click', function (event) {
            var target = event.target;

            if (!target || !target.closest) {
                return;
            }

            if (target.closest('[data-cf-cookie-settings]')) {
                event.preventDefault();
                showBanner();
            }
        });
    }

    function init() {
        var existing = readCookie(COOKIE_NAME);

        window.cfCookieConsent = existing === 'accepted' || existing === 'necessary_only' ? existing : null;

        if (window.cfCookieConsent === 'accepted') {
            activateAdGates();
        }

        bindSettingsTriggers();

        if (!window.cfCookieConsent) {
            showBanner();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
