<?php

namespace LoginRadius\CustomerRegistration\Controller\Auth;

use Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient;

require_once('Customhttpclient.php');
global $apiClient_class;
$apiClient_class = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';

class Index extends \Magento\Framework\App\Action\Action {

    /**
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_accessToken;
    protected $_customerSession;
    protected $_accountRedirect;
    protected $_helperCustomerRegistration;
    protected $_helperActivation;
    protected $_messageManager;
    protected $_accountManagement;

    /**
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Account\Redirect $accountRedirect
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Customer\Model\Account\Redirect $accountRedirect
    ) {


        $this->_customerSession = $customerSession;
        $this->_accountRedirect = $accountRedirect;
        parent::__construct($context);
    }

    /**
     * 
     * @param type $path
     * @return type
     */
    public function redirectLoginPage($path) {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($path);
        return $resultRedirect;
    }

    /**
     * Load the page defined in view/frontend/layout/samplenewpage_index_index.xml
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute() {
        $this->_request = $this->_objectManager->get('\Magento\Framework\App\RequestInterface');
        $request = $this->_request->getParams();
        $token = isset($request['token']) && !empty($request['token']) ? trim($request['token']) : '';
        if (empty($token)) {
            return $this->redirectLoginPage('customer/account');
        }
        $this->_messageManager = $this->_objectManager->get('Magento\Framework\Message\ManagerInterface');
        $this->_helperActivation = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
        $this->_accountManagement = $this->_objectManager->get('Magento\Customer\Api\AccountManagementInterface');
        $this->_customerUrl = $this->_objectManager->get('Magento\Customer\Model\Url');
        $this->_storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->_helperCustomerRegistration = $this->_objectManager->get('LoginRadius\\' . $this->_helperActivation->getAuthDirectory() . '\Model\Helper\Data');
        $userAPIObject = new \LoginRadiusSDK\CustomerRegistration\Authentication\UserAPI($this->_helperActivation->siteApiKey(), $this->_helperActivation->siteApiSecret(), array('output_format' => 'json'));
        if (!empty($token)) {

            // Social API's

            $this->_accessToken = !empty($token) ? trim($token) : '';
            try {
                $userProfileData = $userAPIObject->getProfile($this->_accessToken);
                if (isset($userProfileData->Uid)) {
                    /* Checking  provider id in local database */
                    $socialEntityId = $this->getEntityIdbyProfileData($userProfileData);
                    if (isset($socialEntityId['is_verified']) && $socialEntityId['is_verified'] == true) {
                        $this->_customerSession->setLoginRadiusStatus('Error');
                        $this->_customerSession->setLoginRadiusMessage(__('This account is not confirmed. <a href="' . $this->_customerUrl->getEmailConfirmationUrl($socialEntityId['email']) . '"Click here to resend confirmation email.'));
                    }
                    elseif ($this->_customerSession->isLoggedIn()) {
                        return $this->redirectLoginPage('customerregistration/accounts/linking');
                    }
                    else {
                        /* If provider id exists then update user profile */
                        if (!empty($socialEntityId)) {                            
                            /* update query */
                            $customer = $this->updateEntitiesData($socialEntityId, $userProfileData);
                            $this->socialLinkingData($socialEntityId, $userProfileData, true);

                            $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
                            $ruleTable = $resource->getTableName('lr_sociallogin');
                            $connection = $resource->getConnection();
                            if (isset($userProfileData->Identities) && count($userProfileData->Identities) > 0) {
                                foreach ($userProfileData->Identities as $Identity) {
                                    $select = $connection->select()->from(['r' => $ruleTable])
                                        ->where('uid=?', $userProfileData->Uid)
                                        ->where('provider=?', $Identity->Provider);

                                    $output = $connection->fetchAll($select);

                                    if (empty($output)) {
                                        $this->socialLinkingData($socialEntityId, $Identity);
                                    }
                                }
                            }
                            return $this->setCustomerLoggedIn($customer, $userProfileData);
                        }
                        else {
                            /* Checking if email is not empty */                           
                                $customerEmail = $this->getEntityIdbyEmail($userProfileData);
                                if (isset($customerEmail[0]['email']) && !empty($customerEmail[0]['email'])) {
                                    $customer = $this->updateEntitiesData($customerEmail[0]['entity_id'], $userProfileData);
                                    $this->socialLinkingData($customerEmail[0]['entity_id'], $userProfileData);
                                    return $this->setCustomerLoggedIn($customer, $userProfileData);
                                }                            
                                else {
                                    // Register
                                    $customer = $this->saveEntitiesData($userProfileData);
                                    $this->socialLinkingData($customer->getId(), $userProfileData);
                                    return $this->setCustomerLoggedIn($customer, $userProfileData, true);
                                }         
                        }
                    }
                }
            }
            catch (\LoginRadiusSDK\LoginRadiusException $e) {                
                $this->_eventManager->dispatch('lr_logout_sso', array('exception' => $e));
            }
        }return $this->redirectLoginPage('customer/account');
    }

    /**
     * 
     * @param type $email
     * @return type
     */
    function getEntityIdbyEmail($userProfileData) {
        $output = '';
        if (isset($userProfileData->Email[0]->Value) && !empty($userProfileData->Email[0]->Value)) {
        $customerData = $this->_objectManager->get('Magento\Customer\Model\Customer')->getCollection()
            ->addAttributeToSelect('email', 'entity_id')
            ->addAttributeToFilter('email', $userProfileData->Email[0]->Value);
        $output = $customerData->getData();        
        }
        return $output;
    }

    /**
     * 
     * @param type $entity_id
     * @return type
     */
    function checkEntityIdExist($entity_id) {
        $customerData = $this->_objectManager->get('Magento\Customer\Model\Customer')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToFilter('entity_id', $entity_id);
        return $customerData->getData();
    }

    /**
     * 
     * @param type $customerId
     */
    function unlinkSocialAccount($customerId) {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $changelogName = $resource->getTableName('lr_sociallogin');
        $connection = $resource->getConnection();
        $where = array("entity_id =" . $customerId);
        $connection->delete($changelogName, $where);
    }

    /**
     * 
     * @param type $gender
     * @return string
     */
    function getGenderValue($gender) {
        if (in_array($gender, array('M', 'm', 'Male', 'male'))) {
            return '1';
        }
        if (in_array($gender, array('F', 'f', 'Female', 'female'))) {
            return '2';
        }
        return '3';
    }

    /**
     * 
     * @param type $userProfileData
     * @return string
     */
    function getEntityIdbyProfileData($userProfileData) {
        $output = '';
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $ruleTable = $resource->getTableName('lr_sociallogin');
        $connection = $resource->getConnection();
        if (isset($userProfileData->Uid) && !is_null($userProfileData->Uid)) {
            $select = $connection->select()->from(['r' => $ruleTable])->where('uid=?', $userProfileData->Uid);
            $output = $connection->fetchAll($select);
        }
        if (empty($output) || (!isset($output[0]['entity_id']))) {
            $select = $connection->select()->from(['r' => $ruleTable])->where('sociallogin_id=?', $userProfileData->ID);
            $output = $connection->fetchAll($select);
        }
        $entity_id = isset($output[0]['entity_id']) ? $output[0]['entity_id'] : '';
        if (!empty($entity_id)) {
            $customer = $this->checkEntityIdExist($entity_id);

            if (isset($customer[0]['entity_id']) && !empty($customer[0]['entity_id'])) {
                return $customer[0]['entity_id'];
            }
            else {
                $this->unlinkSocialAccount($entity_id);
            }
        }
        return '';
    }

    /**
     * 
     * @param type $userProfileData
     * @param type $isActive
     * @return type
     */
    function saveEntitiesData($userProfileData, $isActive = 1) {
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customer->setFirstname($this->nameMapping($userProfileData, 'FirstName'));
        $customer->setLastname($this->nameMapping($userProfileData, 'LastName'));
        
         if (isset($userProfileData->Email[0]->Value) && !empty($userProfileData->Email[0]->Value)) {               
            $customer->setEmail($userProfileData->Email[0]->Value);           
        } else {            
            $phoneId = isset($userProfileData->PhoneId) ? $userProfileData->PhoneId : $userProfileData->ID;  
            $reandomEmail = $this->getRandomEmail($_SERVER['HTTP_HOST'], $phoneId);
            $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
            $ruleTable = $resource->getTableName('customer_entity');
            $connection = $resource->getConnection();
                            
            $select = $connection->select()->from(['r' => $ruleTable])                
             ->where('email=?', $reandomEmail);
            $output = $connection->fetchAll($select);
            if (!empty($output)) {
                $reandomEmail = $this->getRandomEmail($_SERVER['HTTP_HOST'], $userProfileData->ID);
            }            
            $customer->setEmail($reandomEmail);           
        }         
      
        if ($userProfileData->BirthDate != "") {
            $this->_date = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime');
            $customer->setDob($this->_date->gmDate('d-m-Y', $this->_date->strToTime($userProfileData->BirthDate)));
        }
        if ($userProfileData->Gender != "") {
            $customer->setGender($this->getGenderValue($userProfileData->Gender));
        }
        $customer->setPassword($customer->getRandomConfirmationKey());
        $store_id = $this->_storeManager->getStore()->getStoreId();
        $website_id = $this->_storeManager->getStore()->getWebsiteId();
        $customer->setWebsiteId($website_id);
        $customer->setStoreId($store_id);
        $customer->save();
        return $customer;
    }
    
    public function getRandomEmail($host, $id) {        
        $email_name = substr(str_replace(array(
          "-",
          "/",
          ".",
          "+",
                ), "", $id), -13);
        
        $host = str_replace(".com",'.',$host);
        $email = $email_name . '@' . $host . 'com';
        return $email;
    }

    /**
     * 
     * @param type $userProfileData
     * @param type $parameter
     * @return type
     */
    function nameMapping($userProfileData, $parameter) {
        $output = '';
        if (isset($userProfileData->$parameter) && !empty($userProfileData->$parameter) && $userProfileData->$parameter != null) {
            $output = $userProfileData->$parameter;
        }
        elseif (isset($userProfileData->FirstName) && !empty($userProfileData->FirstName) && $userProfileData->FirstName != null) {
            $output = $userProfileData->FirstName;
        }
        elseif (isset($userProfileData->LastName) && !empty($userProfileData->LastName) && $userProfileData->LastName != null) {
            $output = $userProfileData->LastName;
        }
        elseif (isset($userProfileData->FullName) && !empty($userProfileData->FullName) && $userProfileData->FullName != null) {
            $output = $userProfileData->FullName;
        }
        elseif (isset($userProfileData->Email[0]->Value) && !empty($userProfileData->Email[0]->Value)) {
            $tempOutput = explode('@', $userProfileData->Email[0]->Value);
            $output = isset($tempOutput[0]) ? $tempOutput[0] : '';
        }
        if (empty($output)) {
            $output = $userProfileData->ID;
        }
        return str_replace(array(' '), array(''), $output);
    }

    /**
     * 
     * @param type $entity_id
     * @param type $userProfileData
     * @return type
     */
    function updateEntitiesData($entity_id, $userProfileData) {
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $model = $customer->load($entity_id);
        $model->save();
        return $customer;
    }

    /**
     * 
     * @param type $entity_id
     * @param type $userProfileData
     * @param type $is_update
     */
    function socialLinkingData($entity_id, $userProfileData, $is_update = false) {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $changelogName = $resource->getTableName('lr_sociallogin');
        $connection = $resource->getConnection();
        $userProfileData->Uid = isset($userProfileData->Uid) ? $userProfileData->Uid : '';
        $emailVerified = isset($userProfileData->EmailVerified) ? $userProfileData->EmailVerified : true;
        $data = ['entity_id' => $entity_id, 'uid' => $userProfileData->Uid, 'sociallogin_id' => $userProfileData->ID, 'avatar' => $userProfileData->ImageUrl, 'verified' => $emailVerified, 'status' => 'unblock', 'provider' => $userProfileData->Provider];
        if ($is_update) {
            $connection->update($changelogName, $data, "entity_id ='" . $entity_id . "'&sociallogin_id='" . $userProfileData->ID . "'");
        }
        else {
            $connection->insert($changelogName, $data);
        }
    }

    /**
     * 
     * @param type $customer
     * @param type $userProfileData
     * @param type $is_new
     * @return type
     */
    function setCustomerLoggedIn($customer, $userProfileData, $is_new = false) {
        $userProfileData->Uid = isset($userProfileData->Uid) ? $userProfileData->Uid : '';
        $this->_customerSession->setLoginRadiusId($userProfileData->ID);
        $this->_customerSession->setLoginRadiusAccessToken($this->_accessToken);
        $this->_customerSession->unsLoginRadiusUid();
        $this->_customerSession->setLoginRadiusUid($userProfileData->Uid);
        $socialAPIObject = new \LoginRadiusSDK\CustomerRegistration\Social\SocialLoginAPI($this->_helperActivation->siteApiKey(), $this->_helperActivation->siteApiSecret(), array('output_format' => 'json'));
        $socialProfileData = $socialAPIObject->getUserProfiledata($this->_accessToken, false, 'Provider');
            
        $this->_customerSession->setCurrentProvider($socialProfileData->Provider);    
        $emailVerified = isset($userProfileData->EmailVerified) ? $userProfileData->EmailVerified : true;
        $this->_customerSession->setLoginRadiusEmailVerified($emailVerified);
        $this->_customerSession->setCustomerAsLoggedIn($customer);
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($this->_customerSession->getLoginRadiusRedirection()) {
            $resultRedirect->setPath('checkout');
        }
        else {
            $resultRedirect->setUrl($this->redirectionUrl($is_new));
        }
        return $resultRedirect;
    }

    /**
     * 
     * @param type $is_new
     * @return type
     */
    function redirectionUrl($is_new) {
        $redirection = $this->_helperCustomerRegistration->loginRedirection();
        $request = $this->_request->getParams();
        if (isset($request['redirect_to']) && !empty($request['redirect_to'])) {
            $redirection = $request['redirect_to'];
        }
        else {
            switch ($redirection) {
                case 'custom':
                    $redirection = $this->_helperCustomerRegistration->customLoginRedirection();
                    break;
                case 'account':
                    $redirection = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                        ->getStore()
                        ->getUrl('customer/account');
                    break;
                case 'home':
                    $redirection = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                        ->getStore()
                        ->getBaseUrl();
                    break;
                default :
                    if (!empty($this->_customerSession->getRefererURLData())) {
                        $redirection = $this->_customerSession->getRefererURLData();
                    }
                    else {
                        $redirection = $this->_redirect->getRefererUrl();
                    }
            }
        }
        return $redirection;
    }
}
