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
        if (isset($request['changepassword']) && $request['changepassword'] == '1') {
            if (isset($request['oldpassword']) && isset($request['newpassword']) && isset($request['confirmnewpassword'])) {
                if (($customerRegistrationHelper->minPassword() != 0) && ($customerRegistrationHelper->minPassword() > strlen($request['newpassword']))) {
                    $this->_messageManager->addError('The Password field must be at least ' . $customerRegistrationHelper->minPassword() . ' characters in length.');
                } elseif (($customerRegistrationHelper->maxPassword() != 0) && (strlen($request['newpassword']) > $customerRegistrationHelper->maxPassword())) {
                    $this->_messageManager->addError('The Password field must not exceed ' . $customerRegistrationHelper->maxPassword() . ' characters in length.');
                } elseif ($request['newpassword'] !== $request['confirmnewpassword']) {
                    //password not match
                    $this->_messageManager->addError('Password and Confirm Password don\'t match');
                } else {
                    try {
                        $changeUserPassword = $userAPI->changeAccountPassword($customerSession->getLoginRadiusAccessToken(), $request['oldpassword'], $request['newpassword']);
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                        $this->_messageManager->addError($errorDescription);
                    }
                }
            }
        }
    }

}
