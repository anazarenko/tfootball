$(document).ready(function() {
    $('.status-change>li').click(function () {
        var li = $(this);
        var url = $(this).parent().data('href');
        var status = $(this).data('status');
        $.ajax({
            type: "POST",
            url: url,
            data: {'status' : status},
            dataType: "json",
            success: function (data) {
                console.log(data);
                if (data.status == 1) {
                    var changeBtn = li.closest('.dropdown').find('.confirm');
                    var label = li.closest('tr').find('.label');

                    changeBtn.removeClass(data.oldConfirmStatus + '-status');
                    changeBtn.addClass(data.newConfirmStatus + '-status');
                    console.log(label);
                    label.html(data.newGameStatus.charAt(0).toUpperCase() + data.newGameStatus.substr(1));
                    label.removeClass('label-danger').andSelf().removeClass('label-info').andSelf().removeClass('label-success');
                    if (data.newGameStatus == 'new') {
                        label.addClass('label-info')
                    } else if (data.newGameStatus == 'confirmed') {
                        label.addClass('label-success')
                    } else if (data.newGameStatus == 'rejected') {
                        label.addClass('label-danger')
                    }
                }
            }
        });
    });
});
