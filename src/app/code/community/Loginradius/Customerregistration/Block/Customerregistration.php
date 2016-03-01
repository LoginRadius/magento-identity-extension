<?php

class Loginradius_Customerregistration_Block_Customerregistration extends Loginradius_Sociallogin_Block_Sociallogin {

    public function redirectionAfterLogin() {
        return Mage::getStoreConfig('customerregistration/settings/redirectionafterlogin');
    }

    public function redirectionAfterLoginCustom() {
        return Mage::getStoreConfig('customerregistration/settings/redirectionafterlogincustom');
    }

    public function titleInterface() {
        return Mage::getStoreConfig('customerregistration/socialinterface/title');
    }

    public function updateProfileData() {
        return Mage::getStoreConfig('customerregistration/othersettings/updateprofiledata');
    }

    public function socialLinking() {
        //if ($this->emailVerified() == '1') {
            return '1';
        //}
        //return Mage::getStoreConfig('customerregistration/othersettings/sociallinking');
    }

    public function debugMode() {
        return Mage::getStoreConfig('customerregistration/othersettings/debugMode');
    }

    public function emailVerified() {
        $session = Mage::getSingleton("customer/session");
        $dbEmailVerification = Mage::getStoreConfig('customerregistration/othersettings/emailVerified');
        if ($session->isLoggedIn() && $dbEmailVerification == '2') {
            $emailVerified = $session->getLoginRadiusEmailVerified();
            if ($emailVerified == true) {
                return '0';
            }
            return '1';
        }
        return $dbEmailVerification;
    }

    /**/

    public function validationMessage() {
        return Mage::getStoreConfig('customerregistration/socialinterface/validationmessage');
    }

    public function termsAndCondition() {
        return Mage::getStoreConfig('customerregistration/socialinterface/termsandcondition');
    }

    public function formRenderDelay() {
        return Mage::getStoreConfig('customerregistration/socialinterface/formrenderdelay');
    }

    public function recaptchaKey() {
        return Mage::getStoreConfig('customerregistration/socialinterface/recaptchakey');
    }

    public function passwordOnSocialLogin() {
        if ($this->emailVerified() != '0') {
            return '0';
        }
        return Mage::getStoreConfig('customerregistration/othersettings/passwordonsociallogin');
    }

    public function minPasswordLength() {
        return (int) Mage::getStoreConfig('customerregistration/socialinterface/minpasswordlength');
    }

    public function maxPasswordLength() {
        return (int) Mage::getStoreConfig('customerregistration/socialinterface/maxpasswordlength');
    }

    public function enableUserName() {
        return Mage::getStoreConfig('customerregistration/othersettings/enableusername');
    }

    public function forgotPasswordTemplate() {
        return Mage::getStoreConfig('customerregistration/othersettings/forgotpasswordtemplate');
    }

    public function emailVerificationTemplate() {
        if ($this->emailVerified() == '1') {
            return '';
        }
        return Mage::getStoreConfig('customerregistration/othersettings/emailverificationtemplate');
    }

    public function loginOnEmailVerification() {
        if ($this->emailVerified() == '1') {
            return '0';
        }
        return Mage::getStoreConfig('customerregistration/othersettings/loginonemailverification');
    }

    public function askEmailAlwaysForUnverified() {
        if ($this->emailVerified() == '1') {
            return '0';
        }
        return Mage::getStoreConfig('customerregistration/othersettings/askemailalwaysforunverified');
    }

    public function customRaasOptions() {
        return Mage::getStoreConfig('customerregistration/othersettings/customraasoptions');
    }

    /**/

    public function profilefieldsRequired() {
        return 0;
    }

    public function redirectionAfterRegistration() {
        return $this->redirectionAfterLogin();
    }

    public function redirectionAfterRegistrationCustom() {
        return $this->redirectionAfterLoginCustom();
    }

    public function typeOfInterface() {
        return 1;
    }

    public function iconSize() {
        return 'small';
    }

    public function iconsPerRow() {
        return '';
    }

    public function backgroundColor() {
        return '';
    }

    public function showDefault() {
        return 0;
    }

    public function aboveLogin() {
        return 0;
    }

    public function belowLogin() {
        return 0;
    }

    public function aboveRegister() {
        return 0;
    }

    public function belowRegister() {
        return 0;
    }

    public function emailRequired() {
        return 1;
    }

    public function verificationText() {
        return '';
    }

    public function popupText() {
        return '';
    }

    public function popupError() {
        return '';
    }

    public function notifyUser() {
        return 0;
    }

    public function notifyUserText() {
        return '';
    }

    public function notifyAdmin() {
        return 0;
    }

    public function notifyAdminText() {
        return '';
    }

    public function getSocialLoginContainer() {
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        $apiKey = trim($activationBlockObj->apiKey());
        $apiSecrete = trim($activationBlockObj->apiSecret());
        if ($apiKey == "" && $apiSecrete == "") {
            $result = $activationBlockObj->activationErrorMessage();
        } else {
            $result = '<div style="margin:5px"></div><div class="lr_embed_bricks_200 interfacecontainerdiv" id="interfacecontainerdiv" ></div>';
        }
        return $result;
    }

    /**
     * Get script and css for email required popup
     *
     * @return string
     */
    public function getPopupScriptUrl() {
        return '';
    }

    public function getLoginRadiusInterfaceScript() {
        return parent::getLoginRadiusInterfaceScript() . '<script src="//cdn.loginradius.com/hub/prod/js/LoginRadiusRaaS.js"></script>';
    }

}
