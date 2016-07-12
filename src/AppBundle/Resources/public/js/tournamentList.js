$(document).ready(function() {
    applyBtnInit();
});

var applyBtnInit = function() {
    $('.t-match-form .game-submit').click(function () {
        var form = $(this).closest('.t-match-form');

        var isEmpty = false;
        form.find('.game-score-input input').each(function () {
            if (!$(this).val().length) {
                isEmpty = true;
            }
        });

        if (!isEmpty) {

            $.ajax({
                type: "POST",
                url: form.attr('action'),
                data: form.serialize(),
                success: function (response) {
                    if (response.status == 1) {
                        $('#tournament-stat-table').html(response.statistics);
                        $('#tournament-game-list').html(response.games);
                        applyBtnInit();
                    }
                }
            });

        }
    });
};
