<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\CustomerRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveUserData implements ObserverInterface {

    protected $_messageManager;
    protected $_objectManager;
    protected $_createRaasUserObject;
    protected $_getRaasUserByEmailObject;
    protected $_setRaasUserPasswordObject;
    protected $_changeRaasUserPasswordObject;

    public function __construct(
    \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $customer = $observer->getEvent()->getCustomerDataObject();

        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
        $customerRegistrationHelper = $this->_objectManager->get("LoginRadius" . "\\" . $activationHelper->getAuthDirectory() . "\Model\Helper\Data");
        $editUserData = array(
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'emailid' => $customer->getEmail(),
            'gender' => $customer->getGender(),
            'birthdate' => $customer->getDob()
        );
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        $userAPI = new \LoginRadiusSDK\CustomerRegistration\UserAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('authentication' => true, 'output_format' => 'json'));
        try {

            $userEditdata = $userAPI->edit($customerSession->getLoginRadiusId(), $editUserData);
            /* Edit profile in local db */
        } catch (\LoginRadiusSDK\LoginRadiusException $e) {
            if ($customerRegistrationHelper->debug() == '1') {
                $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                $this->_messageManager->addError($errorDescription);
            }
        }
        if (isset($_REQUEST['changepassword']) && $_REQUEST['changepassword'] == '1') {
            $accountAPI = new \LoginRadiusSDK\CustomerRegistration\AccountAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('authentication' => true, 'output_format' => 'json'));
            $postData = $_REQUEST;
            if (isset($postData['emailid']) && !empty($postData['emailid'])) {

                if (($customerRegistrationHelper->minPassword() != 0) && ($customerRegistrationHelper->minPassword() > strlen($postData['password']))) {
                    $this->_messageManager->addError('The Password field must be at least ' . $customerRegistrationHelper->minPassword() . ' characters in length.');
                } elseif (($customerRegistrationHelper->maxPassword() != 0) && (strlen($postData['password']) > $customerRegistrationHelper->maxPassword())) {
                    $this->_messageManager->addError('The Password field must not exceed ' . $customerRegistrationHelper->maxPassword() . ' characters in length.');
                } elseif ($postData['password'] === $postData['confirmpassword']) { //check both password
                    //setpassword
                    $data = array(
                        'accountid' => $customerSession->getLoginRadiusUid(),
                        'password' => trim($postData['password']),
                        'emailid' => trim($postData['emailid'])
                    );
                    try {
                        $setUserPassword = $accountAPI->createUserRegistrationProfile($data);
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                        $this->_messageManager->addError($errorDescription);
                    }
                } else { //password not match
                    $this->_messageManager->addError('Password don\'t match');
                }
            } else if (isset($postData['oldpassword']) && isset($postData['newpassword']) && isset($postData['confirmnewpassword'])) {
                if (($customerRegistrationHelper->minPassword() != 0) && ($customerRegistrationHelper->minPassword() > strlen($postData['newpassword']))) {
                    $this->_messageManager->addError('The Password field must be at least ' . $customerRegistrationHelper->minPassword() . ' characters in length.');
                } elseif (($customerRegistrationHelper->maxPassword() != 0) && (strlen($postData['newpassword']) > $customerRegistrationHelper->maxPassword())) {
                    $this->_messageManager->addError('The Password field must not exceed ' . $customerRegistrationHelper->maxPassword() . ' characters in length.');
                } elseif ($postData['newpassword'] !== $postData['confirmnewpassword']) {
                    //password not match
                    $this->_messageManager->addError('Password and Confirm Password don\'t match');
                } else {
                    try {
                        $changeUserPassword = $accountAPI->changeAccountPassword($customerSession->getLoginRadiusUid(), $postData['oldpassword'], $postData['newpassword']);
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                        $this->_messageManager->addError($errorDescription);
                    }
                }
            }
        }
    }

}
