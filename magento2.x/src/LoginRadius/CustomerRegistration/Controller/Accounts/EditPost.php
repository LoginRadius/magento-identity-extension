<?php

namespace LoginRadius\CustomerRegistration\Controller\Accounts;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

global $apiClient_class;
$apiClient_class = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';

class EditPost extends \Magento\Customer\Controller\AbstractAccount {

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Customer\Model\CustomerExtractor $customerExtractor
    ) {
        parent::__construct($context);
        $this->session = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRepository = $customerRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerExtractor = $customerExtractor;
    }

    /**
     * Change customer email or password action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute() {
        /** @var \Magento\Framework\App\Http\Context $context */
        $resultRedirect = $this->resultRedirectFactory->create();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if ($this->getRequest()->isPost()) {
            $request = $this->_request->getParams();
            $customer = $this->customerRepository->getById($this->session->getCustomerId());

            if (isset($request['firstname'])) {
                $customer->setFirstname($request['firstname']);
            }
            if (isset($request['lastname'])) {
                $customer->setLastname($request['lastname']);
            }
            if (isset($request['varifiedEmailValue'][0])) {
                $customer->setEmail($request['varifiedEmailValue'][0]);
            }
            if (isset($request['dob'])) {
                $this->_date = $objectManager->create('Magento\Framework\Stdlib\DateTime');
                $customer->setDob($this->_date->gmDate('d-m-Y', $this->_date->strToTime($request['dob'])));
            }
            if (isset($request['gender'])) {
                $customer->setGender($this->getGenderValue($request['gender']));
            }
            try {
                $this->customerRepository->save($customer);

                $activationHelper = $objectManager->get('LoginRadius\Activation\Model\Helper\Data');
                $customerRegistrationHelper = $objectManager->get("LoginRadius" . "\\" . $activationHelper->getAuthDirectory() . "\Model\Helper\Data");
                $userAPI = new \LoginRadiusSDK\CustomerRegistration\Authentication\UserAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('authentication' => true, 'output_format' => 'json'));

                $editUserData = array(
                    'FirstName' => $customer->getFirstname(),
                    'LastName' => $customer->getLastname(),
                    'Gender' => $customer->getGender(),
                    'BirthDate' => $customer->getDob()
                );
                try {
                    $userEditdata = $userAPI->updateProfile($this->session->getLoginRadiusAccessToken(), $editUserData);

                    if (isset($request['changepassword']) && $request['changepassword'] == '1') {
                        if (isset($request['oldpassword']) && isset($request['newpassword']) && isset($request['confirmnewpassword'])) {
                            if (($customerRegistrationHelper->minPassword() != 0) && ($customerRegistrationHelper->minPassword() > strlen($request['newpassword']))) {
                                $this->messageManager->addError('The Password field must be at least ' . $customerRegistrationHelper->minPassword() . ' characters in length.');
                                return $resultRedirect->setPath('customer/account/edit');
                            } elseif (($customerRegistrationHelper->maxPassword() != 0) && (strlen($request['newpassword']) > $customerRegistrationHelper->maxPassword())) {
                                $this->messageManager->addError('The Password field must not exceed ' . $customerRegistrationHelper->maxPassword() . ' characters in length.');
                                return $resultRedirect->setPath('customer/account/edit');
                            } elseif ($request['newpassword'] !== $request['confirmnewpassword']) {
                                //password not match
                                $this->messageManager->addError('Password and Confirm Password don\'t match');
                                return $resultRedirect->setPath('customer/account/edit');
                            } else {
                                try {
                                    $changeUserPassword = $userAPI->changeAccountPassword($this->session->getLoginRadiusAccessToken(), $request['oldpassword'], $request['newpassword']);
                                    if(isset($changeUserPassword->Description) && !empty($changeUserPassword->Description)){
                                        $this->messageManager->addError($changeUserPassword->Description);
                                        return $resultRedirect->setPath('customer/account/edit');
                                    }
                                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                                    $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                                    $this->messageManager->addError($errorDescription);
                                    return $resultRedirect->setPath('customer/account/edit');
                                }
                            }
                        }
                    }
                    $this->messageManager->addSuccess(__('You saved the account information.'));
                    $this->_eventManager->dispatch(
                            'customer_account_edited', ['email' => $customer->getEmail()]
                    );

                    return $resultRedirect->setPath('customer/account');
                    /* Edit profile in local db */
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    if ($customerRegistrationHelper->debug() == '1') {
                        $errorDescription = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : '';
                        $this->messageManager->addError($errorDescription);
                    }
                }

                return $resultRedirect->setPath('customer/account/edit');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t save the customer.'));
            }

            $this->session->setCustomerFormData($this->getRequest()->getPostValue());
        }

        return $resultRedirect->setPath('customer/account/edit');
    }

}
