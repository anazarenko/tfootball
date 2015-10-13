$(document).ready(function() {

    var createGameForm = $('.create-game-form');

    var notify = $('#notify');
    var notifyClose = $('#notify .close');

    notifyClose.click(function(){
        notify.removeClass('show');
    });

    // process the form
    createGameForm.submit(function(event) {

        var submitBtn = $('#createGame');
        var errorField = $('.form-error');

        var values = {};
        $.each( createGameForm.serializeArray(), function(i, field) {
            values[field.name] = field.value;
        });

        errorField.html('');
        submitBtn.button('loading');

        $.ajax({
            type        : 'POST',
            url         : createGameForm.attr('action'),
            data        : values,
            dataType    : 'json'
        }).success(function(data){
            console.log(data);
            if (data.status == 0) {
                errorField.html(data.error);
            } else {
                $('.create-game-popup').modal('hide');
                createGameForm.trigger("reset");
                errorField.html('');
                notify.addClass('show');
                setTimeout(function(){
                    notify.removeClass('show');
                }, 4000);
            }
            submitBtn.button('reset');
        });

        event.preventDefault();
    });

    $('.dropdown-menu').click(function(e) {
        e.stopPropagation();
    });

    $('.notify-btn').click(function(){

        var notifies = $('.notify-item').length;
        var btn = $(this);
        btn.button('loading');

        $.ajax({
            type        : 'POST',
            url         : $(this).data('href'),
            dataType    : 'json'
        }).success(function(data){
            if (data.status == 0) {
                btn.button('reset');
            } else {
                btn.closest('.notify-item').remove();
                notifies--;
                if (notifies == 0) {
                    $('.dropdown-menu').append('<li class="notify-item">' +
                    '<div>No matches for confirmation</div>' +
                    '</li>');
                    $('.game-notify').removeClass('notify-active');
                }
            }
        });
    });

});