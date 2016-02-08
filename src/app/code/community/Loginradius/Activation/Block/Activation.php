<?php

class Loginradius_Activation_Block_Activation extends Mage_Core_Block_Template {

    public function siteName() {
        return Mage::getStoreConfig('activation/apisettings/sitename');
    }

    public function apiKey() {
        return Mage::getStoreConfig('activation/apisettings/apikey');
    }

    public function apiSecret() {
        return Mage::getStoreConfig('activation/apisettings/apisecret');
    }

    public function raasEnable() {
        $result = 0;
        if(isset(Mage::getConfig()->getNode()->modules->Loginradius_Customerregistration->active)){
            if((string) Mage::getConfig()->getNode()->modules->Loginradius_Customerregistration->active == "true"){
                $result = 1;
            }            
        }
        return $result;
    }
    function getBlockDir(){
        if($this->raasEnable() == 1){
            return 'customerregistration';
        }
        return 'sociallogin';
    }
}
