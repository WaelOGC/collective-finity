(function () {
    function initAccordions() {
        document.querySelectorAll('[data-cf-accordion]').forEach(function (root) {
            if (root.dataset.cfAccordionBound) {
                return;
            }
            root.dataset.cfAccordionBound = '1';

            root.addEventListener('click', function (event) {
                var trigger = event.target.closest('.cf-faq__trigger');
                if (!trigger || !root.contains(trigger)) {
                    return;
                }

                var item = trigger.closest('.cf-faq__item');
                var panel = item ? item.querySelector('.cf-faq__panel') : null;
                if (!item || !panel) {
                    return;
                }

                var isOpen = item.classList.contains('is-open');

                root.querySelectorAll('.cf-faq__item').forEach(function (faqItem) {
                    faqItem.classList.remove('is-open');
                    var faqTrigger = faqItem.querySelector('.cf-faq__trigger');
                    var faqPanel = faqItem.querySelector('.cf-faq__panel');
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
        });
    }

    function initReadProgress() {
        var bar = document.querySelector('.cf-read-progress__bar');
        var article = document.querySelector('.cf-post-main');
        if (!bar || !article) {
            return;
        }

        function updateProgress() {
            var rect = article.getBoundingClientRect();
            var total = article.offsetHeight - window.innerHeight;
            if (total <= 0) {
                bar.style.width = '0%';
                return;
            }
            var scrolled = window.scrollY - article.offsetTop;
            var pct = Math.max(0, Math.min(100, (scrolled / total) * 100));
            bar.style.width = pct + '%';
        }

        window.addEventListener('scroll', updateProgress, { passive: true });
        updateProgress();
    }

    function buildTocList(container, headings) {
        headings.forEach(function (heading) {
            var link = document.createElement('a');
            link.className = 'cf-post-toc__link';
            link.href = '#' + heading.id;
            link.textContent = heading.textContent;
            container.appendChild(link);
        });
    }

    function initPostToc() {
        var tocLists = document.querySelectorAll('[data-cf-post-toc]');
        if (!tocLists.length) {
            return;
        }

        var headings = document.querySelectorAll('.cf-post-content h2[id]');
        if (!headings.length) {
            return;
        }

        tocLists.forEach(function (tocList) {
            buildTocList(tocList, headings);
        });

        var accordion = document.querySelector('[data-cf-toc-accordion]');
        if (accordion) {
            var trigger = accordion.querySelector('.cf-faq__trigger');
            var panel = accordion.querySelector('.cf-faq__panel');
            if (trigger && panel) {
                trigger.addEventListener('click', function () {
                    var open = accordion.classList.toggle('is-open');
                    trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                    panel.hidden = !open;
                });
            }
        }
    }

    function initPostLike() {
        var btn = document.querySelector('[data-cf-post-like]');
        if (!btn || typeof cf_ajax === 'undefined') {
            return;
        }

        btn.addEventListener('click', function () {
            var postId = btn.getAttribute('data-post-id');
            if (!postId) {
                return;
            }

            jQuery.post(cf_ajax.ajax_url, {
                action: 'cf_toggle_post_like',
                security: cf_ajax.nonce,
                post_id: postId
            }).done(function (response) {
                if (response && response.success) {
                    btn.classList.toggle('is-active', response.data.status === 'liked');
                    var countEl = btn.querySelector('[data-like-count]');
                    if (countEl && typeof response.data.likes_count !== 'undefined') {
                        countEl.textContent = response.data.likes_count;
                    }
                }
            });
        });
    }

    function initAuthForms() {
        document.querySelectorAll('[data-cf-auth-form]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                if (typeof cf_ajax === 'undefined') {
                    return;
                }

                var action = form.getAttribute('data-cf-auth-form');
                var message = form.querySelector('.cf-auth-message');
                var formData = new FormData(form);
                formData.append('action', action);
                formData.append('security', cf_ajax.nonce);

                if (message) {
                    message.hidden = false;
                }

                fetch(cf_ajax.ajax_url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }).then(function (res) {
                    return res.json();
                }).then(function (data) {
                    if (!message) {
                        if (data.success && data.data && data.data.redirect) {
                            window.location.href = data.data.redirect;
                        }
                        return;
                    }

                    message.classList.remove('is-error', 'is-success');
                    if (data.success) {
                        message.classList.add('is-success');
                        message.textContent = data.data && data.data.message ? data.data.message : 'Success';
                        if (data.data && data.data.redirect) {
                            window.setTimeout(function () {
                                window.location.href = data.data.redirect;
                            }, 600);
                        }
                    } else {
                        message.classList.add('is-error');
                        message.textContent = data.data && data.data.message ? data.data.message : 'Request failed.';
                    }
                }).catch(function () {
                    if (message) {
                        message.classList.add('is-error');
                        message.textContent = 'Request failed.';
                    }
                });
            });
        });
    }

    function initProfileTabs() {
        var tabs = document.querySelectorAll('[data-cf-profile-tab]');
        var panels = document.querySelectorAll('[data-cf-profile-panel]');
        if (!tabs.length || !panels.length) {
            return;
        }

        function activate(tabName) {
            tabs.forEach(function (tab) {
                tab.classList.toggle('is-active', tab.getAttribute('data-cf-profile-tab') === tabName);
            });
            panels.forEach(function (panel) {
                panel.hidden = panel.getAttribute('data-cf-profile-panel') !== tabName;
            });
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function (event) {
                event.preventDefault();
                activate(tab.getAttribute('data-cf-profile-tab'));
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tab.getAttribute('data-cf-profile-tab'));
                }
            });
        });

        var hash = window.location.hash.replace('#', '');
        if (hash) {
            activate(hash);
        }
    }

    function initNotificationToggles() {
        document.querySelectorAll('[data-cf-notif-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (typeof cf_ajax === 'undefined') {
                    return;
                }

                var key = btn.getAttribute('data-notif-key');
                if (!key) {
                    return;
                }

                fetch(cf_ajax.ajax_url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: new URLSearchParams({
                        action: 'cf_toggle_notification_pref',
                        security: cf_ajax.nonce,
                        key: key
                    }).toString()
                }).then(function (res) {
                    return res.json();
                }).then(function (data) {
                    if (data && data.success) {
                        var enabled = !!(data.data && data.data.enabled);
                        btn.classList.toggle('is-on', enabled);
                        btn.setAttribute('aria-pressed', enabled ? 'true' : 'false');
                    }
                });
            });
        });
    }

    function initPlaylistModalTrigger() {
        document.querySelectorAll('[data-cf-open-playlist-modal]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (typeof jQuery !== 'undefined') {
                    jQuery('#cf-playlist-modal').fadeIn(200);
                    return;
                }
                var modal = document.getElementById('cf-playlist-modal');
                if (modal) {
                    modal.style.display = 'flex';
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initAccordions();
        initReadProgress();
        initPostToc();
        initPostLike();
        initAuthForms();
        initProfileTabs();
        initNotificationToggles();
        initPlaylistModalTrigger();
    });
})();
