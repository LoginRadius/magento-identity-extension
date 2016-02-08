<?php

class Loginradius_Socialshare_Block_Horizontalsharing extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {

    private $loginRadiusHorizontalSocialshare;

    public function __construct() {
        $this->loginRadiusHorizontalSocialshare = Mage::getBlockSingleton('socialshare/socialshare');
    }

    protected function _toHtml() {
        $content = "";
        if ($this->loginRadiusHorizontalSocialshare->horizontalShareEnable() == "1") {
            $content = $this->loginRadiusHorizontalSocialshare->getHorizontalSharingInterface();
        }
        return $content;
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
    }

}
