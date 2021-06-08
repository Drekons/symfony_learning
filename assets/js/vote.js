import $ from 'jquery';

$(function () {

    $('[data-vote-control]').on('click', function (e) {
        e.preventDefault();
        const $container = $(this),
        $counter = $container.parent().find('span');

        $('[data-vote-control]').prop('disabled', true);

        $.ajax({
            url: $container.data('vote-control'),
            method: 'POST'
        }).then(function (data) {
            $counter.text(data.count);
            $counter.removeClass('text-success text-danger');
            if (data.count > 0) {
                $counter.addClass('text-success')
            }
            if (data.count < 0) {
                $counter.addClass('text-danger')
            }
        });
    });

});
