(function ($) {
    'use strict';
    $(document).ready(function () {
        var tsvg_card_zindex = 10;
        $(document).on("click", "div.tsvg_addon_card", function (e) {
            if ($(".tsvg_addon_card-actions .tsvg_addon_btn").is(e.target)) {
                return;
            }
            e.preventDefault();
            var tsvgIsShowing = false;
            if ($(this).hasClass("tsvg_show")) {
                tsvgIsShowing = true
            }
            if ($("div.tsvg_addon_cards").hasClass("tsvg_showing")) {
                $("div.tsvg_addon_card.tsvg_show").removeClass("tsvg_show");
                if (tsvgIsShowing) {
                    $("div.tsvg_addon_cards").removeClass("tsvg_showing");
                } else {
                    $(this).css({ zIndex: tsvg_card_zindex }).addClass("tsvg_show");
                }
                tsvg_card_zindex++;
            } else {
                $("div.tsvg_addon_cards").addClass("tsvg_showing");
                $(this).css({ zIndex: tsvg_card_zindex }).addClass("tsvg_show");
                tsvg_card_zindex++;
            }
        });
    });
})(jQuery);