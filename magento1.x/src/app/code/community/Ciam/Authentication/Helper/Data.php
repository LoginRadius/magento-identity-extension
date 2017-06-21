<?php

class Ciam_Authentication_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getCustomerData($tables, $params, $action) {
        $Conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        foreach ($tables as $table) {
            $tableName[] = Mage::getSingleton('core/resource')->getTableName($table);
        }
        $websiteId = Mage::app()->getWebsite()->getId();
        $storeId = Mage::app()->getStore()->getId();
        $query = '';
        switch ($action) {
            case 'id':
                $query = "SELECT * FROM $tableName[0] INNER JOIN $tableName[1] WHERE $tableName[0].entity_id=$tableName[1].entity_id AND $tableName[1].id = '" . $params[0] . "' AND $tableName[0].website_id = $websiteId AND $tableName[0].store_id =" . $storeId;
                break;
            case 'email':
                $query = "SELECT * FROM $tableName[0] WHERE email = '" . $params[0] . "' AND website_id = $websiteId";
                break;
            case 'uid':
                $query = "SELECT * FROM $tableName[0] WHERE uid = '" . $params[0] . "'";
                break;
            case 'activation':
                $query = "SELECT * FROM $tableName[0] WHERE vkey = '" . $params[0] . "'";
                break;
            case 'check_uid':
                $query = "SELECT * FROM $tableName[0] INNER JOIN $tableName[1] WHERE $tableName[0].entity_id=$tableName[1].entity_id AND $tableName[1].uid = '" . $params[0] . "'";
        }
        if (!empty($query)) {
            return $Conn->query($query);
        }
        return false;
    }

    public function getSamePage() {
        $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer() : Mage::getUrl();
        if (strpos($url, 'sociallogin') !== false && !empty(Mage::app()->getRequest()->getParam('redirect_to'))) {
            $url = trim(Mage::app()->getRequest()->getParam('redirect_to'));
        } else {
            $url = Mage::getUrl();
        }
        return $url;
    }

    function getValueFromStringUrl($url, $parameter_name) {
        $parts = parse_url($url);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (isset($query[$parameter_name])) {
                return $query[$parameter_name];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function redirectAfterLogin($loginOrRegister) {

// check if logged in from callback page
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        if (!empty(Mage::app()->getRequest()->getParam('loginradiuscheckout'))) {
            $currentUrl = Mage::helper('checkout/url')->getCheckoutUrl();
        } elseif ($this->getValueFromStringUrl($currentUrl, 'redirect_to')) {
            $currentUrl = $this->getValueFromStringUrl($currentUrl, 'redirect_to');
        } elseif (!empty(Mage::getSingleton('core/session')->getRefererURLData())) {
            $currentUrl = Mage::getSingleton('core/session')->getRefererURLData();
        } else {
            $currentUrl = $url;
        }
        if (strpos($url, 'https://') !== false) {
            $currentUrl = str_replace('http://', 'https://', $currentUrl);
        } else {
            $currentUrl = str_replace('https://', 'http://', $currentUrl);
        }
        Mage::app()->getResponse()->setRedirect($currentUrl)->sendHeadersAndExit();
        return;
    }

    public function socialLoginFilterAvatar($id, $imgUrl, $provider) {

        $thumbnail = (!empty($imgUrl) ? trim($imgUrl) : '');

        if (empty($thumbnail) && ($provider == 'facebook')) {
            $thumbnail = "http://graph.facebook.com/" . $id . "/picture?type=large";
        }
        return $thumbnail;
    }

    public function loginDataInsert($lrTable, $lrInsertData) {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->beginTransaction();
        $sociallogin = Mage::getSingleton('core/resource')->getTableName($lrTable);
        try {
            $data = $connection->insert($sociallogin, $lrInsertData);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $connection->commit();
    }

    public function createUpdateUserProfile($userProfile, $customerId = '') {

        $websiteId = Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();
// add new user magento way
        $customerModel = Mage::getModel("customer/customer");

        $redirectionTo = 'Registration';
        $customer = $customerModel;

        $customer->website_id = $websiteId;
        $customer->setStore($store);
        if ($userProfile->FirstName != "") {
            $customer->firstname = $userProfile->FirstName != '' ? $userProfile->FirstName : '';
        }

        $customer->lastname = $userProfile->LastName == "" ? $userProfile->FirstName : $userProfile->LastName;


        $customer->email = $userProfile->Email[0]->Value;
        $loginRadiusPwd = $customer->generatePassword(10);
        $customer->password_hash = md5($loginRadiusPwd);

        if ($userProfile->BirthDate != "") {
            $customer->dob = $userProfile->BirthDate;
        }
        if ($userProfile->Gender != "") {
            $customer->gender = $userProfile->Gender;
        }
        $customer->setConfirmation(null);

        $customer->save();
        $address = Mage::getModel("customer/address");
//if (!$update) {
        $address->setCustomerId($customer->getId());

        $address->firstname = $customer->firstname;
        $address->lastname = $customer->lastname;
        $address->country_id = isset($userProfile->Country->Code) ? ucfirst($userProfile->Country->Code) : '';

        if (!isset($userProfile->Country->Code) || empty($userProfile->Country->Code)) {
            if (isset($userProfile->Country->Name) && !empty($userProfile->Country->Name)) {
                $countryList = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
                foreach ($countryList as $key => $val) {
                    if (strtolower($val['label']) === strtolower($userProfile->Country->Name)) {
                        $address->country_id = $val['value'];
                        break;
                    }
                }
            }
        }
        $address->city = isset($userProfile->City) ? ucfirst($userProfile->City) : '';

        $address->region = isset($userProfile->State) && !empty($userProfile->State) ? $userProfile->State : '';
        $address->telephone = '';
        if (isset($userProfile->PhoneNumbers) && is_array($userProfile->PhoneNumbers) && count($userProfile->PhoneNumbers) > 0) {
            $address->telephone = isset($userProfile->PhoneNumbers[0]->PhoneNumber) ? $userProfile->PhoneNumbers[0]->PhoneNumber : '';
        }
        $address->company = isset($userProfile->Industry) ? ucfirst($userProfile->Industry) : '';
        if (isset($userProfile->Addresses) && is_array($userProfile->Addresses) && count($userProfile->Addresses) > 0) {
            $address->street = isset($userProfile->Addresses[0]->Address1) ? ucfirst($userProfile->Addresses[0]->Address1) : '';
            $address->postcode = isset($userProfile->Addresses[0]->PostalCode) ? $userProfile->Addresses[0]->PostalCode : '';
// If country is USA, set up province
            $address->region = isset($userProfile->Addresses[0]->Region) ? $userProfile->Addresses[0]->Region : $address->region;
        }
// set default billing, shipping address and save in address book
        $address->setIsDefaultShipping('1')->setIsDefaultBilling('1')->setSaveInAddressBook('1');
        $address->save();

        $this->linkingData($customer->getId(), $userProfile);
        $this->loginUser($customer->getId(), $userProfile, $redirectionTo);
        return;
    }

    public function linkingData($entityId, $userProfile) {
// add info in sociallogin table

        $fields = array();
        $fields['id'] = $userProfile->ID;
        $fields['entity_id'] = $entityId;
        $fields['avatar'] = $this->socialLoginFilterAvatar($userProfile->ID, $userProfile->ThumbnailImageUrl, $userProfile->Provider);
        $fields['provider'] = $userProfile->Provider;
        $fields['uid'] = $userProfile->Uid;

        $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_write');
        $loginRadiusQuery = "SELECT * FROM `" . Mage::getSingleton('core/resource')->getTableName('lr_authentication') . "` WHERE id = '" . $fields['id'] . "'";
        $loginRadiusQueryHandle = $loginRadiusConn->query($loginRadiusQuery);
        $loginRadiusResult = $loginRadiusQueryHandle->fetchAll();
        if (count($loginRadiusResult) > 0) {
            $loginRadiusQuery = "DELETE FROM `" . Mage::getSingleton('core/resource')->getTableName('lr_authentication') . "` WHERE id = '" . $fields['id'] . "'";
            $loginRadiusConn->query($loginRadiusQuery);
        }
        $this->loginDataInsert("lr_authentication", $fields);
    }

    public function loginUser($entityId, $profileData, $loginOrRegister = 'Login') {
        $session = Mage::getSingleton("customer/session");
        $session->loginById($entityId);
        $session->setLoginRadiusId($profileData->ID);
        $emailVerified = isset($profileData->EmailVerified) ? $profileData->EmailVerified : true;
        $session->setLoginRadiusEmailVerified($emailVerified);
        if (isset($profileData->Uid) && ($profileData->Uid != null)) {
            $session->setLoginRadiusUid($profileData->Uid);
            $session->setLoginRadiusCheckoutUid(false);
        }
        $this->linkingData($entityId, $profileData);
        $this->redirectAfterLogin($loginOrRegister);
        return;
    }

}
