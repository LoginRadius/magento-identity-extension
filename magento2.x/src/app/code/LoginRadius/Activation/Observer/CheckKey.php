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

class CheckKey implements ObserverInterface {

    protected $_messageManager;
    protected $_objectManager;
    protected $redirect;

    public function __construct(
            \Magento\Framework\App\RequestInterface $request,
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \Magento\Framework\ObjectManagerInterface $objectManager,
            \Magento\Framework\App\Response\RedirectInterface $redirect,
            \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
    ) {
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
        $this->_redirect = $redirect;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        global $alreadyLoad, $apiClientClass;
        $apiClientClass = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';
        if ($alreadyLoad != 'true') {
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
                        if ($result->Messages[0] == 'API_KEY_NOT_FORMATED') {
                            $data = 'LoginRadius API key is not correct.';
                        } elseif ($result->Messages[0] == 'API_SECRET_NOT_FORMATED') {
                            $data = 'LoginRadius API Secret key is not correct.';
                        } elseif ($result->Messages[0] == 'API_KEY_NOT_VALID') {
                            $data = 'LoginRadius API key is not valid.';
                        } elseif ($result->Messages[0] == 'API_SECRET_NOT_VALID') {
                            $data = 'LoginRadius API Secret key is not valid.';
                        }
                        $errorDescription = isset($data) ? $data : '';
                        $this->_messageManager->addError($errorDescription);                         
                    } elseif (isset($result->Status) && $result->Status == true) {
                        
                       return;
                    } else {
                        $this->_messageManager->addError('an error occurred. Please try againyy');                          
                    }             
                }
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                $this->_messageManager->addError('an error occurred. Please try againtt');      
            }
        }
    }
}