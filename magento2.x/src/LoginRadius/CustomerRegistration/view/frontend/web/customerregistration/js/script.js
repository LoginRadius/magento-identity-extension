
var ciamForm = {
    resetPassword: function () {
//initialize reset password form and handel email verification

        var vtype = LRObject.util.getQueryParameterByName("vtype");
        if (vtype != null && vtype != "") {
            var reset_options = {};
            if (vtype == "reset") {
                reset_options.onSuccess = function (response) {
                //On Success
                    responseHandler(true, "Password reset successfully");
                    showformbyId("login_form");
                };
                reset_options.onError = function (errors) {
                //On Errors

                    if (errors[0].Description != null) {
                        responseHandler(false, errors[0].Description);
                    }
                };
                reset_options.container = "resetpassword-container";
                showformbyId("reset_form");
                LRObject.init("resetPassword", reset_options);
            } 
        }
    },
    socialLogin: function () {
        //initialize social Login form
        var sl_options = {};
        sl_options.onSuccess = function (response) {
            //On Success
            //Here you get the access token
            if (response.IsPosted) {
                if (!commonOptions.disabledEmailVerification) {
                    responseHandler(true, "An email has been sent to " + document.getElementById('loginradius-socialRegistration-emailid').value + ".Please verify your email address.");
                } else {
                    responseHandler(true, 'Your account has been created. Please log in on the login page.');
                }
                showformbyId("login_form");
            } else {
                responseHandler(true, "");

                if (response.access_token && response.access_token != null) {
                    raasRedirection(response.access_token);
                }
            }
        };
        sl_options.onError = function (errors) {
            //On Errors
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
            }
        };
        sl_options.container = "social-registration-container";
        LRObject.util.ready(function () {
            LRObject.init('socialLogin', sl_options);
        });
    },
    forgotPassword: function () {
        //initialize forgot password form
        var forgotpassword_options = {};
        forgotpassword_options.container = "forgotpassword-container";
        forgotpassword_options.onSuccess = function (response) {
            // On Success
            responseHandler(true, "An email has been sent to " + document.getElementById('loginradius-forgotpassword-emailid').value + " with reset Password link.");
            document.getElementById('loginradius-forgotpassword-emailid').value = '';
        };
        forgotpassword_options.onError = function (errors) {
            // On Errors
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
            }
        }

        LRObject.util.ready(function () {
            LRObject.init("forgotPassword", forgotpassword_options);
        });
    },
    accountLinking: function () {
        var la_options = {};
        la_options.container = "interfacecontainerdiv";
        la_options.templateName = 'loginradiuscustom_tmpl_link';
        la_options.onSuccess = function (response) {
// On Success
            if (response.IsPosted) {
                responseHandler(true, 'Provider has been connected successfully.');
                setTimeout(function () {
                    window.location = window.location.href;
                    document.getElementById('ciam-loading-image-div').style.display = 'block';
                }, 5000);
            } else {
                document.getElementById('ciam-loading-image-div').style.display = 'block';
                raasRedirection(response);
            }
        };
        la_options.onError = function (errors) {
// On Errors
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
            }
        };

        LRObject.init("linkAccount", la_options);
    },
    accountUnlinking: function () {
        var unlink_options = {};
        unlink_options.onSuccess = function (response) {
// On Success
            if (response.IsDeleted) {
                responseHandler(true, 'Provider has been removed successfully.');
                setTimeout(function () {
                    window.location = window.location.href;
                    document.getElementById('ciam-loading-image-div').style.display = 'block';
                }, 5000);
            }
        };
        unlink_options.onError = function (errors) {
// On Errors
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
            }
        }

        LRObject.init("unLinkAccount", unlink_options);
    },
    interface: function () {
        var custom_interface_option = {};
        custom_interface_option.templateName = 'loginradiuscustom_tmpl';
        LRObject.util.ready(function () {
            LRObject.customInterface(".interfacecontainerdiv", custom_interface_option);
        });
    },
    login: function () {
        //initialize Login form
        var login_options = {};
        login_options.onSuccess = function (response) {
            //On Success
            responseHandler(true, "");
            if (response.access_token && response.access_token != null) {
                raasRedirection(response.access_token);
            }
        };
        login_options.onError = function (errors) {
            //On Errors
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
            }
        };
        login_options.container = "login-container";
        LRObject.util.ready(function () {
            LRObject.init("login", login_options);
        })
    },
    register: function () {
        var registration_options = {}
        registration_options.onSuccess = function (response) {
            //On Success
            if ((commonOptions.optionalEmailVerification || commonOptions.disabledEmailVerification) && response.access_token) {
                raasRedirection(response.access_token);
            } else {
                responseHandler(true, "An email has been sent to " + document.getElementById('loginradius-registration-emailid').value + ".Please verify your email address.");
            }
            jQuery('input[type="text"],input[type="password"], select,textarea').val('');
        };
        registration_options.onError = function (errors) {
            //On Errors
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
            }
        };
        registration_options.container = "registeration-container";
        LRObject.util.ready(function () {
            LRObject.init("registration", registration_options);
        })
    },
    accountPassword: function () {
        var changepassword_options = {};
        changepassword_options.container = "changepassword-container";
        changepassword_options.onSuccess = function (response) {
// On Success
            console.log(response);
        };
        changepassword_options.onError = function (response) {
// On Error
            console.log(response);
        };

        LRObject.util.ready(function () {

            LRObject.init("changePassword", changepassword_options);


        });
    },
    emailVerify: function () {
        var verifyemail_options = {};
        verifyemail_options.onSuccess = function (response) {
        // On Success
            responseHandler(true, "Your email has been verified successfully");
            document.getElementById('ciam-loading-image-div').style.display = 'none';
            if (commonOptions.loginOnEmailVerification && typeof response.access_token != "undefined" && response.access_token && response.access_token != null) {
                    raasRedirection(response.access_token);
            } else if (commonOptions.loginOnEmailVerification && response.Data != null && response.Data.access_token != null) {
                    raasRedirection(response.Data.access_token);
            } else {
                window.setTimeout(function () {
                    window.location.href = SignInDomain;
                }, 5000);
            }
        };
        verifyemail_options.onError = function (errors) {
        // On Errors
            responseHandler(false, errors[0].Description);
        }

        LRObject.init("verifyEmail", verifyemail_options);

    }
}
require(['jquery', "mage/calendar"], function ($) {
    $(document).ready(function () {
        LRObject.$hooks.register('startProcess', function () {
            $('#ciam-loading-image-div').show();
        });
        LRObject.$hooks.register('afterFormRender', function (actionName) {
            if (actionName == "registration") {
                showDatePicker();
            }
            if (actionName == 'socialRegistration') {
                showformbyId('required_form');
                showDatePicker();
            }
            if (actionName == 'changepassword') {
                var getPasswordForm = $('#socialpasswordbox').find('form');
                $(getPasswordForm.parent()).html(getPasswordForm.html());
                $('#loginradius-submit-submit').hide();
            }
            $('#ciam-loading-image-div').hide();
        });
        if ($(".lr_embed_bricks_200.interfacecontainerdiv")) {
            var ciamTimeout = setInterval(function () {
                if ($(".lr_embed_bricks_200.interfacecontainerdiv").html() != '') {
                    clearInterval(ciamTimeout);
                    $('#ciam-loading-image-div').hide();
                }
            }, 100);
        }
        $('#ciam-loading-image-div').click(function () {
            $(this).hide();
        });
        $('#myModal .close').click(function () {
            window.location.href = window.location.href;
        });
    });
    function showDatePicker() {
        var maxYear = new Date().getFullYear();
        var minYear = maxYear - 100;
        $('.loginradius-birthdate').calendar({
            buttonText: "Select Date",
            dateFormat: 'mm-dd-yy',
            maxDate: new Date(),
            minDate: "-100y",
            changeMonth: true,
            changeYear: true,
            yearRange: (minYear + ":" + maxYear)
        });
    }
    raasRedirection = function (token) {
        document.getElementById('ciam-loading-image-div').style.display = 'block';
        var form = document.createElement('form');
        form.action = LocalDomain;
        form.method = 'POST';
        var hiddenToken = document.createElement('input');
        hiddenToken.type = 'hidden';
        hiddenToken.value = token;
        hiddenToken.name = "token";
        form.appendChild(hiddenToken);
        document.body.appendChild(form);
        form.submit();
    }
    responseHandler = function (isSuccess, message) {
        if (message != null && message != "") {
            var status = (isSuccess) ? 'Success' : 'Error';
            $('#loginradiusmessagediv').show();
            $('#loginradiusmessagediv').html('<div class="' + status + '">' + message + '</div>');
        } else {
            $('#loginradiusmessagediv').hide();
        }
        $('#ciam-loading-image-div').hide();
    }
    hideallforms = function () {
        $('#login_form,#required_form,#register_form,#forgot_from,#reset_form').hide();
    }
    showformbyId = function (id) {
        hideallforms();
        document.getElementById(id).style.display = 'block';
    }
    setPasswordForm = function (arg) {
        if (arg) {
            $('#socialpasswordbox').show();
        } else {
            $('#socialpasswordbox').hide();
        }
    }
});