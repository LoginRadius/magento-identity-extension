<?php

namespace LoginRadius\CustomerRegistration\Model\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Customer\Model\CustomerFactory $customerFactory, \Magento\Framework\ObjectManagerInterface $objectManager) {
        $this->_customerFactory = $customerFactory;
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    public function getConfig($section, $config_path) {
        return $this->scopeConfig->getValue(
                        'lr' . $section . '/' . $config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    //Redirection Settings on checkout page
    public function redirectLogin() {
        return $this->getConfig('customerregistration', 'redirection_settings/login_interface');
    }

    public function loginRedirection() {
        return $this->getConfig('customerregistration', 'redirection_settings/login_redirection');
    }

    public function registerRedirection() {
        return;
    }

    public function customLoginRedirection() {
        return $this->getConfig('customerregistration', 'redirection_settings/custom_login_redirection');
    }

    public function customRegisterRedirection() {
        return;
    }

    //Interface Settings
    public function interfaceTitle() {
        return $this->getConfig('customerregistration', 'interface_customization_settings/title');
    }

    public function formValidationMessage() {
        return $this->getConfig('customerregistration', 'interface_customization_settings/form_validation_message');
    }

    public function termsConditions() {
        return $this->getConfig('customerregistration', 'interface_customization_settings/terms_conditions');
    }

    public function delayTime() {
        return $this->getConfig('customerregistration', 'interface_customization_settings/delay_time');
    }

    //Password Customization Settings
    public function minPassword() {
        return $this->getConfig('customerregistration', 'password_customization_settings/min_password');
    }

    public function maxPassword() {
        return $this->getConfig('customerregistration', 'password_customization_settings/max_password');
    }

    //Advance Settings
    public function updateProfile() {
        return $this->getConfig('customerregistration', 'advance_settings/update_profile');
    }

    public function requiredProfile() {
        return;
    }

    public function usernameLogin() {
        return $this->getConfig('customerregistration', 'advance_settings/username_login');
    }

    public function emailVerification() {
        return $this->getConfig('customerregistration', 'advance_settings/email_verification');
    }

    public function loginUponEmailVerification() {
        return $this->getConfig('customerregistration', 'advance_settings/login_upon_email_verification');
    } 

    public function alwaysAskEmailForUnverified() {

        return $this->getConfig('customerregistration', 'advance_settings/always_ask_email_for_unverified');
    }

    public function promptPasswordOnSocialLogin() {
        return $this->getConfig('customerregistration', 'advance_settings/prompt_password_on_social_login');
    }

    public function customJsOptions() {
        return $this->getConfig('customerregistration', 'advance_settings/custom_js_options');
    }

    //Email Settings
    public function forgotEmail() {
        return $this->getConfig('customerregistration', 'email_template_settings/forgot_email');
    }

    public function verificationEmail() {
        if ($this->emailVerification() == '1') {
            return '';
        }
        return $this->getConfig('customerregistration', 'email_template_settings/verification_email');
    }
    public function enableHostedPage() {
        return $this->getConfig('customerregistration', 'redirection_settings/enable_hosted_page');
    }

    //Debug Settings
    public function debug() {
        return $this->getConfig('customerregistration', 'debug_settings/debug');
    }

    public function selectSocialLinkingData($id) {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $changelogName = $resource->getTableName('lr_sociallogin');
        $connection = $resource->getConnection();
        $select = $connection->select()
                ->from(['o' => $changelogName])
                ->where('entity_id="' . $id . '"');
        return $connection->fetchAll($select);
    }
    public function getValueFromStringUrl($url, $parameter_name) {
        $parts = parse_url($url);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (isset($query[$parameter_name])) {
                return $query[$parameter_name];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
