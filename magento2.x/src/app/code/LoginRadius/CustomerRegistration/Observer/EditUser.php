<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\CustomerRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;

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

        $userAPI = new \LoginRadiusSDK\CustomerRegistration\UserAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('authentication' => true, 'output_format' => 'json'));
        $getUserDataByEmail = $userAPI->getProfileByEmail($customerData['customer']['email']);

        $editUserData = array(
            'firstname' => $customerData['customer']['firstname'],
            'lastname' => $customerData['customer']['lastname'],
            'birthdate' => $customerData['customer']['dob']
        );

        try {
            $userEditdata = $userAPI->edit($getUserDataByEmail[0]->ID, $editUserData);
        } catch (\LoginRadiusSDK\LoginRadiusException $e) {

        }
    }

}
