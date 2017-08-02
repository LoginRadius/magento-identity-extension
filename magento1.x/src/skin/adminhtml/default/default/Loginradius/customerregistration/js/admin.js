var $loginRadiusJquery = jQuery.noConflict();
$loginRadiusJquery(document).ready(function () {
    customerregistrationCustomUrl('login');
    
    $loginRadiusJquery('#customerregistration_settings_redirectionafterlogin').change(function(){customerregistrationCustomUrl('login')});
    $loginRadiusJquery('#customerregistration_othersettings_emailVerified').change(function(){emailVerifiedOption();});
    if($loginRadiusJquery('#googleanalytics_settings_enable').val() == '1'){
        $loginRadiusJquery('#row_googleanalytics_settings_enableuserlogin').show();
    }
    $loginRadiusJquery('#system_config_tabs').find('span').each(function () {
        if ($loginRadiusJquery(this).text().replace(/\s/g, "") == 'SocialLogin') {
            $loginRadiusJquery(this).parents('dd').hide();
        }
    });
    lrCheckValidJson();
    $loginRadiusJquery('#customerregistration_socialinterface_formrenderdelay,#customerregistration_socialinterface_minpasswordlength,#customerregistration_socialinterface_maxpasswordlength').prop("type", "number");
setTimeout(function(){
    emailVerifiedOption();
},1000);


});
function lrCheckValidJson() {
    $loginRadiusJquery('#customerregistration_othersettings_customraasoptions').blur(function(){
        var profile = $loginRadiusJquery('#customerregistration_othersettings_customraasoptions').val();
        var response = '';
        try
        {
            response = $loginRadiusJquery.parseJSON(profile);
            if(response != true && response != false){
                var validjson = JSON.stringify(response, null, '\t').replace(/</g, '&lt;');
                if(validjson != 'null'){
                    $loginRadiusJquery('#customerregistration_othersettings_customraasoptions').val(validjson);
                    $loginRadiusJquery('#customerregistration_othersettings_customraasoptions').css("border","1px solid green");
                }else{
                    $loginRadiusJquery('#customerregistration_othersettings_customraasoptions').css("border","1px solid red");
                }
            }
            else{
                $loginRadiusJquery('#customerregistration_othersettings_customraasoptions').css("border","1px solid green");
            }
        } catch (e)
        {
            $loginRadiusJquery('#customerregistration_othersettings_customraasoptions').css("border","1px solid green");
        }
    });
}
function customerregistrationCustomUrl(action){
    var url = $loginRadiusJquery('#customerregistration_settings_redirectionafter'+action).val();
    if(url == 'custom'){
        $loginRadiusJquery('#row_customerregistration_settings_redirectionafter'+action+'custom').show();
    }else{
        $loginRadiusJquery('#row_customerregistration_settings_redirectionafter'+action+'custom').hide();
    }
}

function emailVerifiedOption(){
    var emailVerified = $loginRadiusJquery('#customerregistration_othersettings_emailVerified').val();
    if(emailVerified == '2'){
        $loginRadiusJquery('#row_customerregistration_othersettings_askemailalwaysforunverified,#row_customerregistration_othersettings_emailverificationtemplate,#row_customerregistration_othersettings_loginonemailverification,#row_customerregistration_othersettings_sociallinking,#row_customerregistration_othersettings_loginonemailverification').show();
        $loginRadiusJquery('#row_customerregistration_othersettings_passwordonsociallogin,#row_customerregistration_othersettings_enableusername').hide();
    }else if(emailVerified == '1'){
        $loginRadiusJquery('#row_customerregistration_othersettings_passwordonsociallogin,#row_customerregistration_othersettings_emailverificationtemplate,#row_customerregistration_othersettings_askemailalwaysforunverified,#row_customerregistration_othersettings_loginonemailverification,#row_customerregistration_othersettings_sociallinking,#row_customerregistration_othersettings_loginonemailverification,#row_customerregistration_othersettings_enableusername').hide();
    }else{
        $loginRadiusJquery('#row_customerregistration_othersettings_passwordonsociallogin,#row_customerregistration_othersettings_emailverificationtemplate,#row_customerregistration_othersettings_askemailalwaysforunverified,#row_customerregistration_othersettings_loginonemailverification,#row_customerregistration_othersettings_sociallinking,#row_customerregistration_othersettings_loginonemailverification,#row_customerregistration_othersettings_enableusername').show();
    }
}
