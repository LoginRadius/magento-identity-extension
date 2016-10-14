<?php

class Loginradius_Hostedregistration_Block_Hostedregistration extends Loginradius_Sociallogin_Block_Sociallogin {

    public function enableHostedPage() {
        $result = '0';
        if (isset(Mage::getConfig()->getNode()->modules->Loginradius_Customerregistration->active)) {
            if ((string) Mage::getConfig()->getNode()->modules->Loginradius_Customerregistration->active == "true") {
                $result = Mage::getStoreConfig('customerregistration/settings/hostedpage');
            }
        }
        return $result;
    }
}
