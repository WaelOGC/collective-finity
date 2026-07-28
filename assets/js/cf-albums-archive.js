(function () {
	'use strict';

	var root = document.querySelector('.cf-albums-page');
	if (!root) {
		return;
	}

	var VIEW_STORAGE_KEY = 'cf_albums_view_mode';

	/* ── Genre filter carousel ─────────────────────────────────────────── */
	var wrap = root.querySelector('[data-cf-filter-carousel]');
	if (wrap) {
		var row = wrap.querySelector('.cf-filter-row');
		var prev = wrap.querySelector('.cf-filter-nav-prev');
		var next = wrap.querySelector('.cf-filter-nav-next');

		if (row && prev && next) {
			function pageWidth() {
				return Math.max(160, Math.floor(row.clientWidth * 0.7));
			}

			function updateButtons() {
				var maxScroll = Math.max(0, row.scrollWidth - row.clientWidth);
				var hasOverflow = maxScroll > 2;
				prev.hidden = !hasOverflow || row.scrollLeft <= 2;
				next.hidden = !hasOverflow || row.scrollLeft >= maxScroll - 2;
			}

			prev.addEventListener('click', function () {
				row.scrollBy({ left: -pageWidth(), behavior: 'smooth' });
			});
			next.addEventListener('click', function () {
				row.scrollBy({ left: pageWidth(), behavior: 'smooth' });
			});

			row.addEventListener('scroll', updateButtons, { passive: true });
			window.addEventListener('resize', updateButtons);

			if (typeof ResizeObserver !== 'undefined') {
				var ro = new ResizeObserver(updateButtons);
				ro.observe(row);
			}

			updateButtons();
		}
	}

	/* ── Play buttons ──────────────────────────────────────────────────── */
	function bindPlayButtons(scope) {
		(scope || root).querySelectorAll('.cf-play-btn[data-audio], .cf-albums-featured__listen[data-audio]').forEach(function (btn) {
			if (btn.dataset.cfPlayBound) {
				return;
			}
			btn.dataset.cfPlayBound = '1';
			btn.addEventListener('click', function (event) {
				event.preventDefault();
				event.stopPropagation();
				if (typeof window.playTrack !== 'function') {
					return;
				}
				window.playTrack(
					btn.getAttribute('data-audio') || '',
					btn.getAttribute('data-title') || '',
					btn.getAttribute('data-artist') || '',
					btn.getAttribute('data-cover') || ''
				);
			});
		});
	}

	bindPlayButtons();

	/* ── View mode toggle ──────────────────────────────────────────────── */
	function applyViewMode(mode) {
		mode = mode === 'list' ? 'list' : 'grid';
		try {
			localStorage.setItem(VIEW_STORAGE_KEY, mode);
		} catch (e) {}

		root.querySelectorAll('[data-cf-view]').forEach(function (el) {
			var isMatch = el.getAttribute('data-cf-view') === mode;
			if (isMatch) {
				el.removeAttribute('hidden');
			} else {
				el.setAttribute('hidden', 'hidden');
			}
		});

		root.querySelectorAll('[data-cf-view-mode]').forEach(function (btn) {
			var pressed = btn.getAttribute('data-cf-view-mode') === mode;
			btn.setAttribute('aria-pressed', pressed ? 'true' : 'false');
		});
	}

	var savedMode = 'grid';
	try {
		var stored = localStorage.getItem(VIEW_STORAGE_KEY);
		if (stored === 'list' || stored === 'grid') {
			savedMode = stored;
		}
	} catch (e) {}
	applyViewMode(savedMode);

	root.querySelectorAll('[data-cf-view-mode]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			applyViewMode(btn.getAttribute('data-cf-view-mode'));
		});
	});

	/* ── Sort ──────────────────────────────────────────────────────────── */
	var sortSelect = root.querySelector('[data-cf-albums-sort]');
	var gridEl = root.querySelector('[data-cf-albums-grid]');
	var listEl = root.querySelector('[data-cf-albums-list]');

	function sortContainer(container, mode) {
		if (!container) {
			return;
		}
		var items = Array.prototype.slice.call(container.children);
		items.sort(function (a, b) {
			if (mode === 'alpha') {
				return (a.getAttribute('data-cf-sort-title') || '').localeCompare(
					b.getAttribute('data-cf-sort-title') || ''
				);
			}
			var aDate = parseInt(a.getAttribute('data-cf-sort-date') || '0', 10);
			var bDate = parseInt(b.getAttribute('data-cf-sort-date') || '0', 10);
			if (mode === 'oldest') {
				return aDate - bDate;
			}
			return bDate - aDate;
		});
		items.forEach(function (item) {
			container.appendChild(item);
		});
	}

	function applySort(mode) {
		sortContainer(gridEl, mode);
		sortContainer(listEl, mode);
	}

	if (sortSelect) {
		sortSelect.addEventListener('change', function () {
			applySort(sortSelect.value || 'newest');
		});
	}

	/* ── Search filter ─────────────────────────────────────────────────── */
	var searchInput = root.querySelector('[data-cf-albums-search]');
	var emptyHint = root.querySelector('[data-cf-albums-empty-search]');

	function filterAlbums() {
		var q = (searchInput && searchInput.value ? searchInput.value : '').trim().toLowerCase();
		var visible = 0;

		root.querySelectorAll('[data-cf-search-title]').forEach(function (el) {
			var title = el.getAttribute('data-cf-search-title') || '';
			var show = !q || title.indexOf(q) !== -1;
			el.style.display = show ? '' : 'none';
			if (show) {
				visible += 1;
			}
		});

		if (emptyHint) {
			if (q && visible === 0) {
				emptyHint.removeAttribute('hidden');
			} else {
				emptyHint.setAttribute('hidden', 'hidden');
			}
		}
	}

	if (searchInput) {
		searchInput.addEventListener('input', filterAlbums);
	}
})();
