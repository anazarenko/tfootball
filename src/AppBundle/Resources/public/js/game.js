$(document).ready(function() {

    // Notify btn in popup
    $('.more-game').click(function(){

        var btn = $(this);

        if (!btn.hasClass('loading')) {

            btn.button('loading');
            btn.addClass('loading');

            $.ajax({
                type: 'POST',
                url: $(this).data('href'),
                data: {page: $(this).data('page'), dateRange: $('#date-filter').val()},
                dataType: 'json'
            }).success(function (data) {
                if (data.status == 1) {
                    if (data.moreBtn == false) {
                        $('.load-more-container').remove();
                    } else {
                        btn.data('page', data.page);
                    }
                    $('.game-list').append(data.games);
                } else {
                    console.log(data);
                }
            }).always(function () {
                btn.button('reset');
                btn.removeClass('loading');
            });
        }

    });

});
