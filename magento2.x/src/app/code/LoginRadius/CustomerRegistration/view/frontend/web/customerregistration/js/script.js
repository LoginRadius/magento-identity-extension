var ciamForm = {
    resetPassword: function () {
        var lrResetInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrResetInterval);
                var vtype = LRObject.util.getQueryParameterByName("vtype");
                if (vtype != null && vtype != "") {
                    var reset_options = {};
                    if (vtype == "reset") {
                        reset_options.onSuccess = function (response) {
                            responseHandler(true, Messages.FORGOT_PASSWORD_SUCCESS_MSG);
                            showformbyId("login_form");
                        };
                        reset_options.onError = function (errors) {
                            if (errors[0].Description != null) {
                                responseHandler(false, errors[0].Description);
                            }
                        };
                        reset_options.container = "resetpassword-container";
                        showformbyId("reset_form");
                        LRObject.init("resetPassword", reset_options);
                    }
                }
            }
        }, 1);
    },
    getBackupCodes: function () {
        var lrGetBackupInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrGetBackupInterval);
                LRObject.api.getBackupCode(accessToken,
                    function (response) {
                        jQuery('#backupcode-table-body').empty();
                        for (var i = 0; i < response.BackUpCodes.length; i++) {
                            var html = '';
                            jQuery('#resettable').hide();
                            jQuery('#lr_ciam_reset_table').show();

                            html += '<div class="form-item code-list" id="backup-codes-' + i + '-field">';
                            html += '<span class="backupCode">' + response.BackUpCodes[i] + '</span>';
                            html += '</div>';

                            jQuery('#backupcode-table-body').append(html);
                            jQuery('#ciam-loading-image-div').hide();
                        }
                        jQuery('.mybackupcopy').click(function () {
                            setClipboard();
                        });
                    }, function (errors) {
                        jQuery('#resettable').show();
                    });
            }
        }, 1);
    },
    socialLogin: function () {
        //initialize social Login form
        var sl_options = {};
        sl_options.onSuccess = function (response) {
            //On Success
            //Here you get the access token
            if (response.IsPosted && typeof response.Data.AccountSid !== 'undefined') {
                responseHandler(true, Messages.REGISTRATION_OTP_MSG);
            }
            else if (response.IsPosted == true && typeof response.Data.AccountSid === 'undefined') {
                responseHandler(true, Messages.SOCIAL_LOGIN_MSG);
                showformbyId("login_form");
            } else if (response.access_token) {
                ciamRedirection(response.access_token);
            }

        };
        sl_options.onError = function (errors) {
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
            }
        };
        sl_options.container = "social-registration-container";
        var lrSocialLoginInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrSocialLoginInterval);
                LRObject.util.ready(function () {
                    LRObject.init('socialLogin', sl_options);
                });
            }
        }, 1);
    },
    login: function () {
        //initialize Login form
        var login_options = {};
        login_options.onSuccess = function (response) {

            responseHandler(true, "");
            if (response.IsPosted == true && typeof response.access_token !== 'undefined') {
                if (jQuery('#loginradius-login-username').length !== 0 || jQuery('#loginradius-login-emailid').length !== 0) {
                    responseHandler(true, Messages.LOGIN_BY_EMAIL_MSG);
                }
            }
            else if (typeof response.Data !== 'undefined' && typeof response.Data.Sid !== 'undefined') {
                responseHandler(true, Messages.LOGIN_BY_PHONE_MSG);
            } else if (typeof response.Data !== 'undefined' && typeof response.access_token === 'undefined') {
                responseHandler(true, Messages.EMAIL_VERIFICATION_SUCCESS_MSG);
            } else if (response.IsPosted == true) {
                responseHandler(true, Messages.LOGIN_BY_EMAIL_MSG);
            } else if (response.access_token) {
                responseHandler(true);
                ciamRedirection(response.access_token);
            }
        };
        login_options.onError = function (errors) {
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
            }
        };
        jQuery('html, body').animate({ scrollTop: 0 }, 1000);
        login_options.container = "login-container";
        var lrLoginInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrLoginInterval);
                LRObject.util.ready(function () {
                    LRObject.init("login", login_options);
                });
            }
        }, 1);
    },
    register: function () {
        var registration_options = {}
        registration_options.onSuccess = function (response) {
            var optionalemailverification = '';
            var disableemailverification = '';
            if (typeof LRObject.options.optionalEmailVerification != 'undefined') {
                optionalemailverification = LRObject.options.optionalEmailVerification;
            }
            if (typeof LRObject.options.disabledEmailVerification != 'undefined') {
                disableemailverification = LRObject.options.disabledEmailVerification;
            }
            if (response.IsPosted && response.Data == null) {
                if ((typeof (optionalemailverification) == 'undefined' || optionalemailverification !== true) && (typeof (disableemailverification) == 'undefined' || disableemailverification !== true)) {
                    responseHandler(true, Messages.REGISTRATION_SUCCESS_MSG);
                    jQuery('html, body').animate({ scrollTop: 0 }, 1000);
                }
            } else if (response.access_token != null && response.access_token != "") {
                ciamRedirection(response.access_token);
            } else if (response.IsPosted && typeof response.Data !== 'undefined' && response.Data !== null && typeof response.Data.Sid !== 'undefined') {
                responseHandler(true, Messages.REGISTRATION_OTP_MSG);
            } else if (LRObject.options.otpEmailVerification == true && response.Data == null) {
                responseHandler(true, Messages.REGISTRATION_OTP_VERIFICATION_MSG);
            } else {
                responseHandler(true, Messages.REGISTRATION_SUCCESS_MSG);
            }
            jQuery('input[type="text"],input[type="password"],select,textarea').val('');
        };
        registration_options.onError = function (errors) {
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description, "", "error");
            } else if (errors[0] != null) {
                responseHandler(false, errors[0], "", "error");
            }
            jQuery('html, body').animate({ scrollTop: 0 }, 1000);
        };

        registration_options.container = "registration-container";
        var lrRegisterInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrRegisterInterval);
                LRObject.util.ready(function () {
                    LRObject.init("registration", registration_options);
                });
            }
        }, 1);
    },
    forgotPassword: function () {
        //initialize forgot password form
        var forgotpassword_options = {};
        forgotpassword_options.container = "forgotpassword-container";
        forgotpassword_options.onSuccess = function (response) {
            if (response.IsPosted == true && typeof response.Data !== 'undefined' && response.Data !== null) {
                responseHandler(true, Messages.FORGOT_PASSWORD_PHONE_MSG);
            } else if (LRObject.options.otpEmailVerification == true && typeof response.Data === 'undefined') {
                responseHandler(true, Messages.FORGOT_PHONE_OTP_VERIFICATION_MSG);
            } else if (response.IsPosted == true && typeof (response.Data) === "object") {
                if (jQuery('form[name="loginradius-resetpassword"]').length > 0) {
                    responseHandler(true, Messages.FORGOT_PASSWORD_SUCCESS_MSG);
                    window.setTimeout(function () {
                        window.location.replace(commonOptions.resetPasswordUrl);
                    }, 1000);
                }
            } else if (response.IsPosted == true && typeof (response.Data) === "undefined") {
                if (jQuery('form[name="loginradius-resetpassword"]').length > 0) {
                    responseHandler(true, Messages.FORGOT_PASSWORD_SUCCESS_MSG);
                    window.setTimeout(function () {
                        window.location.replace(commonOptions.resetPasswordUrl);
                    }, 1000);
                } else {
                    responseHandler(true, Messages.FORGOT_PASSWORD_MSG);
                }
            }
            jQuery('input[type="text"]').val('');
            jQuery('input[type="password"]').val('');

        };
        forgotpassword_options.onError = function (errors) {
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
            }
        }
        var lrForgotInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrForgotInterval);
                LRObject.util.ready(function () {
                    LRObject.init("forgotPassword", forgotpassword_options);
                });
            }
        }, 1);
    },
    accountLinking: function () {
        var la_options = {};
        la_options.container = "interfacecontainerdiv";
        la_options.templateName = 'loginradiuscustom_tmpl_link';
        la_options.onSuccess = function (response) {
            if (response.IsPosted) {
                responseHandler(true, Messages.ACCOUNT_LINKING_MSG);
                setTimeout(function () {
                    window.location = window.location.href;
                    document.getElementById('ciam-loading-image-div').style.display = 'block';
                }, 5000);
            } else {
                document.getElementById('ciam-loading-image-div').style.display = 'block';
                ciamRedirection(response);
            }
        };
        la_options.onError = function (errors) {
            if (errors[0].ErrorCode != '1028' && errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
            }
        };
        var lrLinkingInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrLinkingInterval);
                LRObject.init("linkAccount", la_options);
            }
        }, 1);
    },
    accountUnlinking: function () {
        var unlink_options = {};
        unlink_options.onSuccess = function (response) {
            if (response.IsDeleted == true) {
                responseHandler(true, Messages.ACCOUNT_UNLINKING_MSG);
                setTimeout(function () {
                    window.location = window.location.href;
                    document.getElementById('ciam-loading-image-div').style.display = 'block';
                }, 5000);
            }
        };
        unlink_options.onError = function (errors) {
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
            }
        }
        var lrunLinkingInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrunLinkingInterval);
                LRObject.init("unLinkAccount", unlink_options);
            }
        }, 1);
    },
    interface: function () {
        var custom_interface_option = {};
        custom_interface_option.templateName = 'loginradiuscustom_tmpl';
        var lrSocialInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrSocialInterval);
                LRObject.util.ready(function () {
                    LRObject.customInterface(".interfacecontainerdiv", custom_interface_option);
                });
            }
        }, 1);
    },
    accountPassword: function () {
        var changepassword_options = {};
        changepassword_options.container = "changepassword-container";
        changepassword_options.onSuccess = function (response) {
            responseHandler(true, Messages.CHANGE_PASSWORD_SUCCESS_MSG);
            jQuery('input[type="password"]').val('');
            jQuery('html, body').animate({ scrollTop: 0 }, 1000);
        };
        changepassword_options.onError = function (errors) {
            responseHandler(false, errors[0].Description);
            jQuery('html, body').animate({ scrollTop: 0 }, 1000);
        };
        var lrChangeInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrChangeInterval);
                LRObject.util.ready(function () {
                    LRObject.init("changePassword", changepassword_options);
                });
            }
        }, 1);
    },
    emailVerify: function () {
        var verifyemail_options = {};
        verifyemail_options.onSuccess = function (response) {
            if (typeof response !== 'undefined') {
                if (!LoggedIn && typeof response.access_token != "undefined" && response.access_token != null && response.access_token != "") {
                    ciamRedirection(response.access_token);
                }
                else if (!LoggedIn && response.Data != null && response.Data.access_token != null && response.Data.access_token != "") {
                    ciamRedirection(response.Data.access_token);
                } else {
                    responseHandler(true, Messages.EMAIL_VERIFICATION_SUCCESS_MSG);
                    window.setTimeout(function () {
                        window.location.href = SignInDomain;
                    }, 3000);
                }
            }
        };
        verifyemail_options.onError = function (errors) {
            responseHandler(false, errors[0].Description);
        }
        var lrVerifyInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrVerifyInterval);
                LRObject.init("verifyEmail", verifyemail_options);
            }
        }, 1);
    },
    oneClickSignIn: function () {
        var options = {};
        options.onSuccess = function (response) {
            ciamRedirection(response.access_token);
        };
        options.onError = function (errors) {
            if (!LoggedIn) {
                responseHandler(false, errors[0].Description);
            }
        };
        var lrInstantInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrInstantInterval);
                LRObject.init("instantLinkLogin", options);
            }
        }, 1);
    },
    updatePhoneContainer: function () {
        var updatephone_options = {};
        updatephone_options.container = "updatephone-container";
        updatephone_options.onSuccess = function (response) {
            if (typeof response.Data !== 'undefined') {
                responseHandler(true, Messages.UPDATE_PHONE_MSG);
                jQuery('html, body').animate({ scrollTop: 0 }, 1000);
            }
            else if (response.IsPosted == true) {
                responseHandler(true, Messages.UPDATE_PHONE_SUCCESS_MSG);
                jQuery('html, body').animate({ scrollTop: 0 }, 1000);
                window.setTimeout(function () {
                    window.location.reload();
                }, 1000);
            }
        };
        updatephone_options.onError = function (errors) {
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
                jQuery('html, body').animate({ scrollTop: 0 }, 1000);
            }
        };
        var lrUpdateInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrUpdateInterval);
                LRObject.init("updatePhone", updatephone_options);
            }
        }, 1);
    },

    profileEditor: function () {
        var profileeditor_options = {};
        profileeditor_options.container = "profileeditor-container";
        profileeditor_options.onSuccess = function (response) {
            responseHandler(true, Messages.UPDATE_USER_PROFILE);
            lrSetCookie('lr_profile_update', 'true');
            jQuery('html, body').animate({ scrollTop: 0 }, 1000);
            window.setTimeout(function () {
                window.location.reload();
            }, 2000);
        };
        profileeditor_options.onError = function (errors) {
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
                jQuery('html, body').animate({ scrollTop: 0 }, 1000);


            }
        };
        var lrUpdateInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrUpdateInterval);
                LRObject.init("profileEditor", profileeditor_options);
            }
        }, 1);
    },

    twoFaContainer: function () {
        var authentication_options = {};
        authentication_options.container = "authentication-container";
        authentication_options.onSuccess = function (response) {
            if (response.Sid) {
                responseHandler(true, Messages.TWO_FA_MSG);
            } else if (response.IsDeleted) {
                responseHandler(true, Messages.TWO_FA_DISABLED_MSG);
                window.setTimeout(function () {
                    window.location.reload();
                }, 2000);
            } else if (typeof response.Uid != 'undefined') {
                responseHandler(true, Messages.TWO_FA_ENABLED_MSG);
                window.setTimeout(function () {
                    window.location.reload();
                }, 2000);
            }
            jQuery('html, body').animate({ scrollTop: 0 }, 1000);
        };
        authentication_options.onError = function (errors) {
            if (errors[0].Description != null) {
                responseHandler(false, errors[0].Description);
            }
        };
        var lrTwoFAInterval = setInterval(function () {
            if (typeof LRObject !== 'undefined') {
                clearInterval(lrTwoFAInterval);
                LRObject.util.ready(function () {
                    LRObject.init("createTwoFactorAuthentication", authentication_options);
                });
            }
        }, 1);
    }
}

require(['jquery', "mage/calendar"], function ($) {
    $(document).ready(function () {
        if (typeof (LRObject) != "undefined") {

            if (typeof registrationSchema !== 'undefined') {
                if (registrationSchema != '') {
                    LRObject.registrationFormSchema = registrationSchema;
                }
            }
            LRObject.$hooks.register('startProcess', function () {
                $('#ciam-loading-image-div').show();
            });

            LRObject.$hooks.register('endProcess', function (name) {
                if (LRObject.options.twoFactorAuthentication === true || LRObject.options.optionalTwoFactorAuthentication === true) {
                    $('#authenticationdiv').show();
                    $('#authentication-container').show();
                }
                if (LRObject.options.phoneLogin === true) {
                    $('#updatephonediv').show();
                    $('#updatephone-container').show();
                }
                if (name === 'resendOTP' && jQuery('#login-container').length > 0) {
                    responseHandler(true, Messages.UPDATE_PHONE_MSG);
                }
                $('#ciam-loading-image-div').hide();
            });

            LRObject.$hooks.call('setButtonsName', {
                removeemail: "Remove"
            });

            LRObject.$hooks.register('afterFormRender', function (actionName) {
                if (actionName == "registration") {
                    showDatePicker();
                }
                if (actionName == 'twofaotp') {
                    responseHandler(true, Messages.TWO_FA_MSG);
                }
                if (actionName == 'socialRegistration') {
                    showformbyId('required_form');
                    showDatePicker();
                }
                $('#ciam-loading-image-div').hide();
            });
        }

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

    setClipboard = function () {
        var value = '';
        jQuery('.code-list').find('span').each(function () {
            value += jQuery(this).html() + "\n";
        });
        var tempInput = document.createElement("textarea");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = value;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        jQuery('.copyMessage').show();
        setTimeout(removeCodeCss, 5000);
    }

    removeCodeCss = function () {
        jQuery('.code-list').find('span').removeAttr('style');
        jQuery('.copyMessage').hide();
    }

    changeIconColor = function () {
        jQuery('.code-list').find('span').css({ 'background-color': '#29d', 'color': '#fff' });
    }

    ciamRedirection = function (token) {
        document.getElementById('ciam-loading-image-div').style.display = 'block';
        var form = document.createElement('form');
        form.action = LocalDomain;
        form.method = 'GET';
        var hiddenToken = document.createElement('input');
        hiddenToken.type = 'hidden';
        hiddenToken.value = token;
        hiddenToken.name = "token";
        form.appendChild(hiddenToken);
        document.body.appendChild(form);
        form.submit();
    }


    lrSetCookie = function (cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    responseHandler = function (isSuccess, message) {
        if (message != null && message != "") {
            var status = (isSuccess) ? 'Success' : 'Error';
            $('#loginradiusmessagediv').show();
            $('#loginradiusmessagediv').html('<div class="' + status + '">' + message + '</div>');
            if (AutoHideTime != "" && AutoHideTime != "0") {
                setTimeout(fadeout, AutoHideTime * 1000);
            }
        } else {
            $('#loginradiusmessagediv').hide();
        }
        $('#ciam-loading-image-div').hide();
    }

    fadeout = function () {
        $('#loginradiusmessagediv').hide();
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