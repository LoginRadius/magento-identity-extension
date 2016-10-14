jQuery(document).ready(function () {
    if (jQuery('.loginradius-raas-birthdate').length != 0) {
        jQuery('.loginradius-raas-birthdate').datepicker("option", "dateFormat", 'mm-dd-yyyy');
    }
});
function show_birthdate_date_block() {
    var maxYear = new Date().getFullYear();
    var minYear = maxYear - 100;
    jQuery('body').on('focus', ".loginradius-raas-birthdate", function () {
        jQuery('.loginradius-raas-birthdate').datepicker({
            dateFormat: 'mm-dd-yy',
            maxDate: new Date(),
            minDate: "-100y",
            changeMonth: true,
            changeYear: true,
            yearRange: (minYear + ":" + maxYear)
        });
    });
}


function hideallforms() {
    jQuery(".magento-raas-loading-image-div,#login_form, #social_registration_from, #forgot_from, #reset_form, #registeration-container, .interfacecontainerdiv").hide();
    jQuery(".account-login").find('.legend').hide();
    jQuery('#reg_from').find('.legend').each(function(){
        jQuery(this).html('');
    });
    jQuery('#lr_social_login_interface').find('.legend').html('You have almost done.');
}
function ShowformbyId(currentform) {
    hideallforms();
    jQuery("#" + currentform).show();
    if(currentform == 'login_form'){
        jQuery(".interfacecontainerdiv").show();
    }
}
function redirect(token, name) {
    jQuery('.magento-raas-loading-image-div').html('<div class="loadinternal"></div>');
    jQuery('.magento-raas-loading-image-div').show();
    var token_name = name ? name : 'token';
    jQuery('#fade').show();
    var form = document.createElement('form');
    form.action = LocalDomain;
    form.method = 'POST';
    var hiddenToken = document.createElement('input');
    hiddenToken.type = 'hidden';
    hiddenToken.value = token;
    hiddenToken.name = token_name;
    form.appendChild(hiddenToken);

    document.body.appendChild(form);
    form.submit();
}
function front_social_login() {
    LoginRadiusSDK.setLoginCallback(function () {
        var token = LoginRadiusSDK.getToken();
        redirect(token);
    });
}
function handleResponse(isSuccess, message, show) {
    if (message != null && message != "") {
        var status='Error';
        if (isSuccess) {
            status='Success';
            setTimeout(function(){window.location.href = loginPage;},5000);
        }
        jQuery(".loginradiusmessagediv").show();
        jQuery('.loginradiusmessagediv').html('<div class="'+status+'">'+message+'</div>');
    } else {
        jQuery(".loginradiusmessagediv").hide();
        jQuery('.loginradiusmessagediv').html('');
    }
    jQuery(".magento-raas-loading-image-div").hide();
}
LoginRadiusRaaS.$hooks.setProcessHook(function () {
    jQuery('.magento-raas-loading-image-div').html('<div class="loadinternal"></div>');
    jQuery('.magento-raas-loading-image-div').show();
    jQuery(".loginradiusmessagediv").hide();
    jQuery(document).ready(function () {
        add_required_field_class('loginradius-raas-setpassword-password');
        add_required_field_class('loginradius-raas-setpassword-confirmpassword');
        add_required_field_class('loginradius-raas-changepassword-oldpassword');
        add_required_field_class('loginradius-raas-changepassword-newpassword');
        add_required_field_class('loginradius-raas-changepassword-confirmnewpassword');
        jQuery('#loginradius-raas-setpassword-emailid').addClass("validate-email");
    });
}, function () {
    if (jQuery('#changepasswordbox') || jQuery('#setpasswordbox')) {
        var getPasswordForm = jQuery('#socialpasswordbox').find('form');
        jQuery(getPasswordForm.parent()).html(getPasswordForm.html());
        jQuery('#loginradius-raas-submit-Save').hide();
    }
    if(raasoption.formRenderDelay){
        setTimeout(function(){ jQuery('.magento-raas-loading-image-div').hide(); }, 1);
    }
    if (jQuery('#loginradius_account_linking_container').length != 0 && jQuery('#interfacecontainerdiv').text() != '') {
        linking();
    }
});
LoginRadiusRaaS.$hooks.socialLogin.onFormRender = function () {
    jQuery('#social_form').show();
    ShowformbyId("social_registration_from");

};
function callSocialLoginInterface() {
    LoginRadiusRaaS.CustomInterface(".interfacecontainerdiv", raasoption);
}
function initializeLoginRaasForm() {
//initialize Login form
    LoginRadiusRaaS.init(raasoption, 'login', function (response) {
        handleResponse(true, "");
        redirect(response.access_token);
    }, function (response) {
        if (response[0].description != null) {
            handleResponse(false, response[0].description);
        }
        jQuery(".magento-raas-loading-image-div").hide();
    }, "login-container");
}
function initializeRegisterRaasForm() {
    LoginRadiusRaaS.init(raasoption, 'registration', function (response) {
        if((raasoption.OptionalEmailVerification || raasoption.DisabledEmailVerification) && response.access_token){
            redirect(response.access_token);
        }else{
            handleResponse(true, "An email has been sent to " + jQuery("#loginradius-raas-registration-emailid").val() + ".Please verify your email address.");
        }
        jQuery('input[type="text"],input[type="password"], select').val('');
    }, function (response) {
        if (response[0].description != null) {
            handleResponse(false, response[0].description);
        }
        jQuery(".magento-raas-loading-image-div").hide();
    }, "registeration-container");
}
function initializeResetPasswordRaasForm() {
    //initialize reset password form and handel email verification
    var vtype = $SL.util.getQueryParameterByName("vtype");
    if (vtype != null && vtype != "") {
        LoginRadiusRaaS.init(raasoption, 'resetpassword', function (response) {
            handleResponse(true, "Password reset successfully");
            ShowformbyId("login_form");
        }, function (response) {
            handleResponse(false, response[0].description);
        }, "resetpassword-container");

        if (vtype == "reset") {
            LoginRadiusRaaS.init(raasoption, 'emailverification', function (response) {
                handleResponse(true, "");
                ShowformbyId("reset_form");
            }, function (response) {
                // on failure this function will call ?errors? is an array of error with message.
                handleResponse(false, response[0].description);
            });
        } else {
            LoginRadiusRaaS.init(raasoption, 'emailverification', function (response) {
                //On Success this callback will call
                handleResponse(true, "Your email has been verified successfully");
                if(raasoption.enableLoginOnEmailVerification && response.access_token && response.access_token != null){
                    redirect(response.access_token);
                }else{
                    ShowformbyId("login_form");
                }
            }, function (response) {
                // on failure this function will call ?errors? is an array of error with message.
                handleResponse(false, response[0].description);
            });
        }
    }
}
function initializeSocialRegisterRaasForm() {
    //initialize social Login form
    LoginRadiusRaaS.init(raasoption, 'sociallogin', function (response) {
        if (response.isPosted) {
            if(!raasoption.DisabledEmailVerification){
                handleResponse(true, "An email has been sent to " + jQuery("#loginradius-raas-social-registration-emailid").val() + ".Please verify your email address.");
            }else{
                handleResponse(true, 'Your account has been created. Please log in on the login page.');
            }
            ShowformbyId("login_form");
        } else {
            handleResponse(true, "", true);
            redirect(response);
        }
        lrHideOverlayOnCheckoutPage();
    }, function (response) {
        if (response[0].description != null) {
            handleResponse(false, response[0].description);
        }
    }, "social-registration-container");
}

function initializeForgotPasswordRaasForms() {
    //initialize forgot password form
    LoginRadiusRaaS.init(raasoption, 'forgotpassword', function (response) {
        handleResponse(true, "An email has been sent to " + jQuery("#loginradius-raas-forgotpassword-emailid").val() + " with reset Password link.");
        jQuery('#loginradius-raas-forgotpassword-emailid').val('');
    }, function (response) {
        if (response[0].description != null) {
            handleResponse(false, response[0].description);
        }
    }, "forgotpassword-container");
}
function initializeAccountLinkingRaasForms() {
    LoginRadiusRaaS.init(raasoption, "accountlinking", function (response) {
        if (response.isPosted) {
            window.location = window.location.href;
        } else {
            handleResponse(true, "");
            redirect(response);
        }
    }, function (response) {
        jQuery(".magento-raas-loading-image-div").hide();
        if (response[0].description != null) {
            handleResponse(false, response[0].description);
        }
    }, "interfacecontainerdiv");
}
function initializeAccountPasswordRaasForms() {
    LoginRadiusRaaS.passwordHandleForms("setpasswordbox", "changepasswordbox", function (israas) {
        if (israas) {
            jQuery("#changepasswordbox").show();
            jQuery("#changepasswordtitlecheckbox, #changepasswordtitle").html('Change Password');
        } else {
            jQuery("#setpasswordbox").show();
            jQuery("#changepasswordtitlecheckbox, #changepasswordtitle").html('Set Password');
        }
    }, function () {
        document.forms["setpassword"].action = "";
        document.forms["setpassword"].submit();
    }, function () {
    }, function () {
        document.forms["changepassword"].action = "";
        document.forms["changepassword"].submit();
    }, function () {
    }, raasoption);

}
function setPasswordForm(arg) {
    if (arg) {
        jQuery("#socialpasswordbox").show();
    } else {
        jQuery("#socialpasswordbox").hide();
    }
}
function hideFormContainer() {
    jQuery('#changepasswordbox').children().unwrap();

}
function display_error_message(elem) {
    var error_container = jQuery("<div class='lr_validate_error' style='color:#d9534f;padding: 3px 0 0;font-size: 0.6875em;'>This is a required field.</div>");
    if (jQuery('#' + elem).length != 0) {
        jQuery('#' + elem).addClass('validation-failed');
        var input = jQuery('#' + elem);
        if (input.val() == "") {
            jQuery('#' + elem).addClass('validation-failed');
            var x = jQuery('#' + elem).next().attr('class');
            if (x == 'lr_validate_error') {
                jQuery('#' + elem).next().remove();
            }
            var x = jQuery('#' + elem).next().next().attr('class');
            if (x == 'lr_validate_error') {
                jQuery('#' + elem).next().next().remove();
            }
            jQuery(error_container).insertAfter('#' + elem);
            jQuery('#' + elem).css('background', '#fff7f5');
        } else {
            var x = jQuery('#' + elem).next().attr('class');
            if (x == 'lr_validate_error') {
                jQuery('#' + elem).next().remove();
            }
            var x = jQuery('#' + elem).next().next().attr('class');
            if (x == 'lr_validate_error') {
                jQuery('#' + elem).next().next().remove();
            }
            jQuery('#' + elem).removeClass('validation-failed');
            jQuery('#' + elem).css('background', '#fcfcfc');
        }
    }
}
function add_required_field_class(elem) {
    if (jQuery('#' + elem).length != 0) {
        jQuery('#' + elem).addClass("required-entry");
        jQuery('#' + elem).keyup(function () {
            var input = jQuery('#' + elem);
            if (input.val() == "") {
                var x = jQuery('#' + elem).next().attr('class');
                if (x == 'lr_validate_error') {
                    jQuery('#' + elem).next().remove();
                }
                var x = jQuery('#' + elem).next().next().attr('class');
                if (x == 'lr_validate_error') {
                    jQuery('#' + elem).next().next().remove();
                }
                display_error_message('loginradius-raas-setpassword-password');
                display_error_message('loginradius-raas-setpassword-confirmpassword');
                display_error_message('loginradius-raas-changepassword-oldpassword');
                display_error_message('loginradius-raas-changepassword-newpassword');
                display_error_message('lloginradius-raas-changepassword-confirmnewpassword');
            } else {
                var x = jQuery('#' + elem).next().attr('class');
                if (x == 'lr_validate_error') {
                    jQuery('#' + elem).next().remove();
                }
                var x = jQuery('#' + elem).next().next().attr('class');
                if (x == 'lr_validate_error') {
                    jQuery('#' + elem).next().next().remove();
                }
                jQuery('#' + elem).removeClass('validation-failed');
                jQuery('#' + elem).css('background', '#fcfcfc');

            }
        });
    }

}
function lrHideOverlayOnCheckoutPage() {
    jQuery("#social_form").hide();
}