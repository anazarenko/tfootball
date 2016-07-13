$(document).ready(function() {
    applyBtnInit();
    editBtnInit();
    standingInit();
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
                        editBtnInit();
                    }
                }
            });

        }
    });
};

var editBtnInit = function(){
    $('.game-edit').click(function(){
        $(this).closest('.game-row').addClass('game-input-active');
    });
};

var standingInit = function (){
    // Sortable rows
    $('.sorted_table').sortable({
        containerSelector: 'table',
        itemPath: '> tbody',
        itemSelector: 'tr',
        placeholder: '<tr class="placeholder"/>',
        onDrop: function (item, container, _super, event) {
            var table = $('.table-tournament');
            var tr =  table.find('tr');
            tr.removeClass('playoff-zone');
            for (var i = 1; i <= table.data('playoff'); i++) {
                tr.eq(i).addClass('playoff-zone');
            }
            item.removeClass(container.group.options.draggedClass).removeAttr("style");
            $("body").removeClass(container.group.options.bodyClass);
        }
    });
};
