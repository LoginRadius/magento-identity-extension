<?php

namespace LoginRadius\SocialShare\Model\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    public function getConfig($config_path) {
        return $this->scopeConfig->getValue(
                        $config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function horizontalShareEnable() {
        return $this->getConfig('lrsocialshare/horizontal_share/enabled');
    }    

    public function horizontalSharingTheme() {
        return $this->getConfig('lrsocialshare/horizontal_share/theme');
    }

    public function horizontalShareProviders() {
        return $this->getConfig('lrsocialshare/horizontal_share/rearrange_icons');
    }

    public function horizontalCounterProviders() {
        return $this->getConfig('lrsocialshare/horizontal_share/counter_provider_theme');
    }

    public function horizontalShareProductPage() {
        return $this->getConfig('lrsocialshare/horizontal_share/show_product_pages');
    }

    public function verticalShareEnable() {
        return $this->getConfig('lrsocialshare/vertical_share/enabled');
    }

    public function verticalAlignment() {
        return $this->getConfig('lrsocialshare/vertical_share/alignment');
    }

    public function verticalSharingProviders() {
        return $this->getConfig('lrsocialshare/vertical_share/rearrange_icons');
    }

    public function verticalCounterProviders() {
        return $this->getConfig('lrsocialshare/vertical_share/counter_provider_theme');
    }

    public function verticalShareProductPage() {
        return $this->getConfig('lrsocialshare/vertical_share/show_product_pages');
    }

    public function verticalSharingTheme() {
        return $this->getConfig('lrsocialshare/vertical_share/theme');
    }

    public function getHorizontalSharingInterface() {

        return '<div style="z-index: 1000000;" class="lr_horizontal_share"></div>';
    }

    public function getVerticalSharingInterface() {
        return '<div style="z-index: 1000000;" class="lr_vertical_share"></div>';
    }

    public function enableMobileSharing() {
        return $this->getConfig('lrsocialshare/advance_setting/enabledMobile');
    }
    
    public function getDesiredEmailMessage() {
        return $this->getConfig('lrsocialshare/advance_setting/desired_email');
    }

    public function getEmailSubject() {
        return $this->getConfig('lrsocialshare/advance_setting/email_sbject');
    }

    public function getEmailContentReadOnly() {
        return $this->getConfig('lrsocialshare/advance_setting/email_content_read_only');
    }

    public function getShortUrl() {
        return $this->getConfig('lrsocialshare/advance_setting/short_url');
    }

    public function getTotalShare() {
        return $this->getConfig('lrsocialshare/advance_setting/total_share');
    }

    public function getSingleWindowPopUp() {
        return $this->getConfig('lrsocialshare/advance_setting/single_window');
    }

    public function getTwitterMention() {
        return $this->getConfig('lrsocialshare/advance_setting/twitter_mention');
    }

    public function getTwitterHashTag() {
        return $this->getConfig('lrsocialshare/advance_setting/twitter_hash_tag');
    }

    public function getFacebookAppId() {
        return $this->getConfig('lrsocialshare/advance_setting/facebook_app_id');
    }

    public function getPopUpHeight() {
        return $this->getConfig('lrsocialshare/advance_setting/popup_height');
    }

    public function getPopUpWidth() {
        return $this->getConfig('lrsocialshare/advance_setting/popup_width');
    }

    public function getCustomOption() {
        return $this->getConfig('lrsocialshare/advance_setting/custom_option');
    }

}
