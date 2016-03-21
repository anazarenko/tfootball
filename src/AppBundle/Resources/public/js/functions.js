var clearCreateGameForm = function() {
    $('#game_create_firstTeam').val(null).trigger("change");
    $('#game_create_secondTeam').val(null).trigger("change");

    $('#game_create_firstScore').val('');
    $('#game_create_secondScore').val('');

    clearPopupStats();
};