/**
 * Collective Finity — Add to Home Screen banner.
 * Captures beforeinstallprompt and shows a footer install strip.
 * iOS Safari is unsupported (no beforeinstallprompt); the banner stays hidden.
 */
(function () {
	'use strict';

	var banner = document.getElementById('cf-a2hs-banner');
	var installBtn = document.getElementById('cf-a2hs-install-btn');
	var dismissBtn = document.getElementById('cf-a2hs-dismiss-btn');
	var deferredPrompt = null;

	if (!banner) {
		return;
	}

	function isMarkedInstalled() {
		try {
			return window.localStorage.getItem('cf_pwa_installed') === '1';
		} catch (e) {
			return false;
		}
	}

	function hideBanner() {
		banner.setAttribute('hidden', '');
	}

	function showBanner() {
		if (!deferredPrompt || isMarkedInstalled()) {
			return;
		}
		banner.removeAttribute('hidden');
	}

	function markInstalled() {
		try {
			window.localStorage.setItem('cf_pwa_installed', '1');
		} catch (e) {}
		deferredPrompt = null;
		hideBanner();
	}

	window.addEventListener('beforeinstallprompt', function (event) {
		event.preventDefault();
		deferredPrompt = event;
		showBanner();
	});

	window.addEventListener('appinstalled', function () {
		markInstalled();
	});

	if (installBtn) {
		installBtn.addEventListener('click', function () {
			if (!deferredPrompt) {
				return;
			}

			var promptEvent = deferredPrompt;
			deferredPrompt = null;
			promptEvent.prompt();

			if (promptEvent.userChoice && typeof promptEvent.userChoice.then === 'function') {
				promptEvent.userChoice.then(function (choice) {
					if (choice && choice.outcome === 'accepted') {
						markInstalled();
					}
				});
			}
		});
	}

	if (dismissBtn) {
		dismissBtn.addEventListener('click', function () {
			hideBanner();
		});
	}
})();
