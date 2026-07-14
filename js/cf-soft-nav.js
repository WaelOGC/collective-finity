/**
 * Collective Finity — soft navigation (SPA-style content swap).
 * Keeps the persistent audio player and shell sidebars intact across page transitions.
 */
(function () {
    'use strict';

    var CONTENT_SELECTOR = '#cf-app-content';
    var loading = false;
    var abortController = null;

    function getContainer() {
        return document.querySelector(CONTENT_SELECTOR);
    }

    function isInternalLink(link) {
        if (!link || !link.href) {
            return false;
        }

        if (link.hasAttribute('download') || link.classList.contains('no-ajax')) {
            return false;
        }

        var target = (link.getAttribute('target') || '').toLowerCase();
        if (target && target !== '_self') {
            return false;
        }

        var href = link.getAttribute('href') || '';
        if (!href || href.charAt(0) === '#') {
            return false;
        }

        if (/^(mailto:|tel:|javascript:)/i.test(href)) {
            return false;
        }

        try {
            var url = new URL(link.href, window.location.origin);
            if (url.origin !== window.location.origin) {
                return false;
            }
            if (url.pathname.indexOf('/wp-admin') !== -1 || url.pathname.indexOf('wp-login') !== -1) {
                return false;
            }
            if (url.search.indexOf('action=logout') !== -1) {
                return false;
            }
            if (link.closest('.wp-admin, #wpadminbar')) {
                return false;
            }
        } catch (e) {
            return false;
        }

        return true;
    }

    function executeScripts(container) {
        var scripts = container.querySelectorAll('script');
        scripts.forEach(function (oldScript) {
            var parent = oldScript.parentNode;
            if (!parent) {
                return;
            }

            var newScript = document.createElement('script');
            Array.prototype.forEach.call(oldScript.attributes, function (attr) {
                if (attr.name === 'src' && attr.value) {
                    newScript.src = attr.value;
                } else {
                    newScript.setAttribute(attr.name, attr.value);
                }
            });

            if (!oldScript.src) {
                newScript.textContent = oldScript.textContent;
            }

            parent.replaceChild(newScript, oldScript);
        });
    }

    function syncShellFromDoc(doc) {
        var fetchedBody = doc.body;
        if (fetchedBody) {
            document.body.className = fetchedBody.className;
        }

        var pairs = [
            ['.cf-left-sidebar .cf-nav-row', '.cf-left-sidebar .cf-nav-row'],
            ['.cf-mobile-drawer .cf-nav-row', '.cf-mobile-drawer .cf-nav-row']
        ];

        pairs.forEach(function (pair) {
            var fetched = doc.querySelectorAll(pair[0]);
            var current = document.querySelectorAll(pair[1]);
            fetched.forEach(function (link, index) {
                var target = current[index];
                if (!target) {
                    return;
                }
                var active = link.classList.contains('is-active');
                target.classList.toggle('is-active', active);
                if (active) {
                    target.setAttribute('aria-current', 'page');
                } else {
                    target.removeAttribute('aria-current');
                }
            });
        });
    }

    var STYLESHEET_LOAD_TIMEOUT_MS = 10000;

    function waitForStylesheet(link) {
        return new Promise(function (resolve, reject) {
            if (link.sheet) {
                resolve();
                return;
            }

            var settled = false;
            var timer = setTimeout(function () {
                if (settled) {
                    return;
                }
                settled = true;
                reject(new Error('Stylesheet load timeout'));
            }, STYLESHEET_LOAD_TIMEOUT_MS);

            function finish(err) {
                if (settled) {
                    return;
                }
                settled = true;
                clearTimeout(timer);
                if (err) {
                    reject(err);
                } else {
                    resolve();
                }
            }

            link.addEventListener('load', function () {
                finish(null);
            });
            link.addEventListener('error', function () {
                finish(new Error('Stylesheet failed to load'));
            });
        });
    }

    function syncHeadAssetsFromDoc(doc) {
        var fetchedHead = doc.head;
        if (!fetchedHead) {
            return Promise.resolve();
        }

        var pending = [];
        var nodes = fetchedHead.querySelectorAll('link[rel="stylesheet"], style[id]');

        Array.prototype.forEach.call(nodes, function (node) {
            var id = node.getAttribute('id');
            if (!id || document.getElementById(id)) {
                return;
            }

            var clone = node.cloneNode(true);
            document.head.appendChild(clone);

            if (clone.tagName === 'LINK' && /\bstylesheet\b/i.test(clone.getAttribute('rel') || '')) {
                pending.push(waitForStylesheet(clone));
            }
        });

        return Promise.all(pending);
    }

    function revealPageSwap(container, doc, url, push) {
        var newContent = doc.querySelector(CONTENT_SELECTOR);
        if (!newContent) {
            window.location.href = url;
            return;
        }

        var titleEl = doc.querySelector('title');
        if (titleEl) {
            document.title = titleEl.textContent;
        }

        container.innerHTML = newContent.innerHTML;
        executeScripts(container);
        syncShellFromDoc(doc);

        if (push) {
            // Update the address bar (including any #hash) before post-swap hooks run so
            // listeners on cf:page-loaded / cfOnPageSwap can read window.location.hash.
            history.pushState({ cfSoftNav: true, url: url }, '', url);
        }

        window.scrollTo(0, 0);
        container.style.opacity = '1';
        container.style.filter = 'none';

        if (typeof window.cfOnPageSwap === 'function') {
            window.cfOnPageSwap(url);
        }

        document.dispatchEvent(new CustomEvent('cf:page-loaded', { detail: { url: url } }));
    }

    function finishPageSwap(container, doc, url, push) {
        var newContent = doc.querySelector(CONTENT_SELECTOR);
        if (!newContent) {
            window.location.href = url;
            return;
        }

        try {
            syncHeadAssetsFromDoc(doc)
                .then(function () {
                    revealPageSwap(container, doc, url, push);
                })
                .catch(function () {
                    window.location.href = url;
                });
        } catch (e) {
            window.location.href = url;
        }
    }

    function loadPage(url, push) {
        var container = getContainer();
        if (!container) {
            window.location.href = url;
            return;
        }

        if (loading && abortController) {
            abortController.abort();
        }

        loading = true;
        abortController = new AbortController();

        container.style.opacity = '0.55';
        container.style.filter = 'blur(2px)';
        container.style.transition = 'opacity 0.2s ease, filter 0.2s ease';

        fetch(url, {
            credentials: 'same-origin',
            signal: abortController.signal,
            headers: { 'X-CF-Soft-Nav': '1' }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.text();
            })
            .then(function (htmlText) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(htmlText, 'text/html');
                finishPageSwap(container, doc, url, push);
            })
            .catch(function (err) {
                if (err && err.name === 'AbortError') {
                    return;
                }
                window.location.href = url;
            })
            .finally(function () {
                loading = false;
                abortController = null;
            });
    }

    function onLinkClick(e) {
        var link = e.target.closest('a');
        if (!link || !isInternalLink(link)) {
            return;
        }

        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
            return;
        }

        e.preventDefault();
        loadPage(link.href, true);
    }

    function scrollToCurrentHash() {
        var hash = window.location.hash;
        if (!hash || hash.length < 2) {
            return;
        }

        var target = document.getElementById(hash.slice(1));
        if (target) {
            target.scrollIntoView({ block: 'start' });
        }
    }

    function onPopState() {
        // Same-document hash changes (e.g. legal-page TOC) fire popstate because the
        // shell seeds history with replaceState. Reloading the page resets scroll to
        // the top and fights the browser's anchor jump — skip soft-nav for hashes.
        if (window.location.hash) {
            scrollToCurrentHash();
            return;
        }

        loadPage(window.location.href, false);
    }

    function init() {
        var container = getContainer();
        if (!container) {
            return;
        }

        if (!history.state || !history.state.cfSoftNav) {
            history.replaceState({ cfSoftNav: true, url: window.location.href }, '', window.location.href);
        }

        document.addEventListener('click', onLinkClick);
        window.addEventListener('popstate', onPopState);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.cfSoftNavLoadPage = loadPage;
}());
