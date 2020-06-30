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
   
    //Redirection Settings
    public function enableIefPage() {
        return $this->getConfig('authentication', 'ief_settings/enable_ief_page');
    }
    
    public function loginRedirection() {
        return $this->getConfig('authentication', 'redirection_settings/login_redirection');
    }

    public function customLoginRedirection() {
        return $this->getConfig('authentication', 'redirection_settings/custom_login_redirection');
    }

    public function checkoutRedirection() {
        return $this->getConfig('authentication', 'redirection_settings/checkout_redirection');
    }

    public function requiredProfile() {
        return;
    }
    
    public function registerRedirection() {
        return;
    }

    public function customRegisterRedirection() {
        return;
    }

 
    //Email Authentication Settings    
    public function promptPasswordOnSocialLogin() {
        return $this->getConfig('authentication', 'email_authentication_settings/prompt_password_on_social_login');
    }

    public function usernameLogin() {
        return $this->getConfig('authentication', 'email_authentication_settings/username_login');
    }

    public function alwaysAskEmailForUnverified() {
        return $this->getConfig('authentication', 'email_authentication_settings/always_ask_email_for_unverified');
    }
    
    public function askRequiredFieldOnTraditionalLogin() {
        return $this->getConfig('authentication', 'email_authentication_settings/ask_required_field_on_traditional_login');
    }

    public function welcomeEmail() {
        return $this->getConfig('authentication', 'email_authentication_settings/welcome_email');
    }

    public function verificationEmail() {
        return $this->getConfig('authentication', 'email_authentication_settings/verification_email');
    }

    public function forgotEmail() {
        return $this->getConfig('authentication', 'email_authentication_settings/forgot_email');
    } 


    //Phone Authentication Settings
    public function existPhoneNo() {
        return $this->getConfig('authentication', 'phone_authentication_settings/exist_phone_no');
    }
    
    public function smsTemplate() {
        return $this->getConfig('authentication', 'phone_authentication_settings/welcome_sms_template');
    }
    
    public function smsTemplatePhoneVerification() {
        return $this->getConfig('authentication', 'phone_authentication_settings/sms_template_phone_verification');
    }

    public function smsTemplateResetPhone() {
        return $this->getConfig('authentication', 'phone_authentication_settings/sms_template_reset_phone');
    }

    public function smsTemplateChangePhone() {
        return $this->getConfig('authentication', 'phone_authentication_settings/sms_template_change_phone_number');
    }

    
    //Advanced Settings 
    public function passwordLessLogin() {
        return $this->getConfig('advancedsettings', 'advanced_settings/passwordless_login');
    }
    
    public function passwordLessLoginEmailTemplate() {
        return $this->getConfig('advancedsettings', 'advanced_settings/passwordless_login_email_template');
    }

    public function passwordLessOtpLogin() {
        return $this->getConfig('advancedsettings', 'advanced_settings/passwordless_otp_login');
    }
    
    public function passwordLessLoginOtpTemplate() {
        return $this->getConfig('advancedsettings', 'advanced_settings/passwordless_otp_login_template');
    }

    public function displayPasswordStrength() {
        return $this->getConfig('advancedsettings', 'advanced_settings/display_password_strength');
    }

    public function notificationTime() {
        return $this->getConfig('advancedsettings', 'advanced_settings/notification_time_out');
    }
    
    public function saveMailInDb() {
        return $this->getConfig('advancedsettings', 'advanced_settings/save_mail_in_db');
    }

    public function deletelrUserAccount() {
        return $this->getConfig('advancedsettings', 'advanced_settings/delete_lr_user_account');
    }

    public function termsConditions() {
        return $this->getConfig('advancedsettings', 'advanced_settings/terms_conditions');
    }

    public function customJsOptions() {
        return $this->getConfig('advancedsettings', 'advanced_settings/custom_js_options');
    }

    public function registrationFormSchema() {
        return $this->getConfig('advancedsettings', 'advanced_settings/registration_form_schema');
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
                echo $query[$parameter_name];
                return $query[$parameter_name];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}