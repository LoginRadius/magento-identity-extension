<?php

class Loginradius_Activation_Block_Activation extends Mage_Core_Block_Template {

    public function siteName() {
        return (Mage::getStoreConfig('activation/apisettings/sitename') != null) ? trim(Mage::getStoreConfig('activation/apisettings/sitename')) : '';
    }

    public function apiKey() {
        return (Mage::getStoreConfig('activation/apisettings/apikey') != null) ? trim(Mage::getStoreConfig('activation/apisettings/apikey')) : '';
    }

    public function apiSecret() {
        return (Mage::getStoreConfig('activation/apisettings/apisecret') != null) ? trim(Mage::getStoreConfig('activation/apisettings/apisecret')) : '';
    }

    public function activationErrorMessage() {
        return '<p style ="color:red;">To activate your Extension, please log in to LoginRadius and get API Key & Secret. Web: <b><a href ="http://www.loginradius.com" target = "_blank">www.LoginRadius.com</a></b></p>';
    }

    public function raasEnable() {
        $result = 0;
        if (isset(Mage::getConfig()->getNode()->modules->Loginradius_Customerregistration->active)) {
            if ((string) Mage::getConfig()->getNode()->modules->Loginradius_Customerregistration->active == "true") {
                $result = 1;
            }
        }
        return $result;
    }

    function getBlockDir() {
        if ($this->raasEnable() == 1) {
            return 'customerregistration';
        }
        return 'sociallogin';
    }

}
