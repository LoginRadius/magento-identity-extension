require(['jquery', 'jquery/ui'], function ($) {
    $(document).ready(function () {
        if ($('#system_config_tabs').find('li._active').text().toLowerCase().replace(/\s/g, "") != 'socialshare') {
            return;
        }
        $('#row_lrsocialshare_horizontal_share_counter_provider_theme, #row_lrsocialshare_vertical_share_counter_provider_theme').hide();
        horizontalShareTheme();
        verticalShareTheme();
        shareProviderClick('horizontal', 'share');
        shareProviderClick('vertical', 'share');
        shareProviderClick('horizontal', 'count');
        shareProviderClick('vertical', 'count');
        $("#lrsocialshare_horizontal_share_rearrange_icons").after('<div id="rearrangecontainerhorizontal"></div>');
        $("#lrsocialshare_vertical_share_rearrange_icons").after('<div id="rearrangecontainervertical"></div>');
        rearrangeProviders('horizontal');
        rearrangeProviders('vertical');
        popupWidthToggle()
        $("#lrsocialshare_advance_setting_custom_popup").change(function () {
            popupWidthToggle();
        });
        customOptionCheckValidJson();
        $('#row_lrsocialshare_horizontal_share_loginRadiushorizontalerrordiv,#row_lrsocialshare_vertical_share_loginRadiusverticalerrordiv').html('<div class="errorShareDiv">You can select only 9 providers.</div>');
        $('#row_lrsocialshare_horizontal_share_loginRadiushorizontalerrordiv,#row_lrsocialshare_vertical_share_loginRadiusverticalerrordiv').hide();
        $('.lr-vertical-interface-32').parent().parent().css("margin-top", "6px");
    });
    function popupWidthToggle() {
        if ($('#lrsocialshare_advance_setting_custom_popup').val() == '1') {
            $('#row_lrsocialshare_advance_setting_popup_height, #row_lrsocialshare_advance_setting_popup_width').show();
        } else {
            $('#row_lrsocialshare_advance_setting_popup_height, #row_lrsocialshare_advance_setting_popup_width').hide();
        }
    }
    function shareProviderClick(theme, type) {
        var shareProvider = $('#row_lrsocialshare_' + theme + '_share_' + type + '_provider .value');
        if (shareProvider == '') {
            if (type == 'share') {
                shareProvider = 'facebook|googleplus|twitter|pinterest|email|print';
            } else if (type == 'count') {
                shareProvider = 'facebook-like|facebook-recommend|facebook-send|twitter-tweet|pinterest-pin-it';
            }
            shareProvider = shareProvider.split("|");
        }

        for (var i = 0; i < shareProvider.length; i++) {
            var shareProviderTag = shareProvider[i].getElementsByTagName('input');
            for (var j = 0; j < shareProviderTag.length; j++) {
                shareProviderTag[j].addEventListener("click", function (event) {
                    var eventId = event.target.id;
                    var provider = eventId.replace('lrsocialshare_' + theme + '_share_' + type + '_provider_', '');
                    loginRadiusSharingLimit(provider, theme, type);
                });
            }
        }
        socialProvidersChecked(theme, type);
    }
    function customOptionCheckValidJson() {
        var addCustomOption = $('#lrsocialshare_advance_setting_custom_option');
        addCustomOption.blur(function () {
            var profile = addCustomOption.val();
            var response = '';
            try
            {
                response = $.parseJSON(profile);
                if (response != true && response != false) {
                    var validjson = JSON.stringify(response, null, '\t').replace(/</g, '&lt;');
                    if (validjson != null && validjson != 'null') {
                        addCustomOption.val(validjson);
                        addCustomOption.css("border", "1px solid green");
                    } else {
                        addCustomOption.css("border", "1px solid red");
                    }
                }
                else {
                    addCustomOption.css("border", "1px solid green");
                }
            } catch (e)
            {
                addCustomOption.css("border", "1px solid green");
            }
        });
    }
    function shareProviderChanged(theme, type) {
        var provider = '';
        var shareProvider = $('#row_lrsocialshare_' + theme + '_share_' + type + '_provider .value');
        if (shareProvider == '') {
            if (type == 'share') {
                shareProvider = 'facebook|googleplus|twitter|pinterest|email|print';
            } else if (type == 'count') {
                shareProvider = 'facebook-like|facebook-recommend|facebook-send|twitter-tweet|pinterest-pin-it';
            }
            shareProvider = shareProvider.split("|");
        }
        for (var i = 0; i < shareProvider.length; i++) {
            var shareProviderTag = shareProvider[i].getElementsByTagName('input');
            for (var j = 0; j < shareProviderTag.length; j++) {
                if (shareProviderTag[j].checked == true) {
                    provider += shareProviderTag[j].value + '|';
                }
            }
        }
        if (type == 'share') {
            $('#lrsocialshare_' + theme + '_share_rearrange_icons').val(provider.slice(0, -1));
            rearrangeProviders(theme);
        } else if (type == 'count') {
            $('#lrsocialshare_' + theme + '_share_counter_provider_theme').val(provider.slice(0, -1));
        }
    }

    function rearrangeProviders(type) {
        var providerShow = $('#lrsocialshare_' + type + '_share_rearrange_icons').val();
        if (providerShow == '') {
            providerShow = 'facebook|googleplus|twitter|pinterest|email|print';
        }
        var providers = providerShow.split('|');
        var output = '<div class="lrsharecontainer" >';
        output += '<ul id="' + type + 'sortable" class="ui-sortable" style="float:left; margin-left:0px;">';
        for (var i = 0; i < providers.length; i++) {
            output += '<li title="' + providers[i] + '" id="oss' + type + '_' + providers[i] + '" class="lr_iconsprite32 lr_' + providers[i] + ' dragcursor"></li>';
        }
        output += '</ul><div style="clear:both;"></div></div>';
        $('#rearrangecontainer' + type).html(output);
        $('#' + type + 'sortable').sortable({
            revert: true,
            stop: function () {
                var provider = "";
                $(this).children('li').each(function () {
                    provider += $(this).attr('title') + "|";
                });
                provider = provider.slice(0, -1);
                $('#lrsocialshare_' + type + '_share_rearrange_icons').val(provider);
            }
        });
    }

    function socialProvidersChecked(theme, type) {
        if (type == 'share') {
            var horizontalRearrange = $('#lrsocialshare_' + theme + '_share_rearrange_icons').val();
            if (horizontalRearrange == '') {
                horizontalRearrange = 'facebook|googleplus|twitter|pinterest|email|print';
            }
            rearrangeProviders(theme);
        } else if (type == 'count') {
            var horizontalRearrange = $('#lrsocialshare_' + theme + '_share_counter_provider_theme').val();
            if (horizontalRearrange == '') {
                horizontalRearrange = 'facebook-like|facebook-recommend|facebook-send|twitter-tweet|pinterest-pin-it';
            }

        }
        horizontalRearrange = horizontalRearrange.split("|");
        for (var i = 0; i < horizontalRearrange.length; i++) {
            var provider = horizontalRearrange[i].toLowerCase().replace(/\s/g, "-");
            if (provider != '') {
                $('#lrsocialshare_' + theme + '_share_' + type + '_provider_' + provider).attr('checked', 'checked');
            }
        }
    }

    function horizontalShareTheme() {
        var shareTheme = $('#row_lrsocialshare_horizontal_share_theme .value');
        for (var i = 0; i < shareTheme.length; i++) {
            var shareThemeTag = shareTheme[i].getElementsByTagName('input');
            for (var j = 0; j < shareThemeTag.length; j++) {
                var shareThemeInput = shareThemeTag[j];
                shareThemeInput.addEventListener("click", horizontalShareThemeChanged);
                if (shareThemeInput.checked == true) {
                    if (shareThemeInput.value == '' || shareThemeInput.value == '0' || shareThemeInput.value == '1' || shareThemeInput.value == '2') {
                        $('#row_lrsocialshare_horizontal_share_share_provider,#row_lrsocialshare_horizontal_share_rearrange_icons').show();
                        $('#row_lrsocialshare_horizontal_share_count_provider').hide();
                        shareProviderClick('horizontal', 'share');

                    } else if (shareThemeInput.value == '3' || shareThemeInput.value == '4') {
                        $('#row_lrsocialshare_horizontal_share_share_provider, #row_lrsocialshare_horizontal_share_count_provider, #row_lrsocialshare_horizontal_share_rearrange_icons').hide();
                    } else if (shareThemeInput.value == '5' || shareThemeInput.value == '6') {
                        $('#row_lrsocialshare_horizontal_share_share_provider, #row_lrsocialshare_horizontal_share_rearrange_icons').hide();
                        $('#row_lrsocialshare_horizontal_share_count_provider').show();
                        shareProviderClick('horizontal', 'count');
                    }
                }
            }
        }
    }
    function verticalShareTheme() {
        var shareTheme = $('#row_lrsocialshare_vertical_share_theme .value');
        for (var i = 0; i < shareTheme.length; i++) {
            var shareThemeTag = shareTheme[i].getElementsByTagName('input');
            for (var j = 0; j < shareThemeTag.length; j++) {
                var shareThemeInput = shareThemeTag[j];
                shareThemeInput.addEventListener("click", verticalShareThemeChanged);
                if (shareThemeInput.checked == true) {
                    if (shareThemeInput.value == '' || shareThemeInput.value == '0' || shareThemeInput.value == '1') {
                        $('#row_lrsocialshare_vertical_share_share_provider, #row_lrsocialshare_vertical_share_rearrange_icons').show();
                        $('#row_lrsocialshare_vertical_share_count_provider').hide();
                        shareProviderClick('vertical', 'share');
                    } else if (shareThemeInput.value == '2' || shareThemeInput.value == '3') {
                        $('#row_lrsocialshare_vertical_share_share_provider, #row_lrsocialshare_vertical_share_rearrange_icons').hide();
                        $('#row_lrsocialshare_vertical_share_count_provider').show();
                        shareProviderClick('vertical', 'count');
                    }
                }
            }
        }
    }
    function horizontalShareThemeChanged() {
        horizontalShareTheme();
    }
    function verticalShareThemeChanged() {
        verticalShareTheme();
    }

    function loginRadiusSharingLimit(provider, theme, type) {
        var checkCount = 0;
        // get providers table-row reference
        var loginRadiusSharingProvidersRow = document.getElementById('row_lrsocialshare_' + theme + '_share_share_provider');
        // get sharing providers checkboxes reference
        var loginRadiusSharingProviders = loginRadiusSharingProvidersRow.getElementsByTagName('input');
        for (var i = 0; i < loginRadiusSharingProviders.length; i++) {
            if (loginRadiusSharingProviders[i].checked) {
                // count checked providers
                checkCount++;
                if (checkCount > 9) {
                    $('#row_lrsocialshare_' + theme + '_share_loginRadius' + theme + 'errordiv').show();
                    $('#lrsocialshare_' + theme + '_share_' + type + '_provider_' + provider).removeAttr('checked');
                    setTimeout(function () {
                        $('#row_lrsocialshare_' + theme + '_share_loginRadius' + theme + 'errordiv').hide();
                    }, 5000);
                    return;
                } else {
                    shareProviderChanged(theme, type);
                }
            }
        }
        if (checkCount == 0) {
            var elms = document.getElementById(theme + 'sortable').getElementsByTagName('li');
            for (var i = 0; i < elms.length; i++) {
                var lastProvider = elms[i].getAttribute('title');
                document.getElementById("lrsocialshare_" + theme + "_share_share_provider_" + lastProvider).checked = true;
            }
            alert('You Can\'t Removed single provider.');
        }
    }

});