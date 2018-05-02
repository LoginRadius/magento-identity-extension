<?php

namespace LoginRadius\Activation\Model\Helper;
use \LoginRadiusSDK\Advance\CloudAPI;
global $apiClient_class;
$apiClient_class = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';
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
     
    public function getConfigOptions()
    {
        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');       
        if ($activationHelper->siteApiKey() != '' && $activationHelper->siteApiSecret() != '') {
            try {
                  $cloudObject = new CloudAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('output_format' => 'json'));
                  return $cloudObject->getConfigurationList();
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
        $options = $this->getConfigOptions();
        return (isset($options->AppName) ? $options->AppName : '');
    }
    
    public function emailVerificationFlow() {
        $options = $this->getConfigOptions();
        return (isset($options->EmailVerificationFlow) ? $options->EmailVerificationFlow : '');
    }

    public function enableCustomerRegistration() {
        return $this->_moduleManager->isEnabled('LoginRadius_CustomerRegistration');
    }

    public function getAuthDirectory() {
        return 'CustomerRegistration';
    }
}
