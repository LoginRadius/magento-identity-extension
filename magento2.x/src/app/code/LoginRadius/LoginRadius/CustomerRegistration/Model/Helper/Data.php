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
    public function enableRaas() {
        return $this->getConfig('customerregistration', 'redirection_settings/raas_enable');
    }

    public function interfaceSize() {
        if($this->enableRaas() == '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'redirection_settings/interface_size');
    }

    public function numColumns() {
        if($this->enableRaas() == '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'redirection_settings/number_of_columns');
    }

    public function backgroundColor() {
        if($this->enableRaas() == '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'redirection_settings/background_color');
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
        if($this->enableRaas() != '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'interface_customization_settings/form_validation_message');
    }

    public function termsConditions() {
        if($this->enableRaas() != '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'interface_customization_settings/terms_conditions');
    }

    public function delayTime() {
        if($this->enableRaas() != '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'interface_customization_settings/delay_time');
    }

    public function googleRecaptcha() {
        if($this->enableRaas() != '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'interface_customization_settings/google_recaptcha');
    }

    public function interfaceIconSize() {
        return;
    }

    public function interfaceIconRow() {
        return;
    }

    public function interfaceBgColor() {
        return;
    }

    public function rightSide() {
        return;
    }

    public function topLoginPage() {
        return;
    }

    public function belowLoginPage() {
        return;
    }

    public function topRegisterPage() {
        return;
    }

    public function belowRegisterPage() {
        return;
    }

    //Password Customization Settings
    public function minPassword() {
        if($this->enableRaas() != '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'password_customization_settings/min_password');
    }

    public function maxPassword() {
        if($this->enableRaas() != '1'){
            return '';
        }
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
        if($this->enableRaas() != '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'advance_settings/username_login');
    }

    public function emailVerification() {
        if($this->enableRaas() != '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'advance_settings/email_verification');
    }

    public function loginUponEmailVerification() {
        if($this->enableRaas() != '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'advance_settings/login_upon_email_verification');
    }

    public function alwaysAskEmailForUnverified() {

        return $this->getConfig('customerregistration', 'advance_settings/always_ask_email_for_unverified');
    }

    public function promptPasswordOnSocialLogin() {
        if($this->enableRaas() != '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'advance_settings/prompt_password_on_social_login');
    }

    public function customJsOptions() {
        if($this->enableRaas() != '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'advance_settings/custom_js_options');
    }

    //Email Settings
    public function forgotEmail() {
        return $this->getConfig('customerregistration', 'email_template_settings/forgot_email');
    }

    public function verificationEmail() {
        if($this->enableRaas() != '1' || $this->emailVerification() == '1') {
            return '';
        }
        return $this->getConfig('customerregistration', 'email_template_settings/verification_email');
    }

    public function emailRequired() {
        return;
    }

    public function emailPopupTitle() {
        return;
    }

    public function emailPopupMessage() {
        return;
    }

    public function emailPopupErrorMessage() {
        return;
    }

    public function welcomeEmail() {
        return;
    }

    public function welcomeEmailMessage() {
        return;
    }

    public function welcomeEmailOwner() {
        return;
    }

    public function welcomeEmailMessageOwner() {
        return;
    }
    
    public function enableHostedPage() {
        if($this->enableRaas() != '1'){
            return '';
        }
        return $this->getConfig('customerregistration', 'enablehostingpage/enable_hosted_page');
    }
    
    //Debug Settings
    public function debug() {
        return $this->getConfig('customerregistration', 'debug_settings/debug');
    }

    public function generateInterfaceDiv() {
        $output = '<div style="margin:5px"></div><div class="lr_embed_bricks_200 interfacecontainerdiv" id="interfacecontainerdiv" ></div>';
        return $output;
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

    

}
