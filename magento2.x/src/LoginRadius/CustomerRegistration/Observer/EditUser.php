<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\CustomerRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;

global $apiClient_class;
$apiClient_class = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';

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
        $customerRegistrationHelper = $this->_objectManager->get("LoginRadius" . "\\" . $activationHelper->getAuthDirectory() . "\Model\Helper\Data");
        if($customerRegistrationHelper->enableRaas() != '1'){
            return ;
        }
        $events = $observer->getEvent();
        $customerData = $events->getRequest()->getPostValue();

        $customer = $observer->getEvent()->getCustomer();
        $this->_date = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime');
        $customer->setDob($this->_date->gmDate('m-d-Y', $this->_date->strToTime($customerData['customer']['dob'])));

        if (isset($_POST['customer']['entity_id'])) {
            $editUserData = array(
                'firstname' => $customerData['customer']['firstname'],
                'lastname' => $customerData['customer']['lastname'],
                'birthdate' => $customer->getDob()
            );

            $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
            $ruleTable = $resource->getTableName('lr_sociallogin');
            $connection = $resource->getConnection();
            $select = $connection->select()->from(['r' => $ruleTable])->where('entity_id=?', $customerData['customer']['entity_id']);
            $output = $connection->fetchAll($select);

            $userAPI = new \LoginRadiusSDK\CustomerRegistration\UserAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('authentication' => true, 'output_format' => 'json'));
            $accountApi = new \LoginRadiusSDK\CustomerRegistration\AccountAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('authentication' => true, 'output_format' => 'json'));

            /* Getting Raas Profile By User Profile by UID Api */
            $getRaasProfileEmailByUid = $accountApi->getAccounts($output[0]['uid']);
            $checkProvider = '';
            foreach ($getRaasProfileEmailByUid as $key => $value) {
                if ($getRaasProfileEmailByUid[$key]->Provider == 'RAAS') {
                    $emailRaas = $getRaasProfileEmailByUid[$key]->Email[0]->Value;
                    $checkProvider = 'true';
                }
            }

            if ($checkProvider == 'true') {
                if ($customerData['customer']['email'] != $emailRaas) {

                    /* Adding New Email by Add/Remove Email Api */
                    $addEmailData = array('EmailId' => $customerData['customer']['email'], 'EmailType' => 'Primary');

                    try {

                        $addEmailRaas = $accountApi->userAdditionalEmail($output[0]['uid'], 'add', $addEmailData);
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {


                        /* Getting Raas Profile By User Profile by UID Api */
                        $getRaasProfileByUid = $accountApi->getAccounts($output[0]['uid']);

                        $providerStatus = '';
                        foreach ($getRaasProfileByUid as $key => $value) {
                            if ($getRaasProfileByUid[$key]->Provider == 'RAAS') {

                                $providerStatus = 'true';
                            }
                        }

                        if ($providerStatus == 'true') {
                            $getRaasProfile = $userAPI->getProfileByID($output[0]['sociallogin_id']);

                            /* Updating email in local database */
                            $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
                            $updateRuleTable = $resource->getTableName('customer_entity');
                            $updateGridTable = $resource->getTableName('customer_grid_flat');
                            $updateConnection = $resource->getConnection();
                            $data = array("email" => $getRaasProfile->Email[0]->Value);

                            $updateConnection->update($updateRuleTable, $data, "entity_id ='" . $output[0]['entity_id'] . "'");
                            $updateConnection->update($updateGridTable, $data, "entity_id ='" . $output[0]['entity_id'] . "'");

                           

                            $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                            $this->_messageManager->addError($errorDescription);
                        } else {

                            $getRaasProfile = $userAPI->getProfileByID($output[0]['sociallogin_id']);

                            /* Updating email in local database */
                            $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
                            $updateRuleTable = $resource->getTableName('customer_entity');
                            $updateGridTable = $resource->getTableName('customer_grid_flat');
                            $updateConnection = $resource->getConnection();
                            $data = array("email" => $getRaasProfile->Email[0]->Value);

                            $updateConnection->update($updateRuleTable, $data, "entity_id ='" . $output[0]['entity_id'] . "'");
                            $updateConnection->update($updateGridTable, $data, "entity_id ='" . $output[0]['entity_id'] . "'");



                            $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                            $this->_messageManager->addError('Please Reset Set Password then change the email id.');
                        }
                    }


                    if (isset($addEmailRaas->isPosted) && ($addEmailRaas->isPosted == 'true')) {

                        // $getRaasProfile = $userAPI->getProfileByID($output[0]['sociallogin_id']);
                        $getRaasProfileUid = $accountApi->getAccounts($output[0]['uid']);

                        foreach ($getRaasProfileUid as $key => $value) {
                            if ($getRaasProfileUid[$key]->Provider == 'RAAS') {
                                $raasEmail = $getRaasProfileUid[$key]->Email[0]->Value;
                            }
                        }

                        /* Removing Old Email by Add/Remove Email Api */
                        $removeEmailData = array('EmailId' => $raasEmail, 'EmailType' => 'Primary');

                        try {

                            $removeEmail = $accountApi->userAdditionalEmail($output[0]['uid'], 'remove', $removeEmailData);
                            try {

                                $getUserDataByEmail = $userAPI->getProfileByEmail($customerData['customer']['email']);

                                try {

                                    $userEditdata = $userAPI->edit($getUserDataByEmail[0]->ID, $editUserData);
                                } catch (\LoginRadiusSDK\LoginRadiusException $e) {

                                    $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                                    $this->_messageManager->addError($errorDescription);
                                }
                            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                                
                            }
                        } catch (\LoginRadiusSDK\LoginRadiusException $e) {

                            $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                            $this->_messageManager->addError($errorDescription);
                        }
                    }
                } else {
                    
                    try {

                        $getUserDataByEmail = $userAPI->getProfileByEmail($customerData['customer']['email']);

                        try {

                            $userEditdata = $userAPI->edit($getUserDataByEmail[0]->ID, $editUserData);
                        } catch (\LoginRadiusSDK\LoginRadiusException $e) {

                            $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                            $this->_messageManager->addError($errorDescription);
                        }
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        
                    }
                }
            } else {
                
                try {
                    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=?";
                    $data = array('accountid' => $output[0]['uid'], 'emailid' => $customerData['customer']['email'], 'password' => substr(str_shuffle($chars), 0, 8));
                   
                    $accountApi->createUserRegistrationProfile($data);
                    

                    /* Updating Basic profile */
                    try {

                        $getUserDataByEmail = $userAPI->getProfileByEmail($customerData['customer']['email']);

                        try {

                            $userEditdata = $userAPI->edit($getUserDataByEmail[0]->ID, $editUserData);
                        } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                           
                            $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                            $this->_messageManager->addError($errorDescription);
                        }
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        
                        $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                            $this->_messageManager->addError($errorDescription);
                    }
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                   
                    $getRaasProfile = $userAPI->getProfileByID($output[0]['sociallogin_id']);

                            /* Updating email in local database */
                            $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
                            $updateRuleTable = $resource->getTableName('customer_entity');
                            $updateGridTable = $resource->getTableName('customer_grid_flat');
                            $updateConnection = $resource->getConnection();
                            $data = array("email" => $getRaasProfile->Email[0]->Value);

                            $updateConnection->update($updateRuleTable, $data, "entity_id ='" . $output[0]['entity_id'] . "'");
                            $updateConnection->update($updateGridTable, $data, "entity_id ='" . $output[0]['entity_id'] . "'");
                    
                    //$errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                   $this->_messageManager->addError('Not able to change the email of social user.');
                }
                return;
            }
        }
    }

}
