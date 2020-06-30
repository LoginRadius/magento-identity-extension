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
use \LoginRadiusSDK\CustomerRegistration\Authentication\AuthenticationAPI;

global $apiClientClass;
$apiClientClass = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';

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
        $activationHelper = $objectManager->get('LoginRadius\Activation\Model\Helper\Data');
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

            if ($activationHelper->siteApiKey() != ''){
                define('LR_API_KEY', $activationHelper->siteApiKey());
            }
            if ($activationHelper->siteApiSecret() != ''){
                $decrypted_key = $this->lr_secret_encrypt_and_decrypt($activationHelper->siteApiSecret(), $activationHelper->siteApiKey(), 'd');
                define('LR_API_SECRET', $decrypted_key);
            }

            try {
                $this->customerRepository->save($customer);

                $activationHelper = $objectManager->get('LoginRadius\Activation\Model\Helper\Data');
                $authObject = new AuthenticationAPI();

                $editUserData = array(
                    'FirstName' => $customer->getFirstname(),
                    'LastName' => $customer->getLastname(),
                    'Gender' => $customer->getGender(),
                    'BirthDate' => $customer->getDob()
                );
                try {
                    $userEditdata = $authObject->updateProfileByAccessToken($this->session->getLoginRadiusAccessToken(), $editUserData);
                    $this->messageManager->addSuccess(__('You saved the account information.'));
                    $this->_eventManager->dispatch(
                            'customer_account_edited', ['email' => $customer->getEmail()]
                    );

                    return $resultRedirect->setPath('customer/account');
                    /* Edit profile in local db */
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    
                        $errorDescription = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : '';
                        $this->messageManager->addError($errorDescription);
                    
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


    /**
     * Encrypt and decrypt
     *
     * @param string $string string to be encrypted/decrypted
     * @param string $action what to do with this? e for encrypt, d for decrypt
     */     
    function lr_secret_encrypt_and_decrypt( $string, $secretIv, $action) {
        $secret_key = $secretIv;
        $secret_iv = $secretIv;
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
        if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'd' ) {
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv ); 
        }   
        return $output;
    }

}
