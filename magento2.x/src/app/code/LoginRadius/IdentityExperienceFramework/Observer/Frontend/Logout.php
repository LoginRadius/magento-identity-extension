<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\IdentityExperienceFramework\Observer\Frontend;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Logout implements ObserverInterface {

    protected $_helperActivation;
    protected $_helperCustomerRegistration;
    
     public function __construct(
\Magento\Framework\App\RequestInterface $request, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $this->_helperCustomerRegistration = \Magento\Framework\App\ObjectManager::getInstance()->get('LoginRadius\CustomerRegistration\Model\Helper\Data');
        if ($this->_helperCustomerRegistration->enableIefPage() == '1') {
            $this->_helperActivation = \Magento\Framework\App\ObjectManager::getInstance()->get('LoginRadius\Activation\Model\Helper\Data');
            $request = $this->_request->getParams();
            $islogout = isset($request['islogout']) && !empty($request['islogout']) ? trim($request['islogout']) : '';

            if ($this->_helperActivation->siteApiKey() != ''){
                define('LR_API_KEY', $this->_helperActivation->siteApiKey());
            }
            if ($this->_helperActivation->siteApiSecret() != ''){
                $decrypted_key = $this->lr_secret_encrypt_and_decrypt($this->_helperActivation->siteApiSecret(), $this->_helperActivation->siteApiKey(), 'd');
                define('LR_API_SECRET', $decrypted_key);
            }

            if (empty($islogout)) {
                $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
                $url = $urlInterface->getUrl('customer/account/logout/');

                $iefPageUrl = 'https://' . $this->_helperActivation->siteName() . '.hub.loginradius.com/auth.aspx?action=logout&return_url=' . $url . '?islogout=true';
                header('Location:' . $iefPageUrl);
                return;
            }
        }
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
