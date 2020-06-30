<?php

namespace LoginRadius\Activation\Model\Helper;
use \LoginRadiusSDK\CustomerRegistration\Advanced\ConfigurationAPI;
global $apiClientClass;
$apiClientClass = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';

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
     
    public function getConfigOptions() {
        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');       
        if ($activationHelper->siteApiKey() != '' && $activationHelper->siteApiSecret() != '') {
      
            try {
                $configObject = new ConfigurationAPI();
                return $configObject->getConfigurations();
            }
            catch (LoginRadiusException $e) {
                
            }
        }
    }

    //Activation Settings
  
    public function siteApiKey() {
        return (($this->getConfig('activation', 'activation/site_api') != null) ? trim($this->getConfig('activation', 'activation/site_api')) : '');
    }

    public function siteApiSecret() {
        return (($this->getConfig('activation', 'activation/site_secret') != null) ? trim($this->getConfig('activation', 'activation/site_secret')) : '');
    }
    
    public function siteName() {
        $configurations = $this->getConfigOptions();
        return (isset($configurations->AppName) ? $configurations->AppName : '');
    }

    public function apiRequestSinging() {
        $configurations = $this->getConfigOptions();
        return ((isset($configurations->ApiRequestSigningConfig->IsEnabled) && $configurations->ApiRequestSigningConfig->IsEnabled) ? 'true' : 'false');
    }

    public function phoneLoginEnabled() {
        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');   
        if ($activationHelper->siteApiKey() != ''){
            define('LR_API_KEY', $activationHelper->siteApiKey());
        }
        if ($activationHelper->siteApiSecret() != ''){
            $decrypted_key = $this->lr_secret_encrypt_and_decrypt($activationHelper->siteApiSecret(), $activationHelper->siteApiKey(), 'd');
            define('LR_API_SECRET', $decrypted_key);
        }
        $configurations = $this->getConfigOptions();
        return ((isset($configurations->IsPhoneLogin) && $configurations->IsPhoneLogin) ? $configurations->IsPhoneLogin : false);
    }

    public function customHubDomain() {        
        $configurations = $this->getConfigOptions();
        return ((isset($configurations->CustomDomain) && $configurations->CustomDomain != '') ? $configurations->CustomDomain : '');
    }
    
    public function emailVerificationFlow() {
        $configurations = $this->getConfigOptions();
        return (isset($configurations->EmailVerificationFlow) ? $configurations->EmailVerificationFlow : '');
    }

    public function enableCustomerRegistration() {
        return $this->_moduleManager->isEnabled('LoginRadius_CustomerRegistration');
    }

    public function getAuthDirectory() {
        return 'CustomerRegistration';
    }

    /**
     * Encrypt and decrypt
     *
     * @param string $string string to be encrypted/decrypted
     * @param string $action what to do with this? e for encrypt, d for decrypt
     */     
    public function lr_secret_encrypt_and_decrypt( $string, $secretIv, $action) {
        $secret_key = $secretIv;
        $secret_iv = $secretIv;
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
        if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'd' ) {
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv ); 
        }   
        return $output;
    }
}
