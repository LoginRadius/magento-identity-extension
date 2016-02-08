<?php

class Loginradius_Socialshare_Block_Socialshare extends Mage_Core_Block_Template
{
    public function horizontalShareEnable()
    {
        return Mage::getStoreConfig('socialshare/horizontalsharing/horizontalshareenable');
    }
    public function horizontalSharingTheme()
    {
        return Mage::getStoreConfig('socialshare/horizontalsharing/horizontalsharingtheme');
    }
    public function horizontalShareProviders()
    {
        return Mage::getStoreConfig('socialshare/horizontalsharing/horizontalsharingprovidershidden');
    }
    public function horizontalCounterProviders()
    {
        return Mage::getStoreConfig('socialshare/horizontalsharing/horizontalcounterprovidershidden');
    }
    public function horizontalShareProductPage()
    {
        return Mage::getStoreConfig('socialshare/horizontalsharing/horizontalshareproduct');
    }
    public function apiKey()
    {
        return Mage::getStoreConfig('activation/apisettings/apikey');
    }
    public function verticalShareEnable()
    {
        return Mage::getStoreConfig('socialshare/verticalsharing/verticalshareenable');
    }
    public function verticalAlignment()
    {
        return Mage::getStoreConfig('socialshare/verticalsharing/verticalalignment');
    }
    public function verticalSharingProviders()
    {
        return Mage::getStoreConfig('socialshare/verticalsharing/verticalsharingprovidershidden');
    }
    public function verticalCounterProviders()
    {
        return Mage::getStoreConfig('socialshare/verticalsharing/verticalcounterprovidershidden');
    }
    public function verticalShareProductPage()
    {
        return Mage::getStoreConfig('socialshare/verticalsharing/verticalshareproduct');
    }
    public function verticalSharingTheme()
    {
        return Mage::getStoreConfig('socialshare/verticalsharing/verticalsharingtheme');
    }
    public function getHorizontalSharingInterface(){
        return '<div class="loginRadiusHorizontalSharing"></div>';
    }
    public function getVerticalSharingInterface(){
        return '<div class="loginRadiusVerticalSharing"></div>';
    }
}

