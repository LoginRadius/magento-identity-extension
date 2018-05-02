<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\CustomerRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;

global $apiClient_class;
$apiClient_class = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';

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
        $customerRegistrationHelper = $this->_objectManager->get("LoginRadius" . "\\" . $activationHelper->getAuthDirectory() . "\Model\Helper\Data");
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        $userAPI = new \LoginRadiusSDK\CustomerRegistration\Authentication\UserAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('authentication' => true, 'output_format' => 'json'));

        $editUserData = array(
            'FirstName' => $customer->getFirstname(),
            'LastName' => $customer->getLastname(),
            'Gender' => $customer->getGender(),
            'BirthDate' => $customer->getDob()
        );
        try {
            $userEditdata = $userAPI->updateProfile($customerSession->getLoginRadiusAccessToken(), $editUserData);
            /* Edit profile in local db */
        } catch (\LoginRadiusSDK\LoginRadiusException $e) {
            if ($customerRegistrationHelper->debug() == '1') {
                $errorDescription = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : '';
                $this->_messageManager->addError($errorDescription);
            }
        }
    }
}
