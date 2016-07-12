(function ($) {

    var settings = {
        applyBtnSelector: ".game-submit",
        scoreContainerSelector: ".game-score-input"
    };

    $.fn.tournamentMath = function(options) {
        settings = $.extend(settings, options);

        this.each(function(){
            applyBtnInit($(this));
        });

        return this;
    };

    var applyBtnInit = function(form){
        form.find(settings.applyBtnSelector).click(function(){
            var isEmpty = false;
            form.find(settings.scoreContainerSelector + ' input').each(function(){
                if (!$(this).val().length) {
                    isEmpty = true;
                }
            });

            if (!isEmpty) {

                $.ajax({
                    type: "POST",
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(response) {
                        if (response.status == 1) {
                            $('.table-tournament tbody').html(response.statistics);
                            $('#tournament .main-game-list').html(response.games);
                        }
                    }
                });

            }
        });
    };

}(jQuery));
