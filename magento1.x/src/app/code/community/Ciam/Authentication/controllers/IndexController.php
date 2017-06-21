<?php

global $apiClient_class;
$apiClient_class = 'Ciam_Authentication_Helper_SDKClient';
require_once Mage::getModuleDir('', 'Ciam_Authentication') . DS . 'Helper' . DS . 'SDKClient.php';

class Ciam_Authentication_IndexController extends Mage_Core_Controller_Front_Action {

    protected $blockObj, $dataObject;

    /**
     * Index action
     *
     * @access public

     * @return void
     */
    public function indexAction() {
        $vtype = $this->getRequest()->getParam('vtype');
        $vtoken = $this->getRequest()->getParam('vtoken');
        $token = $this->getRequest()->getPost('token');
        $this->blockObj = new Ciam_Authentication_Block_Authentication();
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . 'authentication';
        $authenticationObject = new LoginRadiusSDK\CustomerRegistration\Authentication\UserAPI($this->blockObj->apiKey(), $this->blockObj->apiSecret(), array("output_format" => 'json'));
        $socialLoginObject = new LoginRadiusSDK\CustomerRegistration\Social\SocialLoginAPI($this->blockObj->apiKey(), $this->blockObj->apiSecret(), array("output_format" => 'json'));

        $session = Mage::getSingleton('core/session');
        if ($vtype == 'emailverification') {
            try {
                $authenticationObject->verifyEmail($vtoken, $url);
                $session->addSuccess($this->__('Email has been verified successfully. Now you may login.'));
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                $session->addError($this->__($e->getMessage()));
            }
        } elseif ($vtype == 'reset') {
            $password = $this->getRequest()->getPost('vtoken');
            try {
                $authenticationObject->resetPassword($vtoken, $password);
                $session->addSuccess($this->__('Password Reset Successfully'));
            } catch (LoginRadiusSDK\LoginRadiusException $e) {
                $session->addError($this->__($e->getMessage()));
            }
        } elseif (isset($token) && !empty($token)) {
            $accessToken = $socialLoginObject->exchangeAccessToken($token);
            if (isset($accessToken->access_token) && !empty($accessToken->access_token)) {
                try {
                    $userProfileData = $authenticationObject->getProfile($accessToken->access_token);

                    if (!$this->blockObj->user_is_already_login()) {
                        $session->setLoginRadiusToken($accessToken->access_token);
                        $this->authenticateUser($userProfileData);
                    } else {
                        $this->linkUser($userProfileData, $accessToken->access_token);
                    }
                } catch (LoginRadiusSDK\LoginRadiusException $e) {
                    $session->addError(__($e->getMessage()));
                }
            }
        }
        Mage::app()->getResponse()->setRedirect(Mage::helper('customer')->getLoginUrl());
        return;
    }

    public function linkUser($userProfileData, $token) {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $session = Mage::getSingleton('core/session');
        $access_token = $session->getLoginRadiusToken();
        if ($this->blockObj->user_is_already_login()) {
            $userAPIObject = new LoginRadiusSDK\CustomerRegistration\Authentication\UserAPI($this->blockObj->apiKey(), $this->blockObj->apiSecret(), array("output_format" => 'json'));
            $socialLoginAPIObject = new LoginRadiusSDK\CustomerRegistration\Social\SocialLoginAPI($this->blockObj->apiKey(), $this->blockObj->apiSecret(), array("output_format" => 'json'));
            try {
                $userProfileData = $socialLoginAPIObject->getUserProfiledata($token);
                try {
                    $result = $userAPIObject->accountLink($access_token, $token);
                    if (isset($result->IsPosted)) {
                        $this->dataObject->linkingData($customer->getId(), $userProfileData);
                        $session->addMessage(__('Account Linked successFully'));
                    }
                } catch (LoginRadiusSDK\LoginRadiusException $e) {

                    $session->addError(__($e->getMessage()));
                }
            } catch (LoginRadiusSDK\LoginRadiusException $e) {

                $session->addError(__($e->getMessage()));
            }
        }
    }

    public function authenticateUser($userProfileData) {
        $this->dataObject = new Ciam_Authentication_Helper_Data();
        if (isset($userProfileData->ID) && !empty($userProfileData->ID)) {
            if (!$this->blockObj->user_is_already_login()) {

                $uidQuery = $this->dataObject->getCustomerData(array('customer_entity', 'lr_authentication'), array($userProfileData->Uid), 'check_uid');
                $customerEntity = $uidQuery->fetch();

                if (!$customerEntity) {
                    $innerJoinQuery = $this->dataObject->getCustomerData(array('customer_entity', 'lr_authentication'), array($userProfileData->ID), 'id');
                    $customerEntity = $innerJoinQuery->fetch();
                    if (!$customerEntity) {
                        if (isset($userProfileData->Email[0]->Value) && !empty($userProfileData->Email[0]->Value)) {
                            $emailQuery = $this->dataObject->getCustomerData(array('customer_entity'), array($userProfileData->Email[0]->Value), 'email');
                            $customerEntity = $emailQuery->fetch();
                        }
                    }
                }
                if ($customerEntity) {
                    //existing user
                    $this->dataObject->loginUser($customerEntity['entity_id'], $userProfileData);
                } else {
                    //new user
                    $this->dataObject->createUpdateUserProfile($userProfileData);
                }
            }
        }
    }

}
