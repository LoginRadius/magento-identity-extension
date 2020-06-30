<?php

/**
 * Copyright Â© 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\Activation\Observer;

use Magento\Framework\Event\ObserverInterface;
use \LoginRadiusSDK\Utility\Functions;

global $apiClientClass;
$apiClientClass = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';

class SaveKey implements ObserverInterface {

    protected $_messageManager;
    protected $_objectManager;
    protected $redirect;

    public function __construct(
            \Magento\Framework\App\RequestInterface $request,
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \Magento\Framework\ObjectManagerInterface $objectManager,
            \Magento\Framework\App\Response\RedirectInterface $redirect,
            \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
            \Magento\Backend\Helper\Data $HelperBackend
    ) {
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
        $this->_redirect = $redirect;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->HelperBackend = $HelperBackend;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        global $alreadyLoad, $apiClientClass;
        $apiClientClass = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';
        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');  
      
        if ($alreadyLoad == 'true') {
            $alreadyLoad = 'true';
            try {
                $post = $this->_request->getParams();
                if (isset($post['config_state']['lractivation_activation']) && $post['config_state']['lractivation_activation'] == '1') {
                    $apiKey = (($post['groups']['activation']['fields']['site_api']['value'] != null) ? trim($post['groups']['activation']['fields']['site_api']['value']) : '');
                    $apiSecret = (($post['groups']['activation']['fields']['site_secret']['value'] != null) ? trim($post['groups']['activation']['fields']['site_secret']['value']) : '');
             
                    $query_array = array(
                        'apiKey' => $apiKey,
                        'apiSecret' => $apiSecret
                    );

                    $validateUrl = 'https://api.loginradius.com/api/v2/app/validate';
                    $result = Functions::_apiClientHandler('GET', $validateUrl, $query_array);
                
                    if (isset($result->Status) && $result->Status != true) {
                              
                        $apiKey = $activationHelper->siteApiKey();   
                        $secretKey = $activationHelper->siteApiSecret();
                    
                        if (!empty($apiKey) && !empty($secretKey)) {                             
                            $this->save_api_key($apiKey);
                            $this->save_encrypted_secret_key($secretKey);
                        }else{
                            $this->save_api_key();
                            $this->save_encrypted_secret_key();
                        }

                    }elseif (isset($result->Status) && $result->Status == true) {
                        $encrypted_key = $this->lr_secret_encrypt_and_decrypt($apiSecret, $apiKey, 'e');
                        $this->save_encrypted_secret_key($encrypted_key);                       
                        return;
                    }            
                }
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
              //  $this->_messageManager->addError('an error occurred. Please try again');      
            }
        }
    }

   /**
     * 
     * @param type $apikey
     */
    function save_api_key($apikey = '') {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $tableName = $resource->getTableName('core_config_data');
        $connection = $resource->getConnection();
       
        $data = ['scope' => 'default', 'scope_id' => '0', 'path' => 'lractivation/activation/site_api', 'value' => $apikey];
        $connection->insertOnDuplicate($tableName, $data, ['value']);
        
    }

    /**
     * 
     * @param type $encryptedKey
     */
    function save_encrypted_secret_key($encryptedKey = '') {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $tableName = $resource->getTableName('core_config_data');
        $connection = $resource->getConnection();
       
        $data = ['scope' => 'default', 'scope_id' => '0', 'path' => 'lractivation/activation/site_secret', 'value' => $encryptedKey];           
        $connection->insertOnDuplicate($tableName, $data, ['value']);    
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