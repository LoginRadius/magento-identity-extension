require(['jquery', 'jquery/ui'], function ($) {
    $(document).ready(function () {
        hostedPageToggle();
        $("#lrcustomerregistration_redirection_settings_enable_hosted_page").change(function () {
            hostedPageToggle();
        });
    });
    function hostedPageToggle() {
        if ($('#lrcustomerregistration_redirection_settings_enable_hosted_page').val() == '1') {
            setTimeout(function () {
                $('#lrcustomerregistration_interface_customization_settings-state,#lrcustomerregistration_password_customization_settings-state,#lrcustomerregistration_advance_settings-state,#lrcustomerregistration_email_template_settings-state').parent().hide();
            }, 1000);
        } else {
            setTimeout(function () {
                $('#lrcustomerregistration_interface_customization_settings-state,#lrcustomerregistration_password_customization_settings-state,#lrcustomerregistration_advance_settings-state,#lrcustomerregistration_email_template_settings-state').parent().show();
            }, 1000);
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
                } else {
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