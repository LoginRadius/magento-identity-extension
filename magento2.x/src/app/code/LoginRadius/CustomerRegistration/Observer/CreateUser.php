<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\CustomerRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;

global $apiClient_class;
$apiClient_class = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';

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

        $events = $observer->getEvent();
        $customer = $events->getCustomerDataObject();
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=?";

        
       
        $birthDate = date("m-d-Y", strtotime($customer->getDob()));
        
        $newUserData = array(
            'emailid' => $customer->getEmail(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'password' => substr(str_shuffle($chars), 0, 8),
            'gender' => $this->getGenderValue($customer->getGender()),
             'birthdate' => $birthDate
        );



        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
        $userAPI = new \LoginRadiusSDK\CustomerRegistration\UserAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('authentication' => true, 'output_format' => 'json'));
        
        $homeDomain = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl();
        
       

        if (!isset($_POST['customer']['entity_id'])) {

            try {

                $userCreatedata = $userAPI->create($newUserData);
                
                try {
                $rsetPasswordUrl = 'https://api.loginradius.com/raas/client/password/forgot?apikey=' . $activationHelper->siteApiKey() . '&emailid=' . $customer->getEmail() . '&resetpasswordurl=' . $homeDomain . 'customer/account/login/';
               
                $result = \LoginRadiusSDK\LoginRadius::apiClient($rsetPasswordUrl, FALSE, array('output_format' => 'json'));
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    
                $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';

                $this->_messageManager->addError($errorDescription);
            }
                
                try {
                    $this->socialLinkingData($customer->getId(), $userCreatedata);
                   
                } catch (\Exception $e) {
                  
                    
                }
                
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {

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

}
