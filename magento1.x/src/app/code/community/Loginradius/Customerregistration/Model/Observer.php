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
                $this->loginradius_save_data_in_table($loginRadiusProfileData, $customer->getId());
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
        $blockObj = Mage::helper('customerregistration/RaasSDK');
        if (Mage::app()->getFrontController()->getRequest()->getRouteName() == "checkout" && !Mage::getSingleton('customer/session')->isLoggedIn()) {

            $customer = $observer->getCustomer();
            $address = $observer->getCustomerAddress();
            $modified = $this->loginradius_get_customer_saved_data($customer, $address);
            if (isset($modified['EmailId']) && !empty($modified['EmailId'])) {
                if (false) {
                    $modified['emailverificationurl'] = Mage::helper('customer')->getLoginUrl();
                    $response = $blockObj->raas_create_user_with_email_verification(http_build_query($modified));
                } else {
                    $response = $blockObj->raas_create_user(http_build_query($modified));
                }
                if (isset($response->description)) {
                    Mage::throwException($response->description);
                } else {
                    $_POST['lr_raas_resonse'] = $response;
                }

                return;
            }
        }
        $socialId = Mage::getSingleton("customer/session")->getloginRadiusId();
        $postData = Mage::app()->getRequest()->getPost();

        if (isset($postData['email'])) {
            $customer = Mage::getSingleton("customer/session")->getCustomer();
            if (isset($postData['email']) && ($postData['email'] == $customer->email)) {


                //customer basic info object
                $modified = array();

                //customer basic info
                $modified['firstname'] = isset($postData['firstname']) ? $postData['firstname'] : $customer->firstname; //firstname
                $modified['lastname'] = isset($postData['lastname']) ? $postData['lastname'] : $customer->lastname; //lastname
                $modified['birthdate'] = isset($postData['dob']) ? date('m-d-Y', strtotime($postData['dob'])) : date('m-d-Y', strtotime($customer->dob)); //dob
                $modified['taxvat'] = isset($postData['taxvat']) ? $postData['taxvat'] : $customer->taxvat; //taxvat
                $modified['gender'] = isset($postData['gender']) ? $postData['gender'] : $customer->gender; //gender
                if ($modified['gender'] == '1') {
                    $modified['gender'] = 'M';
                } elseif ($modified['gender'] == '0') {
                    $modified['gender'] = 'F';
                } else {
                    $modified['gender'] = 'M';
                }
                $address = $observer->getCustomerAddress();
                if (!isset($address) || empty($address)) {
                    $address = new stdClass;
                }
                $modified['Company'] = $this->checking_post_and_isset('company', $address);
                $modified['street'] = $this->checking_post_and_isset('street', $address);
                $modified['city'] = $this->checking_post_and_isset('city', $address);
                $modified['PostCode'] = $this->checking_post_and_isset('postcode', $address);
                $modified['phonenumber'] = $this->checking_post_and_isset('telephone', $address);

                //change and set password
                if (isset($postData['social_password']) && $postData['social_password'] == 1) {
                    $userId = Mage::getSingleton("customer/session")->getId();
                    $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_read');
                    $loginRadiusQuery = "select uid from " . Mage::getSingleton('core/resource')->getTableName('lr_sociallogin') . " where entity_id = '" . $userId . "' LIMIT 1";
                    $loginRadiusQueryHandle = $loginRadiusConn->query($loginRadiusQuery);
                    $loginRadiusResult = $loginRadiusQueryHandle->fetch();
                    if (isset($loginRadiusResult["uid"]) && !empty($loginRadiusResult["uid"])) {
                        //set password
                        $raasSettings = Mage::getBlockSingleton('customerregistration/customerregistration');
                       
                        if (isset($postData['emailid']) && isset($postData['confirmpassword']) && isset($postData['password'])
                        ) {
                            if (empty($postData['emailid'])) {
                                Mage::getSingleton('core/session')->addError('Please select Email Address');
                                /* not the best redirect but don`t know how to */
                                $this->_redirectUrl('customer/account/edit');
                            }
                            if(($raasSettings->minPasswordLength() != 0) && ($raasSettings->minPasswordLength() > strlen($postData['password']))){
                                Mage::getSingleton('core/session')->addError('The Password field must be at least '.$raasSettings->minPasswordLength().' characters in length.');
                            }elseif(($raasSettings->maxPasswordLength() != 0) && (strlen($postData['password']) > $raasSettings->maxPasswordLength())){
                                Mage::getSingleton('core/session')->addError('The Password field must not exceed '.$raasSettings->maxPasswordLength().' characters in length.');
                            }elseif ($postData['password'] === $postData['confirmpassword']) { //check both password
                                $data = array('accountid' => trim($loginRadiusResult["uid"]), 'emailid' => trim($postData['emailid']), 'password' => trim($postData['password']));
                                $response = $blockObj->raas_set_password(http_build_query($data));
                                if (isset($response->description)) { // check any error from loginradius
                                    Mage::getSingleton('core/session')->addError($response->description);
                                } else {
                                    Mage::getSingleton('core/session')->addSuccess('Password updated successfully.');
                                }
                            } else { //password not match
                                Mage::getSingleton('core/session')->addError('Password don\'t match');
                            }
                            $this->_redirectUrl('customer/account/edit');
                        } elseif (isset($postData['newpassword']) && isset($postData['confirmnewpassword'])) {
                            if(($raasSettings->minPasswordLength() != 0) && ($raasSettings->minPasswordLength() > strlen($postData['newpassword']))){
                                Mage::getSingleton('core/session')->addError('The Password field must be at least '.$raasSettings->minPasswordLength().' characters in length.');
                            }elseif(($raasSettings->maxPasswordLength() != 0) && (strlen($postData['newpassword']) > $raasSettings->maxPasswordLength())){
                                Mage::getSingleton('core/session')->addError('The Password field must not exceed '.$raasSettings->maxPasswordLength().' characters in length.');
                            }elseif ($postData['newpassword'] !== $postData['confirmnewpassword']) {
                                //password not match
                                Mage::getSingleton('core/session')->addError('Password and Confirm Password don\'t match');
                            }else{
                                $password['oldpassword'] = $postData['oldpassword'];
                                $password['newpassword'] = $postData['newpassword'];
                                $response = $blockObj->raas_update_password(http_build_query($password), $loginRadiusResult["uid"]);
                                if (isset($response->description)) {
                                    Mage::getSingleton('core/session')->addError($response->description);
                                } else {
                                    Mage::getSingleton('core/session')->addSuccess('Password updated successfully.');
                                }
                            }
                        $this->_redirectUrl('customer/account/edit');
                        }                        
                    } else {
                        Mage::getSingleton('core/session')->addError('An error occurred');
                        $this->_redirectUrl('customer/account/edit');
                    }
                }
                //update  user at raas
                $response = $blockObj->raas_update_user($modified, $socialId);
                if (isset($response->description)) {
                    Mage::getSingleton('core/session')->addError($response->description);
                    $this->_redirectUrl('customer/account/edit');
                }
            }
        }
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
        $blockObj = Mage::helper('customerregistration/RaasSDK');
        $postData = Mage::app()->getRequest()->getPost();
        $isError = '';
        if (is_array($postData['customer'])) {
            $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_read');

            foreach ($postData['customer'] as $customerId) {
                $loginRadiusQuery = "select uid from " . Mage::getSingleton('core/resource')->getTableName('lr_sociallogin') . " where entity_id = '" . $customerId . "' LIMIT 1";
                $loginRadiusQueryHandle = $loginRadiusConn->query($loginRadiusQuery);
                $loginRadiusResult = $loginRadiusQueryHandle->fetch();
                if (isset($loginRadiusResult['uid']) && !empty($loginRadiusResult['uid'])) {
                    $response = $blockObj->raas_admin_delete_user($loginRadiusResult['uid']);
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
            $this->loginradius_save_data_in_table($_POST['lr_raas_resonse'], $customer->getId());

            return;
        }
    }

    public function admin_customer_save_before($observer) {
        $blockObj = Mage::helper('customerregistration/RaasSDK');
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
                $response = $blockObj->raas_create_user(http_build_query($params));
                if (isset($response->description)) {
                    Mage::throwException($response->description);
                } else {
                    $_POST['lr_raas_resonse'] = $response;
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
                    $passwordField = array('password' => $postData['account']['new_password']);
                    $res = $blockObj->raas_admin_set_password(http_build_query($passwordField), $result['sociallogin_id']);
                    if (isset($res->description)) {
                        Mage::throwException($res->description);
                    }
                }
                $result = $blockObj->raas_update_user($params, $result['sociallogin_id']);
                if (isset($result->description)) {
                    Mage::throwException($result->description);
                }
            }
        }
    }

}
