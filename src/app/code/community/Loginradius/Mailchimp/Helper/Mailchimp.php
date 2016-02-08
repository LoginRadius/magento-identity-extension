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
 *  sociallogin loginhelper helper
 *
 * @category    Loginradius
 * @package     Loginradius_Sociallogin
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sociallogin_Helper_LoginHelper which contains functions related tosocail login process functionality
 */
class Loginradius_Mailchimp_Helper_Mailchimp extends Mage_Core_Helper_Abstract {

    public static function enableMailchimp() {
        return trim(Mage::getStoreConfig('mailchimp/mailchimp/enable'));
    }

    public static function updateMailchimp() {
        return trim(Mage::getStoreConfig('mailchimp/mailchimp/update'));
    }

    public static function apikeyMailchimp() {
        return trim(Mage::getStoreConfig('mailchimp/mailchimp/apikey'));
    }

    public static function listsMailchimp() {
        return trim(Mage::getStoreConfig('mailchimp/mailchimp/lists'));
    }

    public static function getSelectedMappingFields() {
        return Mage::getStoreConfig('mailchimp/mailchimp/mappingFieldsValue');
    }

    public static function getMappingFieldNameTags() {
        return Mage::getStoreConfig('mailchimp/mailchimp/mappingFieldsTag');
    }

    public function createProfileDataatMailchimp($entityId) {
        if (($this->enableMailchimp() == '1') && ($this->apikeyMailchimp() != '') && ($this->listsMailchimp() != '')) {
            $mailchimpsdk = Mage::helper('mailchimp/MCAPI');
            $mailchimpsdk->MCAPI($this->apikeyMailchimp());
            if ($mailchimpsdk->errorCode) {
                return false;
            }
            $tempMergeTags = explode(',', $this->getMappingFieldNameTags());
            $tempMergeFields = explode(',', $this->getSelectedMappingFields());

            $merge_vars = array();
            $customerData = Mage::getModel('customer/customer')->load($entityId)->getData();
            $customerAddressId = Mage::getModel('customer/customer')->load($entityId)->getDefaultBilling();
            $address = Mage::getModel('customer/address')->load($customerAddressId);

            foreach ($tempMergeTags as $key => $tempMergeTag) {
                // if value exists for this merge var
                if (isset($tempMergeFields[$key])) {
                    $tempParts = explode('-', $tempMergeFields[$key]);
                    $value = '';

                    // if field is from any separate profile data table
                    if (count($tempParts) > 1) {
                        // execute query according to the prefix
                        switch ($tempParts[0]) {
                            // basic_profile_data table
                            case 'basic':
                                $value = $this->getprofiledata($entityId, $tempParts[1], 'basic_profile_data');
                                break;
                            // extended_location_data table
                            case 'exloc':
                                $value = $this->getprofiledata($entityId, $tempParts[1], 'extended_location_data');
                                break;
                            // extended_profile_data table
                            case 'exprofile':
                                $value = $this->getprofiledata($entityId, $tempParts[1], 'extended_profile_data');
                                break;
                        }
                    } else {

                        // Get data according to the value.
                        switch ($tempParts[0]) {
                            case 'Entity ID':
                                $value = $entityId;
                                break;
                            case 'First Name':
                                $value = $customerData['firstname'];
                                break;
                            case 'Last Name':
                                $value = $customerData['lastname'];
                                break;
                            case 'Full Name':
                                $value = $customerData['firstname'] . ' ' . $customerData['lastname'];
                                break;
                            case 'Email':
                                $value = $customerData['email'];
                                break;
                            case 'Telephone':
                                $value = $address->getTelephone();
                                break;
                            case 'Region':
                                $value = $address->getRegion();
                                break;
                            case 'Postcode':
                                $value = $address->getPostcode();
                                break;
                        }
                    }
                } else /* value for this merge var does not exist in database */ {
                    $value = '';
                }
                $merge_vars[$tempMergeTag] = $value;
            }
            $mailchimpsdk->listSubscribe($this->listsMailchimp(), $customerData['email'], $merge_vars);
            if ($mailchimpsdk->errorCode && $this->updateMailchimp() == 1) {
                $mailchimpsdk->listUpdateMember($this->listsMailchimp(), $customerData['email'], $merge_vars);
            }
        }
    }

    public function getprofiledata($customerid, $getColamn, $tableName) {
        $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_read');
        if ($tableName == 'companies') {
            $loginRadiusQuery = "select " . $getColamn . " from " . Mage::getSingleton('core/resource')->getTableName('lr_' . $tableName) . " where id = " . $customerid;
        } else {
            $loginRadiusQuery = "select " . $getColamn . " from " . Mage::getSingleton('core/resource')->getTableName('lr_' . $tableName) . " where user_id = " . $customerid;
        }
        $loginRadiusQueryHandle = $loginRadiusConn->query($loginRadiusQuery);
        $loginRadiusResult = $loginRadiusQueryHandle->fetchAll();

        return isset($loginRadiusResult[0][$getColamn]) ? $loginRadiusResult[0][$getColamn] : '';
    }

    public function getListsMailchimp() {
        $apikey = isset($_POST['apikey']) ? trim($_POST['apikey']) : '';
        $output['status'] = false;
        if (empty($apikey)) {
            $output['message'] = 'API key is required fields.';
        } else {
            $mailchimpsdk = Mage::helper('mailchimp/MCAPI');
            $mailchimpsdk->MCAPI($apikey);
            if ($mailchimpsdk->errorCode) {
                $output['message'] = $mailchimpsdk->errorMessage;
            } else {
                $listInfo = $mailchimpsdk->lists();
                if ($mailchimpsdk->errorCode) {
                    $output['message'] = $mailchimpsdk->errorMessage;
                } elseif (isset($listInfo['data']) && !empty($listInfo['data'])) {
                    $output['status'] = true;
                    $output['html'] = '<option value=""> --- Select List --- </option>';
                    foreach ($listInfo['data'] as $value) {
                        $output['html'] .= '<option value="' . $value['id'] . '"';
                        if ($this->listsMailchimp() == $value['id']) {
                            $output['html'] .= ' selected="selected"';
                        }
                        $output['html'] .= '>' . $value['name'] . '</option>';
                    }
                }
            }
        }
        return json_encode($output);
    }

    public function getFieldsMailchimp() {
        $apikey = isset($_POST['apikey']) ? trim($_POST['apikey']) : '';
        $list = isset($_POST['list']) ? trim($_POST['list']) : '';
        $output['status'] = false;
        if (empty($apikey)) {
            $output['message'] = 'API key is required fields.';
        } elseif (empty($list)) {
            $output['message'] = 'Please Select List is fields.';
        } else {
            $mailchimpsdk = Mage::helper('mailchimp/MCAPI');
            $mailchimpsdk->MCAPI($apikey);
            if ($mailchimpsdk->errorCode) {
                $output['message'] = $mailchimpsdk->errorMessage;
            } else {
                $listFields = $mailchimpsdk->listMergeVars($list);
                if ($mailchimpsdk->errorCode) {
                    $output['message'] = $mailchimpsdk->errorMessage;
                } elseif (isset($listFields) && !empty($listFields)) {
                    $output['status'] = true;
                    $output['html'] = '';
                    $count = 0;
                    foreach ($listFields as $value) {
                        $output['html'] .= '<tr>';
                        $output['html'] .= '<td class="lrmappinglabel">';
                        $output['html'] .= '<label for="mailchimp_mailchimp_mappingFields' . $count . '">' . $value['name'];
                        $output['html'] .= '<input type="hidden" class="mailchimp_mailchimp_mappingFields' . $count . '" value="' . $value['tag'] . '">';
                        $output['html'] .= '</label>';
                        $output['html'] .= '</td>';
                        $output['html'] .= '<td class="lrmappingvalue">';
                        $output['html'] .= '<select id="mailchimp_mailchimp_mappingFields' . $count . '" class="lrmappingselect" onchange="fillMappingFields();">';
                        $output['html'] .= '<option value=""> --- Select Field --- </option>';

                        $lrMappingFields = $this->getMailchimpMappingFields();
                        $lrselectedMappingFields = explode(',', $this->getSelectedMappingFields());
                        foreach ($lrMappingFields as $key => $value) {
                            $output['html'] .= '<option value="' . $value . '"';
                            if (isset($lrselectedMappingFields[$count]) && ($value == $lrselectedMappingFields[$count])) {
                                $output['html'] .= ' selected="selected"';
                            }
                            $output['html'] .= '>' . $this->mappingFieldsUcwords($value) . '</option>';
                        }
                        $output['html'] .= '</select></td></tr>';
                        $count++;
                    }
                }
            }
        }
        return json_encode($output);
    }

    function mappingFieldsUcwords($value) {
        $tempValue = explode('-', $value);
        if (isset($tempValue[1])) {
            return ucwords(str_replace('_', ' ', $tempValue[1]));
        }
        return ucwords($tempValue[0]);
    }

    function getMailchimpMappingFieldsName($fieldPrefix, $tableName) {
        $result = array();
        $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $saleafields = $loginRadiusConn->describeTable(Mage::getSingleton('core/resource')->getTableName('lr_' . $tableName));
        foreach ($saleafields as $key => $value) {
            $result[] = $fieldPrefix . '-' . $key;
        }
        return $result;
    }

    function getMailchimpMappingFields() {
        $mappingFields = array('Entity ID', 'First Name', 'Last Name', 'Full Name', 'Email', 'Telephone', 'Region', 'Postcode');
        if (class_exists('Loginradius_Socialprofiledata_Model_Observer')) {
            $blockObject = new Loginradius_Socialprofiledata_Model_Observer();
            $socialProfileCheckboxes = explode(',', $blockObject->getSocialProfileCheckboxes());
            if (!is_array($socialProfileCheckboxes)) {
                return;
            }

            if (in_array('basic', $socialProfileCheckboxes)) {
                $basicProfileData = $this->getMailchimpMappingFieldsName('basic', 'basic_profile_data');
                $mappingFields = array_merge($mappingFields, $basicProfileData);
            }
            if (in_array('ex_location', $socialProfileCheckboxes)) {
                $extendedLocationData = $this->getMailchimpMappingFieldsName('exloc', 'extended_location_data');
                $mappingFields = array_merge($mappingFields, $extendedLocationData);
            }
            if (in_array('ex_profile', $socialProfileCheckboxes)) {
                $extendedProfileData = $this->getMailchimpMappingFieldsName('exprofile', 'extended_profile_data');
                $mappingFields = array_merge($mappingFields, $extendedProfileData);
            }
            $unsetData = array('basic-first_name', 'basic-last_name', 'basic-full_name', 'basic-user_id', 'exloc-first_name', 'exloc-last_name', 'exloc-full_name', 'exloc-user_id', 'exprofile-first_name', 'exprofile-last_name', 'exprofile-full_name', 'exprofile-user_id');
            foreach ($mappingFields as $key => $value) {
                if (in_array($value, $unsetData)) {
                    unset($mappingFields[$key]);
                }
            }
        }
        return array_values($mappingFields);
    }

}
