<?php

/**
 * Customer account controller
 */
require_once 'Mage/Customer/controllers/AccountController.php';
global $apiClient_class;
$apiClient_class = 'Ciam_Authentication_Helper_SDKClient';
require_once Mage::getModuleDir('', 'Ciam_Authentication') . DS . 'Helper' . DS . 'SDKClient.php';

class Ciam_Authentication_AccountController extends Mage_Customer_AccountController {

    /**
     * Change customer password action
     */
    public function editPostAction() {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('customer/account/edit');
        }

        if ($this->getRequest()->isPost()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getSession()->getCustomer();
            $customer->setOldEmail($customer->getEmail());
            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = $this->_getModel('customer/form');
            $customerForm->setFormCode('customer_account_edit')
                    ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());

            $errors = array();
            $customerErrors = $customerForm->validateData($customerData);
            if ($customerErrors !== true) {
                $errors = array_merge($customerErrors, $errors);
            } else {
                $customerForm->compactData($customerData);
                $errors = array();

                // If email change was requested then set flag
                $isChangeEmail = ($customer->getOldEmail() != $customer->getEmail()) ? true : false;
                $customer->setIsChangeEmail($isChangeEmail);

                // If password change was requested then add it to common validation scheme
                $customer->setIsChangePassword($this->getRequest()->getParam('change_password'));

                // Validate account and compose list of errors if any
                $customerErrors = $customer->validate();
                if (is_array($customerErrors)) {
                    $errors = array_merge($errors, $customerErrors);
                }
            }

            if (!empty($errors)) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('customer/account/edit');
                return $this;
            }

            try {
                $customer->cleanPasswordsValidationData();

                // Reset all password reset tokens if all data was sufficient and correct on email change
                if ($customer->getIsChangeEmail()) {
                    $customer->setRpToken(null);
                    $customer->setRpTokenCreatedAt(null);
                }
                $session = Mage::getSingleton('core/session');
                $postData = $this->getRequest()->getPost();
                if (isset($postData['varifiedEmailValue'])) {
                    $blockObj = new Ciam_Authentication_Block_Authentication();
                    $customer = Mage::getSingleton("customer/session")->getCustomer();
                    //customer basic info object
                    $modified = array();
                    //customer basic info
                    $modified['firstname'] = isset($postData['firstname']) ? $postData['firstname'] : $customer->firstname; //firstname
                    $modified['middlename'] = isset($postData['middlename']) ? $postData['middlename'] : $customer->middlename; //firstname
                    $modified['lastname'] = isset($postData['lastname']) ? $postData['lastname'] : $customer->lastname; //lastname
                    $modified['birthdate'] = isset($postData['dob']) ? date('m-d-Y', strtotime($postData['dob'])) : date('m-d-Y', strtotime($customer->dob)); //dob
                    $modified['taxvat'] = isset($postData['taxvat']) ? $postData['taxvat'] : $customer->taxvat; //taxvat
                    $modified['gender'] = isset($postData['gender']) ? $postData['gender'] : $customer->gender; //gender
                    if ($modified['gender'] == '0') {
                        $modified['gender'] = 'F';
                    } else {
                        $modified['gender'] = 'M';
                    }
                    //update  user at LoginRadius
                    $userId = Mage::getSingleton("customer/session")->getId();
                    $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_read');
                    $loginRadiusQuery = "SELECT uid FROM " . Mage::getSingleton('core/resource')->getTableName('lr_authentication') . " WHERE entity_id = '" . $userId . "' LIMIT 1";
                    $loginRadiusQueryHandle = $loginRadiusConn->query($loginRadiusQuery);
                    $loginRadiusResult = $loginRadiusQueryHandle->fetch();
                    $accountObject = new LoginRadiusSDK\CustomerRegistration\Management\AccountAPI($blockObj->apiKey(), $blockObj->apiSecret(), array("output_format" => 'json'));
                    if (isset($loginRadiusResult["uid"]) && !empty($loginRadiusResult["uid"])) {
                        try {
                            $response = $accountObject->update($loginRadiusResult["uid"], json_encode($modified));
                            if (isset($postData['varifiedEmailValue'][0]) && !empty($postData['varifiedEmailValue'][0])) {
                                $customer->setEmail($postData['varifiedEmailValue'][0]);
                            }
                        } catch (LoginRadiusSDK\LoginRadiusException $e) {
                            $session->addError($e->getErrorResponse()->Description);
                            Mage::app()->getResponse()->setRedirect('customer/account/edit');
                        }
                    }
                }

                if (isset($postData['social_password']) && $postData['social_password'] == 1) {
                    if (!empty($session->getLoginRadiusToken())) {
                        //change password
                        if (isset($postData['newpassword']) && isset($postData['confirmnewpassword'])) {
                            if ($postData['newpassword'] !== $postData['confirmnewpassword']) {
                                //password not match
                                $session->addError('Password and Confirm Password don\'t match');
                                $this->_redirect('customer/account/edit');
                                return;
                            } else {
                                $userAPI = new LoginRadiusSDK\CustomerRegistration\Authentication\UserAPI($blockObj->apiKey(), $blockObj->apiSecret(), array("output_format" => 'json'));
                                try {
                                    $response = $userAPI->changeAccountPassword($session->getLoginRadiusToken(), $postData['oldpassword'], $postData['newpassword']);
                                } catch (LoginRadiusSDK\LoginRadiusException $e) {
                                    $session->addError($e->getErrorResponse()->Description);
                                    $this->_redirect('customer/account/edit');
                                    return;
                                }
                            }
                        }
                    } else {
                        $session->addError('An error occurred');
                        $this->_redirect('customer/account/edit');
                        return;
                    }
                }

                $customer->save();
                $this->_getSession()->setCustomer($customer)
                        ->addSuccess($this->__('The account information has been saved.'));

                $this->_redirect('customer/account');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                        ->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                        ->addException($e, $this->__('Cannot save the customer.'));
            }
        }

        $this->_redirect('customer/account/edit');
    }

}
