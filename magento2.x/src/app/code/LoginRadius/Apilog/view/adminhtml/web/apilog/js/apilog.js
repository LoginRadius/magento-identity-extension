require(['jquery', 'jquery/ui'], function ($) {

    $(document).ready(function () {
        $('#row_apilog_showapilog_hidden').hide();
        $('#row_apilog_showapilog_log').hide();
        $('#apilog_showapilog-head').hide();
        var showChar = 200;
        var ellipsestext = "...";
        var moretext = "more";
        var lesstext = "less";
        jQuery('.more').each(function () {
            var content = jQuery(this).html();

            if (content.length > showChar) {

                var c = content.substr(0, showChar);
                var h = content.substr(showChar - 1, content.length - showChar);

                var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

                jQuery(this).html(html);
            }

        });

        jQuery(".morelink").click(function () {
            if (jQuery(this).hasClass("less")) {
                jQuery(this).removeClass("less");
                jQuery(this).html(moretext);
            } else {
                jQuery(this).addClass("less");
                jQuery(this).html(lesstext);
            }
            jQuery(this).parent().prev().toggle();
            jQuery(this).prev().toggle();
            return false;
        });
    });
});