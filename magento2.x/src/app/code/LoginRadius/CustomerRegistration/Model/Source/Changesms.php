<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */

namespace LoginRadius\CustomerRegistration\Model\Source;
use LoginRadiusSDK\Utility\Functions;

class Changesms implements \Magento\Framework\Option\ArrayInterface
{
    
    protected $_objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager) {
        $this->_objectManager = $objectManager;
    }
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
       
        if ($activationHelper->siteApiKey() != '' && $activationHelper->siteApiSecret() != '') {
            try {
                $decrypted_secret = $this->lr_secret_encrypt_and_decrypt( $activationHelper->siteApiSecret(), $activationHelper->siteApiKey(), 'd' );
                $query_array = array(
                    'apiKey' => $activationHelper->siteApiKey(),
                    'apiSecret' => $decrypted_secret
                  );
                  $options = array(
                    'output_format' => 'json',
                  );
                  $url = 'https://config.lrcontent.com/ciam/appInfo/templates';
                  $templates = Functions::apiClient($url, $query_array, $options);
            }
            catch (LoginRadiusException $e) {
                
            }
        }
            
        $template = array();
        $template[''] = 'select';
        if(!empty($templates->SMSTemplates)){
        foreach ($templates->SMSTemplates->ChangePhoneNo as $name) {
            $template[$name] = $name; 
        }}
        
        return $template;
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
