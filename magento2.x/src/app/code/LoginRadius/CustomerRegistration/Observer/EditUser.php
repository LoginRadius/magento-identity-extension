<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\CustomerRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;
use \LoginRadiusSDK\CustomerRegistration\Account\AccountAPI;

global $apiClientClass;
$apiClientClass = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';

class EditUser implements ObserverInterface {

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
        $events = $observer->getEvent();
        $customerData = $events->getRequest()->getPostValue();

        if ($activationHelper->siteApiKey() != ''){
            define('LR_API_KEY', $activationHelper->siteApiKey());
        }
        if ($activationHelper->siteApiSecret() != ''){
            $decrypted_key = $this->lr_secret_encrypt_and_decrypt($activationHelper->siteApiSecret(), $activationHelper->siteApiKey(), 'd');
            define('LR_API_SECRET', $decrypted_key);
        }

        $customer = $observer->getEvent()->getCustomer();
        $this->_date = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime');
        $customer->setDob($this->_date->gmDate('m-d-Y', $this->_date->strToTime($customerData['customer']['dob'])));

        if (isset($_POST['customer']['entity_id'])) {
            $editUserData = '{
            "Email":[
               {
                  "Type":"Primary",
                  "Value":"' . $customerData['customer']['email'] . '"
               }
            ],
            "FirstName":"' . $customerData['customer']['firstname'] . '",
            "LastName":"' . $customerData['customer']['lastname'] . '",
            "Birthdate":"' . $customer->getDob() . '"
            }';

            $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
            $ruleTable = $resource->getTableName('lr_sociallogin');
            $connection = $resource->getConnection();
            $select = $connection->select()->from(['r' => $ruleTable])->where('entity_id=?', $customerData['customer']['entity_id']);
            $output = $connection->fetchAll($select);

            $accountObj = new AccountAPI();

            try {
                $response = $accountObj->updateAccountByUid($editUserData, $output[0]['uid']);
            }
            catch (\LoginRadiusSDK\LoginRadiusException $e) {

                $errorDescription = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : '';
                $this->_messageManager->addError($errorDescription);
            }
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
