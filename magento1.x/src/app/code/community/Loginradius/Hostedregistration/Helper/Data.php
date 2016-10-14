<?php

class Loginradius_Hostedregistration_Helper_Data extends Mage_Customer_Helper_Data {

    function __construct() {
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        $this->siteName = $activationBlockObj->siteName();
        $this->apiKey = $activationBlockObj->apiKey();
    }

    public function getLoginUrl() {
        return 'https://' . $this->siteName . '.hub.loginradius.com/auth.aspx?action=login&return_url=' . $this->getCallbackUrl();
    }

    public function getRegisterUrl() {
        return 'https://' . $this->siteName . '.hub.loginradius.com/auth.aspx?action=register&return_url=' . $this->getCallbackUrl();
    }

    public function getForgotPasswordUrl() {
        return 'https://' . $this->siteName . '.hub.loginradius.com/auth.aspx?action=forgotpassword&return_url=' . $this->getCallbackUrl();
    }
    public function getProfileUrl() {
        $getCurrentUrl = Mage::helper('core/url')->getCurrentUrl();
        return 'https://' . $this->siteName . '.hub.loginradius.com/profile.aspx?return_url=' . $getCurrentUrl;
    }
    public function getLogoutUrl() {
        $getLogoutUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."customer/account/logoutSuccess?islogout=true";
        return 'https://' . $this->siteName . '.hub.loginradius.com/auth.aspx?action=logout&return_url=' . $getLogoutUrl;
    }    
    
    public function getCallbackUrl() {
        $loginRadiusCallback = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . "sociallogin";
        if (strpos(Mage::helper('core/url')->getCurrentUrl(), 'https://') !== false) {
            $loginRadiusCallback = str_replace('http://', 'https://', $loginRadiusCallback);
        }
        return $loginRadiusCallback;
    }

}
