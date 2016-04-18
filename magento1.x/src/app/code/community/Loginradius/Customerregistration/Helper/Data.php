<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 *  sociallogin data helper
 *
 * @category    Loginradius
 * @package     Customerregistration
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Customerregistration_Helper_Data which is default helper for loginradius module and contains function related to session
 */
class Loginradius_Customerregistration_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * function responsible for customer registration
     *
     * @param array  $profileData
     * @param bool   $verify whether it is email verification process
     * @param bool   $update updating data or not
     * @param string $customerId
     */
    public function createUpdateUserProfile($userProfile, $verify = false, $update = false, $customerId = '') {
        $blockObject = new Loginradius_Customerregistration_Block_Customerregistration();
        $websiteId = Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();
        // add new user magento way
        $customerModel = Mage::getModel("customer/customer");
        if (!$update) {
            $redirectionTo = 'Registration';
            $customer = $customerModel;
        } else {
            $redirectionTo = 'Login';
            $customer = $customerModel->load($customerId);
        }
        $customer->website_id = $websiteId;
        $customer->setStore($store);
        if ($userProfile->FirstName != "") {
            $customer->firstname = $userProfile->FirstName;
        }
        if (!$update) {
            $customer->lastname = $userProfile->LastName == "" ? $userProfile->FirstName : $userProfile->LastName;
        } elseif ($update && $userProfile->LastName != "") {
            $customer->lastname = $userProfile->LastName;
        }
        if (!$update) {
            $customer->email = $userProfile->Email[0]->Value;
            $loginRadiusPwd = $customer->generatePassword(10);
            $customer->password_hash = md5($loginRadiusPwd);
        }
        if ($userProfile->BirthDate != "") {
            $customer->dob = $userProfile->BirthDate;
        }
        if ($userProfile->Gender != "") {
            $customer->gender = $userProfile->Gender;
        }
        $customer->setConfirmation(null);
        $customer->save();
        $address = Mage::getModel("customer/address");
        if (!$update) {
            $address->setCustomerId($customer->getId());
            $address->firstname = $customer->firstname;
            $address->lastname = $customer->lastname;
            $address->country_id = isset($userProfile->Country->Code) ? ucfirst($userProfile->Country->Code) : '';
            if (!isset($userProfile->Country->Code) || empty($userProfile->Country->Code)) {
                if (isset($userProfile->Country->Name) && !empty($userProfile->Country->Name)) {
                    $countryList = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
                    foreach ($countryList as $key => $val) {
                        if (strtolower($val['label']) === strtolower($userProfile->Country->Code)) {
                            $address->country_id = $val['value'];
                            break;
                        }
                    }
                }
            }
            if (isset($userProfile->Addresses[0]->PostalCode)) {
                $address->postcode = $userProfile->Addresses[0]->PostalCode;
            }
            $address->city = isset($userProfile->City) ? ucfirst($userProfile->City) : '';
            if (isset($userProfile->State) && !empty($userProfile->State)) {
                $address->region = $userProfile->State;
            }
            // If country is USA, set up province
            if (isset($userProfile->Addresses[0]->Region)) {
                $address->region = $userProfile->Addresses[0]->Region;
            }
            $address->telephone = isset($userProfile->PhoneNumber) ? ucfirst($userProfile->PhoneNumber) : '';
            $address->company = isset($userProfile->Industry) ? ucfirst($userProfile->Industry) : '';
            $address->street = isset($userProfile->Address) ? ucfirst($userProfile->Address) : '';
            // set default billing, shipping address and save in address book
            $address->setIsDefaultShipping('1')->setIsDefaultBilling('1')->setSaveInAddressBook('1');
            $address->save();
        }

        // add info in customerregistration table
        if (!$verify) {
            $fields = array();
            $fields['sociallogin_id'] = $userProfile->ID;
            $fields['entity_id'] = $customer->getId();
            $fields['avatar'] = $userProfile->Thumbnail;
            $fields['provider'] = $userProfile->Provider;
            $fields['uid'] = $userProfile->Uid;

            if (!$update) {
                $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_write');
                $loginRadiusQuery = "select * from " . Mage::getSingleton('core/resource')->getTableName('lr_sociallogin') . " where entity_id = " . $fields['entity_id'];

                $loginRadiusQueryHandle = $loginRadiusConn->query($loginRadiusQuery);
                $loginRadiusResult = $loginRadiusQueryHandle->fetchAll();

                if (count($loginRadiusResult) > 0) {
                    $loginRadiusQuery = "DELETE  from " . Mage::getSingleton('core/resource')->getTableName('lr_sociallogin') . " where entity_id = " . $fields['entity_id'];
                    $loginRadiusConn->query($loginRadiusQuery);
                }
                $this->SocialLoginInsert("lr_sociallogin", $fields);
            } else {
                $this->SocialLoginInsert("lr_sociallogin", array('avatar' => $fields['avatar']), true, array('entity_id = ?' => $customerId));
            }
            if (!$update) {
                $loginRadiusUsername = $userProfile->FirstName . " " . $userProfile->LastName;
                // email notification to user
                if ($blockObject->notifyUser() == "1") {
                    $loginRadiusMessage = $blockObject->notifyUserText();
                    if (empty($loginRadiusMessage)) {
                        $loginRadiusMessage = __("Welcome to ") . $store->getGroup()->getName() . ". " . __("You can login to the store using following e-mail address and password:-");
                    }
                    $loginRadiusMessage .= "<br/>" . __("Email : ") . $userProfile->Email[0]->Value . "<br/>" . __("Password : ") . $loginRadiusPwd;

                    $this->loginRadiusEmail("Welcome " . $loginRadiusUsername . "!", $loginRadiusMessage, $userProfile->Email[0]->Value, $loginRadiusUsername);
                }
                // new user notification to admin
                if ($blockObject->notifyAdmin() == "1") {
                    $loginRadiusAdminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
                    $loginRadiusAdminName = Mage::getStoreConfig('trans_email/ident_general/name');
                    $loginRadiusMessage = trim($blockObject->notifyAdminText());
                    $loginRadiusMessage .= "<br/>" . __("Name : ") . $loginRadiusUsername . "<br/>" . __("Email : ") . $userProfile->Email[0]->Value;
                    $this->loginRadiusEmail(__("New Customer Registration"), $loginRadiusMessage, $loginRadiusAdminEmail, $loginRadiusAdminName);
                }
            }
            //login and redirect user
            $this->loginUserProfile( $customer->getId(), $userProfile, $redirectionTo);
        }
        if ($verify) {
            $this->verifyUser($userProfile->ID, $customer->getId(), $userProfile->Thumbnail, $userProfile->Provider, $userProfile->Email[0]->Value);
            // new user notification to admin
            if ($blockObject->notifyAdmin() == "1") {
                $loginRadiusAdminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
                $loginRadiusAdminName = Mage::getStoreConfig('trans_email/ident_general/name');
                $loginRadiusMessage = trim($blockObject->notifyAdminText());
                if (empty($loginRadiusMessage)) {
                    $loginRadiusMessage = __("New customer has been registered to your store with following details:-");
                }
                $loginRadiusMessage .= "<br/>" . __("Name : ") . $loginRadiusUsername . "<br/>" . __("Email : ") . $userProfile->Email[0]->Value;
                $this->loginRadiusEmail(__("New Customer Registration"), $loginRadiusMessage, $loginRadiusAdminEmail, $loginRadiusAdminName);
            }
        }
        $this->loginUserProfile($customer->getId(), $userProfile, $redirectionTo);
        if ($verify) {
            $loginRadiusUsername = $userProfile->FirstName . " " . $userProfile->LastName;
            $this->verifyUser($userProfile->ID, $customer->getId(), $userProfile->thumbnail, $userProfile->Provider, $userProfile->Email[0]->Value, true, $loginRadiusUsername);
        }
    }
public function loginRadiusEmail($subject, $message, $to, $toName)
    {
        $storeName = Mage::app()->getStore()->getGroup()->getName();
        $mailObj = new Mage_Core_Model_Email_Template();
        $mail = $mailObj->getMail();
        $mail->setBodyHtml($message); //for sending message containing html code
        $mail->setFrom("Owner", $storeName);
        $mail->addTo($to, $toName);
        $mail->setSubject($subject);
        try {
            $mail->send();
        } catch (Exception $ex) {
            Mage::logException($ex);
        }
    }
    /**
     * function responsible for providing login to customer
     *
     * @param        $newCustomer if user is logging in first time
     * @param        $entityId    customer entity id
     * @param        $socialId    social id
     * @param        $provider    provider
     * @param bool   $write       is permission to post
     * @param string $token
     */
    public function loginUserProfile($entityId, $profileData, $loginOrRegister = 'Login') {
        $session = Mage::getSingleton("customer/session");
        $blockObject = Mage::getBlockSingleton('customerregistration/customerregistration');
        $session->loginById($entityId);
        $session->setLoginRadiusId($profileData->ID);
        if (isset($profileData->Uid) && !empty($profileData->Uid)) {
            $session->setLoginRadiusUid($profileData->Uid);
        }
        $userProfileData  =  array('entityid' => $entityId);
        if(($blockObject->updateProfileData() == 1) || ($loginOrRegister != 'Login')){
            $userProfileData['profiledata'] = $profileData;
            $userProfileData['update'] = true;
        }
        
        Mage::dispatchEvent('lr_get_profile_data_after_login', $userProfileData);
        $this->redirectAfterLogin($loginOrRegister);
    }

    /**
     * Redirect to checkout page if needed!
     */
    public function redirectAfterLogin($loginOrRegister) {
        // check if logged in from callback page
        if (isset($_GET['loginradiuscheckout'])) {
            $currentUrl = Mage::helper('checkout/url')->getCheckoutUrl();
        } else {
            $blockObj = Mage::getBlockSingleton('customerregistration/customerregistration');
            $functionForRedirectOption = 'redirectionAfter' . $loginOrRegister;
            $Hover = $blockObj->$functionForRedirectOption();
            $functionForCustomRedirectOption = 'redirectionAfter' . $loginOrRegister . 'Custom';
            $write_url = $blockObj->$functionForCustomRedirectOption();
            $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
            if ($Hover == 'account') {
                $currentUrl = $url . 'customer/account';
            } elseif ($Hover == 'index') {
                $currentUrl = $url;
            } elseif ($Hover == 'custom' && $write_url != '') {
                $currentUrl = $write_url;
            } elseif ($Hover == 'same') {
                $currentUrl = $this->getSamePage();
            } else {
                if (isset($_GET['redirect_to'])) {
                    $currentUrl = trim($_GET['redirect_to']);
                } else {
                    $currentUrl = $url;
                }
            }
        }
        if (strpos(Mage::helper('core/url')->getCurrentUrl(), 'https://') !== false) {
            $currentUrl = str_replace('http://', 'https://', $currentUrl);
        } else {
            $currentUrl = str_replace('https://', 'http://', $currentUrl);
        }

        header('Location: ' . $currentUrl);
        exit();
    }

    public function getSamePage(){
        return Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer() : Mage::getUrl();
    }

    public function linkSocialProfile($entityId, $userProfileData) {
        $socialLoginLinkData = array();
        $socialLoginLinkData['entity_id'] = $entityId;
        $socialLoginLinkData['sociallogin_id'] = $userProfileData->ID;
        $socialLoginLinkData['provider'] = $userProfileData->Provider;
        $socialLoginLinkData['avatar'] = $this->socialLoginFilterAvatar($userProfileData->ID, $userProfileData->ThumbnailImageUrl, $userProfileData->Provider);
        $socialLoginLinkData['uid'] = isset($userProfileData->Uid) ? $userProfileData->Uid : '';
        $socialLoginLinkData['status'] = 'unblocked';
        $this->SocialLoginInsert("lr_sociallogin", $socialLoginLinkData);
    }

    /**
     * Set social network profile data in session
     *
     * @param $id
     * @param $socialloginProfileData
     */
    public function setInSession($id, $socialloginProfileData)
    {
        Mage::getSingleton('core/session')->setSocialLoginData($socialloginProfileData);
    }
    public function setTmpSession($loginRadiusPopupTxt = '', $socialLoginMsg = "", $loginRadiusShowForm = true, $profileData = array(), $emailRequired = true, $hideZipcode = false)
    {
        Mage::getSingleton('core/session')->setTmpPopupTxt($loginRadiusPopupTxt);
        Mage::getSingleton('core/session')->setTmpPopupMsg($socialLoginMsg);
        Mage::getSingleton('core/session')->setTmpShowForm($loginRadiusShowForm);
        Mage::getSingleton('core/session')->setTmpProfileData($profileData);
        Mage::getSingleton('core/session')->setTmpEmailRequired($emailRequired);
        Mage::getSingleton('core/session')->setTmpHideZipcode($hideZipcode);
    }
    public function SocialLoginInsert($lrTable, $lrInsertData, $update = false, $condition = '', $isStatusUpdate = false) {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->beginTransaction();
        $sociallogin = $this->getMazeTable($lrTable);
        if (!$update) {
            if ($lrTable == 'lr_facebook_events') {
                $query = "INSERT INTO " . $sociallogin . " VALUES ('" . $lrInsertData['user_id'] . "', '" . $lrInsertData['event_id'] . "', '" . $lrInsertData['event'] . "', STR_TO_DATE('" . $lrInsertData['start_time'] . "', '%c/%e/%Y %r'), '" . $lrInsertData['rsvp_status'] . "', '" . $lrInsertData['location'] . "')";
                try {
                    $connection->query($query);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            } else {
                try {
                    $connection->insert($sociallogin, $lrInsertData);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        } elseif ($isStatusUpdate && $update) {
            $sql = "UPDATE " . $sociallogin . " SET status = '" . $lrInsertData['status'] . "' WHERE entity_id = " . $lrInsertData['entity_id'] . ";";
            try {
                // update query magento way
                $connection->query($sql);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        } else {
            try {
                // update query magento way
                $connection->update($sociallogin, $lrInsertData, $condition);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        $connection->commit();
        if ($lrTable == 'lr_companies') {
            $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_read');
            try {
                $result = $loginRadiusConn->raw_fetchRow("SELECT MAX(id) as LastID FROM `{$sociallogin}`");
            } catch (Exception $e) {
                Mage::logException($e);
            }

            return $result['LastID'];
        }
    }

    public function getMazeTable($tableName) {
        return Mage::getSingleton('core/resource')->getTableName($tableName);
    }

    public function getCustomerData($tables, $params, $action) {
        $socialLoginConn = Mage::getSingleton('core/resource')->getConnection('core_read');
        foreach ($tables as $table) {
            $tableName[] = $this->getMazeTable($table);
        }
        $websiteId = Mage::app()->getWebsite()->getId();
        $storeId = Mage::app()->getStore()->getId();
        $query = '';
        switch ($action) {
            case 'id':
                $query = "SELECT * FROM $tableName[0] INNER JOIN $tableName[1] WHERE $tableName[0].entity_id=$tableName[1].entity_id AND $tableName[1].sociallogin_id = '" . $params[0] . "' AND $tableName[0].website_id = $websiteId AND $tableName[0].store_id =" . $storeId;
                break;
            case 'email':
                $query = "SELECT * FROM $tableName[0] WHERE email = '" . $params[0] . "' AND website_id = $websiteId";
                break;
            case 'uid':
                $query = "SELECT * FROM $tableName[0] WHERE uid = '" . $params[0] . "'";
        }

        return $socialLoginConn->query($query);
    }
public function loginRadiusRead($table, $handle, $params, $result = false)
    {
        $socialLoginConn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tbl = $this->getMazeTable($table);
        $websiteId = Mage::app()->getWebsite()->getId();
        $storeId = Mage::app()->getStore()->getId();
        $query = "";
        switch ($handle) {
            case "email exists pop1":
                $query = "select entity_id from $tbl where email = '" . $params[0] . "' and website_id = $websiteId and store_id = $storeId";
                break;
            case "get user":
                $query = "select entity_id, verified from $tbl where sociallogin_id= '" . $params[0] . "'";
                break;
            case "get user2":
                $query = "select entity_id from $tbl where entity_id = " . $params[0] . " and website_id = $websiteId and store_id = $storeId";
                break;
            case "email exists login":
                $query = "select * from $tbl where email = '" . $params[0] . "' and website_id = $websiteId and store_id = $storeId";
                break;
            case "email exists sl":
                $query = "select verified,sociallogin_id from $tbl where entity_id = '" . $params[0] . "' and provider = '" . $params[1] . "'";
                break;
            case "provider exists in sociallogin":
                $query = "select entity_id from $tbl where entity_id = '" . $params[0] . "' and provider = '" . $params[1] . "'";
                break;
            case "verification":
                $query = "select entity_id, provider from $tbl where vkey = '" . $params[0] . "'";
                break;
            case "verification2":
                $query = "select entity_id from $tbl where entity_id = " . $params[0] . " and provider = '" . $params[1] . "' and vkey != '' ";
                break;
            case "get company ids":
                $query = "select company from $tbl where user_id = " . $params[0];
                break;
            case "get status":
                $query = "select uid, status from $tbl where entity_id = '" . $params[0] . "'";
                return $socialLoginConn->query($query)->fetch();
        }
        $select = $socialLoginConn->query($query);
        if ($result) {
            return $select;
        }
        if ($select->fetch()) {
            return true;
        }

        return false;
    }
    public function getAutoGeneratedEmail($user_obj, $count=1)
    {
        $emailName = str_replace(array("/", "."), "_", substr($user_obj->ID, -10));
        $email = $emailName . '@' . $user_obj->Provider . '.com';
        $userId = $this->loginRadiusRead("customer_entity", "email exists pop1", array($email), true);
        if ($userId->fetch()) {
            $user_obj->ID = $user_obj->ID.$count;
            $email = $this->getAutoGeneratedEmail($user_obj,$count+1);
        }

        return $email;
    }
    function basicProfileMapping($profileObject) {
        $profileArray = array();
        $profileArray['Uid'] = isset($profileObject->Uid) ? trim($profileObject->Uid) : '';
        $profileArray['ID'] = isset($profileObject->ID) ? trim($profileObject->ID) : '';
        $profileArray['FirstName'] = isset($profileObject->FirstName) ? trim($profileObject->FirstName) : '';
        $profileArray['LastName'] = isset($profileObject->LastName) ? trim($profileObject->LastName) : '';
        $profileArray['FullName'] = isset($profileObject->FullName) ? trim($profileObject->FullName) : '';
        $profileArray['Email'] = isset($profileObject->Email[0]->Value) ? $profileObject->Email[0]->Value : '';
        $profileArray['NickName'] = isset($profileObject->NickName) ? trim($profileObject->NickName) : '';
        $profileArray['ProfileName'] = isset($profileObject->ProfileName) ? trim($profileObject->ProfileName) : '';
        $profileArray['Provider'] = isset($profileObject->Provider) ? trim($profileObject->Provider) : '';
        $profileArray['State'] = empty($profileObject->State) ? "" : $profileObject->State;
        $profileArray['City'] = empty($profileObject->City) || $profileObject->City == "unknown" ? "" : $profileObject->City;
        $profileArray['Industry'] = empty($profileObject->Positions['0']->Company->Name) ? "" : $profileObject->Positions['0']->Company->Name;
        $profileArray['PhoneNumber'] = empty($profileObject->PhoneNumbers['0']->PhoneNumber) ? "" : $profileObject->PhoneNumbers['0']->PhoneNumber;
        $profileArray['Thumbnail'] = $this->socialLoginFilterAvatar($profileObject->ID, $profileObject->ThumbnailImageUrl, $profileArray['Provider']);
        $profileArray['Gender'] = (!empty($profileObject->Gender) ? $profileObject->Gender : '');
        $profileArray['BirthDate'] = (!empty($profileObject->BirthDate) ? $profileObject->BirthDate : '');
        $profileArray['Bio'] = (!empty($profileObject->About) ? $profileObject->About : '');
        $profileArray['ProfileUrl'] = (!empty($profileObject->ProfileUrl) ? $profileObject->ProfileUrl : '');

        //manage phone numbers
        if (empty($profileArray['PhoneNumber']) && !empty($profileObject->PhoneNumbers) && count($profileObject->PhoneNumbers) > 0) {
            foreach ($profileObject->PhoneNumbers as $type => $number) {
                if (!empty($number)) {
                    $profileArray['PhoneNumber'] = $number;
                    break;
                }
            }
        }
        //manage addresses 
        $profileArray['Address'] = "";
        if (isset($profileObject->Addresses)) {
            if (is_array($profileObject->Addresses) && count($profileObject->Addresses) > 0) {
                foreach ($profileObject->Addresses as $address) {
                    if (isset($address->Address1) && !empty($address->Address1)) {
                        $profileArray['Address'] = $address->Address1;
                        break;
                    }
                }
            } elseif (is_string($profileObject->Addresses)) {
                $profileArray['Address'] = $profileObject->Addresses != "" ? $profileObject->Addresses : "";
            }
        }
        //country code insert in db
        $profileArray['Country'] = "";
        if (isset($profileObject->Country->Code) && is_string($profileObject->Country->Code)) {
            $profileArray['Country'] = $profileObject->Country->Code;
        }
        //full fill first name in value else user never register on db
        if (empty($profileArray['FirstName'])) {
            if (!empty($profileArray['FullName'])) {
                $profileArray['FirstName'] = $profileArray['FullName'];
            } elseif (!empty($profileArray['NickName'])) {
                $profileArray['FirstName'] = $profileArray['NickName'];
            } elseif (!empty($profileArray['Email'])) {
                $explode = explode("@", $profileArray['Email']);
                $profileArray['FirstName'] = $explode[0];
            }
        }

        if ($profileArray['FirstName'] == '') {
            $letters = range('a', 'z');
            for ($i = 0; $i < 5; $i++) {
                $profileArray['FirstName'] .= $letters[rand(0, 26)];
            }
        }
        //change dob formate
        if ($profileArray['BirthDate'] != "") {
            $profileArray['BirthDate'] = date('m/d/y', strtotime($profileArray['BirthDate']));
        }
        // manage gender value in int
        if (strtolower(substr($profileArray['Gender'], 0, 1)) == 'm') {
            $profileArray['Gender'] = '1';
        } elseif (strtolower(substr($profileArray['Gender'], 0, 1)) == 'f') {
            $profileArray['Gender'] = '2';
        } else {
            $profileArray['Gender'] = '';
        }
        return $profileArray;
    }

    public function socialLoginFilterAvatar($id, $imgUrl, $provider) {
        $thumbnail = (!empty($imgUrl) ? trim($imgUrl) : '');
        if (empty($thumbnail) && ($provider == 'facebook')) {
            $thumbnail = "http://graph.facebook.com/" . $id . "/picture?type=large";
        }

        return $thumbnail;
    }

}
