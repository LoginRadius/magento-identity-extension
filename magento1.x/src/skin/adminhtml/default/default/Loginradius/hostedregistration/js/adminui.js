var $loginRadiusJquery = jQuery.noConflict();

$loginRadiusJquery(document).ready(function () {
    $loginRadiusJquery('#row_customerregistration_settings_hostedpage').show();
    enableHostedPage();
    $loginRadiusJquery('#customerregistration_settings_hostedpage').change(function () {
        enableHostedPage();
    });
});
function enableHostedPage() {
    if ($loginRadiusJquery('#customerregistration_settings_hostedpage').val() == '0') {
        $loginRadiusJquery('#customerregistration_socialinterface-head').parent().parent().show();
        $loginRadiusJquery('#row_customerregistration_othersettings_enableusername,#row_customerregistration_othersettings_emailVerified,#row_customerregistration_othersettings_emailVerified,#row_customerregistration_othersettings_loginonemailverification,#row_customerregistration_othersettings_askemailalwaysforunverified,#row_customerregistration_othersettings_passwordonsociallogin,#row_customerregistration_othersettings_customraasoptions,#row_customerregistration_othersettings_emailtemplateheading,#row_customerregistration_othersettings_forgotpasswordtemplate,#row_customerregistration_othersettings_emailverificationtemplate').show();
    } else {
        $loginRadiusJquery('#customerregistration_socialinterface-head').parent().parent().hide();
        $loginRadiusJquery('#row_customerregistration_othersettings_enableusername,#row_customerregistration_othersettings_emailVerified,#row_customerregistration_othersettings_emailVerified,#row_customerregistration_othersettings_loginonemailverification,#row_customerregistration_othersettings_askemailalwaysforunverified,#row_customerregistration_othersettings_passwordonsociallogin,#row_customerregistration_othersettings_customraasoptions,#row_customerregistration_othersettings_emailtemplateheading,#row_customerregistration_othersettings_forgotpasswordtemplate,#row_customerregistration_othersettings_emailverificationtemplate').hide();
    }
}