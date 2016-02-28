$(document).ready(function() {

    // Notify btn in popup
    $('.more-game').click(function(){

        var btn = $(this);

        if (!btn.hasClass('loading')) {

            btn.button('loading');
            btn.addClass('loading');

            var firstTeam = [];
            $('#game_filter_firstTeam :selected').each(function(i, selected){
                firstTeam[i] = $(selected).val();
            });
            var secondTeam = [];
            $('#game_filter_secondTeam :selected').each(function(i, selected){
                secondTeam[i] = $(selected).val();
            });

            $.ajax({
                type: 'POST',
                url: $(this).data('href'),
                data: {
                    page: $(this).data('page'),
                    dateRange: $('#date-filter').val(),
                    firstTeam: firstTeam,
                    secondTeam: secondTeam
                },
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
