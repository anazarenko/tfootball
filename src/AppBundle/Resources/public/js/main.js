$(document).ready(function() {

    $('#main-page-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show')
    })

});