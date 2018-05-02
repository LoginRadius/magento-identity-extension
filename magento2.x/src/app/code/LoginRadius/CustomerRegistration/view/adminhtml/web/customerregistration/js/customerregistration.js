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
                $('#lrcustomerregistration_interface_customization_settings-state,#lrcustomerregistration_password_customization_settings-state,#lrcustomerregistration_advance_settings-state,#lrcustomerregistration_email_template_settings-state,#lrcustomerregistration_phone_login_settings,#lrcustomerregistration_two_fa_settings').parent().hide();
            }, 1000);
        } else {
            setTimeout(function () {
                $('#lrcustomerregistration_interface_customization_settings-state,#lrcustomerregistration_password_customization_settings-state,#lrcustomerregistration_advance_settings-state,#lrcustomerregistration_email_template_settings-state,#lrcustomerregistration_phone_login_settings,#lrcustomerregistration_two_fa_settings').parent().show();
            }, 1000);
        }
    }
});