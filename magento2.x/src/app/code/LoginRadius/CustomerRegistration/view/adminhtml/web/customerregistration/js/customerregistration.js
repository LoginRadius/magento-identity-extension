require(['jquery', 'jquery/ui'], function ($) {
    $(document).ready(function () {
        customOptionCheckValidJson();
        raasOptionToggle();
        $("#lrcustomerregistration_enablehostingpage_enable_hosted_page").change(function () {
            raasOptionToggle();
        });
    });
    function raasOptionToggle() {
        if ($('#lrcustomerregistration_enablehostingpage_enable_hosted_page').val() == '1') {
            $('#lrcustomerregistration_email_template_settings-head').parent().parent().hide();
            $('#lrcustomerregistration_interface_customization_settings-head').parent().parent().hide();
            $('#lrcustomerregistration_password_customization_settings').parent().hide();
            $('#row_lrcustomerregistration_advance_settings_username_login').hide();
            $('#row_lrcustomerregistration_advance_settings_email_verification').hide();
            $('#row_lrcustomerregistration_advance_settings_login_upon_email_verification').hide();
            $('#row_lrcustomerregistration_advance_settings_always_ask_email_for_unverified').hide();
            $('#row_lrcustomerregistration_advance_settings_prompt_password_on_social_login').hide();
            $('#row_lrcustomerregistration_advance_settings_custom_js_options').hide();

        } else {
            $('#lrcustomerregistration_email_template_settings-head').parent().parent().show();
            $('#lrcustomerregistration_interface_customization_settings-head').parent().parent().show();
            $('#lrcustomerregistration_password_customization_settings').parent().show();
            $('#row_lrcustomerregistration_advance_settings_username_login').show();
            $('#row_lrcustomerregistration_advance_settings_email_verification').show();
            $('#row_lrcustomerregistration_advance_settings_login_upon_email_verification').show();
            $('#row_lrcustomerregistration_advance_settings_always_ask_email_for_unverified').show();
            $('#row_lrcustomerregistration_advance_settings_prompt_password_on_social_login').show();
            $('#row_lrcustomerregistration_advance_settings_custom_js_options').show();

        }
    }
    function customOptionCheckValidJson() {
        var addCustomOption = $('#lrcustomerregistration_advance_settings_custom_js_options');
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

    $('#lrcustomerregistration_advance_settings_email_verification').change(function () {
        if ($(this).val() == '1') {
            $('#row_lrcustomerregistration_email_template_settings_verification_email').hide();
        } else {
            $('#row_lrcustomerregistration_email_template_settings_verification_email').show();
        }
    });
});