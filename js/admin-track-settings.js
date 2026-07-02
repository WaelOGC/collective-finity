(function($) {
    $(document).ready(function() {

        function handleReleaseTypeToggle() {
            var selectedValue = $('.cf-release-type-radio:checked').val();

            if (selectedValue === 'album_track') {
                $('#cf-associated-album-wrapper').slideDown(250);
                $('#associated_album').attr('required', 'required');
            } else {
                $('#cf-associated-album-wrapper').slideUp(200);
                $('#associated_album').removeAttr('required').val('');
            }
        }

        handleReleaseTypeToggle();

        $(document).on('change', '.cf-release-type-radio', function() {
            handleReleaseTypeToggle();
        });

        function updateMediaFieldState(targetInputId) {
            var $input = $('#' + targetInputId);
            var hasValue = $.trim($input.val()).length > 0;
            var $row = $input.closest('.cf-audio-file-row, .cf-cover-field-group');
            var $clearBtn = $('.cf-media-clear-btn[data-target="' + targetInputId + '"]');

            if ($row.length) {
                $row.toggleClass('has-file', hasValue);
            }

            if ($clearBtn.length) {
                $clearBtn.prop('disabled', !hasValue).toggleClass('is-disabled', !hasValue);
            }
        }

        function initMediaFieldStates() {
            $('.cf-audio-file-row [id], .cf-cover-field-group [id]').each(function() {
                if (this.id) {
                    updateMediaFieldState(this.id);
                }
            });
        }

        initMediaFieldStates();

        var urlParams = new URLSearchParams(window.location.search);
        var preselectAlbum = urlParams.get('preselect_album');
        if (preselectAlbum) {
            $('.cf-release-type-radio[value="album_track"]').prop('checked', true);
            handleReleaseTypeToggle();
            $('#associated_album').val(preselectAlbum);
        }

        var mediaUploader;

        $(document).on('click', '.cf-media-upload-btn', function(e) {
            e.preventDefault();

            var button = $(this);
            var targetInputId = button.data('target');
            var mediaType = button.data('type') || 'audio';

            mediaUploader = wp.media({
                title: 'Select Track Resource File',
                button: {
                    text: 'Assign to Track'
                },
                multiple: false,
                library: {
                    type: mediaType
                }
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#' + targetInputId).val(attachment.url);
                updateMediaFieldState(targetInputId);

                if (mediaType === 'image') {
                    $('#' + targetInputId + '_preview').attr('src', attachment.url);
                }
            });

            mediaUploader.open();
        });

        $(document).on('click', '.cf-media-clear-btn', function(e) {
            e.preventDefault();

            if ($(this).prop('disabled')) {
                return;
            }

            var targetInputId = $(this).data('target');
            $('#' + targetInputId).val('');
            updateMediaFieldState(targetInputId);

            if (targetInputId === 'track_cover_url') {
                $('#track_cover_url_preview').attr('src', '');
            }
        });

        var $sections = $('.cf-track-meta-panel .cf-meta-section');
        if ($sections.length && 'IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        $sections.removeClass('is-focused');
                        $(entry.target).addClass('is-focused');
                    }
                });
            }, { root: null, rootMargin: '-20% 0px -55% 0px', threshold: 0.1 });

            $sections.each(function() {
                observer.observe(this);
            });
        } else {
            $sections.first().addClass('is-focused');
            $sections.on('focusin', function() {
                $sections.removeClass('is-focused');
                $(this).addClass('is-focused');
            });
        }

    });
})(jQuery);
