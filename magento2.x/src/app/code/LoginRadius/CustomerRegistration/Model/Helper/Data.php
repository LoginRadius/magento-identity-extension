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
     public function termsConditions() {
        return $this->getConfig('customerregistration', 'interface_customization_settings/terms_conditions');
    }

    //Password Customization Settings
    public function displayPasswordStrength() {
        return $this->getConfig('customerregistration', 'password_customization_settings/display_password_strength');
    }
 
    //Email Settings
    public function welcomeEmail() {
        return $this->getConfig('customerregistration', 'email_template_settings/welcome_email');
    }
    public function forgotEmail() {
        return $this->getConfig('customerregistration', 'email_template_settings/forgot_email');
    }

    public function verificationEmail() {
        return $this->getConfig('customerregistration', 'email_template_settings/verification_email');
    }
    
    public function instantLink() {
        return $this->getConfig('customerregistration', 'email_template_settings/instant_link');
    }
    

    //Advance Settings    
    public function enableHostedPage() {
        return $this->getConfig('customerregistration', 'redirection_settings/enable_hosted_page');
    }
    
    public function requiredProfile() {
        return;
    }

    public function usernameLogin() {
        return $this->getConfig('customerregistration', 'advance_settings/username_login');
    }
    
    public function notificationTime() {
        return $this->getConfig('customerregistration', 'advance_settings/notification_time_out');
    }

    public function alwaysAskEmailForUnverified() {
        return $this->getConfig('customerregistration', 'advance_settings/always_ask_email_for_unverified');
    }
    
    public function instantLinkLogin() {
        return $this->getConfig('customerregistration', 'advance_settings/instant_link_login');
    }
    
    public function promptPasswordOnSocialLogin() {
        return $this->getConfig('customerregistration', 'advance_settings/prompt_password_on_social_login');
    }

    public function customJsOptions() {
        return $this->getConfig('customerregistration', 'advance_settings/custom_js_options');
    }
        
    public function askRequiredFieldOnTraditionalLogin() {
        return $this->getConfig('customerregistration', 'advance_settings/ask_required_field_on_traditional_login');
    }
 
    //Phone Login Settings
    public function existPhoneNo() {
        return $this->getConfig('customerregistration', 'phone_login_settings/exist_phone_no');
    }
    
    public function smsTemplate() {
        return $this->getConfig('customerregistration', 'phone_login_settings/sms_template');
    }
    
    public function smsTemplatePhoneVerification() {
        return $this->getConfig('customerregistration', 'phone_login_settings/sms_template_phone_verification');
    }
    
    
    public function instantOtpLogin() {
        return $this->getConfig('customerregistration', 'phone_login_settings/instant_otp_login');
    }
    
    public function instantOtp() {
        return $this->getConfig('customerregistration', 'phone_login_settings/instant_otp');
    }
        
    //Two Factor Settings
    public function smsTemplate2fa() {
        return $this->getConfig('customerregistration', 'two_fa_settings/sms_template_2fa');
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