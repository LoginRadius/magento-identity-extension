require(['jquery', 'jquery/ui'], function ($) {
    $(document).ready(function () {
        customOptionCheckValidJson();
        raasOptionToggle();
        $("#lrcustomerregistration_enablehostingpage_enable_hosted_page").change(function () {
            raasOptionToggle();
        });
        lrAuthType();
        $('#lrcustomerregistration_redirection_settings_raas_enable').change(function () {
            lrAuthType();
        });
    });
    function raasOptionToggle() {
        if ($('#lrcustomerregistration_enablehostingpage_enable_hosted_page').val() == '1') {
            $('#lrcustomerregistration_email_template_settings-head').parent().parent().hide();
            $('#lrcustomerregistration_interface_customization_settings-head').parent().parent().hide();
            $('#lrcustomerregistration_password_customization_settings').parent().hide();
            $('#row_lrcustomerregistration_advance_settings_username_login,#row_lrcustomerregistration_advance_settings_email_verification,#row_lrcustomerregistration_advance_settings_login_upon_email_verification,#row_lrcustomerregistration_advance_settings_always_ask_email_for_unverified,#row_lrcustomerregistration_advance_settings_prompt_password_on_social_login,#row_lrcustomerregistration_advance_settings_custom_js_options').hide();
        } else {
            $('#lrcustomerregistration_email_template_settings-head').parent().parent().show();
            $('#lrcustomerregistration_interface_customization_settings-head').parent().parent().show();
            $('#lrcustomerregistration_password_customization_settings').parent().show();
            $('#row_lrcustomerregistration_advance_settings_username_login,#row_lrcustomerregistration_advance_settings_email_verification,#row_lrcustomerregistration_advance_settings_login_upon_email_verification,#row_lrcustomerregistration_advance_settings_always_ask_email_for_unverified,#row_lrcustomerregistration_advance_settings_prompt_password_on_social_login,#row_lrcustomerregistration_advance_settings_custom_js_options').show();

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

    function lrAuthType() {

        if ($('#lrcustomerregistration_redirection_settings_raas_enable').val() != '1') {
            $('#lrcustomerregistration_interface_customization_settings-state,#lrcustomerregistration_password_customization_settings-state,#lrcustomerregistration_email_template_settings-state,#lrcustomerregistration_enablehostingpage-state').parent().hide();
            setTimeout(function () {
                $('#row_lrcustomerregistration_advance_settings_username_login,#row_lrcustomerregistration_advance_settings_email_verification,#row_lrcustomerregistration_advance_settings_login_upon_email_verification,#row_lrcustomerregistration_advance_settings_always_ask_email_for_unverified,#row_lrcustomerregistration_advance_settings_prompt_password_on_social_login,#row_lrcustomerregistration_advance_settings_custom_js_options').hide();
            }, 1000);
        } else {
            $('#lrcustomerregistration_interface_customization_settings-state,#lrcustomerregistration_password_customization_settings-state,#lrcustomerregistration_email_template_settings-state,#lrcustomerregistration_enablehostingpage-state').parent().show();
            setTimeout(function () {
                $('#row_lrcustomerregistration_advance_settings_username_login,#row_lrcustomerregistration_advance_settings_email_verification,#row_lrcustomerregistration_advance_settings_login_upon_email_verification,#row_lrcustomerregistration_advance_settings_always_ask_email_for_unverified,#row_lrcustomerregistration_advance_settings_prompt_password_on_social_login,#row_lrcustomerregistration_advance_settings_custom_js_options').show();
            }, 1000);
        }

    }
});