<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\CustomerRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;
use \LoginRadiusSDK\CustomerRegistration\Authentication\AuthenticationAPI;

global $apiClientClass;
$apiClientClass = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';


class SaveUserData implements ObserverInterface {

    protected $_messageManager;
    protected $_objectManager;
    protected $_createRaasUserObject;
    protected $_getRaasUserByEmailObject;
    protected $_setRaasUserPasswordObject;
    protected $_changeRaasUserPasswordObject;

    public function __construct(
    \Magento\Framework\App\RequestInterface $request, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $customer = $observer->getEvent()->getCustomerDataObject();
        $request = $this->_request->getParams();
        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
      
      
        if ($activationHelper->siteApiKey() != ''){
            define('LR_API_KEY', $activationHelper->siteApiKey());
        }
        if ($activationHelper->siteApiSecret() != ''){
            $decrypted_key = $this->lr_secret_encrypt_and_decrypt($activationHelper->siteApiSecret(), $activationHelper->siteApiKey(), 'd');
            define('LR_API_SECRET', $decrypted_key);
        }
       
        $authObj = new AuthenticationAPI();

        $editUserData = array(
            'FirstName' => $customer->getFirstname(),
            'LastName' => $customer->getLastname(),
            'Gender' => $customer->getGender(),
            'BirthDate' => $customer->getDob()
        );
        try {
            $userEditdata = $authObj->updateProfileByAccessToken($customerSession->getLoginRadiusAccessToken(), $editUserData);
            /* Edit profile in local db */
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
