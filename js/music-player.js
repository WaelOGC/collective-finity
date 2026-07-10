jQuery(document).ready(function($) {
    var audio = document.getElementById('cf-native-audio-element');
    var playBtn = $('#player-toggle-btn');
    var seekBar = $('#player-progress-bg');
    var seekFill = $('#player-progress-fill');
    var volumeBar = $('#player-volume-bg');
    var volumeFill = $('#player-volume-fill');
    var volumeIcon = $('#player-volume-icon');

    window.likedTracksArray = [];
    window.cfPlayerQueue = [];
    window.cfPlayerQueueIndex = -1;
    window.cfRepeatMode = 'off';
    window.cfShuffleMode = false;
    window.cfPlaybackRate = 1;
    window.cfSpeedRates = [0.5, 0.75, 1, 1.25, 1.5, 2];
    window.cfLastVolume = 0.72;
    window.cfIsMuted = false;

    if (!audio) {
        console.warn('Collective Finity: audio element not found.');
        return;
    }

    function formatTime(seconds) {
        if (!seconds || isNaN(seconds)) {
            return '0:00';
        }
        var min = Math.floor(seconds / 60);
        var sec = Math.floor(seconds % 60);
        return min + ':' + (sec < 10 ? '0' + sec : sec);
    }

    function updateProgress() {
        if (audio.duration) {
            var progress = (audio.currentTime / audio.duration) * 100;
            seekFill.css('width', progress + '%');
            $('#player-current-time').text(formatTime(audio.currentTime));
            var $footerFill = $('#cf-footer-player-progress-fill');
            if ($footerFill.length) {
                $footerFill.css('width', progress + '%');
            }
        }
    }

    function queueTrackKeys(queue) {
        return (queue || []).map(function(track) {
            var t = normalizeTrack(track);
            return t.id != null ? String(t.id) : t.url;
        });
    }

    function isSameAlbumQueue(albumQueue) {
        if (!albumQueue || !albumQueue.length || !window.cfPlayerQueue.length) {
            return false;
        }
        if (albumQueue.length !== window.cfPlayerQueue.length) {
            return false;
        }
        var albumKeys = queueTrackKeys(albumQueue);
        var playerKeys = queueTrackKeys(window.cfPlayerQueue);
        for (var i = 0; i < albumKeys.length; i++) {
            if (albumKeys[i] !== playerKeys[i]) {
                return false;
            }
        }
        return true;
    }

    function updateAlbumPlayBtnState() {
        var $btn = $('.cf-play-album-btn');
        if (!$btn.length) {
            return;
        }
        var queue = window.cfAlbumQueue || [];
        var isSame = isSameAlbumQueue(queue);
        var $icon = $btn.find('.cf-icon').first();
        if (isSame && !audio.paused) {
            $icon.removeClass('cf-icon-play').addClass('cf-icon-pause');
            $btn.attr('aria-label', 'Pause album');
        } else {
            $icon.removeClass('cf-icon-pause').addClass('cf-icon-play');
            $btn.attr('aria-label', 'Play album');
        }
    }

    function updatePlayState() {
        if (audio.paused) {
            playBtn.html('<span class="cf-icon cf-icon-play" aria-hidden="true"></span>');
            playBtn.attr('aria-label', 'Play');
        } else {
            playBtn.html('<span class="cf-icon cf-icon-pause" aria-hidden="true"></span>');
            playBtn.attr('aria-label', 'Pause');
        }
        var $footerPlay = $('#cf-footer-player-toggle-btn');
        if ($footerPlay.length) {
            if (audio.paused) {
                $footerPlay.html('<span class="cf-icon cf-icon-play" aria-hidden="true"></span>');
                $footerPlay.attr('aria-label', 'Play');
            } else {
                $footerPlay.html('<span class="cf-icon cf-icon-pause" aria-hidden="true"></span>');
                $footerPlay.attr('aria-label', 'Pause');
            }
        }
        updateAlbumPlayBtnState();
    }

    function updateQueueIndicator() {
        var $indicator = $('#player-queue-indicator');
        if (!$indicator.length) {
            return;
        }
        if (window.cfPlayerQueue.length > 1 && window.cfPlayerQueueIndex >= 0) {
            $indicator.text((window.cfPlayerQueueIndex + 1) + ' / ' + window.cfPlayerQueue.length);
            $indicator.show();
        } else {
            $indicator.hide();
        }
    }

    function updateSpeedLabel() {
        var label = window.cfPlaybackRate === 1 ? '1×' : window.cfPlaybackRate + '×';
        $('#player-speed-btn').text(label).attr('title', 'Playback speed: ' + label);
    }

    function updateVolumeUI() {
        var vol = window.cfIsMuted ? 0 : audio.volume;
        volumeFill.css('width', (vol * 100) + '%');
        volumeIcon.toggleClass('is-muted', window.cfIsMuted || vol === 0);
    }

    function escapeCssUrl(url) {
        return String(url || '').replace(/\\/g, '\\\\').replace(/"/g, '\\"');
    }

    function normalizeTrack(track) {
        return {
            url: track.url || track.fileUrl || '',
            title: track.title || 'Unknown Track',
            artist: track.artist || 'Unknown Artist',
            art: track.art || track.artUrl || '',
            id: track.id || track.trackId || null
        };
    }

    function showPlayerError(message) {
        $('#player-track-title').text(message || 'Unable to play track');
        var $footerTitle = $('#cf-footer-player-title');
        if ($footerTitle.length) {
            $footerTitle.text(message || 'Unable to play track');
        }
        updatePlayState();
    }

    function updatePlayerTrackActions(trackId) {
        var $like = $('#player-like-btn');
        var $playlist = $('#player-playlist-btn');
        var $footerLike = $('#cf-footer-player-like-btn');
        if (!$like.length || !$playlist.length) {
            if ($footerLike.length) {
                if (trackId) {
                    $footerLike.attr('data-track-id', trackId).prop('disabled', false);
                    $footerLike.toggleClass('active', window.likedTracksArray.indexOf(parseInt(trackId, 10)) !== -1);
                } else {
                    $footerLike.removeAttr('data-track-id').removeClass('active').prop('disabled', true);
                }
            }
            return;
        }
        if (trackId) {
            $like.attr('data-track-id', trackId).prop('disabled', false);
            $playlist.attr('data-track-id', trackId).prop('disabled', false);
            if (window.likedTracksArray.indexOf(parseInt(trackId, 10)) !== -1) {
                $like.addClass('active');
            } else {
                $like.removeClass('active');
            }
            if ($footerLike.length) {
                $footerLike.attr('data-track-id', trackId).prop('disabled', false);
                $footerLike.toggleClass('active', window.likedTracksArray.indexOf(parseInt(trackId, 10)) !== -1);
            }
        } else {
            $like.removeAttr('data-track-id').removeClass('active').prop('disabled', true);
            $playlist.removeAttr('data-track-id').prop('disabled', true);
            if ($footerLike.length) {
                $footerLike.removeAttr('data-track-id').removeClass('active').prop('disabled', true);
            }
        }
    }

    function playQueueIndex(index) {
        if (!window.cfPlayerQueue.length) {
            showPlayerError('No tracks in queue');
            return;
        }

        if (index < 0 || index >= window.cfPlayerQueue.length) {
            return;
        }

        var track = normalizeTrack(window.cfPlayerQueue[index]);
        if (!track.url) {
            showPlayerError('Track has no audio file');
            return;
        }

        window.cfPlayerQueueIndex = index;
        audio.src = track.url;
        audio.playbackRate = window.cfPlaybackRate;
        $('#player-track-title').text(track.title);
        $('#player-track-artist').text(track.artist);

        if (track.art) {
            $('.cf-player-cover').css('background-image', 'url("' + escapeCssUrl(track.art) + '")');
        }

        var $footerTitle = $('#cf-footer-player-title');
        if ($footerTitle.length) {
            $footerTitle.text(track.title);
            if (track.art) {
                $('#cf-footer-player-cover').css('background-image', 'url("' + escapeCssUrl(track.art) + '")');
            }
        }

        if (track.id) {
            playBtn.attr('data-track-id', track.id);
        } else {
            playBtn.removeAttr('data-track-id');
        }

        updatePlayerTrackActions(track.id || null);

        highlightQueueRow(track.id, index);
        updateQueueIndicator();

        audio.play().then(updatePlayState).catch(function() {
            showPlayerError('Playback blocked — try again');
        });
    }

    function highlightQueueRow(trackId, index) {
        $('.cf-queue-active').removeClass('cf-queue-active');
        $('.cf-is-playing').removeClass('cf-is-playing');
        if (trackId) {
            $('.cf-album-track-row[data-track-id="' + trackId + '"]').addClass('cf-queue-active cf-is-playing');
        } else if (typeof index === 'number') {
            $('.cf-album-track-row').eq(index).addClass('cf-queue-active cf-is-playing');
        }
    }

    function getNextIndex() {
        if (!window.cfPlayerQueue.length) {
            return -1;
        }

        if (window.cfShuffleMode && window.cfPlayerQueue.length > 1) {
            var next = window.cfPlayerQueueIndex;
            while (next === window.cfPlayerQueueIndex) {
                next = Math.floor(Math.random() * window.cfPlayerQueue.length);
            }
            return next;
        }

        return window.cfPlayerQueueIndex + 1;
    }

    function getPrevIndex() {
        if (!window.cfPlayerQueue.length) {
            return -1;
        }

        if (window.cfShuffleMode && window.cfPlayerQueue.length > 1) {
            var prev = window.cfPlayerQueueIndex;
            while (prev === window.cfPlayerQueueIndex) {
                prev = Math.floor(Math.random() * window.cfPlayerQueue.length);
            }
            return prev;
        }

        return window.cfPlayerQueueIndex - 1;
    }

    function playNextTrack() {
        if (!window.cfPlayerQueue.length) {
            return;
        }

        var next = getNextIndex();
        if (next >= 0 && next < window.cfPlayerQueue.length) {
            playQueueIndex(next);
            return;
        }

        if (window.cfRepeatMode === 'all') {
            playQueueIndex(0);
        } else {
            updatePlayState();
        }
    }

    function playPrevTrack() {
        if (!window.cfPlayerQueue.length) {
            return;
        }

        if (audio.currentTime > 3) {
            audio.currentTime = 0;
            updateProgress();
            return;
        }

        var prev = getPrevIndex();
        if (prev >= 0) {
            playQueueIndex(prev);
        }
    }

    function cycleSpeed() {
        var rates = window.cfSpeedRates;
        var currentIndex = rates.indexOf(window.cfPlaybackRate);
        if (currentIndex === -1) {
            currentIndex = rates.indexOf(1);
        }
        var nextIndex = (currentIndex + 1) % rates.length;
        window.cfPlaybackRate = rates[nextIndex];
        audio.playbackRate = window.cfPlaybackRate;
        updateSpeedLabel();
    }

    function toggleMute() {
        if (window.cfIsMuted) {
            audio.volume = window.cfLastVolume || 0.72;
            window.cfIsMuted = false;
        } else {
            window.cfLastVolume = audio.volume;
            audio.volume = 0;
            window.cfIsMuted = true;
        }
        updateVolumeUI();
    }

    playBtn.on('click', function() {
        if (!audio.src) {
            if (window.cfAlbumQueue && window.cfAlbumQueue.length) {
                window.playAlbumQueue(window.cfAlbumQueue, 0);
            }
            return;
        }
        if (audio.paused) {
            audio.play().then(updatePlayState).catch(updatePlayState);
        } else {
            audio.pause();
            updatePlayState();
        }
    });

    $('#player-next-btn').on('click', playNextTrack);
    $('#player-prev-btn').on('click', playPrevTrack);

    $('#player-repeat-btn').on('click', function() {
        var modes = ['off', 'all', 'one'];
        var current = modes.indexOf(window.cfRepeatMode);
        window.cfRepeatMode = modes[(current + 1) % modes.length];
        $(this).toggleClass('is-active', window.cfRepeatMode !== 'off');
        $(this).attr('data-mode', window.cfRepeatMode);
        var titles = { off: 'Repeat off', all: 'Repeat all', one: 'Repeat one' };
        $(this).attr('title', titles[window.cfRepeatMode] || 'Repeat');
    });

    $('#player-shuffle-btn').on('click', function() {
        window.cfShuffleMode = !window.cfShuffleMode;
        $(this).toggleClass('is-active', window.cfShuffleMode);
    });

    $('#player-speed-btn').on('click', cycleSpeed);

    volumeIcon.on('click', toggleMute);

    audio.addEventListener('timeupdate', updateProgress);
    audio.addEventListener('loadedmetadata', function() {
        $('#player-duration').text(formatTime(audio.duration));
        updateProgress();
    });
    audio.addEventListener('ended', function() {
        if (window.cfRepeatMode === 'one') {
            audio.currentTime = 0;
            audio.play();
            return;
        }
        playNextTrack();
    });
    audio.addEventListener('play', updatePlayState);
    audio.addEventListener('pause', updatePlayState);

    seekBar.on('click', function(e) {
        if (!audio.duration) {
            return;
        }
        var offset = $(this).offset();
        var x = e.pageX - offset.left;
        var width = $(this).width();
        var percent = x / width;
        audio.currentTime = audio.duration * percent;
        updateProgress();
    });

    volumeBar.on('click', function(e) {
        var offset = $(this).offset();
        var x = e.pageX - offset.left;
        var width = $(this).width();
        var percent = Math.max(0, Math.min(1, x / width));
        audio.volume = percent;
        window.cfIsMuted = false;
        window.cfLastVolume = percent;
        updateVolumeUI();
    });

    $('#cf-footer-player-toggle-btn').on('click', function() {
        playBtn.trigger('click');
    });

    $('#cf-footer-player-prev-btn').on('click', function() {
        playPrevTrack();
    });

    $('#cf-footer-player-next-btn').on('click', function() {
        playNextTrack();
    });

    $('#cf-footer-player-like-btn').on('click', function(e) {
        e.preventDefault();
        var trackId = $(this).attr('data-track-id');
        if (trackId) {
            $('.cf-like-btn[data-track-id="' + trackId + '"]').first().trigger('click');
        }
    });

    $('#cf-footer-player-progress-bg').on('click', function(e) {
        if (!audio.duration) {
            return;
        }
        var offset = $(this).offset();
        var x = e.pageX - offset.left;
        var width = $(this).width();
        var percent = x / width;
        audio.currentTime = audio.duration * percent;
        updateProgress();
    });

    window.playTrack = function(fileUrl, title, artist, artUrl, trackId, queue, queueIndex) {
        if (!audio) {
            return;
        }

        if (Array.isArray(queue) && queue.length) {
            window.cfPlayerQueue = queue.map(normalizeTrack).filter(function(item) {
                return !!item.url;
            });
            if (!window.cfPlayerQueue.length) {
                showPlayerError('No playable tracks in queue');
                return;
            }
            window.cfPlayerQueueIndex = typeof queueIndex === 'number' ? queueIndex : 0;
            if (window.cfPlayerQueueIndex >= window.cfPlayerQueue.length) {
                window.cfPlayerQueueIndex = 0;
            }
            playQueueIndex(window.cfPlayerQueueIndex);
            return;
        }

        if (!fileUrl) {
            return;
        }

        var singleTrack = normalizeTrack({
            url: fileUrl,
            title: title,
            artist: artist,
            art: artUrl,
            id: trackId
        });

        function playWithLibrary(library) {
            var normalized = (library || []).map(normalizeTrack).filter(function(item) {
                return !!item.url;
            });

            if (!normalized.length) {
                window.cfPlayerQueue = [singleTrack];
                window.cfPlayerQueueIndex = 0;
                playQueueIndex(0);
                return;
            }

            var idx = 0;
            if (trackId) {
                for (var i = 0; i < normalized.length; i++) {
                    if (normalized[i].id != null && String(normalized[i].id) === String(trackId)) {
                        idx = i;
                        break;
                    }
                }
            } else {
                for (var j = 0; j < normalized.length; j++) {
                    if (normalized[j].url === fileUrl) {
                        idx = j;
                        break;
                    }
                }
            }

            window.cfPlayerQueue = normalized;
            window.cfPlayerQueueIndex = idx;
            playQueueIndex(idx);
        }

        if (window.cfTrackLibraryCache && window.cfTrackLibraryCache.length) {
            playWithLibrary(window.cfTrackLibraryCache);
            return;
        }

        if (!window.cfTrackLibraryCachePromise) {
            window.cfTrackLibraryCachePromise = $.ajax({
                url: cf_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'cf_get_track_library',
                    security: cf_ajax.nonce
                }
            }).then(function(response) {
                if (response.success && response.data && response.data.tracks) {
                    window.cfTrackLibraryCache = response.data.tracks;
                    return response.data.tracks;
                }
                return [];
            }).catch(function() {
                return [];
            });
        }

        window.cfTrackLibraryCachePromise.then(playWithLibrary);
    };

    window.playAlbumQueue = function(queue, startIndex) {
        if (!Array.isArray(queue) || !queue.length) {
            showPlayerError('No tracks available');
            return;
        }
        window.playTrack(null, null, null, null, null, queue, startIndex || 0);
    };

    $(document).on('click', '.cf-play-album-btn', function(e) {
        e.preventDefault();
        var queue = window.cfAlbumQueue || [];
        if (!queue.length) {
            showPlayerError('No playable tracks on this album');
            return;
        }
        if (isSameAlbumQueue(queue)) {
            if (audio.paused) {
                audio.play().then(updatePlayState).catch(updatePlayState);
            } else {
                audio.pause();
                updatePlayState();
            }
            return;
        }
        window.playAlbumQueue(queue, 0);
    });

    $(document).on('click', '.cf-list-play-trigger', function(e) {
        e.preventDefault();
        var queue = window.cfAlbumQueue || [];
        if (!queue.length) {
            return;
        }
        var index = parseInt($(this).attr('data-queue-index'), 10);
        if (isNaN(index)) {
            index = 0;
        }
        window.playAlbumQueue(queue, index);
    });

    $(document).on('keydown', function(e) {
        if ($(e.target).is('input, textarea, select, [contenteditable="true"]')) {
            return;
        }
        if (e.code === 'Space') {
            e.preventDefault();
            playBtn.trigger('click');
        } else if (e.code === 'ArrowRight' && e.shiftKey) {
            playNextTrack();
        } else if (e.code === 'ArrowLeft' && e.shiftKey) {
            playPrevTrack();
        }
    });

    playBtn.html('<span class="cf-icon cf-icon-play" aria-hidden="true"></span>');
    updateSpeedLabel();
    updateVolumeUI();

    // Cold load / hard refresh: never autoplay when the queue is empty (browser policy + idle player).
    if (!window.cfPlayerQueue.length && !audio.src) {
        updatePlayState();
    }

    window.cfOnPageSwap = function() {
        updateAlbumPlayBtnState();
        highlightQueueRow(
            playBtn.attr('data-track-id') || null,
            window.cfPlayerQueueIndex
        );
        if (window.cfPageTrackId) {
            updatePlayerTrackActions(window.cfPageTrackId);
        } else if (playBtn.attr('data-track-id')) {
            updatePlayerTrackActions(playBtn.attr('data-track-id'));
        }
        $('.cf-like-btn').each(function() {
            var tid = $(this).data('track-id');
            if (tid && window.likedTracksArray.indexOf(parseInt(tid, 10)) !== -1) {
                $(this).addClass('active');
            }
        });
    };

    $(document).on('click', '#cf-sidebar-toggle-btn', function(e) {
        e.preventDefault();
        var panel = $('#cf-music-sidebar');
        var icon = $('#cf-toggle-icon');
        var body = $('body');

        panel.toggleClass('active');
        body.toggleClass('cf-sidebar-open');

        if (panel.hasClass('active')) {
            icon.removeClass('dashicons-menu-alt3').addClass('dashicons-no');
        } else {
            icon.removeClass('dashicons-no').addClass('dashicons-menu-alt3');
        }
    });

    $(document).on('click', '#cf-mobile-hamburger-trigger', function(e) {
        e.preventDefault();
        $('#cf-music-sidebar').addClass('active');
        $('body').addClass('cf-sidebar-open');
        $('#cf-toggle-icon').removeClass('dashicons-menu-alt3').addClass('dashicons-no');
    });

    $(document).on('mouseup', function(e) {
        var container = $('#cf-music-sidebar');
        var trigger = $('#cf-mobile-hamburger-trigger, #cf-sidebar-toggle-btn');
        var icon = $('#cf-toggle-icon');
        if (!container.is(e.target) && container.has(e.target).length === 0 && !trigger.is(e.target) && trigger.has(e.target).length === 0) {
            container.removeClass('active');
            $('body').removeClass('cf-sidebar-open');
            icon.removeClass('dashicons-no').addClass('dashicons-menu-alt3');
        }
    });

    if (window.CF_AUTH && window.CF_AUTH.is_logged_in === '1' && typeof window.CF_Auth !== 'undefined' && window.CF_Auth.getFavorites) {
        window.CF_Auth.getFavorites().then(function(result) {
            if (result && result.tracks) {
                window.likedTracksArray = result.tracks;
                $('.cf-like-btn').each(function() {
                    var trackId = $(this).data('track-id');
                    if (window.likedTracksArray.indexOf(parseInt(trackId, 10)) !== -1) {
                        $(this).addClass('active');
                    }
                });
                if (window.cfPageTrackId) {
                    updatePlayerTrackActions(window.cfPageTrackId);
                } else if (playBtn.attr('data-track-id')) {
                    updatePlayerTrackActions(playBtn.attr('data-track-id'));
                }
            }
        }).catch(function() {});
    }

    if (window.cfPageTrackId) {
        updatePlayerTrackActions(window.cfPageTrackId);
    }

    $(document).on('click', '.cf-like-btn', function(e) {
        e.preventDefault();

        if (!window.CF_AUTH || window.CF_AUTH.is_logged_in !== '1') {
            alert('Please log in to add tracks to your favorites.');
            return;
        }
        if (typeof window.CF_Auth === 'undefined' || !window.CF_Auth.toggleFavorite) {
            alert('An error occurred. Please try again.');
            return;
        }

        var $btn = $(this);
        var trackId = $btn.data('track-id');
        if (!trackId) {
            return;
        }

        $('.cf-like-btn[data-track-id="' + trackId + '"]').toggleClass('active');

        window.CF_Auth.toggleFavorite(trackId, 'track')
            .then(function(result) {
                var isLiked = result.is_favorite;
                var index = window.likedTracksArray.indexOf(parseInt(trackId, 10));

                if (isLiked) {
                    if (index === -1) {
                        window.likedTracksArray.push(parseInt(trackId, 10));
                    }
                    $('.cf-like-btn[data-track-id="' + trackId + '"]').addClass('active');
                } else {
                    if (index !== -1) {
                        window.likedTracksArray.splice(index, 1);
                    }
                    $('.cf-like-btn[data-track-id="' + trackId + '"]').removeClass('active');
                }

                $('.live-likes').text(result.likes_count);
                if ($('#player-like-btn').attr('data-track-id') == trackId) {
                    $('#player-like-btn').toggleClass('active', isLiked);
                }
                if ($('#cf-footer-player-like-btn').attr('data-track-id') == trackId) {
                    $('#cf-footer-player-like-btn').toggleClass('active', isLiked);
                }
            })
            .catch(function(err) {
                $('.cf-like-btn[data-track-id="' + trackId + '"]').toggleClass('active');
                var message = (err && err.message) ? err.message : 'An error occurred. Please try again.';
                alert(message);
            });
    });

    $(document).on('click', '.cf-playlist-btn', function(e) {
        e.preventDefault();
        $('#cf-target-track-id').val($(this).data('track-id'));
        $('#cf-playlist-modal').fadeIn(200);
    });

    $(document).on('click', '#cf-close-playlist-modal, .cf-playlist-modal-overlay', function(e) {
        if (e.target === this) {
            $('#cf-playlist-modal').fadeOut(150);
        }
    });

    $(document).on('click', '.cf-playlist-item', function() {
        $(this).toggleClass('added');
        var statusIcon = $(this).find('.cf-playlist-status');
        if ($(this).hasClass('added')) {
            statusIcon.removeClass('dashicons-no').addClass('dashicons-yes');
        } else {
            statusIcon.removeClass('dashicons-yes').addClass('dashicons-no');
        }
    });

    $(document).on('click', '#cf-create-playlist-btn', function(e) {
        e.preventDefault();
        var playlistName = $('#cf-new-playlist-input').val().trim();
        if (playlistName !== '') {
            var randomId = Math.floor(Math.random() * 1000);
            var newItem = $('<div class="cf-playlist-item" data-playlist-id="' + randomId + '">' +
                '<span class="dashicons dashicons-media-text"></span>' +
                '<span class="cf-playlist-name">' + playlistName + '</span>' +
                '<span class="cf-playlist-status dashicons dashicons-no"></span>' +
                '</div>');
            $('.cf-playlists-list').append(newItem);
            $('#cf-new-playlist-input').val('');
        }
    });

    $(document).on('click', '.cf-share-copy-btn', function() {
        var url = $(this).data('share-url');
        var $btn = $(this);
        if (!url) {
            return;
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function() {
                $btn.addClass('is-copied');
                setTimeout(function() { $btn.removeClass('is-copied'); }, 2000);
            });
        }
    });
});
