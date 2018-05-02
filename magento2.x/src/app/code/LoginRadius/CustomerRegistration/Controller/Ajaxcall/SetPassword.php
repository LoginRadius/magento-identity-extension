<?php

namespace LoginRadius\CustomerRegistration\Controller\Ajaxcall;

use Magento\Framework\Controller\ResultFactory;

class SetPassword extends \Magento\Customer\Controller\AbstractAccount {

    /**
     * customer set password action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $activationHelper = $objectManager->get('LoginRadius\Activation\Model\Helper\Data');
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $currentUid = $customerSession->getLoginRadiusUid();

        $password = $this->getRequest()->getParam('password');
        $confirmPassword = $this->getRequest()->getParam('confirmPassword');

        if (isset($password) && !empty($password) && isset($confirmPassword) && !empty($confirmPassword)) { 
                try {
                    $accountObject = new \LoginRadiusSDK\CustomerRegistration\Management\AccountAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('output_format' => 'json'));
                    $result = $accountObject->setPassword($currentUid, $password);
                    
                    if (isset($result->Description) && $result->Description) {
                        $responseContent = ['success' => false, 'message' => $result->Description];
                    } else {
                        $responseContent = ['success' => true, 'message' => "Password set successfully"];
                    }
                }
                catch (LoginRadiusException $e) {
                    $responseContent = ['success' => false, 'message' => $e->getMessage()];
                }           
        }
        else {
            $responseContent = ['success' => false, 'message' => 'The password and confirm password fields are required'];
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseContent);
        return $resultJson;
    }
}
