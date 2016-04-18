<?php

class Loginradius_Socialshare_Block_Verticalsharing extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {

    private $loginRadiusVerticalSocialshare;

    public function __construct() {
        $this->loginRadiusVerticalSocialshare = Mage::getBlockSingleton('socialshare/socialshare');
    }

    protected function _toHtml() {
        $content = "";
        if ($this->loginRadiusVerticalSocialshare->verticalShareEnable() == "1") {
            $content = $this->loginRadiusVerticalSocialshare->getVerticalSharingInterface();
        }
        return $content;
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
    }

}
