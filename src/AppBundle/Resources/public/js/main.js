$(document).ready(function() {

    var createGameForm = $('.create-game-form');

    var notify = $('#notify');
    var notifyClose = $('#notify .close');

    notifyClose.click(function(){
        notify.removeClass('show');
    });

    $('#game_create_firstTeam').select2();
    $('#game_create_secondTeam').select2();

     //process the form
    createGameForm.submit(function(event) {

        var submitBtn = $('#createGame');
        var errorField = $('.form-error');

        var firstTeam = [];
        $('#game_create_firstTeam :selected').each(function(i, selected){
            firstTeam[i] = $(selected).val();
        });
        var secondTeam = [];
        $('#game_create_secondTeam :selected').each(function(i, selected){
            secondTeam[i] = $(selected).val();
        });

        var values = {};
        $.each( createGameForm.serializeArray(), function(i, field) {
            console.log(field.name);
            if (field.name == 'game_create[firstTeam][]') {
                values[field.name] = firstTeam;
            } else if (field.name == 'game_create[secondTeam][]') {
                values[field.name] = secondTeam;
            } else {
                values[field.name] = field.value;
            }
        });

        errorField.html('');
        submitBtn.button('loading');

        $.ajax({
            type        : 'POST',
            url         : createGameForm.attr('action'),
            data        : values,
            dataType    : 'json'
        }).success(function(data){
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
        }).always(function () {
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
            if (data.status == 1) {
                btn.closest('.notify-item').remove();
                notifies--;
                if (notifies == 0) {
                    $('.dropdown-menu').append('<li class="notify-item">' +
                    '<div>No matches for confirmation</div>' +
                    '</li>');
                    $('.game-notify').removeClass('notify-active');
                }
            } else {
                console.log(data);
            }
        }).always(function(){
            btn.button('reset');
        });
    });

});