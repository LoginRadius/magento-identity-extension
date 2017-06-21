<?php

namespace LoginRadius\Activation\Model\Helper;

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

    //Activation Settings
    public function siteName() {
        return (($this->getConfig('activation', 'activation/site_name') != null) ? trim($this->getConfig('activation', 'activation/site_name')) : '');
    }

    public function siteApiKey() {
        return (($this->getConfig('activation', 'activation/site_api') != null) ? trim($this->getConfig('activation', 'activation/site_api')) : '');
    }

    public function siteApiSecret() {
        return (($this->getConfig('activation', 'activation/site_secret') != null) ? trim($this->getConfig('activation', 'activation/site_secret')) : '');
    }

    public function enableCustomerRegistration() {
        return $this->_moduleManager->isEnabled('LoginRadius_CustomerRegistration');
    }

    public function getAuthDirectory() {
        return 'CustomerRegistration';
    }

}
