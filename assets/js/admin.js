jQuery(function ($) {
    var frame;

    $('.alrp-color').wpColorPicker();

    $(document).on('click', '.alrp-media-select', function (e) {
        e.preventDefault();
        var $box = $(this).closest('.alrp-media');

        frame = wp.media({
            title: alrpAdmin.title,
            button: { text: alrpAdmin.button },
            library: { type: 'image' },
            multiple: false
        });

        frame.on('select', function () {
            var image = frame.state().get('selection').first().toJSON();
            var thumb = image.sizes && image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url;

            $box.find('input[type="hidden"]').first().val(image.id);
            $box.find('.alrp-media-url').val('');
            $box.find('.alrp-media-preview').empty().append($('<img>', { src: thumb, alt: '' }));
            $box.find('.alrp-media-remove').prop('hidden', false);
        });

        frame.open();
    });

    $(document).on('click', '.alrp-media-remove', function (e) {
        e.preventDefault();
        var $box = $(this).closest('.alrp-media');

        $box.find('input[type="hidden"]').val('');
        $box.find('.alrp-media-preview').empty();
        $(this).prop('hidden', true);
    });
});
