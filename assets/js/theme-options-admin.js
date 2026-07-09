jQuery(function ($) {
    $('.cf-color-field').wpColorPicker();

    $(document).on('click', '.cf-logo-upload', function (e) {
        e.preventDefault();

        var $field = $(this).closest('[data-cf-media-field]');
        var frame = wp.media({
            title: 'Select Logo',
            button: { text: 'Use this logo' },
            library: { type: 'image' },
            multiple: false
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            var url = (attachment.sizes && attachment.sizes.thumbnail)
                ? attachment.sizes.thumbnail.url
                : attachment.url;

            $field.find('.cf-logo-input').val(attachment.id);
            $field.find('.cf-logo-preview').html('<img src="' + url + '" alt="">');
            $field.find('.cf-logo-remove').prop('disabled', false);
        });

        frame.open();
    });

    $(document).on('click', '.cf-logo-remove', function (e) {
        e.preventDefault();

        var $field = $(this).closest('[data-cf-media-field]');
        $field.find('.cf-logo-input').val('');
        $field.find('.cf-logo-preview').empty();
        $(this).prop('disabled', true);
    });
});
