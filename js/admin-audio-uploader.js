jQuery(document).ready(function($) {
    
    // --- 1. Dynamic Toggle Logic for Release Type ---
    function handleReleaseTypeToggle() {
        var selectedValue = $('.cf-release-type-radio:checked').val();
        
        if (selectedValue === 'album_track') {
            $('#cf-associated-album-wrapper').slideDown(250);
            $('#associated_album').attr('required', 'required');
        } else {
            $('#cf-associated-album-wrapper').slideUp(200);
            $('#associated_album').removeAttr('required').val(''); // Clear on collapse
        }
    }

    // Run on load and change
    handleReleaseTypeToggle();
    $(document).on('change', '.cf-release-type-radio', function() {
        handleReleaseTypeToggle();
    });


    // --- 2. Upgraded WP Core Media Library Uploader (With Event Delegation) ---
    var mediaUploader;

    $(document).on('click', '.cf-media-upload-btn', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var targetInputId = button.data('target');
        var mediaType = button.data('type') || 'audio';

        // Initialize WordPress Media Selection Window
        mediaUploader = wp.media({
            title: 'Select Track Resource File',
            button: {
                text: 'Assign to Track'
            },
            multiple: false,
            library: {
                type: mediaType // Filters library (audio, image, text)
            }
        });

        // Write selection details to inputs
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#' + targetInputId).val(attachment.url);
            
            // If the selector is an image, update the preview element dynamically
            if(mediaType === 'image') {
                $('#' + targetInputId + '_preview').attr('src', attachment.url);
            }
        });

        mediaUploader.open();
    });

    // Handle Clear / Remove Action
    $(document).on('click', '.cf-media-clear-btn', function(e) {
        e.preventDefault();
        var targetInputId = $(this).data('target');
        $('#' + targetInputId).val('');
        
        // Reset preview image to default if cover was cleared
        if(targetInputId === 'track_cover_url') {
            $('#track_cover_url_preview').attr('src', '');
        }
    });
});