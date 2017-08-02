<?php

/**
 * Customer account controller
 */
require_once Mage::getModuleDir('controllers', 'Mage_Customer') . DS . "AccountController.php";

class Loginradius_Customerregistration_AccountController extends Mage_Customer_AccountController {

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch() {

        $action = $this->getRequest()->getActionName();
        $this->dataObject = Mage::helper('hostedregistration/Data');

        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }


        $openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation'
        );
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }

    /**
     * Change customer password action
     */
    public function editPostAction() {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('customer/account/edit');
        }
        require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'SDKClient.php';
        global $apiClient_class;
        $apiClient_class = 'Loginradius_Sociallogin_Helper_SDKClient';
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        $userApi = new LoginRadiusSDK\CustomerRegistration\UserAPI($activationBlockObj->apiKey(), $activationBlockObj->apiSecret(), array('output_format' => 'json'));

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

            $session = Mage::getSingleton('core/session');
            $postData = $this->getRequest()->getPost();
            if (isset($postData['email'])) {
                $customer = Mage::getSingleton("customer/session")->getCustomer();
                if (isset($postData['email']) && ($postData['email'] == $customer->email)) {

                    //customer basic info object
                    $modified = array();
                    //customer basic info
                    $modified['firstname'] = isset($postData['firstname']) ? $postData['firstname'] : $customer->firstname; //firstname
                    $modified['lastname'] = isset($postData['lastname']) ? $postData['lastname'] : $customer->lastname; //lastname
                    $modified['email'] = isset($postData['email']) ? $postData['email'] : $customer->email; //email
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
                    //change and set password
                    if (isset($postData['social_password']) && $postData['social_password'] == 1) {
                        $accountAPI = new LoginRadiusSDK\CustomerRegistration\AccountAPI($activationBlockObj->apiKey(), $activationBlockObj->apiSecret(), array('output_format' => 'json'));

                        $userId = Mage::getSingleton("customer/session")->getId();
                        $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_read');
                        $loginRadiusQuery = "SELECT uid FROM " . Mage::getSingleton('core/resource')->getTableName('lr_sociallogin') . " where entity_id = '" . $userId . "' LIMIT 1";
                        $loginRadiusQueryHandle = $loginRadiusConn->query($loginRadiusQuery);
                        $loginRadiusResult = $loginRadiusQueryHandle->fetch();

                        if (isset($loginRadiusResult["uid"]) && !empty($loginRadiusResult["uid"])) {
                            //set password
                            $raasSettings = Mage::getBlockSingleton('customerregistration/customerregistration');

                            if (isset($postData['emailid']) && isset($postData['confirmpassword']) && isset($postData['password'])
                            ) {
                                if (empty($postData['emailid'])) {
                                    $session->addError('Please select Email Address');
                                    /* not the best redirect but don`t know how to */
                                    $this->_redirect('customer/account/edit');
                                    return;
                                }
                                if (($raasSettings->minPasswordLength() != 0) && ($raasSettings->minPasswordLength() > strlen($postData['password']))) {
                                    $session->addError('The Password field must be at least ' . $raasSettings->minPasswordLength() . ' characters in length.');
                                    $this->_redirect('customer/account/edit');
                                    return;
                                } elseif (($raasSettings->maxPasswordLength() != 0) && (strlen($postData['password']) > $raasSettings->maxPasswordLength())) {
                                    $session->addError('The Password field must not exceed ' . $raasSettings->maxPasswordLength() . ' characters in length.');
                                    $this->_redirect('customer/account/edit');
                                    return;
                                } elseif ($postData['password'] === $postData['confirmpassword']) { //check both password
                                    $data = array('accountid' => trim($loginRadiusResult["uid"]), 'emailid' => trim($postData['emailid']), 'password' => trim($postData['password']));
                                    try {
                                        $response = $accountAPI->createUserRegistrationProfile($data);
                                    } catch (LoginRadiusSDK\LoginRadiusException $e) {
                                        $session->addError($e->getErrorResponse()->description);
                                        $this->_redirect('customer/account/edit');
                                        return;
                                    }
                                } else { //password not match
                                    $session->addError('Password don\'t match');
                                    $this->_redirect('customer/account/edit');
                                    return;
                                }
                            } elseif (isset($postData['newpassword']) && isset($postData['confirmnewpassword'])) {
                                if (($raasSettings->minPasswordLength() != 0) && ($raasSettings->minPasswordLength() > strlen($postData['newpassword']))) {
                                    $session->addError('The Password field must be at least ' . $raasSettings->minPasswordLength() . ' characters in length.');
                                    $this->_redirect('customer/account/edit');
                                    return;
                                } elseif (($raasSettings->maxPasswordLength() != 0) && (strlen($postData['newpassword']) > $raasSettings->maxPasswordLength())) {
                                    $session->addError('The Password field must not exceed ' . $raasSettings->maxPasswordLength() . ' characters in length.');
                                    $this->_redirect('customer/account/edit');
                                    return;
                                } elseif ($postData['newpassword'] !== $postData['confirmnewpassword']) {
                                    //password not match
                                    $session->addError('Password and Confirm Password don\'t match');
                                    $this->_redirect('customer/account/edit');
                                    return;
                                } else {
                                    try {
                                        $response = $accountAPI->changeAccountPassword($loginRadiusResult["uid"], $postData['oldpassword'], $postData['newpassword']);
                                    } catch (LoginRadiusSDK\LoginRadiusException $e) {
                                        $session->addError($e->getErrorResponse()->description);
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

                    //update  user at raas
                    try {
                        $socialId = Mage::getSingleton("customer/session")->getloginRadiusId();
                        $response = $userApi->edit($socialId, $modified);
                    } catch (LoginRadiusSDK\LoginRadiusException $e) {
                        $session->addError($e->getErrorResponse()->description);
                        $this->_redirect('customer/account/edit');
                        return;
                    }
                }
            }
            $customer->save();
            $this->_getSession()->setCustomer($customer)
                    ->addSuccess($this->__('The account information has been saved.'));

            $this->_redirect('customer/account');
            return;
        }

        $this->_redirect('customer/account/edit');
    }

    public function loginAction() {
        if (Mage::getBlockSingleton('hostedregistration/hostedregistration')->enableHostedPage() == '1') {
            $this->_redirectUrl($this->dataObject->getLoginUrl());
        } else {
            parent::loginAction();
        }
    }

    public function createAction() {
        if (Mage::getBlockSingleton('hostedregistration/hostedregistration')->enableHostedPage() == '1') {
            $this->_redirectUrl($this->dataObject->getRegisterUrl());
        } else {
            parent::createAction();
        }
    }

    public function forgotPasswordAction() {
        if (Mage::getBlockSingleton('hostedregistration/hostedregistration')->enableHostedPage() == '1') {
            $this->_redirectUrl($this->dataObject->getForgotPasswordUrl());
        } else {
            parent::forgotPasswordAction();
        }
    }

    public function editAction() {
        if (Mage::getBlockSingleton('hostedregistration/hostedregistration')->enableHostedPage() == '1') {
            $this->_redirectUrl($this->dataObject->getProfileUrl());
        } else {
            parent::editAction();
        }
    }

    public function logoutAction() {
        if (Mage::getBlockSingleton('hostedregistration/hostedregistration')->enableHostedPage() == '1') {
            $this->_redirectUrl($this->dataObject->getLogoutUrl());
        } else {
            parent::logoutAction();
        }
    }

    public function logoutSuccessAction() {
        if (Mage::getBlockSingleton('hostedregistration/hostedregistration')->enableHostedPage() == '1') {
            $islogout = $this->getRequest()->getParam('islogout');
            if ($islogout == 'true') {
                parent::logoutAction();
            }
            parent::logoutSuccessAction();
        } else {
            parent::logoutSuccessAction();
        }
    }

}
