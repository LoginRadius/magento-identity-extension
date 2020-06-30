<?php

/**
 * Copyright Â© 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\CustomerRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;
use \LoginRadiusSDK\CustomerRegistration\Account\AccountAPI;

global $apiClientClass;
$apiClientClass = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';

class DeleteUser implements ObserverInterface {

    protected $_messageManager;
    protected $_objectManager;

    public function __construct(
    \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
        $customerRegistrationHelper = $this->_objectManager->get("LoginRadius" . "\\" . $activationHelper->getAuthDirectory() . "\Model\Helper\Data");
        $customer = $observer->getEvent()->getCustomer();
        $customerId = $customer->getId();  
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $changelogName = $resource->getTableName('lr_sociallogin');
        $connection = $resource->getConnection();
        $select = $connection->select()->from(['r' => $changelogName])->where('entity_id=?', $customerId);
        $output = $connection->fetchAll($select);
        
        if ($activationHelper->siteApiKey() != ''){
            define('LR_API_KEY', $activationHelper->siteApiKey());
        }
        if ($activationHelper->siteApiSecret() != ''){
            $decrypted_key = $this->lr_secret_encrypt_and_decrypt($activationHelper->siteApiSecret(), $activationHelper->siteApiKey(), 'd');
            define('LR_API_SECRET', $decrypted_key);
        }
           
        /* Delete profile from local db */
        $accountObj = new AccountAPI();
        try {
            $output[0]['uid'] = isset($output[0]['uid']) ? $output[0]['uid'] : '';
            if (!empty($output[0]['uid'])) {

                if($customerRegistrationHelper->deletelrUserAccount() =='1'){                                    
                $accountObj->deleteAccountByUid($output[0]['uid']);                
                }
                
                $changelogName = $resource->getTableName('lr_sociallogin');
                $connection = $resource->getConnection();
                $where = array("entity_id =" . $customerId);
                $connection->delete($changelogName, $where);
           }
        } catch (\LoginRadiusSDK\LoginRadiusException $e) {
               $errorDescription = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : '';
               $this->_messageManager->addError($errorDescription);
        }
    }

    /**
     * Encrypt and decrypt
     *
     * @param string $string string to be encrypted/decrypted
     * @param string $action what to do with this? e for encrypt, d for decrypt
     */     
    function lr_secret_encrypt_and_decrypt( $string, $secretIv, $action) {
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