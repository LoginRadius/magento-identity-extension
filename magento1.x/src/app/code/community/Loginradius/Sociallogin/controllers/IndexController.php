<?php

Mage::app('default');

/**
 * Class Loginradius_Sociallogin_IndexController this is the controller where loginradius login and registration takes place
 */
class Loginradius_Sociallogin_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * handle social login functionlaity
     */
    function indexAction() {
        $token = $this->getRequest()->getParam('token');
        $getBlockDir = Mage::getBlockSingleton('activation/activation')->getBlockDir();
        $this->blockObj = Mage::getBlockSingleton($getBlockDir . '/' . $getBlockDir);
        $this->dataObject = Mage::helper('sociallogin/Data');
        $this->loginRadiusPopErr = $this->blockObj->popupError();
        if (!empty($token)) {
            $this->tokenHandler($token);
        } else {
            $requiredFieldPopupSubmit = $this->getRequest()->getPost('EmailPopupOkButton');
            $requiredFieldPopupCancel = $this->getRequest()->getPost('LoginRadiusPopupCancel');
            if (($requiredFieldPopupSubmit == 'Submit') || ($requiredFieldPopupCancel == 'Cancel')) {
                $this->popupHandler();
            } else {                
                $activation = $this->getRequest()->getParam('lractivation');
                $email = $this->getRequest()->getParam('email');
                $redirect = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
                if(!empty($activation) && !empty($email)){
                    $customerQuery = $this->dataObject->getCustomerData(array('customer_entity'), array($email), 'email');
                    $customerData = $customerQuery->fetch();
                    $socialQuery = $this->dataObject->getCustomerData(array('lr_sociallogin'), array($activation), 'activation');
                    $socialData = $socialQuery->fetch();
                    $session = Mage::getSingleton('core/session');
                    if(isset($customerData['entity_id']) && isset($socialData['entity_id']) && $socialData['entity_id'] == $customerData['entity_id']){
                        $lrData = array(
                            'vkey' => '',
                            'verified' => 1,
                        );
                        $condition = array(
                            'entity_id = ?' => (int) $customerData['entity_id'],
                            'vkey = ?' => $activation,
                        );
                        $this->dataObject->SocialLoginInsert('lr_sociallogin', $lrData, true, $condition);
                        $session->addSuccess(__('Your Account is verified success. Please Login with your account.'));
                        $redirect = Mage::helper('customer')->getLoginUrl();
                    }else{
                        $session->addError(__('Verification link has been expired. Please get new link from <a href="' . Mage::helper('customer')->getForgotPasswordUrl() . '">here</a>.'));
                    }                    
                }
                header("Location: " . $redirect); // redirect to index page
                exit();
            }
        }
    }

    /**
     * handle popup request
     * 
     * @return boolean
     */
    function popupHandler() {
        $requiredFieldPopupSubmit = $this->getRequest()->getPost('EmailPopupOkButton');
        $requiredFieldPopupCancel = $this->getRequest()->getPost('LoginRadiusPopupCancel');
        if ($requiredFieldPopupSubmit == 'Submit') {
            $socialLoginProfileData = Mage::getSingleton('core/session')->getSocialLoginData();
            if (!isset($socialLoginProfileData->ID)) {
                $session = Mage::getSingleton('customer/session');
                $session->addError(__('Your session has been expied. Please try again.'));
                $this->_redirectUrl($this->dataObject->getSamePage());
                return;
            }
            $sessionUserId = $socialLoginProfileData->ID;
            $loginRadiusPopProvider = $socialLoginProfileData->Provider;
            $loginRadiusAvatar = $socialLoginProfileData->ThumbnailImageUrl;
            if (!empty($sessionUserId)) {
                $loginRadiusProfileData = array();
                // address
                if (isset($_POST['loginRadiusAddress'])) {
                    $loginRadiusProfileData['Address'] = "";
                    $profileAddress = trim($_POST['loginRadiusAddress']);
                }
                // city
                if (isset($_POST['loginRadiusCity'])) {
                    $loginRadiusProfileData['City'] = "";
                    $profileCity = trim($_POST['loginRadiusCity']);
                }
                // country
                if (isset($_POST['loginRadiusCountry'])) {
                    $loginRadiusProfileData['Country'] = "";
                    $profileCountry = trim($_POST['loginRadiusCountry']);
                }
                // phone number
                if (isset($_POST['loginRadiusPhone'])) {
                    $loginRadiusProfileData['PhoneNumber'] = "";
                    $profilePhone = trim($_POST['loginRadiusPhone']);
                }
                // email
                if (isset($_POST['loginRadiusEmail'])) {
                    $email = trim($_POST['loginRadiusEmail']);
                    // check if email already exists
                    $userId = $this->dataObject->loginRadiusRead("customer_entity", "email exists pop1", array($email), true);
                    if (isset($userId) && $userId->fetch()) {
                       
                        if ($this->blockObj->getProfileFieldsRequired() == 1) {
                            $this->dataObject->setTmpSession($this->loginRadiusPopErr, true, $socialLoginProfileData, true, true);
                        } else {
                            
                            $this->dataObject->setTmpSession($this->loginRadiusPopErr, true, true, true, true);
                        }
                        $this->getPopupTemplate();
                        return;
                    }

                    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)) {
                        if ($this->blockObj->getProfileFieldsRequired() == 1) {
                            $hideZipCountry = false;
                        } else {
                            $hideZipCountry = true;
                        }
                        $this->dataObject->setTmpSession($this->loginRadiusPopErr, true, $loginRadiusProfileData, true, $hideZipCountry);
                        $this->getPopupTemplate();

                        return;
                    }
                    // check if email already exists
                    $userId = $this->dataObject->loginRadiusRead("customer_entity", "email exists pop1", array($email), true);
                    if ($rowArray = $userId->fetch()) { // email exists
                        //check if entry exists on same provider in sociallogin table
                        $verified = $this->dataObject->loginRadiusRead("lr_sociallogin", "email exists sl", array($rowArray['entity_id'], $loginRadiusPopProvider), true);
                        if ($rowArrayTwo = $verified->fetch()) {
                            // check verified field
                            if ($rowArrayTwo['verified'] == "1") {
                                // check sociallogin id
                                if ($rowArrayTwo['sociallogin_id'] == $sessionUserId) {
                                    $this->dataObject->socialLoginUserLogin(false, $rowArray['entity_id'], $rowArrayTwo['sociallogin_id'], $loginRadiusPopProvider);
                                    return;
                                } else {
                                    $this->dataObject->setTmpSession($this->loginRadiusPopErr, true, array(), true, true);
                                    $this->getPopupTemplate();
                                    return;
                                }
                            } else {
                                // check sociallogin id
                                if ($rowArrayTwo['sociallogin_id'] == $sessionUserId) {
                                    $session = Mage::getSingleton('core/session');
                                    $session->addError(__('Please verify your email to login.'));
                                    header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK));
                                    die;
                                } else {
                                    // send verification email
                                    $this->dataObject->verifyUser($sessionUserId, $rowArray['entity_id'], $loginRadiusAvatar, $loginRadiusPopProvider, $email);
                                    return;
                                }
                            }
                        } else {
                            // send verification email
                            $this->dataObject->verifyUser($sessionUserId, $rowArray['entity_id'], $loginRadiusAvatar, $loginRadiusPopProvider, $email);
                            return;
                        }
                    }
                }

                // validate other profile fields
                if ((isset($profileAddress) && $profileAddress == "") || (isset($profileCity) && $profileCity == "") || (isset($profileCountry) && $profileCountry == "") || (isset($profilePhone) && $profilePhone == "")) {
                    $this->dataObject->setTmpSession($this->loginRadiusPopErr, true, $loginRadiusProfileData, false);
                    $this->getPopupTemplate();
                    return false;
                }
                $profileData = Mage::getSingleton('core/session')->getSocialLoginData();
                // set provider class member variable
                $this->loginRadiusProvider = $profileData->Provider;
                // assign submitted profile fields to array
                // address
                if (isset($profileAddress) && !empty($profileAddress)) {
                    $profileData->Addresses[0] = (object) array('Address1' => $profileAddress);
                }
                // city
                if (isset($profileCity) && !empty($profileCity)) {
                    $profileData->City = $profileCity;
                }
                // Country
                if (isset($profileCountry) && !empty($profileCountry)) {
                    $profileData->Country = (object) array('Name' => $profileCountry);
                }
                // Phone Number
                if (isset($profilePhone) && !empty($profilePhone)) {
                    $profileData->PhoneNumbers = (object) array('PhoneNumber' => $profilePhone);
                }
                // Zipcode
                if (isset($_POST['loginRadiusZipcode'])) {
                    $profileData->Addresses[0] = (object) array('PostalCode' => trim($_POST['loginRadiusZipcode']));
                }
                // Province
                if (isset($_POST['loginRadiusProvince'])) {
                    $profileData->Addresses[0] = (object) array('Region' => trim($_POST['loginRadiusProvince']));
                }
                // Email
                if (isset($email)) {
                    $profileData->Email[0] = (object) array('Type' => 'Primary');
                    $profileData->Email[0] = (object) array('Value' => $email);
                    $verify = true;
                } else {
                    $verify = false;
                }
                Mage::getSingleton('core/session')->unsSocialLoginData(); // unset session
                $this->dataObject->createUpdateUserProfile($profileData, $verify);
                return;
            }
        } elseif ($requiredFieldPopupCancel == 'Cancel') {
            Mage::getSingleton('core/session')->unsSocialLoginData(); // unset session
            header("Location: " . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)); // redirect to index page
            exit();
        }
    }

    /**
     * handle loginradius token
     * 
     * @param type $token
     * @return type
     */
    function tokenHandler($token) {
        require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'SDKClient.php';
        global $apiClient_class;
        $apiClient_class = 'Loginradius_Sociallogin_Helper_SDKClient';
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        $this->loginradiusSdkObject = new LoginRadiusSDK\SocialLogin\SocialLoginAPI($activationBlockObj->apiKey(), $activationBlockObj->apiSecret(), array('output_format' => 'json'));
        try {
            $this->accessTokenObject = $this->loginradiusSdkObject->exchangeAccessToken($token);
        } catch (LoginRadiusException $e) {
            Mage::dispatchEvent('lr_logout_sso', array('exception' => $e));
            $this->handleDebugMode($e);
        }
        
        if (isset($this->accessTokenObject->access_token) && !empty($this->accessTokenObject->access_token)) {
            try {
                $this->userProfileData = $this->loginradiusSdkObject->getUserProfiledata($this->accessTokenObject->access_token);
            } catch (LoginRadiusException $e) {
                $this->handleDebugMode($e);
            }
            if (isset($this->userProfileData->ID) && !empty($this->userProfileData->ID)) {
                $this->userProfileData->accesstoken = $this->accessTokenObject->access_token;
                //mep user profile data in array format
                
                if ($this->blockObj->isLoggedIn()) {
                    //linking functionlaity will work
                    $this->dataObject->loginRadiusSocialLinking(Mage::getSingleton("customer/session")->getCustomer()->getId(), $this->userProfileData);
                } else {
                    $checkSocialId = true;
                    if (($activationBlockObj->raasEnable() == 1) && isset($this->userProfileData->Uid) && !empty($this->userProfileData->Uid)) {
                        $uidQuery = $this->dataObject->getCustomerData(array('customer_entity', 'lr_sociallogin'), array($this->userProfileData->Uid), 'check uid');
                        $customerEntity = $uidQuery->fetch();
                        if ($customerEntity) {
                            $checkSocialId = false;
                            if ($customerEntity['verified'] == "0") {//Account is not verified
                                $session = Mage::getSingleton('customer/session');
                                $session->addError(__('Please verify your email to login.'));
                                $this->_redirectUrl($this->dataObject->getSamePage());
                            } else {
                                if ($this->blockObj->socialLinking() == "1") {
                                    $this->dataObject->linkSocialProfile($customerEntity['entity_id'], $this->userProfileData);
                                }
                                if ($this->blockObj->updateProfileData() != '1') {
                                    //not-update user profile data and login
                                    $this->dataObject->loginUserProfile($customerEntity['entity_id'], $this->userProfileData);
                                } else {
                                    //update user profile data and login
                                    $this->dataObject->createUpdateUserProfile($this->userProfileData, false, true, $customerEntity['entity_id']);
                                }
                            }
                        }
                    } 
                    if($checkSocialId){
                        //login functionlaity will work
                        $innerJoinQuery = $this->dataObject->getCustomerData(array('customer_entity', 'lr_sociallogin'), array($this->userProfileData->ID), 'id');
                        $customerEntity = $innerJoinQuery->fetch();

                        if ($customerEntity) {//check social ID exist OR not
                            if ($customerEntity['verified'] == "0") {//Account is not verified
                                $session = Mage::getSingleton('customer/session');
                                $session->addError(__('Please verify your email to login.'));
                                $this->_redirectUrl($this->dataObject->getSamePage());
                            } else {
                                if ($this->blockObj->updateProfileData() != '1') {
                                    //not-update user profile data and login
                                    $this->dataObject->loginUserProfile($customerEntity['entity_id'], $this->userProfileData);
                                } else {
                                    //update user profile data and login
                                    $this->dataObject->createUpdateUserProfile($this->userProfileData, false, true, $customerEntity['entity_id']);
                                }
                            }
                        } else {
                            if (isset($this->userProfileData->Email[0]->Value) && !empty($this->userProfileData->Email[0]->Value)) {
                                $emailQuery = $this->dataObject->getCustomerData(array('customer_entity'), array($this->userProfileData->Email[0]->Value), 'email');
                                $customerEntity = $emailQuery->fetch();
                                if ($customerEntity) {
                                    if ($this->blockObj->socialLinking() == "1") {
                                        $this->dataObject->linkSocialProfile($customerEntity['entity_id'], $this->userProfileData);
                                    }
                                    if ($this->blockObj->updateProfileData() != '1') {
                                        $this->dataObject->loginUserProfile($customerEntity['entity_id'], $this->userProfileData);
                                    } else {
                                        //create new user without showing popup
                                        $this->dataObject->createUpdateUserProfile($this->userProfileData, false, true, $customerEntity['entity_id']);
                                    }
                                } else {
                                    if ($this->blockObj->profilefieldsRequired() == 1) {//show required field popup
                                        $this->dataObject->setInSession($this->userProfileData->ID, $this->userProfileData);
                                        // show a popup to fill required profile fields
                                        $this->dataObject->setTmpSession("", true, $this->userProfileData, false);
                                        $this->getPopupTemplate();
                                        return;
                                    } else {
                                        //create new user without showing popup
                                        $this->dataObject->createUpdateUserProfile($this->userProfileData);
                                    }
                                }
                            } else {
                                $emailRequired = true;
                                if ($this->blockObj->emailRequired() == false) {
                                    $email = $this->dataObject->getAutoGeneratedEmail($this->userProfileData);
                                    $this->userProfileData->Email = array(json_decode(json_encode(array('Value' => $email))));
                                    
                                    $emailRequired = false;
                                }
                                //show required fields popup
                                $this->dataObject->setInSession($this->userProfileData->ID, $this->userProfileData);
                                if ($this->blockObj->profilefieldsRequired() == 1) {
                                    // show a popup to fill required profile fields
                                    $this->dataObject->setTmpSession("", true, $this->userProfileData, $emailRequired);
                                    $this->getPopupTemplate();
                                } elseif ($this->blockObj->emailRequired() == 1) {
                                    $this->dataObject->setTmpSession("", true, array(), $emailRequired, true);
                                    $this->getPopupTemplate();
                                } else {
                                    //create new user without showing popup
                                    $this->dataObject->createUpdateUserProfile($this->userProfileData);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function handleDebugMode($e) {
        if ($this->blockObj->debugMode() == 1) {
            $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
            Mage::getSingleton('core/session')->addNotice($errorDescription);
        }
        $referralUrl = $this->_getRefererUrl();
        if (empty($referralUrl)) {
            $referralUrl = Mage::getBaseUrl();
        }
        $this->getResponse()->setRedirect($referralUrl);
        return;
    }

    /**
     * Get template to render email required popup
     */
    public function getPopupTemplate() {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock(
                'Mage_Core_Block_Template', 'emailpopup', array('template' => 'Loginradius/sociallogin/popup.phtml')
        );
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

}
