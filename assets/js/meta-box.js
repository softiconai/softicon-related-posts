jQuery(function ($) {
    var $box = $('.alrp-manual');
    if (!$box.length) {
        return;
    }

    var $input = $box.find('input[name="alrp_manual_ids"]');
    var $search = $box.find('.alrp-manual-search');
    var $results = $box.find('.alrp-manual-results');
    var $list = $box.find('.alrp-manual-list');
    var postId = parseInt($box.data('post-id'), 10);
    var postType = $box.data('post-type');
    var timer;

    function ids() {
        return $list.children().map(function () {
            return parseInt($(this).data('id'), 10);
        }).get();
    }

    function sync() {
        $input.val(ids().join(','));
    }

    function addItem(id, title) {
        var $remove = $('<button type="button" class="button-link alrp-manual-remove">').attr('aria-label', alrpMetaBox.remove).html('&times;');
        $list.append($('<li>').attr('data-id', id).text(title + ' ').append($remove));
        sync();
    }

    $search.on('input', function () {
        clearTimeout(timer);
        var term = $search.val().trim();
        if (term.length < 2) {
            $results.empty();
            return;
        }

        timer = setTimeout(function () {
            wp.apiFetch({
                path: wp.url.addQueryArgs('/wp/v2/search', { search: term, type: 'post', subtype: postType, per_page: 10 })
            }).then(function (posts) {
                var picked = ids();
                $results.empty();
                posts = posts.filter(function (post) {
                    return post.id !== postId && picked.indexOf(post.id) === -1;
                });
                if (!posts.length) {
                    $results.append($('<li>').text(alrpMetaBox.noResults));
                }
                posts.forEach(function (post) {
                    // search titles come HTML-encoded, e.g. &#8217;
                    var title = $('<textarea>').html(post.title).val();
                    var $pick = $('<button type="button" class="button-link alrp-manual-pick">').data('id', post.id).text(title);
                    $results.append($('<li>').append($pick));
                });
            }).catch(function () {
                // post types without show_in_rest can't be searched
                $results.empty().append($('<li>').text(alrpMetaBox.noResults));
            });
        }, 300);
    });

    $results.on('click', '.alrp-manual-pick', function () {
        if (ids().length >= alrpMetaBox.max) {
            return;
        }
        addItem($(this).data('id'), $(this).text());
        $results.empty();
        $search.val('').trigger('focus');
    });

    $list.on('click', '.alrp-manual-remove', function () {
        $(this).closest('li').remove();
        sync();
    });
});
