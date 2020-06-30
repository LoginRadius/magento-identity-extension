require(['jquery', 'jquery/ui'], function ($) {
    $(document).ready(function () {
        $('#lractivation_activationhelp,#lractivation_activationhelp-head').hide();
        $('#lractivation_activation_site_secret').after('<div id="show_and_hide_secret">Show</div>');
       

    if (typeof secretkey !== 'undefined') {
    jQuery("#lractivation_activation_site_secret").val(secretkey);
    }
    
    if (typeof phoneLoginEnabled !== 'undefined' && phoneLoginEnabled == false) {        
        jQuery("#lrauthentication_phone_authentication_settings-head").hide();
        jQuery("#lrauthentication_phone_authentication_settings").hide();
        jQuery("#row_lradvancedsettings_advanced_settings_passwordless_otp_login").hide();
        jQuery("#row_lradvancedsettings_advanced_settings_passwordless_otp_login_template").hide();
    }

    $( "#row_lradvancedsettings_advanced_settings_custom_js_options" ).after( "<tr><td></td><td><span>Insert custom option like commonOptions.usernameLogin = true;</span></td></tr>" );

    $('#row_lractivation_activation_site_api').before('<tr id="row_lractivation_activation_toottip"><td></td><td><div class="toottip" style="margin-left: -170px;">To access the loginradius web service please enter the credentials below ( <a href="https://www.loginradius.com/docs/api/v2/admin-console/platform-security/api-key-and-secret/" target="_blank">How to get it?</a> )</div></td></tr>');

    if( jQuery('#messages').length ) 
    {
        if( jQuery(".message-error").length != 0){
        jQuery('.message-success').hide();
        }
    }

    jQuery("#lradvancedsettings_advanced_settings_notification_time_out").prop("type",'number');

    jQuery("#save").click(function(event){                   
        var schema = jQuery('#lradvancedsettings_advanced_settings_registration_form_schema').val();
        var response = '';
        try
        {
            response = jQuery.parseJSON(schema);
            if (response != true && response != false) {
                var validjson = JSON.stringify(response, null, '\t').replace(/</g, '&lt;');
                if (validjson != 'null') {
                    jQuery('#lradvancedsettings_advanced_settings_registration_form_schema').val(validjson);
                    jQuery('#lradvancedsettings_advanced_settings_registration_form_schema').css("border", "1px solid green");
                } else {
                    jQuery('#lradvancedsettings_advanced_settings_registration_form_schema').css("border", "1px solid green");
                }
            } else {
                jQuery('#lradvancedsettings_advanced_settings_registration_form_schema').css("border", "1px solid green");
            }
        } catch (error)
        {      
            event.stopImmediatePropagation();       
            jQuery('#lradvancedsettings_advanced_settings_registration_form_schema').css("border", "1px solid red");
            jQuery('#row_lradvancedsettings_advanced_settings_registration_form_schema').after( '<tr><td></td><td><span style="color:red;">Please enter a valid json value.</span></td></tr>' );
            return false;            
        }
    });
        
    jQuery("#show_and_hide_secret").click(function () {
        if(jQuery("#lractivation_activation_site_secret").prop("type") == 'password'){
            jQuery("#lractivation_activation_site_secret").prop("type",'text');       
            jQuery("#show_and_hide_secret").text("Hide");       
        }else{
            jQuery("#lractivation_activation_site_secret").prop("type",'password');
            jQuery("#show_and_hide_secret").text("Show"); 
        }
      });

    });    
});