<?php

class Loginradius_Sociallogin_Block_Sociallogin extends Mage_Core_Block_Template {

    public function isLoggedIn() {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function redirectionAfterLogin() {
        return Mage::getStoreConfig('sociallogin/settings/redirectionafterlogin');
    }

    public function redirectionAfterLoginCustom() {
        return Mage::getStoreConfig('sociallogin/settings/redirectionafterlogincustom');
    }

    public function redirectionAfterRegistration() {
        return Mage::getStoreConfig('sociallogin/settings/redirectionafterregistration');
    }

    public function redirectionAfterRegistrationCustom() {
        return Mage::getStoreConfig('sociallogin/settings/redirectionafterregistrationcustom');
    }

    public function typeOfInterface() {
        return Mage::getStoreConfig('sociallogin/socialinterface/customenable');
    }

    public function titleInterface() {
        return Mage::getStoreConfig('sociallogin/socialinterface/title');
    }

    public function iconSize() {
        return (Mage::getStoreConfig('sociallogin/socialinterface/iconsize') == 'small') ? 'small' : '';
    }

    public function iconsPerRow() {
        return Mage::getStoreConfig('sociallogin/socialinterface/iconsperRow');
    }

    public function backgroundColor() {
        return Mage::getStoreConfig('sociallogin/socialinterface/backgroundcolor');
    }

    public function showDefault() {
        return Mage::getStoreConfig('sociallogin/socialinterface/showdefault');
    }

    public function aboveLogin() {
        return Mage::getStoreConfig('sociallogin/socialinterface/abovelogin');
    }

    public function belowLogin() {
        return Mage::getStoreConfig('sociallogin/socialinterface/belowlogin');
    }

    public function aboveRegister() {
        return Mage::getStoreConfig('sociallogin/socialinterface/aboveregister');
    }

    public function belowRegister() {
        return Mage::getStoreConfig('sociallogin/socialinterface/belowregister');
    }

    public function emailRequired() {
        return Mage::getStoreConfig('sociallogin/emailsettings/emailrequired');
    }

    public function verificationText() {
        return Mage::getStoreConfig('sociallogin/emailsettings/verificationText');
    }

    public function popupText() {
        return Mage::getStoreConfig('sociallogin/emailsettings/popupText');
    }

    public function popupError() {
        return Mage::getStoreConfig('sociallogin/emailsettings/popupError');
    }

    public function notifyUser() {
        return Mage::getStoreConfig('sociallogin/emailsettings/notifyUser');
    }

    public function notifyUserText() {
        return Mage::getStoreConfig('sociallogin/emailsettings/notifyUserText');
    }

    public function notifyAdmin() {
        return Mage::getStoreConfig('sociallogin/emailsettings/notifyAdmin');
    }

    public function notifyAdminText() {
        return Mage::getStoreConfig('sociallogin/emailsettings/notifyAdminText');
    }

    public function updateProfileData() {
        return Mage::getStoreConfig('sociallogin/othersettings/updateprofiledata');
    }

    public function socialLinking() {
        return Mage::getStoreConfig('sociallogin/othersettings/sociallinking');
    }

    public function debugMode() {
        return Mage::getStoreConfig('sociallogin/othersettings/debugMode');
    }

    public function profilefieldsRequired() {
        return Mage::getStoreConfig('sociallogin/othersettings/profilefieldsrequired');
    }
    public function emailVerified(){
        return '0';
    }

    public function getAvatar($id)
    {
        $socialLoginConn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $socialLoginTbl = Mage::getSingleton('core/resource')->getTableName("lr_sociallogin");
        $select = $socialLoginConn->query("select avatar from $socialLoginTbl where entity_id = '$id' limit 1");
        if ($rowArray = $select->fetch()) {
            if (($avatar = trim($rowArray['avatar'])) != "") {
                return $avatar;
            }
        }

        return false;
    }
    public function getSocialLoginContainer() {
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        $apiKey = trim($activationBlockObj->apiKey());
        $apiSecrete = trim($activationBlockObj->apiSecret());
        if ($apiKey == "" && $apiSecrete == "") {
            $result = $activationBlockObj->activationErrorMessage();
        } else {
            $result = '<h2>' . $this->titleInterface() . '</h2><div class="interfacecontainerdiv"></div>';
        }
        return $result;
    }

    /**
     * Get script and css for email required popup
     *
     * @return string
     */
    public function getPopupScriptUrl() {
            $jsPath = Mage::getDesign()->getSkinUrl('Loginradius/sociallogin/js/popup.js', array('_area' => 'frontend'));
            $cssPath = Mage::getDesign()->getSkinUrl('Loginradius/sociallogin/css/popup.css', array('_area' => 'frontend'));
            return '<script  type="text/javascript" src="' . $jsPath . '"></script><link rel = "stylesheet" href="' . $cssPath . '" media = "all" />';
    }
    public function getLoginRadiusInterfaceScript(){
        return '<script src="//hub.loginradius.com/include/js/LoginRadius.js"></script>';
    }

}
