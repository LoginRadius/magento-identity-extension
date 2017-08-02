<?php

class Loginradius_Customerregistration_Model_Observer extends Mage_Core_Model_Abstract {

    public function customer_order_after($observer) {
        $quote = $observer->getEvent()->getQuote();
        $postData = Mage::app()->getRequest()->getPost();

        if (isset($postData['lr_raas_resonse'])) {

            if ($quote->getData('checkout_method') != Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER) {
                return;
            }
            $loginRadiusProfileData = $postData['lr_raas_resonse'];
            $session = Mage::getSingleton("customer/session");
            $session->setCurrentLoginRadiusId($loginRadiusProfileData->ID);
            $session->setCurrentLoginRadiusUid($loginRadiusProfileData->Uid);
            $session->setLoginRadiusId($loginRadiusProfileData->ID);
            $session->setLoginRadiusUid($loginRadiusProfileData->Uid);

            $customer = $quote->getCustomer();
            $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
            if ($customer->getId()) {
                $this->lr_save_data_in_table($loginRadiusProfileData, $customer->getId());
            }
        }
    }

    public function lr_save_data_in_table($loginRadiusProfileData, $user_id) {
        // Data for basic profile table
        $data = array();
        $data['user_id'] = $user_id;
        $data['loginradius_id'] = $loginRadiusProfileData->ID;
        $data['provider'] = $loginRadiusProfileData->Provider;
        $data['prefix'] = $loginRadiusProfileData->Prefix;
        $data['first_name'] = $loginRadiusProfileData->FirstName;
        $data['middle_name'] = $loginRadiusProfileData->MiddleName;
        $data['last_name'] = $loginRadiusProfileData->LastName;
        $data['suffix'] = $loginRadiusProfileData->Suffix;
        $data['full_name'] = $loginRadiusProfileData->FullName;
        $data['nick_name'] = $loginRadiusProfileData->NickName;
        $data['profile_name'] = $loginRadiusProfileData->ProfileName;
        $data['birth_date'] = ($loginRadiusProfileData->BirthDate) && !empty($loginRadiusProfileData->BirthDate) ? $loginRadiusProfileData->BirthDate : '0000-00-00';
        $data['gender'] = isset($loginRadiusProfileData->Gender) && !empty($loginRadiusProfileData->Gender) ? $loginRadiusProfileData->Gender : 'unknown';
        $data['country_code'] = $loginRadiusProfileData->Country->Code;
        $data['country_name'] = isset($loginRadiusProfileData->country_name) ? $loginRadiusProfileData->country_name : 'unknown';
        $data['thumbnail_image_url'] = $loginRadiusProfileData->ThumbnailImageUrl;
        $data['image_url'] = $loginRadiusProfileData->ImageUrl;
        $data['local_country'] = $loginRadiusProfileData->LocalCountry;
        $data['profile_country'] = $loginRadiusProfileData->ProfileCountry;
        //Data for  socialogin table
        $fields = array();
        $fields['sociallogin_id'] = $loginRadiusProfileData->ID;
        $fields['entity_id'] = $user_id;
        $fields['avatar'] = Mage::helper('sociallogin/Data')->socialLoginFilterAvatar($fields['sociallogin_id'], $loginRadiusProfileData->ThumbnailImageUrl, $loginRadiusProfileData->Provider);
        $fields['provider'] = $loginRadiusProfileData->Provider;
        $fields['uid'] = $loginRadiusProfileData->Uid;

        $connection = Mage::getSingleton('core/resource');
        $writeConnection = $connection->getConnection('core_write');
        $tableName = $connection->getTableName('lr_basic_profile_data');
        $writeConnection->insert($tableName, $data);
        $tableNameSocialLogin = $connection->getTableName('lr_sociallogin');
        $writeConnection->insert($tableNameSocialLogin, $fields);
        return;
    }

    /*
     * function for deleteing user from raas
     */

    public function customer_save_before($observer) {
        require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'SDKClient.php';
        global $apiClient_class;
        $apiClient_class = 'Loginradius_Sociallogin_Helper_SDKClient';
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        $userApi = new LoginRadiusSDK\CustomerRegistration\UserAPI($activationBlockObj->apiKey(), $activationBlockObj->apiSecret(), array('output_format' => 'json'));

        if (Mage::app()->getFrontController()->getRequest()->getRouteName() == "checkout" && !Mage::getSingleton('customer/session')->isLoggedIn()) {
            $session = Mage::getSingleton('core/session');
            $customer = $observer->getCustomer();
            $address = $observer->getCustomerAddress();
            $modified = $this->loginradius_get_customer_saved_data($customer, $address);
            if (isset($modified['EmailId']) && !empty($modified['EmailId'])) {
                try {
                    $response = $userApi->create($modified);
                    $_POST['lr_raas_resonse'] = $response;
                    $session->getMessages(true);
                } catch (LoginRadiusSDK\LoginRadiusException $e) {
                    $session->addError(__($e->getErrorResponse()->description));
                    Mage::throwException(__($e->getErrorResponse()->description));
                }
            }
        }
        return;
    }

    public function loginradius_get_customer_saved_data($customer, $address) {
        //customer basic info
        $modified['lastname'] = $customer->firstname; //firstname
        $modified['LastName'] = $customer->lastname; //lastname
        $modified['EmailId'] = $customer->email; //email
        $modified['password'] = $customer->password; //password
        //customer address info
        $modified['Company'] = $address->company; //company
        $modified['Street'] = $address->street; //street
        $modified['city'] = $address->city; //city
        $modified['country_id'] = $address->country_id; //country_id
        $modified['PostCode'] = $address->postcode; //postcode
        $modified['phonenumber'] = $address->telephone; //telephone
        return $modified;
    }

    public function checking_post_and_isset($value, $address) {
        //customer address info
        if (isset($postData[$value])) {
            return $postData[$value];
        } elseif (isset($address->$value) && !empty($address->$value)) {
            return $address->$value;
        }

        return '';
    }

    public function delete_before_customer($observer) {
        require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'SDKClient.php';
        global $apiClient_class;
        $apiClient_class = 'Loginradius_Sociallogin_Helper_SDKClient';
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        $accountAPI = new LoginRadiusSDK\CustomerRegistration\AccountAPI($activationBlockObj->apiKey(), $activationBlockObj->apiSecret(), array('output_format' => 'json'));

        $postData = Mage::app()->getRequest()->getPost();

        $isError = '';
        if (isset($postData['customer']) && is_array($postData['customer'])) {
            $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_read');

            foreach ($postData['customer'] as $customerId) {
                $loginRadiusQuery = "select uid from " . Mage::getSingleton('core/resource')->getTableName('lr_sociallogin') . " where entity_id = '" . $customerId . "' LIMIT 1";
                $loginRadiusQueryHandle = $loginRadiusConn->query($loginRadiusQuery);
                $loginRadiusResult = $loginRadiusQueryHandle->fetch();
                if (isset($loginRadiusResult['uid']) && !empty($loginRadiusResult['uid'])) {
                    $response = $accountAPI->deleteAccount($loginRadiusResult['uid']);
                    if (isset($response->description)) {
                        $isError = $response->description;
                    } else {
                        $isError = '';
                    }
                }
                unset($loginRadiusResult['uid']);
            }
        }
    }

    public function admin_customer_save_after($observer) {
        $postData = Mage::app()->getRequest()->getPost();
        if (!isset($postData['customer_id']) && ($postData['account']['website_id'] != 0)) {
            $postData = Mage::app()->getRequest()->getPost();
            $customer_email = $postData['account']['email'];
            $customer = Mage::getModel("customer/customer");
            $customer->setWebsiteId($postData['account']['website_id']);
            $customer->loadByEmail($customer_email);
            $this->lr_save_data_in_table($_POST['lr_raas_resonse'], $customer->getId());

            return;
        }
    }

    public function admin_customer_save_before($observer) {
        require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'SDKClient.php';
        global $apiClient_class;
        $apiClient_class = 'Loginradius_Sociallogin_Helper_SDKClient';
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        $userAPI = new LoginRadiusSDK\CustomerRegistration\UserAPI($activationBlockObj->apiKey(), $activationBlockObj->apiSecret(), array('output_format' => 'json'));

        $postData = Mage::app()->getRequest()->getPost();
        $formattedDate = '';

        if (isset($postData['account']['dob']) && !empty($postData['account']['dob'])) {
            $dateString = strtotime($postData['account']['dob']);
            $formattedDate = date('m-d-Y', $dateString);
        }
        /**
         * Creating user on RAAS
         */
        if (!isset($postData['customer_id'])) {

            if ($postData['account']['website_id'] != 0) {
                $params = array('EmailId' => $postData['account']['email'], 'firstname' => $postData['account']['firstname'], 'lastname' => $postData['account']['lastname'], 'birthdate' => $formattedDate, 'password' => $postData['account']['password']);
                if (isset($postData['account']['gender'])) {
                    if ($postData['account']['gender'] == '1') {
                        $params['gender'] = 'M';
                    } elseif ($postData['account']['gender'] == '0') {
                        $params['gender'] = 'F';
                    }
                }

                if ($postData['account']['password'] == 'auto') {

                    $charsPass = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=?";
                    $autoGenratedPassParams = array('EmailId' => $postData['account']['email'], 'firstname' => $postData['account']['firstname'], 'lastname' => $postData['account']['lastname'], 'birthdate' => $formattedDate, 'password' => substr(str_shuffle($charsPass), 0, 8));

                    try {

                        $response = $userAPI->create($params);

                        $_POST['lr_raas_resonse'] = $response;
                        $forgotPassDomain = Mage::getBaseUrl() . 'customerregistration/index/verification/';
                        try {

                            $rsetPasswordUrl = 'https://api.loginradius.com/raas/client/password/forgot?apikey=' . $activationBlockObj->apiKey() . '&emailid=' . $postData['account']['email'] . '&resetpasswordurl=' . $forgotPassDomain;
                        } catch (LoginRadiusSDK\LoginRadiusException $e) {
                            Mage::throwException($e->getErrorResponse()->description);
                        }

                        $result = LoginRadiusSDK\LoginRadius::apiClient($rsetPasswordUrl, FALSE, array('output_format' => 'json'));
                    } catch (LoginRadiusSDK\LoginRadiusException $e) {

                        Mage::throwException($e->getErrorResponse()->description);
                    }
                } else {
                    try {

                        $response = $userAPI->create($params);

                        $_POST['lr_raas_resonse'] = $response;
                    } catch (LoginRadiusSDK\LoginRadiusException $e) {

                        Mage::throwException($e->getErrorResponse()->description);
                    }
                }
            }
        } else {

            // Updating user profile
            $params = array('EmailId' => $postData['account']['email'], 'firstname' => $postData['account']['firstname'], 'lastname' => $postData['account']['lastname'], 'birthdate' => $formattedDate);
            if (isset($postData['account']['gender'])) {
                if ($postData['account']['gender'] == '1') {
                    $params['gender'] = 'M';
                } elseif ($postData['account']['gender'] == '0') {
                    $params['gender'] = 'F';
                }
            }

            $connection = Mage::getSingleton('core/resource');
            $readConnection = $connection->getConnection('core_read');
            $tableName = $connection->getTableName('lr_sociallogin');
            $query = "select sociallogin_id, uid from $tableName where entity_id= '" . $postData['customer_id'] . "'";
            $result = $readConnection->query($query)->fetch();

            if (isset($result['sociallogin_id']) && !empty($result['sociallogin_id'])) {

                //Code for password changing
                if (isset($postData['account']['new_password']) && !empty($postData['account']['new_password'])) {

                    $accountAPI = new LoginRadiusSDK\CustomerRegistration\AccountAPI($activationBlockObj->apiKey(), $activationBlockObj->apiSecret(), array('output_format' => 'json'));
                    $getRaasProfile = $accountAPI->getAccounts($result['uid']);

                    $checkProviderStatus = '';
                    foreach ($getRaasProfile as $key => $value) {
                        if ($value->Provider == 'RAAS') {

                            $checkProviderStatus = 'true';
                        }
                    }

                    if ($checkProviderStatus == 'true') {

                        if ($postData['account']['new_password'] == 'auto') {



                            $forgotPassDomain = Mage::getBaseUrl() . 'customerregistration/index/verification/';
                            try {

                                $rsetPasswordUrl = 'https://api.loginradius.com/raas/client/password/forgot?apikey=' . $activationBlockObj->apiKey() . '&emailid=' . $postData['account']['email'] . '&resetpasswordurl=' . $forgotPassDomain;
                            } catch (LoginRadiusSDK\LoginRadiusException $e) {
                                Mage::throwException($e->getErrorResponse()->description);
                            }

                            $result = LoginRadiusSDK\LoginRadius::apiClient($rsetPasswordUrl, FALSE, array('output_format' => 'json'));
                        } else {

                            try {

                                $accountAPI->setPassword(trim($result['uid']), trim($postData['account']['new_password']));
                            } catch (LoginRadiusSDK\LoginRadiusException $e) {
                                Mage::throwException($e->getErrorResponse()->description);
                            }
                        }
                    } else {

                        if ($postData['account']['new_password'] == 'auto') {
                            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=?";
                            try {
                                $data = array('accountid' => trim($result['uid']), 'emailid' => trim($postData['account']['email']), 'password' => substr(str_shuffle($chars), 0, 8));
                                $res = $accountAPI->createUserRegistrationProfile($data);

                                try {

                                    $forgotPassDomain = Mage::getBaseUrl() . 'customerregistration/index/verification/';
                                    try {

                                        $rsetPasswordUrl = 'https://api.loginradius.com/raas/client/password/forgot?apikey=' . $activationBlockObj->apiKey() . '&emailid=' . $postData['account']['email'] . '&resetpasswordurl=' . $forgotPassDomain;
                                    } catch (LoginRadiusSDK\LoginRadiusException $e) {
                                        Mage::throwException($e->getErrorResponse()->description);
                                    }

                                    $result = LoginRadiusSDK\LoginRadius::apiClient($rsetPasswordUrl, FALSE, array('output_format' => 'json'));
                                } catch (LoginRadiusSDK\LoginRadiusException $e) {

                                    Mage::throwException($e->getErrorResponse()->description);
                                }
                            } catch (LoginRadiusSDK\LoginRadiusException $e) {

                                Mage::throwException($e->getErrorResponse()->description);
                            }
                        } else {

                            try {

                                $data = array('accountid' => trim($result['uid']), 'emailid' => trim($postData['account']['email']), 'password' => trim($postData['account']['new_password']));
                                $res = $accountAPI->createUserRegistrationProfile($data);
                            } catch (LoginRadiusSDK\LoginRadiusException $e) {
                                Mage::throwException($e->getErrorResponse()->description);
                            }
                        }
                    }
                }

                $connectionDb = Mage::getSingleton('core/resource');
                $readConnectionDb = $connection->getConnection('core_read');
                $tableNameCustomer = $connection->getTableName('customer_entity');
                $queryCustomer = "select email from $tableNameCustomer where entity_id= '" . $postData['customer_id'] . "'";
                $resultCustomer = $readConnectionDb->query($queryCustomer)->fetch();

                if ($postData['account']['email'] != $resultCustomer["email"]) {

                    $accountAPI = new LoginRadiusSDK\CustomerRegistration\AccountAPI($activationBlockObj->apiKey(), $activationBlockObj->apiSecret(), array('output_format' => 'json'));
                    /* Adding New Email by Add/Remove Email Api */
                    $addEmailData = array('EmailId' => $postData['account']['email'], 'EmailType' => 'Primary');
                    try {
                        $addEmail = $accountAPI->userAdditionalEmail($result['uid'], 'add', $addEmailData);
                    } catch (LoginRadiusSDK\LoginRadiusException $e) {

                        if (isset($e->getErrorResponse()->description)) {

                            /* Getting Raas Profile By User Profile by UID Api */
                            $getRaasProfile = $accountAPI->getAccounts($result['uid']);

                            $providerStatus = '';
                            foreach ($getRaasProfile as $key => $value) {
                                if ($value->Provider == 'RAAS') {

                                    $providerStatus = 'true';
                                }
                            }

                            if ($providerStatus == 'true') {

                                $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';

                                Mage::throwException($errorDescription);
                            } else {

                                Mage::throwException('Please Set Password then change the email id.');
                            }
                        }
                        return;
                    }

                    if (isset($addEmail->isPosted) && ($addEmail->isPosted == 'true')) {
                        /* Removing Old Email by Add/Remove Email Api */
                        $removeEmailData = array('EmailId' => $resultCustomer["email"], 'EmailType' => 'Primary');
                        try {
                            $removeEmail = $accountAPI->userAdditionalEmail($result['uid'], 'remove', $removeEmailData);
                        } catch (LoginRadiusSDK\LoginRadiusException $e) {

                            Mage::throwException($e->getErrorResponse()->description);
                            return;
                        }
                    }
                }
            }
        }
    }

}
