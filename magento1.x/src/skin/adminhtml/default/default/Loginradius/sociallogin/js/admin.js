var $loginRadiusJquery = jQuery.noConflict();
$loginRadiusJquery(document).ready(function () {
    socialloginCustomUrl('login');
    socialloginCustomUrl('registration');
    $loginRadiusJquery('#sociallogin_settings_redirectionafterlogin').change(function(){socialloginCustomUrl('login')});
    $loginRadiusJquery('#sociallogin_settings_redirectionafterregistration').change(function(){socialloginCustomUrl('registration')});
    if($loginRadiusJquery('#googleanalytics_settings_enable').val() == '1'){
        $loginRadiusJquery('#row_googleanalytics_settings_enableuserlogin').show();
    }
});
function socialloginCustomUrl(action){
    var url = $loginRadiusJquery('#sociallogin_settings_redirectionafter'+action).val();
    if(url == 'custom'){
        $loginRadiusJquery('#row_sociallogin_settings_redirectionafter'+action+'custom').show();
    }else{
        $loginRadiusJquery('#row_sociallogin_settings_redirectionafter'+action+'custom').hide();
    }
}