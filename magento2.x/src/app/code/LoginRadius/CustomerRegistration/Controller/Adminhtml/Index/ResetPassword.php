<?php

namespace LoginRadius\CustomerRegistration\Controller\Adminhtml\Index;

class ResetPassword extends \Magento\Customer\Controller\Adminhtml\Index\ResetPassword {

    public function execute() {
        $this->_helperActivation = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
        $resultRedirect = $this->resultRedirectFactory->create();
        $accountApi = new \LoginRadiusSDK\CustomerRegistration\AccountAPI($this->_helperActivation->siteApiKey(), $this->_helperActivation->siteApiSecret(), array('authentication' => true, 'output_format' => 'json'));
        $customerId = (int) $this->getRequest()->getParam('customer_id', 0);
        $customer = $this->_customerRepository->getById($customerId);

        $homeDomain = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl();

        
            try {
                $rsetPasswordUrl = 'https://api.loginradius.com/raas/client/password/forgot?apikey=' . rawurlencode(trim($this->_helperActivation->siteApiKey())) . '&emailid=' . $customer->getEmail() . '&resetpasswordurl=' . $homeDomain . 'customer/account/login/';
                $this->messageManager->addSuccess(__('The customer will receive an email with a link to reset password.'));
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';

                $this->_messageManager->addError($errorDescription);
            }
           
        $result = \LoginRadiusSDK\LoginRadius::apiClient($rsetPasswordUrl, FALSE, array('output_format' => 'json'));

        $resultRedirect->setPath(
                'customer/*/edit', ['id' => $customerId, '_current' => true]
        );
        return $resultRedirect;
    }

}
