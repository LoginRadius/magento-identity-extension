<?php

class Loginradius_Socialshare_Block_Socialshare extends Mage_Core_Block_Template {

    public function horizontalShareEnable() {
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        if ($activationBlockObj->apiKey() != '' && $activationBlockObj->apiSecret() != '') {
            return Mage::getStoreConfig('socialshare/horizontalsharing/horizontalshareenable');
        }
        return '0';
    }

    public function horizontalSharingTheme() {
        return Mage::getStoreConfig('socialshare/horizontalsharing/horizontalsharingtheme');
    }

    public function horizontalShareProviders() {
        return Mage::getStoreConfig('socialshare/horizontalsharing/horizontalsharingprovidershidden');
    }

    public function horizontalCounterProviders() {
        return Mage::getStoreConfig('socialshare/horizontalsharing/horizontalcounterprovidershidden');
    }

    public function horizontalShareProductPage() {
        return Mage::getStoreConfig('socialshare/horizontalsharing/horizontalshareproduct');
    }

    public function verticalShareEnable() {
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        if ($activationBlockObj->apiKey() != '' && $activationBlockObj->apiSecret() != '') {
            return Mage::getStoreConfig('socialshare/verticalsharing/verticalshareenable');
        }
        return '0';
    }

    public function verticalAlignment() {
        return Mage::getStoreConfig('socialshare/verticalsharing/verticalalignment');
    }

    public function verticalShareProviders() {
        return Mage::getStoreConfig('socialshare/verticalsharing/verticalsharingprovidershidden');
    }

    public function verticalCounterProviders() {
        return Mage::getStoreConfig('socialshare/verticalsharing/verticalcounterprovidershidden');
    }

    public function verticalShareProductPage() {
        return Mage::getStoreConfig('socialshare/verticalsharing/verticalshareproduct');
    }

    public function verticalSharingTheme() {
        return Mage::getStoreConfig('socialshare/verticalsharing/verticalsharingtheme');
    }

    public function getHorizontalSharingInterface() {
        return '<div class="openSocialShareHorizontalSharing"></div>';
    }

    public function getVerticalSharingInterface() {
        return '<div class="openSocialShareVerticalSharing"></div>';
    }

    public function getSharingInterface($class) {
        $output = '<div class="' . $class . '"';
        $current_product = Mage::registry('current_product');
        if (is_object($current_product)) {
            $productid = $current_product->getId();
            if ($productid != '') {
                $_product = Mage::getModel('catalog/product')->load($productid);
                if ($_product->getShortDescription() != '') {
                    $output .= ' data-share-description="' . $_product->getShortDescription() . '"';
                }
                if ($_product->getImageUrl() != '') {
                    $output .= ' data-share-imageurl="' . $_product->getImageUrl() . '"';
                }
                if ($_product->getProductUrl() != '') {
                    $output .= ' data-share-url="' . $_product->getProductUrl() . '"';
                }
                if ($_product->getName() != '') {
                    $output .= ' data-share-title="' . $_product->getName() . '"';
                }
            }
        }
        $output .= '></div>';
        return $output;
    }

    //advance section
    public function isMobileFriendly() {
        return (Mage::getStoreConfig('socialshare/advancesharing/ismobile') == '1') ? "true" : "false";
    }

    public function emailReadOnly() {
        return (Mage::getStoreConfig('socialshare/advancesharing/emailreadonly') == '1') ? "true" : "false";
    }

    public function emailSubject() {
        return Mage::getStoreConfig('socialshare/advancesharing/emailsubject');
    }

    public function emailMessage() {
        return Mage::getStoreConfig('socialshare/advancesharing/emailmessage');
    }

    public function shortUrl() {
        return (Mage::getStoreConfig('socialshare/advancesharing/shorturl') == '1') ? "true" : "false";
    }

    public function totalShare() {
        return (Mage::getStoreConfig('socialshare/advancesharing/totalshare') == '1') ? "true" : "false";
    }

    public function samePopup() {
        return (Mage::getStoreConfig('socialshare/advancesharing/samepopup') == '1') ? "true" : "false";
    }

    public function customPopup() {
        return Mage::getStoreConfig('socialshare/advancesharing/custompopup');
    }

    public function popupHeight() {
        if ($this->customPopup() == '1') {
            return (int) Mage::getStoreConfig('socialshare/advancesharing/popupheight');
        }
        return '';
    }

    public function popupWidth() {
        if ($this->customPopup() == '1') {
            return (int) Mage::getStoreConfig('socialshare/advancesharing/popupwidth');
        }
        return '';
    }

    public function twitterMention() {
        return Mage::getStoreConfig('socialshare/advancesharing/twittermention');
    }

    public function twitterHash() {
        return Mage::getStoreConfig('socialshare/advancesharing/twitterhash');
    }

    public function facebookAppId() {
        return Mage::getStoreConfig('socialshare/advancesharing/facebookappid');
    }

    public function customOptions() {
        return Mage::getStoreConfig('socialshare/advancesharing/customoptions');
    }

    public function getSelectedSocialProvider($interface, $type) {
        $providers = '';
        if ($type == 'counter') {
            $providers .= "widgets: { top: [\"";
            if ($interface == 'vertical') {
                $providers .= str_replace(',', '", "', $this->verticalCounterProviders());
            } elseif ($interface == 'horizontal') {
                $providers .= str_replace(',', '", "', $this->horizontalCounterProviders());
            }
            $providers .= "\"]},";
        } else if ($type == 'share') {
            $providers .= "providers: { top: [\"";
            if ($interface == 'vertical') {
                $providers .= str_replace(',', '", "', $this->verticalShareProviders());
            } elseif ($interface == 'horizontal') {
                $providers .= str_replace(',', '", "', $this->horizontalShareProviders());
            }
            $providers .= "\"]},";
        }
        return $providers;
    }

}
