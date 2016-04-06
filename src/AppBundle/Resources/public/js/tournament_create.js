$(document).ready(function() {

    // Player select in randomizer
    $('#tournament_form_users').select2();

    // Get func with scope for adding team
    var addTeamFunc = addTeam;

    $('.add-participant').click(addTeamFunc());
});

var addTeam = function () {
    var teamNo = 0;
    // Select element with players
    var selectPlayers = $('#tournament_form_users');

    return function() {
        teamNo++;
        var container = $('<div class="col-md-4"></div>');
        var panel = $('<div class="panel panel-default"></div>');
        var panelBody = $('<div class="panel-body"></div>');
        var close = $('<p class="remove-icon remove-icon-'+teamNo+' glyphicon glyphicon-remove"></p>');

        selectPlayers.find(':selected').each(function(){
            var name = $(this).html();
            var value = $(this).val();
            var span = $('<p>'+name+'</p>');
            $(this).remove();

            var input = $('<input class="tournament-team" data-name="'+name+'" type="hidden" name="team'+teamNo+'[]" value="'+value+'">');

            container.append(input);
            panelBody.append(span);
        });

        // Clear select element
        selectPlayers.val(null).trigger("change");
        // Update html elements
        panel.append(close);
        panel.append(panelBody);
        container.append(panel);
        $('.participant-list').append(container);
        // Init js remove team for click
        initRemoveIcon(teamNo);
    }
};

var initRemoveIcon = function(i){
    $('.participant-list .remove-icon-' + i ).click(function(){
        // Find each player and move his to select player
        $(this).closest('.col-md-4').find('input.tournament-team').each(function(){
            var option = $('<option value="'+$(this).val()+'">'+$(this).data('name')+'</option>');
            $('#tournament_form_users').append(option);
        });
        // Remove selected team
        $(this).closest('.col-md-4').remove();
    });
};