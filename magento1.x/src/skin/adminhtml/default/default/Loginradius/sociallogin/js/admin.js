var $loginRadiusJquery = jQuery.noConflict();
$loginRadiusJquery(document).ready(function () {
    socialloginCustomUrl('login');
    socialloginCustomUrl('registration');
    $loginRadiusJquery('#sociallogin_settings_redirectionafterlogin').change(function(){socialloginCustomUrl('login')});
    $loginRadiusJquery('#sociallogin_settings_redirectionafterregistration').change(function(){socialloginCustomUrl('registration')});
    if($loginRadiusJquery('#googleanalytics_settings_enable').val() == '1'){
        $loginRadiusJquery('#row_googleanalytics_settings_enableuserlogin').show();
    }
    hideiconsperrow();
    $loginRadiusJquery('#sociallogin_socialinterface_customenable').change(function(){
        hideiconsperrow();
    });
    hideusernotificationemail();
    $loginRadiusJquery('#sociallogin_emailsettings_notifyUser').change(function(){
        hideusernotificationemail();
    });
    hideownernotificationemail();
    $loginRadiusJquery('#sociallogin_emailsettings_notifyAdmin').change(function(){
        hideownernotificationemail();
    });
});
function socialloginCustomUrl(action){
    var url = $loginRadiusJquery('#sociallogin_settings_redirectionafter'+action).val();
    if(url == 'custom'){
        $loginRadiusJquery('#row_sociallogin_settings_redirectionafter'+action+'custom').show();
    }else{
        $loginRadiusJquery('#row_sociallogin_settings_redirectionafter'+action+'custom').hide();
    }
}
function hideiconsperrow(){
    var customInterface = $loginRadiusJquery('#sociallogin_socialinterface_customenable').val();
    if(customInterface == '1'){
        $loginRadiusJquery('#row_sociallogin_socialinterface_iconsperRow,#row_sociallogin_socialinterface_iconsize,#row_sociallogin_socialinterface_backgroundcolor').hide();
        
    }else{
        $loginRadiusJquery('#row_sociallogin_socialinterface_iconsperRow,#row_sociallogin_socialinterface_iconsize,#row_sociallogin_socialinterface_backgroundcolor').show();
    }
}
function hideusernotificationemail(){
    var userregistrationnotification = $loginRadiusJquery('#sociallogin_emailsettings_notifyUser').val();
    if(userregistrationnotification == '0'){
        $loginRadiusJquery('#row_sociallogin_emailsettings_notifyUserText').hide();
        
    }else{
        $loginRadiusJquery('#row_sociallogin_emailsettings_notifyUserText').show();
    }
}
function hideownernotificationemail(){
    var ownerregistrationnotification = $loginRadiusJquery('#sociallogin_emailsettings_notifyAdmin').val();
    if(ownerregistrationnotification == '0'){
        $loginRadiusJquery('#row_sociallogin_emailsettings_notifyAdminText').hide();
        
    }else{
        $loginRadiusJquery('#row_sociallogin_emailsettings_notifyAdminText').show();
    }
}