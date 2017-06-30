<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\Activation\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckKey implements ObserverInterface {

    protected $_messageManager;
    protected $_objectManager;

    public function __construct(
            \Magento\Framework\App\RequestInterface $request,
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        global $alreadyLoad, $apiClient_class;
        $apiClient_class = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';
        if ($alreadyLoad != 'true') {
            $alreadyLoad = 'true';
            try {
                $post = $this->_request->getParams();
                if (isset($post['config_state']['lractivation_activation']) && $post['config_state']['lractivation_activation'] == '1') {
                    $apiKey = (($post['groups']['activation']['fields']['site_api']['value'] != null) ? trim($post['groups']['activation']['fields']['site_api']['value']) : '');
                    $apiSecret = (($post['groups']['activation']['fields']['site_api']['value'] != null) ? trim($post['groups']['activation']['fields']['site_secret']['value']) : '');
                    $validateUrl = 'https://api.loginradius.com/api/v2/app/validate?apikey=' . $apiKey . '&apisecret=' . $apiSecret;

                    $checkUrl = new $apiClient_class();

                    $result = json_decode($checkUrl->request($validateUrl));
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
                        throw new \Exception($errorDescription);
                    }elseif (isset($result->Status) && $result->Status == true) {
                        return;
                    }else {
                        throw new \Exception('an error occurred. Please try again');
                    }                    
                }
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                throw new \Exception('an error occurred. Please try again');
            }
        }
    }

}
