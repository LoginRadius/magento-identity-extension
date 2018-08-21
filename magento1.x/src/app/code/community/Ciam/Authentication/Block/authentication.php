<?php
global $apiClient_class;
$apiClient_class = 'Ciam_Authentication_Helper_SDKClient';
require_once Mage::getModuleDir('', 'Ciam_Authentication') . DS . 'Helper' . DS . 'SDKClient.php';

class Ciam_Authentication_Block_Authentication extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getAuthenticationLayout() {
        if (!$this->hasData('sociallogin')) {
            $this->setData('sociallogin', Mage::registry('sociallogin'));
        }
        return $this->getData('sociallogin');
    }

    public function user_is_already_login() {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return true;
        }
        return false;
    }

    public function siteName() {
        return Mage::getStoreConfig('authentication/apisettings/sitename');
    }

    public function apiKey() {
        return Mage::getStoreConfig('authentication/apisettings/apikey');
    }

    public function apiSecret() {
        return Mage::getStoreConfig('authentication/apisettings/apisecret');
    }

    public function getLinkedProviders($entity_id) {
        $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_write');
        $loginRadiusQuery = "select * from " . Mage::getSingleton('core/resource')->getTableName('lr_authentication') . " where entity_id = '" . $entity_id . "'";
        $loginRadiusQueryHandle = $loginRadiusConn->query($loginRadiusQuery);
        $result = $loginRadiusQueryHandle->fetchAll();

        $providers = array();
        if (is_array($result)) {
            foreach ($result as $key) {
                $providers[$key['provider']] = $key['id'];
            }
        }

        return $providers;
    }

    public function getSOTT() {
        $sott = new LoginRadiusSDK\Utility\SOTT($this->apiKey(), $this->apiSecret(), array('output_format' => 'json'));
        return $sott->encrypt('10')->Sott;
    }
    
    function getValueFromStringUrl($url, $parameter_name) {
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
