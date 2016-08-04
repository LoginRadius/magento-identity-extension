
var raasForm = {
    resetPassword: function () {
        //initialize reset password form and handel email verification
        var vtype = $SL.util.getQueryParameterByName("vtype");
        if (vtype != null && vtype != "") {
            LoginRadiusRaaS.init(raasoption, 'resetpassword', function (response) {
                responseHandler(true, "Password reset successfully");
                showformbyId("login_form");
            }, function (response) {
                responseHandler(false, response[0].description);
            }, "resetpassword-container");

            if (vtype == "reset") {
                LoginRadiusRaaS.init(raasoption, 'emailverification', function (response) {
                    responseHandler(true, "");
                    showformbyId("reset_form");
                }, function (response) {
                    // on failure this function will call ?errors? is an array of error with message.
                    responseHandler(false, response[0].description);
                });
            }
            else {
                LoginRadiusRaaS.init(raasoption, 'emailverification', function (response) {
                    //On Success this callback will call
                    responseHandler(true, "Your email has been verified successfully");

                    if (raasoption.enableLoginOnEmailVerification && response.access_token && response.access_token != null) {
                        raasRedirection(response.access_token);
                    } else {
                        showformbyId("login_form");
                    }
                }, function (response) {
                    // on failure this function will call ?errors? is an array of error with message.
                    responseHandler(false, response[0].description);
                });
            }
        }
    },
    socialLogin: function () {
        //initialize social Login form
        LoginRadiusRaaS.init(raasoption, 'sociallogin', function (response) {
            if (response.isPosted) {
                if (!raasoption.DisabledEmailVerification) {
                    responseHandler(true, "An email has been sent to " + document.getElementById('loginradius-raas-social-registration-emailid').value + ".Please verify your email address.");
                } else {
                    responseHandler(true, 'Your account has been created. Please log in on the login page.');
                }
                showformbyId("login_form");
            } else {
                responseHandler(true, "");
                raasRedirection(response);
            }
        }, function (response) {
            if (response[0].description != null) {
                responseHandler(false, response[0].description);
            }
        }, "social-registration-container");
    },
    forgotPassword: function () {
        //initialize forgot password form
        LoginRadiusRaaS.init(raasoption, 'forgotpassword', function (response) {
            responseHandler(true, "An email has been sent to " + document.getElementById('loginradius-raas-forgotpassword-emailid').value + " with reset Password link.");
            document.getElementById('loginradius-raas-forgotpassword-emailid').value = '';
        }, function (response) {
            if (response[0].description != null) {
                responseHandler(false, response[0].description);
            }
        }, "forgotpassword-container");
    },
    accountLinking: function () {
        LoginRadiusRaaS.init(raasoption, "accountlinking", function (response) {
            if (response.isPosted) {
                document.getElementById('magento-raas-loading-image-div').style.display = 'block';
                window.location = window.location.href;
            } else {
                document.getElementById('magento-raas-loading-image-div').style.display = 'block';
                raasRedirection(response);
            }
        }, function (response) {

            if (response[0].description != null) {
                raasRedirection(false, response[0].description);
            }
        }, "interfacecontainerdiv");
    },
    interface: function () {
        LoginRadiusRaaS.CustomInterface(".interfacecontainerdiv", raasoption);
    },
    login: function () {
//initialize Login form
        LoginRadiusRaaS.init(raasoption, 'login', function (response) {
            responseHandler(true, "");
            raasRedirection(response.access_token);
        }, function (response) {
            if (response[0].description != null) {
                responseHandler(false, response[0].description);
            }
        }, "login-container");
    },
    register: function () {
        LoginRadiusRaaS.init(raasoption, 'registration', function (response) {

            if ((raasoption.OptionalEmailVerification || raasoption.DisabledEmailVerification) && response.access_token) {

                raasRedirection(response.access_token);
            } else {

                responseHandler(true, "An email has been sent to " + document.getElementById('loginradius-raas-registration-emailid').value + ".Please verify your email address.");

                //responseHandler(true, "An email has been sent to " + document.getElementById('loginradius-raas-registration-emailid').value + ".Please verify your email address.");
            }
            jQuery('input[type="text"],input[type="password"], select').val('');
        }, function (response) {

            if (response[0].description != null) {
                responseHandler(false, response[0].description);
            }

        }, "registeration-container");
    },
    accountPassword: function () {
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
    },
    emailVerify: function () {
        LoginRadiusRaaS.init(raasoption, 'emailverification', function (response) {
            //On Success this callback will call
           responseHandler(true, "Your email has been verified successfully");
           document.getElementById('magento-raas-loading-image-div').style.display = 'none';
             if (raasoption.enableLoginOnEmailVerification && response.access_token && response.access_token != null) {
                raasRedirection(response.access_token);
            } else {
                window.setTimeout(function () {
                    window.location.href = SignInDomain;
                }, 5000);


            }
        }, function (response) {
            // on failure this function will call ?errors? is an array of error with message.
            responseHandler(false, response[0].description);
        });
    }
}
require(['jquery', "mage/calendar"], function ($) {
    $(document).ready(function () {
        $('#magento-raas-loading-image-div').click(function () {
            $(this).hide();
        });
        
            if ($('#changepasswordbox') || $('#setpasswordbox')) {
                var passwordButtonHide = setInterval(function () {
                    try {
                        var getPasswordForm = $('#socialpasswordbox').find('form');
                        $(getPasswordForm.parent()).html(getPasswordForm.html());
                        
                        $('#loginradius-raas-submit-Save').hide();
                    } catch (err) {

                    }
                    if($(getPasswordForm.parent()).html(getPasswordForm.html()).length > 0){
                        
                    stopPasswordInterval();
                }
                }, 1000);

                function stopPasswordInterval() {
                    clearInterval(passwordButtonHide);
                }
            }
       
        LoginRadiusRaaS.$hooks.setProcessHook(function () {
            $('#magento-raas-loading-image-div').show();
        }, function () {

            if (raasoption.formRenderDelay) {
               
                setTimeout(function () {
                    $('#magento-raas-loading-image-div').hide();
                }, 1);
            }else{
                setTimeout(function () {
                    $('#magento-raas-loading-image-div').hide();
                }, 1);
            }
            
        });
        LoginRadiusRaaS.$hooks.socialLogin.onFormRender = function () {
           
            $('#magento-raas-loading-image-div').hide();
            showformbyId('required_form');
        };
        var myVar = setInterval(function () {
            if ($('.loginradius-raas-birthdate').length != 0) {
                showDatePicker();
                myStopFunction();
            }
        }, 3000);
        function myStopFunction() {
            clearInterval(myVar);
        }
    });

    function showDatePicker() {
        var maxYear = new Date().getFullYear();
        var minYear = maxYear - 100;
        $('.loginradius-raas-birthdate').calendar({
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
        document.getElementById('magento-raas-loading-image-div').style.display = 'block';
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
        $('#magento-raas-loading-image-div').hide();
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
    hideFormContainer = function () {
        $('#changepasswordbox').children().unwrap();
    }
});