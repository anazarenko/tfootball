$(document).ready(function() {

    // Player select in randomizer
    $('#randomizer_players').select2();

    $('.random-btn').click(function(){
        var players = [];
        var firstTeam = [];
        var secondTeam = [];
        var firstTeamSelect = $('#game_create_firstTeam');
        var secondTeamSelect = $('#game_create_secondTeam');
        $('#randomizer_players :selected').each(function(){
            players.push($(this).val());
        });

        clearCreateGameForm();

        var message = $('.randomizer-message');
        message.html('');

        if (players.length % 2 == 0) {
            players = shuffle(players);
            for (var i = 0; i < players.length / 2; i++) {
                firstTeam.push(players[i]);
            }
            for (var j = players.length / 2; j < players.length; j++) {
                secondTeam.push(players[j]);
            }
            firstTeamSelect.val(firstTeam).trigger("change");
            secondTeamSelect.val(secondTeam).trigger("change");
            $('.create-game-popup').modal('show');
        } else {
            message.html('Error');
        }
    });

});

function shuffle(a) {
    var j, x, i;
    for (i = a.length; i; i -= 1) {
        j = Math.floor(Math.random() * i);
        x = a[i - 1];
        a[i - 1] = a[j];
        a[j] = x;
    }

    return a;
}