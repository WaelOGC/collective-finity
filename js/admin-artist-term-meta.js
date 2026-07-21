/**
 * Media uploader, bio counter, and genre pills for track_artist term fields.
 */
(function ($) {
    'use strict';

    function updateBioCounter($textarea) {
        var max = parseInt($textarea.attr('maxlength'), 10) || 150;
        var len = ($textarea.val() || '').length;
        var $counter = $textarea.closest('.form-field, td').find('[data-cf-artist-bio-counter]').first();
        if (!$counter.length) {
            $counter = $('[data-cf-artist-bio-counter]').first();
        }
        $counter.text(len + ' / ' + max);
        $counter.toggleClass('is-near-limit', len >= max);
    }

    function syncGenrePill($input) {
        $input.closest('.cf-artist-genre-pill').toggleClass('is-checked', $input.is(':checked'));
    }

    $(document).on('click', '.cf-artist-photo-upload', function (e) {
        e.preventDefault();

        var $field = $(this).closest('[data-cf-artist-photo]');
        var frame = wp.media({
            title: 'Select Artist Photo',
            button: { text: 'Use this photo' },
            library: { type: 'image' },
            multiple: false
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            var url = (attachment.sizes && attachment.sizes.medium)
                ? attachment.sizes.medium.url
                : attachment.url;

            $field.find('#artist_photo_id').val(attachment.id);
            $field.find('.cf-artist-photo-preview').html(
                '<img src="' + url + '" alt="" style="max-width:150px;height:auto;border-radius:50%;display:block;" />'
            );
            $field.find('.cf-artist-photo-remove').prop('disabled', false);
        });

        frame.open();
    });

    $(document).on('click', '.cf-artist-photo-remove', function (e) {
        e.preventDefault();

        if ($(this).prop('disabled')) {
            return;
        }

        var $field = $(this).closest('[data-cf-artist-photo]');
        $field.find('#artist_photo_id').val('');
        $field.find('.cf-artist-photo-preview').html(
            '<span class="cf-artist-photo-placeholder" style="display:inline-flex;align-items:center;justify-content:center;width:120px;height:120px;border-radius:50%;background:#1a1a1a;color:#666;font-size:12px;">No photo</span>'
        );
        $(this).prop('disabled', true);
    });

    $(document).on('input', '[data-cf-artist-bio]', function () {
        updateBioCounter($(this));
    });

    $(document).on('change', '[data-cf-artist-genres] input[type="checkbox"]', function () {
        syncGenrePill($(this));
    });

    $(function () {
        $('[data-cf-artist-bio]').each(function () {
            updateBioCounter($(this));
        });
        $('[data-cf-artist-genres] input[type="checkbox"]').each(function () {
            syncGenrePill($(this));
        });
    });
})(jQuery);
