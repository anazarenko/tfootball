$(document).ready(function() {
    $('.user-modal').click(function () {
        var url = $(this).data('href');
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            success: function (data) {
                $('#dashboard').append(data);
                var userModal = $('#userModal');
                userModal.modal('show');
                userModal.on('hidden.bs.modal', function (e) {
                    $(this).remove();
                });
            }
        });
    });
});
