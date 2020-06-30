<?php

/**
 * Copyright Â© 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\CustomerRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;
use \LoginRadiusSDK\CustomerRegistration\Account\AccountAPI;
use \LoginRadiusSDK\CustomerRegistration\Authentication\AuthenticationAPI;

global $apiClientClass;
$apiClientClass = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';

class CreateUser implements ObserverInterface {

    protected $_messageManager;
    protected $_objectManager;

    public function __construct(
    \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
    }

    public function socialLinkingData($entity_id, $userProfileData) {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $changelogName = $resource->getTableName('lr_sociallogin');
        $connection = $resource->getConnection();
        $userProfileData->Uid = isset($userProfileData->Uid) ? $userProfileData->Uid : '';
        $data = ['entity_id' => $entity_id, 'uid' => $userProfileData->Uid, 'sociallogin_id' => $userProfileData->ID, 'avatar' => $userProfileData->ImageUrl, 'verified' => $userProfileData->EmailVerified, 'status' => 'unblock', 'provider' => $userProfileData->Provider];
        $connection->insert($changelogName, $data);
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        
        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
        $events = $observer->getEvent();
        $customer = $events->getCustomerDataObject();
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=?";
        $birthDate = date("m-d-Y", strtotime($customer->getDob()));
        $newUserData = '{
            "Email":[
               {
                  "Type":"Primary",
                  "Value":"'.$customer->getEmail().'"
               }
            ],
            "Password":"'.substr(str_shuffle($chars), 0, 8).'",
            "FirstName":"'.$customer->getFirstname().'",
            "LastName":"'.$customer->getLastname().'",
            "Gender":"'.$this->getGenderValue($customer->getGender()).'",
            "Birthdate":"'.$birthDate.'"
            }';  
              
        if ($activationHelper->siteApiKey() != ''){
            define('LR_API_KEY', $activationHelper->siteApiKey());
        }
        if ($activationHelper->siteApiSecret() != ''){
            $decrypted_key = $this->lr_secret_encrypt_and_decrypt($activationHelper->siteApiSecret(), $activationHelper->siteApiKey(), 'd');
            define('LR_API_SECRET', $decrypted_key);
        }

        $authObj = new AuthenticationAPI();
        $accountObj = new AccountAPI();
        $homeDomain = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl();
        if (!isset($_POST['customer']['entity_id'])) {          
            try {
                $userCreatedata = $accountObj->createAccount($newUserData);                
                try {
                    $resetPasswordUrl = $homeDomain . 'customer/account/login/';
                    $result = $authObj->forgotPassword($customer->getEmail(), $resetPasswordUrl);                  
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    $errorDescription = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : '';
                    $this->_messageManager->addError($errorDescription);
                }
                try {
                    $this->socialLinkingData($customer->getId(), $userCreatedata);
                } catch (\Exception $e) {
                    $this->_messageManager->addError('error is occoured');
                }
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                $this->_messageManager->addError('error is occouredff');
            }
            return;
        }
    }

    function getGenderValue($gender) {
        if ($gender == '1') {
            return 'M';
        } elseif ($gender == '2') {
            return 'F';
        }
        return 'U';
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