function initializeLoginForm() {
//initialize Login form
    var login_options = {};
    login_options.onSuccess = function (response) {
//On Success
        if (response.access_token && response.access_token != null) {
            redirect(response.access_token);
        }
    };
    login_options.onError = function (errors) {
//On Errors
        if (errors[0].Description != null) {
            handleResponse(false, errors[0].Description);
        }
    };
    login_options.container = "login-container";
    LRObject.init("login", login_options);
}
function hideallforms() {
    jQuery(".magento-raas-loading-image-div,#login_form, #social_registration_from, #forgot_from, #reset_form, #registeration-container, .interfacecontainerdiv").hide();
    jQuery(".account-login").find('.legend').hide();
    jQuery('#reg_from').find('.legend').each(function () {
        jQuery(this).html('');
    });
    jQuery('#lr_social_login_interface').find('.legend').html('You have almost done.');
}

function ShowformbyId(currentform) {
    hideallforms();
    jQuery("#" + currentform).show();
    if (currentform == 'login_form' || currentform == 'reg_from') {
        jQuery(".interfacecontainerdiv").show();
    }
}
function initializeSocialForm() {
    //initialize social Login form
    var sl_options = {};
    sl_options.onSuccess = function (response) {
//On Success
//Here you get the access token
        if (response.isPosted) {
            if (!commonOptions.disabledEmailVerification) {
                handleResponse(true, "An email has been sent to " + jQuery("#loginradius-social-registration-emailid").val() + ".Please verify your email address.");
            } else {
                handleResponse(true, 'Your account has been created. Please log in on the login page.');
            }

        } else {
            if (response.access_token && response.access_token != null) {
                redirect(response.access_token);
            } else {
                handleResponse(true, "An email has been sent. Please verify your email address.");
            }
        }
        ShowformbyId("login_form");
        ShowformbyId('reg_from');
    };
    sl_options.onError = function (errors) {
//On Errors
        if (errors[0].Description != null) {
            handleResponse(false, errors[0].Description);
        }
    };
    sl_options.container = "social-registration-container";
    sl_options.templateName = 'loginradiuscustom_tmpl';
    LRObject.customInterface(".interfacecontainerdiv", sl_options);
    LRObject.$hooks.register('socialLoginFormRender', function () {
        jQuery("#social_registration_from").show();
        jQuery("#login_form,#reg_from").hide();
        
        show_birthdate_date_block();
    });
    LRObject.init('socialLogin', sl_options);

}
function initializeResetPasswordForm() {
    //initialize reset password form and handel email verification
    var vtype = LRObject.util.getQueryParameterByName("vtype");
    if (vtype != null && vtype != "") {
        var reset_options = {};
        if (vtype == "reset") {

            reset_options.onSuccess = function (response) {
//On Success
                handleResponse(true, "Password reset successfully");
                ShowformbyId("login_form");
            };
            reset_options.onError = function (errors) {
//On Errors

                if (errors[0].Description != null) {
                    handleResponse(false, errors[0].Description);
                }
            };

            reset_options.container = "resetpassword-container";
            ShowformbyId("reset_form");
            LRObject.init("resetPassword", reset_options);
        } else {
            var verify_email = {};
            verify_email.onSuccess = function (response) {
//On Success
                if (response.access_token && response.access_token != null) {
                    redirect(response.access_token);
                } else {
                    handleResponse(true, "Your email has been verified successfully");
                }
            };
            verify_email.onError = function (errors) {
//On Errors
                if (errors[0].Description != null) {
                    handleResponse(false, errors[0].Description);
                }
            };

            reset_options.container = "resetpassword-container";
            LRObject.init("verifyEmail", verify_email);

        }
    }
}
function initializeRegisterForm() {
    var registration_options = {};
    registration_options.onSuccess = function (response) {
//On Success
        if ((commonOptions.optionalEmailVerification || commonOptions.disabledEmailVerification) && response.access_token) {
            redirect(response.access_token);
        } else {
            handleResponse(true, "An email has been sent to " + jQuery("#loginradius-registration-emailid").val() + ". Please verify your email address.");
        }
        jQuery('input[type="text"],input[type="password"], select, textarea').val('');
    };
    registration_options.onError = function (errors) {
//On Errors
        if (errors[0].Description != null) {
            handleResponse(false, errors[0].Description);
        }
    };
    registration_options.container = "registration-container";
    LRObject.init("registration", registration_options);

}
function initializeForgotPasswordForm() {
    var forgot_options = {};
    forgot_options.onSuccess = function (response) {

//On Success
        handleResponse(true, "An email has been sent to " + jQuery("#loginradius-forgotpassword-emailid").val() + " with reset Password link.");
        jQuery('#loginradius-forgotpassword-emailid').val('');
    };
    forgot_options.onError = function (errors) {
//On Errors

        if (errors[0].Description != null) {
            handleResponse(false, errors[0].Description);
        }
    };
    forgot_options.container = "forgotpassword-container";
    LRObject.init("forgotPassword", forgot_options);
}
function initializeAccountLinkinForm() {
    var account_linking_options = {};
    account_linking_options.onSuccess = function (response) {
//On Success
        if (response.IsPosted) {
            handleResponse(true, 'Your account linked successfully. we will reload page in 3 seconds...');
            setTimeout(function () {
                location.reload();
            }, 3000);
        } else {
            if (response.access_token && response.access_token != null) {
                redirect(response.access_token);
            }
        }
    };
    account_linking_options.onError = function (errors) {
//On Errors
        if (errors[0].Description != null) {
            handleResponse(false, errors[0].Description);
        }
    };
    // account_linking_options.templateName = 'loginradiuscustom_tmpl_link';
    account_linking_options.container = "lr-linked-social,lr-not-linked-social";
    LRObject.init("linkAccount", account_linking_options);
    var unlink_options = {};
    unlink_options.onSuccess = function (response) {
// On Success
        handleResponse(true, 'Your account unlinked successfully. we will reload page in 3 seconds...');
        setTimeout(function () {
            location.reload();
        }, 3000);
    };
    unlink_options.onError = function (errors) {
// On Errors
        if (errors[0].Description != null) {
            handleResponse(false, errors[0].Description);
        }
    }
    LRObject.init("unLinkAccount", unlink_options);
}


function initializeChangePasswordForm() {
    var change_password_options = {};
    change_password_options.onSuccess = function (response) {
//On Success
        if (response.access_token && response.access_token != null) {
            redirect(response.access_token);
        }
    };
    change_password_options.onError = function (errors) {
//On Errors

        if (errors[0].Description != null) {
            handleResponse(false, errors[0].Description);
        }
    };
    change_password_options.container = "changepassword-container";
    LRObject.$hooks.register('afterFormRender', function (name) {
        if (name == "changepassword") {
            var models = jQuery('#changepassword-container');
            models.find('label').addClass('required');
            var getPasswordForm = jQuery('#socialpasswordbox').find('form');
            jQuery('#loginradius-submit-Submit').hide();
            jQuery('#loginradius-changepassword-oldpassword, #loginradius-changepassword-newpassword, #loginradius-changepassword-confirmnewpassword').addClass('required-entry input-text');
            jQuery('#form-validate').submit(function () {

                jQuery('#loginradius-submit-Submit').trigger('click');

            });
        }
    });
    LRObject.init("changePassword", change_password_options);
}
function initializeProfileEditorForm() {
    var profile_editor = {};
    profile_editor.onSuccess = function (response) {
//On Success
        //redirect(response.access_token);
    };
    profile_editor.onError = function (errors) {
//On Errors
        if (errors[0].Description != null) {
            handleResponse(false, errors[0].Description);
        }
    };
    profile_editor.container = "profileeditor-container";
    LRObject.init("profileEditor", profile_editor);
}
function redirect(token, name) {
    setTimeout(function () {
        jQuery('.magento-raas-loading-image-div').show();
    }, 700);
    var token_name = name ? name : 'token';
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
function handleResponse(isSuccess, message, show) {

    if (message != null && message != "") {
        var status = 'Error error-msg';
        if (isSuccess) {
            status = 'Success success-msg';
        }
        jQuery('.loginradiusmessagediv').addClass('messages');
        jQuery('.loginradiusmessagediv').html('<li class="' + status + '"><ul><li>' + message + '</li></ul></li>').show();
    } else {
        jQuery('.loginradiusmessagediv').html('').hide();
    }
    jQuery('.magento-raas-loading-image-div').hide();
}
jQuery(document).ready(function ($) {
    $('.magento-raas-loading-image-div').hide();
    LRObject.$hooks.register('startProcess', function () {
        $('.magento-raas-loading-image-div').show();
    });
    LRObject.$hooks.register('endProcess', function () {
        $('.magento-raas-loading-image-div').hide();
    });
    LRObject.$hooks.register('afterFormRender', function (actionName) {
        if (actionName == "registration") {
            show_birthdate_date_block();
        }
        if (actionName == 'changepassword') {
            var getPasswordForm = $('#socialpasswordbox').find('form');
            $(getPasswordForm.parent()).html(getPasswordForm.html());
            $('#loginradius-submit-submit').hide();
        }
    });
});
function show_birthdate_date_block() {
    var maxYear = new Date().getFullYear();
    var minYear = maxYear - 100;
    jQuery('body').on('focus', ".loginradius-birthdate", function () {
        jQuery('.loginradius-birthdate').datepicker({
            dateFormat: 'mm-dd-yy',
            maxDate: new Date(),
            minDate: "-100y",
            changeMonth: true,
            changeYear: true,
            yearRange: (minYear + ":" + maxYear)
        });
    });
}