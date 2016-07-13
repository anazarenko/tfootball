$(document).ready(function() {
    applyBtnInit();
    editBtnInit();

    $('.btn-finish-group').click(function(){
        standingInit();
        $('.btn-start-playoff').show();
    });

    // Cache selectors
    var lastId,
        topMenu = $(".tournament-sub-menu"),
        topMenuHeight = topMenu.outerHeight()+15,
    // All list items
        menuItems = topMenu.find("a"),
    // Anchors corresponding to menu items
        scrollItems = menuItems.map(function(){
            var item = $($(this).attr("href"));
            if (item.length) { return item; }
        });

    // Bind click handler to menu items
    // so we can get a fancy scroll animation
    menuItems.click(function(e){
        var href = $(this).attr("href"),
            offsetTop = href === "#" ? 0 : $(href).offset().top-topMenuHeight+1;
        $('html, body').stop().animate({
            scrollTop: offsetTop
        }, 300);
        e.preventDefault();
    });

    // Bind to scroll
    $(window).scroll(function(){

        if ($('body').scrollTop() >= 71) {
            $('.tournament-sub-menu').addClass('fixed');
        } else {
            $('.tournament-sub-menu').removeClass('fixed');
        }

        // Get container scroll position
        var fromTop = $(this).scrollTop()+topMenuHeight;

        // Get id of current scroll item
        var cur = scrollItems.map(function(){
            if ($(this).offset().top < fromTop)
                return this;
        });
        // Get the id of the current element
        cur = cur[cur.length-1];
        var id = cur && cur.length ? cur[0].id : "";

        if (lastId !== id) {
            lastId = id;
            // Set/remove active class
            menuItems
                .parent().removeClass("active")
                .end().filter("[href='#"+id+"']").parent().addClass("active");
        }
    });
});

var applyBtnInit = function() {
    $('.t-match-form .game-submit').click(function () {
        var form = $(this).closest('.t-match-form');

        var isEmpty = false;
        form.find('.game-score-input input').each(function () {
            if (!$(this).val().length) {
                isEmpty = true;
            }
        });

        if (!isEmpty) {

            $.ajax({
                type: "POST",
                url: form.attr('action'),
                data: form.serialize(),
                success: function (response) {
                    if (response.status == 1) {
                        $('#tournament-stat-table').html(response.statistics);
                        $('#tournament-game-list').html(response.games);
                        applyBtnInit();
                        editBtnInit();
                    }
                }
            });

        }
    });
};

var editBtnInit = function(){
    $('.game-edit').click(function(){
        $(this).closest('.game-row').addClass('game-input-active');
    });
};

var standingInit = function (){
    // Sortable rows
    $('.sorted_table').sortable({
        containerSelector: 'table',
        itemPath: '> tbody',
        itemSelector: 'tr',
        placeholder: '<tr class="placeholder"/>',
        onDrop: function (item, container, _super, event) {
            var table = $('.table-tournament');
            var tr =  table.find('tr');
            tr.removeClass('playoff-zone');
            for (var i = 1; i <= tr.length; i++) {
                // Add row number
                tr.eq(i).find('td').eq(0).html(i);
                tr.eq(i).find('.input-position').val(i);
                // Add play off zone class
                if (i <= table.data('playoff')) {
                    tr.eq(i).addClass('playoff-zone');
                }
            }
            item.removeClass(container.group.options.draggedClass).removeAttr("style");
            $("body").removeClass(container.group.options.bodyClass);
        }
    });
};
