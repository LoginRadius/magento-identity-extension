<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\CustomerRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;

class CreateUser implements ObserverInterface {

    protected $_messageManager;
    protected $_objectManager;

    public function __construct(
    \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
    }

    public function socialLinkingData($entity_id, $userProfileData, $is_update = false) {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $changelogName = $resource->getTableName('lr_sociallogin');
        $connection = $resource->getConnection();
        $userProfileData->Uid = isset($userProfileData->Uid) ? $userProfileData->Uid : '';
        $data = ['entity_id' => $entity_id, 'uid' => $userProfileData->Uid, 'sociallogin_id' => $userProfileData->ID, 'avatar' => $userProfileData->ImageUrl, 'verified' => $userProfileData->EmailVerified, 'status' => 'unblock', 'provider' => $userProfileData->Provider];
        if ($is_update) {
            $connection->update($changelogName, $data, "entity_id ='" . $entity_id . "'&sociallogin_id='" . $userProfileData->ID . "'");
        } else {
            $connection->insert($changelogName, $data);
        }
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $events = $observer->getEvent();
        $customer = $events->getCustomerDataObject();
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=?";

        $newUserData = array(
            'emailid' => $customer->getEmail(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'password' => substr(str_shuffle($chars), 0, 8),
            'gender' => $this->getGenderValue($customer->getGender()),
            'birthdate' => $customer->getDob()
        );

        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
        $userAPI = new \LoginRadiusSDK\CustomerRegistration\UserAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('authentication' => true, 'output_format' => 'json'));
        try {
            $userCreatedata = $userAPI->create($newUserData);
            $this->socialLinkingData($customer->getId(), $userCreatedata);
        } catch (\LoginRadiusSDK\LoginRadiusException $e) {
            
        }
        return;
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
