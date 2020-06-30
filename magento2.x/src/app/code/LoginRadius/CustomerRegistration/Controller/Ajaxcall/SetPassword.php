<?php

namespace LoginRadius\CustomerRegistration\Controller\Ajaxcall;

use Magento\Framework\Controller\ResultFactory;
use \LoginRadiusSDK\CustomerRegistration\Account\AccountAPI;


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

        if ($activationHelper->siteApiKey() != ''){
            define('LR_API_KEY', $activationHelper->siteApiKey());
        }
        if ($activationHelper->siteApiSecret() != ''){
            $decrypted_key = $this->lr_secret_encrypt_and_decrypt($activationHelper->siteApiSecret(), $activationHelper->siteApiKey(), 'd');
            define('LR_API_SECRET', $decrypted_key);
        }

        $password = $this->getRequest()->getParam('password');
        $confirmPassword = $this->getRequest()->getParam('confirmPassword');

        if (isset($password) && !empty($password) && isset($confirmPassword) && !empty($confirmPassword)) { 
                try {
                    $accountObject = new AccountAPI();
                    $result = $accountObject->setAccountPasswordByUid($password, $currentUid);

                    if (isset($result->Description) && $result->Description) {
                        $responseContent = ['success' => false, 'message' => $result->Description];
                    } else {
                        $responseContent = ['success' => true, 'message' => "Password has been set successfully."];
                    }
                }
                catch (LoginRadiusException $e) {
                    $responseContent = ['success' => false, 'message' => $e->getMessage()];
                }           
        }
        else {
            $responseContent = ['success' => false, 'message' => 'The password and confirm password fields are required.'];
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseContent);
        return $resultJson;
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
