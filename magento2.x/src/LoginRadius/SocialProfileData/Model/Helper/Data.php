<?php

namespace LoginRadius\SocialProfileData\Model\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Customer\Model\CustomerFactory $customerFactory, \Magento\Framework\ObjectManagerInterface $objectManager) {
        $this->_customerFactory = $customerFactory;
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    protected function getConfig($section, $config_path) {
        return $this->scopeConfig->getValue(
                        'lr' . $section . '/' . $config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    //Redirection Settings
    public function basicProfile() {
        return $this->getConfig('socialprofiledata', 'social_profile_data_settings/basic_profile');
    }

    public function extendedLocation() {
        return $this->getConfig('socialprofiledata', 'social_profile_data_settings/extended_location');
    }

    public function extendedProfile() {
        return $this->getConfig('socialprofiledata', 'social_profile_data_settings/extended_profile');
    }

    public function followedCompanies() {
        return $this->getConfig('socialprofiledata', 'social_profile_data_settings/followed_companies');
    }

    public function facebookEvents() {
        return $this->getConfig('socialprofiledata', 'social_profile_data_settings/facebook_profile_events');
    }

    public function statusMessages() {
        return $this->getConfig('socialprofiledata', 'social_profile_data_settings/status_messages');
    }

    public function facebookPosts() {
        return $this->getConfig('socialprofiledata', 'social_profile_data_settings/facebook_posts');
    }

    public function twitterMentions() {
        return $this->getConfig('socialprofiledata', 'social_profile_data_settings/twitter_mentions');
    }

    public function groups() {
        return $this->getConfig('socialprofiledata', 'social_profile_data_settings/groups');
    }

    public function contacts() {
        return $this->getConfig('socialprofiledata', 'social_profile_data_settings/contacts');
    }
    public function likes() {
        return $this->getConfig('socialprofiledata', 'social_profile_data_settings/likes');
    }

}
