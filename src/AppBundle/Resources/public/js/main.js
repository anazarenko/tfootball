$(document).ready(function() {

    // Best player tabs
    $('#main-page-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show')
    });

    // Best player period
    $('#days').change(function(){

        var loader = $('.loader-area');
        loader.show();

        $.ajax({
            type: 'POST',
            url: $(this).data('href'),
            data: {days: $(this).val()},
            dataType: 'json'
        }).success(function (data) {
            if (data.status == 1) {
                $('#single').html(data.single);
                $('#double').html(data.double);
            } else {
                console.log(data);
            }
        }).always(function(){
            loader.hide();
        });

    });

});