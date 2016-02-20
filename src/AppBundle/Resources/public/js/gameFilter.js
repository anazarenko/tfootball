$(document).ready(function() {

    // Player select in game filter
    $('#game_filter_firstTeam').select2();
    $('#game_filter_secondTeam').select2();

    // Date filter
    var dateFilter = $('#date-filter');
    dateFilter.daterangepicker({
        locale: {
            format: 'DD.MM.YYYY',
            cancelLabel: 'Clear'
        }
    });
    dateFilter.on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    dateFilter.data('daterangepicker').setStartDate($('input[name="startDate"]').val());
    dateFilter.data('daterangepicker').setEndDate($('input[name="endDate"]').val());

    // Game filters
    $('#game-filters h4').click(function(){
        $(this).parent().toggleClass('open');
    });

});
